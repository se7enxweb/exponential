<?php
/**
 * File containing the expSysCardLogo class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The logo of the system card: the orange "e" of Exponential in three sizes, in Unicode block art and in plain
 * ASCII, and the wordmark "EXPONENTIAL" that can go under it.
 *
 * The drawings are text files, one per size and character set (share/syscard/logos/):
 *
 *   <name>-big.txt  <name>-small.txt  <name>-mini.txt                  Unicode block art
 *   <name>-big.ascii.txt  <name>-small.ascii.txt  <name>-mini.ascii.txt   ASCII only
 *   wordmark.txt  wordmark.ascii.txt
 *
 * syscard.ini [Logo] Logo= picks the name (e) and Directories[] the places to look in, the last first, so an
 * extension that adds Directories[]=extension/<ext>/share/syscard/logos can draw its own (or replace "e").
 */
class expSysCardLogo
{
    const BIG = 'big';
    const SMALL = 'small';
    const MINI = 'mini';

    /** @var string[] directories, the one looked in first first */
    protected $directories = array();
    /** @var string */
    protected $name = 'e';
    /** @var bool */
    protected $unicode = true;
    /** @var array cache: file => lines */
    protected $cache = array();

    /**
     * @param string[] $directories absolute directories, the one looked in first first
     * @param string $name
     * @param bool $unicode
     */
    public function __construct( array $directories, $name = 'e', $unicode = true )
    {
        $this->directories = array_values( array_filter( $directories, 'strlen' ) );
        $this->name = preg_replace( '/[^a-z0-9_-]/i', '', (string)$name ) ?: 'e';
        $this->unicode = (bool)$unicode;
    }

    /**
     * The logo of syscard.ini [Logo].
     *
     * @param string $root the installation root
     * @param bool $unicode
     * @return expSysCardLogo
     */
    public static function fromIni( $root, $unicode = true )
    {
        $ini = class_exists( 'eZINI' ) && eZINI::exists( 'syscard.ini' ) ? eZINI::instance( 'syscard.ini' ) : null;
        $name = $ini && $ini->hasVariable( 'Logo', 'Logo' ) ? (string)$ini->variable( 'Logo', 'Logo' ) : 'e';
        $dirs = $ini && $ini->hasVariable( 'Logo', 'Directories' ) ? (array)$ini->variable( 'Logo', 'Directories' ) : array();
        if ( empty( $dirs ) )
            $dirs = array( 'share/syscard/logos' );
        $absolute = array();
        foreach ( array_reverse( $dirs ) as $dir )
        {
            $dir = trim( (string)$dir );
            if ( $dir !== '' )
                $absolute[] = $dir[0] === '/' ? $dir : rtrim( $root, '/' ) . '/' . $dir;
        }
        return new self( $absolute, $name, $unicode );
    }

    /**
     * Whether the terminal takes UTF-8 (LC_ALL, LC_CTYPE, LANG).
     *
     * @param array $env
     * @return bool
     */
    public static function unicodeTerminal( array $env )
    {
        foreach ( array( 'LC_ALL', 'LC_CTYPE', 'LANG' ) as $name )
        {
            if ( isset( $env[$name] ) && $env[$name] !== '' )
                return (bool)preg_match( '/utf-?8/i', (string)$env[$name] );
        }
        return false;
    }

    /**
     * @return bool
     */
    public function unicode()
    {
        return $this->unicode;
    }

    /**
     * The lines of the logo at a size (trailing blanks kept off), or an empty list.
     *
     * @param string $size big, small or mini
     * @return string[]
     */
    public function lines( $size )
    {
        return $this->drawing( $this->name . '-' . $size );
    }

    /**
     * @return string[] the lines of the wordmark
     */
    public function wordmark()
    {
        return $this->drawing( 'wordmark' );
    }

    /**
     * The lines of a drawing: the Unicode one, or the ASCII one (the Unicode one when there is no ASCII one and
     * the reverse).
     *
     * @param string $base
     * @return string[]
     */
    protected function drawing( $base )
    {
        $names = $this->unicode ? array( $base . '.txt', $base . '.ascii.txt' ) : array( $base . '.ascii.txt' );
        foreach ( $names as $file )
        {
            foreach ( $this->directories as $dir )
            {
                $path = $dir . '/' . $file;
                if ( isset( $this->cache[$path] ) )
                    return $this->cache[$path];
                if ( is_file( $path ) && is_readable( $path ) )
                {
                    $text = (string)file_get_contents( $path, false, null, 0, 65536 );
                    $lines = array_map( 'rtrim', explode( "\n", str_replace( "\r", '', rtrim( $text, "\n" ) ) ) );
                    return $this->cache[$path] = $lines;
                }
            }
        }
        return array();
    }

    /**
     * The width of a drawing (its longest line).
     *
     * @param string[] $lines
     * @return int
     */
    public static function widthOf( array $lines )
    {
        $width = 0;
        foreach ( $lines as $line )
            $width = max( $width, expSysCardColor::width( $line ) );
        return $width;
    }

    /**
     * The logo in its colors: the Logo color, shaded down to LogoShade from the top line to the bottom one.
     *
     * @param string[] $lines
     * @param expSysCardTheme $theme
     * @param string $depth
     * @return string[]
     */
    public static function paint( array $lines, expSysCardTheme $theme, $depth )
    {
        if ( $depth === expSysCardColor::NONE )
            return $lines;
        $stops = array_values( array_filter( array( $theme->color( 'Logo' ), $theme->color( 'LogoShade' ) ), function ( $c ) { return expSysCardColor::rgb( $c ) !== null; } ) );
        $out = array();
        $count = count( $lines );
        foreach ( $lines as $i => $line )
        {
            if ( empty( $stops ) || ( $depth === expSysCardColor::C16 ) )
            {
                $out[] = $theme->paint( $line, 'Logo', $depth, true );
                continue;
            }
            $hex = expSysCardColor::gradient( $stops, $count > 1 ? $i / ( $count - 1 ) : 0.0 );
            $code = expSysCardColor::code( $hex, $depth );
            $out[] = $line === '' || $code === '' ? $line : "\033[" . ( $theme->bold() ? '1;' : '' ) . $code . 'm' . $line . "\033[0m";
        }
        return $out;
    }
}
