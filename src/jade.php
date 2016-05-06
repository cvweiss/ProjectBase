<?php

use \Project\Supply\Config;

$jade = new \Tale\Jade\Renderer([
	'paths' => [Config::get('projectDir') . '/view/'],
	'pretty' => Config::get('debug', false),
        'cachePath' => Config::get('projectDir') . '/cache/jade/',
]);

$view = new \Project\Supply\Render($jade);
Config::set('view', $view);
