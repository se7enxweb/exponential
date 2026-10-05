<?php
/**
 * bin/php/ezpgenerateautoloads.php accepts --allow-root-user, as the operating guide writes it, and an option it does
 * not know ends with a non-zero exit status: it printed the help and exited 0, so "-e --allow-root-user" silently
 * generated nothing. Only --help and an unknown option are run: nothing is scanned or written.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AutoloadGeneratorOptionsTest extends PHPUnit\Framework\TestCase
{
    private function run_( array $args )
    {
        $root = dirname( __DIR__, 5 );
        $proc = proc_open( array_merge( array( PHP_BINARY, 'bin/php/ezpgenerateautoloads.php' ), $args ),
                           array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $root );
        $out = stream_get_contents( $pipes[1] ) . stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        return array( proc_close( $proc ), $out );
    }

    public function testAllowRootUserIsAccepted()
    {
        list( $status, $out ) = $this->run_( array( '--allow-root-user', '--help' ) );
        $this->assertSame( 0, $status );
        $this->assertStringNotContainsString( 'allow-root-user is not', $out );
        $this->assertStringContainsString( 'allow-root-user', $out );
    }

    public function testUnknownOptionExitsNonZero()
    {
        list( $status, $out ) = $this->run_( array( '--no-such-option' ) );
        $this->assertSame( 1, $status );
    }
}
