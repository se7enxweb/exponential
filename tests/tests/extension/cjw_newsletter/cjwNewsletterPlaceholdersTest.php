<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/**
 * The placeholders of an edition ([[name]], [[first_name]] ..., #_hash_*_#): a value goes into the HTML part
 * escaped and into the text part and the subject as it is (cjw_newsletter 4.1.20). Mail is written by the file
 * transport into a directory of the test; the subscriber is nltest-*@example.invalid and removed afterwards.
 */
class cjwNewsletterPlaceholdersTest extends cjwNewsletterTestCase
{
    const NAME = '<b>Tom & "Jerry"</b>';
    const NAME_ESCAPED = '&lt;b&gt;Tom &amp; &quot;Jerry&quot;&lt;/b&gt;';

    public static function setUpBeforeClass(): void
    {
        ezpLiveInstallation::requireOrSkip();
        parent::setUpBeforeClass();
    }

    public function testHtmlPartGetsEscapedValuesAndTheTextPartRawOnes()
    {
        $values = array( '[[first_name]]' => self::NAME, '#_hash_item_#' => 'abc123' );
        $bodies = CjwNewsletterPlaceholders::replaceInBodies(
            array( 'html' => '<p>Hi [[first_name]]</p><a href="x/#_hash_item_#">', 'text' => 'Hi [[first_name]] #_hash_item_#' ), $values );
        $this->assertSame( '<p>Hi ' . self::NAME_ESCAPED . '</p><a href="x/abc123">', $bodies['html'] );
        $this->assertSame( 'Hi ' . self::NAME . ' abc123', $bodies['text'] );
        $this->assertSame( 'About ' . self::NAME, CjwNewsletterPlaceholders::replaceInSubject( 'About [[first_name]]', $values ) );
    }

    public function testBothPartsAreAlwaysPresent()
    {
        $bodies = CjwNewsletterPlaceholders::replaceInBodies( array( 'text' => 'only text' ), array( '[[name]]' => 'x' ) );
        $this->assertSame( array( 'html' => '', 'text' => 'only text' ), $bodies );
    }

    public function testNamesAreLeftOutWhenPersonalisationIsOff()
    {
        $user = $this->newSubscriber( 'phoff' );
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() - 5 );
        $item = new CjwNewsletterEditionSendItem( array( 'hash' => 'itemhash' ) );
        $off = CjwNewsletterPlaceholders::valuesForRecipient( $item, $send, 'subhash', $user, false );
        $this->assertSame( array( '#_hash_unsubscribe_#', '#_hash_configure_#', '#_hash_item_#', '#_hash_edition_#' ), array_keys( $off ) );
        $on = CjwNewsletterPlaceholders::valuesForRecipient( $item, $send, 'subhash', $user, true );
        $this->assertSame( 'Test', $on['[[first_name]]'] );
        $this->assertSame( 'subhash', $on['#_hash_unsubscribe_#'] );
    }

    public function testASentEditionCarriesTheNameEscapedInHtmlAndRawInText()
    {
        $user = $this->newSubscriber( 'phesc' );
        $user->setAttribute( 'first_name', self::NAME );
        $user->store();
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() - 5 );
        $xml = new DOMDocument();
        $xml->loadXML( $send->attribute( 'output_xml' ) );
        foreach ( $xml->getElementsByTagName( 'type' ) as $type )
        {
            if ( $type->getAttribute( 'name' ) === 'html' )
                $type->nodeValue = '<html><body><p>Hello [[first_name]]</p></body></html>';
            if ( $type->getAttribute( 'name' ) === 'text' )
                $type->nodeValue = 'Hello [[first_name]]';
        }
        $send->setAttribute( 'output_xml', $xml->saveXML() );
        $send->setAttribute( 'personalize_content', 1 );
        $send->store();
        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $this->runCronjob( 'cjw_newsletter_mailqueue_process' );

        $parts = null;
        foreach ( $this->outbox() as $file )
        {
            if ( strpos( $this->mailText( $file ), $user->attribute( 'email' ) ) === false )
                continue;
            $parts = $this->textParts( $file );
        }
        $this->assertNotNull( $parts, 'a mail to the test subscriber was written' );
        $this->assertArrayHasKey( 'html', $parts, 'the mail has an HTML part' );
        $this->assertArrayHasKey( 'plain', $parts, 'the mail has a text part' );
        $this->assertStringContainsString( 'Hello ' . self::NAME_ESCAPED, $parts['html'] );
        $this->assertStringNotContainsString( '<b>Tom', $parts['html'], 'no markup from the subscriber data in the HTML part' );
        $this->assertStringContainsString( 'Hello ' . self::NAME, $parts['plain'] );
        $this->assertStringNotContainsString( '&amp;', $parts['plain'], 'the text part is not escaped' );
    }

    /** @return array subtype (html, plain) => decoded text of a mail file */
    private function textParts( $file )
    {
        $parser = new ezcMailParser();
        $mails = $parser->parseMail( new ezcMailFileSet( array( $file ) ) );
        $this->assertCount( 1, $mails );
        $parts = array();
        $collect = function ( $part ) use ( &$collect, &$parts )
        {
            if ( $part instanceof ezcMailText )
                $parts[$part->subType] = $part->text;
            else if ( $part instanceof ezcMailMultipart )
                foreach ( $part->getParts() as $sub )
                    $collect( $sub );
        };
        $collect( $mails[0]->body );
        return $parts;
    }
}
