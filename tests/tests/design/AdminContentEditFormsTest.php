<?php
/**
 * admin4's content edit forms (content/edit, translate, multiedit, register), checked on the files alone (no database):
 *
 * - inside an attribute (.a4-content-attr) and the edit form (.a4-content-edit) the legacy markup of the datatype
 *   templates is drawn as the components: a field as .a4-input (--a4-control-h, --a4-text-m, the field edge and
 *   radius, the ring on focus), a button as .a4-btn (the main action filled, a disabled one faded, no hatch), a
 *   checkbox or radio as in .a4-check, a file field's button as .a4-btn; values come from the tokens only;
 * - an invalid control keeps its bad edge over the field look, forced colors get the native select back and keep
 *   the main action apart, touch screens get 16px field text;
 * - the attribute's name is a label or legend of its own class without a trailing colon, a grouped input is a
 *   fieldset without a box of its own;
 * - the datatype templates admin4 carries draw their parts in a row with a label above each field, end no label with
 *   a colon, draw no design image, no float helpers and no spacing entities, and the calendar is an icon button.
 *
 * doc/bc/6.0/admin4-styling.md, "Edit forms"
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminContentEditFormsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminBaseStylesheetsTest.php';

class AdminContentEditFormsTest extends PHPUnit\Framework\TestCase
{
    const FIELD = '.a4-content-attr :is(input, select, textarea):where(:not([type]), [type=text], [type=password], [type=email], [type=number], [type=url], [type=tel], [type=search], [type=date], [type=time], [type=datetime-local], select, textarea)';
    const BUTTONS = ':is(.a4-content-attr, .a4-content-edit) :is(input.button, input.button-disabled, button.button, input.defaultbutton)';
    const SELECT = '.a4-content-attr select:where(:not([multiple]):not([size]), [size="1"])';

    /** The datatype edit templates admin4 carries, each drawn from design/standard's with the admin4 markup */
    const DATATYPES = array( 'ezbinaryfile', 'ezdate', 'ezdatetime', 'ezimage', 'ezinisetting', 'ezmedia', 'ezmultioption', 'ezmultiprice',
        'ezobjectrelationlist', 'ezoption', 'ezpackage', 'ezprice', 'ezrangeoption', 'eztime', 'ezurl', 'ezuser' );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        return (string)file_get_contents( self::root() . '/' . $path );
    }

    /** The module stylesheet without comments */
    private static function css()
    {
        return preg_replace( '#/\*.*?\*/#s', '', self::read( 'design/admin4/stylesheets/modules/content.css' ) );
    }

    public function testBothDesignsShareTheEditFormRules()
    {
        $this->assertSame( self::read( 'design/admin4/stylesheets/modules/content.css' ), self::read( 'design/admin/stylesheets/modules/content.css' ),
            'modules/content.css is the same file in design/admin and design/admin4' );
    }

    public function testFieldsLookLikeTheInputComponent()
    {
        // the touch screen block (16px) is checked below; rules() would let it win here
        $rules = AdminBaseStylesheetsTest::rules( preg_replace( '/@media \(pointer: coarse\)\s*\{((?:[^{}]*\{[^{}]*\})*)[^{}]*\}/s', '', self::css() ) );
        $this->assertArrayHasKey( self::FIELD, $rules, 'the fields of an attribute take the .a4-input look' );
        $field = $rules[self::FIELD];
        $this->assertSame( 'var(--a4-control-h)', $field['min-height'] );
        $this->assertSame( 'var(--a4-text-m)', $field['font-size'] );
        $this->assertSame( 'var(--a4-space-1) var(--a4-space-3)', $field['padding'] );
        $this->assertSame( '1px solid var(--a4-field-line)', $field['border'] );
        $this->assertSame( 'var(--a4-radius-s)', $field['border-radius'] );
        $this->assertSame( 'var(--a4-field)', $field['background-color'] );
        $this->assertArrayNotHasKey( 'height', $field, 'a minimum height only: a textarea or list box grows' );

        $focus = $rules[self::FIELD . ':focus'] ?? null;
        $this->assertNotNull( $focus );
        $this->assertSame( 'var(--a4-accent)', $focus['border-color'] );
        $this->assertSame( '3px solid var(--a4-ring)', $focus['outline'] );

        $select = $rules[self::SELECT] ?? null;
        $this->assertNotNull( $select, 'a one-line select gets the drawn chevron' );
        $this->assertSame( 'none', $select['appearance'] );
        $this->assertStringContainsString( 'var(--a4-muted)', $select['background-image'] );
    }

    public function testAnInvalidControlKeepsItsBadEdge()
    {
        $css = self::css();
        $invalid = strpos( $css, '.a4-content-attr input[aria-invalid="true"]' );
        $this->assertNotFalse( $invalid );
        // later in the file, and with an attribute selector more than the field rule: it wins over the field look
        $this->assertGreaterThan( strpos( $css, self::FIELD ), $invalid );
        $rules = AdminBaseStylesheetsTest::rules( $css );
        $this->assertSame( 'var(--a4-bad)', $rules['.a4-content-attr input[aria-invalid="true"], .a4-content-attr select[aria-invalid="true"], .a4-content-attr textarea[aria-invalid="true"]']['border-color'] );
    }

    public function testButtonsLookLikeTheButtonComponent()
    {
        $rules = AdminBaseStylesheetsTest::rules( self::css() );
        $button = $rules[self::BUTTONS] ?? null;
        $this->assertNotNull( $button, 'the legacy buttons of an attribute and of the edit form take the .a4-btn look' );
        $this->assertSame( 'var(--a4-control-h)', $button['min-height'] );
        $this->assertSame( 'var(--a4-space-1) var(--a4-space-3)', $button['padding'] );
        $this->assertSame( 'var(--a4-radius-s)', $button['border-radius'] );
        $this->assertSame( 'var(--a4-text-s)', $button['font-size'] );
        $this->assertSame( '600', $button['font-weight'] );

        $hover = $rules[':is(.a4-content-attr, .a4-content-edit) :is(input.button, button.button):hover:not(:disabled)'];
        $this->assertSame( 'var(--a4-accent)', $hover['border-color'] );
        $this->assertSame( 'var(--a4-accent-text)', $hover['color'] );
        $primary = $rules[':is(.a4-content-attr, .a4-content-edit) input.defaultbutton'];
        $this->assertSame( 'var(--a4-accent-strong)', $primary['background-color'] );
        $this->assertSame( 'var(--a4-on-accent)', $primary['color'] );
        $disabled = $rules[':is(.a4-content-attr, .a4-content-edit) :is(input.button-disabled, input.button, button.button, input.defaultbutton):disabled'];
        $this->assertSame( '.55', $disabled['opacity'], 'a disabled button is faded like .a4-btn[disabled]' );
        $this->assertSame( 'none', $disabled['background-image'], 'no hatch inside the edit form' );

        $file = $rules['.a4-content-attr input[type=file]::file-selector-button'] ?? null;
        $this->assertNotNull( $file, "a file field's button is drawn as a button" );
        $this->assertSame( 'var(--a4-control-h)', $file['min-height'] );
    }

    public function testChecksTakeTheCheckComponentsLook()
    {
        $rules = AdminBaseStylesheetsTest::rules( self::css() );
        $check = $rules['.a4-content-attr input:is([type=checkbox], [type=radio])'] ?? null;
        $this->assertNotNull( $check );
        $this->assertSame( '18px', $check['width'] );
        $this->assertSame( '18px', $check['height'] );
        $this->assertSame( 'var(--a4-accent-strong)', $check['accent-color'] );
        $this->assertArrayHasKey( '.a4-content-attr label:has(> input:is([type=checkbox], [type=radio]))', $rules,
            'a label around a checkbox and its text is a row, not a bold block label' );
    }

    public function testEveryValueComesFromTheTokens()
    {
        $css = self::css();
        $start = strpos( $css, '.a4-content-attr-label {' );
        $this->assertNotFalse( $start );
        foreach ( AdminBaseStylesheetsTest::rules( substr( $css, $start ) ) as $selector => $declarations )
        {
            foreach ( $declarations as $property => $value )
            {
                $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,8}\b|\b(rgba?|hsla?)\(/i', $value, "$selector { $property }" );
                $this->assertStringNotContainsString( '!important', $value, "$selector { $property }" );
                if ( $property === 'font-size' )
                    $this->assertStringStartsWith( 'var(--a4-text-', $value, "$selector { $property }" );
            }
        }
    }

    public function testForcedColorsAndTouchScreens()
    {
        $css = self::css();
        $forced = AdminBaseStylesheetsTest::rules( $css, true );
        $this->assertSame( 'auto', $forced[self::SELECT]['appearance'] ?? null, 'forced colors: the native select comes back' );
        $this->assertSame( 'none', $forced[self::SELECT]['background-image'] );
        $this->assertSame( '2px', $forced[':is(.a4-content-attr, .a4-content-edit) input.defaultbutton']['border-width'] ?? null, 'the main action keeps a thicker edge' );
        $this->assertMatchesRegularExpression( '/@media \(pointer: coarse\) \{\s*' . preg_quote( self::FIELD, '/' ) . ' \{ font-size: var\(--a4-text-l\); \}/',
            preg_replace( '/\s+/', ' ', $css ), 'a phone does not zoom in on a field' );
    }

    public function testTheAttributeNameIsALabelWithoutAColon()
    {
        $tpl = self::read( 'design/admin4/templates/content/edit_attribute.tpl' );
        // view only, translation, grouped input (legend), single input
        $this->assertSame( 4, substr_count( $tpl, 'class="a4-content-attr-label' ) );
        $this->assertSame( 2, substr_count( $tpl, '<fieldset class="a4-content-attr-group"' ), 'a grouped input, edited or translated' );
        $this->assertDoesNotMatchRegularExpression( '/\{\/if\}:\s*$/m', $tpl, 'no colon after the name' );
        $this->assertStringContainsString( '<form name="editform" id="editform" class="a4-content-edit"', self::read( 'design/admin4/templates/content/edit.tpl' ) );
        $rules = AdminBaseStylesheetsTest::rules( self::css() );
        $this->assertSame( 'var(--a4-text-s)', $rules['.a4-content-attr-label']['font-size'] );
        $this->assertSame( '0', $rules['#page fieldset.a4-content-attr-group']['border'], 'no box of its own around a grouped input' );
    }

    /** The form fields a template posts: the names and the ids of its controls, and its template blocks */
    private static function formFields( $source )
    {
        $source = preg_replace( '/\{\*.*?\*\}/s', '', $source );
        preg_match_all( '/<(?:input|select|textarea)\b[^>]*?\sname="([^"]*)"/', $source, $names );
        preg_match_all( '/<(?:input|select|textarea)\b[^>]*?\sid="([^"]*)"/', $source, $ids );
        // the id prefix of a nested edit ({if ne( $attribute_base, ... )}...{/if}) is a value, not a block
        $code = preg_replace( '/\{if ne\( \$attribute_base, \'ContentObjectAttribute\' \)\}\{\$attribute_base\}-\{\/if\}/', '', $source );
        $blocks = array();
        foreach ( array( 'section', 'if', 'def', 'default', 'foreach', 'let' ) as $block )
            $blocks[$block] = preg_match_all( '/\{' . $block . '\b/', $code ) . '/' . preg_match_all( '/\{\/' . $block . '\}/', $code );
        // the table structure: a pattern that adds an attribute to <th must not turn <thead> into something else
        foreach ( array( 'table', 'thead', 'tbody', 'tr' ) as $tag )
            $blocks[$tag] = preg_match_all( '/<' . $tag . '[\s>]/', $code ) . '/' . preg_match_all( '/<\/' . $tag . '>/', $code );
        $sorted = function ( array $list ) { sort( $list ); return $list; };
        return array( 'names' => $sorted( $names[1] ), 'ids' => $sorted( $ids[1] ), 'blocks' => $blocks );
    }

    /**
     * The copies of design/standard's templates post the same fields with the same ids and keep the template blocks of
     * the original: only the markup around them changed (ezdate and ezdatetime were admin4 templates before and inline
     * their ids). A label's target may change: ezmultioption's pointed at an id that does not exist
     */
    public function testTheCopiesPostWhatTheOriginalsPost()
    {
        foreach ( array_diff( self::DATATYPES, array( 'ezdate', 'ezdatetime' ) ) as $datatype )
        {
            $original = self::formFields( self::read( "design/standard/templates/content/datatype/edit/$datatype.tpl" ) );
            $copy = self::formFields( self::read( "design/admin4/templates/content/datatype/edit/$datatype.tpl" ) );
            $this->assertNotEmpty( $copy['names'], $datatype );
            $this->assertSame( $original, $copy, "$datatype.tpl posts other fields than design/standard's" );
        }
    }

    /** The rich text editors' content follows the mode: the form names the sheets, admin4.js puts them in, tokens only */
    public function testTheEditorContentFollowsTheMode()
    {
        $this->assertStringContainsString( 'data-a4-editor-css="{\'stylesheets/components.css\'|ezdesign( \'no\' )} {\'stylesheets/editor_content.css\'|ezdesign( \'no\' )}"',
            self::read( 'design/admin4/templates/content/edit.tpl' ) );
        $js = self::read( 'design/admin4/javascript/admin4.js' );
        foreach ( array( "form[data-a4-editor-css]", "mceContentBody|mce-content-body", "doc.documentElement.setAttribute('data-a4-theme'",
                         "attributeFilter: ['data-a4-theme']", 'mce.onAddEditor.add', "mce.on('AddEditor'" ) as $needle )
            $this->assertStringContainsString( $needle, $js );
        $css = preg_replace( '#/\*.*?\*/#s', '', self::read( 'design/admin4/stylesheets/editor_content.css' ) );
        $rules = AdminBaseStylesheetsTest::rules( $css );
        $this->assertNotEmpty( $rules );
        foreach ( $rules as $selector => $declarations )
        {
            $this->assertStringStartsWith( 'html[data-a4-theme', $selector, 'nothing applies without the script' );
            foreach ( $declarations as $property => $value )
                $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,8}\b|\b(rgba?|hsla?)\(/i', $value, "$selector { $property }" );
        }
        $body = 'html[data-a4-theme] body:is(.mceContentBody, .mce-content-body)';
        $this->assertSame( 'var(--a4-field)', $rules[$body]['background-color'] );
        $this->assertSame( 'var(--a4-ink)', $rules[$body]['color'] );
    }

    public function testTheLanguagesToTranslateFromAreCheckRows()
    {
        $menu = self::read( 'design/admin4/templates/content/edit_menu.tpl' );
        $this->assertSame( 2, substr_count( $menu, '<label class="a4-check">' ), 'none and each other language: a radio row' );
        $this->assertDoesNotMatchRegularExpression( '/<label>\s*<input type="radio"/', $menu );
    }

    public function testTheDatatypeTemplatesUseTheAdminFourMarkup()
    {
        foreach ( self::DATATYPES as $datatype )
        {
            $path = "design/admin4/templates/content/datatype/edit/$datatype.tpl";
            $this->assertFileExists( self::root() . '/' . $path );
            $tpl = preg_replace( '/\{\*.*?\*\}/s', '', self::read( $path ) );
            $this->assertDoesNotMatchRegularExpression( '/:\s*<\/label>/', $tpl, "$path: a label ends without a colon" );
            $this->assertDoesNotMatchRegularExpression( '/\{\/if\}:\s*<\/label>/', $tpl, "$path: a label ends without a colon" );
            $this->assertDoesNotMatchRegularExpression( '/<label>[^<]*<\/label>/', $tpl, "$path: a heading without a control of its own is a .a4-label paragraph" );
            $this->assertStringNotContainsString( '|ezimage', $tpl, "$path: icons are .a4-icon" );
            $this->assertStringNotContainsString( 'class="element"', $tpl, "$path: parts stand in .a4-content-parts, not in floats" );
            $this->assertStringNotContainsString( 'class="break"', $tpl, $path );
            $this->assertDoesNotMatchRegularExpression( '/^\s*(&nbsp;\s*)+$/m', $tpl, "$path: spacing comes from the stylesheet, not from lines of &nbsp;" );
            $this->assertDoesNotMatchRegularExpression( '/\sstyle="/', $tpl, $path );
        }
        foreach ( array( 'ezdate', 'ezdatetime' ) as $datatype )
        {
            $tpl = self::read( "design/admin4/templates/content/datatype/edit/$datatype.tpl" );
            $this->assertStringContainsString( '<button type="button" class="a4-btn a4-btn-icon a4-content-datepicker-icon"', $tpl );
            $this->assertStringContainsString( '<span class="a4-icon a4-bi-calendar3" aria-hidden="true"></span></button>', $tpl );
        }
        foreach ( array( 'ezdate', 'eztime', 'ezuser' ) as $datatype )
            $this->assertStringContainsString( 'class="block a4-content-parts"', self::read( "design/admin4/templates/content/datatype/edit/$datatype.tpl" ) );
        foreach ( array( 'admin4', 'admin' ) as $design )
            $this->assertStringContainsString( '.a4-bi-calendar3 {', self::read( "design/$design/stylesheets/components.css" ) );
    }
}
