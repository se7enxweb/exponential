<?php
/**
 * admin4's legacy form controls and side columns, checked on the files alone (no database):
 *
 * - input.button, input.defaultbutton, input.button-disabled and button.button inside #page take the size of the
 *   .a4-btn components (--a4-control-h, the space and type tokens), with the base rules' weight, so every rule with
 *   one more class still wins; a select and a text field without a class of their own stand at the same height, a
 *   select with a chevron drawn in the tokens, with no weight beyond the element; forced colors get the native
 *   select back and keep the default button apart;
 * - the side columns draw the rule between two blocks only, never above the first heading however deep it is nested;
 *   their block titles have one size; their link lists have no bullets;
 * - every left menu of admin4 ends with the same width switch (three icon buttons in a POST form, the pressed one
 *   marked), drawn as one pill (.a4-tabs.is-segmented), and the tree menus share one trash link;
 * - the user preferences show each setting as its name and its on/off choice as the design's tabs (ul.tabs);
 * - the node's tab box fills its height with the open tab and keeps room for the resize grip under its last row.
 *
 * doc/bc/6.0/admin4-styling.md
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminLegacyControlsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminBaseStylesheetsTest.php';

class AdminLegacyControlsTest extends PHPUnit\Framework\TestCase
{
    const BUTTONS = ':is(input.button, input.defaultbutton, input.button-disabled, button.button):where(#page *)';
    const SELECT = 'select:where(#page *):where(:not([class])):where(:not([multiple]):not([size]), [size="1"])';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function css( $file )
    {
        return preg_replace( '#/\*.*?\*/#s', '', (string)file_get_contents( self::root() . '/design/admin4/stylesheets/' . $file ) );
    }

    private static function template( $file )
    {
        return (string)file_get_contents( self::root() . '/design/admin4/templates/' . $file );
    }

    /** The rule whose selector list holds $selector (one entry of a list counts) */
    private static function rule( array $rules, $selector )
    {
        foreach ( $rules as $list => $declarations )
        {
            if ( in_array( $selector, self::selectors( $list ), true ) )
            {
                return $declarations;
            }
        }
        return null;
    }

    /** Splits a selector list at its top-level commas, counting nested parentheses and brackets */
    private static function selectors( $list )
    {
        $parts = array();
        $depth = 0;
        $current = '';
        foreach ( str_split( $list ) as $char )
        {
            if ( $char === '(' || $char === '[' ) $depth++;
            if ( $char === ')' || $char === ']' ) $depth--;
            if ( $char === ',' && $depth === 0 )
            {
                $parts[] = trim( $current );
                $current = '';
                continue;
            }
            $current .= $char;
        }
        $parts[] = trim( $current );
        return $parts;
    }

    /** A value built from tokens only: var(--a4-*), calc() of them, 0, 1px, unitless numbers and keywords */
    private static function isTokenValue( $value )
    {
        $rest = preg_replace( '/var\(--a4-[a-z0-9-]+\)/', '', $value );
        $rest = preg_replace( '/\b(calc|linear-gradient|max|min)\(/', '(', $rest );
        $rest = preg_replace( '/(?<![\w.-])-?(0|1px|2px|\d+(\.\d+)?%?|\d*\.\d+|\d+deg)(?![\w.])/', '', $rest );
        $rest = preg_replace( '/\b(transparent|none|auto|block|flex|grid|inline-flex|column|wrap|nowrap|center|start|end|flex-end|space-between|middle|pointer|not-allowed|border-box|no-repeat|ellipsis|solid|break-word|inherit|ButtonText|GrayText|Highlight)\b/', '', $rest );
        return trim( preg_replace( '/[\s(),\/+*-]+/', ' ', $rest ) ) === '';
    }

    public function testLegacyButtonsTakeTheButtonComponentsSize()
    {
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $button = self::rule( $rules, self::BUTTONS );
        $this->assertNotNull( $button, 'admin4.css sizes the legacy buttons inside #page' );
        $this->assertSame( 'var(--a4-control-h)', $button['min-height'] );
        $this->assertSame( '0 var(--a4-space-3)', $button['padding'] );
        $this->assertSame( 'var(--a4-text-s)', $button['font-size'] );
        $this->assertSame( 'middle', $button['vertical-align'] );
        $this->assertArrayNotHasKey( 'height', $button, 'a minimum height only: a long label may wrap' );
        foreach ( $button as $property => $value )
        {
            $this->assertTrue( self::isTokenValue( $value ), "$property: $value is not built from tokens" );
            $this->assertStringNotContainsString( '!important', $value );
        }
    }

    public function testFieldsWithoutAClassStandAsHighAsTheButtons()
    {
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $field = self::rule( $rules, self::SELECT );
        $this->assertNotNull( $field );
        $this->assertSame( 'var(--a4-control-h)', $field['min-height'] );
        $text = self::rule( $rules, 'input:where(#page *):where(:not([type]), [type=text], [type=password], [type=email], [type=number], [type=url], [type=tel], [type=date], [type=time], [type=datetime-local], [type=search]):where(:not([class]), .box, .halfbox)' );
        $this->assertNotNull( $text, 'text fields without a class (or the base\'s .box/.halfbox) get the same height' );
        $this->assertSame( 'var(--a4-control-h)', $text['min-height'] );

        $select = $rules[self::SELECT];
        $this->assertSame( 'none', $select['appearance'] );
        $this->assertStringContainsString( 'var(--a4-muted)', $select['background-image'], 'the chevron in the tokens' );
        $this->assertSame( 'var(--a4-space-6)', $select['padding-right'], 'room for the chevron' );
        foreach ( array_merge( $field, $select, $text ) as $property => $value )
        {
            $this->assertTrue( self::isTokenValue( $value ), "$property: $value is not built from tokens" );
        }
    }

    public function testTheBareFieldSelectorsAddNoWeight()
    {
        // element plus :where(): a class (.a4-input, .a4-field select, the data tables', the editor's) always wins
        foreach ( array( self::SELECT, self::BUTTONS ) as $selector )
        {
            $outside = preg_replace( '/:where\((?:[^()]|\((?:[^()]|\([^()]*\))*\))*\)/', '', $selector );
            $this->assertStringNotContainsString( '#', $outside, "$selector: an id outside :where() outweighs the components" );
            $this->assertStringNotContainsString( ':not(', $outside, "$selector: a :not() outside :where() adds weight" );
        }
        $this->assertStringContainsString( ':where(:not([class]))', self::SELECT, 'a select with a class of its own is not touched' );
    }

    public function testForcedColorsGetTheNativeSelectAndAVisibleDefaultButton()
    {
        $forced = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ), true );
        $this->assertSame( 'auto', $forced[self::SELECT]['appearance'] );
        $this->assertSame( 'none', $forced[self::SELECT]['background-image'] );
        $this->assertSame( '1px solid ButtonText', $forced[self::BUTTONS]['border'] );
        $this->assertSame( '2px', $forced['input.defaultbutton:where(#page *)']['border-width'] );
    }

    public function testBlocksAreSeparatedBySpaceNotByALine()
    {
        // no admin4 stylesheet draws a line above a .box-header: blocks are separated by space
        $root = self::root();
        foreach ( glob( "$root/design/admin4/stylesheets/{,*/}*.css", GLOB_BRACE ) ?: array() as $file )
        {
            foreach ( AdminBaseStylesheetsTest::rules( preg_replace( '#/\*.*?\*/#s', '', (string)file_get_contents( $file ) ) ) as $selector => $declarations )
            {
                if ( strpos( $selector, 'box-header' ) === false )
                    continue;
                foreach ( array( 'border-top', 'border' ) as $property )
                {
                    if ( isset( $declarations[$property] ) )
                        $this->assertMatchesRegularExpression( '/^(0|none|0 none)$/', $declarations[$property], basename( $file ) . ": $selector draws a line ($property: {$declarations[$property]})" );
                }
            }
        }
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $this->assertSame( '0', $rules['.box-header']['border-top'], 'admin4.css takes the base\'s dashed line away' );
        // a heading after a block in a side column: 24px (the gutter) above it, its own 12px margin included,
        // however deep the menu template nests it
        // the space is the header's padding: margins collapse once no border stands between them (measured 12px
        // instead of 24px with a margin); the heading inside adds none, the block above ends at its content
        $between = self::rule( $rules, ':is(#leftmenu-design, #rightmenu-design) :is(.box-bc, :has(.box-bc)) ~ .box-header' );
        $this->assertNotNull( $between );
        $this->assertSame( 'var(--a4-gutter)', $between['padding-top'] );
        $this->assertSame( '0', $between['margin-top'] );
        $this->assertSame( '0', self::rule( $rules, ':is(#leftmenu-design, #rightmenu-design) :is(.box-bc, :has(.box-bc)) ~ .box-header h4' )['margin-top'] );
        $this->assertSame( '0', $rules[':is(#leftmenu-design, #rightmenu-design) .box-content > :last-child']['margin-bottom'] );
        $this->assertNotNull( self::rule( $rules, ':is(#leftmenu-design, #rightmenu-design) :is(.box-bc, :has(.box-bc)) ~ * .box-header' ),
            'also a header inside a wrapper (#content-tree) after a block' );
        $this->assertSame( 'var(--a4-space-3) 0 var(--a4-space-2)', $rules[':is(#leftmenu-design, #rightmenu-design) .box-header h4']['margin'] );
    }

    public function testTheSideColumnsShareOneTitleAndListStyle()
    {
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $title = $rules[':is(#leftmenu-design, #rightmenu-design) .box-header h4'];
        $this->assertSame( '1.25em', $title['font-size'] );
        $this->assertSame( 'auto', $title['hyphens'] );
        $this->assertSame( 'break-word', $title['overflow-wrap'] );
        $list = $rules[':is(#leftmenu, #rightmenu) :is(ul.leftmenu-items, .box-content > ul, .settings > ul)'];
        $this->assertSame( 'none', $list['list-style'], 'no bullets in any menu' );
        $this->assertSame( '0', $list['padding'] );
        // without the list margins a plain menu's links need their own 24px target (the newsletter menu)
        $this->assertSame( 'var(--a4-space-5)', $rules[':is(#leftmenu, #rightmenu) .box-content > ul > li > a']['min-height'] );
        $links = $rules['#leftmenu ul.leftmenu-items li > div > :is(a, span)'];
        $this->assertSame( 'var(--a4-space-2)', $links['padding-inline'], 'every ini menu has the grouped menu\'s inset' );
    }

    public function testEveryLeftMenuEndsWithTheSameWidthSwitch()
    {
        $menus = glob( self::root() . '/design/admin4/templates/parts/*/menu.tpl' );
        $this->assertGreaterThanOrEqual( 8, count( $menus ) );
        foreach ( $menus as $menu )
        {
            $source = (string)file_get_contents( $menu );
            $name = basename( dirname( $menu ) );
            $this->assertStringNotContainsString( 'id="widthcontrol-links"', $source, "parts/$name/menu.tpl writes its own width links" );
            $this->assertStringNotContainsString( 'id="trash"', $source, "parts/$name/menu.tpl writes its own trash link" );
            $this->assertStringNotContainsString( 'admin_left_menu_size', $source );
            $this->assertStringContainsString( "{include uri='design:parts/left_width_control.tpl'}", $source, "parts/$name/menu.tpl" );
        }
    }

    public function testTheWidthSwitchIsThreeNamedIconButtons()
    {
        $switch = self::template( 'parts/left_width_control.tpl' );
        $this->assertMatchesRegularExpression( '/<form id="widthcontrol-links" class="widthcontrol" method="post"/', $switch, 'a POST form: ezformtoken adds its token' );
        $this->assertStringContainsString( '<input type="hidden" name="Key" value="admin_left_menu_size" />', $switch );
        $this->assertStringContainsString( 'class="a4-tabs is-segmented is-icons" role="group" aria-label=', $switch );
        $this->assertStringContainsString( 'aria-pressed="{if eq( $a4_left_size, $a4_size.value )}true{else}false{/if}"', $switch );
        $this->assertStringContainsString( '<span class="a4-sr-only">{$a4_size.name|wash}</span>', $switch, 'each button is named' );
        $this->assertStringContainsString( 'title="{$a4_size.name|wash}"', $switch, 'and shows its name as tooltip' );
        $this->assertStringContainsString( '<button type="submit" name="Value" value="{$a4_size.value}"', $switch );
        $this->assertStringContainsString( 'name="RedirectURIAfterSet"', $switch, 'back to this page also without a Referer' );
        $menubar = (string)file_get_contents( self::root() . '/design/admin4/javascript/ezadmin_menubar.js' );
        $this->assertStringNotContainsString( '#widthcontrol-links a', $menubar, 'the switch has no links to catch any more' );
        $components = self::css( 'components.css' );
        foreach ( array( 'small' => 'arrows-collapse-vertical', 'medium' => 'layout-sidebar-inset', 'large' => 'arrows-expand-vertical' ) as $value => $icon )
        {
            $this->assertMatchesRegularExpression( "/'value', '$value',\s+'icon', '$icon'/", $switch );
            $this->assertFileExists( self::root() . "/share/icons/bootstrap-icons/svg/$icon.svg" );
            $this->assertStringContainsString( ".a4-bi-$icon { -webkit-mask-image: url(../../../share/icons/bootstrap-icons/svg/$icon.svg);", $components );
        }
        $this->assertSame( 3, substr_count( $switch, "hash( 'value'," ) );
    }

    public function testTheSegmentedSwitchMarksItsPressedButton()
    {
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'components.css' ) );
        $this->assertSame( 'calc(var(--a4-control-h) - 6px)', $rules['.a4-tabs.is-segmented > :is(a, button, span.current, .is-current)']['min-height'] );
        $pressed = self::rule( $rules, '.a4-tabs.is-segmented > :is(span.current, a.current, .is-current, [aria-pressed="true"], [aria-current="true"], [aria-current="page"])' );
        $this->assertNotNull( $pressed, 'one rule marks the current choice of buttons and of links' );
        $this->assertSame( 'var(--a4-card)', $pressed['background-color'], 'tinted over the card, not filled' );
        $this->assertSame( 'linear-gradient(var(--a4-accent-soft), var(--a4-accent-soft))', $pressed['background-image'], 'the tint over the card keeps the text at 4.5:1' );
        $this->assertSame( 'var(--a4-accent-text)', $pressed['color'] );
        $this->assertSame( 'var(--a4-accent-text)', $pressed['border-color'], 'an edge of 3:1 on the track in both modes' );
        $forced = AdminBaseStylesheetsTest::rules( self::css( 'components.css' ), true );
        $this->assertNotNull( self::rule( $forced, '.a4-tabs button[aria-pressed="true"]' ), 'forced colors drop the fill: an outline marks it' );
    }

    /** The segmented switch is one pill: a track around every choice, each choice round inside it, no joined boxes */
    public function testTheSegmentedSwitchIsOnePill()
    {
        $components = self::css( 'components.css' );
        $rules = AdminBaseStylesheetsTest::rules( $components );
        $track = $rules['.a4-tabs.is-segmented'];
        $this->assertSame( '999px', $track['border-radius'] );
        $this->assertSame( '1px solid var(--a4-line-strong)', $track['border'] );
        $this->assertSame( 'var(--a4-soft)', $track['background-color'] );
        $this->assertSame( 'var(--a4-control-h)', $track['min-height'], 'as high as the other controls' );
        $this->assertSame( '2px', $track['padding'] );
        $this->assertSame( 'nowrap', $track['flex-wrap'] );
        $choice = $rules['.a4-tabs.is-segmented > :is(a, button, span.current, .is-current)'];
        $this->assertSame( '999px', $choice['border-radius'] );
        $this->assertSame( '1px solid transparent', $choice['border'], 'no lines between the choices' );
        $this->assertSame( 'transparent', $choice['background-color'] );
        // 1px edge + 2px inset above and below: the choice fills the track exactly
        $this->assertSame( 'calc(var(--a4-control-h) - 6px)', $choice['min-height'] );
        foreach ( array_keys( $rules ) as $selector )
        {
            $this->assertStringNotContainsString( '.a4-tabs.is-segmented > :first-child', $selector, 'no joined corners any more' );
            $this->assertStringNotContainsString( '.a4-tabs.is-segmented > :last-child', $selector );
            $this->assertStringNotContainsString( '.a4-tabs.is-segmented > :not(:first-child)', $selector, 'no -1px overlap' );
        }
        $focus = $rules['.a4-tabs.is-segmented > :is(a, button):focus-visible'];
        $this->assertSame( '3px solid var(--a4-ring)', $focus['outline'] );
        $this->assertSame( '1', $focus['z-index'], 'the ring is drawn over the neighbours' );
        $this->assertSame( 'var(--a4-accent-text)', $rules['.a4-tabs.is-segmented > :is(a, button):hover']['color'] );
        $icons = $rules['.a4-tabs.is-segmented.is-icons > button'];
        $this->assertSame( 'calc(var(--a4-control-h) + 2px)', $icons['min-width'] );
        $forced = AdminBaseStylesheetsTest::rules( $components, true );
        $this->assertSame( 'CanvasText', $forced['.a4-tabs.is-segmented']['border-color'] );
        $current = self::rule( $forced, '.a4-tabs.is-segmented > :is(span.current, a.current, .is-current, [aria-pressed="true"], [aria-current="true"], [aria-current="page"])' );
        $this->assertSame( 'Highlight', $current['border-color'] );
        $this->assertSame( '2px solid Highlight', $current['outline'] );
        $this->assertSame( 'none', $current['background-image'], 'no tint left over the system colours' );
        $focus = $forced['.a4-tabs.is-segmented > :is(a, button):focus-visible'];
        $this->assertSame( '3px solid CanvasText', $focus['outline'], 'the focus ring is not replaced by the current outline' );
        $this->assertSame( '1px', $focus['outline-offset'] );
        $order = array_keys( $forced );
        $this->assertGreaterThan( array_search( '.a4-tabs.is-segmented > :is(span.current, a.current, .is-current, [aria-pressed="true"], [aria-current="true"], [aria-current="page"])', $order, true ),
                                  array_search( '.a4-tabs.is-segmented > :is(a, button):focus-visible', $order, true ), 'the focus rule comes after the current rule' );
        $this->assertFileEquals( self::root() . '/design/admin4/stylesheets/components.css', self::root() . '/design/admin/stylesheets/components.css', 'design/admin keeps a copy' );
    }

    /**
     * Every segmented switch answers the arrow keys (admin4.js, run in node by tests/tests/design/js/segmented_switch.js):
     * Left/Right to the previous/next choice (round, a disabled one skipped), Home/End to the first/last; Up/Down,
     * a key with Shift and a button outside a switch are left alone. ezoe's engine switch is the same pill.
     */
    public function testTheSegmentedSwitchesAnswerTheArrowKeys()
    {
        $engine = (string)file_get_contents( self::root() . '/extension/ezoe/design/standard/templates/content/datatype/edit/ezxmltext_ezoe_engine_switch.tpl' );
        $this->assertStringContainsString( '<span class="ezoe-engine-switch-options a4-tabs is-segmented">', $engine, 'the engine switch is the shared pill' );
        $this->assertStringContainsString( 'role="group"', self::template( 'parts/left_width_control.tpl' ) );
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
        {
            $this->markTestSkipped( 'node is not installed' );
        }
        $out = shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/segmented_switch.js' ) . ' '
                           . escapeshellarg( self::root() . '/design/admin4/javascript/admin4.js' ) );
        $s = json_decode( (string)$out, true );
        $this->assertIsArray( $s, 'the harness ran: ' . $out );
        $k = $s['keys'];
        $this->assertSame( array( 'to' => 'medium', 'prevented' => true ), $k['right'] );
        $this->assertSame( array( 'to' => 'small', 'prevented' => true ), $k['rightFromLastSkipsDisabled'], 'round, the disabled choice skipped' );
        $this->assertSame( array( 'to' => 'large', 'prevented' => true ), $k['left'] );
        $this->assertSame( 'small', $k['home']['to'] );
        $this->assertSame( 'large', $k['end']['to'] );
        $this->assertSame( array( 'to' => 'small', 'prevented' => false ), $k['down'], 'Up and Down keep scrolling the page' );
        $this->assertFalse( $k['shift']['prevented'] );
        $this->assertFalse( $k['outside']['prevented'], 'only the choices of a switch' );
    }

    public function testTheTreeMenusShareOneTrashLink()
    {
        $trash = self::template( 'parts/left_trash.tpl' );
        $this->assertStringContainsString( '<span class="a4-icon a4-bi-trash" aria-hidden="true"></span><span>', $trash );
        foreach ( array( 'content', 'media', 'user' ) as $part )
        {
            $this->assertStringContainsString( "{include uri='design:parts/left_trash.tpl'}", self::template( "parts/$part/menu.tpl" ) );
        }
    }

    public function testThePreferencesAreANameAndAnOnOffSwitch()
    {
        $preferences = self::template( 'toolbar/full/admin_preferences.tpl' );
        $this->assertSame( 1, substr_count( $preferences, '<span class="a4-pref-label" id="{$a4_pref.id}">' ), 'one label per preference, drawn in a loop' );
        $this->assertStringNotContainsString( '&nbsp;|&nbsp;', $preferences, 'no text separator between on and off' );
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $row = $rules['#currentuserpreferences .settings > ul > li'];
        $this->assertSame( 'flex', $row['display'] );
        $this->assertSame( 'space-between', $row['justify-content'] );
    }

    /**
     * The on/off choice of a preference is drawn as the design's tabs (ul.tabs, the current one in li.selected), not
     * as a segmented button switch: a group named by the preference's label around the list; the two states are the
     * buttons of a POST form to user/preferences/set (as the width switch), the current one aria-pressed="true".
     */
    public function testThePreferenceSwitchesAreTabs()
    {
        $preferences = self::template( 'toolbar/full/admin_preferences.tpl' );
        $markup = preg_replace( '/\{\*.*?\*\}/s', '', $preferences );
        $this->assertStringNotContainsString( 'is-segmented', $preferences, 'tabs, not the segmented button switch' );
        $this->assertSame( 1, preg_match_all( '#<div class="a4-pref-tabs" role="group" aria-labelledby="\{\$a4_pref\.id\}"><ul class="tabs">#', $markup ) );
        $this->assertStringContainsString( '<span class="a4-pref-label" id="{$a4_pref.id}">', $markup, 'the group is named by the visible label' );
        $this->assertSame( 2, preg_match_all( "#'key', 'admin_edit_show_(locations|re_edit)', 'id', 'a4-pref-[a-z-]+'#", $markup ) );
        $this->assertStringNotContainsString( '<ul class="tabs" role=', $preferences, 'the list keeps its list role; the group is the div around it' );
        // the choice is a POST form (as the width switch): no GET link writes a preference, the state is aria-pressed
        $this->assertStringNotContainsString( '/user/preferences/set/admin_edit', $markup, 'no GET link changes a preference' );
        $this->assertStringContainsString( "<form class=\"a4-pref-form\" method=\"post\" action={'/user/preferences/set'|ezurl}>", $markup );
        $this->assertStringContainsString( '<input type="hidden" name="Key" value="{$a4_pref.key}" />', $markup );
        $this->assertStringContainsString( 'name="RedirectURIAfterSet"', $markup );
        $this->assertSame( 2, preg_match_all( '#<button type="submit" name="Value" value="[01]" title="\{\$a4_pref\.title_(on|off)\}" aria-pressed="\{if \$a4_pref_on\}(true|false)\{else\}(true|false)\{/if\}">#', $markup ) );
        $this->assertStringNotContainsString( 'aria-current=', $markup, 'the state of a toggle is aria-pressed, not aria-current' );

        $css = self::css( 'admin4.css' );
        $rules = AdminBaseStylesheetsTest::rules( $css );
        $selected = self::rule( $rules, '#currentuserpreferences .a4-pref-tabs ul.tabs li.selected button' );
        $this->assertNotNull( $selected, 'the pressed state takes the selected tab\'s look' );
        $this->assertSame( 'var(--a4-accent-soft)', $selected['background-color'] );
        $this->assertSame( 'var(--a4-accent-text)', $selected['color'] );
        $this->assertSame( 'inset 0 -2px 0 var(--a4-accent)', $selected['box-shadow'] );
        $button = self::rule( $rules, '#currentuserpreferences .a4-pref-tabs ul.tabs li button' );
        $this->assertSame( 'var(--a4-muted)', $button['color'] );
        $this->assertSame( '0', $button['border'], 'no native button face' );
        $tab = $rules['#currentuserpreferences .a4-pref-tabs ul.tabs li :is(a, span.current, button)'];
        $this->assertSame( 'var(--a4-space-5)', $tab['min-height'], 'a 24px target (WCAG 2.5.8)' );
        $this->assertSame( 'none', $rules['#currentuserpreferences .a4-pref-tabs ul.tabs li']['float'] );
        foreach ( $rules as $selector => $declarations )
        {
            if ( strpos( $selector, '.a4-pref-tabs' ) === false )
            {
                continue;
            }
            foreach ( $declarations as $property => $value )
            {
                $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,8}\b|\brgba?\(|\bhsla?\(/i', $value, "$selector { $property }: tokens, no literal colours" );
            }
        }
        $forced = AdminBaseStylesheetsTest::rules( $css, true );
        $this->assertSame( '2px solid Highlight', $forced['ul.tabs li.selected :is(a, span.disabled, span.current)']['outline'] ?? null,
            'forced colors drop the tint and the bar: an outline marks the current tab' );
    }

    public function testTheRightColumnStacksAFieldAndItsButton()
    {
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        // only the core tool boxes: an extension's form in the right column keeps its own layout
        $tools = ':is(#clearcache-tool, #quicksettings-tool, #bookmarks)';
        $this->assertSame( '100%', $rules[$tools . ' .box-content :is(select, input[type=text])']['width'] );
        $this->assertSame( 'grid', $rules[$tools . ' .box-content form']['display'] );
        $this->assertSame( 'flex-end', $rules[$tools . ' .block:has(> :is(input.button, input.defaultbutton, input.button-disabled))']['justify-content'] );
        foreach ( array_keys( $rules ) as $selector )
        {
            $this->assertStringNotContainsString( '#rightmenu-design .box-content form', $selector, 'every right column form' );
        }
    }

    public function testTheNodeTabBoxFillsItsHeightAndClearsTheGrip()
    {
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $this->assertStringContainsString( "#window-controls.a4-node-tabs {\n  display: grid;\n  grid-template-columns: minmax(0, 1fr) auto auto;\n  grid-template-rows: auto minmax(0, 1fr);\n}",
            self::css( 'admin4.css' ), 'the tab row, then the panel in what is left' );
        $panel = $rules['#window-controls.a4-node-tabs > .tabs-content'];
        $this->assertSame( 'auto', $panel['height'], 'not the base\'s 100% - 2.5em' );
        $this->assertSame( '0', $panel['min-height'] );
        $tab = $rules['#window-controls.a4-node-tabs > .tabs-content > .tab-content'];
        $this->assertSame( '0', $tab['min-height'] );
        $this->assertSame( 'var(--a4-space-5)', $tab['padding-bottom'], 'the last row clears the resize grip' );
    }

    public function testTheOrderingFormUsesTheBareControls()
    {
        // the sort field, the direction and "Set" have no class of their own (the button: button/button-disabled),
        // so the rules above reach them
        $ordering = self::template( 'ordering.tpl' );
        $this->assertMatchesRegularExpression( '/<select id="ezasi-sort-field" name="SortingField"(?![^>]*class=)/', $ordering );
        $this->assertMatchesRegularExpression( '/<select id="ezasi-sort-order" name="SortingOrder"(?![^>]*class=)/', $ordering );
        $this->assertStringContainsString( '{if $disabled}class="button-disabled"{else}class="button"{/if}', $ordering );
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $this->assertNotNull( self::rule( $rules, ':is(select, input):where(#page *) + :is(input.button, input.defaultbutton, input.button-disabled, button.button, select):where(#page *)' ),
            'two selects and their button stand a small step apart' );
    }

    public function testAnExtensionsNavigationPartFindsItsMenu()
    {
        // <name>navigationpart without the ez prefix => parts/<name>/menu.tpl, not a name two letters short; the same
        // rule in admin4l's left menu block (the admin siteaccess draws its left column through it)
        foreach ( array( 'admin4' => self::template( 'page_leftmenu.tpl' ),
                         'admin4l' => (string)file_get_contents( self::root() . '/design/admin4l/templates/explayouts/block/admin_left_menu.tpl' ) )
                  as $design => $leftmenu )
        {
            $this->assertStringContainsString( "{elseif \$navigation_part_name|begins_with( 'ez' )|not}{set \$part_name = \$navigation_part_name|explode( 'navigationpart' )[0]}{/if}", $leftmenu, $design );
            // the exp prefix is cut only from the kernel's own parts, named in a list
            $this->assertStringContainsString( "hash( 'expauditnavigationpart', 'audit' )", $leftmenu, $design );
            $this->assertStringNotContainsString( "begins_with( 'exp' )", $leftmenu, $design );
        }
    }
}
