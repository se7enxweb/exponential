<?php
/**
 * The syslog / journald sink (doc/bc/6.0/audit.md, "Sinks", "syslog / journald"): one RFC 5424 message per
 * record,
 *
 *   <PRI>1 2026-10-02T13:30:01.123Z web1 exponential 991876 access [exp@32473 id="01J9ZK…" name="…" seq="211"
 *   channel="access" user="" ip="203.0.113.0/24" result="failed" request="r-01J9ZK…" hash="sha256:41aa…"] {"v":1,…}
 *
 * PRI = facility * 8 + severity; MSGID is the channel (RFC 5424 limits MSGID to 32 characters, names are longer);
 * the structured data element is exp@32473 (32473 is the documentation enterprise number of RFC 5612, to be
 * replaced by Exponential's own when registered); '"', '\' and ']' in values are escaped (RFC 5424 6.3.3).
 *
 * [AuditSink_syslog] Transport:
 *   local    the system's log: journald's native socket when the system runs journald (the message is the
 *            RFC 5424 line, SYSLOG_IDENTIFIER is AppName, so `journalctl -t <AppName>` finds it and rsyslog
 *            reading the journal gets it too), else /dev/log with the RFC 5424 line
 *   journald journald's native socket (/run/systemd/journal/socket) only
 *   devlog   /dev/log with the RFC 5424 line as it is (journald 252 does not parse RFC 5424 headers there, so
 *            the identifier is lost: use local or journald on a journald system)
 *   udp      Host:Port, the header and structured data only (RFC 5426: the JSON body would exceed a safe datagram)
 *   tcp|tls  Host:Port, the full record as MSG with octet counting (RFC 6587 / RFC 5425)
 * local, journald and devlog deliver at flush time (a local socket write); udp, tcp and tls through the spool.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditSyslogSink extends expAuditSinkBase
{
    const SD_ID = 'exp@32473';
    const JOURNAL_SOCKET = '/run/systemd/journal/socket';
    const DEV_LOG = '/dev/log';

    /** @var array facility name => code */
    public static $facilities = array( 'kern' => 0, 'user' => 1, 'mail' => 2, 'daemon' => 3, 'auth' => 4, 'syslog' => 5,
                                       'lpr' => 6, 'news' => 7, 'uucp' => 8, 'cron' => 9, 'authpriv' => 10, 'ftp' => 11,
                                       'local0' => 16, 'local1' => 17, 'local2' => 18, 'local3' => 19, 'local4' => 20,
                                       'local5' => 21, 'local6' => 22, 'local7' => 23 );

    /** @var string|null Where the last message went (journald, devlog, udp, tcp, tls), for tests and status */
    public $lastTransport = null;

    /** @var string|null The last error */
    public $lastError = null;

    protected function defaults()
    {
        return array( 'Transport' => 'local', 'Host' => '', 'Port' => '514', 'Facility' => 'authpriv',
                      'AppName' => 'exponential', 'Events' => array(), 'MinSeverity' => 'info', 'Timeout' => '5' );
    }

    public function problem()
    {
        $t = $this->transport();
        switch ( $t )
        {
            case 'journald':
                if ( !function_exists( 'socket_create' ) )
                    return 'the PHP extension sockets is missing';
                return file_exists( self::JOURNAL_SOCKET ) ? '' : 'no journald socket at ' . self::JOURNAL_SOCKET;
            case 'devlog':
                if ( !function_exists( 'socket_create' ) )
                    return 'the PHP extension sockets is missing';
                return file_exists( self::DEV_LOG ) ? '' : 'no ' . self::DEV_LOG;
            case 'udp':
            case 'tcp':
            case 'tls':
                return trim( (string)$this->setting( 'Host' ) ) === '' ? "Transport=$t needs a Host" : '';
        }
        return "unknown Transport '" . $this->setting( 'Transport' ) . "' (local, journald, devlog, udp, tcp, tls)";
    }

    /** @return string The transport actually used (local resolved) */
    public function transport()
    {
        $t = strtolower( trim( (string)$this->setting( 'Transport' ) ) );
        if ( $t === '' || $t === 'local' )
            return file_exists( self::JOURNAL_SOCKET ) ? 'journald' : 'devlog';
        return $t;
    }

    public function isSpooled()
    {
        return !in_array( $this->transport(), array( 'journald', 'devlog' ), true );
    }

    /** @return string The APP-NAME / journald identifier: printable ASCII, at most 48 characters */
    public function appName()
    {
        $a = preg_replace( '/[^\x21-\x7e]/', '', (string)$this->setting( 'AppName' ) );
        return $a === '' ? 'exponential' : substr( $a, 0, 48 );
    }

    /** @return int The facility code */
    public function facility()
    {
        $f = strtolower( trim( (string)$this->setting( 'Facility' ) ) );
        return isset( self::$facilities[$f] ) ? self::$facilities[$f] : 10;
    }

    public function deliver( array $records )
    {
        $this->lastError = null;
        $t = $this->transport();
        $n = 0;
        if ( $t === 'tcp' || $t === 'tls' )
            return $this->deliverStream( $records, $t );
        foreach ( $records as $r )
        {
            $ok = false;
            if ( $t === 'journald' )
                $ok = $this->sendUnix( self::JOURNAL_SOCKET, $this->journalDatagram( $r ) );
            elseif ( $t === 'devlog' )
                $ok = $this->sendUnix( self::DEV_LOG, $this->message( $r ) );
            elseif ( $t === 'udp' )
                $ok = $this->sendUdp( $this->message( $r, false ) );
            if ( !$ok )
                break;
            $n++;
        }
        $this->lastTransport = $t;
        return $n;
    }

    /**
     * The RFC 5424 message of a record.
     *
     * @param array $r
     * @param bool $withBody false: header and structured data only (udp)
     * @param bool $withPri false: without <PRI> (journald, which has PRIORITY and SYSLOG_FACILITY)
     * @return string
     */
    public function message( array $r, $withBody = true, $withPri = true )
    {
        $severity = expAuditTaxonomy::rank( isset( $r['severity'] ) ? $r['severity'] : 'info' );
        $pri = $this->facility() * 8 + $severity;
        $host = isset( $r['request']['host'] ) && $r['request']['host'] !== '' ? $r['request']['host'] : ( gethostname() ?: '-' );
        $host = substr( preg_replace( '/[^\x21-\x7e]/', '', (string)$host ), 0, 255 ) ?: '-';
        $pid = isset( $r['request']['pid'] ) ? (string)(int)$r['request']['pid'] : (string)getmypid();
        $msgid = isset( $r['channel'] ) ? substr( preg_replace( '/[^\x21-\x7e]/', '', (string)$r['channel'] ), 0, 32 ) : '-';
        $time = isset( $r['time'] ) ? (string)$r['time'] : gmdate( 'Y-m-d\TH:i:s.000\Z' );
        $sd = array(
            'id' => isset( $r['id'] ) ? $r['id'] : '',
            'name' => isset( $r['name'] ) ? $r['name'] : '',
            'seq' => isset( $r['seq'] ) ? (string)$r['seq'] : '',
            'channel' => isset( $r['channel'] ) ? $r['channel'] : '',
            'user' => isset( $r['actor']['login'] ) ? (string)$r['actor']['login'] : '',
            'ip' => isset( $r['actor']['ip'] ) ? (string)$r['actor']['ip'] : '',
            'result' => isset( $r['result'] ) ? $r['result'] : '',
            'request' => isset( $r['request']['id'] ) ? $r['request']['id'] : '',
            'hash' => isset( $r['hash'] ) ? $r['hash'] : '',
        );
        $params = array();
        foreach ( $sd as $k => $v )
            $params[] = $k . '="' . self::escapeParam( (string)$v ) . '"';
        $line = ( $withPri ? '<' . $pri . '>' : '' ) . '1 ' . $time . ' ' . $host . ' ' . $this->appName() . ' ' . $pid . ' '
              . ( $msgid !== '' ? $msgid : '-' ) . ' [' . self::SD_ID . ' ' . implode( ' ', $params ) . ']';
        if ( $withBody )
        {
            $body = $r;
            unset( $body['file'] );
            $line .= ' ' . expAuditJson::encode( $body );
        }
        return $line;
    }

    /** RFC 5424 6.3.3: '"', '\' and ']' escaped with '\'. */
    public static function escapeParam( $v )
    {
        return strtr( $v, array( '\\' => '\\\\', '"' => '\\"', ']' => '\\]' ) );
    }

    /**
     * The journald native datagram of a record: MESSAGE is the RFC 5424 line without <PRI>; the record's id,
     * name, channel, seq, hash, result and severity are fields of their own (EXP_AUDIT_*).
     *
     * @param array $r
     * @return string
     */
    public function journalDatagram( array $r )
    {
        $message = $this->message( $r, true, false );
        if ( strlen( $message ) > 65536 )
            $message = substr( $message, 0, 65536 ) . '…';
        $fields = array(
            'MESSAGE' => $message,
            'PRIORITY' => (string)expAuditTaxonomy::rank( isset( $r['severity'] ) ? $r['severity'] : 'info' ),
            'SYSLOG_FACILITY' => (string)$this->facility(),
            'SYSLOG_IDENTIFIER' => $this->appName(),
            'SYSLOG_PID' => isset( $r['request']['pid'] ) ? (string)(int)$r['request']['pid'] : (string)getmypid(),
            'EXP_AUDIT_ID' => isset( $r['id'] ) ? (string)$r['id'] : '',
            'EXP_AUDIT_NAME' => isset( $r['name'] ) ? (string)$r['name'] : '',
            'EXP_AUDIT_CHANNEL' => isset( $r['channel'] ) ? (string)$r['channel'] : '',
            'EXP_AUDIT_SEQ' => isset( $r['seq'] ) ? (string)$r['seq'] : '',
            'EXP_AUDIT_HASH' => isset( $r['hash'] ) ? (string)$r['hash'] : '',
            'EXP_AUDIT_RESULT' => isset( $r['result'] ) ? (string)$r['result'] : '',
            'EXP_AUDIT_SEVERITY' => isset( $r['severity'] ) ? (string)$r['severity'] : '',
            'EXP_AUDIT_REQUEST' => isset( $r['request']['id'] ) ? (string)$r['request']['id'] : '',
        );
        $out = '';
        foreach ( $fields as $k => $v )
        {
            if ( strpos( $v, "\n" ) === false )
                $out .= $k . '=' . $v . "\n";
            else
                $out .= $k . "\n" . pack( 'P', strlen( $v ) ) . $v . "\n";
        }
        return $out;
    }

    /** Sends one datagram to a unix socket. */
    protected function sendUnix( $path, $datagram )
    {
        if ( !function_exists( 'socket_create' ) )
        {
            $this->lastError = 'the PHP extension sockets is missing';
            return false;
        }
        $s = @socket_create( AF_UNIX, SOCK_DGRAM, 0 );
        if ( !$s )
        {
            $this->lastError = 'socket_create failed';
            return false;
        }
        $n = @socket_sendto( $s, $datagram, strlen( $datagram ), 0, $path );
        if ( $n === false )
            $this->lastError = socket_strerror( socket_last_error( $s ) );
        socket_close( $s );
        return $n === strlen( $datagram );
    }

    /** Sends one UDP datagram to Host:Port. */
    protected function sendUdp( $message )
    {
        $h = @stream_socket_client( 'udp://' . $this->hostPort(), $errno, $errstr, (float)$this->setting( 'Timeout' ) ?: 5 );
        if ( !$h )
        {
            $this->lastError = "$errno $errstr";
            return false;
        }
        $n = @fwrite( $h, $message );
        fclose( $h );
        return $n === strlen( $message );
    }

    /** Sends records over one TCP or TLS connection with octet counting; returns the count sent. */
    protected function deliverStream( array $records, $transport )
    {
        $h = @stream_socket_client( ( $transport === 'tls' ? 'tls://' : 'tcp://' ) . $this->hostPort(), $errno, $errstr,
                                    (float)$this->setting( 'Timeout' ) ?: 5 );
        $this->lastTransport = $transport;
        if ( !$h )
        {
            $this->lastError = "$errno $errstr";
            return 0;
        }
        stream_set_timeout( $h, (int)$this->setting( 'Timeout' ) ?: 5 );
        $n = 0;
        foreach ( $records as $r )
        {
            $m = $this->message( $r );
            $frame = strlen( $m ) . ' ' . $m;
            if ( @fwrite( $h, $frame ) !== strlen( $frame ) )
            {
                $this->lastError = 'the connection was closed';
                break;
            }
            $n++;
        }
        fclose( $h );
        return $n;
    }

    /** @return string host:port (IPv6 in brackets) */
    protected function hostPort()
    {
        $host = trim( (string)$this->setting( 'Host' ) );
        if ( strpos( $host, ':' ) !== false && $host[0] !== '[' )
            $host = '[' . $host . ']';
        return $host . ':' . ( (int)$this->setting( 'Port' ) ?: 514 );
    }
}
