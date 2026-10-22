<?php
/**
 * File containing the expImageEditorException class
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expimageeditor
 */

/**
 * A request the image editor refuses: the message (English, translated when shown) and the HTTP status to answer with.
 *
 * @package expimageeditor
 */
class expImageEditorException extends RuntimeException
{
    /** @var int */
    private $status;

    /**
     * @param int $status HTTP status
     * @param string $message
     */
    public function __construct( $status, $message )
    {
        parent::__construct( $message );
        $this->status = (int)$status;
    }

    /** @return int */
    public function status()
    {
        return $this->status;
    }
}
