<?php
/**
 * The kernel service container (expKernelServices, expKernelServiceContainer, expKernelServiceScope,
 * expKernelServiceCompiler) and its hooks in eZINI and eZExtension. No database; compiled files are written below
 * var/tmp of the installation root and removed again.
 *
 *  KS-01 - kernelservices.ini blocks become definitions: class, factory, arguments, scope (default request), lazy,
 *          tags, description; Enabled=false drops a service; [Tags] TagList is read
 *  KS-02 - validate() reports an unknown scope, a missing class or factory, an argument naming no service, a cycle, a
 *          worker-scoped service taking a request-scoped one, an ini.<name> service that is not eZINI and an
 *          undeclared tag; the shipped settings/kernelservices.ini has no error
 *  KS-03 - Scopes: worker-scoped services survive resetRequest() (per siteaccess), request-scoped ones do not,
 *          prototypes are new each time
 *  KS-04 - Arguments: @id, %ini:file/Section/Variable%, @@ and %% escapes, literals; a cycle and an unknown id throw;
 *          ini.<name> is eZINI::instance()
 *  KS-05 - resetRequest() runs the kernel's seven resets first, in the constructor's old order, then the registered
 *          hooks; registering a name twice keeps one hook
 *  KS-06 - eZINI's worker store: attached, instance() stores a copy and hands out copies; a change of the global
 *          override directories, resetAllInstances() and resetInstance() detach or drop; flushSharedStore() empties
 *  KS-07 - Compiled files: written with their stamp as file time, read back; another generation, another format or
 *          a file time that is not the stamp is not read
 *  KS-08 - applyExtensionStage() applies a recording only when the override directories and the settings it read
 *          are exactly the recorded ones; otherwise it says no and the pass runs live
 *  KS-09 - Velocity keeps the worker store's global; the web kernel calls the request scope instead of the seven
 *          hand-written resets, boots the container before reading settings and enters the siteaccess after the
 *          INI reset; the deploy's INI step reaches the compile through the "kernelservices" cache item
 *  KS-10 - Without a booted container, eZExtension::activateExtensions() does not ask it (nothing changes)
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expKernelServicesTest extends PHPUnit\Framework\TestCase
{
    /** @var array the global override directories before the test */
    private $dirs;

    /** @var string|null */
    private $tmp;

    protected function setUp(): void
    {
        eZINI::instance();
        $this->dirs = eZINI::globalOverrideDirs( false );
        expKernelServices::reset( true );
        expKernelServiceTestFixture::$made = 0;
        expKernelServiceTestFixture::$calls = array();
    }

    protected function tearDown(): void
    {
        expKernelServices::reset( true );
        eZINI::instance()->setOverrideDirs( $this->dirs );
        eZINI::resetAllInstances( false );
        if ( $this->tmp !== null )
        {
            foreach ( glob( $this->tmp . '/*' ) ?: array() as $file )
                unlink( $file );
            rmdir( $this->tmp );
        }
    }

    /**
     * An eZINI with the given blocks and nothing read from disk.
     */
    private function ini( array $blocks )
    {
        $ini = new eZINI( 'kernelservices.ini', 'settings', null, false, null, false, false, false );
        $ini->BlockValues = $blocks;
        return $ini;
    }

    private function read( array $blocks )
    {
        return expKernelServiceCompiler::definitionsFromINI( $this->ini( $blocks ) );
    }

    private function messages( array $problems )
    {
        return implode( "\n", array_map( function ( $p ) { return $p[0] . ' ' . $p[1] . ': ' . $p[2]; }, $problems ) );
    }

    /** KS-01 */
    public function testDefinitionsFromINI()
    {
        $read = $this->read( array(
            'Service_one' => array( 'Class' => 'expKernelServiceTestFixture', 'Arguments' => array( '', 'a', '@two' ),
                                    'Tags' => array( 'x', 'y', 'x' ), 'Description' => ' One ', 'Lazy' => 'false' ),
            'Service_two' => array( 'Factory' => 'expKernelServiceTestFixture::make', 'Scope' => 'Worker' ),
            'Service_off' => array( 'Class' => 'stdClass', 'Enabled' => 'false' ),
            'Other' => array( 'Class' => 'stdClass' ),
            'Tags' => array( 'TagList' => array( 'x', 'y' ) ),
        ) );
        $this->assertSame( array( 'one', 'two' ), array_keys( $read['definitions'] ) );
        $one = $read['definitions']['one'];
        $this->assertSame( 'expKernelServiceTestFixture', $one['class'] );
        $this->assertSame( array( 'a', '@two' ), $one['arguments'], 'the empty array reset is not an argument' );
        $this->assertSame( 'request', $one['scope'], 'request is the default scope' );
        $this->assertFalse( $one['lazy'] );
        $this->assertSame( array( 'x', 'y' ), $one['tags'] );
        $this->assertSame( 'One', $one['description'] );
        $this->assertSame( 'worker', $read['definitions']['two']['scope'] );
        $this->assertTrue( $read['definitions']['two']['lazy'] );
        $this->assertSame( array( 'x' => array( 'one' ), 'y' => array( 'one' ) ), $read['tags'] );
        $this->assertSame( array( 'x', 'y' ), $read['declared_tags'] );
    }

    /** KS-02 */
    public function testValidate()
    {
        $problems = expKernelServiceCompiler::validate( $this->read( array(
            'Service_badscope' => array( 'Class' => 'stdClass', 'Scope' => 'forever' ),
            'Service_noclass' => array( 'Class' => 'expNoSuchClassAnywhere' ),
            'Service_nothing' => array( 'Description' => 'neither' ),
            'Service_badfactory' => array( 'Factory' => 'expKernelServiceTestFixture::nope' ),
            'Service_ref' => array( 'Class' => 'stdClass', 'Arguments' => array( '@missing' ) ),
            'Service_a' => array( 'Class' => 'stdClass', 'Arguments' => array( '@b' ) ),
            'Service_b' => array( 'Class' => 'stdClass', 'Arguments' => array( '@a' ) ),
            'Service_keeper' => array( 'Class' => 'stdClass', 'Scope' => 'worker', 'Arguments' => array( '@perrequest' ) ),
            'Service_perrequest' => array( 'Class' => 'stdClass' ),
            'Service_ini.wrong' => array( 'Class' => 'stdClass' ),
            'Service_tagged' => array( 'Class' => 'stdClass', 'Tags' => array( 'unknown.tag' ) ),
            'Tags' => array( 'TagList' => array( 'kernel.core' ) ),
        ) ) );
        $text = $this->messages( $problems );
        $this->assertStringContainsString( "error badscope: unknown Scope 'forever'", $text );
        $this->assertStringContainsString( 'error noclass: Class expNoSuchClassAnywhere not found', $text );
        $this->assertStringContainsString( 'error nothing: neither Class nor Factory', $text );
        $this->assertStringContainsString( 'error badfactory: Factory expKernelServiceTestFixture::nope does not exist', $text );
        $this->assertStringContainsString( 'error ref: argument @missing names no service', $text );
        $this->assertMatchesRegularExpression( '/error (a|b): depends on itself: (a -> b -> a|b -> a -> b)/', $text );
        $this->assertStringContainsString( 'error keeper: worker-scoped, but takes @perrequest, which is request-scoped', $text );
        $this->assertStringContainsString( 'error ini.wrong: an ini.<name> service is always eZINI', $text );
        $this->assertStringContainsString( "warning tagged: tag 'unknown.tag' is not in [Tags] TagList", $text );

        $shipped = expKernelServiceCompiler::definitionsFromINI(
            new eZINI( 'kernelservices.ini', 'settings', null, false, null, true ) );
        $this->assertArrayHasKey( 'db', $shipped['definitions'] );
        $this->assertArrayHasKey( 'extensions.overrides', $shipped['definitions'] );
        $errors = array_filter( expKernelServiceCompiler::validate( $shipped ), function ( $p ) { return $p[0] === 'error'; } );
        $this->assertSame( '', $this->messages( $errors ), 'settings/kernelservices.ini as shipped' );
    }

    private function container( array $blocks, $siteAccess = 'site' )
    {
        $read = $this->read( $blocks );
        return new expKernelServiceContainer( $read['definitions'], $read['tags'], $siteAccess, false );
    }

    /** KS-03 */
    public function testScopes()
    {
        $blocks = array(
            'Service_w' => array( 'Class' => 'expKernelServiceTestFixture', 'Scope' => 'worker' ),
            'Service_r' => array( 'Class' => 'expKernelServiceTestFixture', 'Scope' => 'request' ),
            'Service_p' => array( 'Class' => 'expKernelServiceTestFixture', 'Scope' => 'prototype' ),
        );
        $c = $this->container( $blocks );
        $w = $c->get( 'w' );
        $r = $c->get( 'r' );
        $this->assertSame( $w, $c->get( 'w' ) );
        $this->assertSame( $r, $c->get( 'r' ) );
        $this->assertNotSame( $c->get( 'p' ), $c->get( 'p' ) );

        expKernelServiceScope::resetRequest();
        $this->assertSame( $w, $c->get( 'w' ), 'a worker-scoped service outlives the request' );
        $this->assertNotSame( $r, $c->get( 'r' ), 'a request-scoped one is made again' );

        $other = $this->container( $blocks, 'admin' );
        $this->assertNotSame( $w, $other->get( 'w' ), 'worker scope is kept per siteaccess' );

        expKernelServiceScope::worker()->sync( 'g2' );
        $this->assertNotSame( $w, $c->get( 'w' ), 'another generation empties the worker store' );
    }

    /** KS-04 */
    public function testArgumentsAndErrors()
    {
        $c = $this->container( array(
            'Service_dep' => array( 'Class' => 'expKernelServiceTestFixture', 'Scope' => 'worker' ),
            'Service_main' => array( 'Factory' => 'expKernelServiceTestFixture::make',
                                     'Arguments' => array( '@dep', '%ini:site.ini/ExtensionSettings/ExtensionDirectory%',
                                                           '%ini:site.ini/NoSuchBlock/NoSuch%', '@@at', '%%pct', 'plain' ) ),
            'Service_loop1' => array( 'Class' => 'expKernelServiceTestFixture', 'Arguments' => array( '@loop2' ) ),
            'Service_loop2' => array( 'Class' => 'expKernelServiceTestFixture', 'Arguments' => array( '@loop1' ) ),
        ) );
        $main = $c->get( 'main' );
        $this->assertSame( $c->get( 'dep' ), $main->args[0] );
        $this->assertSame( eZINI::instance()->variable( 'ExtensionSettings', 'ExtensionDirectory' ), $main->args[1] );
        $this->assertNull( $main->args[2] );
        $this->assertSame( array( '@at', '%pct', 'plain' ), array_slice( $main->args, 3 ) );

        $this->assertTrue( $c->has( 'ini.site' ) );
        $this->assertSame( eZINI::instance( 'site.ini' ), $c->get( 'ini.site' ) );
        $this->assertSame( 'i18n.ini', expKernelServiceContainer::iniFileName( 'ini.i18n' ) );
        $this->assertNull( expKernelServiceContainer::iniFileName( 'db' ) );

        try
        {
            $c->get( 'loop1' );
            $this->fail( 'a cycle must throw' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'depends on itself', $e->getMessage() );
        }
        $this->expectException( InvalidArgumentException::class );
        $c->get( 'nothing.here' );
    }

    /** KS-05 */
    public function testRequestResetHooks()
    {
        $this->assertSame( array( 'querycache', 'sqlprofile', 'csp', 'audit', 'renderstats', 'requestlog', 'classifier', 'userapproval' ),
                           array_keys( expKernelServiceScope::DEFAULT_RESET_HOOKS ) );
        $this->assertSame( array( 'eZDBQueryCache', 'resetRequest' ), expKernelServiceScope::DEFAULT_RESET_HOOKS['querycache'] );
        $this->assertSame( array( 'expRequestClassifier', 'reset' ), expKernelServiceScope::DEFAULT_RESET_HOOKS['classifier'] );

        expKernelServiceScope::onRequestReset( 'test', array( 'expKernelServiceTestFixture', 'hook' ) );
        expKernelServiceScope::onRequestReset( 'test', array( 'expKernelServiceTestFixture', 'hook' ) );
        $this->assertSame( array_merge( array_keys( expKernelServiceScope::DEFAULT_RESET_HOOKS ), array( 'test' ) ),
                           expKernelServiceScope::resetHookNames() );
        expKernelServiceScope::resetRequest();
        $this->assertSame( array( 'hook' ), expKernelServiceTestFixture::$calls, 'once, though registered twice' );
        expKernelServiceScope::removeRequestReset( 'test' );
        expKernelServiceScope::resetRequest();
        $this->assertSame( array( 'hook' ), expKernelServiceTestFixture::$calls );
    }

    /** KS-06 */
    public function testINIWorkerStore()
    {
        $store = expKernelServiceScope::worker();
        eZINI::resetAllInstances( false );
        eZINI::attachSharedStore( $store, 'test|1' );
        $this->assertSame( 'test|1', eZINI::sharedStoreState() );

        $site = eZINI::instance( 'site.ini' );
        $this->assertArrayHasKey( 'settings-site.ini-', $store->ini['test|1'] );
        $this->assertNotSame( $site, $store->ini['test|1']['settings-site.ini-'], 'the store keeps its own copy' );

        // a request changes its instance; the next request gets the pristine copy
        $site->setVariable( 'ExtensionSettings', 'ExtensionDirectory', 'changed-by-a-request' );
        eZINI::resetAllInstances( false );
        $this->assertNull( eZINI::sharedStoreState(), 'resetAllInstances() detaches' );
        eZINI::attachSharedStore( $store, 'test|1' );
        $again = eZINI::instance( 'site.ini' );
        $this->assertNotSame( $site, $again );
        $this->assertNotSame( 'changed-by-a-request', $again->variable( 'ExtensionSettings', 'ExtensionDirectory' ) );

        eZINI::resetInstance( 'site.ini' );
        $this->assertArrayNotHasKey( 'settings-site.ini-', $store->ini['test|1'], 'resetInstance() drops the stored copy' );

        eZINI::instance( 'site.ini' )->prependOverrideDir( 'extension/nosuch/settings', true, 'extension:nosuch', 'extension' );
        $this->assertNull( eZINI::sharedStoreState(), 'a change of the global override directories detaches' );

        eZINI::attachSharedStore( $store, 'test|1' );
        eZINI::instance( 'i18n.ini' );
        $this->assertNotEmpty( $store->ini );
        eZINI::flushSharedStore();
        $this->assertSame( array(), $store->ini );
        $this->assertNull( eZINI::sharedStoreState() );

        // a local-override instance changes only itself
        eZINI::attachSharedStore( $store, 'test|1' );
        $local = new eZINI( 'site.ini', 'settings', null, null, true );
        $local->prependOverrideDir( 'extension/nosuch2/settings', true, 'extension:nosuch2', 'extension' );
        $this->assertSame( 'test|1', eZINI::sharedStoreState() );
    }

    /** KS-07 */
    public function testCompiledFiles()
    {
        $this->tmp = expKernelServices::installationRoot() . '/var/tmp/kernelservices_test_' . uniqid();
        $file = $this->tmp . '/site.php';
        $data = array( 'format' => expKernelServices::FORMAT, 'generation' => 'g1', 'source' => 'compile', 'siteaccess' => 'site',
                       'x' => array( 1, 2 ) );
        $this->assertTrue( expKernelServiceCompiler::writeCompiledFile( $file, $data ) );
        $read = expKernelServices::readCompiled( $file, 'g1' );
        $this->assertSame( array( 1, 2 ), $read['x'] );
        $this->assertSame( filemtime( $file ), $read['stamp'] );
        $this->assertNotNull( expKernelServices::readCompiled( $file, null ) );
        $this->assertNull( expKernelServices::readCompiled( $file, 'g2' ), 'another generation' );
        $this->assertNull( expKernelServices::readCompiled( $this->tmp . '/missing.php', null ) );

        touch( $file, $read['stamp'] - 10 );
        $this->assertNull( expKernelServices::readCompiled( $file, 'g1' ), 'a file whose time is not its stamp is not the one written' );

        $this->assertTrue( expKernelServiceCompiler::writeCompiledFile( $file, array( 'format' => 99 ) + $data ) );
        $this->assertNull( expKernelServices::readCompiled( $file, 'g1' ), 'another format' );

        $this->assertTrue( expKernelServices::validSiteAccessName( 'admin_ger' ) );
        $this->assertFalse( expKernelServices::validSiteAccessName( '../x' ) );
        $this->assertFalse( expKernelServices::validSiteAccessName( 'index' ) );
    }

    private function boot( $index = null )
    {
        $set = function ( $name, $value ) {
            $p = new ReflectionProperty( 'expKernelServices', $name );
            $p->setValue( null, $value );
        };
        $set( 'booted', true );
        $set( 'index', $index );
    }

    /** KS-08 */
    public function testApplyExtensionStageOnlyOnTheRecordedState()
    {
        unset( $GLOBALS['eZCurrentAccess'], $GLOBALS['eZActiveExtensions'] );
        $ini = eZINI::instance();
        $before = eZINI::globalOverrideDirs( false );
        $after = $before;
        $after['extension'] = array( 'extension:recorded' => array( 'extension/recorded/settings', true ) ) + $after['extension'];
        $recording = array( 'before' => $before, 'fingerprint' => expKernelServices::fingerprint( 'default', $ini ), 'after' => $after );
        $index = array( 'generation' => 'g1', 'default' => $recording,
                        'extensions' => array( 'paths' => array( 'recorded' => 'extension/recorded' ), 'names' => array(), 'active' => array() ) );

        // another starting state: not applied
        $other = $recording;
        $other['fingerprint']['settings']['ActiveExtensions'] = array( 'something-else' );
        $this->boot( array( 'default' => $other ) + $index );
        $this->assertFalse( expKernelServices::applyExtensionStage( 'default' ) );
        $this->assertSame( $before, eZINI::globalOverrideDirs( false ), 'nothing changed' );

        $other = $recording;
        $other['before']['override'][] = array( 'elsewhere', false );
        $this->boot( array( 'default' => $other ) + $index );
        $this->assertFalse( expKernelServices::applyExtensionStage( 'default' ) );

        // the recorded state: applied
        $this->boot( $index );
        $this->assertTrue( expKernelServices::applyExtensionStage( 'default' ) );
        $this->assertSame( $after, eZINI::globalOverrideDirs( false ) );
        $caches = eZExtension::exportCaches();
        $this->assertSame( 'extension/recorded', $caches['paths']['recorded'] );
        $status = expKernelServices::status();
        $this->assertSame( array( 'default' => true ), $status['applied'] );
    }

    /** KS-09 */
    public function testKernelWiring()
    {
        $this->assertContains( expKernelServiceScope::WORKER_GLOBAL, expVelocity::DEFAULT_KEEP_GLOBALS );

        $kernel = file_get_contents( expKernelServices::installationRoot() . '/kernel/private/classes/ezpkernelweb.php' );
        $start = strpos( $kernel, 'public function __construct' );
        $ctor = substr( $kernel, $start, strpos( $kernel, 'private function setupFatalErrorHandler' ) - $start );
        $this->assertStringContainsString( 'expKernelServiceScope::resetRequest();', $ctor );
        foreach ( array( 'eZDBQueryCache::resetRequest()', 'expAudit::resetRequest()', 'expRequestLog::reset()' ) as $call )
            $this->assertStringNotContainsString( $call, $ctor, 'the resets are request scope hooks now' );
        $boot = strpos( $ctor, 'expKernelServices::bootWeb(' );
        $this->assertNotFalse( $boot );
        $this->assertLessThan( strpos( $ctor, "eZINI::instance( 'i18n.ini' )" ), $boot, 'booted before any setting is read' );
        $reset = strpos( $ctor, 'eZINI::resetAllInstances( false );' );
        $enter = strpos( $ctor, 'expKernelServices::enterSiteAccess();' );
        $this->assertGreaterThan( $reset, $enter, 'the siteaccess is entered after the INI reset' );

        $ezcache = file_get_contents( expKernelServices::installationRoot() . '/kernel/classes/ezcache.php' );
        $this->assertMatchesRegularExpression( "/'id' => 'kernelservices',\s*'tag' => array\( 'ini' \)/", $ezcache );
        $this->assertStringContainsString( "array( 'expKernelServiceCompiler', 'clearCacheItem' )", $ezcache );
    }

    /** KS-10 */
    public function testNoContainerNoChange()
    {
        $this->assertFalse( expKernelServices::isBooted() );
        $extension = file_get_contents( expKernelServices::installationRoot() . '/lib/ezutils/classes/ezextension.php' );
        $this->assertStringContainsString( "class_exists( 'expKernelServices', false ) && expKernelServices::isBooted()", $extension,
                                           'lib asks only a container that is loaded and booted' );
        $before = eZINI::globalOverrideDirs( false );
        $this->assertFalse( expKernelServices::bootWeb( array( 'injected-settings' => array( 'site.ini/A/B' => 'c' ) ) ),
                            'injected settings keep the container out' );
        $this->assertFalse( expKernelServices::isBooted() );
        $this->assertSame( $before, eZINI::globalOverrideDirs( false ) );
    }
}

/**
 * A service class for the tests.
 */
class expKernelServiceTestFixture
{
    public static $made = 0;
    public static $calls = array();
    public $args;

    public function __construct( ...$args )
    {
        self::$made++;
        $this->args = $args;
    }

    public static function make( ...$args )
    {
        return new self( ...$args );
    }

    public static function hook()
    {
        self::$calls[] = 'hook';
    }
}
