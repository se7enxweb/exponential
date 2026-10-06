<?php
/**
 * File containing the expUserAccountOverview class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The account page of a user (user/edit, user/edit/<id>), described for a person: who the account is (name, login,
 * e-mail, groups, roles), its state (enabled, locked after failed sign-ins, never signed in), when it was last used,
 * the security hints that apply (two-step sign-in off, a password stored with an old method, a password not changed
 * for a long time, failed sign-ins) and the pages the viewer may go on to: the profile, the password, the account
 * settings, two-step sign-in, API keys, bookmarks, notifications and e-mail preferences.
 *
 * The overview only describes; every change stays with the page it links to. fromUser() reads this installation;
 * the constructor takes the facts built by hand, so the state, the hints and the actions are tested without a
 * database (tests/tests/kernel/classes/user/expUserAccountOverviewTest.php). The texts are the template's: the overview
 * hands it keys, numbers and addresses. Guide: doc/guides/security-and-audit.md, section 6
 */
class expUserAccountOverview
{
    /** A password not changed for this many days gets a hint */
    const PASSWORD_AGE_HINT_DAYS = 365;

    /** The cards, in the order the page shows them */
    const ACTIONS = array( 'profile', 'password', 'settings', 'twofactor', 'apikeys', 'bookmarks', 'notifications', 'mail' );

    /** @var array */
    protected $facts;

    /** @var int */
    protected $now;

    /**
     * @param array $facts see defaults()
     * @param int|null $now the time the ages are counted to
     */
    public function __construct( array $facts, $now = null )
    {
        $this->facts = $facts + self::defaults();
        $this->now = $now === null ? time() : (int)$now;
    }

    /**
     * The facts the overview is built from, with their defaults.
     *
     * own: the viewer looks at their own account. access: what the viewer may open (password, settings, roles,
     * twofactor_setup, apikeys_own, apikeys_admin, bookmarks, notifications, mail_own, mail_admin). twofactor:
     * null when the user class has no two-step field or the extension is not active, else array( active, method ).
     * apikeys: null when not readable, else the counts of the apikey module (active, total). password_changed: the
     * time of the last password change the audit index knows, else null. hash_type: eZUser::PASSWORD_HASH_*.
     *
     * @return array
     */
    public static function defaults()
    {
        return array(
            'user_id' => 0, 'login' => '', 'email' => '', 'name' => '', 'node_id' => 0, 'language' => '',
            'own' => false, 'enabled' => true, 'locked' => false, 'failed_attempts' => 0, 'max_failed_attempts' => 0,
            'login_count' => 0, 'last_visit' => null, 'hash_type' => eZUser::PASSWORD_HASH_PHP_DEFAULT,
            'password_changed' => null, 'groups' => array(), 'roles' => array(), 'twofactor' => null, 'apikeys' => null,
            'can_create_apikeys' => false, 'access' => array(),
        );
    }

    /**
     * The overview of an account as this installation has it.
     *
     * @param eZUser $account the account shown
     * @param eZUser $viewer the user looking at it
     * @return expUserAccountOverview
     */
    public static function fromUser( eZUser $account, eZUser $viewer )
    {
        $id = (int)$account->attribute( 'contentobject_id' );
        $object = $account->attribute( 'contentobject' );
        $own = $id === (int)$viewer->attribute( 'contentobject_id' );
        $loginCount = (int)$account->loginCount();

        $facts = array(
            'user_id' => $id,
            'login' => (string)$account->attribute( 'login' ),
            'email' => (string)$account->attribute( 'email' ),
            'name' => $object instanceof eZContentObject ? (string)$object->attribute( 'name' ) : '',
            'node_id' => $object instanceof eZContentObject ? (int)$object->attribute( 'main_node_id' ) : 0,
            'language' => $object instanceof eZContentObject ? (string)$object->attribute( 'initial_language_code' ) : '',
            'own' => $own,
            'enabled' => (bool)$account->isEnabled( false ),
            'locked' => (bool)$account->isLocked(),
            'failed_attempts' => (int)$account->failedLoginAttempts(),
            'max_failed_attempts' => (int)eZUser::maxNumberOfFailedLogin(),
            'login_count' => $loginCount,
            // eZUser::lastVisit() answers "now" for an account that never signed in
            'last_visit' => $loginCount > 0 ? (int)$account->lastVisit() : null,
            'hash_type' => (int)$account->attribute( 'password_hash_type' ),
            'password_changed' => self::passwordChanged( $id ),
            'groups' => self::groupsOf( $object ),
            'roles' => self::rolesOf( $account ),
            'twofactor' => self::twoFactorOf( $object ),
            'access' => self::accessOf( $viewer, $own ),
        );
        if ( class_exists( 'expApiKeyFunctionCollection' ) )
        {
            $counts = expApiKeyFunctionCollection::fetchCounts( $id );
            $facts['apikeys'] = is_array( $counts['result'] ) ? $counts['result'] : null;
            if ( $own )
            {
                $can = expApiKeyFunctionCollection::fetchCanCreate();
                $facts['can_create_apikeys'] = !empty( $can['result'] );
            }
        }
        return new self( $facts );
    }

