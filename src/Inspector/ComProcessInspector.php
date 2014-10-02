<?php

namespace Aztech\Process\Inspector;

use Aztech\Process\Inspector;
use Aztech\Process\ProcessInfo;
use Aztech\Process\Com\ComProcessLocator;
use Aztech\Process\SignalEmitter\ComSignalEmitter;

class ComProcessInspector implements Inspector
{

	private $locator;
	
	public function __construct(ComProcessLocator $locator = null)
	{
		$this->locator = $locator ?: new ComProcessLocator();
	}
	
    public function getProcessInfo($pid)
    {
        $process = $this->locator->getProcess($pid);
        
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
    
    public function getSignalEmitter()
    {
    	return new ComSignalEmitter($this->locator);
    }
}
