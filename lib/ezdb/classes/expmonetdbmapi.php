<?php
/**
 * File containing the expMonetDBMapi class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * MonetDB's own client protocol, MAPI (protocol version 9), in plain PHP over a TCP or Unix socket: no PHP extension
 * and no client library is needed. expMonetDBDB sends its statements through it.
 *
 * What travels: blocks of at most 8190 bytes, each with a two-byte little-endian header (length << 1 | last). The server
 * opens with a challenge (salt, protocol, hash algorithms, the password hash); the client answers with the user and
 * {ALGO}hash( hex( PWHASH( password ) ) . salt ). A monetdbd in front of the database proxies (a new challenge on the
 * same connection) or redirects (another address); both are followed.
 *
 * A statement is sent as "s<sql>\n;", a client command as "X<command>". The answer is text: "&1" a result set (its
 * header lines start with "%", each row is "[ v1,\tv2\t]"), "&2" a count of changed rows with the last id an
 * AUTO_INCREMENT column gave, "&3" DDL done, "&4" the autocommit state, "&6" a further block of a result set, "!" an
 * error ("!SQLSTATE!message"). The reply size is set to -1 at login, so a result set arrives whole.
 *
 * @since 6.0.15
 * @package lib
 */
class expMonetDBMapi
{
    /** The largest payload of one block */
    const BLOCK_SIZE = 8190;

    /** The hash algorithms of the answer, best first (as named by MonetDB) */
    const ALGORITHMS = array( 'SHA512' => 'sha512', 'SHA384' => 'sha384', 'SHA256' => 'sha256', 'SHA224' => 'sha224',
                              'SHA1' => 'sha1', 'RIPEMD160' => 'ripemd160' );

    /** @var resource|null the socket */
    protected $Socket = null;

    /** @var string */
    public $Error = '';

    /** @var string the SQLSTATE of the last error, or '' */
    public $SQLState = '';

    /** @var string the version the server announced, or '' */
    public $ServerVersion = '';

    /** @var float seconds a read or write may take */
    protected $Timeout;

    /**
     * @param float $timeout seconds a read or write may wait
     */
    public function __construct( $timeout = 300.0 )
    {
        $this->Timeout = (float)$timeout;
    }

    /**
     * Connects and logs in.
     *
     * @param string $host a host name, an address, or a Unix socket path (starting with /)
     * @param int $port
     * @param string $user
     * @param string $password
     * @param string $database
     * @param float $connectTimeout
     * @return bool false with $Error set
     */
    public function connect( $host, $port, $user, $password, $database, $connectTimeout = 10.0 )
    {
        $this->close();
        $address = ( $host !== '' && $host[0] === '/' ) ? 'unix://' . $host : 'tcp://' . ( $host === '' ? '127.0.0.1' : $host ) . ':' . (int)$port;
        for ( $hop = 0; $hop < 10; $hop++ )
        {
            $errno = 0;
            $errstr = '';
            $socket = @stream_socket_client( $address, $errno, $errstr, $connectTimeout );
            if ( !$socket )
                return $this->fail( "Cannot connect to MonetDB at $address: $errstr", '08001' );
            stream_set_timeout( $socket, (int)$this->Timeout, (int)( ( $this->Timeout - (int)$this->Timeout ) * 1000000 ) );
            $this->Socket = $socket;
            $redirect = $this->login( $user, $password, $database );
            if ( $redirect === true )
            {
                // the whole answer to the session's first commands
                if ( !$this->command( 'reply_size -1' ) )
                    return false;
                return true;
            }
            if ( $redirect === false )
            {
                $this->close();
                return false;
            }
            // another server: mapi:monetdb://host:port/database
            $parts = parse_url( $redirect );
            if ( !$parts || !isset( $parts['host'] ) )
            {
                $this->close();
                return $this->fail( "MonetDB sent a redirect that is not understood: $redirect", '08001' );
            }
            $this->close();
            $address = 'tcp://' . $parts['host'] . ':' . ( isset( $parts['port'] ) ? (int)$parts['port'] : 50000 );
            if ( isset( $parts['path'] ) && trim( $parts['path'], '/' ) !== '' )
                $database = trim( $parts['path'], '/' );
        }
        return $this->fail( 'MonetDB redirected the connection too often', '08001' );
    }

