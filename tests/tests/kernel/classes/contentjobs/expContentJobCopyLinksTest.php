<?php
/**
 * After a subtree is copied, links, embeds and objects in the copies' XML text that point inside the copied subtree
 * are re-pointed at the copies (expContentJobCopySubtree::fixXMLString()); everything else, and whatever is inside a
 * literal block, is left as it was. Also the overlap rules of content job locks, which decide whether two jobs may
 * work on the tree at the same time.
 *
 * No database: the maps of a copy are given to the job directly.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class expContentJobCopyLinksTestJob extends expContentJobCopySubtree
{
    public function withMaps( array $nodeMap, array $objectMap )
    {
        $this->nodeMap = $nodeMap;
        $this->objectMap = $objectMap;
        return $this;
    }
}

class expContentJobCopyLinksTest extends PHPUnit\Framework\TestCase
{
    private function job()
    {
        // source nodes 10, 11 -> 110, 111; source objects 20, 21 -> 120, 121
        return ( new expContentJobCopyLinksTestJob() )->withMaps( array( 10 => 110, 11 => 111 ), array( 20 => 120, 21 => 121 ) );
    }

    public static function xmlProvider()
    {
        return array(
            'link to a node' => array( '<p><link node_id="10">x</link></p>', '<p><link node_id="110">x</link></p>' ),
            'link to an object' => array( '<p><link object_id="20">x</link></p>', '<p><link object_id="120">x</link></p>' ),
            'link outside the subtree' => array( '<p><link node_id="99">x</link></p>', '<p><link node_id="99">x</link></p>' ),
            'link by url' => array( '<p><link url_id="10">x</link></p>', '<p><link url_id="10">x</link></p>' ),
            'node id wins over object id' => array( '<link object_id="20" node_id="10">x</link>', '<link object_id="20" node_id="110">x</link>' ),
            'embed' => array( '<embed view="embed" node_id="11"/>', '<embed view="embed" node_id="111"/>' ),
            'inline embed' => array( '<embed-inline object_id="21"/>', '<embed-inline object_id="121"/>' ),
            'object' => array( '<object id="21" size="medium"/>', '<object id="121" size="medium"/>' ),
            'several' => array( '<link node_id="10">a</link><link node_id="11">b</link><link node_id="99">c</link>',
                                '<link node_id="110">a</link><link node_id="111">b</link><link node_id="99">c</link>' ),
            'an id that grows' => array( '<link node_id="10">a</link><link node_id="1">b</link>', '<link node_id="110">a</link><link node_id="1">b</link>' ),
            'inside a literal' => array( '<literal class="html">&lt;link node_id="10"&gt;</literal>', '<literal class="html">&lt;link node_id="10"&gt;</literal>' ),
            'nothing to change' => array( '<p>plain</p>', '<p>plain</p>' ),
            'empty' => array( '', '' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('xmlProvider')]
    public function testLinksInsideTheCopyPointAtTheCopies( $xml, $expected )
    {
        $this->assertSame( $expected, $this->job()->fixXMLString( $xml ) );
    }

    public function testAnObjectAfterALinkIsStillRepointed()
    {
        $xml = '<p><link node_id="10">x</link></p><object id="21"/>';
        $this->assertSame( '<p><link node_id="110">x</link></p><object id="121"/>', $this->job()->fixXMLString( $xml ) );
    }

    // ---------------------------------------------------------------- locks

    public static function overlapProvider()
    {
        $sub = expContentJobLock::SUBTREE;
        $node = expContentJobLock::NODE;
        return array(
            'same subtree' => array( '/1/2/42/', $sub, '/1/2/42/', $sub, true ),
            'subtree inside subtree' => array( '/1/2/42/50/', $sub, '/1/2/42/', $sub, true ),
            'subtree around subtree' => array( '/1/2/', $sub, '/1/2/42/', $sub, true ),
            'siblings' => array( '/1/2/42/', $sub, '/1/2/43/', $sub, false ),
            'number sharing a start' => array( '/1/2/4/', $sub, '/1/2/42/', $sub, false ),
            'node inside a subtree' => array( '/1/2/42/', $sub, '/1/2/42/50/', $node, true ),
            'node above a subtree' => array( '/1/2/42/', $sub, '/1/2/', $node, false ),
            'subtree above a node' => array( '/1/2/42/50/', $node, '/1/2/', $sub, true ),
            'two nodes' => array( '/1/2/', $node, '/1/2/', $node, false ),
            'not a path' => array( 'x', $sub, '/1/', $sub, false ),
            'path without slashes' => array( '1/2/42', $sub, '/1/2/42/', $sub, true ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('overlapProvider')]
    public function testLockOverlaps( $pathA, $modeA, $pathB, $modeB, $overlap )
    {
        $this->assertSame( $overlap, expContentJobLock::overlaps( array( 'path' => $pathA, 'mode' => $modeA ), array( 'path' => $pathB, 'mode' => $modeB ) ) );
    }

    public static function pathProvider()
    {
        return array( array( '/1/2/', '/1/2/' ), array( '1/2', '/1/2/' ), array( ' /1/ ', '/1/' ), array( '/1//2/', '' ), array( '/a/', '' ), array( '', '' ), array( null, '' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('pathProvider')]
    public function testLockPaths( $path, $expected )
    {
        $this->assertSame( $expected, expContentJobLock::normalizePath( $path ) );
    }
}
