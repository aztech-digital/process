<?php

namespace Aztech\Process\SignalEmitter;

use Aztech\Process\SignalEmitter;

class PosixSignalEmitter implements SignalEmitter
{
	public function kill($pid, $signal)
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			return;
		}

		return posix_kill($pid, $signal);
	}
}