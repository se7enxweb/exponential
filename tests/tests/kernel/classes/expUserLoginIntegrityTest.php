<?php
/**
 * expUserLoginIntegrity: the logins more than one account has are found (also those that differ only in case), the
 * unique index on ezuser.login is refused while there are any and added after, and an account whose login another
 * account has is not stored, neither by expUserLoginIntegrity::storeUser() nor by the user datatype.
 *
 * SQLite runs for real, on a database in memory. MySQL, PostgreSQL and Oracle are covered by a stand-in that
 * answers the catalogue queries of each engine and records the statements: the statements --fix-index runs, and
 * that it runs none while there are duplicates. MongoDB's grouping (done in PHP, byte for byte for the index) is
 * covered through groupRows().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 826e1d4647), Copyright (C) Mugo Web, GPL v2
 */

require_once __DIR__ . '/datatypes/eZDatatypeTestFixtures.php';
require_once __DIR__ . '/../../../../kernel/classes/expuserloginintegrity.php';

/**
 * A database of one engine that answers the duplicate and index queries from rows the test gives it.
 */
class expUserLoginIntegrityTestEngineDB extends eZDBInterface
{
    public $engine;
    /** @var array[] contentobject_id, login */
    public $users = array();
    /** @var array[] the catalogue rows for the engine's index query */
    public $indexRows = array();
    /** @var string[] statements other than SELECTs */
    public $statements = array();
    /** @var bool the next catalogue read shows the unique index (after the CREATE ran) */
    public $created = false;

    public function __construct( $engine )
    {
        $this->engine = $engine;
        $this->DB = 'k1-login';
    }

    function databaseName()
    {
        return $this->engine;
    }

    function isConnected()
    {
        return true;
    }

    function escapeString( $str )
    {
        return str_replace( "'", "''", (string)$str );
    }

    function query( $sql, $server = false )
    {
        $this->statements[] = preg_replace( '/\s+/', ' ', trim( $sql ) );
        if ( preg_match( '/CREATE UNIQUE INDEX|ADD UNIQUE KEY/i', $sql ) )
            $this->created = true;
        return true;
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        if ( preg_match( '/GROUP BY LOWER\( login \)/', $sql ) )
            return $this->groups( true );
        if ( preg_match( '/GROUP BY login/', $sql ) )
            return $this->groups( false );
        if ( preg_match( "/WHERE LOWER\\( login \\) = '(.*)'/", $sql, $m ) )
            return array_values( array_filter( $this->users, function ( $u ) use ( $m ) { return strtolower( $u['login'] ) === str_replace( "''", "'", $m[1] ); } ) );
        if ( preg_match( "/WHERE login = '(.*)'/", $sql, $m ) )
            return array_values( array_filter( $this->users, function ( $u ) use ( $m ) { return $u['login'] === str_replace( "''", "'", $m[1] ); } ) );
        if ( preg_match( '/SHOW INDEX|pg_indexes|user_indexes/', $sql ) )
            return $this->created ? $this->uniqueRows() : $this->indexRows;
        return array();
    }

    private function groups( $lower )
    {
        $count = array();
        foreach ( $this->users as $u )
        {
            $key = $lower ? strtolower( $u['login'] ) : $u['login'];
            $count[$key] = isset( $count[$key] ) ? $count[$key] + 1 : 1;
        }
        $rows = array();
        foreach ( $count as $key => $n )
            if ( $n > 1 )
                $rows[] = array( 'login_key' => (string)$key, 'row_count' => $n );
        return $rows;
    }

    private function uniqueRows()
    {
        switch ( $this->engine )
        {
            case 'mysql':
                return array( array( 'Key_name' => 'ezuser_login', 'Column_name' => 'login', 'Non_unique' => 0 ) );
            case 'postgresql':
                return array( array( 'indexname' => 'ezuser_login', 'indexdef' => 'CREATE UNIQUE INDEX ezuser_login ON public.ezuser USING btree (login)' ) );
            case 'oracle':
                return array( array( 'INDEX_NAME' => 'EZUSER_LOGIN', 'UNIQUENESS' => 'UNIQUE' ) );
        }
        return array();
    }
}

