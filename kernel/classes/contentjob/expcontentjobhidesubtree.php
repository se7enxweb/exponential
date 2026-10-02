<?php
/**
 * File containing the expContentJobHideSubtree class.
 *
 * The content job 'hide' (and, through expContentJobRevealSubtree, 'reveal'): hides or reveals a node and its
 * subtree with the semantics of eZContentObjectTreeNode::hideSubTree() / unhideSubTree() and the 'hide'
 * operation of content/hide (eZContentOperationCollection::changeHideStatus()):
 *
 *   main   the node's is_hidden and the subtree's is_invisible, the same UPDATE statements as the kernel (a hidden
 *          node inside stays hidden, its subtree invisible), the node's modified_subnode, the content/cache event
 *          and the search engine's updateNodeVisibility() for the node, all in one transaction
 *   nodes  the view cache of every object of the subtree, in batches (the kernel clears them all at once in
 *          clearViewCacheForSubtree(), which is what makes a large hide slow)
 *
 * Permission: can_hide on the node (content/hide), as the requesting user. Idempotent: hiding a hidden node or
 * revealing a visible one changes nothing in the tree and only clears the caches.
 *
 * Parameters: node_id.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobHideSubtree extends expContentJobBatchType
{
    /** @var bool true: hide, false: reveal */
    protected $hide = true;

    protected function validateParams( array $params, eZUser $user )
    {
        $node = $this->requireNode( $params, 'node_id' );
        if ( !$node->attribute( 'can_hide' ) )
            throw new expContentJobException( self::tr( $this->hide ? 'You do not have permission to hide the node %1.' : 'You do not have permission to reveal the node %1.',
                                                        array( $node->attribute( 'node_id' ) ) ) );
        return array( 'node_id' => (int) $node->attribute( 'node_id' ), 'path' => $node->attribute( 'path_string' ) );
    }

    public function locks( array $params )
    {
        return array( array( 'path' => $params['path'], 'mode' => expContentJobLock::SUBTREE ) );
    }

    public function describe( array $params )
    {
        return self::tr( $this->hide ? 'Hide the subtree of node %1' : 'Reveal the subtree of node %1', array( $params['node_id'] ) );
    }

    protected function hasMainStep()
    {
        return true;
    }

    protected function mainStep( expContentJob $job, array &$cp )
    {
        $p = $job->params();
        $node = eZContentObjectTreeNode::fetch( $p['node_id'] );
        if ( !$node )
            throw new expContentJobException( self::tr( 'The node %1 does not exist any more.', array( $p['node_id'] ) ) );
        $isHidden = (bool) $node->attribute( 'is_hidden' );
        if ( $isHidden === $this->hide )
            return self::tr( $this->hide ? 'The node is hidden already.' : 'The node is visible already.' );

        $nodeID = (int) $node->attribute( 'node_id' );
        $db = eZDB::instance();
        $nodePath = $db->escapeString( $node->attribute( 'path_string' ) );
        $time = time();
        // Who hides or reveals which subtree (doc/bc/6.0/audit.md, content.node.hide / content.node.reveal); the
        // record is a child of the job's run (expContentJobWorker), with the job id
        if ( class_exists( 'expAuditHook' ) )
        {
            $hide = $this->hide;
            expAuditHook::emit( $hide ? 'content.node.hide' : 'content.node.reveal', function () use ( $node, $hide ) {
                $parent = $node->attribute( 'parent' );
                return array( 'object' => expAuditHook::node( $node ),
                              'before' => array( 'hidden' => (bool)$node->attribute( 'is_hidden' ), 'invisible' => (bool)$node->attribute( 'is_invisible' ) ),
                              'after' => array( 'hidden' => $hide,
                                                'invisible' => $hide ? true : ( $parent ? (bool)$parent->attribute( 'is_invisible' ) : false ) ) );
            } );
        }
        if ( $this->hide )
        {
            // eZContentObjectTreeNode::hideSubTree()
            if ( !$node->attribute( 'is_invisible' ) )
            {
                $db->query( "UPDATE ezcontentobject_tree SET is_hidden=1, is_invisible=1, modified_subnode=$time WHERE node_id=$nodeID" );
                $db->query( "UPDATE ezcontentobject_tree SET is_invisible=1, modified_subnode=$time WHERE is_invisible=0 AND path_string LIKE '$nodePath%'" );
            }
            else
                $db->query( "UPDATE ezcontentobject_tree SET is_hidden=1, modified_subnode=$time WHERE node_id=$nodeID" );
        }
        else
        {
            // eZContentObjectTreeNode::unhideSubTree()
            $parentNode = $node->attribute( 'parent' );
            if ( !$parentNode instanceof eZContentObjectTreeNode )
                throw new expContentJobException( self::tr( 'The parent of node %1 does not exist.', array( $nodeID ) ) );
            if ( !$parentNode->attribute( 'is_invisible' ) )
            {
                $db->query( "UPDATE ezcontentobject_tree SET is_invisible=0, is_hidden=0, modified_subnode=$time WHERE node_id=$nodeID" );
                $hiddenChildren = $db->arrayQuery( "SELECT path_string FROM ezcontentobject_tree WHERE node_id <> $nodeID AND is_hidden=1 AND path_string LIKE '$nodePath%'" );
                $skip = '';
                foreach ( $hiddenChildren as $i )
                    $skip .= " AND path_string NOT LIKE '" . $db->escapeString( $i['path_string'] ) . "%'";
                $db->query( "UPDATE ezcontentobject_tree SET is_invisible=0, modified_subnode=$time WHERE path_string LIKE '$nodePath%' $skip" );
            }
            else
                $db->query( "UPDATE ezcontentobject_tree SET is_hidden=0, modified_subnode=$time WHERE node_id=$nodeID" );
        }
        $node->updateAndStoreModified();
        // eZContentOperationCollection::changeHideStatus(): the cache event and the search engine
        ezpEvent::getInstance()->notify( 'content/cache', array( array( $nodeID ), array( (int) $node->attribute( 'contentobject_id' ) ) ) );
        eZSearch::updateNodeVisibility( $nodeID, $this->hide ? 'hide' : 'show' );
        $cp['changed']++;
        return self::tr( $this->hide ? 'Hidden; now the caches of the subtree' : 'Revealed; now the caches of the subtree' );
    }

    protected function processNodes( expContentJob $job, array $rows, array &$cp )
    {
        self::clearViewCaches( self::objectIDs( $rows ) );
    }
}
