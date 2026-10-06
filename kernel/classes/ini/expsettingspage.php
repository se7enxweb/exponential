<?php
/**
 * File containing the expSettingsPage class: what settings/view and settings/edit show, worked out from an
 * expSettingsChain and an expSettingsSecretRule.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The rows, figures, searches and comparisons of the settings pages, and what a write is followed by.
 *
 * rows(), search(), compareRows(), restartNeeded() and legacySettings() work on their arguments only (tests call
 * them with chains built in memory); chainFor(), iniFileList(), clearIniCache() and afterWrite() read the
 * installation.
 */
class expSettingsPage
{
    /** The session variable that carries the notice of a write to the next settings/view */
    const NOTICE = 'SettingsViewNotice';

    /** How many elements of an array a row shows before the rest is folded away */
    const ARRAY_PREVIEW = 8;

    /** At most this many hits of a search across every file */
    const SEARCH_LIMIT = 300;

    // ------------------------------------------------------------------ reading the installation

    /**
     * Every INI file name the settings page offers: settings/*.ini and the INI files of every override
     * directory (extensions, siteaccesses, settings/override), without their path and suffix, sorted.
     *
     * @return string[] 'site.ini', ...
     */
    public static function iniFileList()
    {
        $iniFiles = eZDir::recursiveFindRelative( 'settings', '', '.ini' );
        foreach ( eZINI::globalOverrideDirs() as $iniDataSet )
        {
            $iniPath = $iniDataSet[1] ? $iniDataSet[0] : 'settings/' . $iniDataSet[0];
            $iniFiles = array_merge( $iniFiles, eZDir::recursiveFindRelative( $iniPath, '', '.ini' ) );
            $iniFiles = array_merge( $iniFiles, eZDir::recursiveFindRelative( $iniPath, '', '.ini.append.php' ) );
        }
        $iniFiles = preg_replace( '%.*/%', '', $iniFiles );
        $iniFiles = preg_replace( '%\.ini.*%', '.ini', $iniFiles );
        $iniFiles = array_values( array_unique( $iniFiles ) );
        sort( $iniFiles );
        return $iniFiles;
    }

    /**
     * An uncached eZINI of a file with the override directories of a siteaccess - what eZINI reads for it there -
     * read with $load, and its chain.
     *
     * @param string $iniFile Checked with expSettingsTarget::iniFile()
     * @param string $siteAccess Checked with expSettingsTarget::siteAccess()
     * @param bool $load Also parse the files with eZINI (the effective values to compare against)
     * @return array ini (eZINI), chain (expSettingsChain)
     */
    public static function chainFor( $iniFile, $siteAccess, $load = true )
    {
        static $templates = array();
        $current = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : null;
        if ( !isset( $templates[$siteAccess] ) )
        {
            $templates[$siteAccess] = $siteAccess === $current ? eZINI::instance( 'site.ini' )
                                                                : eZSiteAccess::getIni( $siteAccess, 'site.ini' );
        }
        $ini = new eZINI( $iniFile, 'settings', null, false, true, false, false, false );
        $ini->setOverrideDirs( $templates[$siteAccess]->overrideDirs( false ) );
        if ( $load )
            $ini->load();
        return array( 'ini' => $ini, 'chain' => expSettingsChain::fromIni( $ini ) );
    }

    /** @return string[] The active extensions of the installation */
    public static function activeExtensions()
    {
        $ini = eZINI::instance();
        $list = $ini->hasVariable( 'ExtensionSettings', 'ActiveExtensions' ) ? (array)$ini->variable( 'ExtensionSettings', 'ActiveExtensions' ) : array();
        if ( $ini->hasVariable( 'ExtensionSettings', 'ActiveAccessExtensions' ) )
            $list = array_merge( $list, (array)$ini->variable( 'ExtensionSettings', 'ActiveAccessExtensions' ) );
        return array_values( array_unique( array_filter( array_map( 'strval', $list ) ) ) );
    }

    /**
     * Whether this request is served by Velocity (the application server) rather than PHP-FPM.
     *
     * @return bool
     */
    public static function servedByVelocity()
    {
        return defined( 'QBIX_SERVER_VERSION' ) || isset( $_SERVER['QBIX_WORKER'] ) || isset( $_SERVER['VELOCITY'] );
    }