    /**
     * The challenge and answer on the open socket; proxies (merovingian://proxy) are followed on the same socket.
     *
     * @return bool|string true when logged in, false on an error, a mapi: address to connect to instead
     */
    protected function login( $user, $password, $database )
    {
        for ( $round = 0; $round < 10; $round++ )
        {
            $challenge = $this->readMessage();
            if ( $challenge === false )
                return false;
            $answer = self::answer( $challenge, $user, $password, $database );
            if ( $answer === null )
                return $this->fail( 'MonetDB sent a challenge that is not understood: ' . trim( $challenge ), '08001' );
            if ( !$this->writeMessage( $answer ) )
                return false;
            $reply = $this->readMessage();
            if ( $reply === false )
                return false;
            $reply = trim( $reply );
            if ( $reply === '' || $reply[0] === '#' )
                return true;
            if ( $reply[0] === '!' )
                return $this->fail( 'MonetDB refused the login: ' . self::errorText( $reply ), self::errorState( $reply, '28000' ) );
            if ( $reply[0] === '^' )
            {
                $target = trim( strtok( substr( $reply, 1 ), "\n" ) );
                if ( strpos( $target, 'mapi:merovingian://proxy' ) === 0 )
                    continue;
                if ( strpos( $target, 'mapi:monetdb://' ) === 0 )
                    return $target;
                return $this->fail( "MonetDB sent a redirect that is not understood: $target", '08001' );
            }
            return true;
        }
        return $this->fail( 'MonetDB proxied the login too often', '08001' );
    }

    /**
     * The answer to a challenge (pure).
     *
     * @param string $challenge "salt:server:9:ALGOS:endian:PWHASH:..."
     * @return string|null null when the challenge cannot be answered
     */
    public static function answer( $challenge, $user, $password, $database )
    {
        $fields = explode( ':', trim( $challenge ) );
        if ( count( $fields ) < 6 || $fields[2] !== '9' )
            return null;
        $salt = $fields[0];
        $pwAlgo = strtoupper( $fields[5] );
        if ( !isset( self::ALGORITHMS[$pwAlgo] ) )
            return null;
        $offered = array_map( 'strtoupper', explode( ',', $fields[3] ) );
        $chosen = null;
        foreach ( self::ALGORITHMS as $name => $php )
        {
            if ( in_array( $name, $offered, true ) && in_array( $php, hash_algos(), true ) )
            {
                $chosen = $name;
                break;
            }
        }
        if ( $chosen === null )
            return null;
        $pw = hash( self::ALGORITHMS[$pwAlgo], (string)$password );
        $hash = '{' . $chosen . '}' . hash( self::ALGORITHMS[$chosen], $pw . $salt );
        return 'LIT:' . $user . ':' . $hash . ':sql:' . $database . ':';
    }

    /**
     * Runs SQL; the parsed answer.
     *
     * @param string $sql one or more statements
     * @return array|false a list of results (see parse()), false when the connection failed
     */
    public function query( $sql )
    {
        if ( !$this->Socket )
            return $this->fail( 'Not connected to MonetDB', '08003' );
        if ( !$this->writeMessage( 's' . $sql . "\n;" ) )
            return false;
        $reply = $this->readMessage();
        if ( $reply === false )
            return false;
        $results = self::parse( $reply );
        // a result set larger than one reply: fetch the rest (reply_size -1 normally prevents it)
        foreach ( $results as $i => $result )
        {
            if ( $result['type'] !== 'table' )
                continue;
            while ( count( $result['rows'] ) < $result['count'] )
            {
                if ( !$this->writeMessage( 'Xexport ' . $result['id'] . ' ' . count( $result['rows'] ) . ' ' . ( $result['count'] - count( $result['rows'] ) ) ) )
                    return false;
                $more = $this->readMessage();
                if ( $more === false )
                    return false;
                $block = self::parse( $more );
                if ( !$block || $block[0]['type'] !== 'block' || !$block[0]['rows'] )
                    break;
                foreach ( $block[0]['rows'] as $row )
                    $result['rows'][] = $row;
            }
            $results[$i] = $result;
        }
        return $results;
    }

    /**
     * A client command ("reply_size -1", "auto_commit 0").
     *
     * @return bool
     */
    public function command( $command )
    {
        if ( !$this->writeMessage( 'X' . $command ) )
            return false;
        $reply = $this->readMessage();
        if ( $reply === false )
            return false;
        $reply = ltrim( $reply );
        if ( $reply !== '' && $reply[0] === '!' )
            return $this->fail( self::errorText( $reply ), self::errorState( $reply, 'HY000' ) );
        return true;
    }

