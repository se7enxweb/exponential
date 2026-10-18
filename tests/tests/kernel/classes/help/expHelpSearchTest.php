<?php
/**
 * expHelpSearch (doc/features/6.0/help-system.md, "Search"): ranking with title and heading boosts, every word
 * required with a relaxed fallback, prefix matching for search while typing, exclusions, filters and facets,
 * sections with anchors, snippets that escape every character of the source, the cache file. No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpSearchTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

// a worktree whose vendor/ is a link to another checkout autoloads from that checkout's class map
if ( !class_exists( 'expHelpSearch' ) )
    require_once dirname( __DIR__, 5 ) . '/kernel/classes/help/exphelpsearch.php';

class expHelpSearchTest extends PHPUnit\Framework\TestCase
{
    private $cacheDir = null;

    protected function tearDown(): void
    {
        if ( $this->cacheDir !== null && is_dir( $this->cacheDir ) )
        {
            foreach ( (array)glob( $this->cacheDir . '/*' ) as $file )
                unlink( $file );
            rmdir( $this->cacheDir );
        }
        parent::tearDown();
    }

    private static function documents()
    {
        return array(
            'guides/velocity.md' => array(
                'title' => 'Running Velocity', 'audience' => array( 'admin', 'developer' ), 'category' => 'operations', 'type' => 'guide',
                'text' => "# Running Velocity\n\nVelocity is the application server.\n\n## Restart the server\n\nUse exp:velocity restart after a class change.\n\n## Logs\n\nThe logs live in var/log.\n" ),
            'features/cache.md' => array(
                'title' => 'Cache management', 'audience' => array( 'admin' ), 'category' => 'operations', 'type' => 'feature',
                'text' => "# Cache management\n\nClear caches with ezcache.php. Velocity has a response cache too.\n\n## Clearing the template cache\n\nRun php bin/php/ezcache.php --clear-tag=template.\n" ),
            'help/editing.md' => array(
                'title' => 'Editing content', 'audience' => array( 'editor', 'user' ), 'category' => 'content', 'type' => 'help',
                'text' => "---\naudience: editor\n---\n# Editing content\n\nOpen the object and press Edit. Publishing makes it visible.\n\n## Images\n\nUpload images in the media library.\n" ),
            'changelogs/6.0.15.md' => array(
                'title' => 'Changelog 6.0.15', 'audience' => array( 'developer' ), 'category' => 'releases', 'type' => 'changelog',
                'text' => "# Changelog 6.0.15\n\n- Velocity restart is faster.\n- Cache clear renames aside.\n" ),
            'bc/unsafe.md' => array(
                'title' => 'Escaping', 'audience' => array( 'developer' ), 'category' => 'reference', 'type' => 'bc',
                'text' => "# Escaping\n\nA template must escape <script>alert(1)</script> & \"quotes\" with wash.\n" ),
        );
    }

    private static function search( array $settings = array() )
    {
        return expHelpSearch::fromDocuments( self::documents(), $settings );
    }

    private static function ids( array $result )
    {
        return array_map( function ( $hit ) { return $hit['id']; }, $result['hits'] );
    }

    public function testTitleMatchRanksFirst()
    {
        $result = self::search()->search( 'velocity ' );
        $this->assertSame( 'guides/velocity.md', $result['hits'][0]['id'] );
        $this->assertFalse( $result['relaxed'] );
        $this->assertSame( $result['hits'], $result['results'] );
    }

    public function testHeadingMatchPointsToItsSection()
    {
        $result = self::search()->search( 'restart ' );
        $hit = $result['hits'][0];
        $this->assertSame( 'guides/velocity.md', $hit['id'] );
        $this->assertSame( 'Restart the server', $hit['section'] );
        $this->assertSame( 'restart-the-server', $hit['anchor'] );
        $this->assertStringEndsWith( '#restart-the-server', $hit['url'] );
        $this->assertStringContainsString( '/exphelp/viewer/guides/velocity.md', $hit['url'] );
        $this->assertSame( 'doc/guides/velocity.md', $hit['path'] );
    }

    public function testSectionsFindSetextAndRepeatedHeadingsAsTheIndexDoes()
    {
        $source = "---\ntitle: x\n---\nIntro text.\n\nFirst part\n==========\n\nOne.\n\n## Usage\n\nTwo.\n\n```\n# not a heading\n```\n\n- item\n---\n\n## Usage\n\nThree.\n\nSecond `part`\n-------------\n\nFour.\n";
        $sections = expHelpSearch::sections( $source );
        $headings = array_map( function ( $s ) { return array( $s['heading'], $s['anchor'] ); }, $sections );
        $this->assertSame( array( array( null, null ), array( 'First part', 'first-part' ), array( 'Usage', 'usage' ),
                                  array( 'Usage', 'usage-1' ), array( 'Second part', 'second-part' ) ), $headings );
        $this->assertStringContainsString( '# not a heading', $sections[2]['text'] );
        if ( is_callable( array( 'expHelpDocumentIndex', 'headings' ) ) )
        {
            $index = array_map( function ( $h ) { return array( $h['text'], $h['anchor'] ); }, expHelpDocumentIndex::headings( substr( $source, strpos( $source, 'Intro' ) ) ) );
            $this->assertSame( array_slice( $headings, 1 ), $index, 'the same headings and anchors as the document index' );
        }
    }

    public function testATextPageHasNoAnchors()
    {
        $search = expHelpSearch::fromDocuments( array( 'features/3.8/notes.txt' => array( 'title' => 'Notes', 'type' => 'feature',
            'text' => "Notes\n=====\n\n# Section\n\nThe zebra lives here.\n" ) ) );
        $hit = $search->search( 'zebra ' )['hits'][0];
        $this->assertNull( $hit['anchor'] );
        $this->assertStringEndsWith( '/features/3.8/notes.txt', $hit['url'] );
        $this->assertStringContainsString( '<mark>zebra</mark>', $hit['snippet'] );
    }

    public function testPagesOfOlderVersionsRankLower()
    {
        $page = array( 'title' => 'Image handling', 'type' => 'feature', 'text' => "# Image handling\n\nImages are scaled.\n" );
        $search = expHelpSearch::fromDocuments( array( 'features/3.10/images.md' => $page, 'features/6.0/images.md' => $page, 'features/5.1/images.md' => $page ) );
        $this->assertSame( array( 'features/6.0/images.md', 'features/5.1/images.md', 'features/3.10/images.md' ), self::ids( $search->search( 'images ' ) ) );
    }

    public function testChangelogRanksBelowAGuideWithTheSameWords()
    {
        $ids = self::ids( self::search()->search( 'velocity restart ' ) );
        $this->assertSame( 'guides/velocity.md', $ids[0] );
        $this->assertContains( 'changelogs/6.0.15.md', $ids );
        $this->assertGreaterThan( array_search( 'guides/velocity.md', $ids ), array_search( 'changelogs/6.0.15.md', $ids ) );
    }

    public function testEveryWordIsRequiredThenTheSearchRelaxes()
    {
        $search = self::search();
        $strict = $search->search( 'velocity images ' );
        $this->assertTrue( $strict['relaxed'], 'no page has both words' );
        $this->assertContains( 'help/editing.md', self::ids( $strict ) );
        $this->assertContains( 'guides/velocity.md', self::ids( $strict ) );

        $both = $search->search( 'cache velocity ' );
        $this->assertFalse( $both['relaxed'] );
        $this->assertSame( array( 'features/cache.md' ), array_values( array_diff( self::ids( $both ), array( 'changelogs/6.0.15.md' ) ) ) );
    }

    public function testTheLastWordMatchesAsAPrefixWhileTyping()
    {
        $search = self::search();
        $typing = $search->search( 'veloc' );
        $this->assertSame( 'guides/velocity.md', $typing['hits'][0]['id'] );
        $this->assertContains( 'velocity', $typing['suggest'] );

        $done = $search->search( 'veloc ' );
        $this->assertSame( 0, $done['total'], 'a trailing space ends the word' );
    }

    public function testPluralsFindTheSingular()
    {
        $this->assertContains( 'features/cache.md', self::ids( self::search()->search( 'caches ' ) ) );
        $this->assertContains( 'guides/velocity.md', self::ids( self::search()->search( 'log ' ) ) );
    }

    public function testAMinusWordExcludes()
    {
        $ids = self::ids( self::search()->search( 'velocity -changelog' ) );
        $this->assertNotContains( 'changelogs/6.0.15.md', $ids );
        $this->assertContains( 'guides/velocity.md', $ids );
    }

    public function testFiltersAndFacets()
    {
        $search = self::search();
        $all = $search->search( 'velocity ' );
        $this->assertSame( 3, $all['total'] );
        $this->assertSame( 2, $all['facets']['audience']['developer'] );
        $this->assertSame( 2, $all['facets']['audience']['admin'] );
        $this->assertSame( 1, $all['facets']['type']['changelog'] );

        $filtered = $search->search( 'velocity ', array( 'type' => 'guide,feature' ) );
        $this->assertSame( array( 'features/cache.md', 'guides/velocity.md' ), self::sorted( self::ids( $filtered ) ) );
        // a facet is counted without its own filter, so the other choices still show their counts
        $this->assertSame( 1, $filtered['facets']['type']['changelog'] );
        $this->assertSame( 1, $filtered['facets']['audience']['developer'] );

        $audience = $search->search( 'velocity ', array( 'audience' => array( 'developer' ), 'category' => 'operations' ) );
        $this->assertSame( array( 'guides/velocity.md' ), self::ids( $audience ) );

        $junk = $search->search( 'velocity ', array( 'audience' => '<script>', 'type' => array( 'x y' ) ) );
        $this->assertSame( 3, $junk['total'], 'invalid filter values are ignored' );
    }

    private static function sorted( array $list )
    {
        sort( $list );
        return $list;
    }

    public function testAnEmptyQueryBrowsesEveryPageTheFiltersAllow()
    {
        $result = self::search()->search( '' );
        $this->assertSame( 'browse', $result['mode'] );
        $this->assertSame( 5, $result['total'] );
        $this->assertSame( $result['hits'], $result['results'] );
        // help pages and guides first, changelogs last
        $this->assertSame( 'help/editing.md', $result['hits'][0]['id'] );
        $this->assertSame( 'guides/velocity.md', $result['hits'][1]['id'] );
        $this->assertSame( 'changelogs/6.0.15.md', end( $result['hits'] )['id'] );
        $this->assertSame( 1, $result['facets']['type']['guide'] );
        $this->assertSame( 3, $result['facets']['audience']['developer'] );
        $this->assertNull( $result['hits'][0]['anchor'] );
        $this->assertStringEndsWith( '/exphelp/viewer/help/editing.md', $result['hits'][0]['url'] );

        $ops = self::search()->search( '', array( 'category' => 'operations' ) );
        $this->assertSame( array( 'guides/velocity.md', 'features/cache.md' ), self::ids( $ops ) );
        $this->assertSame( 1, $ops['facets']['category']['content'], 'the category facet ignores its own filter' );

        $paged = self::search()->search( '', array(), 2, 4 );
        $this->assertSame( 5, $paged['total'] );
        $this->assertCount( 1, $paged['hits'] );

        $this->assertNotContains( 'changelogs/6.0.15.md', self::ids( self::search()->search( '-changelog' ) ), 'an excluded word alone browses without it' );
        $this->assertSame( 'browse', self::search()->search( '-changelog' )['mode'] );
    }

    public function testBrowseOrderFeaturedThenOrderThenTitle()
    {
        $page = function ( $title, $order = 0, $featured = false ) {
            return array( 'title' => $title, 'type' => 'help', 'category' => 'layouts', 'order' => $order, 'featured' => $featured,
                          'summary' => "About $title.", 'text' => "# $title\n\nText.\n" );
        };
        $search = expHelpSearch::fromDocuments( array(
            'help/zeta.md' => $page( 'Zeta' ), 'help/alpha.md' => $page( 'Alpha' ), 'help/second.md' => $page( 'Second', 2 ),
            'help/first.md' => $page( 'First', 1 ), 'help/star.md' => $page( 'Star', 9, true ) ) );
        $result = $search->search( '', array( 'category' => 'layouts' ) );
        $this->assertSame( array( 'help/star.md', 'help/first.md', 'help/second.md', 'help/alpha.md', 'help/zeta.md' ), self::ids( $result ) );
        $this->assertTrue( $result['hits'][0]['featured'] );
        $this->assertSame( 'About Star.', $result['hits'][0]['snippet'] );
        $this->assertSame( array( array( 'About Star.', false ) ), $result['hits'][0]['snippet_parts'] );
    }

    public function testSnippetsEscapeEverythingAndMarkTheWords()
    {
        $result = self::search()->search( 'escape wash ' );
        $hit = $result['hits'][0];
        $this->assertSame( 'bc/unsafe.md', $hit['id'] );
        $this->assertStringNotContainsString( '<script', $hit['snippet'] );
        $this->assertStringContainsString( '&lt;script&gt;', $hit['snippet'] );
        $this->assertStringContainsString( '<mark>escape</mark>', $hit['snippet'] );
        $this->assertStringContainsString( '<mark>wash</mark>', $hit['snippet'] );
        $this->assertSame( 0, preg_match( '/<(?!\/?mark>)/', $hit['snippet'] ), 'no tag but mark' );

        // the parts are the raw text, for clients that build text nodes
        $text = '';
        $marked = array();
        foreach ( $hit['snippet_parts'] as $part )
        {
            $text .= $part[0];
            if ( $part[1] )
                $marked[] = $part[0];
        }
        $this->assertStringContainsString( '<script>alert(1)</script>', $text );
        $this->assertSame( array( 'escape', 'wash' ), $marked );
    }

    public function testSnippetWindowFollowsTheMatches()
    {
        $text = str_repeat( 'filler words here ', 40 ) . 'the needle is here ' . str_repeat( 'more filler ', 40 );
        $html = expHelpSearch::snippet( $text, array( 'needle' ) );
        $this->assertStringContainsString( '<mark>needle</mark>', $html );
        $this->assertStringStartsWith( '… ', $html );
        $this->assertStringEndsWith( ' …', $html );
        $this->assertLessThan( 300, mb_strlen( strip_tags( $html ) ) );
        $this->assertTrue( mb_check_encoding( expHelpSearch::snippet( str_repeat( 'äöü ', 200 ) . 'needle', array( 'needle' ) ), 'UTF-8' ) );
    }

    public function testQueriesAreBounded()
    {
        $search = self::search();
        $result = $search->search( "velocity\x00\xff", array(), 500, -5 );
        $this->assertSame( 50, $result['limit'] );
        $this->assertSame( 0, $result['offset'] );
        $this->assertTrue( mb_check_encoding( $result['query'], 'UTF-8' ) );
        $this->assertLessThanOrEqual( expHelpSearch::MAX_QUERY_LENGTH, mb_strlen( $search->search( str_repeat( 'a', 5000 ) )['query'] ) );
        $paged = $search->search( 'velocity ', array(), 1, 1 );
        $this->assertCount( 1, $paged['hits'] );
        $this->assertSame( 3, $paged['total'] );
    }

    public function testStopWordsAreNotIndexed()
    {
        $result = self::search()->search( 'the ' );
        $this->assertSame( array( 'the' ), $result['terms'] );
        $this->assertSame( 'search', $result['mode'] );
        $this->assertSame( 0, $result['total'] );
        $this->assertArrayHasKey( 'audience', $result['facets'] );
        $this->assertSame( 'features/cache.md', self::search()->search( 'the template cache ' )['hits'][0]['id'] );
    }

    public function testTokenize()
    {
        $this->assertSame( array( 'ezcache', 'php', 'clear', 'tag' ), expHelpSearch::tokenize( 'ezcache.php --clear-tag' ) );
        $this->assertSame( array( 'site_access', 'site', 'access', 'ezcontentobject' ), expHelpSearch::tokenize( 'Site_Access eZContentObject' ) );
        $this->assertSame( array( 'übersicht' ), expHelpSearch::tokenize( 'Übersicht' ) );
    }

    public function testTheIndexIsCachedAsAPhpFileAndRebuiltForNewDocuments()
    {
        $this->cacheDir = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-exphelpsearch-' . getmypid() . '-' . mt_rand();
        $documents = self::documents();
        $first = expHelpSearch::fromDocuments( $documents, array( 'cacheDir' => $this->cacheDir ) );
        $this->assertSame( 5, $first->stats()['documents'] );
        $files = glob( $this->cacheDir . '/search-*.php' );
        $this->assertCount( 1, $files );

        $again = expHelpSearch::fromDocuments( $documents, array( 'cacheDir' => $this->cacheDir ) );
        $this->assertSame( self::ids( $first->search( 'velocity ' ) ), self::ids( $again->search( 'velocity ' ) ) );

        $documents['help/new.md'] = array( 'title' => 'Velocity for beginners', 'type' => 'help', 'text' => "# Velocity for beginners\n" );
        $changed = expHelpSearch::fromDocuments( $documents, array( 'cacheDir' => $this->cacheDir ) );
        $this->assertContains( 'help/new.md', self::ids( $changed->search( 'beginners ' ) ) );
        $this->assertCount( 1, glob( $this->cacheDir . '/search-*.php' ), 'the file of the old documents is removed' );
    }
}
