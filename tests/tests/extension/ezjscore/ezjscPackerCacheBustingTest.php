<?php
/**
 * Script and stylesheet URLs change when the file changes, so a browser does not keep an old copy after an update
 * (the files are served with a long cache lifetime).
 *
 *  PC-01 - packing off (DevelopmentMode, Packer=disabled): each file's URL carries its time as ?v=<time>
 *  PC-02 - a URL with ?v=<time> is still written as <script src> / <link href>, generated content inline
 *  PC-03 - packed files carry the newest file time in their name (AppendLastModifiedTime is on by default)
 *  PC-04 - admin4 loads its scripts and stylesheets only through ezscript/ezcss, not as plain tags
 *
 * No database; ezjscore.ini is changed in memory.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscPackerCacheBustingTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezjscPackerCacheBustingTest extends PHPUnit\Framework\TestCase
{
    private $cwd;

    protected function setUp(): void
    {
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 4 ) );
        $ini = eZINI::instance( 'ezjscore.ini' );
        $ini->setReadOnlySettingsCheck( false );
        $ini->setVariable( 'eZJSCore', 'Packer', 'disabled' );
    }

    protected function tearDown(): void
    {
        eZINI::resetInstance( 'ezjscore.ini' );
        chdir( $this->cwd );
    }

    /** PC-01, PC-02 */
    public function testUnpackedURLsCarryTheFileTime()
    {
        $js = 'design/standard/javascript/expdebugbar.js';
        $this->assertFileExists( $js );
        $files = ezjscPacker::buildJavascriptFiles( array( 'expdebugbar.js' ) );
        $this->assertCount( 1, $files );
        $this->assertStringEndsWith( '/' . $js . '?v=' . filemtime( $js ), $files[0] );
        $tag = ezjscPacker::buildJavascriptTag( array( 'expdebugbar.js' ) );
        $this->assertMatchesRegularExpression( '#^<script src="[^"]*/' . preg_quote( $js, '#' ) . '\?v=\d+"></script>#', $tag );

        $css = ezjscPacker::buildStylesheetTag( array( 'core.css' ) );
        $this->assertMatchesRegularExpression( '#^<link rel="stylesheet" href="[^"]*/stylesheets/core\.css\?v=\d+">#', $css );
    }

    /** PC-02 */
    public function testFileURLsAndContentAreTold()
    {
        foreach ( array( '/design/x/javascript/a.js', '/design/x/javascript/a.js?v=1792043096', 'https://cdn.example.com/a',
                         '/var/cache/public/javascript/0123abcd_1792043096.js' ) as $url )
            $this->assertTrue( ezjscPacker::isFileURL( $url, '.js' ), $url );
        $this->assertTrue( ezjscPacker::isFileURL( '/design/x/stylesheets/a.css?v=1', '.css' ) );
        foreach ( array( 'var a = 1;', 'alert( "a.js?" );', '.js', '', 'a.js?v=x' ) as $content )
            $this->assertFalse( ezjscPacker::isFileURL( $content, '.js' ), $content );
        $this->assertFalse( ezjscPacker::isFileURL( 'a.css?v=1', '.js' ) );
    }

    /** PC-03 */
    public function testPackedNamesCarryTheFileTime()
    {
        $this->assertMatchesRegularExpression( '/^\[Packer\][^\[]*^AppendLastModifiedTime=enabled$/ms',
                                               (string)file_get_contents( 'extension/ezjscore/settings/ezjscore.ini' ) );
        $this->assertStringContainsString( "\$data['cache_hash'] = md5( \$data['cache_name'] . \$data['pack_level'] ) . '_' . \$data['last_modified']",
                                           (string)file_get_contents( 'extension/ezjscore/classes/ezjscpacker.php' ) );
    }

    /** PC-04 */
    public function testAdmin4HasNoPlainScriptOrStylesheetTags()
    {
        $root = 'design/admin4/templates';
        $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $files as $file )
        {
            if ( substr( $file->getFilename(), -4 ) !== '.tpl' )
                continue;
            $code = preg_replace( '/<!--.*?-->|\{\*.*?\*\}/s', '', (string)file_get_contents( $file->getPathname() ) );
            $this->assertDoesNotMatchRegularExpression( '/<script[^>]*\ssrc=|<link[^>]*rel="?stylesheet/i', $code, $file->getPathname() );
        }
    }
}
