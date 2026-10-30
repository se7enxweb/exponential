<?php
/**
 * Renders Twig templates (*.twig, *.html.twig) inside the Exponential template system.
 *
 * How a .twig reaches Twig:
 *  1. The design resource (or the default file resource) resolves a template to a .twig file.
 *  2. eZTemplateFileResource::handleResourceData() gives the template system the stub
 *     {exp_twig_render('<file>')} (stubFor(), through expTemplateEngines) instead of the file's text. The template
 *     system parses, compiles and caches the stub like any template, under a key of its own (cacheKey()).
 *  3. On every request the operator calls render(), which runs Twig with the variables of the calling template
 *     namespace and writes back what the template defined, changed or set to null, as {def}/{set}/{undef} do.
 *
 * Twig keeps its compiled templates (never output) in <cache dir>/twig/compile (compile-secure with the sandbox).
 * The engine-neutral work (operators, functions, values, write-back) is the shared bridge in lib/templatebridge.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigRenderer
{
    /** The class Twig sees objects of the template system as. */
    const PROXY = 'expTwigObjectProxy';

    /** Part of the compiled-template key of a stub. */
    const KEY_PREFIX = 'twig:stub:1:';

    /** Context keys of Twig itself, never written back. */
    const INTERNAL = array( '_parent' => true, '_seq' => true, '_key' => true, '_iterated' => true, 'loop' => true );

    /** @var \Twig\Environment[] by configuration */
    private static $instances = array();

    /** @var \Twig\Environment[] by design bases and cache directory */
    private static $instancesByBases = array();

    /** @var bool|null */
    private static $libraryLoaded = null;

    /** @var array[] variables captured at the end of the templates being rendered, innermost last */
    private static $captureStack = array();

    /** @var string|null */
    private static $rootDir = null;

    /** @var float|null the request the renders belong to ($_SERVER['REQUEST_TIME_FLOAT']), see render() */
    private static $request = null;

    /** @var bool|null [TwigSettings] Profile=enabled (with debug output): the Twig accumulators of the debug bar */
    private static $profile = null;

    /**
     * Whether the Twig accumulators are recorded: [TwigSettings] Profile=enabled and debug output on.
     */
    public static function profiling()
    {
        if ( self::$profile === null )
        {
            $ini = eZINI::instance( 'twig.ini' );
            self::$profile = $ini->hasVariable( 'TwigSettings', 'Profile' ) && $ini->variable( 'TwigSettings', 'Profile' ) === 'enabled' &&
                             eZDebug::isDebugEnabled();
        }
        return self::$profile;
    }

    /** @var array key => array( name, calls, seconds, depth, started ): this page's profile */
    private static $profileData = array();

    public static function profileStart( $key, $name )
    {
        if ( !isset( self::$profileData[$key] ) )
            self::$profileData[$key] = array( $name, 0, 0.0, 0, 0.0 );
        ++self::$profileData[$key][1];
        // inclusive time, counted once for nested calls of the same kind (a render inside a render)
        if ( self::$profileData[$key][3]++ === 0 )
            self::$profileData[$key][4] = microtime( true );
    }

    public static function profileStop( $key )
    {
        if ( isset( self::$profileData[$key] ) && --self::$profileData[$key][3] === 0 )
            self::$profileData[$key][2] += microtime( true ) - self::$profileData[$key][4];
    }

    /**
     * Writes this page's profile as one debug notice (called when the page's outermost .twig ends) and starts anew.
     */
    private static function profileReport()
    {
        $lines = array();
        // the summary rows first, then the operators by time, the slowest first
        $details = array_filter( self::$profileData, function ( $key ) { return strpos( $key, ':' ) !== false; }, ARRAY_FILTER_USE_KEY );
        uasort( $details, function ( $a, $b ) { return $b[2] <=> $a[2]; } );
        foreach ( array_diff_key( self::$profileData, $details ) + $details as $key => $row )
            $lines[] = sprintf( '%-42s %5d calls %8.1f ms', $row[0], $row[1], $row[2] * 1000 );
        if ( $lines )
            eZDebug::writeNotice( implode( "\n", $lines ), 'twig profile' );
        self::$profileData = array();
    }

    /**
     * @param string $path
     * @return bool true for a file name ending in .twig (.html.twig included)
     */
    public static function isTwigTemplate( $path )
    {
        return is_string( $path ) && substr( $path, -5 ) === '.twig' && strlen( $path ) > 5;
    }

    /**
     * The template text the template system parses in place of a .twig file.
     */
    public static function stubFor( $file )
    {
        if ( strpbrk( $file, "'\\\n\r{}" ) !== false )
            return '<!-- twig: unsupported characters in the template file name -->';
        return "{exp_twig_render( '" . $file . "' )}";
    }

    /**
     * Compiled-template key of a stub: the kernel's md5 of the key data, prefixed for .twig files.
     */
    public static function cacheKey( $keyData, $templatePath )
    {
        if ( self::isTwigTemplate( (string)$keyData ) || self::isTwigTemplate( (string)$templatePath ) )
            return md5( self::KEY_PREFIX . $keyData );
        return md5( $keyData );
    }

    private static function rootDir()
    {
        if ( self::$rootDir === null )
            self::$rootDir = rtrim( eZSys::rootDir(), '/' );
        return self::$rootDir;
    }

    /**
     * @param string $file relative to the installation root or absolute
     * @return string the absolute path
     */
    public static function absolutePath( $file )
    {
        return ( $file !== '' && $file[0] === '/' ) ? $file : self::rootDir() . '/' . $file;
    }

    /**
     * The name Twig loads a file by: its path below the installation root; a file outside it gets a namespace of
     * its own directory (tests).
     */
    public static function templateName( \Twig\Environment $twig, $path )
    {
        $root = self::rootDir() . '/';
        if ( strncmp( $path, $root, strlen( $root ) ) === 0 )
            return substr( $path, strlen( $root ) );
        $dir = dirname( $path );
        $namespace = 'exp' . substr( md5( $dir ), 0, 12 );
        $loader = $twig->getLoader();
        if ( $loader instanceof \Twig\Loader\FilesystemLoader && !in_array( $dir, $loader->getPaths( $namespace ), true ) )
            $loader->addPath( $dir, $namespace );
        return '@' . $namespace . '/' . basename( $path );
    }

    /**
     * Renders a Twig template with the variables of the given template namespaces.
     *
     * @param eZTemplate $tpl
     * @param string $file
     * @param string $rootNamespace
     * @param string $currentNamespace
     * @return string
     */
    public static function render( eZTemplate $tpl, $file, $rootNamespace, $currentNamespace )
    {
        if ( !self::isTwigTemplate( $file ) )
            return self::failure( "not a Twig template: $file" );
        $path = self::absolutePath( $file );
        if ( !is_file( $path ) )
            return self::failure( "template not found: $file" );
        $twig = self::twig();
        if ( !$twig )
            return self::failure( 'the Twig library is not available (install twig/twig with Composer, or see [TwigSettings] LibraryPath in twig.ini)' );

        // a new request: a persistent worker serves every siteaccess (the profile setting is read again) and templates
        // may have changed since its last request
        $request = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? $_SERVER['REQUEST_TIME_FLOAT'] : null;
        if ( $request !== self::$request )
        {
            self::$request = $request;
            self::$profile = null;
            if ( $twig instanceof expTwigEnvironment )
                $twig->pageStart();
        }
        expTemplateBridge::enter( $tpl, $rootNamespace, $currentNamespace );
        $depth = count( self::$captureStack );
        self::$captureStack[] = null;
        $profile = self::profiling();
        if ( $profile )
            self::profileStart( 'render', 'render() of a .twig from the template system' );
        try
        {
            if ( $profile )
                self::profileStart( 'wrap', 'variables in (wrap)' );
            $input = expTemplateValues::templateVariables( $tpl, $rootNamespace, $currentNamespace );
            $context = array();
            foreach ( $input as $name => $value )
                $context[$name] = expTemplateValues::wrapNamed( $name, $value, self::PROXY );
            if ( $profile )
            {
                self::profileStop( 'wrap' );
                self::profileStart( 'load', 'load (compiled template)' );
            }
            $template = $twig->load( self::templateName( $twig, $path ) );
            if ( $profile )
                self::profileStop( 'load' );
            $output = $template->render( $context );
            $variables = self::$captureStack[$depth] ?: array();
            if ( $profile )
                self::profileStart( 'writeback', 'variables out (write-back)' );
            expTemplateValues::writeBack( self::writeBackVariables( $variables ), $tpl, (string)$rootNamespace, $input, self::PROXY );
            if ( $profile )
                self::profileStop( 'writeback' );
        }
        catch ( \Throwable $e )
        {
            expTemplateBridge::rethrowExit( $e );
            $output = self::failure( "rendering $file failed: " . $e->getMessage() . ' (' . basename( $e->getFile() ) . ':' . $e->getLine() . ')' );
        }
        finally
        {
            array_splice( self::$captureStack, $depth );
            $outermost = expTemplateBridge::leave();
            if ( $profile )
            {
                self::profileStop( 'render' );
                if ( $outermost )
                    self::profileReport();
            }
        }
        return $output;
    }

    /**
     * The variables of a template's context that are written back: no Twig internals.
     */
    private static function writeBackVariables( array $variables )
    {
        return array_diff_key( $variables, self::INTERNAL );
    }

    /**
     * Called at the end of every compiled template (expTwigCaptureNode): keeps its variables for the render in
     * progress, so render() can write them back.
     */
    public static function captureVariables( array $context )
    {
        if ( self::$captureStack )
            self::$captureStack[count( self::$captureStack ) - 1] = $context;
    }

    /**
     * {% expinclude uri='design:x.tpl' name='ns' a=b %}: the include function of the template system. A .twig in
     * the shared namespace runs as a Twig template with this template's variables (and the parameters for it
     * only), any other template through the template system; what the included template defines, changes or sets
     * to null is visible here afterwards, as after an {include}.
     *
     * @return array (output, the context afterwards)
     */
    public static function includeTemplate( \Twig\Environment $twig, array $context, array $params )
    {
        if ( !isset( $params['uri'] ) || (string)$params['uri'] === '' )
            return array( self::failure( "expinclude: missing parameter 'uri'" ), $context );
        if ( self::profiling() )
        {
            self::profileStart( 'include', '{% expinclude %}' );
            try
            {
                return self::includeTemplateUnprofiled( $twig, $context, $params );
            }
            finally
            {
                self::profileStop( 'include' );
            }
        }
        return self::includeTemplateUnprofiled( $twig, $context, $params );
    }

    private static function includeTemplateUnprofiled( \Twig\Environment $twig, array $context, array $params )
    {
        $uri = (string)$params['uri'];
        $name = isset( $params['name'] ) ? (string)$params['name'] : '';
        unset( $params['uri'], $params['name'] );

        list( $tpl, $rootNamespace, $currentNamespace ) = expTemplateBridge::contextOrDefault();
        $namespace = $currentNamespace;
        if ( $name !== '' )
            $namespace = $namespace !== '' ? "$namespace:$name" : $name;
        $shared = $name === '' && $namespace === $rootNamespace;

        if ( $shared )
        {
            $file = expTemplateBridge::resolveURI( $tpl, $uri, $matchedName );
            if ( $file !== null && self::isTwigTemplate( $file ) )
                return self::renderSubtemplate( $twig, $context, self::absolutePath( $file ), $params, $uri, $matchedName );
            expTemplateValues::export( self::writeBackVariables( $context ), $tpl, $namespace, self::PROXY );
        }

        $unset = array();
        $replaced = array();
        foreach ( $params as $key => $value )
        {
            if ( $tpl->hasVariable( $key, $namespace ) )
                $replaced[$key] = $tpl->variable( $key, $namespace );
            else
                $unset[] = $key;
            $tpl->setVariable( $key, expTemplateValues::unwrap( $value ), $namespace );
        }
        $textElements = array();
        $extraParameters = array();
        $tpl->processURI( $uri, true, $extraParameters, $textElements, $namespace, $namespace );
        foreach ( $unset as $key )
            $tpl->unsetVariable( $key, $namespace );
        foreach ( $replaced as $key => $value )
            $tpl->setVariable( $key, $value, $namespace );

        if ( $shared )
        {
            $variables = isset( $tpl->Variables[$namespace] ) && is_array( $tpl->Variables[$namespace] ) ? $tpl->Variables[$namespace] : array();
            foreach ( $context as $key => $value )
            {
                if ( expTemplateValues::isWriteBackName( $key ) && !isset( self::INTERNAL[$key] ) && !array_key_exists( $key, $variables ) && $value !== null )
                    $context[$key] = null;
            }
            foreach ( $variables as $key => $value )
            {
                if ( !expTemplateValues::isVariableName( $key ) )
                    continue;
                $current = isset( $context[$key] ) ? $context[$key] : null;
                if ( expTemplateValues::unwrapNamed( $key, $current, self::PROXY ) !== $value )
                    $context[$key] = expTemplateValues::wrapNamed( $key, $value, self::PROXY );
            }
        }
        return array( implode( '', $textElements ), $context );
    }

    /**
     * Renders a .twig included by a .twig in the shared namespace: it sees $context, $params are its own, and what
     * it defines, changes or sets to null is in the returned context.
     *
     * @return array (output, the context afterwards)
     */
    public static function renderSubtemplate( \Twig\Environment $twig, array $context, $path, array $params, $uri = null, $templateName = null )
    {
        $childContext = $params + $context;
        $treeIndex = false;
        $depth = count( self::$captureStack );
        self::$captureStack[] = null;
        try
        {
            $template = $twig->load( self::templateName( $twig, $path ) );
            if ( !empty( $GLOBALS['eZDebugEnabled'] ) && class_exists( 'expRenderStats' ) )
                $treeIndex = expRenderStats::templateEnter( $uri !== null ? $uri : $path, $path, $templateName, true );
            $output = $template->render( $childContext );
            $variables = self::$captureStack[$depth] ?: array();
        }
        catch ( \Throwable $e )
        {
            expTemplateBridge::rethrowExit( $e );
            return array( self::failure( "rendering $path failed: " . $e->getMessage() ), $context );
        }
        finally
        {
            array_splice( self::$captureStack, $depth );
            if ( $treeIndex !== false )
                expRenderStats::templateLeave( $treeIndex );
        }
        foreach ( self::writeBackVariables( $variables ) as $name => $value )
        {
            if ( array_key_exists( $name, $params ) || !expTemplateValues::isWriteBackName( $name ) )
                continue;
            $context[$name] = $value;
        }
        return array( $output, $context );
    }

    /**
     * {% expset name='x' value=v scope='global' namespace='ns' %}: a variable in a namespace of the template system.
     *
     * @return array the context afterwards
     */
    public static function setScoped( array $context, array $params )
    {
        if ( !isset( $params['name'] ) || (string)$params['name'] === '' )
            return $context;
        $variable = (string)$params['name'];
        $value = isset( $params['value'] ) ? $params['value'] : null;
        list( $tpl, $rootNamespace ) = expTemplateBridge::contextOrDefault();
        $namespace = expTemplateBridge::scopedNamespace( isset( $params['scope'] ) ? (string)$params['scope'] : 'relative',
                                                         isset( $params['namespace'] ) ? (string)$params['namespace'] : '' );
        $tpl->setVariable( $variable, expTemplateValues::unwrap( $value ), $namespace );
        if ( $namespace === (string)$rootNamespace && expTemplateValues::isVariableName( $variable ) )
            $context[$variable] = expTemplateValues::wrap( expTemplateValues::unwrap( $value ), self::PROXY );
        return $context;
    }

    /**
     * {% expsetblock variable='x' scope='global' name='ns' %}...{% endexpsetblock %} and expappendblock.
     *
     * @return array the context afterwards
     */
    public static function setBlock( array $context, array $params, $content, $append )
    {
        if ( !isset( $params['variable'] ) || (string)$params['variable'] === '' )
        {
            self::failure( "expsetblock: missing parameter 'variable'" );
            return $context;
        }
        $variable = (string)$params['variable'];
        $scope = isset( $params['scope'] ) ? (string)$params['scope'] : 'relative';
        $name = isset( $params['name'] ) ? (string)$params['name'] : '';
        list( $tpl, $rootNamespace ) = expTemplateBridge::contextOrDefault();
        $namespace = expTemplateBridge::scopedNamespace( $scope, $name );
        $value = (string)$content;
        if ( $append )
        {
            if ( $namespace === (string)$rootNamespace && array_key_exists( $variable, $context ) )
                $current = expTemplateValues::unwrap( $context[$variable] );
            else
                $current = $tpl->hasVariable( $variable, $namespace ) ? $tpl->variable( $variable, $namespace ) : null;
            $list = is_array( $current ) ? $current : array();
            $list[] = $value;
            $value = $list;
        }
        $tpl->setVariable( $variable, $value, $namespace );
        if ( $namespace === (string)$rootNamespace && expTemplateValues::isVariableName( $variable ) )
            $context[$variable] = $value;
        return $context;
    }

    /**
     * {% expundefall %}: removes every variable of the root namespace.
     *
     * @return array the context afterwards
     */
    public static function undefAll( array $context )
    {
        list( $tpl, $rootNamespace ) = expTemplateBridge::contextOrDefault();
        if ( isset( $tpl->Variables[$rootNamespace] ) )
        {
            foreach ( array_keys( $tpl->Variables[$rootNamespace] ) as $name )
                $tpl->unsetVariable( $name, $rootNamespace );
        }
        foreach ( array_keys( $context ) as $name )
        {
            if ( expTemplateValues::isVariableName( $name ) && !isset( self::INTERNAL[$name] ) )
                $context[$name] = null;
        }
        return $context;
    }

    /** @var array open cache-block handlers */
    private static $openCacheBlocks = array();

    /**
     * Start of {% expcacheblock %}: array( true, cached text ) for a hit, array( false, handler ) otherwise.
     */
    public static function cacheBlockStart( array $params, $templateName, $id )
    {
        if ( eZINI::instance()->variable( 'TemplateSettings', 'TemplateCache' ) != 'enabled' )
            return array( false, null );
        $accessName = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : false;
        if ( !empty( $GLOBALS['expUISwitchVariantKey'] ) )
            $accessName .= '-' . $GLOBALS['expUISwitchVariantKey'];
        $placement = 'twig:' . $templateName . ':' . $id;
        $keys = isset( $params['keys'] ) ? expTemplateValues::unwrap( $params['keys'] ) : null;
        $keyArray = $keys === null ? array( $placement, $accessName ) : array( $keys, $placement, $accessName );
        $subtreeExpiry = isset( $params['subtree_expiry'] ) ? expTemplateValues::unwrap( $params['subtree_expiry'] ) : null;
        $expiry = isset( $params['expiry'] ) ? (int)$params['expiry'] : eZTemplateCacheFunction::DEFAULT_TTL;
        $ignoreContentExpiry = isset( $params['ignore_content_expiry'] ) ? $params['ignore_content_expiry'] === true : false;
        if ( $subtreeExpiry !== null )
            $ignoreContentExpiry = true;
        list( $handler, $cached ) = eZTemplateCacheBlock::retrieve( $keyArray, $subtreeExpiry, $expiry > 0 ? $expiry : 0, !$ignoreContentExpiry );
        $hit = !( $cached instanceof eZClusterFileFailure );
        if ( self::profiling() )
        {
            self::profileStart( 'cacheblock:' . ( $hit ? 'hit' : 'miss' ), '  {% expcacheblock %} ' . ( $hit ? 'hit' : 'miss' ) . ' ' . $placement );
            self::profileStop( 'cacheblock:' . ( $hit ? 'hit' : 'miss' ) );
        }
        if ( $hit )
            return array( true, $cached );
        return array( false, $handler );
    }

    /**
     * End of {% expcacheblock %} after a miss: stores the rendered body.
     */
    public static function cacheBlockEnd( array $start, $content )
    {
        if ( $start[1] )
            eZTemplateCacheBlock::store( $start[1], (string)$content );
    }

    /**
     * {% exprunonce %}: true the first time a block runs on the page.
     */
    public static function runOnce( $templateName, $id )
    {
        $key = 'twig:' . $templateName . ':' . $id;
        if ( isset( $GLOBALS['eZTemplateRunOnceKeys'][$key] ) )
            return false;
        $GLOBALS['eZTemplateRunOnceKeys'][$key] = true;
        return true;
    }

    /**
     * {% expwhile %} reached [TwigSettings] WhileLoopLimit.
     */
    public static function whileLimitReached( $templateName, $limit )
    {
        eZDebug::writeNotice( "a loop in $templateName stopped after $limit rounds ([TwigSettings] WhileLoopLimit)", 'twig' );
    }

    /**
     * @return int [TwigSettings] WhileLoopLimit
     */
    public static function whileLoopLimit()
    {
        $ini = eZINI::instance( 'twig.ini' );
        $limit = $ini->hasVariable( 'TwigSettings', 'WhileLoopLimit' ) ? (int)$ini->variable( 'TwigSettings', 'WhileLoopLimit' ) : 10000;
        return $limit > 0 ? $limit : 10000;
    }

    /**
     * The Twig environment for the current siteaccess (its design template directories and cache directory), one per
     * process.
     *
     * @return \Twig\Environment|null
     */
    public static function twig()
    {
        if ( !self::loadLibrary() )
            return null;
        $bases = eZTemplateDesignResource::allDesignBases();
        $basesKey = eZSys::cacheDirectory() . "\n" . implode( "\n", $bases );
        if ( isset( self::$instancesByBases[$basesKey] ) )
            return self::$instancesByBases[$basesKey];

        $profile = self::profiling();
        if ( $profile )
            self::profileStart( 'environment', 'environment built (once per process)' );
        try
        {
            return self::buildTwig( $bases, $basesKey );
        }
        finally
        {
            if ( $profile )
                self::profileStop( 'environment' );
        }
    }

    private static function buildTwig( array $bases, $basesKey )
    {
        $root = self::rootDir();
        $templateDirs = array();
        foreach ( $bases as $base )
        {
            foreach ( array( '/override/templates', '/templates' ) as $subdir )
            {
                if ( is_dir( $root . '/' . $base . $subdir ) )
                    $templateDirs[] = $root . '/' . $base . $subdir;
            }
        }
        $ini = eZINI::instance( 'twig.ini' );
        $setting = function ( $name, $default ) use ( $ini ) {
            return $ini->hasVariable( 'TwigSettings', $name ) ? $ini->variable( 'TwigSettings', $name ) : $default;
        };
        $autoReload = $setting( 'AutoReload', 'enabled' ) !== 'disabled';
        $debug = $setting( 'Debug', 'disabled' ) === 'enabled';
        $strict = $setting( 'StrictVariables', 'disabled' ) === 'enabled';
        $autoescape = $setting( 'Autoescape', 'disabled' );
        $autoescape = ( $autoescape === 'disabled' || $autoescape === '' ) ? false : ( $autoescape === 'enabled' ? 'html' : $autoescape );
        $security = $setting( 'Security', 'enabled' ) === 'enabled';
        $cacheDir = $root . '/' . eZSys::cacheDirectory() . '/twig/' . ( $security ? 'compile-secure' : 'compile' );
        $securitySettings = $security ? expTwigSecurity::settingsKey( $ini ) : '';

        $id = md5( serialize( array( $templateDirs, $cacheDir, $autoReload, $debug, $strict, $autoescape, $securitySettings ) ) );
        if ( isset( self::$instances[$id] ) )
            return self::$instancesByBases[$basesKey] = self::$instances[$id];

        if ( !is_dir( $cacheDir ) )
            eZDir::mkdir( $cacheDir, false, true );
        $loader = new \Twig\Loader\FilesystemLoader( array(), $root );
        foreach ( $templateDirs as $dir )
            $loader->addPath( $dir );
        $loader->addPath( $root );
        $twig = new expTwigEnvironment( $loader, array(
            'cache' => $cacheDir,
            'auto_reload' => $autoReload,
            'debug' => $debug,
            'strict_variables' => $strict,
            'autoescape' => $autoescape,
            // output is generated, never buffered: an operator that prints and exits (a redirect) keeps its output
            'use_yield' => true,
        ), $root );
        $twig->addExtension( new expTwigExtension() );
        expTwigExtension::registerCallbacks( $twig );
        if ( $debug )
            $twig->addExtension( new \Twig\Extension\DebugExtension() );
        if ( $security )
            $twig->addExtension( new \Twig\Extension\SandboxExtension( expTwigSecurity::fromSettings( $ini ), true ) );
        return self::$instancesByBases[$basesKey] = self::$instances[$id] = $twig;
    }

    /** @var \Twig\Environment|null the environment of compileCheck() */
    private static $checkEnvironment = null;

    /**
     * The environment templates are checked with, in memory: an ArrayLoader, the bridge and its tags, the sandbox as the
     * site has it ([TwigSettings] Security), no cache.
     *
     * @return \Twig\Environment
     * @throws RuntimeException when Twig is not available
     */
    public static function checkEnvironment()
    {
        if ( !self::loadLibrary() )
            throw new RuntimeException( 'the Twig library is not available' );
        if ( self::$checkEnvironment === null )
        {
            $ini = eZINI::instance( 'twig.ini' );
            $twig = new \Twig\Environment( new \Twig\Loader\ArrayLoader( array() ), array( 'cache' => false, 'autoescape' => false, 'use_yield' => true ) );
            $twig->addExtension( new expTwigExtension() );
            expTwigExtension::registerCallbacks( $twig );
            if ( !$ini->hasVariable( 'TwigSettings', 'Security' ) || $ini->variable( 'TwigSettings', 'Security' ) === 'enabled' )
                $twig->addExtension( new \Twig\Extension\SandboxExtension( expTwigSecurity::fromSettings( $ini ), true ) );
            self::$checkEnvironment = $twig;
        }
        return self::$checkEnvironment;
    }

    /**
     * Compiles a template text in memory, with the bridge, its tags and the sandbox as the site has them; nothing
     * is written to the cache directory.
     *
     * @param string $text
     * @param string $name name used in messages
     * @return string|null the error, null when the text compiles
     * @throws RuntimeException when Twig is not available
     */
    public static function compileCheck( $text, $name = 'template.twig' )
    {
        $twig = self::checkEnvironment();
        $twig->getLoader()->setTemplate( $name, $text );
        try
        {
            $twig->compileSource( new \Twig\Source( $text, $name ) );
        }
        catch ( \Twig\Error\Error $e )
        {
            return $e->getMessage();
        }
        return null;
    }

    /**
     * Loads Twig 3, from the first of: the installation's Composer autoload (twig/twig is required by
     * composer.json); [TwigSettings] LibraryPath: a Composer directory (vendor/autoload.php) or the twig/twig
     * package (src/Environment.php). The environment variable TWIG_LIBRARY_PATH (tests) is read like LibraryPath.
     *
     * @return bool
     */
    public static function loadLibrary()
    {
        if ( self::$libraryLoaded !== null )
            return self::$libraryLoaded;
        if ( !class_exists( '\Twig\Environment' ) )
        {
            $libraryPath = trim( (string)eZINI::instance( 'twig.ini' )->variable( 'TwigSettings', 'LibraryPath' ) );
            if ( $libraryPath === '' && getenv( 'TWIG_LIBRARY_PATH' ) )
                $libraryPath = getenv( 'TWIG_LIBRARY_PATH' );
            if ( $libraryPath !== '' )
                self::registerLibraryPath( $libraryPath );
        }
        self::$libraryLoaded = class_exists( '\Twig\Environment' );
        if ( !self::$libraryLoaded )
            eZDebug::writeError( 'Twig 3 not found: install twig/twig with Composer or set [TwigSettings] LibraryPath in twig.ini', __METHOD__ );
        return self::$libraryLoaded;
    }

    private static function registerLibraryPath( $libraryPath )
    {
        if ( $libraryPath[0] !== '/' )
            $libraryPath = self::rootDir() . '/' . $libraryPath;
        $libraryPath = rtrim( $libraryPath, '/' );
        foreach ( array( $libraryPath . '/vendor/autoload.php', $libraryPath . '/autoload.php', dirname( $libraryPath, 2 ) . '/autoload.php' ) as $autoload )
        {
            if ( is_file( $autoload ) && is_file( dirname( $autoload ) . '/twig/twig/src/Environment.php' ) )
            {
                require_once $autoload;
                return;
            }
        }
        $src = $libraryPath . '/src/';
        if ( is_file( $src . 'Environment.php' ) )
        {
            spl_autoload_register( function ( $class ) use ( $src )
            {
                if ( strncmp( $class, 'Twig\\', 5 ) !== 0 )
                    return;
                $file = $src . str_replace( '\\', '/', substr( $class, 5 ) ) . '.php';
                if ( is_file( $file ) )
                    require $file;
            } );
            // the package alone: symfony/deprecation-contracts (trigger_deprecation) must come from elsewhere
            if ( is_file( $src . 'Resources/core.php' ) )
            {
                foreach ( glob( $src . 'Resources/*.php' ) as $file )
                    require_once $file;
            }
        }
    }

    /**
     * Forgets what was read from twig.ini and the environments built from it (tests; a worker after a settings change).
     */
    public static function resetSettings()
    {
        expTemplateBridge::resetSettings();
        expTwigExtension::reset();
        self::$instances = array();
        self::$instancesByBases = array();
        self::$checkEnvironment = null;
        self::$rootDir = null;
        self::$profile = null;
    }

    /**
     * Logs a rendering problem; an HTML comment in its place with debug output on, nothing otherwise.
     */
    public static function failure( $message )
    {
        return expTemplateBridge::failure( $message, 'twig' );
    }
}
