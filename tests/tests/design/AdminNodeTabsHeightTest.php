<?php
/**
 * admin4's node view: the tab box (#window-controls) keeps the height the user gave it, checked on the files alone
 * (no database):
 *
 *  TH-01 - the behaviour (admin4.js with window.a4Pref of page_head_style.tpl run in node,
 *          tests/tests/design/js/tabs_height.js; skipped without node): the preference wins over the browser's copy,
 *          which wins over the default; a drag saves once at its end, keys once 400ms after the last one, an unchanged
 *          height not at all; the height is held between 10em and 60em and 85% of the window; the reset button and
 *          a return to the default set the preference empty and remove the browser's copy; a preference that cannot
 *          be saved stays in the browser
 *  TH-02 - nothing of the user's in the view-cached templates: the height reaches the page on <html>, which is written
 *          before every cache-block of the page layouts
 *  TH-03 - the grip is a focusable separator with a name, the reset button a named button with an icon of the list,
 *          both translated, and hidden until the script takes them on
 *  TH-04 - the stylesheet: the height only from 576px and only while the tabs are on, the same limits as the script,
 *          tokens only, forced colours, not printed
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminNodeTabsHeightTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminBaseStylesheetsTest.php';

class AdminNodeTabsHeightTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        return (string)file_get_contents( self::root() . '/' . $path );
    }

    private static function css( $file )
    {
        return preg_replace( '#/\*.*?\*/#s', '', self::read( 'design/admin4/stylesheets/' . $file ) );
    }

    /** The part of admin4.css that draws the control, from its first rule to the phone rule's end */
    private static function block()
    {
        $css = self::css( 'admin4.css' );
        $start = strpos( $css, 'html[data-a4-node-tabs-height]' );
        $end = strpos( $css, '.a4-tabs-height-grip:focus-visible { outline-color: Highlight; }' );
        self::assertNotFalse( $start );
        self::assertNotFalse( $end );
        return substr( $css, $start, $end - $start );
    }

    /** TH-01 */
    public function testTheHeightIsKeptAndSavedSparingly()
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
            $this->markTestSkipped( 'node is not installed' );
        $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/tabs_height.js' ) . ' '
                                   . escapeshellarg( self::root() . '/design/admin4/javascript/admin4.js' ) . ' '
                                   . escapeshellarg( self::root() . '/design/admin4/templates/page_head_style.tpl' ) );
        $s = array();
        foreach ( array_filter( explode( "\n", $out ) ) as $line )
        {
            $row = json_decode( $line, true );
            $this->assertIsArray( $row, 'the harness ran: ' . $out );
            $s[$row['name']] = $row;
        }
        $this->assertCount( 28, $s );

        // precedence: the preference, the browser's copy, the default
        $this->assertSame( '500', $s['preference wins']['applied'] );
        $this->assertSame( '700', $s['browser copy without a preference']['applied'] );
        $this->assertSame( array( '700' ), $s['browser copy without a preference']['saved'], 'taken along into the preference' );
        $this->assertNull( $s['default']['applied'] );
        $this->assertNull( $s['reset preference is the default']['applied'], 'an emptied preference does not fall back to the browser' );
        $this->assertSame( '640', $s['anonymous browser copy']['applied'] );
        $this->assertNull( $s['a value that is no height is ignored']['applied'] );
        $this->assertNull( $s['blocked storage']['applied'] );
        foreach ( array( 'default', 'reset preference is the default', 'blocked storage' ) as $name )
        {
            $this->assertTrue( $s[$name]['resetHidden'], $name . ': no reset button at the default' );
            $this->assertSame( 320, $s[$name]['height'], $name );
        }
        $this->assertFalse( $s['preference wins']['resetHidden'] );
        // the script takes the control on: the grip shows, the browser's resize grip goes
        $this->assertFalse( $s['default']['barHidden'] );
        $this->assertTrue( $s['default']['sizable'] );
        $this->assertSame( array( '160', '850', '320' ), array( $s['default']['valuemin'], $s['default']['valuemax'], $s['default']['valuenow'] ) );

        // a drag: one save, at its end
        $this->assertSame( array( '520' ), $s['drag saves once at the end']['saved'] );
        $this->assertSame( array(), $s['a click on the grip saves nothing']['saved'] );
        $this->assertSame( array( '850' ), $s['drag held at the largest height']['saved'], '60em, but at most 85% of a 1000px window' );
        $this->assertSame( array( '160' ), $s['drag held at the smallest height']['saved'], '10em, half the default' );

        // keys: one save 400ms after the last one
        $k = $s['keys save once after a rest'];
        $this->assertSame( array( 0, 0 ), array( $k['early'], $k['before'] ), 'nothing saved while the keys go on and 399ms after' );
        $this->assertSame( array( '400' ), $k['saved'], '5 x 16px' );
        $this->assertSame( array( '432' ), $s['page and shift steps']['saved'], 'Page Down 64px, Shift+Arrow Down 64px, Arrow Up 16px, as the other grips' );
        $this->assertSame( 765, $s['home and end']['end'], 'End: 85% of a 900px window' );
        $this->assertSame( array( '765', '160' ), $s['home and end']['saved'] );
        $this->assertSame( array( '336' ), $s['an unchanged height is not saved again']['saved'] );
        $this->assertSame( array(), $s['other keys do nothing']['saved'] );
        $this->assertSame( array( '336' ), $s['a waiting change is saved when the page is left']['saved'] );

        // the layouts tab draws 22em: announced as drawn, the keys step from there (352 + 64 + 16 + 16)
        $this->assertSame( '352', $s['layouts tab']['layoutsNow'] );
        $this->assertSame( array( '448' ), $s['layouts tab']['saved'] );
        $this->assertSame( array( '370' ), $s['a lost drag saves where it ended, another pointer is ignored']['saved'] );

        // back to the default: the preference is set empty, not to the default
        $this->assertSame( array( '-' ), $s['back at the default forgets the height']['saved'] );
        $this->assertTrue( $s['back at the default forgets the height']['resetHidden'] );
        $r = $s['reset forgets the height'];
        $this->assertFalse( $r['resetWasHidden'] );
        $this->assertSame( array( '-' ), $r['saved'] );
        $this->assertSame( '-', $r['pref'] );
        $this->assertNull( $r['local'], 'the browser copy is removed' );
        $this->assertNull( $r['applied'] );
        $this->assertTrue( $r['resetHidden'] );
        $this->assertSame( 'grip', $r['focus'], 'the hidden button hands the focus to the grip' );
        $this->assertNull( $s['reset on an anonymous page']['local'] );
        $d = $s['double-click on the grip forgets the height'];
        $this->assertSame( array( '-' ), $d['saved'], 'one save: the two clicks of the double-click move nothing' );
        $this->assertSame( '-', $d['pref'] );
        $this->assertNull( $d['local'] );
        $this->assertNull( $d['applied'] );
        $this->assertTrue( $d['resetHidden'] );
        $this->assertSame( 'grip', $d['focus'] );

        // a preference that could not be saved stays in this browser
        foreach ( array( 'error', 'reject', 'redirect' ) as $how )
            $this->assertSame( '480', $s["not saved ($how) keeps it in the browser"]['local'], $how );
        $this->assertNull( $s['saved: nothing in the browser']['local'] );
        $this->assertSame( array(), $s['anonymous: the browser only']['saved'] );
        $this->assertSame( '480', $s['anonymous: the browser only']['local'] );
    }

    /** TH-01: the script goes through window.a4Pref, the one place that touches the browser's storage */
    public function testTheScriptUsesThePreferenceHelper()
    {
        $js = self::read( 'design/admin4/javascript/admin4.js' );
        $this->assertStringContainsString( "var NAME = 'admin_node_preview_height', KEY = 'exp-a4-node-preview-height', DELAY = 400, STEP = 16, BIG = 64;", $js );
        $this->assertStringContainsString( "window.a4Pref.set(NAME, height === null ? '' : String(height), KEY);", $js );
        $this->assertStringNotContainsString( 'localStorage', $js );
        // the limits are admin4.css's: 10em, 60em, 85% of the window; the default is pagelayout.css's 20em (22em)
        $this->assertStringContainsString( 'var min = Math.round(em * 10);', $js );
        $this->assertStringContainsString( 'max: Math.max(min, Math.round(Math.min(em * 60, window.innerHeight * 0.85))),', $js );
        $this->assertStringContainsString( 'def: Math.round(em * 20),', $js );
        $this->assertStringContainsString( "shown: Math.round(em * (box.classList.contains('layouts-active') ? 22 : 20))", $js );
        $base = self::css( 'pagelayout.css' );
        $this->assertMatchesRegularExpression( '/#window-controls \{[^}]*height: 20em;/', $base );
        $this->assertMatchesRegularExpression( '/#window-controls\.layouts-active \{[^}]*height: 22em;/', $base );

        $head = self::read( 'design/admin4/templates/page_head_style.tpl' );
        $this->assertStringContainsString( "if (value === '') window.localStorage.removeItem(key);", $head );
        $this->assertStringContainsString( '.then(function (r) { if (!r.ok || r.redirected) fail(); }, fail);', $head );
        $this->assertStringContainsString( 'send(name, value, function () { if (root.getAttribute(attr(name)) === shown) local(key, value); });', $head );
    }

    /** TH-02 */
    public function testNoUserValueIsCached()
    {
        $attributes = self::read( 'design/admin4/templates/parts/a4_preference_attributes.tpl' );
        $this->assertStringContainsString( "'admin_navgroups', 'admin_node_preview_height' ) as \$a4_pref}", $attributes );
        foreach ( array( 'design/admin4/templates/pagelayout.tpl', 'design/admin4l/templates/pagelayout.tpl' ) as $file )
        {
            $layout = self::read( $file );
            $at = strpos( $layout, "{include uri='design:parts/a4_preference_attributes.tpl'}>" );
            $this->assertNotFalse( $at, $file );
            $this->assertLessThan( strpos( $layout, '{cache-block' ), $at, $file . ': the attributes come before the first cache-block' );
            $this->assertSame( 0, substr_count( substr( $layout, 0, $at ), '{cache-block' ), $file );
        }
        // the node view is view cached: its templates name no preference of this control, the part carries no value
        foreach ( array( 'design/admin4/templates/node/view/a4_full_body.tpl', 'design/admin4/templates/node/top_node_full.tpl' ) as $file )
        {
            $tpl = self::read( $file );
            $this->assertStringNotContainsString( 'admin_node_preview_height', $tpl, $file );
            $this->assertSame( 1, substr_count( $tpl, "{include uri='design:parts/a4_tabs_height.tpl'}" ), $file );
        }
        $part = self::read( 'design/admin4/templates/parts/a4_tabs_height.tpl' );
        $this->assertStringNotContainsString( 'ezpreference', $part );
        $this->assertStringNotContainsString( 'current_user', $part );
        // inside the box, after the tabs
        $body = self::read( 'design/admin4/templates/node/view/a4_full_body.tpl' );
        $this->assertStringContainsString( "<div id=\"window-controls\" class=\"tab-block a4-node-tabs\">\n{include uri='design:window_controls.tpl'}\n{include uri='design:parts/a4_tabs_height.tpl'}\n</div>", $body );
    }

    /** TH-03 */
    public function testTheControlsAreNamedAndReachableByKeyboard()
    {
        $part = self::read( 'design/admin4/templates/parts/a4_tabs_height.tpl' );
        $this->assertStringContainsString( '<div class="a4-tabs-height" hidden>', $part );
        $this->assertMatchesRegularExpression( '/<div class="a4-tabs-height-grip" role="separator" aria-orientation="horizontal" aria-controls="window-controls" tabindex="0" aria-label="\{\'Height of the tab panel\'\|i18n\( \'design\/admin\/node\/view\/full\' \)\|wash\}" title="/', $part );
        $this->assertStringContainsString( '<button type="button" class="a4-icon-btn a4-tabs-height-reset" hidden title="{\'Default size\'|i18n( \'design/admin/node/view/full\' )|wash}" aria-label="{\'Default size\'|i18n( \'design/admin/node/view/full\' )|wash}"><span class="a4-icon a4-bi-arrow-counterclockwise" aria-hidden="true"></span></button>', $part );
        $this->assertMatchesRegularExpression( '/^IconList\[\]=arrow-counterclockwise$/m', self::read( 'settings/design.ini' ) );

        $translations = array(
            'ger-DE' => array( 'Default size' => 'Standardgröße', 'Height of the tab panel' => 'Höhe des Reiterbereichs',
                               'Drag, or use the arrow keys, to change the height; double-click for the default size' => 'Ziehen oder mit den Pfeiltasten die Höhe ändern; Doppelklick für die Standardgröße' ),
            'eng-US' => array( 'Default size' => 'Default size', 'Height of the tab panel' => 'Height of the tab panel',
                               'Drag, or use the arrow keys, to change the height; double-click for the default size' => 'Drag, or use the arrow keys, to change the height; double-click for the default size' ),
        );
        foreach ( $translations as $locale => $messages )
        {
            $ts = new DOMDocument();
            $this->assertTrue( $ts->load( self::root() . "/share/translations/$locale/translation.ts" ), $locale );
            $xpath = new DOMXPath( $ts );
            foreach ( $messages as $source => $translation )
            {
                $found = $xpath->query( "//context[name='design/admin/node/view/full']/message[source=\"$source\"]/translation" );
                $this->assertSame( 1, $found->length, "$locale: $source" );
                $this->assertSame( $translation, $found->item( 0 )->textContent, "$locale: $source" );
            }
        }
        $this->assertStringContainsString( '<source>Default size</source>', self::read( 'share/translations/untranslated/translation.ts' ) );
    }

    /** TH-04 */
    public function testTheStylesheetHoldsTheHeight()
    {
        $css = self::css( 'admin4.css' );
        $this->assertStringContainsString( "@media screen and (min-width: 576px) {\n  html[data-a4-node-tabs-height] #window-controls:has(> .a4-tabs-height):not(:has(.tabs-content.disabled)) {\n    height: clamp(10em, var(--a4-node-tabs-height), min(60em, 85vh));\n    min-height: 10em;", $css );
        $rules = AdminBaseStylesheetsTest::rules( $css );
        $this->assertSame( 'none', $rules['#window-controls.a4-tabs-sizable']['resize'] );
        // hidden stays hidden (the display of the bar and of .a4-icon-btn would win over it), and so does the bar
        // while the tabs are off
        $this->assertSame( 'none', $rules['.a4-tabs-height[hidden], .a4-tabs-height-reset[hidden], #window-controls:has(.tabs-content.disabled) > .a4-tabs-height']['display'] );
        $this->assertSame( 'none', $rules['.a4-tabs-height-grip']['touch-action'] );
        $this->assertSame( 'ns-resize', $rules['.a4-tabs-height-grip']['cursor'] );
        $this->assertMatchesRegularExpression( '/@media screen and \(max-width: 575\.98px\) \{\s*\.a4-tabs-height \{ display: none; \}/', $css );
        $forced = AdminBaseStylesheetsTest::rules( $css, true );
        $this->assertSame( 'CanvasText', $forced['.a4-tabs-height-grip::before']['color'] );
        $this->assertSame( '1px solid CanvasText', $forced['.a4-tabs-height-grip, .a4-tabs-height-reset']['border'] );
        $this->assertMatchesRegularExpression( '/@media print \{.*\.leftmenu-resize-handle,\s*\.a4-tabs-height,/s', $css );
        // tokens only: no colour literal in the control's rules
        $this->assertDoesNotMatchRegularExpression( '/#[0-9a-fA-F]{3,8}\b|rgba?\(|hsla?\(/', self::block() );
    }
}
