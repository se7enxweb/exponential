<?php
/**
 * The DFS cluster of a database of the PostgreSQL protocol, live: the PostgreSQL backend
 * (expDFSFileHandlerPostgresqlBackend) on the tables exp:cluster:enable makes there (expClusterSetup::schemaFor()),
 * through the parity checks every backend passes (expDFSBackendParityChecks): stores, fetches, a file's round trip
 * through the DFS share, renames, deletes and purges, the cache generation and its lock.
 *
 * A test class per database uses this trait and expDFSBackendParityChecks and names its PROFILE. The server is the
 * one of the pgwire-live suite: EXP_TEST_PGWIRE_<PROFILE>_HOST, _PORT, _USER, _PASSWORD, _DATABASE (exp_pgwire_test by
 * default, which must exist). The tables ezdfsfile and ezdfsfile_cache are made in that database and dropped after
 * the class. Skipped without the variables.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 6 ) . '/tests/tests/kernel/classes/clusterfilehandlers/dfsbackends/expDFSBackendParityChecks.php';

trait expDFSPostgreSQLWireLiveSetup
{
    /** @var array|null host, port, user, password, database */
    private static $wireServer = null;

    /** @var PDO|null a connection outside the backend */
    private static $wireRaw = null;

    protected function backendClass()
    {
        return 'expDFSFileHandlerPostgresqlBackend';
    }

    /** @return array|string the server, or why there is none */
    private static function wireServer()
    {
        if ( !extension_loaded( 'pdo_pgsql' ) )
            return 'the PHP extension pdo_pgsql is not loaded';
        $profile = expPostgreSQLWireProfile::get( static::PROFILE );
        $prefix = 'EXP_TEST_PGWIRE_' . strtoupper( static::PROFILE ) . '_';
        $host = getenv( $prefix . 'HOST' );
        if ( $host === false || $host === '' )
            return 'No ' . static::PROFILE . ' server: set ' . $prefix . 'HOST (and _PORT, _USER, _PASSWORD, _DATABASE)';
        $value = function ( $key, $default ) use ( $prefix ) {
            $v = getenv( $prefix . $key );
            return $v === false || $v === '' ? $default : $v;
        };
        return array( 'host' => $host, 'port' => (int)$value( 'PORT', $profile['port'] ), 'user' => $value( 'USER', $profile['user'] ),
                      'password' => $value( 'PASSWORD', '' ), 'database' => $value( 'DATABASE', 'exp_pgwire_test' ) );
    }

    protected static function engineSetUp()
    {
        $server = self::wireServer();
        if ( !is_array( $server ) )
            return $server;
        try
        {
            self::$wireRaw = new PDO( "pgsql:host={$server['host']};port={$server['port']};dbname={$server['database']};sslmode=disable",
                                      $server['user'], $server['password'], array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ) );
            self::$wireServer = $server;
            self::dropTables();
            // the statements exp:cluster:enable runs, as it adapts them to this database
            $schema = expClusterSetup::schemaFor( file_get_contents( dirname( __DIR__, 6 ) . '/kernel/sql/postgresql/cluster_dfs_schema.sql' ), static::PROFILE );
            foreach ( expClusterSetup::schemaStatements( $schema ) as $statement )
                self::$wireRaw->exec( $statement['sql'] );
        }
        catch ( PDOException $e )
        {
            return 'Cannot prepare the ' . static::PROFILE . ' cluster tables: ' . $e->getMessage();
        }
        return null;
    }

    private static function dropTables()
    {
        foreach ( array( 'ezdfsfile', 'ezdfsfile_cache' ) as $table )
            self::$wireRaw->exec( "DROP TABLE IF EXISTS $table" );
    }

    protected static function engineTearDown()
    {
        if ( self::$wireRaw !== null )
        {
            try
            {
                self::dropTables();
            }
            catch ( PDOException $e )
            {
                fwrite( STDERR, 'Could not drop the cluster tables: ' . $e->getMessage() . "\n" );
            }
        }
        self::$wireRaw = null;
        self::$wireServer = null;
    }

    protected function engineSettings()
    {
        return array(
            'DBHost' => self::$wireServer['host'],
            'DBPort' => (string)self::$wireServer['port'],
            'DBSocket' => '',
            'DBName' => self::$wireServer['database'],
            'DBUser' => self::$wireServer['user'],
            'DBPassword' => self::$wireServer['password'],
        );
    }

    protected function rawExec( $sql )
    {
        self::$wireRaw->exec( $sql );
    }

    protected function rawRows( $sql )
    {
        return self::$wireRaw->query( $sql )->fetchAll( PDO::FETCH_ASSOC );
    }

    /** The plan of exp:cluster:enable for a site on this database: the PostgreSQL backend, at the server's port */
    public function testTheClusterCommandTakesThisDatabase()
    {
        $this->assertSame( 'postgresql', expClusterSetup::engineOf( static::PROFILE ) );
        $db = array( 'DatabaseImplementation' => static::PROFILE, 'Server' => self::$wireServer['host'], 'Port' => '', 'Socket' => '',
                     'User' => self::$wireServer['user'] !== '' ? self::$wireServer['user'] : 'u', 'Password' => '', 'Database' => self::$wireServer['database'] );
        $plan = expClusterSetup::plan( array( 'default' => 'site', 'backends' => array( 'postgresql' => 'eZDFSFileHandlerPostgresqlBackend' ),
                                              'siteaccesses' => array( 'site' => array( 'db' => $db, 'var' => 'var/site', 'handler' => 'eZFSFileHandler' ) ) ) );
        $this->assertIsArray( $plan );
        $this->assertSame( array( 'postgresql', 'dfspostgresql', expPostgreSQLWireProfile::get( static::PROFILE )['port'] ),
                           array( $plan['engine'], $plan['gateway'], $plan['port'] ) );
        $tables = array_column( $this->rawRows( "SELECT table_name FROM information_schema.tables WHERE table_name IN ( 'ezdfsfile', 'ezdfsfile_cache' ) ORDER BY table_name" ), 'table_name' );
        $this->assertSame( array( 'ezdfsfile', 'ezdfsfile_cache' ), $tables );
    }
}
