<?php
/**
 * File containing the expMailPreferencesService class.
 *
 * The flows around the preferences: the double opt-in mail and its confirmation, the one-click unsubscribe, "send me
 * a link" for people without login, the confirmation of a new e-mail address, and the numbers of the status page and
 * exp:mail:status.
 *
 * The mails it sends are of the essential category 'security' (requested by the person). Their text comes from the
 * templates design:mailpreferences/mail/confirm.tpl, link.tpl and email_change.tpl when they exist (a template sets
 * the subject with {set-block scope=root variable=subject}), else from the built-in text.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMailPreferencesService
{
    const TABLES = array( 'expmail_category', 'expmail_preference', 'expmail_consent_log', 'expmail_suppression', 'expmail_pending' );

    // ------------------------------------------------------------------ links

    /**
     * @param expMailRecipient $recipient
     * @param int|null $ttl null: [TokenSettings] TTL[manage]
     * @return string the absolute URL of the preference page for this person without login
     */
    public static function manageURL( expMailRecipient $recipient, $ttl = null )
    {
        return expMailToken::url( 'manage', expMailToken::create( $recipient, 'manage', null, $ttl ) );
    }

    /**
     * @param expMailRecipient $recipient
     * @param string|null $category null: all optional mail (the master switch)
     * @return string the absolute URL of the one-click unsubscribe
     */
    public static function unsubscribeURL( expMailRecipient $recipient, $category = null )
    {
        return expMailToken::url( 'unsubscribe', expMailToken::create( $recipient, 'unsubscribe', $category ) );
    }

    // ------------------------------------------------------------------ double opt-in

    /**
     * Sends the confirmation mail of a pending double opt-in (expMailPreferences::set() calls it).
     *
     * @param expMailRecipient $recipient
     * @param expMailCategory $category
     * @param expMailPendingRow $pending
     * @return bool sent
     */
    public static function sendConfirmation( expMailRecipient $recipient, expMailCategory $category, expMailPendingRow $pending )
    {
        $email = $recipient->email();
        if ( $email === '' || self::blockedAddress( $email ) )
            return false;
        $token = expMailToken::create( $recipient, 'confirm', $category->identifier, max( 60, (int)$pending->attribute( 'expires' ) - time() ),
                                       array( 'pending' => (int)$pending->attribute( 'id' ) ) );
        $url = expMailToken::url( 'confirm', $token );
        $name = ezpI18n::tr( 'kernel/mailpreferences/categories', $category->name );
        $vars = array( 'category' => $category, 'confirm_url' => $url, 'email' => $email, 'expires' => (int)$pending->attribute( 'expires' ),
                       'kind' => 'category' );
        $subject = ezpI18n::tr( 'kernel/mailpreferences/mail', 'Please confirm: %category', null, array( '%category' => $name ) );
        $body = ezpI18n::tr( 'kernel/mailpreferences/mail', 'Please confirm that you want to receive "%category" at this address.', null, array( '%category' => $name ) )
              . "\n\n" . $url . "\n\n"
              . ezpI18n::tr( 'kernel/mailpreferences/mail', 'If you did not ask for this, ignore this e-mail: nothing will be sent to you.' ) . "\n";
        return self::sendTemplated( $email, 'design:mailpreferences/mail/confirm.tpl', $vars, $subject, $body );
    }

    /**
     * Confirms a double opt-in (a category, or a new e-mail address).
     *
     * @param string $token
     * @param expConsentContext|null $context null: from the request, source 'confirm'
     * @return array result (confirmed, already, expired, invalid), kind, category, recipient (expMailRecipient|null)
     */
    public static function confirm( $token, ?expConsentContext $context = null )
    {
        $out = array( 'result' => 'invalid', 'kind' => '', 'category' => '', 'recipient' => null );
        $payload = expMailToken::verify( $token, 'confirm' );
        if ( $payload === null )
        {
            if ( expMailToken::lastError() === 'expired' )
                $out['result'] = 'expired';
            return $out;
        }
        $recipient = expMailRecipient::fromPayload( $payload );
        if ( $recipient === null )
            return $out;
        $out['recipient'] = $recipient;
        $out['category'] = $payload['category'];
        $context = $context ?: expConsentContext::fromRequest( 'confirm', '' );
        if ( $context->source !== 'confirm' )
            $context = new expConsentContext( 'confirm', $context->wording, $context->ip, $context->actorUserId, $context->siteaccess );
        $pending = $payload['pending'] ? expMailPendingRow::fetch( $payload['pending'] ) : null;
        if ( !$pending || $pending->attribute( 'recipient_key' ) !== $recipient->key() )
        {
            if ( $payload['category'] !== '' && expMailPreferences::forRecipient( $recipient )->state( $payload['category'] ) === expMailPreferences::ON )
                $out['result'] = 'already';
            return $out;
        }
        $out['kind'] = (string)$pending->attribute( 'kind' );
        if ( (int)$pending->attribute( 'expires' ) > 0 && (int)$pending->attribute( 'expires' ) < time() )
        {
            $pending->remove();
            $out['result'] = 'expired';
            return $out;
        }
        if ( $out['kind'] === 'category' )
        {
            $prefs = expMailPreferences::forRecipient( $recipient );
            $result = $prefs->set( (string)$pending->attribute( 'category' ), true, $context );
            $out['result'] = $result === expMailPreferences::ON ? 'confirmed' : 'invalid';
            return $out;
        }
        if ( $out['kind'] === 'email_change' )
        {
            $data = $pending->dataArray();
            $user = $recipient->hasAccount() ? eZUser::fetch( $recipient->userId() ) : null;
            $new = isset( $data['email'] ) ? (string)$data['email'] : '';
            $pending->remove();
            if ( !$user || !eZMail::validate( $new ) )
                return $out;
            $existing = eZUser::fetchByEmail( $new );
            if ( $existing && (int)$existing->attribute( 'contentobject_id' ) !== $recipient->userId() )
                return $out;
            $old = (string)$user->attribute( 'email' );
            $user->setAttribute( 'email', $new );
            $user->store();
            expConsentLog::record( $recipient, '', 'email_change', '', '', $context );
            if ( class_exists( 'eZContentCacheManager' ) )
                eZContentCacheManager::clearContentCacheIfNeeded( $recipient->userId() );
            $out['result'] = 'confirmed';
            $out['recipient'] = expMailRecipient::fromUser( $user );
            return $out;
        }
        return $out;
    }

    /**
     * A new e-mail address for an account, confirmed by a link sent to the new address (double opt-in).
     *
     * @param eZUser $user
     * @param string $newEmail
     * @param expConsentContext $context
     * @return string pending_confirmation, invalid, in_use, unchanged
     */
    public static function requestEmailChange( eZUser $user, $newEmail, expConsentContext $context )
    {
        $newEmail = expMailRecipient::normalise( $newEmail );
        if ( !eZMail::validate( $newEmail ) )
            return 'invalid';
        if ( $newEmail === expMailRecipient::normalise( $user->attribute( 'email' ) ) )
            return 'unchanged';
        $existing = eZUser::fetchByEmail( $newEmail );
        if ( $existing && (int)$existing->attribute( 'contentobject_id' ) !== (int)$user->attribute( 'contentobject_id' ) )
            return 'in_use';
        $recipient = expMailRecipient::fromUser( $user );
        foreach ( expMailPendingRow::fetchForKey( $recipient->key(), 'email_change' ) as $old )
            $old->remove();
        $pending = new expMailPendingRow( array( 'recipient_key' => $recipient->key(), 'user_id' => $recipient->userId(), 'category' => '',
                                                 'kind' => 'email_change', 'data' => json_encode( array( 'email' => $newEmail ) ),
                                                 'created' => time(), 'expires' => time() + expMailPreferences::pendingSeconds() ) );
        $pending->store();
        expConsentLog::record( $recipient, '', 'pending', '', 'email_change', $context );
        $token = expMailToken::create( $recipient, 'confirm', null, expMailPreferences::pendingSeconds(), array( 'pending' => (int)$pending->attribute( 'id' ) ) );
        $url = expMailToken::url( 'confirm', $token );
        $subject = ezpI18n::tr( 'kernel/mailpreferences/mail', 'Please confirm your new e-mail address' );
        $body = ezpI18n::tr( 'kernel/mailpreferences/mail', 'Please confirm that this is the new e-mail address of your account.' )
              . "\n\n" . $url . "\n\n"
              . ezpI18n::tr( 'kernel/mailpreferences/mail', 'If you did not ask for this, ignore this e-mail: your account keeps its address.' ) . "\n";
        self::sendTemplated( $newEmail, 'design:mailpreferences/mail/email_change.tpl',
                             array( 'confirm_url' => $url, 'email' => $newEmail, 'expires' => (int)$pending->attribute( 'expires' ), 'kind' => 'email_change' ),
                             $subject, $body );
        return 'pending_confirmation';
    }

    // ------------------------------------------------------------------ leaving

    /**
     * The one-click unsubscribe (GET confirm page, POST and RFC 8058 POST): a link of a category switches that
     * category off, a link without category switches all optional mail off (master switch).
     *
     * @param string $token
     * @param expConsentContext|null $context null: from the request, source 'link'
     * @return array result (unsubscribed, invalid, expired), category, recipient
     */
    public static function unsubscribe( $token, ?expConsentContext $context = null )
    {
        $out = array( 'result' => 'invalid', 'category' => '', 'recipient' => null );
        $payload = expMailToken::verify( $token, 'unsubscribe' );
        if ( $payload === null )
        {
            if ( expMailToken::lastError() === 'expired' )
                $out['result'] = 'expired';
            return $out;
        }
        $recipient = expMailRecipient::fromPayload( $payload );
        if ( $recipient === null )
            return $out;
        $context = $context ?: expConsentContext::fromRequest( 'link', ezpI18n::tr( 'kernel/mailpreferences/mail', 'Unsubscribe link' ) );
        $prefs = expMailPreferences::forRecipient( $recipient );
        $category = $payload['category'] !== '' ? expMailCategoryRegistry::instance()->get( $payload['category'] ) : null;
        if ( $category && !$category->essential )
            $prefs->set( $category->identifier, false, $context );
        else
            $prefs->setMaster( false, $context );
        $out['result'] = 'unsubscribed';
        $out['category'] = $category ? $category->identifier : '';
        $out['recipient'] = $recipient;
        return $out;
    }

    /**
     * "Unsubscribe from everything": the master switch off and, when $suppress, the address on the suppression list
     * (reason unsubscribe_all; switching mail on again on the page lifts it).
     *
     * @param expMailRecipient $recipient
     * @param expConsentContext $context
     * @param bool $suppress
     */
    public static function unsubscribeAll( expMailRecipient $recipient, expConsentContext $context, $suppress = true )
    {
        expMailPreferences::forRecipient( $recipient )->setMaster( false, $context );
        if ( $suppress && $recipient->email() !== '' && !expMailSuppression::isSuppressed( $recipient->email() ) )
        {
            expMailSuppression::add( $recipient->email(), 'unsubscribe_all', 'by the person (' . $context->source . ')', $context->actorUserId );
            expConsentLog::record( $recipient, '', 'suppress', '', 'unsubscribe_all', $context );
        }
    }

    // ------------------------------------------------------------------ send me a link

    /**
     * "Send me a link": sends a link to the preference page to an address. The page must answer the same for every
     * address (no one learns whether an address is known); the result is for logs and tests.
     *
     * @param string $email
     * @param expConsentContext|null $context
     * @return string sent, invalid, rate_limited, blocked
     */
    public static function requestLink( $email, ?expConsentContext $context = null )
    {
        $recipient = expMailRecipient::fromAddress( $email );
        if ( $recipient === null )
            return 'invalid';
        if ( self::blockedAddress( $recipient->email() ) )
            return 'blocked';
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $limit = $ini->hasVariable( 'TokenSettings', 'RequestLinkLimit' ) ? (int)$ini->variable( 'TokenSettings', 'RequestLinkLimit' ) : 3;
        $ttl = $ini->hasVariable( 'TokenSettings', 'RequestLinkTTL' ) ? (int)$ini->variable( 'TokenSettings', 'RequestLinkTTL' ) : 86400;
        // the rate limit is per address hash (also for an account: its address may be asked for by anyone)
        $rateKey = 'a:' . expMailSuppression::hash( $recipient->email() );
        $recent = 0;
        foreach ( expMailPendingRow::fetchForKey( $rateKey, 'link' ) as $row )
            if ( (int)$row->attribute( 'created' ) > time() - 3600 )
                $recent++;
        if ( $limit > 0 && $recent >= $limit )
            return 'rate_limited';
        $row = new expMailPendingRow( array( 'recipient_key' => $rateKey, 'user_id' => 0, 'category' => '', 'kind' => 'link', 'data' => '',
                                             'created' => time(), 'expires' => time() + 3600 ) );
        $row->store();
        $url = self::manageURL( $recipient, max( 60, $ttl ) );
        $subject = ezpI18n::tr( 'kernel/mailpreferences/mail', 'Your link to your e-mail preferences' );
        $body = ezpI18n::tr( 'kernel/mailpreferences/mail', 'With this link you can choose which e-mail you receive from us:' )
              . "\n\n" . $url . "\n\n"
              . ezpI18n::tr( 'kernel/mailpreferences/mail', 'If you did not ask for this, ignore this e-mail.' ) . "\n";
        $ok = self::sendTemplated( $recipient->email(), 'design:mailpreferences/mail/link.tpl',
                                   array( 'manage_url' => $url, 'email' => $recipient->email(), 'expires' => time() + $ttl ), $subject, $body );
        return $ok ? 'sent' : 'invalid';
    }

    // ------------------------------------------------------------------ status

    /**
     * The numbers of the status page and exp:mail:status.
     *
     * @return array
     */
    public static function status()
    {
        $db = eZDB::instance();
        $tables = array();
        foreach ( self::TABLES as $t )
            $tables[$t] = self::tableExists( $t );
        $count = function ( $sql ) use ( $db ) {
            $r = $db->arrayQuery( $sql );
            return isset( $r[0]['c'] ) ? (int)$r[0]['c'] : 0;
        };
        $categories = array();
        $byState = array();
        if ( $tables['expmail_preference'] )
            foreach ( (array)$db->arrayQuery( 'SELECT category, state, COUNT(*) AS c FROM expmail_preference GROUP BY category, state' ) as $r )
                $byState[$r['category']][$r['state']] = (int)$r['c'];
        foreach ( expMailCategoryRegistry::instance()->all() as $id => $cat )
            $categories[$id] = array( 'name' => $cat->name, 'essential' => $cat->essential, 'source' => $cat->source, 'double_opt_in' => $cat->doubleOptIn,
                                      'on' => isset( $byState[$id]['on'] ) ? $byState[$id]['on'] : 0,
                                      'off' => isset( $byState[$id]['off'] ) ? $byState[$id]['off'] : 0,
                                      'pending' => isset( $byState[$id]['pending'] ) ? $byState[$id]['pending'] : 0 );
        $suppression = array( 'total' => 0, 'by_reason' => array() );
        if ( $tables['expmail_suppression'] )
        {
            foreach ( (array)$db->arrayQuery( 'SELECT reason, COUNT(*) AS c FROM expmail_suppression GROUP BY reason' ) as $r )
                $suppression['by_reason'][$r['reason']] = (int)$r['c'];
            $suppression['total'] = array_sum( $suppression['by_reason'] );
        }
        $now = time();
        $org = expMailSenderDetails::get();
        $s = array(
            'tables' => $tables,
            'gate' => expMailGate::enabled() ? 'enabled' : 'disabled',
            'secret' => expMailSecret::exists(),
            'base_url' => expMailToken::baseURL(),
            'footer' => array( 'organisation_name' => $org['name'], 'organisation_address' => $org['address'], 'organisation_name_source' => $org['name_source'] ),
            'categories' => $categories,
            'recipients' => $tables['expmail_preference'] ? $count( 'SELECT COUNT(DISTINCT recipient_key) AS c FROM expmail_preference' ) : 0,
            'master_off' => $tables['expmail_preference'] ? $count( "SELECT COUNT(*) AS c FROM expmail_preference WHERE category = '" . expMailPreferenceRow::MASTER . "' AND state = 'off'" ) : 0,
            'pending' => $tables['expmail_pending'] ? $count( "SELECT COUNT(*) AS c FROM expmail_pending WHERE kind <> 'link' AND ( expires = 0 OR expires >= $now )" ) : 0,
            'pending_expired' => $tables['expmail_pending'] ? $count( "SELECT COUNT(*) AS c FROM expmail_pending WHERE expires > 0 AND expires < $now" ) : 0,
            'suppression' => $suppression,
            'consent_log' => $tables['expmail_consent_log'] ? $count( 'SELECT COUNT(*) AS c FROM expmail_consent_log' ) : 0,
            'consent_log_anonymised' => $tables['expmail_consent_log'] ? $count( 'SELECT COUNT(*) AS c FROM expmail_consent_log WHERE anonymised = 1' ) : 0,
            'gate_24h' => expMailGate::stats( $now - 86400 ),
            'gate_7d' => expMailGate::stats( $now - 7 * 86400 ),
            'log_file' => expMailGate::logFile(),
            'bounce' => class_exists( 'expMailBounceReader' ) ? expMailBounceReader::status() : null,
            'problems' => array(),
        );
        foreach ( $tables as $t => $ok )
            if ( !$ok )
                $s['problems'][] = array( 'error', 'table_missing', $t );
        if ( $org['name'] === '' || $org['address'] === '' )
            $s['problems'][] = array( 'warning', 'footer_missing', '' );
        if ( !$s['secret'] )
            $s['problems'][] = array( 'notice', 'secret_missing', '' );
        if ( $s['gate'] === 'disabled' )
            $s['problems'][] = array( 'notice', 'gate_disabled', '' );
        if ( $s['gate_7d']['uncategorised'] > 0 )
            $s['problems'][] = array( 'notice', 'uncategorised', (string)$s['gate_7d']['uncategorised'] );
        if ( $s['gate_24h']['error'] > 0 )
            $s['problems'][] = array( 'warning', 'gate_errors', (string)$s['gate_24h']['error'] );
        if ( is_array( $s['bounce'] ) && $s['bounce']['enabled'] && $s['bounce']['last_error'] !== '' )
            $s['problems'][] = array( 'warning', 'bounce_error', $s['bounce']['last_error'] );
        else if ( is_array( $s['bounce'] ) && $s['bounce']['enabled'] && $s['bounce']['last_read'] < $now - 86400 )
            $s['problems'][] = array( 'warning', 'bounce_stale', $s['bounce']['last_read'] ? date( 'Y-m-d H:i', $s['bounce']['last_read'] ) : '' );
        return $s;
    }

    /**
     * A problem of status() as a sentence.
     *
     * @param array $problem level, code, detail
     * @return string
     */
    public static function problemText( array $problem )
    {
        switch ( $problem[1] )
        {
            case 'table_missing':
                return ezpI18n::tr( 'kernel/mailpreferences/status', 'The table %table is missing: run the database update.', null, array( '%table' => $problem[2] ) );
            case 'footer_missing':
                return ezpI18n::tr( 'kernel/mailpreferences/status', 'The postal address of the mail footer is empty: enter it under "Sender details" on this page (mailpreferences/admin/status). The law requires the organisation and its postal address in every optional mail; mail is sent without it until then.' );
            case 'secret_missing':
                return ezpI18n::tr( 'kernel/mailpreferences/status', 'The site secret of the links has not been generated yet; it is made on first use.' );
            case 'gate_disabled':
                return ezpI18n::tr( 'kernel/mailpreferences/status', 'The mail gate is disabled: optional mail is sent without checking the preferences.' );
            case 'uncategorised':
                return ezpI18n::tr( 'kernel/mailpreferences/status', '%count mails without a category in the last 7 days.', null, array( '%count' => $problem[2] ) );
            case 'gate_errors':
                return ezpI18n::tr( 'kernel/mailpreferences/status', '%count mails could not be sent or checked in the last 24 hours.', null, array( '%count' => $problem[2] ) );
            case 'bounce_error':
                return ezpI18n::tr( 'kernel/mailpreferences/status', 'The bounce mailbox could not be read: %error', null, array( '%error' => $problem[2] ) );
            case 'bounce_stale':
                return $problem[2] !== ''
                    ? ezpI18n::tr( 'kernel/mailpreferences/status', 'The bounce mailbox has not been read since %time: is the cronjob part mailbounces running?', null, array( '%time' => $problem[2] ) )
                    : ezpI18n::tr( 'kernel/mailpreferences/status', 'The bounce mailbox has never been read: is the cronjob part mailbounces running?' );
        }
        return $problem[1];
    }

    // ------------------------------------------------------------------ helpers

    /**
     * The recipient a console option names.
     *
     * @param string|null $user a login or a user id
     * @param string|null $email an address
     * @return expMailRecipient|null
     */
    public static function recipientFor( $user, $email )
    {
        if ( $user !== null && $user !== '' && $user !== false )
        {
            $u = ctype_digit( (string)$user ) ? eZUser::fetch( (int)$user ) : eZUser::fetchByName( (string)$user );
            return $u instanceof eZUser ? expMailRecipient::fromUser( $u ) : null;
        }
        if ( $email !== null && $email !== '' && $email !== false )
            return expMailRecipient::fromAddress( (string)$email );
        return null;
    }

    /** @return string the address with the local part hidden: n***@example.com */
    public static function maskAddress( $email )
    {
        $email = (string)$email;
        $at = strrpos( $email, '@' );
        if ( $at === false || $at === 0 )
            return $email === '' ? '' : '***';
        return $email[0] . '***' . substr( $email, $at );
    }

    /**
     * Renders a template if it exists.
     *
     * @param string $uri design:...
     * @param array $vars
     * @return array|null body, subject (or null), content_type (or null); null when the template does not exist
     */
    public static function renderTemplate( $uri, array $vars )
    {
        if ( strpos( $uri, 'design:' ) !== 0 || !class_exists( 'eZTemplateDesignResource' ) )
            return null;
        $path = substr( $uri, 7 );
        $tried = array();
        if ( !eZTemplateDesignResource::fileMatch( eZTemplateDesignResource::allDesignBases(), 'templates', $path, $tried ) )
            return null;
        // every mail template knows the public site and the privacy notice: a mail sent from the console or the
        // administration names the public site, never the siteaccess that sends (expMailSenderDetails)
        if ( !array_key_exists( 'site_name', $vars ) )
        {
            $details = expMailSenderDetails::get();
            $vars['site_name'] = $details['name'];
        }
        if ( !array_key_exists( 'privacy_url', $vars ) )
            $vars['privacy_url'] = expMailSenderDetails::privacyURL();
        $tpl = eZTemplate::factory();
        foreach ( $vars as $k => $v )
            $tpl->setVariable( $k, $v );
        $body = (string)$tpl->fetch( $uri );
        $subject = $tpl->hasVariable( 'subject' ) ? (string)$tpl->variable( 'subject' ) : null;
        $type = $tpl->hasVariable( 'content_type' ) ? (string)$tpl->variable( 'content_type' ) : null;
        foreach ( array_merge( array_keys( $vars ), array( 'subject', 'content_type' ) ) as $k )
            if ( $tpl->hasVariable( $k ) )
                $tpl->unsetVariable( $k );
        return array( 'body' => $body, 'subject' => $subject, 'content_type' => $type );
    }

    /**
     * Sends one of the service's mails (category security) from the template, or the built-in text.
     *
     * @return bool
     */
    protected static function sendTemplated( $email, $template, array $vars, $subject, $body )
    {
        $rendered = self::renderTemplate( $template, $vars );
        $mail = new eZMail();
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
        $mail->setBody( $body );
        $mail->setCategory( 'security' );
        return (bool)eZMailTransport::send( $mail );
    }

    /** @return bool the address must get nothing (bounce, complaint, legal); "unsubscribe from all" does not block a requested mail */
    protected static function blockedAddress( $email )
    {
        $reason = expMailSuppression::reason( $email );
        return $reason !== null && in_array( $reason, array( 'bounce', 'complaint', 'legal' ), true );
    }

    /** @return bool */
    public static function tableExists( $table )
    {
        $db = eZDB::instance();
        $name = $db->escapeString( (string)$table );
        try
        {
            switch ( $db->databaseName() )
            {
                case 'sqlite':
                    $r = $db->arrayQuery( "SELECT COUNT(*) AS c FROM sqlite_master WHERE type = 'table' AND name = '$name'" );
                    return isset( $r[0]['c'] ) && (int)$r[0]['c'] > 0;
                case 'mysql':
                    $r = $db->arrayQuery( "SELECT COUNT(*) AS c FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '$name'" );
                    return isset( $r[0]['c'] ) && (int)$r[0]['c'] > 0;
                case 'postgresql':
                    $r = $db->arrayQuery( "SELECT COUNT(*) AS c FROM information_schema.tables WHERE table_schema = current_schema() AND table_name = '$name'" );
                    return isset( $r[0]['c'] ) && (int)$r[0]['c'] > 0;
            }
            // other engines (Oracle, MongoDB): the kernel's own list
            $list = $db->eZTableList();
            return is_array( $list ) && isset( $list[$table] );
        }
        catch ( Throwable $e )
        {
        }
        return false;
    }
}
