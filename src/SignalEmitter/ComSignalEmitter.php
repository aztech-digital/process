<?php

namespace Aztech\Process\SignalEmitter;

use Aztech\Process\SignalEmitter;
use Aztech\Process\Com\ComProcessLocator;

class ComSignalEmitter implements SignalEmitter
{
	
	private $locator;
	
	public function __construct(ComProcessLocator $locator)
	{
		$this->locator = $locator;
	}
	
	public function kill($pid, $signal)
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
			return;
		}

		if ($signal == 9) {
			try {
				$process = $this->locator->getProcess($pid);
			
				return ($process->Terminate() == 0);
			}
			catch (\Exception $ex) {
				return false;
			}
		}	
		
		return;
	}
}