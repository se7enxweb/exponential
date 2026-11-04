<?php
/**
 * A help page never waits for the help index to be built while an older one is there, and the console and the web
 * build the same index (doc/eng-us/features/6.0/help-system.md). No database.
 *
 *  BB-01  a web load that finds no index of its signature but an older one answers from the older one (its
 *         documents and signature), builds nothing and starts one background build; a second load meanwhile starts
 *         none (the marker); the build (a console load) writes the new index and removes the marker, and the next
 *         web load uses the new index without starting anything
 *  BB-02  with no index at all the web load builds it itself (nothing older to answer from)
 *  BB-03  no background build is started while another process holds the build lock
 *  BB-04  the build lock and the marker get the storage file mode, as the index does, so a console run as root
 *         leaves them usable by the web server
 *  BB-05  the extensions whose documentation is indexed are the same whichever siteaccess asks: ActiveExtensions[]
 *         and the ActiveAccessExtensions[] of every siteaccess, in an order that does not follow the current one
 *  BB-06  the navigation order (neighbours()) is kept in order/<signature>.php and read from there, with the same
 *         previous and next pages
 *  BB-07  the background command is exp:help index for the index's language, without the image download
 *  BB-08  exp:velocity deploy builds the help index (bin/php/exphelp.php index), and its failure does not stop it
 *  BB-09  a rewrite of the git index that changes no page: the web load answers from the older allowlist, writes
 *         none and starts one background build; the console writes the current allowlist, finds the index current
 *         (builds none) and removes the marker; the next web load starts nothing
 *  BB-10  a page added while the older allowlist is served (StatFilesInterval set) is in the index the background
 *         build writes, and found by the next web load
 *  BB-11  the background build keeps a current search index (exp:help index --keep-search)
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpIndexBackgroundBuildTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

foreach ( array( 'expHelpDocumentIndex' => 'exphelpdocumentindex.php', 'expHelpAllowlist' => 'exphelpallowlist.php' ) as $helpClass => $helpFile )
    if ( !class_exists( $helpClass ) )
        require_once dirname( __DIR__, 5 ) . '/kernel/classes/help/' . $helpFile;
unset( $helpClass, $helpFile );

class expHelpIndexBackgroundBuildTest extends PHPUnit\Framework\TestCase
{
    private $base = '';

    /** @var int how often the background build was started */
    private $started = 0;

    protected function setUp(): void
    {
        $out = array();
        $rc = 1;
        @exec( 'git --version 2>/dev/null', $out, $rc );
        if ( $rc !== 0 )
            $this->markTestSkipped( 'git is not available' );
        $this->base = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-exphelp-background-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
        mkdir( $this->base . '/install/doc/eng-us/help', 0777, true );
        file_put_contents( $this->base . '/install/doc/eng-us/help/start.md', "# Start\n\nText about publishing.\n" );
        file_put_contents( $this->base . '/install/doc/eng-us/help/other.md', "# Other\n\nMore text.\n" );
        file_put_contents( $this->base . '/install/doc/eng-us/help/third.md', "# Third\n\nEven more.\n" );
        $this->git( 'init -q' );
        $this->git( 'config user.email t@example.invalid' );
        $this->git( 'config user.name T' );
        $this->git( 'add -A' );
        $this->git( 'commit -q -m fixture' );
        mkdir( $this->base . '/cache', 0777, true );
        $this->started = 0;
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

    /** A new index as each request makes one: $web answers from an older index and records the background start. */
    private function index( $web, array $options = array() )
    {
        $allowlist = new expHelpAllowlist( array( 'root' => $this->base . '/install', 'roots' => array( 'doc' ), 'max_size' => 1024 * 1024 ) );
        $started = &$this->started;
        return new expHelpDocumentIndex( $allowlist, $options + array( 'cache_dir' => $this->base . '/cache',
            'background' => $web ? function () use ( &$started ) { ++$started; } : false ) );
    }

    private function allowlistFiles()
    {
        $files = glob( $this->base . '/cache/allowlist-*.php' );
        sort( $files );
        return $files;
    }

    /** A git operation that changes no page (git status, a commit elsewhere) rewrites the git index. */
    private function rewriteGitIndex()
    {
        $file = $this->base . '/install/.git/index';
        touch( $file, time() + 10 );
        clearstatcache();
    }

    private function indexFiles()
    {
        $files = glob( $this->base . '/cache/index-*.php' );
        sort( $files );
        return $files;
    }

    private function addPage()
    {
        file_put_contents( $this->base . '/install/doc/eng-us/help/new.md', "# New\n\nA new page.\n" );
        $this->git( 'add doc/eng-us/help/new.md' );
        clearstatcache();
    }

    /** BB-01 */
    public function testWebLoadServesTheOlderIndexAndBuildsInTheBackground()
    {
        $first = $this->index( true );
        $this->assertCount( 3, $first->documents(), 'the first index is built in the request (BB-02)' );
        $old = $first->signature();
        $this->assertSame( 0, $this->started );

        $this->addPage();
        $during = $this->index( true );
        $this->assertCount( 3, $during->documents(), 'the index of a moment ago' );
        $this->assertSame( $old, $during->signature(), 'and what is derived from it follows its signature' );
        $this->assertCount( 1, $this->indexFiles(), 'nothing was built in the request' );
        $this->assertSame( 1, $this->started, 'one background build was started' );
        $this->assertFileExists( $this->base . '/cache/' . expHelpDocumentIndex::BUILD_MARKER_FILE );

        $again = $this->index( true );
        $this->assertCount( 3, $again->documents() );
        $this->assertSame( 1, $this->started, 'not started twice while it runs' );

        // the background build: the console builds and waits
        $console = $this->index( false );
        $this->assertCount( 4, $console->documents() );
        $this->assertNotSame( $old, $console->signature() );
        $this->assertFileDoesNotExist( $this->base . '/cache/' . expHelpDocumentIndex::BUILD_MARKER_FILE, 'the build removed its marker' );

        $after = $this->index( true );
        $this->assertCount( 4, $after->documents(), 'the next page uses the new index' );
        $this->assertSame( $console->signature(), $after->signature() );
        $this->assertSame( 1, $this->started );
    }

    /** BB-02 */
    public function testWithoutAnyIndexTheWebLoadBuildsIt()
    {
        $index = $this->index( true );
        $this->assertCount( 3, $index->documents() );
        $this->assertCount( 1, $this->indexFiles() );
        $this->assertSame( 0, $this->started );
    }

    /** BB-03 */
    public function testNoBackgroundBuildWhileAnotherProcessBuilds()
    {
        $this->assertCount( 3, $this->index( true )->documents() );
        $this->addPage();
        $lock = fopen( $this->base . '/cache/' . expHelpDocumentIndex::BUILD_LOCK_FILE, 'c' );
        $this->assertTrue( flock( $lock, LOCK_EX | LOCK_NB ) );
        $this->assertCount( 3, $this->index( true )->documents() );
        $this->assertSame( 0, $this->started, 'the build that runs is not started again' );
        flock( $lock, LOCK_UN );
        fclose( $lock );
        $this->assertCount( 3, $this->index( true )->documents() );
        $this->assertSame( 1, $this->started, 'once the lock is free, a build is started' );
    }

    /** BB-04 */
    public function testLockAndMarkerHaveTheStorageMode()
    {
        $old = umask( 022 );
        try
        {
            $this->assertCount( 3, $this->index( false )->documents() );
            $this->addPage();
            $this->index( true )->documents();
        }
        finally
        {
            umask( $old );
        }
        clearstatcache();
        $files = $this->indexFiles();
        $mode = fileperms( $files[0] ) & 0777;
        $this->assertSame( $mode, fileperms( $this->base . '/cache/' . expHelpDocumentIndex::BUILD_LOCK_FILE ) & 0777, 'the lock' );
        $this->assertSame( $mode, fileperms( $this->base . '/cache/' . expHelpDocumentIndex::BUILD_MARKER_FILE ) & 0777, 'the marker' );
    }

    /** BB-05 */
    public function testIndexedExtensionsDoNotDependOnTheSiteaccess()
    {
        $active = array( 'expui', 'ezjscore' );
        $admin = expHelpAllowlist::installationExtensions( array( 'admin' => array( 'expai', 'exp_adminui' ), 'site' => array(),
                                                                  'editor' => array( 'expai', 'expmcp' ) ), $active );
        $site = expHelpAllowlist::installationExtensions( array( 'site' => array(), 'editor' => array( 'expai', 'expmcp' ),
                                                                 'admin' => array( 'expai', 'exp_adminui' ) ), $active );
        $this->assertSame( $admin, $site );
        $this->assertSame( array( 'expui', 'ezjscore', 'expai', 'exp_adminui', 'expmcp' ), $admin );
    }

    /** BB-06 */
    public function testNavigationOrderIsCached()
    {
        $index = $this->index( false );
        $neighbours = $index->neighbours( 'help/start.md' );
        $this->assertNotNull( $neighbours['prev'] );
        $this->assertNotNull( $neighbours['next'] );
        $file = $this->base . '/cache/order/' . $index->signature() . '.php';
        $this->assertFileExists( $file );
        $this->assertSame( $index->pageOrder(), include $file );

        $again = $this->index( false );
        $this->assertSame( $neighbours, $again->neighbours( 'help/start.md' ) );
        $this->assertSame( array( 'prev' => null, 'next' => null ), $again->neighbours( 'help/missing.md' ) );
    }

    /** BB-07 */
    public function testBackgroundCommand()
    {
        mkdir( $this->base . '/install/bin/php', 0777, true );
        file_put_contents( $this->base . '/install/bin/php/exphelp.php', "<?php\n" );
        $command = $this->index( false )->backgroundCommand();
        if ( $command === null )
            $this->markTestSkipped( 'no setsid or command line PHP here' );
        $this->assertSame( '-f', $command[1] );
        $this->assertSame( array( 'bin/php/exphelp.php', 'index', '--language=eng-us', '--no-images' ), array_slice( $command, 3, 4 ) );
        $this->assertContains( '--allow-root-user', $command );
    }

    /** BB-08 */
    public function testDeployBuildsTheHelpIndex()
    {
        $class = new ReflectionClass( 'expVelocityDeploy' );
        $deploy = $class->newInstanceWithoutConstructor();
        $root = $class->getProperty( 'root' );
        if ( PHP_VERSION_ID < 80100 )
            $root->setAccessible( true );
        $root->setValue( $deploy, dirname( __DIR__, 5 ) );
        $method = $class->getMethod( 'helpIndexStep' );
        if ( PHP_VERSION_ID < 80100 )
            $method->setAccessible( true );
        $step = $method->invoke( $deploy );
        $this->assertSame( 'help-index', $step['id'] );
        $this->assertSame( '', $step['skip'] );
        $this->assertSame( 'php bin/php/exphelp.php index --no-images --allow-root-user --no-colors', $step['command'] );
    }

    /** BB-09 */
    public function testGitIndexRewriteServesTheOlderAllowlist()
    {
        $this->assertCount( 3, $this->index( true )->documents(), 'with no allowlist at all the request builds it' );
        $lists = $this->allowlistFiles();
        $this->assertCount( 1, $lists );
        $this->assertSame( 0, $this->started );

        $this->rewriteGitIndex();
        $web = $this->index( true );
        $this->assertCount( 3, $web->documents() );
        $this->assertSame( $lists, $this->allowlistFiles(), 'the request wrote no allowlist' );
        $this->assertSame( 1, $this->started, 'the background build was started once' );
        $this->assertFileExists( $this->base . '/cache/' . expHelpDocumentIndex::BUILD_MARKER_FILE );
        $this->index( true )->documents();
        $this->assertSame( 1, $this->started, 'not twice while it runs' );

        // the background build: the console writes the current allowlist and finds the index current
        $console = $this->index( false );
        $this->assertCount( 3, $console->documents() );
        $this->assertCount( 2, $this->allowlistFiles(), 'the current allowlist is written beside the older one' );
        $this->assertCount( 1, $this->indexFiles(), 'the pages did not change: the index is not built again' );
        $this->assertFileDoesNotExist( $this->base . '/cache/' . expHelpDocumentIndex::BUILD_MARKER_FILE, 'the build is done' );

        $this->assertCount( 3, $this->index( true )->documents() );
        $this->assertSame( 1, $this->started, 'the next request uses the current allowlist' );
    }

    /** BB-10 */
    public function testPageAddedWhileTheOlderAllowlistIsServedIsIndexedByTheBackgroundBuild()
    {
        $options = array( 'stat_interval' => 300 );
        $this->assertCount( 3, $this->index( false, $options )->documents() );
        $this->addPage();
        $this->assertCount( 3, $this->index( true, $options )->documents(), 'the older allowlist and index answer' );
        $this->assertSame( 1, $this->started );
        // the signature the request took from the older allowlist is not reused by the build
        $console = $this->index( false, $options );
        $this->assertCount( 4, $console->documents() );
        $this->assertNotNull( $console->document( 'help/new.md' ) );
        $after = $this->index( true, $options );
        $this->assertNotNull( $after->document( 'help/new.md' ), 'right after the build the new page is found' );
        $this->assertSame( 1, $this->started );
    }

    /** BB-11 */
    public function testBackgroundCommandKeepsACurrentSearchIndex()
    {
        mkdir( $this->base . '/install/bin/php', 0777, true );
        file_put_contents( $this->base . '/install/bin/php/exphelp.php', "<?php\n" );
        $command = $this->index( false )->backgroundCommand();
        if ( $command === null )
            $this->markTestSkipped( 'no setsid or command line PHP here' );
        $this->assertContains( '--keep-search', $command );
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/private/classes/commands/exphelp.php' );
        $this->assertStringContainsString( '[keep-search]', $source, 'exp:help knows the option' );
    }
}
