<?php
/**
 * File containing the expMigrationRemoteCredential class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * How a deploy signs in to a remote (expMigrationRemote, Credential setting):
 *
 *   sshkey:<secret name>      a private SSH key in the secret store (Setup > Secrets)
 *   sshkey:env:<VARIABLE>     ... or in an environment variable of the process (CI)
 *   password:<secret name>    a password (SSH or FTP), from the secret store
 *   password:env:<VARIABLE>   ... or from an environment variable
 *   none                      the host's own SSH agent and keys
 *
 * A secret never reaches a command line, an address or a log, the same way static publishing keeps its credentials
 * (expStaticCredentials): the key goes into a file only this process can read for the time of the deploy (removed by
 * cleanup()); an SSH password is answered by the askpass helper bin/shell/expstaticaskpass.sh from the environment of
 * the ssh process (SSH_ASKPASS_REQUIRE=force); an FTP password goes into a netrc file only this process can read.
 * redact() replaces the value in any text before it is shown or stored, as a second line of defence.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRemoteCredential
{
    /** @var string sshkey, password, none; '' when the setting is not one of them */
    protected $kind;

    /** @var string the secret name or env:VARIABLE */
    protected $name;

    /** @var string|null the value once read */
    protected $value = null;

    /** @var string the folder of the key and netrc files (only this process can enter it) */
    protected $workDir;

    /** @var string */
    protected $askpass;

    /** @var callable|null name => value instead of expSecrets::get() (tests) */
    protected $lookup;

    /** @var string[] files written for the deploy, removed by cleanup() */
    protected $files = array();

    /**
     * @param string $credential the remote's Credential setting
     * @param array $options work_dir (for the key and netrc files), root (the installation, for the askpass helper),
     *        lookup (callable name => value|null, default expSecrets::get())
     */
    public function __construct( $credential, array $options = array() )
    {
        list( $this->kind, $this->name ) = self::parse( $credential );
        $root = isset( $options['root'] ) ? rtrim( (string)$options['root'], '/' ) : rtrim( (string)getcwd(), '/' );
        $this->workDir = isset( $options['work_dir'] ) ? rtrim( (string)$options['work_dir'], '/' ) : $root . '/var/migration-deploy';
        $this->askpass = $root . '/bin/shell/expstaticaskpass.sh';
        $this->lookup = isset( $options['lookup'] ) && is_callable( $options['lookup'] ) ? $options['lookup'] : null;
    }

    /**
     * A Credential setting as kind and name. Pure.
     *
     * @param string $credential
     * @return array( kind, name ): kind '' when it is not sshkey, password or none
     */
    public static function parse( $credential )
    {
        $credential = trim( (string)$credential );
        if ( $credential === '' || $credential === 'none' )
            return array( 'none', '' );
        $colon = strpos( $credential, ':' );
        $kind = $colon === false ? $credential : substr( $credential, 0, $colon );
        $name = $colon === false ? '' : trim( substr( $credential, $colon + 1 ) );
        if ( !in_array( $kind, array( 'sshkey', 'password' ), true ) || $name === '' || $name === 'env:' )
            return array( '', $name );
        return array( $kind, $name );
    }

    /** @return string */
    public function kind()
    {
        return $this->kind;
    }

    /** @return string the credential as reports show it: kind and name, never the value */
    public function describe()
    {
        return $this->kind === 'none' ? 'none' : $this->kind . ':' . $this->name;
    }

    /**
     * Why the credential cannot be used, '' when it can. Reads the secret.
     *
     * @return string
     */
    public function problem()
    {
        if ( $this->kind === '' )
            return 'Credential must be sshkey:<secret name>, password:<secret name> (or env:VARIABLE in place of the name) or none';
        if ( $this->kind === 'none' )
            return '';
        $value = $this->read( $this->name );
        if ( $value === null || $value === '' )
            return "the secret {$this->name} is not set (Setup > Secrets, or env:VARIABLE)";
        if ( $this->kind === 'sshkey' && strpos( $value, 'PRIVATE KEY-----' ) === false )
            return "the secret {$this->name} holds no private key";
        if ( $this->kind === 'password' && preg_match( '/[\r\n]/', $value ) )
            return "the secret {$this->name} holds more than one line";
        $this->value = $value;
        return '';
    }

    /** @return string|null the value read by problem() (the HTTP trigger signs with it), null before */
    public function secret()
    {
        return $this->value;
    }

    /** @return string|null */
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
     * The ssh options and the environment of an ssh, scp, sftp or rsync call. Call problem() first.
     *
     * @param string $knownHosts the known_hosts file of the remote
     * @param bool $strict true: the host key must already be in $knownHosts (a fingerprint was checked); false: a
     *        host key seen for the first time is learnt (accept-new), a changed one is refused
     * @return array options (string[] -o ... -i ...), env (name => value)
     */
    public function ssh( $knownHosts, $strict = false )
    {
        $options = array( '-o', 'StrictHostKeyChecking=' . ( $strict ? 'yes' : 'accept-new' ), '-o', 'UserKnownHostsFile=' . $knownHosts,
                          '-o', 'ConnectTimeout=20', '-o', 'ServerAliveInterval=15', '-o', 'ServerAliveCountMax=4' );
        $env = array();
        if ( $this->kind === 'sshkey' && $this->value !== null )
        {
            $options = array_merge( $options, array( '-i', $this->file( 'key', rtrim( $this->value ) . "\n" ), '-o', 'IdentitiesOnly=yes',
                                                     '-o', 'BatchMode=yes', '-o', 'PasswordAuthentication=no' ) );
        }
        else if ( $this->kind === 'password' && $this->value !== null )
        {
            // ssh asks the helper, never a terminal; the password is only in the environment of the ssh process
            $options = array_merge( $options, array( '-o', 'PreferredAuthentications=password,keyboard-interactive', '-o', 'PubkeyAuthentication=no',
                                                     '-o', 'NumberOfPasswordPrompts=1' ) );
            $env = array( 'SSH_ASKPASS' => $this->askpass, 'SSH_ASKPASS_REQUIRE' => 'force', 'DISPLAY' => 'none',
                          'EXP_STATIC_GIT_USER' => '', 'EXP_STATIC_GIT_TOKEN' => $this->value );
        }
        else
            $options = array_merge( $options, array( '-o', 'BatchMode=yes' ) );
        return array( 'options' => $options, 'env' => $env );
    }

    /**
     * A netrc file with the FTP user and password, for curl --netrc-file. Call problem() first.
     *
     * @param string $host
     * @param string $user
     * @return string the file
     */
    public function netrc( $host, $user )
    {
        $password = $this->kind === 'password' && $this->value !== null ? $this->value : '';
        return $this->file( 'netrc', 'machine ' . $host . ' login ' . ( $user !== '' ? $user : 'anonymous' ) . ' password ' . $password . "\n" );
    }

    /** Writes a file only this process can read, removed by cleanup(). */
    protected function file( $prefix, $content )
    {
        $dir = $this->workDir . '/.keys';
        if ( !is_dir( $dir ) )
            @mkdir( $dir, eZDir::dirMode( 0700 ), true );
        @chmod( $dir, eZDir::dirMode( 0700 ) );
        $file = $dir . '/' . $prefix . '-' . bin2hex( random_bytes( 8 ) );
        touch( $file );
        @chmod( $file, eZFile::fileMode( 0600 ) );
        file_put_contents( $file, $content );
        $this->files[] = $file;
        return $file;
    }

    /** Removes the files written for the deploy (key, netrc), overwritten first. */
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
     * A text with the secret (and its URL-encoded form, a private key block) replaced by [secret <kind:name>].
     *
     * @param string $text
     * @return string
     */
    public function redact( $text )
    {
        $text = (string)$text;
        if ( $this->value === null || strlen( $this->value ) < 3 )
            return $text;
        $mark = '[secret ' . $this->describe() . ']';
        $text = str_replace( array( $this->value, rawurlencode( $this->value ) ), $mark, $text );
        return (string)preg_replace( '/-----BEGIN [A-Z ]*PRIVATE KEY-----.*?-----END [A-Z ]*PRIVATE KEY-----/s', $mark, $text );
    }
}
