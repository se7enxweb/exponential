<?php
/**
 * File containing the expWebAuthnCose class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The public key of a passkey as COSE (RFC 9052/9053) and the check of a signature made with it.
 *
 * Algorithms (the COSE numbers a browser is offered and a key may carry):
 *  -7   ES256  ECDSA with P-256 and SHA-256 (what nearly every passkey uses)
 *  -35  ES384  ECDSA with P-384 and SHA-384
 *  -36  ES512  ECDSA with P-521 and SHA-512
 *  -257 RS256  RSASSA-PKCS1-v1_5 with SHA-256 (Windows Hello, some security keys)
 *  -258 RS384, -259 RS512
 *  -8   EdDSA  Ed25519 (with PHP's sodium extension)
 *
 * The EC and RSA keys are turned into a PEM SubjectPublicKeyInfo and checked with openssl_verify(); an ECDSA
 * signature of WebAuthn is already ASN.1 DER, which is what OpenSSL expects. Ed25519 is checked with
 * sodium_crypto_sign_verify_detached(). The key is kept as the COSE bytes the authenticator sent, so a later
 * algorithm needs no change of what is stored. Nothing here needs a database or an installation.
 */
class expWebAuthnCose
{
    const ALG_ES256 = -7;
    const ALG_ES384 = -35;
    const ALG_ES512 = -36;
    const ALG_RS256 = -257;
    const ALG_RS384 = -258;
    const ALG_RS512 = -259;
    const ALG_EDDSA = -8;

    const KTY_OKP = 1;
    const KTY_EC2 = 2;
    const KTY_RSA = 3;

    /**
     * Per algorithm: key type, curve (EC2, OKP), coordinate length, hash for OpenSSL, the curve's OID (DER, hex).
     */
    const ALGORITHMS = array(
        -7 => array( 'name' => 'ES256', 'kty' => 2, 'crv' => 1, 'size' => 32, 'hash' => 'sha256', 'oid' => '2a8648ce3d030107' ),
        -35 => array( 'name' => 'ES384', 'kty' => 2, 'crv' => 2, 'size' => 48, 'hash' => 'sha384', 'oid' => '2b81040022' ),
        -36 => array( 'name' => 'ES512', 'kty' => 2, 'crv' => 3, 'size' => 66, 'hash' => 'sha512', 'oid' => '2b81040023' ),
        -257 => array( 'name' => 'RS256', 'kty' => 3, 'hash' => 'sha256' ),
        -258 => array( 'name' => 'RS384', 'kty' => 3, 'hash' => 'sha384' ),
        -259 => array( 'name' => 'RS512', 'kty' => 3, 'hash' => 'sha512' ),
        -8 => array( 'name' => 'EdDSA', 'kty' => 1, 'crv' => 6, 'size' => 32 ),
    );

    /** OID of id-ecPublicKey (1.2.840.10045.2.1) and rsaEncryption (1.2.840.113549.1.1.1), DER contents in hex */
    const OID_EC_PUBLIC_KEY = '2a8648ce3d0201';
    const OID_RSA_ENCRYPTION = '2a864886f70d010101';

    /**
     * Whether this PHP can check signatures of the algorithm (Ed25519 needs sodium).
     *
     * @param int $alg
     * @return bool
     */
    public static function isSupported( $alg )
    {
        $alg = (int)$alg;
        if ( !isset( self::ALGORITHMS[$alg] ) )
            return false;
        if ( $alg === self::ALG_EDDSA )
            return function_exists( 'sodium_crypto_sign_verify_detached' );
        return function_exists( 'openssl_verify' );
    }

    /**
     * @param int $alg
     * @return string ES256, RS256 ... or the number
     */
    public static function name( $alg )
    {
        return isset( self::ALGORITHMS[(int)$alg] ) ? self::ALGORITHMS[(int)$alg]['name'] : (string)(int)$alg;
    }

