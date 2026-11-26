<?php
/**
 * The DDL eZPgsqlSchema writes for the databases of the PostgreSQL wire protocol (no server needed):
 *
 *  PS-01 - PostgreSQL itself: unchanged (ALTER TABLE ONLY ... PRIMARY KEY afterwards, USING btree, setval)
 *  PS-02 - CockroachDB: the primary key inside CREATE TABLE (no ALTER TABLE ... ADD PRIMARY KEY, which would replace
 *          the hidden rowid key); sequences as PostgreSQL's
 *  PS-03 - YugabyteDB: the primary key inside CREATE TABLE, no USING btree (lsm is its own)
 *  PS-04 - Spanner: bit-reversed sequences and bigint ids, no smallint or real, no setval after the data, no USING
 *  PS-05 - parseDefault() reads the serial defaults CockroachDB and Spanner report back as auto_increment
 *  PS-06 - the kernel schema (share/db_schema.dba) for CockroachDB: every table with a primary key has it inline
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('pgwire')]
class expPostgreSQLWireSchemaTest extends PHPUnit\Framework\TestCase
{
    /** a table with a serial key, a unique and a plain index, a small integer and a reserved column name */
    protected static function table()
    {
        return array(
            'name' => 'exp_t',
            'fields' => array(
                'id' => array( 'type' => 'auto_increment', 'default' => false ),
                'name' => array( 'length' => 255, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
                'flag' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                'small' => array( 'length' => 6, 'type' => 'smallint', 'default' => null ),
                'group' => array( 'type' => 'longtext', 'default' => false ),
            ),
            'indexes' => array(
                'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                'exp_t_name' => array( 'type' => 'unique', 'fields' => array( 'name' ) ),
                'exp_t_flag' => array( 'type' => 'non-unique', 'fields' => array( 'flag', 'group' ) ),
            ),
        );
    }

    protected function sql( $profile )
    {
        $schema = new eZPgsqlSchema( array( 'instance' => null ) );
        $params = $profile ? array( 'wire_profile' => $profile ) : array();
        return $schema->generateTableSQLList( 'exp_t', self::table(), $params, false );
    }

    public function testPostgreSQLUnchanged()
    {
        $sql = $this->sql( false );
        $this->assertCount( 5, $sql );
        $this->assertStringStartsWith( 'CREATE SEQUENCE IF NOT EXISTS exp_t_id_seq', $sql[0] );
        $this->assertStringContainsString( "id integer DEFAULT nextval('exp_t_id_seq'::text) NOT NULL", $sql[1] );
        $this->assertStringNotContainsString( 'PRIMARY KEY', $sql[1] );
        $this->assertStringContainsString( 'USING btree', $sql[2] );
        $this->assertStringStartsWith( 'ALTER TABLE ONLY exp_t ADD CONSTRAINT exp_t_pkey PRIMARY KEY', $sql[4] );
        $this->assertSame( $sql, $this->sql( 'postgresql' ), 'the postgresql profile writes what PostgreSQL gets' );
    }

    public function testCockroachDB()
    {
        $sql = $this->sql( 'cockroachdb' );
        $this->assertCount( 4, $sql, 'no ALTER TABLE for the key' );
        $this->assertStringStartsWith( 'CREATE SEQUENCE IF NOT EXISTS exp_t_id_seq', $sql[0] );
        $this->assertStringContainsString( 'flag int4 DEFAULT 0 NOT NULL', $sql[1], 'INTEGER is INT8 on CockroachDB: int4' );
        $this->assertStringContainsString( "id integer DEFAULT nextval('exp_t_id_seq'::text) NOT NULL", $sql[1] );
        $this->assertStringContainsString( 'CONSTRAINT exp_t_pkey PRIMARY KEY ( id )', $sql[1] );
        $this->assertStringContainsString( 'USING btree', $sql[2] );
        $this->assertStringContainsString( '"group"', $sql[3] );
        foreach ( $sql as $statement )
            $this->assertStringNotContainsString( 'ALTER TABLE', $statement );
    }

    public function testYugabyteDB()
    {
        $sql = $this->sql( 'yugabytedb' );
        $this->assertCount( 4, $sql );
        $this->assertStringContainsString( 'CONSTRAINT exp_t_pkey PRIMARY KEY ( id )', $sql[1] );
        $this->assertSame( 'CREATE UNIQUE INDEX exp_t_name ON exp_t ( name )', $sql[2] );
        $this->assertSame( 'CREATE INDEX exp_t_flag ON exp_t ( flag, "group" )', $sql[3] );
    }

    public function testSpanner()
    {
        $sql = $this->sql( 'spanner' );
        $this->assertSame( 'CREATE SEQUENCE IF NOT EXISTS exp_t_id_seq BIT_REVERSED_POSITIVE', $sql[0] );
        $this->assertStringContainsString( "id bigint DEFAULT nextval('exp_t_id_seq') NOT NULL", $sql[1] );
        $this->assertStringContainsString( 'flag bigint DEFAULT 0 NOT NULL', $sql[1] );
        $this->assertStringContainsString( 'small bigint', $sql[1] );
        $this->assertStringNotContainsString( 'smallint', $sql[1] );
        $this->assertStringContainsString( "\n  PRIMARY KEY ( id )", $sql[1] );
        $this->assertStringNotContainsString( 'CONSTRAINT', $sql[1], 'Spanner takes no name for the key' );
        $this->assertSame( 'CREATE UNIQUE INDEX exp_t_name ON exp_t ( name )', $sql[2] );

        $schema = new eZPgsqlSchema( array( 'instance' => null ) );
        $inserts = $schema->generateTableInsertSQLList( 'exp_t', self::table(), array( 'fields' => array( 'id', 'name' ), 'rows' => array( array( 1, 'a' ) ) ),
                                                        array( 'wire_profile' => 'spanner' ), false );
        foreach ( $inserts as $statement )
            $this->assertStringNotContainsString( 'setval', $statement );
        $inserts = $schema->generateTableInsertSQLList( 'exp_t', self::table(), array( 'fields' => array( 'id', 'name' ), 'rows' => array( array( 1, 'a' ) ) ),
                                                        array( 'wire_profile' => 'cockroachdb' ), false );
        $this->assertStringStartsWith( "SELECT setval('exp_t_id_seq'", end( $inserts ) );
    }

    public static function defaults()
    {
        return array(
            'postgresql 8' => array( "nextval(('ezbasket_id_seq'::text)::regclass)", 1, '' ),
            'cockroachdb' => array( "nextval('public.ezbasket_id_seq'::REGCLASS)", 1, '' ),
            'spanner' => array( "nextval('ezbasket_id_seq')", 1, '' ),
            'a text default' => array( "'abc'::character varying", 0, 'abc' ),
            'cockroach string' => array( "''::STRING", 0, '' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('defaults')]
    public function testParseDefault( $default, $autoinc, $expected )
    {
        $schema = new eZPgsqlSchema( array( 'instance' => null ) );
        $found = 0;
        $this->assertSame( $expected, $schema->parseDefault( $default, $found ) );
        $this->assertSame( $autoinc, $found );
    }

    public function testKernelSchemaForCockroachDB()
    {
        $dba = eZDbSchema::read( __DIR__ . '/../../../../../share/db_schema.dba', false );
        $this->assertIsArray( $dba );
        $schema = new eZPgsqlSchema( array( 'instance' => null ) );
        $tables = 0;
        foreach ( $dba as $table => $def )
        {
            if ( $table === '_info' )
                continue;
            $hasPrimary = false;
            foreach ( $def['indexes'] as $index )
                $hasPrimary = $hasPrimary || $index['type'] === 'primary';
            $sql = $schema->generateTableSQLList( $table, $def, array( 'wire_profile' => 'cockroachdb' ), false );
            $create = implode( "\n", $sql );
            $this->assertStringNotContainsString( 'ALTER TABLE', $create, $table );
            $this->assertSame( $hasPrimary, strpos( $create, ' PRIMARY KEY ( ' ) !== false, $table );
            $tables++;
        }
        $this->assertGreaterThan( 100, $tables );
    }
}
