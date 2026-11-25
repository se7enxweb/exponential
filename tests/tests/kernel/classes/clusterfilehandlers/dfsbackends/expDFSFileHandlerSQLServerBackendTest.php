<?php
/**
 * The SQL Server DFS cluster backend without a database: the SQL Server errors it retries, LIKE patterns, prefixes and
 * wildcards with ESCAPE '\' (and [ as a literal), rows as the other backends return them (filesize as size, NULL as
 * ''), the :name parameters as ?, the result class, the method for method parity with the MySQLi backend, the schema
 * file, the gateway and its connection settings. The parts that need a database are in
 * expDFSSQLServerBackendIntegrationTest.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDFSFileHandlerSQLServerBackendTest extends ezpTestCase
{
    /** @return string the root of the installation */
    private static function root()
    {
        return dirname( __DIR__, 6 );
    }

    /** Whether a LIKE pattern with ESCAPE '\' matches $name, as SQL Server compares in a BIN2 collation */
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
            elseif ( $char === '[' )
                return null; // an unescaped [ would start a character class: never produced
            else
                $regex .= preg_quote( $char, '/' );
        }
        return preg_match( '/^' . $regex . '$/s', $name ) === 1;
    }

    public function testRetryableCodes()
    {
        // a lock wait that ran out, a deadlock, a snapshot conflict, the duplicate key of two concurrent upserts
        foreach ( array( '1222', 1222, '1205', '3960', '2627', '2601' ) as $code )
            $this->assertTrue( expDFSFileHandlerSQLServerBackend::isRetryableCode( $code ), var_export( $code, true ) );
        // a missing table, a failed login, a syntax error, PostgreSQL's SQLSTATE, Oracle's 30006 and nothing are not
        foreach ( array( '208', '18456', '102', '55P03', '30006', '', null ) as $code )
            $this->assertFalse( expDFSFileHandlerSQLServerBackend::isRetryableCode( $code ), var_export( $code, true ) );
    }

    public function testLikePattern()
    {
        // % is any text, _ a literal (as in the MySQLi backend), LIKE's own characters in names are literals
        $this->assertSame( 'var/site/cache/a\\_b%', expDFSFileHandlerSQLServerBackend::likePattern( 'var/site/cache/a_b%' ) );
        $this->assertTrue( self::likeMatches( expDFSFileHandlerSQLServerBackend::likePattern( 'var/a_b%' ), 'var/a_b/c.txt' ) );
        $this->assertFalse( self::likeMatches( expDFSFileHandlerSQLServerBackend::likePattern( 'var/a_b%' ), 'var/aXb/c.txt' ) );
        $this->assertSame( 'a\\_b\\%c', expDFSFileHandlerSQLServerBackend::likePattern( 'a\\_b\\%c' ) );
        // [ starts a character class in SQL Server's LIKE: escaped, so [1] in a name is a literal
        $this->assertSame( 'img\\[1].png', expDFSFileHandlerSQLServerBackend::likePattern( 'img[1].png' ) );
        $this->assertTrue( self::likeMatches( expDFSFileHandlerSQLServerBackend::likePattern( 'img[1].png' ), 'img[1].png' ) );
    }

    public function testLikePrefix()
    {
        $this->assertSame( 'var/100\\%\\_\\[x%', expDFSFileHandlerSQLServerBackend::likePrefix( 'var/100%_[x' ) );
        $this->assertTrue( self::likeMatches( expDFSFileHandlerSQLServerBackend::likePrefix( 'c\\d' ), 'c\\d.txt' ) );
        $this->assertFalse( self::likeMatches( expDFSFileHandlerSQLServerBackend::likePrefix( 'c\\d' ), 'cd.txt' ) );
    }

    public function testWildcardToLikes()
    {
        $this->assertSame( array( 'var/a.txt', 'var/b.txt' ), expDFSFileHandlerSQLServerBackend::wildcardToLikes( 'var/{a,b}.txt' ) );
        $this->assertSame( array( 'var/_.%' ), expDFSFileHandlerSQLServerBackend::wildcardToLikes( 'var/?.*' ) );
        $this->assertSame( array( 'x/a', 'x/b1', 'x/b2' ), expDFSFileHandlerSQLServerBackend::wildcardToLikes( 'x/{a,b{1,2}}' ) );
        $this->assertSame( array( 'a,b' ), expDFSFileHandlerSQLServerBackend::wildcardToLikes( 'a,b' ) );
        $this->assertSame( array( 'x/a', 'x/b' ), expDFSFileHandlerSQLServerBackend::wildcardToLikes( 'x/{a,b' ) );
        $this->assertSame( array( '100\\%\\_\\\\\\[.txt' ), expDFSFileHandlerSQLServerBackend::wildcardToLikes( '100%_\\[.txt' ) );

        $this->expectException( InvalidArgumentException::class );
        expDFSFileHandlerSQLServerBackend::wildcardToLikes( str_repeat( '{a,b,c}', 6 ) );
    }

    public function testNormalizeRow()
    {
        $row = expDFSFileHandlerSQLServerBackend::normalizeRow( array( 'name' => 'var/x', 'name_trunk' => 'var/x', 'name_hash' => md5( 'var/x' ), 'datatype' => null,
                                                                       'scope' => null, 'filesize' => '12', 'mtime' => '-1700000000', 'expired' => '1' ) );
        $this->assertSame( array( 'name' => 'var/x', 'name_trunk' => 'var/x', 'name_hash' => md5( 'var/x' ), 'datatype' => '', 'scope' => '',
                                  'size' => '12', 'mtime' => '-1700000000', 'expired' => '1' ), $row );
    }

    public function testNameTrunkOfViewCache()
    {
        $this->assertSame( 'var/site/cache/content/site/1/2/2-', expDFSFileHandlerSQLServerBackend::nameTrunk( 'var/site/cache/content/site/1/2/2-abc.cache', 'viewcache' ) );
        $this->assertSame( 'var/site/storage/x.png', expDFSFileHandlerSQLServerBackend::nameTrunk( 'var/site/storage/x.png', 'image' ) );
    }

    /** :name parameters become ? in order (one used twice is bound twice); names in literals stay */
    public function testPositionalParameters()
    {
        $this->assertSame( array( "SELECT * FROM t WHERE a = ? AND b LIKE ? ESCAPE '\\' AND c = ?", array( '1', 'x%', '1' ) ),
                           expDFSFileHandlerSQLServerBackend::positionalParameters( "SELECT * FROM t WHERE a = :a AND b LIKE :p ESCAPE '\\' AND c = :a",
                                                                                    array( ':a' => 1, ':p' => 'x%' ) ) );
        $this->assertSame( array( "SELECT ':a'", array() ), expDFSFileHandlerSQLServerBackend::positionalParameters( "SELECT ':a'", array() ) );
        $this->assertSame( array( "SELECT ':a', ?", array( '5' ) ), expDFSFileHandlerSQLServerBackend::positionalParameters( "SELECT ':a', :b", array( ':b' => 5, ':a' => 1 ) ) );
        $this->assertSame( array( 'SELECT ?', array( null ) ), expDFSFileHandlerSQLServerBackend::positionalParameters( 'SELECT :n', array( ':n' => null ) ) );
    }

    public function testResult()
    {
        $result = new expDFSSQLServerResult( array( array( 'a' => '1', 'b' => null ), array( 'a' => '2', 'b' => 'x' ) ), 0 );
        $this->assertSame( 2, $result->affected(), 'a SELECT counts its rows' );
        $this->assertSame( array( 'a' => '1', 'b' => null ), $result->fetchAssoc() );
        $this->assertSame( array( '2', 'x' ), $result->fetchNum() );
        $this->assertFalse( $result->fetchAssoc() );
        $this->assertSame( 3, ( new expDFSSQLServerResult( array(), 3 ) )->affected() );
        $this->assertSame( 0, ( new expDFSSQLServerResult( array(), 0 ) )->affected() );
    }

    /**
     * Every public method of the MySQLi backend exists in the SQL Server backend, with as many parameters and the
     * same number of required ones: eZDFSFileHandler can use either.
     */
    public function testMethodParityWithTheMySQLiBackend()
    {
        $mysql = new ReflectionClass( 'eZDFSFileHandlerMySQLiBackend' );
        $sqlserver = new ReflectionClass( 'expDFSFileHandlerSQLServerBackend' );
        foreach ( $mysql->getMethods( ReflectionMethod::IS_PUBLIC ) as $method )
        {
            $name = $method->getName();
            $this->assertTrue( $sqlserver->hasMethod( $name ), "missing $name" );
            $other = $sqlserver->getMethod( $name );
            $this->assertTrue( $other->isPublic(), "$name is not public" );
            $this->assertGreaterThanOrEqual( $method->getNumberOfParameters(), $other->getNumberOfParameters(), $name );
            $this->assertSame( $method->getNumberOfRequiredParameters(), $other->getNumberOfRequiredParameters(), $name );
        }
        foreach ( array( 'eZClusterEventNotifier', 'expDFSFileHandlerDBBackendInterface' ) as $interface )
            $this->assertTrue( $sqlserver->implementsInterface( $interface ), $interface );
    }

    public function testSQLServerSqlOnlyInTheCode()
    {
        // the code only: the comments compare with the other engines on purpose
        $source = '';
        foreach ( token_get_all( file_get_contents( self::root() . '/kernel/private/classes/clusterfilehandlers/dfsbackends/sqlserver.php' ) ) as $token )
        {
            if ( !is_array( $token ) || !in_array( $token[0], array( T_COMMENT, T_DOC_COMMENT ), true ) )
                $source .= is_array( $token ) ? $token[1] : $token;
        }
        foreach ( array( 'ON CONFLICT', ' LIMIT ', 'mysqli_', 'GLOB', 'pg_', 'oci_', 'FOR UPDATE', 'SKIP LOCKED', 'ON DUPLICATE KEY' ) as $other )
            $this->assertStringNotContainsString( $other, $source );
        foreach ( array( 'MERGE ', 'WITH (HOLDLOCK)', 'UPDLOCK', 'READPAST', "ESCAPE '\\\\'", 'TOP ', 'expSQLServerTransport::create' ) as $sqlserver )
            $this->assertStringContainsString( $sqlserver, $source );
        // a connection's row counts: the session setup switches NOCOUNT on, the backend needs it off
        $this->assertStringContainsString( 'SET NOCOUNT OFF', $source );
    }

    public function testSchemaFile()
    {
        $schema = file_get_contents( self::root() . '/kernel/sql/sqlserver/cluster_dfs_schema.sql' );
        foreach ( array( 'ezdfsfile', 'ezdfsfile_cache' ) as $table )
            $this->assertMatchesRegularExpression( "/CREATE TABLE $table \\(/", $schema );
        $this->assertSame( 4, preg_match_all( '/^\s+(filesize|mtime) BIGINT NOT NULL/m', $schema ) );
        $this->assertDoesNotMatchRegularExpression( '/^\s+size\s/m', $schema );
        // case-sensitive names within the 1,700 byte index key; a key that seeks with the NVARCHAR values of the driver
        $this->assertSame( 4, preg_match_all( '/^\s+name(_trunk)? NVARCHAR\(830\) COLLATE Latin1_General_100_BIN2 NOT NULL/m', $schema ) );
        $this->assertSame( 2, preg_match_all( '/^\s+name_hash VARCHAR\(34\) COLLATE Latin1_General_100_BIN2 NOT NULL/m', $schema ) );
        $this->assertSame( 2, preg_match_all( '/PRIMARY KEY \(name_hash\)/', $schema ) );
        // the statements exp:cluster:enable runs
        $this->assertCount( 10, expClusterSetup::schemaStatements( $schema ) );
    }

    public function testGatewaySelection()
    {
        $gateway = file_get_contents( self::root() . '/kernel/clustering/dfssqlserver.php' );
        $this->assertStringContainsString( 'class expDfsSQLServerClusterGateway extends ezpClusterGateway', $gateway );
        $this->assertStringContainsString( "setGatewayClass( 'expDfsSQLServerClusterGateway' )", $gateway );
        $this->assertStringContainsString( 'SELECT datatype, filesize, mtime', $gateway );
        $classes = require self::root() . '/autoload/ezp_kernel.php';
        $this->assertSame( 'kernel/private/classes/clusterfilehandlers/dfsbackends/sqlserver.php', $classes['expDFSFileHandlerSQLServerBackend'] );
        $this->assertSame( 'kernel/private/classes/clusterfilehandlers/dfsbackends/sqlserverresult.php', $classes['expDFSSQLServerResult'] );
        $this->assertSame( 'kernel/clustering/dfssqlserver.php', $classes['expDfsSQLServerClusterGateway'] );
        // index_cluster.php has no autoloader: every driver file the gateway loads exists and declares its class
        foreach ( expDfsSQLServerClusterGateway::DRIVER_FILES as $class => $file )
        {
            $this->assertFileExists( self::root() . '/' . $file );
            $this->assertSame( $file, $classes[$class], $class );
        }
    }

    public function testGatewayConnectionSettings()
    {
        $s = expDfsSQLServerClusterGateway::connectionSettings( array( 'host' => 'sql.example', 'port' => '', 'name' => 'site', 'user' => 'u', 'password' => 'p' ), array() );
        $this->assertSame( 'auto', $s['layer'] );
        $this->assertSame( array( 'sql.example', 1433, 'site', 'u', 'p', 'yes', false ),
                           array( $s['config']['host'], $s['config']['port'], $s['config']['database'], $s['config']['user'], $s['config']['password'],
                                  $s['config']['encrypt'], $s['config']['trust_server_certificate'] ) );
        $this->assertSame( 0, $s['config']['connect_retry_count'] );

        $s = expDfsSQLServerClusterGateway::connectionSettings( array( 'host' => 'db\\SQLEXPRESS', 'port' => 1433, 'name' => 'site' ),
                                                                 array( 'layer' => 'pdo_odbc', 'encrypt' => 'strict', 'trust_server_certificate' => true ) );
        $this->assertSame( 'pdo_odbc', $s['layer'] );
        $this->assertNull( $s['config']['port'], 'a named instance has no port' );
        $this->assertSame( 'strict', $s['config']['encrypt'] );
        $this->assertTrue( $s['config']['trust_server_certificate'] );
        $this->assertSame( 'localhost', expDfsSQLServerClusterGateway::connectionSettings( array(), array() )['config']['host'] );
    }
}
