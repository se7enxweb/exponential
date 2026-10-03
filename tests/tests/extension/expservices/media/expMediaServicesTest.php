<?php
require_once __DIR__ . '/expMediaTestCase.php';

/** expmedia: audio and video (ezmedia) information and statistics. */
class expMediaServicesTest extends expMediaTestCase
{
    public function testEveryServiceIsDeclared()
    {
        $this->assertDeclared( 'expMediaServices', 10 );
    }

    public function testStatsGroupsByMime()
    {
        $s = $this->ok( 'expMediaServices', 'stats' )['data'];
        $this->assertArrayHasKey( 'files', $s );
        $this->assertIsArray( $s['groups'] );
    }

    public function testByMime()
    {
        $this->assertIsArray( $this->ok( 'expMediaServices', 'bymime' )['data'] );
    }

    public function testMimeGroup()
    {
        $this->assertSame( 'video', $this->ok( 'expMediaServices', 'mimegroup', array( 'video/mp4' ) )['data']['group'] );
    }

    public function testSafeName()
    {
        $this->assertArrayHasKey( 'safe', $this->ok( 'expMediaServices', 'safename', array( 'a b.mp3' ) )['data'] );
    }

    public function testListIsPaged()
    {
        $this->assertPaged( $this->ok( 'expMediaServices', 'list', array( 43, 5, 0 ) ) );
    }

    public function testInfoOfAMediaNode()
    {
        list( $node, $attr ) = $this->nodeWith( 'ezmedia', false );
        $i = $this->ok( 'expMediaServices', 'info', array( $node->attribute( 'node_id' ), $attr->contentClassAttributeIdentifier() ) )['data'];
        $this->assertArrayHasKey( 'media', $i );
        $this->assertArrayHasKey( 'width', $i['media'] );
    }

    public function testPlayerSettingsAndUrl()
    {
        list( $node ) = $this->nodeWith( 'ezmedia', false );
        $p = $this->ok( 'expMediaServices', 'player', array( $node->attribute( 'node_id' ) ) )['data'];
        $this->assertArrayHasKey( 'autoplay', $p );
        $this->assertStringContainsString( 'content/download/', $this->ok( 'expMediaServices', 'url', array( $node->attribute( 'node_id' ) ) )['data']['url'] );
    }

    public function testByObjectListsRows()
    {
        list( $node ) = $this->nodeWith( 'ezmedia', false );
        $this->assertNotEmpty( $this->ok( 'expMediaServices', 'byobject', array( $node->attribute( 'contentobject_id' ) ) )['data'] );
    }

    public function testByObjectOfMissingObjectIs404()
    {
        $this->fails( 404, 'expMediaServices', 'byobject', array( 99999999 ) );
    }

    public function testNodeWithoutMediaIs404()
    {
        $this->fails( 404, 'expMediaServices', 'info', array( 2 ) );
    }
}
