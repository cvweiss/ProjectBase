<?php

use \Project\Supply\Config;

$jade = new \Tale\Jade\Renderer([
	'paths' => [\Project\Supply\Config::get('projectDir') . '/view/'],
	'pretty' => Config::get('debug', false),
]);

$view = new \Project\Supply\Render($jade);
Config::set('view', $view);
