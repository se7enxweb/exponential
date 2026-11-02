<?php
/**
 * A static build of a target with several siteaccesses on one host (SiteAccessList[]) end to end, on SQLite in
 * process: the English site at the root of the host and the German one below /ger (URI matching), answered by a fake
 * request function. No server, no network, no installation's database.
 *
 *  SM-01 both siteaccesses in one copy: the default one at its root, the other in its folder; the links between them
 *        stay in the copy (relative), hreflang alternates are absolute below the public address, a link to a
 *        siteaccess the target does not publish is outside (OutsideLinks=unlink: a placeholder); one sitemap lists
 *        both, one 404 (the default one's), the search index tags each page with its siteaccess and the search page
 *        offers each; the manifest names both; the link check finds nothing broken at the root and below a base path
 *  SM-02 without a DefaultSiteAccess both are in their folders and the root links to them
 *  SM-03 --siteaccess: only the German pages are built, the English ones of the last build stay; --nodes builds a
 *        node's page in each siteaccess
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/migration/expMigrationRunnerEngineChecks.php';

class expStaticBuildMultiSiteSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationRunnerEngineChecks;

    /** @var eZSQLite3DB|null */
    private $db;

    /** host path => response */
    private $site = array();

    /** the requests made, by host path */
    private $requested = array();

    const CONTEXT = array( 'siteaccesses' => array( 'eng', 'ger', 'classic', 'admin' ), 'admin' => array( 'admin' ),
                           'urls' => array( 'eng' => 'www.example.com', 'ger' => 'www.example.com/ger', 'classic' => 'www.example.com/classic', 'admin' => 'admin.example.com' ),
                           'prefixes' => array( 'eng' => 'eng', 'ger' => 'ger', 'classic' => 'classic', 'admin' => 'admin' ) );

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $this->db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->runnerDir() . '/kernel.sqlite',
                                            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                            'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                            'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                            'use_persistent_connection' => false, 'show_errors' => false ) );
        $this->runnerBegin( $this->db );
        foreach ( array( 'schema.sql', 'cleandata.sql' ) as $sql )
            $this->assertNotFalse( $this->db->insertFile( self::runnerRoot() . '/kernel/sql/sqlite', $sql, false ), "kernel/sql/sqlite/$sql loads" );
        $root = $this->runnerDir() . '/root';
        mkdir( $root . '/design/s/stylesheets', 0775, true );
        file_put_contents( $root . '/design/s/stylesheets/site.css', 'body{margin:0}' );
    }

    protected function tearDown(): void
    {
        if ( $this->db )
            $this->db->close();
        $this->runnerEnd();
    }

    private static function page( $lang, $title, $body, $alternates = '' )
    {
        return array( 'status' => 200, 'type' => 'text/html; charset=utf-8', 'cookies' => array(),
                      'body' => "<!DOCTYPE html><html lang=\"$lang\"><head><title>$title</title><link rel=\"stylesheet\" href=\"/design/s/stylesheets/site.css\">$alternates</head>\n"
                              . "<body><main>$body</main></body></html>" );
    }

    private function site()
    {
        $alternates = '<link rel="alternate" hreflang="en" href="http://site.test/"><link rel="alternate" hreflang="de" href="http://site.test/ger">';
        $this->site = array(
            '/' => self::page( 'en', 'Home', '<h1>Welcome</h1><a href="/about-us">About us</a> <a href="/ger">Deutsch</a> <a href="/classic/old">Classic</a>', $alternates ),
            '/about-us' => self::page( 'en', 'About us', '<p>Our team of gardeners</p><a href="/">Home</a> <a href="/ger/ueber-uns">Deutsch</a>' ),
            '/ger' => self::page( 'de', 'Startseite', '<h1>Willkommen</h1><a href="/ger/ueber-uns">Über uns</a> <a href="/">English</a>', $alternates ),
            '/ger/ueber-uns' => self::page( 'de', 'Über uns', '<p>Unser Team der Gärtner</p><a href="/ger">Start</a> <a href="/about-us">English</a>' ),
            '/classic/old' => self::page( 'en', 'Classic', '<p>Another site</p>' ),
        );
    }

    /** Public for the closure. */
    public function answer( $url )
    {
        $path = (string)parse_url( $url, PHP_URL_PATH );
        $this->requested[] = $path;
        if ( parse_url( $url, PHP_URL_QUERY ) === null && isset( $this->site[$path] ) )
            return $this->site[$path];
        return array( 'status' => 404 ) + self::page( 'en', 'Not found', '<h1>Not found</h1><a href="/">Go home</a>' );
    }

    private function build( array $settings = array(), array $options = array() )
    {
        $test = $this;
        $target = new expStaticTarget( 'two-sites', $settings + array(
            'SiteAccessList' => array( 'eng', 'ger' ), 'DefaultSiteAccess' => 'eng', 'BasePath' => '/repo/', 'PublicURL' => 'https://owner.github.io/repo/',
            'CrawlURL' => 'http://site.test', 'MaxDepth' => '5', 'OutsideLinks' => 'unlink', 'Search' => 'enabled', 'Assets' => 'referenced',
        ) );
        $build = new expStaticBuild( $target, $options + array(
            'root' => $this->runnerDir() . '/root', 'var_dir' => 'var/test', 'work_dir' => $this->runnerDir() . '/out', 'db' => $this->db,
            'fetcher' => function ( $url, $headOnly = false ) use ( $test ) { return $test->answer( $url ); },
            'base_paths' => array( 'eng' => '', 'ger' => '/ger' ), 'context' => self::CONTEXT, 'run_id' => '20261102-120000-abcdef',
            'max_site_bytes' => 1000000, 'max_file_bytes' => 100000, 'audit' => false,
            'node_lookup' => function ( $relative, $siteAccess ) { return null; },
        ) );
        return array( $build->run(), $build->targetDirectory() );
    }

    /** SM-01 */
    public function testBothSiteAccessesInOneCopy()
    {
        $this->site();
        list( $report, $dir ) = $this->build();
        $this->assertSame( 0, $report['exit'], json_encode( $report['errors'] ) );
        $this->assertSame( array( 'eng', 'ger' ), $report['siteaccesses'] );
        $this->assertSame( 4, $report['counts']['pages'], json_encode( $report['pages'] ) );
        foreach ( array( 'index.html', 'about-us/index.html', 'ger/index.html', 'ger/ueber-uns/index.html', '404.html', 'sitemap.xml', 'robots.txt',
                         'search.html', 'static-search/index-en.json', 'static-search/index-de.json' ) as $file )
            $this->assertFileExists( "$dir/$file" );
        $this->assertFileDoesNotExist( "$dir/classic/old/index.html", 'a siteaccess the target does not publish is not built' );

        $home = file_get_contents( "$dir/index.html" );
        $this->assertStringContainsString( '<a href="about-us/">About us</a> <a href="ger/">Deutsch</a>', $home );
        $this->assertStringContainsString( 'class="exp-static-unavailable" aria-disabled="true">Classic</a>', $home, 'outside: OutsideLinks=unlink' );
        $this->assertStringContainsString( '<link rel="alternate" hreflang="de" href="https://owner.github.io/repo/ger/">', $home );
        $this->assertStringContainsString( '<link rel="alternate" hreflang="en" href="https://owner.github.io/repo/">', $home );
        $about = file_get_contents( "$dir/about-us/index.html" );
        $this->assertStringContainsString( '<a href="../ger/ueber-uns/">Deutsch</a>', $about );
        $ger = file_get_contents( "$dir/ger/index.html" );
        $this->assertStringContainsString( '<a href="ueber-uns/">Über uns</a> <a href="../">English</a>', $ger );
        $this->assertStringContainsString( 'href="../design/s/stylesheets/site.css"', $ger );
        $ueber = file_get_contents( "$dir/ger/ueber-uns/index.html" );
        $this->assertStringContainsString( '<a href="../">Start</a> <a href="../../about-us/">English</a>', $ueber );

        $sitemap = file_get_contents( "$dir/sitemap.xml" );
        foreach ( array( 'https://owner.github.io/repo/</loc>', 'https://owner.github.io/repo/about-us/</loc>', 'https://owner.github.io/repo/ger/</loc>',
                         'https://owner.github.io/repo/ger/ueber-uns/</loc>' ) as $loc )
            $this->assertStringContainsString( $loc, $sitemap );
        $this->assertStringContainsString( 'Not found', file_get_contents( "$dir/404.html" ), 'the default siteaccess\'s 404 page' );

        $de = json_decode( file_get_contents( "$dir/static-search/index-de.json" ), true );
        $this->assertSame( array( 'ger', 'ger' ), array_column( $de['pages'], 's' ) );
        $this->assertContains( 'ger/ueber-uns/', array_column( $de['pages'], 'u' ) );
        $search = file_get_contents( "$dir/search.html" );
        $this->assertStringContainsString( '<select id="exp-static-site" name="site">', $search );
        $this->assertStringContainsString( '<option value="ger">ger (/ger/)</option>', $search );

        $manifest = expStaticManifest::load( $dir );
        $this->assertSame( array( array( 'name' => 'eng', 'folder' => '/' ), array( 'name' => 'ger', 'folder' => '/ger/' ) ), $manifest->meta()['siteaccesses'] );
        $this->assertSame( 'ger', $manifest->get( 'ger/ueber-uns/index.html' )['siteaccess'] );

        $this->assertSame( 0, $report['link_check']['broken_count'], json_encode( $report['link_check']['broken'] ) );
        $atRoot = expStaticLinkChecker::check( $dir, '/', array( 'skip' => array( '404.html' ) ) );
        $this->assertSame( 0, $atRoot['broken_count'], json_encode( $atRoot['broken'] ) );
    }

    /** SM-02 */
    public function testWithoutADefaultTheRootLinksToEach()
    {
        $this->site();
        $this->site['/eng'] = $this->site['/'];
        list( $report, $dir ) = $this->build( array( 'DefaultSiteAccess' => '' ), array( 'base_paths' => array( 'eng' => '/eng', 'ger' => '/ger' ) ) );
        $this->assertSame( 0, $report['exit'], json_encode( $report['errors'] ) );
        $index = file_get_contents( "$dir/index.html" );
        $this->assertStringContainsString( '<a href="eng/"', $index );
        $this->assertStringContainsString( '<a href="ger/"', $index );
        $this->assertSame( 'extra', expStaticManifest::load( $dir )->get( 'index.html' )['kind'] );
        $this->assertFileExists( "$dir/ger/ueber-uns/index.html" );
    }

    /** SM-03 */
    public function testOneSiteAccessOnly()
    {
        $this->site();
        list( $first, $dir ) = $this->build();
        $this->assertSame( 0, $first['exit'], json_encode( $first['errors'] ) );
        $this->site['/ger/ueber-uns'] = self::page( 'de', 'Über uns', '<p>Neu</p><a href="/about-us">English</a>' );
        $this->site['/about-us'] = self::page( 'en', 'About us', '<p>Changed but not built</p>' );
        $this->requested = array();
        list( $only, $dir ) = $this->build( array(), array( 'siteaccess' => 'ger', 'run_id' => '20261102-130000-abcdef' ) );
        $this->assertSame( 0, $only['exit'], json_encode( $only['errors'] ) );
        $this->assertSame( 'partial', $only['scope'] );
        $this->assertNotContains( '/about-us', $this->requested, 'the English siteaccess is not crawled' );
        $this->assertStringContainsString( 'Neu', file_get_contents( "$dir/ger/ueber-uns/index.html" ) );
        $this->assertStringContainsString( '<a href="../../about-us/">English</a>', file_get_contents( "$dir/ger/ueber-uns/index.html" ), 'the English page of the last build is in the copy' );
        $this->assertStringNotContainsString( 'Changed but not built', file_get_contents( "$dir/about-us/index.html" ) );
        $this->assertSame( 4, expStaticManifest::load( $dir )->count( 'page' ) );

        list( $wrong, ) = $this->build( array(), array( 'siteaccess' => 'classic', 'run_id' => '20261102-140000-abcdef' ) );
        $this->assertSame( 1, $wrong['exit'] );

        list( $paths, $dir ) = $this->build( array(), array( 'paths' => array( '/ger/ueber-uns' ), 'run_id' => '20261102-150000-abcdef' ) );
        $this->assertSame( 0, $paths['exit'], json_encode( $paths['errors'] ) );
        $this->assertSame( array( 'ger/ueber-uns/index.html' ), array_column( $paths['pages'], 'file' ) );
    }
}
