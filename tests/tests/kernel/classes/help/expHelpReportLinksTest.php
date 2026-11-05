<?php
/**
 * Where "Report an error" leads (expHelp::reportLinks(), fetch( 'exphelp', 'report_links' ); doc/eng-us/features/6.0/
 * help-system.md, "Reporting an error"): the view exphelp/report while the siteaccess allows it, else the help page of
 * exphelp.ini [ContextHelp] Map[exphelp/report] and its security section, so a site that switches the view off never
 * links an address that answers 404. No database.
 *
 *  HR-01  the view allowed: /exphelp/report and /exphelp/report#security
 *  HR-02  the view off: the help page of Map[exphelp/report] and its heading ReportSecurityAnchor
 *  HR-03  no page mapped (or an unsafe one): the help dashboard for both links
 *  HR-04  site.ini [SiteAccessRules] decides when no answer is passed: a rule switching exphelp/report off
 *  HR-05  the anchor exists: "Security problems" on the English page, the heading in the same position on the German one
 *  HR-06  the dashboard view, the fetch function and the help templates use it; none links exphelp/report itself
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpReportLinksTest extends PHPUnit\Framework\TestCase
{
    /** @var array [ini file, group, name, previous value or null] */
    private $changed = array();

    protected function tearDown(): void
    {
        foreach ( array_reverse( $this->changed ) as $c )
        {
            $ini = eZINI::instance( $c[0] );
            if ( $c[3] === null )
                $ini->removeSetting( $c[1], $c[2] );
            else
                $ini->setVariable( $c[1], $c[2], $c[3] );
        }
        $this->changed = array();
    }

    private function set( $file, $group, $name, $value )
    {
        $ini = eZINI::instance( $file );
        $this->changed[] = array( $file, $group, $name, $ini->hasVariable( $group, $name ) ? $ini->variable( $group, $name ) : null );
        $ini->setVariable( $group, $name, $value );
    }

    private function setMap( array $entries )
    {
        $map = (array)expHelp::setting( 'ContextHelp', 'Map', array() );
        foreach ( $entries as $key => $value )
        {
            if ( $value === null )
                unset( $map[$key] );
            else
                $map[$key] = $value;
        }
        $this->set( 'exphelp.ini', 'ContextHelp', 'Map', $map );
    }

    /** HR-01 */
    public function testTheViewWhereItIsAllowed()
    {
        $this->assertSame( array( 'url' => '/exphelp/report', 'security' => '/exphelp/report#security', 'view' => true ),
            expHelp::reportLinks( true ) );
    }

    /** HR-02 */
    public function testTheMappedHelpPageWhereTheViewIsOff()
    {
        $this->setMap( array( 'exphelp/report' => 'help/when-something-goes-wrong.md' ) );
        $this->set( 'exphelp.ini', 'ContextHelp', 'ReportSecurityAnchor', 'security-problems' );
        $links = expHelp::reportLinks( false );
        $this->assertFalse( $links['view'] );
        $this->assertSame( '/exphelp/viewer/help/when-something-goes-wrong.md', $links['url'] );
        $this->assertStringStartsWith( '/exphelp/viewer/help/when-something-goes-wrong.md#', $links['security'] );
        $this->assertContains( substr( $links['security'], strpos( $links['security'], '#' ) + 1 ),
            array( 'security-problems', 'sicherheitsprobleme' ), 'the security heading in the language the reader sees' );

        // a mapped anchor is kept for the page link; an unsafe ReportSecurityAnchor falls back to the default
        $this->setMap( array( 'exphelp/report' => 'help/when-something-goes-wrong.md#filing-a-bug-in-exponential' ) );
        $this->set( 'exphelp.ini', 'ContextHelp', 'ReportSecurityAnchor', '"><script>' );
        $links = expHelp::reportLinks( false );
        $this->assertSame( '/exphelp/viewer/help/when-something-goes-wrong.md#filing-a-bug-in-exponential', $links['url'] );
        $this->assertStringNotContainsString( 'script', $links['security'] );
        $this->assertStringNotContainsString( '"', $links['security'] );
    }

    /** HR-03 */
    public function testTheDashboardWhenNoPageIsMapped()
    {
        $this->setMap( array( 'exphelp/report' => null ) );
        $this->assertSame( array( 'url' => '/exphelp/dashboard', 'security' => '/exphelp/dashboard', 'view' => false ),
            expHelp::reportLinks( false ) );
        $this->setMap( array( 'exphelp/report' => 'https://example.com/x.md' ) );
        $this->assertSame( '/exphelp/dashboard', expHelp::reportLinks( false )['url'], 'an address outside the help is never linked' );
        $this->setMap( array( 'exphelp/report' => '../../etc/passwd.md' ) );
        $this->assertSame( '/exphelp/dashboard', expHelp::reportLinks( false )['url'] );
    }

    /** HR-04 */
    public function testTheSiteAccessRulesDecide()
    {
        $this->setMap( array( 'exphelp/report' => 'help/when-something-goes-wrong.md' ) );
        $this->set( 'site.ini', 'SiteAccessRules', 'Rules', array( 'access;disable', 'module;exphelp/report' ) );
        $off = expHelp::reportLinks();
        $this->assertFalse( $off['view'] );
        $this->assertSame( '/exphelp/viewer/help/when-something-goes-wrong.md', $off['url'] );

        $this->set( 'site.ini', 'SiteAccessRules', 'Rules', array( 'access;disable', 'module;exphelp/report', 'access;enable', 'module;exphelp/report' ) );
        $this->assertSame( '/exphelp/report', expHelp::reportLinks()['url'], 'switched on again' );

        $this->set( 'site.ini', 'SiteAccessRules', 'Rules', array( 'access;disable', 'module;exphelp/edit' ) );
        $this->assertSame( '/exphelp/report', expHelp::reportLinks()['url'], 'another view switched off' );

        $this->set( 'site.ini', 'SiteAccessRules', 'Rules', array( 'access;disable', 'module;exphelp' ) );
        $this->assertFalse( expHelp::reportLinks()['view'], 'the whole module switched off' );
    }

    /** HR-05 */
    public function testTheSecurityHeadingExistsInEveryLanguage()
    {
        $root = dirname( __DIR__, 5 );
        $headings = function ( $file ) {
            $text = (string)file_get_contents( $file );
            preg_match_all( '/^##+ +(.+?) *$/m', preg_replace( '/^```.*?^```/ms', '', $text ), $m );
            return array_map( function ( $h ) { return expHelpMarkdownRenderer::slug( $h ); }, $m[1] );
        };
        $english = $headings( $root . '/doc/eng-us/help/when-something-goes-wrong.md' );
        $german = $headings( $root . '/doc/ger-de/hilfe/wenn-etwas-schiefgeht.md' );
        $position = array_search( expHelp::REPORT_SECURITY_ANCHOR, $english, true );
        $this->assertNotFalse( $position, 'the English page has the heading #' . expHelp::REPORT_SECURITY_ANCHOR );
        $this->assertCount( count( $english ), $german, 'the translation keeps the headings of its original' );
        $this->assertSame( 'sicherheitsprobleme', $german[$position] );
        $this->assertSame( 'help/when-something-goes-wrong.md',
            (string)eZINI::instance( 'exphelp.ini' )->variable( 'ContextHelp', 'Map' )['exphelp/report'] );
    }

    /** HR-06 */
    public function testTheViewsAndTemplatesUseIt()
    {
        $root = dirname( __DIR__, 5 );
        $dashboard = (string)file_get_contents( $root . '/kernel/private/classes/views/exphelp/dashboard.php' );
        $this->assertStringContainsString( "expHelp::reportLinks()", $dashboard );
        $this->assertStringContainsString( "'help_report_security_url'", $dashboard );
        $this->assertSame( array( 'result' => expHelp::reportLinks() ), expHelpFunctionCollection::fetchReportLinks() );
        include $root . '/kernel/exphelp/function_definition.php';
        $this->assertSame( 'fetchReportLinks', $FunctionList['report_links']['call_method']['method'] );

        foreach ( array( 'admin', 'admin4', 'standard' ) as $design )
        {
            foreach ( array( 'dashboard', 'viewer', 'topic', 'search' ) as $view )
            {
                $file = $root . "/design/$design/templates/exphelp/$view.tpl";
                if ( !is_file( $file ) )
                    continue;
                $tpl = (string)file_get_contents( $file );
                $this->assertDoesNotMatchRegularExpression( "#href=\\{'/?exphelp/report'#", $tpl, "$design $view links the view itself" );
                $this->assertStringNotContainsString( "concat( \$help_report, '#security' )|ezurl", $tpl, "$design $view" );
                if ( $view === 'dashboard' )
                    $this->assertStringContainsString( 'href={$help_report_security|ezurl}', $tpl, $design );
                else
                    $this->assertStringContainsString( "fetch( 'exphelp', 'report_links' ).url|ezurl", $tpl, "$design $view" );
            }
        }
    }
}
