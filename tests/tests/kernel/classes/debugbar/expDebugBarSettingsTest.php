<?php
/**
 * The server side of the Exp Debug bar on this installation: the registry (settings/debugbar.ini, discovery), the
 * values in effect with their origin, writes through the exp:ini engine with the write log and undo, presets, the
 * lock-out checks, eZDebug's IP check, the summary and the ezjscore functions.
 *
 * Like the subitems column tests, the kernel is started once on the admin siteaccess with the installation's own
 * database (eZScript), and the admin user is logged in. No test database is created.
 *
 * Every write goes to a probe block ([ExpDebugBarTestProbe] of site.ini) in settings/override, never to a real
 * setting, with the ini cache left alone (the site does not even read the probe), and every test undoes its writes:
 * the file is compared byte for byte with what it was before the test, and put back if it differs. The write log is
 * a file of its own under var/tmp/.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/debugbar/
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDebugBarTestRegistry extends expDebugBarRegistry
{
    public function settings()
    {
        $probe = function ( $id, $variable, $type, array $extra = array() ) {
            return $extra + array( 'id' => $id, 'file' => 'site.ini', 'block' => 'ExpDebugBarTestProbe', 'variable' => $variable,
                                   'type' => $type, 'label' => $variable, 'group' => 'output', 'help' => 'test probe',
                                   'values' => array(), 'on' => null, 'off' => null, 'extension' => null, 'source' => 'registry' );
        };
        return parent::settings() + array(
            'probe_bool' => $probe( 'probe_bool', 'Probe', 'bool' ),
            'probe_text' => $probe( 'probe_text', 'ProbeText', 'text' ),
            'probe_enum' => $probe( 'probe_enum', 'ProbeEnum', 'enum', array( 'values' => array( 'inline', 'popup' ) ) ),
            'probe_ips' => $probe( 'probe_ips', 'ProbeIPList', 'iplist' ),
            'probe_users' => $probe( 'probe_users', 'ProbeUsers', 'userlist' ),
            'probe_levels' => $probe( 'probe_levels', 'ProbeLevels', 'list', array( 'values' => array( 'error', 'warning' ) ) ),
        );
    }

    public function setting( $id )
    {
        $s = $this->settings();
        return isset( $s[$id] ) ? $s[$id] : null;
    }

    public function presets()
    {
        return parent::presets() + array(
            'probe_preset' => array( 'id' => 'probe_preset', 'name' => 'Probe', 'description' => '',
                                     'values' => array( 'probe_bool' => 'on', 'probe_text' => 'preset', 'probe_ips' => array( '203.0.113.0/24 ; preset' ) ),
                                     'source' => 'ini' ),
        );
    }
}

class expDebugBarTestSettings extends expDebugBarSettings
{
    public function check( array $def, array $after )
    {
        return $this->accessCheck( $def, $after );
    }
}

class expDebugBarSettingsTest extends PHPUnit\Framework\TestCase
{
    /** @var eZScript|null */
    protected static $script = null;
    /** @var string|null */
    protected static $bootError = null;
    /** @var string The file the probe writes go to */
    protected static $target;
    /** @var string|null Its bytes before the tests */
    protected static $original = null;
    /** @var bool */
    protected static $existed = false;
    /** @var string */
    protected static $logPath;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        try
        {
            $root = dirname( __DIR__, 5 );
            chdir( $root );
            if ( !class_exists( 'eZScript' ) )
                require_once $root . '/autoload.php';
            $script = eZScript::instance( array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
            $script->startup();
            $script->setUseSiteAccess( 'admin' );
            $script->initialize();
            eZExecution::setCleanExit();
            if ( !eZDB::instance()->isConnected() )
                throw new RuntimeException( 'no database connection' );
            self::$script = $script;
        }
        catch ( Throwable $e )
        {
            self::$bootError = $e->getMessage();
            return;
        }
        self::$target = expIniEditor::root() . 'settings/override/site.ini.append.php';
        self::$existed = is_file( self::$target );
        self::$original = self::$existed ? file_get_contents( self::$target ) : null;
        $dir = expIniEditor::root() . 'var/tmp/debugbar-test';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0775, true );
        self::$logPath = $dir . '/debugbar-' . getmypid() . '-' . date( 'Ymd-His' ) . '.log';
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$original !== null && is_file( self::$target ) && file_get_contents( self::$target ) !== self::$original )
        {
            // never leave the probe behind: the bytes of before, owner and mode kept by writing in place
            file_put_contents( self::$target, self::$original, LOCK_EX );
            fwrite( STDERR, "\nexpDebugBarSettingsTest: " . self::$target . " was restored from its bytes before the test\n" );
        }
        parent::tearDownAfterClass();
    }

    public function setUp(): void
    {
        parent::setUp();
        if ( self::$bootError !== null )
            $this->markTestSkipped( 'Kernel not available: ' . self::$bootError );
        $admin = eZUser::fetchByName( 'admin' );
        if ( $admin instanceof eZUser )
            eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ) );
        expDebugBarServerFunctions::$serviceFactory = null;
        expDebugBarServerFunctions::$trustRequest = null;
    }

    public function tearDown(): void
    {
        if ( self::$original !== null )
            $this->assertSame( sha1( self::$original ), sha1( (string)@file_get_contents( self::$target ) ),
                               'settings/override/site.ini.append.php is byte for byte what it was' );
        parent::tearDown();
    }

    protected function service( $siteAccess = 'admin' )
    {
        $s = new expDebugBarTestSettings( new expDebugBarTestRegistry(), $siteAccess, new expDebugBarLog( self::$logPath ) );
        $s->clearCache = false;
        $s->clientIP = false;
        return $s;
    }

    protected function assertRestored()
    {
        clearstatcache();
        $this->assertSame( self::$original, file_get_contents( self::$target ), 'byte for byte what it was' );
    }

    // ------------------------------------------------------------------ registry

    public function testRegistryHasEveryKernelDebugSetting()
    {
        $r = new expDebugBarRegistry();
        $settings = $r->settings();
        foreach ( array( 'debug_output', 'debug_redirection', 'display_debug_warnings', 'debug_log_only', 'debug_mode',
                         'display_included_files', 'always_log', 'script_debug_output', 'debug_by_ip', 'debug_ip_list',
                         'debug_by_user', 'debug_user_list', 'template_debug', 'template_show_xhtml_code',
                         'template_show_used_templates', 'template_show_method_debug', 'template_compile_accumulators',
                         'template_compile_timing_points', 'sql_output', 'sql_debug_transactions', 'regional_debug',
                         'mail_debug_sending', 'mail_debug_receiver', 'condition_debug', 'rest_debug' ) as $id )
            $this->assertArrayHasKey( $id, $settings, $id );
        $this->assertSame( array(), $r->problems() );
        $this->assertSame( 'iplist', $settings['debug_ip_list']['type'] );
        $this->assertSame( array( 'inline', 'popup' ), $settings['debug_mode']['values'] );
        // debug.ini GeneralCondition, discovered
        $this->assertArrayHasKey( 'cond_kernel_content_view', $settings );
        $this->assertSame( 'conditions', $settings['cond_kernel_content_view']['group'] );
        // every group a setting uses is listed
        $groups = array_column( $r->groups(), 'id' );
        foreach ( $settings as $def )
            $this->assertContains( $def['group'], $groups );
        $this->assertSame( array( 'template_work', 'sql_tuning', 'everything', 'off' ), array_keys( $r->presets() ) );
        foreach ( $r->presets() as $p )
            foreach ( array_keys( $p['values'] ) as $id )
                $this->assertArrayHasKey( $id, $settings, "preset {$p['id']} names $id" );
    }

    public function testExtensionDiscovery()
    {
        $r = new expDebugBarRegistry();
        foreach ( $r->discoveredExtensionSettings() as $def )
        {
            $this->assertSame( 'extension', $def['source'] );
            $this->assertContains( $def['extension'], expDebugBarRegistry::activeExtensions() );
            $this->assertMatchesRegularExpression( '/debug/i', $def['variable'] );
            $this->assertFileExists( 'extension/' . $def['extension'] . '/settings' );
        }
        $survey = $r->survey();
        $this->assertSame( $survey['settings'], $survey['registered'] + $survey['discovered_blocks'] + $survey['discovered_extensions'] );
        $this->assertSame( 4, $survey['presets'] );
    }

    public function testBoolWords()
    {
        $def = array( 'on' => null, 'off' => null );
        $this->assertSame( array( 'enabled', 'disabled' ), expDebugBarRegistry::boolWords( $def, 'disabled' ) );
        $this->assertSame( array( 'true', 'false' ), expDebugBarRegistry::boolWords( $def, 'false' ) );
        $this->assertSame( array( 'Enabled', 'Disabled' ), expDebugBarRegistry::boolWords( $def, 'Enabled' ) );
        $this->assertSame( array( 'enabled', 'disabled' ), expDebugBarRegistry::boolWords( $def, null ) );
        $this->assertSame( array( 'yes', 'no' ), expDebugBarRegistry::boolWords( array( 'on' => 'yes', 'off' => 'no' ), 'true' ) );
    }

    // ------------------------------------------------------------------ reading

    public function testDescribeMatchesEZINIAndTheLocator()
    {
        $s = $this->service();
        foreach ( array( 'debug_output', 'template_debug', 'sql_output', 'always_log', 'debug_ip_list', 'condition_debug' ) as $id )
        {
            $d = $s->describe( $id );
            $where = expIniLocator::where( $d['file'], $d['block'], $d['variable'], 'admin' );
            $this->assertSame( $where['effective'], $d['effective'], "$id in effect" );
            $this->assertSame( array_column( $where['files'], 'path' ), array_column( $d['files'], 'path' ), "$id files" );
            if ( $d['files'] )
                $this->assertSame( end( $where['files'] )['path'], $d['origin']['path'] );
        }
        $d = $s->describe( 'debug_output' );
        $this->assertSame( 'bool', $d['type'] );
        $this->assertIsBool( $d['is_on'] );
        $this->assertSame( 'disabled', $d['default'] );
        $this->assertArrayHasKey( 'global', $d['scopes'] );
        $this->assertArrayHasKey( 'siteaccess:admin', $d['scopes'] );
        $this->assertSame( 'siteaccess:admin', $s->defaultScope() );
        $scopes = array_column( $s->scopes(), 'name' );
        $this->assertContains( 'global', $scopes );
        $this->assertContains( 'siteaccess:admin', $scopes );
        $this->assertNotContains( 'default', $scopes );
    }

    public function testDescribeAllIsQuickAndComplete()
    {
        $s = $this->service();
        $t = microtime( true );
        $all = $s->describeAll();
        $this->assertLessThan( 5.0, microtime( true ) - $t );
        $this->assertCount( count( $s->registry()->settings() ), $all );
        foreach ( $all as $d )
            $this->assertArrayNotHasKey( 'error', $d, $d['id'] );
    }

    public function testUnknownSiteAccessAndSettingAreRefused()
    {
        try
        {
            new expDebugBarSettings( null, 'no_such_siteaccess_xyz' );
            $this->fail( 'unknown siteaccess refused' );
        }
        catch ( expIniException $e )
        {
            $this->assertStringContainsString( 'no_such_siteaccess_xyz', $e->getMessage() );
        }
        $this->expectException( InvalidArgumentException::class );
        $this->service()->describe( 'no_such_setting' );
    }

    // ------------------------------------------------------------------ writing and undo

    public function testBoolWriteLogUndoByteIdentical()
    {
        $s = $this->service();
        $r = $s->write( 'probe_bool', 'set', 'on', 'global' );
        $this->assertTrue( $r['ok'] );
        $this->assertTrue( $r['changed'] );
        $this->assertStringContainsString( '+Probe=enabled', $r['diff'] );
        $this->assertSame( 'enabled', $r['setting']['effective'] );
        $this->assertSame( 'global', $r['setting']['origin']['scope'] );
        $e = $r['entry'];
        $this->assertSame( array( 'set', 'probe_bool', 'global', null, 'enabled', 'admin' ),
                           array( $e['op'], $e['setting'], $e['scope'], $e['old'], $e['new'], $e['user'] ) );
        $this->assertTrue( $e['created_block'] );
        $this->assertSame( 'settings/override/site.ini.append.php', $e['path'] );
        $this->assertNotEmpty( $e['backup'] );
        $this->assertFileExists( expIniEditor::root() . $e['backup'] );
        $this->assertSame( self::$original, file_get_contents( expIniEditor::root() . $e['backup'] ), 'the backup is the file before' );

        // toggle, then undo both, newest first
        $t = $s->write( 'probe_bool', 'toggle', null, 'global' );
        $this->assertSame( 'disabled', $t['setting']['effective'] );
        $this->assertSame( 'enabled', $t['entry']['old'] );

        // the older write cannot be undone while the newer one stands
        try
        {
            $s->undo( $e['id'] );
            $this->fail( 'changed since: refused' );
        }
        catch ( InvalidArgumentException $x )
        {
            $this->assertStringContainsString( 'changed since', $x->getMessage() );
        }
        $u = $s->undo( $t['entry']['id'] );
        $this->assertTrue( $u['byte_identical'] );
        $this->assertSame( $t['entry']['id'], $u['entry']['undoes'] );
        $u2 = $s->undo( $e['id'] );
        $this->assertTrue( $u2['byte_identical'] );
        $this->assertNull( $u2['setting']['effective'] );
        $this->assertRestored();

        // undone entries are marked and cannot be undone again
        $log = new expDebugBarLog( self::$logPath );
        $this->assertSame( $u2['entry']['id'], $log->find( $e['id'] )['undone_by'] );
        $this->expectException( InvalidArgumentException::class );
        $s->undo( $e['id'] );
    }

    public function testNothingToChangeAndDryRun()
    {
        $s = $this->service();
        $dry = $s->write( 'probe_text', 'set', 'hello', 'global', array( 'dry_run' => true ) );
        $this->assertTrue( $dry['dry_run'] );
        $this->assertStringContainsString( '+ProbeText=hello', $dry['diff'] );
        $this->assertNull( $dry['entry'] );
        $this->assertRestored();

        $unset = $s->write( 'probe_text', 'unset', null, 'global' );
        $this->assertFalse( $unset['changed'] );
        $this->assertNull( $unset['entry'] );
    }

    public function testTextEnumAndValidation()
    {
        $s = $this->service();
        $a = $s->write( 'probe_text', 'set', 'first value', 'global' );
        $b = $s->write( 'probe_text', 'set', 'second', 'global' );
        $this->assertSame( 'first value', $b['entry']['old'] );
        $c = $s->write( 'probe_enum', 'set', 'popup', 'global' );
        foreach ( array( array( 'probe_enum', 'set', 'sideways' ), array( 'probe_bool', 'set', 'maybe' ), array( 'probe_text', 'set', "two\nlines" ),
                         array( 'probe_text', 'add', 'x' ), array( 'probe_ips', 'toggle', null ), array( 'probe_text', 'frobnicate', 'x' ) ) as $bad )
        {
            try
            {
                $s->write( $bad[0], $bad[1], $bad[2], 'global' );
                $this->fail( 'refused: ' . json_encode( $bad ) );
            }
            catch ( InvalidArgumentException | expIniException $x )
            {
                $this->assertNotSame( '', $x->getMessage() );
            }
        }
        foreach ( array( $c, $b, $a ) as $r )
            $s->undo( $r['entry']['id'] );
        $this->assertRestored();
    }

    public function testDefaultScopeIsRefused()
    {
        $this->expectException( InvalidArgumentException::class );
        $this->service()->write( 'probe_bool', 'set', 'on', 'default' );
    }

    public function testIPListAddRemoveReplaceAndUndo()
    {
        $s = $this->service();
        $s->now = mktime( 12, 0, 0, 10, 2, 2026 );
        $a = $s->write( 'probe_ips', 'add', json_encode( array( 'address' => '203.0.113.0/24', 'label' => 'Office', 'expires' => '+1h' ) ), 'global' );
        $this->assertSame( '203.0.113.0/24 ; Office ; expires=2026-10-02T13:00', $a['entry']['value'] );
        $b = $s->write( 'probe_ips', 'add', '2001:db8::/32', 'global' );
        $this->assertSame( array( '203.0.113.0/24 ; Office ; expires=2026-10-02T13:00', '2001:db8::/32' ), $b['setting']['effective'] );
        $this->assertCount( 2, $b['setting']['entries'] );
        $this->assertSame( 'Office', $b['setting']['entries'][0]['label'] );
        try
        {
            $s->write( 'probe_ips', 'add', '10.0.0.0/33', 'global' );
            $this->fail( 'bad CIDR refused' );
        }
        catch ( InvalidArgumentException $x )
        {
            $this->assertStringContainsString( '/32', $x->getMessage() );
        }
        // remove by address finds the line with its label
        $c = $s->write( 'probe_ips', 'remove', '203.0.113.0/24', 'global' );
        $this->assertSame( '203.0.113.0/24 ; Office ; expires=2026-10-02T13:00', $c['entry']['value'] );
        $this->assertSame( array( '2001:db8::/32' ), $c['setting']['effective'] );
        $d = $s->write( 'probe_ips', 'replace', json_encode( array( '198.51.100.7', '2001:db8::1 ; me' ) ), 'global' );
        $this->assertSame( array( '198.51.100.7', '2001:db8::1 ; me' ), $d['setting']['effective'] );
        $this->assertStringContainsString( 'ProbeIPList[]' . "\n", $d['diff'] . "\n" );

        foreach ( array( $d, $c, $b, $a ) as $r )
            $s->undo( $r['entry']['id'] );
        $this->assertRestored();
    }

    public function testUserListAndPlainList()
    {
        $s = $this->service();
        $adminID = (string)eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' );
        $a = $s->write( 'probe_users', 'add', $adminID, 'global' );
        $this->assertSame( array(), $a['warnings'] === array() ? array() : array_filter( $a['warnings'], function ( $w ) { return strpos( $w, 'no user' ) !== false; } ) );
        $b = $s->write( 'probe_users', 'add', '999999999', 'global' );
        $this->assertNotEmpty( array_filter( $b['warnings'], function ( $w ) { return strpos( $w, 'no user' ) !== false; } ) );
        $c = $s->write( 'probe_levels', 'add', 'warning', 'global' );
        try
        {
            $s->write( 'probe_users', 'add', 'admin', 'global' );
            $this->fail( 'a login is no user id' );
        }
        catch ( InvalidArgumentException $x )
        {
        }
        try
        {
            $s->write( 'probe_levels', 'add', 'loud', 'global' );
            $this->fail( 'not an allowed value' );
        }
        catch ( InvalidArgumentException $x )
        {
        }
        foreach ( array( $c, $b, $a ) as $r )
            $s->undo( $r['entry']['id'] );
        $this->assertRestored();
    }

    public function testUnsetAndUndoBringsTheValueBack()
    {
        $s = $this->service();
        $a = $s->write( 'probe_text', 'set', 'keep me', 'global' );
        $b = $s->write( 'probe_text', 'unset', null, 'global' );
        $this->assertSame( 'keep me', $b['entry']['old'] );
        $this->assertNull( $b['setting']['effective'] );
        $u = $s->undo( $b['entry']['id'] );
        $this->assertSame( 'keep me', $u['setting']['effective'] );
        $s->undo( $a['entry']['id'] );
        $this->assertRestored();
    }

    public function testSiteAccessScopeWrite()
    {
        $s = $this->service();
        $path = expIniEditor::root() . 'settings/siteaccess/admin/site.ini.append.php';
        $before = is_file( $path ) ? file_get_contents( $path ) : null;
        if ( $before === null )
            $this->markTestSkipped( 'settings/siteaccess/admin/site.ini.append.php does not exist' );
        $r = $s->write( 'probe_bool', 'set', 'on', 'siteaccess:admin' );
        $this->assertSame( 'siteaccess:admin', $r['setting']['origin']['scope'] );
        $this->assertSame( 'enabled', $r['setting']['scopes']['siteaccess:admin'] );
        $u = $s->undo( $r['entry']['id'] );
        $this->assertTrue( $u['byte_identical'] );
        clearstatcache();
        $this->assertSame( $before, file_get_contents( $path ) );
    }

    public function testPresetAppliedAndUndoneAsAGroup()
    {
        $s = $this->service();
        $dry = $s->applyPreset( 'probe_preset', 'global', array( 'dry_run' => true ) );
        $this->assertCount( 3, $dry['plan'] );
        $this->assertRestored();
        $p = $s->applyPreset( 'probe_preset', 'global' );
        $this->assertTrue( $p['ok'] );
        $this->assertCount( 3, $p['entries'] );
        foreach ( $p['entries'] as $e )
            $this->assertSame( $p['group'], $e['group'] );
        $this->assertSame( 'preset', $s->describe( 'probe_text' )['effective'] );
        // applied again: everything is already in effect
        $again = $s->applyPreset( 'probe_preset', 'global' );
        $this->assertCount( 0, $again['entries'] );
        $this->assertCount( 3, $again['skipped'] );
        $u = $s->undoGroup( $p['group'] );
        $this->assertTrue( $u['ok'], json_encode( $u['errors'] ) );
        $this->assertCount( 3, $u['entries'] );
        $this->assertRestored();
    }

    // ------------------------------------------------------------------ lock-out

    public function testLockoutAndOpenChecks()
    {
        $s = $this->service();
        $s->clientIP = '203.0.113.9';
        $s->userID = 14;
        $def = $s->definition( 'debug_ip_list' );
        $after = function ( array $debug ) {
            return array( 'DebugSettings' => $debug + array( 'DebugByIP' => 'disabled', 'DebugIPList' => array(), 'DebugByUser' => 'disabled',
                                                             'DebugUserIDList' => array(), 'DebugOutput' => 'enabled' ) );
        };
        $this->assertNull( $s->check( $def, $after( array() ) ), 'debug by IP off: no lock-out' );
        $this->assertNull( $s->check( $def, $after( array( 'DebugByIP' => 'enabled', 'DebugIPList' => array( '203.0.113.0/24 ; office' ) ) ) ) );
        $lock = $s->check( $def, $after( array( 'DebugByIP' => 'enabled', 'DebugIPList' => array( '198.51.100.0/24' ) ) ) );
        $this->assertSame( 'lockout', $lock['reason'] );
        $this->assertStringContainsString( '203.0.113.9', $lock['message'] );
        $expired = $s->check( $def, $after( array( 'DebugByIP' => 'enabled', 'DebugIPList' => array( '203.0.113.9 ; expires=2020-01-01T00:00' ) ) ) );
        $this->assertSame( 'lockout', $expired['reason'], 'an expired entry does not count' );
        $open = $s->check( $def, $after( array( 'DebugByIP' => 'enabled', 'DebugIPList' => array( '0.0.0.0/0' ) ) ) );
        $this->assertSame( 'open', $open['reason'] );
        $user = $s->check( $s->definition( 'debug_by_user' ), $after( array( 'DebugByUser' => 'enabled', 'DebugUserIDList' => array( '10' ) ) ) );
        $this->assertSame( 'lockout', $user['reason'] );
        $this->assertNull( $s->check( $s->definition( 'debug_by_user' ), $after( array( 'DebugByUser' => 'enabled', 'DebugUserIDList' => array( '14' ) ) ) ) );
        $this->assertNull( $s->check( $s->definition( 'probe_bool' ), $after( array( 'DebugByIP' => 'enabled' ) ) ), 'other settings are not checked' );
    }

    public function testLockoutRefusesTheWriteUnlessConfirmed()
    {
        $s = $this->service();
        $s->clientIP = '203.0.113.9';
        // DebugByIP is disabled on this installation and its list is empty: switching it on would lock this address out
        if ( expDebugBarRegistry::isOn( (string)$s->effective( 'debug_by_ip' ) ) )
            $this->markTestSkipped( 'DebugByIP is enabled here' );
        $r = $s->write( 'debug_by_ip', 'set', 'on', 'global', array( 'dry_run' => true ) );
        $this->assertFalse( $r['ok'] );
        $this->assertSame( 'lockout', $r['needs_confirm']['reason'] );
        $this->assertNull( $r['entry'] );
        $confirmed = $s->write( 'debug_by_ip', 'set', 'on', 'global', array( 'dry_run' => true, 'confirm' => true ) );
        $this->assertTrue( $confirmed['ok'] );
        $this->assertTrue( $confirmed['dry_run'] );
        $this->assertRestored();
    }

    // ------------------------------------------------------------------ eZDebug's IP check

    public function testEZDebugIPCheckUsesTheEngine()
    {
        $check = new ReflectionMethod( 'eZDebug', 'isAllowedByCurrentIP' );
        $check->setAccessible( true );
        $saved = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
        $savedMatch = isset( $GLOBALS['eZDebugIPMatch'] ) ? $GLOBALS['eZDebugIPMatch'] : null;
        try
        {
            $_SERVER['REMOTE_ADDR'] = '203.0.113.9';
            $this->assertTrue( $check->invoke( null, array( '203.0.113.9' ) ), 'a plain line' );
            $this->assertTrue( $check->invoke( null, array( '10.0.0.1', '203.0.113.0/24 ; office ; expires=2099-01-01T00:00' ) ) );
            $this->assertSame( 1, $GLOBALS['eZDebugIPMatch']['index'] );
            $this->assertFalse( $check->invoke( null, array( '203.0.113.0/24 ; office ; expires=2020-01-01T00:00' ) ), 'expired' );
            $this->assertFalse( $check->invoke( null, array( '198.51.100.0/24' ) ) );
            $this->assertFalse( $check->invoke( null, 'not an array' ) );
            $_SERVER['REMOTE_ADDR'] = '2001:db8::5';
            $this->assertTrue( $check->invoke( null, array( '2001:db8::/64 ; vpn' ) ) );
            $this->assertFalse( $check->invoke( null, array( '2001:db8::6' ) ), 'a plain IPv6 address is that address only' );

            // the fallback for a process that has no expDebugBarIPList: the address part, expired entries left out
            $fallback = new ReflectionMethod( 'eZDebug', 'ipListEntryAddress' );
            $fallback->setAccessible( true );
            $this->assertSame( '10.0.0.0/8', $fallback->invoke( null, '10.0.0.0/8' ) );
            $this->assertSame( '10.0.0.0/8', $fallback->invoke( null, '10.0.0.0/8 ; LAN ; expires=2099-01-01T00:00' ) );
            $this->assertSame( '', $fallback->invoke( null, '10.0.0.0/8 ; LAN ; expires=2020-01-01T00:00' ) );
            $this->assertSame( '', $fallback->invoke( null, '10.0.0.0/8 ; expires=whenever' ) );
        }
        finally
        {
            if ( $saved === null )
                unset( $_SERVER['REMOTE_ADDR'] );
            else
                $_SERVER['REMOTE_ADDR'] = $saved;
            $GLOBALS['eZDebugIPMatch'] = $savedMatch;
        }
    }

    // ------------------------------------------------------------------ summary

    public function testSummary()
    {
        eZDebug::accumulatorStart( 'expdebugbartest_query', 'expdebugbartest_total', 'Test queries' );
        eZDebug::accumulatorStop( 'expdebugbartest_query' );
        $sum = expDebugBarSummary::collect( null, array( 'time_ms' => array( 1, 2 ) ) );
        foreach ( array( 'time', 'sql', 'memory', 'templates', 'messages', 'included_files', 'accumulators', 'timing_points', 'debug', 'engine', 'thresholds' ) as $key )
            $this->assertArrayHasKey( $key, $sum );
        $this->assertSame( 'cli', $sum['engine'] );
        $this->assertGreaterThan( 0, $sum['memory']['peak_bytes'] );
        $this->assertContains( $sum['time']['level'], array( 'ok', 'warn', 'high' ) );
        $this->assertSame( 'ok', expDebugBarSummary::level( 5, array( 10, 20 ) ) );
        $this->assertSame( 'warn', expDebugBarSummary::level( 10, array( 10, 20 ) ) );
        $this->assertSame( 'high', expDebugBarSummary::level( 25, array( 10, 20 ) ) );
        $json = expDebugBarSummary::json();
        $this->assertStringNotContainsString( '</', $json );
        $this->assertIsArray( json_decode( $json, true ) );
    }

    // ------------------------------------------------------------------ the ezjscore functions

    public function testServerFunctionIsRegistered()
    {
        $ini = eZINI::instance( 'ezjscore.ini' );
        $this->assertSame( 'expDebugBarServerFunctions', $ini->variable( 'ezjscServer_expdebugbar', 'Class' ) );
        $router = ezjscServerRouter::getInstance( array( 'expdebugbar', 'iptest' ) );
        $this->assertInstanceOf( 'ezjscServerRouter', $router );
    }

    public function testSettingsAnswer()
    {
        expDebugBarServerFunctions::$serviceFactory = function ( $sa ) { return $this->service( $sa ); };
        $a = expDebugBarServerFunctions::settings( array( 'admin' ) );
        foreach ( array( 'siteaccess', 'siteaccesses', 'can_write', 'can_cache', 'token', 'scopes', 'default_scope', 'groups', 'settings', 'presets', 'ip', 'log' ) as $key )
            $this->assertArrayHasKey( $key, $a );
        $this->assertTrue( $a['can_write'] );
        $this->assertSame( 'admin', $a['user']['login'] );
        $this->assertNotEmpty( array_filter( $a['settings'], function ( $s ) { return $s['id'] === 'debug_ip_list'; } ) );
        $this->assertArrayHasKey( 'suggest', $a['ip'] );
        $this->assertNotFalse( json_encode( $a ) );
    }

    public function testVisitorWithoutPolicyGetsNoConfigurationAtAll()
    {
        expDebugBarServerFunctions::$serviceFactory = function ( $sa ) { return $this->service( $sa ); };
        $anon = eZUser::fetch( eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) );
        eZUser::setCurrentlyLoggedInUser( $anon, $anon->attribute( 'contentobject_id' ) );
        $savedEnabled = isset( $GLOBALS['eZDebugEnabled'] ) ? $GLOBALS['eZDebugEnabled'] : null;
        $savedMatch = isset( $GLOBALS['eZDebugIPMatch'] ) ? $GLOBALS['eZDebugIPMatch'] : null;
        try
        {
            // with and without debug output for this request: settings, log, iptest and the cache list are refused
            foreach ( array( false, true ) as $debugOutput )
            {
                $GLOBALS['eZDebugEnabled'] = $debugOutput;
                foreach ( array( 'settings' => 'setup/setup', 'log' => 'setup/setup', 'iptest' => 'setup/setup', 'cache' => 'setup/managecache' ) as $fn => $policy )
                {
                    try
                    {
                        $r = expDebugBarServerFunctions::$fn( array() );
                        $this->fail( "$fn must be refused without $policy (debug output " . var_export( $debugOutput, true ) . '): ' . substr( json_encode( $r ), 0, 120 ) );
                    }
                    catch ( InvalidArgumentException $e )
                    {
                        $this->assertStringContainsString( $policy, $e->getMessage(), $fn );
                    }
                }
            }
            $this->assertFalse( expDebugBarServerFunctions::canWrite() );
            $this->assertFalse( expDebugBarServerFunctions::canCache() );

            // the summary stays: the report itself, without the line of the list that matched
            $GLOBALS['eZDebugEnabled'] = true;
            $GLOBALS['eZDebugIPMatch'] = array( 'index' => 0, 'line' => '203.0.113.7/32 ; secret label', 'address' => '203.0.113.7/32', 'label' => 'secret label' );
            $s = expDebugBarServerFunctions::summary( array() );
            $this->assertTrue( $s['debug']['ip_match'] );
            $this->assertStringNotContainsString( 'secret label', json_encode( $s ) );
            $this->assertStringNotContainsString( '203.0.113.7', json_encode( $s ) );
            $GLOBALS['eZDebugEnabled'] = false;
            try
            {
                expDebugBarServerFunctions::summary( array() );
                $this->fail( 'no debug output and no policy: refused' );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertStringContainsString( 'setup/setup', $e->getMessage() );
            }
        }
        finally
        {
            $GLOBALS['eZDebugEnabled'] = $savedEnabled;
            if ( $savedMatch === null )
                unset( $GLOBALS['eZDebugIPMatch'] );
            else
                $GLOBALS['eZDebugIPMatch'] = $savedMatch;
        }
    }

    public function testSetupPolicyAloneOpensSettingsLogAndIPTestButNotTheCacheList()
    {
        // admin has both policies; a fake user with managecache only is not available without creating one, so the
        // rule is checked through the guards: setup/setup decides settings, log and iptest
        expDebugBarServerFunctions::requireSetup();
        expDebugBarServerFunctions::requireCache();
        $this->assertTrue( expDebugBarServerFunctions::canWrite() );
        $this->assertTrue( expDebugBarServerFunctions::canCache() );
        $source = file_get_contents( __DIR__ . '/../../../../../kernel/classes/debugbar/expdebugbarserverfunctions.php' );
        foreach ( array( 'settings', 'log', 'iptest' ) as $fn )
            $this->assertMatchesRegularExpression( '/function ' . $fn . '\( \$args \)\s*\{\s*self::requireSetup\(\);/', $source, $fn );
        $this->assertMatchesRegularExpression( '/self::requireCache\(\);\s*return self::cacheList\(\)/', $source );
    }

    public function testReportCarriesNoSettingsForAVisitor()
    {
        $report = file_get_contents( __DIR__ . '/../../../../../lib/ezutils/classes/expdebugbarreport.php' );
        // the classic toolbar (values of the quick settings) and the cache tab's content only behind the rights
        $this->assertMatchesRegularExpression( '/if \( !\$userInfo\[\'can_setup\'\] \)\s*\{[^}]*Sign in with setup access[^}]*\}\s*else if \( \$classicToolbar/s', $report );
        $this->assertMatchesRegularExpression( '/if \( !\$user\[\'can_cache\'\] \)\s*return \'<p[^;]*Sign in with cache access/s', $report );
        $js = file_get_contents( __DIR__ . '/../../../../../design/standard/javascript/expdebugbar.js' );
        $this->assertStringContainsString( '!userRights.can_setup', $js );
        $this->assertStringContainsString( '!userRights.can_cache', $js );
        foreach ( array( 'Sign in with setup access to change debug settings', 'Sign in with cache access to manage caches' ) as $note )
            $this->assertContains( $note, expDebugBarReport::scriptStrings() );
    }

    public function testWritesNeedPostTokenAndPolicy()
    {
        expDebugBarServerFunctions::$serviceFactory = function ( $sa ) { return $this->service( $sa ); };
        $savedMethod = isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : null;
        $savedPost = $_POST;
        try
        {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            try
            {
                expDebugBarServerFunctions::set( array() );
                $this->fail( 'GET refused' );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertStringContainsString( 'POST', $e->getMessage() );
            }
            $_SERVER['REQUEST_METHOD'] = 'POST';
            if ( expDebugBarServerFunctions::token() !== null )
            {
                $_POST = array( 'setting' => 'probe_bool', 'op' => 'set', 'value' => 'on', 'scope' => 'global', 'ezxform_token' => 'wrong' );
                try
                {
                    expDebugBarServerFunctions::set( array() );
                    $this->fail( 'wrong token refused' );
                }
                catch ( InvalidArgumentException $e )
                {
                    $this->assertStringContainsString( 'token', $e->getMessage() );
                }
            }
            // anonymous: no policy
            $anon = eZUser::fetch( eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) );
            eZUser::setCurrentlyLoggedInUser( $anon, $anon->attribute( 'contentobject_id' ) );
            try
            {
                expDebugBarServerFunctions::cache( array( 'clear' ) );
                $this->fail( 'anonymous cannot clear caches' );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertStringContainsString( 'setup/managecache', $e->getMessage() );
            }
            $this->assertFalse( expDebugBarServerFunctions::canWrite() );
        }
        finally
        {
            $_POST = $savedPost;
            if ( $savedMethod === null )
                unset( $_SERVER['REQUEST_METHOD'] );
            else
                $_SERVER['REQUEST_METHOD'] = $savedMethod;
        }
    }

    public function testSetAndUndoThroughTheFunctions()
    {
        expDebugBarServerFunctions::$serviceFactory = function ( $sa ) { return $this->service( $sa ); };
        expDebugBarServerFunctions::$trustRequest = true;
        $savedPost = $_POST;
        try
        {
            $_POST = array( 'setting' => 'probe_bool', 'op' => 'set', 'value' => '1', 'scope' => 'global' );
            $r = expDebugBarServerFunctions::set( array() );
            $this->assertTrue( $r['ok'] );
            $this->assertSame( 'enabled', $r['setting']['effective'] );
            $_POST = array( 'entry' => $r['entry']['id'] );
            $u = expDebugBarServerFunctions::undo( array() );
            $this->assertTrue( $u['byte_identical'] );
            $_POST = array( 'setting' => 'probe_bool', 'op' => 'set', 'value' => 'on', 'scope' => 'default' );
            $this->expectException( InvalidArgumentException::class );
            expDebugBarServerFunctions::set( array() );
        }
        finally
        {
            $_POST = $savedPost;
            $this->assertRestored();
        }
    }

    public function testIPTestAndCacheList()
    {
        $_GET['address'] = '2001:db8::1';
        $_GET['list'] = json_encode( array( '2001:db8::/48 ; lab', '0.0.0.0/0' ) );
        try
        {
            $a = expDebugBarServerFunctions::iptest( array() );
        }
        finally
        {
            unset( $_GET['address'], $_GET['list'] );
        }
        $this->assertTrue( $a['valid'] );
        $this->assertSame( 6, $a['family'] );
        $this->assertTrue( $a['allowed'] );
        $this->assertSame( 'lab', $a['matched']['label'] );
        $this->assertSame( '2001:db8::/64', $a['suggest']['network'] );

        $c = expDebugBarServerFunctions::cacheList();
        $this->assertNotEmpty( $c['caches'] );
        $this->assertContains( 'content', array_column( $c['caches'], 'id' ) );
        $this->assertNotEmpty( $c['tags'] );
        $this->assertArrayHasKey( 'opcache', $c );
    }
}
