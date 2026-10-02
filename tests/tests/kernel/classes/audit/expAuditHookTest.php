<?php
/**
 * The kernel's audit hook points (doc/bc/6.0/audit.md, stage 3): expAuditHook and the ezpEvent bridge.
 *
 *  AH-01 — every kernel name of the catalogue is raised somewhere in the kernel (a hook point names it), except the
 *          audit's own names (stages 2, 4, 5) and the re-authentication of the audit module (stage 4)
 *  AH-02 — emit() of a name that is off returns null without building its data; a muted name records nothing
 *  AH-03 — a record carries the catalogue's verb; a call site's own verb for a kernel name goes to after.action;
 *          a name outside the catalogue keeps its verb
 *  AH-04 — the descriptions read the installation's own database: node 2, the admin user, the Administrator role
 *  AH-05 — the bridge records a mapped ezpEvent once per notify, also after attach() ran twice on one instance (a
 *          persistent Velocity worker), hashes the arguments of session/* events, passes a filter's value through
 *
 * Live-database tests: the kernel is started once on the admin siteaccess against the installation's own database
 * (no test database, nothing written to it); audit records go to var/tmp/audit-tests/ with test settings and keys,
 * never to the live log.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/audit/expAuditHookTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/fixtures/expaudittestfixtures.php';

class expAuditHookTest extends PHPUnit\Framework\TestCase
{
    /** @var string|null why the kernel could not be started */
    protected static $bootError = null;

    /** @var bool */
    protected static $booted = false;

    protected $dir;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if ( self::$booted || self::$bootError !== null )
            return;
        try
        {
            $root = dirname( __DIR__, 5 );
            chdir( $root );
            if ( !class_exists( 'eZScript' ) )
                require_once $root . '/autoload.php';
            if ( !eZScript::instance()->isInitialized() )
            {
                $script = eZScript::instance( array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
                $script->startup();
                $script->setUseSiteAccess( 'admin' );
                $script->initialize();
                eZExecution::setCleanExit();
            }
            if ( !eZDB::instance()->isConnected() )
                throw new RuntimeException( 'no database connection' );
            self::$booted = true;
        }
        catch ( Throwable $e )
        {
            self::$bootError = $e->getMessage();
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        if ( self::$bootError !== null )
            $this->markTestSkipped( 'Kernel not available: ' . self::$bootError );
        $this->dir = expAuditTestFixtures::setUp( $this->name(), array( 'AuditEventSettings/Enabled' => array( 'content.*', 'access.*', 'system.*' ),
                                                                       'AuditEventSettings/Disabled' => array( 'content.object.publish' ) ) );
    }

    protected function tearDown(): void
    {
        expAuditHook::reset();
        expAuditTestFixtures::tearDown();
        parent::tearDown();
    }

    protected function records()
    {
        expAudit::flush();
        $out = array();
        foreach ( array( 'content', 'access', 'system', 'commerce' ) as $c )
            foreach ( expAuditTestFixtures::records( $this->dir, $c ) as $r )
                if ( strpos( $r['name'], 'system.audit.' ) !== 0 )
                    $out[] = $r;
        return $out;
    }

    public function testEveryKernelNameHasAHookPoint()
    {
        $root = dirname( __DIR__, 5 ) . '/';
        $code = '';
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . 'kernel', FilesystemIterator::SKIP_DOTS ) );
        foreach ( $iterator as $file )
            if ( substr( $file->getFilename(), -4 ) === '.php' && strpos( $file->getPathname(), '/kernel/classes/audit/' ) === false )
                $code .= file_get_contents( $file->getPathname() );
        foreach ( array( 'lib/ezutils/classes/ezmodule.php', 'lib/ezutils/classes/ezini.php', 'lib/ezutils/classes/ezprepairqueue.php', 'settings/audit.ini' ) as $f )
            $code .= file_get_contents( $root . $f );
        $missing = array();
        foreach ( array_keys( expAuditTaxonomy::catalogue() ) as $name )
        {
            if ( strpos( $name, 'system.audit.' ) === 0 || strpos( $name, 'access.session.reauth' ) === 0 || $name === 'system.error.fatal'
                 || $name === 'system.setting.undo' )
                continue;
            if ( strpos( $code, "'" . $name . "'" ) === false && strpos( $code, '=' . $name . "\n" ) === false )
                $missing[] = $name;
        }
        $this->assertSame( array(), $missing, 'catalogue names without a hook point' );
    }

    public function testOffAndMutedRecordNothing()
    {
        $built = false;
        $id = expAuditHook::emit( 'content.object.publish', function () use ( &$built ) { $built = true; return array(); } );
        $this->assertNull( $id );
        $this->assertFalse( $built, 'the data of a name that is off is never built' );
        $this->assertNull( expAuditHook::muted( 'content.node.move', function () {
            return expAuditHook::emit( 'content.node.move', array( 'object' => array( 'type' => 'node', 'id' => 2 ) ) );
        } ) );
        $this->assertNotNull( expAuditHook::emit( 'content.node.move', array( 'object' => array( 'type' => 'node', 'id' => 2 ) ) ) );
        $this->assertCount( 1, $this->records() );
    }

    public function testCatalogueVerbs()
    {
        expAuditHook::emit( 'content.node.section', array( 'object' => array( 'type' => 'node', 'id' => 2 ) ) );
        expAuditHook::emit( 'content.section.change', array( 'object' => array( 'type' => 'section', 'id' => 1 ), 'verb' => 'create' ) );
        expAuditHook::emit( 'system.legacy.hook_test', array( 'verb' => 'ping' ) );
        $by = array();
        foreach ( $this->records() as $r )
            $by[$r['name']] = $r;
        $this->assertSame( 'assign', $by['content.node.section']['verb'] );
        $this->assertSame( 'change', $by['content.section.change']['verb'] );
        $this->assertSame( 'create', $by['content.section.change']['after']['action'] );
        $this->assertSame( 'ping', $by['system.legacy.hook_test']['verb'] );
    }

    public function testDescriptionsFromTheLiveDatabase()
    {
        $node = expAuditHook::node( 2 );
        $this->assertSame( 'node', $node['type'] );
        $this->assertSame( 2, $node['id'] );
        $this->assertSame( '/1/2/', $node['path'] );
        $this->assertNotEmpty( $node['name'] );
        $admin = eZUser::fetchByName( 'admin' );
        $user = expAuditHook::user( $admin );
        $this->assertSame( 'admin', $user['login'] );
        $this->assertArrayNotHasKey( 'email', $user, 'the e-mail address only when asked' );
        $role = eZRole::fetchByName( 'Administrator' );
        if ( $role )
        {
            $d = expAuditHook::role( $role );
            $this->assertSame( 'Administrator', $d['name'] );
            $this->assertNotEmpty( expAuditHook::policies( $role ) );
        }
        $this->assertSame( array( 'type' => 'node', 'id' => 999999999 ), expAuditHook::node( 999999999 ) );
    }

    public function testBridgeRecordsOnceAndHashesSessions()
    {
        $ini = eZINI::instance( 'audit.ini' );
        $old = $ini->variable( 'AuditBridgeSettings', 'Bridge' );
        $ini->setVariable( 'AuditBridgeSettings', 'Bridge', array( 'session/regenerate' => 'access.session.regenerate',
                                                                   'audittest/filter' => 'system.legacy.bridge_test' ) );
        try
        {
            $events = new ezpEvent( false );
            expAuditBridge::attach( $events );
            expAuditBridge::attach( $events );
            $events->notify( 'session/regenerate', array( 'oldsessionid0123456789', 'newsessionid0123456789' ) );
            $this->assertSame( 'value', $events->filter( 'audittest/filter', 'value', 42 ) );
            $records = $this->records();
        }
        finally
        {
            $ini->setVariable( 'AuditBridgeSettings', 'Bridge', $old );
        }
        $names = array_map( function ( $r ) { return $r['name']; }, $records );
        $this->assertSame( array( 'access.session.regenerate', 'system.legacy.bridge_test' ), $names );
        $raw = json_encode( $records );
        $this->assertStringNotContainsString( 'sessionid0123456789', $raw, 'a session id is never recorded' );
        foreach ( $records[0]['after']['args'] as $arg )
            $this->assertStringStartsWith( 'h:', $arg );
        $this->assertSame( array( 'value', 42 ), $records[1]['after']['args'] );
    }
}
