<?php
/**
 * File containing the expSettingsChain class: for every setting of one INI file in one siteaccess, which files set
 * it, in eZINI's load order, what each of them does to it and which one wins.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The override chain of one INI file, built once from the files eZINI reads for it, in eZINI's own order.
 *
 * The order is not derived here: fromIni() asks an eZINI instance (eZINI::findInputFiles()) for its input files, so
 * the chain is exactly the load order of that instance - settings/<file>.ini first, then the override directories in
 * eZINI's scope order (extension siteaccess directories, settings/siteaccess/<sa>, the active extensions, then
 * settings/override). Each file is read once, line by line, by expIniWriter (the same reading rules as
 * eZINI::parseFile()), and the lines are then replayed in order the way eZINI merges them:
 *
 *   Var=value      sets a plain value; whatever earlier files gave the variable is replaced
 *   Var[]          resets an array: every element earlier files added is dropped
 *   Var[]=value    adds an element at the end of the array
 *   Var[key]=value sets the element "key"; an earlier file's element with the same key is replaced
 *
 * The replay keeps, for every element, the file it came from, so an array shows where each element really comes
 * from. (eZINI's own groupPlacements() keeps appending placements past a reset, so after a reset its placements no
 * longer line up with the elements - the settings page used to show those, and named the wrong files.)
 *
 * Nothing here reads a database, and the core (the constructor and everything after it) reads no file at all: a
 * test gives it layers in memory.
 */
class expSettingsChain
{
    /** The file names eZINI reads in a settings directory (eZINI::findInputFiles()), longest first for stripping. */
    const SUFFIXES = '.ini.append.php|.ini.append|.ini.php|.ini';

    /** @var array[] path, placement (array), entries */
    protected $layers = array();

    /** @var array|null block => var => result, built on first use */
    protected $settings = null;

    /**
     * @param array[] $layers In load order, each array( 'path' => 'settings/override/site.ini.append.php',
     *                        'entries' => expIniWriter::entries() of that file ). A layer may also give 'placement'
     *                        (see placementOf()); otherwise it is worked out from the path.
     */
    public function __construct( array $layers )
    {
        foreach ( $layers as $layer )
        {
            $path = (string)$layer['path'];
            $this->layers[] = array(
                'path' => $path,
                'placement' => isset( $layer['placement'] ) ? $layer['placement'] : self::placementOf( $path ),
                'entries' => isset( $layer['entries'] ) ? (array)$layer['entries'] : array(),
                // the file is in the load order but this process could not read it (permissions)
                'unreadable' => !empty( $layer['unreadable'] ),
            );
        }
    }

    /**
     * The chain of an eZINI instance: its input files, read from disk.
     *
     * @param eZINI $ini An instance that has not been read with direct access (expIniLocator::iniFor())
     * @param string|null $root Installation root with a trailing slash; null: expIniEditor::root()
     * @return expSettingsChain
     */
    public static function fromIni( eZINI $ini, $root = null )
    {
        $root = $root !== null ? $root : expIniEditor::root();
        $inputFiles = array();
        $iniFile = null;
        $ini->findInputFiles( $inputFiles, $iniFile );
        $layers = array();
        foreach ( $inputFiles as $path )
        {
            $absolute = $path !== '' && $path[0] === '/' ? $path : $root . $path;
            if ( !is_file( $absolute ) )
                continue;
            $unreadable = !is_readable( $absolute );
            try
            {
                $entries = expIniWriter::fromFile( $absolute )->entries();
            }
            catch ( Exception $e )
            {
                $entries = array();
                $unreadable = true;
            }
            $layers[] = array( 'path' => self::relativePath( $absolute, $root ), 'entries' => $entries,
                               'unreadable' => $unreadable );
        }
        return new self( $layers );
    }

