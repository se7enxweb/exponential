<?php
/**
 * ezpMail::generateHeaders(): the Message-Id of a mail sent by SMTP.
 *
 *  MI-01 - without a Message-Id of its own a mail gets a random one at the sender's domain, not the library's
 *          date + process id + counter (ezcMailTools::generateMessageId() wrote the PHP process id into every mail)
 *  MI-02 - each generation gets a new one, and the mail object keeps none afterwards (a mail object sent twice is
 *          two messages)
 *  MI-03 - one set with eZMail::setMessageID() stays as it is
 *  MI-04 - without a sender, or with a sender domain that is no host name, the host is localhost, as in the library
 *
 * DB-less; nothing is sent. Fake addresses use the .invalid domain.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class ezpMailMessageIdTest extends PHPUnit\Framework\TestCase
{
    private static function messageId( ezpMail $mail )
    {
        $headers = $mail->generateHeaders();
        return preg_match( '/^Message-Id: (.*)$/mi', $headers, $match ) ? rtrim( $match[1], "\r" ) : null;
    }

    private static function mail( $sender = 'site@mail.example.invalid' )
    {
        $mail = new eZMail();
        if ( $sender !== null )
            $mail->setSender( $sender );
        $mail->setReceiver( 'reader@example.invalid' );
        $mail->setSubject( 'Message-Id test' );
        $mail->setBody( 'Body' );
        return $mail;
    }

    /** MI-01 */
    public function testARandomMessageIdAtTheSenderDomain()
    {
        $id = self::messageId( self::mail()->Mail );
        $this->assertMatchesRegularExpression( '/^<[0-9a-f]{32}@mail\.example\.invalid>$/', (string)$id );
        $this->assertStringNotContainsString( '.' . getmypid() . '.', (string)$id, 'no process id' );
    }

    /** MI-02 */
    public function testEachGenerationHasItsOwn()
    {
        $mail = self::mail()->Mail;
        $first = self::messageId( $mail );
        $second = self::messageId( $mail );
        $this->assertNotSame( $first, $second );
        $this->assertNull( $mail->messageId, 'the mail object keeps none' );
    }

    /** MI-03 */
    public function testASetMessageIdStays()
    {
        $mail = self::mail();
        $mail->setMessageID( '<fixed.1@example.invalid>' );
        $this->assertSame( '<fixed.1@example.invalid>', self::messageId( $mail->Mail ) );
        $this->assertSame( '<fixed.1@example.invalid>', self::messageId( $mail->Mail ) );
    }

    /** MI-04 */
    public function testLocalhostWithoutAUsableSenderDomain()
    {
        $this->assertMatchesRegularExpression( '/^<[0-9a-f]{32}@localhost>$/', (string)self::messageId( self::mail( null )->Mail ) );
        $mail = self::mail();
        $mail->Mail->from = new ezcMailAddress( 'odd@exa mple>.invalid' );
        $this->assertMatchesRegularExpression( '/^<[0-9a-f]{32}@localhost>$/', (string)self::messageId( $mail->Mail ) );
    }
}
