<?php
/**
 * The TLS options of eZSOAPClient over HTTPS, without a server.
 *
 *  ST-01 - By default the certificate of the server is verified (peer and host name), against the system CA bundle
 *  ST-02 - setCAFile() adds a CA file of its own; null, false (a missing setting), '' or blanks set none
 *  ST-03 - setVerifyPeer( false ) turns the check off for this client only
 *  ST-04 - send() makes its cURL call with these options
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

/**
 * Gives the cURL options of a call and records the options send() used.
 */
class eZSOAPClientTlsOptionsTestClient extends eZSOAPClient
{
    public $used = null;

    public function options()
    {
        return $this->curlOptions( 'https://soap.example.invalid:443/soap', '<x/>', array( 'Content-Type: text/xml' ) );
    }

    protected function curlOptions( $URL, $payload, array $headers )
    {
        $this->used = parent::curlOptions( $URL, $payload, $headers );
        return $this->used;
    }
}

class eZSOAPClientTlsOptionsTest extends PHPUnit\Framework\TestCase
{
    private $cwd;

    protected function setUp(): void
    {
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 4 ) );
    }

    protected function tearDown(): void
    {
        chdir( $this->cwd );
    }

    /** ST-01 */
    public function testTheCertificateIsVerifiedByDefault()
    {
        $client = new eZSOAPClientTlsOptionsTestClient( 'soap.example.invalid', '/soap', 443 );
        $this->assertTrue( $client->verifyPeer() );
        $this->assertNull( $client->caFile() );
        $options = $client->options();
        $this->assertTrue( $options[CURLOPT_SSL_VERIFYPEER] );
        $this->assertSame( 2, $options[CURLOPT_SSL_VERIFYHOST] );
        $this->assertArrayNotHasKey( CURLOPT_CAINFO, $options );
        $this->assertSame( 'https://soap.example.invalid:443/soap', $options[CURLOPT_URL] );
        $this->assertSame( '<x/>', $options[CURLOPT_POSTFIELDS] );
    }

    /** ST-02 */
    public function testACAFileOfItsOwn()
    {
        $client = new eZSOAPClientTlsOptionsTestClient( 'soap.example.invalid', '/soap', 443 );
        $client->setCAFile( '/etc/ssl/internal-ca.pem' );
        $this->assertSame( '/etc/ssl/internal-ca.pem', $client->caFile() );
        $options = $client->options();
        $this->assertSame( '/etc/ssl/internal-ca.pem', $options[CURLOPT_CAINFO] );
        $this->assertTrue( $options[CURLOPT_SSL_VERIFYPEER], 'a CA file does not turn the check off' );
        $this->assertSame( 2, $options[CURLOPT_SSL_VERIFYHOST] );

        foreach ( array( '', '  ', null, false ) as $none )
        {
            $client->setCAFile( '/etc/ssl/internal-ca.pem' );
            $client->setCAFile( $none );
            $this->assertNull( $client->caFile(), var_export( $none, true ) . ' sets no CA file' );
            $this->assertArrayNotHasKey( CURLOPT_CAINFO, $client->options() );
        }
        $client->setCAFile( ' /etc/ssl/internal-ca.pem ' );
        $this->assertSame( '/etc/ssl/internal-ca.pem', $client->caFile(), 'trimmed' );
    }

    /** ST-03 */
    public function testTurningTheCheckOffIsForThisClientOnly()
    {
        $client = new eZSOAPClientTlsOptionsTestClient( 'soap.example.invalid', '/soap', 443 );
        $client->setVerifyPeer( false );
        $this->assertFalse( $client->verifyPeer() );
        $options = $client->options();
        $this->assertFalse( $options[CURLOPT_SSL_VERIFYPEER] );
        $this->assertSame( 0, $options[CURLOPT_SSL_VERIFYHOST] );

        $other = new eZSOAPClientTlsOptionsTestClient( 'soap.example.invalid', '/soap', 443 );
        $this->assertTrue( $other->options()[CURLOPT_SSL_VERIFYPEER], 'another client still verifies' );
    }

    /** ST-04 */
    public function testSendUsesTheOptions()
    {
        if ( !function_exists( 'curl_init' ) )
        {
            $this->markTestSkipped( 'needs the curl extension' );
        }
        // a closed port on this machine: the call fails at once, after the options were taken
        $client = new eZSOAPClientTlsOptionsTestClient( '127.0.0.1', '/soap', 1, true );
        $client->setCAFile( '/etc/ssl/internal-ca.pem' );
        $client->setTimeout( 2 );
        $this->assertSame( 0, $client->send( new eZSOAPRequest( 'x1', 'urn:x1' ) ) );
        $this->assertIsArray( $client->used, 'send() took the options from curlOptions()' );
        $this->assertSame( '/etc/ssl/internal-ca.pem', $client->used[CURLOPT_CAINFO] );
        $this->assertSame( 'https://127.0.0.1:1/soap', $client->used[CURLOPT_URL] );
    }
}
