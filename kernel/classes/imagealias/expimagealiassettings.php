<?php
/**
 * File containing the expImageAliasSettings class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The settings of on-demand image aliases (image.ini [AliasSettings], [AliasStaging], [AliasSource_<env>]) and the
 * mode switch.
 *
 * In on-demand mode (AliasGeneration=on-demand, the default) a template that shows an image alias gets the alias's
 * URL, file name and size computed, and the alias is made when the browser asks for it (content/image,
 * index_image.php). In render mode (AliasGeneration=render) the template call converts the image while the page
 * renders, as Exponential always did. The switch works both ways at any time; nothing is migrated.
 *
 * Command line scripts (cronjobs, mailers, exports) always work in render mode: they read the alias files they ask
 * for, and nobody is there to ask for a URL later.
 *
 * @since 6.0.15
 * @package kernel
 */
class expImageAliasSettings
{
    const MODE_ON_DEMAND = 'on-demand';
    const MODE_RENDER = 'render';

    /** @var string|null a mode set in the process (tests, scripts), over the settings */
    protected static $modeOverride = null;

    /** @var int nesting depth of forceRender() calls: the alias is made now, whatever the mode */
    protected static $forceRender = 0;

    /**
     * Whether templates get computed aliases (no conversion while rendering) in this request.
     *
     * @return bool
     */
    public static function isOnDemand()
    {
        if ( self::$forceRender > 0 )
            return false;
        if ( self::$modeOverride !== null )
            return self::$modeOverride === self::MODE_ON_DEMAND;
        if ( PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg' )
            return false;
        return self::configuredMode() === self::MODE_ON_DEMAND;
    }

    /**
     * The mode of the settings (image.ini [AliasSettings] AliasGeneration); anything else than render is on-demand.
     *
     * @return string on-demand|render
     */
    public static function configuredMode()
    {
        $value = strtolower( trim( (string)self::value( 'AliasSettings', 'AliasGeneration', self::MODE_ON_DEMAND ) ) );
        return $value === self::MODE_RENDER ? self::MODE_RENDER : self::MODE_ON_DEMAND;
    }

    /**
     * Sets the mode for this process, over the settings and the command line rule (tests, scripts); null goes back
     * to the settings.
     *
     * @param string|null $mode on-demand|render|null
     */
    public static function setMode( $mode )
    {
        self::$modeOverride = ( $mode === self::MODE_ON_DEMAND || $mode === self::MODE_RENDER ) ? $mode : null;
    }

    /**
     * Runs $callback with render mode forced: the aliases it asks for are made now (content/image, index_image.php).
     *
     * @param callable $callback
     * @return mixed what $callback returns
     */
    public static function withRender( $callback )
    {
        ++self::$forceRender;
        try
        {
            return call_user_func( $callback );
        }
        finally
        {
            --self::$forceRender;
        }
    }

    /**
     * @return string token|ids-token|ids|node|storage
     */
    public static function urlForm()
    {
        $form = strtolower( trim( (string)self::value( 'AliasSettings', 'OnDemandURL', 'token' ) ) );
        return in_array( $form, array( 'token', 'ids-token', 'ids', 'node', 'storage' ), true ) ? $form : 'token';
    }

    /**
     * @return string auto|redirect|stream
     */
    public static function delivery()
    {
        $value = strtolower( trim( (string)self::value( 'AliasSettings', 'Delivery', 'auto' ) ) );
        return in_array( $value, array( 'auto', 'redirect', 'stream' ), true ) ? $value : 'auto';
    }

    /**
     * Seconds a request may spend making an alias (MaxWait), at least 0.
     *
     * @return float
     */
    public static function maxWait()
    {
        return self::number( 'AliasSettings', 'MaxWait', 2, 0, 300 );
    }

    /**
     * Seconds to wait for another process's lock of one alias file ([ImageConverterSettings] LockTimeout).
     *
     * @return float
     */
    public static function lockTimeout()
    {
        return self::number( 'ImageConverterSettings', 'LockTimeout', 60, 0, 3600 );
    }

    /**
     * @return int seconds a signed URL of a draft image stays valid
     */
    public static function draftTokenLifetime()
    {
        return (int)self::number( 'AliasSettings', 'DraftTokenLifetime', 3600, 60, 31536000 );
    }

    /**
     * @return int seconds the previous signing key is accepted after a change of the key
     */
    public static function signingKeyGrace()
    {
        return (int)self::number( 'AliasSettings', 'SigningKeyGrace', 604800, 0, 31536000 );
    }

    /**
     * @return int Cache-Control max-age of a real alias
     */
    public static function cacheMaxAge()
    {
        return (int)self::number( 'AliasSettings', 'CacheMaxAge', 31536000, 0, 31536000 );
    }

    /**
     * @return string auto|imagick|imagemagick|gd
     */
    public static function preferredConverter()
    {
        $value = strtolower( trim( (string)self::value( 'AliasSettings', 'PreferredConverter', 'auto' ) ) );
        return in_array( $value, array( 'auto', 'imagick', 'imagemagick', 'gd' ), true ) ? $value : 'auto';
    }

    /**
     * @return int conversions at once per server for bulk work
     */
    public static function maxParallel()
    {
        return (int)self::number( 'AliasSettings', 'MaxParallel', 2, 1, 64 );
    }

    /** @return bool */
    public static function placeholderEnabled()
    {
        return self::enabled( 'AliasSettings', 'Placeholder', true );
    }

    /** @return bool */
    public static function swapScriptEnabled()
    {
        return self::enabled( 'AliasSettings', 'SwapScript', true );
    }

    /** @return bool */
    public static function negotiateEnabled()
    {
        return self::enabled( 'AliasSettings', 'Negotiate', true );
    }

    /** @return bool */
    public static function preGenerateEnabled()
    {
        return self::enabled( 'AliasSettings', 'PreGenerate', true );
    }

    /**
     * The extra formats an alias block lists (Formats[]=webp), lower case.
     *
     * @param string $aliasName
     * @return string[]
     */
    public static function aliasFormats( $aliasName )
    {
        $formats = self::value( (string)$aliasName, 'Formats', array() );
        $out = array();
        foreach ( (array)$formats as $format )
        {
            $format = strtolower( trim( (string)$format ) );
            if ( in_array( $format, array( 'webp', 'avif' ), true ) && !in_array( $format, $out, true ) )
                $out[] = $format;
        }
        return $out;
    }

    /**
     * The formats content/image may answer the alias $aliasName in, for the browsers that accept them: its Formats[]
     * while Negotiate is enabled, else none. Never for the original.
     *
     * @param string $aliasName
     * @return string[]
     */
    public static function negotiatedFormats( $aliasName )
    {
        if ( $aliasName === 'original' || !self::negotiateEnabled() )
            return array();
        return self::aliasFormats( $aliasName );
    }

    /**
     * The quality (1..100) of the variant $format of the alias $aliasName: the alias block's Quality[]=<MIME>;<value>,
     * else [MIMETypeSettings] Quality[]=<MIME>;<value>, else 80 for WebP and 60 for AVIF.
     *
     * @param string $aliasName
     * @param string $format webp|avif
     * @return int
     */
    public static function formatQuality( $aliasName, $format )
    {
        $mime = $format === 'avif' ? 'image/avif' : 'image/webp';
        foreach ( array( array( (string)$aliasName, 'Quality' ), array( 'MIMETypeSettings', 'Quality' ) ) as $setting )
        {
            foreach ( (array)self::value( $setting[0], $setting[1], array() ) as $line )
            {
                $parts = explode( ';', (string)$line );
                if ( count( $parts ) >= 2 && trim( $parts[0] ) === $mime && is_numeric( trim( $parts[1] ) ) )
                    return max( 1, min( 100, (int)trim( $parts[1] ) ) );
            }
        }
        return $format === 'avif' ? 60 : 80;
    }

    /**
     * The staging settings ([AliasStaging]) as one array, read by expImageAliasStager. An unknown Environment is pro
     * (no staging), an unknown SyncFrom is none.
     *
     * @return array environment, sync_from, path_map, timeout, failures_before_pause, pause_seconds, alert_email,
     *               alert_recipients, max_file_size
     */
    public static function staging()
    {
        $environments = expImageAliasStager::ENVIRONMENTS;
        $environment = strtolower( trim( (string)self::value( 'AliasStaging', 'Environment', 'pro' ) ) );
        $syncFrom = strtolower( trim( (string)self::value( 'AliasStaging', 'SyncFrom', '' ) ) );
        return array(
            'environment' => in_array( $environment, $environments, true ) ? $environment : 'pro',
            'sync_from' => in_array( $syncFrom, $environments, true ) ? $syncFrom : '',
            'path_map' => array_values( array_filter( (array)self::value( 'AliasStaging', 'PathMap', array() ), 'strlen' ) ),
            'timeout' => self::number( 'AliasStaging', 'Timeout', 5, 1, 120 ),
            'failures_before_pause' => (int)self::number( 'AliasStaging', 'FailuresBeforePause', 5, 1, 1000 ),
            'pause_seconds' => (int)self::number( 'AliasStaging', 'PauseSeconds', 300, 0, 86400 ),
            'alert_email' => self::enabled( 'AliasStaging', 'AlertEmail', true ),
            'alert_recipients' => array_values( array_filter( (array)self::value( 'AliasStaging', 'AlertRecipients', array() ), 'strlen' ) ),
            'max_file_size' => (int)self::number( 'AliasStaging', 'MaxFileSize', 67108864, 1024, 2147483647 ),
        );
    }

    /**
     * A source block ([AliasSource_<environment>]): base URL and the name of the credential secret.
     *
     * @param string $environment
     * @return array base_url, credential
     */
    public static function source( $environment )
    {
        $block = 'AliasSource_' . preg_replace( '/[^a-z]/', '', strtolower( (string)$environment ) );
        return array( 'base_url' => rtrim( trim( (string)self::value( $block, 'BaseURL', '' ) ), '/' ),
                      'credential' => trim( (string)self::value( $block, 'Credential', '' ) ) );
    }

    // ------------------------------------------------------------------------------------------------ helpers

    /**
     * A value of image.ini, or $default when the block or the setting is missing.
     *
     * @param string $block
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    protected static function value( $block, $name, $default )
    {
        if ( !class_exists( 'eZINI' ) )
            return $default;
        $ini = eZINI::instance( 'image.ini' );
        if ( !$ini->hasVariable( $block, $name ) )
            return $default;
        return $ini->variable( $block, $name );
    }

    /**
     * @param string $block
     * @param string $name
     * @param bool $default
     * @return bool
     */
    protected static function enabled( $block, $name, $default )
    {
        $value = strtolower( trim( (string)self::value( $block, $name, $default ? 'enabled' : 'disabled' ) ) );
        return in_array( $value, array( 'enabled', 'true', '1', 'yes', 'on' ), true );
    }

    /**
     * @param string $block
     * @param string $name
     * @param float $default
     * @param float $min
     * @param float $max
     * @return float
     */
    protected static function number( $block, $name, $default, $min, $max )
    {
        $value = self::value( $block, $name, $default );
        if ( !is_numeric( $value ) )
            $value = $default;
        return (float)max( $min, min( $max, (float)$value ) );
    }
}
