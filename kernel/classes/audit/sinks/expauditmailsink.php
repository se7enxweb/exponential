<?php
/**
 * The e-mail sink (doc/bc/6.0/audit.md, "E-mail"): events at MinSeverity (critical) or matching Events[] are mailed
 * through eZMail, one mail per alert or event and recipient, with the console link and nothing beyond what the
 * record holds after the privacy rules. Always spooled: mail is sent by the audit cronjob part, so a request never
 * waits on SMTP.
 *
 * Recipients (expAuditMailRecipients): an alert's rule's [AlertRule_<rule>] Recipients[], else [AuditAlertSettings]
 * Recipients[], else [AuditSink_mail] Receivers[], else the site's AdminEmail; each list may name addresses, named
 * groups ([AlertRecipients_<name>]), users, logins, user groups and roles, resolved at send time, deduplicated,
 * disabled users left out. Nothing in the record ever becomes a recipient. At most one mail per rule (or, for
 * another event, per name) and recipient within Throttle seconds.
 *
 * [AuditSink_mail] Transport names the transport: empty = the kernel's (site.ini [MailSettings] Transport through
 * eZMailTransport::send(), which honours DebugSending); a class extending eZMailTransport = that one (tests use a
 * transport that writes into their own directory).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditMailSink extends expAuditSinkBase
{
    /** @var array[] The mails of the last deliver(): to, subject, body (tests, status) */
    public $sent = array();

    /** @var int Mails left out by the throttle in the last deliver() */
    public $throttled = 0;

    /** @var string|null */
    public $lastError = null;

    /** @var int|null Fixed clock (tests) */
    public $now = null;

    protected function defaults()
    {
        return array( 'Receivers' => array(), 'MinSeverity' => 'critical', 'Events' => array( 'system.audit.*', 'access.role.assign' ),
                      'Throttle' => '900', 'Transport' => '' );
    }

    public function problem()
    {
        if ( !class_exists( 'eZMail' ) )
            return 'eZMail is not available';
        $t = trim( (string)$this->setting( 'Transport' ) );
        if ( $t !== '' && ( !class_exists( $t ) || !is_subclass_of( $t, 'eZMailTransport' ) ) )
            return "Transport=$t is not a class extending eZMailTransport";
        if ( !$this->receivers() )
            return 'no recipient: set [AuditAlertSettings] Recipients[] or site.ini [MailSettings] AdminEmail';
        return '';
    }

    /**
     * The addresses a record is mailed to.
     *
     * @param array|null $record null: the default list
     * @return string[]
     */
    public function receivers( ?array $record = null )
    {
        $s = expAuditMailRecipients::specsFor( $record );
        return expAuditMailRecipients::addresses( $s['specs'] );
    }

    public function batchSize()
    {
        return 20;
    }

    /**
     * Mails the records (throttled ones count as delivered: they are not retried).
     *
     * @param array[] $records
     * @return int
     */
    public function deliver( array $records )
    {
        $this->sent = array();
        $this->throttled = 0;
        $this->lastError = null;
        if ( !class_exists( 'eZMail' ) )
        {
            $this->lastError = $this->problem();
            return 0;
        }
        $state = $this->throttleState();
        $now = $this->now !== null ? (int)$this->now : time();
        $throttle = max( 0, (int)$this->setting( 'Throttle' ) );
        $n = 0;
        foreach ( $records as $r )
        {
            $receivers = $this->receivers( $r );
            if ( !$receivers )
            {
                $this->lastError = 'no recipient resolved for ' . self::throttleKey( $r, '' );
                break;
            }
            list( $subject, $body ) = $this->compose( $r );
            $failed = false;
            foreach ( $receivers as $to )
            {
                $key = self::throttleKey( $r, $to );
                if ( $throttle > 0 && isset( $state[$key] ) && $now - $state[$key] < $throttle )
                {
                    $this->throttled++;
                    continue;
                }
                if ( !$this->send( array( $to ), $subject, $body ) )
                {
                    $failed = true;
                    break;
                }
                $state[$key] = $now;
                $this->sent[] = array( 'to' => array( $to ), 'subject' => $subject, 'body' => $body );
            }
            if ( $failed )
                break; // retried later; the recipients mailed already are throttled
            $n++;
        }
        foreach ( $state as $k => $t )
            if ( $now - $t > max( $throttle, 86400 ) )
                unset( $state[$k] );
        $this->saveThrottleState( $state );
        return $n;
    }

    /** @return string The throttle key: rule (an alert) or name (any other event), and the recipient */
    public static function throttleKey( array $r, $recipient )
    {
        if ( isset( $r['name'] ) && $r['name'] === 'system.audit.alert' && isset( $r['after']['rule'] ) )
            return 'alert:' . $r['after']['rule'] . ':' . strtolower( $recipient );
        return 'event:' . ( isset( $r['name'] ) ? $r['name'] : '' ) . ':' . strtolower( $recipient );
    }

    /**
     * The subject and text of the mail of a record.
     *
     * @param array $r
     * @return array( subject, body )
     */
    public function compose( array $r )
    {
        $name = isset( $r['name'] ) ? $r['name'] : '';
        $severity = isset( $r['severity'] ) ? $r['severity'] : '';
        $site = expAuditWebhookSink::siteName();
        if ( $name === 'system.audit.alert' && isset( $r['after']['rule'] ) )
            $subject = sprintf( '[%s audit] %s: alert %s', $site !== '' ? $site : 'Exponential', $severity, $r['after']['rule'] );
        else
            $subject = sprintf( '[%s audit] %s: %s', $site !== '' ? $site : 'Exponential', $severity, $name );
        $lines = array();
        $lines[] = 'Event:    ' . $name . ( isset( $r['result'] ) ? ' (' . $r['result'] . ')' : '' );
        $lines[] = 'Time:     ' . ( isset( $r['time'] ) ? $r['time'] : '' ) . ' (UTC)';
        $lines[] = 'Severity: ' . $severity;
        $lines[] = 'Channel:  ' . ( isset( $r['channel'] ) ? $r['channel'] : '' ) . ( isset( $r['seq'] ) ? ', seq ' . $r['seq'] : '' );
        if ( isset( $r['after']['rule'] ) && $name === 'system.audit.alert' )
        {
            $a = $r['after'];
            $lines[] = 'Rule:     ' . $a['rule'] . ( isset( $a['message'] ) ? ' - ' . $a['message'] : '' );
            $lines[] = 'Group:    ' . ( isset( $a['group'] ) ? $a['group'] : '' );
            $lines[] = 'Count:    ' . ( isset( $a['count'] ) ? $a['count'] : '' ) . ( !empty( $a['window'] ) ? ' within ' . $a['window'] . ' s' : '' );
            $lines[] = 'Events:   ' . ( isset( $a['first'] ) ? $a['first'] : '' ) . ( isset( $a['last'] ) && $a['last'] !== ( isset( $a['first'] ) ? $a['first'] : null ) ? ' ... ' . $a['last'] : '' );
        }
        else
        {
            $lines[] = 'Actor:    ' . expAuditReader::actorText( $r );
            $lines[] = 'Object:   ' . expAuditReader::objectText( $r );
        }
        $lines[] = 'Request:  ' . ( isset( $r['request']['id'] ) ? $r['request']['id'] : '' );
        $lines[] = 'Event id: ' . ( isset( $r['id'] ) ? $r['id'] : '' );
        $link = self::consoleLink( isset( $r['id'] ) ? $r['id'] : '' );
        if ( $link !== '' )
            $lines[] = 'Console:  ' . $link;
        $lines[] = '';
        $lines[] = 'This message was sent by the audit of ' . ( $site !== '' ? $site : 'Exponential' ) . '. Settings: audit.ini [AuditSink_mail].';
        return array( $subject, implode( "\n", $lines ) . "\n" );
    }

    /** @return string The console link of an event, from site.ini [SiteSettings] SiteURL of the admin, '' when unknown */
    public static function consoleLink( $id )
    {
        try
        {
            if ( !class_exists( 'eZINI' ) || expAuditConfig::isOverridden() )
                return '';
            $url = trim( (string)eZINI::instance()->variable( 'SiteSettings', 'SiteURL' ) );
            if ( $url === '' )
                return '';
            if ( !preg_match( '#^https?://#', $url ) )
                $url = 'https://' . $url;
            return rtrim( $url, '/' ) . '/audit/event/' . rawurlencode( $id );
        }
        catch ( Throwable $e )
        {
            return '';
        }
    }

    /** Sends one mail through the configured transport. */
    protected function send( array $receivers, $subject, $body )
    {
        try
        {
            $mail = new eZMail();
            foreach ( $receivers as $i => $to )
            {
                if ( $i === 0 )
                    $mail->setReceiver( $to );
                else
                    $mail->addReceiver( $to );
            }
            $sender = '';
            if ( class_exists( 'eZINI' ) && !expAuditConfig::isOverridden() )
            {
                $ini = eZINI::instance();
                $sender = trim( (string)$ini->variable( 'MailSettings', 'EmailSender' ) ) ?: trim( (string)$ini->variable( 'MailSettings', 'AdminEmail' ) );
            }
            if ( $sender !== '' )
                $mail->setSender( $sender );
            $mail->setSubject( $subject );
            $mail->setBody( $body );
            $mail->setCategory( 'admin' );
            $transport = trim( (string)$this->setting( 'Transport' ) );
            if ( $transport !== '' )
            {
                $t = new $transport();
                $ok = $t->sendMail( $mail );
            }
            else
            {
                $ok = eZMailTransport::send( $mail );
            }
            if ( !$ok )
                $this->lastError = 'the mail transport refused the mail';
            return (bool)$ok;
        }
        catch ( Throwable $e )
        {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /** @return string The throttle state file */
    protected function throttlePath()
    {
        return expAuditSinkRegistry::spoolDir() . '/mail.throttle.json';
    }

    protected function throttleState()
    {
        $s = @file_get_contents( $this->throttlePath() );
        $s = $s !== false ? json_decode( $s, true ) : null;
        return is_array( $s ) ? $s : array();
    }

    protected function saveThrottleState( array $state )
    {
        $dir = dirname( $this->throttlePath() );
        if ( !expAuditWriter::ensureDirectory( $dir ) )
            return;
        $path = $this->throttlePath();
        $created = !is_file( $path );
        $tmp = $path . '.' . getmypid() . '.tmp';
        if ( @file_put_contents( $tmp, json_encode( $state ) ) !== false )
            @rename( $tmp, $path );
        if ( $created )
            expAuditWriter::ownLikeParent( $path, 0640 );
    }
}
