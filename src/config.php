<?php

namespace Project\Base;

$vendorDir = dirname(dirname(dirname(__DIR__)));
// Composer insists that libs are kept in vendor, so we'll make that assumption
$projectDir = basename($vendorDir) == 'vendor' ? dirname($vendorDir) : dirname(__DIR__);

$config = Config::getInstance();

$config->setAll([
	'debug' => true,
	'siteName' => 'Project Base',

	// Project settings
	'projectDir' => $projectDir
]);

if (file_exists($projectDir . '/config.json'))
{
    $raw = file_get_contents($projectDir . '/config.json');
    $configValues = json_decode($raw, true);
    foreach ($configValues as $key=>$value) {
        $config->set($key, $value, true);
    }
}
