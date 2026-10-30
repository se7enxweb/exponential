<?php
/**
 * Every mail of the kernel is an HTML mail with a plain-text part in the shared layout (design:mail/layout.tpl and
 * design:mail/layout_text.tpl): the account, security, e-mail preference, tip a friend, collected information and
 * order mails made from templates, and the notifications and audit alerts made from text.
 *
 *  ML-01 - each mail template sets a subject, content_type text/html and body_text; the output is the layout
 *  ML-02 - the text part carries no markup, the HTML part carries the layout's table and footer mark
 *  ML-03 - a value typed by a visitor or a user (a name, a login, a comment) is escaped in the HTML part
 *  ML-04 - the mail templates carry no styles and no markup of their own (the layout does), and never the old name
 *  ML-05 - links are absolute https; the button's address is also given as text (copy it when a button fails)
 *  ML-06 - the forgotten-password mails: the reset link with its lifetime, then the new password as the code
 *  ML-07 - expMailTemplateBody::fromText(): the text stays the text part, the HTML part has the paragraphs, the
 *          first address on a line of its own as the button, the rest below it as small print, all escaped
 *  ML-08 - expMailTemplateBody::applyRendered(): multipart/alternative only with an HTML type and a text part
 *  ML-09 - the mail gate puts the footer of an optional mail into both parts of a multipart/alternative body
 *  ML-10 - the audit alert: its "Label: value" lines are a table, the console link the button, values escaped
 *  ML-12 - a notification (text) is sent as text and HTML, as text only with notification.ini HTMLLayout=disabled
 *  ML-11 - the layout: the code (mail_code) large and selectable, the preheader hidden, dark colors only in the
 *          media query, mail_rows marked washed printed as they are
 *
 * DB-less: templates are rendered as the kernel does (eZTemplate::factory(), fetch), uncompiled, with hashes standing
 * in for users, orders and collections; nothing is sent. Fake addresses use the .invalid domain.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/mail/expMailLayoutMailsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

/** Keeps the mails it is given; never sends */
class expMailLayoutMailsTestTransport extends eZMailTransport
{
    public static $sent = array();

    function sendMail( eZMail $mail )
    {
        static::$sent[] = $mail;
        return true;
    }
}

class expMailLayoutMailsTest extends PHPUnit\Framework\TestCase
{
    const EVIL = '<script>alert(1)</script>"x';
    const EVIL_HTML = '&lt;script&gt;alert(1)&lt;/script&gt;';

