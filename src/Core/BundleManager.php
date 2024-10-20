<?php

namespace GuiBranco\PocMvc\Src\Core;

class BundleManager
{
    private static $bundles = [];

    public static function register($bundleName, $assets)
    {
        if (!isset(self::$bundles[$bundleName])) {
            self::$bundles[$bundleName] = $assets;
        } else {
            self::$bundles[$bundleName] = array_merge(self::$bundles[$bundleName], $assets);
        }
    }

    public static function getBundle($bundleName)
    {
        return self::$bundles[$bundleName] ?? [];
    }

    public static function getAllBundles()
    {
        return self::$bundles;
    }
}
