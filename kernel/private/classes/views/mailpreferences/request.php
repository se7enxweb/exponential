<?php
/**
 * File containing the Exponential\View\Kernel\Mailpreferences\Request class: the view mailpreferences/request.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Mailpreferences
{

use Exponential\Service\MailPreferencesPage;

/**
 * "Send me a link": a person without an account, or not signed in, types an address and gets a personal link to the
 * preference page. The answer is the same for every well-formed address, known or not, sent, limited or blocked, so
 * the page tells nobody who is on our lists.
 */
class Request extends Page
{
    protected function page()
    {
        $email = '';
        $error = false;
        $state = 'form';
        if ( $this->http->hasPostVariable( 'RequestButton' ) )
        {
            $email = trim( (string)$this->http->postVariable( 'Email', '' ) );
            if ( !\eZMail::validate( $email ) )
                $error = MailPreferencesPage::tr( 'Please enter a complete e-mail address, for example name@example.com.' );
            else
            {
                try
                {
                    \expMailPreferencesService::requestLink( $email, \expConsentContext::fromRequest( 'link', MailPreferencesPage::tr( 'Send me the link' ) ) );
                }
                catch ( \Throwable $e )
                {
                    \eZDebug::writeError( $e->getMessage(), __METHOD__ );
                }
                $state = 'sent';
            }
        }
        return $this->render( 'request.tpl', array( 'state' => $state, 'email' => $email, 'error' => $error,
                                                    'valid_hours' => self::validHours() ),
                              \ezpI18n::tr( 'kernel/mailpreferences', 'Send me a link' ) );
    }

    /**
     * The "send me a link" form with the notice that a personal link does not work any more (manage, unsubscribe).
     *
     * @param Page $view the view that found the link broken
     * @return array $Result
     */
    public static function invalidLink( Page $view )
    {
        return $view->render( 'request.tpl', array( 'state' => 'form', 'email' => '', 'valid_hours' => self::validHours(),
                                                    'error' => MailPreferencesPage::tr( 'This link does not work any more: it may be incomplete or too old. Enter your e-mail address and we send you a new one.' ) ),
                              \ezpI18n::tr( 'kernel/mailpreferences', 'Send me a link' ) );
    }

    /** @return int how many hours the link sent by this page works */
    public static function validHours()
    {
        $ini = \eZINI::instance( 'mailpreferences.ini' );
        $ttl = $ini->hasVariable( 'TokenSettings', 'RequestLinkTTL' ) ? (int)$ini->variable( 'TokenSettings', 'RequestLinkTTL' ) : 86400;
        return max( 1, (int)round( max( 60, $ttl ) / 3600 ) );
    }
}

}
