<?php
/**
 * The modern user/password page, kernel side (doc/features/6.0/modern-password-change.md): the rules of
 * expPasswordPolicy, the user/password view's errors per field and success state, ending the user's other
 * sessions, the audit records and the "your password was changed" mail.
 *
 * Live style: the tests run on the installation they find (no test database), with one test user (remote id and
 * login p1pwd-user, address on p1pwd.invalid) that is removed again; where there is no installation (CI) they are
 * skipped. Every setting a test changes is changed in the process only (eZINI::setVariable) and put back.
 *
 * NO MAIL LEAVES THE SERVER. The mail transport is forced to "file" (into var/tmp) in the process's own ini, never
 * in a settings file; setUp() refuses to run otherwise. The only address is on the reserved .invalid domain.
 *
 *  PWD-01  The shipped defaults are today's rules: the length only, from [UserSettings] MinPasswordLength
 *  PWD-02  Each optional rule refuses what it should and accepts the rest (also outside ASCII)
 *  PWD-03  not_login and not_current read the user
 *  PWD-04  The client config: rules, fields, translated strings, the login only with ForbidLogin
 *  PWD-05  The view: wrong current password, then every problem of the new password at once, per field
 *  PWD-06  The view: a change goes through; the old flags and the new variables say so; the typed passwords
 *          are never handed to the template
 *  PWD-07  The view with an optional rule and an old template's flags: no "success" flag for a refused change
 *  PWD-08  The other sessions of the user end, the one to keep stays, nobody else's are touched
 *  PWD-09  The mail: category security, to the user, never the password; off with ChangeNotificationMail
 *  PWD-10  The audit: access.user.password.change.failed names the rule, access.session.revoke the count
 *  PWD-11  A session handler without a backend says it cannot (null); the database handler ends one user's other sessions
 */

require_once dirname( __DIR__ ) . '/audit/fixtures/expaudittestfixtures.php';

class expPasswordPolicyTest extends PHPUnit\Framework\TestCase
{
    const LOGIN = 'p1pwd-user';
    const EMAIL = 'p1pwd-user@p1pwd.invalid';
    const OTHER_USER_ID = 999999931;

