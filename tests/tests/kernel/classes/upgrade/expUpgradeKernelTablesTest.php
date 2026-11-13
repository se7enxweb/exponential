<?php
/**
 * 6.0.15-kernel-tables: every table of the kernel schema a database lacks is created, so a database upgraded across
 * many releases gets the ones it never had (ezapprove_items on a site from the eZ Publish 3 era, where the approve
 * workflow event had never run). And 6.0.15-user-session-table creates expuser_session_info too, which it left out.
 * Run against a real SQLite database in var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUpgradeKernelTablesTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private static $root;

    /** @var eZSQLite3DB|null */
    private $db;

    /** @var string */
    private $file;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
        foreach ( array( 'expUpgradeStepKernelTables' => 'expupgradestepkerneltables.php',
                         'expUpgradeStepUserSessionTable' => 'expupgradestepusersessiontable.php' ) as $class => $file )
            if ( !class_exists( $class ) )
                require_once self::$root . '/kernel/classes/upgrade/steps/' . $file;
    }

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $dir = self::$root . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $this->file = "$dir/upgrade-kernel-tables-" . getmypid() . '-' . mt_rand() . '.sqlite';
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
        expKernelTableSchema::reset();
    }

    private function context( $apply )
    {
        return new expUpgradeContext( $this->db, $apply, array( 'extensions' => array() ) );
    }

    public function testIsRegisteredInTheDatabaseStageBeforeTheScripts()
    {
        $step = new expUpgradeStepKernelTables();
        $this->assertSame( '6.0.15-kernel-tables', $step->id() );
        $this->assertSame( 'database', $step->stage() );
        $ini = file_get_contents( self::$root . '/settings/upgrade.ini' );
        $here = strpos( $ini, "StepList[]=expUpgradeStepKernelTables\n" );
        $this->assertNotFalse( $here );
        $this->assertLessThan( strpos( $ini, "StepList[]=expUpgradeStepUserApproval\n" ), $here,
                               'before the scripts stage: the account approval needs ezapprove_items' );
    }

    public function testItNamesEveryTableOfTheKernelSchema()
    {
        $tables = ( new expUpgradeStepKernelTables() )->tables();
        $this->assertSame( array_keys( expKernelTableSchema::read() ), $tables );
        $this->assertContains( 'ezapprove_items', $tables );
        $this->assertContains( 'expuser_session_info', $tables );
        $this->assertGreaterThan( 100, count( $tables ) );
    }

    public function testTheSessionStepCreatesTheSessionInfoTableToo()
    {
        $this->assertSame( array( 'expuser_session', 'expuser_session_info' ), ( new expUpgradeStepUserSessionTable() )->tables() );
    }

    public function testAMissingKernelTableIsCreatedAndASecondRunDoesNothing()
    {
        $step = new expUpgradeStepKernelTables();
        $all = $step->tables();
        // a database with every kernel table but ezapprove_items, as on an old site
        expKernelTableSchema::install( array_values( array_diff( $all, array( 'ezapprove_items' ) ) ), $this->db );
        expKernelTableSchema::reset();
        $detect = $step->detect( $this->context( false ) );
        $this->assertSame( expUpgradeStep::MISSING, $detect['effect'] );
        $this->assertStringContainsString( 'ezapprove_items', $detect['detail'] );
        $this->assertSame( array( 'ezapprove_items' ), $step->missing( $this->context( false ) )['tables'] );
        $done = $step->apply( $this->context( true ) );
        $this->assertNotEmpty( $done );
        expKernelTableSchema::reset();
        $this->assertTrue( expKernelTableSchema::exists( 'ezapprove_items', $this->db, true ) );
        $present = $step->detect( $this->context( false ) );
        $this->assertSame( expUpgradeStep::PRESENT, $present['effect'] );
        $this->assertSame( 'in place: all ' . count( $all ) . ' tables of the kernel schema', $present['detail'], 'a count, not every name' );
        $this->assertSame( array(), $step->apply( $this->context( true ) ), 'a second run does nothing' );
    }
}
