<?php
/**
 * File containing the expDebugBarRegistry class: the settings, groups and presets of the Exp Debug bar.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads settings/debugbar.ini (and every extension's debugbar.ini.append.php, merged by eZINI):
 *
 * - [DebugBarSettings] Settings[] and one [Setting_<id>] block per setting,
 * - DiscoverBlocks[]: blocks whose every variable becomes a bool setting (debug.ini GeneralCondition),
 * - ExtensionDiscovery: the debug switches of the active extensions' own INI files,
 * - Presets[] and [Preset_<id>], Groups[], Thresholds[].
 *
 * Guide: doc/bc/6.0/debug-bar.md
 */
class expDebugBarRegistry
{
    /** The types a setting can have. */
    public static $Types = array( 'bool', 'enum', 'list', 'text', 'iplist', 'userlist' );

    /** Values a bool setting is written with, by style. */
    public static $BoolWords = array( 'enabled' => 'disabled', 'true' => 'false', 'yes' => 'no', 'on' => 'off' );

    /** @var eZINI */
    protected $ini;

    /** @var array|null id => definition */
    protected $settings = null;

    /** @var array Problems found while reading: setting, why */
    protected $problems = array();

    /**
     * @param eZINI|null $ini debugbar.ini (null: eZINI::instance( 'debugbar.ini' ))
     */
    public function __construct( ?eZINI $ini = null )
    {
        $this->ini = $ini !== null ? $ini : eZINI::instance( 'debugbar.ini' );
    }

    /** @return eZINI */
    public function ini()
    {
        return $this->ini;
    }

    /**
     * A variable of [DebugBarSettings], or $default.
     */
    public function option( $name, $default = null )
    {
        return $this->ini->hasVariable( 'DebugBarSettings', $name ) ? $this->ini->variable( 'DebugBarSettings', $name ) : $default;
    }

    /**
     * Every setting: the registered ones in their order, then the discovered blocks, then the extension switches.
     *
     * @return array id => definition (id, file, block, variable, type, label, group, help, values, on, off,
     *               extension, source registry|block|extension)
     */
    public function settings()
    {
        if ( $this->settings !== null )
            return $this->settings;
        $this->problems = array();
        $settings = array();
        $seen = array();
        foreach ( (array)$this->option( 'Settings', array() ) as $id )
        {
            $id = trim( (string)$id );
            if ( $id === '' || isset( $settings[$id] ) )
                continue;
            $def = $this->readSetting( $id );
            if ( $def === null )
                continue;
            $settings[$id] = $def;
            $seen[self::key( $def['file'], $def['block'], $def['variable'] )] = true;
        }
        foreach ( $this->discoveredBlockSettings() as $id => $def )
        {
            $key = self::key( $def['file'], $def['block'], $def['variable'] );
            if ( isset( $settings[$id] ) || isset( $seen[$key] ) )
                continue;
            $settings[$id] = $def;
            $seen[$key] = true;
        }
        if ( $this->option( 'ExtensionDiscovery', 'enabled' ) === 'enabled' )
        {
            foreach ( $this->discoveredExtensionSettings() as $id => $def )
            {
                $key = self::key( $def['file'], $def['block'], $def['variable'] );
                if ( isset( $settings[$id] ) || isset( $seen[$key] ) )
                    continue;
                $settings[$id] = $def;
                $seen[$key] = true;
            }
        }
        return $this->settings = $settings;
    }

    /**
     * @param string $id
     * @return array|null
     */
    public function setting( $id )
    {
        $settings = $this->settings();
        return isset( $settings[$id] ) ? $settings[$id] : null;
    }

    /** @return array Problems of the registry: id, why */
    public function problems()
    {
        $this->settings();
        return $this->problems;
    }

