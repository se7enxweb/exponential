<?php
/**
 * The request limit of the log viewer's feed, per session ([RateLimitSettings], doc/features/6.0/log-viewer.md,
 * "Cost"), and the memory of which files a session was recorded reading (system.log.read, once per ReadInterval).
 *
 * One small file per session in <cache dir>/logviewer/rate (named by a hash of the session id, never the id
 * itself): a fixed window counter. It is read and replaced (temporary file and rename) without a lock; two polls
 * of one session at the same moment may both pass, which a limit of this kind can afford. Nothing is written to
 * the session, so a poll costs no session write.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLogRateLimit
{
    /** @var string */
    protected $dir;

    /** @var string */
    protected $key;

    /** @var int */
    protected $limit;

    /** @var int */
    protected $window;

    /** @var array|null the state as read */
    protected $state = null;

    /**
     * @param string $dir the directory of the state files
     * @param string $sessionID the session the limit is for
     * @param int $limit requests per window
     * @param int $window seconds
     */
    public function __construct( $dir, $sessionID, $limit, $window )
    {
        $this->dir = rtrim( (string)$dir, '/' );
        $this->key = hash( 'sha256', 'logviewer|' . (string)$sessionID );
        $this->limit = max( 1, (int)$limit );
        $this->window = max( 1, (int)$window );
    }

    /**
     * The limit of this request's session.
     *
     * @param expLogViewerConfig $config
     * @return expLogRateLimit
     */
    public static function forSession( expLogViewerConfig $config )
    {
        $sid = session_id();
        if ( $sid === '' && class_exists( 'eZSession' ) && method_exists( 'eZSession', 'userID' ) )
            $sid = 'user-' . eZSession::userID();
        $dir = ( class_exists( 'eZSys' ) ? eZSys::cacheDirectory() : sys_get_temp_dir() ) . '/logviewer/rate';
        return new self( $dir, $sid, $config->int( 'RateLimitSettings', 'Requests' ), $config->int( 'RateLimitSettings', 'Window' ) );
    }

    /** @return string */
    protected function file()
    {
        return $this->dir . '/' . $this->key . '.json';
    }

    /** @return array */
    protected function load()
    {
        if ( $this->state !== null )
            return $this->state;
        $raw = @file_get_contents( $this->file() );
        $state = $raw !== false ? json_decode( $raw, true ) : null;
        if ( !is_array( $state ) )
            $state = array();
        $state += array( 'start' => 0, 'count' => 0, 'read' => array() );
        if ( !is_array( $state['read'] ) )
            $state['read'] = array();
        return $this->state = $state;
    }

    /** Writes the state (temporary file and rename, no lock) */
    protected function save()
    {
        if ( !is_dir( $this->dir ) && !@mkdir( $this->dir, eZDir::dirMode( 0770 ), true ) && !is_dir( $this->dir ) )
            return;
        $tmp = $this->file() . '.' . getmypid() . '.' . mt_rand() . '.tmp';
        if ( @file_put_contents( $tmp, json_encode( $this->state ) ) === false )
            return;
        @chmod( $tmp, eZFile::fileMode( 0660 ) );
        if ( !@rename( $tmp, $this->file() ) )
            @unlink( $tmp );
    }

    /**
     * Counts one request.
     *
     * @param int|null $now
     * @return int 0 when the request may go on, else the seconds until the window ends (Retry-After)
     */
    public function hit( $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $state = $this->load();
        if ( $now - (int)$state['start'] >= $this->window || $now < (int)$state['start'] )
        {
            $state['start'] = $now;
            $state['count'] = 0;
        }
        if ( (int)$state['count'] >= $this->limit )
        {
            $this->state = $state;
            return max( 1, (int)$state['start'] + $this->window - $now );
        }
        $state['count'] = (int)$state['count'] + 1;
        $this->state = $state;
        $this->save();
        return 0;
    }

    /**
     * Whether a read of a file is to be recorded now (not recorded for it in the last $interval seconds), and
     * remembers that it is.
     *
     * @param string $fileID
     * @param int $interval
     * @param int|null $now
     * @return bool
     */
    public function shouldRecordRead( $fileID, $interval, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $state = $this->load();
        $key = sha1( (string)$fileID );
        if ( isset( $state['read'][$key] ) && $now - (int)$state['read'][$key] < (int)$interval )
            return false;
        $state['read'][$key] = $now;
        // forget the oldest when a session opened very many files
        if ( count( $state['read'] ) > 200 )
        {
            asort( $state['read'] );
            $state['read'] = array_slice( $state['read'], -100, null, true );
        }
        $this->state = $state;
        $this->save();
        return true;
    }
}
