<?php
/**
 * The code of kernel/content/dashboard.php, moved into a class (#207 stage 1). The file kernel/content/dashboard.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/dashboard.php:
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

class Dashboard extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $ini = \eZINI::instance( 'dashboard.ini' );
        $currentUser = \eZUser::currentUser();

        $orderedBlocks = self::visibleBlocks( $ini, $currentUser );

        $contentInfoArray = array();

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'blocks', $orderedBlocks );
        $tpl->setVariable( 'user', $currentUser );
        $tpl->setVariable( 'persistent_variable', false );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/dashboard.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'Dashboard' ),
                                        'url' => false ) );

        $contentInfoArray['persistent_variable'] = false;
        if ( $tpl->variable( 'persistent_variable' ) !== false )
            $contentInfoArray['persistent_variable'] = $tpl->variable( 'persistent_variable' );

        $Result['content_info'] = $contentInfoArray;

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
    /**
     * The blocks of dashboard.ini [DashboardSettings] DashboardBlocks[] the user may see, in priority order:
     * a block is left out unless the user has every policy of its PolicyList[] ("<module>/<function>", or a
     * node id to be readable) and can open every address of its ViewList[] (expViewAccess, checked the way
     * the kernel checks the request).
     *
     * @param \eZINI $ini dashboard.ini
     * @param \eZUser $currentUser
     * @return array priority => array( 'identifier', 'template', 'number_of_items' )
     */
    public static function visibleBlocks( \eZINI $ini, \eZUser $currentUser )
    {
        $orderedBlocks = array();

        $dashboardBlocks = $ini->variable( 'DashboardSettings', 'DashboardBlocks' );

        foreach( $dashboardBlocks as $blockIdentifier )
        {
            $blockGroupName = 'DashboardBlock_' . $blockIdentifier;
            if ( !$ini->hasGroup( $blockGroupName ) )
                continue;

            $hasAccess = true;
            if ( $ini->hasVariable( $blockGroupName, 'PolicyList' ) )
            {
                $policyList = $ini->variable( $blockGroupName, 'PolicyList' );
                foreach( $policyList as $policy )
                {
                    // Value is either "<node_id>" or "<module>/<function>"
                    if ( strpos( $policy, '/' ) !== false )
                    {
                        list( $module, $function ) = explode( '/', $policy );
                            $result = $currentUser->hasAccessTo( $module, $function );

                        if ( $result['accessWord'] === 'no' )
                        {
                            $hasAccess = false;
                            break;
                        }
                    }
                    else
                    {
                        $node = \eZContentObjectTreeNode::fetch( $policy );
                        if ( !$node instanceof \eZContentObjectTreeNode || !$node->attribute('can_read') )
                        {
                            $hasAccess = false;
                            break;
                        }
                    }
                }
            }

            // ViewList[]: the addresses the block links to must open for the user, checked the way the
            // kernel checks the request (the view's policies with their limitations, the siteaccess)
            if ( $hasAccess && $ini->hasVariable( $blockGroupName, 'ViewList' ) )
            {
                foreach( (array)$ini->variable( $blockGroupName, 'ViewList' ) as $viewURI )
                {
                    if ( $viewURI !== '' && !\expViewAccess::canOpen( $viewURI, $currentUser ) )
                    {
                        $hasAccess = false;
                        break;
                    }
                }
            }

            if ( $hasAccess === false )
                continue;

            $priority = 0;
            if ( $ini->hasVariable( $blockGroupName, 'Priority' ) )
                $priority = $ini->variable( $blockGroupName, 'Priority' );

            $numberOfItems = null;
            if ( $ini->hasVariable( $blockGroupName, 'NumberOfItems' ) )
                $numberOfItems = $ini->variable( $blockGroupName, 'NumberOfItems' );

            $template = null;
            if ( $ini->hasVariable( $blockGroupName, 'Template' ) )
                $template = $ini->variable( $blockGroupName, 'Template' );

            while( isset( $orderedBlocks[$priority]  ) )
                $priority++;

            $orderedBlocks[$priority] = array( 'identifier' => $blockIdentifier,
                                               'template' => $template,
                                               'number_of_items' => $numberOfItems );
        }

        // Sort $orderedBlocks by key, starting from the lowest priority
        ksort( $orderedBlocks );

        return $orderedBlocks;
    }
}

}
