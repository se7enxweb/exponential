<?php
/**
 * File containing the expDebugBarReport class
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit fbbd333a85), Copyright (C) Mugo Web, GPL v2
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The HTML debug report as the "Exp Debug" bar: a bar pinned to the bottom of the window with a live
 * summary of the request, and a tabbed panel above it (Messages, Settings, Cache, Timing, SQL, Templates,
 * Included files, Memory, Server (id velocity), and Other for reports nobody claimed).
 *
 * Every section the classic report printed is still printed, with the same ids and the same rows, only
 * placed in a tab: scripts and stylesheets that look for #main-debug-table, #timingpoints,
 * #timeaccumulators, #debug_includes, #templateusage or #ezjscpackerusage still find them. The top
 * report "Debug toolbar" (design:setup/debug_toolbar.tpl, and any override of it) is shown in the
 * Settings tab under "Classic controls".
 *
 * The settings and cache controls are drawn by design/standard/javascript/expdebugbar.js from the
 * debug bar's server functions (ezjscore expdebugbar::*); this class prints the report, the summary
 * and the data the script starts from (a JSON block, #exp-debug-data). Without the script the panel
 * still shows every section, one below the other.
 *
 * eZDebug::printReportInternal() hands HTML reports to render() when this file is present; text
 * reports and the popup window are unchanged.
 */
class expDebugBarReport
{
    /** Translation context of every string of the bar, in PHP and in the script. */
    const CONTEXT = 'design/standard/debugbar';

    /**
     * The height of the open panel as the user left it, in pixels (0: the default, 60% of the window), kept in this
     * user preference (eZPreferences) for logged-in users only. Saved by the server function expdebugbar::panelheight
     * and read back by render() into #debug-details data-exp-debug-panel-height, so the panel opens at that height.
     * An anonymous visitor (the debug bar shown by IP) never has a preference read or saved (eZPreferences keep an
     * anonymous user's values in the session, and the debug bar never starts one): the script keeps that height in
     * the browser's localStorage (exp-debug-panel-height) instead. A logged-in user's preference is authoritative.
     * The script holds the height between PANEL_HEIGHT_MIN and the window's height minus the pinned bar;
     * PANEL_HEIGHT_MAX bounds what is stored.
     */
    const PANEL_HEIGHT_PREFERENCE = 'expdebugbar_panel_height';
    const PANEL_HEIGHT_MIN = 120;
    const PANEL_HEIGHT_MAX = 4000;

    /**
     * The looks of the bar, debugbar.ini [DebugBarSettings] Theme: 'default' (the light chips) and 'toolbar' (a dark,
     * compact toolbar with an icon in each block, the request's HTTP status and module/view in the first one, and the
     * details of a block in a popup above it while the pointer or the focus is on it). The panel is the same in both.
     */
    const THEMES = array( 'default', 'toolbar' );

    /**
     * When a summary value turns amber (warn) and red (high).
     * Memory is compared with memory_limit when PHP has one, else with the absolute bytes.
     */
    protected static $thresholds = array(
        'time'      => array( 'warn' => 1.0, 'high' => 3.0 ),          // seconds
        'sql_count' => array( 'warn' => 100, 'high' => 300 ),
        'sql_ms'    => array( 'warn' => 200, 'high' => 1000 ),
        'memory'    => array( 'warn' => 0.5, 'high' => 0.8 ),          // share of memory_limit
        'memory_abs'=> array( 'warn' => 134217728, 'high' => 268435456 ), // 128 MB, 256 MB
        'templates' => array( 'warn' => 150, 'high' => 400 ),
        'querycache_hit_rate' => array( 'warn' => 50, 'high' => 10 ), // percent: the level rises BELOW these
        'opcache_memory' => array( 'warn' => 80, 'high' => 95 ),     // percent of opcache.memory_consumption used
        'apcu_memory'    => array( 'warn' => 75, 'high' => 90 ),     // percent of apc.shm_size used
    );

    /**
     * The theme the bar is drawn in (THEMES): debugbar.ini [DebugBarSettings] Theme, 'default' when the INI can not
     * be read (an early fatal error, a script without the kernel).
     */
    public static function theme()
    {
        if ( !class_exists( 'eZINI', false ) )
            return 'default';
        try
        {
            $ini = eZINI::instance( 'debugbar.ini' );
            return self::themeName( $ini->hasVariable( 'DebugBarSettings', 'Theme' ) ? $ini->variable( 'DebugBarSettings', 'Theme' ) : '' );
        }
        catch ( Throwable $e )
        {
            return 'default';
        }
    }

    /** $value as one of THEMES, without regard to case; 'default' for anything else. */
    public static function themeName( $value )
    {
        $value = is_string( $value ) ? strtolower( trim( $value ) ) : '';
        return in_array( $value, self::THEMES, true ) ? $value : 'default';
    }

    /**
     * Translates $source in the bar's context, when the translation system is there (it is not in an
     * early fatal error, or in a script that never loaded the kernel).
     */
    public static function t( $source, $arguments = array() )
    {
        if ( class_exists( 'ezpI18n', true ) )
        {
            try
            {
                return ezpI18n::tr( self::CONTEXT, $source, null, $arguments );
            }
            catch ( Throwable $e )
            {
            }
        }
        return $arguments ? strtr( $source, $arguments ) : $source;
    }

    /**
     * A panel height as stored or sent: a whole number of pixels (an int, or a string of digits with optional
     * spaces around it). 0 stays 0 (the default height); any other number is held between PANEL_HEIGHT_MIN and
     * PANEL_HEIGHT_MAX.
     *
     * @param mixed $value
     * @return int|null the height, or null when $value is not a whole number of pixels (empty, negative, a
     *                  fraction, text, an array, a boolean)
     */
    public static function panelHeight( $value )
    {
        if ( is_int( $value ) )
            $digits = $value >= 0 ? (string)$value : '';
        else if ( is_string( $value ) )
            $digits = trim( $value );
        else
            return null;
        if ( $digits === '' || !ctype_digit( $digits ) )
            return null;
        // more digits than an int holds is far above the maximum
        $height = strlen( $digits ) > 9 ? self::PANEL_HEIGHT_MAX : (int)$digits;
        if ( $height === 0 )
            return 0;
        return max( self::PANEL_HEIGHT_MIN, min( self::PANEL_HEIGHT_MAX, $height ) );
    }

    /**
     * The current user's saved panel height (preference PANEL_HEIGHT_PREFERENCE) for render(): 0 for the default,
     * for a value that is not a height, and when there is no user to ask (an early error, a script without the
     * kernel, no database). For a visitor who is not logged in it is always 0 and the preferences are not asked at
     * all: eZPreferences keep an anonymous user's values in the session, and the debug bar never starts one.
     *
     * @param bool $loggedIn whether the current user is logged in (render() passes userInfo()'s logged_in)
     * @param callable|null $reader function( string $name ): mixed, the preference reader (tests); default eZPreferences::value()
     * @return int
     */
    public static function savedPanelHeight( $loggedIn, $reader = null )
    {
        if ( $loggedIn !== true )
            return 0;
        try
        {
            if ( $reader === null )
            {
                if ( !class_exists( 'eZUser', false ) || !class_exists( 'eZPreferences' ) )
                    return 0;
                $user = eZUser::currentUser();
                if ( !$user instanceof eZUser || !$user->isRegistered() )
                    return 0;
                $reader = function ( $name ) { return eZPreferences::value( $name ); };
            }
            $height = self::panelHeight( $reader( self::PANEL_HEIGHT_PREFERENCE ) );
            return $height === null ? 0 : $height;
        }
        catch ( Throwable $e )
        {
            return 0;
        }
    }

    /** t() escaped for HTML. */
    protected static function h( $source, $arguments = array() )
    {
        return htmlspecialchars( self::t( $source, $arguments ), ENT_QUOTES, 'UTF-8' );
    }

    /**
     * Every string expdebugbar.js shows. The script looks its words up in this list (passed in the
     * data block) and falls back to the English source, so a string missing here still reads.
     * Every t('...') in the script must be listed here (the bar's test checks it).
     */
    public static function scriptStrings()
    {
        return array(
            'Messages', 'Settings', 'Cache', 'Timing', 'SQL', 'Templates', 'Included files', 'Memory', 'Server', 'Other',
            'Debug report sections', 'Filter', 'Filter settings and report rows', 'No rows match the filter.',
            '%count rows match', 'Clear filter',
            'All', 'Errors', 'Warnings', 'Notices', 'Debug', 'Timing points', 'Strict',
            'Show messages', 'Copied 1 message', 'Copied %count messages','No messages to copy.', 'Could not copy the messages.', 'Sort by time', 'Sort by order', 'Slowest first',
            'Loading...', 'Could not load: %error', 'Retry',
            'Sign in with a user who may change settings (setup/setup) to change them here.', 'Sign in with setup access to change debug settings', 'Sign in with cache access to manage caches',
            'The settings service is not available yet. The classic controls below still work.',
            'Effective value', 'Comes from', 'Write to', 'Apply', 'Applied', 'Not changed', 'Reset', 'Help',
            'default', 'global override', 'siteaccess', 'extension', 'not set',
            'enabled', 'disabled', 'on', 'off',
            'One entry per line', 'Add', 'Remove', 'Label', 'Expires', 'For 1 hour', 'Today', 'Until removed',
            'Add my IPv4 address', 'Add my IPv6 address', 'Add my /24', 'Add my /64',
            'Your address as the server sees it: %ip', 'This request matched: %entry', 'This request matched no entry.',
            'IPv4 or IPv6 address or CIDR range, for example 192.0.2.10, 192.0.2.0/24, 2001:db8::/64',
            'Not a valid IPv4 or IPv6 address or CIDR range.', 'A prefix length must be 0 to 32 for IPv4.',
            'A prefix length must be 0 to 128 for IPv6.', 'Already in the list.',
            'Test an address', 'Test', 'matches %entry', 'matches no entry',
            'Warning: your own address is not in the list. Applying it locks you out of the debug output.',
            'Warning: the list is empty. With "Debug by IP" on, nobody gets the debug output.',
            'Warning: %entry opens the debug output to everyone.',
            'expired', 'expires %time',
            'Add me', 'Find a user', 'User ID or name', 'No user found.', 'Warning: you are not in the list. Applying it locks you out of the debug output.',
            'Presets', 'Apply preset', 'Revert preset', 'Save current as preset', 'Preset name',
            'Preset "%name" applied.', 'Preset reverted.',
            'Change log', 'Undo', 'Undone', 'No changes yet.', '%user changed %setting from %old to %new in %scope',
            'Extension switches',
            'Reload the page to see the effect.', 'Reload now',
            'Clear', 'Cleared', 'Clearing...', 'Last cleared: %time', 'never',
            'This page only', 'Clears the view cache of the node this page shows.', 'This page is not a content node.',
            'Velocity response cache', 'Clears the pages Velocity keeps in memory.',
            'OPcache', 'Status', 'Memory used', 'Hit rate', 'Cached scripts', 'Not available',
            'By tag', 'By ID', 'All caches',
            'You may not clear caches (setup/managecache).',
            'Response cache', 'Engine',
            // the Server tab: a page that came from a cache carries the report of the request that rendered it
            'This report was rendered at %time by %renderer for a request on port %entry, %age before this page was loaded on port %port: the page came from a cache, not from this request.',
            '%count s', '%count min',
            'Close', 'Open the debug report',
            'Minimise debug bar', 'Show debug bar', '%count errors', '%count warnings',
            // the handle that resizes the open panel
            'Resize the debug panel', 'Drag or use the arrow keys to change the height; double-click for the default height.',
            '%count pixels',
            // the SQL query cache in the Cache tab
            'SQL query cache', 'Off (querycache.ini Mode=off)', 'Mode %mode, MaxAge %age s, MaxRows %rows',
            'Storage %storage (setting %setting), ApcuReserve %reserve %', 'none',
            'APCu is not available to this server: shared works as request.', 'APCu', 'Files', '%count entries',
            '%used of %size (%pct %)', 'APCu used in all: %pct %', 'This server',
            'since %time: %requests requests, hit rate %rate, %stores stored, %writes writes', 'State',
            'generation %gen, cleared %time, %count tables written in the last MaxAge',
            'What is cached (by table)', 'Table', 'Entries', 'Rows', 'Size', 'Hits', 'Largest entries', 'Key', 'Tables', 'Age',
            'Only the first %count entries were read.', 'Nothing is cached on this server yet.',
            'Clears every cached result (a new generation).', 'Counters', "Zeroes this server's counters; the cached results stay.",
            '%count results not kept in APCu: less than %reserve % of it was free (ApcuReserve)',
        );
    }

