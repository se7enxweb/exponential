<?php
/**
 * The tree menu front controller (index_treemenu.php) answered every error with
 * "500 Internal Server Error", also when it refused a visitor without access to the
 * siteaccess. A tree menu request that reached the server just after a logout (the
 * page was still loading its menu) was therefore logged as a server error, although
 * nothing was wrong. The status now follows the error code: 403 for no access, 500
 * for the real failures (no database, no content module).
 *
 * No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expTreeMenuErrorStatusTest extends PHPUnit\Framework\TestCase
{
    public function testAccessDeniedIsForbidden()
    {
        $this->assertSame( '403 Forbidden', ezpKernelTreeMenu::statusForErrorCode( 403 ) );
        $this->assertSame( '403 Forbidden', ezpKernelTreeMenu::statusForErrorCode( '403' ) );
    }

    public function testOtherErrorsStayInternalServerError()
    {
        $this->assertSame( '500 Internal Server Error', ezpKernelTreeMenu::statusForErrorCode( 500 ) );
        $this->assertSame( '500 Internal Server Error', ezpKernelTreeMenu::statusForErrorCode( 0 ) );
    }

    public function testErrorExitSendsTheStatusOfTheErrorCode()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/kernel/private/classes/ezpkerneltreemenu.php' );
        $start = strpos( $source, 'protected function exitWithInternalError(' );
        $this->assertNotFalse( $start );
        $body = substr( $source, $start, 1500 );
        $this->assertStringContainsString( 'self::statusForErrorCode( $errorCode )', $body );
        $this->assertStringNotContainsString( "' 500 Internal Server Error'", $body );
    }
}
