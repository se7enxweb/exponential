<?php
/**
 * dse/adminneo answers with AdminNeo's own page and Content-Security-Policy in every request of a persistent
 * Exponential Velocity worker (#619). No database.
 *
 * The fault: dse/dashboard defined NEO_BASE_URL (AdminNeo is embedded in the dashboard) and then ended the request on
 * its warning gate or a redirect before it asked for the worker to be replaced. The next dse/adminneo request in that
 * worker found the constant, AdminNeo took itself for embedded, left out its document head and its
 * Content-Security-Policy with the nonce of its inline scripts, and the kernel's enforced policy blocked them.
 *
 * - dse/dashboard asks for the worker to be retired before it defines any constant and before any path that ends
 *   the request;
 * - dse/adminneo asks for it first as well, recognises a process that already ran AdminNeo or was prepared for it,
 *   and then answers 503 with a page that reloads itself (no script) instead of a broken AdminNeo;
 * - dse/adminneo removes the kernel's report-only companion policy, so the page carries AdminNeo's policy only.
 *
 * Skips when extension/sevenx_dse is not installed; SEVENX_DSE_EXTENSION_DIR=<dir> checks a clone instead.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/sevenx_dse/SevenxDseAdminNeoWorkerStateTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class SevenxDseAdminNeoWorkerStateTest extends PHPUnit\Framework\TestCase
{
    const VIEW = 'classes/runnable/views/dse/adminneo.php';
    const DASHBOARD = 'classes/runnable/views/dse/dashboard.php';
    const VIEW_CLASS = 'Exponential\View\Extension\SevenxDse\Dse\Adminneo';

    private static function ext()
    {
        return getenv( 'SEVENX_DSE_EXTENSION_DIR' ) ?: dirname( __DIR__, 4 ) . '/extension/sevenx_dse';
    }

    private function loadView()
    {
        $ext = self::ext();
        if ( !is_file( $ext . '/' . self::VIEW ) || !is_file( $ext . '/' . self::DASHBOARD ) )
        {
            $this->markTestSkipped( 'extension/sevenx_dse is not installed' );
        }
        if ( !class_exists( self::VIEW_CLASS, false ) )
        {
            if ( !class_exists( 'Exponential\Runnable\ModuleView' ) )
            {
                $this->markTestSkipped( 'the kernel runnable classes are not autoloadable' );
            }
            require_once $ext . '/' . self::VIEW;
        }
    }

    /** The body of run() of a view file, comments removed */
    private function runBody( $file )
    {
        $source = (string)file_get_contents( self::ext() . '/' . $file );
        $this->assertSame( 1, preg_match( '~public function run\(.*?\n    \}\n~s', $source, $m ), 'run() found in ' . $file );
        return preg_replace( '~//[^\n]*~', '', $m[0] );
    }

    public function testAFreshProcessHasNotRunAdminNeo()
    {
        $this->loadView();
        if ( defined( 'NEO_BASE_URL' ) || defined( 'AdminNeo\SERVER' ) )
        {
            $this->markTestSkipped( 'AdminNeo ran in this test process' );
        }
        $class = self::VIEW_CLASS;
        $this->assertFalse( $class::processRanAdminNeo() );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState( false )]
    public function testAProcessPreparedByTheDashboardIsRecognised()
    {
        $this->loadView();
        define( 'NEO_BASE_URL', '/admin/dse/dashboard' );
        $class = self::VIEW_CLASS;
        $this->assertTrue( $class::processRanAdminNeo() );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState( false )]
    public function testAProcessThatRanAdminNeoIsRecognised()
    {
        $this->loadView();
        define( 'AdminNeo\SERVER', 'localhost' );
        $class = self::VIEW_CLASS;
        $this->assertTrue( $class::processRanAdminNeo() );
    }

    public function testReloadPageReloadsItselfWithoutScriptOrStyle()
    {
        $this->loadView();
        $class = self::VIEW_CLASS;
        $page = $class::reloadSoonPage();
        $this->assertStringStartsWith( '<!DOCTYPE html>', $page );
        $this->assertStringContainsString( '<meta http-equiv="refresh" content="1">', $page );
        $this->assertStringNotContainsStringIgnoringCase( '<script', $page );
        $this->assertStringNotContainsStringIgnoringCase( 'style', $page );
    }

    public function testRemovingTheReportOnlyPolicyWithoutOneIsHarmless()
    {
        $this->loadView();
        $class = self::VIEW_CLASS;
        // The CLI keeps no headers: nothing was set, nothing is removed, nothing fails
        $this->assertFalse( $class::removeKernelReportOnlyPolicy() );
    }

    public function testAdminNeoViewRetiresTheWorkerAndChecksItBeforeAnythingElse()
    {
        $this->loadView();
        $run = $this->runBody( self::VIEW );
        $retire = strpos( $run, 'self::retireWorkerAfterResponse()' );
        $check = strpos( $run, 'self::processRanAdminNeo()' );
        $define = strpos( $run, 'define(' );
        $session = strpos( $run, 'session_write_close()' );
        $removeReportOnly = strpos( $run, 'self::removeKernelReportOnlyPolicy()' );
        $include = strpos( $run, 'self::includeAdminNeo(' );
        foreach ( array( 'retire' => $retire, 'check' => $check, 'define' => $define, 'session' => $session,
                         'report-only' => $removeReportOnly, 'include' => $include ) as $what => $at )
        {
            $this->assertNotFalse( $at, $what . ' found in run()' );
        }
        $this->assertLessThan( $check, $retire, 'the worker is retired before the check' );
        $this->assertLessThan( $define, $check, 'the check comes before this view defines a constant' );
        $this->assertLessThan( $session, $check, 'the check comes before the session is switched' );
        $this->assertLessThan( $include, $removeReportOnly, "the kernel's report-only policy goes before AdminNeo runs" );
        $this->assertSame( 1, preg_match( '~if \( self::processRanAdminNeo\(\) \)\s*\{\s*self::answerReloadSoon\(\);\s*\\\\eZExecution::cleanExit\(\);\s*\}~', $run ),
                           'a used process answers the reload page and ends the request' );
    }

    public function testDashboardRetiresTheWorkerBeforeItDefinesAConstantOrEndsTheRequest()
    {
        $this->loadView();
        $run = $this->runBody( self::DASHBOARD );
        $retire = strpos( $run, "Q_WebServer_Pool::retireAfterResponse(" );
        $this->assertNotFalse( $retire, 'the dashboard asks for the worker to be retired' );
        $this->assertSame( 1, substr_count( $run, "Q_WebServer_Pool::retireAfterResponse(" ), 'once' );
        foreach ( array( 'define(', "'NEO_BASE_URL'", 'exit;', 'return ', 'include ' ) as $needle )
        {
            $at = strpos( $run, $needle );
            $this->assertNotFalse( $at, $needle . ' found in the dashboard' );
            $this->assertLessThan( $at, $retire, 'retired before the first ' . $needle );
        }
    }

    public function testAdminNeoSkipsItsHeadersOnlyWhenEmbedded()
    {
        // The mechanism of the fault: AdminNeo sends no header and no document head at all while NEO_BASE_URL is
        // defined. If this changes, the checks above may no longer be needed, but they stay correct.
        $design = self::ext() . '/adminneo/include/design.inc.php';
        if ( !is_file( $design ) )
        {
            $this->markTestSkipped( 'AdminNeo is not installed' );
        }
        $source = (string)file_get_contents( $design );
        $this->assertSame( 1, preg_match( '~function page_headers\(\): void\s*\{.*?if \(defined\(\'NEO_BASE_URL\'\)\) \{\s*return;~s', $source ) );
        $this->assertStringContainsString( 'header("Content-Security-Policy: "', $source );
    }
}
