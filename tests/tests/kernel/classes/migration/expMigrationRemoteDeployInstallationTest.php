<?php
/**
 * Remote deploy of migrations to real throwaway destination INSTALLATIONS (not stand-in scripts): one on SQLite, one
 * on PostgreSQL (in Docker), each running the kernel's own exp:migration:status, migrate, rollback and heal with its
 * own database, captions and lock; the HTTP trigger is the real view migration/trigger of the SQLite installation
 * served by PHP's built-in server. Skipped unless the installations are given (they are made and started by the
 * project's integration script):
 *
 *   EXP_RD_DEST_SQLITE, EXP_RD_DEST_PGSQL   the roots of the two installations
 *   EXP_RD_DEST_PHP                          the PHP CLI that runs their console scripts
 *   EXP_RD_PG_DSN, EXP_RD_PG_USER, EXP_RD_PG_PASS   the PostgreSQL database of the second one
 *   EXP_RD_TRIGGER_URL, EXP_RD_TRIGGER_SECRET       the first one's migration/trigger and its secret
 *
 *  IN-01 — a migration deployed to both at once: each applied it in its own database, both in sync; a second deploy
 *          sends nothing and applies nothing
 *  IN-02 — it fails on PostgreSQL only: PostgreSQL restored itself, SQLite is taken back to its caption, both in sync
 *  IN-03 — self-healing on a real installation: a stale holder file of the run lock removed before the preflight
 *  IN-04 — the real HTTP trigger: refusals (GET, unsigned, replay), status with the host, then a migration run and
 *          taken back through it
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationRemoteDeployInstallationTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $source;

    /** @var string a suffix that keeps the ids and tables of this run apart from earlier runs */
    private $tag;

    protected function setUp(): void
    {
        foreach ( array( 'EXP_RD_DEST_SQLITE', 'EXP_RD_DEST_PGSQL', 'EXP_RD_DEST_PHP', 'EXP_RD_PG_DSN' ) as $name )
            if ( (string)getenv( $name ) === '' )
                $this->markTestSkipped( "$name is not set: no throwaway destination installations" );
        $this->tag = date( 'His' ) . mt_rand( 100, 999 );
        $this->source = dirname( __DIR__, 5 ) . '/var/tmp/expmigrationinstallation-' . getmypid() . '-' . $this->tag;
        mkdir( $this->source . '/migrations', 0775, true );
        expMigrationRemoteProcess::setRunner( null );
    }

    protected function tearDown(): void
    {
        if ( $this->source !== null && is_dir( $this->source ) )
        {
            // the migrations of this run leave the installations too (their records stay, as after any deploy)
            foreach ( glob( $this->source . '/migrations/*.yml' ) as $file )
                foreach ( array( 'EXP_RD_DEST_SQLITE', 'EXP_RD_DEST_PGSQL' ) as $env )
                    if ( is_file( getenv( $env ) . '/migrations/' . basename( $file ) ) )
                        unlink( getenv( $env ) . '/migrations/' . basename( $file ) );
            foreach ( glob( $this->source . '/migrations/*' ) as $file )
                unlink( $file );
            rmdir( $this->source . '/migrations' );
            foreach ( glob( $this->source . '/work/*/*' ) ?: array() as $file )
                is_file( $file ) && unlink( $file );
        }
    }

    private function remote( $name, $env )
    {
        return new expMigrationRemote( $name, array( 'Transport' => 'local', 'Path' => getenv( $env ), 'Php' => getenv( 'EXP_RD_DEST_PHP' ),
                                                     'Environment' => $name, 'AllowRootUser' => 'enabled' ) );
    }

    /** A YAML migration of the source; $pgsql in place of the SQL on PostgreSQL. */
    private function migration( $name, $table, $pgsql = null )
    {
        $id = '20261102' . substr( $this->tag, 0, 6 ) . '_wp18c_' . $name . substr( $this->tag, 6 ) . mt_rand( 10, 99 );
        $yaml = "# description: wp18c $name\nsteps:\n  - type: sql\n    sql: \"CREATE TABLE $table (id INTEGER)\"\n";
        if ( $pgsql !== null )
            $yaml .= "    postgresql: \"$pgsql\"\n";
        file_put_contents( $this->source . "/migrations/$id.yml", $yaml );
        return $id;
    }

    private function local( array $only )
    {
        $local = array();
        foreach ( glob( $this->source . '/migrations/*.yml' ) as $file )
        {
            $id = basename( $file, '.yml' );
            if ( !in_array( $id, $only, true ) )
                continue;
            $rel = 'migrations/' . basename( $file );
            $local[] = array( 'id' => $id, 'source' => 'site', 'files' => array( '*' => $rel ), 'paths' => array( $rel => $file ),
                              'checksums' => array( '*' => expMigrationDefinition::checksumOf( file_get_contents( $file ) ) ) );
        }
        return $local;
    }

    private function deploy( array $only, array $options = array() )
    {
        $remotes = array( 'sqlite' => $this->remote( 'sqlite', 'EXP_RD_DEST_SQLITE' ), 'pgsql' => $this->remote( 'pgsql', 'EXP_RD_DEST_PGSQL' ) );
        return ( new expMigrationRemoteDeploy( $remotes, $options + array(
            'root' => $this->source, 'work_dir' => $this->source . '/work', 'local' => $this->local( $only ), 'only' => $only, 'apply' => true,
            'version' => '6.0.15stable', 'retries' => 0, 'timeout' => 300, 'store' => false, 'audit' => function () {},
            'host' => array( 'php' => PHP_VERSION, 'disk_free' => null, 'extensions' => array() ), 'php_check' => 'major',
        ) ) )->run();
    }

    /** @return bool whether a table exists in each database: sqlite, pgsql */
    private function tables( $table )
    {
        $sqlite = new PDO( 'sqlite:' . getenv( 'EXP_RD_DEST_SQLITE' ) . '/var/storage/sqlite3/dest.db' );
        $pg = new PDO( getenv( 'EXP_RD_PG_DSN' ), getenv( 'EXP_RD_PG_USER' ), getenv( 'EXP_RD_PG_PASS' ) );
        return array( 'sqlite' => (int)$sqlite->query( "SELECT count(*) FROM sqlite_master WHERE type = 'table' AND name = '$table'" )->fetchColumn() === 1,
                      'pgsql' => (int)$pg->query( "SELECT count(*) FROM information_schema.tables WHERE table_name = '$table'" )->fetchColumn() === 1 );
    }

    /** IN-01, IN-02 */
    public function testDeployToSqliteAndPostgresqlInSyncAndAllTakenBack()
    {
        $table = 'wp18c_a_' . $this->tag;
        $a = $this->migration( 'marker', $table );
        $report = $this->deploy( array( $a ) );
        $this->assertSame( 'deployed', $report['state'], implode( "\n", $report['errors'] ) );
        $this->assertTrue( $report['in_sync'] );
        $this->assertSame( array( 'sqlite' => true, 'pgsql' => true ), $this->tables( $table ) );
        foreach ( array( 'sqlite', 'pgsql' ) as $id )
        {
            $this->assertSame( array( $a ), $report['remotes'][$id]['applied'], "$id applied it" );
            $this->assertNotSame( '', $report['remotes'][$id]['caption'], "$id took a caption before the run" );
            $this->assertSame( PHP_VERSION, $report['remotes'][$id]['host']['php'] );
        }
        $again = $this->deploy( array( $a ) );
        $this->assertSame( 'deployed', $again['state'], implode( "\n", $again['errors'] ) );
        $this->assertSame( array(), $again['remotes']['sqlite']['send'] );
        $this->assertSame( array(), $again['remotes']['pgsql']['applied'] );

        $table2 = 'wp18c_b_' . $this->tag;
        $b = $this->migration( 'breaks', $table2, "CREATE TABLE $table2 (id INTEGER); SELECT * FROM wp18c_no_such_table" );
        $report = $this->deploy( array( $b ) );
        $this->assertSame( 'rolled-back', $report['state'], json_encode( array( $report['errors'], array_map( function ( $r ) { return array( $r['state'], $r['restore'], $r['applied'], $r['problems'] ); }, $report['remotes'] ) ) ) );
        $this->assertTrue( $report['in_sync'] );
        $this->assertSame( 'rolled-back', $report['remotes']['sqlite']['state'] );
        $this->assertContains( $report['remotes']['pgsql']['state'], array( 'restored', 'unchanged' ) );
        $this->assertSame( array( 'sqlite' => false, 'pgsql' => false ), $this->tables( $table2 ), 'neither keeps the table of the failed deploy' );
        $this->assertSame( array( 'sqlite' => true, 'pgsql' => true ), $this->tables( $table ), 'the earlier deploy stays' );
    }

    /** IN-03 */
    public function testStaleHolderFileHealed()
    {
        $holder = getenv( 'EXP_RD_DEST_PGSQL' ) . '/var/upgrade.lock.holder';
        file_put_contents( $holder, json_encode( array( 'purpose' => 'migration run that crashed', 'pid' => 1, 'host' => 'elsewhere', 'started' => 1 ) ) );
        $c = $this->migration( 'healed', 'wp18c_c_' . $this->tag );
        $report = $this->deploy( array( $c ), array( 'heal' => array( 'stale-lock', 'caches' ) ) );
        $this->assertSame( 'deployed', $report['state'], implode( "\n", $report['errors'] ) );
        $this->assertStringContainsString( 'removed the holder file of an ended run', implode( "\n", $report['remotes']['pgsql']['fixes'] ) );
        $this->assertStringContainsString( 'heal caches: cleared', implode( "\n", $report['remotes']['sqlite']['fixes'] ) );
        $this->assertFileDoesNotExist( $holder );
    }

    /** IN-04 */
    public function testRealHttpTrigger()
    {
        $url = (string)getenv( 'EXP_RD_TRIGGER_URL' );
        $key = (string)getenv( 'EXP_RD_TRIGGER_SECRET' );
        if ( $url === '' || $key === '' )
            $this->markTestSkipped( 'no trigger server' );
        $post = function ( array $request, $signature = null, $method = 'POST' ) use ( $url, $key ) {
            $body = json_encode( $request, JSON_UNESCAPED_SLASHES );
            $config = expMigrationTrigger::curlConfig( $url, $key, $body );
            if ( $signature !== null )
                $config = preg_replace( '/^header = "X-Exp-Migration-Trigger: [^"]*"$/m', 'header = "X-Exp-Migration-Trigger: ' . $signature . '"', $config );
            $config = str_replace( 'request = "POST"', 'request = "' . $method . '"', $config ) . "write-out = \"\\n%{http_code}\"\n";
            $results = expMigrationRemoteProcess::runAll( array( 't' => array( array( 'argv' => array( 'curl', '-sS', '-K', '-' ), 'env' => array(), 'cwd' => null,
                                                                                       'stdin' => $config ) ) ), 1, 300 );
            $out = (string)$results['t']['stdout'];
            $cut = strrpos( $out, "\n" );
            return array( (int)substr( $out, $cut + 1 ), json_decode( substr( $out, 0, $cut ), true ), $config );
        };
        $this->assertSame( 405, $post( array( 'action' => 'status' ), null, 'GET' )[0] );
        $this->assertSame( 401, $post( array( 'action' => 'status' ), 'v1.1.' . str_repeat( 'a', 32 ) . '.' . str_repeat( 'b', 43 ) )[0] );
        list( $status, $report, $config ) = $post( array( 'action' => 'status', 'runs' => 1 ) );
        $this->assertSame( 200, $status, json_encode( $report ) );
        $this->assertSame( 'exp-migration/1', $report['schema'] );
        $this->assertSame( 'sqlite', $report['engine'] );
        $this->assertArrayHasKey( 'disk_free', $report['host'] );
        preg_match( '/X-Exp-Migration-Trigger: ([^"]+)/', $config, $m );
        $this->assertSame( 409, $post( array( 'action' => 'status', 'runs' => 1 ), $m[1] )[0], 'a replayed signature is refused' );

        // the file arrives as an FTP transfer would leave it; the trigger runs it there and takes it back
        $table = 'wp18c_t_' . $this->tag;
        $id = $this->migration( 'trigger', $table );
        copy( $this->source . "/migrations/$id.yml", getenv( 'EXP_RD_DEST_SQLITE' ) . "/migrations/$id.yml" );
        $runId = expMigrationContext::newRunId();
        list( $status, $dry ) = $post( array( 'action' => 'dry-run', 'only' => array( $id ) ) );
        $this->assertSame( 200, $status, json_encode( $dry ) );
        $this->assertSame( 'would-apply', $dry['migrations'][0]['action'] );
        list( $status, $run ) = $post( array( 'action' => 'apply', 'run_id' => $runId, 'only' => array( $id ) ) );
        $this->assertSame( 200, $status, json_encode( $run ) );
        $this->assertSame( 0, $run['exit'], json_encode( $run['errors'] ) );
        $this->assertTrue( $this->tables( $table )['sqlite'] );
        list( $status, $back ) = $post( array( 'action' => 'rollback', 'run_id' => $runId ) );
        $this->assertSame( 200, $status, json_encode( $back ) );
        $this->assertSame( 0, $back['exit'], json_encode( $back['errors'] ) );
        $this->assertFalse( $this->tables( $table )['sqlite'], 'taken back through the trigger' );
        unlink( getenv( 'EXP_RD_DEST_SQLITE' ) . "/migrations/$id.yml" );
    }
}
