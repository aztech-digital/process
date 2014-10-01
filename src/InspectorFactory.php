<?php

namespace Aztech\Process;

use Aztech\Process\Inspector\ComProcessInspector;
use Aztech\Process\Inspector\ProcFilesystemInspector;

class InspectorFactory
{

    public function create()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            if (class_exists('\COM')) {
                return new ComProcessInspector();
            }

            throw new \BadMethodCallException('Not supported (install COM extension).');
        }
        else {
            return new ProcFilesystemInspector();
        }
    }
}