class expUserLoginIntegrityTest extends eZDatatypeTestCase
{
    /** @var eZSQLite3DB|null */
    private $sqlite;

    protected function setUp(): void
    {
        parent::setUp();
        unset( $GLOBALS['eZUserObject_812'], $GLOBALS['eZUserObject_813'] );
    }

    protected function tearDown(): void
    {
        unset( $GLOBALS['eZUserObject_812'], $GLOBALS['eZUserObject_813'] );
        $this->sqlite = null;
        parent::tearDown();
    }

    /**
     * A SQLite database in memory with the user tables of the kernel schema (the plain index on login, as an
     * upgraded site has it) and the given accounts, made the current database.
     *
     * @param array $logins contentobject_id => login
     * @return eZSQLite3DB
     */
    private function sqlite( array $logins )
    {
        if ( !extension_loaded( 'sqlite3' ) )
            $this->markTestSkipped( 'SQLite3 extension is not loaded' );
        $db = new eZSQLite3DB( array(
            'server' => '', 'port' => '', 'user' => '', 'password' => '', 'database' => ':memory:',
            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => '', 'slave_user' => '', 'slave_password' => '',
            'slave_database' => '', 'socket' => false, 'charset' => 'utf-8', 'is_internal_charset' => true,
            'builtin_encoding' => false, 'connect_retries' => 0, 'use_persistent_connection' => 'disabled' ) );
        $db->query( "CREATE TABLE ezuser ( contentobject_id INTEGER NOT NULL DEFAULT 0, email varchar(150) NOT NULL DEFAULT '',
                     login varchar(150) NOT NULL DEFAULT '', password_hash varchar(255) DEFAULT NULL,
                     password_hash_type INTEGER NOT NULL DEFAULT 1, PRIMARY KEY ( contentobject_id ) )" );
        $db->query( 'CREATE INDEX ezuser_login ON ezuser ( login )' );
        $db->query( 'CREATE TABLE ezuser_setting ( is_enabled INTEGER NOT NULL DEFAULT 0, max_login INTEGER DEFAULT NULL, user_id INTEGER NOT NULL DEFAULT 0, PRIMARY KEY ( user_id ) )' );
        $db->query( "CREATE TABLE ezuser_accountkey ( hash_key varchar(32) NOT NULL DEFAULT '', id INTEGER PRIMARY KEY AUTOINCREMENT, time INTEGER NOT NULL DEFAULT 0, user_id INTEGER NOT NULL DEFAULT 0 )" );
        foreach ( $logins as $id => $login )
            $db->query( "INSERT INTO ezuser ( contentobject_id, email, login, password_hash, password_hash_type ) VALUES ( " . (int)$id . ", '', '" .
                        $db->escapeString( $login ) . "', 'x', 7 )" );
        eZDB::setInstance( $db );
        return $this->sqlite = $db;
    }

    private function sqliteValue( $sql )
    {
        $rows = $this->sqlite->arrayQuery( $sql );
        return $rows ? current( $rows[0] ) : null;
    }

    public function testSQLiteFindsByteEqualAndCaseOnlyDuplicates()
    {
        $db = $this->sqlite( array( 10 => 'anna', 11 => 'anna', 12 => 'Bob', 13 => 'bob', 14 => 'carl', 15 => 'BOB' ) );
        $groups = expUserLoginIntegrity::duplicates( $db );
        $this->assertCount( 2, $groups );

        $this->assertSame( array( 10, 11 ), $groups[0]['user_ids'] );
        $this->assertSame( array( 'anna' ), $groups[0]['logins'] );
        $this->assertTrue( $groups[0]['blocks_index'], 'byte-equal logins: the unique index refuses them' );

        $this->assertSame( array( 12, 13, 15 ), $groups[1]['user_ids'] );
        $this->assertCount( 3, $groups[1]['logins'] );
        $this->assertFalse( $groups[1]['blocks_index'], 'SQLite compares bytes: logins differing in case pass its index' );
    }