    /**
     * Whether a Velocity server of this installation is running (its process ids exist), null when unknown.
     *
     * @return bool|null
     */
    public static function velocityRunning()
    {
        try
        {
            if ( !class_exists( 'expVelocity' ) )
                return null;
            return expVelocity::create()->isRunning();
        }
        catch ( Throwable $e )
        {
            return null;
        }
    }

    /**
     * Clears the INI caches (the tag ini: what "ezcache.php --clear-tag=ini" clears), so both PHP-FPM and
     * Velocity read the changed files on their next request: config.php switches eZINI's file time checks off,
     * and without the clear neither server would see the change at all.
     *
     * @return array ok, caches (names of what was cleared), message
     */
    public static function clearIniCache()
    {
        try
        {
            if ( class_exists( 'expCacheManager' ) )
            {
                $result = ( new expCacheManager() )->clear( 'tag', array( 'ini' ) );
                $names = array();
                foreach ( (array)$result['items'] as $item )
                {
                    if ( isset( $item['name'] ) )
                        $names[] = (string)$item['name'];
                }
                return array( 'ok' => (bool)$result['ok'], 'caches' => $names, 'message' => (string)$result['message'] );
            }
            eZCache::clearByTag( 'ini' );
            return array( 'ok' => true, 'caches' => array( 'ini' ), 'message' => 'cleared tag ini' );
        }
        catch ( Throwable $e )
        {
            return array( 'ok' => false, 'caches' => array(), 'message' => $e->getMessage() );
        }
    }

    /**
     * What a write is followed by: the INI cache clear, and the notice the next settings/view shows (kept in the
     * session). No value is put in it.
     *
     * @param string $action saved, removed or unchanged
     * @param string $iniFile
     * @param string $siteAccess
     * @param array[] $settings block, name, path (the file written)
     * @return array The notice
     */
    public static function afterWrite( $action, $iniFile, $siteAccess, array $settings )
    {
        $cache = $action === 'unchanged' ? array( 'ok' => true, 'caches' => array(), 'message' => '' ) : self::clearIniCache();
        $restart = false;
        $list = self::restartList();
        foreach ( $settings as $s )
            $restart = $restart || self::restartNeeded( $iniFile, $s['block'], $s['name'], $list );
        $notice = array(
            'action' => $action,
            'ini_file' => $iniFile,
            'siteaccess' => $siteAccess,
            'settings' => $settings,
            'cache_ok' => $cache['ok'],
            'caches' => $cache['caches'],
            'restart' => $restart,
            'velocity_running' => self::velocityRunning(),
            'served_by_velocity' => self::servedByVelocity(),
        );
        eZHTTPTool::instance()->setSessionVariable( self::NOTICE, $notice );
        return $notice;
    }

    /**
     * The notice of the last write, once: it is removed from the session when read.
     *
     * @return array|false
     */
    public static function takeNotice()
    {
        $http = eZHTTPTool::instance();
        if ( !$http->hasSessionVariable( self::NOTICE ) )
            return false;
        $notice = $http->sessionVariable( self::NOTICE );
        $http->removeSessionVariable( self::NOTICE );
        return is_array( $notice ) ? $notice : false;
    }

    /** @return string[] site.ini [SettingsViewSettings] RestartSettingList */
    public static function restartList()
    {
        $ini = eZINI::instance();
        return $ini->hasVariable( 'SettingsViewSettings', 'RestartSettingList' )
            ? (array)$ini->variable( 'SettingsViewSettings', 'RestartSettingList' ) : array( 'velocity.ini' );
    }

    // ------------------------------------------------------------------ pure

    /**
     * Whether a setting is one that a running Velocity server only reads when it starts: its file, its block or
     * the setting itself is in the list ('velocity.ini', 'site.ini/DatabaseSettings',
     * 'site.ini/ExtensionSettings/ActiveExtensions').
     *
     * @param string $iniFile
     * @param string $block
     * @param string $name
     * @param string[] $list
     * @return bool
     */
    public static function restartNeeded( $iniFile, $block, $name, array $list )
    {
        foreach ( $list as $entry )
        {
            $parts = explode( '/', trim( (string)$entry ), 3 );
            if ( $parts[0] !== $iniFile )
                continue;
            if ( count( $parts ) === 1 )
                return true;
            if ( $parts[1] !== $block )
                continue;
            if ( count( $parts ) === 2 || $parts[2] === $name )
                return true;
        }
        return false;
    }

