<?php
/**
 * Discovery and ordering of migrations without a database, on the fixture trees in fixtures/.
 *
 *  ML-01 discovery: the site's, an extension's and a package's directory; engine files are one migration; other
 *        files and subdirectories are skipped; a missing directory is no error
 *  ML-02 problems of a directory: invalid names, unknown types, engine names outside SQL, two types for one id,
 *        header mistakes
 *  ML-03 the settings: migration.ini names the site directories, the extension directory and Enabled
 *  ML-04 order: by timestamp, then after what a migration requires; closure()
 *  ML-05 errors of the graph: an id defined twice, a requirement that does not exist, an inactive extension, cycles
 *  ML-06 the environment filter, and a requirement that does not run in the environment
 *  ML-07 performance: discovery, ordering and checksums of 1,000 migration files in under 1 s (3 s in CI)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expMigrationLocatorTest extends PHPUnit\Framework\TestCase
{
    private static $root;
    private static $fixtures;

    /** @var string|null */
    private $dir;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
        self::$fixtures = __DIR__ . '/fixtures';
        require_once self::$root . '/kernel/classes/expdependencyorder.php';
        foreach ( array( 'expmigration', 'expmigrationdefinition', 'expmigrationlocator', 'expmigrationgraph' ) as $f )
            require_once self::$root . "/kernel/classes/migration/$f.php";
    }

    protected function tearDown(): void
    {
        if ( $this->dir !== null && is_dir( $this->dir ) )
        {
            foreach ( glob( $this->dir . '/*/*' ) as $file )
                unlink( $file );
            foreach ( glob( $this->dir . '/*' ) as $sub )
                rmdir( $sub );
            rmdir( $this->dir );
        }
        $this->dir = null;
    }

    /** The fixture tree's directories, as the settings would give them. */
    private function tree()
    {
        return array(
            array( 'source' => 'site', 'path' => self::$fixtures . '/site' ),
            array( 'source' => 'site', 'path' => self::$fixtures . '/no-such-directory' ),
            array( 'source' => 'extension:expnews', 'path' => self::$fixtures . '/extension/expnews/migrations' ),
            array( 'source' => 'package:newsdemo', 'path' => self::$fixtures . '/packages/local/newsdemo/migrations/' ),
        );
    }

    private static function ids( array $definitions )
    {
        return array_map( function ( $d ) { return $d->id(); }, $definitions );
    }

    /** ML-01 */
    public function testDiscoveryOnTheFixtureTree()
    {
        $locator = new expMigrationLocator( $this->tree() );
        $found = $locator->find();
        $this->assertSame( array(), $locator->errors() );
        $this->assertSame( array( '20261001100000_news_class', '20261002100000_news_index', '20261003100000_news_settings', '20261004100000_news_content',
                                  '20261005100000_news_folder', '20261006100000_demo_news' ), self::ids( $found ) );
        $this->assertSame( array( 'site', 'site', 'site', 'site', 'extension:expnews', 'package:newsdemo' ),
                           array_map( function ( $d ) { return $d->source(); }, $found ) );
        $this->assertSame( array( 'php', 'sql', 'ini', 'yml', 'yml', 'yml' ), array_map( function ( $d ) { return $d->type(); }, $found ) );

        $index = $found[1];
        $this->assertSame( array( '*', 'mysql' ), array_keys( $index->files() ), 'the engine file belongs to the same migration' );
        $this->assertSame( 'An index for the news list', $index->description() );
        $this->assertSame( array( '20261001100000_news_class' ), $index->requires() );
        $this->assertSame( hash_file( 'sha256', self::$fixtures . '/site/20261002100000_news_index.mysql.sql' ), $index->checksumFor( 'mysql' ) );
        $this->assertSame( self::$fixtures . '/packages/local/newsdemo/migrations/20261006100000_demo_news.yml', $found[5]->file() );

        $this->assertTrue( $found[0]->maintenance() );
        $this->assertSame( 'Fixture Author', $found[0]->author() );
        $this->assertSame( array( 'staging', 'production' ), $found[2]->environments() );
        $this->assertSame( array( '!production' ), $found[4]->environments() );
        $this->assertSame( array( 'expnews' ), $found[3]->requiredExtensions() );
        $this->assertCount( 4, $locator->searchedDirectories() );

        $this->assertSame( array(), ( new expMigrationLocator( array() ) )->find() );
    }

    /** ML-02 */
    public function testProblemsOfADirectory()
    {
        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => self::$fixtures . '/broken' ) ) );
        $found = $locator->find();
        $this->assertSame( array( '20261002100000_two_types', '20261003100000_bad_header' ), self::ids( $found ) );
        $errors = implode( "\n", $locator->errors() );
        foreach ( array( '20261001100000_engine_php.mysql.php: only SQL files name an engine', '20261001100000_unknown_type.yaml has the unknown type .yaml',
                         '20261002100000_two_types.yml: the migration 20261002100000_two_types already has a .sql file',
                         '20261004100000_.sql is not a migration file name', '20261099100000_bad_date.sql does not start with a valid date',
                         'the header names the id 20261003100000_another_id', 'unknown header key enviroments', 'unknown engine db2',
                         'perhaps is not yes or no', 'description is given twice' ) as $message )
            $this->assertStringContainsString( $message, $errors );
        $this->assertCount( 10, $locator->errors() );
        $this->assertSame( 'a header with mistakes', $found[1]->description(), 'the first value counts' );
    }

    /** ML-03 */
    public function testDirectoriesOfTheSettings()
    {
        $ini = $this->createStub( eZINI::class );
        $values = array( 'Enabled' => 'enabled', 'DirectoryList' => array( 'migrations', '', '/srv/shared/migrations' ), 'ExtensionDirectory' => 'db/migrations/' );
        $ini->method( 'hasVariable' )->willReturnCallback( function ( $group, $name ) use ( &$values ) {
            return $group === 'MigrationSettings' && isset( $values[$name] );
        } );
        $ini->method( 'variable' )->willReturnCallback( function ( $group, $name ) use ( &$values ) {
            return $values[$name];
        } );
        $settings = expMigrationLocator::settings( $ini );
        $this->assertSame( array( 'enabled' => true, 'directories' => array( 'migrations', '/srv/shared/migrations' ), 'extension_directory' => 'db/migrations' ), $settings );

        $values['Enabled'] = 'disabled';
        $this->assertFalse( expMigrationLocator::settings( $ini )['enabled'] );
        $this->assertSame( array(), expMigrationLocator::directories( $ini ), 'disabled: nothing is searched' );
        $values = array();
        $this->assertSame( array( 'enabled' => true, 'directories' => array( 'migrations' ), 'extension_directory' => 'migrations' ),
                           expMigrationLocator::settings( $ini ), 'the defaults' );

        $layout = expMigrationLocator::layout( array( 'enabled' => true, 'directories' => array( 'site', '/srv/shared/migrations' ), 'extension_directory' => 'migrations' ),
                                               self::$fixtures . '/', 'extension', array( 'expnews', 'expother' ), array( 'packages/local', 'packages/missing' ) );
        $this->assertSame( array(
            array( 'source' => 'site', 'path' => self::$fixtures . '/site' ),
            array( 'source' => 'site', 'path' => '/srv/shared/migrations' ),
            array( 'source' => 'extension:expnews', 'path' => self::$fixtures . '/extension/expnews/migrations' ),
            array( 'source' => 'extension:expother', 'path' => self::$fixtures . '/extension/expother/migrations' ),
            array( 'source' => 'package:newsdemo', 'path' => self::$fixtures . '/packages/local/newsdemo/migrations' ),
        ), $layout, 'only packages that have a migration directory' );
        $this->assertCount( 6, ( new expMigrationLocator( $layout ) )->find() );
    }

    /** ML-03 */
    public function testShippedSettings()
    {
        $ini = file_get_contents( self::$root . '/settings/migration.ini' );
        $this->assertStringContainsString( "[MigrationSettings]\n", $ini );
        $this->assertSame( 1, preg_match( '/^Enabled=enabled$/m', $ini ) );
        $this->assertSame( 1, preg_match( '/^DirectoryList\[\]\nDirectoryList\[\]=migrations$/m', $ini ) );
        $this->assertSame( 1, preg_match( '/^ExtensionDirectory=migrations$/m', $ini ) );
    }

    /** ML-04 */
    public function testOrderByTimestampAndRequires()
    {
        $locator = new expMigrationLocator( $this->tree() );
        $graph = new expMigrationGraph( $locator->find(), array( 'extensions' => array( 'expnews' ) ) );
        $this->assertSame( array(), $graph->errors() );
        $this->assertSame( array( '20261001100000_news_class', '20261002100000_news_index', '20261003100000_news_settings',
                                  '20261005100000_news_folder', '20261004100000_news_content', '20261006100000_demo_news' ), $graph->ids(),
                           'news_content requires the later news_folder of the extension' );
        $this->assertSame( $graph->ids(), self::ids( $graph->migrations() ) );
        $this->assertSame( array( '20261005100000_news_folder', '20261004100000_news_content', '20261006100000_demo_news' ),
                           self::ids( $graph->closure( '20261006100000_demo_news' ) ) );
        $this->assertSame( array(), $graph->closure( 'nothing' ) );
        $this->assertSame( 'site', $graph->get( '20261001100000_news_class' )->source() );
        $this->assertNull( $graph->get( 'nothing' ) );
        $this->assertSame( array(), $graph->blocked() );
    }

    private function definition( $id, $header = '', $source = 'site' )
    {
        if ( $this->dir === null )
        {
            $this->dir = sys_get_temp_dir() . '/expmigrationlocator-' . getmypid() . '-' . mt_rand();
            mkdir( $this->dir );
        }
        $sub = $this->dir . '/' . preg_replace( '/\W/', '_', $source );
        if ( !is_dir( $sub ) )
            mkdir( $sub );
        file_put_contents( "$sub/$id.sql", $header );
        $definition = new expMigrationDefinition( $id, $source, 'sql' );
        $definition->addFile( "$sub/$id.sql" );
        return $definition;
    }

    /** ML-05 */
    public function testErrorsOfTheGraph()
    {
        $graph = new expMigrationGraph( array(
            $this->definition( '20261001000000_a' ),
            $this->definition( '20261001000000_a', '', 'extension:other' ),
            $this->definition( '20261002000000_b', "-- requires: 20261009000000_missing, extension:inactive, extension:expnews\n" ),
            $this->definition( '20261003000000_self', "-- requires: 20261003000000_self\n" ),
        ), array( 'extensions' => array( 'expnews' ) ) );
        $errors = implode( "\n", $graph->errors() );
        $this->assertStringContainsString( 'the migration 20261001000000_a is defined twice (site: ', $errors );
        $this->assertStringContainsString( 'extension:other: ', $errors );
        $this->assertStringContainsString( 'the migration 20261002000000_b requires 20261009000000_missing, which does not exist', $errors );
        $this->assertStringContainsString( 'requires the extension inactive, which is not active', $errors );
        $this->assertStringNotContainsString( 'extension expnews', $errors );
        $this->assertStringContainsString( 'the migration 20261003000000_self requires itself', $errors );
        $this->assertCount( 4, $graph->errors() );
        $this->assertSame( array( '20261001000000_a', '20261002000000_b', '20261003000000_self' ), $graph->ids() );
        $this->assertSame( 'site', $graph->get( '20261001000000_a' )->source(), 'the first definition counts' );

        $unchecked = new expMigrationGraph( array( $this->definition( '20261004000000_c', "-- requires: extension:inactive\n" ) ) );
        $this->assertSame( array(), $unchecked->errors(), 'extensions not given: not checked' );
    }

    /** ML-05 */
    public function testCycles()
    {
        $graph = new expMigrationGraph( array(
            $this->definition( '20261001000000_a', "-- requires: 20261003000000_c\n" ),
            $this->definition( '20261002000000_b', "-- requires: 20261001000000_a\n" ),
            $this->definition( '20261003000000_c', "-- requires: 20261002000000_b\n" ),
            $this->definition( '20261004000000_d', "-- requires: 20261002000000_b\n" ),
            $this->definition( '20261005000000_e' ),
        ) );
        $this->assertSame( array( '20261005000000_e', '20261001000000_a', '20261002000000_b', '20261003000000_c', '20261004000000_d' ), $graph->ids(),
                           'the blocked migrations stay listed at the end' );
        $this->assertSame( array( '20261001000000_a', '20261002000000_b', '20261003000000_c', '20261004000000_d' ), $graph->blocked() );
        $this->assertCount( 1, $graph->errors() );
        $this->assertStringContainsString( 'wait for each other (a cycle in requires: 20261001000000_a -> 20261003000000_c -> 20261002000000_b -> 20261001000000_a)',
                                           $graph->errors()[0] );
    }

    /** ML-06 */
    public function testEnvironmentFilter()
    {
        $definitions = ( new expMigrationLocator( $this->tree() ) )->find();

        $production = new expMigrationGraph( $definitions, array( 'environment' => 'production' ) );
        $this->assertSame( array( '20261001100000_news_class', '20261002100000_news_index', '20261003100000_news_settings',
                                  '20261004100000_news_content', '20261006100000_demo_news' ), $production->ids() );
        $this->assertSame( array( 'the migration 20261004100000_news_content requires 20261005100000_news_folder, which does not run in the environment production' ),
                           $production->errors() );

        $development = new expMigrationGraph( $definitions, array( 'environment' => 'development' ) );
        $this->assertSame( array( '20261001100000_news_class', '20261002100000_news_index', '20261005100000_news_folder',
                                  '20261004100000_news_content', '20261006100000_demo_news' ), $development->ids() );
        $this->assertSame( array(), $development->errors() );

        $every = new expMigrationGraph( $definitions );
        $this->assertCount( 6, $every->ids(), 'no environment: every migration' );
    }

    /** Writes $count migration files into four directories; each requires an earlier one now and then. */
    private function generate( $count )
    {
        $this->dir = sys_get_temp_dir() . '/expmigrationlocator-perf-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir );
        $directories = array();
        foreach ( array( 'site', 'extension:a', 'extension:b', 'package:c' ) as $source )
        {
            $path = $this->dir . '/' . preg_replace( '/\W/', '_', $source );
            mkdir( $path );
            $directories[] = array( 'source' => $source, 'path' => $path );
        }
        $types = array( 'php', 'yml', 'sql', 'ini', 'mysql.sql' );
        $body = str_repeat( "-- a line of the migration's body, to give the file a realistic size\n", 30 );
        $ids = array();
        mt_srand( 1000 );
        for ( $i = 0; $i < $count; $i++ )
        {
            $timestamp = date( 'YmdHis', 1790000000 + $i * 3600 );
            $id = $timestamp . "_migration_$i";
            $type = $types[$i % 5];
            $header = "description: migration $i\n";
            if ( $ids && $i % 3 === 0 )
                $header .= 'requires: ' . $ids[mt_rand( 0, count( $ids ) - 1 )] . "\n";
            if ( $i % 7 === 0 )
                $header .= "environments: staging, production\n";
            $marker = $type === 'php' ? '// ' : ( $type === 'yml' || $type === 'ini' ? '# ' : '-- ' );
            $content = ( $type === 'php' ? "<?php\n" : '' ) . $marker . str_replace( "\n", "\n$marker", rtrim( $header ) ) . "\n" . $body;
            file_put_contents( $directories[$i % 4]['path'] . "/$id.$type", $content );
            $ids[] = $id;
        }
        return $directories;
    }

    /** ML-07 */
    public function testPerformanceOfAThousandMigrations()
    {
        $directories = $this->generate( 1000 );
        $limit = getenv( 'CI' ) ? 3.0 : 1.0;
        $best = INF;
        for ( $run = 0; $run < 3; $run++ )
        {
            $start = microtime( true );
            $locator = new expMigrationLocator( $directories );
            $graph = new expMigrationGraph( $locator->find(), array( 'environment' => 'production', 'extensions' => array( 'a', 'b' ) ) );
            $sums = array();
            foreach ( $graph->migrations() as $definition )
                $sums[] = $definition->checksumFor( 'mysql' );
            $best = min( $best, microtime( true ) - $start );
            $this->assertSame( array(), $locator->errors() );
            $this->assertSame( array(), $graph->errors() );
            $this->assertCount( 1000, $graph->ids() );
            $this->assertCount( 1000, array_filter( $sums ) );
        }
        $this->assertLessThan( $limit, $best, sprintf( 'discovery, ordering and checksums of 1,000 migrations took %.3f s', $best ) );

        // every migration comes after what it requires
        $position = array_flip( $graph->ids() );
        foreach ( $graph->migrations() as $definition )
            foreach ( $definition->requiredMigrations() as $dep )
                $this->assertLessThan( $position[$definition->id()], $position[$dep] );
    }
}
