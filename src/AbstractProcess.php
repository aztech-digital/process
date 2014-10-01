<?php

namespace Aztech\Process;

abstract class AbstractProcess implements Process
{
    protected $pid = 0;

    protected $info = null;

    protected $pipes = [];

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
        if ($this->info->getUid() !== CurrentProcess::getInstance()->getInfo()->getUid()) {
            throw new \BadMethodCallException('Cannot kill process (no such privilege).');
        }

        return posix_kill($this->pid, $signal);
    }
}
