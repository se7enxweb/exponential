<?php
/**
 * File containing the expUrlMap404Source class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The addresses visitors asked for and did not find, as URL map rows to fill in (old_url set, new_url empty), the
 * most asked first:
 *
 *   $source = new expUrlMap404Source( array( 'min_hits' => 3, 'since' => strtotime( '-30 days' ) ) );
 *   $rows = $source->rows( expUrlMap404Source::logFiles() );
 *
 * Reads the not-found log the kernel writes (site.ini [RequestLogSettings] NotFoundLog, var/log/404.log, one line
 * per 404 or 410 answer, written by expRequestLog) and its rotated files (404.log.1, 404.log.2.gz ...), and also
 * web server access logs in the common or combined format (Apache, nginx, Velocity), of which the 404 and 410 lines
 * count. Requests the request classifier named probes are in security.log, not here.
 *
 * Left out by default: assets (images, scripts, style sheets, fonts, files below /var/, /design/, /extension/,
 * /share/), requests of bots (a user agent that says bot, crawler, spider and the like; the not-found log keeps the
 * first 60 characters of it) and methods other than GET and HEAD. The not-found log writes every query value as
 * ***: such a query cannot be redirected and is dropped (the path counts); a query of an access log is kept.
 *
 * Options: min_hits (default 1), since (a timestamp; lines before it are skipped), until, include_assets,
 * include_bots, limit (the most rows, 0 all), siteaccess (only lines of this siteaccess, not-found log only).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUrlMap404Source
{
    /** File endings of assets */
    const ASSETS = '/\.(?:css|js|mjs|map|json|xml|txt|ico|png|jpe?g|gif|svg|webp|avif|bmp|tiff?|woff2?|ttf|otf|eot|mp[34]|webm|ogg|wav|pdf|zip|gz)$/i';

    /** Path starts of assets and system files */
    const ASSET_PATHS = '#^/(?:var|design|extension|share|static|assets|\.well-known)(?:/|$)#i';

    /** User agents of bots */
    const BOTS = '/bot\b|bot\/|crawl|spider|slurp|scan|curl\/|wget|python|java\/|go-http|libwww|httpclient|okhttp|headless|lighthouse|monitor|feedfetcher|facebookexternalhit|preview/i';

    /** @var array */
    protected $options;

    /** @var array lines, matched, skipped (assets, bots, methods, older, siteaccess, other), unreadable files */
    protected $counts = array();

    /** @var string[] */
    protected $warnings = array();

    /**
     * @param array $options see the class
     */
    public function __construct( array $options = array() )
    {
        $this->options = $options + array( 'min_hits' => 1, 'since' => null, 'until' => null, 'include_assets' => false, 'include_bots' => false,
                                           'limit' => 0, 'siteaccess' => '' );
    }

    /** @return array lines, not_found, assets, bots, methods, outside, siteaccess, unparsed, addresses */
    public function counts()
    {
        return $this->counts;
    }

    /** @return string[] */
    public function warnings()
    {
        return $this->warnings;
    }

    /**
     * The not-found log of this installation and its rotated files, oldest first.
     *
     * @param string|null $file the log (default expRequestLog::logFile())
     * @return string[]
     */
    public static function logFiles( $file = null )
    {
        $file = $file !== null ? (string)$file : ( class_exists( 'expRequestLog' ) ? expRequestLog::logFile() : 'var/log/404.log' );
        $out = array();
        for ( $i = 50; $i >= 1; $i-- )
        {
            foreach ( array( "$file.$i.gz", "$file.$i" ) as $rotated )
                if ( is_file( $rotated ) )
                    $out[] = $rotated;
        }
        if ( is_file( $file ) )
            $out[] = $file;
        return $out;
    }

    /**
     * Rows from log files.
     *
     * @param string[] $files plain or .gz
     * @return expUrlMapRow[]
     */
    public function rows( array $files )
    {
        $this->reset();
        $entries = array();
        foreach ( $files as $file )
        {
            $handle = substr( (string)$file, -3 ) === '.gz' ? @gzopen( $file, 'rb' ) : @fopen( $file, 'rb' );
            if ( !$handle )
            {
                $this->warnings[] = "the log $file cannot be read";
                continue;
            }
            $gz = substr( (string)$file, -3 ) === '.gz';
            while ( ( $line = $gz ? gzgets( $handle, 65536 ) : fgets( $handle, 65536 ) ) !== false )
                $this->addLine( $line, $entries );
            $gz ? gzclose( $handle ) : fclose( $handle );
        }
        return self::toRows( $this->finishEntries( $entries ) );
    }

    /**
     * The addresses of log lines, by hits. Pure apart from the counts.
     *
     * @param string[] $lines
     * @return array[] path (with the query an access log kept), hits, first, last (timestamps), siteaccesses,
     *                 referrers (the most frequent ones, at most 3)
     */
    public function aggregate( array $lines )
    {
        $this->reset();
        $entries = array();
        foreach ( $lines as $line )
            $this->addLine( $line, $entries );
        return $this->finishEntries( $entries );
    }

    /**
     * A line of the not-found log or an access log, or null for any other line. Pure.
     *
     * Not-found log: [ Nov 06 2026 01:38:33 ] [34.95.x.x] 404 GET /path?q=*** siteaccess=site reason=... node=- referrer=- ua=hash "agent"
     * Access log: 203.0.113.9 - - [06/Nov/2026:01:38:33 +0000] "GET /path?q=1 HTTP/1.1" 404 512 "referrer" "agent"
     *
     * @param string $line
     * @return array|null status, method, uri, time (or null), siteaccess, referrer, agent, source (notfound, access)
     */
    public static function parseLine( $line )
    {
        $line = rtrim( (string)$line, "\r\n" );
        if ( preg_match( '/^\[ ([A-Z][a-z]{2} \d{2} \d{4} \d{2}:\d{2}:\d{2}) \] \[[^\]]*\] (\d{3}|-) ([A-Z]{1,10}|-) (\S+)(.*)$/', $line, $m ) )
        {
            $rest = $m[5];
            $time = DateTime::createFromFormat( 'M d Y H:i:s', $m[1] );
            $agent = preg_match( '/ ua=\S+ "(.*)"\s*$/', $rest, $a ) ? $a[1] : '';
            return array( 'status' => $m[2] === '-' ? 0 : (int)$m[2], 'method' => $m[3], 'uri' => $m[4],
                          'time' => $time ? $time->getTimestamp() : null,
                          'siteaccess' => preg_match( '/ siteaccess=(\S*)/', $rest, $s ) ? $s[1] : '',
                          'referrer' => preg_match( '/ referrer=(\S+)/', $rest, $r ) && $r[1] !== '-' ? $r[1] : '',
                          'agent' => $agent, 'source' => 'notfound' );
        }
        if ( preg_match( '/^\S+ \S+ \S+ \[([^\]]+)\] "([A-Z]{1,10}) (\S+)(?: HTTP\/[\d.]+)?" (\d{3}) \S+(?: "((?:[^"\\\\]|\\\\.)*)" "((?:[^"\\\\]|\\\\.)*)")?/', $line, $m ) )
        {
            $time = DateTime::createFromFormat( 'd/M/Y:H:i:s O', $m[1] );
            return array( 'status' => (int)$m[4], 'method' => $m[2], 'uri' => $m[3], 'time' => $time ? $time->getTimestamp() : null,
                          'siteaccess' => '', 'referrer' => isset( $m[5] ) && $m[5] !== '-' ? $m[5] : '', 'agent' => isset( $m[6] ) ? stripcslashes( $m[6] ) : '',
                          'source' => 'access' );
        }
        return null;
    }

    /**
     * Whether an address is an asset or a system file. Pure.
     *
     * @param string $path
     * @return bool
     */
    public static function isAsset( $path )
    {
        $path = (string)parse_url( (string)$path, PHP_URL_PATH );
        return (bool)preg_match( self::ASSETS, $path ) || (bool)preg_match( self::ASSET_PATHS, $path );
    }

    /**
     * Whether a user agent is a bot's (or empty). Pure.
     *
     * @param string $agent
     * @return bool
     */
    public static function isBot( $agent )
    {
        $agent = trim( (string)$agent );
        return $agent === '' || (bool)preg_match( self::BOTS, $agent );
    }

    /**
     * Rows of aggregated addresses: old_url the address, new_url empty, the note "404: 37 hits, last 2026-11-05".
     *
     * @param array[] $entries as aggregate() gives them
     * @return expUrlMapRow[]
     */
    public static function toRows( array $entries )
    {
        $rows = array();
        foreach ( $entries as $entry )
        {
            $note = '404: ' . $entry['hits'] . ( $entry['hits'] === 1 ? ' hit' : ' hits' )
                  . ( $entry['last'] ? ', last ' . gmdate( 'Y-m-d', $entry['last'] ) : '' )
                  . ( $entry['siteaccesses'] ? ', siteaccess ' . implode( ' ', $entry['siteaccesses'] ) : '' );
            $rows[] = new expUrlMapRow( $entry['path'], '', '', '', '', $note );
        }
        return $rows;
    }

    protected function reset()
    {
        $this->counts = array( 'lines' => 0, 'not_found' => 0, 'assets' => 0, 'bots' => 0, 'methods' => 0, 'outside' => 0, 'siteaccess' => 0,
                               'unparsed' => 0, 'addresses' => 0 );
        $this->warnings = array();
    }

    /**
     * @param string $line
     * @param array $entries
     */
    protected function addLine( $line, array &$entries )
    {
        if ( trim( (string)$line ) === '' )
            return;
        $this->counts['lines']++;
        $parsed = self::parseLine( $line );
        if ( $parsed === null )
        {
            $this->counts['unparsed']++;
            return;
        }
        if ( $parsed['status'] !== 404 && $parsed['status'] !== 410 )
            return;
        $this->counts['not_found']++;
        if ( $parsed['method'] !== 'GET' && $parsed['method'] !== 'HEAD' )
        {
            $this->counts['methods']++;
            return;
        }
        if ( $parsed['time'] !== null && ( ( $this->options['since'] !== null && $parsed['time'] < (int)$this->options['since'] )
                                           || ( $this->options['until'] !== null && $parsed['time'] > (int)$this->options['until'] ) ) )
        {
            $this->counts['outside']++;
            return;
        }
        if ( (string)$this->options['siteaccess'] !== '' && $parsed['source'] === 'notfound' && $parsed['siteaccess'] !== (string)$this->options['siteaccess'] )
        {
            $this->counts['siteaccess']++;
            return;
        }
        $uri = $parsed['uri'];
        $q = strpos( $uri, '?' );
        $path = $q === false ? $uri : substr( $uri, 0, $q );
        $query = $q === false ? '' : (string)substr( $uri, $q + 1 );
        // the not-found log hides every query value: such a query cannot be matched again
        if ( strpos( $query, '***' ) !== false )
            $query = '';
        if ( empty( $this->options['include_assets'] ) && self::isAsset( $path ) )
        {
            $this->counts['assets']++;
            return;
        }
        if ( empty( $this->options['include_bots'] ) && self::isBot( $parsed['agent'] ) )
        {
            $this->counts['bots']++;
            return;
        }
        $parts = expUrlMapRow::parseUrl( $path . ( $query !== '' ? '?' . $query : '' ) );
        if ( $parts['problems'] || substr( $parts['path'], 0, 1 ) !== '/' )
        {
            $this->counts['unparsed']++;
            return;
        }
        $url = expUrlMapRow::buildUrl( array( 'scheme' => '', 'host' => '', 'path' => $parts['path'], 'query' => $parts['query'] ) );
        $key = strtolower( $url );
        if ( !isset( $entries[$key] ) )
            $entries[$key] = array( 'path' => $url, 'hits' => 0, 'first' => null, 'last' => null, 'siteaccesses' => array(), 'referrers' => array() );
        $e =& $entries[$key];
        $e['hits']++;
        if ( $parsed['time'] !== null )
        {
            $e['first'] = $e['first'] === null ? $parsed['time'] : min( $e['first'], $parsed['time'] );
            $e['last'] = $e['last'] === null ? $parsed['time'] : max( $e['last'], $parsed['time'] );
        }
        if ( $parsed['siteaccess'] !== '' )
            $e['siteaccesses'][$parsed['siteaccess']] = true;
        if ( $parsed['referrer'] !== '' && count( $e['referrers'] ) < 50 )
            $e['referrers'][$parsed['referrer']] = ( isset( $e['referrers'][$parsed['referrer']] ) ? $e['referrers'][$parsed['referrer']] : 0 ) + 1;
        unset( $e );
    }

    /**
     * @param array $entries
     * @return array[] sorted by hits, then by the latest hit, then by address
     */
    protected function finishEntries( array $entries )
    {
        $min = max( 1, (int)$this->options['min_hits'] );
        $out = array();
        foreach ( $entries as $e )
        {
            if ( $e['hits'] < $min )
                continue;
            ksort( $e['siteaccesses'] );
            $e['siteaccesses'] = array_keys( $e['siteaccesses'] );
            arsort( $e['referrers'] );
            $e['referrers'] = array_slice( array_keys( $e['referrers'] ), 0, 3 );
            $out[] = $e;
        }
        usort( $out, function ( $a, $b )
        {
            return $b['hits'] - $a['hits'] ?: (int)$b['last'] - (int)$a['last'] ?: strcmp( $a['path'], $b['path'] );
        } );
        if ( (int)$this->options['limit'] > 0 )
            $out = array_slice( $out, 0, (int)$this->options['limit'] );
        $this->counts['addresses'] = count( $out );
        return $out;
    }
}
