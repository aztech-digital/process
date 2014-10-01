<?php

namespace Aztech\Process\Inspector;

use Aztech\Process\Inspector;
use Aztech\Process\ProcessInfo;

class CurrentProcessInspector implements Inspector
{
    public function getProcessInfo($pid)
    {
        global $argv;

        $pid = getmypid();
        $uid = getmyuid();
        $name = PHP_BINARY;

        $process = new ProcessInfo($pid, $uid, basename($name));

        $process->setBinaryPath($name);
        $process->setArguments($argv);
        $process->setEnvironment($_ENV);
        $process->setParentId(function_exists('posix_getppid') ? posix_getppid() : 0);

        return $process;
    }

    public function getProcessPipes(ProcessInfo $process)
    {
        return [];
    }
}
