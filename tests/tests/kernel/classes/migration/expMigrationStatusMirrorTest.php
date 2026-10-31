<?php
/**
 * The status mirror of the migration record (expMigrationStatusMirror): migrations/status/<environment>.json in the
 * schema exp-migration-status/1 and the run logs migrations/runs/<run-id>.log. No database; the files are written to a
 * directory under var/tmp that is removed afterwards.
 *
 *  MS-01 document(): the shape of exp-migration-status/1, sorted, caption rows left out, times in UTC, the summary
 *  MS-02 the document is valid against share/migration/schema/exp-migration-status-1.json; the schema refuses a
 *        document with a user name or a message, an unknown schema id or a bad time
 *  MS-03 no secrets and no personal data: run_by and messages never reach the status file
 *  MS-04 write() and read(): the file under status/, the same bytes when nothing changed, a newline at the end
 *  MS-05 log(): one line per call, "<UTC time> <level> <text>", on one line, cleaned of secret values, credentials,
 *        password pairs, bearer tokens and e-mail addresses
 *  MS-06 names: an environment or run id that is no name is refused, so nothing is written outside the directory
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expMigrationStatusMirrorTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    protected function setUp(): void
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/migration-mirror-' . getmypid() . '-' . substr( md5( $this->name() ), 0, 8 );
    }

    protected function tearDown(): void
    {
        if ( $this->dir && is_dir( $this->dir ) )
            self::remove( $this->dir );
    }

    private static function remove( $path )
    {
        if ( is_dir( $path ) && !is_link( $path ) )
        {
            foreach ( scandir( $path ) as $entry )
                if ( $entry !== '.' && $entry !== '..' )
                    self::remove( $path . '/' . $entry );
            rmdir( $path );
        }
        else if ( file_exists( $path ) || is_link( $path ) )
            unlink( $path );
    }

    private static function rows()
    {
        return array(
            array( 'source' => 'site', 'migration_id' => '20261031120000_b', 'stage' => 'migration', 'file_path' => 'migrations/20261031120000_b.php',
                   'checksum' => str_repeat( 'a', 64 ), 'state' => 'done', 'environment' => 'staging', 'run_id' => 'r-1', 'started_at' => 1761912000,
                   'finished_at' => 1761912002, 'duration_ms' => 2000, 'run_by' => 'web jane.doe', 'caption_id' => 'cap-1',
                   'revision' => str_repeat( 'f', 40 ), 'engine' => 'mysql', 'message' => 'password=hunter2 for jane@example.com' ),
            array( 'source' => 'kernel', 'migration_id' => '6.0.15-trash-columns', 'stage' => 'database', 'version' => '6.0.15', 'state' => 'done',
                   'finished_at' => 1761900000, 'checksum' => 'not hex', 'run_by' => 'cli root' ),
            array( 'source' => 'kernel', 'migration_id' => 'caption:cap-1', 'stage' => 'caption', 'state' => 'pre', 'finished_at' => 1761911990 ),
            array( 'source' => 'site', 'migration_id' => '20261031110000_a', 'stage' => 'migration', 'state' => 'failed', 'finished_at' => 1761911000 ),
        );
    }

    private static function runs()
    {
        return array(
            array( 'run_id' => 'r-0', 'state' => 'failed', 'started_at' => 1761911000, 'finished_at' => 1761911001, 'run_by' => 'cli root',
                   'message' => 'secret stuff' ),
            array( 'run_id' => 'r-1', 'state' => 'done', 'started_at' => 1761912000, 'finished_at' => 1761912005, 'caption_pre' => 'cap-1',
                   'caption_post' => 'cap-2', 'commit_id' => 'abc123', 'tag_name' => 'migration-r-1', 'push_state' => 'pushed',
                   'log_file' => 'runs/r-1.log', 'environment' => 'staging' ),
            array( 'run_id' => '../escape', 'state' => 'done' ),
        );
    }

    /** MS-01 */
    public function testDocument()
    {
        $doc = expMigrationStatusMirror::document( 'Staging', self::rows(), self::runs(), 'mysql' );
        $this->assertSame( array( 'schema', 'environment', 'engine', 'updated_at', 'summary', 'migrations', 'runs' ), array_keys( $doc ) );
        $this->assertSame( 'exp-migration-status/1', $doc['schema'] );
        $this->assertSame( 'staging', $doc['environment'] );
        $this->assertSame( '2025-10-31T12:00:05Z', $doc['updated_at'], 'the newest time of the record, not "now"' );
        $this->assertSame( array( 'kernel/6.0.15-trash-columns', 'site/20261031110000_a', 'site/20261031120000_b' ),
                           array_map( function ( $m ) { return $m['source'] . '/' . $m['id']; }, $doc['migrations'] ), 'sorted, caption rows left out' );
        $this->assertSame( array( 'done' => 2, 'failed' => 1, 'total' => 3 ), $doc['summary'] );
        $b = $doc['migrations'][2];
        $this->assertSame( array( 'source', 'id', 'state', 'file', 'checksum', 'stage', 'version', 'run_id', 'started_at', 'finished_at',
                                  'duration_ms', 'caption_id', 'revision' ), array_keys( $b ) );
        $this->assertSame( '2025-10-31T12:00:00Z', $b['started_at'] );
        $this->assertSame( 'migrations/20261031120000_b.php', $b['file'] );
        $this->assertSame( '', $doc['migrations'][0]['checksum'], 'a checksum that is not SHA-256 hex is left empty' );
        $this->assertNull( $doc['migrations'][0]['started_at'] );
        $this->assertSame( array( 'r-1', 'r-0' ), array_column( $doc['runs'], 'run_id' ), 'newest first, a run id that is no name left out' );
        $this->assertSame( array( 'run_id' => 'r-1', 'state' => 'done', 'started_at' => '2025-10-31T12:00:00Z', 'finished_at' => '2025-10-31T12:00:05Z',
                                  'caption_pre' => 'cap-1', 'caption_post' => 'cap-2', 'commit' => 'abc123', 'tag' => 'migration-r-1',
                                  'push_state' => 'pushed', 'log' => 'runs/r-1.log' ), $doc['runs'][0] );
        $empty = expMigrationStatusMirror::document( '', array(), array(), 'sqlite' );
        $this->assertSame( 'default', $empty['environment'] );
        $this->assertNull( $empty['updated_at'] );
        $this->assertSame( array( 'total' => 0 ), $empty['summary'] );
    }

    /** MS-02 */
    public function testSchema()
    {
        $file = dirname( __DIR__, 5 ) . '/' . expMigrationStatusMirror::SCHEMA_FILE;
        $schema = json_decode( file_get_contents( $file ), true );
        $this->assertIsArray( $schema, 'the schema file is JSON' );
        $this->assertSame( 'exp-migration-status/1', $schema['properties']['schema']['const'] );
        $doc = expMigrationStatusMirror::document( 'staging', self::rows(), self::runs(), 'mysql' );
        $this->assertSame( array(), expMigrationStatusMirror::validate( $doc ) );
        $this->assertSame( array(), expMigrationStatusMirror::validate( json_decode( expMigrationStatusMirror::encode( $doc ), true ) ), 'after a round trip through JSON' );
        $this->assertSame( array(), expMigrationStatusMirror::validate( expMigrationStatusMirror::document( '', array(), array(), '' ) ) );

        $bad = $doc;
        $bad['migrations'][0]['run_by'] = 'cli root';
        $this->assertNotEmpty( expMigrationStatusMirror::validate( $bad ), 'a user name is refused' );
        $bad = $doc;
        $bad['runs'][0]['message'] = 'x';
        $this->assertNotEmpty( expMigrationStatusMirror::validate( $bad ), 'a message is refused' );
        $bad = $doc;
        $bad['schema'] = 'exp-migration-status/2';
        $this->assertNotEmpty( expMigrationStatusMirror::validate( $bad ) );
        $bad = $doc;
        $bad['updated_at'] = '2026-10-31 12:00';
        $this->assertNotEmpty( expMigrationStatusMirror::validate( $bad ) );
    }

    /** MS-03 */
    public function testNoPersonalDataInTheStatus()
    {
        $json = expMigrationStatusMirror::encode( expMigrationStatusMirror::document( 'staging', self::rows(), self::runs(), 'mysql' ) );
        foreach ( array( 'jane', 'root', 'hunter2', 'example.com', 'secret stuff', 'password' ) as $text )
            $this->assertStringNotContainsString( $text, $json );
    }

    /** MS-04 */
    public function testWriteAndRead()
    {
        $mirror = new expMigrationStatusMirror( $this->dir );
        $this->assertNull( $mirror->read( 'staging' ) );
        $file = $mirror->write( 'staging', self::rows(), self::runs(), 'mysql' );
        $this->assertSame( $this->dir . '/status/staging.json', $file );
        $text = file_get_contents( $file );
        $this->assertStringEndsWith( "}\n", $text );
        $this->assertStringContainsString( '"file": "migrations/20261031120000_b.php"', $text, 'slashes as they are, pretty' );
        $this->assertSame( expMigrationStatusMirror::document( 'staging', self::rows(), self::runs(), 'mysql' ), $mirror->read( 'staging' ) );
        $before = md5_file( $file );
        $mirror->write( 'staging', array_reverse( self::rows() ), array_reverse( self::runs() ), 'mysql' );
        $this->assertSame( $before, md5_file( $file ), 'the same record in another order gives the same bytes' );
        $this->assertSame( array( 'staging.json' ), array_values( array_diff( scandir( $this->dir . '/status' ), array( '.', '..' ) ) ), 'no temporary file left' );
        file_put_contents( $this->dir . '/status/broken.json', '{"schema":"other"}' );
        $this->assertNull( $mirror->read( 'broken' ), 'a file that is not valid is not read' );
    }

    /** MS-05 */
    public function testLog()
    {
        $mirror = new expMigrationStatusMirror( $this->dir );
        $line = $mirror->log( 'r-1', 'info', "applied 20261031120000_b\nin 812 ms", array(), array(), 1761912000 );
        $this->assertSame( '2025-10-31T12:00:00Z info applied 20261031120000_b | in 812 ms', $line );
        $mirror->log( 'r-1', 'shout', 'connect to mysql://app:s3cr3t@db.example/site with token=abc123 and "Authorization: Bearer eyJhbGc.x-y" for ops@example.org and password: \'p w\'',
                      array(), array( 'tops3cret' ), 1761912001 );
        $mirror->log( 'r-1', 'error', 'value tops3cret leaked', array(), array( 'tops3cret' ), 1761912002 );
        $lines = file( $this->dir . '/runs/r-1.log', FILE_IGNORE_NEW_LINES );
        $this->assertCount( 3, $lines );
        $this->assertSame( '2025-10-31T12:00:01Z info connect to mysql://[credentials]@db.example/site with token=[secret] and "Authorization: Bearer [secret]" for [e-mail] and password: [secret]', $lines[1], 'an unknown level is info' );
        $this->assertSame( '2025-10-31T12:00:02Z error value [secret] leaked', $lines[2] );
        foreach ( array( 's3cr3t', 'abc123', 'eyJhbGc', 'ops@', 'p w', 'tops3cret' ) as $text )
            $this->assertStringNotContainsString( $text, file_get_contents( $this->dir . '/runs/r-1.log' ) );
        $this->assertSame( 'runs/r-1.log', expMigrationStatusMirror::logPath( 'r-1' ) );
        $this->assertSame( 'plain text stays', expMigrationStatusMirror::clean( 'plain text stays' ) );
    }

    /** MS-06 */
    public function testNames()
    {
        $mirror = new expMigrationStatusMirror( $this->dir );
        $this->assertSame( $this->dir . '/status/default.json', $mirror->statusFile( '' ) );
        $this->assertSame( $this->dir . '/status/prod-eu.1.json', $mirror->statusFile( 'Prod-EU.1' ) );
        foreach ( array( '../etc', 'a/b', '.hidden', 'with space', str_repeat( 'a', 51 ) ) as $name )
        {
            try
            {
                $mirror->statusFile( $name );
                $this->fail( "environment $name accepted" );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertStringContainsString( 'not an environment name', $e->getMessage() );
            }
        }
        foreach ( array( '../x', 'a/b', '', '.x' ) as $id )
        {
            try
            {
                $mirror->log( $id, 'info', 'x' );
                $this->fail( "run id $id accepted" );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertStringContainsString( 'not a run id', $e->getMessage() );
            }
        }
        $this->assertFalse( is_dir( $this->dir ), 'nothing was written' );
    }
}
