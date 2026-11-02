<?php
/**
 * Remote deploy, part 2 (expMigrationRemoteDeploy staged mode, rollback of a deploy, live progress;
 * expMigrationDeployRecord; expMigrationDeployNotifier; expMigrationRemoteAdmin; the deploy job), without a network:
 * destination installations in local folders with the stand-in console scripts (fixtures/remotedeploy), as in
 * expMigrationRemoteDeployTest. Nothing leaves the process: mail goes to a fake sender or to the file transport in a
 * folder of the test.
 *
 *  RS-01 stages: the remotes grouped by environment in the order of StageOrder[], unknown environments last
 *  RS-02 a staged deploy: the preflight on every remote, the first stage switched, a gate (state gated, exit 0, the
 *        later stages waiting); continue switches the next stage and records who approved; the last one ends deployed
 *  RS-03 a later stage fails: taken back within the stage, the earlier stays deployed (not in sync, said so); a
 *        rollback of the deploy takes the earlier back too; a deploy with nothing applied cannot be rolled back
 *  RS-04 the live progress: run, phase, host, step-done and end events; fold() keeps every remote's state, 100 % at
 *        the end
 *  RS-05 the record: the rows of a report, written and replaced on SQLite with the upgrade step's tables
 *  RS-06 the notice: its kind (failure, gate, success), sent only for the kinds Notify[] names, plain text in the
 *        kernel's mail model with every remote's state, and through the file transport
 *  RS-07 the wizard: settings from the form, a secret never written, the INI lines, the check; the deploy form; the
 *        deploy job's command line
 *  RS-08 the module: the views and policies, "Verify it's you" for saving, removing, deploying, approving and rolling
 *        back, never for the lists, Test connection or the preflight
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationRemoteDeployStagedTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    /** @var array[] */
    private $events = array();

    /** @var array[] the reports notify was called with */
    private $notified = array();

    /** @var array the mail settings changed by a test */
    private $mailBackup = null;

    protected function setUp(): void
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/expmigrationremotestaged-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir . '/source/migrations', 0775, true );
        $this->events = array();
        $this->notified = array();
        expMigrationRemoteProcess::setRunner( null );
    }

    protected function tearDown(): void
    {
        expMigrationRemoteProcess::setRunner( null );
        if ( $this->mailBackup !== null )
        {
            $ini = eZINI::instance();
            foreach ( $this->mailBackup as $name => $value )
                $ini->setVariable( 'MailSettings', $name, $value );
        }
        self::remove( $this->dir );
    }

    private static function remove( $path )
    {
        if ( is_dir( $path ) && !is_link( $path ) )
        {
            foreach ( scandir( $path ) as $name )
                if ( $name !== '.' && $name !== '..' )
                    self::remove( "$path/$name" );
            rmdir( $path );
        }
        else if ( file_exists( $path ) || is_link( $path ) )
            unlink( $path );
    }

    private function migration( $id, $content = "steps: []\n" )
    {
        file_put_contents( $this->dir . "/source/migrations/$id.yml", $content );
    }

    private function local()
    {
        $local = array();
        foreach ( glob( $this->dir . '/source/migrations/*.yml' ) as $file )
        {
            $rel = 'migrations/' . basename( $file );
            $local[] = array( 'id' => basename( $file, '.yml' ), 'source' => 'site', 'files' => array( '*' => $rel ), 'paths' => array( $rel => $file ),
                              'checksums' => array( '*' => expMigrationDefinition::checksumOf( file_get_contents( $file ) ) ) );
        }
        return $local;
    }

    private function destination( $name, $environment )
    {
        $path = $this->dir . '/' . $name;
        mkdir( "$path/bin/php", 0775, true );
        mkdir( "$path/migrations", 0775, true );
        mkdir( "$path/var", 0775, true );
        foreach ( array( 'expmigrationstatus', 'expmigrationmigrate', 'expmigrationrollback' ) as $script )
            copy( __DIR__ . '/fixtures/remotedeploy/fakemigrationcli.php', "$path/bin/php/$script.php" );
        return new expMigrationRemote( $name, array( 'Transport' => 'local', 'Path' => $path, 'Php' => PHP_BINARY, 'Environment' => $environment ) );
    }

    private function done( $name )
    {
        $file = $this->dir . "/$name/var/fake-state.json";
        return is_file( $file ) ? array_keys( json_decode( file_get_contents( $file ), true )['done'] ) : array();
    }

    private function deploy( array $remotes, array $options = array() )
    {
        $list = array();
        foreach ( $remotes as $remote )
            $list[$remote->id()] = $remote;
        $events =& $this->events;
        $notified =& $this->notified;
        return new expMigrationRemoteDeploy( $list, $options + array(
            'root' => $this->dir . '/source', 'work_dir' => $this->dir . '/work', 'local' => $this->local(), 'version' => '6.0.15', 'retries' => 0,
            'audit' => function ( $name, $data ) use ( &$events ) { $events[] = array( $name, $data ); },
            'notify' => function ( array $report ) use ( &$notified ) { $notified[] = $report; },
        ) );
    }

    /** @return expMigrationRemote[] dev, stage, prod1, prod2 */
    private function four()
    {
        return array( $this->destination( 'prod1', 'production' ), $this->destination( 'dev', 'dev' ), $this->destination( 'stage', 'stage' ),
                      $this->destination( 'prod2', 'production' ) );
    }

    /** RS-01 */
    public function testStages()
    {
        $remotes = array();
        foreach ( array( 'p1' => 'production', 'x' => 'lab', 'd' => 'Dev', 's' => 'stage', 'p2' => 'production', 'y' => 'zoo' ) as $id => $environment )
            $remotes[$id] = new expMigrationRemote( $id, array( 'Environment' => $environment ) );
        $this->assertSame( array( array( 'd' ), array( 's' ), array( 'p1', 'p2' ), array( 'x' ), array( 'y' ) ),
                           expMigrationRemoteDeploy::stagesOf( $remotes, expMigrationRemoteDeploy::STAGE_ORDER ) );
        $this->assertSame( array( array( 'p1', 'p2' ), array( 'd' ), array( 's' ), array( 'x' ), array( 'y' ) ),
                           expMigrationRemoteDeploy::stagesOf( $remotes, array( 'production', 'dev', 'stage' ) ) );
        $this->assertSame( array(), expMigrationRemoteDeploy::stagesOf( array(), expMigrationRemoteDeploy::STAGE_ORDER ) );
    }

    /** RS-02 */
    public function testStagedDeployWithGates()
    {
        $this->migration( '20261102100000_first' );
        $remotes = $this->four();
        $deploy = $this->deploy( $remotes, array( 'apply' => true, 'staged' => true ) );
        $report = $deploy->run();
        $this->assertSame( 'gated', $report['state'], json_encode( $report['errors'] ) );
        $this->assertSame( 0, $report['exit'] );
        $this->assertNull( $report['in_sync'] );
        $this->assertSame( array( array( 'dev' ), array( 'stage' ), array( 'prod1', 'prod2' ) ), $report['stages'] );
        $this->assertSame( 1, $report['next_stage'] );
        $this->assertSame( 'applied', $report['remotes']['dev']['state'] );
        foreach ( array( 'stage', 'prod1', 'prod2' ) as $name )
        {
            $this->assertSame( 'waiting', $report['remotes'][$name]['state'], $name );
            $this->assertSame( array(), $this->done( $name ), "$name is not touched before its gate" );
        }
        $this->assertSame( array( '20261102100000_first' ), $this->done( 'dev' ) );
        $this->assertSame( array( 'prepare', 'preflight', 'stage1-transfer', 'stage1-verify', 'stage1-switch' ), array_keys( $report['phases'] ) );
        $this->assertSame( 'gate', expMigrationDeployNotifier::kind( end( $this->notified ) ) );
        $actions = expMigrationRemoteDeploy::actions( $report );
        $this->assertTrue( $actions['continue'] );
        $this->assertSame( array( 'stage' ), $actions['next'] );
        $this->assertSame( array( 'dev' ), $actions['applied'] );

        $stored = expMigrationRemoteDeploy::load( $deploy->deployId(), $this->dir . '/work' );
        $this->assertSame( 'gated', $stored['state'] );
        $this->assertNull( expMigrationRemoteDeploy::load( 'not-an-id', $this->dir . '/work' ) );

        // the gate of stage 2, approved
        $next = $this->deploy( $remotes, array( 'apply' => true, 'deploy_id' => $deploy->deployId(), 'user' => 'approver' ) )->continueStaged( $stored );
        $this->assertSame( 'gated', $next['state'], json_encode( $next['errors'] ) );
        $this->assertSame( 2, $next['next_stage'] );
        $this->assertSame( 'applied', $next['remotes']['stage']['state'] );
        $this->assertSame( 'applied', $next['remotes']['dev']['state'], 'the first stage stays as it was' );
        $this->assertSame( array( array( 'stage' => 2, 'by' => 'approver' ) ), array_map( function ( $g ) { return array( 'stage' => $g['stage'], 'by' => $g['by'] ); }, $next['gates'] ) );
        $this->assertArrayHasKey( 'stage2-preflight', $next['phases'], 'the next stage is preflighted again' );

        $last = $this->deploy( $remotes, array( 'apply' => true, 'deploy_id' => $deploy->deployId() ) )
                     ->continueStaged( expMigrationRemoteDeploy::load( $deploy->deployId(), $this->dir . '/work' ) );
        $this->assertSame( 'deployed', $last['state'], json_encode( $last['errors'] ) );
        $this->assertTrue( $last['in_sync'] );
        $this->assertNull( $last['next_stage'] );
        foreach ( array( 'dev', 'stage', 'prod1', 'prod2' ) as $name )
            $this->assertSame( array( '20261102100000_first' ), $this->done( $name ), $name );
        $this->assertSame( 'caption-' . $deploy->deployId(), $last['remotes']['prod2']['caption'], 'one deploy id on every stage' );
        $this->assertSame( 'success', expMigrationDeployNotifier::kind( end( $this->notified ) ) );

        // no gate left
        $again = $this->deploy( $remotes, array( 'apply' => true, 'deploy_id' => $deploy->deployId() ) )->continueStaged( $last );
        $this->assertSame( 1, $again['exit'] );
        $this->assertStringContainsString( 'does not wait at a gate', $again['errors'][0] );
        $history = expMigrationRemoteDeploy::history( 5, $this->dir . '/work' );
        $this->assertSame( 3, $history[0]['stages'] );
        $this->assertSame( 'deployed', $history[0]['state'] );
    }

    /** RS-03 */
    public function testLaterStageFailureAndRollbackOfTheDeploy()
    {
        $this->migration( '20261102100000_first' );
        $remotes = array( $this->destination( 'dev', 'dev' ), $this->destination( 'prod1', 'production' ), $this->destination( 'prod2', 'production' ) );
        file_put_contents( $this->dir . '/prod2/migrations/20261102090000_breaks.yml', "FAIL\n" );
        $deploy = $this->deploy( $remotes, array( 'apply' => true, 'staged' => true ) );
        $first = $deploy->run();
        $this->assertSame( 'gated', $first['state'], json_encode( $first['errors'] ) );

        $failed = $this->deploy( $remotes, array( 'apply' => true, 'deploy_id' => $deploy->deployId() ) )->continueStaged( $first );
        $this->assertSame( 'rolled-back', $failed['state'] );
        $this->assertFalse( $failed['in_sync'], 'the first stage stays deployed' );
        $this->assertStringContainsString( 'earlier stages stay deployed', implode( "\n", $failed['warnings'] ) );
        $this->assertSame( 'applied', $failed['remotes']['dev']['state'] );
        $this->assertSame( 'rolled-back', $failed['remotes']['prod1']['state'] );
        $this->assertSame( 'restored', $failed['remotes']['prod2']['state'] );
        $this->assertSame( array(), $this->done( 'prod1' ) );
        $this->assertSame( 'failure', expMigrationDeployNotifier::kind( end( $this->notified ) ) );

        $back = $this->deploy( $remotes, array( 'apply' => true, 'deploy_id' => $deploy->deployId(), 'user' => 'ops' ) )->rollbackDeploy( $failed );
        $this->assertSame( 'rolled-back', $back['state'], json_encode( $back['errors'] ) );
        $this->assertTrue( $back['in_sync'] );
        $this->assertSame( 'rolled-back', $back['remotes']['dev']['state'] );
        $this->assertSame( array(), $this->done( 'dev' ) );
        $this->assertSame( 'ops', $back['rollback']['by'] );
        $this->assertContains( 'system.migration.deploy.rollback', array_column( $this->events, 0 ) );
        $nothing = $this->deploy( $remotes, array( 'apply' => true, 'deploy_id' => $deploy->deployId() ) )->rollbackDeploy( $back );
        $this->assertSame( 1, $nothing['exit'] );
        $this->assertStringContainsString( 'nothing to take back', $nothing['errors'][0] );

        // a plain deploy, rolled back afterwards
        $this->migration( '20261102110000_second' );
        $remotes = array( $this->destination( 'a', 'dev' ), $this->destination( 'b', 'stage' ) );
        $plain = $this->deploy( $remotes, array( 'apply' => true ) );
        $done = $plain->run();
        $this->assertSame( 'deployed', $done['state'], json_encode( $done['errors'] ) );
        $undone = $this->deploy( $remotes, array( 'apply' => true, 'deploy_id' => $plain->deployId() ) )->rollbackDeploy( $done );
        $this->assertSame( 'rolled-back', $undone['state'] );
        $this->assertSame( 0, $undone['exit'], 'a rollback someone asked for that took every remote back succeeded' );
        $this->assertSame( 'success', expMigrationDeployNotifier::kind( $undone ) );
        $this->assertSame( array(), $this->done( 'a' ) );
        $this->assertSame( array(), $this->done( 'b' ) );
        $wrong = $this->deploy( $remotes, array( 'apply' => true ) )->rollbackDeploy( $done );
        $this->assertStringContainsString( 'not the report', $wrong['errors'][0], 'a rollback needs the deploy\'s own id' );
    }

    /** RS-04 */
    public function testLiveProgress()
    {
        $this->migration( '20261102100000_first' );
        $file = $this->dir . '/progress.jsonl';
        touch( $file );
        $report = $this->deploy( array( $this->destination( 'dev', 'dev' ), $this->destination( 'prod', 'production' ) ),
                                 array( 'apply' => true, 'progress' => expMigrationProgress::writer( $file ) ) )->run();
        $this->assertSame( 'deployed', $report['state'] );
        $read = expMigrationProgress::read( $file, 0 );
        $names = array_unique( array_column( $read['events'], 'event' ) );
        $this->assertSame( array( 'run', 'phase', 'host', 'step-done', 'end' ), array_values( $names ) );
        $half = expMigrationProgress::fold( array_slice( $read['events'], 0, 8 ) );
        $this->assertSame( 'deploy', $half['mode'] );
        $this->assertFalse( $half['ended'] );
        $this->assertArrayHasKey( 'dev', $half['hosts'] );
        $state = expMigrationProgress::fold( $read['events'] );
        $this->assertTrue( $state['ended'] );
        $this->assertSame( 0, $state['exit'] );
        $this->assertSame( 100, $state['percent'] );
        $this->assertSame( 5, $state['done'] );
        $this->assertSame( array( 'state' => 'applied', 'phase' => '', 'problems' => 0 ), $state['hosts']['prod'] );
        $this->assertSame( array(), expMigrationProgress::fold( array( array( 'event' => 'host', 'remote' => '../x', 'state' => 'a' ) ) )['hosts'], 'a remote id is checked' );
    }

    /** RS-05 */
    public function testRecord()
    {
        $report = array( 'schema' => expMigrationRemoteDeploy::REPORT, 'deploy_id' => '20261102-120000-a1b2c3', 'mode' => 'apply', 'state' => 'gated', 'in_sync' => null,
                         'started_at' => '2026-11-02T12:00:00+00:00', 'finished_at' => '2026-11-02T12:01:00+00:00', 'user' => 'admin',
                         'stages' => array( array( 'dev' ), array( 'prod' ) ), 'next_stage' => 1, 'errors' => array(),
                         'remotes' => array( 'dev' => array( 'remote' => array( 'environment' => 'dev', 'transport' => 'rsync', 'address' => 'rsync://d:22/srv' ),
                                                             'state' => 'applied', 'applied' => array( 'm1', 'm2' ), 'caption' => 'c1', 'problems' => array() ),
                                             'prod' => array( 'remote' => array( 'environment' => 'production', 'transport' => 'ssh', 'address' => 'ssh://p:22/srv' ),
                                                              'state' => 'waiting', 'applied' => array(), 'caption' => '', 'problems' => array( str_repeat( 'x', 1200 ) ) ) ) );
        $rows = expMigrationDeployRecord::rows( $report );
        $this->assertSame( array( 'deploy_id' => '20261102-120000-a1b2c3', 'mode' => 'apply', 'state' => 'gated', 'in_sync' => -1, 'stages' => 2, 'next_stage' => 2,
                                  'run_by' => 'admin', 'started_at' => strtotime( '2026-11-02T12:00:00+00:00' ), 'finished_at' => strtotime( '2026-11-02T12:01:00+00:00' ),
                                  'message' => '' ), $rows['deploy'] );
        $this->assertSame( array( 1, 2 ), array_column( $rows['hosts'], 'stage' ) );
        $this->assertSame( 2, $rows['hosts'][0]['applied'] );
        $this->assertSame( 1000, mb_strlen( $rows['hosts'][1]['message'] ) );

        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $root = dirname( __DIR__, 5 );
        $db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->dir . '/kernel.sqlite',
                                      'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                      'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                      'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                      'use_persistent_connection' => false, 'show_errors' => false ) );
        expKernelTableSchema::reset();
        $this->assertFalse( expMigrationDeployRecord::exists( $db ) );
        $this->assertFalse( expMigrationDeployRecord::write( $db, $report ), 'nothing is written without the tables' );
        $this->assertSame( array(), expMigrationDeployRecord::deploys( $db ) );
        $context = new expUpgradeContext( $db, true, array(), $root . '/share/db_schema.dba' );
        $step = new expUpgradeStepMigrationDeployTables();
        $this->assertSame( '6.0.15-migration-deploy-tables', $step->id() );
        $this->assertSame( expUpgradeStep::MISSING, $step->detect( $context )['effect'] );
        $this->assertSame( array( 'created table exp_migration_deploy', 'created table exp_migration_deploy_host' ), $step->apply( $context ) );
        expKernelTableSchema::reset();
        $this->assertSame( expUpgradeStep::PRESENT, $step->detect( $context )['effect'] );
        $this->assertTrue( expMigrationDeployRecord::exists( $db ) );

        $this->assertTrue( expMigrationDeployRecord::write( $db, $report ) );
        $report['state'] = 'deployed';
        $report['in_sync'] = true;
        $report['next_stage'] = null;
        $report['remotes']['prod']['state'] = 'applied';
        $this->assertTrue( expMigrationDeployRecord::write( $db, $report ), 'a second write replaces the rows' );
        $this->assertTrue( expMigrationDeployRecord::write( $db, array( 'deploy_id' => '20261101-120000-000000', 'started_at' => '2026-11-01T12:00:00+00:00', 'remotes' => array() ) + $report ) );
        $deploys = expMigrationDeployRecord::deploys( $db );
        $this->assertSame( array( '20261102-120000-a1b2c3', '20261101-120000-000000' ), array_column( $deploys, 'deploy_id' ), 'newest first' );
        $this->assertSame( array( 'deployed', 1, 0 ), array( $deploys[0]['state'], $deploys[0]['in_sync'], $deploys[0]['next_stage'] ) );
        $hosts = expMigrationDeployRecord::hosts( $db, '20261102-120000-a1b2c3' );
        $this->assertSame( array( 'dev', 'prod' ), array_column( $hosts, 'remote_id' ) );
        $this->assertSame( 'applied', $hosts[1]['state'] );
        $this->assertCount( 1, expMigrationDeployRecord::hosts( $db, null, 'prod' ) );
        $this->expectException( InvalidArgumentException::class );
        expMigrationDeployRecord::write( $db, array( 'deploy_id' => 'x; DROP' ) );
    }

    /** RS-06 */
    public function testNotice()
    {
        $report = array( 'deploy_id' => '20261102-120000-a1b2c3', 'mode' => 'apply', 'state' => 'rolled-back', 'in_sync' => true, 'user' => 'admin',
                         'errors' => array( 'prod: the migration m1 failed' ), 'stages' => array(), 'next_stage' => null,
                         'remotes' => array( 'dev' => array( 'remote' => array( 'environment' => 'dev' ), 'state' => 'rolled-back', 'problems' => array() ),
                                             'prod' => array( 'remote' => array( 'environment' => 'production' ), 'state' => 'restored', 'problems' => array( 'the migration m1 failed' ) ) ) );
        $this->assertSame( 'failure', expMigrationDeployNotifier::kind( $report ) );
        $this->assertSame( 'gate', expMigrationDeployNotifier::kind( array( 'state' => 'gated' ) ) );
        $this->assertSame( 'failure', expMigrationDeployNotifier::kind( array( 'state' => 'gated', 'errors' => array( 'the preflight of stage 2 failed' ) ) ) );
        $this->assertSame( 'success', expMigrationDeployNotifier::kind( array( 'state' => 'deployed' ) ) );
        $this->assertSame( '', expMigrationDeployNotifier::kind( array( 'state' => 'planned' ) ) );

        $mails = array();
        $notifier = function ( array $notify ) use ( &$mails ) {
            return new expMigrationDeployNotifier( array( 'email' => 'ops@example.com', 'notify' => $notify, 'site' => 'Demo', 'admin_url' => 'https://admin.example.com/',
                                                          'mail' => function ( eZMail $mail ) use ( &$mails ) { $mails[] = $mail; return true; } ) );
        };
        $this->assertNull( $notifier( array( 'gate' ) )->notify( $report )['mail'], 'not a kind Notify[] names' );
        $this->assertCount( 0, $mails );
        $this->assertTrue( $notifier( array( 'failure', 'gate' ) )->notify( $report )['mail'] );
        $this->assertCount( 1, $mails );
        $mail = $mails[0];
        $this->assertSame( 'admin', $mail->category() );
        $this->assertSame( 'The deploy 20261102-120000-a1b2c3 failed on Demo', $mail->subject() );
        $body = (string)$mail->body();
        $this->assertStringNotContainsString( '<', $body, 'plain text, no HTML' );
        foreach ( array( 'Deploy: 20261102-120000-a1b2c3', 'Result: rolled back', '- dev (dev): rolled-back', '- prod (production): restored',
                         'the migration m1 failed', 'https://admin.example.com/migration/deployment/20261102-120000-a1b2c3',
                         'because migration.ini [RemoteSettings] Notify[] asks for it' ) as $line )
            $this->assertStringContainsString( $line, $body );
        list( , $fallback ) = expMigrationDeployNotifier::mail( $report, 'Demo', 'https://admin.example.com/migration/deployment/x' );
        $this->assertStringContainsString( 'Result: rolled back', $fallback );
        $this->assertStringContainsString( "Errors:\n- prod: the migration m1 failed", $fallback );
        $this->assertSame( '', expMigrationDeployNotifier::pageURL( 'x', '' ) );
        $bad = new expMigrationDeployNotifier( array( 'email' => 'not an address', 'notify' => array( 'failure' ), 'site' => 'Demo', 'admin_url' => '',
                                                      'mail' => function () { return true; } ) );
        $this->assertFalse( $bad->notify( $report )['mail'] );

        // the file transport: the notice is written to a folder of the test, never sent
        $ini = eZINI::instance();
        $this->mailBackup = array();
        foreach ( array( 'Transport', 'FileTransportDirectory', 'DebugSending' ) as $name )
            $this->mailBackup[$name] = $ini->hasVariable( 'MailSettings', $name ) ? $ini->variable( 'MailSettings', $name ) : '';
        $ini->setVariable( 'MailSettings', 'Transport', 'file' );
        $ini->setVariable( 'MailSettings', 'FileTransportDirectory', $this->dir . '/mail' );
        $ini->setVariable( 'MailSettings', 'DebugSending', 'disabled' );
        if ( trim( (string)$ini->variable( 'MailSettings', 'Transport' ) ) !== 'file' )
            $this->fail( 'The mail transport is not the file transport: the test refuses to send.' );
        mkdir( $this->dir . '/mail', 0775, true );
        $file = new expMigrationDeployNotifier( array( 'email' => 'ops@example.com', 'notify' => array( 'failure' ), 'site' => 'Demo', 'admin_url' => 'https://admin.example.com' ) );
        $sent = $file->notify( $report );
        if ( !$sent['mail'] )
            $this->markTestSkipped( 'The file transport did not take the notice here: ' . implode( '; ', $sent['problems'] ) );
        $written = glob( $this->dir . '/mail/*' );
        $this->assertCount( 1, $written );
        $this->assertStringContainsString( 'The deploy 20261102-120000-a1b2c3 failed on Demo', (string)file_get_contents( $written[0] ) );
    }

    /** RS-07 */
    public function testWizardFormsAndJob()
    {
        $settings = expMigrationRemoteAdmin::settingsFromPost( array(
            'Remote' => array( 'Environment' => 'production', 'Transport' => 'RSYNC', 'Host' => 'node1.example.com', 'Port' => '', 'User' => 'deploy', 'Path' => '/var/www/site',
                               'Php' => 'php', 'HostKeyFingerprint' => '', 'AllowRootUser' => 'enabled', 'Password' => 'hunter2' ),
            'RemoteGroups' => 'production, eu ,production', 'RemoteCredentialKind' => 'sshkey', 'RemoteCredentialName' => 'deploy_production1', 'RemoteCredentialEnv' => '' ) );
        $this->assertSame( 'rsync', $settings['Transport'] );
        $this->assertSame( array( 'production', 'eu' ), $settings['Groups'] );
        $this->assertSame( 'sshkey:deploy_production1', $settings['Credential'] );
        $this->assertArrayNotHasKey( 'Password', $settings, 'a secret from the form is never kept' );
        $ini = expMigrationRemoteAdmin::iniSettings( $settings );
        $this->assertSame( array( 'Environment' => 'production', 'Transport' => 'rsync', 'Host' => 'node1.example.com', 'User' => 'deploy', 'Path' => '/var/www/site',
                                  'Php' => 'php', 'Credential' => 'sshkey:deploy_production1', 'Groups' => array( 'production', 'eu' ), 'AllowRootUser' => 'enabled' ), $ini );
        $env = expMigrationRemoteAdmin::settingsFromPost( array( 'RemoteCredentialKind' => 'password', 'RemoteCredentialName' => 'x', 'RemoteCredentialEnv' => 'FTP_PASS' ) );
        $this->assertSame( 'password:env:FTP_PASS', $env['Credential'] );
        $this->assertSame( array( 'kind' => 'password', 'name' => '', 'env' => 'FTP_PASS' ), expMigrationRemoteAdmin::credentialChoice( $env['Credential'] ) );
        $this->assertSame( array( 'kind' => 'sshkey', 'name' => 'deploy_production1', 'env' => '' ), expMigrationRemoteAdmin::credentialChoice( $settings['Credential'] ) );
        $this->assertSame( 'none', expMigrationRemoteAdmin::settingsFromPost( array( 'RemoteCredentialKind' => 'token' ) )['Credential'] );

        $lookup = function ( $name ) { return $name === 'deploy_production1' ? "-----BEGIN OPENSSH PRIVATE KEY-----\nx\n-----END OPENSSH PRIVATE KEY-----\n" : null; };
        $this->assertSame( array( 'errors' => array(), 'warnings' => array() ), expMigrationRemoteAdmin::check( 'production1', $settings, array( 'stage' ), true, $lookup ) );
        $check = expMigrationRemoteAdmin::check( 'stage', $settings, array( 'stage' ), true, $lookup );
        $this->assertStringContainsString( 'exists already', $check['errors'][0] );
        $check = expMigrationRemoteAdmin::check( 'p', array( 'Port' => 'x', 'Groups' => array( 'all' ), 'Path' => 'relative' ) + $settings, array(), true, $lookup );
        $this->assertGreaterThanOrEqual( 3, count( $check['errors'] ) );
        $check = expMigrationRemoteAdmin::check( 'p', array( 'Credential' => 'sshkey:missing' ) + $settings, array(), true, $lookup );
        $this->assertSame( array(), $check['errors'] );
        $this->assertStringContainsString( 'cannot be read now', $check['warnings'][0] );

        // the INI lines, in a scope of the test
        mkdir( $this->dir . '/settings/override', 0775, true );
        $scope = new expIniScope( 'test', expIniScope::KIND_GLOBAL, 'settings/override', $this->dir . '/', 'Test' );
        $editor = expMigrationRemoteAdmin::editor( 'production1', $settings, $scope );
        $content = $editor->content();
        foreach ( array( '[Remote_production1]', 'Transport=rsync', 'Credential=sshkey:deploy_production1', 'Groups[]', 'Groups[]=production', 'Groups[]=eu',
                         'AllowRootUser=enabled', 'RemoteList[]=production1' ) as $line )
            $this->assertStringContainsString( $line, $content );
        $this->assertStringNotContainsString( 'hunter2', $content );
        $this->assertStringNotContainsString( 'Port=', $content, 'an empty setting is left out' );

        // the deploy form
        $remotes = array( 'dev' => new expMigrationRemote( 'dev', array( 'Transport' => 'local', 'Path' => '/srv/dev', 'Groups' => array( 'test' ) ) ),
                          'ftp1' => new expMigrationRemote( 'ftp1', array( 'Transport' => 'ftp', 'Host' => 'h', 'Path' => '/site', 'Credential' => 'password:p', 'Groups' => array( 'test' ) ) ),
                          'prod' => new expMigrationRemote( 'prod', array( 'Transport' => 'local', 'Path' => '/srv/prod' ) ) );
        $this->assertSame( array( 'test' => array( 'dev', 'ftp1' ) ), expMigrationRemoteAdmin::groups( $remotes ) );
        $errors = array();
        $options = expMigrationRemoteAdmin::deployOptions( array( 'DeployRemote' => array( 'prod' ), 'DeployGroup' => array( 'test' ), 'DeployMode' => 'apply' ), $remotes, false, $errors );
        $this->assertSame( array( 'dev', 'ftp1', 'prod' ), $options['remotes'], 'in the order of RemoteList[]' );
        $this->assertSame( array( 'confirm', 'ftp' ), $errors );
        $errors = array();
        $options = expMigrationRemoteAdmin::deployOptions( array( 'DeployRemote' => array( 'prod', 'dev' ), 'DeployMode' => 'apply', 'DeployStaged' => '1', 'ConfirmDeploy' => '1',
                                                                 'DeployOnly' => 'm1, m2' ), $remotes, false, $errors );
        $this->assertSame( array(), $errors );
        $this->assertSame( array( true, false, true, array( 'm1', 'm2' ) ), array( $options['apply'], $options['transfer-only'], $options['staged'], $options['only'] ) );
        $errors = array();
        $preflight = expMigrationRemoteAdmin::deployOptions( array( 'DeployRemote' => array( 'ftp1' ), 'DeployStaged' => '1' ), $remotes, true, $errors );
        $this->assertSame( array(), $errors, 'a preflight needs no confirmation' );
        $this->assertSame( array( false, false, false ), array( $preflight['apply'], $preflight['transfer-only'], $preflight['staged'] ) );
        $errors = array();
        expMigrationRemoteAdmin::deployOptions( array( 'DeployRemote' => array( 'gone' ), 'DeployOnly' => '-x' ), $remotes, true, $errors );
        $this->assertSame( array( 'unknown', 'remotes', 'only' ), $errors );

        // the deploy job's command line
        $args = expMigrationJob::arguments( 'deploy', '20261102-120000-a1b2c3', array( 'remotes' => array( 'dev', 'prod' ), 'only' => array( 'm1' ), 'apply' => true, 'staged' => true ),
                                            'admin', 'admin', '/p/x.progress' );
        $this->assertSame( array( 'bin/php/expmigrationdeploy.php', '--remotes=dev,prod', '--only=m1', '--staged', '--run-id=20261102-120000-a1b2c3', '--apply', '--format=json',
                                  '--login=admin', '--siteaccess=admin', '--progress=/p/x.progress' ), $args );
        $dry = expMigrationJob::arguments( 'deploy', '20261102-120000-a1b2c3', array( 'remotes' => 'dev' ), 'admin', '', '/p/x.progress', true );
        $this->assertNotContains( '--apply', $dry, 'without apply a deploy job is a preflight' );
        $this->assertContains( '--allow-root-user', $dry );
        $this->assertSame( array( 'bin/php/expmigrationdeploy.php', '--continue=20261101-000000-abcdef', '--apply', '--format=json', '--login=admin', '--progress=/p/x.progress' ),
                           expMigrationJob::arguments( 'deploy', '20261102-120000-a1b2c3', array( 'continue' => '20261101-000000-abcdef' ), 'admin', '', '/p/x.progress' ) );
        foreach ( array( array( 'remotes' => array() ), array( 'remotes' => '-oProxy' ), array( 'rollback' => 'x y' ),
                         array( 'continue' => '20261101-000000-abcdef', 'rollback' => '20261101-000000-abcdef' ) ) as $bad )
        {
            try
            {
                expMigrationJob::arguments( 'deploy', '20261102-120000-a1b2c3', $bad, 'admin', '', '/p/x.progress' );
                $this->fail( 'refused: ' . json_encode( $bad ) );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->addToAssertionCount( 1 );
            }
        }
    }

    /** RS-08 */
    public function testTheModule()
    {
        $root = dirname( __DIR__, 5 );
        $Module = null;
        include $root . '/kernel/migration/module.php';
        foreach ( array( 'remotes' => 'read', 'remote' => 'remotes', 'deploy' => 'read', 'deployment' => 'read' ) as $view => $function )
        {
            $this->assertArrayHasKey( $view, $ViewList );
            $this->assertSame( array( $function ), $ViewList[$view]['functions'], $view );
            $this->assertFileExists( $root . '/kernel/migration/' . $ViewList[$view]['script'] );
            foreach ( array( 'admin', 'admin4' ) as $design )
                $this->assertFileExists( "$root/design/$design/templates/migration/$view.tpl" );
        }
        $this->assertArrayHasKey( 'remotes', $FunctionList );
        $this->assertArrayHasKey( 'deploy', $FunctionList );
        $site = (string)file_get_contents( $root . '/settings/site.ini' );
        foreach ( array( 'migration/remote SaveButton', 'migration/remote RemoveButton', 'migration/deploy DeployButton', 'migration/deployment ApproveButton',
                         'migration/deployment RollbackButton' ) as $rule )
            $this->assertStringContainsString( "\nRules[]=$rule\n", $site, $rule );
        foreach ( array( 'migration/remotes', 'migration/deploy PreflightButton', 'migration/remote TestButton', 'migration/deployment' . "\n" ) as $never )
            $this->assertStringNotContainsString( "\nRules[]=$never", $site, $never );
        // the decision the kernel takes on the rules of [ReauthGroup_system]
        preg_match( '/\n\[ReauthGroup_system\]\n(.*?)(\n\[|$)/s', $site, $m );
        $rules = array();
        foreach ( explode( "\n", $m[1] ) as $line )
            if ( strpos( $line, 'Rules[]=' ) === 0 )
                $rules[] = substr( $line, 8 );
        $settings = array( 'enabled' => true, 'groups' => array( 'system' => array( 'enabled' => true, 'rules' => $rules, 'user_groups' => array() ) ) );
        $asks = function ( $view, array $post ) use ( $settings ) {
            return expReauth::matchGroup( $settings, 'migration', $view, 'POST', $post ) !== null;
        };
        $this->assertTrue( $asks( 'remote', array( 'SaveButton' => '1' ) ), 'saving a remote' );
        $this->assertTrue( $asks( 'remote', array( 'RemoveButton' => '1' ) ), 'removing a remote' );
        $this->assertTrue( $asks( 'deploy', array( 'DeployButton' => '1' ) ), 'a deploy' );
        $this->assertTrue( $asks( 'deployment', array( 'ApproveButton' => '1' ) ), 'approving a gate' );
        $this->assertTrue( $asks( 'deployment', array( 'RollbackButton' => '1' ) ), 'rolling a deploy back' );
        $this->assertFalse( $asks( 'deploy', array( 'PreflightButton' => '1' ) ), 'a preflight' );
        $this->assertFalse( $asks( 'remote', array( 'ReviewButton' => '1' ) ), 'a review' );
        $this->assertFalse( $asks( 'remote', array( 'TestButton' => '1' ) ), 'Test connection in the wizard' );
        $this->assertFalse( $asks( 'remotes', array( 'TestButton' => '1' ) ), 'Test connection in the list' );
        $this->assertSame( 'ok', expMigrationRemoteAdmin::stateKind( 'deployed' ) );
        $this->assertSame( 'warn', expMigrationRemoteAdmin::stateKind( 'gated' ) );
        $this->assertSame( 'bad', expMigrationRemoteAdmin::stateKind( 'out-of-sync' ) );
        $decorated = expMigrationRemoteAdmin::decorate( array( 'state' => 'gated', 'stages' => array( array( 'a' ), array( 'b' ) ), 'next_stage' => 1, 'phases' => array( 'prepare' => array( 'ok' => true, 'duration_ms' => 3 ) ),
                                                               'remotes' => array( 'a' => array( 'remote' => array(), 'state' => 'applied', 'send' => array( 'f' ), 'applied' => array( 'm' ), 'pending' => array(),
                                                                                                 'problems' => array(), 'fixes' => array(), 'version' => '6.0.15', 'caption' => 'c' ) ) ) );
        $this->assertSame( 2, $decorated['next_stage_number'] );
        $this->assertSame( 1, $decorated['remote_list'][0]['stage'] );
        $this->assertSame( 'prepare', $decorated['phase_list'][0]['name'] );
    }
}