    /**
     * A path relative to the installation root ('settings/site.ini'), from an absolute or a relative one.
     *
     * @param string $path
     * @param string $root With a trailing slash
     * @return string
     */
    public static function relativePath( $path, $root )
    {
        $path = preg_replace( '#/+#', '/', str_replace( '\\', '/', (string)$path ) );
        $root = rtrim( str_replace( '\\', '/', (string)$root ), '/' ) . '/';
        foreach ( array_unique( array( $root, ( realpath( $root ) ?: rtrim( $root, '/' ) ) . '/' ) ) as $prefix )
        {
            if ( $prefix !== '/' && strpos( $path, $prefix ) === 0 )
                return substr( $path, strlen( $prefix ) );
        }
        $real = $path !== '' && $path[0] === '/' ? realpath( $path ) : false;
        $realRoot = realpath( $root );
        if ( $real !== false && $realRoot !== false && strpos( $real, $realRoot . '/' ) === 0 )
            return substr( $real, strlen( $realRoot ) + 1 );
        while ( strpos( $path, './' ) === 0 )
            $path = substr( $path, 2 );
        return $path;
    }

    /**
     * Where a settings file sits in the chain, from its path relative to the root.
     *
     *   settings/site.ini                                       default
     *   settings/override/site.ini.append.php                   override
     *   settings/siteaccess/admin/site.ini.append.php           siteaccess (admin)
     *   extension/ezoe/settings/site.ini.append.php             extension (ezoe)
     *   extension/ezoe/settings/siteaccess/admin/site.ini...    extension-siteaccess (ezoe, admin)
     *   extension/ezoe/settings/<dir>/site.ini.append.php       extension-dir (ezoe, <dir>), e.g. a multi-site group
     *
     * 'legacy' is the string eZINI::findSettingPlacement() gives the same path (default, override, siteaccess,
     * extension:<ext>, ext-siteaccess:<ext>, ext-siteaccess-<dir>:<ext>), which the edit links and the old
     * template variables use.
     *
     * @param string $path
     * @return array kind, legacy, extension, siteaccess, dir, path
     */
    public static function placementOf( $path )
    {
        $parts = explode( '/', trim( (string)$path, '/' ) );
        $result = array( 'kind' => 'other', 'legacy' => 'undefined', 'extension' => null, 'siteaccess' => null,
                         'dir' => null, 'path' => (string)$path );
        $n = count( $parts );
        if ( $n === 2 && $parts[0] === 'settings' )
            return array( 'kind' => 'default', 'legacy' => 'default' ) + $result;
        if ( $n === 3 && $parts[0] === 'settings' && $parts[1] === 'override' )
            return array( 'kind' => 'override', 'legacy' => 'override' ) + $result;
        if ( $n === 4 && $parts[0] === 'settings' && $parts[1] === 'siteaccess' )
            return array( 'kind' => 'siteaccess', 'legacy' => 'siteaccess', 'siteaccess' => $parts[2] ) + $result;
        if ( $parts[0] === 'extension' && $n >= 4 && $parts[2] === 'settings' )
        {
            if ( $n === 4 )
                return array( 'kind' => 'extension', 'legacy' => 'extension:' . $parts[1], 'extension' => $parts[1] ) + $result;
            if ( $n === 6 && $parts[3] === 'siteaccess' )
                return array( 'kind' => 'extension-siteaccess', 'legacy' => 'ext-siteaccess:' . $parts[1],
                              'extension' => $parts[1], 'siteaccess' => $parts[4] ) + $result;
            if ( $n === 5 )
                return array( 'kind' => 'extension-dir', 'legacy' => 'ext-siteaccess-' . $parts[3] . ':' . $parts[1],
                              'extension' => $parts[1], 'dir' => $parts[3] ) + $result;
        }
        return $result;
    }

    /**
     * The settings directory of a file's path ('settings/siteaccess/admin'), the part a reader recognises.
     *
     * @param string $path
     * @return string
     */
    public static function directoryOf( $path )
    {
        $dir = dirname( (string)$path );
        return $dir === '.' ? '' : $dir;
    }

    /** @return array[] The layers in load order: path, placement, entries */
    public function layers()
    {
        return $this->layers;
    }

