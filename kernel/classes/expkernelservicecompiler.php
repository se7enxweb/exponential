<?php
/**
 * File containing the expKernelServiceCompiler class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Builds, checks and removes the compiled kernel service container (var/cache/kernelservices/).
 *
 * The compiled container is one PHP file per siteaccess plus an index, each an array returned by the file (served
 * from the opcode cache):
 *
 *   index.php       format, generation, stamp, the recorded 'default' extension pass (override directories before
 *                   and after, the settings it read) and the extension caches after it
 *   <siteaccess>.php  the recorded 'access' pass of that siteaccess (before, after, settings), the extension caches,
 *                   and the service definitions and tags of kernelservices.ini as that siteaccess sees them
 *
 * Nothing in it is computed by imitation: compile() starts a separate PHP process (bin/php/services.php with
 * --kernel-services-record) that boots the real web kernel once per siteaccess in recording mode and writes what
 * the two eZExtension::activateExtensions() passes did. A recording is used by a request only when that request
 * starts from exactly the recorded state (expKernelServices::applyExtensionStage()).
 *
 * compile() runs on the command line only: exp:services:compile, the deploy (through ezcache.php --clear-tag=ini)
 * and ezcache.php --clear-tag=ini / --clear-all. A cache clear from the administration removes the files; with
 * AutoRebuild=enabled each siteaccess records itself again on its next web request.
 *
 * @since 6.0.15
 * @package kernel
 */
class expKernelServiceCompiler
{
    /** Marker of the result line the recording process prints */
    const RESULT_MARKER = 'EXP-KERNEL-SERVICES-RESULT ';

    /** The option that makes bin/php/services.php the recording process */
    const RECORD_OPTION = '--kernel-services-record';

    // ----------------------------------------------------------------------------------------------------------
    // Definitions
    // ----------------------------------------------------------------------------------------------------------

    /**
     * The service definitions and tags of a kernelservices.ini.
     *
     * @param eZINI $ini
     * @return array array( 'definitions' => id => definition, 'tags' => tag => ids, 'declared_tags' => list,
     *               'problems' => list of array( level, id, message ) )
     */
    public static function definitionsFromINI( eZINI $ini )
    {
        $definitions = array();
        $tags = array();
        $problems = array();
        foreach ( $ini->groups() as $group => $values )
        {
            if ( strncmp( $group, 'Service_', 8 ) !== 0 || !is_array( $values ) )
                continue;
            $id = substr( $group, 8 );
            if ( !preg_match( '/^[A-Za-z0-9_][A-Za-z0-9_.-]*$/', $id ) )
            {
                $problems[] = array( 'error', $id, "[$group]: a service id is letters, digits, '_', '.' and '-'" );
                continue;
            }
            $enabled = isset( $values['Enabled'] ) ? strtolower( trim( (string)$values['Enabled'] ) ) : 'true';
            if ( in_array( $enabled, array( 'false', 'disabled', 'no', '0' ), true ) )
                continue;

            $lazy = isset( $values['Lazy'] ) ? strtolower( trim( (string)$values['Lazy'] ) ) : 'true';
            $definition = array(
                'class' => isset( $values['Class'] ) ? trim( (string)$values['Class'] ) : '',
                'factory' => isset( $values['Factory'] ) ? trim( (string)$values['Factory'] ) : '',
                'arguments' => isset( $values['Arguments'] ) ? self::listValue( $values['Arguments'] ) : array(),
                'scope' => isset( $values['Scope'] ) ? strtolower( trim( (string)$values['Scope'] ) ) : expKernelServiceScope::REQUEST,
                'lazy' => !in_array( $lazy, array( 'false', 'disabled', 'no', '0' ), true ),
                'tags' => isset( $values['Tags'] ) ? array_values( array_unique( self::listValue( $values['Tags'] ) ) ) : array(),
                'description' => isset( $values['Description'] ) ? trim( (string)$values['Description'] ) : '',
            );
            $definitions[$id] = $definition;
            foreach ( $definition['tags'] as $tag )
                $tags[$tag][] = $id;
        }

        $declared = array();
        if ( $ini->hasVariable( 'Tags', 'TagList' ) )
            $declared = array_values( array_unique( self::listValue( $ini->variable( 'Tags', 'TagList' ) ) ) );

        return array( 'definitions' => $definitions, 'tags' => $tags, 'declared_tags' => $declared, 'problems' => $problems );
    }

