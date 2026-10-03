<?php
require_once __DIR__ . '/../media/expMediaTestCase.php';

/** exppdf: PDF links and the export definitions. */
class expPdfServicesTest extends expMediaTestCase
{
    public function testEveryServiceIsDeclared()
    {
        $this->assertDeclared( 'expPdfServices', 10 );
        foreach ( expPdfServices::$services as $m => $d )
            $this->assertFalse( $d['write'], "$m" );
    }

    public function testAvailable()
    {
        $a = $this->ok( 'expPdfServices', 'available' )['data'];
        $this->assertTrue( $a['available'] );
        $this->assertGreaterThanOrEqual( 0, $a['exports'] );
    }

    public function testCanPdf()
    {
        $this->assertTrue( $this->ok( 'expPdfServices', 'canpdf', array( 2 ) )['data']['can_pdf'] );
        $this->fails( 404, 'expPdfServices', 'canpdf', array( 99999999 ) );
    }

    public function testLinkPointsToContentPdf()
    {
        $l = $this->ok( 'expPdfServices', 'link', array( 2 ) )['data'];
        $this->assertStringContainsString( 'content/pdf/2', $l['url'] );
        $this->assertStringStartsWith( 'http', $l['absolute_url'] );
    }

    public function testLinkWithALanguage()
    {
        $top = eZContentLanguage::topPriorityLanguage()->attribute( 'locale' );
        $this->assertStringContainsString( '(language)/' . $top, $this->ok( 'expPdfServices', 'link', array( 2, $top ) )['data']['url'] );
        $this->fails( 404, 'expPdfServices', 'link', array( 2, 'xxx-XX' ) );
    }

    public function testLinksForSeveralNodes()
    {
        $r = $this->ok( 'expPdfServices', 'links', array( '2,43,99999999' ) );
        $this->assertSame( 3, $r['meta']['asked'] );
        $this->assertSame( 2, $r['meta']['allowed'] );
        $this->fails( 400, 'expPdfServices', 'links', array( '2,abc' ) );
    }

    public function testExportsAreAdministration()
    {
        $this->assertPaged( $this->ok( 'expPdfServices', 'exports', array( 5, 0 ) ) );
        $this->assertSame( $this->ok( 'expPdfServices', 'exports', array( 200, 0 ) )['meta']['total'], $this->ok( 'expPdfServices', 'exportcount' )['data']['count'] );
        $this->loginAnonymous();
        $this->assertFalse( $this->call( 'expPdfServices', 'exports', array( 5, 0 ) )['ok'] );
    }

    public function testExportByIdIs404WhenMissing()
    {
        $this->fails( 404, 'expPdfServices', 'export', array( 99999999 ) );
        $this->fails( 404, 'expPdfServices', 'exportfile', array( 99999999 ) );
        $this->assertIsArray( $this->ok( 'expPdfServices', 'exportsfor', array( 2 ) )['data'] );
    }

    public function testExportModes()
    {
        $this->assertSame( array( 'once', 'on_the_fly' ), array_values( $this->ok( 'expPdfServices', 'statuses' )['data'] ) );
    }

    public function testAnonymousMayAskForALinkWhenTheyMayRead()
    {
        $this->loginAnonymous();
        $r = $this->call( 'expPdfServices', 'link', array( 2 ) );
        $this->assertContains( $r['ok'] ? 'ok' : $r['error']['code'], array( 'ok', 401, 403 ) );
    }
}
