<?php

namespace Aztech\Process;

interface Inspector
{
    /**
     *
     * @param int $pid
     * @return ProcessInfo
     */
    function getProcessInfo($pid);

    function getProcessPipes(ProcessInfo $processInfo);
}
