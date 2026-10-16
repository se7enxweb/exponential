<?php
/**
 * The registration mails sent to the user: the activation mail (design:user/registrationinfo.tpl), the approval mail
 * (design:user/registrationapproved.tpl) and the activation mail sent again from the administration
 * (design:user/unactivated_resend_mail.tpl), all rendered through design:mail/layout.tpl and its text twin.
 *
 *  UM-01 - each template sets the subject, the content type text/html and body_text (the text part); the output is a page
 *  UM-02 - the activation link is absolute https, carries the hash, in the HTML part and in the text part
 *  UM-03 - no link to content/view/full (the account has no node yet); a sign-in link instead
 *  UM-04 - the password row shows only with [UserSettings] PasswordInRegistrationEmail=enabled
 *  UM-05 - the approval mail: a sign-in button on https, the login and the e-mail address
 *  UM-06 - the resend mail: an activation_url given with http is shown with https
 *  UM-07 - values are washed in the HTML part: a login with markup is escaped
 *  UM-08 - the templates carry no styles and no markup (the layout does), and never name the old product name
 *
 * The templates are rendered as the kernel does (eZTemplate::factory(), fetch), uncompiled, with hashes standing in for
 * the user and the content object; no database, nothing is sent. Fake addresses use the .invalid domain.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/user/expRegistrationUserMailsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expRegistrationUserMailsTest extends PHPUnit\Framework\TestCase
{
    const INFO = 'design/standard/templates/user/registrationinfo.tpl';
    const APPROVED = 'design/standard/templates/user/registrationapproved.tpl';
    const RESEND = 'design/standard/templates/user/unactivated_resend_mail.tpl';
    const HASH = '0123456789abcdef0123456789abcdef';

    private $savedGlobals = array();
    private $savedPasswordSetting = null;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics', 'eZCurrentAccess' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
        $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        // a web request has a siteaccess; the design lookup of the extensions reads its name
        if ( !isset( $GLOBALS['eZCurrentAccess']['name'] ) )
            $GLOBALS['eZCurrentAccess'] = array( 'name' => eZINI::instance()->variable( 'SiteSettings', 'DefaultAccess' ),
                                                 'type' => eZSiteAccess::TYPE_DEFAULT );
        $this->savedPasswordSetting = eZINI::instance()->variable( 'UserSettings', 'PasswordInRegistrationEmail' );
    }

    protected function tearDown(): void
    {
        eZINI::instance()->setVariable( 'UserSettings', 'PasswordInRegistrationEmail', $this->savedPasswordSetting );
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

    /**
     * @return array html, text, subject, content_type
     */
    private function render( $template, array $variables = array(), $login = 'um-test' )
    {
        $tpl = eZTemplate::factory();
        $tpl->resetVariables();
        $tpl->setVariable( 'user', array( 'login' => $login, 'email' => 'um-test@example.invalid', 'contentobject_id' => 7 ) );
        // an unpublished account: no main node yet
        $tpl->setVariable( 'object', array( 'name' => 'UM Test', 'id' => 7, 'main_node_id' => null ) );
        $tpl->setVariable( 'hostname', 'www.example.invalid' );
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
                'content_type' => $tpl->hasVariable( 'content_type' ) ? (string)$tpl->variable( 'content_type' ) : null,
            );
        }
        finally
        {
            if ( self::exceptionHandler() !== $handler )
                restore_exception_handler();
            $tpl->resetVariables();
        }
    }

    private function resendVariables( $url )
    {
        return array( 'hash' => self::HASH, 'activation_url' => $url, 'site_url' => 'www.example.invalid' );
    }

    /** UM-01 */
    public function testEveryTemplateSetsSubjectContentTypeAndTextPart()
    {
        $cases = array(
            'info' => array( self::INFO, array( 'hash' => self::HASH ), 'registration info' ),
            'info without hash' => array( self::INFO, array(), 'registration info' ),
            'approved' => array( self::APPROVED, array(), 'registration approved' ),
            'resend' => array( self::RESEND, $this->resendVariables( 'https://www.example.invalid/user/activate/' . self::HASH . '/0' ), 'Activate your account at' ),
        );
        foreach ( $cases as $name => $case )
        {
            $mail = $this->render( $case[0], $case[1] );
            $this->assertStringContainsString( $case[2], $mail['subject'], $name );
            $this->assertSame( 'text/html', trim( $mail['content_type'] ), $name );
            $this->assertNotNull( $mail['text'], "$name: body_text is set" );
            $this->assertStringContainsString( 'um-test@example.invalid', $mail['text'], $name );
            $this->assertStringContainsString( 'um-test', $mail['text'], $name );
            $this->assertStringContainsString( '<html', $mail['html'], $name );
            $this->assertStringContainsString( 'role="presentation"', $mail['html'], $name );
        }
    }

    /** UM-02 */
    public function testActivationLinkIsHttpsWithTheHash()
    {
        $mail = $this->render( self::INFO, array( 'hash' => self::HASH ) );
        $pattern = '#https://www\.example\.invalid/(?:[^\s"<]*/)?user/activate/' . self::HASH . '#';
        $this->assertMatchesRegularExpression( $pattern, $mail['html'] );
        $this->assertMatchesRegularExpression( $pattern, $mail['text'] );
        $this->assertStringContainsString( 'Activate account', $mail['html'] );
        foreach ( array( 'html', 'text' ) as $part )
            $this->assertDoesNotMatchRegularExpression( '#http://www\.example\.invalid#', $mail[$part], $part );
        $this->assertStringNotContainsString( 'href="http://', $mail['html'] );

        $plain = $this->render( self::INFO );
        $this->assertStringNotContainsString( 'user/activate', $plain['html'], 'no activation link without a hash' );
    }

    /** UM-03 */
    public function testNoLinkToTheUnpublishedNode()
    {
        foreach ( array( array( 'hash' => self::HASH ), array() ) as $variables )
        {
            $mail = $this->render( self::INFO, $variables );
            foreach ( array( 'html', 'text' ) as $part )
            {
                $this->assertStringNotContainsString( 'content/view/full', $mail[$part], $part );
                $this->assertStringNotContainsString( 'Link to user information', $mail[$part], $part );
                $this->assertMatchesRegularExpression( '#https://www\.example\.invalid/(?:[^\s"<]*/)?user/login#', $mail[$part], $part );
                $this->assertStringContainsString( 'Sign in', $mail[$part], $part );
            }
        }
    }

    /** UM-04 */
    public function testPasswordRowOnlyWhenEnabled()
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'UserSettings', 'PasswordInRegistrationEmail', 'disabled' );
        $mail = $this->render( self::INFO, array( 'hash' => self::HASH, 'password' => 'Um-Secret-1' ) );
        foreach ( array( 'html', 'text' ) as $part )
            $this->assertStringNotContainsString( 'Um-Secret-1', $mail[$part], "disabled: $part" );

        $ini->setVariable( 'UserSettings', 'PasswordInRegistrationEmail', 'enabled' );
        $mail = $this->render( self::INFO, array( 'hash' => self::HASH, 'password' => 'Um-Secret-1' ) );
        foreach ( array( 'html', 'text' ) as $part )
        {
            $this->assertStringContainsString( 'Um-Secret-1', $mail[$part], "enabled: $part" );
            $this->assertStringContainsString( 'Password', $mail[$part], "enabled: $part" );
        }
        $mail = $this->render( self::INFO, array( 'hash' => self::HASH ) );
        $this->assertStringNotContainsString( 'Password', $mail['text'], 'no row without a password' );
    }

    /** UM-05 */
    public function testApprovalMailHasSignIn()
    {
        $site = eZINI::instance()->variable( 'SiteSettings', 'SiteURL' );
        $mail = $this->render( self::APPROVED );
        foreach ( array( 'html', 'text' ) as $part )
        {
            $this->assertStringContainsString( "https://$site/user/login", $mail[$part], $part );
            $this->assertStringContainsString( 'Sign in', $mail[$part], $part );
            $this->assertStringContainsString( 'um-test@example.invalid', $mail[$part], $part );
            $this->assertStringNotContainsString( 'http://', str_replace( 'http://www.w3.org/', '', $mail[$part] ), $part );
        }
    }

    /** UM-06 */
    public function testResendMailShowsTheLinkWithHttps()
    {
        $path = 'www.example.invalid/user/activate/' . self::HASH . '/0';
        $mail = $this->render( self::RESEND, $this->resendVariables( "http://$path" ) );
        foreach ( array( 'html', 'text' ) as $part )
        {
            $this->assertStringContainsString( "https://$path", $mail[$part], $part );
            $this->assertStringNotContainsString( "http://$path", $mail[$part], $part );
            $this->assertStringContainsString( 'https://www.example.invalid/user/login', $mail[$part], $part );
        }
        $this->assertStringContainsString( 'Activate account', $mail['html'] );
        $mail = $this->render( self::RESEND, $this->resendVariables( "https://$path" ) );
        $this->assertStringContainsString( "https://$path", $mail['html'], 'an https link is kept as it is' );
    }

    /** UM-07 */
    public function testValuesAreWashed()
    {
        $login = '<script>alert(1)</script>"um';
        foreach ( array( array( self::INFO, array( 'hash' => self::HASH ) ), array( self::APPROVED, array() ),
                         array( self::RESEND, $this->resendVariables( 'https://www.example.invalid/user/activate/x/0' ) ) ) as $case )
        {
            $mail = $this->render( $case[0], $case[1], $login );
            $this->assertStringNotContainsString( '<script>', $mail['html'], $case[0] );
            $this->assertStringContainsString( '&lt;script&gt;alert(1)&lt;/script&gt;', $mail['html'], $case[0] );
        }
    }

    /** UM-08 */
    public function testTemplatesCarryNoStylesOrMarkup()
    {
        foreach ( array( self::INFO, self::APPROVED, self::RESEND ) as $file )
        {
            $source = file_get_contents( $file );
            $this->assertDoesNotMatchRegularExpression( '/style\s*=|<style/i', $source, "$file has no styles" );
            $this->assertDoesNotMatchRegularExpression( '/<[a-z]/i', preg_replace( '/\{\*.*?\*\}/s', '', $source ), "$file has no markup" );
            $this->assertDoesNotMatchRegularExpression( '/ez\s*publish/i', $source, $file );
            $this->assertStringContainsString( 'design:mail/layout.tpl', $source, $file );
            $this->assertStringContainsString( 'design:mail/layout_text.tpl', $source, $file );
        }
    }
}
