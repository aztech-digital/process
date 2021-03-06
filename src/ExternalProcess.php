<?php

namespace Aztech\Process;

class ExternalProcess extends AbstractProcess
{
	
	private $inspector;
	
    public function __construct($pid, Inspector $inspector = null)
    {
        $this->pid = $pid;

        $currentProcess = CurrentProcess::getInstance();

        if ($currentProcess && intval($pid, 10) === $currentProcess->getInfo()->getPid()) {
            throw new \InvalidArgumentException('Use CurrentProcess::getInstance() to access current process.');
        }

        $this->inspect($inspector ?: (new InspectorFactory())->create());
        
        parent::__construct($this->inspector->getSignalEmitter());
    }

    private function inspect(Inspector $inspector)
    {
        $this->inspector = $inspector;

        $this->info = $inspector->getProcessInfo($this->pid);
        $this->pipes = $inspector->getProcessPipes($this->info);
    }
}
