<?php
use HabrReverseProxy\App;

require_once 'vendor/autoload.php';

$app = new App($_REQUEST);
echo $app->run();
die();
