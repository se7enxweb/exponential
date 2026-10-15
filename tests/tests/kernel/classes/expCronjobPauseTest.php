<?php
/**
 * Pausing cronjob parts (expCronjobPause): a paused part, or "all", is left out by runcronjobs.php until it is
 * resumed; a script run alone is left out while a part that lists it is paused; the state files are those of
 * cjw_admincockpit; every pause and resume is recorded with its user; part names cannot leave the directory.
 *
 * No database: the paths point into a temporary directory and the user is a stand-in.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

/** Stands in for an eZUser: the history records its object id and login. */
class expCronjobPauseTestUser
{
    public function attribute( $name )
    {
        return $name === 'login' ? 'tester' : 42;
    }
}

class expCronjobPauseTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $user;

    protected function setUp(): void
    {
        if ( !class_exists( 'expCronjobPause' ) || !class_exists( 'eZINI' ) || !class_exists( 'eZFile' ) )
            $this->markTestSkipped( 'expCronjobPause, eZINI and eZFile are not autoloadable here' );
        $this->dir = sys_get_temp_dir() . '/exp_cronjob_pause_' . bin2hex( random_bytes( 6 ) );
        mkdir( $this->dir . '/control', 0700, true );
        expCronjobPause::setPaths( $this->dir . '/control', $this->dir . '/pause.log' );
        $this->user = new expCronjobPauseTestUser();
    }

    protected function tearDown(): void
    {
        expCronjobPause::setPaths( null, null );
        if ( $this->dir && is_dir( $this->dir ) )
        {
            foreach ( (array)glob( $this->dir . '/control/*' ) as $file )
                unlink( $file );
            @unlink( $this->dir . '/pause.log' );
            @rmdir( $this->dir . '/control' );
            @rmdir( $this->dir );
        }
    }

    public function testNothingIsPausedAtFirst()
    {
        $this->assertFalse( expCronjobPause::notice( 'frequent' ) );
        $this->assertFalse( expCronjobPause::notice( '', array( 'notification.php' ), true ) );
        $this->assertSame( array(), expCronjobPause::pausedParts() );
        $this->assertSame( array(), expCronjobPause::history() );
    }

    public function testPauseAndResumeAPart()
    {
        $this->assertTrue( expCronjobPause::pause( 'frequent', $this->user ) );
        $this->assertTrue( expCronjobPause::isPaused( 'frequent' ) );
        $this->assertStringContainsString( "'frequent' is paused", (string)expCronjobPause::notice( 'frequent' ) );
        $this->assertFalse( expCronjobPause::notice( 'infrequent' ) );

        $state = expCronjobPause::state( 'frequent' );
        $this->assertTrue( $state['paused'] );
        $this->assertGreaterThan( 0, $state['since'] );
        $this->assertSame( 'tester', $state['by']['login'] );
        $this->assertSame( 42, $state['by']['user_id'] );

        $this->assertTrue( expCronjobPause::resume( 'frequent', $this->user ) );
        $this->assertFalse( expCronjobPause::isPaused( 'frequent' ) );
        $this->assertFalse( expCronjobPause::notice( 'frequent' ) );
        $history = expCronjobPause::history();
        $this->assertSame( array( 'resume', 'pause' ), array( $history[0]['action'], $history[1]['action'] ) );
        $this->assertSame( 'frequent', $history[0]['part'] );
    }

    public function testPausingAllLeavesEverythingOut()
    {
        expCronjobPause::pause( expCronjobPause::ALL, $this->user );
        $this->assertStringContainsString( 'All cronjob parts are paused', (string)expCronjobPause::notice( 'frequent' ) );
        $this->assertStringContainsString( 'All cronjob parts are paused', (string)expCronjobPause::notice( '' ) );
        $this->assertStringContainsString( 'All cronjob parts are paused', (string)expCronjobPause::notice( '', array( 'anything.php' ), true ) );
        $this->assertSame( array( 'all' ), expCronjobPause::pausedParts() );
    }

    public function testAScriptRunAloneIsLeftOutWhileItsPartIsPaused()
    {
        // A part of its own, so the test does not depend on what the installation's cronjob.ini lists.
        $ini = eZINI::instance( 'cronjob.ini' );
        $ini->setVariable( 'CronjobPart-exppausetest', 'Scripts', array( 'exppausetest.php' ) );
        try
        {
            expCronjobPause::pause( 'exppausetest', $this->user );
            $this->assertStringContainsString( "'exppausetest' is paused", (string)expCronjobPause::notice( '', array( 'exppausetest.php' ), true ) );
            // The script name as runcronjobs.php may pass it, with a directory.
            $this->assertStringContainsString( "'exppausetest' is paused", (string)expCronjobPause::notice( '', array( 'cronjobs/exppausetest.php' ), true ) );
            $this->assertFalse( expCronjobPause::notice( '', array( 'other.php' ), true ) );
            // The scripts of [CronjobSettings] are the part without a name.
            $this->assertFalse( expCronjobPause::notice( '' ) );
            expCronjobPause::pause( '', $this->user );
            $this->assertStringContainsString( "'global' is paused", (string)expCronjobPause::notice( '' ) );
        }
        finally
        {
            $ini->removeGroup( 'CronjobPart-exppausetest' );
        }
    }

    public function testWhoPausedIsForgottenWhenTheFileChangedSince()
    {
        expCronjobPause::pause( 'frequent', $this->user );
        $this->assertSame( 'tester', expCronjobPause::state( 'frequent' )['by']['login'] );
        // Paused again later by other means: the history's pause is older than the file.
        $file = $this->dir . '/control/cronpart_deactivated_frequent.txt';
        touch( $file, time() + 60 );
        clearstatcache();
        $state = expCronjobPause::state( 'frequent' );
        $this->assertTrue( $state['paused'] );
        $this->assertFalse( $state['by'] );
    }

    public function testFilesOfCjwAdminCockpitAreHonoured()
    {
        file_put_contents( $this->dir . '/control/cronpart_deactivated_frequent.txt', "1\n" );
        file_put_contents( $this->dir . '/control/cronpart_deactivated_infrequent.txt', '0 - set to -> 1 <- if you want do disable running the cronpart' );
        $this->assertTrue( expCronjobPause::isPaused( 'frequent' ) );
        $this->assertFalse( expCronjobPause::isPaused( 'infrequent' ) );
        $this->assertSame( array( 'frequent' ), expCronjobPause::pausedParts() );
        // Paused by someone else: since when is known, by whom is not.
        $this->assertFalse( expCronjobPause::state( 'frequent' )['by'] );
    }

    public function testPartNamesCannotLeaveTheDirectory()
    {
        foreach ( array( '../x', 'a/b', '..', '.hidden', "a\nb", 'a b' ) as $name )
        {
            $this->assertFalse( expCronjobPause::isValidPart( $name ), $name );
            $this->assertFalse( expCronjobPause::pause( $name, $this->user ), $name );
        }
        foreach ( array( '', 'all', 'frequent', 'cache_cleanup', 'my-part.2' ) as $name )
            $this->assertTrue( expCronjobPause::isValidPart( $name ), $name );
        $this->assertSame( array(), glob( $this->dir . '/*.txt' ) );
        $this->assertSame( array(), expCronjobPause::history() );
    }

    public function testBrokenHistoryLinesAreSkipped()
    {
        file_put_contents( $this->dir . '/pause.log', "not json\n"
            . json_encode( array( 'time' => 1, 'part' => '../x', 'action' => 'pause' ) ) . "\n"
            . json_encode( array( 'time' => 1, 'part' => array( 'x' ), 'action' => 'pause', 'login' => array() ) ) . "\n" );
        expCronjobPause::pause( 'frequent', $this->user );
        $history = expCronjobPause::history();
        $this->assertCount( 1, $history );
        $this->assertSame( 'frequent', $history[0]['part'] );
    }

    public function testThePauseCanBeSwitchedOff()
    {
        expCronjobPause::pause( expCronjobPause::ALL, $this->user );
        $ini = eZINI::instance( 'cronjob.ini' );
        $had = $ini->hasVariable( 'PauseSettings', 'Pause' ) ? $ini->variable( 'PauseSettings', 'Pause' ) : null;
        $ini->setVariable( 'PauseSettings', 'Pause', 'disabled' );
        try
        {
            $this->assertFalse( expCronjobPause::enabled() );
            $this->assertFalse( expCronjobPause::notice( 'frequent' ) );
        }
        finally
        {
            if ( $had === null )
                $ini->removeSetting( 'PauseSettings', 'Pause' );
            else
                $ini->setVariable( 'PauseSettings', 'Pause', $had );
        }
    }

    public function testRuncronjobsAsksThePauseBeforeTheFilter()
    {
        $source = file_get_contents( __DIR__ . '/../../../../kernel/private/classes/commands/runcronjobs.php' );
        $pause = strpos( $source, 'expCronjobPause::notice(' );
        $filter = strpos( $source, 'static::partMayRun(' );
        $this->assertNotFalse( $pause );
        $this->assertNotFalse( $filter );
        $this->assertLessThan( $filter, $pause );
    }

    /**
     * A part paused while it runs: runcronjobs.php asks again before each further script, leaves the rest out and
     * says so in the run's output and in cronjob_skipped.log.
     */
    public function testRuncronjobsAsksThePauseAgainBeforeEachFurtherScript()
    {
        $source = file_get_contents( __DIR__ . '/../../../../kernel/private/classes/commands/runcronjobs.php' );
        $loop = strpos( $source, 'foreach ( $scripts as $cronScript )' );
        $this->assertNotFalse( $loop );
        $body = substr( $source, $loop );
        $again = strpos( $body, 'expCronjobPause::notice( $partName )' );
        $run = strpos( $body, 'eZRunCronjobs::runScript(' );
        $this->assertNotFalse( $again, 'the loop asks the pause again' );
        $this->assertLessThan( $run, $again, 'before the next script runs' );
        $this->assertStringContainsString( 'break;', substr( $body, $again, $run - $again ) );
        $this->assertSame( 2, substr_count( $source, 'expCronjobPause::logSkip(' ), 'both skips are logged' );
    }

    /**
     * The state file is replaced, not opened: a file the writer may not open (another user's, as Velocity's root
     * workers and the site user's web server write the same directory) does not stop a pause or a resume.
     */
    public function testAStateFileThatCannotBeOpenedIsReplaced()
    {
        $this->assertTrue( expCronjobPause::pause( 'frequent', $this->user ) );
        $file = $this->dir . '/control/cronpart_deactivated_frequent.txt';
        chmod( $file, 0444 );
        $this->assertTrue( expCronjobPause::resume( 'frequent', $this->user ) );
        $this->assertFalse( expCronjobPause::isPaused( 'frequent' ) );
        chmod( $file, 0444 );
        $this->assertTrue( expCronjobPause::pause( 'frequent', $this->user ) );
        $this->assertTrue( expCronjobPause::isPaused( 'frequent' ) );
        $this->assertSame( array(), glob( $this->dir . '/control/ezfile-tmp.*' ), 'no temporary file is left behind' );
    }

    /**
     * The state file and the history get the installation's file mode, so the other user can replace and append.
     */
    public function testTheStateFileAndTheHistoryGetTheStorageFileMode()
    {
        if ( !function_exists( 'posix_geteuid' ) )
            $this->markTestSkipped( 'posix is not available' );
        $mode = eZFile::fileMode( octdec( (string)eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' ) ) );
        $old = umask( 0077 );
        try
        {
            $this->assertTrue( expCronjobPause::pause( 'frequent', $this->user ) );
        }
        finally
        {
            umask( $old );
        }
        clearstatcache();
        $this->assertSame( sprintf( '%o', $mode ), sprintf( '%o', fileperms( $this->dir . '/control/cronpart_deactivated_frequent.txt' ) & 0777 ) );
        $this->assertSame( sprintf( '%o', $mode ), sprintf( '%o', fileperms( $this->dir . '/pause.log' ) & 0777 ) );
    }
}
