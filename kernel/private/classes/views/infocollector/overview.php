<?php
/**
 * The code of kernel/infocollector/overview.php, moved into a class (#207 stage 1). The file kernel/infocollector/overview.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 * User guide: doc/guides/collected-information.md
 */
/*
 * The original header of kernel/infocollector/overview.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Infocollector
{

/**
 * Setup > Collected information (infocollector/overview): the objects (forms, polls, feedback pages) that have
 * collected information, with figures, a search by name, sorting, paging, the collections of the last 30 days per
 * form, a CSV export per form (infocollector/export) and, when nothing or little has been collected, the objects
 * that can collect. Removal goes through infocollector/confirmremoval.tpl as before.
 */
class Overview extends \Exponential\Runnable\ModuleView
{
    /** sort keys, mapped to the column alias the list orders by */
    const SORT_COLUMNS = array( 'name' => 'name', 'collections' => 'collections', 'last' => 'last_collection', 'first' => 'first_collection', 'class' => 'class_identifier' );

    /** the days "recent" means */
    const RECENT_DAYS = 30;

    /** objects that can collect, listed at most */
    const CANDIDATES_SHOWN = 10;

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $module = $Params['Module'];
        $offset = $Params['Offset'];

        if( !is_numeric( $offset ) )
        {
            $offset = 0;
        }
        $offset = max( 0, (int)$offset );


