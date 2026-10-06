<?php
/**
 * The code of kernel/search/stats.php, moved into a class (#207 stage 1). The file kernel/search/stats.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 * User guide of the page: doc/guides/urls-and-aliases.md (search statistics)
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
    /** The orders the page offers, the first is the default (the order the page always had). */
    const SORTS = array( 'count', 'phrase', 'fewest' );

    /** The filters the page offers: every phrase, or only those whose searches found nothing. */
    const SHOWS = array( 'all', 'none' );

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
        if ( !is_numeric( $offset ) || $offset < 0 )
        {
            $offset = 0;
        }
        $offset = (int)$offset;

        $http = \eZHTTPTool::instance();
        $module = $Params['Module'];

        $userParameters = isset( $Params['UserParameters'] ) ? (array)$Params['UserParameters'] : array();
        $sort = self::sortKey( isset( $userParameters['sort'] ) ? $userParameters['sort'] : '' );
        $show = self::showKey( isset( $userParameters['show'] ) ? $userParameters['show'] : '' );
        $search = \Exponential\View\Kernel\Url\ListView::searchText( $http->hasGetVariable( 'q' ) ? $http->getVariable( 'q' ) : '' );

        $wasReset = false;
        if ( $module->isCurrentAction( 'ResetSearchStats' ) )
        {
            \eZSearchLog::removeStatistics();
            $wasReset = true;
        }

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
        $totalCount = (int)$searchListCount[0]['count'];

        $filtered = ( $search !== '' || $show !== 'all' );
        $matchCount = $filtered ? self::filteredCount( $db, $search, $show ) : $totalCount;
        if ( $offset > 0 && $offset >= $matchCount )
            $offset = 0;

        $viewParameters = array( 'offset' => $offset, 'limit'  => $limit );
        $tpl = \eZTemplate::factory();

        if ( !$filtered && $sort === 'count' )
        {
            // the list the page has always shown, read the way it always was
            $mostFrequentPhraseArray = \eZSearchLog::mostFrequentPhraseArray( $viewParameters );
        }
        else
        {
            $mostFrequentPhraseArray = self::phrases( $db, $search, $show, $sort, $offset, $limit );
        }

        if ( $sort !== self::SORTS[0] )
            $viewParameters['sort'] = $sort;
        if ( $show !== self::SHOWS[0] )
            $viewParameters['show'] = $show;

        $tpl->setVariable( "view_parameters", $viewParameters );
        $tpl->setVariable( "limit_choices", $limitChoices );
        $tpl->setVariable( "limit_choice", $limitChoice );
        $tpl->setVariable( "limit", $limit );
        $tpl->setVariable( "most_frequent_phrase_array", $mostFrequentPhraseArray );
        // the number of phrases the list pages through (all of them unless a search or a filter is used)
        $tpl->setVariable( "search_list_count", $matchCount );
        $tpl->setVariable( "search_total_count", $totalCount );
        $tpl->setVariable( "search_stats_summary", self::summary( $db ) );
        $tpl->setVariable( "search_stats_sort", $sort );
        $tpl->setVariable( "search_stats_sorts", self::SORTS );
        $tpl->setVariable( "search_stats_show", $show );
        $tpl->setVariable( "search_stats_search", $search );
        $tpl->setVariable( "search_stats_suffix", $search !== '' ? '?q=' . rawurlencode( $search ) : '' );
        $tpl->setVariable( "search_stats_reset", $wasReset );
        // the query string of a search for each phrase, so the page can offer to run it again
        $searchQueries = array();
        $pageMax = 0;
        foreach ( (array)$mostFrequentPhraseArray as $row )
        {
            if ( isset( $row['id'] ) )
                $searchQueries[$row['id']] = '?SearchText=' . rawurlencode( (string)$row['phrase'] );
            if ( isset( $row['phrase_count'] ) )
                $pageMax = max( $pageMax, (int)$row['phrase_count'] );
        }
        $tpl->setVariable( "search_stats_queries", $searchQueries );
        $tpl->setVariable( "search_stats_page_max", $pageMax );
        $tpl->setVariable( "search_stats_logging", self::loggingSiteAccesses() );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:search/stats.tpl" );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/search', 'Search stats' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * The order asked for, the default for anything the page does not offer.
     *
     * @param mixed $value
     * @return string
     */
    public static function sortKey( $value )
    {
        return in_array( $value, self::SORTS, true ) ? $value : self::SORTS[0];
    }

    /**
     * The filter asked for, 'all' for anything the page does not offer.
     *
     * @param mixed $value
     * @return string
     */
    public static function showKey( $value )
    {
        return in_array( $value, self::SHOWS, true ) ? $value : self::SHOWS[0];
    }

    /**
     * The LIKE pattern of a phrase search, with %, _ and the escape character ! matched as themselves.
     *
     * @param string $search
     * @return string
     */
    public static function likePattern( $search )
    {
        return '%' . strtr( (string)$search, array( '!' => '!!', '%' => '!%', '_' => '!_' ) ) . '%';
    }

    /**
     * The WHERE of a search and a filter on ezsearch_search_phrase, '' for none.
     *
     * @param \eZDBInterface $db
     * @param string $search
     * @param string $show
     * @return string
     */
    public static function whereSQL( $db, $search, $show )
    {
        $conditions = array();
        if ( $search !== '' )
            $conditions[] = "LOWER( phrase ) LIKE LOWER( '" . $db->escapeString( self::likePattern( $search ) ) . "' ) ESCAPE '!'";
        if ( $show === 'none' )
            $conditions[] = 'result_count = 0';
        return $conditions ? ' WHERE ' . implode( ' AND ', $conditions ) : '';
    }

    /**
     * The ORDER BY of an order: the most searched first, A to Z, or the fewest results on average first (the
     * phrases visitors did not find much for), each ending in the id so pages never overlap.
     *
     * @param string $sort
     * @return string
     */
    public static function orderSQL( $sort )
    {
        switch ( $sort )
        {
            case 'phrase': return ' ORDER BY phrase ASC, id ASC';
            case 'fewest': return ' ORDER BY result_count / phrase_count ASC, phrase_count DESC, id ASC';
        }
        return ' ORDER BY phrase_count DESC, id ASC';
    }

    /**
     * The MongoDB $match of a search and a filter.
     *
     * @param string $search
     * @param string $show
     * @return array
     */
    public static function mongoMatch( $search, $show )
    {
        $match = array();
        if ( $search !== '' )
            $match['phrase'] = array( '$regex' => preg_quote( $search ), '$options' => 'i' );
        if ( $show === 'none' )
            $match['result_count'] = 0;
        return $match;
    }

    /**
     * How many phrases a search and a filter leave.
     *
     * @return int
     */
    public static function filteredCount( $db, $search, $show )
    {
        if ( $db->databaseName() === 'mongo' )
        {
            $rows = $db->aggregate( 'ezsearch_search_phrase', array( array( '$match' => self::mongoMatch( $search, $show ) ), array( '$count' => 'count' ) ) );
            return !empty( $rows ) ? (int)$rows[0]['count'] : 0;
        }
        $rows = $db->arrayQuery( 'SELECT count(*) AS count FROM ezsearch_search_phrase' . self::whereSQL( $db, $search, $show ) );
        return !empty( $rows ) ? (int)$rows[0]['count'] : 0;
    }

    /**
     * One page of phrases with the columns eZSearchLog::mostFrequentPhraseArray() gives: id, phrase, phrase_count
     * and result_count (the average number of results).
     *
     * @return array
     */
    public static function phrases( $db, $search, $show, $sort, $offset, $limit )
    {
        if ( $db->databaseName() === 'mongo' )
        {
            $sorts = array( 'count'  => array( 'phrase_count' => -1, 'id' => 1 ),
                            'phrase' => array( 'phrase' => 1, 'id' => 1 ),
                            'fewest' => array( 'result_count' => 1, 'phrase_count' => -1, 'id' => 1 ) );
            $pipeline = array( array( '$match' => self::mongoMatch( $search, $show ) ),
                               array( '$project' => array( '_id' => 0, 'id' => 1, 'phrase' => 1, 'phrase_count' => 1,
                                                           'result_count' => array( '$cond' => array(
                                                               'if'   => array( '$gt' => array( '$phrase_count', 0 ) ),
                                                               'then' => array( '$divide' => array( '$result_count', '$phrase_count' ) ),
                                                               'else' => 0 ) ) ) ),
                               array( '$sort' => $sorts[self::sortKey( $sort )] ) );
            if ( $offset > 0 )
                $pipeline[] = array( '$skip' => (int)$offset );
            if ( $limit > 0 )
                $pipeline[] = array( '$limit' => (int)$limit );
            return $db->aggregate( 'ezsearch_search_phrase', $pipeline );
        }
        return $db->arrayQuery( 'SELECT phrase_count, result_count / phrase_count AS result_count, id, phrase
                                   FROM ezsearch_search_phrase' . self::whereSQL( $db, $search, $show ) . self::orderSQL( $sort ),
                                array( 'offset' => (int)$offset, 'limit' => (int)$limit ) );
    }

    /**
     * Which siteaccesses record their searches: a phrase is only counted when the siteaccess the visitor searches
     * in has site.ini [SearchSettings] LogSearchStats=enabled, and it is disabled by default.
     *
     * @return array enabled (siteaccess names), checked (how many were looked at)
     */
    public static function loggingSiteAccesses()
    {
        $ini = \eZINI::instance();
        $names = $ini->hasVariable( 'SiteAccessSettings', 'AvailableSiteAccessList' )
               ? (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' )
               : array();
        $enabled = array();
        $checked = 0;
        foreach ( array_unique( $names ) as $name )
        {
            if ( !is_string( $name ) || $name === '' )
                continue;
            $siteIni = \eZSiteAccess::getIni( $name, 'site.ini' );
            if ( !$siteIni )
                continue;
            $checked++;
            if ( $siteIni->hasVariable( 'SearchSettings', 'LogSearchStats' ) && $siteIni->variable( 'SearchSettings', 'LogSearchStats' ) === 'enabled' )
                $enabled[] = $name;
        }
        return array( 'enabled' => $enabled, 'checked' => $checked );
    }

    /**
     * The figures of the overview: phrases, searches, phrases and searches that found nothing.
     *
     * @return array phrases, searches, phrases_none, searches_none
     */
    public static function summary( $db )
    {
        if ( $db->databaseName() === 'mongo' )
        {
            $rows = $db->aggregate( 'ezsearch_search_phrase', array( array( '$group' => array(
                '_id' => null,
                'phrases' => array( '$sum' => 1 ),
                'searches' => array( '$sum' => '$phrase_count' ),
                'phrases_none' => array( '$sum' => array( '$cond' => array( array( '$eq' => array( '$result_count', 0 ) ), 1, 0 ) ) ),
                'searches_none' => array( '$sum' => array( '$cond' => array( array( '$eq' => array( '$result_count', 0 ) ), '$phrase_count', 0 ) ) ) ) ) ) );
            $row = !empty( $rows ) ? $rows[0] : array();
        }
        else
        {
            $rows = $db->arrayQuery( 'SELECT count(*) AS phrases, SUM( phrase_count ) AS searches,
                                             SUM( CASE WHEN result_count = 0 THEN 1 ELSE 0 END ) AS phrases_none,
                                             SUM( CASE WHEN result_count = 0 THEN phrase_count ELSE 0 END ) AS searches_none
                                        FROM ezsearch_search_phrase' );
            $row = !empty( $rows ) ? $rows[0] : array();
        }
        $summary = array();
        foreach ( array( 'phrases', 'searches', 'phrases_none', 'searches_none' ) as $key )
            $summary[$key] = isset( $row[$key] ) ? (int)$row[$key] : 0;
        return $summary;
    }
}

}
