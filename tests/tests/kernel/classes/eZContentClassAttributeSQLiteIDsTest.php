<?php
/**
 * A class attribute added to a class with objects gets object attribute rows
 * with an id on SQLite too.
 *
 * ezcontentobject_attribute's key is ( id, version ); SQLite only numbers a
 * row by itself for a single INTEGER key, so the batch INSERT ... SELECT of
 * eZContentClassAttribute::initializeObjectAttributes() left the id NULL.
 * Every page showing such an object then logged
 * "eZImageAliasHandler::storeDOMTree: Invalid objectAttribute: id = version = N".
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

// The class of this checkout, not one an autoloader may map elsewhere.
require_once __DIR__ . '/../../../../kernel/classes/ezcontentclassattribute.php';

class eZContentClassAttributeSQLiteIDsTest extends PHPUnit\Framework\TestCase
{
    /** @var SQLite3 */
    private $sqlite;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'SQLite3 extension is not loaded' );

        $this->sqlite = new SQLite3( ':memory:' );
        // The table as the SQLite installations have it.
        $this->sqlite->exec( "CREATE TABLE ezcontentobject_attribute (
            attribute_original_id INTEGER(11) DEFAULT '0',
            contentclassattribute_id INTEGER(11) NOT NULL DEFAULT '0',
            contentobject_id INTEGER(11) NOT NULL DEFAULT '0',
            data_float double DEFAULT NULL,
            data_int INTEGER(11) DEFAULT NULL,
            data_text longtext,
            data_type_string varchar(50) DEFAULT '',
            id INTEGER,
            language_code varchar(20) NOT NULL DEFAULT '',
            language_id bigint(20) NOT NULL DEFAULT '0',
            sort_key_int INTEGER(11) NOT NULL DEFAULT '0',
            sort_key_string varchar(255) NOT NULL DEFAULT '',
            version INTEGER(11) NOT NULL DEFAULT '0',
            PRIMARY KEY ( id, version ) )" );
        $this->sqlite->exec( "CREATE TABLE ezcontentobject ( id INTEGER PRIMARY KEY, contentclass_id INTEGER )" );

        // Two objects of class 7: object 10 with versions 1-3 in English,
        // object 11 with version 1 in English and German.
        $this->sqlite->exec( "INSERT INTO ezcontentobject VALUES ( 10, 7 ), ( 11, 7 )" );
        $rows = array( array( 100, 10, 1, 'eng-US' ), array( 100, 10, 2, 'eng-US' ), array( 100, 10, 3, 'eng-US' ),
                       array( 200, 11, 1, 'eng-US' ), array( 201, 11, 1, 'ger-DE' ) );
        foreach ( $rows as $r )
        {
            $this->sqlite->exec( "INSERT INTO ezcontentobject_attribute ( id, contentobject_id, version, language_code, contentclassattribute_id, language_id )
                                  VALUES ( {$r[0]}, {$r[1]}, {$r[2]}, '{$r[3]}', 50, 2 )" );
        }
    }

    /** The batch insert of initializeObjectAttributes() for SQLite, for class attribute 60. */
    private function batchInsert()
    {
        $this->sqlite->exec( "INSERT OR REPLACE INTO ezcontentobject_attribute( contentobject_id, version, contentclassattribute_id, data_type_string, language_code, language_id )
                              SELECT a.contentobject_id, a.version, 60, 'ezimage', a.language_code, MAX(a.language_id)
                              FROM ezcontentobject_attribute a, ezcontentobject o
                              WHERE o.id = a.contentobject_id AND o.contentclass_id = 7
                              GROUP BY contentobject_id, version, language_code" );
    }

    private function db()
    {
        $sqlite = $this->sqlite;
        return new class( $sqlite ) {
            private $s;
            public function __construct( $s ) { $this->s = $s; }
            public function arrayQuery( $sql )
            {
                $result = $this->s->query( $sql );
                $rows = array();
                while ( $result && ( $row = $result->fetchArray( SQLITE3_ASSOC ) ) )
                    $rows[] = $row;
                return $rows;
            }
            public function query( $sql ) { return $this->s->exec( $sql ); }
            public function escapeString( $s ) { return SQLite3::escapeString( (string)$s ); }
        };
    }

    public function testBatchInsertAloneLeavesTheIdNull()
    {
        $this->batchInsert();
        $this->assertSame( 5, (int)$this->sqlite->querySingle( "SELECT COUNT(*) FROM ezcontentobject_attribute WHERE contentclassattribute_id = 60 AND id IS NULL" ) );
    }

    public function testEveryNewRowGetsAnIdSharedByItsVersions()
    {
        $this->batchInsert();
        $given = eZContentClassAttribute::assignMissingSQLiteObjectAttributeIDs( $this->db(), 60 );

        // object 10 eng-US, object 11 eng-US, object 11 ger-DE
        $this->assertSame( 3, $given );
        $this->assertSame( 0, (int)$this->sqlite->querySingle( "SELECT COUNT(*) FROM ezcontentobject_attribute WHERE id IS NULL" ) );

        $ids = $this->db()->arrayQuery( "SELECT contentobject_id, language_code, COUNT(DISTINCT id) AS ids, MIN(id) AS id
                                         FROM ezcontentobject_attribute WHERE contentclassattribute_id = 60
                                         GROUP BY contentobject_id, language_code ORDER BY contentobject_id, language_code" );
        $this->assertCount( 3, $ids );
        foreach ( $ids as $row )
            $this->assertSame( 1, (int)$row['ids'], 'all versions of one object and language share one id' );
        $this->assertSame( array( 202, 203, 204 ), array_map( 'intval', array_column( $ids, 'id' ) ), 'new ids follow the highest id' );

        // and the rows can be fetched back by ( id, version ), as eZContentObjectAttribute::fetch() does
        $this->assertSame( 60, (int)$this->sqlite->querySingle( "SELECT contentclassattribute_id FROM ezcontentobject_attribute WHERE id = 202 AND version = 3" ) );
    }

    public function testNothingToDoGivesNoId()
    {
        $this->assertSame( 0, eZContentClassAttribute::assignMissingSQLiteObjectAttributeIDs( $this->db(), 60 ) );
        $this->assertSame( 200 + 1, (int)$this->sqlite->querySingle( "SELECT MAX(id) FROM ezcontentobject_attribute" ) );
    }
}
