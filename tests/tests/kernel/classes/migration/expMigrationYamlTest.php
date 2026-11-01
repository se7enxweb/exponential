<?php
/**
 * The YAML engine of migrations without a database: the parser, the document, the checks of steps against their
 * types' schemas, placeholders, conditions, loops, references, the id map, identifiers, the step type registry, and
 * YAML migrations found next to PHP migrations and run by the runner with a context that keeps everything in memory
 * (expMigrationRunnerTestContext).
 *
 *  MY-01 the parser: symfony/yaml found through Composer, [YamlSettings] LibraryPath or MIGRATION_YAML_LIBRARY_PATH;
 *        a parse error names its line; duplicate keys and PHP tags are refused; without the library every YAML
 *        migration is refused with the install hint
 *  MY-02 the document: a list of steps or a map of steps, down and transactional; header keys as YAML keys and
 *        unknown keys are errors; an empty file has no steps
 *  MY-03 the checks before a run: unknown and missing types, modes, unknown and missing keys, key types and allowed
 *        values, keys of another mode, the type's own validate(), references, placeholders, loops and conditions,
 *        each with where it is (steps[2] (wp9_record).count)
 *  MY-04 placeholders: ${ref.*}, ${loop.*}, ${idmap.*.*}, ${run.*}, a whole value keeps its type, $${ escapes,
 *        unknown names throw (or stay in the dry run), lists cannot go into a text
 *  MY-05 conditions: all, any, not, ref/set, environment, engine, value with every operator, truthiness, the dry
 *        run's "cannot be told yet"
 *  MY-06 the id map: migrations/idmap/<environment>.json, schema exp-migration-idmap/1, errors, encode
 *  MY-07 identifiers: numbers, remote:, object-remote:, path:, problems per kind
 *  MY-08 the registry: migration.ini [StepSettings] StepTypeList, broken registrations listed, the kernel's step
 *        types reference and assert
 *  MY-09 running: steps in order, references to later steps and migrations, if/environments/engines skip, foreach
 *        over lists, maps and references, while with its maximum, failures name the step; the plan of the dry run
 *  MY-10 rollback: down: steps, revert() of revertible step types the last first, reversible(); caches()
 *  MY-11 the runner and the check: .yml found next to .php, ordered by requires, loaded and run by
 *        expMigrationRunner (a reference set in YAML read by PHP and back), exp:migration:check passes the valid ones
 *        and lists the problems of the broken ones; the checksum of a .yml is the file's, line endings normalized
 *  MY-12 the YAML skeleton of exp:migration:generate --type=yml passes the check
 *
 * Needs symfony/yaml (composer.json requires it; without a Composer install, MIGRATION_YAML_LIBRARY_PATH names a
 * copy): the tests that parse YAML are skipped without it.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerFakes.php';
require_once __DIR__ . '/expMigrationYamlFakes.php';

class expMigrationYamlTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private static $root;

    /** @var string|null a folder of the test under var/tmp */
    private $dir;

    /** @var array|null the [StepSettings] StepTypeList before the test */
    private $savedTypes;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    protected function setUp(): void
    {
        expMigrationYamlRecordStep::$log = array();
        expMigrationIdMap::forget();
        $ini = eZINI::instance( 'migration.ini' );
        $this->savedTypes = $ini->hasVariable( 'StepSettings', 'StepTypeList' ) ? $ini->variable( 'StepSettings', 'StepTypeList' ) : null;
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'migration.ini' );
        if ( $this->savedTypes !== null )
            $ini->setVariable( 'StepSettings', 'StepTypeList', $this->savedTypes );
        expMigrationStepRegistry::forget();
        expMigrationYamlParser::useLibraryClass();
        expMigrationIdMap::forget();
        if ( $this->dir !== null && is_dir( $this->dir ) )
        {
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->dir, FilesystemIterator::SKIP_DOTS ),
                                                       RecursiveIteratorIterator::CHILD_FIRST );
            foreach ( $iterator as $item )
                $item->isDir() ? rmdir( $item->getPathname() ) : unlink( $item->getPathname() );
            rmdir( $this->dir );
        }
    }

    private function needsParser()
    {
        if ( !expMigrationYamlParser::available() )
            $this->markTestSkipped( (string)expMigrationYamlParser::problem() );
    }

    /** @return string a folder of the test, made once */
    private function dir()
    {
        if ( $this->dir === null )
        {
            $this->dir = self::$root . '/var/tmp/migration-yaml-test-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
            mkdir( $this->dir . '/migrations', 0775, true );
        }
        return $this->dir;
    }

    /** @return expMigrationStepRegistry the kernel's step types and the test's */
    private static function registry()
    {
        return new expMigrationStepRegistry( array( 'reference' => 'expMigrationStepReference', 'assert' => 'expMigrationStepAssert',
                                                    'wp9_record' => 'expMigrationYamlRecordStep', 'wp9_oneway' => 'expMigrationYamlOneWayStep' ) );
    }

    /** Registers the test's step types in migration.ini, as an extension would. */
    private function registerInSettings()
    {
        $types = is_array( $this->savedTypes ) ? $this->savedTypes : array();
        $types['wp9_record'] = 'expMigrationYamlRecordStep';
        $types['wp9_oneway'] = 'expMigrationYamlOneWayStep';
        eZINI::instance( 'migration.ini' )->setVariable( 'StepSettings', 'StepTypeList', $types );
        expMigrationStepRegistry::forget();
    }

    /**
     * A YAML migration of a text.
     *
     * @param string $yaml
     * @param string $id
     * @return expMigrationYaml
     */
    private function migration( $yaml, $id = '20261101120000_wp9_test' )
    {
        $file = $this->dir() . "/$id.yml";
        file_put_contents( $file, $yaml );
        $definition = new expMigrationDefinition( $id, 'site', 'yml' );
        $this->assertTrue( $definition->addFile( $file ) );
        return expMigrationYaml::load( $definition, self::registry() );
    }

    /** @return string[] the errors of a document text */
    private static function errorsOf( $yaml, array &$warnings = array() )
    {
        $errors = array();
        expMigrationYaml::fromText( $yaml, self::registry(), $errors, $warnings );
        return $errors;
    }

    /** MY-01 */
    public function testParser()
    {
        $this->needsParser();
        $errors = array();
        $this->assertSame( array( 'a' => array( 1, 'two', true, null ), 'b' => 'yes', 'c' => '2026-11-01' ),
                           expMigrationYamlParser::parse( "a: [ 1, two, true, ~ ]\nb: yes\nc: '2026-11-01'\n", $errors ) );
        $this->assertSame( array(), $errors );
        $this->assertNull( expMigrationYamlParser::parse( "# only a comment\n", $errors ) );

        $this->assertNull( expMigrationYamlParser::parse( "a: 1\nb:\n  - x\n - y\n", $errors ) );
        $this->assertStringStartsWith( 'YAML, line ', $errors[0] );

        $errors = array();
        expMigrationYamlParser::parse( "a: 1\na: 2\n", $errors );
        $this->assertStringContainsString( 'Duplicate key', implode( ' ', $errors ) );

        $errors = array();
        expMigrationYamlParser::parse( "a: !php/object 'O:8:\"stdClass\":0:{}'\n", $errors );
        $this->assertNotSame( array(), $errors, 'PHP objects are refused' );
        $errors = array();
        expMigrationYamlParser::parse( "a: !php/const PHP_VERSION\n", $errors );
        $this->assertNotSame( array(), $errors, 'PHP constants are refused' );

        // without the library: every YAML migration is refused with the hint, nothing else breaks
        expMigrationYamlParser::useLibraryClass( 'NoSuchYamlLibrary\\Yaml' );
        $this->assertFalse( expMigrationYamlParser::available() );
        $this->assertStringContainsString( 'composer require "symfony/yaml:^5.4 || ^6.4 || ^7.0"', (string)expMigrationYamlParser::problem() );
        $errors = array();
        $this->assertNull( expMigrationYamlParser::parse( "a: 1\n", $errors ) );
        $this->assertStringContainsString( '[YamlSettings] LibraryPath', $errors[0] );
        expMigrationYamlParser::useLibraryClass();
        $this->assertTrue( expMigrationYamlParser::available() );
    }

    /** MY-02 */
    public function testDocument()
    {
        $errors = array();
        $this->assertSame( array( 'steps' => array(), 'down' => null, 'transactional' => true ), expMigrationYaml::document( null, $errors ) );
        $list = expMigrationYaml::document( array( array( 'type' => 'reference' ) ), $errors );
        $this->assertSame( array( array( 'type' => 'reference' ) ), $list['steps'] );
        $map = expMigrationYaml::document( array( 'steps' => array(), 'down' => array( array( 'type' => 'x' ) ), 'transactional' => false ), $errors );
        $this->assertSame( array( 'steps' => array(), 'down' => array( array( 'type' => 'x' ) ), 'transactional' => false ), $map );
        $this->assertSame( array(), $errors );

        $this->assertNull( expMigrationYaml::document( array( 'description' => 'x', 'steps' => array(), 'colour' => 1 ), $errors ) );
        $this->assertStringContainsString( 'description: the header is written in comment lines at the top of the file (# description: ...)', $errors[0] );
        $this->assertStringContainsString( 'the unknown key colour (steps, down, transactional)', $errors[1] );
        $errors = array();
        $this->assertNull( expMigrationYaml::document( array( 'down' => array() ), $errors ) );
        $this->assertSame( array( 'steps is missing' ), $errors );
        $errors = array();
        $this->assertNull( expMigrationYaml::document( array( 'steps' => array( 'a' => 1 ), 'transactional' => 'no' ), $errors ) );
        $this->assertSame( array( 'steps: a list of steps', 'transactional: true or false' ), $errors );
        $errors = array();
        $this->assertNull( expMigrationYaml::document( 'text', $errors ) );

        $this->needsParser();
        $warnings = array();
        $this->assertSame( array(), self::errorsOf( "# description: nothing\n", $warnings ) );
        $this->assertSame( array( 'no steps: the migration does nothing' ), $warnings );
        $warnings = array();
        $this->assertSame( array(), self::errorsOf( "steps: []\ndown: []\n", $warnings ) );
        $this->assertContains( 'down is empty: rolling the migration back does nothing', $warnings );
    }

    /** MY-03 */
    public function testChecksBeforeARun()
    {
        $registry = self::registry();
        $check = function ( array $steps ) use ( $registry )
        {
            return expMigrationYaml::validate( array( 'steps' => $steps, 'down' => null, 'transactional' => true ), $registry );
        };
        $this->assertSame( array(), $check( array(
            array( 'type' => 'wp9_record', 'name' => 'a', 'count' => '3', 'color' => 'red', 'parent' => 'remote:abc', 'references' => array( 'x' => 'name' ) ),
            array( 'type' => 'wp9_record', 'mode' => 'delete', 'name' => '${ref.x}', 'keep' => true, 'count' => '${ref.n}', 'parent' => '${idmap.node.news}' ),
            array( 'type' => 'reference', 'set' => array( 'a' => 1, 'b_2' => array( 1, 2 ) ) ),
            array( 'type' => 'assert', 'that' => array( 'all' => array( array( 'ref' => 'x', 'set' => true ), array( 'value' => '${ref.x}', 'in' => array( 'a', 'b' ) ) ) ) ),
            array( 'foreach' => '${ref.list}', 'as' => 'one', 'key' => 'k', 'steps' => array( array( 'type' => 'wp9_record', 'name' => '${loop.one}' ) ) ),
            array( 'while' => array( 'not' => array( 'ref' => 'done', 'set' => true ) ), 'max' => 3, 'steps' => array( array( 'type' => 'reference', 'set' => array( 'done' => true ) ) ) ),
            array( 'type' => 'wp9_record', 'name' => 'b', 'environments' => array( 'staging', '!production' ), 'engines' => 'mysql, postgresql',
                   'if' => '${ref.flag}', 'comment' => 'a note' ),
        ) ) );

        $errors = $check( array(
            array( 'type' => 'no_such_type' ),
            array( 'name' => 'no type' ),
            array( 'type' => 'wp9_record', 'mode' => 'replace', 'name' => 'x' ),
            array( 'type' => 'wp9_record', 'colour' => 'red' ),
            array( 'type' => 'wp9_record', 'name' => 'invalid', 'color' => 'blue', 'count' => 'many', 'keep' => true,
                   'references' => array( '1st' => 'name', 'other' => 'size' ) ),
            array( 'type' => 'wp9_record', 'name' => '${ref.unclosed', 'value' => '${nope.x}', 'parent' => 'node 5' ),
            array( 'foreach' => array( 'a' ), 'type' => 'wp9_record', 'steps' => array() ),
            array( 'while' => array( 'value' => 1, 'bigger' => 0 ), 'max' => 0, 'as' => 'x',
                   'steps' => array( array( 'type' => 'wp9_record', 'name' => 'n', 'if' => 'maybe' ) ) ),
            array( 'type' => 'wp9_record', 'name' => 'e', 'engines' => array( 'db2' ), 'environments' => 5 ),
            'not a map',
            array( 'type' => 'reference', 'set' => array( '9lives' => 1 ) ),
            array( 'foreach' => 'not a placeholder', 'as' => 'index', 'max' => 2, 'steps' => array( array( 'type' => 'reference', 'set' => array( 'a' => 1 ) ) ) ),
            array( 'type' => 'wp9_record', 'name' => '${loop.item}' ),
            array( 'foreach' => array( 1 ), 'as' => 'lang', 'steps' => array(
                array( 'while' => array( 'value' => '${loop.index}', 'lt' => 2 ), 'steps' => array(
                    array( 'type' => 'wp9_record', 'name' => '${loop.lang}-${loop.index}-${loop.key}-${loop.language}' ) ) ) ) ),
        ) );
        $text = implode( "\n", $errors );
        foreach ( array(
            'steps[0]: the unknown step type no_such_type (registered: assert, reference, wp9_oneway, wp9_record; an extension adds one in migration.ini [StepSettings] StepTypeList)',
            'steps[1]: type is missing (assert, reference, wp9_oneway, wp9_record)',
            'steps[2] (wp9_record).mode: "replace" is not one of create, ensure, delete',
            'steps[3] (wp9_record): the unknown key colour (name, value, count, parent, color, keep, fail)',
            'steps[3] (wp9_record): name is missing',
            'steps[4] (wp9_record).color: "blue" is not one of "red", "green"',
            'steps[4] (wp9_record).count: a whole number is expected, not "many"',
            'steps[4] (wp9_record).keep: only in the mode delete',
            'steps[4] (wp9_record): the name invalid is refused by the type',
            'steps[4] (wp9_record).references.1st: a reference name is letters, digits and _, not starting with a digit',
            'steps[4] (wp9_record).references.other: the step gives no value size (name, value, count)',
            'steps[5] (wp9_record).name: a ${ without its closing }',
            'steps[5] (wp9_record).value: ${nope.x}: the unknown scope nope (ref, loop, idmap, run)',
            'steps[5] (wp9_record).parent: "node 5" is no identifier',
            'steps[6]: the unknown key type of a loop',
            'steps[6].steps: a loop has a list of steps',
            'steps[7].while: value takes exactly one operator',
            'steps[7].max: a whole number above 0',
            'steps[7].as: only a foreach loop names its item and key',
            'steps[7].steps[0].if: a condition text must be a placeholder',
            'steps[8] (wp9_record).engines: the unknown engine db2',
            'steps[8] (wp9_record).environments: a list of names',
            'steps[9]: a step is a map',
            'steps[10] (reference): set.9lives: a reference name is letters, digits and _',
            'steps[11].foreach: a list, a map or one placeholder (${ref.list})',
            'steps[11].as: a name of letters, digits and _ (not index',
            'steps[11].max: only a while loop has a maximum',
            'steps[12]: ${loop.item} names no variable of a loop around it (it is in no loop)',
            'steps[13].steps[0].steps[0]: ${loop.language} names no variable of a loop around it (lang, key, index)',
        ) as $expected )
            $this->assertStringContainsString( $expected, $text );
        foreach ( array( '${loop.lang}', '${loop.key}', '${loop.index}' ) as $fine )
            $this->assertStringNotContainsString( $fine, $text );

        // nesting is limited
        $steps = array( array( 'type' => 'reference', 'set' => array( 'a' => 1 ) ) );
        for ( $i = 0; $i < expMigrationYaml::MAX_DEPTH + 1; $i++ )
            $steps = array( array( 'foreach' => array( 1 ), 'steps' => $steps ) );
        $this->assertStringContainsString( 'loops nest 8 deep at most', implode( "\n", $check( $steps ) ) );

        // the down steps are checked too
        $errors = expMigrationYaml::validate( array( 'steps' => array(), 'down' => array( array( 'type' => 'nope' ) ), 'transactional' => true ), $registry );
        $this->assertStringContainsString( 'down[0]: the unknown step type nope', implode( "\n", $errors ) );
    }

    /** MY-04 */
    public function testPlaceholders()
    {
        $scope = array( 'ref' => array( 'id' => 42, 'name' => 'News', 'list' => array( 'a', 'b' ), 'flag' => true, 'none' => null,
                                        'node' => array( 'id' => 5, 'path' => 'users' ) ),
                        'loop' => array( 'item' => 'x', 'index' => 0 ),
                        'idmap' => array( 'node' => array( 'news' => 1234 ) ),
                        'run' => array( 'id' => '20261101-120000-abcdef', 'environment' => 'staging', 'engine' => 'sqlite' ) );
        $this->assertSame( 42, expMigrationReferences::interpolate( '${ref.id}', $scope ) );
        $this->assertSame( array( 'a', 'b' ), expMigrationReferences::interpolate( '${ref.list}', $scope ) );
        $this->assertSame( 'News 42 on sqlite', expMigrationReferences::interpolate( '${ref.name} ${ref.id} on ${run.engine}', $scope ) );
        $this->assertSame( 'true/', expMigrationReferences::interpolate( '${ref.flag}/${ref.none}', $scope ) );
        $this->assertSame( 5, expMigrationReferences::interpolate( '${ref.node.id}', $scope ) );
        $this->assertSame( 'b', expMigrationReferences::interpolate( '${ref.list.1}', $scope ) );
        $this->assertSame( 1234, expMigrationReferences::interpolate( '${idmap.node.news}', $scope ) );
        $this->assertSame( 'x0', expMigrationReferences::interpolate( '${loop.item}${loop.index}', $scope ) );
        $this->assertSame( 'a ${ref.id} b', expMigrationReferences::interpolate( 'a $${ref.id} b', $scope ) );
        $this->assertSame( array( 'k' => array( 42, 'plain', 7 ) ), expMigrationReferences::interpolate( array( 'k' => array( '${ref.id}', 'plain', 7 ) ), $scope ) );
        $this->assertSame( array( '${ref.id}' => 1 ), expMigrationReferences::interpolate( array( '${ref.id}' => 1 ), $scope ), 'keys stay' );

        foreach ( array( '${ref.missing}' => 'no such reference', 'x ${idmap.node.other}' => 'not in the id map', '${loop.color}' => 'no such loop variable',
                         'all: ${ref.list}' => 'cannot be written into a text' ) as $text => $message )
        {
            try
            {
                expMigrationReferences::interpolate( $text, $scope );
                $this->fail( "$text throws" );
            }
            catch ( RuntimeException $e )
            {
                $this->assertStringContainsString( $message, $e->getMessage() );
            }
        }
        $unresolved = array();
        $this->assertSame( 'id ${ref.later} and 42', expMigrationReferences::interpolate( 'id ${ref.later} and ${ref.id}', $scope, false, $unresolved ) );
        $this->assertSame( array( '${ref.later}' ), $unresolved );

        $this->assertTrue( expMigrationReferences::contains( array( 'a' => array( 'b' => 'x ${ref.y}' ) ) ) );
        $this->assertFalse( expMigrationReferences::contains( 'only $${escaped}' ) );
        $errors = array();
        expMigrationReferences::validate( array( 'a' => '${ref}', 'b' => '${run.user}', 'c' => '${idmap.node}', 'd' => '${ref.a b}', 'e' => '${ref.ok} $${x}' ), 'k', $errors );
        $this->assertCount( 4, $errors );
        $this->assertStringContainsString( 'k.b: ${run.user}: the run has id, environment and engine', implode( "\n", $errors ) );
        $this->assertTrue( expMigrationReferences::validName( 'news_parent2' ) );
        $this->assertFalse( expMigrationReferences::validName( '2news' ) );
    }

    /** MY-05 */
    public function testConditions()
    {
        $scope = array( 'ref' => array( 'n' => 3, 'name' => 'news-1', 'empty' => '', 'list' => array( 'a' ), 'off' => 'false' ),
                        'run' => array( 'environment' => 'staging', 'engine' => 'postgresql' ) );
        $true = array(
            true, '${ref.n}', '${ref.list}',
            array( 'all' => array() ),
            array( 'any' => array( false, '${ref.n}' ) ),
            array( 'not' => '${ref.off}' ),
            array( 'ref' => 'n', 'set' => true ),
            array( 'ref' => 'gone', 'set' => false ),
            array( 'environment' => 'staging' ),
            array( 'environment' => array( '!production' ) ),
            array( 'engine' => array( 'mysql', 'postgresql' ) ),
            array( 'value' => '${ref.n}', 'equals' => '3' ),
            array( 'value' => '${ref.n}', 'not_equals' => 4 ),
            array( 'value' => '${ref.name}', 'in' => array( 'x', 'news-1' ) ),
            array( 'value' => '${ref.name}', 'not_in' => array( 'x' ) ),
            array( 'value' => '${ref.n}', 'gt' => 2 ),
            array( 'value' => '${ref.n}', 'gte' => 3 ),
            array( 'value' => '${ref.n}', 'lt' => 3.5 ),
            array( 'value' => '${ref.n}', 'lte' => 3 ),
            array( 'value' => '${ref.name}', 'matches' => '/^news-\d+$/' ),
            array( 'value' => '${ref.empty}', 'empty' => true ),
            array( 'value' => '${ref.list}', 'empty' => false ),
        );
        foreach ( $true as $i => $condition )
        {
            $this->assertSame( array(), expMigrationCondition::validate( $condition, "c$i" ), "c$i is valid" );
            $this->assertTrue( expMigrationCondition::evaluate( $condition, $scope ), "c$i is true: " . json_encode( $condition ) );
        }
        $false = array( false, '${ref.empty}', '${ref.off}', array( 'any' => array() ), array( 'environment' => 'production' ),
                        array( 'value' => 'b', 'in' => array( 'a' ) ), array( 'value' => 2, 'gt' => 2 ) );
        foreach ( $false as $i => $condition )
            $this->assertFalse( expMigrationCondition::evaluate( $condition, $scope ), "f$i is false" );

        // the dry run: a reference set when the migration runs cannot be told yet
        $this->assertNull( expMigrationCondition::evaluate( '${ref.later}', $scope, false ) );
        $this->assertNull( expMigrationCondition::evaluate( array( 'all' => array( true, array( 'value' => '${ref.later}', 'gt' => 1 ) ) ), $scope, false ) );
        $this->assertFalse( expMigrationCondition::evaluate( array( 'all' => array( false, '${ref.later}' ) ), $scope, false ), 'false wins' );
        $this->assertNull( expMigrationCondition::evaluate( array( 'ref' => 'later', 'set' => true ), $scope + array( 'pending' => array( 'later' ) ), false ) );
        try
        {
            expMigrationCondition::evaluate( array( 'value' => 'text', 'gt' => 1 ), $scope );
            $this->fail( 'gt of a text throws' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'gt compares numbers', $e->getMessage() );
        }

        $invalid = array( 'text', array( 1, 2 ), array( 'all' => 'x' ), array( 'ref' => 'x' ), array( 'environment' => array() ),
                          array( 'engine' => 'db2' ), array( 'value' => 1, 'gt' => 'many' ), array( 'value' => 1, 'matches' => 'no delimiters' ),
                          array( 'value' => 1, 'in' => 'x' ), array( 'value' => 1, 'empty' => 'yes' ), array( 'when' => 1 ),
                          array( 'not' => true, 'all' => array() ) );
        foreach ( $invalid as $i => $condition )
            $this->assertNotSame( array(), expMigrationCondition::validate( $condition, "i$i" ), "i$i is invalid: " . json_encode( $condition ) );
        $this->assertTrue( expMigrationCondition::same( '3', 3.0 ) );
        $this->assertFalse( expMigrationCondition::same( true, 'true' ) );
        $this->assertFalse( expMigrationCondition::truthy( 'No' ) );
    }

    /** MY-06 */
    public function testIdMap()
    {
        $repository = $this->dir() . '/migrations';
        $this->assertSame( "$repository/idmap/staging.json", expMigrationIdMap::file( $repository, 'staging' ) );
        $this->assertSame( "$repository/idmap/default.json", expMigrationIdMap::file( $repository, '' ) );
        $errors = array();
        $this->assertSame( array(), expMigrationIdMap::load( $repository, 'staging', $errors ), 'no file: an empty map' );
        $this->assertSame( array(), $errors );

        mkdir( "$repository/idmap" );
        $ids = array( 'node' => array( 'news' => 1234, 'media' => 'remote:media-root' ), 'section' => array( 'intranet' => 6 ) );
        file_put_contents( "$repository/idmap/staging.json", expMigrationIdMap::encode( $ids ) );
        expMigrationIdMap::forget();
        $this->assertSame( array( 'node' => array( 'media' => 'remote:media-root', 'news' => 1234 ), 'section' => array( 'intranet' => 6 ) ),
                           expMigrationIdMap::load( $repository, 'staging', $errors ) );
        $this->assertSame( array(), $errors );
        $this->assertStringContainsString( '"schema": "exp-migration-idmap/1"', file_get_contents( "$repository/idmap/staging.json" ) );

        file_put_contents( "$repository/idmap/production.json", '{"schema":"exp-migration-idmap/1","ids":{"node":{"bad name":1,"x":[]}}}' );
        expMigrationIdMap::load( $repository, 'production', $errors );
        $this->assertSame( array( 'idmap/production.json: ids.node.bad name: a name is letters, digits, _ and -',
                                  'idmap/production.json: ids.node.x: a number or an identifier text' ), $errors );
        $errors = array();
        expMigrationIdMap::parse( '{"ids":{}}', $errors );
        $this->assertSame( array( 'the schema is not exp-migration-idmap/1' ), $errors );
        $errors = array();
        expMigrationIdMap::parse( 'not json', $errors );
        $this->assertStringStartsWith( 'not JSON', $errors[0] );
    }

    /** MY-07 */
    public function testIdentifiers()
    {
        $this->assertSame( array( 'form' => 'id', 'value' => 5 ), expMigrationIdentifier::parse( 5 ) );
        $this->assertSame( array( 'form' => 'id', 'value' => 5 ), expMigrationIdentifier::parse( '5' ) );
        $this->assertSame( array( 'form' => 'remote', 'value' => 'abc' ), expMigrationIdentifier::parse( 'remote:abc' ) );
        $this->assertSame( array( 'form' => 'object-remote', 'value' => 'abc' ), expMigrationIdentifier::parse( 'object-remote: abc' ) );
        $this->assertSame( array( 'form' => 'path', 'value' => 'media/images' ), expMigrationIdentifier::parse( 'path:/media/images/' ) );
        $error = null;
        foreach ( array( 0, -1, '', 'node 5', 'remote:', 'uuid:1', 1.5, array() ) as $bad )
        {
            $this->assertNull( expMigrationIdentifier::parse( $bad, $error ), json_encode( $bad ) );
            $this->assertNotNull( $error );
        }
        $this->assertNull( expMigrationIdentifier::problem( 'node', '${idmap.node.news}' ) );
        $this->assertNull( expMigrationIdentifier::problem( 'node', 'object-remote:abc' ) );
        $this->assertSame( 'object-remote: names a node; an object is named by remote:', expMigrationIdentifier::problem( 'object', 'object-remote:abc' ) );
        try
        {
            expMigrationIdentifier::resolve( 'widget', 5 );
            $this->fail( 'no kind widget' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'no identifiers of the kind widget', $e->getMessage() );
        }
    }

    /** MY-08 */
    public function testRegistry()
    {
        $registry = new expMigrationStepRegistry( array( 'wp9_record' => 'expMigrationYamlRecordStep', 'reference' => new expMigrationStepReference(),
                                                         'wp9_broken' => 'expMigrationYamlBrokenStep', 'other' => 'expMigrationYamlRecordStep',
                                                         'missing' => 'NoSuchStepClass', 'plain' => 'stdClass', 'Bad-Name' => 'expMigrationYamlRecordStep' ) );
        $this->assertSame( array( 'reference', 'wp9_record' ), $registry->names() );
        $this->assertInstanceOf( 'expMigrationYamlRecordStep', $registry->get( 'wp9_record' ) );
        $this->assertNull( $registry->get( 'missing' ) );
        $text = implode( "\n", $registry->errors() );
        foreach ( array( 'the step type wp9_broken (expMigrationYamlBrokenStep): the key if is the engine\'s', 'the key size: the type is one of',
                         'the step type other: expMigrationYamlRecordStep calls itself wp9_record', 'the class NoSuchStepClass does not exist',
                         'stdClass does not implement expMigrationStepType', 'the step type name Bad-Name' ) as $expected )
            $this->assertStringContainsString( $expected, $text );
        // a broken registration fails every YAML migration that is checked with it, so it is not missed
        $this->assertNotSame( array(), expMigrationYaml::validate( array( 'steps' => array(), 'down' => null, 'transactional' => true ), $registry ) );

        $ini = eZINI::instance( 'migration.ini' );
        $settings = expMigrationStepRegistry::fromSettings();
        $this->assertSame( $settings, expMigrationStepRegistry::fromSettings(), 'made once' );
        $this->assertSame( array( 'assert', 'reference' ), array_values( array_intersect( array( 'assert', 'reference' ), $settings->names() ) ) );
        $this->assertSame( array(), $settings->errors() );
        $this->assertSame( 1000, expMigrationYaml::defaultMaxIterations() );
        $this->assertTrue( $ini->hasVariable( 'YamlSettings', 'LibraryPath' ) );

        $this->registerInSettings();
        $this->assertContains( 'wp9_record', expMigrationStepRegistry::fromSettings()->names() );
        $this->assertSame( array(), expMigrationStepSchema::problems( ( new expMigrationStepReference() )->schema() ) );
        $this->assertSame( array(), expMigrationStepSchema::problems( ( new expMigrationStepAssert() )->schema() ) );
    }

    /** MY-09 */
    public function testRunning()
    {
        $this->needsParser();
        $migration = $this->migration( "# description: run\nsteps:\n"
            . "    - type: reference\n      set: { parent: 7, names: [ one, two ] }\n"
            . "    - type: wp9_record\n      name: 'p\${ref.parent}'\n      value: '\${ref.parent}'\n      count: '\${ref.parent}'\n      references: { got: value }\n"
            . "    - foreach: '\${ref.names}'\n      as: n\n      steps:\n          - type: wp9_record\n            name: '\${loop.n}-\${loop.index}-\${loop.key}'\n"
            . "    - type: wp9_record\n      if: { value: '\${ref.got}', equals: 8 }\n      name: never\n"
            . "    - type: assert\n      that: { value: '\${ref.got}', equals: 7 }\n" );
        $this->assertTrue( $migration->transactional() );
        $context = new expMigrationRunnerTestContext( true );
        $lines = $migration->up( $context );
        $this->assertSame( array( 'p7', 'one-0-0', 'two-1-1' ), expMigrationYamlRecordStep::names( 'apply' ) );
        $this->assertSame( 7, expMigrationYamlRecordStep::$log[0][1]['value'], 'a whole placeholder keeps the type' );
        $this->assertSame( 7, expMigrationYamlRecordStep::$log[0][1]['count'] );
        $this->assertSame( 'create', expMigrationYamlRecordStep::$log[0][1]['mode'], 'the default mode' );
        $this->assertFalse( expMigrationYamlRecordStep::$log[1][1]['fail'], 'the default of a key' );
        $this->assertSame( 7, $context->reference( 'got' ) );
        $this->assertSame( array( 'set the reference parent', 'set the reference names', 'created p7', 'created one-0-0', 'created two-1-1',
                                  'skipped steps[3] (wp9_record): the condition is false', 'checked that {"value":7,"equals":7}' ), $lines );

        // a failure names the step
        expMigrationYamlRecordStep::$log = array();
        $failing = $this->migration( "steps:\n  - type: wp9_record\n    name: ok\n  - foreach: [ x ]\n    steps:\n      - type: wp9_record\n        name: bad\n        fail: true\n  - type: wp9_record\n    name: after\n",
                                     '20261101120100_wp9_fail' );
        try
        {
            $failing->up( new expMigrationRunnerTestContext( true ) );
            $this->fail( 'the step fails' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertSame( 'steps[1].steps[0] (wp9_record): failed on purpose: bad', $e->getMessage() );
        }
        $this->assertSame( array( 'ok', 'bad' ), expMigrationYamlRecordStep::names( 'apply' ), 'nothing after the failure' );

        // an unknown reference, a value of the wrong type after the replacement, a while loop that does not end
        foreach ( array(
            "- type: wp9_record\n  name: '\${ref.nothing}'\n" => 'steps[0] (wp9_record): ${ref.nothing}: no such reference',
            "- type: reference\n  set: { n: many }\n- type: wp9_record\n  name: x\n  count: '\${ref.n}'\n" => 'steps[1] (wp9_record).count: a whole number is expected, not "many"',
            "- while: true\n  max: 3\n  steps:\n    - type: wp9_record\n      name: again\n" => 'steps[0]: the loop ran 3 times and its condition is still true (max)',
            "- type: reference\n  set: { one: 1 }\n- foreach: '\${ref.one}'\n  steps:\n    - type: wp9_record\n      name: x\n" => 'steps[1].foreach: "${ref.one}" is not a list or a map but 1',
            "- type: wp9_record\n  name: x\n  references: { r: value }\n- type: assert\n  that: { ref: r, set: false }\n  message: the reference is set\n" => 'steps[1] (assert): the reference is set',
        ) as $yaml => $message )
        {
            try
            {
                $this->migration( $yaml, '20261101120200_wp9_' . substr( md5( $yaml ), 0, 6 ) )->up( new expMigrationRunnerTestContext( true ) );
                $this->fail( "fails: $message" );
            }
            catch ( RuntimeException $e )
            {
                $this->assertStringStartsWith( $message, $e->getMessage() );
            }
        }

        // the plan: what can be told before the run, the rest marked
        expMigrationYamlRecordStep::$log = array();
        $plan = $this->migration( "steps:\n"
            . "  - type: wp9_record\n    name: first\n    references: { later: name }\n"
            . "  - type: wp9_record\n    if: { ref: later, set: true }\n    name: 'after \${ref.later}'\n"
            . "  - foreach: [ a, b ]\n    steps:\n      - type: wp9_record\n        name: '\${loop.item}'\n"
            . "  - foreach: '\${ref.later}'\n    steps:\n      - type: wp9_record\n        name: unknown\n"
            . "  - while: { value: '\${ref.later}', empty: false }\n    steps:\n      - type: wp9_record\n        name: loop\n"
            . "  - type: wp9_record\n    if: { engine: mysql }\n    name: mysql\n"
            . "  - type: wp9_record\n    environments: production\n    name: prod\n", '20261101120300_wp9_plan' )->plan( new expMigrationRunnerTestContext( false ) );
        $this->assertSame( array(
            'create first',
            'run if {"ref":"later","set":true} (decided when the migration runs):',
            '  create after ${ref.later}',
            'run for each of 2 item(s), as item:',
            '  create a',
            '  create b',
            'run for each item of "${ref.later}", as item (known when the migration runs):',
            '  create unknown',
            'run while {"value":"${ref.later}","empty":false}, at most 1000 times:',
            '  create loop',
            'skip steps[5] (wp9_record): the condition is false',
            'skip steps[6] (wp9_record): not for the environment staging',
        ), $plan );
        $this->assertSame( array(), expMigrationYamlRecordStep::names( 'apply' ), 'the plan applies nothing' );

        // the id map of the run's environment, from the run's repository
        $repository = $this->dir() . '/migrations';
        @mkdir( "$repository/idmap" );
        file_put_contents( "$repository/idmap/staging.json", expMigrationIdMap::encode( array( 'node' => array( 'news' => 'remote:news-root' ) ) ) );
        expMigrationYamlRecordStep::$log = array();
        $this->migration( "- type: wp9_record\n  name: '\${idmap.node.news}'\n  parent: '\${idmap.node.news}'\n", '20261101120400_wp9_idmap' )
             ->up( new expMigrationRunnerTestContext( true, array( 'repository' => $repository ) ) );
        $this->assertSame( array( 'remote:news-root' ), expMigrationYamlRecordStep::names( 'apply' ) );
    }

    /** MY-10 */
    public function testRollbackAndCaches()
    {
        $this->needsParser();
        $explicit = $this->migration( "steps:\n  - type: wp9_oneway\ndown:\n  - type: wp9_record\n    mode: delete\n    name: undo\n" );
        $this->assertTrue( $explicit->reversible() );
        $this->assertSame( array( 'deleted undo' ), $explicit->down( new expMigrationRunnerTestContext( true ) ) );

        $auto = $this->migration( "- type: reference\n  set: { a: 1 }\n- type: wp9_record\n  name: one\n- type: wp9_record\n  mode: delete\n  name: two\n"
                                  . "- type: wp9_record\n  name: skipped\n  engines: mysql\n", '20261101120500_wp9_auto' );
        $this->assertTrue( $auto->reversible() );
        $this->assertSame( array( 'reverted two', 'reverted one' ), $auto->down( new expMigrationRunnerTestContext( true ) ), 'the last first' );
        $this->assertSame( array( 'content', 'template-block' ), $auto->caches() );

        $oneWay = $this->migration( "- type: wp9_record\n  name: one\n- type: wp9_oneway\n", '20261101120600_wp9_oneway' );
        $this->assertFalse( $oneWay->reversible() );
        $looped = $this->migration( "- foreach: [ 1 ]\n  steps:\n    - type: wp9_record\n      name: x\n", '20261101120700_wp9_looped' );
        $this->assertFalse( $looped->reversible(), 'a loop needs down:' );
        $this->assertSame( array( 'content' ), $looped->caches() );
        try
        {
            $oneWay->down( new expMigrationRunnerTestContext( true ) );
            $this->fail( 'cannot be undone' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'cannot be undone by itself', $e->getMessage() );
        }
        $this->assertFalse( $this->migration( "steps: []\ntransactional: false\n", '20261101120800_wp9_notx' )->transactional() );
    }

    /** MY-11 */
    public function testRunnerAndCheck()
    {
        $this->needsParser();
        $this->registerInSettings();
        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => __DIR__ . '/fixtures/yaml' ) ) );
        $definitions = $locator->find();
        $this->assertSame( array( 'yml', 'php', 'yml' ), array_map( function ( $d ) { return $d->type(); }, $definitions ) );
        $errors = array();
        $migrations = expMigrationRunner::load( ( new expMigrationGraph( $definitions ) )->migrations(), $errors );
        $this->assertSame( array(), $errors );
        $this->assertSame( array( 'expMigrationYaml', 'ExpTests\\Migrations\\Yaml\\Between', 'expMigrationYaml' ), array_map( 'get_class', $migrations ) );
        $this->assertSame( 'The first YAML migration of the engine test', $migrations[0]->description() );
        $this->assertSame( array( '20261101090100_wp9_between' ), $migrations[2]->requires() );
        $this->assertSame( expMigrationDefinition::checksum( __DIR__ . '/fixtures/yaml/20261101090000_wp9_first.yml' ), $migrations[0]->checksum() );
        $this->assertSame( hash( 'sha256', file_get_contents( __DIR__ . '/fixtures/yaml/20261101090000_wp9_first.yml' ) ), $migrations[0]->checksum() );
        $this->assertSame( expMigrationDefinition::checksumOf( "- type: x\n" ), expMigrationDefinition::checksumOf( "- type: x\r\n" ), 'line endings do not count' );

        $dry = ( new expMigrationRunner( new expMigrationRunnerTestContext( false ) ) )->run( $migrations );
        $this->assertSame( 0, $dry['exit'], implode( '; ', $dry['errors'] ) );
        $this->assertSame( array( 'would-apply', 'would-apply', 'would-apply' ), array_column( $dry['migrations'], 'action' ) );
        $this->assertSame( array( 'set the reference parent to 42', 'set the reference title to "Parent of staging"', 'create first' ), $dry['migrations'][0]['plan'] );
        $this->assertSame( array(), expMigrationYamlRecordStep::names( 'apply' ) );

        $context = new expMigrationRunnerTestContext( true );
        $report = ( new expMigrationRunner( $context ) )->run( $migrations );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) . json_encode( array_column( $report['migrations'], 'messages' ) ) );
        $this->assertSame( array( 'applied', 'applied', 'applied' ), array_column( $report['migrations'], 'action' ) );
        $this->assertSame( array( 'first', 'first-red-0', 'first-green-1', 'pair a=1', 'pair b=2', 'turn 0', 'turn 1', 'php saw first' ),
                           expMigrationYamlRecordStep::names( 'apply' ) );
        $this->assertSame( 'ensure', end( expMigrationYamlRecordStep::$log )[1]['mode'] );
        $this->assertSame( 2, expMigrationYamlRecordStep::$log[4][1]['count'], 'a loop item keeps its type' );
        $this->assertSame( 1, $context->reference( 'turn' ) );
        $this->assertSame( 42, $context->reference( 'first_value' ) );
        $this->assertSame( 'full', $report['migrations'][2]['transaction'] );
        $this->assertContains( 'skipped steps[3] (wp9_record): the condition is false', $report['migrations'][2]['messages'] );
        $this->assertContains( 'skipped steps[4] (wp9_record): not for the environment staging', $report['migrations'][2]['messages'] );
        $this->assertContains( 'skipped steps[5] (wp9_record): not for sqlite', $report['migrations'][2]['messages'] );
        $this->assertSame( $migrations[2]->checksum(), $context->records["site\n20261101090200_wp9_loops"]['checksum'] );
        $this->assertSame( 'done', $migrations[2]->state( $context ) );

        // rolled back with its down: steps
        $rollback = ( new expMigrationRunner( $context ) )->rollback( array( $migrations[2] ) );
        $this->assertSame( 0, $rollback['exit'], implode( '; ', $rollback['errors'] ) );
        $this->assertSame( array( 'undone' ), array_column( $rollback['migrations'], 'action' ) );
        $this->assertSame( array( 'undo loops' ), array_slice( expMigrationYamlRecordStep::names( 'apply' ), -1 ) );
        $this->assertSame( 'pending', $migrations[2]->state( $context ) );

        // a failing YAML migration: rolled back and recorded failed, like a PHP one
        $failing = $this->migration( "- type: wp9_record\n  name: x\n  fail: true\n", '20261101121000_wp9_failing' );
        $context = new expMigrationRunnerTestContext( true );
        $failed = ( new expMigrationRunner( $context ) )->run( array( $failing ) );
        $this->assertSame( 1, $failed['exit'] );
        $this->assertSame( array( 'begin', 'rollback' ), array_values( array_intersect( $context->calls, array( 'begin', 'commit', 'rollback' ) ) ) );
        $this->assertContains( 'steps[0] (wp9_record): failed on purpose: x', $failed['migrations'][0]['messages'] );
        $this->assertSame( 'failed', $context->records["site\n20261101121000_wp9_failing"]['state'] );

        // the check
        $ok = expMigrationCheck::run( array( 'paths' => array( __DIR__ . '/fixtures/yaml' ), 'root' => self::$root ) );
        $this->assertSame( 0, $ok['exit'], implode( '; ', $ok['errors'] ) );
        $this->assertSame( 3, $ok['checked'] );
        $this->assertSame( array( true, true, true ), array_column( $ok['migrations'], 'ok' ) );
        $broken = expMigrationCheck::run( array( 'paths' => array( __DIR__ . '/fixtures/yaml-broken' ), 'root' => self::$root ) );
        $this->assertSame( 1, $broken['exit'] );
        $byId = array_column( $broken['migrations'], null, 'id' );
        $this->assertStringStartsWith( 'YAML, line 5: ', $byId['20261101091000_wp9_bad_yaml']['errors'][0] );
        $text = implode( "\n", $byId['20261101091100_wp9_bad_steps']['errors'] );
        foreach ( array( 'steps[0]: the unknown step type no_such_type', 'steps[1]: type is missing', 'steps[2] (wp9_record).mode: "replace"',
                         'steps[4] (wp9_record).references.other: the step gives no value size', 'steps[5] (wp9_record).name: a ${ without its closing }',
                         'steps[6]: the unknown key type of a loop', 'steps[7].while: value takes exactly one operator',
                         'steps[7].steps[0].if: a condition text must be a placeholder' ) as $expected )
            $this->assertStringContainsString( $expected, $text );
        $site = expMigrationCheck::run( array( 'paths' => array( __DIR__ . '/fixtures/site' ), 'root' => self::$root ) );
        $byId = array_column( $site['migrations'], null, 'id' );
        $this->assertStringContainsString( 'steps[1] (content): class is missing (mode create)', implode( "\n", $byId['20261004100000_news_content']['errors'] ) );

        // a registry of a test instead of the settings
        $check = expMigrationCheck::run( array( 'paths' => array( __DIR__ . '/fixtures/yaml' ), 'root' => self::$root,
                                                'registry' => new expMigrationStepRegistry( array( 'reference' => 'expMigrationStepReference' ) ) ) );
        $this->assertStringContainsString( 'the unknown step type wp9_record', implode( "\n", $check['errors'] ) );
    }

    /** MY-12 */
    public function testSkeleton()
    {
        $this->needsParser();
        $skeleton = expMigrationGenerator::skeleton( 'News folder', array( 'type' => 'yml', 'directory' => $this->dir() . '/migrations',
                                                                           'root' => self::$root, 'time' => mktime( 12, 0, 0, 11, 1, 2026 ),
                                                                           'author' => 'Site team' ) );
        $this->assertTrue( $skeleton['ok'], implode( '; ', $skeleton['errors'] ) );
        $this->assertSame( '20261101120000_news_folder', $skeleton['id'] );
        $this->assertStringEndsWith( '/20261101120000_news_folder.yml', $skeleton['file'] );
        $this->assertSame( '', $skeleton['class'] );
        $this->assertSame( array(), expMigrationGenerator::write( $skeleton ) );
        $check = expMigrationCheck::run( array( 'paths' => array( $skeleton['file'] ), 'root' => self::$root ) );
        $this->assertSame( 0, $check['exit'], implode( '; ', $check['errors'] ) );
        $this->assertSame( array( '20261101120000_news_folder: no steps: the migration does nothing' ), $check['warnings'] );
        $definition = $check['migrations'][0];
        $this->assertSame( 'yml', $definition['type'] );
        $header = expMigrationDefinition::parseHeader( $skeleton['content'] );
        $this->assertSame( array( 'description' => 'News folder', 'author' => 'Site team' ), $header );
    }
}
