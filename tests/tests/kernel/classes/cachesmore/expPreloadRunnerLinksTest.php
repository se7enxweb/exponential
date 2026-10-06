<?php
/**
 * How the cache preloader (expPreloadRunner) decides what to request, without requesting anything: the links it
 * takes from a page (same host, same site, no files, no module or admin paths, no in-page anchors), how it writes an
 * address so one page is fetched once, which siteaccess prefixes it knows, and the referrers and problems it keeps
 * for its closing report.
 *
 * No database, no network: the private helpers are called directly.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once dirname( __DIR__ ) . '/radwizards/expRadWizardTestHelper.php';

class expPreloadRunnerLinksTest extends PHPUnit\Framework\TestCase
{
    private $injected;
    private $said = array();

    public static function setUpBeforeClass(): void
    {
        expRadWizardTestHelper::boot();
    }

    protected function setUp(): void
    {
        expRadWizardTestHelper::boot();
        $this->injected = expRadWizardTestHelper::injectIni( 'site.ini', array( 'SiteAccessSettings' => array(
            'AvailableSiteAccessList' => array( 'k1e_site', 'k1e_other', 'k1e_admin' ), 'RelatedSiteAccessList' => array( 'k1e_site', '' ) ) ) );
    }

    protected function tearDown(): void
    {
        expRadWizardTestHelper::restoreInjected( $this->injected );
    }

    private function runner( array $options = array() )
    {
        $said =& $this->said;
        return new expPreloadRunner( function ( $type, $message, $data ) use ( &$said ) { $said[] = array( $type, $message, $data ); },
                                     $options + array( 'base_url' => 'https://k1e.example.invalid', 'base_path' => '' ) );
    }

    private static function call( $runner, $method, array $arguments = array() )
    {
        return ( new ReflectionMethod( 'expPreloadRunner', $method ) )->invokeArgs( $runner, $arguments );
    }

    public static function normaliseProvider()
    {
        return array(
            'host only' => array( 'https://k1e.example.invalid', 'https://k1e.example.invalid/' ),
            'root' => array( 'https://k1e.example.invalid/', 'https://k1e.example.invalid/' ),
            'trailing slash' => array( 'https://k1e.example.invalid/news/', 'https://k1e.example.invalid/news' ),
            'index.php' => array( 'https://k1e.example.invalid/index.php/news', 'https://k1e.example.invalid/news' ),
            'index.php alone' => array( 'https://k1e.example.invalid/index.php', 'https://k1e.example.invalid/' ),
            'index.php in a name' => array( 'https://k1e.example.invalid/index.phpx', 'https://k1e.example.invalid/index.phpx' ),
            'query kept' => array( 'https://k1e.example.invalid/search?SearchText=x', 'https://k1e.example.invalid/search?SearchText=x' ),
            'query after a trailing slash' => array( 'https://k1e.example.invalid/news/?offset=10', 'https://k1e.example.invalid/news?offset=10' ),
            'slash in the query' => array( 'https://k1e.example.invalid/news/?next=/a/b', 'https://k1e.example.invalid/news?next=/a/b' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('normaliseProvider')]
    public function testOnePageHasOneAddress( $url, $expected )
    {
        $this->assertSame( $expected, self::call( $this->runner(), 'normalise', array( $url ) ) );
    }

    public function testLinksTakenFromAPage()
    {
        $html = '<a href="/news">News</a> <a class="x" href="/news/">again</a> <a href="about">relative</a>'
              . ' <a href="#main">skip</a> <a href="/contact#form">anchor</a> <a href="mailto:x@k1e.example.invalid">mail</a>'
              . ' <a href="javascript:void(0)">js</a> <a href="https://elsewhere.example.invalid/x">other host</a>'
              . ' <a href="//k1e.example.invalid/protocol-relative">pr</a> <a href="/files/report.PDF">pdf</a>'
              . ' <a href="/visual/templateview/x">admin</a> <a href="/k1e_site/content/edit/12">edit</a>'
              . ' <a href="/k1e_other/page">other siteaccess</a> <a href="/index.php/old">old</a>'
              . ' <a href="/tags?x=1&amp;y=2">entity</a> <A HREF="/upper">upper</A> <a href="">empty</a>'
              . ' <a href="/content/search">search</a> <a href="/user/login">login</a>';
        $links = self::call( $this->runner(), 'linksFrom', array( $html, 'https://k1e.example.invalid/blog/page', 'https://k1e.example.invalid' ) );
        $this->assertSame( array(
            'https://k1e.example.invalid/news',
            'https://k1e.example.invalid/blog/about',
            'https://k1e.example.invalid/contact',
            'https://k1e.example.invalid/protocol-relative',
            'https://k1e.example.invalid/old',
            'https://k1e.example.invalid/tags?x=1&y=2',
            'https://k1e.example.invalid/upper',
        ), $links );
        $this->assertSame( array(), self::call( $this->runner(), 'linksFrom', array( '', 'https://k1e.example.invalid/', 'https://k1e.example.invalid' ) ) );
    }

    public function testASiteBelowAPrefixKeepsToIt()
    {
        $runner = $this->runner( array( 'base_path' => '/k1e_site/' ) );
        $this->assertSame( '/k1e_site', $runner->basePath() );
        $this->assertTrue( self::call( $runner, 'belongsToSite', array( '/k1e_site' ) ) );
        $this->assertTrue( self::call( $runner, 'belongsToSite', array( '/k1e_site/news' ) ) );
        $this->assertFalse( self::call( $runner, 'belongsToSite', array( '/k1e_site_old/news' ) ) );
        $this->assertFalse( self::call( $runner, 'belongsToSite', array( '/news' ) ) );
        $links = self::call( $runner, 'linksFrom', array( '<a href="/k1e_site/a">a</a><a href="/b">b</a><a href="c">c</a>', 'https://k1e.example.invalid/k1e_site/x', 'https://k1e.example.invalid/k1e_site' ) );
        $this->assertSame( array( 'https://k1e.example.invalid/k1e_site/a', 'https://k1e.example.invalid/k1e_site/c' ), $links );

        $hostRun = $this->runner();
        $this->assertTrue( self::call( $hostRun, 'belongsToSite', array( '/news' ) ) );
        $this->assertFalse( self::call( $hostRun, 'belongsToSite', array( '/k1e_other/news' ) ) );
        $this->assertFalse( self::call( $hostRun, 'belongsToSite', array( '/k1e_other' ) ) );
    }

    public static function modulePathProvider()
    {
        return array(
            array( '/visual/templateview/x', true ), array( '/setup', true ), array( '/content/edit/1', true ),
            array( '/content/view/full/2', false ), array( '/content/search', true ), array( '/user/login', true ),
            array( '/user/register', false ), array( '/k1e_site/visual/menu', true ), array( '/unknown_prefix/visual/menu', false ),
            array( '/a/collapse-12', true ), array( '/a/debug-end', true ), array( '/news/stats', true ), array( '/settingsx', false ),
            array( '/healthy-eating', false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modulePathProvider')]
    public function testModulePathsAreNotCrawled( $path, $module )
    {
        $this->assertSame( $module, self::call( $this->runner(), 'isModulePath', array( $path ) ) );
    }

    public function testSiteaccessNames()
    {
        $this->assertSame( array( 'k1e_site' => true, 'k1e_other' => true, 'k1e_admin' => true ), self::call( $this->runner(), 'siteaccessNames' ) );
    }

    public function testReferrersAndProblemsForTheReport()
    {
        $runner = $this->runner();
        $target = 'https://k1e.example.invalid/missing';
        self::call( $runner, 'noteReferrer', array( $target, $target ) );
        $this->assertSame( array(), $runner->referrersOf( $target ), 'a page linking to itself is no referrer' );
        $this->assertSame( '  (a starting page)', self::call( $runner, 'referrerNote', array( $target ) ) );

        for ( $i = 1; $i <= expPreloadRunner::MAX_REFERRERS + 3; $i++ )
            self::call( $runner, 'noteReferrer', array( $target, "https://k1e.example.invalid/p$i" ) );
        self::call( $runner, 'noteReferrer', array( $target, 'https://k1e.example.invalid/p1' ) );
        $this->assertCount( expPreloadRunner::MAX_REFERRERS, $runner->referrersOf( $target ) );
        $this->assertSame( 'https://k1e.example.invalid/p1', $runner->referrersOf( $target )[0] );
        $this->assertSame( '  linked from /p1 and ' . ( expPreloadRunner::MAX_REFERRERS - 1 + 3 ) . ' other pages', self::call( $runner, 'referrerNote', array( $target ) ) );

        self::call( $runner, 'noteProblem', array( $target, 404, 'Missing pages (404)' ) );
        self::call( $runner, 'noteProblem', array( $target, 404, 'Missing pages (404)' ) );
        self::call( $runner, 'noteProblem', array( 'https://k1e.example.invalid/', 500, 'Server errors (500)' ) );
        $problems = $runner->problems();
        $this->assertCount( 2, $problems, 'one entry per target' );
        $this->assertSame( array( 'url' => $target, 'path' => '/missing', 'status' => 404, 'reason' => 'Missing pages (404)' ),
                           array_intersect_key( $problems[0], array_flip( array( 'url', 'path', 'status', 'reason' ) ) ) );
        $this->assertSame( 3, $problems[0]['more'] );

        self::call( $runner, 'report' );
        list( $type, $message, $data ) = end( $this->said );
        $this->assertSame( 'report', $type );
        $this->assertStringContainsString( '2 broken links on ' . expPreloadRunner::MAX_REFERRERS . ' pages of this site.', $message );
        $this->assertStringContainsString( '... and 3 more', $message );
        $this->assertStringContainsString( 'a starting page; nothing on the site links to it', $message );
        $this->assertCount( 2, $data['broken'] );

        $clean = $this->runner();
        self::call( $clean, 'report' );
        $this->assertSame( array( 'report', 'No broken links were found.', array( 'broken' => array() ) ), end( $this->said ) );
        $this->assertSame( array( 'fetched' => 0, 'skipped' => 0, 'broken' => 0, 'denied' => 0, 'bytes' => 0, 'images' => 0, 'images_broken' => 0 ), $clean->counts() );
    }

    public function testSizesAndShortening()
    {
        $runner = $this->runner();
        $this->assertSame( '512B', self::call( $runner, 'formatBytes', array( 512 ) ) );
        $this->assertSame( '1.5K', self::call( $runner, 'formatBytes', array( 1536 ) ) );
        $this->assertSame( '2M', self::call( $runner, 'formatBytes', array( 2097152 ) ) );
        $this->assertSame( 'abc', self::call( $runner, 'shorten', array( 'abc', 3 ) ) );
        $this->assertSame( "ab\xe2\x80\xa6", self::call( $runner, 'shorten', array( 'abcd', 3 ) ) );
    }

    public function testNoSiteUrlIsNothingWarmed()
    {
        $injected = expRadWizardTestHelper::injectIni( 'site.ini', array( 'SiteSettings' => array( 'SiteURL' => '' ) ) );
        try
        {
            $runner = $this->runner( array( 'base_url' => '' ) );
            $this->assertFalse( $runner->baseUrl() );
            $this->assertFalse( $runner->run() );
            $this->assertSame( array( 'error', 'done' ), array_column( $this->said, 0 ) );
        }
        finally
        {
            expRadWizardTestHelper::restoreInjected( $injected );
        }
        $this->assertSame( 'https://k1e.example.invalid', $this->runner( array( 'base_url' => ' k1e.example.invalid/ ' ) )->baseUrl() );
        $this->assertSame( 'http://k1e.example.invalid', $this->runner( array( 'base_url' => 'http://k1e.example.invalid' ) )->baseUrl() );
    }

    public function testStartPathsAreTheOnlyPagesAskedFor()
    {
        $runner = $this->runner( array( 'start_paths' => array( '/about/', 'news', '/about' ) ) );
        $this->assertSame( array( 'https://k1e.example.invalid/about', 'https://k1e.example.invalid/news' ), $runner->startUrls( 'https://k1e.example.invalid' ) );
    }
}
