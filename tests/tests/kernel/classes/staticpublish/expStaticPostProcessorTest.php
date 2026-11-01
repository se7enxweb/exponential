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
}