    /** @return string[] The files of the chain, relative to the root, in load order */
    public function paths()
    {
        $paths = array();
        foreach ( $this->layers as $layer )
            $paths[] = $layer['path'];
        return $paths;
    }

    /**
     * Every setting of the file, by block and name, in the order they first appear in the chain.
     *
     * @return array block => name => result (see setting())
     */
    public function settings()
    {
        if ( $this->settings === null )
            $this->settings = $this->replay();
        return $this->settings;
    }

    /**
     * One setting's chain.
     *
     * @param string $block
     * @param string $name
     * @return array|null block, name, value (the effective value), type (eZINI::settingType()), kind (plain, list,
     *         hash or mixed), steps (every file that touches it, in order: path, placement, ops (op reset, set,
     *         append or hash, key, value), value (what that file alone gives), status (wins, adds, kept,
     *         overridden, reset)), winner (the path whose value is in effect for a plain value, the last reset or
     *         else the first file for an array), elements (an array's elements: key, value, path, placement),
     *         default (the value of the default file, null without one), inDefault, changed (the effective value
     *         differs from the default, or there is no default), overridden (paths whose contribution is gone);
     *         null when no file sets it
     */
    public function setting( $block, $name )
    {
        $settings = $this->settings();
        return isset( $settings[$block][$name] ) ? $settings[$block][$name] : null;
    }

    /** @return string[] The block names, in the order they first appear */
    public function blocks()
    {
        return array_keys( $this->settings() );
    }

    /**
     * Figures of the file: blocks, settings, arrays, changed from the default, settings with no default, and per
     * place in the chain how many settings it decides.
     *
     * @return array blocks, settings, arrays, changed, added, files, by_kind (kind => settings won), files_used
     *               (path => settings it sets)
     */
    public function summary()
    {
        $summary = array( 'blocks' => 0, 'settings' => 0, 'arrays' => 0, 'changed' => 0, 'added' => 0,
                          'files' => count( $this->layers ), 'by_kind' => array(), 'files_used' => array(),
                          'unreadable' => array() );
        foreach ( $this->layers as $layer )
        {
            $summary['files_used'][$layer['path']] = 0;
            if ( $layer['unreadable'] )
                $summary['unreadable'][] = $layer['path'];
        }
        foreach ( $this->settings() as $block => $settings )
        {
            ++$summary['blocks'];
            foreach ( $settings as $setting )
            {
                ++$summary['settings'];
                if ( $setting['kind'] !== 'plain' )
                    ++$summary['arrays'];
                if ( $setting['changed'] )
                    ++$summary['changed'];
                if ( !$setting['inDefault'] )
                    ++$summary['added'];
                $kind = $setting['winnerPlacement']['kind'];
                $summary['by_kind'][$kind] = ( isset( $summary['by_kind'][$kind] ) ? $summary['by_kind'][$kind] : 0 ) + 1;
                foreach ( $setting['steps'] as $step )
                    ++$summary['files_used'][$step['path']];
            }
        }
        return $summary;
    }

