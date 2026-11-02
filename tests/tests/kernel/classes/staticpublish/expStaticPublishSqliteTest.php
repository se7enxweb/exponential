<?php
/**
 * Publishing end to end (expStaticPublisher, expStaticRepository) on SQLite in process with real git: a SQLite file
 * under var/tmp with the kernel schema and the clean data, a small site answered by a fake request function, the
 * target's folder as its git repository and a local bare repository as its remote (receive.denyNonFastForwards and
 * receive.denyDeletes on, so a forced push or a moved tag would be refused there). Skipped without git or SQLite3.
 *
 *  SP-01 the tables: the upgrade step, the record and the on-publish queue (expStaticStatusEngineChecks on SQLite)
 *  SP-02 build and publish: one commit of only the build's files, tag static/<target>/1, branch and tag in the remote,
 *        a run row (done, pushed, counts) and the pages; the local manifest of the build before stays out of git
 *  SP-03 a second publish after the site changed: a page removed and one changed, committed as such, tag 2; a third
 *        with nothing new commits and tags nothing
 *  SP-04 the remote unreachable: the commit and tag 3 stay local, the run is pending, exp:static:push's retry()
 *        pushes it once the remote is back and the run is pushed
 *  SP-05 rollback to 1: the dry run changes nothing; --apply makes a new commit on top (the history stays) whose
 *        files are those of tag 1, tagged 4 and pushed; no tag moved, here or in the remote; a run of kind rollback
 *  SP-06 Folder=docs on Branch=main of a remote that has a README: the history is taken over, the build lands in
 *        docs/, the README stays
 *  SP-07 the lock: a publish while another holds the target's lock is refused; a build that fails commits nothing
 *  SP-08 the preview and Discard: a build before the first publish is all new and thrown away without a trace; after a
 *        publish a new build's diff (added, changed, removed) and Discard bring the publish back; the tags and commits
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/migration/expMigrationRunnerEngineChecks.php';
require_once __DIR__ . '/expStaticStatusEngineChecks.php';

class expStaticPublishSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationRunnerEngineChecks;
    use expStaticStatusEngineChecks;

    /** @var eZSQLite3DB|null */
    private $db;

    /** host path => response */
    private $site = array();

    /** @var array[] audit events: name, data */
    private $audits = array();

    const CONTEXT = array( 'siteaccesses' => array( 'site', 'admin' ), 'admin' => array( 'admin' ), 'urls' => array( 'site' => 'www.example.com', 'admin' => 'admin.example.com' ) );

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $probe = self::git( dirname( __DIR__, 5 ), array( '--version' ) );
        if ( !$probe['ok'] )
            $this->markTestSkipped( 'git is not installed' );
        $this->db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->runnerDir() . '/kernel.sqlite',
                                            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                            'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                            'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                            'use_persistent_connection' => false, 'show_errors' => false ) );
        $this->runnerBegin( $this->db );
        foreach ( array( 'schema.sql', 'cleandata.sql' ) as $sql )
            $this->assertNotFalse( $this->db->insertFile( self::runnerRoot() . '/kernel/sql/sqlite', $sql, false ), "kernel/sql/sqlite/$sql loads" );
        $root = $this->runnerDir() . '/root';
        foreach ( array( 'design/s/stylesheets/site.css' => 'body{background:url(../images/bg.png)}', 'design/s/images/bg.png' => 'PNG-bg' ) as $path => $content )
        {
            if ( !is_dir( dirname( "$root/$path" ) ) )
                mkdir( dirname( "$root/$path" ), 0775, true );
            file_put_contents( "$root/$path", $content );
        }
        $this->site = array(
            '/' => self::page( 'Home', '<h1>Home</h1><a href="/about-us">About</a> <a href="/news">News</a>' ),
            '/about-us' => self::page( 'About', '<h1>About us</h1>' ),
            '/news' => self::page( 'News', '<h1>News</h1><a href="/news/item-1">Item 1</a>' ),
            '/news/item-1' => self::page( 'Item 1', '<h1>Item 1</h1>' ),
        );
    }

    protected function tearDown(): void
    {
        if ( $this->db )
            $this->db->close();
        $this->runnerEnd();
    }

    private static function page( $title, $body )
    {
        return array( 'status' => 200, 'type' => 'text/html; charset=utf-8', 'cookies' => array(),
                      'body' => "<!DOCTYPE html><html><head><title>$title</title><link rel=\"stylesheet\" href=\"/design/s/stylesheets/site.css\"></head>"
                              . "<body><nav><a href=\"/\">Home</a></nav><main>$body</main></body></html>" );
    }

    /** Public for the closure. */
    public function answer( $url )
    {
        $path = (string)parse_url( $url, PHP_URL_PATH );
        if ( isset( $this->site[$path] ) )
            return $this->site[$path];
        return array( 'status' => 404 ) + self::page( 'Not found', '<h1>Not found</h1>' );
    }

    /** git without a shell in a folder */
    private static function git( $cwd, array $args )
    {
        $pipes = array();
        $p = proc_open( array_merge( array( 'git' ), $args ), array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $cwd,
                        array( 'PATH' => getenv( 'PATH' ) ?: '/usr/bin:/bin', 'HOME' => getenv( 'HOME' ) ?: '/tmp', 'LC_ALL' => 'C' ) );
        fclose( $pipes[0] );
        $out = stream_get_contents( $pipes[1] );
        $err = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        return array( 'ok' => proc_close( $p ) === 0, 'output' => trim( (string)$out ), 'error' => (string)$err );
    }

    private function gitOk( $cwd, array $args )
    {
        $r = self::git( $cwd, $args );
        $this->assertTrue( $r['ok'], 'git ' . implode( ' ', $args ) . ": {$r['error']}" );
        return $r['output'];
    }

    /** A bare repository that refuses non-fast-forward pushes and deletions. */
    private function bare( $name )
    {
        $bare = $this->runnerDir() . "/$name.git";
        mkdir( $bare, 0775, true );
        $this->gitOk( $bare, array( 'init', '--quiet', '--bare' ) );
        $this->gitOk( $bare, array( 'config', 'receive.denyNonFastForwards', 'true' ) );
        $this->gitOk( $bare, array( 'config', 'receive.denyDeletes', 'true' ) );
        return $bare;
    }

    private function target( array $settings )
    {
        return new expStaticTarget( 'test-site', $settings + array( 'SiteAccess' => 'site', 'BasePath' => '/repo/', 'CrawlURL' => 'http://site.test', 'MaxDepth' => '5',
                                                                   'Kind' => 'git', 'Branch' => 'gh-pages', 'Credential' => 'none' ) );
    }

    private function publisher( expStaticTarget $target )
    {
        $test = $this;
        return new expStaticPublisher( $target, array(
            'root' => $this->runnerDir() . '/root', 'var_dir' => 'var/test', 'work_dir' => $this->runnerDir() . '/out', 'db' => $this->db,
            'by' => 'cli test', 'push' => true, 'commit_name' => 'Static Test', 'commit_email' => 'static@example.com', 'git_manager' => false,
            'audit' => function ( $name, $data ) use ( $test ) { $test->audit( $name, $data ); },
            'build' => array( 'fetcher' => function ( $url ) use ( $test ) { return $test->answer( $url ); }, 'base_path' => '', 'context' => self::CONTEXT,
                              'max_site_bytes' => 1000000, 'max_file_bytes' => 100000 ),
        ) );
    }

    /** Public for the closure. */
    public function audit( $name, $data )
    {
        $this->audits[] = array( $name, $data );
    }

    /** @return string[] name => object sha of every tag of a repository */
    private function tagObjects( $dir )
    {
        $tags = array();
        foreach ( array_filter( explode( "\n", $this->gitOk( $dir, array( 'for-each-ref', '--format=%(refname:short) %(objectname)', 'refs/tags' ) ) ) ) as $line )
        {
            list( $name, $sha ) = explode( ' ', $line );
            $tags[$name] = $sha;
        }
        return $tags;
    }

    /** SP-01 */
    public function testTables()
    {
        $this->checkStatusTables( $this->db );
        $this->checkStatusWriteAndRead( $this->db );
        $this->checkStatusQueue( $this->db );
    }

    /** SP-02 to SP-05 */
    public function testPublishPushRetryAndRollback()
    {
        $bare = $this->bare( 'remote' );
        $target = $this->target( array( 'Remote' => $bare ) );
        $checkout = $this->runnerDir() . '/out/test-site';

        // SP-02
        $report = $this->publisher( $target )->publish( array( 'build' => true ) );
        $this->assertSame( array(), $report['errors'], implode( '; ', $report['errors'] ) );
        $this->assertTrue( $report['build']['applied'] );
        $this->assertSame( 'done', $report['state'] );
        $this->assertSame( 'static/test-site/1', $report['tag_name'] );
        $this->assertSame( 'pushed', $report['push_state'] );
        $this->assertSame( 0, $report['exit'] );
        $this->assertSame( $report['commit_id'], $this->gitOk( $checkout, array( 'rev-parse', 'HEAD' ) ) );
        $this->assertSame( $report['commit_id'], $this->gitOk( $bare, array( 'rev-parse', 'refs/heads/gh-pages' ) ) );
        $this->assertSame( $report['commit_id'], $this->gitOk( $bare, array( 'rev-parse', 'static/test-site/1^{commit}' ) ) );
        $this->assertSame( 'tag', $this->gitOk( $bare, array( 'cat-file', '-t', 'static/test-site/1' ) ), 'an annotated tag' );
        $files = explode( "\n", $this->gitOk( $checkout, array( 'ls-files' ) ) );
        $this->assertContains( 'index.html', $files );
        $this->assertContains( 'about-us/index.html', $files );
        $this->assertContains( '.static-publish/manifest.json', $files );
        $this->assertContains( 'design/s/stylesheets/site.css', $files );
        $this->assertSame( $report['counts']['added'], count( $files ) );
        $this->assertStringStartsWith( 'Publish test-site: ', $this->gitOk( $checkout, array( 'log', '-1', '--format=%s' ) ) );
        $this->assertSame( 'Static Test <static@example.com>', $this->gitOk( $checkout, array( 'log', '-1', '--format=%an <%ae>' ) ) );
        $runs = expStaticStatus::runs( $this->db, 'test-site' );
        $this->assertCount( 1, $runs );
        $this->assertSame( array( 'publish', 'done', 'pushed', 'static/test-site/1', 'cli test' ),
                           array( $runs[0]['kind'], $runs[0]['state'], $runs[0]['push_state'], $runs[0]['tag_name'], $runs[0]['run_by'] ) );
        $this->assertSame( $report['build']['run_id'], $runs[0]['run_id'] );
        $this->assertSame( 4, $runs[0]['pages'] );
        $this->assertGreaterThan( 0, $runs[0]['bytes'] );
        $this->assertSame( array( 'about-us/index.html', 'index.html', 'news/index.html', 'news/item-1/index.html' ),
                           array_keys( expStaticStatus::pages( $this->db, 'test-site' ) ) );
        $this->assertSame( 'system.staticpublish.publish', end( $this->audits )[0] );
        $this->assertSame( 'success', end( $this->audits )[1]['result'] );

        // SP-03
        unset( $this->site['/news/item-1'] );
        $this->site['/news'] = self::page( 'News', '<h1>News, updated</h1>' );
        $report = $this->publisher( $target )->publish( array( 'build' => true, 'message' => 'Item 1 withdrawn' ) );
        $this->assertSame( array(), $report['errors'], implode( '; ', $report['errors'] ) );
        $this->assertSame( 'static/test-site/2', $report['tag_name'] );
        $this->assertSame( 1, $report['counts']['removed'] );
        $this->assertGreaterThanOrEqual( 2, $report['counts']['changed'], 'news/index.html and the manifest' );
        $this->assertSame( 'Item 1 withdrawn', $this->gitOk( $checkout, array( 'log', '-1', '--format=%s' ) ) );
        $this->assertNotContains( 'news/item-1/index.html', explode( "\n", $this->gitOk( $checkout, array( 'ls-files' ) ) ) );
        $this->assertFileExists( $checkout . '/' . expStaticManifest::PREVIOUS_FILE );
        $this->assertSame( '', $this->gitOk( $checkout, array( 'status', '--porcelain' ) ), 'the build before stays out of git (.git/info/exclude)' );

        $same = $this->publisher( $target )->publish();
        $this->assertSame( array(), $same['errors'] );
        $this->assertSame( 'unchanged', $same['state'] );
        $this->assertSame( '', $same['tag_name'] );
        $this->assertCount( 2, $this->tagObjects( $checkout ) );

        // SP-04
        $this->assertTrue( rename( $bare, $bare . '.away' ) );
        $this->site['/about-us'] = self::page( 'About', '<h1>About us, new</h1>' );
        $pending = $this->publisher( $target )->publish( array( 'build' => true ) );
        $this->assertSame( 'done', $pending['state'] );
        $this->assertSame( 'pending', $pending['push_state'] );
        $this->assertSame( 'static/test-site/3', $pending['tag_name'] );
        $this->assertSame( 1, $pending['exit'], 'a push that failed fails the command' );
        $this->assertStringContainsString( 'the push failed', implode( ' ', $pending['errors'] ) );
        $this->assertSame( array( $pending['run_id'] ), array_column( expStaticStatus::pending( $this->db, 'test-site' ), 'run_id' ) );
        $this->assertTrue( rename( $bare . '.away', $bare ) );
        $this->assertNotSame( $pending['commit_id'], $this->gitOk( $bare, array( 'rev-parse', 'refs/heads/gh-pages' ) ) );
        $retry = $this->publisher( $target )->retry();
        $this->assertSame( array(), $retry['errors'], implode( '; ', $retry['errors'] ) );
        $this->assertSame( 'pushed', $retry['push_state'] );
        $this->assertSame( array( $pending['run_id'] ), $retry['runs'] );
        $this->assertSame( array(), expStaticStatus::pending( $this->db, 'test-site' ) );
        $this->assertSame( $pending['commit_id'], $this->gitOk( $bare, array( 'rev-parse', 'refs/heads/gh-pages' ) ) );
        $this->assertSame( $pending['commit_id'], $this->gitOk( $bare, array( 'rev-parse', 'static/test-site/3^{commit}' ) ) );
        $this->assertSame( 'no run of test-site waits for a push', $this->publisher( $target )->retry()['messages'][0] );

        // SP-05
        $tagsBefore = $this->tagObjects( $checkout );
        $headBefore = $this->gitOk( $checkout, array( 'rev-parse', 'HEAD' ) );
        $plan = $this->publisher( $target )->rollback( '1' );
        $this->assertSame( array(), $plan['errors'], implode( '; ', $plan['errors'] ) );
        $this->assertSame( 'planned', $plan['state'] );
        $this->assertSame( 'static/test-site/1', $plan['from_tag'] );
        $this->assertSame( 1, $plan['counts']['added'], 'news/item-1 comes back' );
        $this->assertSame( $headBefore, $this->gitOk( $checkout, array( 'rev-parse', 'HEAD' ) ), 'the dry run changes nothing' );
        $this->assertCount( 4, expStaticStatus::runs( $this->db, 'test-site', 0 ), 'the dry run is not recorded (publish 1, 2, the unchanged one, 3)' );

        $rolled = $this->publisher( $target )->rollback( 'static/test-site/1', array( 'apply' => true ) );
        $this->assertSame( array(), $rolled['errors'], implode( '; ', $rolled['errors'] ) );
        $this->assertSame( 'done', $rolled['state'] );
        $this->assertSame( 'static/test-site/4', $rolled['tag_name'] );
        $this->assertSame( 'pushed', $rolled['push_state'] );
        $this->assertSame( $headBefore, $this->gitOk( $checkout, array( 'rev-parse', 'HEAD^' ) ), 'a new commit on top: the history stays' );
        $this->assertSame( $this->gitOk( $checkout, array( 'rev-parse', 'static/test-site/1^{tree}' ) ), $this->gitOk( $checkout, array( 'rev-parse', 'HEAD^{tree}' ) ),
                           'the files of publish 1' );
        $this->assertFileExists( $checkout . '/news/item-1/index.html' );
        $this->assertSame( 'Roll back test-site to static/test-site/1', $this->gitOk( $checkout, array( 'log', '-1', '--format=%s' ) ) );
        $this->assertSame( $rolled['commit_id'], $this->gitOk( $bare, array( 'rev-parse', 'refs/heads/gh-pages' ) ) );
        $after = $this->tagObjects( $checkout );
        foreach ( $tagsBefore as $name => $sha )
            $this->assertSame( $sha, $after[$name], "$name never moved" );
        $this->assertSame( $after, $this->tagObjects( $bare ), 'the remote has the same tags' );
        $this->assertSame( '4', (string)count( $after ) );
        $runs = expStaticStatus::runs( $this->db, 'test-site', 0 );
        // the newest run; a publish in the same second may be listed first (runs are ordered by the second they started)
        $rollbackRun = array_values( array_filter( $runs, function ( $run ) use ( $rolled ) { return $run['run_id'] === $rolled['run_id']; } ) );
        $this->assertCount( 1, $rollbackRun );
        $this->assertSame( $runs[0]['started_at'], $rollbackRun[0]['started_at'], 'the rollback is the newest run' );
        $this->assertSame( array( 'rollback', 'done', 'pushed' ), array( $rollbackRun[0]['kind'], $rollbackRun[0]['state'], $rollbackRun[0]['push_state'] ) );
        $this->assertSame( 'system.staticpublish.rollback', end( $this->audits )[0] );
        $this->assertSame( 'static/test-site/1', end( $this->audits )[1]['x']['from_tag'] );
        $this->assertArrayHasKey( 'news/item-1/index.html', expStaticStatus::pages( $this->db, 'test-site' ) );
        $this->assertSame( '', $this->gitOk( $checkout, array( 'status', '--porcelain' ) ) );
        $unknown = $this->publisher( $target )->rollback( '9', array( 'apply' => true ) );
        $this->assertStringContainsString( 'there is no tag static/test-site/9', implode( ' ', $unknown['errors'] ) );

        // every update of the remote's branch was a fast-forward (it refuses anything else): one line of history
        $this->assertSame( 4, (int)$this->gitOk( $bare, array( 'rev-list', '--count', 'refs/heads/gh-pages' ) ), 'publish 1, 2, 3 and the rollback' );
    }

    /** SP-06 */
    public function testFolderDocsOnMainKeepsTheRemotesFiles()
    {
        $bare = $this->bare( 'docs-remote' );
        $seed = $this->runnerDir() . '/seed';
        mkdir( $seed );
        $this->gitOk( $seed, array( 'init', '--quiet' ) );
        $this->gitOk( $seed, array( 'checkout', '--quiet', '-b', 'main' ) );
        file_put_contents( "$seed/README.md", "# The site's code\n" );
        $this->gitOk( $seed, array( 'add', 'README.md' ) );
        $this->gitOk( $seed, array( '-c', 'user.name=Owner', '-c', 'user.email=owner@example.com', '-c', 'commit.gpgsign=false', 'commit', '--quiet', '-m', 'Start' ) );
        $this->gitOk( $seed, array( 'push', '--quiet', $bare, 'main' ) );
        $start = $this->gitOk( $seed, array( 'rev-parse', 'HEAD' ) );

        $target = $this->target( array( 'Remote' => $bare, 'Branch' => 'main', 'Folder' => 'docs', 'Kind' => 'github-pages', 'Extras' => array( 'nojekyll', '404', 'manifest' ) ) );
        $report = $this->publisher( $target )->publish( array( 'build' => true ) );
        $this->assertSame( array(), $report['errors'], implode( '; ', $report['errors'] ) );
        $this->assertSame( 'pushed', $report['push_state'] );
        $checkout = $this->runnerDir() . '/out/test-site';
        $this->assertFileExists( "$checkout/docs/index.html" );
        $this->assertFileDoesNotExist( "$checkout/index.html" );
        $this->assertSame( $start, $this->gitOk( $bare, array( 'rev-parse', 'refs/heads/main^' ) ), 'on top of the remote\'s history' );
        $files = explode( "\n", $this->gitOk( $bare, array( 'ls-tree', '-r', '--name-only', 'refs/heads/main' ) ) );
        $this->assertContains( 'README.md', $files, 'the remote\'s own file stays' );
        $this->assertContains( 'docs/index.html', $files );
        $this->assertContains( 'docs/.static-publish/manifest.json', $files );
        foreach ( $files as $file )
            $this->assertTrue( $file === 'README.md' || strpos( $file, 'docs/' ) === 0, "$file is in docs/" );
        $this->assertFileExists( "$checkout/README.md" );
    }

    /** SP-07 */
    public function testLockAndFailedBuild()
    {
        $bare = $this->bare( 'lock-remote' );
        $target = $this->target( array( 'Remote' => $bare ) );
        $problem = '';
        $lock = expStaticLock::acquire( $this->runnerDir() . '/out', 'test-site', 'build', $problem );
        $this->assertNotNull( $lock, $problem );
        $refused = $this->publisher( $target )->publish( array( 'build' => true ) );
        $this->assertStringContainsString( 'another process works on the target test-site (build by process', implode( ' ', $refused['errors'] ) );
        $lock->release();

        $this->site['/about-us']['cookies'] = array( 'eZSESSID' );
        $failed = $this->publisher( $target )->publish( array( 'build' => true ) );
        $this->assertSame( 'failed', $failed['state'] );
        $this->assertStringContainsString( 'nothing is committed or pushed', implode( ' ', $failed['errors'] ) );
        $this->assertFileDoesNotExist( $this->runnerDir() . '/out/test-site/.git' );
        $this->assertSame( '', $this->gitOk( $bare, array( 'for-each-ref' ) ), 'nothing reached the remote' );
        $runs = expStaticStatus::runs( $this->db, 'test-site' );
        $this->assertSame( 'failed', $runs[0]['state'] );
        $this->assertSame( 'failure', end( $this->audits )[1]['result'] );
    }

    /** SP-08 */
    public function testPreviewDiscardAndHistoryLists()
    {
        $bare = $this->bare( 'preview-remote' );
        $target = $this->target( array( 'Remote' => $bare ) );
        $checkout = $this->runnerDir() . '/out/test-site';
        $publisher = $this->publisher( $target );

        // a build before the first publish: everything is new; throwing it away leaves no file of it
        $test = $this;
        $build = function () use ( $test, $target ) {
            $b = new expStaticBuild( $target, array( 'root' => $this->runnerDir() . '/root', 'var_dir' => 'var/test', 'work_dir' => $this->runnerDir() . '/out',
                                                     'db' => null, 'fetcher' => function ( $url ) use ( $test ) { return $test->answer( $url ); },
                                                     'base_path' => '', 'context' => self::CONTEXT ) );
            $report = $b->run();
            $test->assertTrue( $report['applied'], implode( '; ', $report['errors'] ) );
        };
        $build();
        $this->assertNull( $publisher->repository()->committedManifest() );
        $first = expStaticAdmin::diffDetails( null, expStaticManifest::load( $checkout ) );
        $this->assertGreaterThan( 4, $first['counts']['added'] );
        $result = array();
        $this->assertTrue( $publisher->repository()->discard( $result ) );
        $this->assertFileDoesNotExist( "$checkout/index.html" );
        $this->assertFileDoesNotExist( "$checkout/.static-publish/manifest.json" );

        // published, then the site changes and a new build is thrown away: the folder holds the publish again
        $report = $this->publisher( $target )->publish( array( 'build' => true ) );
        $this->assertSame( 'static/test-site/1', $report['tag_name'], implode( '; ', $report['errors'] ) );
        $this->site['/news'] = self::page( 'News', '<h1>All the news</h1>' );
        $this->site['/contact'] = self::page( 'Contact', '<h1>Contact</h1>' );
        $this->site['/'] = self::page( 'Home', '<h1>Home</h1><a href="/about-us">About</a> <a href="/news">News</a> <a href="/contact">Contact</a>' );
        $build();
        $repo = $publisher->repository();
        $diff = expStaticAdmin::diffDetails( $repo->committedManifest(), expStaticManifest::load( $checkout ) );
        $this->assertContains( 'contact/index.html', array_column( $diff['added'], 'path' ) );
        $this->assertContains( 'news/index.html', array_column( $diff['changed'], 'path' ) );
        $this->assertContains( 'news/item-1/index.html', array_column( $diff['removed'], 'path' ) );
        $result = array();
        $this->assertTrue( $repo->discard( $result ), implode( '; ', $result['problems'] ) );
        $this->assertFileDoesNotExist( "$checkout/contact/index.html" );
        $this->assertFileExists( "$checkout/news/item-1/index.html" );
        $this->assertSame( '', $this->gitOk( $checkout, array( 'status', '--porcelain' ) ), 'nothing left that the last publish does not hold' );

        // the lists of the history
        $tags = $repo->tagList();
        $this->assertSame( array( 'static/test-site/1' ), array_column( $tags, 'name' ) );
        $this->assertSame( $report['commit_id'], $tags[0]['commit'], 'the commit, not the tag object' );
        $this->assertSame( 1, $tags[0]['number'] );
        $this->assertGreaterThan( 0, $tags[0]['time'] );
        $commits = $repo->commits( 5 );
        $this->assertCount( 1, $commits );
        $this->assertSame( 'Static Test', $commits[0]['author'] );
        $this->assertStringStartsWith( 'Publish test-site', $commits[0]['subject'] );
    }
}
