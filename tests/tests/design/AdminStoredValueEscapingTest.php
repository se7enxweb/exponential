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
            'sub-items class name in script' => array( 'children_detailed.tpl',
                array( 'text: "{$can_create_class.name|wash|wash(javascript)}"' ), array( 'text: "{$can_create_class.name|wash()}"' ) ),
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
