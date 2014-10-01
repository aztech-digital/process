<?php

namespace Aztech\Process;

use Aztech\Process\Inspector\CurrentProcessInspector;

final class CurrentProcess extends AbstractProcess
{

    private static $instance = null;

    private static $building = false;

    public static function getInstance()
    {
        if (self::$instance === null && ! self::$building) {
            self::$building = true;
            self::$instance = new self();
            self::$building = false;
        }

        return self::$instance;
    }

    private $depth = 0;

    private $children = [];

    private $killOnExit = [];

    /**
     *
     * @var ProcessInfo
     */
    private $process;

    private function __construct()
    {
        $inspector = new CurrentProcessInspector();

        if (self::$instance !== null) {
            $this->depth = self::$instance->depth + 1;
        }

        $this->pid = getmypid();
        $this->info = $inspector->getProcessInfo(getmypid());
        $this->pipes = [
            STDIN,
            STDOUT,
            STDERR
        ];
    }

    public function __destruct()
    {
        foreach ($this->killOnExit as $pid) {
            posix_kill($pid, SIGKILL);
        }
    }

    public function isFork()
    {
        return $this->depth > 0;
    }

    public function getForkDepth()
    {
        return $this->depth;
    }

    public function fork(callable $task, $daemonize = true)
    {
        if ($this->depth > 5) {
            throw new \BadMethodCallException('Too many nested forks !');
        }

        $pid = pcntl_fork();

        if ($pid === 0) {
            self::$instance = new self();

            exit((int) call_user_func($task));
        }

        $this->children[] = $pid;

        if (! $daemonize) {
            $this->killOnExit[] = $pid;
        }

        return $pid;
    }

    public function restart()
    {
        if ($this->isFork()) {
            return false;
        }

        $info = $this->info;

        return pcntl_exec($info->getBinaryPath(), $info->getArguments());
    }

    public function waitFor($childPid)
    {
        $status = null;

        pcntl_waitpid($childPid, $status);
    }

    public function wait()
    {
        $status = null;

        while (count($this->children)) {
            for ($i = count($this->children) - 1; $i >= 0; $i --) {
                if (pcntl_waitpid($this->children[$i], $status, WNOHANG) == $this->children[$i]) {
                    unset($this->children[$i]);

                    continue;
                }

                usleep(250000);
            }
        }
    }
}
