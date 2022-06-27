<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitb7fec5fd27bc6a31c84c88fc1b4ba9dd
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitb7fec5fd27bc6a31c84c88fc1b4ba9dd', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitb7fec5fd27bc6a31c84c88fc1b4ba9dd', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitb7fec5fd27bc6a31c84c88fc1b4ba9dd::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
