<?php

namespace Project\Base;

$config = Config::getInstance();

$paths = [$config->get('projectDir') . '/view/', $config->get('projectDir') . '/vendor/cvweiss/project.base/view/'];

$jade = new \Tale\Jade\Renderer([
	'paths' => $paths,
	'pretty' => $config->get('debug', false),
        'cachePath' => $config->get('cachePath', $config->get('projectDir') . '/cache/jade/'),
]);

$view = new Render($jade);
