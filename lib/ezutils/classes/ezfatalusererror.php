<?php
/**
 * File containing the eZFatalUserError class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/**
 * A fatal error raised by the kernel itself, in place of a user error of the level E_USER_ERROR, which PHP 8.4
 * deprecates for trigger_error() ("throw an exception or call exit with a string message instead").
 *
 * raise() does what PHP did with such an error, without the deprecation:
 *
 * - The error handler that is set (eZDebug's, the setup log's, a persistent worker's, the test runner's) gets it
 *   first, with E_USER_ERROR, the message and the file and line of the call to raise(), and is not active while
 *   it runs, as PHP does. When it answers anything but false the error counts as handled and the script goes on,
 *   as it did before: eZDebug's handler, set for every web request, logs the error and returns.
 * - Without a handler, or when the handler answers false, the script stops: raise() throws this error, which nobody
 *   is meant to catch. Uncaught, PHP reports "Fatal error: Uncaught eZFatalUserError: <message>" and ends with the
 *   exit code 255 (an exception handler that is set, such as eZExecution's, renders its error page instead). A
 *   persistent worker (Velocity) answers the request with a 500 and serves the next one, where the fatal error
 *   ended the worker.
 *
 * It extends Error rather than Exception, so that code that catches Exception does not swallow what used to stop
 * the script. Only a handler's level mask is not known to it: a handler set for some levels without E_USER_ERROR
 * would have been passed over by PHP, and is asked here.
 *
 * Works without the autoloader (index_cluster.php): require this file when the class does not exist yet.
 *
 * @package lib
 */
class eZFatalUserError extends Error
{
    /**
     * Raises the fatal user error $message; returns only when the error handler took it.
     *
     * @param string $message
     * @return void
     * @throws eZFatalUserError when no handler took it
     */
    public static function raise( $message )
    {
        $message = (string)$message;
        $trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 1 );
        $file = isset( $trace[0]['file'] ) ? $trace[0]['file'] : __FILE__;
        $line = isset( $trace[0]['line'] ) ? (int)$trace[0]['line'] : __LINE__;

        // the handler that is set; set_error_handler() is the only way to read it before PHP 8.5
        $handler = set_error_handler( static function () { return false; } );
        restore_error_handler();
        if ( $handler !== null )
        {
            // PHP takes the handler off while it runs, so that an error inside it gets PHP's own handling
            set_error_handler( null );
            try
            {
                $handled = call_user_func( $handler, E_USER_ERROR, $message, $file, $line );
            }
            finally
            {
                restore_error_handler();
            }
            if ( $handled !== false )
            {
                return;
            }
        }

        $error = new self( $message );
        $error->file = $file;
        $error->line = $line;
        throw $error;
    }
}