    /**
     * Reads [Setting_<id>].
     *
     * @return array|null
     */
    protected function readSetting( $id )
    {
        $block = 'Setting_' . $id;
        if ( !preg_match( '/^[A-Za-z0-9_]+$/', $id ) )
        {
            $this->problems[] = array( 'id' => $id, 'why' => 'the id may only use letters, digits and _' );
            return null;
        }
        if ( !$this->ini->hasGroup( $block ) )
        {
            $this->problems[] = array( 'id' => $id, 'why' => "Settings[] names $id but there is no [$block]" );
            return null;
        }
        $g = $this->ini->group( $block );
        foreach ( array( 'File', 'Block', 'Variable' ) as $required )
        {
            if ( !isset( $g[$required] ) || trim( (string)$g[$required] ) === '' )
            {
                $this->problems[] = array( 'id' => $id, 'why' => "[$block] has no $required" );
                return null;
            }
        }
        $type = isset( $g['Type'] ) ? strtolower( trim( $g['Type'] ) ) : 'text';
        if ( !in_array( $type, self::$Types, true ) )
        {
            $this->problems[] = array( 'id' => $id, 'why' => "[$block] Type=$type is not one of " . implode( ', ', self::$Types ) );
            return null;
        }
        $file = self::iniFileName( $g['File'] );
        return array(
            'id' => $id,
            'file' => $file,
            'block' => trim( $g['Block'] ),
            'variable' => trim( $g['Variable'] ),
            'type' => $type,
            'label' => isset( $g['Label'] ) && $g['Label'] !== '' ? $g['Label'] : $g['Variable'],
            'group' => isset( $g['Group'] ) && $g['Group'] !== '' ? $g['Group'] : 'extensions',
            'help' => isset( $g['Help'] ) ? $g['Help'] : '',
            'values' => isset( $g['Values'] ) && is_array( $g['Values'] ) ? array_values( array_filter( $g['Values'], 'strlen' ) ) : array(),
            'on' => isset( $g['On'] ) && $g['On'] !== '' ? $g['On'] : null,
            'off' => isset( $g['Off'] ) && $g['Off'] !== '' ? $g['Off'] : null,
            'extension' => isset( $g['Extension'] ) && $g['Extension'] !== '' ? $g['Extension'] : $this->registeringExtension( $id ),
            'source' => 'registry',
        );
    }

    /**
     * The active extension whose debugbar.ini.append.php registers a setting id, null for the kernel's.
     */
    protected function registeringExtension( $id )
    {
        foreach ( self::activeExtensions() as $ext )
        {
            foreach ( array( 'debugbar.ini.append.php', 'debugbar.ini.append', 'debugbar.ini' ) as $name )
            {
                $path = self::extensionDirectory() . '/' . $ext . '/settings/' . $name;
                if ( is_file( $path ) && strpos( (string)@file_get_contents( $path ), '[Setting_' . $id . ']' ) !== false )
                    return $ext;
            }
        }
        return null;
    }

    /**
     * DiscoverBlocks[] = <file>;<block>;<group>: every variable of the block (in effect) as a bool setting.
     *
     * @return array id => definition
     */
    public function discoveredBlockSettings()
    {
        $out = array();
        foreach ( (array)$this->option( 'DiscoverBlocks', array() ) as $line )
        {
            $parts = array_map( 'trim', explode( ';', (string)$line ) );
            if ( count( $parts ) < 2 || $parts[0] === '' || $parts[1] === '' )
                continue;
            $file = self::iniFileName( $parts[0] );
            $group = isset( $parts[2] ) && $parts[2] !== '' ? $parts[2] : 'conditions';
            $ini = eZINI::instance( $file );
            if ( !$ini->hasGroup( $parts[1] ) )
                continue;
            foreach ( $ini->group( $parts[1] ) as $variable => $value )
            {
                if ( !is_string( $value ) || self::boolStyle( $value ) === null )
                    continue;
                $id = 'cond_' . strtolower( preg_replace( '/[^A-Za-z0-9_]+/', '_', (string)$variable ) );
                $out[$id] = array( 'id' => $id, 'file' => $file, 'block' => $parts[1], 'variable' => (string)$variable,
                                   'type' => 'bool', 'label' => (string)$variable, 'group' => $group,
                                   'help' => "$file [{$parts[1]}] $variable", 'values' => array(), 'on' => null,
                                   'off' => null, 'extension' => null, 'source' => 'block' );
            }
        }
        return $out;
    }

