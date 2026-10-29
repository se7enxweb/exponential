<?php
/**
 * The server side of WebAuthn (expWebAuthn, expWebAuthnCose, expWebAuthnCbor), checked without a database or a browser:
 * recorded responses of real authenticators, and ceremonies built here with a key made by OpenSSL, changed one
 * field at a time to see each check refuse.
 *
 * The recorded vectors are responses of browsers and authenticators published with the test suite of py_webauthn
 * (Duo Security, BSD 3-Clause licence, github.com/duo-labs/py_webauthn, tests/test_verify_*_response.py): an EC2
 * (ES256), an RSA (RS256) and an OKP (Ed25519) sign-in and a registration with attestation "none", on the relying
 * party "localhost" at http://localhost:5000. Recorded vectors of Chromium's virtual authenticator (the browser proof of
 * this feature, doc/eng-us/guides/passkeys.md) are in fixtures/virtual-authenticator.json.
 *
 *  WA-01 base64url both ways, padding and standard base64 tolerated, junk refused
 *  WA-02 CBOR: integers, strings, arrays, maps, floats, tags; truncated, indefinite and duplicate keys refused
 *  WA-03 recorded ES256 sign-in passes (user verification preferred), counter 77 -> 78
 *  WA-04 recorded RS256 sign-in passes with user verification required
 *  WA-05 recorded Ed25519 sign-in passes
 *  WA-06 a recorded signature with another key fails; user verification required but not done fails
 *  WA-07 recorded registration (attestation none): credential id, COSE key, counter 23, AAGUID of zeros
 *  WA-08 a built registration and sign-in pass; each changed field is refused with its own error code
 *  WA-09 the signature counter: counting up passes, equal or lower fails, 0/0 passes
 *  WA-10 origins compare normalised (case, default ports); a cross-origin answer is refused
 *  WA-11 options: algorithms the PHP can check, discoverable credential required, exclude and allow lists
 *  WA-12 COSE keys: wrong curve, short coordinates, short RSA keys and unknown algorithms are refused
 *  WA-13 recorded vectors of Chromium's virtual authenticator (ES256 and RS256 registration and sign-in) pass
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expWebAuthnTest extends PHPUnit\Framework\TestCase
{
    const ORIGIN = 'http://localhost:5000';
    const RP_ID = 'localhost';

    private static function b( $text )
    {
        $bytes = expWebAuthn::base64UrlDecode( $text );
        if ( $bytes === false )
            throw new RuntimeException( 'bad fixture' );
        return $bytes;
    }

    private static function expect( $challenge, $uv = 'required' )
    {
        return array( 'challenge' => self::b( $challenge ), 'origins' => array( self::ORIGIN ), 'rp_id' => self::RP_ID,
                      'user_verification' => $uv, 'algorithms' => array( -7, -257, -8, -35, -36 ) );
    }

    /** WA-01 */
    public function testBase64Url()
    {
        $this->assertSame( '', expWebAuthn::base64UrlEncode( '' ) );
        $this->assertSame( '-_8', expWebAuthn::base64UrlEncode( "\xfb\xff" ) );
        $this->assertSame( "\xfb\xff", expWebAuthn::base64UrlDecode( '-_8' ) );
        $this->assertSame( "\xfb\xff", expWebAuthn::base64UrlDecode( '+/8=' ) );
        for ( $i = 1; $i < 50; $i++ )
        {
            $bytes = random_bytes( $i );
            $this->assertSame( $bytes, expWebAuthn::base64UrlDecode( expWebAuthn::base64UrlEncode( $bytes ) ) );
        }
        $this->assertFalse( expWebAuthn::base64UrlDecode( 'a' ) );
        $this->assertFalse( expWebAuthn::base64UrlDecode( 'ab cd' ) );
        $this->assertFalse( expWebAuthn::base64UrlDecode( array( 'x' ) ) );
    }

    /** WA-02 */
    public function testCbor()
    {
        $this->assertSame( 0, expWebAuthnCbor::decode( "\x00" ) );
        $this->assertSame( 23, expWebAuthnCbor::decode( "\x17" ) );
        $this->assertSame( 1000, expWebAuthnCbor::decode( "\x19\x03\xe8" ) );
        $this->assertSame( 1000000000000, expWebAuthnCbor::decode( "\x1b\x00\x00\x00\xe8\xd4\xa5\x10\x00" ) );
        $this->assertSame( -7, expWebAuthnCbor::decode( "\x26" ) );
        $this->assertSame( -257, expWebAuthnCbor::decode( "\x39\x01\x00" ) );
        $this->assertSame( 'IETF', expWebAuthnCbor::decode( "\x64IETF" ) );
        $this->assertSame( "\x01\x02", expWebAuthnCbor::decode( "\x42\x01\x02" ) );
        $this->assertSame( array( 1, array( 2, 3 ) ), expWebAuthnCbor::decode( "\x82\x01\x82\x02\x03" ) );
        $this->assertSame( array( 'a' => 1, -1 => true ), expWebAuthnCbor::decode( "\xa2\x61a\x01\x20\xf5" ) );
        $this->assertNull( expWebAuthnCbor::decode( "\xf6" ) );
        $this->assertSame( 1.5, expWebAuthnCbor::decode( "\xf9\x3e\x00" ) );
        $this->assertSame( 100000.0, expWebAuthnCbor::decode( "\xfa\x47\xc3\x50\x00" ) );
        $this->assertSame( 'x', expWebAuthnCbor::decode( "\xc0\x61x" ), 'a tag gives its value' );
        $offset = 0;
        $this->assertSame( 1, expWebAuthnCbor::decodeFirst( "\x01\x02", $offset ) );
        $this->assertSame( 1, $offset );
        foreach ( array( "\x19\x03", "\x62a", "\x5f\x41a\xff", "\xa2\x01\x01\x01\x02", "\x01\x02", "\x9a\xff\xff\xff\xff", "" ) as $bad )
        {
            try
            {
                expWebAuthnCbor::decode( $bad );
                $this->fail( 'accepted ' . bin2hex( $bad ) );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertStringStartsWith( 'CBOR', $e->getMessage() );
            }
        }
        $deep = str_repeat( "\x81", 40 ) . "\x01";
        $this->expectException( InvalidArgumentException::class );
        expWebAuthnCbor::decode( $deep );
    }

    /** WA-03 */
    public function testRecordedEs256Assertion()
    {
        $r = expWebAuthn::verifyAssertion(
            self::b( 'eyJjaGFsbGVuZ2UiOiJ4aTMwR1BHQUZZUnhWRHBZMXNNMTBEYUx6VlFHNjZudi1fN1JVYXpIMHZJMll2RzhMWWdERW52TjVmWlpOVnV2RUR1TWk5dGUzVkxxYjQyTjBma0xHQSIsImNsaWVudEV4dGVuc2lvbnMiOnt9LCJoYXNoQWxnb3JpdGhtIjoiU0hBLTI1NiIsIm9yaWdpbiI6Imh0dHA6Ly9sb2NhbGhvc3Q6NTAwMCIsInR5cGUiOiJ3ZWJhdXRobi5nZXQifQ' ),
            self::b( 'SZYN5YgOjGh0NBcPZHZgW4_krrmihjLHmVzzuoMdl2MBAAAATg' ),
            self::b( 'MEUCIGisVZOBapCWbnJJvjelIzwpixxIwkjCCb5aCHafQu68AiEA88v-2pJNNApPFwAKFiNuf82-2hBxYW5kGwVweeoxCwo' ),
            self::b( 'pQECAyYgASFYIIeDTe-gN8A-zQclHoRnGFWN8ehM1b7yAsa8I8KIvmplIlgg4nFGT5px8o6gpPZZhO01wdy9crDSA_Ngtkx0vGpvPHI' ),
            77, self::expect( 'xi30GPGAFYRxVDpY1sM10DaLzVQG66nv-_7RUazH0vI2YvG8LYgDEnvN5fZZNVuvEDuMi9te3VLqb42N0fkLGA', 'preferred' ) );
        $this->assertTrue( $r['ok'], json_encode( $r ) );
        $this->assertSame( 78, $r['sign_count'] );
        $this->assertFalse( $r['user_verified'] );
        $this->assertFalse( $r['backup_state'] );
    }

    /** WA-04 */
    public function testRecordedRs256Assertion()
    {
        $r = expWebAuthn::verifyAssertion(
            self::b( 'eyJ0eXBlIjoid2ViYXV0aG4uZ2V0IiwiY2hhbGxlbmdlIjoiaVBtQWkxUHAxWEw2b0FncTNQV1p0WlBuWmExekZVRG9HYmFRMF9LdlZHMWxGMnMzUnRfM280dVN6Y2N5MHRtY1RJcFRUVDRCVTFULUk0bWFhdm5kalEiLCJvcmlnaW4iOiJodHRwOi8vbG9jYWxob3N0OjUwMDAiLCJjcm9zc09yaWdpbiI6ZmFsc2V9' ),
            self::b( 'SZYN5YgOjGh0NBcPZHZgW4_krrmihjLHmVzzuoMdl2MFAAAAAQ' ),
            self::b( 'iOHKX3erU5_OYP_r_9HLZ-CexCE4bQRrxM8WmuoKTDdhAnZSeTP0sjECjvjfeS8MJzN1ArmvV0H0C3yy_FdRFfcpUPZzdZ7bBcmPh1XPdxRwY747OrIzcTLTFQUPdn1U-izCZtP_78VGw9pCpdMsv4CUzZdJbEcRtQuRS03qUjqDaovoJhOqEBmxJn9Wu8tBi_Qx7A33RbYjlfyLm_EDqimzDZhyietyop6XUcpKarKqVH0M6mMrM5zTjp8xf3W7odFCadXEJg-ERZqFM0-9Uup6kJNLbr6C5J4NDYmSm3HCSA6lp2iEiMPKU8Ii7QZ61kybXLxsX4w4Dm3fOLjmDw' ),
            self::rsaKey(), 0, self::expect( 'iPmAi1Pp1XL6oAgq3PWZtZPnZa1zFUDoGbaQ0_KvVG1lF2s3Rt_3o4uSzccy0tmcTIpTTT4BU1T-I4maavndjQ' ) );
        $this->assertTrue( $r['ok'], json_encode( $r ) );
        $this->assertSame( 1, $r['sign_count'] );
        $this->assertTrue( $r['user_verified'] );
    }

    /** WA-05 */
    public function testRecordedEd25519Assertion()
    {
        if ( !expWebAuthnCose::isSupported( expWebAuthnCose::ALG_EDDSA ) )
            $this->markTestSkipped( 'no sodium' );
        $r = expWebAuthn::verifyAssertion(
            self::b( 'eyJ0eXBlIjoid2ViYXV0aG4uZ2V0IiwiY2hhbGxlbmdlIjoiZVo0ZWVBM080ank1Rkl6cURhU0o2SkROR3UwYkJjNXpJMURqUV9rTHNvMVdOcWtHNms1bUNZZjFkdFFoVlVpQldaV2xaa3pSNU1GZWVXQ3BKUlVOWHciLCJvcmlnaW4iOiJodHRwOi8vbG9jYWxob3N0OjUwMDAiLCJjcm9zc09yaWdpbiI6ZmFsc2V9' ),
            self::b( 'SZYN5YgOjGh0NBcPZHZgW4_krrmihjLHmVzzuoMdl2MBAAAABw' ),
            self::b( 'RRWV8mYDRvK7YdQgdtZD4pJ2dh1D_IWZ_D6jsZo6FHJBoenbj0CVT5nA20vUzlRhN4R6dOEUHmUwP1F8eRBhBg' ),
            self::b( 'pAEBAycgBiFYIMz6_SUFLiDid2Yhlq0YboyJ-CDrIrNpkPUGmJp4D3Dp' ),
            3, self::expect( 'eZ4eeA3O4jy5FIzqDaSJ6JDNGu0bBc5zI1DjQ_kLso1WNqkG6k5mCYf1dtQhVUiBWZWlZkzR5MFeeWCpJRUNXw', 'preferred' ) );
        $this->assertTrue( $r['ok'], json_encode( $r ) );
        $this->assertSame( 7, $r['sign_count'] );
    }

    /** WA-06 */
    public function testRecordedFailures()
    {
        $r = expWebAuthn::verifyAssertion(
            self::b( 'eyJ0eXBlIjoid2ViYXV0aG4uZ2V0IiwiY2hhbGxlbmdlIjoienNmaU1aajE2VFVWQ3JUNXREUllYZFlsVXJKcDd6bl9VTmQ1Tm1Cb2NQYzRJMmRLWmJlRVdwd0JBd0E0czZvSGtWWDZfbHlfamdwNzQzZHlpV0hZWXciLCJvcmlnaW4iOiJodHRwOi8vbG9jYWxob3N0OjUwMDAiLCJjcm9zc09yaWdpbiI6ZmFsc2V9' ),
            self::b( 'SZYN5YgOjGh0NBcPZHZgW4_krrmihjLHmVzzuoMdl2MFAAAAJA' ),
            self::b( 'MEQCIBX9B1LaLaQ0LYJsRv7cOyMS-Do1rJfFJoF9oO1tHMA4AiBRKdNneMKPlN53i8uoTZ5y9Gj4ORZySmiercS38655_g' ),
            self::rsaKey(), 35, self::expect( 'zsfiMZj16TUVCrT5tDRYXdYlUrJp7zn_UNd5NmBocPc4I2dKZbeEWpwBAwA4s6oHkVX6_ly_jgp743dyiWHYYw' ) );
        $this->assertSame( array( 'ok' => false, 'error' => expWebAuthn::ERROR_SIGNATURE ), $r );

        $r = expWebAuthn::verifyAssertion(
            self::b( 'eyJ0eXBlIjoid2ViYXV0aG4uZ2V0IiwiY2hhbGxlbmdlIjoidW1HZW1YSklQQlhQeGtEOEhqYW51djlCRG9yOFo3TzNhUGR0T2dNQ2RXNFBBZnFEWDQzRUZsaHJzRjBQVzkwZGY1enJnYnQ3WVZNUkFhMjd0Q2RIenciLCJvcmlnaW4iOiJodHRwOi8vbG9jYWxob3N0OjUwMDAiLCJjcm9zc09yaWdpbiI6ZmFsc2V9' ),
            self::b( 'SZYN5YgOjGh0NBcPZHZgW4_krrmihjLHmVzzuoMdl2MBAAAAIQ' ),
            self::b( 'MEUCIGp5ADnU_SFvT4J_bKvQJ4Pc1GmANhbYq5GioOLjyUrxAiEA6Kk5qAZb8MLY-jyTiJLr_R9Fke02UHkxsRB0dnZt2X8' ),
            self::b( 'pQECAyYgASFYIOQ5TKpXJR2cV76Wgfge9BkLkEhLxVjhFjM1jKHYOcqpIlggaiNy1blt3OU8Hsmg041HUYP7eajgL7fk3nSuTEjYCwU' ),
            32, self::expect( 'umGemXJIPBXPxkD8Hjanuv9BDor8Z7O3aPdtOgMCdW4PAfqDX43EFlhrsF0PW90df5zrgbt7YVMRAa27tCdHzw' ) );
        $this->assertSame( array( 'ok' => false, 'error' => expWebAuthn::ERROR_USER_VERIFIED ), $r );
    }

    /** WA-07 */
    public function testRecordedRegistration()
    {
        $r = expWebAuthn::verifyRegistration(
            self::b( 'eyJ0eXBlIjoid2ViYXV0aG4uY3JlYXRlIiwiY2hhbGxlbmdlIjoiVHdON240V1R5R0tMYzRaWS1xR3NGcUtuSE00bmdscXN5VjBJQ0psTjJUTzlYaVJ5RnRya2FEd1V2c3FsLWdrTEpYUDZmbkYxTWxyWjUzTW00UjdDdnciLCJvcmlnaW4iOiJodHRwOi8vbG9jYWxob3N0OjUwMDAiLCJjcm9zc09yaWdpbiI6ZmFsc2V9' ),
            self::b( 'o2NmbXRkbm9uZWdhdHRTdG10oGhhdXRoRGF0YVjESZYN5YgOjGh0NBcPZHZgW4_krrmihjLHmVzzuoMdl2NFAAAAFwAAAAAAAAAAAAAAAAAAAAAAQPctcQPE5oNRRJk_nO_371mf7qE7qIodzr0eOf6ACvnMB1oQG165dqutoi1U44shGezu5_gkTjmOPeJO0N8a7P-lAQIDJiABIVggSFbUJF-42Ug3pdM8rDRFu_N5oiVEysPDB6n66r_7dZAiWCDUVnB39FlGypL-qAoIO9xWHtJygo2jfDmHl-_eKFRLDA' ),
            self::expect( 'TwN7n4WTyGKLc4ZY-qGsFqKnHM4nglqsyV0ICJlN2TO9XiRyFtrkaDwUvsql-gkLJXP6fnF1MlrZ53Mm4R7Cvw' ) );
        $this->assertTrue( $r['ok'], json_encode( $r ) );
        $this->assertSame( self::b( '9y1xA8Tmg1FEmT-c7_fvWZ_uoTuoih3OvR45_oAK-cwHWhAbXrl2q62iLVTjiyEZ7O7n-CROOY494k7Q3xrs_w' ), $r['credential_id'] );
        $this->assertSame( self::b( 'pQECAyYgASFYIEhW1CRfuNlIN6XTPKw0RbvzeaIlRMrDwwep-uq_-3WQIlgg1FZwd_RZRsqS_qgKCDvcVh7ScoKNo3w5h5fv3ihUSww' ), $r['public_key'] );
        $this->assertSame( -7, $r['alg'] );
        $this->assertSame( 23, $r['sign_count'] );
        $this->assertSame( '00000000-0000-0000-0000-000000000000', $r['aaguid'] );
        $this->assertSame( 'none', $r['format'] );
        $this->assertFalse( $r['backup_eligible'] );

        // ES256 only offered: an RSA-only answer would fail, this one passes; nothing offered at all: refused
        $e = self::expect( 'TwN7n4WTyGKLc4ZY-qGsFqKnHM4nglqsyV0ICJlN2TO9XiRyFtrkaDwUvsql-gkLJXP6fnF1MlrZ53Mm4R7Cvw' );
        $e['algorithms'] = array( -257 );
        $r = expWebAuthn::verifyRegistration(
            self::b( 'eyJ0eXBlIjoid2ViYXV0aG4uY3JlYXRlIiwiY2hhbGxlbmdlIjoiVHdON240V1R5R0tMYzRaWS1xR3NGcUtuSE00bmdscXN5VjBJQ0psTjJUTzlYaVJ5RnRya2FEd1V2c3FsLWdrTEpYUDZmbkYxTWxyWjUzTW00UjdDdnciLCJvcmlnaW4iOiJodHRwOi8vbG9jYWxob3N0OjUwMDAiLCJjcm9zc09yaWdpbiI6ZmFsc2V9' ),
            self::b( 'o2NmbXRkbm9uZWdhdHRTdG10oGhhdXRoRGF0YVjESZYN5YgOjGh0NBcPZHZgW4_krrmihjLHmVzzuoMdl2NFAAAAFwAAAAAAAAAAAAAAAAAAAAAAQPctcQPE5oNRRJk_nO_371mf7qE7qIodzr0eOf6ACvnMB1oQG165dqutoi1U44shGezu5_gkTjmOPeJO0N8a7P-lAQIDJiABIVggSFbUJF-42Ug3pdM8rDRFu_N5oiVEysPDB6n66r_7dZAiWCDUVnB39FlGypL-qAoIO9xWHtJygo2jfDmHl-_eKFRLDA' ),
            $e );
        $this->assertSame( array( 'ok' => false, 'error' => expWebAuthn::ERROR_ALGORITHM ), $r );
    }

    /** WA-08 */
    public function testBuiltCeremoniesAndEachRefusal()
    {
        $k = self::newEcKey();
        $challenge = random_bytes( 32 );
        $credentialId = random_bytes( 32 );
        $expect = array( 'challenge' => $challenge, 'origins' => array( 'https://example.invalid' ), 'rp_id' => 'example.invalid',
                         'user_verification' => 'required', 'algorithms' => array( -7 ) );
        $client = self::clientJson( 'webauthn.create', $challenge, 'https://example.invalid' );
        $auth = self::authData( 'example.invalid', 0x45, 0, $credentialId, $k['cose'] );
        $att = self::cborMap( array( 'fmt' => self::cborText( 'none' ), 'attStmt' => "\xa0", 'authData' => self::cborBytes( $auth ) ) );
        $r = expWebAuthn::verifyRegistration( $client, $att, $expect );
        $this->assertTrue( $r['ok'], json_encode( $r ) );
        $this->assertSame( $credentialId, $r['credential_id'] );
        $this->assertSame( $k['cose'], $r['public_key'] );

        $cases = array(
            'type' => array( self::clientJson( 'webauthn.get', $challenge, 'https://example.invalid' ), $att, expWebAuthn::ERROR_TYPE ),
            'challenge' => array( self::clientJson( 'webauthn.create', random_bytes( 32 ), 'https://example.invalid' ), $att, expWebAuthn::ERROR_CHALLENGE ),
            'origin' => array( self::clientJson( 'webauthn.create', $challenge, 'https://evil.example' ), $att, expWebAuthn::ERROR_ORIGIN ),
            'json' => array( '{"type":', $att, expWebAuthn::ERROR_CLIENT_DATA ),
            'attestation' => array( $client, "\xa1\x01", expWebAuthn::ERROR_ATTESTATION ),
            'rp' => array( $client, self::att( self::authData( 'other.invalid', 0x45, 0, $credentialId, $k['cose'] ) ), expWebAuthn::ERROR_RP_ID ),
            'up' => array( $client, self::att( self::authData( 'example.invalid', 0x44, 0, $credentialId, $k['cose'] ) ), expWebAuthn::ERROR_USER_PRESENT ),
            'uv' => array( $client, self::att( self::authData( 'example.invalid', 0x41, 0, $credentialId, $k['cose'] ) ), expWebAuthn::ERROR_USER_VERIFIED ),
            'backup' => array( $client, self::att( self::authData( 'example.invalid', 0x55, 0, $credentialId, $k['cose'] ) ), expWebAuthn::ERROR_BACKUP ),
            'no credential' => array( $client, self::att( self::authData( 'example.invalid', 0x05, 0 ) ), expWebAuthn::ERROR_CREDENTIAL ),
            'trailing' => array( $client, self::att( self::authData( 'example.invalid', 0x45, 0, $credentialId, $k['cose'] ) . "\x00" ), expWebAuthn::ERROR_AUTH_DATA ),
        );
        foreach ( $cases as $name => $case )
        {
            $r = expWebAuthn::verifyRegistration( $case[0], $case[1], $expect );
            $this->assertSame( array( 'ok' => false, 'error' => $case[2] ), $r, $name );
        }
        $e = $expect;
        $e['algorithms'] = array( -257 );
        $this->assertSame( expWebAuthn::ERROR_ALGORITHM, expWebAuthn::verifyRegistration( $client, $att, $e )['error'] );

        // sign-in with the registered key
        $challenge2 = random_bytes( 32 );
        $expect['challenge'] = $challenge2;
        $client2 = self::clientJson( 'webauthn.get', $challenge2, 'https://example.invalid' );
        $auth2 = self::authData( 'example.invalid', 0x05, 5 );
        $sig = self::sign( $k, $auth2 . hash( 'sha256', $client2, true ) );
        $r = expWebAuthn::verifyAssertion( $client2, $auth2, $sig, $k['cose'], 4, $expect );
        $this->assertTrue( $r['ok'], json_encode( $r ) );
        $this->assertSame( 5, $r['sign_count'] );
        $this->assertSame( expWebAuthn::ERROR_COUNTER, expWebAuthn::verifyAssertion( $client2, $auth2, $sig, $k['cose'], 5, $expect )['error'] );
        $this->assertSame( expWebAuthn::ERROR_SIGNATURE, expWebAuthn::verifyAssertion( $client2, $auth2, $sig, self::newEcKey()['cose'], 4, $expect )['error'] );
        $tampered = self::authData( 'example.invalid', 0x05, 6 );
        $this->assertSame( expWebAuthn::ERROR_SIGNATURE, expWebAuthn::verifyAssertion( $client2, $tampered, $sig, $k['cose'], 4, $expect )['error'] );
        $this->assertSame( expWebAuthn::ERROR_TYPE, expWebAuthn::verifyAssertion( $client, $auth2, $sig, $k['cose'], 4, $expect )['error'] );
        $this->assertSame( expWebAuthn::ERROR_AUTH_DATA, expWebAuthn::verifyAssertion( $client2, $auth, $sig, $k['cose'], 4, $expect )['error'],
                           'an assertion with an attested credential is refused' );
        $this->assertSame( expWebAuthn::ERROR_SIGNATURE, expWebAuthn::verifyAssertion( $client2, $auth2, '', $k['cose'], 4, $expect )['error'] );
    }

    /** WA-09 */
    public function testCounterRule()
    {
        $this->assertTrue( expWebAuthn::counterAccepted( 0, 0 ) );
        $this->assertTrue( expWebAuthn::counterAccepted( 0, 1 ) );
        $this->assertTrue( expWebAuthn::counterAccepted( 9, 10 ) );
        $this->assertFalse( expWebAuthn::counterAccepted( 10, 10 ) );
        $this->assertFalse( expWebAuthn::counterAccepted( 10, 3 ) );
        $this->assertFalse( expWebAuthn::counterAccepted( 10, 0 ) );
    }

    /** WA-10 */
    public function testOrigins()
    {
        $this->assertSame( 'https://example.invalid', expWebAuthn::normaliseOrigin( 'HTTPS://Example.Invalid:443/path?x' ) );
        $this->assertSame( 'http://localhost:8080', expWebAuthn::normaliseOrigin( 'http://localhost:8080' ) );
        $this->assertSame( 'http://localhost', expWebAuthn::normaliseOrigin( 'http://localhost:80' ) );
        $this->assertFalse( expWebAuthn::normaliseOrigin( 'not a url' ) );
        $c = random_bytes( 32 );
        $expect = array( 'challenge' => $c, 'origins' => array( 'https://EXAMPLE.invalid:443' ) );
        $this->assertNull( expWebAuthn::checkClientData( self::clientJson( 'webauthn.get', $c, 'https://example.invalid' ), 'webauthn.get', $expect ) );
        $cross = json_encode( array( 'type' => 'webauthn.get', 'challenge' => expWebAuthn::base64UrlEncode( $c ),
                                     'origin' => 'https://example.invalid', 'crossOrigin' => true ) );
        $this->assertSame( expWebAuthn::ERROR_CROSS_ORIGIN, expWebAuthn::checkClientData( $cross, 'webauthn.get', $expect ) );
        $this->assertSame( expWebAuthn::ERROR_ORIGIN, expWebAuthn::checkClientData( self::clientJson( 'webauthn.get', $c, 'https://sub.example.invalid' ), 'webauthn.get', $expect ) );
        $this->assertSame( expWebAuthn::ERROR_CHALLENGE, expWebAuthn::checkClientData( self::clientJson( 'webauthn.get', $c, 'https://example.invalid' ), 'webauthn.get', array( 'origins' => array( 'https://example.invalid' ) ) ) );
    }

    /** WA-11 */
    public function testOptions()
    {
        $o = expWebAuthn::creationOptions( array( 'id' => 'example.invalid', 'name' => 'Example' ),
                                           array( 'id' => "\x01\x02", 'name' => 'jane', 'displayName' => 'Jane' ),
                                           "\xff", array( -7, -257, -999 ), array( "\xaa" ), 'bogus' );
        $this->assertSame( array( array( 'type' => 'public-key', 'alg' => -7 ), array( 'type' => 'public-key', 'alg' => -257 ) ), $o['pubKeyCredParams'] );
        $this->assertSame( 'AQI', $o['user']['id'] );
        $this->assertSame( '_w', $o['challenge'] );
        $this->assertSame( 'required', $o['authenticatorSelection']['residentKey'] );
        $this->assertSame( 'required', $o['authenticatorSelection']['userVerification'] );
        $this->assertSame( 'none', $o['attestation'] );
        $this->assertSame( array( array( 'type' => 'public-key', 'id' => 'qg' ) ), $o['excludeCredentials'] );
        $g = expWebAuthn::requestOptions( 'example.invalid', "\xff", array(), 'preferred' );
        $this->assertSame( array(), $g['allowCredentials'] );
        $this->assertSame( 'preferred', $g['userVerification'] );
        $g = expWebAuthn::requestOptions( 'example.invalid', "\xff", array( "\xaa" ) );
        $this->assertSame( 'qg', $g['allowCredentials'][0]['id'] );
    }

    /** WA-12 */
    public function testCoseKeysRefused()
    {
        $x = str_repeat( "\x01", 32 );
        $bad = array(
            'curve' => array( 1 => 2, 3 => -7, -1 => 2, -2 => $x, -3 => $x ),
            'short' => array( 1 => 2, 3 => -7, -1 => 1, -2 => substr( $x, 1 ), -3 => $x ),
            'kty' => array( 1 => 3, 3 => -7, -1 => 1, -2 => $x, -3 => $x ),
            'alg' => array( 1 => 2, 3 => -999, -1 => 1, -2 => $x, -3 => $x ),
            'rsa short' => array( 1 => 3, 3 => -257, -1 => str_repeat( "\xff", 128 ), -2 => "\x01\x00\x01" ),
            'none' => array( 1 => 2 ),
        );
        foreach ( $bad as $name => $map )
        {
            try
            {
                expWebAuthnCose::parse( $map );
                $this->fail( 'accepted ' . $name );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertStringStartsWith( 'COSE', $e->getMessage(), $name );
            }
            $this->assertFalse( expWebAuthnCose::verify( $map, 'data', 'sig' ), $name );
        }
        $this->assertSame( 'ES256', expWebAuthnCose::name( -7 ) );
        $this->assertSame( 'RS256', expWebAuthnCose::name( -257 ) );
        $this->assertSame( '-999', expWebAuthnCose::name( -999 ) );
    }

    /** WA-13 */
    public function testVirtualAuthenticatorRecordings()
    {
        $file = __DIR__ . '/fixtures/virtual-authenticator.json';
        $this->assertFileExists( $file );
        $all = json_decode( file_get_contents( $file ), true );
        $this->assertIsArray( $all );
        $algs = array();
        foreach ( $all['ceremonies'] as $c )
        {
            $expect = array( 'challenge' => self::b( $c['registration']['challenge'] ), 'origins' => array( $all['origin'] ),
                             'rp_id' => $all['rp_id'], 'user_verification' => 'required', 'algorithms' => array( -7, -257 ) );
            $reg = expWebAuthn::verifyRegistration( self::b( $c['registration']['clientDataJSON'] ),
                                                    self::b( $c['registration']['attestationObject'] ), $expect );
            $this->assertTrue( $reg['ok'], $c['name'] . ' ' . json_encode( $reg ) );
            $this->assertSame( self::b( $c['credential_id'] ), $reg['credential_id'] );
            $algs[] = $reg['alg'];
            $expect['challenge'] = self::b( $c['assertion']['challenge'] );
            $as = expWebAuthn::verifyAssertion( self::b( $c['assertion']['clientDataJSON'] ), self::b( $c['assertion']['authenticatorData'] ),
                                                self::b( $c['assertion']['signature'] ), $reg['public_key'], $reg['sign_count'], $expect );
            $this->assertTrue( $as['ok'], $c['name'] . ' ' . json_encode( $as ) );
            $this->assertTrue( $as['user_verified'] );
            // the same answer a second time: the counter (or the challenge, which the store uses up) refuses it
            if ( $as['sign_count'] > 0 )
                $this->assertFalse( expWebAuthn::verifyAssertion( self::b( $c['assertion']['clientDataJSON'] ), self::b( $c['assertion']['authenticatorData'] ),
                                                                  self::b( $c['assertion']['signature'] ), $reg['public_key'], $as['sign_count'], $expect )['ok'] );
        }
        sort( $algs );
        $this->assertSame( array( -257, -7 ), $algs, 'one ES256 and one RS256 passkey' );
    }

    // ------------------------------------------------------------------------------------------------------------

    private static function rsaKey()
    {
        return self::b( 'pAEDAzkBACBZAQDfV20epzvQP-HtcdDpX-cGzdOxy73WQEvsU7Dnr9UWJophEfpngouvgnRLXaEUn_d8HGkp_HIx8rrpkx4BVs6X_B6ZjhLlezjIdJbLbVeb92BaEsmNn1HW2N9Xj2QM8cH-yx28_vCjf82ahQ9gyAr552Bn96G22n8jqFRQKdVpO-f-bvpvaP3IQ9F5LCX7CUaxptgbog1SFO6FI6ob5SlVVB00lVXsaYg8cIDZxCkkENkGiFPgwEaZ7995SCbiyCpUJbMqToLMgojPkAhWeyktu7TlK6UBWdJMHc3FPAIs0lH_2_2hKS-mGI1uZAFVAfW1X-mzKL0czUm2P1UlUox7IUMBAAE' );
    }

    private static function newEcKey()
    {
        $key = openssl_pkey_new( array( 'private_key_type' => OPENSSL_KEYTYPE_EC, 'curve_name' => 'prime256v1' ) );
        $d = openssl_pkey_get_details( $key );
        $x = str_pad( $d['ec']['x'], 32, "\0", STR_PAD_LEFT );
        $y = str_pad( $d['ec']['y'], 32, "\0", STR_PAD_LEFT );
        // COSE: {1: 2, 3: -7, -1: 1, -2: x, -3: y}
        $cose = "\xa5\x01\x02\x03\x26\x20\x01\x21\x58\x20" . $x . "\x22\x58\x20" . $y;
        return array( 'key' => $key, 'cose' => $cose );
    }

    private static function sign( array $k, $data )
    {
        openssl_sign( $data, $sig, $k['key'], OPENSSL_ALGO_SHA256 );
        return $sig;
    }

    private static function clientJson( $type, $challenge, $origin )
    {
        return json_encode( array( 'type' => $type, 'challenge' => expWebAuthn::base64UrlEncode( $challenge ), 'origin' => $origin,
                                   'crossOrigin' => false ), JSON_UNESCAPED_SLASHES );
    }

    private static function authData( $rpId, $flags, $count, $credentialId = null, $cose = null )
    {
        $out = hash( 'sha256', $rpId, true ) . chr( $flags ) . pack( 'N', $count );
        if ( $credentialId !== null )
            $out .= str_repeat( "\0", 16 ) . pack( 'n', strlen( $credentialId ) ) . $credentialId . $cose;
        return $out;
    }

    private static function att( $authData )
    {
        return self::cborMap( array( 'fmt' => self::cborText( 'none' ), 'attStmt' => "\xa0", 'authData' => self::cborBytes( $authData ) ) );
    }

    private static function cborHead( $major, $length )
    {
        if ( $length < 24 )
            return chr( ( $major << 5 ) | $length );
        if ( $length < 256 )
            return chr( ( $major << 5 ) | 24 ) . chr( $length );
        return chr( ( $major << 5 ) | 25 ) . pack( 'n', $length );
    }

    private static function cborText( $s )
    {
        return self::cborHead( 3, strlen( $s ) ) . $s;
    }

    private static function cborBytes( $s )
    {
        return self::cborHead( 2, strlen( $s ) ) . $s;
    }

    /** @param array $items text key => encoded value */
    private static function cborMap( array $items )
    {
        $out = self::cborHead( 5, count( $items ) );
        foreach ( $items as $k => $v )
            $out .= self::cborText( $k ) . $v;
        return $out;
    }
}
