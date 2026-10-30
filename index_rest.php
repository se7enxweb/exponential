<?php
/**
 * File containing the rest bootstrap
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// Maintenance mode (var/maintenance.json): answered here, before the settings,
// the database and every cache, so a site being installed or taken offline never
// serves a half-built page. Asked for every request: a persistent worker
// (Velocity, FrankenPHP) remembers what it learnt about files, so a plain
// is_file() here kept serving the site to the workers that had looked before
// the installation began. See kernel/classes/expmaintenance.php.
if ( !class_exists( 'expMaintenance', false ) )
    require_once __DIR__ . '/kernel/classes/expmaintenance.php';
if ( expMaintenance::check( __DIR__ ) )
    return;

// The GraphQL response cache: a GET or a POST of a persisted query (/api/graphql?id=..., or a JSON body naming one)
// whose answer is cached is answered here, before autoload, settings, siteaccess and database, anonymous or with
// credentials the kernel checked a moment ago (doc/eng-us/features/6.0/graphql.md, "Speed"). Only what the raw
// request says decides it; anything else goes on to the kernel. See kernel/classes/graphql/expgraphqlresponsecache.php.
if ( isset( $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'] ) && ( $_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST' )
     && strpos( $_SERVER['REQUEST_URI'], '/api/graphql' ) !== false )
{
    if ( !class_exists( 'expGraphQLResponseCache', false ) )
        require_once __DIR__ . '/kernel/classes/graphql/expgraphqlresponsecache.php';
    if ( expGraphQLResponseCache::serveEarly( __DIR__, $_SERVER ) )
        return;
}

require __DIR__ . '/autoload.php';

ignore_user_abort( true );
error_reporting( E_ALL ); // was E_ALL | E_STRICT & E_DEPRECATED, which evaluates to E_ALL; E_STRICT is deprecated in 8.4
require 'autoload.php';

$kernel = new ezpKernel( new ezpKernelRest() );

// mvc_tools will directly output the headers and content
$kernel->run();
