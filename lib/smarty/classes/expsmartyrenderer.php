<?php
/**
 * Renders Smarty templates (*.stpl) inside the Exponential template system.
 *
 * How a .stpl reaches Smarty:
 *  1. The design resource (or the default file resource) resolves a template to a *.stpl file.
 *  2. eZTemplateFileResource::handleResourceData() gives the template system the stub
 *     {exp_smarty_render('<file>')} (stubFor()) instead of the file's text. The template system
 *     parses, compiles and caches the stub like any template, under a key of its own
 *     (cacheKey(), called by eZTemplateFileResource::cacheKey()).
 *  3. On every request, from the compiled stub as well as from the parsed one, the operator calls
 *     render(), which runs Smarty with the variables of the calling template namespace.
 *
 * Nothing rendered is ever stored in the template system's caches: they only hold the stub, so a
 * cached (compiled) request renders the Smarty template with its own variables. Smarty keeps its
 * own compiled templates (never its output: Smarty caching is off) in
 * <cache dir>/smarty/compile.
 *
 * The engine-neutral part (operator input modes, operator and function calls, value wrapping, write-back, the
 * render context, URI resolution) is the shared bridge in lib/templatebridge (expTemplateBridge, expTemplateValues);
 * the methods of the same names here delegate to it, with the Smarty proxy class expSmartyObjectProxy.
 *
 * @package lib
 */
class expSmartyRenderer
{
    /** Suffix of the files rendered by Smarty. */
    const SUFFIX = '.stpl';

    /** The class Smarty sees objects of the template system as. */
    const PROXY = 'expSmartyObjectProxy';

    /** Part of the compiled-template key of a stub, so a stub never shares a compiled file with a parse of the raw text. */
    const KEY_PREFIX = 'smarty:stub:1:';

    /** @var \Smarty\Smarty[] Smarty instances by configuration */
    private static $instances = array();

    /** @var \Smarty\Smarty[] the same instances by design bases and cache directory, see smarty() */
    private static $instancesByBases = array();

    /** @var bool|null Whether the Smarty library could be loaded */
    private static $libraryLoaded = null;

    /** @var array template object id => the variables it had when it ended, see captureVariables() */
    private static $captured = array();

    /**
     * @param string $path
     * @return bool true for a file name ending in .stpl
     */
    public static function isSmartyTemplate( $path )
    {
        return is_string( $path ) && substr( $path, -strlen( self::SUFFIX ) ) === self::SUFFIX;
    }

    /**
     * The template text the template system parses in place of a .stpl file.
     *
     * @param string $file
     * @return string
     */
    public static function stubFor( $file )
    {
        if ( strpbrk( $file, "'\\\n\r{}" ) !== false )
            return '<!-- smarty: unsupported characters in the template file name -->';
        return "{exp_smarty_render( '" . $file . "' )}";
    }

    /**
     * Compiled-template key: the kernel's md5 of the key data, prefixed for .stpl files. The key data
     * is "file:<resolved file>" on every path, so the design and the file resource agree.
     *
     * @param string $keyData
     * @param string $templatePath
     * @return string
     */
    public static function cacheKey( $keyData, $templatePath )
    {
        if ( self::isSmartyTemplate( (string)$keyData ) || self::isSmartyTemplate( (string)$templatePath ) )
            return md5( self::KEY_PREFIX . $keyData );
        return md5( $keyData );
    }

