<?php

namespace Project\Base;

$vendorDir = dirname(dirname(dirname(__DIR__)));
// Composer insists that libs are kept in vendor, so we'll make that assumption
$projectDir = basename($vendorDir) == 'vendor' ? dirname($vendorDir) : dirname(__DIR__);

Config::setAll([
	'debug' => true,
	'siteName' => 'Project Base',

	// Project settings
	'projectDir' => $projectDir
]);

if (file_exists($projecDir . '/config.json'))
{
    $raw = file_get_contents($projectDir . '/config.json');
    $config = json_decode($raw, true);
    foreach ($config as $key=>$value) Config::set($key, $value, true);
}
