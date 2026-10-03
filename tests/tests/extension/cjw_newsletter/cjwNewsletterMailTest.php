<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/** CjwNewsletterMail, the transports (file and failures), the INI-driven settings. */
class cjwNewsletterMailTest extends cjwNewsletterTestCase
{
    private function send( $mail, $to, $subject = 'Hello', array $body = array( 'text' => 'plain body', 'html' => '<p>html body</p>' ), $method = 'Directly' )
    {
        $setter = 'setTransportMethod' . $method . 'FromIni';
        $mail->$setter();
        return $mail->sendEmail( 'sender@example.com', 'Sender Name', $to, 'Receiver Name', $subject, $body, false, 'utf-8', false, false );
    }

    public function testFileTransportWritesOneEmlPerMailWithTheRecipientInTheName()
    {
        $to = $this->newEmail( 'eml' );
        $result = $this->send( new CjwNewsletterMail(), $to );
        $this->assertTrue( $result['send_result'] );
        $this->assertSame( 'multipart/alternative', $result['email_content_type'] );
        $files = $this->outbox();
        $this->assertCount( 1, $files );
        $this->assertStringEndsWith( '.eml', $files[0] );
        $this->assertStringContainsString( $to, basename( $files[0] ) );
    }

    public function testManyMailsInTheSameSecondDoNotOverwriteEachOther()
    {
        $mail = new CjwNewsletterMail();
        for ( $i = 0; $i < 12; $i++ )
            $this->send( $mail, $this->newEmail( 'bulk' ) );
        $this->assertCount( 12, $this->outbox() );
    }

    public function testMailHeadersAndBody()
    {
        $to = $this->newEmail( 'hdr' );
        $this->send( new CjwNewsletterMail(), $to, 'Ümlaut subject äöü' );
        $text = $this->mailText( $this->outbox()[0] );
        $this->assertMatchesRegularExpression( '/^From: .*<sender@example.com>/m', $text );
        $this->assertMatchesRegularExpression( '/^To: .*<' . preg_quote( $to, '/' ) . '>/m', $text );
        $this->assertMatchesRegularExpression( '/^Subject: /m', $text );
        $this->assertMatchesRegularExpression( '/^Return-Path: <sender@example.com>/m', $text );
        $this->assertMatchesRegularExpression( '/^Reply-To: /m', $text );
        $this->assertMatchesRegularExpression( '/^MIME-Version: 1.0/m', $text );
        $this->assertStringContainsString( 'plain body', $text );
        $this->assertStringContainsString( '<p>html body</p>', $text );
        $this->assertStringContainsString( 'Content-Type: multipart/alternative', $text );
    }

    public function testContentTypeFollowsTheBodiesGiven()
    {
        $this->assertSame( 'text/plain', $this->send( new CjwNewsletterMail(), $this->newEmail( 'a' ), 's', array( 'text' => 'only text' ) )['email_content_type'] );
        $this->assertSame( 'text/html', $this->send( new CjwNewsletterMail(), $this->newEmail( 'b' ), 's', array( 'html' => '<b>only html</b>' ) )['email_content_type'] );
    }

    public function testExtraHeadersCarryTheIdentifiersABounceNeeds()
    {
        $user = $this->newSubscriber( 'xh' );
        $mail = new CjwNewsletterMail();
        $mail->setExtraMailHeadersByNewsletterUser( $user );
        $this->send( $mail, $user->attribute( 'email' ) );
        $text = $this->mailText( $this->outbox()[0] );
        $this->assertMatchesRegularExpression( '/^x-cjwnl-user: ' . $user->attribute( 'hash' ) . '/mi', $text );
        $this->assertMatchesRegularExpression( '/^x-cjwnl-receiver: /mi', $text );
        $this->assertMatchesRegularExpression( '/^x-cjwnl-version: ' . preg_quote( cjw_newsletterInfo::SOFTWARE_VERSION, '/' ) . '/mi', $text );
        $this->assertFalse( $mail->setExtraMailHeadersByNewsletterUser( 'not a user' ) );
        $this->assertFalse( $mail->setExtraMailHeadersByNewsletterSendItem( null ) );
    }

