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
 * for, and nobody is there to ask for a URL later. A web server running PHP in the CLI SAPI (Velocity, PHP's built-in
 * server) is not a command line script: its requests follow the settings like PHP-FPM's (isWebProcess()).
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
        if ( !self::isWebProcess() )
            return false;
        return self::configuredMode() === self::MODE_ON_DEMAND;
    }

    /**
     * Whether this process answers a web request. A web server that runs PHP in the CLI SAPI (Velocity's
     * persistent workers, PHP's built-in server) answers browsers as 'cli' too; a request method and a Host header
     * tell it from a command line script (eZExecution::isWebRequest()).
     *
     * @return bool
     */
    public static function isWebProcess()
    {
        if ( PHP_SAPI === 'phpdbg' )
            return false;
        return eZExecution::isWebRequest();
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
     * @return int conversions at once per server, every process counted (expImageAliasThrottle)
     */
    public static function maxParallel()
    {
        return (int)self::number( 'AliasSettings', 'MaxParallel', 2, 1, 64 );
    }

    /**
     * Whether a request that finds every conversion slot taken answers the placeholder at once and leaves the alias
     * to the backlog, made right after the answer ([AliasSettings] Backlog, expImageAliasBacklog).
     *
     * @return bool
     */
    public static function backlogEnabled()
    {
        return self::enabled( 'AliasSettings', 'Backlog', true );
    }

    /**
     * @return float seconds a request may go on making the backlog after its answer ([AliasSettings] BacklogTime,
     *               1 to 600, default 30)
     */
    public static function backlogTime()
    {
        return self::number( 'AliasSettings', 'BacklogTime', 30, 1, 600 );
    }

    /**
     * Who makes the backlog after a request's answer ([AliasSettings] BacklogDrainer): process (default) starts a
     * drainer process of its own (bin/php/expimagealiasdrain.php, at most MaxParallel per server); request makes it in
     * the request's own process, as before.
     *
     * @return string process|request
     */
    public static function backlogDrainer()
    {
        $value = strtolower( trim( (string)self::value( 'AliasSettings', 'BacklogDrainer', 'process' ) ) );
        return $value === 'request' ? 'request' : 'process';
    }

    /**
     * Whether a page that shows many images not made yet links the rest to a static placeholder of their size
     * ([AliasSettings] PagePlaceholders, expImageAliasPagePlaceholder).
     *
     * @return bool
     */
    public static function pagePlaceholdersEnabled()
    {
        return self::enabled( 'AliasSettings', 'PagePlaceholders', true );
    }

    /**
     * @return int aliases not made yet a page links through content/image before the rest get the static placeholder
     *             ([AliasSettings] PagePlaceholderAfter, 0 to 10000, default 24)
     */
    public static function pagePlaceholderAfter()
    {
        return (int)self::number( 'AliasSettings', 'PagePlaceholderAfter', 24, 0, 10000 );
    }

    /**
     * @return int seconds a page that links such placeholders may be kept in the view cache ([AliasSettings]
     *             PagePlaceholderTime, 10 to 3600, default 60)
     */
    public static function pagePlaceholderTime()
    {
        return (int)self::number( 'AliasSettings', 'PagePlaceholderTime', 60, 10, 3600 );
    }

    /**
     * @return int the size (bytes) var/log/imagealias.log may reach before it is rotated: its own, not the kernel's
     *             LogFileMaxSize for all logs, since a bulk run writes a line per alias (1 MB to 1 GB, default 20 MB)
     */
    public static function logFileMaxSize()
    {
        return (int)self::number( 'AliasSettings', 'LogFileMaxSize', 20971520, 1048576, 1073741824 );
    }

    /**
     * @return int the nice value bulk runs lower their worker to (0 = unchanged)
     */
    public static function bulkNice()
    {
        return (int)self::number( 'AliasSettings', 'BulkNice', 10, 0, 19 );
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
     * The aliases pre-generation is limited to ([AliasSettings] PreGenerateAliasList[]): the names given, trimmed,
     * each once, in the order given. A name no related siteaccess defines (or 'original', the upload itself) stays in
     * the list, so preGeneratePlan() reports it. Empty (the default): every alias of the related siteaccesses.
     *
     * @return string[]
     */
    public static function preGenerateAliasList()
    {
        $out = array();
        foreach ( (array)self::value( 'AliasSettings', 'PreGenerateAliasList', array() ) as $name )
        {
            $name = trim( (string)$name );
            if ( $name === '' || in_array( $name, $out, true ) )
                continue;
            $out[] = $name;
        }
        return $out;
    }

    /**
     * Whether pre-generation also makes the WebP/AVIF variants the alias blocks list in Formats[]
     * ([AliasSettings] PreGenerateFormats, default enabled).
     *
     * @return bool
     */
    public static function preGenerateFormatsEnabled()
    {
        return self::enabled( 'AliasSettings', 'PreGenerateFormats', true );
    }

    /**
     * The aliases pre-generation makes: $plan (siteAccessAliasPlan(), siteaccess => names) narrowed to $list
     * (preGenerateAliasList()); each alias stays with the siteaccess whose image.ini makes it. An empty $list keeps
     * the whole plan. Names in $list that no siteaccess of the plan defines are returned as unknown.
     *
     * @param array $plan siteaccess => alias names
     * @param string[] $list
     * @return array plan (siteaccess => names, siteaccesses left without an alias dropped), unknown (names)
     */
    public static function preGeneratePlan( array $plan, array $list )
    {
        if ( !$list )
            return array( 'plan' => $plan, 'unknown' => array() );
        $out = array();
        $known = array();
        foreach ( $plan as $siteAccess => $names )
        {
            foreach ( (array)$names as $name )
            {
                $known[$name] = true;
                if ( in_array( $name, $list, true ) )
                    $out[$siteAccess][] = $name;
            }
        }
        $unknown = array();
        foreach ( $list as $name )
        {
            if ( !isset( $known[$name] ) && !in_array( $name, $unknown, true ) )
                $unknown[] = $name;
        }
        return array( 'plan' => $out, 'unknown' => $unknown );
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

    // ------------------------------------------------------------------------------------------------ siteaccesses

    /**
     * The siteaccesses whose aliases pre-generation covers: the current one first, then site.ini
     * [SiteAccessSettings] RelatedSiteAccessList[] (the siteaccesses that share this database), each once.
     *
     * @return string[]
     */
    public static function relatedSiteAccesses()
    {
        $out = array();
        $current = self::currentSiteAccess();
        if ( $current !== '' )
            $out[] = $current;
        $site = eZINI::instance( 'site.ini' );
        $related = $site->hasVariable( 'SiteAccessSettings', 'RelatedSiteAccessList' ) ? (array)$site->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' ) : array();
        foreach ( $related as $name )
        {
            $name = trim( (string)$name );
            if ( preg_match( '/^[A-Za-z0-9_-]{1,100}$/', $name ) && !in_array( $name, $out, true ) )
                $out[] = $name;
        }
        return $out;
    }

    /**
     * @return string the name of the current siteaccess, '' when none is loaded
     */
    public static function currentSiteAccess()
    {
        return isset( $GLOBALS['eZCurrentAccess']['name'] ) && is_string( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : '';
    }

    /**
     * The union of the aliases of several siteaccesses, each alias with the siteaccess whose image.ini makes it: the
     * first one in the order given that lists it in AliasList[]. 'original' is never in it (it is the upload).
     *
     * @param array $aliasLists siteaccess => its AliasList[], in order of preference
     * @return array siteaccess => alias names, in order; siteaccesses without an alias of their own are left out
     */
    public static function aliasUnion( array $aliasLists )
    {
        $seen = array();
        $plan = array();
        foreach ( $aliasLists as $siteAccess => $aliases )
        {
            foreach ( (array)$aliases as $alias )
            {
                $alias = trim( (string)$alias );
                if ( $alias === '' || $alias === 'original' || isset( $seen[$alias] ) || !expImageAliasUrl::validAliasName( $alias ) )
                    continue;
                $seen[$alias] = true;
                $plan[(string)$siteAccess][] = $alias;
            }
        }
        return $plan;
    }

    /**
     * The aliases pre-generation makes for a published image: aliasUnion() of the AliasList[] of
     * relatedSiteAccesses(), each read through the INI system of that siteaccess (its overrides and extensions).
     * Kept for the process.
     *
     * @return array siteaccess => alias names
     */
    public static function siteAccessAliasPlan()
    {
        static $plans = array();
        $siteAccesses = self::relatedSiteAccesses();
        $key = implode( ',', $siteAccesses );
        if ( isset( $plans[$key] ) )
            return $plans[$key];
        $lists = array();
        $current = self::currentSiteAccess();
        foreach ( $siteAccesses as $siteAccess )
        {
            $ini = $siteAccess === $current ? eZINI::instance( 'image.ini' ) : eZSiteAccess::getIni( $siteAccess, 'image.ini' );
            $lists[$siteAccess] = $ini->hasVariable( 'AliasSettings', 'AliasList' ) ? (array)$ini->variable( 'AliasSettings', 'AliasList' ) : array();
        }
        if ( !$siteAccesses )
            $lists[''] = (array)self::value( 'AliasSettings', 'AliasList', array() );
        return $plans[$key] = self::aliasUnion( $lists );
    }

    /**
     * Runs $callback with the image.ini of the siteaccess $siteAccess in place of this one's, so eZImageManager makes
     * the aliases that siteaccess defines, exactly as it would (a queue worker making the aliases of a site it does
     * not run as). The current siteaccess or '' runs $callback as it is.
     *
     * @param string $siteAccess
     * @param callable $callback
     * @return mixed what $callback returns
     */
    public static function withSiteAccess( $siteAccess, $callback )
    {
        $siteAccess = (string)$siteAccess;
        if ( $siteAccess === '' || $siteAccess === self::currentSiteAccess() || !preg_match( '/^[A-Za-z0-9_-]{1,100}$/', $siteAccess ) )
            return call_user_func( $callback );
        $own = eZINI::instance( 'image.ini' );
        $other = eZSiteAccess::getIni( $siteAccess, 'image.ini' );
        $saved = $own->BlockValues;
        $own->BlockValues = $other->BlockValues;
        eZImageManager::resetInstance();
        try
        {
            return call_user_func( $callback );
        }
        finally
        {
            $own->BlockValues = $saved;
            eZImageManager::resetInstance();
        }
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
