<?php

namespace Aztech\Process;

class AttachedProcess extends AbstractProcess
{

    private $process;

    public function __construct($process, array $pipes, Inspector $inspector = null)
    {
        if (! is_resource($process)) {
            throw new \InvalidArgumentException('Process is not a valid resource.');
        }

        $this->process = $process;
        $this->pipes = $pipes;

        $status = proc_get_status($process);

        $this->pid = $status['pid'];
        $this->info = $inspector->getProcessInfo($this->pid);
    }

    public function writeStdIn($text)
    {
        fwrite($this->pipes[0], $text);
    }

    public function readStdOut(callable $reader = null)
    {
        fclose($this->pipes[0]);

        $stdOut = '';

        while ($line = fgets($this->pipes[1])) {
            if ($reader) {
                $reader($line);
            }

            $stdOut .= $line;
        }

        return $stdOut;
    }
}
