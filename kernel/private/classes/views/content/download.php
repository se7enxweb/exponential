<?php
/**
 * The code of kernel/content/download.php, moved into a class (#207 stage 1). The file kernel/content/download.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/download.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Content
{

class Download extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $contentObjectID = $Params['ContentObjectID'];
        $contentObjectAttributeID = $Params['ContentObjectAttributeID'];
        $contentObject = \eZContentObject::fetch( $contentObjectID );
        if ( !is_object( $contentObject ) || $contentObject->attribute( 'status' ) == \eZContentObject::STATUS_ARCHIVED)
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }
        $currentVersion = $contentObject->attribute( 'current_version' );

        if ( isset(  $Params['Version'] ) && is_numeric( $Params['Version'] ) )
             $version = $Params['Version'];
        else
             $version = $currentVersion;

        $contentObjectAttribute = \eZContentObjectAttribute::fetch( $contentObjectAttributeID, $version, true );
        if ( !is_object( $contentObjectAttribute ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }
        $contentObjectIDAttr = $contentObjectAttribute->attribute( 'contentobject_id' );
        if ( $contentObjectID != $contentObjectIDAttr or !$contentObject->attribute( 'can_read' ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
        }

        // Get locations.
        $nodeAssignments = $contentObject->attribute( 'assigned_nodes' );
        if ( count( $nodeAssignments ) === 0 )
        {
            // oops, no locations. probably it's related object. Let's check his owners
            $ownerList = \eZContentObject::fetch( $contentObjectID )->reverseRelatedObjectList( false, false, false, false );
            foreach ( $ownerList as $owner )
            {
                if ( is_object( $owner ) )
                {
                    $ownerNodeAssignments = $owner->attribute( 'assigned_nodes' );
                    $nodeAssignments = array_merge( $nodeAssignments, $ownerNodeAssignments );
                }
            }
        }

        // If exists location that current user has access to and location is visible.
        $canAccess = false;
        $isContentDraft = $contentObject->attribute( 'status' ) == \eZContentObject::STATUS_DRAFT;
        foreach ( $nodeAssignments as $nodeAssignment )
        {
            if ( ( \eZContentObjectTreeNode::showInvisibleNodes() || !$nodeAssignment->attribute( 'is_invisible' ) ) and $nodeAssignment->canRead() )
            {
                $canAccess = true;
                break;
            }
        }
        if ( !$canAccess && !$isContentDraft )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        // If $version is not current version (published)
        // we should check permission versionRead for the $version.
        if ( $version != $currentVersion || $isContentDraft )
        {
            $versionObj = \eZContentObjectVersion::fetchVersion( $version, $contentObjectID );
            if ( is_object( $versionObj ) and !$versionObj->canVersionRead() )
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }

        \ezpEvent::getInstance()->notify(
            'content/download',
            array( 'contentObjectID' => $contentObjectID,
                   'contentObjectAttributeID' => $contentObjectAttributeID ) );

        $fileHandler = \eZBinaryFileHandler::instance();
        $result = $fileHandler->handleDownload( $contentObject, $contentObjectAttribute, \eZBinaryFileHandler::TYPE_FILE );

        if ( $result == \eZBinaryFileHandler::RESULT_UNAVAILABLE )
        {
            \eZDebug::writeError( "The specified file could not be found." );
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
