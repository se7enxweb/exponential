<?php
/**
 * File containing the Exponential\View\Kernel\Mailpreferences\Confirm class: the view mailpreferences/confirm/<token>.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Mailpreferences
{

use Exponential\Service\MailPreferencesPage;

/**
 * The link of a double opt-in mail (a category, or a new e-mail address). Opening it shows one button; only the
 * button confirms (expMailPreferencesService::confirm()), so a mail scanner that opens the link changes nothing.
 * No login.
 */
class Confirm extends Page
{
    protected function page()
    {
        $token = (string)$this->param( 'Token', '' );
        $base = 'mailpreferences/confirm/' . rawurlencode( $token );
        $payload = $token !== '' ? \expMailToken::verify( $token, 'confirm' ) : null;
        $recipient = $payload ? \expMailRecipient::fromPayload( $payload ) : null;
        $pending = $payload && $payload['pending'] ? \expMailPendingRow::fetch( $payload['pending'] ) : null;
        if ( $pending && $recipient && $pending->attribute( 'recipient_key' ) !== $recipient->key() )
            $pending = null;
        $kind = $pending ? (string)$pending->attribute( 'kind' ) : 'category';
        $category = $payload && $payload['category'] !== '' ? \expMailCategoryRegistry::instance()->get( $payload['category'] ) : null;

        $variables = array( 'state' => 'invalid', 'kind' => $kind, 'email' => $recipient ? $recipient->email() : '',
                            'category_name' => $category ? MailPreferencesPage::categoryName( $category ) : '',
                            'category_description' => $category ? MailPreferencesPage::categoryDescription( $category ) : '',
                            'form_action' => $base, 'manage_url' => false );
        if ( $kind === 'email_change' && $pending )
        {
            $data = $pending->dataArray();
            $variables['email'] = isset( $data['email'] ) ? (string)$data['email'] : '';
        }

        if ( $recipient && self::isPost() && $this->http->hasPostVariable( 'ConfirmButton' ) )
        {
            $wording = $kind === 'email_change'
                ? MailPreferencesPage::tr( 'Use %email for this account?', array( '%email' => $variables['email'] ) )
                : MailPreferencesPage::tr( 'Send e-mail of the kind "%category" to %email?', array( '%category' => $variables['category_name'], '%email' => $variables['email'] ) );
            $result = \expMailPreferencesService::confirm( $token, \expConsentContext::fromRequest( 'confirm', $wording . ' ' . MailPreferencesPage::tr( 'Yes, confirm' ) ) );
            if ( in_array( $result['result'], array( 'confirmed', 'already' ), true ) )
            {
                $variables['state'] = 'done';
                if ( $result['recipient'] instanceof \expMailRecipient )
                {
                    $recipient = $result['recipient'];
                    if ( $kind === 'email_change' )
                        $variables['email'] = $recipient->email();
                }
            }
        }
        else if ( $recipient && $pending && ( (int)$pending->attribute( 'expires' ) === 0 || (int)$pending->attribute( 'expires' ) >= time() ) )
            $variables['state'] = 'confirm';
        else if ( $recipient && $category && \expMailPreferences::forRecipient( $recipient )->state( $category->identifier ) === \expMailPreferences::ON )
            $variables['state'] = 'done';

        if ( $recipient && $variables['state'] === 'done' )
            $variables['manage_url'] = 'mailpreferences/manage/' . rawurlencode( \expMailToken::create( $recipient, 'manage' ) );
        $title = $kind === 'email_change' ? MailPreferencesPage::tr( 'Confirm your e-mail address' ) : MailPreferencesPage::tr( 'Confirm your subscription' );
        return $this->render( 'confirm.tpl', $variables, $title );
    }
}

}
