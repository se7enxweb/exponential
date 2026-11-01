<?php
/**
 * The link rewriting of static publishing (expStaticLinkRewriter): pure, no installation, no database.
 *
 *  LR-01 the file of a page and the address it is linked by; paths unsafe on Windows are refused
 *  LR-02 relative paths between files of the export
 *  LR-03 classification: page, file, outside the site, external, not a link; index.php and the siteaccess prefix
 *  LR-04 relative mode: href, src, links from the root and from deep pages, anchors kept, query of a file kept
 *  LR-05 base-path mode: every link below /repo/
 *  LR-06 srcset, poster, data-src, style url(), <style> blocks; script content and comments untouched
 *  LR-07 social cards, canonical and hreflang alternates absolute with a public address
 *  LR-08 pages the export does not hold (module views, queries, unknown pages) and paths outside the site go to the
 *        live site; without a live address they stay as they are
 *  LR-09 style sheets: url() and @import relative to the style sheet; data: left alone
 *  LR-10 entities and quoting survive; unchanged tags are byte for byte the same
 *  LR-11 what the rewriter found: pages, files (with their source path), counts
 *  LR-12 dot segments and base path normalisation
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expStaticLinkRewriterTest extends PHPUnit\Framework\TestCase
{
    private static function rewriter( array $options = array() )
    {
        return new expStaticLinkRewriter( $options + array(
            'origins' => array( 'http://127.0.0.1:18308', 'www.example.com' ),
            'site_path' => '',
            'mode' => 'relative',
            'base_path' => '/repo/',
            'live_url' => 'https://www.example.com',
        ) );
    }

    /** LR-01 */
    public function testPageFileAndUrl()
    {
        $this->assertSame( 'index.html', expStaticLinkRewriter::pageFile( '/' ) );
        $this->assertSame( 'index.html', expStaticLinkRewriter::pageFile( '' ) );
        $this->assertSame( 'about-us/index.html', expStaticLinkRewriter::pageFile( '/about-us' ) );
        $this->assertSame( 'about-us/team/index.html', expStaticLinkRewriter::pageFile( '/about-us/team/' ) );
        $this->assertSame( 'old/page.html', expStaticLinkRewriter::pageFile( '/old/page.html' ) );
        $this->assertSame( 'news/(offset)/10/index.html', expStaticLinkRewriter::pageFile( '/news/(offset)/10' ) );
        $this->assertSame( 'über uns/index.html', expStaticLinkRewriter::pageFile( '/%C3%BCber%20uns' ) );
        foreach ( array( '/a:b', '/a/b*', '/con', '/x/aux.txt', '/trailing.', '/a/../b', '/a|b', '/a?b' ) as $unsafe )
            $this->assertNull( expStaticLinkRewriter::pageFile( $unsafe ), "$unsafe cannot be a file name everywhere" );
        $this->assertSame( '', expStaticLinkRewriter::pageUrl( 'index.html' ) );
        $this->assertSame( 'about-us/', expStaticLinkRewriter::pageUrl( 'about-us/index.html' ) );
        $this->assertSame( 'old/page.html', expStaticLinkRewriter::pageUrl( 'old/page.html' ) );
        $this->assertSame( '%C3%BCber%20uns/', expStaticLinkRewriter::pageUrl( 'über uns/index.html' ) );
        $this->assertSame( 'news/(offset)/10/', expStaticLinkRewriter::pageUrl( 'news/(offset)/10/index.html' ) );
        $this->assertTrue( expStaticLinkRewriter::safePath( 'var/site/storage/images/a.jpg' ) );
        $this->assertFalse( expStaticLinkRewriter::safePath( '/abs' ) );
        $this->assertFalse( expStaticLinkRewriter::safePath( 'a/../b' ) );
    }

    /** LR-02 */
    public function testRelative()
    {
        $this->assertSame( './', expStaticLinkRewriter::relative( 'index.html', '' ) );
        $this->assertSame( 'about-us/', expStaticLinkRewriter::relative( 'index.html', 'about-us/' ) );
        $this->assertSame( '../', expStaticLinkRewriter::relative( 'about-us/index.html', '' ) );
        $this->assertSame( '../../', expStaticLinkRewriter::relative( 'a/b/index.html', '' ) );
        $this->assertSame( '../c/', expStaticLinkRewriter::relative( 'a/b/index.html', 'a/c/' ) );
        $this->assertSame( './', expStaticLinkRewriter::relative( 'a/b/index.html', 'a/b/' ) );
        $this->assertSame( 'c/', expStaticLinkRewriter::relative( 'a/b/index.html', 'a/b/c/' ) );
        $this->assertSame( '../../var/x.css', expStaticLinkRewriter::relative( 'a/b/index.html', 'var/x.css' ) );
        $this->assertSame( 'x.css', expStaticLinkRewriter::relative( 'var/y.css', 'var/x.css' ) );
        $this->assertSame( '../fonts/f.woff2', expStaticLinkRewriter::relative( 'design/s/stylesheets/a.css', 'design/s/fonts/f.woff2' ) );
    }

    /** LR-03 */
    public function testClassify()
    {
        $r = self::rewriter( array( 'site_path' => '/site' ) );
        $c = $r->classify( '/site/about-us/', '/site' );
        $this->assertSame( array( 'page', '/about-us' ), array( $c['kind'], $c['page'] ) );
        $c = $r->classify( '/index.php/site/about-us', '/site' );
        $this->assertSame( array( 'page', '/about-us' ), array( $c['kind'], $c['page'] ) );
        $c = $r->classify( '/site', '/site/x' );
        $this->assertSame( array( 'page', '/' ), array( $c['kind'], $c['page'] ) );
        $c = $r->classify( '/index.php', '/site/x' );
        $this->assertSame( 'outside', $c['kind'], '/index.php without the prefix is the default siteaccess, not this site' );
        $c = $r->classify( '/var/site/storage/images/a/b.jpg', '/site/x' );
        $this->assertSame( array( 'file', 'var/site/storage/images/a/b.jpg' ), array( $c['kind'], $c['output'] ) );
        $c = $r->classify( '/extension/theme/design/x/stylesheets/a.css?x=2', '/site/x' );
        $this->assertSame( array( 'file', 'extension/theme/design/x/stylesheets/a.css', '?x=2' ), array( $c['kind'], $c['output'], $c['query'] ) );
        $c = $r->classify( '/site/content/download/12/34/file/report.pdf', '/site/x' );
        $this->assertSame( array( 'file', 'content/download/12/34/file/report.pdf' ), array( $c['kind'], $c['output'] ) );
        $c = $r->classify( '/admin/content/view', '/site/x' );
        $this->assertSame( 'outside', $c['kind'] );
        $c = $r->classify( 'http://127.0.0.1:18308/site/news', '/site/x' );
        $this->assertSame( array( 'page', '/news' ), array( $c['kind'], $c['page'] ) );
        $c = $r->classify( 'https://www.example.com/site/news#top', '/site/x' );
        $this->assertSame( array( 'page', '/news', '#top' ), array( $c['kind'], $c['page'], $c['fragment'] ) );
        $c = $r->classify( '//www.example.com/site/news', '/site/x' );
        $this->assertSame( 'page', $c['kind'], 'protocol relative addresses of the site are the site' );
        $this->assertSame( 'external', $r->classify( 'https://github.com/x', '/site' )['kind'] );
        $this->assertSame( 'external', $r->classify( 'http://127.0.0.1:9999/site', '/site' )['kind'], 'another port is another origin' );
        foreach ( array( '#main', 'mailto:a@b.c', 'tel:+4912', 'javascript:void(0)', 'data:image/png;base64,AAAA', '', '?page=2' ) as $none )
            $this->assertSame( 'none', $r->classify( $none, '/site/x' )['kind'], $none );
        $c = $r->classify( 'team', '/site/about-us/x' );
        $this->assertSame( array( 'page', '/about-us/team' ), array( $c['kind'], $c['page'] ), 'relative to the document' );
        $c = $r->classify( '../images/a.png', '/design/s/stylesheets/site.css' );
        $this->assertSame( array( 'file', 'design/s/images/a.png' ), array( $c['kind'], $c['output'] ) );
    }

    /** LR-04 */
    public function testRelativeMode()
    {
        $r = self::rewriter();
        $html = '<a href="/">Home</a><a href="/about-us#team">About</a><img src="/var/site/storage/images/a.jpg" alt="">'
              . '<link rel="stylesheet" href="/var/site/cache/public/stylesheets/p_123.css?x=1">';
        $out = $r->rewriteHtml( $html, '/', 'index.html' );
        $this->assertStringContainsString( '<a href="./">Home</a>', $out );
        $this->assertStringContainsString( '<a href="about-us/#team">About</a>', $out );
        $this->assertStringContainsString( '<img src="var/site/storage/images/a.jpg" alt="">', $out );
        $this->assertStringContainsString( 'href="var/site/cache/public/stylesheets/p_123.css?x=1"', $out );

        $out = $r->rewriteHtml( $html, '/a/b', 'a/b/index.html' );
        $this->assertStringContainsString( '<a href="../../">Home</a>', $out );
        $this->assertStringContainsString( '<a href="../../about-us/#team">About</a>', $out );
        $this->assertStringContainsString( 'src="../../var/site/storage/images/a.jpg"', $out );
        $out = $r->rewriteHtml( '<a href="/index.php/a/c">c</a><a href="http://127.0.0.1:18308/a">a</a>', '/a/b', 'a/b/index.html' );
        $this->assertSame( '<a href="../c/">c</a><a href="../">a</a>', $out );
    }

    /** LR-05 */
    public function testBasePathMode()
    {
        $r = self::rewriter( array( 'mode' => 'base-path', 'base_path' => 'repo' ) );
        $out = $r->rewriteHtml( '<a href="/">h</a><a href="/about-us/">a</a><script src="/design/s/javascript/x.js"></script>', '/a/b', 'a/b/index.html' );
        $this->assertSame( '<a href="/repo/">h</a><a href="/repo/about-us/">a</a><script src="/repo/design/s/javascript/x.js"></script>', $out );
        $r = self::rewriter( array( 'mode' => 'base-path', 'base_path' => '/' ) );
        $this->assertSame( '<a href="/about-us/">a</a>', $r->rewriteHtml( '<a href="/about-us">a</a>', '/', 'index.html' ) );
    }

    /** LR-06 */
    public function testSrcsetMediaStyleAndScripts()
    {
        $r = self::rewriter();
        $html = '<img src="/var/s/storage/images/a_small.jpg" srcset="/var/s/storage/images/a_small.jpg 480w, /var/s/storage/images/a_large.jpg 1200w" sizes="100vw">'
              . '<video poster="/var/s/storage/images/p.jpg"><source src="/var/s/storage/original/video/v.mp4" type="video/mp4"></video>'
              . '<div data-src="/var/s/storage/images/lazy.jpg" style="background-image: url(\'/design/s/images/bg.png\')"></div>'
              . '<style>.x{background:url(/design/s/images/b.png)}</style>'
              . '<script>var u = "/about-us"; el.innerHTML = \'<a href="/x">\';</script><!-- <a href="/c"> -->';
        $out = $r->rewriteHtml( $html, '/a/b', 'a/b/index.html' );
        $this->assertStringContainsString( 'srcset="../../var/s/storage/images/a_small.jpg 480w, ../../var/s/storage/images/a_large.jpg 1200w"', $out );
        $this->assertStringContainsString( 'poster="../../var/s/storage/images/p.jpg"', $out );
        $this->assertStringContainsString( '<source src="../../var/s/storage/original/video/v.mp4"', $out );
        $this->assertStringContainsString( 'data-src="../../var/s/storage/images/lazy.jpg"', $out );
        $this->assertStringContainsString( "url(&#039;../../design/s/images/bg.png&#039;)", $out );
        $this->assertStringContainsString( '<style>.x{background:url(../../design/s/images/b.png)}</style>', $out );
        $this->assertStringContainsString( '<script>var u = "/about-us"; el.innerHTML = \'<a href="/x">\';</script>', $out, 'script content untouched' );
        $this->assertStringContainsString( '<!-- <a href="/c"> -->', $out, 'comments untouched' );
        $this->assertSame( '<img srcset="var/x.png 1x, var/y.png 2x">', $r->rewriteHtml( '<img srcset="/var/x.png 1x,/var/y.png 2x">', '/', 'index.html' ),
                           'candidates without a space after the comma' );
        $this->assertSame( array( array( '/a.png', '' ), array( '/b,c.png', '2x' ), array( '/d.png', '100w' ) ),
                           expStaticLinkRewriter::parseSrcset( ' /a.png, /b,c.png 2x ,/d.png 100w' ) );
    }

    /** LR-07 */
    public function testSocialCardsCanonicalAndHreflang()
    {
        $r = self::rewriter( array( 'public_url' => 'https://owner.github.io/repo' ) );
        $html = '<meta property="og:url" content="https://www.example.com/about-us">'
              . '<meta property="og:image" content="/var/s/storage/images/card.jpg">'
              . '<meta name="twitter:image" content="/var/s/storage/images/card.jpg">'
              . '<meta name="description" content="/not/a/link">'
              . '<link rel="canonical" href="/about-us">'
              . '<link rel="alternate" hreflang="de" href="/about-us">'
              . '<a href="/about-us">a</a>';
        $out = $r->rewriteHtml( $html, '/about-us', 'about-us/index.html' );
        $this->assertStringContainsString( '<meta property="og:url" content="https://owner.github.io/repo/about-us/">', $out );
        $this->assertStringContainsString( '<meta property="og:image" content="https://owner.github.io/repo/var/s/storage/images/card.jpg">', $out );
        $this->assertStringContainsString( '<meta name="twitter:image" content="https://owner.github.io/repo/var/s/storage/images/card.jpg">', $out );
        $this->assertStringContainsString( '<meta name="description" content="/not/a/link">', $out );
        $this->assertStringContainsString( '<link rel="canonical" href="https://owner.github.io/repo/about-us/">', $out );
        $this->assertStringContainsString( '<link rel="alternate" hreflang="de" href="https://owner.github.io/repo/about-us/">', $out );
        $this->assertStringContainsString( '<a href="./">a</a>', $out, 'ordinary links stay relative' );

        $out = self::rewriter()->rewriteHtml( '<link rel="canonical" href="/about-us">', '/x', 'x/index.html' );
        $this->assertSame( '<link rel="canonical" href="../about-us/">', $out, 'without a public address like any link' );

        $ld = '<script type="application/ld+json">{"@type":"WebPage","url":"http://127.0.0.1:18308/about-us","image":"https:\/\/www.example.com\/var\/s\/a.jpg",'
            . '"license":"https://www.gnu.org/licenses/gpl.html","search":"http://127.0.0.1:18308/content/search?q={q}"}</script>';
        $out = $r->rewriteHtml( $ld, '/about-us', 'about-us/index.html' );
        $this->assertStringContainsString( '"url":"https://owner.github.io/repo/about-us/"', $out, 'structured data: absolute below the public address' );
        $this->assertStringContainsString( '"image":"https:\/\/owner.github.io\/repo\/var\/s\/a.jpg"', $out, 'escaped slashes stay escaped' );
        $this->assertStringContainsString( '"license":"https://www.gnu.org/licenses/gpl.html"', $out );
        $this->assertStringContainsString( '"search":"https://www.example.com/content/search?q={q}"', $out, 'a page the copy does not hold goes live' );
        $this->assertStringNotContainsString( '127.0.0.1', $out );
    }

    /** LR-08 */
    public function testWhatTheExportDoesNotHoldGoesLive()
    {
        $held = array( '/' => true, '/about-us' => true );
        $r = self::rewriter( array( 'site_path' => '/site', 'pages' => function ( $page, $query ) use ( $held ) { return $query === '' && isset( $held[$page] ); } ) );
        $html = '<a href="/site/about-us">a</a><a href="/site/content/search?SearchText=x">s</a><a href="/site/user/login">l</a>'
              . '<a href="/site/blog?page=2">b</a><a href="/admin">adm</a><form action="/site/content/search"></form><a href="/site/a:b">c</a>';
        $out = $r->rewriteHtml( $html, '/site', 'index.html' );
        $this->assertStringContainsString( '<a href="about-us/">a</a>', $out );
        $this->assertStringContainsString( '<a href="https://www.example.com/site/content/search?SearchText=x">s</a>', $out );
        $this->assertStringContainsString( '<a href="https://www.example.com/site/user/login">l</a>', $out );
        $this->assertStringContainsString( '<a href="https://www.example.com/site/blog?page=2">b</a>', $out );
        $this->assertStringContainsString( '<a href="https://www.example.com/admin">adm</a>', $out );
        $this->assertStringContainsString( '<form action="https://www.example.com/site/content/search">', $out );
        $this->assertStringContainsString( '<a href="https://www.example.com/site/a:b">c</a>', $out, 'a path that cannot be a file' );
        $counts = $r->counts();
        $this->assertSame( 1, $counts['rewritten'] );
        $this->assertSame( 6, $counts['live'] );

        $r = self::rewriter( array( 'live_url' => '', 'pages' => function () { return false; } ) );
        $this->assertSame( '<a href="/x">x</a>', $r->rewriteHtml( '<a href="/x">x</a>', '/', 'index.html' ) );
        $r = self::rewriter( array( 'files' => function ( $file ) { return $file === 'var/a.png'; } ) );
        $this->assertSame( '<img src="var/a.png"><img src="https://www.example.com/var/b.png">',
                           $r->rewriteHtml( '<img src="/var/a.png"><img src="/var/b.png">', '/', 'index.html' ), 'files not collected go live' );
    }

    /** LR-09 */
    public function testCss()
    {
        $r = self::rewriter();
        $css = "@import url('/design/s/stylesheets/base.css');\n@import \"print.css\";\n.a{background:url(../images/a.png) no-repeat}\n"
             . ".b{background:url(\"/var/s/storage/images/b.jpg\")}\n.c{background:url(data:image/png;base64,AAAA)}\n"
             . "@font-face{src:url(/design/s/fonts/f.woff2?#iefix) format('woff2'),url(https://fonts.example.org/f.woff)}";
        $out = $r->rewriteCss( $css, '/design/s/stylesheets/site.css', 'design/s/stylesheets/site.css' );
        $this->assertStringContainsString( "@import url('base.css');", $out );
        $this->assertStringContainsString( '@import "print.css";', $out );
        $this->assertStringContainsString( 'url(../images/a.png) no-repeat', $out );
        $this->assertStringContainsString( 'url("../../../var/s/storage/images/b.jpg")', $out );
        $this->assertStringContainsString( 'url(data:image/png;base64,AAAA)', $out );
        $this->assertStringContainsString( 'url(../fonts/f.woff2?#iefix)', $out, 'query and fragment of a font kept' );
        $this->assertStringContainsString( 'url(https://fonts.example.org/f.woff)', $out );
        $files = $r->foundFiles();
        $this->assertSame( '/design/s/stylesheets/base.css', $files['design/s/stylesheets/base.css'] );
        $this->assertArrayHasKey( 'design/s/images/a.png', $files );
        $this->assertArrayHasKey( 'design/s/fonts/f.woff2', $files );
        $packed = self::rewriter( array( 'mode' => 'base-path', 'base_path' => '/repo/' ) )->rewriteCss( '.x{background:url(/design/s/images/x.png)}', '/var/s/cache/public/p.css', 'var/s/cache/public/p.css' );
        $this->assertSame( '.x{background:url(/repo/design/s/images/x.png)}', $packed );
    }

    /** LR-10 */
    public function testEntitiesAndQuotingAndUntouchedTags()
    {
        $r = self::rewriter();
        $out = $r->rewriteHtml( "<a href='/a?x=1&amp;y=2' class=\"k\">a</a><a href=/b>b</a>", '/', 'index.html' );
        $this->assertStringContainsString( "<a href='https://www.example.com/a?x=1&amp;y=2' class=\"k\">", $out, 'single quotes kept, & encoded once' );
        $this->assertStringContainsString( '<a href="b/">b</a>', $out, 'an unquoted value gets quotes' );
        $html = "<div  class='x'   data-x=\"1\">\n<p>Text with /path/ and src=\"/x\" in it</p><input type=\"checkbox\" checked>";
        $this->assertSame( $html, $r->rewriteHtml( $html, '/', 'index.html' ), 'nothing to rewrite: byte for byte the same' );
        $out = $r->rewriteHtml( '<A HREF="/About">x</A><IMG SRC="/var/x.png"/>', '/', 'index.html' );
        $this->assertSame( '<A HREF="About/">x</A><IMG SRC="var/x.png"/>', $out );
    }

    /** LR-11 */
    public function testFound()
    {
        $r = self::rewriter( array( 'site_path' => '/site' ) );
        $r->rewriteHtml( '<a href="/site/a">a</a><a href="/site/b?x=1">b</a><img src="/var/site/storage/images/x.png"><a href="/site/files/r.pdf">r</a>', '/site', 'index.html' );
        $this->assertSame( array( '/a' => true ), $r->foundPages() );
        $this->assertSame( array( 'var/site/storage/images/x.png' => '/var/site/storage/images/x.png', 'files/r.pdf' => '/site/files/r.pdf' ), $r->foundFiles() );
        $r->reset();
        $this->assertSame( array(), $r->foundFiles() );
        $this->assertSame( array( 'rewritten' => 0, 'live' => 0, 'external' => 0 ), $r->counts() );
    }

    /** LR-12 */
    public function testDotSegmentsAndBasePath()
    {
        $this->assertSame( '/a/c', expStaticLinkRewriter::removeDotSegments( '/a/b/../c' ) );
        $this->assertSame( '/c', expStaticLinkRewriter::removeDotSegments( '/../../c' ) );
        $this->assertSame( '/a/', expStaticLinkRewriter::removeDotSegments( '/a/./b/..' ) );
        $this->assertSame( '/', expStaticLinkRewriter::removeDotSegments( '/' ) );
        $this->assertSame( '/repo/', expStaticLinkRewriter::normaliseBasePath( 'repo' ) );
        $this->assertSame( '/repo/x/', expStaticLinkRewriter::normaliseBasePath( '//repo//x/' ) );
        $this->assertSame( '/', expStaticLinkRewriter::normaliseBasePath( '' ) );
        $this->assertSame( '/', expStaticLinkRewriter::normaliseBasePath( '/../' ) );
        $this->assertSame( '/a/', expStaticLinkRewriter::normaliseBasePath( '\\a\\' ), 'Windows separators' );
    }
}
