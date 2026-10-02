<?php
/**
 * The code of kernel/content/removeassignment.php, moved into a class (#207 stage 1). The file kernel/content/removeassignment.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/removeassignment.php:
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

class Removeassignment extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];
        $http   = \eZHTTPTool::instance();

        if ( $http->hasSessionVariable( 'AssignmentRemoveData' ) )
        {
            $data = $http->sessionVariable( 'AssignmentRemoveData' );
            $removeList   = $data['remove_list'];
            $objectID     = $data['object_id'];
            $editVersion  = $data['edit_version'];
            $editLanguage = $data['edit_language'];
            $fromLanguage = $data['from_language'];

            $object = \eZContentObject::fetch( $objectID );
            if ( !$object )
                return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
            if ( !$object->checkAccess( 'edit' ) )
                return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
            unset( $object );
        }
        else
        {
            \eZDebug::writeError( "No assignments passed to content/removeassignment" );
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'view', array( 'full', 2 ) ) );
        }

        // process current action
        if ( $module->isCurrentAction( 'ConfirmRemoval' ) )
        {
            $http->removeSessionVariable( 'AssignmentRemoveData' );

            $assignments     = \eZNodeAssignment::fetchListByID( $removeList );
            $mainNodeChanged = false;

            $db = \eZDB::instance();
            $db->begin();
            foreach ( $assignments as $assignment )
            {
                $assignmentID = $assignment->attribute( 'id' );
                if ( $assignment->attribute( 'is_main' ) )
                    $mainNodeChanged = true;
                \eZNodeAssignment::purgeByID( $assignmentID );
            }
            if ( $mainNodeChanged )
                \eZNodeAssignment::setNewMainAssignment( $objectID, $editVersion );

            $db->commit();

            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'edit', array( $objectID, $editVersion, $editLanguage, $fromLanguage ) ) );
        }
        else if ( $module->isCurrentAction( 'CancelRemoval' ) )
        {
            $http->removeSessionVariable( 'AssignmentRemoveData' );

            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'edit', array( $objectID, $editVersion, $editLanguage, $fromLanguage ) ) );
        }

        // default action: show the confirmation dialog
        $assignmentsToRemove = \eZNodeAssignment::fetchListByID( $removeList );
        $removeList = array();
        $canRemoveAll = true;
        foreach ( $assignmentsToRemove as $assignment )
        {
            $node = $assignment->attribute( 'node' );

            // skip assignments which don't have associated node or node with no children
            if ( !$node )
                continue;
            $count = $node->subTreeCount( array( 'Limitation' => array() ) );
            if ( $count < 1 )
                continue;

            // Find the number of items in the subtree we are allowed to remove
            // if this differs from the total count it means we have items we cannot remove
            // We do this by fetching the limitation list for content/remove
            // and passing it to the subtre count function.
            $currentUser = \eZUser::currentUser();
            $accessResult = $currentUser->hasAccessTo( 'content', 'remove' );
            $canRemoveSubtree = true;
            if ( $accessResult['accessWord'] == 'limited' )
            {
                $limitationList = $accessResult['policies'] ?? [];
                $removeableChildCount = $node->subTreeCount( array( 'Limitation' => $limitationList ) );
                $canRemoveSubtree = ( $removeableChildCount == $count );
            }
            if ( !$canRemoveSubtree )
                $canRemoveAll = false;
            $object = $node->object();
            $class = $object->contentClass();

            $removeList[] = array( 'node' => $node,
                                   'object' => $object,
                                   'class' => $class,
                                   'count' => $count,
                                   'can_remove' => $canRemoveSubtree,
                                   'child_count' => $count );
        }
        unset( $assignmentsToRemove );

        $assignmentData = array( 'object_id'      => $objectID,
                                 'object_version' => $editVersion,
                                 'remove_list'    => $removeList );
        $info = array( 'can_remove_all' => $canRemoveAll );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'assignment_data', $assignmentData );
        $tpl->setVariable( 'remove_info', $info );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:content/removeassignment.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/content', 'Remove location' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
