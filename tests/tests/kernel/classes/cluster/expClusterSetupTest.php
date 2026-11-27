<?php
/**
 * exp:cluster:enable and exp:cluster:disable (expClusterSetup): the pure parts, no database.
 *
 *  CS-01 - The plan: one database for every siteaccess, its engine's backend in this kernel, one move per var directory
 *  CS-02 - The INI block goes before the closing comment, keeps the installation's own lines byte for byte, is replaced
 *          and taken out again; a file without a closing comment is refused
 *  CS-03 - Cluster settings that are not ours (the installation's own, a hosting panel's) are seen; values that could
 *          end the PHP comment are refused
 *  CS-04 - config.cluster.php is valid PHP with every value quoted, and is recognized as ours
 *  CS-05 - The schema statements, the default share, the web server rules, the aliases of the commands
 *  CS-06 - The share is writable for its group (the other nodes write as the group over NFS), keeps owner and setgid
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expClusterSetupTest extends PHPUnit\Framework\TestCase
{
    private static function facts( $impl = 'ezmysqli', array $backends = array( 'mysqli' => 'eZDFSFileHandlerMySQLiBackend' ) )
    {
        $db = array( 'DatabaseImplementation' => $impl, 'Server' => 'localhost', 'Port' => '', 'Socket' => 'disabled', 'User' => 'u1', 'Password' => "p'w\"\$x", 'Database' => 'd1' );
        return array( 'default' => 'site', 'backends' => $backends, 'siteaccesses' => array(
            'site' => array( 'db' => $db, 'var' => 'var/site', 'handler' => 'eZFSFileHandler' ),
            'admin' => array( 'db' => $db, 'var' => 'var/site', 'handler' => 'eZFSFileHandler' ) ) );
    }

    public function testThePlan()
    {
        $p = expClusterSetup::plan( self::facts() );
        $this->assertSame( array( 'mysqli', 'dfsmysqli', 'localhost', 3306, '' ), array( $p['engine'], $p['gateway'], $p['host'], $p['port'], $p['socket'] ) );
        $this->assertSame( array( 'var/site' => 'site' ), $p['vardirs'] );
        $f = self::facts();
        $f['siteaccesses']['admin']['db']['Database'] = 'd2';
        $this->assertStringContainsString( 'more than one database', expClusterSetup::plan( $f ) );
        $this->assertStringContainsString( 'no PostgreSQL cluster backend yet', expClusterSetup::plan( self::facts( 'ezpostgresql' ) ) );
        $pg = expClusterSetup::plan( self::facts( 'ezpostgresql', array( 'postgresql' => 'eZDFSFileHandlerPostgresqlBackend' ) ) );
        $this->assertSame( array( 'postgresql', 'dfspostgresql', 5432 ), array( $pg['engine'], $pg['gateway'], $pg['port'] ) );
        $this->assertStringContainsString( 'needs MySQL/MariaDB, PostgreSQL, SQLite, MongoDB, Oracle or SQL Server', expClusterSetup::plan( self::facts( 'eznulldb' ) ) );
        $this->assertStringContainsString( 'not postgresql', expClusterSetup::plan( self::facts(), 'postgresql' ) );
    }

    /** SQLite: a file, no account, no host or port; one node only */
    public function testThePlanOnSQLite()
    {
        $this->assertStringContainsString( 'no SQLite cluster backend yet', expClusterSetup::plan( self::facts( 'sqlite3' ) ) );
        $f = self::facts( 'sqlite3', array( 'sqlite' => 'expDFSFileHandlerSQLiteBackend' ) );
        foreach ( $f['siteaccesses'] as $sa => $s )
            $f['siteaccesses'][$sa]['db'] = array( 'User' => '', 'Password' => '', 'Server' => '', 'Database' => 'site.db' ) + $s['db'];
        $p = expClusterSetup::plan( $f );
        $this->assertSame( array( 'sqlite', 'dfssqlite', 'sqlite', '', 0, '' ), array( $p['engine'], $p['gateway'], $p['sql'], $p['host'], $p['port'], $p['socket'] ) );
        $block = expClusterSetup::iniBlock( $p, '/srv/dfs' );
        $this->assertStringContainsString( "DBBackend=expDFSFileHandlerSQLiteBackend\n", $block );
        $this->assertStringContainsString( "DBHost=\nDBPort=\nDBSocket=\nDBName=site.db\n", $block );
        $this->assertStringContainsString( "define( 'CLUSTER_STORAGE_BACKEND', 'dfssqlite' );", expClusterSetup::gatewayText( $p, '/srv/dfs' ) );
        $this->assertSame( 'sqlite', expClusterSetup::engineOf( 'ezsqlite3' ) );
        $this->assertSame( '', expClusterSetup::engineOf( 'eznulldb' ) );
        $this->assertStringContainsString( 'not mysqli', expClusterSetup::plan( $f, 'mysqli' ) );
    }

    /** MongoDB: collections from a JSON schema, a server that may run without an account */
    public function testThePlanOnMongoDB()
    {
        $this->assertStringContainsString( 'no MongoDB cluster backend yet', expClusterSetup::plan( self::facts( 'mongodb' ) ) );
        $f = self::facts( 'mongodb', array( 'mongodb' => 'expDFSFileHandlerMongoDBBackend' ) );
        foreach ( $f['siteaccesses'] as $sa => $s )
            $f['siteaccesses'][$sa]['db'] = array( 'User' => '', 'Password' => '', 'Server' => 'db.example', 'Port' => '', 'Database' => 'site' ) + $s['db'];
        $p = expClusterSetup::plan( $f );
        $this->assertSame( array( 'mongodb', 'dfsmongodb', 'mongodb', 'db.example', 27017, '' ), array( $p['engine'], $p['gateway'], $p['sql'], $p['host'], $p['port'], $p['socket'] ) );
        $block = expClusterSetup::iniBlock( $p, '/srv/dfs' );
        $this->assertStringContainsString( "DBBackend=expDFSFileHandlerMongoDBBackend\n", $block );
        $this->assertStringContainsString( "DBHost=db.example\nDBPort=27017\nDBSocket=\nDBName=site\n", $block );
        $this->assertStringContainsString( "define( 'CLUSTER_STORAGE_BACKEND', 'dfsmongodb' );", expClusterSetup::gatewayText( $p, '/srv/dfs' ) );
        foreach ( array( 'mongodb', 'expmongodb', 'MongoDB' ) as $implementation )
            $this->assertSame( 'mongodb', expClusterSetup::engineOf( $implementation ) );
        $backends = expClusterSetup::backends();
        $this->assertSame( 'cluster_dfs_schema.json', expClusterSetup::schemaFile( $backends['mongodb'] ) );
        $this->assertSame( 'cluster_dfs_schema.sql', expClusterSetup::schemaFile( $backends['sqlite'] ) );
        $this->assertFileExists( dirname( __DIR__, 5 ) . '/kernel/sql/mongodb/cluster_dfs_schema.json' );
    }

    /** SQL Server: host and port (1433 by default) as the site's database; the tables of kernel/sql/sqlserver */
    public function testThePlanOnSQLServer()
    {
        $this->assertStringContainsString( 'no SQL Server cluster backend yet', expClusterSetup::plan( self::facts( 'sqlserver' ) ) );
        $f = self::facts( 'sqlserver', array( 'sqlserver' => 'expDFSFileHandlerSQLServerBackend' ) );
        foreach ( $f['siteaccesses'] as $sa => $s )
            $f['siteaccesses'][$sa]['db'] = array( 'Server' => 'sql.example', 'Port' => '', 'Socket' => '', 'Database' => 'site' ) + $s['db'];
        $p = expClusterSetup::plan( $f );
        $this->assertSame( array( 'sqlserver', 'dfssqlserver', 'sqlserver', 'sql.example', 1433, '' ), array( $p['engine'], $p['gateway'], $p['sql'], $p['host'], $p['port'], $p['socket'] ) );
        $block = expClusterSetup::iniBlock( $p, '/srv/dfs' );
        $this->assertStringContainsString( "DBBackend=expDFSFileHandlerSQLServerBackend\n", $block );
        $this->assertStringContainsString( "DBHost=sql.example\nDBPort=1433\nDBSocket=\nDBName=site\n", $block );
        $this->assertStringContainsString( "define( 'CLUSTER_STORAGE_BACKEND', 'dfssqlserver' );", expClusterSetup::gatewayText( $p, '/srv/dfs' ) );
        foreach ( array( 'sqlserver', 'mssql', 'sqlsrv', 'expSQLServerDB' ) as $implementation )
            $this->assertSame( 'sqlserver', expClusterSetup::engineOf( $implementation ) );
        $this->assertSame( 'SQL Server', expClusterSetup::engineName( 'sqlserver' ) );
        // the gateway gets the layer and the encryption of the site's connection
        $p['sqlserver'] = array( 'Layer' => 'pdo_odbc', 'ODBCDriver' => 'ODBC Driver 18 for SQL Server', 'Encrypt' => 'strict', 'TrustServerCertificate' => 'enabled' );
        $gateway = expClusterSetup::gatewayText( $p, '/srv/dfs' );
        $this->assertStringContainsString( "define( 'CLUSTER_SQLSERVER_LAYER', 'pdo_odbc' );", $gateway );
        $this->assertStringContainsString( "define( 'CLUSTER_SQLSERVER_ENCRYPT', 'strict' );", $gateway );
        $this->assertStringContainsString( "define( 'CLUSTER_SQLSERVER_TRUST_SERVER_CERTIFICATE', true );", $gateway );
        $this->assertStringContainsString( "define( 'CLUSTER_STORAGE_PORT', 1433 );", $gateway );
        $this->assertFileExists( dirname( __DIR__, 5 ) . '/kernel/sql/sqlserver/cluster_dfs_schema.sql' );
    }

    /**
     * The databases of the PostgreSQL protocol (their drivers' databaseName() is postgresql): the PostgreSQL backend and
     * its gateway, with the database's own port when the site names none; Spanner holds no site and has no cluster
     */
    public function testThePlanOnThePostgreSQLWireDatabases()
    {
        $backends = array( 'postgresql' => 'eZDFSFileHandlerPostgresqlBackend' );
        $cases = array( 'cockroachdb' => 26257, 'expCockroachDB' => 26257, 'crdb' => 26257, 'yugabytedb' => 5433, 'expYugabyteDB' => 5433,
                        'neon' => 5432, 'expNeonDB' => 5432, 'supabase' => 5432, 'expSupabaseDB' => 5432, 'aurorapostgresql' => 5432,
                        'expAuroraPostgreSQLDB' => 5432 );
        foreach ( $cases as $implementation => $port )
        {
            $this->assertSame( 'postgresql', expClusterSetup::engineOf( $implementation ), $implementation );
            $p = expClusterSetup::plan( self::facts( $implementation, $backends ) );
            $this->assertIsArray( $p, $implementation );
            $this->assertSame( array( 'postgresql', 'dfspostgresql', 'postgresql', $port ), array( $p['engine'], $p['gateway'], $p['sql'], $p['port'] ), $implementation );
            $this->assertStringContainsString( "DBBackend=eZDFSFileHandlerPostgresqlBackend\n", expClusterSetup::iniBlock( $p, '/srv/dfs' ) );
        }
        // a Port of the site's settings wins
        $f = self::facts( 'cockroachdb', $backends );
        foreach ( $f['siteaccesses'] as $sa => $s )
            $f['siteaccesses'][$sa]['db']['Port'] = '26300';
        $this->assertSame( 26300, expClusterSetup::plan( $f )['port'] );
        $this->assertSame( '', expClusterSetup::engineOf( 'spanner' ) );
        // CockroachDB takes no operator class on a plain index: the schema leaves text_pattern_ops out there only
        $schema = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/sql/postgresql/cluster_dfs_schema.sql' );
        $this->assertStringNotContainsString( 'text_pattern_ops', expClusterSetup::schemaFor( $schema, 'cockroachdb' ) );
        $this->assertStringContainsString( 'CREATE INDEX ezdfsfile_name ON ezdfsfile (name);', expClusterSetup::schemaFor( $schema, 'expCockroachDB' ) );
        $this->assertSame( $schema, expClusterSetup::schemaFor( $schema, 'yugabytedb' ) );
        $this->assertSame( $schema, expClusterSetup::schemaFor( $schema, 'ezpostgresql' ) );
        $this->assertSame( 4000, expClusterSetup::plan( self::facts( 'tidb', array( 'mysqli' => 'eZDFSFileHandlerMySQLiBackend' ) ) )['port'] );
        // every database of the wire protocol that a site can be installed on has its cluster
        if ( class_exists( 'expPostgreSQLWireProfile' ) )
            foreach ( expPostgreSQLWireProfile::all() as $name => $profile )
                if ( ( $profile['level'] ?? 'site' ) !== 'datatable' )
                    $this->assertSame( 'postgresql', expClusterSetup::engineOf( $name ), $name );
    }

    /** A site over ODBC: the engine is the dialect; PostgreSQL's backend gets the ODBC driver, SQL Server's its layer */
    public function testThePlanOverODBC()
    {
        $backends = array( 'postgresql' => 'expDFSFileHandlerPostgresqlBackend', 'sqlserver' => 'expDFSFileHandlerSQLServerBackend',
                           'mysqli' => 'eZDFSFileHandlerMySQLiBackend' );
        $f = self::facts( 'odbc', $backends );
        foreach ( $f['siteaccesses'] as $sa => $s )
            $f['siteaccesses'][$sa]['db'] += array( 'ODBCDialect' => 'postgresql', 'ODBCDriver' => '' );
        $p = expClusterSetup::plan( $f );
        $this->assertSame( array( 'postgresql', 'dfspostgresql', 5432 ), array( $p['engine'], $p['gateway'], $p['port'] ) );
        $p['odbc_driver'] = expClusterSetup::odbcDriver( $p );
        $this->assertSame( expODBCDB::defaultDriver( 'postgresql' ), $p['odbc_driver'] );
        $this->assertStringContainsString( "DBODBCDriver=" . $p['odbc_driver'] . "\n# END", expClusterSetup::iniBlock( $p, '/srv/dfs' ) );
        $this->assertStringContainsString( "define( 'CLUSTER_STORAGE_ODBC_DRIVER', '" . $p['odbc_driver'] . "' );", expClusterSetup::gatewayText( $p, '/srv/dfs' ) );
        foreach ( $f['siteaccesses'] as $sa => $s )
            $f['siteaccesses'][$sa]['db']['ODBCDriver'] = 'PostgreSQL Unicode(x64)';
        $this->assertSame( 'PostgreSQL Unicode(x64)', expClusterSetup::odbcDriver( expClusterSetup::plan( $f ) ) );
        foreach ( array( 'sqlserver' => 'sqlserver', 'mssql' => 'sqlserver', 'mariadb' => 'mysqli', 'mysql' => 'mysqli' ) as $dialect => $engine )
        {
            foreach ( $f['siteaccesses'] as $sa => $s )
                $f['siteaccesses'][$sa]['db']['ODBCDialect'] = $dialect;
            $p = expClusterSetup::plan( $f );
            $this->assertSame( $engine, $p['engine'], $dialect );
            $this->assertSame( '', expClusterSetup::odbcDriver( $p ), $dialect );
            $this->assertStringNotContainsString( 'DBODBCDriver', expClusterSetup::iniBlock( $p + array( 'odbc_driver' => '' ), '/srv/dfs' ) );
        }
        foreach ( $f['siteaccesses'] as $sa => $s )
            $f['siteaccesses'][$sa]['db']['ODBCDialect'] = 'generic';
        $this->assertStringContainsString( 'has no cluster backend', expClusterSetup::plan( $f ) );
        // a native PostgreSQL site never gets an ODBC driver
        $pg = expClusterSetup::plan( self::facts( 'ezpostgresql', $backends ) );
        $this->assertSame( '', expClusterSetup::odbcDriver( $pg ) );
    }

    /** Oracle: the connect string is the database, no host or port; the tables of kernel/sql/oracle */
    public function testThePlanOnOracle()
    {
        $this->assertStringContainsString( 'no Oracle cluster backend yet', expClusterSetup::plan( self::facts( 'ezoracle' ) ) );
        $f = self::facts( 'ezoracle', array( 'oracle' => 'expDFSFileHandlerOracleBackend' ) );
        foreach ( $f['siteaccesses'] as $sa => $s )
            $f['siteaccesses'][$sa]['db'] = array( 'Server' => '', 'Database' => '127.0.0.1:1521/FREEPDB1' ) + $s['db'];
        $p = expClusterSetup::plan( $f );
        $this->assertSame( array( 'oracle', 'dfsoracle', 'oracle', '', 0, '' ), array( $p['engine'], $p['gateway'], $p['sql'], $p['host'], $p['port'], $p['socket'] ) );
        $block = expClusterSetup::iniBlock( $p, '/srv/dfs' );
        $this->assertStringContainsString( "DBBackend=expDFSFileHandlerOracleBackend\n", $block );
        $this->assertStringContainsString( "DBHost=\nDBPort=\nDBSocket=\nDBName=127.0.0.1:1521/FREEPDB1\nDBUser=u1\n", $block );
        $this->assertStringContainsString( "define( 'CLUSTER_STORAGE_BACKEND', 'dfsoracle' );", expClusterSetup::gatewayText( $p, '/srv/dfs' ) );
        foreach ( array( 'ezoracle', 'oracle' ) as $implementation )
            $this->assertSame( 'oracle', expClusterSetup::engineOf( $implementation ) );
        // an account is needed: Oracle has no anonymous login
        foreach ( $f['siteaccesses'] as $sa => $s )
            $f['siteaccesses'][$sa]['db']['User'] = '';
        $this->assertStringContainsString( 'incomplete', expClusterSetup::plan( $f ) );
        $this->assertCount( 10, expClusterSetup::schemaStatements( file_get_contents( dirname( __DIR__, 5 ) . '/kernel/sql/oracle/cluster_dfs_schema.sql' ) ) );
    }

    public function testTheIniBlock()
    {
        $p = expClusterSetup::plan( self::facts() );
        $block = expClusterSetup::iniBlock( $p, '/var/www/vhosts/a.example/dfs' );
        $new = expClusterSetup::iniText( null, $block );
        $this->assertStringStartsWith( "<?php /* #?ini charset=\"utf-8\"?\n", $new );
        $this->assertStringContainsString( "FileHandler=eZDFSFileHandler\n", $new );
        $this->assertTrue( expClusterSetup::iniOurs( $new ) );
        $this->assertFalse( expClusterSetup::iniForeign( $new ) );
        $this->assertSame( '/var/www/vhosts/a.example/dfs', expClusterSetup::iniShare( $new ) );
        $own = "<?php /* #?ini charset=\"utf-8\"?\r\n\r\n[PassThroughSettings]\r\nDefaultContentDisposition=inline\r\n*/ ?>";
        $with = expClusterSetup::iniText( $own, $block );
        $this->assertSame( 1, substr_count( expClusterSetup::iniText( $with, $block ), '# BEGIN Exponential cluster' ) );
        $this->assertSame( $own, expClusterSetup::iniText( $with, '' ) );
        $this->assertNull( expClusterSetup::iniText( "[ClusteringSettings]\n", $block ) );
    }

    public function testSettingsThatAreNotOursAndUnsafeValues()
    {
        $this->assertTrue( expClusterSetup::iniForeign( "<?php /*\n[eZDFSClusteringSettings]\nMountPointPath=/nfs\n*/ ?>" ) );
        $katalyst = "<?php /*\n# BEGIN Katalyst cluster (set in Katalyst)\n[ClusteringSettings]\nFileHandler=eZDFSFileHandler\n# END Katalyst cluster\n*/ ?>";
        $this->assertTrue( expClusterSetup::iniForeign( $katalyst ), 'a hosting panel\'s block is not ours' );
        $this->assertFalse( expClusterSetup::iniOurs( $katalyst ) );
        foreach ( array( "a\nb", 'a*/b', 'a?>b' ) as $bad )
            $this->assertFalse( expClusterSetup::iniSafe( $bad ) );
        $this->assertTrue( expClusterSetup::iniSafe( 'Kx-9aZ_!#%&()' ) );
    }

    public function testConfigClusterPhp()
    {
        $text = expClusterSetup::gatewayText( expClusterSetup::plan( self::facts() ), '/srv/dfs' );
        $this->assertTrue( expClusterSetup::gatewayOurs( $text ) );
        $strings = array();
        foreach ( token_get_all( $text ) as $t )
            if ( is_array( $t ) && $t[0] === T_CONSTANT_ENCAPSED_STRING )
                $strings[] = $t[1];
        $this->assertContains( var_export( "p'w\"\$x", true ), $strings );
        $this->assertStringContainsString( "define( 'CLUSTER_STORAGE_BACKEND', 'dfsmysqli' );", $text );
        $this->assertFalse( expClusterSetup::gatewayOurs( "<?php\ndefine( 'CLUSTER_STORAGE_BACKEND', 'dfsmysqli' );\n" ) );
    }

    public function testTheKickstartersClusterChoice()
    {
        require_once eZSys::rootDir() . '/tests/tests/kernel/private/classes/commands/expApplyFlagInstallerCommandsTest.php';
        $this->assertSame( array( 'mode' => 'single', 'share' => '' ), expKickstarter::clusterChoice( new expApplyFlagInstallerFakeIni( array() ) ) );
        $ini = new expApplyFlagInstallerFakeIni( array( 'cluster' => array( 'Mode' => 'One-Node', 'Share' => '/mnt/e1/dfs/a.example' ) ) );
        $this->assertSame( array( 'mode' => 'one-node', 'share' => '/mnt/e1/dfs/a.example' ), expKickstarter::clusterChoice( $ini ) );
        $this->assertStringContainsString( 'one-node DFS cluster after the install', implode( "\n", expKickstarter::planLines( $ini ) ) );
        $this->assertStringContainsString( 'single or one-node', expKickstarter::clusterChoice( new expApplyFlagInstallerFakeIni( array( 'cluster' => array( 'Mode' => 'two' ) ) ) ) );
        $this->assertStringContainsString( 'absolute path', expKickstarter::clusterChoice( new expApplyFlagInstallerFakeIni( array( 'cluster' => array( 'Mode' => 'one-node', 'Share' => 'dfs' ) ) ) ) );
    }

    public function testTheShareIsWritableForItsGroup()
    {
        $this->assertSame( 02770, expClusterSetup::shareMode( 02750 ) );
        $this->assertSame( 0770, expClusterSetup::shareMode( 0700 ) );
        $this->assertSame( 02775, expClusterSetup::shareMode( 02775 ) );
        $this->assertSame( 0775, expClusterSetup::shareMode( 0755 ) );
        $this->assertSame( 0, expClusterSetup::shareMode( 0750 ) & 0007 );
        $this->assertSame( 02770, expClusterSetup::shareMode( 0102750 ) );
    }

    public function testSchemaShareRulesAndAliases()
    {
        $root = eZSys::rootDir();
        $st = expClusterSetup::schemaStatements( (string)file_get_contents( $root . '/kernel/sql/mysql/cluster_dfs_schema.sql' ) );
        $this->assertSame( array( 'ezdfsfile', 'ezdfsfile_cache' ), array_column( $st, 'name' ) );
        $this->assertSame( '/var/www/vhosts/a.example/dfs', expClusterSetup::defaultShare( '/var/www/vhosts/a.example/httpdocs/' ) );
        $rules = expClusterSetup::webServerRules();
        $this->assertStringContainsString( '[PT,END]', $rules['apache-vhost'] );
        // the public cache (packed scripts and stylesheets) lives in the cluster too: every form sends it to the gateway
        foreach ( $rules as $where => $rule )
        {
            $this->assertMatchesRegularExpression( '#cache/\(texttoimage\|public\)\)?/#', $rule, "$where: the public cache goes to the gateway" );
            $this->assertStringContainsString( 'storage/images(-versioned)?', $rule, "$where: the stored images go to the gateway" );
        }
        $this->assertSame( 1, preg_match( '#^/var/([^/]+/)?(storage/images(-versioned)?|cache/(texttoimage|public))/#', '/var/site/cache/public/javascript/a_1.js' ) );
        foreach ( array( 'enable', 'disable' ) as $n )
        {
            $this->assertStringContainsString( '@alias exp:cluster:' . $n, (string)file_get_contents( $root . '/bin/php/expcluster' . $n . '.php' ) );
            $this->assertStringContainsString( '@alias exp:cluster:' . $n, (string)file_get_contents( $root . '/kernel/private/classes/commands/expcluster' . $n . '.php' ) );
        }
    }

    /**
     * The shipped rewrite rules (.htaccess_root, .htaccess_root_static) send the public cache of a cluster to the
     * gateway too, in the cluster block, before the rule that serves var/<site>/cache/(texttoimage|public) from disk:
     * on a DFS cluster the packed scripts and stylesheets are on the shared storage, and a node that did not make one
     * answered 404 for it.
     */
    public function testTheShippedRulesSendThePublicCacheOfAClusterToTheGateway()
    {
        $root = eZSys::rootDir();
        foreach ( array( '.htaccess_root', '.htaccess_root_static' ) as $file )
        {
            $lines = file( $root . '/' . $file, FILE_IGNORE_NEW_LINES );
            $cluster = array_search( '#RewriteRule ^var/([^/]+/)?cache/(texttoimage|public)/.* index_cluster.php [L]', $lines, true );
            $static = array_search( 'RewriteRule ^var/([^/]+/)?cache/(texttoimage|public)/.* - [L]', $lines, true );
            $gate = array_search( '#RewriteRule ^index_cluster\\.php - [L]', $lines, true );
            $this->assertIsInt( $cluster, "$file: the cluster rule of the public cache" );
            $this->assertIsInt( $static, "$file: the rule that serves the public cache from disk" );
            $this->assertIsInt( $gate, "$file: the rule that lets index_cluster.php through" );
            $this->assertLessThan( $static, $cluster, "$file: the cluster rule stands before the disk rule" );
            $this->assertLessThan( $gate, $cluster, "$file: the cluster rules stand together, before index_cluster.php's own" );
        }
    }
}