    /**
     * Renders a Smarty template with the variables of the given template namespaces.
     *
     * The template sees the variables of its root namespace (what a .tpl reads as $name) and of
     * its current namespace. When it is done, the variables it defined or changed are written
     * back to its root namespace, and the ones it set to null are removed there, as {def},
     * {set} and {undef} do in a .tpl: a template that includes it without a namespace, a
     * pagelayout or a module reading variables such as $subject of a mail template see them.
     *
     * @param eZTemplate $tpl
     * @param string $file template file, relative to the installation root or absolute
     * @param string $rootNamespace
     * @param string $currentNamespace
     * @return string
     */
    public static function render( eZTemplate $tpl, $file, $rootNamespace, $currentNamespace )
    {
        if ( !self::isSmartyTemplate( $file ) )
            return self::failure( "not a Smarty template: $file" );
        $path = ( $file !== '' && $file[0] === '/' ) ? $file : eZSys::rootDir() . '/' . $file;
        if ( !is_file( $path ) )
            return self::failure( "template not found: $file" );

        $smarty = self::smarty();
        if ( !$smarty )
            return self::failure( 'the Smarty library is not available (install smarty/smarty with Composer, or see [SmartySettings] LibraryPath in smarty.ini)' );

        expTemplateBridge::enter( $tpl, $rootNamespace, $currentNamespace );
        try
        {
            $template = $smarty->createTemplate( 'file:' . $path );
            $input = self::templateVariables( $tpl, $rootNamespace, $currentNamespace );
            foreach ( $input as $name => $value )
                $template->assign( $name, self::wrapNamed( $name, $value ) );
            $key = spl_object_id( $template );
            $output = $template->fetch();
            $variables = isset( self::$captured[$key] ) ? self::$captured[$key] : array();
            unset( self::$captured[$key] );
            self::writeBack( $variables, $tpl, (string)$rootNamespace, $input );
        }
        catch ( \Throwable $e )
        {
            expTemplateBridge::rethrowExit( $e );
            $output = self::failure( "rendering $file failed: " . $e->getMessage() );
        }
        finally
        {
            // templates of Smarty's own {include file=...} captured theirs too: a persistent worker keeps none of them
            if ( expTemplateBridge::leave() )
                self::$captured = array();
        }
        return $output;
    }

    /**
     * The template variables visible to a template: those of its root namespace, then those of
     * its current namespace. Names Smarty cannot use as a variable are left out.
     *
     * @return array
     */
    public static function templateVariables( eZTemplate $tpl, $rootNamespace, $currentNamespace )
    {
        return expTemplateValues::templateVariables( $tpl, $rootNamespace, $currentNamespace );
    }

    /**
     * @param mixed $name
     * @return bool whether $name can be a Smarty variable of a converted template
     */
    private static function isVariableName( $name )
    {
        return expTemplateValues::isVariableName( $name );
    }

    /**
     * Writes the variables a Smarty template defined, changed or set to null back to the
     * template system's namespace. Helper variables of converted templates (__name) stay in Smarty.
     *
     * @param array $variables the template's variables when it ended (captureVariables())
     */
    private static function writeBack( array $variables, eZTemplate $tpl, $namespace, array $input )
    {
        expTemplateValues::writeBack( $variables, $tpl, $namespace, $input, self::PROXY );
    }

    /**
     * Puts the variables of a Smarty template into a namespace of the template system, before
     * an {expinclude} that shares it.
     */
    public static function exportVariables( \Smarty\Template $template, eZTemplate $tpl, $namespace )
    {
        expTemplateValues::export( $template->getTemplateVars( null, false ), $tpl, $namespace, self::PROXY );
    }

    /**
     * Takes the variables of a namespace back into the Smarty template after an {expinclude}
     * that shared it: what the included template defined, changed or removed.
     */
    public static function importVariables( \Smarty\Template $template, eZTemplate $tpl, $namespace )
    {
        $variables = isset( $tpl->Variables[$namespace] ) && is_array( $tpl->Variables[$namespace] ) ? $tpl->Variables[$namespace] : array();
        foreach ( $template->getTemplateVars( null, false ) as $name => $value )
        {
            if ( self::isVariableName( $name ) && strncmp( $name, '__', 2 ) !== 0 && !array_key_exists( $name, $variables ) && $value !== null )
                $template->assign( $name, null );
        }
        foreach ( $variables as $name => $value )
        {
            if ( !self::isVariableName( $name ) )
                continue;
            $current = $template->getTemplateVars( $name, false );
            if ( self::unwrapNamed( $name, $current ) !== $value )
                $template->assign( $name, self::wrapNamed( $name, $value ) );
        }
    }

    /**
     * {set}/{set-block} with a scope: the namespace a scope and a name stand for, as the set
     * function computes it.
     */
    private static function scopedNamespace( $scope, $name )
    {
        return expTemplateBridge::scopedNamespace( $scope, $name );
    }

    public static function setScoped( \Smarty\Template $template, $variable, $value, $scope, $name )
    {
        list( $tpl, $rootNamespace ) = self::context() ?: array( eZTemplate::factory(), '' );
        $namespace = self::scopedNamespace( $scope, $name );
        $tpl->setVariable( $variable, self::unwrap( $value ), $namespace );
        if ( $namespace === (string)$rootNamespace && self::isVariableName( $variable ) )
            $template->assign( $variable, self::wrap( $value ) );
    }

