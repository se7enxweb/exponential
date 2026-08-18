<?php

namespace PHPUnit\TextUI\Configuration;

final class Registry
{
    private static $configuration;

    public static function get()
    {
        return self::$configuration;
    }

    public static function set( $configuration ): void
    {
        self::$configuration = $configuration;
    }

    public static function init( $configuration ): void
    {
        if ( self::$configuration === null )
        {
            self::$configuration = $configuration;
        }
    }
}
