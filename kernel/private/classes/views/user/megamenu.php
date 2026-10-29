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
 * The view answers a script, never a person, so every answer is its own bare response (no page layout, no debug
 * report, no sign-in page in its place) with a status the script can trust and an X-Exp-Mega header that names the
 * verdict:
 *
 *   200 ok          the fragment
 *   404 unknown     not a tab of MegaMenuTabs[] with a MegaMenuTemplate
 *   403 signed-out  no signed-in user (or one the site asks to sign in again)
 *   403 denied      a signed-in user who may not use this siteaccess, or whose top menu has no such open tab
 *
 * For that the kernel lets every visitor reach the view (site.ini [SiteAccessSettings] AnonymousAccessList[] and
 * [RoleSettings] PolicyOmitList[] name user/megamenu), and the view makes the checks itself: a signed-in user who may
 * log in to the siteaccess (user/login, as the kernel checks it), and the tab in that user's top menu, enabled and
 * open to the user, as the topmenu operator decides it for the header (Tabs[], HiddenTabs[], SetupTabs[],
 * PolicyList[] and CheckViewAccess of the tab's address), in the context of the admin's pages (navigation); every
 * link in it is then checked as in the left menu (PolicyList_<link>[], CheckViewAccess).
 *
 * The view only reads the session, so it releases it (and its lock) as soon as the user is known: the other requests
 * of the same session do not wait for the menu to be drawn. Every answer is private to the user (Cache-Control:
 * private, no-store; Vary: Cookie), so no shared cache keeps one user's menu, or a refusal, for another.
 * Guide: doc/eng-us/features/6.0/admin4-design.md ("Mega menu").
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\User
{

class Megamenu extends \Exponential\Runnable\ModuleView
{
    /** the HTTP status of each verdict */
    const STATUS = array( 'ok' => 200, 'unknown' => 404, 'signed-out' => 403, 'denied' => 403 );

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $tab = isset( $Params['Tab'] ) ? (string)$Params['Tab'] : '';

        // a fragment for a script: no debug report appended to it, whatever the answer
        \eZDebug::updateSettings( array( 'debug-enabled' => false ) );

        $ini = \eZINI::instance( 'menu.ini' );
        $template = self::templateFor( $tab, $ini );
        $verdict = self::verdict( $tab, $template, \eZUser::currentUser(), \eZSiteAccess::current(), $ini );

        // the session is only read: the other requests of this session go on while the menu is drawn
        self::releaseSession();

        if ( $verdict !== 'ok' )
            return $this->send( $verdict, '' );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'mega_tab', $tab );
        $tpl->setVariable( 'mega_template', $template );
        return $this->send( 'ok', self::compact( $tpl->fetch( 'design:user/megamenu.tpl' ) ) );
    }

    /**
     * What the view answers: 'ok', or why not ('unknown', 'signed-out', 'denied'), checked in that order, so a tab
     * that is not switched on says so before anything is asked about the user.
     *
     * @param string $tab
     * @param string|null $template templateFor()'s answer
     * @param \eZUser $user the current user
     * @param array|null $access the current siteaccess (eZSiteAccess::current()); null skips the siteaccess check
     * @param \eZINI $ini menu.ini
     * @return string
     */
    public static function verdict( $tab, $template, \eZUser $user, $access, \eZINI $ini )
    {
        if ( $template === null )
            return 'unknown';
        // the anonymous user, or a session the site asks to sign in again (eZUserLoginHandler's force login)
        if ( !$user->isRegistered() || self::loginForced() )
            return 'signed-out';
        // the check the kernel makes for every other view of the siteaccess (user/login, its SiteAccess limitation)
        if ( is_array( $access ) && isset( $access['name'] ) && !$user->canLoginToSiteAccess( $access ) )
            return 'denied';
        if ( !self::tabIsOpen( $tab, $ini ) )
            return 'denied';
        return 'ok';
    }

    /**
     * Sends the answer and ends the request (eZExecution::cleanExit(), which a persistent worker turns into the end
     * of the request: never call this inside a catch of \Exception or \Throwable).
     *
     * @param string $verdict a key of STATUS
     * @param string $body the fragment, empty for a refusal
     * @return mixed never returns in a web request
     */
    protected function send( $verdict, $body )
    {
        \eZExecution::discardOutputBuffers();
        if ( !headers_sent() )
        {
            http_response_code( self::STATUS[$verdict] );
            header( 'Content-Type: text/html; charset=utf-8' );
            // what it holds follows the user's rights and session: kept by no shared cache, never served to another
            header( 'Cache-Control: private, no-store' );
            header( 'Vary: Cookie' );
            header( 'X-Exp-Mega: ' . $verdict );
        }
        echo $body;
        \eZExecution::cleanExit();
        return null;
    }

    /**
     * Closes the session for writing, so its lock is released: the view reads the user from it and writes nothing.
     * eZSession stays marked as started, so nothing reopens it; anything set in $_SESSION later is not kept.
     */
    public static function releaseSession()
    {
        if ( function_exists( 'session_status' ) && session_status() === PHP_SESSION_ACTIVE )
            session_write_close();
    }

    /**
     * Whether the session asks its user to sign in again (eZUserLoginHandler::FORCE_LOGIN).
     *
     * @return bool
     */
    public static function loginForced()
    {
        return class_exists( 'eZSession' ) && \eZSession::hasStarted()
               && \eZHTTPTool::instance()->hasSessionVariable( \eZUserLoginHandler::FORCE_LOGIN );
    }

    /**
     * The fragment without the indentation of the templates between its tags (about 70 KB of the Setup menu's 75 KB):
     * a run of white space between two tags becomes one space. Left as it is when it holds an element whose white
     * space counts (pre, textarea, script, style).
     *
     * @param string $html
     * @return string
     */
    public static function compact( $html )
    {
        $html = (string)$html;
        if ( preg_match( '#<(?:pre|textarea|script|style)\b#i', $html ) )
            return trim( $html );
        $compact = preg_replace( '/>\s+</', '> <', $html );
        return $compact === null ? trim( $html ) : trim( $compact );
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
