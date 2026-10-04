<?php
/**
 * expPreloadRunner::startUrls() builds the starting pages once.
 *
 *  PL-01 a base url that already ends in the siteaccess prefix does not get it again (/site, not /site/site)
 *  PL-02 a base url with only a host gets the prefix
 *  PL-03 a siteaccess matched by host (no prefix) keeps the base as is
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group processtools
 */
use PHPUnit\Framework\TestCase;

class ExpPreloadStartUrlsTest extends TestCase
{
    private static function urls( $base, $prefix )
    {
        $runner = new expPreloadRunner( function () {}, array( 'base_path' => $prefix, 'start_paths' => array( '/' ) ) );
        return $runner->startUrls( $base );
    }

    public function testPrefixNotRepeated()
    {
        $this->assertSame( array( 'https://demo.example/site' ), self::urls( 'https://demo.example/site', '/site' ) );
    }

    public function testPrefixAdded()
    {
        $this->assertSame( array( 'https://demo.example/site' ), self::urls( 'https://demo.example', '/site' ) );
    }

    public function testHostMatched()
    {
        $this->assertSame( array( 'https://demo.example/' ), self::urls( 'https://demo.example', '' ) );
    }
}
