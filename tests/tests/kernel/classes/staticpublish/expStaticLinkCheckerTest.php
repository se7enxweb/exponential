<?php
/**
 * The link check and the local preview of a static export (expStaticLinkChecker, expStaticServe), on a small export
 * written under var/tmp. No installation, no network.
 *
 *  LC-01 a relative export has no broken link at / and below /repo/
 *  LC-02 broken links and files are found, with the file that holds them; links that leave the mount are broken
 *  LC-03 a base-path export works below its base path only
 *  LC-04 what is not checked: other hosts, mailto:, data:, fragments; .git and .static-publish are skipped
 *  LC-05 404.html resolves against the mount
 *  SV-01 the preview: index.html of a folder, redirects to the slash and to the mount, 404.html for what is missing,
 *        never a file outside the export, in .git or in .static-publish
 *  SV-02 content types and the command line of the server
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expStaticLinkCheckerTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function setUp(): void
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/static-linkcheck-test-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
    }

    protected function tearDown(): void
    {
        expStaticBuild::removeDirectory( $this->dir );
    }

    private function write( array $files )
    {
        foreach ( $files as $path => $content )
        {
            $file = $this->dir . '/' . $path;
            if ( !is_dir( dirname( $file ) ) )
                mkdir( dirname( $file ), 0775, true );
            file_put_contents( $file, $content );
        }
    }

    private function relativeExport()
    {
        $this->write( array(
            'index.html' => '<link rel="stylesheet" href="var/s/cache/public/a.css"><a href="./">h</a><a href="about-us/#team">a</a>'
                          . '<img src="var/s/storage/images/x.jpg" srcset="var/s/storage/images/x.jpg 1x, var/s/storage/images/x2.jpg 2x">'
                          . '<a href="https://www.example.com/content/search">live</a><a href="mailto:a@b.c">m</a><a href="#top">t</a>'
                          . '<img src="data:image/png;base64,AA"><script src="design/s/javascript/a.js"></script><script>var x = "<a href=\"/nowhere\">";</script>',
            'about-us/index.html' => '<a href="../">h</a><a href="../about-us/">self</a><a href="team">team</a><img src="../var/s/storage/images/x.jpg">',
            'about-us/team/index.html' => '<a href="../../">h</a><div style="background:url(\'../../var/s/storage/images/x.jpg\')"></div>',
            'var/s/cache/public/a.css' => '.a{background:url(../../../../design/s/images/bg.png)}@import "b.css";',
            'var/s/cache/public/b.css' => '.b{}',
            'var/s/storage/images/x.jpg' => 'jpg', 'var/s/storage/images/x2.jpg' => 'jpg',
            'design/s/images/bg.png' => 'png', 'design/s/javascript/a.js' => '1;',
            '404.html' => '<a href="/repo/">home</a><img src="/repo/var/s/storage/images/x.jpg">',
            '.git/HEAD' => 'ref: refs/heads/gh-pages', '.static-publish/manifest.json' => '{}',
        ) );
    }

    /** LC-01 */
    public function testRelativeExportAtRootAndBelowARepository()
    {
        $this->relativeExport();
        $check = expStaticLinkChecker::check( $this->dir, '/repo/' );
        $this->assertSame( 0, $check['broken_count'], json_encode( $check['broken'] ) );
        $this->assertSame( 6, $check['files'] );
        $this->assertGreaterThan( 15, $check['links'] );
        unlink( $this->dir . '/404.html' );
        $check = expStaticLinkChecker::check( $this->dir, '/' );
        $this->assertSame( 0, $check['broken_count'], json_encode( $check['broken'] ) );
    }

    /** LC-02 */
    public function testBroken()
    {
        $this->relativeExport();
        $this->write( array( 'about-us/index.html' => '<a href="../missing/">m</a><img src="../var/s/storage/images/none.jpg"><a href="../../../outside/">o</a>',
                             'var/s/cache/public/b.css' => '.b{background:url(gone.png)}' ) );
        $check = expStaticLinkChecker::check( $this->dir, '/repo/' );
        $this->assertSame( 4, $check['broken_count'] );
        $resolved = array_column( $check['broken'], 'resolved', 'link' );
        $this->assertSame( '/repo/missing/', $resolved['../missing/'] );
        $this->assertSame( '/repo/var/s/storage/images/none.jpg', $resolved['../var/s/storage/images/none.jpg'] );
        $this->assertSame( '/outside/', $resolved['../../../outside/'], 'above the mount' );
        $this->assertSame( '/repo/var/s/cache/public/gone.png', $resolved['gone.png'] );
        $files = array_column( $check['broken'], 'file', 'link' );
        $this->assertSame( 'var/s/cache/public/b.css', $files['gone.png'] );
        $this->assertCount( 1, expStaticLinkChecker::check( $this->dir, '/repo/', array( 'max_broken' => 1 ) )['broken'] );
    }

    /** LC-03 */
    public function testBasePathExport()
    {
        $this->write( array( 'index.html' => '<a href="/repo/about/">a</a><img src="/repo/var/x.png">', 'about/index.html' => '<a href="/repo/">h</a>', 'var/x.png' => 'x' ) );
        $this->assertSame( 0, expStaticLinkChecker::check( $this->dir, '/repo/' )['broken_count'] );
        $this->assertSame( 3, expStaticLinkChecker::check( $this->dir, '/' )['broken_count'], 'at the root the /repo/ links lead nowhere' );
    }

    /** LC-04 */
    public function testNotCheckedAndSkipped()
    {
        foreach ( array( 'https://x.org/a', '//cdn.example.org/a.js', 'mailto:a@b', 'tel:1', 'data:x', '#a', '?q=1', '', 'javascript:void(0)' ) as $ref )
            $this->assertNull( expStaticLinkChecker::resolve( $ref, '/repo/' ), $ref );
        $this->assertSame( '/repo/a/b', expStaticLinkChecker::resolve( 'b?x=1#y', '/repo/a/' ) );
        $this->assertSame( '/repo/', expStaticLinkChecker::resolve( '../', '/repo/a/' ) );
        $this->relativeExport();
        $files = expStaticLinkChecker::files( $this->dir );
        $this->assertNotContains( '.git/HEAD', $files );
        $this->assertNotContains( '.static-publish/manifest.json', $files );
        $this->assertContains( 'about-us/team/index.html', $files );
    }

    /** LC-05 */
    public function testNotFoundPageResolvesAgainstTheMount()
    {
        $this->write( array( 'index.html' => 'x', '404.html' => '<a href="about/">a</a>', 'about/index.html' => 'a' ) );
        $this->assertSame( 0, expStaticLinkChecker::check( $this->dir, '/repo/' )['broken_count'] );
    }

    /** SV-01 */
    public function testServe()
    {
        $this->relativeExport();
        $r = expStaticServe::resolve( $this->dir, '/repo/', '/repo/' );
        $this->assertSame( array( 200, $this->dir . '/index.html' ), array( $r['status'], $r['file'] ) );
        $r = expStaticServe::resolve( $this->dir, '/repo/', '/repo/about-us/team/' );
        $this->assertSame( array( 200, $this->dir . '/about-us/team/index.html' ), array( $r['status'], $r['file'] ) );
        $r = expStaticServe::resolve( $this->dir, '/repo/', '/repo/about-us' );
        $this->assertSame( array( 301, '/repo/about-us/' ), array( $r['status'], $r['location'] ) );
        $this->assertSame( array( 301, '/repo/' ), array( expStaticServe::resolve( $this->dir, '/repo/', '/repo' )['status'], expStaticServe::resolve( $this->dir, '/repo/', '/' )['location'] ) );
        $r = expStaticServe::resolve( $this->dir, '/repo/', '/repo/var/s/storage/images/x.jpg' );
        $this->assertSame( array( 200, 'image/jpeg' ), array( $r['status'], $r['type'] ) );
        foreach ( array( '/repo/missing/', '/other/', '/repo/../index.html', '/repo/.git/HEAD', '/repo/.static-publish/manifest.json', '/repo/%2e%2e/x' ) as $path )
        {
            $r = expStaticServe::resolve( $this->dir, '/repo/', $path );
            $this->assertSame( array( 404, $this->dir . '/404.html' ), array( $r['status'], $r['file'] ), $path );
        }
        $r = expStaticServe::resolve( $this->dir, '/', '/about-us/' );
        $this->assertSame( 200, $r['status'], 'at the root' );
    }

    /** SV-02 */
    public function testTypesAndCommand()
    {
        $this->assertSame( 'text/css; charset=utf-8', expStaticServe::type( 'a/b.CSS' ) );
        $this->assertSame( 'font/woff2', expStaticServe::type( 'f.woff2' ) );
        $this->assertSame( 'application/octet-stream', expStaticServe::type( 'x.unknown' ) );
        $command = expStaticServe::command( '/usr/bin/php', '127.0.0.1', 8090, '/srv/my site' );
        $this->assertSame( array( '/usr/bin/php', '-S', '127.0.0.1:8090', '-t', '/srv/my site' ), array_slice( $command, 0, 5 ) );
        $this->assertFileExists( $command[5] );
        $this->assertStringEndsWith( 'expstaticserverouter.php', $command[5] );
    }
}
