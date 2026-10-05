<?php
/**
 * The setup wizard's PHP check asks for the PHP version composer.json requires: setup.ini [phpversion]
 * MinimumVersion was 5.3.3 while the code needs 8.0. No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class SetupPhpVersionTest extends PHPUnit\Framework\TestCase
{
    public function testMinimumVersionMatchesComposer()
    {
        $root = dirname( __DIR__, 5 );
        $this->assertSame( 1, preg_match( '/^\[phpversion\]\s*\nMinimumVersion=(\S+)/m', file_get_contents( $root . '/settings/setup.ini' ), $m ) );
        $minimum = $m[1];

        $composer = json_decode( file_get_contents( $root . '/composer.json' ), true );
        $this->assertSame( 1, preg_match( '/\^(\d+\.\d+)/', $composer['require']['php'], $c ), 'composer.json requires ^x.y' );
        $this->assertTrue( version_compare( $minimum, $c[1] . '.0', '>=' ), "MinimumVersion $minimum is below composer's ^{$c[1]}" );
        $this->assertSame( $c[1] . '.0', $minimum );
    }
}
