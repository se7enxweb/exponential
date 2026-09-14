<?php
/**
 * Security regression tests for the /rss/ module, 6.0.15.
 *
 * Test IDs map to doc/bc/6.0/opml.md, "Security".
 *
 *  RSS-01 — An outline address may only be one a reader should follow
 *  RSS-02 — Characters no xml document can carry never reach one
 *  RSS-03 — A sort column is chosen from a list, never taken from the request
 *  RSS-04 — A page size is chosen from a list, so no request can ask for the lot
 *  RSS-05 — An offset cannot be walked outside the list
 *  RSS-06 — An import fetches http and https only, and fetches what it checked
 *  RSS-07 — A feed format label cannot change the value that is stored
 *
 * No database and no network: every method exercised is pure.
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group security
 * @group opml
 */

class eZRSSSecurityTest extends PHPUnit\Framework\TestCase
{
    // ── RSS-01 ────────────────────────────────────────────────────────────────

    /**
     * An OPML document is read by other people's software, which follows the
     * addresses in it. Only http and https, and addresses relative to the site,
     * are written out.
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'schemesThatMustNotSurvive' )]
    public function testRSS01OutlineAddressesAreLimitedToWhatAReaderShouldFollow( $url )
    {
        self::assertSame( '', eZRSSExportOPMLItem::safeURL( $url ),
                          'this address would have been written into a subscription list' );
    }

    public static function schemesThatMustNotSurvive()
    {
        return array(
            array( 'javascript:alert(1)' ),
            array( 'JAVASCRIPT:alert(1)' ),
            array( "java\nscript:alert(1)" ),
            array( "java\tscript:alert(1)" ),
            array( "\tjavascript:alert(1)" ),
            array( 'data:text/html,<script>alert(1)</script>' ),
            array( 'vbscript:msgbox(1)' ),
            array( 'file:///etc/passwd' ),
            array( 'file://localhost/etc/shadow' ),
            array( 'php://filter/read=convert.base64-encode/resource=config.php' ),
            array( 'expect://id' ),
            array( 'gopher://127.0.0.1:6379/_SET%20x%20y' ),
            array( 'ftp://example.com/feed' ),
        );
    }

    // ── RSS-02 ────────────────────────────────────────────────────────────────

    /**
     * XML 1.0 has no representation for most control characters, escaped or
     * otherwise. One stored in a row would produce a document that nothing can
     * parse, from an address that is public.
     */
    public function testRSS02ControlCharactersNeverReachADocument()
    {
        $poisoned = "title\x00\x08\x0B\x0C\x1F\x7Fhere";
        self::assertSame( 'titlehere', eZRSSExportOPMLItem::safeText( $poisoned ) );

        $doc = new DOMDocument();
        $element = $doc->createElement( 'outline' );
        $element->setAttribute( 'text', eZRSSExportOPMLItem::safeText( $poisoned ) );
        $doc->appendChild( $element );

        $reread = new DOMDocument();
        self::assertTrue( @$reread->loadXML( $doc->saveXML() ) );
    }

    public function testRSS02MarkupInTextIsEscapedRatherThanInjected()
    {
        $xml = eZRSSExport::emptyOPML( '</opml><evil/>' );

        $doc = new DOMDocument();
        self::assertTrue( @$doc->loadXML( $xml ) );
        self::assertSame( 0, ( new DOMXPath( $doc ) )->query( '//evil' )->length );
    }

    // ── RSS-03 ────────────────────────────────────────────────────────────────

    /**
     * The list and the browser both order by a column named in the request. It
     * is matched against the list of columns the table offers, and anything
     * else falls back rather than reaching the order clause.
     */
    public function testRSS03ASortColumnComesFromTheListNotFromTheRequest()
    {
        $allowed = eZRSSExport::sortableFields();

        foreach ( array( 'id; DROP TABLE ezrss_export',
                         'title, (SELECT password_hash FROM ezuser LIMIT 1)',
                         "title' --",
                         'password_hash',
                         '',
                         '1' ) as $attempt )
        {
            $sort = eZRSSListPager::sort( $attempt, 'asc', $allowed, 'title' );
            self::assertContains( $sort['field'], $allowed );
            foreach ( array_keys( $sort['sorts'] ) as $field )
                self::assertContains( $field, $allowed );
        }
    }

    public function testRSS03ASortDirectionIsOneOfTwoWords()
    {
        foreach ( array( 'asc; DROP TABLE ezrss_export', 'sideways', '', null, 'DESC --' ) as $attempt )
        {
            $sort = eZRSSListPager::sort( 'title', $attempt, eZRSSExport::sortableFields(), 'title' );
            self::assertContains( $sort['direction'], array( 'asc', 'desc' ) );
            foreach ( $sort['sorts'] as $direction )
                self::assertContains( $direction, array( 'asc', 'desc' ) );
        }
    }

