<?php
/**
 * The error page for a known cause (lib/ezutils/classes/ezexecution.php): errorCase() and renderErrorCasePage().
 *
 *  EC-01 — errorCase() is '' when the Composer libraries are present (they are, here)
 *  EC-02 — The 'dependencies' page carries the title, the three steps for the administrator, the repair
 *          panel placeholder replaced, the logo and the footer
 *  EC-03 — Steps name the commands in <code>; the reference and the detail are escaped and shown when given
 *  EC-04 — Without a reference and detail neither appears
 *  EC-05 — An unknown case falls back to the general 500 page
 *  EC-06 — A repair request (exp_repair=status) is answered by the repair queue instead of the page
 *
 * Reads, never writes, the real repair settings: the panel is the form when a key is set up, else the hint.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group errorcase
 */

require_once __DIR__ . '/../../../../lib/ezutils/classes/ezexecution.php';

class ErrorCasePageTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    protected function setUp(): void
    {
        $this->saved = array( $_REQUEST, $_GET, $_POST );
        $_REQUEST = $_GET = $_POST = array();
    }

    protected function tearDown(): void
    {
        list( $_REQUEST, $_GET, $_POST ) = $this->saved;
    }

    private function page( $case = 'dependencies', $reference = '', $detail = '' )
    {
        ob_start();
        try
        {
            eZExecution::renderErrorCasePage( $case, $reference, $detail );
        }
        finally
        {
            $out = ob_get_clean();
        }
        return $out;
    }

    /** EC-01 */
    public function testErrorCaseIsEmptyWithLibraries()
    {
        $this->assertSame( '', eZExecution::errorCase() );
    }

    /** EC-02 */
    public function testDependenciesPage()
    {
        $html = $this->page();
        $this->assertStringContainsString( '<title>The site is missing the software libraries it needs</title>', $html );
        $this->assertStringContainsString( '<h1>The site is missing the software libraries it needs</h1>', $html );
        $this->assertStringContainsString( '503', $html );
        $this->assertSame( 3, substr_count( $html, '<li>' ) - substr_count( $html, '<li data-step' ), 'three steps' );
        $this->assertStringContainsString( 'put it back in the installation directory', $html );
        $this->assertStringContainsString( 'composer install', $html );
        $this->assertStringContainsString( 'restart it', $html );
        $this->assertStringContainsString( 'For the administrator', $html );
        $this->assertStringContainsString( 'logo-light.png', $html );
        $this->assertStringContainsString( 'alt="Exponential"', $html );
        $this->assertStringContainsString( '<footer>Powered by <strong>Exponential</strong>', $html );
        $this->assertStringContainsString( '1998-' . date( 'Y' ), $html );
        $this->assertStringNotContainsString( '{repair}', $html );
        $this->assertStringNotContainsString( '{steps}', $html );
        $this->assertTrue( strpos( $html, 'id="exp-repair"' ) !== false || strpos( $html, 'exprepair.php --create-key' ) !== false,
            'the repair panel: the form, or how to create a key' );
    }

    /** EC-03 */
    public function testStepsReferenceAndDetail()
    {
        $html = $this->page( 'dependencies', 'ref<1>', 'trace "x" <b>' );
        $this->assertStringContainsString( '<code>composer install (add --no-dev on a production server)</code>', $html );
        $this->assertStringContainsString( '<code>php bin/php/ezcache.php --clear-all</code>', $html );
        $this->assertStringContainsString( 'Reference: ref&lt;1&gt;', $html );
        $this->assertStringContainsString( 'trace &quot;x&quot; &lt;b&gt;', $html );
        $this->assertStringNotContainsString( 'ref<1>', $html );
    }

    /** EC-04 */
    public function testNoReferenceNoDetail()
    {
        $html = $this->page();
        $this->assertStringNotContainsString( 'Reference:', $html );
        $this->assertStringNotContainsString( '<pre style="white-space:pre-wrap">', $html );
    }

    /** EC-05 */
    public function testUnknownCaseUsesTheGeneralPage()
    {
        $html = $this->page( 'no-such-case', 'r1' );
        $this->assertStringContainsString( 'Something went wrong on our side', $html );
        $this->assertStringContainsString( 'Reference: r1', $html );
        $this->assertStringNotContainsString( 'software libraries', $html );
    }

    /** EC-06 */
    public function testRepairRequestIsAnsweredByTheQueue()
    {
        $_REQUEST = array( 'exp_repair' => 'status' );
        $_GET = array( 'token' => 'does-not-exist' );
        $out = $this->page();
        $this->assertSame( array( 'ok' => false, 'error' => 'unknown' ), json_decode( $out, true ) );
        $this->assertStringNotContainsString( '<html', $out );
    }
}
