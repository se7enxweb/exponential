<?php
/**
 * File containing the expPasskeyService class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The passkey ceremonies of the web pages (doc/eng-us/guides/passkeys.md):
 *
 *  - registration on user/passkeys: registrationOptions() gives the page the options of
 *    navigator.credentials.create() with a new challenge; register() checks the answer (expWebAuthn) and stores
 *    the passkey (expPasskey) under a name the user chose
 *  - sign-in on user/login: loginOptions() gives the options of navigator.credentials.get() without credential ids
 *    (the browser offers the passkeys it has for the site, also in the autofill of the user name field);
 *    signIn() checks the answer, finds the account by the passkey and signs it in (expPasskeyLogin)
 *  - "Verify it's you" (expReauth): reauthOptions() lists the signed-in user's own passkeys, verifyForUser() checks
 *    an answer with one of them
 *  - rename() and remove() of the user's own passkeys
 *
 * Every answer is checked against a challenge issued for that purpose and user in this session, which it uses up
 * (expWebAuthnChallenge). Every outcome is recorded through expAuditHook: access.passkey.add, .rename, .remove, .use
 * (with what it was used for: login or reauth) and .use.failed with the reason (an error code of expWebAuthn, or
 * unknown, disabled, locked, siteaccess, owner). Never recorded: keys, signatures, challenges, user handles.
 *
 * The form fields an answer is posted in (by design/standard/javascript/exp_passkey.js):
 * PasskeyCredentialID, PasskeyClientData, PasskeyAuthenticatorData, PasskeySignature, PasskeyUserHandle (sign-in)
 * and PasskeyCredentialID, PasskeyClientData, PasskeyAttestation, PasskeyTransports (registration), all base64url.
 */
class expPasskeyService
{
    const FIELD_ID = 'PasskeyCredentialID';
    const FIELD_CLIENT_DATA = 'PasskeyClientData';
    const FIELD_AUTH_DATA = 'PasskeyAuthenticatorData';
    const FIELD_SIGNATURE = 'PasskeySignature';
    const FIELD_USER_HANDLE = 'PasskeyUserHandle';
    const FIELD_ATTESTATION = 'PasskeyAttestation';
    const FIELD_TRANSPORTS = 'PasskeyTransports';

    /** @var expPasskeySettings */
    protected $settings;

    public function __construct( ?expPasskeySettings $settings = null )
    {
        $this->settings = $settings ?: expPasskeySettings::read();
    }

    /** @return expPasskeySettings */
    public function settings()
    {
        return $this->settings;
    }

    /**
     * Whether passkeys can be used here: switched on, a relying party id, and the table in the database.
     *
     * @return bool
     */
    public function available()
    {
        return $this->settings->enabled() && expPasskeySchema::exists() === true;
    }

    // ------------------------------------------------------------------------------------------------------------
    // Registration
    // ------------------------------------------------------------------------------------------------------------

    /**
     * @param eZUser $user
     * @return array the options for navigator.credentials.create()
     */
    public function registrationOptions( eZUser $user )
    {
        $userID = (int)$user->attribute( 'contentobject_id' );
        $challenge = expWebAuthnChallenge::issue( expWebAuthnChallenge::PURPOSE_REGISTER, $userID, $this->settings->challengeSeconds() );
        $exclude = array();
        foreach ( expPasskey::fetchByUser( $userID ) as $row )
            $exclude[] = $row->credentialBytes();
        $handle = expWebAuthn::base64UrlDecode( expPasskey::userHandleFor( $userID ) );
        $object = $user->attribute( 'contentobject' );
        $display = $object instanceof eZContentObject ? (string)$object->attribute( 'name' ) : (string)$user->attribute( 'login' );
        // the handle is remembered for register(): a second tab cannot slip another one in
        eZHTTPTool::instance()->setSessionVariable( 'ExpPasskeyUserHandle', expWebAuthn::base64UrlEncode( $handle ) );
        return expWebAuthn::creationOptions(
            array( 'id' => $this->settings->rpId(), 'name' => $this->settings->rpName() ),
            array( 'id' => $handle, 'name' => (string)$user->attribute( 'login' ), 'displayName' => $display !== '' ? $display : (string)$user->attribute( 'login' ) ),
            $challenge, $this->settings->algorithms(), $exclude, $this->settings->userVerification(),
            1000 * $this->settings->challengeSeconds(), $this->settings->attestation() );
    }

