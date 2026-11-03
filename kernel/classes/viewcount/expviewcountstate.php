<?php
/**
 * File containing the expViewCountState class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Where the view counter stopped reading an access log, for each siteaccess that counts from it: one JSON file per
 * log in the state directory (logfile.ini [ViewCountSettings] StateDir, by default <VarDir>/viewcount), with a lock
 * file next to it so two runs never read the same log at once.
 *
 *   {"format": "exp-viewcount-state/1", "log": "/var/log/httpd/access_log",
 *    "sites": {"site": {"inode": 1234, "device": 64768, "offset": 81920, "head": "<sha1 of the first line>",
 *                       "lines": 512, "updated": "2026-11-03T10:00:00+00:00"}}}
 *
 * Each siteaccess has an offset of its own, so siteaccesses that share a log, whether counted by one run or by one
 * run each (-s), never move each other's place, and a siteaccess added later starts at the beginning of the log
 * instead of where the others are. The file is written to a temporary file and renamed: a run that stops halfway
 * leaves the previous state, never half of one.
 */
class expViewCountState
{
    const FORMAT = 'exp-viewcount-state/1';

    /** @var string */
    private $directory;

    /** @var string */
    private $logFile;

    /** @var array|null site name => entry */
    private $sites = null;

    /** @var resource|null */
    private $lockHandle = null;

    /**
     * @param string $directory the state directory (made when the state is saved)
     * @param string $logFile the access log the state is for
     */
    public function __construct( $directory, $logFile )
    {
        $this->directory = rtrim( (string)$directory, '/' );
        $this->logFile = (string)$logFile;
    }

    /**
     * The state file of the log: the log's file name, made safe, and a hash of its full path, so two logs with the
     * same name in different directories (two virtual hosts' access_log) never share one.
     *
     * @return string
     */
    public function file()
    {
        $name = preg_replace( '~[^A-Za-z0-9._-]+~', '-', basename( $this->logFile ) );
        return $this->directory . '/' . trim( $name, '-.' ) . '-' . substr( sha1( $this->logFile ), 0, 12 ) . '.json';
    }

    /**
     * Takes the lock of the log, waiting up to $timeout seconds for a run that holds it.
     *
     * @param int $timeout seconds
     * @return bool false: another run still holds it, or the lock file cannot be made
     */
    public function lock( $timeout = 60 )
    {
        if ( $this->lockHandle !== null )
            return true;
        if ( !is_dir( $this->directory ) && !@mkdir( $this->directory, eZDir::dirMode( 0775 ), true ) && !is_dir( $this->directory ) )
            return false;
        $handle = @fopen( $this->file() . '.lock', 'c' );
        if ( !$handle )
            return false;
        $until = microtime( true ) + max( 0, (int)$timeout );
        while ( !flock( $handle, LOCK_EX | LOCK_NB ) )
        {
            if ( microtime( true ) >= $until )
            {
                fclose( $handle );
                return false;
            }
            usleep( 250000 );
        }
        $this->lockHandle = $handle;
        return true;
    }

    /** Lets the lock go. */
    public function unlock()
    {
        if ( $this->lockHandle !== null )
        {
            flock( $this->lockHandle, LOCK_UN );
            fclose( $this->lockHandle );
            $this->lockHandle = null;
        }
    }

    /**
     * The entry of a site: inode, device, offset, head, lines, updated; null when it has none.
     *
     * @param string $site
     * @return array|null
     */
    public function entry( $site )
    {
        $sites = $this->sites();
        return isset( $sites[$site] ) ? $sites[$site] : null;
    }

    /**
     * @param string $site
     * @param array $entry
     */
    public function set( $site, array $entry )
    {
        $this->sites();
        $this->sites[(string)$site] = $entry;
    }

    /** @param string $site */
    public function remove( $site )
    {
        $this->sites();
        unset( $this->sites[(string)$site] );
    }

    /** @return array site name => entry */
    public function sites()
    {
        if ( $this->sites === null )
        {
            $this->sites = array();
            $file = $this->file();
            if ( is_file( $file ) )
            {
                $data = json_decode( (string)@file_get_contents( $file ), true );
                if ( is_array( $data ) && isset( $data['sites'] ) && is_array( $data['sites'] ) )
                    $this->sites = $data['sites'];
            }
        }
        return $this->sites;
    }

    /**
     * Writes the state: to a temporary file next to it, then renamed over it.
     *
     * @return bool
     */
    public function save()
    {
        if ( !is_dir( $this->directory ) && !@mkdir( $this->directory, eZDir::dirMode( 0775 ), true ) && !is_dir( $this->directory ) )
            return false;
        $file = $this->file();
        $data = array( 'format' => self::FORMAT, 'log' => $this->logFile, 'sites' => (object)$this->sites() );
        $tmp = $file . '.' . getmypid() . '.tmp';
        $json = json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
        if ( @file_put_contents( $tmp, $json, LOCK_EX ) !== strlen( $json ) )
        {
            @unlink( $tmp );
            return false;
        }
        if ( !@rename( $tmp, $file ) )
        {
            @unlink( $tmp );
            return false;
        }
        return true;
    }
}
