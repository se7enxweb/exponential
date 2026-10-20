<?php
/**
 * DB-less tests of expHelpRecordRenderer: the release and branch history (doc/en/us/release-overview,
 * and the SVN branch records doc/branch-overview had, kept as fixtures/branch-overview-svn.txt) shown as tables per
 * series, recognised by the shape of the text.
 *
 *  RR-01  both files are recognised; every record of the file becomes one table row
 *  RR-02  every date is read and written as ISO 8601
 *  RR-03  no link to a host that is gone (zev.ez.no, svn.ez.no); those addresses are marked historical
 *  RR-04  only the renderer's own markup; values escaped; no form controls, no inline styles or handlers
 *  RR-05  title and summary for the index; grouping by series and section; table of contents
 *  RR-06  other texts are not taken for records (changelogs with "- " lists, prose)
 *  RR-07  the date formats of the old files
 *  RR-08  search ranks the history like the pages of the version most of its records belong to (VersionBoost)
 *  RR-09  the records after 4.2 (5.x, 6.x): sourced, dates from their changelogs, unknown dates marked, live links
 *         only to hosts that are online (GitHub), changelogs open in the help
 *  RR-10  doc/en/us/branch-overview.md (Markdown) keeps every SVN branch of the old record file, title "Branch history"
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpRecordRendererTest extends PHPUnit\Framework\TestCase
{
    const ELEMENTS = array( 'div', 'p', 'h1', 'h2', 'h3', 'a', 'span', 'details', 'summary', 'table', 'caption',
                            'thead', 'tbody', 'tr', 'th', 'td', 'ul', 'li', 'code', 'time' );

    const ATTRIBUTES = array( 'class', 'id', 'href', 'rel', 'aria-hidden', 'aria-label', 'aria-labelledby', 'tabindex',
                              'role', 'open', 'scope', 'datetime', 'data-exphelp-records', 'data-exphelp-sortable',
                              'data-sort-value', 'data-sort' );

    protected static function root()
    {
        return dirname( __DIR__, 5 );
    }

    /** A page of doc/en/us/, or "branch-overview-svn": the SVN branch records doc/branch-overview had until it became Markdown */
    protected static function source( $name )
    {
        $text = file_get_contents( $name === 'branch-overview-svn' ? __DIR__ . '/fixtures/branch-overview-svn.txt' : self::root() . '/doc/en/us/' . $name );
        self::assertIsString( $text );
        return $text;
    }

    protected static function renderer()
    {
        return new expHelpRecordRenderer( array( 'translate' => function( $text ) { return $text; } ) );
    }

    /** The records a file holds: a name line followed by "- " lines */
    protected static function countRecords( $text )
    {
        $count = 0;
        $previous = '';
        foreach ( explode( "\n", str_replace( "\r\n", "\n", $text ) ) as $line )
        {
            if ( strncmp( $line, '- ', 2 ) === 0 && trim( $previous ) !== '' && strncmp( $previous, '- ', 2 ) !== 0 )
                $count++;
            $previous = $line;
        }
        return $count;
    }

    protected static function dom( $html )
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors( true );
        $dom->loadHTML( '<?xml encoding="UTF-8"><body>' . $html . '</body>', LIBXML_NONET );
        libxml_clear_errors();
        return $dom;
    }

    public static function files()
    {
        return array( 'release-overview' => array( 'release-overview' ), 'branch-overview' => array( 'branch-overview-svn' ) );
    }

    /** RR-01 RR-02 RR-03 RR-04 */
    #[PHPUnit\Framework\Attributes\DataProvider( 'files' )]
    public function testRendersTheFile( $name )
    {
        $text = self::source( $name );
        $parsed = expHelpRecordRenderer::parse( $text );
        $this->assertNotNull( $parsed, "$name is recognised as a record list" );
        $expected = self::countRecords( $text );
        $this->assertGreaterThanOrEqual( 10, $expected );
        $this->assertCount( $expected, $parsed['records'], 'every record is parsed' );

        // RR-02: every date line is read
        preg_match_all( '/^- (?:Release )?[Dd]ate\s+(.+)$/m', $text, $dates );
        foreach ( $parsed['records'] as $record )
            foreach ( array( 'date' => 'date_text', 'release_date' => 'release_date_text' ) as $iso => $raw )
                // every date is read, unless the record says it has none ("unknown", "not released yet")
                if ( $record[$raw] !== '' && !preg_match( '/^(unknown|not released)/i', $record[$raw] ) )
                    $this->assertMatchesRegularExpression( '/^\d{4}-\d{2}-\d{2}$/', $record[$iso], "date of {$record['name']}: {$record[$raw]}" );

        $rendered = self::renderer()->render( $text );
        $this->assertIsArray( $rendered );
        $dom = self::dom( $rendered['html'] );
        $rows = $dom->getElementsByTagName( 'tbody' );
        $count = 0;
        foreach ( $rows as $tbody )
            foreach ( $tbody->childNodes as $tr )
                if ( $tr->nodeName === 'tr' )
                    $count++;
        $this->assertSame( $expected, $count, 'one table row per record' );
        $withDate = 0;
        foreach ( $parsed['records'] as $record )
            $withDate += ( $record['date'] !== '' ? 1 : 0 ) + ( $record['release_date'] !== '' && $record['release_date'] !== $record['date'] ? 1 : 0 );
        $this->assertGreaterThan( 0, count( $dates[1] ) );
        $this->assertSame( $withDate, $dom->getElementsByTagName( 'time' )->length, 'every date shown as <time>' );

        // RR-03: no link to a gone host, and their addresses are marked
        foreach ( $dom->getElementsByTagName( 'a' ) as $a )
        {
            $href = $a->getAttribute( 'href' );
            $this->assertDoesNotMatchRegularExpression( '#^(?:https?:)?//(?:zev|svn|pubsvn)\.ez\.no#i', $href );
            $this->assertMatchesRegularExpression( '/^(#|https:\/\/)/', $href, 'only anchors and https links' );
        }
        if ( strpos( $text, 'ez.no/svn' ) !== false )
        {
            $this->assertStringContainsString( 'zev.ez.no', $rendered['html'], 'the addresses stay as text' );
            $this->assertStringContainsString( 'historical (no longer online)', $rendered['html'] );
        }

        // RR-04: the renderer's own markup only
        foreach ( ( new DOMXPath( $dom ) )->query( '//body//*' ) as $element )
        {
            $this->assertContains( $element->nodeName, self::ELEMENTS, 'element ' . $element->nodeName );
            foreach ( $element->attributes as $attribute )
                $this->assertContains( $attribute->name, self::ATTRIBUTES, 'attribute ' . $attribute->name . ' on ' . $element->nodeName );
        }
    }

    /** RR-05 */
    public function testTitlesSummariesAndGroups()
    {
        $release = self::renderer()->describe( self::source( 'release-overview' ) );
        $this->assertSame( "Release history (3.x\u{2013}6.x)", $release['title'], 'not "from the original SVN": the newer records come from other sources' );
        $this->assertMatchesRegularExpression( '/^\d+ releases from 2002-11-01 to 2026-\d\d-\d\d /', $release['summary'] );
        $this->assertStringContainsString( 'historical', $release['summary'] );

        $branch = self::renderer()->describe( self::source( 'branch-overview-svn' ) );
        $this->assertSame( 'Branch history (3.x, from the original SVN)', $branch['title'] );
        $this->assertStringStartsWith( '10 branches from 2003-04-09 to 2006-02-20', $branch['summary'] );

        $rendered = self::renderer()->render( self::source( 'release-overview' ) );
        $level2 = array_values( array_map( function( $h ) { return $h['text']; },
            array_filter( $rendered['toc'], function( $h ) { return $h['level'] === 2; } ) ) );
        $this->assertSame( array( 'Series 3.x', 'Series 4.x', 'Series 5.x', 'Series 6.x' ), $level2 );
        $level3 = array_values( array_map( function( $h ) { return $h['text']; },
            array_filter( $rendered['toc'], function( $h ) { return $h['level'] === 3; } ) ) );
        $this->assertContains( '3.0', $level3 );
        $this->assertContains( '3.10', $level3 );
        $this->assertContains( '4.2', $level3 );
        $this->assertSame( '3.0', $level3[0], 'minor versions in version order' );
        // 2.9-3 "AKA 3.0-beta2" belongs to 3.0
        $this->assertMatchesRegularExpression( '#id="30".*?2\.9-3.*?id="31"#s', $rendered['html'] );

        $branches = self::renderer()->render( self::source( 'branch-overview-svn' ) );
        $this->assertSame( array( 'Series 3.x', 'Various development branches' ),
                           array_map( function( $h ) { return $h['text']; }, $branches['toc'] ) );
        $this->assertStringContainsString( 'This an overview of the various branches.', $branches['html'] );

        // through the Markdown renderer, as the viewer calls it
        $viewer = ( new expHelpMarkdownRenderer( array( 'translate' => function( $t ) { return $t; } ) ) )->renderPlainText( self::source( 'branch-overview-svn' ), 'branch-overview-svn' );
        $this->assertStringContainsString( 'a4-help-records', $viewer['html'] );
        $this->assertSame( $branch['title'], $viewer['title'] );
    }

    /** RR-06 */
    public function testOtherTextsAreNotRecords()
    {
        $this->assertNull( expHelpRecordRenderer::parse( "Changes from 3.5 to 3.6\n\n*Kernel:\n- Fixed a bug in the cache\n- Added a setting\n\n*Design:\n- Fixed the menu\n- Fixed the footer\n\n*Library:\n- Updated something\n" ) );
        $this->assertNull( expHelpRecordRenderer::parse( "Just a text.\n\nWith paragraphs.\n" ) );
        $this->assertNull( expHelpRecordRenderer::parse( "a:\n- x=1\n\nb:\n- y=2\n" ), 'fewer than three records' );
        $this->assertNotNull( expHelpRecordRenderer::parse( "1.0:\n- Revision=1\n- Date 01-Jan-2001\n\n1.1:\n- Revision=2\n\n1.2\n- Revision=3\n" ), 'the shape, not the file name' );
        // a changelog of the old text format, if there is one in the documentation
        foreach ( glob( self::root() . '/doc/changelogs/*/*' ) ?: array() as $file )
        {
            if ( !is_file( $file ) || preg_match( '/\.(md|markdown)$/', $file ) )
                continue;
            $this->assertNull( expHelpRecordRenderer::parse( (string)file_get_contents( $file ) ), basename( $file ) . ' is no record list' );
        }
    }

    /** RR-04 */
    public function testValuesAreEscaped()
    {
        $text = "<b>1.0</b>:\n- AKA <script>alert(1)</script>\n- Revision=1\" onclick=\"x\n- Release url=javascript:alert(1)\n- Version url=https://example.com/\"><img src=x>\n- Date 01-Jan-2001\n\n"
            . "1.1:\n- Revision=2\n- url=https://example.com/ok\n\n1.2:\n- Revision=3\n- url=http://zev.ez.no/svn/x (5)\n";
        $rendered = self::renderer()->render( $text );
        $this->assertIsArray( $rendered );
        $html = $rendered['html'];
        $this->assertStringNotContainsString( '<script', $html );
        $this->assertStringNotContainsString( '<img', $html );
        $this->assertStringNotContainsString( '<b>', $html );
        foreach ( ( new DOMXPath( self::dom( $html ) ) )->query( '//@*' ) as $attribute )
            $this->assertStringStartsNotWith( 'on', $attribute->name, 'no event handler attributes' );
        $this->assertStringNotContainsString( 'href="javascript', $html );
        $this->assertStringContainsString( 'href="https://example.com/ok"', $html );
        $this->assertStringNotContainsString( 'href="http://zev', $html );
        $this->assertStringContainsString( 'revision 5', $html );
    }

    /** RR-07 */
    public function testDates()
    {
        $cases = array( '01-Nov-2002' => '2002-11-01', '5-Feb-2003' => '2003-02-05', '02-June-2004' => '2004-06-02',
                        ' 1-Oct-2004' => '2004-10-01', '20. feb. 2006' => '2006-02-20', '2009-09-29' => '2009-09-29',
                        '24-March-2005' => '2005-03-24', '31-Feb-2003' => '', 'soon' => '', '' => '' );
        foreach ( $cases as $text => $iso )
            $this->assertSame( $iso, expHelpRecordRenderer::isoDate( $text ), "'$text'" );
    }

    /** RR-08 the index gives the page the major version most of its records belong to, so search ranks it like those pages */
    public function testSearchRanksTheHistoryLikeItsVersion()
    {
        $described = self::renderer()->describe( self::source( 'release-overview' ) );
        $this->assertSame( '3', $described['version'] );
        $this->assertSame( '3', self::renderer()->describe( self::source( 'branch-overview-svn' ) )['version'] );

        $text = "# Release notes\n\nRelease 4.2 of the revision history.\n";
        $documents = array(
            'release-overview' => array( 'title' => 'Release history', 'type' => 'reference', 'version' => '4', 'text' => $text ),
            'features/6.0/releases.md' => array( 'title' => 'Release history', 'type' => 'reference', 'text' => $text ),
        );
        $search = expHelpSearch::fromDocuments( $documents, array( 'versionBoost' => array( '4' => 0.6 ), 'typeBoost' => array() ) );
        $result = $search->search( 'release history ' );
        $ids = array_map( function( $hit ) { return $hit['id']; }, $result['hits'] );
        $this->assertSame( array( 'features/6.0/releases.md', 'release-overview' ), $ids );
    }

    /** RR-09 the records after 4.2: 5.x and 6.x, each with its source, dates only where a source gives one */
    public function testRecordsAfterFourTwo()
    {
        $parsed = expHelpRecordRenderer::parse( self::source( 'release-overview' ) );
        $this->assertNotEmpty( $parsed['comments'], 'the appended records say where they come from' );
        $bySeries = array();
        $byName = array();
        foreach ( $parsed['records'] as $record )
        {
            $bySeries[$record['series']][] = $record;
            $byName[$record['name']] = $record;
        }
        $this->assertArrayHasKey( '5.x', $bySeries );
        $this->assertArrayHasKey( '6.x', $bySeries );
        $this->assertGreaterThanOrEqual( 50, count( $bySeries['5.x'] ) );

        // every record after 4.2 names its source, and its date is ISO or says why there is none
        $after = false;
        foreach ( $parsed['records'] as $record )
        {
            if ( in_array( $record['series'], array( '5.x', '6.x' ), true ) || $record['sources'] )
                $after = true;
            if ( !$after )
                continue;
            $this->assertNotEmpty( $record['sources'], "{$record['name']} names its source" );
            if ( $record['date'] === '' )
                $this->assertMatchesRegularExpression( '/unknown|not released/i', $record['date_text'], "{$record['name']}: a missing date is marked" );
        }

        // 6.x: one record per changelog page, with the date and tag the page gives
        $changelogs = glob( self::root() . '/doc/changelogs/6.*/6.*.md' ) ?: array();
        $this->assertNotEmpty( $changelogs );
        foreach ( $changelogs as $file )
        {
            $version = basename( $file, '.md' );
            $this->assertArrayHasKey( $version, $byName, "a record for $version" );
            $record = $byName[$version];
            $this->assertSame( '6.x', $record['series'] );
            $head = implode( '', array_slice( file( $file ), 0, 6 ) );
            if ( preg_match( '/Released (\d{4}-\d\d-\d\d)\. Tag `([^`]+)`/', $head, $m ) )
            {
                $this->assertSame( $m[1], $record['date'], "$version: the date of its changelog" );
                $this->assertContains( array( 'Tag', $m[2] ), $record['revisions'], "$version: its tag" );
            }
            else
                $this->assertSame( '', $record['date'], "$version is not released: no date" );
            $links = array_map( function( $url ) { return $url[1]; }, $record['urls'] );
            $this->assertContains( 'changelogs/' . basename( dirname( $file ) ) . '/' . basename( $file ), $links, "$version links its changelog" );
        }

        // only hosts that are online are links: GitHub yes, the old ez.no servers no
        $html = ( new expHelpMarkdownRenderer( array(
            'translate' => function( $t ) { return $t; },
            'link_resolver' => function( $href, $id ) { return '/admin/exphelp/viewer/' . expHelpMarkdownRenderer::resolvePath( $id, $href ); },
        ) ) )->renderPlainText( self::source( 'release-overview' ), 'release-overview' )['html'];
        $dom = self::dom( $html );
        $github = 0;
        foreach ( $dom->getElementsByTagName( 'a' ) as $a )
        {
            $href = $a->getAttribute( 'href' );
            $this->assertDoesNotMatchRegularExpression( '#//[^/]*\.ez\.no#i', $href, 'no link to the old ez.no servers' );
            if ( strpos( $href, 'https://github.com/' ) === 0 )
                $github++;
        }
        $this->assertGreaterThanOrEqual( 40, $github, 'GitHub releases and tags are links' );
        $this->assertStringContainsString( 'href="/admin/exphelp/viewer/changelogs/6.0/6.0.14.md"', $html, 'a changelog opens in the help' );
        $this->assertStringContainsString( 'confluence.ez.no', $html, 'the dead address stays as text' );
    }

    /** RR-10 doc/en/us/branch-overview.md is a Markdown page, not a record list, and keeps every SVN branch of the old file */
    public function testBranchOverviewKeepsTheSvnBranches()
    {
        $page = self::source( 'branch-overview.md' );
        $this->assertNull( expHelpRecordRenderer::parse( $page ), 'a Markdown page, not records' );
        $this->assertMatchesRegularExpression( '/^# Branch history$/m', $page, 'the title is just "Branch history"' );
        $this->assertStringContainsString( '## Historical branches (original SVN, 3.x)', $page );
        $this->assertStringContainsString( 'eZ Systems AS', $page, 'the inherited section names its authors and terms' );
        $old = expHelpRecordRenderer::parse( self::source( 'branch-overview-svn' ) );
        $this->assertCount( 10, $old['records'] );
        foreach ( $old['records'] as $record )
        {
            $first = '';
            foreach ( $record['revisions'] as $revision )
                if ( $revision[0] === 'Revision' )
                    $first = $revision[1];
            $this->assertMatchesRegularExpression( '/^\| `' . preg_quote( $record['name'], '/' ) . '` \| ' . preg_quote( $first, '/' ) . ' \| [^|]+ \| '
                                                   . preg_quote( $record['date'], '/' ) . ' \|$/m', $page, $record['name'] . ' with its revision and date' );
        }
        $rendered = ( new expHelpMarkdownRenderer( array( 'translate' => function( $t ) { return $t; } ) ) )->render( $page, 'branch-overview.md' );
        $this->assertSame( 'Branch history', $rendered['title'] );
    }
}
