<?php

use Aztech\Process\ProcessBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

$builder = new ProcessBuilder();
$builder->setCommand('/bin/bash');

$process = $builder->run();

$process->writeStdIn("echo \"\033[1mhello world\033[0m\"");
$process->readStdOut(function ($text) {
    echo $text;
});

