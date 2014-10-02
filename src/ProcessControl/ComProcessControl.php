<?php

namespace Aztech\Process\ProcessControl;

use Aztech\Process\ProcessControl;
use Aztech\Process\CurrentProcess;
use Aztech\Process\ProcessInfo;
use Aztech\Process\ExternalProcess;
use Aztech\Process\Com\ComProcessLocator;
use Aztech\Process\Com\ComProcessManager;

class ComProcessControl implements ProcessControl
{
	
	/**
	 * @var ComProcessManager
	 */
	private $manager;
	
	public function __construct(ComProcessManager $manager = null)
	{
		$this->manager = $manager ?: new ComProcessManager();
	}
	
	public function kill(ProcessInfo $info, $signal)
	{
		if ($signal == 9) {
			$process = $this->manager->getLocator()->getProcess($pid);
			$process->Terminate_();
		}
		
		if ($signal == 17) {
			$process = $this->manager->sendKeys($info, '{BREAK}');
		}
		
		return;
	}
	
	public function restart(CurrentProcess $process)
	{		
		$comProcess = $this->manager->create($process->getInfo());
		
		return new ExternalProcess($out->ProcessId);
	}
	
	public function waitFor($childPid)
	{
		$childPid->send('done');
	}
	
	public function wait(array $children)
	{
		foreach ($children as $child) {
			echo '>>> Resuming interrupted function...' . PHP_EOL;
			// It runs ...
			//$child->current();
			$child->send(null);
			echo '>>> Done' . PHP_EOL;
		}
		
		return;
	}
}