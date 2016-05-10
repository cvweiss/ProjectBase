<?php

namespace Project\Base;

Config::setAll([
	'debug' => true,
	'siteName' => 'Project Base',

	// Project settings
	'projectDir' => realpath(__DIR__ . '/../'),
]);

if (file_exists(__DIR__ . '/../config.json'))
{
    $raw = file_get_contents(__DIR__ . '/../config.json');
    $config = json_decode($raw, true);
    foreach ($config as $key=>$value) Config::set($key, $value, true);
}
