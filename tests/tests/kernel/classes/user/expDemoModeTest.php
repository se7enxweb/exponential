<?php
/**
 * Demo mode (kernel/classes/expdemomode.php, doc/bc/6.0/demo-mode.md): the environment variable EXP_DEMO_MODE=1 marks
 * an installation as a public demo. Administrator passwords cannot be changed or reset, the installers accept a
 * well-known administrator password, the administration shows a banner, and each process records the mode once.
 *
 * DB-less: the accounts are eZUser objects of the test's own with a fixed access array; nothing is read from or
 * written to a database. The demo password is built at run time from a base word of the well-known list.
 *
 *  DM-01  Only the exact value "1" switches it on (trimmed); "0", "true", "yes", "on" and an empty value do not
 *  DM-02  getenv() first, then $_SERVER; never a request parameter, never an INI setting; nothing is cached
 *  DM-03  An administrator is an account with unlimited access to every module and function; an error counts as one
 *  DM-04  The store guard's decision: a new account, an unchanged password, a non-administrator and installing()
 *         pass; a changed administrator password does not, and only while the mode is on
 *  DM-05  installing() is a scope: nested, and it ends on an exception too
 *  DM-06  The installers: a well-known administrator password is accepted only in demo mode; an empty one never is
 *         (setup wizard, kickstarter and exp:install share the check)
 *  DM-07  announce() records once per process, and nothing when the mode is off
 *  DM-08  The template operator exp_demo_mode() follows the environment and is registered
 *  DM-09  The banner: one partial in design/admin and design/admin4 (admin4l falls back to admin4), the shared
 *         .a4-notice.is-warn, no inline CSS, included outside every cache-block of the admin, admin4 and admin4l
 *         pagelayouts
 *  DM-10  Every password path asks demo mode: user/password, forgot-password (request and link), the user account
 *         form, exp:resetuserpassword, the web services, the password operations and eZUser::store(); the
 *         installation sets the administrator password inside installing()
 *  DM-12  The short demo password (publish) is accepted by the installers only in demo mode, and by the account's
 *         length rule only while the installation sets its administrator password; an empty one never
 *  DM-11  The audit knows system.demo.mode (always on), and the refusal and banner texts are in every complete
 *         kernel catalogue
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expDemoModeTest extends PHPUnit\Framework\TestCase
{
    /** @var string|false the environment value before the test */
    private $envBefore;

    /** @var array the $_SERVER, $_GET, $_POST, $_COOKIE and $_REQUEST values before the test */
    private $globalsBefore = array();

    protected function setUp(): void
    {
        parent::setUp();
        $this->envBefore = getenv( expDemoMode::ENV );
        foreach ( array( '_SERVER', '_GET', '_POST', '_COOKIE', '_REQUEST' ) as $g )
            $this->globalsBefore[$g] = array_key_exists( expDemoMode::ENV, $GLOBALS[$g] ) ? array( $GLOBALS[$g][expDemoMode::ENV] ) : null;
        self::setMode( null );
        expDemoMode::resetAnnounced();
    }

    protected function tearDown(): void
    {
        putenv( $this->envBefore === false ? expDemoMode::ENV : expDemoMode::ENV . '=' . $this->envBefore );
        foreach ( $this->globalsBefore as $g => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$g][expDemoMode::ENV] );
            else
                $GLOBALS[$g][expDemoMode::ENV] = $value[0];
        }
        expDemoMode::resetAnnounced();
        parent::tearDown();
    }

    /** Sets the environment value (null: unset) and leaves $_SERVER without it */
    private static function setMode( $value )
    {
        putenv( $value === null ? expDemoMode::ENV : expDemoMode::ENV . '=' . $value );
        unset( $_SERVER[expDemoMode::ENV] );
    }

    private static function file( $path )
    {
        return file_get_contents( __DIR__ . '/../../../../../' . $path );
    }

    /** An account with a fixed access array (a callable throws, for the error case) */
    private static function account( $id, $accessArray )
    {
        $user = new class( array( 'contentobject_id' => $id, 'login' => 'demo' . $id, 'email' => 'demo' . $id . '@example.invalid',
                                  'password_hash' => 'h', 'password_hash_type' => 7 ) ) extends eZUser {
            public $fixedAccess = array();
            function accessArray()
            {
                if ( is_callable( $this->fixedAccess ) )
                    return call_user_func( $this->fixedAccess );
                return $this->fixedAccess;
            }
        };
        $user->fixedAccess = $accessArray;
        return $user;
    }

    private static function adminAccess()
    {
        return array( '*' => array( '*' => array( '*' => '*' ) ) );
    }

    // ------------------------------------------------------------------ DM-01

    public function testOnlyTheExactValueOneSwitchesDemoModeOn()
    {
        $this->assertFalse( expDemoMode::enabled(), 'not set' );
        foreach ( array( '1', ' 1 ', "1\n" ) as $on )
        {
            self::setMode( $on );
            $this->assertTrue( expDemoMode::enabled(), var_export( $on, true ) );
        }
        foreach ( array( '', '0', 'true', 'TRUE', 'yes', 'on', '2', '01', '1.0', 'enabled' ) as $off )
        {
            self::setMode( $off );
            $this->assertFalse( expDemoMode::enabled(), var_export( $off, true ) );
        }
    }

    // ------------------------------------------------------------------ DM-02

    public function testTheEnvironmentComesFirstThenServerAndNeverTheRequest()
    {
        // $_SERVER (Apache SetEnv with mod_php) when the process environment has nothing
        $_SERVER[expDemoMode::ENV] = '1';
        $this->assertTrue( expDemoMode::enabled() );
        // a value in the process environment wins
        putenv( expDemoMode::ENV . '=0' );
        $this->assertFalse( expDemoMode::enabled(), 'getenv() is read first' );
        self::setMode( null );

        // a request can never switch it on
        $_GET[expDemoMode::ENV] = '1';
        $_POST[expDemoMode::ENV] = '1';
        $_COOKIE[expDemoMode::ENV] = '1';
        $_REQUEST[expDemoMode::ENV] = '1';
        $_SERVER['HTTP_EXP_DEMO_MODE'] = '1';
        $this->assertFalse( expDemoMode::enabled() );
        unset( $_SERVER['HTTP_EXP_DEMO_MODE'] );

        // nothing is cached: the answer follows the environment at once (persistent workers)
        self::setMode( '1' );
        $this->assertTrue( expDemoMode::enabled() );
        self::setMode( null );
        $this->assertFalse( expDemoMode::enabled() );

        $source = self::file( 'kernel/classes/expdemomode.php' );
        $code = preg_replace( '#/\*.*?\*/#s', '', $source );
        foreach ( array( 'eZINI', '$_GET', '$_POST', '$_COOKIE', '$_REQUEST', 'eZHTTPTool', 'ezini(' ) as $never )
            $this->assertStringNotContainsString( $never, $code, "demo mode is never read from $never" );
        $this->assertStringContainsString( "const ENV = 'EXP_DEMO_MODE';", $source );
    }

    // ------------------------------------------------------------------ DM-03

    public function testAnAdministratorHasUnlimitedAccessToEveryModule()
    {
        $this->assertTrue( expDemoMode::accessArrayIsUnlimited( self::adminAccess() ) );
        $this->assertTrue( expDemoMode::accessArrayIsUnlimited( array( '*' => array( '*' => array( '*' => array( '*' ) ) ) ) ) );
        $this->assertFalse( expDemoMode::accessArrayIsUnlimited( array() ) );
        $this->assertFalse( expDemoMode::accessArrayIsUnlimited( null ) );
        $this->assertFalse( expDemoMode::accessArrayIsUnlimited( array( 'content' => array( '*' => array( '*' => '*' ) ) ) ), 'one module' );
        $this->assertFalse( expDemoMode::accessArrayIsUnlimited( array( '*' => array( '*' => array( 'Section' => array( 1 ) ) ) ) ), 'limited' );
        $this->assertFalse( expDemoMode::accessArrayIsUnlimited( array( 'user' => array( 'password' => array( '*' => '*' ) ) ) ) );

        $this->assertTrue( expDemoMode::isAdministrator( self::account( 14, self::adminAccess() ) ) );
        $this->assertFalse( expDemoMode::isAdministrator( self::account( 10, array( 'user' => array( 'login' => array( '*' => '*' ) ) ) ) ) );
        $this->assertFalse( expDemoMode::isAdministrator( null ) );
        $this->assertFalse( expDemoMode::isAdministrator( 0 ) );
        $this->assertFalse( expDemoMode::isAdministrator( 'admin' ) );
        // roles that cannot be read: the account counts as an administrator (fail closed)
        $this->assertTrue( expDemoMode::isAdministrator( self::account( 15, function () { throw new RuntimeException( 'no roles' ); } ) ) );

        // protectsPassword(): only while demo mode is on
        $admin = self::account( 14, self::adminAccess() );
        $this->assertFalse( expDemoMode::protectsPassword( $admin ) );
        self::setMode( '1' );
        $this->assertTrue( expDemoMode::protectsPassword( $admin ) );
        $this->assertFalse( expDemoMode::protectsPassword( self::account( 10, array() ) ) );
        $this->assertNotSame( '', expDemoMode::refusalText() );
    }

    // ------------------------------------------------------------------ DM-04

    public function testTheStoreGuardKeepsOnlyAnAdministratorsChangedPassword()
    {
        $stored = array( 'password_hash' => 'old', 'password_hash_type' => '7' );
        $admin = function () { return true; };
        $editor = function () { return false; };
        $never = function () { throw new LogicException( 'asked whether the account is an administrator when it does not matter' ); };

        $this->assertFalse( expDemoMode::storeAllowed( $stored, 'new', 7, true, false, $admin ), 'a changed administrator password' );
        $this->assertFalse( expDemoMode::storeAllowed( $stored, 'old', 8, true, false, $admin ), 'a changed hash type' );
        $this->assertTrue( expDemoMode::storeAllowed( $stored, 'old', 7, true, false, $never ), 'unchanged' );
        $this->assertTrue( expDemoMode::storeAllowed( $stored, 'new', 7, true, false, $editor ), 'not an administrator' );
        $this->assertTrue( expDemoMode::storeAllowed( false, 'new', 7, true, false, $never ), 'a new account' );
        $this->assertTrue( expDemoMode::storeAllowed( null, 'new', 7, true, false, $never ), 'a new account' );
        $this->assertTrue( expDemoMode::storeAllowed( $stored, 'new', 7, false, false, $never ), 'demo mode off' );
        $this->assertTrue( expDemoMode::storeAllowed( $stored, 'new', 7, true, true, $never ), 'an installation' );

        // the guard runs before the store and the audit's view of it
        $store = self::file( 'kernel/classes/datatypes/ezuser/ezuser.php' );
        $guard = strpos( $store, 'expDemoMode::guardStore( $this )' );
        $this->assertNotFalse( $guard );
        $this->assertLessThan( strpos( $store, 'parent::store( $fieldFilters );' ), $guard );
    }

    // ------------------------------------------------------------------ DM-05

    public function testInstallingIsAScopeThatEndsOnAnException()
    {
        $this->assertFalse( expDemoMode::isInstalling() );
        $inner = null;
        $result = expDemoMode::installing( function () use ( &$inner ) {
            expDemoMode::installing( function () use ( &$inner ) { $inner = expDemoMode::isInstalling(); } );
            return expDemoMode::isInstalling() ? 'inside' : 'outside';
        } );
        $this->assertSame( 'inside', $result );
        $this->assertTrue( $inner );
        $this->assertFalse( expDemoMode::isInstalling() );
        try
        {
            expDemoMode::installing( function () { throw new RuntimeException( 'failed install' ); } );
            $this->fail( 'the exception goes through' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertSame( 'failed install', $e->getMessage() );
        }
        $this->assertFalse( expDemoMode::isInstalling(), 'the scope ended with the exception' );
    }

    // ------------------------------------------------------------------ DM-06

    public function testTheInstallersAcceptAWellKnownPasswordOnlyInDemoMode()
    {
        // the classic demo password with an ending: well-known, and as long as MinPasswordLength (10)
        $demoPassword = 'publish' . 'ing';
        $min = eZStepSiteAdmin::minAdminPasswordLength();

        $this->assertTrue( eZStepSiteAdmin::isWeakDefaultPassword( $demoPassword ) );
        $this->assertSame( 'well-known', eZStepSiteAdmin::kickstartPasswordProblem( $demoPassword ) );
        $this->assertSame( 'well-known', \Exponential\Command\Kernel\Install::passwordProblem( $demoPassword ) );
        $this->assertFalse( eZStepSiteAdmin::demoModeAcceptsWellKnown() );

        self::setMode( '1' );
        $this->assertTrue( eZStepSiteAdmin::demoModeAcceptsWellKnown() );
        $this->assertFalse( eZStepSiteAdmin::isWeakDefaultPassword( $demoPassword ) );
        if ( strlen( $demoPassword ) >= $min )
        {
            $this->assertNull( eZStepSiteAdmin::kickstartPasswordProblem( $demoPassword ), 'kickstarter' );
            $this->assertNull( \Exponential\Command\Kernel\Install::passwordProblem( $demoPassword ), 'exp:install' );
        }
        // never an empty one, and the length rule stays
        $this->assertTrue( eZStepSiteAdmin::isWeakDefaultPassword( '' ) );
        $this->assertTrue( eZStepSiteAdmin::isWeakDefaultPassword( '   ' ) );
        $this->assertTrue( eZStepSiteAdmin::isWeakDefaultPassword( null ) );
        $this->assertSame( 'empty', eZStepSiteAdmin::kickstartPasswordProblem( '' ) );
        // the forms' check (every other account) is not relaxed
        $this->assertTrue( expWeakPasswordList::instance()->isWeak( $demoPassword ) );

        self::setMode( '0' );
        $this->assertTrue( eZStepSiteAdmin::isWeakDefaultPassword( $demoPassword ) );

        // the setup wizard says so on its page
        $this->assertStringContainsString( "'demo_mode', self::demoModeAcceptsWellKnown()", self::file( 'kernel/setup/steps/ezstep_site_admin.php' ) );
        $this->assertStringContainsString( 'first_set( $demo_mode, false() )', self::file( 'design/standard/templates/setup/init/site_admin.tpl' ) );
    }

    // ------------------------------------------------------------------ DM-12

    public function testTheInstallersAcceptTheShortDemoPasswordOnlyInDemoModeAndTheInstallingScope()
    {
        // the classic demo password: well-known and shorter than MinPasswordLength (10 as shipped)
        $short = 'publish';
        $min = eZStepSiteAdmin::minAdminPasswordLength();
        $this->assertLessThan( $min, strlen( $short ), 'the shipped MinPasswordLength is longer than the demo password' );

        // without demo mode: refused by every installer, and by the length rule of the account
        $this->assertNotNull( eZStepSiteAdmin::kickstartPasswordProblem( $short ), 'kickstarter' );
        $this->assertNotNull( \Exponential\Command\Kernel\Install::passwordProblem( $short ), 'exp:install' );
        $this->assertTrue( eZStepSiteAdmin::isTooShortAdminPassword( $short ) );
        $this->assertFalse( eZUser::validatePassword( $short ) );
        $this->assertFalse( expDemoMode::installing( function () use ( $short ) {
            return expDemoMode::settingInstallationAdministratorPassword( function () use ( $short ) { return eZUser::validatePassword( $short ); } );
        } ), 'the scopes alone do not open the length without demo mode' );

        self::setMode( '1' );
        // demo mode: the installers' checks (wizard, kickstarter, exp:install) accept it
        $this->assertFalse( eZStepSiteAdmin::isTooShortAdminPassword( $short ) );
        $this->assertNull( eZStepSiteAdmin::kickstartPasswordProblem( $short ), 'kickstarter' );
        $this->assertNull( \Exponential\Command\Kernel\Install::passwordProblem( $short ), 'exp:install' );
        // the account's length rule opens only inside both scopes (the installation setting the administrator)
        $this->assertFalse( eZUser::validatePassword( $short ), 'every other account and every later change' );
        $this->assertFalse( expDemoMode::installing( function () use ( $short ) { return eZUser::validatePassword( $short ); } ),
                            'installing() alone (other accounts the installation creates)' );
        $this->assertFalse( expDemoMode::settingInstallationAdministratorPassword( function () use ( $short ) { return eZUser::validatePassword( $short ); } ),
                            'outside an installation' );
        $inside = expDemoMode::installing( function () use ( $short ) {
            return expDemoMode::settingInstallationAdministratorPassword( function () use ( $short ) {
                return array( eZUser::validatePassword( $short ), eZUser::validatePassword( '' ), eZUser::validatePassword( '   ' ),
                              eZUser::validatePassword( false ), eZUser::validatePassword( null ) );
            } );
        } );
        $this->assertSame( array( true, false, false, false, false ), $inside, 'the short one is accepted, an empty one never' );
        $this->assertFalse( expDemoMode::acceptsShortInstallationAdministratorPassword(), 'the scope ended' );
        $this->assertFalse( eZUser::validatePassword( $short ) );
        // an empty one stays refused by every installer
        $this->assertSame( 'empty', eZStepSiteAdmin::kickstartPasswordProblem( '' ) );
        $this->assertSame( 'empty', eZStepSiteAdmin::kickstartPasswordProblem( '  ' ) );
        $this->assertTrue( eZStepSiteAdmin::isWeakDefaultPassword( '' ), 'exp:install' );
        // the form rules of every account stay
        $this->assertNotSame( array(), expPasswordPolicy::instance()->accountErrors( $short ) );

        // the paths: the wizard's step, exp:install and the installation's own setting of the password
        $this->assertStringContainsString( '!self::demoModeAcceptsWellKnown() && !eZUser::validatePassword(', self::file( 'kernel/setup/steps/ezstep_site_admin.php' ) );
        $this->assertStringContainsString( '\eZStepSiteAdmin::isTooShortAdminPassword( $password )', self::file( 'kernel/private/classes/commands/install.php' ) );
        $this->assertStringContainsString( 'expDemoMode::settingInstallationAdministratorPassword( $setAdminPassword )', self::file( 'kernel/setup/steps/ezstep_create_sites.php' ) );
    }

    // ------------------------------------------------------------------ DM-07

    public function testAnnounceRecordsOncePerProcessAndNothingWhenOff()
    {
        // the decision of announce(), without writing the log and the audit
        $this->assertFalse( expDemoMode::claimAnnouncement(), 'off' );
        $this->assertFalse( expDemoMode::announce(), 'off: nothing is recorded' );
        self::setMode( '1' );
        $this->assertTrue( expDemoMode::claimAnnouncement() );
        $this->assertFalse( expDemoMode::claimAnnouncement(), 'once per process' );
        $this->assertFalse( expDemoMode::announce(), 'already recorded in this process' );
        expDemoMode::resetAnnounced();
        $this->assertTrue( expDemoMode::claimAnnouncement() );

        // the line has the request classifier's form, so the log viewer groups it by its first word
        $line = expDemoMode::securityLine( '4711', 'fpm-fcgi' );
        $this->assertStringStartsWith( 'demo/mode on pid=4711 sapi=fpm-fcgi EXP_DEMO_MODE=1', $line );
        $this->assertStringStartsWith( 'demo/mode on pid=12 sapi=clix ', expDemoMode::securityLine( "1\n2", "cli\"<x>" ), 'only safe characters' );
        $this->assertStringNotContainsString( "\n", expDemoMode::securityLine( "1\n2", "cli\n" ) );

        $this->assertStringContainsString( "expDemoMode::announce()", self::file( 'kernel/private/classes/ezpkernelweb.php' ) );
        $this->assertStringContainsString( "expDemoMode::announce()", self::file( 'kernel/classes/ezscript.php' ) );
        $source = self::file( 'kernel/classes/expdemomode.php' );
        $this->assertStringContainsString( "'security.log'", $source );
        $this->assertStringContainsString( "getmypid()", $source, 'a forked worker records it too' );
    }

    // ------------------------------------------------------------------ DM-08

    public function testTheTemplateOperatorFollowsTheEnvironment()
    {
        $operator = new expDemoModeOperator();
        $this->assertSame( array( 'exp_demo_mode' ), $operator->operatorList() );
        $this->assertSame( array(), $operator->namedParameterList() );
        $value = null;
        $operator->modify( null, 'exp_demo_mode', array(), '', '', $value, array(), false );
        $this->assertFalse( $value );
        self::setMode( '1' );
        $operator->modify( null, 'exp_demo_mode', array(), '', '', $value, array(), false );
        $this->assertTrue( $value );
        $this->assertFalse( method_exists( $operator, 'operatorTemplateHints' ), 'never worked out at compile time' );
        $this->assertStringContainsString( "'class' => 'expDemoModeOperator',\n                                    'operator_names' => array( 'exp_demo_mode' )",
                                           self::file( 'kernel/common/eztemplateautoload.php' ) );
    }

    // ------------------------------------------------------------------ DM-09

    public function testTheBannerIsOneSharedPartialOutsideTheCacheBlocks()
    {
        $admin = self::file( 'design/admin/templates/parts/exp_demo_mode_banner.tpl' );
        $admin4 = self::file( 'design/admin4/templates/parts/exp_demo_mode_banner.tpl' );
        $body = function ( $tpl ) { return preg_replace( '/^\{\*.*?\*\}\s*/s', '', $tpl ); };
        $this->assertSame( $body( $admin4 ), $body( $admin ), 'the same banner in both designs' );
        foreach ( array( $admin, $admin4 ) as $tpl )
        {
            $this->assertStringContainsString( '{if exp_demo_mode()}', $tpl );
            $this->assertStringContainsString( 'class="a4-notice is-warn"', $tpl );
            $this->assertDoesNotMatchRegularExpression( '/<style|\sstyle\s*=/i', $tpl );
            $this->assertStringContainsString( "|i18n( 'design/admin/pagelayout' )", $tpl );
        }
        // admin4 (and admin4l without a layout): at the top of the main area, above the page's content, below the
        // fixed header
        $main = self::file( 'design/admin4/templates/page_mainarea.tpl' );
        $this->assertLessThan( strpos( $main, '{$module_result.content}' ), strpos( $main, "{include uri='design:parts/exp_demo_mode_banner.tpl'}" ) );
        $this->assertStringNotContainsString( 'cache-block', $main );
        // where each pagelayout draws the banner (directly, or through the main area): never inside a cache-block
        $needles = array(
            'design/admin' => array( "{include uri='design:parts/exp_demo_mode_banner.tpl'}" ),
            'design/admin4' => array( "{include uri='design:page_mainarea.tpl'}" ),
            'design/admin4l' => array( "{include uri='design:page_mainarea.tpl'}", "{if \$admin_layout}{include uri='design:parts/exp_demo_mode_banner.tpl'}" ),
        );
        foreach ( $needles as $design => $list )
        {
            $layout = self::file( "$design/templates/pagelayout.tpl" );
            foreach ( $list as $needle )
            {
                $offset = 0;
                $found = 0;
                while ( ( $at = strpos( $layout, $needle, $offset ) ) !== false )
                {
                    $before = substr( $layout, 0, $at );
                    $this->assertSame( substr_count( $before, '{cache-block' ), substr_count( $before, '{/cache-block}' ),
                                       "$design: $needle is outside every cache-block" );
                    $this->assertGreaterThan( strpos( $layout, '<body' ), $at, $design );
                    $offset = $at + 1;
                    $found++;
                }
                $this->assertGreaterThan( 0, $found, "$design: $needle" );
            }
        }
        // admin4l with a layout: every main zone has the banner in front of it
        $admin4l = self::file( 'design/admin4l/templates/pagelayout.tpl' );
        $this->assertSame( substr_count( $admin4l, "zone_identifier='main'" ),
                           substr_count( $admin4l, "{include uri='design:parts/exp_demo_mode_banner.tpl'}{include uri='design:explayouts/admin_zone.tpl' layout=\$admin_layout zone_identifier='main'}" ) );
    }

    // ------------------------------------------------------------------ DM-10

    public function testEveryPasswordPathAsksDemoMode()
    {
        $paths = array(
            'kernel/private/classes/views/user/password.php' => array( '\expDemoMode::protectsPassword( $user )', '\expDemoMode::refusalText()', '"demo_mode_locked"' ),
            'kernel/private/classes/views/user/forgotpassword.php' => array( "\\expDemoMode::protectsPassword( (int)\$forgotPasswdObj->attribute( 'user_id' ) )",
                                                                       '\expDemoMode::protectsPassword( $users[0] )', "'demo_mode_text'" ),
            'kernel/classes/datatypes/ezuser/ezusertype.php' => array( 'expDemoMode::isAdministrator( $demoAccount )', 'setValidationError( expDemoMode::refusalText() )' ),
            'kernel/private/classes/commands/resetuserpassword.php' => array( '\expDemoMode::protectsPassword( $ruleAccount )', '$script->shutdown( 1 );' ),
            'extension/expservices/classes/users/expusersbase.php' => array( 'expDemoMode::protectsPassword( $user )', 'new expServiceException( expDemoMode::refusalText(), 403 )' ),
            'extension/expservices/classes/users/expaccountservices.php' => array( 'expDemoMode::protectsPassword( $users[0] )' ),
            'kernel/user/ezuseroperationcollection.php' => array( "'reason' => 'demo_mode'" ),
            'kernel/setup/steps/ezstep_create_sites.php' => array( 'expDemoMode::installing( $initialize )' ),
        );
        foreach ( $paths as $path => $needles )
        {
            $source = self::file( $path );
            foreach ( $needles as $needle )
                $this->assertStringContainsString( $needle, $source, $path );
        }
        // both operations (the password and the reset key) refuse
        $this->assertSame( 2, substr_count( self::file( 'kernel/user/ezuseroperationcollection.php' ), 'expDemoMode::protectsPassword( $user )' ) );
        // the console refuses before it changes anything, also with --allow-root-user
        $console = self::file( 'kernel/private/classes/commands/resetuserpassword.php' );
        $this->assertLessThan( strpos( $console, '$targetUser->setInformation(' ), strpos( $console, '\expDemoMode::protectsPassword( $ruleAccount )' ) );
        // the templates show the refusal
        foreach ( array( 'design/admin/templates/user/password.tpl', 'design/admin4/templates/user/password.tpl', 'design/standard/templates/user/password.tpl' ) as $tpl )
            $this->assertStringContainsString( 'first_set( $demo_mode_locked, false() )', self::file( $tpl ), $tpl );
        $this->assertStringContainsString( "first_set( \$demo_mode_text, '' )", self::file( 'design/standard/templates/user/forgotpassword.tpl' ) );
    }

    // ------------------------------------------------------------------ DM-11

    public function testTheAuditAndTheCataloguesKnowDemoMode()
    {
        $source = self::file( 'kernel/classes/audit/expaudittaxonomy.php' );
        $this->assertMatchesRegularExpression( "/'system\\.demo\\.mode' => array\\( 'severity' => 'warning', 'default' => 'always'/", $source );
        $this->assertStringContainsString( '`system.demo.mode`', self::file( 'doc/bc/6.0/audit.md' ) );

        $messages = array(
            'kernel/user/demo' => 'This installation is a public demo: the passwords of administrators cannot be changed or reset.',
            'design/admin/pagelayout' => 'Demo mode',
        );
        $root = __DIR__ . '/../../../../../share/translations';
        $catalogues = 0;
        foreach ( array( 'ara-SA', 'eng-US', 'esl-ES', 'esl-MX', 'fre-FR', 'ger-DE', 'heb-IL', 'lat-VA', 'tlh-QO', 'ukr-UA', 'untranslated' ) as $locale )
        {
            $ts = file_get_contents( "$root/$locale/translation.ts" );
            foreach ( $messages as $context => $text )
            {
                $at = strpos( $ts, '<name>' . $context . '</name>' );
                $this->assertNotFalse( $at, "$locale: $context" );
                $end = strpos( $ts, '</context>', $at );
                $this->assertStringContainsString( '<source>' . htmlspecialchars( $text, ENT_NOQUOTES ) . '</source>',
                                                   substr( $ts, $at, $end - $at ), "$locale: $context" );
            }
            $catalogues++;
        }
        $this->assertSame( 11, $catalogues );
    }
}
?>
