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
 *  LR-13 links to other hosts that carry an address of the site in their query (share links) get the public
 *        address there, or the live one; never the crawl address
 *  LR-14 OutsideLinks=unlink: a link to a page the export does not hold (relative, absolute, with a query) becomes a
 *        placeholder <a> (no href, target, rel or handlers; its classes plus exp-static-unavailable, aria-disabled),
 *        the style sheet linked in the <head> of a page that has one (relative or below the base path); held pages,
 *        fragments, files, srcset, images, forms and other hosts untouched; counted
 *  LR-15 AdminLinks=unlink: links into an administration siteaccess (with or without a site path, on the live
 *        address too) become placeholders, /administrators is no admin path; each setting alone; navigation <link>s
 *        dropped, style sheets kept; the admin paths a build derives (expStaticBuild::adminPaths())
 *  LR-16 keep (the default) changes nothing: the output is byte for byte the one without the settings
 *  LR-17 a site served at the root of its SiteURL host (origin_site_paths): its pages there are the site's below either
 *        path, so a canonical link names the public address and links to held pages are never taken out
 *  LR-18 several siteaccesses in one export (sites): each siteaccess's host path maps to its folder, the longest
 *        first; links between them stay in the export (relative and base path), hreflang alternates are absolute, a
 *        siteaccess not in the export is outside (unlink), on a SiteURL origin of its own too; the language switcher
 *        (switchlanguage/to/<siteaccess>) goes to the same page in the other siteaccess, else its front page
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
        $this->assertSame( array( 'rewritten' => 0, 'live' => 0, 'external' => 0, 'removed_outside' => 0, 'removed_admin' => 0, 'unavailable' => 0 ), $r->counts() );
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

    /** LR-13 */
    public function testShareLinksCarryThePublicAddress()
    {
        $r = self::rewriter( array( 'site_path' => '/site', 'public_url' => 'https://owner.github.io/repo/' ) );
        $html = '<a href="https://www.facebook.com/sharer.php?u=http%3A%2F%2F127.0.0.1%3A18308%2Fsite%2Ffitness%2Frun&amp;t=Run+fast">f</a>'
              . '<a href="https://www.linkedin.com/shareArticle?mini=true&amp;url=http%3A%2F%2F127.0.0.1%3A18308%2Fsite%2Ffitness%2Frun&amp;title=Run">l</a>'
              . '<a href="https://twitter.com/intent/tweet?url=https://www.example.com/site/fitness/run">t</a>'
              . '<a href="https://www.facebook.com/sharer.php?u=https%3A%2F%2Fother.example.org%2Fx">o</a>'
              . '<a href="https://www.example.org/?q=plain">p</a>';
        $out = $r->rewriteHtml( $html, '/site/fitness/run', 'fitness/run/index.html' );
        $this->assertStringNotContainsString( '127.0.0.1', $out );
        $encoded = rawurlencode( 'https://owner.github.io/repo/fitness/run/' );
        $this->assertStringContainsString( 'sharer.php?u=' . $encoded . '&amp;t=Run+fast"', $out );
        $this->assertStringContainsString( 'shareArticle?mini=true&amp;url=' . $encoded . '&amp;title=Run"', $out );
        $this->assertStringContainsString( 'tweet?url=' . $encoded . '"', $out, 'a plain address in the query too' );
        $this->assertStringContainsString( 'sharer.php?u=https%3A%2F%2Fother.example.org%2Fx"', $out, 'an address of another host stays' );
        $this->assertStringContainsString( 'href="https://www.example.org/?q=plain"', $out );

        $live = self::rewriter( array( 'site_path' => '/site' ) )->rewriteHtml( $html, '/site/fitness/run', 'fitness/run/index.html' );
        $this->assertStringContainsString( 'sharer.php?u=' . rawurlencode( 'https://www.example.com/site/fitness/run' ), $live, 'no public address: the live one' );

        $none = self::rewriter( array( 'live_url' => '' ) );
        $this->assertSame( $html, $none->rewriteHtml( $html, '/x', 'x/index.html' ), 'neither: left as it is' );
    }

    /** A rewriter of /site that holds / and /about, with the given link settings. */
    private static function unlinker( array $options = array() )
    {
        return self::rewriter( $options + array(
            'site_path' => '/site',
            'admin_paths' => array( '/admin', '/admin4/' ),
            'pages' => function ( $page, $query ) { return $query === '' && in_array( $page, array( '/', '/about' ), true ); },
        ) );
    }

    /** LR-14 */
    public function testOutsideLinksUnlink()
    {
        $r = self::unlinker( array( 'outside_links' => 'unlink' ) );
        $html = '<p><a href="/site/user/login" class="nav-link login" target="_blank" rel="nofollow">Log in</a>'
              . ' <a href="basket">Basket</a>'
              . ' <a href=\'http://127.0.0.1:18308/site/news?page=2\' class=\'more\'>More <b>news</b></a>'
              . ' <a href="https://www.example.com/site/eng/about">English</a>'
              . ' <a href="/site/about?x=1#top">Query</a>'
              . ' <a href="/site/about#team" class="in">Team</a>'
              . ' <a href="#top">Top</a>'
              . ' <a href="https://other.example.org/site/user/login">Other</a>'
              . ' <a href="/var/site/storage/a.pdf">PDF</a>'
              . ' <img src="/site/content/view/image/1" srcset="/site/x/y 2x" />'
              . ' <form action="/site/content/search"><input name="SearchText" /></form></p>';
        $out = $r->rewriteHtml( $html, '/site/about', 'about/index.html' );
        $u = ' aria-disabled="true">';
        $this->assertStringContainsString( '<p><a class="nav-link login exp-static-unavailable"' . $u . 'Log in</a>', $out, 'a placeholder <a> keeps its classes' );
        $this->assertStringNotContainsString( 'nofollow', $out );
        $this->assertStringNotContainsString( '_blank', $out );
        $this->assertStringContainsString( ' <a class="exp-static-unavailable"' . $u . 'Basket</a> ', $out, 'relative, no class: the class alone' );
        $this->assertStringContainsString( '<a class="more exp-static-unavailable"' . $u . 'More <b>news</b></a>', $out, 'absolute with a query' );
        $this->assertStringContainsString( ' <a class="exp-static-unavailable"' . $u . 'English</a> ', $out, 'another siteaccess on the live address' );
        $this->assertStringContainsString( ' <a class="exp-static-unavailable"' . $u . 'Query</a> ', $out, 'a held page with a query is not held' );
        $this->assertStringContainsString( '<a href="./#team" class="in">Team</a>', $out, 'a held page stays a link' );
        $this->assertStringContainsString( '<a href="#top">Top</a>', $out );
        $this->assertStringContainsString( '<a href="https://other.example.org/site/user/login">Other</a>', $out, 'other hosts untouched' );
        $this->assertStringContainsString( '<a href="../var/site/storage/a.pdf">PDF</a>', $out, 'files are files' );
        $this->assertStringContainsString( 'src="https://www.example.com/site/content/view/image/1" srcset="https://www.example.com/site/x/y 2x"', $out, 'images untouched' );
        $this->assertStringContainsString( '<form action="https://www.example.com/site/content/search">', $out, 'forms are the degrader\'s' );
        $this->assertSame( 9, substr_count( $out, '</a>' ), 'every closing tag stays' );
        $this->assertStringNotContainsString( 'exp-static.css', $out, 'no style sheet without unavailable_css' );
        $counts = $r->counts();
        $this->assertSame( 5, $counts['removed_outside'] );
        $this->assertSame( 0, $counts['removed_admin'] );
        $this->assertSame( 5, $counts['unavailable'] );

        $noLive = self::unlinker( array( 'outside_links' => 'unlink', 'live_url' => '' ) );
        $this->assertSame( '<a class="exp-static-unavailable"' . $u . 'Log in</a>', $noLive->rewriteHtml( '<a href="/site/user/login">Log in</a>', '/site', 'index.html' ),
                           'without a live site too' );
        $this->assertSame( 'a<a class="x exp-static-unavailable"' . $u . 'b<a href="./">c</a>', $r->rewriteHtml( 'a<a class="x" href="/site/user">b<a href="/site/">c</a>', '/site', 'index.html' ),
                           'nested markup is left as it was' );
        $this->assertSame( '<a class="x exp-static-unavailable"' . $u . 'a<!-- c -->b</a>', $r->rewriteHtml( '<a class="x" href="/site/user">a<!-- c -->b</a>', '/site', 'index.html' ),
                           'across a comment' );
        $this->assertSame( '<a id="m" class="menu-item exp-static-unavailable" data-x="/site/user" title="Basket" role="menuitem"' . $u . 'Basket</a>',
                           $r->rewriteHtml( '<a id="m" href="/site/shop/basket" class=menu-item onclick="go()" data-x="/site/user" download ping="/p" hreflang="en" '
                                            . 'title="Basket" role="menuitem" aria-disabled="false">Basket</a>', '/site', 'index.html' ),
                           'link-only attributes and handlers go, the others stay; an unquoted class is merged' );
        $this->assertSame( '<a class="exp-static-unavailable a"' . $u . 'x</a>', $r->rewriteHtml( '<a href="/site/user" class="exp-static-unavailable a">x</a>', '/site', 'index.html' ),
                           'the class is not added twice' );

        // the style sheet: linked once at the end of the <head> of a page with a placeholder link, as any link
        $page = '<html><head><title>t</title></head><body><a href="/site/user/login">a</a><a href="/site/x">b</a></body></html>';
        $css = self::unlinker( array( 'outside_links' => 'unlink', 'unavailable_css' => expStaticLinkRewriter::UNAVAILABLE_CSS ) );
        $out = $css->rewriteHtml( $page, '/site/about', 'about/index.html' );
        $this->assertStringContainsString( '<title>t</title><link rel="stylesheet" href="../exp-static.css" />' . "\n" . '</head>', $out );
        $this->assertSame( 1, substr_count( $out, 'exp-static.css' ) );
        $based = self::unlinker( array( 'outside_links' => 'unlink', 'unavailable_css' => 'exp-static.css', 'mode' => expStaticLinkRewriter::MODE_BASE_PATH,
                                        'base_path' => '/repo/' ) );
        $this->assertStringContainsString( '<link rel="stylesheet" href="/repo/exp-static.css" />', $based->rewriteHtml( $page, '/site/about', 'about/index.html' ) );
        $plainPage = '<html><head></head><body><a href="/site/about">a</a></body></html>';
        $this->assertSame( '<html><head></head><body><a href="about/">a</a></body></html>', $css->rewriteHtml( $plainPage, '/site', 'index.html' ),
                           'a page without a placeholder link does not link it' );
        $this->assertArrayNotHasKey( 'exp-static.css', $css->foundFiles(), 'the build writes it; it is not collected from the site' );

        $sheet = expStaticLinkRewriter::unavailableStyleSheet();
        $this->assertStringContainsString( '.exp-static-unavailable{opacity:.5;cursor:not-allowed;text-decoration:none;pointer-events:none}', $sheet );
        $this->assertStringContainsString( '.exp-static-unavailable:hover', $sheet );
        $this->assertDoesNotMatchRegularExpression( '/color|\#[0-9a-f]{3}|rgb|background|font/i', $sheet, 'no color, font or background of its own' );
    }

    /** LR-15 */
    public function testAdminLinksUnlink()
    {
        $html = '<a href="/admin/content/view/full/2" class="edit">Edit</a> <a href="https://www.example.com/admin4">Admin</a>'
              . ' <a href="/administrators">People</a> <a href="/site/user/login">Log in</a>'
              . '<link rel="alternate" type="application/rss+xml" href="/site/rss/feed/news" />'
              . '<link rel="stylesheet" href="/site/user/style" /><link rel="search" href="/admin/search" />';
        $r = self::unlinker( array( 'admin_links' => 'unlink' ) );
        $out = $r->rewriteHtml( $html, '/site', 'index.html' );
        $this->assertStringStartsWith( '<a class="edit exp-static-unavailable" aria-disabled="true">Edit</a> <a class="exp-static-unavailable" aria-disabled="true">Admin</a>'
                                       . ' <a href="https://www.example.com/administrators">People</a>'
                                       . ' <a href="https://www.example.com/site/user/login">Log in</a>', $out );
        $this->assertStringContainsString( '<link rel="alternate" type="application/rss+xml" href="https://www.example.com/site/rss/feed/news" />', $out, 'outside kept' );
        $this->assertStringNotContainsString( 'rel="search"', $out, 'a navigation <link> into the administration is dropped' );
        $this->assertSame( array( 2, 1 ), array( $r->counts()['removed_admin'] - 1, $r->counts()['removed_outside'] + 1 ) );

        $both = self::unlinker( array( 'admin_links' => 'unlink', 'outside_links' => 'unlink' ) );
        $out = $both->rewriteHtml( $html, '/site', 'index.html' );
        $this->assertStringContainsString( ' <a class="exp-static-unavailable" aria-disabled="true">Log in</a>', $out );
        $this->assertStringNotContainsString( 'rel="alternate"', $out, 'a feed the copy does not hold' );
        $this->assertStringContainsString( '<link rel="stylesheet" href="https://www.example.com/site/user/style" />', $out, 'a style sheet is never dropped' );
        $this->assertSame( 3, $both->counts()['removed_admin'] );
        $this->assertSame( 3, $both->counts()['removed_outside'] );

        $root = self::unlinker( array( 'site_path' => '', 'admin_links' => 'unlink', 'pages' => null ) );
        $this->assertSame( '<a class="exp-static-unavailable" aria-disabled="true">Edit</a> <a href="about/">About</a>', $root->rewriteHtml( '<a href="/admin/x">Edit</a> <a href="/about">About</a>', '/', 'index.html' ),
                           'a site at the root: /admin is not one of its pages' );

        $this->assertTrue( $r->isAdminPath( '/admin' ) );
        $this->assertTrue( $r->isAdminPath( '/admin4/setup' ) );
        $this->assertFalse( $r->isAdminPath( '/administrators' ) );
        $this->assertFalse( $r->isAdminPath( '/site/admin' ) );

        $context = array( 'admin' => array( 'admin', 'editor', 'site' ), 'urls' => array( 'admin' => 'www.example.com/backend', 'editor' => 'edit.example.com',
                                                                                        'site' => 'www.example.com/site' ) );
        $this->assertSame( array( '/admin', '/backend', '/editor' ), expStaticBuild::adminPaths( $context, array( 'http://127.0.0.1:8080', 'https://www.example.com' ), '/site' ),
                           'the SiteURL path only on a host of the site; never the site itself' );
        $this->assertSame( array(), expStaticBuild::adminPaths( array(), array() ) );
    }

    /** LR-16 */
    public function testKeepChangesNothing()
    {
        $html = '<a href="/site/user/login" class="x">a</a><a href="/admin/x">b</a><link rel="search" href="/site/s" /><a href="/site/about">c</a>';
        $plain = self::unlinker()->rewriteHtml( $html, '/site', 'index.html' );
        $this->assertSame( $plain, self::unlinker( array( 'outside_links' => 'keep', 'admin_links' => 'keep' ) )->rewriteHtml( $html, '/site', 'index.html' ) );
        $this->assertSame( $plain, self::unlinker( array( 'outside_links' => 'bogus' ) )->rewriteHtml( $html, '/site', 'index.html' ), 'anything else is keep' );
        $this->assertStringContainsString( '<a href="https://www.example.com/admin/x">b</a>', $plain );
    }

    /** LR-17 */
    public function testSiteAtTheRootOfItsSiteUrlHost()
    {
        $r = self::unlinker( array( 'outside_links' => 'unlink', 'admin_links' => 'unlink', 'public_url' => 'https://owner.github.io/repo/',
                                    'origin_site_paths' => array( 'www.example.com' => '' ) ) );
        $html = '<link rel="canonical" href="https://www.example.com/about" />'
              . '<a href="https://www.example.com/about">root</a> <a href="https://www.example.com/site/about">prefixed</a>'
              . ' <a href="https://www.example.com/user/login">login</a> <a href="https://www.example.com/admin/x">admin</a>'
              . ' <a href="http://127.0.0.1:18308/about">crawl</a> <a href="https://www.example.com:8080/about">port</a>';
        $out = $r->rewriteHtml( $html, '/site/about', 'about/index.html' );
        $this->assertStringContainsString( '<link rel="canonical" href="https://owner.github.io/repo/about/" />', $out, 'the canonical of a page the copy holds' );
        $this->assertStringContainsString( '<a href="./">root</a> <a href="./">prefixed</a> <a class="exp-static-unavailable" aria-disabled="true">login</a> '
                                                . '<a class="exp-static-unavailable" aria-disabled="true">admin</a>', $out, 'below either path on the SiteURL host' );
        $this->assertStringContainsString( ' aria-disabled="true">crawl</a> ', $out, 'on the crawl origin the site path stays /site' );
        $this->assertStringContainsString( '<a href="https://www.example.com:8080/about">port</a>', $out, 'another port is another host' );
        $this->assertSame( array( 2, 1 ), array( $r->counts()['removed_outside'], $r->counts()['removed_admin'] ) );
    }

    /** LR-18 */
    public function testSeveralSiteAccesses()
    {
        $held = array( '/', '/about', '/bold', '/bold/team', '/bold_ger', '/bold_ger/team' );
        $options = array(
            'sites' => array( array( 'path' => '', 'prefix' => '' ), array( 'path' => '/bold', 'prefix' => '/bold' ), array( 'path' => '/bold_ger', 'prefix' => '/bold_ger' ),
                              array( 'path' => '/de', 'prefix' => '/bold_ger', 'origin' => 'www.example.de' ) ),
            'origins' => array( 'http://127.0.0.1:18308', 'www.example.com', 'www.example.de' ),
            'outside_links' => 'unlink', 'admin_links' => 'unlink', 'admin_paths' => array( '/admin' ),
            'public_url' => 'https://owner.github.io/repo/',
            'pages' => function ( $page, $query ) use ( $held ) { return $query === '' && in_array( $page, $held, true ); },
        );
        $r = self::rewriter( $options );
        $this->assertSame( '/bold_ger/team', $r->classify( '/bold_ger/team', '/' )['page'], 'the longest path first: not /bold' );
        $this->assertSame( '/bold/team', $r->classify( 'team', '/bold/' )['page'] );
        $this->assertSame( '/classic/x', $r->classify( '/classic/x', '/' )['page'], 'below the siteaccess at the root' );
        $this->assertSame( '/bold_ger/team', $r->classify( 'https://www.example.de/de/team', '/' )['page'], 'its own SiteURL host' );

        $html = '<link rel="alternate" hreflang="de" href="/bold_ger/team">'
              . '<a href="/">en</a> <a href="/bold_ger/team">de</a> <a href="http://127.0.0.1:18308/bold">bold</a> <a href="https://www.example.de/de/team">de host</a>'
              . ' <a href="/classic/x">classic</a> <a href="/admin/x">admin</a>';
        $out = $r->rewriteHtml( $html, '/bold/team', 'bold/team/index.html' );
        $this->assertStringContainsString( '<link rel="alternate" hreflang="de" href="https://owner.github.io/repo/bold_ger/team/">', $out );
        $this->assertStringContainsString( '<a href="../../">en</a> <a href="../../bold_ger/team/">de</a> <a href="../">bold</a> <a href="../../bold_ger/team/">de host</a>', $out );
        $this->assertStringContainsString( '<a class="exp-static-unavailable" aria-disabled="true">classic</a> <a class="exp-static-unavailable" aria-disabled="true">admin</a>', $out,
                                           'a siteaccess not in the export is outside it' );
        $this->assertSame( array( 1, 1 ), array( $r->counts()['removed_outside'], $r->counts()['removed_admin'] ) );

        $base = self::rewriter( array( 'mode' => 'base-path', 'public_url' => '' ) + $options );
        $this->assertStringContainsString( '<a href="/repo/bold_ger/team/">de</a>', $base->rewriteHtml( '<a href="/bold_ger/team">de</a>', '/bold/team', 'bold/team/index.html' ) );

        // the language switcher: the same page in the other siteaccess (expStaticBuild::switchedPage())
        $pages = array( '/bold/team' => array( 'siteaccess' => 'bold', 'node' => 7 ), '/bold_ger/team' => array( 'siteaccess' => 'bold_ger', 'node' => 7 ),
                        '/about' => array( 'siteaccess' => 'site', 'node' => 9 ) );
        $members = array( array( 'siteaccess' => 'site', 'folder' => '' ), array( 'siteaccess' => 'bold', 'folder' => '/bold' ),
                          array( 'siteaccess' => 'bold_ger', 'folder' => '/bold_ger' ) );
        $switch = self::rewriter( array( 'switch_language' => function ( $sa, $from ) use ( $pages, $members ) { return expStaticBuild::switchedPage( $sa, $from, $pages, $members ); } )
                                  + $options );
        $out = $switch->rewriteHtml( '<a href="/bold/switchlanguage/to/bold_ger/team">de</a> <a href="/switchlanguage/to/bold/about">bold</a>'
                                     . ' <a href="/bold/switchlanguage/to/classic/team">classic</a>', '/bold/team', 'bold/team/index.html' );
        $this->assertStringContainsString( '<a href="../../bold_ger/team/">de</a> <a href="../">bold</a>', $out, 'the same node there, else its front page' );
        $this->assertStringContainsString( 'aria-disabled="true">classic</a>', $out, 'a siteaccess not in the export: outside' );

        // all below their folders, none at the root (no DefaultSiteAccess): the root of the host is the siteaccess site's
        $none = self::rewriter( array( 'sites' => array( array( 'path' => '', 'prefix' => '/site' ), array( 'path' => '/bold', 'prefix' => '/bold' ) ) ) + $options );
        $this->assertSame( '/site/about', $none->classify( '/about', '/' )['page'] );
        $this->assertSame( '/bold', $none->classify( '/bold', '/' )['page'] );
    }
}
