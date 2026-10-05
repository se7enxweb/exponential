<?php
/**
 * File containing the expMailSenderDetails class.
 *
 * Who sends the optional mail: the organisation name and the postal address the mail gate puts in the footer of
 * every optional mail (CAN-SPAM, CASL and the EU rules require both).
 *
 *  - The name comes from the application: mailpreferences.ini [FooterSettings] OrganisationName when it is set
 *    (an explicit override), else the SiteName of the siteaccess the mail is sent for, and on the console or a
 *    cronjob without a siteaccess the SiteName of the DefaultAccess.
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
     * @return array name (what the footer shows), address, name_source ('setting' or 'site'), name_setting (the
     *               value of OrganisationName, '' when the site name is used)
     */
    public static function get()
    {
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $value = function ( $v ) use ( $ini ) {
            return $ini->hasVariable( self::BLOCK, $v ) ? trim( str_replace( '\n', "\n", (string)$ini->variable( self::BLOCK, $v ) ) ) : '';
        };
        $name = $value( 'OrganisationName' );
        return array( 'name' => $name !== '' ? $name : self::siteName(), 'address' => $value( 'OrganisationAddress' ),
                      'name_source' => $name !== '' ? 'setting' : 'site', 'name_setting' => $name );
    }

    /**
     * The SiteName of the siteaccess this process serves; on the console without a siteaccess that of the
     * DefaultAccess.
     *
     * @return string
     */
    public static function siteName()
    {
        try
        {
            $site = eZINI::instance();
            if ( PHP_SAPI === 'cli' && empty( $GLOBALS['eZCurrentAccess']['name'] ) )
            {
                $default = $site->hasVariable( 'SiteSettings', 'DefaultAccess' ) ? trim( (string)$site->variable( 'SiteSettings', 'DefaultAccess' ) ) : '';
                if ( $default !== '' )
                {
                    $access = eZINI::getSiteAccessIni( $default, 'site.ini' );
                    if ( $access && $access->hasVariable( 'SiteSettings', 'SiteName' ) && trim( (string)$access->variable( 'SiteSettings', 'SiteName' ) ) !== '' )
                        return trim( (string)$access->variable( 'SiteSettings', 'SiteName' ) );
                }
            }
            return $site->hasVariable( 'SiteSettings', 'SiteName' ) ? trim( (string)$site->variable( 'SiteSettings', 'SiteName' ) ) : '';
        }
        catch ( Throwable $e )
        {
            return '';
        }
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
        if ( !is_dir( $dir ) && !@mkdir( $dir, 0775, true ) )
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
            @chmod( $file, 0640 );
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
