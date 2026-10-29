<?php
/**
 * The table of the passkeys (exppasskey) is the same everywhere it is written: the persistent object, the schema
 * definition share/db_schema.dba (which installs every engine, Oracle and MongoDB included), the kernel schemas of
 * MySQL, PostgreSQL and SQLite, and the 6.0.0 to 6.0.15 update files. The SQLite update runs twice on a small
 * database it builds in var/tmp and changes nothing the second time. Needs no installation.
 *
 *  PS-01 the columns of expPasskey::definition() are those of share/db_schema.dba, with the two indexes
 *  PS-02 the three kernel schemas create the table with the same columns and indexes
 *  PS-03 the update files create it only when it is missing (IF NOT EXISTS, a guarded DO block on PostgreSQL)
 *  PS-04 the SQLite update runs twice; a credential is stored once
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class PasskeySchemaSqlTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    private static $columns = array( 'aaguid', 'algorithm', 'backup_eligible', 'backup_state', 'created', 'credential_hash',
                                     'credential_id', 'id', 'last_ip', 'last_used', 'name', 'public_key', 'sign_count',
                                     'transports', 'user_handle', 'user_id' );

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    /** PS-01 */
    public function testDefinitionAndSchemaAgree()
    {
        $schema = null;
        include self::$root . '/share/db_schema.dba';
        $this->assertArrayHasKey( 'exppasskey', $schema );
        $this->assertSame( self::$columns, array_keys( $schema['exppasskey']['fields'] ) );
        $this->assertSame( 'auto_increment', $schema['exppasskey']['fields']['id']['type'] );
        $this->assertSame( 'unique', $schema['exppasskey']['indexes']['exppasskey_credential']['type'] );
        $this->assertSame( array( 'credential_hash' ), $schema['exppasskey']['indexes']['exppasskey_credential']['fields'] );
        $this->assertSame( array( 'user_id' ), $schema['exppasskey']['indexes']['exppasskey_user']['fields'] );
        $definition = expPasskey::definition();
        $persistent = array_keys( $definition['fields'] );
        sort( $persistent );
        $this->assertSame( self::$columns, $persistent );
        $this->assertSame( 'exppasskey', $definition['name'] );
    }

    /** PS-02 */
    public function testKernelSchemas()
    {
        $files = array( 'mysql' => 'kernel/sql/mysql/kernel_schema.sql',
                        'postgresql' => 'kernel/sql/postgresql/kernel_schema.sql',
                        'sqlite' => 'kernel/sql/sqlite/schema.sql' );
        foreach ( $files as $engine => $file )
        {
            $sql = file_get_contents( self::$root . '/' . $file );
            $this->assertMatchesRegularExpression( '/CREATE TABLE `?exppasskey`? \((.*?)\n\)/s', $sql, $engine );
            preg_match( '/CREATE TABLE `?exppasskey`? \((.*?)\n\)/s', $sql, $m );
            preg_match_all( '/^[\s,]*`?([a-z_]+)`? (?:int|integer|bigint|varchar|character|longtext|text)/m', $m[1], $cols );
            $this->assertSame( self::$columns, $cols[1], $engine );
            $this->assertMatchesRegularExpression( '/UNIQUE (?:KEY|INDEX) exppasskey_credential/', $sql, $engine );
            $this->assertMatchesRegularExpression( '/(?:KEY|INDEX) exppasskey_user/', $sql, $engine );
        }
        $pg = file_get_contents( self::$root . '/kernel/sql/postgresql/kernel_schema.sql' );
        $this->assertStringContainsString( 'CREATE SEQUENCE exppasskey_id_seq', $pg );
        $this->assertStringContainsString( 'ADD CONSTRAINT exppasskey_pkey PRIMARY KEY (id)', $pg );
        $this->assertStringContainsString( "setval('exppasskey_id_seq'", file_get_contents( self::$root . '/kernel/sql/postgresql/setval.sql' ) );
        $this->assertStringContainsString( "setval('exppasskey_id_seq'", file_get_contents( self::$root . '/kernel/sql/postgresql/sequence.sql.template' ) );
    }

    /** PS-03 */
    public function testUpdateFilesAreGuarded()
    {
        $mysql = file_get_contents( self::$root . '/update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertStringContainsString( 'CREATE TABLE IF NOT EXISTS exppasskey', $mysql );
        $pg = file_get_contents( self::$root . '/update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $section = substr( $pg, strpos( $pg, '-- Passkeys' ) );
        $this->assertMatchesRegularExpression( "/IF NOT EXISTS \( SELECT 1 FROM information_schema.tables WHERE table_schema = current_schema\(\) AND table_name = 'exppasskey' \)/", $section );
        $this->assertStringContainsString( 'CREATE UNIQUE INDEX exppasskey_credential', $section );
        $this->assertStringContainsString( 'END' . "\n" . '$$;', $section );
        $sqlite = file_get_contents( self::$root . '/update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertStringContainsString( 'CREATE TABLE IF NOT EXISTS exppasskey', $sqlite );
        $this->assertStringContainsString( 'CREATE UNIQUE INDEX IF NOT EXISTS exppasskey_credential', $sqlite );
    }

    /** PS-04 */
    public function testSqliteUpdateRunsTwice()
    {
        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'no pdo_sqlite' );
        $dir = self::$root . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $file = $dir . '/passkey-update-test-' . getmypid() . '.db';
        $pdo = new PDO( 'sqlite:' . $file );
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $apply = function () use ( $pdo ) {
            $sql = file_get_contents( self::$root . '/update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
            $section = substr( $sql, strpos( $sql, '-- Passkeys' ) );
            $body = preg_replace( '/^--.*$/m', '', $section );
            foreach ( preg_split( '/;\s*$/m', $body ) as $statement )
                if ( trim( $statement ) !== '' )
                    $pdo->exec( $statement );
        };
        $apply();
        $cols = array_column( $pdo->query( 'PRAGMA table_info(exppasskey)' )->fetchAll( PDO::FETCH_ASSOC ), 'name' );
        $this->assertSame( self::$columns, $cols );
        $pdo->exec( "INSERT INTO exppasskey (user_id, name, credential_hash, credential_id, public_key) VALUES (14, 'a', 'aa', 'x', 'y')" );
        $apply();
        $this->assertSame( '1', (string)$pdo->query( 'SELECT COUNT(*) FROM exppasskey' )->fetchColumn(), 'the second run kept the row' );
        $refused = false;
        try { $pdo->exec( "INSERT INTO exppasskey (user_id, name, credential_hash) VALUES (15, 'b', 'aa')" ); }
        catch ( PDOException $e ) { $refused = true; }
        $this->assertTrue( $refused, 'a credential is stored once' );
        $pdo = null;
        @unlink( $file );
    }
}