        if( $module->isCurrentAction( 'RemoveObjectCollection' ) && $http->hasPostVariable( 'ObjectIDArray' ) )
        {
            $objectIDArray = $http->postVariable( 'ObjectIDArray' );
            $http->setSessionVariable( 'ObjectIDArray', $objectIDArray );

            $collections = 0;
            $objects = array();

            foreach( $objectIDArray as $objectID )
            {
                $count = \eZInformationCollection::fetchCollectionCountForObject( $objectID );
                $collections += $count;
                $object = \eZContentObject::fetch( (int)$objectID );
                if ( $object )
                    $objects[] = array( 'id' => (int)$objectID, 'name' => $object->attribute( 'name' ), 'collections' => (int)$count );
            }

            $tpl = \eZTemplate::factory();
            $tpl->setVariable( 'module', $module );
            $tpl->setVariable( 'collections', $collections );
            $tpl->setVariable( 'remove_type', 'objects' );
            $tpl->setVariable( 'remove_objects', $objects );

            $Result = array();
            $Result['content'] = $tpl->fetch( 'design:infocollector/confirmremoval.tpl' );
            $Result['path'] = array( array( 'url' => false,
                                            'text' => \ezpI18n::tr( 'kernel/infocollector', 'Collected information' ) ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $feedback = false;
        if( $module->isCurrentAction( 'RemoveObjectCollection' ) )
        {
            $feedback = array( 'type' => 'none_selected' );
        }

        if( $module->isCurrentAction( 'ConfirmRemoval' ) )
        {

            $objectIDArray = $http->sessionVariable( 'ObjectIDArray' );

            if( is_array( $objectIDArray) )
            {
                $removed = 0;
                foreach( $objectIDArray as $objectID )
                {
                    $auditCount = (int)\eZInformationCollection::fetchCollectionsCount( $objectID );
                    $removed += $auditCount;
                    \eZInformationCollection::removeContentObject( $objectID );
                    // Audit (doc/bc/6.0/audit.md, data.infocollection.remove): all of an object's collections
                    if ( class_exists( 'expAuditHook' ) )
                        \expAuditHook::emit( 'data.infocollection.remove', array( 'object' => array( 'type' => 'collection', 'id' => 'all' ),
                            'target' => \expAuditHook::object( (int)$objectID ), 'verb' => 'remove', 'before' => array( 'count' => $auditCount ) ) );
                }
                $feedback = array( 'type' => 'removed', 'objects' => count( $objectIDArray ), 'collections' => $removed );
                $http->removeSessionVariable( 'ObjectIDArray' );
            }
        }

        // The search lives in the viewer's session, like the other list filters of the admin.
        $search = $http->hasSessionVariable( 'eZInfoCollectorSearch' ) ? self::cleanSearch( $http->sessionVariable( 'eZInfoCollectorSearch' ) ) : '';
        if ( $http->hasPostVariable( 'InfoFilterButton' ) )
            $search = self::cleanSearch( $http->hasPostVariable( 'InfoSearch' ) ? $http->postVariable( 'InfoSearch' ) : '' );
        if ( $http->hasPostVariable( 'InfoClearSearchButton' ) )
            $search = '';
        if ( $http->hasPostVariable( 'InfoFilterButton' ) || $http->hasPostVariable( 'InfoClearSearchButton' ) )
        {
            $http->setSessionVariable( 'eZInfoCollectorSearch', $search );
            $offset = 0;
        }

        $userParameters = isset( $scope['Params']['UserParameters'] ) ? (array)$scope['Params']['UserParameters'] : array();
        $sortBy = self::cleanSort( isset( $userParameters['sortby'] ) ? $userParameters['sortby'] : '' );
        $order = self::cleanOrder( isset( $userParameters['order'] ) ? $userParameters['order'] : '', self::defaultOrder( $sortBy ) );

        // The sizes on offer are configured, not written here; the preference holds
        // the position in that list, which is what it has always held.
        list( $limit, $limitChoice, $limitChoices ) = \expAdminPagination::chosen( 'infocollector/overview', 'admin_infocollector_list_limit' );


        $db = \eZDB::instance();
        $now = time();
        $canFilter = $db->databaseName() !== 'mongo';
        if ( !$canFilter )
        {
            $objects = $db->aggregate( 'ezinfocollection', [
                [ '$group'  => [ '_id' => '$contentobject_id' ] ],
                [ '$lookup' => [
                    'from'         => 'ezcontentobject',
                    'localField'   => '_id',
                    'foreignField' => 'id',
                    'as'           => '_obj',
                ] ],
                [ '$unwind' => '$_obj' ],
                [ '$lookup' => [
                    'from'     => 'ezcontentobject_tree',
                    'let'      => [ 'obj_id' => '$_id' ],
                    'pipeline' => [ [ '$match' => [ '$expr' => [ '$and' => [
                        [ '$eq' => [ '$contentobject_id', '$$obj_id' ] ],
                        [ '$eq' => [ '$node_id', '$main_node_id' ] ],
                    ] ] ] ] ],
                    'as'       => '_tree',
                ] ],
                [ '$unwind' => '$_tree' ],
                [ '$lookup' => [
                    'from'     => 'ezcontentclass',
                    'let'      => [ 'class_id' => '$_obj.contentclass_id' ],
                    'pipeline' => [ [ '$match' => [ '$expr' => [ '$and' => [
                        [ '$eq' => [ '$id', '$$class_id' ] ],
                        [ '$eq' => [ '$version', 0 ] ],
                    ] ] ] ] ],
                    'as'       => '_class',
                ] ],
                [ '$unwind' => '$_class' ],
                [ '$sort'   => [ '_obj.name' => 1 ] ],
                [ '$skip'   => (int) $offset ],
                [ '$limit'  => (int) $limit ],
                [ '$project' => [
                    '_id'                  => 0,
                    'contentobject_id'     => '$_id',
                    'name'                 => '$_obj.name',
                    'main_node_id'         => '$_tree.main_node_id',
                    'serialized_name_list' => '$_class.serialized_name_list',
                    'class_identifier'     => '$_class.identifier',
                ] ],
            ] );
            $numberOfInfoCollectorObjects = 0;
            $countRows = $db->aggregate( 'ezinfocollection', [
                [ '$group' => [ '_id' => '$contentobject_id' ] ],
                [ '$count' => 'count' ],
            ] );
            if ( !empty( $countRows ) )
                $numberOfInfoCollectorObjects = (int) $countRows[0]['count'];

            foreach ( array_keys( $objects ) as $i )
            {
                $firstCollections = \eZInformationCollection::fetchCollectionsList( (int)$objects[$i]['contentobject_id'], false, false,
                                                                                   array( 'limit' => 1, 'offset' => 0 ), array( 'created', true ), false );
                $objects[$i]['first_collection'] = $firstCollections[0]['created'];
                $lastCollections = \eZInformationCollection::fetchCollectionsList( (int)$objects[$i]['contentobject_id'], false, false,
                                                                                  array( 'limit' => 1, 'offset' => 0 ), array( 'created', false ), false );
                $objects[$i]['last_collection'] = $lastCollections[0]['created'];
                $objects[$i]['collections'] = \eZInformationCollection::fetchCollectionCountForObject( $objects[$i]['contentobject_id'] );
            }
            $summary = false;
            $recent = array();
            $search = '';
            $sortBy = 'name';
            $order = 'asc';
        }
        else
        {
            // One query for the page with its counts and dates, instead of three per row.
            $where = self::searchCondition( $search );
            $direction = $order === 'desc' ? 'DESC' : 'ASC';
            $orderBy = self::SORT_COLUMNS[$sortBy] . ' ' . $direction . ( $sortBy !== 'name' ? ', name ASC' : '' );
            $objects = $db->arrayQuery( 'SELECT ezcontentobject.id AS contentobject_id,
                                                ezcontentobject.name AS name,
                                                ezcontentobject_tree.main_node_id AS main_node_id,
                                                ezcontentclass.serialized_name_list AS serialized_name_list,
                                                ezcontentclass.identifier AS class_identifier,
                                                ic.collections AS collections,
                                                ic.first_collection AS first_collection,
                                                ic.last_collection AS last_collection
                                         FROM ezcontentobject,
                                              ezcontentobject_tree,
                                              ezcontentclass,
                                              ( SELECT contentobject_id, COUNT( * ) AS collections, MIN( created ) AS first_collection, MAX( created ) AS last_collection
                                                FROM ezinfocollection GROUP BY contentobject_id ) ic
                                         WHERE ic.contentobject_id = ezcontentobject.id
                                               AND ezcontentobject_tree.contentobject_id = ezcontentobject.id
                                               AND ezcontentobject_tree.node_id = ezcontentobject_tree.main_node_id
                                               AND ezcontentobject.contentclass_id = ezcontentclass.id
                                               AND ezcontentclass.version = ' . \eZContentClass::VERSION_STATUS_DEFINED . $where . '
                                         ORDER BY ' . $orderBy,
                                         array( 'limit'  => (int)$limit,
                                                'offset' => (int)$offset ) );

            $infoCollectorObjectsQuery = $db->arrayQuery( 'SELECT COUNT( DISTINCT ezinfocollection.contentobject_id ) as count
                                                           FROM ezinfocollection,
                                                                ezcontentobject,
                                                                ezcontentobject_tree,
                                                                ezcontentclass
                                                           WHERE
                                                                ezinfocollection.contentobject_id=ezcontentobject.id
                                                                AND ezinfocollection.contentobject_id=ezcontentobject_tree.contentobject_id
                                                                AND ezcontentobject_tree.node_id = ezcontentobject_tree.main_node_id
                                                                AND ezcontentobject.contentclass_id = ezcontentclass.id
                                                                AND ezcontentclass.version = ' . \eZContentClass::VERSION_STATUS_DEFINED . $where );
            $numberOfInfoCollectorObjects = 0;

            if ( $infoCollectorObjectsQuery )
            {
                $numberOfInfoCollectorObjects = (int)$infoCollectorObjectsQuery[0]['count'];
            }

            $summary = self::summary( $now );
            $recent = self::recentCounts( array_map( function ( $row ) { return (int)$row['contentobject_id']; }, (array)$objects ), $now - self::RECENT_DAYS * 86400 );
        }

        foreach ( array_keys( $objects ) as $i )
        {
            $objects[$i]['class_name'] = \eZContentClassNameList::nameFromSerializedString( $objects[$i]['serialized_name_list'] );
            $objects[$i]['collections'] = (int)$objects[$i]['collections'];
            $objects[$i]['first_collection'] = (int)$objects[$i]['first_collection'];
            $objects[$i]['last_collection'] = (int)$objects[$i]['last_collection'];
            $id = (int)$objects[$i]['contentobject_id'];
            $objects[$i]['recent_collections'] = isset( $recent[$id] ) ? (int)$recent[$id] : 0;
        }

        // Objects that could collect but have nothing yet: what to look at when the list is empty or short.
        $candidates = array();
        $candidateCount = 0;
        if ( $canFilter && $search === '' && $offset === 0 && count( $objects ) < $limit )
            list( $candidates, $candidateCount ) = self::candidates( self::CANDIDATES_SHOWN );

        $viewParameters = array( 'offset' => $offset );
        if ( isset( $userParameters['sortby'] ) )
        {
            $viewParameters['sortby'] = $sortBy;
            $viewParameters['order'] = $order;
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'module', $module );
        $tpl->setVariable( 'limit', $limit );
        $tpl->setVariable( 'limit_choices', $limitChoices );
        $tpl->setVariable( 'limit_choice', $limitChoice );
        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'object_array', $objects );
        $tpl->setVariable( 'object_count', $numberOfInfoCollectorObjects );
        $tpl->setVariable( 'info_summary', $summary );
        $tpl->setVariable( 'info_search', $search );
        $tpl->setVariable( 'info_sort', $sortBy );
        $tpl->setVariable( 'info_order', $order );
        $tpl->setVariable( 'info_can_filter', $canFilter );
        $tpl->setVariable( 'info_feedback', $feedback );
        $tpl->setVariable( 'info_recent_days', self::RECENT_DAYS );
        $tpl->setVariable( 'info_candidates', $candidates );
        $tpl->setVariable( 'info_candidate_count', $candidateCount );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:infocollector/overview.tpl' );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/infocollector', 'Collected information' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * The figures above the list.
     *
     * @return array collections, objects (with collections, listed or not), listed is set by the caller,
     *               recent (last RECENT_DAYS days), week (last 7 days), latest (timestamp or 0)
     */
    public static function summary( $now )
    {
        $db = \eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT COUNT( * ) AS collections,
                                         COUNT( DISTINCT contentobject_id ) AS objects,
                                         MAX( created ) AS latest,
                                         SUM( CASE WHEN created > ' . ( (int)$now - self::RECENT_DAYS * 86400 ) . ' THEN 1 ELSE 0 END ) AS recent,
                                         SUM( CASE WHEN created > ' . ( (int)$now - 7 * 86400 ) . ' THEN 1 ELSE 0 END ) AS week
                                  FROM ezinfocollection' );
        $row = $rows ? $rows[0] : array();
        $unlisted = $db->arrayQuery( 'SELECT COUNT( DISTINCT contentobject_id ) AS count FROM ezinfocollection
                                      WHERE contentobject_id NOT IN ( SELECT contentobject_id FROM ezcontentobject_tree )' );
        return array(
            'collections' => isset( $row['collections'] ) ? (int)$row['collections'] : 0,
            'objects'     => isset( $row['objects'] ) ? (int)$row['objects'] : 0,
            'latest'      => isset( $row['latest'] ) ? (int)$row['latest'] : 0,
            'recent'      => isset( $row['recent'] ) ? (int)$row['recent'] : 0,
            'week'        => isset( $row['week'] ) ? (int)$row['week'] : 0,
            'unlisted'    => isset( $unlisted[0]['count'] ) ? (int)$unlisted[0]['count'] : 0,
        );
    }

    /**
     * Collections since a time, per object.
     *
     * @param int[] $objectIDs
     * @return array object id => count
     */
    public static function recentCounts( array $objectIDs, $since )
    {
        $objectIDs = array_values( array_filter( array_map( 'intval', $objectIDs ) ) );
        if ( !$objectIDs )
            return array();
        $rows = \eZDB::instance()->arrayQuery( 'SELECT contentobject_id, COUNT( * ) AS count FROM ezinfocollection
                                                WHERE created > ' . (int)$since . ' AND contentobject_id IN ( ' . implode( ', ', $objectIDs ) . ' )
                                                GROUP BY contentobject_id' );
        $result = array();
        foreach ( (array)$rows as $row )
            $result[(int)$row['contentobject_id']] = (int)$row['count'];
        return $result;
    }

    /**
     * Published objects whose class has an information collector attribute and that have collected nothing.
     *
     * @return array( array $objects (contentobject_id, name, main_node_id, class_name), int $count )
     */
    public static function candidates( $max )
    {
        $db = \eZDB::instance();
        $from = 'FROM ezcontentobject, ezcontentobject_tree, ezcontentclass
                 WHERE ezcontentobject_tree.contentobject_id = ezcontentobject.id
                       AND ezcontentobject_tree.node_id = ezcontentobject_tree.main_node_id
                       AND ezcontentobject.contentclass_id = ezcontentclass.id
                       AND ezcontentclass.version = ' . \eZContentClass::VERSION_STATUS_DEFINED . '
                       AND ezcontentobject.status = ' . \eZContentObject::STATUS_PUBLISHED . '
                       AND ezcontentobject.contentclass_id IN ( SELECT contentclass_id FROM ezcontentclass_attribute
                                                                WHERE is_information_collector = 1 AND version = ' . \eZContentClass::VERSION_STATUS_DEFINED . ' )
                       AND ezcontentobject.id NOT IN ( SELECT contentobject_id FROM ezinfocollection )';
        $count = $db->arrayQuery( 'SELECT COUNT( * ) AS count ' . $from );
        $rows = $db->arrayQuery( 'SELECT ezcontentobject.id AS contentobject_id, ezcontentobject.name AS name, ezcontentobject_tree.main_node_id AS main_node_id,
                                         ezcontentclass.serialized_name_list AS serialized_name_list, ezcontentclass.identifier AS class_identifier ' . $from . '
                                  ORDER BY ezcontentobject.name ASC', array( 'limit' => (int)$max, 'offset' => 0 ) );
        $result = array();
        foreach ( (array)$rows as $row )
        {
            $result[] = array( 'contentobject_id' => (int)$row['contentobject_id'], 'name' => (string)$row['name'],
                               'main_node_id' => (int)$row['main_node_id'], 'class_identifier' => (string)$row['class_identifier'],
                               'class_name' => \eZContentClassNameList::nameFromSerializedString( $row['serialized_name_list'] ) );
        }
        return array( $result, isset( $count[0]['count'] ) ? (int)$count[0]['count'] : 0 );
    }

    /** @return string " AND ..." for a search by object name, or '' */
    public static function searchCondition( $search )
    {
        $search = self::cleanSearch( $search );
        if ( $search === '' )
            return '';
        $like = \eZDB::instance()->escapeString( '%' . self::escapeLike( mb_strtolower( $search ) ) . '%' );
        return " AND LOWER( ezcontentobject.name ) LIKE '$like' ESCAPE '!'";
    }

    // ---- Pure helpers (tests/tests/kernel/classes/infocollector/InfoCollectorOverviewTest.php) -------------

    /** A search as typed, trimmed, without control characters, at most 100 characters. */
    public static function cleanSearch( $search )
    {
        if ( !is_scalar( $search ) )
            return '';
        $search = preg_replace( '/[\x00-\x1F\x7F]+/u', ' ', (string)$search );
        return mb_substr( trim( preg_replace( '/\s+/u', ' ', (string)$search ) ), 0, 100 );
    }

    /** Escapes LIKE's wildcards with "!" (the query says ESCAPE '!'). */
    public static function escapeLike( $text )
    {
        return str_replace( array( '!', '%', '_' ), array( '!!', '!%', '!_' ), (string)$text );
    }

    /** @return string a key of SORT_COLUMNS; 'name' for anything else */
    public static function cleanSort( $sort )
    {
        return is_string( $sort ) && isset( self::SORT_COLUMNS[$sort] ) ? $sort : 'name';
    }

    /** @return string 'asc' or 'desc' */
    public static function cleanOrder( $order, $default )
    {
        return $order === 'asc' || $order === 'desc' ? $order : $default;
    }

    /** Names and classes read A to Z; counts and dates newest or largest first. */
    public static function defaultOrder( $sort )
    {
        return in_array( $sort, array( 'collections', 'last', 'first' ), true ) ? 'desc' : 'asc';
    }
}

}
