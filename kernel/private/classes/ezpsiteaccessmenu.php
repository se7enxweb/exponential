<?php
/**
 * File containing the ezpSiteAccessMenu class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The administration's site menu: the siteaccesses of this installation with
 * their front page addresses, for the globe button in the admin4 header. The
 * siteaccesses are otherwise known only from the INI settings; the menu shows
 * an administrator which end points there are.
 *
 * Settings: site.ini [AdminSiteAccessMenu] of the current siteaccess.
 *
 * - Show: enabled (the default) or disabled; disabled leaves the globe a plain
 *   link to the site.
 * - List: related (the default) lists [SiteAccessSettings] RelatedSiteAccessList[]
 *   of the current siteaccess, the siteaccesses sharing its database, or
 *   AvailableSiteAccessList[] where that is empty; available lists
 *   AvailableSiteAccessList[]. Either way only siteaccesses of
 *   AvailableSiteAccessList[] are listed: the others cannot be reached. An
 *   installation serving several sites with separate databases sets
 *   RelatedSiteAccessList[] for each administration, so that it lists its own
 *   site only.
 * - HiddenSiteAccessList[]: siteaccesses left out. The site the globe opens is
 *   always the first entry: the globe leads there anyway.
 * - AdminSiteAccessList[]: the siteaccesses listed under Administration. Empty:
 *   those whose [DesignSettings] SiteDesign begins with "admin", or that name a
 *   [SiteSettings] FrontSiteAccess (only an admin siteaccess has a site of its
 *   own to link to).
 *
 * The addresses come from ezpSiteAccessURL::root(), as siteaccess_url() has
 * them: scheme, host and port of this request, the siteaccess path by
 * MatchOrder, URIMatchType, HostMatchMapItems and SiteURL. The first entry is
 * the site the globe opens (ezpSiteAccessURL::frontSiteAccess()); it is not
 * repeated in the groups below it.
 */
class ezpSiteAccessMenu
{
    /**
     * The menu of the current siteaccess and request.
     *
     * @return array hash enabled (bool), front (an item, or null) and groups
     *               (list of hash identifier, site or admin, and items); an
     *               item is a hash identifier, name, locale, lang, url,
     *               display_url, current and admin, see build()
     */
    public static function current()
    {
        $settings = self::settings();
        if ( !$settings['show'] )
            return self::build( array(), '', '', array(), null, null );
        $current = eZSiteAccess::current();
        return self::build( self::names( $settings ), ezpSiteAccessURL::frontSiteAccess(),
                            $current ? (string)$current['name'] : '', $settings['admin'],
                            array( __CLASS__, 'siteAccessInfo' ), array( 'ezpSiteAccessURL', 'root' ) );
    }

    /**
     * A key for the cache-blocks that draw the menu: it changes with the
     * settings that decide which siteaccesses are listed and with the scheme,
     * host and port of the request, which go into every address. Cheap: it
     * reads no siteaccess's own settings, so a changed SiteName, Locale or
     * SiteDesign of a listed siteaccess shows after the template caches of
     * the administration siteaccess are cleared, as any other INI change.
     *
     * @return string an empty string when the menu is off
     */
    public static function key()
    {
        $settings = self::settings();
        if ( !$settings['show'] )
            return '';
        $current = eZSiteAccess::current();
        return md5( implode( "\n", array(
            implode( ',', self::names( $settings ) ),
            implode( ',', $settings['admin'] ),
            ezpSiteAccessURL::frontSiteAccess(),
            $current ? (string)$current['name'] : '',
            eZSys::isSSLNow() ? 'https' : 'http',
            (string)eZSys::hostname(),
            (string)eZSys::serverVariable( 'SERVER_PORT', true ),
        ) ) );
    }

    /**
     * The menu from its parts, without reading any settings.
     *
     * @param string[] $names the siteaccesses to list, in their order
     * @param string $front the siteaccess the globe opens
     * @param string $current the current siteaccess
     * @param string[] $adminList AdminSiteAccessList[]; empty: by the design and FrontSiteAccess
     * @param callable|null $info name => hash site_name, locale, lang, site_design, front_site_access
     * @param callable|null $url name => the front page URL, or false when it cannot be reached
     * @return array see current()
     */
    public static function build( array $names, $front, $current, array $adminList, $info, $url )
    {
        $menu = array( 'enabled' => false, 'front' => null, 'groups' => array() );
        if ( $info === null || $url === null )
            return $menu;

        $front = (string)$front;
        $frontItem = $front !== '' ? self::item( $front, $current, $adminList, $info, $url ) : null;
        $groups = array( 'site' => array(), 'admin' => array() );
        foreach ( array_unique( $names ) as $name )
        {
            if ( $name === $front )
                continue;
            $item = self::item( $name, $current, $adminList, $info, $url );
            if ( $item !== null )
                $groups[$item['admin'] ? 'admin' : 'site'][] = $item;
        }
        foreach ( $groups as $identifier => $items )
        {
            if ( $items )
                $menu['groups'][] = array( 'identifier' => $identifier, 'items' => $items );
        }
        $menu['front'] = $frontItem;
        // a menu with the site alone says nothing the globe link does not
        $menu['enabled'] = $frontItem !== null && count( $menu['groups'] ) > 0;
        return $menu;
    }

