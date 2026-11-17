<?php
/**
 * The drainer processes of the image alias backlog ([AliasSettings] BacklogDrainer, expImageAliasBacklog), without a
 * database and without starting a process (the spawner is replaced):
 *
 *  IAD-01 - the setting: process by default, request; drainers only where proc_open() may run
 *  IAD-02 - startDrainers(): nothing for an empty backlog; one drainer per DRAINER_ENTRIES waiting entries, at most
 *           MaxParallel; a drainer just started counts as running (its marker) and is not started twice; a slot
 *           whose lock another process holds counts as running; null when none could be started
 *  IAD-03 - runDrainer(): only one process per slot (the lock); makes the backlog oldest first in a conversion slot,
 *           removes its start marker, waits a moment for new entries, then ends
 *  IAD-04 - the entry script, the command, the settings documented in image.ini
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/imagealias/expImageAliasDrainerTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expImageAliasDrainerTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $base;

    /** @var string|false */
    private $cwd = false;

    /** @var int[] slots the spawner was asked to start */
    private $spawned = array();

    /** @var resource[] lock files held by the test (as another process would) */
    private $held = array();

    protected function setUp(): void
    {
        parent::setUp();
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 5 ) );
        $this->base = 'var/tmp/exp_iad_' . getmypid() . '_' . mt_rand();
        mkdir( $this->base . '/backlog', 0700, true );
        mkdir( $this->base . '/slots', 0700, true );
        expImageAliasBacklog::setDirectory( $this->base . '/backlog' );
        expImageAliasThrottle::setDirectory( $this->base . '/slots' );
        expImageAliasLog::setDirectory( $this->base . '/log' );
        expImageAliasThrottle::setLimit( 6 );
        expImageAliasThrottle::setApplies( true );
        expImageAliasThrottle::reset();
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'MaxParallel', '6' );
        $this->spawned = array();
        $spawned =& $this->spawned;
        expImageAliasBacklog::setSpawner( function ( $slot ) use ( &$spawned ) {
            $spawned[] = $slot;
            return true;
        } );
    }

    protected function tearDown(): void
    {
        foreach ( $this->held as $handle )
        {
            @flock( $handle, LOCK_UN );
            @fclose( $handle );
        }
        $this->held = array();
        expImageAliasBacklog::setSpawner( null );
        expImageAliasBacklog::setWorker( null );
        expImageAliasBacklog::setDirectory( null );
        expImageAliasLog::setDirectory( null );
        expImageAliasThrottle::reset();
        expImageAliasThrottle::setDirectory( null );
        expImageAliasThrottle::setLimit( null );
        expImageAliasThrottle::setApplies( null );
        ezpINIHelper::restoreINISettings();
        $this->removeTree( $this->base );
        if ( $this->cwd !== false )
            chdir( $this->cwd );
        parent::tearDown();
    }

    private function removeTree( $dir )
    {
        if ( !is_dir( $dir ) )
        {
            @unlink( $dir );
            return;
        }
        foreach ( array_merge( (array)glob( $dir . '/*' ), (array)glob( $dir . '/.[!.]*' ) ) as $file )
            $this->removeTree( $file );
        @rmdir( $dir );
    }

    private function addEntries( $n )
    {
        for ( $i = 1; $i <= $n; $i++ )
            $this->assertTrue( expImageAliasBacklog::add( 100 + $i, 1, 'eng-GB', 'i160', array(), null, 'site' ) );
    }

    /** IAD-01 */
    public function testSetting()
    {
        $this->assertSame( 'process', expImageAliasSettings::backlogDrainer(), 'process by default' );
        $this->assertTrue( expImageAliasBacklog::usesDrainers() );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'BacklogDrainer', 'request' );
        $this->assertSame( 'request', expImageAliasSettings::backlogDrainer() );
        $this->assertFalse( expImageAliasBacklog::usesDrainers() );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'BacklogDrainer', 'anything' );
        $this->assertSame( 'process', expImageAliasSettings::backlogDrainer(), 'anything else is process' );
    }

    /** IAD-02 */
    public function testStartDrainers()
    {
        $this->assertSame( 0, expImageAliasBacklog::startDrainers(), 'nothing waits' );
        $this->assertSame( array(), $this->spawned );

        $this->addEntries( 10 );
        $this->assertSame( 3, expImageAliasBacklog::startDrainers(), 'one drainer per 4 waiting entries' );
        $this->assertSame( array( 0, 1, 2 ), $this->spawned );
        foreach ( array( 0, 1, 2 ) as $slot )
            $this->assertTrue( expImageAliasBacklog::drainerRunning( $slot ), 'a drainer just started counts as running' );
        $this->assertFalse( expImageAliasBacklog::drainerRunning( 3 ) );
        $this->assertSame( 0, expImageAliasBacklog::startDrainers(), 'not started twice' );

        // Many waiting: at most MaxParallel; a slot another process holds counts as running
        for ( $i = 11; $i <= 60; $i++ )
            expImageAliasBacklog::add( 100 + $i, 1, 'eng-GB', 'i160', array(), null, 'site' );
        $handle = fopen( expImageAliasBacklog::drainerDirectory() . '/drainer-4.lock', 'c' );
        $this->assertTrue( flock( $handle, LOCK_EX | LOCK_NB ) );
        $this->held[] = $handle;
        $this->assertTrue( expImageAliasBacklog::drainerRunning( 4 ), 'held by another process' );
        $this->assertSame( 2, expImageAliasBacklog::startDrainers(), 'the two free slots of six' );
        $this->assertSame( array( 0, 1, 2, 3, 5 ), $this->spawned );

        // None can be started (no PHP command line, say) and none runs: null, and no marker left
        foreach ( (array)glob( expImageAliasBacklog::drainerDirectory() . '/*.start' ) as $marker )
            unlink( $marker );
        flock( $handle, LOCK_UN );
        expImageAliasBacklog::setSpawner( function ( $slot ) { return false; } );
        $this->assertNull( expImageAliasBacklog::startDrainers() );
        $this->assertSame( array(), (array)glob( expImageAliasBacklog::drainerDirectory() . '/*.start' ) );
        $log = (string)@file_get_contents( expImageAliasLog::path() );
        $this->assertSame( 1, substr_count( $log, 'event=queue result=no-drainer' ), 'said once in imagealias.log' );
    }

    /** IAD-03 */
    public function testRunDrainer()
    {
        $this->addEntries( 3 );
        $made = array();
        expImageAliasBacklog::setWorker( function ( array $entry ) use ( &$made ) {
            $made[] = $entry['a'];
        } );
        $directory = expImageAliasBacklog::drainerDirectory();
        mkdir( $directory, 0700, true );
        touch( $directory . '/drainer-1.start' );

        // Another process holds slot 1: nothing done
        $handle = fopen( $directory . '/drainer-1.lock', 'c' );
        $this->assertTrue( flock( $handle, LOCK_EX | LOCK_NB ) );
        $this->assertFalse( expImageAliasBacklog::runDrainer( 1 ) );
        $this->assertSame( array(), $made );
        flock( $handle, LOCK_UN );
        fclose( $handle );

        $started = microtime( true );
        $this->assertSame( 3, expImageAliasBacklog::runDrainer( 1 ) );
        $this->assertSame( array( 101, 102, 103 ), $made, 'oldest first' );
        $this->assertSame( 0, expImageAliasBacklog::count() );
        $this->assertFileDoesNotExist( $directory . '/drainer-1.start', 'its start marker removed' );
        $this->assertGreaterThanOrEqual( expImageAliasBacklog::DRAINER_IDLE - 0.3, microtime( true ) - $started, 'waited for new entries' );
        $this->assertFalse( expImageAliasBacklog::drainerRunning( 1 ), 'the lock is given back' );
    }

    /** IAD-04 */
    public function testEntryScriptCommandAndSettings()
    {
        $root = dirname( __DIR__, 5 );
        $entry = (string)file_get_contents( $root . '/bin/php/expimagealiasdrain.php' );
        $this->assertStringStartsWith( "#!/usr/bin/env php\n", $entry );
        $this->assertStringContainsString( 'Expimagealiasdrain::main( __FILE__ )', $entry );
        $this->assertTrue( is_executable( $root . '/bin/php/expimagealiasdrain.php' ) );
        $command = (string)file_get_contents( $root . '/kernel/private/classes/commands/expimagealiasdrain.php' );
        $this->assertStringContainsString( 'dropPrivileges', $command, 'started by root, it becomes the owner of var/' );
        $this->assertStringContainsString( 'expImageAliasBacklog::runDrainer( $slot )', $command );
        $ini = (string)file_get_contents( $root . '/settings/image.ini' );
        $this->assertStringContainsString( "\nBacklogDrainer=process\n", $ini );
        $backlog = (string)file_get_contents( $root . '/kernel/classes/imagealias/expimagealiasbacklog.php' );
        $this->assertStringContainsString( 'if ( !self::usesDrainers() || self::startDrainers() === null )', $backlog,
                                           'after the answer: drainers, else the request itself' );
    }
}
