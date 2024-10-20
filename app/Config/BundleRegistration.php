<?php

namespace GuiBranco\PocMvc\App\Config;
use GuiBranco\PocMvc\Src\Core\BundleManager;

class BundleRegistration
{
    public static function registerBundles()
    {
        BundleManager::register('styles', [
            '/public/css/style.css',
        ]);

        BundleManager::register('scripts', [
            '/public/js/scritps.js'
        ]);
    }
}
