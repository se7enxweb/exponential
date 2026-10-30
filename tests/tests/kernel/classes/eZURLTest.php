<?php
#[\PHPUnit\Framework\Attributes\Group('database')]
/**
 * File containing eZURLTest class
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 * @group database
 */

class eZURLTest extends ezpDatabaseTestCase
{
    protected $backupGlobals = false;

    /**
     * @group ezurl
     */
    public function testFetchByUrl()
    {
        $url = 'http://ez.no/ezpublish';
        $urlObj = eZURL::create( $url );
        $urlObj->store();

        $urlObj2 = eZURL::fetchByUrl( $url );
        self::assertInstanceOf( 'eZURL', $urlObj2 );
    }

    /**
     * Another spelling of an address gets its own row; the row of the first spelling keeps its url (before,
     * registerURL() found it through the case-insensitive collation and overwrote it).
     *
     * @group ezurl
     */
    public function testRegisterURLNeverOverwritesAnotherSpelling()
    {
        $first = eZURL::registerURL( 'https://example.com/wiki/Present_Perfect' );
        $second = eZURL::registerURL( 'https://example.com/wiki/Present_perfect' );

        self::assertNotEquals( $first, $second );
        self::assertSame( 'https://example.com/wiki/Present_Perfect', eZURL::url( $first ) );
        self::assertSame( 'https://example.com/wiki/Present_perfect', eZURL::url( $second ) );
        self::assertEquals( $first, eZURL::registerURL( 'https://example.com/wiki/Present_Perfect' ) );
    }

    /**
     * @group ezurl
     */
    public function testFetchExactByUrl()
    {
        $id = eZURL::registerURL( 'https://example.com/Stra%C3%9Fe' );

        self::assertEquals( $id, eZURL::fetchExactByUrl( 'https://example.com/Stra%C3%9Fe' )->attribute( 'id' ) );
        self::assertNull( eZURL::fetchExactByUrl( 'https://example.com/stra%c3%9fe' ) );
    }
}
?>
