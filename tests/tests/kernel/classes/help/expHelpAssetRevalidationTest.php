<?php
/**
 * exphelp/asset revalidates instead of keeping a copy for an hour: a regenerated image (the language coverage map)
 * keeps its address and its links carry no version query, so the view sends Cache-Control: no-cache with an ETag and
 * answers 304 while the file is unchanged. No database and no siteaccess.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group help
 */

use Exponential\View\Kernel\Exphelp\Asset;

class expHelpAssetRevalidationTest extends PHPUnit\Framework\TestCase
{
    private $file;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        if ( !class_exists( Asset::class ) )
            require_once "kernel/private/classes/views/exphelp/asset.php";
        $this->file = 'var/tmp/phpunit-help-asset-' . getmypid() . '.webp';
        file_put_contents( $this->file, 'first' );
    }

    protected function tearDown(): void
    {
        if ( is_file( $this->file ) )
            unlink( $this->file );
    }

    public function testTheTagChangesWithTheContent()
    {
        $first = Asset::etag( $this->file );
        $this->assertMatchesRegularExpression( '/^"[0-9a-f]+-[0-9a-f]+-[0-9a-f]{16}"$/', $first );
        file_put_contents( $this->file, 'other' );
        clearstatcache( true, $this->file );
        $this->assertNotSame( $first, Asset::etag( $this->file ), 'same size, same second, new content' );
    }

    public function testNotModified()
    {
        $etag = Asset::etag( $this->file );
        $this->assertTrue( Asset::notModified( $etag, $etag ) );
        $this->assertTrue( Asset::notModified( $etag, '"x", ' . $etag ) );
        $this->assertTrue( Asset::notModified( $etag, 'W/' . $etag ) );
        $this->assertTrue( Asset::notModified( $etag, '*' ) );
        $this->assertFalse( Asset::notModified( $etag, '' ) );
        $this->assertFalse( Asset::notModified( $etag, '"other"' ) );
    }

    public function testTheViewNoLongerKeepsACopyForAnHour()
    {
        $source = file_get_contents( 'kernel/private/classes/views/exphelp/asset.php' );
        $this->assertStringContainsString( "Cache-Control: private, no-cache", $source );
        $this->assertStringNotContainsString( 'max-age=3600', $source );
    }
}
