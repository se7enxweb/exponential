<?php
/**
 * File containing the expHelpCommitFeed class.
 *
 * The latest commits of the Exponential repository for the developers' landing page of the help
 * (exphelp/audience/developer, doc/features/6.0/help-system.md "Landing pages"). The server reads the repository's
 * Atom feed ([CommitFeedSettings] FeedURL, by default https://github.com/se7enxweb/exponential/commits/main.atom),
 * never the browser: the admin's Content Security Policy allows no outside requests, and the visitor's address is not
 * handed to GitHub.
 *
 * - One read per CacheTTL seconds (default 600) for the whole installation, with a timeout (Timeout, default 4 s);
 *   requests in between use the cache, and only one process reads at a time (a lock), the others use what is cached.
 * - The feed holds about the 20 newest commits. Each read is merged into the cached list (by commit hash, newest
 *   first, at most MaxCommits), so the list that the commit log pages through grows beyond one feed.
 * - Offline or failing: the cached commits stay in use, marked stale with the time of the last good read; a failed
 *   read is retried after RetryAfter seconds (default 120), not on every request. Without any cache the page says
 *   the commits could not be read and links the commit history on GitHub.
 * - Everything from the feed is data: titles, messages and author names are plain text (tags removed, entities
 *   decoded, control characters dropped, lengths bounded) for the templates to escape; links are built from the
 *   40-character hash and CommitBaseURL, never taken from the feed. No avatars or other remote images are kept.
 *
 * Plain PHP apart from the settings (expHelp::setting(), with defaults), so the unit tests run it without a database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpCommitFeed
{
    const CACHE_FILE = 'commits.php';
    const LOCK_FILE = 'commits.lock';
    const DEFAULT_FEED = 'https://github.com/se7enxweb/exponential/commits/main.atom';
    const DEFAULT_COMMIT_BASE = 'https://github.com/se7enxweb/exponential/commit/';
    const DEFAULT_HISTORY = 'https://github.com/se7enxweb/exponential/commits/main';
    const MAX_TITLE = 300;
    const MAX_MESSAGE = 2000;
    const MAX_AUTHOR = 100;

    /** @var array settings: feed, commitBase, history, ttl, timeout, retry, max, cacheDir, enabled */
    protected $options;

    /** @var callable|null fetches a URL: function( $url, $timeout ) returning the body or false (tests) */
    protected $fetcher;

    /**
     * @param array $options overrides of the settings (tests): feed, commitBase, history, ttl, timeout, retry, max,
     *                       cacheDir, enabled
     * @param callable|null $fetcher function( string $url, int $timeout ): string|false
     */
    public function __construct( array $options = array(), $fetcher = null )
    {
        $this->options = $options + self::settings();
        $this->fetcher = $fetcher;
    }

    /**
     * The settings of [CommitFeedSettings], checked: an address that is not https is replaced by the default.
     *
     * @return array
     */
    public static function settings()
    {
        $get = function ( $name, $default ) {
            return class_exists( 'expHelp' ) && class_exists( 'eZINI' ) ? expHelp::setting( 'CommitFeedSettings', $name, $default ) : $default;
        };
        $https = function ( $value, $default ) {
            return is_string( $value ) && preg_match( '#^https://[a-z0-9.-]+(?::[0-9]+)?/[^\s"\'<>]*$#i', $value ) ? $value : $default;
        };
        $cacheDir = class_exists( 'expHelp' ) && class_exists( 'eZSys' ) ? expHelp::cacheDirectory() : null;
        return array(
            'enabled'    => $get( 'Enabled', 'enabled' ) === 'enabled',
            'feed'       => $https( $get( 'FeedURL', self::DEFAULT_FEED ), self::DEFAULT_FEED ),
            'commitBase' => $https( $get( 'CommitBaseURL', self::DEFAULT_COMMIT_BASE ), self::DEFAULT_COMMIT_BASE ),
            'history'    => $https( $get( 'HistoryURL', self::DEFAULT_HISTORY ), self::DEFAULT_HISTORY ),
            'ttl'        => max( 60, (int)$get( 'CacheTTL', 600 ) ),
            'timeout'    => min( 15, max( 1, (int)$get( 'Timeout', 4 ) ) ),
            'retry'      => max( 30, (int)$get( 'RetryAfter', 120 ) ),
            'max'        => min( 2000, max( 20, (int)$get( 'MaxCommits', 500 ) ) ),
            'pageSize'   => min( 50, max( 5, (int)$get( 'PageSize', 10 ) ) ),
            'cacheDir'   => $cacheDir,
        );
    }

    /** @return array the effective settings */
    public function options()
    {
        return $this->options;
    }

    /**
     * The commits, newest first, reading the feed when the cache is older than CacheTTL.
     *
     * @param int|null $now
     * @return array array( 'commits' => list of array( hash, short, title, message, author, time, url ),
     *               'fetched' => time of the last good read (0: never), 'stale' => bool (the last read failed or is
     *               older than CacheTTL), 'error' => '' or why the last read failed, 'history' => URL of the history )
     */
    public function commits( $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $cache = $this->readCache();
        if ( $this->options['enabled'] && $this->due( $cache, $now ) && $this->lock() )
        {
            try
            {
                $cache = $this->refresh( $cache, $now );
            }
            finally
            {
                $this->unlock();
            }
        }
        return array(
            'commits' => $cache['commits'],
            'fetched' => $cache['fetched'],
            'stale'   => $cache['error'] !== '' || $cache['fetched'] < $now - $this->options['ttl'],
            'error'   => $cache['error'],
            'history' => $this->options['history'],
        );
    }

    /**
     * One page of the commits with the paging numbers (the maths of the git_manager commit log, GitManagerLog::paging(),
     * which the kernel cannot depend on).
     *
     * @param int $offset
     * @param int|null $limit
     * @param bool $merges false: merge commits ("Merge ...") are left out
     * @param int|null $now
     * @return array the result of commits() with 'commits' cut to the page, plus 'paging' and 'total'
     */
    public function page( $offset, $limit = null, $merges = false, $now = null )
    {
        $result = $this->commits( $now );
        $all = $merges ? $result['commits'] : array_values( array_filter( $result['commits'], function ( $c ) {
            return !self::isMerge( $c['title'] );
        } ) );
        $limit = $limit === null ? $this->options['pageSize'] : min( 50, max( 1, (int)$limit ) );
        $paging = self::paging( count( $all ), $offset, $limit );
        $result['commits'] = array_slice( $all, $paging['offset'], $paging['limit'] );
        $result['paging'] = $paging;
        $result['total'] = count( $all );
        return $result;
    }

    /**
     * The paging numbers for $total items shown $limit at a time from $offset.
     *
     * @return array offset, limit, total, page, page_count, from, to, previous_offset, next_offset (false: none)
     */
    public static function paging( $total, $offset, $limit )
    {
        $total = max( 0, (int)$total );
        $limit = max( 1, (int)$limit );
        $pageCount = max( 1, (int)ceil( $total / $limit ) );
        $page = min( $pageCount, intdiv( max( 0, (int)$offset ), $limit ) + 1 );
        $offset = ( $page - 1 ) * $limit;
        return array(
            'offset' => $offset, 'limit' => $limit, 'total' => $total, 'page' => $page, 'page_count' => $pageCount,
            'from' => $total > 0 ? $offset + 1 : 0, 'to' => min( $total, $offset + $limit ),
            'previous_offset' => $page > 1 ? $offset - $limit : false,
            'next_offset' => $page < $pageCount ? $offset + $limit : false,
        );
    }

    /** Whether a commit title is a merge commit's */
    public static function isMerge( $title )
    {
        return (bool)preg_match( '/^Merge (remote-tracking )?(branch|pull request|tag|commit)\b/i', (string)$title );
    }

    /**
     * Whether the feed is to be read now: never read, older than CacheTTL, and no failed read in the last RetryAfter
     * seconds.
     */
    protected function due( array $cache, $now )
    {
        if ( $cache['attempted'] > $now - $this->options['retry'] && $cache['error'] !== '' )
            return false;
        return $cache['fetched'] < $now - $this->options['ttl'];
    }

    /**
     * Reads the feed and merges it into the cache, which is stored; a failure keeps the commits and notes the error.
     *
     * @return array the new cache
     */
    protected function refresh( array $cache, $now )
    {
        $cache['attempted'] = $now;
        $body = $this->fetch( $this->options['feed'], $this->options['timeout'] );
        $commits = is_string( $body ) && $body !== '' ? $this->parse( $body ) : null;
        if ( $commits === null )
        {
            $cache['error'] = is_string( $body ) && $body !== '' ? 'unreadable' : 'unreachable';
        }
        else
        {
            $cache['commits'] = self::merge( $commits, $cache['commits'], $this->options['max'] );
            $cache['fetched'] = $now;
            $cache['error'] = '';
        }
        $this->writeCache( $cache );
        return $cache;
    }

    /**
     * The newer list first, then the older commits it does not have; newest first by time, at most $max.
     *
     * @return array
     */
    public static function merge( array $newer, array $older, $max )
    {
        $byHash = array();
        foreach ( array_merge( $newer, $older ) as $commit )
            if ( isset( $commit['hash'] ) && !isset( $byHash[$commit['hash']] ) )
                $byHash[$commit['hash']] = $commit;
        $list = array_values( $byHash );
        usort( $list, function ( $a, $b ) { return $b['time'] <=> $a['time']; } );
        return array_slice( $list, 0, max( 1, (int)$max ) );
    }

    /**
     * The commits of an Atom feed, or null when it is not one. No network access while parsing (LIBXML_NONET), no
     * entities expanded from outside.
     *
     * @param string $xml
     * @return array|null list of array( hash, short, title, message, author, time, url )
     */
    public function parse( $xml )
    {
        if ( !is_string( $xml ) || strlen( $xml ) > 2097152 || stripos( $xml, '<!DOCTYPE' ) !== false )
            return null;
        $previous = libxml_use_internal_errors( true );
        $doc = new DOMDocument();
        $ok = $doc->loadXML( $xml, LIBXML_NONET | LIBXML_NOCDATA );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        if ( !$ok || !$doc->documentElement || $doc->documentElement->localName !== 'feed' )
            return null;
        $xpath = new DOMXPath( $doc );
        $xpath->registerNamespace( 'a', 'http://www.w3.org/2005/Atom' );
        $commits = array();
        foreach ( $xpath->query( '/a:feed/a:entry' ) as $entry )
        {
            $id = trim( (string)$xpath->evaluate( 'string(a:id)', $entry ) );
            $link = trim( (string)$xpath->evaluate( 'string(a:link[@rel="alternate"]/@href)', $entry ) );
            $hash = '';
            if ( preg_match( '/([0-9a-f]{40})\s*$/', $id, $m ) || preg_match( '#/commit/([0-9a-f]{40})\b#', $link, $m ) )
                $hash = $m[1];
            if ( $hash === '' )
                continue;
            $title = self::plain( (string)$xpath->evaluate( 'string(a:title)', $entry ), self::MAX_TITLE, true );
            $message = self::plain( html_entity_decode( strip_tags( (string)$xpath->evaluate( 'string(a:content)', $entry ) ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ), self::MAX_MESSAGE, false );
            $time = strtotime( (string)$xpath->evaluate( 'string(a:updated)', $entry ) );
            $commits[] = array(
                'hash'    => $hash,
                'short'   => substr( $hash, 0, 10 ),
                'title'   => $title !== '' ? $title : substr( $hash, 0, 10 ),
                'message' => $message,
                'author'  => self::plain( (string)$xpath->evaluate( 'string(a:author/a:name)', $entry ), self::MAX_AUTHOR, true ),
                'time'    => $time === false ? 0 : (int)$time,
                'url'     => $this->options['commitBase'] . $hash,
            );
        }
        return $commits;
    }

    /**
     * Plain text: entities decoded, tags removed, control characters dropped, invalid UTF-8 dropped, trimmed and cut
     * at $max characters; $oneLine: white space collapsed to single spaces.
     */
    public static function plain( $text, $max, $oneLine )
    {
        $text = html_entity_decode( strip_tags( (string)$text ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        if ( !mb_check_encoding( $text, 'UTF-8' ) )
            $text = mb_convert_encoding( $text, 'UTF-8', 'UTF-8' );
        $text = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text );
        $text = $oneLine ? preg_replace( '/\s+/u', ' ', $text ) : preg_replace( "/\r\n?/", "\n", $text );
        $text = trim( (string)$text );
        return mb_strlen( $text ) > $max ? rtrim( mb_substr( $text, 0, $max - 1 ) ) . '…' : $text;
    }

    /**
     * Reads a URL with a timeout: the test fetcher, else curl, else a stream with an https context. No cookies, no
     * credentials, redirects only to https.
     *
     * @return string|false
     */
    protected function fetch( $url, $timeout )
    {
        if ( $this->fetcher !== null )
            return call_user_func( $this->fetcher, $url, $timeout );
        $agent = 'Exponential help (+https://exponential.earth/)';
        if ( function_exists( 'curl_init' ) )
        {
            $ch = curl_init( $url );
            curl_setopt_array( $ch, array(
                CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_MAXREDIRS => 3,
                CURLOPT_PROTOCOLS => CURLPROTO_HTTPS, CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
                CURLOPT_CONNECTTIMEOUT => $timeout, CURLOPT_TIMEOUT => $timeout, CURLOPT_USERAGENT => $agent,
                CURLOPT_HTTPHEADER => array( 'Accept: application/atom+xml' ), CURLOPT_SSL_VERIFYPEER => true,
            ) );
            $body = curl_exec( $ch );
            $status = (int)curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
            return is_string( $body ) && $status === 200 ? $body : false;
        }
        $context = stream_context_create( array( 'http' => array( 'timeout' => $timeout, 'user_agent' => $agent,
            'header' => "Accept: application/atom+xml\r\n", 'ignore_errors' => false ) ) );
        $body = @file_get_contents( $url, false, $context, 0, 2097152 );
        return is_string( $body ) ? $body : false;
    }

    /** @return array the cached state: commits, fetched, attempted, error */
    protected function readCache()
    {
        $empty = array( 'commits' => array(), 'fetched' => 0, 'attempted' => 0, 'error' => '' );
        $file = $this->cacheFile();
        if ( $file === null || !is_file( $file ) )
            return $empty;
        $data = @include $file;
        if ( !is_array( $data ) || !isset( $data['commits'] ) || !is_array( $data['commits'] ) )
            return $empty;
        return array_merge( $empty, array_intersect_key( $data, $empty ) );
    }

    /** Stores the cache atomically (a temporary file renamed) */
    protected function writeCache( array $cache )
    {
        $file = $this->cacheFile();
        if ( $file === null )
            return false;
        $dir = dirname( $file );
        if ( !is_dir( $dir ) && !self::makeDir( $dir ) )
            return false;
        $temporary = $file . '.' . getmypid() . '.' . mt_rand() . '.tmp';
        $content = "<?php\n// The latest commits for the help (expHelpCommitFeed). Generated, safe to remove.\nreturn " . var_export( $cache, true ) . ";\n";
        if ( @file_put_contents( $temporary, $content, LOCK_EX ) === false )
            return false;
        @chmod( $temporary, eZFile::fileMode( 0666 ) );
        if ( !@rename( $temporary, $file ) )
        {
            @unlink( $temporary );
            return false;
        }
        if ( function_exists( 'opcache_invalidate' ) )
            @opcache_invalidate( $file, true );
        return true;
    }

    /** @var resource|null */
    protected $lockHandle;

    /** Takes the read lock without waiting; false when another process holds it */
    protected function lock()
    {
        $dir = $this->options['cacheDir'];
        if ( $dir === null )
            return $this->fetcher !== null;
        if ( !is_dir( $dir ) && !self::makeDir( $dir ) )
            return false;
        $handle = @fopen( $dir . '/' . self::LOCK_FILE, 'c' );
        if ( !$handle )
            return false;
        if ( !flock( $handle, LOCK_EX | LOCK_NB ) )
        {
            fclose( $handle );
            return false;
        }
        $this->lockHandle = $handle;
        return true;
    }

    protected function unlock()
    {
        if ( $this->lockHandle )
        {
            flock( $this->lockHandle, LOCK_UN );
            fclose( $this->lockHandle );
            $this->lockHandle = null;
        }
    }

    /** Creates a directory with the installation's directory mode (eZDir::dirMode()) */
    protected static function makeDir( $dir )
    {
        return (bool)eZDir::mkdir( $dir, false, true );
    }

    /** @return string|null */
    protected function cacheFile()
    {
        return $this->options['cacheDir'] === null ? null : $this->options['cacheDir'] . '/' . self::CACHE_FILE;
    }
}
