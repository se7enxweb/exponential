<?php
/**
 * The request classifier's machinery (settings/requestclassifier.ini): the namespace path, the extension points
 * (RuleSets[] and [RuleSet-<id>] Class=, patterns added by INI, Listeners[]), the compiled pattern cache, the
 * security.log line and its file. DB-less; the settings are given to the constructor, files go to a temporary
 * directory.
 *
 *  RC-10  namespace path: /admin/wp-login.php and /site/.env are evaluated as /wp-login.php and /.env; index.php and
 *         several URI elements; a path of another siteaccess stays as it is
 *  RC-11  an extension's rule set by RuleSets[] + Class=; a Class that does not exist or does not implement the
 *         interface is skipped with one warning; a set that throws is skipped
 *  RC-12  patterns by INI: a rule added to a built-in set, a built-in rule disabled, an exclude added, a broken
 *         pattern or a back reference left out with a warning
 *  RC-13  Classification=disabled classifies nothing; the first set in RuleSets[] order wins
 *  RC-14  the security.log line (contract format) and its file: masked address, redacted query, UA hash; the
 *         listeners are told after the line; a broken listener is skipped
 *  RC-16  the shipped settings/requestclassifier.ini: the safe defaults, every built-in set, no warning
 *  RC-17  BUILT_IN_FINGERPRINT matches the built-in patterns
 *  RC-15  the compiled cache file: written once per settings version, used by a fresh instance, a new one for new
 *         settings
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** An extension's rule set, for RC-11 */
class expRequestClassifierTestRuleSet implements expRequestRuleSetInterface
{
    public static $created = array();
    protected $id;

    public function __construct( $id, array $settings = array(), $classifier = null )
    {
        $this->id = $id;
        self::$created[] = array( $id, $settings );
    }

    public function identifier()
    {
        return $this->id;
    }

    public function evaluate( expRequestContext $context )
    {
        return strpos( $context->namespacePath, '/honeypot' ) === 0 ? new expRequestVerdict( 'trap', 'honeypot', 'high' ) : null;
    }
}

/** A pattern rule set of an extension, for RC-11 */
class expRequestClassifierTestPatternRuleSet extends expRequestPatternRuleSet
{
    public function category()
    {
        return 'custom_cat';
    }

    protected function builtInPatterns()
    {
        return array( 'old-joomla' => '^/administrator(?:/|$)' );
    }

    protected function subjects( expRequestContext $context )
    {
        return array( $context->decodedNamespacePath() );
    }
}

/** A rule set that fails, for RC-11 */
class expRequestClassifierTestFailingRuleSet extends expRequestClassifierTestRuleSet
{
    public function evaluate( expRequestContext $context )
    {
        throw new RuntimeException( 'broken' );
    }
}

/** Not a rule set, for RC-11 */
class expRequestClassifierTestNotARuleSet
{
}

/** A listener, for RC-14 */
class expRequestClassifierTestListener implements expRequestVerdictListenerInterface
{
    public static $calls = array();

    public function onVerdict( expRequestVerdict $verdict, expRequestContext $context )
    {
        self::$calls[] = array( $verdict->key(), $context->namespacePath, isset( $GLOBALS['expRequestClassifierTestLog'] ) && is_file( $GLOBALS['expRequestClassifierTestLog'] ) );
    }
}

/** A listener that fails, for RC-14 */
class expRequestClassifierTestFailingListener implements expRequestVerdictListenerInterface
{
    public function onVerdict( expRequestVerdict $verdict, expRequestContext $context )
    {
        throw new RuntimeException( 'listener down' );
    }
}

class expRequestClassifierTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $dir;

    protected function setUp(): void
    {
        expRequestClassifier::reset( true );
        expRequestClassifierTestRuleSet::$created = array();
        expRequestClassifierTestListener::$calls = array();
        $this->dir = sys_get_temp_dir() . '/rc-' . bin2hex( random_bytes( 6 ) );
        mkdir( $this->dir, 0700, true );
    }

    protected function tearDown(): void
    {
        unset( $GLOBALS['expRequestClassifierTestLog'] );
        self::remove( $this->dir );
        expRequestClassifier::reset( true );
    }

    protected static function remove( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( scandir( $dir ) as $name )
        {
            if ( $name === '.' || $name === '..' )
                continue;
            $path = $dir . '/' . $name;
            if ( is_dir( $path ) && !is_link( $path ) )
                self::remove( $path );
            else
                unlink( $path );
        }
        rmdir( $dir );
    }

    protected function classifier( array $config = array() )
    {
        return new expRequestClassifier( $config + array(
            'compileCache' => false,
            'salt' => 'test-salt',
            'cacheDir' => $this->dir . '/cache',
            'logDir' => $this->dir . '/log',
            'maskIP' => true,
        ) );
    }

    protected function context( $uri, array $values = array() )
    {
        return expRequestContext::create( $values + array( 'uri' => $uri, 'siteaccess' => 'site', 'ip' => '203.0.113.7', 'status' => 404,
                                                           'userAgent' => 'curl/8.5.0' ) );
    }

    /** RC-10 */
    public function testNamespacePath()
    {
        $c = $this->context( '/admin/wp-login.php', array( 'siteaccess' => 'admin' ) );
        $this->assertSame( '/wp-login.php', $c->namespacePath );
        $verdict = $this->classifier()->classify( $c );
        $this->assertSame( 'probe/wp-login', $verdict->key() );

        $c = $this->context( '/site/.env' );
        $this->assertSame( '/.env', $c->namespacePath );
        $this->assertSame( 'probe/env', $this->classifier()->classify( $c )->key() );

        $this->assertSame( '/', $this->context( '/site' )->namespacePath );
        $this->assertSame( '/content/view/full/2', $this->context( '/index.php/site/content/view/full/2' )->namespacePath );
        $this->assertSame( '/content/view/full/2', $this->context( '/Admin/content/view/full/2', array( 'siteaccess' => 'admin' ) )->namespacePath );
        // only a whole element is the siteaccess
        $this->assertSame( '/sitemap.xml', $this->context( '/sitemap.xml' )->namespacePath );
        // host matching: nothing to strip
        $this->assertSame( '/site/x', $this->context( '/site/x', array( 'siteaccessInURI' => false ) )->namespacePath );
        // several URI elements
        $this->assertSame( '/x', expRequestContext::namespaceOf( '/de/shop/x', array( 'de', 'shop' ) ) );
        $this->assertSame( '/other/x', expRequestContext::namespaceOf( '/other/x', array( 'de' ) ) );

        // the query and the fragment are not part of the path
        $c = $this->context( 'https://example.org/site/a/b?x=1#frag' );
        $this->assertSame( '/site/a/b?x=1', $c->uri );
        $this->assertSame( '/a/b', $c->namespacePath );
        $this->assertSame( 'x=1', $c->query );
    }

    /** RC-10: from the globals, as the kernel builds it */
    public function testFromGlobals()
    {
        $server = $_SERVER;
        $access = isset( $GLOBALS['eZCurrentAccess'] ) ? $GLOBALS['eZCurrentAccess'] : null;
        try
        {
            $_SERVER['REQUEST_URI'] = '/admin/.git/config?x=1';
            $_SERVER['REQUEST_METHOD'] = 'get';
            $_SERVER['HTTP_USER_AGENT'] = 'nuclei';
            $GLOBALS['eZCurrentAccess'] = array( 'name' => 'admin', 'type' => eZSiteAccess::TYPE_URI, 'uri_part' => array( 'admin' ) );
            $c = expRequestContext::fromGlobals( 404 );
            $this->assertSame( 'GET', $c->method );
            $this->assertSame( 'admin', $c->siteaccess );
            $this->assertSame( '/.git/config', $c->namespacePath );
            $this->assertSame( 404, $c->status );
            $this->assertSame( 'nuclei', $c->userAgent );

            // no siteaccess known: the path as it is
            unset( $GLOBALS['eZCurrentAccess'] );
            $this->assertSame( '/admin/.git/config', expRequestContext::fromGlobals()->namespacePath );
        }
        finally
        {
            $_SERVER = $server;
            if ( $access === null )
                unset( $GLOBALS['eZCurrentAccess'] );
            else
                $GLOBALS['eZCurrentAccess'] = $access;
        }
    }

    /** RC-11 */
    public function testExtensionRuleSets()
    {
        $classifier = $this->classifier( array(
            'ruleSets' => array( 'missing', 'notone', 'failing', 'probes', 'trap', 'joomla' ),
            'sets' => array(
                'missing' => array( 'Class' => 'expRequestClassifierNoSuchClass' ),
                'notone' => array( 'Class' => 'expRequestClassifierTestNotARuleSet' ),
                'failing' => array( 'Class' => 'expRequestClassifierTestFailingRuleSet' ),
                'trap' => array( 'Class' => 'expRequestClassifierTestRuleSet', 'Note' => 'x' ),
                'joomla' => array( 'Class' => 'expRequestClassifierTestPatternRuleSet' ),
            ),
        ) );
        $this->assertSame( array( 'failing', 'probes', 'trap', 'joomla' ), array_keys( $classifier->ruleSets() ) );
        $this->assertSame( array( array( 'failing', array( 'Class' => 'expRequestClassifierTestFailingRuleSet' ) ),
                                  array( 'trap', array( 'Class' => 'expRequestClassifierTestRuleSet', 'Note' => 'x' ) ) ),
                           expRequestClassifierTestRuleSet::$created );

        $v = $classifier->classify( $this->context( '/site/honeypot/x' ) );
        $this->assertSame( 'trap/honeypot', $v->key() );
        $this->assertSame( 'trap', $v->ruleSet );
        $this->assertSame( 'custom_cat/old-joomla', $classifier->classify( $this->context( '/administrator/index.php' ) )->key() );
        // the built-in sets still work after a failing one
        $this->assertSame( 'probe/env', $classifier->classify( $this->context( '/.env' ) )->key() );
        $this->assertNull( $classifier->classify( $this->context( '/news' ) ) );

        $warnings = implode( "\n", expRequestClassifier::warnings() );
        $this->assertStringContainsString( "'expRequestClassifierNoSuchClass' of the rule set 'missing' does not exist", $warnings );
        $this->assertStringContainsString( "'expRequestClassifierTestNotARuleSet' of the rule set 'notone' does not implement", $warnings );
        $this->assertStringContainsString( "the rule set 'failing' failed", $warnings );
        // once per process
        $this->classifier( array( 'ruleSets' => array( 'missing' ), 'sets' => array( 'missing' => array( 'Class' => 'expRequestClassifierNoSuchClass' ) ) ) )->ruleSets();
        $this->assertSame( 1, substr_count( implode( "\n", expRequestClassifier::warnings() ), "'missing' does not exist" ) );

        // a set without a class (not built in)
        $this->classifier( array( 'ruleSets' => array( 'nameless' ) ) )->ruleSets();
        $this->assertStringContainsString( "'nameless' has no Class", implode( "\n", expRequestClassifier::warnings() ) );
    }

    /** RC-12 */
    public function testPatternsByINI()
    {
        $classifier = $this->classifier( array(
            'ruleSets' => array( 'probes', 'injection', 'scanner' ),
            'sets' => array(
                'probes' => array(
                    'Class' => '',
                    'Patterns' => array( 'old-admin' => '^/administrator(/|$)', 'broken' => '([a-', 'backref' => '(a)\1', 'named' => '(?<n>x)y' ),
                    'Disabled' => array( 'cgi-bin' ),
                    'Excludes' => array( 'legacy' => '^/legacy/' ),
                    'Confidence' => array( 'old-admin' => 'low' ),
                ),
                'injection' => array( 'Disabled' => array(), 'Patterns' => array() ),
                'scanner' => array( 'Patterns' => array( 'probe-bot' => '\bprobebot\b' ), 'DefaultConfidence' => 'medium' ),
            ),
        ) );
        $v = $classifier->classify( $this->context( '/administrator/' ) );
        $this->assertSame( 'probe/old-admin', $v->key() );
        $this->assertSame( 'low', $v->confidence );
        $this->assertNull( $classifier->classify( $this->context( '/cgi-bin/x' ) ) );
        $this->assertNull( $classifier->classify( $this->context( '/legacy/.env' ) ) );
        $this->assertSame( 'probe/env', $classifier->classify( $this->context( '/.env' ) )->key() );
        $v = $classifier->classify( $this->context( '/', array( 'userAgent' => 'ProbeBot/1.0', 'status' => 200 ) ) );
        $this->assertSame( 'scanner/probe-bot', $v->key() );
        $this->assertSame( 'medium', $v->confidence );

        $warnings = implode( "\n", expRequestClassifier::warnings() );
        $this->assertStringContainsString( "'broken' of the rule set 'probes' was left out (does not compile)", $warnings );
        $this->assertStringContainsString( "'backref' of the rule set 'probes' was left out (back reference", $warnings );
        $this->assertStringContainsString( "'named' of the rule set 'probes' was left out (back reference", $warnings );

        // a built-in exclude removed: a download path is a probe again
        $classifier = $this->classifier( array( 'ruleSets' => array( 'probes' ), 'sets' => array( 'probes' => array( 'Excludes' => array( 'download' => '' ) ) ) ) );
        $this->assertSame( 'probe/backup', $classifier->classify( $this->context( '/content/download/1/2/file/backup.zip' ) )->key() );
        // a ~ in a pattern does not end the expression
        $classifier = $this->classifier( array( 'ruleSets' => array( 'probes' ), 'sets' => array( 'probes' => array( 'Patterns' => array( 'tilde' => '^/~root' ) ) ) ) );
        $this->assertSame( 'probe/tilde', $classifier->classify( $this->context( '/~root/x' ) )->key() );
    }

    /** RC-13 */
    public function testDisabledAndOrder()
    {
        $this->assertNull( $this->classifier( array( 'enabled' => false ) )->classify( $this->context( '/wp-login.php' ) ) );
        $this->assertFalse( $this->classifier( array( 'enabled' => false ) )->enabled() );
        // a scanner asking for a probe: the probe set comes first by default, the scanner set when listed first
        $c = $this->context( '/wp-login.php', array( 'userAgent' => 'sqlmap/1.8' ) );
        $this->assertSame( 'probe/wp-login', $this->classifier()->classify( $c )->key() );
        $this->assertSame( 'scanner/sqlmap', $this->classifier( array( 'ruleSets' => array( 'scanner', 'probes' ) ) )->classify( $c )->key() );
        // the defaults
        $config = $this->classifier()->config();
        $this->assertTrue( $config['enabled'] );
        $this->assertSame( 'security.log', $config['logFile'] );
        $this->assertSame( array( 'probes', 'traversal', 'injection', 'scanner', 'threshold' ), $config['ruleSets'] );
        $this->assertSame( array( 'max' => 30, 'window' => 60, 'statuses' => array( 404, 410 ) ), $config['threshold'] );
        // a log file name never leaves the log directory
        $this->assertSame( $this->dir . '/log/passwd', $this->classifier( array( 'logFile' => '../../etc/passwd' ) )->logFile() );
    }

    /** RC-14 */
    public function testSecurityLineAndLog()
    {
        $c = $this->context( '/wp-login.php', array( 'siteaccess' => 'site' ) );
        $v = new expRequestVerdict( 'probe', 'wp-login', 'high' );
        $this->assertSame( 'probe/wp-login confidence=high 404 GET /wp-login.php siteaccess=site namespace=/wp-login.php ua='
                           . substr( sha1( 'curl/8.5.0' ), 0, 8 ) . ' "curl/8.5.0"', expRequestClassifier::securityLine( $v, $c ) );

        // query secrets and addresses redacted, quotes and control characters out, the length capped, status unknown
        $c = $this->context( "/site/x?password=hunter2&mail=jane.doe%40example.com&q=a b\"c\x01", array( 'status' => null, 'method' => 'post',
                             'userAgent' => str_repeat( 'A', 50 ) . "\"quoted\"\x07" . str_repeat( 'B', 30 ) ) );
        $line = expRequestClassifier::securityLine( new expRequestVerdict( 'injection', 'sql-union', 'bogus' ), $c, 300 );
        $this->assertStringStartsWith( 'injection/sql-union confidence=medium - POST /site/x?password=***&mail=j***@***.com&q=a%20b%22c siteaccess=site namespace=/x ua=', $line );
        $this->assertStringNotContainsString( 'hunter2', $line );
        $this->assertMatchesRegularExpression( '/ ua=[0-9a-f]{8} "A{50}quotedB{4}"$/', $line );
        $long = expRequestClassifier::securityLine( $v, $this->context( '/' . str_repeat( 'a', 500 ) ), 300 );
        $this->assertStringContainsString( ' GET /' . str_repeat( 'a', 299 ) . '... ', $long );
        $this->assertStringEndsWith( 'ua=- ""', expRequestClassifier::securityLine( $v, $this->context( '/', array( 'userAgent' => '' ) ) ) );

        // the address masked as the log viewer masks it
        $this->assertSame( '203.0.x.x', expRequestClassifier::maskIP( '203.0.113.7' ) );
        $this->assertSame( '2001:db8:x:x', expRequestClassifier::maskIP( '2001:db8:85a3::8a2e:370:7334' ) );
        $this->assertSame( 'x', expRequestClassifier::maskIP( 'web01' ) );
        $this->assertSame( '', expRequestClassifier::maskIP( '' ) );
        $this->assertMatchesRegularExpression( '/^\[ [A-Z][a-z]{2} \d{2} \d{4} \d{2}:\d{2}:\d{2} \] \[203\.0\.x\.x\] $/', expRequestClassifier::prefix( '203.0.113.7' ) );
        $this->assertSame( '[ Oct 17 2026 09:00:00 ] [203.0.113.7] ', expRequestClassifier::prefix( '203.0.113.7', false, mktime( 9, 0, 0, 10, 17, 2026 ) ) );

        // the file, then the listeners
        $classifier = $this->classifier( array( 'listeners' => array( 'expRequestClassifierTestListener', 'expRequestClassifierTestFailingListener',
                                                                      'expRequestClassifierTestNotARuleSet', 'expRequestClassifierNoSuchListener' ) ) );
        $GLOBALS['expRequestClassifierTestLog'] = $classifier->logFile();
        $c = $this->context( '/site/.env' );
        $this->assertTrue( $classifier->write( $classifier->classify( $c ), $c ) );
        $c2 = $this->context( '/site/x?id=1%20union%20select%201', array( 'ip' => '2001:db8:85a3::1', 'status' => 200 ) );
        $this->assertTrue( $classifier->write( $classifier->classify( $c2 ), $c2 ) );
        $lines = file( $this->dir . '/log/security.log', FILE_IGNORE_NEW_LINES );
        $this->assertCount( 2, $lines );
        $this->assertMatchesRegularExpression( '#^\[ \w{3} \d{2} \d{4} [\d:]{8} \] \[203\.0\.x\.x\] probe/env confidence=high 404 GET /site/\.env siteaccess=site namespace=/\.env ua=[0-9a-f]{8} "curl/8\.5\.0"$#', $lines[0] );
        $this->assertMatchesRegularExpression( '#\] \[2001:db8:x:x\] injection/sql-union confidence=high 200 GET /site/x\?id=1%20union%20select%201 siteaccess=site namespace=/x #', $lines[1] );
        $this->assertStringNotContainsString( '203.0.113.7', implode( "\n", $lines ) );
        $this->assertSame( array( array( 'probe/env', '/.env', true ), array( 'injection/sql-union', '/x', true ) ), expRequestClassifierTestListener::$calls );
        $warnings = implode( "\n", expRequestClassifier::warnings() );
        $this->assertStringContainsString( 'expRequestClassifierTestFailingListener failed: listener down', $warnings );
        $this->assertStringContainsString( "'expRequestClassifierTestNotARuleSet' does not exist or does not implement", $warnings );
        $this->assertStringContainsString( "'expRequestClassifierNoSuchListener' does not exist or does not implement", $warnings );

        // unmasked when MaskIP is disabled
        $classifier = $this->classifier( array( 'maskIP' => false, 'logFile' => 'other.log' ) );
        $classifier->write( $v, $this->context( '/wp-login.php' ) );
        $this->assertStringContainsString( '] [203.0.113.7] probe/wp-login', file_get_contents( $this->dir . '/log/other.log' ) );
    }

    /** RC-16: the shipped settings/requestclassifier.ini gives the safe defaults and every built-in set */
    public function testShippedSettings()
    {
        $ini = eZINI::instance( 'requestclassifier.ini', 'settings', null, false, false, true );
        $this->assertSame( 'enabled', $ini->variable( 'ClassifierSettings', 'Classification' ) );
        $this->assertSame( 'security.log', $ini->variable( 'ClassifierSettings', 'LogFile' ) );
        $this->assertSame( array( 'probes', 'traversal', 'injection', 'scanner', 'threshold' ), $ini->variable( 'ClassifierSettings', 'RuleSets' ) );
        $this->assertSame( array(), $ini->variable( 'ClassifierSettings', 'Listeners' ), 'no listener, nothing blocked' );
        $this->assertSame( '30', $ini->variable( 'ThresholdSettings', 'Max404PerMinute' ) );

        $config = expRequestClassifier::configFromINI();
        $this->assertTrue( $config['enabled'] );
        $this->assertSame( array( 'probes', 'traversal', 'injection', 'scanner', 'threshold' ), array_keys( $config['sets'] ) );
        $classifier = $this->classifier( array( 'ruleSets' => $config['ruleSets'], 'sets' => $config['sets'], 'threshold' => $config['threshold'] ) );
        $this->assertSame( array( 'probes', 'traversal', 'injection', 'scanner', 'threshold' ), array_keys( $classifier->ruleSets() ) );
        $this->assertSame( 'probe/wp-login', $classifier->classify( $this->context( '/site/wp-login.php' ) )->key() );
        $this->assertNull( $classifier->classify( $this->context( '/site/content/view/full/2', array( 'status' => 200 ) ) ) );
        $this->assertSame( array(), expRequestClassifier::warnings() );
    }

    /** RC-17: a changed built-in pattern needs a new BUILT_IN_FINGERPRINT (the compiled caches' version) */
    public function testBuiltInFingerprint()
    {
        $this->assertSame( expRequestClassifier::builtInFingerprint(), expRequestClassifier::BUILT_IN_FINGERPRINT,
                           'a built-in pattern changed: set expRequestClassifier::BUILT_IN_FINGERPRINT to the new value' );
    }

    /** RC-15 */
    public function testCompiledCacheFile()
    {
        $config = array( 'compileCache' => true, 'ruleSets' => array( 'probes', 'scanner' ) );
        $this->classifier( $config )->classify( $this->context( '/news' ) );
        $files = glob( $this->dir . '/cache/compiled-*.php' );
        $this->assertCount( 1, $files );
        $data = include $files[0];
        $this->assertSame( array( 'probes', 'scanner' ), array_keys( $data['sets'] ) );
        $this->assertStringContainsString( '(*MARK:r0)', $data['sets']['probes']['regex'] );
        $this->assertSame( array(), glob( $this->dir . '/cache/.compiled-*' ), 'no temporary file left' );

        // a fresh process (no static cache, no APCu entry) uses the file
        expRequestClassifier::reset( true );
        if ( function_exists( 'apcu_enabled' ) && apcu_enabled() )
        {
            $this->assertNotFalse( apcu_fetch( 'expRequestClassifier:' . $data['hash'] ), 'kept in APCu too' );
            apcu_delete( 'expRequestClassifier:' . $data['hash'] );
        }
        file_put_contents( $files[0], str_replace( "'wp-login'", "'wp-login-from-file'", file_get_contents( $files[0] ) ) );
        if ( function_exists( 'opcache_invalidate' ) )
            opcache_invalidate( $files[0], true );
        $this->assertSame( 'probe/wp-login-from-file', $this->classifier( $config )->classify( $this->context( '/wp-login.php' ) )->key() );

        // other settings, another file
        $config['sets'] = array( 'probes' => array( 'Patterns' => array( 'x' => '^/x-probe' ) ) );
        $this->assertSame( 'probe/x', $this->classifier( $config )->classify( $this->context( '/x-probe' ) )->key() );
        $this->assertCount( 2, glob( $this->dir . '/cache/compiled-*.php' ) );
    }
}
