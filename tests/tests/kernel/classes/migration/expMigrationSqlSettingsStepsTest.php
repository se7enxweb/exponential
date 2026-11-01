<?php
/**
 * SQL migrations, INI migrations and the step types schema, data, sql and settings without a database: file names,
 * the files' parts, the portable schema DSL as each engine's SQL (the kernel's schema handlers, no connection), values
 * as SQL, INI migrations as settings steps, and the settings step writing a settings tree of the test.
 *
 *  MQ-01 the kernel registers schema, data, sql and settings; valid schemas and examples; schema undoes its
 *        create_table, add_column and add_index, nothing else undoes itself
 *  MQ-02 file names: .pgsql.sql is PostgreSQL's, .mongodb.json MongoDB's, .mongodb.sql is refused; a portable .sql runs
 *        on the SQL engines, and on MongoDB only with its .mongodb.json; the header key transactional
 *  MQ-03 SQL files: statements, the "-- down" part; MongoDB files: a list or up/down, every command checked; the
 *        engine's own file wins over the portable one; schema changes are recognised
 *  MQ-04 schema: the checks of each mode; the DSL as a .dba table; CREATE TABLE, ADD COLUMN and CREATE INDEX of MySQL,
 *        PostgreSQL and SQLite through the schema handlers
 *  MQ-05 data: the checks; values quoted, where with lists and null, MongoDB filters
 *  MQ-06 sql: the checks; a plan per engine; nothing for an engine fails or, with missing: skip, passes
 *  MQ-07 INI migrations: blocks name their file and siteaccess, Var[] makes a list, Var[]= adds, Var[key]= sets a key,
 *        -Var removes; mistakes with their line
 *  MQ-08 settings: writes settings/override and settings/siteaccess/<name> only, a backup first, the mode kept, only
 *        the lines that change; a second run changes nothing; a secret is masked in the plan; an unknown siteaccess
 *        fails
 *  MQ-09 SQL and INI migrations load as YAML migrations of these steps; down only from a "-- down" part; the
 *        header's transactional: no
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationSqlSettingsStepsTest extends PHPUnit\Framework\TestCase
{
    const TYPES = array( 'schema' => 'expMigrationStepSchemaChange', 'data' => 'expMigrationStepData', 'sql' => 'expMigrationStepSql',
                         'settings' => 'expMigrationStepSettings' );

    /** @var string|null a folder of the test under var/tmp */
    private $dir;

    protected function setUp(): void
    {
        expMigrationStepRegistry::forget();
    }

    protected function tearDown(): void
    {
        expIniEditor::setRoot( null );
        if ( $this->dir !== null && is_dir( $this->dir ) )
        {
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->dir, FilesystemIterator::SKIP_DOTS ),
                                                       RecursiveIteratorIterator::CHILD_FIRST );
            foreach ( $iterator as $item )
                $item->isDir() ? rmdir( $item->getPathname() ) : unlink( $item->getPathname() );
            rmdir( $this->dir );
        }
        $this->dir = null;
    }

    /** @return string the folder of the test, made once */
    private function dir()
    {
        if ( $this->dir === null )
        {
            $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/migration-sql-settings-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
            mkdir( $this->dir . '/migrations', 0775, true );
        }
        return $this->dir;
    }

    /**
     * @param string $name
     * @param string $text
     * @return string the path
     */
    private function file( $name, $text )
    {
        $path = $this->dir() . "/migrations/$name";
        file_put_contents( $path, $text );
        return $path;
    }

    /**
     * @param string $name
     * @param array $step
     * @return array the step with its defaults
     */
    private static function step( $name, array $step )
    {
        $type = expMigrationStepRegistry::fromSettings()->get( $name );
        return expMigrationStepSchema::withDefaults( $type->schema(), $step );
    }

    /**
     * @param string $name
     * @param array $step
     * @return string the problems of a step: its schema and the type's own checks
     */
    private static function problems( $name, array $step )
    {
        $type = expMigrationStepRegistry::fromSettings()->get( $name );
        $step = expMigrationStepSchema::withDefaults( $type->schema(), $step );
        return implode( "\n", array_merge( expMigrationStepSchema::check( $type->schema(), $step, $name ), $type->validate( $step ) ) );
    }

    /**
     * A context of an engine without a connection: what plan() and the file lookup of the step sql ask.
     *
     * @param string $engine
     * @param string|null $file the migration's file
     * @return object
     */
    private static function engineContext( $engine, $file = null )
    {
        return new class( $engine, $file ) {
            public $engine;
            public $file;
            public function __construct( $engine, $file ) { $this->engine = $engine; $this->file = $file; }
            public function engine() { return $this->engine; }
            public function migrationFile() { return $this->file; }
            public function root() { return dirname( __DIR__, 5 ); }
            public function db() { return expKernelTableSchema::nullDB(); }
            public function transactionMode() { return expMigrationContext::transactionModeOf( $this->engine ); }
        };
    }

    /** MQ-01 */
    public function testRegistered()
    {
        $registry = expMigrationStepRegistry::fromSettings();
        $this->assertSame( array(), $registry->errors() );
        foreach ( self::TYPES as $name => $class )
        {
            $type = $registry->get( $name );
            $this->assertInstanceOf( $class, $type, $name );
            $schema = $type->schema();
            $this->assertSame( array(), expMigrationStepSchema::problems( $schema ), $name );
            $this->assertNotEmpty( $schema['values'], "$name gives values to references:" );
            if ( expMigrationYamlParser::available() )
            {
                $errors = array();
                expMigrationYaml::fromText( $schema['example'], $registry, $errors );
                $this->assertSame( array(), $errors, "the example of $name is a valid step" );
            }
        }
        $schema = $registry->get( 'schema' );
        $this->assertInstanceOf( 'expMigrationPartlyRevertibleStepType', $schema );
        foreach ( array( 'create_table' => true, 'add_column' => true, 'add_index' => true, 'drop_table' => false, 'drop_column' => false, 'drop_index' => false ) as $mode => $undoes )
            $this->assertSame( $undoes, $schema->revertible( array( 'mode' => $mode ) ), $mode );
        foreach ( array( 'data', 'sql', 'settings' ) as $name )
            $this->assertNotInstanceOf( 'expMigrationRevertibleStepType', $registry->get( $name ), "$name does not undo itself" );
        $this->assertSame( array( 'tag:ini' ), $registry->get( 'settings' )->caches( array() ) );
    }

    /** MQ-02 */
    public function testFileNames()
    {
        $this->assertSame( array( 'id' => '20261107100000_index', 'timestamp' => '20261107100000', 'name' => 'index', 'type' => 'sql', 'engine' => 'postgresql' ),
                           expMigrationDefinition::parseName( '20261107100000_index.pgsql.sql' ) );
        $this->assertSame( array( 'sql', 'mongodb' ), array_values( array_intersect_key( expMigrationDefinition::parseName( '20261107100000_index.mongodb.json' ), array( 'type' => 1, 'engine' => 1 ) ) ) );
        $this->assertStringContainsString( 'its file is 20261107100000_index.mongodb.json', expMigrationDefinition::parseName( '20261107100000_index.mongodb.sql' )['error'] );
        $this->assertStringContainsString( 'unknown type .json', expMigrationDefinition::parseName( '20261107100000_index.json' )['error'] );

        $portable = new expMigrationDefinition( '20261107100000_index', 'site', 'sql' );
        $portable->addFile( $this->file( '20261107100000_index.sql', "-- description: Index\n-- transactional: no\nSELECT 1;\n" ) );
        $this->assertSame( array(), $portable->errors() );
        $this->assertSame( array( 'mysql', 'postgresql', 'sqlite', 'oracle' ), $portable->engines() );
        $this->assertFalse( $portable->supportsEngine( 'mongodb' ), 'no MongoDB file: not on MongoDB' );
        $this->assertFalse( $portable->transactional() );
        $portable->addFile( $this->file( '20261107100000_index.mongodb.json', "[]\n" ), 'mongodb' );
        $this->assertSame( array( '*' ), $portable->engines() );
        $this->assertSame( 'Index', $portable->description(), 'the portable file\'s header counts' );

        $bad = new expMigrationDefinition( '20261107100001_bad', 'site', 'sql' );
        $bad->addFile( $this->file( '20261107100001_bad.sql', "-- transactional: perhaps\n" ) );
        $this->assertStringContainsString( 'transactional: perhaps is not yes or no', implode( "\n", $bad->errors() ) );
        $this->assertTrue( $bad->transactional() );
    }

    /** MQ-03 */
    public function testFiles()
    {
        $parts = expMigrationStepSql::parseSqlFile( "-- description: x\nCREATE TABLE a ( id INT );\nINSERT INTO a VALUES ( 1 );\n-- down\nDROP TABLE a;\n" );
        $this->assertSame( array( 'CREATE TABLE a ( id INT )', 'INSERT INTO a VALUES ( 1 )' ), $parts['up'] );
        $this->assertSame( array( 'DROP TABLE a' ), $parts['down'] );
        $this->assertNull( expMigrationStepSql::parseSqlFile( "SELECT 1;\n-- downward\nSELECT 2;\n" )['down'], 'only a line "-- down" starts the down part' );
        $this->assertSame( array(), expMigrationStepSql::parseSqlFile( "SELECT 1;\n-- Down:\n" )['down'] );

        $errors = array();
        $this->assertSame( array( array( 'drop_collection' => 'a' ) ), expMigrationStepSql::parseMongoFile( '[ { "drop_collection": "a" } ]', $errors )['up'] );
        $this->assertSame( array(), $errors );
        $both = expMigrationStepSql::parseMongoFile( '{ "up": [ { "create_collection": "a" } ], "down": [ { "drop_collection": "a" } ] }', $errors );
        $this->assertSame( array(), $errors );
        $this->assertSame( array( array( 'drop_collection' => 'a' ) ), $both['down'] );
        expMigrationStepSql::parseMongoFile( '{ "up": [ { "insert": "a" }, { "update": "a", "filter": {} }, { "create_index": "a", "keys": { "x": 1 } }, { "rename": "a" }, 7 ], "other": 1 }', $errors );
        $text = implode( "\n", $errors );
        foreach ( array( 'the unknown key other', 'up[0].documents: a list of documents', 'up[1]: set or unset', 'up[2].name: the name of the index', 'up[3]: one of insert', 'up[4]: a command is a map' ) as $expected )
            $this->assertStringContainsString( $expected, $text );
        $errors = array();
        expMigrationStepSql::parseMongoFile( 'not json', $errors );
        $this->assertStringContainsString( 'not JSON', $errors[0] );

        $portable = $this->file( '20261107110000_pick.sql', "SELECT 0;\n" );
        $mysql = $this->file( '20261107110000_pick.mysql.sql', "SELECT 1;\n" );
        $pgsql = $this->file( '20261107110000_pick.pgsql.sql', "SELECT 2;\n" );
        $this->assertSame( $mysql, expMigrationStepSql::fileFor( $portable, 'mysql' ) );
        $this->assertSame( $pgsql, expMigrationStepSql::fileFor( $portable, 'postgresql' ) );
        $this->assertSame( $portable, expMigrationStepSql::fileFor( $mysql, 'sqlite' ), 'any of the files names the set' );
        $this->assertNull( expMigrationStepSql::fileFor( $portable, 'mongodb' ) );

        foreach ( array( 'CREATE INDEX a ON b ( c )', ' alter table a add x int', 'DROP TABLE a', 'TRUNCATE a', 'RENAME TABLE a TO b' ) as $ddl )
            $this->assertTrue( expMigrationStepSql::isSchemaChange( $ddl ), $ddl );
        foreach ( array( 'INSERT INTO a VALUES ( 1 )', 'UPDATE a SET created = 1', "DELETE FROM a WHERE b = 'DROP TABLE'" ) as $dml )
            $this->assertFalse( expMigrationStepSql::isSchemaChange( $dml ), $dml );
        $this->assertTrue( expMigrationStepSql::safeFile( 'sql/a.sql' ) );
        foreach ( array( '/etc/a.sql', '../a.sql', 'sql/../../a.sql' ) as $outside )
            $this->assertFalse( expMigrationStepSql::safeFile( $outside ), $outside );
    }

    /** MQ-04 */
    public function testSchema()
    {
        $create = array( 'mode' => 'create_table', 'table' => 'exp_test_wp11_rating',
                         'columns' => array( 'id' => array( 'type' => 'auto_increment' ),
                                             'object_id' => array( 'type' => 'int', 'not_null' => true, 'default' => 0 ),
                                             'comment' => array( 'type' => 'varchar', 'length' => 255, 'default' => '' ) ),
                         'indexes' => array( 'exp_test_wp11_rating_object' => array( 'columns' => array( 'object_id' ) ) ) );
        $this->assertSame( '', self::problems( 'schema', $create ) );
        $broken = array( 'mode' => 'create_table', 'table' => 'bad-name', 'primary_key' => array( 'nope' ),
                         'columns' => array( 'a' => array( 'type' => 'varchar' ), 'b' => array( 'type' => 'blob' ), 'c' => array( 'type' => 'int', 'size' => 4 ) ),
                         'indexes' => array( 'i' => array( 'columns' => array( 'zzz' ) ) ) );
        $text = self::problems( 'schema', $broken );
        foreach ( array( 'table: a name', 'columns.a.length: varchar needs a length', 'columns.b.type: one of', 'columns.c: the unknown key size',
                         'primary_key: nope is not one of the columns', 'indexes.i: zzz not among the columns' ) as $expected )
            $this->assertStringContainsString( $expected, $text );
        $this->assertStringContainsString( 'column is missing', self::problems( 'schema', array( 'mode' => 'add_column', 'table' => 't', 'definition' => array( 'type' => 'int' ) ) ) );
        $this->assertStringContainsString( 'made with its table', self::problems( 'schema', array( 'mode' => 'add_column', 'table' => 't', 'column' => 'c', 'definition' => array( 'type' => 'auto_increment' ) ) ) );
        $this->assertStringContainsString( 'columns: the list', self::problems( 'schema', array( 'mode' => 'add_index', 'table' => 't', 'index' => 'i' ) ) );

        $table = expMigrationStepSchemaChange::table( self::step( 'schema', $create ) );
        $this->assertSame( array( 'type' => 'primary', 'fields' => array( 'id' ) ), $table['indexes']['PRIMARY'], 'an auto_increment column is the primary key' );
        $this->assertSame( array( 'type' => 'non-unique', 'fields' => array( 'object_id' ) ), $table['indexes']['exp_test_wp11_rating_object'] );
        $this->assertSame( array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ), $table['fields']['object_id'] );
        $this->assertSame( array( 'type' => 'longtext', 'not_null' => '0', 'default' => null ), expMigrationStepSchemaChange::field( array( 'type' => 'text' ) ) );

        $type = new expMigrationStepSchemaChange();
        $statements = new ReflectionMethod( $type, 'statements' );
        $null = expKernelTableSchema::nullDB();
        foreach ( array( 'mysql' => 'AUTO_INCREMENT', 'postgresql' => 'nextval', 'sqlite' => 'AUTOINCREMENT' ) as $engine => $serial )
        {
            $sql = implode( ";\n", $statements->invoke( $type, self::step( 'schema', $create ), $engine, $null ) );
            $this->assertMatchesRegularExpression( '/CREATE TABLE (IF NOT EXISTS )?exp_test_wp11_rating/', $sql, $engine );
            $this->assertStringContainsStringIgnoringCase( $serial, $sql, $engine );
            $add = implode( ";\n", $statements->invoke( $type, self::step( 'schema', array( 'mode' => 'add_column', 'table' => 'exp_test_wp11_rating', 'column' => 'created',
                                                                                          'definition' => array( 'type' => 'int', 'not_null' => true, 'default' => 0 ) ) ), $engine, $null ) );
            $this->assertStringContainsString( 'ALTER TABLE exp_test_wp11_rating ADD COLUMN created', $add, $engine );
            $this->assertStringContainsString( 'NOT NULL', $add, $engine );
            $index = implode( ";\n", $statements->invoke( $type, self::step( 'schema', array( 'mode' => 'add_index', 'table' => 'exp_test_wp11_rating', 'index' => 'exp_test_wp11_rating_created',
                                                                                            'columns' => array( 'created' ) ) ), $engine, $null ) );
            $this->assertStringContainsString( 'exp_test_wp11_rating_created', $index, $engine );
            $this->assertStringContainsString( 'created', $index, $engine );
        }
        $this->assertSame( array(), $statements->invoke( $type, self::step( 'schema', $create ), 'mongodb', $null ), 'MongoDB has no SQL' );
    }

    /** MQ-05 */
    public function testData()
    {
        $this->assertSame( '', self::problems( 'data', array( 'mode' => 'ensure', 'table' => 'ezsite_data', 'key' => array( 'name' ), 'rows' => array( array( 'name' => 'a', 'value' => '1' ) ) ) ) );
        $text = self::problems( 'data', array( 'mode' => 'ensure', 'table' => 't', 'key' => array( 'name' ), 'rows' => array( array( 'value' => '1' ), array( 'x' => array( 1 ) ), 'row' ) ) );
        foreach ( array( 'rows[0]: the key column name is missing', 'rows[1].x: a string, a number', 'rows[2]: a map of column => value' ) as $expected )
            $this->assertStringContainsString( $expected, $text );
        $this->assertStringContainsString( 'where: the rows to delete (all: true for every row)', self::problems( 'data', array( 'mode' => 'delete', 'table' => 't' ) ) );
        $this->assertSame( '', self::problems( 'data', array( 'mode' => 'delete', 'table' => 't', 'all' => true ) ) );
        $this->assertStringContainsString( 'all: not with where', self::problems( 'data', array( 'mode' => 'update', 'table' => 't', 'where' => array( 'a' => 1 ), 'values' => array( 'b' => 2 ), 'all' => true ) ) );

        $db = new class {
            public function escapeString( $text ) { return str_replace( "'", "''", $text ); }
        };
        $this->assertSame( "'it''s'", expMigrationStepData::literal( $db, "it's" ) );
        $this->assertSame( 'NULL', expMigrationStepData::literal( $db, null ) );
        $this->assertSame( '1', expMigrationStepData::literal( $db, true ) );
        $this->assertSame( '2.5', expMigrationStepData::literal( $db, 2.5 ) );
        $this->assertSame( "'7'", expMigrationStepData::literal( $db, '7' ), 'a string stays a string' );
        $this->assertSame( " WHERE a = 1 AND b IS NULL AND c IN ('x', 'y')", expMigrationStepData::whereSql( $db, array( 'a' => 1, 'b' => null, 'c' => array( 'x', 'y' ) ) ) );
        $this->assertSame( ' WHERE 1 = 0', expMigrationStepData::whereSql( $db, array( 'e' => array() ) ), 'an empty list matches nothing' );
        $this->assertSame( '', expMigrationStepData::whereSql( $db, array() ) );
        $this->assertSame( array( 'a' => 1, 'c' => array( '$in' => array( 'x', 'y' ) ) ), expMigrationStepData::filter( array( 'a' => 1, 'c' => array( 'x', 'y' ) ) ) );
    }

    /** MQ-06 */
    public function testSql()
    {
        $this->assertStringContainsString( 'the statements to run', self::problems( 'sql', array() ) );
        $this->assertStringContainsString( 'either a file or statements', self::problems( 'sql', array( 'file' => 'a.sql', 'sql' => 'SELECT 1;' ) ) );
        $this->assertStringContainsString( 'part: only with file', self::problems( 'sql', array( 'sql' => 'SELECT 1;', 'part' => 'down' ) ) );
        $this->assertStringContainsString( 'a relative path below', self::problems( 'sql', array( 'file' => '../a.sql' ) ) );
        $this->assertStringContainsString( 'mongodb[0]: one of', self::problems( 'sql', array( 'mongodb' => array( array( 'nothing' => 'a' ) ) ) ) );

        $type = new expMigrationStepSql();
        $step = self::step( 'sql', array( 'sql' => "UPDATE a SET b = 1;\nUPDATE a SET c = 2;", 'mysql' => 'UPDATE a SET b = 3;' ) );
        $this->assertSame( array( 'mysql: UPDATE a SET b = 3' ), $type->plan( $step, self::engineContext( 'mysql' ) ) );
        $this->assertSame( array( 'sqlite: UPDATE a SET b = 1', 'sqlite: UPDATE a SET c = 2' ), $type->plan( $step, self::engineContext( 'sqlite' ) ) );
        $this->assertSame( array( 'sql: nothing for mongodb (fails)' ), $type->plan( $step, self::engineContext( 'mongodb' ) ) );
        try
        {
            $type->apply( $step, self::engineContext( 'mongodb' ) );
            $this->fail( 'no statements for MongoDB' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'no statements for mongodb', $e->getMessage() );
        }
        $skip = self::step( 'sql', array( 'sql' => 'SELECT 1;', 'missing' => 'skip' ) );
        $this->assertSame( array( 'statements' => 0, 'engine' => 'mongodb' ), $type->apply( $skip, self::engineContext( 'mongodb' ) )['values'] );

        $file = $this->file( '20261107120000_files.sql', "SELECT 0;\n-- down\nSELECT 9;\n" );
        $this->file( '20261107120000_files.mongodb.json', '{ "up": [ { "drop_collection": "a" } ] }' );
        $byFile = self::step( 'sql', array( 'file' => '20261107120000_files.sql' ) );
        $this->assertSame( array( 'postgresql: SELECT 0' ), $type->plan( $byFile, self::engineContext( 'postgresql', $file ) ) );
        $this->assertSame( array( 'postgresql: SELECT 9' ), $type->plan( self::step( 'sql', array( 'file' => '20261107120000_files.sql', 'part' => 'down' ) ), self::engineContext( 'postgresql', $file ) ) );
        $this->assertSame( array( 'mongodb: {"drop_collection":"a"}' ), $type->plan( $byFile, self::engineContext( 'mongodb', $file ) ) );
    }

    /** MQ-07 */
    public function testIniMigrationText()
    {
        $text = <<<'INI'
#?ini charset="utf-8"?
# description: News settings
[news.ini/NewsSettings]
ListLimit=20
ShownClasses[]
ShownClasses[]=article
ShownClasses[]=blog_post
[siteaccess:admin site.ini/SiteSettings]
DefaultPage=content/dashboard
[site.ini/ExtensionSettings]
ActiveExtensions[]=expnews
[news.ini/MailSettings]
TransportAlias[smtp]=eZSMTPTransport
-DebugByIP
-DebugRedirection[ip]
-AllowedIPs[]=127.0.0.1
INI;
        $errors = array();
        $steps = expMigrationIni::steps( $text, $errors );
        $this->assertSame( array(), $errors );
        $this->assertSame( array(
            array( 'type' => 'settings', 'file' => 'news.ini',
                   'set' => array( 'NewsSettings' => array( 'ListLimit' => '20', 'ShownClasses' => array( 'article', 'blog_post' ) ),
                                   'MailSettings' => array( 'TransportAlias' => array( 'smtp' => 'eZSMTPTransport' ) ) ),
                   'remove' => array( 'MailSettings' => array( 'DebugByIP', 'DebugRedirection[ip]', 'AllowedIPs[]=127.0.0.1' ) ) ),
            array( 'type' => 'settings', 'file' => 'site.ini', 'siteaccess' => 'admin', 'set' => array( 'SiteSettings' => array( 'DefaultPage' => 'content/dashboard' ) ) ),
            array( 'type' => 'settings', 'file' => 'site.ini', 'add' => array( 'ExtensionSettings' => array( 'ActiveExtensions' => array( 'expnews' ) ) ) ),
        ), $steps );
        foreach ( $steps as $step )
            $this->assertSame( '', self::problems( 'settings', array_diff_key( $step, array( 'type' => true ) ) ) );

        $errors = array();
        expMigrationIni::steps( "Before=1\n[NewsSettings]\nA=1\n[news.ini/B]\nA=1\nA[]\n~nonsense\n-Bad[\n", $errors );
        $text = implode( "\n", $errors );
        foreach ( array( 'line 1: a setting before the first block', 'line 2: [NewsSettings] does not name its file', 'line 6: A is a value and an array',
                         'line 7: ~nonsense is not', 'line 8: -Bad[ is not' ) as $expected )
            $this->assertStringContainsString( $expected, $text );
    }

    /**
     * A settings tree of the test: settings/override with site.ini.append.php, settings/siteaccess/admin, and the INI
     * editor working on it.
     *
     * @return string the root, with a / at the end
     */
    private function settingsRoot()
    {
        $root = $this->dir() . '/root/';
        mkdir( $root . 'settings/override', 0775, true );
        mkdir( $root . 'settings/siteaccess/admin', 0775, true );
        mkdir( $root . 'var', 0775, true );
        file_put_contents( $root . 'settings/override/site.ini.append.php',
                           "<?php /* #?ini charset=\"utf-8\"?\n\n[SiteSettings]\nSiteName=Old\n# a comment that stays\nIndexPage=/content/view/full/2\n\n[DebugSettings]\nDebugOutput=enabled\n*/ ?>\n" );
        chmod( $root . 'settings/override/site.ini.append.php', 0640 );
        expIniEditor::setRoot( $root, array(), array( 'admin' ) );
        return $root;
    }

    /** MQ-08 */
    public function testSettingsStep()
    {
        $root = $this->settingsRoot();
        $type = new expMigrationStepSettings();
        $step = self::step( 'settings', array( 'file' => 'site.ini',
                                               'set' => array( 'SiteSettings' => array( 'SiteName' => 'New', 'IndexPage' => '/content/view/full/2' ),
                                                               'RegionalSettings' => array( 'SiteLanguageList' => array( 'eng-GB', 'ger-DE' ) ),
                                                               'MailSettings' => array( 'SMTPPassword' => 'hidden-value', 'TransportAlias' => array( 'smtp' => 'eZSMTPTransport' ) ) ),
                                               'add' => array( 'ExtensionSettings' => array( 'ActiveExtensions' => array( 'expnews' ) ) ),
                                               'remove' => array( 'DebugSettings' => array( 'DebugOutput', 'DebugByIP' ) ) ) );
        $plan = implode( "\n", $type->plan( $step, null ) );
        $this->assertStringContainsString( 'write settings/override/site.ini.append.php (a backup first):', $plan );
        $this->assertStringContainsString( 'set [SiteSettings] SiteName = New', $plan );
        $this->assertStringNotContainsString( 'IndexPage', $plan, 'what the file says already is not a change' );
        $this->assertStringNotContainsString( 'hidden-value', $plan, 'a secret is masked' );
        $this->assertStringContainsString( 'remove [DebugSettings] DebugOutput', $plan );
        $this->assertStringNotContainsString( 'DebugByIP', $plan, 'a removal of what is not there is no change' );
        $this->assertStringContainsString( "<?php /* #?ini charset=\"utf-8\"?\n\n[SiteSettings]\nSiteName=Old", file_get_contents( $root . 'settings/override/site.ini.append.php' ), 'a plan writes nothing' );

        $result = $type->apply( $step, null );
        $this->assertSame( array( 'file' => 'settings/override/site.ini.append.php', 'changed' => true ), $result['values'] );
        $this->assertStringContainsString( 'the old file kept as var/backup/ini/', $result['lines'][0] );
        $written = file_get_contents( $root . 'settings/override/site.ini.append.php' );
        foreach ( array( 'SiteName=New', '# a comment that stays', "SiteLanguageList[]\nSiteLanguageList[]=eng-GB\nSiteLanguageList[]=ger-DE", 'TransportAlias[smtp]=eZSMTPTransport',
                         'ActiveExtensions[]=expnews', 'SMTPPassword=hidden-value' ) as $expected )
            $this->assertStringContainsString( $expected, $written );
        $this->assertStringNotContainsString( 'DebugOutput', $written );
        $this->assertSame( 0640, fileperms( $root . 'settings/override/site.ini.append.php' ) & 0777, 'the mode is kept' );
        $backups = glob( $root . 'var/backup/ini/*/settings/override/site.ini.append.php' );
        $this->assertCount( 1, $backups );
        $this->assertStringContainsString( 'SiteName=Old', file_get_contents( $backups[0] ) );

        $again = $type->apply( $step, null );
        $this->assertSame( false, $again['values']['changed'], 'a second run changes nothing' );
        $this->assertSame( $written, file_get_contents( $root . 'settings/override/site.ini.append.php' ) );
        $this->assertCount( 1, glob( $root . 'var/backup/ini/*/settings/override/site.ini.append.php' ), 'and backs nothing up' );

        $admin = $type->apply( self::step( 'settings', array( 'file' => 'site.ini', 'siteaccess' => 'admin', 'set' => array( 'SiteSettings' => array( 'DefaultPage' => true ) ) ) ), null );
        $this->assertSame( 'settings/siteaccess/admin/site.ini.append.php', $admin['values']['file'] );
        $this->assertStringContainsString( 'DefaultPage=enabled', file_get_contents( $root . 'settings/siteaccess/admin/site.ini.append.php' ) );

        try
        {
            $type->apply( self::step( 'settings', array( 'file' => 'site.ini', 'siteaccess' => 'nosuch', 'set' => array( 'A' => array( 'B' => 'c' ) ) ) ), null );
            $this->fail( 'an unknown siteaccess' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( "Unknown siteaccess 'nosuch'", $e->getMessage() );
        }
        $this->assertStringContainsString( 'file: the name of an INI file', self::problems( 'settings', array( 'file' => '../site.ini', 'set' => array( 'A' => array( 'B' => 'c' ) ) ) ) );
        $this->assertStringContainsString( 'set, add or remove', self::problems( 'settings', array( 'file' => 'site.ini' ) ) );
        $this->assertStringContainsString( 'remove.A: x[ is not', self::problems( 'settings', array( 'file' => 'site.ini', 'remove' => array( 'A' => array( 'x[' ) ) ) ) );
    }

    /** MQ-09 */
    public function testLoad()
    {
        $locator = function () {
            $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => $this->dir() . '/migrations' ) ) );
            $definitions = $locator->find();
            $this->assertSame( array(), $locator->errors() );
            $byId = array();
            foreach ( $definitions as $definition )
                $byId[$definition->id()] = $definition;
            return $byId;
        };
        $this->file( '20261107130000_table.sql', "-- description: A table\nCREATE TABLE exp_test_wp11 ( id INT );\n-- down\nDROP TABLE exp_test_wp11;\n" );
        $this->file( '20261107130000_table.mysql.sql', "-- description: A table (MySQL)\nCREATE TABLE exp_test_wp11 ( id INT ) ENGINE=InnoDB;\n" );
        $this->file( '20261107130100_plain.pgsql.sql', "-- transactional: no\nCREATE INDEX CONCURRENTLY a ON b ( c );\n" );
        $this->file( '20261107130200_settings.ini', "# description: Settings\n[site.ini/SiteSettings]\nSiteName=Example\n" );
        $this->file( '20261107130300_broken.ini', "[SiteSettings]\nSiteName=Example\n" );
        $this->file( '20261107130400_broken.mongodb.json', "[ 1 ]\n" );
        $byId = $locator();

        $warnings = array();
        $errors = array();
        $table = expMigrationSql::read( $byId['20261107130000_table'], null, $errors, $warnings );
        $this->assertSame( array(), $errors );
        $this->assertInstanceOf( 'expMigrationYaml', $table );
        $this->assertSame( array( array( 'type' => 'sql', 'file' => '20261107130000_table.sql' ) ), $table->steps() );
        $this->assertSame( array( array( 'type' => 'sql', 'file' => '20261107130000_table.sql', 'part' => 'down' ) ), $table->downSteps() );
        $this->assertTrue( $table->reversible() );
        $this->assertTrue( $table->transactional() );
        $this->assertStringContainsString( '20261107130000_table.mysql.sql has no "-- down" part', implode( "\n", $warnings ) );

        $plain = expMigrationSql::load( $byId['20261107130100_plain'] );
        $this->assertFalse( $plain->transactional(), 'transactional: no in the header' );
        $this->assertFalse( $plain->reversible(), 'no down part: only the caption undoes it' );
        $this->assertSame( array( 'postgresql' ), $plain->engines() );

        $settings = expMigrationIni::load( $byId['20261107130200_settings'] );
        $this->assertSame( array( array( 'type' => 'settings', 'file' => 'site.ini', 'set' => array( 'SiteSettings' => array( 'SiteName' => 'Example' ) ) ) ), $settings->steps() );
        $this->assertFalse( $settings->reversible() );

        $loadErrors = array();
        $loaded = expMigrationRunner::load( array( $byId['20261107130300_broken'], $byId['20261107130400_broken'], $byId['20261107130200_settings'] ), $loadErrors );
        $this->assertCount( 1, $loaded );
        $text = implode( "\n", $loadErrors );
        $this->assertStringContainsString( '20261107130300_broken: line 1: [SiteSettings] does not name its file', $text );
        $this->assertStringContainsString( '20261107130400_broken: 20261107130400_broken.mongodb.json: up[0]: a command is a map', $text );
    }
}
