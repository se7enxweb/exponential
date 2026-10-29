<?php
/**
 * File containing the expContentSecurityPolicy class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The Content Security Policy of a page, from site.ini [ContentSecurityPolicy].
 *
 * - Mode: disabled, report-only (Content-Security-Policy-Report-Only: the browser reports what the policy would
 *   block and blocks nothing), enforce (Content-Security-Policy), or auto: report-only on an administration
 *   siteaccess ([DesignSettings] SiteDesign or one of AdditionalSiteDesignList[] begins with "admin"), disabled
 *   elsewhere. ReportOnlyDesigns[]: designs whose requests stay report-only under Mode=enforce (the classic
 *   administration, with inline code, which a user can pick with the UI switch). ReportOnlyViews[]: the same for views
 *   (module/view) that send a policy of their own.
 * - Directives[<directive>]=<sources>: the policy, one directive per entry; an empty value leaves the directive out.
 *   frame-ancestors and sandbox are sent only when the policy is enforced (browsers ignore them in report-only).
 * - AdditionalSources[]=<directive> <sources>: sources added to a directive of Directives[], a list so that several
 *   extensions and the siteaccess can each add a host without restating the directive.
 * - ReportOnlyDirectives[<directive>]=<sources>: with Mode=enforce, a second, stricter policy sent as
 *   Content-Security-Policy-Report-Only (Directives[] with these entries in their place), to measure what a stricter
 *   policy would still block while the enforced one allows it.
 * - Nonce, NonceDirectives[]: a nonce of the request ('nonce-...') added to these directives, for an inline script a
 *   template cannot avoid (template operator csp_nonce()). Not added where the directive allows 'unsafe-inline':
 *   browsers ignore 'unsafe-inline' next to a nonce.
 * - ReportURI: where the browser sends its reports; empty: with ReportEndpoint=enabled the kernel's own csp/report of
 *   this siteaccess, otherwise none. An absolute address is also announced as the Reporting-Endpoints endpoint
 *   csp-endpoint (report-to), which current browsers use in place of report-uri.
 *
 * The policy is a header of its own beside [HTTPHeaderSettings] SecurityHeaders[]: an enforced policy is sent after
 * the Content-Security-Policy that SecurityHeaders[] already carries (frame-ancestors 'self' by default), as a second
 * policy of the same header, and the browser applies both.
 *
 * Settings: site.ini [ContentSecurityPolicy] of the current siteaccess. See doc/eng-us/features/6.0/content-security-policy.md.
 */
class expContentSecurityPolicy
{
    const MODE_DISABLED = 'disabled';
    const MODE_REPORT_ONLY = 'report-only';
    const MODE_ENFORCE = 'enforce';

    const HEADER_ENFORCE = 'Content-Security-Policy';
    const HEADER_REPORT_ONLY = 'Content-Security-Policy-Report-Only';
    const HEADER_REPORTING_ENDPOINTS = 'Reporting-Endpoints';

    /** The name of the reporting endpoint (Reporting-Endpoints, report-to) */
    const REPORT_ENDPOINT_NAME = 'csp-endpoint';

    /** The kernel's report view (module csp), used when ReportEndpoint=enabled and ReportURI is empty */
    const REPORT_ENDPOINT_PATH = 'csp/report';

    /** Directives browsers ignore in a report-only policy */
    const NOT_IN_REPORT_ONLY = array( 'frame-ancestors', 'sandbox' );

    /** @var string|null the nonce of the current request */
    private static $nonce = null;

    /** @var string|null the request the nonce belongs to (see requestKey()) */
    private static $nonceRequest = null;

