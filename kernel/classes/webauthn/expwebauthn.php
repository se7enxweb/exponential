<?php
/**
 * File containing the expWebAuthn class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The server side of WebAuthn (W3C Web Authentication, Level 2/3) for passkeys: the options a browser is given, and
 * the checks of what it sends back when a passkey is registered (navigator.credentials.create) and when it is used
 * (navigator.credentials.get).
 *
 * Registration (verifyRegistration(), §7.1 of the specification):
 *  - clientDataJSON: type "webauthn.create", the challenge the server issued, an allowed origin, not cross-origin
 *  - attestationObject (CBOR): authData with the SHA-256 of the relying party id, user present, user verified when
 *    that is required, the attested credential (id at most 1023 bytes) and its COSE public key, whose algorithm is
 *    one the server offered and can check
 *  - the attestation statement is not checked: the server asks for attestation "none" and trusts no particular
 *    authenticator model, which the specification allows; the format is kept for the record
 * Authentication (verifyAssertion(), §7.2):
 *  - clientDataJSON: type "webauthn.get", challenge, origin, not cross-origin
 *  - authenticatorData: relying party id hash, user present, user verified when required
 *  - the signature over authenticatorData followed by SHA-256(clientDataJSON), with the stored key
 *  - the signature counter: when the authenticator counts (either value not 0), the new value must be higher than
 *    the stored one, else the passkey may have been cloned and the sign-in is refused
 *
 * Every check is a pure function of its arguments (no session, no database, no settings): the caller passes what it
 * expects. The challenges themselves are kept by expWebAuthnChallenge, the passkeys by expPasskey. Results are arrays:
 * ok => true with the facts, or ok => false with an error code (ERROR_*) that is safe to record but never shown in
 * detail to a visitor.
 */
class expWebAuthn
{
    const FLAG_UP = 0x01;
    const FLAG_UV = 0x04;
    const FLAG_BE = 0x08;
    const FLAG_BS = 0x10;
    const FLAG_AT = 0x40;
    const FLAG_ED = 0x80;

    /** The longest credential id the specification allows */
    const MAX_CREDENTIAL_ID = 1023;

    /** Bytes of a new challenge */
    const CHALLENGE_BYTES = 32;

    const ERROR_CLIENT_DATA = 'client_data';
    const ERROR_TYPE = 'type';
    const ERROR_CHALLENGE = 'challenge';
    const ERROR_ORIGIN = 'origin';
    const ERROR_CROSS_ORIGIN = 'cross_origin';
    const ERROR_ATTESTATION = 'attestation';
    const ERROR_AUTH_DATA = 'auth_data';
    const ERROR_RP_ID = 'rp_id';
    const ERROR_USER_PRESENT = 'user_present';
    const ERROR_USER_VERIFIED = 'user_verified';
    const ERROR_CREDENTIAL = 'credential';
    const ERROR_ALGORITHM = 'algorithm';
    const ERROR_KEY = 'key';
    const ERROR_SIGNATURE = 'signature';
    const ERROR_COUNTER = 'counter';
    const ERROR_BACKUP = 'backup';

    // ------------------------------------------------------------------------------------------------------------
    // base64url (RFC 4648 §5, no padding): how every binary value travels between page and server
    // ------------------------------------------------------------------------------------------------------------

    /**
     * @param string $bytes
     * @return string
     */
    public static function base64UrlEncode( $bytes )
    {
        return rtrim( strtr( base64_encode( (string)$bytes ), '+/', '-_' ), '=' );
    }

    /**
     * @param mixed $text
     * @return string|false false when it is not base64url (standard base64 with + / = is accepted too)
     */
    public static function base64UrlDecode( $text )
    {
        if ( !is_string( $text ) || !preg_match( '/^[A-Za-z0-9_\-+\/]*={0,2}$/', $text ) )
            return false;
        $text = strtr( rtrim( $text, '=' ), '-_', '+/' );
        if ( strlen( $text ) % 4 === 1 )
            return false;
        $bytes = base64_decode( str_pad( $text, (int)( ceil( strlen( $text ) / 4 ) * 4 ), '=' ), true );
        return $bytes === false ? false : $bytes;
    }

