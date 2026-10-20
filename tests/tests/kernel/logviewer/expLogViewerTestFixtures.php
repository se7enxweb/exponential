<?php
/**
 * Shared fixtures of the log viewer tests (doc/en/us/features/6.0/log-viewer.md): a throwaway installation root in the
 * temporary directory with synthetic log lines (no real log is ever read), and its removal.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expLogViewerTestFixtures
{
    /**
     * A new empty root with var/log and var/site/log.
     *
     * @param string $prefix
     * @return string the real path
     */
    public static function root( $prefix = 'lv' )
    {
        $dir = sys_get_temp_dir() . '/' . $prefix . '-' . bin2hex( random_bytes( 6 ) );
        mkdir( $dir . '/var/log', 0777, true );
        mkdir( $dir . '/var/site/log', 0777, true );
        return realpath( $dir );
    }

    /**
     * Removes a directory tree (links are removed, never followed).
     *
     * @param string $dir
     */
    public static function remove( $dir )
    {
        if ( $dir === '' || $dir === null || !file_exists( $dir ) && !is_link( $dir ) )
            return;
        if ( is_link( $dir ) || is_file( $dir ) )
        {
            unlink( $dir );
            return;
        }
        foreach ( scandir( $dir ) as $name )
            if ( $name !== '.' && $name !== '..' )
                self::remove( $dir . '/' . $name );
        rmdir( $dir );
    }

    /**
     * eZDebug style entries: a header line and a message line each, one second apart.
     *
     * @param int $count
     * @param int $start Unix time of the first
     * @param string $url
     * @param string $ipPrefix
     * @return string
     */
    public static function debugLines( $count, $start, $url = 'https://example.test/admin/content/view/full/2', $ipPrefix = '203.0.113.' )
    {
        $out = '';
        for ( $i = 0; $i < $count; $i++ )
        {
            $time = date( 'M d Y H:i:s', $start + $i );
            $out .= "[ $time ] [" . $ipPrefix . ( $i % 250 ) . "] [admin] [" . $url . "] eZTemplate:\n";
            $out .= "Error number " . $i . " happened\n";
        }
        return $out;
    }

    /**
     * A configuration for a root, with the chunk cache in <root>/cache.
     *
     * @param string $root
     * @param array $values
     * @return expLogViewerConfig
     */
    public static function config( $root, array $values = array() )
    {
        return expLogViewerConfig::fromArray( $values, $root );
    }
}
