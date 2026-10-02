<?php
/**
 * File containing the expContentJobException class.
 *
 * The error a content job refuses or fails with; its message is shown to the user. A failure about one node
 * carries its id (forNode()), kept in the job as error_node_id so the job page can link it.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobException extends Exception
{
    /** @var int the node the error is about, 0 when none */
    public $nodeID = 0;

    /**
     * An exception about one node.
     *
     * @param string $message
     * @param int $nodeID
     * @return expContentJobException
     */
    public static function forNode( $message, $nodeID )
    {
        $e = new self( $message );
        $e->nodeID = (int) $nodeID;
        return $e;
    }
}
