<?php
/**
 * Extending exp:ini: an action and a scope provider registered by an extension's ini.ini.append.php, the
 * registry, and the dispatcher run in-process. Guide doc/bc/6.0/console-exp-ini.md.
 *
 *  IE-01 — settings/ini.ini (read by eZINI) registers every built-in action, the remove alias and the two
 *          built-in scope providers, and every registration works
 *  IE-02 — An extension's ini.ini.append.php (the fixture, read by eZINI) adds an action and an alias and
 *          switches a built-in off; the built-ins stay
 *  IE-03 — The extension's action runs through the dispatcher, by its name and its alias; the switched-off one
 *          is an unknown action (1)
 *  IE-04 — A scope provider named in the root's ini.ini adds a scope that is listed and written like the others
 *  IE-05 — A registration that cannot work (missing class, class not implementing expIniAction) is reported
 *          by problems() and the actions action, and running it is a usage error (1)
 *  IE-06 — The context: arguments, the setting syntaxes, options and their defaults, an injected editor
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group ini
 */

require_once __DIR__ . '/fixtures/iniroot.php';
require_once __DIR__ . '/fixtures/extension/iniactionfixture/classes/iniactionfixture.php';

class iniActionFixtureNotAnAction
{
}

class IniCommandExtensionTest extends PHPUnit\Framework\TestCase
{
    const FIXTURE = '/fixtures/extension/iniactionfixture/settings/ini.ini.append.php';

