<?php
/**
 * The help cache keeps only what the current index uses (doc/eng-us/features/6.0/help-system.md). No database.
 *
 *  CP-01  a rebuild removes the other index-<signature>.php files and the navigation trees (nav/*.html) written
 *         before it, keeps the current index, files written after the rebuild started, the rendered pages and PDFs
 *         (keyed on the link signature, they outlive an index) and every file that is not a cache file of these kinds
 *  CP-02  pruneStale() does nothing without a cache directory
 *  CP-03  pruneUnused() removes rendered pages and PDFs not used for the given days and temporary files older than a
 *         day, keeps the rest and other files; with dry run it only counts; keepInCache() renews a day-old file
 *  CP-04  the link signature stays when a page's text changes and changes when a page is added or a heading (an
 *         anchor) changes, while the index signature changes with every one of them
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpCachePruneTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
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
                        '/nav/' . str_repeat( 'c', 40 ) . '.html' );
        $kept = array( '/pdf/' . str_repeat( 'e', 40 ) . '.pdf', '/render/' . str_repeat( 'd', 40 ) . '.json',
                       '/search-' . str_repeat( 'f', 32 ) . '.php',
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
        $this->assertGreaterThanOrEqual( 1, $stats['pruned'] );
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

    /** CP-03 */
    public function testPruneUnusedRemovesOnlyOldPageCacheFiles()
    {
        $cache = $this->base . '/cache';
        foreach ( array( '/render', '/pdf', '/nav' ) as $sub )
            mkdir( $cache . $sub, 0777, true );
        $files = array(
            '/render/' . str_repeat( 'a', 40 ) . '.json'          => time() - 20 * 86400,   // unused: goes
            '/pdf/' . str_repeat( 'b', 40 ) . '.pdf'              => time() - 20 * 86400,   // unused: goes
            '/pdf/' . str_repeat( 'c', 40 ) . '.pdf'              => time() - 3 * 86400,    // in use: stays
            '/pdf/' . str_repeat( 'd', 40 ) . '.pdf.123.tmp'      => time() - 2 * 86400,    // stopped write: goes
            '/pdf/' . str_repeat( 'e', 40 ) . '.pdf.124.tmp'      => time() - 600,          // being written: stays
            '/pdf/notes.txt'                                      => time() - 90 * 86400,   // not a cache file: stays
            '/nav/' . str_repeat( 'f', 40 ) . '.html'             => time() - 90 * 86400,   // not a page cache: stays
        );
        foreach ( $files as $file => $time )
        {
            file_put_contents( $cache . $file, 'xx' );
            touch( $cache . $file, $time );
        }
        $index = $this->index( $cache );

        $dry = $index->pruneUnused( 14, true );
        $this->assertSame( 3, $dry['files'] );
        $this->assertSame( 6, $dry['bytes'] );
        $this->assertFileExists( $cache . '/pdf/' . str_repeat( 'b', 40 ) . '.pdf', 'a dry run removes nothing' );

        $done = $index->pruneUnused( 14 );
        $this->assertSame( 3, $done['files'] );
        $this->assertSame( 2, $done['kept'] );
        foreach ( array( '/render/' . str_repeat( 'a', 40 ) . '.json', '/pdf/' . str_repeat( 'b', 40 ) . '.pdf',
                         '/pdf/' . str_repeat( 'd', 40 ) . '.pdf.123.tmp' ) as $gone )
            $this->assertFileDoesNotExist( $cache . $gone );
        foreach ( array( '/pdf/' . str_repeat( 'c', 40 ) . '.pdf', '/pdf/' . str_repeat( 'e', 40 ) . '.pdf.124.tmp',
                         '/pdf/notes.txt', '/nav/' . str_repeat( 'f', 40 ) . '.html' ) as $stays )
            $this->assertFileExists( $cache . $stays );

        // a file served from the cache: renewed once it is a day old, so the next prune keeps it
        $used = $cache . '/pdf/' . str_repeat( 'c', 40 ) . '.pdf';
        expHelpDocumentIndex::keepInCache( $used );
        clearstatcache( true, $used );
        $this->assertGreaterThan( time() - 60, filemtime( $used ) );
    }

    /** CP-04 */
    public function testLinkSignatureFollowsLinksNotText()
    {
        $root = $this->base . '/install';
        $pages = $root . '/doc/notes';
        $commit = function ( $message ) use ( $root ) {
            foreach ( array( 'add -A', 'commit -q -m ' . escapeshellarg( $message ) ) as $args )
                exec( 'git -C ' . escapeshellarg( $root ) . ' ' . $args . ' 2>&1' );
        };
        mkdir( $pages );
        file_put_contents( $pages . '/start.md', "# Start\n\nText.\n" );
        $commit( 'notes' );
        $state = function () {
            $index = $this->index( $this->base . '/cache' );
            $index->rebuild();
            return array( $index->signature(), $index->linkSignature() );
        };
        list( $signature, $links ) = $state();

        file_put_contents( $pages . '/start.md', "# Start\n\nOther text, same heading.\n" );
        $commit( 'text' );
        list( $signature2, $links2 ) = $state();
        $this->assertNotSame( $signature, $signature2, 'the index signature follows the text' );
        $this->assertSame( $links, $links2, 'the link signature does not' );

        file_put_contents( $pages . '/start.md', "# Start\n\n## A new section\n\nOther text, same heading.\n" );
        $commit( 'heading' );
        list( , $links3 ) = $state();
        $this->assertNotSame( $links2, $links3, 'a new heading is a new anchor' );

        file_put_contents( $pages . '/second.md', "# Second\n" );
        $commit( 'page' );
        list( , $links4 ) = $state();
        $this->assertNotSame( $links3, $links4, 'a new page is a new link target' );
    }
}
