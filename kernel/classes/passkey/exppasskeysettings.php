<?php
/**
 * File containing the expPasskeySettings class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * site.ini [PasskeySettings], read for one request (doc/eng-us/guides/passkeys.md): whether passkeys are on, the
 * relying party (id and name), the origins answers may come from, user verification, the algorithms offered, how
 * long a challenge may be answered, how many passkeys an account may have, the attestation asked for, the relying
 * party id of each host of a multi-host installation (RelyingPartyIDMap) and the names a site gives authenticators.
 *
 * The relying party id and the origins default to the request's host and origin. fromValues() takes the raw values
 * and the request's host and origin, so the rules are tested without an installation; read() takes them from
 * eZINI and eZSys. Nothing is kept in a static: Velocity's persistent workers serve many hosts.
 */
class expPasskeySettings
{
    /** @var array */
    protected $values;

    protected function __construct( array $values )
    {
        $this->values = $values;
    }

    /**
     * The settings of this request.
     *
     * @return expPasskeySettings
     */
    public static function read()
    {
        $ini = eZINI::instance();
        $get = function ( $name, $default ) use ( $ini ) {
            return $ini->hasVariable( 'PasskeySettings', $name ) ? $ini->variable( 'PasskeySettings', $name ) : $default;
        };
        $siteName = $ini->hasVariable( 'SiteSettings', 'SiteName' ) ? (string)$ini->variable( 'SiteSettings', 'SiteName' ) : '';
        $origin = self::requestOrigin();
        return self::fromValues( array(
            'Passkeys' => $get( 'Passkeys', 'enabled' ),
            'SignInForm' => $get( 'SignInForm', 'enabled' ),
            'RelyingPartyID' => $get( 'RelyingPartyID', '' ),
            'RelyingPartyName' => $get( 'RelyingPartyName', '' ),
            'AllowedOrigins' => $get( 'AllowedOrigins', array() ),
            'UserVerification' => $get( 'UserVerification', 'required' ),
            'Algorithms' => $get( 'Algorithms', array( -7, -257 ) ),
            'ChallengeSeconds' => $get( 'ChallengeSeconds', 300 ),
            'MaxPerUser' => $get( 'MaxPerUser', 20 ),
            'Attestation' => $get( 'Attestation', 'none' ),
            'RelyingPartyIDMap' => $get( 'RelyingPartyIDMap', array() ),
            'AuthenticatorNames' => $get( 'AuthenticatorNames', array() ),
        ), $origin, $siteName );
    }

