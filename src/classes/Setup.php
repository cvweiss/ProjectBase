<?php

namespace Project\Base;

class Setup
{
    public static function prepareProject()
    {
        $vendorDir = dirname(dirname(dirname(__DIR__)));
        // Composer insists that libs are kept in vendor, so we'll make that assumption
        $projectDir = basename($vendorDir) == 'vendor' ? dirname($vendorDir) : dirname(__DIR__);

        $dirs = ['/view/', '/cache/', '/cache/jade'];
        foreach ($dirs as $dir) self::makeDir($projectDir . $dir);
    }

    private static function makeDir($dir)
    {
        if (is_dir($dir)) return;
        echo "Creating directory: $dir\n";
        mkdir($dir);
    }
}
