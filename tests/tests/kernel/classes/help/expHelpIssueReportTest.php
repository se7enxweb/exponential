<?php
/**
 * "Report an error" (doc/features/6.0/help-system.md, "Reporting an error"): the trackers, the prefilled issue and the
 * diagnostics a user copies into a public GitHub issue. The diagnostics must never carry a secret: the test plants
 * marker values in the database, mail and session settings of the running INI and checks none of them, nor any
 * server path outside the installation, appears anywhere the report produces. No database.
 *
 *  HI-01  trackers(): keys, https://github.com/se7enxweb/... issue forms; the software tracker is se7enxweb/exponential
 *  HI-02  environment()/environmentText(): no planted secret, no DB host/user/name/password, no absolute server path,
 *         no e-mail address, no session id; but the useful facts (Exponential version, PHP version)
 *  HI-03  issueUrl(): the tracker's new-issue form, title and body encoded, bounded length, no secret
 *  HI-04  securityContact(): a private way (e-mail or policy URL), never a public issue form
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpIssueReportTest extends PHPUnit\Framework\TestCase
{
    const MARK = 'W612SECRETMARK';

    /** @var array [ini file, group, name, previous value or null] */
    private $planted = array();

    protected function setUp(): void
    {
        $plant = array(
            array( 'site.ini', 'DatabaseSettings', 'Server', 'db-' . strtolower( self::MARK ) . '.internal' ),
            array( 'site.ini', 'DatabaseSettings', 'User', 'user' . self::MARK ),
            array( 'site.ini', 'DatabaseSettings', 'Password', 'pw' . self::MARK ),
            array( 'site.ini', 'DatabaseSettings', 'Database', 'db' . self::MARK ),
            array( 'site.ini', 'DatabaseSettings', 'Socket', '/var/run/' . self::MARK . '.sock' ),
            array( 'site.ini', 'MailSettings', 'TransportServer', 'smtp-' . strtolower( self::MARK ) . '.internal' ),
            array( 'site.ini', 'MailSettings', 'TransportUser', 'mailuser' . self::MARK ),
            array( 'site.ini', 'MailSettings', 'TransportPassword', 'mailpw' . self::MARK ),
            array( 'site.ini', 'MailSettings', 'AdminEmail', 'admin-' . strtolower( self::MARK ) . '@example.com' ),
            array( 'site.ini', 'MailSettings', 'EmailSender', 'sender-' . strtolower( self::MARK ) . '@example.com' ),
            array( 'site.ini', 'Session', 'SessionNamePrefix', 'sess' . self::MARK ),
            array( 'site.ini', 'SiteSettings', 'SiteURL', 'internal-' . strtolower( self::MARK ) . '.example.com' ),
        );
        foreach ( $plant as $p )
        {
            $ini = eZINI::instance( $p[0] );
            $previous = $ini->hasVariable( $p[1], $p[2] ) ? $ini->variable( $p[1], $p[2] ) : null;
            $this->planted[] = array( $p[0], $p[1], $p[2], $previous );
            $ini->setVariable( $p[1], $p[2], $p[3] );
        }
        $_SERVER['W612_TEST_TOKEN'] = 'tok' . self::MARK;
    }

    protected function tearDown(): void
    {
        foreach ( array_reverse( $this->planted ) as $p )
        {
            $ini = eZINI::instance( $p[0] );
            if ( $p[3] === null )
                $ini->removeSetting( $p[1], $p[2] );
            else
                $ini->setVariable( $p[1], $p[2], $p[3] );
        }
        $this->planted = array();
        unset( $_SERVER['W612_TEST_TOKEN'] );
    }

    private function assertNoSecret( $text, $what )
    {
        $text = (string)$text;
        $decoded = rawurldecode( urldecode( $text ) );
        foreach ( array( $text, $decoded ) as $t )
        {
            $this->assertStringNotContainsStringIgnoringCase( self::MARK, $t, $what . ' carries a planted secret' );
            $this->assertDoesNotMatchRegularExpression( '#/(var/www|web/vh|home|root|srv|opt/plesk)/#', $t, $what . ' carries a server path' );
            $this->assertDoesNotMatchRegularExpression( '/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[a-z]{2,}/i', $t, $what . ' carries an e-mail address' );
            $this->assertDoesNotMatchRegularExpression( '/(password|passwd|secret|token|api[_-]?key)\s*[:=]\s*(?!\[redacted\])\S/i', $t, $what . ' carries a credential' );
            $this->assertDoesNotMatchRegularExpression( '/BEGIN [A-Z ]*PRIVATE KEY/', $t, $what );
            if ( session_id() !== '' )
                $this->assertStringNotContainsString( session_id(), $t, $what . ' carries the session id' );
        }
        $root = expHelp::installRoot();
        if ( strlen( $root ) > 1 )
            $this->assertStringNotContainsString( $root, $text, $what . ' carries the install root path' );
    }

    /** HI-01 */
    public function testTrackers()
    {
        $trackers = ( new expHelpIssueReport() )->trackers();
        $this->assertNotEmpty( $trackers );
        $this->assertArrayHasKey( 'software', $trackers );
        foreach ( $trackers as $key => $t )
        {
            $this->assertMatchesRegularExpression( '/^[a-z0-9_-]+$/', (string)$key );
            foreach ( array( 'key', 'label', 'description', 'url', 'new_issue_url' ) as $field )
                $this->assertArrayHasKey( $field, $t, $key );
            $this->assertMatchesRegularExpression( '#^https://github\.com/se7enxweb/[A-Za-z0-9_.-]+#', $t['url'], $key );
            $this->assertMatchesRegularExpression( '#^https://github\.com/se7enxweb/[A-Za-z0-9_.-]+/(issues/new|discussions)#', $t['new_issue_url'], $key );
        }
        $this->assertMatchesRegularExpression( '#^https://github\.com/se7enxweb/exponential(/|$)#', $trackers['software']['url'] );
    }

    /** HI-02 */
    public function testEnvironmentHasNoSecrets()
    {
        $report = new expHelpIssueReport();
        $env = $report->environment();
        $this->assertIsArray( $env );
        $this->assertNotEmpty( $env );
        $this->assertNoSecret( json_encode( $env ), 'environment()' );
        $text = $report->environmentText();
        $this->assertNotSame( '', trim( $text ) );
        $this->assertNoSecret( $text, 'environmentText()' );
        $this->assertStringContainsString( PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION, $text, 'the PHP version is useful' );
        $this->assertMatchesRegularExpression( '/Exponential/i', $text );
        $this->assertDoesNotMatchRegularExpression( '/eZ ?Publish/i', $text, 'the product is called Exponential' );
    }

    /** HI-03 */
    public function testIssueUrl()
    {
        $report = new expHelpIssueReport();
        $title = 'Saving fails & <script>alert(1)</script> #1';
        $body = "Steps:\n1. open\n\n" . $report->environmentText();
        $url = $report->issueUrl( 'software', $title, $body );
        $this->assertMatchesRegularExpression( '#^https://github\.com/se7enxweb/exponential/issues/new\?#', $url );
        $this->assertDoesNotMatchRegularExpression( '/[\s<>"]/', $url, 'title and body are encoded' );
        parse_str( (string)parse_url( $url, PHP_URL_QUERY ), $q );
        $this->assertSame( $title, $q['title'] ?? null );
        $this->assertLessThanOrEqual( 8192, strlen( $url ), 'GitHub refuses longer addresses' );
        $this->assertNoSecret( $url, 'issueUrl()' );
        // an unknown tracker never becomes another host
        $other = $report->issueUrl( 'https://evil.example/', 'x', 'y' );
        $this->assertTrue( $other === '' || strpos( $other, 'https://github.com/se7enxweb/' ) === 0, $other );
        // a body far too long is cut, not refused
        $long = $report->issueUrl( 'software', 'x', str_repeat( 'long line ', 5000 ) );
        $this->assertLessThanOrEqual( 8192, strlen( $long ) );
    }

    /** HI-05 planted values given to the report directly: scrubbed, and unknown keys dropped */
    public function testPlantedValuesScrubbed()
    {
        if ( !defined( 'expHelpIssueReport::ENVIRONMENT_KEYS' ) )
            $this->markTestSkipped( 'no ENVIRONMENT_KEYS' );
        $root = expHelp::installRoot();
        $values = array();
        foreach ( expHelpIssueReport::ENVIRONMENT_KEYS as $key )
            $values[$key] = $key . ' ok';
        $values['database'] = 'mysql://user' . self::MARK . ':pw' . self::MARK . '@db.internal:3306/db 10.11';
        $values['design'] = $root . '/design/admin4 and /var/www/vhosts/other/' . self::MARK;
        $values['siteaccess'] = 'admin password=pw' . self::MARK . ' token: ' . str_repeat( 'a1', 20 );
        $values['language'] = 'eng-US admin-' . strtolower( self::MARK ) . '@example.com 10.1.2.3 fe80::1';
        $values['os'] = 'Linux /home/' . self::MARK . '/x ~/' . self::MARK . ' C:\\Users\\' . self::MARK;
        $values['secret_extra'] = 'never' . self::MARK;
        $values['server'] = 'db-host-' . self::MARK;
        $report = new expHelpIssueReport( $values, array( 'extension_infos' => array() ) );
        $this->assertNoSecret( json_encode( $report->environment() ), 'environment() of planted values' );
        $this->assertNoSecret( $report->environmentText(), 'environmentText() of planted values' );
        $this->assertNoSecret( $report->issueUrl( 'software', 'x', $report->environmentText() ), 'issueUrl() of planted values' );
        $this->assertStringNotContainsString( 'never', $report->environmentText(), 'a key outside ENVIRONMENT_KEYS is ignored' );
        $this->assertStringNotContainsString( 'db.internal', $report->environmentText(), 'no database host name' );
        $this->assertStringNotContainsString( 'fe80::1', $report->environmentText(), 'IPv6 addresses scrubbed' );
        $this->assertStringNotContainsString( '10.1.2.3', $report->environmentText(), 'IPv4 addresses scrubbed' );
        $this->assertStringContainsString( 'design/admin4', $report->environmentText(), 'paths inside the installation stay, relative' );
        $this->assertStringContainsString( '10.11', $report->environmentText(), 'the database version stays' );
        // the scrubber on its own
        $this->assertNoSecret( expHelpIssueReport::scrub( 'https://u:p' . self::MARK . '@h.example/x?api_key=' . self::MARK ), 'scrub()' );
        $this->assertStringContainsString( '/content/edit/12', expHelpIssueReport::scrub( 'at /content/edit/12' ), 'module paths stay' );
        // trackers given by settings: only GitHub issue forms
        $r = new expHelpIssueReport( $values, array( 'IssueTrackers/TrackerList' => array( 'software', 'evil' ),
                                                     'Tracker_software/URL' => 'https://github.com/se7enxweb/exponential/issues',
                                                     'Tracker_evil/URL' => 'https://evil.example/issues', 'extension_infos' => array() ) );
        $this->assertArrayNotHasKey( 'evil', $r->trackers() );
    }

    /** HI-04 */
    public function testSecurityContact()
    {
        $c = ( new expHelpIssueReport() )->securityContact();
        $this->assertArrayHasKey( 'email', $c );
        $this->assertArrayHasKey( 'policy_url', $c );
        $this->assertTrue( $c['email'] !== '' || $c['policy_url'] !== '', 'there is a private way to report a vulnerability' );
        if ( $c['email'] !== '' )
            $this->assertMatchesRegularExpression( '/^[^@\s]+@[^@\s]+\.[a-z]{2,}$/i', $c['email'] );
        if ( $c['policy_url'] !== '' )
        {
            $this->assertMatchesRegularExpression( '#^https://#', $c['policy_url'] );
            $this->assertStringNotContainsString( '/issues/new', $c['policy_url'], 'a vulnerability is never reported in a public issue' );
        }
    }
}
