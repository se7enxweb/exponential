<?php
/**
 * What a page of a static copy must not carry (expStaticPostProcessor): pure, no installation.
 *
 *  PP-01 the debug report and the template debug comments are taken out, the rest of the page stays
 *  PP-02 form tokens: hidden fields, csrf <meta> tags, the hidden span of a page without <head>
 *  PP-03 session ids in addresses, with the rest of the query kept
 *  PP-04 Content Security Policy nonces and a CSP <meta> that names one
 *  PP-05 refusals: a session cookie, debug output or a token left, a session id left; a clean page has none
 *  PP-06 cookie names from header lines
 *  PP-07 minify: white space and comments go; pre, textarea, script, style and conditional comments stay
 *  PP-08 precompress: a .gz of a text file of 1 KB or more when it is smaller; nothing for images or small files
 *  PP-09 fingerprint: the content hash in the name of a style sheet or script; references renamed only where they resolve to it
 *  PP-10 a PCRE failure stops the step with an exception (checked()), never as an empty page; minify keeps its
 *        placeholders on every PHP version (PHP 8.0 and 8.1 refuse a NUL byte inside a pattern)
 *  PP-11 no preg_* pattern written as a double-quoted literal in kernel/ or lib/ holds a NUL byte (a PCRE escape
 *        such as '#\\x00#' in single quotes works on every PHP version)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expStaticPostProcessorTest extends PHPUnit\Framework\TestCase
{
    /** PP-01 */
    public function testDebugOutput()
    {
        $page = "<html><body><main>Content</main>\n<div id=\"debug\"><h2><a href=\"#debug-end\">Exp Debug</a></h2><div id=\"debug-details\"><table><tr><td>x</td></tr></table></div></div>"
              . "<a id=\"debug-end\"></a><script>\n(function () { document.body.classList.add('exp-debug-bar'); })();\n</script>\n</body></html>";
        $result = expStaticPostProcessor::strip( $page );
        $this->assertSame( "<html><body><main>Content</main>\n\n</body></html>", $result['html'] );
        $this->assertSame( 1, $result['removed']['debug'] );

        $page = "<div>\n<!-- START: including template: design/x/templates/a.tpl (a.tpl) -->\n<p>A</p>\n<!-- STOP: including template: design/x/templates/a.tpl (a.tpl) -->\n</div>";
        $result = expStaticPostProcessor::strip( $page );
        $this->assertSame( "<div>\n<p>A</p>\n</div>", $result['html'] );
        $this->assertSame( 2, $result['removed']['template_comments'] );

        $page = "<html><body><p>x</p></body></html><!--exp-debug-bar--><link rel=\"stylesheet\" href=\"/design/standard/stylesheets/expdebugbar.css\">"
              . "<div id=\"debug\" class=\"exp-debug\" data-exp-debug-bar=\"2\"><h2><a href=\"#debug-end\">x</a></h2></div><script>var c = {};</script>"
              . "<script src=\"/design/standard/javascript/expdebugbar.js\" defer></script><!--/exp-debug-bar-->";
        $result = expStaticPostProcessor::strip( $page );
        $this->assertSame( '<html><body><p>x</p></body></html>', $result['html'], 'the debug bar between its markers, with its files' );
        $this->assertSame( 1, $result['removed']['debug'] );
        $this->assertSame( array( 'debug output' ), expStaticPostProcessor::refusals( '<!--exp-debug-bar--><div class="x">' ) );

        $page = '<div id="debug-panel">not the report</div><script>keep()</script>';
        $this->assertSame( $page, expStaticPostProcessor::strip( $page )['html'], 'other ids and scripts stay' );
    }

    /** PP-02 */
    public function testFormTokens()
    {
        $page = "<head>\n<meta name=\"csrf-param\" content=\"ezxform_token\" />\n<meta name=\"csrf-token\" id=\"ezxform_token_js\" title=\"abc\" content=\"abc\" />\n<title>T</title></head>"
              . "<form method=\"post\" action=\"/x\">\n<input type=\"hidden\" name=\"ezxform_token\" value=\"abc\" />\n<input name=\"q\"></form>";
        $result = expStaticPostProcessor::strip( $page );
        $this->assertSame( "<head>\n<title>T</title></head><form method=\"post\" action=\"/x\">\n<input name=\"q\"></form>", $result['html'] );
        $this->assertSame( 3, $result['removed']['tokens'] );
        $page = "<body>\n<span style='display:none;' id=\"ezxform_token_js\" title=\"abc\"></span>\n<p>x</p></body>";
        $this->assertSame( "<body>\n<p>x</p></body>", expStaticPostProcessor::strip( $page )['html'] );
        $page = '<input value="abc" type="hidden" name=\'ezxform_token\'>';
        $this->assertSame( '', expStaticPostProcessor::strip( $page )['html'], 'any attribute order and quoting' );
    }

    /** PP-03 */
    public function testSessionIds()
    {
        $page = '<a href="/a?eZSESSID=0123abcd">a</a><a href="/b?x=1&amp;eZSESSID21=ff00&amp;y=2">b</a><a href="/c?PHPSESSID=99">c</a>';
        $result = expStaticPostProcessor::strip( $page );
        $this->assertSame( '<a href="/a">a</a><a href="/b?x=1&amp;y=2">b</a><a href="/c">c</a>', $result['html'] );
        $this->assertSame( 3, $result['removed']['session_ids'] );
    }

    /** PP-04 */
    public function testNonces()
    {
        $page = "<meta http-equiv=\"Content-Security-Policy\" content=\"script-src 'nonce-r4nd0m'\">\n<script nonce=\"r4nd0m\">a()</script><style nonce='r4nd0m'>p{}</style>"
              . "<meta http-equiv=\"Content-Security-Policy\" content=\"default-src 'self'\">";
        $result = expStaticPostProcessor::strip( $page );
        $this->assertSame( "\n<script>a()</script><style>p{}</style><meta http-equiv=\"Content-Security-Policy\" content=\"default-src 'self'\">", $result['html'] );
        $this->assertSame( 2, $result['removed']['nonces'] );
        $this->assertSame( 1, $result['removed']['csp'] );
    }

    /** PP-05 */
    public function testRefusals()
    {
        $this->assertSame( array(), expStaticPostProcessor::refusals( '<p>clean</p><script>var f = "ezxform_token";</script>', array( 'lang' ) ),
                           'a script that names the field is not a token; a cookie that is no session is fine' );
        $this->assertSame( array( 'the response set the session cookie eZSESSID21' ), expStaticPostProcessor::refusals( '<p>x</p>', array( 'eZSESSID21' ) ) );
        $this->assertSame( array( 'debug output' ), expStaticPostProcessor::refusals( '<div id="debug"><h2>x</h2>' ) );
        $this->assertSame( array( 'debug output' ), expStaticPostProcessor::refusals( '<!-- START: including template: a.tpl -->' ) );
        $this->assertSame( array( 'a form token' ), expStaticPostProcessor::refusals( '<input type="hidden" name="ezxform_token" value="x">' ) );
        $this->assertSame( array( 'a form token' ), expStaticPostProcessor::refusals( '<meta name="csrf-token" content="x">' ) );
        $this->assertSame( array( 'a session id in an address' ), expStaticPostProcessor::refusals( '<a href="/x?a=1&amp;eZSESSID=1">' ) );
        $this->assertSame( array( 'the response set the session cookie MYSESS' ), expStaticPostProcessor::refusals( '', array( 'MYSESS' ), array( 'MYSESS' ) ) );

        $dirty = '<head><meta name="csrf-token" content="x"></head><a href="/a?eZSESSID=1">a</a><div id="debug">d</div><a id="debug-end"></a>';
        $this->assertSame( array(), expStaticPostProcessor::refusals( expStaticPostProcessor::strip( $dirty )['html'] ), 'strip leaves nothing to refuse' );
    }

    /** PP-06 */
    public function testCookieNames()
    {
        $this->assertSame( array( 'eZSESSID', 'lang' ), expStaticPostProcessor::cookieNames( array( 'HTTP/1.1 200 OK', 'Set-Cookie: eZSESSID=abc; path=/', 'set-cookie:lang=de',
                                                                                                  'Set-Cookie: eZSESSID=def', 'Content-Type: text/html' ) ) );
    }

    /** PP-07 */
    public function testMinify()
    {
        $html = "<html>\n  <head>  <!-- a comment -->\n<!--[if IE]><p>  old  </p><![endif]-->\n</head>\n<body>  <p>One   two</p>\n\n"
              . "<pre>  keep\n   this </pre><textarea>  a\n\n b</textarea><script>var  a = '<!-- x -->';</script><style>a  { }</style></body></html>";
        $this->assertSame( "<html>\n<head>\n<!--[if IE]><p>  old  </p><![endif]-->\n</head>\n<body> <p>One two</p>\n"
                         . "<pre>  keep\n   this </pre><textarea>  a\n\n b</textarea><script>var  a = '<!-- x -->';</script><style>a  { }</style></body></html>",
                           expStaticPostProcessor::minify( $html ) );
    }

    /** PP-08 */
    public function testPrecompress()
    {
        $css = str_repeat( "body { color: red; }\n", 100 );
        $out = expStaticPostProcessor::precompress( 'css/site.css', $css );
        $this->assertArrayHasKey( 'css/site.css.gz', $out );
        $this->assertSame( $css, gzdecode( $out['css/site.css.gz'] ) );
        $this->assertSame( function_exists( 'brotli_compress' ), isset( $out['css/site.css.br'] ) );
        $this->assertSame( array(), expStaticPostProcessor::precompress( 'a.css', 'small' ) );
        $this->assertSame( array(), expStaticPostProcessor::precompress( 'a.png', $css ) );
    }

    /** PP-09 */
    public function testFingerprint()
    {
        $this->assertSame( 'css/site.' . substr( hash( 'sha256', 'x' ), 0, 8 ) . '.css', expStaticPostProcessor::fingerprintName( 'css/site.css', 'x' ) );
        $this->assertNull( expStaticPostProcessor::fingerprintName( 'img/a.png', 'x' ) );
        $map = array( 'design/s/site.css' => 'design/s/site.1234abcd.css', 'js/app.js' => 'js/app.feedbeef.js' );
        $html = '<link href="../design/s/site.css" rel="stylesheet"><script src="../js/app.js?x=1"></script><a href="../other/site.css">o</a>'
              . '<link href="/repo/design/s/site.css"><link href="https://o.github.io/repo/js/app.js"><link href="https://cdn.example.com/js/app.js">';
        $this->assertSame( '<link href="../design/s/site.1234abcd.css" rel="stylesheet"><script src="../js/app.feedbeef.js?x=1"></script><a href="../other/site.css">o</a>'
                         . '<link href="/repo/design/s/site.1234abcd.css"><link href="https://o.github.io/repo/js/app.feedbeef.js"><link href="https://cdn.example.com/js/app.js">',
                           expStaticPostProcessor::renameReferences( $html, 'news/index.html', $map, '/repo/', 'https://o.github.io/repo/' ) );
        $this->assertSame( "@import url(site.1234abcd.css);@import 'site.1234abcd.css';",
                           expStaticPostProcessor::renameReferences( "@import url(site.css);@import 'site.css';", 'design/s/print.css', $map ) );
    }

    /** PP-10 */
    public function testPcreFailureIsAnError()
    {
        $this->assertSame( 'a', expStaticPostProcessor::checked( 'a', 'step' ) );
        $this->assertSame( '', expStaticPostProcessor::checked( '', 'step' ) );
        try
        {
            expStaticPostProcessor::checked( null, 'minify' );
            $this->fail( 'null from preg_replace() must throw' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'minify', $e->getMessage() );
        }
        $this->assertSame( "<p>a</p>\n<pre>  x  </pre>", expStaticPostProcessor::minify( "<p>a</p>\n\n<pre>  x  </pre>" ) );
    }

    /** PP-11 */
    public function testNoPatternHoldsANulByte()
    {
        $root = dirname( __DIR__, 5 );
        $found = array();
        foreach ( array( 'kernel', 'lib' ) as $dir )
        {
            $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . '/' . $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $files as $file )
            {
                if ( substr( $file->getFilename(), -4 ) !== '.php' )
                    continue;
                $code = (string)file_get_contents( $file->getPathname() );
                if ( strpos( $code, 'preg_' ) === false || ( strpos( $code, '\\x00' ) === false && strpos( $code, '\\0' ) === false && strpos( $code, "\0" ) === false ) )
                    continue;
                $tokens = token_get_all( $code );
                $n = count( $tokens );
                for ( $i = 0; $i < $n; $i++ )
                {
                    if ( !is_array( $tokens[$i] ) || $tokens[$i][0] !== T_STRING || strpos( strtolower( $tokens[$i][1] ), 'preg_' ) !== 0 )
                        continue;
                    $j = $i + 1;
                    while ( $j < $n && is_array( $tokens[$j] ) && $tokens[$j][0] === T_WHITESPACE )
                        $j++;
                    if ( $j >= $n || $tokens[$j] !== '(' )
                        continue;
                    $j++;
                    while ( $j < $n && is_array( $tokens[$j] ) && $tokens[$j][0] === T_WHITESPACE )
                        $j++;
                    if ( $j < $n && is_array( $tokens[$j] ) && $tokens[$j][0] === T_CONSTANT_ENCAPSED_STRING && $tokens[$j][1][0] === '"' )
                    {
                        // the literal as PHP reads it: a double-quoted string without variables
                        $value = stripcslashes( substr( $tokens[$j][1], 1, -1 ) );
                        if ( strpos( $value, "\0" ) !== false )
                            $found[] = substr( $file->getPathname(), strlen( $root ) + 1 ) . ':' . $tokens[$j][2];
                    }
                }
            }
        }
        $this->assertSame( array(), $found, 'a NUL byte in a pattern: PHP 8.0 and 8.1 return null (write the escape in single quotes)' );
    }
}
