<?php
/**
 * File containing the eZDBException class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 3d18f47551), Copyright (C) Mugo Web, GPL v2
 * @version //autogentag//
 * @package kernel
 */

/**
 * Class representing a the top class for any
 * database related exception
 *
 * getCode() is the database driver's error number of the failed statement, the same as
 * eZDBInterface::errorNumber(): MySQL's error number (1062 duplicate entry, 1146 table
 * missing), SQLite's result code (19 constraint), Oracle's ORA number (1, 942),
 * PostgreSQL's SQLSTATE as a string ('23505', '42P01'). Code can tell "duplicate key"
 * from "table missing" without reading the message.
 *
 * @version //autogentag//
 * @package kernel
 */
class eZDBException extends ezcBaseException
{
    /**
     * Original message, before escaping
     */
    public $originalMessage;

    /**
     * Constructs a new eZDBException with $message and $code
     *
     * @param string $message
     * @param int $code
     */
    public function __construct( $message, $code = 0 )
    {
        $this->originalMessage = $message;
        $this->code = $code;

        if ( php_sapi_name() == 'cli' )
        {
            $this->message = $message;
        }
        else
        {
            $this->message = htmlspecialchars( $message, ENT_QUOTES, 'UTF-8' );
        }
    }
}
?>
