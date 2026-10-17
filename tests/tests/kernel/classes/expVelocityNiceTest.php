<?php
/**
 * File containing the expVelocityNiceTest class.
 *
 * [ServerSettings] Nice: the server runs at the configured priority whatever
 * the priority of the shell that started it (2026-10-17: a deploy run under
 * nice -n 19 left every worker at 19). No server and no database.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class expVelocityNiceTest extends ezpTestCase
{
    public function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        parent::tearDown();
    }

    /**
     * @param string $value
     * @return int|null
     */
    protected function niceFor( $value )
    {
        ezpINIHelper::setINISetting( 'velocity.ini', 'ServerSettings', 'Nice', $value );
        return expVelocity::create( 'velocity.ini', 'qbix' )->niceSetting();
    }

    public function testNiceSetting()
    {
        $this->assertSame( 5, $this->niceFor( '5' ) );
        $this->assertSame( 0, $this->niceFor( '0' ) );
        $this->assertSame( -3, $this->niceFor( '-3' ) );
        $this->assertSame( 7, $this->niceFor( ' +7 ' ) );
        $this->assertSame( 19, $this->niceFor( '40' ), 'clamped to 19' );
        $this->assertSame( -20, $this->niceFor( '-99' ), 'clamped to -20' );
        $this->assertNull( $this->niceFor( 'inherit' ), 'inherit: the old behavior' );
        $this->assertNull( $this->niceFor( 'Inherit' ) );
        $this->assertNull( $this->niceFor( '' ) );
        $this->assertNull( $this->niceFor( 'five' ), 'not a number: nothing is changed' );
        $this->assertNull( $this->niceFor( '5; rm -rf /' ), 'never reaches a shell' );
    }

    public function testNiceOfThisProcess()
    {
        $this->assertSame( self::ownNice(), expVelocity::niceOf( getmypid() ) );
        $this->assertNull( expVelocity::niceOf( 4194305 ), 'a pid that cannot exist' );
        $this->assertNull( expVelocity::niceOf( 0 ) );
    }

    public function testNicePrefix()
    {
        $this->assertSame( "'/usr/bin/nice' -n -14 ", expVelocity::nicePrefix( 5, 19, '/usr/bin/nice' ),
            'a deploy under nice 19 starts the server at 5' );
        $this->assertSame( "'/usr/bin/nice' -n -10 ", expVelocity::nicePrefix( 5, 15, '/usr/bin/nice' ) );
        $this->assertSame( "'/usr/bin/nice' -n 5 ", expVelocity::nicePrefix( 5, 0, '/usr/bin/nice' ) );
        $this->assertSame( "'/usr/bin/nice' -n 5 ", expVelocity::nicePrefix( 5, null, '/usr/bin/nice' ), 'unknown current: 0' );
        $this->assertSame( '', expVelocity::nicePrefix( 5, 5, '/usr/bin/nice' ), 'already there' );
        $this->assertSame( '', expVelocity::nicePrefix( null, 19, '/usr/bin/nice' ), 'inherit' );
        $this->assertSame( '', expVelocity::nicePrefix( 5, 19, false ), 'no nice binary: started as before' );
        $this->assertSame( '', expVelocity::nicePrefix( 5, 19, '' ) );
        $this->assertSame( "'/opt/my nice' -n 1 ", expVelocity::nicePrefix( 1, 0, '/opt/my nice' ), 'the binary is quoted' );
    }

    public function testNiceProblems()
    {
        $nices = array( 11 => 5, 12 => 19, 13 => null );
        $niceOf = function ( $pid ) use ( $nices ) { return isset( $nices[$pid] ) ? $nices[$pid] : null; };

        $problems = expVelocity::niceProblems( array( 11, 12, 13 ), 5, $niceOf );
        $this->assertCount( 1, $problems, 'only the parent at another nice; an unreadable one is not a problem' );
        $this->assertStringContainsString( 'parent 12 runs at nice 19, not [ServerSettings] Nice=5', $problems[0] );
        $this->assertStringContainsString( 'exp:velocity restart', $problems[0] );

        $this->assertSame( array(), expVelocity::niceProblems( array( 11, 12 ), null, $niceOf ), 'inherit: never a problem' );
        $this->assertSame( array(), expVelocity::niceProblems( array(), 5, $niceOf ) );
    }

    /**
     * This process's nice value, the way the kernel reports it.
     *
     * @return int|null
     */
    protected static function ownNice()
    {
        if ( function_exists( 'pcntl_getpriority' ) )
        {
            $value = pcntl_getpriority();
            return $value === false ? null : (int)$value;
        }
        $stat = (string)@file_get_contents( '/proc/self/stat' );
        $fields = preg_split( '/\s+/', trim( substr( $stat, strrpos( $stat, ')' ) + 1 ) ) );
        return isset( $fields[16] ) ? (int)$fields[16] : null;
    }
}
