<?php
/**
 * "Change password at next sign-in" (kernel/classes/user/exppasswordchangerequired.php,
 * doc/eng-us/features/6.0/password-change-at-next-sign-in.md). DB-less: the accounts are eZUser objects of the test's
 * own, the mark is a stub of the test (no ezpreferences row is read or written), and the wiring is read from the
 * source files.
 *
 *  PCR-01  A stored value is a mark when it is set, not empty and not "0"
 *  PCR-02  The decision: a signed-in account with the mark must change its password; anonymous never; an
 *          administrator in demo mode (EXP_DEMO_MODE=1) is never locked out, a non-administrator still is
 *  PCR-03  The views that stay open: module/view, a whole module, module/*; user/password always; the shipped
 *          list has user/password, user/logout and the two-step sign-in (user2fa)
 *  PCR-04  The edit form: ticked by default for an account being created, else the draft's choice, else the mark;
 *          only another person's account, by a signed-in editor
 *  PCR-05  The posted form: no hidden field means "not shown" (nothing changes); the checkbox ticks; the draft carries
 *          the choice only when the form showed it
 *  PCR-06  The preference name is reserved: user/preferences refuses to write it
 *  PCR-07  The REST answer: 403 with the error code password_change_required, wired before the request is signed in;
 *          WebDAV and SOAP refuse the sign-in
 *  PCR-08  The kernel asks before every view (after the audit guard, before the request rules); user/password clears
 *          the mark after a change and tells its template
 *  PCR-09  The audit knows .set, .clear and .redirect, with their verbs, and audit.md lists them
 *  PCR-10  The templates: the checkbox and its hidden field in design/standard and design/admin4, no inline CSS, the
 *          shared .a4-check in admin4; the notice on every user/password template
 *  PCR-11  The new texts are in every complete kernel catalogue; the console command is registered
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/user/expPasswordChangeRequiredTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

/** The mark without a database: what the test sets */
class expPasswordChangeRequiredTestDouble extends expPasswordChangeRequired
{
    public static $marked = false;

    public static function isMarked( $user )
    {
        return (bool)static::$marked;
    }
}

class expPasswordChangeRequiredTest extends PHPUnit\Framework\TestCase
{
    /** @var string|false */
    private $envBefore;

    /** @var array */
    private $postBefore;

    protected function setUp(): void
    {
        parent::setUp();
        $this->envBefore = getenv( expDemoMode::ENV );
        putenv( expDemoMode::ENV );
        unset( $_SERVER[expDemoMode::ENV] );
        $this->postBefore = $_POST;
        expPasswordChangeRequiredTestDouble::$marked = false;
    }

    protected function tearDown(): void
    {
        putenv( $this->envBefore === false ? expDemoMode::ENV : expDemoMode::ENV . '=' . $this->envBefore );
        $_POST = $this->postBefore;
        parent::tearDown();
    }

