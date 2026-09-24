#!/usr/bin/env php
<?php
/**
 * File containing the cache warming command.
 *
 * Discovered by the console as exp:warm.
 *
 *   bin/php/console exp:warm                 warm everything
 *   bin/php/console exp:warm --limit=40      the first 40 pages only
 *   bin/php/console exp:warm --verbose       name each page that had to render
 *   bin/php/console exp:warm --json
 *
 * Run it on a timer shorter than the cache window. Measured here: a page from
 * the cache takes about 70ms and the same page rendered takes 1000 to 1600ms,
 * so whoever asks first after an entry expires waits over a second. This makes
 * that first asker a script instead of a visitor.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license   For full copyright and license information view LICENSE file.
 * @package   kernel
 */

require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array(
    'description' => "Exponential cache warmer\n\nRequest every published page so no visitor pays for a render.",
    'use-session' => false,
    'use-modules' => true,
    'use-extensions' => true,
) );
$script->startup();

$options = $script->getOptions(
    '[json][verbose][limit:][host:][base:][concurrency:]',
    '',
    array(
        'json'        => 'Print the result as JSON.',
        'verbose'     => 'Name every page that had to be rendered.',
        'limit'       => 'Warm at most this many pages.',
        'host'        => 'Host header to send (default: alpha.se7enx.com).',
        'base'        => 'Where to send them (default: http://127.0.0.1:8088).',
        'concurrency' => 'How many at once (default: 8).',
    ) );
$script->initialize();

require_once 'kernel/classes/expcachewarm.php';

// Where the server is actually listening, rather than an assumption. It binds
// the address named in the service settings, which on this installation is a
// public interface and not loopback, so a hard-coded 127.0.0.1 simply fails to
// connect and reports every page as unreachable.
$base = isset( $options['base'] ) ? $options['base'] : '';
if ( $base === '' )
{
    $velocity = eZINI::instance( 'velocity.ini' );
    $bindHost = $velocity->hasVariable( 'ServerSettings', 'Host' )
              ? $velocity->variable( 'ServerSettings', 'Host' ) : '127.0.0.1';
    $bindPort = $velocity->hasVariable( 'ServerSettings', 'Port' )
              ? (int)$velocity->variable( 'ServerSettings', 'Port' ) : 8088;
    $base = 'http://' . $bindHost . ':' . $bindPort;
}

// The Host header a browser actually sends, port and all.
//
// The response cache keys on host, path and encoding. A browser asking for
// https://alpha.se7enx.com:8080/site sends "alpha.se7enx.com:8080"; warming
// with "alpha.se7enx.com" fills a different entry, which nothing ever reads.
// The warmer then reports success while every visitor still pays the render --
// measured at 1036ms for a page the cache could have answered in 3ms.
$warmHost = isset( $options['host'] ) ? $options['host'] : '';
if ( $warmHost === '' )
{
    $siteIni = eZINI::instance();
    $warmHost = $siteIni->hasVariable( 'SiteSettings', 'SiteURL' )
              ? (string)$siteIni->variable( 'SiteSettings', 'SiteURL' ) : 'alpha.se7enx.com';

    $velocityIni = eZINI::instance( 'velocity.ini' );
    if ( $velocityIni->hasVariable( 'ServerSettings', 'HTTPSPort' ) )
    {
        $httpsPort = (int)$velocityIni->variable( 'ServerSettings', 'HTTPSPort' );
        // 443 is implied and a browser omits it; anything else is part of the
        // Host header and therefore part of the cache key.
        if ( $httpsPort and $httpsPort !== 443 and strpos( $warmHost, ':' ) === false )
            $warmHost .= ':' . $httpsPort;
    }
}

$paths = expCacheWarm::urls( isset( $options['limit'] ) ? (int)$options['limit'] : 0 );

$result = expCacheWarm::warm( $paths, array(
    'host'        => $warmHost,
    'base'        => $base,
    'concurrency' => isset( $options['concurrency'] ) ? (int)$options['concurrency'] : 8,
    'verbose'     => !empty( $options['verbose'] ),
) );

if ( !empty( $options['json'] ) )
{
    $cli->output( json_encode( $result ) );
}
else
{
    $cli->output( ( $result['ok'] ? '  ' : '  ERROR: ' ) . $result['message'] );
    foreach ( $result['data'] as $k => $v )
        $cli->output( sprintf( '    %-11s %s', $k, is_bool( $v ) ? ( $v ? 'yes' : 'no' ) : $v ) );
}

$script->shutdown( $result['ok'] ? 0 : 1 );
