<?php
/**
 * File containing the expContentJobRevealSubtree class.
 *
 * The content job 'reveal': reveals a hidden node and its subtree with the semantics of
 * eZContentObjectTreeNode::unhideSubTree() (a node hidden on its own inside stays hidden and keeps its subtree
 * invisible), the view caches cleared in batches. See expContentJobHideSubtree.
 *
 * Parameters: node_id.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobRevealSubtree extends expContentJobHideSubtree
{
    protected $hide = false;
}
