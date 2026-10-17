<?php
/**
 * The line rate of the navigator: how many lines a minute each log file gains (doc/features/6.0/log-viewer.md,
 * "The navigator").
 *
 * The navigator lists sizes and times from stat() anyway; the rate is the growth in bytes between two of those
 * looks, at least MinInterval seconds apart, divided by the file's average line length (measured once from its
 * last 16 KB and kept). The samples are kept in one small file in <cache dir>/logviewer, replaced (temporary file
 * and rename) at most every MinInterval seconds, never locked. A file that shrank (rotated or emptied) starts again.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLogFileStats
{
    /** Seconds between two samples */
    const MIN_INTERVAL = 30;

    /** The bytes read to measure the average line length */
    const SAMPLE_BYTES = 16384;

    /** @var string|null */
    protected $file;

    /**
     * @param string|null $file the samples file; null: <cache dir>/logviewer/stats.json
     */
    public function __construct( $file = null )
    {
        if ( $file === null && class_exists( 'eZSys' ) )
            $file = eZSys::cacheDirectory() . '/logviewer/stats.json';
        $this->file = $file;
    }

    /**
     * Adds the rate to every file of a listing (rate: lines per minute, or null when not known yet), and takes a
     * new sample when the last one is old enough.
     *
     * @param array $groups expLogFileLocator::listFiles()
     * @param expLogFileLocator $locator
     * @param int|null $now
     * @return array the groups with rate set
     */
    public function withRates( array $groups, expLogFileLocator $locator, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $samples = $this->load();
        $changed = false;
        $root = $locator->config()->root();
        foreach ( $groups as $group => &$list )
        {
            foreach ( $list as &$info )
            {
                $id = $info['id'];
                $info['rate'] = null;
                if ( !empty( $info['archive'] ) )
                {
                    $info['rate'] = 0;
                    continue;
                }
                $s = isset( $samples[$id] ) ? $samples[$id] : null;
                if ( $s !== null && (int)$info['size'] >= (int)$s['size'] && $now > (int)$s['time'] )
                {
                    $avg = isset( $s['avg'] ) && $s['avg'] > 0 ? (float)$s['avg'] : 120.0;
                    $bytesPerSecond = ( (int)$info['size'] - (int)$s['size'] ) / max( 1, $now - (int)$s['time'] );
                    if ( isset( $s['rate'] ) && $now - (int)$s['time'] < self::MIN_INTERVAL )
                        $info['rate'] = (float)$s['rate'];
                    else
                        $info['rate'] = round( $bytesPerSecond * 60 / $avg, 1 );
                }
                if ( $s === null || $now - (int)$s['time'] >= self::MIN_INTERVAL || (int)$info['size'] < (int)$s['size'] )
                {
                    $avg = $s !== null && isset( $s['avg'] ) && (int)$info['size'] >= (int)$s['size'] ? (float)$s['avg'] : self::averageLineLength( $root . '/' . $id, (int)$info['size'] );
                    $samples[$id] = array( 'size' => (int)$info['size'], 'time' => $now, 'avg' => $avg, 'rate' => $info['rate'] );
                    $changed = true;
                }
            }
            unset( $info );
        }
        unset( $list );
        if ( $changed )
            $this->save( $samples );
        return $groups;
    }

    /**
     * The average length of a line in the last SAMPLE_BYTES of a file.
     *
     * @param string $path
     * @param int $size
     * @return float
     */
    public static function averageLineLength( $path, $size )
    {
        if ( $size <= 0 )
            return 120.0;
        $handle = @fopen( $path, 'rb' );
        if ( $handle === false )
            return 120.0;
        $start = max( 0, $size - self::SAMPLE_BYTES );
        if ( $start > 0 )
            fseek( $handle, $start );
        $data = (string)fread( $handle, self::SAMPLE_BYTES );
        fclose( $handle );
        $lines = substr_count( $data, "\n" );
        return $lines > 0 ? max( 1.0, round( strlen( $data ) / $lines, 1 ) ) : 120.0;
    }

    /** @return array */
    protected function load()
    {
        if ( $this->file === null )
            return array();
        $raw = @file_get_contents( $this->file );
        $data = $raw !== false ? json_decode( $raw, true ) : null;
        return is_array( $data ) ? $data : array();
    }

    /** @param array $samples */
    protected function save( array $samples )
    {
        if ( $this->file === null )
            return;
        $dir = dirname( $this->file );
        if ( !is_dir( $dir ) && !@mkdir( $dir, eZDir::dirMode( 0770 ), true ) && !is_dir( $dir ) )
            return;
        $tmp = $this->file . '.' . getmypid() . '.' . mt_rand() . '.tmp';
        if ( @file_put_contents( $tmp, json_encode( $samples ) ) === false )
            return;
        @chmod( $tmp, eZFile::fileMode( 0660 ) );
        if ( !@rename( $tmp, $this->file ) )
            @unlink( $tmp );
    }
}
