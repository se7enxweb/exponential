<?php
/**
 * File containing the expStaticCredentials class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * How git signs in to a target's remote (static publishing): the target's Credential setting.
 *
 *   token:<secret name>      a token (a fine-grained GitHub token, a GitLab or Gitea access token) read from the secret
 *   token:env:<VARIABLE>     store (Setup > Secrets) or from an environment variable of the process
 *   sshkey:<secret name>     an SSH deploy key: the private key in the secret store (exp:static:keys makes one per
 *                            target, ed25519), the public key added to the repository as a deploy key with write access
 *   app:<installation>       a GitHub App installation token, from the GitHub integration (provider()); without it the
 *                            credential is refused
 *   none                     the host's own credentials (an SSH agent, a credential helper, a file:// or local remote)
 *
 * A credential reaches git only through the environment of the git process, never through its command line, the
 * remote address or a log:
 *
 *   token   GIT_ASKPASS names bin/shell/expstaticaskpass.sh, which prints the user name and the token git asks for
 *           from EXP_STATIC_GIT_USER and EXP_STATIC_GIT_TOKEN; credential helpers are switched off for the call
 *   sshkey  the private key is written to a file only the process can read for the time of the call (removed in
 *           cleanup()); GIT_SSH_COMMAND names it, with BatchMode and the target's own known_hosts file
 *   none    GIT_TERMINAL_PROMPT=0 and BatchMode: git never waits for a password
 *
 * redact() replaces the token in any text before it is shown or stored, as a second line of defence.
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticCredentials
{
    /** The user name sent with a token: GitHub ignores it for tokens, GitLab and Gitea accept any */
    const TOKEN_USER = 'x-access-token';

    /** The scope of the secrets static publishing stores */
    const SCOPE = 'staticpublish';

    /** The variables the askpass helper reads */
    const ENV_USER = 'EXP_STATIC_GIT_USER';
    const ENV_TOKEN = 'EXP_STATIC_GIT_TOKEN';

    /** @var callable[] kind => callable( $name, expStaticTarget ) returning a token or null (the GitHub App: app) */
    protected static $providers = array();

    /** @var string token, sshkey, app, none */
    protected $kind;

    /** @var string the secret name, env:VARIABLE or installation */
    protected $name;

    /** @var string|null the token or private key, once read */
    protected $value = null;

    /** @var string[] files written for a call (the private key), removed by cleanup() */
    protected $files = array();

    /** @var string the folder for the key file and known_hosts */
    protected $workDir;

    /** @var string the askpass helper */
    protected $askpass;

    /** @var callable|null name => value instead of expSecrets::get() (tests) */
    protected $lookup;

    /**
     * @param string $credential the target's Credential (token:<name>, sshkey:<name>, app:<installation>, none)
     * @param array $options work_dir (the folder of the targets, for the key file and known_hosts), root (the
     *        installation, for the askpass helper), lookup (callable name => value|null, default expSecrets::get())
     */
    public function __construct( $credential, array $options = array() )
    {
        list( $this->kind, $this->name ) = self::parse( $credential );
        $root = isset( $options['root'] ) ? rtrim( str_replace( '\\', '/', (string)$options['root'] ), '/' ) : rtrim( str_replace( '\\', '/', (string)getcwd() ), '/' );
        $this->workDir = isset( $options['work_dir'] ) ? rtrim( str_replace( '\\', '/', (string)$options['work_dir'] ), '/' ) : $root . '/var/static-publish';
        $this->askpass = $root . '/bin/shell/expstaticaskpass.sh';
        $this->lookup = isset( $options['lookup'] ) && is_callable( $options['lookup'] ) ? $options['lookup'] : null;
    }

    /**
     * A Credential setting as kind and name. Pure.
     *
     * @param string $credential
     * @return array( kind, name ): kind '' when it is not one of token, sshkey, app, none
     */
    public static function parse( $credential )
    {
        $credential = trim( (string)$credential );
        if ( $credential === '' || $credential === 'none' )
            return array( 'none', '' );
        $colon = strpos( $credential, ':' );
        $kind = $colon === false ? $credential : substr( $credential, 0, $colon );
        $name = $colon === false ? '' : substr( $credential, $colon + 1 );
        if ( !in_array( $kind, array( 'token', 'sshkey', 'app' ), true ) || $name === '' )
            return array( '', $name );
        return array( $kind, $name );
    }

    /**
     * Registers where a kind of credential gets its token: the GitHub integration registers app (a GitHub App
     * installation token). The callable gets the installation and the target and returns a token or null.
     *
     * @param string $kind
     * @param callable|null $provider null removes it
     */
    public static function provider( $kind, $provider )
    {
        if ( $provider === null )
            unset( self::$providers[(string)$kind] );
        else
            self::$providers[(string)$kind] = $provider;
    }

    /** @return string token, sshkey, app, none, '' when unknown */
    public function kind()
    {
        return $this->kind;
    }

    /** @return string the secret name, env:VARIABLE or installation ('' for none) */
    public function name()
    {
        return $this->name;
    }

    /** @return string the credential as the report shows it: its kind and name, never its value */
    public function describe()
    {
        return $this->kind === 'none' ? 'none' : $this->kind . ':' . $this->name;
    }

    /**
     * Why the credential cannot be used, '' when it can. Reads the secret.
     *
     * @param expStaticTarget|null $target
     * @return string
     */
    public function problem( $target = null )
    {
        if ( $this->kind === '' )
            return 'Credential must be token:<secret name>, sshkey:<secret name>, app:<installation> or none';
        if ( $this->kind === 'none' )
            return '';
        if ( $this->kind === 'app' )
        {
            // the GitHub integration provides it unless a provider was registered (tests) or it is switched off
            if ( !isset( self::$providers['app'] ) && class_exists( 'expStaticGitHub' ) )
                expStaticGitHub::register();
            if ( !isset( self::$providers['app'] ) )
                return 'Credential=app:' . $this->name . ' needs the GitHub integration (a GitHub App), which is not available here'
                     . ( class_exists( 'expStaticGitHub' ) ? ' (' . expStaticGitHub::unavailable() . ')' : '' ) . '; use token: or sshkey:';
            $this->value = call_user_func( self::$providers['app'], $this->name, $target );
            if ( is_string( $this->value ) && $this->value !== '' )
                return '';
            $why = class_exists( 'expStaticGitHub' ) ? expStaticGitHub::lastProblem() : '';
            return 'the GitHub App installation ' . $this->name . ' gave no token' . ( $why !== '' ? ': ' . $why : '' );
        }
        if ( $this->kind === 'sshkey' && strpos( $this->name, 'env:' ) === 0 )
            return 'sshkey: names a secret of the secret store, not an environment variable';
        $value = $this->read( $this->name );
        if ( $value === null || $value === '' )
            return $this->kind === 'token'
                ? "the secret {$this->name} is not set (Setup > Secrets, or env:VARIABLE)"
                : "the deploy key {$this->name} is not in the secret store (./console exp:static:keys <target> --generate)";
        if ( $this->kind === 'sshkey' && strpos( $value, 'PRIVATE KEY-----' ) === false )
            return "the secret {$this->name} holds no private key";
        if ( $this->kind === 'token' && preg_match( '/[\r\n]/', $value ) )
            return "the secret {$this->name} holds more than one line";
        $this->value = $value;
        return '';
    }

    /**
     * The token of a token: or app: credential for GitHub's API (expStaticGitHub sends it in the Authorization
     * header only). Call problem() first.
     *
     * @return string '' for a deploy key, none, or a credential problem() refused
     */
    public function apiToken()
    {
        return ( $this->kind === 'token' || $this->kind === 'app' ) && is_string( $this->value ) ? $this->value : '';
    }

    /** @return string|null a secret's value */
    protected function read( $name )
    {
        if ( strpos( (string)$name, 'env:' ) === 0 )
        {
            $value = getenv( substr( (string)$name, 4 ) );
            return $value === false ? null : (string)$value;
        }
        if ( $this->lookup !== null )
            return call_user_func( $this->lookup, $name );
        return class_exists( 'expSecrets' ) ? expSecrets::get( $name ) : null;
    }

    /**
     * The environment a git call that talks to the remote runs with (fetch, ls-remote, push), and the git options
     * before the command. Call problem() first; cleanup() afterwards removes the key file.
     *
     * @return array env (name => value), options (git -c options)
     */
    public function gitEnvironment()
    {
        $env = array( 'GIT_TERMINAL_PROMPT' => '0' );
        $options = array();
        if ( ( $this->kind === 'token' || $this->kind === 'app' ) && $this->value !== null )
        {
            // no stored credential of the host answers in place of the token
            $options = array( '-c', 'credential.helper=' );
            $env['GIT_ASKPASS'] = $this->askpass;
            $env['SSH_ASKPASS'] = $this->askpass;
            $env[self::ENV_USER] = self::TOKEN_USER;
            $env[self::ENV_TOKEN] = $this->value;
            $env['GIT_SSH_COMMAND'] = 'ssh -o BatchMode=yes';
        }
        else if ( $this->kind === 'sshkey' && $this->value !== null )
        {
            $file = $this->keyFile( $this->value );
            $env['GIT_SSH_COMMAND'] = 'ssh -i ' . self::shellQuote( $file ) . ' -o IdentitiesOnly=yes -o BatchMode=yes'
                                    . ' -o StrictHostKeyChecking=accept-new -o UserKnownHostsFile=' . self::shellQuote( $this->workDir . '/.known_hosts' );
        }
        else
        {
            $ssh = getenv( 'GIT_SSH_COMMAND' );
            $env['GIT_SSH_COMMAND'] = $ssh !== false && $ssh !== '' ? $ssh : 'ssh -o BatchMode=yes';
        }
        return array( 'env' => $env, 'options' => $options );
    }

    /** Writes the private key to a file only this process can read. */
    protected function keyFile( $key )
    {
        $dir = $this->workDir . '/.keys';
        if ( !is_dir( $dir ) )
            @mkdir( $dir, eZDir::dirMode( 0700 ), true );
        @chmod( $dir, eZDir::dirMode( 0700 ) );
        $file = $dir . '/key-' . bin2hex( random_bytes( 8 ) );
        // made empty in a folder only this process can enter, narrowed, then filled: the key is never readable by others
        touch( $file );
        @chmod( $file, eZFile::fileMode( 0600 ) );
        file_put_contents( $file, rtrim( $key ) . "\n" );
        $this->files[] = $file;
        return $file;
    }

    /** Removes the files written for git calls (the private key). */
    public function cleanup()
    {
        foreach ( $this->files as $file )
            if ( is_file( $file ) )
            {
                file_put_contents( $file, str_repeat( "\0", (int)filesize( $file ) ) );
                unlink( $file );
            }
        $this->files = array();
    }

    public function __destruct()
    {
        $this->cleanup();
    }

    /**
     * A text with the token (and its URL-encoded form) replaced by [secret <name>]: for any git output that is shown
     * or stored.
     *
     * @param string $text
     * @return string
     */
    public function redact( $text )
    {
        $text = (string)$text;
        if ( $this->value === null || strlen( $this->value ) < 3 || $this->kind === 'none' )
            return $text;
        $mark = '[secret ' . $this->describe() . ']';
        $text = str_replace( array( $this->value, rawurlencode( $this->value ) ), $mark, $text );
        if ( $this->kind === 'sshkey' )
            $text = preg_replace( '/-----BEGIN [A-Z ]*PRIVATE KEY-----.*?-----END [A-Z ]*PRIVATE KEY-----/s', $mark, $text );
        return $text;
    }

    /**
     * A path quoted for the shell git runs GIT_SSH_COMMAND with (sh, also Git for Windows). Pure.
     *
     * @param string $path
     * @return string
     */
    public static function shellQuote( $path )
    {
        return "'" . str_replace( "'", "'\\''", str_replace( '\\', '/', (string)$path ) ) . "'";
    }

    /**
     * The secret name of a target's generated deploy key. Pure.
     *
     * @param string $targetId
     * @return string
     */
    public static function keySecretName( $targetId )
    {
        return 'staticpublish.' . $targetId . '.deploy_key';
    }

    /**
     * Makes an ed25519 key pair with ssh-keygen in a private folder and returns both halves; the files are removed.
     *
     * @param string $workDir the folder of the targets
     * @param string $comment the public key's comment
     * @param string $keygen the ssh-keygen program
     * @return array private, public, fingerprint; or error
     */
    public static function generateKey( $workDir, $comment, $keygen = 'ssh-keygen' )
    {
        $dir = rtrim( str_replace( '\\', '/', (string)$workDir ), '/' ) . '/.keys';
        if ( !is_dir( $dir ) && !@mkdir( $dir, eZDir::dirMode( 0700 ), true ) && !is_dir( $dir ) )
            return array( 'error' => "the folder $dir cannot be created" );
        @chmod( $dir, eZDir::dirMode( 0700 ) );
        $file = $dir . '/new-' . bin2hex( random_bytes( 8 ) );
        $result = self::process( array( $keygen, '-q', '-t', 'ed25519', '-N', '', '-C', (string)$comment, '-f', $file ) );
        $out = array( 'error' => '' );
        if ( !$result['ok'] || !is_file( $file ) || !is_file( $file . '.pub' ) )
            $out['error'] = 'ssh-keygen could not make a key (is OpenSSH installed?): ' . trim( $result['output'] );
        else
        {
            $out['private'] = (string)file_get_contents( $file );
            $out['public'] = trim( (string)file_get_contents( $file . '.pub' ) );
            $out['fingerprint'] = self::fingerprint( $out['public'] );
        }
        foreach ( array( $file, $file . '.pub' ) as $f )
            if ( is_file( $f ) )
            {
                file_put_contents( $f, str_repeat( "\0", (int)filesize( $f ) ) );
                unlink( $f );
            }
        return $out;
    }

    /**
     * The SHA256 fingerprint of a public key line, as ssh-keygen -l prints it. Pure.
     *
     * @param string $public ssh-ed25519 AAAA... comment
     * @return string SHA256:..., '' when it is not a key line
     */
    public static function fingerprint( $public )
    {
        $parts = preg_split( '/\s+/', trim( (string)$public ) );
        if ( count( $parts ) < 2 || ( $blob = base64_decode( $parts[1], true ) ) === false )
            return '';
        return 'SHA256:' . rtrim( base64_encode( hash( 'sha256', $blob, true ) ), '=' );
    }

    /**
     * Runs a program without a shell. Pure but for the process.
     *
     * @param string[] $command
     * @return array ok, exit, output
     */
    protected static function process( array $command )
    {
        $pipes = array();
        $process = @proc_open( $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
        if ( !is_resource( $process ) )
            return array( 'ok' => false, 'exit' => 127, 'output' => 'cannot start ' . $command[0] );
        fclose( $pipes[0] );
        $output = (string)stream_get_contents( $pipes[1] ) . (string)stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $exit = proc_close( $process );
        return array( 'ok' => $exit === 0, 'exit' => $exit, 'output' => $output );
    }
}
