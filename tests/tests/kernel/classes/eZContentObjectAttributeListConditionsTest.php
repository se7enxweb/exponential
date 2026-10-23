<?php
/**
 * eZContentObject::attributeListConditions(), the WHERE fragments of fillNodeListAttributes(). The method used to join
 * one "( version AND contentobject_id AND language_code )" term per object with OR into a single query, and with a few
 * hundred objects SQLite refused it ("Expression tree is too large (maximum depth 1000)"), after which the attributes of
 * every object on the page were missing. No database of the installation: the fragments are run on an in-memory SQLite.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class eZContentObjectAttributeListConditionsTest extends PHPUnit\Framework\TestCase
{
    /**
     * 1500 objects in mixed versions and two languages.
     */
    private function keys()
    {
        $keys = array();
        for ( $id = 1; $id <= 1500; $id++ )
        {
            $keys[] = array( 'id' => $id, 'version' => 1 + $id % 7, 'language' => $id % 3 ? 'eng-GB' : 'ger-DE' );
        }
        return $keys;
    }

    public function testEveryObjectIsInExactlyOneFragmentAndFragmentsStaySmall()
    {
        $fragments = eZContentObject::attributeListConditions( $this->keys() );
        $this->assertNotEmpty( $fragments );
        $seen = array();
        foreach ( $fragments as $fragment )
        {
            $this->assertLessThanOrEqual( 99, substr_count( $fragment, ' OR ' ) );
            preg_match_all( '/IN \( ([0-9, ]+) \)/', $fragment, $m );
            foreach ( $m[1] as $list )
            {
                $ids = explode( ', ', $list );
                $this->assertLessThanOrEqual( 500, count( $ids ) );
                foreach ( $ids as $id )
                    $seen[] = (int) $id;
            }
        }
        sort( $seen );
        $this->assertSame( range( 1, 1500 ), $seen );
    }

    public function testLanguageCodesAreQuoted()
    {
        $fragments = eZContentObject::attributeListConditions( array( array( 'id' => 5, 'version' => 2, 'language' => "x'y" ) ) );
        $this->assertSame( array( "( ezcontentobject_attribute.version = 2 AND ezcontentobject_attribute.language_code = 'x''y' AND " .
            "ezcontentobject_attribute.contentobject_id IN ( 5 ) )" ), $fragments );
    }

    public function testTheFragmentsSelectExactlyTheRowsOfTheOldConditionOnSQLite()
    {
        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'pdo_sqlite is not available' );
        $db = new PDO( 'sqlite::memory:' );
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $db->exec( 'CREATE TABLE ezcontentobject_attribute ( id INTEGER PRIMARY KEY, contentobject_id INTEGER, version INTEGER, language_code TEXT )' );
        $insert = $db->prepare( 'INSERT INTO ezcontentobject_attribute ( contentobject_id, version, language_code ) VALUES ( ?, ?, ? )' );
        $expected = array();
        $db->beginTransaction();
        foreach ( $this->keys() as $key )
        {
            // The wanted row, plus rows in another version and another language that must not be selected.
            $insert->execute( array( $key['id'], $key['version'], $key['language'] ) );
            $expected[] = (int) $db->lastInsertId();
            $insert->execute( array( $key['id'], $key['version'] + 1, $key['language'] ) );
            $insert->execute( array( $key['id'], $key['version'], 'nor-NO' ) );
        }
        $db->commit();

        $found = array();
        foreach ( eZContentObject::attributeListConditions( $this->keys() ) as $where )
        {
            foreach ( $db->query( "SELECT id FROM ezcontentobject_attribute WHERE ( $where )" )->fetchAll( PDO::FETCH_COLUMN ) as $id )
                $found[] = (int) $id;
        }
        sort( $found );
        $this->assertSame( $expected, $found );
    }
}
