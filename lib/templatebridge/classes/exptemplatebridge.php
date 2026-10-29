<?php
/**
 * The engine-neutral part of running another template engine (Smarty 5 for .stpl, Twig 3 for .twig) inside the
 * Exponential template system: how operators take their input, calling operators and template functions, the
 * render context, resolving a design: URI, the helpers the converters write (expvar, expattr, expslice ...) and
 * failure reporting. lib/smarty and lib/twig call it; expTemplateValues converts the values.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTemplateBridge
{
    /** Operators that hint input-as-parameter true but read their input as the operator value at run time. */
    const INPUT_AT_RUN_TIME = array( 'nl2br', 'indent' );

    /**
     * Operators of the kernel without template hints whose modify() reads its parameters only (or nothing at all)
     * and never the operator value: ezhttp( 'Name', 'get' ) is called with 'Name' as its first parameter, as in a
     * .tpl. Extensions add theirs with site.ini [TemplateBridgeSettings] ParameterOperatorList[] (smarty.ini and
     * twig.ini ParameterOperatorList[] are read too). Every other operator without hints reads its input
     * (wordtoimage, autolink, simpletags, x18n, month_overview ...).
     */
    const PARAMETER_OPERATORS = array(
        // kernel/common/ezurloperator.php
        'ezhttp', 'ezhttp_hasvariable',
        // kernel/common/ezwordtoimageoperator.php (the other icon operators read their input)
        'icon_info',
        // kernel/common/ezalphabetoperator.php
        'alphabet',
        // kernel/common/ezcontentstructuretreeoperator.php
        'content_structure_tree',
        // kernel/common/expcontentsecuritypolicyoperator.php
        'csp_nonce',
        // kernel/common/expdemomodeoperator.php
        'exp_demo_mode',
        // kernel/common/expenvironmentoperator.php
        'exp_environment', 'exp_environment_badge',
        // kernel/common/ezmoduleoperator.php
        'ezmodule',
        // kernel/common/eztocoperator.php
        'eztoc',
        // lib/eztemplate/classes/eztemplateroleoperator.php
        'has_role', 'has_role_id', 'has_any_role', 'has_role_by_user', 'has_role_by_user_id', 'has_policy',
        'has_policy_by_user',
        // kernel/classes/expinstallationoperator.php
        'installation_name', 'is_production_system',
        // kernel/private/eztemplate/ezplanguageswitcheroperator.php
        'language_switcher',
        // kernel/common/ezmoduleparamsoperator.php
        'module_params',
        // lib/ezpdf/classes/ezpdf.php
        'pdf',
        // kernel/common/eztopmenuoperator.php
        'topmenu',
        // kernel/common/expkerneltransferoperator.php
        'transfer_actions', 'transfer_related', 'transfer_tool', 'transfer_tools',
        // kernel/common/eztreemenuoperator.php
        'treemenu',
        // lib/smarty/classes/expsmartyoperator.php, lib/twig/classes/exptwigoperator.php
        'exp_smarty_render', 'exp_twig_render',
    );

    /** @var array[] Stack of (eZTemplate, root namespace, current namespace) of the renders in progress, all engines */
    private static $contextStack = array();

    /** @var int Counter for the template namespaces of bridged functions */
    private static $namespaceCounter = 0;

    /** @var array input modes of the operators, by name and by the class that held the name when it was asked */
    private static $inputModes = array();

    /** @var array|null name => true: PARAMETER_OPERATORS and the ParameterOperatorList settings, read once per process */
    private static $parameterOperators = null;

    /** @var expTemplateVoid|null */
    private static $void = null;

    /**
     * Starts a render: the template system's template and namespaces the engine's template sees.
     */
    public static function enter( eZTemplate $tpl, $rootNamespace, $currentNamespace )
    {
        self::$contextStack[] = array( $tpl, $rootNamespace, $currentNamespace );
    }

    /**
     * Ends the render started last.
     *
     * @return bool true when no render is left in progress (the page's outermost engine template ended)
     */
    public static function leave()
    {
        array_pop( self::$contextStack );
        if ( self::$contextStack )
            return false;
        expTemplateValues::reset();
        return true;
    }

    /**
     * @return array|null (eZTemplate, root namespace, current namespace) of the innermost render, or null
     */
    public static function context()
    {
        if ( self::$contextStack )
            return self::$contextStack[count( self::$contextStack ) - 1];
        return null;
    }

    /**
     * @return array (eZTemplate, root namespace, current namespace): the innermost render's, or the template system's
     *               own template with the empty namespaces outside a render
     */
    public static function contextOrDefault()
    {
        return self::context() ?: array( eZTemplate::factory(), '', '' );
    }

    /**
     * {set}/{set-block} with a scope: the namespace a scope and a name stand for, as the set function computes it.
     */
    public static function scopedNamespace( $scope, $name )
    {
        list( , $rootNamespace, $currentNamespace ) = self::contextOrDefault();
        if ( $scope === 'global' )
            return (string)$name;
        $base = $scope === 'root' ? (string)$rootNamespace : (string)$currentNamespace;
        if ( $name === '' || $name === null )
            return $base;
        return $base !== '' ? "$base:$name" : (string)$name;
    }

    /**
     * expvar(name, namespace): a variable of the global namespaces ($#name, $#Namespace:name).
     */
    public static function globalVariable( $name, $namespace = '', $proxyClass = 'expTemplateObjectProxy' )
    {
        list( $tpl ) = self::contextOrDefault();
        $namespace = (string)$namespace;
        return $tpl->hasVariable( $name, $namespace ) ? expTemplateValues::wrap( $tpl->variable( $name, $namespace ), $proxyClass ) : null;
    }

    /**
     * expattr: an attribute of a value that is not a variable, e.g. (fetch(...))|expattr('name').
     */
    public static function attributeOf( $value, $key, $proxyClass = 'expTemplateObjectProxy' )
    {
        if ( $value instanceof expTemplateObjectProxy )
            return $value->offsetGet( $key );
        if ( is_array( $value ) )
            return array_key_exists( $key, $value ) ? expTemplateValues::wrap( $value[$key], $proxyClass ) : null;
        if ( is_object( $value ) && method_exists( $value, 'attribute' ) && method_exists( $value, 'hasAttribute' ) )
            return $value->hasAttribute( $key ) ? expTemplateValues::wrap( $value->attribute( $key ), $proxyClass ) : null;
        return null;
    }

    /**
     * expcolumn: the attribute $key of every item.
     */
    public static function column( $array, $key, $proxyClass = 'expTemplateObjectProxy' )
    {
        $values = array();
        foreach ( self::loopArray( $array ) as $item )
            $values[] = self::attributeOf( $item, $key, $proxyClass );
        return $values;
    }

    /**
     * expsequence: the item of a sequence for loop round $index (sequence= of foreach and section).
     */
    public static function sequence( $sequence, $index = 0 )
    {
        $sequence = is_array( $sequence ) ? array_values( $sequence ) : array( $sequence );
        return $sequence ? $sequence[( (int)$index ) % count( $sequence )] : null;
    }

    /**
     * exprange: the integers from $from to $to.
     */
    public static function range( $from, $to )
    {
        return range( (int)$from, (int)$to );
    }

    /**
     * expvoid(): an empty operator parameter, as in i18n('context',,hash(...)): the operator uses the parameter's
     * default.
     */
    public static function void()
    {
        if ( self::$void === null )
            self::$void = new expTemplateVoid();
        return self::$void;
    }

    /**
     * expslice: a foreach with offset=, max=, reverse=.
     */
    public static function loopSlice( $array, $offset = 0, $max = null, $reverse = false )
    {
        $array = self::loopArray( $array );
        if ( $reverse )
            $array = array_reverse( $array, true );
        return array_slice( $array, (int)$offset, $max === null || $max === false ? null : (int)$max, true );
    }

    /**
     * expsectionloop: the items of {section loop=...}: an array, or a number n for 1..n. With $iterators (a section
     * with var=) each item is the section's iterator, as the template system gives $var: index, number, key, item,
     * sequence (the item of $sequence for the round) and the item's own attributes; as a value it is the item.
     */
    public static function sectionLoop( $loop, $offset = 0, $max = null, $reverse = false, $iterators = false, $sequence = null, $proxyClass = 'expTemplateObjectProxy' )
    {
        if ( is_numeric( $loop ) )
            $loop = $loop > 0 ? array_combine( range( 0, (int)$loop - 1 ), range( 1, (int)$loop ) ) : array();
        $items = self::loopSlice( $loop, $offset, $max, $reverse );
        if ( !$iterators )
            return $items;
        $index = 0;
        $last = false;
        foreach ( $items as $key => $item )
        {
            $iterator = new eZTemplateSectionIterator();
            $iterator->setIteratorValues( expTemplateValues::unwrap( $item ), $key, $index, $index + 1,
                                          $sequence === null ? false : self::sequence( expTemplateValues::unwrap( $sequence ), $index ), $last );
            $items[$key] = new $proxyClass( $iterator );
            ++$index;
        }
        return $items;
    }

    /**
     * @param mixed $value
     * @return array the items a loop over $value walks
     */
    public static function loopArray( $value )
    {
        if ( is_array( $value ) )
            return $value;
        if ( $value instanceof expTemplateObjectProxy )
            $value = $value->expObject();
        if ( $value instanceof Traversable )
            return iterator_to_array( $value );
        return $value === null ? array() : array( $value );
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
        $resourceName = '';
        $templateName = '';
        $matchedName = null;
        $resource = $tpl->resourceFor( $uri, $resourceName, $templateName );
        if ( !is_object( $resource ) )
            return null;
        $resourceData = $tpl->resourceData( $resource, $uri, $resourceName, $templateName );
        $extraParameters = array();
        if ( !$resource->handleResource( $tpl, $resourceData, eZTemplate::RESOURCE_QUERY, $extraParameters ) )
            return null;
        if ( isset( $resourceData['template-name'] ) )
            $matchedName = (string)$resourceData['template-name'];
        return isset( $resourceData['template-filename'] ) ? (string)$resourceData['template-filename'] : null;
    }

    /**
     * @param string $name
     * @return bool whether the template system has an operator of that name
     */
    public static function hasOperator( $name )
    {
        $tpl = eZTemplate::factory();
        return isset( $tpl->Operators[$name] );
    }

    /**
     * @param string $name
     * @return bool whether the template system has a template function of that name
     */
    public static function hasFunction( $name )
    {
        $tpl = eZTemplate::factory();
        return isset( $tpl->Functions[$name] );
    }

    /**
     * Whether an Exponential template function has a body ({explblock}...{/explblock}): such a function is bridged
     * as a block (a Smarty block, a Twig tag with an end tag), not as a function.
     *
     * @param string $name
     * @return bool
     */
    public static function isBlockFunction( $name )
    {
        $tpl = eZTemplate::factory();
        if ( !isset( $tpl->Functions[$name] ) )
            return false;
        if ( is_array( $tpl->Functions[$name] ) )
            $tpl->loadAndRegisterFunctions( $tpl->Functions[$name] );
        $object = $tpl->Functions[$name];
        return is_object( $object ) && method_exists( $object, 'hasChildren' ) && $object->hasChildren();
    }

    /**
     * Whether an operator reads its parameters only, never its input: PARAMETER_OPERATORS and the
     * ParameterOperatorList[] of site.ini [TemplateBridgeSettings], smarty.ini [SmartySettings] and twig.ini
     * [TwigSettings]. Such an operator is in 'param' mode (operatorInputMode()); the converters leave out a value
     * piped into it, as the template system ignores it.
     *
     * @param string $name
     * @return bool
     */
    public static function isParameterOperator( $name )
    {
        if ( self::$parameterOperators === null )
        {
            $names = self::PARAMETER_OPERATORS;
            foreach ( array( array( 'site.ini', 'TemplateBridgeSettings' ), array( 'smarty.ini', 'SmartySettings' ), array( 'twig.ini', 'TwigSettings' ) ) as $source )
            {
                list( $file, $group ) = $source;
                if ( !eZINI::exists( $file ) )
                    continue;
                $ini = eZINI::instance( $file );
                if ( $ini->hasVariable( $group, 'ParameterOperatorList' ) )
                {
                    $list = $ini->variable( $group, 'ParameterOperatorList' );
                    if ( is_array( $list ) )
                        $names = array_merge( $names, array_filter( array_map( 'trim', $list ), 'strlen' ) );
                }
            }
            self::$parameterOperators = array_fill_keys( $names, true );
        }
        return isset( self::$parameterOperators[$name] );
    }

    /**
     * Forgets what was read from the settings: the operator modes and the parameter operators are read again. For
     * tests, and for a persistent worker after a settings change.
     */
    public static function resetSettings()
    {
        self::$inputModes = array();
        self::$parameterOperators = null;
        self::$ignoresInput = array();
    }

    /**
     * How an operator takes the value it is applied to, for an engine that cannot tell a|op(b) from op(a, b)
     * (Smarty compiles both as a modifier) and for the converters:
     *  - 'param': as its first parameter: the operators without an input (input false: fetch ...), the control
     *    operators (cond, first_set, which read their parameters alone), the ones that hint input-as-parameter
     *    true (ezini, sum, concat, and ...), whose modify() gives the same result for a|op(b) and op(a, b), and
     *    the operators without hints that read their parameters only (isParameterOperator()).
     *  - 'input': as its input, the operator value modify() reads: input-as-parameter 'always' (contains, implode,
     *    i18n, ezurl ...: the hint describes the compiled form only), the other operators without hints, and
     *    INPUT_AT_RUN_TIME.
     * The engine parity tests check every operator case of the template engine's own tests against the .tpl output.
     *
     * @param string $name
     * @return string
     */
    public static function operatorInputMode( $name )
    {
        // Two operator classes can register one name, and the one loaded last holds it from then on (strip_tags:
        // eZTemplatePHPOperator takes the input as a parameter, eZTemplateStringsOperator, loaded with ristring,
        // as its input): the mode is the one of the class that holds the name now.
        $tpl = eZTemplate::factory();
        $object = isset( $tpl->Operators[$name] ) ? $tpl->Operators[$name] : null;
        $holder = is_object( $object ) ? get_class( $object ) : '';
        if ( isset( self::$inputModes[$name][$holder] ) )
            return self::$inputModes[$name][$holder];
        if ( self::isParameterOperator( $name ) )
            return self::$inputModes[$name][$holder] = 'param';
        $mode = 'input';
        if ( is_array( $object ) )
        {
            $tpl->loadAndRegisterOperators( $object );
            $object = isset( $tpl->Operators[$name] ) ? $tpl->Operators[$name] : null;
            $holder = is_object( $object ) ? get_class( $object ) : '';
        }
        if ( is_object( $object ) && method_exists( $object, 'operatorTemplateHints' ) )
        {
            $hints = $object->operatorTemplateHints();
            if ( $object instanceof eZTemplateControlOperator )
                $mode = 'param';
            if ( isset( $hints[$name] ) )
            {
                if ( ( isset( $hints[$name]['input-as-parameter'] ) && $hints[$name]['input-as-parameter'] === true &&
                       !in_array( $name, self::INPUT_AT_RUN_TIME, true ) ) ||
                     ( array_key_exists( 'input', $hints[$name] ) && $hints[$name]['input'] === false ) )
                    $mode = 'param';
            }
        }
        return self::$inputModes[$name][$holder] = $mode;
    }

    /**
     * Runs an Exponential template operator: $input|name( $parameters... ).
     *
     * For an operator in 'param' mode (operatorInputMode()) the input is passed as the first parameter, which is
     * what the compiled template system does. expvoid() as a parameter is an empty one: the operator uses its
     * default. An operator that reads its parameters only (isParameterOperator()) gets no first parameter for a
     * null input: op() has no parameters, and op(null, a, b) is op(a, b).
     *
     * @param string $name
     * @param mixed $input
     * @param array $parameters
     * @param string $proxyClass the proxy class the engine's values use (expTemplateValues::wrap())
     * @return mixed
     */
    public static function callOperator( $name, $input, array $parameters, $proxyClass = 'expTemplateObjectProxy' )
    {
        list( $tpl, $rootNamespace, $currentNamespace ) = self::contextOrDefault();
        if ( self::operatorInputMode( $name ) === 'param' && !( $input === null && self::isParameterOperator( $name ) ) )
        {
            array_unshift( $parameters, $input );
            $input = null;
        }
        $operatorParameters = array();
        foreach ( $parameters as $parameter )
        {
            if ( $parameter instanceof expTemplateVoid )
                $operatorParameters[] = array();
            else
                $operatorParameters[] = array( array( eZTemplate::TYPE_ARRAY, expTemplateValues::unwrap( $parameter ) ) );
        }
        $valueData = array( 'value' => $input instanceof expTemplateVoid ? null : expTemplateValues::unwrap( $input ) );
        $tpl->processOperator( $name, $operatorParameters, $rootNamespace, $currentNamespace, $valueData );
        return expTemplateValues::wrap( $valueData['value'], $proxyClass );
    }

    /**
     * Runs an Exponential template operator with an input, for an engine that tells the input apart from the
     * parameters (a Twig filter): $input|name( $parameters... ). An operator that ignores its input at run time
     * (the control operators cond and first_set, and the parameter-only operators, isParameterOperator()) gets the
     * input as the template system hands it, which it ignores; any other operator in 'param' mode
     * (operatorInputMode(): eq, gt, sum, concat ...) gets it as its first parameter, as compiled templates pass it.
     *
     * @param string $name
     * @param mixed $input
     * @param array $parameters
     * @param string $proxyClass
     * @return mixed
     */
    public static function callOperatorWithInput( $name, $input, array $parameters, $proxyClass = 'expTemplateObjectProxy' )
    {
        if ( self::ignoresInput( $name ) )
            return self::callOperatorDirect( $name, true, $input, $parameters, $proxyClass );
        return self::callOperator( $name, $input, $parameters, $proxyClass );
    }

    /** @var array name => bool, see ignoresInput() */
    private static $ignoresInput = array();

    /**
     * @param string $name
     * @return bool whether an operator ignores its input at run time: a control operator (cond, first_set) or a
     *              parameter-only operator
     */
    public static function ignoresInput( $name )
    {
        if ( !isset( self::$ignoresInput[$name] ) )
        {
            $ignores = self::isParameterOperator( $name );
            if ( !$ignores )
            {
                $tpl = eZTemplate::factory();
                if ( isset( $tpl->Operators[$name] ) && is_array( $tpl->Operators[$name] ) )
                    $tpl->loadAndRegisterOperators( $tpl->Operators[$name] );
                $ignores = isset( $tpl->Operators[$name] ) && $tpl->Operators[$name] instanceof eZTemplateControlOperator;
            }
            self::$ignoresInput[$name] = $ignores;
        }
        return self::$ignoresInput[$name];
    }

    /**
     * Runs an Exponential template operator exactly as a .tpl writes it, for an engine that tells the two forms
     * apart (Twig: a filter has an input, a function has none): $input|name( $parameters... ) when $hasInput,
     * name( $parameters... ) otherwise. No input mode is applied: the operator reads what the template system
     * would hand it. expvoid() as a parameter is an empty one.
     *
     * @param string $name
     * @param bool $hasInput
     * @param mixed $input
     * @param array $parameters
     * @param string $proxyClass
     * @return mixed
     */
    public static function callOperatorDirect( $name, $hasInput, $input, array $parameters, $proxyClass = 'expTemplateObjectProxy' )
    {
        list( $tpl, $rootNamespace, $currentNamespace ) = self::contextOrDefault();
        $operatorParameters = array();
        foreach ( $parameters as $parameter )
        {
            if ( $parameter instanceof expTemplateVoid )
                $operatorParameters[] = array();
            else
                $operatorParameters[] = array( array( eZTemplate::TYPE_ARRAY, expTemplateValues::unwrap( $parameter ) ) );
        }
        $valueData = array( 'value' => $hasInput && !( $input instanceof expTemplateVoid ) ? expTemplateValues::unwrap( $input ) : null );
        $tpl->processOperator( $name, $operatorParameters, $rootNamespace, $currentNamespace, $valueData );
        return expTemplateValues::wrap( $valueData['value'], $proxyClass );
    }

    /**
     * Runs an Exponential template function such as attribute_view_gui with named parameters and returns its
     * output. A small generated .tpl in the cache directory holds the call, so the template system compiles and
     * caches it like any template.
     *
     * @param string $name
     * @param array $parameters
     * @param string $engine the engine calling (smarty, twig): the generated file lives in <cache>/<engine>/functions
     * @return string
     */
    public static function callFunction( $name, array $parameters, $engine = 'smarty' )
    {
        list( $tpl ) = self::contextOrDefault();
        $names = array();
        foreach ( array_keys( $parameters ) as $parameterName )
        {
            if ( !preg_match( '/^[a-z][a-z0-9_]*$/i', (string)$parameterName ) )
                return self::failure( "invalid parameter name for $name: $parameterName", $engine );
            $names[] = $parameterName;
        }
        sort( $names );

        $dir = eZSys::cacheDirectory() . '/' . $engine . '/functions';
        $file = $dir . '/' . $name . '-' . md5( implode( ',', $names ) ) . '.tpl';
        if ( !is_file( $file ) )
        {
            $call = '{' . $name;
            foreach ( $names as $parameterName )
                $call .= ' ' . $parameterName . '=$' . $parameterName;
            $call .= '}';
            if ( !is_dir( $dir ) )
                eZDir::mkdir( $dir, false, true );
            eZFile::create( basename( $file ), $dir, $call, true );
        }

        $namespace = 'Exp' . ucfirst( $engine ) . ( ++self::$namespaceCounter );
        foreach ( $parameters as $parameterName => $value )
            $tpl->setVariable( $parameterName, expTemplateValues::unwrap( $value ), $namespace );
        $textElements = array();
        $extraParameters = array();
        $tpl->processURI( 'file:' . $file, true, $extraParameters, $textElements, $namespace, $namespace );
        foreach ( array_keys( $parameters ) as $parameterName )
            $tpl->unsetVariable( $parameterName, $namespace );
        return implode( '', $textElements );
    }

    /**
     * Runs an Exponential block function ({explblock}...{/explblock}) with its rendered body as the only child.
     *
     * @param string $name
     * @param array $parameters
     * @param string $content the rendered body
     * @return string
     */
    public static function callBlockFunction( $name, array $parameters, $content )
    {
        list( $tpl, $rootNamespace, $currentNamespace ) = self::contextOrDefault();
        $functionParameters = array();
        foreach ( $parameters as $parameterName => $value )
            $functionParameters[$parameterName] = array( array( eZTemplate::TYPE_ARRAY, expTemplateValues::unwrap( $value ) ) );
        $children = array( array( eZTemplate::NODE_TEXT, false, (string)$content ) );
        $textElements = array();
        $tpl->processFunction( $name, $textElements, $children, $functionParameters, false, $rootNamespace, $currentNamespace );
        return implode( '', $textElements );
    }

    /** Classes of the exceptions that end a request on a persistent worker (Velocity turns exit into one). */
    const EXIT_SIGNALS = array( 'Q_WebServer_ExitSignal' );

    /**
     * Rethrows the exception that ends the request (eZExecution::cleanExit() under Velocity, for a redirect or a
     * download an operator sends while a template renders), also when an engine wrapped it in an error of its
     * own; any other exception is left to the caller. An engine that catches what a template throws calls this
     * first, so the request ends as it does with a .tpl.
     *
     * @param Throwable $e
     * @throws Throwable the exit signal
     */
    public static function rethrowExit( Throwable $e )
    {
        for ( $current = $e; $current !== null; $current = $current->getPrevious() )
        {
            foreach ( self::EXIT_SIGNALS as $class )
            {
                if ( $current instanceof $class )
                    throw $current;
            }
        }
    }

    /**
     * Logs a rendering problem and returns what is shown in its place: an HTML comment when debug output is on,
     * nothing otherwise.
     *
     * @param string $message
     * @param string $label the engine (smarty, twig): the debug label and the comment's prefix
     * @return string
     */
    public static function failure( $message, $label = 'template' )
    {
        eZDebug::writeError( $message, $label );
        if ( eZDebug::isDebugEnabled() )
            return '<!-- ' . $label . ': ' . htmlspecialchars( $message, ENT_QUOTES ) . ' -->';
        return '';
    }
}
