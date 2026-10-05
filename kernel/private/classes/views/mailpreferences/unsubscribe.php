<?php
/**
 * File containing the Exponential\View\Kernel\Mailpreferences\Unsubscribe class: the view mailpreferences/unsubscribe/<token>.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Mailpreferences
{

use Exponential\Service\MailPreferencesPage;

/**
 * The unsubscribe link of an optional e-mail. No login.
 *
 *  - GET: a page with one button (opening a link must not unsubscribe: mail scanners open links).
 *  - POST from that button: unsubscribed at once, with a link to the person's preference page.
 *  - POST with List-Unsubscribe=One-Click (RFC 8058, sent by the mail program): unsubscribed at once, a short
 *    plain answer and nothing else; the mail program does not show a page.
 *
 * A link for a category switches that category off; a link without one switches all optional e-mail off.
 */
class Unsubscribe extends Page
{
    protected function page()
    {
        $token = (string)$this->param( 'Token', '' );
        $oneClick = self::isPost() && isset( $_POST['List-Unsubscribe'] ) && trim( (string)$_POST['List-Unsubscribe'] ) === 'One-Click';
        $payload = $token !== '' ? \expMailToken::verify( $token, 'unsubscribe' ) : null;
        $recipient = $payload ? \expMailRecipient::fromPayload( $payload ) : null;
        $category = $payload && $payload['category'] !== '' ? \expMailCategoryRegistry::instance()->get( $payload['category'] ) : null;
        if ( $category && $category->essential )
            $category = null;

        if ( $oneClick )
            return $this->oneClick( $token, $recipient, $category );

        $base = 'mailpreferences/unsubscribe/' . rawurlencode( $token );
        $title = MailPreferencesPage::tr( 'Unsubscribe' );
        if ( !$recipient )
            return $this->render( 'unsubscribe.tpl', array( 'state' => 'invalid', 'email' => '', 'category_name' => false,
                                                            'form_action' => $base, 'manage_url' => false ), $title );

        $name = $category ? MailPreferencesPage::categoryName( $category ) : false;
        $state = 'confirm';
        if ( self::isPost() && $this->http->hasPostVariable( 'UnsubscribeButton' ) )
        {
            $heading = $name ? MailPreferencesPage::tr( 'Stop e-mail of the kind "%category"?', array( '%category' => $name ) )
                             : MailPreferencesPage::tr( 'Stop all optional e-mail?' );
            $context = \expConsentContext::fromRequest( 'link', $heading . ' ' . MailPreferencesPage::tr( 'Unsubscribe' ) );
            $result = \expMailPreferencesService::unsubscribe( $token, $context );
            $state = $result['result'] === 'unsubscribed' ? 'done' : 'invalid';
        }
        $manage = 'mailpreferences/manage/' . rawurlencode( \expMailToken::create( $recipient, 'manage' ) );
        return $this->render( 'unsubscribe.tpl', array( 'state' => $state, 'email' => $recipient->email(), 'category_name' => $name,
                                                        'form_action' => $base, 'manage_url' => $manage ), $title );
    }

    /**
     * RFC 8058: the mail program posts List-Unsubscribe=One-Click to the address of the List-Unsubscribe header.
     * The answer is 200 when the person is unsubscribed, 400 for a link that does not work.
     */
    protected function oneClick( $token, $recipient, $category )
    {
        $ok = false;
        if ( $recipient )
        {
            $what = $category ? MailPreferencesPage::categoryName( $category ) : MailPreferencesPage::tr( 'All optional e-mail' );
            $context = \expConsentContext::fromRequest( 'link', MailPreferencesPage::tr( 'One-click unsubscribe from the mail program (RFC 8058): %what', array( '%what' => $what ) ) );
            $result = \expMailPreferencesService::unsubscribe( $token, $context );
            $ok = $result['result'] === 'unsubscribed';
        }
        while ( ob_get_level() > 0 )
            ob_end_clean();
        if ( !$ok )
            header( $_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request' );
        header( 'Content-Type: text/plain; charset=utf-8' );
        MailPreferencesPage::privateHeaders();
        echo $ok ? MailPreferencesPage::tr( 'You are unsubscribed.' ) : MailPreferencesPage::tr( 'This link does not work any more' );
        echo "\n";
        \eZExecution::cleanExit();
    }
}

}
