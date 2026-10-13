<?php
/**
 * The REST request parser (ezpRestHttpRequestParser) on recorded HTTP input: each request_parser_data/*.data file
 * holds the $_SERVER, $_GET, $_POST, $_FILES, $_REQUEST and $_COOKIE of one request, and the .data.exp file next to
 * it the ezpRestRequest the parser made of it (var_export()).
 *
 *  RP-01 - every recorded request gives the recorded ezpRestRequest (GET with a parameter, HTTPS, a plain request,
 *          POST and PUT, POST with a value and with a query parameter)
 *  RP-02 - the HTTPS request's Accept headers, which carry a quality value for every entry, are listed in the order
 *          of their quality; the request is marked encrypted
 *  RP-03 - the superglobals are put back after each request
 *
 * The older regression test (ezprest_request_parser_regression.php) runs only under the old test runner; this one
 * runs in the PHPUnit suite. LoveStack gave the HTTPS request's Accept headers distinct quality values, so the order
 * no longer depends on how equal values are sorted (commit 30c1260b84), and kept the date comparison working across
 * PHP versions (commit 09e49ce1ff); Exponential carries both. The recorded request is made again from the .exp
 * file and compared as an object, because PHP 8 exports it in another order. No database.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commits 30c1260b84 and 09e49ce1ff), Copyright (C)
 * Mugo Web, GPL v2
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/private/rest/ezpRestHttpRequestParserTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group rest
 */

class ezpRestHttpRequestParserTest extends PHPUnit\Framework\TestCase
{
    /** @var array the superglobals before the test */
    private $saved = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->saved = array( $_SERVER, $_GET, $_POST, $_FILES, $_REQUEST, $_COOKIE );
    }

    protected function tearDown(): void
    {
        list( $_SERVER, $_GET, $_POST, $_FILES, $_REQUEST, $_COOKIE ) = $this->saved;
        unset( $GLOBALS['eZSysServerPort'] );
    }

    public static function requestProvider()
    {
        $cases = array();
        foreach ( glob( __DIR__ . '/request_parser_data/*.data' ) as $file )
            $cases[basename( $file, '.data' )] = array( $file );
        return $cases;
    }

    /** The parsed request of a .data file */
    private function parse( $file )
    {
        $server = $get = $post = $files = $request = $cookies = array();
        include $file;
        $_SERVER = $server;
        $_GET = $get;
        $_POST = $post;
        $_FILES = $files;
        $_REQUEST = $request;
        $_COOKIE = $cookies;
        unset( $GLOBALS['eZSysServerPort'] );
        $parser = new ezpRestHttpRequestParser();
        return $parser->createRequest();
    }

    /**
     * The request recorded in a .data.exp file, made again from its var_export() (the structs' __set_state()) in a
     * PHP file of the test's own under var/tmp, removed again.
     */
    private static function recorded( $expFile )
    {
        $file = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-rest-parser-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 ) . '.php';
        file_put_contents( $file, "<?php\nreturn " . file_get_contents( $expFile ) . ";\n" );
        try
        {
            return include $file;
        }
        finally
        {
            unlink( $file );
        }
    }

    /**
     * RP-01. The objects are compared, not the text: PHP 8 exports the properties in another order and with a
     * leading backslash, and isFatal, absent from the old recordings (null), is false now.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('requestProvider')]
    public function testRecordedRequestGivesTheRecordedResult( $file )
    {
        $this->assertFileExists( $file . '.exp' );
        $request = $this->parse( $file );
        $this->assertInstanceOf( 'ezpRestRequest', $request );
        $expected = self::recorded( $file . '.exp' );
        $this->assertInstanceOf( 'ezpRestRequest', $expected );
        $this->assertNull( $expected->isFatal );
        $this->assertFalse( $request->isFatal );
        $expected->isFatal = false;
        $this->assertEquals( $expected, $request, basename( $file ) );
    }

    /** RP-02 */
    public function testAcceptHeadersInTheOrderOfTheirQuality()
    {
        $request = $this->parse( __DIR__ . '/request_parser_data/get_with_https.data' );
        $this->assertTrue( $request->isEncrypted );
        $this->assertSame( array( 'text/html', 'application/xhtml+xml', 'application/xml', '*/*' ), $request->accept->types );
        $this->assertSame( array( 'UTF-8', '*' ), $request->accept->charsets );
        $this->assertSame( array( 'en-gb', 'en', 'en-us' ), $request->accept->languages );
        $this->assertSame( 'text/html,application/xhtml+xml;q=0.9,application/xml;q=0.8,*/*;q=0.7', $_SERVER['HTTP_ACCEPT'] );
    }

    /** RP-03 */
    public function testTheSuperglobalsArePutBack()
    {
        $before = $_SERVER;
        $this->parse( __DIR__ . '/request_parser_data/post_put.data' );
        $this->tearDown();
        $this->assertSame( $before, $_SERVER );
        $this->setUp();
    }
}