    /**
     * Checks a registration answer and stores the passkey.
     *
     * @param eZUser $user
     * @param array $post the posted fields
     * @param string $name the name the user gave it
     * @return array ok, passkey (expPasskey) or error (code)
     */
    public function register( eZUser $user, array $post, $name )
    {
        $userID = (int)$user->attribute( 'contentobject_id' );
        $clientData = expWebAuthn::base64UrlDecode( self::field( $post, self::FIELD_CLIENT_DATA ) );
        $attestation = expWebAuthn::base64UrlDecode( self::field( $post, self::FIELD_ATTESTATION ) );
        if ( $clientData === false || $attestation === false )
            return $this->refused( 'add', $user, expWebAuthn::ERROR_CLIENT_DATA );
        $data = expWebAuthn::clientData( $clientData );
        if ( $data === null || !expWebAuthnChallenge::consume( expWebAuthnChallenge::PURPOSE_REGISTER, $data['challenge_bytes'], $userID ) )
            return $this->refused( 'add', $user, expWebAuthn::ERROR_CHALLENGE );
        if ( expPasskey::countByUser( $userID ) >= $this->settings->maxPerUser() )
            return $this->refused( 'add', $user, 'limit' );

        $result = expWebAuthn::verifyRegistration( $clientData, $attestation, $this->settings->expect( $data['challenge_bytes'] ) );
        if ( !$result['ok'] )
            return $this->refused( 'add', $user, $result['error'] );
        if ( expPasskey::fetchByCredential( $result['credential_id'] ) )
            return $this->refused( 'add', $user, 'exists' );

        $http = eZHTTPTool::instance();
        $handle = $http->hasSessionVariable( 'ExpPasskeyUserHandle' ) ? (string)$http->sessionVariable( 'ExpPasskeyUserHandle' ) : '';
        if ( $handle === '' )
            $handle = expPasskey::userHandleFor( $userID );
        $passkey = expPasskey::create( $userID, $result, expPasskey::cleanName( $name, self::defaultName() ), $handle,
                                       self::field( $post, self::FIELD_TRANSPORTS ) );
        if ( !$passkey )
            return $this->refused( 'add', $user, 'store' );
        self::audit( 'access.passkey.add', $user, $passkey );
        return array( 'ok' => true, 'passkey' => $passkey );
    }

    /**
     * The name a passkey gets when the user gives none: "Passkey of <date>".
     *
     * @return string
     */
    public static function defaultName()
    {
        $locale = class_exists( 'eZLocale' ) ? eZLocale::instance() : null;
        $date = $locale ? $locale->formatShortDate( time() ) : date( 'Y-m-d' );
        return class_exists( 'ezpI18n' ) ? ezpI18n::tr( 'design/standard/user/passkeys', 'Passkey of %date', null, array( '%date' => $date ) )
                                         : 'Passkey of ' . $date;
    }

    /**
     * The sentence for a refusal, for the person (the code itself is for the audit).
     *
     * @param string $error
     * @return string
     */
    public static function errorText( $error )
    {
        switch ( (string)$error )
        {
            case 'limit':
                return ezpI18n::tr( 'design/standard/user/passkeys', 'This account has as many passkeys as it may have. Remove one first.' );
            case 'store':
                return ezpI18n::tr( 'design/standard/user/passkeys', 'The database was busy and nothing was saved. Please try again.' );
            case 'exists':
                return ezpI18n::tr( 'design/standard/user/passkeys', 'This passkey is registered already.' );
            case expWebAuthn::ERROR_CHALLENGE:
                return ezpI18n::tr( 'design/standard/user/passkeys', 'The request took too long or was already used. Please try again.' );
            case expWebAuthn::ERROR_USER_VERIFIED:
                return ezpI18n::tr( 'design/standard/user/passkeys', 'The passkey did not check who you are (PIN, fingerprint or face). Please try again and confirm on your device.' );
            case expWebAuthn::ERROR_ALGORITHM:
                return ezpI18n::tr( 'design/standard/user/passkeys', 'This authenticator uses a kind of key this site does not accept.' );
            case expWebAuthn::ERROR_ORIGIN:
            case expWebAuthn::ERROR_RP_ID:
                return ezpI18n::tr( 'design/standard/user/passkeys', 'The passkey was made for another address than this site.' );
            case 'unknown':
            case 'owner':
                return ezpI18n::tr( 'design/standard/user/passkeys', 'This passkey is not registered for an account here. Sign in with your password and add it on My account, Passkeys.' );
            case 'disabled':
            case 'locked':
                return ezpI18n::tr( 'design/standard/user/passkeys', 'This account cannot sign in at the moment. Please contact the site administrator.' );
        }
        return ezpI18n::tr( 'design/standard/user/passkeys', 'The passkey could not be checked. Please try again.' );
    }

