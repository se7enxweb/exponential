<?php
/**
 * The databases of the MongoDB wire protocol against a live server (FerretDB, the Azure Cosmos DB emulator, MongoDB):
 * EXP_MONGOWIRE_URI (a mongodb:// connection string), EXP_MONGOWIRE_PROFILE (mongodb, ferretdb, documentdb, cosmosdb,
 * cosmosdbvcore), EXP_MONGOWIRE_DATABASE (default exp_mongowire, emptied first); EXP_MONGOWIRE_REPORT names a JSON file
 * the capability probe writes. Skipped without a server.
 *
 *  ML-01 - the operators the adapter and the kernel use, probed one by one: what the profile claims must work
 *  ML-02 - the kernel's language mask filter with $bitAnd written as arithmetic gives the rows $bitsAnySet gives
 *  ML-03 - the driver: CREATE TABLE with its indexes, INSERT, a WHERE with "&", ORDER BY, an atomic id
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

#[\PHPUnit\Framework\Attributes\Group('mongowire-live')]
class expMongoWireLiveTest extends PHPUnit\Framework\TestCase
{
    private static $client = null;
    private static $database = '';
    private static $profile = array();
    private static $skip = null;

    public static function setUpBeforeClass(): void
    {
        $uri = (string)getenv( 'EXP_MONGOWIRE_URI' );
        if ( $uri === '' )
        {
            self::$skip = 'EXP_MONGOWIRE_URI is not set';
            return;
        }
        if ( !extension_loaded( 'mongodb' ) )
        {
            self::$skip = 'the PHP extension mongodb is not loaded';
            return;
        }
        $autoload = __DIR__ . '/../../../../../../vendor/autoload.php';
        if ( is_file( $autoload ) )
            require_once $autoload;
        expMongoDB::requireClientLibrary();
        self::$profile = expMongoWireProfile::get( (string)getenv( 'EXP_MONGOWIRE_PROFILE' ) );
        self::$database = getenv( 'EXP_MONGOWIRE_DATABASE' ) ?: 'exp_mongowire';
        try
        {
            self::$client = new MongoDB\Client( $uri );
            self::$client->selectDatabase( self::$database )->command( array( 'ping' => 1 ) );
            foreach ( self::$client->selectDatabase( self::$database )->listCollectionNames() as $name )
                self::$client->selectCollection( self::$database, $name )->drop();
        }
        catch ( Exception $e )
        {
            self::$client = null;
            self::$skip = 'cannot connect: ' . $e->getMessage();
        }
    }

    protected function setUp(): void
    {
        if ( self::$skip !== null )
            $this->markTestSkipped( self::$skip );
    }

    private static function seed( $name )
    {
        $collection = self::$client->selectCollection( self::$database, $name );
        $collection->drop();
        $collection->createIndex( array( 'id' => 1 ), array( 'unique' => true, 'name' => 'PRIMARY' ) );
        if ( self::$profile['index_every_field'] )
            foreach ( array( 'mask', 'name', 'parent' ) as $field )
                $collection->createIndex( array( $field => 1 ), array( 'name' => 'exp_sort_' . $field ) );
        $rows = array();
        for ( $i = 1; $i <= 12; $i++ )
            $rows[] = array( 'id' => $i, 'mask' => $i, 'name' => 'n' . ( 13 - $i ), 'parent' => $i % 3, 'smask' => (string)$i );
        $collection->insertMany( $rows );
        $other = self::$client->selectCollection( self::$database, $name . '_o' );
        $other->drop();
        $other->insertMany( array( array( 'id' => 1, 'label' => 'zero' ), array( 'id' => 2, 'label' => 'one' ) ) );
        return $collection;
    }

    private static function ids( $cursor )
    {
        $ids = array();
        foreach ( $cursor as $doc )
            $ids[] = (int)$doc['id'];
        sort( $ids );
        return $ids;
    }

    public function testCapabilities()
    {
        $c = self::seed( 'probe' );
        $o = 'probe_o';
        $probes = array(
            'bitsAnySet' => function () use ( $c ) { return self::ids( $c->find( array( 'mask' => array( '$bitsAnySet' => 4 ) ) ) ) === array( 4, 5, 6, 7, 12 ); },
            'bitAnd_native' => function () use ( $c ) { return self::ids( $c->aggregate( array( array( '$match' => array( '$expr' => array( '$gt' => array( array( '$bitAnd' => array( '$mask', 4 ) ), 0 ) ) ) ) ) ) ) === array( 4, 5, 6, 7, 12 ); },
            'bitAnd_emulated' => function () use ( $c ) { return self::ids( $c->aggregate( expMongoWireProfile::emulateBitwise( array( array( '$match' => array( '$expr' => array( '$gt' => array( array( '$bitAnd' => array( '$mask', 4 ) ), 0 ) ) ) ) ) ) ) ) === array( 4, 5, 6, 7, 12 ); },
            'convert' => function () use ( $c ) { return self::ids( $c->aggregate( array( array( '$match' => array( '$expr' => array( '$eq' => array( array( '$convert' => array( 'input' => '$smask', 'to' => 'long', 'onError' => 0, 'onNull' => 0 ) ), 3 ) ) ) ) ) ) ) === array( 3 ); },
            'lookup_equality' => function () use ( $c, $o ) { $r = iterator_to_array( $c->aggregate( array( array( '$match' => array( 'id' => 2 ) ), array( '$lookup' => array( 'from' => $o, 'localField' => 'parent', 'foreignField' => 'id', 'as' => 'p' ) ) ) ) ); return count( $r ) === 1 && count( $r[0]['p'] ) === 1; },
            'lookup_let' => function () use ( $c, $o ) { $r = iterator_to_array( $c->aggregate( array( array( '$match' => array( 'id' => 2 ) ), array( '$lookup' => array( 'from' => $o, 'let' => array( 'pid' => '$parent' ), 'pipeline' => array( array( '$match' => array( '$expr' => array( '$eq' => array( '$id', '$$pid' ) ) ) ) ), 'as' => 'p' ) ) ) ) ); return count( $r ) === 1 && count( $r[0]['p'] ) === 1; },
            'unwind_group_sort' => function () use ( $c ) { $r = iterator_to_array( $c->aggregate( array( array( '$group' => array( '_id' => '$parent', 'n' => array( '$sum' => 1 ), 'm' => array( '$max' => '$id' ) ) ), array( '$sort' => array( '_id' => 1 ) ) ) ) ); return count( $r ) === 3 && (int)$r[0]['n'] === 4; },
            'replaceRoot_mergeObjects' => function () use ( $c ) { $r = iterator_to_array( $c->aggregate( array( array( '$match' => array( 'id' => 1 ) ), array( '$replaceRoot' => array( 'newRoot' => array( '$mergeObjects' => array( array( 'x' => 1 ), '$$ROOT' ) ) ) ) ) ) ); return count( $r ) === 1 && (int)$r[0]['x'] === 1; },
            'indexOfArray_let' => function () use ( $c ) { $r = iterator_to_array( $c->aggregate( array( array( '$match' => array( 'id' => 3 ) ), array( '$project' => array( 'at' => array( '$let' => array( 'vars' => array( 'a' => array( '$indexOfArray' => array( array( 1, 3, 5 ), '$mask' ) ) ), 'in' => '$$a' ) ) ) ) ) ) ); return count( $r ) === 1 && (int)$r[0]['at'] === 1; },
            'sort_skip_limit' => function () use ( $c ) { $r = iterator_to_array( $c->find( array(), array( 'sort' => array( 'name' => 1 ), 'skip' => 1, 'limit' => 2 ) ) ); return count( $r ) === 2 && $r[0]['name'] === 'n10'; },
            'sort_two_fields' => function () use ( $c ) { $r = iterator_to_array( $c->find( array(), array( 'sort' => array( 'parent' => 1, 'id' => -1 ), 'limit' => 1 ) ) ); return count( $r ) === 1 && (int)$r[0]['id'] === 12; },
            'regex_case_insensitive' => function () use ( $c ) { return self::ids( $c->find( array( 'name' => new MongoDB\BSON\Regex( '^N1', 'i' ) ) ) ) === array( 1, 2, 3, 4 ); },
            'findOneAndUpdate_upsert_inc' => function () { $s = self::$client->selectCollection( self::$database, 'ezsequence' ); $s->findOneAndUpdate( array( '_id' => 'probe' ), array( '$inc' => array( 'seq' => 1 ) ), array( 'upsert' => true ) ); $d = $s->findOneAndUpdate( array( '_id' => 'probe' ), array( '$inc' => array( 'seq' => 1 ) ), array( 'upsert' => true, 'returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER ) ); return (int)$d['seq'] === 2; },
            'unique_index_refuses_duplicate' => function () use ( $c ) { try { $c->insertOne( array( 'id' => 1 ) ); return false; } catch ( Exception $e ) { return true; } },
            'updateMany_deleteMany' => function () use ( $c ) { $c->updateMany( array( 'parent' => 0 ), array( '$set' => array( 'flag' => 1 ) ) ); $n = $c->countDocuments( array( 'flag' => 1 ) ); $c->deleteMany( array( 'id' => array( '$gt' => 11 ) ) ); return $n === 4 && $c->countDocuments() === 11; },
            'listIndexes' => function () use ( $c ) { foreach ( $c->listIndexes() as $index ) if ( $index->getName() === 'PRIMARY' ) return true; return false; },
            'buildInfo' => function () { $info = current( iterator_to_array( self::$client->selectDatabase( self::$database )->command( array( 'buildInfo' => 1 ) ) ) ); return isset( $info['version'] ); },
        );
        $results = array();
        foreach ( $probes as $name => $probe )
        {
            try
            {
                $results[$name] = $probe() ? 'yes' : 'wrong result';
            }
            catch ( Throwable $e )
            {
                $results[$name] = 'refused: ' . substr( $e->getMessage(), 0, 160 );
            }
        }
        $info = current( iterator_to_array( self::$client->selectDatabase( self::$database )->command( array( 'buildInfo' => 1 ) ) ) );
        $report = array( 'profile' => self::$profile['name'], 'version' => isset( $info['version'] ) ? (string)$info['version'] : '',
                         'ferretdb' => isset( $info['ferretdb'] ) ? json_decode( json_encode( $info['ferretdb'] ), true ) : null,
                         'probes' => $results );
        fwrite( STDERR, "\n" . json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
        if ( ( $path = (string)getenv( 'EXP_MONGOWIRE_REPORT' ) ) !== '' )
            file_put_contents( $path, json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );

        // what the driver relies on, whatever the profile
        foreach ( array( 'bitsAnySet', 'bitAnd_emulated', 'convert', 'lookup_equality', 'unwind_group_sort', 'replaceRoot_mergeObjects',
                         'indexOfArray_let', 'sort_skip_limit', 'regex_case_insensitive', 'findOneAndUpdate_upsert_inc',
                         'unique_index_refuses_duplicate', 'updateMany_deleteMany', 'listIndexes', 'buildInfo' ) as $name )
            $this->assertSame( 'yes', $results[$name], $name );
        // what the profile claims
        if ( self::$profile['lookup_let'] )
            $this->assertSame( 'yes', $results['lookup_let'], 'lookup_let (the profile claims it)' );
        if ( self::$profile['bitwise_aggregation'] === true )
            $this->assertSame( 'yes', $results['bitAnd_native'], 'bitAnd_native (the profile claims it)' );
    }

    public function testDriver()
    {
        $uri = parse_url( (string)getenv( 'EXP_MONGOWIRE_URI' ) );
        $class = self::$profile['driver'];
        $db = new $class( array( 'server' => $uri['host'], 'port' => isset( $uri['port'] ) ? $uri['port'] : '',
                                 'user' => isset( $uri['user'] ) ? rawurldecode( $uri['user'] ) : '',
                                 'password' => isset( $uri['pass'] ) ? rawurldecode( $uri['pass'] ) : '',
                                 'database' => self::$database, 'charset' => 'utf-8', 'socket' => false,
                                 'use_persistent_connection' => false, 'show_errors' => false ) );
        $this->assertTrue( $db->isConnected(), 'the driver connects (' . $db->clientUri() . ')' );
        $this->assertSame( 'mongo', $db->databaseName() );
        $this->assertTrue( $db->query( 'CREATE TABLE exp_mw ( id int NOT NULL auto_increment, mask int NOT NULL default 0, name varchar(255) NOT NULL default \'\', PRIMARY KEY (id), KEY exp_mw_name (name) )' ) );
        for ( $i = 1; $i <= 6; $i++ )
            $this->assertNotFalse( $db->query( "INSERT INTO exp_mw ( mask, name ) VALUES ( $i, 'n" . ( 7 - $i ) . "' )" ) );
        $rows = $db->arrayQuery( 'SELECT id, mask FROM exp_mw WHERE mask & 2 > 0 ORDER BY mask DESC' );
        $this->assertSame( array( 6, 3, 2 ), array_map( function ( $r ) { return (int)$r['mask']; }, $rows ) );
        $rows = $db->arrayQuery( 'SELECT name FROM exp_mw ORDER BY name ASC', array( 'limit' => 2 ) );
        $this->assertSame( array( 'n1', 'n2' ), array_map( function ( $r ) { return $r['name']; }, $rows ) );
        $this->assertSame( 2, count( $db->aggregate( 'exp_mw', array( array( '$match' => array( '$expr' => array( '$gt' => array( array( '$bitAnd' => array( '$mask', 4 ) ), 0 ) ) ) ), array( '$match' => array( 'mask' => array( '$lt' => 6 ) ) ) ) ) ) );
        $first = $db->nextAtomicID( 'exp_mw_seq' );
        $this->assertSame( $first + 1, $db->nextAtomicID( 'exp_mw_seq' ) );
    }
}
