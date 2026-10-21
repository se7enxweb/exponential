<?php
/**
 * expViewParameters: the options of an administration view as view parameters, /(name)/value, never GET query
 * parameters. DB-less.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expViewParametersTest extends PHPUnit\Framework\TestCase
{
    public function testGetReadsTheDeclaredVariableThenTheUserParameter()
    {
        $params = array( 'Query' => 'editor', 'UserParameters' => array( 'q' => 'other', 'kind' => 'redirect' ) );
        $this->assertSame( 'editor', expViewParameters::get( $params, 'q', 'Query' ) );
        $this->assertSame( 'redirect', expViewParameters::get( $params, 'kind' ) );
        $this->assertSame( 'other', expViewParameters::get( array( 'Query' => false, 'UserParameters' => array( 'q' => 'other' ) ), 'q', 'Query' ) );
        $this->assertFalse( expViewParameters::get( array(), 'q', 'Query' ) );
        $this->assertFalse( expViewParameters::get( array( 'UserParameters' => array( 'q' => array( 'x' ) ) ), 'q' ) );
    }

    public function testGetNeverReadsTheQueryString()
    {
        $saved = $_GET;
        $_GET = array( 'q' => 'from-get' );
        try
        {
            $this->assertFalse( expViewParameters::get( array(), 'q', 'Query' ) );
            $this->assertSame( '', expViewParameters::text( array(), 'q', 'Query' ) );
        }
        finally
        {
            $_GET = $saved;
        }
    }

    public function testCleanAndSuffix()
    {
        $this->assertSame( 'a b c', expViewParameters::clean( " a/b(c)\n " ) );
        $this->assertSame( 200, mb_strlen( expViewParameters::clean( str_repeat( 'x', 300 ) ) ) );
        $this->assertSame( '', expViewParameters::clean( array( 'x' ) ) );
        $this->assertSame( '/(q)/a%20b/(order)/1/(all)/1',
                           expViewParameters::suffix( array( 'q' => 'a/b', 'sort' => '', 'user' => false, 'order' => 1, 'all' => true, 'x' => null ) ) );
        $this->assertSame( '/(q)/%3Cx%3E%2B', expViewParameters::suffix( array( 'q' => '<x>+' ) ) );
        $this->assertStringNotContainsString( '?', expViewParameters::suffix( array( 'q' => 'a?b=c' ) ) );
    }

    public function testRedirectsOnlyAPostOfTheForm()
    {
        $post = array( 'REQUEST_METHOD' => 'POST' );
        $get = array( 'REQUEST_METHOD' => 'GET' );
        $this->assertTrue( expViewParameters::redirectsPost( array( 'q' => '' ), array( 'q' ), $post ), 'an empty field counts' );
        $this->assertFalse( expViewParameters::redirectsPost( array( 'Other' => '1' ), array( 'q' ), $post ) );
        $this->assertFalse( expViewParameters::redirectsPost( array( 'q' => 'x' ), array( 'q' ), $get ), 'a GET is never redirected' );
        $this->assertSame( 'x', expViewParameters::posted( array( 'q' => 'x' ), 'q' ) );
        $this->assertSame( '', expViewParameters::posted( array( 'q' => array( 'x' ) ), 'q' ) );
    }
}
