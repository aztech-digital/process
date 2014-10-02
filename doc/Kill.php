<?php

use Aztech\Process\Process;
use Aztech\Process\CurrentProcess;
use Aztech\Process\ExternalProcess;

require_once __DIR__ . '/../vendor/autoload.php';

$pid = $argv[1];

$process = new ExternalProcess($pid);
$process->kill(17);
