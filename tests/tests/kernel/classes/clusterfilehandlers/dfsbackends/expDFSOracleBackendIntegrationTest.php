<?php
/**
 * The Oracle DFS cluster backend (expDFSFileHandlerOracleBackend) on a real Oracle database: the parity checks every
 * backend passes (expDFSBackendParityChecks), including four processes on one cache key and a bounded wait for a row
 * lock held by another connection, plus the Oracle gateway of index_cluster.php, the schema's column types, the case
 * sensitivity of the name searches, the LIKE characters in names, Oracle's empty string (NULL) in scopes, and a
 * connection error.
 *
 * Needs an Oracle account (12.1 or later) of its own, with CREATE TABLE: EXP_TEST_DFS_ORACLE_USER,
 * EXP_TEST_DFS_ORACLE_PASSWORD, EXP_TEST_DFS_ORACLE_CONNECT (the connect string, e.g. 127.0.0.1:1521/FREEPDB1). The
 * class creates ezdfsfile and ezdfsfile_cache with kernel/sql/oracle/cluster_dfs_schema.sql in that schema and drops
 * them after the last test; a schema that has those tables already is not touched (the class is skipped). Skipped
 * without the variables or without oci8.
 *   EXP_TEST_DFS_ORACLE_USER=dfstest EXP_TEST_DFS_ORACLE_PASSWORD=... EXP_TEST_DFS_ORACLE_CONNECT=127.0.0.1:1521/FREEPDB1 \
 *     php vendor/bin/phpunit tests/tests/kernel/classes/clusterfilehandlers/dfsbackends/expDFSOracleBackendIntegrationTest.php
 *
 * The rawExec()/rawRows() of the parity checks run as they are, except that the size column is filesize in Oracle
 * (SIZE is a reserved word).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group cluster-live
 */

require_once __DIR__ . '/expDFSBackendParityChecks.php';

#[\PHPUnit\Framework\Attributes\Group('cluster-live')]
class expDFSOracleBackendIntegrationTest extends ezpTestCase
{
    use expDFSBackendParityChecks;

    /** @var array|null user, password, connect */
    private static $account = null;

    /** @var resource|object|null a connection outside the backend */
    private static $raw = null;

    /** @var bool whether the class created the tables (and so drops them) */
    private static $created = false;

    protected function backendClass()
    {
        return 'expDFSFileHandlerOracleBackend';
    }

    protected function supportsBoundedLockRetry()
    {
        return true;
    }

    protected function supportsTimesAfter2038()
    {
        return true;
    }

    /** The row lock of another connection: SELECT ... FOR UPDATE in a transaction */
    protected function holdLock( $holder, $path )
    {
        $stmt = oci_parse( $holder->db, "SELECT name_hash FROM ezdfsfile WHERE name_hash = '" . md5( $path ) . "' FOR UPDATE" );
        oci_execute( $stmt, OCI_NO_AUTO_COMMIT );
    }

    protected function releaseLock( $holder )
    {
        oci_commit( $holder->db );
    }

    /** ORA-30006: resource busy; acquire with WAIT timeout expired */
    protected function lockWaitErrorCode()
    {
        return '30006';
    }

    /** A WAIT that ran out is ORA-30006 up to Oracle 21c and ORA-00054 ("... or timeout expired") on 23ai and later */
    protected function lastBackendErrorCode()
    {
        $code = $this->backend->lastErrorCode();
        return $code === '54' ? '30006' : $code;
    }

    /** Runs one statement on a raw connection, committed; @return resource the statement */
    private static function exec( $db, $sql )
    {
        $stmt = oci_parse( $db, $sql );
        if ( !$stmt || !@oci_execute( $stmt, OCI_COMMIT_ON_SUCCESS ) )
        {
            $e = oci_error( $stmt ? $stmt : $db );
            throw new RuntimeException( ( is_array( $e ) ? $e['message'] : 'oci8 error' ) . " in: $sql" );
        }
        return $stmt;
    }

    protected static function engineSetUp()
    {
        if ( !function_exists( 'oci_new_connect' ) )
            return 'the PHP extension oci8 is not loaded';
        $account = array( 'user' => trim( (string)getenv( 'EXP_TEST_DFS_ORACLE_USER' ) ),
                          'password' => (string)getenv( 'EXP_TEST_DFS_ORACLE_PASSWORD' ),
                          'connect' => trim( (string)getenv( 'EXP_TEST_DFS_ORACLE_CONNECT' ) ) );
        if ( $account['user'] === '' || $account['connect'] === '' )
            return 'EXP_TEST_DFS_ORACLE_USER and EXP_TEST_DFS_ORACLE_CONNECT are not set';
        $db = @oci_new_connect( $account['user'], $account['password'], $account['connect'], 'AL32UTF8' );
        if ( !$db )
        {
            $e = oci_error();
            return 'Cannot connect to the Oracle test schema: ' . ( is_array( $e ) ? $e['message'] : 'oci_new_connect() failed' );
        }
        self::$account = $account;
        self::$raw = $db;
        try
        {
            $stmt = self::exec( $db, "SELECT COUNT(*) FROM user_tables WHERE table_name IN ('EZDFSFILE', 'EZDFSFILE_CACHE')" );
            $row = oci_fetch_array( $stmt, OCI_NUM );
            if ( (int)$row[0] > 0 )
                return 'the test schema has cluster tables already; it is left alone (use a schema of its own)';
            self::$created = true;
            foreach ( expClusterSetup::schemaStatements( file_get_contents( dirname( __DIR__, 6 ) . '/kernel/sql/oracle/cluster_dfs_schema.sql' ) ) as $st )
                self::exec( $db, $st['sql'] );
        }
        catch ( Exception $e )
        {
            return 'Cannot prepare the Oracle test tables: ' . $e->getMessage();
        }
        return null;
    }

