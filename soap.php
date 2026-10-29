<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*
 * Deprecated: the SOAP entry script is index_soap.php since Exponential 6.0.15.
 *
 * This file only forwards to it, so SOAP clients and web server rules that
 * still name soap.php keep working for now. Change the rewrite rules, the
 * virtual host and the clients (the syndication extension, middleware) to
 * index_soap.php; this shim will be removed in a later major version.
 * What to change and how to test it: doc/eng-us/bc/6.0/soap-entry-script-index-soap.md.
 *
 * Once a day a request that comes through here writes one notice to
 * var/log/warning.log, so an administrator sees that something still calls
 * soap.php. The day is kept as the date of var/log/soap-php-deprecated.stamp.
 * No function is declared here: a persistent worker may include this file more
 * than once.
 */
$expSoapShimStamp = __DIR__ . '/var/log/soap-php-deprecated.stamp';
clearstatcache( true, $expSoapShimStamp );
$expSoapShimLast = @filemtime( $expSoapShimStamp );
if ( ( $expSoapShimLast === false || date( 'Ymd', $expSoapShimLast ) !== date( 'Ymd' ) )
     && is_dir( __DIR__ . '/var/log' ) && @touch( $expSoapShimStamp ) )
{
    @file_put_contents(
        __DIR__ . '/var/log/warning.log',
        '[ ' . date( 'M d Y H:i:s' ) . ' ] [' . ( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '' ) . '] soap.php:' . "\n"
        . 'A request came through soap.php, the deprecated name of the SOAP entry script. It still answers, but '
        . 'change the web server rules and the SOAP clients to index_soap.php (Exponential 6.0.15). '
        . 'Requested URI: ' . preg_replace( '/[\x00-\x1f\x7f]/', '', isset( $_SERVER['REQUEST_URI'] ) ? (string)$_SERVER['REQUEST_URI'] : '' ) . "\n",
        FILE_APPEND | LOCK_EX
    );
}
unset( $expSoapShimStamp, $expSoapShimLast );

require __DIR__ . '/index_soap.php';
