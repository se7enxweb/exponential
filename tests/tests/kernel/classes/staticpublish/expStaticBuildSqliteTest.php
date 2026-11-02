<?php
/**
 * A static build end to end (expStaticBuild) on SQLite in process: a SQLite file under var/tmp with the kernel schema
 * and the clean data (the anonymous user, the content tree, the roles), a small site answered by a fake request
 * function instead of a web server, and an installation folder with design files and storage images. No server,
 * no network, no installation's database.
 *
 *  SB-01 a full build, its requests signed (no debug output from the fake server): the pages, tokens taken out, links
 *        rewritten, the files named by the pages
 *        (local, through the cluster file handler, and requested), style sheets rewritten, the storage images of
 *        content the anonymous user may read (and not the others), the extras and the manifest with node data
 *  SB-02 the link check over the build finds no broken link or file at the root and below /repo/
 *  SB-03 a second build: the diff, and a file the manifest no longer lists is removed; a file it never listed stays
 *  SB-04 a page that set a session cookie is refused, the build fails and the folder is not changed
 *  SB-05 a dry run reports and changes nothing; a partial build (--paths) keeps the rest of the build
 *  SB-06 base-path links
 *  SB-07 built from a CrawlURL, the site's links to its own SiteURL in full are followed and rewritten
 *  SB-08 the extras: the degradation report (search page, form endpoint with a signed token, login removed), the search
 *        index and page, redirect stubs from URL aliases, README and LICENSE, minify, fingerprint and precompress; the link
 *        check finds nothing broken; the build is an audit event
 *  SB-09 a SiteURL that only answers on the build computer (localhost, 127.0.0.1) is not the live site: links to
 *        what the copy does not hold stay as they are, with a warning; never https://127.0.0.1
 *  SB-10 a page that comes with debug output (the server did not take the build's signed X-Exp-Static-Build header,
 *        or sent a debug header) is refused, not stripped: the build fails and the folder is not changed
 *  SB-11 an address that answers with a redirect becomes a stub page that sends on to the copy's page (a chain to where
 *        it ends), to the live site or another host (never fetched), or with OutsideLinks=unlink to the front page;
 *        never a copy of the page it sends to; the page sent to is fetched once, under its own address; loops are
 *        skipped; redirects_followed counts the stubs
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
        expStaticBuildSignature::setForTest( self::SIGNATURE_KEY );
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
        expStaticBuildSignature::setForTest();
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
                                       . self::DEBUG_REPORT . "</body></html>" );
    }

    /** The debug report the fake server adds to a page for a request without a valid static build signature */
    const DEBUG_REPORT = "<div id=\"debug\"><h2><a href=\"#debug-end\">Exp Debug</a></h2><div id=\"debug-details\"><a href=\"/visual/templateview/x\">t</a></div></div>"
                       . "<a id=\"debug-end\"></a><script>document.body.classList.add('exp-debug-bar');</script>";

    /** The key the fake server and the build sign with */
    const SIGNATURE_KEY = 'static-build-test-key';

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
        return function ( $url, $headOnly = false, array $headers = array() ) use ( $test )
        {
            return $test->answer( $url, $headers );
        };
    }

    /**
     * Public for the closure. The fake server is one whose own address is in DebugIPList[]: every page comes with
     * the debug report, unless the request carries a valid X-Exp-Static-Build signature.
     */
    public function answer( $url, array $headers = array() )
    {
        $path = (string)parse_url( $url, PHP_URL_PATH );
        $this->requested[] = $path;
        if ( parse_url( $url, PHP_URL_QUERY ) === null && isset( $this->site[$path] ) )
            $response = $this->site[$path] + array( 'cookies' => array() );
        else
            $response = array( 'status' => 404 ) + self::page( 'Not found', '<h1>Not found</h1><a href="/">Go home</a>' );
        $signed = false;
        foreach ( $headers as $line )
            if ( preg_match( '/^' . preg_quote( expStaticBuildSignature::HEADER, '/' ) . ':\s*(\S+)$/i', $line, $m ) )
                $signed = expStaticBuildSignature::problem( $m[1], null, self::SIGNATURE_KEY ) === '';
        if ( $signed )
            $response['body'] = str_replace( self::DEBUG_REPORT, '', $response['body'] );
        return $response;
    }

    private function build( array $settings = array(), array $options = array() )
    {
        $target = new expStaticTarget( 'test-site', $settings + array( 'SiteAccess' => 'site', 'BasePath' => '/repo/', 'PublicURL' => 'https://owner.github.io/repo/',
                                                                      'CrawlURL' => 'http://site.test', 'MaxDepth' => '5' ) );
        $build = new expStaticBuild( $target, $options + array(
            'root' => $this->runnerDir() . '/root', 'var_dir' => 'var/test', 'work_dir' => $this->runnerDir() . '/out', 'db' => $this->db,
            'fetcher' => $this->fetcher(), 'base_path' => '', 'context' => self::CONTEXT, 'run_id' => '20261101-120000-abcdef',
            'max_site_bytes' => 1000000, 'max_file_bytes' => 100000, 'audit' => false, 'sign_requests' => true,
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
        $this->assertArrayNotHasKey( 'debug', $report['counts']['stripped'], 'the signed requests came without debug output: nothing to strip' );
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

    /** SB-10 */
    public function testDebugOutputFailsTheBuild()
    {
        $this->site();
        list( $first, $dir ) = $this->build();
        $this->assertSame( 0, $first['exit'], json_encode( $first['errors'] ) );
        $before = file_get_contents( "$dir/.static-publish/manifest.json" );
        // a server that does not take the signature (unsigned requests here): the debug report is not stripped
        list( $report, $dir ) = $this->build( array(), array( 'run_id' => '20261101-160000-abcdef', 'sign_requests' => false ) );
        $this->assertSame( 1, $report['exit'] );
        $this->assertFalse( $report['applied'] );
        $this->assertStringContainsString( 'came with debug output', $report['errors'][0] );
        $this->assertStringContainsString( 'X-Exp-Static-Build', $report['errors'][0] );
        $this->assertStringStartsWith( 'debug output from the server (<div id="debug"', $report['refused'][0]['reasons'][0] );
        $this->assertSame( $before, file_get_contents( "$dir/.static-publish/manifest.json" ), 'the folder is not changed' );

        // a debug header alone is debug output too
        $this->site['/news'] += array( 'headers' => array( 'content-type', 'x-debug-token' ) );
        list( $report, $dir ) = $this->build( array(), array( 'run_id' => '20261101-170000-abcdef' ) );
        $this->assertSame( 1, $report['exit'] );
        $this->assertSame( '/news', $report['refused'][0]['path'] );
        $this->assertSame( array( 'debug output from the server (the header x-debug-token)' ), $report['refused'][0]['reasons'] );
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

    /** SB-08 */
    public function testExtras()
    {
        $this->site();
        $this->site['/'] = self::page( 'Home', '<h1>Home</h1><p>Welcome to the bicycle site.</p>'
                                              . '<form action="/content/search" method="get"><input name="SearchText"><button>Search</button></form>' );
        $this->site['/about-us'] = self::page( 'About', '<h1>About</h1><p>Write to us about bicycles.</p>'
                                                      . '<form method="post" action="/content/action"><input type="hidden" name="ContentObjectID" value="1" />'
                                                      . '<input type="hidden" name="ContentNodeID" value="2" /><textarea name="ContentObjectAttribute_data_text_9"></textarea>'
                                                      . '<input type="submit" name="ActionCollectInformation" value="Send" /></form>'
                                                      . '<form method="post" action="/user/login"><input name="Login"><input type="password" name="Password"></form>' );
        expStaticFormToken::setKeyForTest( 'k' );
        $events = array();
        try
        {
            list( $report, $dir ) = $this->build(
                array( 'Search' => 'enabled', 'Forms' => 'endpoint', 'LiveURL' => 'https://live.example.com', 'License' => 'CC-BY-4.0',
                       'Extras' => array( 'nojekyll', '404', 'robots', 'sitemap', 'manifest', 'redirects', 'readme', 'license' ),
                       'PostProcess' => array( 'strip', 'minify', 'fingerprint', 'precompress' ) ),
                array( 'form_endpoint' => true,
                       // node 2 (the front page) had another name once; a custom alias points at it too; a wildcard to /news
                       'redirect_rows' => array( 'aliases' => array(
                           array( 'id' => 90, 'parent' => 0, 'lang_mask' => 3, 'text' => 'old-home', 'action' => 'eznode:2', 'is_original' => 0, 'is_alias' => 0 ),
                           array( 'id' => 91, 'parent' => 90, 'lang_mask' => 3, 'text' => 'deeper', 'action' => 'eznode:2', 'is_original' => 0, 'is_alias' => 0 ),
                           array( 'id' => 92, 'parent' => 0, 'lang_mask' => 3, 'text' => 'start', 'action' => 'eznode:2', 'is_original' => 1, 'is_alias' => 1 ) ),
                                                'wildcards' => array( array( 'source_url' => 'go/news', 'destination_url' => 'news' ) ) ),
                       'audit' => function ( $name, $data ) use ( &$events ) { $events[] = array( $name, $data['result'], $data['x']['degraded'] ); } ) );
        }
        finally
        {
            expStaticFormToken::setKeyForTest( null );
        }
        $this->assertSame( 0, $report['exit'], json_encode( $report['errors'] ) );

        // the degradation report
        $this->assertSame( array( 'search-page' => 1 ), $report['degraded']['search']['handled'] );
        $this->assertSame( array( 'endpoint' => 1 ), $report['degraded']['collect']['handled'] );
        $this->assertSame( array( 'removed' => 1 ), $report['degraded']['login']['handled'] );
        $this->assertSame( array( '/about-us' ), $report['degraded']['collect']['examples'] );
        $this->assertSame( array( array( 'system.staticpublish.build', 'success', array_keys( $report['degraded'] ) ) ), $events );

        $home = file_get_contents( "$dir/index.html" );
        $this->assertStringContainsString( '<form action="search.html" method="get" data-exp-static="search">', $home );
        $about = file_get_contents( "$dir/about-us/index.html" );
        $this->assertStringContainsString( 'action="https://live.example.com/staticpublish/form"', $about );
        $this->assertMatchesRegularExpression( '#name="ExpStaticToken" value="(v1\.\d+\.1\.[A-Za-z0-9_-]{43})"#', $about );
        preg_match( '#name="ExpStaticToken" value="([^"]+)"#', $about, $m );
        $this->assertSame( '', expStaticFormToken::problem( $m[1], 'test-site', '1', 400, null, 'k' ) );
        $this->assertStringNotContainsString( 'action="https://live.example.com/user/login"', $about, 'the login form is taken out' );
        $this->assertStringNotContainsString( "\n\n", $about, 'minified' );

        // the search
        $this->assertFileExists( "$dir/search.html" );
        $this->assertFileExists( "$dir/static-search/search.js" );
        $index = json_decode( file_get_contents( "$dir/static-search/index-und.json" ), true );
        $this->assertSame( 'exp-static-search/1', $index['schema'] );
        $this->assertContains( 'about-us/', array_column( $index['pages'], 'u' ) );
        $this->assertSame( 4, $report['search']['pages'] );

        // the redirect stubs
        $this->assertSame( 4, $report['counts']['redirects'] );
        $this->assertStringContainsString( 'content="0; url=../"', file_get_contents( "$dir/old-home/index.html" ) );
        $this->assertStringContainsString( 'content="0; url=../../"', file_get_contents( "$dir/old-home/deeper/index.html" ) );
        $this->assertStringContainsString( '<link rel="canonical" href="https://owner.github.io/repo/">', file_get_contents( "$dir/start/index.html" ) );
        $this->assertStringContainsString( 'url=../../news/', file_get_contents( "$dir/go/news/index.html" ) );

        // README, LICENSE, fingerprint, precompress
        $this->assertStringContainsString( '- Website: https://owner.github.io/repo/', file_get_contents( "$dir/README.md" ) );
        $this->assertStringContainsString( 'CC-BY-4.0', file_get_contents( "$dir/LICENSE" ) );
        $this->assertFileDoesNotExist( "$dir/design/s/stylesheets/site.css" );
        $sheets = glob( "$dir/design/s/stylesheets/site.*.css" );
        $this->assertCount( 1, $sheets );
        $this->assertStringContainsString( 'href="design/s/stylesheets/' . basename( $sheets[0] ) . '"', $home );
        $this->assertCount( 1, glob( "$dir/var/test/cache/public/javascript/p_1.*.js" ) );
        $this->assertMatchesRegularExpression( "#@import 'print\\.[0-9a-f]{8}\\.css';#", file_get_contents( $sheets[0] ) );
        $this->assertGreaterThanOrEqual( 2, $report['counts']['fingerprinted'] );
        $this->assertGreaterThan( 0, $report['counts']['precompressed'] );
        $this->assertSame( file_get_contents( "$dir/about-us/index.html" ), gzdecode( file_get_contents( "$dir/about-us/index.html.gz" ) ) );
        $this->assertSame( 'extra', expStaticManifest::load( $dir )->get( 'about-us/index.html.gz' )['kind'] );

        $this->assertSame( 0, $report['link_check']['broken_count'], json_encode( $report['link_check']['broken'] ) );
    }

    /** SB-09 */
    public function testALocalSiteUrlIsNoLiveSite()
    {
        foreach ( array( 'localhost', '127.0.0.1', '127.1.2.3', '::1', '[::1]', 'site.localhost', '0.0.0.0' ) as $host )
            $this->assertTrue( expStaticBuild::isLocalHost( $host ), $host );
        foreach ( array( '', 'www.example.com', '10.0.0.1', '128.0.0.1', 'localhost.example.com', '127.0.0.1.example.com' ) as $host )
            $this->assertFalse( expStaticBuild::isLocalHost( $host ), $host );

        $this->site();
        $context = self::CONTEXT;
        $context['urls']['site'] = '127.0.0.1:8080/site';
        list( $report, $dir ) = $this->build( array( 'Assets' => 'referenced' ), array( 'context' => $context ) );
        $this->assertSame( 0, $report['exit'], json_encode( $report['errors'] ) );
        $this->assertStringContainsString( 'visitors cannot reach', implode( ' ', $report['warnings'] ) );
        $home = file_get_contents( "$dir/index.html" );
        $this->assertStringNotContainsString( 'https://127.0.0.1', $home );
        $this->assertStringContainsString( '<a href="/news?page=2">Older</a>', $home, 'not held, no live site: left as it is' );
    }

    /** The site with addresses that answer with a redirect, linked from the front page. */
    private function redirectingSite()
    {
        $this->site();
        $this->site['/']['body'] = str_replace( '</main>', '<a href="/authors">Authors</a> <a href="/authors/jane-smith">Jane</a> <a href="/loop-a">Loop</a>'
                                                . ' <a href="/elsewhere">Elsewhere</a> <a href="/moved">Moved</a> <a href="/to-login">Sign in</a></main>', $this->site['/']['body'] );
        $redirect = function ( $status, $location )
        {
            return array( 'status' => $status, 'location' => $location, 'type' => 'text/html; charset=utf-8', 'cookies' => array(), 'body' => '<p>Moved</p>' );
        };
        $this->site += array(
            '/authors' => $redirect( 302, 'http://site.test/' ),
            '/authors/jane-smith' => $redirect( 301, '/authors' ),
            '/loop-a' => $redirect( 302, '/loop-b' ),
            '/loop-b' => $redirect( 302, '/loop-a' ),
            '/elsewhere' => $redirect( 302, 'https://other.example.org/far-away' ),
            '/moved' => $redirect( 301, 'news' ),
            '/to-login' => $redirect( 302, '/user/login' ),
        );
    }

    /** SB-11 */
    public function testRedirectsBecomeStubs()
    {
        $this->redirectingSite();
        list( $report, $dir ) = $this->build( array( 'Assets' => 'referenced' ) );
        $this->assertSame( 0, $report['exit'], json_encode( $report['errors'] ) );
        $this->assertSame( 4, $report['counts']['pages'], 'a redirecting address is no page of the copy' );
        $this->assertSame( 5, $report['counts']['redirects_followed'], json_encode( $report['redirects_followed'] ) );

        $authors = file_get_contents( "$dir/authors/index.html" );
        $this->assertStringNotContainsString( '<h1>Home</h1>', $authors, 'not a copy of the page it sends to' );
        $this->assertSame( expStaticRedirects::stub( '../', 'https://owner.github.io/repo/' ), $authors );
        $this->assertSame( expStaticRedirects::stub( '../../', 'https://owner.github.io/repo/' ), file_get_contents( "$dir/authors/jane-smith/index.html" ), 'a chain: to where it ends' );
        $this->assertSame( expStaticRedirects::stub( '../news/', 'https://owner.github.io/repo/news/' ), file_get_contents( "$dir/moved/index.html" ) );
        $this->assertSame( expStaticRedirects::stub( 'https://other.example.org/far-away', 'https://other.example.org/far-away' ), file_get_contents( "$dir/elsewhere/index.html" ) );
        $this->assertSame( expStaticRedirects::stub( 'https://www.example.com/user/login', 'https://www.example.com/user/login' ), file_get_contents( "$dir/to-login/index.html" ), 'outside the copy: the live site' );
        $this->assertFileDoesNotExist( "$dir/loop-a/index.html" );
        $this->assertFileDoesNotExist( "$dir/loop-b/index.html" );
        $reasons = array();
        foreach ( $report['skipped'] as $skipped )
            $reasons[$skipped['path']] = $skipped['reason'];
        $this->assertSame( 'a redirect loop', $reasons['/loop-a'] );
        $this->assertSame( 'a redirect loop', $reasons['/loop-b'] );

        $kinds = array();
        foreach ( $report['redirects_followed'] as $entry )
            $kinds[$entry['path']] = $entry['kind'] . ' ' . $entry['status'];
        $this->assertSame( array( '/authors' => 'page 302', '/authors/jane-smith' => 'page 301', '/elsewhere' => 'live 302', '/moved' => 'page 301', '/to-login' => 'live 302' ), $kinds );

        $requested = array_count_values( $this->requested );
        $this->assertSame( 1, $requested['/'], 'the page sent to is fetched once, under its own address' );
        $this->assertArrayNotHasKey( '/far-away', $requested, 'another host is never fetched' );
        $this->assertArrayNotHasKey( '/user/login', $requested );

        $home = file_get_contents( "$dir/index.html" );
        $this->assertStringContainsString( '<a href="authors/">Authors</a>', $home, 'a link to a redirecting address goes to its stub' );
        $manifest = json_decode( file_get_contents( "$dir/.static-publish/manifest.json" ), true );
        $this->assertNotEmpty( $manifest );
        $this->assertSame( 0, $report['link_check']['broken_count'], json_encode( $report['link_check']['broken'] ) );

        // OutsideLinks=unlink: a redirect out of the copy goes to its front page; another host stays as it is
        list( $report, $dir ) = $this->build( array( 'Assets' => 'referenced', 'OutsideLinks' => 'unlink' ) );
        $this->assertSame( 0, $report['exit'], json_encode( $report['errors'] ) );
        $this->assertSame( expStaticRedirects::stub( '../', 'https://owner.github.io/repo/' ), file_get_contents( "$dir/to-login/index.html" ) );
        $this->assertSame( expStaticRedirects::stub( 'https://other.example.org/far-away', 'https://other.example.org/far-away' ), file_get_contents( "$dir/elsewhere/index.html" ) );
    }
}
