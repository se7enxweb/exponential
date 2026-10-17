<?php
/**
 * File containing the ezpContentSecurityPolicy class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * The Content Security Policy of a page, from site.ini [ContentSecurityPolicy].
 *
 * - Mode: disabled, report-only (Content-Security-Policy-Report-Only: the browser reports what the policy would
 *   block and blocks nothing), enforce (Content-Security-Policy), or auto: report-only on an administration
 *   siteaccess ([DesignSettings] SiteDesign begins with "admin"), disabled elsewhere.
 * - Directives[<directive>]=<sources>: the policy, one directive per entry; an empty value leaves the directive out.
 *   frame-ancestors and sandbox are sent only when the policy is enforced (browsers ignore them in report-only).
 * - ReportURI: where the browser sends its reports (report-uri); empty: none.
 *
 * The policy is a header of its own beside [HTTPHeaderSettings] SecurityHeaders[]: an enforced policy is sent after
 * the Content-Security-Policy that SecurityHeaders[] already carries (frame-ancestors 'self' by default), as a second
 * policy of the same header, and the browser applies both.
 *
 * Settings: site.ini [ContentSecurityPolicy] of the current siteaccess. See doc/features/6.0/content-security-policy.md.
 */
class ezpContentSecurityPolicy
{
    const MODE_DISABLED = 'disabled';
    const MODE_REPORT_ONLY = 'report-only';
    const MODE_ENFORCE = 'enforce';

    const HEADER_ENFORCE = 'Content-Security-Policy';
    const HEADER_REPORT_ONLY = 'Content-Security-Policy-Report-Only';

    /** Directives browsers ignore in a report-only policy */
    const NOT_IN_REPORT_ONLY = array( 'frame-ancestors', 'sandbox' );

    /**
     * The headers of the policy for the current siteaccess: none, or one of Content-Security-Policy and
     * Content-Security-Policy-Report-Only.
     *
     * @param eZINI|null $ini site.ini; null: the current siteaccess's
     * @return array header name => value
     */
    public static function headers( $ini = null )
    {
        if ( !$ini instanceof eZINI )
            $ini = eZINI::instance();
        $value = function ( $name, $default ) use ( $ini ) {
            return $ini->hasVariable( 'ContentSecurityPolicy', $name ) ? $ini->variable( 'ContentSecurityPolicy', $name ) : $default;
        };
        $siteDesign = $ini->hasVariable( 'DesignSettings', 'SiteDesign' ) ? $ini->variable( 'DesignSettings', 'SiteDesign' ) : '';
        $mode = self::mode( $value( 'Mode', self::MODE_DISABLED ), $siteDesign );
        if ( $mode === self::MODE_DISABLED )
            return array();

        $directives = $value( 'Directives', array() );
        if ( $mode === self::MODE_REPORT_ONLY )
            $directives = self::reportOnlyDirectives( $directives );
        $policy = self::policy( $directives, $value( 'ReportURI', '' ) );
        if ( $policy === '' )
            return array();
        return array( $mode === self::MODE_ENFORCE ? self::HEADER_ENFORCE : self::HEADER_REPORT_ONLY => $policy );
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
     * The mode in use: auto resolved by the design, an unknown value disabled.
     *
     * @param mixed $mode [ContentSecurityPolicy] Mode
     * @param mixed $siteDesign [DesignSettings] SiteDesign
     * @return string one of the MODE_ constants
     */
    public static function mode( $mode, $siteDesign )
    {
        $mode = is_scalar( $mode ) ? strtolower( trim( (string)$mode ) ) : '';
        if ( $mode === 'auto' )
        {
            $siteDesign = is_scalar( $siteDesign ) ? trim( (string)$siteDesign ) : '';
            return strncasecmp( $siteDesign, 'admin', 5 ) === 0 ? self::MODE_REPORT_ONLY : self::MODE_DISABLED;
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
     * @param mixed $reportUri ReportURI
     * @return string the policy; empty without a single directive
     */
    public static function policy( $directives, $reportUri = '' )
    {
        $parts = array();
        if ( is_array( $directives ) )
        {
            foreach ( $directives as $name => $sources )
            {
                $name = strtolower( trim( (string)$name ) );
                $sources = is_scalar( $sources ) ? trim( preg_replace( '/[ \t]+/', ' ', (string)$sources ) ) : '';
                if ( !preg_match( '/^[a-z][a-z0-9-]*$/D', $name ) || $name === 'report-uri' || $sources === ''
                     || preg_match( '/[;,\r\n\0]/', $sources ) )
                    continue;
                $parts[$name] = $name . ' ' . $sources;
            }
        }
        if ( !$parts )
            return '';
        $reportUri = is_scalar( $reportUri ) ? trim( (string)$reportUri ) : '';
        if ( $reportUri !== '' && !preg_match( '/[\s;,\0]/', $reportUri ) )
            $parts['report-uri'] = 'report-uri ' . $reportUri;
        return implode( '; ', $parts );
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
