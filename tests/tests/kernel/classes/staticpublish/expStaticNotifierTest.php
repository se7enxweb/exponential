<?php
/**
 * The notices of static publishing (expStaticNotifier): fake senders, nothing leaves the process.
 *
 *  NT-01 the payload (exp-static-webhook/1): publish.done, publish.failed, rollback; no credential
 *  NT-02 the e-mail goes as Notify[] asks: failure, success (only a run that committed)
 *  NT-03 the webhook: https only (http to localhost), signed with the secret, its status checked
 *  NT-04 the mail text names the target, run, result, push and errors, and links to the run's log
 *  NT-05 the notice is made like the kernel's other notices to administrators: plain text from its template,
 *        sender EmailSender (else AdminEmail), category admin; a failed run never reads "unchanged"
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expStaticNotifierTest extends PHPUnit\Framework\TestCase
{
    private $mails = array();
    private $posts = array();
    private $status = 200;

    private function notifier( array $options = array() )
    {
        $self = $this;
        return new expStaticNotifier( $options + array(
            'email' => 'ops@example.com', 'secret' => '', 'site' => 'Demo', 'admin_url' => 'https://admin.example.com/',
            'mail' => function ( eZMail $mail ) use ( $self ) { $self->mails[] = $mail; return true; },
            'post' => function ( $url, $body, array $headers ) use ( $self ) { $self->posts[] = array( $url, $body, $headers ); return $self->status; },
        ) );
    }

    private static function target( array $settings = array() )
    {
        return new expStaticTarget( 'pub', $settings + array( 'Name' => 'Public', 'SiteAccess' => 'site', 'PublicURL' => 'https://o.github.io/r/' ) );
    }

    private static function report( $state, array $errors = array(), $mode = 'publish' )
    {
        return array( 'mode' => $mode, 'run_id' => 'r1', 'state' => $state, 'commit_id' => 'abc', 'tag_name' => 'static/pub/3', 'push_state' => 'pushed',
                      'counts' => array( 'pages' => 4 ), 'errors' => $errors, 'credential' => 'token:gh' );
    }

    /** NT-01 */
    public function testPayload()
    {
        $p = expStaticNotifier::payload( self::target(), self::report( 'done' ) );
        $this->assertSame( 'exp-static-webhook/1', $p['schema'] );
        $this->assertSame( 'publish.done', $p['event'] );
        $this->assertSame( 'static/pub/3', $p['tag'] );
        $this->assertArrayNotHasKey( 'credential', $p );
        $this->assertSame( 'publish.failed', expStaticNotifier::payload( self::target(), self::report( 'failed', array( 'x' ) ) )['event'] );
        $this->assertSame( 'rollback.done', expStaticNotifier::payload( self::target(), self::report( 'done', array(), 'rollback' ) )['event'] );
    }

    /** NT-02 */
    public function testMail()
    {
        $this->notifier()->notify( self::target(), self::report( 'done' ) );
        $this->assertSame( array(), $this->mails, 'Notify[]=failure is the default' );
        $result = $this->notifier()->notify( self::target(), self::report( 'failed', array( 'push refused' ) ) );
        $this->assertTrue( $result['mail'] );
        $this->assertSame( 'ops@example.com', $this->mails[0]->receiverText() );
        $this->assertSame( 'Static publishing of Public (pub) failed on Demo', $this->mails[0]->subject() );
        $this->mails = array();
        $success = self::target( array( 'Notify' => array( 'success' ) ) );
        $this->notifier()->notify( $success, self::report( 'unchanged' ) );
        $this->assertSame( array(), $this->mails, 'nothing committed, nothing to tell' );
        $this->notifier()->notify( $success, self::report( 'done' ) );
        $this->assertCount( 1, $this->mails );
        $this->assertSame( 'Static publishing of Public (pub) succeeded on Demo', $this->mails[0]->subject() );
        $this->assertSame( array( 'no address for the notice: set [StaticPublishSettings] NotifyEmail' ),
                           $this->notifier( array( 'email' => '' ) )->notify( self::target(), self::report( 'failed' ) )['problems'] );
        $result = $this->notifier( array( 'email' => 'not an address' ) )->notify( self::target(), self::report( 'failed' ) );
        $this->assertFalse( $result['mail'] );
        $this->assertSame( array( 'the address for the notice is not valid: [StaticPublishSettings] NotifyEmail' ), $result['problems'] );
    }

    /** NT-03 */
    public function testWebhook()
    {
        $this->assertTrue( expStaticNotifier::validWebhook( 'https://hooks.example.com/x' ) );
        $this->assertTrue( expStaticNotifier::validWebhook( 'http://localhost:9000/x' ) );
        $this->assertFalse( expStaticNotifier::validWebhook( 'http://hooks.example.com/x' ) );
        $this->assertFalse( expStaticNotifier::validWebhook( 'ftp://x' ) );
        $result = $this->notifier( array( 'secret' => 's3cret' ) )->notify( self::target( array( 'Webhook' => 'https://hooks.example.com/x' ) ), self::report( 'done' ) );
        $this->assertSame( 200, $result['webhook'] );
        list( $url, $body, $headers ) = $this->posts[0];
        $this->assertSame( 'https://hooks.example.com/x', $url );
        $this->assertSame( 'publish.done', json_decode( $body, true )['event'] );
        $this->assertSame( 'sha256=' . hash_hmac( 'sha256', $body, 's3cret' ), $headers['X-Exp-Static-Signature'] );
        $this->assertStringNotContainsString( 's3cret', $body );
        $this->status = 500;
        $this->posts = array();
        $result = $this->notifier()->notify( self::target( array( 'Webhook' => 'https://hooks.example.com/x' ) ), self::report( 'done' ) );
        $this->assertSame( array( 'the webhook answered 500' ), $result['problems'] );
        $this->assertArrayNotHasKey( 'X-Exp-Static-Signature', $this->posts[0][2] );
        $result = $this->notifier()->notify( self::target( array( 'Webhook' => 'http://hooks.example.com/x' ) ), self::report( 'done' ) );
        $this->assertNull( $result['webhook'] );
    }

    /** NT-04 */
    public function testMailText()
    {
        $payload = expStaticNotifier::payload( self::target(), self::report( 'failed', array( 'push refused' ) ) );
        list( $subject, $body ) = expStaticNotifier::mail( $payload, 'Demo', 'https://admin.example.com/staticpublish/log/r1' );
        $this->assertSame( 'Static publishing of Public (pub) failed on Demo', $subject );
        foreach ( array( 'Target: Public (pub)', 'Id: r1', 'Result: failed', 'Push: pushed', 'Public address: https://o.github.io/r/',
                         "Errors:\n- push refused", 'Log: https://admin.example.com/staticpublish/log/r1',
                         'This mail was sent by Demo because the static publishing target pub asks for it.' ) as $line )
            $this->assertStringContainsString( $line, $body );
        $this->assertSame( 'https://admin.example.com/staticpublish/log/r%201', expStaticNotifier::logURL( 'r 1', 'https://admin.example.com/' ) );
        $this->assertSame( '', expStaticNotifier::logURL( 'r1', '' ) );
    }

    /** NT-05 */
    public function testNoticeFollowsTheKernelMailModel()
    {
        $this->notifier()->notify( self::target(), self::report( 'unchanged', array( 'not pushed: the secret github_pages_token is not set' ) ) );
        $this->assertCount( 1, $this->mails );
        $mail = $this->mails[0];
        $this->assertSame( 'admin', $mail->category() );
        $this->assertSame( 'Static publishing of Public (pub) failed on Demo', $mail->subject() );
        $ini = eZINI::instance();
        $sender = $ini->variable( 'MailSettings', 'EmailSender' ) ?: $ini->variable( 'MailSettings', 'AdminEmail' );
        $this->assertSame( (string)$sender, $mail->senderText() );
        $this->assertStringNotContainsStringIgnoringCase( 'html', (string)$mail->contentType() );
        $body = (string)$mail->body();
        $this->assertStringNotContainsString( '<', $body, 'plain text, no HTML' );
        foreach ( array( 'Static publishing of Public (pub) failed on Demo', 'Target: Public (pub)', 'Id: r1', 'Result: failed',
                         'Changes: nothing changed', 'Push: pushed', "Errors:\n- not pushed: the secret github_pages_token is not set",
                         'Log: https://admin.example.com/staticpublish/log/r1',
                         'This mail was sent by Demo because the static publishing target pub asks for it.' ) as $line )
            $this->assertStringContainsString( $line, $body );
        $this->assertStringNotContainsString( 'unchanged', $body, 'a failed run reads "failed", never "unchanged"' );
        // the template is the source of the text, the built-in text only its fallback: both say the same
        list( , $fallback ) = expStaticNotifier::mail( expStaticNotifier::payload( self::target(), self::report( 'unchanged', array( 'x' ) ) ), 'Demo', 'https://admin.example.com/staticpublish/log/r1' );
        $this->assertStringContainsString( 'Result: failed', $fallback );
        $this->assertStringContainsString( 'Changes: nothing changed', $fallback );
    }
}
