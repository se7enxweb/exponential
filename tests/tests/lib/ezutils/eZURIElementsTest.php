<?php
/**
 * eZURI (lib/ezutils), the request path of every page view, without a web server:
 *   - setURIString(): the leading slash, percent decoding, user parameters "(name)/value" (several, an empty one
 *     at the end, a value that runs over several elements), the URI without them, the original URI
 *   - walking the elements: element() relative and absolute, elements(), increase()/toBeginning()/toEnd(),
 *     base(), dropBase() keeping the user parameters, index(), isEmpty()
 *   - matchBase() with longer, equal, shorter and different URIs
 *   - encodeIRI()/decodeIRI() and encodeURL()/decodeURL(): slashes and "~" kept, user parameter brackets kept,
 *     scheme, user, password, host, port, query and fragment rebuilt
 *   - the attribute interface
 *
 * No database, no web server. User parameters need template.ini AllowUserVariables=true; the test skips when a
 * site has them off.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZURIElementsTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    private function requireUserVariables()
    {
        if ( eZINI::instance( 'template.ini' )->variable( 'ControlSettings', 'AllowUserVariables' ) == 'false' )
            $this->markTestSkipped( 'user parameters are off in template.ini' );
    }

    public function testPlainUri()
    {
        $uri = new eZURI( '/content/view/full/2' );
        $this->assertSame( 'content/view/full/2', $uri->uriString() );
        $this->assertSame( '/content/view/full/2', $uri->uriString( true ) );
        $this->assertSame( '/content/view/full/2', $uri->originalURIString( true ) );
        $this->assertFalse( $uri->isEmpty() );
        $this->assertSame( array(), $uri->userParameters() );
        $this->assertTrue( ( new eZURI( '' ) )->isEmpty() );
        $this->assertTrue( ( new eZURI( '/' ) )->isEmpty() );
    }

    public function testPercentDecoding()
    {
        $uri = new eZURI( '/Gr%C3%BC%C3%9Fe/a%20b' );
        $this->assertSame( 'Grüße/a b', $uri->uriString() );
    }

    public function testUserParameters()
    {
        $this->requireUserVariables();
        $uri = new eZURI( '/content/view/full/2/(offset)/20/(year)/2025' );
        $this->assertSame( 'content/view/full/2', $uri->uriString() );
        $this->assertSame( 'content/view/full/2/(offset)/20/(year)/2025', $uri->originalURIString() );
        $this->assertSame( array( 'offset' => '20', 'year' => '2025' ), $uri->userParameters() );
    }

    public function testUserParameterEdgeCases()
    {
        $this->requireUserVariables();
        $this->assertSame( array( 'flag' => '' ), ( new eZURI( '/a/(flag)' ) )->userParameters(), 'a name at the end' );
        $this->assertSame( array( 'path' => 'x/y/z' ), ( new eZURI( '/a/(path)/x/y/z' ) )->userParameters(), 'a value over several elements' );
        $this->assertSame( array( 'a' => '1', 'b' => '' ), ( new eZURI( '/p/(a)/1/(b)' ) )->userParameters() );
    }

    public function testWalkingTheElements()
    {
        $uri = new eZURI( '/a/b/c/d' );
        $this->assertSame( 'a', $uri->element() );
        $this->assertSame( 'c', $uri->element( 2 ) );
        $this->assertNull( $uri->element( 9 ) );
        $uri->increase();
        $this->assertSame( 1, $uri->index() );
        $this->assertSame( 'b', $uri->element() );
        $this->assertSame( 'a', $uri->element( 0, false ), 'absolute position' );
        $this->assertSame( 'b/c/d', $uri->elements() );
        $this->assertSame( array( 'b', 'c', 'd' ), $uri->elements( false ) );
        $this->assertSame( '/a', $uri->base() );
        $this->assertSame( array( 'a' ), $uri->base( false ) );
        $uri->increase( -5 );
        $this->assertSame( 0, $uri->index(), 'never before the start' );
        $uri->toEnd();
        $this->assertSame( 4, $uri->index() );
        $this->assertSame( '', $uri->elements() );
        $uri->toBeginning();
        $this->assertSame( 0, $uri->index() );
    }

    public function testDropBaseKeepsTheUserParameters()
    {
        $this->requireUserVariables();
        $uri = new eZURI( '/siteaccess/content/view/(offset)/10' );
        $uri->increase();
        $uri->dropBase();
        $this->assertSame( 'content/view', $uri->uriString() );
        $this->assertSame( 'content/view/(offset)/10', $uri->originalURIString() );
        $this->assertSame( 0, $uri->index() );
    }

    /**
     * A URI shorter than this one does not match; the elements past its end were read with undefined offsets.
     */
    public function testMatchBase()
    {
        $base = new eZURI( '/content/view' );
        $this->assertTrue( $base->matchBase( new eZURI( '/content/view/full/2' ) ) );
        $this->assertTrue( $base->matchBase( new eZURI( '/content/view' ) ) );
        $this->assertFalse( $base->matchBase( new eZURI( '/content/edit/2' ) ) );
        $this->assertFalse( $base->matchBase( new eZURI( '/content' ) ) );
    }

    public static function iriProvider()
    {
        return array(
            'plain'             => array( 'content/view', 'content/view' ),
            'space and umlaut'  => array( 'a b/grüße', 'a+b/gr%C3%BC%C3%9Fe' ),
            'tilde kept'        => array( '~user/page', '~user/page' ),
            'user parameter'    => array( 'list/(offset)/10', 'list/(offset)/10' ),
            'brackets in text'  => array( 'a(b)c', 'a%28b%29c' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('iriProvider')]
    public function testEncodeIri( $text, $encoded )
    {
        $this->assertSame( $encoded, eZURI::encodeIRI( $text ) );
        $this->assertSame( $text, eZURI::decodeIRI( $encoded ) );
    }

    public function testEncodeAndDecodeUrl()
    {
        $url = 'https://user:pw@example.org:8443/a b/grüße?x=1&y=2#part';
        $encoded = eZURI::encodeURL( $url );
        $this->assertSame( 'https://user:pw@example.org:8443/a+b/gr%C3%BC%C3%9Fe?x=1&y=2#part', $encoded );
        $this->assertSame( 'https://user:pw@example.org:8443/a b/grüße?x=1&y=2#part', eZURI::decodeURL( $encoded ) );
        $this->assertSame( '//example.org/x', eZURI::encodeURL( '//example.org/x' ), 'no scheme' );
        $this->assertSame( '/only/a/path', eZURI::encodeURL( '/only/a/path' ) );
        $this->assertSame( 'https://me@example.org/', eZURI::encodeURL( 'https://me@example.org/' ) );
    }

    public function testAttributes()
    {
        $uri = new eZURI( '/a/b' );
        $uri->increase();
        $this->assertTrue( $uri->hasAttribute( 'tail' ) );
        $this->assertFalse( $uri->hasAttribute( 'nothing' ) );
        $this->assertSame( 'b', $uri->attribute( 'element' ) );
        $this->assertSame( 'b', $uri->attribute( 'tail' ) );
        $this->assertSame( '/a', $uri->attribute( 'base' ) );
        $this->assertSame( 1, $uri->attribute( 'index' ) );
        $this->assertSame( 'a/b', $uri->attribute( 'uri' ) );
        $this->assertSame( 'a/b', $uri->attribute( 'original_uri' ) );
        $this->assertNull( @$uri->attribute( 'nothing' ) );
    }
}
