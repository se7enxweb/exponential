<?php
/**
 * The image analyzers of lib/ezimage, which tell the image manager whether a GIF is animated:
 *   - eZGIFImageAnalyzer::process() on GIF streams built byte by byte: version, size, global and local colour
 *     tables, frame count and is_animated, the delay of the graphic control extension, transparency, comments,
 *     the NETSCAPE application extension, the plain text extension (skipped, the frames after it still count),
 *     a GIF87a file, a GIF written by GD, a file that is not a GIF, a truncated file
 *   - eZImageAnalyzer: the analyzers configured in image.ini by MIME type (createForMIME), create() by handler
 *     name, the base analyzer, eZEXIFImageAnalyzer on a JPEG without EXIF data
 *
 * No database. Image files are written to a private directory under var/tmp that tearDown() removes.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezimage
 */

class eZImageAnalyzerTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $savedGlobal;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/phpunit-ezimageanalyzer-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir, 0777, true );
        $this->savedGlobal = array_key_exists( 'eZImageAnalyzer', $GLOBALS ) ? array( $GLOBALS['eZImageAnalyzer'] ) : null;
        unset( $GLOBALS['eZImageAnalyzer'] );
    }

    protected function tearDown(): void
    {
        foreach ( glob( $this->dir . '/*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
        unset( $GLOBALS['eZImageAnalyzer'] );
        if ( $this->savedGlobal !== null )
            $GLOBALS['eZImageAnalyzer'] = $this->savedGlobal[0];
    }

    private static function le16( $n )
    {
        return chr( $n & 0xff ) . chr( ( $n >> 8 ) & 0xff );
    }

    private static function subBlocks( $data )
    {
        $out = '';
        foreach ( str_split( $data, 255 ) as $chunk )
            $out .= chr( strlen( $chunk ) ) . $chunk;
        return $out . "\0";
    }

    /**
     * A GIF stream: frames are arrays of 'delay', 'transparent', 'local' (local colour table bits) entries.
     */
    private static function gif( $width, $height, $frames, $options = array() )
    {
        $version = $options['version'] ?? '89a';
        $globalBits = $options['global_bits'] ?? 1; // 2^(bits+1) colours
        $gif = 'GIF' . $version . self::le16( $width ) . self::le16( $height );
        $gif .= chr( 0x80 | $globalBits ) . "\0\0";
        $gif .= str_repeat( "\0\0\0", 1 << ( $globalBits + 1 ) );
        if ( !empty( $options['netscape'] ) )
            $gif .= "\x21\xff\x0bNETSCAPE2.0\x03\x01\0\0\0";
        if ( isset( $options['comment'] ) )
            $gif .= "\x21\xfe" . self::subBlocks( $options['comment'] );
        if ( isset( $options['plain_text'] ) )
            $gif .= "\x21\x01" . "\x0c" . str_repeat( "\0", 12 ) . self::subBlocks( $options['plain_text'] );
        foreach ( $frames as $frame )
        {
            if ( isset( $frame['delay'] ) )
                $gif .= "\x21\xf9\x04" . chr( !empty( $frame['transparent'] ) ? 1 : 0 ) . self::le16( $frame['delay'] ) . "\0\0";
            $local = $frame['local'] ?? null;
            $gif .= "\x2c" . self::le16( 0 ) . self::le16( 0 ) . self::le16( $width ) . self::le16( $height );
            $gif .= chr( $local === null ? 0 : 0x80 | $local );
            if ( $local !== null )
                $gif .= str_repeat( "\0\0\0", 1 << ( $local + 1 ) );
            $gif .= "\x02" . self::subBlocks( "\x4c\x01" );
        }
        return $gif . "\x3b";
    }

    private function analyze( $bytes, $name = 'image.gif' )
    {
        $file = $this->dir . '/' . $name;
        file_put_contents( $file, $bytes );
        $analyzer = new eZGIFImageAnalyzer();
        return $analyzer->process( array( 'url' => $file ) );
    }

    public function testSingleFrame()
    {
        $info = $this->analyze( self::gif( 300, 2, array( array() ) ) );
        $this->assertSame( '89a', $info['version'] );
        $this->assertSame( 300, $info['width'] );
        $this->assertSame( 2, $info['height'] );
        $this->assertSame( 4, $info['color_count'] );
        $this->assertSame( 1, $info['frame_count'] );
        $this->assertFalse( $info['is_animated'] );
        $this->assertSame( eZImageAnalyzer::MODE_INDEXED, $info['mode'] );
        $this->assertSame( eZImageAnalyzer::TRANSPARENCY_OPAQUE, $info['transparency_type'] );
        $this->assertFalse( $info['animation_timer'] );
        $this->assertSame( array(), $info['comment_list'] );
    }

    public function testAnimation()
    {
        $frames = array( array( 'delay' => 10 ), array( 'delay' => 25, 'transparent' => true ), array( 'delay' => 25 ) );
        $info = $this->analyze( self::gif( 16, 16, $frames, array( 'netscape' => true ) ) );
        $this->assertSame( 3, $info['frame_count'] );
        $this->assertTrue( $info['is_animated'] );
        $this->assertSame( 25, $info['animation_timer'] );
        $this->assertSame( eZImageAnalyzer::TIMER_HUNDRETHS_OF_A_SECOND, $info['animation_timer_type'] );
        $this->assertSame( eZImageAnalyzer::TRANSPARENCY_TRANSPARENT, $info['transparency_type'] );
    }

    public function testCommentsAndLocalColourTables()
    {
        $comment = str_repeat( 'long comment ', 30 ); // more than one sub-block
        $info = $this->analyze( self::gif( 8, 8, array( array( 'local' => 7 ), array( 'local' => 2 ) ), array( 'comment' => $comment ) ) );
        $this->assertSame( array( $comment ), $info['comment_list'] );
        $this->assertSame( 256, $info['color_count'], 'the largest colour table' );
        $this->assertSame( 2, $info['frame_count'] );
    }

    /**
     * The plain text extension of GIF89a is skipped like any other extension; the analysis used to stop at it, so
     * the frames after it were not counted and an animation was taken for a still image.
     */
    public function testFramesAfterAPlainTextExtensionCount()
    {
        $info = $this->analyze( self::gif( 8, 8, array( array( 'delay' => 5 ), array( 'delay' => 5 ) ), array( 'plain_text' => 'Hello' ) ) );
        $this->assertSame( 2, $info['frame_count'] );
        $this->assertTrue( $info['is_animated'] );
    }

    public function testGif87a()
    {
        $info = $this->analyze( self::gif( 1, 1, array( array() ), array( 'version' => '87a', 'global_bits' => 0 ) ) );
        $this->assertSame( '87a', $info['version'] );
        $this->assertSame( 2, $info['color_count'] );
    }

    public function testGifWrittenByGd()
    {
        if ( !function_exists( 'imagegif' ) )
            $this->markTestSkipped( 'GD with GIF support is not available' );
        $image = imagecreate( 40, 30 );
        imagecolorallocate( $image, 255, 0, 0 );
        imagecolortransparent( $image, imagecolorallocate( $image, 0, 0, 0 ) );
        $file = $this->dir . '/gd.gif';
        imagegif( $image, $file );
        $analyzer = new eZGIFImageAnalyzer();
        $info = $analyzer->process( array( 'url' => $file ) );
        $this->assertSame( 40, $info['width'] );
        $this->assertSame( 30, $info['height'] );
        $this->assertSame( 1, $info['frame_count'] );
        $this->assertFalse( $info['is_animated'] );
        $this->assertSame( eZImageAnalyzer::TRANSPARENCY_TRANSPARENT, $info['transparency_type'] );
    }

    public function testTruncatedFileStops()
    {
        $bytes = self::gif( 8, 8, array( array( 'delay' => 5 ), array( 'delay' => 5 ) ) );
        $info = $this->analyze( substr( $bytes, 0, strlen( $bytes ) - 8 ) );
        $this->assertGreaterThanOrEqual( 1, $info['frame_count'] );
    }

    public function testNotAGif()
    {
        $info = @$this->analyze( "\x89PNG\r\n\x1a\n" . str_repeat( "\0", 20 ), 'x.png' );
        $this->assertIsArray( $info );
        $this->assertFalse( $info['is_animated'] );
        $this->assertFalse( @( new eZGIFImageAnalyzer() )->process( array( 'url' => $this->dir . '/missing.gif' ) ) );
    }

    public function testAnalyzersByMimeType()
    {
        eZImageAnalyzer::readAnalyzerSettingsFromINI();
        $this->assertInstanceOf( eZGIFImageAnalyzer::class, eZImageAnalyzer::createForMIME( array( 'name' => 'image/gif' ) ) );
        $this->assertInstanceOf( eZEXIFImageAnalyzer::class, eZImageAnalyzer::createForMIME( array( 'name' => 'image/jpeg' ) ) );
        $this->assertFalse( eZImageAnalyzer::createForMIME( array( 'name' => 'image/png' ) ) );
        $this->assertInstanceOf( eZGIFImageAnalyzer::class, eZImageAnalyzer::create( 'ezgif' ) );
        $this->assertFalse( @eZImageAnalyzer::create( 'nosuchanalyzer' ) );
        $this->assertFalse( ( new eZImageAnalyzer() )->process( array( 'url' => 'x' ) ) );
    }

    public function testExifAnalyzerOnAJpegWithoutExif()
    {
        if ( !function_exists( 'imagejpeg' ) )
            $this->markTestSkipped( 'GD with JPEG support is not available' );
        $file = $this->dir . '/plain.jpg';
        imagejpeg( imagecreatetruecolor( 4, 4 ), $file );
        $info = ( new eZEXIFImageAnalyzer() )->process( array( 'url' => $file ) );
        $this->assertTrue( $info === false || is_array( $info ) );
    }
}
