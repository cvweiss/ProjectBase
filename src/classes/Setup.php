<?php

namespace cvweiss\projectbase;

class Setup
{
    public static function prepareProject()
    {
        $vendorDir = dirname(dirname(dirname(__DIR__)));
        // Composer insists that libs are kept in vendor, so we'll make that assumption
        $projectDir = basename($vendorDir) == 'vendor' ? dirname($vendorDir) : dirname(dirname(__DIR__));

        // Create necessary directories
        $dirs = ['/view/', '/cache/', '/cache/twig'];
        foreach ($dirs as $dir) self::makeDir($projectDir . $dir);

        // Copy the public directory
        if (strlen($vendorDir) > strlen($projectDir)) {
            echo("cp -r $vendorDir/public $projectDir/public\n");
        }
    }

    private static function makeDir($dir)
    {
        if (is_dir($dir)) return;
        echo "Creating directory: $dir\n";
        mkdir($dir);
    }
}
