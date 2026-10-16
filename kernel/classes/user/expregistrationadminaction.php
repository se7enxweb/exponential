<?php
/**
 * File containing the expRegistrationAdminAction class.
 *
 * The signed links of the administrator's "New user registered" mail (design:user/registrationfeedback.tpl) and the
 * actions behind them (user/registration, doc/features/6.0/registration-admin-links.md).
 *
 * A link carries a token: "<user id>.<action>.<expiry>.<nonce>.<signature>", the signature an HMAC-SHA-256 over the
 * other four parts under a key derived from the site secret (expMailSecret::derive( 'registration-action' )). It
 * cannot be made, changed or moved to another account or action without the secret, and it ends at its expiry
 * (site.ini [UserSettings] RegistrationActionTTL, 7 days by default). An action link (approve, reject) works once:
 * the action stores its nonce (ezsite_data, a primary key, so two requests cannot both use it) before it acts. The
 * view link only shows the account and may be opened until it expires.
 *
 * The page the link opens (kernel/private/classes/views/user/registration.php) requires a signed-in user with the
 * policy (may()), asks for a confirmation and acts only on a POST with the form token: mail scanners fetch links.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expRegistrationAdminAction
{
    const VERSION = 'v1';

    /** The actions a token can carry */
    const ACTION_VIEW = 'view';
    const ACTION_APPROVE = 'approve';
    const ACTION_REJECT = 'reject';

    /** The states of a registration (state()) */
    const STATE_MISSING = 'missing';        // no such account (removed, or never a user)
    const STATE_EMAIL = 'email';            // disabled, waiting for the user to confirm the e-mail address
    const STATE_ADMINISTRATOR = 'administrator'; // disabled without an activation key: waiting for an administrator
    const STATE_WORKFLOW = 'workflow';      // its publication waits for an approval (collaboration item)
    const STATE_PENDING = 'pending';        // enabled, but the user object is not published (a workflow still runs)
    const STATE_ACTIVE = 'active';          // enabled and published
    const STATE_DISABLED = 'disabled';      // disabled without an activation key after it had signed in: an
                                            // administrator deactivated it; not a registration that waits

    /** Default lifetime of a link, in seconds (7 days) */
    const DEFAULT_TTL = 604800;

    /** The prefix of the used-token rows in ezsite_data */
    const USED_PREFIX = 'exp_regaction_';

    /** @var string|null test override of the key (raw bytes) */
    protected static $testKey = null;

    /** @var array|null test override of the used-token store: nonce => expiry */
    protected static $testUsed = null;

    /** @var string|null why the last verify() refused: malformed, signature, expired, action, used */
    protected static $lastError = null;

    /** @return string[] */
    public static function actions()
    {
        return array( self::ACTION_VIEW, self::ACTION_APPROVE, self::ACTION_REJECT );
    }

    /**
     * A token for one account and one action.
     *
     * @param int $userID
     * @param string $action view|approve|reject
     * @param int|null $ttl seconds; null: ttl()
     * @param int|null $now
     * @return string
     */
    public static function create( $userID, $action, $ttl = null, $now = null )
    {
        if ( !in_array( $action, self::actions(), true ) )
            throw new InvalidArgumentException( "Unknown registration action '$action'" );
        $now = $now === null ? time() : (int)$now;
        $expiry = $now + ( $ttl === null ? self::ttl() : max( 1, (int)$ttl ) );
        $nonce = bin2hex( random_bytes( 12 ) );
        return self::sign( (int)$userID, $action, $expiry, $nonce );
    }

    /**
     * The token of these parts (the signature appended).
     *
     * @param int $userID
     * @param string $action
     * @param int $expiry
     * @param string $nonce hex
     * @return string
     */
    public static function sign( $userID, $action, $expiry, $nonce )
    {
        $payload = (int)$userID . '.' . $action . '.' . (int)$expiry . '.' . $nonce;
        return $payload . '.' . self::signature( $payload );
    }

    /**
     * Checks a token. Nothing is stored: a used action token is refused, but verify() does not use it (consume() does).
     *
     * @param string $token
     * @param string|null $action the action the page expects; null: any
     * @param int|null $now
     * @return array|false user_id, action, expiry, nonce; false with lastError() set
     */
    public static function verify( $token, $action = null, $now = null )
    {
        self::$lastError = null;
        $parts = explode( '.', (string)$token );
        if ( count( $parts ) !== 5 || !ctype_digit( $parts[0] ) || !ctype_digit( $parts[2] ) ||
             !in_array( $parts[1], self::actions(), true ) || !preg_match( '/^[0-9a-f]{24}$/', $parts[3] ) ||
             !preg_match( '/^[A-Za-z0-9_-]{43}$/', $parts[4] ) || (int)$parts[0] <= 0 )
        {
            self::$lastError = 'malformed';
            return false;
        }
        $payload = $parts[0] . '.' . $parts[1] . '.' . $parts[2] . '.' . $parts[3];
        if ( !hash_equals( self::signature( $payload ), $parts[4] ) )
        {
            self::$lastError = 'signature';
            return false;
        }
        $now = $now === null ? time() : (int)$now;
        if ( (int)$parts[2] < $now )
        {
            self::$lastError = 'expired';
            return false;
        }
        if ( $action !== null && $parts[1] !== $action )
        {
            self::$lastError = 'action';
            return false;
        }
        if ( $parts[1] !== self::ACTION_VIEW && self::isUsed( $parts[3] ) )
        {
            self::$lastError = 'used';
            return false;
        }
        return array( 'user_id' => (int)$parts[0], 'action' => $parts[1], 'expiry' => (int)$parts[2], 'nonce' => $parts[3] );
    }

    /**
     * Verifies an action token and marks it used, in one step: of two requests with the same token only one gets it.
     *
     * @param string $token
     * @param string|null $action
     * @param int|null $now
     * @return array|false as verify()
     */
    public static function consume( $token, $action = null, $now = null )
    {
        $data = self::verify( $token, $action, $now );
        if ( $data === false )
            return false;
        if ( $data['action'] === self::ACTION_VIEW )
            return $data;
        if ( !self::markUsed( $data['nonce'], $data['expiry'] ) )
        {
            self::$lastError = 'used';
            return false;
        }
        return $data;
    }

    /**
     * @param string $payload
     * @return string base64url of the HMAC-SHA-256 (43 characters)
     */
    protected static function signature( $payload )
    {
        $mac = hash_hmac( 'sha256', self::VERSION . '|' . $payload, self::key(), true );
        return rtrim( strtr( base64_encode( $mac ), '+/', '-_' ), '=' );
    }

    /** @return string the raw key: derived from the site secret (expMailSecret), never the secret itself */
    protected static function key()
    {
        if ( self::$testKey !== null )
            return self::$testKey;
        return expMailSecret::derive( 'registration-action' );
    }

    /**
     * Tests only: a fixed key (raw bytes) and an in-memory used-token store; null resets both.
     *
     * @param string|null $key
     * @param array|null $used nonce => expiry
     */
    public static function setForTest( $key = null, ?array $used = null )
    {
        self::$testKey = $key;
        self::$testUsed = $key === null ? null : ( $used === null ? array() : $used );
    }

    /** @return string|null why the last verify() or consume() refused */
    public static function lastError()
    {
        return self::$lastError;
    }

    /** @return int the lifetime of a link in seconds: site.ini [UserSettings] RegistrationActionTTL */
    public static function ttl()
    {
        $ini = eZINI::instance();
        $ttl = $ini->hasVariable( 'UserSettings', 'RegistrationActionTTL' ) ? (int)$ini->variable( 'UserSettings', 'RegistrationActionTTL' ) : 0;
        return $ttl > 0 ? $ttl : self::DEFAULT_TTL;
    }

    /** @return bool site.ini [UserSettings] RegistrationAdminLinks is not "disabled" */
    public static function enabled()
    {
        $ini = eZINI::instance();
        return !$ini->hasVariable( 'UserSettings', 'RegistrationAdminLinks' ) || $ini->variable( 'UserSettings', 'RegistrationAdminLinks' ) !== 'disabled';
    }

    // ---- used tokens ------------------------------------------------------------------------------------------

    /**
     * @param string $nonce
     * @return bool
     */
    public static function isUsed( $nonce )
    {
        if ( self::$testUsed !== null )
            return isset( self::$testUsed[$nonce] );
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT value FROM ezsite_data WHERE name = '" . $db->escapeString( self::USED_PREFIX . $nonce ) . "'" );
        return !empty( $rows );
    }

    /**
     * Stores the nonce as used; false when it was used already. The rows of expired tokens are removed on the way.
     *
     * @param string $nonce
     * @param int $expiry
     * @return bool
     */
    public static function markUsed( $nonce, $expiry )
    {
        if ( self::$testUsed !== null )
        {
            if ( isset( self::$testUsed[$nonce] ) )
                return false;
            self::$testUsed[$nonce] = (int)$expiry;
            return true;
        }
        $db = eZDB::instance();
        $name = $db->escapeString( self::USED_PREFIX . $nonce );
        if ( self::isUsed( $nonce ) )
            return false;
        // a duplicate key throws instead of reporting a database error (the mode is set back to the default after)
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            $db->query( "INSERT INTO ezsite_data ( name, value ) VALUES ( '$name', '" . (int)$expiry . "' )" );
        }
        catch ( Throwable $e )
        {
            // the primary key: another request stored it a moment ago
            return false;
        }
        finally
        {
            $db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        }
        self::purgeUsed();
        return true;
    }

    /** Removes the used-token rows whose tokens have expired. */
    public static function purgeUsed( $now = null )
    {
        if ( self::$testUsed !== null )
            return;
        $now = $now === null ? time() : (int)$now;
        $db = eZDB::instance();
        // now and then (one consume in 20): the rows are few, a link is used once
        if ( random_int( 1, 20 ) !== 1 )
            return;
        foreach ( $db->arrayQuery( "SELECT name, value FROM ezsite_data WHERE name LIKE '" . self::USED_PREFIX . "%'" ) as $row )
        {
            if ( (int)$row['value'] < $now )
                $db->query( "DELETE FROM ezsite_data WHERE name = '" . $db->escapeString( $row['name'] ) . "'" );
        }
    }

    // ---- links ------------------------------------------------------------------------------------------------

    /**
     * The address the links point to: scheme, host and siteaccess path of the administration, no trailing slash.
     * site.ini [UserSettings] RegistrationAdminBaseURL when set, else the administration siteaccess's SiteURL (always
     * https), else what ezpSiteAccessURL::root() makes of it (https).
     *
     * @return string
     */
    public static function adminBaseURL()
    {
        $ini = eZINI::instance();
        $setting = $ini->hasVariable( 'UserSettings', 'RegistrationAdminBaseURL' ) ? (string)$ini->variable( 'UserSettings', 'RegistrationAdminBaseURL' ) : '';
        $siteaccess = self::adminSiteAccess();
        $siteURL = '';
        $root = '';
        if ( trim( $setting ) === '' && $siteaccess !== '' )
        {
            try
            {
                $saIni = eZSiteAccess::getIni( $siteaccess, 'site.ini' );
                if ( $saIni && $saIni->hasVariable( 'SiteSettings', 'SiteURL' ) )
                    $siteURL = (string)$saIni->variable( 'SiteSettings', 'SiteURL' );
            }
            catch ( Throwable $e )
            {
            }
            if ( class_exists( 'ezpSiteAccessURL' ) )
            {
                $r = ezpSiteAccessURL::root( $siteaccess );
                $root = is_string( $r ) ? $r : '';
            }
        }
        return self::buildBaseURL( $setting, $siteURL, $root );
    }

    /**
     * The base URL from its sources (pure, for the tests): the setting as it is (http only when it says so),
     * else the SiteURL with https (a loopback or example host is skipped), else the root URL with https.
     *
     * @param string $setting
     * @param string $siteURL
     * @param string $rootURL
     * @return string no trailing slash; '' when none of them gives one
     */
    public static function buildBaseURL( $setting, $siteURL, $rootURL )
    {
        $setting = trim( (string)$setting );
        $setting = ltrim( $setting, '/' );
        if ( $setting !== '' && preg_match( '#^https?://#i', $setting ) )
            return rtrim( $setting, '/' );
        // another scheme (ftp://, javascript:) is not an address of the administration: ignored
        if ( $setting !== '' && !preg_match( '#^[a-z][a-z0-9+.-]*:#i', $setting ) )
            return rtrim( 'https://' . $setting, '/' );
        $siteURL = trim( preg_replace( '#^https?://#i', '', trim( (string)$siteURL ) ), '/' );
        $host = strtolower( trim( (string)parse_url( 'https://' . $siteURL, PHP_URL_HOST ), '[]' ) );
        if ( $siteURL !== '' && !in_array( $host, array( 'localhost', '127.0.0.1', '::1', 'example.com', 'www.example.com' ), true ) )
            return 'https://' . $siteURL;
        $rootURL = trim( (string)$rootURL );
        if ( $rootURL !== '' )
            return rtrim( preg_replace( '#^http://#i', 'https://', $rootURL ), '/' );
        return '';
    }

    /**
     * The administration siteaccess: site.ini [UserSettings] RegistrationAdminSiteAccess, else the siteaccess whose
     * [SiteSettings] FrontSiteAccess is the current one, else "admin".
     *
     * @return string
     */
    public static function adminSiteAccess()
    {
        $ini = eZINI::instance();
        $setting = $ini->hasVariable( 'UserSettings', 'RegistrationAdminSiteAccess' ) ? trim( (string)$ini->variable( 'UserSettings', 'RegistrationAdminSiteAccess' ) ) : '';
        if ( $setting !== '' )
            return $setting;
        $current = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '';
        if ( $current !== '' && $ini->hasVariable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) )
        {
            foreach ( (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) as $sa )
            {
                if ( $sa === $current )
                    continue;
                try
                {
                    $saIni = eZSiteAccess::getIni( $sa, 'site.ini' );
                    if ( $saIni && $saIni->hasVariable( 'SiteSettings', 'FrontSiteAccess' ) && $saIni->variable( 'SiteSettings', 'FrontSiteAccess' ) === $current )
                        return $sa;
                }
                catch ( Throwable $e )
                {
                }
            }
        }
        return 'admin';
    }

    /**
     * The address of a token's page.
     *
     * @param string $token
     * @param string|null $base null: adminBaseURL()
     * @return string
     */
    public static function url( $token, $base = null )
    {
        return ( $base === null ? self::adminBaseURL() : rtrim( $base, '/' ) ) . '/user/registration/' . rawurlencode( $token );
    }

    /**
     * The links of the mail for an account: view, approve, reject (signed), pending (the approval item, when one
     * waits), unactivated (the list of unactivated accounts) and expires (timestamp).
     *
     * @param int $userID
     * @param int|null $now
     * @param string|null $base null: adminBaseURL()
     * @return array
     */
    public static function links( $userID, $now = null, $base = null )
    {
        $base = $base === null ? self::adminBaseURL() : rtrim( $base, '/' );
        $now = $now === null ? time() : (int)$now;
        $links = array( 'expires' => $now + self::ttl() );
        foreach ( self::actions() as $action )
            $links[$action] = self::url( self::create( $userID, $action, null, $now ), $base );
        $collaborationID = self::pendingCollaborationID( $userID );
        $links['pending'] = $collaborationID ? $base . '/collaboration/item/full/' . $collaborationID : '';
        $links['unactivated'] = $base . '/user/unactivated';
        return $links;
    }

    // ---- state --------------------------------------------------------------------------------------------------

    /**
     * The state of a registration (STATE_*).
     *
     * @param int $userID
     * @return string
     */
    public static function state( $userID )
    {
        $userID = (int)$userID;
        $user = eZUser::fetch( $userID );
        $object = eZContentObject::fetch( $userID );
        if ( !$user instanceof eZUser || !$object instanceof eZContentObject )
            return self::STATE_MISSING;
        $hasKey = eZUserAccountKey::fetchByUserID( $userID ) instanceof eZUserAccountKey;
        $enabled = $user->isEnabled( false );
        // a sign-in is counted in ezuservisit.login_count: an account that has signed in is not a new registration
        $signedInBefore = !$enabled && !$hasKey && (int)$user->loginCount() > 0;
        return self::stateOf( $enabled, $hasKey, (int)self::pendingCollaborationID( $userID ),
                              (int)$object->attribute( 'status' ) === eZContentObject::STATUS_PUBLISHED, $signedInBefore );
    }

    /**
     * The state from its facts (pure, for the tests).
     *
     * @param bool $enabled
     * @param bool $hasKey
     * @param int $collaborationID
     * @param bool $published
     * @param bool $signedInBefore the account has signed in at least once (ezuservisit.login_count > 0)
     * @return string
     */
    public static function stateOf( $enabled, $hasKey, $collaborationID, $published, $signedInBefore = false )
    {
        if ( $collaborationID > 0 )
            return self::STATE_WORKFLOW;
        if ( !$enabled )
        {
            if ( $hasKey )
                return self::STATE_EMAIL;
            // disabled without a key after it had been used: deactivated by an administrator, not waiting
            return $signedInBefore ? self::STATE_DISABLED : self::STATE_ADMINISTRATOR;
        }
        return $published ? self::STATE_ACTIVE : self::STATE_PENDING;
    }

    /**
     * The approval item (collaboration) that waits for the account's user object, 0 when none.
     *
     * @param int $userID
     * @return int
     */
    public static function pendingCollaborationID( $userID )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT id FROM ezcollab_item WHERE type_identifier = 'ezapprove' AND data_int1 = " . (int)$userID .
                                 " AND data_int3 = " . (int)eZApproveCollaborationHandler::STATUS_WAITING . " ORDER BY id DESC", array( 'limit' => 1 ) );
        return $rows ? (int)$rows[0]['id'] : 0;
    }

    /**
     * Whether an action may be done to the account now, from its state: approve and reject only while it waits.
     *
     * @param string $action
     * @param string $state
     * @return bool
     */
    public static function allowedInState( $action, $state )
    {
        if ( $action === self::ACTION_VIEW )
            return $state !== self::STATE_MISSING;
        if ( $action !== self::ACTION_APPROVE && $action !== self::ACTION_REJECT )
            return false;
        $waiting = array( self::STATE_EMAIL, self::STATE_ADMINISTRATOR, self::STATE_WORKFLOW );
        return in_array( $state, $waiting, true );
    }

    // ---- policy -------------------------------------------------------------------------------------------------

    /**
     * Whether the signed-in user may do the action to the account: user/activation (the policy of
     * user/unactivated); for view also content/edit on the account; while an approval waits, an approver of it.
     *
     * @param eZUser $current
     * @param int $userID
     * @param string $action
     * @return bool
     */
    public static function may( eZUser $current, $userID, $action )
    {
        if ( $current->isAnonymous() )
            return false;
        $access = $current->hasAccessTo( 'user', 'activation' );
        if ( isset( $access['accessWord'] ) && $access['accessWord'] !== 'no' )
            return true;
        $collaborationID = self::pendingCollaborationID( $userID );
        if ( $collaborationID && self::isApprover( $current, $collaborationID ) )
            return true;
        if ( $action === self::ACTION_VIEW )
        {
            $object = eZContentObject::fetch( (int)$userID );
            if ( $object instanceof eZContentObject && $object->canEdit() )
                return true;
        }
        return false;
    }

    /**
     * @param eZUser $current
     * @param int $collaborationID
     * @return bool an approver of the item
     */
    public static function isApprover( eZUser $current, $collaborationID )
    {
        $currentID = (int)$current->attribute( 'contentobject_id' );
        foreach ( eZCollaborationItemParticipantLink::fetchParticipantList( array( 'item_id' => (int)$collaborationID ) ) as $participant )
        {
            if ( (int)$participant->ParticipantID === $currentID && (int)$participant->ParticipantRole === eZCollaborationItemParticipantLink::ROLE_APPROVER )
                return true;
        }
        return false;
    }

    // ---- actions ------------------------------------------------------------------------------------------------

    /**
     * Approves a waiting registration: activates the account as user/unactivated does (the registration then
     * completes: publish, the approval mail, post_register), or accepts the approval item and continues its
     * workflow at once.
     *
     * @param int $userID
     * @return array result (activated|approved|refused), state (the state after), state_before
     */
    public static function approve( $userID )
    {
        $userID = (int)$userID;
        $before = self::state( $userID );
        if ( !self::allowedInState( self::ACTION_APPROVE, $before ) )
            return array( 'result' => 'refused', 'state_before' => $before, 'state' => $before );
        if ( $before === self::STATE_WORKFLOW )
        {
            $collaborationID = self::pendingCollaborationID( $userID );
            self::decideCollaboration( $collaborationID, eZApproveCollaborationHandler::STATUS_ACCEPTED );
            self::continueWorkflow( $collaborationID );
            $result = 'approved';
        }
        else
        {
            $accountKey = eZUserAccountKey::fetchByUserID( $userID );
            $hash = $accountKey instanceof eZUserAccountKey ? $accountKey->attribute( 'hash_key' ) : '';
            if ( eZOperationHandler::operationIsAvailable( 'user_activation' ) )
                eZOperationHandler::execute( 'user', 'activation', array( 'user_id' => $userID, 'user_hash' => $hash, 'is_enabled' => true ) );
            else
                eZUserOperationCollection::activation( $userID, $hash, true );
            if ( $accountKey instanceof eZUserAccountKey )
                eZUserAccountKey::removeByUserID( $userID );
            expUserActivation::finish( $userID, true );
            $result = 'activated';
        }
        eZContentObject::clearCache( array( $userID ) );
        $after = self::state( $userID );
        self::audit( 'access.registration.approve', $userID, $before, $after );
        return array( 'result' => $result, 'state_before' => $before, 'state' => $after );
    }

    /**
     * Rejects a waiting registration: denies the approval item when one waits, and removes the account (its user
     * object purged, as user/unactivated removes one). An active account is never removed here, nor a protected one
     * (protectedIDs(): the anonymous user, the administrator account, the user who rejects).
     *
     * @param int $userID
     * @param int|null $currentUserID the user who rejects; null: the current user
     * @return array result (removed|refused), state, state_before; reason 'protected' when the account is protected
     */
    public static function reject( $userID, $currentUserID = null )
    {
        $userID = (int)$userID;
        if ( $currentUserID === null )
            $currentUserID = (int)eZUser::currentUserID();
        if ( self::isProtected( $userID, self::protectedIDs( $currentUserID ) ) )
        {
            $state = self::state( $userID );
            return array( 'result' => 'refused', 'reason' => 'protected', 'state_before' => $state, 'state' => $state );
        }
        $before = self::state( $userID );
        if ( !self::allowedInState( self::ACTION_REJECT, $before ) )
            return array( 'result' => 'refused', 'state_before' => $before, 'state' => $before );
        // audited before the purge, while the account's name is still known
        self::audit( 'access.registration.reject', $userID, $before, self::STATE_MISSING );
        if ( $before === self::STATE_WORKFLOW )
        {
            $collaborationID = self::pendingCollaborationID( $userID );
            self::decideCollaboration( $collaborationID, eZApproveCollaborationHandler::STATUS_DENIED );
            self::removeWorkflow( $collaborationID );
        }
        $object = eZContentObject::fetch( $userID );
        if ( $object instanceof eZContentObject )
            $object->purge();
        return array( 'result' => 'removed', 'state_before' => $before, 'state' => self::state( $userID ) );
    }

    /**
     * The accounts reject() never removes: the anonymous user (site.ini [UserSettings] AnonymousUserID), the
     * administrator account (UserCreatorID, and the account with the login "admin", as user/unactivated protects
     * it) and the user who rejects.
     *
     * @param int $currentUserID
     * @return int[]
     */
    public static function protectedIDs( $currentUserID )
    {
        $ini = eZINI::instance();
        $setting = function ( $name ) use ( $ini )
        {
            return $ini->hasVariable( 'UserSettings', $name ) ? (int)$ini->variable( 'UserSettings', $name ) : 0;
        };
        $admin = eZUser::fetchByName( 'admin' );
        return self::protectedIDsOf( $currentUserID, $setting( 'AnonymousUserID' ), $setting( 'UserCreatorID' ),
                                     $admin instanceof eZUser ? (int)$admin->attribute( 'contentobject_id' ) : 0 );
    }

    /**
     * The protected ids from their sources (pure, for the tests); 0 stands for none.
     *
     * @param int $currentUserID
     * @param int $anonymousID site.ini [UserSettings] AnonymousUserID
     * @param int $creatorID site.ini [UserSettings] UserCreatorID (the administrator account)
     * @param int $adminLoginID the account with the login "admin"
     * @return int[]
     */
    public static function protectedIDsOf( $currentUserID, $anonymousID, $creatorID, $adminLoginID )
    {
        $ids = array_map( 'intval', array( $currentUserID, $anonymousID, $creatorID, $adminLoginID ) );
        return array_values( array_unique( array_filter( $ids, function ( $id ) { return $id > 0; } ) ) );
    }

    /**
     * Whether an account is one of the protected ones (pure, for the tests).
     *
     * @param int $userID
     * @param int[] $protected
     * @return bool
     */
    public static function isProtected( $userID, array $protected )
    {
        return (int)$userID <= 0 || in_array( (int)$userID, array_map( 'intval', $protected ), true );
    }

    /**
     * Sets the decision of an approval item as its Approve and Deny buttons do.
     *
     * @param int $collaborationID
     * @param int $status eZApproveCollaborationHandler::STATUS_ACCEPTED or STATUS_DENIED
     */
    protected static function decideCollaboration( $collaborationID, $status )
    {
        $item = eZCollaborationItem::fetch( (int)$collaborationID );
        if ( !$item instanceof eZCollaborationItem )
            return;
        $item->setAttribute( 'data_int3', (int)$status );
        $item->setAttribute( 'status', eZCollaborationItem::STATUS_INACTIVE );
        $item->setAttribute( 'modified', time() );
        $item->setIsActive( false );
        $item->sync();
    }

    /**
     * The workflow processes waiting for an approval item.
     *
     * @param int $collaborationID
     * @return eZWorkflowProcess[]
     */
    protected static function processesOf( $collaborationID )
    {
        $db = eZDB::instance();
        $out = array();
        foreach ( $db->arrayQuery( 'SELECT workflow_process_id FROM ezapprove_items WHERE collaboration_id = ' . (int)$collaborationID ) as $row )
        {
            $process = eZWorkflowProcess::fetch( (int)$row['workflow_process_id'] );
            if ( $process instanceof eZWorkflowProcess )
                $out[] = $process;
        }
        return $out;
    }

    /**
     * Runs the workflow processes of an accepted approval item now, as the workflow cronjob would on its next run:
     * the publication of the user object and the rest of the registration follow at once.
     *
     * @param int $collaborationID
     */
    protected static function continueWorkflow( $collaborationID )
    {
        foreach ( self::processesOf( $collaborationID ) as $process )
        {
            if ( (int)$process->attribute( 'status' ) !== eZWorkflow::STATUS_DEFERRED_TO_CRON )
                continue;
            $db = eZDB::instance();
            $db->begin();
            $workflow = eZWorkflow::fetch( $process->attribute( 'workflow_id' ) );
            $workflowEvent = $process->attribute( 'event_id' ) != 0 ? eZWorkflowEvent::fetch( $process->attribute( 'event_id' ) ) : null;
            $eventLog = array();
            $process->run( $workflow, $workflowEvent, $eventLog );
            if ( (int)$process->attribute( 'status' ) === eZWorkflow::STATUS_DONE )
            {
                $bodyMemento = eZOperationMemento::fetchChild( $process->attribute( 'memento_key' ) );
                $mainMemento = $bodyMemento ? $bodyMemento->attribute( 'main_memento' ) : null;
                if ( $bodyMemento && $mainMemento )
                {
                    $mementoData = $bodyMemento->data();
                    $mementoData['main_memento'] = $mainMemento;
                    $mementoData['skip_trigger'] = true;
                    $mementoData['memento_key'] = $process->attribute( 'memento_key' );
                    $bodyMemento->remove();
                    $parameters = isset( $mementoData['parameters'] ) ? $mementoData['parameters'] : array();
                    eZOperationHandler::execute( $mementoData['module_name'], $mementoData['operation_name'], $parameters, $mementoData );
                }
                $process->removeThis();
            }
            else
            {
                $process->store();
            }
            $db->commit();
        }
    }

    /**
     * Removes the workflow processes of a denied approval item, with their mementos and the item's link rows.
     *
     * @param int $collaborationID
     */
    protected static function removeWorkflow( $collaborationID )
    {
        $db = eZDB::instance();
        foreach ( self::processesOf( $collaborationID ) as $process )
        {
            $key = $process->attribute( 'memento_key' );
            if ( $main = eZOperationMemento::fetchMain( $key ) )
                $main->remove();
            foreach ( eZOperationMemento::fetchList( $key ) as $memento )
                $memento->remove();
            $process->removeThis();
        }
        $db->query( 'DELETE FROM ezapprove_items WHERE collaboration_id = ' . (int)$collaborationID );
        // the item itself: it named the user object, which reject() removes, so no inbox keeps an item of nothing
        foreach ( array( 'ezcollab_item_group_link', 'ezcollab_item_participant_link', 'ezcollab_item_status', 'ezcollab_item_message_link' ) as $table )
            $db->query( "DELETE FROM $table WHERE collaboration_id = " . (int)$collaborationID );
        $db->query( 'DELETE FROM ezcollab_item WHERE id = ' . (int)$collaborationID );
    }

    /**
     * @param string $name access.registration.*
     * @param int $userID
     * @param string $before
     * @param string $after
     */
    protected static function audit( $name, $userID, $before, $after )
    {
        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::emit( $name, array( 'object' => expAuditHook::user( (int)$userID ),
                                              'before' => array( 'state' => $before ), 'after' => array( 'state' => $after ) ) );
    }

    /**
     * Records a refused link (a bad, expired or used token, or a user without the policy).
     *
     * @param int $userID 0 when the token did not say
     * @param string $reason malformed, signature, expired, action, used, policy, state
     * @param string $action
     */
    public static function auditRefused( $userID, $reason, $action )
    {
        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::emit( 'access.registration.refused', array( 'object' => $userID > 0 ? array( 'type' => 'user', 'id' => (int)$userID ) : array( 'type' => 'user' ),
                                                                      'after' => array( 'reason' => (string)$reason, 'action' => (string)$action ) ) );
    }

    // ---- mail ---------------------------------------------------------------------------------------------------

    /**
     * Makes a mail multipart/alternative: a plain-text part and an HTML part (expMailTemplateBody::setAlternative()).
     *
     * @param eZMail $mail
     * @param string $text
     * @param string $html
     */
    public static function setAlternativeBody( eZMail $mail, $text, $html )
    {
        expMailTemplateBody::setAlternative( $mail, $text, $html );
    }
}
