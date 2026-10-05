<?php
/**
 * File containing the Exponential\View\Kernel\Mailpreferences\Settings class: the view mailpreferences/settings.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Mailpreferences
{

use Exponential\Service\MailPreferencesPage;

/**
 * The e-mail preferences of the signed-in user: the main switch, the categories and their frequencies, essential mail
 * (not switchable), the consent history and the download of the data. Someone not signed in is sent to the login
 * (the page links to "send me a link" for people without an account).
 */
class Settings extends Page
{
    protected function page()
    {
        $user = \eZUser::currentUser();
        if ( !$user->isRegistered() )
            return $this->module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );

        $prefs = \expMailPreferences::forRecipient( \expMailRecipient::fromUser( $user ) );
        $notices = MailPreferencesPage::handlePost( $prefs, $this->http, 'page' );

        $variables = MailPreferencesPage::templateVariables( $prefs, 'account', 'mailpreferences/settings',
                                                             array( 'json' => 'mailpreferences/export/json', 'csv' => 'mailpreferences/export/csv' ),
                                                             $notices );
        $variables['can_administrate'] = self::canAdministrate();
        $notification = $user->hasAccessTo( 'notification', 'use' );
        $variables['notification_settings'] = $notification['accessWord'] !== 'no';
        return $this->render( 'settings.tpl', $variables, \ezpI18n::tr( 'kernel/mailpreferences', 'My e-mail preferences' ) );
    }
}

}