    /**
     * @return string a new random challenge (raw bytes)
     */
    public static function newChallenge()
    {
        return random_bytes( self::CHALLENGE_BYTES );
    }

    // ------------------------------------------------------------------------------------------------------------
    // Options for the browser (PublicKeyCredentialCreationOptions / RequestOptions, binary values as base64url)
    // ------------------------------------------------------------------------------------------------------------

    /**
     * The options of navigator.credentials.create() for a new passkey: a discoverable credential (resident key
     * required), the algorithms in order of preference, attestation "none" (or "indirect" / "direct": the browser
     * then keeps the authenticator's AAGUID, which names its maker; the statement itself is never relied on), the
     * user's existing passkeys excluded.
     *
     * @param array $rp id, name
     * @param array $user id (raw bytes, at most 64), name, displayName
     * @param string $challenge raw bytes
     * @param int[] $algorithms COSE numbers
     * @param string[] $excludeIds credential ids (raw bytes)
     * @param string $userVerification required, preferred, discouraged
     * @param int $timeout milliseconds
     * @param string $attestation none, indirect or direct
     * @return array
     */
    public static function creationOptions( array $rp, array $user, $challenge, array $algorithms, array $excludeIds = array(),
                                            $userVerification = 'required', $timeout = 300000, $attestation = 'none' )
    {
        $params = array();
        foreach ( $algorithms as $alg )
            if ( expWebAuthnCose::isSupported( (int)$alg ) )
                $params[] = array( 'type' => 'public-key', 'alg' => (int)$alg );
        $exclude = array();
        foreach ( $excludeIds as $id )
            $exclude[] = array( 'type' => 'public-key', 'id' => self::base64UrlEncode( $id ) );
        return array(
            'rp' => array( 'id' => (string)$rp['id'], 'name' => (string)$rp['name'] ),
            'user' => array( 'id' => self::base64UrlEncode( $user['id'] ), 'name' => (string)$user['name'],
                             'displayName' => (string)$user['displayName'] ),
            'challenge' => self::base64UrlEncode( $challenge ),
            'pubKeyCredParams' => $params,
            'timeout' => (int)$timeout,
            'excludeCredentials' => $exclude,
            'authenticatorSelection' => array( 'residentKey' => 'required', 'requireResidentKey' => true,
                                               'userVerification' => self::userVerification( $userVerification ) ),
            'attestation' => in_array( $attestation, array( 'none', 'indirect', 'direct' ), true ) ? $attestation : 'none',
        );
    }

    /**
     * The options of navigator.credentials.get(): with no credential ids the browser offers every passkey it has
     * for the relying party (discoverable credentials, also in the autofill of the sign-in form); with ids only
     * those (re-authentication of a signed-in user).
     *
     * @param string $rpId
     * @param string $challenge raw bytes
     * @param string[] $allowIds credential ids (raw bytes)
     * @param string $userVerification
     * @param int $timeout milliseconds
     * @return array
     */
    public static function requestOptions( $rpId, $challenge, array $allowIds = array(), $userVerification = 'required', $timeout = 300000 )
    {
        $allow = array();
        foreach ( $allowIds as $id )
            $allow[] = array( 'type' => 'public-key', 'id' => self::base64UrlEncode( $id ) );
        return array(
            'rpId' => (string)$rpId,
            'challenge' => self::base64UrlEncode( $challenge ),
            'timeout' => (int)$timeout,
            'allowCredentials' => $allow,
            'userVerification' => self::userVerification( $userVerification ),
        );
    }

    /**
     * @param mixed $value
     * @return string required, preferred or discouraged (anything else: required)
     */
    public static function userVerification( $value )
    {
        return in_array( $value, array( 'required', 'preferred', 'discouraged' ), true ) ? $value : 'required';
    }

    // ------------------------------------------------------------------------------------------------------------
    // Checks
    // ------------------------------------------------------------------------------------------------------------

