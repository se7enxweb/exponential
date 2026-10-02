<?php
/**
 * The code of kernel/content/restore.php, moved into a class (#207 stage 1). The file kernel/content/restore.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/restore.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Content
{

class Restore extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $objectID = $Params['ObjectID'];
        $module = $Params['Module'];

        $object = \eZContentObject::fetch( $objectID );
        if ( !is_object( $object ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }
        if ( $object->attribute( 'status' ) != \eZContentObject::STATUS_ARCHIVED )
        {
            \eZDebug::writeError( "Object with ID " . (int)$objectID . " is not archived, cannot restore." );
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'trash' ) );
        }

        $ini = \eZINI::instance();
        $userClassID = $ini->variable( "UserSettings", "UserClassID" );

        if ( $module->isCurrentAction( 'Cancel' ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'trash' ) );
        }

        $class = $object->contentClass();
        $version = $object->attribute( 'current' );

        $location = null;
        $trashNode = \eZContentObjectTrashNode::fetchByContentObjectID( $objectID );
        if ( $trashNode instanceof \eZContentObjectTrashNode )
        {
            $location = $trashNode->attribute( 'original_parent' );
        }

        if ( $module->isCurrentAction( 'Confirm' ) )
        {
            $type = $module->actionParameter( 'RestoreType' );
            if ( $type == 1 )
            {
                if ( $location === null )
                {
                    // Location unavailable – fall back to browse mode
                    $type = 2;
                }
                else
                {
                    $selectedNodeIDArray = array( $location->attribute( 'node_id' ) );
                    $module->setCurrentAction( 'AddLocation' );
                }
            }
            elseif ( $type == 2 )
            {
                $languageCode = $object->attribute( 'initial_language_code' );
                \eZContentBrowse::browse( array( 'action_name' => 'AddNodeAssignment',
                                                'description_template' => 'design:content/browse_placement.tpl',
                                                'keys' => array( 'class' => $class->attribute( 'id' ),
                                                                 'class_id' => $class->attribute( 'identifier' ),
                                                                 'classgroup' => $class->attribute( 'ingroup_id_list' ),
                                                                 'section' => $object->attribute( 'section_id' ) ),
                                                'ignore_nodes_select' => array(),
                                                'ignore_nodes_click'  => array(),
                                                'persistent_data' => array( 'ContentObjectID' => $objectID,
                                                                            'AddLocationAction' => '1' ),
                                                'content' => array( 'object_id' => $objectID,
                                                                    'object_version' => $version->attribute( 'version' ),
                                                                    'object_language' => $languageCode ),
                                                'cancel_page' => '/content/trash/',
                                                'from_page' => "/content/restore/" . $objectID ),
                                         $module );

                return $this->viewResult( isset( $Result ) ? $Result : null, null );
            }
        }

        if ( $module->isCurrentAction( 'AddLocation' ) )
        {
            // If $selectedNodeIDArray is already set then use it as it is,
            // if not get the browse data.
            if ( !isset( $selectedNodeIDArray ) )
            {
                $selectedNodeIDArray = \eZContentBrowse::result( 'AddNodeAssignment' );
                if ( !$selectedNodeIDArray )
                {
                    return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'trash' ) );
                }
            }

            $db = \eZDB::instance();
            $db->begin();
            $locationAdded = false;

            $newLocationList    = array();
            $failedLocationList = array();
            foreach ( $selectedNodeIDArray as $selectedNodeID )
            {
                $parentNode = \eZContentObjectTreeNode::fetch( $selectedNodeID );
                $parentNodeObject = $parentNode->attribute( 'object' );

                $canCreate = $parentNode->checkAccess( 'create', $class->attribute( 'id' ), $parentNodeObject->attribute( 'contentclass_id' ) ) == 1;

                if ( $canCreate )
                {
                    $newLocationList[] = array(
                        'parent_node_id' => $selectedNodeID,
                        'is_main' => !$locationAdded
                    );
                    $locationAdded = true;
                }
                else
                {
                    $failedLocationList[] = array( 'parent_node_id' => $selectedNodeID );
                }
            }

            // Check if we have failures
            if ( count( $failedLocationList ) > 0 )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
            }

            // Remove all existing assignments, only our new ones should be present.
            foreach ( $version->attribute( 'node_assignments' ) as $assignment )
            {
                $assignment->purge();
            }

            // Add all new locations
            foreach ( $newLocationList as $newLocation )
            {
                $version->assignToNode( $newLocation['parent_node_id'], $newLocation['is_main'] );
            }

            $object->setAttribute( 'status', \eZContentObject::STATUS_DRAFT );
            $object->store();
            $version->setAttribute( 'status', \eZContentObjectVersion::STATUS_DRAFT );
            $version->store();

            $object->restoreObjectAttributes();

            $user = \eZUser::currentUser();
            // the publish of a restore is the restore (audit: content.object.restore below), not a create or publish
            $publish = function () use ( $objectID, $version ) {
                return \eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $objectID,
                                                                                  'version' => $version->attribute( 'version' ) ) );
            };
            $operationResult = class_exists( 'expAuditHook' )
                ? \expAuditHook::muted( array( 'content.object.create', 'content.object.publish', 'content.object.translate' ), $publish )
                : $publish();
            if ( ( array_key_exists( 'status', $operationResult ) && $operationResult['status'] != \eZModuleOperationInfo::STATUS_CONTINUE ) )
            {
                switch( $operationResult['status'] )
                {
                    case \eZModuleOperationInfo::STATUS_HALTED:
                    case \eZModuleOperationInfo::STATUS_CANCELLED:
                    {
                        $module->redirectToView( 'trash' );
                    }
                }
            }
            $objectID = $object->attribute( 'id' );
            $object = \eZContentObject::fetch( $objectID );
            $mainNodeID = $object->attribute( 'main_node_id' );

            \eZContentObjectTrashNode::purgeForObject( $objectID  );

            if ( $locationAdded )
            {
                if ( $object->attribute( 'contentclass_id' ) == $userClassID )
                {
                    \eZUser::purgeUserCacheByUserId( $object->attribute( 'id' ) );
                }
            }

            \eZContentObject::fixReverseRelations( $objectID, 'restore', false );

            $db->commit();

            // Audit (doc/bc/6.0/audit.md, content.object.restore)
            if ( class_exists( 'expAuditHook' ) )
                \expAuditHook::emit( 'content.object.restore', function () use ( $object, $mainNodeID ) {
                    $main = \eZContentObjectTreeNode::fetch( $mainNodeID );
                    return array( 'object' => \expAuditHook::object( $object ),
                                  'target' => $main ? array( 'type' => 'node', 'id' => (int)$main->attribute( 'parent_node_id' ) ) : null,
                                  'after' => array( 'node' => (int)$mainNodeID, 'parent' => $main ? (int)$main->attribute( 'parent_node_id' ) : null ) );
                } );

            // we need to clear cache again after db transcation is commited
            \eZContentCacheManager::clearContentCacheIfNeeded( $objectID,  $version->attribute( 'version' ) );

            $module->redirectToView( 'view', array( 'full', $mainNodeID ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $tpl = \eZTemplate::factory();

        $res = \eZTemplateDesignResource::instance();

        $designKeys = array( array( 'object', $object->attribute( 'id' ) ), // Object ID
                             array( 'remote_id', $object->attribute( 'remote_id' ) ),
                             array( 'class', $class->attribute( 'id' ) ), // Class ID
                             array( 'class_identifier', $class->attribute( 'identifier' ) ) ); // Class identifier

        $res->setKeys( $designKeys );

        $Result = array();

        $tpl->setVariable( "object",   $object );
        $tpl->setVariable( "version",  $version );
        $tpl->setVariable( "location", $location );

        $Result['content'] = $tpl->fetch( 'design:content/restore.tpl' );
        $Result['path'] = array( array( 'uri'  => false,
                                        'text' => \ezpI18n::tr( "kernel/content/restore", "Restore object" ) ),
                                 array( 'uri'  => false,
                                        'text' => $object->attribute( 'name' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
