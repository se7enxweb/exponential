<?php
/**
 * File containing the expStaticNotifier class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The notices of static publishing after a publish or a rollback (doc/eng-us/help/static-publishing/
 * notifications-and-webhooks.md):
 *
 *   e-mail   to [StaticPublishSettings] NotifyEmail (default site.ini [MailSettings] AdminEmail), when the target's
 *            Notify[] has failure (a run that failed) or success (a run that committed something). Made like the
 *            kernel's other notices to administrators (expUserSessionTracker::sendAdminMail()): plain text from the
 *            template design:staticpublish/notice_mail.tpl (design:mail/layout_text.tpl), the texts translated,
 *            the sender site.ini [MailSettings] EmailSender (else AdminEmail), the mail category admin (essential,
 *            through the mail gate), and the address of the run's log in the administration
 *   webhook  a POST of JSON (schema exp-static-webhook/1) to the target's Webhook (https only, or http to localhost)
 *            after every run; signed with X-Exp-Static-Signature: sha256=<HMAC of the body> when
 *            [StaticPublishSettings] WebhookSecret names a secret (expSecrets) - its name travels, never its value
 *
 * Neither ever holds a credential or a token. A notice that cannot be sent is logged, never an error of the publish.
 * The senders are options, so tests send nothing.
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticNotifier
{
    const SCHEMA = 'exp-static-webhook/1';
    const SIGNATURE_HEADER = 'X-Exp-Static-Signature';

    private $options;

    /**
    /** The mail category of the notice (mailpreferences.ini [Category_admin]: essential, for the administrators) */
    const MAIL_CATEGORY = 'admin';

    /** The template of the notice */
    const MAIL_TEMPLATE = 'design:staticpublish/notice_mail.tpl';

    /**
     * @param array $options mail (callable( eZMail $mail ) => bool, default eZMailTransport::send()), post
     *        (callable( $url, $body, array $headers ) => int status), email (the address, default from the settings),
     *        secret (the webhook key, default from the settings), site (the site's name, default the public site's,
     *        expMailSenderDetails), admin_url (the administration's address, default
     *        expRegistrationAdminAction::adminBaseURL())
     */
    public function __construct( array $options = array() )
    {
        $this->options = $options + array( 'mail' => array( __CLASS__, 'sendMail' ), 'post' => array( __CLASS__, 'post' ), 'email' => null,
                                           'secret' => null, 'site' => null, 'admin_url' => null );
    }

    /**
     * The payload of a run. Pure.
     *
     * @param expStaticTarget $target
     * @param array $report an exp-static/1 report of a publish or rollback
     * @return array
     */
    public static function payload( expStaticTarget $target, array $report )
    {
        $failed = !empty( $report['errors'] ) || ( isset( $report['state'] ) && $report['state'] === expStaticStatus::FAILED );
        return array(
            'schema' => self::SCHEMA,
            'event' => ( isset( $report['mode'] ) && $report['mode'] === 'rollback' ? 'rollback' : 'publish' ) . ( $failed ? '.failed' : '.done' ),
            'target' => $target->id(),
            'name' => $target->name(),
            'public_url' => $target->publicUrl(),
            'run_id' => isset( $report['run_id'] ) ? (string)$report['run_id'] : '',
            'state' => isset( $report['state'] ) ? (string)$report['state'] : '',
            'commit' => isset( $report['commit_id'] ) ? (string)$report['commit_id'] : '',
            'tag' => isset( $report['tag_name'] ) ? (string)$report['tag_name'] : '',
            'push_state' => isset( $report['push_state'] ) ? (string)$report['push_state'] : '',
            'counts' => isset( $report['counts'] ) ? (array)$report['counts'] : array(),
            'errors' => array_values( array_slice( (array)( isset( $report['errors'] ) ? $report['errors'] : array() ), 0, 10 ) ),
            'at' => gmdate( 'c' ),
        );
    }

    /**
     * Whether a target wants an e-mail about a run. Pure.
     *
     * @param expStaticTarget $target
     * @param array $payload
     * @return bool
     */
    public static function wantsMail( expStaticTarget $target, array $payload )
    {
        $notify = (array)$target->setting( 'Notify' );
        if ( substr( $payload['event'], -7 ) === '.failed' )
            return in_array( 'failure', $notify, true );
        return in_array( 'success', $notify, true ) && $payload['state'] === expStaticStatus::DONE;
    }

    /**
     * Whether an address may take the webhook: https, or http to the host itself. Pure.
     *
     * @param string $url
     * @return bool
     */
    public static function validWebhook( $url )
    {
        $parts = parse_url( trim( (string)$url ) );
        if ( !is_array( $parts ) || empty( $parts['host'] ) || empty( $parts['scheme'] ) )
            return false;
        $scheme = strtolower( $parts['scheme'] );
        return $scheme === 'https' || ( $scheme === 'http' && in_array( strtolower( $parts['host'] ), array( 'localhost', '127.0.0.1', '[::1]' ), true ) );
    }

    /**
     * The signature of a webhook body. Pure.
     *
     * @param string $body
     * @param string $secret
     * @return string sha256=<hex>
     */
    public static function signature( $body, $secret )
    {
        return 'sha256=' . hash_hmac( 'sha256', (string)$body, (string)$secret );
    }

    /**
     * Sends what the target asks for after a run.
     *
     * @param expStaticTarget $target
     * @param array $report
     * @return array mail (bool|null: null when none was wanted), webhook (int status|null), problems
     */
    public function notify( expStaticTarget $target, array $report )
    {
        $payload = self::payload( $target, $report );
        $result = array( 'mail' => null, 'webhook' => null, 'problems' => array() );
        if ( self::wantsMail( $target, $payload ) )
        {
            $to = $this->options['email'] !== null ? (string)$this->options['email'] : self::defaultEmail();
            if ( $to === '' )
                $result['problems'][] = 'no address for the notice: set [StaticPublishSettings] NotifyEmail';
            else if ( class_exists( 'eZMail' ) && !eZMail::validate( $to ) )
            {
                $result['mail'] = false;
                $result['problems'][] = 'the address for the notice is not valid: [StaticPublishSettings] NotifyEmail';
            }
            else
            {
                $site = $this->options['site'] !== null ? (string)$this->options['site'] : self::siteName();
                $logUrl = self::logURL( $payload['run_id'], $this->options['admin_url'] !== null ? (string)$this->options['admin_url'] : self::adminURL() );
                $result['mail'] = (bool)call_user_func( $this->options['mail'], self::message( $to, $payload, $site, $logUrl ) );
                if ( !$result['mail'] )
                    $result['problems'][] = 'the notice could not be sent by e-mail';
            }
        }
        $url = trim( (string)$target->setting( 'Webhook' ) );
        if ( $url !== '' )
        {
            if ( !self::validWebhook( $url ) )
                $result['problems'][] = 'the webhook is not an https address; nothing was sent';
            else
            {
                $body = (string)json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
                $headers = array( 'Content-Type' => 'application/json', 'User-Agent' => 'Exponential static publishing' );
                $secret = $this->options['secret'] !== null ? (string)$this->options['secret'] : self::webhookSecret();
                if ( $secret !== '' )
                    $headers[self::SIGNATURE_HEADER] = self::signature( $body, $secret );
                $result['webhook'] = (int)call_user_func( $this->options['post'], $url, $body, $headers );
                if ( $result['webhook'] < 200 || $result['webhook'] >= 300 )
                    $result['problems'][] = sprintf( 'the webhook answered %d', $result['webhook'] );
            }
        }
        foreach ( $result['problems'] as $problem )
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeWarning( 'Static publishing, ' . $target->id() . ': ' . $problem, __METHOD__ );
        return $result;
    }

    /**
     * The variables of the notice's template. Pure.
     *
     * @param array $payload
     * @param string $site
     * @param string $logUrl the address of the run's log, '' when unknown
     * @return array
     */
    public static function mailVariables( array $payload, $site, $logUrl )
    {
        return array(
            'failed' => substr( $payload['event'], -7 ) === '.failed',
            'kind' => strpos( $payload['event'], 'rollback.' ) === 0 ? 'rollback' : 'publish',
            'target_id' => (string)$payload['target'],
            'target_name' => (string)$payload['name'],
            'run_id' => (string)$payload['run_id'],
            'state' => (string)$payload['state'],
            'commit' => (string)$payload['commit'],
            'tag' => (string)$payload['tag'],
            'push_state' => (string)$payload['push_state'],
            'public_url' => (string)$payload['public_url'],
            'errors' => array_values( array_map( 'strval', (array)$payload['errors'] ) ),
            'log_url' => (string)$logUrl,
            'site_name' => (string)$site,
        );
    }

    /**
     * The subject and the plain text of the notice without its template (the template is missing or empty): the
     * same rows, translated in the same contexts.
     *
     * @param array $payload
     * @param string $site
     * @param string $logUrl
     * @return string[] subject, body
     */
    public static function mail( array $payload, $site, $logUrl = '' )
    {
        $v = self::mailVariables( $payload, $site, $logUrl );
        $sp = 'design/admin/staticpublish';
        $mailContext = 'design/standard/staticpublish/mail';
        $label = $v['target_name'] !== '' ? $v['target_name'] . ' (' . $v['target_id'] . ')' : $v['target_id'];
        $subject = $v['failed'] ? self::tr( $mailContext, 'Static publishing of %target failed on %site', array( '%target' => $label, '%site' => $v['site_name'] ) )
                                : self::tr( $mailContext, 'Static publishing of %target succeeded on %site', array( '%target' => $label, '%site' => $v['site_name'] ) );
        $rows = array( array( self::tr( $sp, 'Target' ), $label ), array( self::tr( $sp, 'Id' ), $v['run_id'] ),
                       array( self::tr( 'design/admin/migration', 'Result' ), self::resultText( $v ) ) );
        if ( $v['failed'] && $v['state'] === 'unchanged' )
            $rows[] = array( self::tr( $sp, 'Changes' ), self::tr( $sp, 'nothing changed' ) );
        if ( $v['commit'] !== '' )
            $rows[] = array( self::tr( $sp, 'Commit' ), $v['commit'] );
        if ( $v['tag'] !== '' )
            $rows[] = array( self::tr( $sp, 'Tag' ), $v['tag'] );
        $rows[] = array( self::tr( $sp, 'Push' ), self::pushText( $v['push_state'] ) );
        if ( $v['public_url'] !== '' )
            $rows[] = array( self::tr( $sp, 'Public address' ), $v['public_url'] );
        $lines = array( $subject, '' );
        foreach ( $rows as $row )
            $lines[] = $row[0] . ': ' . str_replace( array( "\r", "\n" ), ' ', (string)$row[1] );
        if ( $v['errors'] )
        {
            $lines[] = '';
            $lines[] = self::tr( 'design/admin/setup/cronjobs', 'Errors' ) . ':';
            foreach ( $v['errors'] as $error )
                $lines[] = '- ' . str_replace( array( "\r", "\n" ), ' ', $error );
        }
        if ( $v['log_url'] !== '' )
        {
            $lines[] = '';
            $lines[] = self::tr( 'design/admin/content/job', 'Log' ) . ': ' . $v['log_url'];
        }
        $lines[] = '';
        $lines[] = '--';
        $lines[] = self::tr( $mailContext, 'This mail was sent by %site because the static publishing target %target asks for it.',
                             array( '%site' => $v['site_name'], '%target' => $v['target_id'] ) );
        return array( $subject, implode( "\n", $lines ) . "\n" );
    }

    /**
     * The notice as a mail: plain text from the template (else mail()), sender, recipient, category admin.
     *
     * @param string $to
     * @param array $payload
     * @param string $site
     * @param string $logUrl
     * @return eZMail
     */
    public static function message( $to, array $payload, $site, $logUrl )
    {
        list( $subject, $body ) = self::mail( $payload, $site, $logUrl );
        $rendered = class_exists( 'expMailPreferencesService' ) ? expMailPreferencesService::renderTemplate( self::MAIL_TEMPLATE, self::mailVariables( $payload, $site, $logUrl ) ) : null;
        if ( $rendered !== null && trim( $rendered['body'] ) !== '' )
        {
            $body = trim( $rendered['body'] ) . "\n";
            if ( $rendered['subject'] !== null && trim( $rendered['subject'] ) !== '' )
                $subject = trim( $rendered['subject'] );
        }
        $ini = eZINI::instance();
        $sender = $ini->variable( 'MailSettings', 'EmailSender' );
        if ( !$sender )
            $sender = $ini->variable( 'MailSettings', 'AdminEmail' );
        $mail = new eZMail();
        $mail->setSender( $sender );
        $mail->setReceiver( (string)$to );
        $mail->setSubject( $subject );
        $mail->setBody( $body );
        $mail->setCategory( self::MAIL_CATEGORY );
        return $mail;
    }

    /** @return string the result of the run in words: failed, published, rolled back or nothing changed */
    protected static function resultText( array $v )
    {
        $sp = 'design/admin/staticpublish';
        if ( $v['failed'] )
            return self::tr( $sp, 'failed' );
        if ( $v['state'] === 'unchanged' )
            return self::tr( $sp, 'nothing changed' );
        return $v['kind'] === 'rollback' ? self::tr( $sp, 'rolled back' ) : self::tr( $sp, 'published' );
    }

    /** @return string the push state in words, as the history of the administration shows it */
    protected static function pushText( $state )
    {
        $sp = 'design/admin/staticpublish';
        switch ( (string)$state )
        {
            case 'pushed': return self::tr( $sp, 'pushed' );
            case 'pending': return self::tr( $sp, 'push pending' );
            case 'no-remote': return self::tr( $sp, 'no remote' );
            default: return self::tr( $sp, 'not pushed' );
        }
    }

    /** @return string a text translated by ezpI18n, the English text with its arguments when there is no ezpI18n */
    protected static function tr( $context, $source, array $arguments = array() )
    {
        if ( class_exists( 'ezpI18n' ) )
            return (string)ezpI18n::tr( $context, $source, null, $arguments );
        return strtr( $source, $arguments );
    }

    /**
     * The absolute address of a run's log in the administration (staticpublish/log/<run id>). Pure.
     *
     * @param string $runId
     * @param string $adminUrl the administration's address
     * @return string '' without one of them
     */
    public static function logURL( $runId, $adminUrl )
    {
        $adminUrl = rtrim( trim( (string)$adminUrl ), '/' );
        if ( $adminUrl === '' || (string)$runId === '' )
            return '';
        return $adminUrl . '/staticpublish/log/' . rawurlencode( (string)$runId );
    }

    /** @return string the administration's address (expRegistrationAdminAction::adminBaseURL()), '' when unknown */
    public static function adminURL()
    {
        try
        {
            return class_exists( 'expRegistrationAdminAction' ) ? (string)expRegistrationAdminAction::adminBaseURL() : '';
        }
        catch ( Throwable $e )
        {
            return '';
        }
    }

    /** @return string [StaticPublishSettings] NotifyEmail, else site.ini [MailSettings] AdminEmail */
    public static function defaultEmail()
    {
        $ini = eZINI::instance( 'staticcache.ini' );
        if ( $ini->hasVariable( 'StaticPublishSettings', 'NotifyEmail' ) && trim( (string)$ini->variable( 'StaticPublishSettings', 'NotifyEmail' ) ) !== '' )
            return trim( (string)$ini->variable( 'StaticPublishSettings', 'NotifyEmail' ) );
        $site = eZINI::instance();
        return $site->hasVariable( 'MailSettings', 'AdminEmail' ) ? trim( (string)$site->variable( 'MailSettings', 'AdminEmail' ) ) : '';
    }

    /** @return string the value of the secret [StaticPublishSettings] WebhookSecret names, '' without one */
    public static function webhookSecret()
    {
        $ini = eZINI::instance( 'staticcache.ini' );
        $name = $ini->hasVariable( 'StaticPublishSettings', 'WebhookSecret' ) ? trim( (string)$ini->variable( 'StaticPublishSettings', 'WebhookSecret' ) ) : '';
        if ( $name === '' || !class_exists( 'expSecrets' ) )
            return '';
        try
        {
            $value = expSecrets::get( $name );
            return is_string( $value ) ? $value : '';
        }
        catch ( Throwable $e )
        {
            return '';
        }
    }

    /**
     * @return string the name of the public site, as every mail of the kernel names it (expMailSenderDetails: never
     *         the administration's), else site.ini [SiteSettings] SiteName
     */
    public static function siteName()
    {
        if ( class_exists( 'expMailSenderDetails' ) )
        {
            try
            {
                $details = expMailSenderDetails::get();
                if ( isset( $details['name'] ) && trim( (string)$details['name'] ) !== '' )
                    return (string)$details['name'];
            }
            catch ( Throwable $e )
            {
            }
        }
        $ini = eZINI::instance();
        return $ini->hasVariable( 'SiteSettings', 'SiteName' ) ? (string)$ini->variable( 'SiteSettings', 'SiteName' ) : '';
    }

    /** Sends the notice through the installation's mail transport (and so through the mail gate). */
    public static function sendMail( eZMail $mail )
    {
        return (bool)eZMailTransport::send( $mail );
    }

    /** Posts a body; the status of the answer, 0 without one. */
    public static function post( $url, $body, array $headers )
    {
        if ( !function_exists( 'curl_init' ) )
            return 0;
        $lines = array();
        foreach ( $headers as $name => $value )
            $lines[] = $name . ': ' . $value;
        $ch = curl_init( $url );
        curl_setopt_array( $ch, array( CURLOPT_POST => true, CURLOPT_POSTFIELDS => (string)$body, CURLOPT_HTTPHEADER => $lines, CURLOPT_RETURNTRANSFER => true,
                                       CURLOPT_TIMEOUT => 10, CURLOPT_CONNECTTIMEOUT => 5, CURLOPT_FOLLOWLOCATION => false,
                                       CURLOPT_PROTOCOLS => CURLPROTO_HTTPS | CURLPROTO_HTTP ) );
        curl_exec( $ch );
        $status = (int)curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        if ( PHP_VERSION_ID < 80000 ) curl_close( $ch ); // no effect since PHP 8.0, deprecated in 8.5
        return $status;
    }
}