    /**
     * The headers of the policy for the current siteaccess: none; Content-Security-Policy or
     * Content-Security-Policy-Report-Only; with Mode=enforce and ReportOnlyDirectives[] both; and Reporting-Endpoints
     * when the reports go to an absolute address.
     *
     * @param eZINI|null $ini site.ini; null: the current siteaccess's
     * @param string|null $view module/view of the request, for ReportOnlyViews[]; null: the current request's
     * @return array header name => value
     */
    public static function headers( $ini = null, $view = null )
    {
        $settings = self::settings( $ini, $view );
        $mode = $settings['mode'];
        if ( $mode === self::MODE_DISABLED )
            return array();

        $nonce = $settings['nonce'] ? self::nonce() : '';
        $report = self::reportParts( $settings['reportURI'], $settings['reportEndpoint'] );

        $directives = self::withSources( $settings['directives'], $settings['additionalSources'] );
        if ( $mode === self::MODE_REPORT_ONLY )
            $directives = self::reportOnlyDirectives( $directives );
        $policy = self::policy( self::withNonce( $directives, $nonce, $settings['nonceDirectives'] ), $report['report-uri'], $report['report-to'] );
        if ( $policy === '' )
            return array();

        $headers = array( $mode === self::MODE_ENFORCE ? self::HEADER_ENFORCE : self::HEADER_REPORT_ONLY => $policy );

        // The stricter companion: what the enforced policy still allows and a stricter one would not
        if ( $mode === self::MODE_ENFORCE && $settings['reportOnlyDirectives'] )
        {
            $companion = self::withSources( array_merge( self::normalize( $settings['directives'] ),
                                                         self::normalize( $settings['reportOnlyDirectives'] ) ),
                                            $settings['additionalSources'] );
            $companion = self::policy( self::withNonce( self::reportOnlyDirectives( $companion ), $nonce, $settings['nonceDirectives'] ),
                                       $report['report-uri'], $report['report-to'] );
            if ( $companion !== '' )
                $headers[self::HEADER_REPORT_ONLY] = $companion;
        }

        if ( $report['endpoints'] !== '' )
            $headers[self::HEADER_REPORTING_ENDPOINTS] = $report['endpoints'];
        return $headers;
    }

    /**
     * Whether an inline script (a <script> element without nonce, an on...= attribute, a javascript: link) runs under
     * the policy of the current siteaccess: true unless the policy is enforced and its script-src (or default-src
     * without script-src) lacks 'unsafe-inline'. A nonce or hash does not count: it allows marked elements only, not
     * the handler attributes an editor such as TinyMCE 3 writes.
     *
     * @param eZINI|null $ini site.ini; null: the current siteaccess's
     * @return bool
     */
    public static function allowsInlineScripts( $ini = null )
    {
        $settings = self::settings( $ini );
        if ( $settings['mode'] !== self::MODE_ENFORCE )
            return true;
        $directives = self::withSources( $settings['directives'], $settings['additionalSources'] );
        if ( isset( $directives['script-src'] ) )
            $sources = $directives['script-src'];
        else if ( isset( $directives['default-src'] ) )
            $sources = $directives['default-src'];
        else
            return true;
        return in_array( "'unsafe-inline'", self::sourceList( $sources ), true );
    }

    /**
     * The sources an image may come from under the policy of the current siteaccess: the img-src sources (default-src
     * without img-src) after AdditionalSources[], as a list. A report-only policy counts too: the browser still makes
     * the request and logs every image it would block. Null: no policy, or none that names images (any image loads).
     *
     * @param eZINI|null $ini site.ini; null: the current siteaccess's
     * @return array|null
     */
    public static function imageSources( $ini = null )
    {
        $settings = self::settings( $ini );
        if ( $settings['mode'] === self::MODE_DISABLED )
            return null;
        $directives = self::withSources( $settings['directives'], $settings['additionalSources'] );
        if ( isset( $directives['img-src'] ) )
            return self::sourceList( $directives['img-src'] );
        if ( isset( $directives['default-src'] ) )
            return self::sourceList( $directives['default-src'] );
        return null;
    }

