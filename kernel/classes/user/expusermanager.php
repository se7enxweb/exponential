<?php
/**
 * File containing the expUserManager class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * User management as the console does it (the exp:user commands) and the services share: find an account, check a
 * new login, e-mail address or password, create, edit, remove, disable and enable an account, reset its password,
 * approve or reject a registration. Every action goes through the kernel's own paths, the same as the administration:
 * a new account is a content object published by the content/publish operation (its workflows run, an approval
 * waits as in the administration), enabling and disabling is the setsettings operation of the user module (which
 * ends the sessions of a disabled account), a removal is the removal of the object's locations (accounts are never
 * moved to the trash, as content/removeobject refuses), the registration is approved or rejected by
 * expRegistrationAdminAction. So every change reaches the audit (expAuditHook) and from it the user audit
 * (expUserAudit) exactly as when it is done in the administration; nothing here writes an audit row of its own except
 * where the administration does it in its view rather than in the kernel path (the console names the reason).
 *
 * Every action is planned first and done only with $apply: the plan lists what would change and what refuses it,
 * so a dry run says exactly what --apply will do. Passwords and password hashes never appear in a result, except the
 * one password a caller asked to be generated, which is returned once.
 *
 * Guide: doc/eng-us/guides/user-management-from-the-console.md
 */
class expUserManager
{
    /** The machine output of the exp:user commands */
    const REPORT = 'exp-user/1';

    /** The states exp:user:list filters by: the filters of the user hygiene page and the console's own */
    const STATES = array( 'all', 'enabled', 'disabled', 'unactivated', 'pending', 'locked', 'never', 'inactive', 'orphaned',
                          'duplicate_login', 'duplicate_email' );

    /** The orders of exp:user:list (expUserHygiene::SORTS) */
    const SORTS = array( 'name', 'login', 'email', 'last_visit', 'login_count', 'id' );

    /** The largest page of exp:user:list */
    const MAX_LIMIT = 500;

    /** The modes of a password reset */
    const RESET_MODES = array( 'send-link', 'generate', 'stdin' );

    /** The characters of a generated password: no 0/O, 1/l/I, so it can be read out and typed */
    const PASSWORD_LOWER = 'abcdefghijkmnopqrstuvwxyz';
    const PASSWORD_UPPER = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    const PASSWORD_DIGITS = '23456789';
    const PASSWORD_SYMBOLS = '-_.!@#%+=';

    /** @var callable|null ( eZMail $mail ) => bool, replaces eZMailTransport::send() (tests) */
    protected static $mailSender = null;

    /**
     * Replaces the mail transport (tests send to a file or an array, never to a mailbox); null restores it.
     *
     * @param callable|null $sender
     */
    public static function setMailSender( $sender = null )
    {
        self::$mailSender = $sender;
    }

    // ---- finding ----------------------------------------------------------------------------------------------------

    /**
     * How a reference names an account: id (digits), email (has an @) or login. Pure.
     *
     * @param string $ref
     * @return string id, email, login, or '' when empty
     */
    public static function refKind( $ref )
    {
        $ref = trim( (string)$ref );
        if ( $ref === '' )
            return '';
        if ( ctype_digit( $ref ) )
            return 'id';
        return strpos( $ref, '@' ) !== false ? 'email' : 'login';
    }

    /**
     * The account a reference names: a content object id, a login (any case, as the sign-in compares) or an
     * e-mail address (only when one account has it). A login that is all digits is found when no account has that
     * id; an address that is a login is found as a login.
     *
     * @param string $ref
     * @return array user (eZUser|null), error ('' when found)
     */
    public static function find( $ref )
    {
        $ref = trim( (string)$ref );
        switch ( self::refKind( $ref ) )
        {
            case '':
                return array( 'user' => null, 'error' => 'name the account: a login, an e-mail address or a user id' );
            case 'id':
                $user = eZUser::fetch( (int)$ref );
                if ( !$user instanceof eZUser )
                    $user = eZUser::fetchByName( $ref );
                return $user instanceof eZUser ? array( 'user' => $user, 'error' => '' )
                                               : array( 'user' => null, 'error' => "no account has the id or login $ref" );
            case 'email':
                $users = eZPersistentObject::fetchObjectList( eZUser::definition(), null, array( 'email' => $ref ), null, null, true );
                if ( !$users )
                {
                    $user = eZUser::fetchByName( $ref );
                    if ( $user instanceof eZUser )
                        return array( 'user' => $user, 'error' => '' );
                    return array( 'user' => null, 'error' => "no account has the e-mail address $ref" );
                }
                if ( count( $users ) > 1 )
                    return array( 'user' => null, 'error' => count( $users ) . " accounts share the e-mail address $ref: name the account by its login or id" );
                return array( 'user' => $users[0], 'error' => '' );
        }
        $user = eZUser::fetchByName( $ref );
        return $user instanceof eZUser ? array( 'user' => $user, 'error' => '' ) : array( 'user' => null, 'error' => "no account has the login $ref" );
    }

