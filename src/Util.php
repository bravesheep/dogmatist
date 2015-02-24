<?php

namespace Bravesheep\Dogmatist;

class Util 
{
    /**
     * @param string $class
     * @return bool
     */
    public static function isUserClass($class)
    {
        return !self::isSystemClass($class);
    }

    /**
     * @param string $class
     * @return bool
     */
    public static function isSystemClass($class)
    {
        return $class === 'object' || $class === 'array' || $class === 'stdClass';
    }
}