    private $root;
    private $out;
    private $err;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 6 ) );
        $this->root = dirname( __DIR__, 6 ) . '/var/tmp/ini/b/extension-' . getmypid() . '-' . substr( md5( $this->name() ), 0, 8 );
        iniCommandTestRoot::build( $this->root );
        $this->out = array();
        $this->err = array();
    }

    protected function tearDown(): void
    {
        expIniEditor::setRoot( null );
        if ( is_dir( $this->root ) )
            iniCommandTestRoot::remove( $this->root );
    }

    /** The fixture extension's registry: the built-ins with the fixture's append laid over them, read by eZINI. */
    private function fixtureRegistry()
    {
        return expIniActionRegistry::fromSettings( eZINI::fetchFromFile( 'tests/tests/kernel/classes/ini/command' . self::FIXTURE ) );
    }

    private function dispatch( array $positional, array $options = array(), $registry = null )
    {
        $self = $this;
        return expIniCommand::dispatch( $positional,
                                        array_merge( expIniCommandContext::defaults(), array( 'root' => $this->root ), $options ),
                                        false,
                                        function ( $l ) use ( $self ) { $self->out[] = $l; },
                                        function ( $l ) use ( $self ) { $self->err[] = $l; },
                                        $registry !== null ? $registry : $this->fixtureRegistry() );
    }

    /** IE-01 */
    public function testKernelSettingsRegisterTheBuiltIns()
    {
        $ini = eZINI::fetchFromFile( 'settings/ini.ini' );
        $registry = expIniActionRegistry::fromSettings( $ini );
        $this->assertSame( expIniActionRegistry::BUILT_IN, $registry->actions(), 'settings/ini.ini lists exactly the built-ins, in order' );
        $this->assertSame( array( 'remove' => 'rem' ), $registry->aliases() );
        $this->assertSame( array( 'expIniCoreScopeProvider', 'expIniExtensionScopeProvider' ), $registry->scopeProviders() );
        $this->assertSame( array(), $registry->problems() );
        foreach ( $registry->names() as $name )
        {
            $action = $registry->create( $name );
            $this->assertSame( $name, $action->name() );
            $this->assertNotSame( '', $action->description() );
            $this->assertNotSame( '', $action->usage() );
            $this->assertTrue( $registry->isBuiltIn( $name ) );
        }
    }

    /** IE-02 */
    public function testExtensionAppendAddsAndRemoves()
    {
        $registry = $this->fixtureRegistry();
        $this->assertSame( 'iniActionFixtureCount', $registry->actions()['count'] );
        $this->assertSame( 'count', $registry->resolve( 'n' ) );
        $this->assertFalse( $registry->has( 'copy' ), 'Actions[copy]= switches it off' );
        foreach ( array( 'get', 'set', 'add', 'rem', 'remove', 'clear', 'toggle', 'move', 'move-all', 'where', 'list', 'scopes', 'actions' ) as $name )
            $this->assertTrue( $registry->has( $name ), $name );
        $this->assertFalse( $registry->isBuiltIn( 'count' ) );
        $this->assertContains( 'iniActionFixtureScopeProvider', $registry->scopeProviders() );
    }

    /** IE-03 */
    public function testExtensionActionRuns()
    {
        $this->assertSame( 0, $this->dispatch( array( 'count', 'site.ini', 'global' ) ), implode( "\n", $this->err ) );
        $this->assertSame( array( '4' ), $this->out, 'four blocks in the override' );

        $this->out = array();
        $this->assertSame( 0, $this->dispatch( array( 'n', 'site.ini/DebugSettings', 'global' ) ) );
        $this->assertSame( array( '3' ), $this->out, 'by the alias' );

        $this->out = array();
        $this->assertSame( 0, $this->dispatch( array( 'count', 'site.ini', 'global' ), array( 'json' => true ) ) );
        $data = json_decode( implode( "\n", $this->out ), true );
        $this->assertSame( 4, $data['data']['count'] );
        $this->assertSame( 'count', $data['action'] );

        $this->assertSame( 1, $this->dispatch( array( 'copy', 'site.ini/SiteSettings/SiteName', 'global', 'admin' ) ) );
        $this->assertStringContainsString( 'unknown action "copy"', implode( "\n", $this->err ) );

        $this->out = array();
        $this->assertSame( 0, $this->dispatch( array( 'actions' ) ) );
        $this->assertStringContainsString( 'count      Count the blocks', implode( "\n", $this->out ) );
        $this->assertStringContainsString( '[iniActionFixtureCount]', implode( "\n", $this->out ), 'a registered action names its class' );
    }

    /** IE-04 */
    public function testExtensionScopeProvider()
    {
        file_put_contents( $this->root . '/settings/ini.ini',
                           "#?ini charset=\"utf-8\"?\n[IniCommandSettings]\nScopeProviders[]\nScopeProviders[]=iniActionFixtureScopeProvider\n" );
        mkdir( $this->root . '/settings/shared' );

        $this->assertSame( 0, $this->dispatch( array( 'scopes' ), array( 'json' => true ) ) );
        $data = json_decode( implode( "\n", $this->out ), true );
        $this->assertContains( 'shared', array_column( $data['data']['scopes'], 'name' ) );
        $this->assertContains( 'global', array_column( $data['data']['scopes'], 'name' ), 'the built-in providers stay' );

        $this->out = array();
        $this->assertSame( 0, $this->dispatch( array( 'set', 'site.ini/SiteSettings/SiteName', 'Cluster', 'shared' ) ), implode( "\n", $this->err ) );
        $this->assertStringContainsString( "[SiteSettings]\nSiteName=Cluster",
                                           file_get_contents( $this->root . '/settings/shared/site.ini.append.php' ) );
    }

    /** IE-05 */
    public function testBrokenRegistrations()
    {
        $registry = new expIniActionRegistry( array( 'get' => 'expIniActionGet', 'gone' => 'iniActionFixtureNoSuchClass',
                                                     'odd' => 'iniActionFixtureNotAnAction', 'actions' => 'expIniActionActions' ),
                                              array(), array( 'iniActionFixtureNoSuchProvider' ) );
        $problems = $registry->problems();
        $this->assertCount( 3, $problems );
        $this->assertSame( array( 'gone', 'odd', 'iniActionFixtureNoSuchProvider' ), array_column( $problems, 'name' ) );

        $this->assertSame( 1, $this->dispatch( array( 'gone' ), array(), $registry ) );
        $this->assertStringContainsString( 'does not exist', implode( "\n", $this->err ) );
        $this->assertSame( 1, $this->dispatch( array( 'odd' ), array(), $registry ) );
        $this->assertStringContainsString( 'does not implement expIniAction', implode( "\n", $this->err ) );

        $this->assertSame( 0, $this->dispatch( array( 'actions' ), array( 'json' => true ), $registry ) );
        $data = json_decode( implode( "\n", $this->out ), true );
        $this->assertCount( 3, $data['data']['problems'] );
        $this->assertFalse( $data['data']['actions'][1]['ok'] );
    }

    /** IE-06 */
    public function testContext()
    {
        $c = new expIniCommandContext( 'set', array( 'site.ini', '[SiteSettings]', 'SiteName', 'v', 'global' ), array(),
                                       $this->fixtureRegistry() );
        $s = $c->setting();
        $this->assertSame( array( 'site', 'SiteSettings', 'SiteName', 'plain' ), array( $s['file'], $s['block'], $s['variable'], $s['kind'] ) );
        $this->assertSame( 'v', $c->shift( 'value' ) );
        $this->assertSame( 'global', $c->shift( 'scope' ) );
        $this->assertNull( $c->shift() );
        $c->noMoreArguments();

        $c = new expIniCommandContext( 'list', array( 'site.ini', '[SiteSettings]' ), array(), $this->fixtureRegistry() );
        $this->assertSame( array( 'file' => 'site', 'block' => 'SiteSettings' ), $c->fileAndBlock() );

        $this->assertSame( 'site.ini/A/B[]', expIniCommandContext::settingText( array( 'file' => 'site', 'block' => 'A', 'variable' => 'B', 'kind' => 'array', 'key' => null ) ) );
        $this->assertSame( 'site.ini/A/B[k]', expIniCommandContext::settingText( array( 'file' => 'site', 'block' => 'A', 'variable' => 'B', 'kind' => 'hash', 'key' => 'k' ) ) );

        $c = new expIniCommandContext( 'set', array(), array( 'create' => false ), $this->fixtureRegistry() );
        $this->assertFalse( $c->mayCreate( true ) );
        $this->assertTrue( $c->option( 'backup' ) );
        $this->assertTrue( $c->option( 'clear-cache' ) );
        $this->assertSame( "+Password=********\n Name=x", $c->maskText( "+Password=abc\n Name=x" ) );
        $this->assertSame( '********', $c->display( 'Password', 'abc' ) );
        $this->assertSame( 'abc', ( new expIniCommandContext( 'get', array(), array( 'show-secrets' => true ), $this->fixtureRegistry() ) )->display( 'Password', 'abc' ) );

        try
        {
            $c->shift( 'scope' );
            $this->fail( 'a missing argument is a usage error' );
        }
        catch ( expIniException $e )
        {
            $this->assertSame( expIniException::USAGE, $e->getCode() );
        }

        // an injected editor: the actions never make one themselves
        $made = array();
        $c = new expIniCommandContext( 'get', array( 'site.ini/SiteSettings/SiteName', 'global' ), array(), $this->fixtureRegistry() );
        expIniEditor::setRoot( $this->root );
        $c->setEditorFactory( function ( $scope, $file ) use ( &$made ) {
            $made[] = $scope->name() . ':' . $file;
            return new expIniEditor( $scope, $file );
        } );
        $lines = array();
        $c->setWriter( function ( $l ) use ( &$lines ) { $lines[] = $l; } );
        $this->assertSame( 0, ( new expIniActionGet() )->run( $c ) );
        $this->assertSame( array( 'global:site' ), $made );
        $this->assertSame( array( 'Override site' ), $lines );
    }
}