    /**
     * Reads clientDataJSON without checking it, for finding the challenge it answers.
     *
     * @param string $clientDataJSON
     * @return array|null the decoded object; challenge as raw bytes in challenge_bytes
     */
    public static function clientData( $clientDataJSON )
    {
        if ( !is_string( $clientDataJSON ) || $clientDataJSON === '' || strlen( $clientDataJSON ) > 16384 )
            return null;
        $data = json_decode( $clientDataJSON, true );
        if ( !is_array( $data ) || !isset( $data['type'], $data['challenge'], $data['origin'] )
             || !is_string( $data['type'] ) || !is_string( $data['challenge'] ) || !is_string( $data['origin'] ) )
            return null;
        $challenge = self::base64UrlDecode( $data['challenge'] );
        if ( $challenge === false )
            return null;
        $data['challenge_bytes'] = $challenge;
        return $data;
    }

    /**
     * The origin of a URL or origin string, normalised for comparing: lower-case scheme and host, no default port,
     * no path.
     *
     * @param string $url
     * @return string|false
     */
    public static function normaliseOrigin( $url )
    {
        $parts = is_string( $url ) ? parse_url( trim( $url ) ) : false;
        if ( !is_array( $parts ) || !isset( $parts['scheme'], $parts['host'] ) )
            return false;
        $scheme = strtolower( $parts['scheme'] );
        $host = strtolower( $parts['host'] );
        $port = isset( $parts['port'] ) ? (int)$parts['port'] : null;
        if ( ( $scheme === 'https' && $port === 443 ) || ( $scheme === 'http' && $port === 80 ) )
            $port = null;
        return $scheme . '://' . $host . ( $port ? ':' . $port : '' );
    }

    /**
     * Checks clientDataJSON against what the server expects.
     *
     * @param string $clientDataJSON
     * @param string $type webauthn.create or webauthn.get
     * @param array $expect challenge (raw bytes), origins (string[])
     * @return string|null an error code, or null when it is right
     */
    public static function checkClientData( $clientDataJSON, $type, array $expect )
    {
        $data = self::clientData( $clientDataJSON );
        if ( $data === null )
            return self::ERROR_CLIENT_DATA;
        if ( $data['type'] !== $type )
            return self::ERROR_TYPE;
        if ( !isset( $expect['challenge'] ) || !is_string( $expect['challenge'] ) || $expect['challenge'] === ''
             || !hash_equals( $expect['challenge'], $data['challenge_bytes'] ) )
            return self::ERROR_CHALLENGE;
        $origin = self::normaliseOrigin( $data['origin'] );
        $allowed = array();
        foreach ( isset( $expect['origins'] ) ? (array)$expect['origins'] : array() as $o )
            if ( ( $n = self::normaliseOrigin( $o ) ) !== false )
                $allowed[] = $n;
        if ( $origin === false || !in_array( $origin, $allowed, true ) )
            return self::ERROR_ORIGIN;
        if ( isset( $data['crossOrigin'] ) && $data['crossOrigin'] === true )
            return self::ERROR_CROSS_ORIGIN;
        return null;
    }

