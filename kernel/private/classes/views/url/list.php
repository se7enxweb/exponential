<?php
/**
 * The code of kernel/url/list.php, moved into a class (#207 stage 1). The file kernel/url/list.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 * User guide of the page: doc/guides/urls-and-aliases.md
 */
/*
 * The original header of kernel/url/list.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Url
{

class ListView extends \Exponential\Runnable\ModuleView
{
    /** The lists the page offers: the address after url/list/. */
    const VIEW_MODES = array( 'all', 'valid', 'invalid', 'unchecked' );

    /** The orders the page offers, the first is the default; eZURL::listOrderSQL() knows them all. */
    const SORTS = array( 'address', 'checked', 'modified' );

    /** How many objects using a URL its row names; the rest are counted and on url/view. */
    const USAGE_SHOWN = 3;

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $ViewMode = self::viewMode( $Params['ViewMode'] );
        $http = \eZHTTPTool::instance();

        // The sizes come from admininterface.ini [PaginationSettings] (10, 25 and 50 unless configured); the
        // preference holds the position in that list, which is what it has always held.
        list( $limit, $limitChoice, $limitChoices ) = \expAdminPagination::chosen( 'url/list', 'admin_url_list_limit' );

        $offset = $Params['Offset'];
        if ( !is_numeric( $offset ) || $offset < 0 )
        {
            $offset = 0;
        }
        $offset = (int)$offset;

        $userParameters = isset( $Params['UserParameters'] ) ? (array)$Params['UserParameters'] : array();
        $sort = self::sortKey( isset( $userParameters['sort'] ) ? $userParameters['sort'] : '' );
        $search = self::searchText( $http->hasGetVariable( 'q' ) ? $http->getVariable( 'q' ) : '' );

        $feedback = false;
        if ( $Module->isCurrentAction( 'SetValid' ) || $Module->isCurrentAction( 'SetInvalid' ) )
        {
            $setValid = $Module->isCurrentAction( 'SetValid' );
            $urlSelection = self::selectedIDs( $Module->hasActionParameter( 'URLSelection' ) ? $Module->actionParameter( 'URLSelection' ) : array() );
            if ( $urlSelection )
            {
                \eZURL::setIsValid( $urlSelection, $setValid );
                $feedback = array( 'type' => $setValid ? 'set_valid' : 'set_invalid', 'count' => count( $urlSelection ) );
            }
            else
            {
                $feedback = array( 'type' => 'none_selected', 'count' => 0 );
            }
        }

        $filter = self::listFilter( $ViewMode );
        $listParameters = array_merge( $filter, array( 'offset' => $offset, 'limit' => $limit, 'only_published' => true,
                                                       'search' => $search !== '' ? $search : null, 'sort' => $sort ) );
        $countParameters = array_merge( $filter, array( 'only_published' => true, 'search' => $search !== '' ? $search : null ) );

        $listCount = (int)\eZURL::fetchListCount( $countParameters );
        if ( $offset > 0 && $offset >= $listCount )
        {
            // a filter or a change of state left the page behind the end of the list
            $offset = 0;
            $listParameters['offset'] = 0;
        }
        $list = \eZURL::fetchList( $listParameters );

        $viewParameters = array( 'offset' => $offset, 'limit'  => $limit );
        if ( $sort !== self::SORTS[0] )
            $viewParameters['sort'] = $sort;

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'url_list', $list );
        $tpl->setVariable( 'url_list_count', $listCount );
        $tpl->setVariable( 'view_mode', $ViewMode );
        $tpl->setVariable( 'limit', $limit );
        $tpl->setVariable( 'limit_choices', $limitChoices );
        $tpl->setVariable( 'limit_choice', $limitChoice );
        $tpl->setVariable( 'url_sort', $sort );
        $tpl->setVariable( 'url_sorts', self::SORTS );
        $tpl->setVariable( 'url_search', $search );
        $tpl->setVariable( 'url_search_suffix', $search !== '' ? '?q=' . rawurlencode( $search ) : '' );
        $tpl->setVariable( 'url_summary', self::summary() );
        $tpl->setVariable( 'url_usage', self::usageFromDatabase( $list ) );
        $tpl->setVariable( 'url_feedback', $feedback );
        $checks = array();
        foreach ( $list as $url )
        {
            if ( is_object( $url ) )
                $checks[(int)$url->attribute( 'id' )] = array( 'kind' => self::checkKind( $url->attribute( 'url' ) ),
                                                               'openable' => self::isOpenable( $url->attribute( 'url' ) ) );
        }
        $tpl->setVariable( 'url_checks', $checks );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:url/list.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/url', 'URL' ) ),
                                 array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/url', 'List' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * The list asked for by the address, 'all' for anything the page does not offer.
     *
     * @param mixed $value
     * @return string
     */
    public static function viewMode( $value )
    {
        return in_array( $value, self::VIEW_MODES, true ) ? $value : 'all';
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
     * The search text: one line, trimmed, at most 200 characters.
     *
     * @param mixed $value
     * @return string
     */
    public static function searchText( $value )
    {
        if ( !is_string( $value ) )
            return '';
        // text that is not UTF-8 is not searched for at all
        $value = preg_replace( '/[\x00-\x1F\x7F]+/u', ' ', $value );
        if ( !is_string( $value ) )
            return '';
        $value = trim( $value );
        return function_exists( 'mb_substr' ) ? mb_substr( $value, 0, 200 ) : substr( $value, 0, 200 );
    }

    /**
     * The eZURL::fetchList() parameters of a list.
     *
     * @param string $viewMode
     * @return array
     */
    public static function listFilter( $viewMode )
    {
        switch ( $viewMode )
        {
            case 'valid':     return array( 'is_valid' => true );
            case 'invalid':   return array( 'is_valid' => false );
            case 'unchecked': return array( 'is_valid' => null, 'last_checked' => 'never' );
        }
        return array( 'is_valid' => null );
    }

    /**
     * The ids of a selection posted by the page: whole positive numbers only, each once.
     *
     * @param mixed $selection
     * @return int[]
     */
    public static function selectedIDs( $selection )
    {
        $ids = array();
        foreach ( (array)$selection as $id )
        {
            if ( is_scalar( $id ) && ctype_digit( (string)$id ) && (int)$id > 0 )
                $ids[(int)$id] = (int)$id;
        }
        return array_values( $ids );
    }

    /**
     * How the link check treats an address (expLinkCheck::kind()): 'web' (http, https and ftp are requested),
     * 'mailto' (the mail domain is looked up), 'content' (a rich text link to a node or object is judged by
     * whether its target is published and visible), 'file' (never tested), 'internal' (a path of this site,
     * looked up as an alias or on SiteURL[]) or 'other' (another scheme, looked up as a path like 'internal', so
     * usually invalid).
     *
     * @param string $url
     * @return string
     */
    public static function checkKind( $url )
    {
        $kind = \expLinkCheck::kind( $url );
        if ( $kind === 'internal' && preg_match( '#^[a-z][a-z0-9+.-]*:#i', trim( (string)$url ) ) )
            return 'other';
        return $kind;
    }

    /**
     * Whether the page may make the address a link to open it: only web and mail addresses and paths, never a
     * javascript:, data: or file: address that an editor's link could carry.
     *
     * @param string $url
     * @return bool
     */
    public static function isOpenable( $url )
    {
        return in_array( self::checkKind( $url ), array( 'web', 'mailto', 'internal' ), true );
    }

    /**
     * The figures of the overview: every published address, valid, invalid, never checked, and the time of the
     * most recent check. Four counts and one maximum, none of which reads a URL's text.
     *
     * @return array
     */
    public static function summary()
    {
        $summary = array( 'all'       => (int)\eZURL::fetchListCount( array( 'only_published' => true ) ),
                          'valid'     => (int)\eZURL::fetchListCount( array( 'only_published' => true, 'is_valid' => true ) ),
                          'invalid'   => (int)\eZURL::fetchListCount( array( 'only_published' => true, 'is_valid' => false ) ),
                          'unchecked' => (int)\eZURL::fetchListCount( array( 'only_published' => true, 'last_checked' => 'never' ) ),
                          'last_check' => 0 );
        $db = \eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
        {
            $rows = $db->aggregate( 'ezurl', array( array( '$group' => array( '_id' => null, 'last' => array( '$max' => '$last_checked' ) ) ) ) );
            $summary['last_check'] = !empty( $rows ) ? (int)$rows[0]['last'] : 0;
        }
        else
        {
            $rows = $db->arrayQuery( 'SELECT MAX( last_checked ) AS last_check FROM ezurl' );
            $summary['last_check'] = !empty( $rows ) ? (int)$rows[0]['last_check'] : 0;
        }
        return $summary;
    }

    /**
     * Which published objects use the URLs of a page: two queries for the whole page (none on MongoDB, where the
     * row links to url/view instead).
     *
     * @param \eZURL[] $urls
     * @return array see usage()
     */
    public static function usageFromDatabase( $urls )
    {
        $ids = array();
        foreach ( (array)$urls as $url )
        {
            if ( is_object( $url ) )
                $ids[] = (int)$url->attribute( 'id' );
        }
        $db = \eZDB::instance();
        if ( !$ids || $db->databaseName() === 'mongo' )
            return array();

        $versionColumn = \eZPersistentObject::getShortAttributeName( $db, \eZURLObjectLink::definition(), 'contentobject_attribute_version' );
        $rows = $db->arrayQuery( "SELECT DISTINCT ezurl_object_link.url_id AS url_id, ezcontentobject.id AS object_id, ezcontentobject.name AS name
                                    FROM ezurl_object_link, ezcontentobject_attribute, ezcontentobject_version, ezcontentobject
                                   WHERE ezurl_object_link.url_id IN ( " . implode( ', ', $ids ) . " )
                                     AND ezcontentobject_attribute.id             = ezurl_object_link.contentobject_attribute_id
                                     AND ezcontentobject_attribute.version        = ezurl_object_link.$versionColumn
                                     AND ezcontentobject_version.contentobject_id = ezcontentobject_attribute.contentobject_id
                                     AND ezcontentobject_version.version          = ezcontentobject_attribute.version
                                     AND ezcontentobject_version.status           = " . \eZContentObjectVersion::STATUS_PUBLISHED . "
                                     AND ezcontentobject.id                       = ezcontentobject_attribute.contentobject_id
                                ORDER BY ezcontentobject.name, ezcontentobject.id" );
        $objectIDs = array();
        foreach ( $rows as $row )
            $objectIDs[(int)$row['object_id']] = (int)$row['object_id'];
        $nodeRows = $objectIDs
                  ? $db->arrayQuery( "SELECT contentobject_id, main_node_id FROM ezcontentobject_tree
                                       WHERE contentobject_id IN ( " . implode( ', ', $objectIDs ) . " ) AND node_id = main_node_id" )
                  : array();
        return self::usage( $ids, $rows, $nodeRows, self::USAGE_SHOWN );
    }

    /**
     * The objects using each URL, from the rows of usageFromDatabase(): for every URL id of the page its count
     * and the first $shown objects, each with its id, name and main node (0 without one).
     *
     * @param int[] $urlIDs
     * @param array $rows url_id, object_id, name
     * @param array $nodeRows contentobject_id, main_node_id
     * @param int $shown
     * @return array url id => array( 'count' => int, 'objects' => array( array( 'id', 'name', 'node_id' ) ) )
     */
    public static function usage( array $urlIDs, array $rows, array $nodeRows, $shown = self::USAGE_SHOWN )
    {
        $nodes = array();
        foreach ( $nodeRows as $row )
            $nodes[(int)$row['contentobject_id']] = (int)$row['main_node_id'];

        $usage = array();
        foreach ( $urlIDs as $id )
            $usage[(int)$id] = array( 'count' => 0, 'objects' => array() );

        $seen = array();
        foreach ( $rows as $row )
        {
            $urlID = (int)$row['url_id'];
            $objectID = (int)$row['object_id'];
            if ( !isset( $usage[$urlID] ) || isset( $seen[$urlID][$objectID] ) )
                continue;
            $seen[$urlID][$objectID] = true;
            $usage[$urlID]['count']++;
            if ( count( $usage[$urlID]['objects'] ) < $shown )
                $usage[$urlID]['objects'][] = array( 'id' => $objectID,
                                                     'name' => (string)$row['name'],
                                                     'node_id' => isset( $nodes[$objectID] ) ? $nodes[$objectID] : 0 );
        }
        return $usage;
    }
}

}
