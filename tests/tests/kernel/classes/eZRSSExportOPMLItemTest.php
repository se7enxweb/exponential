<?php
/**
 * Unit tests for the OPML export support added in 6.0.15.
 *
 * Everything exercised here is a pure static method: no database, no kernel
 * bootstrap, no network. The behaviour that needs a live installation - the
 * edit page, the browser, publishing a draft - is covered by the functional
 * scripts described in doc/bc/6.0/opml.md.
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group opml
 */

class eZRSSExportOPMLItemTest extends PHPUnit\Framework\TestCase
{
    // ── Text that a document can carry ────────────────────────────────────────

    public function testSafeTextDropsControlCharactersXmlCannotCarry()
    {
        self::assertSame( 'abcd', eZRSSExportOPMLItem::safeText( "a\x00b\x0Bc\x1Fd" ) );
        self::assertSame( '', eZRSSExportOPMLItem::safeText( "\x00\x01\x02" ) );
    }

    public function testSafeTextKeepsTheThreeControlCharactersXmlAllows()
    {
        self::assertSame( "a\tb\nc\rd", eZRSSExportOPMLItem::safeText( "a\tb\nc\rd" ) );
    }

    public function testSafeTextCutsToTheColumnWidthOnACharacterBoundary()
    {
        $long = eZRSSExportOPMLItem::safeText( str_repeat( 'a', 5000 ) );
        self::assertSame( eZRSSExportOPMLItem::MAX_TEXT, mb_strlen( $long, 'UTF-8' ) );

        $wide = eZRSSExportOPMLItem::safeText( str_repeat( 'ä', 5000 ) );
        self::assertSame( eZRSSExportOPMLItem::MAX_TEXT, mb_strlen( $wide, 'UTF-8' ) );
        self::assertTrue( mb_check_encoding( $wide, 'UTF-8' ), 'cut in the middle of a character' );
    }

    public function testSafeTextRefusesWhatIsNotText()
    {
        self::assertSame( '', eZRSSExportOPMLItem::safeText( array( 'x' ) ) );
        self::assertSame( '', eZRSSExportOPMLItem::safeText( new stdClass ) );
        self::assertSame( '', eZRSSExportOPMLItem::safeText( "a\xC3\x28b" ) );   // invalid utf-8
    }

    // ── Addresses a reader may be handed ──────────────────────────────────────

    #[PHPUnit\Framework\Attributes\DataProvider( 'dangerousAddresses' )]
    public function testSafeURLRefusesAnythingButHttp( $url )
    {
        self::assertSame( '', eZRSSExportOPMLItem::safeURL( $url ) );
    }