    /**
     * The rows of a file for the page: per block, its settings with the effective value (masked as the rule
     * says), where it comes from, its chain and what may be done with it.
     *
     * @param expSettingsChain $chain
     * @param expSettingsSecretRule $rule
     * @param array $options query (search text), changed (only settings changed from the default), runtime
     *                       (block => name => value this server runs with, or null: not known), file, siteaccess,
     *                       extensions (active), restart (RestartSettingList), readOnly (callable( block, name ):
     *                       bool, true when eZINI::isSettingReadOnly() says it is not editable)
     * @return array blocks (name, anchor, total, settings => rows), shown, total, pending, secrets
     */
    public static function rows( expSettingsChain $chain, expSettingsSecretRule $rule, array $options = array() )
    {
        $options += array( 'query' => '', 'changed' => false, 'runtime' => null, 'file' => '', 'siteaccess' => '',
                           'extensions' => array(), 'restart' => array(), 'readOnly' => null );
        $blocks = array();
        $shown = 0;
        $total = 0;
        $pending = 0;
        $secrets = 0;
        $settings = $chain->settings();
        ksort( $settings );
        foreach ( $settings as $block => $vars )
        {
            $blockRows = array();
            foreach ( $vars as $name => $setting )
            {
                ++$total;
                $secret = $rule->isSecretName( $name );
                if ( $secret )
                    ++$secrets;
                if ( $options['changed'] && !$setting['changed'] )
                    continue;
                if ( !$rule->matchesSearch( $options['query'], $block, $name, $setting['value'] ) )
                    continue;
                $row = self::row( $setting, $rule, $options );
                if ( $row['pending'] )
                    ++$pending;
                $blockRows[] = $row;
                ++$shown;
            }
            if ( $blockRows )
            {
                $blocks[] = array( 'name' => (string)$block, 'anchor' => self::anchor( $block ), 'total' => count( $vars ),
                                   'settings' => $blockRows );
            }
        }
        return array( 'blocks' => $blocks, 'shown' => $shown, 'total' => $total, 'pending' => $pending, 'secrets' => $secrets );
    }