    /**
     * The account: who it is, its groups and roles, its use.
     *
     * @return array
     */
    public function account()
    {
        $f = $this->facts;
        return array(
            'user_id' => (int)$f['user_id'], 'login' => $f['login'], 'email' => $f['email'], 'name' => $f['name'],
            'node_id' => (int)$f['node_id'], 'language' => $f['language'], 'own' => (bool)$f['own'],
            'groups' => array_values( $f['groups'] ), 'roles' => array_values( $f['roles'] ),
            'login_count' => (int)$f['login_count'], 'last_visit' => $f['last_visit'] === null ? false : (int)$f['last_visit'],
            'password_changed' => $f['password_changed'] === null ? false : (int)$f['password_changed'],
            'password_age_days' => $f['password_changed'] === null ? false : max( 0, (int)floor( ( $this->now - (int)$f['password_changed'] ) / 86400 ) ),
            'failed_attempts' => (int)$f['failed_attempts'], 'max_failed_attempts' => (int)$f['max_failed_attempts'],
        );
    }

    /**
     * The state of the account: disabled, locked (after too many failed sign-ins) or active, the worst first.
     *
     * @return array key (disabled, locked, active), level (bad, ok)
     */
    public function state()
    {
        if ( !$this->facts['enabled'] )
            return array( 'key' => 'disabled', 'level' => 'bad' );
        if ( $this->facts['locked'] )
            return array( 'key' => 'locked', 'level' => 'bad' );
        return array( 'key' => 'active', 'level' => 'ok' );
    }

    /**
     * The security hints that apply, the most pressing first. Each: key, level (bad, warn, info) and the
     * numbers its text needs.
     *
     * @return array[]
     */
    public function hints()
    {
        $f = $this->facts;
        $hints = array();
        $state = $this->state();
        if ( $state['key'] !== 'active' )
            $hints[] = array( 'key' => $state['key'], 'level' => 'bad' );
        elseif ( (int)$f['failed_attempts'] > 0 )
            $hints[] = array( 'key' => 'failed_attempts', 'level' => 'warn', 'count' => (int)$f['failed_attempts'],
                              'max' => (int)$f['max_failed_attempts'] );
        if ( is_array( $f['twofactor'] ) && empty( $f['twofactor']['active'] ) )
            $hints[] = array( 'key' => 'twofactor_off', 'level' => 'warn' );
        $hash = (int)$f['hash_type'];
        if ( $hash === eZUser::PASSWORD_HASH_EMPTY )
            $hints[] = array( 'key' => 'no_password', 'level' => 'warn' );
        elseif ( $hash < eZUser::PASSWORD_HASH_BCRYPT )
            $hints[] = array( 'key' => 'old_hash', 'level' => 'warn' );
        $account = $this->account();
        if ( $account['password_age_days'] !== false && $account['password_age_days'] >= self::PASSWORD_AGE_HINT_DAYS )
            $hints[] = array( 'key' => 'password_old', 'level' => 'info', 'days' => $account['password_age_days'] );
        if ( $account['last_visit'] === false )
            $hints[] = array( 'key' => 'never_signed_in', 'level' => 'info' );
        return $hints;
    }

