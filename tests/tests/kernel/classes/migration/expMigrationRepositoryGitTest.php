<?php
/**
 * The migrations repository with real git: a throwaway checkout and a bare repository as its remote under var/tmp,
 * removed afterwards. git runs through git_manager when it is installed, else through the same no-shell call
 * (expMigrationTestGit). Skipped without git.
 *
 *  MG-1 site mode: the run's status file and log committed by path (a file the site staged stays staged and out of
 *       the commit), an annotated tag on that commit, branch and tag in the remote; the same run again has nothing
 *       to commit; a tag that exists is never moved
 *  MG-2 separate mode: Path is a repository of its own, committed, tagged and pushed there (a failed run too)
 *  MG-3 a push that fails stays local (pending); retryPushes() pushes it once the remote works and records pushed
 *       in exp_migration_run (SQLite in process)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationCaptionFakes.php';

class expMigrationRepositoryGitTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    protected function setUp(): void
    {
        $probe = expMigrationTestGit::run( dirname( __DIR__, 5 ), array( '--version' ) );
        if ( !$probe['ok'] || strpos( $probe['output'], 'git version' ) !== 0 )
            $this->markTestSkipped( 'git is not installed' );
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/migration-git-test-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
        mkdir( $this->dir, 0775, true );
    }

    protected function tearDown(): void
    {
        if ( !$this->dir || !is_dir( $this->dir ) )
            return;
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->dir, FilesystemIterator::SKIP_DOTS ),
                                                   RecursiveIteratorIterator::CHILD_FIRST );
        foreach ( $iterator as $item )
        {
            if ( !$item->isLink() )
                @chmod( $item->getPathname(), 0700 );
            $item->isDir() && !$item->isLink() ? rmdir( $item->getPathname() ) : unlink( $item->getPathname() );
        }
        rmdir( $this->dir );
    }

    /** git in a folder; fails the test when git fails */
    private function git( $path, array $args )
    {
        $r = expMigrationTestGit::run( $path, $args );
        $this->assertTrue( $r['ok'], 'git ' . implode( ' ', $args ) . " in $path failed" );
        return trim( $r['output'] );
    }

    /** A checkout on main with one commit, pushed to a bare repository as origin. */
    private function checkout( $name, $identity = true )
    {
        $bare = "$this->dir/$name-remote.git";
        $work = "$this->dir/$name";
        mkdir( $bare );
        mkdir( $work );
        $this->git( $bare, array( 'init', '--quiet', '--bare' ) );
        $this->git( $work, array( 'init', '--quiet' ) );
        $this->git( $work, array( 'checkout', '--quiet', '-b', 'main' ) );
        if ( $identity )
        {
            $this->git( $work, array( 'config', 'user.name', 'Site Team' ) );
            $this->git( $work, array( 'config', 'user.email', 'team@example.com' ) );
        }
        $this->git( $work, array( 'config', 'commit.gpgsign', 'false' ) );
        $this->git( $work, array( 'config', 'tag.gpgsign', 'false' ) );
        file_put_contents( "$work/README", "site\n" );
        $this->git( $work, array( 'add', 'README' ) );
        $this->git( $work, array( '-c', 'user.name=Setup', '-c', 'user.email=setup@example.com', 'commit', '--quiet', '-m', 'Initial' ) );
        $this->git( $work, array( 'remote', 'add', 'origin', $bare ) );
        $this->git( $work, array( 'push', '--quiet', 'origin', 'main' ) );
        return array( $work, $bare );
    }

    private function context( $root, $runId = '20261031-130000-a1b2c3' )
    {
        return new expMigrationCaptionTestContext( true, array( 'root' => $root, 'run-id' => $runId, 'environment' => 'staging' ) );
    }

    /** The status file and log of a run, as the status mirror writes them. */
    private function writeRecord( $dir, $runId, $text = 'done' )
    {
        @mkdir( "$dir/status", 0775, true );
        @mkdir( "$dir/runs", 0775, true );
        file_put_contents( "$dir/status/staging.json", json_encode( array( 'schema' => 'exp-migration-status/1', 'last' => $runId, 'text' => $text ) ) . "\n" );
        file_put_contents( "$dir/runs/$runId.log", "2026-10-31T13:00:00Z info run $runId $text\n" );
    }

    private function report( $runId, $exit = 0 )
    {
        return array( 'run_id' => $runId, 'environment' => 'staging', 'exit' => $exit, 'summary' => array( 'applied' => 1 ),
                      'migrations' => array( array( 'id' => '20261031130000_wp4_note', 'source' => 'site', 'action' => $exit ? 'failed' : 'applied' ) ) );
    }

    /** MG-1 */
    public function testSiteModeCommitTagPush()
    {
        list( $work, $bare ) = $this->checkout( 'site' );
        file_put_contents( "$work/notes.txt", "staged by the site\n" );
        $this->git( $work, array( 'add', 'notes.txt' ) );
        $this->writeRecord( "$work/migrations", '20261031-130000-a1b2c3' );

        $repo = new expMigrationRepositoryRealGit( $work, array( 'mode' => 'site', 'path' => 'migrations' ) );
        $context = $this->context( $work );
        $result = $repo->record( $context, $this->report( '20261031-130000-a1b2c3' ) );
        $this->assertSame( array(), $result['problems'] );
        $this->assertSame( 'pushed', $result['push_state'] );
        $this->assertSame( 'migrations/staging/20261031-130000-a1b2c3', $result['tag_name'] );
        $this->assertSame( $this->git( $work, array( 'rev-parse', 'HEAD' ) ), $result['commit_id'] );
        $this->assertSame( "migrations/runs/20261031-130000-a1b2c3.log\nmigrations/status/staging.json",
                           $this->git( $work, array( 'show', '--name-only', '--format=', 'HEAD' ) ) );
        $this->assertSame( 'notes.txt', $this->git( $work, array( 'diff', '--cached', '--name-only' ) ), 'what the site staged stays staged' );
        $this->assertSame( 'Updated: Migration run 20261031-130000-a1b2c3 in staging: 1 applied', $this->git( $work, array( 'log', '-1', '--format=%s' ) ) );
        $this->assertSame( 'Site Team', $this->git( $work, array( 'log', '-1', '--format=%an' ) ), "the repository's own identity" );
        $this->assertSame( 'tag', $this->git( $work, array( 'cat-file', '-t', 'migrations/staging/20261031-130000-a1b2c3' ) ), 'an annotated tag' );
        $this->assertSame( $result['commit_id'], $this->git( $work, array( 'rev-parse', 'migrations/staging/20261031-130000-a1b2c3^{commit}' ) ) );
        $this->assertSame( $result['commit_id'], $this->git( $bare, array( 'rev-parse', 'refs/heads/main' ) ), 'the branch in the remote' );
        $this->assertSame( $result['commit_id'], $this->git( $bare, array( 'rev-parse', 'refs/tags/migrations/staging/20261031-130000-a1b2c3^{commit}' ) ), 'the tag in the remote' );
        $this->assertSame( 'system.migration.push', end( $context->audits )[0] );
        $this->assertSame( 'success', end( $context->audits )[1]['result'] );

        // the same run again: nothing to commit
        $again = $repo->record( $context, $this->report( '20261031-130000-a1b2c3' ) );
        $this->assertSame( array( '', '', 'none' ), array( $again['commit_id'], $again['tag_name'], $again['push_state'] ) );

        // a run whose tag exists already: committed, the tag never moved
        $this->writeRecord( "$work/migrations", '20261031-130000-a1b2c3', 'changed' );
        $moved = $repo->record( $context, $this->report( '20261031-130000-a1b2c3' ) );
        $this->assertNotSame( $result['commit_id'], $moved['commit_id'] );
        $this->assertSame( '', $moved['tag_name'] );
        $this->assertStringContainsString( 'exists already: it is never moved', implode( "\n", $moved['problems'] ) );
        $this->assertSame( $result['commit_id'], $this->git( $work, array( 'rev-parse', 'migrations/staging/20261031-130000-a1b2c3^{commit}' ) ) );
        $this->assertSame( $result['commit_id'], $this->git( $bare, array( 'rev-parse', 'refs/tags/migrations/staging/20261031-130000-a1b2c3^{commit}' ) ) );
        $this->assertSame( $moved['commit_id'], $this->git( $bare, array( 'rev-parse', 'refs/heads/main' ) ), 'the branch moved on' );
    }

    /** MG-2 */
    public function testSeparateRepository()
    {
        list( $repoDir, $bare ) = $this->checkout( 'migrations-repo' );
        mkdir( "$this->dir/site" );
        $this->writeRecord( $repoDir, '20261031-130100-a1b2c3' );
        $repo = new expMigrationRepositoryRealGit( "$this->dir/site", array( 'mode' => 'separate', 'path' => $repoDir ) );
        $result = $repo->record( $this->context( "$this->dir/site", '20261031-130100-a1b2c3' ), $this->report( '20261031-130100-a1b2c3', 1 ) );
        $this->assertSame( array(), $result['problems'] );
        $this->assertSame( 'pushed', $result['push_state'] );
        $this->assertSame( "runs/20261031-130100-a1b2c3.log\nstatus/staging.json", $this->git( $repoDir, array( 'show', '--name-only', '--format=', 'HEAD' ) ) );
        $this->assertSame( 'Updated: Migration run 20261031-130100-a1b2c3 in staging failed', $this->git( $repoDir, array( 'log', '-1', '--format=%s' ) ) );
        $this->assertSame( $result['commit_id'], $this->git( $bare, array( 'rev-parse', 'refs/tags/migrations/staging/20261031-130100-a1b2c3^{commit}' ) ) );
    }

    /** MG-3 */
    public function testFailedPushIsRetried()
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        list( $work, $bare ) = $this->checkout( 'retry' );
        $this->git( $work, array( 'remote', 'set-url', 'origin', "$this->dir/missing.git" ) );
        $this->writeRecord( "$work/migrations", '20261031-130200-a1b2c3' );
        $repo = new expMigrationRepositoryRealGit( $work, array() );
        $result = $repo->record( $this->context( $work, '20261031-130200-a1b2c3' ), $this->report( '20261031-130200-a1b2c3' ) );
        $this->assertSame( 'pending', $result['push_state'] );
        $this->assertNotSame( '', $result['commit_id'] );
        $this->assertNotSame( $result['commit_id'], $this->git( $bare, array( 'rev-parse', 'refs/heads/main' ) ), 'nothing reached the remote' );

        $db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => "$this->dir/record.sqlite",
                                      'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                      'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                      'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                      'use_persistent_connection' => false, 'show_errors' => false ) );
        $handler = set_exception_handler( null );
        restore_exception_handler();
        try
        {
            expKernelTableSchema::reset();
            $messages = array();
            $this->assertTrue( expKernelTableSchema::install( array( expMigrationRecord::RUN_TABLE ), $db, dirname( __DIR__, 5 ) . '/share/db_schema.dba', $messages ),
                               implode( '; ', $messages ) );
            $this->assertTrue( expMigrationRecord::writeRun( $db, array( 'run_id' => '20261031-130200-a1b2c3', 'state' => 'done', 'started_at' => 1, 'finished_at' => 2,
                                                                         'commit_id' => $result['commit_id'], 'tag_name' => $result['tag_name'], 'push_state' => 'pending' ) ) );
            $this->assertTrue( expMigrationRecord::writeRun( $db, array( 'run_id' => '20261031-120000-000000', 'state' => 'done', 'started_at' => 1, 'push_state' => 'pushed' ) ) );

            // still failing: stays pending
            $retry = $repo->retryPushes( $db );
            $this->assertSame( array( 1, 0, 1 ), array( $retry['tried'], $retry['pushed'], $retry['pending'] ) );
            $this->assertSame( 'pending', expMigrationRecord::run( $db, '20261031-130200-a1b2c3' )['push_state'] );

            $this->git( $work, array( 'remote', 'set-url', 'origin', $bare ) );
            $retry = $repo->retryPushes( $db );
            $this->assertSame( array( 1, 1, 0 ), array( $retry['tried'], $retry['pushed'], $retry['pending'] ), implode( '; ', $retry['messages'] ) );
            $this->assertSame( 'pushed', expMigrationRecord::run( $db, '20261031-130200-a1b2c3' )['push_state'] );
            $this->assertSame( $result['commit_id'], $this->git( $bare, array( 'rev-parse', 'refs/heads/main' ) ) );
            $this->assertSame( $result['commit_id'], $this->git( $bare, array( 'rev-parse', 'refs/tags/' . $result['tag_name'] . '^{commit}' ) ) );
            $this->assertSame( 0, $repo->retryPushes( $db )['tried'], 'nothing left' );
        }
        finally
        {
            $db->close();
            expKernelTableSchema::reset();
            // the kernel may install its exception handler on the first query of a process
            for ( $i = 0; $i < 5; $i++ )
            {
                $now = set_exception_handler( null );
                restore_exception_handler();
                if ( $now === $handler )
                    break;
                restore_exception_handler();
            }
        }
    }
}