    public static function scopedValue( \Smarty\Template $template, $variable, $scope, $name )
    {
        list( $tpl, $rootNamespace ) = self::context() ?: array( eZTemplate::factory(), '' );
        $namespace = self::scopedNamespace( $scope, $name );
        if ( $namespace === (string)$rootNamespace && self::isVariableName( $variable ) )
            return self::unwrap( $template->getTemplateVars( $variable, false ) );
        return $tpl->hasVariable( $variable, $namespace ) ? $tpl->variable( $variable, $namespace ) : null;
    }

    /**
     * expvar(name, namespace): a variable of the global namespaces ($#name, $#Namespace:name).
     */
    public static function globalVariable( $name, $namespace = '' )
    {
        return expTemplateBridge::globalVariable( $name, $namespace, self::PROXY );
    }

    /**
     * expattr: an attribute of a value that is not a variable, e.g. (fetch(...))|expattr:'name'.
     */
    public static function attributeOf( $value, $key )
    {
        return expTemplateBridge::attributeOf( $value, $key, self::PROXY );
    }

    /**
     * expvoid(): an empty operator parameter, as in i18n('context',,hash(...)): the operator uses
     * the parameter's default.
     */
    public static function void()
    {
        static $void = null;
        if ( $void === null )
            $void = new expSmartyVoid();
        return $void;
    }

    /**
     * expslice: {foreach} with offset=, max=, reverse=.
     */
    public static function loopSlice( $array, $offset = 0, $max = null, $reverse = false )
    {
        return expTemplateBridge::loopSlice( $array, $offset, $max, $reverse );
    }

    /**
     * expsectionloop: the items of {section loop=...}: an array, or a number n for 1..n.
     */
    public static function sectionLoop( $loop, $offset = 0, $max = null, $reverse = false, $iterators = false, $sequence = null )
    {
        return expTemplateBridge::sectionLoop( $loop, $offset, $max, $reverse, $iterators, $sequence, self::PROXY );
    }

    private static function loopArray( $value )
    {
        return expTemplateBridge::loopArray( $value );
    }

    /**
     * @return array|null (eZTemplate, root namespace, current namespace) of the innermost render, or null
     */
    public static function context()
    {
        return expTemplateBridge::context();
    }

    /**
     * The Smarty instance for the current siteaccess (its template directories and cache directory).
     *
     * @return \Smarty\Smarty|null
     */
    public static function smarty()
    {
        if ( !self::loadLibrary() )
            return null;

        // Called for every .stpl a page renders: the instance of the same design bases and cache directory is
        // found without checking the directories and the settings again (an INI change needs a deploy anyway).
        $bases = eZTemplateDesignResource::allDesignBases();
        $basesKey = eZSys::cacheDirectory() . "\n" . implode( "\n", $bases );
        if ( isset( self::$instancesByBases[$basesKey] ) )
            return self::$instancesByBases[$basesKey];

        $templateDirs = array();
        foreach ( $bases as $base )
        {
            foreach ( array( '/override/templates', '/templates' ) as $subdir )
            {
                if ( is_dir( $base . $subdir ) )
                    $templateDirs[] = eZSys::rootDir() . '/' . $base . $subdir;
            }
        }
        $ini = eZINI::instance( 'smarty.ini' );
        $compileCheck = $ini->variable( 'SmartySettings', 'CompileCheck' ) !== 'disabled';
        $debugging = $ini->variable( 'SmartySettings', 'Debugging' ) === 'enabled';
        $security = $ini->hasVariable( 'SmartySettings', 'Security' ) && $ini->variable( 'SmartySettings', 'Security' ) === 'enabled';
        // the policy is checked when a template is compiled: a template compiled without it must not be run with it
        $compileDir = eZSys::rootDir() . '/' . eZSys::cacheDirectory() . '/smarty/' . ( $security ? 'compile-secure' : 'compile' );
        $securitySettings = array();
        if ( $security )
        {
            foreach ( $ini->variableMulti( 'SmartySettings', array(
                          'SecurityAllowedTagList', 'SecurityDisabledTagList', 'SecurityAllowedModifierList',
                          'SecurityDisabledModifierList', 'SecurityDisabledSpecialVariableList', 'SecurityConstants',
                          'SecurityStaticClassList', 'SecurityStreamList', 'SecurityObjectAccess', 'SecurityTrustedDirList' ) ) as $value )
                $securitySettings[] = $value;
        }

        $id = md5( serialize( array( $templateDirs, $compileDir, $compileCheck, $debugging, $securitySettings ) ) );
        if ( isset( self::$instances[$id] ) )
            return self::$instancesByBases[$basesKey] = self::$instances[$id];

        if ( !is_dir( $compileDir ) )
            eZDir::mkdir( $compileDir, false, true );

        $smarty = new \Smarty\Smarty();
        $smarty->setTemplateDir( $templateDirs );
        $smarty->setCompileDir( $compileDir );
        $smarty->setCaching( \Smarty\Smarty::CACHING_OFF );
        $smarty->setCompileCheck( $compileCheck ? \Smarty\Smarty::COMPILECHECK_ON : \Smarty\Smarty::COMPILECHECK_OFF );
        $smarty->setDebugging( $debugging );
        $smarty->muteUndefinedOrNullWarnings();
        $smarty->addExtension( new expSmartyExtension() );
        // fetch() resets a template's variables when it returns: every compiled template ends with a call
        // that keeps them (captureVariables()), so render() can write them back.
        $smarty->registerFilter( 'post', array( __CLASS__, 'appendCapture' ) );
        if ( $security )
        {
            $smarty->enableSecurity( expSmartySecurity::fromSettings( $smarty, $ini ) );
            if ( !expSmartySecurity::allowsObjectAccess( $ini ) )
                $smarty->registerFilter( 'pre', array( 'expSmartySecurity', 'objectAccessFilter' ) );
        }

        return self::$instancesByBases[$basesKey] = self::$instances[$id] = $smarty;
    }

