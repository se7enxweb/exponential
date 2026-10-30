<?php
/**
 * The help index is built once per change of the help files, not once per touch of the git index, and only by one
 * process at a time (doc/eng-us/features/6.0/help-system.md). No database.
 *
 *  IS-01  a second load of the index for the same language, after the git index was written again without a
 *         change to a help file (git status, a commit elsewhere), reads the cached index: same signature, no new
 *         index-<signature>.php, the same build time
 *  IS-02  the signature still changes when a help page changes, when one is added and when a commit is made
 *         (the history), so a stale index is never used
 *  IS-03  while another process holds the build lock, a load uses the newest index in the cache (its signature
 *         and documents) and builds nothing; once the lock is free the next load builds the current one
 *  IS-04  the search index, while another process builds it, answers from the newest one already there
 *  IS-05  unlinkCompiled() removes a cache file (and drops its OPcache copy)
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpIndexStabilityTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

foreach ( array( 'expHelpDocumentIndex' => 'exphelpdocumentindex.php', 'expHelpAllowlist' => 'exphelpallowlist.php',
                 'expHelpSearch' => 'exphelpsearch.php' ) as $helpClass => $helpFile )
    if ( !class_exists( $helpClass ) )
        require_once dirname( __DIR__, 5 ) . '/kernel/classes/help/' . $helpFile;
unset( $helpClass, $helpFile );

class expHelpIndexStabilityTest extends PHPUnit\Framework\TestCase
{
    private $base = '';

    protected function setUp(): void
    {
        $out = array();
        $rc = 1;
        @exec( 'git --version 2>/dev/null', $out, $rc );
        if ( $rc !== 0 )
            $this->markTestSkipped( 'git is not available' );
        $this->base = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-exphelp-stability-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
        mkdir( $this->base . '/install/doc/eng-us/help', 0777, true );
        file_put_contents( $this->base . '/install/doc/eng-us/help/start.md', "# Start\n\nText about publishing.\n" );
        file_put_contents( $this->base . '/install/doc/eng-us/help/other.md', "# Other\n\nMore text.\n" );
        mkdir( $this->base . '/install/lib', 0777, true );
        file_put_contents( $this->base . '/install/lib/code.php', "<?php\n" );
        $this->git( 'init -q' );
        $this->git( 'config user.email t@example.invalid' );
        $this->git( 'config user.name T' );
        $this->git( 'add -A' );
        $this->git( 'commit -q -m fixture' );
        mkdir( $this->base . '/cache', 0777, true );
    }

    protected function tearDown(): void
    {
        if ( $this->base !== '' && is_dir( $this->base ) )
            self::remove( $this->base );
    }

    private function git( $args )
    {
        exec( 'git -C ' . escapeshellarg( $this->base . '/install' ) . ' ' . $args . ' 2>&1' );
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

    /** A new index as each request makes one (stat_interval 0: the files are looked at every time). */
    private function index()
    {
        $root = $this->base . '/install';
        $allowlist = new expHelpAllowlist( array( 'root' => $root, 'roots' => array( 'doc' ), 'max_size' => 1024 * 1024 ) );
        return new expHelpDocumentIndex( $allowlist, array( 'root' => $root, 'cache_dir' => $this->base . '/cache' ) );
    }

    private function indexFiles()
    {
        $files = glob( $this->base . '/cache/index-*.php' );
        sort( $files );
        return $files;
    }

    /** Writes the git index again, later, with nothing changed in the help files (what git status or a commit of another file does). */
    private function touchGitIndex( $seconds )
    {
        file_put_contents( $this->base . '/install/lib/code.php', "<?php\n// changed\n" );
        $this->git( 'add lib/code.php' );
        $this->git( 'reset -q lib/code.php' );
        $later = time() + $seconds;
        touch( $this->base . '/install/.git/index', $later );
        clearstatcache();
    }

    /** IS-01 */
    public function testSecondLoadAfterGitIndexWriteDoesNotRebuild()
    {
        $first = $this->index();
        $this->assertCount( 2, $first->documents() );
        $signature = $first->signature();
        $files = $this->indexFiles();
        $this->assertCount( 1, $files );
        $built = ( include $files[0] )['built'];

        $stampBefore = ( new expHelpAllowlist( array( 'root' => $this->base . '/install', 'roots' => array( 'doc' ) ) ) )->stamp();
        $this->touchGitIndex( 5 );
        $stampAfter = ( new expHelpAllowlist( array( 'root' => $this->base . '/install', 'roots' => array( 'doc' ) ) ) )->stamp();
        $this->assertNotSame( $stampBefore, $stampAfter, 'the git index was written again (the allowlist stamp moved)' );

        sleep( 1 );
        $second = $this->index();
        $this->assertCount( 2, $second->documents() );
        $this->assertSame( $signature, $second->signature(), 'the signature does not follow a rewrite of the git index' );
        $this->assertSame( $files, $this->indexFiles(), 'no new index file' );
        $this->assertSame( $built, ( include $files[0] )['built'], 'the cached index was read, not built again' );
    }

    /** IS-02 */
    public function testSignatureFollowsHelpFilesAndCommits()
    {
        $signature = $this->index()->signature();

        file_put_contents( $this->base . '/install/doc/eng-us/help/start.md', "# Start\n\nText about publishing, longer now.\n" );
        clearstatcache();
        $changed = $this->index()->signature();
        $this->assertNotSame( $signature, $changed, 'a changed page' );

        file_put_contents( $this->base . '/install/doc/eng-us/help/new.md', "# New\n\nA new page.\n" );
        $this->git( 'add doc/eng-us/help/new.md' );
        clearstatcache();
        $added = $this->index()->signature();
        $this->assertNotSame( $changed, $added, 'an added page' );

        $this->git( 'commit -q -m "add a page"' );
        clearstatcache();
        $committed = $this->index()->signature();
        $this->assertNotSame( $added, $committed, 'a commit (the history of the pages)' );
        $this->assertCount( 3, $this->index()->documents() );
    }

    /** IS-03 */
    public function testLoadUsesNewestIndexWhileAnotherProcessBuilds()
    {
        $first = $this->index();
        $this->assertCount( 2, $first->documents() );
        $old = $first->signature();

        file_put_contents( $this->base . '/install/doc/eng-us/help/new.md', "# New\n\nA new page.\n" );
        $this->git( 'add doc/eng-us/help/new.md' );
        clearstatcache();

        // another process builds: it holds the lock (flock locks belong to the open file, so this handle conflicts)
        $lock = fopen( $this->base . '/cache/' . expHelpDocumentIndex::BUILD_LOCK_FILE, 'c' );
        $this->assertTrue( flock( $lock, LOCK_EX | LOCK_NB ) );
        $during = $this->index();
        $this->assertCount( 2, $during->documents(), 'the index of a moment ago' );
        $this->assertSame( $old, $during->signature(), 'and what is derived from it follows its signature' );
        $this->assertCount( 1, $this->indexFiles(), 'nothing was built meanwhile' );
        flock( $lock, LOCK_UN );
        fclose( $lock );

        $after = $this->index();
        $this->assertCount( 3, $after->documents() );
        $this->assertNotSame( $old, $after->signature() );
    }

    /** IS-04 */
    public function testSearchUsesNewestIndexWhileAnotherProcessBuilds()
    {
        $cache = $this->base . '/cache';
        $search = new expHelpSearch( $this->index(), array( 'cacheDir' => $cache ) );
        $this->assertNotEmpty( $search->search( 'publishing' )['results'] );
        $this->assertCount( 1, glob( $cache . '/search-*.php' ) );

        file_put_contents( $this->base . '/install/doc/eng-us/help/new.md', "# New\n\nAnother page.\n" );
        $this->git( 'add doc/eng-us/help/new.md' );
        clearstatcache();
        $lock = fopen( $cache . '/search.lock', 'c' );
        $this->assertTrue( flock( $lock, LOCK_EX | LOCK_NB ) );
        // the document index of the new files exists (built by its own process), the search index not yet
        $current = $this->index();
        $this->assertCount( 3, $current->documents() );
        $during = new expHelpSearch( $current, array( 'cacheDir' => $cache ) );
        $this->assertNotEmpty( $during->search( 'publishing' )['results'], 'answered from the older search index' );
        $this->assertCount( 1, glob( $cache . '/search-*.php' ), 'nothing was built meanwhile' );
        flock( $lock, LOCK_UN );
        fclose( $lock );
    }

    /** IS-05 */
    public function testUnlinkCompiledRemovesTheFile()
    {
        $file = $this->base . '/cache/index-' . str_repeat( 'a', 40 ) . '.php';
        file_put_contents( $file, "<?php\nreturn array();\n" );
        $this->assertSame( array(), include $file );
        $this->assertTrue( expHelpDocumentIndex::unlinkCompiled( $file ) );
        $this->assertFileDoesNotExist( $file );
        $this->assertFalse( expHelpDocumentIndex::unlinkCompiled( $file ) );
    }
}
