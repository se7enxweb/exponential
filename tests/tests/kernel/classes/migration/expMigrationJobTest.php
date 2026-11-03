<?php
/**
 * Runs started in the background from Setup > Migrations (expMigrationJob) and the logic of the module's views
 * (expMigrationAdmin), without a database.
 *
 *  JB-01 the command line of each kind: the command, the target, --apply --format=json --login --siteaccess,
 *        --run-id for migrate and resume (not rollback), --progress, only the flags and values of the kind
 *  JB-02 refused arguments: an unknown kind, an id that is no run id, no user, a rollback without a target, a target
 *        that is no id (one starting with - would be an option)
 *  JB-03 the shell line: detached, the pid written by the shell that becomes php, every argument quoted
 *  JB-04 a job read back: running or ended, the report of its output (a line before it does not matter), its errors,
 *        the newest first; a job file that is not one, an id that is not one
 *  JB-05 start() refuses an unknown kind, a rollback without a target, no user, the lock held by another process, no
 *        php binary, and writes nothing then
 *  JB-06 expMigrationAdmin: the badge of each state, the run form (all, a choice, none ticked, flags, a reason), the
 *        rollback form (confirm, expert and the typed id, parts), the kind of a target, runs by state, decorate(),
 *        the dashboard's summary of a status, the end of a log
 *  JB-07 the module: its views, policies and the policy of each view, the menu entry, "Verify it's you" for run,
 *        rollback, edit and the simulation (and never for seeing), the dashboard block with its policy and fetch
 *        function, the two designs' templates and page script the same
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationJobTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private static $root;

    /** @var string */
    private $dir;

    const ID = '20261031-120000-a1b2c3';

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    protected function setUp(): void
    {
        $this->dir = self::$root . '/var/tmp/migration-job-test-' . getmypid();
        if ( !is_dir( $this->dir ) )
            mkdir( $this->dir, 0775, true );
    }

    protected function tearDown(): void
    {
        expMigrationLock::installation( $this->dir . '/upgrade.lock' )->release();
        foreach ( glob( $this->dir . '/*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    /** JB-01 */
    public function testArguments()
    {
        $this->assertSame(
            array( 'bin/php/expmigrationmigrate.php', '--apply', '--format=json', '--login=editor', '--siteaccess=admin', '--run-id=' . self::ID,
                   '--progress=/x/' . self::ID . '.progress', '--no-push', '--force', '--only=m1,m2', '--reason=the file was reformatted' ),
            expMigrationJob::arguments( 'migrate', self::ID, array( 'no-push' => true, 'force' => true, 'no-tag' => false, 'expert' => true,
                                                                     'only' => array( 'm1', 'm2' ), 'reason' => 'the file was reformatted',
                                                                     'confirm' => 'x', 'target' => 'ignored' ),
                                        'editor', 'admin', '/x/' . self::ID . '.progress' ) );
        $this->assertSame(
            array( 'bin/php/expmigrationresume.php', '20261030-080000-ffffff', '--apply', '--format=json', '--login=editor', '--run-id=' . self::ID, '--progress=p' ),
            expMigrationJob::arguments( 'resume', self::ID, array( 'target' => '20261030-080000-ffffff', 'only' => 'm1' ), 'editor', '', 'p' ),
            'resume: the run as its argument, no --only, no siteaccess when none is given' );
        $this->assertSame(
            array( 'bin/php/expmigrationrollback.php', '2026-10-29_10-15-00', '--apply', '--format=json', '--allow-root-user', '--login=editor', '--siteaccess=admin',
                   '--progress=p', '--expert', '--confirm=2026-10-29_10-15-00', '--parts=database' ),
            expMigrationJob::arguments( 'rollback', self::ID, array( 'target' => '2026-10-29_10-15-00', 'expert' => true, 'no-push' => true,
                                                                      'confirm' => '2026-10-29_10-15-00', 'parts' => 'database' ), 'editor', 'admin', 'p', true ),
            'rollback: no --run-id, its own flags only; a server that runs as root passes --allow-root-user' );
    }

    /** JB-02 */
    public function testRefusedArguments()
    {
        foreach ( array( array( 'upgrade', self::ID, 'admin', array() ), array( 'migrate', '../../etc', 'admin', array() ),
                         array( 'migrate', self::ID, '', array() ), array( 'rollback', self::ID, 'admin', array( 'target' => ' ' ) ),
                         array( 'rollback', self::ID, 'admin', array( 'target' => '--parts=code' ) ), array( 'resume', self::ID, 'admin', array( 'target' => 'a b' ) ) ) as $case )
        {
            try
            {
                expMigrationJob::arguments( $case[0], $case[1], $case[3], $case[2], '', 'p' );
                $this->fail( 'refused: ' . json_encode( $case ) );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertNotSame( '', $e->getMessage() );
            }
        }
    }

    /** JB-03 */
    public function testCommandLine()
    {
        $line = expMigrationJob::commandLine( '/usr/bin/php', array( 'bin/php/expmigrationmigrate.php', "--reason=it's; rm -rf /" ), '/srv/site', '/srv/site/var/jobs/' . self::ID );
        $this->assertStringStartsWith( "cd '/srv/site' && nohup sh -c ", $line );
        $this->assertStringEndsWith( " > '/srv/site/var/jobs/" . self::ID . ".out' 2> '/srv/site/var/jobs/" . self::ID . ".err' < /dev/null &", $line );
        $this->assertStringContainsString( 'echo $$ > ', $line, 'the shell writes its own pid' );
        $this->assertStringContainsString( 'exec ', $line, 'and becomes php' );
        $this->assertStringNotContainsString( "--reason=it's; rm -rf /'", $line, 'the reason is quoted, not run' );
        // the inner command, unquoted once, is the php command with each argument quoted
        $this->assertSame( 1, preg_match( "/nohup sh -c ('(?:[^']|'\\\\'')*')/", $line, $m ) );
        $inner = str_replace( "'\\''", "'", substr( $m[1], 1, -1 ) );
        $this->assertStringContainsString( "exec '/usr/bin/php' 'bin/php/expmigrationmigrate.php' '--reason=it'\\''s; rm -rf /'", $inner );
    }

    /** JB-04 */
    public function testJobsReadBack()
    {
        $older = '20261031-110000-000001';
        file_put_contents( $this->dir . "/$older.json", json_encode( array( 'id' => $older, 'kind' => 'rollback', 'target' => 'm1', 'user' => 'editor', 'pid' => 0, 'started' => 1 ) ) );
        file_put_contents( $this->dir . "/$older.out", "WARN something {odd}\n" . json_encode( array( 'schema' => 'exp-migration/1', 'exit' => 1, 'errors' => array( 'x' ) ), JSON_PRETTY_PRINT ) );
        file_put_contents( $this->dir . "/$older.err", "FAIL: x\n" );
        file_put_contents( $this->dir . '/' . self::ID . '.json', json_encode( array( 'id' => self::ID, 'kind' => 'migrate', 'user' => 'editor', 'pid' => getmypid(), 'started' => 2 ) ) );
        file_put_contents( $this->dir . '/20261031-130000-000002.json', json_encode( array( 'id' => 'another' ) ) );
        file_put_contents( $this->dir . '/notes.json', '{}' );

        $job = expMigrationJob::get( $older, $this->dir );
        $this->assertFalse( $job['running'] );
        $this->assertTrue( $job['ended'] );
        $this->assertTrue( $job['reported'] );
        $this->assertSame( 1, $job['exit'] );
        $this->assertSame( array( 'x' ), $job['report']['errors'] );
        $this->assertSame( 'FAIL: x', $job['errors'], 'a job with a report: its errors only' );
        $this->assertSame( $this->dir . "/$older.progress", $job['progress'] );

        $all = expMigrationJob::all( 10, $this->dir );
        $this->assertSame( array( self::ID, $older ), array_column( $all, 'id' ), 'newest first; a file naming another id is no job' );
        $this->assertNull( expMigrationJob::get( 'notes', $this->dir ) );
        $this->assertNull( expMigrationJob::get( '20261031-130000-000002', $this->dir ) );
        $this->assertNull( expMigrationJob::report( 'no report' ) );
        $this->assertNull( expMigrationJob::report( '{"exit": 0}' ), 'a report names its schema' );
        $this->assertFalse( expMigrationJob::alive( 0 ) );
        // this test process is alive, but it is no migration command
        if ( is_dir( '/proc/' . getmypid() ) )
        {
            $this->assertFalse( expMigrationJob::alive( getmypid() ) );
            $this->assertFalse( $all[0]['running'] );
            $this->assertFalse( $all[0]['reported'], 'ended without a report' );
        }
        $this->assertNull( expMigrationJob::running( $this->dir ) );
    }

    /** JB-05 */
    public function testStartRefuses()
    {
        $settings = array( 'directory' => $this->dir, 'php' => '/usr/bin/php', 'lock' => expMigrationLock::installation( $this->dir . '/upgrade.lock' ) );
        $this->assertSame( expMigrationJob::REFUSED_KIND, expMigrationJob::start( 'upgrade', array(), 'admin', $settings )['refused'] );
        $this->assertSame( expMigrationJob::REFUSED_TARGET, expMigrationJob::start( 'rollback', array(), 'admin', $settings )['refused'] );
        $this->assertSame( expMigrationJob::REFUSED_USER, expMigrationJob::start( 'migrate', array(), '', $settings )['refused'] );

        // another process holds the installation lock
        $handle = fopen( $this->dir . '/upgrade.lock', 'c' );
        $this->assertTrue( flock( $handle, LOCK_EX | LOCK_NB ) );
        $refused = expMigrationJob::start( 'migrate', array(), 'tester', $settings );
        flock( $handle, LOCK_UN );
        fclose( $handle );
        $this->assertFalse( $refused['ok'] );
        $this->assertSame( expMigrationJob::REFUSED_LOCKED, $refused['refused'] );

        // no php command line binary: refused before the user is looked up (which needs the database)
        $this->assertSame( expMigrationJob::REFUSED_PHP, expMigrationJob::start( 'migrate', array(), 'tester', array( 'php' => '' ) + $settings )['refused'] );
        $this->assertSame( array(), glob( $this->dir . '/*.json' ), 'nothing was written' );
    }

    /** JB-06 */
    public function testAdmin()
    {
        $this->assertSame( array( 'ok', 'warn', 'bad', 'info', 'info', 'ok', 'warn' ),
                           array_map( array( 'expMigrationAdmin', 'stateKind' ), array( 'done', 'pending', 'failed', 'not-needed', 'rolled-back', 'applied', 'blocked' ) ) );

        $pending = array( 'm1', 'm2', 'm3' );
        $errors = array();
        $this->assertSame( array(), expMigrationAdmin::runOptions( array( 'MigrationChoice' => '1', 'MigrationOnly' => $pending ), $pending, $errors ), 'all ticked: all' );
        $this->assertSame( array(), $errors );
        $this->assertSame( array( 'only' => array( 'm1', 'm3' ), 'no-push' => true ),
                           expMigrationAdmin::runOptions( array( 'MigrationOnly' => array( 'm1', 'm3', 'm9' ), 'RunFlag' => array( 'no-push' => '1', 'force' => '1', 'no-tag' => '' ) ), $pending, $errors ),
                           'a choice of the listed ones; only the run flags of the form' );
        $this->assertSame( array(), $errors );
        expMigrationAdmin::runOptions( array( 'MigrationChoice' => '1' ), $pending, $errors );
        $this->assertSame( array( 'only' ), $errors, 'none ticked is a choice of none' );
        $errors = array();
        expMigrationAdmin::runOptions( array( 'ForceChanged' => '1', 'ForceReason' => '  ' ), $pending, $errors );
        $this->assertSame( array( 'reason' ), $errors );
        $errors = array();
        expMigrationAdmin::runOptions( array( 'ForceChanged' => '1', 'ForceReason' => str_repeat( 'x', 501 ) ), $pending, $errors );
        $this->assertSame( array( 'reason-length' ), $errors );
        $errors = array();
        $this->assertSame( array( 'force' => true, 'reason' => 'reformatted only' ),
                           expMigrationAdmin::runOptions( array( 'ForceChanged' => '1', 'ForceReason' => " reformatted\n only " ), $pending, $errors ) );

        $errors = array();
        $this->assertSame( array( 'target' => 'r' ), expMigrationAdmin::rollbackOptions( 'run', 'r', array( 'ConfirmRollback' => '1', 'RestorePart' => array( 'database', 'storage', 'settings' ) ), $errors ) );
        $this->assertSame( array(), $errors );
        $this->assertSame( array( 'target' => 'r', 'parts' => 'database' ), expMigrationAdmin::rollbackOptions( 'run', 'r', array( 'ConfirmRollback' => '1', 'RestorePart' => array( 'database', 'code' ) ), $errors ) );
        expMigrationAdmin::rollbackOptions( 'run', 'r', array( 'RestorePart' => array( 'code' ) ), $errors );
        $this->assertSame( array( 'confirm', 'parts' ), $errors );
        $errors = array();
        $this->assertSame( array( 'target' => 'm1' ), expMigrationAdmin::rollbackOptions( 'migration', 'm1', array( 'ConfirmRollback' => '1', 'RestorePart' => array( 'database' ) ), $errors ), 'one migration restores no parts' );
        $errors = array();
        expMigrationAdmin::rollbackOptions( 'caption', 'c1', array( 'ConfirmCaption' => 'c2' ), $errors );
        $this->assertSame( array( 'expert', 'typed' ), $errors );
        $errors = array();
        $this->assertSame( array( 'target' => 'c1', 'expert' => true, 'confirm' => 'c1' ), expMigrationAdmin::rollbackOptions( 'caption', 'c1', array( 'Expert' => '1', 'ConfirmCaption' => ' c1 ' ), $errors ) );
        $this->assertSame( array(), $errors );

        $this->assertSame( 'run', expMigrationAdmin::targetKind( self::ID, array( self::ID ), array( self::ID ), array() ) );
        $this->assertSame( 'migration', expMigrationAdmin::targetKind( 'm1', array( self::ID ), array( 'm1' ), array( 'm1' ) ) );
        $this->assertSame( 'caption', expMigrationAdmin::targetKind( 'c1', array(), array( 'm1' ), array( 'c1' ) ) );
        $this->assertSame( '', expMigrationAdmin::targetKind( 'x', array(), array(), array() ) );

        $runs = array( array( 'run_id' => 'a', 'state' => 'done' ), array( 'run_id' => 'b', 'state' => 'failed' ) );
        $this->assertSame( array( 'b' ), array_column( expMigrationAdmin::filterRuns( $runs, 'failed' ), 'run_id' ) );
        $this->assertCount( 2, expMigrationAdmin::filterRuns( $runs, 'nonsense' ) );
        $decorated = expMigrationAdmin::decorate( array( array( 'state' => 'failed', 'action' => 'applied', 'started_at' => '2026-10-31T12:00:00+00:00', 'finished_at' => 5 ) ) );
        $this->assertSame( array( 'bad', 'ok', 1793448000, 5 ), array( $decorated[0]['kind'], $decorated[0]['action_kind'], $decorated[0]['started_ts'], $decorated[0]['finished_ts'] ) );

        $summary = expMigrationAdmin::summary( array( 'table' => true, 'orphans' => array( array( 'id' => 'gone' ) ), 'lock' => null,
            'migrations' => array( array( 'state' => 'pending' ), array( 'state' => 'failed' ), array( 'state' => 'pending' ), array( 'state' => 'done' ) ),
            'runs' => array( array( 'run_id' => self::ID, 'state' => 'failed', 'started_at' => '2026-10-31T12:00:00+00:00' ) ) ) );
        $this->assertSame( array( 2, 1, 1, 4, true, '' ), array( $summary['pending'], $summary['failed'], $summary['orphans'], $summary['total'], $summary['ready'], $summary['lock'] ) );
        $this->assertSame( array( self::ID, 'bad', 1793448000 ), array( $summary['run']['run_id'], $summary['run']['kind'], $summary['run']['started_ts'] ) );
        $this->assertNull( expMigrationAdmin::summary( array() )['run'] );

        $log = $this->dir . '/run.log';
        file_put_contents( $log, "first line\nsecond line\nthird line\n" );
        $tail = expMigrationAdmin::logTail( $log, 15 );
        $this->assertSame( array( "third line\n", 34, true, true ), array( $tail['text'], $tail['size'], $tail['truncated'], $tail['exists'] ), 'from the first whole line' );
        $this->assertSame( array( '', 0, false, false ), array_values( expMigrationAdmin::logTail( $this->dir . '/none.log', 10 ) ) );
    }

    /** JB-07 */
    public function testTheModule()
    {
        $Module = $ViewList = $FunctionList = null;
        include self::$root . '/kernel/migration/module.php';
        $this->assertSame( array( 'read', 'run', 'rollback', 'edit', 'remotes', 'deploy' ), array_keys( $FunctionList ) );
        $policies = array();
        foreach ( $ViewList as $view => $def )
        {
            $policies[$view] = isset( $def['functions'] ) ? $def['functions'] : array();
            $this->assertFileExists( self::$root . '/kernel/migration/' . $def['script'] );
            $this->assertSame( 'ezsetupnavigationpart', $def['default_navigation_part'] );
        }
        $this->assertSame( array( 'status' => array( 'read' ), 'history' => array( 'read' ), 'log' => array( 'read' ), 'stream' => array( 'read' ),
                                  'dryrun' => array( 'read' ), 'run' => array( 'run' ), 'rollback' => array( 'rollback' ), 'recording' => array( 'edit' ),
                                  'remotes' => array( 'read' ), 'remote' => array( 'remotes' ), 'deploy' => array( 'read' ), 'deployment' => array( 'read' ),
                                  'editor' => array( 'edit' ), 'trigger' => array() ), $policies, 'migration/trigger: no policy, the signature is the permission' );

        $this->assertMatchesRegularExpression( '/^ModuleList\[\]=migration$/m', file_get_contents( self::$root . '/settings/module.ini' ) );
        $menu = self::section( 'settings/menu.ini', 'Leftmenu_setup' );
        foreach ( array( 'Links[migrations]=migration/status', 'PolicyList_migrations[]=migration/read', 'GroupLinks_system[]=migrations',
                         'LinkNames[migrations]=Migrations', 'LinkIcons[migrations]=clock-history' ) as $line )
            $this->assertContains( $line, $menu );
        $rules = array();
        foreach ( self::section( 'settings/site.ini', 'ReauthGroup_system' ) as $line )
            if ( strpos( $line, 'Rules[]=' ) === 0 )
                $rules[] = substr( $line, 8 );
        foreach ( array( 'migration/run', 'migration/rollback', 'migration/edit', 'migration/dryrun SimulateButton' ) as $rule )
            $this->assertContains( $rule, $rules, "\"Verify it's you\" for $rule" );
        foreach ( array( 'migration/status', 'migration/history', 'migration/log', 'migration/stream', 'migration/*' ) as $rule )
            $this->assertNotContains( $rule, $rules, 'seeing is never asked' );
        // the decision the kernel takes on these rules
        $settings = array( 'enabled' => true, 'groups' => array( 'system' => array( 'enabled' => true, 'rules' => $rules, 'user_groups' => array() ) ) );
        $asks = function ( $view, $method, array $post ) use ( $settings ) {
            return expReauth::matchGroup( $settings, 'migration', $view, $method, $post ) !== null;
        };
        $this->assertTrue( $asks( 'run', 'POST', array( 'RunButton' => '1' ) ), 'a run' );
        $this->assertTrue( $asks( 'run', 'POST', array( 'ResumeButton' => '1' ) ), 'a resume' );
        $this->assertTrue( $asks( 'rollback', 'POST', array( 'RollbackButton' => '1' ) ), 'a rollback' );
        $this->assertTrue( $asks( 'dryrun', 'POST', array( 'SimulateButton' => '1' ) ), 'a simulation' );
        $this->assertFalse( $asks( 'run', 'GET', array() ), 'the run form' );
        $this->assertFalse( $asks( 'dryrun', 'GET', array() ), 'the dry run' );
        $this->assertFalse( $asks( 'status', 'GET', array() ) );
        $this->assertFalse( $asks( 'stream', 'GET', array() ) );

        // the dashboard block and its fetch function
        $this->assertContains( 'DashboardBlocks[]=migrations', self::section( 'settings/dashboard.ini', 'DashboardSettings' ) );
        $block = self::section( 'settings/dashboard.ini', 'DashboardBlock_migrations' );
        $this->assertContains( 'PolicyList[]=migration/read', $block );
        $this->assertContains( 'ViewList[]=migration/status', $block );
        $FunctionList = null;
        include self::$root . '/kernel/migration/function_definition.php';
        $this->assertSame( array( 'class' => 'expMigrationAdmin', 'method' => 'fetchSummary' ), $FunctionList['summary']['call_method'] );
        $this->assertTrue( method_exists( 'expMigrationAdmin', 'fetchSummary' ) );
        $this->assertFileEquals( self::$root . '/design/admin4/templates/dashboard/migrations.tpl', self::$root . '/design/admin/templates/dashboard/migrations.tpl' );
        foreach ( array( "'migration/status'|ezurl", "concat( 'migration/log/', \$migrations.run.run_id )|ezurl" ) as $link )
            $this->assertStringContainsString( $link, file_get_contents( self::$root . '/design/admin4/templates/dashboard/migrations.tpl' ) );

        foreach ( glob( self::$root . '/design/admin4/templates/migration/{,parts/}*.tpl', GLOB_BRACE ) as $file )
            $this->assertFileEquals( $file, str_replace( '/design/admin4/', '/design/admin/', $file ), 'design/admin has the same template' );
        $this->assertFileEquals( self::$root . '/design/admin4/javascript/page/migration_log.js', self::$root . '/design/admin/javascript/page/migration_log.js' );
    }

    /**
     * The lines of a section of a shipped settings file, without comments.
     *
     * @param string $file
     * @param string $section
     * @return string[]
     */
    private static function section( $file, $section )
    {
        $lines = array();
        $in = false;
        foreach ( file( self::$root . '/' . $file, FILE_IGNORE_NEW_LINES ) as $line )
        {
            if ( preg_match( '/^\[(.+)\]\s*$/', $line, $m ) )
                $in = $m[1] === $section;
            else if ( $in && $line !== '' && $line[0] !== '#' )
                $lines[] = rtrim( $line );
        }
        return $lines;
    }
}