    /**
     * Smarty post-filter: appends the capture of the template's variables to its compiled code.
     *
     * @param string $code
     * @return string
     */
    public static function appendCapture( $code )
    {
        return $code . "<?php \\expSmartyRenderer::captureVariables( \$_smarty_tpl ); ?>\n";
    }

    /**
     * Called at the end of every compiled template: keeps its variables, which fetch() discards when it
     * returns, for render() (keyed by the template object, so an included template keeps its own).
     *
     * @param \Smarty\Template $template
     */
    public static function captureVariables( \Smarty\Template $template )
    {
        self::$captured[spl_object_id( $template )] = $template->getTemplateVars( null, false );
    }

    /**
     * The file the template system would load for $uri (design:..., file:...), through the same resource, override
     * rules and design keys, without loading it.
     *
     * @param eZTemplate $tpl
     * @param string $uri
     * @param string|null $matchedName set to the template name the resource matched (the debug bar's template tree)
     * @return string|null the file, or null when no resource serves it
     */
    public static function resolveURI( eZTemplate $tpl, $uri, &$matchedName = null )
    {
        return expTemplateBridge::resolveURI( $tpl, $uri, $matchedName );
    }

    /**
     * Renders a .stpl included by a .stpl ({expinclude} in the shared namespace) as a Smarty subtemplate: it sees
     * the variables of $parent, $parameters are its own (as {include} parameters, for the included template only),
     * and what it defines, changes or sets to null is visible in $parent afterwards, as after an {include}.
     *
     * With debug output on, the subtemplate is an entry of the debug bar's template tree (expRenderStats), below the
     * template that included it, as an {include} of a .tpl is.
     *
     * @param \Smarty\Template $parent
     * @param string $file
     * @param array $parameters Smarty values
     * @param string|null $uri what {expinclude} asked for (design:parts/x.stpl), for the template tree
     * @param string|null $templateName the template name the design resource matched, for the template tree
     * @return string
     */
    public static function renderSubtemplate( \Smarty\Template $parent, $file, array $parameters, $uri = null, $templateName = null )
    {
        $path = ( $file !== '' && $file[0] === '/' ) ? $file : eZSys::rootDir() . '/' . $file;
        $treeIndex = false;
        try
        {
            $child = $parent->getSmarty()->createTemplate( 'file:' . $path, null, null, $parent );
            foreach ( $parameters as $name => $value )
                $child->assign( $name, $value );
            $key = spl_object_id( $child );
            // the debug bar's template tree, as eZTemplate::processURI() notes an {include}; nothing without debug output
            if ( !empty( $GLOBALS['eZDebugEnabled'] ) && class_exists( 'expRenderStats' ) )
                $treeIndex = expRenderStats::templateEnter( $uri !== null ? $uri : $file, $file, $templateName, $child->getCompiled()->exists );
            $output = $child->fetch();
        }
        catch ( \Throwable $e )
        {
            expTemplateBridge::rethrowExit( $e );
            return self::failure( "rendering $file failed: " . $e->getMessage() );
        }
        finally
        {
            if ( $treeIndex !== false )
                expRenderStats::templateLeave( $treeIndex );
        }
        $variables = isset( self::$captured[$key] ) ? self::$captured[$key] : array();
        unset( self::$captured[$key] );
        foreach ( $variables as $name => $value )
        {
            if ( array_key_exists( $name, $parameters ) || !self::isVariableName( $name ) || strncmp( $name, '__', 2 ) === 0 )
                continue;
            if ( $parent->getTemplateVars( $name ) !== $value )
                $parent->assign( $name, $value );
        }
        return $output;
    }

