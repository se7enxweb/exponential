<?php
/**
 * The code of kernel/content/search.php, moved into a class (#207 stage 1). The file kernel/content/search.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/search.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{
if ( !function_exists( 'pageLimit' ) ) {
/*!
 Get search limit
 */
function pageLimit( $searchPageLimit )
{
    switch ( $searchPageLimit )
    {
        case 1:
            return 5;

        case 2:
        default:
            return 10;

        case 3:
            return 20;

        case 4:
            return 30;

        case 5:
            return 50;
    }
}
}
}

namespace Exponential\View\Kernel\Content
{

class Search extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();

        $Module = $Params['Module'];
        $Offset = $Params['Offset'];

        if ( !is_numeric( $Offset ) )
            $Offset = 0;

        $searchPageLimit = 2;
        $tpl = \eZTemplate::factory();
        $ini = \eZINI::instance();
        $useSearchCode = $ini->variable( 'SearchSettings', 'SearchViewHandling' ) == 'default';
        $logSearchStats = $ini->variable( 'SearchSettings', 'LogSearchStats' ) == 'enabled';

        if ( $http->hasVariable( 'BrowsePageLimit' ) )
        {
            $pageLimit = (int)$http->variable( 'BrowsePageLimit' );
        }
        else
        {
            if ( $http->hasVariable( 'SearchPageLimit' ) )
            {
                $searchPageLimit = (int)$http->variable( 'SearchPageLimit' );
            }
            $pageLimit = pageLimit( $searchPageLimit );
        }

        $maximumSearchLimit = $ini->variable( 'SearchSettings', 'MaximumSearchLimit' );
        if ( $pageLimit > $maximumSearchLimit )
            $pageLimit = $maximumSearchLimit;

        $searchText = '';
        if ( $http->hasVariable( "SearchText" ) )
        {
            $searchText = $http->variable( "SearchText" );
            // A form or link can pass the text as an array; treat that as no text
            if ( !is_string( $searchText ) ) $searchText = "";
        }

        $searchSectionID = -1;
        if ( $http->hasVariable( "SectionID" ) )
        {
            $searchSectionID = (int)$http->variable( "SectionID" );
        }

        $searchTimestamp = false;
        if ( $http->hasVariable( 'SearchTimestamp' ) and
             $http->variable( 'SearchTimestamp' ) )
        {
            $searchTimestamp = (int)$http->variable( 'SearchTimestamp' );
        }

        $searchType = "fulltext";
        if ( $http->hasVariable( "SearchType" ) )
        {
            $searchType = $http->variable( "SearchType" );
        }

        $subTreeArray = array();
        if ( $http->hasVariable( "SubTreeArray" ) )
        {
            if ( is_array( $http->variable( "SubTreeArray" ) ) )
                $subTreeList = $http->variable( "SubTreeArray" );
            else
                $subTreeList = array( $http->variable( "SubTreeArray" ) );
            foreach ( $subTreeList as $subTreeItem )
            {
                if ( is_numeric( $subTreeItem ) && $subTreeItem > 0 )
                    $subTreeArray[] = $subTreeItem;
            }
        }

        $Module->setTitle( "Search for: " . htmlspecialchars( $searchText, ENT_QUOTES, 'UTF-8' ) );

        if ( $useSearchCode )
        {
            $sortArray = array( array( 'attribute', true, 153 ), array( 'priority', true ) );
            $searchResult = \eZSearch::search( $searchText, array( "SearchType" => $searchType,
                                                                  "SearchSectionID" => $searchSectionID,
                                                                  "SearchSubTreeArray" => $subTreeArray,
                                                                  'SearchTimestamp' => $searchTimestamp,
                                                                  "SearchLimit" => $pageLimit,
                                                                  "SearchOffset" => $Offset ) );
        }

        if ( $searchSectionID != -1 )
        {
            $res = \eZTemplateDesignResource::instance();
            $section = \eZSection::fetch( $searchSectionID );
            $keyArray = array( array( 'section', $searchSectionID ),
                               array( 'section_identifier', $section->attribute( 'identifier' ) ) );
            $res->setKeys( $keyArray );
        }

        $viewParameters = array( 'offset' => $Offset );

