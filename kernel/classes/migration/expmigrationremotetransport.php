<?php
/**
 * File containing the expMigrationRemoteTransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The commands that reach one remote (expMigrationRemote): running a console script of the installation there,
 * sending files to it and, for FTP, reading a file back. Every method only builds operations; expMigrationRemoteProcess
 * runs them. An operation is one of
 *
 *   array( 'argv' => string[], 'env' => array, 'cwd' => string|null, 'stdin' => string|null )   a process, no shell
 *   array( 'copy' => array( local file => destination file ) )                                  a local copy (local)
 *   array( 'http' => array( url, body, key ) )        a request to the remote's HTTP trigger (FTP remotes with Trigger),
 *                                                     signed when it starts (expMigrationTrigger, curl -K -)
 *
 * Transports: ssh (commands over ssh, files with scp), sftp (files with an sftp batch), rsync (files with rsync over
 * ssh, --checksum and --partial: only what differs is sent, a broken transfer resumes), ftp and ftps (curl; files
 * only, no commands), local (a directory of this host, commands with the PHP CLI of the remote setting Php).
 *
 * Relative paths of the files and the remote's Path are restricted to characters that need no quoting
 * (expMigrationRemote::validate(), safePath()); the remote shell command is quoted all the same.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRemoteTransport
{
    /** @var expMigrationRemote */
    protected $remote;

    /** @var expMigrationRemoteCredential */
    protected $credential;

    /** @var string */
    protected $knownHosts;

    /** @var bool the host key was checked against HostKeyFingerprint: only known keys are accepted */
    protected $strict = false;

    /** @var string|null the secret of the remote's HTTP trigger (setTriggerKey()) */
    protected $triggerKey = null;

    /**
     * @param expMigrationRemote $remote
     * @param expMigrationRemoteCredential $credential problem() already called
     * @param string $knownHosts the remote's own known_hosts file
     */
    public function __construct( expMigrationRemote $remote, expMigrationRemoteCredential $credential, $knownHosts )
    {
        $this->remote = $remote;
        $this->credential = $credential;
        $this->knownHosts = (string)$knownHosts;
    }

    /** @param bool $strict */
    public function setStrict( $strict )
    {
        $this->strict = (bool)$strict;
    }

    /** @param string|null $key the secret the remote's HTTP trigger checks (TriggerSecret) */
    public function setTriggerKey( $key )
    {
        $this->triggerKey = $key === null ? null : (string)$key;
    }

    /**
     * Whether a relative path of a file to send needs no quoting and stays inside the installation. Pure.
     *
     * @param string $path
     * @return bool
     */
    public static function safePath( $path )
    {
        $path = (string)$path;
        return $path !== '' && $path[0] !== '/' && preg_match( '#^[A-Za-z0-9._@+-]+(/[A-Za-z0-9._@+-]+)*$#', $path ) === 1
            && strpos( "/$path/", '/../' ) === false && strpos( "/$path/", '/./' ) === false;
    }

    /**
     * A word quoted for a POSIX shell. Pure.
     *
     * @param string $word
     * @return string
     */
    public static function quote( $word )
    {
        return "'" . str_replace( "'", "'\\''", (string)$word ) . "'";
    }

    /** @return string user@host or host */
    protected function login()
    {
        return ( $this->remote->user() !== '' ? $this->remote->user() . '@' : '' ) . $this->remote->host();
    }

    /** @return array the ssh options (with -p port) and environment */
    protected function ssh( $portFlag = '-p' )
    {
        $ssh = $this->credential->ssh( $this->knownHosts, $this->strict );
        return array( 'options' => array_merge( $ssh['options'], array( $portFlag, (string)$this->remote->port() ) ), 'env' => $ssh['env'] );
    }

    /**
     * Runs a console script of the installation on the remote: <Php> <script> <arguments> in the remote's Path.
     *
     * @param string[] $args the script (relative to the installation) and its arguments
     * @return array|null the operation; null for a transport that cannot run commands (ftp, ftps)
     */
    public function run( array $args )
    {
        $transport = $this->remote->transport();
        if ( $this->remote->setting( 'AllowRootUser' ) === 'enabled' )
            $args[] = '--allow-root-user';
        if ( $transport === 'local' )
            return array( 'argv' => array_merge( array( $this->remote->php() ), $args ), 'env' => array(), 'cwd' => $this->remote->path(), 'stdin' => null );
        if ( !$this->remote->canRun() )
            return null;
        if ( $this->remote->isFtp() )
        {
            // an FTP remote runs nothing over FTP: its HTTP trigger runs the console script there
            $request = expMigrationTrigger::requestFromArgs( $args );
            if ( $request === null || $this->triggerKey === null )
                return null;
            return array( 'http' => array( 'url' => $this->remote->trigger(), 'body' => json_encode( $request, JSON_UNESCAPED_SLASHES ), 'key' => $this->triggerKey ) );
        }
        $command = 'cd ' . self::quote( $this->remote->path() ) . ' && ' . implode( ' ', array_map( array( __CLASS__, 'quote' ), array_merge( array( $this->remote->php() ), $args ) ) );
        $ssh = $this->ssh();
        return array( 'argv' => array_merge( array( 'ssh' ), $ssh['options'], array( '--', $this->login(), $command ) ), 'env' => $ssh['env'], 'cwd' => null, 'stdin' => null );
    }

    /**
     * The operations that send files to the remote, folders made where missing.
     *
     * @param array $files relative path (on both sides) => local file
     * @param string $localRoot the source installation's root (rsync sends relative to it)
     * @return array[] operations, in order
     */
    public function send( array $files, $localRoot )
    {
        if ( !$files )
            return array();
        ksort( $files );
        $path = $this->remote->path();
        $folders = array();
        foreach ( array_keys( $files ) as $rel )
        {
            $dir = dirname( $rel );
            if ( $dir !== '.' && $dir !== '' )
                $folders[$dir][] = $rel;
            else
                $folders[''][] = $rel;
        }
        switch ( $this->remote->transport() )
        {
            case 'local':
                $copy = array();
                foreach ( $files as $rel => $local )
                    $copy[$local] = $path . '/' . $rel;
                return array( array( 'copy' => $copy ) );

            case 'ssh':
                $ssh = $this->ssh();
                $mkdir = array();
                foreach ( array_keys( $folders ) as $dir )
                    $mkdir[] = self::quote( $path . ( $dir !== '' ? '/' . $dir : '' ) );
                $ops = array( array( 'argv' => array_merge( array( 'ssh' ), $ssh['options'], array( '--', $this->login(), 'mkdir -p ' . implode( ' ', $mkdir ) ) ),
                                     'env' => $ssh['env'], 'cwd' => null, 'stdin' => null ) );
                $scp = $this->ssh( '-P' );
                foreach ( $folders as $dir => $rels )
                {
                    $locals = array();
                    foreach ( $rels as $rel )
                        $locals[] = $files[$rel];
                    $ops[] = array( 'argv' => array_merge( array( 'scp', '-p', '-q' ), $scp['options'], $locals,
                                                           array( $this->login() . ':' . $path . ( $dir !== '' ? '/' . $dir : '' ) . '/' ) ),
                                    'env' => $scp['env'], 'cwd' => null, 'stdin' => null );
                }
                return $ops;

            case 'sftp':
                $sftp = $this->ssh( '-P' );
                $batch = array();
                $made = array();
                foreach ( array_keys( $folders ) as $dir )
                {
                    $current = $path;
                    foreach ( $dir !== '' ? explode( '/', $dir ) : array() as $part )
                    {
                        $current .= '/' . $part;
                        if ( !isset( $made[$current] ) )
                        {
                            // "-" before a command: a folder that exists already is no error
                            $batch[] = '-mkdir ' . $current;
                            $made[$current] = true;
                        }
                    }
                }
                foreach ( $files as $rel => $local )
                    $batch[] = 'put -p ' . $local . ' ' . $path . '/' . $rel;
                return array( array( 'argv' => array_merge( array( 'sftp', '-q', '-b', '-' ), $sftp['options'], array( '--', $this->login() ) ),
                                     'env' => $sftp['env'], 'cwd' => null, 'stdin' => implode( "\n", $batch ) . "\n" ) );

            case 'rsync':
                $ssh = $this->ssh();
                $shell = 'ssh ' . implode( ' ', array_map( array( __CLASS__, 'quote' ), $ssh['options'] ) );
                return array( array( 'argv' => array( 'rsync', '-rt', '--checksum', '--partial', '--files-from=-', '-e', $shell,
                                                      rtrim( (string)$localRoot, '/' ) . '/', $this->login() . ':' . $path . '/' ),
                                     'env' => $ssh['env'], 'cwd' => null, 'stdin' => implode( "\n", array_keys( $files ) ) . "\n" ) );

            case 'ftp':
            case 'ftps':
                $netrc = $this->credential->netrc( $this->remote->host(), $this->remote->user() );
                $ops = array();
                foreach ( $files as $rel => $local )
                    $ops[] = array( 'argv' => array_merge( $this->curl( $netrc ), array( '--ftp-create-dirs', '-T', $local, $this->ftpUrl( $rel ) ) ),
                                    'env' => array(), 'cwd' => null, 'stdin' => null );
                return $ops;
        }
        return array();
    }

    /**
     * The operation that reads a file of the remote back (FTP: the check that a sent file arrived unchanged).
     *
     * @param string $rel
     * @return array
     */
    public function fetch( $rel )
    {
        if ( $this->remote->transport() === 'local' )
            return array( 'argv' => array( 'cat', '--', $this->remote->path() . '/' . $rel ), 'env' => array(), 'cwd' => null, 'stdin' => null );
        $netrc = $this->credential->netrc( $this->remote->host(), $this->remote->user() );
        return array( 'argv' => array_merge( $this->curl( $netrc ), array( $this->ftpUrl( $rel ) ) ), 'env' => array(), 'cwd' => null, 'stdin' => null );
    }

    /** @return string[] curl and its options */
    protected function curl( $netrc )
    {
        $argv = array( 'curl', '-sS', '--fail', '--connect-timeout', '20', '--netrc-file', $netrc );
        if ( $this->remote->transport() === 'ftps' )
            $argv[] = '--ssl-reqd';
        return $argv;
    }

    /** @return string the ftp:// address of a file of the installation */
    protected function ftpUrl( $rel )
    {
        $host = $this->remote->host();
        if ( strpos( $host, ':' ) !== false && $host[0] !== '[' )
            $host = '[' . $host . ']';
        return 'ftp://' . $host . ':' . $this->remote->port() . $this->remote->path() . '/' . $rel;
    }

    /** @return array the operation that reads the remote's host keys (ssh-keyscan) */
    public function keyscan()
    {
        return array( 'argv' => array( 'ssh-keyscan', '-T', '15', '-p', (string)$this->remote->port(), '--', $this->remote->host() ),
                      'env' => array(), 'cwd' => null, 'stdin' => null );
    }

    /**
     * The known_hosts lines of an ssh-keyscan output whose key has the fingerprint. Pure.
     *
     * @param string $output ssh-keyscan's output
     * @param string $fingerprint SHA256:<base64 without padding>
     * @return string[] the matching lines
     */
    public static function matchingKeys( $output, $fingerprint )
    {
        $want = rtrim( (string)$fingerprint, '=' );
        $lines = array();
        foreach ( preg_split( '/\r?\n/', (string)$output ) as $line )
        {
            $parts = preg_split( '/\s+/', trim( $line ) );
            if ( count( $parts ) < 3 || $parts[0] === '' || $parts[0][0] === '#' )
                continue;
            $blob = base64_decode( $parts[2], true );
            if ( $blob === false )
                continue;
            if ( hash_equals( $want, 'SHA256:' . rtrim( base64_encode( hash( 'sha256', $blob, true ) ), '=' ) ) )
                $lines[] = $parts[0] . ' ' . $parts[1] . ' ' . $parts[2];
        }
        return $lines;
    }
}
