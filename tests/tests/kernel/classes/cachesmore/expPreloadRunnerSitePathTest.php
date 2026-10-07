<?php
/**
 * Which links belong to the site the cache preloader warms, under every way a siteaccess is matched, and the section
 * pages it warms first. No database, no network: the private helpers are called directly, on the front page of the
 * demo installation (uri matching, SiteURL latest.demo.exponential.earth/site) saved as a fixture.
 *
 *  SP-01 uri matching with the prefix in SiteURL: every /site/... link is the site's, nothing outside /site (/,
 *        /admin/..., /bold/...) is. Only the front page was warmed before: the prefix in SiteURL left none to
 *        compare with, and /site/... was dropped as another siteaccess's
 *  SP-02 the same site given as host plus prefix gives the same links, and the prefix is never doubled
 *  SP-03 host matching at the root of a host: everything but other siteaccesses' prefixes
 *  SP-04 an installation in a folder matched by host: the folder, without other siteaccesses below it
 *  SP-05 a host_uri prefix (/de): its own pages only
 *  SP-06 the section pages: the queued top level pages below the site, in order, without deeper ones or queries
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

require_once dirname( __DIR__ ) . '/radwizards/expRadWizardTestHelper.php';

class expPreloadRunnerSitePathTest extends PHPUnit\Framework\TestCase
{
    const DEMO = 'https://latest.demo.exponential.earth';

    private $injected;

    public static function setUpBeforeClass(): void
    {
        expRadWizardTestHelper::boot();
    }

    protected function setUp(): void
    {
        expRadWizardTestHelper::boot();
        $this->injected = expRadWizardTestHelper::injectIni( 'site.ini', array( 'SiteAccessSettings' => array(
            'AvailableSiteAccessList' => array( 'site', 'admin', 'bold', 'de', 'en' ), 'RelatedSiteAccessList' => array( 'site', 'admin' ) ) ) );
    }

    protected function tearDown(): void
    {
        expRadWizardTestHelper::restoreInjected( $this->injected );
    }

    private static function runner( $base, $prefix )
    {
        return new expPreloadRunner( function () {}, array( 'base_url' => $base, 'base_path' => $prefix ) );
    }

    private static function call( $runner, $method, array $arguments = array() )
    {
        return ( new ReflectionMethod( 'expPreloadRunner', $method ) )->invokeArgs( $runner, $arguments );
    }

    private static function demoPage()
    {
        return file_get_contents( __DIR__ . '/fixtures/preload-demo-site-front-page-uri-matching.html' )
             // links of other siteaccesses, as an administrator's page or a site switcher would have them
             . '<a href="/admin/setup/preload">a</a><a href="' . self::DEMO . '/admin/content/view/full/2">b</a><a href="/admin">c</a>';
    }

    private static function paths( array $urls )
    {
        return array_map( function ( $url ) { return (string)parse_url( $url, PHP_URL_PATH ); }, $urls );
    }

    /** SP-01 */
    public function testUriMatchingWithThePrefixInSiteUrl()
    {
        $runner = self::runner( self::DEMO . '/site', '' );
        $this->assertSame( '/site', $runner->sitePath() );
        $links = self::call( $runner, 'linksFrom', array( self::demoPage(), self::DEMO . '/site/', self::DEMO . '/site' ) );
        $paths = self::paths( $links );
        $this->assertGreaterThan( 30, count( $paths ), 'the front page links to the whole site' );
        foreach ( array( '/site/fitness', '/site/healthy-eating', '/site/recipes', '/site/video', '/site/exponential-live' ) as $section )
            $this->assertContains( $section, $paths );
        foreach ( $paths as $path )
            $this->assertMatchesRegularExpression( '#^/site(/|$)#', $path );
        $this->assertSame( array(), preg_grep( '#^/(admin|bold)(/|$)|^/$#', $paths ), 'other siteaccesses and the host root are not this site' );
        $this->assertNotContains( '/site/content/search', $paths, 'module views stay out' );
    }

    /** SP-02 */
    public function testHostPlusPrefixIsTheSameSite()
    {
        $prefixed = self::runner( self::DEMO, '/site' );
        $this->assertSame( '/site', $prefixed->sitePath() );
        $this->assertSame( '/site', self::runner( self::DEMO . '/site', '/site' )->sitePath(), 'not /site/site' );
        $this->assertSame( self::call( self::runner( self::DEMO . '/site', '' ), 'linksFrom', array( self::demoPage(), self::DEMO . '/site/', self::DEMO . '/site' ) ),
                           self::call( $prefixed, 'linksFrom', array( self::demoPage(), self::DEMO . '/site/', self::DEMO ) ) );
    }

    /** SP-03 */
    public function testHostMatchingAtTheRoot()
    {
        $runner = self::runner( 'https://example.test', '' );
        $this->assertSame( '', $runner->sitePath() );
        $this->assertTrue( self::call( $runner, 'belongsToSite', array( '/fitness' ) ) );
        $this->assertTrue( self::call( $runner, 'belongsToSite', array( '/' ) ) );
        $this->assertFalse( self::call( $runner, 'belongsToSite', array( '/admin/setup' ) ) );
        $this->assertFalse( self::call( $runner, 'belongsToSite', array( '/site/fitness' ) ), 'another siteaccess by its prefix' );
    }

    /** SP-04 */
    public function testAnInstallationInAFolder()
    {
        $runner = self::runner( 'https://example.test/cms', '' );
        $this->assertSame( '/cms', $runner->sitePath() );
        $this->assertTrue( self::call( $runner, 'belongsToSite', array( '/cms/fitness' ) ) );
        $this->assertTrue( self::call( $runner, 'belongsToSite', array( '/cms' ) ) );
        $this->assertFalse( self::call( $runner, 'belongsToSite', array( '/cms/admin/setup' ) ) );
        $this->assertFalse( self::call( $runner, 'belongsToSite', array( '/fitness' ) ) );
        $this->assertFalse( self::call( $runner, 'belongsToSite', array( '/cmsx/fitness' ) ) );
    }

    /** SP-05 */
    public function testHostUriPrefix()
    {
        $runner = self::runner( 'https://example.test', '/de' );
        $this->assertTrue( self::call( $runner, 'belongsToSite', array( '/de/nachrichten' ) ) );
        $this->assertFalse( self::call( $runner, 'belongsToSite', array( '/en/news' ) ) );
        $this->assertFalse( self::call( $runner, 'belongsToSite', array( '/news' ) ) );
    }

    /** SP-06 */
    public function testSectionPagesAreTheTopLevel()
    {
        $runner = self::runner( self::DEMO . '/site', '' );
        $queue = new ReflectionProperty( 'expPreloadRunner', 'queue' );
        if ( PHP_VERSION_ID < 80100 )
            $queue->setAccessible( true );
        $queue->setValue( $runner, array(
            array( 'url' => self::DEMO . '/site/fitness', 'depth' => 1 ),
            array( 'url' => self::DEMO . '/site/fitness/running-shoes', 'depth' => 1 ),
            array( 'url' => self::DEMO . '/site/recipes', 'depth' => 1 ),
            array( 'url' => self::DEMO . '/site/search?SearchText=x', 'depth' => 1 ),
            array( 'url' => self::DEMO . '/site', 'depth' => 1 ),
        ) );
        $this->assertSame( array( self::DEMO . '/site/fitness', self::DEMO . '/site/recipes' ),
                           array_column( self::call( $runner, 'takeTopLevel' ), 'url' ) );
        $this->assertSame( array( self::DEMO . '/site/fitness/running-shoes', self::DEMO . '/site/search?SearchText=x', self::DEMO . '/site' ),
                           array_column( $queue->getValue( $runner ), 'url' ), 'the rest stays queued for the crawl' );

        $host = self::runner( 'https://example.test', '' );
        $queue->setValue( $host, array( array( 'url' => 'https://example.test/fitness', 'depth' => 1 ), array( 'url' => 'https://example.test/a/b', 'depth' => 1 ) ) );
        $this->assertSame( array( 'https://example.test/fitness' ), array_column( self::call( $host, 'takeTopLevel' ), 'url' ) );
    }
}
