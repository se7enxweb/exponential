<?php
/**
 * The WebDAV entry script is index_webdav.php, named like index_rest.php, index_treemenu.php and
 * index_cluster.php. webdav.php, its old name, is a shim that only includes index_webdav.php (so existing
 * virtual hosts and rewrite rules keep working) and writes one deprecation warning a day.
 *
 * No database: the files are read and the helper is called with a log directory of its own.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class expWebDAVEntryScriptTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $logDir;

    protected function tearDown(): void
    {
        if ( $this->logDir !== null && is_dir( $this->logDir ) )
        {
            eZDir::recursiveDelete( $this->logDir );
        }
        parent::tearDown();
    }

    private static function root()
    {
        return dirname( __DIR__, 4 );
    }

    private static function code( $file )
    {
        // The source without comments and whitespace, so only what runs is compared
        $code = '';
        foreach ( token_get_all( file_get_contents( self::root() . '/' . $file ) ) as $token )
        {
            if ( is_array( $token ) )
            {
                if ( in_array( $token[0], array( T_COMMENT, T_DOC_COMMENT, T_WHITESPACE, T_OPEN_TAG, T_CLOSE_TAG ), true ) )
                {
                    continue;
                }
                $code .= $token[1];
            }
            else
            {
                $code .= $token;
            }
        }
        return $code;
    }

    public function testIndexWebdavIsTheWebdavServer()
    {
        $this->assertFileExists( self::root() . '/index_webdav.php' );
        $code = self::code( 'index_webdav.php' );
        $this->assertStringContainsString( 'ezcWebdavServer::getInstance()', $code );
        $this->assertStringContainsString( 'eZSys::init($webdavEntryScript', $code );
        $this->assertStringContainsString( '$webdavEntryScript=expWebDAVEntryScript::current();', $code );
        $this->assertStringNotContainsString( "'webdav.php'", $code, 'index_webdav.php must not hard-code its old name' );
    }

    public function testTheShimOnlyNamesItselfAndIncludesIndexWebdav()
    {
        $this->assertFileExists( self::root() . '/webdav.php' );
        $this->assertSame(
            "define('EXP_WEBDAV_ENTRY_SCRIPT','webdav.php');require__DIR__.'/index_webdav.php';",
            self::code( 'webdav.php' )
        );
    }

    public function testTheEntryScriptIsIndexWebdavUnlessTheShimSaysOtherwise()
    {
        // The shim is not loaded in this process, so the constant is not defined
        $this->assertFalse( defined( 'EXP_WEBDAV_ENTRY_SCRIPT' ) );
        $this->assertSame( 'index_webdav.php', expWebDAVEntryScript::current() );
        $this->assertSame( 'index_webdav.php', expWebDAVEntryScript::SCRIPT );
        $this->assertSame( 'webdav.php', expWebDAVEntryScript::DEPRECATED_SCRIPT );
    }

    public function testBothNamesAreTheEntryScript()
    {
        $this->assertTrue( expWebDAVEntryScript::isEntryScript( 'index_webdav.php' ) );
        $this->assertTrue( expWebDAVEntryScript::isEntryScript( '/index_webdav.php' ) );
        $this->assertTrue( expWebDAVEntryScript::isEntryScript( '/sub/dir/webdav.php' ) );
        $this->assertFalse( expWebDAVEntryScript::isEntryScript( 'index.php' ) );
        $this->assertFalse( expWebDAVEntryScript::isEntryScript( 'index_rest.php' ) );
        $this->assertFalse( expWebDAVEntryScript::isEntryScript( '' ) );
    }

    public function testRootRequests()
    {
        foreach ( array( '', '/', '/index_webdav.php', '/index_webdav.php/', '/webdav.php', '/webdav.php/' ) as $uri )
        {
            $this->assertTrue( expWebDAVEntryScript::isRootRequest( $uri ), $uri );
        }
        foreach ( array( '/site', '/index_webdav.php/site', '/webdav.php/site/Content', '/index.php', '/index_webdav.phpx' ) as $uri )
        {
            $this->assertFalse( expWebDAVEntryScript::isRootRequest( $uri ), $uri );
        }
    }

    public function testTheDeprecationNoticeIsWrittenOnceADay()
    {
        $this->logDir = self::root() . '/var/tmp/expWebDAVEntryScriptTest-' . getmypid() . '-' . mt_rand();
        $day1 = mktime( 9, 0, 0, 10, 29, 2026 );
        $later = mktime( 18, 0, 0, 10, 29, 2026 );
        $day2 = mktime( 9, 0, 0, 10, 30, 2026 );

        $this->assertTrue( expWebDAVEntryScript::noteDeprecatedEntry( $this->logDir, $day1 ) );
        $this->assertFalse( expWebDAVEntryScript::noteDeprecatedEntry( $this->logDir, $later ) );
        $this->assertTrue( expWebDAVEntryScript::noteDeprecatedEntry( $this->logDir, $day2 ) );

        $log = file_get_contents( $this->logDir . '/warning.log' );
        $this->assertSame( 2, substr_count( $log, 'webdav.php only forwards to index_webdav.php' ) );
        $this->assertStringContainsString( 'later major version', $log );
        $this->assertSame( '2026-10-30', file_get_contents( $this->logDir . '/' . expWebDAVEntryScript::STAMP_FILE ) );
    }

    public function testASignInThroughEitherNameIsAWebdavSession()
    {
        $before = isset( $_SERVER['SCRIPT_NAME'] ) ? $_SERVER['SCRIPT_NAME'] : null;
        try
        {
            foreach ( array( '/index_webdav.php' => 'webdav', '/webdav.php' => 'webdav', '/soap.php' => 'soap' ) as $script => $method )
            {
                $_SERVER['SCRIPT_NAME'] = $script;
                $this->assertSame( $method, expUserSessionTracker::detectMethod(), $script );
            }
        }
        finally
        {
            if ( $before === null )
            {
                unset( $_SERVER['SCRIPT_NAME'] );
            }
            else
            {
                $_SERVER['SCRIPT_NAME'] = $before;
            }
        }
    }

    public function testTheShippedRewriteRulesNameIndexWebdav()
    {
        foreach ( array( '.htaccess_root', '.htaccess_root_static' ) as $file )
        {
            $rules = file_get_contents( self::root() . '/' . $file );
            $this->assertStringContainsString( '#RewriteRule ^index_webdav\.php - [L]', $rules, $file );
            $this->assertStringNotContainsString( 'RewriteRule ^webdav', $rules, $file );
        }
    }
}
