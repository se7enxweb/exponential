<?php
/**
 * The setup wizard on an installed site: a GET of setup/init (a crawler of a signed-in administrator) started the
 * wizard and put the whole site into maintenance. Now an installed site refuses the wizard with an explanation page,
 * maintenance starts only from a posted step (fresh installation) or a confirmed POST (installed site, development
 * switch on), and the wizard's lease runs out by itself. Without a database; files under
 * var/tmp/setup-wizard-guard-tests/ only.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class SetupWizardGuardTestHttp
{
    public $post = array();
    public $session = array();
    function hasPostVariable( $name ) { return array_key_exists( $name, $this->post ); }
    function postVariable( $name ) { return $this->post[$name]; }
    function hasSessionVariable( $name ) { return array_key_exists( $name, $this->session ); }
    function sessionVariable( $name ) { return $this->session[$name]; }
    function setSessionVariable( $name, $value ) { $this->session[$name] = $value; }
    function removeSessionVariable( $name ) { unset( $this->session[$name] ); }
}

class SetupWizardGuardTest extends PHPUnit\Framework\TestCase
{
    private $root;
    private $method;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 5 ) . '/var/tmp/setup-wizard-guard-tests/' . getmypid() . '-' . $this->name();
        if ( !is_dir( $this->root . '/var' ) )
            mkdir( $this->root . '/var', 0775, true );
        @unlink( $this->root . '/' . expMaintenance::MARKER );
        @unlink( $this->root . '/' . expSetupWizardGuard::INSTALLED_MARKER );
        $this->method = $_SERVER['REQUEST_METHOD'] ?? null;
    }

    protected function tearDown(): void
    {
        @unlink( $this->root . '/' . expMaintenance::MARKER );
        @unlink( $this->root . '/' . expSetupWizardGuard::INSTALLED_MARKER );
        @rmdir( $this->root . '/var' );
        @rmdir( $this->root );
        if ( $this->method === null )
            unset( $_SERVER['REQUEST_METHOD'] );
        else
            $_SERVER['REQUEST_METHOD'] = $this->method;
    }

    public function testAnInstalledSiteRefusesTheWizardWhateverIsPosted()
    {
        foreach ( array( false, true ) as $isStepPost )
            foreach ( array( false, true ) as $confirmed )
                foreach ( array( false, true ) as $lease )
                    $this->assertSame( expSetupWizardGuard::REFUSE,
                                       expSetupWizardGuard::decide( true, false, $isStepPost, $confirmed, $lease ) );
    }

    public function testAGetNeverStartsMaintenance()
    {
        // fresh installation: the first page is shown, maintenance waits for the first posted step
        $this->assertSame( expSetupWizardGuard::SHOW, expSetupWizardGuard::decide( false, false, false, false, false ) );
        // installed site with the development switch: a confirm page first
        $this->assertSame( expSetupWizardGuard::CONFIRM, expSetupWizardGuard::decide( true, true, false, false, false ) );
    }

    public function testMaintenanceStartsOnlyFromAPostedStepOrAConfirmedPost()
    {
        $this->assertSame( expSetupWizardGuard::BEGIN, expSetupWizardGuard::decide( false, false, true, false, false ) );
        $this->assertSame( expSetupWizardGuard::CONTINUE_RUN, expSetupWizardGuard::decide( false, false, true, false, true ) );
        $this->assertSame( expSetupWizardGuard::BEGIN, expSetupWizardGuard::decide( true, true, false, true, false ) );
        $this->assertSame( expSetupWizardGuard::CONTINUE_RUN, expSetupWizardGuard::decide( true, true, true, false, true ) );
        // a step of an installed site whose lease ran out asks again rather than starting maintenance
        $this->assertSame( expSetupWizardGuard::CONFIRM, expSetupWizardGuard::decide( true, true, true, false, false ) );
    }

    public function testInstalledIsTheMarkerOrANormallyServedSiteOrADatabaseWithContent()
    {
        $never = function () { throw new RuntimeException( 'not asked' ); };
        // served normally (not routed by CheckValidity): the site was set up
        $this->assertTrue( expSetupWizardGuard::isInstalled( $this->root, false, $never ) );
        $this->assertTrue( expSetupWizardGuard::isInstalled( $this->root, true, function () { return true; } ) );
        $this->assertFalse( expSetupWizardGuard::isInstalled( $this->root, true, function () { return false; } ) );
        // a database that cannot be reached is a fresh installation
        $this->assertFalse( expSetupWizardGuard::isInstalled( $this->root, true, $never ) );
        $this->assertTrue( expSetupWizardGuard::markInstalled( $this->root, 'run-1' ) );
        $this->assertTrue( expSetupWizardGuard::isInstalled( $this->root, true, $never ) );
    }

    public function testTheConfirmTokenIsPostedOnceAndOnlyWithPost()
    {
        $http = new SetupWizardGuardTestHttp();
        $token = expSetupWizardGuard::issueConfirmToken( $http );
        $http->post = array( expSetupWizardGuard::CONFIRM_BUTTON => '1', expSetupWizardGuard::CONFIRM_TOKEN => $token );

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertFalse( expSetupWizardGuard::confirmedByPost( $http ), 'a GET never confirms' );

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $http->post[expSetupWizardGuard::CONFIRM_TOKEN] = 'wrong';
        $this->assertFalse( expSetupWizardGuard::confirmedByPost( $http ) );
        $this->assertFalse( $http->hasSessionVariable( expSetupWizardGuard::CONFIRM_SESSION_KEY ), 'a wrong token uses it up' );

        $token = expSetupWizardGuard::issueConfirmToken( $http );
        $http->post[expSetupWizardGuard::CONFIRM_TOKEN] = $token;
        $this->assertTrue( expSetupWizardGuard::confirmedByPost( $http ) );
        $this->assertFalse( expSetupWizardGuard::confirmedByPost( $http ), 'the token is used once' );
    }

    public function testTheSwitchIsOffByDefault()
    {
        $ini = file_get_contents( dirname( __DIR__, 5 ) . '/settings/site.ini' );
        $this->assertMatchesRegularExpression( '/^\[SetupSettings\][^\[]*^AllowWizardOnInstalledSite=disabled$/m', $ini );
    }

    public function testTheViewStartsMaintenanceOnlyInTheBeginBranchAndExplainsAnInstalledSite()
    {
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/private/classes/views/setup/ezsetup.php' );
        $this->assertSame( 1, substr_count( $source, 'expMaintenance::beginWizard(' ), 'one place starts maintenance' );
        $begin = strpos( $source, 'expMaintenance::beginWizard(' );
        // the nearest decision branch before it is the BEGIN branch
        $branch = strrpos( substr( $source, 0, $begin ), '$decision === \\expSetupWizardGuard::' );
        $this->assertNotFalse( $branch );
        $this->assertStringStartsWith( '$decision === \\expSetupWizardGuard::BEGIN', substr( $source, $branch ) );
        // the refusal returns before the setup log or maintenance are touched
        $refuse = strpos( $source, "'design:setup/init/installed.tpl'" );
        $this->assertNotFalse( $refuse );
        $this->assertLessThan( strpos( $source, '\\expSetupLog::' ), $refuse );
        $this->assertLessThan( strpos( $source, '\\expMaintenance::' ), $refuse );
        // the form action of a step outside the validity check names the view (was an undefined variable)
        $this->assertMatchesRegularExpression( '/\$partName = \'init\';/', $source );

        $tpl = file_get_contents( dirname( __DIR__, 5 ) . '/design/standard/templates/setup/init/installed.tpl' );
        $this->assertStringContainsString( 'This site is already installed', $tpl );
        $this->assertStringContainsString( 'kickstarter', $tpl );
        $this->assertStringNotContainsString( 'style=', $tpl );
    }

    public function testTheWizardLeaseExpiresByItselfAndIsShort()
    {
        $this->assertLessThanOrEqual( 900, expMaintenance::WIZARD_LEASE );
        $handlerBefore = set_exception_handler( null );
        restore_exception_handler();
        $this->assertTrue( expMaintenance::beginWizard( $this->root, 'run-9' ) );
        // the settings layer installs eZExecution's exception handler once, on first use; not this test's to keep
        $handlerAfter = set_exception_handler( null );
        restore_exception_handler();
        if ( $handlerAfter !== $handlerBefore )
            restore_exception_handler();
        $this->assertIsArray( expMaintenance::state( $this->root ) );
        $this->assertTrue( expMaintenance::renewWizard( $this->root, 'run-9' ) );
        $this->assertFalse( expMaintenance::renewWizard( $this->root, 'another-run' ), 'another run does not renew it' );

        $marker = $this->root . '/' . expMaintenance::MARKER;
        $state = json_decode( file_get_contents( $marker ), true );
        $state['lease'] = time() - 1;
        file_put_contents( $marker, json_encode( $state ) );
        $this->assertFalse( expMaintenance::state( $this->root ), 'an abandoned wizard no longer holds the site' );
        $this->assertFalse( expMaintenance::renewWizard( $this->root, 'run-9' ), 'an expired lease is not revived' );
    }
}