    /**
     * Checks definitions: known scope, class or factory that exists, arguments that name services that exist, no
     * cycles, no worker-scoped service that takes a request-scoped or prototype one (it would keep one request's
     * object for every later request), ini.<name> ids that are eZINI, tags that are declared in [Tags] TagList.
     *
     * @param array $read As definitionsFromINI() returns
     * @return array list of array( level (error|warning), id, message )
     */
    public static function validate( array $read )
    {
        $definitions = $read['definitions'];
        $problems = isset( $read['problems'] ) ? $read['problems'] : array();

        foreach ( $definitions as $id => $definition )
        {
            if ( !expKernelServiceScope::isValid( $definition['scope'] ) )
                $problems[] = array( 'error', $id, "unknown Scope '{$definition['scope']}' (worker, request or prototype)" );

            if ( expKernelServiceContainer::iniFileName( $id ) !== null )
            {
                if ( strcasecmp( $definition['class'], 'eZINI' ) !== 0 )
                    $problems[] = array( 'error', $id, "an ini.<name> service is always eZINI::instance( '<name>.ini' ); Class must be eZINI" );
                continue;
            }

            if ( $definition['factory'] !== '' )
            {
                $parts = explode( '::', $definition['factory'], 2 );
                if ( count( $parts ) !== 2 || !class_exists( $parts[0] ) || !method_exists( $parts[0], $parts[1] ) )
                    $problems[] = array( 'error', $id, "Factory {$definition['factory']} does not exist" );
            }
            else if ( $definition['class'] === '' )
            {
                $problems[] = array( 'error', $id, 'neither Class nor Factory is set' );
            }
            if ( $definition['class'] !== '' && !class_exists( $definition['class'] ) && !interface_exists( $definition['class'] ) )
                $problems[] = array( 'error', $id, "Class {$definition['class']} not found (autoloads current?)" );

            foreach ( $definition['arguments'] as $argument )
            {
                if ( !is_string( $argument ) || $argument === '' || $argument[0] !== '@' || strncmp( $argument, '@@', 2 ) === 0 )
                    continue;
                $ref = substr( $argument, 1 );
                if ( !isset( $definitions[$ref] ) && expKernelServiceContainer::iniFileName( $ref ) === null )
                {
                    $problems[] = array( 'error', $id, "argument @$ref names no service" );
                    continue;
                }
                $refScope = isset( $definitions[$ref] ) ? $definitions[$ref]['scope'] : expKernelServiceScope::WORKER;
                if ( $definition['scope'] === expKernelServiceScope::WORKER && $refScope !== expKernelServiceScope::WORKER )
                    $problems[] = array( 'error', $id, "worker-scoped, but takes @$ref, which is $refScope-scoped: one request's object would be kept for every later one" );
            }

            foreach ( $definition['tags'] as $tag )
            {
                if ( !empty( $read['declared_tags'] ) && !in_array( $tag, $read['declared_tags'], true ) )
                    $problems[] = array( 'warning', $id, "tag '$tag' is not in [Tags] TagList" );
            }
        }

        foreach ( self::cycles( $definitions ) as $cycle )
            $problems[] = array( 'error', $cycle[0], 'depends on itself: ' . implode( ' -> ', $cycle ) );

        return $problems;
    }

