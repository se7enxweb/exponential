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
 *  BD-03  the roles, policies, limitations, limitation values and role assignments are the same rows in the three
 *         files (the Editor role has uiswitch/language, uiswitch/design, oauth/authorize and exphelp/read in each)
 *  BD-04  the search index (ezsearch_word, ezsearch_object_word_link) is the same in the three files, every word's
 *         object_count is the number of objects linking it, and object 1 is indexed with the words of its texts
 *         in an unbroken placement chain
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

    /**
     * The rows of an INSERT INTO <table> ( ... ) VALUES ( ... ); statement list, as assoc arrays of strings (NULL as
     * null). $backslash: the file escapes with backslashes (the common clean data) besides doubled quotes.
     */
    private static function sqlRows( $sql, $table, $backslash )
    {
        $rows = array();
        $offset = 0;
        $re = '/INSERT INTO ' . preg_quote( $table, '/' ) . '\s*\(([^)]*)\)\s*VALUES\s*\(/';
        while ( preg_match( $re, $sql, $m, PREG_OFFSET_CAPTURE, $offset ) )
        {
            $columns = array_map( 'trim', explode( ',', $m[1][0] ) );
            $values = array();
            $current = '';
            $inString = false;
            $isString = false;
            for ( $i = $m[0][1] + strlen( $m[0][0] ), $n = strlen( $sql ); $i < $n; $i++ )
            {
                $c = $sql[$i];
                if ( $inString )
                {
                    if ( $backslash && $c === '\\' )
                    {
                        $e = $sql[++$i];
                        $current .= $e === 'n' ? "\n" : $e;
                    }
                    elseif ( $c === "'" && isset( $sql[$i + 1] ) && $sql[$i + 1] === "'" )
                    {
                        $current .= "'";
                        $i++;
                    }
                    elseif ( $c === "'" )
                        $inString = false;
                    else
                        $current .= $c;
                    continue;
                }
                if ( $c === "'" )
                {
                    $inString = $isString = true;
                    $current = '';
                }
                elseif ( $c === ',' || $c === ')' )
                {
                    $value = $isString ? $current : trim( $current );
                    $values[] = !$isString && strtoupper( $value ) === 'NULL' ? null : $value;
                    $current = '';
                    $isString = false;
                    if ( $c === ')' )
                        break;
                }
                elseif ( !$isString )
                    $current .= $c;
            }
            $row = array_combine( $columns, $values );
            ksort( $row );
            $rows[] = $row;
            $offset = $i;
        }
        return $rows;
    }

    private static function dbaRows( array $data, $table )
    {
        $rows = array();
        foreach ( self::rows( $data, $table ) as $row )
        {
            $row = array_map( function ( $v ) { return $v === null ? null : (string)$v; }, $row );
            ksort( $row );
            $rows[] = $row;
        }
        return $rows;
    }

    private static function byId( array $rows )
    {
        $out = array();
        foreach ( $rows as $row )
            $out[$row['id']] = $row;
        ksort( $out );
        return $out;
    }

    /** BD-03 and BD-04: the tables the three files carry row for row. */
    public static function sameRowsProvider()
    {
        $tables = array( 'ezrole', 'ezpolicy', 'ezpolicy_limitation', 'ezpolicy_limitation_value', 'ezuser_role', 'ezsearch_word', 'ezsearch_object_word_link' );
        $cases = array();
        foreach ( $tables as $table )
        {
            $cases["$table common"] = array( $table, 'kernel/sql/common/cleandata.sql', true );
            $cases["$table sqlite"] = array( $table, 'kernel/sql/sqlite/cleandata.sql', false );
        }
        return $cases;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('sameRowsProvider')]
    public function testTheCleanDataCarriesTheRowsOfTheDba( $table, $file, $backslash )
    {
        $dba = self::byId( self::dbaRows( self::dba(), $table ) );
        $sql = self::byId( self::sqlRows( (string)file_get_contents( self::root() . '/' . $file ), $table, $backslash ) );
        $this->assertNotEmpty( $dba, $table );
        $this->assertSame( array_keys( $dba ), array_keys( $sql ), "$file: the $table ids of share/db_data.dba" );
        $this->assertSame( $dba, $sql, "$file: the $table rows of share/db_data.dba" );
    }

    public function testTheEditorRoleHasTheHelpOAuthAndInterfacePoliciesInTheDba()
    {
        $editor = array();
        foreach ( self::rows( self::dba(), 'ezpolicy' ) as $policy )
        {
            if ( $policy['role_id'] === '3' )
                $editor[] = $policy['module_name'] . '/' . $policy['function_name'];
        }
        foreach ( array( 'uiswitch/language', 'uiswitch/design', 'oauth/authorize', 'exphelp/read' ) as $policy )
            $this->assertContains( $policy, $editor );
    }

    public function testTheSearchIndexIsConsistentAndIndexesTheTopObjectText()
    {
        $data = self::dba();
        $words = array();
        foreach ( self::rows( $data, 'ezsearch_word' ) as $word )
            $words[$word['id']] = $word;
        $objectsOfWord = array();
        $top = array();
        foreach ( self::rows( $data, 'ezsearch_object_word_link' ) as $link )
        {
            $this->assertArrayHasKey( $link['word_id'], $words, 'every link names a word' );
            $objectsOfWord[$link['word_id']][$link['contentobject_id']] = true;
            if ( $link['contentobject_id'] === '1' )
                $top[(int)$link['placement']] = $link;
        }
        foreach ( $words as $id => $word )
            $this->assertSame( (int)$word['object_count'], isset( $objectsOfWord[$id] ) ? count( $objectsOfWord[$id] ) : 0, "object_count of '{$word['word']}'" );

        ksort( $top );
        $this->assertSame( range( 0, count( $top ) - 1 ), array_keys( $top ), 'the placements of object 1 run from 0 without a gap' );
        // eZSearchEngine::addObject() writes the words in slices of 1000 (indexWords()), and each slice starts
        // without a previous word and ends without a next one.
        $previous = '0';
        foreach ( $top as $placement => $link )
        {
            $sliceEnd = ( $placement + 1 ) % 1000 === 0;
            $this->assertSame( $placement % 1000 === 0 ? '0' : $previous, $link['prev_word_id'], "placement $placement: previous word" );
            $this->assertSame( isset( $top[$placement + 1] ) && !$sliceEnd ? $top[$placement + 1]['word_id'] : '0', $link['next_word_id'], "placement $placement: next word" );
            $previous = $link['word_id'];
        }

        // Each indexed word of the two texts occurs in the text itself (lowercased, tags and entities taken out),
        // and both texts are indexed.
        $plain = array();
        foreach ( self::rows( $data, 'ezcontentobject_attribute' ) as $attribute )
        {
            if ( $attribute['contentobject_id'] === '1' && $attribute['data_type_string'] === 'ezxmltext' )
                // with the entities as written too: the indexer escapes the text again and keeps "gt" of "&gt;"
                $plain[$attribute['contentclassattribute_id']] = mb_strtolower( strip_tags( str_replace( '><', '> <', $attribute['data_text'] ) ) );
        }
        $this->assertCount( 2, $plain );
        // letters and digits only: the search transformation changes some punctuation ("do?" is indexed as "do.")
        $lettersOf = function ( $text ) { return preg_replace( '/[^\p{L}\p{N}]+/u', '', $text ); };
        $seen = array();
        foreach ( $top as $placement => $link )
        {
            if ( !isset( $plain[$link['contentclass_attribute_id']] ) )
                continue;
            $seen[$link['contentclass_attribute_id']] = true;
            $this->assertStringContainsString( $lettersOf( $words[$link['word_id']]['word'] ), $lettersOf( $plain[$link['contentclass_attribute_id']] ), "placement $placement: '{$words[$link['word_id']]['word']}' is a word of the text" );
        }
        $this->assertCount( 2, $seen, 'both texts are indexed' );
    }
}
