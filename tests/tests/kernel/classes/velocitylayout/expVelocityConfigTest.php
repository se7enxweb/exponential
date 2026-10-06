<?php
/**
 * expVelocityConfig, behind `exp:velocity config`: the settings velocity.ini defines (read from the packaged file),
 * what get() and listAll() report, and the refusals of set() and unset that write nothing (a setting the packaged
 * file does not define, a setting that is not overridden).
 *
 * No database, and nothing is written: the successful set and unset write the installation's override and are not
 * called here.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class expVelocityConfigTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        if ( !is_file( 'settings/velocity.ini' ) )
            $this->markTestSkipped( 'settings/velocity.ini is not in this checkout' );
    }

    /**
     * The blocks and variables of settings/velocity.ini, parsed independently of the class.
     */
    private static function packaged()
    {
        $known = array();
        $block = null;
        foreach ( file( 'settings/velocity.ini', FILE_IGNORE_NEW_LINES ) as $line )
        {
            if ( preg_match( '/^\s*\[([^\]]+)\]\s*$/', $line, $m ) )
            {
                $block = $m[1];
                $known += array( $block => array() );
            }
            else if ( $block !== null && preg_match( '/^\s*([^#=\s][^=]*?)\s*=/', $line, $m ) && !in_array( $m[1], $known[$block], true ) )
                $known[$block][] = $m[1];
        }
        return $known;
    }

    public function testKnownSettingsAreThePackagedOnes()
    {
        $config = new expVelocityConfig();
        $known = $config->known();
        $this->assertSame( self::packaged(), $known );
        $this->assertNotEmpty( $known );
        $this->assertSame( $known, $config->known(), 'kept for the next call' );
    }

    public function testIsKnown()
    {
        $config = new expVelocityConfig();
        foreach ( $config->known() as $block => $variables )
        {
            foreach ( $variables as $variable )
                $this->assertTrue( $config->isKnown( $block, $variable ), "[$block] $variable" );
            $this->assertFalse( $config->isKnown( $block, 'K1NoSuchVariable' ) );
            $this->assertFalse( $config->isKnown( strtolower( $block ) . 'x', $variables ? $variables[0] : 'x' ) );
        }
    }

    public function testListAllReportsEveryKnownSettingThatIsSet()
    {
        $config = new expVelocityConfig();
        $ini = eZINI::instance( 'velocity.ini' );
        $rows = $config->listAll();
        $this->assertNotEmpty( $rows );
        foreach ( $rows as $row )
        {
            $this->assertSame( array( 'block', 'variable', 'value', 'overridden' ), array_keys( $row ) );
            $this->assertTrue( $config->isKnown( $row['block'], $row['variable'] ) );
            $this->assertSame( $ini->variable( $row['block'], $row['variable'] ), $row['value'] );
            $this->assertIsBool( $row['overridden'] );
        }
        $block = $rows[0]['block'];
        foreach ( $config->listAll( strtoupper( $block ) ) as $row )
            $this->assertSame( $block, $row['block'], 'the block filter ignores case' );
        $this->assertSame( array(), $config->listAll( 'K1NoSuchBlock' ) );
    }

    public function testGet()
    {
        $config = new expVelocityConfig();
        $row = $config->listAll()[0];
        $got = $config->get( $row['block'], $row['variable'] );
        $this->assertTrue( $got['ok'] );
        $this->assertSame( $row['value'], $got['value'] );
        $this->assertSame( $row['overridden'], $got['overridden'] );
        $this->assertSame( array( 'ok' => false, 'message' => '[K1Block] K1Variable is not set' ), $config->get( 'K1Block', 'K1Variable' ) );
    }

    public function testSetRefusesWhatVelocityIniDoesNotDefine()
    {
        $config = new expVelocityConfig();
        $result = $config->set( 'K1Block', 'K1Variable', 'x' );
        $this->assertFalse( $result['ok'] );
        $this->assertSame( "[K1Block] K1Variable is not a setting velocity.ini defines; run 'config list' to see what is", $result['message'] );
    }

    public function testUnsetRefusesWhatIsNotOverridden()
    {
        $config = new expVelocityConfig();
        $this->assertSame( array( 'ok' => false, 'message' => '[K1Block] K1Variable is not overridden here; nothing to remove' ),
                           $config->unsetVariable( 'K1Block', 'K1Variable' ) );
    }

    public function testPaths()
    {
        $config = new expVelocityConfig();
        $this->assertSame( array(
            'source of truth' => 'settings/velocity.ini',
            'this installation' => 'settings/override/velocity.ini.append.php',
            'generated on every start, do not edit' => 'var/tmp/velocity-server.json',
            'read by nothing' => '/etc/qbix/qbix.json',
        ), $config->paths() );
    }
}
