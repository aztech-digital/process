<?php

namespace Aztech\Process;

abstract class AbstractProcess implements Process
{
	protected $signalEmitter = null;
	
    protected $pid = 0;

    protected $info = null;

    protected $pipes = [];

    protected function __construct(SignalEmitter $emitter)
    {
    	$this->signalEmitter = $emitter;
    }
    
    public function getPid()
    {
        return $this->pid;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function hasParentProcess()
    {
        return ($this->info && $this->info->getParentId() > 0);
    }

    public function getParentProcess()
    {
        if ($this->hasParentProcess()) {
            return new ExternalProcess($this->info->getParentId());
        }

        return null;
    }

    public function getPipes()
    {
        return $this->pipes;
    }

    public function kill($signal)
    {
    	return $this->signalEmitter->kill($this->pid, $signal);
    }
}
