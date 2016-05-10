<?php

namespace Project\Base;

$jade = new \Tale\Jade\Renderer([
	'paths' => [Config::get('projectDir') . '/view/'],
	'pretty' => Config::get('debug', false),
        'cachePath' => Config::get('projectDir') . '/cache/jade/',
]);

$view = new Render($jade);
