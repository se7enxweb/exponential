<?php
/**
 * File containing the ezpDatabaseTestCase class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

/**
 * Database backed test case class.
 *
 * Inherit from this class if you want your test case to interact with a database.
 */
class ezpDatabaseTestCase extends ezpTestCase
{
    /**
     * Holds paths to custom sql files
     *
     * @var array( array( string => string ) )
     */
    protected $sqlFiles = array();

    /**
     * Controls if the database should be initialized with default data
     *
     * @var bool
     */
    protected $insertDefaultData = true;

    /**
     * Hold shared fixtures
     *
     * @var mixed
     */
    protected $sharedFixture;

    /**
     * Starts the kernel and checks for throwaway databases before the first test, so that the error and
     * exception handlers the kernel installs are not counted against a test. Without them the whole class is
     * skipped here, before any setUp()/tearDown() of a subclass runs on a fixture that was never made.
     * Not with the runner's --db-per-test, which makes its own database.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if ( !ezpTestRunner::dbPerTest() )
            ezpThrowawayDatabase::requireOrSkip();
    }

    /**
     * Sets up the database environment.
     *
     * With tests/runtests.php --db-per-test --dsn=..., the database of the DSN is made for the test. Otherwise
     * every test class runs on a throwaway database of its own (ezpThrowawayDatabase): a copy of the kernel
     * schema and the clean data (none with $insertDefaultData = false), plus $sqlFiles, made on the first test of the
     * class and dropped when the next class starts or the run ends. The test is skipped, with the reason, when no
     * such database can be made.
     */
    protected function setUp(): void
    {
        parent::setUp();
        if ( ezpTestRunner::dbPerTest() )
        {
            $dsn = ezpTestRunner::dsn();
            $this->sharedFixture = ezpTestDatabaseHelper::create( $dsn );

            if ( $this->insertDefaultData === true )
                ezpTestDatabaseHelper::insertDefaultData( $this->sharedFixture );

            if ( count( $this->sqlFiles ) > 0 )
                ezpTestDatabaseHelper::insertSqlData( $this->sharedFixture, $this->sqlFiles );

            eZDB::setInstance( $this->sharedFixture );
            return;
        }

        $this->sharedFixture = ezpThrowawayDatabase::forClass( static::class, $this->insertDefaultData === true, $this->sqlFiles );
        // the database a test writes to is one this run made, never the database of a site
        if ( !ezpThrowawayDatabase::isTestDatabaseName( eZDB::instance()->DB ) )
            self::fail( 'the database of the test is not a throwaway database: ' . eZDB::instance()->DB );
    }

    protected function tearDown(): void
    {
        if ( ezpTestRunner::dbPerTest() )
        {
            $db = eZDB::instance();
            $db->close();
        }
        parent::tearDown();
    }
}

?>
