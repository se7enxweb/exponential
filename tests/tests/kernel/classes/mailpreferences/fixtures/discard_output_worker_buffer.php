<?php
/**
 * MailPreferencesHardeningTest MPH-01, run in a process of its own: a buffer that cannot be removed, as a persistent
 * worker (Velocity) keeps one under the script, and one the page opened above it. MailPreferencesPage::discardOutput()
 * must return at once (it uses eZExecution::discardOutputBuffers()). The result goes to STDOUT directly (fwrite bypasses the output buffers) as JSON.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require dirname( __DIR__, 6 ) . '/lib/ezutils/classes/ezexecution.php';
require dirname( __DIR__, 6 ) . '/kernel/private/classes/services/mailpreferencespage.php';

$captured = '';
ob_start( function ( $buffer, $phase ) use ( &$captured ) {
    if ( !( $phase & PHP_OUTPUT_HANDLER_CLEAN ) )
        $captured .= $buffer;
    return '';
}, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_FLUSHABLE );
$workerLevel = ob_get_level();
echo 'printed before the page';
ob_start();
echo 'printed by the page';
$started = microtime( true );
$clean = \Exponential\Service\MailPreferencesPage::discardOutput();
$seconds = microtime( true ) - $started;
$level = ob_get_level();
$length = (int)ob_get_length();
echo 'the answer';
@ob_flush();
fwrite( STDOUT, json_encode( array( 'clean' => $clean, 'seconds' => $seconds, 'level' => $level, 'worker_level' => $workerLevel,
                                    'length' => $length, 'captured' => $captured ) ) );
// the buffer cannot be removed; end the process without letting PHP flush it to the real output
$captured = '';
exit( 0 );
