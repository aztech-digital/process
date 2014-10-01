<?php

use Aztech\Process\Process;
use Aztech\Process\CurrentProcess;
use Aztech\Process\ExternalProcess;

require_once __DIR__ . '/../vendor/autoload.php';

$pid = $argv[1];

$process = new ExternalProcess($pid);

$process->kill(SIGTSTP);
$process->kill(SIGCONT);

$mainPid = CurrentProcess::getInstance()->getPid();
echo $mainPid . ' : Main process PID ' . CurrentProcess::getInstance()->getPid() . PHP_EOL;
echo $mainPid . ' : Parent process PID ' . CurrentProcess::getInstance()->getInfo()->getParentId() . PHP_EOL;

$task = function() use($pid) {
    $process = new ExternalProcess($pid);

    $pid = CurrentProcess::getInstance()->getPid();

    echo $pid .  ' : Child started' . PHP_EOL;
    echo $pid .  ' : Parent process PID ' . CurrentProcess::getInstance()->getInfo()->getParentId() . PHP_EOL;

    $process->kill(SIGTSTP);
    $process->kill(SIGCONT);

    CurrentProcess::getInstance()->fork(function () {
        $pid = CurrentProcess::getInstance()->getPid();

        echo $pid .  ' : Child started with PID ' . CurrentProcess::getInstance()->getPid() . PHP_EOL;
        echo $pid .  ' : Parent process PID ' . CurrentProcess::getInstance()->getInfo()->getParentId() . PHP_EOL;

        echo $pid .  ' : A fork within a fork !' . PHP_EOL;

        sleep(1);
    });

    echo $pid .  ' : Restarting PID ' . CurrentProcess::getInstance()->getPid() . PHP_EOL;
    CurrentProcess::getInstance()->restart();
    CurrentProcess::getInstance()->wait();

    echo $pid . ' : Child done' . PHP_EOL;
};

$child = CurrentProcess::getInstance()->fork($task);

CurrentProcess::getInstance()->wait();

//echo 'Forking as "foreground" process' . PHP_EOL;

$child = CurrentProcess::getInstance()->fork(function() use($pid) {
    sleep(10);

    throw new RuntimeException('This should not happen...');
}, false);

//echo 'Parent dying...' . PHP_EOL;
