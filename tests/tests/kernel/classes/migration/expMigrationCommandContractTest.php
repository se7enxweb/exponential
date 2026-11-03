<?php
/**
 * The permanent contract of the exp:migration commands: their names, their options and arguments, the exit codes and
 * the keys of the machine output exp-migration/1. Options, arguments and keys may be added (extend the snapshot);
 * nothing here may ever be renamed or removed. Needs no installation.
 *
 *  MC-01 every command and its option configuration (snapshot), and the arguments
 *  MC-02 exit codes, states, format name
 *  MC-03 the keys of a run report and of a migration entry; the keys the commands add (command, schema)
 *  MC-04 every command has its entry point in bin/php with the @alias the console dispatches, and a description
 *  MC-05 the literal option configuration, arguments and help of each command (what exp:help:examples reads) are
 *        the contract's
 *  MC-06 the text output of a run report names every migration and ends with the summary (runLines())
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expMigrationCommandContractTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    /** The snapshot. Add, never change or remove. */
    const COMMANDS = array(
        'exp:migration:status' => '[only:*][environment:][format:][fail-on-pending][runs:]',
        'exp:migration:migrate' => '[apply][dry-run][only:*][path:*][environment:][format:][login:][no-caption][no-restore][include-uncommitted][no-commit][no-push][no-tag][force][reason:][run-id:][progress:]',
        'exp:migration:dry-run' => '[only:*][path:*][environment:][format:][login:][diff][simulate]',
        'exp:migration:rollback' => '[apply][dry-run][format:][source:][environment:][login:][expert][confirm:][parts:][backup-dir:][no-safety-caption][force-no-caption][no-verify][progress:]',
        'exp:migration:resume' => '[apply][dry-run][environment:][format:][login:][no-caption][no-restore][include-uncommitted][no-commit][no-push][no-tag][force][reason:][run-id:][progress:]',
        'exp:migration:caption' => '[apply][dry-run][format:][list][label:][backup-dir:]',
        'exp:migration:push' => '[apply][dry-run][format:][limit:]',
        'exp:migration:check' => '[path:*][environment:][format:]',
        'exp:migration:generate' => '[skeleton][source:][description:][author:][type:][apply][dry-run][format:][from-recording:][engine:][from-content:][depth:][max-objects:][from-class:][from-role:][from-section:][from-settings:][siteaccess:][from-diff:][compare:][subtree:][token:][backup-dir:]',
        'exp:migration:record:start' => '[apply][dry-run][format:][site-wide][label:][user:][for:][until:]',
        'exp:migration:record:stop' => '[apply][dry-run][format:][site-wide][user:]',
        'exp:migration:record:status' => '[format:][user:]',
        'exp:migration:record:list' => '[format:][count:]',
        'exp:migration:record:show' => '[format:][plan]',
        'exp:migration:record:discard' => '[apply][dry-run][format:]',
        'exp:migration:deploy' => '[remotes:*][apply][dry-run][transfer-only][only:*][format:][parallel:][timeout:][retries:][staged][continue:][rollback:][login:][run-id:][progress:]',
        'exp:migration:remote:list' => '[remotes:*][format:]',
        'exp:migration:remote:status' => '[remotes:*][format:][parallel:][timeout:][history:]',
        'exp:migration:heal' => '[fix:*][stale-lock-age:][apply][dry-run][format:]',
        'exp:migration:convert' => '[apply][dry-run][format:][source:][author:]',
        'exp:migration:repo:split' => '[apply][dry-run][format:][directory:][branch:][to:][remote:]',
    );

    /** The arguments. Add, never change or remove. */
    const ARGUMENTS = array(
        'exp:migration:rollback' => '[target]',
        'exp:migration:resume' => '[run]',
        'exp:migration:generate' => '[name]',
        'exp:migration:record:stop' => '[recording]',
        'exp:migration:record:show' => '[recording]',
        'exp:migration:record:discard' => '[recording]',
        'exp:migration:convert' => '[file]',
    );

    /** command => entry point in bin/php */
    const ENTRY = array(
        'exp:migration:status' => 'expmigrationstatus',
        'exp:migration:migrate' => 'expmigrationmigrate',
        'exp:migration:dry-run' => 'expmigrationdryrun',
        'exp:migration:rollback' => 'expmigrationrollback',
        'exp:migration:resume' => 'expmigrationresume',
        'exp:migration:caption' => 'expmigrationcaption',
        'exp:migration:push' => 'expmigrationpush',
        'exp:migration:check' => 'expmigrationcheck',
        'exp:migration:generate' => 'expmigrationgenerate',
        'exp:migration:record:start' => 'expmigrationrecordstart',
        'exp:migration:record:stop' => 'expmigrationrecordstop',
        'exp:migration:record:status' => 'expmigrationrecordstatus',
        'exp:migration:record:list' => 'expmigrationrecordlist',
        'exp:migration:record:show' => 'expmigrationrecordshow',
        'exp:migration:record:discard' => 'expmigrationrecorddiscard',
        'exp:migration:deploy' => 'expmigrationdeploy',
        'exp:migration:remote:list' => 'expmigrationremotelist',
        'exp:migration:remote:status' => 'expmigrationremotestatus',
        'exp:migration:heal' => 'expmigrationheal',
        'exp:migration:convert' => 'expmigrationconvert',
        'exp:migration:repo:split' => 'expmigrationreposplit',
    );

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
        foreach ( array( 'upgrade/expupgradestep', 'upgrade/expupgraderegistry', 'upgrade/expupgrade', 'migration/expmigration', 'migration/expmigrationrunner',
                         'migration/expmigrationlock' ) as $f )
            require_once self::$root . "/kernel/classes/$f.php";
        require_once self::$root . '/kernel/private/classes/runnable/runnable.php';
        require_once self::$root . '/kernel/private/classes/runnable/command.php';
        require_once self::$root . '/kernel/private/classes/commands/expmigrationbase.php';
    }

    /** @return array option => true of a config string */
    private static function options( $config )
    {
        preg_match_all( '/\[([a-z-]+:?\*?)\]/', $config, $m );
        return array_fill_keys( $m[1], true );
    }

    /** MC-01 */
    public function testCommandsAndOptionsOnlyGrow()
    {
        $now = \Exponential\Command\Kernel\ExpmigrationBase::contract();
        foreach ( self::COMMANDS as $command => $config )
        {
            $this->assertArrayHasKey( $command, $now, "the command $command is permanent" );
            foreach ( array_keys( self::options( $config ) ) as $option )
                $this->assertArrayHasKey( $option, self::options( $now[$command] ), "$command keeps --$option with the same arity" );
        }
        $help = \Exponential\Command\Kernel\ExpmigrationBase::optionHelp();
        foreach ( $now as $command => $config )
        {
            $this->assertArrayHasKey( $command, self::COMMANDS, "a new command $command: add it to the snapshot" );
            foreach ( array_keys( self::options( $config ) ) as $option )
                $this->assertArrayHasKey( rtrim( $option, ':*' ), $help, "--$option has a help text" );
        }
        $arguments = \Exponential\Command\Kernel\ExpmigrationBase::arguments();
        foreach ( self::ARGUMENTS as $command => $config )
            $this->assertSame( $config, isset( $arguments[$command] ) ? $arguments[$command] : null, "$command keeps its argument $config" );
        foreach ( array_keys( $arguments ) as $command )
            $this->assertArrayHasKey( $command, self::ARGUMENTS, "a new argument of $command: add it to the snapshot" );
    }

    /** MC-02 */
    public function testCodesStatesFormat()
    {
        $this->assertSame( 'exp-migration/1', expMigrationRunner::REPORT );
        $this->assertSame( array( 0, 1, 2, 3, 4 ), array( expUpgrade::EXIT_OK, expUpgrade::EXIT_FAILED, expUpgrade::EXIT_USAGE,
                                                           expUpgrade::EXIT_UNREACHABLE, expUpgrade::EXIT_INTERNAL ) );
        $this->assertSame( array( 'done', 'pending', 'not-needed', 'failed', 'unknown' ),
                           array( expMigration::DONE, expMigration::PENDING, expMigration::NOT_NEEDED, expMigration::FAILED, expMigration::UNKNOWN ) );
    }

    /** MC-03 */
    public function testReportKeys()
    {
        $source = file_get_contents( self::$root . '/kernel/classes/migration/expmigrationrunner.php' );
        foreach ( array( 'schema', 'mode', 'run_id', 'environment', 'engine', 'transaction', 'user', 'started_at', 'migrations', 'summary', 'errors',
                         'warnings', 'exit' ) as $key )
            $this->assertStringContainsString( "'$key' =>", $source, "report key $key" );
        foreach ( array( 'id', 'source', 'description', 'file', 'checksum', 'record_checksum', 'changed', 'state', 'detail', 'action', 'maintenance',
                         'transaction', 'plan', 'messages', 'duration_ms', 'record' ) as $key )
            $this->assertStringContainsString( "'$key' =>", $source, "migration key $key" );
        foreach ( array( 'applied', 'would-apply', 'not-run', 'blocked', 'refused', 'accepted', 'would-accept', 'undone', 'would-undo', 'restored', 'simulated' ) as $action )
            $this->assertStringContainsString( "'$action'", $source, "action $action" );
        $base = file_get_contents( self::$root . '/kernel/private/classes/commands/expmigrationbase.php' );
        $this->assertStringContainsString( "array( 'schema' => \\expMigrationRunner::REPORT, 'command' => \$this->name() ) + \$report", $base,
                                           'every command output names the schema and the command' );
        $status = file_get_contents( self::$root . '/kernel/classes/migration/expmigrationstatus.php' );
        foreach ( array( 'table', 'orphans', 'runs', 'pushes', 'lock' ) as $key )
            $this->assertStringContainsString( "\$report['$key']", $status, "status key $key" );
        $simulation = file_get_contents( self::$root . '/kernel/classes/migration/expmigrationsimulation.php' );
        foreach ( array( 'result', 'messages', 'duration_ms', 'diff' ) as $key )
            $this->assertStringContainsString( "'$key' =>", $simulation, "simulation key $key" );
    }

    /** MC-04 */
    public function testEntryPoints()
    {
        foreach ( self::ENTRY as $command => $file )
        {
            $path = self::$root . "/bin/php/$file.php";
            $this->assertFileExists( $path );
            $this->assertTrue( is_executable( $path ), "$file.php is executable" );
            $text = file_get_contents( $path );
            $this->assertStringStartsWith( "#!/usr/bin/env php\n", $text );
            $this->assertStringContainsString( "@alias $command\n", $text );
            $class = self::$root . "/kernel/private/classes/commands/$file.php";
            $this->assertFileExists( $class );
            $code = file_get_contents( $class );
            $this->assertMatchesRegularExpression( '/@description \S/', $code );
            $this->assertStringContainsString( "@alias $command\n", $code );
            $this->assertStringContainsString( "return '$command';", $code, "$file names itself $command" );
        }
        $this->assertSame( array_keys( self::COMMANDS ), array_keys( self::ENTRY ), 'every command has its entry point' );
    }

    /** MC-05 */
    public function testLiteralOptionsMatchTheContract()
    {
        require_once self::$root . '/kernel/classes/help/exphelpexamplesgenerator.php';
        $contract = \Exponential\Command\Kernel\ExpmigrationBase::contract();
        $arguments = \Exponential\Command\Kernel\ExpmigrationBase::arguments();
        $help = \Exponential\Command\Kernel\ExpmigrationBase::optionHelp();
        foreach ( self::ENTRY as $command => $file )
        {
            $code = file_get_contents( self::$root . "/kernel/private/classes/commands/$file.php" );
            $this->assertSame( 1, preg_match( "/->startup\\( '([^']+)', '([^']*)'/", $code, $m ), "$file: startup() with a literal configuration" );
            $this->assertSame( $contract[$command], $m[1], "$command: the literal option configuration is the contract's" );
            $this->assertSame( isset( $arguments[$command] ) ? $arguments[$command] : '', $m[2], "$command: the literal arguments are the contract's" );
            $parsed = expHelpExamplesGenerator::scriptHelp( $code );
            $this->assertNotSame( '', $parsed['description'], "$command: a literal description" );
            foreach ( $parsed['options'] as $name => $option )
                $this->assertSame( $help[$name], $option['help'], "$command --$name: the help text of optionHelp()" );
            $this->assertCount( count( self::options( $contract[$command] ) ), $parsed['options'], "$command: every option has its help" );
            $this->assertStringNotContainsString( 'eZ Publish', $parsed['description'] );
        }
    }

    /** MC-06 */
    public function testRunLines()
    {
        $entry = array( 'id' => '20261031120000_add_news', 'source' => 'site', 'description' => 'News', 'file' => 'migrations/20261031120000_add_news.php',
                        'checksum' => str_repeat( 'a', 64 ), 'record_checksum' => '', 'changed' => false, 'state' => 'pending', 'detail' => '',
                        'action' => 'would-apply', 'maintenance' => 'no', 'transaction' => '', 'plan' => array( 'create the class news' ),
                        'messages' => array(), 'duration_ms' => 0, 'record' => null );
        $report = array( 'schema' => 'exp-migration/1', 'mode' => 'dry-run', 'run_id' => '20261031-120000-a1b2c3', 'environment' => 'staging',
                         'engine' => 'sqlite', 'transaction' => 'full', 'user' => '', 'started_at' => 0, 'migrations' => array( $entry ),
                         'summary' => array( 'total' => 1, 'done' => 0, 'pending' => 1, 'not-needed' => 0, 'failed' => 0, 'unknown' => 0, 'would-apply' => 1 ),
                         'errors' => array(), 'warnings' => array(), 'exit' => 0 );
        $lines = \Exponential\Command\Kernel\ExpmigrationBase::runLines( $report );
        $text = implode( "\n", $lines );
        $this->assertStringContainsString( 'dry run, nothing is changed', $lines[0] );
        $this->assertStringContainsString( 'environment staging, database sqlite (transactions: full)', $text );
        $this->assertMatchesRegularExpression( '/would-apply\s+20261031120000_add_news\s+site/', $text );
        $this->assertStringContainsString( 'would create the class news', $text );
        $this->assertStringStartsWith( 'Summary: 1 migration(s): 0 done, 1 pending', end( $lines ) );
        $this->assertStringContainsString( '1 would-apply', end( $lines ) );
    }
}
