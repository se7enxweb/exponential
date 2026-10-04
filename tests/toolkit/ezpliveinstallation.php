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
}
