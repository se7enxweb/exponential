<?php
/**
 * File containing the expFakeMailServer class.
 *
 * A fake IMAP or POP3 server on 127.0.0.1 for tests of mailbox readers (expMailReceive, the kernel's bounce reader,
 * the newsletter's bounce mailboxes, the help desk's mail intake). It runs tests/toolkit/extras/fakemailserver/
 * server.php through ezpLoopbackServer, so it neither holds the test process's descriptors nor outlives it.
 *
 *   $server = expFakeMailServer::start( 'imap', array( $raw1, $raw2 ) );
 *   $account = $server->account();          // for expMailReceive::connect()
 *   ...
 *   $server->commands();                    // every command the client sent
 *   $server->left();                        // the numbers still on the server
 *   $server->stop();
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

require_once __DIR__ . '/ezploopbackserver.php';

class expFakeMailServer
{
    const PASSWORD = 'fake-secret';

    public $protocol;
    public $port;
    public $dir;
    protected $process;

    /**
     * @param string $protocol imap or pop3
     * @param string[] $messages the raw messages, numbered from 1
     * @return expFakeMailServer listening
     * @throws RuntimeException when the server does not answer within five seconds
     */
    public static function start( $protocol, array $messages )
    {
        $server = new self();
        $server->protocol = $protocol === 'pop3' ? 'pop3' : 'imap';
        $server->port = self::freePort();
        $server->dir = sys_get_temp_dir() . '/expfakemail-' . getmypid() . '-' . $server->port;
        @mkdir( $server->dir . '/spool', 0700, true );
        $n = 0;
        foreach ( $messages as $raw )
            file_put_contents( $server->dir . '/spool/' . ( ++$n ) . '.eml', $raw );
        touch( $server->dir . '/commands.log' );
        $server->process = ezpLoopbackServer::start(
            array( PHP_BINARY, __DIR__ . '/extras/fakemailserver/server.php', $server->protocol, (string)$server->port,
                   $server->dir . '/spool', $server->dir . '/commands.log', self::PASSWORD ),
            null, null, $server->dir . '/server.log' );
        $until = microtime( true ) + 5;
        while ( microtime( true ) < $until )
        {
            $probe = @stream_socket_client( 'tcp://127.0.0.1:' . $server->port, $errno, $errstr, 0.2 );
            if ( $probe )
            {
                fclose( $probe );
                // the probe's empty session is in no log: the server logs commands only
                return $server;
            }
            usleep( 50000 );
        }
        $server->stop();
        throw new RuntimeException( 'the fake ' . $protocol . ' server did not start' );
    }

    /** @return int a port nothing listens on right now */
    public static function freePort()
    {
        $socket = stream_socket_server( 'tcp://127.0.0.1:0', $errno, $errstr );
        $name = stream_socket_get_name( $socket, false );
        fclose( $socket );
        return (int)substr( $name, strrpos( $name, ':' ) + 1 );
    }

    /**
     * @param array $overrides e.g. array( 'password' => 'wrong' )
     * @return array account settings for expMailReceive::connect()
     */
    public function account( array $overrides = array() )
    {
        return array_merge( array( 'protocol' => $this->protocol, 'server' => '127.0.0.1', 'port' => $this->port,
                                   'ssl' => false, 'user' => 'reader', 'password' => self::PASSWORD,
                                   'folder' => 'INBOX', 'timeout' => 5 ), $overrides );
    }

    /** @return string[] the commands the clients sent, in order (passwords masked) */
    public function commands()
    {
        clearstatcache();
        return array_values( array_filter( explode( "\n", (string)file_get_contents( $this->dir . '/commands.log' ) ), 'strlen' ) );
    }

    /** @return int[] the numbers of the messages still on the server */
    public function left()
    {
        $out = array();
        foreach ( (array)glob( $this->dir . '/spool/*.eml' ) as $path )
            $out[] = (int)basename( $path, '.eml' );
        sort( $out );
        return $out;
    }

    public function stop()
    {
        ezpLoopbackServer::stop( $this->process );
        $this->process = null;
        foreach ( array_merge( (array)glob( $this->dir . '/spool/*' ), (array)glob( $this->dir . '/*.log' ) ) as $file )
            @unlink( $file );
        @rmdir( $this->dir . '/spool' );
        @rmdir( $this->dir );
    }
}
