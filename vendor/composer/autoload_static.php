<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4cd3394acd2c8b4243ac9e20573a5365
{
    public static $prefixesPsr0 = array (
        'R' => 
        array (
            'Rych\\Random' => 
            array (
                0 => __DIR__ . '/..' . '/rych/random',
            ),
        ),
    );

    public static $classMap = array (
        'hocine\\protun\\core\\Hash' => __DIR__ . '/../..' . '/core/Hash.php',
        'hocine\\protun\\core\\Post' => __DIR__ . '/../..' . '/core/Post.php',
        'hocine\\protun\\core\\User' => __DIR__ . '/../..' . '/core/User.php',
        'hocine\\protun\\core\\db' => __DIR__ . '/../..' . '/core/db.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInit4cd3394acd2c8b4243ac9e20573a5365::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit4cd3394acd2c8b4243ac9e20573a5365::$classMap;

        }, null, ClassLoader::class);
    }
}
