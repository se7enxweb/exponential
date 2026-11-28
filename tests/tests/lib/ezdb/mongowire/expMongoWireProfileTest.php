<?php
/**
 * The profiles of the databases of the MongoDB wire protocol (no server needed):
 *
 *  MW-01 - every profile is there, found by its name and its aliases, with the keys the driver reads
 *  MW-02 - forDriver(): the profile of a driver class, a configured profile wins
 *  MW-03 - bitwiseAggregation(): MongoDB from 6.3 on, never on FerretDB, DocumentDB and Cosmos DB
 *  MW-04 - uriOptions(): TLS, retryWrites=false, the replica set, settings over the profile, URIOptions
 *  MW-05 - emulateBitwise(): $bitAnd, $bitOr, $bitXor and $bitNot with a constant give the same numbers as PHP
 *  MW-06 - emulateBitwise() leaves two fields alone; hasBitwise(), hasCorrelatedLookup()
 *  MW-07 - expMongoDB::connectionUri() with options, sortIndexes() and parseCreateTableColumns()
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('mongowire')]
class expMongoWireProfileTest extends PHPUnit\Framework\TestCase
{
    public function testEveryProfileIsThere()
    {
        $all = expMongoWireProfile::all();
        $this->assertSame( array( 'mongodb', 'ferretdb', 'documentdb', 'cosmosdb', 'cosmosdbvcore' ), array_keys( $all ) );
        foreach ( $all as $name => $profile )
        {
            $this->assertSame( $name, $profile['name'] );
            foreach ( array( 'label', 'alias', 'aliases', 'driver', 'port', 'tls', 'retry_writes', 'bitwise_aggregation',
                             'lookup_let', 'index_every_field', 'level', 'live_tested', 'notes' ) as $key )
                $this->assertArrayHasKey( $key, $profile, "$name: $key" );
            $this->assertContains( $profile['level'], array( 'site', 'datatable' ) );
            $this->assertTrue( class_exists( $profile['driver'] ), "$name: driver {$profile['driver']}" );
            $this->assertSame( $name, expMongoWireProfile::get( $profile['alias'] )['name'] );
            foreach ( $profile['aliases'] as $alias )
                $this->assertSame( $name, expMongoWireProfile::get( $alias )['name'], "$name: alias $alias" );
        }
        $this->assertSame( 'mongodb', expMongoWireProfile::get( '' )['name'] );
        $this->assertSame( 'mongodb', expMongoWireProfile::get( 'nosuchdatabase' )['name'] );
        $this->assertSame( 'cosmosdb', expMongoWireProfile::get( ' Cosmos ' )['name'] );
    }

    public function testForDriver()
    {
        $this->assertSame( 'ferretdb', expMongoWireProfile::forDriver( 'expFerretDB' )['name'] );
        $this->assertSame( 'documentdb', expMongoWireProfile::forDriver( 'EXPDOCUMENTDB' )['name'] );
        $this->assertSame( 'cosmosdbvcore', expMongoWireProfile::forDriver( 'expCosmosVCoreDB' )['name'] );
        $this->assertSame( 'mongodb', expMongoWireProfile::forDriver( 'expMongoDB' )['name'] );
        $this->assertSame( 'cosmosdb', expMongoWireProfile::forDriver( 'expMongoDB', 'cosmosdb' )['name'] );
        $this->assertSame( 'ferretdb', expFerretDB::PROFILE );
        $this->assertSame( 'documentdb', expDocumentDB::PROFILE );
        $this->assertSame( 'cosmosdb', expCosmosMongoDB::PROFILE );
        $this->assertSame( 'mongodb', expMongoDB::PROFILE );
    }

    public function testBitwiseAggregation()
    {
        $mongo = expMongoWireProfile::get( 'mongodb' );
        $this->assertTrue( expMongoWireProfile::bitwiseAggregation( $mongo, '7.0.14' ) );
        $this->assertTrue( expMongoWireProfile::bitwiseAggregation( $mongo, '6.3.0' ) );
        $this->assertFalse( expMongoWireProfile::bitwiseAggregation( $mongo, '6.0.19' ) );
        $this->assertFalse( expMongoWireProfile::bitwiseAggregation( $mongo, '4.4.29' ) );
        $this->assertFalse( expMongoWireProfile::bitwiseAggregation( $mongo, '' ) );
        foreach ( array( 'ferretdb', 'documentdb', 'cosmosdb', 'cosmosdbvcore' ) as $name )
            $this->assertFalse( expMongoWireProfile::bitwiseAggregation( expMongoWireProfile::get( $name ), '7.0.0' ), $name );
    }

    public function testUriOptions()
    {
        $this->assertSame( array(), expMongoWireProfile::uriOptions( expMongoWireProfile::get( 'mongodb' ) ) );
        $this->assertSame( array( 'retryWrites' => 'false' ), expMongoWireProfile::uriOptions( expMongoWireProfile::get( 'ferretdb' ) ) );
        $this->assertSame( array( 'tls' => 'true', 'tlsCAFile' => '/etc/ssl/global-bundle.pem', 'retryWrites' => 'false',
                                  'replicaSet' => 'rs0', 'readPreference' => 'secondaryPreferred' ),
            expMongoWireProfile::uriOptions( expMongoWireProfile::get( 'documentdb' ),
                array( 'TLSCAFile' => '/etc/ssl/global-bundle.pem', 'ReadPreference' => 'secondaryPreferred' ) ) );
        $this->assertSame( array( 'retryWrites' => 'false' ),
            expMongoWireProfile::uriOptions( expMongoWireProfile::get( 'documentdb' ), array( 'TLS' => 'disabled', 'TLSCAFile' => '/x', 'ReplicaSet' => 'none' ) ) );
        $this->assertSame( array( 'tls' => 'true', 'maxIdleTimeMS' => '120000', 'appName' => 'exp' ),
            expMongoWireProfile::uriOptions( expMongoWireProfile::get( 'cosmosdb' ),
                array( 'RetryWrites' => 'enabled', 'URIOptions' => array( 'maxIdleTimeMS=120000', 'appName' => 'exp', 'bad key=1', 'novalue' ) ) ) );
    }

    /**
     * Evaluates the arithmetic expressions emulateBitwise() writes, as the server does.
     */
    private static function evaluate( $node, $x )
    {
        if ( is_int( $node ) )
            return $node;
        if ( $node === '$x' )
            return $x;
        $operator = key( $node );
        $args = current( $node );
        $values = array();
        foreach ( (array)( is_array( $args ) && array_keys( $args ) === range( 0, count( $args ) - 1 ) ? $args : array( $args ) ) as $arg )
            $values[] = self::evaluate( $arg, $x );
        switch ( $operator )
        {
            case '$add': return array_sum( $values );
            case '$subtract': return $values[0] - $values[1];
            case '$multiply': return array_product( $values );
            case '$divide': return $values[0] / $values[1];
            case '$floor': return floor( $values[0] );
            case '$mod': return fmod( $values[0], $values[1] );
            case '$toLong': return (int)$values[0];
        }
        throw new RuntimeException( "unexpected operator $operator" );
    }

    public function testEmulatedBitwiseGivesTheSameNumbers()
    {
        $constants = array( 0, 1, 2, 3, 6, 7, 8, 255, 1 << 20, ( 1 << 30 ) + 5, -2, ~6 );
        $xs = array( 0, 1, 2, 3, 5, 6, 7, 8, 9, 254, 255, 256, 1023, ( 1 << 31 ) - 1, 1 << 40, ( 1 << 40 ) + 3 );
        foreach ( $constants as $c )
        {
            foreach ( $xs as $x )
            {
                $this->assertSame( $x & $c, (int)self::evaluate( expMongoWireProfile::emulateBitwise( array( '$bitAnd' => array( '$x', $c ) ) ), $x ), "$x & $c" );
                $this->assertSame( $x & $c, (int)self::evaluate( expMongoWireProfile::emulateBitwise( array( '$bitAnd' => array( $c, '$x' ) ) ), $x ), "$c & $x" );
                if ( $c >= 0 )
                {
                    $this->assertSame( $x | $c, (int)self::evaluate( expMongoWireProfile::emulateBitwise( array( '$bitOr' => array( '$x', $c ) ) ), $x ), "$x | $c" );
                    $this->assertSame( $x ^ $c, (int)self::evaluate( expMongoWireProfile::emulateBitwise( array( '$bitXor' => array( '$x', $c ) ) ), $x ), "$x ^ $c" );
                }
            }
        }
        foreach ( $xs as $x )
            $this->assertSame( ~$x, (int)self::evaluate( expMongoWireProfile::emulateBitwise( array( '$bitNot' => '$x' ) ), $x ), "~$x" );
        $this->assertSame( 6 & 3, expMongoWireProfile::emulateBitwise( array( '$bitAnd' => array( 6, 3 ) ) ) );
        $this->assertSame( ~1, expMongoWireProfile::emulateBitwise( array( '$bitNot' => 1 ) ) );
        // three operands fold left
        $three = expMongoWireProfile::emulateBitwise( array( '$bitAnd' => array( '$x', 7, 6 ) ) );
        $this->assertSame( 13 & 7 & 6, (int)self::evaluate( $three, 13 ) );
    }

    public function testEmulationInsidePipelinesAndWhatItLeavesAlone()
    {
        $pipeline = array(
            array( '$match' => array( '$expr' => array( '$gt' => array( array( '$bitAnd' => array( '$language_mask', 4 ) ), 0 ) ) ) ),
            array( '$count' => 'count' ),
        );
        $this->assertTrue( expMongoWireProfile::hasBitwise( $pipeline ) );
        $rewritten = expMongoWireProfile::emulateBitwise( $pipeline );
        $this->assertFalse( expMongoWireProfile::hasBitwise( $rewritten ) );
        $this->assertSame( array( '$count' => 'count' ), $rewritten[1] );
        $this->assertSame( array( '$toLong' => array( '$multiply' => array(
            array( '$mod' => array( array( '$floor' => array( '$divide' => array( '$language_mask', 4 ) ) ), 2 ) ), 4 ) ) ),
            $rewritten[0]['$match']['$expr']['$gt'][0] );
        // two fields: left for the server to refuse
        $fields = array( '$bitAnd' => array( '$a', '$b' ) );
        $this->assertSame( $fields, expMongoWireProfile::emulateBitwise( $fields ) );
        $this->assertFalse( expMongoWireProfile::hasBitwise( array( array( '$match' => array( 'lang_mask' => array( '$bitsAnySet' => 3 ) ) ) ) ) );
        $this->assertTrue( expMongoWireProfile::hasCorrelatedLookup( array( array( '$lookup' => array( 'from' => 't', 'let' => array( 'i' => '$id' ), 'pipeline' => array(), 'as' => 'o' ) ) ) ) );
        $this->assertFalse( expMongoWireProfile::hasCorrelatedLookup( array( array( '$lookup' => array( 'from' => 't', 'localField' => 'a', 'foreignField' => 'b', 'as' => 'o' ) ) ) ) );
    }

    public function testDriverHelpers()
    {
        $this->assertSame( 'mongodb://h:10255/db?tls=true&retryWrites=false',
            expMongoDB::connectionUri( 'h', 10255, '', '', 'db', array( 'tls' => 'true', 'retryWrites' => 'false' ) ) );
        $this->assertSame( 'mongodb://u:p%40ss@h:27017/db', expMongoDB::connectionUri( 'h', 27017, 'u', 'p@ss', 'db' ) );
        $body = "id int NOT NULL auto_increment, name varchar(255) default '', sort_key int, PRIMARY KEY (id), KEY ez_name (name)";
        $this->assertSame( array( 'id', 'name', 'sort_key' ), expMongoDB::parseCreateTableColumns( $body ) );
        $indexes = expMongoDB::parseCreateTableIndexes( $body );
        $this->assertSame( array(), expMongoDB::sortIndexes( expMongoWireProfile::get( 'mongodb' ), array( 'id', 'name', 'sort_key' ), $indexes ) );
        $this->assertSame( array( 'exp_sort_sort_key' => array( 'sort_key' => 1 ) ),
            expMongoDB::sortIndexes( expMongoWireProfile::get( 'cosmosdb' ), array( 'id', 'name', 'sort_key' ), $indexes ) );
    }
}
