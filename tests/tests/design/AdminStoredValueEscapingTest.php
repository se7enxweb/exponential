<?php
/**
 * Values that come from imported packages, settings, editors or the request are escaped where the admin templates
 * print them (admin4 review 15.10.2026): package metadata and the document error of the package viewer, the hidden
 * fields of the browse page, the template name of visual/templatecreate, workflow, shop and toolbar values, the class
 * name in the script of the sub-items table; the tree menu's class list is cached per role set, and the page layout
 * keys its cache on the section ID as a number.
 *
 * These are source-text checks over every copy (admin, admin4): each pins the escaped form and fails when the raw
 * output comes back.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminStoredValueEscapingTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminStoredValueEscapingTest extends PHPUnit\Framework\TestCase
{
    private static function copies( $path )
    {
        $files = array();
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $file = dirname( __DIR__, 3 ) . "/design/$design/templates/$path";
            if ( is_file( $file ) )
                $files["design/$design/templates/$path"] = (string)file_get_contents( $file );
        }
        self::assertCount( 2, $files, "admin and admin4 both carry $path" );
        return $files;
    }

    public static function escapedOutputProvider()
    {
        return array(
            'package version, release and type' => array( 'package/view/files.tpl',
                array( '{$package.version-number|wash}', '{$package.release-number|wash}', '[{$package.type|wash}]', "'%collectionname', \$collection.key|wash" ),
                array( '{$package.version-number}', '{$package.release-number}', '[{$package.type}]', "'%collectionname', \$collection.key )" ) ),
            'package document error' => array( 'package/view/full.tpl',
                array( "'%error', \$viewed.as_error|wash" ), array( "'%error', \$viewed.as_error )" ) ),
            'background install error' => array( 'package/install.tpl',
                array( "'%error', \$background_error|wash" ), array( "'%error', \$background_error )" ) ),
            'install error' => array( 'package/install_error.tpl',
                array( "'%element_id', \$error.element_id|wash", 'value="{$action_id|wash}"' ), array( 'value="{$action_id}"' ) ),
            'uninstall error' => array( 'package/uninstall_error.tpl',
                array( "'%element_id', \$error.element_id|wash", 'value="{$action_id|wash}"' ), array( 'value="{$action_id}"' ) ),
            'browse hidden fields' => array( 'content/browse.tpl',
                array( 'value="{$PersistentData.item|wash}"', 'value="{$browse.action_name|wash}"', 'name="{$browse.browse_custom_action.name|wash}" value="{$browse.browse_custom_action.value|wash}"' ),
                array( 'value="{$PersistentData.item}"', 'value="{$browse.action_name}"', '{$browse.browse_custom_action.name}' ) ),
            'template name' => array( 'visual/templatecreate.tpl',
                array( 'value="{$template_name|wash}"', '<option value="{$extensionName|wash}">{$extensionName|wash}</option>' ),
                array( 'value="{$template_name}"', '{$extensionName}' ) ),
            'workflow group name' => array( 'workflow/workflowlist.tpl',
                array( 'value="{$group_name|wash}"' ), array( 'value="{$group_name}"' ) ),
            'order product option' => array( 'shop/orderview.tpl',
                array( '{$:Options.item.value|wash}' ), array( '{$:Options.item.value}' ) ),
            'wait-until class names' => array( 'workflow/eventtype/edit/event_ezwaituntildate.tpl',
                array( '{$Entries.item.class_name|wash}', '{$Entries.item.classattribute_name|wash}' ),
                array( '{$Entries.item.class_name}', '{$Entries.item.classattribute_name}' ) ),
            'toolbar parameters' => array( 'visual/toolbar.tpl',
                array( '{$Parameter.description|wash}', '({$Parameter.value|wash})' ), array( '{$Parameter.description}', '({$Parameter.value})' ) ),
            'currencies' => array( 'shop/currencylist.tpl',
                array( '<td>{$currency.symbol|wash}</td>', '<td>{$currency.locale|wash}</td>', 'CurrencyList[{$currency.code|wash}]' ),
                array( '{$currency.symbol}', '{$currency.locale}', '{$currency.code}' ) ),
            'unknown currency' => array( 'error/shop/3.tpl',
                array( 'array( $preferred_currency|wash )' ), array( 'array( $preferred_currency )' ) ),
            'inactive currency' => array( 'error/shop/4.tpl',
                array( 'array( $preferred_currency|wash )' ), array( 'array( $preferred_currency )' ) ),
            // the currency code comes from the URL and reaches the template for an unknown currency as well
            'edited currency code' => array( 'shop/editcurrency.tpl',
                array( "'%currency_code', \$original_currency_code|wash", 'value="{$original_currency_code|wash}"', '{$error|wash}' ),
                array( "'%currency_code', \$original_currency_code )", 'value="{$original_currency_code}"' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('escapedOutputProvider')]
    public function testValuesAreEscaped( $path, array $escaped, array $raw )
    {
        foreach ( self::copies( $path ) as $file => $tpl )
        {
            foreach ( $escaped as $needle )
                $this->assertStringContainsString( $needle, $tpl, $file );
            foreach ( $raw as $needle )
                $this->assertStringNotContainsString( $needle, $tpl, $file );
        }
    }

    /**
     * The tree menu's root block holds the classes the user may create below the root node: without the roles and
     * limitations in its key, every user got the list of whoever rendered it first.
     */
    public function testTheTreeMenuClassListIsCachedPerRoleSet()
    {
        foreach ( self::copies( 'contentstructuremenu/content_structure_menu_dynamic.tpl' ) as $file => $tpl )
        {
            $this->assertMatchesRegularExpression( '/\{cache-block keys=array\( \$root_node_id, \$access_type, \$csm_perm_key \) expiry=0\}/', $tpl, $file );
            $this->assertStringContainsString( '$csm_perm_key = concat( $csm_user.role_id_list|implode', $tpl, $file );
            $this->assertStringContainsString( "limited_assignment_value_list|implode( ',' ) )|md5", $tpl, $file );
            $this->assertLessThan( strpos( $tpl, "'can_instantiate_class_list'" ), strpos( $tpl, '$csm_perm_key )' ), $file );
            // the menu keeps its data attributes (no inline script) and drops its variables again
            $this->assertStringNotContainsString( '<script', $tpl, $file );
            $this->assertStringContainsString( '{undef $tree_path $csm_user $csm_perm_key}', $tpl, $file );
        }
    }

    /**
     * The class names of the sub-items table's "Create" menu are shown as HTML (html: true in
     * ezajaxsubitems_expdatatable.js), so they are washed for HTML first and then for their carrier: a JavaScript
     * string in the inline script of design/admin, JSON in a data attribute in design/admin4 (no inline script since
     * the Content Security Policy wave). A class name ending in a backslash broke the inline script.
     */
    public function testSubItemsClassNamesAreEscapedForTheirContext()
    {
        $base = dirname( __DIR__, 3 );
        $admin = (string)file_get_contents( "$base/design/admin/templates/children_detailed.tpl" );
        $this->assertStringContainsString( 'text: "{$can_create_class.name|wash|wash(javascript)}"', $admin );
        $this->assertStringNotContainsString( 'text: "{$can_create_class.name|wash()}"', $admin );
        $this->assertStringContainsString( '"{$group.group_name|wash(javascript)}"', $admin );

        $admin4 = (string)file_get_contents( "$base/design/admin4/templates/children_detailed.tpl" );
        $this->assertStringNotContainsString( 'text: "', $admin4, 'admin4 carries the options as JSON, not as script' );
        $this->assertStringContainsString( "hash( 'text', \$can_create_class.name|wash(), 'value', \$can_create_class.id|int() )", $admin4 );
        $this->assertStringContainsString( 'data-create-options="{json_encode( $subitems_create_options )|wash}"', $admin4 );
    }

    /**
     * What the two layers make of a hostile class name: the JavaScript string literal holds no quote, backslash or
     * angle bracket of its own, and once the browser has read it the text is the HTML-escaped name, so the menu that
     * puts it in as HTML shows the name instead of running it.
     */
    public function testTheTwoLayersOfTheInlineScriptKeepAHostileNameInert()
    {
        $name = 'Article\\"</script><img src=x onerror=alert(1)>\'';
        $literal = eZTemplateStringOperator::javascriptString( htmlspecialchars( $name, ENT_QUOTES ) );
        $this->assertDoesNotMatchRegularExpression( '/(?<!\\\\)["\'<>]/', $literal );
        $this->assertStringNotContainsString( '</script', $literal );
        $decoded = json_decode( '"' . preg_replace_callback( '/\\\\(x[0-9A-F]{2}|0[0-7]{2})/', function ( $m )
        {
            return sprintf( '\\u%04x', $m[1][0] === 'x' ? hexdec( substr( $m[1], 1 ) ) : octdec( $m[1] ) );
        }, $literal ) . '"' );
        $this->assertSame( htmlspecialchars( $name, ENT_QUOTES ), $decoded );
    }

    /**
     * design/standard is the fallback of every design without its own package pages: the same package values are
     * escaped there, and the description, which only it printed raw.
     */
    public function testTheStandardDesignPackagePagesEscapeTheDefinition()
    {
        $base = dirname( __DIR__, 3 ) . '/design/standard/templates/package';
        foreach ( array( 'view/files.tpl', 'view/full.tpl' ) as $path )
        {
            $tpl = (string)file_get_contents( "$base/$path" );
            $this->assertStringContainsString( '{$package.version-number|wash}-{$package.release-number|wash}', $tpl, $path );
            $this->assertStringContainsString( '[{$package.type|wash}]', $tpl, $path );
            $this->assertStringNotContainsString( '{$package.version-number}', $tpl, $path );
        }
        $this->assertStringContainsString( '{$package.description|wash|nl2br}', (string)file_get_contents( "$base/view/full.tpl" ) );
        $this->assertStringContainsString( '{$:item.version-number|wash}-{$:item.release-number|wash}', (string)file_get_contents( "$base/list.tpl" ) );
        foreach ( array( 'install_error.tpl', 'uninstall_error.tpl' ) as $path )
        {
            $tpl = (string)file_get_contents( "$base/$path" );
            $this->assertStringContainsString( "'{\$package.name|wash}'", $tpl, $path );
            $this->assertStringContainsString( 'value="{$action_id|wash}"', $tpl, $path );
            $this->assertStringContainsString( "'%element_id', \$error.element_id|wash", $tpl, $path );
        }
    }

    /** Every distinct SectionID of the request wrote its own copy of the page layout cache */
    public function testThePageLayoutKeysOnTheSectionIDAsANumber()
    {
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $file = dirname( __DIR__, 3 ) . "/design/$design/templates/pagelayout.tpl";
            $this->assertFileExists( $file );
            $tpl = (string)file_get_contents( $file );
            $this->assertStringContainsString( "ezhttp( 'SectionID', 'get' )|int", $tpl, $file );
            $this->assertDoesNotMatchRegularExpression( "/ezhttp\\( 'SectionID', 'get' \\) \\)/", $tpl, $file );
        }
    }
}
