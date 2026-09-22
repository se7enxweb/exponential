<?php
/**
 * File containing the expCacheWarm class.
 *
 * Keeps the response cache full, so that no visitor is the one who pays for a
 * page to be rendered.
 *
 * The numbers this exists for, measured on this installation: a page served
 * from the cache takes about 70ms, and the same page rendered takes between
 * 1000 and 1600ms, because the front page alone costs 529 database queries.
 * The cache holds a page for as long as its Cache-Control says, and the moment
 * that expires the next person to ask waits more than a second.
 *
 * So something should ask first. This walks the published content, requests
 * each page the way an anonymous visitor would, and reports which ones were
 * slow enough to have been a miss. Run it on a timer shorter than the cache
 * window and the slow path belongs to this script rather than to a person.
 *
 * It requests over the loopback HTTP port rather than the public TLS one:
 * there is no reason to spend a handshake on it, and it keeps the warmer off
 * the access log the public site is measured by.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license   For full copyright and license information view LICENSE file.
 * @package   kernel
 */

class expCacheWarm
{
    /** Anything slower than this was almost certainly a cache miss. */
    const MISS_THRESHOLD_MS = 300;

    /**
     * The URLs worth keeping warm, most important first.
     *
     * The root comes first because it is what most visitors ask for, then the
     * published nodes by their URL alias. A node without an alias is skipped:
     * it is reachable only by an internal path nobody links to.
     *
     * @param int $limit
     * @return array of path strings, each beginning with a slash
     */
    public static function urls( $limit = 0 )
    {
        $paths = array( '/' );

        // Only the public content tree.
        //
        // The node table also holds users, groups and the media library, which
        // are reachable only to someone signed in. Warming those asks the
        // server for pages an anonymous visitor is refused, which fills the
        // log with 401s and 404s and warms nothing: the first run reported
        // five failures out of seven for exactly that reason.
        // This siteaccess's own root, not the content tree's.
        //
        // The installation is a multisite: node 2 holds one subtree per site,
        // and the other sites answer on other hosts. Warming from node 2 asks
        // this host for another site's pages and is told 404 -- correctly --
        // which is what the first runs showed for every /bold-agency path.
        //
        // IndexPage names the node this siteaccess treats as its front page,
        // so its subtree is exactly the set of pages this host serves.
        $ini = eZINI::instance();
        $rootNode = 2;
        if ( $ini->hasVariable( 'SiteSettings', 'IndexPage' ) )
        {
            $indexPage = (string)$ini->variable( 'SiteSettings', 'IndexPage' );
            if ( preg_match( '#/(\d+)\s*$#', $indexPage, $m ) )
                $rootNode = (int)$m[1];
        }
        if ( $ini->hasVariable( 'NodeSettings', 'RootNode' ) )
            $rootNode = (int)$ini->variable( 'NodeSettings', 'RootNode' );

        $nodes = eZContentObjectTreeNode::subTreeByNodeID(
            array(
                'Depth' => 6,
                'DepthOperator' => 'le',
                'Limitation' => array(),   // anonymous view, not the caller's
                'AsObject' => true,
                'SortBy' => array( 'depth', true ),
            ),
            $rootNode
        );

        foreach ( (array)$nodes as $node )
        {
            // urlAlias(), not path_identification_string. The latter is an
            // internal identifier -- "bold_agency", "fit_healthy" -- and
            // requesting it gives a 404, which the first run demonstrated for
            // seven pages out of ten. The alias is the address a visitor uses
            // and therefore the one the cache is keyed by.
            $alias = trim( (string)$node->urlAlias() );
            if ( $alias === '' )
                continue;

            $paths[] = '/' . ltrim( $alias, '/' );
            if ( $limit > 0 and count( $paths ) >= $limit )
                break;
        }

        return array_values( array_unique( $paths ) );
    }

