<?php
/**
 * The PostgreSQL client programs the backup, the caption check and the rollback run, without a database: pg_dump
 * refuses a server of a newer major version, so the program has to be at least as new as the server (a host with
 * PostgreSQL 13 tools on PATH and a database server upgraded to 18).
 *
 *  PT-01 the first program on the list whose major version reaches the server's wins, PATH order kept
 *  PT-02 an older program on PATH is passed over for a newer versioned one
 *  PT-03 none new enough: the newest found, so the tool itself names the mismatch
 *  PT-04 nothing found: the bare name; unknown server version: the first program found
 *  PT-05 the version of a program from --version, a beta or rc server version reads as its major
 *  PT-06 psqlCommand() quotes the program and carries host, port, user and database
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expUpgradeBackupPostgresqlToolTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    private $dir;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
        require_once self::$root . '/kernel/classes/upgrade/expupgradebackup.php';
    }

    protected function setUp(): void
    {
        $this->dir = self::$root . '/var/tmp/expUpgradeBackupPostgresqlToolTest-' . getmypid();
        foreach ( array( 13, 17, 18, 19 ) as $major )
            $this->fakeTool( $major, 'pg_dump' );
    }

    protected function tearDown(): void
    {
        foreach ( glob( $this->dir . '/*/*' ) ?: array() as $file )
            @unlink( $file );
        foreach ( glob( $this->dir . '/*' ) ?: array() as $dir )
            @rmdir( $dir );
        @rmdir( $this->dir );
    }

    /** A program that answers --version as PostgreSQL's client of $major does. */
    private function fakeTool( $major, $name, $version = null )
    {
        $dir = $this->dir . '/pg' . $major;
        @mkdir( $dir, 0700, true );
        $file = $dir . '/' . $name;
        file_put_contents( $file, "#!/bin/sh\necho '$name (PostgreSQL) " . ( $version ?: $major . '.2' ) . "'\n" );
        chmod( $file, 0700 );
        return $file;
    }

    private function dirs( array $majors )
    {
        $dirs = array();
        foreach ( $majors as $major )
            $dirs[] = $this->dir . '/pg' . $major;
        return $dirs;
    }

    /** PT-01 */
    public function testFirstNewEnoughWins()
    {
        $this->assertSame( $this->dir . '/pg18/pg_dump', expUpgradeBackup::postgresqlTool( 'pg_dump', 18, $this->dirs( array( 18, 19 ) ) ) );
        $this->assertSame( $this->dir . '/pg19/pg_dump', expUpgradeBackup::postgresqlTool( 'pg_dump', 18, $this->dirs( array( 19, 18 ) ) ) );
        $this->assertSame( $this->dir . '/pg13/pg_dump', expUpgradeBackup::postgresqlTool( 'pg_dump', 13, $this->dirs( array( 13, 18 ) ) ) );
    }

    /** PT-02 */
    public function testOlderProgramOnPathIsPassedOver()
    {
        $this->assertSame( $this->dir . '/pg18/pg_dump', expUpgradeBackup::postgresqlTool( 'pg_dump', 18, $this->dirs( array( 13, 17, 18 ) ) ) );
        $this->assertSame( $this->dir . '/pg19/pg_dump', expUpgradeBackup::postgresqlTool( 'pg_dump', 19, $this->dirs( array( 13, 17, 18, 19 ) ) ) );
    }

    /** PT-03 */
    public function testNoneNewEnoughGivesTheNewest()
    {
        $this->assertSame( $this->dir . '/pg17/pg_dump', expUpgradeBackup::postgresqlTool( 'pg_dump', 19, $this->dirs( array( 13, 17 ) ) ) );
    }

    /** PT-04 */
    public function testNothingFoundOrUnknownServer()
    {
        $this->assertSame( 'pg_restore', expUpgradeBackup::postgresqlTool( 'pg_restore', 18, $this->dirs( array( 13, 18 ) ) ) );
        $this->assertSame( $this->dir . '/pg13/pg_dump', expUpgradeBackup::postgresqlTool( 'pg_dump', null, $this->dirs( array( 13, 18 ) ) ) );
        $this->assertSame( 'pg_dump', expUpgradeBackup::postgresqlTool( 'pg_dump', 18, array( $this->dir . '/missing' ) ) );
    }

    /** PT-05 */
    public function testVersions()
    {
        $this->assertSame( 18, expUpgradeBackup::postgresqlToolMajor( $this->fakeTool( 'beta', 'pg_dump', '18.1 (Debian 18.1-1.pgdg13+2)' ) ) );
        $this->assertSame( 19, expUpgradeBackup::postgresqlToolMajor( $this->fakeTool( 'rc', 'psql', '19rc1' ) ) );
        $this->assertSame( 0, expUpgradeBackup::postgresqlToolMajor( $this->dir . '/missing/pg_dump' ) );

        foreach ( array( '18.1' => 18, '19rc1' => 19, '19beta2' => 19, '13.23' => 13, '' => null ) as $version => $major )
        {
            $db = new expUpgradeBackupPostgresqlToolTestDB( $version );
            $this->assertSame( $major, expUpgradeBackup::postgresqlServerMajor( $db ), "server version '$version'" );
        }
        $this->assertNull( expUpgradeBackup::postgresqlServerMajor( null ) );
    }

    /** PT-06 */
    public function testPsqlCommand()
    {
        $db = new expUpgradeBackupPostgresqlToolTestDB( '13.23' );
        $db->Server = 'db.example';
        $db->Port = 5433;
        $db->User = "o'neil";
        $command = expUpgradeBackup::psqlCommand( $db, 'site db', '-X -q -v ON_ERROR_STOP=1' );
        $this->assertMatchesRegularExpression( "/^'[^']*psql' -X -q -v ON_ERROR_STOP=1 -h 'db\\.example' -p 5433 -U 'o'\\\\''neil' -d 'site db'$/", $command );
        $db->Port = '';
        $this->assertStringNotContainsString( ' -p ', expUpgradeBackup::psqlCommand( $db, 'x' ) );
    }
}

/** A connection that only says its server version. */
class expUpgradeBackupPostgresqlToolTestDB
{
    public $Server = 'localhost';
    public $Port = 5432;
    public $User = 'postgres';
    private $version;

    public function __construct( $version )
    {
        $this->version = $version;
    }

    public function databaseServerVersion()
    {
        return $this->version === '' ? false : array( 'string' => $this->version, 'values' => explode( '.', $this->version ) );
    }
}
