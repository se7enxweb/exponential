<?php

class eZSQLite3MirrorTest extends PHPUnit\Framework\TestCase
{
    /** @var SQLite3|null */
    private static $sqlite = null;

    /** @var string|null */
    private static $sqlitePath = null;

    public static function setUpBeforeClass(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            self::markTestSkipped( 'SQLite3 extension is not loaded' );

        $schemaFile = realpath( __DIR__ . '/../../../../kernel/sql/sqlite/workingdataandschema.sql' );
        if ( $schemaFile === false )
            self::markTestSkipped( 'SQLite test fixture SQL file not found' );

        $sql = file_get_contents( $schemaFile );
        if ( $sql === false )
            self::markTestSkipped( 'Could not read SQLite test fixture SQL file' );

        $path = tempnam( sys_get_temp_dir(), 'ez-sqlite-mirror-' );
        if ( $path === false )
            self::markTestSkipped( 'Could not create temp SQLite database file' );

        self::$sqlitePath = $path;
        self::$sqlite = new SQLite3( $path );

        $ok = self::$sqlite->exec( $sql );
        if ( !$ok )
            self::markTestSkipped( 'Could not initialize SQLite fixture DB: ' . self::$sqlite->lastErrorMsg() );
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$sqlite instanceof SQLite3 )
            self::$sqlite->close();

        if ( self::$sqlitePath !== null && file_exists( self::$sqlitePath ) )
            unlink( self::$sqlitePath );
    }

    public function testSqliteConnectionWorks(): void
    {
        $result = self::$sqlite->querySingle( 'SELECT sqlite_version()' );
        $this->assertNotEmpty( $result );
    }

    public function testSqliteExpectedTablesExist(): void
    {
        $requiredTables = [
            'ezcontentobject',
            'ezcontentobject_tree',
            'ezcontent_language',
            'ezcontentobject_version',
        ];

        foreach ( $requiredTables as $table )
        {
            $exists = (int)self::$sqlite->querySingle(
                "SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name = '" . SQLite3::escapeString( $table ) . "'"
            );
            $this->assertSame( 1, $exists, "SQLite table '{$table}' must exist" );
        }
    }

    public function testSqliteContentObjectSchemaHasExpectedFields(): void
    {
        $result = self::$sqlite->query( 'PRAGMA table_info(ezcontentobject)' );
        $this->assertInstanceOf( SQLite3Result::class, $result );

        $columns = [];
        while ( $row = $result->fetchArray( SQLITE3_ASSOC ) )
            $columns[] = $row['name'];

        foreach ( [ 'id', 'status', 'contentclass_id', 'language_mask' ] as $column )
            $this->assertContains( $column, $columns, "ezcontentobject must contain column '{$column}'" );
    }

    public function testSqliteContentLanguageHasRows(): void
    {
        $count = (int)self::$sqlite->querySingle( 'SELECT COUNT(*) FROM ezcontent_language' );
        $this->assertGreaterThan( 0, $count );
    }

    public function testSqliteContentObjectHasRows(): void
    {
        $count = (int)self::$sqlite->querySingle( 'SELECT COUNT(*) FROM ezcontentobject' );
        $this->assertGreaterThan( 0, $count );
    }

    public function testSqliteTreeNodePathStringFormat(): void
    {
        $result = self::$sqlite->query( 'SELECT path_string FROM ezcontentobject_tree LIMIT 5' );
        $this->assertInstanceOf( SQLite3Result::class, $result );

        while ( $row = $result->fetchArray( SQLITE3_ASSOC ) )
        {
            $this->assertRegexCompat(
                '#^/\d+/#',
                (string)$row['path_string'],
                'SQLite path_string must match /id/id/ format'
            );
        }
    }

    public function testSqliteNoOrphanedArchivedVersionsQueryRuns(): void
    {
        $sql = "SELECT COUNT(*) AS c FROM ezcontentobject_version v
                WHERE v.status = 5
                  AND NOT EXISTS (
                    SELECT 1 FROM ezcontentobject_version v2
                    WHERE v2.contentobject_id = v.contentobject_id AND v2.status = 1
                  )";
        $count = self::$sqlite->querySingle( $sql );
        $this->assertIsNumeric( $count );
    }

    public function testSqliteContentClassExists(): void
    {
        $count = (int)self::$sqlite->querySingle( 'SELECT COUNT(*) FROM ezcontentclass WHERE version=0' );
        $this->assertGreaterThan( 0, $count );
    }

    public function testSqliteContentObjectNoNullStatus(): void
    {
        $count = (int)self::$sqlite->querySingle( 'SELECT COUNT(*) FROM ezcontentobject WHERE status IS NULL' );
        $this->assertSame( 0, $count );
    }

    public function testSqliteSectionTableHasRows(): void
    {
        $count = (int)self::$sqlite->querySingle( 'SELECT COUNT(*) FROM ezsection' );
        $this->assertGreaterThan( 0, $count );
    }

    public function testSqlitePublishedContentObjectCountIsPositive(): void
    {
        $count = (int)self::$sqlite->querySingle( 'SELECT COUNT(*) FROM ezcontentobject WHERE status=1' );
        $this->assertGreaterThan( 0, $count );
    }

    public function testSqliteInsertSelectDeleteRoundTrip(): void
    {
        $name = '_phpunit_' . time();
        $safeName = SQLite3::escapeString( $name );

        $inserted = self::$sqlite->exec(
            "INSERT INTO ezpreferences (user_id, name, value) VALUES (0, '{$safeName}', 'test_value')"
        );
        $this->assertTrue( $inserted );

        $id = (int)self::$sqlite->lastInsertRowID();
        $this->assertGreaterThan( 0, $id );

        $storedName = (string)self::$sqlite->querySingle( "SELECT name FROM ezpreferences WHERE id={$id}" );
        $this->assertStringStartsWith( '_phpunit_', $storedName );

        $deleted = self::$sqlite->exec( "DELETE FROM ezpreferences WHERE id={$id}" );
        $this->assertTrue( $deleted );

        $remaining = (int)self::$sqlite->querySingle( "SELECT COUNT(*) FROM ezpreferences WHERE id={$id}" );
        $this->assertSame( 0, $remaining );
    }

    private function assertRegexCompat( string $pattern, string $subject, string $message = '' ): void
    {
        if ( method_exists( $this, 'assertMatchesRegularExpression' ) )
            $this->assertMatchesRegularExpression( $pattern, $subject, $message );
        else
            $this->assertRegExp( $pattern, $subject, $message );
    }
}
