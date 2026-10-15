<?php
/**
 * File containing the setup/megamenu view.
 *
 * The groups and links of the Setup menu as an HTML fragment, without the page layout, for admin4's mega menu
 * (menu.ini [TopAdminMenu] MegaMenuTabs[], design/admin4/javascript/a4_mega_menu.js): the header draws only the
 * button and an empty panel, and the script asks this view once, when the panel is first opened. The links, names,
 * groups and access checks are those of the Setup menu and overview (menu.ini [Leftmenu_setup], parts/ini_menu.tpl);
 * the fragment holds no figures. Guide: doc/features/6.0/admin4-design.md ("Mega menu").
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Setup
{

class Megamenu extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // a fragment for a script: no debug report appended to it
        \eZDebug::updateSettings( array( 'debug-enabled' => false ) );
        // what it holds follows the user's rights: kept by no shared cache
        if ( !headers_sent() )
            header( 'Cache-Control: private, no-store' );

        $tpl = \eZTemplate::factory();

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/megamenu.tpl' );
        $Result['pagelayout'] = false;
        $Result['path'] = array( array( 'url' => '/setup/menu',
                                        'text' => \ezpI18n::tr( 'kernel/setup', 'Setup menu' ) ) );

        return $this->viewResult( $Result, null );
    }
}

}
