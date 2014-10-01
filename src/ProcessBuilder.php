<?php

namespace Aztech\Process;

class ProcessBuilder
{

    private $command;

    private $args = [];

    private $env = null;

    private $workingDirectory = null;

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
        return $this->env ?: $_ENV;
    }

    public function setWorkingDirectory($path)
    {
        $this->workingDirectory = $path;
    }

    public function getWorkingDirectory()
    {
        return $this->workingDirectory ?: getcwd();
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

        $env = $this->getEnvironment();
        $cwd = $this->getWorkingDirectory();

        $process = proc_open($cmd, $descriptorspec, $pipes, $cwd, $env);

        if ($process !== false) {
            return new AttachedProcess($process, $pipes, (new InspectorFactory())->create());
        }

        throw new \RuntimeException('Unable to start process.');
    }
}