    /**
     * The dependency cycles among definitions (through '@id' arguments).
     *
     * @param array $definitions
     * @return array list of id paths
     */
    public static function cycles( array $definitions )
    {
        $cycles = array();
        $state = array();
        $walk = function ( $id, array $path ) use ( &$walk, &$state, &$cycles, $definitions )
        {
            if ( isset( $state[$id] ) && $state[$id] === 'done' )
                return;
            if ( in_array( $id, $path, true ) )
            {
                $cycles[] = array_merge( array_slice( $path, array_search( $id, $path, true ) ), array( $id ) );
                return;
            }
            $path[] = $id;
            foreach ( $definitions[$id]['arguments'] as $argument )
            {
                if ( is_string( $argument ) && $argument !== '' && $argument[0] === '@' && strncmp( $argument, '@@', 2 ) !== 0 )
                {
                    $ref = substr( $argument, 1 );
                    if ( isset( $definitions[$ref] ) )
                        $walk( $ref, $path );
                }
            }
            $state[$id] = 'done';
        };
        foreach ( array_keys( $definitions ) as $id )
            $walk( $id, array() );
        return $cycles;
    }

    // ----------------------------------------------------------------------------------------------------------
    // The compiled files
    // ----------------------------------------------------------------------------------------------------------

    /**
     * A new generation: every compile and every recorded index gets one, and a worker that sees another one
     * forgets what it kept.
     *
     * @return string
     */
    public static function newGeneration()
    {
        return gmdate( 'YmdHis' ) . '-' . bin2hex( random_bytes( 4 ) );
    }

    /**
     * The index from a recording of the default pass.
     *
     * @param array $recording As expKernelServices records a stage
     * @param array $siteAccesses
     * @param string $source compile or request
     * @param string|null $generation
     * @return array
     */
    public static function buildIndex( array $recording, array $siteAccesses, $source, $generation = null )
    {
        return array(
            'format' => expKernelServices::FORMAT,
            'generation' => $generation !== null ? $generation : self::newGeneration(),
            'stamp' => 0,
            'created' => date( 'c' ),
            'source' => $source,
            'siteaccesses' => array_values( $siteAccesses ),
            'default' => array(
                'before' => $recording['before'],
                'fingerprint' => $recording['fingerprint'],
                'after' => $recording['after'],
            ),
            'extensions' => self::cachesValue( $recording['caches'] ),
        );
    }

    /**
     * The compiled data of one siteaccess from a recording of its access pass.
     *
     * @param string $siteAccess
     * @param array $recording
     * @param array $read As definitionsFromINI() returns
     * @param string $generation
     * @param string $source
     * @return array
     */
    public static function buildSiteAccess( $siteAccess, array $recording, array $read, $generation, $source )
    {
        return array(
            'format' => expKernelServices::FORMAT,
            'generation' => $generation,
            'stamp' => 0,
            'created' => date( 'c' ),
            'source' => $source,
            'siteaccess' => $siteAccess,
            'stage' => array(
                'before' => $recording['before'],
                'fingerprint' => $recording['fingerprint'],
                'after' => $recording['after'],
                'after_hash' => md5( serialize( $recording['after'] ) ),
            ),
            'extensions' => self::cachesValue( $recording['caches'] ),
            'services' => $read['definitions'],
            'tags' => $read['tags'],
        );
    }

    /**
     * Writes a compiled file: to a temporary name first, then renamed over the old one, its time set to the stamp
     * inside (expKernelServices::readCompiled() reads only a file whose time and stamp agree), readable by the
     * web server's user when written by root.
     *
     * @param string $file
     * @param array $data
     * @return bool
     */
    public static function writeCompiledFile( $file, array $data )
    {
        $dir = dirname( $file );
        if ( !is_dir( $dir ) )
        {
            if ( !eZDir::mkdir( $dir, false, true ) && !is_dir( $dir ) )
                return false;
            self::giveToSiteOwner( $dir );
        }

        $stamp = time();
        $data['stamp'] = $stamp;
        $php = "<?php\n// Compiled kernel services of Exponential (" . basename( $file ) . "), written "
             . date( 'c', $stamp ) . " by " . ( $data['source'] === 'request' ? 'a web request (AutoRebuild)' : 'exp:services:compile' )
             . ".\n// Do not edit: ./console exp:services:compile writes it again, ./console exp:services:check checks it.\n"
             . 'return ' . var_export( $data, true ) . ";\n";

        $tmp = $file . '.' . bin2hex( random_bytes( 4 ) ) . '.tmp';
        if ( @file_put_contents( $tmp, $php ) !== strlen( $php ) )
        {
            @unlink( $tmp );
            return false;
        }
        // as the INI cache files: PHP that is included, never writable by others (within EZP_FILE_MODE_MAX)
        @chmod( $tmp, eZFile::fileMode( 0644 ) );
        @touch( $tmp, $stamp );
        self::giveToSiteOwner( $tmp );
        if ( !@rename( $tmp, $file ) )
        {
            @unlink( $tmp );
            return false;
        }
        clearstatcache( true, $file );
        if ( function_exists( 'opcache_invalidate' ) )
            @opcache_invalidate( $file, true );
        return true;
    }

