<?php
/**
 * File containing the expUserSecurityAlerts class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Security alerts by e-mail to the owner of an account, as Google sends "Security alert": a passkey added or removed
 * (also revoked by an administrator), two-step sign-in turned on
 * or off, a recovery code used, repeated failed sign-ins, the account locked. Each mail says what happened, when and
 * from where (approximately), and links to "Secure your account" (user/security) and Your devices.
 *
 * They are sent from the user audit log: when the sink useraudit stores new rows (expUserAudit::storeRecords()), the
 * rows of this request are handed to notify(), so an event recorded by any code path (the kernel, the two-step
 * extension through expSignInSecondFactor::audit()) alerts the user once, and a batch delivered again from the spool
 * alerts nobody twice (only rows stored for the first time come here; rows older than an hour are skipped).
 *
 * The other security mails stay where they were: "New sign-in" (new device or place, expUserSessionTracker, the user
 * may turn it off) and "Your password was changed" (expPasswordPolicy). These alerts cannot be turned off by the user;
 * the site turns them off with [UserSessionSettings] SecurityAlertMail=disabled. The mail goes through the mail gate,
 * category security; with [MailSettings] Transport=file it is written to var/log/mail and never sent.
 *
 * decide() is a plain function over a row, tested without a database (expUserSecurityAlertsTest).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUserSecurityAlerts
{
    /** Rows older than this many seconds are not alerted (a spool delivered late) */
    const MAX_AGE = 3600;

    /** The template of every alert */
    const TEMPLATE = 'design:user/security_alert_mail.tpl';

    /** @var array the alerts sent in this process (tests): array( kind, user_id, ok ) */
    public static $sent = array();

    /**
     * The settings of the alerts.
     *
     * @return array enabled (bool), threshold (int: failed sign-ins in a row that alert; 0 never)
     */
    public static function settings()
    {
        $ini = eZINI::instance();
        $get = function ( $name, $default ) use ( $ini ) {
            return $ini->hasVariable( 'UserSessionSettings', $name ) ? $ini->variable( 'UserSessionSettings', $name ) : $default;
        };
        return array( 'enabled' => $get( 'SecurityAlertMail', 'enabled' ) !== 'disabled',
                      'threshold' => max( 0, (int)$get( 'FailedSignInAlertThreshold', 5 ) ) );
    }

    /**
     * The alert a row of expuser_audit calls for, or null.
     *
     * @param array $row the row as stored (event_name, target_type, target_id, created, value_after ...)
     * @param array $settings settings()
     * @param int $now
     * @return string|null twofactor_on, twofactor_off, recovery_used, failures, locked, passkey_added, passkey_removed
     */
    public static function decide( array $row, array $settings, $now )
    {
        if ( empty( $settings['enabled'] ) || !isset( $row['event_name'] ) )
            return null;
        if ( !isset( $row['target_type'] ) || $row['target_type'] !== 'user' || empty( $row['target_id'] ) )
            return null;
        if ( isset( $row['created'] ) && (int)$row['created'] < (int)$now - self::MAX_AGE )
            return null;
        $after = isset( $row['value_after'] ) && is_string( $row['value_after'] ) && $row['value_after'] !== '' ? json_decode( $row['value_after'], true ) : array();
        $after = is_array( $after ) ? $after : array();
        switch ( (string)$row['event_name'] )
        {
            case 'access.user.two_factor.enroll':
                return 'twofactor_on';
            case 'access.user.two_factor.remove':
                return 'twofactor_off';
            case 'access.user.two_factor.recovery_used':
                return 'recovery_used';
            case 'access.passkey.add':
            case 'access.passkey.remove':
            case 'access.passkey.revoke':
                // only a passkey actually added or removed; a refused registration is no change
                if ( isset( $row['result'] ) && $row['result'] !== '' && $row['result'] !== 'success' )
                    return null;
                return $row['event_name'] === 'access.passkey.add' ? 'passkey_added' : 'passkey_removed';
            case 'access.user.lock':
                return 'locked';
            case 'access.session.login.failed':
                // once, when the count of failed sign-ins in a row reaches the threshold
                $attempts = isset( $after['attempts'] ) ? (int)$after['attempts'] : 0;
                return $settings['threshold'] > 0 && $attempts === (int)$settings['threshold'] ? 'failures' : null;
        }
        return null;
    }

    /**
     * Sends the alerts the new rows call for (expUserAudit::storeRecords()). One mail per account and kind; a lock and
     * the failures that caused it make one mail (locked).
     *
     * @param array[] $rows rows stored for the first time
     * @param int|null $now
     * @return int mails handed to the transport
     */
    public static function notify( array $rows, $now = null )
    {
        $settings = self::settings();
        if ( !$settings['enabled'] )
            return 0;
        $now = $now === null ? time() : (int)$now;
        $wanted = array();
        foreach ( $rows as $row )
        {
            $kind = self::decide( $row, $settings, $now );
            if ( $kind !== null )
                $wanted[(int)$row['target_id']][$kind] = $row;
        }
        $sent = 0;
        foreach ( $wanted as $userID => $kinds )
        {
            if ( isset( $kinds['locked'] ) )
                unset( $kinds['failures'] );
            $user = eZUser::fetch( $userID );
            if ( !$user instanceof eZUser )
                continue;
            foreach ( $kinds as $kind => $row )
                if ( self::send( $user, $kind, $row ) )
                    $sent++;
        }
        return $sent;
    }

    /**
     * The variables of the mail template.
     *
     * @param eZUser $user
     * @param string $kind
     * @param array $row
     * @return array
     */
    public static function variables( eZUser $user, $kind, array $row )
    {
        $after = isset( $row['value_after'] ) && is_string( $row['value_after'] ) && $row['value_after'] !== '' ? json_decode( $row['value_after'], true ) : array();
        $after = is_array( $after ) ? $after : array();
        $urls = array();
        foreach ( array( 'security_url' => 'user/security/(secure)/1', 'sessions_url' => 'user/sessions', 'password_url' => 'user/forgotpassword' ) as $key => $path )
        {
            $url = $path;
            eZURI::transformURI( $url, false, 'full' );
            $urls[$key] = $url;
        }
        return $urls + array(
            'user' => $user, 'alert' => (string)$kind, 'time' => isset( $row['created'] ) ? (int)$row['created'] : time(),
            'ip' => isset( $row['client_ip'] ) ? expUserAgent::approximateIP( (string)$row['client_ip'] ) : '',
            'method' => isset( $after['method'] ) && is_scalar( $after['method'] ) ? (string)$after['method'] : '',
            'left' => isset( $after['left'] ) && is_numeric( $after['left'] ) ? (int)$after['left'] : -1,
            'attempts' => isset( $after['attempts'] ) && is_numeric( $after['attempts'] ) ? (int)$after['attempts'] : 0,
        );
    }

    /**
     * Sends one alert to the account's address (design:user/security_alert_mail.tpl, category security).
     *
     * @param eZUser $user
     * @param string $kind
     * @param array $row
     * @return bool handed to the transport
     */
    public static function send( eZUser $user, $kind, array $row )
    {
        $email = (string)$user->attribute( 'email' );
        if ( !eZMail::validate( $email ) )
            return false;
        try
        {
            $vars = self::variables( $user, $kind, $row );
            $subject = ezpI18n::tr( 'kernel/user/security/mail', 'Security alert for your account' );
            $body = ezpI18n::tr( 'kernel/user/security/mail', 'There was a security event on your account %login: %event. If it was not you, secure your account at %url.', null,
                                 array( '%login' => $user->attribute( 'login' ), '%event' => $kind, '%url' => $vars['security_url'] ) ) . "\n";
            $mail = new eZMail();
            $rendered = class_exists( 'expMailPreferencesService' ) ? expMailPreferencesService::renderTemplate( self::TEMPLATE, $vars ) : null;
            if ( $rendered !== null && trim( $rendered['body'] ) !== '' )
            {
                $body = $rendered['body'];
                if ( $rendered['subject'] !== null && trim( $rendered['subject'] ) !== '' )
                    $subject = trim( $rendered['subject'] );
                if ( $rendered['content_type'] )
                    $mail->setContentType( $rendered['content_type'] );
            }
            $ini = eZINI::instance();
            $sender = $ini->variable( 'MailSettings', 'EmailSender' );
            if ( !$sender )
                $sender = $ini->variable( 'MailSettings', 'AdminEmail' );
            $mail->setSender( $sender );
            $mail->setReceiver( $email );
            $mail->setSubject( $subject );
            expMailTemplateBody::applyRendered( $mail, $rendered, $body );
            $mail->setCategory( 'security' );
            $ok = (bool)eZMailTransport::send( $mail );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Could not send the security alert: ' . $e->getMessage(), __METHOD__ );
            $ok = false;
        }
        self::$sent[] = array( 'kind' => (string)$kind, 'user_id' => (int)$user->attribute( 'contentobject_id' ), 'ok' => $ok );
        return $ok;
    }
}
?>