    /**
     * The pages the viewer may go on to, in the order of ACTIONS, only those that are there for them. Each: key,
     * how (post: a button of the form, its name in button; link: an address in url, relative to the siteaccess),
     * and a status (key, level) where the card has one.
     *
     * @return array[]
     */
    public function actions()
    {
        $f = $this->facts;
        $a = $f['access'] + array_fill_keys( array( 'password', 'settings', 'roles', 'twofactor_setup', 'apikeys_own',
            'apikeys_admin', 'bookmarks', 'notifications', 'mail_own', 'mail_admin' ), false );
        $id = (int)$f['user_id'];
        $own = (bool)$f['own'];
        $out = array();
        foreach ( self::ACTIONS as $key )
        {
            $card = null;
            switch ( $key )
            {
                case 'profile':
                    $card = array( 'how' => 'post', 'button' => 'EditButton' );
                    break;
                case 'password':
                    if ( $a['password'] )
                        $card = array( 'how' => 'post', 'button' => 'ChangePasswordButton' );
                    break;
                case 'settings':
                    if ( $a['settings'] )
                        $card = array( 'how' => 'post', 'button' => 'ChangeSettingButton',
                                       'status' => array( 'key' => $this->state()['key'], 'level' => $this->state()['level'] ) );
                    break;
                case 'twofactor':
                    if ( is_array( $f['twofactor'] ) )
                    {
                        $active = !empty( $f['twofactor']['active'] );
                        $status = array( 'key' => $active ? 'on_' . ( isset( $f['twofactor']['method'] ) && $f['twofactor']['method'] === 'email' ? 'email' : 'app' ) : 'off',
                                         'level' => $active ? 'ok' : 'warn' );
                        // the setup page is always the signed in user's own; another user's is changed in the profile
                        if ( $own && $a['twofactor_setup'] )
                            $card = array( 'how' => 'link', 'url' => 'user2fa/setup', 'status' => $status );
                        elseif ( !$own )
                            $card = array( 'how' => 'post', 'button' => 'EditButton', 'status' => $status );
                    }
                    break;
                case 'apikeys':
                    $counts = is_array( $f['apikeys'] ) ? $f['apikeys'] : null;
                    $status = ( $counts && (int)$counts['total'] > 0 ) ? array( 'key' => 'count', 'level' => (int)$counts['active'] > 0 ? 'info' : 'none',
                                               'active' => (int)$counts['active'], 'total' => (int)$counts['total'] ) : null;
                    if ( $own && ( $f['can_create_apikeys'] || ( $counts && (int)$counts['total'] > 0 ) ) )
                        $card = array( 'how' => 'link', 'url' => 'apikey/list', 'status' => $status );
                    elseif ( !$own && $a['apikeys_admin'] && $counts )
                        $card = array( 'how' => 'link', 'url' => 'oauthadmin/keys/(user)/' . $id, 'status' => $status );
                    break;
                case 'bookmarks':
                    if ( $own && $a['bookmarks'] )
                        $card = array( 'how' => 'link', 'url' => 'content/bookmark' );
                    break;
                case 'notifications':
                    if ( $own && $a['notifications'] )
                        $card = array( 'how' => 'link', 'url' => 'notification/settings' );
                    break;
                case 'mail':
                    if ( $own && $a['mail_own'] )
                        $card = array( 'how' => 'link', 'url' => 'mailpreferences/settings' );
                    elseif ( !$own && $a['mail_admin'] )
                        $card = array( 'how' => 'link', 'url' => 'mailpreferences/admin/user/' . $id );
                    break;
            }
            if ( $card !== null )
                $out[] = array( 'key' => $key ) + array_filter( $card, function ( $v ) { return $v !== null; } ) + array( 'button' => '', 'url' => '', 'status' => false );
        }
        return $out;
    }

    /**
     * Everything for the template.
     *
     * @return array account, state, hints, actions, can_view_roles
     */
    public function toArray()
    {
        $a = $this->facts['access'];
        return array( 'account' => $this->account(), 'state' => $this->state(), 'hints' => $this->hints(),
                      'actions' => $this->actions(), 'can_view_roles' => !empty( $a['roles'] ) );
    }

