<?php
/**
 * The EmbedID of ezoe/relations, without the database: Exponential\View\Extension\Ezoe\Ezoe\Relations::parseEmbedId().
 *
 *  RE-01 - An object id, eZObject_<id> and eZNode_<id> (the prefix in any case) are read
 *  RE-02 - A missing, empty or malformed EmbedID is false, without a PHP warning
 *  RE-03 - The view sets the embedded object and its type before it reads the EmbedID, so a missing or malformed
 *          one ends with the message and not with an undefined variable
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/relations.php';

use Exponential\View\Extension\Ezoe\Ezoe\Relations;

class expOERelationsEmbedIdTest extends PHPUnit\Framework\TestCase
{
    /** RE-01 */
    public function testTheFormsOfTheAddressAreRead()
    {
        $this->assertSame( array( 'eZObject', 12 ), Relations::parseEmbedId( '12' ) );
        $this->assertSame( array( 'eZObject', 12 ), Relations::parseEmbedId( 12 ) );
        $this->assertSame( array( 'eZObject', 12 ), Relations::parseEmbedId( 'eZObject_12' ) );
        $this->assertSame( array( 'eZNode', 34 ), Relations::parseEmbedId( 'eZNode_34' ) );
        $this->assertSame( array( 'eZNode', 34 ), Relations::parseEmbedId( 'eznode_34' ) );
        $this->assertSame( array( 'eZObject', 5 ), Relations::parseEmbedId( 'EZOBJECT_5' ) );
    }

    public static function malformed()
    {
        return array( 'missing' => array( null ), 'empty' => array( '' ), 'zero' => array( '0' ), 'no underscore' => array( 'eZObject12' ),
                      'no id' => array( 'eZObject_' ), 'other prefix' => array( 'eZFoo_12' ), 'two underscores' => array( 'eZNode_1_2' ),
                      'not a number' => array( 'eZNode_abc' ), 'negative' => array( '-3' ), 'zero id' => array( 'eZObject_0' ),
                      'decimal' => array( '1.5' ), 'spaces' => array( ' 12' ), 'trailing newline' => array( "12\n" ),
                      'array' => array( array( '12' ) ), 'bool' => array( true ), 'underscore only' => array( '_' ),
                      'too long' => array( str_repeat( '9', 40 ) ) );
    }

    /** RE-02 */
    #[PHPUnit\Framework\Attributes\DataProvider( 'malformed' )]
    public function testAMissingOrMalformedEmbedIdIsFalseWithoutAWarning( $value )
    {
        $warnings = array();
        set_error_handler( function ( $number, $message ) use ( &$warnings )
        {
            $warnings[] = $message;
            return true;
        } );
        try
        {
            $result = Relations::parseEmbedId( $value );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertFalse( $result );
        $this->assertSame( array(), $warnings );
    }

    /** RE-03 */
    public function testTheViewSetsTheEmbeddedObjectBeforeItReadsTheAddress()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/relations.php' );
        $object = strpos( $source, '$embedObject = false;' );
        $type = strpos( $source, "\$embedType   = 'eZObject';" );
        $parse = strpos( $source, 'self::parseEmbedId( isset( $Params[\'EmbedID\'] ) ? $Params[\'EmbedID\'] : null )' );
        $use = strpos( $source, 'if ( !$embedObject instanceof \eZContentObject' );
        $this->assertNotFalse( $object );
        $this->assertNotFalse( $type );
        $this->assertNotFalse( $parse );
        $this->assertNotFalse( $use );
        $this->assertLessThan( $parse, $object );
        $this->assertLessThan( $parse, $type );
        $this->assertLessThan( $use, $parse );
        $this->assertStringNotContainsString( "explode('_', \$Params['EmbedID'])", $source );
        $this->assertStringNotContainsString( "(int)\$Params['EmbedID']", $source );
    }
}
