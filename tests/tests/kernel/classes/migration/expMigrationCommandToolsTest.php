<?php
/**
 * The kernel classes behind the exp:migration commands, without a database: the selection of migrate --only and
 * --path, the simulation of dry-run --simulate (with the runner's in-memory context), its diff, the lint of
 * exp:migration:check, the skeleton of exp:migration:generate and the run that exp:migration:resume picks.
 *
 *  MT-01 select(): by id and by file or folder, in the order given; an id or a path that selects nothing is an
 *        error; unselectedRequirements() names the requirements left out
 *  MT-02 expMigrationSimulation::diff(): tables added and dropped, columns, row counts, changed rows
 *  MT-03 the simulation: each pending migration runs in one transaction that is rolled back, nothing recorded, the
 *        lock held and freed; a failure stops the rest; engines without full transactions list the plan instead
 *  MT-04 expMigrationCheck: the runner fixtures pass, broken names, unknown types, a file without a class, a syntax
 *        error and a class declared twice fail
 *  MT-05 expMigrationGenerator: names, the skeleton's path, class and namespace; written once, never replaced; the
 *        file passes the check and loads as a reversible PHP migration with its description
 *  MT-07 expMigrationCheck reports a method PHP would refuse (a helper named definition() or file(), a hook with
 *        another signature, no up()) without loading the file; expMigrationPhp's own methods are final
 *  MT-06 expMigrationResume::pick(): the newest failed or interrupted run, a run still running, a finished run
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerFakes.php';

/** A fake migration with a file, for the selection by path. */
class expMigrationCommandToolsFake extends expMigrationRunnerFake
{
    public $file;

    public function __construct( $id, $file, array $options = array() )
    {
        parent::__construct( $id, $options );
        $this->file = $file;
    }

    public function file()
    {
        return $this->file;
    }
}

class expMigrationCommandToolsTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private static $root;

    /** @var string|null a folder of this test under var/tmp */
    private $dir;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    protected function tearDown(): void
    {
        if ( $this->dir !== null && is_dir( $this->dir ) )
        {
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->dir, FilesystemIterator::SKIP_DOTS ),
                                                       RecursiveIteratorIterator::CHILD_FIRST );
            foreach ( $iterator as $item )
                $item->isDir() ? rmdir( $item->getPathname() ) : unlink( $item->getPathname() );
            rmdir( $this->dir );
        }
        $this->dir = null;
    }

    /** @return string a fresh folder under var/tmp */
    private function dir()
    {
        if ( $this->dir === null )
        {
            $this->dir = self::$root . '/var/tmp/migration-command-tools-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
            mkdir( $this->dir, 0775, true );
        }
        return $this->dir;
    }

    /** MT-01 */
    public function testSelect()
    {
        $root = self::$root;
        $a = new expMigrationCommandToolsFake( '20261001100000_a', "$root/migrations/20261001100000_a.php" );
        $b = new expMigrationCommandToolsFake( '20261002100000_b', "$root/extension/expnews/migrations/20261002100000_b.php", array( 'requires' => array( '20261001100000_a' ) ) );
        $c = new expMigrationCommandToolsFake( '20261003100000_c', "$root/extension/expnews/migrations/20261003100000_c.php" );
        $all = array( $a, $b, $c );

        $errors = array();
        $ids = function ( array $list ) {
            return array_map( function ( $m ) { return $m->id(); }, $list );
        };
        $this->assertSame( array( '20261001100000_a', '20261003100000_c' ), $ids( expMigrationRunner::select( $all, array( '20261003100000_c', '20261001100000_a' ), array(), $root, $errors ) ) );
        $this->assertSame( array(), $errors );
        $this->assertSame( array( '20261002100000_b', '20261003100000_c' ), $ids( expMigrationRunner::select( $all, array(), array( 'extension/expnews/migrations/' ), $root, $errors ) ) );
        $this->assertSame( array( '20261002100000_b' ), $ids( expMigrationRunner::select( $all, array(), array( "./extension/expnews/migrations/20261002100000_b.php" ), $root, $errors ) ) );
        $this->assertSame( array( '20261002100000_b' ), $ids( expMigrationRunner::select( $all, array(), array( "$root/extension/expnews/migrations/20261002100000_b.php" ), $root, $errors ) ) );
        $this->assertSame( array(), $errors );
        // a folder whose name only starts like the one given selects nothing
        $this->assertSame( array(), expMigrationRunner::select( $all, array( 'nope' ), array( 'extension/expnew' ), $root, $errors ) );
        $this->assertSame( array( 'no migration nope (--only)', 'no migration in extension/expnew (--path): a path must be a migration file or a folder in a migrations directory' ), $errors );

        $left = expMigrationRunner::unselectedRequirements( array( $b, $c ), $all );
        $this->assertSame( array( '20261002100000_b' ), array_keys( $left ) );
        $this->assertSame( array( '20261001100000_a' ), $ids( $left['20261002100000_b'] ) );
        $this->assertSame( array(), expMigrationRunner::unselectedRequirements( $all, $all ) );
    }

    /** MT-02 */
    public function testDiff()
    {
        $before = array(
            'a' => array( 'columns' => array( 'id', 'name' ), 'rows' => 2, 'hash' => 'h1' ),
            'b' => array( 'columns' => array( 'id' ), 'rows' => 5, 'hash' => 'h2' ),
            'c' => array( 'columns' => array( 'id', 'old' ), 'rows' => 1, 'hash' => 'h3' ),
            'gone' => array( 'columns' => array( 'id' ), 'rows' => 12, 'hash' => 'h4' ),
            'same' => array( 'columns' => array( 'id' ), 'rows' => 3, 'hash' => 'h5' ),
        );
        $after = array(
            'a' => array( 'columns' => array( 'id', 'name' ), 'rows' => 3, 'hash' => 'x1' ),
            'b' => array( 'columns' => array( 'id' ), 'rows' => 5, 'hash' => 'x2' ),
            'c' => array( 'columns' => array( 'id', 'new' ), 'rows' => 1, 'hash' => 'h3' ),
            'new' => array( 'columns' => array( 'id', 'title' ), 'rows' => 1, 'hash' => 'x5' ),
            'same' => array( 'columns' => array( 'id' ), 'rows' => 3, 'hash' => 'h5' ),
        );
        $this->assertSame( array(
            '~ table a: rows 2 -> 3',
            '~ table b: rows changed (5)',
            '~ table c: columns +new, -old',
            '- table gone (12 rows)',
            '+ table new (2 columns, 1 row)',
        ), expMigrationSimulation::diff( $before, $after ) );
        $this->assertSame( array(), expMigrationSimulation::diff( $before, $before ) );
        $this->assertTrue( expMigrationSimulation::supports( 'sqlite' ) );
        $this->assertTrue( expMigrationSimulation::supports( 'postgresql' ) );
        $this->assertFalse( expMigrationSimulation::supports( 'mysql' ) );
        $this->assertFalse( expMigrationSimulation::supports( 'mongodb' ) );
        $this->assertFalse( expMigrationSimulation::supports( 'oracle' ) );
    }

    /** MT-03 */
    public function testSimulation()
    {
        $lockFile = $this->dir() . '/upgrade.lock';
        $ran = array();
        $make = function ( $failSecond ) use ( &$ran ) {
            return array(
                new expMigrationRunnerFake( '20261001100000_a', array( 'up' => function () use ( &$ran ) { $ran[] = 'a'; return array( 'did a' ); } ) ),
                new expMigrationRunnerFake( '20261002100000_b', array( 'up' => function () use ( &$ran, $failSecond ) {
                    $ran[] = 'b';
                    if ( $failSecond )
                        throw new RuntimeException( 'b broke' );
                    return array( 'did b' );
                } ) ),
                new expMigrationRunnerFake( '20261003100000_c', array( 'up' => function () use ( &$ran ) { $ran[] = 'c'; return array(); } ) ),
            );
        };

        $context = new expMigrationRunnerTestContext( true, array( 'lock-file' => $lockFile ) );
        $dry = new expMigrationRunnerTestContext( false, array( 'lock-file' => $lockFile ) );
        $report = ( new expMigrationSimulation( $context, array( 'dry-context' => $dry ) ) )->run( $make( false ) );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( 'simulate', $report['mode'] );
        $this->assertTrue( $report['simulated'] );
        $this->assertSame( array( 'a', 'b', 'c' ), $ran );
        $this->assertSame( array( 'simulated', 'simulated', 'simulated' ), array_column( $report['migrations'], 'action' ) );
        $this->assertSame( array( 'pending', 'pending', 'pending' ), array_column( $report['migrations'], 'state' ), 'the installation is unchanged' );
        $this->assertSame( array( 'did a' ), $report['migrations'][0]['simulation']['messages'] );
        $this->assertSame( 3, $report['summary']['simulated'] );
        $this->assertSame( 0, $report['summary']['would-apply'] );
        $this->assertSame( array( 'become-user', 'begin', 'rollback', 'restore-user' ), $context->calls, 'one transaction, rolled back, nothing recorded' );
        $this->assertFalse( ( new expMigrationLock( 'upgrade', $lockFile ) )->isLocked(), 'the lock is free again' );
        $this->assertSame( 'system.migration.run', $context->audits[0][0] );
        $this->assertSame( 'simulate', $context->audits[0][1]['verb'] );

        $ran = array();
        $context = new expMigrationRunnerTestContext( true, array( 'lock-file' => $lockFile ) );
        $report = ( new expMigrationSimulation( $context, array( 'dry-context' => $dry ) ) )->run( $make( true ) );
        $this->assertSame( 1, $report['exit'] );
        $this->assertSame( array( 'a', 'b' ), $ran, 'a failure stops the simulation' );
        $this->assertSame( array( 'passed', 'failed', 'not-run' ), array_map( function ( $m ) { return $m['simulation']['result']; }, $report['migrations'] ) );
        $this->assertSame( array( '20261002100000_b failed in the simulation: b broke' ), $report['errors'] );
        $this->assertContains( 'rollback', $context->calls );

        // MySQL commits schema changes at once: no simulation, the plan as a dry run lists it
        $ran = array();
        $context = new expMigrationRunnerTestContext( true, array( 'lock-file' => $lockFile, 'engine' => 'mysql' ) );
        $dryMysql = new expMigrationRunnerTestContext( false, array( 'lock-file' => $lockFile, 'engine' => 'mysql' ) );
        $report = ( new expMigrationSimulation( $context, array( 'dry-context' => $dryMysql ) ) )->run( $make( false ) );
        $this->assertSame( 0, $report['exit'] );
        $this->assertFalse( $report['simulated'] );
        $this->assertSame( array(), $ran );
        $this->assertSame( array( 'would-apply', 'would-apply', 'would-apply' ), array_column( $report['migrations'], 'action' ) );
        $this->assertSame( array( 'plan of 20261001100000_a' ), $report['migrations'][0]['plan'] );
        $this->assertStringContainsString( 'no simulation on mysql', $report['warnings'][0] );
        $this->assertSame( array(), $context->calls );
    }

    /** MT-04 */
    public function testCheck()
    {
        $fixtures = __DIR__ . '/fixtures';
        $ok = expMigrationCheck::run( array( 'paths' => array( "$fixtures/runner" ), 'root' => self::$root ) );
        $this->assertSame( 0, $ok['exit'], implode( '; ', $ok['errors'] ) );
        $this->assertSame( 'check', $ok['mode'] );
        $this->assertSame( 2, $ok['checked'] );
        $this->assertSame( array( true, true ), array_column( $ok['migrations'], 'ok' ) );
        $this->assertSame( 'tests/tests/kernel/classes/migration/fixtures/runner', $ok['directories'][0]['path'] );

        $one = expMigrationCheck::run( array( 'paths' => array( 'tests/tests/kernel/classes/migration/fixtures/runner/20261031130000_wp3_note_table.php' ), 'root' => self::$root ) );
        $this->assertSame( 1, $one['checked'] );
        $this->assertSame( 0, $one['exit'] );

        $broken = expMigrationCheck::run( array( 'paths' => array( "$fixtures/broken" ), 'root' => self::$root ) );
        $this->assertSame( 1, $broken['exit'] );
        $text = implode( "\n", $broken['errors'] );
        $this->assertStringContainsString( 'unknown type', $text );
        $this->assertStringContainsString( 'does not start with a valid date', $text );

        $site = expMigrationCheck::run( array( 'paths' => array( "$fixtures/site" ), 'root' => self::$root ) );
        $this->assertSame( 1, $site['exit'] );
        $byId = array_column( $site['migrations'], null, 'id' );
        // a YAML migration whose second content step names neither class nor parent: the step type's own check
        $this->assertFalse( $byId['20261004100000_news_content']['ok'] );
        $contentErrors = implode( "\n", $byId['20261004100000_news_content']['errors'] );
        if ( expMigrationYamlParser::available() )
        {
            $this->assertStringContainsString( 'steps[1] (content): class is missing (mode create)', $contentErrors );
            $this->assertStringContainsString( 'steps[1] (content): parent is missing (mode create)', $contentErrors );
            $this->assertStringNotContainsString( 'steps[0]', $contentErrors );
        }
        else
        {
            $this->assertStringContainsString( 'symfony/yaml is not installed', $contentErrors );
        }
        $this->assertSame( array(), $byId['20261003100000_news_settings']['errors'], 'an INI migration is checked like any other' );
        $this->assertSame( array(), $byId['20261002100000_news_index']['errors'], 'an SQL migration is checked like any other' );

        $runnerBroken = expMigrationCheck::run( array( 'paths' => array( "$fixtures/runner-broken" ), 'root' => self::$root ) );
        $this->assertSame( 1, $runnerBroken['exit'] );
        $this->assertFalse( $runnerBroken['migrations'][0]['ok'] );

        $missing = expMigrationCheck::run( array( 'paths' => array( 'no/such/folder' ), 'root' => self::$root ) );
        $this->assertSame( array( 'no/such/folder: no such file or folder' ), $missing['errors'] );

        $this->assertNull( expMigrationCheck::syntaxError( "<?php\nclass A {}\n" ) );
        $this->assertStringContainsString( 'on line',(string)expMigrationCheck::syntaxError( "<?php\nclass A {\n" ) );
        $this->assertNotNull( expMigrationCheck::syntaxError( "<?php\nfunction ( {\n" ) );

        // a class declared by two files, and a syntax error, in a folder of the test
        $dir = $this->dir();
        $class = "<?php\n// description: twice\nnamespace ExpTests\\MigrationCheck;\nfinal class Twice extends \\expMigrationPhp\n{\n    public function up( \$context ) { return array(); }\n}\n";
        file_put_contents( "$dir/20261001100000_first.php", $class );
        file_put_contents( "$dir/20261002100000_second.php", $class );
        file_put_contents( "$dir/20261003100000_broken.php", "<?php\n// description: broken\nnamespace ExpTests\\MigrationCheck;\nfinal class Broken extends \\expMigrationPhp {\n" );
        $twice = expMigrationCheck::run( array( 'paths' => array( $dir ), 'root' => self::$root ) );
        $byId = array_column( $twice['migrations'], null, 'id' );
        $this->assertTrue( $byId['20261001100000_first']['ok'], implode( '; ', $byId['20261001100000_first']['errors'] ) );
        $this->assertStringContainsString( 'is declared by 20261001100000_first.php too', implode( "\n", $byId['20261002100000_second']['errors'] ) );
        $this->assertStringContainsString( 'PHP syntax', implode( "\n", $byId['20261003100000_broken']['errors'] ) );
    }

    /** MT-07 */
    public function testCheckReportsMethodsPhpWouldRefuse()
    {
        $head = "<?php\n// description: %s\nnamespace ExpTests\\MigrationClash;\n";
        $files = array(
            '20261005100000_helper_definition' => "final class HelperDefinition extends \\expMigrationPhp\n{\n    private function definition( \$table ) { return array(); }\n    public function up( \$context ) { return array(); }\n}\n",
            '20261005100100_helper_file' => "final class HelperFile extends \\expMigrationPhp\n{\n    public function up( \$context ) { return array( \$this->file( 'a' ) ); }\n    protected static function file( \$name ) { return \$name; }\n}\n",
            '20261005100200_typed_plan' => "final class TypedPlan extends \\expMigrationPhp\n{\n    public function plan( \\expMigrationContext \$context ) { return array(); }\n    public function up( \$context ) { return array(); }\n}\n",
            '20261005100300_private_down' => "final class PrivateDown extends \\expMigrationPhp\n{\n    public function up( \$context ) { return array(); }\n    private function down( \$context, \$more ) { return array(); }\n}\n",
            '20261005100400_no_up' => "final class NoUp extends \\expMigrationPhp\n{\n    public function plan( \$context ) { return array(); }\n}\n",
            '20261005100500_fine' => "final class Fine extends \\expMigrationPhp\n{\n    #[\\ReturnTypeWillChange]\n    public function plan( \$context, array \$extra = array() ): array { return \$this->tableDefinition(); }\n"
                                   . "    public function up( \$context ) { \$f = function () { return 1; }; return array( \"{\$f()}\" ); }\n"
                                   . "    private function tableDefinition() { return array( 'x' ); }\n    public static function definitionOf( \$x ) { return \$x; }\n}\n",
        );
        $dir = $this->dir();
        foreach ( $files as $id => $body )
            file_put_contents( "$dir/$id.php", sprintf( $head, $id ) . $body );

        $check = expMigrationCheck::run( array( 'paths' => array( $dir ), 'root' => self::$root ) );
        $byId = array_column( $check['migrations'], null, 'id' );
        $errors = function ( $id ) use ( $byId ) { return implode( "\n", $byId[$id]['errors'] ); };
        $this->assertSame( 1, $check['exit'] );
        $this->assertStringContainsString( 'HelperDefinition::definition() has the name of a method of expMigrationPhp that a migration cannot declare', $errors( '20261005100000_helper_definition' ) );
        $this->assertStringContainsString( 'HelperFile::file() has the name of a method of expMigrationPhp', $errors( '20261005100100_helper_file' ) );
        $this->assertStringContainsString( 'TypedPlan::plan(): the parameter $context has the type \\expMigrationContext', $errors( '20261005100200_typed_plan' ) );
        $this->assertStringContainsString( 'PrivateDown::down() must be public', $errors( '20261005100300_private_down' ) );
        $this->assertStringContainsString( 'PrivateDown::down() requires 2 parameter(s), expMigrationPhp passes 1', $errors( '20261005100300_private_down' ) );
        $this->assertStringContainsString( 'the class NoUp has no method up()', $errors( '20261005100400_no_up' ) );
        $this->assertTrue( $byId['20261005100500_fine']['ok'], $errors( '20261005100500_fine' ) );
        $this->assertFalse( class_exists( 'ExpTests\\MigrationClash\\HelperDefinition', false ), 'a refused file is never loaded' );

        $this->assertSame( array(), expMigrationPhp::methodClashes( (string)file_get_contents( __DIR__ . '/fixtures/runner/20261031130000_wp3_note_table.php' ) ) );
        $this->assertSame( array(), expMigrationPhp::methodClashes( "<?php\nclass NotAMigration { public function definition() {} }\n" ), 'only classes that extend expMigrationPhp' );
        $this->assertTrue( ( new ReflectionMethod( 'expMigrationPhp', 'definition' ) )->isFinal() );
        $this->assertFalse( ( new ReflectionMethod( 'expMigrationPhp', 'plan' ) )->isFinal() );
    }

    /** MT-05 */
    public function testGenerator()
    {
        $this->assertSame( 'add_news_class', expMigrationGenerator::slug( ' Add news-class! ' ) );
        $this->assertSame( '', expMigrationGenerator::slug( '---' ) );
        $this->assertSame( 'AddNewsClass20261031120000', expMigrationGenerator::className( 'add_news_class', '20261031120000' ) );
        $this->assertSame( 'Migration2fa20261031120000', expMigrationGenerator::className( '2fa', '20261031120000' ) );
        $this->assertSame( 'Site\\Migrations', expMigrationGenerator::namespaceOf( 'site' ) );
        $this->assertSame( 'Extension\\ExpNews\\Migrations', expMigrationGenerator::namespaceOf( 'extension:exp_news' ) );

        $dir = $this->dir() . '/migrations';
        $time = mktime( 12, 0, 0, 10, 31, 2026 );
        $skeleton = expMigrationGenerator::skeleton( 'Tools test skeleton', array( 'directory' => $dir, 'time' => $time, 'root' => self::$root,
                                                                                  'description' => 'A skeleton of the tools test', 'author' => 'Exponential tests' ) );
        $this->assertTrue( $skeleton['ok'], implode( '; ', $skeleton['errors'] ) );
        $this->assertSame( '20261031120000_tools_test_skeleton', $skeleton['id'] );
        $this->assertSame( "$dir/20261031120000_tools_test_skeleton.php", $skeleton['file'] );
        $this->assertSame( 'ToolsTestSkeleton20261031120000', $skeleton['class'] );
        $this->assertStringStartsWith( "<?php\n// description: A skeleton of the tools test\n// author: Exponential tests\nnamespace Site\\Migrations;", $skeleton['content'] );
        $this->assertFileDoesNotExist( $skeleton['file'], 'skeleton() writes nothing' );
        $this->assertSame( array(), expMigrationGenerator::write( $skeleton ) );
        $this->assertSame( $skeleton['content'], file_get_contents( $skeleton['file'] ) );
        $this->assertNotSame( array(), expMigrationGenerator::write( $skeleton ), 'an existing file is never replaced' );
        $again = expMigrationGenerator::skeleton( 'Tools test skeleton', array( 'directory' => $dir, 'time' => $time, 'root' => self::$root ) );
        $this->assertFalse( $again['ok'] );
        $this->assertStringContainsString( 'exists already', $again['errors'][0] );

        $check = expMigrationCheck::run( array( 'paths' => array( $dir ), 'root' => self::$root ) );
        $this->assertSame( 0, $check['exit'], implode( '; ', $check['errors'] ) );
        $this->assertSame( array(), $check['warnings'] );
        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => $dir ) ) );
        $definitions = $locator->find();
        $this->assertSame( array(), $definitions[0]->errors() );
        $this->assertSame( 'A skeleton of the tools test', $definitions[0]->description() );
        $migration = expMigrationPhp::load( $definitions[0] );
        $this->assertInstanceOf( 'Site\\Migrations\\ToolsTestSkeleton20261031120000', $migration );
        $this->assertTrue( $migration->reversible() );

        foreach ( array( array( '', array() ), array( 'x', array( 'type' => 'xml' ) ), array( 'x', array( 'source' => 'package:demo' ) ) ) as $bad )
        {
            $result = expMigrationGenerator::skeleton( $bad[0], $bad[1] + array( 'directory' => $dir, 'root' => self::$root ) );
            $this->assertFalse( $result['ok'] );
            $this->assertNotSame( array(), $result['errors'] );
        }
    }

    /** MT-06 */
    public function testResumePick()
    {
        $run = function ( $id, $state ) {
            return array( 'run_id' => $id, 'state' => $state );
        };
        $error = '';
        $this->assertSame( 'r2', expMigrationResume::pick( array( $run( 'r2', 'failed' ), $run( 'r1', 'done' ) ), null, false, $error )['run_id'] );
        $this->assertSame( 'r3', expMigrationResume::pick( array( $run( 'r3', 'running' ), $run( 'r2', 'failed' ) ), null, false, $error )['run_id'], 'interrupted' );
        $this->assertNull( expMigrationResume::pick( array( $run( 'r3', 'running' ), $run( 'r2', 'failed' ) ), null, true, $error ) );
        $this->assertSame( 'the run r3 is still running (the installation lock is held)', $error );
        $this->assertNull( expMigrationResume::pick( array( $run( 'r4', 'done' ), $run( 'r2', 'failed' ) ), null, false, $error ) );
        $this->assertSame( 'the newest run r4 is done, there is nothing to resume', $error );
        $this->assertSame( 'r2', expMigrationResume::pick( array( $run( 'r4', 'done' ), $run( 'r2', 'failed' ) ), 'r2', false, $error )['run_id'] );
        $this->assertNull( expMigrationResume::pick( array( $run( 'r4', 'rolled-back' ) ), 'r4', false, $error ) );
        $this->assertSame( 'the run r4 is rolled-back, there is nothing to resume', $error );
        $this->assertNull( expMigrationResume::pick( array(), 'r9', false, $error ) );
        $this->assertSame( 'no run r9 (exp_migration_run)', $error );
        $this->assertNull( expMigrationResume::pick( array(), null, false, $error ) );
        $this->assertSame( 'no run that failed or was interrupted', $error );
    }
}
