<?php
/**
 * Generated script and stylesheet content (the output of a server function such as ezjsc::jqueryio with packing off,
 * or of one that is never cached) reaches the page as a file of its own, not as an inline <script> or <style>: a page
 * under a Content Security Policy without 'unsafe-inline' (script-src 'self', style-src 'self') would not run it
 * (doc/eng-us/features/6.0/content-security-policy.md).
 *
 *  GC-01 - ezsjc::jqueryio with packing off is a <script src> of a file that holds its code
 *  GC-02 - the file is named after the content: the same content gives the same file, other content another one
 *  GC-03 - stylesheet content the same way, as a <link>
 *  GC-04 - empty content gives no file
 *
 * No database; ezjscore.ini is changed in memory. The files written are removed again.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscPackerGeneratedContentTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezjscPackerGeneratedContentTest extends PHPUnit\Framework\TestCase
{
    private $cwd;
    private $written = array();

    /** The cluster file handler registers eZExecution's shutdown and exception handlers on first use: once, here */
    public static function setUpBeforeClass(): void
    {
        eZClusterFileHandler::instance( eZSys::cacheDirectory() . '/public/javascript/warm-up.js' );
    }

    protected function setUp(): void
    {
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 4 ) );
        $ini = eZINI::instance( 'ezjscore.ini' );
        $ini->setReadOnlySettingsCheck( false );
        $ini->setVariable( 'eZJSCore', 'Packer', 'disabled' );
        // the ezjsc server functions, as extension/ezjscore/settings/ezjscore.ini sets them up
        $ini->setVariable( 'ezjscServer_ezjsc', 'Class', 'ezjscServerFunctionsJs' );
    }

    protected function tearDown(): void
    {
        foreach ( $this->written as $file )
        {
            if ( is_file( $file ) )
                unlink( $file );
        }
        eZINI::resetInstance( 'ezjscore.ini' );
        chdir( $this->cwd );
    }

    /** The local file of a URL generatedContentURL() returned: the path from the packer's cache directory on */
    private function fileOf( $url )
    {
        $cacheDir = eZSys::cacheDirectory() . '/public/';
        $at = strpos( $url, $cacheDir );
        $this->assertNotFalse( $at, "$url is not in $cacheDir" );
        $file = substr( $url, $at );
        $this->written[] = $file;
        return $file;
    }

    /** GC-01 */
    public function testAServerFunctionWithPackingOffIsAFile()
    {
        $tag = ezjscPacker::buildJavascriptTag( array( 'ezjsc::jqueryio' ) );
        $this->assertStringNotContainsString( "<script>\r\n", $tag, 'no inline script' );
        $this->assertSame( 1, preg_match( '#^<script src="([^"]*/javascript/generated_[0-9a-f]{32}\.js)"></script>\r\n$#', $tag, $match ), $tag );
        $file = $this->fileOf( $match[1] );
        $this->assertFileExists( $file );
        $this->assertStringContainsString( "_serverUrl = _rootUrl + 'ezjscore/'", (string)file_get_contents( $file ) );
    }

    /** GC-02 */
    public function testTheFileIsNamedAfterItsContent()
    {
        $a = ezjscPacker::generatedContentURL( "var a = 1;\n", '.js' );
        $again = ezjscPacker::generatedContentURL( "var a = 1;\n", '.js' );
        $b = ezjscPacker::generatedContentURL( "var b = 2;\n", '.js' );
        $this->assertIsString( $a );
        $this->assertSame( $a, $again );
        $this->assertNotSame( $a, $b );
        $this->assertStringEndsWith( 'generated_' . md5( "var a = 1;\n" ) . '.js', $a );
        $this->assertSame( "var a = 1;\n", (string)file_get_contents( $this->fileOf( $a ) ) );
        $this->assertSame( "var b = 2;\n", (string)file_get_contents( $this->fileOf( $b ) ) );
    }

    /** GC-03 */
    public function testStylesheetContentIsAFileToo()
    {
        $url = ezjscPacker::generatedContentURL( "a { color: red; }\n", '.css' );
        $this->assertIsString( $url );
        $this->assertMatchesRegularExpression( '#/stylesheets/generated_[0-9a-f]{32}\.css$#', $url );
        $this->assertSame( "a { color: red; }\n", (string)file_get_contents( $this->fileOf( $url ) ) );
    }

    /** GC-04 */
    public function testEmptyContentGivesNoFile()
    {
        $this->assertFalse( ezjscPacker::generatedContentURL( '', '.js' ) );
        $this->assertFalse( ezjscPacker::generatedContentURL( null, '.css' ) );
    }
}
