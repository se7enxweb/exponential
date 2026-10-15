<?php
/**
 * The environment badge of admin4 and admin4l (site.ini [AdminEnvironment], exp_environment_badge(),
 * design:parts/a4_environment_badge.tpl), checked on the files and the operator's pure function (no database):
 *
 * - off by default: site.ini ships Level empty, and an empty or unknown level, or "auto" without a known EXP_ENV,
 *   gives no badge;
 * - four levels with their default names and codes, "auto" from EXP_ENV, Name/ShortName/Release/ShowVersion,
 *   ShowInTitle, and a key that changes with every one of them;
 * - the header (admin4 and admin4l's logo block) and the sign-in page include the badge; it is text, not only color,
 *   with the level as a class; the release only for signed-in users;
 * - the page title starts with "[NAME] " (page_head.tpl's title_prefix), and every cache-block that holds the title
 *   or the header is keyed by the badge;
 * - the header keeps its heights (56, 88, 56px): the badge is 24px in the 36px row, the short code up to 767px, and the
 *   search field gives it room only from 576 to 1279px (the rules that make it so; the rendered heights were
 *   measured in a browser at 390, 576, 768, 1280 and 1440px, which this file alone cannot do);
 * - each level's text 4.5:1 on its fill and its edge 3:1 on the header, in both modes; forced colors keep the text;
 * - German and American English for the new strings.
 *
 * doc/features/6.0/admin4-design.md
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminEnvironmentBadgeTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminBaseStylesheetsTest.php';
require_once __DIR__ . '/AdminTokenContrastTest.php';

class AdminEnvironmentBadgeTest extends PHPUnit\Framework\TestCase
{
    const PARTIAL = 'design/admin4/templates/parts/a4_environment_badge.tpl';

    const LAYOUTS = array(
        'design/admin4/templates/pagelayout.tpl',
        'design/admin4l/templates/pagelayout.tpl',
        'design/admin4/templates/loginpagelayout.tpl',
    );

    private static function file( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    private static function css()
    {
        return preg_replace( '#/\*.*?\*/#s', '', self::file( 'design/admin4/stylesheets/admin4.css' ) );
    }

    /** The settings of one section of an INI file, name => value */
    private static function iniSection( $path, $section )
    {
        $text = self::file( $path );
        $settings = array();
        if ( preg_match( '/^\[' . preg_quote( $section, '/' ) . '\]\s*$(.*?)(?=^\[|\z)/ms', $text, $m ) )
        {
            foreach ( preg_split( '/\R/', $m[1] ) as $line )
            {
                if ( preg_match( '/^([A-Za-z]+)=(.*)$/', $line, $pair ) )
                    $settings[$pair[1]] = $pair[2];
            }
        }
        return $settings;
    }

    public function testTheBadgeIsOffByDefault()
    {
        $settings = self::iniSection( 'settings/site.ini', 'AdminEnvironment' );
        $this->assertSame( array( 'Level', 'Name', 'ShortName', 'Release', 'ShowVersion', 'ShowInTitle' ), array_keys( $settings ) );
        $this->assertSame( '', $settings['Level'] );
        $this->assertSame( 'disabled', $settings['ShowVersion'] );
        $this->assertSame( 'enabled', $settings['ShowInTitle'] );

        foreach ( array( false, 'dev', 'prod' ) as $environment )
        {
            $badge = expEnvironmentOperator::badge( $settings, $environment, '6.0.15' );
            $this->assertSame( '', $badge['level'] );
            $this->assertSame( '', $badge['key'], 'no badge, no title prefix, the cache keys as before' );
            $this->assertFalse( $badge['title'] );
        }
        foreach ( array( 'prod', 'Live!', 'auto ', ' ' ) as $level )
        {
            $this->assertSame( '', expEnvironmentOperator::badge( array( 'Level' => $level ), 'qa', '' )['level'], $level );
        }
        $this->assertSame( '', expEnvironmentOperator::badge( array( 'Level' => array( 'test' ) ), false )['level'] );
    }

    public static function levelProvider()
    {
        return array(
            array( 'development', 'Development', 'DEV' ),
            array( 'test', 'Test', 'TEST' ),
            array( 'staging', 'Staging', 'STAGE' ),
            array( 'production', 'Production', 'PROD' ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'levelProvider' )]
    public function testEachLevelHasItsNameAndCode( $level, $name, $short )
    {
        $badge = expEnvironmentOperator::badge( array( 'Level' => ' ' . strtoupper( $level ) . ' ' ), false );
        $this->assertSame( $level, $badge['level'] );
        $this->assertSame( $name, $badge['name'] );
        $this->assertSame( $short, $badge['short'] );
        $this->assertSame( '', $badge['release'] );
        $this->assertTrue( $badge['title'], 'ShowInTitle is on unless disabled' );

        $translated = expEnvironmentOperator::badge( array( 'Level' => $level ), false, '', function ( $text ) { return '<' . $text . '>'; } );
        $this->assertSame( '<' . $name . '>', $translated['name'] );
        $this->assertSame( '<' . $short . '>', $translated['short'] );
    }

    public function testAutoTakesTheLevelFromTheEnvironmentName()
    {
        $expected = array( 'dev' => 'development', 'development' => 'development', 'test' => 'test', 'stage' => 'staging',
                           'staging' => 'staging', 'prod' => 'production', 'production' => 'production', 'live' => 'production',
                           'qa' => '', 'local' => '' );
        foreach ( $expected as $environment => $level )
        {
            $this->assertSame( $level, expEnvironmentOperator::badge( array( 'Level' => 'auto' ), $environment )['level'], $environment );
        }
        $this->assertSame( '', expEnvironmentOperator::badge( array( 'Level' => 'auto' ), false )['level'] );
    }

    public function testNameReleaseVersionTitleAndKey()
    {
        $badge = expEnvironmentOperator::badge( array( 'Level' => 'test', 'Name' => "  Customer\t acceptance ", 'ShortName' => 'UAT',
                                                       'Release' => ' 2026.10 rc2 ', 'ShowInTitle' => 'disabled' ), false, '6.0.15' );
        $this->assertSame( 'Customer acceptance', $badge['name'] );
        $this->assertSame( 'UAT', $badge['short'] );
        $this->assertSame( '2026.10 rc2 · 6.0.15', $badge['release'] );
        $this->assertFalse( $badge['title'] );
        $this->assertSame( '6.0.15', expEnvironmentOperator::badge( array( 'Level' => 'test' ), false, '6.0.15' )['release'] );

        $keys = array();
        foreach ( array( array(), array( 'Name' => 'X' ), array( 'ShortName' => 'X' ), array( 'Release' => 'X' ), array( 'ShowInTitle' => 'disabled' ) ) as $change )
        {
            $keys[] = expEnvironmentOperator::badge( $change + array( 'Level' => 'production' ), false )['key'];
        }
        $keys[] = expEnvironmentOperator::badge( array( 'Level' => 'test' ), false )['key'];
        $this->assertCount( 6, array_unique( $keys ), 'every setting changes the cache key' );

        // the operator is registered under its name, next to exp_environment
        $this->assertStringContainsString( "'operator_names' => array( 'exp_environment', 'exp_environment_badge' )", self::file( 'kernel/common/eztemplateautoload.php' ) );
        $this->assertSame( array( 'exp_environment', 'exp_environment_badge' ), ( new expEnvironmentOperator() )->operatorList() );
    }

    public function testTheHeaderAndTheSignInPageIncludeTheBadge()
    {
        $include = "{include uri='design:parts/a4_environment_badge.tpl'}";
        foreach ( array( 'design/admin4/templates/page_topmenu.tpl', 'design/admin4l/templates/explayouts/block/admin_logo.tpl' ) as $path )
        {
            $tpl = self::file( $path );
            $this->assertSame( 1, substr_count( $tpl, $include ), $path );
            $this->assertMatchesRegularExpression( '#<div id="header-logo" class="header-logo">.*' . preg_quote( $include, '#' ) . '\s*</div>#s', $tpl, $path . ': inside the logo box' );
        }
        $this->assertMatchesRegularExpression( '#<header class="a4-login-top">\s*<a class="a4-login-logo"[^>]*>\{include uri=\'design:parts/a4_logo.tpl\'\}</a>\s*' . preg_quote( $include, '#' ) . '\s*</header>#', self::file( 'design/admin4/templates/loginpagelayout.tpl' ) );

        $partial = self::file( self::PARTIAL );
        $this->assertStringContainsString( '{def $a4_env_badge = exp_environment_badge()}', $partial );
        $this->assertStringContainsString( "{if \$a4_env_badge.level|ne( '' )}", $partial, 'nothing without a level' );
        $this->assertStringContainsString( 'class="a4-env a4-env-{$a4_env_badge.level}"', $partial );
        $this->assertStringContainsString( '<span class="a4-env-dot" aria-hidden="true"></span>', $partial );
        $this->assertStringContainsString( "<span class=\"a4-sr-only\">{'Environment'|i18n( 'design/admin/pagelayout' )|wash}:</span>", $partial );
        $this->assertStringContainsString( '<span class="a4-env-name">{$a4_env_badge.name|wash}</span>', $partial );
        $this->assertStringContainsString( '<span class="a4-env-short" aria-hidden="true">{$a4_env_badge.short|wash}</span>', $partial );
        $this->assertStringContainsString( "cond( first_set( \$current_user.is_logged_in, false() ), \$a4_env_badge.release, '' )", $partial, 'the release only for signed-in users' );
        // every value written into the markup is washed (the level is one of four words, see badge())
        preg_match_all( '/\{\$a4_env_(?:badge\.(?!level\b)\w+|release)([^}]*)\}/', $partial, $outputs, PREG_SET_ORDER );
        $this->assertNotEmpty( $outputs );
        foreach ( $outputs as $output )
        {
            $this->assertSame( '|wash', $output[1], $output[0] );
        }
    }

    public function testThePageTitleStartsWithTheNameAndTheCacheBlocksAreKeyedByTheBadge()
    {
        $head = self::file( 'design/standard/templates/page_head.tpl' );
        $this->assertStringContainsString( "disable_meta_language=false() title_prefix=''}", $head, 'no prefix by default' );
        $this->assertStringContainsString( '<title>{$title_prefix|wash}{$site_title}</title>', $head );

        foreach ( self::LAYOUTS as $path )
        {
            $tpl = self::file( $path );
            $this->assertMatchesRegularExpression( '/\$env_badge\s+= exp_environment_badge\(\)/', $tpl, $path );
            $this->assertStringContainsString( "{include uri='design:page_head.tpl' title_prefix=cond( \$env_badge.title, concat( '[', \$env_badge.name|upcase, '] ' ), '' )}", $tpl, $path );
            // each cache-block that holds the head or the header carries the badge's key
            // the innermost cache-block around each include of the head and the header carries the badge's key
            preg_match_all( '/\{cache-block keys=array\((.*)\)( ignore_content_expiry)?\}|\{\/cache-block\}|design:page_head(?:er)?\.tpl/', $tpl, $tokens, PREG_SET_ORDER );
            $open = array();
            $checked = 0;
            foreach ( $tokens as $token )
            {
                if ( strpos( $token[0], '{cache-block' ) === 0 )
                    $open[] = $token[1];
                elseif ( $token[0] === '{/cache-block}' )
                    array_pop( $open );
                else
                {
                    $this->assertNotEmpty( $open, "$path: {$token[0]} is cached" );
                    $this->assertStringContainsString( '$env_badge.key', end( $open ), "$path: {$token[0]}" );
                    $checked++;
                }
            }
            $this->assertSame( strpos( $tpl, 'page_header.tpl' ) !== false ? 2 : 1, $checked, $path );
        }
    }

    public function testTheHeaderKeepsItsHeights()
    {
        $css = self::css();
        $base = AdminBaseStylesheetsTest::rules( preg_replace( '/@media[^{]*\{((?:[^{}]*\{[^{}]*\})*)[^{}]*\}/s', '', $css ) );
        $this->assertSame( '24px', $base['.a4-env']['height'], 'in the 36px row of the header tools' );
        $this->assertSame( 'border-box', $base['.a4-env']['box-sizing'] );
        $this->assertSame( 'nowrap', $base['.a4-env']['white-space'] );
        $this->assertSame( '0', $base['.a4-env']['min-width'] );

        $media = function ( $query ) use ( $css ) {
            // the rules of every "@media <query> { ... }" block, joined
            $inner = '';
            $head = '@media ' . $query . ' {';
            for ( $offset = 0; ( $start = strpos( $css, $head, $offset ) ) !== false; $offset = $i )
            {
                for ( $i = $start + strlen( $head ), $depth = 1, $from = $i; $depth > 0; $i++ )
                {
                    if ( $css[$i] === '{' ) $depth++;
                    if ( $css[$i] === '}' ) $depth--;
                }
                $inner .= substr( $css, $from, $i - 1 - $from ) . "\n";
            }
            $this->assertNotSame( '', $inner, $query );
            return AdminBaseStylesheetsTest::rules( $inner );
        };
        // the heights of AdminHeaderTest stay
        $this->assertSame( '88px', $media( 'screen and (min-width: 576px)' )[':root']['--header-height'] );
        $this->assertSame( '56px', $media( 'screen and (min-width: 1280px)' )[':root']['--header-height'] );

        // up to 767px, phones included, the short code in words: never the color alone
        $compact = $media( 'screen and (max-width: 767.98px)' );
        $this->assertSame( 'none', $compact['#header .a4-env-dot']['display'] );
        // 576 to 767px with a badge the logo is the mark alone, as on phones, so the field keeps its room
        $mark = $media( 'screen and (min-width: 576px) and (max-width: 767.98px)' );
        $this->assertSame( 'none', $mark['#header:has(.a4-env) #header-logo .a4-logo-full']['display'] );
        $this->assertSame( 'block', $mark['#header:has(.a4-env) #header-logo .a4-logo-mark']['display'] );
        $this->assertSame( 'block', $compact['#header .a4-env-short']['display'] );
        $this->assertSame( '4rem', $compact['#header .a4-env']['max-width'] );
        $this->assertSame( 'calc(4rem + var(--a4-space-2) - 122px)', $compact['#header:has(.a4-env)']['--a4-env-room'] );
        $this->assertSame( 'calc(13rem + var(--a4-space-2))', $media( 'screen and (min-width: 768px) and (max-width: 1279.98px)' )['#header:has(.a4-env)']['--a4-env-room'] );
        $room = $media( 'screen and (min-width: 576px) and (max-width: 1279.98px)' );
        $this->assertSame( 'clamp(5.5rem, calc(100vw - 454px - var(--a4-env-room)), 230px)', $room['#header:has(.a4-env) .searchblock input.searchtext']['width'],
                           'the field gives the badge its room up to 1279px; from 1280px the main menu does' );
        // what is not shown stays for screen readers: clipped, not display: none
        $this->assertDoesNotMatchRegularExpression( '/\.a4-env-name[^{]*\{[^}]*display:\s*none/', $css );
    }

    /** The custom properties of every top-level block whose selector is exactly $selector */
    private static function tokens( $css, $selector )
    {
        preg_match_all( '/(^|\})\s*' . preg_quote( $selector, '/' ) . '\s*\{([^}]*)\}/s', $css, $blocks );
        $tokens = array();
        foreach ( $blocks[2] as $block )
        {
            preg_match_all( '/(--[\w-]+)\s*:\s*([^;]+);/', $block, $pairs, PREG_SET_ORDER );
            foreach ( $pairs as $pair )
                $tokens[$pair[1]] = trim( $pair[2] );
        }
        return $tokens;
    }

    public function testEachLevelReachesItsContrastInBothModes()
    {
        $css = self::css();
        $light = self::tokens( $css, ':root' );
        $dark = self::tokens( $css, 'html[data-a4-theme="dark"]' ) + $light;
        foreach ( array( 'light' => $light, 'dark' => $dark ) as $mode => $tokens )
        {
            $page = $tokens['--a4-page-color'];
            $header = AdminTokenContrastTest::opaque( $tokens['--a4-header-bg'], $page );
            foreach ( array( 'development', 'test', 'staging', 'production' ) as $level )
            {
                $fill = $tokens['--a4-env-' . $level];
                $this->assertGreaterThanOrEqual( 4.5, AdminTokenContrastTest::ratio( $tokens['--a4-env-' . $level . '-ink'], $fill ), "$mode $level: text on fill" );
                $this->assertGreaterThanOrEqual( 3.0, AdminTokenContrastTest::ratio( $tokens['--a4-env-' . $level . '-line'], $header ), "$mode $level: edge on the header" );
            }
        }
        $base = AdminBaseStylesheetsTest::rules( $css );
        foreach ( array( 'development', 'test', 'staging', 'production' ) as $level )
        {
            $this->assertSame( 'var(--a4-env-' . $level . ')', $base['.a4-env-' . $level]['--a4-env-bg'] );
        }
        $forced = AdminBaseStylesheetsTest::rules( $css, true );
        $this->assertSame( 'CanvasText', $forced['.a4-env']['color'], 'forced colors: the text stays' );
        $this->assertSame( 'CanvasText', $forced['.a4-env']['border-color'] );
        $this->assertSame( 'CanvasText', $forced['.a4-env-dot']['background-color'] );
    }

    public function testTheStringsAreTranslated()
    {
        $strings = array( 'Environment' => 'Umgebung', 'Development' => 'Entwicklung', 'Test' => 'Test', 'Staging' => 'Staging',
                          'Production' => 'Produktion', 'DEV' => 'DEV', 'TEST' => 'TEST', 'STAGE' => 'STAGE', 'PROD' => 'PROD' );
        foreach ( array( 'ger-DE', 'eng-US' ) as $language )
        {
            $ts = self::file( 'share/translations/' . $language . '/translation.ts' );
            $start = strpos( $ts, '<name>design/admin/pagelayout</name>' );
            $context = substr( $ts, $start, strpos( $ts, '</context>', $start ) - $start );
            foreach ( $strings as $source => $german )
            {
                $expected = $language === 'ger-DE' ? $german : $source;
                $this->assertStringContainsString( "<source>$source</source>\n        <translation>$expected</translation>", $context, "$language: $source" );
            }
        }
    }
}
