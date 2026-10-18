<?php
/**
 * exphelp/search (doc/features/6.0/help-system.md, "Search"): GET q, audience, category, type, offset, limit.
 * format=json answers the client's instant search with expHelpSearch::search()'s array; otherwise the results page
 * design:exphelp/search.tpl (help_search: that array; help_filters: the filters given; help_audiences, help_categories).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Search extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Module = $scope['Params']['Module'];
        if ( !\expHelp::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $request = self::request( $_GET );
        $index = \expHelpDocumentIndex::instance();
        $search = new \expHelpSearch( $index );
        $result = $search->search( $request['q'], $request['filters'], $request['limit'], $request['offset'] );
        $result = self::withContentTopics( $result, $request );

        if ( $request['format'] === 'json' )
        {
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Cache-Control: private, no-store' );
            header( 'X-Content-Type-Options: nosniff' );
            echo json_encode( $result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE );
            \eZExecution::cleanExit();
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'help_i18n_json', \expHelp::clientStringsJson() );
        $tpl->setVariable( 'help_search', $result );
        $tpl->setVariable( 'help_filters', $request['filters'] );
        $tpl->setVariable( 'help_audiences', $index->audiences() );
        $tpl->setVariable( 'help_categories', $index->categories() );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/search.tpl' );
        $Result['path'] = array( array( 'url' => 'exphelp/dashboard', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Search' ) ) );
        return $Result;
    }

    /**
     * Help topics maintained as content (doc/features/6.0/help-content.md) the user may see, put before the pages of
     * doc/ on the first page of results; the total counts them too. Nothing when help as content is not installed.
     *
     * @param array $result expHelpSearch::search()'s array
     * @param array $request self::request()
     * @return array
     */
    public static function withContentTopics( array $result, array $request )
    {
        if ( $request['q'] === '' || !class_exists( 'expHelpContentProvider' ) || !\expHelpContentProvider::isEnabled() )
            return $result;
        try
        {
            $topics = \expHelpContentProvider::instance()->search( $request['q'], $request['filters'], $request['limit'] );
        }
        catch ( \Exception $e )
        {
            \eZDebug::writeWarning( 'Help topics could not be searched: ' . $e->getMessage(), __METHOD__ );
            return $result;
        }
        if ( !$topics['hits'] )
            return $result;
        if ( (int)$request['offset'] === 0 )
        {
            $hits = array_slice( array_merge( $topics['hits'], isset( $result['hits'] ) ? $result['hits'] : array() ), 0, $request['limit'] );
            $result['hits'] = $result['results'] = $hits;
        }
        $result['total'] = ( isset( $result['total'] ) ? (int)$result['total'] : 0 ) + $topics['total'];
        $result['topics_total'] = $topics['total'];
        return $result;
    }

    /**
     * The search parameters of a request, bounded.
     *
     * @param array $get
     * @return array array( 'q' => string, 'filters' => array, 'limit' => int, 'offset' => int, 'format' => html|json )
     */
    public static function request( array $get )
    {
        $text = function ( $key, $max ) use ( $get ) {
            return isset( $get[$key] ) && is_string( $get[$key] ) ? mb_substr( trim( $get[$key] ), 0, $max ) : '';
        };
        $filters = array();
        foreach ( array( 'audience', 'category', 'type' ) as $key )
        {
            $value = $text( $key, 64 );
            // one value or a comma separated list (audience=admin,developer); expHelpSearch bounds each value
            if ( $value !== '' && preg_match( '/^[a-z0-9_.-]+(?:,[a-z0-9_.-]+){0,9}$/', $value ) )
                $filters[$key] = $value;
        }
        $limit = isset( $get['limit'] ) ? (int)$get['limit'] : (int)\expHelp::setting( 'SearchSettings', 'ResultLimit', 20 );
        return array( 'q' => $text( 'q', 200 ),
                      'filters' => $filters,
                      'limit' => max( 1, min( 50, $limit ) ),
                      'offset' => max( 0, min( 10000, isset( $get['offset'] ) ? (int)$get['offset'] : 0 ) ),
                      'format' => $text( 'format', 8 ) === 'json' || self::acceptsJson() ? 'json' : 'html' );
    }

    /**
     * Whether the client asks for JSON first (Accept: application/json, as the help script sends); a browser's
     * Accept (text/html first, or any type) gets the page.
     *
     * @param array|null $server default $_SERVER
     * @return bool
     */
    public static function acceptsJson( ?array $server = null )
    {
        $server = $server === null ? $_SERVER : $server;
        $accept = isset( $server['HTTP_ACCEPT'] ) && is_string( $server['HTTP_ACCEPT'] ) ? strtolower( $server['HTTP_ACCEPT'] ) : '';
        return (bool)preg_match( '/^\s*application\/json\s*(?:[;,]|$)/', $accept );
    }
}

}
