<?php
/**
 * The table of the personal API keys (expapikey) is the same everywhere it is written: the persistent object, the
 * schema definition share/db_schema.dba (which installs every engine, Oracle and MongoDB included), the kernel schemas
 * of MySQL, PostgreSQL and SQLite, and the 6.0.0 to 6.0.15 update files. The SQLite update runs twice on a small
 * database it builds in var/tmp and changes nothing the second time. Needs no installation.
 *
 *  AS-01 the columns of expApiKey::definition() are those of share/db_schema.dba, with the two indexes
 *  AS-02 the three kernel schemas create the table with the same columns and indexes
 *  AS-03 the update files create it only when it is missing (IF NOT EXISTS, a guarded DO block on PostgreSQL)
 *  AS-04 the SQLite update runs twice; the prefix is unique
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ApiKeySchemaSqlTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    private static $columns = array( 'created', 'created_by', 'expires', 'id', 'key_prefix', 'last_ip', 'last_used', 'name',
                                     'revoked', 'revoked_by', 'salt', 'scopes', 'secret_hash', 'user_id' );

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    /** AS-01 */
    public function testDefinitionAndSchemaAgree()
    {
        $schema = null;
        include self::$root . '/share/db_schema.dba';
        $this->assertArrayHasKey( 'expapikey', $schema );
        $fields = array_keys( $schema['expapikey']['fields'] );
        $this->assertSame( self::$columns, $fields );
        $this->assertSame( 'auto_increment', $schema['expapikey']['fields']['id']['type'] );
        $this->assertSame( 'unique', $schema['expapikey']['indexes']['expapikey_prefix']['type'] );
        $this->assertSame( array( 'key_prefix' ), $schema['expapikey']['indexes']['expapikey_prefix']['fields'] );
        $this->assertSame( array( 'user_id' ), $schema['expapikey']['indexes']['expapikey_user']['fields'] );

        $definition = expApiKey::definition();
        $persistent = array_keys( $definition['fields'] );
        sort( $persistent );
        $this->assertSame( self::$columns, $persistent );
        $this->assertSame( 'expapikey', $definition['name'] );
    }

    /** AS-02 */
    public function testKernelSchemas()
    {
        $files = array( 'mysql' => 'kernel/sql/mysql/kernel_schema.sql',
                        'postgresql' => 'kernel/sql/postgresql/kernel_schema.sql',
                        'sqlite' => 'kernel/sql/sqlite/schema.sql' );
        foreach ( $files as $engine => $file )
        {
            $sql = file_get_contents( self::$root . '/' . $file );
            $this->assertMatchesRegularExpression( '/CREATE TABLE `?expapikey`? \((.*?)\n\)/s', $sql, $engine );
            preg_match( '/CREATE TABLE `?expapikey`? \((.*?)\n\)/s', $sql, $m );
            preg_match_all( '/^[\s,]*`?([a-z_]+)`? (?:int|integer|varchar|character)/m', $m[1], $cols );
            $this->assertSame( self::$columns, $cols[1], $engine );
            $this->assertMatchesRegularExpression( '/UNIQUE (?:KEY|INDEX) expapikey_prefix/', $sql, $engine );
            $this->assertMatchesRegularExpression( '/(?:KEY|INDEX) expapikey_user/', $sql, $engine );
        }
        $pg = file_get_contents( self::$root . '/kernel/sql/postgresql/kernel_schema.sql' );
        $this->assertStringContainsString( 'CREATE SEQUENCE expapikey_id_seq', $pg );
        $this->assertStringContainsString( 'ADD CONSTRAINT expapikey_pkey PRIMARY KEY (id)', $pg );
        $this->assertStringContainsString( "setval('expapikey_id_seq'", file_get_contents( self::$root . '/kernel/sql/postgresql/setval.sql' ) );
    }

    /** AS-03 */
    public function testUpdateFilesAreGuarded()
    {
        $mysql = file_get_contents( self::$root . '/update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertStringContainsString( 'CREATE TABLE IF NOT EXISTS expapikey', $mysql );
        $pg = file_get_contents( self::$root . '/update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $section = substr( $pg, strpos( $pg, '-- Personal API keys' ) );
        $this->assertMatchesRegularExpression( "/IF NOT EXISTS \( SELECT 1 FROM information_schema.tables WHERE table_schema = current_schema\(\) AND table_name = 'expapikey' \)/", $section );
        $this->assertStringContainsString( 'CREATE UNIQUE INDEX expapikey_prefix', $section );
        $this->assertStringContainsString( 'END' . "\n" . '$$;', $section );
        $sqlite = file_get_contents( self::$root . '/update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertStringContainsString( 'CREATE TABLE IF NOT EXISTS expapikey', $sqlite );
        $this->assertStringContainsString( 'CREATE UNIQUE INDEX IF NOT EXISTS expapikey_prefix', $sqlite );
    }

    /** AS-04 */
    public function testSqliteUpdateRunsTwice()
    {
        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'no pdo_sqlite' );
        $dir = self::$root . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $file = $dir . '/api-key-update-test-' . getmypid() . '.db';
        $pdo = new PDO( 'sqlite:' . $file );
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $apply = function () use ( $pdo ) {
            $sql = file_get_contents( self::$root . '/update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
            $section = substr( $sql, strpos( $sql, '-- Personal API keys' ) );
            $body = preg_replace( '/^--.*$/m', '', $section );
            foreach ( preg_split( '/;\s*$/m', $body ) as $statement )
                if ( trim( $statement ) !== '' )
                    $pdo->exec( $statement );
        };
        $apply();
        $cols = array_column( $pdo->query( 'PRAGMA table_info(expapikey)' )->fetchAll( PDO::FETCH_ASSOC ), 'name' );
        $this->assertSame( self::$columns, $cols );
        $pdo->exec( "INSERT INTO expapikey (user_id, name, key_prefix) VALUES (14, 'a', 'expk_aaaaaaaaaaaa')" );
        $apply();
        $this->assertSame( '1', (string)$pdo->query( 'SELECT COUNT(*) FROM expapikey' )->fetchColumn(), 'the second run kept the row' );
        $refused = false;
        try { $pdo->exec( "INSERT INTO expapikey (user_id, name, key_prefix) VALUES (15, 'b', 'expk_aaaaaaaaaaaa')" ); }
        catch ( PDOException $e ) { $refused = true; }
        $this->assertTrue( $refused, 'two keys cannot share a prefix' );
        $pdo = null;
        @unlink( $file );
    }
}
