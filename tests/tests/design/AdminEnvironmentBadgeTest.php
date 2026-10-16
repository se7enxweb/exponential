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
 * - BadgeDisplay=hover, the default: the badge takes no room and shows nothing until a fine pointer that can hover
 *   rests on the logo link (or the link has the keyboard focus); Escape dismisses it (admin4.js); on touch screens it
 *   stands in the row with the room of always; screen readers keep it (clipped, not removed, and the logo link's
 *   aria-describedby); no fade for reduced motion; BadgeDisplay=always
 *   keeps it in the row;
 * - the page title starts with "[NAME] " (page_head.tpl's title_prefix), and every cache-block that holds the title
 *   or the header is keyed by the badge;
 * - the header keeps its heights (56, 88, 56px): the badge is 24px in the 36px row; with BadgeDisplay=always the short
 *   code up to 767px, and the search field gives it room only from 576 to 1279px (the rules that make it so; the rendered heights were
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
        $this->assertSame( array( 'Level', 'Name', 'ShortName', 'Release', 'ShowVersion', 'ShowInTitle', 'BadgeDisplay' ), array_keys( $settings ) );
        $this->assertSame( '', $settings['Level'] );
        $this->assertSame( 'disabled', $settings['ShowVersion'] );
        $this->assertSame( 'enabled', $settings['ShowInTitle'] );
        $this->assertSame( 'hover', $settings['BadgeDisplay'] );

        foreach ( array( false, 'dev', 'prod' ) as $environment )
        {
            $badge = expEnvironmentOperator::badge( $settings, $environment, '6.0.15' );
            $this->assertSame( '', $badge['level'] );
            $this->assertSame( '', $badge['key'], 'no badge, no title prefix, the cache keys as before' );
            $this->assertSame( '', $badge['display'] );
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
        foreach ( array( array(), array( 'Name' => 'X' ), array( 'ShortName' => 'X' ), array( 'Release' => 'X' ), array( 'ShowInTitle' => 'disabled' ),
                         array( 'BadgeDisplay' => 'always' ) ) as $change )
        {
            $keys[] = expEnvironmentOperator::badge( $change + array( 'Level' => 'production' ), false )['key'];
        }
        $keys[] = expEnvironmentOperator::badge( array( 'Level' => 'test' ), false )['key'];
        $this->assertCount( 7, array_unique( $keys ), 'every setting changes the cache key' );

        // the operator is registered under its name, next to exp_environment
        $this->assertStringContainsString( "'operator_names' => array( 'exp_environment', 'exp_environment_badge' )", self::file( 'kernel/common/eztemplateautoload.php' ) );
        $this->assertSame( array( 'exp_environment', 'exp_environment_badge' ), ( new expEnvironmentOperator() )->operatorList() );
    }

    public function testHoverIsTheDefaultDisplayAndAlwaysIsTheOnlyOther()
    {
        $this->assertSame( 'hover', expEnvironmentOperator::badge( array( 'Level' => 'production' ), false )['display'], 'unset: hover' );
        // read as Level is: case and surrounding spaces do not matter (BadgeDisplay=Always was taken as hover before)
        foreach ( array( 'hover' => 'hover', 'always' => 'always', 'Always' => 'always', ' ALWAYS ' => 'always', 'never' => 'hover', '' => 'hover' ) as $value => $display )
        {
            $badge = expEnvironmentOperator::badge( array( 'Level' => 'production', 'BadgeDisplay' => (string)$value ), false );
            $this->assertSame( $display, $badge['display'], var_export( $value, true ) );
            $this->assertTrue( $badge['title'], 'the title prefix does not depend on the display' );
            $this->assertSame( 'Production', $badge['name'] );
        }
        $this->assertSame( 'hover', expEnvironmentOperator::badge( array( 'Level' => 'production', 'BadgeDisplay' => array( 'always' ) ), false )['display'] );
        foreach ( array( 'disabled' => false, 'Disabled' => false, ' DISABLED ' => false, 'enabled' => true, '' => true ) as $value => $title )
            $this->assertSame( $title, expEnvironmentOperator::badge( array( 'Level' => 'production', 'ShowInTitle' => (string)$value ), false )['title'], "ShowInTitle=$value" );
        $this->assertTrue( expEnvironmentOperator::badge( array( 'Level' => 'production', 'ShowInTitle' => array( 'disabled' ) ), false )['title'] );
        $this->assertSame( 'enabled', expEnvironmentOperator::keyword( array( 'ShowVersion' => ' Enabled ' ), 'ShowVersion' ), 'ShowVersion too' );
        $this->assertStringContainsString( "'ShowInTitle', 'BadgeDisplay' )", self::file( 'kernel/common/expenvironmentoperator.php' ), 'currentBadge() reads the setting' );
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

        // the logo link names the badge as its description, so screen readers keep the environment however it is shown
        foreach ( array( 'design/admin4/templates/page_topmenu.tpl', 'design/admin4l/templates/explayouts/block/admin_logo.tpl' ) as $path )
        {
            $tpl = self::file( $path );
            $this->assertStringContainsString( "{def \$a4_brand_env = exp_environment_badge().level|ne( '' )}", $tpl, $path );
            preg_match_all( '#<a class="brand"[^>]*>#', $tpl, $links );
            $this->assertCount( 2, $links[0], $path );
            foreach ( $links[0] as $link )
                $this->assertStringContainsString( '{if $a4_brand_env} aria-describedby="a4-env-badge"{/if}', $link, $path );
            $this->assertStringContainsString( '{undef $a4_brand_name $a4_brand_url $a4_brand_env}', $tpl, $path );
        }
        $this->assertStringContainsString( "<a class=\"a4-login-logo\" href={'/'|ezurl} aria-label=\"Exponential\"{if \$env_badge.level|ne( '' )} aria-describedby=\"a4-env-badge\"{/if}>",
                                           self::file( 'design/admin4/templates/loginpagelayout.tpl' ) );

        $partial = self::file( self::PARTIAL );
        $this->assertStringContainsString( '{def $a4_env_badge = exp_environment_badge()}', $partial );
        $this->assertStringContainsString( "{if \$a4_env_badge.level|ne( '' )}", $partial, 'nothing without a level' );
        $this->assertStringContainsString( '<span id="a4-env-badge" class="a4-env a4-env-{$a4_env_badge.level} a4-env-{$a4_env_badge.display}"', $partial );
        $this->assertStringContainsString( "{if \$a4_env_badge.display|eq( 'always' )} title=", $partial, 'the title attribute only where the badge stays in view' );
        $this->assertStringContainsString( '<span class="a4-env-dot" aria-hidden="true"></span>', $partial );
        $this->assertStringContainsString( "<span class=\"a4-sr-only\">{'Environment'|i18n( 'design/admin/pagelayout' )|wash}:</span>", $partial );
        $this->assertStringContainsString( '<span class="a4-env-name">{$a4_env_badge.name|wash}</span>', $partial );
        $this->assertStringContainsString( '<span class="a4-env-short" aria-hidden="true">{$a4_env_badge.short|wash}</span>', $partial );
        $this->assertStringContainsString( "cond( first_set( \$current_user.is_logged_in, false() ), \$a4_env_badge.release, '' )", $partial, 'the release only for signed-in users' );
        // every value written into the markup is washed (the level is one of four words, the display one of two, see badge())
        preg_match_all( '/\{\$a4_env_(?:badge\.(?!(?:level|display)\b)\w+|release)([^}]*)\}/', $partial, $outputs, PREG_SET_ORDER );
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

    /** The text of every "@media <query> { ... }" block, joined */
    private static function media( $css, $query )
    {
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
        return $inner;
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
            $inner = self::media( $css, $query );
            $this->assertNotSame( '', $inner, $query );
            return AdminBaseStylesheetsTest::rules( $inner );
        };
        // the heights of AdminHeaderTest stay
        $this->assertSame( '88px', $media( 'screen and (min-width: 576px)' )[':root']['--header-height'] );
        $this->assertSame( '56px', $media( 'screen and (min-width: 1280px)' )[':root']['--header-height'] );

        // BadgeDisplay=always, up to 767px, phones included, the short code in words: never the color alone
        $compact = $media( 'screen and (max-width: 767.98px)' );
        $this->assertSame( 'none', $compact['#header .a4-env-always .a4-env-dot']['display'] );
        // 576 to 767px with a badge the logo is the mark alone, as on phones, so the field keeps its room
        $mark = $media( 'screen and (min-width: 576px) and (max-width: 767.98px)' );
        $this->assertSame( 'none', $mark['#header:has(.a4-env-always) #header-logo .a4-logo-full']['display'] );
        $this->assertSame( 'block', $mark['#header:has(.a4-env-always) #header-logo .a4-logo-mark']['display'] );
        $this->assertSame( 'block', $compact['#header .a4-env-always .a4-env-short']['display'] );
        $this->assertSame( '4rem', $compact['#header .a4-env-always']['max-width'] );
        $this->assertSame( 'calc(4rem + var(--a4-space-2) - 122px)', $compact['#header:has(.a4-env-always)']['--a4-env-room'] );
        $this->assertSame( 'calc(13rem + var(--a4-space-2))', $media( 'screen and (min-width: 768px) and (max-width: 1279.98px)' )['#header:has(.a4-env-always)']['--a4-env-room'] );
        $room = $media( 'screen and (min-width: 576px) and (max-width: 1279.98px)' );
        $this->assertSame( 'clamp(5.5rem, calc(100vw - 454px - var(--a4-env-room)), 230px)', $room['#header:has(.a4-env-always) .searchblock input.searchtext']['width'],
                           'the field gives the badge its room up to 1279px; from 1280px the main menu does' );
        // with a mouse or a trackpad a hover badge changes nothing in the header's layout: every rule that makes room
        // asks for .a4-env-always, or for .a4-env-hover only where the pointer cannot hover finely (a touch screen)
        $this->assertDoesNotMatchRegularExpression( '/:has\(\s*(?:>\s*)?\.a4-env\s*\)/', $css );
        $this->assertDoesNotMatchRegularExpression( '/#header \.a4-env[\s{,:]/', $css );
        $touch = ' and (not ((hover: hover) and (pointer: fine)))';
        foreach ( array( 'screen and (max-width: 767.98px)', 'screen and (min-width: 576px) and (max-width: 767.98px)',
                         'screen and (min-width: 768px) and (max-width: 1279.98px)', 'screen and (min-width: 1280px)',
                         'screen and (min-width: 576px) and (max-width: 1279.98px)' ) as $query )
        {
            $always = array();
            foreach ( $media( $query ) as $selector => $declarations )
            {
                if ( strpos( $selector, '.a4-env-always' ) !== false )
                    $always[str_replace( '.a4-env-always', '.a4-env-hover', $selector )] = $declarations;
            }
            $this->assertNotEmpty( $always, $query );
            $this->assertEquals( $always, $media( $query . $touch ), "$query: on a touch screen the hover badge gets the room of always" );
        }
        $this->assertSame( 'var(--a4-space-2)', $media( '(not ((hover: hover) and (pointer: fine)))' )['.header-logo:has(> .a4-env-hover)']['gap'] );
        // outside the media queries of the two kinds of pointer nothing names the hover badge
        $rest = preg_replace( '/@media [^{]*\(hover: hover\) and \(pointer: fine\)[^{]*\{((?:[^{}]*\{[^{}]*\})*)[^{}]*\}/s', '', $css );
        $this->assertStringNotContainsString( '.a4-env-hover', preg_replace( '#/\*.*?\*/#s', '', $rest ) );
        // what is not shown stays for screen readers: clipped, not display: none
        $this->assertDoesNotMatchRegularExpression( '/\.a4-env-name[^{]*\{[^}]*display:\s*none/', $css );
    }

    public function testTheHoverBadgeShowsOnlyForAFinePointerOnTheLogo()
    {
        $css = self::css();
        $fine = AdminBaseStylesheetsTest::rules( self::media( $css, '(hover: hover) and (pointer: fine)' ) );

        // at rest, with a mouse or a trackpad: out of the flow (no room in the header), clipped to nothing, not a
        // target, but in the accessibility tree (clipped, never display: none or visibility: hidden)
        $rest = $fine['.a4-env-hover'];
        $this->assertSame( 'absolute', $rest['position'] );
        $this->assertSame( 'inset(50%)', $rest['clip-path'] );
        $this->assertSame( '0', $rest['opacity'] );
        $this->assertSame( 'none', $rest['pointer-events'] );
        $this->assertSame( '0 -50%', $rest['translate'], 'centered on the logo' );
        $this->assertSame( 'var(--a4-z-popover)', $rest['z-index'] );
        $this->assertArrayNotHasKey( 'display', $rest );
        $this->assertDoesNotMatchRegularExpression( '/\.a4-env-(hover|dismissed)[^{\s]*\s*\{[^}]*(display:\s*none|visibility:\s*hidden)/', $css );
        $this->assertSame( 'relative', $fine['.header-logo:has(> .a4-env-hover), .a4-login-top:has(> .a4-env-hover)']['position'] );
        $this->assertSame( '""', $fine['.a4-env-hover::before']['content'], 'the gap to the logo is part of the pill: the pointer can move onto it (hoverable)' );

        // under the logo where the main menu follows it (from 1280px) or the row is full (phones); beside it otherwise
        $under = AdminBaseStylesheetsTest::rules( self::media( $css, 'screen and (hover: hover) and (pointer: fine) and (max-width: 575.98px), screen and (hover: hover) and (pointer: fine) and (min-width: 1280px)' ) );
        $this->assertSame( 'calc(100% + var(--a4-space-2))', $under['#header .a4-env-hover']['top'] );
        $this->assertSame( 'none', $under['#header .a4-env-hover']['translate'] );
        $this->assertSame( '100%', $under['#header .a4-env-hover::before']['bottom'], 'the bridge to the logo above it' );
        $this->assertSame( 'calc(100% + var(--a4-space-2))', $rest['inset-inline-start'] );

        // shown while the pointer rests on the logo link or the pill, or the link has the keyboard focus, until
        // Escape dismisses it (.a4-env-dismissed); no :active, no :focus without -visible
        $selectors = array( '.brand:hover + .a4-env-hover', '.brand:focus-visible + .a4-env-hover', '.a4-login-logo:hover + .a4-env-hover',
                            '.a4-login-logo:focus-visible + .a4-env-hover' );
        $shown = $fine[implode( ':not(.a4-env-dismissed), ', $selectors ) . ':not(.a4-env-dismissed), .a4-env-hover:not(.a4-env-dismissed):hover'];
        $this->assertSame( 'none', $shown['clip-path'] );
        $this->assertSame( '1', $shown['opacity'] );
        $this->assertSame( 'auto', $shown['pointer-events'] );
        $this->assertArrayNotHasKey( 'animation', $shown, 'the fade only without a wish for reduced motion' );
        preg_match_all( '/[^{}]*\+\s*\.a4-env-hover[^{}]*\{/', $css, $all );
        $this->assertCount( 2, $all[0], 'the show rule and the fade, both inside the hover media queries' );
        $this->assertDoesNotMatchRegularExpression( '/\.a4-env-hover[^{]*:active|:focus\s*\+\s*\.a4-env-hover/', $css );
        $outside = preg_replace( '/@media \(hover: hover\) and \(pointer: fine\)[^{]*\{((?:[^{}]*\{[^{}]*\})*)[^{}]*\}/s', '', $css );
        $this->assertStringNotContainsString( '+ .a4-env-hover', $outside, 'no show rule for touch screens or coarse pointers' );
        $this->assertStringNotContainsString( '.a4-env-hover:hover', $outside );
        $this->assertDoesNotMatchRegularExpression( '/\.a4-env[^{]*\{[^}]*clip-path:\s*inset\(50%\)/', preg_replace( '#/\*.*?\*/#s', '', $outside ), 'hidden only where a pointer can show it' );

        // dismissible: Escape (admin4.js) hides it until the pointer or the focus comes back
        $js = self::file( 'design/admin4/javascript/admin4.js' );
        $this->assertMatchesRegularExpression( "/getElementById\('a4-env-badge'\)/", $js );
        $this->assertMatchesRegularExpression( "/e\.key !== 'Escape'[^\n]*defaultPrevented/", $js, 'an open menu\'s Escape comes first' );
        $this->assertStringContainsString( "badge.classList.add('a4-env-dismissed')", $js );
        $this->assertStringContainsString( "logo.addEventListener('pointerenter', again)", $js );
        $this->assertStringContainsString( "logo.addEventListener('focus', again)", $js );

        $fade = AdminBaseStylesheetsTest::rules( self::media( $css, '(hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)' ) );
        $this->assertSame( 'a4-env-in .12s ease-out', $fade[implode( ', ', $selectors )]['animation'] );
        $this->assertMatchesRegularExpression( '/@keyframes a4-env-in\s*\{\s*from\s*\{\s*opacity:\s*0;\s*\}\s*to\s*\{\s*opacity:\s*1;\s*\}\s*\}/', $css, 'opacity only' );
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