    /**
     * Reads a COSE key and checks it is complete and fits its algorithm.
     *
     * @param string|array $cose the CBOR bytes, or the decoded map
     * @return array alg, kty, and pem (EC2, RSA) or raw (OKP: the 32 bytes of the Ed25519 key)
     * @throws InvalidArgumentException
     */
    public static function parse( $cose )
    {
        $map = is_array( $cose ) ? $cose : expWebAuthnCbor::decode( (string)$cose );
        if ( !is_array( $map ) || !isset( $map[1], $map[3] ) || !is_int( $map[1] ) || !is_int( $map[3] ) )
            throw new InvalidArgumentException( 'COSE: the key has no type or algorithm' );
        $alg = $map[3];
        if ( !isset( self::ALGORITHMS[$alg] ) )
            throw new InvalidArgumentException( 'COSE: algorithm ' . $alg . ' is not supported' );
        $spec = self::ALGORITHMS[$alg];
        if ( $map[1] !== $spec['kty'] )
            throw new InvalidArgumentException( 'COSE: the key type does not fit the algorithm' );

        switch ( $spec['kty'] )
        {
            case self::KTY_EC2:
                if ( !isset( $map[-1], $map[-2], $map[-3] ) || $map[-1] !== $spec['crv'] )
                    throw new InvalidArgumentException( 'COSE: the EC key has the wrong curve' );
                $x = $map[-2];
                $y = $map[-3];
                if ( !is_string( $x ) || !is_string( $y ) || strlen( $x ) !== $spec['size'] || strlen( $y ) !== $spec['size'] )
                    throw new InvalidArgumentException( 'COSE: the EC key coordinates have the wrong length' );
                $algorithm = self::derSequence( self::derOid( self::OID_EC_PUBLIC_KEY ) . self::derOid( $spec['oid'] ) );
                $der = self::derSequence( $algorithm . self::derBitString( "\x04" . $x . $y ) );
                return array( 'alg' => $alg, 'kty' => self::KTY_EC2, 'pem' => self::pem( $der ) );

            case self::KTY_RSA:
                $n = isset( $map[-1] ) ? $map[-1] : null;
                $e = isset( $map[-2] ) ? $map[-2] : null;
                if ( !is_string( $n ) || !is_string( $e ) || $e === '' || strlen( ltrim( $n, "\0" ) ) < 256 )
                    throw new InvalidArgumentException( 'COSE: the RSA key is missing or shorter than 2048 bits' );
                $rsaKey = self::derSequence( self::derInteger( $n ) . self::derInteger( $e ) );
                $algorithm = self::derSequence( self::derOid( self::OID_RSA_ENCRYPTION ) . "\x05\x00" );
                $der = self::derSequence( $algorithm . self::derBitString( $rsaKey ) );
                return array( 'alg' => $alg, 'kty' => self::KTY_RSA, 'pem' => self::pem( $der ) );

            case self::KTY_OKP:
                if ( !isset( $map[-1], $map[-2] ) || $map[-1] !== $spec['crv'] || !is_string( $map[-2] ) || strlen( $map[-2] ) !== $spec['size'] )
                    throw new InvalidArgumentException( 'COSE: the OKP key is not an Ed25519 key' );
                return array( 'alg' => $alg, 'kty' => self::KTY_OKP, 'raw' => $map[-2] );
        }
        throw new InvalidArgumentException( 'COSE: unknown key type' );
    }

    /**
     * Checks a signature over $data with a COSE key.
     *
     * @param string|array $cose the key (CBOR bytes or decoded)
     * @param string $data what was signed (authenticatorData followed by SHA-256 of clientDataJSON)
     * @param string $signature as the authenticator sent it
     * @return bool
     */
    public static function verify( $cose, $data, $signature )
    {
        try
        {
            $key = self::parse( $cose );
        }
        catch ( InvalidArgumentException $e )
        {
            return false;
        }
        if ( !self::isSupported( $key['alg'] ) || !is_string( $signature ) || $signature === '' )
            return false;
        if ( $key['kty'] === self::KTY_OKP )
        {
            if ( strlen( $signature ) !== 64 )
                return false;
            try
            {
                return sodium_crypto_sign_verify_detached( $signature, (string)$data, $key['raw'] );
            }
            catch ( Throwable $e )
            {
                return false;
            }
        }
        $public = openssl_pkey_get_public( $key['pem'] );
        if ( $public === false )
            return false;
        $result = openssl_verify( (string)$data, $signature, $public, self::ALGORITHMS[$key['alg']]['hash'] );
        // an OpenSSL error (-1, or false) is a failed check, and the error queue is emptied for the next call
        while ( openssl_error_string() !== false );
        return $result === 1;
    }

    // ------------------------------------------------------------------------------------------------------------
    // DER, just enough for a SubjectPublicKeyInfo
    // ------------------------------------------------------------------------------------------------------------

    /** @return string the DER length octets */
    protected static function derLength( $length )
    {
        if ( $length < 0x80 )
            return chr( $length );
        $bytes = ltrim( pack( 'N', $length ), "\0" );
        return chr( 0x80 | strlen( $bytes ) ) . $bytes;
    }

    protected static function derSequence( $content )
    {
        return "\x30" . self::derLength( strlen( $content ) ) . $content;
    }

    protected static function derOid( $hex )
    {
        $bytes = hex2bin( $hex );
        return "\x06" . self::derLength( strlen( $bytes ) ) . $bytes;
    }

    protected static function derBitString( $bytes )
    {
        return "\x03" . self::derLength( strlen( $bytes ) + 1 ) . "\x00" . $bytes;
    }

    /** An unsigned big-endian integer as DER INTEGER (a leading zero byte when the high bit is set) */
    protected static function derInteger( $bytes )
    {
        $bytes = ltrim( $bytes, "\0" );
        if ( $bytes === '' )
            $bytes = "\0";
        if ( ord( $bytes[0] ) & 0x80 )
            $bytes = "\0" . $bytes;
        return "\x02" . self::derLength( strlen( $bytes ) ) . $bytes;
    }

    protected static function pem( $der )
    {
        return "-----BEGIN PUBLIC KEY-----\n" . chunk_split( base64_encode( $der ), 64, "\n" ) . "-----END PUBLIC KEY-----\n";
    }
}
