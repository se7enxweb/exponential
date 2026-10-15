<?php
/**
 * Pausing cronjob parts (expCronjobPause): a paused part, or "all", is left out by runcronjobs.php until it is
 * resumed; a script run alone is left out while a part that lists it is paused; state files written by others are
 * read; every pause and resume is recorded with its user and an optional note, which the console and the notice of
 * runcronjobs.php show with since when and by whom; part names cannot leave the directory.
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
        // The history keeps the user id; the login shown is looked up by it (the stand-in user is 42, "tester").
        expCronjobPause::setUserResolver( function ( $id ) { return $id === 42 ? 'tester' : null; } );
    }

    protected function tearDown(): void
    {
        expCronjobPause::setPaths( null, null );
        expCronjobPause::setUserResolver( null );
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

    public function testStateFilesWrittenByOthersAreHonoured()
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

    /**
     * A note given with a pause is kept in the history, comes back with the state, and the notice runcronjobs.php
     * prints says since when, by whom and why.
     */
    public function testANoteIsKeptAndTheNoticeSaysSinceWhenByWhomAndWhy()
    {
        $this->assertTrue( expCronjobPause::pause( 'frequent', $this->user, "Index rebuild\nuntil noon" ) );
        $state = expCronjobPause::state( 'frequent' );
        $this->assertSame( 'Index rebuild until noon', $state['by']['note'] );
        $this->assertSame( 'Index rebuild until noon', expCronjobPause::history()[0]['note'] );

        $notice = (string)expCronjobPause::notice( 'frequent' );
        $this->assertStringStartsWith( "Notice: The cronjob part 'frequent' is paused (Setup > Cronjobs). Paused since ", $notice );
        $this->assertStringContainsString( date( 'Y-m-d', $state['since'] ), $notice );
        $this->assertStringEndsWith( ' by tester: Index rebuild until noon.', $notice );

        // A resume keeps its own note; the part runs, so the notice is gone.
        $this->assertTrue( expCronjobPause::resume( 'frequent', $this->user, 'Done' ) );
        $this->assertSame( array( 'Done', 'Index rebuild until noon' ), array_column( expCronjobPause::history(), 'note' ) );
        $this->assertFalse( expCronjobPause::notice( 'frequent' ) );

        // Every part paused, without a note: since when and by whom only.
        expCronjobPause::pause( expCronjobPause::ALL, $this->user );
        $this->assertStringEndsWith( ' by tester.', (string)expCronjobPause::notice( 'infrequent' ) );
    }

    /**
     * A pause made by other means (a shell, an extension) has no history line: the notice says since when only.
     */
    public function testTheNoticeOfAPauseMadeElsewhereSaysSinceWhenOnly()
    {
        file_put_contents( $this->dir . '/control/cronpart_deactivated_frequent.txt', '1' );
        $notice = (string)expCronjobPause::notice( 'frequent' );
        $this->assertStringContainsString( ' Paused since ', $notice );
        $this->assertStringNotContainsString( ' by ', $notice );
        $this->assertSame( '', expCronjobPause::describe( array( 'paused' => false, 'since' => 0, 'by' => false ) ) );
    }

    public function testANoteIsOneShortLine()
    {
        $this->assertSame( '', expCronjobPause::cleanNote( null ) );
        $this->assertSame( '', expCronjobPause::cleanNote( array( 'x' ) ) );
        $this->assertSame( '', expCronjobPause::cleanNote( "  \n\t " ) );
        $this->assertSame( 'a b c', expCronjobPause::cleanNote( "a\r\nb\x00c" ) );
        $this->assertSame( "a b", expCronjobPause::cleanNote( "a\u{2028}b" ) );
        // C1 controls (NEL) and bidirectional overrides would break or reorder the line.
        $this->assertSame( 'a b c', expCronjobPause::cleanNote( "a\u{0085}b\u{202E}c" ) );
        $this->assertSame( 'a b', expCronjobPause::cleanNote( "a\u{2066}b" ) );
        $long = str_repeat( 'ä', expCronjobPause::NOTE_LENGTH + 50 );
        $this->assertSame( expCronjobPause::NOTE_LENGTH, mb_strlen( expCronjobPause::cleanNote( $long ), 'UTF-8' ) );
        // Bytes that are not UTF-8 do not make the history line unwritable (json_encode() refuses them).
        $this->assertTrue( expCronjobPause::pause( 'frequent', $this->user, "bad \xC3\x28 byte" ) );
        $this->assertTrue( (bool)preg_match( '//u', expCronjobPause::history()[0]['note'] ) );
        $this->assertSame( 'tester', expCronjobPause::state( 'frequent' )['by']['login'] );
    }

    /**
     * History lines written before notes existed, and notes changed in the file, are read as one short line.
     */
    public function testHistoryLinesWithoutANoteHaveAnEmptyOne()
    {
        file_put_contents( $this->dir . '/pause.log',
            json_encode( array( 'time' => time() - 60, 'part' => 'frequent', 'action' => 'pause', 'user_id' => 14, 'login' => 'admin' ) ) . "\n" .
            json_encode( array( 'time' => time(), 'part' => 'frequent', 'action' => 'resume', 'user_id' => 14, 'login' => 'admin', 'note' => array( 'x' ) ) ) . "\n" );
        expCronjobPause::setPaths( $this->dir . '/control', $this->dir . '/pause.log' );
        $this->assertSame( array( '', '' ), array_column( expCronjobPause::history(), 'note' ) );
    }

    /**
     * The figure of the Setup overview's Cronjobs tile: the parts paused one by one, and whether every part is.
     */
    public function testTheSetupOverviewCountsThePausedParts()
    {
        $collection = new eZSetupFunctionCollection();
        $this->assertSame( array( 'enabled' => true, 'all' => false, 'count' => 0 ), $collection->fetchCronjobPauses()['result'] );
        expCronjobPause::pause( 'frequent', $this->user );
        expCronjobPause::pause( '', $this->user );
        $this->assertSame( array( 'enabled' => true, 'all' => false, 'count' => 2 ), $collection->fetchCronjobPauses()['result'] );
        expCronjobPause::pause( expCronjobPause::ALL, $this->user );
        $this->assertSame( array( 'enabled' => true, 'all' => true, 'count' => 2 ), $collection->fetchCronjobPauses()['result'] );
    }

    /**
     * The console asks for the note in one field that every Pause and Resume button submits, in both designs, and
     * shows it on the part, in the notice of a pause of every part and under Pauses; the overview counts the paused
     * parts.
     */
    public function testTheConsoleAsksForTheNoteAndShowsIt()
    {
        $root = __DIR__ . '/../../../../';
        $module = file_get_contents( $root . 'kernel/setup/module.php' );
        $this->assertSame( 2, substr_count( $module, "'CronjobPauseNote' => 'CronjobPauseNote'" ), 'pause and resume both take the note' );
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $tpl = file_get_contents( $root . "design/$design/templates/setup/cronjobs.tpl" );
            $this->assertStringContainsString( '<textarea id="cronjob-pause-note" name="CronjobPauseNote"', $tpl, "$design: the note is a textarea, so Enter does not submit the form" );
            $this->assertStringContainsString( 'maxlength="{$cronjob_pause_note_length}"', $tpl );
            $this->assertStringContainsString( '$cronjob_pause_all.by.note|wash', $tpl );
            $this->assertSame( 2, substr_count( $tpl, '$cronjob_part.pause.by.note|wash' ), "$design: the compact row and the card" );
            $this->assertStringContainsString( '{$cronjob_pause.note|wash}', $tpl );
            $this->assertStringContainsString( '{$cronjob_summary.paused}', $tpl );
        }
    }

    /**
     * Who paused is kept as the user id only; the login shown is the user's current one, so a renamed user shows under
     * the new name. An older line's login is used only when the user cannot be found, and "#<id>" when there is none.
     */
    public function testWhoPausedIsKeptByIdAndShownByTheCurrentLogin()
    {
        $this->assertTrue( expCronjobPause::pause( 'frequent', $this->user, 'release' ) );
        $line = json_decode( trim( (string)file_get_contents( $this->dir . '/pause.log' ) ), true );
        $this->assertSame( 42, $line['user_id'] );
        $this->assertArrayNotHasKey( 'login', $line, 'no copy of the name' );
        $this->assertSame( 'tester', expCronjobPause::state( 'frequent' )['by']['login'] );
        $this->assertStringContainsString( ' by tester: release.', expCronjobPause::notice( 'frequent' ) );

        expCronjobPause::setUserResolver( function ( $id ) { return $id === 42 ? 'renamed' : null; } );
        $this->assertSame( 'renamed', expCronjobPause::state( 'frequent' )['by']['login'] );

        file_put_contents( $this->dir . '/pause.log',
            json_encode( array( 'time' => time(), 'part' => 'infrequent', 'action' => 'resume', 'user_id' => 14, 'login' => 'old' ) ) . "\n" .
            json_encode( array( 'time' => time(), 'part' => 'infrequent', 'action' => 'resume', 'user_id' => 15 ) ) . "\n", FILE_APPEND );
        expCronjobPause::setUserResolver( function ( $id ) { return null; } );
        $this->assertSame( array( '#15', 'old', '#42' ), array_column( expCronjobPause::history(), 'login' ) );
    }

    /**
     * A persistent worker (Velocity) keeps the class's statics: the history read in one request is read again in the
     * next, so a pause made meanwhile by another process shows.
     */
    public function testTheHistoryIsReadAgainInTheNextRequest()
    {
        $had = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? $_SERVER['REQUEST_TIME_FLOAT'] : null;
        try
        {
            $_SERVER['REQUEST_TIME_FLOAT'] = 1000.5;
            $this->assertSame( array(), expCronjobPause::history() );
            file_put_contents( $this->dir . '/pause.log',
                json_encode( array( 'time' => time(), 'part' => 'frequent', 'action' => 'pause', 'user_id' => 42 ) ) . "\n" );
            $this->assertSame( array(), expCronjobPause::history(), 'the same request keeps what it read' );
            $_SERVER['REQUEST_TIME_FLOAT'] = 1001.5;
            $this->assertCount( 1, expCronjobPause::history() );
        }
        finally
        {
            if ( $had === null )
                unset( $_SERVER['REQUEST_TIME_FLOAT'] );
            else
                $_SERVER['REQUEST_TIME_FLOAT'] = $had;
        }
    }

    /**
     * The Setup overview counts only the parts cronjob.ini defines: a state file left by a removed part pauses nothing.
     */
    public function testTheSetupOverviewLeavesOutPartsThatAreNotDefined()
    {
        expCronjobPause::pause( 'frequent', $this->user );
        expCronjobPause::pause( 't471-removed-part', $this->user );
        $collection = new eZSetupFunctionCollection();
        $this->assertSame( 1, $collection->fetchCronjobPauses()['result']['count'] );
    }
}