    /**
     * Whether a list of sources (imageSources()) allows an absolute http(s) address from another site: *, a scheme
     * source (https:), or a host source (example.com, *.example.com, https://example.com:443/path/). Keywords ('self',
     * 'none' ...), nonces, hashes, data: and blob: never allow it; 'self' is left out on purpose, as the help shows
     * absolute addresses of other sites only.
     *
     * @param array $sources
     * @param string $url
     * @return bool
     */
    public static function sourcesAllowUrl( array $sources, $url )
    {
        $parts = parse_url( (string)$url );
        if ( !is_array( $parts ) || empty( $parts['scheme'] ) || empty( $parts['host'] ) )
            return false;
        $scheme = strtolower( $parts['scheme'] );
        if ( $scheme !== 'http' && $scheme !== 'https' )
            return false;
        $host = strtolower( $parts['host'] );
        $port = isset( $parts['port'] ) ? (int)$parts['port'] : ( $scheme === 'https' ? 443 : 80 );
        $path = isset( $parts['path'] ) ? $parts['path'] : '/';
        foreach ( $sources as $source )
        {
            $source = (string)$source;
            if ( $source === '*' )
                return true;
            if ( $source === '' || $source[0] === "'" )
                continue;
            if ( preg_match( '/^([a-z][a-z0-9+.-]*):$/i', $source, $m ) === 1 )
            {
                // a scheme source; http: also allows https
                $allowed = strtolower( $m[1] );
                if ( $allowed === $scheme || ( $allowed === 'http' && $scheme === 'https' ) )
                    return true;
                continue;
            }
            if ( preg_match( '#^(?:([a-z][a-z0-9+.-]*)://)?(\*\.)?([a-z0-9.-]+|\*)(?::(\d+|\*))?(/[^?\#]*)?$#i', $source, $m ) !== 1 )
                continue;
            $sourceScheme = strtolower( $m[1] );
            if ( $sourceScheme !== '' && $sourceScheme !== $scheme && !( $sourceScheme === 'http' && $scheme === 'https' ) )
                continue;
            if ( $sourceScheme === '' && $scheme !== 'https' )
                continue;
            $sourceHost = strtolower( $m[3] );
            if ( $sourceHost !== '*' )
            {
                if ( $m[2] !== '' ? substr( $host, -strlen( '.' . $sourceHost ) ) !== '.' . $sourceHost : $host !== $sourceHost )
                    continue;
            }
            elseif ( $m[2] !== '' )
                continue;
            if ( isset( $m[4] ) && $m[4] !== '' && $m[4] !== '*' && (int)$m[4] !== $port )
                continue;
            if ( ( !isset( $m[4] ) || $m[4] === '' ) && $port !== ( $scheme === 'https' ? 443 : 80 ) )
                continue;
            if ( isset( $m[5] ) && $m[5] !== '' && $m[5] !== '/' )
            {
                $sourcePath = rawurldecode( $m[5] );
                $urlPath = rawurldecode( $path );
                if ( substr( $sourcePath, -1 ) === '/' ? strpos( $urlPath, $sourcePath ) !== 0 : $urlPath !== $sourcePath )
                    continue;
            }
            return true;
        }
        return false;
    }

    /**
     * The nonce of the current request: 22 characters of base64url from 16 random bytes, the same for every call within
     * one request. A long-running process (Velocity) gets a new one per request: reset() is called at the start of
     * every request, and the nonce is also bound to the request's start time, address and process.
     *
     * @return string
     */
    public static function nonce()
    {
        $request = self::requestKey();
        if ( self::$nonce === null || self::$nonceRequest !== $request )
        {
            self::$nonce = rtrim( strtr( base64_encode( random_bytes( 16 ) ), '+/', '-_' ), '=' );
            self::$nonceRequest = $request;
        }
        return self::$nonce;
    }

    /**
     * The nonce for a template: the nonce when the policy of the current siteaccess carries one, otherwise an empty
     * string (nonce="" is harmless). Used by the template operator csp_nonce().
     *
     * @param eZINI|null $ini site.ini; null: the current siteaccess's
     * @return string
     */
    public static function templateNonce( $ini = null )
    {
        $settings = self::settings( $ini );
        return $settings['mode'] !== self::MODE_DISABLED && $settings['nonce'] ? self::nonce() : '';
    }

    /**
     * Forgets the nonce: called at the start of every request (ezpKernelWeb).
     */
    public static function reset()
    {
        self::$nonce = null;
        self::$nonceRequest = null;
    }

    /**
     * The request a nonce belongs to: its start time, address and process.
     *
     * @return string
     */
    private static function requestKey()
    {
        $time = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : '';
        $uri = isset( $_SERVER['REQUEST_URI'] ) && is_scalar( $_SERVER['REQUEST_URI'] ) ? (string)$_SERVER['REQUEST_URI'] : '';
        return $time . '|' . $uri . '|' . getmypid();
    }

