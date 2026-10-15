<?php
/**
 * admin4's left column: its size (small, medium, large, or a width dragged by hand) is a width only. The text keeps
 * the body's size at every width, as the right column; admin3 scaled the font with the size.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminLeftColumnFontTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminLeftColumnFontTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /**
     * The rules of a stylesheet, comments removed, as selector => declarations (one entry per rule, in order).
     */
    private static function rules( $path )
    {
        $css = (string)file_get_contents( self::root() . '/' . $path );
        $css = preg_replace( '#/\*.*?\*/#s', '', $css );
        preg_match_all( '/([^{}]+)\{([^{}]*)\}/', $css, $m, PREG_SET_ORDER );
        $rules = array();
        foreach ( $m as $r )
        {
            $rules[] = array( trim( $r[1] ), trim( $r[2] ) );
        }
        return $rules;
    }

    private static function admin4Stylesheets()
    {
        $files = array();
        foreach ( array( 'design/admin4/stylesheets', 'design/admin4l/stylesheets' ) as $dir )
        {
            if ( !is_dir( self::root() . '/' . $dir ) )
            {
                continue;
            }
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . '/' . $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $file )
            {
                if ( substr( $file->getFilename(), -4 ) === '.css' )
                {
                    $files[] = substr( $file->getPathname(), strlen( self::root() ) + 1 );
                }
            }
        }
        sort( $files );
        return $files;
    }

    public function testTheColumnSizesSetAWidthOnly()
    {
        $seen = 0;
        foreach ( self::admin4Stylesheets() as $path )
        {
            foreach ( self::rules( $path ) as $rule )
            {
                list( $selector, $body ) = $rule;
                if ( strpos( $selector, 'data-a4-left-size' ) === false && strpos( $selector, 'data-a4-left-width' ) === false )
                {
                    continue;
                }
                ++$seen;
                $this->assertDoesNotMatchRegularExpression( '/(^|;|\s)(font-size|font|--[\w-]*font[\w-]*)\s*:/', $body,
                    "$path: `$selector` sets a font size; the column's size is a width only" );
            }
        }
        $this->assertSame( 2, $seen, 'the medium and large rules of pagelayout.css were found' );
    }

    public function testTheLeftColumnKeepsTheBodysFontSize()
    {
        $found = false;
        foreach ( self::rules( 'design/admin4/stylesheets/pagelayout.css' ) as $rule )
        {
            if ( $rule[0] !== '.sidebar.left' )
            {
                continue;
            }
            $found = true;
            $this->assertStringContainsString( 'width: var(--left-sidebar-width', $rule[1] );
            $this->assertMatchesRegularExpression( '/font-size:\s*inherit\s*;/', $rule[1], 'the column takes the body\'s --a4-text-body, as the right one' );
        }
        $this->assertTrue( $found, '.sidebar.left is in pagelayout.css' );
    }

    public function testNoAdmin4FileUsesTheOldFontProperty()
    {
        $hits = array();
        foreach ( array( 'design/admin4', 'design/admin4l' ) as $dir )
        {
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . '/' . $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $file )
            {
                if ( !preg_match( '/\.(css|js|tpl)$/', $file->getFilename() ) )
                {
                    continue;
                }
                if ( strpos( (string)file_get_contents( $file->getPathname() ), '--left-sidebar-font-size' ) !== false )
                {
                    $hits[] = substr( $file->getPathname(), strlen( self::root() ) + 1 );
                }
            }
        }
        $this->assertSame( array(), $hits, 'admin4 sets the left column\'s font by no size property' );
    }
}
