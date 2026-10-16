<?php
/**
 * ezpContentSecurityPolicy and the security headers of ezpKernelWeb: the mode (auto by the design), the policy from
 * its directives (nothing a setting could use to split the header or add a directive), the report-only and the
 * enforced header beside SecurityHeaders[Content-Security-Policy], and the defaults of settings/site.ini.
 *
 * No database. The site.ini settings are set by each test and put back.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class ezpContentSecurityPolicyTest extends PHPUnit\Framework\TestCase
{
    /** @var array section => variable => array( saved value ) or null (not set) */
    private $saved = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        foreach ( $this->saved as $section => $variables )
        {
            foreach ( $variables as $name => $value )
            {
                if ( $value === null )
                    $ini->removeSetting( $section, $name );
                else
                    $ini->setVariable( $section, $name, $value[0] );
            }
        }
        $this->saved = array();
    }

    private function setting( $section, $name, $value )
    {
        $ini = eZINI::instance();
        if ( !isset( $this->saved[$section] ) || !array_key_exists( $name, $this->saved[$section] ) )
            $this->saved[$section][$name] = $ini->hasVariable( $section, $name ) ? array( $ini->variable( $section, $name ) ) : null;
        $ini->setVariable( $section, $name, $value );
    }

    // ------------------------------------------------------------ mode

    public function testModeAutoIsReportOnlyForAdministrationDesignsOnly()
    {
        foreach ( array( 'admin', 'admin4', 'admin4l', 'adminui', 'Admin3' ) as $design )
            $this->assertSame( 'report-only', ezpContentSecurityPolicy::mode( 'auto', $design ), $design );
        foreach ( array( 'standard', 'mysite', 'plain', '', 'myadmin', null ) as $design )
            $this->assertSame( 'disabled', ezpContentSecurityPolicy::mode( 'auto', $design ), var_export( $design, true ) );
    }

    public function testModeExplicitValuesAndUnknownOnes()
    {
        $this->assertSame( 'enforce', ezpContentSecurityPolicy::mode( 'enforce', 'standard' ) );
        $this->assertSame( 'enforce', ezpContentSecurityPolicy::mode( ' Enforce ', 'admin4' ) );
        $this->assertSame( 'report-only', ezpContentSecurityPolicy::mode( 'report-only', 'standard' ) );
        $this->assertSame( 'disabled', ezpContentSecurityPolicy::mode( 'disabled', 'admin4' ) );
        foreach ( array( '', 'on', 'enabled', 'report', array( 'enforce' ), null ) as $mode )
            $this->assertSame( 'disabled', ezpContentSecurityPolicy::mode( $mode, 'admin4' ), var_export( $mode, true ) );
    }

    // ------------------------------------------------------------ policy

    public function testPolicyJoinsTheDirectivesInTheirOrder()
    {
        $this->assertSame(
            "default-src 'self'; script-src 'self' https://cdn.example.com; object-src 'none'",
            ezpContentSecurityPolicy::policy( array(
                'default-src' => "'self'",
                'Script-Src' => " 'self'   https://cdn.example.com ",
                'object-src' => "'none'",
            ) )
        );
    }

    public function testPolicyLeavesOutEmptyAndUnsafeDirectives()
    {
        $policy = ezpContentSecurityPolicy::policy( array(
            'default-src' => "'self'",
            'style-src' => '',
            'img-src' => "'self'; script-src *",
            'font-src' => "'self', script-src *",
            'media-src' => "'self'\r\nX-Injected: 1",
            'bad name' => "'self'",
            'connect-src;' => "'self'",
            'report-uri' => 'https://elsewhere.example.com/',
            0 => "'self'",
        ) );
        $this->assertSame( "default-src 'self'", $policy );
        $this->assertSame( '', ezpContentSecurityPolicy::policy( array() ) );
        $this->assertSame( '', ezpContentSecurityPolicy::policy( 'default-src *' ) );
        $this->assertSame( '', ezpContentSecurityPolicy::policy( array( 'default-src' => '' ) ) );
    }

    public function testPolicyReportUri()
    {
        $this->assertSame( "default-src 'self'; report-uri /csp-report",
                           ezpContentSecurityPolicy::policy( array( 'default-src' => "'self'" ), ' /csp-report ' ) );
        foreach ( array( '/a b', '/a;script-src *', '/a,b', "/a\nb" ) as $uri )
            $this->assertSame( "default-src 'self'", ezpContentSecurityPolicy::policy( array( 'default-src' => "'self'" ), $uri ), $uri );
    }

    // ------------------------------------------------------------ merge

    public function testMergeAddsReportOnlyAsHeaderOfItsOwn()
    {
        $headers = array( 'Content-Security-Policy' => "frame-ancestors 'self'", 'X-Frame-Options' => 'SAMEORIGIN' );
        $this->assertSame(
            array( 'Content-Security-Policy' => "frame-ancestors 'self'", 'X-Frame-Options' => 'SAMEORIGIN',
                   'Content-Security-Policy-Report-Only' => "script-src 'self'" ),
            ezpContentSecurityPolicy::merge( $headers, array( 'Content-Security-Policy-Report-Only' => "script-src 'self'" ) )
        );
    }

    public function testMergeSendsAnEnforcedPolicyAsSecondPolicyOfTheHeader()
    {
        $this->assertSame(
            array( 'content-security-policy' => "frame-ancestors 'self', script-src 'self'" ),
            ezpContentSecurityPolicy::merge( array( 'content-security-policy' => "frame-ancestors 'self'" ),
                                             array( 'Content-Security-Policy' => "script-src 'self'" ) )
        );
        $this->assertSame( array( 'Content-Security-Policy' => "script-src 'self'" ),
                           ezpContentSecurityPolicy::merge( array(), array( 'Content-Security-Policy' => "script-src 'self'" ) ) );
        $this->assertSame( array( 'A' => 'b' ), ezpContentSecurityPolicy::merge( array( 'A' => 'b' ), array() ) );
    }

    // ------------------------------------------------------------ headers from site.ini

    public function testHeadersFollowTheMode()
    {
        $this->setting( 'ContentSecurityPolicy', 'Directives', array( 'default-src' => "'self'", 'object-src' => "'none'" ) );
        $this->setting( 'ContentSecurityPolicy', 'ReportURI', '' );
        $this->setting( 'DesignSettings', 'SiteDesign', 'admin4' );

        $this->setting( 'ContentSecurityPolicy', 'Mode', 'auto' );
        $this->assertSame( array( 'Content-Security-Policy-Report-Only' => "default-src 'self'; object-src 'none'" ),
                           ezpContentSecurityPolicy::headers() );

        $this->setting( 'ContentSecurityPolicy', 'Mode', 'enforce' );
        $this->assertSame( array( 'Content-Security-Policy' => "default-src 'self'; object-src 'none'" ),
                           ezpContentSecurityPolicy::headers() );

        $this->setting( 'ContentSecurityPolicy', 'Mode', 'disabled' );
        $this->assertSame( array(), ezpContentSecurityPolicy::headers() );

        $this->setting( 'ContentSecurityPolicy', 'Mode', 'auto' );
        $this->setting( 'DesignSettings', 'SiteDesign', 'standard' );
        $this->assertSame( array(), ezpContentSecurityPolicy::headers() );

        $this->setting( 'ContentSecurityPolicy', 'Mode', 'report-only' );
        $this->setting( 'ContentSecurityPolicy', 'Directives', array() );
        $this->assertSame( array(), ezpContentSecurityPolicy::headers(), 'no directive, no header' );
    }

    public function testKernelSecurityHeadersCarryThePolicy()
    {
        $this->setting( 'HTTPHeaderSettings', 'SecurityHeaders', array( 'X-Content-Type-Options' => 'nosniff', 'Content-Security-Policy' => "frame-ancestors 'self'" ) );
        $this->setting( 'ContentSecurityPolicy', 'Directives', array( 'script-src' => "'self'" ) );
        $this->setting( 'ContentSecurityPolicy', 'ReportURI', '' );
        $this->setting( 'DesignSettings', 'SiteDesign', 'admin4' );

        $this->setting( 'ContentSecurityPolicy', 'Mode', 'auto' );
        $headers = ezpKernelWeb::securityHeaders();
        $this->assertSame( "frame-ancestors 'self'", $headers['Content-Security-Policy'] );
        $this->assertSame( "script-src 'self'", $headers['Content-Security-Policy-Report-Only'] );
        $this->assertSame( 'nosniff', $headers['X-Content-Type-Options'] );

        $this->setting( 'ContentSecurityPolicy', 'Mode', 'enforce' );
        $headers = ezpKernelWeb::securityHeaders();
        $this->assertSame( "frame-ancestors 'self', script-src 'self'", $headers['Content-Security-Policy'] );
        $this->assertArrayNotHasKey( 'Content-Security-Policy-Report-Only', $headers );

        $this->setting( 'ContentSecurityPolicy', 'Mode', 'disabled' );
        $headers = ezpKernelWeb::securityHeaders();
        $this->assertSame( array( 'X-Content-Type-Options' => 'nosniff', 'Content-Security-Policy' => "frame-ancestors 'self'" ), $headers );
    }

    public function testShippedDefaults()
    {
        $ini = eZINI::fetchFromFile( 'settings/site.ini' );
        $this->assertSame( 'auto', $ini->variable( 'ContentSecurityPolicy', 'Mode' ) );
        $this->assertSame( '', $ini->variable( 'ContentSecurityPolicy', 'ReportURI' ) );
        $policy = ezpContentSecurityPolicy::policy( $ini->variable( 'ContentSecurityPolicy', 'Directives' ) );
        foreach ( array( "default-src 'self'", "script-src 'self'", "style-src 'self'", "object-src 'none'",
                         "base-uri 'self'", "frame-ancestors 'self'", "form-action 'self'" ) as $directive )
            $this->assertStringContainsString( $directive, $policy );
        // the strict default: nothing that lets inline code or eval() run
        $this->assertStringNotContainsString( 'unsafe-', $policy );
    }
}
