<?php
/**
 * The setup wizard on an installed site: a GET of setup/init (a crawler of a signed-in administrator) started the
 * wizard and put the whole site into maintenance. Now an installed site refuses the wizard with an explanation page,
 * maintenance starts only from a posted step (fresh installation) or a confirmed POST (installed site, development
 * switch on), and the wizard's lease runs out by itself. Without a database; files under
 * var/tmp/setup-wizard-guard-tests/ only.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class SetupWizardGuardTestHttp
{
    public $post = array();
    function hasPostVariable( $name ) { return array_key_exists( $name, $this->post ); }
    function postVariable( $name ) { return $this->post[$name]; }
}

class SetupWizardGuardTestIni
{
    private $value;
    function __construct( $value ) { $this->value = $value; }
    function hasVariable( $group, $name ) { return $this->value !== null; }
    function variable( $group, $name ) { return $this->value; }
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
        unset( $_COOKIE[expSetupWizardGuard::CONFIRM_COOKIE] );
    }

    protected function tearDown(): void
    {
        @unlink( $this->root . '/' . expMaintenance::MARKER );
        @unlink( $this->root . '/' . expSetupWizardGuard::INSTALLED_MARKER );
        @rmdir( $this->root . '/var' );
        @rmdir( $this->root );
        unset( $_COOKIE[expSetupWizardGuard::CONFIRM_COOKIE] );
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
        // installed site, wizard allowed: the explanation page with its button, then the confirm page
        $this->assertSame( expSetupWizardGuard::OFFER, expSetupWizardGuard::decide( true, true, false, false, false ) );
        $this->assertSame( expSetupWizardGuard::CONFIRM, expSetupWizardGuard::decide( true, true, false, false, false, true ) );
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
        // No session: the wizard runs before the session starts (CheckValidity). The token pairs with a cookie.
        $cookieName = expSetupWizardGuard::CONFIRM_COOKIE;
        $http = new SetupWizardGuardTestHttp();
        $token = expSetupWizardGuard::issueConfirmToken();
        $this->assertMatchesRegularExpression( '/^[0-9]+\.[0-9a-f]{32}$/', $token );
        $this->assertSame( substr( $token, strpos( $token, '.' ) + 1 ), $_COOKIE[$cookieName], 'the browser gets the nonce' );
        $nonce = $_COOKIE[$cookieName];
        $http->post = array( expSetupWizardGuard::CONFIRM_BUTTON => '1', expSetupWizardGuard::CONFIRM_TOKEN => $token );

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertFalse( expSetupWizardGuard::confirmedByPost( $http ), 'a GET never confirms' );

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_COOKIE[$cookieName] = $nonce;
        $http->post[expSetupWizardGuard::CONFIRM_TOKEN] = 'wrong';
        $this->assertFalse( expSetupWizardGuard::confirmedByPost( $http ) );
        $this->assertArrayNotHasKey( $cookieName, $_COOKIE, 'a wrong token uses the cookie up' );

        // without the cookie (another browser, a page of another site): refused
        $http->post[expSetupWizardGuard::CONFIRM_TOKEN] = $token;
        $this->assertFalse( expSetupWizardGuard::confirmedByPost( $http ) );
        // another browser's cookie: refused
        $_COOKIE[$cookieName] = str_repeat( 'a', 32 );
        $this->assertFalse( expSetupWizardGuard::confirmedByPost( $http ) );

        // the page's GET, then its POST with the same browser's cookie: confirmed, once
        $token = expSetupWizardGuard::issueConfirmToken();
        $http->post[expSetupWizardGuard::CONFIRM_TOKEN] = $token;
        $this->assertTrue( expSetupWizardGuard::confirmedByPost( $http ) );
        $this->assertFalse( expSetupWizardGuard::confirmedByPost( $http ), 'the token is used once' );

        // an expired token: refused
        $token = expSetupWizardGuard::issueConfirmToken( time() - expSetupWizardGuard::CONFIRM_LIFETIME - 1 );
        $http->post[expSetupWizardGuard::CONFIRM_TOKEN] = $token;
        $this->assertFalse( expSetupWizardGuard::confirmedByPost( $http ) );
    }

    public function testTheSwitchIsAutoByDefault()
    {
        $ini = file_get_contents( dirname( __DIR__, 5 ) . '/settings/site.ini' );
        // the [SetupSettings] block: up to the next block header (a comment may name another block)
        $this->assertSame( 1, preg_match( '/^\[SetupSettings\]\R(.*?)(?=^\[[A-Za-z]|\z)/ms', $ini, $block ) );
        $this->assertMatchesRegularExpression( '/^AllowWizardOnInstalledSite=auto$/m', $block[1] );
    }

    public function testTheModeIsAutoUnlessEnabledOrDisabled()
    {
        $this->assertSame( 'auto', expSetupWizardGuard::mode( new SetupWizardGuardTestIni( null ) ) );
        $this->assertSame( 'auto', expSetupWizardGuard::mode( new SetupWizardGuardTestIni( 'auto' ) ) );
        $this->assertSame( 'auto', expSetupWizardGuard::mode( new SetupWizardGuardTestIni( 'something' ) ) );
        $this->assertSame( 'enabled', expSetupWizardGuard::mode( new SetupWizardGuardTestIni( ' Enabled ' ) ) );
        $this->assertSame( 'disabled', expSetupWizardGuard::mode( new SetupWizardGuardTestIni( 'disabled' ) ) );
    }

    /**
     * Installed or not x auto/enabled/disabled x CheckValidity: what a first visit (a GET) gets, and
     * what the "Run the setup wizard anyway" button gets.
     */
    public function testTheGuardDecisionMatrix()
    {
        $expected = array(
            // installed, mode, CheckValidity => first visit, the button
            array( true,  'auto',     true,  'offer',  'confirm' ),
            array( true,  'auto',     false, 'refuse', 'refuse' ),
            array( true,  'enabled',  true,  'offer',  'confirm' ),
            array( true,  'enabled',  false, 'offer',  'confirm' ),
            array( true,  'disabled', true,  'refuse', 'refuse' ),
            array( true,  'disabled', false, 'refuse', 'refuse' ),
            array( false, 'auto',     true,  'show',   'show' ),
            array( false, 'auto',     false, 'show',   'show' ),
            array( false, 'enabled',  true,  'show',   'show' ),
            array( false, 'enabled',  false, 'show',   'show' ),
            array( false, 'disabled', true,  'show',   'show' ),
            array( false, 'disabled', false, 'show',   'show' ),
        );
        foreach ( $expected as $row )
        {
            list( $installed, $mode, $validity, $visit, $button ) = $row;
            $allowed = expSetupWizardGuard::allowedOnInstalledSite( new SetupWizardGuardTestIni( $mode ), $validity );
            $label = ( $installed ? 'installed' : 'fresh' ) . ", $mode, CheckValidity=" . ( $validity ? 'true' : 'false' );
            $this->assertSame( $visit, expSetupWizardGuard::decide( $installed, $allowed, false, false, false, false ), $label );
            $this->assertSame( $button, expSetupWizardGuard::decide( $installed, $allowed, false, false, false, true ), $label . ', button' );
        }
    }

    public function testNothingStartsWithoutTheConfirmOnAnInstalledSite()
    {
        // every combination without a valid confirm: never BEGIN, so no run and no maintenance
        foreach ( array( false, true ) as $allowed )
            foreach ( array( false, true ) as $isStepPost )
                foreach ( array( false, true ) as $runAnyway )
                {
                    $decision = expSetupWizardGuard::decide( true, $allowed, $isStepPost, false, false, $runAnyway );
                    $this->assertNotSame( expSetupWizardGuard::BEGIN, $decision );
                    $this->assertNotSame( expSetupWizardGuard::CONTINUE_RUN, $decision );
                }
        // the button is not a confirm: a GET with it, or a POST of it, gives no BEGIN either
        $http = new SetupWizardGuardTestHttp();
        $http->post = array( expSetupWizardGuard::RUN_ANYWAY_BUTTON => '1' );
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertFalse( expSetupWizardGuard::runAnywayPosted( $http ) );
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertTrue( expSetupWizardGuard::runAnywayPosted( $http ) );
        $this->assertFalse( expSetupWizardGuard::confirmedByPost( $http ), 'the button alone does not confirm' );
        // the view returns the explanation page before the setup log or maintenance are touched
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/private/classes/views/setup/ezsetup.php' );
        $offer = strpos( $source, '$decision === \\expSetupWizardGuard::OFFER' );
        $this->assertNotFalse( $offer );
        $this->assertLessThan( strpos( $source, '\\expSetupLog::startWeb(' ), $offer );
        $this->assertLessThan( strpos( $source, '\\expMaintenance::beginWizard(' ), $offer );
    }

    public function testNoCacheKeepsAWizardPage()
    {
        $headers = expSetupWizardGuard::noStoreHeaders();
        $this->assertSame( 'no-store, private', $headers['Cache-Control'] );
        $this->assertStringStartsWith( 'BYPASS', $headers['X-Exp-Cache'] );
        // sent before any page of the wizard: the explanation page, the confirm page and the steps
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/private/classes/views/setup/ezsetup.php' );
        $sent = strpos( $source, '\\expSetupWizardGuard::sendNoStoreHeaders()' );
        $this->assertNotFalse( $sent );
        $this->assertLessThan( strpos( $source, "'design:setup/init/installed.tpl'" ), $sent );
        $this->assertLessThan( strpos( $source, "'design:setup/init/confirm_reinstall.tpl'" ), $sent );
    }

    public function testTheAccountChecksLeaveTheSetupWizardAlone()
    {
        // "Verify it's you" ([ReauthGroup_system] setup/*) refused every POST of the wizard reached through CheckValidity:
        // there is no session and no user there
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/private/classes/ezpkernelweb.php' );
        $this->assertStringContainsString( "\$accountChecks = empty( \$this->siteBasics['validity-check-required'] );", $source );
        foreach ( array( 'expPasswordChangeRequired', 'expPasskeyPolicy', 'expReauth' ) as $class )
            $this->assertStringContainsString( "if ( \$accountChecks && \$moduleResult === null && class_exists( '$class' ) )", $source, $class );
    }

    public function testARejectedSystemCheckNamesTheFailedTestInTheSetupLog()
    {
        // on an installed site a folder owned by another user (settings/ root:root) failed directory_permissions,
        // and the step came back with nothing in var/log/setup.log to say why
        $root = dirname( __DIR__, 5 );
        include_once $root . '/kernel/setup/ezsetuptests.php';
        include_once $root . '/kernel/setup/steps/ezstep_installer.php';
        include_once $root . '/kernel/setup/steps/ezstep_system_check.php';
        $lines = eZStepSystemCheck::failedTests( array(
            array( EZ_SETUP_TEST_SUCCESS, 'phpversion', array( 'result' => true ) ),
            array( 2, 'directory_permissions', array( 'result' => false, 'result_elements' => array(
                array( 'file' => 'design', 'result' => 1 ),
                array( 'file' => 'settings', 'result' => 3 ) ) ) ),
            array( 2, 'memory_limit', array( 'result' => false ) ),
        ) );
        $this->assertSame( array( 'critical test failed: directory_permissions: settings (not writable)',
                                  'critical test failed: memory_limit' ), $lines );
    }

    public function testTheFormsPostToTheSiteRootThroughTheValidityCheck()
    {
        $this->assertSame( '/', expSetupWizardGuard::formAction( true, '', '', '', '' ) );
        $this->assertSame( '/', expSetupWizardGuard::formAction( true, '', '/index.php', '/index.php', '' ) );
        $this->assertSame( '/sub/', expSetupWizardGuard::formAction( true, '/sub', '/index.php', '/sub/index.php', '' ) );
        // served normally (the admin's setup/init): the view's address
        $this->assertSame( '/index.php/setup/init', expSetupWizardGuard::formAction( false, '', '/index.php', '/index.php', '' ) );
    }

    public function testNoWizardFormTargetsASetupAddress()
    {
        // through the validity check every address is the wizard; /setup/... is an admin module a frontend firewall
        // (request-shield EXP-MODULES) refuses, so every form posts to $script and no link names setup/...
        $dir = dirname( __DIR__, 5 ) . '/design/standard/templates/setup/init';
        $files = glob( $dir . '/*.tpl' );
        $this->assertNotEmpty( $files );
        foreach ( $files as $file )
        {
            $tpl = file_get_contents( $file );
            $this->assertDoesNotMatchRegularExpression( '#(action|href)=\{?\s*["\'][^"\']*setup/#i', $tpl, basename( $file ) );
            if ( preg_match_all( '#<form\b[^>]*>#i', $tpl, $forms ) )
                foreach ( $forms[0] as $form )
                    $this->assertMatchesRegularExpression( '#action="\{\$script(\|wash)?\}"#', $form, basename( $file ) . ': ' . $form );
        }
        $page = file_get_contents( dirname( __DIR__, 5 ) . '/design/standard/templates/setup_pagelayout.tpl' );
        $this->assertDoesNotMatchRegularExpression( '#(action|href)=\{?\s*["\'][^"\']*setup/#i', $page );
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
        // one form action for every page of the wizard
        $this->assertStringContainsString( 'expSetupWizardGuard::formAction(', $source );

        $tpl = file_get_contents( dirname( __DIR__, 5 ) . '/design/standard/templates/setup/init/installed.tpl' );
        $this->assertStringContainsString( 'This site is already installed', $tpl );
        $this->assertStringContainsString( 'kickstarter', $tpl );
        $this->assertStringContainsString( 'Run the setup wizard anyway', $tpl );
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
