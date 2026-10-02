<?php
/**
 * The code of kernel/infocollector/overview.php, moved into a class (#207 stage 1). The file kernel/infocollector/overview.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
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

class Overview extends \Exponential\Runnable\ModuleView
{
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


        if( $module->isCurrentAction( 'RemoveObjectCollection' ) && $http->hasPostVariable( 'ObjectIDArray' ) )
        {
            $objectIDArray = $http->postVariable( 'ObjectIDArray' );
            $http->setSessionVariable( 'ObjectIDArray', $objectIDArray );

            $collections = 0;

            foreach( $objectIDArray as $objectID )
            {
                $collections += \eZInformationCollection::fetchCollectionCountForObject( $objectID );
            }

            $tpl = \eZTemplate::factory();
            $tpl->setVariable( 'module', $module );
            $tpl->setVariable( 'collections', $collections );
            $tpl->setVariable( 'remove_type', 'objects' );

            $Result = array();
            $Result['content'] = $tpl->fetch( 'design:infocollector/confirmremoval.tpl' );
            $Result['path'] = array( array( 'url' => false,
                                            'text' => \ezpI18n::tr( 'kernel/infocollector', 'Collected information' ) ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }


        if( $module->isCurrentAction( 'ConfirmRemoval' ) )
        {

            $objectIDArray = $http->sessionVariable( 'ObjectIDArray' );

            if( is_array( $objectIDArray) )
            {
                foreach( $objectIDArray as $objectID )
                {
                    $auditCount = class_exists( 'expAuditHook' ) ? (int)\eZInformationCollection::fetchCollectionsCount( $objectID ) : 0;
                    \eZInformationCollection::removeContentObject( $objectID );
                    // Audit (doc/bc/6.0/audit.md, data.infocollection.remove): all of an object's collections
                    if ( class_exists( 'expAuditHook' ) )
                        \expAuditHook::emit( 'data.infocollection.remove', array( 'object' => array( 'type' => 'collection', 'id' => 'all' ),
                            'target' => \expAuditHook::object( (int)$objectID ), 'verb' => 'remove', 'before' => array( 'count' => $auditCount ) ) );
                }
            }
        }


        // The sizes on offer are configured, not written here; the preference holds
        // the position in that list, which is what it has always held.
        list( $limit, $limitChoice, $limitChoices ) = \expAdminPagination::chosen( 'infocollector/overview', 'admin_infocollector_list_limit' );


        $db = \eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
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
        }
        else
        {
        $objects = $db->arrayQuery( 'SELECT DISTINCT ezcontentobject.id AS contentobject_id,
                                                     ezcontentobject.name,
                                                     ezcontentobject_tree.main_node_id,
                                                     ezcontentclass.serialized_name_list,
                                                     ezcontentclass.identifier AS class_identifier
                                     FROM ezcontentobject,
                                          ezcontentobject_tree,
                                          ezcontentclass
                                     WHERE ezcontentobject_tree.contentobject_id = ezcontentobject.id
                                           AND ezcontentobject.contentclass_id = ezcontentclass.id
                                           AND ezcontentclass.version = ' . \eZContentClass::VERSION_STATUS_DEFINED . '
                                           AND ezcontentobject.id IN
                                           ( SELECT DISTINCT ezinfocollection.contentobject_id FROM ezinfocollection )
                                     ORDER BY ezcontentobject.name ASC',
                                     array( 'limit'  => (int)$limit,
                                            'offset' => (int)$offset ) );

        $infoCollectorObjectsQuery = $db->arrayQuery( 'SELECT COUNT( DISTINCT ezinfocollection.contentobject_id ) as count
                                                       FROM ezinfocollection,
                                                            ezcontentobject,
                                                            ezcontentobject_tree
                                                       WHERE
                                                            ezinfocollection.contentobject_id=ezcontentobject.id
                                                            AND ezinfocollection.contentobject_id=ezcontentobject_tree.contentobject_id' );
        $numberOfInfoCollectorObjects = 0;

        if ( $infoCollectorObjectsQuery )
        {
            $numberOfInfoCollectorObjects = $infoCollectorObjectsQuery[0]['count'];
        }
        }

        foreach ( array_keys( $objects ) as $i )
        {
            $firstCollections = \eZInformationCollection::fetchCollectionsList( (int)$objects[$i]['contentobject_id'], /* object id */
                                                                               false, /* creator id */
                                                                               false, /* user identifier */
                                                                               array( 'limit' => 1, 'offset' => 0 ), /* limitArray */
                                                                               array( 'created', true ), /* sortArray */
                                                                               false  /* asObject */
                                                                             );
            $objects[$i]['first_collection'] = $firstCollections[0]['created'];

            $lastCollections = \eZInformationCollection::fetchCollectionsList( (int)$objects[$i]['contentobject_id'], /* object id */
                                                                              false, /* creator id */
                                                                              false, /* user identifier */
                                                                              array( 'limit' => 1, 'offset' => 0 ), /* limitArray */
                                                                              array( 'created', false ), /* sortArray */
                                                                              false  /* asObject */
                                                                            );
            $objects[$i]['last_collection'] = $lastCollections[0]['created'];

            $objects[$i]['class_name'] = \eZContentClassNameList::nameFromSerializedString( $objects[$i]['serialized_name_list'] );
            $objects[$i]['collections']= \eZInformationCollection::fetchCollectionCountForObject( $objects[$i]['contentobject_id'] );
        }

        $viewParameters = array( 'offset' => $offset );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'module', $module );
        $tpl->setVariable( 'limit', $limit );
        $tpl->setVariable( 'limit_choices', $limitChoices );
        $tpl->setVariable( 'limit_choice', $limitChoice );
        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'object_array', $objects );
        $tpl->setVariable( 'object_count', $numberOfInfoCollectorObjects );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:infocollector/overview.tpl' );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/infocollector', 'Collected information' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
