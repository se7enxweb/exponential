<?php
/**
 * eZSiteAccess::change() sets the log directory, the INI cache directory and the expiry file of the siteaccess it
 * changes to (multi-site hosting), and a second change in the same process takes them from the second site.
 *
 * A persistent worker (Velocity) serves the sites of an installation one request after another and removes the
 * globals between requests; a script can change siteaccess while it runs. Neither may keep the paths of the site
 * before: everything is derived again from site.ini on every change.
 *
 * The test writes siteaccesses of its own into settings/siteaccess/ and gives each its log, cache and expiry
 * directories as absolute paths under the system temp directory (an installation may set VarDir in its override,
 * which a siteaccess cannot change); all are removed afterwards. It runs in its own process, because it changes the
 * global site.ini.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
class eZSiteAccessSitePathsTest extends PHPUnit\Framework\TestCase
{
    private $root;
    private $created = array();
    private $createdSiteAccessDir = false;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 4 );
        chdir( $this->root );
        if ( !is_dir( 'settings/siteaccess' ) )
        {
            mkdir( 'settings/siteaccess', 0777, true );
            $this->createdSiteAccessDir = true;
        }
    }

    protected function tearDown(): void
    {
        chdir( $this->root );
        foreach ( array_reverse( $this->created ) as $dir )
        {
            self::removeTree( $dir );
        }
        if ( $this->createdSiteAccessDir )
        {
            @rmdir( 'settings/siteaccess' );
        }
        parent::tearDown();
    }

    /** Removes a directory, also outside the installation, where eZDir::recursiveDelete() does not delete */
    private static function removeTree( $dir )
    {
        if ( !is_dir( $dir ) || is_link( $dir ) )
        {
            @unlink( $dir );
            return;
        }
        foreach ( scandir( $dir ) as $entry )
        {
            if ( $entry !== '.' && $entry !== '..' )
            {
                self::removeTree( $dir . '/' . $entry );
            }
        }
        @rmdir( $dir );
    }

    /**
     * A siteaccess with the given [FileSettings]; {dir} in a value is a directory of its own under the system temp
     * directory.
     *
     * @return array array( siteaccess name, its directory under the system temp directory )
     */
    private function site( $label, array $fileSettings )
    {
        $name = 'x1sitepaths' . $label . substr( uniqid(), -6 );
        $siteDir = sys_get_temp_dir() . '/x1-site-paths-' . $name;
        mkdir( $siteDir, 0777, true );
        $this->created[] = $siteDir;

        $dir = 'settings/siteaccess/' . $name;
        mkdir( $dir, 0777, true );
        $this->created[] = $dir;
        $lines = array( '<?php /* #?ini charset="utf-8"?', '', '[FileSettings]' );
        foreach ( $fileSettings as $setting => $value )
        {
            $lines[] = $setting . '=' . str_replace( '{dir}', $siteDir, $value );
        }
        $lines[] = '*/ ?>';
        file_put_contents( $dir . '/site.ini.append.php', implode( "\n", $lines ) . "\n" );
        return array( $name, $siteDir );
    }

    public function testEachChangeTakesThePathsOfItsOwnSite()
    {
        list( $siteA, $dirA ) = $this->site( 'a', array(
            'UseGlobalLogDir' => 'disabled', 'LogDir' => '{dir}/log',
            'INICacheDir' => 'site', 'CacheDir' => '{dir}/cache',
            'ExpiryDir' => '{dir}/expiry',
        ) );
        list( $siteB, $dirB ) = $this->site( 'b', array() );

        eZSiteAccess::change( array( 'name' => $siteA, 'type' => eZSiteAccess::TYPE_DEFAULT ) );

        $this->assertSame( $dirA . '/log/', eZDebug::instance()->logDirectory() );
        $this->assertSame( $dirA . '/cache/ini/', $GLOBALS['eZINI_CONFIG_CACHE_DIR'] );
        $this->assertSame( $dirA . '/expiry/expiry.php', eZExpiryHandler::instance()->CacheFile->name() );

        // An INI file read now is cached in the cache directory of site A
        eZINI::instance( 'shopaccount.ini' );
        $this->assertNotEmpty( glob( $dirA . '/cache/ini/shopaccount*' ), 'site A caches its INI files in its own cache' );

        eZSiteAccess::change( array( 'name' => $siteB, 'type' => eZSiteAccess::TYPE_DEFAULT ) );

        $this->assertSame( 'var/log/', eZDebug::instance()->logDirectory() );
        // Back to var/cache/ini/, which eZINI::loadCache() puts there itself when nothing else is set
        $this->assertStringEndsWith( '/var/cache/ini/', $GLOBALS['eZINI_CONFIG_CACHE_DIR'] ?? '/var/cache/ini/' );
        $this->assertSame( eZSys::cacheDirectory() . '/expiry.php', eZExpiryHandler::instance()->CacheFile->name() );
        $this->assertStringStartsNotWith( $dirA, eZSys::cacheDirectory() );
    }

    /** The setup of a multi-site installation: one log tree and one cache tree for all sites, set by one line each */
    public function testLogVarDirAndCacheVarDirMoveLogsCachesAndTheINICache()
    {
        foreach ( array( 'x1-var-log', 'x1-var-cache' ) as $tree )
        {
            if ( !file_exists( $tree ) )
            {
                $this->created[] = $this->root . '/' . $tree;
            }
        }
        list( $site, $siteDir ) = $this->site( 'g', array(
            'UseGlobalLogDir' => 'disabled', 'LogVarDir' => 'x1-var-log',
            'CacheVarDir' => 'x1-var-cache', 'INICacheDir' => 'site', 'ExpiryDir' => 'expiry',
        ) );

        eZSiteAccess::change( array( 'name' => $site, 'type' => eZSiteAccess::TYPE_DEFAULT ) );

        $varDir = eZSys::varDirectory();
        $rest = substr( $varDir, strpos( $varDir . '/', '/' ) );
        $this->assertSame( 'x1-var-log' . $rest . '/log/', eZDebug::instance()->logDirectory() );
        $this->assertSame( 'x1-var-cache' . $rest . '/cache', eZSys::cacheDirectory() );
        $this->assertSame( $this->root . '/x1-var-cache' . $rest . '/cache/ini/', $GLOBALS['eZINI_CONFIG_CACHE_DIR'] );
        $this->assertSame( $varDir . '/expiry/expiry.php', eZExpiryHandler::instance()->CacheFile->name() );

        eZINI::instance( 'shopaccount.ini' );
        $this->assertNotEmpty( glob( 'x1-var-cache' . $rest . '/cache/ini/shopaccount*' ) );
    }

    public function testADirectoryTheInstallationSetIsLeftAlone()
    {
        list( $site, $siteDir ) = $this->site( 'c', array() );
        $GLOBALS['eZINI_CONFIG_CACHE_DIR'] = $siteDir . '/own-ini-cache/';

        eZSiteAccess::change( array( 'name' => $site, 'type' => eZSiteAccess::TYPE_DEFAULT ) );

        $this->assertSame( $siteDir . '/own-ini-cache/', $GLOBALS['eZINI_CONFIG_CACHE_DIR'] );
    }

    public function testADirectoryTheInstallationSetComesBackAfterASiteWithItsOwn()
    {
        list( $siteA, $dirA ) = $this->site( 'e', array( 'INICacheDir' => 'site', 'CacheDir' => '{dir}/cache' ) );
        list( $siteB, $dirB ) = $this->site( 'f', array() );
        $GLOBALS['eZINI_CONFIG_CACHE_DIR'] = $dirB . '/shared-ini-cache/';

        eZSiteAccess::change( array( 'name' => $siteA, 'type' => eZSiteAccess::TYPE_DEFAULT ) );
        $this->assertSame( $dirA . '/cache/ini/', $GLOBALS['eZINI_CONFIG_CACHE_DIR'] );

        eZSiteAccess::change( array( 'name' => $siteB, 'type' => eZSiteAccess::TYPE_DEFAULT ) );
        $this->assertSame( $dirB . '/shared-ini-cache/', $GLOBALS['eZINI_CONFIG_CACHE_DIR'] );
    }

    /** The globals a persistent worker removes between requests: the next request starts with the defaults */
    public function testANewRequestWithoutTheGlobalsStartsWithTheDefaults()
    {
        list( $site, $siteDir ) = $this->site( 'd', array( 'UseGlobalLogDir' => 'disabled', 'LogDir' => '{dir}/log' ) );
        eZSiteAccess::change( array( 'name' => $site, 'type' => eZSiteAccess::TYPE_DEFAULT ) );
        $this->assertSame( $siteDir . '/log/', eZDebug::instance()->logDirectory() );

        foreach ( array( 'eZDebugGlobalInstance', 'eZDebugLogDir', 'eZINI_CONFIG_CACHE_DIR', 'eZSiteAccessINICacheDir', 'eZSiteAccessINICacheDirBefore', 'eZExpiryHandlerInstance' ) as $global )
        {
            unset( $GLOBALS[$global] );
        }

        $this->assertSame( 'var/log/', eZDebug::instance()->logDirectory() );
    }
}