    /**
     * The settings of [ContentSecurityPolicy], read once per call.
     *
     * @param eZINI|null $ini
     * @return array
     */
    private static function settings( $ini, $view = null )
    {
        if ( !$ini instanceof eZINI )
            $ini = eZINI::instance();
        $value = function ( $section, $name, $default ) use ( $ini ) {
            return $ini->hasVariable( $section, $name ) ? $ini->variable( $section, $name ) : $default;
        };
        $nonceDirectives = $value( 'ContentSecurityPolicy', 'NonceDirectives', array( 'script-src' ) );
        $siteDesign = $value( 'DesignSettings', 'SiteDesign', '' );
        $mode = self::mode( $value( 'ContentSecurityPolicy', 'Mode', self::MODE_DISABLED ), $siteDesign,
                            $value( 'DesignSettings', 'AdditionalSiteDesignList', array() ) );
        // A design with inline code (the classic administration, which a user can pick with the UI switch) is never
        // enforced: the request's design, after the switch, decides
        if ( $mode === self::MODE_ENFORCE
             && self::designListed( $siteDesign, $value( 'ContentSecurityPolicy', 'ReportOnlyDesigns', array() ) ) )
            $mode = self::MODE_REPORT_ONLY;
        // A view that sends a policy of its own (for its own inline scripts) is not enforced by the kernel's: under a
        // server that does not let the view's header replace the kernel's, both would apply and block the view
        if ( $mode === self::MODE_ENFORCE
             && self::designListed( $view === null ? self::currentView() : $view,
                                    $value( 'ContentSecurityPolicy', 'ReportOnlyViews', array() ) ) )
            $mode = self::MODE_REPORT_ONLY;
        return array(
            'mode' => $mode,
            'directives' => $value( 'ContentSecurityPolicy', 'Directives', array() ),
            'additionalSources' => $value( 'ContentSecurityPolicy', 'AdditionalSources', array() ),
            'reportOnlyDirectives' => $value( 'ContentSecurityPolicy', 'ReportOnlyDirectives', array() ),
            'nonce' => strtolower( trim( (string)$value( 'ContentSecurityPolicy', 'Nonce', 'disabled' ) ) ) === 'enabled',
            'nonceDirectives' => is_array( $nonceDirectives ) ? $nonceDirectives : array(),
            'reportURI' => $value( 'ContentSecurityPolicy', 'ReportURI', '' ),
            'reportEndpoint' => strtolower( trim( (string)$value( 'ContentSecurityPolicy', 'ReportEndpoint', 'disabled' ) ) ) === 'enabled',
        );
    }

    /**
     * The module/view of the current request ("dse/adminneo"), from the request's URI after the siteaccess was
     * matched; empty before that or outside a web request.
     *
     * @return string
     */
    public static function currentView()
    {
        if ( !isset( $GLOBALS['eZURIRequestInstance'] ) || !$GLOBALS['eZURIRequestInstance'] instanceof eZURI )
            return '';
        $uri = $GLOBALS['eZURIRequestInstance'];
        $module = (string)$uri->element( 0 );
        $view = (string)$uri->element( 1 );
        return $module === '' ? '' : strtolower( $module . '/' . $view );
    }

    /**
     * Whether a design (or a module/view) is in a list such as ReportOnlyDesigns[] (names compared without case).
     *
     * @param mixed $siteDesign [DesignSettings] SiteDesign
     * @param mixed $designs ReportOnlyDesigns[]
     * @return bool
     */
    public static function designListed( $siteDesign, $designs )
    {
        $siteDesign = is_scalar( $siteDesign ) ? strtolower( trim( (string)$siteDesign ) ) : '';
        if ( $siteDesign === '' || !is_array( $designs ) )
            return false;
        foreach ( $designs as $design )
        {
            if ( is_scalar( $design ) && strtolower( trim( (string)$design ) ) === $siteDesign )
                return true;
        }
        return false;
    }

    /**
     * The directives a report-only policy may carry: browsers ignore frame-ancestors and sandbox there (and say so in
     * the console on every page), so they are left out of Content-Security-Policy-Report-Only and sent only when the
     * policy is enforced. Framing stays limited by SecurityHeaders[] (X-Frame-Options, frame-ancestors).
     *
     * @param mixed $directives Directives[] (name => sources)
     * @return array
     */
    public static function reportOnlyDirectives( $directives )
    {
        if ( !is_array( $directives ) )
            return array();
        foreach ( array_keys( $directives ) as $name )
        {
            if ( in_array( strtolower( trim( (string)$name ) ), self::NOT_IN_REPORT_ONLY, true ) )
                unset( $directives[$name] );
        }
        return $directives;
    }