    public function testResetExtraHeadersClearsTheOnesOfThePreviousMail()
    {
        $user = $this->newSubscriber( 'reset' );
        $mail = new CjwNewsletterMail();
        $mail->setExtraMailHeadersByNewsletterUser( $user );
        $mail->resetExtraMailHeaders();
        $this->send( $mail, $this->newEmail( 'plain' ) );
        $text = $this->mailText( $this->outbox()[0] );
        $this->assertDoesNotMatchRegularExpression( '/x-cjwnl-user/i', $text, 'the user header of the earlier mail is gone' );
        $this->assertMatchesRegularExpression( '/x-cjwnl-version/i', $text );
    }

    public function testEveryTransferEncodingProducesAMail()
    {
        foreach ( array( '7bit', '8bit', 'quoted-printable', 'base64', 'binary', 'nonsense' ) as $encoding )
        {
            $this->setIni( 'cjw_newsletter.ini', 'NewsletterMailSettings', 'ContentTransferEncoding', $encoding );
            $result = $this->send( new CjwNewsletterMail(), $this->newEmail( 'enc' ), 'Subject', array( 'text' => 'Zeile äöü' ) );
            $this->assertTrue( $result['send_result'], $encoding );
        }
        $this->assertCount( 6, $this->outbox() );
    }

    public function testEveryHeaderLineEndingProducesAMail()
    {
        foreach ( array( 'LF', 'CRLF', 'CR', 'auto', 'nonsense' ) as $ending )
        {
            $this->setIni( 'cjw_newsletter.ini', 'NewsletterMailSettings', 'HeaderLineEnding', $ending );
            $this->assertTrue( $this->send( new CjwNewsletterMail(), $this->newEmail( 'le' ) )['send_result'], $ending );
        }
    }

    public function testTransportMethodsComeFromTheIni()
    {
        $this->setIni( 'cjw_newsletter.ini', 'NewsletterMailSettings', 'TransportMethodCronjob', 'smtp' );
        $this->setIni( 'cjw_newsletter.ini', 'NewsletterMailSettings', 'TransportMethodPreview', 'sendmail' );
        $this->setIni( 'cjw_newsletter.ini', 'NewsletterMailSettings', 'TransportMethodDirectly', 'file' );
        $mail = new CjwNewsletterMail();
        $this->assertSame( 'smtp', $mail->setTransportMethodCronjobFromIni() );
        $this->assertSame( 'sendmail', $mail->setTransportMethodPreviewFromIni() );
        $this->assertSame( 'file', $mail->setTransportMethodDirectlyFromIni() );
    }

    public function testUnwritableMailDirectoryIsAFailedSendNotAFatal()
    {
        $this->setIni( 'cjw_newsletter.ini', 'NewsletterMailSettings', 'FileTransportMailDir', '/proc/nltest-no-such-dir' );
        $result = $this->send( new CjwNewsletterMail(), $this->newEmail( 'fail' ) );
        $this->assertNotTrue( $result['send_result'] );
        $this->assertInstanceOf( 'Exception', $result['send_result'] );
        $this->assertArrayHasKey( 'send_error', $result );
    }

    public function testUnknownTransportMethodIsAFailedSend()
    {
        $transport = new CjwNewsletterTransport( 'file' );
        $this->expectException( ezcBaseValueException::class );
        $transport->transportMethod = 'carrier pigeon';
    }

    public function testTransportAcceptsTheDocumentedMethods()
    {
        foreach ( array( 'file', 'smtp', 'sendmail', 'mta' ) as $method )
        {
            $transport = new CjwNewsletterTransport( $method );
            $this->assertSame( $method, $transport->transportMethod );
            $this->assertTrue( isset( $transport->transportMethod ) );
        }
        $this->assertFalse( isset( $transport->nothing ) );
    }

    public function testTransportRefusesUnknownProperties()
    {
        $transport = new CjwNewsletterTransport( 'file' );
        $this->expectException( ezcBasePropertyNotFoundException::class );
        $transport->nothing;
    }

    public function testSmtpToAClosedPortFailsCleanlyAndIsReported()
    {
        // 127.0.0.1:1 refuses at once; the transport turns that into a result, the sender never throws
        $this->setIni( 'cjw_newsletter.ini', 'NewsletterMailSettings', 'SmtpTransportServer', '127.0.0.1' );
        $this->setIni( 'cjw_newsletter.ini', 'NewsletterMailSettings', 'SmtpTransportPort', '1' );
        $this->setIni( 'cjw_newsletter.ini', 'NewsletterMailSettings', 'TransportMethodDirectly', 'smtp' );
        $result = $this->send( new CjwNewsletterMail(), $this->newEmail( 'smtp' ) );
        $this->assertInstanceOf( 'Exception', $result['send_result'] );
        $this->assertCount( 0, $this->outbox() );
    }

