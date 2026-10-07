<?php
/**
 * The spell checker's JSON reader and checker classes create no properties they
 * do not declare (a deprecation since PHP 8.2, an Error in PHP 9).
 *
 * Moxiecode_JSONReader declared $_lastLocations but pushed and popped an
 * undeclared $_lastLocation; Moxiecode_JSON, SpellChecker and PSpellShell
 * declared nothing they assign.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$classes = __DIR__ . '/../../../../extension/ezoe/modules/ezoe/classes';
require_once $classes . '/utils/mcejson.php';
require_once $classes . '/SpellChecker.php';
require_once $classes . '/PSpellShell.php';

class ezoeSpellCheckerPropertiesTest extends PHPUnit\Framework\TestCase
{
    private function withDeprecationsAsErrors( callable $fn )
    {
        set_error_handler( function ( $no, $message ) {
            throw new ErrorException( $message, 0, $no );
        }, E_DEPRECATED | E_USER_DEPRECATED | E_WARNING );
        try
        {
            return $fn();
        }
        finally
        {
            restore_error_handler();
        }
    }

    public function testDecodesNestedJsonWithoutDynamicProperties()
    {
        $json = new Moxiecode_JSON();
        $result = $this->withDeprecationsAsErrors( function () use ( $json ) {
            return $json->decode( '{"method":"checkWords","params":["en",["helo","world"]],"id":"c0"}' );
        } );
        $this->assertSame( 'checkWords', $result['method'] );
        $this->assertSame( array( 'helo', 'world' ), $result['params'][1] );
    }

    public function testCheckerClassesDeclareWhatTheyAssign()
    {
        $this->assertTrue( property_exists( 'SpellChecker', '_config' ) );
        $this->assertTrue( property_exists( 'PSpellShell', '_tmpfile' ) );
        foreach ( array( 'data', 'parents', 'cur' ) as $name )
            $this->assertTrue( property_exists( 'Moxiecode_JSON', $name ), $name );

        $config = array( 'PSpellShell.tmp' => sys_get_temp_dir() );
        $checker = $this->withDeprecationsAsErrors( function () use ( &$config ) {
            return new PSpellShell( $config );
        } );
        $this->assertSame( $config, $checker->_config );
    }
}
