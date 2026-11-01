<?php
/**
 * The navigation tree of the help (expHelpNavigation, doc/eng-us/features/6.0/help-system.md, "Viewer"), without a database.
 *
 *  HN-01  every page of the tree is linked once, at any depth, nothing capped; folders carry their page count
 *  HN-02  the folders on the way to the current page are open, the others closed; the current page has aria-current
 *  HN-03  titles and addresses are escaped; a folder's README is its "Overview" link, not one of its pages
 *  HN-04  folder names: configured, README title, built-in, humanized
 *  HN-05  the built tree is cached per index signature and rebuilt when it changes
 *  HN-06  a folded folder ([NavigationSettings] FoldedFolders[]) lists its pages only on the way to the current page
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

// a checkout whose class map was built before the class existed
if ( !class_exists( 'expHelpNavigation' ) )
    require_once __DIR__ . '/../../../../../kernel/classes/help/exphelpnavigation.php';

class expHelpNavigationTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $cache;

    protected function setUp(): void
    {
        $this->cache = sys_get_temp_dir() . '/exphelpnav-' . getmypid() . '-' . mt_rand();
    }

    protected function tearDown(): void
    {
        if ( is_dir( $this->cache . '/nav' ) )
        {
            foreach ( glob( $this->cache . '/nav/*' ) as $file )
                unlink( $file );
            rmdir( $this->cache . '/nav' );
        }
        if ( is_dir( $this->cache ) )
            rmdir( $this->cache );
    }

    protected static function node( $name, $path, $title, array $children = array(), array $documents = array(), $index = null )
    {
        return array( 'name' => $name, 'path' => $path, 'title' => $title, 'index' => $index, 'index_id' => $index, 'order' => 1000,
                      'children' => $children, 'documents' => $documents );
    }

    protected static function doc( $id, $title )
    {
        return array( 'id' => $id, 'path' => 'doc/' . $id, 'title' => $title, 'order' => 1000, 'type' => 'feature', 'audience' => array() );
    }

    protected function tree()
    {
        $deep = array();
        for ( $i = 1; $i <= 150; $i++ )
            $deep[] = self::doc( "features/6.0/extensions/page$i.md", "Page $i" );
        return self::node( '', '', '', array(
            self::node( 'features', 'features', 'Features', array(
                self::node( '6.0', 'features/6.0', '6.0', array(
                    self::node( 'extensions', 'features/6.0/extensions', 'Extensions in 6.0', array(), $deep, 'features/6.0/extensions/README.md' ),
                ), array( self::doc( 'features/6.0/help-system.md', 'The <help> system' ) ) ),
            ) ),
            self::node( 'bc', 'bc', 'Bc', array(), array( self::doc( 'bc/a.md', 'A' ) ) ),
            self::node( 'empty', 'empty', 'Empty' ),
        ), array( self::doc( 'INSTALL.md', 'Install' ) ), 'README.md' );
    }

    protected function index( $signature = 'one', $tree = null )
    {
        $tree = $tree === null ? $this->tree() : $tree;
        $cache = $this->cache;
        return new class( $signature, $tree, $cache ) {
            public $signature; public $tree; public $cache; public $calls = 0;
            public function __construct( $signature, $tree, $cache ) { $this->signature = $signature; $this->tree = $tree; $this->cache = $cache; }
            public function signature() { return $this->signature; }
            public function cacheDir() { return $this->cache; }
            public function tree() { $this->calls++; return $this->tree; }
        };
    }

    /** HN-01 */
    public function testEveryPageLinkedOnceWithCounts()
    {
        $html = ( new expHelpNavigation( $this->index() ) )->html();
        preg_match_all( '#href="[^"]*/exphelp/viewer/([^"]+)"#', $html, $m );
        $this->assertCount( 155, $m[1], '150 deep pages, the extensions README, help-system, bc/a, INSTALL and the root README' );
        $this->assertSame( array_unique( $m[1] ), $m[1] );
        $this->assertStringContainsString( 'features/6.0/extensions/page150.md', $html );
        $this->assertStringNotContainsString( 'data-nav-path="empty"', $html, 'a folder without pages is left out' );
        $this->assertStringContainsString( '<span class="a4-help-nav-count" aria-hidden="true">152</span>', $html, 'features: 150 + README + help-system' );
        $this->assertSame( 155, ( new expHelpNavigation( $this->index() ) )->count() );
    }

    /** HN-02 */
    public function testCurrentPathOpen()
    {
        $html = ( new expHelpNavigation( $this->index() ) )->html( 'features/6.0/extensions/page7.md' );
        $this->assertStringContainsString( '<details open class="a4-help-nav-details" data-nav-path="features">', $html );
        $this->assertStringContainsString( '<details open class="a4-help-nav-details" data-nav-path="features/6.0/extensions">', $html );
        $this->assertStringContainsString( '<details class="a4-help-nav-details" data-nav-path="bc">', $html );
        $this->assertSame( 1, substr_count( $html, 'aria-current="page"' ) );
        $this->assertMatchesRegularExpression( '#/page7\.md" aria-current="page"#', $html );
        $all = ( new expHelpNavigation( $this->index() ) )->html( '', true );
        $this->assertStringNotContainsString( '<details class=', $all, 'the contents page opens every folder' );
    }

    /** HN-03 */
    public function testEscapingAndOverview()
    {
        $html = ( new expHelpNavigation( $this->index() ) )->html();
        $this->assertStringContainsString( 'The &lt;help&gt; system', $html );
        $this->assertStringNotContainsString( '<help>', $html );
        $this->assertMatchesRegularExpression( '#extensions/README\.md">Overview</a>#', $html );
        $tree = self::node( '', '', '', array(), array( self::doc( '"><script>.md', 'x' ) ) );
        $html = ( new expHelpNavigation( $this->index( 'two', $tree ) ) )->html();
        $this->assertStringNotContainsString( '<script>', $html );
    }

    /** HN-04 */
    public function testFolderTitles()
    {
        $this->assertSame( 'Extensions in 6.0', expHelpNavigation::folderTitle( self::node( 'extensions', 'x/extensions', 'Extensions in 6.0' ), array() ) );
        $this->assertSame( 'Backward compatibility', expHelpNavigation::folderTitle( self::node( 'bc', 'bc', 'Bc' ), array() ) );
        $this->assertSame( 'Mine', expHelpNavigation::folderTitle( self::node( 'bc', 'bc', 'Bc' ), array( 'bc' => 'Mine' ) ) );
        $this->assertSame( 'Some dir', expHelpNavigation::folderTitle( self::node( 'some-dir', 'a/some-dir', 'Some dir' ), array() ) );
    }

    /** HN-05 */
    public function testCachedPerSignature()
    {
        $index = $this->index();
        ( new expHelpNavigation( $index ) )->html( 'bc/a.md' );
        ( new expHelpNavigation( $index ) )->html( 'INSTALL.md' );
        $this->assertSame( 1, $index->calls, 'the second request reads the cached tree' );
        $index->signature = 'changed';
        ( new expHelpNavigation( $index ) )->html();
        $this->assertSame( 2, $index->calls );
    }

    /** HN-06 a folded folder shows its overview and count only; its pages appear on the way to the current page */
    public function testFoldedFolders()
    {
        $nav = ( new expHelpNavigation( $this->index() ) )->setFoldedFolders( array( 'features/6.0/extensions' ) );
        $html = $nav->html( 'INSTALL.md' );
        $this->assertStringNotContainsString( 'page7.md', $html, 'folded: its pages are not listed' );
        $this->assertMatchesRegularExpression( '#extensions/README\.md">Overview</a>#', $html, 'the overview link stays' );
        $this->assertStringContainsString( '<span class="a4-help-nav-count" aria-hidden="true">151</span>', $html, 'the count stays' );
        $this->assertStringNotContainsString( '<!--', $html, 'no mark is left in the page' );

        $html = ( new expHelpNavigation( $this->index() ) )->setFoldedFolders( array( 'features/6.0/extensions' ) )->html( 'features/6.0/extensions/page7.md' );
        $this->assertSame( 150, preg_match_all( '#/extensions/page\d+\.md"#', $html ), 'inside the folder every page is listed' );
        $this->assertMatchesRegularExpression( '#/page7\.md" aria-current="page"#', $html );
        $this->assertStringContainsString( '<details open class="a4-help-nav-details" data-nav-path="features/6.0/extensions">', $html );

        $all = ( new expHelpNavigation( $this->index() ) )->setFoldedFolders( array( 'features/6.0/extensions' ) )->html( '', true );
        $this->assertStringNotContainsString( 'page7.md', $all, 'the contents page keeps folded folders folded' );
        $this->assertStringNotContainsString( '<!--', $all );
    }

    /** HN-07 the viewer's tree: the top folders, the way to the page open with its pages, the rest closed and lazy */
    public function testPathHtmlOpensOnlyTheWayToThePage()
    {
        $html = ( new expHelpNavigation( $this->index() ) )->pathHtml( 'features/6.0/extensions/page7.md' );
        $this->assertStringStartsWith( '<ul class="a4-help-nav-list is-top" role="list">', $html );
        $this->assertStringContainsString( '<details open class="a4-help-nav-details" data-nav-path="features">', $html );
        $this->assertStringContainsString( '<details open class="a4-help-nav-details" data-nav-path="features/6.0">', $html );
        $this->assertStringContainsString( '<details open class="a4-help-nav-details" data-nav-path="features/6.0/extensions">', $html );
        $this->assertSame( 150, preg_match_all( '#/extensions/page\d+\.md"#', $html ), 'the folder of the page lists all its pages' );
        $this->assertMatchesRegularExpression( '#/page7\.md" aria-current="page"#', $html );
        $this->assertSame( 1, substr_count( $html, 'aria-current="page"' ) );
        // bc is closed, its list loads on demand; without JavaScript it links to its listing
        $this->assertMatchesRegularExpression( '#<details class="a4-help-nav-details" data-nav-path="bc" data-nav-lazy="[^"]*/exphelp/navigation/bc/\(format\)/fragment">#', $html );
        $this->assertStringNotContainsString( 'bc/a.md', $html, 'a closed folder does not list its pages' );
        $this->assertMatchesRegularExpression( '#<a class="a4-help-nav-link is-more" href="[^"]*/exphelp/navigation/bc">#', $html );
        $this->assertStringNotContainsString( '<!--', $html, 'no mark is left in the page' );
        $this->assertStringNotContainsString( 'data-nav-path="empty"', $html );
        $this->assertStringContainsString( 'README.md">About this documentation</a>', $html );
        $this->assertStringContainsString( 'INSTALL.md"', $html, 'the pages at the top are listed' );

        $top = ( new expHelpNavigation( $this->index() ) )->pathHtml( 'INSTALL.md' );
        $this->assertStringNotContainsString( 'page7.md', $top );
        $this->assertStringNotContainsString( '<details open', $top, 'a page at the top opens no folder' );
        $this->assertMatchesRegularExpression( '#/INSTALL\.md" aria-current="page"#', $top );
        $this->assertLessThan( strlen( $html ), strlen( $top ) );

        // an overview page opens its own folder and is marked there
        $readme = ( new expHelpNavigation( $this->index() ) )->pathHtml( 'features/6.0/extensions/README.md' );
        $this->assertStringContainsString( '<details open class="a4-help-nav-details" data-nav-path="features/6.0/extensions">', $readme );
        $this->assertMatchesRegularExpression( '#extensions/README\.md" aria-current="page">Overview</a>#', $readme );

        // an id whose folders are not in the tree opens nothing
        $unknown = ( new expHelpNavigation( $this->index() ) )->pathHtml( 'nowhere/deep/x.md' );
        $this->assertStringNotContainsString( '<details open', $unknown );
    }

    /** HN-08 a folder's list items (the fragment the viewer loads), escaped, its subfolders lazy */
    public function testFolderHtml()
    {
        $nav = new expHelpNavigation( $this->index() );
        $features = $nav->folderHtml( 'features' );
        $this->assertStringNotContainsString( 'help-system.md', $features, 'the pages of features/6.0 are not listed in features' );
        $this->assertStringContainsString( 'data-nav-path="features/6.0" data-nav-lazy=', $features );
        $features = $nav->folderHtml( 'features/6.0' );
        $this->assertStringContainsString( 'The &lt;help&gt; system', $features );
        $this->assertStringContainsString( 'data-nav-path="features/6.0/extensions" data-nav-lazy=', $features );
        $this->assertStringNotContainsString( 'page7.md', $features );
        $this->assertStringNotContainsString( '<!--', $features );
        $this->assertStringStartsWith( '<li', $features, 'list items only, for the folder\'s own list' );
        $deep = $nav->folderHtml( '/features/6.0/extensions/' );
        $this->assertSame( 150, preg_match_all( '#/extensions/page\d+\.md"#', $deep ) );
        $this->assertMatchesRegularExpression( '#^<li class="a4-help-nav-page"><a class="a4-help-nav-link is-index" href="[^"]*extensions/README\.md">Overview</a></li>#', $deep );
        $this->assertNull( $nav->folderHtml( 'nowhere' ) );
        $this->assertNull( $nav->folderHtml( 'empty' ), 'a folder without pages is not listed' );
        $this->assertNull( $nav->folderHtml( '../etc' ) );
        $marked = $nav->folderHtml( 'bc', 'bc/a.md' );
        $this->assertMatchesRegularExpression( '#/bc/a\.md" aria-current="page"#', $marked );
        $info = $nav->folderInfo( 'features/6.0/extensions' );
        $this->assertSame( array( 'path' => 'features/6.0/extensions', 'title' => 'Extensions in 6.0', 'count' => 151,
                                  'index_id' => 'features/6.0/extensions/README.md' ), $info );
        $this->assertNull( $nav->folderInfo( 'nowhere' ) );
    }

    /** HN-09 each level is cached per signature: a second request walks no tree, a new signature builds again */
    public function testLevelsCachedPerSignature()
    {
        $index = $this->index();
        $first = ( new expHelpNavigation( $index ) )->pathHtml( 'features/6.0/extensions/page7.md' );
        $calls = $index->calls;
        $this->assertSame( 1, $calls, 'four levels, one walk of the tree' );
        $again = ( new expHelpNavigation( $index ) )->pathHtml( 'features/6.0/extensions/page7.md' );
        ( new expHelpNavigation( $index ) )->folderHtml( 'features' );
        ( new expHelpNavigation( $index ) )->pathHtml( 'bc/a.md' );
        $this->assertSame( $first, $again );
        $this->assertSame( $calls + 1, $index->calls, 'only bc, not seen before, walks the tree' );
        ( new expHelpNavigation( $index ) )->folderHtml( 'nowhere' );
        ( new expHelpNavigation( $index ) )->folderHtml( 'nowhere' );
        $this->assertSame( $calls + 2, $index->calls, 'an unknown folder is remembered too' );
        $index->signature = 'changed';
        ( new expHelpNavigation( $index ) )->pathHtml( 'bc/a.md' );
        $this->assertSame( $calls + 3, $index->calls, 'top and bc again, from one walk of the tree' );
    }

    /** HN-10 the fragment view (exphelp/navigation/<folder>/(format)/fragment|tree) */
    public function testFragmentView()
    {
        if ( !class_exists( 'Exponential\\View\\Kernel\\Exphelp\\Navigation' ) )
        {
            if ( !class_exists( 'Exponential\\Runnable\\ModuleView' ) )
                $this->markTestSkipped( 'the module view base class is not loadable here' );
            require_once __DIR__ . '/../../../../../kernel/private/classes/views/exphelp/navigation.php';
        }
        $nav = new expHelpNavigation( $this->index() );
        $view = 'Exponential\\View\\Kernel\\Exphelp\\Navigation';
        $this->assertSame( $nav->folderHtml( 'bc' ), $view::fragment( $nav, 'bc', 'fragment' ) );
        $this->assertStringContainsString( 'bc/a.md', $view::fragment( $nav, 'bc/', 'fragment' ) );
        $this->assertNull( $view::fragment( $nav, 'nowhere', 'fragment' ) );
        $this->assertNull( $view::fragment( $nav, 'bc', 'json' ) );
        $tree = $view::fragment( $nav, '', 'tree' );
        $this->assertSame( 155, preg_match_all( '#href="[^"]*/exphelp/viewer/[^"]+"#', $tree ), 'the filter gets every page' );
        $this->assertStringNotContainsString( 'data-nav-lazy', $tree );
        $this->assertStringNotContainsString( '<details open', $tree );
        $module = file_get_contents( __DIR__ . '/../../../../../kernel/exphelp/module.php' );
        $this->assertMatchesRegularExpression( "#\\\$ViewList\['navigation'\] = array\(.*?'functions' => array\( 'read' \)#s", $module, 'the policy of the viewer' );
    }
}
