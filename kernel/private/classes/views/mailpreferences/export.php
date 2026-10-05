<?php
/**
 * File containing the Exponential\View\Kernel\Mailpreferences\Export class: the view mailpreferences/export/<format>[/(token)/<token>].
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Mailpreferences
{

use Exponential\Service\MailPreferencesPage;

/**
 * "Download my e-mail data": everything stored about a person's e-mail preferences, as JSON or CSV (the categories
 * and their state, pending confirmations, the whole consent log). For the signed-in user, or with the personal
 * link of the preference page (/(token)/<token>). The download is recorded in the consent log.
 */
class Export extends Page
{
    protected function page()
    {
        $format = strtolower( (string)$this->param( 'Format', 'json' ) );
        if ( !in_array( $format, array( 'json', 'csv' ), true ) )
            $format = 'json';
        $token = (string)$this->param( 'Token', '' );
        if ( $token !== '' )
        {
            $payload = \expMailToken::verify( $token, 'manage' );
            $recipient = $payload ? \expMailRecipient::fromPayload( $payload ) : null;
            if ( !$recipient )
                return Request::invalidLink( $this );
            $source = 'link';
        }
        else
        {
            $user = \eZUser::currentUser();
            if ( !$user->isRegistered() )
                return $this->module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            $recipient = \expMailRecipient::fromUser( $user );
            $source = 'page';
        }
        MailPreferencesPage::sendExport( \expMailPreferences::forRecipient( $recipient ), $format, $source );
        return null;
    }
}

}
