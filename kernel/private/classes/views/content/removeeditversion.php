<?php
/**
 * The code of kernel/content/removeeditversion.php, moved into a class (#207 stage 1). The file kernel/content/removeeditversion.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/removeeditversion.php:
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

class Removeeditversion extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $db = \eZDB::instance();
        $objectID = (int) $http->sessionVariable( "DiscardObjectID" );
        $version = (int) $http->sessionVariable( "DiscardObjectVersion" );
        $editLanguage = $http->sessionVariable( "DiscardObjectLanguage" );

        $isConfirmed = false;
        if ( $http->hasPostVariable( "ConfirmButton" ) )
            $isConfirmed = true;

        if ( $http->hasSessionVariable( "DiscardConfirm" ) )
        {
            $discardConfirm = $http->sessionVariable( "DiscardConfirm" );
            if ( !$discardConfirm )
                $isConfirmed = true;
        }

        if ( $isConfirmed )
        {
            $object = \eZContentObject::fetch( $objectID );
            if ( $object === null )
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

            $db->begin();

            if ( $db->databaseName() === 'mongo' )
            {
                $versionRows = $db->aggregate( 'ezcontentobject_version', [
                    [ '$match' => [ 'version' => $version, 'contentobject_id' => $objectID ] ],
                ] );
            }
            elseif( $db->DatabaseName() == 'sqlite' )
            {
                $versionRows = $db->arrayQuery( "SELECT * FROM ezcontentobject_version WHERE version = $version AND contentobject_id = $objectID" );
            }
            else
            {
                $versionRows = $db->arrayQuery( "SELECT * FROM ezcontentobject_version WHERE version = $version AND contentobject_id = $objectID FOR UPDATE" );
            }

            if ( empty( $versionRows ) )
            {
                $db->commit(); // We haven't made any changes, but commit here to avoid affecting any outer transactions.
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
            }
            $versionObject = \eZContentObjectVersion::fetch( $versionRows[0]['id'] );
            if ( is_object( $versionObject ) and
                 in_array( $versionObject->attribute( 'status' ), array( \eZContentObjectVersion::STATUS_DRAFT, \eZContentObjectVersion::STATUS_INTERNAL_DRAFT ) ) )
            {
                if ( !$object->attribute( 'can_edit' ) )
                {
                    // An object that was never published may be edited, at every version, by someone who may create
                    // it under the parent of its main node assignment (no assignment or no parent: denied).
                    $allowEdit = (bool)$object->draftCreateAccess();

                    if ( !$allowEdit )
                    {
                        $db->commit(); // We haven't made any changes, but commit here to avoid affecting any outer transactions.
                        return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel', array( 'AccessList' => $object->accessList( 'edit' ) ) ) );
                    }
                }

                $versionCount= $object->getVersionCount();
                $nodeID = $versionCount == 1 ? $versionObject->attribute( 'main_parent_node_id' ) : $object->attribute( 'main_node_id' );
                // Audit (doc/bc/6.0/audit.md, content.version.remove): a discarded draft
                $auditVersion = array( 'version' => (int)$versionObject->attribute( 'version' ), 'status' => (int)$versionObject->attribute( 'status' ),
                                       'language' => (string)$versionObject->initialLanguageCode() );
                $auditName = (string)$object->attribute( 'name' );
                if ( class_exists( 'expAuditHook' ) )
                    // the draft of a new object takes the object with it: that is this discard, not a purge of its own
                    \expAuditHook::muted( 'content.object.purge', function () use ( $versionObject ) { $versionObject->removeThis(); } );
                else
                    $versionObject->removeThis();
                if ( class_exists( 'expAuditHook' ) )
                    \expAuditHook::emit( 'content.version.remove', array(
                        'object' => array( 'type' => 'version', 'id' => (string)$auditVersion['version'], 'object_id' => (int)$objectID,
                                           'name' => $auditName !== '' ? $auditName : null ),
                        'before' => array( 'versions' => array( $auditVersion ), 'discard' => true ) ) );
            }

            $db->commit();

            $hasRedirected = false;
            if ( $http->hasSessionVariable( 'RedirectIfDiscarded' ) )
            {
                $Module->redirectTo( $http->sessionVariable( 'RedirectIfDiscarded' ) );
                $http->removeSessionVariable( 'RedirectIfDiscarded' );
                $hasRedirected = true;
            }
            if ( $http->hasSessionVariable( 'ParentObject' ) && $http->sessionVariable( 'NewObjectID' ) == $objectID )
            {
                $parentArray = $http->sessionVariable( 'ParentObject' );
                $parentURL = $Module->redirectionURI( 'content', 'edit', $parentArray );
                $Module->redirectTo( $parentURL );
                $hasRedirected = true;
            }

            $http->removeSessionVariable( 'RedirectURIAfterPublish' );
            $http->removeSessionVariable( 'ParentObject' );
            $http->removeSessionVariable( 'NewObjectID' );

            if ( $hasRedirected )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null, null );
            }
            else if ( isset( $nodeID ) && $nodeID )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( '/content/view/full/' . $nodeID . '/' ) );
            }
            else
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  \eZRedirectManager::redirectTo( $Module, '/', true, array( 'content/edit' ) ) );
            }
        }

        if ( $http->hasPostVariable( "CancelButton" ) )
        {
            $Module->redirectTo( '/content/edit/' . $objectID . '/' . $version . '/' );
        }

        $Module->setTitle( "Remove Editing Version" );


        $tpl = \eZTemplate::factory();
        $tpl->setVariable( "Module", $Module );
        $tpl->setVariable( "object_id", $objectID );
        $tpl->setVariable( "object_version", $version );
        $tpl->setVariable( "object_language", $editLanguage );
        $Result = array();
        $Result['content'] = $tpl->fetch( "design:content/removeeditversion.tpl" );
        $Result['path'] = array( array( 'url' => '/content/removeeditversion/',
                                        'text' => \ezpI18n::tr( 'kernel/content', 'Remove editing version' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
