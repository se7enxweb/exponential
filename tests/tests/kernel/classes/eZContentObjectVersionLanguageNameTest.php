<?php
/**
 * eZContentObject::versionLanguageName() with version 0: the early return before the database raised
 * "Undefined variable $contentObjectID", because the variable is set further down. No database needed.
 *
 *  VL-01 - Version 0 returns false without a PHP warning
 *  VL-02 - A negative version as well (it used to run on to the database)
 *  VL-03 - Both return without touching the database (a stand-in fails on every access)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
/** Database stand-in: every access is an error */
class expVersionLanguageNameStandInDB extends eZNullDB
{
    public static $touched = 0;

    function __construct( $parameters )
    {
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        self::$touched++;
        return array();
    }

    function databaseName()
    {
        self::$touched++;
        return 'stand-in';
    }
}

class eZContentObjectVersionLanguageNameTest extends PHPUnit\Framework\TestCase
{
    private function callCollectingWarnings( $version )
    {
        $previousDB = $GLOBALS['eZDBGlobalInstance'] ?? null;
        expVersionLanguageNameStandInDB::$touched = 0;
        eZDB::setInstance( new expVersionLanguageNameStandInDB( array() ) );
        $object = new eZContentObject( array( 'id' => 4711 ) );
        $messages = array();
        set_error_handler( function ( $no, $str ) use ( &$messages ) { $messages[] = $str; return true; } );
        try
        {
            $result = $object->versionLanguageName( $version );
        }
        finally
        {
            restore_error_handler();
            eZDB::setInstance( $previousDB );
        }
        return array( $result, $messages );
    }

    public function testVersionZeroReturnsFalseWithoutWarning()
    {
        list( $result, $messages ) = $this->callCollectingWarnings( 0 );
        $this->assertFalse( $result );
        $this->assertSame( array(), $messages );
    }

    public function testNegativeVersionReturnsFalseWithoutWarning()
    {
        list( $result, $messages ) = $this->callCollectingWarnings( -1 );
        $this->assertFalse( $result );
        $this->assertSame( array(), $messages );
    }

    public function testNoVersionReachesTheDatabase()
    {
        $this->callCollectingWarnings( 0 );
        $this->callCollectingWarnings( -1 );
        $this->assertSame( 0, expVersionLanguageNameStandInDB::$touched );
    }
}
