<?php
/**
 * Ending the other sessions of a user after a password change, whatever the session handler
 * (doc/features/6.0/modern-password-change.md, "Other sessions"): the password stamp a sign-in keeps in the
 * session, its check in eZUser::currentUser(), and the session that changes its own password staying signed in.
 *
 * Live style: one test user (remote id and login p1stamp-user, address on p1stamp.invalid), created and removed
 * again; skipped without an installation. A session is simulated in the process (eZSession's started flag and
 * $_SESSION), so no cookie, no session file and no session row is made. The site secret is a fixed test value
 * (expMailSecret::setForTest), never the installation's. No mail is sent: [PasswordSettings]
 * ChangeNotificationMail is off in the process and the transport is the file transport.
 *
 *  STAMP-01  A stamp: "<id>:<32 characters>", stable, changes with the password, never holds the hash
 *  STAMP-02  Nothing to stamp: no user, the anonymous user, an account without a password of its own
 *  STAMP-03  compareStamp(): match, mismatch, legacy (no stamp, another user's), skip (setting off, anonymous,
 *            no password of its own)
 *  STAMP-04  A sign-in stamps the session; a temporary switch does not; a sign-out removes the stamp
 *  STAMP-05  The session that changes its own password keeps going, in the same request too
 *  STAMP-06  Another session signed in with the old password ends on its next currentUser(), audited
 *  STAMP-07  A session from before the upgrade (no stamp) keeps going
 *  STAMP-08  EndOtherSessions=disabled switches the check off
 *  STAMP-09  An account without a password of its own (LDAP, SSO) is never ended by the check
 *  STAMP-10  An outdated user cache (OPcache) does not end the session signed in with the stored password
 */

require_once dirname( __DIR__ ) . '/audit/fixtures/expaudittestfixtures.php';

class expPasswordSessionStampTest extends PHPUnit\Framework\TestCase
{
    const LOGIN = 'p1stamp-user';
    const EMAIL = 'p1stamp-user@p1stamp.invalid';