    protected static function engineTearDown()
    {
        if ( self::$raw && self::$created )
        {
            // the server ends the sessions of killed lock holders a moment later: wait for their locks
            try
            {
                self::exec( self::$raw, "ALTER SESSION SET DDL_LOCK_TIMEOUT = 30" );
            }
            catch ( Exception $e )
            {
            }
            foreach ( array( 'ezdfsfile', 'ezdfsfile_cache' ) as $table )
            {
                try
                {
                    self::exec( self::$raw, "DROP TABLE $table PURGE" );
                }
                catch ( Exception $e )
                {
                }
            }
        }
        if ( self::$raw )
            @oci_close( self::$raw );
        self::$raw = null;
        self::$created = false;
    }

    protected function engineSettings()
    {
        return array(
            'DBHost' => '',
            'DBPort' => '',
            'DBSocket' => '',
            'DBName' => self::$account['connect'],
            'DBUser' => self::$account['user'],
            'DBPassword' => self::$account['password'],
        );
    }

    protected function rawExec( $sql )
    {
        // SIZE is a reserved word in Oracle: the column is filesize
        $sql = preg_replace_callback( '/^(\s*INSERT\s+INTO\s+\w+\s*\()([^)]*)\)/i', function ( $m )
        {
            return $m[1] . preg_replace( '/\bsize\b/', 'filesize', $m[2] ) . ')';
        }, $sql );
        self::exec( self::$raw, $sql );
    }

    protected function rawRows( $sql )
    {
        $stmt = self::exec( self::$raw, $sql );
        $rows = array();
        while ( ( $row = oci_fetch_array( $stmt, OCI_ASSOC | OCI_RETURN_NULLS ) ) !== false )
            $rows[] = expDFSFileHandlerOracleBackend::normalizeRow( $row );
        return $rows;
    }

    public function testSchemaUses64BitNumbersAndFilesize()
    {
        foreach ( array( 'EZDFSFILE', 'EZDFSFILE_CACHE' ) as $table )
        {
            $types = array();
            foreach ( $this->rawRows( "SELECT column_name, data_type, data_precision FROM user_tab_columns WHERE table_name = '$table'" ) as $row )
                $types[strtolower( $row['column_name'] )] = $row['data_type'] . '(' . $row['data_precision'] . ')';
            $this->assertSame( 'NUMBER(19)', $types['filesize'] );
            $this->assertSame( 'NUMBER(19)', $types['mtime'] );
            $this->assertArrayNotHasKey( 'size', $types );
            $this->assertArrayHasKey( 'name_trunk', $types );
        }
        $meta = null;
        $path = $this->path( 'files/size.txt' );
        $this->backend->_storeContents( $path, '12345', 'binaryfile', 'text/plain', 1700000000 );
        $meta = $this->backend->_fetchMetadata( $path );
        $this->assertSame( '5', $meta['size'], 'filesize is returned as size' );
        $this->assertArrayNotHasKey( 'filesize', $meta );
    }

    /** Names are compared case sensitively, as MySQL's binary LIKE on name compares them */
    public function testNameSearchesAreCaseSensitive()
    {
        $this->backend->_storeContents( $this->path( 'case/Upper.txt' ), 'U', 'binaryfile', 'text/plain', 1700000000 );
        $this->backend->_storeContents( $this->path( 'case/upper.txt' ), 'u', 'binaryfile', 'text/plain', 1700000000 );
        $this->assertTrue( $this->backend->_deleteByLike( $this->path( 'case/up%' ) ) );
        $this->assertTrue( $this->backend->_exists( $this->path( 'case/Upper.txt' ) ) );
        $this->assertFalse( $this->backend->_exists( $this->path( 'case/upper.txt' ) ) );
        $this->assertSame( array( $this->path( 'case/Upper.txt' ) ), $this->backend->_getFileList( false, false, false, $this->path( 'case/U' ) ) );
    }

