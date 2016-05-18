<?php

namespace Project\Base;

$paths = [Config::get('projectDir') . '/view/', Config::get('projectDir') . '/vendor/cvweiss/project.base/view/'];

$jade = new \Tale\Jade\Renderer([
	'paths' => $paths,
	'pretty' => Config::get('debug', false),
        'cachePath' => Config::get('cachePath', Config::get('projectDir') . '/cache/jade/'),
]);

$view = new Render($jade);