    /**
     * Reads authenticator data (§6.1).
     *
     * @param string $authData
     * @return array|null rp_id_hash, flags, sign_count, and with an attested credential aaguid, credential_id,
     *                    public_key (COSE bytes), public_key_map; null when it is malformed
     */
    public static function parseAuthenticatorData( $authData )
    {
        if ( !is_string( $authData ) || strlen( $authData ) < 37 )
            return null;
        $out = array(
            'rp_id_hash' => substr( $authData, 0, 32 ),
            'flags' => ord( $authData[32] ),
            'sign_count' => unpack( 'N', substr( $authData, 33, 4 ) )[1],
        );
        $offset = 37;
        if ( $out['flags'] & self::FLAG_AT )
        {
            if ( strlen( $authData ) < $offset + 18 )
                return null;
            $out['aaguid'] = substr( $authData, $offset, 16 );
            $length = unpack( 'n', substr( $authData, $offset + 16, 2 ) )[1];
            $offset += 18;
            if ( $length < 1 || $length > self::MAX_CREDENTIAL_ID || strlen( $authData ) < $offset + $length )
                return null;
            $out['credential_id'] = substr( $authData, $offset, $length );
            $offset += $length;
            $start = $offset;
            try
            {
                $out['public_key_map'] = expWebAuthnCbor::decodeFirst( $authData, $offset );
            }
            catch ( InvalidArgumentException $e )
            {
                return null;
            }
            if ( !is_array( $out['public_key_map'] ) )
                return null;
            $out['public_key'] = substr( $authData, $start, $offset - $start );
        }
        if ( $out['flags'] & self::FLAG_ED )
        {
            try
            {
                expWebAuthnCbor::decodeFirst( $authData, $offset );
            }
            catch ( InvalidArgumentException $e )
            {
                return null;
            }
        }
        if ( $offset !== strlen( $authData ) )
            return null;
        return $out;
    }

    /**
     * The checks of authenticator data both ceremonies share.
     *
     * @param array $auth parseAuthenticatorData()
     * @param array $expect rp_id, user_verification
     * @return string|null error code
     */
    protected static function checkAuthenticatorData( array $auth, array $expect )
    {
        if ( !isset( $expect['rp_id'] ) || !hash_equals( hash( 'sha256', (string)$expect['rp_id'], true ), $auth['rp_id_hash'] ) )
            return self::ERROR_RP_ID;
        if ( !( $auth['flags'] & self::FLAG_UP ) )
            return self::ERROR_USER_PRESENT;
        $uv = isset( $expect['user_verification'] ) ? self::userVerification( $expect['user_verification'] ) : 'required';
        if ( $uv === 'required' && !( $auth['flags'] & self::FLAG_UV ) )
            return self::ERROR_USER_VERIFIED;
        // "backed up" is only possible for a credential that can be backed up
        if ( ( $auth['flags'] & self::FLAG_BS ) && !( $auth['flags'] & self::FLAG_BE ) )
            return self::ERROR_BACKUP;
        return null;
    }

    /**
     * Checks the answer of navigator.credentials.create().
     *
     * @param string $clientDataJSON raw bytes
     * @param string $attestationObject raw bytes (CBOR)
     * @param array $expect challenge (raw), origins (string[]), rp_id, user_verification, algorithms (int[])
     * @return array ok, and either error or credential_id (raw), public_key (COSE bytes), alg, sign_count,
     *               aaguid (hex, 36 characters with dashes), user_verified, backup_eligible, backup_state, format
     */
    public static function verifyRegistration( $clientDataJSON, $attestationObject, array $expect )
    {
        $error = self::checkClientData( $clientDataJSON, 'webauthn.create', $expect );
        if ( $error !== null )
            return array( 'ok' => false, 'error' => $error );
        try
        {
            $attestation = expWebAuthnCbor::decode( (string)$attestationObject );
        }
        catch ( InvalidArgumentException $e )
        {
            return array( 'ok' => false, 'error' => self::ERROR_ATTESTATION );
        }
        if ( !is_array( $attestation ) || !isset( $attestation['authData'], $attestation['fmt'] )
             || !is_string( $attestation['authData'] ) || !is_string( $attestation['fmt'] ) )
            return array( 'ok' => false, 'error' => self::ERROR_ATTESTATION );
        $auth = self::parseAuthenticatorData( $attestation['authData'] );
        if ( $auth === null )
            return array( 'ok' => false, 'error' => self::ERROR_AUTH_DATA );
        $error = self::checkAuthenticatorData( $auth, $expect );
        if ( $error !== null )
            return array( 'ok' => false, 'error' => $error );
        if ( !isset( $auth['credential_id'], $auth['public_key'] ) )
            return array( 'ok' => false, 'error' => self::ERROR_CREDENTIAL );
        try
        {
            $key = expWebAuthnCose::parse( $auth['public_key_map'] );
        }
        catch ( InvalidArgumentException $e )
        {
            return array( 'ok' => false, 'error' => self::ERROR_KEY );
        }
        $algorithms = isset( $expect['algorithms'] ) ? array_map( 'intval', (array)$expect['algorithms'] ) : array( expWebAuthnCose::ALG_ES256, expWebAuthnCose::ALG_RS256 );
        if ( !in_array( $key['alg'], $algorithms, true ) || !expWebAuthnCose::isSupported( $key['alg'] ) )
            return array( 'ok' => false, 'error' => self::ERROR_ALGORITHM );

        return array(
            'ok' => true,
            'credential_id' => $auth['credential_id'],
            'public_key' => $auth['public_key'],
            'alg' => $key['alg'],
            'sign_count' => (int)$auth['sign_count'],
            'aaguid' => self::formatAaguid( $auth['aaguid'] ),
            'user_verified' => (bool)( $auth['flags'] & self::FLAG_UV ),
            'backup_eligible' => (bool)( $auth['flags'] & self::FLAG_BE ),
            'backup_state' => (bool)( $auth['flags'] & self::FLAG_BS ),
            'format' => $attestation['fmt'],
        );
    }

