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
 *   marked), and the tree menus share one trash link;
 * - the user preferences show each setting as its name and an on/off switch;
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

    public function testTheSideColumnsDrawTheRuleOnlyBetweenBlocks()
    {
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $this->assertSame( '0', $rules[':is(#leftmenu-design, #rightmenu-design) .box-header']['border-top'] );
        $between = self::rule( $rules, ':is(#leftmenu-design, #rightmenu-design) :is(.box-bc, :has(.box-bc)) ~ .box-header' );
        $this->assertNotNull( $between, 'a header after a block draws the rule' );
        $this->assertSame( '1px solid var(--a4-line)', $between['border-top'] );
        $this->assertNotNull( self::rule( $rules, ':is(#leftmenu-design, #rightmenu-design) :is(.box-bc, :has(.box-bc)) ~ * .box-header' ),
            'also a header inside a wrapper (#content-tree) after a block' );
        // the old rule only knew a header one level deep; the setup menu's header is the column's first child
        $this->assertArrayNotHasKey( '#leftmenu-design > :first-child > .box-header:first-child, #rightmenu-design > :first-child > .box-header:first-child', $rules );
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
        $this->assertSame( 'var(--a4-control-h)', $rules['.a4-tabs.is-segmented > :is(a, button, span.current, .is-current)']['min-height'] );
        $pressed = self::rule( $rules, '.a4-tabs.is-segmented > button[aria-pressed="true"]' );
        $this->assertSame( 'var(--a4-accent-strong)', $pressed['background-color'] );
        $this->assertSame( 'var(--a4-on-accent)', $pressed['color'] );
        $forced = AdminBaseStylesheetsTest::rules( self::css( 'components.css' ), true );
        $this->assertNotNull( self::rule( $forced, '.a4-tabs button[aria-pressed="true"]' ), 'forced colors drop the fill: an outline marks it' );
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
        $this->assertSame( 2, substr_count( $preferences, '<span class="a4-tabs is-segmented" role="group" aria-labelledby="a4-pref-' ) );
        $this->assertSame( 2, substr_count( $preferences, '<span class="a4-pref-label" id="a4-pref-' ) );
        $this->assertStringNotContainsString( '&nbsp;|&nbsp;', $preferences, 'no text separator between on and off' );
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $row = $rules['#currentuserpreferences .settings > ul > li'];
        $this->assertSame( 'flex', $row['display'] );
        $this->assertSame( 'space-between', $row['justify-content'] );
    }

    public function testTheRightColumnStacksAFieldAndItsButton()
    {
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $this->assertSame( '100%', $rules['#rightmenu-design .box-content :is(select, input[type=text])']['width'] );
        $this->assertSame( 'grid', $rules['#rightmenu-design .box-content form']['display'] );
        $this->assertSame( 'flex-end', $rules['#rightmenu-design .block:has(> :is(input.button, input.defaultbutton, input.button-disabled))']['justify-content'] );
    }

    public function testTheNodeTabBoxFillsItsHeightAndClearsTheGrip()
    {
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $box = $rules['#window-controls.a4-node-tabs'];
        $this->assertSame( 'grid', $box['display'] );
        $this->assertSame( 'auto minmax(0, 1fr)', $box['grid-template-rows'], 'the tab row, then the panel in what is left' );
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
        // <name>navigationpart without the ez prefix => parts/<name>/menu.tpl, not a name two letters short
        $leftmenu = self::template( 'page_leftmenu.tpl' );
        $this->assertStringContainsString( "{elseif \$navigation_part_name|begins_with( 'ez' )|not}{set \$part_name = \$navigation_part_name|explode( 'navigationpart' )[0]}{/if}", $leftmenu );
    }
}
