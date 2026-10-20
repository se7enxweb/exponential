<?php
/**
 * The help search (doc/en/us/features/6.0/help-system.md, "Search") on a fixture installation (a throwaway git repository
 * in the temporary directory): ranking, filters, snippets, paging, and that nothing outside the index is found.
 *
 *  HS-01  tokenize(): lower case, Unicode letters, punctuation split, no empty tokens
 *  HS-02  ranking: a title hit outranks a heading hit outranks a body hit; more hits rank higher
 *  HS-03  filters: audience, category, type; facets count the matches
 *  HS-04  snippets: escaped text with the hit highlighted by <mark> only; the hit url/anchor point at the page/heading
 *  HS-05  paging: total, offset, limit; empty and absurd queries are harmless
 *  HS-06  untracked pages and files outside doc/ are never found
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpSearchContractTest extends PHPUnit\Framework\TestCase
{
    private static $root = '';

    public static function setUpBeforeClass(): void
    {
        exec( 'git --version 2>/dev/null', $o, $rc );
        if ( $rc !== 0 )
            return;
        self::$root = sys_get_temp_dir() . '/exphelp-search-' . bin2hex( random_bytes( 6 ) );
        $pages = array(
            'doc/en/us/help/zebra-title.md' => "---\naudience: editor\ncategory: getting-started\n---\n# Zebra crossings\n\nNothing else here.\n",
            'doc/en/us/help/zebra-heading.md' => "---\naudience: admin\ncategory: layouts\n---\n# Traffic\n\n## Zebra rules\n\nRules.\n",
            'doc/en/us/help/zebra-body.md' => "---\naudience: developer\ncategory: layouts\n---\n# Animals\n\nA zebra appears once in the body.\n",
            'doc/en/us/help/zebra-body-many.md' => "---\naudience: developer\ncategory: layouts\n---\n# Stripes\n\nzebra zebra zebra zebra and more zebra.\n",
            'doc/en/us/help/xss.md' => "# Snippets\n\nThe word quagga <script>alert(1)</script> next to <img src=x onerror=alert(1)> markup.\n",
            'doc/en/us/help/unicode.md' => "# Übersicht\n\nGrüße aus Köln: Straßenbahn.\n",
            'doc/notes.txt' => "Plain text page about the quagga too.\n",
            'AGENTS.md' => "# zebra private\n\nquagga private\n",
            'settings/site.ini' => "[X]\nzebra=quagga\n",
        );
        foreach ( $pages as $path => $content )
        {
            @mkdir( dirname( self::$root . '/' . $path ), 0777, true );
            file_put_contents( self::$root . '/' . $path, $content );
        }
        foreach ( array( 'init -q', 'config user.email t@example.invalid', 'config user.name T', 'add -A', 'commit -q -m fixture' ) as $a )
            exec( 'git -C ' . escapeshellarg( self::$root ) . ' ' . $a . ' 2>&1' );
        file_put_contents( self::$root . '/doc/en/us/help/untracked.md', "# Zebra untracked\n\nquagga\n" );
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$root !== '' )
            self::remove( self::$root );
    }

    private static function remove( $path )
    {
        if ( is_link( $path ) || is_file( $path ) )
            return unlink( $path );
        if ( !is_dir( $path ) )
            return;
        foreach ( scandir( $path ) as $name )
            if ( $name !== '.' && $name !== '..' )
                self::remove( $path . '/' . $name );
        rmdir( $path );
    }

    protected function setUp(): void
    {
        if ( self::$root === '' )
            $this->markTestSkipped( 'git is not available' );
    }

    private static function search()
    {
        $allowlist = new expHelpAllowlist( array( 'root' => self::$root, 'roots' => array( 'doc' ) ) );
        return new expHelpSearch( new expHelpDocumentIndex( $allowlist, array( 'root' => self::$root, 'cache_dir' => false ) ) );
    }

    private static function ids( array $result )
    {
        return array_map( static function ( $h ) { return $h['id']; }, $result['hits'] );
    }

    /** HS-01 */
    public function testTokenize()
    {
        $t = expHelpSearch::tokenize( 'Clear the CACHE, then re-index: Straßenbahn/Köln!' );
        $this->assertContains( 'cache', $t );
        $this->assertContains( 'clear', $t );
        $this->assertContains( 'köln', $t );
        $this->assertNotContains( '', $t );
        foreach ( $t as $token )
            $this->assertSame( mb_strtolower( $token ), $token );
        $this->assertSame( array(), expHelpSearch::tokenize( '  ,;:!  ' ) );
    }

    /** HS-02 */
    public function testRanking()
    {
        $r = self::search()->search( 'zebra' );
        $ids = self::ids( $r );
        $this->assertSame( 'help/zebra-title.md', $ids[0] ?? null, 'the title hit first' );
        $this->assertSame( 'help/zebra-heading.md', $ids[1] ?? null, 'then the heading hit' );
        $this->assertLessThan( array_search( 'help/zebra-body.md', $ids, true ), array_search( 'help/zebra-body-many.md', $ids, true ),
                               'more body hits rank higher' );
        $scores = array_map( static function ( $h ) { return $h['score']; }, $r['hits'] );
        $sorted = $scores;
        rsort( $sorted );
        $this->assertSame( $sorted, $scores, 'hits are ordered by score' );
        foreach ( $r['hits'] as $h )
            foreach ( array( 'id', 'title', 'url', 'anchor', 'snippet', 'audience', 'category', 'type', 'score' ) as $k )
                $this->assertArrayHasKey( $k, $h, $k );
        $this->assertSame( 'Zebra crossings', $r['hits'][0]['title'] );
    }

    /** HS-03 */
    public function testFilters()
    {
        $s = self::search();
        $this->assertSame( array( 'help/zebra-title.md' ), self::ids( $s->search( 'zebra', array( 'audience' => 'editor' ) ) ) );
        $setup = self::ids( $s->search( 'zebra', array( 'category' => 'layouts' ) ) );
        $this->assertNotContains( 'help/zebra-title.md', $setup );
        $this->assertCount( 3, $setup );
        $this->assertSame( array(), self::ids( $s->search( 'zebra', array( 'audience' => 'nobody' ) ) ) );
        $all = $s->search( 'zebra' );
        $editor = $all['facets']['audience']['editor'] ?? 0;
        $this->assertSame( 1, (int)( is_array( $editor ) ? ( $editor['count'] ?? 0 ) : $editor ), 'facet audience/editor counts one match' );
        $this->assertArrayHasKey( 'category', $all['facets'] );
        $this->assertArrayHasKey( 'type', $all['facets'] );
    }

    /** HS-04 */
    public function testSnippets()
    {
        $r = self::search()->search( 'quagga' );
        $hit = null;
        foreach ( $r['hits'] as $h )
            if ( $h['id'] === 'help/xss.md' )
                $hit = $h;
        $this->assertNotNull( $hit );
        $snippet = $hit['snippet'];
        $this->assertMatchesRegularExpression( '#<mark>quagga</mark>#i', $snippet );
        $this->assertSame( '', preg_replace( '#</?mark>#', '', preg_replace( '/[^<>]/', '', str_replace( array( '<mark>', '</mark>' ), '', $snippet ) ) ),
                           'only <mark> is markup; the page\'s own tags are escaped: ' . $snippet );
        if ( stripos( $snippet, 'script' ) !== false )
            $this->assertStringContainsString( '&lt;script&gt;', $snippet, 'the page\'s markup shown escaped' );
        $this->assertStringStartsWith( '/exphelp/viewer/help/xss.md', $hit['url'] );
        $heading = self::search()->search( 'rules' )['hits'][0] ?? array();
        $this->assertSame( 'help/zebra-heading.md', $heading['id'] ?? null );
        $this->assertSame( 'zebra-rules', $heading['anchor'] ?? null, 'a heading hit links to its heading' );
        // a query with markup is never reflected unescaped
        $evil = self::search()->search( '<script>alert(1)</script> quagga' );
        foreach ( $evil['hits'] as $h )
            $this->assertStringNotContainsString( '<script', $h['snippet'] );
    }

    /** HS-05 */
    public function testPaging()
    {
        $s = self::search();
        $all = $s->search( 'zebra', array(), 50, 0 );
        $this->assertSame( 4, $all['total'] );
        $page = $s->search( 'zebra', array(), 2, 2 );
        $this->assertSame( 4, $page['total'] );
        $this->assertSame( 2, $page['offset'] );
        $this->assertSame( 2, $page['limit'] );
        $this->assertSame( array_slice( self::ids( $all ), 2, 2 ), self::ids( $page ) );
        foreach ( array( '', '   ', str_repeat( 'a', 10000 ), '*', '"', "\0", 'a OR 1=1', '%', '.*' ) as $q )
        {
            $r = $s->search( $q );
            $this->assertIsArray( $r['hits'], var_export( substr( $q, 0, 20 ), true ) );
        }
        // an empty query browses (wave 2): every served page, with the browse mode, never an empty page
        $browse = $s->search( '' );
        $this->assertSame( 'browse', $browse['mode'] );
        $this->assertGreaterThan( 0, $browse['total'] );
        $this->assertNotContains( 'help/untracked.md', self::ids( $s->search( '', array(), 50 ) ) );
        $this->assertSame( array(), self::ids( $s->search( 'zebra', array(), 10, 1000 ) ) );
    }

    /** HS-06 */
    public function testOnlyServedPagesFound()
    {
        foreach ( array( 'zebra', 'quagga', 'private', 'untracked' ) as $q )
        {
            foreach ( self::search()->search( $q, array(), 50 )['hits'] as $h )
            {
                $this->assertStringNotContainsString( 'AGENTS', $h['id'] );
                $this->assertStringNotContainsString( 'settings', $h['id'] );
                $this->assertNotSame( 'help/untracked.md', $h['id'] );
                $this->assertStringNotContainsString( 'private', $h['snippet'] );
            }
        }
        $this->assertContains( 'notes.txt', self::ids( self::search()->search( 'quagga' ) ), 'plain text pages are searched too' );
        $this->assertContains( 'help/unicode.md', self::ids( self::search()->search( 'köln' ) ) );
    }
}
