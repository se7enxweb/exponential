<?php
/**
 * File containing the expRequestThresholdCounter class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Counts events per key in fixed time windows (a minute by default), in small JSON files shared by every process
 * of the installation: the web server's PHP-FPM workers (one user) and Velocity's persistent workers (another user)
 * count into the same files. A file is locked (flock) while it is read and written, and created with the log file
 * permissions (site.ini [FileSettings] LogFilePermissions, limited by the installation's file mode limit) so the
 * other user can write it too.
 *
 * The keys are spread over 16 files by their first hex digit, so a burst of not-found answers does not queue on one
 * lock. A key should be a hash (expRequestClassifier::ipKey()), never a raw address. Entries of past windows are
 * dropped whenever a file is written; a file holds at most MAX_ENTRIES keys.
 *
 * APCu is not used: Velocity and PHP-FPM do not share it, so the two would count apart.
 *
 * @package kernel
 */
class expRequestThresholdCounter
{
    const MAX_ENTRIES = 2000;

    /** @var string */
    protected $dir;
    /** @var int */
    protected $window;

    /**
     * @param string $dir the directory of the counter files (created when missing)
     * @param int $window seconds of a window
     */
    public function __construct( $dir, $window = 60 )
    {
        $this->dir = rtrim( (string)$dir, '/' );
        $this->window = max( 1, (int)$window );
    }

    /**
     * The file a key is counted in.
     *
     * @param string $key
     * @return string
     */
    public function fileOf( $key )
    {
        $shard = preg_match( '/^[0-9a-f]/', (string)$key ) ? substr( (string)$key, 0, 1 ) : '0';
        return $this->dir . '/counter-' . $shard . '.json';
    }

    /**
     * Counts one event of $key and returns how many there were in the current window, this one included; 0 when
     * the counter cannot be written (a counter that fails never flags anybody).
     *
     * @param string $key
     * @param int|null $now
     * @return int
     */
    public function hit( $key, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $start = $now - ( $now % $this->window );
        $file = $this->fileOf( $key );
        if ( !is_dir( $this->dir ) )
        {
            @eZDir::mkdir( $this->dir, false, true );
        }
        $existed = is_file( $file );
        $fp = @fopen( $file, 'c+' );
        if ( !$fp )
            return 0;
        if ( !$existed )
            expRequestClassifier::chmodLike( $file );
        if ( !@flock( $fp, LOCK_EX ) )
        {
            fclose( $fp );
            return 0;
        }
        $state = json_decode( (string)stream_get_contents( $fp ), true );
        if ( !is_array( $state ) )
            $state = array();
        foreach ( $state as $k => $entry )
        {
            if ( !is_array( $entry ) || !isset( $entry[0], $entry[1] ) || (int)$entry[0] !== $start )
                unset( $state[$k] );
        }
        $count = isset( $state[$key] ) ? (int)$state[$key][1] + 1 : 1;
        $state[$key] = array( $start, $count );
        if ( count( $state ) > self::MAX_ENTRIES )
        {
            // the keys with the most events stay: those are the ones a threshold is about
            uasort( $state, function ( $a, $b ) { return $b[1] - $a[1]; } );
            $state = array_slice( $state, 0, self::MAX_ENTRIES, true );
            $state[$key] = array( $start, $count );
        }
        ftruncate( $fp, 0 );
        rewind( $fp );
        fwrite( $fp, json_encode( $state ) );
        fflush( $fp );
        flock( $fp, LOCK_UN );
        fclose( $fp );
        return $count;
    }

    /**
     * How many events $key has in the current window, without counting one.
     *
     * @param string $key
     * @param int|null $now
     * @return int
     */
    public function count( $key, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $start = $now - ( $now % $this->window );
        $file = $this->fileOf( $key );
        if ( !is_file( $file ) )
            return 0;
        $state = json_decode( (string)@file_get_contents( $file ), true );
        if ( !is_array( $state ) || !isset( $state[$key][0], $state[$key][1] ) || (int)$state[$key][0] !== $start )
            return 0;
        return (int)$state[$key][1];
    }
}
