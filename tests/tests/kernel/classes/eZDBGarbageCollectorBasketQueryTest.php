<?php
/**
 * The basket clean-up query of eZDBGarbageCollector::collectBaskets() (cronjob basket_cleanup) for the databases other
 * than MySQL's single DELETE and Oracle: it must run on SQLite, which before 3.39 refuses RIGHT JOIN (the cronjob logged
 * "Query failed: RIGHT and FULL OUTER JOINs are not currently supported"), and find exactly the baskets without a
 * session. No database of the installation: the query is read from the source and run on an in-memory SQLite.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class eZDBGarbageCollectorBasketQueryTest extends PHPUnit\Framework\TestCase
{
    private function query()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/kernel/classes/ezdbgarbagecollector.php' );
        $this->assertSame( 1, preg_match( '/\$sql = "(SELECT ezbasket\.id AS id.*?)";/s', $source, $m ), 'the portable basket query' );
        return $m[1];
    }

    public function testTheQueryUsesNoRightJoin()
    {
        $this->assertStringNotContainsStringIgnoringCase( 'RIGHT JOIN', $this->query() );
    }

    public function testItFindsExactlyTheBasketsWithoutASession()
    {
        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'pdo_sqlite is not available' );
        $db = new PDO( 'sqlite::memory:' );
        $db->exec( 'CREATE TABLE ezsession ( session_key TEXT PRIMARY KEY )' );
        $db->exec( 'CREATE TABLE ezbasket ( id INTEGER PRIMARY KEY, session_id TEXT, productcollection_id INTEGER )' );
        $db->exec( "INSERT INTO ezsession VALUES ( 'alive' )" );
        $db->exec( "INSERT INTO ezbasket VALUES ( 1, 'alive', 10 ), ( 2, 'gone', 20 ), ( 3, 'also-gone', 30 )" );
        $rows = $db->query( $this->query() )->fetchAll( PDO::FETCH_ASSOC );
        usort( $rows, function ( $a, $b ) { return $a['id'] <=> $b['id']; } );
        $this->assertSame( array( array( 'id' => 2, 'productcollection_id' => 20 ), array( 'id' => 3, 'productcollection_id' => 30 ) ), $rows );
    }
}
