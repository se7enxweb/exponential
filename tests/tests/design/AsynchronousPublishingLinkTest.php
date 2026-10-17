<?php
/**
 * The asynchronous publishing page (content/queued, ezasynchronouspublishing.js) links to the published node only
 * when ezpublishingqueue::status gives its address: a user who cannot read the published object (the creator of the
 * version) gets the status "finished" without node_uri, and the link "View the published item" is hidden instead
 * of pointing nowhere. Run in node against a stand-in for jQuery and Exp.io (js/asynchronous_publishing.js).
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AsynchronousPublishingLinkTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AsynchronousPublishingLinkTest extends PHPUnit\Framework\TestCase
{
    public function testTheLinkToTheNodeNeedsItsAddress()
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
            $this->markTestSkipped( 'node is not installed' );
        $script = dirname( __DIR__, 3 ) . '/design/standard/javascript/ezasynchronouspublishing.js';
        $this->assertFileExists( $script );
        $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/asynchronous_publishing.js' ) . ' '
                                   . escapeshellarg( $script ) . ' 2>&1' );
        $rows = array();
        foreach ( array_filter( explode( "\n", $out ) ) as $line )
        {
            $row = json_decode( $line, true );
            $this->assertIsArray( $row, "the harness ran: $out" );
            $rows[$row['name']] = $row;
        }
        $this->assertSame( array(
            'finished with the node address' => array( 'name' => 'finished with the node address', 'shown' => array( 'ezap-message-finished' ),
                                                       'href' => '/index.php/news/item', 'display' => '' ),
            'finished without the node address' => array( 'name' => 'finished without the node address', 'shown' => array( 'ezap-message-finished' ),
                                                          'href' => '#', 'display' => 'none' ),
        ), $rows );
    }
}
