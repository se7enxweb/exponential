<?php
/**
 * File containing the expMailGate class.
 *
 * The central mail gate. eZMailTransport::send() hands every mail to dispatch() before the transport (file,
 * sendmail, SMTP or an extension's) gets it:
 *
 *  - Mail without a category (eZMail::setCategory() or the X-Exp-Mail-Category header) is sent exactly as before
 *    and logged: as "essential" when its sender is listed in [GateSettings] EssentialSenders[], else as
 *    "uncategorised" with the file that sent it, so it can be given a category.
 *  - Mail of an unknown category is sent as before and logged.
 *  - Essential mail (account security, orders, legal, admin alerts) is sent as before, with the category header.
 *  - Optional mail goes only to the recipients the preferences allow (expMailPreferences::decision(): suppression,
 *    master switch, category on, no pending double opt-in). Each allowed recipient gets the mail on its own (so the
 *    links are its own) with the footer (why, manage link, unsubscribe link, the organisation's name and postal
 *    address) and the RFC 8058 headers List-Unsubscribe and List-Unsubscribe-Post. Blocked recipients are logged.
 *    A mail whose recipients are all blocked is not sent and counts as delivered (true).
 *
 * The log ([GateSettings] LogFile, JSON lines) holds no address: a user id or the start of the address hash.
 *
 * \code
 * $mail = new eZMail();
 * $mail->setCategory( 'newsletter' );
 * ...
 * eZMailTransport::send( $mail );           // goes through dispatch()
 * expMailGate::lastResult();                // sent, blocked, the reasons
 * \endcode
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMailGate
{
    const HEADER = 'X-Exp-Mail-Category';

    /** @var array|null the result of the last dispatch() */
    protected static $lastResult = null;

    /** @var string|null test override of the log file (absolute) */
    protected static $logFile = null;

    /**
     * Sends a mail through the gate.
     *
     * @param eZMail $mail
     * @param eZMailTransport $transport the transport object that sends
     * @return bool what the transport answered; true when the gate blocked every recipient
     */
    public static function dispatch( eZMail $mail, eZMailTransport $transport )
    {
        self::$lastResult = array( 'category' => null, 'decision' => 'sent', 'sent' => 0, 'blocked' => array(), 'result' => null );
        $sent = false;
        try
        {
            $categoryId = self::category( $mail );
            if ( $categoryId === null )
            {
                $sender = self::callingSender();
                $essential = self::isEssentialSender( $mail, $sender );
                self::log( $essential ? 'essential' : 'uncategorised', null, null, $essential ? 'essential_sender' : 'no_category', $sender );
                self::$lastResult['decision'] = $essential ? 'essential' : 'uncategorised';
                $sent = true;
                return self::$lastResult['result'] = $transport->sendMail( $mail );
            }
            $category = expMailCategoryRegistry::instance()->get( $categoryId );
            self::$lastResult['category'] = $categoryId;
            if ( !$category )
            {
                self::log( 'uncategorised', $categoryId, null, 'unknown_category', self::callingSender() );
                self::$lastResult['decision'] = 'unknown_category';
                $sent = true;
                return self::$lastResult['result'] = $transport->sendMail( $mail );
            }
            $mail->setExtraHeader( self::HEADER, $category->identifier );
            if ( $category->essential )
            {
                self::log( 'sent', $category->identifier, null, 'essential', null );
                self::$lastResult['decision'] = 'essential';
                $sent = true;
                return self::$lastResult['result'] = $transport->sendMail( $mail );
            }
            return self::dispatchOptional( $mail, $transport, $category, $sent );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Mail gate: ' . $e->getMessage(), __METHOD__ );
            if ( $sent )
                return isset( self::$lastResult['result'] ) ? (bool)self::$lastResult['result'] : false;
            // optional mail is not sent when the gate cannot decide; everything else goes as before
            $categoryId = $mail->category();
            $category = $categoryId !== null ? expMailCategoryRegistry::instance()->get( $categoryId ) : null;
            self::log( 'error', $categoryId, null, substr( $e->getMessage(), 0, 200 ), null );
            if ( $category && !$category->essential )
                return false;
            return $transport->sendMail( $mail );
        }
    }

    /**
     * Who may get the mail.
     *
     * @param eZMail $mail
     * @return array allow (bool: someone may get it), reason (allow, no_category, unknown_category, essential, or the
     *               reason when nobody may), category (identifier or null), essential (bool),
     *               recipients (list of email, name, field to|cc|bcc, decision)
     */
    public static function check( eZMail $mail )
    {
        $categoryId = self::category( $mail );
        $out = array( 'allow' => true, 'reason' => 'allow', 'category' => $categoryId, 'essential' => false, 'recipients' => array() );
        $list = self::recipientsOf( $mail );
        if ( $categoryId === null )
        {
            $out['reason'] = 'no_category';
            foreach ( $list as $r )
                $out['recipients'][] = $r + array( 'decision' => 'allow' );
            return $out;
        }
        $category = expMailCategoryRegistry::instance()->get( $categoryId );
        if ( !$category || $category->essential )
        {
            $out['reason'] = $category ? 'essential' : 'unknown_category';
            $out['essential'] = (bool)$category;
            foreach ( $list as $r )
                $out['recipients'][] = $r + array( 'decision' => 'allow' );
            return $out;
        }
        $gate = self::enabled();
        $reasons = array();
        $any = false;
        foreach ( $list as $r )
        {
            $recipient = expMailRecipient::fromAddress( $r['email'] );
            $decision = $recipient ? expMailPreferences::forRecipient( $recipient )->decision( $category->identifier ) : 'invalid_address';
            if ( !$gate && $decision !== 'invalid_address' && $decision !== 'suppressed' )
                $decision = 'allow';
            $out['recipients'][] = $r + array( 'decision' => $decision );
            if ( $decision === 'allow' )
                $any = true;
            else
                $reasons[$decision] = true;
        }
        if ( !$list )
            $reasons['no_recipient'] = true;
        $out['allow'] = $any;
        if ( !$any )
            $out['reason'] = implode( ',', array_keys( $reasons ) );
        return $out;
    }

    /**
     * Adds the footer, the unsubscribe and manage links and the List-Unsubscribe headers for one recipient. Call
     * it on a mail that goes to this recipient alone (dispatch() does).
     *
     * @param eZMail $mail
     * @param expMailRecipient|null $recipient null: a footer with the "send me a link" page and no List-Unsubscribe
     * @param expMailCategory|string $category
     */
    public static function decorate( eZMail $mail, $recipient, $category )
    {
        if ( is_string( $category ) )
            $category = expMailCategoryRegistry::instance()->get( $category );
        if ( !$category instanceof expMailCategory )
            return;
        $mail->setExtraHeader( self::HEADER, $category->identifier );
        if ( $category->essential )
            return;
        $links = array( 'manage' => expMailToken::baseURL() . '/mailpreferences/request', 'unsubscribe' => '' );
        if ( $recipient instanceof expMailRecipient && ( $recipient->userId() > 0 || $recipient->email() !== '' ) )
        {
            $links['manage'] = expMailToken::url( 'manage', expMailToken::create( $recipient, 'manage', null, self::footerTTL( 'manage' ) ) );
            $links['unsubscribe'] = expMailToken::url( 'unsubscribe', expMailToken::create( $recipient, 'unsubscribe', $category->identifier, self::footerTTL( 'unsubscribe' ) ) );
            $mail->setExtraHeader( 'List-Unsubscribe', '<' . $links['unsubscribe'] . '>' );
            $mail->setExtraHeader( 'List-Unsubscribe-Post', 'List-Unsubscribe=One-Click' );
        }
        $isHTML = stripos( (string)$mail->contentType(), 'html' ) !== false;
        $footer = self::footer( $category, $links, $isHTML );
        $body = $mail->body( false );
        if ( isset( $mail->Mail->body ) && $mail->Mail->body instanceof ezcMailPart && !is_string( $body ) )
        {
            // a body set as an ezcMailPart (multipart): the footer goes into its text parts
            $mail->Mail->body = self::decoratePart( self::deepClone( $mail->Mail->body ), $category, $links );
            return;
        }
        $mail->setBody( self::appendFooter( is_string( $body ) ? $body : '', $footer, $isHTML ) );
    }

    /** @return array|null the result of the last dispatch(): category, decision, sent, blocked (reasons), result */
    public static function lastResult()
    {
        return self::$lastResult;
    }

    /** @return bool [GateSettings] Gate=enabled */
    public static function enabled()
    {
        $ini = eZINI::instance( 'mailpreferences.ini' );
        return !$ini->hasVariable( 'GateSettings', 'Gate' ) || $ini->variable( 'GateSettings', 'Gate' ) !== 'disabled';
    }

    /**
     * The category a mail declares: eZMail::setCategory(), else its X-Exp-Mail-Category header.
     *
     * @param eZMail $mail
     * @return string|null
     */
    public static function category( eZMail $mail )
    {
        $id = $mail->category();
        if ( $id === null )
        {
            foreach ( (array)$mail->extraHeaders() as $k => $h )
            {
                $name = is_array( $h ) && isset( $h['name'] ) ? $h['name'] : ( is_string( $k ) ? $k : '' );
                $value = is_array( $h ) && isset( $h['content'] ) ? $h['content'] : ( is_string( $h ) ? $h : '' );
                if ( strcasecmp( (string)$name, self::HEADER ) === 0 )
                    $id = (string)$value;
            }
        }
        if ( $id === null )
            return null;
        $id = expMailCategory::cleanIdentifier( $id );
        return $id === '' ? null : $id;
    }

    // ------------------------------------------------------------------ the log

    /** @return string the log file (absolute or relative to the installation) */
    public static function logFile()
    {
        if ( self::$logFile !== null )
            return self::$logFile;
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $rel = $ini->hasVariable( 'GateSettings', 'LogFile' ) ? trim( (string)$ini->variable( 'GateSettings', 'LogFile' ), '/' ) : 'log/mailgate.jsonl';
        return eZSys::varDirectory() . '/' . ( $rel !== '' ? $rel : 'log/mailgate.jsonl' );
    }

    /** Tests only: another log file; null resets. */
    public static function setLogFileForTest( $file )
    {
        self::$logFile = $file;
    }

    /**
     * @param string $decision sent, blocked, essential, uncategorised, error, from_fallback
     * @param string|null $category
     * @param expMailRecipient|null $recipient
     * @param string $reason
     * @param string|null $sender the file or class that sent (never an address)
     */
    protected static function log( $decision, $category, $recipient, $reason, $sender )
    {
        $entry = array( 't' => time(), 'd' => $decision, 'c' => $category, 'why' => $reason );
        if ( $recipient instanceof expMailRecipient )
            $entry['r'] = $recipient->logKey();
        if ( $sender !== null )
            $entry['s'] = $sender;
        $file = self::logFile();
        $dir = dirname( $file );
        if ( !is_dir( $dir ) )
            @mkdir( $dir, 0775, true );
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $max = $ini->hasVariable( 'GateSettings', 'MaxLogSize' ) ? (int)$ini->variable( 'GateSettings', 'MaxLogSize' ) : 5242880;
        clearstatcache( true, $file );
        if ( $max > 0 && is_file( $file ) && filesize( $file ) > $max )
            @rename( $file, $file . '.1' );
        $new = !is_file( $file );
        @file_put_contents( $file, json_encode( $entry, JSON_UNESCAPED_SLASHES ) . "\n", FILE_APPEND | LOCK_EX );
        if ( $new && class_exists( 'expAuditWriter' ) )
            expAuditWriter::ownLikeParent( $file, 0664 );
    }

    /**
     * Counts of the log since a time.
     *
     * @param int $since
     * @return array sent, blocked, essential, uncategorised, error, from_fallback, by_category (category => sent/blocked),
     *               uncategorised_senders (sender => count), from_fallback_senders (sender => count), last (time of the newest entry)
     */
    public static function stats( $since )
    {
        $out = array( 'sent' => 0, 'blocked' => 0, 'essential' => 0, 'uncategorised' => 0, 'error' => 0, 'from_fallback' => 0, 'by_category' => array(),
                      'uncategorised_senders' => array(), 'blocked_reasons' => array(), 'from_fallback_senders' => array(), 'last' => 0 );
        foreach ( array( self::logFile() . '.1', self::logFile() ) as $file )
        {
            if ( !is_file( $file ) )
                continue;
            $h = @fopen( $file, 'r' );
            if ( !$h )
                continue;
            while ( ( $line = fgets( $h ) ) !== false )
            {
                $e = json_decode( $line, true );
                if ( !is_array( $e ) || !isset( $e['t'], $e['d'] ) || $e['t'] < $since )
                    continue;
                $d = $e['d'];
                if ( isset( $out[$d] ) && is_int( $out[$d] ) )
                    $out[$d]++;
                $out['last'] = max( $out['last'], (int)$e['t'] );
                $c = isset( $e['c'] ) && $e['c'] !== null ? (string)$e['c'] : '';
                if ( $c !== '' && ( $d === 'sent' || $d === 'blocked' ) )
                {
                    if ( !isset( $out['by_category'][$c] ) )
                        $out['by_category'][$c] = array( 'sent' => 0, 'blocked' => 0 );
                    $out['by_category'][$c][$d]++;
                }
                if ( $d === 'blocked' && isset( $e['why'] ) )
                    $out['blocked_reasons'][$e['why']] = ( isset( $out['blocked_reasons'][$e['why']] ) ? $out['blocked_reasons'][$e['why']] : 0 ) + 1;
                if ( $d === 'from_fallback' && isset( $e['s'] ) )
                    $out['from_fallback_senders'][$e['s']] = ( isset( $out['from_fallback_senders'][$e['s']] ) ? $out['from_fallback_senders'][$e['s']] : 0 ) + 1;
                if ( $d === 'uncategorised' && isset( $e['s'] ) )
                    $out['uncategorised_senders'][$e['s']] = ( isset( $out['uncategorised_senders'][$e['s']] ) ? $out['uncategorised_senders'][$e['s']] : 0 ) + 1;
            }
            fclose( $h );
        }
        arsort( $out['uncategorised_senders'] );
        return $out;
    }

    // ------------------------------------------------------------------ internals

    protected static function dispatchOptional( eZMail $mail, eZMailTransport $transport, expMailCategory $category, &$sent )
    {
        if ( !self::ensureSender( $mail, $category ) )
        {
            self::$lastResult['decision'] = 'blocked';
            self::$lastResult['blocked'][] = 'no_sender';
            self::$lastResult['result'] = false;
            return false;
        }
        $check = self::check( $mail );
        $allowed = array();
        foreach ( $check['recipients'] as $r )
        {
            $recipient = expMailRecipient::fromAddress( $r['email'] );
            if ( $r['decision'] === 'allow' )
                $allowed[] = array( $r, $recipient );
            else
            {
                self::$lastResult['blocked'][] = $r['decision'];
                self::log( 'blocked', $category->identifier, $recipient, $r['decision'], null );
            }
        }
        if ( !$allowed )
        {
            self::$lastResult['decision'] = 'blocked';
            self::$lastResult['result'] = true;
            return true;
        }
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $split = !$ini->hasVariable( 'GateSettings', 'SplitRecipients' ) || $ini->variable( 'GateSettings', 'SplitRecipients' ) !== 'disabled';
        $saved = array( 'to' => $mail->receiverElements(), 'cc' => $mail->ccElements(), 'bcc' => $mail->bccElements(),
                        'body' => $mail->body( false ), 'part' => isset( $mail->Mail->body ) ? $mail->Mail->body : null );
        $result = true;
        try
        {
            if ( count( $allowed ) === 1 || $split )
            {
                foreach ( $allowed as $pair )
                {
                    list( $r, $recipient ) = $pair;
                    self::onlyRecipients( $mail, array( $r ) );
                    self::restoreBody( $mail, $saved );
                    self::decorate( $mail, $recipient, $category );
                    $sent = true;
                    $ok = $transport->sendMail( $mail );
                    self::log( $ok ? 'sent' : 'error', $category->identifier, $recipient, $ok ? 'allow' : 'transport', null );
                    self::$lastResult['sent'] += $ok ? 1 : 0;
                    $result = $result && (bool)$ok;
                }
            }
            else
            {
                self::onlyRecipients( $mail, array_map( function ( $p ) { return $p[0]; }, $allowed ) );
                self::decorate( $mail, null, $category );
                $sent = true;
                $result = (bool)$transport->sendMail( $mail );
                foreach ( $allowed as $pair )
                    self::log( $result ? 'sent' : 'error', $category->identifier, $pair[1], $result ? 'allow' : 'transport', null );
                self::$lastResult['sent'] += $result ? count( $allowed ) : 0;
            }
        }
        finally
        {
            $mail->setReceiverElements( $saved['to'] );
            $mail->setCcElements( $saved['cc'] );
            $mail->setBccElements( $saved['bcc'] );
            self::restoreBody( $mail, $saved );
        }
        self::$lastResult['decision'] = self::$lastResult['blocked'] ? 'partly_blocked' : 'sent';
        self::$lastResult['result'] = $result;
        return $result;
    }

    /**
     * Optional mail never leaves with an empty From (CAN-SPAM: accurate header information). An empty sender gets
     * the site's [MailSettings] EmailSender, else its AdminEmail, and the gate log notes it (from_fallback, with the
     * file that sent). Without either the mail is not sent (blocked, no_sender) and an error is written.
     *
     * @return bool the mail has a sender now
     */
    protected static function ensureSender( eZMail $mail, expMailCategory $category )
    {
        $from = $mail->sender( false );
        if ( is_array( $from ) && isset( $from['email'] ) && trim( (string)$from['email'] ) !== '' )
            return true;
        $ini = eZINI::instance();
        $fallback = '';
        foreach ( array( 'EmailSender', 'AdminEmail' ) as $setting )
        {
            $value = $ini->hasVariable( 'MailSettings', $setting ) ? trim( (string)$ini->variable( 'MailSettings', $setting ) ) : '';
            if ( $value !== '' && eZMail::validate( $value ) )
            {
                $fallback = $value;
                break;
            }
        }
        $sender = self::callingSender();
        if ( $fallback === '' )
        {
            eZDebug::writeError( 'Optional mail without a sender and no [MailSettings] EmailSender or AdminEmail: not sent', __METHOD__ );
            self::log( 'blocked', $category->identifier, null, 'no_sender', $sender );
            return false;
        }
        $mail->setSender( $fallback );
        eZDebug::writeWarning( 'Optional mail without a sender: the site\'s sender address was used', __METHOD__ );
        self::log( 'from_fallback', $category->identifier, null, 'empty_from', $sender );
        return true;
    }

    /** @return array[] email, name, field of every recipient (to, cc, bcc), each address once */
    protected static function recipientsOf( eZMail $mail )
    {
        $out = array();
        $seen = array();
        foreach ( array( 'to' => $mail->receiverElements(), 'cc' => $mail->ccElements(), 'bcc' => $mail->bccElements() ) as $field => $list )
        {
            foreach ( (array)$list as $item )
            {
                $email = is_array( $item ) && isset( $item['email'] ) ? trim( (string)$item['email'] ) : '';
                if ( $email === '' || isset( $seen[strtolower( $email )] ) )
                    continue;
                $seen[strtolower( $email )] = true;
                $out[] = array( 'email' => $email, 'name' => isset( $item['name'] ) ? $item['name'] : false, 'field' => $field );
            }
        }
        return $out;
    }

    /** Keeps only these recipients, each in its own field. */
    protected static function onlyRecipients( eZMail $mail, array $recipients )
    {
        $fields = array( 'to' => array(), 'cc' => array(), 'bcc' => array() );
        foreach ( $recipients as $r )
            $fields[$r['field']][] = array( 'email' => $r['email'], 'name' => $r['name'] );
        $mail->setReceiverElements( $fields['to'] );
        $mail->setCcElements( $fields['cc'] );
        $mail->setBccElements( $fields['bcc'] );
    }

    protected static function restoreBody( eZMail $mail, array $saved )
    {
        if ( is_string( $saved['body'] ) )
            $mail->setBody( $saved['body'] );
        else if ( $saved['part'] !== null )
            $mail->Mail->body = $saved['part'];
    }

    /** @return int the lifetime of the links in the footer ([TokenSettings] TTL) */
    protected static function footerTTL( $purpose )
    {
        return expMailToken::defaultTTL( $purpose );
    }

    /**
     * The footer: the template [FooterSettings] Template when it exists, else the built-in text.
     *
     * @param expMailCategory $category
     * @param array $links manage, unsubscribe ('' without)
     * @param bool $isHTML
     * @return string
     */
    public static function footer( expMailCategory $category, array $links, $isHTML )
    {
        $org = self::organisation();
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $template = $ini->hasVariable( 'FooterSettings', 'Template' ) ? trim( (string)$ini->variable( 'FooterSettings', 'Template' ) ) : '';
        if ( $template !== '' )
        {
            $rendered = expMailPreferencesService::renderTemplate( $template, array(
                'category' => $category, 'manage_url' => $links['manage'], 'unsubscribe_url' => $links['unsubscribe'],
                'organisation_name' => $org['name'], 'organisation_address' => $org['address'], 'site_name' => $org['name'],
                'privacy_url' => expMailSenderDetails::privacyURL(), 'is_html' => $isHTML ) );
            if ( $rendered !== null && trim( $rendered['body'] ) !== '' )
                return $rendered['body'];
        }
        $name = ezpI18n::tr( 'kernel/mailpreferences/categories', $category->name );
        $why = ezpI18n::tr( 'kernel/mailpreferences/mail', 'You receive this e-mail because you switched on "%category".', null, array( '%category' => $name ) );
        $manage = ezpI18n::tr( 'kernel/mailpreferences/mail', 'Manage your e-mail preferences' );
        $unsubscribe = ezpI18n::tr( 'kernel/mailpreferences/mail', 'Unsubscribe from "%category"', null, array( '%category' => $name ) );
        $orgLine = trim( $org['name'] . ( $org['name'] !== '' && $org['address'] !== '' ? ', ' : '' ) . $org['address'] );
        $privacy = expMailSenderDetails::privacyURL();
        $privacyText = ezpI18n::tr( 'kernel/mailpreferences/mail', 'Privacy notice' );
        if ( $isHTML )
        {
            $h = function ( $s ) { return htmlspecialchars( (string)$s, ENT_QUOTES, 'UTF-8' ); };
            $html = '<div class="exp-mail-footer" style="margin-top:2em;padding-top:1em;border-top:1px solid #ccc;font-size:12px;color:#555">'
                  . '<p>' . $h( $why ) . '</p><p><a href="' . $h( $links['manage'] ) . '">' . $h( $manage ) . '</a>';
            if ( $links['unsubscribe'] !== '' )
                $html .= ' | <a href="' . $h( $links['unsubscribe'] ) . '">' . $h( $unsubscribe ) . '</a>';
            if ( $privacy !== '' )
                $html .= ' | <a href="' . $h( $privacy ) . '">' . $h( $privacyText ) . '</a>';
            $html .= '</p>';
            if ( $orgLine !== '' )
                $html .= '<p>' . nl2br( $h( $orgLine ) ) . '</p>';
            return $html . '</div>';
        }
        $text = "\n\n-- \n" . $why . "\n" . $manage . ': ' . $links['manage'] . "\n";
        if ( $links['unsubscribe'] !== '' )
            $text .= $unsubscribe . ': ' . $links['unsubscribe'] . "\n";
        if ( $privacy !== '' )
            $text .= $privacyText . ': ' . $privacy . "\n";
        if ( $orgLine !== '' )
            $text .= $orgLine . "\n";
        return $text;
    }

    /** @var bool the empty postal address was noted in this process */
    protected static $addressNoted = false;

    /**
     * Who sends: [FooterSettings] OrganisationName, else the SiteName of the siteaccess (expMailSenderDetails), and
     * the postal address. An empty address does not stop the mail; it is noted once per process.
     *
     * @return array name, address
     */
    public static function organisation()
    {
        $details = expMailSenderDetails::get();
        if ( $details['address'] === '' && !self::$addressNoted )
        {
            self::$addressNoted = true;
            eZDebug::writeNotice( 'The postal address of the mail footer is empty (mailpreferences.ini [FooterSettings] OrganisationAddress; '
                                  . 'enter it on mailpreferences/admin/status). Optional mail is sent without it.', __METHOD__ );
        }
        return array( 'name' => $details['name'], 'address' => $details['address'] );
    }

    /** Puts the footer before </body> of an HTML body, else at its end. */
    protected static function appendFooter( $body, $footer, $isHTML )
    {
        if ( $isHTML )
        {
            $pos = strripos( $body, '</body>' );
            if ( $pos !== false )
                return substr( $body, 0, $pos ) . $footer . substr( $body, $pos );
            return $body . $footer;
        }
        return rtrim( $body, "\r\n" ) . $footer;
    }

    /** A copy of a body part whose text parts can be changed without changing the original. */
    protected static function deepClone( ezcMailPart $part )
    {
        try
        {
            $copy = unserialize( serialize( $part ) );
            if ( $copy instanceof ezcMailPart )
                return $copy;
        }
        catch ( Throwable $e )
        {
        }
        return clone $part;
    }

    /** The footer in every text part of a multipart body. */
    protected static function decoratePart( ezcMailPart $part, expMailCategory $category, array $links )
    {
        if ( $part instanceof ezcMailText )
        {
            $isHTML = strtolower( (string)$part->subType ) === 'html';
            $part->text = self::appendFooter( (string)$part->text, self::footer( $category, $links, $isHTML ), $isHTML );
            return $part;
        }
        if ( $part instanceof ezcMailMultipartAlternative || $part instanceof ezcMailMultipartMixed || $part instanceof ezcMailMultipartRelated )
        {
            $parts = $part instanceof ezcMailMultipartRelated ? array( $part->getMainPart() ) : $part->getParts();
            foreach ( $parts as $child )
            {
                if ( $child instanceof ezcMailText || $child instanceof ezcMailMultipartAlternative )
                {
                    self::decoratePart( $child, $category, $links );
                    if ( $part instanceof ezcMailMultipartMixed )
                        break; // the first text of a mixed mail is its message, the rest are attachments
                }
            }
        }
        return $part;
    }

    /** @return string|null the file (relative) or class in the call stack that handed the mail to eZMailTransport */
    protected static function callingSender()
    {
        $root = rtrim( getcwd(), '/' ) . '/';
        foreach ( debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 12 ) as $frame )
        {
            if ( !isset( $frame['file'] ) )
                continue;
            $file = str_replace( '\\', '/', $frame['file'] );
            if ( strpos( $file, $root ) === 0 )
                $file = substr( $file, strlen( $root ) );
            if ( strpos( $file, 'phar://' ) === 0 && preg_match( '#\.phar/(.*)$#', $file, $m ) )
                $file = $m[1];
            // the mail classes and the gate itself are not the sender
            if ( preg_match( '#^(lib/ezutils/classes/ez[a-z]*mail[a-z]*\.php|kernel/classes/mailpreferences/)#', $file ) )
                continue;
            return $file;
        }
        return null;
    }

    /** @return bool the From address, the calling file or a class in the stack is listed in EssentialSenders[] */
    protected static function isEssentialSender( eZMail $mail, $senderFile )
    {
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $list = $ini->hasVariable( 'GateSettings', 'EssentialSenders' ) ? array_filter( (array)$ini->variable( 'GateSettings', 'EssentialSenders' ), 'strlen' ) : array();
        if ( !$list )
            return false;
        $from = $mail->sender( false );
        $fromEmail = is_array( $from ) && isset( $from['email'] ) ? strtolower( trim( (string)$from['email'] ) ) : '';
        $classes = array();
        foreach ( debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 16 ) as $frame )
            if ( isset( $frame['class'] ) )
                $classes[strtolower( ltrim( $frame['class'], '\\' ) )] = true;
        foreach ( $list as $entry )
        {
            $entry = trim( $entry );
            if ( strpos( $entry, '@' ) !== false && strtolower( $entry ) === $fromEmail )
                return true;
            if ( $senderFile !== null && $entry === $senderFile )
                return true;
            if ( isset( $classes[strtolower( ltrim( $entry, '\\' ) )] ) )
                return true;
        }
        return false;
    }
}
