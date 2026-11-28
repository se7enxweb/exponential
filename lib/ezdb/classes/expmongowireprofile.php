<?php
/**
 * File containing the expMongoWireProfile class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * What a database that speaks the MongoDB wire protocol does differently from MongoDB itself: one profile per
 * database, read by expMongoDB (the driver) and the installer. Pure data and pure functions: nothing here opens a
 * connection, so every rule is tested without a server.
 *
 * Profiles:
 *
 *  - mongodb: MongoDB 4.0 or later. The aggregation operators $bitAnd, $bitOr, $bitXor and $bitNot exist from 6.3
 *    on; below that the driver writes them as arithmetic (see bitwiseAggregation()).
 *  - ferretdb: FerretDB 2 (MongoDB wire protocol over PostgreSQL with the DocumentDB extension). FerretDB 1 (over
 *    plain PostgreSQL or SQLite) has no $lookup and cannot hold a site.
 *  - documentdb: Amazon DocumentDB 5.0. No retryable writes (retryWrites=false), TLS with the Amazon RDS CA bundle,
 *    the replica set rs0, no aggregation bitwise operators, $lookup only as an equality match or an uncorrelated
 *    subquery (no let), index keys of at most 2048 bytes, at most 64 indexes per collection.
 *  - cosmosdb: Azure Cosmos DB for MongoDB, request units (server version 4.2). TLS on port 10255, no retryable
 *    writes, no aggregation bitwise operators, no $lookup with let or pipeline, a sort only on an indexed field
 *    (the driver indexes every column), a unique index only on an empty collection.
 *  - cosmosdbvcore: Azure Cosmos DB for MongoDB vCore, the DocumentDB engine on PostgreSQL as FerretDB 2 uses it;
 *    TLS on port 10260, no retryable writes.
 *
 * @since 6.0.15
 */
class expMongoWireProfile
{
    /** The first MongoDB version with the aggregation operators $bitAnd, $bitOr, $bitXor and $bitNot */
    const BITWISE_AGGREGATION_SINCE = '6.3';

