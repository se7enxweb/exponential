<?php
/**
 * expContentSecurityPolicy and the security headers of ezpKernelWeb: the mode (auto by the design), the policy from
 * its directives (nothing a setting could use to split the header or add a directive), the report-only and the
 * enforced header beside SecurityHeaders[Content-Security-Policy], and the defaults of settings/site.ini.
 *
 * No database. The site.ini settings are set by each test and put back.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class expContentSecurityPolicyTest extends PHPUnit\Framework\TestCase
{
    /** @var array section => variable => array( saved value ) or null (not set) */
    private $saved = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        // The tests of one setting see none of the others; each test turns on what it checks
        $this->setting( 'ContentSecurityPolicy', 'Nonce', 'disabled' );
        $this->setting( 'ContentSecurityPolicy', 'ReportEndpoint', 'disabled' );
        $this->setting( 'ContentSecurityPolicy', 'AdditionalSources', array() );
        $this->setting( 'ContentSecurityPolicy', 'ReportOnlyDirectives', array() );
        $this->setting( 'ContentSecurityPolicy', 'ReportOnlyDesigns', array() );
        $this->setting( 'ContentSecurityPolicy', 'ReportOnlyViews', array() );
        $this->setting( 'DesignSettings', 'AdditionalSiteDesignList', array() );
        expContentSecurityPolicy::reset();
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

    /** Sets a site.ini value for this test (null: removes it); tearDown() puts the old one back */
    private function setting( $section, $name, $value )
    {
        $ini = eZINI::instance();
        if ( !isset( $this->saved[$section] ) || !array_key_exists( $name, $this->saved[$section] ) )
            $this->saved[$section][$name] = $ini->hasVariable( $section, $name ) ? array( $ini->variable( $section, $name ) ) : null;
        if ( $value === null )
            $ini->removeSetting( $section, $name );
        else
            $ini->setVariable( $section, $name, $value );
    }

    // ------------------------------------------------------------ mode

    public function testModeAutoIsReportOnlyForAdministrationDesignsOnly()
    {
        foreach ( array( 'admin', 'admin4', 'admin4l', 'adminui', 'Admin3' ) as $design )
            $this->assertSame( 'report-only', expContentSecurityPolicy::mode( 'auto', $design ), $design );
        foreach ( array( 'standard', 'mysite', 'plain', '', 'myadmin', null ) as $design )
            $this->assertSame( 'disabled', expContentSecurityPolicy::mode( 'auto', $design ), var_export( $design, true ) );
    }

    public function testModeExplicitValuesAndUnknownOnes()
    {
        $this->assertSame( 'enforce', expContentSecurityPolicy::mode( 'enforce', 'standard' ) );
        $this->assertSame( 'enforce', expContentSecurityPolicy::mode( ' Enforce ', 'admin4' ) );
        $this->assertSame( 'report-only', expContentSecurityPolicy::mode( 'report-only', 'standard' ) );
        $this->assertSame( 'disabled', expContentSecurityPolicy::mode( 'disabled', 'admin4' ) );
        foreach ( array( '', 'on', 'enabled', 'report', array( 'enforce' ), null ) as $mode )
            $this->assertSame( 'disabled', expContentSecurityPolicy::mode( $mode, 'admin4' ), var_export( $mode, true ) );
    }

    // ------------------------------------------------------------ policy

    public function testPolicyJoinsTheDirectivesInTheirOrder()
    {
        $this->assertSame(
            "default-src 'self'; script-src 'self' https://cdn.example.com; object-src 'none'",
            expContentSecurityPolicy::policy( array(
                'default-src' => "'self'",
                'Script-Src' => " 'self'   https://cdn.example.com ",
                'object-src' => "'none'",
            ) )
        );
    }

    public function testPolicyLeavesOutEmptyAndUnsafeDirectives()
    {
        $policy = expContentSecurityPolicy::policy( array(
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
        $this->assertSame( '', expContentSecurityPolicy::policy( array() ) );
        $this->assertSame( '', expContentSecurityPolicy::policy( 'default-src *' ) );
        $this->assertSame( '', expContentSecurityPolicy::policy( array( 'default-src' => '' ) ) );
    }

    public function testPolicyReportUri()
    {
        $this->assertSame( "default-src 'self'; report-uri /csp-report",
                           expContentSecurityPolicy::policy( array( 'default-src' => "'self'" ), ' /csp-report ' ) );
        foreach ( array( '/a b', '/a;script-src *', '/a,b', "/a\nb" ) as $uri )
            $this->assertSame( "default-src 'self'", expContentSecurityPolicy::policy( array( 'default-src' => "'self'" ), $uri ), $uri );
    }

    // ------------------------------------------------------------ merge

    public function testMergeAddsReportOnlyAsHeaderOfItsOwn()
    {
        $headers = array( 'Content-Security-Policy' => "frame-ancestors 'self'", 'X-Frame-Options' => 'SAMEORIGIN' );
        $this->assertSame(
            array( 'Content-Security-Policy' => "frame-ancestors 'self'", 'X-Frame-Options' => 'SAMEORIGIN',
                   'Content-Security-Policy-Report-Only' => "script-src 'self'" ),
            expContentSecurityPolicy::merge( $headers, array( 'Content-Security-Policy-Report-Only' => "script-src 'self'" ) )
        );
    }

    public function testMergeSendsAnEnforcedPolicyAsSecondPolicyOfTheHeader()
    {
        $this->assertSame(
            array( 'content-security-policy' => "frame-ancestors 'self', script-src 'self'" ),
            expContentSecurityPolicy::merge( array( 'content-security-policy' => "frame-ancestors 'self'" ),
                                             array( 'Content-Security-Policy' => "script-src 'self'" ) )
        );
        $this->assertSame( array( 'Content-Security-Policy' => "script-src 'self'" ),
                           expContentSecurityPolicy::merge( array(), array( 'Content-Security-Policy' => "script-src 'self'" ) ) );
        $this->assertSame( array( 'A' => 'b' ), expContentSecurityPolicy::merge( array( 'A' => 'b' ), array() ) );
    }

    // ------------------------------------------------------------ headers from site.ini

    public function testHeadersFollowTheMode()
    {
        $this->setting( 'ContentSecurityPolicy', 'Directives', array( 'default-src' => "'self'", 'object-src' => "'none'" ) );
        $this->setting( 'ContentSecurityPolicy', 'ReportURI', '' );
        $this->setting( 'DesignSettings', 'SiteDesign', 'admin4' );

        $this->setting( 'ContentSecurityPolicy', 'Mode', 'auto' );
        $this->assertSame( array( 'Content-Security-Policy-Report-Only' => "default-src 'self'; object-src 'none'" ),
                           expContentSecurityPolicy::headers() );

        $this->setting( 'ContentSecurityPolicy', 'Mode', 'enforce' );
        $this->assertSame( array( 'Content-Security-Policy' => "default-src 'self'; object-src 'none'" ),
                           expContentSecurityPolicy::headers() );

        $this->setting( 'ContentSecurityPolicy', 'Mode', 'disabled' );
        $this->assertSame( array(), expContentSecurityPolicy::headers() );

        $this->setting( 'ContentSecurityPolicy', 'Mode', 'auto' );
        $this->setting( 'DesignSettings', 'SiteDesign', 'standard' );
        $this->assertSame( array(), expContentSecurityPolicy::headers() );

        $this->setting( 'ContentSecurityPolicy', 'Mode', 'report-only' );
        $this->setting( 'ContentSecurityPolicy', 'Directives', array() );
        $this->assertSame( array(), expContentSecurityPolicy::headers(), 'no directive, no header' );
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

    /** Browsers ignore frame-ancestors and sandbox in a report-only policy: sent only when enforced */
    public function testReportOnlyLeavesOutWhatBrowsersIgnoreThere()
    {
        $this->setting( 'ContentSecurityPolicy', 'Directives', array( 'default-src' => "'self'", 'Frame-Ancestors' => "'self'",
                                                                      'sandbox' => 'allow-forms', 'object-src' => "'none'" ) );
        $this->setting( 'ContentSecurityPolicy', 'ReportURI', '/csp-report' );
        $this->setting( 'DesignSettings', 'SiteDesign', 'admin4' );

        $this->setting( 'ContentSecurityPolicy', 'Mode', 'report-only' );
        $this->assertSame( array( 'Content-Security-Policy-Report-Only' => "default-src 'self'; object-src 'none'; report-uri /csp-report" ),
                           expContentSecurityPolicy::headers() );
        $this->setting( 'ContentSecurityPolicy', 'Mode', 'auto' );
        $this->assertSame( array( 'Content-Security-Policy-Report-Only' => "default-src 'self'; object-src 'none'; report-uri /csp-report" ),
                           expContentSecurityPolicy::headers() );

        $this->setting( 'ContentSecurityPolicy', 'Mode', 'enforce' );
        $this->assertSame( array( 'Content-Security-Policy' => "default-src 'self'; frame-ancestors 'self'; sandbox allow-forms; object-src 'none'; report-uri /csp-report" ),
                           expContentSecurityPolicy::headers() );

        $this->assertSame( array( 'a' => 'b' ), expContentSecurityPolicy::reportOnlyDirectives( array( 'a' => 'b', ' SANDBOX ' => 'x' ) ) );
        $this->assertSame( array(), expContentSecurityPolicy::reportOnlyDirectives( 'frame-ancestors x' ) );
    }

    /** Without SecurityHeaders[] (removed, or not a list) the policy is sent all the same */
    public function testThePolicyIsSentWithoutSecurityHeaders()
    {
        $this->setting( 'ContentSecurityPolicy', 'Directives', array( 'script-src' => "'self'" ) );
        $this->setting( 'ContentSecurityPolicy', 'ReportURI', '' );
        $this->setting( 'ContentSecurityPolicy', 'Mode', 'report-only' );
        foreach ( array( null, 'not a list' ) as $securityHeaders )
        {
            $this->setting( 'HTTPHeaderSettings', 'SecurityHeaders', $securityHeaders );
            $this->assertSame( array( 'Content-Security-Policy-Report-Only' => "script-src 'self'" ), ezpKernelWeb::securityHeaders(),
                               var_export( $securityHeaders, true ) );
        }
    }

    public function testShippedDefaults()
    {
        $ini = eZINI::fetchFromFile( 'settings/site.ini' );
        $this->assertSame( 'auto', $ini->variable( 'ContentSecurityPolicy', 'Mode' ) );
        $this->assertSame( '', $ini->variable( 'ContentSecurityPolicy', 'ReportURI' ) );
        $policy = expContentSecurityPolicy::policy( $ini->variable( 'ContentSecurityPolicy', 'Directives' ) );
        foreach ( array( "default-src 'self'", "script-src 'self'", "style-src 'self'", "object-src 'none'",
                         "base-uri 'self'", "frame-ancestors 'self'", "form-action 'self'" ) as $directive )
            $this->assertStringContainsString( $directive, $policy );
        // the strict default: nothing that lets inline code or eval() run
        $this->assertStringNotContainsString( 'unsafe-', $policy );
        // the settings of enforcing: none changes the policy of a new installation but the nonce of a report-only one
        $this->assertSame( 'enabled', $ini->variable( 'ContentSecurityPolicy', 'Nonce' ) );
        $this->assertSame( array( 'script-src' ), $ini->variable( 'ContentSecurityPolicy', 'NonceDirectives' ) );
        $this->assertSame( 'disabled', $ini->variable( 'ContentSecurityPolicy', 'ReportEndpoint' ) );
        $this->assertSame( array(), $ini->variable( 'ContentSecurityPolicy', 'AdditionalSources' ) );
        $this->assertSame( array(), $ini->variable( 'ContentSecurityPolicy', 'ReportOnlyDirectives' ) );
        $this->assertSame( array( 'admin', 'admin2', 'admin_classic' ), $ini->variable( 'ContentSecurityPolicy', 'ReportOnlyDesigns' ) );
        $this->assertSame( array(), $ini->variable( 'ContentSecurityPolicy', 'ReportOnlyViews' ) );
    }

    // ------------------------------------------------------------ enforcing (#600)

    public function testModeAutoCountsAnAdditionalAdministrationDesign()
    {
        $this->assertSame( 'report-only', expContentSecurityPolicy::mode( 'auto', 'editor', array( 'admin4l' ) ) );
        $this->assertSame( 'disabled', expContentSecurityPolicy::mode( 'auto', 'editor', array( 'standard', 'base' ) ) );
        $this->assertSame( 'disabled', expContentSecurityPolicy::mode( 'auto', 'editor', 'admin4l' ), 'not a list' );
        $this->assertSame( 'enforce', expContentSecurityPolicy::mode( 'enforce', 'editor', array() ) );
    }

    public function testAdditionalSourcesAddToExistingDirectivesOnly()
    {
        $directives = array( 'default-src' => "'self'", 'script-src' => "'self'", 'img-src' => "'self' data:" );
        $this->assertSame(
            array( 'default-src' => "'self'", 'script-src' => "'self' https://maps.example.com https://captcha.example.com",
                   'img-src' => "'self' data: https://maps.example.com" ),
            expContentSecurityPolicy::withSources( $directives, array(
                'script-src https://maps.example.com',
                'Script-Src  https://captcha.example.com   https://maps.example.com',
                'img-src data: https://maps.example.com',
                'frame-src https://not-there.example.com',   // no frame-src in the list: ignored
                'script-src https://a.example.com; object-src *', // would add a directive: ignored
                "script-src https://b.example.com,https://c.example.com",
                'script-src',
                array( 'script-src x' ),
                '',
            ) )
        );
        $this->assertSame( array( 'script-src' => "'self'" ), expContentSecurityPolicy::withSources( array( 'script-src' => "'self'" ), 'script-src x' ) );
    }

    public function testNonceIsAddedWhereNoUnsafeInlineIs()
    {
        $directives = array( 'script-src' => "'self'", 'style-src' => "'self' 'unsafe-inline'", 'object-src' => "'none'", 'img-src' => "'self'" );
        $this->assertSame(
            array( 'script-src' => "'self' 'nonce-abc_D-1'", 'style-src' => "'self' 'unsafe-inline'", 'object-src' => "'none'", 'img-src' => "'self'" ),
            expContentSecurityPolicy::withNonce( $directives, 'abc_D-1', array( 'script-src', 'style-src', 'object-src', 'frame-src' ) )
        );
        $this->assertSame( $directives, expContentSecurityPolicy::withNonce( $directives, '', array( 'script-src' ) ) );
        $this->assertSame( $directives, expContentSecurityPolicy::withNonce( $directives, "x' 'unsafe-eval", array( 'script-src' ) ) );
        $this->assertSame( $directives, expContentSecurityPolicy::withNonce( $directives, 'abc', 'script-src' ) );
    }

    public function testNonceIsStablePerRequestAndNewAfterReset()
    {
        $nonce = expContentSecurityPolicy::nonce();
        $this->assertMatchesRegularExpression( '/^[A-Za-z0-9_-]{22}$/D', $nonce );
        $this->assertSame( $nonce, expContentSecurityPolicy::nonce() );
        expContentSecurityPolicy::reset();
        $this->assertNotSame( $nonce, expContentSecurityPolicy::nonce() );

        // a persistent worker that serves the next request without reset() gets a new one too
        $saved = $_SERVER['REQUEST_TIME_FLOAT'] ?? null;
        $before = expContentSecurityPolicy::nonce();
        $_SERVER['REQUEST_TIME_FLOAT'] = microtime( true ) + 1000;
        $after = expContentSecurityPolicy::nonce();
        if ( $saved === null )
            unset( $_SERVER['REQUEST_TIME_FLOAT'] );
        else
            $_SERVER['REQUEST_TIME_FLOAT'] = $saved;
        $this->assertNotSame( $before, $after );
    }

    public function testHeadersCarryTheNonceOfTheRequest()
    {
        $this->setting( 'ContentSecurityPolicy', 'Directives', array( 'script-src' => "'self'", 'style-src' => "'self'" ) );
        $this->setting( 'ContentSecurityPolicy', 'ReportURI', '' );
        $this->setting( 'ContentSecurityPolicy', 'Mode', 'enforce' );
        $this->setting( 'ContentSecurityPolicy', 'Nonce', 'enabled' );
        $this->setting( 'ContentSecurityPolicy', 'NonceDirectives', array( 'script-src' ) );
        $nonce = expContentSecurityPolicy::nonce();
        $this->assertSame( array( 'Content-Security-Policy' => "script-src 'self' 'nonce-$nonce'; style-src 'self'" ),
                           expContentSecurityPolicy::headers() );
        $this->assertSame( $nonce, expContentSecurityPolicy::templateNonce() );

        $operator = new expContentSecurityPolicyOperator();
        $this->assertSame( array( 'csp_nonce' ), $operator->operatorList() );
        $value = null;
        $operator->modify( null, 'csp_nonce', array(), '', '', $value, array(), false );
        $this->assertSame( $nonce, $value );

        $this->setting( 'ContentSecurityPolicy', 'Nonce', 'disabled' );
        $this->assertSame( array( 'Content-Security-Policy' => "script-src 'self'; style-src 'self'" ), expContentSecurityPolicy::headers() );
        $this->assertSame( '', expContentSecurityPolicy::templateNonce() );
        $this->setting( 'ContentSecurityPolicy', 'Nonce', 'enabled' );
        $this->setting( 'ContentSecurityPolicy', 'Mode', 'disabled' );
        $this->assertSame( '', expContentSecurityPolicy::templateNonce() );
    }

    public function testEnforceWithAStricterReportOnlyCompanion()
    {
        $this->setting( 'ContentSecurityPolicy', 'Directives', array( 'default-src' => "'self'", 'script-src' => "'self'",
                                                                      'style-src' => "'self' 'unsafe-inline'", 'frame-ancestors' => "'self'" ) );
        $this->setting( 'ContentSecurityPolicy', 'ReportOnlyDirectives', array( 'style-src' => "'self'" ) );
        $this->setting( 'ContentSecurityPolicy', 'AdditionalSources', array( 'style-src https://fonts.example.com' ) );
        $this->setting( 'ContentSecurityPolicy', 'ReportURI', '/csp/report' );
        $this->setting( 'DesignSettings', 'SiteDesign', 'admin4l' );

        $this->setting( 'ContentSecurityPolicy', 'Mode', 'enforce' );
        $this->assertSame( array(
            'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.example.com; frame-ancestors 'self'; report-uri /csp/report",
            'Content-Security-Policy-Report-Only' => "default-src 'self'; script-src 'self'; style-src 'self' https://fonts.example.com; report-uri /csp/report",
        ), expContentSecurityPolicy::headers() );

        // the rollback: report-only sends the policy that was enforced, and no companion
        $this->setting( 'ContentSecurityPolicy', 'Mode', 'report-only' );
        $this->assertSame( array(
            'Content-Security-Policy-Report-Only' => "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.example.com; report-uri /csp/report",
        ), expContentSecurityPolicy::headers() );

        // through the kernel: the enforced policy after SecurityHeaders[Content-Security-Policy], the companion of its own
        $this->setting( 'HTTPHeaderSettings', 'SecurityHeaders', array( 'Content-Security-Policy' => "frame-ancestors 'self'" ) );
        $this->setting( 'ContentSecurityPolicy', 'Mode', 'enforce' );
        $headers = ezpKernelWeb::securityHeaders();
        $this->assertStringStartsWith( "frame-ancestors 'self', default-src 'self'; script-src 'self';", $headers['Content-Security-Policy'] );
        $this->assertStringContainsString( "style-src 'self' https://fonts.example.com;", $headers['Content-Security-Policy-Report-Only'] );
    }

    public function testReportOnlyDesignsAreNeverEnforced()
    {
        $this->setting( 'ContentSecurityPolicy', 'Directives', array( 'script-src' => "'self'" ) );
        $this->setting( 'ContentSecurityPolicy', 'ReportURI', '' );
        $this->setting( 'ContentSecurityPolicy', 'Mode', 'enforce' );
        $this->setting( 'ContentSecurityPolicy', 'ReportOnlyDesigns', array( 'admin', 'Admin_Classic' ) );
        $this->setting( 'ContentSecurityPolicy', 'ReportOnlyDirectives', array( 'style-src' => "'self'" ) );

        foreach ( array( 'admin', 'admin_classic' ) as $design )
        {
            $this->setting( 'DesignSettings', 'SiteDesign', $design );
            $this->assertSame( array( 'Content-Security-Policy-Report-Only' => "script-src 'self'" ), expContentSecurityPolicy::headers(), $design );
            $this->assertTrue( expContentSecurityPolicy::allowsInlineScripts(), $design );
        }
        $this->setting( 'DesignSettings', 'SiteDesign', 'admin4l' );
        $this->assertArrayHasKey( 'Content-Security-Policy', expContentSecurityPolicy::headers() );
        $this->assertFalse( expContentSecurityPolicy::designListed( 'admin4', array( 'admin' ) ) );
        $this->assertFalse( expContentSecurityPolicy::designListed( '', array( '' ) ) );
        $this->assertFalse( expContentSecurityPolicy::designListed( 'admin', 'admin' ) );
    }

    public function testReportOnlyViewsAreNeverEnforced()
    {
        $this->setting( 'ContentSecurityPolicy', 'Directives', array( 'script-src' => "'self'" ) );
        $this->setting( 'ContentSecurityPolicy', 'ReportURI', '' );
        $this->setting( 'ContentSecurityPolicy', 'Mode', 'enforce' );
        $this->setting( 'DesignSettings', 'SiteDesign', 'admin4l' );
        $this->setting( 'ContentSecurityPolicy', 'ReportOnlyViews', array( 'DSE/AdminNeo' ) );

        $this->assertSame( array( 'Content-Security-Policy-Report-Only' => "script-src 'self'" ),
                           expContentSecurityPolicy::headers( null, 'dse/adminneo' ) );
        $this->assertSame( array( 'Content-Security-Policy' => "script-src 'self'" ),
                           expContentSecurityPolicy::headers( null, 'dse/dashboard' ) );
        $this->assertSame( array( 'Content-Security-Policy' => "script-src 'self'" ), expContentSecurityPolicy::headers( null, '' ) );

        // the current request's view, from the URI the kernel matched
        $saved = $GLOBALS['eZURIRequestInstance'] ?? null;
        $GLOBALS['eZURIRequestInstance'] = new eZURI( '/dse/adminneo/x' );
        $this->assertSame( 'dse/adminneo', expContentSecurityPolicy::currentView() );
        $this->assertSame( array( 'Content-Security-Policy-Report-Only' => "script-src 'self'" ), expContentSecurityPolicy::headers() );
        $GLOBALS['eZURIRequestInstance'] = new eZURI( '/content/view/full/2' );
        $this->assertSame( 'content/view', expContentSecurityPolicy::currentView() );
        $this->assertArrayHasKey( 'Content-Security-Policy', expContentSecurityPolicy::headers() );
        unset( $GLOBALS['eZURIRequestInstance'] );
        $this->assertSame( '', expContentSecurityPolicy::currentView() );
        if ( $saved !== null )
            $GLOBALS['eZURIRequestInstance'] = $saved;
    }

    public function testAllowsInlineScripts()
    {
        $this->setting( 'DesignSettings', 'SiteDesign', 'admin4l' );
        $this->setting( 'ContentSecurityPolicy', 'Directives', array( 'default-src' => "'self'", 'script-src' => "'self'" ) );
        $this->setting( 'ContentSecurityPolicy', 'Mode', 'report-only' );
        $this->assertTrue( expContentSecurityPolicy::allowsInlineScripts() );
        $this->setting( 'ContentSecurityPolicy', 'Mode', 'enforce' );
        $this->assertFalse( expContentSecurityPolicy::allowsInlineScripts() );
        $this->setting( 'ContentSecurityPolicy', 'AdditionalSources', array( "script-src 'unsafe-inline'" ) );
        $this->assertTrue( expContentSecurityPolicy::allowsInlineScripts() );
        $this->setting( 'ContentSecurityPolicy', 'AdditionalSources', array() );
        $this->setting( 'ContentSecurityPolicy', 'Directives', array( 'default-src' => "'self' 'unsafe-inline'" ) );
        $this->assertTrue( expContentSecurityPolicy::allowsInlineScripts(), 'default-src without script-src' );
        $this->setting( 'ContentSecurityPolicy', 'Directives', array( 'default-src' => "'self'" ) );
        $this->assertFalse( expContentSecurityPolicy::allowsInlineScripts() );
        $this->setting( 'ContentSecurityPolicy', 'Directives', array( 'img-src' => "'self'" ) );
        $this->assertTrue( expContentSecurityPolicy::allowsInlineScripts(), 'no script restriction at all' );
    }

    public function testReportPartsAndTheReportingEndpoint()
    {
        $this->assertSame( array( 'report-uri' => '', 'report-to' => '', 'endpoints' => '' ), expContentSecurityPolicy::reportParts( '' ) );
        $this->assertSame( array( 'report-uri' => '/csp/report', 'report-to' => '', 'endpoints' => '' ),
                           expContentSecurityPolicy::reportParts( '/csp/report' ) );
        $this->assertSame( array( 'report-uri' => 'https://r.example.com/x', 'report-to' => 'csp-endpoint',
                                  'endpoints' => 'csp-endpoint="https://r.example.com/x"' ),
                           expContentSecurityPolicy::reportParts( ' https://r.example.com/x ', true, 'https://own.example.com/csp/report' ) );
        $this->assertSame( array( 'report-uri' => 'https://own.example.com/admin/csp/report', 'report-to' => 'csp-endpoint',
                                  'endpoints' => 'csp-endpoint="https://own.example.com/admin/csp/report"' ),
                           expContentSecurityPolicy::reportParts( '', true, 'https://own.example.com/admin/csp/report' ) );
        $this->assertSame( array( 'report-uri' => '', 'report-to' => '', 'endpoints' => '' ),
                           expContentSecurityPolicy::reportParts( '', false, 'https://own.example.com/csp/report' ) );
        // nothing that would end the header value or the quoted endpoint
        foreach ( array( 'https://a.example.com/"x', 'https://a.example.com/\\x' ) as $uri )
            $this->assertSame( '', expContentSecurityPolicy::reportParts( $uri )['endpoints'], $uri );
        foreach ( array( 'https://a.example.com/x;y', "https://a.example.com/x\ny", 'https://a.example.com/x,y' ) as $uri )
            $this->assertSame( array( 'report-uri' => '', 'report-to' => '', 'endpoints' => '' ), expContentSecurityPolicy::reportParts( $uri ), $uri );

        $this->assertSame( "script-src 'self'; report-uri https://r.example.com/x; report-to csp-endpoint",
                           expContentSecurityPolicy::policy( array( 'script-src' => "'self'", 'report-to' => 'evil' ), 'https://r.example.com/x', 'csp-endpoint' ) );
        $this->assertSame( "script-src 'self'", expContentSecurityPolicy::policy( array( 'script-src' => "'self'" ), '', 'a b' ) );

        $this->setting( 'ContentSecurityPolicy', 'Directives', array( 'script-src' => "'self'" ) );
        $this->setting( 'ContentSecurityPolicy', 'Mode', 'enforce' );
        $this->setting( 'DesignSettings', 'SiteDesign', 'admin4l' );
        $this->setting( 'ContentSecurityPolicy', 'ReportURI', 'https://r.example.com/x' );
        $this->assertSame( array( 'Content-Security-Policy' => "script-src 'self'; report-uri https://r.example.com/x; report-to csp-endpoint",
                                  'Reporting-Endpoints' => 'csp-endpoint="https://r.example.com/x"' ),
                           expContentSecurityPolicy::headers() );
    }
}
