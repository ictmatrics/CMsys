<?php

namespace Config;

class Paths
{
    public string $systemDirectory;
    public string $appDirectory;
    public string $writableDirectory;
    public string $viewDirectory;

    public function __construct()
    {
        $this->systemDirectory   = realpath(__DIR__ . '/../System') ?: (__DIR__ . '/../System');
        $this->appDirectory      = realpath(__DIR__ . '/..') ?: (__DIR__ . '/..');
        $this->writableDirectory = defined('FCPATH') ? FCPATH . 'Writables' : (realpath(__DIR__ . '/../../public_html/Writables') ?: '');
        $this->viewDirectory     = realpath(__DIR__ . '/../Views') ?: (__DIR__ . '/../Views');
    }
}