    private $savedGlobals = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics', 'eZCurrentAccess' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
        $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        if ( !isset( $GLOBALS['eZCurrentAccess']['name'] ) )
            $GLOBALS['eZCurrentAccess'] = array( 'name' => eZINI::instance()->variable( 'SiteSettings', 'DefaultAccess' ),
                                                 'type' => eZSiteAccess::TYPE_DEFAULT );
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
    }

    /** The exception handler in place, without changing it */
    private static function exceptionHandler()
    {
        $current = set_exception_handler( null );
        restore_exception_handler();
        return $current;
    }

    private static function user( $login = 'ml-test' )
    {
        return array( 'login' => $login, 'email' => 'ml-test@example.invalid', 'contentobject_id' => 7 );
    }

    /**
     * @return array html, text, subject, content_type
     */
    private function render( $template, array $variables )
    {
        $tpl = eZTemplate::factory();
        $tpl->resetVariables();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $handler = self::exceptionHandler();
        try
        {
            $html = $tpl->fetch( $template );
            return array(
                'html' => $html,
                'text' => $tpl->hasVariable( 'body_text' ) ? (string)$tpl->variable( 'body_text' ) : null,
                'subject' => $tpl->hasVariable( 'subject' ) ? (string)$tpl->variable( 'subject' ) : null,
                'content_type' => $tpl->hasVariable( 'content_type' ) ? trim( (string)$tpl->variable( 'content_type' ) ) : null,
            );
        }
        finally
        {
            if ( self::exceptionHandler() !== $handler )
                restore_exception_handler();
            $tpl->resetVariables();
        }
    }

    /** The variables of a security alert (expUserSecurityAlerts::variables()) */
    private static function alert( $user, $kind )
    {
        return array( 'user' => $user, 'alert' => $kind, 'time' => 1793000000, 'ip' => '203.0.113.x', 'method' => 'totp', 'left' => 7,
                      'attempts' => 5, 'security_url' => 'https://www.example.invalid/user/security',
                      'sessions_url' => 'https://www.example.invalid/user/sessions', 'password_url' => 'https://www.example.invalid/user/forgotpassword',
                      'site_name' => 'ML Site' );
    }

    /** The mail templates with sample variables: template, variables, a text the subject contains */
    private static function cases( $login = 'ml-test', $name = 'Jane' )
    {
        $user = self::user( $login );
        $signin = array( 'user' => $user, 'device' => 'Firefox 131 on Windows 10/11', 'device_type' => 'desktop', 'ip' => '203.0.113.x',
                         'signed_in_at' => 1793000000, 'method' => 'password', 'sessions_url' => 'https://www.example.invalid/user/sessions',
                         'site_name' => 'ML Site' );
        $category = array( 'name' => 'Newsletter', 'description' => 'News, once a month.', 'identifier' => 'newsletter' );
        $collection = array( 'object' => array( 'name' => 'Contact', 'main_node_id' => 60 ), 'attributes' => array(
            array( 'contentclass_attribute_name' => 'Name', 'data_text' => $name, 'data_int' => 0, 'data_float' => 0 ),
            array( 'contentclass_attribute_name' => 'Message', 'data_text' => "Hello\nthere", 'data_int' => 0, 'data_float' => 0 ) ) );
        $order = array( 'order_nr' => 1042, 'id' => 77, 'link_url' => '/shop/orderreceipt/77', 'account_view_template' => 'ez',
            'productcollection' => array( 'currency_code' => '' ), 'account_information' => array( 'first_name' => $name, 'email' => 'ml-test@example.invalid' ),
            'product_items' => array( array( 'item_count' => 2, 'object_name' => $name, 'price_inc_vat' => 1, 'total_price_inc_vat' => 2 ) ),
            'order_items' => array(), 'product_total_inc_vat' => 2, 'total_inc_vat' => 2 );
        return array(
            'forgot link' => array( 'design/standard/templates/user/forgotpasswordmail.tpl',
                array( 'user' => $user, 'link' => true, 'hash_key' => str_repeat( 'ab', 16 ), 'key_lifetime_minutes' => 60, 'password' => '' ), 'Reset your password' ),
            'forgot password' => array( 'design/standard/templates/user/forgotpasswordmail.tpl',
                array( 'user' => $user, 'link' => false, 'hash_key' => '', 'password' => 'Ml-Secret-1' ), 'Your new password' ),
            'password changed' => array( 'design/standard/templates/user/password_changed_mail.tpl',
                array( 'user' => $user, 'changed_at' => 1793000000, 'ip' => '203.0.113.7', 'sessions_ended' => 1, 'other_sessions_signed_out' => true, 'site_name' => 'ML Site' ),
                'was changed' ),
            'new sign-in' => array( 'design/standard/templates/user/new_signin_mail.tpl', $signin, 'New sign-in' ),
            'new sign-in, new place' => array( 'design/standard/templates/user/new_signin_mail.tpl',
                $signin + array( 'location' => 'Berlin, Germany', 'new_device' => false, 'new_location' => true,
                                 'security_url' => 'https://www.example.invalid/user/security' ), 'New sign-in' ),
            'security alert 2fa off' => array( 'design/standard/templates/user/security_alert_mail.tpl', self::alert( $user, 'twofactor_off' ), 'turned off' ),
            'security alert recovery' => array( 'design/standard/templates/user/security_alert_mail.tpl', self::alert( $user, 'recovery_used' ), 'recovery code' ),
            'security alert failures' => array( 'design/standard/templates/user/security_alert_mail.tpl', self::alert( $user, 'failures' ), 'Failed sign-in' ),
            'security alert locked' => array( 'design/standard/templates/user/security_alert_mail.tpl', self::alert( $user, 'locked' ), 'locked' ),
            'new sign-in admin' => array( 'design/standard/templates/user/new_signin_admin_mail.tpl',
                $signin + array( 'admin_url' => 'https://admin.example.invalid/user/sessions/7' ), 'New sign-in of' ),
            'confirm' => array( 'design/standard/templates/mailpreferences/mail/confirm.tpl',
                array( 'category' => $category, 'email' => 'ml-test@example.invalid', 'confirm_url' => 'https://www.example.invalid/mailpreferences/confirm/t1',
                       'expires' => 1793086400, 'kind' => 'category', 'site_name' => 'ML Site' ), 'Please confirm' ),
            'email change' => array( 'design/standard/templates/mailpreferences/mail/email_change.tpl',
                array( 'email' => 'ml-new@example.invalid', 'confirm_url' => 'https://www.example.invalid/mailpreferences/confirm/t2', 'expires' => 1793086400,
                       'kind' => 'email_change', 'site_name' => 'ML Site' ), 'Confirm your new e-mail address' ),
            'link' => array( 'design/standard/templates/mailpreferences/mail/link.tpl',
                array( 'email' => 'ml-test@example.invalid', 'manage_url' => 'https://www.example.invalid/mailpreferences/manage/t3', 'expires' => 1793086400,
                       'site_name' => 'ML Site' ), 'Your link to manage e-mail' ),
            'email change notice' => array( 'design/standard/templates/mailpreferences/mail/email_change_notice.tpl',
                array( 'user' => $user, 'new_email_masked' => 'm***@example.invalid', 'site_name' => 'ML Site' ), 'is being changed' ),
            'tip a friend' => array( 'design/standard/templates/content/tipafriendmail.tpl',
                array( 'hostname' => 'www.example.invalid', 'nodename' => 'Page', 'node_id' => 61, 'node' => array( 'url_alias' => 'Page' ),
                       'your_name' => $name, 'your_email' => 'ml-friend@example.invalid', 'receivers_name' => 'R', 'receivers_email' => 'ml-test@example.invalid',
                       'comment' => $name ), null ),
            'collected information' => array( 'design/standard/templates/content/collectedinfomail.tpl',
                array( 'collection' => $collection, 'object' => $collection['object'] ), 'Collected information from' ),
            'feedback form (base)' => array( 'design/base/override/templates/collectedinfomail/feedback_form.tpl',
                array( 'collection' => $collection, 'object' => array( 'name' => 'Contact', 'data_map' => array( 'recipient' => array( 'content' => 'ml-test@example.invalid' ) ) ) ),
                'Collected information from' ),
            'order' => array( 'design/standard/templates/shop/orderemail.tpl', array( 'order' => $order ), '1042' ),
            'order (base)' => array( 'design/base/templates/shop/orderemail.tpl', array( 'order' => $order ), '1042' ),
        );
    }

    /** ML-01, ML-02 */
    public function testEveryMailTemplateMakesTheLayoutWithATextPart()
    {
        foreach ( self::cases() as $name => $case )
        {
            $mail = $this->render( $case[0], $case[1] );
            if ( $case[2] !== null )
                $this->assertStringContainsString( $case[2], (string)$mail['subject'], "$name: subject" );
            $this->assertSame( 'text/html', $mail['content_type'], "$name: content_type" );
            $this->assertNotNull( $mail['text'], "$name: body_text is set" );
            $this->assertNotSame( '', trim( $mail['text'] ), "$name: a text part" );
            $this->assertDoesNotMatchRegularExpression( '/<(table|td|p|a|div|br|h1)\b|style="/i', $mail['text'], "$name: no markup in the text part" );
            $this->assertStringContainsString( '<html', $mail['html'], $name );
            $this->assertStringContainsString( 'role="presentation"', $mail['html'], "$name: the layout" );
            $this->assertStringContainsString( expMailGate::HTML_FOOTER_MARK, $mail['html'], "$name: the footer mark" );
            $this->assertStringNotContainsString( '{$', $mail['html'], "$name: no template code left" );
        }
    }

    /** ML-03 */
    public function testTypedValuesAreEscaped()
    {
        foreach ( self::cases( self::EVIL, self::EVIL ) as $name => $case )
        {
            $mail = $this->render( $case[0], $case[1] );
            $this->assertStringNotContainsString( '<script>', $mail['html'], "$name: no raw markup of a value" );
            if ( in_array( $name, array( 'tip a friend', 'collected information', 'feedback form (base)', 'order', 'order (base)', 'forgot link',
                                         'forgot password', 'password changed', 'new sign-in', 'new sign-in admin' ), true ) )
                $this->assertStringContainsString( self::EVIL_HTML, $mail['html'], "$name: the value is shown escaped" );
        }
    }

    /** ML-04 */
    public function testTemplatesCarryNoStylesOrMarkup()
    {
        $files = array( 'design/standard/templates/content/collectedinfomail_body.tpl' );
        foreach ( self::cases() as $case )
            $files[] = $case[0];
        foreach ( array_unique( $files ) as $file )
        {
            $source = file_get_contents( $file );
            $this->assertDoesNotMatchRegularExpression( '/style\s*=|<style/i', $source, "$file has no styles" );
            $this->assertDoesNotMatchRegularExpression( '/<[a-z]/i', preg_replace( '/\{\*.*?\*\}/s', '', $source ), "$file has no markup" );
            $this->assertDoesNotMatchRegularExpression( '/ez\s*publish/i', $source, $file );
            if ( strpos( $source, 'collectedinfomail_body.tpl' ) === false )
            {
                $this->assertStringContainsString( 'design:mail/layout.tpl', $source, $file );
                $this->assertStringContainsString( 'design:mail/layout_text.tpl', $source, $file );
            }
        }
    }

    /** ML-05 */
    public function testLinksAreHttpsAndGivenAsText()
    {
        foreach ( self::cases() as $name => $case )
        {
            $mail = $this->render( $case[0], $case[1] );
            $html = str_replace( 'http://www.w3.org/', '', $mail['html'] );
            $this->assertStringNotContainsString( 'href="http://', $html, "$name: https links" );
            if ( preg_match( '#<a href="(https://[^"]+)" style="display:inline-block#', $mail['html'], $m ) )
            {
                $url = html_entity_decode( $m[1] );
                $this->assertStringContainsString( $url, $mail['text'], "$name: the button's address in the text part" );
                $this->assertGreaterThanOrEqual( 2, substr_count( $mail['html'], htmlspecialchars( $url ) ), "$name: the button's address also as text" );
            }
        }
    }

    /** ML-06 */
    public function testForgottenPasswordMails()
    {
        $cases = self::cases();
        $mail = $this->render( $cases['forgot link'][0], $cases['forgot link'][1] );
        foreach ( array( 'html', 'text' ) as $part )
        {
            $this->assertMatchesRegularExpression( '#https://[^\s"<]*user/forgotpassword/' . str_repeat( 'ab', 16 ) . '#', $mail[$part], $part );
            $this->assertStringContainsString( '60 minutes', $mail[$part], $part );
            $this->assertStringContainsString( 'ml-test@example.invalid', $mail[$part], $part );
        }
        $mail = $this->render( $cases['forgot password'][0], $cases['forgot password'][1] );
        $this->assertMatchesRegularExpression( '#font-size:32px[^>]*>Ml-Secret-1<#', $mail['html'], 'the password as the code' );
        $this->assertStringContainsString( '    Ml-Secret-1', $mail['text'] );
        $this->assertStringNotContainsString( 'Ml-Secret-1', explode( '</title>', $mail['html'] )[0], 'not in the title' );
        $this->assertDoesNotMatchRegularExpression( '#display:none[^>]*>[^<]*Ml-Secret-1#', $mail['html'], 'not in the preview text' );
    }

    private static function mail()
    {
        $mail = new eZMail();
        $mail->setSender( 'ml-sender@example.invalid' );
        $mail->setReceiver( 'ml-test@example.invalid' );
        $mail->setSubject( 'ML' );
        return $mail;
    }

    /** ML-07 */
    public function testFromText()
    {
        $text = "A <b>new</b> item was published.\nSee below.\n\nhttps://www.example.invalid/item\n\n"
              . "Change your settings at:\nhttps://www.example.invalid/notification/settings/.\n\n-- \nThe notification system\n";
        $mail = self::mail();
        $handler = self::exceptionHandler();
        try
        {
            $this->assertTrue( expMailTemplateBody::fromText( $mail, $text, array( 'title' => 'Item <x>', 'button' => 'Open the page' ) ) );
        }
        finally
        {
            if ( self::exceptionHandler() !== $handler )
                restore_exception_handler();
        }
        $this->assertSame( 'multipart/alternative', $mail->contentType() );
        $this->assertSame( rtrim( $text ) . "\n", $mail->AlternativeParts['text'] );
        $html = $mail->AlternativeParts['html'];
        $this->assertStringContainsString( 'A &lt;b&gt;new&lt;/b&gt; item was published.<br />See below.', $html );
        $this->assertStringContainsString( 'Item &lt;x&gt;', $html );
        $this->assertMatchesRegularExpression( '#<a href="https://www.example.invalid/item" style="display:inline-block[^>]*>Open the page</a>#', $html );
        $this->assertMatchesRegularExpression( '#<a href="https://www.example.invalid/notification/settings/"[^>]*>https://www.example.invalid/notification/settings/</a>\.#', $html,
                                               'a full stop after an address is not part of it' );
        $this->assertLessThan( strpos( $html, 'Change your settings' ), strpos( $html, 'Open the page' ), 'the rest is below the button' );
        $this->assertStringNotContainsString( '>--<', $html );
        $this->assertStringContainsString( 'text/plain', $mail->body( false ) );
        $this->assertStringContainsString( 'text/html', $mail->body( false ) );
        $this->assertInstanceOf( 'ezcMailMultipartAlternative', $mail->Mail->body );
    }

    /** ML-08 */
    public function testApplyRendered()
    {
        $mail = self::mail();
        $mail->setContentType( 'text/html' );
        $this->assertTrue( expMailTemplateBody::applyRendered( $mail,
            array( 'body' => '<html>h</html>', 'subject' => 's', 'content_type' => 'text/html', 'body_text' => 't' ), 'fallback' ) );
        $this->assertSame( array( 'text' => "t\n", 'html' => '<html>h</html>' ), $mail->AlternativeParts );

        foreach ( array( null, array( 'body' => 'b', 'subject' => null, 'content_type' => null, 'body_text' => null ),
                         array( 'body' => '<html>h</html>', 'subject' => null, 'content_type' => 'text/html', 'body_text' => '  ' ) ) as $i => $rendered )
        {
            $mail = self::mail();
            $this->assertFalse( expMailTemplateBody::applyRendered( $mail, $rendered, "fallback $i" ), "case $i" );
            $this->assertSame( "fallback $i", $mail->body( false ), "case $i" );
            $this->assertNull( $mail->AlternativeParts, "case $i" );
        }
    }

    /** ML-09 */
    public function testGateFooterGoesIntoBothParts()
    {
        $category = new expMailCategory( 'ml_optional', array( 'name' => 'ML optional', 'essential' => false ) );
        $mail = self::mail();
        $handler = self::exceptionHandler();
        try
        {
            expMailTemplateBody::fromText( $mail, "Hello.\n\nhttps://www.example.invalid/item\n", array( 'title' => 'ML', 'button' => 'Open' ) );
            expMailGate::decorate( $mail, null, $category );
        }
        finally
        {
            if ( self::exceptionHandler() !== $handler )
                restore_exception_handler();
        }
        $parts = $mail->AlternativeParts;
        $this->assertStringContainsString( 'mailpreferences/request', $parts['text'], 'the footer in the text part' );
        $this->assertStringContainsString( 'mailpreferences/request', $parts['html'], 'the footer in the HTML part' );
        $this->assertLessThan( strpos( $parts['html'], expMailGate::HTML_FOOTER_MARK ), strpos( $parts['html'], 'mailpreferences/request' ),
                               'the footer inside the card, before the mark' );
        $raw = $mail->body( false );
        $this->assertGreaterThanOrEqual( 2, substr_count( $raw, 'mailpreferences/request' ), 'the raw body carries both footers' );
        $this->assertStringEndsWith( "--\n", rtrim( $raw, "\r\n" ) . "\n", 'nothing after the closing boundary' );
    }

    /** ML-10 */
    public function testAuditAlertHtml()
    {
        $text = "Event:    access.user.login (failure)\nActor:    <b>x</b>\nConsole:  https://admin.example.invalid/audit/event/01\n\nThis message was sent by the audit.\n";
        $handler = self::exceptionHandler();
        try
        {
            $html = expAuditMailSink::htmlBody( '[ML audit] warning', $text );
        }
        finally
        {
            if ( self::exceptionHandler() !== $handler )
                restore_exception_handler();
        }
        $this->assertStringContainsString( 'role="presentation"', $html );
        $this->assertMatchesRegularExpression( '#>Event</td>\s*<td[^>]*>access.user.login \(failure\)</td>#', $html );
        $this->assertStringContainsString( '&lt;b&gt;x&lt;/b&gt;', $html );
        $this->assertMatchesRegularExpression( '#<a href="https://admin.example.invalid/audit/event/01" style="display:inline-block#', $html );
        $this->assertStringContainsString( 'This message was sent by the audit.', $html );
    }

    /** ML-12 */
    public function testNotificationIsSentAsTextAndHtml()
    {
        $ini = eZINI::instance();
        $saved = array();
        foreach ( array( 'Transport', 'TransportAlias' ) as $name )
            $saved[$name] = $ini->hasVariable( 'MailSettings', $name ) ? $ini->variable( 'MailSettings', $name ) : null;
        $notificationINI = eZINI::instance( 'notification.ini' );
        $savedLayout = $notificationINI->hasVariable( 'MailSettings', 'HTMLLayout' ) ? $notificationINI->variable( 'MailSettings', 'HTMLLayout' ) : null;
        $dir = eZSys::rootDir() . '/var/tmp/expmaillayout_' . uniqid( '', true );
        if ( class_exists( 'expMailGate' ) )
            expMailGate::setLogFileForTest( $dir . '/mailgate.jsonl' );
        $ini->setVariable( 'MailSettings', 'Transport', 'mllayoutrecord' );
        $ini->setVariable( 'MailSettings', 'TransportAlias', array( 'mllayoutrecord' => 'expMailLayoutMailsTestTransport' ) );
        expMailLayoutMailsTestTransport::$sent = array();
        $text = "A new item was published.\n\nhttps://www.example.invalid/item\n\nChange your settings at:\nhttps://www.example.invalid/notification/settings/\n";
        $handler = self::exceptionHandler();
        try
        {
            $transport = new eZMailNotificationTransport();
            $this->assertTrue( (bool)$transport->send( array( 'ml-test@example.invalid' ), 'Item "<x>" was published', $text ) );
            $notificationINI->setVariable( 'MailSettings', 'HTMLLayout', 'disabled' );
            $this->assertTrue( (bool)$transport->send( array( 'ml-test@example.invalid' ), 'Plain', $text ) );
        }
        finally
        {
            if ( self::exceptionHandler() !== $handler )
                restore_exception_handler();
            foreach ( $saved as $name => $value )
            {
                if ( $value === null )
                    $ini->removeSetting( 'MailSettings', $name );
                else
                    $ini->setVariable( 'MailSettings', $name, $value );
            }
            if ( $savedLayout === null )
                $notificationINI->removeSetting( 'MailSettings', 'HTMLLayout' );
            else
                $notificationINI->setVariable( 'MailSettings', 'HTMLLayout', $savedLayout );
            if ( class_exists( 'expMailGate' ) )
                expMailGate::setLogFileForTest( null );
            if ( is_file( $dir . '/mailgate.jsonl' ) )
                unlink( $dir . '/mailgate.jsonl' );
            if ( is_dir( $dir ) )
                rmdir( $dir );
        }
        $this->assertCount( 2, expMailLayoutMailsTestTransport::$sent );
        $html = expMailLayoutMailsTestTransport::$sent[0];
        $this->assertSame( 'multipart/alternative', $html->contentType() );
        $this->assertStringContainsString( 'A new item was published.', $html->AlternativeParts['text'] );
        $this->assertStringContainsString( 'Item &quot;&lt;x&gt;&quot; was published', $html->AlternativeParts['html'] );
        $this->assertMatchesRegularExpression( '#<a href="https://www.example.invalid/item" style="display:inline-block#', $html->AlternativeParts['html'] );
        $plain = expMailLayoutMailsTestTransport::$sent[1];
        $this->assertNull( $plain->AlternativeParts, 'HTMLLayout=disabled: text only' );
        $this->assertStringContainsString( 'A new item was published.', $plain->body( false ) );
    }

    /** ML-11 */
    public function testLayoutInputs()
    {
        $html = expMailTemplateBody::renderLayout( array(
            'mail_title' => 'T', 'mail_intro' => 'Intro', 'mail_code' => '005611', 'mail_code_label' => 'Code',
            'mail_preheader' => 'Preview <p>', 'mail_rows' => array( array( 'label' => 'L', 'value' => '<i>kept</i>', 'washed' => true ),
                                                                     array( 'label' => 'M', 'value' => "<i>washed</i>\nnext" ) ) ) );
        $this->assertMatchesRegularExpression( '#font-size:32px[^>]*user-select:all;">005611</td>#', $html );
        $this->assertMatchesRegularExpression( '#<div style="display:none;[^>]*>Preview &lt;p&gt;</div>#', $html );
        $this->assertStringContainsString( '<i>kept</i>', $html );
        $this->assertStringContainsString( '&lt;i&gt;washed&lt;/i&gt;<br />', $html );
        $this->assertStringContainsString( '<meta name="color-scheme" content="light dark" />', $html );
        $style = explode( '</style>', explode( '<style type="text/css">', $html )[1] )[0];
        $this->assertStringContainsString( '@media (prefers-color-scheme: dark)', $style );
        $body = explode( '</head>', $html )[1];
        $this->assertStringNotContainsString( '#0d1117', $body, 'dark colors only in the media query' );
    }
}
