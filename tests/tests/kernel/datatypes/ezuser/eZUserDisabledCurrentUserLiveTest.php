<?php
/**
 * A disabled user in the session, on the installation the tests run on. Live style: no test database. The test
 * creates its own user (never published, address at x1.example.invalid), disables it and removes it again; where
 * there is no installation (CI) the test is skipped.
 *
 *  DU-01 - currentUser() logs a disabled user out once and answers with the anonymous user: the audit record of the
 *          logout asked currentUser() who acts, which logged out again, without end, until memory ran out
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

#[\PHPUnit\Framework\Attributes\Group('database')]
class eZUserDisabledCurrentUserLiveTest extends PHPUnit\Framework\TestCase
{
    const ADDRESS_DOMAIN = 'x1.example.invalid';
    const ADMIN_ID = 14;

    private static $installation;
    private $previousUser;
    private $userID = 0;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 5 );
        ezpLiveInstallation::requireOrSkip();
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        $this->previousUser = eZUser::currentUser();
    }

    protected function tearDown(): void
    {
        if ( $this->previousUser instanceof eZUser )
        {
            eZUser::setCurrentlyLoggedInUser( $this->previousUser, $this->previousUser->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        }
        if ( $this->userID )
        {
            $object = eZContentObject::fetch( $this->userID );
            if ( $object instanceof eZContentObject )
            {
                $object->purge();
            }
            eZUser::removeUser( $this->userID );
            eZUser::purgeUserCacheByUserId( $this->userID );
        }
    }

    /** DU-01 */
    public function testADisabledUserInTheSessionIsLoggedOutOnce()
    {
        $login = 'x1-disabled-' . bin2hex( random_bytes( 4 ) );
        $object = eZContentClass::fetchByIdentifier( 'user' )->instantiate( self::ADMIN_ID );
        $this->userID = (int)$object->attribute( 'id' );
        $user = eZUser::fetch( $this->userID ) ?: eZUser::create( $this->userID );
        $user->setAttribute( 'login', $login );
        $user->setAttribute( 'email', $login . '@' . self::ADDRESS_DOMAIN );
        $user->setAttribute( 'password_hash', eZUser::createHash( $login, bin2hex( random_bytes( 12 ) ), eZUser::site(), eZUser::hashType() ) );
        $user->setAttribute( 'password_hash_type', eZUser::hashType() );
        $user->store();
        $setting = eZUserSetting::fetch( $this->userID ) ?: eZUserSetting::create( $this->userID, 0 );
        $setting->setAttribute( 'is_enabled', 0 );
        $setting->store();
        eZUser::purgeUserCacheByUserId( $this->userID );

        $disabled = eZUser::fetch( $this->userID );
        $this->assertFalse( $disabled->isEnabled( false ) );
        eZUser::setCurrentlyLoggedInUser( $disabled, $this->userID, eZUser::NO_SESSION_REGENERATE );

        $current = eZUser::currentUser();
        $this->assertSame( (int)eZUser::anonymousId(), (int)$current->attribute( 'contentobject_id' ) );
        $this->assertSame( (int)eZUser::anonymousId(), (int)eZUser::currentUserID() );

        $recording = new ReflectionProperty( 'eZUser', 'recordingLogout' );
        $this->assertFalse( $recording->getValue(), 'nothing left over for the next request' );
    }
}
