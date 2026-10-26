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
 * The check runs once per process: the kernel is started with the siteaccess of siteAccess() when nobody has done
 * it yet, the connection is opened and one query is asked. A siteaccess a test names is checked with
 * requireSiteAccessOrSkip().
 *
 * Which siteaccess and which database. The siteaccess is EXP_TEST_SITEACCESS, else [SiteSettings]
 * DefaultAccess (it used to be "admin", which an installation need not have: every live test was skipped
 * with "Unable to connect to the database server"). The tests write (throwaway rows they remove again), so they
 * run on the database of that siteaccess only when "test" is a word of its name; otherwise on a throwaway database of
 * the run (ezpThrowawayDatabase: kernel schema and clean data), and when none can be made, they are skipped
 * with the siteaccess, the database and the reason. The database of a site is never written to.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 */

class ezpLiveInstallation
{
    /** @var string|null|false false: not checked yet, null: available, string: why not */
    private static $reason = false;

    /** @var bool whether the live tests run on a throwaway database */
    private static $throwaway = false;

    /** @var bool whether startKernel() started the kernel */
    private static $kernelStarted = false;

    /**
     * the siteaccess the tests run with: EXP_TEST_SITEACCESS, else [SiteSettings] DefaultAccess (as the
     * kernel resolves it with the extensions active). The one loaded when the kernel is started already.
     *
     * @return string
     */
    public static function siteAccess()
    {
        if ( isset( $GLOBALS['eZCurrentAccess']['name'] ) && $GLOBALS['eZCurrentAccess']['name'] !== '' )
            return (string)$GLOBALS['eZCurrentAccess']['name'];
        return trim( (string)getenv( 'EXP_TEST_SITEACCESS' ) );
    }

    /**
     * Starts the kernel with siteAccess() unless a siteaccess is loaded already. Opens no database connection.
     */
    public static function startKernel()
    {
        $root = dirname( __DIR__, 2 );
        chdir( $root );
        if ( !class_exists( 'eZScript' ) )
            require_once $root . '/autoload.php';
        if ( self::$kernelStarted || ( isset( $GLOBALS['eZCurrentAccess']['name'] ) && $GLOBALS['eZCurrentAccess']['name'] !== '' ) )
            return;
        // only EXP_TEST_SITEACCESS is named here: DefaultAccess is resolved by eZScript::initialize() itself, after the
        // extensions are active (an extension's settings may set it; read before, it is the default of settings/site.ini)
        $siteAccess = trim( (string)getenv( 'EXP_TEST_SITEACCESS' ) );
        if ( $siteAccess !== '' && !preg_match( '/^[A-Za-z0-9_-]+$/D', $siteAccess ) )
            throw new RuntimeException( "EXP_TEST_SITEACCESS '$siteAccess' is not a siteaccess name" );
        $script = eZScript::instance( array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
        $script->startup();
        if ( $siteAccess !== '' )
            $script->setUseSiteAccess( $siteAccess );
        $script->initialize();
        eZExecution::setCleanExit();
        self::$kernelStarted = true;
    }

    /**
     * @return string|null null when the installation is usable, else why it is not
     */
    public static function unavailableReason()
    {
        if ( self::$reason !== false )
            return self::$reason;

        self::$reason = null;
        $siteAccess = '?';
        $database = '?';
        try
        {
            self::startKernel();
            $siteAccess = self::siteAccess();
            if ( trim( (string)getenv( 'EXP_TEST_SITEACCESS' ) ) !== '' && !eZSiteAccess::exists( $siteAccess ) )
                throw new RuntimeException( 'EXP_TEST_SITEACCESS names no siteaccess of [SiteAccessSettings] AvailableSiteAccessList' );
            $ini = eZINI::instance();
            $database = $ini->hasVariable( 'DatabaseSettings', 'Database' ) ? (string)$ini->variable( 'DatabaseSettings', 'Database' ) : '';
            if ( self::isTestDatabaseName( $database ) )
            {
                $db = eZDB::instance();
                if ( !$db->isConnected() )
                    throw new RuntimeException( 'no database connection: ' . $db->errorMessage() );
            }
            else
            {
                // the database of a site ("test" not in its name) is never written to by a test
                $reason = ezpThrowawayDatabase::unavailableReason();
                if ( $reason !== null )
                    throw new RuntimeException( "its database '$database' is not a test database (no word \"test\" in the name; the live tests write to it), and $reason" );
                $db = ezpThrowawayDatabase::forRun();
                $database = $db->DB;
                self::$throwaway = true;
            }
            $rows = $db->arrayQuery( "SELECT COUNT(*) AS c FROM ezuser WHERE login = 'admin'" );
            if ( !is_array( $rows ) || !isset( $rows[0]['c'] ) || (int)$rows[0]['c'] < 1 )
                throw new RuntimeException( 'no installed site in the database (no admin user)' );
        }
        catch ( Throwable $e )
        {
            self::$reason = "needs a live installation (siteaccess '$siteAccess' from " . ( trim( (string)getenv( 'EXP_TEST_SITEACCESS' ) ) !== '' ? 'EXP_TEST_SITEACCESS' : 'DefaultAccess' )
                . ", database '$database'): " . trim( strtok( $e->getMessage(), "\n" ) );
        }
        return self::$reason;
    }

    /**
     * Skips the running test, or the running class from setUpBeforeClass(), unless the installation is usable.
     * On a throwaway database, makes it the database of the test again (a database test class before may have
     * pointed the kernel to its own). Every call checks the database the eZDB instance is connected to, not only the
     * settings once: a test before may have changed it.
     */
    public static function requireOrSkip()
    {
        $reason = self::unavailableReason();
        if ( $reason !== null )
            PHPUnit\Framework\TestCase::markTestSkipped( $reason );
        if ( self::$throwaway )
            ezpThrowawayDatabase::forRun();
        $database = eZDB::instance()->DB;
        if ( !self::isTestDatabaseName( $database ) )
            PHPUnit\Framework\TestCase::markTestSkipped( "the database of the test is '$database', not a test database; the live tests write to it" );
    }

    /**
     * whether a test may write to the database $name: a plain identifier with "test" as one of its words
     * (exp_test_1, site_test, test): not latest_shop, contest or testdb.
     *
     * @param mixed $name
     * @return bool
     */
    public static function isTestDatabaseName( $name )
    {
        return is_string( $name ) && preg_match( '/^[A-Za-z0-9_]{1,64}$/D', $name ) && preg_match( '/(^|_)test(_|$)/iD', $name );
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
        // on a throwaway database the site's web server works on another database than the test; a request
        // to it would neither see the test's rows nor be harmless to the site's
        $reason = self::unavailableReason();
        if ( self::$throwaway )
            return '';
        $host = trim( (string)getenv( 'EXP_TEST_PUBLIC_HOST' ) );
        if ( $host !== '' )
            return $host;
        if ( $reason !== null )
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
            PHPUnit\Framework\TestCase::markTestSkipped( self::$throwaway
                ? 'needs the web server of the installation, which does not run on the throwaway database of the tests'
                : 'needs the public host name of this installation: the SiteURL of the default siteaccess, or EXP_TEST_PUBLIC_HOST' );
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
