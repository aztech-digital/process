<?php

namespace Aztech\Process\Inspector;

use Aztech\Process\Inspector;
use Aztech\Process\ProcessInfo;

class ProcFilesystemInspector implements Inspector
{
    public function getProcessInfo($pid)
    {
        $process = $this->buildFromStatus($pid);

        $this->populateArguments($process);
        $this->populateEnvironment($process);

        return $process;
    }

    private function buildFromStatus($pid)
    {
        $pidDescriptor = '/proc/' . intval($pid) . '/status';

        if (! file_exists($pidDescriptor)) {
            throw new \RuntimeException('No such process.');
        }

        $data = [];
        $status = file_get_contents($pidDescriptor);
        $lines = explode(PHP_EOL, $status);

        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                list($name, $value) = explode(':', $line, 2);

                $data[strtolower(trim($name))] = trim($value);
            }
        }

        $pid = intval($data['pid']);
        $uid = intval($data['uid']);
        $name = (string) $data['name'];

        $process = new ProcessInfo($pid, $uid, $name);

        $process->setParentId(intval($data['ppid']));

        return $process;
    }

    private function populateArguments(ProcessInfo $process)
    {
        $argsDescriptor = '/proc/' . $process->getPid() . '/cmdline';

        if (file_exists($argsDescriptor)) {
            $commandLine = file_get_contents($argsDescriptor);
            $args = explode("\000", $commandLine);
            $args = array_slice($args, 1, -1);

            $process->setArguments($args);
        }

        $exeDescriptor = '/proc/' . $process->getPid() . '/exe';

        if (file_exists($exeDescriptor)) {
            if (is_link($exeDescriptor)) {
                $exeDescriptor = readlink($exeDescriptor);
            }
        }

        $process->setBinaryPath($exeDescriptor);
    }

    private function populateEnvironment(ProcessInfo $process)
    {
        $envDescriptor = '/proc/' . $process->getPid() . '/environ';
        $env = [];

        if (file_exists($envDescriptor) && is_readable($envDescriptor)) {
            $procEnv = explode("\000", trim(file_get_contents($envDescriptor)));

            asort($procEnv);

            foreach ($procEnv as $declare) {
                list($name, $value) = explode('=', $declare, 2);
                $env[$name] = $value;
            }
        }

        $process->setEnvironment($env);
    }

    public function getProcessPipes(ProcessInfo $process)
    {
        $fdDescriptor = '/proc/' . $process->getPid() . '/fd/*';
        $pipes = [];

        foreach (glob($fdDescriptor) as $file) {
            if (file_exists($file) && is_readable($file)) {
                $mode = 'a+';

                if (basename($file) == '0') {
                    $mode = 'r';
                }

                $pipe = fopen($file, $mode);
                $pipes[basename($file)] = $pipe;
            }
        }

        return $pipes;
    }
}