    /**
     * Every profile, by name.
     *
     * Keys: label; alias (site.ini ImplementationAlias); aliases; driver (the class); port; tls (a connection uses
     * TLS by default); retry_writes (retryable writes; false writes retryWrites=false into the URI);
     * replica_set (written into the URI when set); bitwise_aggregation (true, false, or 'version': from
     * BITWISE_AGGREGATION_SINCE on); lookup_let ($lookup with let and pipeline: a correlated subquery);
     * bits_query ($bitsAnySet and friends in a filter); index_every_field (an index on every column, where a sort
     * needs one); unique_index_on_empty (a unique index can only be made while the collection is empty);
     * max_indexes (per collection, 0: no limit known); index_key_bytes (the longest index key, 0: no limit known);
     * auth_source, auth_mechanism (written into the URI when a user logs in); transactions (multi-document
     * transactions); text_index; live_tested; level (site, datatable: the most this
     * database is expected to carry); version_pattern (finds the version in buildInfo's version); notes.
     *
     * @return array
     */
    public static function all()
    {
        $base = array(
            'tls' => false,
            'retry_writes' => true,
            'replica_set' => '',
            'bitwise_aggregation' => false,
            'lookup_let' => true,
            'bits_query' => true,
            'index_every_field' => false,
            'unique_index_on_empty' => false,
            'max_indexes' => 64,
            'index_key_bytes' => 0,
            'auth_source' => '',
            'auth_mechanism' => '',
            'transactions' => true,
            'text_index' => true,
            'live_tested' => false,
            'level' => 'site',
            'version_pattern' => '/^([0-9][0-9.]*)/',
            'notes' => array(),
        );
        $profiles = array(
            'mongodb' => array(
                'label' => 'MongoDB', 'alias' => 'mongodb', 'aliases' => array(), 'driver' => 'expMongoDB',
                'port' => 27017, 'bitwise_aggregation' => 'version', 'live_tested' => true,
                'notes' => array( 'Before 6.3 the aggregation bitwise operators are written as arithmetic' ),
            ),
            'ferretdb' => array(
                'label' => 'FerretDB', 'alias' => 'ferretdb', 'aliases' => array( 'ferret' ), 'driver' => 'expFerretDB',
                'port' => 27017, 'retry_writes' => false, 'text_index' => false,
                // FerretDB 2 authenticates PostgreSQL's users with SCRAM-SHA-256 against admin; negotiated against
                // the site's database, the client falls back to SCRAM-SHA-1 and fails ("invalid salt length")
                'auth_source' => 'admin', 'auth_mechanism' => 'SCRAM-SHA-256',
                'notes' => array( 'FerretDB 2 over PostgreSQL with the DocumentDB extension',
                                  'Logs in with SCRAM-SHA-256 against admin (authSource=admin)',
                                  'FerretDB 1 (PostgreSQL or SQLite backend) has no $lookup: not a site database',
                                  'Bitwise aggregation operators are written as arithmetic' ),
            ),
            'documentdb' => array(
                'label' => 'Amazon DocumentDB', 'alias' => 'documentdb', 'aliases' => array( 'docdb', 'awsdocumentdb' ),
                'driver' => 'expDocumentDB', 'port' => 27017, 'tls' => true, 'retry_writes' => false,
                'replica_set' => 'rs0', 'lookup_let' => false, 'index_key_bytes' => 2048, 'level' => 'datatable',
                'notes' => array( 'No retryable writes: retryWrites=false',
                                  'TLS with the Amazon RDS CA bundle ([MongoWireSettings] TLSCAFile)',
                                  'No $bitAnd/$bitOr/$bitXor/$bitNot in aggregation: written as arithmetic',
                                  '$lookup with let (a correlated subquery) is refused: the kernel uses it in 19 places',
                                  'Index keys of at most 2048 bytes, at most 64 indexes per collection',
                                  'No emulator exists: not tested live' ),
            ),
            'cosmosdb' => array(
                'label' => 'Azure Cosmos DB for MongoDB', 'alias' => 'cosmosdb',
                'aliases' => array( 'cosmos', 'cosmosmongo', 'azurecosmosdb' ), 'driver' => 'expCosmosMongoDB',
                'port' => 10255, 'tls' => true, 'retry_writes' => false, 'lookup_let' => false,
                'index_every_field' => true, 'unique_index_on_empty' => true, 'max_indexes' => 0,
                'text_index' => false, 'level' => 'datatable',
                'notes' => array( 'Request-unit API, server version 4.2 or later',
                                  'No retryable writes: retryWrites=false; TLS on port 10255',
                                  'No $bitAnd/$bitOr/$bitXor/$bitNot in aggregation: written as arithmetic',
                                  'No $lookup with let or pipeline: the kernel uses it in 19 places',
                                  'A sort needs an index on the field: every column is indexed',
                                  'A unique index only on an empty collection: made with CREATE TABLE' ),
            ),
            'cosmosdbvcore' => array(
                'label' => 'Azure Cosmos DB for MongoDB vCore', 'alias' => 'cosmosdbvcore',
                'aliases' => array( 'cosmosvcore' ), 'driver' => 'expCosmosVCoreDB',
                'port' => 10260, 'tls' => true, 'retry_writes' => false, 'text_index' => false,
                'notes' => array( 'The DocumentDB engine on PostgreSQL, as FerretDB 2 uses it',
                                  'No retryable writes: retryWrites=false; TLS on port 10260' ),
            ),
        );
        $all = array();
        foreach ( $profiles as $name => $profile )
            $all[$name] = array_merge( $base, array( 'name' => $name ), $profile );
        return $all;
    }

    /**
     * A profile by its name or one of its aliases; the MongoDB profile for an empty or unknown name.
     *
     * @param string $name
     * @return array
     */
    public static function get( $name )
    {
        $name = strtolower( trim( (string)$name ) );
        $all = self::all();
        if ( isset( $all[$name] ) )
            return $all[$name];
        foreach ( $all as $profile )
            if ( in_array( $name, $profile['aliases'], true ) )
                return $profile;
        return $all['mongodb'];
    }

