<?php
/**
 * site.ini [MailSettings] SendmailEnvelopeSender: whether eZSendmailTransport gives sendmail the sender with -f.
 *
 *  - enabled (the default, also when the setting is missing): "-f <sender>" as before, the mail's own sender or
 *    else EmailSender; SendmailOptions[] are handed on as before.
 *  - disabled: no -f; a mail without a sender gets a From header with EmailSender (also "Name <address>"), so an MTA
 *    that reads the envelope sender from the header (msmtp --read-envelope-from) finds one; a mail with a sender
 *    keeps it. No warning is raised on the way.
 *
 * Each case runs in a child process whose sendmail_path is a capturing script, so mail() really runs and nothing is
 * sent. No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZSendmailEnvelopeSenderTest extends PHPUnit\Framework\TestCase
{
    /** @var string the file the capturing script writes */
    private $captureFile;

    protected function setUp(): void
    {
        if ( !function_exists( 'proc_open' ) || !function_exists( 'mail' ) || PHP_BINARY === '' || DIRECTORY_SEPARATOR !== '/' )
            $this->markTestSkipped( 'needs proc_open(), mail(), the PHP binary and a sendmail_path command (not Windows)' );
        $this->captureFile = tempnam( sys_get_temp_dir(), 'ezsendmail' );
    }

    protected function tearDown(): void
    {
        if ( $this->captureFile && file_exists( $this->captureFile ) )
            unlink( $this->captureFile );
    }

    /**
     * Sends one mail in a child process and returns what the stand-in for sendmail got.
     *
     * @param string $envelopeSender the value of SendmailEnvelopeSender, '-' for none
     * @param string $sender the sender of the mail, '' for none
     * @param string $emailSender the value of EmailSender
     * @param string $extraFrom a From extra header of the mail, '' for none
     * @return array 'args' => the arguments sendmail got, 'message' => the mail it was handed, 'sender' => the
     *               sender of the mail after sending ('From' => eZMail::$From, 'from' => the address of ezcMail::$from)
     */
    private function send( $envelopeSender, $sender = '', $emailSender = 'site@example.com', $extraFrom = '' )
    {
        file_put_contents( $this->captureFile, '' );
        $fixtures = __DIR__ . '/fixtures';
        $this->assertFileExists( $fixtures . '/sendmail_capture.php' );
        $sendmail = escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $fixtures . '/sendmail_capture.php' ) . ' '
                  . escapeshellarg( $this->captureFile );
        $process = proc_open( array( PHP_BINARY, '-d', 'sendmail_path=' . $sendmail, '-d', 'display_errors=stderr',
                                     $fixtures . '/sendmail_envelope_child.php', $envelopeSender, $sender, $emailSender,
                                     $extraFrom ),
                              array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, null,
                              array_merge( getenv(), array( 'EZSENDMAIL_REPORT_SENDER' => '1' ) ) );
        $this->assertIsResource( $process );
        $output = stream_get_contents( $pipes[1] );
        $errors = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        proc_close( $process );
        $lines = explode( "\n", $output, 2 );
        $this->assertSame( 'sent', $lines[0], $output . $errors );
        $this->assertSame( '', $errors, 'no warning or notice' );
        $captured = json_decode( (string)file_get_contents( $this->captureFile ), true );
        $this->assertIsArray( $captured, 'sendmail was called' );
        $captured['sender'] = json_decode( isset( $lines[1] ) ? $lines[1] : '', true );
        $this->assertIsArray( $captured['sender'], 'the child reported the sender' );
        return $captured;
    }

    /**
     * The From header of $message, or null.
     *
     * @param string $message
     * @return string|null
     */
    private static function fromHeader( $message )
    {
        list( $headers ) = preg_split( '/\r?\n\r?\n/', $message, 2 );
        return preg_match( '/^From: ?(.*)$/mi', $headers, $match ) ? rtrim( $match[1], "\r" ) : null;
    }

    public function testEnabledGivesTheSenderWithDashF()
    {
        $args = $this->send( 'enabled' )['args'];
        $this->assertContains( '-fsite@example.com', $args );
        $this->assertSame( array( '-r', 'bounce@example.com' ), array_values( array_slice( $args, array_search( '-r', $args, true ), 2 ) ),
                           'SendmailOptions[] are handed on' );
        $this->assertContains( '-feditor@example.com', $this->send( 'enabled', 'editor@example.com' )['args'] );
        $this->assertContains( '-fsite@example.com', $this->send( '-' )['args'], 'a missing setting means enabled' );
    }

    public function testDisabledLeavesOutDashFAndWritesAFromHeader()
    {
        $captured = $this->send( 'disabled' );
        $this->assertSame( array(), preg_grep( '/^-f/', $captured['args'] ) );
        $this->assertContains( '-r', $captured['args'] );
        $this->assertStringContainsString( 'site@example.com', (string)self::fromHeader( $captured['message'] ) );

        $captured = $this->send( 'disabled', '', 'Portal <portal@example.com>' );
        $from = (string)self::fromHeader( $captured['message'] );
        $this->assertStringContainsString( 'portal@example.com', $from );
        $this->assertStringContainsString( 'Portal', $from );
    }

    public function testDisabledKeepsTheSenderOfTheMail()
    {
        $captured = $this->send( 'disabled', 'editor@example.com' );
        $this->assertSame( array(), preg_grep( '/^-f/', $captured['args'] ) );
        $from = (string)self::fromHeader( $captured['message'] );
        $this->assertStringContainsString( 'editor@example.com', $from );
        $this->assertStringNotContainsString( 'site@example.com', $from );
    }

    /**
     * A mail that carries its From as an extra header (eZMail::addExtraHeader( 'From', ... )) keeps it: eZMail writes
     * an extra header only when the mail has no sender, so setting EmailSender as the sender replaced it.
     */
    public function testDisabledKeepsAFromExtraHeader()
    {
        $captured = $this->send( 'disabled', '', 'site@example.com', 'Shop <shop@example.com>' );
        $this->assertSame( array(), preg_grep( '/^-f/', $captured['args'] ) );
        $this->assertSame( 'Shop <shop@example.com>', self::fromHeader( $captured['message'] ) );
        $this->assertSame( 1, preg_match_all( '/^From:/mi', preg_split( '/\r?\n\r?\n/', $captured['message'], 2 )[0] ),
                           'one From header' );
    }

    /**
     * The From header is given for the headers of this call only: the caller's mail has no sender afterwards, as
     * before, and can be sent again (with other settings) without carrying EmailSender along.
     */
    public function testDisabledLeavesTheMailOfTheCallerAsItWas()
    {
        $captured = $this->send( 'disabled' );
        $this->assertStringContainsString( 'site@example.com', (string)self::fromHeader( $captured['message'] ) );
        $this->assertSame( array( 'From' => false, 'from' => null ), $captured['sender'] );

        $captured = $this->send( 'disabled', 'editor@example.com' );
        $this->assertSame( array( 'From' => array( 'name' => 'Editor', 'email' => 'editor@example.com' ),
                                  'from' => 'editor@example.com' ), $captured['sender'] );
    }

    /**
     * EmailSender or AdminEmail with a line break never adds a header: an address with one is not used, and a name
     * with one is written on one line.
     */
    public function testDisabledNeverAddsAHeaderFromTheSettings()
    {
        $captured = $this->send( 'disabled', '', "site@example.com\r\nBcc: spy@example.com" );
        $headers = preg_split( '/\r?\n\r?\n/', $captured['message'], 2 )[0];
        $this->assertStringNotContainsString( 'spy@example.com', $headers );
        $this->assertStringContainsString( 'admin@example.com', (string)self::fromHeader( $captured['message'] ),
                                           'AdminEmail, since EmailSender is not a valid address' );

        $captured = $this->send( 'disabled', '', "Site\r\nBcc: spy@example.com <site@example.com>" );
        $headers = preg_split( '/\r?\n\r?\n/', $captured['message'], 2 )[0];
        $this->assertDoesNotMatchRegularExpression( '/^Bcc:/mi', $headers );
        $this->assertSame( 1, preg_match_all( '/^From:/mi', $headers ) );
        $this->assertSame( array(), preg_grep( '/^-f|spy/', $captured['args'] ) );
    }

    public function testAnyOtherValueKeepsDashF()
    {
        $this->assertContains( '-fsite@example.com', $this->send( '' )['args'] );
        $this->assertContains( '-fsite@example.com', $this->send( 'no' )['args'] );
    }
}
