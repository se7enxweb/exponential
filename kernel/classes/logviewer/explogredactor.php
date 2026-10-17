<?php
/**
 * What the log viewer masks before a line leaves the server (doc/features/6.0/log-viewer.md, "Privacy").
 *
 * By default: e-mail addresses (a***@***.com), IP addresses (192.0.x.x, 2001:db8:x:...), and secrets: passwords in
 * addresses and "password=..." pairs, tokens, keys, Bearer and Basic credentials, JSON web tokens, session ids and
 * cookies (expSystemReportMask::text()). A viewer with setup/unmasklogs may switch the three masks off for
 * themselves; the values of the secret settings ([RedactionSettings] SecretSettings[], SecretGroups[]) are
 * replaced in every case, masked or not, and so is any password in an address.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLogRedactor
{
    const HIDDEN = '***';

    /** An e-mail address */
    const EMAIL = '/(?<![A-Za-z0-9._%+\-])([A-Za-z0-9])[A-Za-z0-9._%+\-]*@(?:[A-Za-z0-9](?:[A-Za-z0-9\-]{0,61}[A-Za-z0-9])?\.)+([A-Za-z]{2,24})(?![A-Za-z0-9\-])/';

    /** An IPv4 address (each part 0..255; a version such as v6.0.15.1 or 1.2.3.4.5 is left alone) */
    const IPV4 = '/(?<![\w.\-])((?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d))\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?![\w\-]|\.\d)/';

    /** An IPv6 address with at least three colons, or with :: (a time such as 01:54:58 has two) */
    const IPV6 = '/(?<![\w:.])((?:[0-9A-Fa-f]{1,4}:){2})(?=[0-9A-Fa-f:]*:)[0-9A-Fa-f:]*[0-9A-Fa-f](?:%[\w]+)?(?![\w:])|(?<![\w:])::(?:[0-9A-Fa-f]{1,4}:)*[0-9A-Fa-f]{1,4}(?![\w:])/';

    /** Credentials after an Authorization scheme, and JSON web tokens */
    const AUTH = '/\b(Bearer|Basic|Digest|Token)\s+(?=[A-Za-z0-9._~+\/=\-]*\d)[A-Za-z0-9._~+\/=\-]{8,}/i';
    const JWT = '/\beyJ[A-Za-z0-9_\-]{5,}\.[A-Za-z0-9_\-]{5,}\.[A-Za-z0-9_\-]{5,}\b/';

    /** A "password=..." style pair the shared mask does not cover: api_key, apikey, access_token, client_secret, private_key ... */
    const PAIR = '/\b((?:api[_\-]?key|apikey|access[_\-]?token|refresh[_\-]?token|id[_\-]?token|client[_\-]?secret|secret|private[_\-]?key|passwd|password|pwd|pass|token|auth|signature|sig|key)[\'"]?\s*(?:=>|=|:)\s*)("[^"]*"|\'[^\']*\'|[^\s&;,\'"<>\]\)]+)/i';

    /** @var bool */
    protected $emails;

    /** @var bool */
    protected $ips;

    /** @var bool */
    protected $secrets;

    /** @var string[] the values of the secret settings, longest first */
    protected $settingSecrets;

    /**
     * @param bool $emails mask e-mail addresses
     * @param bool $ips mask IP addresses
     * @param bool $secrets mask secrets
     * @param string[] $settingSecrets values replaced in every case
     */
    public function __construct( $emails = true, $ips = true, $secrets = true, array $settingSecrets = array() )
    {
        $this->emails = (bool)$emails;
        $this->ips = (bool)$ips;
        $this->secrets = (bool)$secrets;
        $values = array();
        foreach ( $settingSecrets as $value )
        {
            $value = (string)$value;
            // a value shorter than four characters would mask unrelated text ("1", "on") and protects nothing
            if ( strlen( $value ) >= 4 )
                $values[$value] = strlen( $value );
        }
        arsort( $values );
        $this->settingSecrets = array_keys( $values );
    }

    /**
     * The redactor of a configuration: its masks, unless $unmasked (a viewer with setup/unmasklogs who switched
     * them off), with the values of the secret settings.
     *
     * @param expLogViewerConfig $config
     * @param bool $unmasked
     * @return expLogRedactor
     */
    public static function fromConfig( expLogViewerConfig $config, $unmasked = false )
    {
        return new self( !$unmasked && $config->enabled( 'RedactionSettings', 'MaskEmails' ),
                         !$unmasked && $config->enabled( 'RedactionSettings', 'MaskIPs' ),
                         !$unmasked && $config->enabled( 'RedactionSettings', 'MaskSecrets' ),
                         self::settingSecrets( $config ) );
    }

    /**
     * The values of the secret settings that are set (SecretSettings[] and the secret-named variables of
     * SecretGroups[]). Read through eZINI; nothing without it.
     *
     * @param expLogViewerConfig $config
     * @return string[]
     */
    public static function settingSecrets( expLogViewerConfig $config )
    {
        if ( !class_exists( 'eZINI' ) )
            return array();
        $values = array();
        $add = function ( $value ) use ( &$values ) {
            foreach ( (array)$value as $v )
                if ( is_scalar( $v ) && (string)$v !== '' )
                    $values[] = (string)$v;
        };
        try
        {
            foreach ( $config->lst( 'RedactionSettings', 'SecretSettings' ) as $spec )
            {
                $parts = array_map( 'trim', explode( ';', $spec ) );
                if ( count( $parts ) !== 3 || !preg_match( '/^[\w.\-]+\.ini$/', $parts[0] ) || !eZINI::exists( $parts[0] ) )
                    continue;
                $ini = eZINI::instance( $parts[0] );
                if ( $ini->hasVariable( $parts[1], $parts[2] ) )
                    $add( $ini->variable( $parts[1], $parts[2] ) );
            }
            foreach ( $config->lst( 'RedactionSettings', 'SecretGroups' ) as $spec )
            {
                $parts = array_map( 'trim', explode( ';', $spec ) );
                if ( count( $parts ) !== 2 || !preg_match( '/^[\w.\-]+\.ini$/', $parts[0] ) || !eZINI::exists( $parts[0] ) )
                    continue;
                $ini = eZINI::instance( $parts[0] );
                if ( !$ini->hasGroup( $parts[1] ) )
                    continue;
                foreach ( (array)$ini->group( $parts[1] ) as $name => $value )
                    if ( expSystemReportMask::isSecretSettingName( (string)$name ) || expSystemReportMask::isSecretName( (string)$name ) )
                        $add( $value );
            }
        }
        catch ( Exception $e )
        {
            // a settings file that cannot be read gives no values, never an error page
        }
        return array_values( array_unique( $values ) );
    }

    /** @return bool whether anything a viewer could switch off is masked */
    public function masking()
    {
        return $this->emails || $this->ips || $this->secrets;
    }

    /**
     * A text as the viewer shows it.
     *
     * @param string $text
     * @return string
     */
    public function text( $text )
    {
        $text = (string)$text;
        if ( $text === '' )
            return $text;
        foreach ( $this->settingSecrets as $secret )
            if ( strpos( $text, $secret ) !== false )
                $text = str_replace( $secret, self::HIDDEN, $text );
        // a password in an address is never shown, masked or not
        if ( class_exists( 'expSecretRule' ) )
            $text = expSecretRule::maskInline( $text, self::HIDDEN );
        if ( $this->secrets )
        {
            $text = preg_replace( self::JWT, self::HIDDEN, $text );
            $text = preg_replace( self::AUTH, '$1 ' . self::HIDDEN, $text );
            $text = preg_replace_callback( self::PAIR, function ( $m ) {
                // a numeric value of a "key" or "sig" is an identifier more often than a secret, but the safe side wins
                return $m[1] . expLogRedactor::HIDDEN;
            }, $text );
            if ( class_exists( 'expSystemReportMask' ) )
                $text = expSystemReportMask::text( $text );
        }
        if ( $this->emails )
            $text = preg_replace( self::EMAIL, '$1' . self::HIDDEN . '@' . self::HIDDEN . '.$2', $text );
        if ( $this->ips )
            $text = $this->ipText( $text );
        return $text === null ? '' : $text;
    }

    /**
     * An IP address field as the viewer shows it (the field of a log line: one address or a host name).
     *
     * @param string $ip
     * @return string
     */
    public function ip( $ip )
    {
        $ip = (string)$ip;
        return $this->ips ? $this->ipText( $ip ) : $ip;
    }

    /**
     * IP addresses in a text with their host part hidden.
     *
     * @param string $text
     * @return string
     */
    protected function ipText( $text )
    {
        $text = preg_replace( self::IPV4, '$1.x.x', $text );
        $text = preg_replace_callback( self::IPV6, function ( $m ) {
            return isset( $m[1] ) && $m[1] !== '' ? $m[1] . 'x:x' : '::x';
        }, $text );
        return $text;
    }
}
