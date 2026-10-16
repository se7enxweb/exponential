<?php
/**
 * eZURL::pickExact(), without a database: registerURL() and fetchExactByUrl() take only the byte-identical
 * row out of what the collation of ezurl.url (utf8mb4_general_ci) holds equal, never another spelling.
 *
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
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

    public function testTheFirstOfTwoIdenticalRowsIsTaken()
    {
        $rows = array( array( 'id' => 1, 'url' => 'https://example.com/' ), array( 'id' => 2, 'url' => 'https://example.com/' ) );

        self::assertSame( 1, eZURL::pickExact( $rows, 'https://example.com/' )['id'] );
        self::assertNull( eZURL::pickExact( array(), 'https://example.com/' ) );
    }
}
