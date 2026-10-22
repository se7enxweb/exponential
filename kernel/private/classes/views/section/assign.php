<?php
/**
 * The code of kernel/section/assign.php, moved into a class (#207 stage 1). The file kernel/section/assign.php is one call to it.
 * Guide: doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/section/assign.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Section
{

class Assign extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $SectionID = $Params["SectionID"];
        $Module = $Params['Module'];

        if ( $http->hasPostVariable( 'BrowseCancelButton' ) )
        {
            if ( $http->hasPostVariable( 'BrowseCancelURI' ) )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( $http->postVariable( 'BrowseCancelURI' ) ) );
            }
        }
        else
        {
            $section = \eZSection::fetch( $SectionID );
            if ( !is_object( $section ) )
            {
                \eZDebug::writeError( "Cannot fetch section (ID = $SectionID).", 'section/assign' );
            }
            else
            {
                $currentUser = \eZUser::currentUser();

                if ( $currentUser->canAssignSection( $SectionID ) )
                {
                    if ( $Module->isCurrentAction( 'AssignSection' ) )
                    {   // Assign section to subtree of node

                        $selectedNodeIDArray = \eZContentBrowse::result( 'AssignSection' );
                        // which objects below each chosen item change (eZSection::SCOPE_*), chosen above the browser
                        $sectionScope = \eZSection::scopeFromHTTP( $http );
                        if ( $sectionScope === false )
                        {
                            \eZDebug::writeError( 'Unknown section assignment scope.', 'section/assign' );
                            $selectedNodeIDArray = array();
                        }
                        if ( is_array( $selectedNodeIDArray ) and count( $selectedNodeIDArray ) > 0 )
                        {
                            $nodeList = \eZContentObjectTreeNode::fetch( $selectedNodeIDArray );
                            if ( !is_array( $nodeList ) and is_object( $nodeList ) )
                            {
                                $nodeList = array( $nodeList );
                            }

                            $allowedNodeIDList = array();
                            $deniedNodeIDList = array();
                            foreach ( $nodeList as $node )
                            {
                                $nodeID = $node->attribute( 'node_id' );
                                $object = $node->attribute( 'object' );
                                if ( $currentUser->canAssignSectionToObject( $SectionID, $object ) )
                                {
                                    $allowedNodeIDList[] = $nodeID;
                                }
                                else
                                {
                                    $deniedNodeIDList[] = $nodeID;
                                }
                            }

                            // Content jobs (doc/eng-us/bc/6.0/content-jobs.md): one large subtree (or the user's last
                            // choice) gets a confirmation with the now-or-background choice; small ones as before
                            if ( count( $allowedNodeIDList ) === 1 && !$deniedNodeIDList && $sectionScope !== \eZSection::SCOPE_OBJECT
                                 && class_exists( 'Exponential\\View\\Kernel\\Content\\Job' ) )
                            {
                                try
                                {
                                    $jobNodeID = (int) $allowedNodeIDList[0];
                                    $jobNode = \eZContentObjectTreeNode::fetch( $jobNodeID );
                                    $jobResult = \Exponential\View\Kernel\Content\Job::interstitial( $Module, 'section', 'section',
                                        array( 'node_id' => $jobNodeID, 'section_id' => (int) $SectionID, 'scope' => $sectionScope ),
                                        '/section/assign/' . (int) $SectionID . '/', '/content/view/full/' . $jobNodeID,
                                        \ezpI18n::tr( 'design/admin/content/job', 'Assign the section %section to %name', null,
                                                      array( '%section' => $section->attribute( 'name' ), '%name' => $jobNode ? $jobNode->attribute( 'name' ) : $jobNodeID ) ),
                                        $sectionScope === \eZSection::SCOPE_INHERITED
                                        ? \ezpI18n::tr( 'design/admin/content/job', 'The objects in the subtree that have the section of this item get the section; branches in another section keep theirs, and objects you may not assign it to are skipped.' )
                                        : \ezpI18n::tr( 'design/admin/content/job', 'Every object in the subtree gets the section; objects you may not assign it to are skipped.' ),
                                        array( 'BrowseActionName' => 'AssignSection', 'SelectedNodeIDArray[]' => $jobNodeID, 'SectionScope' => $sectionScope ), $jobNodeID );
                                    if ( $jobResult )
                                        return $this->viewResult( null, $jobResult );
                                }
                                catch ( \Throwable $e )
                                {
                                    \eZDebug::writeError( 'Content jobs: ' . $e->getMessage(), __METHOD__ );
                                }
                            }

                            if ( count( $allowedNodeIDList ) > 0 )
                            {
                                $db = \eZDB::instance();
                                $db->begin();
                                foreach ( $allowedNodeIDList as $nodeID )
                                {
                                    \eZContentObjectTreeNode::assignSectionToSubTree( $nodeID, $SectionID, false, true, $sectionScope );
                                }
                                $db->commit();

                                // clear content caches
                                \eZContentCacheManager::clearAllContentCache();
                            }
                            if ( count( $deniedNodeIDList ) > 0 )
                            {
                                $tpl = \eZTemplate::factory();
                                $tpl->setVariable( 'section_name', $section->attribute( 'name' ) );
                                $tpl->setVariable( 'error_number', 1 );
                                $deniedNodes = \eZContentObjectTreeNode::fetch( $deniedNodeIDList );
                                $tpl->setVariable( 'denied_node_list', $deniedNodes );

                                $Result = array();
                                $Result['content'] = $tpl->fetch( "design:section/assign_notification.tpl" );
                                $Result['path'] = array( array( 'url' => false,
                                                                'text' => \ezpI18n::tr( 'kernel/section', 'Sections' ) ),
                                                         array( 'url' => false,
                                                                'text' => \ezpI18n::tr( 'kernel/section', 'Assign section' ) ) );
                                return $this->viewResult( isset( $Result ) ? $Result : null, null );
                            }
                        }
                    }
                    else
                    {
                        // Redirect to content node browse
                        $classList = $currentUser->canAssignSectionToClassList( $SectionID );
                        if ( count( $classList ) > 0 )
                        {
                            if ( in_array( '*', $classList ) )
                            {
                                $classList = false;
                            }
                            \eZContentBrowse::browse( array( 'action_name' => 'AssignSection',
                                                            'keys' => array(),
                                                            'description_template' => 'design:section/browse_assign.tpl',
                                                            'content' => array( 'section_id' => $SectionID ),
                                                            'from_page' => '/section/assign/' . $SectionID . "/",
                                                            'cancel_page' => '/section/list',
                                                            'class_array' => $classList ),
                                                     $Module );
                            return $this->viewResult( isset( $Result ) ? $Result : null, null );
                        }
                        else
                        {
                            $tpl = \eZTemplate::factory();
                            $tpl->setVariable( 'section_name', $section->attribute( 'name' ) );
                            $tpl->setVariable( 'error_number', 2 );
                            $Result = array();
                            $Result['content'] = $tpl->fetch( "design:section/assign_notification.tpl" );
                            $Result['path'] = array( array( 'url' => false,
                                                            'text' => \ezpI18n::tr( 'kernel/section', 'Sections' ) ),
                                                     array( 'url' => false,
                                                            'text' => \ezpI18n::tr( 'kernel/section', 'Assign section' ) ) );
                            return $this->viewResult( isset( $Result ) ? $Result : null, null );
                        }
                    }
                }
                else
                {
                    $tpl = \eZTemplate::factory();
                    $tpl->setVariable( 'section_name', $section->attribute( 'name' ) );
                    $tpl->setVariable( 'error_number', 3 );
                    $Result = array();
                    $Result['content'] = $tpl->fetch( "design:section/assign_notification.tpl" );
                    $Result['path'] = array( array( 'url' => false,
                                                    'text' => \ezpI18n::tr( 'kernel/section', 'Sections' ) ),
                                             array( 'url' => false,
                                                    'text' => \ezpI18n::tr( 'kernel/section', 'Assign section' ) ) );
                    return $this->viewResult( isset( $Result ) ? $Result : null, null );
                }
            }
        }
        $Module->redirectTo( '/section/list/' );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
