<?php
/**
 * Reads settings/syscard.ini for the system card tests without eZINI (no installation, no cache).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expSysCardTestIni
{
    /**
     * An INI file the way eZINI reads its plain lines: [Block], Name=value, Name[]=value (Name[] alone empties the
     * list), Name[key]=value; # comments.
     *
     * @param string $file
     * @return array block => name => value
     */
    public static function read( $file )
    {
        $data = array();
        $block = '';
        foreach ( file( $file, FILE_IGNORE_NEW_LINES ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' || $line[0] === '#' )
                continue;
            if ( preg_match( '/^\[(.+)\]$/', $line, $m ) )
            {
                $block = $m[1];
                if ( !isset( $data[$block] ) )
                    $data[$block] = array();
                continue;
            }
            if ( preg_match( '/^([A-Za-z0-9_-]+)\[\]$/', $line, $m ) )
            {
                $data[$block][$m[1]] = array();
                continue;
            }
            if ( preg_match( '/^([A-Za-z0-9_-]+)\[([^\]]*)\]=(.*)$/', $line, $m ) )
            {
                if ( $m[2] === '' )
                    $data[$block][$m[1]][] = $m[3];
                else
                    $data[$block][$m[1]][$m[2]] = $m[3];
                continue;
            }
            if ( preg_match( '/^([A-Za-z0-9_-]+)=(.*)$/', $line, $m ) )
                $data[$block][$m[1]] = $m[2];
        }
        return $data;
    }

    /**
     * @return string settings/syscard.ini of this checkout
     */
    public static function file()
    {
        return dirname( __DIR__, 5 ) . '/settings/syscard.ini';
    }
}