    public function testRSS03EverySortIsSettledSoPagingCannotRepeatOrSkipARow()
    {
        $sort = eZRSSListPager::sort( 'rss_version', 'asc', eZRSSExport::sortableFields(), 'title' );
        self::assertArrayHasKey( 'id', $sort['sorts'],
                                 'rows that tie would come back in an order the database may change' );

        $sort = eZRSSListPager::sort( 'id', 'desc', eZRSSExport::sortableFields(), 'title' );
        self::assertSame( array( 'id' => 'desc' ), $sort['sorts'] );
    }

    // ── RSS-04 ────────────────────────────────────────────────────────────────

    /**
     * A list of thousands is fetched a page at a time. A request able to name
     * its own page size could ask for all of them, which is a way of exhausting
     * the server from a single address.
     */
    public function testRSS04APageSizeIsOneOfThoseOffered()
    {
        $offered = eZRSSListPager::limits();

        foreach ( array( 100000, '999999', -1, 0, 'all', '', null, '25; DROP TABLE x', 26 ) as $attempt )
            self::assertContains( eZRSSListPager::limit( $attempt ), $offered );
    }

    public function testRSS04TheSizesOfferedStayModest()
    {
        foreach ( eZRSSListPager::limits() as $limit )
        {
            self::assertIsInt( $limit );
            self::assertGreaterThan( 0, $limit );
            self::assertLessThanOrEqual( 1000, $limit );
        }
    }

    // ── RSS-05 ────────────────────────────────────────────────────────────────

    public function testRSS05AnOffsetStaysInsideTheList()
    {
        self::assertSame( 0, eZRSSListPager::offset( -500, 25, 400 ) );
        self::assertSame( 0, eZRSSListPager::offset( 'nonsense', 25, 400 ) );
        self::assertSame( 0, eZRSSListPager::offset( 999999, 25, 0 ) );
        self::assertSame( 375, eZRSSListPager::offset( 999999, 25, 400 ) );
        self::assertSame( 100, eZRSSListPager::offset( 101, 25, 400 ) );
    }

    // ── RSS-06 ────────────────────────────────────────────────────────────────

    /**
     * The import address is typed into the admin interface and then fetched by
     * the server. curl will open file:// and follows redirects, so a feed
     * address is otherwise a way of reading the disk or reaching inside the
     * network the server sits in.
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'addressesAnImportMustRefuse' )]
    public function testRSS06AnImportFetchesHttpAndHttpsOnly( $url )
    {
        self::assertFalse( eZRSSImport::isFetchableURL( $url ) );
        self::assertFalse( eZRSSImport::fetchableURL( $url ) );
    }

    public static function addressesAnImportMustRefuse()
    {
        return array(
            array( 'file:///etc/passwd' ),
            array( 'php://input' ),
            array( 'gopher://127.0.0.1:6379/' ),
            array( 'ftp://example.com/feed' ),
            array( 'dict://127.0.0.1:11211/' ),
            array( '/etc/passwd' ),
            array( 'example.com/feed' ),
            array( 'http://' ),
            array( '' ),
            array( null ),
            array( "http://exa\nmple.com/feed" ),
            array( "http://example.com/feed\r\nHost: elsewhere" ),
            array( "http://example.com/\0" ),
        );
    }

    public function testRSS06WhatIsFetchedIsWhatWasChecked()
    {
        // Checking a trimmed address and then fetching the untrimmed one is how
        // trailing bytes reach a library that may read them as something else.
        self::assertSame( 'http://example.com/feed.xml',
                          eZRSSImport::fetchableURL( "  http://example.com/feed.xml\n" ) );
        self::assertSame( 'https://example.com/feed.xml',
                          eZRSSImport::fetchableURL( 'https://example.com/feed.xml' ) );
    }

    public function testRSS06AnAbsurdlyLongAddressIsRefused()
    {
        self::assertFalse( eZRSSImport::isFetchableURL( 'http://example.com/' . str_repeat( 'a', 4000 ) ) );
    }

    // ── RSS-07 ────────────────────────────────────────────────────────────────

    /**
     * The drop-down was relabelled so a person can tell the formats apart. The
     * values behind the labels are what is stored and what everything else
     * reads, and they do not change.
     */
    public function testRSS07TheWordsChangedButTheStoredValuesDidNot()
    {
        self::assertSame( array( '1.0', '2.0', 'ATOM', 'OPML' ),
                          array_keys( eZRSSExport::formatLabels() ) );
    }
}
