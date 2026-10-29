<?php
/**
 * The read side of the WebDAV backend for a single resource (GET of a file): a path without a node and a node the
 * user may not read. No database: the node lookup is replaced.
 *
 *  WR-01 - A path without a node: getContentNodeData() answers "nothing found"; it called fetchNodeInfo() with the
 *          false of fetchNodeByTranslation() first (a call on false)
 *  WR-02 - A node the user may not read: refused (false), and nothing about it is read (no fetchNodeInfo())
 *  WR-03 - getResourceContents() of a refused or missing resource is false, without reading an offset of false
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expWebdavReadProbe extends eZWebDAVContentBackend
{
    /** @var eZContentObjectTreeNode|false */
    public $node = false;

    /** @var int */
    public $infoCalls = 0;

    public function __construct()
    {
        $this->availableSites = array( 'site' );
    }

    public function callGetContentNodeData( $target )
    {
        return $this->getContentNodeData( array( 'data' => false, 'file' => false ), 'site', 'Content', $target,
                                          '/site/Content/' . $target );
    }

    public function callGetResourceContents( $path )
    {
        return $this->getResourceContents( $path );
    }

    protected function internalNodePath( $virtualFolder, $collection )
    {
        return $collection;
    }

    protected function fetchNodeByTranslation( $nodePathString )
    {
        return $this->node;
    }

    protected function fetchNodeInfo( $target, &$node )
    {
        $this->infoCalls++;
        return array( 'name' => 'probe' );
    }
}

class expWebdavReadRefusalTest extends PHPUnit\Framework\TestCase
{
    /** WR-01 */
    public function testPathWithoutNode()
    {
        $probe = new expWebdavReadProbe();
        $probe->node = false;
        $this->assertSame( array( 'data' => false, 'file' => false ), $probe->callGetContentNodeData( 'missing.txt' ) );
        $this->assertSame( 0, $probe->infoCalls );
    }

    /** WR-02 */
    public function testNodeTheUserMayNotRead()
    {
        $node = $this->createStub( 'eZContentObjectTreeNode' );
        $node->method( 'canRead' )->willReturn( false );
        $probe = new expWebdavReadProbe();
        $probe->node = $node;
        $this->assertFalse( $probe->callGetContentNodeData( 'hidden.txt' ) );
        $this->assertSame( 0, $probe->infoCalls, 'nothing about the node is read' );
    }

    /** WR-03 */
    public function testResourceContentsOfARefusedResource()
    {
        $node = $this->createStub( 'eZContentObjectTreeNode' );
        $node->method( 'canRead' )->willReturn( false );
        $probe = new expWebdavReadProbe();
        $probe->node = $node;
        $warnings = array();
        set_error_handler( function ( $no, $str ) use ( &$warnings ) { $warnings[] = $str; return true; } );
        try
        {
            $this->assertFalse( $probe->callGetResourceContents( '/site/Content/hidden.txt' ) );
            $probe->node = false;
            $this->assertFalse( $probe->callGetResourceContents( '/site/Content/missing.txt' ) );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertSame( array(), $warnings );
    }
}
