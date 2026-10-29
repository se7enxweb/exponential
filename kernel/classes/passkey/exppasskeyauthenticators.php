<?php
/**
 * File containing the expPasskeyAuthenticators class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The names of authenticators by their AAGUID (the model id an authenticator puts in a new passkey), for the list of
 * passkeys: "iCloud Keychain", "Google Password Manager", "YubiKey 5 Series". The built-in list holds the common
 * passkey providers and security keys, from the community list of passkey provider AAGUIDs; a site adds or corrects
 * names with site.ini [PasskeySettings] AuthenticatorNames[<aaguid>]=<name>, which win.
 *
 * An AAGUID of only zeros names nothing: a browser may blank it when the site asks for attestation "none" (Firefox
 * does; Chromium and Safari keep it for passkey providers). [PasskeySettings] Attestation=indirect keeps it in every
 * browser. The AAGUID is a model, never a device: it says nothing about the person.
 *
 * Pure: no database, no settings read here (the names of the settings are passed in).
 *
 * @since 6.0.15
 */
class expPasskeyAuthenticators
{
    /** An AAGUID that names nothing */
    const ZERO = '00000000-0000-0000-0000-000000000000';

    /** @var array the built-in names, AAGUID (lower case) => name */
    const KNOWN = array(
        'fbfc3007-154e-4ecc-8c0b-6e020557d7bd' => 'iCloud Keychain',
        'dd4ec289-e01d-41c9-bb89-70fa845d4bf2' => 'iCloud Keychain (Managed)',
        'ea9b8d66-4d01-1d21-3ce4-b6b48cb575d4' => 'Google Password Manager',
        'adce0002-35bc-c60a-648b-0b25f1f05503' => 'Chrome on Mac',
        '08987058-cadc-4b81-b6e1-30de50dcbe96' => 'Windows Hello',
        '9ddd1817-af5a-4672-a2b9-3e3dd95000a9' => 'Windows Hello',
        '6028b017-b1d4-4c02-b4b3-afcdafc96bb2' => 'Windows Hello',
        'bada5566-a7aa-401f-bd96-45619a55120d' => '1Password',
        'd548826e-79b4-db40-a3d8-11116f7e8349' => 'Bitwarden',
        '531126d6-e717-415c-9320-3d9aa6981239' => 'Dashlane',
        '53414d53-554e-4700-0000-000000000000' => 'Samsung Pass',
        'fdb141b2-5d84-443e-8a35-4698c205a502' => 'KeePassXC',
        '50726f74-6f6e-5061-7373-50726f746f6e' => 'Proton Pass',
        'cb69481e-8ff7-4039-93ec-0a2729a154a8' => 'YubiKey 5 Series',
        'ee882879-721c-4913-9775-3dfcce97072a' => 'YubiKey 5 Series',
        'fa2b99dc-9e39-4257-8f92-4a30d23c4118' => 'YubiKey 5 Series with NFC',
        '2fc0579f-8113-47ea-b116-bb5a8db9202a' => 'YubiKey 5 Series with NFC',
        'c5ef55ff-ad9a-4b9f-b580-adebafe026d0' => 'YubiKey 5Ci',
        '73bb0cd4-e502-49b8-9c6f-b59445bf720b' => 'YubiKey 5 FIPS Series',
    );

    /**
     * The AAGUID in its usual form (lower case, with dashes), or '' when it is not one.
     *
     * @param string $aaguid
     * @return string
     */
    public static function normalise( $aaguid )
    {
        $hex = strtolower( preg_replace( '/[^0-9A-Fa-f]/', '', (string)$aaguid ) );
        if ( strlen( $hex ) !== 32 )
            return '';
        return substr( $hex, 0, 8 ) . '-' . substr( $hex, 8, 4 ) . '-' . substr( $hex, 12, 4 ) . '-' . substr( $hex, 16, 4 ) . '-' . substr( $hex, 20 );
    }

    /**
     * The name of an authenticator, '' when it is not known (or the AAGUID is all zeros).
     *
     * @param string $aaguid
     * @param array $siteNames AuthenticatorNames[] of the settings (expPasskeySettings::authenticatorNames())
     * @return string
     */
    public static function name( $aaguid, array $siteNames = array() )
    {
        $aaguid = self::normalise( $aaguid );
        if ( $aaguid === '' || $aaguid === self::ZERO )
            return '';
        if ( isset( $siteNames[$aaguid] ) )
            return (string)$siteNames[$aaguid];
        return isset( self::KNOWN[$aaguid] ) ? self::KNOWN[$aaguid] : '';
    }
}
