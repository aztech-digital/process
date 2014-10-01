<?php

namespace Aztech\Process;

class ProcessBuilder
{

    private $command;

    private $args = [];

    private $env = null;

    public function setCommand($executablePath)
    {
        $this->command = $executablePath;

        return $this;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function setArguments(array $args = null)
    {
        $this->args = $args ?: [];

        return $this;
    }

    public function getArguments()
    {
        return $this->args;
    }

    public function setEnvironment(array $env = null)
    {
        $this->env = $env;
    }

    public function getEnvironment()
    {
        return $this->env;
    }

    /**
     *
     * @throws \RuntimeException
     * @return AttachedProcess
     */
    public function run()
    {
        $cmd = trim(sprintf('%s %s', $this->command, implode(' ', $this->args)));
        $descriptorspec = [
            0 => [ 'pipe', 'r' ],
            1 => [ 'pipe', 'w' ],
            2 => [ 'file', 'php://stderr', 'a' ]
        ];
        $pipes = [];

        $process = proc_open($cmd, $descriptorspec, $pipes);

        if ($process !== false) {
            return new AttachedProcess($process, $pipes, (new InspectorFactory())->create());
        }

        throw new \RuntimeException('Unable to start process.');
    }
}
