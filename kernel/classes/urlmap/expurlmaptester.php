<?php
/**
 * File containing the expUrlMapTester class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Tests a URL map against a running site: requests each old address on a base address, follows the redirects hop by
 * hop, and compares where it lands with the row's new_url.
 *
 *   $tester = new expUrlMapTester( 'https://www.example.com', array( 'concurrency' => 8 ) );
 *   $result = $tester->test( expUrlMapFormats::readFile( 'map.csv' ) );
 *
 * The result of a row (state):
 * - landed: it redirected (an alias or a direct wildcard: answered without a redirect) and ended on the expected
 *   address with a 2xx answer, with a temporary status where the row asks for one (302, 303, 307) and a permanent
 *   one otherwise (301, 308);
 * - wrong-status: it landed, but with a permanent redirect where a temporary one was expected or the other way;
 * - wrong-target: it redirected elsewhere, or to the expected address but that does not answer 2xx;
 * - landed also: the old address is the target and answers 2xx (a page that kept its address);
 * - not-redirected: the old address answered without a redirect (a page, or 404);
 * - error: no answer, too many redirects, a redirect loop;
 * - skipped: nothing to test (no new_url, a row that cannot be imported).
 *
 * The old address is requested on the base's scheme and host whatever host the row names (a migration is tested on
 * the new site); a wildcard row is tested with a sample in place of its * (and {1} of its target). A target path is
 * the visitor's address on the base; a full URL is compared with scheme and host; node:<id> targets need the
 * resolver option (the command gives one: the node's address). Paths are compared decoded, without a trailing slash
 * and in any case; equivalent_prefixes lists path prefixes a landing may carry or lack (a siteaccess PathPrefix).
 *
 * The network is an expUrlMapHttp (a fake one in tests).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUrlMapTester
{
    const LANDED = 'landed';
    const WRONG_STATUS = 'wrong-status';
    const WRONG_TARGET = 'wrong-target';
    const NOT_REDIRECTED = 'not-redirected';
    const ERROR = 'error';
    const SKIPPED = 'skipped';

    /** What a * of a wildcard row is replaced with */
    const SAMPLE = 'exp-url-test';

    /** @var string scheme://host[:port] and an optional path, no trailing slash */
    protected $base;

    /** @var array */
    protected $options;

    /** @var expUrlMapHttp */
    protected $http;

    /**
     * @param string $base the site to test, https://www.example.com (a path is put before every old address)
     * @param array $options concurrency (default 8), max_redirects (default 5), resolver (callable( string $target ):
     *                       string|null, the address of node:<id> targets), equivalent_prefixes (string[]), timeout,
     *                       insecure (for an expUrlMapHttp made here)
     * @param expUrlMapHttp|null $http
     * @throws InvalidArgumentException when the base is not an http(s) address
     */
    public function __construct( $base, array $options = array(), $http = null )
    {
        $base = rtrim( trim( (string)$base ), '/' );
        if ( expUrlMapHttp::problem( $base ) !== null )
            throw new InvalidArgumentException( 'The base must be an http or https address, not ' . $base );
        $this->base = $base;
        $this->options = $options + array( 'concurrency' => 8, 'max_redirects' => 5, 'resolver' => null, 'equivalent_prefixes' => array(),
                                           'timeout' => 15, 'insecure' => false );
        $this->http = $http instanceof expUrlMapHttp ? $http
                    : new expUrlMapHttp( array( 'timeout' => (int)$this->options['timeout'], 'insecure' => !empty( $this->options['insecure'] ),
                                                'with_body' => false ) );
    }

    /** @return string[] every state */
    public static function states()
    {
        return array( self::LANDED, self::WRONG_STATUS, self::WRONG_TARGET, self::NOT_REDIRECTED, self::ERROR, self::SKIPPED );
    }

    /**
     * Tests every row.
     *
     * @param expUrlMapRow[] $rows
     * @return array results (per row: line, old_url, new_url, request, expected, state, chain (list of status and
     *               url), landed (the last address), status (the first answer's), message), counts (state => n,
     *               rows)
     */
    public function test( array $rows )
    {
        $results = array();
        $pending = array();
        foreach ( array_values( $rows ) as $i => $row )
        {
            $results[$i] = $this->prepare( $row );
            if ( $results[$i]['state'] === null )
                $pending[$i] = $results[$i]['request'];
        }
        // every row's next hop in one batch of concurrent requests, until each one has landed
        $hop = 0;
        while ( $pending )
        {
            $answers = $this->http->getMany( array_values( $pending ), (int)$this->options['concurrency'] );
            $next = array();
            foreach ( $pending as $i => $url )
            {
                $answer = $answers[$url];
                $results[$i]['chain'][] = array( 'status' => $answer['status'], 'url' => $url );
                if ( $answer['status'] === 0 )
                {
                    $results[$i] = self::finish( $results[$i], self::ERROR, $answer['error'] !== null ? $answer['error'] : 'no answer' );
                    continue;
                }
                if ( $answer['status'] >= 300 && $answer['status'] < 400 && $answer['location'] )
                {
                    $seen = array_column( $results[$i]['chain'], 'url' );
                    if ( in_array( $answer['location'], $seen, true ) )
                        $results[$i] = self::finish( $results[$i], self::ERROR, 'a redirect loop at ' . $answer['location'] );
                    elseif ( $hop >= (int)$this->options['max_redirects'] )
                        $results[$i] = self::finish( $results[$i], self::ERROR, 'more than ' . (int)$this->options['max_redirects'] . ' redirects' );
                    else
                        $next[$i] = $answer['location'];
                    continue;
                }
                $results[$i] = $this->judge( $results[$i], $url, $answer['status'] );
            }
            $pending = $next;
            $hop++;
        }
        $counts = array_fill_keys( self::states(), 0 ) + array( 'rows' => count( $results ) );
        foreach ( $results as $r )
            $counts[$r['state']]++;
        return array( 'results' => array_values( $results ), 'counts' => $counts );
    }

    /**
     * Whether two addresses are the same for the test: scheme and host when both have them, the path decoded, in any
     * case and without a trailing slash, the query in any order. Pure.
     *
     * @param string $landed
     * @param string $expected
     * @param string[] $equivalentPrefixes path prefixes the landing may carry or lack
     * @return bool
     */
    public static function sameAddress( $landed, $expected, array $equivalentPrefixes = array() )
    {
        $a = expUrlMapRow::parseUrl( (string)$landed );
        $b = expUrlMapRow::parseUrl( (string)$expected );
        if ( $a['host'] !== '' && $b['host'] !== '' && ( $a['host'] !== $b['host'] || ( $a['scheme'] !== '' && $b['scheme'] !== '' && $a['scheme'] !== $b['scheme'] ) ) )
            return false;
        if ( expUrlRedirect::canonicalQuery( $a['query'] ) !== expUrlRedirect::canonicalQuery( $b['query'] ) )
            return false;
        $pa = self::comparablePath( $a['path'] );
        $pb = self::comparablePath( $b['path'] );
        if ( $pa === $pb )
            return true;
        foreach ( $equivalentPrefixes as $prefix )
        {
            $prefix = self::comparablePath( $prefix );
            if ( $prefix === '/' )
                continue;
            if ( self::stripPrefix( $pa, $prefix ) === $pb || self::stripPrefix( $pb, $prefix ) === $pa )
                return true;
        }
        return false;
    }

    /**
     * The result of a row before any request: what is requested and expected, or why it is skipped.
     *
     * @param expUrlMapRow $row
     * @return array
     */
    protected function prepare( $row )
    {
        $result = array( 'line' => 0, 'old_url' => '', 'new_url' => '', 'request' => '', 'expected' => '', 'expect_redirect' => true, 'temporary' => false,
                         'state' => null, 'chain' => array(), 'landed' => '', 'status' => 0, 'message' => '' );
        if ( !$row instanceof expUrlMapRow )
            return self::finish( $result, self::SKIPPED, 'not a row' );
        $result['line'] = $row->line();
        $result['old_url'] = $row->oldUrl();
        $result['new_url'] = $row->newUrl();
        if ( $row->oldUrl() === '' || $row->newUrl() === '' )
            return self::finish( $result, self::SKIPPED, 'no new_url to compare with' );
        $kind = $row->effectiveKind();
        if ( !in_array( $kind, expUrlMapRow::kinds(), true ) || preg_match( '/^not importable/', implode( ' ', $row->problems() ) ) )
            return self::finish( $result, self::SKIPPED, 'the row cannot be imported: ' . implode( '; ', $row->problems() ) );
        $result['expect_redirect'] = !in_array( $kind, array( expUrlMapRow::KIND_ALIAS, expUrlMapRow::KIND_WILDCARD_DIRECT ), true );
        $result['temporary'] = $row->isTemporary();

        $path = $row->oldPath();
        $target = $row->newUrl();
        if ( $row->isWildcard() )
        {
            $path = str_replace( '*', self::SAMPLE, $path );
            $target = preg_replace( '/\{\d+\}/', self::SAMPLE, $target );
        }
        $result['request'] = $this->base . $path . ( $row->oldQuery() !== '' ? '?' . $row->oldQuery() : '' );
        if ( preg_match( '#^node:\d+$#', $target ) )
        {
            $resolved = is_callable( $this->options['resolver'] ) ? call_user_func( $this->options['resolver'], $target ) : null;
            if ( $resolved === null || $resolved === '' )
                return self::finish( $result, self::SKIPPED, 'the address of ' . $target . ' is not known' );
            $target = (string)$resolved;
        }
        $parts = expUrlMapRow::parseUrl( $target );
        $result['expected'] = $parts['host'] !== '' ? $target : $this->base . expUrlMapRow::buildUrl( array( 'scheme' => '', 'host' => '', 'path' => $parts['path'], 'query' => $parts['query'] ) );
        return $result;
    }

    /**
     * The state of a row that ended with a final (not redirecting) answer.
     *
     * @param array $result
     * @param string $url where it ended
     * @param int $status the final answer's status
     * @return array
     */
    protected function judge( array $result, $url, $status )
    {
        $result['landed'] = $url;
        $first = (int)$result['chain'][0]['status'];
        $result['status'] = $first;
        $redirected = count( $result['chain'] ) > 1;
        $prefixes = (array)$this->options['equivalent_prefixes'];
        if ( !$result['expect_redirect'] )
        {
            if ( $redirected && !self::sameAddress( $url, $result['expected'], $prefixes ) )
                return self::finish( $result, self::WRONG_TARGET, 'redirected to ' . $url . ', expected an answer at the old address' );
            if ( $status < 200 || $status >= 300 )
                return self::finish( $result, self::WRONG_TARGET, 'answered ' . $status );
            return self::finish( $result, self::LANDED, 'answered ' . $status . ( $redirected ? ' after a redirect' : ' at the old address' ) );
        }
        if ( !$redirected && $status >= 200 && $status < 300 && self::sameAddress( $url, $result['expected'], $prefixes ) )
            return self::finish( $result, self::LANDED, 'answered ' . $status . ' at its target: the old address is the new one' );
        if ( !$redirected )
            return self::finish( $result, self::NOT_REDIRECTED, 'answered ' . $status . ' without a redirect' );
        if ( !self::sameAddress( $url, $result['expected'], $prefixes ) )
            return self::finish( $result, self::WRONG_TARGET, 'landed on ' . $url . ' (' . $status . '), expected ' . $result['expected'] );
        if ( $status < 200 || $status >= 300 )
            return self::finish( $result, self::WRONG_TARGET, 'the target answered ' . $status );
        $temporary = in_array( $first, expUrlMapRow::TEMPORARY_STATUSES, true );
        if ( $temporary !== $result['temporary'] )
            return self::finish( $result, self::WRONG_STATUS, 'redirected with ' . $first . ', expected a ' . ( $result['temporary'] ? 'temporary' : 'permanent' ) . ' redirect' );
        return self::finish( $result, self::LANDED, $first . ' to ' . $url );
    }

    /**
     * @param array $result
     * @param string $state
     * @param string $message
     * @return array
     */
    protected static function finish( array $result, $state, $message )
    {
        $result['state'] = $state;
        $result['message'] = $message;
        if ( $result['chain'] && !$result['status'] )
            $result['status'] = (int)$result['chain'][0]['status'];
        unset( $result['expect_redirect'] );
        return $result;
    }

    /**
     * @param string $path
     * @return string decoded, lower case, no trailing slash
     */
    protected static function comparablePath( $path )
    {
        $path = '/' . trim( rawurldecode( (string)$path ), '/' );
        return function_exists( 'mb_strtolower' ) ? mb_strtolower( $path, 'UTF-8' ) : strtolower( $path );
    }

    /**
     * @param string $path
     * @param string $prefix
     * @return string|null
     */
    protected static function stripPrefix( $path, $prefix )
    {
        if ( $path === $prefix )
            return '/';
        return strpos( $path, $prefix . '/' ) === 0 ? substr( $path, strlen( $prefix ) ) : null;
    }
}
