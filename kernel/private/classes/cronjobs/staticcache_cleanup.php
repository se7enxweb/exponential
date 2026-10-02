<?php
/**
 * The code of cronjobs/staticcache_cleanup.php, moved into a class (#207 stage 1). The file cronjobs/staticcache_cleanup.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of cronjobs/staticcache_cleanup.php:
 *
 *
 * @description Remove expired and stale entries from the static page cache
 *
 * File containing the staticcache_cleanup.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\Cronjob\Kernel
{

class StaticcacheCleanup extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $cli->output( "Starting processing pending static cache cleanups" );

        $db = \eZDB::instance();

        $offset = 0;
        $limit = 20;

        do
        {
            $deleteParams = array();
            $markInvalidParams = array();
            $fileContentCache = array();

            $rows = $db->arrayQuery( "SELECT DISTINCT param FROM ezpending_actions WHERE action = 'static_store'",
                                        array( 'limit' => $limit,
                                               'offset' => $offset ) );
            if ( !$rows || ( empty( $rows ) ) )
                break;

            // the batch's pages, each once and several at a time
            $sources = array();
            foreach ( $rows as $row )
            {
                $paramList = explode( ',', $row['param'] );
                if ( isset( $paramList[1] ) && !isset( $fileContentCache[$paramList[1]] ) )
                    $sources[] = $paramList[1];
            }
            if ( $sources )
            {
                $cli->output( 'Fetching ' . count( array_unique( $sources ) ) . ' URLs' );
                $fileContentCache = \eZStaticCache::fetchPages( $sources ) + $fileContentCache;
            }

            foreach ( $rows as $row )
            {
                $param = $row['param'];
                $paramList = explode( ',', $param );
                $source = $paramList[1];
                $destination = $paramList[0];
                $invalid = isset( $paramList[2] ) ? $paramList[2] : null;

                if ( !isset( $fileContentCache[$source] ) )
                {
                    $cli->output( "Fetching URL: $source" );

                    $fileContentCache[$source] = \eZHTTPTool::getDataByURL( $source, false, \eZStaticCache::USER_AGENT );
                }

                if ( $fileContentCache[$source] === false )
                {
                    $cli->error( "Could not grab content from \"$source\", is the hostname correct and Apache running?" );

                    if ( $invalid !== null )
                    {
                        $deleteParams[] = $param;

                        continue;
                    }

                    $markInvalidParams[] = $param;
                }
                else
                {
                    \eZStaticCache::storeCachedFile( $destination, $fileContentCache[$source] );

                    $deleteParams[] = $param;
                }
            }

            if ( !empty( $markInvalidParams ) )
            {
                $db->begin();
                $db->query( "UPDATE ezpending_actions SET param=( " . $db->concatString( array( "param", "',invalid'" ) ) . " ) WHERE param IN ( '" . implode( "','", $markInvalidParams ) . "' )" );
                $db->commit();
            }

            if ( !empty( $deleteParams ) )
            {
                $db->begin();
                $db->query( "DELETE FROM ezpending_actions WHERE action='static_store' AND param IN ( '" . implode( "','", $deleteParams ) . "' )" );
                $db->commit();
            }
            else
            {
                $offset += $limit;
            }
        } while ( true );

        $cli->output( "Done" );
    }
}

}
