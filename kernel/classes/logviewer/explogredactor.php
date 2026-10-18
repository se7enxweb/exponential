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

    /** @var expLogRedactor|null masked(), made once */
    protected $maskedRedactor = null;

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
            $text = self::inlineSecrets( $text );
        if ( $this->secrets )
        {
            $text = preg_replace( self::JWT, self::HIDDEN, $text );
            $text = preg_replace( self::AUTH, '$1 ' . self::HIDDEN, $text );
            $text = preg_replace_callback( self::PAIR, function ( $m ) {
                // a numeric value of a "key" or "sig" is an identifier more often than a secret, but the safe side wins
                return $m[1] . expLogRedactor::HIDDEN;
            }, $text );
            // what expSystemReportMask::text() adds to the inline secrets (masked above already): session ids and
            // cookies given as pairs, and a run of 26 or more letters and digits with a digit in it (a key, a token)
            $text = preg_replace( '/\b((?:[A-Za-z]*sess(?:ion)?_?id|sid|cookie)[\'"]?\s*(?:=>|=|:(?=\s)))(\s*)("[^"]*"|\'[^\']*\'|[^\s&;,]+)/i',
                                  '${1}${2}' . self::HIDDEN, $text );
            $text = preg_replace_callback( '/(?<![A-Za-z0-9])[A-Za-z0-9]{26,}={0,2}(?![A-Za-z0-9])/', function ( $m ) {
                return preg_match( '/\d/', $m[0] ) && preg_match( '/[A-Za-z]/', $m[0] ) ? expLogRedactor::HIDDEN : $m[0];
            }, $text );
        }
        if ( $this->emails )
            $text = preg_replace( self::EMAIL, '$1' . self::HIDDEN . '@' . self::HIDDEN . '.$2', $text );
        if ( $this->ips )
            $text = $this->ipText( $text );
        return $text === null ? '' : $text;
    }

    /** A line that may hold an inline secret of expSecretRule: an address with a scheme, or a secret-named pair */
    const INLINE_CANDIDATE = '/^[^\n]*(?::\/\/|pass|secret|token|api.?key|pwd|salt|@)[^\n]*$/mi';

    /**
     * expSecretRule::maskInline() on the lines that may hold such a secret only, each on its own: the same result
     * (its patterns never cross a line end, and the one meant for a whole value then fits a whole line), at a fraction
     * of the cost on a long text (a block of a download), where most lines name none of them.
     *
     * @param string $text
     * @return string
     */
    public static function inlineSecrets( $text )
    {
        if ( strpos( $text, "\n" ) === false )
            return (string)expSecretRule::maskInline( $text, self::HIDDEN );
        $out = preg_replace_callback( self::INLINE_CANDIDATE, function ( $m ) {
            return (string)expSecretRule::maskInline( $m[0], expLogRedactor::HIDDEN );
        }, $text );
        return $out === null ? (string)expSecretRule::maskInline( $text, self::HIDDEN ) : $out;
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
        if ( !$this->ips )
            return $ip;
        $shown = $this->ipText( $ip );
        // a compressed address with one group before the :: (2001::7334, fe80::1) is not matched by IPV6, which needs
        // two groups or a leading :: so as not to take "Class::method" in a text; the field holds an address only
        if ( $shown === $ip && strpos( $ip, '::' ) !== false && filter_var( trim( $ip ), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) !== false
             && preg_match( '/^\s*([0-9A-Fa-f]{1,4})::/', $ip, $m ) )
            $shown = $m[1] . '::x';
        return $shown;
    }

    /** @return bool whether IP addresses are masked */
    public function masksIps()
    {
        return $this->ips;
    }

    /**
     * The redactor with every mask on and the same secret setting values: what a viewer without setup/unmasklogs is
     * shown. A filter that holds a mark of the masks (x in an address, *** in a request) is also compared with it.
     *
     * @return expLogRedactor
     */
    public function masked()
    {
        if ( $this->emails && $this->ips && $this->secrets )
            return $this;
        if ( $this->maskedRedactor === null )
            $this->maskedRedactor = new self( true, true, true, $this->settingSecrets );
        return $this->maskedRedactor;
    }

    /**
     * A client address filter (the start of an address, expLogSearch) as it is written in a link when IP addresses
     * are masked: the parts the masks hide are x, so a link never carries more of an address than the viewer is
     * shown. 203.0.113.7 is 203.0.x.x, 203.0.113 is 203.0.x, 203.0. stays; 2001:db8:85a3::7 is 2001:db8:x:x,
     * 2001::7 is 2001::x, ::1 is ::x. A filter that is already masked stays as it is. Pure.
     *
     * @param string $filter
     * @return string
     */
    public static function ipFilter( $filter )
    {
        $filter = (string)$filter;
        if ( $filter === '' )
            return $filter;
        if ( strpos( $filter, ':' ) === false )
        {
            $parts = explode( '.', $filter );
            foreach ( $parts as $i => $part )
                if ( $i >= 2 && $part !== '' )
                    $parts[$i] = 'x';
            return implode( '.', $parts );
        }
        if ( substr( $filter, 0, 2 ) === '::' )
            return strlen( $filter ) > 2 ? '::x' : $filter;
        if ( preg_match( '/^([0-9A-Fa-f]{1,4}::)(.*)$/s', $filter, $m ) )
            return $m[1] . ( $m[2] !== '' ? 'x' : '' );
        if ( preg_match( '/^((?:[0-9A-Fa-f]{1,4}:){2})(.*)$/s', $filter, $m ) )
            return $m[1] . ( $m[2] === '' ? '' : ( strpos( $m[2], ':' ) !== false || strpos( $m[2], '.' ) !== false ? 'x:x' : 'x' ) );
        return $filter;
    }

    /**
     * A client address filter as a link of this redactor writes it: ipFilter() when IP addresses are masked.
     *
     * @param string $filter
     * @return string
     */
    public function ipFilterOf( $filter )
    {
        return $this->ips ? self::ipFilter( $filter ) : (string)$filter;
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
