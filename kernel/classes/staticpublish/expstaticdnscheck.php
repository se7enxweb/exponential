<?php
/**
 * File containing the expStaticDnsCheck class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The DNS check of a custom domain for GitHub Pages (static publishing): the A, AAAA and CNAME records of the domain
 * compared with the addresses GitHub Pages publishes, and what to change when they differ.
 *
 *   an apex domain (example.com)       A records 185.199.108.153 to 185.199.111.153 (all four), AAAA records
 *                                      2606:50c0:8000::153 to 2606:50c0:8003::153 (optional), no CNAME
 *   a subdomain (www.example.com)      a CNAME to <owner>.github.io (not to the repository's address)
 *
 * Whether a name is an apex is guessed from its labels (two, or three under a two-part public suffix such as co.uk),
 * and confirmed by the records: a name with a CNAME is checked as a subdomain.
 *
 * The lookups go through dns_get_record(), or the callable of the option resolver ( $name, $type ) returning the
 * records as dns_get_record() does, which the tests pass. Nothing else is sent anywhere.
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticDnsCheck
{
    /** Second-level labels under which a name of three labels is still an apex (a short list of the common ones) */
    const TWO_PART_SUFFIXES = array( 'co.uk', 'org.uk', 'ac.uk', 'gov.uk', 'me.uk', 'com.au', 'net.au', 'org.au', 'co.nz', 'org.nz',
                                     'co.jp', 'or.jp', 'ne.jp', 'com.br', 'com.mx', 'co.za', 'com.cn', 'com.tr', 'co.in', 'co.kr', 'com.sg' );

    /** @var callable|null */
    private $resolver;

    /**
     * @param array $options resolver (callable( $name, $type ) => records)
     */
    public function __construct( array $options = array() )
    {
        $this->resolver = isset( $options['resolver'] ) && is_callable( $options['resolver'] ) ? $options['resolver'] : null;
    }

    /**
     * Whether a domain is an apex (example.com, example.co.uk) rather than a subdomain. Pure.
     *
     * @param string $domain
     * @return bool
     */
    public static function isApex( $domain )
    {
        $labels = explode( '.', strtolower( rtrim( (string)$domain, '.' ) ) );
        if ( count( $labels ) <= 2 )
            return true;
        return count( $labels ) === 3 && in_array( $labels[1] . '.' . $labels[2], self::TWO_PART_SUFFIXES, true );
    }

    /** @return array the records of one type: A ips, AAAA ipv6s, CNAME targets (lower case, without the final dot) */
    protected function lookup( $name, $type )
    {
        $constants = array( 'A' => DNS_A, 'AAAA' => DNS_AAAA, 'CNAME' => DNS_CNAME );
        $records = $this->resolver !== null ? call_user_func( $this->resolver, $name, $type ) : @dns_get_record( $name, $constants[$type] );
        $out = array();
        foreach ( is_array( $records ) ? $records : array() as $record )
        {
            if ( $type === 'A' && isset( $record['ip'] ) )
                $out[] = (string)$record['ip'];
            else if ( $type === 'AAAA' && isset( $record['ipv6'] ) )
                $out[] = self::normaliseIpv6( (string)$record['ipv6'] );
            else if ( $type === 'CNAME' && isset( $record['target'] ) )
                $out[] = strtolower( rtrim( (string)$record['target'], '.' ) );
        }
        return array_values( array_unique( $out ) );
    }

    /** An IPv6 address in its short form. Pure. */
    public static function normaliseIpv6( $ip )
    {
        $packed = @inet_pton( (string)$ip );
        return $packed === false ? strtolower( (string)$ip ) : (string)inet_ntop( $packed );
    }

    /**
     * Checks a custom domain.
     *
     * @param string $domain www.example.com or example.com
     * @param string $owner the GitHub account or organization that owns the repository ('' when it is not known:
     *        the CNAME is then only checked to end in .github.io)
     * @return array domain, kind (apex, subdomain), ok (bool), records (a, aaaa, cname), expected (a, aaaa, cname),
     *         problems (what is wrong), fixes (what to change, one line per record), notes
     */
    public function check( $domain, $owner = '' )
    {
        $domain = strtolower( rtrim( trim( (string)$domain ), '.' ) );
        $owner = strtolower( trim( (string)$owner ) );
        $expectedCname = $owner !== '' ? $owner . '.github.io' : '<owner>.github.io';
        $out = array( 'domain' => $domain, 'kind' => '', 'ok' => false,
                      'records' => array( 'a' => array(), 'aaaa' => array(), 'cname' => array() ),
                      'expected' => array( 'a' => expStaticGitHub::PAGES_A, 'aaaa' => expStaticGitHub::PAGES_AAAA, 'cname' => $expectedCname ),
                      'problems' => array(), 'fixes' => array(), 'notes' => array() );
        if ( !expStaticExtras::validDomain( $domain ) )
        {
            $out['problems'][] = "$domain is not a host name (www.example.com)";
            return $out;
        }
        $cname = $this->lookup( $domain, 'CNAME' );
        $a = $this->lookup( $domain, 'A' );
        $aaaa = $this->lookup( $domain, 'AAAA' );
        $out['records'] = array( 'a' => $a, 'aaaa' => $aaaa, 'cname' => $cname );
        $apex = self::isApex( $domain ) && !$cname;
        $out['kind'] = $apex ? 'apex' : 'subdomain';

        if ( $apex )
        {
            $pagesA = expStaticGitHub::PAGES_A;
            $pagesAaaa = array_map( array( __CLASS__, 'normaliseIpv6' ), expStaticGitHub::PAGES_AAAA );
            $foreignA = array_values( array_diff( $a, $pagesA ) );
            $missingA = array_values( array_diff( $pagesA, $a ) );
            $foreignAaaa = array_values( array_diff( $aaaa, $pagesAaaa ) );
            if ( !$a )
                $out['problems'][] = "$domain has no A record";
            if ( $foreignA )
                $out['problems'][] = "$domain has A records that are not GitHub Pages': " . implode( ', ', $foreignA );
            else if ( $a && $missingA )
                $out['notes'][] = 'not all four GitHub Pages addresses are set; all four keep the site up when one of them is down';
            if ( $foreignAaaa )
                $out['problems'][] = "$domain has AAAA records that are not GitHub Pages': " . implode( ', ', $foreignAaaa );
            if ( !$a || $foreignA || $missingA )
                foreach ( $pagesA as $ip )
                    $out['fixes'][] = "$domain.  A  $ip";
            foreach ( $foreignA as $ip )
                $out['fixes'][] = "remove: $domain.  A  $ip";
            foreach ( $foreignAaaa as $ip )
                $out['fixes'][] = "remove: $domain.  AAAA  $ip";
            if ( !$aaaa )
                $out['notes'][] = 'AAAA records (IPv6) are optional: ' . implode( ', ', expStaticGitHub::PAGES_AAAA );
            $out['notes'][] = "add www.$domain as a CNAME to $expectedCname as well, so both addresses reach the site";
        }
        else
        {
            $target = $cname ? $cname[0] : '';
            if ( $target === '' )
            {
                $out['problems'][] = "$domain has no CNAME record";
                if ( $a )
                    $out['problems'][] = "$domain has A records (" . implode( ', ', $a ) . '); a subdomain is pointed at GitHub Pages with a CNAME';
            }
            else if ( $owner !== '' && $target !== $expectedCname )
                $out['problems'][] = "$domain is a CNAME to $target, not to $expectedCname"
                                   . ( substr( $target, -10 ) === '.github.io' ? ' (the CNAME names the account, never the repository)' : '' );
            else if ( $owner === '' && substr( $target, -10 ) !== '.github.io' )
                $out['problems'][] = "$domain is a CNAME to $target, not to <owner>.github.io";
            if ( $out['problems'] )
                $out['fixes'][] = "$domain.  CNAME  $expectedCname.";
        }
        $out['ok'] = !$out['problems'];
        if ( $out['ok'] )
            $out['notes'][] = 'GitHub issues the HTTPS certificate once it sees these records; that can take up to 24 hours. Then switch on Enforce HTTPS (exp:static:github setup does).';
        else
            $out['notes'][] = 'DNS changes can take up to 24 hours (the records\' TTL) to be seen everywhere.';
        return $out;
    }
}
