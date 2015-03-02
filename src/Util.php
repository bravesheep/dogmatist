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

    /**
     * @param string $name
     * @return string
     */
    public static function normalizeName($name)
    {
        $name = preg_replace_callback("/[A-Z]/", function ($match) { return '_' . strtolower($match[0]); }, $name);
        $name = trim($name, "_- \t\n\r");
        return $name;
    }
}