    /**
     * The debug switches of the active extensions: a variable matching DiscoveryPattern with a bool value, in
     * extension/<ext>/settings/*.ini, *.ini.append.php and *.ini.append (not the siteaccess directories).
     *
     * @return array id => definition
     */
    public function discoveredExtensionSettings()
    {
        $pattern = (string)$this->option( 'DiscoveryPattern', '/debug/i' );
        if ( @preg_match( $pattern, '' ) === false )
        {
            $this->problems[] = array( 'id' => 'DiscoveryPattern', 'why' => "'$pattern' is not a regular expression" );
            return array();
        }
        $exclude = array_filter( array_map( 'trim', (array)$this->option( 'DiscoveryExclude', array() ) ), 'strlen' );
        $out = array();
        foreach ( self::activeExtensions() as $ext )
        {
            if ( in_array( $ext, $exclude, true ) )
                continue;
            $dir = self::extensionDirectory() . '/' . $ext . '/settings';
            if ( !is_dir( $dir ) )
                continue;
            $files = glob( $dir . '/*.ini*' ) ?: array();
            sort( $files );
            foreach ( $files as $path )
            {
                if ( !preg_match( '/^([A-Za-z0-9_.-]+\.ini)(\.append(\.php)?)?$/', basename( $path ), $m ) )
                    continue;
                $file = $m[1];
                if ( $file === 'debugbar.ini' || in_array( "$ext;$file", $exclude, true ) )
                    continue;
                try
                {
                    $values = expIniWriter::fromFile( $path )->values();
                }
                catch ( Exception $e )
                {
                    continue;
                }
                foreach ( $values as $block => $vars )
                {
                    foreach ( $vars as $variable => $value )
                    {
                        if ( !is_string( $value ) || !preg_match( $pattern, $variable ) || self::boolStyle( $value ) === null )
                            continue;
                        $id = self::autoId( 'ext_' . $ext, $file, $block, $variable );
                        if ( isset( $out[$id] ) )
                            continue;
                        $out[$id] = array( 'id' => $id, 'file' => $file, 'block' => (string)$block, 'variable' => (string)$variable,
                                           'type' => 'bool', 'label' => "$variable ($ext)", 'group' => 'extensions',
                                           'help' => "$file [$block] $variable, found in extension $ext", 'values' => array(),
                                           'on' => null, 'off' => null, 'extension' => $ext, 'source' => 'extension' );
                    }
                }
            }
        }
        return $out;
    }

    /**
     * The groups in order: id, label. A group used by a setting but not listed comes last.
     *
     * @return array[]
     */
    public function groups()
    {
        $names = (array)$this->option( 'GroupNames', array() );
        $groups = array();
        foreach ( (array)$this->option( 'Groups', array() ) as $id )
        {
            if ( $id !== '' )
                $groups[$id] = array( 'id' => $id, 'label' => isset( $names[$id] ) ? $names[$id] : $id );
        }
        foreach ( $this->settings() as $def )
        {
            if ( !isset( $groups[$def['group']] ) )
                $groups[$def['group']] = array( 'id' => $def['group'], 'label' => isset( $names[$def['group']] ) ? $names[$def['group']] : $def['group'] );
        }
        return array_values( $groups );
    }

    /**
     * The presets of debugbar.ini: id, name, description, values (setting id => value), source 'ini'.
     *
     * @return array id => preset
     */
    public function presets()
    {
        $out = array();
        foreach ( (array)$this->option( 'Presets', array() ) as $id )
        {
            $block = 'Preset_' . $id;
            if ( $id === '' || !$this->ini->hasGroup( $block ) )
            {
                if ( $id !== '' )
                    $this->problems[] = array( 'id' => $id, 'why' => "Presets[] names $id but there is no [$block]" );
                continue;
            }
            $g = $this->ini->group( $block );
            $out[$id] = array( 'id' => $id, 'name' => isset( $g['Name'] ) ? $g['Name'] : $id,
                               'description' => isset( $g['Description'] ) ? $g['Description'] : '',
                               'values' => isset( $g['Values'] ) && is_array( $g['Values'] ) ? $g['Values'] : array(),
                               'source' => 'ini' );
        }
        return $out;
    }

    /**
     * Thresholds[<name>]=<warn>;<high>.
     *
     * @return array name => array( warn, high )
     */
    public function thresholds()
    {
        $defaults = array( 'time_ms' => array( 500, 1500 ), 'sql_count' => array( 100, 300 ), 'sql_ms' => array( 200, 800 ),
                           'memory_mb' => array( 64, 192 ), 'templates' => array( 150, 400 ) );
        foreach ( (array)$this->option( 'Thresholds', array() ) as $name => $value )
        {
            $parts = array_map( 'trim', explode( ';', (string)$value ) );
            if ( count( $parts ) === 2 && is_numeric( $parts[0] ) && is_numeric( $parts[1] ) )
                $defaults[$name] = array( $parts[0] + 0, $parts[1] + 0 );
        }
        return $defaults;
    }