    /**
     * The results of an answer (pure): array( 'type' => table|update|schema|trans|block|error, ... ).
     * table: columns (names), types, rows (lists of PHP strings or null), id, count. update: affected, lastid.
     * trans: autocommit (bool). error: message, sqlstate.
     *
     * @param string $reply
     * @return array[]
     */
    public static function parse( $reply )
    {
        $results = array();
        $current = null;
        $lines = explode( "\n", $reply );
        foreach ( $lines as $line )
        {
            if ( $line === '' )
                continue;
            $first = $line[0];
            if ( $first === '[' || $first === '=' )
            {
                if ( $current !== null && isset( $current['rows'] ) )
                    $current['rows'][] = $first === '[' ? self::parseRow( $line ) : array( substr( $line, 1 ) );
                continue;
            }
            if ( $first === '%' )
            {
                if ( $current === null || !isset( $current['columns'] ) )
                    continue;
                $hash = strrpos( $line, '#' );
                if ( $hash === false )
                    continue;
                $what = trim( substr( $line, $hash + 1 ) );
                $values = array_map( 'trim', explode( ",\t", substr( $line, 2, $hash - 2 ) ) );
                $values = array_map( 'trim', $values );
                if ( $what === 'name' )
                    $current['columns'] = $values;
                elseif ( $what === 'type' )
                    $current['types'] = $values;
                continue;
            }
            if ( $first === '!' )
            {
                if ( $current !== null )
                    $results[] = $current;
                $current = null;
                $last = end( $results );
                // an error of several lines is one error
                if ( $last && $last['type'] === 'error' && !self::hasState( $line ) )
                {
                    $results[key( $results )]['message'] .= "\n" . self::errorText( $line );
                    continue;
                }
                $results[] = array( 'type' => 'error', 'message' => self::errorText( $line ), 'sqlstate' => self::errorState( $line, 'HY000' ) );
                continue;
            }
            if ( $first === '&' )
            {
                if ( $current !== null )
                    $results[] = $current;
                $current = null;
                $parts = preg_split( '/\s+/', trim( substr( $line, 1 ) ) );
                switch ( $parts[0] )
                {
                    case '1':
                    case '5':
                        $current = array( 'type' => 'table', 'id' => isset( $parts[1] ) ? (int)$parts[1] : 0,
                                          'count' => isset( $parts[2] ) ? (int)$parts[2] : 0,
                                          'columns' => array(), 'types' => array(), 'rows' => array() );
                        break;
                    case '2':
                        $results[] = array( 'type' => 'update', 'affected' => isset( $parts[1] ) ? (int)$parts[1] : 0,
                                            'lastid' => isset( $parts[2] ) ? (int)$parts[2] : -1 );
                        break;
                    case '3':
                        $results[] = array( 'type' => 'schema' );
                        break;
                    case '4':
                        $results[] = array( 'type' => 'trans', 'autocommit' => isset( $parts[1] ) && $parts[1] === 't' );
                        break;
                    case '6':
                        $current = array( 'type' => 'block', 'id' => isset( $parts[1] ) ? (int)$parts[1] : 0,
                                          'columns' => array(), 'rows' => array() );
                        break;
                }
                continue;
            }
            // "#" information and anything else: nothing to keep
        }
        if ( $current !== null )
            $results[] = $current;
        return $results;
    }

    /**
     * The values of one row line "[ 1,\t\"text\",\tNULL\t]" (pure): strings unescaped, NULL as null.
     *
     * @param string $line
     * @return array
     */
    public static function parseRow( $line )
    {
        $values = array();
        $length = strlen( $line );
        $i = 1;
        while ( $i < $length )
        {
            // skip the separators: spaces, tabs and the commas between the values
            while ( $i < $length && ( $line[$i] === ' ' || $line[$i] === "\t" || $line[$i] === ',' ) )
                $i++;
            if ( $i >= $length || $line[$i] === ']' )
                break;
            if ( $line[$i] === '"' )
            {
                $i++;
                $value = '';
                while ( $i < $length && $line[$i] !== '"' )
                {
                    $c = $line[$i];
                    if ( $c === '\\' && $i + 1 < $length )
                    {
                        $n = $line[$i + 1];
                        $i += 2;
                        switch ( $n )
                        {
                            case 'n': $value .= "\n"; break;
                            case 't': $value .= "\t"; break;
                            case 'r': $value .= "\r"; break;
                            case 'f': $value .= "\f"; break;
                            case 'b': $value .= "\x08"; break;
                            case '0': case '1': case '2': case '3':
                                // \ooo: a byte in octal
                                $digits = $n . substr( $line, $i, 2 );
                                if ( preg_match( '/^[0-7]{3}$/', $digits ) )
                                {
                                    $value .= chr( octdec( $digits ) );
                                    $i += 2;
                                }
                                else
                                    $value .= $n;
                                break;
                            default: $value .= $n;
                        }
                        continue;
                    }
                    $value .= $c;
                    $i++;
                }
                $i++;
                $values[] = $value;
                continue;
            }
            // a bare value: up to ",\t" or "\t]"
            $end = $i;
            while ( $end < $length && !( $line[$end] === "\t" || ( $line[$end] === ',' && $end + 1 < $length && $line[$end + 1] === "\t" ) ) )
                $end++;
            $raw = rtrim( substr( $line, $i, $end - $i ) );
            $values[] = $raw === 'NULL' ? null : $raw;
            $i = $end;
        }
        return $values;
    }