    private static $installation;
    private static $tmp;
    private static $mailDir;
    private static $userID = 0;
    private static $password = '';
    private static $admin;
    private $iniBackup = array();
    private $auditDir = null;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 5 );
        chdir( self::$installation );
        ezpLiveInstallation::requireOrSkip();
        self::$tmp = 'var/tmp/password-policy-test/run-' . getmypid() . '-' . time();
        @mkdir( self::$installation . '/' . self::$tmp . '/mail', 0775, true );
        self::$mailDir = self::$tmp . '/mail';
        self::forceFileTransport();
        self::$admin = eZUser::fetchByName( 'admin' );
        if ( !self::$admin )
            self::markTestSkipped( 'needs the admin user' );
        eZUser::setCurrentlyLoggedInUser( self::$admin, self::$admin->attribute( 'contentobject_id' ) );
        self::removeUser();
        self::makeUser();
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$installation === null )
            return;
        chdir( self::$installation );
        if ( self::$admin )
            eZUser::setCurrentlyLoggedInUser( self::$admin, self::$admin->attribute( 'contentobject_id' ) );
        self::removeUser();
        self::removeSessions();
        self::removeTree( self::$installation . '/' . self::$tmp );
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        self::forceFileTransport();
        foreach ( glob( self::$mailDir . '/*' ) ?: array() as $f )
            @unlink( $f );
        // the shipped defaults, whatever the installation's cached settings say
        foreach ( array( 'RequireLowercase', 'RequireUppercase', 'RequireDigit', 'RequireSymbol', 'ForbidLogin', 'ForbidCurrentPassword' ) as $v )
            $this->setIni( 'PasswordSettings', $v, 'disabled' );
        $this->setIni( 'PasswordSettings', 'MinCharacterClasses', '0' );
        $this->setIni( 'PasswordSettings', 'EndOtherSessions', 'enabled' );
        $this->setIni( 'PasswordSettings', 'ChangeNotificationMail', 'enabled' );
        $this->setIni( 'UserSettings', 'MinPasswordLength', '10' );
    }

    protected function tearDown(): void
    {
        foreach ( $this->iniBackup as $key => $value )
        {
            list( $block, $var ) = explode( '/', $key );
            if ( $value === null )
                eZINI::instance()->removeSetting( $block, $var );
            else
                eZINI::instance()->setVariable( $block, $var, $value );
        }
        $this->iniBackup = array();
        $_POST = array();
        if ( $this->auditDir !== null )
        {
            expAuditHook::reset();
            expAuditTestFixtures::tearDown();
            $this->auditDir = null;
        }
        if ( self::$admin )
            eZUser::setCurrentlyLoggedInUser( self::$admin, self::$admin->attribute( 'contentobject_id' ) );
    }

    // ------------------------------------------------------------------ helpers

    private static function forceFileTransport()
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'MailSettings', 'Transport', 'file' );
        $ini->setVariable( 'MailSettings', 'FileTransportDirectory', self::$mailDir );
        $ini->setVariable( 'MailSettings', 'DebugSending', 'disabled' );
        if ( trim( $ini->variable( 'MailSettings', 'Transport' ) ) !== 'file' )
            throw new RuntimeException( 'The mail transport is not the file transport: the test refuses to run.' );
    }

    private function setIni( $block, $var, $value )
    {
        $ini = eZINI::instance();
        $key = $block . '/' . $var;
        if ( !array_key_exists( $key, $this->iniBackup ) )
            $this->iniBackup[$key] = $ini->hasVariable( $block, $var ) ? $ini->variable( $block, $var ) : null;
        $ini->setVariable( $block, $var, $value );
    }

    private static function makeUser()
    {
        self::$password = 'Start-' . bin2hex( random_bytes( 8 ) );
        $type = eZUser::hashType();
        $hash = eZUser::createHash( self::LOGIN, self::$password, eZUser::site(), $type );
        $account = self::LOGIN . '|' . self::EMAIL . '|' . $hash . '|' . eZUser::passwordHashTypeName( $type ) . '|1';
        $object = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => 5, 'class_identifier' => 'user', 'creator_id' => self::$admin->attribute( 'contentobject_id' ),
            'remote_id' => self::LOGIN, 'attributes' => array( 'first_name' => 'P1PWD', 'last_name' => 'User', 'user_account' => $account ) ) );
        if ( !$object )
            throw new RuntimeException( 'P1PWD: the test user could not be created' );
        self::$userID = (int)$object->attribute( 'id' );
        eZUser::cleanupCache();
    }

    private static function removeUser()
    {
        $object = eZContentObject::fetchByRemoteID( self::LOGIN );
        if ( $object )
        {
            eZDB::instance()->query( 'DELETE FROM ezsession WHERE user_id=' . (int)$object->attribute( 'id' ) );
            $object->purge();
        }
    }

    private static function removeSessions()
    {
        eZDB::instance()->query( "DELETE FROM ezsession WHERE session_key LIKE 'p1pwd-%'" );
    }

    private static function removeTree( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( scandir( $dir ) as $f )
        {
            if ( $f === '.' || $f === '..' )
                continue;
            $p = $dir . '/' . $f;
            if ( is_dir( $p ) )
                self::removeTree( $p );
            else
                @unlink( $p );
        }
        @rmdir( $dir );
    }

    private function user()
    {
        return eZUser::fetch( self::$userID );
    }

    private function addSession( $key, $userID )
    {
        $db = eZDB::instance();
        $db->query( "INSERT INTO ezsession ( session_key, expiration_time, data, user_id ) VALUES ( '" . $db->escapeString( $key ) . "', "
                    . ( time() + 3600 ) . ", '', " . (int)$userID . " )" );
    }

    private function sessionKeys()
    {
        $rows = eZDB::instance()->arrayQuery( "SELECT session_key FROM ezsession WHERE session_key LIKE 'p1pwd-%' ORDER BY session_key" );
        return array_map( function ( $r ) { return $r['session_key']; }, $rows );
    }

    private function mails()
    {
        $out = array();
        foreach ( glob( self::$installation . '/' . self::$mailDir . '/*' ) ?: array() as $f )
            $out[] = file_get_contents( $f );
        return $out;
    }

    /** Runs user/password as the test user with the given POST; returns the template the view filled */
    private function runView( array $post )
    {
        $user = $this->user();
        eZUser::setCurrentlyLoggedInUser( $user, self::$userID );
        $_POST = $post;
        $module = eZModule::exists( 'user' );
        $this->assertInstanceOf( 'eZModule', $module );
        $module->run( 'password', array( self::$userID ) );
        return eZTemplate::factory();
    }

    private function audit()
    {
        $this->auditDir = expAuditTestFixtures::setUp( $this->name(), array( 'AuditEventSettings/Enabled' => array( 'access.*' ) ) );
    }

    private function auditRecords()
    {
        expAudit::flush();
        $out = array();
        foreach ( expAuditTestFixtures::records( $this->auditDir, 'access' ) as $r )
            $out[] = $r;
        return $out;
    }

    // ------------------------------------------------------------------ PWD-01 .. PWD-04: the rules

    public function testShippedDefaultsAreTheLengthOnly()
    {
        // the shipped file itself, not the installation's (cached, overridden) settings
        $text = file_get_contents( self::$installation . '/settings/site.ini' );
        $this->assertSame( 1, preg_match( '/^\[PasswordSettings\]\n(.*?)(?=^\[)/ms', $text, $m ), 'site.ini has [PasswordSettings]' );
        $file = array();
        foreach ( explode( "\n", $m[1] ) as $line )
            if ( preg_match( '/^([A-Za-z]+)=(.*)$/', trim( $line ), $kv ) )
                $file[$kv[1]] = $kv[2];
        foreach ( array( 'RequireLowercase', 'RequireUppercase', 'RequireDigit', 'RequireSymbol', 'ForbidLogin', 'ForbidCurrentPassword' ) as $v )
            $this->assertSame( 'disabled', $file[$v], $v );
        $this->assertSame( '0', $file['MinCharacterClasses'] );
        $this->assertSame( 'enabled', $file['EndOtherSessions'] );
        $this->assertSame( 'enabled', $file['ChangeNotificationMail'] );

        $policy = new expPasswordPolicy();
        $rules = $policy->rules();
        $this->assertCount( 1, $rules );
        $this->assertSame( 'length', $rules[0]['id'] );
        $this->assertSame( 10, $rules[0]['min'] );
        $this->assertSame( array( 'length' ), $policy->validate( 'abcdefghi' ) );
        $this->assertSame( array(), $policy->validate( 'abcdefghij' ), 'ten lowercase letters pass, as before' );
        $this->assertSame( array(), $policy->validate( 'aaaaaaaaaa' ) );

        $this->setIni( 'UserSettings', 'MinPasswordLength', '3' );
        $this->assertSame( array(), $policy->validate( 'abc' ) );
        $this->assertSame( array( 'length' ), $policy->validate( '' ) );
    }

    public function testMissingLengthSettingFallsBackToThree()
    {
        $this->setIni( 'UserSettings', 'MinPasswordLength', '3' );
        eZINI::instance()->removeSetting( 'UserSettings', 'MinPasswordLength' );
        $this->assertSame( 3, ( new expPasswordPolicy() )->minLength() );
    }

    public function testOptionalRules()
    {
        $this->setIni( 'UserSettings', 'MinPasswordLength', '4' );
        foreach ( array( 'RequireLowercase', 'RequireUppercase', 'RequireDigit', 'RequireSymbol' ) as $v )
            $this->setIni( 'PasswordSettings', $v, 'enabled' );
        $policy = new expPasswordPolicy();
        $this->assertSame( array( 'length', 'lowercase', 'uppercase', 'digit', 'symbol' ), array_column( $policy->rules(), 'id' ) );
        $this->assertSame( array( 'uppercase', 'digit', 'symbol' ), $policy->validate( 'abcdef' ) );
        $this->assertSame( array( 'lowercase', 'symbol' ), $policy->validate( 'ABC123' ) );
        $this->assertSame( array(), $policy->validate( 'aB3 x' ), 'a space is a symbol' );
        $this->assertSame( array(), $policy->validate( 'äÖ7€' ), 'letters and symbols outside ASCII count' );
        $this->assertSame( array(), $policy->validate( "aB3!" ) );

        foreach ( array( 'RequireLowercase', 'RequireUppercase', 'RequireDigit', 'RequireSymbol' ) as $v )
            $this->setIni( 'PasswordSettings', $v, 'disabled' );
        $this->setIni( 'PasswordSettings', 'MinCharacterClasses', '3' );
        $this->assertSame( array( 'length', 'classes' ), array_column( $policy->rules(), 'id' ) );
        $this->assertSame( array( 'classes' ), $policy->validate( 'abcdEFGH' ) );
        $this->assertSame( array(), $policy->validate( 'abcdEFG1' ) );
        $this->assertSame( 4, expPasswordPolicy::characterClasses( 'aB3!' ) );
        $this->assertSame( 1, expPasswordPolicy::characterClasses( "\xff\xfe" ), 'a string that is not UTF-8 does not break the check' );
        $this->setIni( 'PasswordSettings', 'MinCharacterClasses', '9' );
        $this->assertSame( 4, $policy->minCharacterClasses(), 'at most four' );
        foreach ( $policy->rules() as $rule )
            $this->assertNotSame( '', $policy->errorText( $rule['id'] ) );
    }

    public function testRulesThatReadTheUser()
    {
        $this->setIni( 'PasswordSettings', 'ForbidLogin', 'enabled' );
        $this->setIni( 'PasswordSettings', 'ForbidCurrentPassword', 'enabled' );
        $policy = new expPasswordPolicy();
        $user = $this->user();
        $this->assertSame( array( 'not_login' ), $policy->validate( 'my-' . strtoupper( self::LOGIN ) . '-x', $user ) );
        $this->assertSame( array( 'not_current' ), $policy->validate( self::$password, $user ) );
        $this->assertSame( array(), $policy->validate( 'a-completely-new-one', $user ) );
        $this->assertSame( array(), $policy->validate( 'a-completely-new-one' ), 'without a user these rules pass' );
    }

    public function testClientConfig()
    {
        $policy = new expPasswordPolicy();
        $config = $policy->clientConfig( $this->user() );
        $this->assertSame( 10, $config['minLength'] );
        $this->assertSame( array( array( 'id' => 'length', 'min' => 10, 'text' => 'At least 10 characters' ) ), $config['rules'] );
        $this->assertSame( array( 'current' => 'password-old', 'new' => 'password-new', 'confirm' => 'password-confirm' ), $config['fields'] );
        foreach ( array( 'show', 'hide', 'showPassword', 'hidePassword', 'match', 'noMatch', 'strength', 'strength0', 'strength4', 'generated' ) as $k )
            $this->assertNotSame( '', (string)$config['i18n'][$k], $k );
        $this->assertGreaterThanOrEqual( 12, $config['generateLength'] );
        $this->assertArrayNotHasKey( 'login', $config, 'the login only when ForbidLogin needs it' );
        $this->setIni( 'PasswordSettings', 'ForbidLogin', 'enabled' );
        $this->assertSame( self::LOGIN, $policy->clientConfig( $this->user() )['login'] );
        $this->assertNotFalse( json_encode( $config ) );
    }

    // ------------------------------------------------------------------ PWD-05 .. PWD-07: the view

    public function testViewWrongCurrentPassword()
    {
        $this->audit();
        $tpl = $this->runView( array( 'OKButton' => 'x', 'oldPassword' => 'wrong-one', 'newPassword' => 'short', 'confirmPassword' => 'other' ) );
        $this->assertSame( 1, $tpl->variable( 'oldPasswordNotValid' ) );
        $this->assertSame( 0, $tpl->variable( 'newPasswordNotMatch' ), 'the old password is checked first, as before' );
        $this->assertTrue( $tpl->variable( 'message' ) );
        $this->assertFalse( $tpl->variable( 'password_changed' ) );
        $errors = $tpl->variable( 'field_errors' );
        $this->assertSame( array( 'oldPassword', 'newPassword', 'confirmPassword' ), array_keys( $errors ) );
        $this->assertCount( 1, $errors['oldPassword'] );
        $this->assertSame( array(), $errors['newPassword'] );
        $summary = $tpl->variable( 'error_summary' );
        $this->assertSame( 'password-old', $summary[0]['field_id'] );
        $this->assertTrue( $tpl->variable( 'has_errors' ) );
        $this->assertTrue( expPasswordPolicy::isCurrentPassword( $this->user(), self::$password ), 'nothing changed' );
        $failed = array_values( array_filter( $this->auditRecords(), function ( $r ) { return $r['name'] === 'access.user.password.change.failed'; } ) );
        $this->assertCount( 1, $failed );
        $this->assertStringNotContainsString( 'wrong-one', json_encode( $failed ) );
    }

    public function testViewReportsEveryProblemOfTheNewPassword()
    {
        $this->audit();
        $tpl = $this->runView( array( 'OKButton' => 'x', 'oldPassword' => self::$password, 'newPassword' => 'short', 'confirmPassword' => 'other' ) );
        $this->assertSame( 0, $tpl->variable( 'oldPasswordNotValid' ) );
        $this->assertSame( 1, $tpl->variable( 'newPasswordNotMatch' ) );
        $this->assertSame( 1, $tpl->variable( 'newPasswordTooShort' ) );
        $errors = $tpl->variable( 'field_errors' );
        $this->assertCount( 1, $errors['newPassword'] );
        $this->assertCount( 1, $errors['confirmPassword'] );
        $this->assertSame( array( 'length' ), $tpl->variable( 'failed_rules' ) );
        $rules = $tpl->variable( 'password_rules' );
        $this->assertTrue( $rules[0]['failed'] );
        $this->assertSame( array( 'password-new', 'password-confirm' ), array_column( $tpl->variable( 'error_summary' ), 'field_id' ) );
        $this->assertSame( '', $tpl->variable( 'oldPassword' ), 'a typed password is never handed back' );
        $this->assertSame( '', $tpl->variable( 'newPassword' ) );
        $this->assertSame( '', $tpl->variable( 'confirmPassword' ) );
        $failed = array_values( array_filter( $this->auditRecords(), function ( $r ) { return $r['name'] === 'access.user.password.change.failed'; } ) );
        $this->assertCount( 1, $failed );
        $this->assertSame( 'confirmation', $failed[0]['after']['rule'] );
        $this->assertSame( array(), $this->mails() );
    }

    public function testOptionalRuleWithAnOldTemplateShowsNoSuccess()
    {
        $this->setIni( 'PasswordSettings', 'RequireDigit', 'enabled' );
        $tpl = $this->runView( array( 'OKButton' => 'x', 'oldPassword' => self::$password, 'newPassword' => 'no-digits-here', 'confirmPassword' => 'no-digits-here' ) );
        $this->assertSame( array( 'digit' ), $tpl->variable( 'failed_rules' ) );
        $this->assertSame( 0, $tpl->variable( 'newPasswordTooShort' ) );
        $this->assertSame( 0, $tpl->variable( 'newPasswordNotMatch' ) );
        $this->assertSame( 0, $tpl->variable( 'message' ), 'an old template shows nothing rather than its success message' );
        $this->assertFalse( $tpl->variable( 'password_changed' ) );
        $this->assertTrue( expPasswordPolicy::isCurrentPassword( $this->user(), self::$password ) );
    }

    public function testViewChangesThePasswordEndsOtherSessionsAndMails()
    {
        $this->audit();
        self::removeSessions();
        $this->addSession( 'p1pwd-a', self::$userID );
        $this->addSession( 'p1pwd-b', self::$userID );
        $this->addSession( 'p1pwd-other', self::OTHER_USER_ID );
        $new = 'Changed-' . bin2hex( random_bytes( 8 ) );
        $tpl = $this->runView( array( 'OKButton' => 'x', 'oldPassword' => self::$password, 'newPassword' => $new, 'confirmPassword' => $new ) );
        $changed = $tpl->variable( 'password_changed' );
        if ( $changed )
            self::$password = $new;
        $this->assertTrue( $changed );
        $this->assertTrue( $tpl->variable( 'message' ) );
        $this->assertSame( 0, $tpl->variable( 'oldPasswordNotValid' ) + $tpl->variable( 'newPasswordNotMatch' ) + $tpl->variable( 'newPasswordTooShort' ) );
        $this->assertFalse( $tpl->variable( 'has_errors' ) );
        $this->assertTrue( expPasswordPolicy::isCurrentPassword( $this->user(), $new ) );
        $this->assertSame( '', $tpl->variable( 'newPassword' ) );
        $this->assertStringNotContainsString( $new, (string)$tpl->variable( 'password_js_config_json' ) );

        if ( eZSession::getHandlerInstance() instanceof ezpSessionHandlerDB )
        {
            $this->assertSame( 2, $tpl->variable( 'sessions_ended' ), 'no session of this process to keep: both test sessions end' );
            $this->assertSame( array( 'p1pwd-other' ), $this->sessionKeys(), 'another user keeps the session' );
        }
        else
            $this->assertNull( $tpl->variable( 'sessions_ended' ) );

        $this->assertTrue( $tpl->variable( 'notification_sent' ) );
        $mails = $this->mails();
        $this->assertCount( 1, $mails );
        $this->assertStringContainsString( self::EMAIL, $mails[0] );
        $this->assertMatchesRegularExpression( '/^X-Exp-Mail-Category: security/mi', $mails[0] );
        $this->assertStringNotContainsString( $new, $mails[0], 'the mail never holds the password' );
        $this->assertStringNotContainsString( base64_encode( $new ), $mails[0] );

        $names = array_column( $this->auditRecords(), 'name' );
        $this->assertContains( 'access.user.password.change', $names );
        if ( eZSession::getHandlerInstance() instanceof ezpSessionHandlerDB )
            $this->assertContains( 'access.session.revoke', $names );
        $this->assertStringNotContainsString( $new, json_encode( $this->auditRecords() ) );
    }

    // ------------------------------------------------------------------ PWD-08 .. PWD-11: sessions and mail

    public function testDatabaseHandlerEndsOtherSessionsOfOneUser()
    {
        self::removeSessions();
        $this->addSession( 'p1pwd-keep', self::$userID );
        $this->addSession( 'p1pwd-x', self::$userID );
        $this->addSession( 'p1pwd-other', self::OTHER_USER_ID );
        $handler = new ezpSessionHandlerDB( false );
        $this->assertSame( 1, $handler->deleteOtherSessionsOfUser( self::$userID, 'p1pwd-keep' ) );
        $this->assertSame( array( 'p1pwd-keep', 'p1pwd-other' ), $this->sessionKeys() );
        $this->assertSame( 0, $handler->deleteOtherSessionsOfUser( 0, '' ), 'no user, nothing' );
        $this->assertSame( 1, $handler->deleteOtherSessionsOfUser( self::$userID, '' ), "'' keeps none" );
        $this->assertSame( array( 'p1pwd-other' ), $this->sessionKeys() );
        self::removeSessions();
    }

    public function testOtherSessionsEndAndTheKeptOneStays()
    {
        if ( !eZSession::getHandlerInstance() instanceof ezpSessionHandlerDB )
            $this->markTestSkipped( 'the installation does not use the database session handler' );
        self::removeSessions();
        $this->addSession( 'p1pwd-keep', self::$userID );
        $this->addSession( 'p1pwd-x', self::$userID );
        $this->addSession( 'p1pwd-y', self::$userID );
        $this->addSession( 'p1pwd-other', self::OTHER_USER_ID );
        $this->assertSame( 2, ( new expPasswordPolicy() )->endOtherSessions( self::$userID, 'p1pwd-keep' ) );
        $this->assertSame( array( 'p1pwd-keep', 'p1pwd-other' ), $this->sessionKeys() );
        $this->setIni( 'PasswordSettings', 'EndOtherSessions', 'disabled' );
        $this->assertNull( ( new expPasswordPolicy() )->endOtherSessions( self::$userID, 'p1pwd-keep' ) );
        self::removeSessions();
    }

    public function testHandlerWithoutBackendCannot()
    {
        $handler = new ezpSessionHandlerPHP( false );
        $this->assertNull( $handler->deleteOtherSessionsOfUser( self::$userID, 'x' ) );
    }

    public function testChangedMail()
    {
        $policy = new expPasswordPolicy();
        $this->assertTrue( $policy->sendChangedMail( $this->user(), 3 ) );
        $mails = $this->mails();
        $this->assertCount( 1, $mails );
        $this->assertStringContainsString( 'To: ' . self::EMAIL, $mails[0] );
        $this->assertMatchesRegularExpression( '/^X-Exp-Mail-Category: security/mi', $mails[0] );
        $this->assertStringContainsString( self::LOGIN, $mails[0] );
        $this->assertStringNotContainsString( self::$password, $mails[0] );

        foreach ( glob( self::$installation . '/' . self::$mailDir . '/*' ) ?: array() as $f )
            @unlink( $f );
        $this->setIni( 'PasswordSettings', 'ChangeNotificationMail', 'disabled' );
        $this->assertFalse( $policy->sendChangedMail( $this->user() ) );
        $this->assertSame( array(), $this->mails() );
        $this->assertFalse( $policy->sendChangedMail( null ) );
    }
}
