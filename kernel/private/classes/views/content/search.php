<?php
/**
 * The code of kernel/content/search.php, moved into a class (#207 stage 1). The file kernel/content/search.php is one call to it.
 * Guide: doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/search.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{
if ( !function_exists( 'expModuleViewContentSearchPageLimit' ) ) {
/*!
 Get search limit
 */
function expModuleViewContentSearchPageLimit( $searchPageLimit )
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

        // a negative offset makes the search engine fail
        $Offset = is_numeric( $Offset ) ? max( 0, (int)$Offset ) : 0;

        $searchPageLimit = 2;
        $tpl = \eZTemplate::factory();
        $ini = \eZINI::instance();
        // default: the view searches and hands the result to the template. A template that is the administration's
        // filtered search page searches itself (fetch content/filtered_search): the view's own search would only
        // run the same query a second time
        $filteredPage = self::templateSearchesItself( 'content/search.tpl' );
        if ( $filteredPage )
        {
            // The page takes its search as view parameters only (/(text)/easter/(class)/2,16, see
            // expContentSearchFilters::viewSuffix()), never as a query string. Its filter bar and the header search
            // field are posted and answered with a 303 to that address; a search of the browse window
            // (Mode=browse) is not the page and keeps its own handling below.
            if ( \expViewParameters::posted( $_POST, 'Mode' ) !== 'browse'
                 && \expViewParameters::redirectsPost( $_POST, \expContentSearchFilters::FORM_FIELDS ) )
                \expViewParameters::redirect( 'content/search' . \expContentSearchFilters::fromRequest( $_POST )->viewSuffix() );
            \expContentSearchFilters::useViewParameters( $scope['Params'] );
        }
        $useSearchCode = $ini->variable( 'SearchSettings', 'SearchViewHandling' ) == 'default'
                         && !$filteredPage;
        $logSearchStats = $ini->variable( 'SearchSettings', 'LogSearchStats' ) == 'enabled';

        if ( $http->hasVariable( 'BrowsePageLimit' ) )
        {
            $pageLimit = (int)$http->variable( 'BrowsePageLimit' );
            if ( $pageLimit < 1 )
                $pageLimit = expModuleViewContentSearchPageLimit( $searchPageLimit );
        }
        else
        {
            if ( $http->hasVariable( 'SearchPageLimit' ) )
            {
                $searchPageLimit = (int)$http->variable( 'SearchPageLimit' );
            }
            $pageLimit = expModuleViewContentSearchPageLimit( $searchPageLimit );
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
        // A very long text makes the search engine fail: it is cut to site.ini [SearchSettings]
        // MaximumSearchTextWords words and MaximumSearchTextLength characters
        list( $maximumWords, $maximumLength ) = self::searchTextLimits( $ini );
        $requestedSearchText = $searchText;
        $searchText = self::limitSearchText( $searchText, $maximumWords, $maximumLength );
        $searchTextShortened = $searchText !== $requestedSearchText;
        // the filtered search page: its text is the view parameter (text), for the statistics and the audit
        if ( $filteredPage )
            $searchText = self::limitSearchText( (string)\expViewParameters::get( $scope['Params'], 'text' ), $maximumWords, $maximumLength );

        // A section that does not exist is no section: the search is not limited to it
        $searchSectionID = -1;
        $section = null;
        if ( $http->hasVariable( "SectionID" ) )
        {
            $searchSectionID = self::sectionID( $http->variable( "SectionID" ) );
            if ( $searchSectionID != -1 )
            {
                $section = \eZSection::fetch( $searchSectionID );
                if ( !$section instanceof \eZSection )
                    $searchSectionID = -1;
            }
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
                if ( is_numeric( $subTreeItem ) && (int)$subTreeItem > 0 )
                    $subTreeArray[] = (int)$subTreeItem;
            }
        }
        // no subtree asked for: the siteaccess's own tree when it has a PathPrefix
        if ( !$subTreeArray && $ini->hasVariable( 'SiteAccessSettings', 'PathPrefix' ) )
            $subTreeArray = self::pathPrefixSubTreeArray( $ini->variable( 'SiteAccessSettings', 'PathPrefix' ) );

        $Module->setTitle( "Search for: " . htmlspecialchars( $searchText, ENT_QUOTES, 'UTF-8' ) );

        // Without a text there is nothing to search for, unless site.ini [SearchSettings] AllowEmptySearch is
        // enabled: an empty search would list every object
        $searchRequested = trim( $searchText ) !== '' || $ini->variable( 'SearchSettings', 'AllowEmptySearch' ) == 'enabled';
        // null until a search ran (or none is requested); the browse mode below searches when it is still null
        $searchResult = $searchRequested ? null : self::emptySearchResult();

        if ( $useSearchCode && $searchRequested )
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
            $keyArray = array( array( 'section', $searchSectionID ),
                               array( 'section_identifier', $section->attribute( 'identifier' ) ) );
            $res->setKeys( $keyArray );
        }

        $viewParameters = array( 'offset' => $Offset );

        $searchData = false;
        $tpl->setVariable( "search_data", $searchData );
        // The filtered search page has its section as the view parameter (section)/<id>: the header search's scope
        // popup preselects it from search_section_id. The view's own search and the design keys stay as they were.
        $tpl->setVariable( "search_section_id", $filteredPage && $searchSectionID == -1 ? self::viewSectionID( $scope['Params'] ) : $searchSectionID );
        $tpl->setVariable( "search_subtree_array", $subTreeArray );
        $tpl->setVariable( 'search_timestamp', $searchTimestamp );
        $tpl->setVariable( "search_text", $searchText );
        $tpl->setVariable( 'search_text_shortened', $searchTextShortened );
        $tpl->setVariable( 'search_requested', $searchRequested );
        $tpl->setVariable( 'search_page_limit', $searchPageLimit );

        $tpl->setVariable( "view_parameters", $viewParameters );
        $tpl->setVariable( 'use_template_search', !$useSearchCode );

        if ( $http->hasVariable( 'Mode' ) && $http->variable( 'Mode' ) == 'browse' )
        {
            if ( $searchResult === null )
                $searchResult = \eZSearch::search( $searchText, array( "SearchType" => $searchType,
                                                                      "SearchSectionID" => $searchSectionID,
                                                                      "SearchSubTreeArray" => $subTreeArray,
                                                                      'SearchTimestamp' => $searchTimestamp,
                                                                      "SearchLimit" => $pageLimit,
                                                                      "SearchOffset" => $Offset ) );
            $sys = \eZSys::instance();
            $searchResult['RequestedURI'] = "content/search";
        //    $searchResult['RequestedURISuffix'] = $sys->serverVariable( "QUERY_STRING" );


            $searchResult['RequestedURISuffix'] = self::pageURISuffix( $searchText, $subTreeArray, $searchSectionID, $searchTimestamp ) . '&BrowsePageLimit=' . $pageLimit . '&Mode=browse';
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->run( 'browse',array(),array( "NodeList" => $searchResult,
                                                         "Offset" => $Offset,
                                                         "NodeID" => isset( $subTreeArray[0] ) && $subTreeArray[0] != 1 ? $subTreeArray[0] : null  ) ) );
        }

        // Audit (doc/eng-us/bc/6.0/audit.md, content.search.query): a sampled read
        if ( $searchRequested && isset( $searchResult ) && is_array( $searchResult ) && class_exists( 'expAuditHook' ) )
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
            // with the search code on, a search ran when one was requested: $searchResult is set
            if ( $searchResult === null )
                $searchResult = self::emptySearchResult();
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

        // The statistics count what the page showed: a template that searches itself (the administration's
        // filtered search page) leaves its result in search_data, also with SearchViewHandling=default, where the
        // view's own, unfiltered search would give another count.
        $searchData = $tpl->hasVariable( "search_data" ) ? $tpl->variable( "search_data" ) : false;
        if ( $useSearchCode && !is_array( $searchData ) )
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

    /**
     * What the pages of a search pass on: the text, every subtree (SubTreeArray[] once per node), the section and
     * the time limit. Without the section and with the subtrees joined by commas, the second page of a search
     * searched elsewhere than the first.
     *
     * @param string $searchText
     * @param int[] $subTreeArray
     * @param int $sectionID -1 for none
     * @param int|false $searchTimestamp
     * @return string the query string, without the leading ? or &
     */
    public static function pageURISuffix( $searchText, array $subTreeArray, $sectionID, $searchTimestamp )
    {
        $suffix = 'SearchText=' . urlencode( (string)$searchText );
        foreach ( $subTreeArray as $subTreeID )
            $suffix .= '&' . urlencode( 'SubTreeArray[]' ) . '=' . (int)$subTreeID;
        if ( (int)$sectionID > 0 )
            $suffix .= '&SectionID=' . (int)$sectionID;
        if ( (int)$searchTimestamp > 0 )
            $suffix .= '&SearchTimestamp=' . (int)$searchTimestamp;
        return $suffix;
    }

    /**
     * A section id of the request: a positive integer, or -1 for none.
     *
     * @param mixed $value
     * @return int
     */
    public static function sectionID( $value )
    {
        $sectionID = is_scalar( $value ) ? (int)$value : 0;
        return $sectionID > 0 ? $sectionID : -1;
    }

    /**
     * The section of the view parameter (section)/<id> when it names exactly one existing section, for the header
     * search's scope popup (which holds one section); else -1. A query string is never read.
     *
     * @param array $params the view's $Params
     * @param callable|null $exists checks that a section id exists (default eZSection::fetch())
     * @return int
     */
    public static function viewSectionID( array $params, $exists = null )
    {
        $value = \expViewParameters::get( $params, 'section' );
        if ( $value === false || strpos( $value, ',' ) !== false )
            return -1;
        $sectionID = self::sectionID( $value );
        if ( $sectionID == -1 )
            return -1;
        $exists = $exists ?: function ( $id ) { return \eZSection::fetch( $id ) instanceof \eZSection; };
        return $exists( $sectionID ) ? $sectionID : -1;
    }

    /**
     * site.ini [SearchSettings] MaximumSearchTextWords and MaximumSearchTextLength: how many words and characters
     * of a search text are searched for. A missing or invalid setting gives 16 words and 1024 characters.
     *
     * @param \eZINI $ini site.ini
     * @return int[] array( <words>, <characters> )
     */
    public static function searchTextLimits( $ini )
    {
        $words = $ini->hasVariable( 'SearchSettings', 'MaximumSearchTextWords' ) ? (int)$ini->variable( 'SearchSettings', 'MaximumSearchTextWords' ) : 0;
        $length = $ini->hasVariable( 'SearchSettings', 'MaximumSearchTextLength' ) ? (int)$ini->variable( 'SearchSettings', 'MaximumSearchTextLength' ) : 0;
        return array( $words > 0 ? $words : 16, $length > 0 ? $length : 1024 );
    }

    /**
     * A search text cut to at most $maximumWords words and $maximumLength characters. Every word is a clause of
     * the search engine's query (one per field searched), and with too many of them the search fails (eZ Find:
     * "too many clauses") instead of giving results. A text within both limits is returned as it is.
     *
     * @param string $searchText
     * @param int $maximumWords 0 or less gives an empty text
     * @param int $maximumLength 0 or less gives an empty text
     * @return string
     */
    public static function limitSearchText( $searchText, $maximumWords, $maximumLength )
    {
        $searchText = (string)$searchText;
        if ( $maximumWords <= 0 || $maximumLength <= 0 )
            return '';
        if ( mb_strlen( $searchText, 'UTF-8' ) > $maximumLength )
            $searchText = mb_substr( $searchText, 0, $maximumLength, 'UTF-8' );
        $words = preg_split( '/\s+/u', trim( $searchText ), -1, PREG_SPLIT_NO_EMPTY );
        if ( $words !== false && count( $words ) > $maximumWords )
            $searchText = implode( ' ', array_slice( $words, 0, $maximumWords ) );
        return $searchText;
    }

    /**
     * The result of a search that was not run.
     *
     * @return array
     */
    public static function emptySearchResult()
    {
        return array( 'SearchResult' => array(), 'SearchCount' => 0, 'StopWordArray' => array() );
    }

    /**
     * Whether the template the view renders is the administration's filtered search page, which searches itself:
     * the design's content/search.tpl (or content/advancedsearch.tpl) includes design:content/search_page.tpl.
     * Looked up along the design bases of the siteaccess as the template resource does (without override rules).
     *
     * @param string $template the template path below templates/, e.g. 'content/search.tpl'
     * @param array|null $bases design bases (tests); null: eZTemplateDesignResource::allDesignBases()
     * @return bool
     */
    public static function templateSearchesItself( $template, $bases = null )
    {
        $tried = array();
        $match = \eZTemplateDesignResource::fileMatch( $bases === null ? \eZTemplateDesignResource::allDesignBases() : $bases,
                                                       'templates', $template, $tried );
        if ( !is_array( $match ) || !is_readable( $match['path'] ) )
            return false;
        $text = file_get_contents( $match['path'], false, null, 0, 16384 );
        // the administration's page, or a site's (content/front_search_page.tpl, with or without parameters)
        return is_string( $text ) && ( strpos( $text, "{include uri='design:content/search_page.tpl'}" ) !== false
                                     || strpos( $text, "{include uri='design:content/front_search_page.tpl'" ) !== false );
    }

    /**
     * The subtree a search without SubTreeArray is limited to: the node of the siteaccess's
     * PathPrefix (site.ini [SiteAccessSettings] PathPrefix), or none.
     *
     * A siteaccess with a PathPrefix only reaches the content under that node: everything
     * else is linked with the prefix in front of its own path and answers 404. Unscoped, the
     * search of /bold listed the other sites' pages as /bold/fit-healthy/..., all broken.
     *
     * @param string $pathPrefix
     * @param callable|null $resolver maps a URL alias path to a node id (0 when there is none);
     *                                defaults to the URL alias table
     * @return int[] the node id, or an empty array (no PathPrefix, or one that is not a node)
     */
    public static function pathPrefixSubTreeArray( $pathPrefix, $resolver = null )
    {
        $pathPrefix = trim( (string)$pathPrefix, '/' );
        if ( $pathPrefix === '' )
            return array();
        if ( $resolver === null )
        {
            $resolver = function ( $path )
            {
                $elements = \eZURLAliasML::fetchByPath( $path );
                if ( !$elements || !preg_match( '#^eznode:(\d+)$#', (string)$elements[0]->attribute( 'action' ), $m ) )
                    return 0;
                return (int)$m[1];
            };
        }
        $nodeID = (int)call_user_func( $resolver, $pathPrefix );
        return $nodeID > 0 ? array( $nodeID ) : array();
    }
}

}
