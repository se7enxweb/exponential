<?php
/**
 * File containing the expMailAddressChange class.
 *
 * A new e-mail address for an account takes effect only once it is confirmed (double opt-in, mailpreferences.ini
 * [EmailChangeSettings] Confirm=enabled, the default): the account keeps its address, a confirmation link goes to
 * the new address (expMailPreferencesService::requestEmailChange(), category security) and the old address gets a
 * notice, so a person whose account was taken over learns of it. The link sets the new address
 * (mailpreferences/confirm).
 *
 * Every way an address is changed asks this class: the user account datatype when a user object is published
 * (user/edit, the admin's content edit), and the account services. A site that needs the old behaviour sets
 * Confirm=disabled: the address changes at once, as before.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMailAddressChange
{
    const CHANGED = 'changed';
    const PENDING = 'pending_confirmation';

    /** @var array|null the result of the last request(), for the page that made it */
    protected static $last = null;

    /** @return bool [EmailChangeSettings] Confirm is not disabled, and the tables are there */
    public static function confirmationRequired()
    {
        if ( !class_exists( 'expMailPreferencesService' ) )
            return false;
        $ini = eZINI::instance( 'mailpreferences.ini' );
        if ( $ini->hasVariable( 'EmailChangeSettings', 'Confirm' ) && $ini->variable( 'EmailChangeSettings', 'Confirm' ) === 'disabled' )
            return false;
        return expMailPreferencesService::tableExists( 'expmail_pending' );
    }

    /**
     * Asks for a new address of an account.
     *
     * @param eZUser $user the account with its current (old) address
     * @param string $newEmail
     * @param expConsentContext|null $context null: from the request, source page (the person) or admin (someone else)
     * @return string changed (no confirmation needed: the caller sets the address), pending_confirmation (the account
     *                keeps its address), unchanged, invalid, in_use
     */
    public static function request( eZUser $user, $newEmail, ?expConsentContext $context = null )
    {
        $old = (string)$user->attribute( 'email' );
        if ( expMailRecipient::normalise( $newEmail ) === expMailRecipient::normalise( $old ) )
            return self::remember( $user, 'unchanged', $newEmail );
        if ( trim( $old ) === '' || !self::confirmationRequired() )
            return self::remember( $user, self::CHANGED, $newEmail );
        if ( $context === null )
        {
            $own = (int)eZUser::currentUserID() === (int)$user->attribute( 'contentobject_id' );
            $context = expConsentContext::fromRequest( $own ? 'page' : 'admin',
                ezpI18n::tr( 'kernel/mailpreferences/mail', 'Change of the e-mail address of the account' ) );
        }
        $result = expMailPreferencesService::requestEmailChange( $user, $newEmail, $context );
        if ( $result === self::PENDING )
            self::noticeToOldAddress( $user, $newEmail );
        return self::remember( $user, $result, $newEmail );
    }

    /** @return array|null user_id, result, email (masked) of the last request() in this process */
    public static function lastResult()
    {
        return self::$last;
    }

    protected static function remember( eZUser $user, $result, $newEmail )
    {
        self::$last = array( 'user_id' => (int)$user->attribute( 'contentobject_id' ), 'result' => $result,
                             'email' => expMailPreferencesService::maskAddress( (string)$newEmail ) );
        if ( $result === self::PENDING && PHP_SAPI !== 'cli' && class_exists( 'eZSession' ) && eZSession::hasStarted() )
            eZHTTPTool::instance()->setSessionVariable( 'MailAddressChangePending', self::$last );
        return $result;
    }

    /**
     * Tells the old address that a change was asked for (category security, always sent).
     *
     * @param eZUser $user
     * @param string $newEmail
     * @return bool
     */
    protected static function noticeToOldAddress( eZUser $user, $newEmail )
    {
        $ini = eZINI::instance( 'mailpreferences.ini' );
        if ( $ini->hasVariable( 'EmailChangeSettings', 'NotifyOldAddress' ) && $ini->variable( 'EmailChangeSettings', 'NotifyOldAddress' ) === 'disabled' )
            return false;
        $old = (string)$user->attribute( 'email' );
        if ( !eZMail::validate( $old ) )
            return false;
        $masked = expMailPreferencesService::maskAddress( $newEmail );
        $subject = ezpI18n::tr( 'kernel/mailpreferences/mail', 'The e-mail address of your account is being changed' );
        $body = ezpI18n::tr( 'kernel/mailpreferences/mail', 'Someone asked to change the e-mail address of your account to %email. The change takes effect only when it is confirmed with the link sent to the new address; until then your account keeps this address.', null, array( '%email' => $masked ) )
              . "\n\n"
              . ezpI18n::tr( 'kernel/mailpreferences/mail', 'If you did not ask for this, change your password and contact us.' ) . "\n";
        $rendered = expMailPreferencesService::renderTemplate( 'design:mailpreferences/mail/email_change_notice.tpl',
                                                                 array( 'user' => $user, 'new_email_masked' => $masked ) );
        $mail = new eZMail();
        if ( $rendered !== null && trim( $rendered['body'] ) !== '' )
        {
            $body = $rendered['body'];
            if ( $rendered['subject'] !== null && trim( $rendered['subject'] ) !== '' )
                $subject = trim( $rendered['subject'] );
            if ( $rendered['content_type'] )
                $mail->setContentType( $rendered['content_type'] );
        }
        $site = eZINI::instance();
        $sender = $site->variable( 'MailSettings', 'EmailSender' );
        if ( !$sender )
            $sender = $site->variable( 'MailSettings', 'AdminEmail' );
        $mail->setSender( $sender );
        $mail->setReceiver( $old );
        $mail->setSubject( $subject );
        $mail->setBody( $body );
        $mail->setCategory( 'security' );
        return (bool)eZMailTransport::send( $mail );
    }
}
