<?php
require_once __DIR__ . '/../vendor/autoload.php';

use GuiBranco\PocMvc\App\Registration;
use GuiBranco\PocMvc\Src\Application;

$app = new Application();
$registration = new Registration($app);
$registration->addServices();
$registration->registerRoutes();
$registration->registerApiControllers();
$app->run();
