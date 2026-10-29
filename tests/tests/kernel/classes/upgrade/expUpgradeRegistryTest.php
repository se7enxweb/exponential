<?php
/**
 * The upgrade step framework without a database: the order of the steps, their dependencies, the state of a step
 * from detection and record, the options, and the kernel's registered steps against the kernel schema.
 *
 *  UP-01 order: by stage, then version, then as declared; after() moves a step behind what it names
 *  UP-02 problems: duplicate id, unknown stage, after() naming no step, a cycle, a dependency in a later stage,
 *        a class that does not exist or is no step
 *  UP-03 state: detection and record give done, not-needed, pending, failed, unknown
 *  UP-04 options: --stages, comma lists, the stage state, the exit code
 *  UP-05 the SQL splitter of the schema steps
 *  UP-06 upgrade.ini lists every step class of kernel/classes/upgrade/steps, ids are unique and versioned, each
 *        table, column and index a schema step names is in share/db_schema.dba
 *  UP-07 the record table expupgrade_step in share/db_schema.dba, the kernel schemas and the 6.0.15 update files
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expUpgradeRegistryTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
        foreach ( array( 'expupgradestep', 'expupgradebasestep', 'expupgraderegistry', 'expupgrade', 'expupgradecatalog', 'expupgradeschemastep' ) as $f )
            require_once self::$root . "/kernel/classes/upgrade/$f.php";
    }

    private function step( $id, $stage = 'database', $version = '6.0.15', array $after = array() )
    {
        return new class( $id, $stage, $version, $after ) extends expUpgradeBaseStep {
            private $i;
            private $s;
            private $v;
            private $a;
            public function __construct( $i, $s, $v, $a ) { $this->i = $i; $this->s = $s; $this->v = $v; $this->a = $a; }
            public function id() { return $this->i; }
            public function stage() { return $this->s; }
            public function version() { return $this->v; }
            public function after() { return $this->a; }
            public function description() { return $this->i; }
            public function detect( expUpgradeContext $c ) { return self::effect( self::PRESENT ); }
            public function plan( expUpgradeContext $c ) { return array(); }
            public function apply( expUpgradeContext $c ) { return array(); }
        };
    }

    private function ids( array $steps )
    {
        return array_map( function ( $s ) { return $s->id(); }, $steps );
    }

    /** UP-01 */
    public function testOrderByStageVersionDeclared()
    {
        $steps = array(
            $this->step( 's1', 'scripts', '6.0.15' ),
            $this->step( 'd2', 'database', '6.0.16' ),
            $this->step( 'd1', 'database', '6.0.15' ),
            $this->step( 'f1', 'finish', '6.0.15' ),
            $this->step( 'd3', 'database', '6.0.15' ),
            $this->step( 'c1', 'code', '6.0.20' ),
            $this->step( 'd10', 'database', '6.0.9' ),
        );
        $errors = array();
        $this->assertSame( array( 'c1', 'd10', 'd1', 'd3', 'd2', 's1', 'f1' ), $this->ids( expUpgradeRegistry::order( $steps, $errors ) ) );
        $this->assertSame( array(), $errors );
    }

    /** UP-01 */
    public function testAfterMovesAStepBehindItsDependency()
    {
        $steps = array( $this->step( 'a', 'database', '6.0.15', array( 'c' ) ), $this->step( 'b' ), $this->step( 'c' ) );
        $errors = array();
        $this->assertSame( array( 'b', 'c', 'a' ), $this->ids( expUpgradeRegistry::order( $steps, $errors ) ) );
        $this->assertSame( array(), $errors );
    }

    /** UP-02 */
    public function testProblemsAreReported()
    {
        $errors = array();
        expUpgradeRegistry::order( array( $this->step( 'a' ), $this->step( 'a' ) ), $errors );
        $this->assertStringContainsString( 'two steps have the id a', $errors[0] );

        $errors = array();
        $out = expUpgradeRegistry::order( array( $this->step( 'x', 'nowhere' ) ), $errors );
        $this->assertSame( array(), $out );
        $this->assertStringContainsString( 'unknown stage nowhere', $errors[0] );

        $errors = array();
        expUpgradeRegistry::order( array( $this->step( 'a', 'database', '6.0.15', array( 'missing' ) ) ), $errors );
        $this->assertStringContainsString( 'which is not registered', $errors[0] );

        $errors = array();
        $out = expUpgradeRegistry::order( array( $this->step( 'a', 'database', '6.0.15', array( 'b' ) ), $this->step( 'b', 'database', '6.0.15', array( 'a' ) ) ), $errors );
        $this->assertCount( 2, $out, 'a cycle keeps both steps' );
        $this->assertStringContainsString( 'cycle', $errors[0] );

        $errors = array();
        expUpgradeRegistry::order( array( $this->step( 'a', 'database', '6.0.15', array( 'b' ) ), $this->step( 'b', 'scripts' ) ), $errors );
        $this->assertStringContainsString( 'in a later stage', implode( ' ', $errors ) );

        $registry = expUpgradeRegistry::fromClasses( array( 'NoSuchUpgradeStepClass', 'stdClass' ) );
        $this->assertSame( array(), $registry->steps() );
        $this->assertStringContainsString( 'does not exist', $registry->errors()[0] );
        $this->assertStringContainsString( 'is not an upgrade step', $registry->errors()[1] );
    }

    /** UP-03 */
    public function testStateOfAStep()
    {
        $present = array( 'effect' => expUpgradeStep::PRESENT, 'detail' => '' );
        $missing = array( 'effect' => expUpgradeStep::MISSING, 'detail' => '' );
        $na = array( 'effect' => expUpgradeStep::NOT_APPLICABLE, 'detail' => '' );
        $unknown = array( 'effect' => expUpgradeStep::UNKNOWN, 'detail' => '' );
        $done = array( 'state' => 'done' );
        $failed = array( 'state' => 'failed' );
        $this->assertSame( 'not-needed', expUpgrade::stateOf( $present, null ) );
        $this->assertSame( 'done', expUpgrade::stateOf( $present, $done ) );
        $this->assertSame( 'not-needed', expUpgrade::stateOf( $present, $failed ), 'fixed by hand after a failure' );
        $this->assertSame( 'pending', expUpgrade::stateOf( $missing, null ) );
        $this->assertSame( 'pending', expUpgrade::stateOf( $missing, $done ), 'drift: the effect went missing' );
        $this->assertSame( 'failed', expUpgrade::stateOf( $missing, $failed ) );
        $this->assertSame( 'not-needed', expUpgrade::stateOf( $na, null ) );
        $this->assertSame( 'unknown', expUpgrade::stateOf( $unknown, $done ) );
        $this->assertSame( 'not-needed', expUpgrade::stateOf( $missing, null, false ), 'not for this engine' );
    }

    /** UP-04 */
    public function testOptions()
    {
        $errors = array();
        $this->assertSame( array( 'code', 'libraries', 'database', 'scripts', 'finish' ), expUpgrade::stages( null, $errors ) );
        $this->assertSame( array( 'database', 'scripts' ), expUpgrade::stages( 'scripts, database', $errors ), 'always in the fixed order' );
        $this->assertSame( array(), $errors );
        expUpgrade::stages( 'database,nope', $errors );
        $this->assertStringContainsString( 'unknown stage nope', $errors[0] );
        $this->assertSame( array( 'a', 'b', 'c' ), expUpgrade::listOption( array( 'a,b', 'c', 'a', ' ' ) ) );
        $this->assertSame( array(), expUpgrade::listOption( false ) );

        $this->assertSame( 'failed', expUpgrade::stageState( array( array( 'state' => 'done' ), array( 'state' => 'failed' ) ) ) );
        $this->assertSame( 'pending', expUpgrade::stageState( array( array( 'state' => 'pending' ), array( 'state' => 'unknown' ) ) ) );
        $this->assertSame( 'done', expUpgrade::stageState( array( array( 'state' => 'not-needed' ), array( 'state' => 'done' ) ) ) );
        $this->assertSame( 'not-needed', expUpgrade::stageState( array() ) );

        $this->assertSame( 4, expUpgrade::exitCode( array() ) );
        $this->assertSame( 1, expUpgrade::exitCode( array( 'exit' => 1 ) ) );
    }

    /** UP-05 */
    public function testStatementSplitter()
    {
        $sql = "ALTER TABLE a ADD COLUMN b int;\n-- a comment\nALTER TABLE a ALTER b SET DEFAULT 0;\n\nCREATE INDEX i ON a ( b );";
        $this->assertSame( array( 'ALTER TABLE a ADD COLUMN b int', 'ALTER TABLE a ALTER b SET DEFAULT 0', 'CREATE INDEX i ON a ( b )' ),
                           expUpgradeCatalog::statements( $sql ) );
        $this->assertSame( array(), expUpgradeCatalog::statements( "-- nothing\n" ) );
    }

    /** @return array class => file of every step class */
    private function stepClasses()
    {
        $classes = array();
        foreach ( glob( self::$root . '/kernel/classes/upgrade/steps/*.php' ) as $file )
        {
            if ( preg_match( '/^class (\w+) extends/m', file_get_contents( $file ), $m ) )
                $classes[$m[1]] = $file;
        }
        return $classes;
    }

    /** @return array share/db_schema.dba, table => definition */
    private static function dba()
    {
        $schema = array();
        include self::$root . '/share/db_schema.dba';
        unset( $schema['_info'] );
        return $schema;
    }

    /** @return string[] StepList[] of settings/upgrade.ini */
    private function stepList()
    {
        preg_match_all( '/^StepList\[\]=(\w+)$/m', file_get_contents( self::$root . '/settings/upgrade.ini' ), $m );
        return $m[1];
    }

    /** UP-06 */
    public function testEveryStepClassIsRegisteredOnce()
    {
        $classes = array_keys( $this->stepClasses() );
        $list = $this->stepList();
        sort( $classes );
        $sorted = $list;
        sort( $sorted );
        $this->assertSame( $classes, $sorted, 'upgrade.ini StepList[] and kernel/classes/upgrade/steps' );
        $ini = file_get_contents( self::$root . '/settings/upgrade.ini' );
        $this->assertSame( 1, preg_match( '/^# stage database[^\n]*\nStepList\[\]=(\w+)$/m', $ini, $m ) );
        $this->assertSame( 'expUpgradeStepRecordTable', $m[1], 'step 0, the record table, is the first of the database stage' );
        $autoload = file_get_contents( self::$root . '/autoload/ezp_kernel.php' );
        foreach ( $classes as $class )
            $this->assertStringContainsString( "'$class'", $autoload, "$class in the kernel autoload array" );
    }

    /** UP-06 */
    public function testStepIdsAndSchemaReferences()
    {
        require_once self::$root . '/kernel/classes/upgrade/expupgradetableinstallerstep.php';
        require_once self::$root . '/kernel/classes/upgrade/expupgradescriptstep.php';
        require_once self::$root . '/kernel/classes/upgrade/expupgraderecord.php';
        $schema = self::dba();
        $ids = array();
        foreach ( $this->stepClasses() as $class => $file )
        {
            require_once $file;
            if ( !class_exists( 'expUserSession', false ) && $class === 'expUpgradeStepUserSessionTable' )
                require_once self::$root . '/kernel/classes/user/expusersession.php';
            $step = new $class();
            $id = $step->id();
            $this->assertMatchesRegularExpression( '/^\d+\.\d+\.\d+-[a-z0-9-]+$/', $id, "$class: id <version>-<name>" );
            $this->assertStringStartsWith( $step->version() . '-', $id, "$class: the id starts with its version" );
            $this->assertArrayNotHasKey( $id, $ids, "$class: unique id" );
            $ids[$id] = $class;
            $this->assertContains( $step->stage(), expUpgradeRegistry::STAGES );
            $this->assertNotSame( '', $step->description() );
            if ( !$step instanceof expUpgradeSchemaStep )
                continue;
            foreach ( $step->tables() as $table )
                $this->assertArrayHasKey( $table, $schema, "$id: table $table in share/db_schema.dba" );
            foreach ( $step->fields() as $table => $fields )
                foreach ( $fields as $field )
                    $this->assertArrayHasKey( $field, $schema[$table]['fields'], "$id: column $table.$field in share/db_schema.dba" );
            foreach ( $step->indexes() as $table => $names )
                foreach ( $names as $name )
                    $this->assertArrayHasKey( $name, $schema[$table]['indexes'], "$id: index $name in share/db_schema.dba" );
        }
        foreach ( $ids as $id => $class )
        {
            $step = new $class();
            foreach ( $step->after() as $dep )
                $this->assertArrayHasKey( $dep, $ids, "$id runs after $dep, a registered step" );
        }
    }

    /** UP-07 */
    public function testRecordTableEverywhere()
    {
        $schema = self::dba();
        $this->assertArrayHasKey( 'expupgrade_step', $schema );
        $this->assertSame( array( 'step_id' ), $schema['expupgrade_step']['indexes']['PRIMARY']['fields'] );
        foreach ( array( 'kernel/sql/mysql/kernel_schema.sql', 'kernel/sql/postgresql/kernel_schema.sql', 'kernel/sql/sqlite/schema.sql' ) as $file )
            $this->assertMatchesRegularExpression( '/CREATE TABLE `?expupgrade_step`? \(/', file_get_contents( self::$root . '/' . $file ), $file );
        foreach ( array( 'mysql', 'postgresql', 'sqlite' ) as $engine )
            $this->assertStringContainsString( 'CREATE TABLE IF NOT EXISTS expupgrade_step (',
                                               file_get_contents( self::$root . "/update/database/$engine/6.0/dbupdate-6.0.0-6.0.15.sql" ), $engine );
    }

    /** UP-07: the SQLite update file runs twice on an empty database without an error for the record table */
    public function testSqliteRecordTableStatementRunsTwice()
    {
        $file = file_get_contents( self::$root . '/update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertSame( 1, preg_match( '/CREATE TABLE IF NOT EXISTS expupgrade_step \(.*?\);/s', $file, $m ) );
        $db = new SQLite3( ':memory:' );
        $this->assertTrue( $db->exec( $m[0] ) );
        $this->assertTrue( $db->exec( $m[0] ) );
        $this->assertTrue( $db->exec( "INSERT INTO expupgrade_step ( step_id, state ) VALUES ( '6.0.15-x', 'done' )" ) );
        $this->assertFalse( @$db->exec( "INSERT INTO expupgrade_step ( step_id, state ) VALUES ( '6.0.15-x', 'done' )" ), 'step_id is the key' );
    }
}
