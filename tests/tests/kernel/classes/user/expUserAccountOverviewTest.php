<?php
/**
 * The account page of user/edit (expUserAccountOverview), from facts built by hand: no database.
 *
 *  UAO-01 - The state: disabled before locked before active
 *  UAO-02 - The hints, the most pressing first: state, failed sign-ins, two-step sign-in off, no or an old password
 *           hash, a password not changed for a year, never signed in; none for a well kept account
 *  UAO-03 - Own account: every card the viewer may open, in page order, two-step sign-in and API keys with status
 *           (API keys without a count while there are none)
 *  UAO-04 - Another user's account: no bookmarks, notifications or own setup pages; two-step sign-in goes to the
 *           profile, API keys and e-mail to the administration pages of that user
 *  UAO-05 - Cards the viewer may not open are left out; profile is always there
 *  UAO-06 - The account: ages counted to now, false where unknown, groups and roles as given
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUserAccountOverviewTest extends PHPUnit\Framework\TestCase
{
    const NOW = 1790000000;

    private static function allAccess( $own = true )
    {
        return array( 'password' => true, 'settings' => true, 'roles' => true, 'twofactor_setup' => $own,
                      'apikeys_own' => $own, 'apikeys_admin' => true, 'bookmarks' => $own, 'notifications' => $own,
                      'mail_own' => $own, 'mail_admin' => true );
    }

    private function overview( array $facts = array() )
    {
        return new expUserAccountOverview( $facts + array(
            'user_id' => 42, 'login' => 'sample', 'email' => 'sample@example.invalid', 'name' => 'Sample User', 'node_id' => 50,
            'own' => true, 'login_count' => 3, 'last_visit' => self::NOW - 3600, 'hash_type' => eZUser::PASSWORD_HASH_PHP_DEFAULT,
            'access' => self::allAccess(),
        ), self::NOW );
    }

    private static function keys( array $list )
    {
        return array_map( function ( $x ) { return $x['key']; }, $list );
    }

    /** UAO-01 */
    public function testState()
    {
        $this->assertSame( array( 'key' => 'active', 'level' => 'ok' ), $this->overview()->state() );
        $this->assertSame( 'locked', $this->overview( array( 'locked' => true ) )->state()['key'] );
        $this->assertSame( 'disabled', $this->overview( array( 'locked' => true, 'enabled' => false ) )->state()['key'] );
        $this->assertSame( 'bad', $this->overview( array( 'enabled' => false ) )->state()['level'] );
    }

    /** UAO-02 */
    public function testHints()
    {
        $this->assertSame( array(), $this->overview()->hints() );

        $hints = $this->overview( array( 'enabled' => false, 'failed_attempts' => 2, 'twofactor' => array( 'active' => false, 'method' => 'disabled' ),
            'hash_type' => eZUser::PASSWORD_HASH_MD5_USER, 'password_changed' => self::NOW - 400 * 86400, 'login_count' => 0,
            'last_visit' => null ) )->hints();
        $this->assertSame( array( 'disabled', 'twofactor_off', 'old_hash', 'password_old', 'never_signed_in' ), self::keys( $hints ) );
        $this->assertSame( 400, $hints[3]['days'] );

        $hints = $this->overview( array( 'failed_attempts' => 2, 'max_failed_attempts' => 5, 'hash_type' => eZUser::PASSWORD_HASH_EMPTY ) )->hints();
        $this->assertSame( array( 'failed_attempts', 'no_password' ), self::keys( $hints ) );
        $this->assertSame( array( 'warn', 2, 5 ), array( $hints[0]['level'], $hints[0]['count'], $hints[0]['max'] ) );

        // a password changed recently, bcrypt, two-step sign-in on: nothing to say
        $this->assertSame( array(), $this->overview( array( 'hash_type' => eZUser::PASSWORD_HASH_BCRYPT,
            'password_changed' => self::NOW - 10 * 86400, 'twofactor' => array( 'active' => true, 'method' => 'totp' ) ) )->hints() );
    }

    /** UAO-03 */
    public function testOwnAccountActions()
    {
        $actions = $this->overview( array( 'twofactor' => array( 'active' => true, 'method' => 'email' ),
            'apikeys' => array( 'active' => 2, 'total' => 3 ), 'can_create_apikeys' => true ) )->actions();
        $this->assertSame( expUserAccountOverview::ACTIONS, self::keys( $actions ) );
        $by = array_column( $actions, null, 'key' );
        $this->assertSame( array( 'post', 'EditButton', '' ), array( $by['profile']['how'], $by['profile']['button'], $by['profile']['url'] ) );
        $this->assertSame( 'ChangePasswordButton', $by['password']['button'] );
        $this->assertSame( 'ChangeSettingButton', $by['settings']['button'] );
        $this->assertSame( 'active', $by['settings']['status']['key'] );
        $this->assertSame( array( 'link', 'user2fa/setup', 'on_email', 'ok' ), array( $by['twofactor']['how'], $by['twofactor']['url'],
            $by['twofactor']['status']['key'], $by['twofactor']['status']['level'] ) );
        $this->assertSame( array( 'apikey/list', 2, 3 ), array( $by['apikeys']['url'], $by['apikeys']['status']['active'], $by['apikeys']['status']['total'] ) );
        $this->assertSame( 'content/bookmark', $by['bookmarks']['url'] );
        $this->assertSame( 'notification/settings', $by['notifications']['url'] );
        $this->assertSame( 'mailpreferences/settings', $by['mail']['url'] );
        $this->assertFalse( $by['bookmarks']['status'] );

        // no keys yet, but the user may make them: the card without a count
        $by = array_column( $this->overview( array( 'apikeys' => array( 'active' => 0, 'total' => 0 ), 'can_create_apikeys' => true ) )->actions(), null, 'key' );
        $this->assertFalse( $by['apikeys']['status'] );

        // without the two-step field, without API keys to show: no such cards
        $this->assertSame( array( 'profile', 'password', 'settings', 'bookmarks', 'notifications', 'mail' ), self::keys( $this->overview()->actions() ) );
    }

    /** UAO-04 */
    public function testOtherUsersActions()
    {
        $actions = $this->overview( array( 'own' => false, 'access' => self::allAccess( false ),
            'twofactor' => array( 'active' => false, 'method' => 'disabled' ), 'apikeys' => array( 'active' => 0, 'total' => 1 ) ) )->actions();
        $this->assertSame( array( 'profile', 'password', 'settings', 'twofactor', 'apikeys', 'mail' ), self::keys( $actions ) );
        $by = array_column( $actions, null, 'key' );
        $this->assertSame( array( 'post', 'EditButton', 'off', 'warn' ), array( $by['twofactor']['how'], $by['twofactor']['button'],
            $by['twofactor']['status']['key'], $by['twofactor']['status']['level'] ) );
        $this->assertSame( 'oauthadmin/keys/(user)/42', $by['apikeys']['url'] );
        $this->assertSame( 'none', $by['apikeys']['status']['level'] );
        $this->assertSame( 'mailpreferences/admin/user/42', $by['mail']['url'] );

        // an administrator without oauthadmin sees no API key card for another user
        $access = self::allAccess( false );
        $access['apikeys_admin'] = false;
        $this->assertNotContains( 'apikeys', self::keys( $this->overview( array( 'own' => false, 'access' => $access,
            'apikeys' => array( 'active' => 1, 'total' => 1 ) ) )->actions() ) );
    }

    /** UAO-05 */
    public function testActionsNeedAccess()
    {
        $this->assertSame( array( 'profile' ), self::keys( $this->overview( array( 'access' => array() ) )->actions() ) );
        $access = self::allAccess();
        $access['twofactor_setup'] = false;
        $this->assertNotContains( 'twofactor', self::keys( $this->overview( array( 'access' => $access,
            'twofactor' => array( 'active' => false, 'method' => 'disabled' ) ) )->actions() ) );
    }

    /** UAO-06 */
    public function testAccount()
    {
        $groups = array( array( 'name' => 'Editors', 'node_id' => 13 ) );
        $roles = array( array( 'id' => 3, 'name' => 'Editor' ) );
        $a = $this->overview( array( 'groups' => $groups, 'roles' => $roles, 'password_changed' => self::NOW - 3 * 86400 - 5 ) )->account();
        $this->assertSame( array( 42, 'sample', 50, 3, self::NOW - 3600, 3 ), array( $a['user_id'], $a['login'], $a['node_id'], $a['login_count'],
            $a['last_visit'], $a['password_age_days'] ) );
        $this->assertSame( $groups, $a['groups'] );
        $this->assertSame( $roles, $a['roles'] );

        $a = $this->overview( array( 'last_visit' => null, 'login_count' => 0 ) )->account();
        $this->assertFalse( $a['last_visit'] );
        $this->assertFalse( $a['password_changed'] );
        $this->assertFalse( $a['password_age_days'] );

        $all = $this->overview( array( 'access' => array( 'roles' => true ) ) )->toArray();
        $this->assertSame( array( 'account', 'state', 'hints', 'actions', 'can_view_roles' ), array_keys( $all ) );
        $this->assertTrue( $all['can_view_roles'] );
    }
}