    /**
     * @param array $raw the [PasskeySettings] values
     * @param string|false $requestOrigin scheme://host[:port] of the request
     * @param string $siteName [SiteSettings] SiteName
     * @return expPasskeySettings
     */
    public static function fromValues( array $raw, $requestOrigin, $siteName = '' )
    {
        $on = function ( $v ) {
            return in_array( strtolower( trim( (string)$v ) ), array( 'enabled', 'true', '1', 'yes', 'on' ), true );
        };
        $requestOrigin = $requestOrigin ? expWebAuthn::normaliseOrigin( $requestOrigin ) : false;
        $requestHost = $requestOrigin ? parse_url( $requestOrigin, PHP_URL_HOST ) : '';

        $rpId = strtolower( trim( (string)( isset( $raw['RelyingPartyID'] ) ? $raw['RelyingPartyID'] : '' ) ) );
        // a multi-host installation maps each host to its relying party id (RelyingPartyIDMap[<host>]=<id>)
        $map = isset( $raw['RelyingPartyIDMap'] ) && is_array( $raw['RelyingPartyIDMap'] ) ? array_change_key_case( $raw['RelyingPartyIDMap'], CASE_LOWER ) : array();
        if ( $requestHost !== '' && isset( $map[strtolower( $requestHost )] ) && trim( (string)$map[strtolower( $requestHost )] ) !== '' )
            $rpId = strtolower( trim( (string)$map[strtolower( $requestHost )] ) );
        // the id must be the host or a parent domain of it, or the browser refuses it: fall back to the host
        if ( $rpId === '' || ( $requestHost !== '' && $requestHost !== $rpId && substr( $requestHost, -strlen( '.' . $rpId ) ) !== '.' . $rpId ) )
            $rpId = (string)$requestHost;

        $origins = array();
        foreach ( (array)( isset( $raw['AllowedOrigins'] ) ? $raw['AllowedOrigins'] : array() ) as $o )
            if ( ( $n = expWebAuthn::normaliseOrigin( $o ) ) !== false )
                $origins[] = $n;
        if ( !$origins && $requestOrigin )
            $origins[] = $requestOrigin;

        $algorithms = array();
        foreach ( (array)( isset( $raw['Algorithms'] ) ? $raw['Algorithms'] : array() ) as $a )
            if ( is_numeric( $a ) && expWebAuthnCose::isSupported( (int)$a ) && !in_array( (int)$a, $algorithms, true ) )
                $algorithms[] = (int)$a;
        if ( !$algorithms )
            $algorithms = array( expWebAuthnCose::ALG_ES256, expWebAuthnCose::ALG_RS256 );

        $name = trim( (string)( isset( $raw['RelyingPartyName'] ) ? $raw['RelyingPartyName'] : '' ) );
        return new self( array(
            'enabled' => $on( isset( $raw['Passkeys'] ) ? $raw['Passkeys'] : 'enabled' ) && $rpId !== '',
            'sign_in_form' => $on( isset( $raw['SignInForm'] ) ? $raw['SignInForm'] : 'enabled' ),
            'rp_id' => $rpId,
            'rp_name' => $name !== '' ? $name : ( trim( (string)$siteName ) !== '' ? trim( (string)$siteName ) : $rpId ),
            'origins' => $origins,
            'user_verification' => expWebAuthn::userVerification( isset( $raw['UserVerification'] ) ? $raw['UserVerification'] : 'required' ),
            'algorithms' => $algorithms,
            'challenge_seconds' => max( 30, min( 3600, (int)( isset( $raw['ChallengeSeconds'] ) ? $raw['ChallengeSeconds'] : 300 ) ) ),
            'max_per_user' => max( 1, (int)( isset( $raw['MaxPerUser'] ) ? $raw['MaxPerUser'] : 20 ) ),
            'attestation' => self::attestationValue( isset( $raw['Attestation'] ) ? $raw['Attestation'] : 'none' ),
            'authenticator_names' => self::authenticatorNames( isset( $raw['AuthenticatorNames'] ) ? (array)$raw['AuthenticatorNames'] : array() ),
        ) );
    }

    /**
     * The origin of the current request: scheme, host and port as the browser sees them (eZSys::serverURL()).
     *
     * @return string|false
     */
    public static function requestOrigin()
    {
        if ( !class_exists( 'eZSys' ) )
            return false;
        return expWebAuthn::normaliseOrigin( eZSys::serverURL() );
    }

    public function enabled() { return $this->values['enabled']; }
    public function signInForm() { return $this->values['enabled'] && $this->values['sign_in_form']; }
    public function rpId() { return $this->values['rp_id']; }
    public function rpName() { return $this->values['rp_name']; }
    public function origins() { return $this->values['origins']; }
    public function userVerification() { return $this->values['user_verification']; }
    public function algorithms() { return $this->values['algorithms']; }
    public function challengeSeconds() { return $this->values['challenge_seconds']; }
    public function maxPerUser() { return $this->values['max_per_user']; }
    public function attestation() { return $this->values['attestation']; }

    /**
     * @param mixed $value
     * @return string none (default), indirect or direct
     */
    public static function attestationValue( $value )
    {
        $value = strtolower( trim( (string)$value ) );
        return in_array( $value, array( 'none', 'indirect', 'direct' ), true ) ? $value : 'none';
    }
    public function authenticatorNamesMap() { return $this->values['authenticator_names']; }

    /**
     * AuthenticatorNames[<aaguid>]=<name>: the names a site adds or corrects, keyed by the AAGUID in lower case.
     *
     * @param array $raw
     * @return array
     */
    public static function authenticatorNames( array $raw )
    {
        $out = array();
        foreach ( $raw as $aaguid => $name )
        {
            $aaguid = strtolower( trim( (string)$aaguid ) );
            $name = trim( (string)$name );
            if ( preg_match( '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $aaguid ) && $name !== '' )
                $out[$aaguid] = $name;
        }
        return $out;
    }

    /**
     * What a check expects (expWebAuthn::verify*()).
     *
     * @param string $challenge raw bytes
     * @param string|null $userVerification null: the setting
     * @return array
     */
    public function expect( $challenge, $userVerification = null )
    {
        return array( 'challenge' => $challenge, 'origins' => $this->values['origins'], 'rp_id' => $this->values['rp_id'],
                      'user_verification' => $userVerification === null ? $this->values['user_verification'] : $userVerification,
                      'algorithms' => $this->values['algorithms'] );
    }
}