    /**
     * Replays every layer's lines in order, as eZINI merges them, keeping where each value came from.
     *
     * @return array block => name => result
     */
    protected function replay()
    {
        // block => name => array( value, sources (key => layer index, for an array), steps )
        $state = array();
        foreach ( $this->layers as $index => $layer )
        {
            foreach ( $layer['entries'] as $entry )
            {
                $type = $entry['type'];
                if ( $type === 'block' )
                {
                    if ( !isset( $state[$entry['block']] ) )
                        $state[$entry['block']] = array();
                    continue;
                }
                if ( !in_array( $type, array( 'reset', 'plain', 'append', 'hash' ), true ) )
                    continue;
                $b = (string)$entry['block'];
                $v = (string)$entry['var'];
                if ( !isset( $state[$b][$v] ) )
                    $state[$b][$v] = array( 'value' => null, 'sources' => null, 'steps' => array() );
                $s =& $state[$b][$v];
                $last = count( $s['steps'] ) - 1;
                if ( $last < 0 || $s['steps'][$last]['layer'] !== $index )
                {
                    $s['steps'][] = array( 'layer' => $index, 'ops' => array() );
                    ++$last;
                }
                $value = isset( $entry['value'] ) ? $entry['value'] : null;
                $key = isset( $entry['key'] ) ? $entry['key'] : null;
                switch ( $type )
                {
                    case 'reset':
                        $s['value'] = array();
                        $s['sources'] = array();
                        break;
                    case 'plain':
                        $s['value'] = $value;
                        $s['sources'] = null;
                        break;
                    case 'append':
                        self::toArray( $s );
                        $s['value'][] = $value;
                        end( $s['value'] );
                        $s['sources'][key( $s['value'] )] = $index;
                        break;
                    case 'hash':
                        self::toArray( $s );
                        $s['value'][$key] = $value;
                        $s['sources'][$key] = $index;
                        break;
                }
                $s['steps'][$last]['ops'][] = array( 'op' => $type === 'plain' ? 'set' : $type, 'key' => $key, 'value' => $value );
                unset( $s );
            }
        }

        $result = array();
        foreach ( $state as $block => $vars )
        {
            $result[$block] = array();
            foreach ( $vars as $name => $s )
                $result[$block][$name] = $this->describe( $block, $name, $s );
        }
        return $result;
    }

    /** A plain value that an append or a hash entry follows becomes an array, as expIniWriter::values() does. */
    protected static function toArray( array &$s )
    {
        if ( !is_array( $s['value'] ) )
        {
            $s['value'] = $s['value'] === null ? array() : array( $s['value'] );
            // the plain value, if any, stays as element 0 from the layer that set it, unknown here: no source
            $s['sources'] = $s['value'] ? array( 0 => null ) : array();
        }
    }

    /**
     * The result of one setting from its replayed state.
     */
    protected function describe( $block, $name, array $s )
    {
        $value = $s['value'];
        $isArray = is_array( $value );
        $kind = 'plain';
        if ( $isArray )
        {
            $hasString = false;
            $hasInt = false;
            foreach ( array_keys( $value ) as $k )
            {
                if ( is_int( $k ) )
                    $hasInt = true;
                else
                    $hasString = true;
            }
            $kind = $hasString ? ( $hasInt ? 'mixed' : 'hash' ) : 'list';
        }

        // which layers still have something in the effective value
        $contributing = array();
        $lastReset = null;
        $lastSet = null;
        foreach ( $s['steps'] as $i => $step )
        {
            foreach ( $step['ops'] as $op )
            {
                if ( $op['op'] === 'reset' )
                    $lastReset = $i;
                if ( $op['op'] === 'set' )
                    $lastSet = $i;
            }
        }
        $elements = array();
        if ( $isArray )
        {
            foreach ( $value as $k => $v )
            {
                $layerIndex = isset( $s['sources'][$k] ) ? $s['sources'][$k] : null;
                if ( $layerIndex !== null )
                    $contributing[$layerIndex] = true;
                $elements[] = array(
                    'key' => $k,
                    'value' => $v,
                    'path' => $layerIndex !== null ? $this->layers[$layerIndex]['path'] : null,
                    'placement' => $layerIndex !== null ? $this->layers[$layerIndex]['placement'] : null,
                );
            }
        }

        $steps = array();
        $overridden = array();
        $count = count( $s['steps'] );
        foreach ( $s['steps'] as $i => $step )
        {
            $layer = $this->layers[$step['layer']];
            $resets = false;
            foreach ( $step['ops'] as $op )
                $resets = $resets || $op['op'] === 'reset';
            if ( !$isArray )
                $status = $i === $lastSet ? 'wins' : 'overridden';
            else if ( isset( $contributing[$step['layer']] ) )
                $status = 'adds';
            else if ( $resets && $i === $lastReset )
                $status = 'reset';
            else
                $status = 'overridden';
            if ( $status === 'overridden' )
                $overridden[] = $layer['path'];
            $steps[] = array(
                'path' => $layer['path'],
                'placement' => $layer['placement'],
                'ops' => $step['ops'],
                'value' => self::valueOfOps( $step['ops'] ),
                'resets' => $resets,
                'status' => $status,
                'last' => $i === $count - 1,
            );
        }

        if ( !$isArray )
            $winnerStep = $lastSet !== null ? $lastSet : $count - 1;
        else
            $winnerStep = $lastReset !== null ? $lastReset : $this->firstContributing( $s['steps'], $contributing, $count );
        $winner = $s['steps'][$winnerStep];

        $default = null;
        $inDefault = false;
        foreach ( $steps as $step )
        {
            if ( $step['placement']['kind'] === 'default' )
            {
                $inDefault = true;
                $default = $step['value'];
            }
        }

        return array(
            'block' => (string)$block,
            'name' => (string)$name,
            'value' => $value,
            'type' => self::settingType( $value ),
            'kind' => $kind,
            'steps' => $steps,
            'winner' => $this->layers[$winner['layer']]['path'],
            'winnerPlacement' => $this->layers[$winner['layer']]['placement'],
            'elements' => $elements,
            'default' => $default,
            'inDefault' => $inDefault,
            'changed' => !$inDefault || $default !== $value,
            'overridden' => $overridden,
        );
    }

