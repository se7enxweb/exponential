<?php
/**
 * eZImageAliasHandler guards: imageAlias() of an attribute that never stored a file is null and creates nothing, and the
 * lazy alias write-back never reverts a row that was published meanwhile.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/datatypes/ezimage/eZImageAliasGuardsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezImageAliasGuardsHandler extends eZImageAliasHandler
{
    public $list = array();
    public function __construct() {}
    function aliasList( $checkValidity = true ) { return $this->list; }
}

class eZImageAliasGuardsTest extends PHPUnit\Framework\TestCase
{
    private function xml( $serial, $dirpath )
    {
        return '<?xml version="1.0" encoding="utf-8"?><ezimage serial_number="' . $serial . '" is_valid="1" dirpath="' . $dirpath . '"><original attribute_id="1"/></ezimage>';
    }

    private function dom( $serial, $dirpath )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $dom->loadXML( $this->xml( $serial, $dirpath ) );
        return $dom;
    }

    public function testAttributeWithoutFileGivesNull()
    {
        ezpLiveInstallation::requireOrSkip();
        $handler = new ezImageAliasGuardsHandler();
        $handler->list = array( 'original' => array( 'url' => '', 'basename' => '', 'is_new' => false ) );
        $this->assertNull( $handler->imageAlias( 'small' ) );
        $this->assertSame( array( 'original' ), array_keys( $handler->list ), 'no alias was created' );
    }

    public function testAttributeWithoutOriginalGivesNull()
    {
        ezpLiveInstallation::requireOrSkip();
        $handler = new ezImageAliasGuardsHandler();
        $this->assertNull( $handler->imageAlias( 'small' ) );
    }

    public function testUnknownAliasGivesNull()
    {
        ezpLiveInstallation::requireOrSkip();
        $handler = new ezImageAliasGuardsHandler();
        $handler->list = array( 'original' => array( 'url' => 'var/a.png', 'basename' => 'a' ) );
        $this->assertNull( $handler->imageAlias( 'no_such_alias_name' ) );
    }

    public function testPublishedRowSupersedesDraftDom()
    {
        $draft = $this->dom( '3', 'var/site/storage/images-versioned/1/2-3-eng-GB' );
        $published = $this->xml( '', 'var/site/storage/images/1/2-eng-GB' );
        $this->assertTrue( eZImageAliasHandler::storedXMLSupersedesDOMTree( $published, $draft ) );
    }

    public function testUnchangedRowDoesNotSupersede()
    {
        $dom = $this->dom( '3', 'var/a' );
        $this->assertFalse( eZImageAliasHandler::storedXMLSupersedesDOMTree( $this->xml( '3', 'var/a' ), $dom ) );
    }

    public function testEmptyBrokenOrForeignStoredValuesNeverSupersede()
    {
        $dom = $this->dom( '3', 'var/a' );
        foreach ( array( '', '<ezimage', '<other serial_number="9" dirpath="x"/>', '<?xml version="1.0"?><a/>' ) as $stored )
        {
            $this->assertFalse( eZImageAliasHandler::storedXMLSupersedesDOMTree( $stored, $dom ), $stored );
        }
        $this->assertFalse( eZImageAliasHandler::storedXMLSupersedesDOMTree( $this->xml( '9', 'x' ), null ) );
    }
}
