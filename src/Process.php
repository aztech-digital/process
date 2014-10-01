<?php

namespace Aztech\Process;

interface Process
{

    function getPid();

    function getInfo();

    function hasParentProcess();

    function getParentProcess();

    function getPipes();

    function kill($signal);

}
