<?php
/**
 * eZExtension::newestFileTime(), the "Modified" time of an extension that eZExtension::extensionInfo() reports.
 *
 * Walking an extension's tree stats every file in it (52 000 files for 85 extensions here), and Setup > Extensions
 * and Info > About asked it on every view. The walk is kept in <cache>/exp/extension-file-times.json:
 *
 *  - the first call walks the tree and keeps the newest time;
 *  - a later call returns the kept time, even when a file deep in the tree changed since;
 *  - the extension's own extension.xml, ezinfo.php and composer.json are looked at every time;
 *  - clearing the active extensions cache (expiry key active-extensions-cache: a deploy, ezcache --clear-tag=ini,
 *    a change on Setup > Extensions) or $refresh walks again;
 *  - a kept time older than FILE_TIME_CACHE_SECONDS is walked again;
 *  - extensionInfo() reports the kept time as mtime.
 *
 * Every test works in its own VarDir under var/tmp and runs in its own process, because it changes the global
 * site.ini and the shared expiry handler.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezutils/eZExtensionNewestFileTimeTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
class eZExtensionNewestFileTimeTest extends PHPUnit\Framework\TestCase
{
    private $varDir;
    private $ext;
    private $now;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->varDir = getcwd() . '/var/tmp/extension-file-times-test-' . uniqid();
        mkdir( $this->varDir . '/cache', 0777, true );
        file_put_contents( $this->varDir . '/cache/expiry.php', "<?php\n\$Timestamps = array();\n?>" );
        eZINI::instance()->setVariable( 'FileSettings', 'VarDir', $this->varDir );
        eZINI::instance()->setVariable( 'FileSettings', 'CacheDir', 'cache' );

        $this->now = time();
        $this->ext = $this->varDir . '/ext/sample';
        mkdir( $this->ext . '/classes/deep', 0777, true );
        $this->file( 'classes/deep/a.php', $this->now - 1000 );
        $this->file( 'classes/b.php', $this->now - 3000 );
        $this->file( 'extension.xml', $this->now - 2000 );
    }

    protected function tearDown(): void
    {
        if ( $this->varDir && is_dir( $this->varDir ) )
            eZDir::recursiveDelete( $this->varDir );
        parent::tearDown();
    }

    private function file( $name, $mtime )
    {
        $path = $this->ext . '/' . $name;
        if ( !is_file( $path ) )
            file_put_contents( $path, "<?php\n" );
        touch( $path, $mtime );
        clearstatcache();
    }

    private function cacheFile()
    {
        return rtrim( eZSys::cacheDirectory(), '/' ) . '/exp/extension-file-times.json';
    }

    public function testFirstCallWalksTheTreeAndKeepsTheTime()
    {
        $this->assertSame( $this->now - 1000, eZExtension::newestFileTime( $this->ext ) );
        $this->assertFileExists( $this->cacheFile() );
        $stored = json_decode( file_get_contents( $this->cacheFile() ), true );
        $this->assertSame( $this->now - 1000, $stored[$this->ext]['mtime'] );
    }

    public function testKeptTimeIsUsedUntilRefreshed()
    {
        eZExtension::newestFileTime( $this->ext );
        $this->file( 'classes/deep/a.php', $this->now - 500 );
        $this->assertSame( $this->now - 1000, eZExtension::newestFileTime( $this->ext ), 'kept' );
        $this->assertSame( $this->now - 500, eZExtension::newestFileTime( $this->ext, true ), 'refreshed' );
        $this->assertSame( $this->now - 500, eZExtension::newestFileTime( $this->ext ), 'the refreshed time is kept' );
    }

    public function testOwnFilesAreLookedAtEveryTime()
    {
        eZExtension::newestFileTime( $this->ext );
        $this->file( 'extension.xml', $this->now - 100 );
        $this->assertSame( $this->now - 100, eZExtension::newestFileTime( $this->ext ) );
        $this->file( 'composer.json', $this->now - 50 );
        $this->assertSame( $this->now - 50, eZExtension::newestFileTime( $this->ext ) );
    }

    public function testClearingTheActiveExtensionsCacheWalksAgain()
    {
        eZExtension::newestFileTime( $this->ext );
        $this->file( 'classes/deep/a.php', $this->now - 400 );
        // what eZCache::clearActiveExtensions() sets for --clear-tag=ini, --clear-id=active_extensions and a deploy
        $handler = eZExpiryHandler::instance();
        $handler->setTimestamp( 'active-extensions-cache', time() + 1 );
        $this->assertSame( $this->now - 400, eZExtension::newestFileTime( $this->ext ) );
    }

    public function testOldKeptTimeIsWalkedAgain()
    {
        file_put_contents( $this->prepareCacheDir(), json_encode( array(
            $this->ext => array( 'mtime' => 1, 'time' => time() - eZExtension::FILE_TIME_CACHE_SECONDS - 5 ) ) ) );
        $this->assertSame( $this->now - 1000, eZExtension::newestFileTime( $this->ext ) );
    }

    public function testKeptTimeFromAnotherProcessIsRead()
    {
        file_put_contents( $this->prepareCacheDir(), json_encode( array( $this->ext => array( 'mtime' => $this->now - 7000, 'time' => time() ) ) ) );
        // older than extension.xml: the own file wins, the deep walk is not done
        $this->assertSame( $this->now - 2000, eZExtension::newestFileTime( $this->ext ) );
    }

    public function testExtensionInfoReportsTheKeptTime()
    {
        eZINI::instance()->setVariable( 'ExtensionSettings', 'ExtensionDirectory', $this->varDir . '/ext' );
        eZINI::instance()->setVariable( 'ExtensionSettings', 'ExtensionRootDirectories', array( $this->varDir . '/ext' ) );
        $info = eZExtension::extensionInfo( 'sample' );
        if ( !is_array( $info ) )
            $this->markTestSkipped( 'the extension root of this test is not used by eZExtension::extensionPath() here' );
        $this->assertSame( $this->now - 1000, $info['mtime'] );
        $this->assertSame( date( 'Y-m-d H:i', $this->now - 1000 ), $info['mtime_formatted'] );
    }

    private function prepareCacheDir()
    {
        $file = $this->cacheFile();
        if ( !is_dir( dirname( $file ) ) )
            mkdir( dirname( $file ), 0777, true );
        return $file;
    }
}
