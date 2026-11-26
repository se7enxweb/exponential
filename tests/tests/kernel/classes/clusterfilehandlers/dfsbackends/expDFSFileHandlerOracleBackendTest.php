<?php
/**
 * The Oracle DFS cluster backend without a database: the Oracle errors it retries, LIKE patterns, prefixes and
 * wildcards with ESCAPE '\', rows as the other backends return them (filesize as size, NULL as ''), the method for
 * method parity with the MySQLi backend, the schema files and the gateway. The parts that need a database are in
 * expDFSOracleBackendIntegrationTest.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDFSFileHandlerOracleBackendTest extends ezpTestCase
{
    /** @return string the root of the installation */
    private static function root()
    {
        return dirname( __DIR__, 6 );
    }

    /** Whether a LIKE pattern with ESCAPE '\' matches $name, as Oracle compares (binary) */
    private static function likeMatches( $pattern, $name )
    {
        $regex = '';
        $length = strlen( $pattern );
        for ( $i = 0; $i < $length; ++$i )
        {
            $char = $pattern[$i];
            if ( $char === '\\' && $i + 1 < $length )
                $regex .= preg_quote( $pattern[++$i], '/' );
            elseif ( $char === '%' )
                $regex .= '.*';
            elseif ( $char === '_' )
                $regex .= '.';
            else
                $regex .= preg_quote( $char, '/' );
        }
        return preg_match( '/^' . $regex . '$/s', $name ) === 1;
    }

    public function testRetryableCodes()
    {
        // a WAIT that ran out (30006, and 54 on 23ai and later), a deadlock, a serialization failure, two upserts
        foreach ( array( '30006', 30006, '54', '60', '8177', '1' ) as $code )
            $this->assertTrue( expDFSFileHandlerOracleBackend::isRetryableCode( $code ), var_export( $code, true ) );
        // a missing table, a bad bind name, a lost connection, PostgreSQL's SQLSTATE and nothing are not
        foreach ( array( '942', '1745', '3113', '55P03', '', null ) as $code )
            $this->assertFalse( expDFSFileHandlerOracleBackend::isRetryableCode( $code ), var_export( $code, true ) );
    }

    public function testLikePattern()
    {
        // % is any text, _ a literal (as in the MySQLi backend), LIKE's own characters in names are literals
        $this->assertSame( 'var/site/cache/a\\_b%', expDFSFileHandlerOracleBackend::likePattern( 'var/site/cache/a_b%' ) );
        $this->assertTrue( self::likeMatches( expDFSFileHandlerOracleBackend::likePattern( 'var/a_b%' ), 'var/a_b/c.txt' ) );
        $this->assertFalse( self::likeMatches( expDFSFileHandlerOracleBackend::likePattern( 'var/a_b%' ), 'var/aXb/c.txt' ) );
        // the escape of the callers (addcslashes( $like, '_' )) makes the next character a literal
        $this->assertSame( 'a\\_b\\%c', expDFSFileHandlerOracleBackend::likePattern( 'a\\_b\\%c' ) );
        $this->assertTrue( self::likeMatches( expDFSFileHandlerOracleBackend::likePattern( 'a\\_b\\%c' ), 'a_b%c' ) );
        $this->assertFalse( self::likeMatches( expDFSFileHandlerOracleBackend::likePattern( 'a\\_b\\%c' ), 'a_bXc' ) );
    }

    public function testLikePrefix()
    {
        $this->assertSame( 'var/100\\%\\_x%', expDFSFileHandlerOracleBackend::likePrefix( 'var/100%_x' ) );
        $this->assertTrue( self::likeMatches( expDFSFileHandlerOracleBackend::likePrefix( 'c\\d' ), 'c\\d.txt' ) );
        $this->assertFalse( self::likeMatches( expDFSFileHandlerOracleBackend::likePrefix( 'c\\d' ), 'cd.txt' ) );
    }

    public function testWildcardToLikes()
    {
        $this->assertSame( array( 'var/a.txt', 'var/b.txt' ), expDFSFileHandlerOracleBackend::wildcardToLikes( 'var/{a,b}.txt' ) );
        $this->assertSame( array( 'var/_.%' ), expDFSFileHandlerOracleBackend::wildcardToLikes( 'var/?.*' ) );
        $this->assertSame( array( 'x/a', 'x/b1', 'x/b2' ), expDFSFileHandlerOracleBackend::wildcardToLikes( 'x/{a,b{1,2}}' ) );
        $this->assertSame( array( 'a,b' ), expDFSFileHandlerOracleBackend::wildcardToLikes( 'a,b' ) );
        $this->assertSame( array( 'x/a', 'x/b' ), expDFSFileHandlerOracleBackend::wildcardToLikes( 'x/{a,b' ) );
        // LIKE's own characters are literals
        $this->assertSame( array( '100\\%\\_\\\\.txt' ), expDFSFileHandlerOracleBackend::wildcardToLikes( '100%_\\.txt' ) );

        $this->expectException( InvalidArgumentException::class );
        expDFSFileHandlerOracleBackend::wildcardToLikes( str_repeat( '{a,b,c}', 6 ) );
    }

    public function testNormalizeRow()
    {
        $row = expDFSFileHandlerOracleBackend::normalizeRow( array( 'NAME' => 'var/x', 'NAME_TRUNK' => 'var/x', 'NAME_HASH' => md5( 'var/x' ), 'DATATYPE' => null,
                                                                    'SCOPE' => null, 'FILESIZE' => '12', 'MTIME' => '-1700000000', 'EXPIRED' => '1' ) );
        $this->assertSame( array( 'name' => 'var/x', 'name_trunk' => 'var/x', 'name_hash' => md5( 'var/x' ), 'datatype' => '', 'scope' => '',
                                  'size' => '12', 'mtime' => '-1700000000', 'expired' => '1' ), $row );
    }

    public function testNameTrunkOfViewCache()
    {
        $this->assertSame( 'var/site/cache/content/site/1/2/2-', expDFSFileHandlerOracleBackend::nameTrunk( 'var/site/cache/content/site/1/2/2-abc.cache', 'viewcache' ) );
        $this->assertSame( 'var/site/storage/x.png', expDFSFileHandlerOracleBackend::nameTrunk( 'var/site/storage/x.png', 'image' ) );
    }

    /**
     * Every public method of the MySQLi backend exists in the Oracle backend, with as many parameters and the same
     * number of required ones: eZDFSFileHandler can use either.
     */
    public function testMethodParityWithTheMySQLiBackend()
    {
        $mysql = new ReflectionClass( 'eZDFSFileHandlerMySQLiBackend' );
        $oracle = new ReflectionClass( 'expDFSFileHandlerOracleBackend' );
        foreach ( $mysql->getMethods( ReflectionMethod::IS_PUBLIC ) as $method )
        {
            $name = $method->getName();
            $this->assertTrue( $oracle->hasMethod( $name ), "missing $name" );
            $other = $oracle->getMethod( $name );
            $this->assertTrue( $other->isPublic(), "$name is not public" );
            $this->assertGreaterThanOrEqual( $method->getNumberOfParameters(), $other->getNumberOfParameters(), $name );
            $this->assertSame( $method->getNumberOfRequiredParameters(), $other->getNumberOfRequiredParameters(), $name );
        }
        foreach ( array( 'eZClusterEventNotifier', 'expDFSFileHandlerDBBackendInterface' ) as $interface )
            $this->assertTrue( $oracle->implementsInterface( $interface ), $interface );
    }

    public function testOracleSqlOnlyInTheCode()
    {
        // the code only: the comments compare with the other engines on purpose
        $source = '';
        foreach ( token_get_all( file_get_contents( self::root() . '/kernel/private/classes/clusterfilehandlers/dfsbackends/oracle.php' ) ) as $token )
        {
            if ( !is_array( $token ) || !in_array( $token[0], array( T_COMMENT, T_DOC_COMMENT ), true ) )
                $source .= is_array( $token ) ? $token[1] : $token;
        }
        foreach ( array( 'ON CONFLICT', ' LIMIT ', 'mysqli_', 'PDO', 'GLOB', 'pg_' ) as $other )
            $this->assertStringNotContainsString( $other, $source );
        foreach ( array( 'MERGE INTO', 'FOR UPDATE WAIT', 'SKIP LOCKED', "ESCAPE '\\\\'", 'FETCH NEXT', 'oci_new_connect' ) as $oracle )
            $this->assertStringContainsString( $oracle, $source );
        // no bind name is a column name: SIZE is a reserved word and ORA-01745 the result
        $this->assertDoesNotMatchRegularExpression( '/:size\b/', $source );
    }

    public function testSchemaFiles()
    {
        $schema = file_get_contents( self::root() . '/kernel/sql/oracle/cluster_dfs_schema.sql' );
        foreach ( array( 'ezdfsfile', 'ezdfsfile_cache' ) as $table )
            $this->assertMatchesRegularExpression( "/CREATE TABLE $table \\(/", $schema );
        $this->assertSame( 4, preg_match_all( '/^\s+(filesize|mtime) NUMBER\(19\) DEFAULT 0 NOT NULL/m', $schema ) );
        $this->assertDoesNotMatchRegularExpression( '/^\s+size\s/m', $schema );
        $this->assertSame( 2, preg_match_all( '/PRIMARY KEY \(name_hash\)/', $schema ) );
        // the statements exp:cluster:enable runs
        $this->assertCount( 10, expClusterSetup::schemaStatements( $schema ) );

        $upgrade = file_get_contents( self::root() . '/kernel/sql/oracle/cluster_dfs_schema_upgrade.sql' );
        $this->assertStringContainsString( 'ALTER TABLE ezdfsfile ADD (name_trunk VARCHAR2(4000))', $upgrade );
        $this->assertStringContainsString( 'CREATE TABLE ezdfsfile_cache', $upgrade );
    }

    public function testGatewaySelection()
    {
        $gateway = file_get_contents( self::root() . '/kernel/clustering/dfsoracle.php' );
        $this->assertStringContainsString( 'class expDfsOracleClusterGateway extends ezpClusterGateway', $gateway );
        $this->assertStringContainsString( "setGatewayClass( 'expDfsOracleClusterGateway' )", $gateway );
        $this->assertStringContainsString( 'SELECT datatype, filesize, mtime', $gateway );
        $classes = require self::root() . '/autoload/ezp_kernel.php';
        $this->assertSame( 'kernel/private/classes/clusterfilehandlers/dfsbackends/oracle.php', $classes['expDFSFileHandlerOracleBackend'] );
        $this->assertSame( 'kernel/clustering/dfsoracle.php', $classes['expDfsOracleClusterGateway'] );
    }
}
