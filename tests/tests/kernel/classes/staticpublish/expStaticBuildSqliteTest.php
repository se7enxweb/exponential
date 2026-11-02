<?php
/**
 * A static build end to end (expStaticBuild) on SQLite in process: a SQLite file under var/tmp with the kernel schema
 * and the clean data (the anonymous user, the content tree, the roles), a small site answered by a fake request
 * function instead of a web server, and an installation folder with design files and storage images. No server,
 * no network, no installation's database.
 *
 *  SB-01 a full build: the pages, debug output and tokens taken out, links rewritten, the files named by the pages
 *        (local, through the cluster file handler, and requested), style sheets rewritten, the storage images of
 *        content the anonymous user may read (and not the others), the extras and the manifest with node data
 *  SB-02 the link check over the build finds no broken link or file at the root and below /repo/
 *  SB-03 a second build: the diff, and a file the manifest no longer lists is removed; a file it never listed stays
 *  SB-04 a page that set a session cookie is refused, the build fails and the folder is not changed
 *  SB-05 a dry run reports and changes nothing; a partial build (--paths) keeps the rest of the build
 *  SB-06 base-path links
 *  SB-07 built from a CrawlURL, the site's links to its own SiteURL in full are followed and rewritten
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/migration/expMigrationRunnerEngineChecks.php';

class expStaticBuildSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationRunnerEngineChecks;

    /** @var eZSQLite3DB|null */
    private $db;


    /** host path => response, the site the fake request function serves */
    private $site = array();

    /** the requests made, by host path */
    private $requested = array();

    const CONTEXT = array( 'siteaccesses' => array( 'site', 'admin' ), 'admin' => array( 'admin' ), 'urls' => array( 'site' => 'www.example.com', 'admin' => 'admin.example.com' ) );

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
        // the installation's files are in a folder of the test, not the current directory, so they are read as files
        // (in an installation, var/ is read through the cluster file handler)
        $this->installation();
    }

    protected function tearDown(): void
    {
        if ( $this->db )
            $this->db->close();
        $this->runnerEnd();
    }

    /** The installation's files and the storage images of two objects: the root folder (Standard) and Users (Users). */
    private function installation()
    {
        $root = $this->runnerDir() . '/root';
        $files = array(
            'design/s/stylesheets/site.css' => "@import 'print.css';\nbody{background:url(../images/bg.png)}\n.logo{background:url(/var/test/storage/images/logo.png)}",
            'design/s/stylesheets/print.css' => '@media print{body{color:#000}}',
            'design/s/images/bg.png' => 'PNG-bg',
            'design/s/templates/secret.tpl' => '{* never copied *}',
            'var/test/storage/images/logo.png' => 'PNG-logo',
            'var/test/storage/images/websites/1-6-eng-GB/websites.png' => 'PNG-websites',
            'var/test/storage/images/websites/1-6-eng-GB/websites_small.png' => 'PNG-websites-small',
            'var/test/storage/images/websites/1-6-eng-GB/websites_large.png' => 'PNG-websites-large',
            'var/test/storage/images/users/4-1-eng-GB/users.png' => 'PNG-users',
        );
        foreach ( $files as $path => $content )
        {
            if ( !is_dir( dirname( $root . '/' . $path ) ) )
                mkdir( dirname( $root . '/' . $path ), 0775, true );
            file_put_contents( $root . '/' . $path, $content );
        }
        foreach ( array( 1 => array( 'var/test/storage/images/websites/1-6-eng-GB/websites.png', 'var/test/storage/images/websites/1-6-eng-GB/websites_small.png',
                                     'var/test/storage/images/websites/1-6-eng-GB/websites_large.png' ),
                         4 => array( 'var/test/storage/images/users/4-1-eng-GB/users.png' ) ) as $objectId => $paths )
        {
            $attribute = $this->db->arrayQuery( "SELECT a.id AS id FROM ezcontentobject_attribute a, ezcontentobject o WHERE a.contentobject_id = o.id AND a.version = o.current_version AND o.id = $objectId" );
            $this->assertNotEmpty( $attribute, "object $objectId has attributes" );
            foreach ( $paths as $path )
                $this->db->query( 'INSERT INTO ezimagefile ( contentobject_attribute_id, filepath ) VALUES ( ' . (int)$attribute[0]['id'] . ", '" . $this->db->escapeString( $path ) . "' )" );
        }
    }

    private static function page( $title, $body, array $extra = array() )
    {
        return $extra + array( 'status' => 200, 'type' => 'text/html; charset=utf-8', 'cookies' => array(),
                               'body' => "<!DOCTYPE html><html><head>\n<meta name=\"csrf-param\" content=\"ezxform_token\" />\n<meta name=\"csrf-token\" id=\"ezxform_token_js\" title=\"t0k\" content=\"t0k\" />\n"
                                       . "<title>$title</title><link rel=\"stylesheet\" href=\"/design/s/stylesheets/site.css\">"
                                       . "<script src=\"/var/test/cache/public/javascript/p_1.js\" nonce=\"n0nce\"></script></head>\n<body>"
                                       . "<nav><a href=\"/\">Home</a> <a href=\"/about-us\">About</a> <a href=\"/news\">News</a> <a href=\"/content/search\">Search</a>"
                                       . " <a href=\"/user/login\">Log in</a></nav>\n<main>$body</main>"
                                       . "<form method=\"post\" action=\"/content/action\"><input type=\"hidden\" name=\"ezxform_token\" value=\"t0k\" /></form>\n"
                                       . "<div id=\"debug\"><h2><a href=\"#debug-end\">Exp Debug</a></h2><div id=\"debug-details\"><a href=\"/visual/templateview/x\">t</a></div></div>"
                                       . "<a id=\"debug-end\"></a><script>document.body.classList.add('exp-debug-bar');</script></body></html>" );
    }

    private function site()
    {
        $this->site = array(
            '/' => self::page( 'Home', '<h1>Home</h1><img src="/var/test/storage/images/websites/1-6-eng-GB/websites.png" alt="">'
                                      . '<a href="/news?page=2">Older</a> <a href="https://github.com/se7enxweb">GitHub</a> <a href="#top">Top</a>'
                                      . ' <a href="/content/download/1/2/file/report.pdf">Report</a> <img src="/missing.png" alt="">' ),
            '/about-us' => self::page( 'About', '<meta property="og:image" content="/var/test/storage/images/logo.png"><link rel="canonical" href="/about-us">'
                                               . '<img srcset="/var/test/storage/images/websites/1-6-eng-GB/websites_small.png 1x, /var/test/storage/images/websites/1-6-eng-GB/websites.png 2x">' ),
            '/news' => self::page( 'News', '<a href="news/item-1">Item 1</a> <a href="/index.php/news/item-1#more">More</a>' ),
            '/news/item-1' => self::page( 'Item 1', '<div style="background:url(/design/s/images/bg.png)"></div><a href="../about-us">About</a>' ),
            '/var/test/cache/public/javascript/p_1.js' => array( 'status' => 200, 'type' => 'text/javascript', 'body' => 'var packed = 1;' ),
            '/content/download/1/2/file/report.pdf' => array( 'status' => 200, 'type' => 'application/pdf', 'body' => '%PDF-1.4 report' ),
            '/missing.png' => array( 'status' => 404, 'type' => 'text/html', 'body' => 'no' ),
        );
    }

    /** @return callable the fake request function: the site above, 404 with the site's 404 page for everything else */
    private function fetcher()
    {
        $test = $this;
        return function ( $url, $headOnly = false ) use ( $test )
        {
            return $test->answer( $url );
        };
    }

    /** Public for the closure. */
    public function answer( $url )
    {
        $path = (string)parse_url( $url, PHP_URL_PATH );
        $this->requested[] = $path;
        if ( parse_url( $url, PHP_URL_QUERY ) === null && isset( $this->site[$path] ) )
            return $this->site[$path] + array( 'cookies' => array() );
        $notFound = self::page( 'Not found', '<h1>Not found</h1><a href="/">Go home</a>' );
        return array( 'status' => 404 ) + $notFound;
    }

    private function build( array $settings = array(), array $options = array() )
    {
        $target = new expStaticTarget( 'test-site', $settings + array( 'SiteAccess' => 'site', 'BasePath' => '/repo/', 'PublicURL' => 'https://owner.github.io/repo/',
                                                                      'CrawlURL' => 'http://site.test', 'MaxDepth' => '5' ) );
        $build = new expStaticBuild( $target, $options + array(
            'root' => $this->runnerDir() . '/root', 'var_dir' => 'var/test', 'work_dir' => $this->runnerDir() . '/out', 'db' => $this->db,
            'fetcher' => $this->fetcher(), 'base_path' => '', 'context' => self::CONTEXT, 'run_id' => '20261101-120000-abcdef',
            'max_site_bytes' => 1000000, 'max_file_bytes' => 100000,
        ) );
        return array( $build->run(), $build->targetDirectory() );
    }

    /** SB-01 SB-02 */
    public function testFullBuild()
    {
        $this->site();
        list( $report, $dir ) = $this->build();
        $this->assertSame( 0, $report['exit'], json_encode( $report['errors'] ) );
        $this->assertTrue( $report['applied'] );
        $this->assertSame( 4, $report['counts']['pages'], json_encode( $report['pages'] ) );
        foreach ( array( 'index.html', 'about-us/index.html', 'news/index.html', 'news/item-1/index.html', '404.html', '.nojekyll', 'robots.txt', 'sitemap.xml',
                         'design/s/stylesheets/site.css', 'design/s/stylesheets/print.css', 'design/s/images/bg.png', 'var/test/storage/images/logo.png',
                         'var/test/storage/images/websites/1-6-eng-GB/websites.png', 'var/test/storage/images/websites/1-6-eng-GB/websites_small.png',
                         'var/test/cache/public/javascript/p_1.js', 'content/download/1/2/file/report.pdf', '.static-publish/manifest.json' ) as $file )
            $this->assertFileExists( "$dir/$file" );
        $this->assertFileDoesNotExist( "$dir/var/test/storage/images/users/4-1-eng-GB/users.png", 'the anonymous user may not read Users' );
        $this->assertFileDoesNotExist( "$dir/missing.png" );
        $this->assertFileDoesNotExist( "$dir/design/s/templates/secret.tpl" );
        $this->assertFileDoesNotExist( "$dir/CNAME" );
        $this->assertFileExists( "$dir/var/test/storage/images/websites/1-6-eng-GB/websites_large.png", 'a storage image no page names' );
        $this->assertSame( 1, $report['counts']['storage'] );
        $this->assertSame( '/missing.png', $report['files_failed'][0]['url'] );

        $home = file_get_contents( "$dir/index.html" );
        foreach ( array( 'id="debug"', 'debug-end', 'ezxform_token', 'csrf-token', 't0k', 'nonce=', '/visual/' ) as $gone )
            $this->assertStringNotContainsString( $gone, $home, "$gone is taken out" );
        $this->assertStringContainsString( '<link rel="stylesheet" href="design/s/stylesheets/site.css">', $home );
        $this->assertStringContainsString( '<script src="var/test/cache/public/javascript/p_1.js"></script>', $home );
        $this->assertStringContainsString( '<a href="./">Home</a> <a href="about-us/">About</a> <a href="news/">News</a>', $home );
        $this->assertStringContainsString( '<a href="https://www.example.com/content/search">Search</a>', $home );
        $this->assertStringContainsString( '<a href="https://www.example.com/user/login">Log in</a>', $home );
        $this->assertStringContainsString( '<a href="https://www.example.com/news?page=2">Older</a>', $home );
        $this->assertStringContainsString( '<a href="https://github.com/se7enxweb">GitHub</a> <a href="#top">Top</a>', $home );
        $this->assertStringContainsString( '<a href="content/download/1/2/file/report.pdf">Report</a>', $home );
        $this->assertStringContainsString( '<img src="https://www.example.com/missing.png" alt="">', $home, 'a file the site does not hand out goes to the live site' );
        $this->assertStringContainsString( 'action="https://www.example.com/content/action"', $home );
        $item = file_get_contents( "$dir/news/item-1/index.html" );
        $this->assertStringContainsString( '<a href="../../">Home</a>', $item );
        $this->assertStringContainsString( 'url(../../design/s/images/bg.png)', $item );
        $this->assertStringContainsString( '<a href="../../about-us/">About</a>', $item );
        $about = file_get_contents( "$dir/about-us/index.html" );
        $this->assertStringContainsString( '<meta property="og:image" content="https://owner.github.io/repo/var/test/storage/images/logo.png">', $about );
        $this->assertStringContainsString( '<link rel="canonical" href="https://owner.github.io/repo/about-us/">', $about );
        $this->assertStringContainsString( 'srcset="../var/test/storage/images/websites/1-6-eng-GB/websites_small.png 1x, ../var/test/storage/images/websites/1-6-eng-GB/websites.png 2x"', $about );
        $news = file_get_contents( "$dir/news/index.html" );
        $this->assertStringContainsString( '<a href="item-1/">Item 1</a> <a href="item-1/#more">More</a>', $news );
        $css = file_get_contents( "$dir/design/s/stylesheets/site.css" );
        $this->assertStringContainsString( "@import 'print.css';", $css );
        $this->assertStringContainsString( 'url(../images/bg.png)', $css );
        $this->assertStringContainsString( 'url(../../../var/test/storage/images/logo.png)', $css );
        $notFound = file_get_contents( "$dir/404.html" );
        $this->assertStringContainsString( '<a href="/repo/">Go home</a>', $notFound, '404.html links below the base path' );
        $this->assertStringContainsString( 'href="/repo/design/s/stylesheets/site.css"', $notFound );
        $this->assertStringNotContainsString( 'ezxform_token', $notFound );
        $this->assertSame( '', file_get_contents( "$dir/.nojekyll" ) );
        $this->assertStringContainsString( 'Sitemap: https://owner.github.io/repo/sitemap.xml', file_get_contents( "$dir/robots.txt" ) );
        $sitemap = file_get_contents( "$dir/sitemap.xml" );
        foreach ( array( 'https://owner.github.io/repo/</loc>', 'https://owner.github.io/repo/about-us/</loc>', 'https://owner.github.io/repo/news/item-1/</loc>' ) as $loc )
            $this->assertStringContainsString( $loc, $sitemap );

        $manifest = expStaticManifest::load( $dir );
        $this->assertNotNull( $manifest );
        $this->assertSame( 2, $manifest->get( 'index.html' )['node'], 'the front page is node 2' );
        $this->assertNotNull( $manifest->get( 'index.html' )['modified'] );
        $this->assertSame( 'storage', $manifest->get( 'var/test/storage/images/websites/1-6-eng-GB/websites_large.png' )['kind'] );
        $this->assertSame( 'file', $manifest->get( 'var/test/storage/images/websites/1-6-eng-GB/websites_small.png' )['kind'], 'named by a page' );
        $this->assertSame( hash_file( 'sha256', "$dir/news/index.html" ), $manifest->get( 'news/index.html' )['sha256'] );
        $this->assertSame( 'extra', $manifest->get( '404.html' )['kind'] );
        $this->assertGreaterThan( 0, $report['counts']['stripped']['debug'] );
        $this->assertGreaterThan( 0, $report['counts']['stripped']['tokens'] );
        $this->assertNotContains( '/content/search', $this->requested, 'module views are not crawled' );
        $this->assertNotContains( '/design/s/stylesheets/site.css', $this->requested, 'files of the installation are copied, not requested' );

        // SB-02: the link check at the root and below the repository
        $this->assertSame( 0, $report['link_check']['broken_count'], json_encode( $report['link_check']['broken'] ) );
        $this->assertSame( '/repo/', $report['link_check']['mount'] );
        $atRoot = expStaticLinkChecker::check( $dir, '/', array( 'skip' => array( '404.html' ) ) );
        $this->assertSame( 0, $atRoot['broken_count'], json_encode( $atRoot['broken'] ) );
        $this->assertGreaterThanOrEqual( 25, $atRoot['links'] );
    }

    /** SB-03 */
    public function testSecondBuildDiffAndRemoval()
    {
        $this->site();
        list( $first, $dir ) = $this->build();
        $this->assertSame( 0, $first['exit'] );
        file_put_contents( "$dir/README.md", 'mine' );
        mkdir( "$dir/.git" );
        file_put_contents( "$dir/.git/HEAD", 'ref' );
        // the news item is gone and the about page changed
        unset( $this->site['/news/item-1'] );
        $this->site['/news'] = self::page( 'News', '<p>No news</p>' );
        $this->site['/about-us'] = self::page( 'About', '<p>Changed</p>' );
        list( $second, $dir ) = $this->build( array(), array( 'run_id' => '20261101-130000-abcdef' ) );
        $this->assertSame( 0, $second['exit'], json_encode( $second['errors'] ) );
        $this->assertGreaterThanOrEqual( 2, $second['diff']['changed'] );
        $this->assertGreaterThanOrEqual( 1, $second['diff']['removed'] );
        $this->assertContains( 'news/item-1/index.html', $second['diff']['files']['removed'] );
        $this->assertFileDoesNotExist( "$dir/news/item-1/index.html" );
        $this->assertDirectoryDoesNotExist( "$dir/news/item-1", 'the empty folder goes too' );
        $this->assertFileExists( "$dir/README.md", 'a file no manifest listed stays' );
        $this->assertFileExists( "$dir/.git/HEAD" );
        $this->assertFileExists( "$dir/.static-publish/manifest.previous.json" );
        $this->assertSame( 0, $second['link_check']['broken_count'] );
    }

    /** SB-04 */
    public function testSessionCookieRefuses()
    {
        $this->site();
        list( $first, $dir ) = $this->build();
        $before = file_get_contents( "$dir/.static-publish/manifest.json" );
        $this->site['/news'] = self::page( 'News', '<p>Per visitor</p>', array( 'cookies' => array( 'eZSESSID' ) ) );
        list( $report, $dir ) = $this->build( array(), array( 'run_id' => '20261101-140000-abcdef' ) );
        $this->assertSame( 1, $report['exit'] );
        $this->assertFalse( $report['applied'] );
        $this->assertSame( '/news', $report['refused'][0]['path'] );
        $this->assertSame( array( 'the response set the session cookie eZSESSID' ), $report['refused'][0]['reasons'] );
        $this->assertSame( $before, file_get_contents( "$dir/.static-publish/manifest.json" ), 'the folder is not changed' );
        $this->assertDirectoryDoesNotExist( dirname( $dir ) . '/.staging/test-site-20261101-140000-abcdef', 'the staging folder is removed' );
    }

    /** SB-05 */
    public function testDryRunAndPartial()
    {
        $this->site();
        list( $dry, $dir ) = $this->build( array(), array( 'dry_run' => true ) );
        $this->assertSame( 0, $dry['exit'] );
        $this->assertFalse( $dry['applied'] );
        $this->assertDirectoryDoesNotExist( $dir );
        $this->assertSame( 0, $dry['link_check']['broken_count'] );
        $this->assertGreaterThan( 10, $dry['diff']['added'] );

        $this->build();
        $this->site['/about-us'] = self::page( 'About', '<p>Only this changed</p><a href="/news/item-1">Item</a>' );
        $this->requested = array();
        list( $partial, $dir ) = $this->build( array(), array( 'paths' => array( '/about-us' ), 'run_id' => '20261101-150000-abcdef' ) );
        $this->assertSame( 0, $partial['exit'], json_encode( $partial['errors'] ) );
        $this->assertSame( 'partial', $partial['scope'] );
        $this->assertNotContains( '/news', $this->requested, 'only the page asked for is fetched' );
        $this->assertSame( 0, $partial['diff']['removed'] );
        $this->assertFileExists( "$dir/news/item-1/index.html", 'the rest of the build stays' );
        $about = file_get_contents( "$dir/about-us/index.html" );
        $this->assertStringContainsString( 'Only this changed', $about );
        $this->assertStringContainsString( '<a href="../news/item-1/">Item</a>', $about, 'a page of the last build is linked inside the export' );
        $this->assertSame( 4, expStaticManifest::load( $dir )->count( 'page' ) );
    }

    /** SB-06 */
    public function testBasePathLinks()
    {
        $this->site();
        list( $report, $dir ) = $this->build( array( 'Links' => 'base-path', 'Assets' => 'referenced' ) );
        $this->assertSame( 0, $report['exit'] );
        $this->assertSame( 0, $report['counts']['storage'] );
        $item = file_get_contents( "$dir/news/item-1/index.html" );
        $this->assertStringContainsString( '<a href="/repo/">Home</a>', $item );
        $this->assertStringContainsString( 'href="/repo/design/s/stylesheets/site.css"', $item );
        $this->assertSame( 0, $report['link_check']['broken_count'], json_encode( $report['link_check']['broken'] ) );
        $this->assertGreaterThan( 0, expStaticLinkChecker::check( $dir, '/' )['broken_count'], 'base-path links work below the base path only' );
    }

    /** SB-07 */
    public function testLinksToTheSiteUrlInFullAreFollowedFromTheCrawlUrl()
    {
        $this->site();
        // the pages are fetched from http://site.test (the CrawlURL), but link to www.example.com (the SiteURL) in full
        $this->site['/'] = self::page( 'Home', '<h1>Home</h1><a href="https://www.example.com/about-us">About</a> <a href="//www.example.com/news">News</a>'
                                              . ' <a href="https://other.example.org/away">Away</a>' );
        $this->site['/news'] = self::page( 'News', '<a href="https://WWW.example.com/news/item-1?x=1#more">Item</a> <a href="http://www.example.com/news/item-1">Again</a>' );
        list( $report, $dir ) = $this->build( array( 'Assets' => 'referenced' ) );
        $this->assertSame( 0, $report['exit'], json_encode( $report['errors'] ) );
        $this->assertSame( 4, $report['counts']['pages'], json_encode( $report['pages'] ) );
        foreach ( array( 'about-us/index.html', 'news/index.html', 'news/item-1/index.html' ) as $file )
            $this->assertFileExists( "$dir/$file" );
        $this->assertNotContains( '/away', $this->requested, 'another host is not crawled' );
        $home = file_get_contents( "$dir/index.html" );
        $this->assertStringContainsString( '<a href="about-us/">About</a>', $home );
        $this->assertStringContainsString( '<a href="news/">News</a>', $home );
        $this->assertStringContainsString( 'https://other.example.org/away', $home );
        // a page with a query is not exported: it goes to the live site, as before
        $this->assertStringContainsString( '<a href="item-1/">Again</a>', file_get_contents( "$dir/news/index.html" ) );
        $this->assertSame( 0, $report['link_check']['broken_count'], json_encode( $report['link_check']['broken'] ) );
    }
}