    // ------------------------------------------------------------------------------------------------------------
    // Sign-in
    // ------------------------------------------------------------------------------------------------------------

    /**
     * @return array the options for navigator.credentials.get() on the sign-in form
     */
    public function loginOptions()
    {
        $challenge = expWebAuthnChallenge::issue( expWebAuthnChallenge::PURPOSE_LOGIN, 0, $this->settings->challengeSeconds() );
        // signing in with a passkey alone always needs the user verified (PIN, fingerprint, face)
        return expWebAuthn::requestOptions( $this->settings->rpId(), $challenge, array(), 'required', 1000 * $this->settings->challengeSeconds() );
    }

    /**
     * Checks a sign-in answer and finds the account. Does not sign in: the caller does that (expPasskeyLogin), after
     * its own checks (siteaccess).
     *
     * @param array $post
     * @return array ok, user (eZUser), passkey (expPasskey) or error
     */
    public function verifyLogin( array $post )
    {
        $parts = $this->assertionParts( $post );
        if ( $parts === null )
            return $this->refused( 'use', null, expWebAuthn::ERROR_CLIENT_DATA, array( 'purpose' => 'login' ) );
        if ( !expWebAuthnChallenge::consume( expWebAuthnChallenge::PURPOSE_LOGIN, $parts['challenge'], 0 ) )
            return $this->refused( 'use', null, expWebAuthn::ERROR_CHALLENGE, array( 'purpose' => 'login' ) );
        $passkey = expPasskey::fetchByCredential( $parts['id'] );
        if ( !$passkey )
            return $this->refused( 'use', null, 'unknown', array( 'purpose' => 'login' ) );
        $user = eZUser::fetch( (int)$passkey->attribute( 'user_id' ) );
        if ( !$user instanceof eZUser || $user->isAnonymous() )
            return $this->refused( 'use', null, 'owner', array( 'purpose' => 'login' ) );
        // a discoverable passkey names its account: it must be the one the passkey is stored for
        if ( $parts['user_handle'] !== '' && !hash_equals( (string)$passkey->attribute( 'user_handle' ), expWebAuthn::base64UrlEncode( $parts['user_handle'] ) ) )
            return $this->refused( 'use', $user, 'owner', array( 'purpose' => 'login' ), $passkey );

        $result = expWebAuthn::verifyAssertion( $parts['client_data'], $parts['auth_data'], $parts['signature'],
                                                $passkey->publicKeyBytes(), (int)$passkey->attribute( 'sign_count' ),
                                                $this->settings->expect( $parts['challenge'], 'required' ) );
        if ( !$result['ok'] )
            return $this->refused( 'use', $user, $result['error'], array( 'purpose' => 'login' ), $passkey );
        if ( !$user->isEnabled( false ) )
            return $this->refused( 'use', $user, 'disabled', array( 'purpose' => 'login' ), $passkey );
        if ( !eZUser::isEnabledAfterFailedLogin( (int)$user->attribute( 'contentobject_id' ) ) )
            return $this->refused( 'use', $user, 'locked', array( 'purpose' => 'login' ), $passkey );

        // the new counter must be kept, or the next use could not be told from a copy
        if ( !$passkey->recordUse( $result ) )
            return $this->refused( 'use', $user, 'store', array( 'purpose' => 'login' ), $passkey );
        self::audit( 'access.passkey.use', $user, $passkey, array( 'purpose' => 'login', 'user_verified' => $result['user_verified'] ) );
        return array( 'ok' => true, 'user' => $user, 'passkey' => $passkey );
    }

    // ------------------------------------------------------------------------------------------------------------
    // Re-authentication
    // ------------------------------------------------------------------------------------------------------------

    /**
     * @param eZUser $user
     * @return array|null the options for navigator.credentials.get() with the user's passkeys, null when they have none
     */
    public function reauthOptions( eZUser $user )
    {
        $userID = (int)$user->attribute( 'contentobject_id' );
        $ids = array();
        foreach ( expPasskey::fetchByUser( $userID ) as $row )
            $ids[] = $row->credentialBytes();
        if ( !$ids )
            return null;
        $challenge = expWebAuthnChallenge::issue( expWebAuthnChallenge::PURPOSE_REAUTH, $userID, $this->settings->challengeSeconds() );
        return expWebAuthn::requestOptions( $this->settings->rpId(), $challenge, $ids, $this->settings->userVerification(),
                                            1000 * $this->settings->challengeSeconds() );
    }