    /**
     * The user who acts: by login, else the user of [UserSettings] UserCreatorID (the administrator the kernel creates
     * accounts as); signed in for this process without a session, so the audit names it.
     *
     * @param string $login '' for the default
     * @return array user (eZUser|null), error
     */
    public static function actAs( $login = '' )
    {
        $login = trim( (string)$login );
        if ( $login !== '' )
        {
            $found = self::find( $login );
            if ( !$found['user'] )
                return array( 'user' => null, 'error' => '--as: ' . $found['error'] );
            $user = $found['user'];
        }
        else
        {
            $id = (int)eZINI::instance()->variable( 'UserSettings', 'UserCreatorID' );
            $user = eZUser::fetch( $id );
            if ( !$user instanceof eZUser )
                return array( 'user' => null, 'error' => "[UserSettings] UserCreatorID=$id names no account: name the user who acts with --as" );
        }
        eZUser::setCurrentlyLoggedInUser( $user, (int)$user->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        return array( 'user' => $user, 'error' => '' );
    }

    // ---- facts ------------------------------------------------------------------------------------------------------

    /**
     * The list filters of the console, checked: state (STATES), days (1 to expUserHygiene::MAX_DAYS, for inactive),
     * group (a node id, 0 for every account), q (the search in login, name and e-mail), sort (SORTS), order, limit
     * (1 to MAX_LIMIT), offset. Pure.
     *
     * @param array $input
     * @return array filters, errors (one sentence per wrong value)
     */
    public static function listFilters( array $input )
    {
        $get = function ( $name, $default ) use ( $input ) {
            return isset( $input[$name] ) && $input[$name] !== false && $input[$name] !== null ? trim( (string)$input[$name] ) : $default;
        };
        $errors = array();
        $state = strtolower( $get( 'state', 'all' ) );
        if ( !in_array( $state, self::STATES, true ) )
            $errors[] = "--state must be one of " . implode( ', ', self::STATES ) . ", not $state";
        $sort = strtolower( $get( 'sort', 'name' ) );
        if ( !in_array( $sort, self::SORTS, true ) )
            $errors[] = "--sort must be one of " . implode( ', ', self::SORTS ) . ", not $sort";
        $order = strtolower( $get( 'order', 'asc' ) );
        if ( !in_array( $order, array( 'asc', 'desc' ), true ) )
            $errors[] = "--order must be asc or desc, not $order";
        $numbers = array();
        foreach ( array( 'days' => array( expUserHygiene::DEFAULT_DAYS, 1, expUserHygiene::MAX_DAYS ), 'limit' => array( 25, 1, self::MAX_LIMIT ),
                         'offset' => array( 0, 0, PHP_INT_MAX ) ) as $name => $range )
        {
            $value = $get( $name, (string)$range[0] );
            if ( !ctype_digit( $value ) || (int)$value < $range[1] || (int)$value > $range[2] )
            {
                $errors[] = "--$name must be a whole number from {$range[1]}" . ( $range[2] < PHP_INT_MAX ? " to {$range[2]}" : '' ) . ", not $value";
                $value = (string)$range[0];
            }
            $numbers[$name] = (int)$value;
        }
        $filters = array( 'filter' => $state, 'state' => $state, 'days' => $numbers['days'], 'group' => $get( 'group', '' ),
                          'q' => expUnactivatedUsers::normaliseSearch( $get( 'search', '' ) ), 'sort' => $sort, 'order' => $order,
                          'limit' => $numbers['limit'], 'offset' => $numbers['offset'] );
        return array( 'filters' => $filters, 'errors' => $errors );
    }

    /**
     * One page of accounts (expUserHygiene: one query for the page, six to enrich it): total, and the accounts with
     * id, login, email, name, enabled, unactivated, locked, failed_attempts, login_count, last_visit, idle_days,
     * object, node_id, groups, roles, states. For the state pending, the waiting registrations (pending()).
     *
     * @param array $filters see listFilters()
     * @return array total, accounts, error ('' when listed)
     */
    public static function listAccounts( array $filters )
    {
        $selected = self::selection( $filters );
        if ( $selected['error'] !== '' )
            return array( 'total' => 0, 'accounts' => array(), 'error' => $selected['error'] );
        $hygiene = $selected['hygiene'];
        $filters = $selected['filters'];
        $total = $hygiene->count( $filters, true, $selected['group_path'] );
        $accounts = $total > (int)$filters['offset'] ? $hygiene->page( $filters, true, $selected['group_path'], (int)$filters['offset'] ) : array();
        if ( $filters['state'] === 'pending' )
            foreach ( $accounts as $i => $account )
                $accounts[$i]['registration'] = expRegistrationAdminAction::state( $account['id'] );
        return array( 'total' => $total, 'accounts' => $accounts, 'error' => '' );
    }

    /**
     * What the list filters select, ready for expUserHygiene: the list (null on MongoDB), the filters with the
     * waiting registrations as ids for the state pending, and the path of --group.
     *
     * @param array $filters see listFilters()
     * @return array hygiene (expUserHygiene|null), filters, group_path (string|null), error
     */
    public static function selection( array $filters )
    {
        $out = array( 'hygiene' => expUserHygiene::fromDatabase(), 'filters' => $filters, 'group_path' => null, 'error' => '' );
        if ( !$out['hygiene'] )
        {
            $out['error'] = 'the list is read with SQL, which this database (MongoDB) does not offer';
            return $out;
        }
        if ( $filters['group'] !== '' )
        {
            $group = self::findGroup( $filters['group'] );
            if ( !$group['node'] )
            {
                $out['error'] = '--group: ' . $group['error'];
                return $out;
            }
            $out['group_path'] = (string)$group['node']->attribute( 'path_string' );
        }
        if ( $filters['state'] === 'pending' )
        {
            $ids = self::pendingIDs();
            $out['filters']['ids'] = $ids ? $ids : array( 0 );
            $out['filters']['filter'] = 'all';
        }
        return $out;
    }

    /**
     * How many accounts are in each state (STATES; inactive for $days days). One count query per state.
     *
     * @param int $days
     * @return array counts (state => n), error
     */
    public static function hygieneCounts( $days )
    {
        $counts = array();
        foreach ( self::STATES as $state )
        {
            $checked = self::listFilters( array( 'state' => $state, 'days' => (string)(int)$days ) );
            $selected = self::selection( $checked['filters'] );
            if ( $selected['error'] !== '' )
                return array( 'counts' => array(), 'error' => $selected['error'] );
            $counts[$state] = $selected['hygiene']->count( $selected['filters'], true, null );
        }
        return array( 'counts' => $counts, 'error' => '' );
    }

    /**
     * The accounts whose registration waits: unactivated (disabled with an account key: the e-mail address is not
     * confirmed, or an administrator activates it), disabled without a key and never signed in (waiting for an
     * administrator), and those whose user object waits for an approval item (expRegistrationAdminAction::state():
     * email, administrator, workflow). Newest first.
     *
     * @return int[]
     */
    public static function pendingIDs()
    {
        $db = eZDB::instance();
        $ids = array();
        $queries = array(
            'SELECT DISTINCT k.user_id AS id FROM ezuser_accountkey k INNER JOIN ezuser_setting s ON s.user_id = k.user_id WHERE s.is_enabled = 0',
            'SELECT s.user_id AS id FROM ezuser_setting s LEFT JOIN ezuservisit v ON v.user_id = s.user_id'
            . ' WHERE s.is_enabled = 0 AND ( v.user_id IS NULL OR v.login_count = 0 )'
            . ' AND NOT EXISTS ( SELECT 1 FROM ezuser_accountkey k WHERE k.user_id = s.user_id )',
            "SELECT DISTINCT c.data_int1 AS id FROM ezcollab_item c INNER JOIN ezuser u ON u.contentobject_id = c.data_int1"
            . " WHERE c.type_identifier = 'ezapprove' AND c.data_int3 = " . (int)eZApproveCollaborationHandler::STATUS_WAITING,
        );
        $anonymous = (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' );
        foreach ( $queries as $sql )
            foreach ( (array)$db->arrayQuery( $sql ) as $row )
                if ( (int)$row['id'] > 0 && (int)$row['id'] !== $anonymous )
                    $ids[(int)$row['id']] = (int)$row['id'];
        krsort( $ids );
        return array_values( $ids );
    }

    /**
     * Everything exp:user:show tells about an account: the listed facts (listAccounts()), the overview of the account
     * page (groups with the main one first, roles, last password change, active sessions, sign-in problems), the
     * registration state, two-step sign-in, whether a password change is required, and whether the account is
     * protected. Never a password hash.
     *
     * @param eZUser $user
     * @param eZUser $viewer the user who acts
     * @return array
     */
    public static function account( eZUser $user, eZUser $viewer )
    {
        $id = (int)$user->attribute( 'contentobject_id' );
        $listed = array();
        $hygiene = expUserHygiene::fromDatabase();
        if ( $hygiene )
        {
            $filters = array( 'filter' => 'all', 'days' => expUserHygiene::DEFAULT_DAYS, 'group' => 0, 'q' => '', 'sort' => 'id',
                              'order' => 'asc', 'limit' => 1, 'ids' => array( $id ) );
            $rows = $hygiene->page( $filters, true );
            $listed = $rows ? $rows[0] : array();
        }
        $overview = expUserAccountOverview::fromUser( $user, $viewer )->toArray();
        $object = $user->contentObject();
        $setting = eZUserSetting::fetch( $id );
        $account = array(
            'id' => $id,
            'login' => (string)$user->attribute( 'login' ),
            'email' => (string)$user->attribute( 'email' ),
            'name' => $object instanceof eZContentObject ? (string)$object->attribute( 'name' ) : '',
            'class' => $object instanceof eZContentObject ? (string)$object->attribute( 'class_identifier' ) : '',
            'node_id' => $object instanceof eZContentObject ? (int)$object->attribute( 'main_node_id' ) : 0,
            'object' => isset( $listed['object'] ) ? $listed['object'] : 'none',
            'enabled' => $setting instanceof eZUserSetting && (int)$setting->attribute( 'is_enabled' ) !== 0,
            'max_login' => $setting instanceof eZUserSetting ? (int)$setting->attribute( 'max_login' ) : 0,
            'unactivated' => !empty( $listed['unactivated'] ),
            'locked' => (bool)$user->isLocked(),
            'failed_attempts' => (int)$user->failedLoginAttempts(),
            'login_count' => (int)$user->loginCount(),
            'last_visit' => $overview['account']['last_visit'],
            'password_changed' => $overview['account']['password_changed'],
            'password_change_required' => class_exists( 'expPasswordChangeRequired' ) && expPasswordChangeRequired::isMarked( $user ),
            'registration' => expRegistrationAdminAction::state( $id ),
            'states' => isset( $listed['states'] ) ? $listed['states'] : array(),
            'groups' => array(),
            'roles' => array(),
            'two_factor' => expUserAccountOverview::twoFactorOf( $object ),
            'sessions' => null,
            'protected' => self::protectedReason( $id, (int)$viewer->attribute( 'contentobject_id' ) ),
            'administrator' => in_array( $id, self::administratorIDs(), true ),
        );
        $main = $object instanceof eZContentObject ? (int)$object->attribute( 'main_node_id' ) : 0;
        if ( $object instanceof eZContentObject )
            foreach ( $object->assignedNodes( false ) as $row )
            {
                $parent = eZContentObjectTreeNode::fetch( (int)$row['parent_node_id'] );
                if ( $parent instanceof eZContentObjectTreeNode )
                    $account['groups'][] = array( 'node_id' => (int)$parent->attribute( 'node_id' ), 'name' => (string)$parent->attribute( 'name' ),
                                                  'main' => (int)$row['node_id'] === $main );
            }
        usort( $account['groups'], function ( $a, $b ) { return (int)$b['main'] - (int)$a['main']; } );
        foreach ( self::roleAssignments( $user ) as $assignment )
            $account['roles'][] = $assignment;
        if ( class_exists( 'expUserSessionTracker' ) && expUserSessionTracker::enabled() && expUserSessionTracker::available() )
        {
            try
            {
                $account['sessions'] = count( expUserSession::fetchForUser( $id, true, 1000 ) );
            }
            catch ( Throwable $e )
            {
                $account['sessions'] = null;
            }
        }
        return $account;
    }

    /**
     * The roles of an account, each with where it comes from (the account itself or a group above it) and its
     * limitation.
     *
     * @param eZUser $user
     * @return array[] role, role_id, via (the object id), via_name, limitation ('' or Subtree:/1/2/, Section:3)
     */
    public static function roleAssignments( eZUser $user )
    {
        $db = eZDB::instance();
        $holders = array_map( 'intval', (array)$user->groups() );
        $holders[] = (int)$user->attribute( 'contentobject_id' );
        $holders = array_values( array_unique( array_filter( $holders ) ) );
        if ( !$holders )
            return array();
        $rows = $db->arrayQuery( 'SELECT ur.contentobject_id, ur.limit_identifier, ur.limit_value, r.id AS role_id, r.name, o.name AS via_name'
                                 . ' FROM ezuser_role ur INNER JOIN ezrole r ON r.id = ur.role_id LEFT JOIN ezcontentobject o ON o.id = ur.contentobject_id'
                                 . ' WHERE r.version = 0 AND ' . $db->generateSQLINStatement( $holders, 'ur.contentobject_id', false, true, 'int' )
                                 . ' ORDER BY r.name, ur.contentobject_id' );
        $out = array();
        foreach ( (array)$rows as $row )
            $out[] = array( 'role' => (string)$row['name'], 'role_id' => (int)$row['role_id'], 'via' => (int)$row['contentobject_id'],
                            'via_name' => (string)$row['via_name'],
                            'limitation' => (string)$row['limit_identifier'] !== '' ? $row['limit_identifier'] . ':' . $row['limit_value'] : '' );
        return $out;
    }

    // ---- protection -------------------------------------------------------------------------------------------------

    /**
     * The accounts that are never disabled or removed here: the anonymous user, the user of UserCreatorID, the
     * administrator account of a clean installation, the account with the login "admin" and the user who acts
     * (expUserHygiene::protectedIDs(), the rule of the user hygiene page).
     *
     * @param int $actorID
     * @return int[]
     */
    public static function protectedIDs( $actorID )
    {
        return expUserHygiene::protectedIDs( (int)$actorID );
    }

    /**
     * Why an account is protected, '' when it is not: anonymous, creator ([UserSettings] UserCreatorID), admin (the
     * administrator account), self (the user who acts).
     *
     * @param int $userID
     * @param int $actorID
     * @return string
     */
    public static function protectedReason( $userID, $actorID )
    {
        $ini = eZINI::instance();
        $userID = (int)$userID;
        if ( $userID === (int)$ini->variable( 'UserSettings', 'AnonymousUserID' ) )
            return 'anonymous';
        if ( $userID === (int)$actorID )
            return 'self';
        if ( $userID === (int)$ini->variable( 'UserSettings', 'UserCreatorID' ) )
            return 'creator';
        return in_array( $userID, self::protectedIDs( $actorID ), true ) ? 'admin' : '';
    }

    /**
     * The enabled, published accounts that have every right: a role with a policy on every module (*), assigned to
     * the account or a group above it without a limitation.
     *
     * @return int[]
     */
    public static function administratorIDs()
    {
        $db = eZDB::instance();
        $holders = array();
        foreach ( (array)$db->arrayQuery( "SELECT DISTINCT ur.contentobject_id, ur.limit_identifier FROM ezuser_role ur"
                                          . " INNER JOIN ezpolicy p ON p.role_id = ur.role_id WHERE p.module_name = '*'" ) as $row )
            if ( trim( (string)$row['limit_identifier'] ) === '' )
                $holders[(int)$row['contentobject_id']] = (int)$row['contentobject_id'];
        if ( !$holders )
            return array();
        $accounts = $holders;
        $paths = $db->arrayQuery( 'SELECT path_string FROM ezcontentobject_tree WHERE '
                                  . $db->generateSQLINStatement( array_values( $holders ), 'contentobject_id', false, true, 'int' ) );
        foreach ( (array)$paths as $path )
        {
            $like = $db->escapeString( (string)$path['path_string'] );
            foreach ( (array)$db->arrayQuery( "SELECT DISTINCT t.contentobject_id FROM ezcontentobject_tree t INNER JOIN ezuser u ON u.contentobject_id = t.contentobject_id"
                                              . " WHERE t.path_string LIKE '$like%'" ) as $row )
                $accounts[(int)$row['contentobject_id']] = (int)$row['contentobject_id'];
        }
        $enabled = array();
        foreach ( (array)$db->arrayQuery( 'SELECT s.user_id FROM ezuser_setting s INNER JOIN ezcontentobject o ON o.id = s.user_id'
                                          . ' WHERE s.is_enabled = 1 AND o.status = ' . (int)eZContentObject::STATUS_PUBLISHED . ' AND '
                                          . $db->generateSQLINStatement( array_values( $accounts ), 's.user_id', false, true, 'int' ) ) as $row )
            $enabled[] = (int)$row['user_id'];
        sort( $enabled );
        return $enabled;
    }

    /**
     * Whether the account is the last enabled one with every right (administratorIDs()). Pure on its inputs.
     *
     * @param int $userID
     * @param int[] $administrators
     * @return bool
     */
    public static function isLastAdministrator( $userID, array $administrators )
    {
        $administrators = array_map( 'intval', $administrators );
        return in_array( (int)$userID, $administrators, true ) && count( array_diff( $administrators, array( (int)$userID ) ) ) === 0;
    }

    /**
     * Why an account may not be disabled or removed, '' when it may: protected (protectedReason()) or
     * last_administrator.
     *
     * @param int $userID
     * @param int $actorID
     * @return string
     */
    public static function refusal( $userID, $actorID )
    {
        $reason = self::protectedReason( $userID, $actorID );
        if ( $reason !== '' )
            return $reason;
        return self::isLastAdministrator( $userID, self::administratorIDs() ) ? 'last_administrator' : '';
    }

    /**
     * The sentence of a refusal.
     *
     * @param string $reason
     * @return string
     */
    public static function refusalText( $reason )
    {
        $texts = array(
            'anonymous' => 'the anonymous user ([UserSettings] AnonymousUserID) is never changed here',
            'self' => 'the user who acts (--as) cannot do this to its own account',
            'creator' => 'the user of [UserSettings] UserCreatorID, which the kernel creates accounts as, is protected',
            'admin' => 'the administrator account (id 14 or the login admin) is protected',
            'last_administrator' => 'this is the last enabled account with every right (a role with a policy on every module): enable or create another administrator first',
        );
        return isset( $texts[$reason] ) ? $texts[$reason] : $reason;
    }

    // ---- validation -------------------------------------------------------------------------------------------------

    /**
     * What is wrong with a new login and e-mail address (and a password, when given): the login rules
     * (eZUser::validateLoginName()), a login another account has, an address that is not valid or (with
     * RequireUniqueEmail) another account's, and the password rules (passwordProblems()).
     *
     * @param string|null $login null: not checked
     * @param string|null $email null: not checked
     * @param string|null $password null: not checked
     * @param int $exceptID the account itself, when it changes
     * @return array[] field (login, email, password), code (invalid, taken, refused), message
     */
    public static function accountProblems( $login, $email, $password = null, $exceptID = 0 )
    {
        $problems = array();
        if ( $login !== null )
        {
            $error = '';
            if ( $login === '' || !eZUser::validateLoginName( $login, $error ) )
                $problems[] = array( 'field' => 'login', 'code' => 'invalid', 'message' => 'The login name is not valid' . ( $error ? ": $error" : '' ) );
            else
            {
                $existing = eZUser::fetchByName( $login );
                if ( $existing instanceof eZUser && (int)$existing->attribute( 'contentobject_id' ) !== (int)$exceptID )
                    $problems[] = array( 'field' => 'login', 'code' => 'taken', 'message' => 'The login name is already taken' );
            }
        }
        if ( $email !== null )
        {
            if ( !eZMail::validate( $email ) )
                $problems[] = array( 'field' => 'email', 'code' => 'invalid', 'message' => 'The e-mail address is not valid' );
            else if ( eZUser::requireUniqueEmail() )
            {
                $other = eZUser::fetchByEmail( $email );
                if ( $other instanceof eZUser && (int)$other->attribute( 'contentobject_id' ) !== (int)$exceptID )
                    $problems[] = array( 'field' => 'email', 'code' => 'taken', 'message' => 'The e-mail address is already in use' );
            }
        }
        if ( $password !== null )
            foreach ( self::passwordProblems( $password, $exceptID ? eZUser::fetch( (int)$exceptID ) : null, $login ) as $message )
                $problems[] = array( 'field' => 'password', 'code' => 'refused', 'message' => $message );
        return $problems;
    }

    /**
     * Why a password is refused: the rules of user/password (expPasswordPolicy), or only the length where the policy
     * is missing. Never names the password.
     *
     * @param string $password
     * @param eZUser|null $user
     * @param string|null $login
     * @return string[]
     */
    public static function passwordProblems( $password, $user = null, $login = null )
    {
        if ( class_exists( 'expPasswordPolicy' ) )
            return expPasswordPolicy::instance()->accountErrors( (string)$password, $user instanceof eZUser ? $user : null, $login );
        return eZUser::validatePassword( $password ) ? array() : array( 'The password is too short' );
    }

    /**
     * A new password that the password rules accept: random_int() over letters of both cases, digits and symbols
     * (each at least once), as long as [UserSettings] GeneratePasswordLength (expPasswordPolicy::generateLength(), 12
     * at least).
     *
     * @param int|null $length
     * @return string
     */
    public static function generatePassword( $length = null )
    {
        if ( $length === null )
            $length = class_exists( 'expPasswordPolicy' ) ? expPasswordPolicy::instance()->generateLength() : 16;
        $length = max( 12, (int)$length );
        $sets = array( self::PASSWORD_LOWER, self::PASSWORD_UPPER, self::PASSWORD_DIGITS, self::PASSWORD_SYMBOLS );
        $all = implode( '', $sets );
        for ( $try = 0; $try < 20; $try++ )
        {
            $chars = array();
            foreach ( $sets as $set )
                $chars[] = $set[random_int( 0, strlen( $set ) - 1 )];
            while ( count( $chars ) < $length )
                $chars[] = $all[random_int( 0, strlen( $all ) - 1 )];
            for ( $i = count( $chars ) - 1; $i > 0; $i-- )
            {
                $j = random_int( 0, $i );
                $swap = $chars[$i];
                $chars[$i] = $chars[$j];
                $chars[$j] = $swap;
            }
            $password = implode( '', $chars );
            if ( !self::passwordProblems( $password ) )
                return $password;
        }
        return $password;
    }

    // ---- groups -----------------------------------------------------------------------------------------------------

    /** @return int[] the classes of user groups ([UserSettings] UserGroupClassID, and the class user_group) */
    public static function groupClassIDs()
    {
        $ini = eZINI::instance();
        $ids = $ini->hasVariable( 'UserSettings', 'UserGroupClassID' ) ? (array)$ini->variable( 'UserSettings', 'UserGroupClassID' ) : array( 3 );
        $class = eZContentClass::fetchByIdentifier( 'user_group' );
        if ( $class instanceof eZContentClass )
            $ids[] = $class->attribute( 'id' );
        return array_values( array_unique( array_filter( array_map( 'intval', $ids ) ) ) );
    }

    /**
     * The user group a reference names: a node id, or a group's name (any case) when one group has it.
     *
     * @param string $ref
     * @return array node (eZContentObjectTreeNode|null), error
     */
    public static function findGroup( $ref )
    {
        $ref = trim( (string)$ref );
        if ( $ref === '' )
            return array( 'node' => null, 'error' => 'name the group: its node id or its name' );
        $classIDs = self::groupClassIDs();
        if ( ctype_digit( $ref ) )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$ref );
            if ( !$node instanceof eZContentObjectTreeNode )
                return array( 'node' => null, 'error' => "no node has the id $ref" );
            $object = $node->object();
            if ( !$object instanceof eZContentObject || !in_array( (int)$object->attribute( 'contentclass_id' ), $classIDs, true ) )
                return array( 'node' => null, 'error' => "node $ref is not a user group" );
            return array( 'node' => $node, 'error' => '' );
        }
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT t.node_id FROM ezcontentobject_tree t INNER JOIN ezcontentobject o ON o.id = t.contentobject_id'
                                 . " WHERE LOWER( o.name ) = '" . $db->escapeString( mb_strtolower( $ref ) ) . "' AND o.status = "
                                 . (int)eZContentObject::STATUS_PUBLISHED . ' AND '
                                 . $db->generateSQLINStatement( $classIDs, 'o.contentclass_id', false, true, 'int' ) . ' ORDER BY t.node_id' );
        if ( !$rows )
            return array( 'node' => null, 'error' => "no user group is named $ref" );
        if ( count( $rows ) > 1 )
            return array( 'node' => null, 'error' => count( $rows ) . " user groups are named $ref (nodes " . implode( ', ', array_column( $rows, 'node_id' ) )
                                                     . '): name the group by its node id' );
        return array( 'node' => eZContentObjectTreeNode::fetch( (int)$rows[0]['node_id'] ), 'error' => '' );
    }

    /**
     * The groups of an account: the parents of its locations, node id => location node id.
     *
     * @param eZContentObject $object
     * @return int[]
     */
    protected static function memberships( eZContentObject $object )
    {
        $out = array();
        foreach ( $object->assignedNodes( false ) as $row )
            $out[(int)$row['parent_node_id']] = (int)$row['node_id'];
        return $out;
    }

    // ---- actions ----------------------------------------------------------------------------------------------------

    /**
     * A result: the planned changes, the refusals and, after --apply, what was done.
     *
     * @return array
     */
    protected static function result()
    {
        return array( 'changes' => array(), 'applied' => false, 'errors' => array(), 'warnings' => array() );
    }

    /**
     * Creates an account: a content object of a user class in its first group, published by the content/publish
     * operation (its workflows run: an approval of new accounts waits as in the administration), placed in the other
     * groups. The password: given (checked against the password rules), generated (returned once in
     * generated_password), or none known with send_activation: the account starts disabled with an activation key
     * and the activation mail is sent (expUnactivatedUsers::resend()); the person sets a password with "Forgot
     * password" after activating.
     *
     * @param array $spec login, email, first_name, last_name, groups (references), password|null, generate (bool),
     *        send_activation (bool), class (identifier, default the class of [UserSettings] UserClassID), disabled (bool)
     * @param bool $apply
     * @return array changes, applied, errors, warnings, user_id, state, generated_password (only when generated and applied)
     */
    public static function create( array $spec, $apply )
    {
        $r = self::result();
        $spec += array( 'login' => '', 'email' => '', 'first_name' => '', 'last_name' => '', 'groups' => array(), 'password' => null,
                        'generate' => false, 'send_activation' => false, 'class' => '', 'disabled' => false );
        $login = trim( (string)$spec['login'] );
        $email = trim( (string)$spec['email'] );
        $modes = (int)( $spec['password'] !== null ) + (int)!empty( $spec['generate'] ) + (int)!empty( $spec['send_activation'] );
        if ( $modes !== 1 )
            $r['errors'][] = 'give exactly one of --password-stdin, --generate-password and --send-activation';
        $password = $spec['password'] !== null ? (string)$spec['password'] : null;
        foreach ( self::accountProblems( $login, $email, $password ) as $problem )
            $r['errors'][] = $problem['message'];
        $classIdentifier = trim( (string)$spec['class'] );
        if ( $classIdentifier === '' )
        {
            $class = eZContentClass::fetch( (int)eZINI::instance()->variable( 'UserSettings', 'UserClassID' ) );
            $classIdentifier = $class instanceof eZContentClass ? (string)$class->attribute( 'identifier' ) : 'user';
        }
        $class = eZContentClass::fetchByIdentifier( $classIdentifier );
        if ( !$class instanceof eZContentClass || !in_array( $classIdentifier, (array)eZUser::fetchUserClassNames(), true ) )
            $r['errors'][] = "$classIdentifier is not a user class";
        $groups = array();
        $refs = $spec['groups'] ? (array)$spec['groups'] : array( (string)eZINI::instance()->variable( 'UserSettings', 'DefaultUserPlacement' ) );
        foreach ( $refs as $ref )
        {
            $group = self::findGroup( $ref );
            if ( !$group['node'] )
                $r['errors'][] = '--group: ' . $group['error'];
            else
                $groups[(int)$group['node']->attribute( 'node_id' )] = $group['node'];
        }
        if ( $r['errors'] )
            return $r;
        $groups = array_values( $groups );
        $first = $groups[0];
        $enabled = empty( $spec['send_activation'] ) && empty( $spec['disabled'] );
        $r['changes'][] = sprintf( 'create the account %s (%s) of class %s in %s (node %d)%s', $login, $email, $classIdentifier,
                                   $first->attribute( 'name' ), $first->attribute( 'node_id' ), $enabled ? '' : ', disabled' );
        foreach ( array_slice( $groups, 1 ) as $group )
            $r['changes'][] = sprintf( 'add it to %s (node %d)', $group->attribute( 'name' ), $group->attribute( 'node_id' ) );
        if ( !empty( $spec['generate'] ) )
            $r['changes'][] = 'set a generated password (shown once)';
        else if ( $password !== null )
            $r['changes'][] = 'set the password read from standard input';
        else
            $r['changes'][] = 'send the activation mail to ' . $email . ' (the person sets a password with "Forgot password")';
        if ( !$apply )
            return $r;

        if ( !empty( $spec['generate'] ) )
            $password = self::generatePassword();
        else if ( $password === null )
            $password = self::generatePassword( 32 );
        $type = eZUser::hashType();
        $hash = eZUser::createHash( $login, $password, eZUser::site(), $type );
        $attributes = array( 'user_account' => $login . '|' . $email . '|' . $hash . '|' . eZUser::passwordHashTypeName( $type ) . '|' . ( $enabled ? 1 : 0 ) );
        $dataMap = $class->attribute( 'data_map' );
        foreach ( array( 'first_name', 'last_name' ) as $field )
            if ( trim( (string)$spec[$field] ) !== '' )
            {
                if ( isset( $dataMap[$field] ) )
                    $attributes[$field] = trim( (string)$spec[$field] );
                else
                    $r['warnings'][] = "the class $classIdentifier has no attribute $field: --" . str_replace( '_', '-', $field ) . ' was left out';
            }
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => (int)$first->attribute( 'node_id' ),
                                                                      'class_identifier' => $classIdentifier,
                                                                      'creator_id' => (int)eZUser::currentUserID(),
                                                                      'attributes' => $attributes ) );
        $user = $object instanceof eZContentObject ? eZUser::fetch( (int)$object->attribute( 'id' ) ) : null;
        if ( !$user instanceof eZUser )
        {
            $r['errors'][] = 'the account could not be created (the content publish did not store it; see the debug log)';
            return $r;
        }
        $id = (int)$user->attribute( 'contentobject_id' );
        $r['applied'] = true;
        $r['user_id'] = $id;
        $object = eZContentObject::fetch( $id );
        if ( (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED )
            $r['warnings'][] = 'the publish waits (a workflow, such as the approval of new accounts, holds it): the account is in place once it is approved';
        else
        {
            $main = (int)$object->attribute( 'main_node_id' );
            foreach ( array_slice( $groups, 1 ) as $group )
                eZContentOperationCollection::addAssignment( $main, $id, array( (int)$group->attribute( 'node_id' ) ) );
        }
        if ( !empty( $spec['send_activation'] ) )
        {
            $key = eZUserAccountKey::createNew( $id, expUnactivatedUsers::newHash(), time() );
            $key->store();
            $sent = expUnactivatedUsers::resend( $id, self::$mailSender );
            if ( $sent !== 'sent' )
                $r['warnings'][] = "the activation mail was not sent ($sent): ./console exp:user:approve " . $login . ' activates the account';
        }
        if ( !empty( $spec['generate'] ) )
            $r['generated_password'] = $password;
        eZContentObject::clearCache( array( $id ) );
        $r['state'] = expRegistrationAdminAction::state( $id );
        return $r;
    }

    /**
     * Changes an account: e-mail address and login (eZUser::store(), audited as access.user.email.change and
     * access.user.login.change), first and last name (a new version published by content/publish), groups added
     * (a new location), removed (the location removed; an account keeps at least one) and the main group.
     *
     * @param eZUser $user
     * @param array $spec email, login, first_name, last_name (null: unchanged), add_groups, remove_groups (references),
     *        main_group (a reference or '')
     * @param bool $apply
     * @return array
     */
    public static function edit( eZUser $user, array $spec, $apply )
    {
        $r = self::result();
        $spec += array( 'email' => null, 'login' => null, 'first_name' => null, 'last_name' => null, 'add_groups' => array(),
                        'remove_groups' => array(), 'main_group' => '' );
        $id = (int)$user->attribute( 'contentobject_id' );
        $object = $user->contentObject();
        if ( !$object instanceof eZContentObject )
        {
            $r['errors'][] = 'the account has no content object to edit';
            return $r;
        }
        if ( $id === (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) && ( $spec['login'] !== null || $spec['remove_groups'] ) )
            $r['errors'][] = self::refusalText( 'anonymous' );
        $email = $spec['email'] !== null ? trim( (string)$spec['email'] ) : null;
        $login = $spec['login'] !== null ? trim( (string)$spec['login'] ) : null;
        if ( $email === (string)$user->attribute( 'email' ) )
            $email = null;
        if ( $login === (string)$user->attribute( 'login' ) )
            $login = null;
        foreach ( self::accountProblems( $login, $email, null, $id ) as $problem )
            $r['errors'][] = $problem['message'];
        if ( $email !== null )
            $r['changes'][] = "change the e-mail address to $email";
        if ( $login !== null )
            $r['changes'][] = "change the login to $login";

        $dataMap = $object->attribute( 'data_map' );
        $fields = array();
        foreach ( array( 'first_name', 'last_name' ) as $field )
        {
            if ( $spec[$field] === null )
                continue;
            if ( !isset( $dataMap[$field] ) )
            {
                $r['errors'][] = 'the class ' . $object->attribute( 'class_identifier' ) . " has no attribute $field";
                continue;
            }
            $value = trim( (string)$spec[$field] );
            if ( $value !== (string)$dataMap[$field]->toString() )
            {
                $fields[$field] = $value;
                $r['changes'][] = 'change ' . str_replace( '_', ' ', $field ) . " to $value";
            }
        }

        $member = self::memberships( $object );
        $add = array();
        $remove = array();
        foreach ( (array)$spec['add_groups'] as $ref )
        {
            $group = self::findGroup( $ref );
            if ( !$group['node'] )
                $r['errors'][] = '--add-group: ' . $group['error'];
            else if ( isset( $member[(int)$group['node']->attribute( 'node_id' )] ) )
                $r['warnings'][] = 'already a member of ' . $group['node']->attribute( 'name' );
            else
                $add[(int)$group['node']->attribute( 'node_id' )] = $group['node'];
        }
        foreach ( (array)$spec['remove_groups'] as $ref )
        {
            $group = self::findGroup( $ref );
            if ( !$group['node'] )
                $r['errors'][] = '--remove-group: ' . $group['error'];
            else if ( !isset( $member[(int)$group['node']->attribute( 'node_id' )] ) )
                $r['warnings'][] = 'not a member of ' . $group['node']->attribute( 'name' );
            else
                $remove[(int)$group['node']->attribute( 'node_id' )] = $group['node'];
        }
        $main = null;
        if ( trim( (string)$spec['main_group'] ) !== '' )
        {
            $group = self::findGroup( $spec['main_group'] );
            if ( !$group['node'] )
                $r['errors'][] = '--main-group: ' . $group['error'];
            else if ( !isset( $member[(int)$group['node']->attribute( 'node_id' )] ) && !isset( $add[(int)$group['node']->attribute( 'node_id' )] ) )
                $r['errors'][] = '--main-group: the account is not a member of ' . $group['node']->attribute( 'name' ) . ' (add it with --add-group)';
            else if ( isset( $remove[(int)$group['node']->attribute( 'node_id' )] ) )
                $r['errors'][] = '--main-group: ' . $group['node']->attribute( 'name' ) . ' is removed by --remove-group';
            else
                $main = $group['node'];
        }
        if ( count( $member ) + count( $add ) - count( $remove ) < 1 )
            $r['errors'][] = 'an account keeps at least one group: add another first (--add-group), or remove the account';
        $mainParent = (int)$object->mainParentNodeID();
        if ( isset( $remove[$mainParent] ) && $main === null )
        {
            $left = array_diff( array_keys( $member ), array_keys( $remove ) );
            $next = $left ? reset( $left ) : ( $add ? key( $add ) : 0 );
            if ( $next )
            {
                $nextNode = isset( $add[$next] ) ? $add[$next] : eZContentObjectTreeNode::fetch( $next );
                $main = $nextNode;
                $r['warnings'][] = 'the main group is removed: ' . $nextNode->attribute( 'name' ) . ' becomes the main group (choose with --main-group)';
            }
        }
        foreach ( $add as $node )
            $r['changes'][] = sprintf( 'add to %s (node %d)', $node->attribute( 'name' ), $node->attribute( 'node_id' ) );
        foreach ( $remove as $node )
            $r['changes'][] = sprintf( 'remove from %s (node %d)', $node->attribute( 'name' ), $node->attribute( 'node_id' ) );
        if ( $main !== null && (int)$main->attribute( 'node_id' ) !== $mainParent )
            $r['changes'][] = sprintf( 'make %s (node %d) the main group', $main->attribute( 'name' ), $main->attribute( 'node_id' ) );
        else
            $main = null;
        if ( $r['errors'] || !$apply || !$r['changes'] )
            return $r;

        $db = eZDB::instance();
        if ( $email !== null || $login !== null )
        {
            $db->begin();
            try
            {
                if ( $email !== null )
                    $user->setAttribute( 'email', $email );
                if ( $login !== null )
                    $user->setAttribute( 'login', $login );
                $user->store();
            }
            catch ( Throwable $e )
            {
                // nothing of a transaction that failed half way is committed by a later commit()
                if ( $db->transactionCounter() > 0 )
                    $db->rollback();
                throw $e;
            }
            $db->commit();
        }
        if ( $fields && !eZContentFunctions::updateAndPublishObject( $object, array( 'attributes' => $fields ) ) )
            $r['errors'][] = 'the names could not be published (see the debug log)';
        $mainNodeID = (int)eZContentObject::fetch( $id )->attribute( 'main_node_id' );
        foreach ( $add as $parent => $node )
            eZContentOperationCollection::addAssignment( $mainNodeID, $id, array( $parent ) );
        eZContentObject::clearCache( array( $id ) );
        if ( $main !== null )
        {
            $member = self::memberships( eZContentObject::fetch( $id ) );
            $newMain = isset( $member[(int)$main->attribute( 'node_id' )] ) ? $member[(int)$main->attribute( 'node_id' )] : 0;
            if ( $newMain )
                eZContentOperationCollection::updateMainAssignment( $newMain, $id, (int)$main->attribute( 'node_id' ) );
            else
                $r['errors'][] = 'the main group could not be changed: the account has no location in ' . $main->attribute( 'name' );
        }
        eZContentObject::clearCache( array( $id ) );
        $member = self::memberships( eZContentObject::fetch( $id ) );
        $locations = array();
        foreach ( $remove as $parent => $node )
            if ( isset( $member[$parent] ) )
                $locations[] = $member[$parent];
        if ( $locations )
            eZContentOperationCollection::removeNodes( $locations );
        eZUser::purgeUserCacheByUserId( $id );
        eZContentObject::clearCache( array( $id ) );
        $r['applied'] = true;
        return $r;
    }

    /**
     * Removes an account as the administration does: its locations removed for good (accounts are never moved to
     * the trash), which removes the account, its settings and sessions (eZUser::removeUser(), audited as
     * access.user.remove). Refused for protected accounts and the last administrator (refusal()).
     *
     * @param eZUser $user
     * @param int $actorID
     * @param bool $apply
     * @return array
     */
    public static function delete( eZUser $user, $actorID, $apply )
    {
        $r = self::result();
        $id = (int)$user->attribute( 'contentobject_id' );
        $refusal = self::refusal( $id, $actorID );
        if ( $refusal !== '' )
        {
            $r['errors'][] = self::refusalText( $refusal );
            $r['refusal'] = $refusal;
            return $r;
        }
        $object = $user->contentObject();
        $nodes = $object instanceof eZContentObject ? array_values( self::memberships( $object ) ) : array();
        $r['changes'][] = sprintf( 'remove the account %s (id %d) for good%s', $user->attribute( 'login' ), $id,
                                   $nodes ? ' with its ' . count( $nodes ) . ' location(s)' : ', which has no location' );
        if ( !$apply )
            return $r;
        if ( $nodes )
            eZContentObjectTreeNode::removeSubtrees( $nodes, false );
        else if ( $object instanceof eZContentObject )
            $object->purge();
        else
            eZUser::removeUser( $id );
        eZContentObject::clearCache( array( $id ) );
        if ( eZUser::fetch( $id ) instanceof eZUser )
            $r['errors'][] = 'the account is still there: the user who acts may not remove it, or see the debug log';
        else
            $r['applied'] = true;
        return $r;
    }

    /**
     * Enables or disables an account through the setsettings operation of the user module, as Edit user settings
     * and the user hygiene page do (expUserHygiene::perform(); audited as access.user.enable and
     * access.user.disable). Disabling ends the account's sessions, the tracked ones too (expUserSessionTracker::
     * revokeAll(), access.session.revoke). An unactivated account is not enabled here: exp:user:approve finishes its
     * registration.
     *
     * @param eZUser $user
     * @param bool $enable
     * @param int $actorID
     * @param bool $apply
     * @return array changes, applied, errors, warnings, sessions_ended
     */
    public static function setEnabled( eZUser $user, $enable, $actorID, $apply )
    {
        $r = self::result();
        $id = (int)$user->attribute( 'contentobject_id' );
        if ( !$enable )
        {
            $refusal = self::refusal( $id, $actorID );
            if ( $refusal !== '' )
            {
                $r['errors'][] = self::refusalText( $refusal );
                $r['refusal'] = $refusal;
                return $r;
            }
        }
        $setting = eZUserSetting::fetch( $id );
        $enabled = $setting instanceof eZUserSetting && (int)$setting->attribute( 'is_enabled' ) !== 0;
        if ( $enabled === (bool)$enable )
        {
            $r['warnings'][] = 'the account is ' . ( $enable ? 'enabled' : 'disabled' ) . ' already: nothing to do';
            return $r;
        }
        if ( $enable && expUnactivatedUsers::isUnactivated( $id ) )
        {
            $r['errors'][] = 'the account was never activated: ./console exp:user:approve ' . $user->attribute( 'login' ) . ' finishes its registration';
            return $r;
        }
        $r['changes'][] = ( $enable ? 'enable' : 'disable' ) . ' the account ' . $user->attribute( 'login' ) . ( $enable ? '' : ' and end its sessions' );
        if ( !$apply )
            return $r;
        if ( !expUserHygiene::perform( $enable ? 'enable' : 'disable', $id ) )
        {
            $r['errors'][] = 'the setsettings operation did not change the account (a workflow may have stopped it)';
            return $r;
        }
        if ( !$enable )
            $r['sessions_ended'] = self::endSessions( $id, $actorID );
        $r['applied'] = true;
        return $r;
    }

    /**
     * Ends every session of an account: the tracked ones (expUserSessionTracker::revokeAll(), audited) and those of
     * the session handler.
     *
     * @param int $userID
     * @param int $actorID
     * @return int how many tracked sessions were ended
     */
    public static function endSessions( $userID, $actorID )
    {
        $ended = 0;
        if ( class_exists( 'expUserSessionTracker' ) && expUserSessionTracker::enabled() && expUserSessionTracker::available() )
        {
            try
            {
                $ended = expUserSessionTracker::revokeAll( (int)$userID, (int)$actorID );
            }
            catch ( Throwable $e )
            {
                eZDebug::writeError( 'Could not end the tracked sessions: ' . $e->getMessage(), __METHOD__ );
            }
        }
        try
        {
            eZUser::removeSessionData( (int)$userID );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Could not end the sessions: ' . $e->getMessage(), __METHOD__ );
        }
        return $ended;
    }

    /**
     * Resets a password. send-link: the forgot-password mail with its one-time link (sendResetLink()), the password
     * stays until the link is used. generate: a new password (generatePassword()), returned once. stdin: the password
     * given, checked against the password rules. Both set it by the password operation of the user module (audited
     * as access.user.password.change) and end the account's sessions. With $requireChange the account must choose a
     * new password at its next sign-in (expPasswordChangeRequired). Refused in demo mode for an administrator.
     *
     * @param eZUser $user
     * @param string $mode one of RESET_MODES
     * @param string|null $password for stdin
     * @param bool $requireChange
     * @param int $actorID
     * @param bool $apply
     * @return array changes, applied, errors, warnings, generated_password (generate, applied), mail_sent (send-link)
     */
    public static function resetPassword( eZUser $user, $mode, $password, $requireChange, $actorID, $apply )
    {
        $r = self::result();
        $id = (int)$user->attribute( 'contentobject_id' );
        if ( !in_array( $mode, self::RESET_MODES, true ) )
            $r['errors'][] = 'choose one of --send-link, --generate and --password-stdin';
        else if ( $id === (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) )
            $r['errors'][] = self::refusalText( 'anonymous' );
        else if ( class_exists( 'expDemoMode' ) && expDemoMode::protectsPassword( $user ) )
            $r['errors'][] = 'demo mode (EXP_DEMO_MODE=1): the password of an administrator is never changed or reset';
        if ( $mode === 'stdin' )
            foreach ( self::passwordProblems( (string)$password, $user ) as $message )
                $r['errors'][] = $message;
        if ( $mode === 'send-link' )
        {
            $email = trim( (string)$user->attribute( 'email' ) );
            if ( !eZMail::validate( $email ) )
                $r['errors'][] = 'the account has no valid e-mail address to send the link to';
            if ( $requireChange )
                $r['warnings'][] = '--require-change has no effect with --send-link: the person chooses the password';
            $requireChange = false;
        }
        if ( $r['errors'] )
            return $r;
        if ( $mode === 'send-link' )
            $r['changes'][] = 'send the reset link to the account\'s e-mail address (the password stays until the link is used)';
        else
            $r['changes'][] = ( $mode === 'generate' ? 'set a generated password (shown once)' : 'set the password read from standard input' )
                            . ' and end the account\'s sessions';
        if ( $requireChange )
            $r['changes'][] = 'require a new password at the next sign-in';
        if ( !$apply )
            return $r;

        if ( $mode === 'send-link' )
        {
            $r['mail_sent'] = self::sendResetLink( $user );
            if ( !$r['mail_sent'] )
                $r['errors'][] = 'the mail could not be handed to the mail transport';
            else
                $r['applied'] = true;
            return $r;
        }
        if ( $mode === 'generate' )
            $password = self::generatePassword();
        $result = eZOperationHandler::operationIsAvailable( 'user_password' )
                ? eZOperationHandler::execute( 'user', 'password', array( 'user_id' => $id, 'new_password' => (string)$password ) )
                : eZUserOperationCollection::password( $id, (string)$password );
        $done = is_array( $result ) && isset( $result['status'] )
              && ( $result['status'] === true || (int)$result['status'] === eZModuleOperationInfo::STATUS_CONTINUE );
        if ( !$done )
        {
            $r['errors'][] = 'the password operation did not change the password' . ( isset( $result['reason'] ) ? ' (' . $result['reason'] . ')' : '' );
            return $r;
        }
        $r['sessions_ended'] = self::endSessions( $id, $actorID );
        if ( $requireChange && class_exists( 'expPasswordChangeRequired' ) )
            expPasswordChangeRequired::set( eZUser::fetch( $id ), 'console' );
        if ( $mode === 'generate' )
            $r['generated_password'] = $password;
        $r['applied'] = true;
        return $r;
    }

    /**
     * Sends the "reset your password" mail of user/forgotpassword with a new one-time key (the earlier keys of the
     * account removed): design:user/forgotpasswordmail.tpl with link, category security, audited as
     * access.user.password.reset.request. Never the key in the audit. Shared by the console and the account service.
     *
     * @param eZUser $user
     * @return bool the mail was handed to the transport
     */
    public static function sendResetLink( eZUser $user )
    {
        $id = (int)$user->attribute( 'contentobject_id' );
        $hash = bin2hex( random_bytes( 16 ) );
        eZForgotPassword::removeByUserID( $id );
        eZUserOperationCollection::forgotpassword( $id, $hash, time() );
        $ini = eZINI::instance();
        $lifetime = eZForgotPassword::lifetime( $ini );
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'user', $user );
        $tpl->setVariable( 'object', $user->attribute( 'contentobject' ) );
        $tpl->setVariable( 'password', '' );
        $tpl->setVariable( 'link', true );
        $tpl->setVariable( 'hash_key', $hash );
        $tpl->setVariable( 'key_lifetime_minutes', $lifetime > 0 ? (int)ceil( $lifetime / 60 ) : 0 );
        $body = $tpl->fetch( 'design:user/forgotpasswordmail.tpl' );
        $mail = new eZMail();
        if ( $tpl->hasVariable( 'content_type' ) )
            $mail->setContentType( $tpl->variable( 'content_type' ) );
        $sender = $ini->variable( 'MailSettings', 'EmailSender' ) ?: $ini->variable( 'MailSettings', 'AdminEmail' );
        $mail->setSender( $sender );
        $mail->setReceiver( (string)$user->attribute( 'email' ) );
        $mail->setSubject( $tpl->hasVariable( 'subject' ) ? $tpl->variable( 'subject' ) : ezpI18n::tr( 'kernel/user/register', 'Registration info' ) );
        expMailTemplateBody::apply( $mail, $tpl, $body );
        $mail->setCategory( 'security' );
        $sent = self::$mailSender !== null ? (bool)call_user_func( self::$mailSender, $mail ) : (bool)eZMailTransport::send( $mail );
        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::emit( 'access.user.password.reset.request', array( 'object' => expAuditHook::user( $user ), 'after' => array( 'mail_sent' => $sent ) ) );
        return $sent;
    }

    /**
     * Approves or rejects a waiting registration (expRegistrationAdminAction: approve activates the account or accepts
     * its approval item and continues the workflow; reject denies the item and removes the account), audited as
     * access.registration.approve and access.registration.reject.
     *
     * @param eZUser $user
     * @param bool $reject
     * @param int $actorID
     * @param bool $apply
     * @return array changes, applied, errors, warnings, state_before, state
     */
    public static function decideRegistration( eZUser $user, $reject, $actorID, $apply )
    {
        $r = self::result();
        $id = (int)$user->attribute( 'contentobject_id' );
        $state = expRegistrationAdminAction::state( $id );
        $r['state_before'] = $state;
        $r['state'] = $state;
        $action = $reject ? expRegistrationAdminAction::ACTION_REJECT : expRegistrationAdminAction::ACTION_APPROVE;
        if ( !expRegistrationAdminAction::allowedInState( $action, $state ) )
        {
            $r['errors'][] = "the registration does not wait (its state is $state): only one waiting for its e-mail address, an administrator or an approval can be "
                           . ( $reject ? 'rejected' : 'approved' );
            return $r;
        }
        if ( $reject && expRegistrationAdminAction::isProtected( $id, expRegistrationAdminAction::protectedIDs( $actorID ) ) )
        {
            $r['errors'][] = self::refusalText( self::protectedReason( $id, $actorID ) ?: 'admin' );
            return $r;
        }
        $what = array( expRegistrationAdminAction::STATE_EMAIL => 'activate the account (its e-mail address was not confirmed)',
                       expRegistrationAdminAction::STATE_ADMINISTRATOR => 'activate the account',
                       expRegistrationAdminAction::STATE_WORKFLOW => 'approve the waiting approval item and publish the account' );
        $r['changes'][] = $reject ? 'reject the registration and remove the account ' . $user->attribute( 'login' ) . ' for good'
                                  : $what[$state];
        if ( !$apply )
            return $r;
        $result = $reject ? expRegistrationAdminAction::reject( $id, $actorID ) : expRegistrationAdminAction::approve( $id );
        $r['state'] = $result['state'];
        if ( $result['result'] === 'refused' )
            $r['errors'][] = 'refused' . ( isset( $result['reason'] ) ? ': ' . $result['reason'] : '' );
        else
            $r['applied'] = true;
        return $r;
    }

    // ---- sign-in security ---------------------------------------------------------------------------------------

    /**
     * Unlocks an account: its failed sign-ins back to 0 (eZUser::setFailedLoginAttempts(), audited as
     * access.user.unlock), as Users > Hygiene does. The password stays.
     *
     * @param eZUser $user
     * @param bool $apply
     * @return array
     */
    public static function unlock( eZUser $user, $apply )
    {
        $r = self::result();
        $id = (int)$user->attribute( 'contentobject_id' );
        $failed = (int)eZUser::failedLoginAttemptsByUserID( $id );
        if ( $failed <= 0 )
        {
            $r['warnings'][] = 'the account has no failed sign-ins: nothing to do';
            return $r;
        }
        $max = (int)eZUser::maxNumberOfFailedLogin();
        $r['changes'][] = sprintf( 'reset the %d failed sign-in(s) of %s to 0%s', $failed, $user->attribute( 'login' ),
                                   $max > 0 && $failed > $max ? ' (it is locked: more than MaxNumberOfFailedLogin=' . $max . ')' : '' );
        if ( !$apply )
            return $r;
        eZUser::setFailedLoginAttempts( $id, 0, true );
        $r['applied'] = (int)eZUser::failedLoginAttemptsByUserID( $id ) === 0;
        if ( !$r['applied'] )
            $r['errors'][] = 'the failed sign-ins could not be reset (see the debug log)';
        return $r;
    }

    /** @return bool the sessions are tracked here (expUserSessionTracker: switched on, the table there) */
    public static function sessionsTracked()
    {
        return class_exists( 'expUserSessionTracker' ) && expUserSessionTracker::enabled() && expUserSessionTracker::available();
    }

    /**
     * The running sessions of an account as Your devices shows them.
     *
     * @param eZUser $user
     * @return array tracking (bool), sessions (id, method, device, browser, label, ip, siteaccess, created, last_active, current)
     */
    public static function sessions( eZUser $user )
    {
        if ( !self::sessionsTracked() )
            return array( 'tracking' => false, 'sessions' => array() );
        $list = expUserSessionTracker::listForUser( (int)$user->attribute( 'contentobject_id' ), false );
        $out = array();
        foreach ( $list['active'] as $s )
            $out[] = array( 'id' => (int)$s['id'], 'method' => (string)$s['method'], 'device' => (string)$s['device'], 'browser' => (string)$s['browser'],
                            'label' => (string)$s['label'], 'ip' => (string)$s['ip'], 'siteaccess' => (string)$s['siteaccess'],
                            'created' => (int)$s['created'], 'last_active' => (int)$s['last_active'], 'current' => !empty( $s['current'] ) );
        return array( 'tracking' => true, 'sessions' => $out );
    }

    /**
     * Ends the sessions of an account: one tracked session (expUserSessionTracker::revoke()), or all of them
     * (endSessions(): the tracked ones and the session handler's), as an administrator does on user/sessions.
     *
     * @param eZUser $user
     * @param int|null $sessionID null for all
     * @param int $actorID
     * @param bool $apply
     * @return array changes, applied, errors, warnings, sessions_ended
     */
    public static function endSessionsOf( eZUser $user, $sessionID, $actorID, $apply )
    {
        $r = self::result();
        $id = (int)$user->attribute( 'contentobject_id' );
        $list = self::sessions( $user );
        if ( $sessionID !== null )
        {
            if ( !$list['tracking'] )
                $r['errors'][] = 'sessions are not tracked here ([UserSessionSettings] Tracking, or the table expuser_session is missing): --end ends them all';
            else if ( !in_array( (int)$sessionID, array_column( $list['sessions'], 'id' ), true ) )
                $r['errors'][] = "the account has no running session $sessionID (the list shows the running ones)";
            if ( $r['errors'] )
                return $r;
            $r['changes'][] = "end the session $sessionID of " . $user->attribute( 'login' );
            if ( !$apply )
                return $r;
            $r['applied'] = expUserSessionTracker::revoke( $id, (int)$sessionID, (int)$actorID );
            $r['sessions_ended'] = $r['applied'] ? 1 : 0;
            if ( !$r['applied'] )
                $r['errors'][] = 'the session had ended already';
            return $r;
        }
        $r['changes'][] = $list['tracking'] ? sprintf( 'end the %d running session(s) of %s', count( $list['sessions'] ), $user->attribute( 'login' ) )
                                            : 'end every session of ' . $user->attribute( 'login' ) . ' the session handler has';
        if ( !$apply )
            return $r;
        $r['sessions_ended'] = self::endSessions( $id, $actorID );
        $r['applied'] = true;
        return $r;
    }

    /**
     * The two-step sign-in of an account: whether it can have one (the extension's field on its class), whether it
     * is on and its method, and how many passkeys it has (null where passkeys cannot be used).
     *
     * @param eZUser $user
     * @return array available, active, method, passkeys
     */
    public static function twoFactor( eZUser $user )
    {
        $state = expUserAccountOverview::twoFactorOf( $user->contentObject() );
        $passkeys = null;
        try
        {
            if ( class_exists( 'expPasskeySchema' ) && expPasskeySchema::exists() === true )
                $passkeys = expPasskey::countByUser( (int)$user->attribute( 'contentobject_id' ) );
        }
        catch ( Throwable $e )
        {
            $passkeys = null;
        }
        return array( 'available' => is_array( $state ), 'active' => is_array( $state ) && !empty( $state['active'] ),
                      'method' => is_array( $state ) ? (string)$state['method'] : '', 'passkeys' => $passkeys );
    }

    /**
     * Removes the second sign-in step of an account (the extension sevenx_authentication_2fa: its stored setup
     * emptied and the failed sign-ins reset, as its console script does; audited as access.user.two_factor.remove)
     * and/or every passkey (expPasskeyService::revoke(), audited as access.passkey.revoke).
     *
     * @param eZUser $user
     * @param bool $reset
     * @param bool $removePasskeys
     * @param bool $apply
     * @return array
     */
    public static function resetTwoFactor( eZUser $user, $reset, $removePasskeys, $apply )
    {
        $r = self::result();
        $id = (int)$user->attribute( 'contentobject_id' );
        $state = self::twoFactor( $user );
        $helper = null;
        // the classes of the extension sevenx_authentication_2fa, named as strings: the kernel does not need it
        $helperClass = 'sevenxAuthentication2faHelper';
        $dataClass = 'sevenxAuthentication2fa';
        if ( $reset )
        {
            if ( !class_exists( $helperClass ) || !class_exists( $dataClass ) )
                $r['errors'][] = '--reset needs the two-step sign-in extension sevenx_authentication_2fa, which is not active';
            else
            {
                $helper = call_user_func( array( $helperClass, 'instance' ) );
                if ( !$helper->canStore( $id ) )
                    $r['errors'][] = 'the class of the account has no two-step sign-in field: there is nothing to reset';
                else if ( !$state['active'] )
                    $r['warnings'][] = 'the account has no second sign-in step: nothing to reset';
                else
                    $r['changes'][] = 'remove the second sign-in step (' . $state['method'] . ') of ' . $user->attribute( 'login' ) . ' and reset its failed sign-ins';
            }
        }
        if ( $removePasskeys )
        {
            if ( $state['passkeys'] === null )
                $r['warnings'][] = 'passkeys cannot be used here (no table expuser_passkey): nothing to remove';
            else if ( $state['passkeys'] === 0 )
                $r['warnings'][] = 'the account has no passkeys: nothing to remove';
            else
                $r['changes'][] = sprintf( 'remove the %d passkey(s) of %s', $state['passkeys'], $user->attribute( 'login' ) );
        }
        if ( $r['errors'] || !$apply || !$r['changes'] )
            return $r;
        if ( $reset && $helper && $state['active'] )
        {
            $helper->saveUserData( $id, new $dataClass() );
            eZUser::setFailedLoginAttempts( $id, 0, true );
            eZContentCacheManager::clearContentCacheIfNeeded( $id );
            if ( method_exists( $helperClass, 'authLog' ) )
                call_user_func( array( $helperClass, 'authLog' ), '2fa_reset_console', 'second step removed and failed logins reset from the console (exp:user:2fa)', $id );
            expSignInSecondFactor::audit( expSignInSecondFactor::AUDIT_2FA_REMOVE, $user, array( 'method' => $state['method'], 'by' => 'console' ) );
        }
        if ( $removePasskeys && $state['passkeys'] )
        {
            $service = new expPasskeyService();
            foreach ( expPasskey::fetchByUser( $id ) as $passkey )
                if ( !$service->revoke( $user, (int)$passkey->attribute( 'id' ) ) )
                    $r['errors'][] = 'the passkey ' . (int)$passkey->attribute( 'id' ) . ' could not be removed';
        }
        $r['applied'] = !$r['errors'];
        return $r;
    }

    /**
     * Sets or clears the mark "change password at next sign-in" (expPasswordChangeRequired, audited as
     * access.user.password.change_required.set and .clear).
     *
     * @param eZUser $user
     * @param bool $set
     * @param bool $apply
     * @return array
     */
    public static function markPasswordChange( eZUser $user, $set, $apply )
    {
        $r = self::result();
        if ( $user->isAnonymous() )
        {
            $r['errors'][] = self::refusalText( 'anonymous' );
            return $r;
        }
        $marked = expPasswordChangeRequired::isMarked( $user );
        if ( $marked === (bool)$set )
        {
            $r['warnings'][] = $set ? 'a new password is required at the next sign-in already: nothing to do' : 'no new password is required: nothing to do';
            return $r;
        }
        $r['changes'][] = $set ? 'require a new password at the next sign-in of ' . $user->attribute( 'login' )
                                : 'no longer require a new password at the next sign-in of ' . $user->attribute( 'login' );
        if ( !$apply )
            return $r;
        $r['applied'] = $set ? expPasswordChangeRequired::set( $user, 'console' ) : expPasswordChangeRequired::clear( $user, 'console' );
        if ( !$r['applied'] )
            $r['errors'][] = 'the mark could not be changed (see the debug log)';
        return $r;
    }

    /**
     * Sends the activation mail again, with a new key, to an account that never activated
     * (expUnactivatedUsers::resend(), as Resend in Users > Unactivated).
     *
     * @param eZUser $user
     * @param bool $apply
     * @return array
     */
    public static function resendActivation( eZUser $user, $apply )
    {
        $r = self::result();
        $id = (int)$user->attribute( 'contentobject_id' );
        if ( !expUnactivatedUsers::isUnactivated( $id ) )
            $r['errors'][] = 'the account does not wait for its activation (registration state ' . expRegistrationAdminAction::state( $id ) . ')';
        else if ( !eZMail::validate( trim( (string)$user->attribute( 'email' ) ) ) )
            $r['errors'][] = 'the account has no valid e-mail address: ./console exp:user:approve ' . $user->attribute( 'login' ) . ' activates it without a mail';
        if ( $r['errors'] )
            return $r;
        $r['changes'][] = 'send the activation mail again to ' . $user->attribute( 'email' ) . ' with a new key (the link of an older mail stops working)';
        if ( !$apply )
            return $r;
        $sent = expUnactivatedUsers::resend( $id, self::$mailSender );
        $r['applied'] = $sent === 'sent';
        if ( !$r['applied'] )
            $r['errors'][] = "the mail was not sent ($sent)";
        return $r;
    }

    /**
     * Whether a password signs an account in: the right password (the stored hash compared), the account enabled,
     * not locked and with its object. Never signs in, opens no session, counts no failed sign-in, records nothing.
     *
     * @param eZUser $user
     * @param string $password
     * @return array valid, reason (ok, wrong_password, no_object, disabled, locked)
     */
    public static function checkLogin( eZUser $user, $password )
    {
        $right = eZUser::authenticateHash( (string)$user->attribute( 'login' ), (string)$password, eZUser::site(),
                                           $user->attribute( 'password_hash_type' ), $user->attribute( 'password_hash' ) );
        $reason = 'ok';
        if ( !$right )
            $reason = 'wrong_password';
        else if ( !$user->contentObject() instanceof eZContentObject )
            $reason = 'no_object';
        else if ( !$user->isEnabled( false ) )
            $reason = 'disabled';
        else if ( $user->isLocked() )
            $reason = 'locked';
        return array( 'valid' => $reason === 'ok', 'reason' => $reason );
    }

    // ---- the user audit -------------------------------------------------------------------------------------------

    /**
     * The filters of exp:user:audit, checked: event (a name or prefix), result, from and to (YYYY-MM-DD), limit (1 to
     * MAX_LIMIT, default 25), offset. Pure.
     *
     * @param array $input
     * @return array filters, errors
     */
    public static function auditFilters( array $input )
    {
        $get = function ( $name, $default = '' ) use ( $input ) {
            return isset( $input[$name] ) && is_scalar( $input[$name] ) && (string)$input[$name] !== '' ? trim( (string)$input[$name] ) : $default;
        };
        $errors = array();
        $event = strtolower( $get( 'event' ) );
        if ( $event !== '' && !preg_match( '/^[a-z0-9_.]+\*?$/', $event ) )
            $errors[] = "--event takes an event name or prefix (letters, digits, _ and .), not $event";
        $result = $get( 'result' );
        if ( $result !== '' && !in_array( $result, array( 'success', 'refused', 'failed' ), true ) )
            $errors[] = "--result must be success, refused or failed, not $result";
        $days = array();
        foreach ( array( 'from', 'to' ) as $name )
        {
            $d = $get( $name );
            if ( $d !== '' && !( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $d ) && checkdate( (int)substr( $d, 5, 2 ), (int)substr( $d, 8, 2 ), (int)substr( $d, 0, 4 ) ) ) )
            {
                $errors[] = "--$name must be a day YYYY-MM-DD, not $d";
                $d = '';
            }
            $days[$name] = $d;
        }
        $limit = $get( 'limit', '25' );
        if ( !ctype_digit( $limit ) || (int)$limit < 1 || (int)$limit > self::MAX_LIMIT )
        {
            $errors[] = '--limit must be a whole number from 1 to ' . self::MAX_LIMIT . ", not $limit";
            $limit = '25';
        }
        $offset = $get( 'offset', '0' );
        if ( !ctype_digit( $offset ) )
        {
            $errors[] = "--offset must be a whole number, not $offset";
            $offset = '0';
        }
        return array( 'filters' => array( 'action' => rtrim( rtrim( $event, '*' ), '.' ), 'result' => $result, 'from' => $days['from'], 'to' => $days['to'],
                                          'limit' => (int)$limit, 'offset' => (int)$offset ),
                      'errors' => $errors );
    }

    /**
     * The rows of the user audit log (expuser_audit) about an account or by it, newest first.
     *
     * @param eZUser $user
     * @param array $filters see auditFilters()
     * @return array total, rows (time, event, result, reason, actor_login, target_login, siteaccess, before, after), error
     */
    public static function auditRows( eZUser $user, array $filters )
    {
        if ( expUserAudit::exists() !== true )
            return array( 'total' => 0, 'rows' => array(),
                          'error' => 'the user audit table expuser_audit does not exist (update/common/scripts/6.0/createuseraudittable.php adds it)' );
        $f = array( 'user' => (string)(int)$user->attribute( 'contentobject_id' ), 'action' => $filters['action'], 'result' => $filters['result'],
                    'from' => $filters['from'], 'to' => $filters['to'] );
        $rows = array();
        foreach ( expUserAudit::fetch( $f, $filters['offset'], $filters['limit'] ) as $row )
            $rows[] = array( 'time' => gmdate( 'Y-m-d\TH:i:s\Z', (int)$row['created'] ), 'event' => (string)$row['event_name'],
                             'result' => (string)$row['result'], 'reason' => isset( $row['reason'] ) ? (string)$row['reason'] : '',
                             'actor_login' => isset( $row['actor_login'] ) ? (string)$row['actor_login'] : '',
                             'target_login' => isset( $row['target_login'] ) ? (string)$row['target_login'] : '',
                             'siteaccess' => isset( $row['siteaccess'] ) ? (string)$row['siteaccess'] : '',
                             'before' => $row['before'], 'after' => $row['after'] );
        return array( 'total' => expUserAudit::count( $f ), 'rows' => $rows, 'error' => '' );
    }

    /**
     * An account as the commands print it: never a hash.
     *
     * @param eZUser $user
     * @return array id, login, email, name, enabled
     */
    public static function summary( eZUser $user )
    {
        $id = (int)$user->attribute( 'contentobject_id' );
        $object = $user->contentObject();
        $setting = eZUserSetting::fetch( $id );
        return array( 'id' => $id, 'login' => (string)$user->attribute( 'login' ), 'email' => (string)$user->attribute( 'email' ),
                      'name' => $object instanceof eZContentObject ? (string)$object->attribute( 'name' ) : '',
                      'enabled' => $setting instanceof eZUserSetting && (int)$setting->attribute( 'is_enabled' ) !== 0 );
    }
}
