<?php
/**
 * admin4's version preview (content/versionview) in the width of a device, checked on the files alone (no database):
 *
 * - a segmented pill of four devices (iPhone 390 x 844, iPad 820 x 1180, Desktop 1280 x 800, Large Desktop
 *   1920 x 1080), each a named button with aria-pressed, and a landscape switch for phone and tablet;
 * - the iframe still asks content/versionview for the chosen siteaccess, so it shows the site's own rendering, and
 *   fills the column without JavaScript; the bar stays hidden until the script shows it;
 * - the script keeps the choice in localStorage only inside try/catch, presses one device, disables landscape for
 *   the desktops and scales the iframe down when the column is narrower than the device;
 * - the stage and the frame take the tokens, in both copies of modules/content.css, and the icons are unchanged
 *   Bootstrap icons with their mask rules and IconList[] entries;
 * - the new texts have their German translation.
 *
 * doc/features/6.0/admin4-design.md ("Version preview"), doc/bc/6.0/admin4-styling.md
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminPreviewDevicesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminBaseStylesheetsTest.php';

class AdminPreviewDevicesTest extends PHPUnit\Framework\TestCase
{
    const TEMPLATE = 'design/admin4/templates/content/view/versionview.tpl';

    /** device id => icon, width, height, name */
    const DEVICES = array(
        'phone'   => array( 'phone',   390,  844,  'iPhone' ),
        'tablet'  => array( 'tablet',  820,  1180, 'iPad' ),
        'desktop' => array( 'laptop',  1280, 800,  'Desktop' ),
        'large'   => array( 'display', 1920, 1080, 'Large Desktop' ),
    );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        return (string)file_get_contents( self::root() . '/' . $path );
    }

    /** The script of the version view (between {literal} and {/literal}) */
    private static function script()
    {
        $template = self::read( self::TEMPLATE );
        $start = strpos( $template, '{literal}' );
        $end = strpos( $template, '{/literal}' );
        return substr( $template, $start, $end - $start );
    }

    public function testTheDevicesAreASegmentedPillOfNamedButtons()
    {
        $template = self::read( self::TEMPLATE );
        $this->assertStringContainsString( '<div class="a4-content-preview-bar" id="a4-content-preview-bar" hidden', $template, 'hidden until the script runs' );
        $this->assertStringContainsString( '<div class="a4-tabs is-segmented a4-content-preview-devices" role="group" aria-label="{\'Preview size\'|i18n( \'design/admin/content/view/versionview\' )|wash}">', $template );
        foreach ( self::DEVICES as $id => list( $icon, $width, $height, $name ) )
        {
            $this->assertMatchesRegularExpression( "/hash\\( 'id', '$id',\\s+'icon', '$icon',\\s+'width', $width,\\s+'height', $height,\\s+'name', '$name'\\|i18n\\( 'design\\/admin\\/content\\/view\\/versionview' \\) \\)/", $template, $name );
        }
        $this->assertSame( 4, substr_count( $template, "hash( 'id', '" ) );
        $button = '<button type="button" data-a4-device="{$a4_device.id}" data-width="{$a4_device.width}" data-height="{$a4_device.height}" aria-pressed="false" title="';
        $this->assertStringContainsString( $button, $template, 'a button with its size, not pressed before the script chose one' );
        $this->assertStringContainsString( '<span class="a4-icon a4-bi-{$a4_device.icon}" aria-hidden="true"></span><span class="a4-content-preview-device-name">{$a4_device.name|wash}</span></button>', $template, 'named by visible text' );
        $this->assertStringContainsString( '<button type="button" data-a4-landscape aria-pressed="false" title="', $template );
        $this->assertStringContainsString( '<span class="a4-icon a4-bi-phone-flip" aria-hidden="true"></span>', $template );
        $this->assertStringContainsString( '<p class="a4-content-preview-size"></p>', $template );
    }

    public function testTheIframeShowsTheSiteAndFillsTheColumnWithoutScript()
    {
        $template = self::read( self::TEMPLATE );
        $this->assertStringContainsString( '<div class="mainobject-window a4-content-preview-stage" id="a4-content-preview-stage">', $template );
        $this->assertSame( 1, substr_count( $template, '<iframe' ) );
        $this->assertMatchesRegularExpression( '/<div class="a4-content-preview-device">\s*<iframe\s+src=\{concat\("content\/versionview\/",\$object\.id,"\/",\$view_version\.version,"\/",\$language, "\/site_access\/", \$siteaccess, \$view_mode_uri \)\|ezurl\}\s+width="100%" height="800"/', $template,
                                               'the site\'s own rendering, for the chosen siteaccess; the column\'s width without the script' );
    }

    public function testTheScriptKeepsTheChoiceAndScalesTheFrame()
    {
        $script = self::script();
        $this->assertStringContainsString( "KEY = 'exp-admin4-preview-device', TURN_KEY = 'exp-admin4-preview-landscape'", $script );
        $this->assertStringContainsString( 'function load( key ) { try { return window.localStorage.getItem( key ); } catch ( e ) { return null; } }', $script );
        $this->assertStringContainsString( 'function save( key, value ) { try { window.localStorage.setItem( key, value ); } catch ( e ) {} }', $script );
        $this->assertSame( 1, substr_count( $script, 'localStorage.getItem' ) );
        $this->assertSame( 1, substr_count( $script, 'localStorage.setItem' ), 'every storage access is inside load() or save()' );
        $this->assertStringContainsString( "initial = bar.querySelector( 'button[data-a4-device=\"desktop\"]' )", $script, 'Desktop without a stored choice' );
        $this->assertStringContainsString( "if ( !current || !width || ( force !== true && width === lastWidth ) ) return;", $script, 'no redraw loop, no drawing into a hidden stage' );
        $this->assertStringContainsString( "setAttribute( 'aria-pressed', buttons[j] === button ? 'true' : 'false' )", $script, 'one device pressed' );
        $this->assertStringContainsString( 'turn.disabled = !turnable();', $script, 'landscape for phone and tablet only' );
        $this->assertStringContainsString( 'scale = Math.min( 1, Math.max( room, 1 ) / w )', $script, 'scaled down, never up' );
        $this->assertStringContainsString( "frame.style.transform = scale < 1 ? 'scale(' + scale + ')' : '';", $script );
        $this->assertStringContainsString( 'bar.hidden = false;', $script );
        $this->assertStringContainsString( 'new ResizeObserver( draw ).observe( stage )', $script, 'follows the column\'s width' );
    }

    public function testTheStageTakesTheTokensInBothCopies()
    {
        $css = self::read( 'design/admin4/stylesheets/modules/content.css' );
        $this->assertSame( $css, self::read( 'design/admin/stylesheets/modules/content.css' ), 'design/admin keeps a copy' );
        $rules = AdminBaseStylesheetsTest::rules( preg_replace( '#/\*.*?\*/#s', '', $css ) );
        $this->assertSame( 'none', $rules['.a4-content-preview-bar[hidden]']['display'] );
        $this->assertSame( 'var(--a4-soft)', $rules['.a4-content-preview-stage.is-device']['background-color'] );
        $frame = $rules['.a4-content-preview-stage.is-device .a4-content-preview-device'];
        $this->assertSame( 'content-box', $frame['box-sizing'], 'the script sizes the page, the edge comes on top' );
        $this->assertSame( '1px solid var(--a4-line-strong)', $frame['border'] );
        $this->assertSame( 'var(--a4-radius-s)', $frame['border-radius'] );
        $this->assertSame( '0 0', $rules['.a4-content-preview-stage.is-device .a4-content-preview-device iframe']['transform-origin'] );
        $disabled = $rules['.a4-content-preview-bar .a4-tabs.is-segmented > button:disabled, .a4-content-preview-bar .a4-tabs.is-segmented > button:disabled:hover'];
        $this->assertSame( 'var(--a4-disabled)', $disabled['color'] );
        $this->assertMatchesRegularExpression( '/@container \(max-width: 640px\) \{\s*\.a4-content-preview-bar \.a4-content-preview-device-name \{[^}]*position: absolute;[^}]*clip: rect\(0 0 0 0\);/', $css,
                                               'a narrow column shows the icons only; the names stay for screen readers' );
        $forced = AdminBaseStylesheetsTest::rules( preg_replace( '#/\*.*?\*/#s', '', $css ), true );
        $this->assertSame( 'CanvasText', $forced['.a4-content-preview-stage.is-device .a4-content-preview-device']['border-color'] );
    }

    public function testTheIconsAreBootstrapIconsWithTheirRules()
    {
        $components = self::read( 'design/admin4/stylesheets/components.css' );
        $ini = self::read( 'settings/design.ini' );
        $icons = array_merge( array_map( function ( $device ) { return $device[0]; }, array_values( self::DEVICES ) ), array( 'phone-flip' ) );
        foreach ( $icons as $icon )
        {
            $svg = self::read( "share/icons/bootstrap-icons/svg/$icon.svg" );
            $this->assertStringStartsWith( '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-' . $icon . '" viewBox="0 0 16 16">', $svg, $icon );
            $this->assertStringContainsString( ".a4-bi-$icon { -webkit-mask-image: url(../../../share/icons/bootstrap-icons/svg/$icon.svg); mask-image: url(../../../share/icons/bootstrap-icons/svg/$icon.svg); }", $components );
            $this->assertMatchesRegularExpression( '/^IconList\[\]=' . preg_quote( $icon, '/' ) . '$/m', $ini );
        }
    }

    public function testTheTextsAreTranslated()
    {
        $ts = self::read( 'share/translations/ger-DE/translation.ts' );
        $start = strpos( $ts, '<name>design/admin/content/view/versionview</name>' );
        $this->assertNotFalse( $start );
        $context = substr( $ts, $start, strpos( $ts, '</context>', $start ) - $start );
        foreach ( array( 'Preview size' => 'Vorschaugröße', 'Large Desktop' => 'Großer Desktop', 'Landscape' => 'Querformat',
                         '%width × %height px, shown at %scale %' => '%width × %height px, angezeigt mit %scale %' ) as $source => $translation )
        {
            $this->assertStringContainsString( "<source>$source</source>\n        <translation>$translation</translation>", $context, $source );
        }
    }
}