    /**
     * Prints the report. Same contract as eZDebug::printReportInternal() for $as_html = true.
     *
     * @param eZDebug $debug
     * @return string|null the report when $returnReport, else null (printed)
     */
    public static function render( eZDebug $debug, $returnReport, $allowedDebugLevels, $useAccumulators, $useTiming, $useIncludedFiles )
    {
        $reportStart = microtime( true );
        if ( !$allowedDebugLevels )
        {
            $allowedDebugLevels = array( eZDebug::LEVEL_NOTICE, eZDebug::LEVEL_WARNING, eZDebug::LEVEL_ERROR,
                                         eZDebug::LEVEL_DEBUG, eZDebug::LEVEL_TIMING_POINT, eZDebug::LEVEL_STRICT );
        }

        $startTime = $debug->ScriptStart;
        $endTime = $debug->ScriptStop == null ? $reportStart : $debug->ScriptStop;
        // one end of the page for the query cache's figures: its report row (rendered below) and its chip agree
        if ( class_exists( 'eZDBQueryCache', false ) )
            eZDBQueryCache::setRequestEnd( $endTime );
        $totalElapsed = $endTime - $startTime;
        if ( $totalElapsed <= 0 )
            $totalElapsed = 0.000001;
        $peakMemory = function_exists( 'memory_get_peak_usage' ) ? memory_get_peak_usage( true ) : 0;

        if ( $returnReport )
            ob_start();

        $ini = eZINI::instance();
        $byUser = $ini->variable( 'DebugSettings', 'DebugByUser' ) == 'enabled' ? ' ' . self::t( '(By User)' ) : '';
        $byIP = $ini->variable( 'DebugSettings', 'DebugByIP' ) == 'enabled' ? ' ' . self::t( '(By IP Address)' ) : '';

        // Messages, with the SQL statements (SQLOutput) taken out into the SQL tab.
        // the panels of extensions (debugbar.ini Panels[]): a tab after Memory each, a chip and data for those that
        // give them. Collected before the messages, so the warning about a panel left out is among them.
        $panels = self::extensionPanels();
        $messages = self::messageRows( $debug, $allowedDebugLevels );

        // Top reports: the classic toolbar goes to Settings, anything else stays above the messages.
        $classicToolbar = '';
        $otherTop = '';
        foreach ( (array)$debug->topReportsList as $name => $content )
        {
            if ( $name === 'Debug toolbar' )
                $classicToolbar .= $content;
            else
                $otherTop .= $content;
        }

        // Bottom reports: template and packer statistics to Templates, the query cache to SQL, the rest to Other.
        $templateReports = '';
        $sqlReports = '';
        $otherReports = '';
        foreach ( (array)$debug->bottomReportsList as $name => $report )
        {
            $content = is_callable( $report ) ? call_user_func_array( $report, array( true ) ) : $report;
            if ( $name === 'Template Usage Statistics' || $name === 'ezjscPacker' )
                $templateReports .= $content;
            else if ( $name === 'Query cache' )
                $sqlReports .= $content;
            else
                $otherReports .= $content;
        }

        $dbType = preg_replace( '/^ez/', '', $ini->variable( 'DatabaseSettings', 'DatabaseImplementation' ) );
        $queryKey = $dbType . '_query';
        $sqlCount = isset( $debug->TimeAccumulatorList[$queryKey] ) ? (int)$debug->TimeAccumulatorList[$queryKey]['count'] : 0;
        $sqlMs = isset( $debug->TimeAccumulatorList[$queryKey] ) ? $debug->TimeAccumulatorList[$queryKey]['time'] * 1000 : 0.0;

        $templates = self::templatesUsed();
        $memoryLimit = self::bytes( ini_get( 'memory_limit' ) );
        $pageInfo = self::pageInfo();
        $userInfo = self::userInfo();

        $summary = array(
            'time'      => round( $totalElapsed, 4 ),
            'sql_count' => $sqlCount,
            'sql_ms'    => round( $sqlMs, 2 ),
            'memory'    => $peakMemory,
            'memory_limit' => $memoryLimit,
            'templates' => $templates,
            'warnings'  => $messages['counts']['warning'],
            'errors'    => $messages['counts']['error'],
            'notices'   => $messages['counts']['notice'],
            // Written to deprecated.log and counted only, never listed with the messages (eZDebug::writeDeprecated())
            'deprecations' => eZDebug::deprecatedCount(),
            'levels'    => array(),
            // PHP, OPcache and APCu of the process that rendered the page: only for those who may see the privileged
            // rows of the Server tab, since the bar can show to anonymous visitors (DebugByIP)
            'runtime'   => self::runtimeFigures( $userInfo['can_setup'] || $userInfo['can_cache'] ),
        );
        // how this page came about: rendered by the kernel (why), for the first chip of the bar; a page served from the
        // page cache gets the same bar with "Cached" instead (ezpHttpCacheContract::debugBar())
        $summary['served'] = self::servedInfo();
        $theme = self::theme();
        // the first block of the toolbar theme: the status the response has so far, the module and view
        if ( $theme === 'toolbar' )
            $summary['request'] = self::requestInfo( null, null, $pageInfo );

        // The server side's summary (kernel/classes/debugbar), when it is loaded: its levels follow
        // [DebugBarSettings] Thresholds[] in debugbar.ini, so they win over the defaults above.
        $serverSummary = null;
        if ( class_exists( 'expDebugBarSummary' ) && method_exists( 'expDebugBarSummary', 'collect' ) )
        {
            try
            {
                $serverSummary = expDebugBarSummary::collect();
                foreach ( array( 'time' => 'time', 'sql' => 'sql', 'memory' => 'memory', 'templates' => 'templates',
                                 'opcache' => 'opcache', 'apcu' => 'apcu' ) as $from => $to )
                {
                    if ( isset( $serverSummary[$from]['level'] ) && in_array( $serverSummary[$from]['level'], array( 'ok', 'warn', 'high' ), true ) )
                        $summary['levels'][$to] = $serverSummary[$from]['level'];
                }
            }
            catch ( Throwable $e )
            {
                $serverSummary = null;
            }
        }
        $summary['querycache'] = $serverSummary !== null && array_key_exists( 'querycache', $serverSummary )
            ? $serverSummary['querycache'] : self::queryCacheSummary();
        // How the page was rendered (controller, view cache key, compiled templates): for setup/setup only, as the
        // cache key carries the user's role and limitation ids
        $rendering = $userInfo['can_setup'] ? self::renderingData() : null;
        $summary['rendering'] = $rendering === null ? null : self::renderingChips( $rendering );

        // the chips of the extensions' panels (collected before the messages, see above)
        $summary['panels'] = array();
        foreach ( $panels as $panel )
        {
            if ( $panel['chip'] !== null )
                $summary['panels'][] = $panel['chip'];
        }

        $tabs = array(
            'messages'  => self::t( 'Messages' ),
            'settings'  => self::t( 'Settings' ),
            'cache'     => self::t( 'Cache' ),
            'timing'    => self::t( 'Timing' ),
            'sql'       => self::t( 'SQL' ),
            'templates' => self::t( 'Templates' ),
            'rendering' => self::t( 'Rendering' ),
            'includes'  => self::t( 'Included files' ),
            'memory'    => self::t( 'Memory' ),
        );
        foreach ( $panels as $panel )
            $tabs[$panel['tab']] = $panel['title'];
        // id velocity, as the tab was named first: scripts and stylesheets find the section by it
        $tabs['velocity'] = self::t( 'Server' );

        if ( $rendering === null )
            unset( $tabs['rendering'] );
        if ( $otherReports !== '' )
            $tabs['other'] = self::t( 'Other' );
        $badges = array(
            'messages' => count( $messages['rows'] ),
            'sql'      => $sqlCount,
            'templates'=> $templates === null ? '' : $templates,
        );

        // --- the bar. Its stylesheet comes first, so the browser has the rules before it draws the bar (a stylesheet
        // in the body holds back the rendering of what follows it); the report carries no inline CSS. debug.css (the
        // classic table rules, when the design does not load it) comes before it, so the bar's rules of the same
        // weight win over its white rows.
        if ( !$debug->UseCSS )
            echo self::includeTag( 'css', 'design/standard/stylesheets/debug.css' );
        echo self::includeTag( 'css', 'design/standard/stylesheets/expdebugbar.css' );
        // The toolbar theme heads a panel with the figures of its chip, in large type (the rows of the chip's popup)
        $metrics = array();
        if ( $theme === 'toolbar' )
        {
            $panelOf = array( 'errors' => 'messages', 'time' => 'timing', 'sql' => 'sql', 'memory' => 'memory',
                              'templates' => 'templates', 'request' => 'velocity' );
            foreach ( self::summaryChips( $summary, $theme ) as $chip )
            {
                if ( isset( $panelOf[$chip[1]] ) && !empty( $chip['rows'] ) )
                    $metrics[$panelOf[$chip[1]]] = $chip['rows'];
            }
        }
        // the toolbar theme's rules after the bar's own: they win at the same weight
        if ( $theme === 'toolbar' )
            echo self::includeTag( 'css', 'design/standard/stylesheets/expdebugbar-toolbar.css' );
        echo '<div id="debug" class="exp-debug" data-exp-debug-bar="2"' . ( $theme === 'toolbar' ? ' data-exp-debug-theme="toolbar"' : '' ) . '>';
        // Everything the bar shows is in the markup, so its first paint is final (#571): the symbol that minimises it
        // and "Keep open on reload" were added by the scripts after the first paint, which made the bar wrap to a
        // second line and moved its link. expdebugbar-head.js only sets their state.
        echo self::barHtml( $byUser . $byIP, $summary, $theme );
        // the panel's height as the user left it (0: the default); expdebugbar-head.js applies it before the
        // panel opens. From the preference for a logged-in user; for an anonymous visitor from the browser's localStorage
        echo '<div id="debug-details" role="region" aria-label="' . self::h( 'Debug report' ) . '"'
           . ' data-exp-debug-panel-height="' . self::savedPanelHeight( $userInfo['logged_in'] ) . '"'
           . ' data-exp-debug-panel-store="' . ( $userInfo['logged_in'] ? 'preference' : 'browser' ) . '"'
           . ' data-exp-debug-panel-min="' . self::PANEL_HEIGHT_MIN . '" data-exp-debug-panel-max="' . self::PANEL_HEIGHT_MAX . '">';

        echo '<div class="exp-debug-toolbar">';
        echo '<div class="exp-debug-tabs" role="tablist" aria-label="' . self::h( 'Debug report sections' ) . '">';
        foreach ( $tabs as $id => $label )
        {
            $badge = isset( $badges[$id] ) && $badges[$id] !== '' ? ' <span class="exp-debug-badge">' . (int)$badges[$id] . '</span>' : '';
            echo '<button type="button" role="tab" class="exp-debug-tab" id="exp-debug-tab-' . $id . '" aria-controls="exp-debug-panel-' . $id
               . '" aria-selected="false" tabindex="-1" data-tab="' . $id . '">' . htmlspecialchars( $label, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . $badge . '</button>';
        }
        echo '</div>';
        echo '<label class="exp-debug-search"><span class="exp-debug-sr">' . self::h( 'Filter' ) . '</span>'
           . '<input type="search" id="exp-debug-filter" placeholder="' . self::h( 'Filter settings and report rows' ) . '" autocomplete="off"></label>';
        echo '</div>';

        // --- Messages
        self::panelStart( 'messages', $tabs, $metrics );
        echo '<div class="exp-debug-message-tools">';
        echo '<div class="exp-debug-levels" data-counts="' . htmlspecialchars( json_encode( $messages['counts'] ), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '"></div>';
        echo self::copyMessagesHtml();
        echo '</div>';
        echo "<table id='main-debug-table' title='Table for actual debug output, shows notices, warnings and errors'>";
        echo $otherTop;
        echo implode( '', $messages['rows'] );
        echo '</table>';
        self::panelEnd();

        // --- Settings (drawn by the script; the classic toolbar below it)
        self::panelStart( 'settings', $tabs, $metrics );
        echo '<div class="exp-debug-settings" data-state="pending"></div>';
        if ( !$userInfo['can_setup'] )
        {
            // the classic toolbar shows the quick settings' values: for setup/setup only
            echo '<p class="exp-debug-hint exp-debug-warn">' . self::h( 'Sign in with setup access to change debug settings' ) . '</p>';
        }
        else if ( $classicToolbar !== '' )
        {
            echo '<details class="exp-debug-classic"><summary>' . self::h( 'Classic controls' ) . '</summary>';
            echo '<table class="exp-debug-classic-table">' . $classicToolbar . '</table>';
            echo '</details>';
        }
        self::panelEnd();

        // --- Cache
        self::panelStart( 'cache', $tabs, $metrics );
        echo self::cacheHtml( $pageInfo, $userInfo );
        self::panelEnd();

        // --- Timing
        self::panelStart( 'timing', $tabs, $metrics );
        if ( $useTiming )
            echo self::timingPointsHtml( $debug, $startTime );
        if ( $useAccumulators )
            echo self::accumulatorsHtml( $debug, $totalElapsed );
        self::panelEnd();

        // --- SQL
        self::panelStart( 'sql', $tabs, $metrics );
        echo $sqlReports;
        echo self::sqlHtml( $debug, $ini, $dbType, $sqlCount, $sqlMs, $messages['sql'], $totalElapsed );
        self::panelEnd();

        // --- Templates
        self::panelStart( 'templates', $tabs, $metrics );
        if ( $templateReports === '' )
            echo '<p class="exp-debug-hint">' . self::h( 'Template usage is not recorded. Turn on "Show used templates" (TemplateSettings ShowUsedTemplates) in the Settings tab.' ) . '</p>';
        echo $templateReports;
        self::panelEnd();

        // --- Rendering
        if ( $rendering !== null )
        {
            self::panelStart( 'rendering', $tabs, $metrics );
            echo self::renderingHtml( $rendering );
            self::panelEnd();
        }

        // --- Included files
        self::panelStart( 'includes', $tabs, $metrics );
        echo self::includedFilesHtml( $useIncludedFiles );
        self::panelEnd();

        // --- Memory
        self::panelStart( 'memory', $tabs, $metrics );
        echo self::memoryHtml( $debug, $totalElapsed, $peakMemory, $memoryLimit, $sqlCount, $queryKey );
        self::panelEnd();

        // --- the extensions' panels
        foreach ( $panels as $panel )
        {
            self::panelStart( $panel['tab'], $tabs, $metrics );
            echo $panel['html'];
            self::panelEnd();
        }

        // --- Server (id velocity)
        self::panelStart( 'velocity', $tabs, $metrics );
        echo self::velocityHtml( $userInfo['can_setup'] || $userInfo['can_cache'] );
        self::panelEnd();

        if ( $otherReports !== '' )
        {
            self::panelStart( 'other', $tabs, $metrics );
            echo $otherReports;
            self::panelEnd();
        }

        $reportTime = number_format( microtime( true ) - $reportStart, 4 );
        echo '<p class="exp-debug-report-time">' . self::h( 'Time used to render debug report: %time secs', array( '%time' => $reportTime ) ) . '</p>';

        echo '</div>'; // #debug-details
        echo '</div>'; // #debug
        echo '<a id="debug-end"></a>';

        $data = array(
            'version'    => 2,
            'summary'    => $summary,
            'server_summary' => $serverSummary,
            'thresholds' => self::$thresholds,
            'page'       => $pageInfo,
            'user'       => $userInfo,
            'client'     => array( 'ip' => class_exists( 'eZSys' ) ? (string)eZSys::clientIP() : '' ),
            'debug_by'   => array( 'ip' => $ini->variable( 'DebugSettings', 'DebugByIP' ) == 'enabled',
                                   'user' => $ini->variable( 'DebugSettings', 'DebugByUser' ) == 'enabled' ),
            'velocity'   => defined( 'QBIX_SERVER_VERSION' ),
            'urls'       => array( 'call' => self::url( 'ezjscore/call' ),
                                   'cachetoolbar' => self::url( 'setup/cachetoolbar' ) ),
            'served'     => $summary['served'],
            'strings'    => self::translatedScriptStrings(),
            'panels'     => array_filter( array_column( $panels, 'data', 'id' ), 'is_array' ),
            'panel'      => array( 'min' => self::PANEL_HEIGHT_MIN, 'max' => self::PANEL_HEIGHT_MAX ),
        );
        echo '<script type="application/json" id="exp-debug-data">'
           . json_encode( $data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR )
           . '</script>';

        echo self::assetsHtml();

        if ( $returnReport )
        {
            $text = ob_get_contents();
            ob_end_clean();
            return $text;
        }
        return null;
    }

    protected static function panelStart( $id, array $tabs, array $metrics = array() )
    {
        echo '<section class="exp-debug-panel" role="tabpanel" id="exp-debug-panel-' . $id . '" aria-labelledby="exp-debug-tab-' . $id . '" tabindex="0">';
        echo '<h3 class="exp-debug-panel-title">' . htmlspecialchars( $tabs[$id], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '</h3>';
        if ( !empty( $metrics[$id] ) )
            echo self::metricsHtml( $metrics[$id] );
    }

    /**
     * The metrics at the head of a panel in the toolbar theme: a value in large type over its label, for each row
     * (label => value) of the panel's chip.
     */
    public static function metricsHtml( array $rows )
    {
        $h = function ( $value ) { return htmlspecialchars( (string)$value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ); };
        $html = '<dl class="exp-debug-metrics">';
        foreach ( $rows as $label => $value )
            $html .= '<div class="exp-debug-metric"><dt>' . $h( $label ) . '</dt><dd>' . $h( $value ) . '</dd></div>';
        return $html . '</dl>';
    }

    protected static function panelEnd()
    {
        echo '</section>';
    }

    /**
     * The "Copy Messages" button of the Messages tab and the line that reports how the copy went. The script
     * (expdebugbar.js) shows the button and copies the messages that are shown (level and filter applied) as
     * plain text; without the script it stays hidden, as it could do nothing.
     */
    public static function copyMessagesHtml()
    {
        return '<span class="exp-debug-copy">'
             . '<button type="button" class="exp-debug-button exp-debug-copy-messages" hidden>' . self::h( 'Copy Messages' ) . '</button>'
             . ' <span class="exp-debug-copy-status" role="status" aria-live="polite"></span>'
             . '</span>';
    }

    /**
     * The message rows, as the classic report printed them, plus the level on each row for the filter.
     *
     * A message that is not a string (null, a number) is shown as its string value, with xdebug loaded too.
     *
     * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 4065ed16b3), Copyright (C) Mugo Web, GPL v2.
     *
     * @param bool|null $xdebug whether xdebug is loaded; null: ask PHP (a test passes true)
     * @return array( 'rows' => string[], 'sql' => array of array( 'html', 'ms', 'n' ), 'counts' => level => count )
     */
    protected static function messageRows( eZDebug $debug, array $allowedDebugLevels, $xdebug = null )
    {
        $hasLevel = array();
        $rows = array();
        $sql = array();
        $counts = array( 'notice' => 0, 'warning' => 0, 'error' => 0, 'debug' => 0, 'timing' => 0, 'strict' => 0 );
        $xdebug = $xdebug === null ? extension_loaded( 'xdebug' ) : (bool)$xdebug;
        foreach ( $debug->DebugStrings as $entry )
        {
            if ( !in_array( $entry['Level'], $allowedDebugLevels ) )
                continue;
            $outputData = isset( $debug->OutputFormat[$entry['Level']] ) ? $debug->OutputFormat[$entry['Level']] : null;
            if ( !is_array( $outputData ) )
                continue;
            $style = $outputData['style'];
            $label = (string)$entry['Label'];
            $isSql = preg_match( '/::query\((?:(\d+) rows, )?([0-9.,]+) ms\) query number per page:(\d+)/', $label, $m );

            $identifierText = '';
            if ( !$isSql && !isset( $hasLevel[$entry['Level']] ) )
            {
                $hasLevel[$entry['Level']] = true;
                $identifierText = ' id="' . $outputData['xhtml-identifier'] . '"';
            }
            $time = date( "M d Y H:i:s", $entry['Time'] );
            $bgclass = $entry['BackgroundClass'];
            $pre = $bgclass != '' ? " class='$bgclass'" : '';
            $text = (string)$entry['String'];
            if ( $xdebug && strncmp( eZDebug::XDEBUG_SIGNATURE, $text, strlen( eZDebug::XDEBUG_SIGNATURE ) ) === 0 )
                $contents = substr( $text, strlen( eZDebug::XDEBUG_SIGNATURE ) );
            else
                $contents = htmlspecialchars( $text, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 );

            $html = "<tr class='$style' data-level='$style'><td class='debugheader debuglabel'$identifierText><span>{$outputData['name']}:</span> " . htmlspecialchars( $label, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . "</td>"
                  . "<td class='debugheader debugtime'>$time</td></tr>"
                  . "<tr class='debugbody' data-level='$style'><td colspan='2'><pre$pre>" . $contents . "</pre></td></tr>";
            if ( $isSql )
            {
                $sql[] = array( 'html' => $html, 'ms' => (float)str_replace( ',', '', $m[2] ), 'n' => (int)$m[3] );
                continue;
            }
            if ( isset( $counts[$style] ) )
                ++$counts[$style];
            $rows[] = $html;
        }
        return array( 'rows' => $rows, 'sql' => $sql, 'counts' => $counts );
    }

    /**
     * The pinned bar and the minimised tab, complete at the first paint (#571): the symbol that minimises the bar, the
     * "Exp Debug" link, the chips, "Keep open on reload" (its box alone in the toolbar theme, the words its tooltip),
     * and the hidden tab that restores the bar with the error or warning count. expdebugbar-head.js sets their state
     * before the first paint, expdebugbar.js wires them.
     *
     * @param string $by " (By IP Address)" and the like
     * @param array $summary the bar's summary (render())
     * @param string $theme THEMES
     */
    public static function barHtml( $by, array $summary, $theme = 'default' )
    {
        $e = function ( $value ) { return htmlspecialchars( (string)$value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ); };
        $minimise = self::t( 'Minimise debug bar' );
        $keep = self::t( 'Keep open on reload' );
        $html = '<h2><button type="button" class="exp-debug-logo" aria-label="' . $e( $minimise ) . '" title="' . $e( $minimise ) . '">' . self::symbolSvg() . '</button>'
              . '<a href="#debug-end" aria-controls="debug-details" aria-expanded="false">Exp Debug' . $e( $by ) . '</a>'
              . self::servedHtml( isset( $summary['served'] ) ? $summary['served'] : null )
              . self::summaryHtml( $summary, $theme )
              . '<label class="debug-keep-open"' . ( $theme === 'toolbar' ? ' title="' . $e( $keep ) . '"' : '' ) . '><input type="checkbox"> ' . $e( $keep ) . '</label>'
              . '</h2>';
        $errors = isset( $summary['errors'] ) ? (int)$summary['errors'] : 0;
        $warnings = isset( $summary['warnings'] ) ? (int)$summary['warnings'] : 0;
        $badge = $errors > 0 ? self::t( '%count errors', array( '%count' => $errors ) ) : ( $warnings > 0 ? self::t( '%count warnings', array( '%count' => $warnings ) ) : '' );
        $label = self::t( 'Show debug bar' ) . ( $badge !== '' ? ' (' . $badge . ')' : '' );
        $html .= '<button type="button" class="exp-debug-restore" hidden aria-label="' . $e( $label ) . '" title="' . $e( $label ) . '">' . self::symbolSvg()
               . ( $badge !== '' ? '<span class="exp-debug-restore-badge" data-level="' . ( $errors > 0 ? 'high' : 'warn' ) . '" aria-hidden="true">' . ( $errors > 0 ? $errors : $warnings ) . '</span>' : '' )
               . '</button>';
        return $html;
    }

    /** The Exponential symbol (the two squares of the logo) of the minimise button and the minimised tab. */
    protected static function symbolSvg()
    {
        return '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" class="exp-debug-symbol">'
             . '<rect x="2" y="2" width="13.5" height="13.5" fill="none" stroke="currentColor" stroke-width="2" class="exp-debug-symbol-back"></rect>'
             . '<rect x="8.5" y="8.5" width="13.5" height="13.5" fill="#f05a22" stroke="currentColor" stroke-width="2" class="exp-debug-symbol-front"></rect></svg>';
    }

    /** The chips of the pinned bar, each opening its tab. */
    protected static function summaryHtml( array $s, $theme = 'default' )
    {
        $toolbar = $theme === 'toolbar';
        $html = '<span class="exp-debug-summary" role="group" aria-label="' . self::h( 'Page summary' ) . '">';
        foreach ( self::summaryChips( $s, $theme ) as $chip )
        {
            list( $tab, $key, $lvl, $text, $title ) = $chip;
            $push = !empty( $chip[5] );
            $levelWord = $lvl === 'high' ? self::t( 'high' ) : ( $lvl === 'warn' ? self::t( 'raised' ) : self::t( 'normal' ) );
            // the page time is marked, for the page cache to put the time of its own answer there (SERVED_TIME)
            if ( $key === 'time' )
                $html .= self::SERVED_TIME_START;
            $html .= '<button type="button" class="exp-debug-chip' . ( $push ? ' exp-debug-end' : '' ) . '" data-tab="' . $tab . '" data-key="' . $key . '" data-level="' . $lvl . '"'
                   // the toolbar theme shows the title in the block's popup, a tooltip of the browser would repeat it
                   . ( $toolbar ? '' : ' title="' . htmlspecialchars( $title . ' (' . $levelWord . ')', ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '"' )
                   . ' aria-label="' . htmlspecialchars( $title . ': ' . $text . ' (' . $levelWord . ')', ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '">'
                   . ( $toolbar ? self::chipInfoHtml( $text, $title, isset( $chip['rows'] ) ? (array)$chip['rows'] : array() )
                                : htmlspecialchars( $text, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) )
                   . '</button>';
            if ( $key === 'time' )
                $html .= self::SERVED_TIME_END;
        }
        return $html . '</span>';
    }

    /** Marks around the whole debug report in the page (eZDisplayResult()): the page cache keeps the report apart */
    const BAR_START = '<!--exp-debug-bar-->';
    const BAR_END = '<!--/exp-debug-bar-->';

    /** Marks around the "how this page came about" part of the bar (servedHtml()), replaced when served from cache */
    const SERVED_START = '<!--exp-served-->';
    const SERVED_END = '<!--/exp-served-->';

    /** Marks around the page time chip */
    const SERVED_TIME_START = '<!--exp-served-time-->';
    const SERVED_TIME_END = '<!--/exp-served-time-->';

    /**
     * How this page came about, for the bar's first chip and the data block: rendered by the kernel, why (a full render
     * asked for with the view parameter (exp_render)/full, or the page cache's answer to the lookup), the addresses
     * of the cached view and of a full render, and the texts the page cache uses when it serves this bar with a page
     * it kept (ezpHttpCacheContract::debugBar()), translated here since it runs before the kernel.
     *
     * @return array
     */
    public static function servedInfo()
    {
        if ( !class_exists( 'expRenderSwitch' ) && is_file( __DIR__ . '/../../../kernel/private/classes/httpcache/exprenderswitch.php' ) )
            require_once __DIR__ . '/../../../kernel/private/classes/httpcache/exprenderswitch.php';
        $switch = class_exists( 'expRenderSwitch' );
        $full = $switch && expRenderSwitch::asked();
        $lookup = isset( $GLOBALS['EXP_HTTPCACHE_LOOKUP'] ) ? (string)$GLOBALS['EXP_HTTPCACHE_LOOKUP'] : '';
        $uri = isset( $_SERVER['REQUEST_URI'] ) ? (string)$_SERVER['REQUEST_URI'] : '/';
        if ( $full )
            $why = self::t( 'a full render was asked for' );
        else if ( $lookup !== '' )
            $why = self::t( 'the page cache did not have the page (%reason)', array( '%reason' => $lookup ) );
        else
            $why = self::t( 'the page cache did not answer' );
        return array(
            'by'         => 'render',
            'full_asked' => $full,
            'reason'     => $full ? 'full render asked' : ( $lookup !== '' ? $lookup : 'not looked up' ),
            'render_ms'  => null,
            'urls'       => array( 'cached' => $uri, 'full' => $switch ? expRenderSwitch::add( $uri ) : $uri ),
            'labels'     => array(
                'render'       => self::t( 'Rendered' ),
                'render_title' => self::t( 'Rendered by the kernel: %why', array( '%why' => $why ) ),
                'cache'        => self::t( 'Cached' ),
                // %ms, %stored, %render and %age are filled in by the page cache when it serves the page
                'cache_title'  => self::t( 'Served from the page cache in %ms ms. The page was stored at %stored by a render of %render ms, %age s ago.' ),
                'render_now'   => self::t( 'Render now' ),
                'back'         => self::t( 'Back to the cached view' ),
                'from_render'  => self::t( 'From the render at %stored (%render ms)' ),
                'this_request' => self::t( 'This request: served from the page cache in %ms ms.' ),
            ),
        );
    }

    /**
     * The bar's first chip, "Rendered" with the reason in its title, and with a full render asked for, the link back
     * to the cached view; between SERVED_START and SERVED_END, which the page cache replaces with "Cached" and a
     * "Render now" link when it serves the page.
     *
     * @param array|null $served servedInfo()
     * @return string
     */
    public static function servedHtml( $served )
    {
        if ( !is_array( $served ) )
            return '';
        $e = function ( $value ) { return htmlspecialchars( (string)$value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ); };
        $html = '<span class="exp-debug-served" data-served="render" title="' . $e( $served['labels']['render_title'] ) . '">'
              . $e( $served['labels']['render'] ) . '</span>';
        if ( !empty( $served['full_asked'] ) )
            $html .= '<a class="exp-debug-served-action" href="' . $e( $served['urls']['cached'] ) . '">' . $e( $served['labels']['back'] ) . '</a>';
        return self::SERVED_START . $html . self::SERVED_END;
    }

    /**
     * The chips of the summary, in their order: array( tab, key, level, text, title ), index 5 true for the first chip
     * at the right end, and 'rows' (label => value) for the popup of the toolbar theme and the metrics of its panels.
     *
     * @return array
     */
    protected static function summaryChips( array $s, $theme = 'default' )
    {
        $t = self::$thresholds;
        $level = function( $value, $warn, $high ) { return $value >= $high ? 'high' : ( $value >= $warn ? 'warn' : 'ok' ); };
        if ( $s['memory_limit'] > 0 )
            $memLevel = $level( $s['memory'] / $s['memory_limit'], $t['memory']['warn'], $t['memory']['high'] );
        else
            $memLevel = $level( $s['memory'], $t['memory_abs']['warn'], $t['memory_abs']['high'] );
        $sqlLevel = max( array_search( $level( $s['sql_count'], $t['sql_count']['warn'], $t['sql_count']['high'] ), array( 'ok', 'warn', 'high' ) ),
                         array_search( $level( $s['sql_ms'], $t['sql_ms']['warn'], $t['sql_ms']['high'] ), array( 'ok', 'warn', 'high' ) ) );
        $sqlLevel = array( 'ok', 'warn', 'high' )[$sqlLevel];
        $tplLevel = $s['templates'] === null ? 'ok' : $level( $s['templates'], $t['templates']['warn'], $t['templates']['high'] );
        $timeLevel = $level( $s['time'], $t['time']['warn'], $t['time']['high'] );
        foreach ( (array)$s['levels'] as $key => $value )
        {
            if ( $key === 'time' ) $timeLevel = $value;
            elseif ( $key === 'sql' ) $sqlLevel = $value;
            elseif ( $key === 'memory' ) $memLevel = $value;
            elseif ( $key === 'templates' ) $tplLevel = $value;
        }

        $toolbar = $theme === 'toolbar';
        $chips = array();
        // The toolbar theme starts with the request, the details of a block are shown in its popup ('rows')
        if ( $toolbar && isset( $s['request'] ) && is_array( $s['request'] ) )
            $chips[] = self::requestChip( $s['request'] );
        $pageMs = $s['time'] * 1000;
        $chips[] = array( 'timing', 'time', $timeLevel,
                          number_format( $s['time'], 3 ) . ' s', self::t( 'Page time' ),
                          'rows' => array( self::t( 'Page time' ) => number_format( $pageMs, 0, '.', '' ) . ' ms',
                                           self::t( 'Query time' ) => number_format( $s['sql_ms'], 0, '.', '' ) . ' ms' ) );
        $chips[] = array( 'sql', 'sql', $sqlLevel,
                          self::t( '%count SQL', array( '%count' => $s['sql_count'] ) ) . ' / ' . number_format( $s['sql_ms'], 0 ) . ' ms',
                          self::t( 'SQL queries and their time' ),
                          'rows' => array( self::t( 'Queries' ) => (string)(int)$s['sql_count'],
                                           self::t( 'Query time' ) => number_format( $s['sql_ms'], 1, '.', '' ) . ' ms',
                                           self::t( 'Share of page time' ) => ( $pageMs > 0 ? number_format( min( 100, $s['sql_ms'] / $pageMs * 100 ), 0, '.', '' ) : '0' ) . ' %' ) );
        if ( !empty( $s['querycache']['measured'] ) )
        {
            $q = $s['querycache'];
            // the saved time against this page's time: 200 % = without the cache the page would have taken three times as long.
            // The page's time is the snapshot's (eZDBQueryCache::requestMs(), ended where this output started), the same
            // span the report row in the SQL tab gives; the bar's own page time only where the figures carry none.
            $pageMs = isset( $q['request_ms'] ) ? (float)$q['request_ms'] : ( isset( $s['time'] ) ? $s['time'] * 1000 : null );
            $share = class_exists( 'eZDBQueryCache' ) ? eZDBQueryCache::savedShare( $q['saved_ms'], $pageMs ) : null;
            $factor = class_exists( 'eZDBQueryCache' ) ? eZDBQueryCache::savedFactor( $q['saved_ms'], $pageMs ) : null;
            $chips[] = array( 'sql', 'querycache', in_array( $q['level'], array( 'ok', 'warn', 'high' ), true ) ? $q['level'] : 'ok',
                              self::t( 'QC %rate', array( '%rate' => $q['hit_rate'] === null ? '-' : number_format( $q['hit_rate'], 0 ) . ' %' ) )
                              . ' · ' . ( round( $q['saved_ms'] ) > 0 ? '-' : '' ) . number_format( $q['saved_ms'], 0 ) . ' ms'
                              // no thousands separator: "2,761 %" would read as a decimal in German
                              . ( $share !== null ? ' (' . number_format( $share, 0, '.', '' ) . ' %)' : '' ),
                              self::t( 'SQL query cache: hit rate and database time saved (estimated); %hits hits, %misses misses, %stores stored, %skipped not cached, %writes writes, mode %mode',
                                       array( '%hits' => $q['hits'], '%misses' => $q['misses'], '%stores' => $q['stores'], '%skipped' => $q['not_cached'],
                                              '%writes' => $q['writes'], '%mode' => $q['mode'] . ( $q['mode'] !== 'shared' ? ''
                                                  : ( empty( $q['storage'] ) ? ' (no APCu)' : ' (' . $q['storage'] . ')' ) ) ) )
                              . ( $share !== null
                                  ? '. ' . self::t( 'The saved time is %share % of this page\'s time: without the cache about %without ms instead of %with ms',
                                                    array( '%share' => number_format( $share, 0, '.', '' ),
                                                           '%without' => number_format( $pageMs + $q['saved_ms'], 0, '.', '' ),
                                                           '%with' => number_format( $pageMs, 0, '.', '' ) ) )
                                    . ( $factor !== null ? ' ' . self::t( '(%factor times as long)', array( '%factor' => $factor ) ) : '' )
                                  : '' ) );
        }
        $chips[] = array( 'memory', 'memory', $memLevel, number_format( $s['memory'] / 1048576, 1 ) . ' MB', self::t( 'Peak memory' ),
                          'rows' => array( self::t( 'Peak memory' ) => number_format( $s['memory'] / 1048576, 1, '.', '' ) . ' MB',
                                           self::t( 'Memory limit' ) => $s['memory_limit'] > 0 ? number_format( $s['memory_limit'] / 1048576, 0, '.', '' ) . ' MB' : self::t( 'none' ) ) );
        if ( $s['templates'] !== null )
            $chips[] = array( 'templates', 'templates', $tplLevel,
                              self::t( '%count tpl', array( '%count' => $s['templates'] ) ), self::t( 'Templates used' ),
                              'rows' => array( self::t( 'Templates used' ) => (string)(int)$s['templates'] ) );
        if ( !empty( $s['rendering'] ) )
        {
            foreach ( $s['rendering'] as $chip )
                $chips[] = $chip;
        }
        // PHP deprecations: only their number, the messages are in deprecated.log (site.ini DeprecatedLog)
        $deprecations = isset( $s['deprecations'] ) ? (int)$s['deprecations'] : 0;
        // the three message blocks show the same counts in their popups
        $messageRows = array( self::t( 'Errors' ) => (string)(int)$s['errors'], self::t( 'Warnings' ) => (string)(int)$s['warnings'],
                              self::t( 'Notices' ) => (string)( isset( $s['notices'] ) ? (int)$s['notices'] : 0 ),
                              self::t( 'Deprecations' ) => (string)$deprecations );
        $chips[] = array( 'messages', 'warnings', $s['warnings'] > 0 ? 'warn' : 'ok',
                          self::t( '%count warn', array( '%count' => $s['warnings'] ) ), self::t( 'Warnings' ), 'rows' => $messageRows );
        $chips[] = array( 'messages', 'errors', $s['errors'] > 0 ? 'high' : 'ok',
                          self::t( '%count err', array( '%count' => $s['errors'] ) ), self::t( 'Errors' ), 'rows' => $messageRows );
        $chips[] = array( 'messages', 'deprecations', $deprecations > 0 ? 'warn' : 'ok',
                          self::t( '%count depr', array( '%count' => $deprecations ) ),
                          self::t( 'PHP deprecations, written to deprecated.log' ), 'rows' => $messageRows );
        foreach ( isset( $s['panels'] ) ? (array)$s['panels'] : array() as $chip )
            $chips[] = $chip;
        // At the right end: OPcache, APCu, then the versions; the first of them pushes them all there
        $runtime = isset( $s['runtime'] ) ? $s['runtime'] : null;
        $end = array_values( array_filter( array_merge( self::runtimeChips( $runtime, (array)$s['levels'] ), array( self::versionsChip( $runtime ) ) ) ) );
        foreach ( $end as $i => $chip )
            $chips[] = $i === 0 ? array_merge( $chip, array( true ) ) : $chip;

        return $chips;
    }

    /**
     * The inside of a block of the toolbar theme: its text, and the popup with the title and the rows (label =>
     * value). The popup is hidden from assistive technology, the block's aria-label carries the same.
     */
    public static function chipInfoHtml( $text, $title, array $rows )
    {
        $h = function ( $value ) { return htmlspecialchars( (string)$value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ); };
        $html = '<span class="exp-debug-chip-text">' . $h( $text ) . '</span>'
              . '<span class="exp-debug-chip-info" aria-hidden="true"><span class="exp-debug-chip-info-title">' . $h( $title ) . '</span>';
        foreach ( $rows as $label => $value )
            $html .= '<span class="exp-debug-chip-info-row"><span>' . $h( $label ) . '</span><span>' . $h( $value ) . '</span></span>';
        return $html . '</span>';
    }

    /**
     * The request for the first block of the toolbar theme: the HTTP status the response has so far (null where PHP
     * does not know it, as on the command line and in a persistent worker), the module and view that ran, from
     * $GLOBALS['eZRequestedModuleParams'], and the siteaccess and node of pageInfo().
     *
     * @param int|false|null $status http_response_code(), null: ask PHP
     * @param array|null $params the module parameters, null: the global ones
     * @param array $page pageInfo()
     * @return array status, module, view, siteaccess, node_id
     */
    public static function requestInfo( $status = null, $params = null, array $page = array() )
    {
        if ( $status === null )
            $status = function_exists( 'http_response_code' ) ? http_response_code() : false;
        if ( $params === null )
            $params = isset( $GLOBALS['eZRequestedModuleParams'] ) ? $GLOBALS['eZRequestedModuleParams'] : null;
        return array(
            'status' => is_int( $status ) && $status >= 100 && $status <= 599 ? $status : null,
            'module' => is_array( $params ) && isset( $params['module_name'] ) && is_string( $params['module_name'] ) ? $params['module_name'] : '',
            'view' => is_array( $params ) && isset( $params['function_name'] ) && is_string( $params['function_name'] ) ? $params['function_name'] : '',
            'siteaccess' => isset( $page['siteaccess'] ) ? (string)$page['siteaccess'] : '',
            'node_id' => isset( $page['node_id'] ) ? (int)$page['node_id'] : 0,
        );
    }

    /**
     * The first block of the toolbar theme from requestInfo(): "200 content/view", amber from status 400, red from
     * 500. It opens the Server tab, which says where the request entered.
     *
     * @return array a chip as summaryHtml() draws it
     */
    public static function requestChip( array $r )
    {
        $status = isset( $r['status'] ) ? $r['status'] : null;
        $route = $r['module'] !== '' ? $r['module'] . ( $r['view'] !== '' ? '/' . $r['view'] : '' ) : '';
        $text = trim( ( $status !== null ? $status : '' ) . ' ' . $route );
        $level = $status >= 500 ? 'high' : ( $status >= 400 ? 'warn' : 'ok' );
        $rows = array( self::t( 'HTTP status' ) => $status !== null ? (string)$status : '-',
                       self::t( 'Module' ) => $r['module'] !== '' ? $r['module'] : '-',
                       self::t( 'View' ) => $r['view'] !== '' ? $r['view'] : '-' );
        if ( $r['siteaccess'] !== '' )
            $rows[self::t( 'Siteaccess' )] = $r['siteaccess'];
        if ( $r['node_id'] > 0 )
            $rows[self::t( 'Node ID' )] = (string)$r['node_id'];
        return array( 'velocity', 'request', $level, $text !== '' ? $text : '-', self::t( 'Request' ), 'rows' => $rows );
    }

    /**
     * The query cache's figures for the summary when the kernel's summary (expDebugBarSummary) is not loaded,
     * against the default thresholds: null when the cache is off, else queryCacheFigures().
     *
     * @return array|null
     */
    protected static function queryCacheSummary()
    {
        if ( !class_exists( 'eZDBQueryCache', false ) || !eZDBQueryCache::enabled() )
            return null;
        return self::queryCacheFigures( eZDBQueryCache::debugSnapshot(), array_values( self::$thresholds['querycache_hit_rate'] ),
                                        eZDBQueryCache::settings()['mode'] );
    }

    /**
     * The query cache's figures for the summary chip and the page's JSON, from eZDBQueryCache::debugSnapshot(): the
     * snapshot with 'measured' => true, its level, and the number of writes that made everything stale
     * ('stale_count') instead of their statements, which stay in the report of the SQL tab. Without a snapshot (the
     * cache saw no statement, or its report is off): 'measured' => false. The one place for these rules: the
     * kernel's expDebugBarSummary uses it too.
     *
     * @param array|null $snapshot eZDBQueryCache::debugSnapshot()
     * @param array $limits array( warn, high ): hit rates in percent below which the level turns warn and high
     * @param string|null $mode the cache's mode, for 'measured' => false
     * @return array
     */
    public static function queryCacheFigures( $snapshot, array $limits, $mode = null )
    {
        if ( !is_array( $snapshot ) )
            return array( 'measured' => false, 'mode' => $mode, 'level' => 'ok' );
        $level = self::queryCacheLevel( $snapshot, $limits );
        $snapshot['stale_count'] = self::queryCacheStaleCount( $snapshot );
        unset( $snapshot['stale'], $snapshot['stale_more'] );
        return array( 'measured' => true ) + $snapshot + array( 'level' => $level );
    }

    /**
     * ok, warn or high for a query cache snapshot (or the figures made from one): high in shared mode without a storage
     * (Storage=apcu without APCu: it works as request then; Storage=auto falls back to the files) or below the second
     * hit rate of $limits, or when a write could not take the state lock (lock_failed); warn below the first, or when a write made
     * every result stale. Without a lookup the hit rate says nothing: ok, unless one of the others holds.
     *
     * @param array $snapshot
     * @param array $limits array( warn, high ), hit rates in percent; the level rises BELOW them
     * @return string
     */
    public static function queryCacheLevel( array $snapshot, array $limits )
    {
        list( $warn, $high ) = array_values( $limits ) + array( 50, 10 );
        // 'storage' since the file storage; a snapshot without it (older figures, tests) has only 'apcu'
        $storage = array_key_exists( 'storage', $snapshot ) ? $snapshot['storage'] : ( empty( $snapshot['apcu'] ) ? null : 'apcu' );
        if ( ( $snapshot['mode'] ?? '' ) === 'shared' && empty( $storage ) )
            return 'high';
        // a write could not take the state lock: every write of the request emptied the cache (the SQL tab says why)
        if ( !empty( $snapshot['lock_failed'] ) )
            return 'high';
        $rate = $snapshot['hit_rate'] ?? null;
        if ( $rate !== null && $rate < $high )
            return 'high';
        if ( ( $rate !== null && $rate < $warn ) || self::queryCacheStaleCount( $snapshot ) > 0 )
            return 'warn';
        return 'ok';
    }

    /** How many writes made everything stale: 'stale_count' of the figures, or the statements of a snapshot. */
    protected static function queryCacheStaleCount( array $snapshot )
    {
        if ( isset( $snapshot['stale_count'] ) )
            return (int)$snapshot['stale_count'];
        return count( (array)( $snapshot['stale'] ?? array() ) ) + (int)( $snapshot['stale_more'] ?? 0 );
    }

    protected static function timingPointsHtml( eZDebug $debug, $startTime )
    {
        $acc = $debug->TimingAccuracy;
        $html = "<div id='timing-points'><h3>" . self::h( 'Timing points:' ) . "</h3>";
        $html .= "<table id='timingpoints' title='Timing point stats'><tr><th>" . self::h( 'Checkpoint' ) . "</th><th>" . self::h( 'Start (sec)' )
               . "</th><th>" . self::h( 'Duration (sec)' ) . "</th><th>" . self::h( 'Memory at start (KB)' ) . "</th><th>" . self::h( 'Memory used (KB)' ) . "</th></tr>";
        for ( $i = 0, $l = count( $debug->TimePoints ); $i < $l; ++$i )
        {
            $point = $debug->TimePoints[$i];
            $next = isset( $debug->TimePoints[$i + 1] ) ? $debug->TimePoints[$i + 1] : false;
            $relElapsed = '&nbsp;';
            $relMemory = '&nbsp;';
            if ( $next !== false )
            {
                $relElapsed = number_format( $next['Time'] - $point['Time'], $acc );
                $relMemory = number_format( ( $next['MemoryUsage'] - $point['MemoryUsage'] ) / 1024, $acc );
            }
            $html .= "<tr class='data'><td>" . $point['Description'] . "</td>"
                   . "<td class='num'>" . number_format( $point['Time'] - $startTime, $acc ) . "</td><td class='num'>$relElapsed</td>"
                   . "<td class='num'>" . number_format( $point['MemoryUsage'] / 1024, $acc ) . "</td><td class='num'>$relMemory</td></tr>";
        }
        return $html . "</table></div>";
    }

    /** The accumulator groups, as the classic report printed them. */
    protected static function accumulatorGroups( eZDebug $debug )
    {
        $timeList = $debug->TimeAccumulatorList;
        $groupList = array();
        foreach ( (array)$debug->TimeAccumulatorGroupList as $groupKey => $keyList )
        {
            if ( count( $keyList ) == 0 and !array_key_exists( $groupKey, $timeList ) )
                continue;
            $groupList[$groupKey] = array( 'name' => $groupKey );
            if ( array_key_exists( $groupKey, $timeList ) )
            {
                if ( $timeList[$groupKey]['time'] != 0 )
                    $groupList[$groupKey]['time_data'] = $timeList[$groupKey];
                $groupList[$groupKey]['name'] = $timeList[$groupKey]['name'];
                unset( $timeList[$groupKey] );
            }
            $children = array();
            foreach ( $keyList as $timeKey )
            {
                if ( array_key_exists( $timeKey, $timeList ) )
                {
                    $children[] = $timeList[$timeKey];
                    unset( $timeList[$timeKey] );
                }
            }
            $groupList[$groupKey]['children'] = $children;
        }
        if ( count( $timeList ) > 0 )
            $groupList['general'] = array( 'name' => 'General', 'children' => $timeList );
        return $groupList;
    }

    protected static function accumulatorRowsHtml( eZDebug $debug, array $groupList, $totalElapsed )
    {
        $acc = $debug->TimingAccuracy;
        $pacc = $debug->PercentAccuracy;
        $html = '';
        foreach ( $groupList as $group )
        {
            $children = $group['children'];
            if ( count( $children ) == 0 and !array_key_exists( 'time_data', $group ) )
                continue;
            $html .= "<tr class='group'><td class='groupname'>{$group['name']}</td>";
            if ( array_key_exists( 'time_data', $group ) )
            {
                $g = $group['time_data'];
                $html .= "<td class='num'>" . number_format( $g['time'], $acc ) . "</td>"
                       . "<td class='num'>" . number_format( $g['time'] * 100.0 / $totalElapsed, 1 ) . "</td>"
                       . "<td class='num'>{$g['count']}</td>"
                       . "<td class='num'>" . number_format( $g['count'] ? $g['time'] / $g['count'] : 0, $acc ) . "</td>";
            }
            else
            {
                $html .= "<td></td><td></td><td></td><td></td>";
            }
            $html .= "</tr>";
            foreach ( $children as $child )
            {
                $avg = $child['count'] > 0 ? $child['time'] / $child['count'] : 0.0;
                $html .= "<tr class='data'><td>{$child['name']}</td>"
                       . "<td class='num'>" . number_format( $child['time'], $acc ) . "</td>"
                       . "<td class='num'>" . number_format( $child['time'] * 100.0 / $totalElapsed, $pacc ) . "</td>"
                       . "<td class='num'>{$child['count']}</td>"
                       . "<td class='num'>" . number_format( $avg, $pacc ) . "</td></tr>";
            }
        }
        return $html;
    }

    protected static function accumulatorsHead( $id, $title )
    {
        return "<table id='$id' title='" . htmlspecialchars( $title, ENT_QUOTES ) . "'><tr><th>&nbsp;" . self::h( 'Accumulator' ) . "</th><th>&nbsp;" . self::h( 'Duration (sec)' )
             . "</th><th>&nbsp;" . self::h( 'Duration (%)' ) . "</th><th>&nbsp;" . self::h( 'Count' ) . "</th><th>&nbsp;" . self::h( 'Average (sec)' ) . "</th></tr>";
    }

    protected static function accumulatorsHtml( eZDebug $debug, $totalElapsed )
    {
        $html = "<div id='time-accumulators'><h3>" . self::h( 'Time accumulators:' ) . "</h3>";
        $html .= self::accumulatorsHead( 'timeaccumulators', 'Detailed list of time accumulators' );
        $html .= self::accumulatorRowsHtml( $debug, self::accumulatorGroups( $debug ), $totalElapsed );
        $html .= "<tr><td colspan=\"5\">" . self::h( 'Note: percentages do not add up to 100% because some accumulators overlap' ) . "</td></tr>";
        return $html . "</table></div>";
    }

    protected static function sqlHtml( eZDebug $debug, eZINI $ini, $dbType, $sqlCount, $sqlMs, array $sqlRows, $totalElapsed )
    {
        $html = '<table class="exp-debug-facts"><tr><th>' . self::h( 'Queries' ) . '</th><td>' . (int)$sqlCount . '</td></tr>'
              . '<tr><th>' . self::h( 'Time in queries' ) . '</th><td>' . number_format( $sqlMs, 2 ) . ' ms (' . number_format( $sqlMs / 10 / $totalElapsed, 1 ) . ' %)</td></tr>'
              . '<tr><th>' . self::h( 'Average' ) . '</th><td>' . ( $sqlCount ? number_format( $sqlMs / $sqlCount, 3 ) : '0' ) . ' ms</td></tr>'
              . '<tr><th>' . self::h( 'Database' ) . '</th><td>' . htmlspecialchars( $dbType, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '</td></tr></table>';

        // The database's own accumulators (connection, queries, loops, conversion).
        $groups = array();
        foreach ( self::accumulatorGroups( $debug ) as $key => $group )
        {
            if ( strpos( (string)$key, $dbType ) === 0 || $key === 'querycache_total' )
                $groups[$key] = $group;
        }
        if ( $groups )
        {
            $html .= self::accumulatorsHead( 'exp-debug-sql-accumulators', 'Database time accumulators' );
            $html .= self::accumulatorRowsHtml( $debug, $groups, $totalElapsed ) . '</table>';
        }

        if ( !$sqlRows )
        {
            $html .= '<p class="exp-debug-hint">' . ( $ini->variable( 'DatabaseSettings', 'SQLOutput' ) == 'enabled'
                ? self::h( 'No statements were recorded for this page.' )
                : self::h( 'The statements are not recorded. Turn on "SQL output" (DatabaseSettings SQLOutput) in the Settings tab.' ) ) . '</p>';
            return $html;
        }
        $slowest = $sqlRows;
        usort( $slowest, function( $a, $b ) { return $b['ms'] <=> $a['ms']; } );
        $html .= '<p class="exp-debug-hint">' . self::h( 'Slowest: %ms ms (statement %n)', array( '%ms' => number_format( $slowest[0]['ms'], 3 ), '%n' => $slowest[0]['n'] ) ) . '</p>';
        $html .= '<div class="exp-debug-sql-tools"></div>';
        $html .= "<table id='exp-debug-sql-table' class='exp-debug-messages'>";
        foreach ( $sqlRows as $row )
            $html .= str_replace( "<tr class='", "<tr data-ms='" . $row['ms'] . "' data-n='" . $row['n'] . "' class='", $row['html'] );
        return $html . '</table>';
    }

    protected static function includedFilesHtml( $useIncludedFiles )
    {
        $phpFiles = get_included_files();
        if ( !$useIncludedFiles )
        {
            return '<p class="exp-debug-hint">' . self::h( '%count PHP files were included. Turn on "Display included files" (DebugSettings DisplayIncludedFiles) in the Settings tab to list them.', array( '%count' => count( $phpFiles ) ) ) . '</p>';
        }
        $html = "<div id='included-files'><h3>" . self::h( 'Included files:' ) . "</h3><table id=\"debug_includes\" title='List of included php files used in the processing of this page'><tr><th>" . self::h( 'File' ) . "</th></tr>";
        $currentPathReg = preg_quote( realpath( "." ), '#' );
        foreach ( $phpFiles as $phpFile )
        {
            if ( preg_match( "#^$currentPathReg/(.+)$#", $phpFile, $matches ) )
                $phpFile = $matches[1];
            $html .= "<tr class='data'><td>" . htmlspecialchars( $phpFile, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . "</td></tr>";
        }
        $html .= "<tr class='summary'><td>" . self::h( 'Number of files included: %count', array( '%count' => count( $phpFiles ) ) ) . "</td></tr>";
        return $html . "</table></div>";
    }

    protected static function memoryHtml( eZDebug $debug, $totalElapsed, $peakMemory, $memoryLimit, $sqlCount, $queryKey )
    {
        // The classic "Main resources" table, unchanged.
        $html = "<div id='main-resources'><h3>" . self::h( 'Main resources:' ) . "</h3>";
        $html .= "<table id='debug_resources' title='Most important resource consumption indicators'>";
        $html .= "<tr class='data'><td>" . self::h( 'Total runtime' ) . "</td><td>" . number_format( $totalElapsed, $debug->TimingAccuracy ) . " sec</td></tr>";
        if ( $peakMemory )
            $html .= "<tr class='data'><td>" . self::h( 'Peak memory usage' ) . "</td><td>" . number_format( $peakMemory / 1024, $debug->TimingAccuracy ) . " KB</td></tr>";
        if ( isset( $debug->TimeAccumulatorList[$queryKey] ) )
            $html .= "<tr class='data'><td>" . self::h( 'Database Queries' ) . "</td><td>" . $sqlCount . "</td></tr>";
        $html .= "</table></div>";

        $html .= '<table class="exp-debug-facts">';
        $html .= '<tr><th>' . self::h( 'Memory now' ) . '</th><td>' . number_format( memory_get_usage( true ) / 1048576, 1 ) . ' MB</td></tr>';
        $html .= '<tr><th>' . self::h( 'Peak memory' ) . '</th><td>' . number_format( $peakMemory / 1048576, 1 ) . ' MB</td></tr>';
        $html .= '<tr><th>' . self::h( 'Memory limit' ) . '</th><td>' . ( $memoryLimit > 0
            ? number_format( $memoryLimit / 1048576, 0 ) . ' MB (' . number_format( $peakMemory * 100 / $memoryLimit, 1 ) . ' %)'
            : self::h( 'none' ) ) . '</td></tr>';
        $html .= '</table>';

        // Where the memory went: the timing points that used the most.
        $steps = array();
        for ( $i = 0, $l = count( $debug->TimePoints ) - 1; $i < $l; ++$i )
        {
            $steps[] = array( $debug->TimePoints[$i]['Description'], $debug->TimePoints[$i + 1]['MemoryUsage'] - $debug->TimePoints[$i]['MemoryUsage'] );
        }
        usort( $steps, function( $a, $b ) { return $b[1] <=> $a[1]; } );
        $steps = array_slice( array_filter( $steps, function( $s ) { return $s[1] > 0; } ), 0, 5 );
        if ( $steps )
        {
            $html .= '<h3>' . self::h( 'Largest steps' ) . '</h3><table class="exp-debug-facts">';
            foreach ( $steps as $step )
                $html .= '<tr><th>' . $step[0] . '</th><td>' . number_format( $step[1] / 1024, 0 ) . ' KB</td></tr>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * What this process can tell about itself, for engineFacts(): the SAPI, Velocity's version (QBIX_SERVER_VERSION
     * is defined only in a Velocity process), the process id, the command line of the parent process (a Velocity
     * worker is forked by the Velocity parent or its zygote, both named qbixserver; the parent itself is not) and
     * the time the report is rendered.
     */
    public static function engineProbe()
    {
        $parentPid = function_exists( 'posix_getppid' ) ? (int)posix_getppid() : 0;
        $parentCmd = '';
        if ( $parentPid > 1 && @is_readable( '/proc/' . $parentPid . '/cmdline' ) )
            $parentCmd = trim( str_replace( "\0", ' ', (string)@file_get_contents( '/proc/' . $parentPid . '/cmdline' ) ) );
        $velocity = null;
        if ( defined( 'QBIX_SERVER_VERSION' ) )
            $velocity = function_exists( 'qbix_version_label' ) ? (string)qbix_version_label() : 'v' . QBIX_SERVER_VERSION;
        return array(
            'sapi'       => PHP_SAPI,
            'velocity'   => $velocity,
            'pid'        => (int)getmypid(),
            'parent_pid' => $parentPid,
            'parent_cmd' => $parentCmd,
            'time'       => microtime( true ),
        );
    }

    /**
     * Where the request entered and which process rendered this report, from the server variables and the probe
     * (engineProbe()); no state, so a test passes both.
     *
     * The signals are the ones a client cannot set: the SAPI and QBIX_SERVER_VERSION belong to the process,
     * SERVER_SOFTWARE and SERVER_PORT to the server that ran the script. Velocity answers every request itself (a
     * worker, its parent, or php-cgi for a script under Q.webserver.cgi.patterns); it never hands a request on to
     * PHP-FPM or Apache, so a report rendered by PHP-FPM was rendered for a request that went to Apache. Forwarded
     * headers (X-Forwarded-For and the like) are listed as seen, as a client can send them too.
     *
     * @return array entry (scheme, port, host, forwarded), receiver, renderer (velocity-worker, velocity-parent,
     *         velocity-cgi, php-fpm, mod-php, frankenphp, cli, other), velocity (bool: rendered by Velocity),
     *         sentence (English source) and arguments (for t()), received_by, rendered_by, pid, rendered_at
     */
    public static function engineFacts( array $server, array $probe )
    {
        $sapi = isset( $probe['sapi'] ) ? (string)$probe['sapi'] : PHP_SAPI;
        $velocity = isset( $probe['velocity'] ) && $probe['velocity'] !== null && $probe['velocity'] !== '' ? (string)$probe['velocity'] : null;
        $software = isset( $server['SERVER_SOFTWARE'] ) ? trim( (string)$server['SERVER_SOFTWARE'] ) : '';
        $softwareIsVelocity = stripos( $software, 'QbixServer' ) === 0;

        $https = ( isset( $server['HTTPS'] ) && $server['HTTPS'] !== '' && strtolower( (string)$server['HTTPS'] ) !== 'off' )
              || ( isset( $server['REQUEST_SCHEME'] ) && strtolower( (string)$server['REQUEST_SCHEME'] ) === 'https' );
        $port = isset( $server['SERVER_PORT'] ) && ctype_digit( (string)$server['SERVER_PORT'] ) ? (int)$server['SERVER_PORT'] : ( $https ? 443 : 80 );
        $forwarded = array();
        foreach ( array( 'HTTP_FORWARDED' => 'Forwarded', 'HTTP_X_FORWARDED_FOR' => 'X-Forwarded-For', 'HTTP_X_FORWARDED_PROTO' => 'X-Forwarded-Proto',
                         'HTTP_X_FORWARDED_HOST' => 'X-Forwarded-Host', 'HTTP_X_REAL_IP' => 'X-Real-IP', 'HTTP_VIA' => 'Via' ) as $key => $name )
        {
            if ( isset( $server[$key] ) && (string)$server[$key] !== '' )
                $forwarded[] = $name;
        }

        // Which server took the request: the one that set SERVER_SOFTWARE ("Apache", "nginx/1.2", "QbixServer/1.0")
        $receivedBy = $software !== '' ? preg_replace( '#/.*$#', '', $software ) : '';
        if ( $velocity !== null || $softwareIsVelocity )
            $receivedBy = 'Exponential Velocity' . ( $velocity !== null ? ' ' . $velocity : '' );

        $pid = isset( $probe['pid'] ) ? (int)$probe['pid'] : 0;
        $args = array( '%port' => (string)$port, '%server' => $receivedBy !== '' ? $receivedBy : $sapi, '%sapi' => $sapi );
        if ( $velocity !== null && $sapi === 'cli' )
        {
            $isWorker = isset( $probe['parent_cmd'] ) && stripos( (string)$probe['parent_cmd'], 'qbixserver' ) !== false;
            $renderer = $isWorker ? 'velocity-worker' : 'velocity-parent';
            $renderedBy = $isWorker ? 'Velocity worker' : 'Velocity parent process';
            $sentence = $isWorker ? 'Received by Velocity on port %port and rendered by a Velocity worker.'
                                  : 'Received by Velocity on port %port and rendered by the Velocity parent process (no worker).';
        }
        else if ( $softwareIsVelocity && ( $sapi === 'cgi-fcgi' || $sapi === 'cgi' ) )
        {
            $renderer = 'velocity-cgi';
            $renderedBy = 'php-cgi';
            $sentence = 'Received by Velocity on port %port and rendered by php-cgi, which Velocity starts for this script (Q.webserver.cgi.patterns).';
        }
        else if ( $sapi === 'cli' || $sapi === 'phpdbg' )
        {
            $renderer = 'cli';
            $renderedBy = 'PHP ' . $sapi;
            $sentence = 'Rendered on the command line (%sapi), not for a web request.';
        }
        else if ( $sapi === 'fpm-fcgi' )
        {
            $renderer = 'php-fpm';
            $renderedBy = 'PHP-FPM';
            $sentence = $receivedBy !== '' ? 'Served by %server / PHP-FPM (port %port), not by Velocity.' : 'Served by PHP-FPM (port %port), not by Velocity.';
        }
        else if ( $sapi === 'apache2handler' )
        {
            $renderer = 'mod-php';
            $renderedBy = 'mod_php';
            $sentence = 'Served by Apache with mod_php (port %port), not by Velocity.';
        }
        else if ( $sapi === 'frankenphp' )
        {
            $renderer = 'frankenphp';
            $renderedBy = 'FrankenPHP';
            $sentence = 'Served by FrankenPHP (port %port), not by Velocity.';
        }
        else
        {
            $renderer = 'other';
            $renderedBy = 'PHP ' . $sapi;
            $sentence = 'Served by %server (%sapi, port %port), not by Velocity.';
        }

        return array(
            'entry'       => array( 'scheme' => $https ? 'https' : 'http', 'port' => $port,
                                    'host' => isset( $server['HTTP_HOST'] ) ? (string)$server['HTTP_HOST'] : '', 'forwarded' => $forwarded ),
            'renderer'    => $renderer,
            'velocity'    => strpos( $renderer, 'velocity-' ) === 0,
            'received_by' => $receivedBy !== '' ? $receivedBy : $sapi,
            'rendered_by' => $renderedBy,
            'sentence'    => $sentence,
            'arguments'   => $args,
            'pid'         => $pid,
            'rendered_at' => isset( $probe['time'] ) ? (float)$probe['time'] : microtime( true ),
        );
    }

    protected static function velocityHtml( $privileged = false )
    {
        $facts = self::engineFacts( $_SERVER, self::engineProbe() );
        $entry = $facts['entry'];
        $entered = $entry['scheme'] . ', ' . self::t( 'port %port', array( '%port' => $entry['port'] ) );
        if ( $entry['forwarded'] )
            $entered .= '; ' . self::t( 'forwarded headers: %headers', array( '%headers' => implode( ', ', $entry['forwarded'] ) ) );

        $rows = array();
        $rows[self::t( 'Entered' )] = $entered;
        $rows[self::t( 'Received by' )] = $facts['received_by'];
        $rows[self::t( 'Rendered by' )] = self::t( $facts['rendered_by'] );
        // the version only for the privileged: the tab shows to anonymous visitors too when DebugByIP matches
        $rows['PHP'] = $privileged ? PHP_VERSION . ' (' . PHP_SAPI . ')' : PHP_SAPI;
        if ( $privileged )
        {
            $rows[self::t( 'Process' )] = (string)$facts['pid'];
            $rows[self::t( 'Host' )] = function_exists( 'gethostname' ) ? (string)gethostname() : '';
        }
        $rows[self::t( 'Rendered at' )] = date( 'Y-m-d H:i:s T', (int)$facts['rendered_at'] );
        $rows[self::t( 'Cached' )] = self::t( 'No: a page with this report is rendered for each request and kept by no cache (Cache-Control: private, no-store).' );

        // The script compares these with the address the browser shows and the time the page was loaded: a page
        // replayed from a cache (or by the browser) carries the report of the request that rendered it.
        $html = '<div class="exp-debug-engine" data-renderer="' . self::a( $facts['renderer'] ) . '" data-velocity="' . ( $facts['velocity'] ? '1' : '0' )
              . '" data-entry-port="' . (int)$entry['port'] . '" data-rendered-at="' . self::a( sprintf( '%.3F', $facts['rendered_at'] ) )
              . '" data-rendered-by="' . self::a( self::t( $facts['rendered_by'] ) . ( $privileged ? ' ' . $facts['pid'] : '' ) ) . '">';
        $html .= '<p class="exp-debug-hint' . ( $facts['velocity'] ? ' exp-debug-ok' : '' ) . '">' . self::h( $facts['sentence'], $facts['arguments'] ) . '</p>';
        $html .= '<table class="exp-debug-facts">';
        foreach ( $rows as $k => $v )
            $html .= '<tr><th>' . self::a( $k ) . '</th><td>' . self::a( $v ) . '</td></tr>';
        $html .= '</table></div>';

        if ( $privileged )
        {
            $html .= '<h3>' . self::h( 'OPcache' ) . '</h3>' . self::opcacheHtml();
            $html .= '<h3>' . self::h( 'APCu' ) . '</h3>' . self::apcuHtml();
            $html .= '<div class="exp-debug-velocity-cache"></div>';
        }
        return $html;
    }

    /** Longest value the Rendering tab shows in full; a longer one is cut, the whole value is its tooltip */
    const RENDERING_VALUE_MAX = 200;

    /**
     * What the Rendering tab shows: what expRenderStats noted for this request and the settings that decide it.
     *
     * @return array|null views, view_cache, templates, siteaccess, navigation_part, design, settings; null when the
     *                    kernel's collector is not loaded
     */
    public static function renderingData()
    {
        if ( !class_exists( 'expRenderStats' ) )
            return null;
        $data = array( 'views' => expRenderStats::views(), 'view_cache' => expRenderStats::viewCache(),
                       'templates' => expRenderStats::templates(),
                       'cache_blocks' => method_exists( 'expRenderStats', 'cacheBlocks' ) ? expRenderStats::cacheBlocks() : array(),
                       'cache_block_totals' => method_exists( 'expRenderStats', 'cacheBlockTotals' ) ? expRenderStats::cacheBlockTotals() : null,
                       'tree' => method_exists( 'expRenderStats', 'tree' ) ? expRenderStats::tree() : array(),
                       'tree_count' => method_exists( 'expRenderStats', 'treeCount' ) ? expRenderStats::treeCount() : 0,
                       'siteaccess' => '', 'navigation_part' => '',
                       'design' => array(), 'settings' => array() );
        if ( isset( $GLOBALS['eZCurrentAccess']['name'] ) )
            $data['siteaccess'] = (string)$GLOBALS['eZCurrentAccess']['name'];
        try
        {
            $ini = eZINI::instance();
            foreach ( array( 'ContentSettings' => array( 'ViewCaching' ),
                             'TemplateSettings' => array( 'TemplateCompile', 'TemplateCache', 'CompileExecution', 'DevelopmentMode' ) ) as $group => $names )
            {
                foreach ( $names as $name )
                    $data['settings'][$name] = $ini->hasVariable( $group, $name ) ? (string)$ini->variable( $group, $name ) : '';
            }
            if ( class_exists( 'eZTemplateDesignResource' ) )
            {
                foreach ( (array)eZTemplateDesignResource::allDesignBases() as $base )
                    $data['design'][] = (string)$base;
            }
            if ( class_exists( 'eZTemplate', false ) )
            {
                $tpl = eZTemplate::factory();
                if ( $tpl->hasVariable( 'module_result' ) )
                {
                    $result = $tpl->variable( 'module_result' );
                    if ( isset( $result['navigation_part'] ) && is_scalar( $result['navigation_part'] ) )
                        $data['navigation_part'] = (string)$result['navigation_part'];
                }
            }
        }
        catch ( Throwable $e )
        {
        }
        return $data;
    }

    /**
     * The chips of the Rendering tab: what the content view cache did, and how many templates were compiled now.
     *
     * @param array $r renderingData()
     * @return array chips as summaryHtml() takes them
     */
    public static function renderingChips( array $r )
    {
        $chips = array();
        $cache = $r['view_cache'];
        if ( is_array( $cache ) && $cache['state'] !== null )
        {
            $words = array( 'hit' => self::t( 'View: hit' ), 'generated' => self::t( 'View: new' ), 'uncached' => self::t( 'View: new' ),
                            'expired' => self::t( 'View: new' ), 'off' => self::t( 'View: off' ), 'pending' => self::t( 'View: ?' ) );
            $state = isset( $words[$cache['state']] ) ? $cache['state'] : 'pending';
            $chips[] = array( 'rendering', 'viewcache', $state === 'hit' || $state === 'off' ? 'ok' : 'warn', $words[$state],
                              self::t( 'Content view cache' ) . ': ' . self::renderingStateText( $cache ) );
        }
        $totals = isset( $r['cache_block_totals'] ) ? $r['cache_block_totals'] : null;
        if ( $totals && $totals['count'] > 0 )
        {
            $misses = $totals['count'] - $totals['hits'];
            $chips[] = array( 'rendering', 'cacheblocks', $misses > 0 ? 'warn' : 'ok',
                              self::t( 'CB %hits/%count', array( '%hits' => $totals['hits'], '%count' => $totals['count'] ) ),
                              self::t( 'Cache blocks: %hits of %count read from the cache, %generated ms to generate the others (outermost blocks)',
                                       array( '%hits' => $totals['hits'], '%count' => $totals['count'],
                                              '%generated' => number_format( $totals['generate_ms'], 0, '.', '' ) ) ) );
        }
        $compiled = count( $r['templates']['compiled'] );
        $chips[] = array( 'rendering', 'compiled', $compiled > 0 ? 'warn' : 'ok', self::t( 'Compiled %count', array( '%count' => $compiled ) ),
                          self::t( '%compiled templates compiled in this request, %executed runs of compiled templates',
                                   array( '%compiled' => $compiled, '%executed' => (int)$r['templates']['executed'] ) ) );
        return $chips;
    }

    /** The content view cache's state as a sentence, with its reason. */
    protected static function renderingStateText( array $cache )
    {
        $texts = array( 'hit' => 'Read from the cache',
                        'generated' => 'Generated and stored in the cache',
                        'uncached' => 'Generated without storing it in the cache',
                        'expired' => 'The cache file was not used',
                        'off' => 'Not used',
                        'pending' => 'The key was built, no outcome was noted' );
        $text = self::t( isset( $texts[$cache['state']] ) ? $texts[$cache['state']] : $texts['pending'] );
        if ( $cache['reason'] !== '' )
            $text .= ' (' . $cache['reason'] . ')';
        return $text;
    }

    /**
     * The Rendering tab: the controller, the content view cache and its key, and the template compilation.
     *
     * @param array $r renderingData()
     * @return string
     */
    public static function renderingHtml( array $r )
    {
        $html = '<h3>' . self::h( 'Controller' ) . '</h3>';
        $html .= '<p class="exp-debug-hint exp-debug-render-explain">' . self::h( 'The module views that ran for this request, in order (an error page or a redirect adds one), with the class that ran each view and its script; below them the siteaccess, the navigation part and the design bases in the order templates are looked up.' ) . '</p>';
        if ( !$r['views'] )
            $html .= '<p class="exp-debug-hint">' . self::h( 'No module view ran.' ) . '</p>';
        else
        {
            $html .= '<table class="exp-debug-facts"><tr><th>' . self::h( 'Module view' ) . '</th><th>' . self::h( 'View class' )
                   . '</th><th>' . self::h( 'Script' ) . '</th></tr>';
            foreach ( $r['views'] as $view )
            {
                $html .= '<tr><td>' . self::a( $view['module'] . '/' . $view['view'] ) . '</td><td>'
                       . ( $view['class'] === null ? '-' : self::a( $view['class'] ) ) . '</td><td>' . self::renderingValue( self::relativePath( $view['script'] ) ) . '</td></tr>';
            }
            $html .= '</table>';
        }
        $rows = array( self::t( 'Siteaccess' ) => $r['siteaccess'],
                       self::t( 'Navigation part' ) => $r['navigation_part'] );
        $html .= '<table class="exp-debug-facts">';
        foreach ( $rows as $k => $v )
            $html .= '<tr><th>' . self::a( $k ) . '</th><td>' . ( $v === '' ? '-' : self::renderingValue( $v ) ) . '</td></tr>';
        $html .= '</table>';
        if ( $r['design'] )
        {
            // the first base that has a template wins
            $html .= '<details class="exp-debug-render-design"><summary>' . self::h( 'Design order (%count bases)', array( '%count' => count( $r['design'] ) ) ) . '</summary><ol>';
            foreach ( $r['design'] as $base )
                $html .= '<li>' . self::renderingValue( self::relativePath( $base ) ) . '</li>';
            $html .= '</ol></details>';
        }

        // the tree right after the controller: what rendered the page, open
        $html .= self::templateTreeHtml( isset( $r['tree'] ) ? $r['tree'] : array(), isset( $r['tree_count'] ) ? (int)$r['tree_count'] : 0 );

        $html .= '<h3>' . self::h( 'Content view cache' ) . '</h3>';
        $html .= '<p class="exp-debug-hint exp-debug-render-explain">' . self::h( 'Whether the content view (content/view) was read from its cache file or generated, and why. The key decides the file: two requests with the same parts share it. Lookup is the time to find and read the file, generation the time to render the view.' ) . '</p>';
        $cache = $r['view_cache'];
        if ( !is_array( $cache ) || $cache['state'] === null )
            $html .= '<p class="exp-debug-hint">' . self::h( 'No content view ran on this page.' ) . '</p>';
        else
        {
            // laid out like the cache blocks below: one row, the key folded in the last column
            $cache += array( 'node_id' => null, 'view_mode' => null, 'lookup_ms' => null, 'generate_ms' => null );
            $node = isset( $cache['parts']['node_id'] ) ? $cache['parts']['node_id'] : $cache['node_id'];
            $mode = isset( $cache['parts']['viewmode'] ) ? $cache['parts']['viewmode'] : $cache['view_mode'];
            $view = $node === null ? '-' : self::renderingValue( self::t( 'node %node', array( '%node' => (int)$node ) ) . ( $mode === null ? '' : ', ' . $mode ) );
            $keys = '-';
            if ( $cache['parts'] )
            {
                $keys = '<details class="exp-debug-render-keys"><summary>' . self::h( 'Cache key (%count parts)', array( '%count' => count( $cache['parts'] ) ) ) . '</summary>';
                $keys .= '<table class="exp-debug-facts">';
                foreach ( $cache['parts'] as $name => $value )
                {
                    $mark = in_array( $name, $cache['from_listeners'], true )
                        ? ' <span class="exp-debug-badge" title="' . self::h( 'Added or changed by a content/view/cachekeys listener' ) . '">' . self::h( 'listener' ) . '</span>' : '';
                    $shown = $value === null ? '' : ( is_bool( $value ) ? ( $value ? '1' : '' ) : (string)$value );
                    $keys .= '<tr><th>' . self::a( $name ) . $mark . '</th><td>' . ( $shown === '' ? '<em>' . self::h( 'empty' ) . '</em>' : self::renderingValue( $shown ) ) . '</td></tr>';
                }
                $keys .= '</table>';
                if ( $cache['path'] !== null )
                    $keys .= self::h( 'Hash' ) . ' <code>' . self::a( $cache['hash'] ) . '</code><br><code>' . self::renderingValue( self::relativePath( $cache['path'] ) ) . '</code>';
                $keys .= '</details>';
            }
            $ms = function ( $value ) { return $value === null ? '-' : number_format( $value, 1, '.', '' ) . ' ms'; };
            $html .= '<table class="exp-debug-facts exp-debug-viewcache"><tr><th>' . self::h( 'View' ) . '</th><th>' . self::h( 'State' )
                   . '</th><th>' . self::h( 'Lookup' ) . '</th><th>' . self::h( 'Generation' ) . '</th><th>' . self::h( 'Size' )
                   . '</th><th>' . self::h( 'Age' ) . '</th><th>' . self::h( 'Keys' ) . '</th></tr>';
            $html .= '<tr data-state="' . self::a( $cache['state'] ) . '"><td>' . $view . '</td><td>' . self::renderingValue( self::renderingStateText( $cache ) )
                   . '</td><td>' . $ms( $cache['lookup_ms'] ) . '</td><td>' . $ms( $cache['generate_ms'] ) . '</td><td>'
                   . ( $cache['size'] === null ? '-' : number_format( $cache['size'] / 1024, 1 ) . ' KB' ) . '</td><td>'
                   . ( $cache['age'] === null ? '-' : self::h( '%seconds s', array( '%seconds' => (int)$cache['age'] ) ) ) . '</td><td>' . $keys . '</td></tr>';
            $html .= '</table>';
        }

        $html .= self::cacheBlocksHtml( isset( $r['cache_blocks'] ) ? $r['cache_blocks'] : array(),
                                        isset( $r['cache_block_totals'] ) ? $r['cache_block_totals'] : null );

        $html .= '<h3>' . self::h( 'Templates' ) . '</h3>';
        $html .= '<p class="exp-debug-hint exp-debug-render-explain">' . self::h( 'The settings that decide caching and compiling, and how many templates ran from the compiled cache or were compiled now. Right after a template cache clear every template is compiled once.' ) . '</p>';
        $settings = $r['settings'];
        $html .= '<table class="exp-debug-facts">';
        foreach ( array( 'ViewCaching', 'TemplateCompile', 'CompileExecution', 'TemplateCache', 'DevelopmentMode' ) as $name )
        {
            if ( isset( $settings[$name] ) )
                $html .= '<tr><th>' . self::a( $name ) . '</th><td>' . self::a( $settings[$name] ) . '</td></tr>';
        }
        $compiled = $r['templates']['compiled'];
        $html .= '<tr><th>' . self::h( 'Templates rendered' ) . '</th><td>' . ( empty( $r['tree_count'] ) ? '-' : (int)$r['tree_count'] ) . '</td></tr>';
        $html .= '<tr><th>' . self::h( 'Compiled templates run' ) . '</th><td>' . (int)$r['templates']['executed'] . '</td></tr>';
        $html .= '<tr><th>' . self::h( 'Compiled in this request' ) . '</th><td>' . count( $compiled ) . '</td></tr>';
        $html .= '</table>';
        if ( $compiled )
        {
            $html .= '<details class="exp-debug-render-compiled"><summary>' . self::h( 'Templates compiled in this request' ) . '</summary><ul>';
            foreach ( $compiled as $file )
                $html .= '<li>' . self::renderingValue( self::relativePath( $file ) ) . '</li>';
            $html .= '</ul></details>';
        }
        return $html;
    }

    /**
     * The template tree of the Rendering tab: every template in the order it started, nested under the template
     * that included it, with the file the overrides chose and the time it took including what it included.
     *
     * @param array $tree expRenderStats::tree()
     * @param int $count expRenderStats::treeCount()
     * @return string
     */
    protected static function templateTreeHtml( array $tree, $count )
    {
        if ( !$tree )
            return '';
        $html = '<h3>' . self::h( 'Template tree' ) . '</h3>';
        $html .= '<p class="exp-debug-hint exp-debug-render-explain">' . self::h( 'Every template this request rendered, in the order it started, nested under the template that included it. An arrow names the file an override chose; otherwise point at a name to see its file. A time holds everything the template included, not the parsing or compiling of the template itself. Templates of a view read from the view cache are not rendered and not listed.' ) . '</p>';
        $html .= '<details class="exp-debug-render-tree" open><summary>' . self::h( '%count templates', array( '%count' => $count ) ) . '</summary>';
        if ( count( $tree ) < $count )
            $html .= '<p class="exp-debug-hint">' . self::h( 'The first %shown are listed.', array( '%shown' => count( $tree ) ) ) . '</p>';
        $html .= '<ul class="exp-debug-tree">';
        $depth = 0;
        $open = false;
        // the recorded depths of the entries above the current one: a template left on the stack by an exception that
        // was caught makes what follows deeper than its place, so the level in the list comes from this stack
        $above = array();
        foreach ( $tree as $entry )
        {
            while ( $above && end( $above ) >= (int)$entry['depth'] )
                array_pop( $above );
            $entryDepth = count( $above );
            $above[] = (int)$entry['depth'];
            // preorder with depths: open a list for a child, close lists back to the entry's depth
            if ( $open && $entryDepth > $depth )
            {
                $html .= '<ul>';
                $depth = $entryDepth;
            }
            else
            {
                if ( $open )
                    $html .= '</li>';
                while ( $depth > $entryDepth )
                {
                    $html .= '</ul></li>';
                    $depth--;
                }
            }
            $html .= '<li>' . self::templateTreeLabel( $entry );
            $open = true;
        }
        if ( $open )
            $html .= '</li>';
        while ( $depth > 0 )
        {
            $html .= '</ul></li>';
            $depth--;
        }
        return $html . '</ul></details>';
    }

    /** One template of the tree: the name asked for, the file used when an override chose another, time, compiled */
    protected static function templateTreeLabel( array $entry )
    {
        $design = strpos( $entry['uri'], 'design:' ) === 0;
        $uri = $design ? substr( $entry['uri'], 7 ) : self::relativePath( preg_replace( '/^file:/', '', $entry['uri'] ) );
        $file = $entry['file'] === null ? null : self::relativePath( $entry['file'] );
        // a design template in its own place (not below override/): no override; a file template is its file
        $own = $file === null || $file === $uri
               || ( $design && substr( $file, -strlen( '/templates/' . $uri ) ) === '/templates/' . $uri
                    && strpos( $file, '/override/templates/' ) === false );
        if ( $own )
            $label = '<code' . ( $file === null || $file === $uri ? '' : ' title="' . self::a( $file ) . '"' ) . '>' . self::renderingValue( $uri ) . '</code>';
        else
            $label = '<code>' . self::renderingValue( $uri ) . '</code> &rarr; <span class="exp-debug-render-override">' . self::renderingValue( $file ) . '</span>';
        $label .= ' <span class="exp-debug-render-ms">' . ( $entry['ms'] === null ? '-' : number_format( $entry['ms'], 1, '.', '' ) . ' ms' ) . '</span>';
        if ( !$entry['compiled'] )
            $label .= ' <span class="exp-debug-badge">' . self::h( 'not compiled' ) . '</span>';
        return $label;
    }

    /**
     * The cache blocks of the Rendering tab: one row per {cache-block} in the order they were looked up.
     *
     * @param array $blocks expRenderStats::cacheBlocks()
     * @param array|null $totals expRenderStats::cacheBlockTotals()
     * @return string
     */
    protected static function cacheBlocksHtml( array $blocks, $totals )
    {
        $html = '<h3>' . self::h( 'Cache blocks' ) . '</h3>';
        $html .= '<p class="exp-debug-hint exp-debug-render-explain">' . self::h( 'Every {cache-block} in the order it was looked up. A hit costs only the lookup; a generated block renders its content and stores it for the next request. A block inside a generated block only runs then, so the counts differ between a first and a second request.' ) . '</p>';
        if ( !$totals || $totals['count'] == 0 )
            return $html . '<p class="exp-debug-hint">' . self::h( 'No cache-block ran on this page.' ) . '</p>';
        $html .= '<p class="exp-debug-hint">' . self::h( '%hits of %count read from the cache; %lookup ms to look them up, %generated ms to generate the others (outermost blocks; a row\'s time holds the blocks inside it).',
                    array( '%hits' => $totals['hits'], '%count' => $totals['count'],
                           '%lookup' => number_format( $totals['lookup_ms'], 1, '.', '' ),
                           '%generated' => number_format( $totals['generate_ms'], 1, '.', '' ) ) ) . '</p>';
        if ( count( $blocks ) < $totals['count'] )
            $html .= '<p class="exp-debug-hint">' . self::h( 'The first %shown are listed.', array( '%shown' => count( $blocks ) ) ) . '</p>';
        $states = array( 'hit' => 'hit', 'generated' => 'generated', 'pending' => 'not found' );
        $html .= '<table class="exp-debug-facts exp-debug-cacheblocks"><tr><th>' . self::h( 'Template' ) . '</th><th>' . self::h( 'State' )
               . '</th><th>' . self::h( 'Lookup' ) . '</th><th>' . self::h( 'Generation' ) . '</th><th>' . self::h( 'Size' )
               . '</th><th>' . self::h( 'Expiry' ) . '</th><th>' . self::h( 'Keys' ) . '</th></tr>';
        foreach ( $blocks as $block )
        {
            // a .tpl block by its line, a Smarty {expcacheblock} by its id
            if ( $block['template'] === null )
                $where = '<em>' . self::h( 'unknown (custom code, or a template compiled before)' ) . '</em>';
            else if ( $block['line'] === null && isset( $block['block_id'] ) )
                $where = self::renderingValue( self::relativePath( $block['template'] ) . ' id=' . $block['block_id'] );
            else
                $where = self::renderingValue( self::relativePath( $block['template'] ) . ':' . (int)$block['line'] );
            $expiry = array();
            if ( $block['ttl'] !== null )
                $expiry[] = self::t( '%seconds s', array( '%seconds' => $block['ttl'] ) );
            if ( $block['node_id'] !== null )
                $expiry[] = self::t( 'subtree %node', array( '%node' => $block['node_id'] ) );
            $keys = $block['keys'] === null ? '-' : '<details><summary>' . self::h( 'keys' ) . '</summary>' . self::renderingValue( $block['keys'] )
                  . '<br><code>' . self::renderingValue( self::relativePath( $block['path'] ) ) . '</code></details>';
            $html .= '<tr data-state="' . self::a( $block['state'] ) . '"><td>' . $where . '</td><td>' . self::h( $states[$block['state']] ) . '</td><td>'
                   . number_format( $block['lookup_ms'], 1, '.', '' ) . ' ms</td><td>'
                   . ( $block['generate_ms'] === null ? '-' : number_format( $block['generate_ms'], 1, '.', '' ) . ' ms' ) . '</td><td>'
                   . ( $block['size'] === null ? '-' : number_format( $block['size'] / 1024, 1 ) . ' KB' ) . '</td><td>'
                   . ( $expiry ? self::a( implode( ', ', $expiry ) ) : '-' ) . '</td><td>' . $keys . '</td></tr>';
        }
        return $html . '</table>';
    }

    /** A value of the Rendering tab, escaped; cut after RENDERING_VALUE_MAX characters with the whole value as tooltip. */
    protected static function renderingValue( $value )
    {
        $value = (string)$value;
        $length = function_exists( 'mb_strlen' ) ? mb_strlen( $value, 'UTF-8' ) : strlen( $value );
        if ( $length <= self::RENDERING_VALUE_MAX )
            return self::a( $value );
        $cut = function_exists( 'mb_substr' ) ? mb_substr( $value, 0, self::RENDERING_VALUE_MAX, 'UTF-8' ) : substr( $value, 0, self::RENDERING_VALUE_MAX );
        return '<span title="' . self::a( $value ) . '">' . self::a( $cut ) . '&hellip;</span>';
    }

    /** A path below the installation root without the root, which says nothing to the reader. */
    public static function relativePath( $path )
    {
        static $root = null;
        $path = (string)$path;
        if ( $root === null )
            $root = getcwd();
        if ( $root !== false && $root !== '' && strpos( $path, $root . '/' ) === 0 )
            return substr( $path, strlen( $root ) + 1 );
        return strpos( $path, './' ) === 0 ? substr( $path, 2 ) : $path;
    }

    /** htmlspecialchars() for text and attribute values. */
    protected static function a( $value )
    {
        return htmlspecialchars( (string)$value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 );
    }


    /**
     * The panels extensions register (expDebugBarRegistry::panels(), debugbar.ini Panels[]) that the current user may
     * see, ready to draw: the tab's id "x-<id>" (apart from the built-in tabs), its title, its chip for summaryHtml()
     * or null, its HTML and its data. A panel that throws, or gives a title that is not a string, is left out of
     * this report with a warning, so one extension cannot break the bar.
     *
     * @return array list of array( 'id', 'tab', 'title', 'chip', 'html', 'data' )
     */
    public static function extensionPanels()
    {
        if ( !class_exists( 'expDebugBarRegistry' ) || !interface_exists( 'expDebugBarPanelInterface' ) )
            return array();
        try
        {
            $classes = ( new expDebugBarRegistry() )->panels( $broken );
        }
        catch ( Throwable $e )
        {
            return array();
        }
        foreach ( $broken as $id => $reason )
            eZDebug::writeWarning( "Debug bar panel '$id' left out: $reason", __METHOD__ );

        $panels = array();
        foreach ( $classes as $id => $class )
        {
            try
            {
                $panel = new $class();
                if ( !$panel->visible() )
                    continue;
                $title = $panel->title();
                if ( !is_string( $title ) || $title === '' )
                    throw new UnexpectedValueException( 'title() gave no text' );
                $tab = 'x-' . strtolower( $id );
                $chip = $panel->chip();
                if ( is_array( $chip ) && isset( $chip['text'] ) && (string)$chip['text'] !== '' )
                {
                    $level = isset( $chip['level'] ) && in_array( $chip['level'], array( 'ok', 'warn', 'high' ), true ) ? $chip['level'] : 'ok';
                    $chip = array( $tab, 'panel-' . strtolower( $id ), $level, (string)$chip['text'],
                                   isset( $chip['title'] ) ? (string)$chip['title'] : $title );
                }
                else
                {
                    $chip = null;
                }
                $data = $panel->data();
                $panels[] = array( 'id' => $id, 'tab' => $tab, 'title' => $title, 'chip' => $chip,
                                   'html' => (string)$panel->html(), 'data' => is_array( $data ) ? $data : null );
            }
            catch ( Throwable $e )
            {
                eZDebug::writeWarning( "Debug bar panel '$id' ($class) left out: " . $e->getMessage(), __METHOD__ );
            }
        }
        return $panels;
    }

    /**
     * PHP version, OPcache and APCu of the process that rendered the page, for the summary chips, or null for a
     * viewer who may not see them. OPcache and APCu are null when they are not loaded or not on for this process.
     *
     * @param bool $privileged the viewer may see the privileged rows of the Server tab (setup or cache rights)
     * @return array|null array( 'php' => string, 'sapi' => string, 'exponential' => string|null, 'opcache' => array|null,
     *                    'apcu' => array|null )
     */
    public static function runtimeFigures( $privileged )
    {
        if ( !$privileged )
            return null;
        $opcache = self::opcacheStatus();
        return array( 'php' => PHP_VERSION, 'sapi' => PHP_SAPI,
                      'exponential' => class_exists( 'eZPublishSDK' ) ? eZPublishSDK::version( true, false, false ) : null,
                      'opcache' => is_array( $opcache ) && !empty( $opcache['enabled'] ) ? $opcache : null,
                      'apcu' => self::apcuStatus() );
    }

    /**
     * The chips of runtimeFigures() for OPcache and APCu, with the share of their memory in use; summaryHtml() draws them
     * at the right end before the versions (versionsChip()). A level from $levels (the kernel's summary, which follows debugbar.ini Thresholds[]) wins over
     * the defaults.
     *
     * @param array|null $runtime runtimeFigures()
     * @param array $levels 'opcache' / 'apcu' => ok | warn | high
     * @return array one chip per cache that is on: array( tab, key, level, text, title )
     */
    public static function runtimeChips( $runtime, array $levels = array() )
    {
        if ( !is_array( $runtime ) )
            return array();
        $t = self::memoryThresholds();
        $level = function( $pct, array $limits ) { return $pct >= $limits['high'] ? 'high' : ( $pct >= $limits['warn'] ? 'warn' : 'ok' ); };
        $chips = array();
        $o = $runtime['opcache'];
        if ( is_array( $o ) )
        {
            $size = $o['used'] + $o['free'];
            $pct = $size > 0 ? $o['used'] / $size * 100 : 0;
            $lvl = isset( $levels['opcache'] ) && in_array( $levels['opcache'], array( 'ok', 'warn', 'high' ), true )
                 ? $levels['opcache'] : $level( $pct, $t['opcache_memory'] );
            $chips[] = array( 'velocity', 'opcache', $lvl, self::t( 'OPc %pct %', array( '%pct' => number_format( $pct, 0 ) ) ),
                              self::t( 'OPcache memory: %used of %size MB in use, hit rate %rate %, %scripts scripts, %restarts restarts',
                                       array( '%used' => number_format( $o['used'] / 1048576, 1 ), '%size' => number_format( $size / 1048576, 0 ),
                                              '%rate' => $o['hit_rate'], '%scripts' => $o['scripts'], '%restarts' => $o['restarts'] ) ) );
        }
        $a = $runtime['apcu'];
        if ( is_array( $a ) )
        {
            $pct = $a['size'] > 0 ? $a['used'] / $a['size'] * 100 : 0;
            $lvl = isset( $levels['apcu'] ) && in_array( $levels['apcu'], array( 'ok', 'warn', 'high' ), true )
                 ? $levels['apcu'] : $level( $pct, $t['apcu_memory'] );
            $chips[] = array( 'velocity', 'apcu', $lvl, self::t( 'APCu %pct %', array( '%pct' => number_format( $pct, 0 ) ) ),
                              self::t( 'APCu memory, shared by every site of the PHP pool: %used of %size MB in use, %entries entries, hit rate %rate %, %expunges expunges',
                                       array( '%used' => number_format( $a['used'] / 1048576, 1 ), '%size' => number_format( $a['size'] / 1048576, 0 ),
                                              '%entries' => $a['entries'], '%rate' => $a['hit_rate'], '%expunges' => $a['expunges'] ) ) );
        }
        return $chips;
    }

    /**
     * The limits of the OPcache and APCu chips: debugbar.ini [DebugBarSettings] Thresholds[opcache_memory] and
     * Thresholds[apcu_memory] where the kernel's registry can read them, else the defaults. The kernel's summary gives
     * its levels only to users with setup/setup; a user with only setup/managecache sees the chips too, with the same
     * limits.
     *
     * @return array 'opcache_memory' / 'apcu_memory' => array( 'warn' => percent, 'high' => percent )
     */
    protected static function memoryThresholds()
    {
        $limits = array( 'opcache_memory' => self::$thresholds['opcache_memory'], 'apcu_memory' => self::$thresholds['apcu_memory'] );
        if ( class_exists( 'expDebugBarRegistry' ) && class_exists( 'eZINI', false ) )
        {
            try
            {
                $ini = ( new expDebugBarRegistry() )->thresholds();
                foreach ( array_keys( $limits ) as $name )
                {
                    if ( isset( $ini[$name] ) )
                        $limits[$name] = array( 'warn' => $ini[$name][0], 'high' => $ini[$name][1] );
                }
            }
            catch ( Throwable $e )
            {
            }
        }
        return $limits;
    }

    /**
     * The chip with the versions of PHP and Exponential, drawn last at the right end of the summary, or null for a
     * viewer who may not see them (runtimeFigures() is null then).
     *
     * @param array|null $runtime runtimeFigures()
     * @return array|null a chip as summaryHtml() draws it: array( tab, key, level, text, title )
     */
    public static function versionsChip( $runtime )
    {
        if ( !is_array( $runtime ) )
            return null;
        $text = 'PHP ' . $runtime['php'] . ( !empty( $runtime['exponential'] ) ? ' | Exp ' . $runtime['exponential'] : '' );
        return array( 'velocity', 'versions', 'ok', $text,
                      self::t( 'Versions: PHP %php (%sapi), Exponential %exponential',
                               array( '%php' => $runtime['php'], '%sapi' => $runtime['sapi'],
                                      '%exponential' => !empty( $runtime['exponential'] ) ? $runtime['exponential'] : '-' ) ) );
    }

    /**
     * APCu of the process that rendered this page, or null when APCu is not loaded or not enabled for it (the
     * command line has it off unless apc.enable_cli is set). Memory without the block lists, so it stays cheap.
     *
     * @return array|null
     */
    public static function apcuStatus()
    {
        if ( !function_exists( 'apcu_enabled' ) || !apcu_enabled() )
            return null;
        $sma = @apcu_sma_info( true );
        $info = @apcu_cache_info( true );
        if ( !is_array( $sma ) || !is_array( $info ) )
            return null;
        $size = (int)( isset( $sma['num_seg'], $sma['seg_size'] ) ? $sma['num_seg'] * $sma['seg_size'] : 0 );
        $avail = (int)( isset( $sma['avail_mem'] ) ? $sma['avail_mem'] : 0 );
        $hits = (int)( isset( $info['num_hits'] ) ? $info['num_hits'] : 0 );
        $misses = (int)( isset( $info['num_misses'] ) ? $info['num_misses'] : 0 );
        return array(
            'size'     => $size,
            'used'     => max( 0, $size - $avail ),
            'entries'  => (int)( isset( $info['num_entries'] ) ? $info['num_entries'] : 0 ),
            'hit_rate' => $hits + $misses > 0 ? round( $hits / ( $hits + $misses ) * 100, 2 ) : 0,
            'expunges' => (int)( isset( $info['expunges'] ) ? $info['expunges'] : 0 ),
        );
    }

    /** APCu facts for the Server tab, or a hint why there are none. */
    protected static function apcuHtml()
    {
        $a = self::apcuStatus();
        if ( $a === null )
            return '<p class="exp-debug-hint">' . self::h( 'Not available: APCu is not loaded or not enabled for this PHP.' ) . '</p>';
        $html = '<table class="exp-debug-facts exp-debug-apcu">';
        $html .= '<tr><th>' . self::h( 'Memory used' ) . '</th><td>' . number_format( $a['used'] / 1048576, 1 ) . ' MB / ' . number_format( $a['size'] / 1048576, 0 ) . ' MB</td></tr>';
        $html .= '<tr><th>' . self::h( 'Entries' ) . '</th><td>' . $a['entries'] . '</td></tr>';
        $html .= '<tr><th>' . self::h( 'Hit rate' ) . '</th><td>' . $a['hit_rate'] . ' %</td></tr>';
        $html .= '<tr><th>' . self::h( 'Expunges' ) . '</th><td>' . $a['expunges'] . '</td></tr>';
        return $html . '</table>';
    }

    /** OPcache status of the process that rendered this page. */
    public static function opcacheStatus()
    {
        if ( !function_exists( 'opcache_get_status' ) )
            return null;
        $status = @opcache_get_status( false );
        if ( !is_array( $status ) )
            return array( 'enabled' => false );
        $mem = isset( $status['memory_usage'] ) ? $status['memory_usage'] : array();
        $stats = isset( $status['opcache_statistics'] ) ? $status['opcache_statistics'] : array();
        return array(
            'enabled'  => !empty( $status['opcache_enabled'] ),
            'used'     => isset( $mem['used_memory'] ) ? (int)$mem['used_memory'] : 0,
            'free'     => isset( $mem['free_memory'] ) ? (int)$mem['free_memory'] : 0,
            'hit_rate' => isset( $stats['opcache_hit_rate'] ) ? round( $stats['opcache_hit_rate'], 2 ) : 0,
            'scripts'  => isset( $stats['num_cached_scripts'] ) ? (int)$stats['num_cached_scripts'] : 0,
            'restarts' => ( isset( $stats['oom_restarts'] ) ? (int)$stats['oom_restarts'] : 0 ) + ( isset( $stats['manual_restarts'] ) ? (int)$stats['manual_restarts'] : 0 ),
            'validate_timestamps' => (string)ini_get( 'opcache.validate_timestamps' ),
            'file_update_protection' => (string)ini_get( 'opcache.file_update_protection' ),
        );
    }

    protected static function opcacheHtml()
    {
        $s = self::opcacheStatus();
        if ( $s === null )
        {
            // Why not: the function is switched off for this PHP (a pool's disable_functions), or OPcache is not loaded
            $disabled = array_map( 'trim', explode( ',', strtolower( (string)ini_get( 'disable_functions' ) ) ) );
            if ( in_array( 'opcache_get_status', $disabled, true ) )
                return '<p class="exp-debug-hint">' . self::h( 'Not available: opcache_get_status() is disabled for this PHP (disable_functions).' ) . '</p>';
            if ( !extension_loaded( 'Zend OPcache' ) )
                return '<p class="exp-debug-hint">' . self::h( 'Not available: OPcache is not loaded in this PHP.' ) . '</p>';
            return '<p class="exp-debug-hint">' . self::h( 'Not available' ) . '</p>';
        }
        $html = '<table class="exp-debug-facts exp-debug-opcache">';
        $html .= '<tr><th>' . self::h( 'Status' ) . '</th><td>' . ( $s['enabled'] ? self::h( 'enabled' ) : self::h( 'disabled' ) ) . '</td></tr>';
        if ( $s['enabled'] )
        {
            $html .= '<tr><th>' . self::h( 'Memory used' ) . '</th><td>' . number_format( $s['used'] / 1048576, 1 ) . ' MB / ' . number_format( ( $s['used'] + $s['free'] ) / 1048576, 0 ) . ' MB</td></tr>';
            $html .= '<tr><th>' . self::h( 'Hit rate' ) . '</th><td>' . $s['hit_rate'] . ' %</td></tr>';
            $html .= '<tr><th>' . self::h( 'Cached scripts' ) . '</th><td>' . $s['scripts'] . '</td></tr>';
            $html .= '<tr><th>' . self::h( 'Restarts' ) . '</th><td>' . $s['restarts'] . '</td></tr>';
            $html .= '<tr><th>opcache.validate_timestamps</th><td>' . htmlspecialchars( $s['validate_timestamps'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '</td></tr>';
            $html .= '<tr><th>opcache.file_update_protection</th><td>' . htmlspecialchars( $s['file_update_protection'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '</td></tr>';
        }
        return $html . '</table>';
    }

    /**
     * The Cache tab: the script fills .exp-debug-cache from the server functions. The form below it
     * works without them (setup/cachetoolbar, the classic toolbar's view), so "This page only" and the
     * common caches can be cleared from the first deploy on.
     */
    protected static function cacheHtml( array $page, array $user )
    {
        if ( !$user['can_cache'] )
            return '<p class="exp-debug-hint exp-debug-warn">' . self::h( 'Sign in with cache access to manage caches' ) . '</p>';
        $html = '<div class="exp-debug-cache" data-state="pending"></div>';

        $types = array();
        if ( $page['node_id'] )
            $types['ContentNode'] = self::t( 'This page only' );
        $types += array( 'Content' => self::t( 'Content' ), 'Template' => self::t( 'Template' ),
                         'TemplateContent' => self::t( 'Template & content' ), 'Ini' => self::t( 'INI settings' ),
                         'All' => self::t( 'All caches' ) );
        if ( $page['node_id'] )
            $types['ContentSubtree'] = self::t( 'This page and below' );

        $html .= '<form class="exp-debug-cache-classic" method="post" action="' . htmlspecialchars( self::url( 'setup/cachetoolbar' ), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '">';
        $html .= '<fieldset><legend>' . self::h( 'Quick clear' ) . '</legend>';
        if ( $page['node_id'] )
        {
            $html .= '<input type="hidden" name="NodeID" value="' . (int)$page['node_id'] . '">';
            $html .= '<input type="hidden" name="ObjectID" value="' . (int)$page['object_id'] . '">';
        }
        $html .= '<input type="hidden" name="RedirectURI" value="' . htmlspecialchars( $page['uri'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '">';
        $html .= '<input type="hidden" name="ClearCacheButton" value="1">';
        foreach ( $types as $value => $label )
        {
            $html .= '<button type="submit" class="exp-debug-button" name="CacheTypeValue" value="' . $value . '">' . htmlspecialchars( $label, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '</button> ';
        }
        $html .= '</fieldset></form>';
        return $html;
    }

    /**
     * A link to one of the bar's files. The address is the plain path: no version or file time query (owner rule,
     * AGENTS.md "Admin design and template styling rules"); a browser that still shows an old file is reloaded by hand.
     */
    protected static function includeTag( $kind, $path, $defer = true )
    {
        $url = eZSys::wwwDir() . '/' . $path;
        if ( $kind === 'css' )
            return '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars( $url, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '">';
        return '<script src="' . htmlspecialchars( $url, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '"' . ( $defer ? ' defer' : '' ) . '></script>';
    }

    /**
     * The bar's scripts, both files (no inline script, so a Content-Security-Policy with script-src 'self' holds):
     * expdebugbar-head.js runs at once, before the first paint, and sets the state of what the markup already has
     * (open and close, "Keep open on reload", the saved panel height, the minimised bar); expdebugbar.js (deferred)
     * the tabs and the controls. All of the bar's CSS is in expdebugbar.css, linked before the bar (render()); the
     * report carries no <style> and no style="" (AGENTS.md "Admin design and template styling rules").
     */
    protected static function assetsHtml()
    {
        return self::includeTag( 'js', 'design/standard/javascript/expdebugbar-head.js', false )
             . self::includeTag( 'js', 'design/standard/javascript/expdebugbar.js' );
    }

    /** The node and URI this page shows (for "This page only"). */
    protected static function pageInfo()
    {
        $info = array( 'node_id' => 0, 'object_id' => 0, 'uri' => '/', 'siteaccess' => '' );
        if ( isset( $GLOBALS['eZCurrentAccess']['name'] ) )
            $info['siteaccess'] = (string)$GLOBALS['eZCurrentAccess']['name'];
        if ( class_exists( 'eZURI', false ) )
        {
            try
            {
                $uri = eZURI::instance()->uriString();
                $info['uri'] = '/' . ltrim( (string)$uri, '/' );
            }
            catch ( Throwable $e )
            {
            }
        }
        if ( class_exists( 'eZTemplate', false ) )
        {
            try
            {
                $tpl = eZTemplate::factory();
                if ( $tpl->hasVariable( 'module_result' ) )
                {
                    $result = $tpl->variable( 'module_result' );
                    if ( isset( $result['content_info']['node_id'] ) )
                    {
                        $info['node_id'] = (int)$result['content_info']['node_id'];
                        $info['object_id'] = isset( $result['content_info']['object_id'] ) ? (int)$result['content_info']['object_id'] : 0;
                    }
                }
            }
            catch ( Throwable $e )
            {
            }
        }
        return $info;
    }

    /** Whether the current user may change settings and clear caches. No names, only rights. */
    protected static function userInfo()
    {
        $info = array( 'logged_in' => false, 'id' => 0, 'can_setup' => false, 'can_cache' => false );
        if ( !class_exists( 'eZUser', false ) )
            return $info;
        try
        {
            $user = eZUser::currentUser();
            if ( !$user )
                return $info;
            $info['logged_in'] = (bool)$user->isRegistered();
            $info['id'] = (int)$user->attribute( 'contentobject_id' );
            $setup = $user->hasAccessTo( 'setup', 'setup' );
            $cache = $user->hasAccessTo( 'setup', 'managecache' );
            $info['can_setup'] = isset( $setup['accessWord'] ) && $setup['accessWord'] !== 'no';
            $info['can_cache'] = isset( $cache['accessWord'] ) && $cache['accessWord'] !== 'no';
        }
        catch ( Throwable $e )
        {
        }
        return $info;
    }

    /** Number of different templates used to render the page, or null when not recorded. */
    protected static function templatesUsed()
    {
        if ( !class_exists( 'eZTemplate', false ) || !eZTemplate::isTemplatesUsageStatisticsEnabled() )
            return null;
        $names = array();
        foreach ( (array)eZTemplate::templatesUsageStatistics() as $entry )
        {
            if ( isset( $entry['actual-template-name'] ) )
                $names[$entry['actual-template-name']] = true;
        }
        return count( $names );
    }

    protected static function url( $path )
    {
        if ( class_exists( 'eZURI', false ) )
        {
            try
            {
                eZURI::transformURI( $path, false, 'relative' );
                return $path;
            }
            catch ( Throwable $e )
            {
            }
        }
        return '/' . $path;
    }

    protected static function bytes( $value )
    {
        $value = trim( (string)$value );
        if ( $value === '' || $value === '-1' )
            return 0;
        $n = (float)$value;
        switch ( strtolower( substr( $value, -1 ) ) )
        {
            case 'g': $n *= 1024;
            case 'm': $n *= 1024;
            case 'k': $n *= 1024;
        }
        return (int)$n;
    }

    protected static function translatedScriptStrings()
    {
        // The same strings are translated for every report; a persistent worker keeps them for the locale while the
        // settings and the translation cache stay (expKernelServiceScope::memo(); a translation cache clear changes
        // its expiry time, which is part of the key).
        if ( class_exists( 'expKernelServiceScope' ) && expKernelServiceScope::memoEnabled() && class_exists( 'eZTSTranslator' ) )
        {
            // the locale translations are looked up in, as ezpI18n::translateText() takes it (an interface language
            // a user chose included)
            $locale = (string)eZLocale::instance()->localeFullCode();
            $expiry = eZExpiryHandler::instance();
            $key = 'debugbar-script-strings|' . $locale . '|' . $expiry->getTimestamp( eZTSTranslator::EXPIRY_KEY, 0 )
                   . '|' . $expiry->getTimestamp( eZTSTranslator::EXPIRY_KEY . '-' . $locale, 0 );
            return expKernelServiceScope::memo( $key, function () { return self::translateScriptStrings(); } );
        }
        return self::translateScriptStrings();
    }

    /**
     * @return array source => translation of the script strings that have one
     */
    private static function translateScriptStrings()
    {
        $out = array();
        foreach ( self::scriptStrings() as $source )
        {
            $translated = self::t( $source );
            if ( $translated !== $source )
                $out[$source] = $translated;
        }
        return $out;
    }
}
