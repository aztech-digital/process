<?php

namespace Aztech\Process;

interface SignalEmitter
{
	function kill($pid, $signal);
}