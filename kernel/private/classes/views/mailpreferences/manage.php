<?php
/**
 * File containing the Exponential\View\Kernel\Mailpreferences\Manage class: the view mailpreferences/manage/<token>.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Mailpreferences
{

use Exponential\Service\MailPreferencesPage;

/**
 * The preference page by a personal link (the "Manage" link of every optional e-mail, or "send me a link"): the same
 * page as mailpreferences/settings, without login. A link that does not work any more shows the "send me a link" form.
 */
class Manage extends Page
{
    protected function page()
    {
        $token = (string)$this->param( 'Token', '' );
        $payload = $token !== '' ? \expMailToken::verify( $token, 'manage' ) : null;
        $recipient = $payload ? \expMailRecipient::fromPayload( $payload ) : null;
        if ( !$recipient )
            return Request::invalidLink( $this );

        $prefs = \expMailPreferences::forRecipient( $recipient );
        $notices = MailPreferencesPage::handlePost( $prefs, $this->http, 'link' );
        $base = 'mailpreferences/manage/' . rawurlencode( $token );
        $variables = MailPreferencesPage::templateVariables( $prefs, 'token', $base,
                                                             array( 'json' => 'mailpreferences/export/json/(token)/' . rawurlencode( $token ),
                                                                    'csv' => 'mailpreferences/export/csv/(token)/' . rawurlencode( $token ) ),
                                                             $notices );
        $variables['can_administrate'] = false;
        $variables['notification_settings'] = false;
        return $this->render( 'settings.tpl', $variables, \ezpI18n::tr( 'kernel/mailpreferences', 'Manage e-mail' ) );
    }
}

}
