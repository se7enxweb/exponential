<?php
/**
 * The module extension wizard of Setup > RAD (expModuleExtensionWizard), which builds an administration module over
 * database tables: the kinds of database and what each asks for, connection strings, the paths and table names it
 * accepts, reading a schema, columns, keys and class and property names, and the extension it writes for the
 * tables of a SQLite database made for the test under var/tmp (connected the way the wizard connects to an external
 * database, never the installation's own).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expRadWizardTestHelper.php';

class expModuleExtensionWizardTest extends PHPUnit\Framework\TestCase
{
    private $scratch;
    private $file;

    public static function setUpBeforeClass(): void
    {
        expRadWizardTestHelper::boot();
    }

    protected function setUp(): void
    {
        expRadWizardTestHelper::boot();
        $this->scratch = expRadWizardTestHelper::scratch( 'module-extension' );
        $this->file = substr( $this->scratch, strlen( expRadWizardTestHelper::root() ) + 1 ) . '/shop.sqlite';
    }

    protected function tearDown(): void
    {
        expRadWizardTestHelper::removeTree( $this->scratch );
    }

    private function makeDatabase()
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'no sqlite3 extension' );
        $db = new SQLite3( $this->scratch . '/shop.sqlite' );
        $db->exec( 'CREATE TABLE k1e_product ( id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(100) NOT NULL, price DOUBLE, contentobject_id INT, created_at TIMESTAMP )' );
        $db->exec( 'CREATE TABLE k1e_tag ( product_id INT NOT NULL, tag VARCHAR(20) NOT NULL, PRIMARY KEY ( product_id, tag ) )' );
        $db->exec( "INSERT INTO k1e_product ( name, price ) VALUES ( 'one', 1.5 ), ( 'two', 2 )" );
        $db->close();
    }

    private function settings( array $input = array() )
    {
        return expModuleExtensionWizard::settings( $input + array( 'name' => 'k1e_shop_ext', 'source' => 'external', 'db_type' => 'sqlite',
                                                                   'db_file' => $this->file, 'tables' => array( 'k1e_product', 'k1e_tag' ), 'licence' => 'MIT' ) );
    }

    public function testDatabaseKinds()
    {
        $types = expModuleExtensionWizard::databaseTypes();
        $this->assertSame( array( 'mysqli', 'postgresql', 'sqlite', 'mongodb', 'oracle', 'odbc' ), array_keys( $types ) );
        $capabilities = expModuleExtensionWizard::databaseCapabilities();
        $this->assertSame( array_keys( $types ), array_keys( $capabilities ) );
        $this->assertTrue( $capabilities['sqlite']['available'] );
        $this->assertSame( 'ez', $capabilities['sqlite']['via'] );
        $this->assertSame( 'eZSQLite3DB', $capabilities['sqlite']['handler'] );
        foreach ( $capabilities as $key => $capability )
            if ( !$capability['available'] )
                $this->assertNotSame( '', $capability['why'], $key );
        $this->assertSame( array( 'current', 'external' ), array_keys( expModuleExtensionWizard::sources() ) );
    }

    public function testFieldsAskedForEachKind()
    {
        $sqlite = expModuleExtensionWizard::fieldsFor( 'sqlite' );
        $this->assertTrue( $sqlite['db_file']['show'] );
        $this->assertFalse( $sqlite['db_server']['show'] );
        $this->assertFalse( $sqlite['db_name']['show'] );
        $mongo = expModuleExtensionWizard::fieldsFor( 'mongodb' );
        $this->assertTrue( $mongo['db_sample']['show'] );
        $this->assertSame( 'Service name or SID', expModuleExtensionWizard::fieldsFor( 'oracle' )['db_name']['label'] );
        $this->assertFalse( expModuleExtensionWizard::fieldsFor( 'odbc' )['db_port']['show'] );
        $this->assertSame( expModuleExtensionWizard::fieldsFor( 'mysqli' ), expModuleExtensionWizard::fieldsFor( 'nonsense' ) );
    }

    public static function dsnProvider()
    {
        return array(
            'mysql' => array( array( 'db_type' => 'mysqli', 'db_name' => 'shop' ), 'mysql:host=localhost;port=3306;dbname=shop;charset=utf8mb4' ),
            'mysql with server and port' => array( array( 'db_type' => 'mysqli', 'db_name' => 'shop', 'db_server' => 'db.k1e.example.invalid', 'db_port' => 3307 ),
                                                   'mysql:host=db.k1e.example.invalid;port=3307;dbname=shop;charset=utf8mb4' ),
            'postgresql' => array( array( 'db_type' => 'postgresql', 'db_name' => 'shop' ), 'pgsql:host=localhost;port=5432;dbname=shop' ),
            'oracle' => array( array( 'db_type' => 'oracle', 'db_name' => 'ORCLPDB1' ), 'oci:dbname=//localhost:1521/ORCLPDB1;charset=AL32UTF8' ),
            'oracle without a name' => array( array( 'db_type' => 'oracle' ), false ),
            'odbc' => array( array( 'db_type' => 'odbc', 'db_name' => 'shopdsn' ), 'odbc:shopdsn' ),
            'odbc without a name' => array( array( 'db_type' => 'odbc' ), false ),
            'mongodb' => array( array( 'db_type' => 'mongodb', 'db_name' => 'shop' ), false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dsnProvider')]
    public function testDsn( array $input, $expected )
    {
        $settings = expModuleExtensionWizard::settings( $input + array( 'name' => 'k1e_shop_ext', 'source' => 'external' ) );
        $types = expModuleExtensionWizard::databaseTypes();
        $this->assertSame( $expected, expModuleExtensionWizard::dsn( $settings, $types[$settings['db_type']] ) );
    }

    public function testSqliteDsnAndTarget()
    {
        $this->makeDatabase();
        $settings = $this->settings();
        $this->assertSame( 'sqlite:' . expModuleExtensionWizard::installationRoot() . '/' . $this->file, expModuleExtensionWizard::dsn( $settings, expModuleExtensionWizard::databaseTypes()['sqlite'] ) );
        $this->assertSame( $this->file, expModuleExtensionWizard::describeTarget( $settings ) );
        $this->assertSame( 'a file', expModuleExtensionWizard::describeTarget( $this->settings( array( 'db_file' => '' ) ) ) );
        $this->assertSame( 'shop on db.k1e.example.invalid', expModuleExtensionWizard::describeTarget( array( 'db_type' => 'mysqli', 'db_name' => 'shop', 'db_server' => 'db.k1e.example.invalid' ) ) );
        $this->assertSame( 'the database', expModuleExtensionWizard::describeTarget( array( 'db_type' => 'mysqli', 'db_name' => '', 'db_server' => '' ) ) );
        $this->assertSame( 'the data source dsn1', expModuleExtensionWizard::describeTarget( array( 'db_type' => 'odbc', 'db_name' => 'dsn1' ) ) );
    }

    public function testSafePathStaysInsideTheInstallation()
    {
        $this->makeDatabase();
        $this->assertSame( $this->file, expModuleExtensionWizard::safePath( $this->file ) );
        $this->assertSame( $this->file, expModuleExtensionWizard::safePath( '  ' . expModuleExtensionWizard::installationRoot() . '/' . $this->file . ' ' ) );
        $this->assertSame( '', expModuleExtensionWizard::safePath( 'var/tmp/../tmp/x' ) );
        $this->assertSame( '', expModuleExtensionWizard::safePath( '/etc/passwd' ) );
        $this->assertSame( '', expModuleExtensionWizard::safePath( 'no/such/file.db' ) );
        $this->assertSame( '', expModuleExtensionWizard::safePath( "x\0y" ) );
        $this->assertSame( '', expModuleExtensionWizard::safePath( array() ) );
        $this->assertSame( str_replace( '/', '/', $this->file ), expModuleExtensionWizard::safePath( str_replace( '/', '\\', $this->file ) ) );
    }

    public function testSafePathRefusesADirectoryBesideTheInstallationWithTheSameStart()
    {
        // An installation at /x/site sits beside /x/site-old or /x/site--backup on many machines; a path in one of
        // those is not inside the installation, however its name starts
        $root = expModuleExtensionWizard::installationRoot();
        $sibling = null;
        foreach ( glob( $root . '?*', GLOB_ONLYDIR ) ?: array() as $directory )
        {
            foreach ( glob( $directory . '/*' ) ?: array() as $candidate )
            {
                if ( is_file( $candidate ) && is_readable( $candidate ) )
                {
                    $sibling = $candidate;
                    break 2;
                }
            }
        }
        if ( $sibling === null )
            $this->markTestSkipped( 'no directory beside the installation whose name starts like it' );
        $this->assertSame( '', expModuleExtensionWizard::safePath( $sibling ), $sibling );
    }

    public function testTablesAndPrefixes()
    {
        $this->assertSame( array( 'a', 'b_2' ), expModuleExtensionWizard::safeTables( array( ' a ', 'b_2', 'a', '1x', 'x;drop', array( 'y' ), 7 ) ) );
        $this->assertSame( array( 'one' ), expModuleExtensionWizard::safeTables( 'one' ) );
        $this->assertSame( array(), expModuleExtensionWizard::safeTables( null ) );
        $this->assertCount( 40, expModuleExtensionWizard::safeTables( array_map( function ( $i ) { return "t$i"; }, range( 1, 50 ) ) ) );
        $this->assertSame( 'MyShop2', expModuleExtensionWizard::safePrefix( 'my-Shop_2' ) === 'myShop2' ? 'MyShop2' : expModuleExtensionWizard::safePrefix( 'MyShop2' ) );
        $this->assertSame( 'myShop2', expModuleExtensionWizard::safePrefix( 'my-Shop_2' ) );
        $this->assertSame( '', expModuleExtensionWizard::safePrefix( '2shop' ) );
        $this->assertSame( str_repeat( 'a', 20 ), expModuleExtensionWizard::safePrefix( str_repeat( 'a', 30 ) ) );
        $this->assertSame( '', expModuleExtensionWizard::safePrefix( null ) );
    }

    public static function propertyProvider()
    {
        return array(
            array( 'contentobject_id', 'ContentObjectID' ), array( 'name', 'Name' ), array( 'created_at', 'CreatedAt' ),
            array( 'rss_url', 'RSSURL' ), array( 'parentnode__md5', 'ParentNodeMD5' ), array( '', 'Field' ), array( '__', 'Field' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('propertyProvider')]
    public function testPropertyName( $column, $expected )
    {
        $this->assertSame( $expected, expModuleExtensionWizard::propertyName( $column ) );
    }

    public static function datatypeProvider()
    {
        return array( array( 'auto_increment', 'integer' ), array( 'BIGINT', 'integer' ), array( 'timestamp', 'integer' ), array( 'decimal', 'float' ),
                      array( 'money', 'float' ), array( 'varchar', 'string' ), array( 'longtext', 'string' ), array( null, 'string' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('datatypeProvider')]
    public function testDatatypeOf( $type, $expected )
    {
        $this->assertSame( $expected, expModuleExtensionWizard::datatypeOf( $type ) );
    }

    public function testColumnsKeysAndClassNames()
    {
        $table = array( 'fields' => array( 'id' => array( 'type' => 'auto_increment' ), 'title' => array( 'type' => 'varchar', 'length' => 80, 'not_null' => '1', 'default' => 'x' ),
                                           'bad name' => array( 'type' => 'int' ), 'score' => array( 'type' => 'double' ) ),
                        'indexes' => array( 'PRIMARY' => array( 'fields' => array( 'id' ) ) ) );
        $columns = expModuleExtensionWizard::columns( $table );
        $this->assertSame( array( 'id', 'title', 'score' ), array_column( $columns, 'name' ) );
        $this->assertSame( array( 'name' => 'title', 'property' => 'Title', 'datatype' => 'string', 'db_type' => 'varchar', 'length' => 80,
                                  'required' => true, 'default' => 'x', 'primary' => false, 'increment' => false ), $columns[1] );
        $this->assertTrue( $columns[0]['increment'] );
        $this->assertSame( array( 'id' ), expModuleExtensionWizard::keys( $columns ) );
        $this->assertSame( 'id', expModuleExtensionWizard::incrementKey( $columns ) );
        // no primary key: the first column stands in
        $plain = expModuleExtensionWizard::columns( array( 'fields' => array( 'a' => array(), 'b' => array() ) ) );
        $this->assertSame( array( 'a' ), expModuleExtensionWizard::keys( $plain ) );
        $this->assertFalse( expModuleExtensionWizard::incrementKey( $plain ) );
        $this->assertSame( array(), expModuleExtensionWizard::keys( array() ) );
        $this->assertSame( 'shopContentObjectTag', expModuleExtensionWizard::className( array( 'prefix' => 'shop' ), 'contentobject_tag' ) );
    }

    public function testSettingsAndProblems()
    {
        $settings = expModuleExtensionWizard::settings( array( 'name' => 'k1e_shop_ext', 'db_sample' => '9999', 'db_port' => 'x', 'source' => 'nonsense', 'db_type' => 'nonsense', 'db_password' => array( 'x' ) ) );
        $this->assertSame( 'current', $settings['source'] );
        $this->assertSame( 'mysqli', $settings['db_type'] );
        $this->assertSame( 500, $settings['db_sample'] );
        $this->assertSame( 0, $settings['db_port'] );
        $this->assertSame( '', $settings['db_password'] );
        $this->assertSame( 'k1e_shop_ext', $settings['module'] );
        $this->assertSame( 'k1eshopext', $settings['prefix'] );
        $this->assertSame( 'Administration for 0 database table(s), through K1e Shop Ext.', $settings['summary'] );
        $this->assertSame( 1, expModuleExtensionWizard::settings( array( 'db_sample' => '-4' ) )['db_sample'] );

        $text = implode( ' | ', expModuleExtensionWizard::problems( $settings + array() ) );
        $this->assertStringContainsString( 'at least one table', $text );

        $external = implode( ' | ', expModuleExtensionWizard::problems( expModuleExtensionWizard::settings( array( 'name' => 'k1e_shop_ext', 'source' => 'external', 'db_type' => 'mysqli', 'tables' => 'a' ) ) ) );
        $this->assertStringContainsString( 'needs a database name', $external );
        $this->assertStringContainsString( 'needs a server', $external );
        $this->assertStringContainsString( 'Choose the database file', implode( ' ', expModuleExtensionWizard::problems( $this->settings( array( 'db_file' => '' ) ) ) ) );
        $this->assertStringContainsString( 'data source name', implode( ' ', expModuleExtensionWizard::problems( expModuleExtensionWizard::settings( array( 'name' => 'k1e_shop_ext', 'source' => 'external', 'db_type' => 'odbc', 'tables' => 'a' ) ) ) ) );
    }

    public function testConnectionToTheSqliteFileAndItsSchema()
    {
        $this->makeDatabase();
        $settings = $this->settings();
        $this->assertSame( array(), expModuleExtensionWizard::problems( $settings ) );
        $connection = expModuleExtensionWizard::connection( $settings );
        $this->assertTrue( $connection['ok'], $connection['message'] );
        $this->assertSame( 'ez', $connection['via'] );
        $this->assertNotSame( eZDB::hasInstance() ? eZDB::instance() : null, $connection['db'], 'the wizard never replaces the installation\'s own connection' );

        $schema = expModuleExtensionWizard::schema( $connection );
        $this->assertSame( array( 'k1e_product', 'k1e_tag' ), array_keys( $schema ) );
        $this->assertSame( 'auto_increment', $schema['k1e_product']['fields']['id']['type'] );
        $this->assertSame( array( 'product_id', 'tag' ), $schema['k1e_tag']['indexes']['PRIMARY']['fields'] );

        $summaries = expModuleExtensionWizard::summaries( $connection['db'], $schema, array( 'k1e_product' ) );
        $this->assertSame( array( 'name' => 'k1e_product', 'columns' => 5, 'keys' => 'id', 'has_key' => true, 'increment' => true, 'rows' => '2', 'selected' => true, 'class' => 'k1e_product' ), $summaries[0] );
        $this->assertSame( '', $summaries[1]['rows'] );
        $this->assertSame( 'product_id, tag', $summaries[1]['keys'] );
        $this->assertSame( array(), expModuleExtensionWizard::schema( array( 'ok' => false ) ) );
    }

    public function testTheExtensionForTheTables()
    {
        $this->makeDatabase();
        $settings = $this->settings( array( 'parts' => array_keys( expModuleExtensionWizard::parts() ) ) );
        $files = expModuleExtensionWizard::files( $settings );
        $this->assertArrayHasKey( 'classes/k1eshopextk1eproduct.php', $files );
        $this->assertArrayHasKey( 'classes/k1eshopextk1etag.php', $files );
        $this->assertArrayHasKey( 'classes/k1eshopextconnection.php', $files );
        $this->assertArrayHasKey( 'modules/k1e_shop_ext/module.php', $files );
        expRadWizardTestHelper::assertFilesAreSafe( $this, $files );
        $product = $files['classes/k1eshopextk1eproduct.php'];
        foreach ( array( 'contentobject_id', 'created_at', 'price', 'k1e_product' ) as $word )
            $this->assertStringContainsString( $word, $product );
        $this->assertStringContainsString( "ActiveExtensions[]=k1e_shop_ext", expModuleExtensionWizard::activation( $settings ) );
    }

    public function testSqliteCandidatesAreFilesInTheUsualPlaces()
    {
        foreach ( expModuleExtensionWizard::sqliteCandidates() as $relative => $label )
        {
            $this->assertMatchesRegularExpression( '/\.(db|db3|sqlite|sqlite3)$/i', $relative );
            $this->assertFileExists( expModuleExtensionWizard::installationRoot() . '/' . $relative );
            $this->assertStringStartsWith( $relative . ' (', $label );
        }
        $this->assertSame( 'mysqli', in_array( expModuleExtensionWizard::currentType(), array_keys( expModuleExtensionWizard::databaseTypes() ), true ) ? 'mysqli' : 'x' );
    }
}
