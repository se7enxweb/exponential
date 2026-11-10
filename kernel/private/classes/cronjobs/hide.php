<?php
/**
 * The code of cronjobs/hide.php, moved into a class (#207 stage 1). The file cronjobs/hide.php is one call to it.
 * Guide: doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of cronjobs/hide.php:
 *
 *
 * @description Process scheduled hide and unhide actions on content objects
 *
 * File containing the hide.php cronjob.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\Cronjob\Kernel
{

class Hide extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $ini = \eZINI::instance( 'content.ini' );
        $rootNodeIDList = $ini->variable( 'HideSettings','RootNodeList' );
        $hideAttributeArray = $ini->variable( 'HideSettings', 'HideDateAttributeList' );

        $currentDate = time();

        $siteIni = \eZINI::instance();
        $showHiddenNodesBefore = $siteIni->hasVariable( 'SiteAccessSettings', 'ShowHiddenNodes' )
                               ? $siteIni->variable( 'SiteAccessSettings', 'ShowHiddenNodes' ) : null;
        $siteIni->setVariable( 'SiteAccessSettings', 'ShowHiddenNodes', 'false' );
        // The fetches below must leave out what this run has hidden, or the batch loop never ends; the value is
        // cached, so a fetch made earlier in this process (an earlier cronjob part) would keep ShowHiddenNodes=true
        \eZContentObjectTreeNode::resetShowInvisibleNodesCache();
        try
        {
            $this->hideNodes( $cli, $rootNodeIDList, $hideAttributeArray, $currentDate );
        }
        finally
        {
            // the cronjob parts after this one in the same process see the site's own ShowHiddenNodes again
            if ( $showHiddenNodesBefore === null )
                $siteIni->removeSetting( 'SiteAccessSettings', 'ShowHiddenNodes' );
            else
                $siteIni->setVariable( 'SiteAccessSettings', 'ShowHiddenNodes', $showHiddenNodesBefore );
            \eZContentObjectTreeNode::resetShowInvisibleNodesCache();
        }
    }

    /**
     * Hides the nodes below each root node whose date in its class's attribute has passed. Runs with
     * ShowHiddenNodes=false (see run()).
     *
     * @param \eZCLI $cli
     * @param int[] $rootNodeIDList content.ini [HideSettings] RootNodeList
     * @param string[] $hideAttributeArray content.ini [HideSettings] HideDateAttributeList, class => attribute
     * @param int $currentDate
     */
    protected function hideNodes( $cli, array $rootNodeIDList, array $hideAttributeArray, $currentDate )
    {
        $hiddenNodesParams = array(
            'LoadDataMap' => false,
            'Limit' => 50,
            'SortBy' => array( array( 'published', true ) ) );

        foreach ( $rootNodeIDList as $nodeID )
        {
            $rootNode = \eZContentObjectTreeNode::fetch( $nodeID );
            if ( !$rootNode instanceof \eZContentObjectTreeNode )
            {
                // a node of RootNodeList that was removed: the other root nodes are still processed
                $cli->warning( "The node $nodeID of content.ini [HideSettings] RootNodeList does not exist; skipped." );
                continue;
            }
            $cli->output( 'Hiding content of node "' . $rootNode->attribute( 'name' ) . '" (' . $nodeID . ')' );
            $cli->output();

            foreach ( $hideAttributeArray as $hideClass => $attributeIdentifier )
            {
                $countParams = array( 'ClassFilterType' => 'include',
                                      'ClassFilterArray' => array( $hideClass ),
                                      'Limitation' => array(),
                                      'AttributeFilter' => array( 'and',
                                          array( "{$hideClass}/{$attributeIdentifier}", '<=', $currentDate ),
                                          array( "{$hideClass}/$attributeIdentifier", '>', 0 ) ) );

                $nodeArrayCount = $rootNode->subTreeCount( $countParams );
                if ( $nodeArrayCount > 0 )
                {
                    $cli->output( "Hiding {$nodeArrayCount} node(s) of class {$hideClass}." );

                    $hiddenNodeIDs = array();
                    do
                    {
                        $nodeArray = $rootNode->subTree( $hiddenNodesParams + $countParams );
                        $newInBatch = 0;

                        foreach ( $nodeArray as $node )
                        {
                            $hideNodeID = (int)$node->attribute( 'node_id' );
                            if ( isset( $hiddenNodeIDs[$hideNodeID] ) )
                                continue;
                            $hiddenNodeIDs[$hideNodeID] = true;
                            $newInBatch++;
                            $cli->output( 'Hiding node: "' . $node->attribute( 'name' ) . '" (' . $hideNodeID . ')' );
                            \eZContentObjectTreeNode::hideSubTree( $node );

                            //call appropriate method from search engine
                            \eZSearch::updateNodeVisibility( $hideNodeID, 'hide' );
                        }
                        // clear memory after every batch
                        \eZContentObject::clearCache();

                        // A batch of nodes this run has hidden already: the fetch still returns hidden nodes, and
                        // fetching again would return the same batch for ever
                        if ( is_array( $nodeArray ) && !empty( $nodeArray ) && $newInBatch === 0 )
                        {
                            $cli->warning( 'The fetch returned only nodes hidden by this run already; stopped to avoid an endless loop.' );
                            break;
                        }
                    } while ( is_array( $nodeArray ) && !empty( $nodeArray ) );

                    $cli->output();
                }
                else
                {
                    $cli->output( "Nothing to hide." );
                }
            }

            $cli->output();
        }
    }
}

}
