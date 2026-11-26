<?php
/**
 * The MongoDB DFS cluster backend without a server: LIKE patterns, prefixes and wildcards as regular expressions,
 * the connection string, the errors it tries again, the method for method parity with the MySQLi backend, the
 * schema file and the gateway. The parts that need a server are in expDFSMongoDBBackendIntegrationTest.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDFSFileHandlerMongoDBBackendTest extends ezpTestCase
{
    /** @return string the root of the installation */
    private static function root()
    {
        return dirname( __DIR__, 6 );
    }

    /** Whether the regular expression the backend builds matches $name, as the server's PCRE would */
    private static function regexMatches( $regex, $name )
    {
        return preg_match( '/' . str_replace( '/', '\\/', $regex ) . '/', $name ) === 1;
    }

    public function testLikeToRegex()
    {
        // % is any text, _ a literal (as in the MySQLi backend); a trailing % leaves a plain prefix
        $this->assertSame( '^var/site/cache/a_b', expDFSFileHandlerMongoDBBackend::likeToRegex( 'var/site/cache/a_b%' ) );
        $this->assertTrue( self::regexMatches( expDFSFileHandlerMongoDBBackend::likeToRegex( 'var/a_b%' ), 'var/a_b/c.txt' ) );
        $this->assertFalse( self::regexMatches( expDFSFileHandlerMongoDBBackend::likeToRegex( 'var/a_b%' ), 'var/aXb/c.txt' ) );
        // without a trailing %: the whole name
        $this->assertTrue( self::regexMatches( expDFSFileHandlerMongoDBBackend::likeToRegex( 'var/%.txt' ), 'var/x/y.txt' ) );
        $this->assertFalse( self::regexMatches( expDFSFileHandlerMongoDBBackend::likeToRegex( 'var/%.txt' ), 'var/x/y.txt.bak' ) );
        $this->assertFalse( self::regexMatches( expDFSFileHandlerMongoDBBackend::likeToRegex( 'var/%.txt' ), 'var/x/yXtxt' ) );
        // the characters of regular expressions are literals
        foreach ( array( 'var/*x?y[1](2)+{3}|.^$\\' ) as $literal )
        {
            $this->assertTrue( self::regexMatches( expDFSFileHandlerMongoDBBackend::likeToRegex( addcslashes( $literal, '\\' ) . '%' ), $literal . 'tail' ) );
            $this->assertFalse( self::regexMatches( expDFSFileHandlerMongoDBBackend::likeToRegex( addcslashes( $literal, '\\' ) . '%' ), 'var/xx' ) );
        }
        // the escape of the callers (addcslashes( $like, '_' )) makes the next character a literal
        $this->assertTrue( self::regexMatches( expDFSFileHandlerMongoDBBackend::likeToRegex( 'a\\_b\\%c' ), 'a_b%c' ) );
        $this->assertFalse( self::regexMatches( expDFSFileHandlerMongoDBBackend::likeToRegex( 'a\\_b\\%c' ), 'a_bXXc' ) );
    }

    public function testPrefixRegex()
    {
        $this->assertSame( '^var/100%_x', expDFSFileHandlerMongoDBBackend::prefixRegex( 'var/100%_x' ) );
        $this->assertTrue( self::regexMatches( expDFSFileHandlerMongoDBBackend::prefixRegex( 'var/*[' ), 'var/*[1' ) );
        $this->assertFalse( self::regexMatches( expDFSFileHandlerMongoDBBackend::prefixRegex( 'var/*[' ), 'var/x[1' ) );
        $this->assertFalse( self::regexMatches( expDFSFileHandlerMongoDBBackend::prefixRegex( 'var/a.' ), 'var/ab' ) );
    }

    public function testWildcardToRegex()
    {
        $regex = expDFSFileHandlerMongoDBBackend::wildcardToRegex( 'var/{a,b}.txt' );
        $this->assertSame( '^var/(?:a|b)\\.txt$', $regex );
        foreach ( array( 'var/a.txt' => true, 'var/b.txt' => true, 'var/c.txt' => false, 'var/aXtxt' => false ) as $name => $expected )
            $this->assertSame( $expected, self::regexMatches( $regex, $name ), $name );
        $regex = expDFSFileHandlerMongoDBBackend::wildcardToRegex( 'var/?.t*' );
        foreach ( array( 'var/c.txt' => true, 'var/cc.txt' => false, 'var/aXtxt' => false ) as $name => $expected )
            $this->assertSame( $expected, self::regexMatches( $regex, $name ), $name );
        // nested alternatives, a comma outside braces, an unclosed brace
        $regex = expDFSFileHandlerMongoDBBackend::wildcardToRegex( 'x/{a,b{1,2}}' );
        foreach ( array( 'x/a' => true, 'x/b1' => true, 'x/b2' => true, 'x/b' => false, 'x/b3' => false ) as $name => $expected )
            $this->assertSame( $expected, self::regexMatches( $regex, $name ), $name );
        $this->assertTrue( self::regexMatches( expDFSFileHandlerMongoDBBackend::wildcardToRegex( 'a,b' ), 'a,b' ) );
        $this->assertTrue( self::regexMatches( expDFSFileHandlerMongoDBBackend::wildcardToRegex( 'x/{a,b' ), 'x/b' ) );
        // the characters of regular expressions are literals
        $this->assertTrue( self::regexMatches( expDFSFileHandlerMongoDBBackend::wildcardToRegex( 'c(1)+[2]$.txt' ), 'c(1)+[2]$.txt' ) );
        $this->assertFalse( self::regexMatches( expDFSFileHandlerMongoDBBackend::wildcardToRegex( 'c(1)+.txt' ), 'c11.txt' ) );

        $this->expectException( InvalidArgumentException::class );
        expDFSFileHandlerMongoDBBackend::wildcardToRegex( str_repeat( 'a', expDFSFileHandlerMongoDBBackend::MAX_WILDCARD_LENGTH + 1 ) );
    }

    public function testConnectionUri()
    {
        list( $uri, $options ) = expDFSFileHandlerMongoDBBackend::connectionUri( array( 'host' => 'db.example', 'port' => '27018', 'user' => 'pm', 'password' => 'p:@/w', 'dbname' => 'site', 'lock_timeout' => 3 ) );
        $this->assertSame( 'mongodb://db.example:27018', $uri );
        // the account goes in the options, never in the string: no character of a password can split it
        $this->assertSame( 'pm', $options['username'] );
        $this->assertSame( 'p:@/w', $options['password'] );
        $this->assertSame( 'site', $options['authSource'] );
        $this->assertSame( 3000, $options['serverSelectionTimeoutMS'] );

        list( $uri, $options ) = expDFSFileHandlerMongoDBBackend::connectionUri( array( 'host' => '', 'port' => '', 'dbname' => 'site' ) );
        $this->assertSame( 'mongodb://localhost:27017', $uri );
        $this->assertArrayNotHasKey( 'username', $options );

        list( $uri, ) = expDFSFileHandlerMongoDBBackend::connectionUri( array( 'socket' => '/run/mongodb/mongod.sock', 'host' => 'ignored' ) );
        $this->assertSame( 'mongodb://%2Frun%2Fmongodb%2Fmongod.sock', $uri );

        // a replica set: the whole string in DBHost, its own account and authSource kept
        list( $uri, $options ) = expDFSFileHandlerMongoDBBackend::connectionUri( array( 'host' => 'mongodb://u:p@a,b,c/?replicaSet=rs0&authSource=admin', 'user' => 'pm', 'dbname' => 'site' ) );
        $this->assertSame( 'mongodb://u:p@a,b,c/?replicaSet=rs0&authSource=admin', $uri );
        $this->assertArrayNotHasKey( 'username', $options );
        list( $uri, $options ) = expDFSFileHandlerMongoDBBackend::connectionUri( array( 'host' => 'mongodb+srv://cluster0.example/?authSource=admin', 'user' => 'pm', 'password' => 'x', 'dbname' => 'site' ) );
        $this->assertSame( 'pm', $options['username'] );
        $this->assertArrayNotHasKey( 'authSource', $options );
    }

    public function testRetryableErrors()
    {
        if ( !extension_loaded( 'mongodb' ) )
            $this->markTestSkipped( 'the PHP extension mongodb is not loaded' );
        $this->assertTrue( expDFSFileHandlerMongoDBBackend::isRetryable( new MongoDB\Driver\Exception\ConnectionTimeoutException( 'timeout' ) ) );
        // NotWritablePrimary, PrimarySteppedDown
        $this->assertTrue( expDFSFileHandlerMongoDBBackend::isRetryable( new MongoDB\Driver\Exception\ServerException( 'not primary', 10107 ) ) );
        $this->assertTrue( expDFSFileHandlerMongoDBBackend::isRetryable( new MongoDB\Driver\Exception\ServerException( 'stepped down', 189 ) ) );
        // a duplicate key, a bad value and an authentication failure are not
        foreach ( array( 11000, 2, 18 ) as $code )
            $this->assertFalse( expDFSFileHandlerMongoDBBackend::isRetryable( new MongoDB\Driver\Exception\ServerException( 'no', $code ) ), (string)$code );
        $this->assertSame( '18', expDFSFileHandlerMongoDBBackend::errorCodeOf( new MongoDB\Driver\Exception\ServerException( 'auth', 18 ) ) );
    }

    public function testNameTrunkOfViewCache()
    {
        $this->assertSame( 'var/site/cache/content/site/1/2/2-', expDFSFileHandlerMongoDBBackend::nameTrunk( 'var/site/cache/content/site/1/2/2-abc.cache', 'viewcache' ) );
        $this->assertSame( 'var/site/storage/x.png', expDFSFileHandlerMongoDBBackend::nameTrunk( 'var/site/storage/x.png', 'image' ) );
    }

    /**
     * Every public method of the MySQLi backend exists in the MongoDB backend, with as many parameters and the
     * same number of required ones: eZDFSFileHandler can use either.
     */
    public function testMethodParityWithTheMySQLiBackend()
    {
        $mysql = new ReflectionClass( 'eZDFSFileHandlerMySQLiBackend' );
        $mongo = new ReflectionClass( 'expDFSFileHandlerMongoDBBackend' );
        foreach ( $mysql->getMethods( ReflectionMethod::IS_PUBLIC ) as $method )
        {
            $name = $method->getName();
            $this->assertTrue( $mongo->hasMethod( $name ), "missing $name" );
            $other = $mongo->getMethod( $name );
            $this->assertTrue( $other->isPublic(), "$name is not public" );
            $this->assertGreaterThanOrEqual( $method->getNumberOfParameters(), $other->getNumberOfParameters(), $name );
            $this->assertSame( $method->getNumberOfRequiredParameters(), $other->getNumberOfRequiredParameters(), $name );
        }
        foreach ( array( 'eZClusterEventNotifier', 'expDFSFileHandlerDBBackendInterface' ) as $interface )
            $this->assertTrue( $mongo->implementsInterface( $interface ), $interface );
    }

    public function testNoSqlAndNoLibraryInTheCode()
    {
        // the code only: the comments compare with the SQL engines on purpose
        $source = '';
        foreach ( token_get_all( file_get_contents( self::root() . '/kernel/private/classes/clusterfilehandlers/dfsbackends/mongodb.php' ) ) as $token )
        {
            if ( !is_array( $token ) || !in_array( $token[0], array( T_COMMENT, T_DOC_COMMENT ), true ) )
                $source .= is_array( $token ) ? $token[1] : $token;
        }
        foreach ( array( 'SELECT ', 'UPDATE ', 'DELETE FROM', 'INSERT INTO', 'MongoDB\\Client', 'startSession', 'eZDB::' ) as $other )
            $this->assertStringNotContainsString( $other, $source );
        $this->assertStringContainsString( 'MongoDB\\Driver\\Manager', $source );
        $this->assertStringContainsString( "'upsert' => true", $source );
    }

    public function testSchemaFile()
    {
        $schema = expDFSFileHandlerMongoDBBackend::schemaFromJson( file_get_contents( self::root() . '/kernel/sql/mongodb/cluster_dfs_schema.json' ) );
        $this->assertSame( array( 'ezdfsfile', 'ezdfsfile_cache' ), array_keys( $schema ) );
        foreach ( $schema as $collection => $indexes )
        {
            $keys = array_column( $indexes, 'key', 'name' );
            $this->assertSame( array( 'name' => 1 ), $keys["{$collection}_name"] );
            $this->assertSame( array( 'name_trunk' => 1 ), $keys["{$collection}_name_trunk"] );
            $this->assertSame( array( 'expired' => 1, 'scope' => 1 ), $keys["{$collection}_expired_scope"] );
        }
        foreach ( array( '{', '{"collections":{"a b":{}}}', '{"collections":{"x":{"indexes":[{"name":"i"}]}}}' ) as $bad )
        {
            try
            {
                expDFSFileHandlerMongoDBBackend::schemaFromJson( $bad );
                $this->fail( "accepted $bad" );
            }
            catch ( RuntimeException $e )
            {
                $this->assertNotSame( '', $e->getMessage() );
            }
        }
    }

    public function testGatewaySelection()
    {
        $gateway = file_get_contents( self::root() . '/kernel/clustering/dfsmongodb.php' );
        $this->assertStringContainsString( 'class expDfsMongoDBClusterGateway extends ezpClusterGateway', $gateway );
        $this->assertStringContainsString( "setGatewayClass( 'expDfsMongoDBClusterGateway' )", $gateway );
        $classes = require self::root() . '/autoload/ezp_kernel.php';
        $this->assertSame( 'kernel/private/classes/clusterfilehandlers/dfsbackends/mongodb.php', $classes['expDFSFileHandlerMongoDBBackend'] );
        $this->assertSame( 'kernel/clustering/dfsmongodb.php', $classes['expDfsMongoDBClusterGateway'] );

        if ( !class_exists( 'ezpClusterGateway', false ) )
            require_once self::root() . '/kernel/clustering/gateway.php';
        if ( !class_exists( 'expDfsMongoDBClusterGateway', false ) )
            require_once self::root() . '/kernel/clustering/dfsmongodb.php';
        list( $uri, $options ) = expDfsMongoDBClusterGateway::connectionUri( 'db.example', 0, 'pm', 'p@ss', 'site' );
        $this->assertSame( 'mongodb://db.example:27017', $uri );
        $this->assertSame( array( 'pm', 'p@ss', 'site' ), array( $options['username'], $options['password'], $options['authSource'] ) );
        list( $uri, ) = expDfsMongoDBClusterGateway::connectionUri( 'mongodb://a,b/?replicaSet=rs0', 0, '', '', 'site' );
        $this->assertSame( 'mongodb://a,b/?replicaSet=rs0', $uri );
    }
}
