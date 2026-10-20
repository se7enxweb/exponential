<?php
/**
 * The contentcheck/scope view (doc/en/us/features/6.0/content-checks.md, "Where a check starts"): chooses the start
 * location of a check with the shared node picker.
 *
 *   GET  contentcheck/scope/<view>/<node id>   opens the node picker (content/browse) at that node
 *   GET  contentcheck/scope/<view>/<node id>/<start node id>   opens it at <start node id> ("More" of the sites row);
 *                                              a cancel still goes back to the check at <node id>
 *   POST from the node picker                  remembers the chosen node and opens contentcheck/<view>/<node id>
 *
 * <view> must be one of contentcheck.ini [ScopeSettings] ViewList[]. The chosen node must be one the user may read;
 * otherwise the check opens where it was. The view needs no policy of its own beyond being signed in: the check it
 * returns to checks its own. Answers on administration siteaccesses only.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Contentcheck
{

class Scope extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $module = $Params['Module'];
        if ( !\expContentCheck::isAvailableHere() )
            return $module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );
        if ( !\eZUser::isCurrentUserRegistered() )
            return $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );

        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? (string)$_SERVER['REQUEST_METHOD'] : 'GET';
        $decision = self::decide( $method, isset( $Params['View'] ) ? $Params['View'] : null,
                                  isset( $Params['NodeID'] ) ? $Params['NodeID'] : null, $_POST );
        if ( $decision['step'] === 'refused' )
            return $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

        if ( $decision['step'] === 'browse' )
        {
            $start = \expContentCheck::readableNode( $decision['node_id'] );
            $startID = $start && $decision['node_id'] > 1 ? $decision['node_id'] : \expContentCheckScope::rootNodeID();
            // "More" of the sites row opens the picker at the content root; a cancel still goes back to the check
            $other = isset( $Params['StartNodeID'] ) ? \expContentCheck::id( $Params['StartNodeID'], 0 ) : 0;
            if ( $other > 1 && \expContentCheck::readableNode( $other ) )
                $startID = $other;
            $back = '/contentcheck/' . $decision['view'] . '/' . (int)$decision['node_id'];
            \eZContentBrowse::browse( array( 'action_name' => \expContentCheckScope::BROWSE_ACTION,
                                             'description_template' => false,
                                             'start_node' => $startID,
                                             'persistent_data' => array( 'ContentCheckView' => $decision['view'],
                                                                         'ContentCheckNodeID' => (int)$decision['node_id'] ),
                                             'from_page' => '/contentcheck/scope',
                                             'cancel_page' => $back ), $module );
            return null;
        }

        // back from the node picker: a node the user may read becomes the start location
        $nodeID = (int)$decision['node_id'];
        if ( $decision['step'] === 'chosen' && \expContentCheck::readableNode( $decision['chosen'] ) )
        {
            $nodeID = (int)$decision['chosen'];
            \expContentCheckScope::remember( $nodeID );
        }
        return $module->redirectTo( '/contentcheck/' . $decision['view'] . '/' . $nodeID );
    }

    /**
     * What a request leads to, from its method, the view parameters and the posted fields only.
     *
     * @param string $method
     * @param mixed $view the view parameter View
     * @param mixed $nodeID the view parameter NodeID
     * @param array $post
     * @return array array( 'step' => browse|chosen|cancel|refused, 'view' => string, 'node_id' => int, 'chosen' => int )
     */
    public static function decide( $method, $view, $nodeID, array $post )
    {
        $decision = array( 'step' => 'refused', 'view' => '', 'node_id' => 0, 'chosen' => 0 );
        if ( strtoupper( (string)$method ) !== 'POST' )
        {
            $decision['view'] = \expContentCheckScope::view( $view ) ?: '';
            $decision['node_id'] = \expContentCheck::id( $nodeID, \expContentCheckScope::rootNodeID() );
            $decision['step'] = $decision['view'] !== '' ? 'browse' : 'refused';
            return $decision;
        }
        $decision['view'] = \expContentCheckScope::view( isset( $post['ContentCheckView'] ) ? $post['ContentCheckView'] : null ) ?: '';
        $decision['node_id'] = \expContentCheck::id( isset( $post['ContentCheckNodeID'] ) ? $post['ContentCheckNodeID'] : null,
                                                    \expContentCheckScope::rootNodeID() );
        if ( $decision['view'] === '' || !\expContentCheck::checkEzFormToken( $post ) )
            return $decision;
        if ( isset( $post['BrowseCancelButton'] ) || !isset( $post['BrowseActionName'] )
             || $post['BrowseActionName'] !== \expContentCheckScope::BROWSE_ACTION )
            return array_merge( $decision, array( 'step' => 'cancel' ) );
        $chosen = \expContentCheck::idList( isset( $post['SelectedNodeIDArray'] ) && is_array( $post['SelectedNodeIDArray'] ) ? $post['SelectedNodeIDArray'] : array(), 1 );
        if ( !$chosen )
            return array_merge( $decision, array( 'step' => 'cancel' ) );
        return array_merge( $decision, array( 'step' => 'chosen', 'chosen' => $chosen[0] ) );
    }
}

}