    private static $installation;
    private static $userID = 0;
    private static $admin;
    private $iniBackup = array();
    private $sessionBackup = null;
    private $auditDir = null;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 5 );
        chdir( self::$installation );
        ezpLiveInstallation::requireOrSkip();
        $ini = eZINI::instance();
        $ini->setVariable( 'MailSettings', 'Transport', 'file' );
        $ini->setVariable( 'MailSettings', 'FileTransportDirectory', 'var/tmp/password-stamp-test-mail' );
        if ( trim( $ini->variable( 'MailSettings', 'Transport' ) ) !== 'file' )
            throw new RuntimeException( 'The mail transport is not the file transport: the test refuses to run.' );
        self::$admin = eZUser::fetchByName( 'admin' );
        if ( !self::$admin )
            self::markTestSkipped( 'needs the admin user' );
        eZUser::setCurrentlyLoggedInUser( self::$admin, self::$admin->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        self::removeUser();
        $password = 'Stamp-' . bin2hex( random_bytes( 8 ) );
        $type = eZUser::hashType();
        $hash = eZUser::createHash( self::LOGIN, $password, eZUser::site(), $type );
        $object = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => 5, 'class_identifier' => 'user', 'creator_id' => self::$admin->attribute( 'contentobject_id' ),
            'remote_id' => self::LOGIN, 'attributes' => array( 'first_name' => 'P1STAMP', 'last_name' => 'User',
                'user_account' => self::LOGIN . '|' . self::EMAIL . '|' . $hash . '|' . eZUser::passwordHashTypeName( $type ) . '|1' ) ) );
        if ( !$object )
            throw new RuntimeException( 'P1STAMP: the test user could not be created' );
        self::$userID = (int)$object->attribute( 'id' );
        eZUser::cleanupCache();
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$installation === null )
            return;
        chdir( self::$installation );
        self::removeUser();
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        expMailSecret::setForTest( null, str_repeat( "\x5a", 32 ) );
        $this->setIni( 'PasswordSettings', 'EndOtherSessions', 'enabled' );
        $this->setIni( 'PasswordSettings', 'ChangeNotificationMail', 'disabled' );
    }

    protected function tearDown(): void
    {
        $this->endSession();
        foreach ( $this->iniBackup as $key => $value )
        {
            list( $block, $var ) = explode( '/', $key );
            if ( $value === null )
                eZINI::instance()->removeSetting( $block, $var );
            else
                eZINI::instance()->setVariable( $block, $var, $value );
        }
        $this->iniBackup = array();
        expMailSecret::setForTest( null, null );
        expMailSecret::reset();
        if ( $this->auditDir !== null )
        {
            expAuditHook::reset();
            expAuditTestFixtures::tearDown();
            $this->auditDir = null;
        }
        unset( $GLOBALS['eZUserGlobalInstance_'], $GLOBALS['eZUserGlobalInstance_' . self::$userID] );
        if ( self::$admin )
            eZUser::setCurrentlyLoggedInUser( self::$admin, self::$admin->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
    }

    // ------------------------------------------------------------------ helpers

    private static function removeUser()
    {
        $object = eZContentObject::fetchByRemoteID( self::LOGIN );
        if ( $object )
            $object->purge();
    }

    private function setIni( $block, $var, $value )
    {
        $ini = eZINI::instance();
        $key = $block . '/' . $var;
        if ( !array_key_exists( $key, $this->iniBackup ) )
            $this->iniBackup[$key] = $ini->hasVariable( $block, $var ) ? $ini->variable( $block, $var ) : null;
        $ini->setVariable( $block, $var, $value );
    }

    /** A session in this process: eZSession says it has started, $_SESSION holds its data */
    private function startSession( array $data = array() )
    {
        if ( $this->sessionBackup === null )
            $this->sessionBackup = array( 'session' => isset( $_SESSION ) ? $_SESSION : null );
        $p = new ReflectionProperty( 'eZSession', 'hasStarted' );
        $p->setAccessible( true );
        $p->setValue( null, true );
        $_SESSION = $data;
    }

    private function endSession()
    {
        $p = new ReflectionProperty( 'eZSession', 'hasStarted' );
        $p->setAccessible( true );
        $p->setValue( null, false );
        if ( $this->sessionBackup !== null )
        {
            if ( $this->sessionBackup['session'] === null )
                unset( $_SESSION );
            else
                $_SESSION = $this->sessionBackup['session'];
            $this->sessionBackup = null;
        }
    }

    private function user()
    {
        return eZUser::fetch( self::$userID );
    }

    /** The test user as eZUser::instance() finds it at the start of a request: from the user cache */
    private function freshRequest()
    {
        unset( $GLOBALS['eZUserGlobalInstance_'], $GLOBALS['eZUserGlobalInstance_' . self::$userID] );
        eZUser::purgeUserCacheByUserId( self::$userID );
    }

    private function changePassword( $password )
    {
        eZUserOperationCollection::password( self::$userID, $password );
    }

    // ------------------------------------------------------------------ STAMP-01 .. STAMP-03

    public function testStampFormat()
    {
        $user = $this->user();
        $stamp = expPasswordPolicy::stampOf( $user );
        $this->assertMatchesRegularExpression( '/^' . self::$userID . ':[A-Za-z0-9_-]{32}$/', $stamp );
        $this->assertSame( $stamp, expPasswordPolicy::stampOf( $this->user() ), 'stable' );
        $this->assertStringNotContainsString( substr( (string)$user->attribute( 'password_hash' ), 7, 20 ), $stamp, 'never the hash' );
        $other = expPasswordPolicy::stampFor( self::$userID, $user->attribute( 'password_hash' ) . 'x', $user->attribute( 'password_hash_type' ) );
        $this->assertNotSame( $stamp, $other, 'another hash, another stamp' );
        expMailSecret::setForTest( null, str_repeat( "\x33", 32 ) );
        $this->assertNotSame( $stamp, expPasswordPolicy::stampOf( $user ), 'keyed with the site secret' );
    }

    public function testNothingToStamp()
    {
        $this->assertNull( expPasswordPolicy::stampFor( 0, 'x', 1 ) );
        $this->assertNull( expPasswordPolicy::stampFor( self::$userID, '', 1 ) );
        $this->assertNull( expPasswordPolicy::stampFor( self::$userID, 'abc', eZUser::PASSWORD_HASH_EMPTY ) );
        $this->assertNull( expPasswordPolicy::stampOf( eZUser::fetch( eZUser::anonymousId() ) ) );
        $this->assertNull( expPasswordPolicy::stampOf( null ) );
    }

    public function testCompareStamp()
    {
        $user = $this->user();
        $stamp = expPasswordPolicy::stampOf( $user );
        $this->assertSame( 'match', expPasswordPolicy::compareStamp( $user, $stamp ) );
        $this->assertSame( 'mismatch', expPasswordPolicy::compareStamp( $user, self::$userID . ':' . str_repeat( 'A', 32 ) ) );
        $this->assertSame( 'legacy', expPasswordPolicy::compareStamp( $user, null ) );
        $this->assertSame( 'legacy', expPasswordPolicy::compareStamp( $user, false ) );
        $this->assertSame( 'legacy', expPasswordPolicy::compareStamp( $user, '14:' . str_repeat( 'A', 32 ) ), "another user's stamp" );
        $this->assertSame( 'skip', expPasswordPolicy::compareStamp( eZUser::fetch( eZUser::anonymousId() ), $stamp ) );
        $external = new eZUser( array( 'contentobject_id' => self::$userID, 'login' => self::LOGIN, 'email' => self::EMAIL,
                                       'password_hash' => '', 'password_hash_type' => eZUser::PASSWORD_HASH_EMPTY ) );
        $this->assertSame( 'skip', expPasswordPolicy::compareStamp( $external, $stamp ), 'no password of its own' );
        $this->setIni( 'PasswordSettings', 'EndOtherSessions', 'disabled' );
        $this->assertSame( 'skip', expPasswordPolicy::compareStamp( $user, self::$userID . ':' . str_repeat( 'A', 32 ) ) );
    }

    // ------------------------------------------------------------------ STAMP-04 .. STAMP-09

    public function testSignInStampsAndSignOutRemoves()
    {
        $this->startSession();
        $user = $this->user();
        eZUser::setCurrentlyLoggedInUser( $user, self::$userID );
        $this->assertSame( expPasswordPolicy::stampOf( $user ), $_SESSION[expPasswordPolicy::SESSION_KEY] );
        $this->assertSame( self::$userID, (int)$_SESSION['eZUserLoggedInID'] );

        // a temporary switch (preview caches) leaves the stamp alone and is not checked against it
        eZUser::setCurrentlyLoggedInUser( self::$admin, self::$admin->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        $this->assertSame( expPasswordPolicy::stampOf( $user ), $_SESSION[expPasswordPolicy::SESSION_KEY] );
        eZUser::setCurrentlyLoggedInUser( $user, self::$userID, eZUser::NO_SESSION_REGENERATE );

        eZUser::logoutCurrent();
        $this->assertArrayNotHasKey( expPasswordPolicy::SESSION_KEY, $_SESSION );
    }

    public function testOwnSessionKeepsGoingOtherSessionEnds()
    {
        $this->auditDir = expAuditTestFixtures::setUp( $this->name(), array( 'AuditEventSettings/Enabled' => array( 'access.*' ) ) );
        // the other session: signed in with the password of now
        $this->startSession();
        eZUser::setCurrentlyLoggedInUser( $this->user(), self::$userID );
        $otherSession = $_SESSION;

        // this session: signed in too, changes the password; the same request goes on as the user
        $this->freshRequest();
        $this->startSession();
        eZUser::setCurrentlyLoggedInUser( $this->user(), self::$userID );
        $this->assertSame( self::$userID, (int)eZUser::currentUser()->attribute( 'contentobject_id' ) );
        $this->changePassword( 'New-' . bin2hex( random_bytes( 8 ) ) );
        $this->assertSame( self::$userID, (int)eZUser::currentUser()->attribute( 'contentobject_id' ), 'the same request keeps the user' );
        $this->assertSame( expPasswordPolicy::stampOf( $this->user() ), $_SESSION[expPasswordPolicy::SESSION_KEY], 'stamped with the new password' );
        $this->freshRequest();
        $this->assertSame( self::$userID, (int)eZUser::currentUser()->attribute( 'contentobject_id' ), 'and its next request' );

        // the other session's next request: signed out
        $this->freshRequest();
        $this->startSession( $otherSession );
        $current = eZUser::currentUser();
        $this->assertTrue( $current->isAnonymous(), 'the other session is signed out' );
        $this->assertSame( (int)eZUser::anonymousId(), (int)$_SESSION['eZUserLoggedInID'] );
        $this->assertArrayNotHasKey( expPasswordPolicy::SESSION_KEY, $_SESSION );

        expAudit::flush();
        $revoked = array_values( array_filter( expAuditTestFixtures::records( $this->auditDir, 'access' ), function ( $r ) {
            return $r['name'] === 'access.session.revoke';
        } ) );
        $this->assertCount( 1, $revoked );
        $this->assertSame( 'password_changed', $revoked[0]['reason'] );
    }

    public function testSessionFromBeforeTheUpgradeKeepsGoing()
    {
        $this->freshRequest();
        $this->startSession( array( 'eZUserLoggedInID' => self::$userID ) );
        $this->changePassword( 'Again-' . bin2hex( random_bytes( 8 ) ) );
        $this->freshRequest();
        $this->assertSame( self::$userID, (int)eZUser::currentUser()->attribute( 'contentobject_id' ) );
    }

    public function testSettingOffSwitchesTheCheckOff()
    {
        $this->setIni( 'PasswordSettings', 'EndOtherSessions', 'disabled' );
        $stale = self::$userID . ':' . str_repeat( 'A', 32 );
        $this->freshRequest();
        $this->startSession( array( 'eZUserLoggedInID' => self::$userID, expPasswordPolicy::SESSION_KEY => $stale ) );
        $this->assertSame( self::$userID, (int)eZUser::currentUser()->attribute( 'contentobject_id' ) );
        // and a sign-in with the check off stamps nothing
        eZUser::setCurrentlyLoggedInUser( $this->user(), self::$userID );
        $this->assertArrayNotHasKey( expPasswordPolicy::SESSION_KEY, $_SESSION );
    }

    public function testStaleUserCacheDoesNotEndTheSessionThatSignedInWithTheNewPassword()
    {
        // the user as an outdated user cache gives it (a server's OPcache keeps the included cache file a while):
        // the old hash, while the session was signed in with the password stored now
        $stored = $this->user();
        $stale = new eZUser( array( 'contentobject_id' => self::$userID, 'login' => self::LOGIN, 'email' => self::EMAIL,
                                    'password_hash' => $stored->attribute( 'password_hash' ) . 'old',
                                    'password_hash_type' => $stored->attribute( 'password_hash_type' ) ) );
        $this->startSession( array( 'eZUserLoggedInID' => self::$userID, expPasswordPolicy::SESSION_KEY => expPasswordPolicy::stampOf( $stored ) ) );
        $this->assertTrue( expPasswordPolicy::sessionIsCurrent( $stale ), 'the stored row decides on a mismatch' );
        $this->assertSame( $stored->attribute( 'password_hash' ), $stale->attribute( 'password_hash' ), 'and the object takes the stored hash' );
        // a session of an old password stays ended although the cache is outdated in the same way
        $stale->setAttribute( 'password_hash', $stored->attribute( 'password_hash' ) . 'old' );
        $_SESSION[expPasswordPolicy::SESSION_KEY] = expPasswordPolicy::stampFor( self::$userID, $stored->attribute( 'password_hash' ) . 'older', $stored->attribute( 'password_hash_type' ) );
        $this->assertFalse( expPasswordPolicy::sessionIsCurrent( $stale ) );
    }

    public function testAccountWithoutPasswordIsNeverEnded()
    {
        $external = new eZUser( array( 'contentobject_id' => self::$userID, 'login' => self::LOGIN, 'email' => self::EMAIL,
                                       'password_hash' => '', 'password_hash_type' => eZUser::PASSWORD_HASH_EMPTY ) );
        $this->startSession( array( 'eZUserLoggedInID' => self::$userID, expPasswordPolicy::SESSION_KEY => self::$userID . ':' . str_repeat( 'A', 32 ) ) );
        $this->assertTrue( expPasswordPolicy::sessionIsCurrent( $external ) );
        $this->assertFalse( expPasswordPolicy::stampSession( $external ), 'nothing to stamp' );
    }
}
