<?php
/**
 * The code of cronjobs/unpublish.php, moved into a class (#207 stage 1). The file cronjobs/unpublish.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\Cronjob\Kernel
{

class Unpublish extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $ini = \eZINI::instance( 'content.ini' );
        $unpublishClasses = $ini->variable( 'UnpublishSettings','ClassList' );

        $rootNodeIDList = $ini->variable( 'UnpublishSettings','RootNodeList' );

        $currentDate = time();

        foreach( $rootNodeIDList as $nodeID )
        {
            $rootNode = \eZContentObjectTreeNode::fetch( $nodeID );

            $articleNodeArray = $rootNode->subTree( 
                array( 
                    'ClassFilterType' => 'include',
                    'ClassFilterArray' => $unpublishClasses,
                    'Limitation' => array()
                ) 
            );

            foreach ( $articleNodeArray as $articleNode )
            {
                $article = $articleNode->attribute( 'object' );
                $dataMap = $article->attribute( 'data_map' );

                $dateAttribute = $dataMap['unpublish_date'];

                if ( $dateAttribute === null )
                    continue;

                $date = $dateAttribute->content();
                $articleRetractDate = $date->attribute( 'timestamp' );
                if ( $articleRetractDate > 0 && $articleRetractDate < $currentDate )
                {
                    // Clean up content cache
                    \eZContentCacheManager::clearContentCacheIfNeeded( $article->attribute( 'id' ) );

                    $article->removeThis( $articleNode->attribute( 'node_id' ) );
                }
            }
        }
    }
}

}
