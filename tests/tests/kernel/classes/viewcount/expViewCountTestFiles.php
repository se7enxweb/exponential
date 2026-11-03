<?php
/**
 * Helpers of the view counter tests: a directory of their own under var/tmp, removed afterwards, and access log
 * lines in the combined format.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

trait expViewCountTestFiles
{
    /** @var string|null */
    private $viewCountDir = null;

    /** @return string the installation root of this checkout */
    protected static function viewCountRoot()
    {
        return dirname( __DIR__, 5 );
    }

    /** @return string a directory under var/tmp, made once per test */
    protected function viewCountDir()
    {
        if ( $this->viewCountDir === null )
        {
            $this->viewCountDir = self::viewCountRoot() . '/var/tmp/viewcount-test-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
            mkdir( $this->viewCountDir . '/logs', 0775, true );
        }
        return $this->viewCountDir;
    }

    /** Removes the directory of viewCountDir() with what is in it. */
    protected function removeViewCountDir()
    {
        if ( $this->viewCountDir === null || !is_dir( $this->viewCountDir ) )
            return;
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->viewCountDir, FilesystemIterator::SKIP_DOTS ),
                                                   RecursiveIteratorIterator::CHILD_FIRST );
        foreach ( $iterator as $item )
            $item->isDir() ? rmdir( $item->getPathname() ) : unlink( $item->getPathname() );
        rmdir( $this->viewCountDir );
        $this->viewCountDir = null;
    }

    /**
     * A line of the combined format, with a line end.
     *
     * @param string $path
     * @param int $status
     * @param string $agent
     * @param string $method
     * @param string $time
     * @return string
     */
    protected static function combined( $path, $status = 200, $agent = 'Mozilla/5.0 (X11; Linux x86_64) Firefox/140.0', $method = 'GET',
                                        $time = '03/Nov/2026:10:00:00 +0000' )
    {
        return '192.0.2.10 - - [' . $time . '] "' . $method . ' ' . $path . ' HTTP/1.1" ' . $status . ' 1234 "-" "' . $agent . '"' . "\n";
    }
}