    /**
     * One row: see rows().
     *
     * @return array name, anchor, type, kind, secret, secret_state, text (a plain value as shown), elements (key,
     *         string_key, text, origin, path), more (elements beyond ARRAY_PREVIEW), origin (the placement that
     *         wins), winner (path), changed, in_default, default_text, steps (path, placement, status, lines
     *         (op, key, text)), pending, runtime_text, restart, removable, remove_from, editable, edit_placement
     */
    public static function row( array $setting, expSettingsSecretRule $rule, array $options )
    {
        $options += array( 'runtime' => null, 'file' => '', 'restart' => array(), 'readOnly' => null );
        $name = $setting['name'];
        $block = $setting['block'];
        $secret = $rule->isSecretName( $name );
        $value = $setting['value'];

        $elements = array();
        // the files an array's elements come from, in load order
        $sources = array();
        if ( is_array( $value ) )
        {
            foreach ( $setting['elements'] as $element )
            {
                $elements[] = array(
                    'key' => (string)$element['key'],
                    'string_key' => is_string( $element['key'] ),
                    'text' => (string)$rule->displayValue( $name, (string)$element['value'] ),
                    'empty' => (string)$element['value'] === '',
                    'origin' => $element['placement'],
                    'path' => $element['path'],
                );
                if ( $element['path'] !== null && !isset( $sources[$element['path']] ) )
                    $sources[$element['path']] = $element['placement'];
            }
        }
        $mask = $secret ? $rule->maskValue( $value ) : null;

        $steps = array();
        foreach ( $setting['steps'] as $step )
        {
            $lines = array();
            foreach ( $step['ops'] as $op )
            {
                $lines[] = array( 'op' => $op['op'], 'key' => $op['key'] === null ? '' : (string)$op['key'],
                                  'text' => $op['value'] === null ? '' : (string)$rule->displayValue( $name, (string)$op['value'] ) );
            }
            $steps[] = array( 'path' => $step['path'], 'placement' => $step['placement'], 'status' => $step['status'],
                              'lines' => $lines );
        }

        // what this server runs with, where known: a value saved but not yet read (the INI cache) is "pending"
        $pending = false;
        $runtimeText = '';
        $runtimeMissing = false;
        if ( is_array( $options['runtime'] ) )
        {
            $runtime = isset( $options['runtime'][$block] ) && array_key_exists( $name, $options['runtime'][$block] )
                     ? $options['runtime'][$block][$name] : null;
            $pending = $runtime !== $value;
            if ( $pending )
            {
                $shown = $rule->displayValue( $name, $runtime );
                $runtimeText = $runtime === null ? '' : ( is_array( $shown ) ? self::arrayText( $shown ) : (string)$shown );
                $runtimeMissing = $runtime === null;
            }
        }

        $removeFrom = self::removeFrom( $setting );
        $readOnly = is_callable( $options['readOnly'] ) ? (bool)call_user_func( $options['readOnly'], $block, $name ) : false;

        $defaultText = null;
        if ( $setting['inDefault'] )
        {
            $d = $rule->displayValue( $name, $setting['default'] );
            $defaultText = is_array( $d ) ? self::arrayText( $d ) : (string)$d;
        }

        return array(
            'name' => (string)$name,
            'anchor' => self::anchor( $block . '-' . $name ),
            'type' => $setting['type'],
            'kind' => $setting['kind'],
            'secret' => $secret,
            'secret_state' => $mask ? $mask['state'] : '',
            'text' => is_array( $value ) ? '' : (string)$rule->displayValue( $name, $value ),
            'inline_masked' => !$secret && !is_array( $value ) && expSettingsSecretRule::hasInlineSecret( (string)$value ),
            'elements' => array_slice( $elements, 0, self::ARRAY_PREVIEW ),
            'more' => array_slice( $elements, self::ARRAY_PREVIEW ),
            'count' => count( $elements ),
            'origin' => $setting['winnerPlacement'],
            'sources' => array_values( $sources ),
            'winner' => $setting['winner'],
            'changed' => $setting['changed'],
            'in_default' => $setting['inDefault'],
            'default_text' => $defaultText,
            'steps' => $steps,
            'overridden' => count( $setting['overridden'] ),
            'pending' => $pending,
            'runtime_text' => $runtimeText,
            'runtime_missing' => $runtimeMissing,
            'restart' => self::restartNeeded( $options['file'], $block, $name, $options['restart'] ),
            'removable' => $removeFrom !== null && !$readOnly,
            'remove_from' => $removeFrom,
            'editable' => !$readOnly,
            'edit_placement' => is_array( $value ) ? 'siteaccess' : $setting['winnerPlacement']['legacy'],
        );
    }

    /**
     * The file "Remove selected" takes a setting out of: the highest file of the installation's own settings
     * (settings/override or settings/siteaccess/<sa>) that sets it. Never settings/<file>.ini, and never an
     * extension's own file, which belongs to the extension and comes back with its next update.
     *
     * @param array $setting expSettingsChain::setting()
     * @return string|null The path, null when no such file sets it
     */
    public static function removeFrom( array $setting )
    {
        foreach ( array_reverse( $setting['steps'] ) as $step )
        {
            $kind = $step['placement']['kind'];
            if ( $kind === 'override' || $kind === 'siteaccess' )
                return $step['path'];
        }
        return null;
    }

    /** An array shown on one line: "a, b, [key]=c" */
    public static function arrayText( array $value )
    {
        $parts = array();
        foreach ( $value as $k => $v )
            $parts[] = ( is_string( $k ) ? '[' . $k . ']=' : '' ) . ( is_array( $v ) ? '...' : (string)$v );
        return implode( ', ', $parts );
    }

    /**
     * An id for an HTML anchor: letters, digits, '-' and '_' only, prefixed so it never clashes with the admin's
     * own ids.
     *
     * @param string $text
     * @return string
     */
    public static function anchor( $text )
    {
        return 'exp-set-' . trim( preg_replace( '#[^A-Za-z0-9_\-]+#', '-', (string)$text ), '-' );
    }