    /**
     * Checks the answer of navigator.credentials.get() for a stored passkey.
     *
     * @param string $clientDataJSON raw bytes
     * @param string $authenticatorData raw bytes
     * @param string $signature raw bytes
     * @param string $publicKey the stored COSE key (raw bytes)
     * @param int $storedSignCount
     * @param array $expect challenge (raw), origins (string[]), rp_id, user_verification
     * @return array ok, and either error or sign_count, user_verified, backup_eligible, backup_state
     */
    public static function verifyAssertion( $clientDataJSON, $authenticatorData, $signature, $publicKey, $storedSignCount, array $expect )
    {
        $error = self::checkClientData( $clientDataJSON, 'webauthn.get', $expect );
        if ( $error !== null )
            return array( 'ok' => false, 'error' => $error );
        $auth = self::parseAuthenticatorData( $authenticatorData );
        // an assertion carries no attested credential
        if ( $auth === null || isset( $auth['credential_id'] ) )
            return array( 'ok' => false, 'error' => self::ERROR_AUTH_DATA );
        $error = self::checkAuthenticatorData( $auth, $expect );
        if ( $error !== null )
            return array( 'ok' => false, 'error' => $error );
        $signed = $authenticatorData . hash( 'sha256', (string)$clientDataJSON, true );
        if ( !expWebAuthnCose::verify( $publicKey, $signed, $signature ) )
            return array( 'ok' => false, 'error' => self::ERROR_SIGNATURE );
        if ( !self::counterAccepted( (int)$storedSignCount, (int)$auth['sign_count'] ) )
            return array( 'ok' => false, 'error' => self::ERROR_COUNTER );
        return array(
            'ok' => true,
            'sign_count' => (int)$auth['sign_count'],
            'user_verified' => (bool)( $auth['flags'] & self::FLAG_UV ),
            'backup_eligible' => (bool)( $auth['flags'] & self::FLAG_BE ),
            'backup_state' => (bool)( $auth['flags'] & self::FLAG_BS ),
        );
    }

    /**
     * The signature counter rule (§7.2 step 21): an authenticator that counts must count up; one that always sends
     * 0 (many synced passkeys) is accepted.
     *
     * @param int $stored
     * @param int $received
     * @return bool
     */
    public static function counterAccepted( $stored, $received )
    {
        if ( $stored === 0 && $received === 0 )
            return true;
        return $received > $stored;
    }

    /**
     * @param string $raw 16 bytes
     * @return string 8-4-4-4-12 hex, or '' for none
     */
    public static function formatAaguid( $raw )
    {
        if ( !is_string( $raw ) || strlen( $raw ) !== 16 )
            return '';
        $hex = bin2hex( $raw );
        return substr( $hex, 0, 8 ) . '-' . substr( $hex, 8, 4 ) . '-' . substr( $hex, 12, 4 ) . '-' . substr( $hex, 16, 4 ) . '-' . substr( $hex, 20 );
    }
}
