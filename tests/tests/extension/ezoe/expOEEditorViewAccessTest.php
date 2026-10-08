<?php
/**
 * ezoe/tags and ezoe/relations on the live installation, each in a process of its own as the admin; the rules
 * without the database are in expOEDialogAccessRulesTest and expOERelationsEmbedIdTest.
 *
 *  - ezoe/tags opens for who may read and edit the object, for the published version as before
 *  - ezoe/relations without an EmbedID, or with a malformed one, ends with its message and without a PHP warning
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expOETestCase.php';

class expOEEditorViewAccessTest extends expOETestCase
{
    /** The object of the content root and its current version */
    private function rootObject()
    {
        $node = eZContentObjectTreeNode::fetch( 2 );
        $this->assertInstanceOf( 'eZContentObjectTreeNode', $node );
        $object = eZContentObject::fetch( (int)$node->attribute( 'contentobject_id' ) );
        return array( (int)$object->attribute( 'id' ), (int)$object->attribute( 'current_version' ) );
    }

    public function testTagsOpensForWhoMayReadAndEdit()
    {
        list( $id, $version ) = $this->rootObject();
        $out = $this->runView( 'tags', array( (string)$id, (string)$version, 'link' ) );
        $this->assertStringNotContainsString( 'Invalid parameter', $out );
        $this->assertNotSame( '', trim( $out ) );
    }

    public static function embedIds()
    {
        return array( 'missing' => array( null ), 'malformed' => array( 'eZNode_' ), 'no underscore' => array( 'eZObjectx' ),
                      'two parts missing' => array( '_' ) );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'embedIds' )]
    public function testRelationsWithoutAUsableEmbedIdEndsWithItsMessage( $embedId )
    {
        list( $id, $version ) = $this->rootObject();
        $params = array( (string)$id, (string)$version, 'auto' );
        if ( $embedId !== null )
            $params[] = $embedId;
        $out = $this->runView( 'relations', $params );
        $this->assertStringContainsString( 'EmbedID', $out );
        $this->assertStringNotContainsString( 'Undefined', $out );
        $this->assertStringNotContainsString( 'Warning', $out );
    }
}
