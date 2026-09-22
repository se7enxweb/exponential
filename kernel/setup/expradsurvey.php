<?php
/**
 * A live survey of what this installation can be extended at.
 *
 * The catalogue beside this is hand written: forty odd points, each explained,
 * each with a tool where there is one. It is the map somebody reads.
 *
 * This is the territory. It walks every ini file the installation has - the
 * kernel's, and every extension's - and reports every setting that names a
 * class, every directory a handler is looked for in, and every interface the
 * kernel declares for somebody else to implement. Nothing here is written
 * down in advance, so nothing here can go stale, and an extension installed
 * this morning appears in it this afternoon.
 *
 * It is deliberately generous about what counts: a setting naming a class that
 * does not exist is reported too, because a broken handler registration is
 * worth seeing. Whoever reads it can tell the difference.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */


if ( !class_exists( 'expRADSurvey', false ) ) {
class expRADSurvey
{
    /**
     * Variable names that mean "this names a class you could replace".
     *
     * Matched on the whole name with the array index taken off, case
     * insensitively. Deliberately a list rather than a pattern: a pattern wide
     * enough to catch all of these catches half the ini files as well.
     */
    const HANDLER_PATTERN = '/(handler|handlerclass|handleralias|implementation|implementationalias'
                          . '|engine|backend|transport|transportalias|provider|providerclass'
                          . '|renderer|rendererclass|listener|formatter|filterclass|filterclasses'
                          . '|routesettingimpl|authenticationstyle|switcherclass|queuereader'
                          . '|schemahandlerclasses|metadataextractor|outputformatter|purgeclass'
                          . '|extractor|factory|storageclass|cacheclass|controllerclass)$/i';

    /**
     * A value that could be a class name.
     *
     * Deliberately not restricted to names beginning eZ: a third party handler
     * is called whatever its author called it, and those are exactly the ones
     * worth seeing. What this cannot do is tell a class name from an alias -
     * several of these settings take one, some take either - so the page says
     * what the value is and whether a class of that name is declared, and does
     * not claim the two are the same thing.
     */
    const CLASS_PATTERN = '/^[A-Za-z_][A-Za-z0-9_]{2,}$/';

    /**
     * Values that are plainly not a class or an alias for one.
     *
     * A handful of settings matched by the name pattern take a switch rather
     * than a name, and reporting "Handler=enabled" as a possible class is just
     * noise on the page.
     */
    const NOT_A_NAME = '/^(enabled|disabled|true|false|yes|no|on|off|none|default|auto|[0-9]+)$/i';

    /**
     * Everything, cached for the life of the request.
     *
     * @var array|null
     */
    protected static $Survey = null;

    /**
     * The whole survey.
     *
     * @return array with keys settings, repositories, contracts, files, counts
     */
    public static function survey()
    {
        if ( self::$Survey !== null )
            return self::$Survey;

        $files = self::iniFiles();

        $settings     = array();
        $repositories = array();

        foreach ( $files as $file )
        {
            $parsed = self::parseIni( $file['path'] );

            foreach ( $parsed as $section => $variables )
                foreach ( $variables as $variable => $values )
                {
                    $bare = preg_replace( '/\[.*$/', '', $variable );

                    // Everything that names a place to look, or an extension to
                    // look in. The last four arrived with 6.0 and were missing
                    // here until the catalogue was written up: a survey that
                    // does not know about a root is a survey of the wrong tree.
                    if ( preg_match( '/^(RepositoryDirectories|ExtensionDirectories|ExtensionRepositories'
                                   . '|ExtensionAutoloadPath|ExtensionDirectory|AdditionalExtensionDirectories'
                                   . '|ActiveExtensions|ActiveAccessExtensions|DesignExtensions'
                                   . '|IconExtensions|TranslationExtensions|ModuleList|AdditionalThemeList'
                                   . '|DesignLocationCache|AutoloadPathList)$/i', $bare ) )
                    {
                        $repositories[] = array(
                            'ini'      => $file['ini'],
                            'origin'   => $file['origin'],
                            'section'  => $section,
                            'variable' => $bare,
                            'values'   => $values );

                        continue;
                    }

                    $named = preg_match( self::HANDLER_PATTERN, $bare ) === 1;

                    foreach ( $values as $index => $value )
                    {
                        if ( !is_string( $value ) || $value === '' )
                            continue;

                        if ( preg_match( self::NOT_A_NAME, $value ) )
                            continue;

                        $looksLikeClass = preg_match( self::CLASS_PATTERN, $value ) === 1;
                        $source         = $looksLikeClass ? self::fileOf( $value ) : '';

                        // Several of these settings take an alias rather than a
                        // class, and the two cannot be told apart by looking at
                        // the setting. They can be told apart by looking at the
                        // value: every class in this system has a capital in it
                        // somewhere and every alias is one lower case word. So a
                        // lower case value that names no class is an alias doing
                        // its job, and only a class shaped one is worth a second
                        // look. Saying otherwise would report a dozen perfectly
                        // healthy settings as broken.
                        $shape = $source !== ''       ? 'class'
                               : ( $looksLikeClass && preg_match( '/[A-Z]/', $value ) ? 'unknown' : 'alias' );

                        // Two ways in. Either the variable is named like one
                        // that takes a handler, or the value is a class this
                        // installation really has - which is the stronger
                        // signal of the two and catches every setting whose
                        // author named it something nobody would guess.
                        if ( !$named && $source === '' )
                            continue;

                        $settings[] = array(
                            'ini'      => $file['ini'],
                            'origin'   => $file['origin'],
                            'section'  => $section,
                            'variable' => $variable,
                            'bare'     => $bare,
                            'value'    => $value,
                            'is_class' => $looksLikeClass,
                            'shape'    => $shape,
                            'exists'   => $source !== '',
                            'source'   => $source,
                            'by_name'  => $named );
                    }
                }
        }

        // Two settings can name the same class in the same place from two files,
        // because an extension appended to what the kernel said. The later one
        // is what is in force, and the earlier is noise.
        $settings = self::lastWins( $settings );

        usort( $settings, array( __CLASS__, 'compareSettings' ) );
        usort( $repositories, array( __CLASS__, 'compareRepositories' ) );

        // contracts() fills self::$Events as it walks, so it runs first.
        $contracts = self::contracts();
        $modules   = self::modules();
        $callables = self::templateCallables();
        $overrides = self::overrides();
        $replaced  = self::kernelOverrides();
        $events    = (array) self::$Events;

        $views    = 0;
        $policies = 0;
        foreach ( $modules as $module )
        {
            $views    += count( $module['views'] );
            $policies += count( $module['functions'] );
        }

        self::$Survey = array(
            'settings'     => $settings,
            'repositories' => $repositories,
            'contracts'    => $contracts,
            'modules'      => $modules,
            'callables'    => $callables,
            'events'       => $events,
            'overrides'    => $overrides,
            'replaced'     => $replaced,
            'files'        => $files,
            'counts'       => array(
                'ini'          => count( $files ),
                'settings'     => count( $settings ),
                'live'         => count( array_filter( $settings, array( __CLASS__, 'isLive' ) ) ),
                'broken'       => count( array_filter( $settings, array( __CLASS__, 'isBroken' ) ) ),
                'aliases'      => count( array_filter( $settings, array( __CLASS__, 'isAlias' ) ) ),
                'repositories' => count( $repositories ),
                'contracts'    => count( $contracts ),
                'implemented'  => count( array_filter( $contracts, array( __CLASS__, 'isImplemented' ) ) ),
                'modules'      => count( $modules ),
                'views'        => $views,
                'policies'     => $policies,
                'callables'    => count( $callables ),
                'operators'    => count( array_filter( $callables, function ( $c ) { return $c['kind'] === 'operator'; } ) ),
                'functions'    => count( array_filter( $callables, function ( $c ) { return $c['kind'] === 'function'; } ) ),
                'events'       => count( $events ),
                'overrides'    => count( $overrides ),
                'replaced'     => count( $replaced ) ) );

        self::$Survey['counts']['total'] = self::$Survey['counts']['settings']
                                         + self::$Survey['counts']['repositories']
                                         + self::$Survey['counts']['contracts']
                                         + self::$Survey['counts']['views']
                                         + self::$Survey['counts']['callables']
                                         + self::$Survey['counts']['events']
                                         + self::$Survey['counts']['overrides']
                                         + self::$Survey['counts']['replaced'];

        return self::$Survey;
    }

    /**
     * Whether a setting names a class that is really there.
     *
     * @param array $setting
     * @return bool
     */
    public static function isLive( array $setting )
    {
        return $setting['exists'];
    }

    /**
     * Whether it names a class that is not.
     *
     * @param array $setting
     * @return bool
     */
    public static function isBroken( array $setting )
    {
        return $setting['shape'] === 'unknown';
    }

    /**
     * Whether a setting takes an alias that something else resolves.
     *
     * @param array $setting
     * @return bool
     */
    public static function isAlias( array $setting )
    {
        return $setting['shape'] === 'alias';
    }

    /**
     * Whether anything in this installation implements a contract.
     *
     * @param array $contract
     * @return bool
     */
    public static function isImplemented( array $contract )
    {
        return count( $contract['implementations'] ) > 0;
    }

    /**
     * Every ini file this installation has: the kernel's, then the extensions'.
     *
     * In that order deliberately. Later files override earlier ones, and the
     * survey says which file won.
     *
     * @return array of array( path, ini, origin )
     */
    public static function iniFiles()
    {
        $files = array();

        foreach ( (array) glob( 'settings/*.ini' ) as $path )
            $files[] = array( 'path' => $path, 'ini' => basename( $path ), 'origin' => 'kernel' );

        foreach ( (array) glob( 'settings/override/*.ini.append.php' ) as $path )
            $files[] = array( 'path'   => $path,
                              'ini'    => str_replace( '.append.php', '', basename( $path ) ),
                              'origin' => 'override' );

        // An extension may keep settings in settings/ or in a siteaccess
        // directory under it. Both are read; a glob two deep covers both
        // without walking the whole tree.
        foreach ( (array) glob( 'extension/*/settings/*.ini*' ) as $path )
            $files[] = self::extensionFile( $path );

        foreach ( (array) glob( 'extension/*/settings/*/*.ini*' ) as $path )
            $files[] = self::extensionFile( $path );

        return $files;
    }

    /**
     * One extension ini file, named by the extension it belongs to.
     *
     * @param string $path
     * @return array
     */
    protected static function extensionFile( $path )
    {
        $bits = explode( '/', $path );

        return array( 'path'   => $path,
                      'ini'    => preg_replace( '/\.append(\.php)?$/', '', basename( $path ) ),
                      'origin' => isset( $bits[1] ) ? $bits[1] : 'extension' );
    }

    /**
     * One ini file, as section to variable to list of values.
     *
     * eZINI is not used here on purpose: it merges every file that contributes
     * to a setting and this survey is about which file said what. It is also
     * asked for files that are not on its search path at all.
     *
     * @param string $path
     * @return array
     */
    public static function parseIni( $path )
    {
        if ( !is_file( $path ) || !is_readable( $path ) )
            return array();

        // A runaway file would be read into memory whole, and some sites keep
        // very large generated ini files.
        if ( filesize( $path ) > 2097152 )
            return array();

        $contents = file_get_contents( $path );

        if ( $contents === false )
            return array();

        $parsed  = array();
        $section = '';

        foreach ( preg_split( '/\r\n|\r|\n/', $contents ) as $line )
        {
            $line = trim( $line );

            if ( $line === '' || $line[0] === '#' || $line[0] === ';' )
                continue;

            // The php wrapper an .append.php file carries, and the comment that
            // opens it. Neither is a setting.
            if ( strpos( $line, '<?php' ) === 0 || strpos( $line, '*/' ) === 0 || strpos( $line, '?>' ) === 0 )
                continue;

            if ( $line[0] === '[' && substr( $line, -1 ) === ']' )
            {
                $section = substr( $line, 1, -1 );
                continue;
            }

            if ( $section === '' )
                continue;

            $equals = strpos( $line, '=' );

            // "Key[]" on its own is eZ for "forget what anything before said
            // about this array". It declares the setting without giving it a
            // value, and a survey that skipped it would miss every array
            // setting an installation has not filled in - which is most of
            // the directories a handler is looked for in.
            if ( $equals === false )
            {
                if ( !preg_match( '/^[A-Za-z][A-Za-z0-9_]*\[\]$/', $line ) )
                    continue;

                if ( !isset( $parsed[$section][$line] ) )
                    $parsed[$section][$line] = array();

                continue;
            }

            $variable = trim( substr( $line, 0, $equals ) );
            $value    = trim( substr( $line, $equals + 1 ) );

            if ( $variable === '' || !preg_match( '/^[A-Za-z][A-Za-z0-9_]*(\[[^\]]*\])?$/', $variable ) )
                continue;

            $parsed[$section][$variable][] = $value;
        }

        return $parsed;
    }

    /**
     * The autoload maps, as class name to path.
     *
     * @var array|null
     */
    protected static $Classes = null;

    /**
     * Where a class is declared, relative to the installation.
     *
     * Read out of the autoload maps rather than by asking php. class_exists()
     * would load the file, and a survey must not be able to bring the site
     * down because one installed extension has a class php refuses - which is
     * exactly the sort of thing a survey ought to be able to report.
     *
     * @param string $class
     * @return string, empty when nothing declares it
     */
    public static function fileOf( $class )
    {
        if ( self::$Classes === null )
        {
            self::$Classes = array();

            foreach ( array( 'autoload/ezp_kernel.php',
                             'var/autoload/ezp_extension.php',
                             'var/autoload/ezp_override.php' ) as $map )
            {
                if ( !is_file( $map ) )
                    continue;

                $loaded = include $map;

                if ( is_array( $loaded ) )
                    foreach ( $loaded as $name => $path )
                        self::$Classes[strtolower( $name )] = $path;
            }

            // Anything already in memory counts too, whether a map mentions it
            // or not: a class declared inline is still a class.
            foreach ( array_merge( get_declared_classes(), get_declared_interfaces() ) as $name )
                if ( !isset( self::$Classes[strtolower( $name )] ) )
                    self::$Classes[strtolower( $name )] = '';
        }

        $key = strtolower( (string) $class );

        if ( !isset( self::$Classes[$key] ) )
            return '';

        // Declared but not in a map: say so rather than say nothing, because
        // the caller reads an empty string as "there is no such class".
        return self::$Classes[$key] !== '' ? self::$Classes[$key] : '(declared at runtime)';
    }

    /**
     * The last setting for each place wins, because that is what the kernel does.
     *
     * @param array $settings
     * @return array
     */
    protected static function lastWins( array $settings )
    {
        $byPlace = array();

        foreach ( $settings as $setting )
        {
            $place = $setting['ini'] . '|' . $setting['section'] . '|' . $setting['variable'] . '|' . $setting['value'];
            $byPlace[$place] = $setting;
        }

        return array_values( $byPlace );
    }

    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function compareSettings( $a, $b )
    {
        $order = strcmp( $a['ini'], $b['ini'] );
        if ( $order !== 0 )
            return $order;

        $order = strcmp( $a['section'], $b['section'] );
        if ( $order !== 0 )
            return $order;

        return strcmp( $a['variable'], $b['variable'] );
    }

    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function compareRepositories( $a, $b )
    {
        $order = strcmp( $a['ini'], $b['ini'] );

        return $order !== 0 ? $order : strcmp( $a['section'], $b['section'] );
    }

    // ── Contracts ────────────────────────────────────────────────────────────

    /**
     * Every interface and abstract class the kernel declares for somebody else.
     *
     * Read off disk rather than out of the autoload map, because the autoload
     * map says what exists and this wants to know where it was declared and
     * what declares that it implements it.
     *
     * @return array
     */
    public static function contracts()
    {
        $contracts = array();

        foreach ( self::sourceFiles() as $path )
        {
            $contents = @file_get_contents( $path );

            if ( $contents === false )
                continue;

            if ( !preg_match_all( '/^(abstract\s+class|interface)\s+([A-Za-z_][A-Za-z0-9_]*)/mi',
                                  $contents, $found, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) )
                continue;

            foreach ( $found as $match )
            {
                $name = $match[2][0];

                // An exception hierarchy is not an extension point: nobody
                // implements one to change what the system does.
                if ( preg_match( '/exception$/i', $name ) )
                    continue;

                $contracts[$name] = array(
                    'name'    => $name,
                    'kind'    => strtolower( $match[1][0] ) === 'interface' ? 'interface' : 'abstract class',
                    'source'  => $path,
                    'methods' => self::methodCount( self::bodyAt( $contents, $match[0][1] ) ),
                    'implementations' => array() );
            }
        }

        // Who implements each of them, and every event the kernel announces.
        // Both want a walk of the same files, so they share one: separately
        // they would be two passes over several thousand files for no reason.
        $names = count( $contracts )
                 ? '(' . implode( '|', array_map( 'preg_quote', array_keys( $contracts ) ) ) . ')'
                 : false;

        self::$Events = array();

        foreach ( self::sourceFiles( true ) as $path )
        {
            $contents = @file_get_contents( $path );

            if ( $contents === false )
                continue;

            if ( $names !== false
                 && preg_match_all( '/^class\s+([A-Za-z_][A-Za-z0-9_]*)\s+(?:extends|implements)\s+[^{]*\b' . $names . '\b/mi',
                                    $contents, $found, PREG_SET_ORDER ) )
                foreach ( $found as $match )
                    if ( isset( $contracts[$match[2]] ) && !in_array( $match[1], $contracts[$match[2]]['implementations'], true ) )
                        $contracts[$match[2]]['implementations'][] = $match[1];

            // Cheap enough to check before running the expensive pattern, and
            // most files have nothing to do with events.
            if ( strpos( $contents, 'ezpEvent' ) === false )
                continue;

            if ( !preg_match_all( "/ezpEvent::getInstance\(\)\s*->\s*(notify|filter)\(\s*'([^']+)'/s",
                                  $contents, $found, PREG_SET_ORDER ) )
                continue;

            foreach ( $found as $match )
            {
                $event = $match[2];

                if ( !isset( self::$Events[$event] ) )
                    self::$Events[$event] = array( 'event' => $event,
                                                   'kind'  => $match[1],
                                                   'where' => array() );

                // A filter and a notify of the same name is possible and worth
                // seeing: what a listener returns matters for one and not the
                // other, so the stricter of the two is what a listener has to
                // satisfy.
                if ( $match[1] === 'filter' )
                    self::$Events[$event]['kind'] = 'filter';

                if ( !in_array( $path, self::$Events[$event]['where'], true ) )
                    self::$Events[$event]['where'][] = $path;
            }
        }

        ksort( self::$Events );
        ksort( $contracts );

        return array_values( $contracts );
    }

    /**
     * How many methods a contract asks for.
     *
     * Counted by reading the declaration rather than by reflecting on it, for
     * the same reason as fileOf(): nothing here may load a file.
     *
     * @param string $body the source from the declaration onwards
     * @return int
     */
    protected static function methodCount( $body )
    {
        return preg_match_all( '/^\s*(?:abstract\s+|public\s+|protected\s+|static\s+|final\s+)*function\s+&?[A-Za-z_]/mi',
                               $body );
    }

    /**
     * One declaration, from its opening brace to the matching one.
     *
     * @param string $contents
     * @param int $from offset of the declaration
     * @return string
     */
    protected static function bodyAt( $contents, $from )
    {
        $open = strpos( $contents, '{', $from );

        if ( $open === false )
            return '';

        $depth  = 0;
        $length = strlen( $contents );

        for ( $i = $open; $i < $length; $i++ )
        {
            if ( $contents[$i] === '{' )
                $depth++;
            else if ( $contents[$i] === '}' && --$depth === 0 )
                return substr( $contents, $open, $i - $open );
        }

        return substr( $contents, $open );
    }

    /**
     * The php files worth reading.
     *
     * @param bool $withExtensions also the extensions', for finding who
     *        implements what
     * @return array of string
     */
    public static function sourceFiles( $withExtensions = false )
    {
        $roots = array( 'kernel', 'lib' );

        if ( $withExtensions )
            $roots[] = 'extension';

        $files = array();

        foreach ( $roots as $root )
        {
            if ( !is_dir( $root ) )
                continue;

            $directory = new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS );
            $iterator  = new RecursiveIteratorIterator( $directory );

            foreach ( $iterator as $file )
            {
                if ( substr( $file->getFilename(), -4 ) !== '.php' )
                    continue;

                // Neither of these is anybody's extension point, and between
                // them they are a large part of the tree.
                $path = $file->getPathname();
                if ( strpos( $path, '/tests/' ) !== false || strpos( $path, '/vendor/' ) !== false )
                    continue;

                $files[] = $path;
            }
        }

        return $files;
    }

    /**
     * Every event the kernel announces, collected during the contract walk.
     *
     * @var array|null
     */
    protected static $Events = null;

    /**
     * Every event something can listen to.
     *
     * Swept out of the source rather than listed, because a list of these goes
     * out of date the first time somebody adds one and nobody notices for a
     * year.
     *
     * @return array
     */
    public static function events()
    {
        if ( self::$Events === null )
            self::survey();

        return (array) self::$Events;
    }

    // ── What a template can call ─────────────────────────────────────────────

    /**
     * Every operator and function the template engine has been taught.
     *
     * Read out of the autoload arrays, which is where they are really declared
     * - there is no ini listing them, and asking the engine would mean building
     * it. The array is read by including the file in a scope of its own, the
     * same way the engine reads it.
     *
     * @return array
     */
    public static function templateCallables()
    {
        $callables = array();

        foreach ( self::templateAutoloadFiles() as $path )
        {
            $declared = self::includeInScope( $path );

            foreach ( array( 'eZTemplateOperatorArray' => 'operator',
                             'eZTemplateFunctionArray' => 'function' ) as $variable => $kind )
            {
                if ( !isset( $declared[$variable] ) || !is_array( $declared[$variable] ) )
                    continue;

                foreach ( $declared[$variable] as $entry )
                {
                    if ( !is_array( $entry ) )
                        continue;

                    $names = $kind === 'operator'
                             ? ( isset( $entry['operator_names'] ) ? $entry['operator_names'] : array() )
                             : ( isset( $entry['function_names'] ) ? $entry['function_names'] : array() );

                    foreach ( (array) $names as $name )
                    {
                        if ( !is_string( $name ) || $name === '' )
                            continue;

                        $callables[] = array(
                            'name'   => $name,
                            'kind'   => $kind,
                            'class'  => isset( $entry['class'] ) && is_string( $entry['class'] )
                                        ? $entry['class']
                                        : ( isset( $entry['function'] ) && is_string( $entry['function'] )
                                            ? $entry['function'] : '' ),
                            'script' => isset( $entry['script'] ) && is_string( $entry['script'] ) ? $entry['script'] : '',
                            'from'   => $path );
                    }
                }
            }
        }

        usort( $callables, function ( $a, $b ) {
            return $a['kind'] === $b['kind'] ? strcmp( $a['name'], $b['name'] ) : strcmp( $a['kind'], $b['kind'] );
        } );

        return $callables;
    }

    /**
     * Where template autoload files are.
     *
     * @return array of string
     */
    protected static function templateAutoloadFiles()
    {
        return array_merge(
            (array) glob( 'kernel/*/eztemplateautoload.php' ),
            (array) glob( 'lib/*/*/eztemplateautoload.php' ),
            (array) glob( 'extension/*/autoloads/eztemplateautoload.php' ) );
    }

    // ── Template overrides, and kernel classes replaced ──────────────────────

    /**
     * Every template override registered on this installation.
     *
     * An override is a point too: it is a place a template has already been
     * replaced, which is both a thing to learn from and a thing to collide
     * with.
     *
     * @return array
     */
    public static function overrides()
    {
        $overrides = array();

        $files = array_merge(
            (array) glob( 'settings/override.ini' ),
            (array) glob( 'settings/override/override.ini.append.php' ),
            (array) glob( 'settings/siteaccess/*/override.ini.append.php' ),
            (array) glob( 'extension/*/settings/override.ini*' ),
            (array) glob( 'extension/*/settings/*/override.ini*' ) );

        foreach ( $files as $path )
            foreach ( self::parseIni( $path ) as $section => $variables )
            {
                $first = function ( $name ) use ( $variables ) {
                    return isset( $variables[$name][0] ) ? $variables[$name][0] : '';
                };

                $overrides[] = array(
                    'name'   => $section,
                    'source' => $first( 'Source' ),
                    'match'  => $first( 'MatchFile' ),
                    'subdir' => $first( 'Subdir' ),
                    'from'   => $path );
            }

        usort( $overrides, function ( $a, $b ) { return strcmp( $a['name'], $b['name'] ); } );

        return $overrides;
    }

    /**
     * Kernel classes this installation has replaced outright.
     *
     * The heaviest mechanism there is, and the one worth knowing about before
     * anything else is diagnosed: a replaced kernel class is not in the kernel
     * any more, whatever the kernel source says.
     *
     * @return array
     */
    public static function kernelOverrides()
    {
        $map = is_file( 'var/autoload/ezp_override.php' ) ? @include 'var/autoload/ezp_override.php' : false;

        if ( !is_array( $map ) )
            return array();

        $overrides = array();

        foreach ( $map as $class => $path )
            $overrides[] = array( 'class'  => (string) $class,
                                  'path'   => (string) $path,
                                  'kernel' => self::kernelFileOf( (string) $class ) );

        usort( $overrides, function ( $a, $b ) { return strcmp( $a['class'], $b['class'] ); } );

        return $overrides;
    }

    /**
     * Where the kernel's own copy of a class is, for something that overrides it.
     *
     * @param string $class
     * @return string
     */
    protected static function kernelFileOf( $class )
    {
        $map = is_file( 'autoload/ezp_kernel.php' ) ? @include 'autoload/ezp_kernel.php' : false;

        if ( !is_array( $map ) )
            return '';

        foreach ( $map as $name => $path )
            if ( strcasecmp( (string) $name, $class ) === 0 )
                return (string) $path;

        return '';
    }

    // ── Modules and their views ──────────────────────────────────────────────

    /**
     * Every module, with the views and policies it declares.
     *
     * A module view is the largest extension surface there is: every one of
     * them can be replaced by an extension carrying a module of the same name,
     * and every one of them is a place a view of your own can be added. They
     * are counted here because forty seven curated points is not the size of
     * this, and neither is a hundred.
     *
     * Read by including each module.php in a scope of its own. That is what the
     * kernel does with them too; they are declarations, not code that runs.
     *
     * @return array
     */
    public static function modules()
    {
        $modules = array();

        foreach ( self::modulePaths() as $origin => $paths )
            foreach ( $paths as $path )
            {
                $module = self::readModule( $path );

                if ( $module === false )
                    continue;

                $module['origin'] = $origin;
                $modules[] = $module;
            }

        usort( $modules, function ( $a, $b ) { return strcmp( $a['name'], $b['name'] ); } );

        return $modules;
    }

    /**
     * Where module.php files are, by where they came from.
     *
     * @return array
     */
    protected static function modulePaths()
    {
        return array(
            // kernel/<module>/ is where most of them are, and
            // kernel/private/modules/<module>/ is where the rest are. Globbing
            // only the first missed oauth, oauthadmin and switchlanguage - and
            // the health check beside this then reported all three as modules
            // that are listed and not there, which they are not.
            'kernel'    => array_merge( (array) glob( 'kernel/*/module.php' ),
                                        (array) glob( 'kernel/private/modules/*/module.php' ) ),
            'extension' => array_merge( (array) glob( 'extension/*/modules/*/module.php' ),
                                        (array) glob( 'extension/*/module/*/module.php' ) ) );
    }

    /**
     * One module.php, read rather than run.
     *
     * @param string $path
     * @return array|false
     */
    protected static function readModule( $path )
    {
        if ( !is_file( $path ) || !is_readable( $path ) )
            return false;

        // In a function of its own so that the variables a module declares
        // cannot reach anything here, and so that two modules cannot see each
        // other's.
        $declared = self::includeInScope( $path );

        if ( !isset( $declared['Module'] ) || !is_array( $declared['Module'] ) )
            return false;

        // The directory is the identifier: it is what a url says and what
        // fetch() and module.ini name. The 'name' a module declares is often a
        // title for a person to read - "CJW Newsletter" - and using that would
        // make this list agree with nothing else in the system.
        $bits = explode( '/', $path );
        $name = $bits[count( $bits ) - 2];
        $title = isset( $declared['Module']['name'] ) && is_string( $declared['Module']['name'] )
                 ? $declared['Module']['name'] : $name;

        $views = array();
        if ( isset( $declared['ViewList'] ) && is_array( $declared['ViewList'] ) )
            foreach ( $declared['ViewList'] as $view => $definition )
            {
                if ( !is_string( $view ) )
                    continue;

                $views[] = array(
                    'name'       => $view,
                    'script'     => isset( $definition['script'] ) && is_string( $definition['script'] )
                                    ? $definition['script'] : '',
                    'functions'  => isset( $definition['functions'] ) && is_array( $definition['functions'] )
                                    ? array_values( array_filter( $definition['functions'], 'is_string' ) )
                                    : array(),
                    'parameters' => isset( $definition['params'] ) && is_array( $definition['params'] )
                                    ? count( $definition['params'] ) : 0,
                    'unordered'  => isset( $definition['unordered_params'] ) && is_array( $definition['unordered_params'] )
                                    ? count( $definition['unordered_params'] ) : 0 );
            }

        $functions = array();
        if ( isset( $declared['FunctionList'] ) && is_array( $declared['FunctionList'] ) )
            foreach ( array_keys( $declared['FunctionList'] ) as $function )
                if ( is_string( $function ) )
                    $functions[] = $function;

        return array(
            'name'      => $name,
            'title'     => $title,
            'path'      => dirname( $path ),
            'views'     => $views,
            'functions' => $functions,
            'fetches'   => self::fetchesOf( dirname( $path ) ) );
    }

    /**
     * The fetch functions a module offers, if it offers any.
     *
     * @param string $directory
     * @return array of string
     */
    protected static function fetchesOf( $directory )
    {
        $path = $directory . '/function_definition.php';

        if ( !is_file( $path ) )
            return array();

        $declared = self::includeInScope( $path );

        if ( !isset( $declared['FunctionList'] ) || !is_array( $declared['FunctionList'] ) )
            return array();

        return array_values( array_filter( array_keys( $declared['FunctionList'] ), 'is_string' ) );
    }

    /**
     * Include a declaration file and hand back only what it declared.
     *
     * @param string $path
     * @return array
     */
    protected static function includeInScope( $path )
    {
        $before = get_defined_vars();

        // A module.php sometimes reaches for these. Declaring them means a
        // module that expects them does not warn, and a module that does not
        // is unaffected.
        $Module = null;
        $ViewList = array();
        $FunctionList = array();

        try
        {
            include $path;
        }
        catch ( Exception $e )
        {
            return array();
        }
        catch ( Error $e )
        {
            return array();
        }

        $after = get_defined_vars();

        unset( $after['before'], $after['path'], $after['e'] );

        return $after;
    }

    // ── Grouping, for the page ───────────────────────────────────────────────

    /**
     * The settings, grouped by the ini file they are in.
     *
     * @return array
     */
    public static function settingsByFile()
    {
        $survey = self::survey();
        $byFile = array();

        foreach ( $survey['settings'] as $setting )
            $byFile[$setting['ini']][] = $setting;

        $groups = array();
        foreach ( $byFile as $ini => $settings )
            $groups[] = array( 'ini'      => $ini,
                               'count'    => count( $settings ),
                               'settings' => $settings );

        return $groups;
    }

    /**
     * The contracts, most demanding first.
     *
     * A contract with many methods and no implementations outside the kernel is
     * where the hard extension points are.
     *
     * @return array
     */
    public static function contractsByWeight()
    {
        $survey    = self::survey();
        $contracts = $survey['contracts'];

        usort( $contracts, function ( $a, $b ) {
            if ( $a['methods'] === $b['methods'] )
                return strcmp( $a['name'], $b['name'] );

            return $a['methods'] < $b['methods'] ? 1 : -1;
        } );

        return $contracts;
    }
}
}


?>
