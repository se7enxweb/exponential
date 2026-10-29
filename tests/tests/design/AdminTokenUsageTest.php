<?php
/**
 * Weights, durations, letter spacing and component radius of the admin designs' stylesheets come from the tokens of
 * components.css (--a4-weight-*, --a4-duration*, --a4-tracking, --a4-radius-s), never as raw numbers: one scale for
 * the whole administration, so a change of the scale is one line. Checked on every stylesheet of admin4, the classic
 * admin (design/admin) and admin3 (admin4l and the editor design load admin4's) and on the admin stylesheets of the
 * extensions in this repository, outside comments and outside the definitions of custom properties. A stylesheet
 * that is also loaded without components.css (core.css: the standard popup, print and setup layouts) names the raw
 * value as the token's fallback, var(--a4-weight-bold, bold).
 *
 * Allowed on purpose: 0, 50% and 999px as a radius; a negative letter spacing (the tracking of large headings);
 * the two progress bars' .4s (a bar grows in steps of real progress, slower than a hover); 0s and .01ms (the
 * reduced-motion block).
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminTokenUsageTest.php
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminTokenUsageTest extends PHPUnit\Framework\TestCase
{
    /** The two rules whose transition is a progress, not a feedback */
    const PROGRESS_BARS = '/\.a4-(?:content-job-bar|setup-progress) > span\s*\{[^}]*\}/';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /**
     * Every stylesheet of every admin design: admin4 (all of its own, the module stylesheets included), the classic
     * admin (design/admin: its base files and themes; its components.css and modules/*.css are admin4's copies,
     * checked equal elsewhere) and admin3, plus the admin stylesheets that extensions in this repository ship for
     * these designs. admin4l and the editor design have no stylesheets of their own: they load admin4's.
     *
     * @return array file path below the root => CSS without comments
     */
    private static function stylesheets()
    {
        $root = self::root();
        $files = array_merge(
            AdminFiles::files( "$root/design/admin4/stylesheets/*.css" ),
            AdminFiles::files( "$root/design/admin4/stylesheets/theme/*.css" ),
            AdminFiles::files( "$root/design/admin4/stylesheets/modules/*.css" ),
            array_diff( AdminFiles::files( "$root/design/admin/stylesheets/*.css" ), array( "$root/design/admin/stylesheets/components.css" ) ),
            AdminFiles::files( "$root/design/admin/stylesheets/theme/*.css" ),
            AdminFiles::files( "$root/design/admin3/stylesheets/*.css" ),
            AdminFiles::files( "$root/design/admin3/stylesheets/theme/*.css" ),
            AdminFiles::files( "$root/extension/*/design/admin{,3,4,4l}/stylesheets/*.css", GLOB_BRACE )
        );
        $css = array();
        foreach ( $files as $file )
        {
            $css[substr( $file, strlen( $root ) + 1 )] = preg_replace( '#/\*.*?\*/#s', '', AdminFiles::read( $file ) );
        }
        return $css;
    }

    /** The scan reaches every admin design and the extensions' admin stylesheets */
    public function testTheScanCoversEveryAdminDesign()
    {
        $names = array_keys( self::stylesheets() );
        foreach ( array( 'design/admin4/stylesheets/base.css', 'design/admin4/stylesheets/core.css', 'design/admin4/stylesheets/theme/modalwindow.css',
                         'design/admin/stylesheets/pagelayout.css', 'design/admin/stylesheets/content.css', 'design/admin/stylesheets/theme/rounded.css',
                         'design/admin/stylesheets/theme/admin_datatable.css', 'design/admin3/stylesheets/pagelayout.css',
                         'design/admin3/stylesheets/theme/rounded.css', 'extension/expimageeditor/design/admin4/stylesheets/expimageeditor.css' ) as $name )
        {
            $this->assertContains( $name, $names );
        }
        foreach ( array( 'admin4l', 'editor' ) as $design )
        {
            $this->assertDirectoryDoesNotExist( self::root() . "/design/$design/stylesheets",
                "$design loads admin4's stylesheets; a stylesheet of its own needs to be added to this scan" );
        }
    }

    /**
     * The values of one property in every file, without the definitions of custom properties (--x-font-weight)
     *
     * @return string[] "file: value"
     */
    private static function values( $css, $property )
    {
        $found = array();
        foreach ( $css as $file => $text )
        {
            preg_match_all( '/(?<![\w-])' . $property . '\s*:\s*([^;}]+)/i', $text, $m );
            foreach ( $m[1] as $value )
            {
                $found[] = "$file: " . trim( $value );
            }
        }
        return $found;
    }

    public function testTheTokensAreDefinedOnce()
    {
        $components = AdminFiles::read( self::root() . '/design/admin4/stylesheets/components.css' );
        foreach ( array( '--a4-weight-medium: 500', '--a4-weight-strong: 600', '--a4-weight-heading: 650', '--a4-weight-bold: 700',
                         '--a4-duration: .15s', '--a4-duration-slow: .3s', '--a4-tracking: .04em',
                         '--a4-leading: 1.5', '--a4-leading-tight: 1.25', '--a4-leading-none: 1' ) as $definition )
        {
            $this->assertSame( 1, preg_match_all( '/^\s*' . preg_quote( $definition, '/' ) . ';/m', $components ), $definition );
        }
        $this->assertDoesNotMatchRegularExpression( '/html\[data-a4-theme="dark"\]\s*\{[^}]*--a4-(weight|duration|tracking|leading)/', $components,
            'weights, durations, tracking and leading are the same in both modes' );
    }

    public function testNoRawFontWeight()
    {
        $problems = array();
        $values = self::values( self::stylesheets(), 'font-weight' );
        $this->assertGreaterThan( 100, count( $values ), 'the stylesheets set many weights: the scan found them' );
        foreach ( $values as $value )
        {
            if ( preg_match( '/: (?:\d+|bold|bolder|lighter)\b/', $value ) )
            {
                $problems[] = $value;
            }
        }
        $this->assertSame( array(), $problems, 'use var(--a4-weight-medium|strong|heading|bold); 400 is the keyword normal' );
    }

    public function testNoRawTransitionDuration()
    {
        $css = self::stylesheets();
        $bars = 0;
        foreach ( $css as $file => $text )
        {
            $css[$file] = preg_replace( self::PROGRESS_BARS, '', $text, -1, $count );
            $bars += $count;
        }
        $this->assertSame( 2, $bars, 'the two progress bars (modules/content.css, modules/setup.css) are the documented exceptions' );
        $problems = array();
        foreach ( array_merge( self::values( $css, 'transition' ), self::values( $css, 'transition-duration' ), self::values( $css, 'transition-delay' ) ) as $value )
        {
            preg_match_all( '/(?<![\w.-])(\d*\.?\d+m?s)\b/', preg_replace( '/^[^:]+: /', '', $value ), $m );
            foreach ( $m[1] as $duration )
            {
                if ( !in_array( $duration, array( '0s', '.01ms' ), true ) )
                {
                    $problems[] = $value;
                }
            }
        }
        $this->assertSame( array(), $problems, 'use var(--a4-duration), or var(--a4-duration-slow) for the phone menu' );
    }

    public function testNoRawPositiveLetterSpacing()
    {
        $problems = array();
        foreach ( self::values( self::stylesheets(), 'letter-spacing' ) as $value )
        {
            if ( preg_match( '/: [^;]*(?<![\w.-])\d*\.?\d*[1-9]\d*em\b/', $value ) )
            {
                $problems[] = $value;
            }
        }
        $this->assertSame( array(), $problems, 'use var(--a4-tracking) for uppercase labels; only a negative value is a value of its own' );
    }

    /** The shorthand names a weight and a line height too: font: 600 13px/1.2 ... */
    public function testNoRawWeightOrLineHeightInTheFontShorthand()
    {
        $problems = array();
        foreach ( self::values( self::stylesheets(), 'font' ) as $value )
        {
            if ( preg_match( '/: (?:\d{3}|bold)\b|\)\/[\d.]/', $value ) )
            {
                $problems[] = $value;
            }
        }
        $this->assertSame( array(), $problems, 'use var(--a4-weight-*) and var(--a4-leading*) in the font shorthand too' );
    }

    public function testNoRawBorderRadius()
    {
        $problems = array();
        $values = self::values( self::stylesheets(), 'border(?:-[a-z]+){0,2}-radius' );
        $this->assertGreaterThan( 100, count( $values ), 'the stylesheets set many radii: the scan found them' );
        foreach ( $values as $value )
        {
            $plain = preg_replace( '/var\([^)]*\)/', '', preg_replace( '/^[^:]+: /', '', $value ) );
            preg_match_all( '/(?<![\w.])(\d*\.?\d+)(px|rem|em|%)/', $plain, $m, PREG_SET_ORDER );
            foreach ( $m as $length )
            {
                $number = (float)$length[1];
                if ( $number == 0 || ( $length[2] === 'px' && $number == 999 ) || ( $length[2] === '%' && $number == 50 ) )
                {
                    continue;
                }
                $problems[] = $value;
            }
        }
        $this->assertSame( array(), $problems, 'use var(--a4-radius-s); only 0, 50% and 999px are values of their own' );
    }

    public function testTheCheckersCatchWhatTheyShould()
    {
        $css = array( 'x.css' => '.a { font-weight: 600; transition: color .15s ease; letter-spacing: .05em; border-radius: 4px; }'
            . ' .b { --a4-weight-strong: 600; font-weight: var(--a4-weight-strong); letter-spacing: -.01em; border-radius: 999px; }' );
        $this->assertSame( array( 'x.css: 600', 'x.css: var(--a4-weight-strong)' ), self::values( $css, 'font-weight' ) );
        $this->assertSame( 1, preg_match( '/: [^;]*(?<![\w.-])\d*\.?\d*[1-9]\d*em\b/', self::values( $css, 'letter-spacing' )[0] ) );
        $this->assertSame( 0, preg_match( '/: [^;]*(?<![\w.-])\d*\.?\d*[1-9]\d*em\b/', self::values( $css, 'letter-spacing' )[1] ) );
    }
}
