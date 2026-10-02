<?php
/**
 * Fixtures of the audit tests: a throwaway log directory and key directory under var/tmp/audit-tests/, test
 * settings (expAuditConfig::setOverride()), fixed test keys and a fixed request and actor context. No test writes
 * to the live audit log, reads the live settings for the audit, or touches the database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expAuditTestFixtures
{
    const INSTALLATION = '6f1c3e0a-2b7d-4c55-9a01-3d2e4f5a6b7c';
    const KEY_ID = 'k1-20261002-3fa94c1b';

    /** @var string|null */
    protected static $runDir = null;

    /** @var int */
    protected static $counter = 0;

    /** @return string The installation root, with a trailing slash */
    public static function realRoot()
    {
        return dirname( __DIR__, 6 ) . '/';
    }

    /** @return string var/tmp/audit-tests/run-<time>-<pid>/ */
    public static function runDir()
    {
        if ( self::$runDir === null )
        {
            self::$runDir = self::realRoot() . 'var/tmp/audit-tests/run-' . date( 'Ymd-His' ) . '-' . getmypid() . '/';
            if ( !is_dir( self::$runDir ) )
                mkdir( self::$runDir, 0750, true );
        }
        return self::$runDir;
    }

    /**
     * A fresh directory for one test, with log/ and keys/ in it, and the audit pointed at it.
     *
     * @param string $name
     * @param array $settings more 'Block/Variable' => value test settings
     * @param bool $knownKeys use the fixed test keys (false: no keys, so they are generated into keys/)
     * @return string the directory, with a trailing slash
     */
    public static function setUp( $name, array $settings = array(), $knownKeys = true )
    {
        $dir = self::runDir() . sprintf( '%03d-', ++self::$counter ) . preg_replace( '#[^\w-]#', '_', $name ) . '/';
        mkdir( $dir . 'log', 0750, true );
        mkdir( $dir . 'keys', 0750, true );
        self::configure( $dir, $settings );
        $_SERVER['REQUEST_TIME_FLOAT'] = microtime( true ) + self::$counter / 1000;
        expAudit::resetAll();
        expAudit::setNow( null );
        if ( $knownKeys )
            expAuditKeys::setKnown( self::keys(), $dir . 'keys' );
        return $dir;
    }

    /**
     * @param string $dir
     * @param array $settings
     */
    public static function configure( $dir, array $settings = array() )
    {
        expAuditConfig::setOverride( $settings + array(
            'logDir' => $dir . 'log',
            'keyDir' => $dir . 'keys',
            'context' => self::context(),
            'handlers' => false,
        ) );
        expAuditTaxonomy::reset();
    }

    /** @return array The fixed request and actor of the tests */
    public static function context()
    {
        return array(
            'actor' => array( 'user_id' => 14, 'login' => 'editor1', 'roles' => array( 2 ), 'session' => 'sess-0123456789abcdef',
                              'ip' => '203.0.113.7', 'ua' => 'Mozilla/5.0 (X11; Linux x86_64; rv:131.0) Gecko/20100101 Firefox/131.0' ),
            'request' => array( 'siteaccess' => 'admin', 'method' => 'POST', 'url' => '/content/action?NodeID=275&ezxform_token=abc',
                                'engine' => 'cli', 'host' => 'web1', 'pid' => 4242 ),
        );
    }

    /** @return array The fixed test keys */
    public static function keys()
    {
        return array( 'installation' => self::INSTALLATION, 'active' => self::KEY_ID,
                      'signing' => array( self::KEY_ID => str_repeat( "\x5a", 32 ) ), 'pseudonym' => str_repeat( "\xa5", 32 ) );
    }

    /** Back to the live settings (no file of the test is removed: they stay in var/tmp/audit-tests/ to inspect). */
    public static function tearDown()
    {
        expAuditConfig::setOverride( null );
        expAuditKeys::setKnown( null );
        expAudit::resetAll();
        expAudit::setNow( null );
    }

    /**
     * The records of a channel, from its files, in order.
     *
     * @param string $dir
     * @param string $channel
     * @return array[]
     */
    public static function records( $dir, $channel )
    {
        $out = array();
        foreach ( expAuditWriter::channelFiles( $dir . 'log', $channel ) as $f )
            foreach ( file( $dir . 'log/' . $f, FILE_IGNORE_NEW_LINES ) as $line )
                if ( $line !== '' )
                    $out[] = json_decode( $line, true );
        return $out;
    }

    /**
     * The records of a channel without the audit's own file records.
     *
     * @return array[]
     */
    public static function events( $dir, $channel )
    {
        return array_values( array_filter( self::records( $dir, $channel ), function ( $r ) {
            return strpos( $r['name'], 'system.audit.file.' ) !== 0 && $r['name'] !== 'system.audit.checkpoint';
        } ) );
    }

    /** @return expAuditVerifier */
    public static function verifier( $dir )
    {
        return new expAuditVerifier( $dir . 'log', new expAuditKeys( expAuditConfig::get() ) );
    }
}
