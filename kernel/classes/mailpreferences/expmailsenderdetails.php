<?php
/**
 * File containing the expMailSenderDetails class.
 *
 * Who sends the optional mail: the organisation name and the postal address the mail gate puts in the footer of
 * every optional mail (CAN-SPAM, CASL and the EU rules require both).
 *
 *  - The name comes from the application: mailpreferences.ini [FooterSettings] OrganisationName when it is set
 *    (an explicit override), else the SiteName of the public siteaccess the mail is about: the one of the web
 *    request when it is public, on the console and in a cronjob the DefaultAccess. Never the name of an
 *    administration or editor siteaccess (one that requires a login). siteName().
 *  - The privacy notice: [FooterSettings] PrivacyURL, else the node of menu.ini [SiteInfo] PrivacyPolicyID of
 *    the public siteaccess, else none (privacyURL()).
 *  - The postal address is held by no other setting: it is entered on the administrator's status page
 *    (mailpreferences/admin/status), in the setup wizard or with exp:install --organisation-address, and kept as
 *    [FooterSettings] OrganisationAddress. An empty address does not stop any mail; the status page and
 *    exp:mail:status warn while it is empty.
 *
 * save() writes both into settings/override/mailpreferences.ini.append.php, the file that also holds the site
 * secret (which it leaves as it is), owner and group of the directory, mode 0640. That file is never committed.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMailSenderDetails
{
    const BLOCK = 'FooterSettings';

    /** @var string|null tests: the settings directory save() writes to when none is given */
    protected static $dir = null;

    /** Tests only: another settings directory for save(); null resets. */
    public static function setDirForTest( $dir )
    {
        self::$dir = $dir;
    }

    /**
     * @param string|null $siteaccess the siteaccess the mail is about (siteName())
     * @return array name (what the footer shows), address, name_source ('setting' or 'site'), name_setting (the
     *               value of OrganisationName, '' when the site name is used)
     */
    public static function get( $siteaccess = null )
    {
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $value = function ( $v ) use ( $ini ) {
            return $ini->hasVariable( self::BLOCK, $v ) ? trim( str_replace( '\n', "\n", (string)$ini->variable( self::BLOCK, $v ) ) ) : '';
        };
        $name = $value( 'OrganisationName' );
        return array( 'name' => $name !== '' ? $name : self::siteName( $siteaccess ), 'address' => $value( 'OrganisationAddress' ),
                      'name_source' => $name !== '' ? 'setting' : 'site', 'name_setting' => $name );
    }

    /**
     * The name of the public site the mail is about, never the name of an administration siteaccess:
     *
     *  1. $siteaccess, when it is given and public;
     *  2. the siteaccess of this web request, when it is public;
     *  3. the DefaultAccess (also on the console and in a cronjob, whatever siteaccess the command runs with);
     *  4. the first public siteaccess of [SiteSettings] SiteList[].
     *
     * A siteaccess is public when it does not require a login ([SiteAccessSettings] RequireUserLogin): the
     * administration and editor siteaccesses do. [FooterSettings] OrganisationName, when set, is used instead of all
     * of this (get()).
     *
     * @param string|null $siteaccess the siteaccess the mail is about
     * @return string '' when no public siteaccess has a name
     */
    public static function siteName( $siteaccess = null )
    {
        try
        {
            $site = eZINI::instance();
            $current = !empty( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '';
            $candidates = array();
            if ( $siteaccess !== null && $siteaccess !== '' )
                $candidates[] = (string)$siteaccess;
            if ( PHP_SAPI !== 'cli' && $current !== '' )
                $candidates[] = $current;
            if ( $site->hasVariable( 'SiteSettings', 'DefaultAccess' ) )
                $candidates[] = trim( (string)$site->variable( 'SiteSettings', 'DefaultAccess' ) );
            if ( $site->hasVariable( 'SiteSettings', 'SiteList' ) )
                $candidates = array_merge( $candidates, array_map( 'trim', (array)$site->variable( 'SiteSettings', 'SiteList' ) ) );
            foreach ( array_unique( array_filter( $candidates, 'strlen' ) ) as $access )
            {
                $name = self::publicSiteName( $access, $access === $current && PHP_SAPI !== 'cli' ? $site : null );
                if ( $name !== '' )
                    return $name;
            }
            return '';
        }
        catch ( Throwable $e )
        {
            return '';
        }
    }

    /** @var array siteaccess => its public name ('' when it is not public), per request */
    protected static $publicNames = array();

    /** @var mixed the request $publicNames belongs to (a persistent worker serves many) */
    protected static $publicNamesFor = null;

    /**
     * @param string $access
     * @param eZINI|null $ini the site.ini of the siteaccess when it is the one already loaded
     * @return string the SiteName of the siteaccess, '' when it requires a login or has no name
     */
    protected static function publicSiteName( $access, $ini = null )
    {
        $request = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? $_SERVER['REQUEST_TIME_FLOAT'] : 0;
        if ( self::$publicNamesFor !== $request )
        {
            self::$publicNames = array();
            self::$publicNamesFor = $request;
        }
        if ( isset( self::$publicNames[$access] ) )
            return self::$publicNames[$access];
        $name = '';
        if ( $ini === null )
            $ini = self::siteAccessIni( $access, 'site.ini' );
        if ( $ini instanceof eZINI )
        {
            $login = $ini->hasVariable( 'SiteAccessSettings', 'RequireUserLogin' ) ? strtolower( trim( (string)$ini->variable( 'SiteAccessSettings', 'RequireUserLogin' ) ) ) : 'false';
            if ( $login !== 'true' && $ini->hasVariable( 'SiteSettings', 'SiteName' ) )
                $name = trim( (string)$ini->variable( 'SiteSettings', 'SiteName' ) );
        }
        return self::$publicNames[$access] = $name;
    }

    /**
     * The address of the privacy notice for the footer of optional mail and the preference pages:
     *
     *  1. [FooterSettings] PrivacyURL: a full https:// address, or a path of the site ('/privacy', 'content/view/full/90');
     *  2. else the node of menu.ini [SiteInfo] PrivacyPolicyID of the public siteaccess (the convention of the site
     *     designs), when that node exists;
     *  3. else '' and the link is left out.
     *
     * @param string|null $siteaccess the siteaccess the mail is about
     * @return string an absolute address, or ''
     */
    public static function privacyURL( $siteaccess = null )
    {
        try
        {
            $ini = eZINI::instance( 'mailpreferences.ini' );
            $url = $ini->hasVariable( self::BLOCK, 'PrivacyURL' ) ? trim( (string)$ini->variable( self::BLOCK, 'PrivacyURL' ) ) : '';
            if ( $url !== '' )
            {
                if ( preg_match( '#^https?://#i', $url ) )
                    return $url;
                return expMailToken::baseURL() . '/' . ltrim( $url, '/' );
            }
            $nodeID = self::privacyNodeID( $siteaccess );
            if ( $nodeID > 0 && class_exists( 'eZContentObjectTreeNode' ) )
            {
                $node = eZContentObjectTreeNode::fetch( $nodeID );
                if ( $node instanceof eZContentObjectTreeNode )
                {
                    $alias = (string)$node->attribute( 'url_alias' );
                    return expMailToken::baseURL() . '/' . ( $alias !== '' ? ltrim( $alias, '/' ) : 'content/view/full/' . $nodeID );
                }
            }
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'The privacy notice address: ' . $e->getMessage(), __METHOD__ );
        }
        return '';
    }

    /**
     * @param string $access a siteaccess of [SiteSettings] SiteList[] (or the current one)
     * @param string $file
     * @return eZINI|null the settings as that siteaccess sees them (extensions and overrides included)
     */
    protected static function siteAccessIni( $access, $file )
    {
        if ( !preg_match( '/^[A-Za-z0-9_-]+$/', (string)$access ) || !class_exists( 'eZSiteAccess' ) )
            return null;
        $site = eZINI::instance();
        $list = $site->hasVariable( 'SiteSettings', 'SiteList' ) ? array_map( 'trim', (array)$site->variable( 'SiteSettings', 'SiteList' ) ) : array();
        $current = !empty( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '';
        if ( !in_array( $access, $list, true ) && $access !== $current )
            return null;
        $ini = eZSiteAccess::getIni( $access, $file );
        return $ini instanceof eZINI ? $ini : null;
    }

    /** @return int menu.ini [SiteInfo] PrivacyPolicyID of the public siteaccess, 0 without */
    protected static function privacyNodeID( $siteaccess )
    {
        $site = eZINI::instance();
        $candidates = array();
        if ( $siteaccess !== null && $siteaccess !== '' )
            $candidates[] = (string)$siteaccess;
        if ( $site->hasVariable( 'SiteSettings', 'DefaultAccess' ) )
            $candidates[] = trim( (string)$site->variable( 'SiteSettings', 'DefaultAccess' ) );
        foreach ( array_unique( array_filter( $candidates, 'strlen' ) ) as $access )
        {
            $menu = self::siteAccessIni( $access, 'menu.ini' );
            if ( $menu && $menu->hasVariable( 'SiteInfo', 'PrivacyPolicyID' ) && ctype_digit( trim( (string)$menu->variable( 'SiteInfo', 'PrivacyPolicyID' ) ) ) )
                return (int)$menu->variable( 'SiteInfo', 'PrivacyPolicyID' );
        }
        return 0;
    }

    /**
     * Stores the organisation name and the postal address in the settings override and clears the INI cache.
     *
     * @param string $name '' leaves the name to the site name
     * @param string $address several lines allowed (stored with \n)
     * @param string|null $dir the settings directory (default settings/override of the installation; tests)
     * @return bool
     * @throws InvalidArgumentException for a value that cannot be stored (two hashes, the end of a PHP comment, a NUL byte)
     */
    public static function save( $name, $address, $dir = null )
    {
        $name = self::clean( $name, false );
        $address = self::clean( $address, true );
        if ( $dir === null && self::$dir !== null )
            $dir = self::$dir;
        $dir = $dir !== null ? rtrim( $dir, "/" ) : rtrim( getcwd(), "/" ) . "/settings/override";
        if ( !is_dir( $dir ) && !@mkdir( $dir, eZDir::dirMode( 0775 ), true ) )
            return false;
        $file = $dir . '/mailpreferences.ini.append.php';
        $scope = new expIniScope( 'mailpreferences-sender', expIniScope::KIND_GLOBAL, basename( $dir ), dirname( $dir ) . '/', 'Mail sender details' );
        $editor = new expIniEditor( $scope, 'mailpreferences.ini' );
        $editor->set( self::BLOCK, 'OrganisationName', $name );
        $editor->set( self::BLOCK, 'OrganisationAddress', $address );
        $editor->save( array( 'backup' => false ) );
        if ( class_exists( 'expAuditWriter' ) )
            expAuditWriter::ownLikeParent( $file, 0640 );
        else
            @chmod( $file, eZFile::fileMode( 0640 ) );
        if ( function_exists( 'opcache_invalidate' ) )
            @opcache_invalidate( $file, true );
        if ( $dir === rtrim( getcwd(), '/' ) . '/settings/override' )
            self::clearIniCache();
        return true;
    }

    /** The INI cache (this installation's settings are read with modification checks off) and this process's copy. */
    public static function clearIniCache()
    {
        try
        {
            if ( class_exists( 'eZCache' ) )
                eZCache::clearByTag( 'ini' );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'INI cache: ' . $e->getMessage(), __METHOD__ );
        }
        eZINI::resetInstance( 'mailpreferences.ini' );
    }

    /**
     * @param string $value
     * @param bool $multiline line breaks become \n
     * @return string
     */
    protected static function clean( $value, $multiline )
    {
        $value = str_replace( array( "\r\n", "\r" ), "\n", (string)$value );
        $lines = array_values( array_filter( array_map( 'trim', explode( "\n", $value ) ), 'strlen' ) );
        $value = $multiline ? implode( '\n', $lines ) : implode( ' ', $lines );
        if ( strpos( $value, '##' ) !== false || strpos( $value, '*/' ) !== false || strpos( $value, "\0" ) !== false )
            throw new InvalidArgumentException( 'The sender details cannot contain ##, */ or a NUL byte' );
        return mb_substr( $value, 0, 500 );
    }
}
