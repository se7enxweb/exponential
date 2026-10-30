<?php
/**
 * Bridges the Exponential template system into Twig:
 *  - every template operator is a Twig filter (input|op(a, b): the operator with an input, passed as the operator
 *    takes it, expTemplateBridge::callOperator()) and a Twig function (op(a, b): the operator without an input), found by name when a template is compiled (undefined
 *    filter/function callbacks); exp_<name> calls the operator <name> where Twig has a filter or function of
 *    that name (exp_length, exp_date ...), or where the operator's own name begins with exp_;
 *  - every template function without children is a Twig function taking a hash of named parameters
 *    ({{ attribute_view_gui({attribute: a}) }}), every one with children a tag ({% explblock %}...{% endexplblock %});
 *  - the helpers the converter writes (expvar, expattr, expvoid, expsequence, expslice, expsectionloop, exprange,
 *    expcolumn), the exp* tags (expTwigTagTokenParser) and the capture of a template's variables for the
 *    write-back (expTwigCaptureVisitor).
 * Lookups are memoized per process.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigExtension extends \Twig\Extension\AbstractExtension
{
    /** Template functions the converter writes as Twig code or tags of its own; never bridged as functions. */
    const NOT_BRIDGED = array(
        'if', 'elseif', 'else', 'foreach', 'for', 'while', 'do', 'def', 'undef', 'set', 'let', 'default',
        'section', 'section-else', 'section-exclude', 'section-include', 'delimiter', 'switch', 'case',
        'set-block', 'append-block', 'cache-block', 'run-once', 'include', 'literal', 'ldelim', 'rdelim',
        'break', 'continue', 'skip',
    );

    /** The helper functions of the converters (the list lives in expTwigConverter, which needs no Twig). */
    const HELPERS = expTwigConverter::HELPERS;

    /** @var array kind => name => TwigFilter|TwigFunction|TokenParser|false */
    private static $found = array( 'filter' => array(), 'function' => array(), 'tag' => array() );

    /** @var array|null names of FunctionList */
    private static $functionList = null;

    public static function reset()
    {
        expTwigNativeOperators::reset();
        self::$found = array( 'filter' => array(), 'function' => array(), 'tag' => array() );
        self::$operatorNames = null;
        self::$functionList = null;
    }

    /**
     * Every operator of the installation as a filter, by the names the converter writes (the operator's name, and
     * exp_<name>), registered when the environment is built: a compiled template asks the environment for a filter on
     * every call, and a registered one is found by its name, where one supplied by the undefined-filter callback is
     * looked for again each time. Twig's own filters keep their names.
     */
    /**
     * @return bool whether every operator and template function is registered when the environment is built: in a
     *              persistent worker (Velocity), which builds it once and then saves the name lookup on every call.
     *              Elsewhere (PHP-FPM builds it on every request, the command line) building some 1,700 filters and
     *              functions costs more than a page uses: the names a template uses are resolved on demand through the
     *              undefined callbacks (filter(), twigFunction()), which give the same objects.
     */
    public static function registersUpFront()
    {
        return defined( 'QBIX_SERVER_VERSION' );
    }

    public function getFilters(): array
    {
        if ( !self::registersUpFront() )
            return array();
        $filters = array();
        foreach ( self::operatorNames() as $operator )
        {
            foreach ( array( $operator, 'exp_' . $operator ) as $name )
            {
                if ( isset( $filters[$name] ) || in_array( $name, expTwigConverter::TWIG_FILTERS, true ) || in_array( $name, expTwigConverter::TWIG_WORDS, true ) ||
                     !preg_match( '/^[a-z_][a-z0-9_]*$/i', $name ) )
                    continue;
                $filter = self::filter( $name );
                if ( $filter !== false )
                    $filters[$name] = $filter;
            }
        }
        return array_values( $filters );
    }

    /** @var string[]|null the operator names of the template system */
    private static $operatorNames = null;

    /**
     * @return string[] the names of every operator the template system has
     */
    private static function operatorNames()
    {
        if ( self::$operatorNames === null )
            self::$operatorNames = array_keys( eZTemplate::factory()->Operators );
        return self::$operatorNames;
    }

    public function getFunctions(): array
    {
        $proxy = expTwigRenderer::PROXY;
        // every operator (without an input) and every bridged template function, registered up front as the filters are
        $bridged = array();
        $names = self::registersUpFront() ? array_merge( self::operatorNames(), array_keys( eZTemplate::factory()->Functions ) ) : array();
        foreach ( $names as $operator )
        {
            foreach ( array( $operator, 'exp_' . $operator ) as $name )
            {
                if ( isset( $bridged[$name] ) || in_array( $name, expTwigConverter::TWIG_FUNCTIONS, true ) || in_array( $name, expTwigConverter::TWIG_WORDS, true ) ||
                     in_array( $name, self::HELPERS, true ) || !preg_match( '/^[a-z_][a-z0-9_]*$/i', $name ) )
                    continue;
                $function = self::twigFunction( $name );
                if ( $function !== false )
                    $bridged[$name] = $function;
            }
        }
        return array_merge( array_values( $bridged ), array(
            new \Twig\TwigFunction( 'expvar', array( 'expTwigBridgeCalls', 'globalVariable' ) ),
            new \Twig\TwigFunction( 'expattr', array( 'expTwigBridgeCalls', 'attributeOf' ) ),
            new \Twig\TwigFunction( 'expvoid', array( 'expTemplateBridge', 'void' ) ),
            new \Twig\TwigFunction( 'expsequence', array( 'expTemplateBridge', 'sequence' ) ),
            new \Twig\TwigFunction( 'expslice', array( 'expTemplateBridge', 'loopSlice' ) ),
            new \Twig\TwigFunction( 'expsectionloop', array( 'expTwigBridgeCalls', 'sectionLoop' ) ),
            new \Twig\TwigFunction( 'exprange', array( 'expTemplateBridge', 'range' ) ),
            new \Twig\TwigFunction( 'expcolumn', array( 'expTwigBridgeCalls', 'column' ) ),
            new \Twig\TwigFunction( 'expi18nargs', array( __CLASS__, 'i18nArguments' ) ),
        ) );
    }

    /**
     * expi18nargs(): the arguments of an i18n whose text was translated when the template was compiled
     * (expTwigConstantVisitor), put in as the compiled .tpl does: a numbered argument n is %(n % 9 + 1), a named one
     * its own key.
     */
    public static function i18nArguments( $text, $arguments )
    {
        $map = array();
        foreach ( is_array( $arguments ) ? expTemplateValues::unwrap( $arguments ) : array() as $key => $value )
        {
            if ( is_int( $key ) )
                $map['%' . ( ( $key % 9 ) + 1 )] = $value;
            else
                $map[$key] = $value;
        }
        return strtr( (string)$text, $map );
    }

    public function getTokenParsers(): array
    {
        $parsers = array();
        foreach ( array_keys( expTwigTagTokenParser::TAGS ) as $tag )
            $parsers[] = new expTwigTagTokenParser( $tag );
        return $parsers;
    }

    /** @var bool whether the node visitors that prepare a template for running are applied */
    private $visitors;

    /**
     * @param bool $visitors false: parse a template as written (the reverse converter), without the node visitors that
     *                       prepare it for running (captured variables, i18n translated when compiling, array lookups)
     */
    public function __construct( $visitors = true )
    {
        $this->visitors = (bool)$visitors;
    }

    public function getNodeVisitors(): array
    {
        if ( !$this->visitors )
            return array();
        return array( new expTwigCaptureVisitor(), new expTwigConstantVisitor(), new expTwigToStringVisitor(), new expTwigAttributeVisitor() );
    }

    /**
     * Registers the undefined filter, function and tag callbacks on an environment.
     */
    public static function registerCallbacks( \Twig\Environment $twig )
    {
        $twig->registerUndefinedFilterCallback( array( __CLASS__, 'filter' ) );
        $twig->registerUndefinedFunctionCallback( array( __CLASS__, 'twigFunction' ) );
        $twig->registerUndefinedTokenParserCallback( array( __CLASS__, 'tag' ) );
    }

    /**
     * @return string|null the operator a filter or function name calls: the name itself, or <name> for exp_<name>
     */
    public static function operatorFor( $name )
    {
        if ( expTemplateBridge::hasOperator( $name ) )
            return $name;
        if ( strncmp( $name, 'exp_', 4 ) === 0 && strlen( $name ) > 4 && expTemplateBridge::hasOperator( substr( $name, 4 ) ) )
            return substr( $name, 4 );
        return null;
    }

    /**
     * @return bool whether a name is exp_<name> for an operator the template system does not have (the converter
     *              writes it so for an unknown operator of the .tpl)
     */
    public static function isUnknownOperatorName( $name )
    {
        return strncmp( $name, 'exp_', 4 ) === 0 && strlen( $name ) > 4 && !in_array( $name, self::HELPERS, true );
    }

    /**
     * @return bool whether a template function is bridged as a Twig function
     */
    public static function isBridgedFunction( $name )
    {
        if ( self::$functionList === null )
        {
            $list = eZINI::instance( 'twig.ini' )->variable( 'TwigSettings', 'FunctionList' );
            self::$functionList = is_array( $list ) ? array_fill_keys( $list, true ) : array();
        }
        if ( isset( self::$functionList[$name] ) )
            return true;
        return !in_array( $name, self::NOT_BRIDGED, true ) && expTemplateBridge::hasFunction( $name ) && !expTemplateBridge::isBlockFunction( $name );
    }

    /**
     * Undefined filter callback: an operator with an input.
     *
     * @return \Twig\TwigFilter|false
     */
    public static function filter( $name )
    {
        if ( !array_key_exists( $name, self::$found['filter'] ) )
        {
            $operator = self::operatorFor( $name );
            if ( $operator === null && self::isUnknownOperatorName( $name ) )
            {
                // as the template system: an unknown operator passes its input through (and says so in the debug output)
                self::$found['filter'][$name] = new \Twig\TwigFilter( $name, function ( $input = null ) use ( $name )
                {
                    eZDebug::writeError( 'Unknown operator ' . substr( $name, 4 ), 'twig' );
                    return $input;
                } );
                return self::$found['filter'][$name];
            }
            if ( $operator !== null && expTwigNativeOperators::supports( $operator ) )
            {
                // a logic or arithmetic operator: in PHP, as the template compiler writes it for a .tpl; a static
                // callable, which the compiled template calls directly (expTwigFastOperators)
                self::$found['filter'][$name] = new \Twig\TwigFilter( $name, array( 'expTwigFastOperators', 'filter_' . $operator ) );
                return self::$found['filter'][$name];
            }
            // compiled as \expTwigBridgeCalls::filter('<operator>', ...) (expTwigBridgeFilterNode); the callable serves a lookup
            self::$found['filter'][$name] = $operator === null ? false : new \Twig\TwigFilter( $name,
                function ( $input, ...$parameters ) use ( $operator ) { return expTwigBridgeCalls::filter( $operator, $input, $parameters ); },
                array( 'node_class' => 'expTwigBridgeFilterNode' ) );
        }
        return self::$found['filter'][$name];
    }

    /**
     * Undefined function callback: a template function (a hash of named parameters) or an operator without an input.
     *
     * @return \Twig\TwigFunction|false
     */
    public static function twigFunction( $name )
    {
        if ( !array_key_exists( $name, self::$found['function'] ) )
        {
            $function = false;
            if ( self::isBridgedFunction( $name ) )
            {
                $function = new \Twig\TwigFunction( $name,
                    function ( $parameters = array() ) use ( $name ) { return expTwigBridgeCalls::templateFunction( $name, $parameters ); },
                    array( 'is_safe' => array( 'all' ), 'node_class' => 'expTwigBridgeFunctionNode' ) );
            }
            else if ( ( $operator = self::operatorFor( $name ) ) !== null && expTwigNativeOperators::supports( $operator ) )
            {
                $function = new \Twig\TwigFunction( $name, array( 'expTwigFastOperators', 'function_' . $operator ) );
            }
            else if ( ( $operator = self::operatorFor( $name ) ) !== null )
            {
                $function = new \Twig\TwigFunction( $name,
                    function ( ...$parameters ) use ( $operator ) { return expTwigBridgeCalls::call( $operator, $parameters ); },
                    array( 'node_class' => 'expTwigBridgeFunctionNode' ) );
            }
            else if ( self::isUnknownOperatorName( $name ) )
            {
                $function = new \Twig\TwigFunction( $name, function () use ( $name )
                {
                    eZDebug::writeError( 'Unknown operator ' . substr( $name, 4 ), 'twig' );
                    return null;
                } );
            }
            self::$found['function'][$name] = $function;
        }
        return self::$found['function'][$name];
    }

    /**
     * Undefined tag callback: a template function with children.
     *
     * @return \Twig\TokenParser\TokenParserInterface|false
     */
    public static function tag( $name )
    {
        if ( !array_key_exists( $name, self::$found['tag'] ) )
        {
            self::$found['tag'][$name] = !in_array( $name, self::NOT_BRIDGED, true ) && expTemplateBridge::isBlockFunction( $name )
                ? new expTwigTagTokenParser( $name, 'blockfunction' ) : false;
        }
        return self::$found['tag'][$name];
    }
}
