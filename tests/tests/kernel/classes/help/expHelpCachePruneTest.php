<?php
/**
 * The help cache keeps only what the current index uses (doc/features/6.0/help-system.md). No database.
 *
 *  CP-01  a rebuild removes the other index-<signature>.php files and the navigation trees (nav/*.html) and rendered
 *         pages (render/*.json) written before it, keeps the current index, files written after the rebuild started
 *         and every file that is not a cache file of these kinds
 *  CP-02  pruneStale() does nothing without a cache directory
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpCachePruneTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

foreach ( array( 'expHelpDocumentIndex' => 'exphelpdocumentindex.php', 'expHelpAllowlist' => 'exphelpallowlist.php' ) as $helpClass => $helpFile )
    if ( !class_exists( $helpClass ) )
        require_once dirname( __DIR__, 5 ) . '/kernel/classes/help/' . $helpFile;
unset( $helpClass, $helpFile );

class expHelpCachePruneTest extends PHPUnit\Framework\TestCase
{
    private $base = '';

    protected function setUp(): void
    {
        $out = array();
        $rc = 1;
        @exec( 'git --version 2>/dev/null', $out, $rc );
        if ( $rc !== 0 )
            $this->markTestSkipped( 'git is not available' );
        $this->base = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-exphelp-prune-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
        mkdir( $this->base . '/install/doc/help', 0777, true );
        file_put_contents( $this->base . '/install/doc/help/start.md', "# Start\n\nText.\n" );
        foreach ( array( 'init -q', 'config user.email t@example.invalid', 'config user.name T', 'add -A', 'commit -q -m fixture' ) as $args )
            exec( 'git -C ' . escapeshellarg( $this->base . '/install' ) . ' ' . $args . ' 2>&1' );
    }

    protected function tearDown(): void
    {
        if ( $this->base !== '' && is_dir( $this->base ) )
            self::remove( $this->base );
    }

    private static function remove( $path )
    {
        if ( is_link( $path ) || is_file( $path ) )
            return unlink( $path );
        foreach ( scandir( $path ) as $name )
            if ( $name !== '.' && $name !== '..' )
                self::remove( $path . '/' . $name );
        rmdir( $path );
    }

    private function index( $cacheDir )
    {
        $root = $this->base . '/install';
        $allowlist = new expHelpAllowlist( array( 'root' => $root, 'roots' => array( 'doc' ), 'max_size' => 1024 * 1024 ) );
        return new expHelpDocumentIndex( $allowlist, array( 'root' => $root, 'cache_dir' => $cacheDir ) );
    }

    /** CP-01 */
    public function testRebuildPrunesStaleCacheFiles()
    {
        $cache = $this->base . '/cache';
        foreach ( array( '', '/nav', '/render', '/pdf' ) as $sub )
            mkdir( $cache . $sub, 0777, true );
        $old = time() - 3600;
        $stale = array( '/index-' . str_repeat( 'a', 40 ) . '.php', '/index-' . str_repeat( 'b', 40 ) . '.php',
                        '/nav/' . str_repeat( 'c', 40 ) . '.html', '/render/' . str_repeat( 'd', 40 ) . '.json' );
        $kept = array( '/pdf/' . str_repeat( 'e', 40 ) . '.pdf', '/search-' . str_repeat( 'f', 32 ) . '.php',
                       '/content-helpids.json', '/nav/notes.txt', '/render/' . str_repeat( 'd', 40 ) . '.json.123.tmp' );
        foreach ( array_merge( $stale, $kept ) as $file )
        {
            file_put_contents( $cache . $file, 'x' );
            touch( $cache . $file, $old );
        }
        // written by another process while this one rebuilds: newer than the start of the rebuild
        $fresh = '/render/' . str_repeat( '1', 40 ) . '.json';
        file_put_contents( $cache . $fresh, 'x' );
        touch( $cache . $fresh, time() + 60 );

        $stats = $this->index( $cache )->rebuild();
        $this->assertTrue( $stats['cached'] );
        // the old indexes may already go when the new one is stored; the derived files go in pruneStale()
        $this->assertGreaterThanOrEqual( 2, $stats['pruned'] );
        foreach ( $stale as $file )
            $this->assertFileDoesNotExist( $cache . $file );
        foreach ( array_merge( $kept, array( $fresh ) ) as $file )
            $this->assertFileExists( $cache . $file );
        $this->assertFileExists( $cache . '/index-' . $stats['signature'] . '.php', 'the current index stays' );
        $this->assertCount( 1, glob( $cache . '/index-*.php' ) );

        // a second rebuild of the same signature keeps its own index
        touch( $cache . '/index-' . $stats['signature'] . '.php', $old );
        $again = $this->index( $cache )->rebuild();
        $this->assertSame( $stats['signature'], $again['signature'] );
        $this->assertFileExists( $cache . '/index-' . $again['signature'] . '.php' );
    }

    /** CP-02 */
    public function testWithoutCacheDirectory()
    {
        $index = $this->index( false );
        $stats = $index->rebuild();
        $this->assertFalse( $stats['cached'] );
        $this->assertSame( 0, $stats['pruned'] );
        $this->assertSame( 0, $index->pruneStale( time() + 10 ) );
    }
}
