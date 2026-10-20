<?php
/**
 * The request logs in the log viewer (doc/en/us/features/6.0/request-logs.md): var/log/404.log and var/log/security.log as
 * first-class kernel logs, without a database.
 *
 *  LV-200  the keys 404 and security resolve, are kernel logs with their labels and levels; their archives too
 *  LV-201  settings/logviewer.ini registers them (KernelLogs[], LogLabel[], FileLevels[], digest and rotation settings)
 *  LV-202  expLogViewerConfig::hash() takes a key of digits (LogLabel[404]) but not the entries of a list
 *  LV-203  the rotation defaults reach expLogManager and expLogManagerFallback (10 MB, 5 kept, compressed, 30/90 days)
 *  LV-204  the parser reads both line formats: time, masked IP, request, siteaccess, status, source; other lines unchanged
 *  LV-205  the digest groups security.log by <category>/<rule> and lists its warnings
 *  LV-206  the digest groups 404.log by reason (ids set aside) and lists its notices (LogLevels[404])
 *  LV-207  groupText(): the groupings reason-kind and uri, the fallback to the message, unknown groupings
 *  LV-208  the templates name a labeled log by its label, in both designs
 *  LV-209  the lines expRequestLog and expRequestClassifier format are read back by the parser and the digest
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expLogViewerTestFixtures.php';

class expLogRequestLogsTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $root;

    protected function setUp(): void
    {
        $this->root = expLogViewerTestFixtures::root( 'lr' );
    }

    protected function tearDown(): void
    {
        expLogViewerTestFixtures::remove( $this->root );
    }

    /** The prefix of a request log line, as eZLog writes it */
    private static function prefix( $t, $ip = '203.0.x.x' )
    {
        return '[ ' . date( 'M d Y H:i:s', $t ) . ' ] [' . $ip . '] ';
    }

    private static function notFound( $t, $uri, $reason, $status = 404, $ip = '203.0.x.x' )
    {
        return self::prefix( $t, $ip ) . $status . ' GET ' . $uri . ' siteaccess=site reason=' . $reason
               . ' node=- referrer=- ua=3f2a9c1b "Mozilla/5.0 (X11; Linux x86_64)"' . "\n";
    }

    private static function security( $t, $rule, $uri, $status = '404', $ip = '203.0.x.x' )
    {
        return self::prefix( $t, $ip ) . $rule . ' confidence=high ' . $status . ' GET ' . $uri . ' siteaccess=site namespace=' . $uri
               . ' ua=3f2a9c1b "curl/8.5.0"' . "\n";
    }

    /**
     * The values of one group of an INI file of this installation, as eZINI reads them for the variables used here:
     * plain values, lists (Name[]=v) and keyed values (Name[key]=v). Comments and empty lines are skipped.
     */
    private static function iniGroup( $file, $group )
    {
        $out = array();
        $in = false;
        foreach ( file( dirname( __DIR__, 4 ) . '/settings/' . $file, FILE_IGNORE_NEW_LINES ) as $line )
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

    /** A configuration as settings/logviewer.ini gives it, over this test's root */
    private function iniConfig()
    {
        $values = array();
        foreach ( array( 'LogViewerSettings', 'DigestSettings', 'RotationSettings' ) as $group )
            $values[$group] = self::iniGroup( 'logviewer.ini', $group );
        $values['LogViewerSettings']['Directories'] = array( 'var/log', 'var/*/log' );
        return expLogViewerTestFixtures::config( $this->root, $values );
    }

    /** LV-200 */
    public function testTheKeysResolveAsKernelLogsWithLabelsAndLevels()
    {
        file_put_contents( $this->root . '/var/log/error.log', "e\n" );
        file_put_contents( $this->root . '/var/log/404.log', self::notFound( time(), '/x', 'module:x' ) );
        file_put_contents( $this->root . '/var/log/404.log.1', "old\n" );
        file_put_contents( $this->root . '/var/log/security.log', self::security( time(), 'probe/wp-login', '/wp-login.php' ) );
        file_put_contents( $this->root . '/var/site/log/404.log', "a site's own\n" );
        foreach ( array( expLogViewerTestFixtures::config( $this->root ), $this->iniConfig() ) as $config )
        {
            $locator = new expLogFileLocator( $config, 'var/log' );
            $nf = $locator->resolveKey( '404' );
            $this->assertNotNull( $nf );
            $this->assertSame( 'var/log/404.log', $nf['id'] );
            $this->assertSame( expLogFileLocator::GROUP_KERNEL, $nf['group'] );
            $this->assertSame( 'Not found requests (404)', $nf['label'] );
            $this->assertTrue( $nf['labeled'] );
            $this->assertFalse( $nf['configured'], 'a log of the log directory, not a configured one: it can be rotated' );
            $this->assertSame( 'notice', $nf['level'] );
            $sec = $locator->resolveKey( 'security' );
            $this->assertSame( 'var/log/security.log', $sec['id'] );
            $this->assertSame( expLogFileLocator::GROUP_KERNEL, $sec['group'] );
            $this->assertSame( 'Security (request classifier)', $sec['label'] );
            $this->assertSame( 'warning', $sec['level'] );
            $archive = $locator->resolveKey( '404', 1 );
            $this->assertSame( 'var/log/404.log.1', $archive['id'] );
            $this->assertSame( expLogFileLocator::GROUP_ARCHIVES, $archive['group'] );
            $this->assertSame( 'notice', $archive['level'] );
            $error = $locator->resolveKey( 'error' );
            $this->assertSame( 'error.log', $error['label'], 'a log without a label keeps its name' );
            $this->assertFalse( $error['labeled'] );
            $site = $locator->resolveKey( 'site-404' );
            $this->assertSame( expLogFileLocator::GROUP_SITE, $site['group'] );
            $this->assertFalse( $site['labeled'], 'the label belongs to the key 404 only' );
            $kernel = array_column( $locator->listFiles()[expLogFileLocator::GROUP_KERNEL], 'key' );
            $this->assertSame( array( 'error', '404', 'security' ), $kernel, 'in the order of KernelLogs[]' );
            $info = expLogViewer::fileInfo( $locator->resolve( 'var/log/404.log' ) );
            $this->assertTrue( $info['labeled'] );
            $this->assertSame( 'Not found requests (404)', $info['label'] );
        }
    }

    /** LV-201 */
    public function testTheSettingsRegisterBothLogs()
    {
        $lv = self::iniGroup( 'logviewer.ini', 'LogViewerSettings' );
        $this->assertContains( '404.log', $lv['KernelLogs'] );
        $this->assertContains( 'security.log', $lv['KernelLogs'] );
        $this->assertSame( 'Not found requests (404)', $lv['LogLabel']['404'] );
        $this->assertSame( 'Security (request classifier)', $lv['LogLabel']['security'] );
        $this->assertContains( '404.log*;notice', $lv['FileLevels'] );
        $this->assertContains( 'security.log*;warning', $lv['FileLevels'] );
        $this->assertNotContains( '404', $lv['LogList'], 'not a configured log: those are never rotated' );
        $digest = self::iniGroup( 'logviewer.ini', 'DigestSettings' );
        $this->assertSame( 'reason', $digest['GroupBy']['404'] );
        $this->assertSame( 'category-rule', $digest['GroupBy']['security'] );
        $this->assertSame( 'error,warning,notice', $digest['LogLevels']['404'] );
        // the defaults of the class equal the file, so a test configuration is the shipped one
        $d = expLogViewerConfig::$defaults;
        $this->assertSame( $lv['KernelLogs'], $d['LogViewerSettings']['KernelLogs'] );
        $this->assertSame( $lv['FileLevels'], $d['LogViewerSettings']['FileLevels'] );
        $this->assertSame( $lv['LogLabel'], $d['LogViewerSettings']['LogLabel'] );
        $this->assertSame( $digest['GroupBy'], $d['DigestSettings']['GroupBy'] );
        $this->assertSame( $digest['LogLevels'], $d['DigestSettings']['LogLevels'] );
        $this->assertSame( $digest['Levels'], $d['DigestSettings']['Levels'] );
        $this->assertSame( (int)$digest['Hours'], $d['DigestSettings']['Hours'] );
    }

    /** LV-202 */
    public function testHashTakesKeysOfDigitsButNotListEntries()
    {
        $config = expLogViewerTestFixtures::config( $this->root, array( 'LogViewerSettings' => array(
            'LogLabel' => array( 'a list entry', 'another', '404' => 'Not found', 'x' => 'X', '410' => '' ) ) ) );
        $this->assertSame( array( '404' => 'Not found', 'x' => 'X' ), $config->hash( 'LogViewerSettings', 'LogLabel' ) );
        $config = expLogViewerTestFixtures::config( $this->root, array( 'LogViewerSettings' => array( 'LogLabel' => array( 404 => 'N' ) ) ) );
        $this->assertSame( array( 404 => 'N' ), $config->hash( 'LogViewerSettings', 'LogLabel' ) );
    }

    /** LV-203 */
    public function testTheRotationDefaultsReachTheManager()
    {
        file_put_contents( $this->root . '/var/log/404.log', "x\n" );
        file_put_contents( $this->root . '/var/log/security.log', "x\n" );
        $rotation = self::iniGroup( 'logviewer.ini', 'RotationSettings' );
        $config = $this->iniConfig();
        $manager = new expLogManager( $config, new expLogFileLocator( $config, 'var/log' ),
                                      array( 'keep' => 3, 'max_size' => 2097152, 'compress' => false, 'max_age_days' => 0, 'overrides' => $rotation ) );
        $logs = $manager->logs();
        $this->assertArrayHasKey( '404', $logs, 'manageable: rotate, prune, compress' );
        $this->assertArrayHasKey( 'security', $logs );
        $expected = array( '404' => array( 10485760, 5, true, 30 ), 'security' => array( 10485760, 5, true, 90 ) );
        foreach ( $expected as $key => $values )
        {
            $s = $manager->effectiveSettings( (string)$key );
            $this->assertSame( $values, array( $s['max_size']['value'], $s['keep']['value'], $s['compress']['value'], $s['max_age_days']['value'] ), $key );
            $this->assertSame( 'MaxAgeDays[' . $key . ']', $s['max_age_days']['source']['var'] );
            $this->assertSame( $values[0], $logs[$key]['limits']['max_size'] );
        }
        $this->assertSame( 3, $manager->effectiveSettings( 'error' )['keep']['value'], 'other logs keep the defaults' );
        // the fallback manager reads the same settings
        $fallback = new expLogManagerFallback( $config, new expLogFileLocator( $config, 'var/log' ) );
        $fallback->siteFileSettings = array();
        $limits = $fallback->limits( 'security' );
        $this->assertSame( array( 10485760, 5, true, 90 ), array( $limits['max_size'], $limits['keep'], $limits['compress'], $limits['max_age_days'] ) );
    }

    /** LV-204 */
    public function testTheParserReadsBothLineFormats()
    {
        $t = mktime( 9, 0, 0, 10, 17, 2026 );
        $h = expLogParser::header( rtrim( self::notFound( $t, '/site/no-such-page?q=***', 'module:no-such-page' ) ) );
        $this->assertSame( 'notfound', $h['format'] );
        $this->assertSame( $t, $h['time'] );
        $this->assertSame( '203.0.x.x', $h['ip'] );
        $this->assertSame( '/site/no-such-page?q=***', $h['url'] );
        $this->assertSame( 'site', $h['siteaccess'] );
        $this->assertSame( 404, $h['status'] );
        $this->assertSame( 'module:no-such-page', $h['reason'] );
        $this->assertStringStartsWith( '404 GET /site/no-such-page', $h['message'] );

        $h = expLogParser::header( rtrim( self::security( $t, 'probe/wp-login', '/wp-login.php', '404', '2001:db8:x:x' ) ) );
        $this->assertSame( 'security', $h['format'] );
        $this->assertSame( '2001:db8:x:x', $h['ip'] );
        $this->assertSame( array( 'probe', 'wp-login', 'high', 404, '/wp-login.php', 'site' ),
                           array( $h['category'], $h['rule'], $h['confidence'], $h['status'], $h['url'], $h['siteaccess'] ) );
        $h = expLogParser::header( rtrim( self::security( $t, 'threshold/404-per-minute', '/a', '-' ) ) );
        $this->assertNull( $h['status'], 'a status not known is -' );
        $this->assertSame( 'threshold', $h['category'] );
        $h = expLogParser::header( rtrim( self::security( $t, 'my_ext-rules/x.y', '/a', '200' ) ) );
        $this->assertSame( 'my_ext-rules', $h['category'], 'an extension\'s category' );
        $this->assertSame( 200, $h['status'] );

        // the entries: one per line, with the file's level and the source
        $chunk = self::notFound( $t, '/a', 'node:12' ) . self::notFound( $t + 1, '/b', 'kernel:kernel/20', 410 );
        $entries = expLogParser::parse( $chunk, 0, 'notice' );
        $this->assertCount( 2, $entries );
        $this->assertSame( array( 'notice', 'notice' ), array_column( $entries, 'level' ) );
        $this->assertSame( array( '404', '410' ), array_column( $entries, 'source' ) );
        $this->assertSame( array( '/a', '/b' ), array_column( $entries, 'url' ) );
        $entries = expLogParser::parse( self::security( $t, 'injection/sql-union', '/x?id=***' ), 0, 'warning' );
        $this->assertSame( 'warning', $entries[0]['level'] );
        $this->assertSame( 'injection/sql-union', $entries[0]['source'] );
        $this->assertSame( 'site', $entries[0]['siteaccess'] );

        // an eZDebug line and a plain eZLog line keep their reading
        $h = expLogParser::header( '[ Oct 17 2026 09:00:00 ] [192.0.2.1] [admin] [https://example.test/x] eZTemplate:' );
        $this->assertSame( 'debug', $h['format'] );
        $this->assertSame( 'admin', $h['siteaccess'] );
        $h = expLogParser::header( '[ Oct 17 2026 09:00:00 ] [192.0.2.1] Login of user 14 refused' );
        $this->assertSame( 'debug', $h['format'] );
        $this->assertSame( '', $h['url'] );
        $this->assertArrayNotHasKey( 'status', $h );
    }

    /** LV-205 */
    public function testTheDigestGroupsSecurityLogByCategoryAndRule()
    {
        $now = time();
        file_put_contents( $this->root . '/var/log/security.log',
            self::security( $now - 30, 'probe/wp-login', '/wp-login.php' )
            . self::security( $now - 20, 'probe/wp-login', '/blog/wp-login.php', '403', '198.51.x.x' )
            . self::security( $now - 10, 'injection/sql-union', '/x?id=***', '200' )
            . self::security( $now - 7200, 'probe/env', '/.env' ) );
        $config = $this->iniConfig();
        $digest = new expLogDigest( $config, new expLogRedactor( true, true, true, array() ) );
        $locator = new expLogFileLocator( $config, 'var/log' );
        $out = $digest->digestFile( $locator->resolveKey( 'security' ), $now - 3600 );
        $this->assertSame( 'category-rule', $out['grouping'] );
        $this->assertSame( array( 'error', 'warning' ), $out['levels'] );
        $this->assertSame( 3, $out['total'] );
        $this->assertSame( array( 'probe/wp-login', 'injection/sql-union' ), array_column( $out['groups'], 'text' ) );
        $this->assertSame( array( 2, 1 ), array_column( $out['groups'], 'count' ) );
        $this->assertSame( 'probe/wp-login', $out['groups'][0]['literal'], 'the search finds every line of the rule' );
        $this->assertSame( 'warning', $out['groups'][0]['level'] );
        $this->assertStringContainsString( '/blog/wp-login.php', $out['groups'][0]['sample'], 'the newest line' );
        $this->assertSame( 'Security (request classifier)', $out['label'] );
    }

    /** LV-206 */
    public function testTheDigestGroups404LogByReasonAndListsItsNotices()
    {
        $now = time();
        file_put_contents( $this->root . '/var/log/404.log',
            self::notFound( $now - 50, '/no-such-page', 'module:no-such-page' )
            . self::notFound( $now - 40, '/no-such-page?x=***', 'module:no-such-page' )
            . self::notFound( $now - 30, '/content/view/full/12', 'node:12' )
            . self::notFound( $now - 20, '/content/view/full/13', 'node:13' )
            . self::notFound( $now - 15, '/a/b/c', 'urlalias:/a/b/c' )
            . self::notFound( $now - 10, '/gone', 'kernel:kernel/20', 410 ) );
        $config = $this->iniConfig();
        $digest = new expLogDigest( $config, new expLogRedactor( true, true, true, array() ) );
        $locator = new expLogFileLocator( $config, 'var/log' );
        $out = $digest->digestFile( $locator->resolveKey( '404' ), $now - 3600 );
        $this->assertSame( 'reason', $out['grouping'] );
        $this->assertSame( array( 'error', 'warning', 'notice' ), $out['levels'] );
        $this->assertSame( 6, $out['total'], 'notices are listed for this log' );
        $texts = array_column( $out['groups'], 'count', 'text' );
        $this->assertSame( 2, $texts['reason=module:no-such-page'] );
        $this->assertSame( 2, $texts['reason=node:#'], 'node ids set aside' );
        $this->assertSame( 1, $texts['reason=urlalias:<path>'] );
        $this->assertSame( 1, $texts['reason=kernel:kernel/#'] );
        foreach ( $out['groups'] as $g )
        {
            $this->assertSame( 'notice', $g['level'] );
            $this->assertStringStartsWith( 'reason=', $g['literal'] );
        }
        // without LogLevels[404] the notices are not in an error digest
        $plain = new expLogDigest( expLogViewerTestFixtures::config( $this->root, array( 'DigestSettings' => array( 'LogLevels' => array() ) ) ),
                                   new expLogRedactor( true, true, true, array() ) );
        $this->assertSame( 0, $plain->digestFile( $locator->resolveKey( '404' ), $now - 3600 )['total'] );
        $this->assertSame( array( 'error', 'warning' ), $plain->levelsFor( '404' ) );
    }

    /** LV-207 */
    public function testGroupText()
    {
        $line = '404 GET /blog/2026/10/post?x=*** siteaccess=site reason=view:content/view node=- referrer=- ua=3f2a9c1b "M"';
        $this->assertSame( 'reason=view:content/view', expLogDigest::groupText( 'reason', $line ) );
        $this->assertSame( 'reason=view:', expLogDigest::groupText( 'reason-kind', $line ) );
        $this->assertSame( 'GET /blog/#/#/post', expLogDigest::groupText( 'uri', $line ) );
        $this->assertSame( 'probe/wp-login', expLogDigest::groupText( 'category-rule', 'probe/wp-login confidence=high 404 GET /wp-login.php' ) );
        // a line without the field: its normalized message
        $this->assertSame( expLogDigest::normalize( 'Node 12 not found' ), expLogDigest::groupText( 'reason', 'Node 12 not found' ) );
        $this->assertSame( expLogDigest::normalize( 'Node 12 not found' ), expLogDigest::groupText( 'category-rule', 'Node 12 not found' ) );
        $digest = new expLogDigest( expLogViewerTestFixtures::config( $this->root, array( 'DigestSettings' => array(
            'GroupBy' => array( 'error' => 'no-such-grouping', 'debug' => 'URI' ) ) ) ), new expLogRedactor( true, true, true, array() ) );
        $this->assertSame( 'message', $digest->groupingFor( 'error' ), 'an unknown grouping is the message' );
        $this->assertSame( 'uri', $digest->groupingFor( 'debug' ) );
        $this->assertSame( 'message', $digest->groupingFor( 'notice' ) );
        $this->assertSame( array( 'error', 'warning' ), $digest->levelsFor( 'notice' ) );
        $this->assertSame( array( 'error', 'warning', 'notice' ), $digest->levelsFor( '404' ), 'the default of LogLevels[404]' );
    }

    /** LV-209 the lines the writers format (expRequestLog, expRequestClassifier) are the ones the viewer reads */
    public function testTheWritersLinesAreReadBack()
    {
        if ( !class_exists( 'expRequestLog' ) || !class_exists( 'expRequestClassifier' ) )
            $this->markTestSkipped( 'the request log classes are not installed' );
        $t = mktime( 9, 0, 0, 10, 17, 2026 );
        $line = expRequestLog::formatLine( expRequestLog::notFoundLine( array(
            'status' => 404, 'method' => 'GET', 'uri' => '/site/no-such-page?q=secret value', 'siteaccess' => 'site',
            'reason' => 'module:no-such-page', 'node' => null, 'referrer' => '', 'ua' => 'Mozilla/5.0 (X11; Linux x86_64)' ) ),
            expRequestLog::formatIp( '203.0.113.9' ), $t );
        $h = expLogParser::header( $line );
        $this->assertSame( 'notfound', $h['format'], $line );
        $this->assertSame( $t, $h['time'] );
        $this->assertSame( '203.0.x.x', $h['ip'] );
        $this->assertSame( 'site', $h['siteaccess'] );
        $this->assertSame( 'module:no-such-page', $h['reason'] );
        $this->assertStringNotContainsString( 'secret', $h['url'] );
        $this->assertSame( 'reason=module:no-such-page', expLogDigest::groupText( 'reason', $h['message'] ) );

        $context = expRequestContext::create( array( 'method' => 'GET', 'uri' => '/site/wp-login.php', 'siteaccess' => 'site',
                                                    'userAgent' => 'curl/8.5.0', 'ip' => '2001:db8:1:2::9' ) );
        $context->status = 404;
        $line = expRequestClassifier::prefix( '2001:db8:1:2::9', true, $t )
              . expRequestClassifier::securityLine( new expRequestVerdict( 'probe', 'wp-login', 'high' ), $context );
        $h = expLogParser::header( $line );
        $this->assertSame( 'security', $h['format'], $line );
        $this->assertSame( array( 'probe', 'wp-login', 'high', 404, 'site' ),
                           array( $h['category'], $h['rule'], $h['confidence'], $h['status'], $h['siteaccess'] ) );
        $this->assertStringNotContainsString( '2001:db8:1:2::9', $line );
        $this->assertSame( 'probe/wp-login', expLogDigest::groupText( 'category-rule', $h['message'] ) );
    }

    /** LV-208 */
    public function testTheTemplatesNameALabeledLogByItsLabel()
    {
        $root = dirname( __DIR__, 4 );
        foreach ( array( 'logs_menu.tpl', 'logs.tpl', 'logs_digest.tpl' ) as $name )
        {
            $a = file_get_contents( "$root/design/admin/templates/setup/$name" );
            $this->assertSame( $a, file_get_contents( "$root/design/admin4/templates/setup/$name" ), $name );
            $this->assertDoesNotMatchRegularExpression( '/<style|style="/i', $a );
        }
        $this->assertStringContainsString( '$lognav_file.labeled', file_get_contents( "$root/design/admin/templates/setup/logs_menu.tpl" ) );
        $this->assertStringContainsString( '{if $log_item.labeled}', file_get_contents( "$root/design/admin/templates/setup/logs.tpl" ) );
        $this->assertStringContainsString( '$digest_log.grouping', file_get_contents( "$root/design/admin/templates/setup/logs_digest.tpl" ) );
    }
}
