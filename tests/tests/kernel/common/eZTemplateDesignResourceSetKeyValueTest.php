<?php
/**
 * eZTemplateDesignResource::setKeyValue( $key, $value, $override ): one override key, replaced by default and
 * kept with $override = false; and the layout key that eZNodeviewfunctions::generateNodeViewData() sets that way
 * before the node's keys: '' for a normal view, the layout of layout/set kept, so an override can tell a layout
 * view apart (Match[layout]). No database: the node and object are stand-ins, and the view stops at the keys.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/common/eZTemplateDesignResourceSetKeyValueTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 856a95e240), Copyright (C) Mugo Web, GPL v2
 */

class eZTemplateDesignResourceSetKeyValueTestStop extends Exception
{
}

/** Records the keys as the node view hands them over, and stops the view there. */
class eZTemplateDesignResourceSetKeyValueTestResource extends eZTemplateDesignResource
{
    public $keysBeforeNodeKeys;

    function setKeys( $keys )
    {
        $this->keysBeforeNodeKeys = $this->Keys;
        parent::setKeys( $keys );
        throw new eZTemplateDesignResourceSetKeyValueTestStop();
    }
}

class eZTemplateDesignResourceSetKeyValueTest extends PHPUnit\Framework\TestCase
{
    private $hadInstance;
    private $instance;

    protected function setUp(): void
    {
        $this->hadInstance = array_key_exists( 'eZTemplateDesignResourceInstance', $GLOBALS );
        $this->instance = $this->hadInstance ? $GLOBALS['eZTemplateDesignResourceInstance'] : null;
    }

    protected function tearDown(): void
    {
        if ( $this->hadInstance )
            $GLOBALS['eZTemplateDesignResourceInstance'] = $this->instance;
        else
            unset( $GLOBALS['eZTemplateDesignResourceInstance'] );
    }

    public function testAKeyIsSetAndReplaced()
    {
        $res = new eZTemplateDesignResource();
        $this->assertSame( $res, $res->setKeyValue( 'layout', 'print' ) );
        $this->assertSame( 'print', $res->keys()['layout'] );
        $res->setKeyValue( 'layout', 'pdf' );
        $this->assertSame( 'pdf', $res->keys()['layout'] );
        $res->setKeyValue( 'layout', 'rss', true );
        $this->assertSame( 'rss', $res->keys()['layout'] );
    }

    public function testWithoutOverrideAnExistingKeyIsKept()
    {
        $res = new eZTemplateDesignResource();
        $res->setKeys( array( array( 'layout', 'print' ) ) );
        $res->setKeyValue( 'layout', '', false );
        $this->assertSame( 'print', $res->keys()['layout'] );
    }

    public function testWithoutOverrideAMissingKeyGetsTheValue()
    {
        $res = new eZTemplateDesignResource();
        $res->setKeyValue( 'layout', '', false );
        $this->assertSame( '', $res->keys()['layout'] );
    }

    public function testWithoutOverrideAKeySetToAnEmptyValueIsKept()
    {
        $res = new eZTemplateDesignResource();
        $res->setKeyValue( 'section', 0 );
        $res->setKeyValue( 'section', 5, false );
        $this->assertSame( 0, $res->keys()['section'] );
    }

    public function testOtherKeysAreLeftAlone()
    {
        $res = new eZTemplateDesignResource();
        $res->setKeys( array( array( 'node', 2 ), array( 'class_identifier', 'folder' ) ) );
        $res->setKeyValue( 'layout', 'print' );
        $this->assertSame( array( 'node' => 2, 'class_identifier' => 'folder', 'layout' => 'print' ), $res->keys() );
    }

    public function testANormalNodeViewHasAnEmptyLayoutKey()
    {
        $this->assertSame( '', $this->layoutKeyOfANodeView( null ) );
    }

    public function testALayoutViewKeepsItsLayoutKey()
    {
        $this->assertSame( 'print', $this->layoutKeyOfANodeView( 'print' ) );
    }

    /**
     * @param string|null $layout what layout/set put in the layout key before the view, null for nothing
     * @return mixed the layout key the node view's templates are matched with
     */
    private function layoutKeyOfANodeView( $layout )
    {
        $res = new eZTemplateDesignResourceSetKeyValueTestResource();
        if ( $layout !== null )
            $res->setKeyValue( 'layout', $layout );
        $GLOBALS['eZTemplateDesignResourceInstance'] = $res;

        $objectAttributes = array( 'section_id' => null, 'id' => 1, 'contentclass_id' => 1, 'remote_id' => 'r',
                                   'match_ingroup_id_list' => array(), 'state_id_array' => array(),
                                   'state_identifier_array' => array() );
        $object = $this->createStub( 'eZContentObject' );
        $object->method( 'attribute' )->willReturnCallback( function ( $name ) use ( $objectAttributes ) {
            return isset( $objectAttributes[$name] ) ? $objectAttributes[$name] : null;
        } );
        $nodeAttributes = array( 'node_id' => 2, 'parent_node_id' => 1, 'class_identifier' => 'folder', 'remote_id' => 'n',
                                 'depth' => 1, 'url_alias' => '', 'parent' => null );
        $node = $this->createStub( 'eZContentObjectTreeNode' );
        $node->method( 'attribute' )->willReturnCallback( function ( $name ) use ( $nodeAttributes ) {
            return isset( $nodeAttributes[$name] ) ? $nodeAttributes[$name] : null;
        } );

        try
        {
            eZNodeviewfunctions::generateNodeViewData( $this->createStub( 'eZTemplate' ), $node, $object, false, 'full', 0 );
            $this->fail( 'The node view did not set its keys' );
        }
        catch ( eZTemplateDesignResourceSetKeyValueTestStop $e )
        {
        }
        $this->assertArrayHasKey( 'layout', $res->keysBeforeNodeKeys );
        $keys = $res->keys();
        $this->assertSame( 2, $keys['node'], 'the node keys follow' );
        return $keys['layout'];
    }
}
