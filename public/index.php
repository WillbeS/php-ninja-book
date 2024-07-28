<?php

include __DIR__ . '/../includes/autoload.php';

$uri = strtok(ltrim($_SERVER['REQUEST_URI'], '/'), '?');

// echo $uri;


$website = new \Ijdb\JokeWebsite();
$entryPoint = new \Ninja\EntryPoint($website);
$entryPoint->run($uri, $_SERVER['REQUEST_METHOD']);

    
