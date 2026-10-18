<?php
/**
 * expNodeQuickPick (doc/features/6.0/quick-location-buttons.md): the keys of the quick location buttons, their labels,
 * the rows with the current place and the place it lies in, a filter that keeps places out, and the shared component
 * .a4-quickpick. No database.
 *
 *  QP-01  the keys: named places, node ids, node remote ids, URL alias paths; anything else is null
 *  QP-02  the labels of the named places, the node's name for the rest
 *  QP-03  current and within, never by a prefix of an id
 *  QP-04  a row: visible children once each, the filter before the limit, "more"
 *  QP-05  the component in both designs, with dark-safe tokens only, and the content checks use it
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expNodeQuickPickTest extends PHPUnit\Framework\TestCase
{
    /** QP-01 */
    public function testKeys()
    {
        $this->assertSame( 'node:1', expNodeQuickPick::source( 'tree' ) );
        $this->assertSame( 'ini:RootNode', expNodeQuickPick::source( 'content' ) );
        $this->assertSame( 'ini:MediaRootNode', expNodeQuickPick::source( 'media' ) );
        $this->assertSame( 'node:58', expNodeQuickPick::source( '58' ) );
        $this->assertSame( 'remote:a1b2-c3_d4', expNodeQuickPick::source( 'remote:a1b2-c3_d4' ) );
        $this->assertSame( 'path:media/images', expNodeQuickPick::source( 'Media/Images' ) );
        foreach ( array( '', '0', '99999999999', 'remote:', 'remote:a b', 'remote:x/y', '../x', 'a//b', '/a', 'a b', null, 5, array( 'tree' ),
                         str_repeat( 'a', 201 ) ) as $bad )
            $this->assertNull( expNodeQuickPick::source( $bad ), var_export( $bad, true ) );
        $this->assertSame( 1, expNodeQuickPick::nodeID( 'node:1' ) );
        $this->assertSame( 0, expNodeQuickPick::nodeID( 'other:1' ) );
        $this->assertSame( 0, expNodeQuickPick::nodeID( 'nothing' ) );
        $this->assertSame( 0, expNodeQuickPick::nodeID( 'ini:NoSuchSetting' ) );
        // the content checks ask the same
        $this->assertSame( expNodeQuickPick::source( 'remote:abc' ), expContentCheckScope::presetSource( 'remote:abc' ) );
    }

    /** QP-02 */
    public function testLabels()
    {
        $this->assertSame( 'Websites', expNodeQuickPick::label( 'content', 'Home' ) );
        $this->assertSame( 'Media', expNodeQuickPick::label( 'media', 'Media files' ) );
        $this->assertSame( 'Images', expNodeQuickPick::label( 'remote:abc', 'Images' ) );
        $this->assertSame( '', expNodeQuickPick::label( array( 'content' ), '' ) );
    }

    /** QP-03 */
    public function testCurrentAndWithin()
    {
        $this->assertSame( array( 'current' => true, 'within' => false ), expNodeQuickPick::state( 43, 43, '/1/43/' ) );
        $this->assertSame( array( 'current' => false, 'within' => true ), expNodeQuickPick::state( 43, 51, '/1/43/51/' ) );
        $this->assertSame( array( 'current' => false, 'within' => false ), expNodeQuickPick::state( 4, 43, '/1/43/' ), 'not by a prefix' );
        $this->assertSame( array( 'current' => false, 'within' => false ), expNodeQuickPick::state( 0, 0, '' ) );
    }

    /** QP-04 */
    public function testRow()
    {
        $children = array();
        foreach ( range( 51, 60 ) as $id )
            $children[] = array( 'node_id' => $id, 'name' => 'Folder ' . $id, 'is_hidden' => 0, 'is_invisible' => 0, 'ok' => $id % 2 === 1 );
        $children[] = array( 'node_id' => 51, 'name' => 'twice', 'ok' => true );
        $children[] = array( 'node_id' => 70, 'name' => 'hidden', 'is_hidden' => 1, 'ok' => true );
        $accept = function ( array $child ) { return !empty( $child['ok'] ); };

        $row = expNodeQuickPick::row( $children, '/1/43/53/90/', 90, 3, $accept );
        $this->assertSame( array( 51, 53, 55 ), array_column( $row['items'], 'node_id' ), 'the filter comes before the limit' );
        $this->assertTrue( $row['more'] );
        $this->assertTrue( $row['items'][1]['within'] );
        $this->assertSame( array( 'node_id', 'name', 'current', 'within' ), array_keys( $row['items'][0] ) );

        $row = expNodeQuickPick::row( $children, '/1/43/55/', 55, 50, $accept );
        $this->assertFalse( $row['more'] );
        $this->assertSame( array( 51, 53, 55, 57, 59 ), array_column( $row['items'], 'node_id' ) );
        $this->assertSame( array( 55 ), array_column( array_filter( $row['items'], function ( $i ) { return $i['current']; } ), 'node_id' ) );
        $this->assertSame( 'Folder 51', $row['items'][0]['name'] );

        $row = expNodeQuickPick::row( $children, '', 0, 50 );
        $this->assertSame( 10, count( $row['items'] ), 'without a filter every visible child' );
        $this->assertSame( array( 'items' => array(), 'more' => false ), expNodeQuickPick::row( array( 'junk', array() ), '', 0, 0 ) );
    }

    /** QP-05 */
    public function testSharedComponent()
    {
        $root = dirname( __DIR__, 4 );
        $admin = (string)file_get_contents( $root . '/design/admin/stylesheets/components.css' );
        $this->assertSame( $admin, (string)file_get_contents( $root . '/design/admin4/stylesheets/components.css' ) );
        $this->assertSame( 1, preg_match( '/\/\* request W608.*?(?=\/\* ----)/s', $admin, $m ) );
        $block = $m[0];
        foreach ( array( '.a4-quickpick {', '.a4-quickpick-label', 'aria-pressed="true"', 'aria-current="true"', '.is-within', 'forced-colors' ) as $needle )
            $this->assertStringContainsString( $needle, $block );
        $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,8}\b|rgba?\(|hsl/i', $block, 'tokens only' );
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $this->assertStringNotContainsString( 'a4-setup-scope', (string)file_get_contents( $root . '/design/' . $design . '/stylesheets/modules/setup.css' ) );
            $this->assertStringContainsString( 'a4-toolbar a4-quickpick', (string)file_get_contents( $root . '/design/' . $design . '/templates/contentcheck/scope.tpl' ) );
        }
    }
}