    /**
     * The profile of a driver: [MongoWireSettings] Profile when it names one, else the profile of the class.
     *
     * @param string $class the driver's class, e.g. expFerretDB
     * @param string $configured [MongoWireSettings] Profile
     * @return array
     */
    public static function forDriver( $class, $configured = '' )
    {
        if ( trim( (string)$configured ) !== '' )
            return self::get( $configured );
        $class = strtolower( (string)$class );
        foreach ( self::all() as $profile )
            if ( strtolower( $profile['driver'] ) === $class )
                return $profile;
        return self::get( 'mongodb' );
    }

    /**
     * Whether the server takes $bitAnd, $bitOr, $bitXor and $bitNot in an aggregation expression.
     *
     * @param array $profile
     * @param string $serverVersion buildInfo's version, '' when not known
     * @return bool
     */
    public static function bitwiseAggregation( array $profile, $serverVersion = '' )
    {
        if ( $profile['bitwise_aggregation'] !== 'version' )
            return (bool)$profile['bitwise_aggregation'];
        if ( !preg_match( $profile['version_pattern'], (string)$serverVersion, $m ) )
            return false;
        return version_compare( $m[1], self::BITWISE_AGGREGATION_SINCE, '>=' );
    }

    /**
     * The options of the connection URI: the profile's, overridden by [MongoWireSettings].
     *
     * @param array $profile
     * @param array $settings TLS (enabled, disabled, '' for the profile's), TLSCAFile, ReplicaSet, AuthSource,
     *                        ReadPreference, RetryWrites (enabled, disabled, ''), URIOptions (name=value list)
     * @param bool $withUser a user logs in: the profile's authSource and authMechanism are written
     * @return array name => value, in the order they are written
     */
    public static function uriOptions( array $profile, array $settings = array(), $withUser = false )
    {
        $get = function ( $key ) use ( $settings )
        {
            return isset( $settings[$key] ) && !is_array( $settings[$key] ) ? trim( (string)$settings[$key] ) : '';
        };
        $options = array();
        $tls = $get( 'TLS' ) !== '' ? $get( 'TLS' ) === 'enabled' : $profile['tls'];
        if ( $tls )
            $options['tls'] = 'true';
        if ( $tls && $get( 'TLSCAFile' ) !== '' )
            $options['tlsCAFile'] = $get( 'TLSCAFile' );
        $retry = $get( 'RetryWrites' ) !== '' ? $get( 'RetryWrites' ) === 'enabled' : $profile['retry_writes'];
        if ( !$retry )
            $options['retryWrites'] = 'false';
        $replicaSet = $get( 'ReplicaSet' ) !== '' ? $get( 'ReplicaSet' ) : $profile['replica_set'];
        if ( $replicaSet !== '' && $replicaSet !== 'none' )
            $options['replicaSet'] = $replicaSet;
        $authSource = $get( 'AuthSource' ) !== '' ? $get( 'AuthSource' ) : ( $withUser ? $profile['auth_source'] : '' );
        if ( $authSource !== '' )
            $options['authSource'] = $authSource;
        if ( $withUser && $profile['auth_mechanism'] !== '' )
            $options['authMechanism'] = $profile['auth_mechanism'];
        if ( $get( 'ReadPreference' ) !== '' )
            $options['readPreference'] = $get( 'ReadPreference' );
        $extra = isset( $settings['URIOptions'] ) ? (array)$settings['URIOptions'] : array();
        foreach ( $extra as $key => $value )
        {
            if ( is_int( $key ) )
            {
                if ( strpos( (string)$value, '=' ) === false )
                    continue;
                list( $key, $value ) = explode( '=', (string)$value, 2 );
            }
            $key = trim( (string)$key );
            if ( $key !== '' && preg_match( '/^[A-Za-z][A-Za-z0-9]*$/', $key ) )
                $options[$key] = trim( (string)$value );
        }
        return $options;
    }