    /**
     * Whether an error line starts with a SQLSTATE ("!42000!...").
     */
    protected static function hasState( $line )
    {
        return (bool)preg_match( '/^!?[0-9A-Z]{5}!/', $line );
    }

    /**
     * The message of an error line, without the "!" and the SQLSTATE (pure).
     */
    public static function errorText( $line )
    {
        $text = trim( ltrim( trim( $line ), '!' ) );
        if ( preg_match( '/^[0-9A-Z]{5}!(.*)$/s', $text, $m ) )
            $text = $m[1];
        return trim( str_replace( "\n!", "\n", $text ) );
    }

    /**
     * The SQLSTATE of an error line, else $default (pure).
     */
    public static function errorState( $line, $default )
    {
        if ( preg_match( '/^!([0-9A-Z]{5})!/', ltrim( $line ), $m ) )
            return $m[1];
        return $default;
    }

    /**
     * Writes one message as blocks.
     *
     * @return bool
     */
    protected function writeMessage( $data )
    {
        $data = (string)$data;
        $length = strlen( $data );
        $offset = 0;
        do
        {
            $chunk = substr( $data, $offset, self::BLOCK_SIZE );
            $offset += strlen( $chunk );
            $last = $offset >= $length ? 1 : 0;
            $block = pack( 'v', ( strlen( $chunk ) << 1 ) | $last ) . $chunk;
            if ( !$this->writeAll( $block ) )
                return false;
        }
        while ( $offset < $length );
        return true;
    }

    protected function writeAll( $bytes )
    {
        $total = strlen( $bytes );
        $written = 0;
        while ( $written < $total )
        {
            $n = @fwrite( $this->Socket, substr( $bytes, $written ) );
            if ( $n === false || $n === 0 )
            {
                $meta = stream_get_meta_data( $this->Socket );
                $this->close();
                return $this->fail( !empty( $meta['timed_out'] ) ? 'Writing to MonetDB timed out' : 'The connection to MonetDB was lost', '08006' );
            }
            $written += $n;
        }
        return true;
    }

    /**
     * Reads one message (blocks up to the last one).
     *
     * @return string|false
     */
    protected function readMessage()
    {
        $message = '';
        while ( true )
        {
            $header = $this->readExactly( 2 );
            if ( $header === false )
                return false;
            $value = unpack( 'v', $header )[1];
            $length = $value >> 1;
            if ( $length > 0 )
            {
                $chunk = $this->readExactly( $length );
                if ( $chunk === false )
                    return false;
                $message .= $chunk;
            }
            if ( $value & 1 )
                return $message;
        }
    }

    protected function readExactly( $length )
    {
        $data = '';
        while ( strlen( $data ) < $length )
        {
            $chunk = @fread( $this->Socket, $length - strlen( $data ) );
            if ( $chunk === false || $chunk === '' )
            {
                $meta = $this->Socket ? stream_get_meta_data( $this->Socket ) : array();
                if ( empty( $meta['timed_out'] ) && $chunk === '' && $this->Socket && !feof( $this->Socket ) )
                    continue;
                $this->close();
                return $this->fail( !empty( $meta['timed_out'] ) ? 'Reading from MonetDB timed out' : 'The connection to MonetDB was lost', '08006' );
            }
            $data .= $chunk;
        }
        return $data;
    }

    /**
     * @return bool whether a socket is open
     */
    public function isConnected()
    {
        return $this->Socket !== null;
    }

    public function close()
    {
        if ( $this->Socket )
            @fclose( $this->Socket );
        $this->Socket = null;
    }

    protected function fail( $message, $state )
    {
        $this->Error = $message;
        $this->SQLState = $state;
        return false;
    }
}
