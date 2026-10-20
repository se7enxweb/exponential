<?php
/**
 * The base data of a new installation (share/db_data.dba, which the setup wizard and the kickstarter load, and the
 * clean data files kernel/sql/common/cleandata.sql and kernel/sql/sqlite/cleandata.sql kept with it). No database:
 * the files are read.
 *
 *  BD-01  the Anonymous role (1) has user/register without a limitation, so visitors can register on a new
 *         installation, which takes user/register out of PolicyOmitList[]; policy ids are unique
 *  BD-02  the three files carry the same short_description and description of the top object (node 2), as
 *         well-formed XML whose links are written with href (no url_id, node_id or object_id: a new installation
 *         has none of those rows)
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expBaseDataSeedTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expBaseDataSeedTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 4 );
    }

    private static function dba()
    {
        $data = null;
        include self::root() . '/share/db_data.dba';
        return $data;
    }

    private static function rows( array $data, $table )
    {
        $out = array();
        foreach ( $data[$table]['rows'] as $row )
            $out[] = array_combine( $data[$table]['fields'], $row );
        return $out;
    }

    public static function cleanDataProvider()
    {
        return array( 'common' => array( 'kernel/sql/common/cleandata.sql' ), 'sqlite' => array( 'kernel/sql/sqlite/cleandata.sql' ) );
    }

    public function testTheDbaGivesTheAnonymousRoleUserRegister()
    {
        $data = self::dba();
        $this->assertIsArray( $data );
        $register = array();
        $ids = array();
        foreach ( self::rows( $data, 'ezpolicy' ) as $policy )
        {
            $ids[] = $policy['id'];
            if ( $policy['module_name'] === 'user' && $policy['function_name'] === 'register' )
                $register[] = $policy;
        }
        $this->assertCount( 1, $register );
        $this->assertSame( '1', $register[0]['role_id'], 'user/register belongs to the Anonymous role' );
        $this->assertSame( count( $ids ), count( array_unique( $ids ) ), 'policy ids are unique' );
        foreach ( self::rows( $data, 'ezpolicy_limitation' ) as $limitation )
            $this->assertNotSame( $register[0]['id'], $limitation['policy_id'], 'user/register has no limitation' );
        $anonymous = array_filter( self::rows( $data, 'ezrole' ), function ( $role ) { return $role['id'] === '1'; } );
        $this->assertSame( 'Anonymous', array_values( $anonymous )[0]['name'] );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('cleanDataProvider')]
    public function testTheCleanDataGivesTheAnonymousRoleUserRegister( $file )
    {
        $sql = (string)file_get_contents( self::root() . '/' . $file );
        $this->assertMatchesRegularExpression( "/\) VALUES \(\s*1,\s*0,\s*'Anonymous',/", $sql );
        preg_match_all( "/INSERT INTO ezpolicy \([^)]*\) VALUES \(\s*'register',\s*(\d+),\s*'user',\s*0,\s*(\d+)\s*\);/", $sql, $m );
        $this->assertSame( array( '1' ), $m[2], "$file: one user/register, for the Anonymous role" );
        $this->assertDoesNotMatchRegularExpression( "/INSERT INTO ezpolicy_limitation \([^)]*\) VALUES \(\s*\d+,\s*'[^']*',\s*{$m[1][0]}\s*\);/", $sql );
        preg_match_all( "/INSERT INTO ezpolicy \([^)]*\) VALUES \(\s*'[^']*',\s*(\d+),/", $sql, $ids );
        $this->assertSame( count( $ids[1] ), count( array_unique( $ids[1] ) ), "$file: policy ids are unique" );
    }

    public function testTheTopObjectTextIsTheSameInEveryFileAndCarriesNoRowIds()
    {
        $data = self::dba();
        $classAttributes = array();
        foreach ( self::rows( $data, 'ezcontentclass_attribute' ) as $attribute )
            $classAttributes[$attribute['id']] = $attribute['identifier'];
        $texts = array();
        foreach ( self::rows( $data, 'ezcontentobject_attribute' ) as $attribute )
        {
            if ( $attribute['contentobject_id'] === '1' && $attribute['data_type_string'] === 'ezxmltext' )
                $texts[$classAttributes[$attribute['contentclassattribute_id']]] = $attribute['data_text'];
        }
        ksort( $texts );
        $this->assertSame( array( 'description', 'short_description' ), array_keys( $texts ) );

        $common = (string)file_get_contents( self::root() . '/kernel/sql/common/cleandata.sql' );
        $sqlite = (string)file_get_contents( self::root() . '/kernel/sql/sqlite/cleandata.sql' );
        foreach ( $texts as $identifier => $text )
        {
            $dom = new DOMDocument();
            $this->assertTrue( $dom->loadXML( $text ), "$identifier is well-formed XML" );
            $this->assertGreaterThan( 0, $dom->getElementsByTagName( 'link' )->length, "$identifier has links" );
            foreach ( $dom->getElementsByTagName( 'link' ) as $link )
            {
                $this->assertNotSame( '', $link->getAttribute( 'href' ), "$identifier: a link has an href" );
                foreach ( array( 'url_id', 'node_id', 'object_id' ) as $id )
                    $this->assertFalse( $link->hasAttribute( $id ), "$identifier: no $id" );
            }
            $this->assertSame( 1, substr_count( $sqlite, "'" . str_replace( "'", "''", $text ) . "'" ), "sqlite cleandata.sql: $identifier" );
            $this->assertSame( 1, substr_count( $common, "'" . str_replace( array( '\\', "'", "\n" ), array( '\\\\', "''", '\n' ), $text ) . "'" ), "common cleandata.sql: $identifier" );
        }
    }
}
