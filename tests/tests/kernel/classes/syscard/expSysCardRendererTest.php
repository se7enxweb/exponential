<?php
/**
 * The look of the system card: color depths, themes, the logo and the layout at 40, 80 and 120 columns, plain text and
 * JSON. Facts are made up; nothing is read from the machine.
 *
 *  SCV-01 - The color depth: NO_COLOR and no terminal give none; COLORTERM truecolor; TERM *256color; else 16
 *  SCV-02 - A color at each depth: 38;2;R;G;B, 38;5;N (cube and gray ramp), 3N/9N; a theme's <Role>16 wins at 16 colors
 *  SCV-03 - Gradients across columns and lines; mono has none and paints nothing but bold
 *  SCV-04 - The layout: stacked at 40, the big logo at 80, the big logo and the wordmark at 120; no line wider than the width
 *  SCV-05 - Without colors there is not one escape code; with colors every line resets; the palette rows and the gradient bar
 *  SCV-06 - Markers and hints: dots or ASCII signs; the Hints block lists errors first
 *  SCV-07 - Plain text: no logo, no codes, ASCII, "Label: value" with [warn]/[error]
 *  SCV-08 - JSON: valid, grouped by section, with hints and runs, and no secret
 *  SCV-09 - Long values are cut with an ellipsis, paragraphs are wrapped
 *  SCV-10 - The shipped logos: three sizes, Unicode and ASCII, and the wordmark
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expsyscardtestini.php';

class expSysCardRendererTest extends PHPUnit\Framework\TestCase
{
    private function logoDir()
    {
        return realpath( __DIR__ . '/../../../../../share/syscard/logos' );
    }

    private function theme( $name = 'rainbow' )
    {
        $settings = expSysCardTestIni::read( expSysCardTestIni::file() );
        return new expSysCardTheme( $name, isset( $settings['Theme-' . $name] ) ? $settings['Theme-' . $name] : array() );
    }

    private function facts()
    {
        $facts = array(
            new expSysCardFact( 'exponential.version', 'Version', 'Exponential 6.0.15 (stable, 6.0 line)', 'info', 'exponential' ),
            new expSysCardFact( 'host.os', 'OS', 'AlmaLinux 9.8 (Olive Jaguar)', 'info', 'system' ),
            new expSysCardFact( 'host.disk', 'Disk', '1.9 TiB / 2.0 TiB (97%)', 'error', 'system', 'The disk of the installation is 97% full.' ),
            new expSysCardFact( 'php.version', 'PHP', '8.5.11 (cli)', 'ok', 'php' ),
            new expSysCardFact( 'php.opcache', 'OPcache', 'off', 'warn', 'php', 'OPcache is off.' ),
            new expSysCardFact( 'php.extensions', 'Extensions', str_repeat( 'intl mbstring curl gd xsl sodium apcu ', 4 ), 'info', 'php' ),
        );
        $text = expSysCardFact::text( 'about.what', 'Exponential is a professional PHP application framework with advanced CMS functionality.', 'about', 'What Exponential is' );
        $facts[] = $text;
        return $facts;
    }

    private function renderer( $width, $depth = expSysCardColor::NONE, $unicode = true, $theme = 'rainbow' )
    {
        $renderer = new expSysCardRenderer( $this->theme( $theme ) );
        $renderer->width = $width;
        $renderer->depth = $depth;
        $renderer->unicode = $unicode;
        $renderer->logo = new expSysCardLogo( array( $this->logoDir() ), 'e', $unicode );
        return $renderer;
    }

    public function testDepthDetection()
    {
        $this->assertSame( 'none', expSysCardColor::detect( array( 'NO_COLOR' => '1', 'COLORTERM' => 'truecolor', 'TERM' => 'xterm-256color' ), true ) );
        $this->assertSame( 'none', expSysCardColor::detect( array( 'COLORTERM' => 'truecolor' ), false ), 'not a terminal' );
        $this->assertSame( 'none', expSysCardColor::detect( array( 'TERM' => 'dumb' ), true ) );
        $this->assertSame( 'truecolor', expSysCardColor::detect( array( 'COLORTERM' => '24bit', 'TERM' => 'xterm' ), true ) );
        $this->assertSame( 'truecolor', expSysCardColor::detect( array( 'NO_COLOR' => '', 'COLORTERM' => 'truecolor' ), true ), 'an empty NO_COLOR does not count' );
        $this->assertSame( '256', expSysCardColor::detect( array( 'TERM' => 'screen-256color' ), true ) );
        $this->assertSame( '16', expSysCardColor::detect( array( 'TERM' => 'xterm' ), true ) );
        $this->assertSame( 'none', expSysCardColor::detect( array(), true ) );
        $this->assertSame( '256', expSysCardColor::parse( '256' ) );
        $this->assertSame( 'none', expSysCardColor::parse( 'never' ) );
        $this->assertNull( expSysCardColor::parse( 'auto' ) );
    }

    public function testColorCodes()
    {
        $this->assertSame( '38;2;242;140;40', expSysCardColor::code( '#F28C28', 'truecolor' ) );
        $this->assertSame( '48;2;242;140;40', expSysCardColor::code( '#F28C28', 'truecolor', true ) );
        $this->assertSame( '38;5;208', expSysCardColor::code( '#F28C28', '256' ) );
        $this->assertSame( '38;5;196', expSysCardColor::code( '#FF0000', '256' ) );
        $this->assertSame( '38;5;244', expSysCardColor::code( '#808080', '256' ), 'a gray takes the gray ramp' );
        $this->assertSame( '91', expSysCardColor::code( '#FF0000', '16' ) );
        $this->assertSame( '30', expSysCardColor::code( '#000000', '16' ) );
        $this->assertSame( '', expSysCardColor::code( '#F28C28', 'none' ) );
        $this->assertSame( '', expSysCardColor::code( '', 'truecolor' ), 'empty: the terminal\'s own color' );
        $this->assertSame( "\033[1;38;5;208mx\033[0m", expSysCardColor::paint( 'x', '#F28C28', '256', true ) );
        $this->assertSame( 'x', expSysCardColor::paint( 'x', '#F28C28', 'none', true ) );

        $theme = $this->theme( 'rainbow' );
        $this->assertSame( '93', $theme->code( 'Logo', '16' ), 'Logo16=bright-yellow: orange is not drawn red' );
        $this->assertSame( '38;2;242;140;40', $theme->code( 'Logo', 'truecolor' ) );
        $this->assertSame( "\033[2mdim\033[0m", $this->theme( 'mono' )->paint( 'dim', 'Dim', '256' ), 'no color: dim as an attribute' );
        $this->assertSame( 15, expSysCardTheme::basicIndex( 'bright-white' ) );
        $this->assertNull( expSysCardTheme::basicIndex( 'orange' ) );
    }

    public function testGradients()
    {
        $this->assertSame( '#ff0000', expSysCardColor::gradient( array( '#FF0000', '#0000FF' ), 0 ) );
        $this->assertSame( '#0000ff', expSysCardColor::gradient( array( '#FF0000', '#0000FF' ), 1 ) );
        $this->assertSame( '#800080', expSysCardColor::gradient( array( '#FF0000', '#0000FF' ), 0.5 ) );
        $line = expSysCardColor::paintGradient( 'AB CD', array( '#FF0000', '#0000FF' ), 'truecolor' );
        $this->assertSame( 'AB CD', expSysCardColor::strip( $line ) );
        $this->assertStringStartsWith( "\033[38;2;255;0;0mA", $line );
        $this->assertStringEndsWith( "\033[38;2;0;0;255mD\033[0m", $line );

        $lines = new expSysCardTheme( 'x', array( 'Gradient' => array( '#FF0000', '#0000FF' ), 'GradientDirection' => 'lines' ) );
        $painted = $lines->paintBlock( array( 'aaa', 'bbb' ), 'truecolor' );
        $this->assertSame( "\033[38;2;255;0;0maaa\033[0m", $painted[0] );
        $this->assertSame( "\033[38;2;0;0;255mbbb\033[0m", $painted[1] );
        $this->assertSame( array(), $this->theme( 'mono' )->gradient() );
        $this->assertSame( "\033[1mEXP\033[0m", $this->theme( 'mono' )->paintBlock( array( 'EXP' ), 'truecolor' )[0] );
    }

    public function testLayoutAtThreeWidths()
    {
        foreach ( array( 40 => array( true, 'mini', false ), 80 => array( false, 'big', false ), 120 => array( false, 'big', true ) ) as $width => $expected )
        {
            $renderer = $this->renderer( $width );
            $layout = $renderer->layout();
            $this->assertSame( $expected[0], $layout['stacked'], "stacked at $width" );
            $this->assertSame( $expected[1], $layout['size'], "logo size at $width" );
            $this->assertSame( $expected[2], $layout['wordmark'], "wordmark at $width" );
            $text = $renderer->text( $this->facts(), array( 'title' => 'Exponential 6.0.15', 'subtitle' => 'web.example.org' ) );
            foreach ( explode( "\n", $text ) as $line )
                $this->assertLessThanOrEqual( $width, expSysCardColor::width( $line ), "line wider than $width: $line" );
            $this->assertStringContainsString( 'Exponential 6.0.15 @ web.example.org', $text );
            $this->assertStringContainsString( 'Hints', $text );
        }
        $stacked = $this->renderer( 40 )->text( $this->facts(), array( 'title' => 'Exponential' ) );
        $this->assertStringStartsWith( "▄▀▀▀▄\n", $stacked, 'stacked: the mini logo on top' );
        $wide = $this->renderer( 120 )->text( $this->facts(), array( 'title' => 'Exponential' ) );
        $this->assertStringContainsString( '█▀▀ ▀▄▀ █▀█', $wide, 'the wordmark at 120' );
        $this->assertSame( 21, $this->renderer( 60 )->layout()['logoWidth'], 'the big logo is 21 columns wide' );
        $this->assertSame( 'big', $this->renderer( 60 )->layout()['size'] );
        $this->assertSame( 'small', $this->renderer( 55 )->layout()['size'] );
        $this->assertSame( 'mini', $this->renderer( 45 )->layout()['size'] );
        $this->assertFalse( $this->renderer( 45 )->layout()['stacked'] );
        $noLogo = $this->renderer( 80 );
        $noLogo->logo = null;
        $this->assertStringStartsWith( 'Exponential', $noLogo->text( $this->facts(), array( 'title' => 'Exponential' ) ) );
    }

    public function testEscapeCodesByDepth()
    {
        $meta = array( 'title' => 'Exponential' );
        $none = $this->renderer( 80 )->text( $this->facts(), $meta );
        $this->assertStringNotContainsString( "\033", $none, 'no color: no escape code at all (NO_COLOR, a pipe)' );

        foreach ( array( 'truecolor' => '38;2;', '256' => '38;5;', '16' => '[1;93m' ) as $depth => $marker )
        {
            $depth = (string)$depth; // PHP turns the keys '256' and '16' into integers
            $text = $this->renderer( 120, $depth )->text( $this->facts(), $meta );
            $this->assertStringContainsString( $marker, $text, "codes of $depth" );
            $this->assertStringContainsString( "\033[41m", $text, 'the palette rows' );
            $this->assertStringContainsString( "\033[101m", $text );
            $this->assertStringContainsString( '▀', $text, 'the gradient bar' );
            foreach ( explode( "\n", $text ) as $line )
                if ( preg_match_all( '/\033\[[0-9;]*m/', $line, $codes ) )
                    $this->assertSame( "\033[0m", end( $codes[0] ), "no color is left on at the end of a line ($depth)" );
            if ( $depth !== 'truecolor' )
                $this->assertStringNotContainsString( '38;2;', $text, "no truecolor codes at $depth" );
        }
        $bare = $this->renderer( 80, '256' );
        $bare->colorBars = false;
        $this->assertStringNotContainsString( "\033[41m", $bare->text( $this->facts(), $meta ) );
    }

    public function testMarkersAndHints()
    {
        $unicode = $this->renderer( 100 )->text( $this->facts(), array() );
        $this->assertStringContainsString( '✖ Disk', $unicode );
        $this->assertStringContainsString( '▲ OPcache', $unicode );
        $this->assertStringContainsString( '● PHP', $unicode );
        $this->assertStringContainsString( '· OS', $unicode );
        $ascii = $this->renderer( 100, 'none', false )->text( $this->facts(), array() );
        $this->assertStringContainsString( 'x Disk', $ascii );
        $this->assertStringContainsString( '! OPcache', $ascii );
        $this->assertSame( 0, preg_match( '/[^\x00-\x7F]/', $ascii ), 'ASCII only' );
        $hints = expSysCardRenderer::hintsOf( array_reverse( $this->facts() ) );
        $this->assertSame( array( 'host.disk', 'php.opcache' ), array_map( function ( $f ) { return $f->key; }, $hints ), 'errors first' );
        $renderer = $this->renderer( 100 );
        $renderer->hints = false;
        $this->assertStringNotContainsString( 'Hints', $renderer->text( $this->facts(), array() ) );
    }

    public function testPlain()
    {
        $plain = $this->renderer( 80, 'truecolor' )->plain( $this->facts(), array( 'title' => 'Exponential 6.0.15', 'subtitle' => 'web.example.org' ) );
        $this->assertStringNotContainsString( "\033", $plain );
        $this->assertStringStartsWith( "Exponential 6.0.15 @ web.example.org\n====", $plain );
        $this->assertStringContainsString( "[System]\nOS: AlmaLinux 9.8 (Olive Jaguar)\nDisk: 1.9 TiB / 2.0 TiB (97%) [error]\n", $plain );
        $this->assertStringContainsString( 'OPcache: off [warn]', $plain );
        $this->assertStringContainsString( "[Hints]\nerror: The disk of the installation is 97% full.\nwarn: OPcache is off.\n", $plain );
        $this->assertStringContainsString( "What Exponential is:\nExponential is a professional", $plain );
        $this->assertStringNotContainsString( '▀', $plain );
    }

    public function testJson()
    {
        $facts = $this->facts();
        $facts[] = new expSysCardFact( 'x.y', 'Leak', 'mysql://root:pw@db/x' );
        $registry = new expSysCardRegistry();
        foreach ( $facts as $i => $fact )
            $facts[$i] = expSysCardRegistry::safe( $fact );
        $json = $this->renderer( 80 )->json( $facts, array( array( 'id' => 'host', 'class' => 'expSysCardHostProvider', 'status' => 'ok', 'seconds' => 0.01 ) ),
                                             array( 'card' => 'info', 'generated' => '2026-10-20T08:00:00+00:00', 'title' => 'Exponential 6.0.15', 'subtitle' => 'web.example.org' ) );
        $data = json_decode( $json, true );
        $this->assertIsArray( $data, 'valid JSON' );
        $this->assertSame( 'info', $data['card'] );
        $this->assertSame( 'web.example.org', $data['host'] );
        $this->assertSame( array( 'exponential', 'system', 'php', 'about' ), array_keys( $data['sections'] ) );
        $this->assertSame( 'OS', $data['sections']['system'][0]['label'] );
        $this->assertSame( 'error', $data['sections']['system'][1]['severity'] );
        $this->assertSame( 'host.disk', $data['hints'][0]['key'] );
        $this->assertSame( 'expSysCardHostProvider', $data['providers'][0]['class'] );
        $this->assertStringNotContainsString( ':pw@', $json );
        $this->assertStringNotContainsString( "\033", $json );
        $this->assertSame( '{}', json_encode( json_decode( $this->renderer( 80 )->json( array() ) )->sections ) );
    }

    public function testCutAndWrap()
    {
        $this->assertSame( 'abcdefghi…', expSysCardRenderer::cut( 'abcdefghijklmnop', 10 ) );
        $this->assertSame( 'abcdefg...', expSysCardRenderer::cut( 'abcdefghijklmnop', 10, false ) );
        $this->assertSame( 'short', expSysCardRenderer::cut( 'short', 10 ) );
        $this->assertSame( array( 'one two', 'three four', 'five' ), expSysCardRenderer::wrap( 'one two three four five', 10 ) );
        $this->assertSame( array( 'abcd', 'efgh', 'ij' ), expSysCardRenderer::wrap( 'abcdefghij', 4 ) );
        $this->assertSame( 8, expSysCardColor::width( "\033[1;38;5;208m●é Label\033[0m" ) );
    }

    public function testShippedLogos()
    {
        $unicode = new expSysCardLogo( array( $this->logoDir() ), 'e', true );
        $ascii = new expSysCardLogo( array( $this->logoDir() ), 'e', false );
        foreach ( array( 'big', 'small', 'mini' ) as $size )
        {
            $this->assertNotEmpty( $unicode->lines( $size ) );
            $this->assertNotEmpty( $ascii->lines( $size ) );
            foreach ( $ascii->lines( $size ) as $line )
                $this->assertSame( 0, preg_match( '/[^\x20-\x7E]/', $line ), "ASCII $size logo" );
        }
        $this->assertGreaterThan( expSysCardLogo::widthOf( $unicode->lines( 'small' ) ), expSysCardLogo::widthOf( $unicode->lines( 'big' ) ) );
        $this->assertGreaterThan( expSysCardLogo::widthOf( $unicode->lines( 'mini' ) ), expSysCardLogo::widthOf( $unicode->lines( 'small' ) ) );
        $this->assertCount( 2, $unicode->wordmark() );
        $this->assertSame( array( 'E X P O N E N T I A L' ), $ascii->wordmark() );
        $this->assertTrue( expSysCardLogo::unicodeTerminal( array( 'LANG' => 'en_US.UTF-8' ) ) );
        $this->assertFalse( expSysCardLogo::unicodeTerminal( array( 'LC_ALL' => 'C', 'LANG' => 'en_US.UTF-8' ) ), 'LC_ALL wins' );
        $other = new expSysCardLogo( array( sys_get_temp_dir() . '/no-such-dir', $this->logoDir() ), 'e', true );
        $this->assertSame( $unicode->lines( 'big' ), $other->lines( 'big' ), 'a directory without the drawing is skipped' );
        $painted = expSysCardLogo::paint( $unicode->lines( 'big' ), $this->theme(), 'truecolor' );
        $this->assertStringContainsString( '38;2;242;140;40', $painted[0], 'the orange e' );
        $this->assertStringContainsString( '38;2;194;65;12', end( $painted ), 'shaded to LogoShade at the bottom' );
    }
}
