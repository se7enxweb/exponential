<?php
/**
 * The shipped .htaccess templates let a request finish under LiteSpeed when the client has closed the connection
 * (doc/eng-us/guides/litespeed-page-cache.md, "Fatal error lines without a message"). No database.
 *
 *  HL-01  .htaccess_root and .htaccess_root_static set LiteSpeed's noabort right after RewriteEngine On, inside
 *         <IfModule LiteSpeed>
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expHtaccessLiteSpeedNoAbortTest extends PHPUnit\Framework\TestCase
{
    /** HL-01 */
    public function testTheTemplatesLetARequestFinishUnderLiteSpeed()
    {
        foreach ( array( '.htaccess_root', '.htaccess_root_static' ) as $name )
        {
            $text = (string)file_get_contents( dirname( __DIR__, 4 ) . '/' . $name );
            $this->assertMatchesRegularExpression( '/^RewriteEngine On\n(#[^\n]*\n)*<IfModule LiteSpeed>\nRewriteRule \.\* - \[E=noabort:1\]\n<\/IfModule>\n/m', $text, $name );
        }
    }
}
