<?php
/**
 * Created by PhpStorm.
 * User: HocineFR
 * Date: 31-12-2016
 * Time: 23:11
 */

namespace hocine\protun\core;


class Hash
{
    private static $_salt = '$[[158#~^\@@]}}]';
    const LENGTH = 8;

    public static function Make( $pass, $type = 'sha512' )
    {
        if( trim( $pass ) < self::LENGTH  )
        {
            return null;
        }
        return hash( $type, $pass.self::$_salt );
    }

}