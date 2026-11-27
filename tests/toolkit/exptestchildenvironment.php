<?php
/**
 * The environment of a child process a test starts with only the variables it names (PATH, HOME, ...).
 *
 * On Windows a process without SystemRoot cannot start Winsock, and PHP stops with "Unable to start curl module";
 * without TEMP and TMP it has no temporary directory. Those variables of Windows are kept; elsewhere the named
 * variables are the whole environment, as before.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */
class expTestChildEnvironment
{
    /** The variables Windows needs in every process */
    const WINDOWS = array( 'SystemRoot', 'SystemDrive', 'windir', 'TEMP', 'TMP', 'COMSPEC', 'PATHEXT' );

    /**
     * @param array $variables name => value
     * @return array
     */
    public static function with( array $variables )
    {
        if ( PHP_OS_FAMILY !== 'Windows' )
            return $variables;
        foreach ( self::WINDOWS as $name )
        {
            $value = getenv( $name );
            if ( $value !== false && !array_key_exists( $name, $variables ) )
                $variables[$name] = $value;
        }
        return $variables;
    }
}
