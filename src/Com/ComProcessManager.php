<?php

namespace Aztech\Process\Com;

use Aztech\Process\CurrentProcess;
class ComProcessManager
{
	private $locator;
	
	public function __construct(ComProcessLocator $locator = null)
	{
		$this->locator = $locator ?: new ComProcessLocator();
	}
	
	public function create()
	{
		$com = new \COM("winmgmts:{impersonationLevel=impersonate}!\\\\.\\root\\cimv2:Win32_Process");
		$method = $com->Methods_('Create');
		
		$in = $method->inParameters->SpawnInstance_();
		$in->CommandLine = $process->getInfo()->getCommandLine();
		$in->CurrentDirectory = $process->getInfo()->getWorkingDirectory();
		
		return $com->ExecMethod_("Create", $in);
	}
	
	public function sendKeys($pid, $keys)
	{
		$shell = new \Com('Wscript.Shell');
		
		$shell->AppActivate($pid);
		$shell->SendKeys($keys);
		$shell->AppActivate(CurrentProcess::getInstance()->getPid());
	}
	
	/**
	 * 
	 * @return ComProcessLocator
	 */
	public function getLocator()
	{
		return $this->locator;
	}
}