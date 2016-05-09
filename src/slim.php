<?php

namespace Project\Supply;

// Create and configure Slim app
$app = new \Slim\App;

$app->get('/', function ($request, $response, $args) {
        global $app, $view, $jade;
	require_once Config::get('projectDir') . "/controller/index.php";
	return $response;
});

// Run app
$app->run();
