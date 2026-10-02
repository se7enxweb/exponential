<?php
/**
 * The code of kernel/search/stats.php, moved into a class (#207 stage 1). The file kernel/search/stats.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/search/stats.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Search
{

class Stats extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        list( $limit, $limitChoice, $limitChoices ) =
            \expAdminPagination::chosen( 'search/stats', 'admin_search_stats_limit' );

        $offset = $Params['Offset'];
        if ( !is_numeric( $offset ) )
        {
            $offset = 0;
        }

        $http = \eZHTTPTool::instance();
        $module = $Params['Module'];

        if ( $module->isCurrentAction( 'ResetSearchStats' ) )
        {
            \eZSearchLog::removeStatistics();
        }

        $viewParameters = array( 'offset' => $offset, 'limit'  => $limit );
        $tpl = \eZTemplate::factory();

        $db = \eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
        {
            $rows = $db->aggregate( 'ezsearch_search_phrase', [ [ '$count' => 'count' ] ] );
            $searchListCount = [ [ 'count' => !empty( $rows ) ? (int) $rows[0]['count'] : 0 ] ];
        }
        else
        {
            $query = "SELECT count(*) as count FROM ezsearch_search_phrase";
            $searchListCount = $db->arrayQuery( $query );
        }

        $mostFrequentPhraseArray = \eZSearchLog::mostFrequentPhraseArray( $viewParameters );

        $tpl->setVariable( "view_parameters", $viewParameters );
        $tpl->setVariable( "limit_choices", $limitChoices );
        $tpl->setVariable( "limit_choice", $limitChoice );
        $tpl->setVariable( "most_frequent_phrase_array", $mostFrequentPhraseArray );
        $tpl->setVariable( "search_list_count", $searchListCount[0]['count'] );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:search/stats.tpl" );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/search', 'Search stats' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
