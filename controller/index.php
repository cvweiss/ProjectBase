<?php

$content = ['content' => 'Hello World!', 'title' => 'Home Page'];

Project\Supply\Config::get('view')->render("index", $content);
