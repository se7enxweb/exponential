<?php
/**
 * The code of kernel/content/download.php, moved into a class (#207 stage 1). The file kernel/content/download.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/download.php:
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
        // The attribute must belong to the object; no extension can change that
        if ( $contentObjectID != $contentObjectIDAttr )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
        }

        $denied = self::access( $contentObject, $contentObjectAttribute, $version, $currentVersion );
        if ( $denied !== null )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( $denied, 'kernel' ) );
        }

        \ezpEvent::getInstance()->notify(
            'content/download',
            array( 'contentObjectID' => $contentObjectID,
                   'contentObjectAttributeID' => $contentObjectAttributeID ) );

        // Audit (doc/bc/6.0/audit.md, content.object.download): a sampled read
        if ( class_exists( 'expAuditHook' ) )
            \expAuditHook::read( 'content.object.download', $contentObject->attribute( 'main_node_id' ), function () use ( $contentObject, $contentObjectAttribute ) {
                return array( 'object' => \expAuditHook::object( $contentObject ),
                              'target' => array( 'type' => 'attribute', 'id' => (int)$contentObjectAttribute->attribute( 'id' ),
                                                 'identifier' => (string)$contentObjectAttribute->attribute( 'contentclass_attribute_identifier' ) ) );
            } );

        $fileHandler = \eZBinaryFileHandler::instance();
        $result = $fileHandler->handleDownload( $contentObject, $contentObjectAttribute, \eZBinaryFileHandler::TYPE_FILE );

        if ( $result == \eZBinaryFileHandler::RESULT_UNAVAILABLE )
        {
            \eZDebug::writeError( "The specified file could not be found." );
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * Whether the current user may download the file of $contentObjectAttribute in version $version: null when they
     * may, otherwise the error to answer with. An attribute of another object or version is refused first, without
     * asking anybody. Then the answer of the kernel (kernelAccess()) goes through the filter content/download/access
     * with the object, the attribute and the version, so an extension can let someone in (an approver who may read
     * the version only) or keep someone out; only true allows, and a refusal keeps the kernel's error.
     *
     * @param \eZContentObject $contentObject
     * @param \eZContentObjectAttribute $contentObjectAttribute
     * @param int $version
     * @param int $currentVersion
     * @return int|null
     */
    public static function access( $contentObject, $contentObjectAttribute, $version, $currentVersion )
    {
        // The attribute must belong to the object and to the version asked for; no listener is asked otherwise, so
        // none can let out the file of another object or version
        if ( !self::attributeBelongs( $contentObject, $contentObjectAttribute, $version ) )
        {
            return \eZError::KERNEL_ACCESS_DENIED;
        }

        $denied = self::kernelAccess( $contentObject, $version, $currentVersion );
        $allowed = \ezpEvent::getInstance()->filter( 'content/download/access', $denied === null,
                                                      $contentObject, $contentObjectAttribute, (int)$version ) === true;
        if ( $allowed !== ( $denied === null ) )
        {
            \eZDebug::writeNotice( 'A listener of content/download/access ' . ( $allowed ? 'allowed' : 'refused' ) . ' the file of attribute ' .
                                   (int)$contentObjectAttribute->attribute( 'id' ) . ' of object ' . (int)$contentObject->attribute( 'id' ) .
                                   ' version ' . (int)$version . ' (the kernel ' . ( $denied === null ? 'allowed' : 'refused' ) . ' it)', __METHOD__ );
        }
        if ( $allowed )
        {
            return null;
        }
        return $denied !== null ? $denied : \eZError::KERNEL_ACCESS_DENIED;
    }

    /**
     * Whether $contentObjectAttribute is an attribute of $contentObject in version $version.
     *
     * @param \eZContentObject $contentObject
     * @param \eZContentObjectAttribute $contentObjectAttribute
     * @param int $version
     * @return bool
     */
    public static function attributeBelongs( $contentObject, $contentObjectAttribute, $version )
    {
        if ( !$contentObject instanceof \eZContentObject || !$contentObjectAttribute instanceof \eZContentObjectAttribute )
        {
            return false;
        }
        return (int)$contentObjectAttribute->attribute( 'contentobject_id' ) === (int)$contentObject->attribute( 'id' ) &&
               (int)$contentObjectAttribute->attribute( 'version' ) === (int)$version;
    }

    /**
     * Whether the kernel lets the current user download a file of version $version of $contentObject: null when it
     * does, otherwise the error to answer with. The user must read the object and one of its locations (or one of
     * the objects relating to it, for an object without location), and read the version when it is not the published
     * one.
     *
     * @param \eZContentObject $contentObject
     * @param int $version
     * @param int $currentVersion
     * @return int|null
     */
    public static function kernelAccess( $contentObject, $version, $currentVersion )
    {
        if ( !$contentObject->attribute( 'can_read' ) )
        {
            return \eZError::KERNEL_ACCESS_DENIED;
        }

        // Get locations.
        $nodeAssignments = $contentObject->attribute( 'assigned_nodes' );
        if ( count( $nodeAssignments ) === 0 )
        {
            // oops, no locations. probably it's related object. Let's check his owners
            $ownerList = $contentObject->reverseRelatedObjectList( false, false, false, false );
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
        {
            return \eZError::KERNEL_NOT_AVAILABLE;
        }

        // If $version is not current version (published)
        // we should check permission versionRead for the $version.
        if ( $version != $currentVersion || $isContentDraft )
        {
            $versionObj = \eZContentObjectVersion::fetchVersion( $version, $contentObject->attribute( 'id' ) );
            if ( is_object( $versionObj ) and !$versionObj->canVersionRead() )
            {
                return \eZError::KERNEL_NOT_AVAILABLE;
            }
        }

        return null;
    }
}

}