        $searchData = false;
        $tpl->setVariable( "search_data", $searchData );
        $tpl->setVariable( "search_section_id", $searchSectionID );
        $tpl->setVariable( "search_subtree_array", $subTreeArray );
        $tpl->setVariable( 'search_timestamp', $searchTimestamp );
        $tpl->setVariable( "search_text", $searchText );
        $tpl->setVariable( 'search_page_limit', $searchPageLimit );

        $tpl->setVariable( "view_parameters", $viewParameters );
        $tpl->setVariable( 'use_template_search', !$useSearchCode );

        if ( $http->hasVariable( 'Mode' ) && $http->variable( 'Mode' ) == 'browse' )
        {
            if( !isset( $searchResult ) )
                $searchResult = \eZSearch::search( $searchText, array( "SearchType" => $searchType,
                                                                      "SearchSectionID" => $searchSectionID,
                                                                      "SearchSubTreeArray" => $subTreeArray,
                                                                      'SearchTimestamp' => $searchTimestamp,
                                                                      "SearchLimit" => $pageLimit,
                                                                      "SearchOffset" => $Offset ) );
            $sys = \eZSys::instance();
            $searchResult['RequestedURI'] = "content/search";
        //    $searchResult['RequestedURISuffix'] = $sys->serverVariable( "QUERY_STRING" );


            $searchResult['RequestedURISuffix'] = 'SearchText=' . urlencode ( $searchText ) . ( isset( $subTreeArray[0] ) ? '&SubTreeArray=' . $subTreeArray[0] : '' ) . ( ( $searchTimestamp > 0 ) ?  '&SearchTimestamp=' . $searchTimestamp : '' ) . '&BrowsePageLimit=' . $pageLimit . '&Mode=browse';
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->run( 'browse',array(),array( "NodeList" => $searchResult,
                                                         "Offset" => $Offset,
                                                         "NodeID" => isset( $subTreeArray[0] ) && $subTreeArray[0] != 1 ? $subTreeArray[0] : null  ) ) );
        }

        // Audit (doc/bc/6.0/audit.md, content.search.query): a sampled read
        if ( isset( $searchResult ) && is_array( $searchResult ) && class_exists( 'expAuditHook' ) )
            \expAuditHook::read( 'content.search.query', null, function () use ( $searchText, $searchResult ) {
                $phrase = (string)$searchText;
                if ( function_exists( 'mb_substr' ) && mb_strlen( $phrase ) > 64 )
                    $phrase = mb_substr( $phrase, 0, 64 ) . '…';
                return array( 'object' => array( 'type' => 'search', 'id' => 'content/search' ),
                              'after' => array( 'phrase' => $phrase, 'hits' => isset( $searchResult['SearchCount'] ) ? (int)$searchResult['SearchCount'] : null ) );
            } );

        // --- Compatibility code start ---
        if ( $useSearchCode )
        {
            $tpl->setVariable( "offset", $Offset );
            $tpl->setVariable( "page_limit", $pageLimit );
            $tpl->setVariable( "search_text_enc", urlencode( $searchText ) );
            $tpl->setVariable( "search_result", $searchResult["SearchResult"] );
            $tpl->setVariable( "search_count", $searchResult["SearchCount"] );
            $tpl->setVariable( "stop_word_array", $searchResult["StopWordArray"] );
            if ( isset( $searchResult["SearchExtras"] ) )
            {
                $tpl->setVariable( "search_extras", $searchResult["SearchExtras"] );
            }
        }
        else
        {
            $tpl->setVariable( "offset", false );
            $tpl->setVariable( "page_limit", false );
            $tpl->setVariable( "search_text_enc", false );
            $tpl->setVariable( "search_result", false );
            $tpl->setVariable( "search_count", false );
            $tpl->setVariable( "stop_word_array", false );
        }
        // --- Compatibility code end ---

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:content/search.tpl" );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'Search' ),
                                        'url' => false ) );

        $searchData = false;
        if ( !$useSearchCode )
        {
            if ( $tpl->hasVariable( "search_data" ) )
            {
                $searchData = $tpl->variable( "search_data" );
            }
        }
        else
        {
            $searchData = $searchResult;
        }

        if ( $logSearchStats and
             trim( $searchText ) != "" and
             is_array( $searchData ) and
             array_key_exists( 'SearchCount', $searchData ) and
             is_numeric( $searchData['SearchCount'] ) )
        {
            \eZSearchLog::addPhrase( $searchText, $searchData["SearchCount"] );
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
