<?php
/**
 * eZSysInfo (lib/ezutils), the CPU and memory figures of the setup and system information pages:
 *   - scanProc() on cpuinfo and meminfo files written for the test: speed, unit, type, memory in kB, MB and GB,
 *     a value without a unit, padding of any width before the value, missing files
 *   - scanDMesg() on a FreeBSD dmesg.boot: CPU type and speed, real memory
 *   - the attribute interface, cpuUnit(), procValue(), and scan() on the machine running the test
 *
 * No database. Fixture files go to a private directory under var/tmp that tearDown() removes.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZSysInfoTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/phpunit-ezsysinfo-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir, 0777, true );
    }

    protected function tearDown(): void
    {
        foreach ( glob( $this->dir . '/*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    private function put( $name, $content )
    {
        file_put_contents( $this->dir . '/' . $name, $content );
        return $this->dir . '/' . $name;
    }

    private function cpuinfo()
    {
        return $this->put( 'cpuinfo',
            "processor\t: 0\nvendor_id\t: GenuineIntel\nmodel name\t: Intel(R) Xeon(R) CPU E5-2680 v4 @ 2.40GHz\n" .
            "cpu MHz\t\t: 2399.998\ncache size\t: 35840 KB\n\nprocessor\t: 1\nmodel name\t: other\ncpu MHz\t\t: 1.0\n" );
    }

    public static function memoryProvider()
    {
        return array(
            'kB'                 => array( "MemTotal:       16318412 kB\nMemFree: 1 kB\n", 16318412 * 1024 ),
            'MB'                 => array( "MemTotal: 2048 MB\n", 2048 * 1024 * 1024 ),
            'GB'                 => array( "MemTotal:\t4 GB\n", 4 * 1024 * 1024 * 1024 ),
            'no unit'            => array( "MemTotal: 123456\n", 123456 ),
            'short padding'      => array( "MemTotal: 1234567 kB\n", 1234567 * 1024 ),
        );
    }

    /**
     * The value is read after the colon. It was read from column 11, so a line padded with fewer spaces than the
     * kernel's usual lost its first digits ("MemTotal: 1234567 kB" gave 234567 kB).
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('memoryProvider')]
    public function testScanProcMemory( $meminfo, $expected )
    {
        $info = new eZSysInfo();
        $this->assertTrue( $info->scanProc( $this->cpuinfo(), $this->put( 'meminfo', $meminfo ) ) );
        $this->assertSame( $expected, $info->memorySize() );
    }

    public function testScanProcCpu()
    {
        $info = new eZSysInfo();
        $info->scanProc( $this->cpuinfo(), $this->put( 'meminfo', "MemTotal: 1 kB\n" ) );
        $this->assertSame( 'Intel(R) Xeon(R) CPU E5-2680 v4 @ 2.40GHz', $info->cpuType(), 'the first processor' );
        $this->assertSame( '2399.998', $info->cpuSpeed() );
        $this->assertSame( 'MHz', $info->cpuUnit() );
        $this->assertSame( 'MHz', $info->attribute( 'cpu_unit' ) );
        $this->assertSame( '2399.998', $info->attribute( 'cpu_speed' ) );
        $this->assertSame( 1024, $info->attribute( 'memory_size' ) );
        $this->assertSame( 'Intel(R) Xeon(R) CPU E5-2680 v4 @ 2.40GHz', $info->attribute( 'cpu_type' ) );
    }

    public function testScanProcWithoutFiles()
    {
        $info = new eZSysInfo();
        $this->assertFalse( $info->scanProc( $this->dir . '/missing', $this->dir . '/missing' ) );
        $this->assertFalse( $info->scanProc( $this->cpuinfo(), $this->dir . '/missing' ) );
        $this->assertFalse( $info->cpuUnit() );
    }

    public function testScanDMesg()
    {
        $file = $this->put( 'dmesg.boot',
            "Copyright (c) 1992-2024 The FreeBSD Project.\n" .
            "CPU: Intel(R) Core(TM) i7 CPU (2933.45-MHz K8-class CPU)\n" .
            "real memory  = 8589934592 (8192 MB)\n" );
        $info = new eZSysInfo();
        $this->assertTrue( $info->scanDMesg( $file ) );
        $this->assertSame( 'Intel(R) Core(TM) i7 CPU (K8-class CPU)', $info->cpuType() );
        $this->assertSame( '2933.45-', $info->cpuSpeed() );
        $this->assertSame( 'MHz', $info->cpuUnit() );
        $this->assertSame( 8589934592, $info->memorySize() );
        $this->assertFalse( $info->scanDMesg( $this->dir . '/missing' ) );
    }

    public function testProcValue()
    {
        $this->assertSame( '2400.000', eZSysInfo::procValue( "cpu MHz\t\t: 2400.000\n" ) );
        $this->assertSame( 'a: b', eZSysInfo::procValue( "x: a: b" ), 'only the first colon separates' );
        $this->assertSame( 'no colon', eZSysInfo::procValue( " no colon " ) );
    }

    public function testAttributes()
    {
        $info = new eZSysInfo();
        $this->assertSame( array( 'is_valid', 'cpu_type', 'cpu_unit', 'cpu_speed', 'memory_size' ), $info->attributes() );
        $this->assertTrue( $info->hasAttribute( 'cpu_unit' ) );
        $this->assertFalse( $info->hasAttribute( 'nothing' ) );
        $this->assertFalse( $info->attribute( 'is_valid' ) );
        $this->assertNull( @$info->attribute( 'nothing' ) );
    }

    public function testScanOfThisMachine()
    {
        $info = new eZSysInfo();
        $valid = $info->scan();
        $this->assertSame( $valid, $info->isValid() );
        if ( $valid && is_readable( '/proc/meminfo' ) )
            $this->assertGreaterThan( 0, $info->memorySize() );
    }
}
