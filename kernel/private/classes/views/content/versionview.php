<?php
/**
 * The code of kernel/content/versionview.php, moved into a class (#207 stage 1). The file kernel/content/versionview.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/versionview.php:
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

class Versionview extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $Offset = (int)$Params['Offset'];
        $ObjectID = (int)$ObjectID;
        $EditVersion = (int)$EditVersion;
        $LanguageCode = htmlspecialchars( (string)$LanguageCode, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 );
        $viewParameters = array( 'offset' => $Offset );

        // The view mode the version is shown in, (view_mode)/print for example; only those of
        // content.ini [VersionView] ViewModes[] (full by default)
        $viewMode = self::viewMode( isset( $scope['Params']['ViewMode'] ) ? $scope['Params']['ViewMode'] : null );
        if ( $viewMode === null )
            return $this->viewResult( null, $scope['Params']['Module']->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        // Will be sent from the content/edit page and should be kept
        // incase the user decides to continue editing.
        $FromLanguage = htmlspecialchars( (string)( $Params['FromLanguage'] ?? '' ), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 );

        if ( $http->hasPostVariable( 'BackButton' )  )
        {
            $userRedirectURI = '';
            if ( $http->hasPostVariable( 'RedirectURI' ) )
            {
                $redurectURI = $http->postVariable( 'RedirectURI' );
                $http->removeSessionVariable( 'LastAccessesVersionURI' );
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( $redurectURI ) );
            }
            if ( $http->hasSessionVariable( "LastAccessesURI", false ) )
                $userRedirectURI = $http->sessionVariable( "LastAccessesURI" );
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( $userRedirectURI ) );
        }

        $contentObject = \eZContentObject::fetch( $ObjectID );
        if ( $contentObject === null )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        $versionObject = $contentObject->version( $EditVersion );
        if ( !$versionObject )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        if ( !$versionObject->attribute( 'can_read' ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
        }

        if ( !$LanguageCode )
        {
            $LanguageCode = $versionObject->initialLanguageCode();
        }

        $user = \eZUser::currentUser();

        $isCreator = ( $versionObject->attribute( 'creator_id' ) == $user->id() );

        if ( $Module->isCurrentAction( 'Versions' ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectToView( 'history', array( $ObjectID, $EditVersion, $LanguageCode, $FromLanguage ) ) );
        }

        $sectionID = false;
        $placementID = false;
        $assignment = false;

        $http = \eZHTTPTool::instance();

        if ( $http->hasPostVariable( 'ContentObjectLanguageCode' ) )
            $LanguageCode = $http->postVariable( 'ContentObjectLanguageCode' );
        if ( $http->hasPostVariable( 'ContentObjectPlacementID' ) )
            $placementID = $http->postVariable( 'ContentObjectPlacementID' );

        $nodeAssignments = $versionObject->attribute( 'node_assignments' );
        $virtualNodeID = null;
        if ( is_array( $nodeAssignments ) and
             count( $nodeAssignments ) == 1 )
        {
            if ( $contentObject->attribute( 'main_node_id' ) != null )
                $virtualNodeID = $contentObject->attribute( 'main_node_id' );
            else
                $virtualNodeID = null;

            $placementID = $nodeAssignments[0]->attribute( 'id' );
        }
        else if ( !$placementID && count( $nodeAssignments ) )
        {
            foreach ( $nodeAssignments as $nodeAssignment )
            {
                if ( $nodeAssignment->attribute( 'is_main' ) )
                {
                    $placementID = $nodeAssignment->attribute( 'id' );
                    $parentNodeID = $nodeAssignment->attribute( 'parent_node' );
                    $ObjectID = (int) $ObjectID;
                    $query="SELECT node_id
                            FROM ezcontentobject_tree
                            WHERE contentobject_id=$ObjectID
                            AND parent_node_id=$parentNodeID";

                    $db = \eZDB::instance();
                    $nodeListArray = $db->arrayQuery( $query );
                    $virtualNodeID = $nodeListArray[0]['node_id'];
                    break;
                }
            }
        }
        $parentNodeID = false;
        $mainAssignment = false;
        foreach ( $nodeAssignments as $nodeAssignment )
        {
            if ( $nodeAssignment->attribute( 'is_main' ) == 1 )
            {
                $mainAssignment = $nodeAssignment;
                $parentNodeID = $mainAssignment->attribute( 'parent_node' );
                break;
            }
        }

        if ( $Module->isCurrentAction( 'ChangeSettings' ) )
        {
            if ( $Module->hasActionParameter( 'Language' ) )
            {
                $LanguageCode = $Module->actionParameter( 'Language' );
            }

            if ( $Module->hasActionParameter( 'PlacementID' ) )
            {
                $placementID = $Module->actionParameter( 'PlacementID' );
            }

            // The view mode chosen in the form (a listed one; anything else keeps the current one)
            if ( $scope['Params']['Module']->hasActionParameter( 'ViewMode' ) )
            {
                $viewMode = self::changedViewMode( $scope['Params']['Module']->actionParameter( 'ViewMode' ), $viewMode );
            }
        }

        $assignment = null;
        if ( is_numeric( $placementID ) )
            $assignment = \eZNodeAssignment::fetchByID( $placementID );
        if ( $assignment !== null )
        {
            $node = $assignment->getParentNode();
            if ( $node !== null )
            {
                $nodeObject = $node->attribute( "object" );
                $sectionID = $nodeObject->attribute( "section_id" );
            }
        }
        else
        {
            $assignment = false;
        }

        if ( $assignment )
        {
            $parentNodeObject = $assignment->attribute( 'parent_node_obj' );
        }

        $navigationPartIdentifier = false;
        if ( $sectionID !== false )
        {
            $designKeys[] = array( 'section', $sectionID ); // Section ID

            $section = \eZSection::fetch( $sectionID );
            if ( $section )
                $navigationPartIdentifier = $section->attribute( 'navigation_part_identifier' );
        }
        $designKeys[] = array( 'navigation_part_identifier', $navigationPartIdentifier );

        if ( !$Module->isCurrentAction( 'Publish' ) )
            $contentObject->setAttribute( 'current_version', $EditVersion );

        $class = \eZContentClass::fetch( $contentObject->attribute( 'contentclass_id' ) );
        $objectName = $class->contentObjectName( $contentObject );
        $contentObject->setCachedName( $objectName );
        if ( $assignment )
            $assignment->setName( $objectName );


        $path = array();
        $pathString = '';
        $pathIdentificationString = '';
        $requestedURIString = '';
        $depth = 2;
        if ( isset( $parentNodeObject ) && is_object( $parentNodeObject ) )
        {
            $pathString = $parentNodeObject->attribute( 'path_string' ) . $virtualNodeID . '/';
            $pathIdentificationString = $parentNodeObject->attribute( 'path_identification_string' ); //TODO add current node ident string.
            $depth = $parentNodeObject->attribute( 'depth' ) + 1;
            $requestedURIString = $parentNodeObject->attribute( 'url_alias' );
        }

        if ( isset( $node ) && is_object( $node ) )
        {
            $requestedURIString = $node->attribute( 'url_alias' );
        }

        $node = new \eZContentObjectTreeNode();
        $node->setAttribute( 'contentobject_version', $EditVersion );
        $node->setAttribute( 'path_identification_string', $pathIdentificationString );
        $node->setAttribute( 'contentobject_id', $ObjectID );
        $node->setAttribute( 'parent_node_id', $parentNodeID );
        $node->setAttribute( 'main_node_id', $virtualNodeID );
        $node->setAttribute( 'path_string', $pathString );
        $node->setAttribute( 'depth', $depth );
        $node->setAttribute( 'node_id', $virtualNodeID );
        $node->setAttribute( 'sort_field', $class->attribute( 'sort_field' ) );
        $node->setAttribute( 'sort_order', $class->attribute( 'sort_order' ) );
        $node->setAttribute( 'remote_id', \eZRemoteIdUtility::generate( 'node' ) );
        $node->setName( $objectName );

        $node->setContentObject( $contentObject );

        if ( $Params['SiteAccess'] )
        {
            $siteAccess = htmlspecialchars( $Params['SiteAccess'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 );
        }
        else
        {
            include( 'kernel/content/versionviewframe.php' );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( !$siteAccess )
        {
            $contentINI = \eZINI::instance( 'content.ini' );
            if ( $contentINI->hasVariable( 'VersionView', 'DefaultPreviewDesign' ) )
            {
                $siteAccess = $contentINI->variable( 'VersionView', 'DefaultPreviewDesign' );
            }
            else
            {
                $siteAccess = \eZTemplateDesignResource::designSetting( 'site' );
            }
        }

        $access = $GLOBALS['eZCurrentAccess'];
        $access['name'] = $siteAccess;

        if ( $access['type'] === \eZSiteAccess::TYPE_URI )
        {
            $access['uri_part'] = array( $siteAccess );
        }

        \eZSiteAccess::load( $access );

        \eZDebug::checkDebugByUser();

        // Change content object default language
        $GLOBALS['eZContentObjectDefaultLanguage'] = $LanguageCode;
        \eZTranslatorManager::resetTranslations();
        \ezpI18n::reset();
        \eZContentObject::clearCache();

        $Module->setTitle( 'View ' . $class->attribute( 'name' ) . ' - ' . $contentObject->attribute( 'name' ) );

        $ini = \eZINI::instance();
        $res = \eZTemplateDesignResource::instance();
        $res->setDesignSetting( $ini->variable( 'DesignSettings', 'SiteDesign' ), 'site' );
        $res->setOverrideAccess( $siteAccess );

        $tpl = \eZTemplate::factory();

        if ( $http->hasSessionVariable( 'LastAccessesVersionURI' ) )
        {
            $tpl->setVariable( 'redirect_uri', $http->sessionVariable( 'LastAccessesVersionURI' ) );
        }
        $tpl->setVariable( 'view_mode', $viewMode );

        $designKeys = array( array( 'object', $contentObject->attribute( 'id' ) ), // Object ID
                             array( 'node', $virtualNodeID ), // Node id
                             array( 'remote_id', $contentObject->attribute( 'remote_id' ) ),
                             array( 'class', $class->attribute( 'id' ) ), // Class ID
                             array( 'class_identifier', $class->attribute( 'identifier' ) ), // Class identifier
                             array( 'viewmode', $viewMode ) );  // View mode

        if ( $assignment )
        {
            $designKeys[] = array( 'parent_node', $assignment->attribute( 'parent_node' ) );
            if ( $parentNodeObject instanceof \eZContentObjectTreeNode )
                $designKeys[] = array( 'depth', $parentNodeObject->attribute( 'depth' ) + 1 );
        }


        $res->setKeys( $designKeys );

        unset( $contentObject );
        $contentObject = $node->attribute( 'object' );

        $Result = \eZNodeviewfunctions::generateNodeViewData( $tpl, $node, $contentObject, $LanguageCode, $viewMode, 0, $viewParameters );

        $Result['requested_uri_string'] = $requestedURIString;
        $Result['ui_context'] = 'view';

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * Returns the view mode a version is to be shown in: $requested when content.ini [VersionView] ViewModes[] lists
     * it, full when nothing was asked for, null for a view mode that is not listed.
     *
     * @param string|null $requested The (view_mode) parameter of the address
     * @return string|null
     */
    public static function viewMode( $requested )
    {
        if ( $requested === null || $requested === '' || $requested === false )
        {
            return 'full';
        }
        if ( !is_string( $requested ) || !preg_match( '/^[A-Za-z0-9_-]+$/', $requested ) )
        {
            // A view mode names a template (node/view/<mode>.tpl): never a path, whatever the setting lists
            return null;
        }
        return in_array( $requested, self::viewModes(), true ) ? $requested : null;
    }

    /**
     * The view modes a version can be shown in: content.ini [VersionView] ViewModes[], full when it is not set.
     *
     * @return string[]
     */
    public static function viewModes()
    {
        $ini = \eZINI::instance( 'content.ini' );
        $listed = $ini->hasVariable( 'VersionView', 'ViewModes' ) ? (array)$ini->variable( 'VersionView', 'ViewModes' ) : array( 'full' );
        return array_values( array_unique( array_filter( array_map( 'strval', $listed ), 'strlen' ) ) );
    }

    /**
     * The view mode after the form of the version preview was sent ("Update view"): $posted when it is a listed view
     * mode, otherwise $current.
     *
     * @param mixed $posted SelectedViewMode of the form
     * @param string $current
     * @return string
     */
    public static function changedViewMode( $posted, $current )
    {
        if ( $posted === null || $posted === '' || $posted === false )
        {
            return $current;
        }
        $viewMode = self::viewMode( $posted );
        return $viewMode !== null ? $viewMode : $current;
    }

    /**
     * Returns the nodes above the previewed node, for the path of the version preview.
     *
     * The preview node of an object that was never published has no node ID; its path string is the parent's path with
     * an empty last element (/1/2/58//). fetchPath() drops the last element as the node's own, which was the parent, so
     * the preview path lost the parent folder. fetchPath() now reads such a path including the last element
     * (eZContentObjectTreeNode::pathStringEndsWithParent()), so a template reading $node.path of the preview node gets
     * the same path; a preview without location gets an empty path.
     *
     * @param \eZContentObjectTreeNode $node the preview node built by the version view
     * @return \eZContentObjectTreeNode[]
     */
    public static function previewParentNodes( \eZContentObjectTreeNode $node )
    {
        if ( trim( (string)$node->attribute( 'path_string' ), '/' ) === '' )
        {
            return array();
        }
        $parents = $node->attribute( 'path' );
        return is_array( $parents ) ? $parents : array();
    }
}

}
