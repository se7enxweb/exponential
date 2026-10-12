<?php
/**
 * The request URL decoded once or twice: site.ini [URLTranslator] DecodeOnce (eZURI::decodeOnce()).
 *
 *  DO-01 - Off (the shipped value): today's decoding, eZSys::requestURI() decodes and eZURI decodes again, so
 *          /(foo)/bar%2Bbar gives the view parameter "bar bar"
 *  DO-02 - On: the same address gives "bar+bar", and an encoded "%" stays a "%"
 *  DO-03 - Both: UTF-8 text in the path (an IRI alias) and plain addresses give the same elements
 *  DO-04 - An eZURI made with $decode = true or false decodes, or not, whatever the setting says
 *  DO-05 - The setting missing from site.ini counts as off
 *  DO-06 - The raw REQUEST_URI and the query string are not changed in either mode (the response cache keys
 *          are made from the raw REQUEST_URI)
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit f435c2f91e), Copyright (C) Mugo Web, GPL v2.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezutils/eZSysRequestURIDecodeOnceTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZSysRequestURIDecodeOnceTest extends ezpTestCase
{
    /** @var eZSys */
    private $sysBackup;

    /** @var mixed */
    private $uriBackup;

    /** @var bool */
    private $hadUri;

    /** @var string|null */
    private $serverRequestUri;

    public function setUp(): void
    {
        parent::setUp();
        $this->sysBackup = eZSys::instance();
        $this->hadUri = isset( $GLOBALS['eZURIRequestInstance'] );
        $this->uriBackup = $this->hadUri ? $GLOBALS['eZURIRequestInstance'] : null;
        $this->serverRequestUri = $_SERVER['REQUEST_URI'] ?? null;
    }

    public function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        if ( $this->serverRequestUri === null )
            unset( $_SERVER['REQUEST_URI'] );
        else
            $_SERVER['REQUEST_URI'] = $this->serverRequestUri;
        eZSys::setInstance( $this->sysBackup );
        unset( $GLOBALS['eZURIRequestInstance'] );
        if ( $this->hadUri )
            $GLOBALS['eZURIRequestInstance'] = $this->uriBackup;
        parent::tearDown();
    }

    /**
     * Starts a request for $requestUri as the kernel does (eZSys::init() in virtual host mode) and returns the
     * request's eZURI.
     *
     * @param string $requestUri the address as the browser sends it
     * @param string|null $decodeOnce 'enabled', 'disabled', or null to leave the setting out
     * @return eZURI
     */
    private function request( $requestUri, $decodeOnce )
    {
        if ( $decodeOnce === null )
        {
            $ini = eZINI::instance();
            ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'DecodeOnce', 'disabled' );
            $ini->removeSetting( 'URLTranslator', 'DecodeOnce' );
        }
        else
            ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'DecodeOnce', $decodeOnce );

        $_SERVER['REQUEST_URI'] = $requestUri;
        eZSys::setInstance( new eZSys( array( '_SERVER' => array(
            'PHP_SELF' => '/index.php',
            'SCRIPT_FILENAME' => '/srv/www/index.php',
            'REQUEST_URI' => $requestUri ) ) ) );
        eZSys::init( 'index.php', true );
        unset( $GLOBALS['eZURIRequestInstance'] );
        return eZURI::instance();
    }

    /** DO-01 */
    public function testOffDecodesTwice()
    {
        $uri = $this->request( '/(foo)/bar%2Bbar', 'disabled' );
        $this->assertFalse( eZURI::decodeOnce() );
        $this->assertSame( '/(foo)/bar+bar', eZSys::requestURI() );
        $this->assertSame( array( 'foo' => 'bar bar' ), $uri->userParameters() );

        $uri = $this->request( '/content/view/full/2/(tag)/a%2525b', 'disabled' );
        $this->assertSame( array( 'tag' => 'a%b' ), $uri->userParameters() );
    }

    /** DO-02 */
    public function testOnDecodesOnce()
    {
        $uri = $this->request( '/(foo)/bar%2Bbar', 'enabled' );
        $this->assertTrue( eZURI::decodeOnce() );
        $this->assertSame( '/(foo)/bar+bar', eZSys::requestURI() );
        $this->assertSame( array( 'foo' => 'bar+bar' ), $uri->userParameters() );

        $uri = $this->request( '/content/view/full/2/(tag)/a%2525b', 'enabled' );
        $this->assertSame( array( 'tag' => 'a%25b' ), $uri->userParameters() );

        $uri = $this->request( '/content/view/full/2/(tag)/100%25', 'enabled' );
        $this->assertSame( array( 'tag' => '100%' ), $uri->userParameters() );
        $this->assertSame( 'content/view/full/2', $uri->uriString() );
    }

    /** DO-03 */
    public function testSameElementsForPlainAndUtf8Paths()
    {
        foreach ( array( 'disabled', 'enabled' ) as $mode )
        {
            $uri = $this->request( '/Caf%C3%A9-M%C3%BCnchen/Ein-Artikel', $mode );
            $this->assertSame( array( 'Café-München', 'Ein-Artikel' ), $uri->elements( false ), $mode );

            $uri = $this->request( '/content/view/full/2/(offset)/10', $mode );
            $this->assertSame( 'content/view/full/2', $uri->uriString(), $mode );
            $this->assertSame( array( 'offset' => '10' ), $uri->userParameters(), $mode );

            $uri = $this->request( '/', $mode );
            $this->assertSame( '', eZSys::requestURI(), $mode );
            $this->assertSame( '', $uri->uriString(), $mode );
        }
    }

    /** DO-04 */
    public function testExplicitDecodeWins()
    {
        foreach ( array( 'disabled', 'enabled' ) as $mode )
        {
            ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'DecodeOnce', $mode );
            $encoded = new eZURI( '/news/a%2Bb', true );
            $this->assertSame( 'news/a+b', $encoded->uriString(), $mode );
            $decoded = new eZURI( '/news/a+b', false );
            $this->assertSame( 'news/a+b', $decoded->uriString(), $mode );
            $decoded->setURIString( 'news/c%2Bd', true, true );
            $this->assertSame( 'news/c+d', $decoded->uriString(), $mode );
        }

        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'DecodeOnce', 'disabled' );
        $default = new eZURI( '/news/a+b' );
        $this->assertSame( 'news/a b', $default->uriString() );
        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'DecodeOnce', 'enabled' );
        $default = new eZURI( '/news/a+b' );
        $this->assertSame( 'news/a+b', $default->uriString() );
    }

    /** DO-05 */
    public function testMissingSettingIsOff()
    {
        $uri = $this->request( '/(foo)/bar%2Bbar', null );
        $this->assertFalse( eZINI::instance()->hasVariable( 'URLTranslator', 'DecodeOnce' ) );
        $this->assertFalse( eZURI::decodeOnce() );
        $this->assertSame( array( 'foo' => 'bar bar' ), $uri->userParameters() );
    }

    /** DO-06 */
    public function testRawRequestUriAndQueryStringUnchanged()
    {
        foreach ( array( 'disabled', 'enabled' ) as $mode )
        {
            $this->request( '/(foo)/bar%2Bbar?SearchText=a%2Bb', $mode );
            $this->assertSame( '/(foo)/bar%2Bbar?SearchText=a%2Bb', eZSys::serverVariable( 'REQUEST_URI' ), $mode );
            $this->assertSame( '?SearchText=a%2Bb', eZSys::queryString(), $mode );
        }
    }
}
