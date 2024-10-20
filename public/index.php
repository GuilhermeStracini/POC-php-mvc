<?php
use GuiBranco\PocMvc\App\Config\BundleRegistration;
use GuiBranco\PocMvc\App\Config\Registration;
use GuiBranco\PocMvc\Src\Core\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application();
$registration = new Registration($app);
$registration->addServices();
$registration->registerRoutes();
$registration->registerApiControllers();
$bundleRegistration = new BundleRegistration();
$bundleRegistration->registerBundles();
$app->run();
