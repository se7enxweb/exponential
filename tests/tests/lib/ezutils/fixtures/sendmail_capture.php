<?php
/**
 * Stands in for sendmail in eZSendmailEnvelopeSenderTest: writes the arguments mail() handed it and the message it
 * piped in to the file named by the first argument, as JSON.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

$captured = array( 'args' => array_slice( $argv, 2 ), 'message' => stream_get_contents( STDIN ) );
file_put_contents( $argv[1], json_encode( $captured ) );