    public function testFileTransportDirectoryIsRelativeToTheInstallation()
    {
        $this->assertSame( rtrim( getcwd(), '/' ) . '/var/log/mail', CjwNewsletterTransportFile::resolveMailDir( 'var/log/mail/' ) );
        $this->assertSame( '/absolute/path', CjwNewsletterTransportFile::resolveMailDir( '/absolute/path' ) );
        $this->assertSame( rtrim( getcwd(), '/' ) . '/var/log/mail', CjwNewsletterTransportFile::resolveMailDir( '' ) );
    }

    public function testFileTransportRefusesAMailWithoutRecipient()
    {
        $mail = new ezcMailComposer();
        $mail->from = new ezcMailAddress( 'a@example.com', 'A' );
        $mail->subject = 's';
        $mail->plainText = 't';
        $mail->build();
        $transport = new CjwNewsletterTransportFile( $this->mailDir );
        $this->expectException( ezcMailTransportException::class );
        $transport->send( $mail );
    }

    public function testPreviewMailGoesToEveryAddressOfTheList()
    {
        $a = $this->newEmail( 'p1' );
        $b = $this->newEmail( 'p2' );
        $mail = new CjwNewsletterMail();
        $mail->setTransportMethodPreviewFromIni();
        $result = $mail->sendEmail( 'sender@example.com', 'S', $a . ';' . $b, 'T', 'Preview', array( 'text' => 'x' ), true );
        $this->assertTrue( $result['send_result'] );
        $text = $this->mailText( $this->outbox()[0] );
        $this->assertStringContainsString( $a, $text );
        $this->assertStringContainsString( $b, $text );
    }

    public function testSendEmailWithEzReportsItsResultWithoutNotices()
    {
        $mail = new CjwNewsletterMail();
        $result = $mail->sendEmailWithEz( 'sender@example.com', $this->newEmail( 'ez' ), 'Subject', 'Body' );
        $this->assertArrayHasKey( 'send_result', $result );
        $this->assertIsBool( $result['send_result'] );
    }

    public function testIniSettingsAreWellFormed()
    {
        $ini = eZINI::instance( 'cjw_newsletter.ini' );
        foreach ( array( 'TransportMethodCronjob', 'TransportMethodPreview', 'TransportMethodDirectly' ) as $name )
            $this->assertContains( $ini->variable( 'NewsletterMailSettings', $name ), array( 'file', 'smtp', 'sendmail', 'mta' ), $name );
        $this->assertGreaterThan( 0, (int)$ini->variable( 'BounceSettings', 'BounceThresholdValue' ) );
        $this->assertContains( $ini->variable( 'NewsletterMailSettings', 'ContentTransferEncoding' ), array( '7bit', '8bit', 'binary', 'quoted-printable', 'base64' ) );
        $this->assertNotEmpty( eZMail::validate( $ini->variable( 'NewsletterMailSettings', 'EmailSender' ) ) );
        $this->assertSame( 18828, (int)$ini->variable( 'NewsletterSettings', 'RootFolderNodeId' ) );
        $this->assertNotNull( eZContentObjectTreeNode::fetch( (int)$ini->variable( 'NewsletterSettings', 'RootFolderNodeId' ) ) );
        foreach ( $ini->variable( 'NewsletterFilterSettings', 'AvailableFilterTypeClassArray' ) as $class )
            $this->assertTrue( class_exists( $class ), $class );
    }

    public function testFilterRegistryLoadsEveryConfiguredFilterType()
    {
        $filter = new CjwNewsletterFilter();
        $types = $filter->getFilterTypesAvailable();
        $this->assertArrayHasKey( 'cjwnl_email', $types );
        $this->assertArrayHasKey( 'cjwnl_salutation', $types );
        $this->assertTrue( $filter->addFilter( 'cjwnl_email', 'like', array( 'a' ) ) );
        $this->assertFalse( $filter->addFilter( 'nonsense' ) );
        $this->assertCount( 1, $filter->getFilterTypesActive() );
        $this->assertTrue( $filter->removeFilterByIndex( 0 ) );
        $this->assertFalse( $filter->removeFilterByIndex( 7 ) );
    }
}
