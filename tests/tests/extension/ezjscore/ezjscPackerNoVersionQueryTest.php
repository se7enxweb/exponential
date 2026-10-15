<?php
/**
 * Script and stylesheet links carry no version query (owner rule, AGENTS.md: no ?v= or other cache-busting query on
 * asset links; a browser that kept an old copy is reloaded by hand). The packed files carry their time in their
 * file name, which is the packer's own mechanism.
 *
 *  PC-01 - packing off (DevelopmentMode, Packer=disabled): each file's URL is the file's own address, no ?v=
 *  PC-02 - a file URL is written as <script src> / <link href>, generated content inline
 *  PC-03 - packed files carry the newest file time in their name (AppendLastModifiedTime is on by default)
 *  PC-04 - admin4 loads its scripts and stylesheets only through ezscript/ezcss, not as plain tags
 *  PC-05 - no template, script or class of the kernel, its designs or its extensions puts a ?v= query on a link
 *
 * No database; ezjscore.ini is changed in memory.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscPackerNoVersionQueryTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezjscPackerNoVersionQueryTest extends PHPUnit\Framework\TestCase
{
    /** A ?v= that is no asset link: the query of a web service call (Google's feed API takes its API version so) */
    const NOT_AN_ASSET_LINK = array(
        'design/standard/templates/dashboard/community_activity.tpl' => 'ajax.googleapis.com/ajax/services/feed/load?v=1.0',
    );

    /** The extensions the kernel repository ships */
    const KERNEL_EXTENSIONS = array( 'expservices', 'ezformtoken', 'ezjscore', 'ezoe' );

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
    public function testUnpackedURLsAreTheFilesOwnAddresses()
    {
        $js = 'design/standard/javascript/expdebugbar.js';
        $this->assertFileExists( $js );
        $files = ezjscPacker::buildJavascriptFiles( array( 'expdebugbar.js' ) );
        $this->assertCount( 1, $files );
        $this->assertStringEndsWith( '/' . $js, $files[0] );
        $tag = ezjscPacker::buildJavascriptTag( array( 'expdebugbar.js' ) );
        $this->assertMatchesRegularExpression( '#^<script src="[^"]*/' . preg_quote( $js, '#' ) . '"></script>#', $tag );
        $this->assertStringNotContainsString( '?v=', $tag );

        $css = ezjscPacker::buildStylesheetTag( array( 'core.css' ) );
        $this->assertMatchesRegularExpression( '#^<link rel="stylesheet" href="[^"]*/stylesheets/core\.css">#', $css );
        $this->assertStringNotContainsString( '?v=', $css );
    }

    /** PC-02 */
    public function testFileURLsAndContentAreTold()
    {
        foreach ( array( '/design/x/javascript/a.js', 'https://cdn.example.com/a', '/var/cache/public/javascript/0123abcd_1792043096.js' ) as $url )
            $this->assertTrue( ezjscPacker::isFileURL( $url, '.js' ), $url );
        $this->assertTrue( ezjscPacker::isFileURL( '/design/x/stylesheets/a.css', '.css' ) );
        foreach ( array( 'var a = 1;', 'alert( "a.js?" );', '.js', '', 'a.js?v=1792043096' ) as $content )
            $this->assertFalse( ezjscPacker::isFileURL( $content, '.js' ), $content );
        $this->assertFalse( ezjscPacker::isFileURL( 'a.css', '.js' ) );
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

    /** PC-05 */
    public function testNoLinkCarriesAVersionQuery()
    {
        $found = array();
        // the kernel, its designs and the extensions of the kernel repository; an extension installed from another
        // repository (composer, a checkout) is that repository's to check
        $dirs = array( 'design', 'kernel', 'lib' );
        foreach ( self::KERNEL_EXTENSIONS as $extension )
        {
            if ( is_dir( "extension/$extension" ) )
                $dirs[] = "extension/$extension";
        }
        foreach ( $dirs as $dir )
        {
            $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $files as $file )
            {
                $path = $file->getPathname();
                if ( !preg_match( '/\.(tpl|php|js)$/', $path ) || preg_match( '#/(node_modules|tinymce8?|vendor)/|\.min\.js$|/tests?/#', $path ) )
                    continue;
                $code = (string)file_get_contents( $path );
                if ( isset( self::NOT_AN_ASSET_LINK[$path] ) )
                    $code = str_replace( self::NOT_AN_ASSET_LINK[$path], '', $code );
                if ( strpos( $code, '?v=' ) !== false )
                    $found[] = $path;
            }
        }
        $this->assertSame( array(), $found, 'asset links carry no ?v= query (owner rule)' );
    }
}
