<?php
/**
 * File containing the expMailBounceReader class.
 *
 * Reads the mailbox that bounces and complaints arrive in (mailpreferences.ini [BounceSettings], set in
 * settings/override/mailpreferences.ini.append.php, which is never committed) and puts the addresses on the
 * suppression list:
 *
 *  - hard bounces: delivery status notifications (RFC 3464, multipart/report; report-type=delivery-status) whose
 *    recipient failed with a permanent status of [BounceSettings] HardStatusCodes[] (an address that does not
 *    exist, a disabled mailbox, no mail server) => reason "bounce". Other failures and delays are counted only.
 *  - complaints: feedback loop reports (RFC 5965, ARF, report-type=feedback-report) of any type but "not-spam"
 *    => reason "complaint".
 *
 * IMAP reads only unread messages (fetching marks them read); POP3 reads every message. [BounceSettings]
 * AfterRead=delete removes the messages that were understood. Nothing is read while Reader is disabled or the
 * server is empty (the default). The last run is kept in [BounceSettings] StatusFile for exp:mail:status and the
 * status page.
 *
 * \code
 * expMailBounceReader::run();                         // the mailbox (cronjob part mailbounces, exp:mail:bounces)
 * expMailBounceReader::runFiles( array( 'x.eml' ) );  // saved messages
 * expMailBounceReader::classify( $raw );              // what a message is, nothing changed
 * \endcode
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMailBounceReader
{
    const HARD = 'hard';
    const SOFT = 'soft';
    const COMPLAINT = 'complaint';
    const NONE = 'none';

    /** @var string|null test override of the status file (absolute) */
    protected static $statusFile = null;

    // ------------------------------------------------------------------ settings

    /** @return string the value of [BounceSettings] $name, or $default */
    protected static function setting( $name, $default = '' )
    {
        $ini = eZINI::instance( 'mailpreferences.ini' );
        return $ini->hasVariable( 'BounceSettings', $name ) ? $ini->variable( 'BounceSettings', $name ) : $default;
    }

    /** @return bool the reader is switched on and has a server */
    public static function enabled()
    {
        return self::setting( 'Reader', 'disabled' ) === 'enabled' && trim( (string)self::setting( 'Server' ) ) !== '';
    }

    /** @return string imap or pop3 */
    public static function protocol()
    {
        return strtolower( (string)self::setting( 'Protocol', 'imap' ) ) === 'pop3' ? 'pop3' : 'imap';
    }

    /** @return string[] the status codes (or their starts) that make a failure a hard bounce */
    public static function hardStatusCodes()
    {
        $codes = array_filter( array_map( 'trim', (array)self::setting( 'HardStatusCodes', array() ) ), 'strlen' );
        return $codes ? array_values( $codes ) : array( '5.1.', '5.2.1', '5.4.4' );
    }

    /** @return string the status file (absolute or relative to the installation) */
    public static function statusFile()
    {
        if ( self::$statusFile !== null )
            return self::$statusFile;
        $rel = trim( (string)self::setting( 'StatusFile', 'log/mailbounces.json' ), '/' );
        return eZSys::varDirectory() . '/' . ( $rel !== '' ? $rel : 'log/mailbounces.json' );
    }

    /** Tests only: another status file; null resets. */
    public static function setStatusFileForTest( $file )
    {
        self::$statusFile = $file;
    }

    // ------------------------------------------------------------------ status

    /**
     * The reader for the status page and exp:mail:status.
     *
     * @return array enabled, protocol, server (host only), folder, after_read, last_read (time of the last successful
     *               read, 0 never), last_run, last_error, last (the counts of the last run), total (counts of all runs)
     */
    public static function status()
    {
        $data = array();
        $file = self::statusFile();
        if ( is_file( $file ) )
        {
            $decoded = json_decode( (string)@file_get_contents( $file ), true );
            if ( is_array( $decoded ) )
                $data = $decoded;
        }
        return array(
            'enabled' => self::enabled(),
            'configured' => trim( (string)self::setting( 'Server' ) ) !== '',
            'protocol' => self::protocol(),
            'server' => trim( (string)self::setting( 'Server' ) ),
            'folder' => (string)self::setting( 'Folder', 'INBOX' ),
            'after_read' => self::setting( 'AfterRead', 'keep' ) === 'delete' ? 'delete' : 'keep',
            'last_read' => isset( $data['last_read'] ) ? (int)$data['last_read'] : 0,
            'last_run' => isset( $data['last_run'] ) ? (int)$data['last_run'] : 0,
            'last_error' => isset( $data['last_error'] ) ? (string)$data['last_error'] : '',
            'last' => isset( $data['last'] ) && is_array( $data['last'] ) ? $data['last'] : self::emptyCounts(),
            'total' => isset( $data['total'] ) && is_array( $data['total'] ) ? $data['total'] : self::emptyCounts(),
        );
    }

    /** @return array messages, hard, soft, complaints, other, suppressed (new entries), errors */
    public static function emptyCounts()
    {
        return array( 'messages' => 0, 'hard' => 0, 'soft' => 0, 'complaints' => 0, 'other' => 0, 'suppressed' => 0, 'errors' => 0 );
    }

    protected static function writeStatus( array $counts, $error )
    {
        $old = self::status();
        $now = time();
        $total = $old['total'];
        foreach ( $counts as $k => $v )
            $total[$k] = ( isset( $total[$k] ) ? (int)$total[$k] : 0 ) + (int)$v;
        $data = array( 'last_run' => $now, 'last_read' => $error === '' ? $now : $old['last_read'], 'last_error' => $error,
                       'last' => $counts, 'total' => $total );
        $file = self::statusFile();
        $dir = dirname( $file );
        if ( !is_dir( $dir ) )
            @mkdir( $dir, 0775, true );
        $new = !is_file( $file );
        @file_put_contents( $file, json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n", LOCK_EX );
        if ( $new && class_exists( 'expAuditWriter' ) )
            expAuditWriter::ownLikeParent( $file, 0664 );
    }

    // ------------------------------------------------------------------ reading

    /**
     * Reads the mailbox.
     *
     * @param bool $dryRun only report: nothing is suppressed, no message is changed or deleted, no status written
     * @param callable|null $report called with ( string line ) for each message
     * @return array ok (bool), skipped (reason or ''), error, counts (emptyCounts()), items (per message: kind, addresses
     *               masked, detail)
     */
    public static function run( $dryRun = false, $report = null )
    {
        $out = array( 'ok' => true, 'skipped' => '', 'error' => '', 'counts' => self::emptyCounts(), 'items' => array() );
        if ( !self::enabled() )
        {
            $out['skipped'] = trim( (string)self::setting( 'Server' ) ) === '' ? 'not_configured' : 'disabled';
            return $out;
        }
        $transport = null;
        try
        {
            $transport = self::connect();
            $numbers = self::messageNumbers( $transport );
            $max = max( 1, (int)self::setting( 'MaxMessages', 500 ) );
            $delete = !$dryRun && self::setting( 'AfterRead', 'keep' ) === 'delete';
            foreach ( array_slice( $numbers, 0, $max ) as $number )
            {
                $raw = self::fetchRaw( $transport, $number, $dryRun );
                $item = self::processMessage( $raw, $dryRun, $out['counts'] );
                $out['items'][] = $item;
                if ( $report )
                    call_user_func( $report, self::describe( $item, '#' . $number ) );
                if ( $delete && $item['kind'] !== self::NONE )
                    $transport->delete( $number );
            }
            if ( $delete && $transport instanceof ezcMailImapTransport )
                $transport->expunge();
        }
        catch ( Throwable $e )
        {
            $out['ok'] = false;
            $out['error'] = substr( $e->getMessage(), 0, 300 );
            $out['counts']['errors']++;
            eZDebug::writeError( 'Bounce reader: ' . $out['error'], __METHOD__ );
        }
        if ( $transport )
        {
            try
            {
                $transport->disconnect();
            }
            catch ( Throwable $e )
            {
            }
        }
        if ( !$dryRun )
            self::writeStatus( $out['counts'], $out['error'] );
        return $out;
    }

    /**
     * Reads saved messages (.eml files, or every .eml file of a directory) instead of the mailbox; for the console
     * and the tests. The status file is not written.
     *
     * @param string[] $paths
     * @param bool $dryRun
     * @param callable|null $report
     * @return array as run()
     */
    public static function runFiles( array $paths, $dryRun = false, $report = null )
    {
        $out = array( 'ok' => true, 'skipped' => '', 'error' => '', 'counts' => self::emptyCounts(), 'items' => array() );
        $files = array();
        foreach ( $paths as $path )
        {
            if ( is_dir( $path ) )
            {
                $list = glob( rtrim( $path, '/' ) . '/*.eml' );
                sort( $list );
                $files = array_merge( $files, $list );
            }
            else if ( is_file( $path ) )
                $files[] = $path;
            else
            {
                $out['ok'] = false;
                $out['error'] = 'No such file: ' . $path;
                $out['counts']['errors']++;
            }
        }
        foreach ( $files as $file )
        {
            $item = self::processMessage( (string)file_get_contents( $file ), $dryRun, $out['counts'] );
            $out['items'][] = $item;
            if ( $report )
                call_user_func( $report, self::describe( $item, basename( $file ) ) );
        }
        return $out;
    }

    /**
     * Classifies one message and suppresses what it reports.
     *
     * @param string $raw
     * @param bool $dryRun
     * @param array $counts updated
     * @return array kind, detail, addresses (masked), suppressed (new entries)
     */
    public static function processMessage( $raw, $dryRun, array &$counts )
    {
        $counts['messages']++;
        $c = self::classify( $raw );
        $item = array( 'kind' => $c['kind'], 'detail' => $c['detail'], 'addresses' => array(), 'suppressed' => 0 );
        switch ( $c['kind'] )
        {
            case self::HARD: $counts['hard']++; break;
            case self::SOFT: $counts['soft']++; break;
            case self::COMPLAINT: $counts['complaints']++; break;
            default: $counts['other']++;
        }
        if ( $c['kind'] !== self::HARD && $c['kind'] !== self::COMPLAINT )
            return $item;
        $reason = $c['kind'] === self::HARD ? 'bounce' : 'complaint';
        foreach ( $c['addresses'] as $email )
        {
            $item['addresses'][] = expMailPreferencesService::maskAddress( $email );
            if ( $dryRun || expMailSuppression::isSuppressed( $email ) )
                continue;
            expMailSuppression::add( $email, $reason, ( $reason === 'bounce' ? 'Hard bounce, status ' : 'Complaint, feedback type ' ) . $c['detail'], 0 );
            $recipient = expMailRecipient::fromAddress( $email );
            if ( $recipient !== null )
                expConsentLog::record( $recipient, '', 'suppress', '', $reason,
                                       expConsentContext::system( ( $reason === 'bounce' ? 'Hard bounce: delivery status ' : 'Complaint: feedback type ' ) . $c['detail'] ) );
            $item['suppressed']++;
            $counts['suppressed']++;
        }
        return $item;
    }

    /** @return string one line about a processed message (no address in clear) */
    public static function describe( array $item, $label )
    {
        return sprintf( '%s: %s%s%s%s', $label, $item['kind'], $item['detail'] !== '' ? ' (' . $item['detail'] . ')' : '',
                        $item['addresses'] ? ' ' . implode( ', ', $item['addresses'] ) : '',
                        $item['suppressed'] ? ', ' . $item['suppressed'] . ' suppressed' : '' );
    }

    // ------------------------------------------------------------------ the mailbox

    /** @return ezcMailImapTransport|ezcMailPop3Transport connected and logged in */
    protected static function connect()
    {
        $server = trim( (string)self::setting( 'Server' ) );
        $port = (int)self::setting( 'Port', 0 );
        $ssl = self::setting( 'SSL', 'enabled' ) !== 'disabled';
        $user = (string)self::setting( 'User' );
        $password = (string)self::setting( 'Password' );
        if ( self::protocol() === 'pop3' )
        {
            $options = new ezcMailPop3TransportOptions();
            $options->ssl = $ssl;
            $transport = new ezcMailPop3Transport( $server, $port > 0 ? $port : null, $options );
            $transport->authenticate( $user, $password );
            return $transport;
        }
        $options = new ezcMailImapTransportOptions();
        $options->ssl = $ssl;
        $transport = new ezcMailImapTransport( $server, $port > 0 ? $port : null, $options );
        $transport->authenticate( $user, $password );
        $transport->selectMailbox( (string)self::setting( 'Folder', 'INBOX' ) );
        return $transport;
    }

    /** @return int[] the messages to read: IMAP the unread ones, POP3 all */
    protected static function messageNumbers( $transport )
    {
        if ( $transport instanceof ezcMailImapTransport )
        {
            $set = $transport->searchMailbox( 'UNSEEN' );
            return array_map( 'intval', (array)$set->getMessageNumbers() );
        }
        return array_map( 'intval', array_keys( (array)$transport->listMessages() ) );
    }

    /** @return string the whole message */
    protected static function fetchRaw( $transport, $number, $dryRun )
    {
        $set = $transport->fetchByMessageNr( $number );
        $raw = '';
        while ( ( $line = $set->getNextLine() ) !== null )
            $raw .= $line;
        if ( $dryRun && $transport instanceof ezcMailImapTransport )
        {
            // a dry run leaves the message unread, so the next real run reads it
            try
            {
                $transport->clearFlag( (string)$number, 'SEEN' );
            }
            catch ( Throwable $e )
            {
            }
        }
        return $raw;
    }

    // ------------------------------------------------------------------ understanding a message

    /**
     * What a message is.
     *
     * @param string $raw the whole message
     * @return array kind (hard, soft, complaint, none), addresses (the failed or complaining recipients), detail
     *               (the status code, or the feedback type)
     */
    public static function classify( $raw )
    {
        $none = array( 'kind' => self::NONE, 'addresses' => array(), 'detail' => '' );
        $parts = self::parts( (string)$raw );
        if ( !$parts )
            return $none;
        // a feedback loop report (ARF)
        foreach ( $parts as $p )
        {
            if ( $p['type'] !== 'message/feedback-report' )
                continue;
            $fields = self::parseHeaders( $p['body'] );
            $type = strtolower( self::first( $fields, 'feedback-type' ) );
            if ( $type === 'not-spam' )
                return array( 'kind' => self::NONE, 'addresses' => array(), 'detail' => $type );
            $addresses = array();
            foreach ( (array)( isset( $fields['original-rcpt-to'] ) ? $fields['original-rcpt-to'] : array() ) as $v )
                $addresses = array_merge( $addresses, self::addresses( $v ) );
            if ( !$addresses )
            {
                // the To of the reported message
                foreach ( $parts as $q )
                {
                    if ( $q['type'] === 'message/rfc822' || $q['type'] === 'text/rfc822-headers' )
                    {
                        $headers = self::parseHeaders( self::split( $q['body'] )[0] );
                        $addresses = self::addresses( self::first( $headers, 'to' ) );
                        break;
                    }
                }
            }
            return array( 'kind' => self::COMPLAINT, 'addresses' => array_values( array_unique( $addresses ) ), 'detail' => $type !== '' ? $type : 'abuse' );
        }
        // a delivery status notification (DSN)
        foreach ( $parts as $p )
        {
            if ( $p['type'] !== 'message/delivery-status' && $p['type'] !== 'message/global-delivery-status' )
                continue;
            $blocks = preg_split( "/\n[ \t]*\n/", trim( $p['body'] ) );
            array_shift( $blocks ); // the per-message fields
            $hard = array();
            $soft = false;
            $detail = '';
            foreach ( $blocks as $block )
            {
                $f = self::parseHeaders( $block );
                $recipient = self::first( $f, 'final-recipient' );
                if ( $recipient === '' )
                    $recipient = self::first( $f, 'original-recipient' );
                $address = self::addresses( preg_replace( '/^\s*[a-z0-9-]+\s*;/i', '', $recipient ) );
                $action = strtolower( self::first( $f, 'action' ) );
                preg_match( '/\d\.\d{1,3}\.\d{1,3}/', self::first( $f, 'status' ), $m );
                $status = $m ? $m[0] : '';
                if ( !$address || $action === 'delivered' || $action === 'relayed' || $action === 'expanded' )
                    continue;
                if ( $action === 'failed' && self::isHardStatus( $status ) )
                {
                    $hard[] = $address[0];
                    $detail = $status;
                }
                else
                {
                    $soft = true;
                    if ( $detail === '' )
                        $detail = $status !== '' ? $status : $action;
                }
            }
            if ( $hard )
                return array( 'kind' => self::HARD, 'addresses' => array_values( array_unique( $hard ) ), 'detail' => $detail );
            if ( $soft )
                return array( 'kind' => self::SOFT, 'addresses' => array(), 'detail' => $detail );
            return $none;
        }
        return $none;
    }

    /** @return bool the status code is one of HardStatusCodes[] (or starts with one ending in a dot) */
    public static function isHardStatus( $status )
    {
        if ( $status === '' || $status[0] !== '5' )
            return false;
        foreach ( self::hardStatusCodes() as $code )
        {
            if ( substr( $code, -1 ) === '.' ? strpos( $status, $code ) === 0 : $status === $code )
                return true;
        }
        return false;
    }

    /**
     * Every part of a message, depth first, the message itself first: type (lower case), params, headers, body
     * (decoded).
     *
     * @param string $raw
     * @param int $depth
     * @return array[]
     */
    public static function parts( $raw, $depth = 0 )
    {
        $raw = str_replace( array( "\r\n", "\r" ), "\n", (string)$raw );
        list( $headerText, $body ) = self::split( $raw );
        $headers = self::parseHeaders( $headerText );
        list( $type, $params ) = self::contentType( self::first( $headers, 'content-type' ) );
        $body = self::decode( $body, strtolower( self::first( $headers, 'content-transfer-encoding' ) ) );
        $out = array( array( 'type' => $type, 'params' => $params, 'headers' => $headers, 'body' => $body ) );
        if ( $depth > 8 )
            return $out;
        if ( strpos( $type, 'multipart/' ) === 0 && isset( $params['boundary'] ) && $params['boundary'] !== '' )
        {
            $chunks = explode( "\n--" . $params['boundary'], "\n" . $body );
            array_shift( $chunks ); // the preamble
            foreach ( $chunks as $chunk )
            {
                if ( strpos( $chunk, '--' ) === 0 )
                    break; // the closing boundary
                $chunk = preg_replace( '/^[ \t]*\n/', '', $chunk );
                $out = array_merge( $out, self::parts( $chunk, $depth + 1 ) );
            }
        }
        return $out;
    }

    /** @return array [ header text, body ] */
    protected static function split( $raw )
    {
        $raw = str_replace( array( "\r\n", "\r" ), "\n", (string)$raw );
        $pos = strpos( $raw, "\n\n" );
        if ( strpos( $raw, "\n" ) === 0 )
            return array( '', substr( $raw, 1 ) );
        return $pos === false ? array( $raw, '' ) : array( substr( $raw, 0, $pos ), substr( $raw, $pos + 2 ) );
    }

    /** @return array lower case name => list of values (folded lines joined) */
    public static function parseHeaders( $text )
    {
        $text = str_replace( array( "\r\n", "\r" ), "\n", (string)$text );
        $text = preg_replace( "/\n[ \t]+/", ' ', $text );
        $out = array();
        foreach ( explode( "\n", $text ) as $line )
        {
            $pos = strpos( $line, ':' );
            if ( $pos === false || $pos === 0 )
                continue;
            $name = strtolower( trim( substr( $line, 0, $pos ) ) );
            if ( preg_match( '/\s/', $name ) )
                continue;
            $out[$name][] = trim( substr( $line, $pos + 1 ) );
        }
        return $out;
    }

    protected static function first( array $headers, $name )
    {
        return isset( $headers[$name][0] ) ? (string)$headers[$name][0] : '';
    }

    /** @return array [ type (lower case, text/plain when missing), params (lower case name => value) ] */
    protected static function contentType( $value )
    {
        if ( trim( $value ) === '' )
            return array( 'text/plain', array() );
        $pieces = explode( ';', $value, 2 );
        $params = array();
        if ( isset( $pieces[1] ) && preg_match_all( '/([a-z0-9_*-]+)\s*=\s*("([^"]*)"|[^;\s]+)/i', $pieces[1], $m, PREG_SET_ORDER ) )
            foreach ( $m as $p )
                $params[strtolower( $p[1] )] = isset( $p[3] ) && $p[3] !== '' ? $p[3] : trim( $p[2], '"' );
        return array( strtolower( trim( $pieces[0] ) ), $params );
    }

    protected static function decode( $body, $encoding )
    {
        if ( $encoding === 'base64' )
            return (string)base64_decode( preg_replace( '/\s+/', '', $body ) );
        if ( $encoding === 'quoted-printable' )
            return quoted_printable_decode( $body );
        return $body;
    }

    /** @return string[] the valid addresses in a header value (lower case) */
    protected static function addresses( $value )
    {
        $out = array();
        if ( preg_match_all( '/[A-Za-z0-9._%+\'=-]+@[A-Za-z0-9.-]+\.[A-Za-z0-9-]{2,}/', (string)$value, $m ) )
            foreach ( $m[0] as $email )
                if ( eZMail::validate( $email ) )
                    $out[] = strtolower( $email );
        return $out;
    }
}
