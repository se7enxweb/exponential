<?php
/**
 * File containing the eZMailEzcTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

/**
 * @group ezmail
 */
class eZMailEzcTest extends ezpTestCase
{
    public $adminEmail = 'ezp-unittests-01@ez.no';
    public $adminName = 'Admin';

    public static function imapIsEnabled()
    {
        return function_exists( 'imap_open' );
    }

    protected function smtpSettings()
    {
        $ini = eZINI::instance( 'test_ezmail_plain.ini' );

        $settings = array(
            'TransportServer' => $ini->hasVariable( 'MailSettings', 'TransportServer' ) ? trim( (string)$ini->variable( 'MailSettings', 'TransportServer' ) ) : '',
            'TransportConnectionType' => $ini->hasVariable( 'MailSettings', 'TransportConnectionType' ) ? trim( (string)$ini->variable( 'MailSettings', 'TransportConnectionType' ) ) : '',
            'TransportPort' => $ini->hasVariable( 'MailSettings', 'TransportPort' ) ? (int)$ini->variable( 'MailSettings', 'TransportPort' ) : 25,
            'TransportUser' => $ini->hasVariable( 'MailSettings', 'TransportUser' ) ? trim( (string)$ini->variable( 'MailSettings', 'TransportUser' ) ) : '',
            'TransportPassword' => $ini->hasVariable( 'MailSettings', 'TransportPassword' ) ? (string)$ini->variable( 'MailSettings', 'TransportPassword' ) : '',
        );

        if ( $settings['TransportUser'] && eZMail::validate( $settings['TransportUser'] ) )
            $this->adminEmail = $settings['TransportUser'];

        return $settings;
    }

    protected function requireConfiguredSmtp( $requirePassword = true )
    {
        $settings = $this->smtpSettings();

        if ( $settings['TransportServer'] === '' )
            $this->markTestSkipped( 'SMTP test host is not configured in test_ezmail_plain.ini' );

        if ( $requirePassword && $settings['TransportPassword'] === '' )
            $this->markTestSkipped( 'SMTP test password is not configured in test_ezmail_plain.ini' );

        return $settings;
    }

