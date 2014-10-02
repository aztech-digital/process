<?php

namespace Aztech\Process;

use Aztech\Process\Inspector\CurrentProcessInspector;
use Aztech\Process\ProcessControl\ComProcessControl;
use Aztech\Process\ProcessControl\PcntlProcessControl;
use Aztech\Process\Com\ComProcessLocator;
use Aztech\Process\SignalEmitter\PosixSignalEmitter;
use Aztech\Process\SignalEmitter\ComSignalEmitter;

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
    
    private static function isWindows()
    {
    	return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    private $depth = 0;

    private $children = [];

    private $killOnExit = [];

    private $processControl = null;
    
    /**
     *
     * @var ProcessInfo
     */
    private $process;

    protected function __construct()
    {
        $inspector = new CurrentProcessInspector();

        if (self::$instance !== null) {
            $this->depth = self::$instance->depth + 1;
        }

        $this->pid = getmypid();
        $this->processControl = new PcntlProcessControl();
        $this->info = $inspector->getProcessInfo(getmypid());
        $this->pipes = [
            STDIN,
            STDOUT,
            STDERR
        ];

        if ($this->isWindows()) {
        	$this->processControl = new ComProcessControl();
        }
        
        parent::__construct($this->isWindows() ? new ComSignalEmitter(new ComProcessLocator()) : new PosixSignalEmitter());
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

    private function _yieldFork(callable $task)
    {
    	
    }
    
    private function yieldFork(callable $task)
    {
    	call_user_func($task);
    	
    	$this->depth--;
    	
	    yield;
    }
    
    public function fork(callable $task, $daemonize = true)
    {
    	if ($this->isWindows()) {
    		$this->depth++;
    		$this->children[$this->depth][] = $this->yieldFork($task);
    		
    		return;
    	}
    	
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
    }

    public function restart()
    {
    	if ($this->depth == 0) { 
        	return $this->processControl->restart($this);
    	}
    	
    	return false;
    }

    public function waitFor($childPid)
    {
        return $this->processControl->waitFor($childPid);
    }

    public function wait()
    {
    	echo 'Waiting on children...' . PHP_EOL;
    	
        return $this->processControl->wait($this->children[$this->depth]);
    }
}
