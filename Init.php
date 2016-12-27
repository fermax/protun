<?php
/**
 * Created by PhpStorm.
 * User: HocineFR
 * Date: 29-11-2016
 * Time: 11:37
 */

spl_autoload_register(function($class){

    if (isset($class)) {
        require $class.".php";
    }

});