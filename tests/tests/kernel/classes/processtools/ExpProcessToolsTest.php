<?php
/**
 * expProcessTools finds setsid and the PHP command line, also when open_basedir hides them from is_executable().
 *
 *  PT-01 without open_basedir: setsid and the PHP command line are found and run
 *  PT-02 under open_basedir=<root>:/tmp (the demo's setting): both are still found, although PHP cannot see them
 *  PT-03 the PHP command line found is the running major.minor when there is one
 *  PT-04 error() is empty when both are found
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group processtools
 */
use PHPUnit\Framework\TestCase;

class ExpProcessToolsTest extends TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function child( $basedir )
    {
        $cmd = array( PHP_BINARY, '-n' );
        if ( $basedir !== '' )
            array_push( $cmd, '-d', 'open_basedir=' . $basedir );
        $code = 'require ' . var_export( self::root() . '/kernel/classes/expprocesstools.php', true ) . ';'
              . 'echo json_encode( array( "setsid" => expProcessTools::setsid(), "php" => expProcessTools::phpCli(),'
              . ' "error" => expProcessTools::error(), "is_exec_setsid" => @is_executable( "/usr/bin/setsid" ) ) );';
        array_push( $cmd, '-r', $code );
        $p = proc_open( $cmd, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
        $out = stream_get_contents( $pipes[1] );
        stream_get_contents( $pipes[2] );
        proc_close( $p );
        $data = json_decode( $out, true );
        self::assertIsArray( $data, 'child output: ' . $out );
        return $data;
    }

    public function testFoundWithoutOpenBasedir()
    {
        $d = self::child( '' );
        $this->assertNotFalse( $d['setsid'] );
        $this->assertNotFalse( $d['php'] );
    }

    public function testFoundUnderOpenBasedir()
    {
        $d = self::child( self::root() . ':/tmp' );
        $this->assertFalse( $d['is_exec_setsid'], 'open_basedir must hide setsid from is_executable' );
        $this->assertNotFalse( $d['setsid'], $d['error'] );
        $this->assertNotFalse( $d['php'], $d['error'] );
        $this->assertSame( '', $d['error'] );
    }

    public function testRunningVersionPreferred()
    {
        $d = self::child( self::root() . ':/tmp' );
        $out = shell_exec( escapeshellarg( $d['php'] ) . ' -r ' . escapeshellarg( 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' ) );
        $this->assertSame( PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION, trim( (string)$out ) );
    }
}
