<?php
/**
 * The backlog of image aliases made after the answer ([AliasSettings] Backlog, expImageAliasBacklog), without a
 * database:
 *
 *  IAB-01 - when it applies: the setting, on-demand mode, a server that can answer first (not the command line)
 *  IAB-02 - entries: one per alias, variant formats and siteaccess; taken oldest first, by one process only; finished
 *           ones gone; a taken entry whose process died waits again after STALE_WORK
 *  IAB-03 - a request with the backlog and every slot taken answers at once (no wait within MaxWait), the alias in its
 *           own format, and leaves the variant to the backlog instead of the job queue; without the backlog it waits
 *           and hands the work to the queue as before
 *  IAB-04 - drain(): only in a free slot (none: nothing done, the entries stay), oldest first, inside the slot, within
 *           its time; the slot given back
 *  IAB-05 - the cronjob part, the settings documented in image.ini, the swap script asking in page order, the
 *           signing keys kept in a file between PHP-FPM requests only
 *  IAB-06 - eZExtension::extensionPath() reads each extension root once: the exact name first, then one that differs
 *           in case, a directory made since found, a removed one not
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/imagealias/expImageAliasBacklogTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** The server with the delivery fixed and the queue recorded */
class expImageAliasBacklogTestServer extends expImageAliasServer
{
    /** @var array completions handed to the job queue (recorded, never queued) */
    public $queuedWork = array();

    public function deliveryMethod( $path )
    {
        return 'redirect';
    }

    protected function queueCompletion( $aliasName, array $formats, $target )
    {
        if ( $this->usesBacklog() )
            return parent::queueCompletion( $aliasName, $formats, $target );
        if ( $this->job || $this->miss === null )
            return;
        $this->queuedWork[] = array( $aliasName, $formats );
    }
}

class expImageAliasBacklogTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $base;

    /** @var string */
    private $dir;

    /** @var string|false */
    private $cwd = false;

    /** @var resource[] slot files held by the test (as another process would) */
    private $held = array();

    public static function setUpBeforeClass(): void
    {
        eZExecution::registerShutdownHandler();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 5 ) );
        $this->base = 'var/tmp/storage/images/exp_iab_' . getmypid() . '_' . mt_rand();
        $this->dir = $this->base . '/7-1-eng-GB';
        mkdir( $this->dir, 0700, true );
        mkdir( $this->base . '/slots', 0700, true );
        mkdir( $this->base . '/backlog', 0700, true );
        expImageAliasLock::setDirectory( $this->base . '/locks' );
        expImageAliasLog::setDirectory( $this->base . '/log' );
        expImageAliasThrottle::setDirectory( $this->base . '/slots' );
        expImageAliasThrottle::setLimit( 2 );
        expImageAliasThrottle::setApplies( true );
        expImageAliasThrottle::reset();
        expImageAliasBacklog::setDirectory( $this->base . '/backlog' );
    }

    protected function tearDown(): void
    {
        foreach ( $this->held as $handle )
        {
            @flock( $handle, LOCK_UN );
            @fclose( $handle );
        }
        $this->held = array();
        expImageAliasThrottle::reset();
        expImageAliasThrottle::setDirectory( null );
        expImageAliasThrottle::setLimit( null );
        expImageAliasThrottle::setApplies( null );
        expImageAliasBacklog::setDirectory( null );
        expImageAliasBacklog::setAnswerFirst( null );
        expImageAliasBacklog::setWorker( null );
        ezpINIHelper::restoreINISettings();
        expImageFormatConverter::setCapabilities( null );
        expImageAliasUrl::resetKeys();
        expImageAliasLock::setDirectory( null );
        expImageAliasLog::setDirectory( null );
        expImageAliasLock::setWaitLimit( null );
        foreach ( array( $this->dir, $this->base . '/locks', $this->base . '/log', $this->base . '/slots', $this->base . '/backlog' ) as $dir )
        {
            foreach ( array_merge( (array)glob( $dir . '/*' ), (array)glob( $dir . '/.*.tmp' ) ) as $file )
                @unlink( $file );
            @rmdir( $dir );
        }
        @rmdir( $this->base );
        if ( $this->cwd !== false )
            chdir( $this->cwd );
        parent::tearDown();
    }

    /** Holds slot $n through a file handle of its own, as another process does */
    private function holdSlot( $n )
    {
        $handle = fopen( $this->base . '/slots/slot-' . $n . '.lock', 'c' );
        $this->assertTrue( flock( $handle, LOCK_EX | LOCK_NB ), "slot $n is free" );
        $this->held[$n] = $handle;
    }

    private function freeSlot( $n )
    {
        flock( $this->held[$n], LOCK_UN );
        fclose( $this->held[$n] );
        unset( $this->held[$n] );
    }

    /** IAB-01 */
    public function testWhenItApplies()
    {
        $this->assertFalse( expImageAliasBacklog::canAnswerFirst(), 'the command line cannot answer first' );
        $this->assertFalse( expImageAliasBacklog::applies() );
        expImageAliasBacklog::setAnswerFirst( true );
        $this->assertTrue( expImageAliasSettings::backlogEnabled(), 'enabled by default' );
        $this->assertTrue( expImageAliasBacklog::applies() );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'Backlog', 'disabled' );
        $this->assertFalse( expImageAliasBacklog::applies(), 'Backlog=disabled' );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'Backlog', 'enabled' );
        expImageAliasThrottle::setApplies( false );
        $this->assertFalse( expImageAliasBacklog::applies(), 'render mode' );
        expImageAliasThrottle::setApplies( true );

        $this->assertSame( 30.0, (float)expImageAliasSettings::backlogTime() );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'BacklogTime', '5000' );
        $this->assertSame( 600.0, (float)expImageAliasSettings::backlogTime(), 'at most 600' );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'BacklogTime', '0' );
        $this->assertSame( 1.0, (float)expImageAliasSettings::backlogTime(), 'at least 1' );
    }

    /** IAB-02 */
    public function testEntries()
    {
        $this->assertSame( 0, expImageAliasBacklog::count() );
        $this->assertNull( expImageAliasBacklog::take() );
        $this->assertTrue( expImageAliasBacklog::add( 7, 1, 'eng-GB', 'small', array(), 'var/x/a_small.jpg', 'site' ) );
        usleep( 2000 );
        $this->assertTrue( expImageAliasBacklog::add( 8, 1, 'eng-GB', 'small', array( 'webp' ), 'var/x/b_small.jpg', 'site' ) );
        $this->assertFalse( expImageAliasBacklog::add( 7, 1, 'eng-GB', 'small', array(), 'var/x/a_small.jpg', 'site' ), 'waiting already' );
        usleep( 2000 );
        $this->assertTrue( expImageAliasBacklog::add( 7, 1, 'eng-GB', 'small', array(), 'var/x/a_small.jpg', 'admin' ), 'another siteaccess' );
        $this->assertSame( 3, expImageAliasBacklog::count() );

        $first = expImageAliasBacklog::take();
        $this->assertSame( array( 'site', 7, 1, 'eng-GB', 'small', array() ), array( $first['s'], $first['a'], $first['v'], $first['l'], $first['n'], $first['f'] ), 'oldest first' );
        $this->assertStringEndsWith( '.' . getmypid() . '.work', $first['file'] );
        $this->assertSame( 2, expImageAliasBacklog::count(), 'a taken entry is not waiting' );
        $this->assertTrue( expImageAliasBacklog::add( 7, 1, 'eng-GB', 'small', array(), 'var/x/a_small.jpg', 'site' ),
                           'asked for again while being made: waiting again (made at most once, the second finds it there)' );
        expImageAliasBacklog::finish( $first );
        $this->assertFileDoesNotExist( $first['file'] );

        $second = expImageAliasBacklog::take();
        $this->assertSame( array( 8, array( 'webp' ), 'var/x/b_small.jpg' ), array( $second['a'], $second['f'], $second['u'] ) );
        // a process that died with it: given back after STALE_WORK
        $this->assertSame( 0, expImageAliasBacklog::recoverStale(), 'not before STALE_WORK' );
        touch( $second['file'], time() - expImageAliasBacklog::STALE_WORK - 1 );
        $this->assertSame( 1, expImageAliasBacklog::recoverStale() );
        $this->assertFileDoesNotExist( $second['file'] );
        $this->assertSame( 3, expImageAliasBacklog::count() );
        // readable and writable for the other user (Velocity root, PHP-FPM the site user)
        foreach ( glob( $this->base . '/backlog/*.json' ) as $file )
            $this->assertSame( '0666', substr( sprintf( '%o', fileperms( $file ) ), -4 ) );
    }

    /** IAB-03 */
    public function testRequestAnswersAtOnceAndLeavesTheVariantToTheBacklog()
    {
        $converters = expImageFormatConverter::convertersFor( 'webp', 'auto' );
        if ( !$converters )
            $this->markTestSkipped( 'No converter on this machine writes WebP' );
        expImageFormatConverter::setCapabilities( array( $converters[0] => array( 'webp' ) ) );
        copy( dirname( __DIR__, 4 ) . '/tests/lib/ezimage/data/andernach.jpg', $this->dir . '/andernach.jpg' );
        copy( dirname( __DIR__, 4 ) . '/tests/lib/ezimage/data/andernach.jpg', $this->dir . '/andernach_small.jpg' );
        expImageAliasUrl::setKeys( str_repeat( 's', 64 ) );
        ezpINIHelper::setINISetting( 'image.ini', 'small', 'Formats', array( 'webp' ) );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'Negotiate', 'enabled' );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'MaxWait', '2' );
        $token = expImageAliasUrl::token( array( 'a' => 7, 'v' => 1, 'l' => 'eng-GB', 'n' => 'small', 'o' => $this->dir . '/andernach.jpg',
                                                 'u' => 'andernach_small.jpg', 'w' => 160, 'h' => 120, 'm' => 'image/jpeg' ) );
        $this->holdSlot( 0 );
        $this->holdSlot( 1 );

        // the backlog: no wait at all, the alias in its own format, the variant in the backlog
        expImageAliasBacklog::setAnswerFirst( true );
        $server = new expImageAliasBacklogTestServer();
        $server->setAccept( 'image/webp,*/*' );
        $t = microtime( true );
        $response = $server->handleParams( array( $token, 'andernach_small.jpg' ) );
        $this->assertLessThan( 0.5, microtime( true ) - $t, 'no wait for a slot (MaxWait is 2)' );
        $this->assertSame( 302, $response['status'] );
        $this->assertStringEndsWith( 'andernach_small.jpg', $response['headers']['Location'] );
        $this->assertSame( array(), $server->queuedWork, 'no job' );
        $this->assertSame( 1, expImageAliasBacklog::count() );
        $entry = expImageAliasBacklog::take();
        $this->assertSame( array( 7, 1, 'eng-GB', 'small', array( 'webp' ), $this->dir . '/andernach_small.jpg' ),
                           array( $entry['a'], $entry['v'], $entry['l'], $entry['n'], $entry['f'], $entry['u'] ) );
        expImageAliasBacklog::finish( $entry );
        $this->assertStringContainsString( 'event=defer result=timeout', (string)file_get_contents( expImageAliasLog::path() ) );

        // without it: the wait within MaxWait and the job, as before
        expImageAliasBacklog::setAnswerFirst( false );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'MaxWait', '0.4' );
        $server = new expImageAliasBacklogTestServer();
        $server->setAccept( 'image/webp,*/*' );
        $t = microtime( true );
        $server->handleParams( array( $token, 'andernach_small.jpg' ) );
        $this->assertGreaterThanOrEqual( 0.35, microtime( true ) - $t, 'waited within MaxWait' );
        $this->assertSame( array( array( 'small', array( 'webp' ) ) ), $server->queuedWork );
        $this->assertSame( 0, expImageAliasBacklog::count() );

        // a slot free, the backlog on: made at once, in that slot
        expImageAliasBacklog::setAnswerFirst( true );
        $this->freeSlot( 1 );
        $server = new expImageAliasBacklogTestServer();
        $server->setAccept( 'image/webp,*/*' );
        $response = $server->handleParams( array( $token, 'andernach_small.jpg' ) );
        $this->assertStringEndsWith( 'andernach_small.jpg.webp', $response['headers']['Location'] );
        $this->assertSame( 0, expImageAliasBacklog::count() );

        // an alias not made yet, every slot taken: the pending placeholder at once, without a database query (this
        // test has no database: a query would find no attribute and answer 404 "gone"), the alias in the backlog
        $this->holdSlot( 1 );
        $token = expImageAliasUrl::token( array( 'a' => 7, 'v' => 1, 'l' => 'eng-GB', 'n' => 'medium', 'o' => $this->dir . '/andernach.jpg',
                                                 'u' => 'andernach_medium.jpg', 'w' => 200, 'h' => 150, 'm' => 'image/jpeg' ) );
        $server = new expImageAliasBacklogTestServer();
        $response = $server->handleParams( array( $token, 'andernach_medium.jpg' ) );
        $this->assertSame( 200, $response['status'] );
        $this->assertSame( '1', $response['headers']['X-Image-Placeholder'] );
        $entry = expImageAliasBacklog::take();
        $this->assertSame( array( 7, 'medium', $this->dir . '/andernach_medium.jpg' ), array( $entry['a'], $entry['n'], $entry['u'] ) );
        expImageAliasBacklog::finish( $entry );
    }

    /** IAB-04 */
    public function testDrain()
    {
        foreach ( array( 'a', 'b', 'c' ) as $i => $name )
        {
            expImageAliasBacklog::add( 10 + $i, 1, 'eng-GB', 'small', array(), "var/x/{$name}_small.jpg", 'site' );
            usleep( 2000 );
        }
        $done = array();
        $busy = array();
        expImageAliasBacklog::setWorker( function ( $entry ) use ( &$done, &$busy ) {
            $done[] = $entry['a'];
            $busy[] = expImageAliasThrottle::heldSlot();
        } );

        // every slot taken: nothing, at once, the entries stay
        $this->holdSlot( 0 );
        $this->holdSlot( 1 );
        $t = microtime( true );
        $this->assertSame( 0, expImageAliasBacklog::drain( 5 ) );
        $this->assertLessThan( 0.5, microtime( true ) - $t );
        $this->assertSame( 3, expImageAliasBacklog::count() );

        // a slot free: all of them, oldest first, inside that slot, which is given back
        $this->freeSlot( 1 );
        $this->assertSame( 3, expImageAliasBacklog::drain( 5 ) );
        $this->assertSame( array( 10, 11, 12 ), $done );
        $this->assertSame( array( 1, 1, 1 ), $busy, 'made in the free slot' );
        $this->assertNull( expImageAliasThrottle::heldSlot(), 'given back' );
        $this->assertSame( 0, expImageAliasBacklog::count() );
        $this->assertSame( array(), glob( $this->base . '/backlog/*.work' ) );

        // its time: stops between two entries
        $done = array();
        foreach ( array( 'd', 'e' ) as $i => $name )
            expImageAliasBacklog::add( 20 + $i, 1, 'eng-GB', 'small', array(), "var/x/{$name}_small.jpg", 'site' );
        expImageAliasBacklog::setWorker( function ( $entry ) use ( &$done ) {
            $done[] = $entry['a'];
            usleep( 300000 );
        } );
        $this->assertSame( 1, expImageAliasBacklog::drain( 0.2 ) );
        $this->assertSame( 1, expImageAliasBacklog::count() );
    }

    /** IAB-05 */
    public function testCronjobSettingsAndSwapScript()
    {
        $root = dirname( __DIR__, 5 );
        $ini = (string)file_get_contents( $root . '/settings/image.ini' );
        $this->assertMatchesRegularExpression( '/^Backlog=enabled$/m', $ini );
        $this->assertMatchesRegularExpression( '/^BacklogTime=30$/m', $ini );
        $cron = (string)file_get_contents( $root . '/settings/cronjob.ini' );
        $frequent = substr( $cron, strpos( $cron, '[CronjobPart-frequent]' ) );
        $frequent = substr( $frequent, 0, strpos( $frequent, "\n[", 1 ) );
        $this->assertStringContainsString( 'Scripts[]=imagealiasbacklog.php', $frequent );
        $this->assertFileExists( $root . '/cronjobs/imagealiasbacklog.php' );
        $this->assertTrue( class_exists( 'Exponential\\Cronjob\\Kernel\\Imagealiasbacklog' ) );

        $js = (string)file_get_contents( $root . '/design/standard/javascript/exp_image_swap.js' );
        $this->assertStringContainsString( 'var AT_ONCE = 4;', $js );
        $this->assertStringContainsString( 'var GIVE_UP = 60000;', $js );
        $this->assertStringContainsString( "return all ? next() : false;", $js, 'a round stops at the first image still pending' );

        // the signing keys are kept in a file between PHP-FPM requests only, never on the command line (scripts,
        // tests, Velocity's persistent workers keep them in memory)
        $this->assertNull( expImageAliasUrl::keyFile() );
        $url = (string)file_get_contents( $root . '/kernel/classes/imagealias/expimagealiasurl.php' );
        $this->assertStringContainsString( 'umask( eZFile::creationUmask( 077 ) )', $url, 'the key file is readable by the site user only' );
        $this->assertStringContainsString( 'self::forgetKeyFile();', $url, 'forgotten when the key changes' );
    }

    /** IAB-06 */
    public function testExtensionPathReadsEachRootOnce()
    {
        $root = $this->base . '/ext';
        mkdir( $root . '/alpha', 0700, true );
        mkdir( $root . '/MixedCase', 0700, true );
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', 'ExtensionDirectory', $root );
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', 'AdditionalExtensionDirectories', array() );
        eZExtension::clearCaches();
        try
        {
            $this->assertSame( $root . '/alpha', eZExtension::extensionPath( 'alpha' ) );
            $this->assertSame( $root . '/MixedCase', eZExtension::extensionPath( 'mixedcase' ), 'the name as it is on disk' );
            $this->assertFalse( eZExtension::extensionPath( 'beta' ) );
            mkdir( $root . '/beta' );
            $this->assertSame( $root . '/beta', eZExtension::extensionPath( 'beta' ), 'made since the root was read' );
            rmdir( $root . '/beta' );
            eZExtension::clearCaches();
            $this->assertSame( $root . '/alpha', eZExtension::extensionPath( 'alpha' ) );
            $this->assertFalse( eZExtension::extensionPath( 'beta' ), 'removed' );
        }
        finally
        {
            eZExtension::clearCaches();
            foreach ( array( 'alpha', 'MixedCase', 'beta' ) as $dir )
                @rmdir( $root . '/' . $dir );
            @rmdir( $root );
        }
    }
}
