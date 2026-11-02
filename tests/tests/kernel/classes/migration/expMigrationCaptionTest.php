<?php
/**
 * Captions, restore on failure, re-caption, commit, tag and push of migration runs, without a database and without
 * git: the run context of expMigrationRunnerTest, a caption store in a folder under var/tmp, a rollback that records
 * what it was asked and git answered by a closure (expMigrationCaptionFakes).
 *
 *  MC-01 a run that succeeds: the caption migration-pre before the first migration, migration-post after the run,
 *        both labelled with the run and its migrations; caption_pre in every record and run row; commit, tag and push
 *        in the last run row; the audit events system.migration.caption and system.migration.push
 *  MC-02 a run that fails: the caption migration-failure, then the pre caption restored (no safety caption of its
 *        own, under the run's lock, the parts of RestoreParts); the migrations applied before the failure are
 *        "restored", the failed one recorded again; system.migration.rollback; the failed run is committed too
 *  MC-03 no caption possible (decision 3a): the run goes on with a warning; a failure is then not restored, the
 *        report lists the steps to restore by hand
 *  MC-04 Oracle (decision 5d): a failure is not restored, the run stops with the exact steps (impdp with the account
 *        and service, never the password); expUpgradeRollback refuses its database part with the same steps
 *  MC-05 --no-restore and RestoreOnFailure=disabled leave the failed state; --no-caption takes no caption at all
 *  MC-06 code that is not committed (decision 7a): --apply refused (exit 2, nothing run, no caption) while
 *        settings/, extension/ or design/ have changes; --include-uncommitted keeps a patch and the new files in the
 *        caption (uncommitted.json, checksums.json written again); nested checkouts are checked on their own; a dry
 *        run warns
 *  MC-07 git status -z parsed: changes staged or not, renames, new files
 *  MC-08 reuse: an unchanged installation reuses the newest migration caption (no new one, audited as reuse); a
 *        change of the database, var/ storage or settings/override takes a new one; never with uncommitted code
 *  MC-09 the repository: tag names and their rules, the commit message, only the run's files committed, a tag that
 *        exists is never moved, push failed = pending, no remote, Commit/Push/Tag disabled, another branch, a
 *        checkout of another system user, no git_manager
 *  MC-10 settings: migration.ini [CaptionSettings] and [RepositorySettings] with the options of a run over them;
 *        the caption kinds of expUpgradeCaption; the cronjob part migrationpush
 *  MC-11 expMigrationRollback: a caption restored only with expert and the typed confirmation
 *  MC-12 expUpgradeBackup::runWithEnv() drains stdout and stderr together (mongorestore's progress blocked it)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationCaptionFakes.php';

class expMigrationCaptionTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private static $root;

    /** @var string a folder of the test under var/tmp */
    private $dir;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    protected function setUp(): void
    {
        $this->dir = self::$root . '/var/tmp/migration-caption-test-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
        mkdir( $this->dir . '/site/migrations', 0775, true );
        mkdir( $this->dir . '/store', 0775, true );
    }

    protected function tearDown(): void
    {
        expUpgradeLock::release();
        expMigrationLock::installation( $this->dir . '/upgrade.lock' )->release();
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->dir, FilesystemIterator::SKIP_DOTS ),
                                                   RecursiveIteratorIterator::CHILD_FIRST );
        foreach ( $iterator as $item )
            $item->isDir() && !$item->isLink() ? rmdir( $item->getPathname() ) : unlink( $item->getPathname() );
        rmdir( $this->dir );
    }

    // ---- helpers -------------------------------------------------------------------------------------------------

    private function context( array $options = array() )
    {
        return new expMigrationCaptionTestContext( true, $options + array( 'lock-file' => $this->dir . '/upgrade.lock', 'root' => $this->dir . '/site',
                                                                           'storage-dir' => '', 'run-id' => '20261031-120000-abcdef' ) );
    }

    /** @var int the caption stores made */
    private $stores = 0;

    /** A caption fake with a store of its own (its captions are cap1, cap2 ...). */
    private function caption( $context, array $settings = array() )
    {
        $store = $this->dir . '/store/' . ( ++$this->stores );
        mkdir( $store, 0775, true );
        return new expMigrationCaptionFake( $context, $store, $settings );
    }

    /** A repository whose git commits, tags and pushes; $answers: subcommand => result overrides. */
    private function repository( array $settings = array(), array $answers = array() )
    {
        $repo = new expMigrationRepositoryFake( $this->dir . '/site', $settings );
        $repo->gitFn = function ( $path, array $args ) use ( $answers ) {
            while ( $args && $args[0] === '-c' )
                $args = array_slice( $args, 2 );
            $key = $args[0] . ( isset( $args[1] ) ? ' ' . $args[1] : '' );
            foreach ( array( $key, $args[0] ) as $k )
                if ( isset( $answers[$k] ) )
                    return $answers[$k];
            switch ( $key )
            {
                case 'rev-parse --abbrev-ref':
                    return array( 'ok' => true, 'exit' => 0, 'output' => "main\n" );
                case 'rev-parse --verify':
                    if ( isset( $args[2] ) && $args[2] === '--quiet' )
                        return array( 'ok' => false, 'exit' => 1, 'output' => '' );
                    return array( 'ok' => true, 'exit' => 0, 'output' => str_repeat( 'c', 40 ) . "\n" );
                case 'diff --cached':
                    return array( 'ok' => false, 'exit' => 1, 'output' => '' );
                case 'remote':
                    return array( 'ok' => true, 'exit' => 0, 'output' => "origin\n" );
                case 'config user.email':
                    return array( 'ok' => true, 'exit' => 0, 'output' => "ops@example.com\n" );
            }
            return array( 'ok' => true, 'exit' => 0, 'output' => '' );
        };
        // the run's two files, as the status mirror writes them
        @mkdir( $this->dir . '/site/.git', 0775, true );
        @mkdir( $this->dir . '/site/migrations/status', 0775, true );
        @mkdir( $this->dir . '/site/migrations/runs', 0775, true );
        file_put_contents( $this->dir . '/site/migrations/status/staging.json', "{}\n" );
        file_put_contents( $this->dir . '/site/migrations/runs/20261031-120000-abcdef.log', "log\n" );
        return $repo;
    }

    private function runner( $context, $caption, $repository = null )
    {
        return new expMigrationRunner( $context, array( 'flow' => new expMigrationRunFlow( $context, $caption, $repository ) ) );
    }

    private function migrations( $failSecond = false )
    {
        $up = function ( $context ) {
            $context->calls[] = 'up';
            return array();
        };
        $fail = function ( $context ) {
            $context->calls[] = 'up';
            throw new RuntimeException( 'failed on purpose' );
        };
        return array( new expMigrationRunnerFake( 'm1', array( 'up' => $up ) ), new expMigrationRunnerFake( 'm2', array( 'up' => $failSecond ? $fail : $up ) ),
                      new expMigrationRunnerFake( 'm3', array( 'up' => $up ) ) );
    }

    private function auditNames( $context )
    {
        return array_map( function ( $a ) { return $a[0] . '/' . $a[1]['verb']; }, $context->audits );
    }

    // ---- MC-01 -----------------------------------------------------------------------------------------------------

    public function testSuccessfulRunTakesCaptionsAndCommits()
    {
        $context = $this->context();
        $caption = $this->caption( $context );
        $repo = $this->repository();
        $report = $this->runner( $context, $caption, $repo )->run( $this->migrations() );

        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( 3, $report['summary']['applied'] );
        // pre before the first migration (and before the run row), post after the run's record
        $calls = array_values( array_filter( $context->calls, function ( $c ) { return in_array( $c, array( 'caption-migration-pre', 'up', 'write-run', 'caption-migration-post' ), true ); } ) );
        $this->assertSame( array( 'caption-migration-pre', 'write-run', 'up', 'up', 'up', 'write-run', 'caption-migration-post', 'write-run' ), $calls );
        $this->assertSame( 'migration run 20261031-120000-abcdef in staging pre: 3 migration(s) m1, m2, m3', $caption->taken[0][1] );
        $this->assertSame( 'migration run 20261031-120000-abcdef in staging post: 3 migration(s) m1, m2, m3', $caption->taken[1][1] );

        foreach ( array( 'm1', 'm2', 'm3' ) as $id )
        {
            $this->assertSame( 'cap1', $context->records["site\n$id"]['caption_id'], "$id: the caption before it" );
            $this->assertSame( str_repeat( 'c', 40 ), $context->records["site\n$id"]['revision'], "$id: the commit of the run" );
        }
        foreach ( $context->runs as $run )
            $this->assertSame( 'cap1', $run['caption_pre'] );
        $last = end( $context->runs );
        $this->assertSame( 'done', $last['state'] );
        $this->assertSame( 'cap2', $last['caption_post'] );
        $this->assertSame( str_repeat( 'c', 40 ), $last['commit_id'] );
        $this->assertSame( 'migrations/staging/20261031-120000-abcdef', $last['tag_name'] );
        $this->assertSame( 'pushed', $last['push_state'] );
        $this->assertSame( array( 'id' => 'cap1', 'ok' => true, 'reused' => false, 'source' => 'exp:upgrade', 'kind' => 'migration-pre', 'messages' => array() ),
                           $report['captions']['pre'] );
        $this->assertSame( 'cap2', $report['captions']['post']['id'] );
        $this->assertSame( 'pushed', $report['repository']['push_state'] );

        $audits = $this->auditNames( $context );
        $this->assertContains( 'system.migration.caption/take', $audits );
        $this->assertSame( 2, count( array_keys( $audits, 'system.migration.caption/take' ) ) );
        $this->assertContains( 'system.migration.push/push', $audits );
        $caption0 = $context->audits[array_search( 'system.migration.caption/take', $audits )][1];
        $this->assertSame( array( 'type' => 'caption', 'id' => 'cap1' ), $caption0['object'] );
        $this->assertSame( 'migration-pre', $caption0['x']['kind'] );
        $this->assertSame( '20261031-120000-abcdef', $caption0['x']['run_id'] );
        $this->assertFileExists( $this->dir . '/store/1/cap1/migration/marker.json' );

        // only the run's two files, committed by path; the tag on the commit; branch and tag pushed
        $this->assertSame( array( 'rev-parse', 'add', 'diff', 'config', 'commit', 'rev-parse', 'rev-parse', 'config', 'tag', 'remote', 'push' ), $repo->commands() );
        $commit = $repo->gitCalls[4];
        $this->assertSame( array( '--', 'migrations/status/staging.json', 'migrations/runs/20261031-120000-abcdef.log' ), array_slice( $commit, -3 ) );
        $this->assertStringStartsWith( "Updated: Migration run 20261031-120000-abcdef in staging: 3 applied\n\n- applied site:m1", $commit[array_search( '-m', $commit ) + 1] );
        $this->assertSame( array( 'push', '--quiet', 'origin', 'refs/heads/main:refs/heads/main',
                                  'refs/tags/migrations/staging/20261031-120000-abcdef:refs/tags/migrations/staging/20261031-120000-abcdef' ), end( $repo->gitCalls ) );
        $this->assertFalse( expMigrationLock::installation( $this->dir . '/upgrade.lock' )->isLocked(), 'the lock is free' );
    }

    // ---- MC-02 -----------------------------------------------------------------------------------------------------

    public function testFailedRunIsRestored()
    {
        $context = $this->context();
        $caption = $this->caption( $context );
        $repo = $this->repository();
        $report = $this->runner( $context, $caption, $repo )->run( $this->migrations( true ) );

        $this->assertSame( 1, $report['exit'] );
        $this->assertSame( array( 'restored', 'failed', 'not-run' ), array_column( $report['migrations'], 'action' ) );
        $this->assertSame( array( 'pending', 'failed', 'pending' ), array_column( $report['migrations'], 'state' ) );
        $this->assertSame( 1, $report['summary']['restored'] );
        // failure caption, then the restore of the pre caption, before the run's last row
        $calls = array_values( array_filter( $context->calls, function ( $c ) { return strpos( $c, 'caption-' ) === 0 || strpos( $c, 'restore-cap' ) === 0 || $c === 'up'; } ) );
        $this->assertSame( array( 'caption-migration-pre', 'up', 'up', 'caption-migration-failure', 'restore-cap1' ), $calls );
        $this->assertSame( 'migration run 20261031-120000-abcdef in staging failure: 2 migration(s) m2, m1', $caption->taken[1][1] );
        $options = $caption->restores[0][1];
        $this->assertTrue( $options['apply'] );
        $this->assertSame( 'database,storage,settings', $options['parts'] );
        $this->assertTrue( $options['no-caption'] && $options['force-no-caption'] && $options['no-verify'], 'no safety caption of its own' );
        $this->assertSame( $this->dir . '/upgrade.lock', $options['lock-file'], 'under the run\'s lock' );
        $this->assertSame( 'restored', $report['restore']['state'] );
        $this->assertSame( 'cap1', $report['restore']['caption'] );
        $this->assertSame( 'cap2', $report['restore']['failure_caption'] );
        $this->assertSame( array( 'database', 'settings' ), array_keys( $report['restore']['parts'] ) );
        $this->assertStringContainsString( 'restored the caption cap1 taken before the run', implode( "\n", $report['warnings'] ) );
        $this->assertSame( 'failed', $context->records["site\nm2"]['state'], 'the failure written again after the restore' );
        $this->assertSame( 'cap1', $context->records["site\nm2"]['caption_id'] );

        $last = end( $context->runs );
        $this->assertSame( 'failed', $last['state'] );
        $this->assertSame( 'cap1', $last['caption_pre'] );
        $this->assertSame( '', $last['caption_post'], 'no post caption after a failure' );
        $this->assertSame( 'pushed', $last['push_state'], 'the failed run is committed and pushed' );
        $this->assertContains( 'system.migration.rollback/restore', $this->auditNames( $context ) );
        $commit = $repo->gitCalls[4];
        $message = $commit[array_search( '-m', $commit ) + 1];
        $this->assertStringStartsWith( 'Updated: Migration run 20261031-120000-abcdef in staging failed', $message );
        $this->assertStringContainsString( "- restored site:m1\n- failed site:m2\nRestore: restored (caption cap1)", $message );

        // a restore that fails: errors, the steps by hand
        $context = $this->context( array( 'run-id' => '20261031-120001-abcdef' ) );
        $caption = $this->caption( $context );
        $caption->restoreExit = 1;
        $report = $this->runner( $context, $caption )->run( $this->migrations( true ) );
        $this->assertSame( 'failed', $report['restore']['state'] );
        $this->assertStringContainsString( 'the restore of the caption cap', implode( "\n", $report['errors'] ) );
        $this->assertNotEmpty( $report['restore']['steps'] );
        $this->assertSame( 'applied', $report['migrations'][0]['action'], 'not restored: m1 stays applied' );
    }

    // ---- MC-03 -----------------------------------------------------------------------------------------------------

    public function testNoCaptionWarnsAndContinues()
    {
        $context = $this->context();
        $caption = $this->caption( $context );
        $caption->fail = array( expMigrationCaption::PRE );
        $report = $this->runner( $context, $caption )->run( $this->migrations() );
        $this->assertSame( 0, $report['exit'], 'the run goes on' );
        $this->assertSame( 3, $report['summary']['applied'] );
        $this->assertStringContainsString( 'no caption could be taken, the run goes on without a way back: the database dump failed: disk full',
                                           implode( "\n", $report['warnings'] ) );
        $this->assertSame( '', end( $context->runs )['caption_pre'] );
        $this->assertSame( 'system.migration.caption', $context->audits[0][0] );
        $this->assertSame( 'failure', $context->audits[0][1]['result'] );

        $context = $this->context( array( 'run-id' => '20261031-120001-abcdef' ) );
        $caption = $this->caption( $context );
        $caption->fail = array( expMigrationCaption::PRE );
        $report = $this->runner( $context, $caption )->run( $this->migrations( true ) );
        $this->assertSame( 'none', $report['restore']['state'] );
        $this->assertSame( array(), $caption->restores, 'nothing to restore' );
        $this->assertStringContainsString( 'no caption was taken before the run: nothing was restored; restore by hand:', implode( "\n", $report['errors'] ) );
        $this->assertStringContainsString( 'exp:maintenance on', $report['restore']['steps'][0] );
    }

    // ---- MC-04 -----------------------------------------------------------------------------------------------------

    public function testOracleStopsWithManualSteps()
    {
        $context = $this->context( array( 'engine' => 'oracle' ) );
        $caption = $this->caption( $context );
        $report = $this->runner( $context, $caption )->run( $this->migrations( true ) );
        $this->assertSame( 1, $report['exit'] );
        $this->assertSame( 'manual', $report['restore']['state'] );
        $this->assertSame( array(), $caption->restores, 'Oracle is never restored automatically' );
        $this->assertSame( 'applied', $report['migrations'][0]['action'] );
        $this->assertStringContainsString( 'no built-in restore for oracle: nothing was restored; restore by hand:', implode( "\n", $report['errors'] ) );

        $db = new stdClass();
        $db->User = 'expo';
        $db->Password = 'secret-password';
        $db->Server = 'db.example.com';
        $db->Port = 1522;
        $db->DB = 'EXPPDB';
        $steps = expUpgradeRollback::manualSteps( 'oracle', $db, '/backup/20261031-120000' );
        $text = implode( "\n", $steps );
        $this->assertStringContainsString( 'impdp expo@//db.example.com:1522/EXPPDB SCHEMAS=EXPO DIRECTORY=DATA_PUMP_DIR DUMPFILE=<the export>.dmp TABLE_EXISTS_ACTION=REPLACE', $text );
        $this->assertStringContainsString( 'DROP TABLE <name> PURGE;', $text );
        $this->assertStringContainsString( './console exp:upgrade:rollback 20261031-120000 --parts=settings,storage --apply', $text );
        $this->assertStringContainsString( './console exp:upgrade:verify --after', $text );
        $this->assertStringNotContainsString( 'secret-password', $text );
        $this->assertSame( 7, count( $steps ) );
        $this->assertStringStartsWith( '1. ', $steps[0] );
        // without a server: the service name as the connect identifier
        $db->Server = '';
        $this->assertStringContainsString( 'impdp expo@EXPPDB ', implode( "\n", expUpgradeRollback::manualSteps( 'oracle', $db ) ) );
        // a database that already is a connect string (the driver connects to it) is kept, with or without a server
        $db->Server = 'db.example.com';
        $db->DB = '//db.example.com:1522/EXPPDB';
        $this->assertStringContainsString( 'impdp expo@//db.example.com:1522/EXPPDB SCHEMAS=EXPO ', implode( "\n", expUpgradeRollback::manualSteps( 'oracle', $db ) ) );
        $this->assertSame( array( 'sqlite', 'mysql', 'postgresql', 'mongodb' ), expUpgradeRollback::RESTORE_ENGINES );
    }

    // ---- MC-05 -----------------------------------------------------------------------------------------------------

    public function testNoRestoreAndNoCaption()
    {
        $context = $this->context();
        $caption = $this->caption( $context, array( 'restore' => false ) );
        $report = $this->runner( $context, $caption )->run( $this->migrations( true ) );
        $this->assertSame( 'off', $report['restore']['state'] );
        $this->assertSame( array(), $caption->restores );
        $this->assertSame( array( expMigrationCaption::PRE ), array_column( $caption->taken, 0 ), 'no failure caption either' );
        $this->assertStringContainsString( './console exp:upgrade:rollback cap1 --apply', implode( "\n", $report['warnings'] ) );

        $context = $this->context( array( 'run-id' => '20261031-120001-abcdef' ) );
        $caption = $this->caption( $context, array( 'before' => false, 'after' => false ) );
        $report = $this->runner( $context, $caption )->run( $this->migrations() );
        $this->assertSame( 0, $report['exit'] );
        $this->assertSame( array(), $caption->taken );
        $this->assertStringContainsString( 'no caption before the run (--no-caption', implode( "\n", $report['warnings'] ) );
    }

    // ---- MC-06 -----------------------------------------------------------------------------------------------------

    /** git status of the root (settings/ changed, a new design file, a nested checkout as untracked) and of extension/shop */
    private function uncommittedGit()
    {
        $root = $this->dir . '/site';
        mkdir( "$root/.git", 0775, true );
        mkdir( "$root/extension/shop/.git", 0775, true );
        mkdir( "$root/design/site/templates", 0775, true );
        file_put_contents( "$root/design/site/templates/new.tpl", "new\n" );
        file_put_contents( "$root/extension/shop/added.php", "<?php\n" );
        return function ( $path, array $args ) use ( $root ) {
            if ( $args[0] === 'rev-parse' )
                return array( 'ok' => true, 'exit' => 0, 'output' => str_repeat( $path === $root ? 'a' : 'b', 40 ) . "\n" );
            if ( in_array( 'status', $args, true ) )
                return array( 'ok' => true, 'exit' => 0, 'output' => $path === $root
                    ? " M settings/site.ini\0?? design/site/templates/new.tpl\0?? extension/shop/\0"
                    : "M  classes/shop.php\0?? added.php\0" );
            if ( $args[0] === 'diff' )
                return array( 'ok' => true, 'exit' => 0, 'output' => "diff --git a/x b/x\n" );
            return array( 'ok' => true, 'exit' => 0, 'output' => '' );
        };
    }

    public function testUncommittedCodeIsRefusedOrKept()
    {
        $context = $this->context();
        $caption = $this->caption( $context );
        $caption->gitFn = $this->uncommittedGit();
        $report = $this->runner( $context, $caption )->run( $this->migrations() );
        $this->assertSame( 2, $report['exit'] );
        $this->assertSame( 0, $report['summary']['applied'], 'nothing ran' );
        $this->assertNotContains( 'up', $context->calls );
        $this->assertSame( array(), $caption->taken, 'no caption' );
        $this->assertSame( array(), $context->runs, 'no run row' );
        $this->assertStringContainsString( '4 change(s) that are not committed in the code trees (settings/site.ini, design/site/templates/new.tpl; '
                                           . 'extension/shop: classes/shop.php, added.php): commit them first, or run with --include-uncommitted',
                                           implode( "\n", $report['errors'] ) );
        $this->assertSame( array( array( 'label' => 'root', 'changed' => 1, 'untracked' => 1 ), array( 'label' => 'extension/shop', 'changed' => 1, 'untracked' => 1 ) ),
                           $report['uncommitted'] );
        $status = $caption->gitCalls[0][1];
        $this->assertSame( array( '--no-optional-locks', 'status', '--porcelain=v1', '-z', '--untracked-files=all', '--', 'settings', 'extension', 'design' ), $status );
        $this->assertFalse( expMigrationLock::installation( $this->dir . '/upgrade.lock' )->isLocked(), 'the lock is free again' );

        // a dry run warns
        $dry = new expMigrationCaptionTestContext( false, array( 'root' => $this->dir . '/site', 'storage-dir' => '' ) );
        $dryCaption = $this->caption( $dry );
        $dryCaption->gitFn = $caption->gitFn;
        $plan = $this->runner( $dry, $dryCaption )->run( $this->migrations() );
        $this->assertSame( 0, $plan['exit'] );
        $this->assertStringContainsString( 'a run with --apply is refused until they are committed', implode( "\n", $plan['warnings'] ) );

        // --include-uncommitted: run, and the patch and the new files in the caption
        $context = $this->context( array( 'run-id' => '20261031-120001-abcdef' ) );
        $caption = $this->caption( $context, array( 'include-uncommitted' => true ) );
        $caption->gitFn = $dryCaption->gitFn;
        $report = $this->runner( $context, $caption )->run( $this->migrations() );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $dir = $this->dir . '/store/3/cap1';
        $this->assertFileExists( "$dir/migration/uncommitted/root.patch" );
        $this->assertFileExists( "$dir/migration/uncommitted/extension-shop.patch" );
        $this->assertSame( "new\n", file_get_contents( "$dir/migration/uncommitted/root/design/site/templates/new.tpl" ) );
        $this->assertSame( "<?php\n", file_get_contents( "$dir/migration/uncommitted/extension-shop/added.php" ) );
        $list = json_decode( file_get_contents( "$dir/migration/uncommitted.json" ), true );
        $this->assertSame( 'exp-migration-uncommitted/1', $list['format'] );
        $this->assertSame( array( 'root', 'extension/shop' ), array_column( $list['repos'], 'label' ) );
        $this->assertSame( str_repeat( 'a', 40 ), $list['repos'][0]['head'] );
        $sums = json_decode( file_get_contents( "$dir/upgrade/checksums.json" ), true );
        $this->assertArrayHasKey( 'migration/uncommitted/root.patch', $sums, 'checksums.json covers the migration part' );
        $this->assertArrayHasKey( 'migration/uncommitted.json', $sums );
        $diff = array_values( array_filter( $caption->gitCalls, function ( $c ) { return $c[1][0] === 'diff'; } ) );
        $this->assertSame( array( 'diff', '--binary', 'HEAD', '--', 'settings/site.ini' ), $diff[0][1] );

        // git_manager missing: cannot check, a warning
        $context = $this->context( array( 'run-id' => '20261031-120002-abcdef' ) );
        $caption = $this->caption( $context );
        $report = $this->runner( $context, $caption )->run( $this->migrations() );
        $this->assertSame( 0, $report['exit'] );
        $this->assertStringContainsString( 'could not be checked for changes that are not committed: git_manager is not active', implode( "\n", $report['warnings'] ) );
    }

    // ---- MC-07 -----------------------------------------------------------------------------------------------------

    public function testParseStatus()
    {
        $this->assertSame( array( 'changed' => array( 'a.php', 'b.php', 'new name.php', 'd.php' ), 'untracked' => array( 'e/f.tpl' ) ),
                           expMigrationCaption::parseStatus( " M a.php\0M  b.php\0R  new name.php\0old name.php\0?? e/f.tpl\0MM d.php\0!! ignored.log\0" ) );
        $this->assertSame( array( 'changed' => array(), 'untracked' => array() ), expMigrationCaption::parseStatus( '' ) );
    }

    // ---- MC-08 -----------------------------------------------------------------------------------------------------

    public function testReuseOfUnchangedCaption()
    {
        $site = $this->dir . '/site';
        mkdir( "$site/settings/override", 0775, true );
        mkdir( "$site/var/storage", 0775, true );
        file_put_contents( "$site/settings/override/site.ini.append.php", "a\n" );
        file_put_contents( "$site/var/storage/image.png", "png" );
        $options = array( 'storage-dir' => 'var/storage' );

        $context = $this->context( $options );
        $caption = $this->caption( $context );
        $first = $caption->take( expMigrationCaption::PRE, array( 'm1' ) );
        $this->assertFalse( $first['reused'] );
        $marker = json_decode( file_get_contents( $first['dir'] . '/migration/marker.json' ), true );
        $this->assertSame( 'exp-migration-marker/1', $marker['format'] );
        $this->assertSame( 'database-1', $marker['database'] );

        $again = $caption->take( expMigrationCaption::PRE, array( 'm2' ) );
        $this->assertTrue( $again['reused'] );
        $this->assertSame( $first['id'], $again['id'] );
        $this->assertCount( 1, $caption->taken, 'no new caption' );
        $this->assertSame( 'reuse', end( $context->audits )[1]['verb'] );

        // a post caption is reused as well (the next run's pre)
        $post = $caption->take( expMigrationCaption::POST, array( 'm2' ) );
        $this->assertFalse( $post['reused'], 'a post caption is always taken' );
        $this->assertSame( $post['id'], $caption->take( expMigrationCaption::PRE, array() )['id'] );

        // the database changed
        $caption->marker = 'database-2';
        $this->assertFalse( $caption->take( expMigrationCaption::PRE, array() )['reused'] );
        // storage changed
        file_put_contents( "$site/var/storage/image2.png", "png" );
        $this->assertFalse( $caption->take( expMigrationCaption::PRE, array() )['reused'] );
        // settings/override changed
        $this->assertTrue( $caption->take( expMigrationCaption::PRE, array() )['reused'] );
        touch( "$site/settings/override/site.ini.append.php", time() + 5 );
        clearstatcache();
        $this->assertFalse( $caption->take( expMigrationCaption::PRE, array() )['reused'] );
        // an engine without an exact marker: never reused
        $caption->marker = null;
        $this->assertFalse( $caption->take( expMigrationCaption::PRE, array() )['reused'] );
        $this->assertFalse( $caption->take( expMigrationCaption::PRE, array() )['reused'] );
        // the setting off
        $caption = $this->caption( $context, array( 'reuse' => false ) );
        $caption->existing = array();
        $this->assertFalse( $caption->take( expMigrationCaption::PRE, array() )['reused'] );
        $this->assertFalse( $caption->take( expMigrationCaption::PRE, array() )['reused'] );

        $this->assertTrue( expMigrationCaption::sameMarker( $marker, $marker ) );
        $this->assertFalse( expMigrationCaption::sameMarker( $marker, array( 'code' => 'x' ) + $marker ) );
        $this->assertFalse( expMigrationCaption::sameMarker( null, $marker ) );
        $this->assertFalse( expMigrationCaption::sameMarker( array( 'database' => '' ) + $marker, array( 'database' => '' ) + $marker ) );
        $this->assertSame( 'none', expMigrationCaption::filesMarker( $this->dir . '/missing' ) );
    }

    // ---- MC-09 -----------------------------------------------------------------------------------------------------

    public function testRepository()
    {
        $this->assertSame( 'migrations/production/20261031-120000-abcdef', expMigrationRepository::tagName( 'migrations/<environment>/<run-id>', 'Production', '20261031-120000-abcdef' ) );
        $this->assertSame( 'migrations/default/r1', expMigrationRepository::tagName( 'migrations/<environment>/<run-id>', '', 'r1' ) );
        $this->assertSame( 'm-20261031', expMigrationRepository::tagName( 'm-<date>', '', 'r1', mktime( 12, 0, 0, 10, 31, 2026 ) ) );
        foreach ( array( 'migrations/default/20261031-120000-abcdef', 'v1.0', 'a/b-c_d' ) as $ok )
            $this->assertTrue( expMigrationRepository::validTagName( $ok ), $ok );
        foreach ( array( '', '/a', 'a/', 'a..b', 'a b', 'a~1', 'a^', 'a:b', 'a?', 'a*', 'a[', 'a\\b', 'a.lock', 'a/.b', 'a//b', 'a@{1}', '-a', 'a.', '@' ) as $bad )
            $this->assertFalse( expMigrationRepository::validTagName( $bad ), $bad );

        $context = $this->context();
        $report = array( 'run_id' => '20261031-120000-abcdef', 'environment' => 'staging', 'exit' => 0, 'summary' => array( 'applied' => 1 ),
                         'migrations' => array( array( 'id' => 'm1', 'source' => 'site', 'action' => 'applied' ) ) );

        // the default: committed, tagged, pushed
        $repo = $this->repository();
        $result = $repo->record( $context, $report );
        $this->assertSame( array(), $result['problems'] );
        $this->assertSame( 'pushed', $result['push_state'] );
        $this->assertSame( "Updated: Migration run 20261031-120000-abcdef in staging: 1 applied\n\n- applied site:m1\n", expMigrationRepository::commitMessage( $report ) );

        // a tag that exists: never moved
        $repo = $this->repository( array(), array( 'rev-parse --verify' => array( 'ok' => true, 'exit' => 0, 'output' => str_repeat( 'c', 40 ) ) ) );
        $result = $repo->record( $context, $report );
        $this->assertSame( '', $result['tag_name'] );
        $this->assertStringContainsString( 'exists already: it is never moved', implode( "\n", $result['problems'] ) );
        $this->assertNotContains( 'tag', $repo->commands() );

        // the push fails: pending, retried later
        $repo = $this->repository( array(), array( 'push' => array( 'ok' => false, 'exit' => 128, 'output' => '' ) ) );
        $result = $repo->record( $context, $report );
        $this->assertSame( 'pending', $result['push_state'] );
        $this->assertNotSame( '', $result['commit_id'] );
        $this->assertStringContainsString( 'pushed again by the cronjob part migrationpush', implode( "\n", $result['problems'] ) );
        $this->assertSame( 'failure', end( $context->audits )[1]['result'] );
        $this->assertSame( 'system.migration.push', end( $context->audits )[0] );

        // no such remote
        $repo = $this->repository( array( 'remote' => 'upstream' ) );
        $this->assertSame( 'no-remote', $repo->record( $context, $report )['push_state'] );
        $repo = $this->repository( array( 'remote' => '' ) );
        $this->assertSame( 'no-remote', $repo->record( $context, $report )['push_state'] );

        // nothing staged: nothing committed
        $repo = $this->repository( array(), array( 'diff --cached' => array( 'ok' => true, 'exit' => 0, 'output' => '' ) ) );
        $result = $repo->record( $context, $report );
        $this->assertSame( array( '', '', 'none' ), array( $result['commit_id'], $result['tag_name'], $result['push_state'] ) );

        // Commit, Push, Tag disabled
        $repo = $this->repository( array( 'commit' => false ) );
        $this->assertSame( 'disabled', $repo->record( $context, $report )['push_state'] );
        $this->assertSame( array(), $repo->gitCalls );
        $repo = $this->repository( array( 'push' => false ) );
        $result = $repo->record( $context, $report );
        $this->assertSame( 'disabled', $result['push_state'] );
        $this->assertNotSame( '', $result['tag_name'] );
        $this->assertNotContains( 'push', $repo->commands() );
        $repo = $this->repository( array( 'tag' => false ) );
        $this->assertSame( '', $repo->record( $context, $report )['tag_name'] );
        $this->assertNotContains( 'tag', $repo->commands() );

        // another branch, no branch
        $repo = $this->repository( array( 'branch' => 'production' ) );
        $result = $repo->record( $context, $report );
        $this->assertSame( '', $result['commit_id'] );
        $this->assertStringContainsString( 'is on the branch main, not production', implode( "\n", $result['problems'] ) );
        $repo = $this->repository( array(), array( 'rev-parse --abbrev-ref' => array( 'ok' => true, 'exit' => 0, 'output' => "HEAD\n" ) ) );
        $this->assertStringContainsString( 'is on no branch', implode( "\n", $repo->record( $context, $report )['problems'] ) );

        // no git_manager; not a repository
        $repo = $this->repository();
        $repo->gitFn = null;
        $this->assertStringContainsString( 'git_manager is not active', implode( "\n", $repo->record( $context, $report )['problems'] ) );
        $repo = new expMigrationRepositoryFake( $this->dir . '/nowhere', array() );
        $repo->gitFn = function () { return array( 'ok' => true, 'exit' => 0, 'output' => '' ); };
        $this->assertStringContainsString( 'is not a git repository', implode( "\n", $repo->record( $context, $report )['problems'] ) );

        // separate: Path is the repository, the files relative to it; the identity of migrations when none is set
        mkdir( $this->dir . '/separate/.git', 0775, true );
        mkdir( $this->dir . '/separate/status', 0775, true );
        file_put_contents( $this->dir . '/separate/status/staging.json', "{}\n" );
        $repo = $this->repository( array( 'mode' => 'separate', 'path' => $this->dir . '/separate' ),
                                   array( 'config' => array( 'ok' => false, 'exit' => 1, 'output' => '' ) ) );
        $this->assertSame( $this->dir . '/separate', $repo->top() );
        $this->assertSame( array( 'status/staging.json' ), $repo->files( $context ) );
        $result = $repo->record( $context, $report );
        $this->assertSame( 'pushed', $result['push_state'] );
        $commit = $repo->gitCalls[4];
        $this->assertSame( '-c', $commit[0] );
        $this->assertSame( 'user.name=Exponential migrations', $commit[1] );

        // the site's Path outside the checkout: nothing to commit
        $repo = $this->repository( array( 'path' => $this->dir . '/elsewhere' ) );
        $this->assertSame( array(), $repo->files( $context ) );
    }

    // ---- MC-10 -----------------------------------------------------------------------------------------------------

    public function testSettings()
    {
        $ini = eZINI::instance( 'migration.ini' );
        $this->assertSame( 'enabled', $ini->variable( 'CaptionSettings', 'Before' ) );
        $this->assertSame( 'enabled', $ini->variable( 'CaptionSettings', 'RestoreOnFailure' ) );
        $settings = expMigrationCaption::settingsFrom( array(), $ini );
        $this->assertSame( array( 'database', 'storage', 'settings' ), $settings['restore-parts'] );
        $this->assertSame( array( 'settings', 'extension', 'design' ), $settings['trees'] );
        $this->assertSame( array( 'exp_migration', 'exp_migration_run', 'ezsession', 'expaudit_*' ), $settings['ignore-tables'] );
        $this->assertTrue( expMigrationCaption::ignoredTable( 'expaudit_event_ip', $settings['ignore-tables'] ) );
        $this->assertTrue( expMigrationCaption::ignoredTable( 'EZSESSION', $settings['ignore-tables'] ) );
        $this->assertFalse( expMigrationCaption::ignoredTable( 'ezsession_x', $settings['ignore-tables'] ) );
        $this->assertFalse( expMigrationCaption::ignoredTable( 'ezcontentobject', $settings['ignore-tables'] ) );
        $this->assertTrue( $settings['before'] && $settings['after'] && $settings['reuse'] && $settings['restore'] );
        $this->assertFalse( $settings['include-uncommitted'] );
        $over = expMigrationCaption::settingsFrom( array( 'no-caption' => true, 'no-restore' => true, 'include-uncommitted' => true ), $ini );
        $this->assertFalse( $over['before'] || $over['after'] || $over['restore'] );
        $this->assertTrue( $over['include-uncommitted'] );

        $repo = expMigrationRepository::fromSettings( array( 'root' => '/srv/site' ), $ini );
        $this->assertSame( 'site', $repo->mode() );
        $this->assertSame( '/srv/site/migrations', $repo->directory() );
        $this->assertSame( '/srv/site', $repo->top() );
        $this->assertSame( 'migrations/<environment>/<run-id>', $repo->setting( 'tag-pattern' ) );
        $this->assertSame( 'origin', $repo->setting( 'remote' ) );
        $this->assertTrue( $repo->setting( 'commit' ) && $repo->setting( 'tag' ) && $repo->setting( 'push' ) );
        $repo = expMigrationRepository::fromSettings( array( 'root' => '/srv/site', 'no-push' => true, 'no-tag' => true ), $ini );
        $this->assertFalse( $repo->setting( 'push' ) || $repo->setting( 'tag' ) );
        $this->assertTrue( $repo->setting( 'commit' ) );
        $repo = expMigrationRepository::fromSettings( array( 'root' => '/srv/site', 'no-commit' => true ), $ini );
        $this->assertFalse( $repo->setting( 'commit' ) || $repo->setting( 'push' ) || $repo->setting( 'tag' ) );

        foreach ( array( 'migration-pre', 'migration-post', 'migration-failure' ) as $kind )
            $this->assertContains( $kind, expUpgradeCaption::KINDS );
        $this->assertSame( array( expMigrationCaption::PRE, expMigrationCaption::POST, expMigrationCaption::FAILURE ),
                           array( 'migration-pre', 'migration-post', 'migration-failure' ) );

        $cron = eZINI::instance( 'cronjob.ini' );
        $this->assertSame( array( 'migrationpush.php' ), $cron->variable( 'CronjobPart-migrationpush', 'Scripts' ) );
        $this->assertFileExists( self::$root . '/cronjobs/migrationpush.php' );
        $this->assertTrue( class_exists( 'Exponential\\Cronjob\\Kernel\\Migrationpush' ) );
        $this->assertTrue( is_subclass_of( 'Exponential\\Cronjob\\Kernel\\Migrationpush', 'Exponential\\Runnable\\CronjobPart' ) );

        // the flow of migrate() is built from the settings
        $context = $this->context();
        $flow = expMigrationRunFlow::fromSettings( $context, array( 'no-push' => true ) );
        $this->assertInstanceOf( 'expMigrationCaption', $flow->caption() );
        $this->assertFalse( $flow->repository()->setting( 'push' ) );
        $this->assertSame( $this->dir . '/site/migrations', $flow->repository()->directory() );
    }

    // ---- MC-11 -----------------------------------------------------------------------------------------------------

    public function testCaptionRollbackNeedsExpertAndConfirmation()
    {
        $report = expMigrationRollback::caption( '20261031-120000', array( 'apply' => true ) );
        $this->assertSame( 2, $report['exit'] );
        $this->assertStringContainsString( 'for experts', $report['errors'][0] );
        $report = expMigrationRollback::caption( '20261031-120000', array( 'apply' => true, 'expert' => true, 'confirm' => '20261031-120001' ) );
        $this->assertSame( 2, $report['exit'] );
        $this->assertStringContainsString( "confirm with the caption's id (20261031-120000) typed again", $report['errors'][0] );
        $report = expMigrationRollback::run( 'not a run id!' );
        $this->assertSame( 2, $report['exit'] );
    }

    // ---- MC-12 -----------------------------------------------------------------------------------------------------

    /** A tool that writes much to stderr (mongorestore's progress) does not block expUpgradeBackup::runWithEnv(). */
    public function testRunWithEnvDrainsBothPipes()
    {
        $err = '';
        $start = microtime( true );
        $code = expUpgradeBackup::runWithEnv( 'yes x 2>/dev/null | head -c 300000 1>&2; yes y 2>/dev/null | head -c 300000; exit 3', array( 'EXP_WP4' => '1' ), $err );
        $this->assertSame( 3, $code );
        // all of it, in order ("yes" itself is silenced: its broken-pipe message went to the same stderr and could land
        // between the x lines, 2026-10-31)
        $this->assertSame( str_repeat( "x\n", 150000 ), substr( $err, 0, 300000 ) );
        $this->assertLessThan( 30, microtime( true ) - $start );
    }
}
