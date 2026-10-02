<?php
/**
 * The code of cronjobs/hide.php, moved into a class (#207 stage 1). The file cronjobs/hide.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
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

        \eZINI::instance()->setVariable( 'SiteAccessSettings', 'ShowHiddenNodes', 'false' );

        $hiddenNodesParams = array(
            'LoadDataMap' => false,
            'Limit' => 50,
            'SortBy' => array( array( 'published', true ) ) );

        foreach ( $rootNodeIDList as $nodeID )
        {
            $rootNode = \eZContentObjectTreeNode::fetch( $nodeID );
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

                    do
                    {
                        $nodeArray = $rootNode->subTree( $hiddenNodesParams + $countParams );

                        foreach ( $nodeArray as $node )
                        {
                            $cli->output( 'Hiding node: "' . $node->attribute( 'name' ) . '" (' . $node->attribute( 'node_id' ) . ')' );
                            \eZContentObjectTreeNode::hideSubTree( $node );

                            //call appropriate method from search engine
                            \eZSearch::updateNodeVisibility( $node->attribute( 'node_id' ), 'hide' );
                        }
                        // clear memory after every batch
                        \eZContentObject::clearCache();
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