    public static function dangerousAddresses()
    {
        return array(
            'javascript'              => array( 'javascript:alert(1)' ),
            'javascript, shouting'    => array( 'JaVaScRiPt:alert(1)' ),
            'javascript, split'       => array( "java\nscript:alert(1)" ),
            'javascript, tabbed'      => array( "java\tscript:alert(1)" ),
            'javascript, indented'    => array( '  javascript:alert(1)' ),
            'data'                    => array( 'data:text/html;base64,PHNjcmlwdD4=' ),
            'vbscript'                => array( 'vbscript:msgbox(1)' ),
            'file'                    => array( 'file:///etc/passwd' ),
            'php stream'              => array( 'php://filter/resource=index.php' ),
            'gopher'                  => array( 'gopher://127.0.0.1:11211/' ),
            'jar'                     => array( 'jar:http://example.com!/' ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'soundAddresses' )]
    public function testSafeURLKeepsAddressesAReaderCanFollow( $url )
    {
        self::assertSame( $url, eZRSSExportOPMLItem::safeURL( $url ) );
    }

    public static function soundAddresses()
    {
        return array(
            'http'              => array( 'http://example.com/feed' ),
            'https'             => array( 'https://example.com/feed' ),
            'protocol relative' => array( '//example.com/feed' ),
            'site relative'     => array( '/rss/feed/news' ),
            'plain path'        => array( 'rss/feed/news' ),
        );
    }

    // ── The site an address belongs to ────────────────────────────────────────

    public function testSiteOfTakesAFeedPathBackOff()
    {
        self::assertSame( 'https://example.com',
                          eZRSSExportOPMLItem::siteOf( 'https://example.com/rss/feed/news' ) );
        self::assertSame( 'https://example.com', eZRSSExportOPMLItem::siteOf( 'https://example.com/' ) );
        self::assertSame( 'https://example.com', eZRSSExportOPMLItem::siteOf( 'https://example.com' ) );
        self::assertSame( '', eZRSSExportOPMLItem::siteOf( '' ) );
        self::assertSame( '', eZRSSExportOPMLItem::siteOf( null ) );
    }

    public function testAbsoluteJoinsWithExactlyOneSlash()
    {
        self::assertSame( 'http://example.com/rss/feed/news',
                          eZRSSExportOPMLItem::absolute( 'http://example.com', 'rss/feed/news' ) );
        self::assertSame( 'http://example.com/rss/feed/news',
                          eZRSSExportOPMLItem::absolute( 'http://example.com/', '/rss/feed/news' ) );
        self::assertSame( '/rss/feed/news', eZRSSExportOPMLItem::absolute( '', 'rss/feed/news' ) );
    }

    // ── What the format offers ────────────────────────────────────────────────

    public function testOutlineTypesCoverWhatOpmlDescribes()
    {
        $types = eZRSSExportOPMLItem::outlineTypes();
        foreach ( array( 'rss', 'link', 'include', 'group', 'text' ) as $type )
            self::assertArrayHasKey( $type, $types );
    }

    public function testEachFeedFormatHasAnOpmlVersionName()
    {
        self::assertSame( 'RSS1', eZRSSExportOPMLItem::opmlVersionOf( '1.0' ) );
        self::assertSame( 'RSS2', eZRSSExportOPMLItem::opmlVersionOf( '2.0' ) );
        self::assertSame( 'ATOM', eZRSSExportOPMLItem::opmlVersionOf( 'ATOM' ) );
        self::assertSame( '', eZRSSExportOPMLItem::opmlVersionOf( 'OPML' ) );
        self::assertSame( '', eZRSSExportOPMLItem::opmlVersionOf( 'something else' ) );
    }

    public function testTheLimitsAreBounded()
    {
        self::assertGreaterThan( 0, eZRSSExportOPMLItem::MAX_BULK );
        self::assertLessThanOrEqual( 1000, eZRSSExportOPMLItem::MAX_BULK );
        self::assertSame( 255, eZRSSExportOPMLItem::MAX_TEXT );
        self::assertGreaterThan( 1, eZRSSExportOPMLItem::MAX_DEPTH );
        self::assertLessThanOrEqual( 100, eZRSSExportOPMLItem::MAX_DEPTH );
    }

    // ── The stored value and the word in front of it ──────────────────────────

    public function testFormatLabelsNameEachStoredValueWithoutChangingIt()
    {
        $labels = eZRSSExport::formatLabels();
        foreach ( array( '1.0', '2.0', 'ATOM', 'OPML' ) as $stored )
        {
            self::assertArrayHasKey( $stored, $labels );
            self::assertNotSame( '', $labels[$stored] );
        }

        self::assertStringContainsString( 'RSS', eZRSSExport::formatLabel( '2.0' ) );
        self::assertStringContainsString( 'OPML', eZRSSExport::formatLabel( 'OPML' ) );
    }

    public function testAFormatNobodyHasANameForIsShownAsItStands()
    {
        self::assertSame( 'JSONFEED', eZRSSExport::formatLabel( 'JSONFEED' ) );
    }

    // ── The document produced when there is nothing to produce ────────────────

    public function testTheFallbackDocumentIsValidOpml()
    {
        $doc = new DOMDocument();
        self::assertTrue( @$doc->loadXML( eZRSSExport::emptyOPML( 'Tom & Jerry <script>' ) ) );

        self::assertSame( 'opml', $doc->documentElement->tagName );
        self::assertSame( '2.0', $doc->documentElement->getAttribute( 'version' ) );

        $xpath = new DOMXPath( $doc );
        self::assertSame( 1, $xpath->query( '/opml/head/title' )->length );
        self::assertGreaterThan( 0, $xpath->query( '/opml/body/outline' )->length );
        self::assertStringNotContainsString( '<script>', $doc->saveXML() );
    }

    public function testAnOpmlDateIsTheFormTheSpecificationAsksFor()
    {
        self::assertMatchesRegularExpression(
            '/^[A-Z][a-z]{2}, \d{2} [A-Z][a-z]{2} \d{4} \d{2}:\d{2}:\d{2} GMT$/',
            eZRSSExport::opmlDate( 1700000000 ) );
        self::assertSame( '', eZRSSExport::opmlDate( 0 ) );
        self::assertSame( '', eZRSSExport::opmlDate( -1 ) );
    }
}
