<?php
/**
 * The field sources of the module table wizard (expDataTableFieldSources, doc/eng-us/features/6.0/data-tables.md,
 * "The module table wizard"). Pure: no database.
 *
 *  FS-01 - parseList(): types, lengths, required and unique, comments and empty lines left out
 *  FS-02 - parseList(): an unknown type becomes string with an error naming the line; a line without a name fails
 *  FS-03 - normalizeField(): the aliases of types, lengths bounded, string 255 by default, labels
 *  FS-04 - columnName(): lower case, umlauts transliterated, a leading digit prefixed, at most 30 characters
 *  FS-05 - toList() and parseList() give the same fields back
 *  FS-06 - all(): the kernel's sources class and table, each an expDataTableFieldSource
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDataTableTestLoader.php';

class expDataTableFieldSourcesTest extends PHPUnit\Framework\TestCase
{
    /** FS-01 */
    public function testTheListGivesTypedFields()
    {
        $errors = array();
        $fields = expDataTableFieldSources::parseList( "# a comment\ntitle string(120) required\n\nremote_id string(64) unique\nprice float\n"
                                                     . "active boolean\npublished date\nseen datetime\nnotes text  # trailing comment\nextra json\n"
                                                     . "article object\nplace node\nowner user\ncount integer required unique", $errors );
        $this->assertSame( array(), $errors );
        $byName = array_column( $fields, null, 'name' );
        $this->assertSame( array( 'title', 'remote_id', 'price', 'active', 'published', 'seen', 'notes', 'extra', 'article', 'place', 'owner', 'count' ), array_keys( $byName ) );
        $this->assertSame( array( 'name' => 'title', 'label' => 'title', 'type' => 'string', 'length' => 120, 'required' => true, 'unique' => false ), $byName['title'] );
        $this->assertSame( 64, $byName['remote_id']['length'] );
        $this->assertTrue( $byName['remote_id']['unique'] );
        $this->assertSame( 'float', $byName['price']['type'] );
        $this->assertSame( 0, $byName['price']['length'] );
        $this->assertSame( 'text', $byName['notes']['type'] );
        $this->assertSame( array( 'object', 'node', 'user' ), array( $byName['article']['type'], $byName['place']['type'], $byName['owner']['type'] ) );
        $this->assertTrue( $byName['count']['required'] && $byName['count']['unique'] );
        $this->assertSame( 0, $byName['seen']['length'], 'a datetime has no length' );
    }

    /** FS-02 */
    public function testAnUnknownTypeIsStringWithAnError()
    {
        $errors = array();
        $fields = expDataTableFieldSources::parseList( "colour hexcolor\n# skip\n!!! integer", $errors );
        $this->assertCount( 1, $fields );
        $this->assertSame( 'string', $fields[0]['type'] );
        $this->assertSame( 255, $fields[0]['length'] );
        $this->assertCount( 2, $errors );
        $this->assertStringContainsString( 'line 1', $errors[0] );
        $this->assertStringContainsString( "unknown type 'hexcolor'", $errors[0] );
        $this->assertStringContainsString( 'line 3', $errors[1] );
        $this->assertStringContainsString( 'no field name', $errors[1] );
        // the words string, varchar and char are no error
        $errors = array();
        expDataTableFieldSources::parseList( "a varchar(10)\nb char(2)\nc string", $errors );
        $this->assertSame( array(), $errors );
    }

    /** FS-03 */
    public function testNormalizeFieldKnowsTheAliases()
    {
        $aliases = array( 'int' => 'integer', 'bool' => 'boolean', 'double' => 'float', 'decimal' => 'float', 'number' => 'float',
                          'varchar' => 'string', 'char' => 'string', 'longtext' => 'text', 'timestamp' => 'datetime',
                          'object_relation' => 'object', 'node_relation' => 'node', 'user_relation' => 'user', 'NODE' => 'node',
                          'nonsense' => 'string' );
        foreach ( $aliases as $alias => $type )
            $this->assertSame( $type, expDataTableFieldSources::normalizeField( array( 'name' => 'x', 'type' => $alias ) )['type'], $alias );
        $this->assertNull( expDataTableFieldSources::normalizeField( array( 'name' => '!!!' ) ) );
        $this->assertNull( expDataTableFieldSources::normalizeField( array( 'type' => 'string' ) ) );
        $this->assertSame( 4000, expDataTableFieldSources::normalizeField( array( 'name' => 'x', 'length' => 99999 ) )['length'] );
        $this->assertSame( 1, expDataTableFieldSources::normalizeField( array( 'name' => 'x', 'length' => -5 ) )['length'] );
        $this->assertSame( 255, expDataTableFieldSources::normalizeField( array( 'name' => 'x' ) )['length'] );
        $field = expDataTableFieldSources::normalizeField( array( 'name' => 'Price (EUR)', 'label' => '  Price  ', 'required' => 1 ) );
        $this->assertSame( 'price_eur', $field['name'] );
        $this->assertSame( 'Price', $field['label'] );
        $this->assertTrue( $field['required'] );
        $this->assertFalse( $field['unique'] );
        $this->assertSame( 'price_eur', expDataTableFieldSources::normalizeField( array( 'name' => 'Price (EUR)', 'label' => '' ) )['label'] );
    }

    /** FS-04 */
    public function testColumnNames()
    {
        // iconv's transliteration of ß and umlauts differs between C libraries (ss, ?, '); the result is a column name
        $this->assertMatchesRegularExpression( '/^gro[a-z_]*stra[a-z_]*e$/', expDataTableFieldSources::columnName( 'Große Straße' ) );
        $this->assertSame( 'uber_uns', expDataTableFieldSources::columnName( 'Über uns' ) );
        $this->assertSame( 'f_2nd_price', expDataTableFieldSources::columnName( '2nd price' ) );
        $this->assertSame( 'a_b_c', expDataTableFieldSources::columnName( '  A--B  C ' ) );
        $this->assertSame( '', expDataTableFieldSources::columnName( '---' ) );
        $long = expDataTableFieldSources::columnName( str_repeat( 'abcdef', 10 ) );
        $this->assertSame( 30, strlen( $long ) );
        $this->assertMatchesRegularExpression( '/^[a-z][a-z0-9_]{0,29}$/', expDataTableFieldSources::columnName( 'Ünïcødé 99 Wörds!' ) );
    }

    /** FS-05 */
    public function testTheListRoundTrips()
    {
        $text = "title string(120) required\nremote_id string(64) unique\nprice float\nnotes text\nowner user required\ncode string";
        $errors = array();
        $fields = expDataTableFieldSources::parseList( $text, $errors );
        $this->assertSame( $text, expDataTableFieldSources::toList( $fields ) );
        $this->assertSame( $fields, expDataTableFieldSources::parseList( expDataTableFieldSources::toList( $fields ) ) );
    }

    /** FS-06 */
    public function testTheKernelSources()
    {
        $this->assertInstanceOf( 'expDataTableFieldSource', new expDataTableClassFieldSource() );
        $this->assertInstanceOf( 'expDataTableFieldSource', new expDataTableTableFieldSource() );
        $this->assertSame( array(), ( new expDataTableTableFieldSource() )->dataTableReferences() );
        $this->assertSame( 'the table site:x', ( new expDataTableTableFieldSource() )->dataTableSourceLabel( array( 'ref' => 'site:x' ) ) );
        $this->assertSame( 'the content class article', ( new expDataTableClassFieldSource() )->dataTableSourceLabel( array( 'ref' => 'article' ) ) );
        try
        {
            ( new expDataTableTableFieldSource() )->dataTableFields( array( 'ref' => 'site:bad name' ) );
            $this->fail( 'a bad table name is refused' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'connection:table', $e->getMessage() );
        }
        foreach ( expDataTableClassFieldSource::DATATYPES as $datatype => $type )
            $this->assertContains( $type[0], expDataTableFieldSources::TYPES, $datatype );
    }
}
