<?php
/**
 * The kernel's MySQLi DFS cluster backend (eZDFSFileHandlerMySQLiBackend) through the same parity checks as the
 * PostgreSQL backend (expDFSBackendParityChecks): one behavior on both engines.
 *
 * Needs a server: EXP_TEST_MYCLUSTER_DSN=mysql://<user>[:<password>]@<host>[:<port>], an account that may CREATE
 * and DROP a database. The class makes exp_test_dfs_<pid>_<random> with kernel/sql/mysql/cluster_dfs_schema.sql and
 * drops it after the last test. Skipped without the variable.
 *   EXP_TEST_MYCLUSTER_DSN=mysql://root@127.0.0.20:33380 php vendor/bin/phpunit \
 *       tests/tests/kernel/classes/clusterfilehandlers/dfsbackends/expDFSMySQLiBackendParityTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group cluster-live
 */

require_once __DIR__ . '/expDFSBackendParityChecks.php';

#[\PHPUnit\Framework\Attributes\Group('cluster-live')]
class expDFSMySQLiBackendParityTest extends ezpTestCase
{
    use expDFSBackendParityChecks;

    /** @var array|null */
    private static $server = null;

    /** @var mysqli|null a connection to the test database, outside the backend */
    private static $raw = null;

    protected function backendClass()
    {
        return 'eZDFSFileHandlerMySQLiBackend';
    }

    /** @return mysqli */
    private static function connect( array $server, $database = null )
    {
        return mysqli_connect( $server['host'], $server['user'], $server['password'], $database, $server['port'] );
    }

    protected static function engineSetUp()
    {
        if ( !extension_loaded( 'mysqli' ) )
            return 'the PHP extension mysqli is not loaded';
        $dsn = trim( (string)getenv( 'EXP_TEST_MYCLUSTER_DSN' ) );
        if ( $dsn === '' )
            return 'EXP_TEST_MYCLUSTER_DSN is not set (mysql://user[:password]@host[:port])';
        $parts = parse_url( $dsn );
        if ( !is_array( $parts ) || !isset( $parts['host'] ) )
            return 'EXP_TEST_MYCLUSTER_DSN is not a mysql:// URL';
        $server = array(
            'host' => $parts['host'],
            'port' => isset( $parts['port'] ) ? (int)$parts['port'] : 3306,
            'user' => isset( $parts['user'] ) ? rawurldecode( $parts['user'] ) : 'root',
            'password' => isset( $parts['pass'] ) ? rawurldecode( $parts['pass'] ) : '',
            'database' => 'exp_test_dfs_' . getmypid() . '_' . bin2hex( random_bytes( 3 ) ),
        );
        try
        {
            $admin = self::connect( $server );
            mysqli_query( $admin, "CREATE DATABASE {$server['database']}" );
            mysqli_close( $admin );
            self::$server = $server;
            self::$raw = self::connect( $server, $server['database'] );
            $sql = preg_replace( '/^\s*--.*$/m', '', file_get_contents( dirname( __DIR__, 6 ) . '/kernel/sql/mysql/cluster_dfs_schema.sql' ) );
            foreach ( preg_split( '/;\s*$/m', $sql ) as $statement )
            {
                if ( trim( $statement ) !== '' )
                    mysqli_query( self::$raw, $statement );
            }
        }
        catch ( mysqli_sql_exception $e )
        {
            return 'Cannot prepare a MySQL test database: ' . $e->getMessage();
        }
        return null;
    }

    protected static function engineTearDown()
    {
        if ( self::$raw )
            mysqli_close( self::$raw );
        self::$raw = null;
        if ( self::$server === null )
            return;
        try
        {
            $admin = self::connect( self::$server );
            mysqli_query( $admin, 'DROP DATABASE IF EXISTS ' . self::$server['database'] );
            mysqli_close( $admin );
        }
        catch ( mysqli_sql_exception $e )
        {
            fwrite( STDERR, "Could not drop " . self::$server['database'] . ": " . $e->getMessage() . "\n" );
        }
        self::$server = null;
    }

    protected function engineSettings()
    {
        return array(
            'DBHost' => self::$server['host'],
            'DBPort' => (string)self::$server['port'],
            'DBSocket' => '',
            'DBName' => self::$server['database'],
            'DBUser' => self::$server['user'],
            'DBPassword' => self::$server['password'],
        );
    }

    protected function rawExec( $sql )
    {
        mysqli_query( self::$raw, $sql );
    }

    protected function rawRows( $sql )
    {
        return mysqli_fetch_all( mysqli_query( self::$raw, $sql ), MYSQLI_ASSOC );
    }
}