    /**
     * Rewrites $bitAnd, $bitOr, $bitXor and $bitNot of an aggregation expression or pipeline as arithmetic, for a
     * server without them, wherever one side is an integer constant (the kernel's masks are). x & c is the sum, over
     * the bits k of c, of 2^k * ((floor(x / 2^k)) mod 2), for x >= 0, as the kernel's masks and ids are; a negative
     * constant c is x - (x & ~c). x | c is x + c - (x & c), x ^ c is x + c - 2 (x & c), ~x is -x - 1. A bitwise
     * operator of two fields is left as it is: the server refuses it, and the driver logs the refusal.
     *
     * @param mixed $node
     * @return mixed
     */
    public static function emulateBitwise( $node )
    {
        if ( !is_array( $node ) )
            return $node;
        $out = array();
        foreach ( $node as $key => $value )
            $out[$key] = self::emulateBitwise( $value );
        if ( count( $out ) !== 1 )
            return $out;
        $operator = key( $out );
        $args = current( $out );
        if ( $operator === '$bitNot' )
        {
            $x = is_array( $args ) && array_keys( $args ) === array( 0 ) ? $args[0] : $args;
            if ( is_int( $x ) )
                return ~$x;
            return array( '$subtract' => array( array( '$multiply' => array( $x, -1 ) ), 1 ) );
        }
        if ( !in_array( $operator, array( '$bitAnd', '$bitOr', '$bitXor' ), true ) || !is_array( $args )
             || array_keys( $args ) !== range( 0, count( $args ) - 1 ) || count( $args ) < 1 )
            return $out;
        $result = array_shift( $args );
        foreach ( $args as $next )
        {
            $folded = self::bitwisePair( $operator, $result, $next );
            if ( $folded === null )
                return $out;
            $result = $folded;
        }
        return $result;
    }

    /**
     * One step of emulateBitwise(): $a <operator> $b, or null when neither side is an integer constant.
     *
     * @return mixed|null
     */
    private static function bitwisePair( $operator, $a, $b )
    {
        if ( is_int( $a ) && is_int( $b ) )
            return $operator === '$bitAnd' ? $a & $b : ( $operator === '$bitOr' ? $a | $b : $a ^ $b );
        if ( is_int( $a ) )
            list( $a, $b ) = array( $b, $a );
        if ( !is_int( $b ) )
            return null;
        $and = self::andConstant( $a, $b );
        if ( $operator === '$bitAnd' )
            return $and;
        if ( $operator === '$bitOr' )
            return array( '$subtract' => array( array( '$add' => array( $a, $b ) ), $and ) );
        return array( '$subtract' => array( array( '$add' => array( $a, $b ) ), array( '$multiply' => array( 2, $and ) ) ) );
    }

    /**
     * x & c for an expression x >= 0 and an integer constant c, as arithmetic.
     *
     * @return mixed
     */
    private static function andConstant( $x, $c )
    {
        if ( $c === 0 )
            return 0;
        if ( $c < 0 )
            return array( '$subtract' => array( $x, self::andConstant( $x, ~$c ) ) );
        $terms = array();
        for ( $k = 0; $k < 62 && ( $c >> $k ) > 0; $k++ )
        {
            if ( ( ( $c >> $k ) & 1 ) === 0 )
                continue;
            $power = 1 << $k;
            $bit = array( '$mod' => array( $power === 1 ? $x : array( '$floor' => array( '$divide' => array( $x, $power ) ) ), 2 ) );
            $terms[] = $power === 1 ? $bit : array( '$multiply' => array( $bit, $power ) );
        }
        return array( '$toLong' => count( $terms ) === 1 ? $terms[0] : array( '$add' => $terms ) );
    }

    /**
     * Whether an aggregation expression or pipeline still holds a bitwise operator.
     *
     * @param mixed $node
     * @return bool
     */
    public static function hasBitwise( $node )
    {
        if ( !is_array( $node ) )
            return false;
        foreach ( $node as $key => $value )
        {
            if ( in_array( $key, array( '$bitAnd', '$bitOr', '$bitXor', '$bitNot' ), true ) )
                return true;
            if ( self::hasBitwise( $value ) )
                return true;
        }
        return false;
    }

    /**
     * Whether a pipeline holds a $lookup with let or pipeline (a correlated subquery), which a profile without
     * lookup_let refuses.
     *
     * @param array $pipeline
     * @return bool
     */
    public static function hasCorrelatedLookup( array $pipeline )
    {
        foreach ( $pipeline as $stage )
            if ( is_array( $stage ) && isset( $stage['$lookup'] ) && is_array( $stage['$lookup'] )
                 && ( isset( $stage['$lookup']['let'] ) || isset( $stage['$lookup']['pipeline'] ) ) )
                return true;
        return false;
    }
}
