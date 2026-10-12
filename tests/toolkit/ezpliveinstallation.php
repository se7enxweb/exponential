<?php
/**
 * File containing the ezpLiveInstallation class.
 *
 * One precondition for every test that needs a running installation: a database it can connect to and a
 * site installed in it (the admin user exists). Where that is missing, as on a CI runner that checks out the
 * code only, the test is skipped with the reason instead of erroring on the first query.
 *
 *   public static function setUpBeforeClass(): void
 *   {
 *       ezpLiveInstallation::requireOrSkip();      // skips the whole class
 *   }
 *
 *   protected function setUp(): void
 *   {
 *       ezpLiveInstallation::requireOrSkip();      // or one test at a time
 *   }
 *
 * The check runs once per process: the kernel is started with the admin siteaccess when nobody has done it
 * yet, the connection is opened and one query is asked. A siteaccess a test names is checked with
 * requireSiteAccessOrSkip().
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 */

class ezpLiveInstallation
{
    /** @var string|null|false false: not checked yet, null: available, string: why not */
    private static $reason = false;

    /**
     * @return string|null null when the installation is usable, else why it is not
     */
    public static function unavailableReason()
    {
        if ( self::$reason !== false )
            return self::$reason;

        self::$reason = null;
        try
        {
            $root = dirname( __DIR__, 2 );
            chdir( $root );
            if ( !class_exists( 'eZScript' ) )
                require_once $root . '/autoload.php';
            if ( !eZDB::hasInstance() || !eZDB::instance()->isConnected() )
            {
                $script = eZScript::instance( array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
                $script->startup();
                $script->setUseSiteAccess( 'admin' );
                $script->initialize();
                eZExecution::setCleanExit();
            }
            $db = eZDB::instance();
            if ( !$db->isConnected() )
                throw new RuntimeException( 'no database connection' );
            $rows = $db->arrayQuery( "SELECT COUNT(*) AS c FROM ezuser WHERE login = 'admin'" );
            if ( !is_array( $rows ) || !isset( $rows[0]['c'] ) || (int)$rows[0]['c'] < 1 )
                throw new RuntimeException( 'no installed site in the database (no admin user)' );
        }
        catch ( Throwable $e )
        {
            self::$reason = 'needs a live installation (database and installed site): ' . trim( strtok( $e->getMessage(), "\n" ) );
        }
        return self::$reason;
    }

    /**
     * Skips the running test, or the running class from setUpBeforeClass(), unless the installation is usable.
     */
    public static function requireOrSkip()
    {
        $reason = self::unavailableReason();
        if ( $reason !== null )
            PHPUnit\Framework\TestCase::markTestSkipped( $reason );
    }

    /**
     * Skips unless the siteaccess is set up in this checkout (siteaccesses are installation data, not code).
     *
     * @param string $siteAccess
     */
    public static function requireSiteAccessOrSkip( $siteAccess )
    {
        if ( !is_dir( dirname( __DIR__, 2 ) . '/settings/siteaccess/' . $siteAccess ) )
            PHPUnit\Framework\TestCase::markTestSkipped( "needs the siteaccess '$siteAccess' of an installed site (settings/siteaccess/$siteAccess)" );
    }

    /**
     * The host name this installation is reached at from outside, for a test that fetches its own pages over HTTP:
     * EXP_TEST_PUBLIC_HOST when set, else the host of the SiteURL of the default siteaccess ([SiteSettings]
     * DefaultAccess). Empty when there is none, when it is an example or reserved name (example.com, .test,
     * .invalid, localhost ...) or when it does not resolve. The host is installation data, never a value of the code.
     *
     * @return string host name without scheme, port or path; '' when unknown
     */
    public static function publicHost()
    {
        $host = trim( (string)getenv( 'EXP_TEST_PUBLIC_HOST' ) );
        if ( $host !== '' )
            return $host;
        if ( self::unavailableReason() !== null )
            return '';
        try
        {
            $ini = eZINI::instance();
            $default = $ini->hasVariable( 'SiteSettings', 'DefaultAccess' ) ? (string)$ini->variable( 'SiteSettings', 'DefaultAccess' ) : '';
            $siteIni = $default !== '' ? eZSiteAccess::getIni( $default, 'site.ini' ) : $ini;
            $url = $siteIni->hasVariable( 'SiteSettings', 'SiteURL' ) ? (string)$siteIni->variable( 'SiteSettings', 'SiteURL' ) : '';
        }
        catch ( Throwable $e )
        {
            return '';
        }
        $url = preg_replace( '#^[a-z][a-z0-9+.-]*://#i', '', trim( $url ) );
        $host = strtolower( preg_replace( '#[/:?].*$#', '', $url ) );
        if ( $host === '' || preg_match( '/(^|\.)(example\.(com|org|net)|localhost|test|invalid|example|local)$/', $host ) )
            return '';
        if ( !filter_var( $host, FILTER_VALIDATE_IP ) && gethostbyname( $host ) === $host )
            return '';
        return $host;
    }

    /**
     * publicHost(), or skips the running test with how to name one.
     *
     * @return string
     */
    public static function publicHostOrSkip()
    {
        $host = self::publicHost();
        if ( $host === '' )
            PHPUnit\Framework\TestCase::markTestSkipped( 'needs the public host name of this installation: the SiteURL of the default siteaccess, or EXP_TEST_PUBLIC_HOST' );
        return $host;
    }

    /**
     * The web servers of this installation a live HTTP test runs against: https://<publicHost()>, and beside it
     * Velocity on the HTTPS port of velocity.ini [ServerSettings] HTTPSPort when something answers there. Empty
     * when publicHost() is.
     *
     * @return string[] base URLs without a trailing slash
     */
    public static function publicBases()
    {
        $host = self::publicHost();
        if ( $host === '' )
            return array();
        $bases = array( 'https://' . $host );
        try
        {
            $velocity = eZINI::instance( 'velocity.ini' );
            $port = $velocity->hasVariable( 'ServerSettings', 'HTTPSPort' ) ? (int)$velocity->variable( 'ServerSettings', 'HTTPSPort' ) : 0;
        }
        catch ( Throwable $e )
        {
            $port = 0;
        }
        if ( $port > 0 && $port !== 443 )
        {
            $socket = @fsockopen( $host, $port, $errno, $errstr, 2 );
            if ( $socket )
            {
                fclose( $socket );
                $bases[] = 'https://' . $host . ':' . $port;
            }
        }
        return $bases;
    }
}