    /**
     * The mode in use: auto resolved by the designs, an unknown value disabled.
     *
     * @param mixed $mode [ContentSecurityPolicy] Mode
     * @param mixed $siteDesign [DesignSettings] SiteDesign
     * @param mixed $additionalDesigns [DesignSettings] AdditionalSiteDesignList[]: a siteaccess whose own design
     *        builds on an administration design (an editor siteaccess on admin4l) counts as an administration one
     * @return string one of the MODE_ constants
     */
    public static function mode( $mode, $siteDesign, $additionalDesigns = array() )
    {
        $mode = is_scalar( $mode ) ? strtolower( trim( (string)$mode ) ) : '';
        if ( $mode === 'auto' )
        {
            $designs = is_array( $additionalDesigns ) ? $additionalDesigns : array();
            array_unshift( $designs, $siteDesign );
            foreach ( $designs as $design )
            {
                $design = is_scalar( $design ) ? trim( (string)$design ) : '';
                if ( strncasecmp( $design, 'admin', 5 ) === 0 )
                    return self::MODE_REPORT_ONLY;
            }
            return self::MODE_DISABLED;
        }
        return in_array( $mode, array( self::MODE_REPORT_ONLY, self::MODE_ENFORCE ), true ) ? $mode : self::MODE_DISABLED;
    }

    /**
     * The policy from its directives: "name sources; name sources". A directive whose name is not a directive name
     * (letters, digits and dashes) or whose sources hold a character that would end the directive, the policy or the
     * header line (";", ",", a line break) is left out, so a setting cannot split the header or add a directive of
     * its own.
     *
     * @param mixed $directives Directives[] (name => sources)
     * @param mixed $reportUri ReportURI (report-uri)
     * @param mixed $reportTo the name of a Reporting-Endpoints endpoint (report-to)
     * @return string the policy; empty without a single directive
     */
    public static function policy( $directives, $reportUri = '', $reportTo = '' )
    {
        $parts = array();
        foreach ( self::normalize( $directives ) as $name => $sources )
        {
            if ( $name === 'report-uri' || $name === 'report-to' )
                continue;
            $parts[$name] = $name . ' ' . $sources;
        }
        if ( !$parts )
            return '';
        $reportUri = is_scalar( $reportUri ) ? trim( (string)$reportUri ) : '';
        if ( $reportUri !== '' && !preg_match( '/[\s;,\0]/', $reportUri ) )
            $parts['report-uri'] = 'report-uri ' . $reportUri;
        $reportTo = is_scalar( $reportTo ) ? trim( (string)$reportTo ) : '';
        if ( $reportTo !== '' && preg_match( '/^[A-Za-z0-9_-]+$/D', $reportTo ) )
            $parts['report-to'] = 'report-to ' . $reportTo;
        return implode( '; ', $parts );
    }

    /**
     * The directives with valid names and sources only: lower-case name => sources with single spaces. Left out: an
     * empty value, a name that is no directive name, sources with ";", ",", a line break or NUL.
     *
     * @param mixed $directives name => sources
     * @return array
     */
    public static function normalize( $directives )
    {
        $result = array();
        if ( !is_array( $directives ) )
            return $result;
        foreach ( $directives as $name => $sources )
        {
            $name = strtolower( trim( (string)$name ) );
            $sources = is_scalar( $sources ) ? trim( preg_replace( '/[ \t]+/', ' ', (string)$sources ) ) : '';
            if ( !preg_match( '/^[a-z][a-z0-9-]*$/D', $name ) || $sources === '' || preg_match( '/[;,\r\n\0]/', $sources ) )
                continue;
            $result[$name] = $sources;
        }
        return $result;
    }

    /**
     * The directives with AdditionalSources[] added: each entry "<directive> <sources>" adds its sources (those the
     * directive does not have yet) to a directive of the list. An entry for a directive the list does not have is
     * ignored, so it cannot start a directive that would replace the default-src fallback; an entry with ";", ","
     * or a line break too.
     *
     * @param mixed $directives name => sources
     * @param mixed $additional AdditionalSources[]
     * @return array normalized directives
     */
    public static function withSources( $directives, $additional )
    {
        $directives = self::normalize( $directives );
        if ( !is_array( $additional ) )
            return $directives;
        foreach ( $additional as $entry )
        {
            if ( !is_scalar( $entry ) )
                continue;
            $entry = trim( (string)$entry );
            if ( $entry === '' || preg_match( '/[;,\r\n\0]/', $entry ) )
                continue;
            $words = preg_split( '/[ \t]+/', $entry );
            $name = strtolower( array_shift( $words ) );
            if ( !isset( $directives[$name] ) || !$words )
                continue;
            $sources = self::sourceList( $directives[$name] );
            foreach ( $words as $word )
            {
                if ( !in_array( $word, $sources, true ) )
                    $sources[] = $word;
            }
            $directives[$name] = implode( ' ', $sources );
        }
        return $directives;
    }