    /**
     * Loads Smarty 5, from the first of:
     *  1. the installation's Composer autoload (smarty/smarty is required by composer.json);
     *  2. [SmartySettings] LibraryPath (a Smarty checkout, the directory holding src/Smarty.php).
     *
     * Smarty also needs its src/functions.php, which only Composer's "files" autoload loads. If
     * the classes came from LibraryPath, the file is loaded from the directory of the Smarty class.
     *
     * @return bool
     */
    public static function loadLibrary()
    {
        if ( self::$libraryLoaded !== null )
            return self::$libraryLoaded;

        if ( !class_exists( '\Smarty\Smarty' ) )
        {
            $libraryPath = trim( (string)eZINI::instance( 'smarty.ini' )->variable( 'SmartySettings', 'LibraryPath' ) );
            if ( $libraryPath !== '' )
            {
                if ( $libraryPath[0] !== '/' )
                    $libraryPath = eZSys::rootDir() . '/' . $libraryPath;
                $src = rtrim( $libraryPath, '/' ) . '/src/';
                if ( is_file( $src . 'Smarty.php' ) )
                {
                    spl_autoload_register( function ( $class ) use ( $src )
                    {
                        if ( strncmp( $class, 'Smarty\\', 7 ) !== 0 )
                            return;
                        $file = $src . str_replace( '\\', '/', substr( $class, 7 ) ) . '.php';
                        if ( is_file( $file ) )
                            require $file;
                    } );
                }
            }
        }

        self::$libraryLoaded = class_exists( '\Smarty\Smarty' );
        if ( !self::$libraryLoaded )
        {
            eZDebug::writeError( 'Smarty 5 not found: install smarty/smarty with Composer or set [SmartySettings] LibraryPath in smarty.ini', __METHOD__ );
            return false;
        }
        if ( !function_exists( 'smarty_ucfirst_ascii' ) )
        {
            $reflection = new ReflectionClass( '\Smarty\Smarty' );
            require_once dirname( $reflection->getFileName() ) . '/functions.php';
        }
        return true;
    }

    /**
     * Wraps template values for Smarty: objects with attribute() become array-accessible
     * proxies, arrays are wrapped element by element.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function wrap( $value )
    {
        return expTemplateValues::wrap( $value, self::PROXY );
    }

    /**
     * The reverse of wrap(), for values handed back to Exponential.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function unwrap( $value )
    {
        return expTemplateValues::unwrap( $value );
    }

    /**
     * wrap() of the variable $name, remembered for the page: the same value again (identical, which for the same
     * object or the same array is one pointer comparison) gives the same Smarty value without walking it.
     *
     * @param string $name
     * @param mixed $value as the template system has it
     * @return mixed
     */
    public static function wrapNamed( $name, $value )
    {
        return expTemplateValues::wrapNamed( $name, $value, self::PROXY );
    }

    /**
     * unwrap() of the variable $name, with the same memory as wrapNamed().
     *
     * @param string $name
     * @param mixed $value as Smarty has it
     * @return mixed
     */
    public static function unwrapNamed( $name, $value )
    {
        return expTemplateValues::unwrapNamed( $name, $value, self::PROXY );
    }

    /**
     * @param string $name
     * @return bool whether the template system has an operator of that name
     */
    public static function hasOperator( $name )
    {
        return expTemplateBridge::hasOperator( $name );
    }

