<?php
/**
 * The siteaccess sub menus of the administration's popup menu (popupmenu/popup_sub_siteaccess_menus.tpl, admin and
 * admin4) are included in the pagelayout after the page's own template, whose variables are still set. With the
 * generic names $link and $siteAccessList, a page that had a $link of its own (Setup > Update > Funding: a hash per
 * funding link) made {let link=...} keep the page's value, and every menu entry printed an array: 24 "Array to string
 * conversion" warnings per page. The template uses names of its own and removes its list again. DB-less, reads the
 * template files.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group design
 */

class AdminPopupSiteAccessMenuVariablesTest extends PHPUnit\Framework\TestCase
{
    public static function designs()
    {
        return array( 'admin' => array( 'admin' ), 'admin4' => array( 'admin4' ) );
    }

    /**
     * @dataProvider designs
     */
    #[\PHPUnit\Framework\Attributes\DataProvider( 'designs' )]
    public function testTheMenuUsesVariableNamesOfItsOwn( $design )
    {
        $source = file_get_contents( dirname( __DIR__, 3 ) . "/design/$design/templates/popupmenu/popup_sub_siteaccess_menus.tpl" );
        $this->assertDoesNotMatchRegularExpression( '/\{(let|def|set)\s+\$?link\s*=/', $source, 'a generic $link collides with the page' );
        $this->assertStringNotContainsString( '$siteAccessList', $source );
        $this->assertStringContainsString( '{def $sa_menu_siteaccesses', $source );
        $this->assertStringContainsString( '{undef $sa_menu_siteaccesses}', $source );
    }
}
