<?php
/**
 * eZSMTPTransport:
 *
 *  ST-01 - an SMTP server that offers only AUTH NTLM: ezcMailSmtpTransport::authNtlm() throws
 *          ezcBaseExtensionNotFoundException('mcrypt'), which is no ezcMailException; sendMail() returns false instead
 *          of letting it out into the request (registration, password reset)
 *  ST-02 - site.ini [MailSettings] TransportAuthMethod=LOGIN: the transport asks for AUTH LOGIN, whatever the server
 *          offers; the server refusing it is a normal failure (false)
 *  ST-03 - TransportAuthMethod: the names of ezcMailSmtpTransport (any case), empty or unknown is the library's choice
 *  ST-04 - credentials over an unencrypted connection to a host other than loopback are recognised (a warning, the
 *          mail is still sent)
 *
 * ST-01/ST-02 talk to a fake SMTP server on 127.0.0.1 (fixtures/smtp_fake_server.php, a child process); nothing is
 * delivered. No database.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZSMTPTransportTest extends PHPUnit\Framework\TestCase
{
    /** @var array the MailSettings values before the test, null for a missing one */
    private $saved = array();

    /** @var resource|null */
    private $server;

    /** @var array */
    private $serverPipes = array();

    private const KEYS = array( 'Transport', 'TransportServer', 'TransportPort', 'TransportConnectionType', 'TransportUser',
                                'TransportPassword', 'TransportAuthMethod', 'EmailSender', 'AdminEmail', 'SenderHost',
                                'DebugSending', 'ExcludeHeaders' );

    protected function setUp(): void
    {
        $ini = eZINI::instance();
        foreach ( self::KEYS as $key )
            $this->saved[$key] = $ini->hasVariable( 'MailSettings', $key ) ? $ini->variable( 'MailSettings', $key ) : null;
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        foreach ( $this->saved as $key => $value )
        {
            if ( $value === null )
                $ini->removeSetting( 'MailSettings', $key );
            else
                $ini->setVariable( 'MailSettings', $key, $value );
        }
        $this->stopServer();
    }

    /**
     * Starts the fake server and returns its port.
     *
     * @param string $auth what it offers after AUTH
     * @return int
     */
    private function startServer( $auth )
    {
        if ( !function_exists( 'proc_open' ) || PHP_BINARY === '' )
            $this->markTestSkipped( 'needs proc_open() and the PHP binary' );
        $fixture = __DIR__ . '/fixtures/smtp_fake_server.php';
        $this->assertFileExists( $fixture );
        $this->server = proc_open( array( PHP_BINARY, $fixture, $auth ),
                                   array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $this->serverPipes );
        $this->assertIsResource( $this->server );
        $line = $this->readLine( 5 );
        $this->assertMatchesRegularExpression( '/^port \d+$/', (string)$line, 'the fake server listens' );
        return (int)substr( $line, 5 );
    }

    /**
     * One line of the fake server's stdout, or null after $timeout seconds.
     */
    private function readLine( $timeout )
    {
        $read = array( $this->serverPipes[1] );
        $write = $except = null;
        if ( !stream_select( $read, $write, $except, $timeout ) )
            return null;
        $line = fgets( $this->serverPipes[1] );
        return $line === false ? null : rtrim( $line, "\n" );
    }

    /**
     * What the client sent, as the fake server saw it, until it ended (at most five seconds).
     *
     * @return string[]
     */
    private function clientLines()
    {
        $lines = array();
        $end = microtime( true ) + 5;
        while ( microtime( true ) < $end && ( $line = $this->readLine( 1 ) ) !== null )
        {
            if ( strncmp( $line, 'C: ', 3 ) === 0 )
                $lines[] = substr( $line, 3 );
        }
        return $lines;
    }

    private function stopServer()
    {
        if ( !$this->server )
            return;
        foreach ( $this->serverPipes as $pipe )
            fclose( $pipe );
        proc_terminate( $this->server );
        proc_close( $this->server );
        $this->server = null;
        $this->serverPipes = array();
    }

    private function configure( $port, $authMethod = '' )
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'MailSettings', 'Transport', 'SMTP' );
        $ini->setVariable( 'MailSettings', 'TransportServer', '127.0.0.1' );
        $ini->setVariable( 'MailSettings', 'TransportPort', (string)$port );
        $ini->setVariable( 'MailSettings', 'TransportConnectionType', '' );
        $ini->setVariable( 'MailSettings', 'TransportUser', 'mailer' );
        $ini->setVariable( 'MailSettings', 'TransportPassword', 'secret' );
        $ini->setVariable( 'MailSettings', 'TransportAuthMethod', $authMethod );
        $ini->setVariable( 'MailSettings', 'EmailSender', 'site@example.invalid' );
        $ini->setVariable( 'MailSettings', 'AdminEmail', 'admin@example.invalid' );
        $ini->setVariable( 'MailSettings', 'SenderHost', 'test.invalid' );
        $ini->setVariable( 'MailSettings', 'DebugSending', 'disabled' );
        $ini->setVariable( 'MailSettings', 'ExcludeHeaders', array() );
    }

    private static function mail()
    {
        $mail = new eZMail();
        $mail->setSender( 'site@example.invalid' );
        $mail->setReceiver( 'reader@example.invalid' );
        $mail->setSubject( 'SMTP test' );
        $mail->setBody( 'Body' );
        return $mail;
    }

    /** ST-01 */
    public function testNtlmWithoutMcryptIsAFailedSendAndNoException()
    {
        if ( ezcBaseFeatures::hasExtensionSupport( 'mcrypt' ) )
            $this->markTestSkipped( 'mcrypt is loaded, NTLM does not throw here' );
        $this->configure( $this->startServer( 'NTLM' ) );

        $transport = new eZSMTPTransport();
        $this->assertFalse( $transport->sendMail( self::mail() ) );

        $lines = $this->clientLines();
        $this->assertNotEmpty( preg_grep( '/^EHLO /', $lines ), implode( "\n", $lines ) );
        $this->assertSame( array(), preg_grep( '/^(MAIL FROM|DATA)/i', $lines ), 'nothing was sent without authentication' );
    }

    /** ST-02 */
    public function testTransportAuthMethodIsAskedFor()
    {
        $this->configure( $this->startServer( 'NTLM' ), 'login' );

        $transport = new eZSMTPTransport();
        $this->assertFalse( $transport->sendMail( self::mail() ), 'the fake server refuses AUTH LOGIN' );

        $lines = $this->clientLines();
        $this->assertContains( 'AUTH LOGIN', $lines, implode( "\n", $lines ) );
        $this->assertSame( array(), preg_grep( '/NTLM/', $lines ) );
    }

    /** ST-03 */
    public function testAuthMethodNames()
    {
        $this->assertSame( ezcMailSmtpTransport::AUTH_LOGIN, eZSMTPTransport::authMethod( 'LOGIN' ) );
        $this->assertSame( ezcMailSmtpTransport::AUTH_PLAIN, eZSMTPTransport::authMethod( ' plain ' ) );
        $this->assertSame( ezcMailSmtpTransport::AUTH_CRAM_MD5, eZSMTPTransport::authMethod( 'cram-md5' ) );
        $this->assertSame( ezcMailSmtpTransport::AUTH_DIGEST_MD5, eZSMTPTransport::authMethod( 'DIGEST-MD5' ) );
        $this->assertSame( ezcMailSmtpTransport::AUTH_XOAUTH2, eZSMTPTransport::authMethod( 'XOAUTH2' ) );
        $this->assertSame( ezcMailSmtpTransport::AUTH_NTLM, eZSMTPTransport::authMethod( 'NTLM' ) );
        $this->assertNull( eZSMTPTransport::authMethod( '' ) );
        $this->assertNull( eZSMTPTransport::authMethod( null ) );
        $this->assertNull( eZSMTPTransport::authMethod( array( 'LOGIN' ) ) );
        $this->assertNull( eZSMTPTransport::authMethod( 'KERBEROS' ) );
    }

    /** ST-04 */
    public function testCredentialsInClearToARemoteHost()
    {
        $this->assertTrue( eZSMTPTransport::sendsCredentialsInClear( 'smtp.example.com', '', 'u', 'p' ) );
        $this->assertTrue( eZSMTPTransport::sendsCredentialsInClear( '192.0.2.10', 'tcp', 'u', 'p' ) );
        $this->assertFalse( eZSMTPTransport::sendsCredentialsInClear( 'smtp.example.com', 'tls', 'u', 'p' ) );
        $this->assertFalse( eZSMTPTransport::sendsCredentialsInClear( 'smtp.example.com', 'ssl', 'u', 'p' ) );
        $this->assertFalse( eZSMTPTransport::sendsCredentialsInClear( 'smtp.example.com', '', '', '' ), 'no credentials' );
        $this->assertFalse( eZSMTPTransport::sendsCredentialsInClear( 'localhost', '', 'u', 'p' ) );
        $this->assertFalse( eZSMTPTransport::sendsCredentialsInClear( '127.0.0.1', '', 'u', 'p' ) );
        $this->assertFalse( eZSMTPTransport::sendsCredentialsInClear( '127.3.4.5', '', 'u', 'p' ) );
        $this->assertFalse( eZSMTPTransport::sendsCredentialsInClear( '::1', '', 'u', 'p' ) );
        $this->assertFalse( eZSMTPTransport::sendsCredentialsInClear( '[::1]', '', 'u', 'p' ) );
    }
}
