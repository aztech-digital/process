<?php

namespace Aztech\Process\ProcessControl;

use Aztech\Process\ProcessControl;
use Aztech\Process\ProcessInfo;
use Aztech\Process\CurrentProcess;

class PcntlProcessControl implements ProcessControl
{
 
	public function kill(ProcessInfo $info, $signal)
	{
		posix_kill($info->getPid(), $signal);
	}
	
	public function restart(CurrentProcess $process)
	{
		if ($process->isFork()) {
			return false;
		}
		
		$info = $process->getInfo();
		
		return pcntl_exec($info->getBinaryPath(), $info->getArguments());
	}
	
	public function waitFor($childPid)
	{
		$status = null;
	
		pcntl_waitpid($childPid, $status);
	}
	
	public function wait(array $children)
	{
		$status = null;
	
		while (count($children)) {
			for ($i = count($children) - 1; $i >= 0; $i --) {
				if (pcntl_waitpid($children[$i], $status, WNOHANG) == $children[$i]) {
					unset($children[$i]);
	
					continue;
				}
	
				usleep(250000);
			}
		}
	}
}