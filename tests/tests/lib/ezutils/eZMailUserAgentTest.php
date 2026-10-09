<?php
/**
 * site.ini [MailSettings] UserAgent: one User-Agent for the mails of the whole site.
 *
 *  - Without the setting a mail says "Exponential, Version ..." (sendmail, file) as before, and a mail sent by SMTP
 *    keeps the default of ezcMail.
 *  - With it, eZMail::userAgent() and the headers of the sendmail and file transports carry it, and so do the headers
 *    ezcMail generates for SMTP.
 *  - setUserAgent() now reaches SMTP mails too; ezcMail used to overwrite it.
 *  - Line breaks in the setting do not start a new header; a value that is nothing but control characters, or an
 *    empty setUserAgent(), leaves the SMTP default in place.
 *  - setUserAgent() still works when an extension put a plain ezcMail into eZMail::$Mail.
 *
 * No database, no mail is sent.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZMailUserAgentTest extends PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
    }

    /**
     * The value of the User-Agent header in $headers, or null.
     *
     * @param string $headers
     * @return string|null
     */
    private static function userAgentIn( $headers )
    {
        return preg_match( '/^User-Agent: ?(.*)$/mi', $headers, $match ) ? rtrim( $match[1], "\r" ) : null;
    }

    public function testWithoutTheSettingNothingChanges()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'UserAgent', '' );
        $mail = new eZMail();

        $this->assertStringStartsWith( 'Exponential, Version ', $mail->userAgent( false ) );
        $this->assertSame( $mail->userAgent( false ), self::userAgentIn( $mail->headerText() ) );
        $this->assertSame( 'Apache Zeta Components', self::userAgentIn( $mail->Mail->generateHeaders() ) );
    }

    public function testTheSettingNamesEveryMail()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'UserAgent', ' Example Portal ' );
        $mail = new eZMail();

        $this->assertSame( 'Example Portal', $mail->userAgent( false ) );
        // sendmail and file write headerText()
        $this->assertSame( 'Example Portal', self::userAgentIn( $mail->headerText() ) );
        // SMTP sends the headers ezcMail generates
        $this->assertSame( 'Example Portal', self::userAgentIn( $mail->Mail->generateHeaders() ) );
    }

    public function testSetUserAgentReachesSmtpMails()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'UserAgent', 'Example Portal' );
        $mail = new eZMail();
        $mail->setUserAgent( 'Newsletter' );

        $this->assertSame( 'Newsletter', self::userAgentIn( $mail->headerText() ) );
        $this->assertSame( 'Newsletter', self::userAgentIn( $mail->Mail->generateHeaders() ) );
    }

    public function testLineBreaksDoNotStartAHeader()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'UserAgent', "Portal\r\nBcc: someone@example.com" );
        $mail = new eZMail();

        $this->assertStringNotContainsString( "\n", $mail->userAgent( false ) );
        $this->assertDoesNotMatchRegularExpression( '/^Bcc:/mi', $mail->headerText() );
        $this->assertDoesNotMatchRegularExpression( '/^Bcc:/mi', $mail->Mail->generateHeaders() );
    }

    public function testLineBreaksInTheMailPropertyDoNotStartAHeader()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'UserAgent', '' );
        $mail = new eZMail();
        $mail->Mail->userAgent = "Portal\r\nBcc: someone@example.com";

        $headers = $mail->Mail->generateHeaders();
        $this->assertDoesNotMatchRegularExpression( '/^Bcc:/mi', $headers );
        $this->assertSame( 'Portal Bcc: someone@example.com', self::userAgentIn( $headers ) );
    }

    public function testEmptyValuesKeepTheSmtpDefault()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'UserAgent', "\x01\x02" );
        $mail = new eZMail();
        $this->assertSame( 'Apache Zeta Components', self::userAgentIn( $mail->Mail->generateHeaders() ) );

        $mail->setUserAgent( '' );
        $this->assertSame( 'Apache Zeta Components', self::userAgentIn( $mail->Mail->generateHeaders() ) );
    }

    public function testSetUserAgentOnAPlainEzcMail()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'UserAgent', '' );
        $mail = new eZMail();
        $mail->Mail = new ezcMail();
        $mail->setUserAgent( 'Newsletter' );

        $this->assertSame( 'Newsletter', $mail->userAgent( false ) );
        $this->assertSame( 'Newsletter', self::userAgentIn( $mail->headerText() ) );
    }
}
