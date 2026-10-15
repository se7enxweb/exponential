<?php
/**
 * File containing the user/megamenu view.
 *
 * The groups and links of a top tab's menu as an HTML fragment, without the page layout, for admin4's mega menu
 * (menu.ini [TopAdminMenu] MegaMenuTabs[], design/admin4/javascript/a4_mega_menu.js): the header draws only the
 * button and an empty panel, and the script asks this view once, when the panel is first opened. The tab is the
 * view's one parameter (user/megamenu/<tab>); its menu is drawn by the template its [Topmenu_<tab>] MegaMenuTemplate
 * names, through parts/ini_menu.tpl, with the access checks of the left menu; the fragment holds no figures.
 *
 * Its policy is user/login (any user of the siteaccess), because the tabs it serves belong to different modules; the
 * rights are the tab's and the links': the view answers only for a tab named in MegaMenuTabs[] with a
 * MegaMenuTemplate, and only while that tab is in the user's top menu, enabled and open to the user, as the topmenu
 * operator decides it for the header (Tabs[], HiddenTabs[], SetupTabs[], PolicyList[] and CheckViewAccess of the tab's
 * address), in the context of the admin's pages (navigation), and never for the anonymous user; every link in it is
 * then checked as in the left menu (PolicyList_<link>[], CheckViewAccess).
 * Guide: doc/features/6.0/admin4-design.md ("Mega menu").
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\User
{

class Megamenu extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $tab = isset( $Params['Tab'] ) ? (string)$Params['Tab'] : '';

        $ini = \eZINI::instance( 'menu.ini' );
        $template = self::templateFor( $tab, $ini );
        if ( $template === null )
            return $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
        // user/login is a policy the anonymous user may hold: no menu without a signed-in user, whatever the tab's
        // own policies
        if ( !\eZUser::currentUser()->isRegistered() || !self::tabIsOpen( $tab, $ini ) )
            return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );

        // a fragment for a script: no debug report appended to it
        \eZDebug::updateSettings( array( 'debug-enabled' => false ) );
        // what it holds follows the user's rights: kept by no shared cache
        if ( !headers_sent() )
            header( 'Cache-Control: private, no-store' );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'mega_tab', $tab );
        $tpl->setVariable( 'mega_template', $template );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:user/megamenu.tpl' );
        $Result['pagelayout'] = false;
        $Result['navigation_part'] = $ini->variable( 'Topmenu_' . $tab, 'NavigationPartIdentifier' );
        $Result['path'] = array( array( 'url' => false, 'text' => $tab ) );

        return $this->viewResult( $Result, null );
    }

    /**
     * The template that draws the tab's mega menu: [Topmenu_<tab>] MegaMenuTemplate, for a tab named in
     * [TopAdminMenu] MegaMenuTabs[] that has its [Topmenu_<tab>] group; null for any other tab.
     *
     * @param string $tab
     * @param \eZINI $ini menu.ini
     * @return string|null a design: template
     */
    public static function templateFor( $tab, \eZINI $ini )
    {
        if ( !preg_match( '/^[a-z0-9_]+$/', $tab )
             || !$ini->hasVariable( 'TopAdminMenu', 'MegaMenuTabs' )
             || !in_array( $tab, (array)$ini->variable( 'TopAdminMenu', 'MegaMenuTabs' ), true )
             || !$ini->hasVariable( 'Topmenu_' . $tab, 'MegaMenuTemplate' )
             || !$ini->hasVariable( 'Topmenu_' . $tab, 'NavigationPartIdentifier' ) )
            return null;
        $template = (string)$ini->variable( 'Topmenu_' . $tab, 'MegaMenuTemplate' );
        // a template of the design, nothing else (no file:, no path out of the design)
        if ( !preg_match( '#^design:[a-z0-9_]+(?:/[a-z0-9_]+)*\.tpl$#', $template ) )
            return null;
        return $template;
    }

    /**
     * Whether the tab is in the current user's top menu, enabled and open to the user, as the header draws it: the
     * topmenu operator, filtered on access, in the context of the admin's pages (navigation).
     *
     * @param string $tab
     * @param \eZINI $ini menu.ini
     * @return bool
     */
    public static function tabIsOpen( $tab, \eZINI $ini )
    {
        $part = $ini->variable( 'Topmenu_' . $tab, 'NavigationPartIdentifier' );
        $operator = new \eZTopMenuOperator();
        $items = array();
        $operator->modify( null, 'topmenu', array(), '', '', $items,
                           array( 'context' => 'navigation', 'filter_on_access' => true ), null );
        foreach ( $items as $item )
        {
            if ( $item['navigationpart_identifier'] === $part )
                return $item['access'] && $item['enabled'];
        }
        return false;
    }
}

}
