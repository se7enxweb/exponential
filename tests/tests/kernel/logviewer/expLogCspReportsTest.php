<?php
/**
 * The reports of the Content Security Policy in the log viewer (doc/eng-us/features/6.0/content-security-policy.md,
 * "Reports"): var/log/csp.log as a kernel log, without a database.
 *
 *  LV-220  the key csp resolves to a kernel log with its label and level, its archives too
 *  LV-221  settings/logviewer.ini registers it (KernelLogs[], LogLabel[], FileLevels[], GroupBy[], rotation), equal to
 *          the class defaults
 *  LV-222  the rotation settings reach the manager (10 MB, 5 kept, compressed, 30 days)
 *  LV-223  the parser reads a csp line: time, masked IP, directive, blocked, disposition, page, siteaccess, source
 *  LV-224  the digest groups csp.log by directive and blocked origin, and its literal finds every line of a group
 *  LV-225  the lines expContentSecurityPolicyReport writes are read back by the parser and the digest
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expLogViewerTestFixtures.php';

class expLogCspReportsTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $root;

    protected function setUp(): void
    {
        $this->root = expLogViewerTestFixtures::root( 'lc' );
    }

    protected function tearDown(): void
    {
        expLogViewerTestFixtures::remove( $this->root );
    }

    private static function csp( $t, $directive, $blocked, $document = 'https://example.test/admin/content/view/full/2', $ip = '203.0.x.x' )
    {
        return '[ ' . date( 'M d Y H:i:s', $t ) . ' ] [' . $ip . '] csp/' . $directive . ' blocked=' . $blocked
               . ' disposition=enforce document=' . $document . ' siteaccess=admin line=3 column=9 source=- status=200'
               . ' sample="" ua=3f2a9c1b "Mozilla/5.0 (X11; Linux x86_64)"' . "\n";
    }

    /** The values of one group of settings/logviewer.ini: plain values, lists and keyed values */
    private static function iniGroup( $group )
    {
        $out = array();
        $in = false;
        foreach ( file( dirname( __DIR__, 4 ) . '/settings/logviewer.ini', FILE_IGNORE_NEW_LINES ) as $line )
        {
            if ( preg_match( '/^\[(.+)\]$/', $line, $m ) )
            {
                $in = $m[1] === $group;
                continue;
            }
            if ( !$in || $line === '' || $line[0] === '#' )
                continue;
            if ( preg_match( '/^(\w+)\[\]$/', $line, $m ) )
                $out[$m[1]] = array();
            else if ( preg_match( '/^(\w+)\[\]=(.*)$/', $line, $m ) )
                $out[$m[1]][] = $m[2];
            else if ( preg_match( '/^(\w+)\[([^\]]+)\]=(.*)$/', $line, $m ) )
                $out[$m[1]][$m[2]] = $m[3];
            else if ( preg_match( '/^(\w+)=(.*)$/', $line, $m ) )
                $out[$m[1]] = $m[2];
        }
        return $out;
    }

    private function iniConfig()
    {
        $values = array();
        foreach ( array( 'LogViewerSettings', 'DigestSettings', 'RotationSettings' ) as $group )
            $values[$group] = self::iniGroup( $group );
        $values['LogViewerSettings']['Directories'] = array( 'var/log', 'var/*/log' );
        return expLogViewerTestFixtures::config( $this->root, $values );
    }

    /** LV-220 */
    public function testTheKeyResolvesAsAKernelLog()
    {
        file_put_contents( $this->root . '/var/log/csp.log', self::csp( time(), 'script-src-elem', 'inline' ) );
        file_put_contents( $this->root . '/var/log/csp.log.1', "old\n" );
        foreach ( array( expLogViewerTestFixtures::config( $this->root ), $this->iniConfig() ) as $config )
        {
            $locator = new expLogFileLocator( $config, 'var/log' );
            $csp = $locator->resolveKey( 'csp' );
            $this->assertNotNull( $csp );
            $this->assertSame( 'var/log/csp.log', $csp['id'] );
            $this->assertSame( expLogFileLocator::GROUP_KERNEL, $csp['group'] );
            $this->assertSame( 'Content Security Policy reports', $csp['label'] );
            $this->assertFalse( $csp['configured'], 'a log of the log directory: it can be rotated' );
            $this->assertSame( 'warning', $csp['level'] );
            $archive = $locator->resolveKey( 'csp', 1 );
            $this->assertSame( 'var/log/csp.log.1', $archive['id'] );
            $this->assertSame( 'warning', $archive['level'] );
        }
    }

    /** LV-221 */
    public function testTheSettingsRegisterTheLog()
    {
        $lv = self::iniGroup( 'LogViewerSettings' );
        $this->assertContains( 'csp.log', $lv['KernelLogs'] );
        $this->assertSame( 'Content Security Policy reports', $lv['LogLabel']['csp'] );
        $this->assertContains( 'csp.log*;warning', $lv['FileLevels'] );
        $digest = self::iniGroup( 'DigestSettings' );
        $this->assertSame( 'directive-blocked', $digest['GroupBy']['csp'] );
        $d = expLogViewerConfig::$defaults;
        $this->assertSame( $lv['KernelLogs'], $d['LogViewerSettings']['KernelLogs'] );
        $this->assertSame( $lv['FileLevels'], $d['LogViewerSettings']['FileLevels'] );
        $this->assertSame( $lv['LogLabel'], $d['LogViewerSettings']['LogLabel'] );
        $this->assertSame( $digest['GroupBy'], $d['DigestSettings']['GroupBy'] );
        $this->assertContains( 'directive-blocked', expLogDigest::GROUPINGS );
        // the label is translated in every language that names the other request logs
        foreach ( glob( dirname( __DIR__, 4 ) . '/share/translations/*/translation.ts' ) as $ts )
        {
            $xml = file_get_contents( $ts );
            if ( strpos( $xml, '<source>Security (request classifier)</source>' ) !== false )
                $this->assertStringContainsString( '<source>Content Security Policy reports</source>', $xml, $ts );
        }
    }

    /** LV-222 */
    public function testTheRotationSettingsReachTheManager()
    {
        file_put_contents( $this->root . '/var/log/csp.log', "x\n" );
        $rotation = self::iniGroup( 'RotationSettings' );
        $config = $this->iniConfig();
        $manager = new expLogManager( $config, new expLogFileLocator( $config, 'var/log' ),
                                      array( 'keep' => 3, 'max_size' => 2097152, 'compress' => false, 'max_age_days' => 0, 'overrides' => $rotation ) );
        $this->assertArrayHasKey( 'csp', $manager->logs() );
        $s = $manager->effectiveSettings( 'csp' );
        $this->assertSame( array( 10485760, 5, true, 30 ),
                           array( $s['max_size']['value'], $s['keep']['value'], $s['compress']['value'], $s['max_age_days']['value'] ) );
    }

    /** LV-223 */
    public function testTheParserReadsACspLine()
    {
        $t = mktime( 9, 0, 0, 10, 17, 2026 );
        $h = expLogParser::header( self::csp( $t, 'img-src', 'https://maps.example.com/a.png', 'https://example.test/admin/setup/info' ) );
        $this->assertSame( 'csp', $h['format'] );
        $this->assertSame( $t, $h['time'] );
        $this->assertSame( '203.0.x.x', $h['ip'] );
        $this->assertSame( array( 'img-src', 'https://maps.example.com/a.png', 'enforce', 'https://example.test/admin/setup/info', 'admin' ),
                           array( $h['directive'], $h['blocked'], $h['disposition'], $h['url'], $h['siteaccess'] ) );
        $this->assertSame( 'csp/img-src', expLogParser::source( $h ) );
        // a document of "-" is no request
        $h = expLogParser::header( self::csp( $t, 'img-src', 'data', '-' ) );
        $this->assertSame( '', $h['url'] );
        // a security line and an ordinary message are left as they were
        $this->assertSame( 'security', expLogParser::header( '[ Oct 17 2026 09:00:00 ] [203.0.x.x] probe/env confidence=high 404 GET /.env siteaccess=site' )['format'] );
        $this->assertSame( 'debug', expLogParser::header( '[ Oct 17 2026 09:00:00 ] [203.0.x.x] csp/not a report line' )['format'] );
        $entries = expLogParser::parse( self::csp( $t, 'script-src-elem', 'inline' ), 0, 'warning' );
        $this->assertCount( 1, $entries );
        $this->assertSame( 'warning', $entries[0]['level'] );
        $this->assertSame( 'csp/script-src-elem', $entries[0]['source'] );
    }

    /** LV-224 */
    public function testTheDigestGroupsByDirectiveAndBlockedOrigin()
    {
        $now = time();
        file_put_contents( $this->root . '/var/log/csp.log',
            self::csp( $now - 50, 'img-src', 'https://maps.example.com/tile/1/2/3.png' )
            . self::csp( $now - 40, 'script-src-elem', 'inline' )
            . self::csp( $now - 30, 'img-src', 'https://maps.example.com/tile/4/5/6.png?key=abc' )
            . self::csp( $now - 20, 'script-src-elem', 'inline', 'https://example.test/admin/setup/info' )
            . self::csp( $now - 15, 'script-src-elem', 'inline' )
            . self::csp( $now - 10, 'style-src-attr', 'inline' )
            . self::csp( $now - 7200, 'font-src', 'https://fonts.example.com/a.woff2' ) );
        $config = $this->iniConfig();
        $digest = new expLogDigest( $config, new expLogRedactor( true, true, true, array() ) );
        $out = $digest->digestFile( ( new expLogFileLocator( $config, 'var/log' ) )->resolveKey( 'csp' ), $now - 3600 );
        $this->assertSame( 'directive-blocked', $out['grouping'] );
        $this->assertSame( 6, $out['total'] );
        $this->assertSame( array( 'csp/script-src-elem blocked=inline', 'csp/img-src blocked=https://maps.example.com', 'csp/style-src-attr blocked=inline' ),
                           array_column( $out['groups'], 'text' ) );
        $this->assertSame( array( 3, 2, 1 ), array_column( $out['groups'], 'count' ) );
        $this->assertSame( 'Content Security Policy reports', $out['label'] );
        // the literal is a run of every line of its group, so the group's link finds them all
        $lines = file( $this->root . '/var/log/csp.log' );
        foreach ( $out['groups'] as $group )
        {
            $this->assertNotSame( '', $group['literal'] );
            $found = 0;
            foreach ( $lines as $line )
                $found += strpos( $line, $group['literal'] ) !== false ? 1 : 0;
            $this->assertGreaterThanOrEqual( $group['count'], $found, $group['text'] );
        }

        $this->assertSame( 'csp/img-src blocked=data', expLogDigest::groupText( 'directive-blocked', 'csp/img-src blocked=data disposition=report document=-' ) );
        $this->assertSame( 'probe/env', expLogDigest::groupText( 'directive-blocked', 'probe/env' ), 'another line: its message' );
    }

    /** LV-225 */
    public function testTheWritersLinesAreReadBack()
    {
        $t = mktime( 9, 0, 0, 10, 17, 2026 );
        $violation = expContentSecurityPolicyReport::parse( json_encode( array( 'csp-report' => array(
            'document-uri' => 'https://example.test/admin/user/edit?token=secret', 'effective-directive' => 'connect-src',
            'blocked-uri' => 'https://api.example.com/v1/x?mail=someone@example.org', 'disposition' => 'report' ) ) ) )[0];
        $line = expRequestClassifier::prefix( '2001:db8:1:2::9', true, $t )
              . expContentSecurityPolicyReport::line( $violation, 'admin', 'Mozilla/5.0' );
        $h = expLogParser::header( $line );
        $this->assertSame( 'csp', $h['format'], $line );
        $this->assertSame( array( 'connect-src', 'report', 'admin' ), array( $h['directive'], $h['disposition'], $h['siteaccess'] ) );
        $this->assertStringNotContainsString( 'secret', $line );
        $this->assertStringNotContainsString( 'someone@example.org', $line );
        $this->assertStringNotContainsString( '2001:db8:1:2::9', $line );
        $this->assertSame( 'csp/connect-src blocked=https://api.example.com', expLogDigest::groupText( 'directive-blocked', $h['message'] ) );
    }
}