    /**
     * What the RAD survey counts: registered settings (kernel and extension), discovered ones, presets,
     * extension registrations and problems.
     *
     * @return array settings, registered, kernel, extension_registered, discovered_blocks, discovered_extensions,
     *               presets, extensions (ext => count), problems
     */
    public function survey()
    {
        $settings = $this->settings();
        $presets = $this->presets();
        $byExt = array();
        $counts = array( 'registry' => 0, 'block' => 0, 'extension' => 0 );
        $kernel = 0;
        foreach ( $settings as $def )
        {
            $counts[$def['source']]++;
            if ( $def['source'] === 'registry' && $def['extension'] === null )
                $kernel++;
            if ( $def['source'] === 'registry' && $def['extension'] !== null )
                $byExt[$def['extension']] = ( isset( $byExt[$def['extension']] ) ? $byExt[$def['extension']] : 0 ) + 1;
        }
        return array( 'settings' => count( $settings ), 'registered' => $counts['registry'], 'kernel' => $kernel,
                      'extension_registered' => $counts['registry'] - $kernel, 'discovered_blocks' => $counts['block'],
                      'discovered_extensions' => $counts['extension'], 'presets' => count( $presets ),
                      'extensions' => $byExt, 'problems' => $this->problems() );
    }

    // ------------------------------------------------------------------ helpers

    /**
     * The style of a bool value: its "on" word (enabled, true, yes, on), null when it is not a bool word. The case
     * is kept: 'Enabled' gives 'Enabled'.
     */
    public static function boolStyle( $value )
    {
        if ( !is_string( $value ) )
            return null;
        $lower = strtolower( trim( $value ) );
        foreach ( self::$BoolWords as $on => $off )
        {
            if ( $lower === $on || $lower === $off )
                return $on;
        }
        return null;
    }

    /**
     * Whether a bool value means on.
     */
    public static function isOn( $value )
    {
        return is_string( $value ) && in_array( strtolower( trim( $value ) ), array( 'enabled', 'true', 'yes', 'on', '1' ), true );
    }

    /**
     * The words a bool setting is written with: its On/Off, else the style of $reference (the value in effect or the
     * default), else enabled/disabled. The case of $reference is kept (Enabled/Disabled).
     *
     * @return array( on, off )
     */
    public static function boolWords( array $def, $reference = null )
    {
        if ( !empty( $def['on'] ) && !empty( $def['off'] ) )
            return array( $def['on'], $def['off'] );
        $style = self::boolStyle( $reference );
        if ( $style === null )
            return array( 'enabled', 'disabled' );
        $on = $style;
        $off = self::$BoolWords[$style];
        $ref = trim( (string)$reference );
        if ( $ref !== '' && ctype_upper( $ref[0] ) )
        {
            $on = ucfirst( $on );
            $off = ucfirst( $off );
        }
        if ( $ref !== '' && strtoupper( $ref ) === $ref && strlen( $ref ) > 1 )
        {
            $on = strtoupper( $on );
            $off = strtoupper( $off );
        }
        return array( $on, $off );
    }

    /** 'site' or 'site.ini' gives 'site.ini'. */
    public static function iniFileName( $file )
    {
        $file = trim( (string)$file );
        return substr( $file, -4 ) === '.ini' ? $file : $file . '.ini';
    }

    protected static function key( $file, $block, $variable )
    {
        return strtolower( self::iniFileName( $file ) ) . '|' . $block . '|' . $variable;
    }

    protected static function autoId( $prefix, $file, $block, $variable )
    {
        $id = $prefix . '_' . preg_replace( '/\.ini$/', '', $file ) . '_' . $block . '_' . $variable;
        return strtolower( preg_replace( '/[^A-Za-z0-9_]+/', '_', $id ) );
    }

    /** @return string[] */
    public static function activeExtensions()
    {
        if ( class_exists( 'eZExtension' ) )
        {
            try
            {
                return array_values( array_unique( (array)eZExtension::activeExtensions() ) );
            }
            catch ( Exception $e )
            {
            }
        }
        return array();
    }

    /** @return string */
    protected static function extensionDirectory()
    {
        return class_exists( 'eZExtension' ) ? eZExtension::baseDirectory() : 'extension';
    }
}