    /**
     * Checks a re-authentication answer: one of the user's own passkeys.
     *
     * @param eZUser $user
     * @param array $post
     * @return array ok, passkey or error
     */
    public function verifyForUser( eZUser $user, array $post )
    {
        $userID = (int)$user->attribute( 'contentobject_id' );
        $parts = $this->assertionParts( $post );
        if ( $parts === null )
            return $this->refused( 'use', $user, expWebAuthn::ERROR_CLIENT_DATA, array( 'purpose' => 'reauth' ) );
        if ( !expWebAuthnChallenge::consume( expWebAuthnChallenge::PURPOSE_REAUTH, $parts['challenge'], $userID ) )
            return $this->refused( 'use', $user, expWebAuthn::ERROR_CHALLENGE, array( 'purpose' => 'reauth' ) );
        $passkey = expPasskey::fetchByCredential( $parts['id'] );
        if ( !$passkey || (int)$passkey->attribute( 'user_id' ) !== $userID )
            return $this->refused( 'use', $user, 'owner', array( 'purpose' => 'reauth' ) );
        $result = expWebAuthn::verifyAssertion( $parts['client_data'], $parts['auth_data'], $parts['signature'],
                                                $passkey->publicKeyBytes(), (int)$passkey->attribute( 'sign_count' ),
                                                $this->settings->expect( $parts['challenge'] ) );
        if ( !$result['ok'] )
            return $this->refused( 'use', $user, $result['error'], array( 'purpose' => 'reauth' ), $passkey );
        if ( !$passkey->recordUse( $result ) )
            return $this->refused( 'use', $user, 'store', array( 'purpose' => 'reauth' ), $passkey );
        self::audit( 'access.passkey.use', $user, $passkey, array( 'purpose' => 'reauth', 'user_verified' => $result['user_verified'] ) );
        return array( 'ok' => true, 'passkey' => $passkey );
    }

    // ------------------------------------------------------------------------------------------------------------
    // Managing one's passkeys
    // ------------------------------------------------------------------------------------------------------------

    /**
     * @param eZUser $user
     * @param int $id
     * @param string $name
     * @return expPasskey|null the passkey, renamed; null when it is not the user's
     */
    public function rename( eZUser $user, $id, $name )
    {
        $passkey = expPasskey::fetch( (int)$id );
        if ( !$passkey || (int)$passkey->attribute( 'user_id' ) !== (int)$user->attribute( 'contentobject_id' ) )
            return null;
        $before = (string)$passkey->attribute( 'name' );
        $passkey->setAttribute( 'name', expPasskey::cleanName( $name, $before ) );
        if ( !expPasskey::inTransaction( function () use ( $passkey ) { $passkey->store(); } ) )
            return null;
        if ( $before !== (string)$passkey->attribute( 'name' ) )
            self::audit( 'access.passkey.rename', $user, $passkey, array(), array( 'name' => $before ) );
        return $passkey;
    }

    /**
     * @param eZUser $user
     * @param int $id
     * @return bool removed (false: not the user's)
     */
    public function remove( eZUser $user, $id )
    {
        return $this->removeChecked( $user, $id ) === null;
    }

    /**
     * Removes one of the user's own passkeys, unless it is the last one and the account could not sign in without it
     * (expPasskeyPolicy::mayRemove(): no password, the password sign-in off, or the policy requires a passkey).
     *
     * @param eZUser $user
     * @param int $id
     * @return string|null null when removed, else why not: 'owner' (not the user's), 'last', 'store'
     */
    public function removeChecked( eZUser $user, $id )
    {
        $passkey = expPasskey::fetch( (int)$id );
        $userID = (int)$user->attribute( 'contentobject_id' );
        if ( !$passkey || (int)$passkey->attribute( 'user_id' ) !== $userID )
            return 'owner';
        if ( class_exists( 'expPasskeyPolicy' )
             && !expPasskeyPolicy::mayRemove( expPasskeyPolicy::forUser( $user ), expPasskey::countByUser( $userID ), expPasskeyPolicy::hasPassword( $user ) ) )
            return 'last';
        if ( !expPasskey::inTransaction( function () use ( $passkey ) { $passkey->remove(); } ) )
            return 'store';
        self::audit( 'access.passkey.remove', $user, $passkey );
        return null;
    }

