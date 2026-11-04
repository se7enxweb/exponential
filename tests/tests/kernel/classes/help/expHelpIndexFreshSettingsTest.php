<?php
/**
 * exp:help index reads exphelp.ini as it is on disk, even where config.php turns the INI file time check off
 * (doc/eng-us/features/6.0/help-system.md, "On the command line"). No database.
 *
 *  FS-01  a cached copy of exphelp.ini older than a file it was built from is removed before the index is built, so
 *         the index takes the new settings (a PageOrder[] entry added by a landing)
 *  FS-02  a current cached copy is kept, and the cached copies of other INI files are not touched
 *
 * The tests use an INI cache directory of their own below var/tmp of the installation root; var/cache/ini is not
 * touched.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpIndexFreshSettingsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpIndexFreshSettingsTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $savedCacheDir;

    protected function setUp(): void
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/exphelp_fresh_settings_' . uniqid( '', true );
        mkdir( $this->dir . '/cache', 0700, true );
        $this->savedCacheDir = array_key_exists( 'eZINI_CONFIG_CACHE_DIR', $GLOBALS ) ? array( $GLOBALS['eZINI_CONFIG_CACHE_DIR'] ) : null;
        $GLOBALS['eZINI_CONFIG_CACHE_DIR'] = $this->dir . '/cache/';
    }

    protected function tearDown(): void
    {
        if ( $this->savedCacheDir === null )
            unset( $GLOBALS['eZINI_CONFIG_CACHE_DIR'] );
        else
            $GLOBALS['eZINI_CONFIG_CACHE_DIR'] = $this->savedCacheDir[0];
        foreach ( glob( $this->dir . '/{cache/*,*}', GLOB_BRACE ) ?: array() as $file )
        {
            if ( is_file( $file ) )
                unlink( $file );
        }
        rmdir( $this->dir . '/cache' );
        rmdir( $this->dir );
    }

    public function testAStaleCachedCopyOfTheHelpSettingsIsRemovedBeforeTheIndexIsBuilt()
    {
        $source = $this->writeSource( 'exphelp.ini.append.php', time() );
        $cache = $this->writeCache( 'exphelp-0123456789abcdef.php', $source, time() - 60 );

        $this->assertSame( 1, \Exponential\Command\Kernel\Exphelp::freshSettings() );
        $this->assertFileDoesNotExist( $cache );
    }

    public function testACurrentCachedCopyAndOtherSettingsFilesAreKept()
    {
        $source = $this->writeSource( 'exphelp.ini.append.php', time() - 60 );
        $current = $this->writeCache( 'exphelp-0123456789abcdef.php', $source, time() );
        $other = $this->writeCache( 'site-0123456789abcdef.php', $this->writeSource( 'site.ini.append.php', time() ), time() - 60 );

        $this->assertSame( 0, \Exponential\Command\Kernel\Exphelp::freshSettings() );
        $this->assertFileExists( $current );
        $this->assertFileExists( $other, 'only the copies of exphelp.ini are looked at' );
    }

    private function writeSource( $name, $mtime )
    {
        $path = $this->dir . '/' . $name;
        file_put_contents( $path, "<?php /*\n[TreeSettings]\nPageOrder[EXTENDING.md]=13\n*/ ?>\n" );
        touch( $path, $mtime );
        clearstatcache( true, $path );
        return $path;
    }

    private function writeCache( $name, $source, $created )
    {
        $path = $this->dir . '/cache/' . $name;
        $data = array( 'rev' => 2, 'created' => date( 'c', $created ), 'files' => array( $source ), 'val' => array() );
        file_put_contents( $path, '<?php $data = ' . var_export( $data, true ) . ";\n" );
        return $path;
    }
}
