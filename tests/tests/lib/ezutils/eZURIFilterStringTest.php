<?php
/**
 * eZURI::convertFilterString() and the (namefilter) user parameter of the alphabetical navigator.
 *
 * convertFilterString() decoded the namefilter value into a local variable and dropped it: it changed nothing and
 * returned nothing, although it is documented to return the converted string. It now writes the value back and
 * returns it. setURIString() no longer calls it: it decodes the whole URI, the namefilter with it, before it splits
 * off the user parameters, and decoding the value a second time would turn an encoded "+" or "%" into something
 * else.
 *
 * No database: the URI objects are made from strings.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZURIFilterStringTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    public function testConvertFilterStringWritesBackAndReturnsTheValue()
    {
        $uri = new eZURI( 'content/view/full/2' );
        $uri->UserArray = array( 'offset' => '10', 'namefilter' => 'C%C3%A4%2B' );

        $this->assertSame( 'Cä+', $uri->convertFilterString() );
        $parameters = $uri->userParameters();
        $this->assertSame( 'Cä+', $parameters['namefilter'] );
        $this->assertSame( '10', $parameters['offset'], 'other parameters are left alone' );
    }

    public function testConvertFilterStringWithoutANameFilter()
    {
        $uri = new eZURI( 'content/view/full/2' );
        $uri->UserArray = array( 'offset' => '%41' );

        $this->assertNull( $uri->convertFilterString() );
        $this->assertSame( array( 'offset' => '%41' ), $uri->userParameters() );
    }

    public static function uriProvider()
    {
        return array(
            'a letter'          => array( 'content/view/full/2/(namefilter)/a', 'a' ),
            'an encoded letter' => array( 'content/view/full/2/(namefilter)/%C3%A4', 'ä' ),
            'an encoded plus'   => array( 'content/view/full/2/(namefilter)/C%2B%2B', 'C++' ),
            'an encoded %'      => array( 'content/view/full/2/(namefilter)/100%2541', '100%41' ),
            'before another'    => array( 'content/view/full/2/(namefilter)/%C3%B6/(offset)/20', 'ö' ),
        );
    }

    /**
     * A URI's namefilter is decoded once, with the URI.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('uriProvider')]
    public function testNameFilterOfAURIIsDecodedOnce( $uriString, $expected )
    {
        $uri = new eZURI( $uriString );
        $parameters = $uri->userParameters();
        $this->assertSame( $expected, $parameters['namefilter'] );
        $this->assertSame( 'content/view/full/2', $uri->uriString() );
    }
}