    public function testSQLiteIndexIsRefusedWhileThereAreDuplicatesAndAddedAfter()
    {
        $db = $this->sqlite( array( 10 => 'anna', 11 => 'Anna', 12 => 'carl' ) );
        $this->assertSame( array( 'engine' => 'sqlite', 'exists' => true, 'unique' => false, 'names' => array( 'ezuser_login' ) ),
                           expUserLoginIntegrity::indexStatus( $db ) );

        $this->assertFalse( expUserLoginIntegrity::addUniqueIndex( $db, $message ), 'a case-only duplicate is refused too' );
        $this->assertStringStartsWith( 'refused:', $message );
        $this->assertFalse( expUserLoginIntegrity::indexStatus( $db )['unique'], 'nothing changed' );

        $db->query( "UPDATE ezuser SET login = 'anna2' WHERE contentobject_id = 11" );
        $this->assertSame( array(), expUserLoginIntegrity::duplicates( $db ) );
        $this->assertTrue( expUserLoginIntegrity::addUniqueIndex( $db, $message ), (string)$message );
        $this->assertStringStartsWith( 'added the unique index', $message );
        $this->assertSame( array( 'engine' => 'sqlite', 'exists' => true, 'unique' => true, 'names' => array( 'ezuser_login' ) ),
                           expUserLoginIntegrity::indexStatus( $db ), 'the name the database had is kept' );

        $this->assertTrue( expUserLoginIntegrity::addUniqueIndex( $db, $message ) );
        $this->assertStringStartsWith( 'the unique index is there already', $message );

        // and the database refuses a second account with the login now
        $this->assertFalse( @$db->query( "INSERT INTO ezuser ( contentobject_id, login ) VALUES ( 20, 'carl' )" ) );
        $this->assertTrue( expUserLoginIntegrity::isDuplicateKeyError( $db, $db->errorNumber(), $db->errorMessage() ) );
    }

    public function testSQLiteBlankLoginsAreDuplicatesToo()
    {
        $db = $this->sqlite( array( 10 => '', 11 => '', 12 => 'carl' ) );
        $groups = expUserLoginIntegrity::duplicates( $db );
        $this->assertCount( 1, $groups );
        $this->assertSame( array( 10, 11 ), $groups[0]['user_ids'] );
        $this->assertTrue( $groups[0]['blocks_index'] );
        $this->assertFalse( expUserLoginIntegrity::addUniqueIndex( $db ) );
    }

    public function testStoreUserRefusesALoginAnotherAccountHas()
    {
        $this->sqlite( array( 813 => 'Taken' ) );
        $user = eZUser::create( 812 );
        $user->setAttribute( 'login', 'taken' );
        $user->setAttribute( 'email', 'k1@example.org' );
        $this->assertSame( 813, expUserLoginIntegrity::loginTakenBy( 'taken', 812 ) );
        $this->assertNull( expUserLoginIntegrity::loginTakenBy( 'taken', 813 ), 'its own login is not taken' );
        $this->assertFalse( expUserLoginIntegrity::storeUser( $user ) );
        $this->assertSame( 0, (int)$this->sqliteValue( 'SELECT COUNT(*) FROM ezuser WHERE contentobject_id = 812' ) );

        $user->setAttribute( 'login', 'free' );
        $this->assertTrue( expUserLoginIntegrity::storeUser( $user ) );
        $this->assertSame( 'free', $this->sqliteValue( 'SELECT login FROM ezuser WHERE contentobject_id = 812' ) );
    }

    public function testFirstVersionWithAValidationErrorStoresNoAccount()
    {
        $this->sqlite( array( 813 => 'taken' ) );
        $user = eZUser::create( 812 );
        $user->setAttribute( 'login', 'taken' );
        $user->setAttribute( 'email', 'k1@example.org' );
        $attribute = $this->objectAttribute( 'ezuser', array(), array( 'version' => 1 ) );
        $attribute->setContent( $user );
        $attribute->setValidationError( 'The username already exists, please choose another one.' );
        $this->dataType( 'ezuser' )->storeObjectAttribute( $attribute );
        $this->assertSame( 0, (int)$this->sqliteValue( 'SELECT COUNT(*) FROM ezuser WHERE contentobject_id = 812' ) );
        $this->assertSame( 1, (int)$this->sqliteValue( "SELECT COUNT(*) FROM ezuser WHERE login = 'taken'" ) );
    }