    protected function firstContributing( array $steps, array $contributing, $count )
    {
        foreach ( $steps as $i => $step )
        {
            if ( isset( $contributing[$step['layer']] ) )
                return $i;
        }
        return $count - 1;
    }

    /**
     * What one file alone gives a setting, from its operations in that file.
     *
     * @param array[] $ops
     * @return string|array|null
     */
    public static function valueOfOps( array $ops )
    {
        $value = null;
        foreach ( $ops as $op )
        {
            switch ( $op['op'] )
            {
                case 'reset':
                    $value = array();
                    break;
                case 'set':
                    $value = $op['value'];
                    break;
                case 'append':
                    $value = is_array( $value ) ? $value : ( $value === null ? array() : array( $value ) );
                    $value[] = $op['value'];
                    break;
                case 'hash':
                    $value = is_array( $value ) ? $value : ( $value === null ? array() : array( $value ) );
                    $value[$op['key']] = $op['value'];
                    break;
            }
        }
        return $value;
    }

    /**
     * eZINI::settingType() without an instance: array, numeric, true/false, enable/disable or string.
     *
     * @param mixed $value
     * @return string
     */
    public static function settingType( $value )
    {
        if ( is_array( $value ) )
            return 'array';
        if ( is_numeric( $value ) )
            return 'numeric';
        if ( $value == 'true' || $value == 'false' )
            return 'true/false';
        if ( $value == 'enabled' || $value == 'disabled' )
            return 'enable/disable';
        return 'string';
    }

    /**
     * The settings whose effective value differs between two chains (two siteaccesses of the same file).
     *
     * @param expSettingsChain $a
     * @param expSettingsChain $b
     * @return array[] block, name, a (result or null), b (result or null), in (both, a, b), in the order of $a
     *                 and then the settings only $b has
     */
    public static function compare( expSettingsChain $a, expSettingsChain $b )
    {
        $diff = array();
        $sb = $b->settings();
        foreach ( $a->settings() as $block => $settings )
        {
            foreach ( $settings as $name => $setting )
            {
                $other = isset( $sb[$block][$name] ) ? $sb[$block][$name] : null;
                if ( $other !== null && $other['value'] === $setting['value'] )
                    continue;
                $diff[] = array( 'block' => $block, 'name' => $name, 'a' => $setting, 'b' => $other,
                                 'in' => $other === null ? 'a' : 'both' );
            }
        }
        $sa = $a->settings();
        foreach ( $sb as $block => $settings )
        {
            foreach ( $settings as $name => $setting )
            {
                if ( !isset( $sa[$block][$name] ) )
                    $diff[] = array( 'block' => $block, 'name' => $name, 'a' => null, 'b' => $setting, 'in' => 'b' );
            }
        }
        return $diff;
    }
}
