<?php
/**
 * The permanent contract of the exp:upgrade commands: their names, their options, the exit codes, the states, the
 * stages and the keys of the machine output exp-upgrade/1. Options and keys may be added (extend the snapshot);
 * nothing here may ever be renamed or removed. Needs no installation.
 *
 *  UC-01 every command and its option configuration (snapshot)
 *  UC-02 exit codes, states, stages, format name
 *  UC-03 the keys of the report, of a stage and of a step
 *  UC-04 every command has its entry point in bin/php with the @alias the console dispatches, and a description
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expUpgradeCommandContractTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    /** The snapshot. Add, never change or remove. */
    const COMMANDS = array(
        'exp:upgrade' => '[stages:][apply][dry-run][only:*][skip:*][format:][no-backup][backup-dir:][force-order][fail-on-pending]',
        'exp:upgrade:status' => '[stages:][only:*][format:][fail-on-pending]',
        'exp:upgrade:steps' => '[stage:][engine:][format:]',
        'exp:upgrade:database' => '[apply][dry-run][only:*][skip:*][format:][no-backup][backup-dir:][force-order][fail-on-pending]',
        'exp:upgrade:scripts' => '[apply][dry-run][only:*][skip:*][format:][no-backup][backup-dir:][force-order][fail-on-pending]',
        'exp:upgrade:verify' => '[stages:][format:]',
        'exp:upgrade:backup' => '[backup-dir:][format:]',
    );

    /** command => entry point in bin/php */
    const ENTRY = array(
        'exp:upgrade' => 'upgrade',
        'exp:upgrade:status' => 'upgradestatus',
        'exp:upgrade:steps' => 'upgradesteps',
        'exp:upgrade:database' => 'upgradedatabase',
        'exp:upgrade:scripts' => 'upgradescripts',
        'exp:upgrade:verify' => 'upgradeverify',
        'exp:upgrade:backup' => 'upgradebackup',
    );

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
        foreach ( array( 'expupgradestep', 'expupgraderegistry', 'expupgrade' ) as $f )
            require_once self::$root . "/kernel/classes/upgrade/$f.php";
        require_once self::$root . '/kernel/private/classes/runnable/runnable.php';
        require_once self::$root . '/kernel/private/classes/runnable/command.php';
        require_once self::$root . '/kernel/private/classes/commands/upgradebase.php';
    }

    /** @return array option => true of a config string */
    private static function options( $config )
    {
        preg_match_all( '/\[([a-z-]+:?\*?)\]/', $config, $m );
        return array_fill_keys( $m[1], true );
    }

    /** UC-01 */
    public function testCommandsAndOptionsOnlyGrow()
    {
        $now = \Exponential\Command\Kernel\UpgradeBase::contract();
        foreach ( self::COMMANDS as $command => $config )
        {
            $this->assertArrayHasKey( $command, $now, "the command $command is permanent" );
            foreach ( array_keys( self::options( $config ) ) as $option )
                $this->assertArrayHasKey( $option, self::options( $now[$command] ), "$command keeps --$option with the same arity" );
        }
        $help = \Exponential\Command\Kernel\UpgradeBase::optionHelp();
        foreach ( $now as $command => $config )
        {
            $this->assertArrayHasKey( $command, self::COMMANDS, "a new command $command: add it to the snapshot" );
            foreach ( array_keys( self::options( $config ) ) as $option )
                $this->assertArrayHasKey( rtrim( $option, ':*' ), $help, "--$option has a help text" );
        }
    }

    /** UC-02 */
    public function testCodesStatesStages()
    {
        $this->assertSame( 'exp-upgrade/1', expUpgrade::FORMAT );
        $this->assertSame( array( 0, 1, 2, 3, 4 ), array( expUpgrade::EXIT_OK, expUpgrade::EXIT_FAILED, expUpgrade::EXIT_USAGE,
                                                           expUpgrade::EXIT_UNREACHABLE, expUpgrade::EXIT_INTERNAL ) );
        $this->assertSame( array( 'done', 'pending', 'not-needed', 'failed', 'unknown' ), expUpgrade::STATES );
        $this->assertSame( array( 'code', 'libraries', 'database', 'scripts', 'finish' ), expUpgradeRegistry::STAGES );
        $this->assertSame( array( 'present', 'missing', 'not-applicable', 'unknown' ),
                           array( expUpgradeStep::PRESENT, expUpgradeStep::MISSING, expUpgradeStep::NOT_APPLICABLE, expUpgradeStep::UNKNOWN ) );
    }

    /** UC-03 */
    public function testReportKeys()
    {
        $report = expUpgrade::report( 'dry-run', array( 'command' => 'exp:upgrade' ) );
        foreach ( array( 'format', 'command', 'mode', 'installation', 'backup', 'stages', 'summary', 'errors', 'warnings', 'exit' ) as $key )
            $this->assertArrayHasKey( $key, $report );
        foreach ( array( 'engine', 'database_version', 'kernel_version' ) as $key )
            $this->assertArrayHasKey( $key, $report['installation'] );
        foreach ( array( 'done', 'pending', 'not-needed', 'failed', 'unknown', 'applied' ) as $key )
            $this->assertArrayHasKey( $key, $report['summary'] );
        $this->assertSame( 0, $report['exit'] );

        // the keys of a step entry and a stage, as expUpgrade writes them
        $source = file_get_contents( self::$root . '/kernel/classes/upgrade/expupgrade.php' );
        foreach ( array( 'id', 'stage', 'version', 'description', 'engines', 'after', 'optional', 'state', 'detail', 'drift', 'action',
                         'plan', 'messages', 'duration_ms', 'record' ) as $key )
            $this->assertStringContainsString( "'$key' =>", $source, "step key $key" );
        $this->assertStringContainsString( "array( 'stage' => \$stageName, 'state' => 'done', 'pending' => 0, 'steps' => array() )", $source );
        foreach ( array( 'none', 'would-apply', 'applied', 'failed', 'blocked', 'not-run' ) as $action )
            $this->assertStringContainsString( "'$action'", $source, "action $action" );
    }

    /** UC-04 */
    public function testEntryPoints()
    {
        foreach ( self::ENTRY as $command => $file )
        {
            $path = self::$root . "/bin/php/$file.php";
            $this->assertFileExists( $path );
            $this->assertTrue( is_executable( $path ), "$file.php is executable" );
            $text = file_get_contents( $path );
            $this->assertStringStartsWith( "#!/usr/bin/env php\n", $text );
            if ( $command !== 'exp:upgrade' )
                $this->assertStringContainsString( "@alias $command", $text );
            $class = self::$root . "/kernel/private/classes/commands/$file.php";
            $this->assertFileExists( $class );
            $this->assertMatchesRegularExpression( '/@description \S/', file_get_contents( $class ) );
        }
    }
}
