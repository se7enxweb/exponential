<?php
/**
 * Tests of eZPackageRequestGuard: the package and repository names a request may name (no "..", no slash, no
 * absolute path, no NUL), the repositories the storage has, the view modes with a template, a posted selection,
 * and the Content-Disposition of a download (no path, no quote, no header splitting). No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZPackageRequestGuardTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
    }

    public static function names()
    {
        return array(
            array( 'sevenx_classes', true ), array( 'ezwt_extension', true ), array( 'explayouts_ui_api_1790989825', true ),
            array( 'ez-systems', true ), array( '7x', true ), array( 'local', true ), array( 'a.b', true ),
            array( '', false ), array( '..', false ), array( '.', false ), array( '.cache', false ), array( '../7x', false ),
            array( '..%2F7x', false ), array( 'a/b', false ), array( '/etc', false ), array( 'a\\b', false ), array( "a\0b", false ),
            array( 'a..b', false ), array( "name\n", false ), array( ' name', false ), array( str_repeat( 'a', 201 ), false ),
            array( null, false ), array( array( 'x' ), false ), array( 12, false ),
        );
    }

    /**
     * @dataProvider names
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'names' )]
    public function testIsSafeName( $name, $expected )
    {
        $this->assertSame( $expected, eZPackageRequestGuard::isSafeName( $name ), var_export( $name, true ) );
    }

    public function testRepositoryIsOneTheStorageHas()
    {
        $repositories = array( array( 'id' => 'local', 'path' => 'var/x/local' ), array( 'id' => '7x', 'path' => 'var/x/7x' ) );
        $this->assertSame( '7x', eZPackageRequestGuard::repository( '7x', $repositories )['id'] );
        $this->assertFalse( eZPackageRequestGuard::repository( 'other', $repositories ) );
        $this->assertFalse( eZPackageRequestGuard::repository( '../7x', $repositories ) );
        $this->assertFalse( eZPackageRequestGuard::repository( '', $repositories ) );
    }

    public function testViewModes()
    {
        $this->assertSame( 'full', eZPackageRequestGuard::viewMode( 'full' ) );
        $this->assertSame( 'files', eZPackageRequestGuard::viewMode( 'files' ) );
        foreach ( array( 'nosuchmode', '../full', 'full/../../x', '', null, 'FULL' ) as $mode )
            $this->assertFalse( eZPackageRequestGuard::viewMode( $mode ), var_export( $mode, true ) );
    }

    public function testSelectionKeepsSafeNamesOnce()
    {
        list( $safe, $refused ) = eZPackageRequestGuard::selection( array( 'a', '../b', 'a', 'c', array( 'x' ) ) );
        $this->assertSame( array( 'a', 'c' ), $safe );
        $this->assertSame( 2, $refused );
    }

    public function testContentDisposition()
    {
        $this->assertSame( 'attachment; filename="k1-1.0-2.ezpkg"', eZPackageRequestGuard::contentDisposition( 'k1-1.0-2.ezpkg' ) );
        $this->assertSame( 'attachment; filename="passwd"', eZPackageRequestGuard::contentDisposition( '../../etc/passwd' ) );
        $this->assertSame( 'attachment; filename="x.php"', eZPackageRequestGuard::contentDisposition( '..\\..\\x.php' ) );
        $value = eZPackageRequestGuard::contentDisposition( "a\"b\r\nSet-Cookie: x;.txt" );
        $this->assertStringNotContainsString( "\r", $value );
        $this->assertStringNotContainsString( "\n", $value );
        $this->assertSame( 1, substr_count( $value, ';' ) );
        $this->assertSame( 'attachment; filename="download.bin"', eZPackageRequestGuard::contentDisposition( '..' ) );
        $this->assertSame( "inline; filename=\"Gr__e.png\"; filename*=UTF-8''Gr%C3%BC%C3%9Fe.png", eZPackageRequestGuard::contentDisposition( 'Grüße.png', 'x', 'inline' ) );
    }
}
