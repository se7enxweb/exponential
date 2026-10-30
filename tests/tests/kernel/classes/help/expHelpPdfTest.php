<?php
/**
 * Print and PDF of a help page (doc/eng-us/features/6.0/help-system.md, "Print and PDF"), DB-less: expHelpPdf, the writer,
 * the canvas, the font subset, the view and the print hook of the viewer.
 *
 *  HP-01  a PDF is made only for a served page: untracked files, traversal, root *.md, settings and unknown ids are
 *         null (the view answers 404); a served page gives a PDF
 *  HP-02  the output starts with %PDF-, ends with %%EOF and carries the title (document information, UTF-16 when it
 *         is not ASCII) and the text
 *  HP-03  the cache key changes with the index signature, the page's text, the address and the fonts, and only then
 *  HP-04  text that looks like a font directive of the library (<b>, <i>, <c:...>) is drawn as it is
 *  HP-05  a TrueType font is embedded as a composite font and cut down to the glyphs used; the subset still reads
 *  HP-06  file names: the title as a slug, an ASCII form for the plain filename parameter
 *  HP-07  images: only the help's own asset view, never an outside address or a path out of it
 *  HP-08  right to left text in visual order, the paragraph direction
 *  HP-09  the view: registered with policy read, headers attachment + nosniff + a CSP that allows nothing
 *  HP-10  the print hook: a hidden button exphelp.js shows and handles, a PDF link, no inline style or script, print
 *         CSS for the actions and the address, the same files in design/admin and design/admin4
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpPdfTest extends PHPUnit\Framework\TestCase
{
    /** @var string the fixture installation (a git repository in the temporary directory) */
    private static $root = '';

    /** the installation this test runs in */
    private static function repo()
    {
        return dirname( __DIR__, 5 );
    }

    public static function setUpBeforeClass(): void
    {
        exec( 'git --version 2>&1', $out, $rc );
        if ( $rc !== 0 )
            return;
        self::$root = sys_get_temp_dir() . '/exphelp-pdf-' . bin2hex( random_bytes( 6 ) );
        $files = array(
            'doc/eng-us/help/start.md' => "---\ntitle: Getting started\n---\n# Getting started\n\nWelcome to the **help**.\n\n## First step\n\n| A | B |\n|---|---|\n| one | two |\n\n```php\necho '<b>not bold</b>';\n```\n",
            'doc/eng-us/help/umlaut.md' => "# Größe und Übermaß\n\nText.\n",
            'AGENTS.md' => "# private\n",
            'settings/site.ini' => "[DatabaseSettings]\nPassword=secret\n",
        );
        foreach ( $files as $path => $content )
        {
            @mkdir( dirname( self::$root . '/' . $path ), 0777, true );
            file_put_contents( self::$root . '/' . $path, $content );
        }
        foreach ( array( 'init -q', 'config user.email t@example.invalid', 'config user.name T', 'add -A', 'commit -q -m fixture' ) as $args )
            exec( 'git -C ' . escapeshellarg( self::$root ) . ' ' . $args . ' 2>&1' );
        file_put_contents( self::$root . '/doc/eng-us/help/untracked.md', "# Untracked\n" );
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$root !== '' )
            self::remove( self::$root );
        if ( method_exists( 'expHelpDocumentIndex', 'setInstance' ) )
            expHelpDocumentIndex::setInstance( null );
    }

    private static function remove( $path )
    {
        if ( is_link( $path ) || is_file( $path ) )
        {
            unlink( $path );
            return;
        }
        if ( !is_dir( $path ) )
            return;
        foreach ( scandir( $path ) as $name )
        {
            if ( $name !== '.' && $name !== '..' )
                self::remove( $path . '/' . $name );
        }
        rmdir( $path );
    }

    private static function index()
    {
        $allowlist = new expHelpAllowlist( array( 'root' => self::$root, 'roots' => array( 'doc' ), 'max_size' => 1024 * 1024 ) );
        $index = new expHelpDocumentIndex( $allowlist, array( 'root' => self::$root, 'cache_dir' => false ) );
        expHelpDocumentIndex::setInstance( $index );
        return $index;
    }

    /** a writer with the core fonts and no compression, so the text can be found in the output */
    private static function plainWriter( array $options = array() )
    {
        return new expHelpPdfWriter( $options + array( 'fonts' => array(), 'compress' => false ) );
    }

    private static function dejaVu()
    {
        foreach ( array( '/usr/share/fonts/dejavu-sans-fonts/DejaVuSans.ttf', '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf' ) as $file )
        {
            if ( is_readable( $file ) )
                return $file;
        }
        return null;
    }

    /** HP-01 */
    public function testOnlyServedPagesArePrinted()
    {
        if ( self::$root === '' )
            $this->markTestSkipped( 'git is not available' );
        $index = self::index();
        foreach ( array( '', 'help/untracked.md', '../AGENTS.md', 'AGENTS.md', '../settings/site.ini', 'help/../../AGENTS.md',
                         '/etc/passwd', 'help/missing.md', 'help/start.md/../../../settings/site.ini' ) as $id )
            $this->assertNull( expHelpPdf::forPage( $id, '', $index ), "refused: $id" );

        $pdf = expHelpPdf::forPage( 'help/start.md', 'https://www.example.com', $index );
        $this->assertIsArray( $pdf );
        $this->assertNull( $pdf['file'], 'an index without a cache directory caches nothing' );
        $this->assertStringStartsWith( '%PDF-', $pdf['data'] );
        $this->assertSame( 'getting-started.pdf', $pdf['filename'] );
        $this->assertSame( strlen( $pdf['data'] ), $pdf['size'] );
    }

    /** HP-02 */
    public function testOutputIsAPdfWithTitleAndText()
    {
        $pdf = self::plainWriter()->render( array( 'title' => 'My help page', 'summary' => 'About it', 'path' => 'doc/eng-us/help/x.md', 'changed' => 1700000000 ),
                                            '<div class="a4-help-body"><h1 id="my-help-page">My help page</h1><p>Paragraph text here.</p><ul><li>An item</li></ul></div>' );
        $this->assertStringStartsWith( '%PDF-', $pdf );
        $this->assertStringContainsString( '%%EOF', substr( $pdf, -16 ) );
        $this->assertStringContainsString( '/Title (My help page)', $pdf );
        $this->assertStringContainsString( '(Paragraph)', $pdf );
        $this->assertStringContainsString( '(doc/eng-us/help/x.md)', $pdf );

        $unicode = self::plainWriter()->render( array( 'title' => 'Größe' ), '<p>x</p>' );
        $utf16 = "\xFE\xFF" . mb_convert_encoding( 'Größe', 'UTF-16BE', 'UTF-8' );
        $this->assertStringContainsString( '/Title (' . str_replace( array( '\\', '(', ')' ), array( '\\\\', '\\(', '\\)' ), $utf16 ), $unicode );
    }

    /** HP-03 */
    public function testCacheKey()
    {
        $base = expHelpPdf::cacheKey( 'help/a.md', 'sig1', 'text', 'https://a', array( 'regular' => 'x.ttf' ), 'A4' );
        $this->assertSame( $base, expHelpPdf::cacheKey( 'help/a.md', 'sig1', 'text', 'https://a', array( 'regular' => 'x.ttf' ), 'A4' ) );
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{40}$/', $base );
        foreach ( array(
            expHelpPdf::cacheKey( 'help/a.md', 'sig2', 'text', 'https://a', array( 'regular' => 'x.ttf' ), 'A4' ),
            expHelpPdf::cacheKey( 'help/b.md', 'sig1', 'text', 'https://a', array( 'regular' => 'x.ttf' ), 'A4' ),
            expHelpPdf::cacheKey( 'help/a.md', 'sig1', 'text2', 'https://a', array( 'regular' => 'x.ttf' ), 'A4' ),
            expHelpPdf::cacheKey( 'help/a.md', 'sig1', 'text', 'https://b', array( 'regular' => 'x.ttf' ), 'A4' ),
            expHelpPdf::cacheKey( 'help/a.md', 'sig1', 'text', 'https://a', array(), 'A4' ),
            expHelpPdf::cacheKey( 'help/a.md', 'sig1', 'text', 'https://a', array( 'regular' => 'x.ttf' ), 'Letter' ),
        ) as $other )
            $this->assertNotSame( $base, $other );
    }

    /** HP-04 */
    public function testDirectivesAreText()
    {
        $pdf = self::plainWriter()->render( array( 'title' => 'T' ),
                                            '<p>Write &lt;b&gt;bold&lt;/b&gt; and &lt;c:alink:x&gt;</p><div class="a4-help-code"><pre class="a4-help-pre"><code>&lt;i&gt;x&lt;/i&gt;</code></pre></div>' );
        $this->assertStringContainsString( '<b>bold</b>', $pdf );
        $this->assertStringContainsString( '<c:alink:x>', $pdf );
        $this->assertStringContainsString( '<i>x</i>', $pdf );
    }

    /** HP-05 */
    public function testTrueTypeEmbeddedAndSubset()
    {
        $font = self::dejaVu();
        if ( $font === null )
            $this->markTestSkipped( 'no DejaVu Sans on this system' );
        $pdf = ( new expHelpPdfWriter( array( 'fonts' => array( 'regular' => $font ) ) ) )
            ->render( array( 'title' => 'Ελληνικά' ), '<p>Größe Русский עברית</p>' );
        $this->assertStringStartsWith( '%PDF-', $pdf );
        $this->assertStringContainsString( '/Subtype /Type0', $pdf );
        $this->assertStringContainsString( '/ToUnicode', $pdf );
        $this->assertLessThan( 150000, strlen( $pdf ), 'the font is cut down to the glyphs used' );

        $data = (string)file_get_contents( $font );
        $ttf = new eZPDFTrueTypeFont( $font );
        $glyphs = array( $ttf->glyphIndex( ord( 'A' ) ), $ttf->glyphIndex( 0x00F6 ) );
        $subset = expHelpPdfFontSubset::subset( $data, $glyphs );
        $this->assertLessThan( strlen( $data ) / 4, strlen( $subset ) );
        $file = tempnam( sys_get_temp_dir(), 'ttf' );
        file_put_contents( $file, $subset );
        $read = new eZPDFTrueTypeFont( $file );
        unlink( $file );
        $this->assertSame( $glyphs[0], $read->glyphIndex( ord( 'A' ) ) );
        $this->assertSame( $ttf->glyphWidth( $glyphs[1] ), $read->glyphWidth( $glyphs[1] ) );
        $this->assertSame( 'not a font', expHelpPdfFontSubset::subset( 'not a font', array( 1 ) ) );
    }

    /** HP-06 */
    public function testFilenames()
    {
        $this->assertSame( 'getting-started.pdf', expHelpPdf::filename( array( 'title' => 'Getting started' ), 'help/start.md' ) );
        $this->assertSame( 'start.pdf', expHelpPdf::filename( array( 'title' => '' ), 'help/start.md' ) );
        $this->assertSame( 'größe.pdf', expHelpPdf::filename( array( 'title' => 'Größe' ), 'x.md' ) );
        $this->assertMatchesRegularExpression( '/^[A-Za-z0-9._-]+\.pdf$/', expHelpPdf::asciiFilename( 'größe "x".pdf' ) );
        $this->assertSame( 'help.pdf', expHelpPdf::asciiFilename( 'עברית.pdf' ) );
        $this->assertStringNotContainsString( '"', expHelpPdf::asciiFilename( 'a"b.pdf' ) );
    }

    /** HP-07 */
    public function testImagesOnlyFromTheHelp()
    {
        if ( self::$root === '' )
            $this->markTestSkipped( 'git is not available' );
        $index = self::index();
        foreach ( array( 'https://evil.example/exphelp/asset/help/x.png', 'file:///etc/passwd', '/exphelp/asset/../../settings/site.ini',
                         '/exphelp/asset/%2e%2e/AGENTS.md', '/somewhere/else.png', '' ) as $src )
            $this->assertNull( expHelpPdf::imageFile( $index, $src ), $src );
        $this->assertNull( expHelpPdfWriter::loadImage( '/etc/passwd' ) );
    }

    /** HP-08 */
    public function testRightToLeft()
    {
        $this->assertSame( 'םולש', expHelpPdfWriter::visual( 'שלום' ) );
        $this->assertSame( 'plain', expHelpPdfWriter::visual( 'plain' ) );
        $this->assertSame( 'rtl', expHelpPdfWriter::direction( array( array( 'שלום world', array() ) ) ) );
        $this->assertSame( 'ltr', expHelpPdfWriter::direction( array( array( '123 hello שלום', array() ) ) ) );
        $line = array( array( 'text' => 'Hello', 'style' => array(), 'w' => 1 ), array( 'text' => ' ', 'style' => array(), 'w' => 1 ),
                       array( 'text' => 'אב', 'style' => array(), 'w' => 1 ), array( 'text' => ' ', 'style' => array(), 'w' => 1 ),
                       array( 'text' => 'גד', 'style' => array(), 'w' => 1 ) );
        $texts = array_map( function ( $item ) { return $item['text']; }, expHelpPdfWriter::reorder( $line, false ) );
        $this->assertSame( array( 'Hello', ' ', 'דג', ' ', 'בא' ), $texts );
    }

    /** HP-09 */
    public function testView()
    {
        $Module = $ViewList = $FunctionList = null;
        include self::repo() . '/kernel/exphelp/module.php';
        $this->assertArrayHasKey( 'pdf', $ViewList );
        $this->assertSame( array( 'read' ), $ViewList['pdf']['functions'] );
        $this->assertSame( 'pdf.php', $ViewList['pdf']['script'] );
        $this->assertFileExists( self::repo() . '/kernel/exphelp/pdf.php' );

        require_once self::repo() . '/kernel/private/classes/views/exphelp/pdf.php';
        $headers = implode( "\n", \Exponential\View\Kernel\Exphelp\Pdf::headers( 'größe.pdf', 123 ) );
        $this->assertStringContainsString( 'Content-Type: application/pdf', $headers );
        $this->assertStringContainsString( 'Content-Disposition: attachment; filename="', $headers );
        $this->assertStringContainsString( "filename*=UTF-8''gr%C3%B6%C3%9Fe.pdf", $headers );
        $this->assertStringContainsString( 'X-Content-Type-Options: nosniff', $headers );
        $this->assertStringContainsString( "Content-Security-Policy: default-src 'none'", $headers );
        $this->assertStringContainsString( 'Content-Length: 123', $headers );
    }

    /** HP-10 */
    public function testPrintHook()
    {
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $dir = self::repo() . "/design/$design";
            $part = (string)file_get_contents( "$dir/templates/exphelp/parts/viewer_actions.tpl" );
            $this->assertMatchesRegularExpression( '/<button[^>]*data-exphelp-print[^>]*hidden/', $part );
            $this->assertStringContainsString( "'exphelp/pdf/'", $part );
            $this->assertStringContainsString( 'a4-help-print-url', $part );
            $this->assertDoesNotMatchRegularExpression( '/<style|style="|<script|\son[a-z]+=/i', $part );
            $this->assertStringContainsString( "design:exphelp/parts/viewer_actions.tpl", (string)file_get_contents( "$dir/templates/exphelp/viewer.tpl" ) );

            $js = (string)file_get_contents( "$dir/javascript/exphelp.js" );
            $this->assertStringContainsString( '[data-exphelp-print]', $js );
            $this->assertStringContainsString( 'window.print()', $js );

            $css = (string)file_get_contents( "$dir/stylesheets/modules/help.css" );
            $this->assertStringContainsString( '.a4-help-print-url { display: none; }', $css );
            $this->assertMatchesRegularExpression( '/@media print \{[^@]*\.a4-help-actions[^@]*display: none/s', $css );
        }
        $this->assertFileEquals( self::repo() . '/design/admin4/templates/exphelp/parts/viewer_actions.tpl',
                                 self::repo() . '/design/admin/templates/exphelp/parts/viewer_actions.tpl' );
    }
}

?>
