<?php
/**
 * File containing the expClusterHosts class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The hosts that serve this installation, from settings/cluster.ini ([ClusterSettings] HostList[] and one
 * [Host_<name>] block per host), for the commands that run on them over ssh: exp:host:run, exp:cluster:run,
 * exp:cluster:cache, exp:cluster:deploy, exp:cluster:status, exp:cluster:hosts
 * (doc/eng-us/features/6.0/cluster-host-commands.md).
 *
 * A host is an array: name, ssh (the target, or "local"), port (int, 0: ssh's default), root, php, user, deploy_user,
 * engine (fpm or velocity), deploy_options (list), allow_root_user (bool). No secret is read or kept: ssh takes its
 * keys from the agent or the ssh configuration of the user who runs the command.
 *
 * Pure: fromSettings() takes the blocks as arrays, so it is tested without eZINI; fromINI() reads cluster.ini.
 *
 * @since 6.0.15
 * @package kernel
 */
class expClusterHosts
{
    const LOCAL = 'local';

    /** The engines a host block may name. */
    const ENGINES = array( 'fpm', 'velocity' );

    /** @var array name => host */
    protected $hosts = array();

    /** @var array the settings of [ClusterSettings], with defaults */
    protected $settings = array();

    /** @var string[] what is wrong with the settings, one text each */
    protected $errors = array();

    /**
     * @param array $hosts name => host
     * @param array $settings
     * @param string[] $errors
     */
    protected function __construct( array $hosts, array $settings, array $errors )
    {
        $this->hosts = $hosts;
        $this->settings = $settings;
        $this->errors = $errors;
    }

    /**
     * Reads settings/cluster.ini with its overrides.
     *
     * @return expClusterHosts
     */
    public static function fromINI()
    {
        $ini = eZINI::instance( 'cluster.ini' );
        $blocks = array();
        if ( $ini->hasGroup( 'ClusterSettings' ) )
            $blocks['ClusterSettings'] = $ini->group( 'ClusterSettings' );
        $list = isset( $blocks['ClusterSettings']['HostList'] ) ? (array)$blocks['ClusterSettings']['HostList'] : array();
        foreach ( $list as $name )
        {
            $name = (string)$name;
            if ( $name !== '' && $ini->hasGroup( 'Host_' . $name ) )
                $blocks['Host_' . $name] = $ini->group( 'Host_' . $name );
        }
        return self::fromSettings( $blocks );
    }

    /**
     * @param array $blocks block name => settings, as eZINI::group() gives them: ClusterSettings and Host_<name>
     * @return expClusterHosts
     */
    public static function fromSettings( array $blocks )
    {
        $main = isset( $blocks['ClusterSettings'] ) && is_array( $blocks['ClusterSettings'] ) ? $blocks['ClusterSettings'] : array();
        $settings = array(
            'default_php' => self::text( $main, 'DefaultPhp', 'php' ),
            'connect_timeout' => max( 1, (int)self::text( $main, 'ConnectTimeout', '20' ) ),
            'timeout' => max( 1, (int)self::text( $main, 'Timeout', '1800' ) ),
            'mode' => self::text( $main, 'Mode', 'sequential' ) === 'parallel' ? 'parallel' : 'sequential',
            'allow_root_user' => self::text( $main, 'AllowRootUser', 'enabled' ) !== 'disabled',
            'ssh_options' => self::listOf( $main, 'SshOptions' ),
            'log_file' => self::text( $main, 'LogFile', 'cluster.log' ),
        );
        $errors = array();
        foreach ( $settings['ssh_options'] as $option )
            if ( !preg_match( '/^[A-Za-z][A-Za-z0-9]*=[^\s]+$/', $option ) )
                $errors[] = "[ClusterSettings] SshOptions[]=$option is not Name=value";
        if ( $settings['default_php'] === '' || !self::isSafePath( $settings['default_php'] ) )
            $errors[] = '[ClusterSettings] DefaultPhp must be a program name or path';

        $hosts = array();
        foreach ( self::listOf( $main, 'HostList' ) as $name )
        {
            if ( !preg_match( '/^[A-Za-z0-9][A-Za-z0-9._-]{0,62}$/', $name ) )
            {
                $errors[] = "HostList[]=$name: a host name is letters, digits, dot, dash and underscore";
                continue;
            }
            if ( isset( $hosts[$name] ) )
            {
                $errors[] = "HostList[]=$name is listed twice";
                continue;
            }
            if ( !isset( $blocks['Host_' . $name] ) || !is_array( $blocks['Host_' . $name] ) )
            {
                $errors[] = "HostList[]=$name has no block [Host_$name]";
                continue;
            }
            $hostErrors = array();
            $hosts[$name] = self::buildHost( $name, $blocks['Host_' . $name], $settings, $hostErrors );
            foreach ( $hostErrors as $error )
                $errors[] = $error;
        }
        return new self( $hosts, $settings, $errors );
    }

