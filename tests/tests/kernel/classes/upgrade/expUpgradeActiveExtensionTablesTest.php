<?php
/**
 * The upgrade step 6.0.15-active-extension-tables: an active extension's share/db_schema.dba tables that the
 * database lacks are found, planned and created on SQLite with the columns and keys of the .dba, a half-applied state
 * is completed, a second run does nothing, an extension without a .dba is not looked at. The installer's
 * expInstallDefaultExtensions::ensureExtensionTables() creates the same tables. Fixture extensions and SQLite files
 * under var/tmp, removed afterwards; no installation. With the Composer copies of ezstarrating and ezgmaplocation in
 * extension/ (an installation, not the kernel repository), their own files are checked too.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUpgradeActiveExtensionTablesTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private static $root;

    /** @var eZSQLite3DB|null */
    private $db;

    /** @var string */
    private $file;

    /** @var string fixture extension directory */
    private $extensions;

    /** The two fixture extensions: one table with a two-column key and an index, two tables */
    private static function fixtures()
    {
        $int = array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 );
        return array(
            'fixture_map' => array(
                'fixture_map' => array(
                    'name' => 'fixture_map',
                    'fields' => array(
                        'address' => array( 'length' => 150, 'type' => 'varchar', 'default' => null ),
                        'attribute_id' => $int,
                        'version' => $int,
                        'latitude' => array( 'type' => 'double', 'not_null' => '1', 'default' => '0' ),
                    ),
                    'indexes' => array(
                        'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'attribute_id', 'version' ) ),
                        'fixture_map_latitude' => array( 'type' => 'non-unique', 'fields' => array( 'latitude' ) ),
                    ),
                ),
            ),
            'fixture_rating' => array(
                'fixture_rating' => array(
                    'name' => 'fixture_rating',
                    'fields' => array( 'object_id' => $int, 'attribute_id' => $int,
                                       'average' => array( 'type' => 'float', 'not_null' => '1', 'default' => 0 ) ),
                    'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'object_id', 'attribute_id' ) ) ),
                ),
                'fixture_rating_data' => array(
                    'name' => 'fixture_rating_data',
                    'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ), 'object_id' => $int,
                                       'session_key' => array( 'length' => 32, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ) ),
                    'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ),
                ),
            ),
        );
    }

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
        if ( !class_exists( 'expUpgradeStepActiveExtensionTables' ) )
            require_once self::$root . '/kernel/classes/upgrade/steps/expupgradestepactiveextensiontables.php';
    }

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $dir = self::$root . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $tag = getmypid() . '-' . mt_rand();
        $this->file = "$dir/upgrade-extension-tables-$tag.sqlite";
        $this->extensions = "$dir/upgrade-extension-tables-$tag";
        foreach ( self::fixtures() as $extension => $schema )
        {
            mkdir( "$this->extensions/$extension/share", 0777, true );
            $schema['_info'] = array( 'format' => 'generic' );
            file_put_contents( "$this->extensions/$extension/share/db_schema.dba",
                               "<?php\n// This array contains the database schema\n\$schema = " . var_export( $schema, true ) . ";\n\n?>\n" );
        }
        mkdir( "$this->extensions/fixture_no_tables", 0777, true );
        $this->db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->file,
                                            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                            'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                            'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                            'use_persistent_connection' => false, 'show_errors' => false ) );
        expKernelTableSchema::reset();
    }

    protected function tearDown(): void
    {
        if ( $this->db )
            $this->db->close();
        if ( $this->file && file_exists( $this->file ) )
            unlink( $this->file );
        if ( $this->extensions && is_dir( $this->extensions ) )
        {
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->extensions, FilesystemIterator::SKIP_DOTS ),
                                                 RecursiveIteratorIterator::CHILD_FIRST );
            foreach ( $it as $item )
                $item->isDir() ? rmdir( $item->getPathname() ) : unlink( $item->getPathname() );
            rmdir( $this->extensions );
        }
        expKernelTableSchema::reset();
    }

    private function context( $apply, array $extensions = array( 'fixture_map', 'fixture_rating', 'fixture_no_tables' ), $root = null )
    {
        return new expUpgradeContext( $this->db, $apply, array( 'extensions' => $extensions, 'extension_root' => $root ?: $this->extensions ) );
    }

    /** @return string[] the column names of a SQLite table */
    private function columns( $table )
    {
        return array_column( (array)$this->db->arrayQuery( "PRAGMA table_info($table)" ), 'name' );
    }

    public function testIsRegisteredInTheDatabaseStage()
    {
        $step = new expUpgradeStepActiveExtensionTables();
        $this->assertSame( '6.0.15-active-extension-tables', $step->id() );
        $this->assertSame( 'database', $step->stage() );
        $this->assertSame( array( '*' ), $step->engines() );
        $this->assertStringContainsString( "StepList[]=expUpgradeStepActiveExtensionTables\n", file_get_contents( self::$root . '/settings/upgrade.ini' ) );
    }

    public function testMissingTablesAreFoundPlannedCreatedAndASecondRunDoesNothing()
    {
        $step = new expUpgradeStepActiveExtensionTables();
        $detect = $step->detect( $this->context( false ) );
        $this->assertSame( expUpgradeStep::MISSING, $detect['effect'] );
        $this->assertSame( 'missing: fixture_map (fixture_map), fixture_rating (fixture_rating, fixture_rating_data)', $detect['detail'] );

        $plan = $step->plan( $this->context( false ) );
        $this->assertCount( 2, $plan );
        $this->assertStringStartsWith( 'create table fixture_map (fixture_map): ', $plan[0] );
        $this->assertStringContainsString( 'CREATE TABLE fixture_map', $plan[0] );
        $this->assertStringContainsString( 'CREATE TABLE fixture_rating_data', $plan[1] );
        $this->assertFalse( expKernelTableSchema::exists( 'fixture_map', $this->db, true ), 'the dry run changes nothing' );

        $done = $step->apply( $this->context( true ) );
        $this->assertSame( array( 'created table fixture_map (fixture_map)', 'created table fixture_rating (fixture_rating)',
                                  'created table fixture_rating_data (fixture_rating)' ), $done );
        $fixtures = self::fixtures();
        foreach ( array( 'fixture_map' => 'fixture_map', 'fixture_rating' => 'fixture_rating', 'fixture_rating_data' => 'fixture_rating' ) as $table => $extension )
        {
            $expected = array_keys( $fixtures[$extension][$table]['fields'] );
            $got = $this->columns( $table );
            sort( $expected );
            sort( $got );
            $this->assertSame( $expected, $got, "$table has the columns of its .dba" );
        }
        $this->assertTrue( $this->db->query( "INSERT INTO fixture_map ( attribute_id, version, latitude ) VALUES ( 7, 1, 1.5 )" ) );
        $this->assertTrue( $this->db->query( "INSERT INTO fixture_map ( attribute_id, version, latitude ) VALUES ( 7, 2, 1.5 )" ) );
        $this->assertFalse( @$this->db->query( "INSERT INTO fixture_map ( attribute_id, version, latitude ) VALUES ( 7, 2, 2.5 )" ),
                            'the primary key of the .dba spans attribute_id and version' );

        $this->assertSame( expUpgradeStep::PRESENT, $step->detect( $this->context( false ) )['effect'] );
        $this->assertSame( array(), $step->plan( $this->context( false ) ) );
        $this->assertSame( array(), $step->apply( $this->context( true ) ), 'a second run does nothing' );
        $rows = $this->db->arrayQuery( 'SELECT COUNT(*) AS n FROM fixture_map' );
        $this->assertSame( '2', (string)$rows[0]['n'], 'nothing dropped' );
    }

    public function testAHalfAppliedStateIsCompleted()
    {
        $messages = array();
        $schema = self::fixtures()['fixture_rating'];
        $this->assertTrue( expKernelTableSchema::install( array( 'fixture_rating' ), $this->db, $schema, $messages ) );
        $step = new expUpgradeStepActiveExtensionTables();
        $context = $this->context( true, array( 'fixture_rating' ) );
        $this->assertSame( 'missing: fixture_rating (fixture_rating_data)', $step->detect( $context )['detail'] );
        $this->assertSame( array( 'created table fixture_rating_data (fixture_rating)' ), $step->apply( $context ) );
        $this->assertSame( expUpgradeStep::PRESENT, $step->detect( $context )['effect'] );
    }

    public function testNotApplicableWithoutAnActiveExtensionThatShipsTables()
    {
        $step = new expUpgradeStepActiveExtensionTables();
        $this->assertSame( expUpgradeStep::NOT_APPLICABLE, $step->detect( $this->context( false, array( 'fixture_no_tables', 'not_there' ) ) )['effect'] );
        $this->assertSame( expUpgradeStep::NOT_APPLICABLE, $step->detect( $this->context( false, array() ) )['effect'] );
    }

    public function testTheInstallerCreatesTheSameTables()
    {
        // the instance there is, without connecting one (a DB-less run has none)
        $before = isset( $GLOBALS['eZDBGlobalInstance'] ) ? $GLOBALS['eZDBGlobalInstance'] : null;
        eZDB::setInstance( $this->db );
        try
        {
            $this->assertSame( 'fixture_rating: created 2 tables',
                               expInstallDefaultExtensions::ensureExtensionTables( 'fixture_rating', $this->extensions ) );
            $this->assertSame( 'fixture_rating: tables present',
                               expInstallDefaultExtensions::ensureExtensionTables( 'fixture_rating', $this->extensions ) );
        }
        finally
        {
            eZDB::setInstance( $before );
        }
        $step = new expUpgradeStepActiveExtensionTables();
        $this->assertSame( 'missing: fixture_map (fixture_map)', $step->detect( $this->context( false ) )['detail'],
                           'the step sees what the installer created' );
    }

    /** The Composer copies of an installation: ezstarrating (two tables) and ezgmaplocation (one) */
    public function testTheComposerCopiesOfEzstarratingAndEzgmaplocation()
    {
        // EXP_TEST_EXTENSION_ROOT: an installation's extension directory, for a run from the kernel repository
        $root = getenv( 'EXP_TEST_EXTENSION_ROOT' ) ?: self::$root . '/extension';
        foreach ( array( 'ezstarrating', 'ezgmaplocation' ) as $extension )
        {
            if ( !is_file( "$root/$extension/share/db_schema.dba" ) )
                $this->markTestSkipped( "extension/$extension is not installed (the kernel repository alone)" );
        }
        $step = new expUpgradeStepActiveExtensionTables();
        $context = $this->context( true, array( 'ezstarrating', 'ezgmaplocation' ), $root );
        $this->assertSame( 'missing: ezstarrating (ezstarrating, ezstarrating_data), ezgmaplocation (ezgmaplocation)',
                           $step->detect( $context )['detail'] );
        $this->assertCount( 3, $step->apply( $context ) );
        foreach ( array( 'ezstarrating' => array( 'ezstarrating', 'ezstarrating_data' ), 'ezgmaplocation' => array( 'ezgmaplocation' ) ) as $extension => $tables )
        {
            $schema = expKernelTableSchema::read( "$root/$extension/share/db_schema.dba" );
            foreach ( $tables as $table )
            {
                $expected = array_keys( $schema[$table]['fields'] );
                $got = $this->columns( $table );
                sort( $expected );
                sort( $got );
                $this->assertSame( $expected, $got, "$table has the columns of $extension's .dba" );
            }
        }
        $this->assertSame( expUpgradeStep::PRESENT, $step->detect( $context )['effect'] );
    }
}
