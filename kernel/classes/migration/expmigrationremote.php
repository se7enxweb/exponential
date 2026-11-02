<?php
/**
 * File containing the expMigrationRemote class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A remote installation migrations are deployed to (exp:migration:deploy): a named target of migration.ini,
 * [RemoteSettings] RemoteList[] and one [Remote_<id>] section each:
 *
 *   Environment=production        the label the reports show (alpha, dev, stage, beta, production, post, arch ...)
 *   Transport=rsync               ssh (scp), sftp, rsync (over ssh), ftp, ftps, local (a directory of this host)
 *   Host=node1.example.com        not for local
 *   Port=22                       default 22 (ssh, sftp, rsync), 21 (ftp, ftps)
 *   User=deploy
 *   Path=/var/www/site            the installation's root on the remote (for ftp: the path the FTP account sees)
 *   Php=php                       the PHP CLI on the remote (default php)
 *   Credential=sshkey:<name>      sshkey:<secret name>, password:<secret name>, either with env:VARIABLE in place of
 *                                 the name, or none (the host's SSH agent and keys)
 *   Groups[]=production           the groups the remote belongs to: --remotes=production selects all of them
 *   HostKeyFingerprint=SHA256:..  optional: the host key the remote must show (else it is learnt on first contact)
 *   AllowRootUser=enabled         the remote's console scripts run as root there (--allow-root-user); default disabled
 *   Trigger=https://.../migration/trigger   ftp and ftps only: the signed HTTP trigger of the remote
 *                                 (expMigrationTrigger), through which the migrations are run there after the transfer
 *   TriggerSecret=<name>          the secret the trigger's requests are signed with: a name of the secret store or
 *                                 env:VARIABLE (the remote's [TriggerSettings] Secret holds the same value)
 *
 * A secret is never in the INI: a section with a Password, Key, Token or Secret setting is refused (validate()).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRemote
{
    /** The transports */
    const TRANSPORTS = array( 'ssh', 'scp', 'sftp', 'rsync', 'ftp', 'ftps', 'local' );

    /** Settings that would hold a secret and are therefore refused */
    const SECRET_SETTINGS = array( 'Password', 'Passphrase', 'Key', 'PrivateKey', 'Token', 'Secret' );

    /** @var string */
    protected $id;

    /** @var array the section's settings */
    protected $settings;

    /**
     * @param string $id
     * @param array $settings the [Remote_<id>] settings
     */
    public function __construct( $id, array $settings = array() )
    {
        $this->id = (string)$id;
        $this->settings = $settings;
    }

    /**
     * The remotes of migration.ini, in the order of RemoteList[].
     *
     * @param eZINI|null $ini
     * @return expMigrationRemote[] id => remote
     */
    public static function all( $ini = null )
    {
        $ini = $ini ?: eZINI::instance( 'migration.ini' );
        $remotes = array();
        $ids = $ini->hasVariable( 'RemoteSettings', 'RemoteList' ) ? (array)$ini->variable( 'RemoteSettings', 'RemoteList' ) : array();
        foreach ( $ids as $id )
        {
            $id = trim( (string)$id );
            if ( $id === '' || isset( $remotes[$id] ) )
                continue;
            $section = 'Remote_' . $id;
            $remotes[$id] = new self( $id, $ini->hasGroup( $section ) ? (array)$ini->group( $section ) : array() );
        }
        return $remotes;
    }

    /**
     * [RemoteSettings] with the defaults of a missing setting.
     *
     * @param eZINI|null $ini
     * @return array heal (fixes), stale_lock_age, disk_margin (bytes), php_check, extension_check, parallel, timeout, retries, store
     *               (bool: keep the reports), stage_order (environments)
     */
    public static function settings( $ini = null )
    {
        $ini = $ini ?: eZINI::instance( 'migration.ini' );
        $get = function ( $name, $default ) use ( $ini ) {
            return $ini->hasVariable( 'RemoteSettings', $name ) && $ini->variable( 'RemoteSettings', $name ) !== '' ? $ini->variable( 'RemoteSettings', $name ) : $default;
        };
        $order = array();
        foreach ( (array)$get( 'StageOrder', array() ) as $environment )
            if ( ( $environment = trim( (string)$environment ) ) !== '' )
                $order[] = $environment;
        $choice = function ( $name, array $allowed, $default ) use ( $get ) {
            $value = strtolower( trim( (string)$get( $name, $default ) ) );
            return in_array( $value, $allowed, true ) ? $value : $default;
        };
        $margin = trim( (string)$get( 'DiskMargin', '50M' ) );
        $units = array( '' => 1, 'k' => 1024, 'm' => 1048576, 'g' => 1073741824 );
        $margin = preg_match( '/^([0-9]+)\s*([kmg]?)b?$/i', $margin, $m ) ? (int)$m[1] * $units[strtolower( $m[2] )] : expMigrationRemoteDeploy::DISK_MARGIN;
        return array(
            // the fixes a deploy may make on a remote (exp:migration:heal): stale-lock, paths, modes, caches
            'heal' => expMigrationHeal::normalize( (array)$get( 'SelfHeal', array() ) ),
            'stale_lock_age' => max( 60, (int)$get( 'StaleLockAge', expMigrationHeal::STALE_LOCK_AGE ) ),
            'disk_margin' => $margin,
            'php_check' => $choice( 'PhpCheck', array( 'major', 'minor', 'warn', 'off' ), 'major' ),
            'extension_check' => $choice( 'ExtensionCheck', array( 'migrations', 'all', 'warn', 'off' ), 'migrations' ),
            'parallel' => max( 1, (int)$get( 'Parallel', 4 ) ),
            'timeout' => max( 10, (int)$get( 'Timeout', 900 ) ),
            'retries' => max( 0, min( 10, (int)$get( 'Retries', 2 ) ) ),
            'store' => $get( 'StoreReports', 'enabled' ) === 'enabled',
            'stage_order' => $order ?: expMigrationRemoteDeploy::STAGE_ORDER,
        );
    }

    /**
     * The remotes a selection names: remote ids, group names or "all", comma-separated or a list. Pure.
     *
     * @param string|string[] $names
     * @param expMigrationRemote[] $remotes id => remote
     * @param string[] $errors the names that are neither a remote nor a group
     * @return expMigrationRemote[] id => remote, in the order of $remotes
     */
    public static function select( $names, array $remotes, array &$errors = array() )
    {
        $wanted = array();
        foreach ( (array)$names as $name )
            foreach ( explode( ',', (string)$name ) as $part )
                if ( ( $part = trim( $part ) ) !== '' )
                    $wanted[$part] = true;
        $picked = array();
        foreach ( array_keys( $wanted ) as $name )
        {
            $found = false;
            foreach ( $remotes as $id => $remote )
                if ( $name === 'all' || $name === (string)$id || in_array( $name, $remote->groups(), true ) )
                {
                    $picked[$id] = true;
                    $found = true;
                }
            if ( !$found )
                $errors[] = "$name is neither a remote nor a group of [RemoteSettings] RemoteList[]";
        }
        $selected = array();
        foreach ( $remotes as $id => $remote )
            if ( isset( $picked[$id] ) )
                $selected[$id] = $remote;
        return $selected;
    }

    /** @return string */
    public function id()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function setting( $name, $default = '' )
    {
        return isset( $this->settings[$name] ) && $this->settings[$name] !== '' ? $this->settings[$name] : $default;
    }

    /** @return string */
    public function transport()
    {
        $transport = strtolower( trim( (string)$this->setting( 'Transport', 'ssh' ) ) );
        return $transport === 'scp' ? 'ssh' : $transport;
    }

    /** @return string */
    public function environment()
    {
        return trim( (string)$this->setting( 'Environment', $this->id ) );
    }

    /** @return string */
    public function host()
    {
        return trim( (string)$this->setting( 'Host' ) );
    }

    /** @return int */
    public function port()
    {
        $default = in_array( $this->transport(), array( 'ftp', 'ftps' ), true ) ? 21 : 22;
        return (int)$this->setting( 'Port', $default );
    }

    /** @return string */
    public function user()
    {
        return trim( (string)$this->setting( 'User' ) );
    }

    /** @return string the installation's root on the remote, without a trailing slash */
    public function path()
    {
        $path = rtrim( str_replace( '\\', '/', trim( (string)$this->setting( 'Path' ) ) ), '/' );
        return $path === '' ? '/' : $path;
    }

    /** @return string */
    public function php()
    {
        return trim( (string)$this->setting( 'Php', 'php' ) );
    }

    /** @return string */
    public function credential()
    {
        return trim( (string)$this->setting( 'Credential', 'none' ) );
    }

    /** @return string[] */
    public function groups()
    {
        $groups = array();
        foreach ( (array)$this->setting( 'Groups', array() ) as $group )
            if ( ( $group = trim( (string)$group ) ) !== '' )
                $groups[] = $group;
        return $groups;
    }

    /** @return string the fingerprint the host key must have, '' when it is learnt on first contact */
    public function hostKeyFingerprint()
    {
        return trim( (string)$this->setting( 'HostKeyFingerprint' ) );
    }

    /** @return bool whether the transport can run commands on the remote (all but ftp and ftps without a trigger) */
    public function canRun()
    {
        return !$this->isFtp() || $this->trigger() !== '';
    }

    /** @return bool whether files go over FTP (ftp, ftps) */
    public function isFtp()
    {
        return in_array( $this->transport(), array( 'ftp', 'ftps' ), true );
    }

    /** @return string the address of the remote's HTTP trigger (FTP remotes), '' for none */
    public function trigger()
    {
        return $this->isFtp() ? trim( (string)$this->setting( 'Trigger' ) ) : '';
    }

    /** @return string the name of the secret the trigger's requests are signed with */
    public function triggerSecret()
    {
        return trim( (string)$this->setting( 'TriggerSecret' ) );
    }

    /** @return string where the remote is, as the reports show it: transport://user@host:port/path */
    public function address()
    {
        if ( $this->transport() === 'local' )
            return 'local://' . $this->path();
        $user = $this->user();
        return $this->transport() . '://' . ( $user !== '' ? $user . '@' : '' ) . $this->host() . ':' . $this->port() . $this->path();
    }

    /**
     * Why the remote cannot be used, one problem a line; empty when it can. Pure (the credential is not read).
     *
     * @return string[]
     */
    public function validate()
    {
        $problems = array();
        $label = 'Remote_' . $this->id;
        if ( !preg_match( '/^[A-Za-z0-9][A-Za-z0-9_-]{0,63}$/', $this->id ) )
            $problems[] = "the remote id {$this->id} must be letters, digits, - and _";
        if ( $this->id === 'all' )
            $problems[] = 'the remote id all is reserved (--remotes=all selects every remote)';
        foreach ( self::SECRET_SETTINGS as $name )
            if ( isset( $this->settings[$name] ) )
                $problems[] = "$label has a $name setting: a secret never goes in an INI file; name it with Credential=password:<secret name> or sshkey:<secret name>";
        if ( !in_array( strtolower( trim( (string)$this->setting( 'Transport', 'ssh' ) ) ), self::TRANSPORTS, true ) )
            $problems[] = "$label: Transport must be one of " . implode( ', ', self::TRANSPORTS );
        $transport = $this->transport();
        if ( $transport !== 'local' )
        {
            if ( !preg_match( '/^[A-Za-z0-9.:_\[\]-]{1,253}$/', $this->host() ) || $this->host()[0] === '-' )
                $problems[] = "$label: Host is missing or not a host name or address";
            if ( $this->user() !== '' && !preg_match( '/^[A-Za-z0-9._][A-Za-z0-9._-]{0,63}$/', $this->user() ) )
                $problems[] = "$label: User is not a valid user name";
            if ( $this->port() < 1 || $this->port() > 65535 )
                $problems[] = "$label: Port must be 1 to 65535";
        }
        $path = $this->path();
        if ( $path === '/' || $path[0] !== '/' || strpos( "$path/", '/../' ) !== false || !preg_match( '#^[A-Za-z0-9._/@+-]+$#', $path ) )
            $problems[] = "$label: Path must be the installation's absolute root on the remote (letters, digits and . _ / @ + -)";
        if ( $this->php() === '' || preg_match( '/[\x00-\x1f\'"`$;&|<>]/', $this->php() ) )
            $problems[] = "$label: Php must be the PHP CLI (a command or a path) without shell characters";
        $credential = expMigrationRemoteCredential::parse( $this->credential() );
        if ( $credential[0] === '' )
            $problems[] = "$label: Credential must be sshkey:<secret name>, password:<secret name> (or env:VARIABLE in place of the name) or none";
        else if ( in_array( $transport, array( 'ftp', 'ftps' ), true ) && $credential[0] !== 'password' )
            $problems[] = "$label: an FTP remote signs in with Credential=password:<secret name>";
        else if ( $transport === 'local' && $credential[0] !== 'none' )
            $problems[] = "$label: a local remote takes no credential (Credential=none)";
        $trigger = trim( (string)$this->setting( 'Trigger' ) );
        if ( $trigger !== '' )
        {
            if ( !$this->isFtp() )
                $problems[] = "$label: Trigger is only for an FTP remote (ftp, ftps); the other transports run the migrations over ssh";
            else if ( !expMigrationTrigger::validUrl( $trigger ) )
                $problems[] = "$label: Trigger must be the https:// address of migration/trigger on the remote (http:// only to this host)";
            if ( !preg_match( '/^(env:)?[A-Za-z0-9][A-Za-z0-9_.-]{0,127}$/', $this->triggerSecret() ) )
                $problems[] = "$label: TriggerSecret must name the secret the trigger is signed with (a name of the secret store, or env:VARIABLE), never the secret itself";
        }
        $fingerprint = $this->hostKeyFingerprint();
        if ( $fingerprint !== '' && !preg_match( '/^SHA256:[A-Za-z0-9+\/]{43}=?$/', $fingerprint ) )
            $problems[] = "$label: HostKeyFingerprint must be SHA256:<base64> as ssh-keygen -l prints it";
        return $problems;
    }

    /** @return array the remote as the reports show it (never a secret: the credential by kind and name) */
    public function toArray()
    {
        return array(
            'id' => $this->id,
            'environment' => $this->environment(),
            'transport' => $this->transport(),
            'address' => $this->address(),
            'credential' => $this->credential(),
            'groups' => $this->groups(),
            'can_run' => $this->canRun(),
            'trigger' => $this->trigger(),
        );
    }
}
