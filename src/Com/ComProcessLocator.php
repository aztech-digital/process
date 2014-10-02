<?php

namespace Aztech\Process\Com;

class ComProcessLocator
{
	public function getProcess($pid)
	{
		$WbemLocator = new \COM("WbemScripting.SWbemLocator");
		$WbemServices = $WbemLocator->ConnectServer(php_uname("n"), 'root\\cimv2');
		$WbemServices->Security_->ImpersonationLevel = 3;
		
		$processes = $WbemServices->ExecQuery("SELECT * FROM Win32_Process WHERE ProcessId = " . intval($pid));
		
		if ($processes->Count == 0) {
			throw new \RuntimeException('Process not found.');
		}
		
		return $processes->ItemIndex(0);
	}
}