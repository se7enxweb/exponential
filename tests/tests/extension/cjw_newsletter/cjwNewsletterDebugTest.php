<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/** The mail a send writes is a well-formed MIME message that a mail reader (here: the zeta parser) reads back. */
class cjwNewsletterDebugTest extends cjwNewsletterTestCase
{
    public function testSentMailParsesBackWithBothPartsAndTheBounceHeaders()
    {
        $user = $this->newSubscriber( 'mime' );
        $edition = $this->newEdition();
        $this->editionContent( $edition )->createNewsletterSendObject( time() - 60 );
        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $this->runCronjob( 'cjw_newsletter_mailqueue_process' );
        $file = null;
        foreach ( $this->outbox() as $candidate )
            if ( strpos( $this->mailText( $candidate ), $user->attribute( 'email' ) ) !== false )
                $file = $candidate;
        $this->assertNotNull( $file );
        $parser = new ezcMailParser();
        $mails = $parser->parseMail( new ezcMailFileSet( array( $file ) ) );
        $this->assertCount( 1, $mails );
        $mail = $mails[0];
        $this->assertSame( $user->attribute( 'email' ), $mail->to[0]->email );
        $this->assertStringContainsString( 'NLTEST', $mail->subject );
        $this->assertInstanceOf( 'ezcMailMultipartAlternative', $mail->body );
        $parts = $mail->body->getParts();
        $this->assertCount( 2, $parts );
        $this->assertSame( 'plain', $parts[0]->subType );
        $this->assertSame( 'html', $parts[1]->subType );
        $this->assertSame( $user->attribute( 'hash' ), $mail->getHeader( 'x-cjwnl-user' ) );
        $this->assertNotSame( '', (string)$mail->getHeader( 'x-cjwnl-senditem' ) );
    }
}
