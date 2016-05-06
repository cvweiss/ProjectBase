<?php

namespace Project\Supply;

Config::setAll([
	'debug' => true,
	'siteName' => 'Project Supply',

	// Project settings
	'projectDir' => realpath(__DIR__ . '/../'),
]);
