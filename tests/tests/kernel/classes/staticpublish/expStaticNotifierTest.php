<?php
/**
 * The notices of static publishing (expStaticNotifier): fake senders, nothing leaves the process.
 *
 *  NT-01 the payload (exp-static-webhook/1): publish.done, publish.failed, rollback; no credential
 *  NT-02 the e-mail goes as Notify[] asks: failure, success (only a run that committed)
 *  NT-03 the webhook: https only (http to localhost), signed with the secret, its status checked
 *  NT-04 the mail text names the target, run, push and errors
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
            'email' => 'ops@example.com', 'secret' => '', 'site' => 'Demo',
            'mail' => function ( $to, $subject, $body ) use ( $self ) { $self->mails[] = array( $to, $subject, $body ); return true; },
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
        $this->assertSame( 'ops@example.com', $this->mails[0][0] );
        $this->assertSame( '[Demo] Static publishing pub: failed', $this->mails[0][1] );
        $this->mails = array();
        $success = self::target( array( 'Notify' => array( 'success' ) ) );
        $this->notifier()->notify( $success, self::report( 'unchanged' ) );
        $this->assertSame( array(), $this->mails, 'nothing committed, nothing to tell' );
        $this->notifier()->notify( $success, self::report( 'done' ) );
        $this->assertCount( 1, $this->mails );
        $this->assertSame( array( 'no address for the notice: set [StaticPublishSettings] NotifyEmail' ),
                           $this->notifier( array( 'email' => '' ) )->notify( self::target(), self::report( 'failed' ) )['problems'] );
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
        list( $subject, $body ) = expStaticNotifier::mail( expStaticNotifier::payload( self::target(), self::report( 'failed', array( 'push refused' ) ) ), '' );
        $this->assertSame( '[Exponential] Static publishing pub: failed', $subject );
        foreach ( array( 'Target:  pub (Public)', 'Run:     r1', 'Push:    pushed', 'Address: https://o.github.io/r/', '- push refused' ) as $line )
            $this->assertStringContainsString( $line, $body );
    }
}
