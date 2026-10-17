<?php
/**
 * content.ini [EditSettings] AttributeTemplates[]: the templates an extension adds to every field of the content edit
 * form (expai's bar of field actions), included by content/edit_attribute.tpl of the admin designs, so they are drawn
 * with the page and the form does not move down when the scripts start (a layout shift of 0.047 at 1440px before,
 * when expai.js added its bars). Checked on the files alone (no database):
 *
 *  EA-01 - settings/content.ini declares the list, empty by default
 *  EA-02 - both copies (design/admin, design/admin4) read it once, render it once per field and output it after the
 *          label (a plain field and a field shown read-only), or first in the block (a field with its own fieldset or
 *          being translated), where expai.js put its bars
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminEditAttributeTemplatesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminEditAttributeTemplatesTest extends PHPUnit\Framework\TestCase
{
    const COPIES = array( 'design/admin/templates/content/edit_attribute.tpl', 'design/admin4/templates/content/edit_attribute.tpl' );

    private static function read( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    public function testTheListIsDeclaredEmpty()
    {
        $ini = self::read( 'settings/content.ini' );
        $this->assertMatchesRegularExpression( '/\[EditSettings\][^\[]*^AttributeTemplates\[\]$/m', $ini );
        $this->assertDoesNotMatchRegularExpression( '/^AttributeTemplates\[\]=/m', $ini, 'no template by default' );
    }

    public function testBothCopiesIncludeTheTemplatesWhereTheScriptsPutTheirBars()
    {
        foreach ( self::COPIES as $path )
        {
            $tpl = self::read( $path );
            $this->assertSame( 1, substr_count( $tpl, "ezini( 'EditSettings', 'AttributeTemplates', 'content.ini' )" ), "$path reads the list once" );
            $this->assertStringContainsString( "ezini_hasvariable( 'EditSettings', 'AttributeTemplates', 'content.ini' )", $tpl, "$path: a settings tree without it" );
            $this->assertSame( 1, substr_count( $tpl, "{set-block variable=exp_attribute_hook}{foreach \$exp_attribute_templates as \$exp_attribute_template}{include uri=concat( 'design:', \$exp_attribute_template )}{/foreach}{/set-block}" ),
                               "$path renders the templates once per field" );
            // first in the block for a field with its own fieldset or being translated, not for a read-only one
            $this->assertMatchesRegularExpression( '/\{\/set-block\}\n\{if and\( [^\n]*\|not, or\( \$is_translating_content, \$attribute\.display_info\.edit\.grouped_input \) \)\}\{\$exp_attribute_hook\}\{\/if\}/', $tpl, $path );
            // after the label of a read-only field and of a plain field
            $this->assertSame( 2, preg_match_all( '#</label>\n\s*\{\$exp_attribute_hook\}\n#', $tpl ), "$path: after two labels" );
            $this->assertSame( 3, substr_count( $tpl, '{$exp_attribute_hook}' ), "$path: three places, one per kind of field" );
            $this->assertStringContainsString( '$exp_attribute_hook}', substr( $tpl, strpos( $tpl, '{undef $contentclass_attribute' ) ), "$path undefines it per field" );
            $this->assertStringContainsString( '{undef $exp_attribute_templates}', $tpl );
        }
    }
}
