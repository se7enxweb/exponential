<?php
/**
 * error.ini [HTTPError-<code>] HeaderList[<name>]=<value>: extra response headers of an error page, after the
 * status lines of HTTPName (expErrorHeaderList); the error view sends them and puts them in
 * $Result['responseHeaders'], and a content view cache file holding the error page gives them back, so the
 * cached page sends them again.
 *
 * The settings are set in-process on error.ini and put back afterwards; alpha's error.ini is not changed. The
 * view cache file is written below var/tmp. No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/error/ErrorHeaderListTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 97690e9c93), Copyright (C) Mugo Web, GPL v2
 */

class ErrorHeaderListTest extends PHPUnit\Framework\TestCase
{
    private $saved = array();
    private $dir;

    public static function setUpBeforeClass(): void
    {
        // The cluster handler is started outside the tests: starting it installs a handler of its own
        eZClusterFileHandler::instance();
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/error_headerlist_' . uniqid( '', true );
        mkdir( $this->dir, 0700, true );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'error.ini' );
        foreach ( $this->saved as $group => $values )
        {
            foreach ( $values as $name => $value )
                $this->apply( $ini, $group, $name, $value );
        }
        $this->saved = array();
        foreach ( glob( $this->dir . '/*' ) ?: array() as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    /** Sets a value of error.ini for this test only */
    private function set( $group, $name, $value )
    {
        $ini = eZINI::instance( 'error.ini' );
        if ( !isset( $this->saved[$group] ) || !array_key_exists( $name, $this->saved[$group] ) )
            $this->saved[$group][$name] = $ini->hasVariable( $group, $name ) ? $ini->variable( $group, $name ) : null;
        $this->apply( $ini, $group, $name, $value );
    }

    /** Sets a value, or removes it for null */
    private function apply( eZINI $ini, $group, $name, $value )
    {
        if ( $value !== null )
            $ini->setVariable( $group, $name, $value );
        else if ( $ini->hasGroup( $group ) )
            $ini->removeSetting( $group, $name );
    }

    public function testWithoutAHeaderListTheStatusLinesAreAsBefore()
    {
        $this->set( 'HTTPError-404', 'HeaderList', array() );
        $this->set( 'HTTPError-404', 'HTTPName', 'Not Found' );
        $headers = expErrorHeaderList::build( eZINI::instance( 'error.ini' ), 404, 'HTTP/1.1' );
        $this->assertSame( 'Not Found', $headers['name'] );
        $this->assertSame( array( 'HTTP/1.1 404 Not Found', 'Status: 404 Not Found' ), $headers['status'] );
        $this->assertSame( array(), $headers['headers'] );
    }

    public function testTheHeaderListFollowsTheStatusLines()
    {
        $this->set( 'HTTPError-404', 'HTTPName', 'Not Found' );
        $this->set( 'HTTPError-404', 'HeaderList', array( 'Cache-Control' => 'public, must-revalidate, max-age=300',
                                                          'X-Robots-Tag' => 'noindex' ) );
        $headers = expErrorHeaderList::build( eZINI::instance( 'error.ini' ), 404, 'HTTP/2' );
        $this->assertSame( array( 'HTTP/2 404 Not Found', 'Status: 404 Not Found' ), $headers['status'] );
        $this->assertSame( array( 'Cache-Control: public, must-revalidate, max-age=300', 'X-Robots-Tag: noindex' ), $headers['headers'] );
    }

    public function testStatusInTheHeaderListStandsInForAMissingHTTPName()
    {
        $this->set( 'HTTPError-410', 'HTTPName', null );
        $this->set( 'HTTPError-410', 'HeaderList', array( 'Status' => 'Gone', 'Cache-Control' => 'no-store' ) );
        $headers = expErrorHeaderList::build( eZINI::instance( 'error.ini' ), 410, null );
        $this->assertSame( 'Gone', $headers['name'] );
        $this->assertSame( array( 'HTTP/1.1 410 Gone', 'Status: 410 Gone' ), $headers['status'] );
        $this->assertSame( array( 'Cache-Control: no-store' ), $headers['headers'] );
    }

    public function testHTTPNameWinsOverStatusInTheHeaderList()
    {
        $this->set( 'HTTPError-404', 'HTTPName', 'Not Found' );
        $this->set( 'HTTPError-404', 'HeaderList', array( 'Status' => 'Something Else' ) );
        $headers = expErrorHeaderList::build( eZINI::instance( 'error.ini' ), 404, 'HTTP/1.1' );
        $this->assertSame( 'Not Found', $headers['name'] );
        $this->assertSame( array(), $headers['headers'] );
    }

    public function testAnInvalidNameOrAValueWithALineBreakIsLeftOut()
    {
        $this->set( 'HTTPError-404', 'HTTPName', 'Not Found' );
        $this->set( 'HTTPError-404', 'HeaderList', array( 'Bad Name' => 'x', 'X-Split' => "a\r\nSet-Cookie: s=1",
                                                          'X-Nul' => "a\0b", 'X-Good' => 'yes' ) );
        $headers = @expErrorHeaderList::build( eZINI::instance( 'error.ini' ), 404, "HTTP/1.1\r\nX-Evil: 1" );
        $this->assertSame( array( 'X-Good: yes' ), $headers['headers'] );
        $this->assertSame( 'HTTP/1.1 404 Not Found', $headers['status'][0], 'a protocol that is not one is replaced' );
    }

    public function testACodeWithoutSettingsGivesNothing()
    {
        $headers = expErrorHeaderList::build( eZINI::instance( 'error.ini' ), 599, 'HTTP/1.1' );
        $this->assertSame( array( 'name' => null, 'status' => array(), 'headers' => array() ), $headers );
    }

    public function testTheErrorViewStoresTheHeadersInItsResult()
    {
        $this->set( 'HTTPError-404', 'HTTPName', 'Not Found' );
        $this->set( 'HTTPError-404', 'HeaderList', array( 'Cache-Control' => 'no-store' ) );
        $result = $this->runErrorView( eZError::KERNEL_NOT_AVAILABLE );

        $this->assertSame( 404, (int)$result['errorCode'] );
        $this->assertSame( 'Not Found', $result['errorMessage'] );
        $this->assertContains( 'Status: 404 Not Found', $result['responseHeaders'] );
        $this->assertContains( 'Cache-Control: no-store', $result['responseHeaders'] );
        $this->assertSame( 'Cache-Control: no-store', end( $result['responseHeaders'] ), 'after the status lines' );
    }

    public function testTheViewCacheGivesTheHeadersBackWithTheErrorPage()
    {
        $this->set( 'HTTPError-404', 'HTTPName', 'Not Found' );
        $this->set( 'HTTPError-404', 'HeaderList', array( 'Cache-Control' => 'no-store' ) );
        $result = $this->runErrorView( eZError::KERNEL_NOT_AVAILABLE );

        // As contentViewGenerateError() stores it and contentViewRetrieve() reads it back for content/view,
        // which sends every line of responseHeaders again
        $file = $this->dir . '/view-cache.cache';
        file_put_contents( $file, serialize( $result ) );
        $cached = eZNodeviewfunctions::contentViewRetrieve( $file, time(), array( 'ini' => eZINI::instance() ) );

        $this->assertIsArray( $cached );
        $this->assertSame( $result['responseHeaders'], $cached['responseHeaders'] );
        $this->assertContains( 'Cache-Control: no-store', $cached['responseHeaders'] );
    }

    private function runErrorView( $errorNumber )
    {
        // Outside a web request nobody sets the module paths
        $pathList = eZModule::globalPathList();
        eZModule::setGlobalPathList( eZModule::activeModuleRepositories() );
        $module = eZModule::exists( 'error' );
        eZModule::setGlobalPathList( $pathList );
        $this->assertInstanceOf( 'eZModule', $module );
        $hadUri = array_key_exists( 'REQUEST_URI', $_SERVER );
        $uri = $hadUri ? $_SERVER['REQUEST_URI'] : null;
        $_SERVER['REQUEST_URI'] = '/error-header-list-test';
        $hadBasics = array_key_exists( 'eZSiteBasics', $GLOBALS );
        $basics = $hadBasics ? $GLOBALS['eZSiteBasics'] : null;
        if ( !isset( $GLOBALS['eZSiteBasics']['user-object-required'] ) )
            $GLOBALS['eZSiteBasics']['user-object-required'] = false;
        $hadRequested = array_key_exists( 'eZRequestedURI', $GLOBALS );
        $requested = $hadRequested ? $GLOBALS['eZRequestedURI'] : null;
        if ( !$hadRequested )
            $GLOBALS['eZRequestedURI'] = null;
        try
        {
            $result = @$module->run( 'view', array( 'kernel', $errorNumber ) );
        }
        finally
        {
            if ( $hadUri )
                $_SERVER['REQUEST_URI'] = $uri;
            else
                unset( $_SERVER['REQUEST_URI'] );
            if ( $hadBasics )
                $GLOBALS['eZSiteBasics'] = $basics;
            else
                unset( $GLOBALS['eZSiteBasics'] );
            if ( $hadRequested )
                $GLOBALS['eZRequestedURI'] = $requested;
            else
                unset( $GLOBALS['eZRequestedURI'] );
            unset( $GLOBALS['eZRequestError'] );
        }
        $this->assertIsArray( $result );
        return $result;
    }
}
