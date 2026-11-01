<?php
/**
 * The step types cache, search and autoload, and the smart cache list of a run, without a database: their schemas, the
 * checks of their steps, their plans, the caches they declare, the list a run clears and hands to the deploy, and the
 * autoload generator run in a process of its own (replaced here).
 *
 *  MC-01 the kernel registers the three types from migration.ini; their schemas are valid, their examples valid steps;
 *        each undoes itself
 *  MC-02 cache: something to clear, ids and tags of ezcache, objects as identifiers, when: end or now
 *  MC-03 search: reindex by objects or the run's, full by classes, queue
 *  MC-04 autoload: the scopes, never the kernel's own array
 *  MC-05 plans name what would happen
 *  MC-06 the caches a cache step declares join the run's list
 *  MC-07 the run's cache list: the applied and undone migrations' caches and the deferred ones, sorted, each once;
 *        the deploy command
 *  MC-08 a cache step for the end leaves its caches with the context; the context notes touched objects once
 *  MC-09 exp:velocity deploy --caches clears the caches named, whatever changed; the others stay as planned
 *  MC-10 autoload runs the generator with the scope's option and checks the file; a failing generator fails the step
 *  MC-11 [RunSettings] ClearCaches and Reindex, and the options that override them
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationCacheSearchAutoloadStepsTest extends PHPUnit\Framework\TestCase
{
    const TYPES = array( 'cache' => 'expMigrationStepCache', 'search' => 'expMigrationStepSearch', 'autoload' => 'expMigrationStepAutoload' );

    /**
     * @param string $yaml
     * @return string the errors of the steps, one per line
     */
    private static function errors( $yaml )
    {
        $errors = array();
        expMigrationYaml::fromText( $yaml, expMigrationStepRegistry::fromSettings(), $errors );
        return implode( "\n", $errors );
    }

    /**
     * @param string $name
     * @param array $step
     * @return array the step with the defaults of its schema
     */
    private static function step( $name, array $step )
    {
        $type = expMigrationStepRegistry::fromSettings()->get( $name );
        return expMigrationStepSchema::withDefaults( $type->schema(), $step );
    }

    /**
     * @param string $name
     * @param array $step
     * @return string[]
     */
    private static function plan( $name, array $step )
    {
        return expMigrationStepRegistry::fromSettings()->get( $name )->plan( self::step( $name, $step ), null );
    }

    protected function setUp(): void
    {
        if ( !expMigrationYamlParser::available() )
            $this->markTestSkipped( (string)expMigrationYamlParser::problem() );
        expMigrationStepRegistry::forget();
    }

    protected function tearDown(): void
    {
        expMigrationProcess::setRunner( null );
    }

    /** MC-01 */
    public function testRegistered()
    {
        $registry = expMigrationStepRegistry::fromSettings();
        $this->assertSame( array(), $registry->errors() );
        foreach ( self::TYPES as $name => $class )
        {
            $type = $registry->get( $name );
            $this->assertInstanceOf( $class, $type, $name );
            $schema = $type->schema();
            $this->assertSame( array(), expMigrationStepSchema::problems( $schema ), $name );
            $this->assertNotEmpty( $schema['values'], "$name gives values to references:" );
            $this->assertSame( '', self::errors( $schema['example'] ), "the example of $name is a valid step" );
            $this->assertInstanceOf( 'expMigrationRevertibleStepType', $type, "$name undoes itself" );
            $this->assertNotInstanceOf( 'expMigrationPartlyRevertibleStepType', $type, $name );
        }
    }

    /** MC-02 */
    public function testCacheChecks()
    {
        $this->assertSame( '', self::errors( "- type: cache\n  caches: [ content, template-block ]\n"
                                             . "- type: cache\n  tags: [ ini ]\n  when: now\n"
                                             . "- type: cache\n  objects: [ 2, 'remote:home', 'path:media' ]\n"
                                             . "- type: cache\n  caches: [ '\${ref.cache}' ]\n" ) );
        $this->assertStringContainsString( 'name what is cleared: caches:, tags: or objects:', self::errors( "- type: cache\n  when: now\n" ) );
        $errors = self::errors( "- type: cache\n  caches: [ no-such-cache, '' ]\n  tags: [ no-such-tag ]\n  objects: [ 'tag:x' ]\n" );
        foreach ( array( 'caches[0]: no-such-cache is no id of ezcache', 'caches[1]: a name of ezcache', 'tags[0]: no-such-tag is no tag of ezcache',
                         'objects[0]:' ) as $expected )
            $this->assertStringContainsString( $expected, $errors );
        $this->assertStringContainsString( 'when', self::errors( "- type: cache\n  caches: [ content ]\n  when: later\n" ) );
    }

    /** MC-03 */
    public function testSearchChecks()
    {
        $this->assertSame( '', self::errors( "- type: search\n"
                                             . "- type: search\n  mode: reindex\n  objects: [ 'remote:home', 12 ]\n  queue: false\n"
                                             . "- type: search\n  mode: full\n  classes: [ article, folder ]\n" ) );
        $this->assertStringContainsString( 'classes[0]: the identifier of a class', self::errors( "- type: search\n  mode: full\n  classes: [ 'not a class' ]\n" ) );
        $this->assertStringContainsString( 'objects[0]:', self::errors( "- type: search\n  objects: [ 'tag:x' ]\n" ) );
        $this->assertStringContainsString( 'steps[0] (search).classes: only in the mode full', self::errors( "- type: search\n  classes: [ article ]\n" ) );
    }

    /** MC-04 */
    public function testAutoloadChecks()
    {
        $this->assertSame( '', self::errors( "- type: autoload\n- type: autoload\n  scope: kernel-override\n" ) );
        $this->assertStringContainsString( 'scope', self::errors( "- type: autoload\n  scope: kernel\n" ) );
    }

    /** MC-05 */
    public function testPlans()
    {
        $this->assertSame( array( 'clear the caches content, tag:ini after the run' ), self::plan( 'cache', array( 'caches' => array( 'content' ), 'tags' => array( 'ini' ) ) ) );
        $this->assertSame( array( 'clear the caches template-block now', 'clear the view caches of 2 now' ),
                           self::plan( 'cache', array( 'caches' => array( 'template-block' ), 'objects' => array( 2 ), 'when' => 'now' ) ) );
        $this->assertSame( array( 'queue for the search index the objects the run created or changed so far' ), self::plan( 'search', array() ) );
        $this->assertSame( array( 'index at once the objects "remote:home"' ), self::plan( 'search', array( 'objects' => array( 'remote:home' ), 'queue' => false ) ) );
        $this->assertSame( array( 'queue for the search index every published object of the classes article' ),
                           self::plan( 'search', array( 'mode' => 'full', 'classes' => array( 'article' ) ) ) );
        $this->assertSame( array( 'regenerate var/autoload/ezp_extension.php (bin/php/ezpgenerateautoloads.php -e)' ), self::plan( 'autoload', array() ) );
        $this->assertSame( array( 'regenerate var/autoload/ezp_override.php (bin/php/ezpgenerateautoloads.php -o)' ), self::plan( 'autoload', array( 'scope' => 'kernel-override' ) ) );
    }

    /** MC-06 */
    public function testDeclaredCaches()
    {
        $registry = expMigrationStepRegistry::fromSettings();
        $this->assertSame( array( 'template-block', 'tag:ini' ), $registry->get( 'cache' )->caches( self::step( 'cache', array( 'caches' => array( 'template-block' ), 'tags' => array( 'ini' ) ) ) ) );
        $this->assertSame( array( 'content' ), $registry->get( 'cache' )->caches( self::step( 'cache', array( 'objects' => array( 2 ) ) ) ) );
        $this->assertSame( array(), $registry->get( 'cache' )->caches( self::step( 'cache', array( 'caches' => array( '${ref.c}' ) ) ) ), 'a placeholder is no name yet' );
        $this->assertSame( array(), $registry->get( 'search' )->caches( self::step( 'search', array() ) ) );
        $this->assertSame( array(), $registry->get( 'autoload' )->caches( self::step( 'autoload', array() ) ) );
    }

    /** MC-07 */
    public function testRunCacheList()
    {
        $migration = function ( array $caches ) {
            return new class( $caches ) {
                private $caches;
                public function __construct( array $caches ) { $this->caches = $caches; }
                public function caches() { return $this->caches; }
            };
        };
        $byId = array( 'a' => $migration( array( 'template-block', 'content' ) ), 'b' => $migration( array( 'urlalias', 'content' ) ),
                       'c' => $migration( array( 'ini' ) ), 'd' => new stdClass() );
        $report = array( 'migrations' => array( array( 'id' => 'a', 'action' => 'applied' ), array( 'id' => 'b', 'action' => 'undone' ),
                                                array( 'id' => 'c', 'action' => 'not-run' ), array( 'id' => 'd', 'action' => 'applied' ) ) );
        $this->assertSame( array( 'content', 'exphttpcache', 'template-block', 'urlalias' ), expMigrationAfterRun::caches( $report, $byId, array( ' exphttpcache ', 'content' ) ) );
        $this->assertSame( array(), expMigrationAfterRun::caches( array( 'migrations' => array() ), $byId ) );
        $this->assertSame( './console exp:velocity deploy --caches=content,template-block --apply', expMigrationAfterRun::deployCommand( array( 'template-block', 'content', '' ) ) );
        $this->assertSame( '', expMigrationAfterRun::deployCommand( array() ) );
        $this->assertSame( 'id', expMigrationAfterRun::kind( 'content' ) );
        $this->assertSame( 'id', expMigrationAfterRun::kind( 'ini' ), 'an id first' );
        $this->assertSame( 'tag', expMigrationAfterRun::kind( 'tag:ini' ) );
        $this->assertSame( 'tag', expMigrationAfterRun::kind( 'image' ) );
        $this->assertSame( '', expMigrationAfterRun::kind( 'tag:imagealias' ) );
        $this->assertSame( '', expMigrationAfterRun::kind( 'no-such-cache' ) );
    }

    /** MC-08 */
    public function testDeferredAndTouched()
    {
        $context = new expMigrationContext( new stdClass(), false, array( 'run-id' => '20261105-120000-abcdef', 'user' => null, 'no-mirror' => true ) );
        $result = expMigrationStepRegistry::fromSettings()->get( 'cache' )->apply( self::step( 'cache', array( 'caches' => array( 'template-block' ), 'tags' => array( 'ini' ) ) ), $context );
        $this->assertSame( array( 'the caches template-block, tag:ini are cleared after the run' ), $result['lines'] );
        $this->assertSame( array( 'caches' => 'template-block,tag:ini', 'objects' => 0 ), $result['values'] );
        $context->deferCaches( array( 'ini', 'content', '' ) );
        $this->assertSame( array( 'template-block', 'tag:ini', 'ini', 'content' ), $context->deferredCaches() );
        $context->touchObject( array( 12, '13', 12, 0, -4, 'x' ) );
        $context->touchObject( 57 );
        $this->assertSame( array( 12, 13, 57 ), $context->touchedObjects() );
        $this->assertSame( array( 'search: nothing to undo' ), expMigrationStepRegistry::fromSettings()->get( 'search' )->revert( self::step( 'search', array() ), $context ) );
    }

    /** MC-09 */
    public function testDeployCaches()
    {
        $changes = array( 'known' => true, 'since' => 1700000000, 'changed' => array() );
        $plan = expVelocityDeploy::plan( $changes, array() );
        $this->assertFalse( $plan['rendered'][0] );
        $this->assertFalse( $plan['ini'][0] );
        $plan = expVelocityDeploy::plan( $changes, array( 'caches' => 'template-block, content,ini' ) );
        $this->assertSame( array( true, 'cleared: --caches (content, template-block)' ), $plan['rendered'] );
        $this->assertSame( array( true, 'cleared: --caches (ini)' ), $plan['ini'] );
        $this->assertFalse( $plan['template'][0], 'a cache not named stays as planned' );
        $this->assertSame( array( 'a', 'b' ), expVelocityDeploy::cacheList( ' a,b,,a ' ) );
        $this->assertSame( array( 'a' ), expVelocityDeploy::cacheList( array( 'a', ' a ' ) ) );
    }

    /** MC-10 */
    public function testAutoloadRunsTheGenerator()
    {
        $calls = array();
        expMigrationProcess::setRunner( function ( array $command, $cwd, $timeout ) use ( &$calls ) {
            $calls[] = array_slice( $command, 1 );
            return array( 0, '' );
        } );
        $context = new expMigrationContext( new stdClass(), true, array( 'run-id' => '20261105-120000-abcdef', 'user' => null, 'no-mirror' => true ) );
        $result = expMigrationStepRegistry::fromSettings()->get( 'autoload' )->apply( self::step( 'autoload', array( 'scope' => 'kernel-override' ) ), $context );
        $this->assertSame( array( 'bin/php/ezpgenerateautoloads.php', '-o', '-q', '--allow-root-user' ), $calls[0] );
        $this->assertSame( '-l', $calls[1][0] );
        $this->assertStringEndsWith( '/var/autoload/ezp_override.php', $calls[1][1] );
        $this->assertSame( array( 'file' => 'var/autoload/ezp_override.php' ), $result['values'] );

        expMigrationProcess::setRunner( function () { return array( 0, "PHP Fatal error: something\nmore" ); } );
        try
        {
            expMigrationStepRegistry::fromSettings()->get( 'autoload' )->apply( self::step( 'autoload', array() ), $context );
            $this->fail( 'a generator that printed a fatal error fails the step' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'the autoload generator failed (exit 0): PHP Fatal error: something | more', $e->getMessage() );
        }
        $this->assertSame( 'b | c', expMigrationProcess::tail( "a\nb\nc", 2 ) );
    }

    /** MC-11 */
    public function testSettings()
    {
        $this->assertTrue( expMigrationAfterRun::clearsCaches() );
        $this->assertSame( 'queue', expMigrationAfterRun::reindexMode() );
        $context = new expMigrationContext( new stdClass(), false, array( 'run-id' => '20261105-120000-abcdef', 'user' => null, 'no-cache-clear' => true, 'reindex' => 'now' ) );
        $this->assertFalse( expMigrationAfterRun::clearsCaches( $context ) );
        $this->assertSame( 'now', expMigrationAfterRun::reindexMode( $context ) );
        $context = new expMigrationContext( new stdClass(), false, array( 'run-id' => '20261105-120000-abcdef', 'user' => null, 'reindex' => 'sometimes' ) );
        $this->assertSame( 'queue', expMigrationAfterRun::reindexMode( $context ), 'an unknown mode is queue' );
    }
}
