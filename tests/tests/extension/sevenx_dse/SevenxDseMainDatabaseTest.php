<?php
/**
 * The DS Editor opens the main database of the installation with one click (#16641): extension/sevenx_dse/classes/
 * sevenxdsemaindatabase.php turns site.ini [DatabaseSettings] into AdminNeo's configured server "main", and the SQLite
 * driver lists the tables of that database without SQLite's own. Any other database is opened as before, through
 * AdminNeo's free-form login form with every driver ("Connect to another database", the dse_switch link).
 *
 * - DB-less: the engine of each kernel driver maps to one AdminNeo driver; an SQLite Database value without a path is
 *   the file in var/storage/sqlite3/, as for the kernel (eZSQLite3DB::filePath()); the server entry carries the INI
 *   login for MySQL (port or socket) and PostgreSQL, none for SQLite; an engine AdminNeo has no driver for gives null.
 * - The login form shows no username or password field for a configured server, and the authentication uses the
 *   server's own login (Origin::getCredentials()), never one from the URL.
 * - "main" is the configured server only for requests about it (isRequested()); every other request gets the
 *   free-form form with the driver list, server, login and database; the dashboard offers both choices.
 * - SQLite, with a throwaway database file below var/tmp: the path the server entry names, resolved against the
 *   installation root, opens the file; the driver's table query lists its tables and views and leaves out
 *   sqlite_sequence; PRAGMA table_info, LIMIT/OFFSET and rowid work on it.
 *
 * Skips when extension/sevenx_dse is not installed; SEVENX_DSE_EXTENSION_DIR=<dir> checks a clone instead.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/sevenx_dse/SevenxDseMainDatabaseTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class SevenxDseMainDatabaseTest extends PHPUnit\Framework\TestCase
{
    private static $root;
    private static $ext;
    private $file = '';

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 4 );
        self::$ext = getenv( 'SEVENX_DSE_EXTENSION_DIR' ) ?: self::$root . '/extension/sevenx_dse';
    }

    protected function setUp(): void
    {
        if ( !is_file( self::$ext . '/classes/sevenxdsemaindatabase.php' ) )
        {
            $this->markTestSkipped( 'extension/sevenx_dse with sevenxdsemaindatabase.php is not installed' );
        }
        if ( !class_exists( 'sevenxDseMainDatabase', false ) )
        {
            require_once self::$ext . '/classes/sevenxdsemaindatabase.php';
        }
    }

    protected function tearDown(): void
    {
        foreach ( array( '', '-wal', '-shm', '-journal' ) as $suffix )
        {
            if ( $this->file !== '' && is_file( $this->file . $suffix ) )
            {
                unlink( $this->file . $suffix );
            }
        }
        if ( $this->file !== '' && is_dir( dirname( $this->file ) ) )
        {
            @rmdir( dirname( $this->file ) );
        }
    }

    public function testEachKernelEngineMapsToOneDriver()
    {
        $this->assertSame( 'sqlite', sevenxDseMainDatabase::driverFor( 'sqlite', 'sqlite3' ) );
        $this->assertSame( 'sqlite', sevenxDseMainDatabase::driverFor( '', 'ezsqlite3' ) );
        $this->assertSame( 'mysql', sevenxDseMainDatabase::driverFor( 'mysql', 'ezmysqli' ) );
        $this->assertSame( 'mysql', sevenxDseMainDatabase::driverFor( 'pdo_mysql', 'exppdo' ) );
        $this->assertSame( 'pgsql', sevenxDseMainDatabase::driverFor( 'postgresql', 'ezpostgresql' ) );
        $this->assertSame( 'pgsql', sevenxDseMainDatabase::driverFor( 'pdo_pgsql', '' ) );
        $this->assertSame( 'oracle', sevenxDseMainDatabase::driverFor( 'oracle', 'ezoracle' ) );
        $this->assertSame( 'mongo', sevenxDseMainDatabase::driverFor( 'mongo', 'expmongo' ) );
        // the kernel's connection decides over a stale DatabaseImplementation
        $this->assertSame( 'sqlite', sevenxDseMainDatabase::driverFor( 'sqlite', 'ezmysqli' ) );
        $this->assertSame( '', sevenxDseMainDatabase::driverFor( 'null', 'eznull' ) );
    }

    public function testSqliteFileIsFoundWhereTheKernelFindsIt()
    {
        $this->assertSame( 'var/storage/sqlite3/sqlite.db', sevenxDseMainDatabase::sqlitePath( 'sqlite.db' ) );
        $this->assertSame( '/srv/data/site.db', sevenxDseMainDatabase::sqlitePath( '/srv/data/site.db' ) );
        $kernel = self::$root . '/lib/ezdb/classes/ezsqlite3db.php';
        if ( is_file( $kernel ) )
        {
            $this->assertStringContainsString( "STORAGE_DIRECTORY = '" . sevenxDseMainDatabase::SQLITE_DIRECTORY . "'", (string)file_get_contents( $kernel ) );
        }
    }

    public function testSqliteServerHasNoLogin()
    {
        $server = sevenxDseMainDatabase::server( array( 'DatabaseImplementation' => 'sqlite3', 'Database' => 'sqlite.db',
                                                        'Server' => '', 'User' => '', 'Password' => '' ), 'sqlite', 'Main' );
        $this->assertSame( array( 'driver' => 'sqlite', 'server' => 'var/storage/sqlite3/sqlite.db',
                                  'database' => 'var/storage/sqlite3/sqlite.db', 'username' => '', 'password' => '',
                                  'name' => 'Main' ), $server );
    }

    public function testServerDatabasesCarryTheIniLogin()
    {
        $mysql = sevenxDseMainDatabase::server( array( 'DatabaseImplementation' => 'ezmysqli', 'Server' => 'db.local',
            'Port' => '3307', 'Socket' => 'disabled', 'User' => 'site', 'Password' => 'secret', 'Database' => 'site' ), 'mysql' );
        $this->assertSame( 'mysql', $mysql['driver'] );
        $this->assertSame( 'db.local:3307', $mysql['server'] );
        $this->assertSame( array( 'site', 'secret', 'site' ), array( $mysql['username'], $mysql['password'], $mysql['database'] ) );

        $socket = sevenxDseMainDatabase::server( array( 'DatabaseImplementation' => 'ezmysqli', 'Server' => 'localhost',
            'Port' => '', 'Socket' => '/run/mysqld/mysqld.sock', 'User' => 'u', 'Password' => 'p', 'Database' => 'd' ), '' );
        $this->assertSame( 'localhost:/run/mysqld/mysqld.sock', $socket['server'] );

        $pgsql = sevenxDseMainDatabase::server( array( 'DatabaseImplementation' => 'ezpostgresql', 'Server' => 'pg',
            'Port' => '5432', 'User' => 'u', 'Password' => 'p', 'Database' => 'd' ), 'postgresql' );
        $this->assertSame( array( 'pgsql', 'pg:5432' ), array( $pgsql['driver'], $pgsql['server'] ) );

        $this->assertNull( sevenxDseMainDatabase::server( array( 'DatabaseImplementation' => 'eznull' ), 'null' ) );
    }

    public function testConfiguredServerLoginIsNeverInTheFormOrTheUrl()
    {
        $admin = (string)file_get_contents( self::$ext . '/adminneo/core/Admin.php' );
        $this->assertStringNotContainsString( 'getPassword()', $admin, 'the login form never prints a configured password' );
        $this->assertStringContainsString( 'input_hidden("auth[username]", "") . input_hidden("auth[password]", "")', $admin );

        $origin = (string)file_get_contents( self::$ext . '/adminneo/core/Origin.php' );
        $this->assertStringContainsString( 'return [$server->getServer(), $server->getUsername(), $server->getPassword()];', $origin );

        $config = (string)file_get_contents( self::$ext . '/adminneo/adminneo-config.php' );
        $this->assertStringContainsString( "'servers'", $config );
        $this->assertStringContainsString( 'sevenxDseMainDatabase::current(', $config );

        // The free-form form (another database) never gets the main database's password either: only InsecureUse
        // (DSE_DEFAULT_PASSWORD, off by default) fills it.
        $this->assertStringContainsString( '$_configPassword = defined(\'DSE_DEFAULT_PASSWORD\') ? \DSE_DEFAULT_PASSWORD : "";', $admin );
    }

    public function testMainIsPreselectedAndOtherDatabasesUseTheFreeFormLogin()
    {
        // "main" is AdminNeo's configured server only for requests about it.
        $this->assertTrue( sevenxDseMainDatabase::isRequested( array( 'sqlite' => 'main', 'db' => 'x' ), array() ) );
        $this->assertTrue( sevenxDseMainDatabase::isRequested( array( 'mysql' => 'main' ), array() ) );
        $this->assertTrue( sevenxDseMainDatabase::isRequested( array(), array( 'auth' => array( 'driver' => 'sqlite', 'server' => 'main' ) ) ) );
        $this->assertFalse( sevenxDseMainDatabase::isRequested( array(), array() ), 'the bare login page is the free-form form' );
        $this->assertFalse( sevenxDseMainDatabase::isRequested( array( 'sqlite' => 'var/tmp/other.db' ), array() ) );
        $this->assertFalse( sevenxDseMainDatabase::isRequested( array( 'pgsql' => 'db.local' ), array() ) );
        $this->assertFalse( sevenxDseMainDatabase::isRequested( array( 'sqlite' => 'main' ), array( 'auth' => array( 'driver' => 'mysql', 'server' => 'db.local' ) ) ),
            'a free-form login posted from a main page is a login to that server' );

        $config = (string)file_get_contents( self::$ext . '/adminneo/adminneo-config.php' );
        $this->assertSame( 1, preg_match( '~if \( \$_dseMain !== null && sevenxDseMainDatabase::isRequested\( \$_GET, \$_POST \) \)\s*\{\s*\$_dseConfig\[\'servers\'\]~', $config ),
            'the server list (which hides the driver list and the free-form fields) is set only for requests about main' );

        // The free-form form offers every driver, the server, the login and the database.
        $admin = (string)file_get_contents( self::$ext . '/adminneo/core/Admin.php' );
        foreach ( array( 'html_select("auth[driver]", $drivers, $driver)', 'name="auth[server]"', 'name="auth[username]"', 'name="auth[password]"', 'name="auth[db]"' ) as $field )
        {
            $this->assertStringContainsString( $field, $admin );
        }

        // The dashboard opens main with one click and offers the other databases next to it.
        $gate = (string)file_get_contents( self::$ext . '/design/standard/templates/dse/gate.tpl' );
        $this->assertStringContainsString( 'name="dse_connect" value="main"', $gate );
        $this->assertStringContainsString( 'name="dse_connect" value="other"', $gate );
        $this->assertStringNotContainsString( 'Only its engine', $gate );
        $dashboard = (string)file_get_contents( self::$ext . '/classes/runnable/views/dse/dashboard.php' );
        $this->assertStringContainsString( "\$_POST['dse_connect'] !== 'other'", $dashboard );
        $this->assertStringContainsString( "'/(dse_switch)/'", $dashboard, 'the change-database link is a view parameter' );
    }

    public function testSqliteTablesAreListedWithoutSqlitesOwn()
    {
        if ( !class_exists( 'SQLite3' ) )
        {
            $this->markTestSkipped( 'the sqlite3 extension is not loaded' );
        }
        $relative = 'var/tmp/dse-main-database-test-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) ) . '/site.db';
        $this->file = self::$root . '/' . $relative;
        mkdir( dirname( $this->file ), 0700, true );

        $server = sevenxDseMainDatabase::server( array( 'DatabaseImplementation' => 'sqlite3', 'Database' => $this->file ), 'sqlite' );
        $this->assertSame( array( 'sqlite', $this->file ), array( $server['driver'], $server['server'] ) );

        $db = new SQLite3( $server['server'] );
        $db->exec( 'CREATE TABLE ezsection ( id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, identifier TEXT )' );
        $db->exec( 'CREATE TABLE ezurlalias_ml ( parent INTEGER, text TEXT )' );
        $db->exec( 'CREATE VIEW sections AS SELECT name FROM ezsection' );
        for ( $i = 1; $i <= 5; $i++ )
        {
            $db->exec( "INSERT INTO ezsection ( name, identifier ) VALUES ( 'Section $i', 's$i' )" );
        }

        // The query of the SQLite driver's tables_list(), as it stands in the driver.
        $driver = (string)file_get_contents( self::$ext . '/adminneo/drivers/sqlite.inc.php' );
        $this->assertSame( 1, preg_match( '~function tables_list\(\) \{\s*return get_key_vals\("([^"]+)"\)~', $driver, $m ) );
        $query = stripcslashes( $m[1] );
        $this->assertStringContainsString( "NOT LIKE 'sqlite\\_%' ESCAPE '\\'", $query );

        $tables = array();
        $result = $db->query( $query );
        while ( $row = $result->fetchArray( SQLITE3_NUM ) )
        {
            $tables[$row[0]] = $row[1];
        }
        $this->assertSame( array( 'ezsection' => 'table', 'ezurlalias_ml' => 'table', 'sections' => 'view' ), $tables );
        $this->assertSame( 1, (int)$db->querySingle( "SELECT COUNT(*) FROM sqlite_master WHERE name = 'sqlite_sequence'" ), 'the fixture has an internal table to leave out' );

        // Columns, paging and rowid, as the structure and select pages use them.
        $columns = array();
        $result = $db->query( 'PRAGMA table_info("ezsection")' );
        while ( $row = $result->fetchArray( SQLITE3_ASSOC ) )
        {
            $columns[] = $row['name'];
        }
        $this->assertSame( array( 'id', 'name', 'identifier' ), $columns );
        $this->assertSame( 'Section 3', $db->querySingle( 'SELECT "name" FROM "ezsection" ORDER BY "id" LIMIT 2 OFFSET 2' ) );
        $this->assertTrue( $db->exec( "UPDATE \"ezsection\" SET \"name\" = 'Renamed' WHERE rowid = (SELECT rowid FROM \"ezsection\" WHERE \"identifier\" = 's4' LIMIT 1)" ) );
        $this->assertSame( 'Renamed', $db->querySingle( "SELECT name FROM ezsection WHERE identifier = 's4'" ) );
        $db->close();
    }
}