    public function testAStoredAccountIsNotGivenALoginAnotherAccountHas()
    {
        $this->sqlite( array( 812 => 'mine', 813 => 'taken' ) );
        $user = eZUser::fetch( 812 );
        $user->setAttribute( 'login', 'TAKEN' );
        $attribute = $this->objectAttribute( 'ezuser', array(), array( 'version' => 1 ) );
        $attribute->setContent( $user );
        $this->dataType( 'ezuser' )->storeObjectAttribute( $attribute );
        $this->assertSame( 'mine', $this->sqliteValue( 'SELECT login FROM ezuser WHERE contentobject_id = 812' ) );
        $this->assertSame( 'mine', $attribute->content()->attribute( 'login' ), 'the attribute shows the account as stored' );
    }

    public function testAFirstVersionWithAFreeLoginCreatesTheAccount()
    {
        $this->sqlite( array( 813 => 'taken' ) );
        $user = eZUser::create( 812 );
        $user->setAttribute( 'login', 'k1free' );
        $user->setAttribute( 'email', 'k1@example.org' );
        $attribute = $this->objectAttribute( 'ezuser', array(), array( 'version' => 1 ) );
        $attribute->setContent( $user );
        $this->dataType( 'ezuser' )->storeObjectAttribute( $attribute );
        $this->assertSame( 'k1free', $this->sqliteValue( 'SELECT login FROM ezuser WHERE contentobject_id = 812' ) );
        $this->assertSame( 1, (int)$this->sqliteValue( 'SELECT is_enabled FROM ezuser_setting WHERE user_id = 812' ) );
    }

    public function testMongoGroupingComparesBytesForTheIndex()
    {
        $groups = expUserLoginIntegrity::groupRows( array(
            array( 'contentobject_id' => 1, 'login' => 'anna' ), array( 'contentobject_id' => 2, 'login' => 'anna' ),
            array( 'contentobject_id' => 3, 'login' => 'Émile' ), array( 'contentobject_id' => 4, 'login' => 'émile' ),
            array( 'contentobject_id' => 5, 'login' => null ), array( 'contentobject_id' => 6, 'login' => null ),
            array( 'contentobject_id' => 7, 'login' => 'zoe' ) ) );
        $this->assertCount( 2, $groups );
        $this->assertSame( array( 1, 2 ), $groups[0]['user_ids'] );
        $this->assertTrue( $groups[0]['blocks_index'] );
        $this->assertSame( array( 3, 4 ), $groups[1]['user_ids'] );
        $this->assertFalse( $groups[1]['blocks_index'], 'case-only, in UTF-8' );
    }

    public static function engines()
    {
        return array(
            'mysql' => array( 'mysql', array( array( 'Key_name' => 'ezuser_login', 'Column_name' => 'login', 'Non_unique' => 1 ),
                                              array( 'Key_name' => 'PRIMARY', 'Column_name' => 'contentobject_id', 'Non_unique' => 0 ) ),
                              array( 'ALTER TABLE ezuser DROP INDEX `ezuser_login`, ADD UNIQUE KEY ezuser_login ( login )' ) ),
            'postgresql' => array( 'postgresql', array( array( 'indexname' => 'ezuser_login', 'indexdef' => 'CREATE INDEX ezuser_login ON public.ezuser USING btree (login)' ),
                                                        array( 'indexname' => 'ezuser_pkey', 'indexdef' => 'CREATE UNIQUE INDEX ezuser_pkey ON public.ezuser USING btree (contentobject_id)' ) ),
                                   array( 'BEGIN', 'DROP INDEX "ezuser_login"', 'CREATE UNIQUE INDEX "ezuser_login" ON ezuser ( login )', 'COMMIT' ) ),
            'oracle' => array( 'oracle', array( array( 'INDEX_NAME' => 'EZUSER_LOGIN', 'UNIQUENESS' => 'NONUNIQUE' ) ),
                               array( 'DROP INDEX ezuser_login', 'CREATE UNIQUE INDEX ezuser_login ON ezuser ( login )' ) ),
        );
    }

