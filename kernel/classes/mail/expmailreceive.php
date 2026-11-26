<?php
/**
 * File containing the expMailReceive class.
 *
 * The shared rules for every part of the system that reads a mailbox (IMAP or POP3): the kernel's bounce reader
 * (expMailBounceReader), the newsletter's bounce mailboxes (cjw_newsletter), the help desk's mail intake
 * (ticketsystem) and any extension that collects mail.
 *
 *  - What a message is, from its own top-level headers: automatic (RFC 3834 Auto-Submitted, Precedence and the
 *    common X-Autoreply headers), a delivery report (multipart/report), sent by a mail system (MAILER-DAEMON,
 *    postmaster). isAutomaticReply() combines them: an out-of-office or other automatic reply, never a bounce. A
 *    reader must not answer such a message, nor count it as a delivery failure.
 *  - One failure stops one unit of work, not the run: isolate() runs a piece of work per mailbox account (or per
 *    message) and records each failure, so one account that cannot connect, or one message that cannot be stored,
 *    leaves the others to be read.
 *  - A message leaves the server only after it is stored: deleteMessages() deletes the numbers a reader has stored
 *    (or already knew) at the end of the run; IMAP expunges them, POP3 removes them when the connection is closed.
 *  - connect(), messageNumbers(), fetchRaw() and disconnect() wrap the ezcMail transports with one settings array,
 *    so a reader does not repeat the connection code.
 *
 * The class is stateless and works without a database; the helpers that read a message take the raw message text.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

class expMailReceive
{
    /** Precedence values that mark a message as automatic (any case) */
    const AUTOMATIC_PRECEDENCE = array( 'bulk', 'junk', 'list', 'auto_reply' );

    // ------------------------------------------------------------------ what a message is

    /**
     * The headers of the message itself (up to the first empty line, folded lines joined), not those of a part or
     * of a quoted copy.
     *
     * @param string $raw the whole message
     * @return array lower case name => value of its first occurrence
     */
    public static function topHeaders( $raw )
    {
        $raw = (string)$raw;
        $end = strpos( $raw, "\n\n" );
        $endCrlf = strpos( $raw, "\r\n\r\n" );
        if ( $endCrlf !== false && ( $end === false || $endCrlf < $end ) )
            $end = $endCrlf;
        $head = $end === false ? $raw : substr( $raw, 0, $end );
        $out = array();
        $name = null;
        foreach ( preg_split( "/\r\n|\n|\r/", $head ) as $line )
        {
            if ( $line === '' )
                break;
            if ( $line[0] === ' ' || $line[0] === "\t" )
            {
                if ( $name !== null )
                    $out[$name] .= ' ' . trim( $line );
                continue;
            }
            $pieces = explode( ':', $line, 2 );
            if ( count( $pieces ) !== 2 )
            {
                $name = null;
                continue;
            }
            $name = strtolower( trim( $pieces[0] ) );
            if ( isset( $out[$name] ) )
                $name = null; // only the first occurrence counts
            else
                $out[$name] = trim( $pieces[1] );
        }
        return $out;
    }

    /**
     * The rule for an automatic message (RFC 3834 and the common vendor headers).
     *
     * @param array $headers lower case name => value (topHeaders(); a value may also be a list, its first entry counts)
     * @return bool Auto-Submitted present and not "no", Precedence bulk, junk, list or auto_reply, or an
     *              X-Autoreply / X-Autorespond header
     */
    public static function isAutomaticHeaders( array $headers )
    {
        $auto = strtolower( trim( self::value( $headers, 'auto-submitted' ) ) );
        if ( $auto !== '' && $auto !== 'no' )
            return true;
        if ( in_array( strtolower( trim( self::value( $headers, 'precedence' ) ) ), self::AUTOMATIC_PRECEDENCE, true ) )
            return true;
        return isset( $headers['x-autoreply'] ) || isset( $headers['x-autorespond'] );
    }

    /**
     * @param array $headers lower case name => value
     * @return bool a delivery or feedback report (Content-Type multipart/report)
     */
    public static function isReport( array $headers )
    {
        return stripos( self::value( $headers, 'content-type' ), 'multipart/report' ) !== false;
    }

    /**
     * @param string $from the From header
     * @return bool sent by a mail system itself (MAILER-DAEMON or postmaster), which marks its bounces automatic too
     */
    public static function isMailSystemSender( $from )
    {
        return (bool)preg_match( '/\b(mailer-daemon|postmaster)@/i', (string)$from );
    }

    /**
     * An automatic reply (out of office, an autoresponder): automatic headers, and neither a report nor a message of
     * a mail system, whose bounces carry Auto-Submitted as well. Such a message is no bounce whatever reply codes its
     * text quotes, and a reader must never answer it.
     *
     * @param string|array $message the whole message, or its topHeaders()
     * @return bool
     */
    public static function isAutomaticReply( $message )
    {
        $headers = is_array( $message ) ? $message : self::topHeaders( $message );
        if ( self::isReport( $headers ) || self::isMailSystemSender( self::value( $headers, 'from' ) ) )
            return false;
        return self::isAutomaticHeaders( $headers );
    }

    /** @return string the header's value; the first entry when the reader keeps a list per name */
    protected static function value( array $headers, $name )
    {
        if ( !isset( $headers[$name] ) )
            return '';
        $value = $headers[$name];
        if ( is_array( $value ) )
            $value = count( $value ) ? reset( $value ) : '';
        return (string)$value;
    }

    // ------------------------------------------------------------------ one failure stops one unit

    /**
     * Runs $work once per key (a mailbox account, a message number) and goes on after a failure.
     *
     * @param array $keys key => item; the item is handed to $work
     * @param callable $work function( $item, $key ): the unit's result; throwing marks the unit as failed
     * @param callable|null $onError function( Throwable $e, $item, $key ): called after a failure, e.g. to log it or
     *                               to close a connection; an exception it throws is ignored
     * @return array key => array( ok bool, result mixed (null when failed), error string ('' when ok) )
     */
    public static function isolate( array $keys, $work, $onError = null )
    {
        $out = array();
        foreach ( $keys as $key => $item )
        {
            try
            {
                $out[$key] = array( 'ok' => true, 'result' => call_user_func( $work, $item, $key ), 'error' => '' );
            }
            catch ( Throwable $e )
            {
                $out[$key] = array( 'ok' => false, 'result' => null, 'error' => substr( $e->getMessage(), 0, 300 ) );
                if ( $onError )
                {
                    try
                    {
                        call_user_func( $onError, $e, $item, $key );
                    }
                    catch ( Throwable $e2 )
                    {
                        // the failure is recorded; a failing handler adds nothing
                    }
                }
            }
        }
        return $out;
    }

    // ------------------------------------------------------------------ the transports

    /**
     * Connects and logs in; IMAP selects the folder.
     *
     * @param array $account protocol (imap or pop3, default imap), server, port (0: the protocol's), ssl (bool,
     *                       default true), user, password, folder (IMAP, default INBOX), timeout (seconds, optional)
     * @return ezcMailImapTransport|ezcMailPop3Transport
     * @throws ezcMailTransportException when the server cannot be reached or refuses the login
     */
    public static function connect( array $account )
    {
        $server = trim( (string)( isset( $account['server'] ) ? $account['server'] : '' ) );
        $port = isset( $account['port'] ) ? (int)$account['port'] : 0;
        $ssl = isset( $account['ssl'] ) ? (bool)$account['ssl'] : true;
        $user = (string)( isset( $account['user'] ) ? $account['user'] : '' );
        $password = (string)( isset( $account['password'] ) ? $account['password'] : '' );
        $protocol = isset( $account['protocol'] ) && strtolower( (string)$account['protocol'] ) === 'pop3' ? 'pop3' : 'imap';
        $options = $protocol === 'pop3' ? new ezcMailPop3TransportOptions() : new ezcMailImapTransportOptions();
        $options->ssl = $ssl;
        if ( isset( $account['timeout'] ) && (int)$account['timeout'] > 0 )
            $options->timeout = (int)$account['timeout'];
        if ( $protocol === 'pop3' )
        {
            $transport = new ezcMailPop3Transport( $server, $port > 0 ? $port : null, $options );
            $transport->authenticate( $user, $password );
            return $transport;
        }
        $transport = new ezcMailImapTransport( $server, $port > 0 ? $port : null, $options );
        $transport->authenticate( $user, $password );
        $transport->selectMailbox( (string)( isset( $account['folder'] ) && $account['folder'] !== '' ? $account['folder'] : 'INBOX' ) );
        return $transport;
    }

    /**
     * @param ezcMailImapTransport|ezcMailPop3Transport $transport
     * @param bool $unseenOnly IMAP: only the unread messages (POP3 has no flags and lists all)
     * @return int[] message numbers
     */
    public static function messageNumbers( $transport, $unseenOnly = true )
    {
        if ( $transport instanceof ezcMailImapTransport && $unseenOnly )
        {
            $set = $transport->searchMailbox( 'UNSEEN' );
            return array_map( 'intval', (array)$set->getMessageNumbers() );
        }
        return array_map( 'intval', array_keys( (array)$transport->listMessages() ) );
    }

    /**
     * @param ezcMailImapTransport|ezcMailPop3Transport $transport
     * @param int $number
     * @param bool $keepUnread IMAP: clear the Seen flag that fetching set (a dry run that must leave the message unread)
     * @return string the whole message
     */
    public static function fetchRaw( $transport, $number, $keepUnread = false )
    {
        $set = $transport->fetchByMessageNr( (int)$number );
        $raw = '';
        while ( ( $line = $set->getNextLine() ) !== null )
            $raw .= $line;
        if ( $keepUnread && $transport instanceof ezcMailImapTransport )
        {
            try
            {
                $transport->clearFlag( (string)(int)$number, 'SEEN' );
            }
            catch ( Throwable $e )
            {
                // the message is read; the next run reads it only if the server keeps it unread
            }
        }
        return $raw;
    }

    /**
     * Deletes the messages a reader has stored or already knew; nothing else. IMAP expunges them at once, POP3
     * removes them when the connection is closed (disconnect()).
     *
     * @param ezcMailImapTransport|ezcMailPop3Transport $transport
     * @param int[] $numbers
     * @return int the number of messages marked for deletion
     */
    public static function deleteMessages( $transport, array $numbers )
    {
        $numbers = array_values( array_unique( array_map( 'intval', $numbers ) ) );
        foreach ( $numbers as $number )
            $transport->delete( $number );
        if ( count( $numbers ) && $transport instanceof ezcMailImapTransport )
            $transport->expunge();
        return count( $numbers );
    }

    /**
     * Closes a connection and ignores a failure to do so (the work is done or already recorded as failed).
     *
     * @param ezcMailImapTransport|ezcMailPop3Transport|null $transport
     */
    public static function disconnect( $transport )
    {
        if ( !$transport )
            return;
        try
        {
            $transport->disconnect();
        }
        catch ( Throwable $e )
        {
        }
    }
}
