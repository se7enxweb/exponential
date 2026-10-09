<?php
/**
 * The TLS options of eZSOAPClient over HTTPS, without a server.
 *
 *  ST-01 - By default the certificate of the server is verified (peer and host name), against the system CA bundle
 *  ST-02 - setCAFile() adds a CA file of its own; null, false (a missing setting), '' or blanks set none
 *  ST-03 - setVerifyPeer( false ) turns the check off for this client only
 *  ST-04 - send() makes its cURL call with these options
 *  ST-05 - Without the PHP extension curl a call over HTTPS is refused with an error; over HTTP it is still made
 *  ST-06 - A CA file name with a NUL byte sets no CA file (cURL would throw a ValueError in send())
 *  ST-07 - Under open_basedir, a CA file outside it is not reported as unreadable (cURL reads it past open_basedir)
 *
 * ST-05 and ST-07 run the client in a PHP of its own (fixtures/soap_client_tls_subprocess.php).
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

    /** ST-05 */
    public function testWithoutCurlHttpsIsRefusedAndHttpIsStillMade()
    {
        $https = $this->runSubprocess( array( '-n' ), array( 'send-https' ) );
        if ( $https['curl'] )
        {
            $this->markTestSkipped( 'curl is compiled into ' . PHP_BINARY . ', it cannot be left out' );
        }
        $this->assertSame( 0, $https['send'] );
        $this->assertStringContainsString( 'needs the PHP extension curl', $https['error'] );
        $this->assertSame( array( array( 'error', 'No SOAP call to 127.0.0.1: HTTPS needs the PHP extension curl, which is not loaded' ) ),
                           $https['messages'] );

        $http = $this->runSubprocess( array( '-n' ), array( 'send-http' ) );
        $this->assertSame( 0, $http['send'] );
        $this->assertStringContainsString( 'Unable to open connection to 127.0.0.1', $http['error'], 'over HTTP the socket is still used' );
        $this->assertSame( array(), $http['messages'] );
    }

    /** ST-06 */
    public function testACAFileNameWithANulByteSetsNone()
    {
        $client = new eZSOAPClientTlsOptionsTestClient( 'soap.example.invalid', '/soap', 443 );
        $client->setCAFile( "/etc/ssl/internal-ca.pem\0.txt" );
        $this->assertNull( $client->caFile() );
        $this->assertArrayNotHasKey( CURLOPT_CAINFO, $client->options() );
    }

    /** ST-07 */
    public function testACAFileOutsideOpenBasedirIsNotReportedAsUnreadable()
    {
        $outside = null;
        foreach ( array( '/etc/pki/tls/certs/ca-bundle.crt', '/etc/ssl/certs/ca-certificates.crt', '/etc/ssl/cert.pem' ) as $file )
        {
            if ( is_readable( $file ) )
            {
                $outside = $file;
                break;
            }
        }
        if ( $outside === null )
        {
            $this->markTestSkipped( 'no CA bundle found outside the source tree' );
        }

        $root = dirname( __DIR__, 4 );
        $restricted = $this->runSubprocess( array( '-d', 'open_basedir=' . $root ), array( 'options', $outside ) );
        $this->assertSame( $outside, $restricted['cainfo'] );
        $this->assertSame( array(), $restricted['messages'], 'cURL reads the file past open_basedir' );

        // without open_basedir a file that is not there is still reported
        $missing = $this->runSubprocess( array(), array( 'options', $root . '/var/no-such-ca-file.pem' ) );
        $this->assertCount( 1, $missing['messages'] );
        $this->assertSame( 'error', $missing['messages'][0][0] );
        $this->assertStringContainsString( 'cannot be read', $missing['messages'][0][1] );
    }

    /**
     * Runs fixtures/soap_client_tls_subprocess.php in a PHP of its own and gives its answer.
     *
     * @param string[] $phpOptions
     * @param string[] $arguments
     * @return array
     */
    private function runSubprocess( array $phpOptions, array $arguments )
    {
        if ( !function_exists( 'proc_open' ) )
        {
            $this->markTestSkipped( 'needs proc_open' );
        }
        $command = array_merge( array( PHP_BINARY ), $phpOptions, array( '-d', 'display_errors=stderr', __DIR__ . '/fixtures/soap_client_tls_subprocess.php' ), $arguments );
        $process = proc_open( $command, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
        $this->assertIsResource( $process );
        $out = stream_get_contents( $pipes[1] );
        $err = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        proc_close( $process );
        $result = json_decode( $out, true );
        $this->assertIsArray( $result, "the subprocess answered: $out $err" );
        return $result;
    }
}