    /**
     * @dataProvider engines
     */
    #[\PHPUnit\Framework\Attributes\DataProvider( 'engines' )]
    public function testEngineIndexOnlyAfterACleanReport( $engine, array $indexRows, array $expected )
    {
        $db = new expUserLoginIntegrityTestEngineDB( $engine );
        $db->indexRows = $indexRows;
        $db->users = array( array( 'contentobject_id' => 10, 'login' => 'anna' ), array( 'contentobject_id' => 11, 'login' => 'ANNA' ),
                            array( 'contentobject_id' => 12, 'login' => 'carl' ) );

        $groups = expUserLoginIntegrity::duplicates( $db );
        $this->assertCount( 1, $groups );
        $this->assertSame( array( 10, 11 ), $groups[0]['user_ids'] );
        $status = expUserLoginIntegrity::indexStatus( $db );
        $this->assertTrue( $status['exists'] );
        $this->assertFalse( $status['unique'] );

        $this->assertFalse( expUserLoginIntegrity::addUniqueIndex( $db, $message ) );
        $this->assertStringStartsWith( 'refused:', $message );
        $this->assertSame( array(), $db->statements, 'nothing is run while there are duplicates' );

        $db->users[1]['login'] = 'anna2';
        $this->assertTrue( expUserLoginIntegrity::addUniqueIndex( $db, $message ), (string)$message );
        $this->assertSame( $expected, $db->statements );
        $this->assertTrue( expUserLoginIntegrity::indexStatus( $db )['unique'] );
    }

    public function testDuplicateKeyErrorsOfEachEngine()
    {
        $cases = array(
            array( 'mysql', 1062, "Duplicate entry 'anna' for key 'ezuser_login'", true ),
            array( 'mysql', 1146, "Table 'x.ezuser' doesn't exist", false ),
            array( 'postgresql', 1, 'ERROR:  duplicate key value violates unique constraint "ezuser_login"', true ),
            array( 'sqlite', 19, 'UNIQUE constraint failed: ezuser.login', true ),
            array( 'sqlite', 1, 'no such table: ezuser', false ),
            array( 'oracle', 1, 'ORA-00001: unique constraint (X.EZUSER_LOGIN) violated', true ),
            array( 'mongo', 11000, 'E11000 duplicate key error collection: x.ezuser index: ezuser_login', true ),
        );
        foreach ( $cases as $case )
        {
            $this->assertSame( $case[3], expUserLoginIntegrity::isDuplicateKeyError( new expUserLoginIntegrityTestEngineDB( $case[0] ), $case[1], $case[2] ),
                               $case[0] . ': ' . $case[2] );
        }
    }

    public function testShippedSchemasHaveTheUniqueIndex()
    {
        $root = dirname( __DIR__, 4 );
        $this->assertMatchesRegularExpression( '/^\s*UNIQUE KEY ezuser_login \(login\)/m', file_get_contents( $root . '/kernel/sql/mysql/kernel_schema.sql' ) );
        $this->assertStringContainsString( 'CREATE UNIQUE INDEX ezuser_login ON ezuser USING btree (login);', file_get_contents( $root . '/kernel/sql/postgresql/kernel_schema.sql' ) );
        $this->assertStringContainsString( 'CREATE UNIQUE INDEX "idx_ezuser_ezuser_login" ON "ezuser" (`login`);', file_get_contents( $root . '/kernel/sql/sqlite/schema.sql' ) );
        // the .dba file sets $schema
        $schema = null;
        include $root . '/share/db_schema.dba';
        $this->assertSame( 'unique', $schema['ezuser']['indexes']['ezuser_login']['type'] );
        $this->assertSame( array( 'login' ), $schema['ezuser']['indexes']['ezuser_login']['fields'] );
    }
}
