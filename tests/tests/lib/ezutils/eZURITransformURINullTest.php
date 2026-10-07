<?php
/**
 * eZURI::transformURI() with no link (null), as the language switcher asks for the server URL of a URI
 * siteaccess and as ezroot does for an empty value: it gives the root and no "ltrim(): Passing null"
 * deprecation from the cluster check that runs first when the index directory is ignored.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZURITransformURINullTest extends PHPUnit\Framework\TestCase
{
    private $deprecations = array();

    public static function setUpBeforeClass(): void
    {
        // The cluster handler is started outside the tests: starting it installs a handler of its own
        eZClusterFileHandler::instance();
    }

    protected function setUp(): void
    {
        $this->deprecations = array();
        set_error_handler( function ( $errno, $errstr ) {
            $this->deprecations[] = $errstr;
            return true;
        }, E_DEPRECATED );
    }

    protected function tearDown(): void
    {
        restore_error_handler();
    }

    public function testNullLinkWithIgnoredIndexDirGivesTheRootWithoutDeprecation()
    {
        $href = null;
        $this->assertTrue( eZURI::transformURI( $href, true, 'relative' ) );
        $this->assertSame( array(), $this->deprecations );
        $this->assertIsString( $href );
        $this->assertSame( '/', substr( $href, -1 ) );
    }

    public function testEmptyLinkWithIgnoredIndexDirGivesTheSameAsNull()
    {
        $null = null;
        $empty = '';
        eZURI::transformURI( $null, true, 'relative' );
        eZURI::transformURI( $empty, true, 'relative' );
        $this->assertSame( $empty, $null );
    }
}
