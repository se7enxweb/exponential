<?php
/**
 * The shared eZExpiryHandler follows the cache directory of the current siteaccess.
 *
 * The handler can be created before the siteaccess is known and then reads the expiry.php of the default VarDir. A
 * siteaccess with a VarDir of its own (multi-site hosting) has its own expiry.php. The handler kept the first file,
 * so clearing a cache of one site wrote the shared file and expired the caches of every site on the installation.
 * eZSiteAccess::change() now calls eZExpiryHandler::resetForCurrentCacheDirectory(), which stores the old instance
 * and drops it when its file is not the one of the current cache directory.
 *
 * Every test works in its own VarDir under the system temp directory and runs in its own process, because it
 * changes the global site.ini and the shared instance.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezutils/eZExpiryHandlerCacheDirectoryTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
class eZExpiryHandlerCacheDirectoryTest extends PHPUnit\Framework\TestCase
{
    private $varDirs = array();

    protected function tearDown(): void
    {
        foreach ( $this->varDirs as $varDir )
        {
            eZDir::recursiveDelete( $varDir );
        }
        parent::tearDown();
    }

    /**
     * Points FileSettings/VarDir at a new directory with an expiry.php holding $timestamps.
     */
    private function useVarDir( array $timestamps = array() )
    {
        $varDir = sys_get_temp_dir() . '/expiry-test-' . uniqid();
        mkdir( $varDir . '/cache', 0777, true );
        file_put_contents(
            $varDir . '/cache/expiry.php',
            "<?php\n\$Timestamps = " . var_export( $timestamps, true ) . ";\n?>"
        );
        $this->varDirs[] = $varDir;
        eZINI::instance()->setVariable( 'FileSettings', 'VarDir', $varDir );
        eZINI::instance()->setVariable( 'FileSettings', 'CacheDir', 'cache' );
        return $varDir;
    }

    public function testInstanceOfTheCurrentCacheDirectoryIsKept()
    {
        $this->useVarDir();
        $instance = eZExpiryHandler::instance();

        $this->assertFalse( eZExpiryHandler::resetForCurrentCacheDirectory() );
        $this->assertSame( $instance, eZExpiryHandler::instance() );
    }

    public function testInstanceOfAnotherCacheDirectoryIsReplaced()
    {
        $this->useVarDir( array( 'image-alias' => 100 ) );
        $this->assertSame( 100, eZExpiryHandler::getTimestamp( 'image-alias' ) );

        $siteVarDir = $this->useVarDir( array( 'image-alias' => 200 ) );

        $this->assertTrue( eZExpiryHandler::resetForCurrentCacheDirectory() );
        $this->assertSame( $siteVarDir . '/cache/expiry.php', eZExpiryHandler::instance()->CacheFile->name() );
        $this->assertSame( 200, eZExpiryHandler::getTimestamp( 'image-alias' ) );
    }

    /**
     * A timestamp set before the siteaccess change belongs to the file it was set for, and the file of the new
     * siteaccess stays untouched.
     */
    public function testTimestampsSetBeforeTheResetAreStoredIntoTheirOwnFile()
    {
        $defaultVarDir = $this->useVarDir( array( 'image-alias' => 100 ) );
        eZExpiryHandler::instance()->setTimestamp( 'image-alias', 150 );

        $siteVarDir = $this->useVarDir( array( 'image-alias' => 200 ) );
        eZExpiryHandler::resetForCurrentCacheDirectory();

        $this->assertSame( 150, eZExpiryHandler::fetchData( $defaultVarDir . '/cache/expiry.php' )['image-alias'] );
        $this->assertSame( 200, eZExpiryHandler::fetchData( $siteVarDir . '/cache/expiry.php' )['image-alias'] );
    }

    /**
     * eZSiteAccess::change() reloads site.ini for the siteaccess, which brings back the configured VarDir: the
     * instance created for the temporary VarDir no longer matches and is dropped.
     */
    public function testSiteAccessChangeDropsAnInstanceOfAnotherCacheDirectory()
    {
        $this->useVarDir();
        eZExpiryHandler::instance();

        eZSiteAccess::change( array( 'name' => 'expiry_test_siteaccess', 'type' => eZSiteAccess::TYPE_DEFAULT, 'uri_part' => array() ) );

        $this->assertFalse( eZExpiryHandler::hasInstance() );
    }
}