    private static function file( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 5 ) . '/' . $path );
    }

    /** An account with a fixed access array */
    private static function account( $id, array $accessArray = array() )
    {
        $user = new class( array( 'contentobject_id' => $id, 'login' => 'pcr' . $id, 'email' => 'pcr' . $id . '@example.invalid',
                                  'password_hash' => 'h', 'password_hash_type' => 7 ) ) extends eZUser {
            public $fixedAccess = array();
            function accessArray()
            {
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

    // ------------------------------------------------------------------ PCR-01

    public function testAStoredValueIsAMark()
    {
        foreach ( array( '1', '1790000000', ' 1 ', 'yes' ) as $on )
            $this->assertTrue( expPasswordChangeRequired::isMark( $on ), var_export( $on, true ) );
        foreach ( array( null, false, '', '   ', '0', ' 0 ', array( 1 ) ) as $off )
            $this->assertFalse( expPasswordChangeRequired::isMark( $off ), var_export( $off, true ) );
    }

    // ------------------------------------------------------------------ PCR-02

    public function testTheDecision()
    {
        $this->assertTrue( expPasswordChangeRequired::decide( true, false, false ) );
        $this->assertFalse( expPasswordChangeRequired::decide( false, false, false ), 'no mark' );
        $this->assertFalse( expPasswordChangeRequired::decide( true, true, false ), 'anonymous' );
        $this->assertFalse( expPasswordChangeRequired::decide( true, false, true ), 'an administrator in demo mode' );
    }

    public function testDemoModeNeverLocksAnAdministratorOut()
    {
        $admin = self::account( 14, self::adminAccess() );
        $editor = self::account( 15, array( 'content' => array( 'read' => array( '*' => '*' ) ) ) );
        expPasswordChangeRequiredTestDouble::$marked = true;

        // demo mode off: the mark holds for both
        $this->assertTrue( expPasswordChangeRequiredTestDouble::isRequired( $admin ) );
        $this->assertTrue( expPasswordChangeRequiredTestDouble::isRequired( $editor ) );

        putenv( expDemoMode::ENV . '=1' );
        $this->assertTrue( expDemoMode::enabled() );
        $this->assertFalse( expPasswordChangeRequiredTestDouble::isRequired( $admin ), 'demo mode: the administrator is not sent to user/password' );
        $this->assertTrue( expPasswordChangeRequiredTestDouble::isRequired( $editor ), 'demo mode: a non-administrator still is' );

        expPasswordChangeRequiredTestDouble::$marked = false;
        $this->assertFalse( expPasswordChangeRequiredTestDouble::isRequired( $editor ) );
        $this->assertFalse( expPasswordChangeRequiredTestDouble::isRequired( null ) );
    }

    // ------------------------------------------------------------------ PCR-03

    public function testExemptViews()
    {
        $list = array( 'user/password', 'user/logout', 'user2fa', 'layout/*' );
        $this->assertTrue( expPasswordChangeRequired::isExempt( 'user', 'password', $list ) );
        $this->assertTrue( expPasswordChangeRequired::isExempt( 'user', 'logout', $list ) );
        $this->assertTrue( expPasswordChangeRequired::isExempt( 'user2fa', 'verify', $list ), 'a whole module' );
        $this->assertTrue( expPasswordChangeRequired::isExempt( 'user2fa', 'setup', $list ) );
        $this->assertTrue( expPasswordChangeRequired::isExempt( 'layout', 'set', $list ), 'module/*' );
        $this->assertTrue( expPasswordChangeRequired::isExempt( 'User', 'Password', $list ), 'case' );
        foreach ( array( array( 'user', 'login' ), array( 'user', 'preferences' ), array( 'user', 'edit' ), array( 'content', 'view' ),
                         array( 'content', 'edit' ), array( 'ezjscore', 'call' ), array( 'oauth', 'authorize' ) ) as $mv )
            $this->assertFalse( expPasswordChangeRequired::isExempt( $mv[0], $mv[1], $list ), implode( '/', $mv ) );
        $this->assertFalse( expPasswordChangeRequired::isExempt( 'user', 'password', array() ) );

        $exempt = expPasswordChangeRequired::exemptViews();
        $this->assertContains( 'user/password', $exempt, 'always open' );

        $ini = self::file( 'settings/site.ini' );
        $at = strpos( $ini, "\n[PasswordChangeRequiredSettings]\n" );
        $this->assertNotFalse( $at );
        $block = substr( $ini, $at, strpos( $ini, "\n[", $at + 2 ) - $at );
        $this->assertStringContainsString( "ExemptViews[]\nExemptViews[]=user/password\nExemptViews[]=user/logout\nExemptViews[]=user2fa\n", $block );
        $this->assertSame( array( 'user/password', 'user/logout', 'user2fa' ), expPasswordChangeRequired::DEFAULT_EXEMPT_VIEWS );
    }

    // ------------------------------------------------------------------ PCR-04

    public function testTheCheckboxAndWhoMayChoose()
    {
        $this->assertTrue( expPasswordChangeRequired::checkboxState( null, true, false ), 'a new account: ticked' );
        $this->assertFalse( expPasswordChangeRequired::checkboxState( false, true, false ), 'unticked in the draft' );
        $this->assertTrue( expPasswordChangeRequired::checkboxState( true, false, false ), 'ticked in the draft' );
        $this->assertFalse( expPasswordChangeRequired::checkboxState( null, false, false ), 'an existing account without the mark' );
        $this->assertTrue( expPasswordChangeRequired::checkboxState( null, false, true ), 'an existing account with the mark' );

        $admin = self::account( 14, self::adminAccess() );
        $other = self::account( 77 );
        $anonymous = self::account( eZUser::anonymousId() );
        $this->assertTrue( expPasswordChangeRequired::editorMayChoose( $admin, $other ) );
        $this->assertFalse( expPasswordChangeRequired::editorMayChoose( $other, $other ), 'not on one\'s own account' );
        $this->assertFalse( expPasswordChangeRequired::editorMayChoose( $anonymous, $other ), 'not on self-registration' );
        $this->assertFalse( expPasswordChangeRequired::editorMayChoose( null, $other ) );
        $this->assertNull( expPasswordChangeRequired::applyFromEdit( $other, null, $admin ), 'the form did not show it' );
        $this->assertNull( expPasswordChangeRequired::applyFromEdit( $other, true, $other ), 'own account: nothing changes' );
    }

    // ------------------------------------------------------------------ PCR-05

    public function testThePostedFormAndTheDraft()
    {
        $http = eZHTTPTool::instance();
        $_POST = array();
        $this->assertNull( eZUserType::postedPasswordChangeRequired( $http, 'ContentObjectAttribute', 5 ), 'not shown' );
        $_POST = array( 'ContentObjectAttribute_data_user_password_change_required_5' => '1' );
        $this->assertNull( eZUserType::postedPasswordChangeRequired( $http, 'ContentObjectAttribute', 5 ), 'a checkbox without the hidden field' );
        $_POST = array( 'ContentObjectAttribute_data_user_password_change_shown_5' => '1' );
        $this->assertFalse( eZUserType::postedPasswordChangeRequired( $http, 'ContentObjectAttribute', 5 ), 'shown, not ticked' );
        $_POST['ContentObjectAttribute_data_user_password_change_required_5'] = '1';
        $this->assertTrue( eZUserType::postedPasswordChangeRequired( $http, 'ContentObjectAttribute', 5 ), 'ticked' );
        $_POST['ContentObjectAttribute_data_user_password_change_shown_5'] = array( '1' );
        $this->assertNull( eZUserType::postedPasswordChangeRequired( $http, 'ContentObjectAttribute', 5 ), 'an array is not the field' );

        $user = self::account( 812 );
        $this->assertArrayNotHasKey( 'password_change_required', json_decode( eZUserType::serializeDraft( $user ), true ) );
        $user->PasswordChangeRequiredDraft = true;
        $this->assertTrue( json_decode( eZUserType::serializeDraft( $user ), true )['password_change_required'] );
        $user->PasswordChangeRequiredDraft = false;
        $this->assertFalse( json_decode( eZUserType::serializeDraft( $user ), true )['password_change_required'] );
    }

    // ------------------------------------------------------------------ PCR-06

    public function testThePreferenceNameIsReserved()
    {
        $this->assertTrue( expPasswordChangeRequired::isReserved( 'exp_password_change_required' ) );
        $this->assertTrue( expPasswordChangeRequired::isReserved( ' EXP_Password_Change_Required ' ) );
        $this->assertFalse( expPasswordChangeRequired::isReserved( 'admin_navigation_content' ) );
        $this->assertFalse( expPasswordChangeRequired::isReserved( null ) );
        $view = self::file( 'kernel/private/classes/views/user/preferences.php' );
        $this->assertStringContainsString( '\expPasswordChangeRequired::isReserved( $key )', $view );
    }

    // ------------------------------------------------------------------ PCR-07

    public function testTheRestAnswerAndOtherSignIns()
    {
        $body = expRestPasswordChangeRequiredStatus::body();
        $this->assertSame( 'password_change_required', $body['error'] );
        $this->assertSame( expPasswordChangeRequired::REST_ERROR, $body['error'] );
        $this->assertNotSame( '', $body['error_description'] );
        $status = self::file( 'kernel/private/rest/classes/status/password_change_required.php' );
        $this->assertStringContainsString( "'HTTP/1.1 403'", $status );

        $config = self::file( 'kernel/private/rest/classes/auth/auth_configuration.php' );
        $refusal = strpos( $config, "expPasswordChangeRequired::refusesSignIn( \$user, 'rest' )" );
        $signIn = strpos( $config, 'eZUser::setCurrentlyLoggedInUser( $user, $userID )' );
        $this->assertNotFalse( $refusal );
        $this->assertLessThan( $signIn, $refusal, 'refused before the request is signed in' );
        $this->assertStringContainsString( 'new expRestPasswordChangeRequiredStatus()', self::file( 'kernel/private/rest/classes/controllers/auth.php' ) );
        $this->assertStringContainsString( "refusesSignIn( \$user, 'webdav' )", self::file( 'kernel/private/classes/webdav/ezwebdavcontentbackendauth.php' ) );
        $this->assertStringContainsString( "refusesSignIn( \$soapUser, 'soap' )", self::file( 'soap.php' ) );
        $this->assertFalse( expPasswordChangeRequired::refusesSignIn( null, 'rest' ) );
    }

    // ------------------------------------------------------------------ PCR-08

    public function testTheKernelAsksBeforeEveryView()
    {
        $kernel = self::file( 'kernel/private/classes/ezpkernelweb.php' );
        $guard = strpos( $kernel, 'expAuditGuard::refusedResult(' );
        $check = strpos( $kernel, 'expPasswordChangeRequired::moduleResult( $this->module, $functionName )' );
        $rules = strpos( $kernel, 'ezpRequestRuleKernel::decide(' );
        $this->assertNotFalse( $check );
        $this->assertGreaterThan( $guard, $check );
        $this->assertLessThan( $rules, $check );
        $this->assertSame( '/user/password', expPasswordChangeRequired::PASSWORD_URI );

        $view = self::file( 'kernel/private/classes/views/user/password.php' );
        $changed = strpos( $view, 'if ( $passwordChanged )' );
        $clear = strpos( $view, "\\expPasswordChangeRequired::clear( \$stored, 'password' )" );
        $this->assertNotFalse( $clear );
        $this->assertGreaterThan( $changed, $clear, 'cleared only after a change that went through' );
        $this->assertStringContainsString( '"password_change_required"', $view );
    }

    // ------------------------------------------------------------------ PCR-09

    public function testTheAudit()
    {
        $catalogue = expAuditTaxonomy::catalogue();
        $verbs = array( 'set' => 'require', 'clear' => 'clear', 'redirect' => 'redirect' );
        $doc = self::file( 'doc/eng-us/bc/6.0/audit.md' );
        $docDe = self::file( 'doc/ger-de/bc/6.0/audit.md' );
        foreach ( $verbs as $last => $verb )
        {
            $name = 'access.user.password.change_required.' . $last;
            $this->assertArrayHasKey( $name, $catalogue );
            $this->assertSame( 'access', $catalogue[$name]['channel'] );
            $this->assertSame( $verb, expAuditHook::$verbs[$name] );
            $this->assertStringContainsString( '`' . $name . '`', $doc );
            $this->assertStringContainsString( '`' . $name . '`', $docDe );
        }
        $this->assertSame( 'access.user.password.change_required.set', expPasswordChangeRequired::AUDIT_SET );
        $this->assertSame( 'access.user.password.change_required.clear', expPasswordChangeRequired::AUDIT_CLEAR );
        $this->assertSame( 'access.user.password.change_required.redirect', expPasswordChangeRequired::AUDIT_REDIRECT );
    }

    // ------------------------------------------------------------------ PCR-10

    public function testTheTemplates()
    {
        foreach ( array( 'design/standard', 'design/admin4' ) as $design )
        {
            $tpl = self::file( $design . '/templates/content/datatype/edit/ezuser.tpl' );
            $this->assertStringContainsString( '_data_user_password_change_shown_{$attribute.id}" value="1"', $tpl, $design );
            $this->assertStringContainsString( '_data_user_password_change_required_{$attribute.id}" value="1"', $tpl, $design );
            $this->assertStringContainsString( '$attribute.content.password_change_checked', $tpl, $design );
            $this->assertStringContainsString( "\$pwc_editor.contentobject_id|ne( \$attribute.contentobject_id )", $tpl, $design );
            $this->assertDoesNotMatchRegularExpression( '/<style|style="/i', $tpl, $design );
        }
        $this->assertStringContainsString( 'class="a4-check"', self::file( 'design/admin4/templates/content/datatype/edit/ezuser.tpl' ) );
        foreach ( array( 'design/standard', 'design/admin', 'design/admin4' ) as $design )
        {
            $tpl = self::file( $design . '/templates/user/password.tpl' );
            $this->assertStringContainsString( 'first_set( $password_change_required, false() )', $tpl, $design );
            $this->assertStringContainsString( 'an administrator asked you to replace the password you have now.', $tpl, $design );
        }
    }

    // ------------------------------------------------------------------ PCR-11

    public function testTheCataloguesAndTheConsoleCommand()
    {
        $messages = array(
            array( 'design/standard/content/datatype', 'Change password at next sign-in' ),
            array( 'design/standard/content/datatype', 'After signing in, the user must choose a new password before doing anything else. Use it when you give someone a temporary password.' ),
            array( 'design/standard/user/password', 'Choose a new password before you continue: an administrator asked you to replace the password you have now.' ),
            array( 'design/admin/user/password_page', 'Choose a new password before you continue: an administrator asked you to replace the password you have now.' ),
            array( 'kernel/audit', 'Password change at next sign-in set' ),
            array( 'kernel/audit', 'Password change at next sign-in cleared' ),
            array( 'kernel/audit', 'Sent to the password change' ),
        );
        $catalogue = expAuditTaxonomy::catalogue();
        $this->assertSame( 'Password change at next sign-in set', $catalogue[expPasswordChangeRequired::AUDIT_SET]['label'] );
        $locales = array( 'ara-SA', 'eng-US', 'esl-ES', 'esl-MX', 'fre-FR', 'ger-DE', 'heb-IL', 'lat-VA', 'tlh-QO', 'ukr-UA', 'untranslated' );
        foreach ( $locales as $locale )
        {
            $ts = self::file( "share/translations/$locale/translation.ts" );
            foreach ( $messages as $m )
            {
                $at = strpos( $ts, '<name>' . $m[0] . '</name>' );
                $this->assertNotFalse( $at, "$locale: {$m[0]}" );
                $end = strpos( $ts, '</context>', $at );
                $block = substr( $ts, $at, $end - $at );
                $source = '<source>' . htmlspecialchars( $m[1], ENT_NOQUOTES ) . '</source>';
                $this->assertStringContainsString( $source, $block, "$locale: {$m[1]}" );
                if ( $locale !== 'untranslated' )
                    $this->assertDoesNotMatchRegularExpression( '/' . preg_quote( $source, '/' ) . '\s*<translation type="unfinished"/', $block, "$locale: finished" );
            }
        }

        $this->assertTrue( class_exists( 'Exponential\\Command\\Kernel\\Userpasswordchangerequired' ) );
        $this->assertStringContainsString( '@alias exp:user-password-change-required', self::file( 'bin/php/userpasswordchangerequired.php' ) );
        $this->assertStringContainsString( '@alias exp:user-password-change-required', self::file( 'kernel/private/classes/commands/userpasswordchangerequired.php' ) );
    }
}
