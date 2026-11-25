<?php
/**
 * expSQLServerSchema, the schema handler of SQL Server (no server needed; the live round trip of the kernel schema is
 * tests/tests/lib/ezdb/sqlserver/live/expSQLServerSchemaLiveTest.php):
 *
 *  SC-01 - Schema types to SQL Server types and back (NVARCHAR throughout, MAX past 4,000, TINYINT avoided)
 *  SC-02 - Column definitions: named default constraints DF_<table>_<column>, NULL / NOT NULL, identity
 *  SC-03 - Index keys within SQL Server's limits: MAX columns and columns past 1,700 bytes of a non-unique index
 *          INCLUDEd, an index without key columns not made, a primary key past 900 bytes NONCLUSTERED, a unique
 *          index past 1,700 bytes reported; a text column in an index key is NVARCHAR(840)
 *  SC-04 - Data inserts: N'' text, numbers, floats kept (the base class cut them to integers), NULL, and
 *          IDENTITY_INSERT around rows that give an identity column a value
 *  SC-05 - Upgrades: add, drop (its default constraint first), alter, index add/drop
 *  SC-06 - Read back: defaults as SQL Server stores them, tables and indexes from sys.* (over a recording driver)
 *  SC-07 - The kernel's share/db_schema.dba as SQL Server DDL: every table, no index on a MAX key
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/ezdb/sqlserver/expSQLServerTestKit.php';
require_once dirname( __DIR__, 4 ) . '/lib/ezdbschema/classes/expsqlserverschema.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdbschema')]
#[\PHPUnit\Framework\Attributes\Group('sqlserver')]
class expSQLServerSchemaTest extends PHPUnit\Framework\TestCase
{
    private function handler( $db = null )
    {
        return new expSQLServerSchema( array( 'instance' => $db ) );
    }

    public static function fromTypes()
    {
        return array(
            array( 'int', 11, 'INT' ), array( 'mediumint', 9, 'INT' ), array( 'bigint', 20, 'BIGINT' ), array( 'tinyint', 4, 'SMALLINT' ),
            array( 'smallint', 6, 'SMALLINT' ), array( 'auto_increment', null, 'INT IDENTITY(1,1)' ), array( 'varchar', 255, 'NVARCHAR(255)' ),
            array( 'varchar', 4000, 'NVARCHAR(4000)' ), array( 'varchar', 4001, 'NVARCHAR(MAX)' ), array( 'varchar', null, 'NVARCHAR(255)' ),
            array( 'char', 32, 'NCHAR(32)' ), array( 'longtext', null, 'NVARCHAR(MAX)' ), array( 'text', null, 'NVARCHAR(MAX)' ),
            array( 'mediumtext', null, 'NVARCHAR(MAX)' ), array( 'float', null, 'REAL' ), array( 'double', null, 'FLOAT' ),
            array( 'decimal', '10,2', 'DECIMAL(10,2)' ), array( 'decimal', ' 12 , 4 ', 'DECIMAL(12,4)' ), array( 'decimal', 'x', 'DECIMAL(18,0)' ),
            array( 'timestamp', null, 'DATETIME2(0)' ), array( 'datetime', null, 'DATETIME2(0)' ), array( 'date', null, 'DATE' ),
            array( 'time', null, 'TIME(0)' ), array( 'longblob', null, 'VARBINARY(MAX)' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('fromTypes')]
    public function testConvertFromStandardType( $type, $length, $expected )
    {
        $this->assertSame( $expected, $this->handler()->convertFromStandardType( $type, $length ) );
    }

    public function testUnknownTypeIsRefused()
    {
        $this->expectException( RuntimeException::class );
        $this->handler()->convertFromStandardType( 'geometry' );
    }

    public static function toTypes()
    {
        return array(
            array( 'int', 4, 10, 0, 'int', 11 ), array( 'bigint', 8, 19, 0, 'bigint', 20 ), array( 'smallint', 2, 5, 0, 'int', 6 ),
            array( 'tinyint', 1, 3, 0, 'int', 6 ), array( 'bit', 1, 1, 0, 'int', 6 ), array( 'nvarchar', 510, 0, 0, 'varchar', 255 ),
            array( 'nvarchar', -1, 0, 0, 'longtext', null ), array( 'nvarchar', 1680, 0, 0, 'longtext', null ), array( 'nchar', 64, 0, 0, 'char', 32 ),
            array( 'varchar', 100, 0, 0, 'varchar', 100 ), array( 'ntext', 16, 0, 0, 'longtext', null ), array( 'real', 4, 24, 0, 'float', null ),
            array( 'float', 8, 53, 0, 'double', null ), array( 'decimal', 9, 10, 2, 'decimal', '10,2' ), array( 'datetime2', 6, 19, 0, 'timestamp', null ),
            array( 'datetime', 8, 23, 3, 'timestamp', null ), array( 'date', 3, 10, 0, 'date', null ), array( 'time', 3, 8, 0, 'time', null ),
            array( 'varbinary', -1, 0, 0, 'longblob', null ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('toTypes')]
    public function testConvertToStandardType( $type, $maxLength, $precision, $scale, $expected, $expectedLength )
    {
        $this->assertSame( $expected, $this->handler()->convertToStandardType( $type, $maxLength, $precision, $scale, $length ) );
        $this->assertSame( $expectedLength, $length );
    }

    public static function fieldDefs()
    {
        return array(
            'int default' => array( array( 'type' => 'int', 'length' => 11, 'not_null' => '1', 'default' => 0 ), '[id] INT CONSTRAINT [DF_t_id] DEFAULT 0 NOT NULL' ),
            'varchar empty default' => array( array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1', 'default' => '' ), "[id] NVARCHAR(50) CONSTRAINT [DF_t_id] DEFAULT N'' NOT NULL" ),
            'varchar quote default' => array( array( 'type' => 'varchar', 'length' => 5, 'default' => "a'b" ), "[id] NVARCHAR(5) CONSTRAINT [DF_t_id] DEFAULT N'a''b' NULL" ),
            'null default' => array( array( 'type' => 'int', 'default' => null ), '[id] INT CONSTRAINT [DF_t_id] DEFAULT NULL NULL' ),
            'no default' => array( array( 'type' => 'longtext', 'default' => false ), '[id] NVARCHAR(MAX) NULL' ),
            'not null without default' => array( array( 'type' => 'int', 'not_null' => '1', 'default' => null ), '[id] INT NOT NULL' ),
            'float default' => array( array( 'type' => 'float', 'default' => 0.5 ), '[id] REAL CONSTRAINT [DF_t_id] DEFAULT 0.5 NULL' ),
            'date empty default dropped' => array( array( 'type' => 'timestamp', 'not_null' => '1', 'default' => '' ), '[id] DATETIME2(0) NOT NULL' ),
            'identity' => array( array( 'type' => 'auto_increment', 'default' => false ), '[id] INT IDENTITY(1,1) NOT NULL' ),
            'int text default' => array( array( 'type' => 'int', 'not_null' => '1', 'default' => '' ), '[id] INT CONSTRAINT [DF_t_id] DEFAULT 0 NOT NULL' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('fieldDefs')]
    public function testFieldDef( array $def, $expected )
    {
        // "id" is no reserved word: the brackets come from the default constraint name only, the column stays bare
        $this->assertSame( str_replace( '[id] ', 'id ', $expected ), $this->handler()->generateFieldDef( 't', 'id', $def ) );
    }

    public function testReservedColumnIsBracketed()
    {
        $this->assertStringStartsWith( '[key] INT', $this->handler()->generateFieldDef( 't', 'key', array( 'type' => 'int' ) ) );
        $this->assertSame( '[order]', $this->handler()->quoteSchemaIdentifier( 'order' ) );
        $this->assertSame( 'name', $this->handler()->quoteSchemaIdentifier( 'name' ) );
    }

    private function table()
    {
        return array(
            'fields' => array(
                'id' => array( 'type' => 'auto_increment', 'default' => false ),
                'parent' => array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                'text' => array( 'type' => 'longtext', 'default' => false ),
                'a' => array( 'type' => 'varchar', 'length' => 600, 'default' => '' ),
                'b' => array( 'type' => 'varchar', 'length' => 600, 'default' => '' ),
                'body' => array( 'type' => 'longtext', 'default' => false ),
            ),
            'indexes' => array(
                'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                't_parent_text' => array( 'type' => 'non-unique', 'fields' => array( 'parent', 'text' ) ),
                't_ab' => array( 'type' => 'non-unique', 'fields' => array( 'a', 'b' ) ),
                't_ab_u' => array( 'type' => 'unique', 'fields' => array( 'a', 'b' ) ),
            ),
        );
    }

    public function testTableDdl()
    {
        $h = $this->handler();
        $sql = $h->generateTableSQLList( 't', $this->table(), array(), false );
        $this->assertStringStartsWith( "CREATE TABLE t (\n  id INT IDENTITY(1,1) NOT NULL,", $sql[0] );
        $this->assertStringContainsString( '  text NVARCHAR(840) NULL', $sql[0] );
        $this->assertStringContainsString( '  body NVARCHAR(MAX) NULL', $sql[0] );
        $this->assertSame( 'ALTER TABLE t ADD CONSTRAINT [PK_t] PRIMARY KEY (id)', $sql[1] );
        $this->assertSame( 'CREATE INDEX [t_parent_text] ON t (parent, text)', $sql[2] );
        // 600 + 600 characters are 2,400 bytes: b is no key column of the non-unique index
        $this->assertSame( 'CREATE INDEX [t_ab] ON t (a) INCLUDE (b)', $sql[3] );
        // a unique index keeps its columns, and is reported
        $this->assertSame( 'CREATE UNIQUE INDEX [t_ab_u] ON t (a, b)', $sql[4] );
        $this->assertNotEmpty( preg_grep( '/t_ab_u: the key may take 2400 bytes/', $h->Notes ) );
    }

    public function testIndexOnMaxColumnOnly()
    {
        $h = $this->handler();
        $sql = $h->generateAddIndexSql( 't', 'i', array( 'type' => 'non-unique', 'fields' => array( 'body' ) ), array(), false,
                                        array( 'body' => array( 'type' => 'longtext' ) ) );
        $this->assertSame( '', $sql );
        $this->assertNotEmpty( preg_grep( '/no column can be a key/', $h->Notes ) );
    }

    public function testMaxColumnIsIncluded()
    {
        $sql = $this->handler()->generateAddIndexSql( 't', 'i', array( 'type' => 'non-unique', 'fields' => array( 'id', array( 'name' => 'body' ) ) ), array(), false,
                                                      array( 'id' => array( 'type' => 'int' ), 'body' => array( 'type' => 'longtext' ) ) );
        $this->assertSame( 'CREATE INDEX [i] ON t (id) INCLUDE (body)', $sql );
    }

    public function testWidePrimaryKeyIsNonclustered()
    {
        $sql = $this->handler()->generateAddIndexSql( 't', 'PRIMARY', array( 'type' => 'primary', 'fields' => array( 'a' ) ), array(), false,
                                                      array( 'a' => array( 'type' => 'varchar', 'length' => 500 ) ) );
        $this->assertSame( 'ALTER TABLE t ADD CONSTRAINT [PK_t] PRIMARY KEY NONCLUSTERED (a)', $sql );
    }

    public function testDropIndex()
    {
        $h = $this->handler();
        $this->assertSame( "DROP INDEX [i] ON t;\n", $h->generateDropIndexSql( 't', 'i', array( 'type' => 'non-unique' ) ) );
        $this->assertSame( 'ALTER TABLE t DROP CONSTRAINT [PK_t]', $h->generateDropIndexSql( 't', 'PRIMARY', array( 'type' => 'primary' ), false ) );
    }

    public function testAddField()
    {
        $this->assertSame( "ALTER TABLE t ADD c INT CONSTRAINT [DF_t_c] DEFAULT 5 NOT NULL;\n",
                           $this->handler()->generateAddFieldSql( 't', 'c', array( 'type' => 'int', 'not_null' => '1', 'default' => 5 ), array() ) );
    }

    public function testDropFieldDropsItsDefaultFirst()
    {
        $this->assertSame( "ALTER TABLE t DROP CONSTRAINT IF EXISTS [DF_t_c];\nALTER TABLE t DROP COLUMN c;\n",
                           $this->handler()->generateDropFieldSql( 't', 'c', array() ) );
    }

    public function testAlterField()
    {
        $sql = $this->handler()->generateAlterFieldSql( 't', 'c', array( 'type' => 'varchar', 'length' => 20, 'not_null' => '1', 'default' => 'x' ), array() );
        $this->assertSame( "ALTER TABLE t DROP CONSTRAINT IF EXISTS [DF_t_c];\nUPDATE t SET c = N'x' WHERE c IS NULL;\nALTER TABLE t ALTER COLUMN c NVARCHAR(20) NOT NULL;\n"
                         . "ALTER TABLE t ADD CONSTRAINT [DF_t_c] DEFAULT N'x' FOR c;\n", $sql );
    }

    public function testInsertsWithIdentity()
    {
        $def = $this->table();
        $list = $this->handler()->generateTableInsertSQLList( 't', $def, array( 'fields' => array( 'id', 'parent', 'text' ),
            'rows' => array( array( '1', '2', "ä'x" ), array( '2', null, '' ) ) ), array(), false );
        $this->assertSame( array( 'SET IDENTITY_INSERT t ON', "INSERT INTO t (id, parent, text) VALUES (1,2,N'ä''x')",
                                  "INSERT INTO t (id, parent, text) VALUES (2,NULL,N'')", 'SET IDENTITY_INSERT t OFF' ), $list );
    }

    public function testInsertsWithoutIdentity()
    {
        $list = $this->handler()->generateTableInsertSQLList( 't', $this->table(), array( 'fields' => array( 'parent' ), 'rows' => array( array( '7' ) ) ), array(), false );
        $this->assertSame( array( 'INSERT INTO t (parent) VALUES (7)' ), $list );
        $this->assertSame( array(), $this->handler()->generateTableInsertSQLList( 't', $this->table(), array( 'fields' => array( 'parent' ), 'rows' => array() ), array() ) );
    }

    public static function values()
    {
        return array(
            array( array( 'type' => 'float' ), '1.25', '1.25' ), array( array( 'type' => 'double' ), 2.5, '2.5' ), array( array( 'type' => 'int' ), '42', '42' ),
            array( array( 'type' => 'int' ), '', '0' ), array( array( 'type' => 'int' ), 'x', 'NULL' ), array( array( 'type' => 'varchar' ), 'ä', "N'ä'" ),
            array( array( 'type' => 'longblob' ), "\x00\x01", '0x0001' ), array( array( 'type' => 'varchar' ), null, 'NULL' ), array( array( 'type' => 'decimal' ), '10.50', '10.5' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('values')]
    public function testDataValue( array $def, $value, $expected )
    {
        $this->assertSame( $expected, $this->handler()->generateDataValueTextSQL( $def, $value ) );
    }

    public static function defaults()
    {
        return array( array( '((0))', '0' ), array( "(N'')", '' ), array( "('a''b')", "a'b" ), array( '(NULL)', null ), array( "(N'x')", 'x' ),
                      array( '((1.5))', '1.5' ), array( "('(x)')", '(x)' ), array( '(getdate())', 'getdate()' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('defaults')]
    public function testParseDefault( $definition, $expected )
    {
        $this->assertSame( $expected, $this->handler()->parseDefault( $definition ) );
    }

    public function testReadBackOverRecordingDriver()
    {
        $saved = expSQLServerTestKit::quietDebug();
        try
        {
            $fake = new expSQLServerFakeTransport();
            $fake->on( '/FROM sys\.tables WHERE is_ms_shipped/', array( array( 'name' => 't' ) ) );
            $fake->on( '/FROM sys\.columns c JOIN sys\.types/', array(
                array( 'name' => 'id', 'type' => 'int', 'max_length' => '4', 'precision' => '10', 'scale' => '0', 'is_nullable' => '0', 'is_identity' => '1', 'default_value' => null ),
                array( 'name' => 'name', 'type' => 'nvarchar', 'max_length' => '100', 'precision' => '0', 'scale' => '0', 'is_nullable' => '0', 'is_identity' => '0', 'default_value' => "(N'')" ),
                array( 'name' => 'n', 'type' => 'int', 'max_length' => '4', 'precision' => '10', 'scale' => '0', 'is_nullable' => '1', 'is_identity' => '0', 'default_value' => '((3))' ),
                array( 'name' => 'body', 'type' => 'nvarchar', 'max_length' => '-1', 'precision' => '0', 'scale' => '0', 'is_nullable' => '1', 'is_identity' => '0', 'default_value' => null ),
            ) );
            $fake->on( '/FROM sys\.indexes i JOIN sys\.index_columns/', array(
                array( 'index_name' => 'PK_t', 'is_primary_key' => '1', 'is_unique' => '1', 'column_name' => 'id', 'key_ordinal' => '1' ),
                array( 'index_name' => 't_name', 'is_primary_key' => '0', 'is_unique' => '0', 'column_name' => 'name', 'key_ordinal' => '1' ),
                array( 'index_name' => 't_name', 'is_primary_key' => '0', 'is_unique' => '0', 'column_name' => 'n', 'key_ordinal' => '2' ),
            ) );
            $db = expSQLServerTestKit::driver( $fake, array( 'LiteralMode' => 'prefix' ) );
            $schema = $this->handler( $db )->schema();
            $this->assertSame( array( '_info' => array( 'format' => 'generic' ), 't' => array(
                'name' => 't',
                'fields' => array(
                    'body' => array( 'type' => 'longtext', 'default' => false ),
                    'id' => array( 'type' => 'auto_increment', 'default' => false ),
                    'n' => array( 'type' => 'int', 'length' => 11, 'default' => 3 ),
                    'name' => array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1', 'default' => '' ),
                ),
                'indexes' => array(
                    'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                    't_name' => array( 'type' => 'non-unique', 'fields' => array( 'name', 'n' ) ),
                ) ) ), $schema );
        }
        finally
        {
            expSQLServerTestKit::restoreDebug( $saved );
        }
    }

    public function testKernelSchemaAsDdl()
    {
        $schema = eZDbSchema::read( dirname( __DIR__, 4 ) . '/share/db_schema.dba' );
        $h = $this->handler();
        $sql = $h->generateSchemaFile( $schema );
        $tables = count( $schema ) - ( isset( $schema['_info'] ) ? 1 : 0 );
        $this->assertSame( $tables, preg_match_all( '/^CREATE TABLE /m', $sql ) );
        $this->assertSame( 0, preg_match( '/INDEX [^\n]*\([^)]*\) INCLUDE/', '' ) );
        foreach ( preg_split( "/;\n/", $sql ) as $statement )
            if ( preg_match( '/^CREATE (UNIQUE )?INDEX/', trim( $statement ) ) )
                $this->assertStringNotContainsString( 'MAX', trim( $statement ) );
        $this->assertSame( 0, preg_match( '/\bTINYINT\b|\bntext\b|\bimage\b/', $sql ) );
        $this->assertStringContainsString( 'CREATE TABLE ezurlalias_ml', $sql );
    }

    public function testNames()
    {
        $h = $this->handler();
        $this->assertSame( 'sqlserver', $h->schemaType() );
        $this->assertSame( 'Microsoft SQL Server', $h->schemaName() );
        $this->assertContains( 'order', $h->reservedKeywordList() );
        $this->assertSame( "DROP TABLE [user];\n", $h->generateDropTable( 'user' ) );
        $this->assertSame( "a''b", $h->escapeSQLString( "a'b\0" ) );
    }
}
