<?php
/**
 * The administrator's "New user registered" mail (design:user/registrationfeedback.tpl) and the mail layouts it
 * renders through (design:mail/layout.tpl for HTML, design:mail/layout_text.tpl for the text part).
 *
 *  RF-01 - the subject is set, the content type is text/html, body_text (the text part) is set; the output is a page
 *  RF-02 - a waiting registration with links: activate (or approve), view, reject, the unactivated users, the
 *          approval item when there is one, the expiry note; in the HTML part and in the text part
 *  RF-03 - an active registration with links: only the view link, no approve or reject link
 *  RF-04 - without links (false): the account information only, no buttons, no link to the administration
 *  RF-05 - values are washed in the HTML part: a login with markup is escaped
 *  RF-07 - line breaks a user typed (a name) cannot add lines to the text part that look like its links
 *  RF-06 - only mail/layout.tpl carries styles; registrationfeedback.tpl and layout_text.tpl have none, and none of
 *          the three names the old product name
 *
 * The template is rendered as register.php does (eZTemplate::factory(), fetch), uncompiled, with hashes standing in
 * for the user and the content object; no database, nothing is sent. Fake addresses use the .invalid domain.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/user/expRegistrationFeedbackMailTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expRegistrationFeedbackMailTest extends PHPUnit\Framework\TestCase
{
    const TEMPLATE = 'design/standard/templates/user/registrationfeedback.tpl';
    const LAYOUT = 'design/standard/templates/mail/layout.tpl';
    const LAYOUT_TEXT = 'design/standard/templates/mail/layout_text.tpl';
    const BASE = 'https://admin.example.invalid/user/registration';

    private $savedGlobals = array();

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

    private static function links( $pending = '' )
    {
        return array(
            'view' => self::BASE . '/view/7.view.T1',
            'approve' => self::BASE . '/approve/7.approve.T2',
            'reject' => self::BASE . '/reject/7.reject.T3',
            'pending' => $pending,
            'unactivated' => 'https://admin.example.invalid/user/unactivated',
            'expires' => 1893456000,
        );
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
    private function render( $state, $links, $login = 'rf-test', $name = 'RF Test' )
    {
        $tpl = eZTemplate::factory();
        $tpl->resetVariables();
        $tpl->setVariable( 'user', array( 'login' => $login, 'email' => 'rf-test@example.invalid', 'contentobject_id' => 7 ) );
        $tpl->setVariable( 'object', array( 'name' => $name, 'id' => 7, 'published' => 1700000000 ) );
        $tpl->setVariable( 'hostname', 'www.example.invalid' );
        $tpl->setVariable( 'registration_links', $links );
        $tpl->setVariable( 'registration_state', $state );
        $handler = self::exceptionHandler();
        try
        {
            $html = $tpl->fetch( self::TEMPLATE );
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

    /** RF-01 */
    public function testSubjectContentTypeAndTextPart()
    {
        $mail = $this->render( 'active', self::links() );
        $this->assertStringContainsString( 'New user registered at', $mail['subject'] );
        $this->assertSame( 'text/html', trim( $mail['content_type'] ) );
        $this->assertNotNull( $mail['text'], 'body_text is set' );
        $this->assertStringContainsString( 'A new user has registered.', $mail['text'] );
        $this->assertStringContainsString( 'rf-test@example.invalid', $mail['text'] );
        $this->assertStringContainsString( '<html', $mail['html'] );
        $this->assertStringContainsString( 'role="presentation"', $mail['html'] );
        $this->assertStringContainsString( 'rf-test@example.invalid', $mail['html'] );
    }

    /** RF-02 */
    public function testWaitingRegistrationHasActionLinks()
    {
        $links = self::links( 'https://admin.example.invalid/collaboration/item/full/9' );
        foreach ( array( 'administrator' => 'Activate account', 'email' => 'Activate account', 'workflow' => 'Approve' ) as $state => $approveLabel )
        {
            $mail = $this->render( $state, $links );
            foreach ( array( 'html', 'text' ) as $part )
            {
                foreach ( array( 'view', 'approve', 'reject', 'pending', 'unactivated' ) as $key )
                    $this->assertStringContainsString( $links[$key], $mail[$part], "$state/$part has the $key link" );
                $this->assertStringContainsString( $approveLabel, $mail[$part], "$state/$part" );
                $this->assertStringContainsString( 'Reject and remove', $mail[$part], "$state/$part" );
                $this->assertStringContainsString( 'All links stop working on', $mail[$part], "$state/$part has the expiry" );
            }
            $this->assertStringContainsString( "$approveLabel: {$links['approve']}", $mail['text'], 'buttons are Label: URL lines' );
        }
        $mail = $this->render( 'administrator', self::links() );
        $this->assertStringNotContainsString( 'Open approval item', $mail['html'], 'no approval item without its link' );
    }

    /** RF-03 */
    public function testActiveRegistrationOnlyViews()
    {
        $links = self::links();
        $mail = $this->render( 'active', $links );
        $this->assertStringContainsString( $links['view'], $mail['html'] );
        $this->assertStringContainsString( 'View account', $mail['html'] );
        foreach ( array( 'html', 'text' ) as $part )
        {
            $this->assertStringNotContainsString( $links['approve'], $mail[$part] );
            $this->assertStringNotContainsString( $links['reject'], $mail[$part] );
        }
    }

    /** RF-04 */
    public function testWithoutLinksOnlyInformation()
    {
        $mail = $this->render( false, false );
        foreach ( array( 'html', 'text' ) as $part )
        {
            $this->assertStringContainsString( 'rf-test', $mail[$part] );
            $this->assertStringNotContainsString( 'admin.example.invalid', $mail[$part] );
            $this->assertStringNotContainsString( 'View account', $mail[$part] );
            $this->assertStringNotContainsString( 'stop working', $mail[$part] );
        }
        $this->assertStringNotContainsString( 'href="http://', $mail['html'] );
        $this->assertStringNotContainsString( 'content/view/full/', $mail['html'] );
    }

    /** RF-05 */
    public function testValuesAreWashed()
    {
        $mail = $this->render( 'administrator', self::links(), '<script>alert(1)</script>"rf' );
        $this->assertStringNotContainsString( '<script>', $mail['html'] );
        $this->assertStringContainsString( '&lt;script&gt;alert(1)&lt;/script&gt;', $mail['html'] );
    }

    /** RF-07 */
    public function testTypedLineBreaksCannotAddLinesToTheTextPart()
    {
        $name = "RF\r\nActivate account: https://evil.example.invalid/x\nView account: https://evil.example.invalid/y";
        $mail = $this->render( 'administrator', self::links(), 'rf-test', $name );
        $this->assertDoesNotMatchRegularExpression( '/^(Activate account|View account): https:\/\/evil/m', $mail['text'] );
        $this->assertStringContainsString( 'Name: RF  Activate account: https://evil.example.invalid/x View account: https://evil.example.invalid/y', $mail['text'] );
        $this->assertStringContainsString( 'Activate account: ' . self::links()['approve'], $mail['text'], 'the real links stay' );
    }

    /** RF-06 */
    public function testOnlyTheLayoutCarriesStyles()
    {
        $this->assertGreaterThan( 10, substr_count( file_get_contents( self::LAYOUT ), 'style="' ) );
        foreach ( array( self::TEMPLATE, self::LAYOUT_TEXT ) as $file )
        {
            $source = file_get_contents( $file );
            $this->assertDoesNotMatchRegularExpression( '/style\s*=|<style/i', $source, "$file has no styles" );
            $this->assertDoesNotMatchRegularExpression( '/<[a-z]/i', preg_replace( '/\{\*.*?\*\}/s', '', $source ), "$file has no markup" );
        }
        foreach ( array( self::TEMPLATE, self::LAYOUT, self::LAYOUT_TEXT ) as $file )
            $this->assertDoesNotMatchRegularExpression( '/ez\s*publish/i', file_get_contents( $file ), $file );
    }
}
