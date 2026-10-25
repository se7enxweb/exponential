<?php
/**
 * the guards of the test database toolkit, without a database. A test may write to, empty or drop only a
 * database whose name has "test" as a word; the throwaway databases are named exp_test_<pid>_<random>; the old helpers
 * that empty a database (ezpTestDatabaseHelper::create(), clean()) refuse any other.
 *
 *  TD-01 - names with "test" as a word are test databases; the databases of sites, words that only contain "test"
 *          (latest_shop, contest) and anything but a plain identifier of at most 64 characters are not
 *  TD-02 - a new name is exp_test_<pid>_<8 hex digits>, a test database name, and differs from the next
 *  TD-03 - ezpTestDatabaseHelper::create() refuses a DSN whose database is not a test database
 *  TD-04 - ezpTestDatabaseHelper::clean() refuses a connection to a database that is not a test database
 *  TD-05 - ezpDsn: the keys the cluster tests read are there also when the DSN does not name them
 *  TD-06 - the old helpers take "test", "tests" or "testdb" as a word of the name, not a substring (expdemolatest);
 *          for Oracle the account, whose schema create() empties
 *
 * Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class ezpThrowawayDatabaseGuardTest extends PHPUnit\Framework\TestCase
{
    public function testTestDatabaseNames()
    {
        foreach ( array( 'exp_test_1_0a1b2c3d', 'exponential_engsw_test', 'my_TEST_db', 'test', 'test_site' ) as $name )
            self::assertTrue( ezpThrowawayDatabase::isTestDatabaseName( $name ), $name );
        foreach ( array( 'exponential_1', 'exponential_lernapp', 'exponential_exponential', '', null, false, 'te_st',
                         'x_test`; DROP DATABASE exponential_1', 'exp-test', 'test_' . str_repeat( 'x', 60 ),
                         'latest_shop', 'contest', 'testdb', 'exponential_lernapp_dbtest', 'attestation' ) as $name )
            self::assertFalse( ezpThrowawayDatabase::isTestDatabaseName( $name ), var_export( $name, true ) );
    }

    public function testNewNames()
    {
        $name = ezpThrowawayDatabase::newName();
        self::assertMatchesRegularExpression( '/^exp_test_' . getmypid() . '_[0-9a-f]{8}$/', $name );
        self::assertTrue( ezpThrowawayDatabase::isTestDatabaseName( $name ) );
        self::assertNotSame( $name, ezpThrowawayDatabase::newName() );
    }

    public function testCreateRefusesASiteDatabase()
    {
        $this->expectException( RuntimeException::class );
        $this->expectExceptionMessage( "'exponential_1'" );
        ezpTestDatabaseHelper::create( new ezpDsn( 'mysqli://user:secret@localhost/exponential_1' ) );
    }

    public function testCleanRefusesASiteDatabase()
    {
        $db = $this->getMockBuilder( eZMySQLiDB::class )->disableOriginalConstructor()->onlyMethods( array( 'relationList', 'removeRelation' ) )->getMock();
        $db->expects( self::never() )->method( 'relationList' );
        $db->expects( self::never() )->method( 'removeRelation' );
        $db->DB = 'exponential_lernapp';
        $this->expectException( RuntimeException::class );
        $this->expectExceptionMessage( "'exponential_lernapp'" );
        ezpTestDatabaseHelper::clean( $db );
    }

    /**
     * TD-06 - the old helpers accept the databases CI's database jobs use (testdb) and the throwaway names, but not
     *         a name that only contains "test" in another word: a demo site's "expdemolatest", a reinstall target
     *         "xa_alpha_ref_pkgtest" or "contest" used to be emptied by create() and clean().
     */
    public function testSetUpDatabaseNames()
    {
        foreach ( array( 'testdb', 'test', 'tests', 'test2', 'testdb_2', 'exp_test_1_0a1b2c3d', 'site_test', 'my_TESTDB' ) as $name )
            self::assertTrue( ezpTestDatabaseHelper::isSetUpDatabaseName( $name ), $name );
        foreach ( array( 'expdemolatest', 'xa_alpha_ref_pkgtest', 'contest', 'attestation', 'latest_shop', 'testimonials', 'exponential_1',
                         '', null, 'test`; DROP DATABASE x', 'test-db' ) as $name )
            self::assertFalse( ezpTestDatabaseHelper::isSetUpDatabaseName( $name ), var_export( $name, true ) );
    }

    public function testCreateRefusesADatabaseThatOnlyContainsTest()
    {
        $this->expectException( RuntimeException::class );
        $this->expectExceptionMessage( "'expdemolatest'" );
        ezpTestDatabaseHelper::create( new ezpDsn( 'mysqli://user:secret@localhost/expdemolatest' ) );
    }

    public function testCreateRefusesAnOracleAccountThatIsNoTestSchema()
    {
        // Oracle's create() empties the schema of the account, so the account is what is checked
        $this->expectException( RuntimeException::class );
        $this->expectExceptionMessage( "'exponential'" );
        ezpTestDatabaseHelper::create( new ezpDsn( 'oracle://exponential:secret@localhost/test' ) );
    }

    public function testDsnKeys()
    {
        $dsn = new ezpDsn( 'mysqli://user:p%40ss@localhost/exp_test_1_0a1b2c3d' );
        self::assertSame( 'mysqli', $dsn->phptype );
        self::assertSame( 'exp_test_1_0a1b2c3d', $dsn->database );
        self::assertSame( 'p@ss', $dsn->password );
        self::assertArrayHasKey( 'socket', $dsn->parts );
        self::assertArrayHasKey( 'port', $dsn->parts );
        self::assertSame( array(), ( new ezpDsn( '' ) )->parts );
    }
}