    /**
     * Request every path, several at a time, and report what happened.
     *
     * Parallel because the point is to finish inside the cache window. One at
     * a time, a few hundred pages at a second each outlives the window it is
     * trying to stay ahead of, and the warmer becomes the thing causing misses.
     *
     * @param array $paths
     * @param array $options host, base, concurrency, verbose
     * @return array ok / message / data
     */
    public static function warm( array $paths, array $options = array() )
    {
        if ( !function_exists( 'curl_multi_init' ) )
            return self::fail( 'the curl extension is required to warm in parallel' );

        $host = isset( $options['host'] ) ? $options['host'] : 'alpha.se7enx.com';
        $base = isset( $options['base'] ) ? $options['base'] : 'http://127.0.0.1:8088';
        $concurrency = max( 1, (int)( isset( $options['concurrency'] ) ? $options['concurrency'] : 8 ) );
        $verbose = !empty( $options['verbose'] );

        $queue = array_values( $paths );
        $total = count( $queue );
        $done = 0; $missed = 0; $failed = 0;
        $slowest = array( 'path' => '', 'ms' => 0 );
        $startedAll = microtime( true );

        $multi = curl_multi_init();
        $active = array();

        $add = function () use ( &$queue, &$active, $multi, $host, $base ) {
            if ( !$queue ) return false;
            $path = array_shift( $queue );
            $ch = curl_init();
            curl_setopt_array( $ch, array(
                CURLOPT_URL => $base . $path,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_NOBODY => false,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_HTTPHEADER => array(
                    'Host: ' . $host,
                    // The cache keys on the encoding variant, so warm the one
                    // a browser will ask for rather than a different entry
                    // nobody will ever read.
                    'Accept-Encoding: gzip, deflate, br',
                    'User-Agent: Exponential-cache-warmer',
                ),
            ) );
            curl_multi_add_handle( $multi, $ch );
            $active[(int)$ch] = array( 'path' => $path, 'started' => microtime( true ) );
            return true;
        };

        for ( $i = 0; $i < $concurrency; $i++ )
            if ( !$add() ) break;

        do
        {
            curl_multi_exec( $multi, $running );
            curl_multi_select( $multi, 0.2 );

            while ( ( $info = curl_multi_info_read( $multi ) ) !== false )
            {
                $ch = $info['handle'];
                $key = (int)$ch;
                $path = isset( $active[$key] ) ? $active[$key]['path'] : '?';
                $ms = curl_getinfo( $ch, CURLINFO_TOTAL_TIME ) * 1000;
                $code = (int)curl_getinfo( $ch, CURLINFO_HTTP_CODE );

                $done++;
                if ( $code < 200 or $code >= 400 )
                {
                    $failed++;
                    if ( $verbose )
                        printf( "    %4d  %7.0f ms  %s\n", $code, $ms, $path );
                }
                else
                {
                    if ( $ms > self::MISS_THRESHOLD_MS )
                    {
                        $missed++;
                        if ( $verbose )
                            printf( "    warmed %7.0f ms  %s\n", $ms, $path );
                    }
                    if ( $ms > $slowest['ms'] )
                        $slowest = array( 'path' => $path, 'ms' => $ms );
                }

                curl_multi_remove_handle( $multi, $ch );
                curl_close( $ch );
                unset( $active[$key] );
                $add();
            }
        }
        while ( $running > 0 or $active or $queue );

        curl_multi_close( $multi );

        $elapsed = microtime( true ) - $startedAll;

        return self::ok(
            sprintf( 'warmed %d of %d page(s) in %.1fs', $done - $failed, $total, $elapsed ),
            array(
                'requested'   => $total,
                'rendered'    => $missed,
                'from_cache'  => $done - $failed - $missed,
                'failed'      => $failed,
                'seconds'     => round( $elapsed, 1 ),
                'slowest'     => $slowest['path'] . ' (' . round( $slowest['ms'] ) . 'ms)',
            ) );
    }

    protected static function ok( $message, array $data = array() )
    {
        return array( 'ok' => true, 'message' => $message, 'data' => $data );
    }

    protected static function fail( $message, array $data = array() )
    {
        return array( 'ok' => false, 'message' => $message, 'data' => $data );
    }
}
