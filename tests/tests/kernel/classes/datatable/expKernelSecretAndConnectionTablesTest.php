<?php
/**
 * The kernel tables of the secret store (exp_secret, doc/eng-us/features/6.0/secret-store.md) and of the named
 * connections of the data tables (exp_data_connection, doc/eng-us/features/6.0/data-tables.md). No installation: the
 * tables are checked in their schema files and in a SQLite database built in var/tmp.
 *
 *  ST-01 - Each table is the same in share/db_schema.dba, its persistent object, the kernel schemas of MySQL,
 *          PostgreSQL and SQLite (sequence, primary key, setval) and the 6.0.0 to 6.0.15 update files (guarded)
 *  ST-02 - The SQLite update runs twice and keeps the rows; the indexes exist, the unique ones refuse a duplicate
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expKernelSecretAndConnectionTablesTest extends PHPUnit\Framework\TestCase
{
    /** table => columns (sorted), indexes (name => array( type, fields )), the unique column, the class and its file */
    private static $tables = array(
        'exp_secret' => array(
            'columns' => array( 'ciphertext', 'created', 'creator_id', 'description', 'id', 'key_id', 'modified', 'modifier_id',
                                'name', 'scope' ),
            'indexes' => array( 'exp_secret_name' => array( 'unique', array( 'name' ) ),
                                'exp_secret_scope' => array( 'non-unique', array( 'scope' ) ) ),
            'unique' => 'name',
            'class' => 'expSecret',
            'file' => 'kernel/classes/secrets/expsecret.php',
        ),
        'exp_data_connection' => array(
            'columns' => array( 'created', 'creator_id', 'database_name', 'description', 'engine', 'id', 'identifier', 'modified',
                                'modifier_id', 'name', 'options', 'port', 'read_only', 'secret', 'server', 'user_name' ),
            'indexes' => array( 'exp_data_connection_identifier' => array( 'unique', array( 'identifier' ) ) ),
            'unique' => 'identifier',
            'class' => 'expDataConnection',
            'file' => 'kernel/classes/datatable/expdataconnection.php',
        ),
    );

    private static $files = array( 'mysql' => 'kernel/sql/mysql/kernel_schema.sql', 'postgresql' => 'kernel/sql/postgresql/kernel_schema.sql',
                                   'sqlite' => 'kernel/sql/sqlite/schema.sql', 'mysql-update' => 'update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql',
                                   'postgresql-update' => 'update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql',
                                   'sqlite-update' => 'update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function read( $file )
    {
        return file_get_contents( self::root() . '/' . $file );
    }

    public static function tableProvider()
    {
        $cases = array();
        foreach ( array_keys( self::$tables ) as $table )
            $cases[$table] = array( $table );
        return $cases;
    }

    /** ST-01: share/db_schema.dba */
    #[PHPUnit\Framework\Attributes\DataProvider( 'tableProvider' )]
    public function testTheDbaDescribesTheTable( $tableName )
    {
        $spec = self::$tables[$tableName];
        $schema = null;
        include self::root() . '/share/db_schema.dba';
        $this->assertArrayHasKey( $tableName, $schema );
        $table = $schema[$tableName];
        $this->assertSame( $tableName, $table['name'] );
        $this->assertSame( $spec['columns'], array_keys( $table['fields'] ) );
        $this->assertSame( 'auto_increment', $table['fields']['id']['type'] );
        $this->assertSame( array( 'id' ), $table['indexes']['PRIMARY']['fields'] );
        foreach ( $spec['indexes'] as $name => $index )
        {
            $this->assertSame( $index[0], $table['indexes'][$name]['type'], $name );
            $this->assertSame( $index[1], $table['indexes'][$name]['fields'], $name );
        }
        $this->assertSame( array_merge( array( 'PRIMARY' ), array_keys( $spec['indexes'] ) ), array_keys( $table['indexes'] ) );
    }

    /** ST-01: the persistent object */
    #[PHPUnit\Framework\Attributes\DataProvider( 'tableProvider' )]
    public function testThePersistentObjectHasTheColumns( $tableName )
    {
        $spec = self::$tables[$tableName];
        $file = self::root() . '/' . $spec['file'];
        if ( !is_file( $file ) )
            $this->markTestSkipped( $spec['file'] . ' does not exist yet' );
        require_once $file;
        $definition = call_user_func( array( $spec['class'], 'definition' ) );
        $persistent = array_keys( $definition['fields'] );
        sort( $persistent );
        $this->assertSame( $spec['columns'], $persistent );
        $this->assertSame( $tableName, $definition['name'] );
        $this->assertSame( array( 'id' ), $definition['keys'] );
        $this->assertSame( 'id', $definition['increment_key'] );
        $this->assertSame( $spec['class'], $definition['class_name'] );
    }

    /** ST-01: the kernel schemas and the update files */
    #[PHPUnit\Framework\Attributes\DataProvider( 'tableProvider' )]
    public function testTheSqlFilesCarryTheTable( $tableName )
    {
        $spec = self::$tables[$tableName];
        $pattern = '/CREATE TABLE (?:IF NOT EXISTS )?`?' . $tableName . '`? \((.*?)\n\s*\)/s';
        foreach ( self::$files as $engine => $file )
        {
            $sql = self::read( $file );
            $this->assertMatchesRegularExpression( $pattern, $sql, $engine );
            preg_match( $pattern, $sql, $m );
            preg_match_all( '/^[\s,]*`?([a-z_]+)`? (?:int|integer|bigint|varchar|character|longtext|text)/m', $m[1], $cols );
            $this->assertSame( $spec['columns'], $cols[1], $engine );
            foreach ( $spec['indexes'] as $name => $index )
            {
                $unique = $index[0] === 'unique';
                $this->assertMatchesRegularExpression( $unique ? '/(?:UNIQUE KEY|UNIQUE INDEX(?: IF NOT EXISTS)?) ' . $name . ' /'
                                                              : '/(?<!UNIQUE )(?:KEY|INDEX(?: IF NOT EXISTS)?) ' . $name . ' /',
                                                       $sql, "$engine $name" );
            }
        }
        $pg = self::read( 'kernel/sql/postgresql/kernel_schema.sql' );
        $this->assertStringContainsString( "CREATE SEQUENCE {$tableName}_id_seq", $pg );
        $this->assertStringContainsString( "nextval('{$tableName}_id_seq'::text)", $pg );
        $this->assertStringContainsString( "ADD CONSTRAINT {$tableName}_pkey PRIMARY KEY (id)", $pg );
        $this->assertStringContainsString( "setval('{$tableName}_id_seq'", self::read( 'kernel/sql/postgresql/setval.sql' ) );

        $mysql = self::read( 'update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertStringContainsString( "CREATE TABLE IF NOT EXISTS $tableName", $mysql );
        // the MySQL update file stops at its last statement on a second run: the table comes before it
        $this->assertLessThan( strpos( $mysql, 'ALTER TABLE ezprest_authcode' ), strpos( $mysql, "CREATE TABLE IF NOT EXISTS $tableName" ) );
        $pgUpdate = self::read( 'update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertMatchesRegularExpression( "/IF NOT EXISTS \( SELECT 1 FROM information_schema.tables WHERE table_schema = current_schema\(\) AND table_name = '$tableName' \)/", $pgUpdate );
        $this->assertMatchesRegularExpression( "/IF NOT EXISTS \( SELECT 1 FROM information_schema.sequences WHERE sequence_schema = current_schema\(\) AND sequence_name = '{$tableName}_id_seq' \)/", $pgUpdate );
        $this->assertStringContainsString( "CREATE TABLE IF NOT EXISTS $tableName", self::read( 'update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' ) );

        // Oracle: no reserved word as a column, every identifier within 30 characters
        foreach ( array_merge( $spec['columns'], array_keys( $spec['indexes'] ), array( $tableName, $tableName . '_id_seq' ) ) as $name )
            $this->assertLessThanOrEqual( 30, strlen( $name ), $name );
        $this->assertEmpty( array_intersect( $spec['columns'], array( 'file', 'size', 'level', 'mode', 'comment', 'number', 'session',
                                                                       'user', 'uid', 'access', 'date', 'password' ) ) );
    }

    /** ST-02 */
    public function testSqliteUpdateRunsTwice()
    {
        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'no pdo_sqlite' );
        $dir = self::root() . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $file = $dir . '/secret-connection-update-test-' . getmypid() . '.db';
        $pdo = new PDO( 'sqlite:' . $file );
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $apply = function () use ( $pdo ) {
            $sql = self::read( 'update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
            $start = strpos( $sql, '-- The named database connections of the data tables' );
            $this->assertNotFalse( $start );
            $section = substr( $sql, $start, strpos( $sql, "\n--\n-- ezuser.login", $start ) - $start );
            $body = preg_replace( '/^--.*$/m', '', $section );
            foreach ( preg_split( '/;\s*$/m', $body ) as $statement )
                if ( trim( $statement ) !== '' )
                    $pdo->exec( $statement );
        };
        try
        {
            $apply();
            foreach ( self::$tables as $tableName => $spec )
            {
                $cols = array_column( $pdo->query( "PRAGMA table_info($tableName)" )->fetchAll( PDO::FETCH_ASSOC ), 'name' );
                $this->assertSame( $spec['columns'], $cols, $tableName );
            }
            $pdo->exec( "INSERT INTO exp_secret (name, scope, ciphertext, key_id) VALUES ('archive_db', 'datatable', 'AQ==', '0123456789abcdef')" );
            $pdo->exec( "INSERT INTO exp_data_connection (identifier, name, engine, database_name) VALUES ('archive', 'Archive', 'sqlite', 'var/datatable/archive.db')" );
            $apply();
            foreach ( self::$tables as $tableName => $spec )
            {
                $this->assertSame( '1', (string)$pdo->query( "SELECT COUNT(*) FROM $tableName" )->fetchColumn(), "the second run kept the row of $tableName" );
                $names = array_column( $pdo->query( "SELECT name FROM sqlite_master WHERE type = 'index' AND tbl_name = '$tableName' AND name NOT LIKE 'sqlite_%'" )->fetchAll( PDO::FETCH_ASSOC ), 'name' );
                sort( $names );
                $this->assertSame( array_keys( $spec['indexes'] ), $names, $tableName );
            }
            foreach ( array( "INSERT INTO exp_secret (name) VALUES ('archive_db')",
                             "INSERT INTO exp_data_connection (identifier) VALUES ('archive')" ) as $duplicate )
            {
                $refused = false;
                try
                {
                    $pdo->exec( $duplicate );
                }
                catch ( PDOException $e )
                {
                    $refused = true;
                }
                $this->assertTrue( $refused, $duplicate );
            }
        }
        finally
        {
            $pdo = null;
            @unlink( $file );
        }
    }
}