    /**
     * One host from its block.
     *
     * @param string $name
     * @param array $block
     * @param array $settings
     * @param string[] $errors gets what is wrong
     * @return array
     */
    protected static function buildHost( $name, array $block, array $settings, array &$errors )
    {
        $where = "[Host_$name]";
        $ssh = self::text( $block, 'Ssh', '' );
        if ( $ssh === '' )
            $errors[] = "$where has no Ssh= (an ssh alias, host, user@host or local)";
        else if ( $ssh !== self::LOCAL && !preg_match( '/^(?:[A-Za-z0-9._][A-Za-z0-9._-]*@)?[A-Za-z0-9._:\[\]-]+$/', $ssh ) || $ssh[0] === '-' )
            $errors[] = "$where Ssh=$ssh is not an ssh target (alias, host or user@host)";
        $port = self::text( $block, 'Port', '' );
        if ( $port !== '' && ( !ctype_digit( $port ) || (int)$port < 1 || (int)$port > 65535 ) )
            $errors[] = "$where Port=$port is not a port number";
        $root = rtrim( self::text( $block, 'Root', '' ), '/' );
        if ( $root === '' )
            $errors[] = "$where has no Root= (the installation's directory on that host)";
        else if ( $root[0] !== '/' || !self::isSafePath( $root ) )
            $errors[] = "$where Root=$root must be an absolute path";
        $php = self::text( $block, 'Php', '' );
        if ( $php === '' )
            $php = $settings['default_php'];
        else if ( !self::isSafePath( $php ) )
            $errors[] = "$where Php=$php must be a program name or path";
        $user = self::text( $block, 'User', '' );
        $deployUser = self::text( $block, 'DeployUser', '' );
        foreach ( array( 'User' => $user, 'DeployUser' => $deployUser ) as $key => $value )
            if ( $value !== '' && !preg_match( '/^[a-z_][a-z0-9_.-]{0,31}$/i', $value ) )
                $errors[] = "$where $key=$value is not a user name";
        $engine = strtolower( self::text( $block, 'Engine', 'fpm' ) );
        if ( !in_array( $engine, self::ENGINES, true ) )
            $errors[] = "$where Engine=$engine: fpm or velocity";
        $deployOptions = self::listOf( $block, 'DeployOptions' );
        foreach ( $deployOptions as $option )
            if ( !preg_match( '/^--[a-z][a-z0-9-]*(=[^\s]*)?$/', $option ) )
                $errors[] = "$where DeployOptions[]=$option is not an option (--name or --name=value)";
        $allowRoot = self::text( $block, 'AllowRootUser', '' );
        return array(
            'name' => $name,
            'ssh' => $ssh,
            'port' => $port !== '' && ctype_digit( $port ) ? (int)$port : 0,
            'root' => $root,
            'php' => $php,
            'user' => $user,
            'deploy_user' => $deployUser,
            'engine' => $engine,
            'deploy_options' => $deployOptions,
            'allow_root_user' => $allowRoot === '' ? $settings['allow_root_user'] : $allowRoot !== 'disabled',
        );
    }

    /** @return array name => host, in the order of HostList[] */
    public function hosts()
    {
        return $this->hosts;
    }

    /** @return string[] */
    public function names()
    {
        return array_keys( $this->hosts );
    }

    /**
     * @param string $name
     * @return array|null
     */
    public function host( $name )
    {
        return isset( $this->hosts[$name] ) ? $this->hosts[$name] : null;
    }

    /** @return array the settings of [ClusterSettings] */
    public function settings()
    {
        return $this->settings;
    }

    /** @return string[] what is wrong with cluster.ini */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * The hosts a command runs on: every host, or the ones named (comma list or array), in the order of HostList[].
     *
     * @param string|string[]|null $names null or '' for every host
     * @param string[] $unknown gets the names that are not hosts
     * @return array name => host
     */
    public function select( $names, &$unknown = array() )
    {
        $unknown = array();
        if ( $names === null || $names === '' || $names === array() )
            return $this->hosts;
        if ( !is_array( $names ) )
            $names = explode( ',', (string)$names );
        $wanted = array();
        foreach ( $names as $name )
        {
            $name = trim( (string)$name );
            if ( $name === '' )
                continue;
            if ( !isset( $this->hosts[$name] ) )
                $unknown[] = $name;
            else
                $wanted[$name] = true;
        }
        $selected = array();
        foreach ( $this->hosts as $name => $host )
            if ( isset( $wanted[$name] ) )
                $selected[$name] = $host;
        return $selected;
    }

    /** A path or program name without shell characters. */
    protected static function isSafePath( $path )
    {
        return (bool)preg_match( '#^[A-Za-z0-9._/+@-]+$#', $path ) && strpos( $path, '-' ) !== 0;
    }

    /** A scalar setting as a trimmed string. */
    protected static function text( array $block, $key, $default )
    {
        if ( !isset( $block[$key] ) || is_array( $block[$key] ) )
            return $default;
        $value = trim( (string)$block[$key] );
        return $value === '' ? $default : $value;
    }

    /** A list setting without empty entries. */
    protected static function listOf( array $block, $key )
    {
        if ( !isset( $block[$key] ) )
            return array();
        $list = array();
        foreach ( (array)$block[$key] as $value )
        {
            $value = trim( (string)$value );
            if ( $value !== '' )
                $list[] = $value;
        }
        return $list;
    }
}
