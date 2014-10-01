<?php

namespace Aztech\Process;

class ProcessInfo
{
    private $args = [];

    private $env = [];

    private $pid;

    private $parentId = 0;

    private $uid;

    private $name;

    private $binPath;

    public function __construct($pid, $uid, $name)
    {
        $this->pid = $pid;
        $this->uid = $uid;
        $this->name = $name;
    }

    public function setBinaryPath($path)
    {
        $this->binPath = $path;
    }

    public function setEnvironment(array $env)
    {
        $this->env = $env;
    }

    public function setParentId($pid)
    {
        $this->parentId = intval($pid);
    }

    public function setArguments(array $arguments)
    {
        $this->args = $arguments;
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEnvironment()
    {
        return $this->env;
    }

    public function getArguments()
    {
        return $this->args;
    }

    public function getBinaryPath()
    {
        return $this->binPath;
    }
}
