<?php
/**
 * File containing the expDebugBarReport class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The HTML debug report as the "Exp Debug" bar: a bar pinned to the bottom of the window with a live
 * summary of the request, and a tabbed panel above it (Messages, Settings, Cache, Timing, SQL, Templates,
 * Included files, Memory, Velocity, and Other for reports nobody claimed).
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
    );

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
            'Messages', 'Settings', 'Cache', 'Timing', 'SQL', 'Templates', 'Included files', 'Memory', 'Velocity', 'Other',
            'Debug report sections', 'Filter', 'Filter settings and report rows', 'No rows match the filter.',
            '%count rows match', 'Clear filter',
            'All', 'Errors', 'Warnings', 'Notices', 'Debug', 'Timing points', 'Strict',
            'Show messages', 'Sort by time', 'Sort by order', 'Slowest first',
            'Loading...', 'Could not load: %error', 'Retry',
            'Sign in with a user who may change settings (setup/setup) to change them here.',
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
            'Response cache', 'Engine', 'Not running on Velocity.',
            'Close', 'Open the debug report',
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

        // Bottom reports: template and packer statistics to Templates, the rest to Other.
        $templateReports = '';
        $otherReports = '';
        foreach ( (array)$debug->bottomReportsList as $name => $report )
        {
            $content = is_callable( $report ) ? call_user_func_array( $report, array( true ) ) : $report;
            if ( $name === 'Template Usage Statistics' || $name === 'ezjscPacker' )
                $templateReports .= $content;
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
            'levels'    => array(),
        );

        // The server side's summary (kernel/classes/debugbar), when it is loaded: its levels follow
        // [DebugBarSettings] Thresholds[] in debugbar.ini, so they win over the defaults above.
        $serverSummary = null;
        if ( class_exists( 'expDebugBarSummary' ) && method_exists( 'expDebugBarSummary', 'collect' ) )
        {
            try
            {
                $serverSummary = expDebugBarSummary::collect();
                foreach ( array( 'time' => 'time', 'sql' => 'sql', 'memory' => 'memory', 'templates' => 'templates' ) as $from => $to )
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

        $tabs = array(
            'messages'  => self::t( 'Messages' ),
            'settings'  => self::t( 'Settings' ),
            'cache'     => self::t( 'Cache' ),
            'timing'    => self::t( 'Timing' ),
            'sql'       => self::t( 'SQL' ),
            'templates' => self::t( 'Templates' ),
            'includes'  => self::t( 'Included files' ),
            'memory'    => self::t( 'Memory' ),
            'velocity'  => self::t( 'Velocity' ),
        );
        if ( $otherReports !== '' )
            $tabs['other'] = self::t( 'Other' );
        $badges = array(
            'messages' => count( $messages['rows'] ),
            'sql'      => $sqlCount,
            'templates'=> $templates === null ? '' : $templates,
        );

        // --- the bar
        echo '<div id="debug" class="exp-debug" data-exp-debug-bar="2">';
        echo '<h2><a href="#debug-end" aria-controls="debug-details" aria-expanded="false">Exp Debug' . htmlspecialchars( $byUser . $byIP ) . '</a>';
        echo self::summaryHtml( $summary );
        echo '</h2>';
        echo '<div id="debug-details" role="region" aria-label="' . self::h( 'Debug report' ) . '">';

        if ( !$debug->UseCSS )
        {
            $wwwDir = eZSys::wwwDir();
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$wwwDir/design/standard/stylesheets/debug.css\">\n";
        }

        echo '<div class="exp-debug-toolbar">';
        echo '<div class="exp-debug-tabs" role="tablist" aria-label="' . self::h( 'Debug report sections' ) . '">';
        foreach ( $tabs as $id => $label )
        {
            $badge = isset( $badges[$id] ) && $badges[$id] !== '' ? ' <span class="exp-debug-badge">' . (int)$badges[$id] . '</span>' : '';
            echo '<button type="button" role="tab" class="exp-debug-tab" id="exp-debug-tab-' . $id . '" aria-controls="exp-debug-panel-' . $id
               . '" aria-selected="false" tabindex="-1" data-tab="' . $id . '">' . htmlspecialchars( $label ) . $badge . '</button>';
        }
        echo '</div>';
        echo '<label class="exp-debug-search"><span class="exp-debug-sr">' . self::h( 'Filter' ) . '</span>'
           . '<input type="search" id="exp-debug-filter" placeholder="' . self::h( 'Filter settings and report rows' ) . '" autocomplete="off"></label>';
        echo '</div>';

        // --- Messages
        self::panelStart( 'messages', $tabs );
        echo '<div class="exp-debug-levels" data-counts="' . htmlspecialchars( json_encode( $messages['counts'] ) ) . '"></div>';
        echo "<table id='main-debug-table' title='Table for actual debug output, shows notices, warnings and errors'>";
        echo $otherTop;
        echo implode( '', $messages['rows'] );
        echo '</table>';
        self::panelEnd();

        // --- Settings (drawn by the script; the classic toolbar below it)
        self::panelStart( 'settings', $tabs );
        echo '<div class="exp-debug-settings" data-state="pending"></div>';
        if ( $classicToolbar !== '' )
        {
            echo '<details class="exp-debug-classic"' . ( $userInfo['can_setup'] ? '' : ' open' ) . '><summary>' . self::h( 'Classic controls' ) . '</summary>';
            echo '<table class="exp-debug-classic-table">' . $classicToolbar . '</table>';
            echo '</details>';
        }
        self::panelEnd();

        // --- Cache
        self::panelStart( 'cache', $tabs );
        echo self::cacheHtml( $pageInfo, $userInfo );
        self::panelEnd();

        // --- Timing
        self::panelStart( 'timing', $tabs );
        if ( $useTiming )
            echo self::timingPointsHtml( $debug, $startTime );
        if ( $useAccumulators )
            echo self::accumulatorsHtml( $debug, $totalElapsed );
        self::panelEnd();

        // --- SQL
        self::panelStart( 'sql', $tabs );
        echo self::sqlHtml( $debug, $ini, $dbType, $sqlCount, $sqlMs, $messages['sql'], $totalElapsed );
        self::panelEnd();

        // --- Templates
        self::panelStart( 'templates', $tabs );
        if ( $templateReports === '' )
            echo '<p class="exp-debug-hint">' . self::h( 'Template usage is not recorded. Turn on "Show used templates" (TemplateSettings ShowUsedTemplates) in the Settings tab.' ) . '</p>';
        echo $templateReports;
        self::panelEnd();

        // --- Included files
        self::panelStart( 'includes', $tabs );
        echo self::includedFilesHtml( $useIncludedFiles );
        self::panelEnd();

        // --- Memory
        self::panelStart( 'memory', $tabs );
        echo self::memoryHtml( $debug, $totalElapsed, $peakMemory, $memoryLimit, $sqlCount, $queryKey );
        self::panelEnd();

        // --- Velocity
        self::panelStart( 'velocity', $tabs );
        echo self::velocityHtml();
        self::panelEnd();

        if ( $otherReports !== '' )
        {
            self::panelStart( 'other', $tabs );
            echo $otherReports;
            self::panelEnd();
        }

        $reportTime = number_format( microtime( true ) - $reportStart, 4 );
        echo '<p class="exp-debug-report-time"><b>' . self::h( 'Time used to render debug report: %time secs', array( '%time' => $reportTime ) ) . '</b></p>';

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
            'strings'    => self::translatedScriptStrings(),
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

    protected static function panelStart( $id, array $tabs )
    {
        echo '<section class="exp-debug-panel" role="tabpanel" id="exp-debug-panel-' . $id . '" aria-labelledby="exp-debug-tab-' . $id . '" tabindex="0">';
        echo '<h3 class="exp-debug-panel-title">' . htmlspecialchars( $tabs[$id] ) . '</h3>';
    }

    protected static function panelEnd()
    {
        echo '</section>';
    }

    /**
     * The message rows, as the classic report printed them, plus the level on each row for the filter.
     *
     * @return array( 'rows' => string[], 'sql' => array of array( 'html', 'ms', 'n' ), 'counts' => level => count )
     */
    protected static function messageRows( eZDebug $debug, array $allowedDebugLevels )
    {
        $hasLevel = array();
        $rows = array();
        $sql = array();
        $counts = array( 'notice' => 0, 'warning' => 0, 'error' => 0, 'debug' => 0, 'timing' => 0, 'strict' => 0 );
        $xdebug = extension_loaded( 'xdebug' );
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
            if ( $xdebug && strncmp( eZDebug::XDEBUG_SIGNATURE, $entry['String'], strlen( eZDebug::XDEBUG_SIGNATURE ) ) === 0 )
                $contents = substr( $entry['String'], strlen( eZDebug::XDEBUG_SIGNATURE ) );
            else
                $contents = htmlspecialchars( (string)$entry['String'] );

            $html = "<tr class='$style' data-level='$style'><td class='debugheader'$identifierText><b><span>{$outputData['name']}:</span> " . htmlspecialchars( $label ) . "</b></td>"
                  . "<td class='debugheader' style=\"text-align:right;\">$time</td></tr>"
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

    /** The chips of the pinned bar, each opening its tab. */
    protected static function summaryHtml( array $s )
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

        $chips = array();
        $chips[] = array( 'timing', 'time', $timeLevel,
                          number_format( $s['time'], 3 ) . ' s', self::t( 'Page time' ) );
        $chips[] = array( 'sql', 'sql', $sqlLevel,
                          self::t( '%count SQL', array( '%count' => $s['sql_count'] ) ) . ' / ' . number_format( $s['sql_ms'], 0 ) . ' ms',
                          self::t( 'SQL queries and their time' ) );
        $chips[] = array( 'memory', 'memory', $memLevel, number_format( $s['memory'] / 1048576, 1 ) . ' MB', self::t( 'Peak memory' ) );
        if ( $s['templates'] !== null )
            $chips[] = array( 'templates', 'templates', $tplLevel,
                              self::t( '%count tpl', array( '%count' => $s['templates'] ) ), self::t( 'Templates used' ) );
        $chips[] = array( 'messages', 'warnings', $s['warnings'] > 0 ? 'warn' : 'ok',
                          self::t( '%count warn', array( '%count' => $s['warnings'] ) ), self::t( 'Warnings' ) );
        $chips[] = array( 'messages', 'errors', $s['errors'] > 0 ? 'high' : 'ok',
                          self::t( '%count err', array( '%count' => $s['errors'] ) ), self::t( 'Errors' ) );

        $html = '<span class="exp-debug-summary" role="group" aria-label="' . self::h( 'Page summary' ) . '">';
        foreach ( $chips as $chip )
        {
            list( $tab, $key, $lvl, $text, $title ) = $chip;
            $levelWord = $lvl === 'high' ? self::t( 'high' ) : ( $lvl === 'warn' ? self::t( 'raised' ) : self::t( 'normal' ) );
            $html .= '<button type="button" class="exp-debug-chip" data-tab="' . $tab . '" data-key="' . $key . '" data-level="' . $lvl . '"'
                   . ' title="' . htmlspecialchars( $title . ' (' . $levelWord . ')' ) . '"'
                   . ' aria-label="' . htmlspecialchars( $title . ': ' . $text . ' (' . $levelWord . ')' ) . '">'
                   . htmlspecialchars( $text ) . '</button>';
        }
        return $html . '</span>';
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
                   . "<td style=\"text-align:right;\">" . number_format( $point['Time'] - $startTime, $acc ) . "</td><td style=\"text-align:right;\">$relElapsed</td>"
                   . "<td style=\"text-align:right;\">" . number_format( $point['MemoryUsage'] / 1024, $acc ) . "</td><td style=\"text-align:right;\">$relMemory</td></tr>";
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
            $html .= "<tr class='group'><td><b>{$group['name']}</b></td>";
            if ( array_key_exists( 'time_data', $group ) )
            {
                $g = $group['time_data'];
                $html .= "<td style=\"text-align:right;\"><i>" . number_format( $g['time'], $acc ) . "</i></td>"
                       . "<td style=\"text-align:right;\"><i>" . number_format( $g['time'] * 100.0 / $totalElapsed, 1 ) . "</i></td>"
                       . "<td style=\"text-align:right;\"><i>{$g['count']}</i></td>"
                       . "<td style=\"text-align:right;\"><i>" . number_format( $g['count'] ? $g['time'] / $g['count'] : 0, $acc ) . "</i></td>";
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
                       . "<td style=\"text-align:right;\">" . number_format( $child['time'], $acc ) . "</td>"
                       . "<td style=\"text-align:right;\">" . number_format( $child['time'] * 100.0 / $totalElapsed, $pacc ) . "</td>"
                       . "<td style=\"text-align:right;\">{$child['count']}</td>"
                       . "<td style=\"text-align:right;\">" . number_format( $avg, $pacc ) . "</td></tr>";
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
              . '<tr><th>' . self::h( 'Database' ) . '</th><td>' . htmlspecialchars( $dbType ) . '</td></tr></table>';

        // The database's own accumulators (connection, queries, loops, conversion).
        $groups = array();
        foreach ( self::accumulatorGroups( $debug ) as $key => $group )
        {
            if ( strpos( (string)$key, $dbType ) === 0 )
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
            $html .= "<tr class='data'><td>" . htmlspecialchars( $phpFile ) . "</td></tr>";
        }
        $html .= "<tr><td><b>&nbsp;" . self::h( 'Number of files included: %count', array( '%count' => count( $phpFiles ) ) ) . "</b></td></tr>";
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

    protected static function velocityHtml()
    {
        $rows = array();
        if ( defined( 'QBIX_SERVER_VERSION' ) )
        {
            $version = function_exists( 'qbix_version_label' ) ? qbix_version_label() : (string)QBIX_SERVER_VERSION;
            $rows[self::t( 'Engine' )] = 'Exponential Velocity ' . $version;
        }
        else
        {
            $software = isset( $_SERVER['SERVER_SOFTWARE'] ) ? (string)$_SERVER['SERVER_SOFTWARE'] : '';
            $engine = getenv( 'EXP_VELOCITY_ENGINE' );
            $rows[self::t( 'Engine' )] = $engine ? $engine : trim( $software . ' (' . PHP_SAPI . ')' );
        }
        $rows['PHP'] = PHP_VERSION . ' (' . PHP_SAPI . ')';
        $rows[self::t( 'Process' )] = (string)getmypid();
        $rows[self::t( 'Host' )] = function_exists( 'gethostname' ) ? (string)gethostname() : '';

        $html = '';
        if ( !defined( 'QBIX_SERVER_VERSION' ) )
            $html .= '<p class="exp-debug-hint">' . self::h( 'Not running on Velocity.' ) . '</p>';
        $html .= '<table class="exp-debug-facts">';
        foreach ( $rows as $k => $v )
            $html .= '<tr><th>' . htmlspecialchars( $k ) . '</th><td>' . htmlspecialchars( $v ) . '</td></tr>';
        $html .= '</table>';

        $html .= '<h3>' . self::h( 'OPcache' ) . '</h3>' . self::opcacheHtml();
        $html .= '<div class="exp-debug-velocity-cache"></div>';
        return $html;
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
            return '<p class="exp-debug-hint">' . self::h( 'Not available' ) . '</p>';
        $html = '<table class="exp-debug-facts exp-debug-opcache">';
        $html .= '<tr><th>' . self::h( 'Status' ) . '</th><td>' . ( $s['enabled'] ? self::h( 'enabled' ) : self::h( 'disabled' ) ) . '</td></tr>';
        if ( $s['enabled'] )
        {
            $html .= '<tr><th>' . self::h( 'Memory used' ) . '</th><td>' . number_format( $s['used'] / 1048576, 1 ) . ' MB / ' . number_format( ( $s['used'] + $s['free'] ) / 1048576, 0 ) . ' MB</td></tr>';
            $html .= '<tr><th>' . self::h( 'Hit rate' ) . '</th><td>' . $s['hit_rate'] . ' %</td></tr>';
            $html .= '<tr><th>' . self::h( 'Cached scripts' ) . '</th><td>' . $s['scripts'] . '</td></tr>';
            $html .= '<tr><th>' . self::h( 'Restarts' ) . '</th><td>' . $s['restarts'] . '</td></tr>';
            $html .= '<tr><th>opcache.validate_timestamps</th><td>' . htmlspecialchars( $s['validate_timestamps'] ) . '</td></tr>';
            $html .= '<tr><th>opcache.file_update_protection</th><td>' . htmlspecialchars( $s['file_update_protection'] ) . '</td></tr>';
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
        $html = '<div class="exp-debug-cache" data-state="pending"></div>';
        if ( !$user['can_cache'] )
            return $html . '<p class="exp-debug-hint">' . self::h( 'You may not clear caches (setup/managecache).' ) . '</p>';

        $types = array();
        if ( $page['node_id'] )
            $types['ContentNode'] = self::t( 'This page only' );
        $types += array( 'Content' => self::t( 'Content' ), 'Template' => self::t( 'Template' ),
                         'TemplateContent' => self::t( 'Template & content' ), 'Ini' => self::t( 'INI settings' ),
                         'All' => self::t( 'All caches' ) );
        if ( $page['node_id'] )
            $types['ContentSubtree'] = self::t( 'This page and below' );

        $html .= '<form class="exp-debug-cache-classic" method="post" action="' . htmlspecialchars( self::url( 'setup/cachetoolbar' ) ) . '">';
        $html .= '<fieldset><legend>' . self::h( 'Quick clear' ) . '</legend>';
        if ( $page['node_id'] )
        {
            $html .= '<input type="hidden" name="NodeID" value="' . (int)$page['node_id'] . '">';
            $html .= '<input type="hidden" name="ObjectID" value="' . (int)$page['object_id'] . '">';
        }
        $html .= '<input type="hidden" name="RedirectURI" value="' . htmlspecialchars( $page['uri'] ) . '">';
        $html .= '<input type="hidden" name="ClearCacheButton" value="1">';
        foreach ( $types as $value => $label )
        {
            $html .= '<button type="submit" class="exp-debug-button" name="CacheTypeValue" value="' . $value . '">' . htmlspecialchars( $label ) . '</button> ';
        }
        $html .= '</fieldset></form>';
        return $html;
    }

    protected static function includeTag( $kind, $path )
    {
        $file = $path;
        $version = @filemtime( $file );
        $url = eZSys::wwwDir() . '/' . $path . ( $version ? '?v=' . $version : '' );
        if ( $kind === 'css' )
            return '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars( $url ) . '">';
        return '<script src="' . htmlspecialchars( $url ) . '" defer></script>';
    }

    /**
     * The bar's stylesheet and script. The pinned bar and the open/close with "Keep open on reload"
     * are inline, so they work in every design even when the files cannot be loaded; the tabs and
     * the controls come from the files.
     */
    protected static function assetsHtml()
    {
        $html = self::includeTag( 'css', 'design/standard/stylesheets/expdebugbar.css' );
        // The bar stays at the bottom of the window wherever the page is scrolled; the details open as a
        // panel above it (its height follows the bar, which may wrap at narrow widths).
        $html .= "<style>
#debug { margin-bottom: 0; }
#debug > h2 { position: fixed; left: 0; right: 0; bottom: 0; z-index: 2147483000; margin: 0;
  display: flex; flex-wrap: wrap; align-items: center; background: #f2f2f2; border-top: 1px solid #999;
  box-shadow: 0 -2px 6px rgba(0,0,0,.15); font-size: 14px; }
#debug > h2 > a { flex: 1 1 auto; padding: 8px 12px; color: #222; text-decoration: none; }
#debug > h2 > label.debug-keep-open { margin-right: 12px; color: #222; }
#debug-details { display: none; }
#debug-details.active { display: block; position: fixed; left: 0; right: 0; bottom: var(--exp-debug-bar-h, 38px); z-index: 2147482999;
  max-height: 60vh; overflow: auto; background: var(--xd-bg, #fff); color: var(--xd-fg, #222); border-top: 2px solid #999;
  box-shadow: 0 -4px 12px rgba(0,0,0,.2); box-sizing: border-box; }
body.exp-debug-bar { padding-bottom: calc(var(--exp-debug-bar-h, 38px) + 8px); }
@media (prefers-color-scheme: dark) {
  #debug > h2 { background: #2a2a2a; border-top-color: #555; }
  #debug > h2 > a, #debug > h2 > label.debug-keep-open { color: #eee; }
}
</style>";
        // Open and close with a click on the header. "Keep open on reload" remembers the state in the
        // browser's localStorage (exp-debug-keep, exp-debug-open); unticked, the details start closed on
        // every page. Storage that throws (private windows, blocked site data) is ignored.
        $html .= "<script>
(function () {
  const header = document.querySelector('#debug h2 a');
  const content = document.querySelector('#debug-details');
  if (!header || !content) return;
  document.body.classList.add('exp-debug-bar');
  const bar = header.parentNode;
  const fit = () => document.documentElement.style.setProperty('--exp-debug-bar-h', bar.offsetHeight + 'px');
  fit();
  if (window.ResizeObserver) new ResizeObserver(fit).observe(bar); else window.addEventListener('resize', fit);
  header.addEventListener('click', (e) => e.preventDefault());
  const read = (key) => { try { return window.localStorage.getItem(key); } catch (e) { return null; } };
  const write = (key, value) => { try { window.localStorage.setItem(key, value); } catch (e) {} };
  const setOpen = (open) => {
    header.classList.toggle('active', open);
    content.classList.toggle('active', open);
    header.setAttribute('aria-expanded', open ? 'true' : 'false');
    document.dispatchEvent(new CustomEvent('exp-debug-toggle', { detail: { open: open } }));
  };
  window.expDebugBarSetOpen = (open) => {
    setOpen(open);
    if (keep.checked) write('exp-debug-open', open ? '1' : '0');
  };
  const keepLabel = document.createElement('label');
  keepLabel.className = 'debug-keep-open';
  keepLabel.style.cssText = 'margin-left:1em;font-size:12px;font-weight:normal;cursor:pointer;white-space:nowrap';
  const keep = document.createElement('input');
  keep.type = 'checkbox';
  keep.checked = read('exp-debug-keep') === '1';
  keepLabel.appendChild(keep);
  keepLabel.appendChild(document.createTextNode(' ' + " . json_encode( self::t( 'Keep open on reload' ), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP ) . "));
  bar.appendChild(keepLabel);
  if (keep.checked && read('exp-debug-open') === '1') setOpen(true);
  keep.addEventListener('change', () => {
    write('exp-debug-keep', keep.checked ? '1' : '0');
    write('exp-debug-open', keep.checked && content.classList.contains('active') ? '1' : '0');
  });
  header.addEventListener('click', () => window.expDebugBarSetOpen(!content.classList.contains('active')));
})();
</script>";
        $html .= self::includeTag( 'js', 'design/standard/javascript/expdebugbar.js' );
        return $html;
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