    /**
     * Removes the compiled container (every file of the cache directory that it writes), and empties the worker
     * store of this process.
     *
     * @return array list of the files removed
     */
    public static function clear()
    {
        $removed = array();
        $dir = expKernelServices::cacheDirectory();
        foreach ( glob( $dir . '/*.php' ) ?: array() as $file )
        {
            $name = basename( $file, '.php' );
            if ( $name !== 'index' && !expKernelServices::validSiteAccessName( $name ) )
                continue;
            if ( @unlink( $file ) )
            {
                $removed[] = $file;
                if ( function_exists( 'opcache_invalidate' ) )
                    @opcache_invalidate( $file, true );
            }
        }
        foreach ( glob( $dir . '/*.tmp' ) ?: array() as $file )
            @unlink( $file );
        eZINI::flushSharedStore();
        if ( expKernelServiceScope::hasWorker() )
            expKernelServiceScope::worker()->flush();
        return $removed;
    }

    /**
     * The purge function of the "kernelservices" cache item (tag ini): removes the compiled container, and on the
     * command line compiles it again (a web request does not start PHP processes; there AutoRebuild records each
     * siteaccess on its next request).
     *
     * @param array $cacheItem
     */
    public static function clearCacheItem( $cacheItem = array() )
    {
        self::clear();
        if ( ( PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg' ) && expKernelServices::mode() === 'compiled' )
        {
            $result = self::compile();
            if ( !$result['ok'] )
                eZDebug::writeWarning( 'Kernel services not compiled: ' . $result['message'], __METHOD__ );
        }
    }

    /**
     * Whether the "kernelservices" cache item is shown and cleared: not with Mode=disabled.
     *
     * @return bool
     */
    public static function cacheItemEnabled()
    {
        return expKernelServices::mode() !== 'disabled';
    }

    /**
     * The siteaccesses to compile: site.ini [SiteAccessSettings] AvailableSiteAccessList, the names that can be
     * file names.
     *
     * @return array
     */
    public static function siteAccessList()
    {
        $ini = eZINI::instance();
        $list = $ini->hasVariable( 'SiteAccessSettings', 'AvailableSiteAccessList' )
              ? self::listValue( $ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) ) : array();
        $valid = array();
        foreach ( array_unique( $list ) as $name )
        {
            if ( expKernelServices::validSiteAccessName( $name ) )
                $valid[] = $name;
        }
        return $valid;
    }

    /**
     * Compiles the container for every siteaccess (or those given) in a separate PHP process. Command line only.
     *
     * @param array $options 'siteaccesses' => list (default: siteAccessList()), 'php' => binary
     * @return array array( 'ok' => bool, 'message' => string, 'data' => array )
     */
    public static function compile( array $options = array() )
    {
        $mode = expKernelServices::mode();
        if ( $mode === 'disabled' )
        {
            $removed = self::clear();
            return array( 'ok' => true, 'message' => 'Mode=disabled: nothing compiled, ' . count( $removed ) . ' compiled file(s) removed',
                          'data' => array( 'mode' => $mode ) );
        }
        if ( PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg' && empty( $options['php'] ) )
            return array( 'ok' => false, 'message' => 'compiling starts a PHP process: command line only', 'data' => array() );

        $siteAccesses = isset( $options['siteaccesses'] ) && $options['siteaccesses'] ? $options['siteaccesses'] : self::siteAccessList();
        $siteAccesses = array_values( array_filter( $siteAccesses, array( 'expKernelServices', 'validSiteAccessName' ) ) );
        if ( !$siteAccesses )
            return array( 'ok' => false, 'message' => 'no siteaccess to compile (site.ini [SiteAccessSettings] AvailableSiteAccessList)', 'data' => array() );

        $root = expKernelServices::installationRoot();
        $php = !empty( $options['php'] ) ? $options['php'] : PHP_BINARY;
        $command = array( $php, $root . '/bin/php/services.php', self::RECORD_OPTION,
                          '--siteaccess=' . implode( ',', $siteAccesses ), '--generation=' . self::newGeneration() );
        if ( !empty( $options['partial'] ) )
            $command[] = '--partial';

        $start = microtime( true );
        $pipes = array();
        $process = proc_open( $command, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $root );
        if ( !is_resource( $process ) )
            return array( 'ok' => false, 'message' => 'could not start ' . $php, 'data' => array() );
        $out = stream_get_contents( $pipes[1] );
        $err = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $code = proc_close( $process );

        $result = null;
        foreach ( array_reverse( explode( "\n", (string)$out ) ) as $line )
        {
            if ( strncmp( $line, self::RESULT_MARKER, strlen( self::RESULT_MARKER ) ) === 0 )
            {
                $result = json_decode( substr( $line, strlen( self::RESULT_MARKER ) ), true );
                break;
            }
        }
        if ( !is_array( $result ) )
        {
            $tail = trim( substr( trim( $err . "\n" . $out ), -400 ) );
            return array( 'ok' => false, 'message' => 'the recording process ended without a result (exit ' . $code . ')' . ( $tail !== '' ? ': ' . $tail : '' ),
                          'data' => array() );
        }
        $result['data']['seconds'] = round( microtime( true ) - $start, 2 );
        // what this process kept for a worker belongs to the old generation
        eZINI::flushSharedStore();
        if ( expKernelServiceScope::hasWorker() )
            expKernelServiceScope::worker()->flush();
        return $result;
    }

    /**
     * The recording process (bin/php/services.php --kernel-services-record --siteaccess=a,b --generation=g):
     * boots the web kernel once per siteaccess in recording mode, as the first request of a process would, and
     * writes the compiled files. Prints one result line (RESULT_MARKER + JSON) and returns the exit code.
     *
     * @param array $argv
     * @return int
     */
    public static function recordMain( array $argv )
    {
        $siteAccesses = array();
        $generation = null;
        $partial = false;
        foreach ( $argv as $arg )
        {
            if ( strncmp( $arg, '--siteaccess=', 13 ) === 0 )
                $siteAccesses = array_filter( explode( ',', substr( $arg, 13 ) ), array( 'expKernelServices', 'validSiteAccessName' ) );
            else if ( strncmp( $arg, '--generation=', 13 ) === 0 && preg_match( '/^[0-9A-Za-z-]{1,64}$/', substr( $arg, 13 ) ) )
                $generation = substr( $arg, 13 );
            else if ( $arg === '--partial' )
                $partial = true;
        }
        $result = self::record( array_values( $siteAccesses ), $generation !== null ? $generation : self::newGeneration(), $partial );
        echo "\n" . self::RESULT_MARKER . json_encode( $result ) . "\n";
        return $result['ok'] ? 0 : 1;
    }

    /**
     * Records and writes (the body of recordMain(), in the recording process).
     *
     * @param array $siteAccesses
     * @param string $generation
     * @param bool $partial keep the files of the other siteaccesses (they belong to the old generation and are
     *        recorded again by their next web request with AutoRebuild)
     * @return array array( 'ok', 'message', 'data' )
     */
    public static function record( array $siteAccesses, $generation, $partial = false )
    {
        $root = expKernelServices::installationRoot();
        @chdir( $root );
        self::fakeWebRequest( $root );
        expKernelServices::reset( true );
        expKernelServices::startCompilerRecording();

        $problems = array();
        $recorded = array();
        $default = null;
        foreach ( $siteAccesses as $siteAccess )
        {
            // as the first request of a process: no settings, no siteaccess, no extension caches yet
            eZINI::resetAllInstances( true );
            unset( $GLOBALS['eZCurrentAccess'] );
            eZExtension::clearCaches();
            try
            {
                new ezpKernelWeb( array( 'siteaccess' => array( 'name' => $siteAccess, 'type' => eZSiteAccess::TYPE_DEFAULT,
                                                                'uri_part' => array() ) ) );
            }
            catch ( Throwable $e )
            {
                $problems[] = "$siteAccess: the kernel did not boot: " . $e->getMessage();
                expKernelServices::takeRecording();
                continue;
            }
            $stages = expKernelServices::takeRecording();
            if ( !isset( $stages['default'], $stages['access'] ) )
            {
                $problems[] = "$siteAccess: the extension passes were not recorded";
                continue;
            }
            if ( $default === null )
                $default = $stages['default'];
            else if ( $stages['default']['before'] !== $default['before'] || $stages['default']['after'] !== $default['after']
                      || $stages['default']['fingerprint'] !== $default['fingerprint'] )
                $problems[] = "$siteAccess: the default extension pass differs from that of the first siteaccess (not applied for it)";

            $read = self::definitionsFromINI( eZINI::create( 'kernelservices.ini' ) );
            foreach ( self::validate( $read ) as $problem )
            {
                if ( $problem[0] === 'error' )
                    $problems[] = "$siteAccess: service {$problem[1]}: {$problem[2]}";
            }
            $recorded[$siteAccess] = self::buildSiteAccess( $siteAccess, $stages['access'], $read, $generation, 'compile' );
        }

        if ( $default === null )
            return array( 'ok' => false, 'message' => 'nothing recorded: ' . implode( '; ', $problems ), 'data' => array( 'problems' => $problems ) );

        $dir = expKernelServices::cacheDirectory();
        $written = array();
        // the siteaccess files first: until the new index is in place they belong to no generation in use
        foreach ( $recorded as $siteAccess => $data )
        {
            if ( self::writeCompiledFile( $dir . '/' . $siteAccess . '.php', $data ) )
                $written[] = $siteAccess;
            else
                $problems[] = "$siteAccess: could not write $dir/$siteAccess.php";
        }
        $index = self::buildIndex( $default, array_keys( $recorded ), 'compile', $generation );
        if ( !self::writeCompiledFile( $dir . '/index.php', $index ) )
            return array( 'ok' => false, 'message' => "could not write $dir/index.php", 'data' => array( 'problems' => $problems ) );

        // files of siteaccesses no longer compiled
        if ( !$partial )
        {
            foreach ( glob( $dir . '/*.php' ) ?: array() as $file )
            {
                $name = basename( $file, '.php' );
                if ( $name !== 'index' && expKernelServices::validSiteAccessName( $name ) && !isset( $recorded[$name] ) )
                    @unlink( $file );
            }
        }

        $ok = count( $written ) === count( $siteAccesses );
        return array(
            'ok' => $ok,
            'message' => count( $written ) . ' of ' . count( $siteAccesses ) . ' siteaccess(es) compiled, generation ' . $generation
                       . ( $problems ? ' (' . count( $problems ) . ' problem(s))' : '' ),
            'data' => array( 'generation' => $generation, 'directory' => $dir, 'siteaccesses' => $written, 'problems' => $problems,
                             'extensions' => count( $index['extensions']['paths'] ) ),
        );
    }

    /**
     * The state of the compiled container on disk, compared with the settings: index, generation, the siteaccesses
     * compiled and missing, and the definition problems of each.
     *
     * @param array $siteAccesses default: siteAccessList()
     * @return array array( 'ok' => bool (no error), 'problems' => list of array( level, where, message ), 'data' )
     */
    public static function check( array $siteAccesses = array() )
    {
        $problems = array();
        $mode = expKernelServices::mode();
        $dir = expKernelServices::cacheDirectory();
        $siteAccesses = $siteAccesses ? $siteAccesses : self::siteAccessList();
        $data = array( 'mode' => $mode, 'directory' => $dir, 'worker_scope' => expKernelServices::workerScopeEnabled(),
                       'generation' => null, 'compiled' => array(), 'missing' => array() );

        $index = expKernelServices::readCompiled( $dir . '/index.php', null );
        if ( $mode === 'compiled' )
        {
            if ( $index === null )
                $problems[] = array( 'warning', 'index', "no compiled container in $dir: the kernel boots as without one (./console exp:services:compile)" );
            else
                $data['generation'] = $index['generation'];
        }

        foreach ( $siteAccesses as $siteAccess )
        {
            $compiled = $index !== null ? expKernelServices::readCompiled( $dir . '/' . $siteAccess . '.php', $index['generation'] ) : null;
            if ( $compiled !== null )
            {
                $data['compiled'][] = $siteAccess;
                $read = array( 'definitions' => $compiled['services'], 'tags' => $compiled['tags'], 'declared_tags' => array() );
            }
            else
            {
                if ( $mode === 'compiled' && $index !== null )
                    $data['missing'][] = $siteAccess;
                $read = self::definitionsFromINI( eZSiteAccess::getIni( $siteAccess, 'kernelservices.ini' ) );
            }
            foreach ( self::validate( $read ) as $problem )
                $problems[] = array( $problem[0], "$siteAccess/{$problem[1]}", $problem[2] );
        }
        if ( $data['missing'] )
            $problems[] = array( 'warning', 'siteaccess', 'not compiled for the current generation: ' . implode( ', ', $data['missing'] )
                                 . ' (recorded on their next web request with AutoRebuild=enabled, or ./console exp:services:compile)' );

        $errors = 0;
        foreach ( $problems as $problem )
        {
            if ( $problem[0] === 'error' )
                $errors++;
        }
        return array( 'ok' => $errors === 0, 'problems' => $problems, 'data' => $data );
    }

    // ----------------------------------------------------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------------------------------------------------

    /**
     * An INI list value without the empty first element of an array reset, in order.
     *
     * @param mixed $value
     * @return array
     */
    protected static function listValue( $value )
    {
        $list = array();
        foreach ( (array)$value as $item )
        {
            if ( is_string( $item ) && trim( $item ) !== '' )
                $list[] = trim( $item );
        }
        return $list;
    }

    /**
     * The extension caches of a recording, always with the three keys.
     *
     * @param mixed $caches
     * @return array
     */
    protected static function cachesValue( $caches )
    {
        $caches = is_array( $caches ) ? $caches : array();
        return array(
            'paths' => isset( $caches['paths'] ) && is_array( $caches['paths'] ) ? $caches['paths'] : array(),
            'names' => isset( $caches['names'] ) && is_array( $caches['names'] ) ? $caches['names'] : array(),
            'active' => isset( $caches['active'] ) && is_array( $caches['active'] ) ? $caches['active'] : array(),
        );
    }

    /**
     * The request variables a web kernel reads in its constructor, for the recording process.
     *
     * @param string $root
     */
    protected static function fakeWebRequest( $root )
    {
        $_SERVER['HTTP_HOST'] = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $_SERVER['SERVER_NAME'] = isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : 'localhost';
        $_SERVER['SERVER_PORT'] = isset( $_SERVER['SERVER_PORT'] ) ? $_SERVER['SERVER_PORT'] : '80';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['QUERY_STRING'] = '';
        $_SERVER['SCRIPT_FILENAME'] = $root . '/index.php';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['PHP_SELF'] = '/index.php';
        $_SERVER['DOCUMENT_ROOT'] = $root;
        $_SERVER['REMOTE_ADDR'] = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    }

    /**
     * Gives a file or directory written as root to the owner of the directory above the cache directory, so the
     * web server's user can replace it later (Velocity runs as root, PHP-FPM as the site's user).
     *
     * @param string $path
     */
    protected static function giveToSiteOwner( $path )
    {
        if ( !function_exists( 'posix_geteuid' ) || posix_geteuid() !== 0 )
            return;
        $parent = dirname( expKernelServices::cacheDirectory() );
        $uid = @fileowner( $parent );
        $gid = @filegroup( $parent );
        if ( $uid !== false && $uid !== 0 )
            @chown( $path, $uid );
        if ( $gid !== false && $gid !== 0 )
            @chgrp( $path, $gid );
    }
}
