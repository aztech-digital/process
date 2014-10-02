<?php

namespace Aztech\Process;

interface ProcessControl
{
	
	function kill(ProcessInfo $process, $signal);
	
	function restart(CurrentProcess $process);
	
	function wait(array $children);
	
	function waitFor($pid);
	
}