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
 *            Notify[] has failure (a run that failed) or success (a run that committed something)
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
     * @param array $options mail (callable( $to, $subject, $body ) => bool), post (callable( $url, $body, array
     *        $headers ) => int status), email (the address, default from the settings), secret (the webhook key,
     *        default from the settings), site (the site's name)
     */
    public function __construct( array $options = array() )
    {
        $this->options = $options + array( 'mail' => array( __CLASS__, 'sendMail' ), 'post' => array( __CLASS__, 'post' ), 'email' => null,
                                           'secret' => null, 'site' => null );
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
            else
            {
                list( $subject, $body ) = self::mail( $payload, $this->options['site'] !== null ? (string)$this->options['site'] : self::siteName() );
                $result['mail'] = (bool)call_user_func( $this->options['mail'], $to, $subject, $body );
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
     * The subject and text of the e-mail. Pure.
     *
     * @param array $payload
     * @param string $site
     * @return string[] subject, body
     */
    public static function mail( array $payload, $site )
    {
        $failed = substr( $payload['event'], -7 ) === '.failed';
        $subject = sprintf( '[%s] Static publishing %s: %s', $site !== '' ? $site : 'Exponential', $payload['target'], $failed ? 'failed' : 'published' );
        $lines = array( $failed ? 'A publish of the static copy failed.' : 'The static copy was published.', '',
                        'Target:  ' . $payload['target'] . ( $payload['name'] !== '' ? ' (' . $payload['name'] . ')' : '' ),
                        'Run:     ' . $payload['run_id'], 'State:   ' . $payload['state'], 'Push:    ' . $payload['push_state'] );
        if ( $payload['tag'] !== '' )
            $lines[] = 'Tag:     ' . $payload['tag'];
        if ( $payload['public_url'] !== '' )
            $lines[] = 'Address: ' . $payload['public_url'];
        if ( $payload['errors'] )
        {
            $lines[] = '';
            $lines[] = 'Errors:';
            foreach ( $payload['errors'] as $error )
                $lines[] = '- ' . $error;
        }
        $lines[] = '';
        $lines[] = 'Setup > Static publishing has the history and the log of the run.';
        return array( $subject, implode( "\n", $lines ) . "\n" );
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

    /** @return string the site's name */
    public static function siteName()
    {
        $ini = eZINI::instance();
        return $ini->hasVariable( 'SiteSettings', 'SiteName' ) ? (string)$ini->variable( 'SiteSettings', 'SiteName' ) : '';
    }

    /** Sends an e-mail through the installation's mail transport. */
    public static function sendMail( $to, $subject, $body )
    {
        $mail = new eZMail();
        if ( !$mail->validate( $to ) )
            return false;
        $mail->setReceiver( $to );
        $sender = eZINI::instance()->variable( 'MailSettings', 'EmailSender' ) ?: eZINI::instance()->variable( 'MailSettings', 'AdminEmail' );
        $mail->setSender( $sender );
        $mail->setSubject( $subject );
        $mail->setBody( $body );
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
