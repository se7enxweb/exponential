<?php
/**
 * eZUser::isEnabled() on the installation the tests run on. Live style: no test database. The test creates
 * its own user (never published, address at x1.example.invalid) and removes it again; where there is no
 * installation (CI) the test is skipped.
 *
 *  UE-01 - an account without an ezuser_setting row is not enabled: loginUser() refuses it, and isEnabled() said
 *          true, so currentUser(), eZUser::accessUser() and the REST filters let its session or token through
 *  UE-02 - with a row, is_enabled decides, as before
 *  UE-03 - eZUserOperationCollection::setSettings() creates the missing row, so enabling such an account (user
 *          settings, the hygiene page, reactivation) repairs it; it did nothing before
 *
 * Run: php vendor/bin/phpunit --group database tests/tests/kernel/datatypes/ezuser/eZUserIsEnabledWithoutSettingLiveTest.php
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

#[\PHPUnit\Framework\Attributes\Group('database')]
class eZUserIsEnabledWithoutSettingLiveTest extends PHPUnit\Framework\TestCase
{
    const ADDRESS_DOMAIN = 'x1.example.invalid';
    const ADMIN_ID = 14;

    private static $installation;
    private $userID = 0;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 5 );
        ezpLiveInstallation::requireOrSkip();
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
    }

    protected function tearDown(): void
    {
        if ( $this->userID )
        {
            $object = eZContentObject::fetch( $this->userID );
            if ( $object instanceof eZContentObject )
            {
                $object->purge();
            }
            eZUser::removeUser( $this->userID );
            eZUserSetting::removeByUserID( $this->userID );
            eZUser::purgeUserCacheByUserId( $this->userID );
        }
    }

    private function createUser()
    {
        $login = 'x1-enabled-' . bin2hex( random_bytes( 4 ) );
        $object = eZContentClass::fetchByIdentifier( 'user' )->instantiate( self::ADMIN_ID );
        $this->userID = (int)$object->attribute( 'id' );
        $user = eZUser::fetch( $this->userID ) ?: eZUser::create( $this->userID );
        $user->setAttribute( 'login', $login );
        $user->setAttribute( 'email', $login . '@' . self::ADDRESS_DOMAIN );
        $user->setAttribute( 'password_hash', eZUser::createHash( $login, bin2hex( random_bytes( 12 ) ), eZUser::site(), eZUser::hashType() ) );
        $user->setAttribute( 'password_hash_type', eZUser::hashType() );
        $user->store();
        eZUserSetting::removeByUserID( $this->userID );
        eZUser::purgeUserCacheByUserId( $this->userID );
        return $this->userID;
    }

    /** UE-01 */
    public function testAnAccountWithoutSettingRowIsNotEnabled()
    {
        $id = $this->createUser();
        $this->assertNull( eZUserSetting::fetch( $id ), 'precondition: no ezuser_setting row' );

        $user = eZUser::fetch( $id );
        $this->assertFalse( $user->isEnabled( false ) );
        $this->assertFalse( $user->isEnabled(), 'also through the user cache' );
    }

    /** UE-02 */
    public function testWithSettingRowIsEnabledDecides()
    {
        $id = $this->createUser();
        $setting = eZUserSetting::create( $id, 1 );
        $setting->store();
        eZUser::purgeUserCacheByUserId( $id );
        $this->assertTrue( eZUser::fetch( $id )->isEnabled( false ) );

        $setting->setAttribute( 'is_enabled', 0 );
        $setting->store();
        eZUser::purgeUserCacheByUserId( $id );
        $this->assertFalse( eZUser::fetch( $id )->isEnabled( false ) );
    }

    /** UE-03 */
    public function testSetSettingsCreatesTheMissingRow()
    {
        $id = $this->createUser();
        $result = eZUserOperationCollection::setSettings( $id, 1, 0 );
        $this->assertTrue( $result['status'] );
        $setting = eZUserSetting::fetch( $id );
        $this->assertInstanceOf( 'eZUserSetting', $setting );
        $this->assertSame( 1, (int)$setting->attribute( 'is_enabled' ) );
        eZUser::purgeUserCacheByUserId( $id );
        $this->assertTrue( eZUser::fetch( $id )->isEnabled( false ) );

        $this->assertFalse( eZUserOperationCollection::setSettings( 987654321, 1, 0 )['status'], 'no account, no row' );
        $this->assertNull( eZUserSetting::fetch( 987654321 ) );
    }
}