    /**
     * @param string $name
     * @return bool whether the template system has a template function of that name
     */
    public static function hasFunction( $name )
    {
        return expTemplateBridge::hasFunction( $name );
    }

    /**
     * Whether an Exponential template function has a body ({explblock}...{/explblock}): such a
     * function is bridged as a Smarty block, not as a function.
     *
     * @param string $name
     * @return bool
     */
    public static function isBlockFunction( $name )
    {
        return expTemplateBridge::isBlockFunction( $name );
    }

    /** See expTemplateBridge::INPUT_AT_RUN_TIME. */
    const INPUT_AT_RUN_TIME = expTemplateBridge::INPUT_AT_RUN_TIME;

    /** See expTemplateBridge::PARAMETER_OPERATORS (smarty.ini ParameterOperatorList[] is read in addition). */
    const PARAMETER_OPERATORS = expTemplateBridge::PARAMETER_OPERATORS;

    /**
     * Whether an operator reads its parameters only, never its input: PARAMETER_OPERATORS and smarty.ini
     * [SmartySettings] ParameterOperatorList[]. Such an operator is in 'param' mode (operatorInputMode()); the
     * converter leaves out a value piped into it, as the template system ignores it.
     *
     * @param string $name
     * @return bool
     */
    public static function isParameterOperator( $name )
    {
        return expTemplateBridge::isParameterOperator( $name );
    }

    /**
     * Forgets what was read from smarty.ini and the Smarty instances built from it: the operator modes, the
     * parameter operators and the security settings are read again. For tests, and for a persistent worker after
     * a settings change.
     */
    public static function resetSettings()
    {
        expTemplateBridge::resetSettings();
        self::$instances = array();
        self::$instancesByBases = array();
    }

    /**
     * How an operator takes the value it is applied to. Smarty compiles op(a, b) as a|op:b, so the
     * bridge cannot tell the two forms apart and decides per operator:
     *  - 'param': as its first parameter: the operators without an input (input false: fetch ...),
     *    the control operators (cond, first_set, which read their parameters alone), the ones that
     *    hint input-as-parameter true (ezini, sum, concat, and ...), whose modify() gives the same
     *    result for a|op(b) and op(a, b), and the operators without hints that read their parameters
     *    only (isParameterOperator(): ezhttp, has_role, topmenu ...). Such an operator is written
     *    op(a, b) in a .stpl, the same call as a|op:b.
     *  - 'input': as its input, the operator value modify() reads: input-as-parameter 'always'
     *    (contains, implode, i18n, ezurl ...: the hint describes the compiled form only), the other
     *    operators without hints, and INPUT_AT_RUN_TIME. Without an input it is written op(null, a, b).
     * tests/tests/lib/smarty/expSmartyOperatorParityTest.php checks every operator case of the
     * template engine's own tests against the .tpl output.
     *
     * @param string $name
     * @return string
     */
    public static function operatorInputMode( $name )
    {
        return expTemplateBridge::operatorInputMode( $name );
    }

    /**
     * Runs an Exponential template operator: $input|name( $parameters... ).
     *
     * For an operator in 'param' mode (operatorInputMode()) the input is passed as the first
     * parameter, which is what the compiled template system does. expvoid() as a parameter is an
     * empty one: the operator uses its default.
     *
     * An operator that reads its parameters only (isParameterOperator()) gets no first parameter for a null
     * input: op() has no parameters, and op(null, a, b), the form the converter wrote for such an operator
     * before it was classified, is op(a, b).
     *
     * @param string $name
     * @param mixed $input
     * @param array $parameters
     * @return mixed
     */
    public static function callOperator( $name, $input, array $parameters )
    {
        return expTemplateBridge::callOperator( $name, $input, $parameters, self::PROXY );
    }

    /**
     * Runs an Exponential template function such as attribute_view_gui with named parameters and
     * returns its output. A small generated .tpl in the cache directory holds the call, so the
     * template system compiles and caches it like any template.
     *
     * @param string $name
     * @param array $parameters
     * @return string
     */
    public static function callFunction( $name, array $parameters )
    {
        return expTemplateBridge::callFunction( $name, $parameters, 'smarty' );
    }

    /**
     * Logs a rendering problem and returns what is shown in its place: an HTML comment when
     * debug output is on, nothing otherwise.
     *
     * @param string $message
     * @return string
     */
    public static function failure( $message )
    {
        return expTemplateBridge::failure( $message, 'smarty' );
    }
}