    /**
     * A siteaccess's own settings the menu shows: SiteName, Locale (with its
     * HTTP language code for the lang attribute), SiteDesign and FrontSiteAccess.
     *
     * @param string $name
     * @return array hash site_name, locale, lang, site_design, front_site_access
     */
    public static function siteAccessInfo( $name )
    {
        $ini = eZSiteAccess::getIni( $name, 'site.ini' );
        $value = function ( $section, $variable ) use ( $ini ) {
            return $ini->hasVariable( $section, $variable ) && is_scalar( $ini->variable( $section, $variable ) )
                   ? trim( (string)$ini->variable( $section, $variable ) ) : '';
        };
        $locale = $value( 'RegionalSettings', 'Locale' );
        $lang = '';
        if ( $locale !== '' && preg_match( '/^[a-z]{3}-[A-Z]{2}(@\w+)?$/', $locale ) )
        {
            $instance = eZLocale::instance( $locale );
            if ( $instance && $instance->isValid() )
                $lang = (string)$instance->httpLocaleCode();
        }
        return array( 'site_name' => $value( 'SiteSettings', 'SiteName' ), 'locale' => $locale, 'lang' => $lang,
                      'site_design' => $value( 'DesignSettings', 'SiteDesign' ),
                      'front_site_access' => $value( 'SiteSettings', 'FrontSiteAccess' ) );
    }

    /**
     * site.ini [AdminSiteAccessMenu] of the current siteaccess.
     *
     * @return array hash show (bool), list (related or available), hidden and admin (string[])
     */
    public static function settings()
    {
        $ini = eZINI::instance( 'site.ini' );
        $value = function ( $name, $default ) use ( $ini ) {
            return $ini->hasVariable( 'AdminSiteAccessMenu', $name ) ? $ini->variable( 'AdminSiteAccessMenu', $name ) : $default;
        };
        $list = function ( $name ) use ( $value ) {
            return array_values( array_filter( array_map( 'trim', array_map( 'strval', (array)$value( $name, array() ) ) ), 'strlen' ) );
        };
        return array( 'show' => $value( 'Show', 'enabled' ) !== 'disabled',
                      'list' => $value( 'List', 'related' ) === 'available' ? 'available' : 'related',
                      'hidden' => $list( 'HiddenSiteAccessList' ),
                      'admin' => $list( 'AdminSiteAccessList' ) );
    }

    /**
     * The siteaccesses the menu lists, in the order of their list: RelatedSiteAccessList[] of the current siteaccess
     * (List=related; AvailableSiteAccessList[] where it is empty) or AvailableSiteAccessList[], only those of
     * AvailableSiteAccessList[] (read without siteaccess overrides, as eZSiteAccess::match() and
     * ezpSiteAccessURL::root() read it), without HiddenSiteAccessList[].
     *
     * @param array $settings see settings()
     * @return string[]
     */
    public static function names( array $settings )
    {
        $global = new eZINI( 'site.ini', 'settings', null, null, true );
        $available = array_map( 'strval', (array)$global->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) );
        $names = $available;
        if ( $settings['list'] === 'related' )
        {
            $ini = eZINI::instance( 'site.ini' );
            $related = $ini->hasVariable( 'SiteAccessSettings', 'RelatedSiteAccessList' )
                     ? array_map( 'strval', (array)$ini->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' ) ) : array();
            $related = array_filter( array_map( 'trim', $related ), 'strlen' );
            if ( $related )
                $names = array_intersect( $related, $available );
        }
        return array_values( array_unique( array_diff( array_filter( array_map( 'trim', $names ), 'strlen' ), $settings['hidden'] ) ) );
    }

    /**
     * One entry: null when the siteaccess has no address
     */
    private static function item( $name, $current, array $adminList, $info, $url )
    {
        $address = call_user_func( $url, $name );
        if ( !is_string( $address ) || $address === '' )
            return null;
        $data = (array)call_user_func( $info, $name ) + array( 'site_name' => '', 'locale' => '', 'lang' => '',
                                                              'site_design' => '', 'front_site_access' => '' );
        $admin = $adminList
               ? in_array( $name, $adminList, true )
               : ( stripos( (string)$data['site_design'], 'admin' ) === 0 || (string)$data['front_site_access'] !== '' );
        return array(
            'identifier' => $name,
            'name' => (string)$data['site_name'] !== '' ? (string)$data['site_name'] : $name,
            'locale' => (string)$data['locale'],
            'lang' => (string)$data['lang'],
            'url' => $address,
            'display_url' => rtrim( preg_replace( '#^[a-z][a-z0-9+.-]*://#i', '', $address ), '/' ),
            'current' => $name === (string)$current,
            'admin' => $admin,
        );
    }
}