    /**
     * The template variable "settings" of settings/view as it has always been (block => content => setting =>
     * content, type, placement, editable, removeable; count, removeable, editable), from the chain: the placement
     * of every array element is where the element really comes from, and secret values are masked.
     *
     * @param expSettingsChain $chain
     * @param expSettingsSecretRule $rule
     * @param callable|null $readOnly see rows()
     * @return array
     */
    public static function legacySettings( expSettingsChain $chain, expSettingsSecretRule $rule, $readOnly = null )
    {
        $settings = array();
        foreach ( $chain->settings() as $block => $vars )
        {
            $blockRemoveable = false;
            foreach ( $vars as $name => $setting )
            {
                $shown = $rule->displayValue( $name, $setting['value'] );
                $entry = array( 'type' => $setting['type'], 'placement' => '' );
                if ( is_array( $setting['value'] ) )
                {
                    $content = array();
                    foreach ( $setting['elements'] as $element )
                    {
                        $content[$element['key']] = array(
                            'content' => str_replace( ';', '; ', (string)$shown[$element['key']] ),
                            'placement' => $element['placement'] ? $element['placement']['legacy'] : 'undefined' );
                    }
                    $entry['content'] = $content;
                }
                else
                {
                    $entry['content'] = str_replace( ';', '; ', (string)$shown );
                    $entry['placement'] = $setting['winnerPlacement']['legacy'];
                }
                $editable = is_callable( $readOnly ) ? !call_user_func( $readOnly, $block, $name ) : true;
                $removeable = $editable && self::removeFrom( $setting ) !== null;
                $blockRemoveable = $blockRemoveable || $removeable;
                $entry['editable'] = $editable;
                $entry['removeable'] = $removeable;
                $settings[$block]['content'][$name] = $entry;
            }
            $settings[$block]['count'] = count( $vars );
            $settings[$block]['removeable'] = $blockRemoveable;
            $settings[$block]['editable'] = is_callable( $readOnly ) ? !call_user_func( $readOnly, $block, false ) : true;
        }
        ksort( $settings );
        return $settings;
    }

    /**
     * Search hits of one file, for the search across every file: block, name, the value as shown, origin.
     *
     * @param string $iniFile
     * @param expSettingsChain $chain
     * @param expSettingsSecretRule $rule
     * @param string $query
     * @param int $limit
     * @return array[] file, block, name, anchor, text, secret, origin, winner
     */
    public static function search( $iniFile, expSettingsChain $chain, expSettingsSecretRule $rule, $query, $limit = self::SEARCH_LIMIT )
    {
        $hits = array();
        if ( trim( (string)$query ) === '' )
            return $hits;
        foreach ( $chain->settings() as $block => $vars )
        {
            foreach ( $vars as $name => $setting )
            {
                if ( count( $hits ) >= $limit )
                    return $hits;
                if ( !$rule->matchesSearch( $query, $block, $name, $setting['value'] ) )
                    continue;
                $shown = $rule->displayValue( $name, $setting['value'] );
                $hits[] = array( 'file' => $iniFile, 'block' => (string)$block, 'name' => (string)$name,
                                 'anchor' => self::anchor( $block . '-' . $name ),
                                 'text' => is_array( $shown ) ? self::arrayText( $shown ) : (string)$shown,
                                 'secret' => $rule->isSecretName( $name ),
                                 'origin' => $setting['winnerPlacement'], 'winner' => $setting['winner'] );
            }
        }
        return $hits;
    }

    /**
     * The rows of a comparison of two siteaccesses (expSettingsChain::compare()), values as shown. A secret
     * says only that it differs.
     *
     * @param array[] $diff
     * @param expSettingsSecretRule $rule
     * @return array[] block, name, anchor, secret, in, a_text, b_text, a_origin, b_origin
     */
    public static function compareRows( array $diff, expSettingsSecretRule $rule )
    {
        $rows = array();
        foreach ( $diff as $d )
        {
            $text = function ( $setting ) use ( $rule, $d ) {
                if ( $setting === null )
                    return null;
                $shown = $rule->displayValue( $d['name'], $setting['value'] );
                return is_array( $shown ) ? self::arrayText( $shown ) : (string)$shown;
            };
            $rows[] = array(
                'block' => (string)$d['block'], 'name' => (string)$d['name'],
                'anchor' => self::anchor( $d['block'] . '-' . $d['name'] ),
                'secret' => $rule->isSecretName( $d['name'] ), 'in' => $d['in'],
                'a_text' => $text( $d['a'] ), 'b_text' => $text( $d['b'] ),
                'a_origin' => $d['a'] ? $d['a']['winnerPlacement'] : null,
                'b_origin' => $d['b'] ? $d['b']['winnerPlacement'] : null,
            );
        }
        return $rows;
    }
}
