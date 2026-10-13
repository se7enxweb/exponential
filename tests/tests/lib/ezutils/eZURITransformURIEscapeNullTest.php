<?php
/**
 * eZURI::transformURI() with HTML escaping of a link that is null or not a string, the case LoveStack
 * (mugoweb/ezpublish-legacy) commit 4065ed16b3 guarded with a (string) cast before htmlspecialchars(): the
 * fragment branch ("#top") and escapeHtmlTransformUri() must raise no PHP 8.1 null deprecation, and
 * eZURI::build() keeps giving the same link.
 *
 * The eZSys state earlier tests leave (access path, www directory, index file) is saved, cleared for each test
 * and restored, so the links are exact whatever runs before.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 * @group php8
 */

class eZURITransformURIEscapeNullTest extends PHPUnit\Framework\TestCase
{
    private $messages = array();
    private $accessPath;
    private $wwwDir;
    private $indexFile;

    public static function setUpBeforeClass(): void
    {
        // The cluster handler is started outside the tests: starting it installs a handler of its own
        eZClusterFileHandler::instance();
    }

    protected function setUp(): void
    {
        $sys = eZSys::instance();
        $this->accessPath = $sys->AccessPath;
        $this->wwwDir = $sys->WWWDir;
        $this->indexFile = $sys->IndexFile;
        $sys->WWWDir = '';
        $sys->IndexFile = '';
        eZSys::clearAccessPath( false );
        eZSys::clearAccessPath( true );

        $this->messages = array();
        set_error_handler( function ( $errno, $errstr ) {
            $this->messages[] = $errstr;
            return true;
        } );
    }

    protected function tearDown(): void
    {
        restore_error_handler();
        $sys = eZSys::instance();
        eZSys::setAccessPath( $this->accessPath['siteaccess']['url'], $this->accessPath['siteaccess']['name'], true );
        eZSys::setAccessPath( $this->accessPath['path']['url'], $this->accessPath['path']['name'], false );
        $sys->WWWDir = $this->wwwDir;
        $sys->IndexFile = $this->indexFile;
    }

    public function testNullLinkEscapedGivesTheRootWithoutDeprecation()
    {
        foreach ( array( false, true ) as $ignoreIndexDir )
        {
            $href = null;
            $this->assertTrue( eZURI::transformURI( $href, $ignoreIndexDir, 'relative', true ) );
            $this->assertSame( '/', $href );
        }
        $this->assertSame( array(), $this->messages );
    }

    public function testFragmentIsEscapedWithoutDeprecation()
    {
        $href = '#a"b';
        $this->assertTrue( eZURI::transformURI( $href, false, 'relative', true ) );
        $this->assertSame( '#a&quot;b', $href );
        $this->assertSame( array(), $this->messages );
    }

    public function testNumericLinkIsEscapedWithoutDeprecation()
    {
        $href = 42;
        $this->assertTrue( eZURI::transformURI( $href, true, 'relative', true ) );
        $this->assertSame( '/42', $href );
        $this->assertSame( array(), $this->messages );
    }

    public function testURIOfNullIsEmptyWithoutDeprecation()
    {
        // eZURI::instance() on the command line before eZSys knows a request URI, as the debug bar asks for it
        $uri = new eZURI( null );
        $this->assertSame( '', $uri->uriString() );
        $this->assertSame( array(), $this->messages );
    }

    public function testBuildKeepsItsLinks()
    {
        $this->assertSame( '#top', eZURI::build( '#top' ) );
        $this->assertSame( 'https://example.org/a?b=1&amp;c=2', eZURI::build( 'https://example.org/a?b=1&amp;c=2' ) );
        $this->assertSame( '/', eZURI::build( '' ) );
        $this->assertSame( array(), $this->messages );
    }
}