    public function setUp(): void
    {
        parent::setUp();

        $settings = $this->smtpSettings();

        // Setup default settings, change these in each test when needed
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'Transport', 'SMTP' );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'TransportServer', $settings['TransportServer'] );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'TransportConnectionType', $settings['TransportConnectionType'] );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'TransportPort', $settings['TransportPort'] );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'TransportUser', $settings['TransportUser'] );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'TransportPassword', $settings['TransportPassword'] );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'AdminEmail', $this->adminEmail );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'EmailSender', $this->adminEmail );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'EmailReplyTo', $this->adminEmail );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'DebugSending', 'disabled' );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'DebugReceiverEmail', $this->adminEmail );
    }

    public function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();

        parent::tearDown();
    }

    /**
     * kernel/content/tipafriend.php
     */
    public function testTipAFriend()
    {
        $this->requireConfiguredSmtp();
        $mail = new eZMail();
        $mail->setSender( $this->adminEmail, $this->adminName );
        $mail->setReceiver( $this->adminEmail, $this->adminName );
        $mail->setSubject( __FUNCTION__ );
        $mail->setBody( __FUNCTION__ );
        $this->assertEquals( true, eZMailTransport::send( $mail ) );
    }

    public function testRegressionToEmail()
    {
        $mail = new eZMail();
        $mail->setReceiver( $this->adminEmail, $this->adminName );

        $result = $mail->receiverEmailText();
        $expected = $mail->composeEmailItems( array( array( 'email' => $this->adminEmail, 'name' => $this->adminName ) ), true, 'email', true );

        $this->assertEquals( $expected, $result );
    }

    public function testRegressionToAll()
    {
        $mail = new eZMail();
        $mail->setReceiver( $this->adminEmail, $this->adminName );

        $ezpResult = $mail->receiverText();
        $ezcResult = $mail->Mail->to;
        $ezpExpected = $mail->composeEmailItems( array( array( 'email' => $this->adminEmail, 'name' => $this->adminName ) ), true, false, true );
        $ezcExpected = array( new ezcMailAddress( $this->adminEmail, $this->adminName, $mail->usedCharset() ) );

        $this->assertEquals( $ezpExpected, $ezpResult );
        $this->assertEquals( $ezcExpected, $ezcResult );
    }

    public function testRegressionCcEmail()
    {
        $mail = new eZMail();
        $mail->addCc( $this->adminEmail, $this->adminName );

        $result = $mail->ccReceiverTextList();
        $expected = array( 'ezp-unittests-01@ez.no' );

        $this->assertEquals( $expected, $result );
    }

    public function testRegressionCcAll()
    {
        $mail = new eZMail();
        $mail->addCc( $this->adminEmail, $this->adminName );

        $ezpResult = $mail->ccElements();
        $ezcResult = $mail->Mail->cc;
        $ezpExpected =
        array( array( 'email' => $this->adminEmail, 'name' => $this->adminName ) );
        $ezcExpected = array( new ezcMailAddress( $this->adminEmail, $this->adminName, $mail->usedCharset() ) );

        $this->assertEquals( $ezpExpected, $ezpResult );
        $this->assertEquals( $ezcExpected, $ezcResult );
    }

    public function testRegressionBccEmail()
    {
        $mail = new eZMail();
        $mail->addBcc( $this->adminEmail, $this->adminName );

        $result = $mail->bccReceiverTextList();
        $expected = array( 'ezp-unittests-01@ez.no' );

        $this->assertEquals( $expected, $result );
    }

    public function testRegressionBccAll()
    {
        $mail = new eZMail();
        $mail->addBcc( $this->adminEmail, $this->adminName );

        $ezpResult = $mail->bccElements();
        $ezcResult = $mail->Mail->bcc;
        $ezpExpected =
        array( array( 'email' => $this->adminEmail, 'name' => $this->adminName ) );
        $ezcExpected = array( new ezcMailAddress( $this->adminEmail, $this->adminName, $mail->usedCharset() ) );

        $this->assertEquals( $ezpExpected, $ezpResult );
        $this->assertEquals( $ezcExpected, $ezcResult );
    }

    public function testRegressionSubject()
    {
        $mail = new eZMail();
        $mail->setSubject( __FUNCTION__ );

        $ezpResult = $mail->subject();
        $ezcResult = $mail->Mail->subject;
        $expected = __FUNCTION__;

        $this->assertEquals( $expected, $ezpResult );
        $this->assertEquals( $expected, $ezcResult );
    }

    public function testRegressionUserAgent()
    {
        $mail = new eZMail();
        $mail->setUserAgent( __FUNCTION__ );

        $ezpResult = $mail->userAgent();
        $ezcResult = $mail->Mail->getHeader( 'User-Agent' );
        $expected = __FUNCTION__;

        $this->assertEquals( $expected, $ezpResult );
        $this->assertEquals( $expected, $ezcResult );
    }

    public function testRegressionBodyString()
    {
        $mail = new eZMail();
        $mail->setBody( __FUNCTION__ );

        $ezpResult = $mail->body();
        $ezcResult = $mail->Mail->body;
        $ezpExpected = __FUNCTION__;
        $ezcExpected = new ezcMailText( __FUNCTION__, 'utf-8' );

        $this->assertEquals( $ezpExpected, $ezpResult );
        $this->assertEquals( $ezcExpected, $ezcResult );
    }

    public function testRegressionWrongPasswordCatchException()
    {
        $settings = $this->requireConfiguredSmtp();
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'TransportPassword', 'wrong password' );
        $mail = new eZMail();
        $mail->setSender( $this->adminEmail, $this->adminName );
        $mail->setReceiver( $this->adminEmail, $this->adminName );
        $mail->setSubject( __FUNCTION__ );
        $mail->setBody( __FUNCTION__ );

        // catching the exception of wrong password and turning it into return false
        $this->assertEquals( false, eZMailTransport::send( $mail ) );

        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'TransportPassword', $settings['TransportPassword'] );
    }

    /**
     * Test for issue #16401: email for confirming when anonymous is subscribing to
     * comments is in plain text, but with html tags
     */
    public function testRegressionSetContentType()
    {
        $mail = new eZMail();
        $mail->setBody( __FUNCTION__ );
        $mail->setContentType( "text/html" );

        $ezcResult = $mail->Mail->generate();

        preg_match( "/Content-Type: text\/html/", $ezcResult, $matches );
        $this->assertEquals( 1, count( $matches ) );
    }

    /**
     * Test for issue #16893: Wrong charset encoding in notification email
     */
    public function testRegressionSetContentTypeCharset()
    {
        // Set a custom charset in site.ini which will be tested
        // if it's set properly in the sent mail
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'OutputCharset', 'custom-charset' );

        $mail = new eZMail();
        $mail->setBody( __FUNCTION__ );

        $ezcResult = $mail->Mail->generate();

        preg_match( "/Content-Type: text\/plain; charset=custom-charset/", $ezcResult, $matches );
        $this->assertEquals( 1, count( $matches ) );
    }
}

?>