    /**
     * The groups the user is placed in: the parents of the user's locations, the main one first.
     *
     * @param eZContentObject|null $object
     * @return array[] name, node_id
     */
    protected static function groupsOf( $object )
    {
        $groups = array();
        if ( !$object instanceof eZContentObject )
            return $groups;
        $main = (int)$object->attribute( 'main_node_id' );
        foreach ( $object->assignedNodes( false ) as $row )
        {
            $parent = eZContentObjectTreeNode::fetch( (int)$row['parent_node_id'] );
            if ( !$parent instanceof eZContentObjectTreeNode )
                continue;
            $group = array( 'name' => (string)$parent->attribute( 'name' ), 'node_id' => (int)$parent->attribute( 'node_id' ) );
            if ( (int)$row['node_id'] === $main )
                array_unshift( $groups, $group );
            else
                $groups[] = $group;
        }
        return $groups;
    }

    /**
     * The roles the user has, directly or through a group, once each, by name.
     *
     * @param eZUser $account
     * @return array[] id, name
     */
    protected static function rolesOf( eZUser $account )
    {
        $roles = array();
        foreach ( (array)$account->roles() as $role )
            if ( $role instanceof eZRole )
                $roles[(int)$role->attribute( 'id' )] = array( 'id' => (int)$role->attribute( 'id' ), 'name' => (string)$role->attribute( 'name' ) );
        uasort( $roles, function ( $x, $y ) { return strcasecmp( $x['name'], $y['name'] ); } );
        return array_values( $roles );
    }

    /**
     * The two-step sign-in of the user, when the user class has its field and the extension's module is there.
     *
     * @param eZContentObject|null $object
     * @return array|null active, method
     */
    protected static function twoFactorOf( $object )
    {
        if ( !$object instanceof eZContentObject || !eZModule::exists( 'user2fa' ) )
            return null;
        foreach ( (array)$object->attribute( 'data_map' ) as $attribute )
        {
            if ( $attribute->attribute( 'data_type_string' ) !== 'sevenxauthentication2fa' )
                continue;
            $content = $attribute->attribute( 'content' );
            if ( !is_object( $content ) || !method_exists( $content, 'attribute' ) )
                return array( 'active' => false, 'method' => 'disabled' );
            return array( 'active' => (bool)$content->attribute( 'is_active' ), 'method' => (string)$content->attribute( 'method' ) );
        }
        return null;
    }

    /**
     * What the viewer may open from the page.
     *
     * @param eZUser $viewer
     * @param bool $own
     * @return bool[]
     */
    protected static function accessOf( eZUser $viewer, $own )
    {
        $may = function ( $module, $function ) use ( $viewer ) {
            if ( !eZModule::exists( $module ) )
                return false;
            $access = $viewer->hasAccessTo( $module, $function );
            return $access['accessWord'] !== 'no';
        };
        return array(
            'password' => $may( 'user', 'password' ),
            'settings' => $may( 'user', 'preferences' ),
            'roles' => $may( 'role', 'view' ),
            'twofactor_setup' => $own && $may( 'user2fa', 'setup' ),
            'apikeys_own' => $own && eZModule::exists( 'apikey' ),
            'apikeys_admin' => $may( 'oauthadmin', 'keys' ),
            'bookmarks' => $own && $may( 'content', 'bookmark' ),
            'notifications' => $own && $may( 'notification', 'use' ),
            'mail_own' => $own && eZModule::exists( 'mailpreferences' ),
            'mail_admin' => $may( 'mailpreferences', 'administrate' ),
        );
    }

    /**
     * When the user's password was last changed, as far as the audit index knows (access.user.password.change).
     *
     * @param int $userID
     * @return int|null a timestamp, or null when not recorded
     */
    protected static function passwordChanged( $userID )
    {
        if ( !class_exists( 'expAuditQuery' ) || !class_exists( 'expAuditIndexSchema' ) )
            return null;
        try
        {
            $query = new expAuditQuery();
            $rows = $query->fetch( array( 'name' => 'access.user.password.change', 'object' => array( 'user', (string)(int)$userID ) ),
                                   null, 0, 1, 'time_ms' );
        }
        catch ( Exception $e )
        {
            return null;
        }
        if ( !is_array( $rows ) || !isset( $rows[0] ) )
            return null;
        $row = $rows[0];
        $ms = isset( $row['time_ms'] ) ? $row['time_ms'] : reset( $row );
        return $ms ? (int)floor( (float)$ms / 1000 ) : null;
    }
}
?>