    /**
     * The directives with the nonce added to those of $nonceDirectives that do not allow 'unsafe-inline' (a nonce
     * would make browsers ignore it) and are in the list.
     *
     * @param array $directives normalized directives
     * @param string $nonce empty: none
     * @param array $nonceDirectives NonceDirectives[]
     * @return array
     */
    public static function withNonce( array $directives, $nonce, $nonceDirectives )
    {
        if ( !is_string( $nonce ) || $nonce === '' || !preg_match( '/^[A-Za-z0-9+\/_=-]+$/D', $nonce ) || !is_array( $nonceDirectives ) )
            return $directives;
        foreach ( $nonceDirectives as $name )
        {
            $name = strtolower( trim( (string)$name ) );
            if ( !isset( $directives[$name] ) )
                continue;
            $sources = self::sourceList( $directives[$name] );
            if ( in_array( "'unsafe-inline'", $sources, true ) || in_array( "'none'", $sources, true ) )
                continue;
            $directives[$name] .= " 'nonce-" . $nonce . "'";
        }
        return $directives;
    }

    /**
     * Where the reports go: report-uri, report-to and the Reporting-Endpoints header. The address is ReportURI, or
     * with ReportEndpoint=enabled and ReportURI empty this siteaccess's csp/report. report-to and Reporting-Endpoints
     * need an absolute https address (or http) without quotes or backslashes.
     *
     * @param mixed $reportUri ReportURI
     * @param bool $endpoint ReportEndpoint=enabled
     * @param string|null $ownUri the address of csp/report; null: built with eZURI (tests pass one)
     * @return array report-uri, report-to, endpoints (the Reporting-Endpoints value); each '' when not used
     */
    public static function reportParts( $reportUri, $endpoint = false, $ownUri = null )
    {
        $parts = array( 'report-uri' => '', 'report-to' => '', 'endpoints' => '' );
        $uri = is_scalar( $reportUri ) ? trim( (string)$reportUri ) : '';
        if ( $uri === '' && $endpoint )
        {
            if ( $ownUri === null && class_exists( 'eZURI' ) )
            {
                $ownUri = self::REPORT_ENDPOINT_PATH;
                eZURI::transformURI( $ownUri, false, 'full', false );
            }
            $uri = is_scalar( $ownUri ) ? trim( (string)$ownUri ) : '';
        }
        if ( $uri === '' || preg_match( '/[\s;,\0]/', $uri ) )
            return $parts;
        $parts['report-uri'] = $uri;
        if ( preg_match( '#^https?://[^\s"\\\\]+$#Di', $uri ) )
        {
            $parts['report-to'] = self::REPORT_ENDPOINT_NAME;
            $parts['endpoints'] = self::REPORT_ENDPOINT_NAME . '="' . $uri . '"';
        }
        return $parts;
    }

    /**
     * The sources of a directive as a list.
     *
     * @param string $sources
     * @return array
     */
    private static function sourceList( $sources )
    {
        return array_values( array_filter( preg_split( '/[ \t]+/', trim( (string)$sources ) ), 'strlen' ) );
    }

    /**
     * The security headers with the policy added: a report-only policy as its own header, an enforced one after a
     * Content-Security-Policy that is already there (two policies of one header, separated by a comma).
     *
     * @param array $headers header name => value ([HTTPHeaderSettings] SecurityHeaders[])
     * @param array $policy headers()
     * @return array header name => value
     */
    public static function merge( array $headers, array $policy )
    {
        foreach ( $policy as $name => $value )
        {
            $existing = null;
            foreach ( array_keys( $headers ) as $key )
            {
                if ( strcasecmp( $key, $name ) === 0 )
                    $existing = $key;
            }
            if ( $existing === null )
                $headers[$name] = $value;
            else
                $headers[$existing] = $headers[$existing] . ', ' . $value;
        }
        return $headers;
    }
}