    /**
     * An administrator revokes a passkey of another account (user/passkeys/<user id>, policy user/passkeys_manage): a
     * lost or stolen device. Always allowed, the last one too: the administrator can set a password. Recorded as
     * access.passkey.revoke on the account, with the administrator as the actor.
     *
     * @param eZUser $account
     * @param int $id
     * @return bool
     */
    public function revoke( eZUser $account, $id )
    {
        $passkey = expPasskey::fetch( (int)$id );
        if ( !$passkey || (int)$passkey->attribute( 'user_id' ) !== (int)$account->attribute( 'contentobject_id' ) )
            return false;
        if ( !expPasskey::inTransaction( function () use ( $passkey ) { $passkey->remove(); } ) )
            return false;
        self::audit( 'access.passkey.revoke', $account, $passkey, array( 'by' => (int)eZUser::currentUserID() ) );
        return true;
    }

    /**
     * Removes the passkeys of an account that is removed (eZUser::removeUser()), when the table exists.
     *
     * @param eZUser $user
     */
    public static function removeForRemovedAccount( eZUser $user )
    {
        try
        {
            if ( expPasskeySchema::exists() === true )
                expPasskey::removeByUser( (int)$user->attribute( 'contentobject_id' ) );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'The passkeys of the removed user could not be removed: ' . $e->getMessage(), __METHOD__ );
        }
    }

    // ------------------------------------------------------------------------------------------------------------

    /**
     * The decoded parts of a posted assertion, or null when one is missing or malformed.
     *
     * @param array $post
     * @return array|null id, client_data, auth_data, signature, user_handle, challenge
     */
    protected function assertionParts( array $post )
    {
        $id = expWebAuthn::base64UrlDecode( self::field( $post, self::FIELD_ID ) );
        $clientData = expWebAuthn::base64UrlDecode( self::field( $post, self::FIELD_CLIENT_DATA ) );
        $authData = expWebAuthn::base64UrlDecode( self::field( $post, self::FIELD_AUTH_DATA ) );
        $signature = expWebAuthn::base64UrlDecode( self::field( $post, self::FIELD_SIGNATURE ) );
        $handle = expWebAuthn::base64UrlDecode( self::field( $post, self::FIELD_USER_HANDLE ) );
        if ( $id === false || $id === '' || $clientData === false || $authData === false || $signature === false )
            return null;
        $data = expWebAuthn::clientData( $clientData );
        if ( $data === null )
            return null;
        return array( 'id' => $id, 'client_data' => $clientData, 'auth_data' => $authData, 'signature' => $signature,
                      'user_handle' => $handle === false ? '' : $handle, 'challenge' => $data['challenge_bytes'] );
    }

    /**
     * @param array $post
     * @param string $name
     * @return string the field, '' when it is missing or not a string
     */
    protected static function field( array $post, $name )
    {
        return isset( $post[$name] ) && is_string( $post[$name] ) ? trim( $post[$name] ) : '';
    }

    /**
     * Records a refusal and returns the result of it.
     */
    protected function refused( $what, $user, $reason, array $after = array(), $passkey = null )
    {
        $name = $what === 'add' ? 'access.passkey.add' : 'access.passkey.use.failed';
        if ( class_exists( 'expAuditHook' ) )
        {
            $data = array( 'object' => $user instanceof eZUser ? expAuditHook::user( $user ) : array( 'type' => 'user' ),
                           'result' => 'failed', 'reason' => (string)$reason, 'after' => $after + ( $passkey ? self::describe( $passkey ) : array() ) );
            expAuditHook::emit( $name, $data );
        }
        return array( 'ok' => false, 'error' => (string)$reason );
    }

    /**
     * @param string $name
     * @param eZUser $user
     * @param expPasskey $passkey
     * @param array $after more facts
     * @param array $before
     */
    protected static function audit( $name, eZUser $user, expPasskey $passkey, array $after = array(), array $before = array() )
    {
        if ( !class_exists( 'expAuditHook' ) )
            return;
        $data = array( 'object' => expAuditHook::user( $user ), 'after' => self::describe( $passkey ) + $after );
        if ( $before )
            $data['before'] = $before;
        expAuditHook::emit( $name, $data );
    }

    /**
     * What the audit keeps of a passkey: never the key or the credential id itself.
     */
    protected static function describe( expPasskey $passkey )
    {
        return array( 'passkey_id' => (int)$passkey->attribute( 'id' ), 'name' => (string)$passkey->attribute( 'name' ),
                      'algorithm' => $passkey->algorithmName(), 'synced' => $passkey->isSynced(),
                      'aaguid' => (string)$passkey->attribute( 'aaguid' ) );
    }
}
