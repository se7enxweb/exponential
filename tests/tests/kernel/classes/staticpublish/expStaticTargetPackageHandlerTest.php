<?php
/**
 * Static publishing targets in packages (expStaticTargetPackageHandler, install item type expstatictarget), without a
 * database and without the installation's settings: a stand-in package and a scratch settings folder under var/tmp.
 *
 *  SP-01 what travels: the wizard's settings only, the credential as its name (a value pasted in becomes none), no
 *        user or password in the git address, no webhook
 *  SP-02 the element in the package and back: values, lists, an empty list; something else is no target
 *  SP-03 adding: one install item per target in statictargets/, its content the element
 *  SP-04 installing: a target the installation has is kept, a new one is written through expIniEditor with the
 *        credential's name and never a value, a failed write is reported
 *  SP-05 package.ini names the handler
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expStaticTargetPackageHandlerTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    const SECRET = 'ghp_Th1sIsAT0kenValue0123456789abcdefABCD';

    protected function setUp(): void
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/static-package-test-' . getmypid();
        if ( !is_dir( $this->dir . '/settings/override' ) )
            mkdir( $this->dir . '/settings/override', 0775, true );
    }

    protected function tearDown(): void
    {
        eZDir::recursiveDelete( $this->dir );
    }

    private static function settings( array $more = array() )
    {
        return $more + array( 'Name' => 'Public site', 'SiteAccess' => 'site', 'Kind' => 'github-pages', 'Remote' => 'https://github.com/octo-org/demo-site.git',
                              'Branch' => 'gh-pages', 'Delivery' => 'branch', 'Credential' => 'token:pages_token', 'Extras' => array( 'nojekyll', '404' ),
                              'ExcludePaths' => array(), 'Webhook' => 'https://hooks.example.com/services/T000/B000/XXXX', 'License' => 'CC-BY-4.0' );
    }

    /** SP-01 */
    public function testWhatTravels()
    {
        $s = expStaticTargetPackageHandler::travelSettings( self::settings( array( 'Remote' => 'https://user:' . self::SECRET . '@github.com/octo-org/demo-site.git',
                                                                                    'Secret' => self::SECRET ) ) );
        $this->assertSame( 'token:pages_token', $s['Credential'] );
        $this->assertSame( 'https://github.com/octo-org/demo-site.git', $s['Remote'], 'no user or password in the address' );
        $this->assertSame( '', $s['Webhook'] );
        $this->assertArrayNotHasKey( 'Secret', $s, 'only the wizard\'s settings' );
        $this->assertSame( array( 'nojekyll', '404' ), $s['Extras'] );
        $this->assertStringNotContainsString( self::SECRET, serialize( $s ) );

        $this->assertSame( 'token:env:GH_TOKEN', expStaticTargetPackageHandler::credentialName( 'token:env:GH_TOKEN' ) );
        $this->assertSame( 'sshkey:staticpublish.gh.deploy_key', expStaticTargetPackageHandler::credentialName( 'sshkey:staticpublish.gh.deploy_key' ) );
        $this->assertSame( 'app:4711', expStaticTargetPackageHandler::credentialName( 'app:4711' ) );
        $this->assertSame( 'none', expStaticTargetPackageHandler::credentialName( 'token:' . self::SECRET ), 'a token value is no name' );
        $this->assertSame( 'none', expStaticTargetPackageHandler::credentialName( 'token:github_pat_11ABCDEFG0123456789_abcdef' ) );
        $this->assertSame( 'none', expStaticTargetPackageHandler::credentialName( 'token:' . str_repeat( 'a1', 20 ) ) );
        $this->assertSame( 'none', expStaticTargetPackageHandler::credentialName( 'app:abc' ) );
        $this->assertSame( 'none', expStaticTargetPackageHandler::credentialName( 'password:x' ) );
        $this->assertSame( 'none', expStaticTargetPackageHandler::credentialName( 'token:a b' ) );
    }

    /** SP-02 */
    public function testTheElementAndBack()
    {
        $element = expStaticTargetPackageHandler::targetElement( 'github-public', self::settings() );
        $xml = $element->ownerDocument->saveXML();
        $this->assertStringNotContainsString( 'hooks.example.com', $xml );
        $dom = new DOMDocument();
        $dom->loadXML( $xml );
        $read = expStaticTargetPackageHandler::readElement( $dom->documentElement );
        $this->assertSame( 'github-public', $read['id'] );
        $this->assertSame( expStaticTargetPackageHandler::travelSettings( self::settings() ), $read['settings'] );
        $this->assertSame( array(), $read['settings']['ExcludePaths'] );

        $other = new DOMDocument();
        $other->loadXML( '<content-class id="x"/>' );
        $this->assertNull( expStaticTargetPackageHandler::readElement( $other->documentElement ) );
        $other->loadXML( '<static-target id="../etc"/>' );
        $this->assertNull( expStaticTargetPackageHandler::readElement( $other->documentElement ), 'an id that is no target id' );
        $this->assertNull( expStaticTargetPackageHandler::readElement( false ) );
    }

    /** SP-03 */
    public function testAdding()
    {
        $package = new class {
            public $items = array();
            public function appendInstall( $type, $name, $os = false, $isInstall = true, $filename = false, $subdirectory = false, $parameters = false )
            {
                $this->items[] = compact( 'type', 'name', 'isInstall', 'filename', 'subdirectory', 'parameters' );
            }
        };
        expStaticTargetPackageHandler::addTarget( $package, new expStaticTarget( 'github-public', self::settings() ) );
        $this->assertCount( 1, $package->items );
        $item = $package->items[0];
        $this->assertSame( array( 'expstatictarget', 'github-public', true, 'target-github-public', 'statictargets' ),
                           array( $item['type'], $item['name'], $item['isInstall'], $item['filename'], $item['subdirectory'] ) );
        $this->assertInstanceOf( DOMElement::class, $item['parameters']['content'] );
        $this->assertSame( 'github-public', $item['parameters']['content']->getAttribute( 'id' ) );
    }

    /** SP-04 */
    public function testInstalling()
    {
        $kept = expStaticTargetPackageHandler::installTarget( 'github-public', self::settings(), array( 'github-public' ), function () { $this->fail( 'nothing is written' ); } );
        $this->assertSame( 'kept', $kept['state'] );

        $scope = new expIniScope( 'global', expIniScope::KIND_GLOBAL, 'settings/override', $this->dir . '/', 'Global override (settings/override)' );
        $written = array();
        $save = function ( $id, array $settings ) use ( $scope, &$written )
        {
            $editor = expStaticAdmin::editor( $id, $settings, false, $scope );
            $editor->save( array( 'create' => true ) );
            $written[] = $editor->content();
            return '';
        };
        $result = expStaticTargetPackageHandler::installTarget( 'github-public', self::settings( array( 'Credential' => 'token:' . self::SECRET ) ), array(), $save );
        $this->assertSame( 'installed', $result['state'] );
        $this->assertCount( 1, $written );
        $this->assertStringContainsString( "[Target_github-public]\nName=Public site\n", $written[0] );
        $this->assertStringContainsString( "Credential=none\n", $written[0] );
        $this->assertStringContainsString( "TargetList[]=github-public\n", $written[0] );
        $this->assertStringNotContainsString( 'Enabled=enabled', $written[0], 'static publishing is not switched on' );
        $this->assertStringNotContainsString( self::SECRET, $written[0] );
        $this->assertStringNotContainsString( 'hooks.example.com', $written[0] );

        $named = expStaticTargetPackageHandler::installTarget( 'other', self::settings(), array(), function () { return ''; } );
        $this->assertStringContainsString( 'token:pages_token', $named['message'], 'the receiving installation is told which secret to store' );
        $failed = expStaticTargetPackageHandler::installTarget( 'other', self::settings(), array(), function () { return 'not writable'; } );
        $this->assertSame( 'failed', $failed['state'] );
        $this->assertStringContainsString( 'not writable', $failed['message'] );
    }

    /** SP-05 */
    public function testPackageIniNamesTheHandler()
    {
        $ini = (string)file_get_contents( dirname( __DIR__, 5 ) . '/settings/package.ini' );
        $this->assertStringContainsString( "HandlerAlias[expstatictarget]=expStaticTargetPackageHandler\n", $ini );
        $handler = new expStaticTargetPackageHandler();
        $this->assertSame( 'expstatictarget', $handler->handlerType() );
        $this->assertTrue( $handler->extractInstallContent() );
    }
}
