<?php
/**
 * File containing the expViewCountRequestFilter class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Which access log requests are page views: the method, the status, the user agent and the path decide, as
 * logfile.ini [ViewCountSettings] sets them. An empty list switches its check off.
 *
 *   $filter = expViewCountRequestFilter::fromINI( eZINI::instance( 'logfile.ini' ) );
 *   $reason = $filter->classify( $record );       // null: a view; else why not (method, status, bot, asset, excluded)
 */
class expViewCountRequestFilter
{
    /** The defaults, used for a setting logfile.ini does not have. */
    const DEFAULTS = array(
        'methods' => array( 'GET' ),
        'statuses' => array( 200, 304 ),
        'skip_extensions' => array( 'css', 'js', 'mjs', 'map', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'avif', 'svg', 'ico', 'bmp',
                                    'tif', 'tiff', 'woff', 'woff2', 'ttf', 'otf', 'eot', 'mp4', 'webm', 'mp3', 'ogg', 'wav',
                                    'pdf', 'zip', 'gz', 'tgz', 'xml', 'txt', 'json', 'webmanifest' ),
        'skip_path_prefixes' => array( '/var/', '/design/', '/extension/', '/share/', '/lib/', '/.well-known/', '/ezjscore/',
                                       '/api/', '/user/', '/layout/', '/content/search', '/content/download/', '/notification/',
                                       '/content/edit/', '/content/action', '/shop/', '/rss/', '/Q/' ),
        'bot_patterns' => array( 'bot', 'crawl', 'spider', 'slurp', 'scrap', 'warmer', 'preview', 'monitor', 'uptime', 'pingdom',
                                 'curl/', 'wget/', 'python', 'java/', 'go-http-client', 'okhttp', 'libwww', 'httpclient',
                                 'headless', 'lighthouse', 'facebookexternalhit', 'feedfetcher', 'validator', 'scan' ),
        'skip_empty_agent' => true,
    );

    /** @var array */
    private $settings;

    /**
     * @param array $settings methods (string[]), statuses (int[]), skip_extensions (string[], without the dot),
     *                        skip_path_prefixes (string[]), bot_patterns (string[], parts of the user agent, any case),
     *                        skip_empty_agent (bool); a key left out takes its default
     */
    public function __construct( array $settings = array() )
    {
        $settings += self::DEFAULTS;
        $list = function ( $values )
        {
            return array_values( array_filter( array_map( 'trim', array_map( 'strval', (array)$values ) ), 'strlen' ) );
        };
        $this->settings = array(
            'methods' => array_map( 'strtoupper', $list( $settings['methods'] ) ),
            'statuses' => array_map( 'intval', $list( $settings['statuses'] ) ),
            'skip_extensions' => array_map( function ( $e ) { return strtolower( ltrim( $e, '.' ) ); }, $list( $settings['skip_extensions'] ) ),
            'skip_path_prefixes' => $list( $settings['skip_path_prefixes'] ),
            'bot_patterns' => array_map( 'strtolower', $list( $settings['bot_patterns'] ) ),
            'skip_empty_agent' => (bool)$settings['skip_empty_agent'],
        );
    }

    /**
     * The filter of logfile.ini [ViewCountSettings]: Methods[], Statuses[], SkipExtensions[], SkipPathPrefixes[],
     * BotUserAgents[] and SkipEmptyUserAgent. A setting that is not there takes its default.
     *
     * @param eZINI $ini logfile.ini
     * @return expViewCountRequestFilter
     */
    public static function fromINI( eZINI $ini )
    {
        $map = array( 'methods' => 'Methods', 'statuses' => 'Statuses', 'skip_extensions' => 'SkipExtensions',
                      'skip_path_prefixes' => 'SkipPathPrefixes', 'bot_patterns' => 'BotUserAgents' );
        $settings = array();
        foreach ( $map as $key => $name )
        {
            if ( $ini->hasVariable( 'ViewCountSettings', $name ) )
                $settings[$key] = (array)$ini->variable( 'ViewCountSettings', $name );
        }
        if ( $ini->hasVariable( 'ViewCountSettings', 'SkipEmptyUserAgent' ) )
            $settings['skip_empty_agent'] = $ini->variable( 'ViewCountSettings', 'SkipEmptyUserAgent' ) !== 'disabled';
        return new self( $settings );
    }

    /** @return array the settings in use */
    public function settings()
    {
        return $this->settings;
    }

    /**
     * Why a parsed request (expViewCountLogParser::parse()) is not a page view, or null when it is one.
     *
     * @param array $record
     * @return string|null method, status, bot, asset or excluded
     */
    public function classify( array $record )
    {
        if ( $record['method'] === '' || $record['target'] === '' )
            return 'malformed';
        if ( $this->settings['methods'] && !in_array( $record['method'], $this->settings['methods'], true ) )
            return 'method';
        if ( $this->settings['statuses'] && !in_array( (int)$record['status'], $this->settings['statuses'], true ) )
            return 'status';
        if ( $this->isBot( $record['agent'] ) )
            return 'bot';
        $path = expViewCountLogParser::path( $record['target'] );
        if ( $path === '' )
            return 'malformed';
        if ( $this->isAsset( $path ) )
            return 'asset';
        if ( $this->isExcluded( $path ) )
            return 'excluded';
        return null;
    }

    /**
     * Whether a user agent is a robot: empty (with SkipEmptyUserAgent) or containing one of BotUserAgents[].
     *
     * @param string $agent
     * @return bool
     */
    public function isBot( $agent )
    {
        $agent = strtolower( trim( (string)$agent ) );
        if ( $agent === '' )
            return $this->settings['skip_empty_agent'];
        foreach ( $this->settings['bot_patterns'] as $pattern )
        {
            if ( strpos( $agent, $pattern ) !== false )
                return true;
        }
        return false;
    }

    /**
     * Whether the last element of a path ends in one of SkipExtensions[] (a style sheet, an image ...).
     *
     * @param string $path
     * @return bool
     */
    public function isAsset( $path )
    {
        $last = substr( (string)$path, strrpos( (string)$path, '/' ) + 1 );
        $dot = strrpos( $last, '.' );
        if ( $dot === false )
            return false;
        return in_array( strtolower( substr( $last, $dot + 1 ) ), $this->settings['skip_extensions'], true );
    }

    /**
     * Whether a path starts with one of SkipPathPrefixes[] (compared without regard to case). The view counter asks
     * twice: for the path as requested and for the path without the siteaccess part (/bold/user/login).
     *
     * @param string $path
     * @return bool
     */
    public function isExcluded( $path )
    {
        $path = strtolower( (string)$path );
        foreach ( $this->settings['skip_path_prefixes'] as $prefix )
        {
            $prefix = strtolower( $prefix );
            if ( strpos( $path, $prefix ) === 0 || rtrim( $prefix, '/' ) === $path )
                return true;
        }
        return false;
    }
}
