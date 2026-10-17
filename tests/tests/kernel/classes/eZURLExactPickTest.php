<?php
/**
 * eZURL::pickExact(), without a database: registerURL() and fetchExactByUrl() take only the byte-identical
 * row out of what the collation of ezurl.url (utf8mb4_general_ci) holds equal, never another spelling.
 *
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
/** Database stand-in: every lookup answers with two spellings the collation of ezurl.url holds equal; writes are counted */
class expURLCollationStandInDB extends eZNullDB
{
    public static $writes = 0;

    function __construct( $parameters )
    {
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        if ( stripos( ltrim( $sql ), 'SELECT' ) !== 0 )
            return array();
        return array(
            array( 'id' => 7, 'url' => 'https://example.com/wiki/Present_Perfect', 'original_url_md5' => '', 'is_valid' => 1,
                   'last_checked' => 0, 'created' => 0, 'modified' => 0 ),
            array( 'id' => 9, 'url' => 'https://example.com/wiki/Present_perfect', 'original_url_md5' => '', 'is_valid' => 1,
                   'last_checked' => 0, 'created' => 0, 'modified' => 0 ),
        );
    }

    function query( $sql, $server = false )
    {
        self::$writes++;
        return true;
    }
}

class eZURLExactPickTest extends PHPUnit\Framework\TestCase
{
    public function testTheByteIdenticalRowWinsOverTheFirstCollationMatch()
    {
        $rows = array(
            new eZURL( array( 'id' => 7, 'url' => 'https://example.com/wiki/Present_Perfect' ) ),
            new eZURL( array( 'id' => 9, 'url' => 'https://example.com/wiki/Present_perfect' ) ),
        );

        $picked = eZURL::pickExact( $rows, 'https://example.com/wiki/Present_perfect' );

        self::assertSame( $rows[1], $picked );
    }

    public function testAnotherSpellingIsNoMatch()
    {
        // what utf8mb4_general_ci holds equal: case, accents, and ß = s
        $rows = array( array( 'id' => 3, 'url' => 'https://example.com/Strase' ), array( 'id' => 4, 'url' => 'https://example.com/CAFE' ) );

        self::assertNull( eZURL::pickExact( $rows, 'https://example.com/Straße' ) );
        self::assertNull( eZURL::pickExact( $rows, 'https://example.com/café' ) );
        self::assertSame( $rows[1], eZURL::pickExact( $rows, 'https://example.com/CAFE' ) );
    }

    /**
     * registerURL() and urlByURL() against a stand-in database that answers like MySQL's utf8mb4_general_ci: both
     * spellings for a lookup of either. The old urlByURL() saw two rows and returned false (its caller then stored a
     * new row on every save); the old registerURL() rewrote the first row to the other spelling.
     */
    public function testRegisterAndLookupTakeTheExactSpellingFromACollationEqualResult()
    {
        $previousDB = $GLOBALS['eZDBGlobalInstance'] ?? null;
        expURLCollationStandInDB::$writes = 0;
        eZDB::setInstance( new expURLCollationStandInDB( array() ) );
        try
        {
            $found = eZURL::urlByURL( 'https://example.com/wiki/Present_perfect' );
            self::assertInstanceOf( eZURL::class, $found );
            self::assertSame( 9, (int)$found->attribute( 'id' ) );
            self::assertSame( 7, (int)eZURL::urlByURL( 'https://example.com/wiki/Present_Perfect' )->attribute( 'id' ) );
            self::assertFalse( eZURL::urlByURL( 'https://example.com/wiki/present_PERFECT' ) );

            self::assertSame( 9, (int)eZURL::registerURL( 'https://example.com/wiki/Present_perfect' ) );
            self::assertSame( 0, expURLCollationStandInDB::$writes, 'a row of another spelling is not rewritten' );
        }
        finally
        {
            eZDB::setInstance( $previousDB );
        }
    }

    public function testTheFirstOfTwoIdenticalRowsIsTaken()
    {
        $rows = array( array( 'id' => 1, 'url' => 'https://example.com/' ), array( 'id' => 2, 'url' => 'https://example.com/' ) );

        self::assertSame( 1, eZURL::pickExact( $rows, 'https://example.com/' )['id'] );
        self::assertNull( eZURL::pickExact( array(), 'https://example.com/' ) );
    }
}
