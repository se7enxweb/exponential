<?php
/**
 * After a subtree is copied, links, embeds and objects in the copies' XML text that point inside the copied subtree
 * are re-pointed at the copies (expContentJobCopySubtree::fixXMLString(), remapXMLText()), also before, between and after
 * literal blocks; everything else, and whatever is inside a
 * literal block, is left as it was. Also the overlap rules of content job locks, which decide whether two jobs may
 * work on the tree at the same time.
 *
 * No database: the maps of a copy are given to the job directly.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

#[\PHPUnit\Framework\Attributes\Group('kernel')]
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
            'node id and object id both' => array( '<link object_id="20" node_id="10">x</link>', '<link object_id="120" node_id="110">x</link>' ),
            // the bug of the string scan: a literal block made it jump past every link before it
            'link before a literal' => array( '<p><link node_id="10">a</link></p><literal>code</literal>',
                                              '<p><link node_id="110">a</link></p><literal>code</literal>' ),
            'link after a literal' => array( '<literal>code</literal><p><link node_id="10">a</link></p>',
                                             '<literal>code</literal><p><link node_id="110">a</link></p>' ),
            'literal at the very start, link before another' => array(
                '<literal>a</literal><link object_id="20">b</link><literal>c</literal><embed node_id="11"/>',
                '<literal>a</literal><link object_id="120">b</link><literal>c</literal><embed node_id="111"/>' ),
            'multiple literal blocks' => array(
                '<link node_id="10">1</link><literal>x</literal><link node_id="11">2</link><literal class="html">y</literal><link object_id="21">3</link><literal>z</literal>',
                '<link node_id="110">1</link><literal>x</literal><link node_id="111">2</link><literal class="html">y</literal><link object_id="121">3</link><literal>z</literal>' ),
            'literal with markup-looking text stays' => array(
                '<link node_id="10">a</link><literal>&lt;embed object_id="20"/&gt; &lt;object id="21"&gt; eznode://10</literal>',
                '<link node_id="110">a</link><literal>&lt;embed object_id="20"/&gt; &lt;object id="21"&gt; eznode://10</literal>' ),
            'literal with CDATA stays' => array( '<literal><![CDATA[<link node_id="10">]]></literal><link node_id="10">a</link>',
                                                 '<literal><![CDATA[<link node_id="10">]]></literal><link node_id="110">a</link>' ),
            // the scan took the next link before an earlier embed or object
            'embed before a link' => array( '<embed node_id="11"/><link node_id="10">a</link>', '<embed node_id="111"/><link node_id="110">a</link>' ),
            'object before a link' => array( '<object id="20"/><link node_id="10">a</link>', '<object id="120"/><link node_id="110">a</link>' ),
            'inline embed by node' => array( '<embed-inline node_id="10"/>', '<embed-inline node_id="110"/>' ),
            'embed outside the subtree' => array( '<embed object_id="99"/><embed node_id="20"/>', '<embed object_id="99"/><embed node_id="20"/>' ),
            'ezobject href' => array( '<link href="ezobject://20">x</link>', '<link href="ezobject://120">x</link>' ),
            'eznode href with anchor' => array( '<link href="eznode://11#top">x</link>', '<link href="eznode://111#top">x</link>' ),
            'href outside the subtree' => array( '<link href="ezobject://99">x</link><link href="eznode://20">y</link>',
                                                 '<link href="ezobject://99">x</link><link href="eznode://20">y</link>' ),
            'href by path or a longer number' => array( '<link href="eznode://a/b">x</link><link href="ezobject://200">y</link><link href="http://x/10">z</link>',
                                                        '<link href="eznode://a/b">x</link><link href="ezobject://200">y</link><link href="http://x/10">z</link>' ),
            'anchor element is not a link' => array( '<anchor name="10"/><link node_id="10">a</link>', '<anchor name="10"/><link node_id="110">a</link>' ),
            'not a number' => array( '<link node_id="10x">a</link>', '<link node_id="10x">a</link>' ),
            'not well-formed stays' => array( '<link node_id="10">a</lnk><literal>', '<link node_id="10">a</lnk><literal>' ),
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

    /** A whole stored XML text with literal blocks between the links: every reference outside them re-pointed, none inside. */
    public function testDocumentWithLiteralBlocks()
    {
        $source = file_get_contents( __DIR__ . '/fixtures/copy-xmltext-with-literals.source.xml' );
        $expected = file_get_contents( __DIR__ . '/fixtures/copy-xmltext-with-literals.expected.xml' );
        $this->assertSame( $expected, $this->job()->fixXMLString( $source ) );
        // a second pass changes nothing more (110, 111, 120, 121 are not source ids)
        $this->assertSame( $expected, $this->job()->fixXMLString( $expected ) );
    }

    public function testDocumentWithoutAnythingToChangeIsReturnedByteForByte()
    {
        $xml = "<?xml version='1.0'?>\n<section xmlns:custom=\"http://ez.no/namespaces/ezpublish3/custom/\"><paragraph>a &gt; b &quot;c&quot;<link node_id=\"99\">x</link></paragraph><literal>&lt;x&gt;</literal></section>";
        $this->assertSame( $xml, $this->job()->fixXMLString( $xml ) );
    }

    public function testDocumentKeepsItsDeclarationAndTrailingNewline()
    {
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<section><paragraph><link node_id=\"10\">\xC3\xA4</link></paragraph></section>\n";
        $this->assertSame( "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<section><paragraph><link node_id=\"110\">\xC3\xA4</link></paragraph></section>\n",
                           $this->job()->fixXMLString( $xml ) );
        $this->assertSame( '<section><link node_id="110">x</link></section>', $this->job()->fixXMLString( '<section><link node_id="10">x</link></section>' ) );
    }

    /** The direct subtree copies keep two parallel lists; the same re-pointing works from them. */
    public function testParallelListsOfTheDirectCopies()
    {
        $nodeMap = expContentJobCopySubtree::idMap( array( '10', '11' ), array( '110', '111' ) );
        $objectMap = expContentJobCopySubtree::idMap( array( 20, 21, 22 ), array( 120, 121 ) );
        $this->assertSame( array( 10 => 110, 11 => 111 ), $nodeMap );
        $this->assertSame( array( 20 => 120, 21 => 121 ), $objectMap );
        $this->assertSame( '<link node_id="110">a</link><literal>x</literal><embed object_id="121"/>',
                           expContentJobCopySubtree::remapXMLText( '<link node_id="10">a</link><literal>x</literal><embed object_id="21"/>', $nodeMap, $objectMap ) );
        $this->assertSame( '<link node_id="10">a</link>', expContentJobCopySubtree::remapXMLText( '<link node_id="10">a</link>', array(), array() ) );
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
