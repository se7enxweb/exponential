<?php
/**
 * The spool of a sink (doc/bc/6.0/audit.md, "Webhook"): records for network sinks are appended at flush time to
 * <SpoolDir>/<sink>.jsonl under flock(), so a web request never waits for a network; the audit cronjob part
 * (and exp:audit sinks flush) delivers them. A delivered batch is cut from the front of the file; what was not
 * delivered stays, so delivery is at least once (receivers de-duplicate by event id).
 *
 * The state of a sink's delivery (attempts, the next try, the last delivery, the last failure report) is kept
 * beside it in <sink>.state.json.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditSpool
{
    /** @var string */
    protected $dir;

    /** @var string */
    protected $sink;

    /**
     * @param string $dir the spool directory
     * @param string $sink the sink's name
     */
    public function __construct( $dir, $sink )
    {
        if ( !preg_match( '/^[a-z][a-z0-9_]{0,31}$/', $sink ) )
            throw new InvalidArgumentException( "Malformed sink name '$sink'" );
        $this->dir = rtrim( $dir, '/' );
        $this->sink = $sink;
    }

    /** @return string The spool file */
    public function path()
    {
        return $this->dir . '/' . $this->sink . '.jsonl';
    }

    /** @return string The state file */
    public function statePath()
    {
        return $this->dir . '/' . $this->sink . '.state.json';
    }

    /**
     * Appends records (one JSON line each).
     *
     * @param array[] $records
     * @return bool
     */
    public function append( array $records )
    {
        if ( !$records )
            return true;
        if ( !expAuditWriter::ensureDirectory( $this->dir ) )
            return false;
        $bytes = '';
        foreach ( $records as $r )
            $bytes .= expAuditJson::encode( $r ) . "\n";
        $path = $this->path();
        $created = !is_file( $path );
        $h = @fopen( $path, 'ab' );
        if ( !$h )
            return false;
        flock( $h, LOCK_EX );
        $n = fwrite( $h, $bytes );
        fflush( $h );
        flock( $h, LOCK_UN );
        fclose( $h );
        if ( $created )
            expAuditWriter::ownLikeParent( $path, 0640 );
        return $n === strlen( $bytes );
    }

    /** @return int Records waiting */
    public function count()
    {
        $path = $this->path();
        if ( !is_file( $path ) )
            return 0;
        $n = 0;
        $h = @fopen( $path, 'rb' );
        while ( $h && ( $line = fgets( $h ) ) !== false )
            if ( substr( $line, -1 ) === "\n" )
                $n++;
        if ( $h )
            fclose( $h );
        return $n;
    }

    /**
     * Runs $deliver over the spool in batches, under the spool's lock, and cuts what it delivered.
     *
     * @param int $batchSize
     * @param callable $deliver fn( array $records ): bool, true when the batch was delivered
     * @param int $maxBatches
     * @return array delivered (records), failed (bool), remaining (records)
     */
    public function drain( $batchSize, $deliver, $maxBatches = 1000 )
    {
        $path = $this->path();
        $result = array( 'delivered' => 0, 'failed' => false, 'remaining' => 0 );
        if ( !is_file( $path ) )
            return $result;
        $h = @fopen( $path, 'r+b' );
        if ( !$h )
            return array( 'delivered' => 0, 'failed' => true, 'remaining' => $this->count() );
        flock( $h, LOCK_EX );
        try
        {
            $lines = array();
            while ( ( $line = fgets( $h ) ) !== false )
            {
                if ( substr( $line, -1 ) === "\n" )
                    $lines[] = $line;
            }
            $done = 0;
            $batches = 0;
            while ( $done < count( $lines ) && $batches < $maxBatches )
            {
                $chunk = array_slice( $lines, $done, max( 1, (int)$batchSize ) );
                $records = array();
                foreach ( $chunk as $l )
                {
                    $r = json_decode( rtrim( $l, "\n" ), true );
                    if ( is_array( $r ) )
                        $records[] = $r;
                }
                $ok = $records ? (bool)call_user_func( $deliver, $records ) : true;
                if ( !$ok )
                {
                    $result['failed'] = true;
                    break;
                }
                $done += count( $chunk );
                $result['delivered'] += count( $records );
                $batches++;
            }
            if ( $done > 0 )
            {
                // cut the delivered lines: rewrite the rest in place under the lock
                $rest = implode( '', array_slice( $lines, $done ) );
                ftruncate( $h, 0 );
                rewind( $h );
                fwrite( $h, $rest );
                fflush( $h );
            }
            $result['remaining'] = count( $lines ) - $done;
        }
        finally
        {
            flock( $h, LOCK_UN );
            fclose( $h );
        }
        return $result;
    }

    /**
     * The first records without removing them.
     *
     * @param int $n
     * @return array[]
     */
    public function peek( $n )
    {
        $out = array();
        $h = @fopen( $this->path(), 'rb' );
        while ( $h && count( $out ) < $n && ( $line = fgets( $h ) ) !== false )
        {
            $r = json_decode( rtrim( $line, "\n" ), true );
            if ( is_array( $r ) )
                $out[] = $r;
        }
        if ( $h )
            fclose( $h );
        return $out;
    }

    /** @return array The delivery state */
    public function state()
    {
        $s = @file_get_contents( $this->statePath() );
        $s = $s !== false ? json_decode( $s, true ) : null;
        return is_array( $s ) ? $s : array();
    }

    /** @param array $state */
    public function saveState( array $state )
    {
        if ( !expAuditWriter::ensureDirectory( $this->dir ) )
            return;
        $path = $this->statePath();
        $created = !is_file( $path );
        $tmp = $path . '.' . getmypid() . '.tmp';
        if ( @file_put_contents( $tmp, json_encode( $state, JSON_UNESCAPED_SLASHES ) ) !== false )
            @rename( $tmp, $path );
        if ( $created )
            expAuditWriter::ownLikeParent( $path, 0640 );
    }
}
