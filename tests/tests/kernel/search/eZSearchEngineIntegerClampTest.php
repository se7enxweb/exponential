<?php
/**
 * Integers of the built-in search index kept within the range of the integer_value column
 * (eZDBInterface::MAX_INT / MIN_INT per driver, eZDBInterface::clampInteger(), eZSearchEngine).
 *
 *  IC-01 - The range per driver: MySQL and PostgreSQL signed 32-bit (the kernel schema's INT / integer), SQLite
 *          and MongoDB signed 64-bit; eZDBInterface keeps the 32-bit values for any other driver
 *  IC-02 - clampInteger(): numbers within the range unchanged (a fraction cut off as (int) does), numbers beyond
 *          it at the limit, in digits or in float notation ("1e30" never wraps around), anything else 0
 *  IC-03 - eZSearchEngine::indexWords() writes words whose number is beyond the column range without a database
 *          error, with the number at the limit of the database in use (live database, inside a transaction that
 *          is rolled back, for an object id no object has)
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 3774a54ead), Copyright (C) Mugo Web, GPL v2.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/search/eZSearchEngineIntegerClampTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZSearchEngineIntegerClampTest extends PHPUnit\Framework\TestCase
{
    /** @var bool */
    private $inTransaction = false;

    /** @var string|null why the live installation cannot be used, null when it can */
    private static $unavailable;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // The kernel is started here, outside a test, so its error handlers are not counted against IC-03
        self::$unavailable = ezpLiveInstallation::unavailableReason();
    }

    protected function setUp(): void
    {
        parent::setUp();
        chdir( dirname( __DIR__, 4 ) );
    }

    protected function tearDown(): void
    {
        if ( $this->inTransaction )
        {
            eZDB::instance()->rollback();
            $this->inTransaction = false;
        }
        parent::tearDown();
    }

    /** @return eZDBInterface a driver object without a connection, for clampInteger() */
    private static function driver( $class )
    {
        return ( new ReflectionClass( $class ) )->newInstanceWithoutConstructor();
    }

    /** IC-01 */
    public function testRangePerDriver()
    {
        $this->assertSame( 2147483647, eZDBInterface::MAX_INT );
        $this->assertSame( -2147483648, eZDBInterface::MIN_INT );
        foreach ( array( 'eZMySQLiDB', 'eZPostgreSQLDB' ) as $class )
        {
            $this->assertSame( 2147483647, $class::MAX_INT, $class );
            $this->assertSame( -2147483648, $class::MIN_INT, $class );
        }
        foreach ( array( 'eZSQLite3DB', 'expMongoDB' ) as $class )
        {
            $this->assertSame( PHP_INT_MAX, $class::MAX_INT, $class );
            $this->assertSame( PHP_INT_MIN, $class::MIN_INT, $class );
        }
        $this->assertSame( 2147483647, eZNullDB::MAX_INT );
    }

    public static function clampCases()
    {
        $max32 = 2147483647;
        $min32 = -2147483648;
        return array(
            // value, 32-bit result, 64-bit result
            'int'                => array( 42, 42, 42 ),
            'negative int'       => array( -7, -7, -7 ),
            'digits'             => array( '1234', 1234, 1234 ),
            'spaces'             => array( ' 99 ', 99, 99 ),
            'fraction'           => array( '12.7', 12, 12 ),
            'negative fraction'  => array( '-12.7', -12, -12 ),
            'float notation'     => array( '1e3', 1000, 1000 ),
            'at the 32-bit max'  => array( '2147483647', $max32, 2147483647 ),
            'past the 32-bit max'=> array( '2147483648', $max32, 2147483648 ),
            'eleven digits'      => array( '99999999999', $max32, 99999999999 ),
            'past the 32-bit min'=> array( '-99999999999', $min32, -99999999999 ),
            'past every int'     => array( '9223372036854775807999', $max32, PHP_INT_MAX ),
            'below every int'    => array( '-9223372036854775807999', $min32, PHP_INT_MIN ),
            'huge float'         => array( '1e30', $max32, PHP_INT_MAX ),
            'huge negative float'=> array( '-1e30', $min32, PHP_INT_MIN ),
            'float value'        => array( 1.0E+20, $max32, PHP_INT_MAX ),
            'PHP_INT_MAX'        => array( PHP_INT_MAX, $max32, PHP_INT_MAX ),
            'PHP_INT_MIN'        => array( PHP_INT_MIN, $min32, PHP_INT_MIN ),
            'text'               => array( 'abc', 0, 0 ),
            'empty'              => array( '', 0, 0 ),
            'null'               => array( null, 0, 0 ),
            'true'               => array( true, 1, 1 ),
            'nan'                => array( NAN, 0, 0 ),
        );
    }

    /**
     * IC-02
     *
     * @dataProvider clampCases
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'clampCases' )]
    public function testClampInteger( $value, $expected32, $expected64 )
    {
        foreach ( array( 'eZMySQLiDB', 'eZPostgreSQLDB', 'eZNullDB' ) as $class )
            $this->assertSame( $expected32, self::driver( $class )->clampInteger( $value ), $class );
        foreach ( array( 'eZSQLite3DB', 'expMongoDB' ) as $class )
            $this->assertSame( $expected64, self::driver( $class )->clampInteger( $value ), $class );
    }

    /** IC-03 */
    public function testIndexWordsBeyondTheColumnRange()
    {
        if ( self::$unavailable !== null )
            $this->markTestSkipped( self::$unavailable );
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT MAX(id) AS m FROM ezcontentobject' );
        $objectID = (int)$rows[0]['m'] + 1000000;
        $object = new eZContentObject( array( 'id' => $objectID, 'contentclass_id' => 1, 'section_id' => 1, 'published' => 0 ) );

        $numbers = array( '99999999999999999999', '-99999999999999999999', '1e30', '42' );
        $indexArray = array();
        $wordIDs = array();
        foreach ( $numbers as $i => $number )
        {
            $indexArray[] = array( 'Word' => "w$i", 'ContentClassAttributeID' => 0, 'identifier' => 'clamp_test',
                                   'integer_value' => $number );
            $wordIDs["w$i"] = 900000000 + $i;
        }

        $db->begin();
        $this->inTransaction = true;
        $engine = new eZSearchEngine();
        $this->assertSame( count( $numbers ), $engine->indexWords( $object, $indexArray, $wordIDs ) );
        $stored = $db->arrayQuery( "SELECT placement, integer_value FROM ezsearch_object_word_link WHERE contentobject_id = $objectID ORDER BY placement" );
        $db->rollback();
        $this->inTransaction = false;

        $max = $db::MAX_INT;
        $min = $db::MIN_INT;
        $this->assertCount( 4, $stored );
        $this->assertSame( array( (string)$max, (string)$min, (string)$max, '42' ),
                           array_map( function ( $r ) { return (string)$r['integer_value']; }, $stored ) );
        $left = $db->arrayQuery( "SELECT COUNT(*) AS c FROM ezsearch_object_word_link WHERE contentobject_id = $objectID" );
        $this->assertSame( 0, (int)$left[0]['c'] );
    }
}
