<?php
/**
 * expMailTemplateBody::apply(): the body of a mail made from a template.
 *
 *  MB-01 - content_type text/html and body_text: a multipart/alternative mail with a text part and an HTML part, in
 *          the raw body (sendmail, file) and in the ezcMail object (SMTP)
 *  MB-02 - only content_type: that type, the output as the body
 *  MB-03 - neither: the output as the body, the content type left as it was
 *  MB-04 - an HTML content_type with an empty body_text is not multipart
 *
 * DB-less: a template object with variables set by hand; nothing is sent. Fake addresses use the .invalid domain.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/mail/expMailTemplateBodyTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expMailTemplateBodyTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
    }

    private static function template( array $variables )
    {
        $tpl = eZTemplate::factory();
        $tpl->resetVariables();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        return $tpl;
    }

    private static function mail()
    {
        $mail = new eZMail();
        $mail->setReceiver( 'mb-test@example.invalid' );
        $mail->setSubject( 'MB test' );
        return $mail;
    }

    /** MB-01 */
    public function testHtmlWithTextPartIsMultipartAlternative()
    {
        $html = '<!DOCTYPE html><html><body><p>Hello &amp; welcome</p></body></html>';
        $tpl = self::template( array( 'content_type' => 'text/html', 'body_text' => "  Hello & welcome\n\n" ) );
        $mail = self::mail();
        try
        {
            $this->assertTrue( expMailTemplateBody::apply( $mail, $tpl, $html ) );
        }
        finally
        {
            $tpl->resetVariables();
        }
        $this->assertSame( 'multipart/alternative', $mail->contentType() );
        $body = $mail->body( false );
        $this->assertStringContainsString( 'Content-Type: text/plain; charset=utf-8', $body );
        $this->assertStringContainsString( 'Content-Type: text/html; charset=utf-8', $body );
        $this->assertStringContainsString( "Hello & welcome\n", $body );
        $this->assertStringContainsString( $html, $body );
        $this->assertLessThan( strpos( $body, 'text/html' ), strpos( $body, 'text/plain' ), 'the text part comes first' );
        $this->assertMatchesRegularExpression( '/--exp-alt-[0-9a-f]{24}--\n$/', $body, 'the closing boundary' );
        if ( class_exists( 'ezcMailMultipartAlternative' ) )
            $this->assertInstanceOf( 'ezcMailMultipartAlternative', $mail->Mail->body );
    }

    /** MB-02 */
    public function testOnlyContentTypeKeepsTheOutput()
    {
        $tpl = self::template( array( 'content_type' => 'text/html' ) );
        $mail = self::mail();
        try
        {
            $this->assertFalse( expMailTemplateBody::apply( $mail, $tpl, '<p>only html</p>' ) );
        }
        finally
        {
            $tpl->resetVariables();
        }
        $this->assertSame( 'text/html', $mail->contentType() );
        $this->assertSame( '<p>only html</p>', $mail->body( false ) );
    }

    /** MB-03 */
    public function testNeitherKeepsTheContentType()
    {
        $tpl = self::template( array() );
        $mail = self::mail();
        $mail->setContentType( 'text/plain' );
        try
        {
            $this->assertFalse( expMailTemplateBody::apply( $mail, $tpl, "plain text\n" ) );
        }
        finally
        {
            $tpl->resetVariables();
        }
        $this->assertSame( 'text/plain', $mail->contentType() );
        $this->assertSame( "plain text\n", $mail->body( false ) );
    }

    /** MB-04 */
    public function testEmptyTextPartIsNotMultipart()
    {
        $tpl = self::template( array( 'content_type' => 'text/html', 'body_text' => "  \n" ) );
        $mail = self::mail();
        try
        {
            $this->assertFalse( expMailTemplateBody::apply( $mail, $tpl, '<p>x</p>' ) );
        }
        finally
        {
            $tpl->resetVariables();
        }
        $this->assertSame( 'text/html', $mail->contentType() );
        $this->assertSame( '<p>x</p>', $mail->body( false ) );
    }
}
