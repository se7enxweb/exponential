<?php
/**
 * Changing a password through the user operation (eZUserOperationCollection::password(), which the user/password
 * view and the REST API use) updates the stored account and the account details kept in the user_account attribute,
 * so an edit opened afterwards does not carry the old hash back (EZP-29096).
 *
 *  PW-01 - the stored password hash changes, and it is the hash of the new password
 *  PW-02 - the user_account attribute's data_text holds the account as serializeDraft() writes it, with the new hash
 *  PW-03 - an id without an account is refused (status false), nothing is changed
 *
 * LoveStack's test checks PW-01 (commit 60a40cdc05, testUpdatePasswordUpdatesSerializedData); Exponential has the
 * same synchronisation (updateUserDraft()). Live style: on the installation the tests run on, in a transaction
 * that is rolled back; the test's own user (never published, address at x1.example.invalid) never reaches the
 * database for good. View and template-block caches are switched off in memory for the test, so no cache of the
 * site is expired. Where there is no installation (CI) the test is skipped.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 60a40cdc05), Copyright (C) Mugo Web, GPL v2
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/datatypes/ezuser/eZUserTypePasswordDraftLiveTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

#[\PHPUnit\Framework\Attributes\Group('database')]
class eZUserTypePasswordDraftLiveTest extends PHPUnit\Framework\TestCase
{
    const ADMIN_ID = 14;

    /** @var array[] array( group, name, had, value ) of site.ini, put back in tearDown() */
    private $saved = array();

    /** @var bool a transaction of the test is open */
    private $inTransaction = false;

    public static function setUpBeforeClass(): void
    {
        ezpLiveInstallation::requireOrSkip();
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->setting( 'ContentSettings', 'ViewCaching', 'disabled' );
        $this->setting( 'TemplateSettings', 'TemplateCache', 'disabled' );
        $this->setting( 'SSLZoneSettings', 'SSLZones', 'disabled' );
        eZDB::instance()->begin();
        $this->inTransaction = true;
    }

    protected function tearDown(): void
    {
        if ( $this->inTransaction )
        {
            eZDB::instance()->rollback();
            $this->inTransaction = false;
        }
        eZContentObject::clearCache();
        $ini = eZINI::instance();
        foreach ( array_reverse( $this->saved ) as $entry )
        {
            list( $group, $name, $had, $value ) = $entry;
            if ( $had )
                $ini->setVariable( $group, $name, $value );
            else
                $ini->removeSetting( $group, $name );
        }
        $this->saved = array();
    }

    private function setting( $group, $name, $value )
    {
        $ini = eZINI::instance();
        $had = $ini->hasVariable( $group, $name );
        $this->saved[] = array( $group, $name, $had, $had ? $ini->variable( $group, $name ) : null );
        $ini->setVariable( $group, $name, $value );
    }

    /** A user object of the test (draft, never published) with a stored account; its id */
    private function user()
    {
        $object = eZContentClass::fetchByIdentifier( 'user' )->instantiate( self::ADMIN_ID );
        $id = (int)$object->attribute( 'id' );
        $login = 'x1-password-' . bin2hex( random_bytes( 4 ) );
        $user = eZUser::create( $id );
        $user->setInformation( $id, $login, $login . '@x1.example.invalid', 'x1-old-password-1', 'x1-old-password-1' );
        $user->store();
        return $id;
    }

    private static function userAccountDataText( $id )
    {
        eZContentObject::clearCache( array( $id ) );
        foreach ( eZContentObject::fetch( $id )->dataMap() as $attribute )
        {
            if ( $attribute->attribute( 'data_type_string' ) === 'ezuser' )
                return $attribute->attribute( 'data_text' );
        }
        return null;
    }

    /** PW-01, PW-02 */
    public function testThePasswordOperationUpdatesTheAccountAndItsDraftData()
    {
        $id = $this->user();
        $before = eZUser::fetch( $id );
        $oldHash = $before->attribute( 'password_hash' );

        $result = eZUserOperationCollection::password( $id, 'x1-new-password-2' );
        $this->assertSame( array( 'status' => true ), $result );

        $after = eZUser::fetch( $id );
        $newHash = $after->attribute( 'password_hash' );
        $this->assertNotSame( $oldHash, $newHash, 'the stored hash changed' );
        $this->assertTrue( eZUser::authenticateHash( $after->attribute( 'login' ), 'x1-new-password-2', $after->site(),
                                                     (int)$after->attribute( 'password_hash_type' ), $newHash ), 'the hash of the new password' );

        $draft = json_decode( (string)self::userAccountDataText( $id ), true );
        $this->assertIsArray( $draft, 'the account details are kept in data_text' );
        $this->assertSame( $newHash, $draft['password_hash'] );
        $this->assertSame( $after->attribute( 'login' ), $draft['login'] );
        $this->assertSame( json_decode( eZUserType::serializeDraft( $after ), true ), $draft );
    }

    /** PW-03 */
    public function testAnIdWithoutAnAccountIsRefused()
    {
        $missing = (int)eZDB::instance()->arrayQuery( 'SELECT MAX(contentobject_id) AS m FROM ezuser' )[0]['m'] + 1000;
        $this->assertNull( eZUser::fetch( $missing ) );
        $this->assertSame( array( 'status' => false ), eZUserOperationCollection::password( $missing, 'x1-never-3' ) );
        $this->assertNull( eZUser::fetch( $missing ) );
    }
}
