<?php
/**
 * Created by PhpStorm.
 * User: HocineFR
 * Date: 30-12-2016
 * Time: 22:13
 */

spl_autoload_register(function($class){

    if (isset($class)) {
        require $class.".php";
    }

});