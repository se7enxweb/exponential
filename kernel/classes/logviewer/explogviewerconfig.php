<?php
/**
 * The settings of the log viewer (settings/logviewer.ini, doc/features/6.0/log-viewer.md, "Settings"), read once
 * per request with safe bounds. A test gives its own values with fromArray(), so nothing here needs eZINI.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLogViewerConfig
{
    /** @var array group => variable => value, as read */
    protected $values;

    /** @var string the installation root, absolute, without a trailing slash */
    protected $root;

    /** @var expLogViewerConfig|null this request's instance */
    protected static $instance = null;

    /** @var string|null the request the instance belongs to (REQUEST_TIME_FLOAT) */
    protected static $instanceKey = null;

    /** The defaults, used for any value the file does not give (and by tests) */
    public static $defaults = array(
        'LogViewerSettings' => array(
            'LogViewer' => 'enabled',
            'Directories' => array( 'var/log', '{log_dir}', 'var/*/log' ),
            'FilePatterns' => array( '*.log', '*.log.[0-9]', '*.log.[0-9][0-9]', '*.log.[0-9].gz', '*.log.[0-9][0-9].gz', '*.log.gz' ),
            'ExcludePatterns' => array(),
            'KernelLogs' => array( 'error.log', 'warning.log', 'notice.log', 'debug.log', 'strict.log', 'deprecated.log', 'auth.log',
                                   'cronjob*.log', 'storage.log', 'setup.log', 'mail.log', 'trashpurge.log' ),
            'DefaultFile' => 'var/log/error.log',
            'DefaultKey' => 'error',
            'LogList' => array( 'webserver-access', 'webserver-error' ),
            'LogPath' => array(),
            'LogLabel' => array( 'webserver-access' => 'Web server access log', 'webserver-error' => 'Web server error log' ),
            'LogGroup' => array(),
            'FileLevels' => array( 'error.log*;error', '*error*.log*;error', 'warning.log*;warning', 'strict.log*;warning',
                                   'deprecated.log*;notice', 'notice.log*;notice', 'debug.log*;debug' ),
        ),
        'TailSettings' => array(
            'InitialBytes' => 65536, 'MaxBytesPerPoll' => 262144, 'PollInterval' => 1000, 'MaxPollInterval' => 15000,
            'MaxLinesKept' => 5000, 'ChunkCacheTTL' => 10, 'ChunkCacheMaxBytes' => 262144, 'WrapLines' => 'enabled',
        ),
        'SearchSettings' => array(
            'MaxBytesPerRequest' => 33554432, 'TimeLimit' => 2000, 'MaxResults' => 200, 'MaxPatternLength' => 200,
            'BacktrackLimit' => 100000, 'Rotations' => 'enabled', 'MaxGzipBytes' => 67108864, 'RequestWindow' => 2,
        ),
        'RedactionSettings' => array(
            'MaskEmails' => 'enabled', 'MaskIPs' => 'enabled', 'MaskSecrets' => 'enabled',
            'SecretSettings' => array( 'site.ini;DatabaseSettings;Password', 'site.ini;MailSettings;TransportPassword' ),
            'SecretGroups' => array( 'site.ini;DatabaseSettings', 'site.ini;MailSettings' ),
        ),
        'ActionSettings' => array(
            'Download' => 'enabled', 'Rotate' => 'disabled', 'Truncate' => 'disabled', 'DeleteArchives' => 'disabled',
            'MaxDownloadBytes' => 52428800,
        ),
        'RateLimitSettings' => array( 'Requests' => 120, 'Window' => 60 ),
        'ViewerAuditSettings' => array( 'ReadInterval' => 600 ),
    );

    /** The bounds of the numeric settings: group/variable => array( min, max ) */
    public static $bounds = array(
        'TailSettings/InitialBytes' => array( 1024, 4194304 ),
        'TailSettings/MaxBytesPerPoll' => array( 4096, 4194304 ),
        'TailSettings/PollInterval' => array( 250, 60000 ),
        'TailSettings/MaxPollInterval' => array( 1000, 300000 ),
        'TailSettings/MaxLinesKept' => array( 100, 50000 ),
        'TailSettings/ChunkCacheTTL' => array( 0, 3600 ),
        'TailSettings/ChunkCacheMaxBytes' => array( 0, 4194304 ),
        'SearchSettings/MaxBytesPerRequest' => array( 65536, 1073741824 ),
        'SearchSettings/TimeLimit' => array( 100, 30000 ),
        'SearchSettings/MaxResults' => array( 1, 2000 ),
        'SearchSettings/MaxPatternLength' => array( 1, 1000 ),
        'SearchSettings/BacktrackLimit' => array( 1000, 10000000 ),
        'SearchSettings/MaxGzipBytes' => array( 65536, 1073741824 ),
        'SearchSettings/RequestWindow' => array( 0, 60 ),
        'ActionSettings/MaxDownloadBytes' => array( 1024, 1073741824 ),
        'RateLimitSettings/Requests' => array( 1, 100000 ),
        'RateLimitSettings/Window' => array( 1, 3600 ),
        'ViewerAuditSettings/ReadInterval' => array( 0, 86400 ),
    );

    /**
     * @param array $values group => variable => value (missing ones take the defaults)
     * @param string $root the installation root
     */
    public function __construct( array $values, $root )
    {
        $merged = self::$defaults;
        foreach ( $values as $group => $vars )
            if ( is_array( $vars ) )
                foreach ( $vars as $name => $value )
                    $merged[$group][$name] = $value;
        $this->values = $merged;
        $root = rtrim( str_replace( '\\', '/', (string)$root ), '/' );
        $real = $root !== '' ? realpath( $root ) : false;
        $this->root = $real !== false ? rtrim( str_replace( '\\', '/', $real ), '/' ) : $root;
    }

    /**
     * A configuration from given values (tests, the command line with options).
     *
     * @param array $values
     * @param string $root
     * @return expLogViewerConfig
     */
    public static function fromArray( array $values, $root )
    {
        return new self( $values, $root );
    }

    /**
     * This request's configuration, from logviewer.ini.
     *
     * @return expLogViewerConfig
     */
    public static function instance()
    {
        $key = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : '';
        if ( self::$instance !== null && self::$instanceKey === $key )
            return self::$instance;
        $values = array();
        if ( class_exists( 'eZINI' ) && eZINI::exists( 'logviewer.ini' ) )
        {
            $ini = eZINI::instance( 'logviewer.ini' );
            foreach ( self::$defaults as $group => $vars )
                foreach ( array_keys( $vars ) as $name )
                    if ( $ini->hasVariable( $group, $name ) )
                        $values[$group][$name] = $ini->variable( $group, $name );
        }
        $root = class_exists( 'eZSys' ) ? eZSys::rootDir() : getcwd();
        if ( $root === '' || $root === null )
            $root = getcwd();
        self::$instance = new self( $values, $root );
        self::$instanceKey = $key;
        return self::$instance;
    }

    /** Forgets this request's instance (tests, persistent workers) */
    public static function reset()
    {
        self::$instance = null;
        self::$instanceKey = null;
    }

    /** @return string */
    public function root()
    {
        return $this->root;
    }

    /**
     * @param string $group
     * @param string $name
     * @return mixed the raw value
     */
    public function get( $group, $name )
    {
        return isset( $this->values[$group][$name] ) ? $this->values[$group][$name] : null;
    }

    /**
     * A numeric setting, inside its bounds.
     *
     * @param string $group
     * @param string $name
     * @return int
     */
    public function int( $group, $name )
    {
        $value = $this->get( $group, $name );
        $default = isset( self::$defaults[$group][$name] ) ? (int)self::$defaults[$group][$name] : 0;
        $value = is_numeric( $value ) ? (int)$value : $default;
        $key = $group . '/' . $name;
        if ( isset( self::$bounds[$key] ) )
            $value = max( self::$bounds[$key][0], min( self::$bounds[$key][1], $value ) );
        return $value;
    }

    /**
     * A switch: enabled, true, 1 and yes are on.
     *
     * @param string $group
     * @param string $name
     * @return bool
     */
    public function enabled( $group, $name )
    {
        $value = $this->get( $group, $name );
        if ( is_bool( $value ) )
            return $value;
        return in_array( strtolower( trim( (string)$value ) ), array( 'enabled', 'true', '1', 'yes', 'on' ), true );
    }

    /**
     * A list setting: the non-empty strings.
     *
     * @param string $group
     * @param string $name
     * @return string[]
     */
    public function lst( $group, $name )
    {
        $value = $this->get( $group, $name );
        if ( !is_array( $value ) )
            $value = $value === null || $value === '' ? array() : array( $value );
        $out = array();
        foreach ( $value as $item )
            if ( is_scalar( $item ) && trim( (string)$item ) !== '' )
                $out[] = trim( (string)$item );
        return $out;
    }

    /**
     * A setting with keys (LogPath[<key>]=...): key => value, the non-empty scalar ones.
     *
     * @param string $group
     * @param string $name
     * @return array
     */
    public function hash( $group, $name )
    {
        $value = $this->get( $group, $name );
        $out = array();
        if ( is_array( $value ) )
            foreach ( $value as $k => $v )
                if ( is_string( $k ) && is_scalar( $v ) && trim( (string)$v ) !== '' )
                    $out[$k] = trim( (string)$v );
        return $out;
    }

    /**
     * The values a browser needs (the page's data attributes): poll intervals and the lines it keeps.
     *
     * @return array
     */
    public function clientSettings()
    {
        $min = $this->int( 'TailSettings', 'PollInterval' );
        return array( 'poll_interval' => $min,
                      'max_poll_interval' => max( $min, $this->int( 'TailSettings', 'MaxPollInterval' ) ),
                      'max_lines' => $this->int( 'TailSettings', 'MaxLinesKept' ) );
    }
}
