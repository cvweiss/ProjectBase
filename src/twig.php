<?php

namespace cvweiss\projectbase;

$config = Config::getInstance();

$defaultDir = $config->get('projectDir') . '/view/';
$otherDir = $config->get('projectDir') . '/vendor/cvweiss/projectbase/view/';
$defaultExists = is_dir($defaultDir);

$path = $defaultExists ? $defaultDir : $otherDir;

$loader = new \Twig_Loader_Filesystem($path);

$twig = new \Twig_Environment($loader, [
		'cache' => $config->get('cachePath', $config->get('projectDir') . '/cache/twig'),
		'debug' => $config->get('debug', false),
]);

$view = new Render($twig);

//$twig->render('index.html', array('name' => 'Fabien'));
