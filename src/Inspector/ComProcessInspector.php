<?php

namespace Aztech\Process\Inspector;

use Aztech\Process\Inspector;
use Aztech\Process\ProcessInfo;

class ComProcessInspector implements Inspector
{

    public function getProcessInfo($pid)
    {
        $WbemLocator = new \COM("WbemScripting.SWbemLocator");
        $WbemServices = $WbemLocator->ConnectServer(php_uname("n"), 'root\\cimv2');
        $WbemServices->Security_->ImpersonationLevel = 3;

        $processes = $WbemServices->ExecQuery("SELECT * FROM Win32_Process WHERE ProcessId = " . intval($pid));

        if (empty($processes)) {
            throw new \RuntimeException('Process not found.');
        }

        $process = $processes[0];

        $sid = null;
        $process->getOwnerSid($sid);

        $info = new ProcessInfo($process->ProcessId, $sid, $process->Caption);
        $info->setArguments(array_slice(explode(' ', $process->CommandLine), 1));
        $info->setBinaryPath($process->ExecutablePath);
        $info->setParentId($process->ParentProcessId);

        return $info;
    }

    public function getProcessPipes(ProcessInfo $processInfo)
    {
        return [];
    }
}