    /** LIKE's own characters (%, _ and the escape \) in a name are literals of a prefix and of a wildcard */
    public function testLikeCharactersInNamesAreLiterals()
    {
        foreach ( array( '100%.txt', '100X.txt', 'a_b.txt', 'aXb.txt', 'c\\d.txt', 'cd.txt' ) as $name )
            $this->backend->_storeContents( $this->path( "like/$name" ), $name, 'binaryfile', 'text/plain', 1700000000 );
        $this->assertSame( array( $this->path( 'like/100%.txt' ) ), $this->backend->_getFileList( false, false, false, $this->path( 'like/100%' ) ) );
        $this->assertSame( array( $this->path( 'like/a_b.txt' ) ), $this->backend->_getFileList( false, false, false, $this->path( 'like/a_' ) ) );
        $this->assertTrue( $this->backend->_deleteByWildcard( $this->path( 'like/c\\d.t?t' ) ) );
        $this->assertFalse( $this->backend->_exists( $this->path( 'like/c\\d.txt' ) ) );
        $this->assertTrue( $this->backend->_exists( $this->path( 'like/cd.txt' ) ) );
    }

    /** Oracle stores the empty string as NULL: a row with no scope is read as '' and is outside every scope list */
    public function testEmptyScopeIsNullAndReadAsEmpty()
    {
        $path = $this->path( 'cache/gen/null.cache' );
        $this->assertSame( 'ok', $this->backend->_startCacheGeneration( $path, "$path.generating" )['result'] );
        $meta = $this->backend->_fetchMetadata( "$path.generating" );
        $this->assertSame( '', $meta['scope'] );
        $this->assertSame( '', $meta['datatype'] );
        $this->backend->_storeContents( $this->path( 'scoped/a.jpg' ), 'a', 'image', 'image/jpeg', 1700000000 );
        $others = $this->backend->_getFileList( array( 'image' ), true );
        $this->assertContains( "$path.generating", $others, 'NOT IN keeps a row without scope, as the other engines keep scope ""' );
        $this->assertNotContains( $this->path( 'scoped/a.jpg' ), $others );
    }

    /** A schema without the tables: the kernel's TableNotFound exception, as on MySQL */
    public function testAMissingTableIsReported()
    {
        ezpINIHelper::setINISetting( 'file.ini', 'eZDFSClusteringSettings', 'MetaDataTableNameCache', 'ezdfsfile_none' );
        $backend = $this->newBackend();
        $this->expectException( eZDFSFileHandlerTableNotFoundException::class );
        $backend->_fetchMetadata( 'var/site/cache/none/x.cache' );
    }

    public function testAWrongPasswordIsAConnectionError()
    {
        ezpINIHelper::setINISetting( 'file.ini', 'eZDFSClusteringSettings', 'DBPassword', 'wrong' . bin2hex( random_bytes( 4 ) ) );
        $this->expectException( eZClusterHandlerDBNoConnectionException::class );
        $this->newBackend();
    }

    public function testGatewayServesMetadataAndRanges()
    {
        $path = $this->path( 'storage/original/application/file.bin' );
        $data = random_bytes( 150000 );
        $this->backend->_storeContents( $path, $data, 'binaryfile', 'application/octet-stream', 1700000000 );

        if ( !class_exists( 'ezpClusterGateway', false ) )
            require_once dirname( __DIR__, 6 ) . '/kernel/clustering/gateway.php';
        require_once dirname( __DIR__, 6 ) . '/kernel/clustering/dfsoracle.php';
        if ( !defined( 'CLUSTER_MOUNT_POINT_PATH' ) )
            define( 'CLUSTER_MOUNT_POINT_PATH', realpath( $this->mount ) );
        if ( CLUSTER_MOUNT_POINT_PATH !== realpath( $this->mount ) )
            $this->markTestSkipped( 'CLUSTER_MOUNT_POINT_PATH is already defined for another mount' );

        $gateway = new expDfsOracleClusterGateway( array( 'user' => self::$account['user'], 'password' => self::$account['password'],
                                                          'name' => self::$account['connect'], 'charset' => 'utf-8' ) );
        $gateway->connect();
        $meta = $gateway->fetchFileMetadata( $path );
        $this->assertSame( array( 'datatype' => 'application/octet-stream', 'size' => '150000', 'mtime' => '1700000000' ), $meta );
        $this->assertFalse( $gateway->fetchFileMetadata( $this->path( 'storage/none.bin' ) ) );

        ob_start();
        $gateway->passthrough( $path, 150000, 100, 70000 );
        $this->assertSame( substr( $data, 100, 70000 ), ob_get_clean() );
        ob_start();
        $gateway->passthrough( $path, 150000 );
        $this->assertSame( $data, ob_get_clean() );
        $gateway->close();

        $gateway = new expDfsOracleClusterGateway( array( 'user' => self::$account['user'], 'password' => 'wrong', 'name' => self::$account['connect'] ) );
        $this->expectException( RuntimeException::class );
        $gateway->connect();
    }
}
