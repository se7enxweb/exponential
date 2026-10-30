<?php
/**
 * Converts Twig templates (.twig, .html.twig) back to Exponential templates (.tpl) that print the same output.
 *
 * The reverse of expTwigConverter. The .twig is parsed by Twig itself (with the tags and the bridge of lib/twig, so
 * every {% exp... %} tag, filter and function is read as Twig reads it), and the syntax tree is written out as .tpl:
 *
 *  - variables keep their names; Ns__name of a converted {section name=Ns} is $Ns:name again;
 *  - filters and functions become operators (x|op(a) -> $x|op(a), op(a) -> op(a)), without the exp_ prefix the
 *    forward converter adds; Twig's own operators (==, and, +, not, ~, ?:, ??, is defined ...) become operators too
 *    (eq(), and(), sum(), not(), concat(), cond(), first_set(), is_set());
 *  - {% set x = ... %} is {def} the first time the variable is assigned in its block and {set} after it, a set to null
 *    is {undef}; a capture ({% set x %}...{% endset %}) is {set-block};
 *  - the shapes the forward converter writes become the template functions again: loops with loop.* (a counter),
 *    {delimiter} (if not loop.first, __delimN), {break}/{continue}/{skip} (__stopN, __skipN), {for} (exprange),
 *    {section} (expsectionloop), {switch} (__switchN), {do} (__doN, expwhile), the declarations it adds at the top
 *    ({% set x = x ?? null %}, dropped), and every exp* tag of lib/twig;
 *  - a template function called as a function ({{ attribute_view_gui({attribute: a}) }}) is its tag again.
 *
 * Whitespace: the text of Twig's syntax tree is what Twig prints (the newline after a {% %} tag is already gone); the
 * template system drops the newline right after and right before every function tag, so each text is written with
 * the newlines it needs to print exactly that.
 *
 * What has no exact .tpl form (macros, blocks and inheritance, Twig's own filters and functions without a template
 * operator, method calls ...) is not guessed: it is reported (warnings()) and kept in the .tpl as a comment
 * {* TWIG (not converted: <why>) line <n> *}, which prints nothing.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigReverseConverter
{
    /** Variables the forward converter adds for itself. */
    const HELPER_PATTERN = '/^__(sequence|delim|switch|do|skip|stop|exclude)\d+$/';

    /** Twig's binary operators and the template operator of each. */
    const BINARY = array(
        'EqualBinary' => 'eq', 'NotEqualBinary' => 'ne', 'LessBinary' => 'lt', 'GreaterBinary' => 'gt',
        'LessEqualBinary' => 'le', 'GreaterEqualBinary' => 'ge', 'AddBinary' => 'sum', 'SubBinary' => 'sub',
        'MulBinary' => 'mul', 'DivBinary' => 'div', 'ModBinary' => 'mod', 'AndBinary' => 'and', 'OrBinary' => 'or',
        'ConcatBinary' => 'concat',
    );

    /** Twig filters with a template operator that does the same. */
    const FILTERS = array(
        'length' => 'count', 'upper' => 'upcase', 'lower' => 'downcase', 'join' => 'implode', 'capitalize' => 'upfirst',
        'title' => 'upword', 'nl2br' => 'nl2br', 'trim' => 'trim', 'round' => 'round', 'abs' => 'abs', 'merge' => 'merge',
        'reverse' => 'reverse',
    );

    /** @var eZTemplate */
    private $tpl;

    /** @var string[] */
    private $warnings = array();

    /** @var string */
    private $file = '';

    /** @var array[] block scopes of defined variables */
    private $scopes = array( array() );

    /** @var array[] open loops: item, key, kind, counter, sourceRef, used, usedSource, skip, stop, delim, namespace */
    private $loops = array();

    /** @var string[] current namespace (sections) */
    private $namespace = array();

    /** @var string[] namespaces of the sections seen, longest first */
    private $namespaces = array();

    /** @var \Twig\Environment|null */
    private static $parseEnvironment = null;

    public function __construct( ?eZTemplate $tpl = null )
    {
        $this->tpl = $tpl ?: eZTemplate::factory();
    }

    /** @return string[] what could not be converted exactly, in the last convert() call */
    public function warnings()
    {
        return $this->warnings;
    }

    public function convertFile( $file )
    {
        return $this->convert( file_get_contents( $file ), $file );
    }

    /**
     * The environment the .twig is parsed with: the bridge and its tags, any filter or function name accepted,
     * Twig's optimizations off (the tree stays as written).
     */
    private static function parseEnvironment()
    {
        if ( self::$parseEnvironment === null )
        {
            if ( !expTwigRenderer::loadLibrary() )
                throw new RuntimeException( 'the Twig library is not available' );
            $twig = new \Twig\Environment( new \Twig\Loader\ArrayLoader( array() ), array( 'cache' => false, 'autoescape' => false, 'optimizations' => 0 ) );
            // the tree as written: none of the visitors that prepare a template for running (an i18n stays an i18n)
            $twig->addExtension( new expTwigExtension( false ) );
            expTwigExtension::registerCallbacks( $twig );
            $twig->registerUndefinedFilterCallback( function ( $name ) { return new \Twig\TwigFilter( $name, 'strval' ); } );
            $twig->registerUndefinedFunctionCallback( function ( $name ) { return new \Twig\TwigFunction( $name, 'strval' ); } );
            self::$parseEnvironment = $twig;
        }
        return self::$parseEnvironment;
    }

    /**
     * @param string $text the .twig text
     * @param string $file name used in messages
     * @return string the .tpl text
     * @throws \Twig\Error\SyntaxError when Twig cannot parse the text
     */
    public function convert( $text, $file = 'template' )
    {
        $this->warnings = array();
        $this->file = $file;
        $this->scopes = array( array() );
        $this->loops = array();
        $this->namespace = array();
        $this->namespaces = array();

        $twig = self::parseEnvironment();
        $module = $twig->parse( $twig->tokenize( new \Twig\Source( (string)$text, (string)$file ) ) );
        if ( $module->hasNode( 'parent' ) && $module->getNode( 'parent' ) instanceof \Twig\Node\Expression\AbstractExpression )
            $this->warn( 0, '{% extends %}: template inheritance has no template form; only the template\'s own text is converted' );
        foreach ( array( 'blocks', 'macros' ) as $kind )
        {
            if ( $module->hasNode( $kind ) && count( $module->getNode( $kind ) ) )
                $this->warn( 0, "{% $kind %} have no template form" );
        }
        $this->collectNamespaces( $module->getNode( 'body' ) );
        $statements = $this->statements( $module->getNode( 'body' ) );
        // the declarations the forward converter puts at the top: {% set x = x ?? null %}
        while ( $statements && $this->isHoist( $statements[0] ) )
            array_shift( $statements );
        return $this->emit( $this->convertNodes( $statements ) );
    }

    // ----------------------------------------------------------------------------------------
    // Statements

    /**
     * The statements of a body as a flat list (Nodes and BodyNode unwrapped, the for loop's own node left out).
     *
     * @return \Twig\Node\Node[]
     */
    private function statements( \Twig\Node\Node $node )
    {
        if ( $node instanceof \Twig\Node\BodyNode || $node instanceof \Twig\Node\Nodes || get_class( $node ) === \Twig\Node\Node::class )
        {
            $list = array();
            foreach ( $node as $child )
                $list = array_merge( $list, $this->statements( $child ) );
            return $list;
        }
        if ( $node instanceof \Twig\Node\ForLoopNode || $node instanceof expTwigCaptureNode )
            return array();
        if ( $node instanceof \Twig\Node\TextNode && $node->getAttribute( 'data' ) === '' )
            return array();
        return array( $node );
    }

    /** {% set x = x ?? null %} */
    private function isHoist( \Twig\Node\Node $node )
    {
        if ( !( $node instanceof \Twig\Node\SetNode ) || $node->getAttribute( 'capture' ) )
            return false;
        $name = $this->assignedName( $node );
        $value = $this->assignedValue( $node );
        return $name !== null && $value instanceof \Twig\Node\Expression\Binary\NullCoalesceBinary &&
               $this->isName( $value->getNode( 'left' ), $name ) && $this->isNull( $value->getNode( 'right' ) );
    }

    /** The one variable a {% set %} assigns, or null. */
    private function assignedName( \Twig\Node\Node $node )
    {
        if ( !( $node instanceof \Twig\Node\SetNode ) )
            return null;
        $names = $node->getNode( 'names' );
        if ( $names instanceof \Twig\Node\Expression\AbstractExpression && $names->hasAttribute( 'name' ) )
            return $names->getAttribute( 'name' );
        if ( count( $names ) === 1 )
        {
            foreach ( $names as $n )
                return $n->hasAttribute( 'name' ) ? $n->getAttribute( 'name' ) : null;
        }
        return null;
    }

    /** The value expression of a single assignment. */
    private function assignedValue( \Twig\Node\SetNode $node )
    {
        $values = $node->getNode( 'values' );
        if ( !( $values instanceof \Twig\Node\Expression\AbstractExpression ) && count( $values ) === 1 )
        {
            foreach ( $values as $v )
                return $v;
        }
        return $values;
    }

    private function helperName( \Twig\Node\Node $node )
    {
        $name = $this->assignedName( $node );
        return $name !== null && !$node->getAttribute( 'capture' ) && preg_match( self::HELPER_PATTERN, $name ) ? $name : null;
    }

    private function isName( \Twig\Node\Node $e, $name )
    {
        $e = $this->condition( $e );
        return $e instanceof \Twig\Node\Expression\NameExpression && $e->getAttribute( 'name' ) === $name;
    }

    private function isNull( \Twig\Node\Node $e )
    {
        return $e instanceof \Twig\Node\Expression\ConstantExpression && $e->getAttribute( 'value' ) === null;
    }

    private function isConstant( \Twig\Node\Node $e, $value )
    {
        return $e instanceof \Twig\Node\Expression\ConstantExpression && $e->getAttribute( 'value' ) === $value;
    }

    /** A condition without the "is true" test Twig wraps around every if condition. */
    private function condition( \Twig\Node\Node $e )
    {
        while ( $e instanceof \Twig\Node\Expression\Test\TrueTest )
            $e = $e->getNode( 'node' );
        return $e;
    }

    /** loop.<property> */
    private function isLoopProperty( \Twig\Node\Node $e, $property )
    {
        $e = $this->condition( $e );
        return $e instanceof \Twig\Node\Expression\GetAttrExpression && $this->isName( $e->getNode( 'node' ), 'loop' ) &&
               $this->isConstant( $e->getNode( 'attribute' ), $property );
    }

    /** not <name> */
    private function isNotName( \Twig\Node\Node $e, $name )
    {
        $e = $this->condition( $e );
        if ( $e instanceof \Twig\Node\Expression\FunctionExpression && $e->getAttribute( 'name' ) === 'exp_not' && count( $args = $this->arguments( $e ) ) === 1 )
            return $this->isName( $args[0], $name );
        return $e instanceof \Twig\Node\Expression\Unary\NotUnary && $this->isName( $e->getNode( 'node' ), $name );
    }

    private function function( \Twig\Node\Node $e, $name )
    {
        return $e instanceof \Twig\Node\Expression\FunctionExpression && $e->getAttribute( 'name' ) === $name;
    }

    /** @return \Twig\Node\Node[] the arguments of a call, in order */
    private function arguments( \Twig\Node\Node $call )
    {
        $args = array();
        if ( $call->hasNode( 'arguments' ) )
        {
            foreach ( $call->getNode( 'arguments' ) as $key => $arg )
            {
                if ( !is_int( $key ) && !ctype_digit( (string)$key ) )
                    throw new UnexpectedValueException( "the named argument $key has no template form" );
                $args[] = $arg;
            }
        }
        return $args;
    }

    /**
     * The sections' namespaces: the key variables Ns__key of every loop over expsectionloop().
     */
    private function collectNamespaces( \Twig\Node\Node $node )
    {
        if ( $node instanceof \Twig\Node\ForNode && $this->function( $node->getNode( 'seq' ), 'expsectionloop' ) )
        {
            $key = $node->getNode( 'key_target' )->getAttribute( 'name' );
            if ( preg_match( '/^(.+)__key$/', $key, $m ) )
                $this->addNamespace( $m[1] );
        }
        foreach ( $node as $child )
            $this->collectNamespaces( $child );
    }

    private function addNamespace( $namespace )
    {
        if ( $namespace === '' || in_array( $namespace, $this->namespaces, true ) )
            return;
        $this->namespaces[] = $namespace;
        usort( $this->namespaces, function ( $a, $b ) { return strlen( $b ) - strlen( $a ); } );
    }

    // items: array( 'T', text ), array( 'F', function tag code ), array( 'V', output code ), array( 'C', comment )

    /**
     * @param \Twig\Node\Node[] $nodes statements
     * @return array[] items
     */
    private function convertNodes( array $nodes )
    {
        $items = array();
        $count = count( $nodes );
        $loop = end( $this->loops );
        for ( $i = 0; $i < $count; ++$i )
        {
            $node = $nodes[$i];

            // the jumps of the current loop
            if ( $loop && $node instanceof \Twig\Node\SetNode && ( $helper = $this->helperName( $node ) ) !== null )
            {
                $value = $this->assignedValue( $node );
                if ( $helper === $loop['skip'] && $this->isConstant( $value, true ) )
                {
                    $items[] = array( 'F', 'continue' );
                    continue;
                }
                if ( $helper === $loop['skip'] && $this->isConstant( $value, false ) )
                    continue;
                if ( $helper === $loop['stop'] && $this->isConstant( $value, true ) && isset( $nodes[$i + 1] ) &&
                     $this->helperName( $nodes[$i + 1] ) === $loop['skip'] )
                {
                    $items[] = array( 'F', 'break' );
                    ++$i;
                    continue;
                }
                if ( $helper === $loop['delim'] && $this->isConstant( $value, false ) && isset( $nodes[$i + 1] ) &&
                     $this->helperName( $nodes[$i + 1] ) === $loop['skip'] )
                {
                    $items[] = array( 'F', 'skip' );
                    ++$i;
                    continue;
                }
            }
            // {% if not __skipN %}rest{% endif %}: the rest of the round
            if ( $loop && $loop['skip'] && $node instanceof \Twig\Node\IfNode && !$node->hasNode( 'else' ) &&
                 count( $node->getNode( 'tests' ) ) === 2 && $this->isNotName( $this->condition( $node->getNode( 'tests' )->getNode( '0' ) ), $loop['skip'] ) )
            {
                $items = array_merge( $items, $this->convertNodes( $this->statements( $node->getNode( 'tests' )->getNode( '1' ) ) ) );
                continue;
            }

            // helper assignments before the loop, switch or do that uses them
            if ( $node instanceof \Twig\Node\SetNode && $this->helperName( $node ) !== null )
            {
                $prelude = array();
                $j = $i;
                while ( $j < $count && ( $helper = $this->helperName( $nodes[$j] ) ) !== null && !isset( $prelude[$helper] ) )
                {
                    $prelude[$helper] = $nodes[$j];
                    ++$j;
                }
                if ( $j < $count )
                {
                    $result = $this->convertStructure( $nodes[$j], $prelude, array_slice( $nodes, $j + 1 ) );
                    if ( $result !== null )
                    {
                        list( $structureItems, $consumed, $skipAfter ) = $result + array( 2 => 0 );
                        foreach ( $prelude as $helper => $n )
                        {
                            if ( !in_array( $helper, $consumed, true ) )
                                $items = array_merge( $items, $this->convertNode( $n ) );
                        }
                        $items = array_merge( $items, $structureItems );
                        $i = $j + $skipAfter;
                        continue;
                    }
                }
            }
            $structure = $this->convertStructure( $node, array(), array_slice( $nodes, $i + 1 ) );
            if ( $structure !== null )
            {
                $items = array_merge( $items, $structure[0] );
                $i += isset( $structure[2] ) ? $structure[2] : 0;
                continue;
            }
            $items = array_merge( $items, $this->convertNode( $node ) );
        }
        return $items;
    }

    /**
     * A loop, switch or do with the helper assignments before it.
     *
     * @return array|null ( items, consumed helper names[, statements after it that were consumed too] )
     */
    private function convertStructure( \Twig\Node\Node $node, array $prelude, array $after )
    {
        try
        {
            if ( $node instanceof \Twig\Node\ForNode )
                return $this->forNode( $node, $prelude );
            if ( $node instanceof expTwigTagNode && $node->getAttribute( 'kind' ) === 'while' )
                return $this->whileNode( $node, $prelude );
            if ( $node instanceof \Twig\Node\IfNode && $prelude )
                return $this->switchNode( $node, $prelude ) ?: $this->sectionShowNode( $node, $prelude );
        }
        catch ( UnexpectedValueException $e )
        {
            return array( $this->unconverted( $node, $e->getMessage() ), array() );
        }
        return null;
    }

    private function convertNode( \Twig\Node\Node $node )
    {
        try
        {
            if ( $node instanceof \Twig\Node\TextNode )
                return array( array( 'T', $node->getAttribute( 'data' ) ) );
            if ( $node instanceof \Twig\Node\PrintNode )
                return $this->printNode( $node->getNode( 'expr' ) );
            if ( $node instanceof \Twig\Node\SetNode )
                return $this->setNode( $node );
            if ( $node instanceof \Twig\Node\IfNode )
                return $this->ifNode( $node );
            if ( $node instanceof expTwigTagNode )
                return $this->tagNode( $node );
            if ( $node instanceof \Twig\Node\ForNode )
                return $this->forNode( $node, array() )[0];
            throw new UnexpectedValueException( 'the Twig node ' . ( new ReflectionClass( $node ) )->getShortName() . ' has no template form' );
        }
        catch ( UnexpectedValueException $e )
        {
            return $this->unconverted( $node, $e->getMessage() );
        }
    }

    private function unconverted( \Twig\Node\Node $node, $why )
    {
        $line = $node->getTemplateLine();
        $this->warn( $line, $why );
        return array( array( 'C', ' TWIG (not converted: ' . $why . ') line ' . $line . ' ' ) );
    }

    private function printNode( \Twig\Node\Node $e )
    {
        // a template function called as a Twig function: its tag
        if ( $e instanceof \Twig\Node\Expression\FunctionExpression && class_exists( 'expTwigExtension' ) &&
             expTwigExtension::isBridgedFunction( $e->getAttribute( 'name' ) ) )
        {
            $args = $this->arguments( $e );
            $code = $e->getAttribute( 'name' );
            if ( $args )
            {
                if ( count( $args ) !== 1 || !( $args[0] instanceof \Twig\Node\Expression\ArrayExpression ) )
                    throw new UnexpectedValueException( 'a template function called with something other than one hash' );
                foreach ( $args[0]->getKeyValuePairs() as $pair )
                {
                    if ( !( $pair['key'] instanceof \Twig\Node\Expression\ConstantExpression ) || !preg_match( '/^[a-z_][a-z0-9_]*$/i', (string)$pair['key']->getAttribute( 'value' ) ) )
                        throw new UnexpectedValueException( 'a template function parameter name that is not a constant name' );
                    $code .= ' ' . $pair['key']->getAttribute( 'value' ) . '=' . $this->expr( $pair['value'] );
                }
            }
            return array( array( 'F', $code ) );
        }
        // expvoid(), an empty parameter of a broken source, prints nothing
        $code = $this->expr( $e );
        return $code === '' ? array() : array( array( 'V', $code ) );
    }

    // ----------------------------------------------------------------------------------------
    // Assignments

    private function pushScope()
    {
        $this->scopes[] = array();
    }

    private function popScope()
    {
        array_pop( $this->scopes );
    }

    private function isDefined( $name )
    {
        foreach ( $this->scopes as $scope )
        {
            if ( isset( $scope[$name] ) )
                return true;
        }
        return false;
    }

    private function define( $name )
    {
        $this->scopes[count( $this->scopes ) - 1][$name] = true;
    }

    private function undefine( $name )
    {
        foreach ( array_keys( $this->scopes ) as $i )
            unset( $this->scopes[$i][$name] );
    }

    private function setNode( \Twig\Node\SetNode $node )
    {
        $name = $this->assignedName( $node );
        if ( $name === null )
            throw new UnexpectedValueException( 'an assignment to more than one variable' );
        if ( $node->getAttribute( 'capture' ) )
        {
            $values = $node->getNode( 'values' );
            $body = $values instanceof \Twig\Node\CaptureNode ? $values->getNode( 'body' ) : $values;
            list( $namespace, $base ) = $this->splitNamespace( $name );
            $head = 'set-block variable=' . $base . ( $namespace === '' && !$this->namespace ? ' scope=root' : '' );
            $this->define( $name );
            $this->pushScope();
            $items = array_merge( array( array( 'F', $head ) ), $this->convertNodes( $this->statements( $body ) ), array( array( 'F', '/set-block' ) ) );
            $this->popScope();
            return $items;
        }
        return $this->assignment( $name, $this->assignedValue( $node ) );
    }

    private function assignment( $name, \Twig\Node\Node $value )
    {
        list( $namespace, $base ) = $this->splitNamespace( $name );
        if ( $namespace !== '' )
        {
            if ( str_replace( '__', ':', $namespace ) !== implode( ':', $this->namespace ) )
                throw new UnexpectedValueException( "an assignment to $name, a variable of the section $namespace, outside of it" );
            return array( array( 'F', 'set $' . $base . ' = ' . $this->expr( $value ) ) );
        }
        if ( $this->isNull( $value ) )
        {
            $this->undefine( $name );
            return array( array( 'F', 'undef $' . $name ) );
        }
        $code = $this->expr( $value );
        if ( $code === '' )
            $code = 'null()';
        $verb = $this->isDefined( $name ) && !$this->namespace ? 'set' : 'def';
        $this->define( $name );
        return array( array( 'F', $verb . ' $' . $name . ' = ' . $code ) );
    }

    // ----------------------------------------------------------------------------------------
    // Tags

    private function ifNode( \Twig\Node\IfNode $node )
    {
        $tests = $node->getNode( 'tests' );
        $items = array();
        for ( $k = 0; $k < count( $tests ); $k += 2 )
        {
            $condition = $this->condition( $tests->getNode( (string)$k ) );
            $items[] = array( 'F', ( $k === 0 ? 'if ' : 'elseif ' ) . $this->expr( $condition ) );
            $this->pushScope();
            $items = array_merge( $items, $tests->hasNode( (string)( $k + 1 ) ) ? $this->convertNodes( $this->statements( $tests->getNode( (string)( $k + 1 ) ) ) ) : array() );
            $this->popScope();
        }
        if ( $node->hasNode( 'else' ) )
        {
            $items[] = array( 'F', 'else' );
            $this->pushScope();
            $items = array_merge( $items, $this->convertNodes( $this->statements( $node->getNode( 'else' ) ) ) );
            $this->popScope();
        }
        $items[] = array( 'F', '/if' );
        return $items;
    }

    private function tagNode( expTwigTagNode $node )
    {
        $kind = $node->getAttribute( 'kind' );
        $params = $node->hasNode( 'params' ) ? $node->getNode( 'params' ) : null;
        $body = function () use ( $node )
        {
            $this->pushScope();
            $items = $this->convertNodes( $this->statements( $node->getNode( 'body' ) ) );
            $this->popScope();
            return $items;
        };
        switch ( $kind )
        {
            case 'include':
                return array( array( 'F', 'include' . $this->attributes( $params ) ) );
            case 'set':
                $p = $this->params( $params );
                if ( !isset( $p['name'] ) || !( $p['name'] instanceof \Twig\Node\Expression\ConstantExpression ) )
                    throw new UnexpectedValueException( '{% expset %} without a constant name' );
                $code = 'set $' . $p['name']->getAttribute( 'value' ) . ' = ' . ( isset( $p['value'] ) ? $this->expr( $p['value'] ) : 'null()' );
                if ( isset( $p['scope'] ) )
                    $code .= ' scope=' . $this->constantName( $p['scope'] );
                if ( isset( $p['namespace'] ) )
                    $code .= ' name=' . $this->constantName( $p['namespace'] );
                return array( array( 'F', $code ) );
            case 'undefall':
                $this->scopes = array_map( function () { return array(); }, $this->scopes );
                return array( array( 'F', 'undef' ) );
            case 'setblock':
            case 'appendblock':
                $p = $this->params( $params );
                if ( !isset( $p['variable'] ) )
                    throw new UnexpectedValueException( '{% expsetblock %} without a variable' );
                $tag = $kind === 'appendblock' ? 'append-block' : 'set-block';
                $head = $tag . ' variable=' . $this->constantName( $p['variable'] );
                if ( isset( $p['scope'] ) )
                    $head .= ' scope=' . $this->constantName( $p['scope'] );
                if ( isset( $p['name'] ) )
                    $head .= ' name=' . $this->constantName( $p['name'] );
                return array_merge( array( array( 'F', $head ) ), $body(), array( array( 'F', '/' . $tag ) ) );
            case 'cacheblock':
                return array_merge( array( array( 'F', 'cache-block' . $this->attributes( $params, array( 'id' ) ) ) ), $body(), array( array( 'F', '/cache-block' ) ) );
            case 'runonce':
                return array_merge( array( array( 'F', 'run-once' ) ), $body(), array( array( 'F', '/run-once' ) ) );
            case 'blockfunction':
                $name = $node->getAttribute( 'name' );
                return array_merge( array( array( 'F', $name . $this->attributes( $params ) ) ), $body(), array( array( 'F', '/' . $name ) ) );
            case 'while':
                return $this->whileNode( $node, array() )[0];
        }
        throw new UnexpectedValueException( "the tag $kind has no template form" );
    }

    /** @return \Twig\Node\Node[] name => expression */
    private function params( $params )
    {
        $list = array();
        if ( $params )
        {
            foreach ( $params as $name => $value )
                $list[$name] = $value;
        }
        return $list;
    }

    /** " name=value ..." of a function tag */
    private function attributes( $params, array $leaveOut = array() )
    {
        $code = '';
        foreach ( $this->params( $params ) as $name => $value )
        {
            if ( in_array( $name, $leaveOut, true ) )
                continue;
            $code .= ' ' . $name . '=' . $this->expr( $value );
        }
        return $code;
    }

    /** A constant written as a bare name (scope=global, variable=x). */
    private function constantName( \Twig\Node\Node $e )
    {
        if ( !( $e instanceof \Twig\Node\Expression\ConstantExpression ) || !preg_match( '/^[a-z_][a-z0-9_:-]*$/i', (string)$e->getAttribute( 'value' ) ) )
            throw new UnexpectedValueException( 'a value that must be a constant name' );
        return (string)$e->getAttribute( 'value' );
    }

    /**
     * {% for %}: {foreach}, {for} (over exprange) or {section} (over expsectionloop), with the jumps, the
     * delimiter, the sequence and loop.* of the forward converter.
     */
    private function forNode( \Twig\Node\ForNode $node, array $prelude )
    {
        $key = $node->getNode( 'key_target' )->getAttribute( 'name' );
        $key = $key === '_key' ? null : $key;
        $item = $node->getNode( 'value_target' )->getAttribute( 'name' );
        $source = $node->getNode( 'seq' );
        $else = $node->hasNode( 'else' ) ? $this->statements( $node->getNode( 'else' )->getNode( 'body' ) ) : null;
        $consumed = array();
        $body = $this->statements( $node->getNode( 'body' ) );

        // jumps: {% if not __stopN %} around the round, {% set __skipN = false %} first
        $stop = null;
        $skip = null;
        if ( count( $body ) === 1 && $body[0] instanceof \Twig\Node\IfNode && !$body[0]->hasNode( 'else' ) && count( $body[0]->getNode( 'tests' ) ) === 2 )
        {
            $test = $this->condition( $body[0]->getNode( 'tests' )->getNode( '0' ) );
            $stopName = null;
            foreach ( array_keys( $prelude ) as $candidate )
            {
                if ( strncmp( $candidate, '__stop', 6 ) === 0 && $this->isNotName( $test, $candidate ) )
                    $stopName = $candidate;
            }
            if ( $stopName !== null && ( $name = $stopName ) !== null )
            {
                $stop = $name;
                $consumed[] = $stop;
                $body = $this->statements( $body[0]->getNode( 'tests' )->getNode( '1' ) );
            }
        }
        if ( $body && ( $helper = $this->helperName( $body[0] ) ) !== null && strncmp( $helper, '__skip', 6 ) === 0 && $this->isConstant( $this->assignedValue( $body[0] ), false ) )
        {
            $skip = $helper;
            array_shift( $body );
        }

        if ( $this->function( $source, 'expsectionloop' ) )
            return $this->sectionNode( $node, $source, $key, $item, $body, $prelude, $consumed, $else, $skip, $stop );

        $isFor = $this->function( $source, 'exprange' ) && $key === null && count( $this->arguments( $source ) ) === 2;
        $parameters = '';
        if ( $isFor )
        {
            list( $from, $to ) = $this->arguments( $source );
            $head = 'for ' . $this->expr( $from ) . ' to ' . $this->expr( $to ) . ' as $' . $item;
        }
        else if ( $this->function( $source, 'expslice' ) && count( $this->arguments( $source ) ) === 4 )
        {
            list( $array, $offset, $max, $reverse ) = $this->arguments( $source );
            if ( !$this->isConstant( $offset, 0 ) )
                $parameters .= ' offset ' . $this->expr( $offset );
            if ( !$this->isNull( $max ) )
                $parameters .= ' max ' . $this->expr( $max );
            if ( $reverse instanceof \Twig\Node\Expression\ConstantExpression && $reverse->getAttribute( 'value' ) )
                $parameters .= ' reverse';
            else if ( !( $reverse instanceof \Twig\Node\Expression\ConstantExpression ) )
                throw new UnexpectedValueException( 'a foreach reverse that is not a constant' );
            $source = $array;
        }

        // sequence: {% set s = expsequence(__sequenceN, loop.index0) %} first in the round
        if ( $body && $body[0] instanceof \Twig\Node\SetNode && !$body[0]->getAttribute( 'capture' ) && ( $sequenceVariable = $this->assignedName( $body[0] ) ) !== null )
        {
            $value = $this->assignedValue( $body[0] );
            if ( $this->function( $value, 'expsequence' ) && count( $args = $this->arguments( $value ) ) === 2 &&
                 $args[0] instanceof \Twig\Node\Expression\NameExpression && isset( $prelude[$args[0]->getAttribute( 'name' )] ) &&
                 $this->isLoopProperty( $args[1], 'index0' ) )
            {
                $consumed[] = $args[0]->getAttribute( 'name' );
                $parameters = ' sequence ' . $this->expr( $this->assignedValue( $prelude[$args[0]->getAttribute( 'name' )] ) ) . ' as $' . $sequenceVariable . $parameters;
                array_shift( $body );
            }
        }
        list( $delimiter, $delimFlag, $body ) = $this->delimiterOf( $body, $prelude, $consumed );

        $sourceCode = $isFor ? null : $this->expr( $source );
        $simpleSource = $sourceCode !== null && preg_match( '/^\$[a-zA-Z0-9_:#.]+$/', $sourceCode );
        $counter = '__index_' . $item;
        $sourceRef = $simpleSource || $sourceCode === null ? (string)$sourceCode : '$__source_' . $item;
        if ( !$isFor )
            $head = 'foreach ' . $sourceCode . ' as ' . ( $key !== null ? '$' . $key . ' => ' : '' ) . '$' . $item . $parameters;
        else
            $head .= $parameters;

        $this->loops[] = array( 'item' => $item, 'key' => $key, 'kind' => 'foreach', 'counter' => $counter, 'sourceRef' => $sourceRef,
                                'used' => false, 'usedSource' => false, 'skip' => $skip, 'stop' => $stop, 'delim' => $delimFlag, 'namespace' => '' );
        $this->pushScope();
        $this->define( $item );
        if ( $key !== null )
            $this->define( $key );
        $delimiterItems = $delimiter !== null ? $this->convertNodes( $delimiter ) : null;
        $bodyItems = $this->convertNodes( $body );
        $this->popScope();
        $loop = array_pop( $this->loops );
        if ( $isFor && $loop['usedSource'] )
            throw new UnexpectedValueException( 'loop.last or loop.length of a for over a range' );

        $items = array();
        if ( $loop['usedSource'] && !$simpleSource )
        {
            $items[] = array( 'F', 'def ' . $sourceRef . ' = ' . $sourceCode );
            $head = 'foreach ' . $sourceRef . substr( $head, strlen( 'foreach ' . $sourceCode ) );
        }
        if ( $loop['used'] )
            $items[] = array( 'F', 'def $' . $counter . ' = -1' );
        $items[] = array( 'F', $head );
        if ( $loop['used'] )
            $items[] = array( 'F', ( $this->namespace ? 'def' : 'set' ) . ' $' . $counter . ' = $' . $counter . '|inc' );
        if ( $delimiterItems !== null )
            $items = array_merge( $items, array( array( 'F', 'delimiter' ) ), $delimiterItems, array( array( 'F', '/delimiter' ) ) );
        $items = array_merge( $items, $bodyItems, array( array( 'F', $isFor ? '/for' : '/foreach' ) ) );
        if ( $loop['used'] )
            $items[] = array( 'F', 'undef $' . $counter );
        if ( $loop['usedSource'] && !$simpleSource )
            $items[] = array( 'F', 'undef ' . $sourceRef );
        if ( $else !== null )
        {
            $this->pushScope();
            $elseItems = $this->convertNodes( $else );
            $this->popScope();
            $items = array_merge( array( array( 'F', 'if ' . ( $isFor ? 'true()' : $sourceCode . '|count' ) ) ), $items,
                                  array( array( 'F', 'else' ) ), $elseItems, array( array( 'F', '/if' ) ) );
        }
        return array( $items, $consumed );
    }

    /**
     * The delimiter at the start of a round: {% if not loop.first %}...{% endif %}, or with a {skip} in the loop
     * {% if __delimN %}...{% endif %}{% set __delimN = true %} (with __delimN = false before the loop).
     */
    private function delimiterOf( array $body, array $prelude, array &$consumed )
    {
        if ( !$body || !( $body[0] instanceof \Twig\Node\IfNode ) || $body[0]->hasNode( 'else' ) || count( $body[0]->getNode( 'tests' ) ) !== 2 )
            return array( null, null, $body );
        $tests = $body[0]->getNode( 'tests' );
        $condition = $this->condition( $tests->getNode( '0' ) );
        $children = $tests->hasNode( '1' ) ? $this->statements( $tests->getNode( '1' ) ) : array();
        if ( ( $condition instanceof \Twig\Node\Expression\Unary\NotUnary && $this->isLoopProperty( $condition->getNode( 'node' ), 'first' ) ) ||
             ( $this->function( $condition, 'exp_not' ) && count( $args = $this->arguments( $condition ) ) === 1 && $this->isLoopProperty( $args[0], 'first' ) ) )
            return array( $children, null, array_slice( $body, 1 ) );
        if ( $condition instanceof \Twig\Node\Expression\NameExpression && strncmp( $flag = $condition->getAttribute( 'name' ), '__delim', 7 ) === 0 &&
             isset( $prelude[$flag] ) && isset( $body[1] ) && $this->helperName( $body[1] ) === $flag && $this->isConstant( $this->assignedValue( $body[1] ), true ) )
        {
            $consumed[] = $flag;
            return array( $children, $flag, array_slice( $body, 2 ) );
        }
        return array( null, null, $body );
    }

    /**
     * {section}: the forward converter writes a for over expsectionloop(loop, offset, max, reverse) with the section's
     * variables in its namespace (Ns__key, Ns__item, Ns__index, Ns__number, Ns__sequence).
     */
    private function sectionNode( \Twig\Node\ForNode $node, \Twig\Node\Node $source, $key, $item, array $body, array $prelude, array $consumed, $else, $skip, $stop )
    {
        if ( $key === null || !preg_match( '/^(?:(.+)__)?key$/', $key, $m ) )
            throw new UnexpectedValueException( 'a for over expsectionloop that is not the one the converter writes for {section}' );
        $prefix = isset( $m[1] ) ? $m[1] : '';
        $variable = function ( $name ) use ( $prefix ) { return $prefix !== '' ? $prefix . '__' . $name : $name; };
        $path = $prefix !== '' ? explode( '__', $prefix ) : array();
        $sectionName = null;
        $wrappers = array();
        if ( $path )
        {
            $depth = count( $this->namespace );
            if ( count( $path ) <= $depth || array_slice( $path, 0, $depth ) !== $this->namespace )
                throw new UnexpectedValueException( "the section namespace $prefix is not below the namespace it is in" );
            $relative = array_slice( $path, $depth );
            $sectionName = array_pop( $relative );
            $wrappers = $relative;
        }
        if ( count( $body ) < 2 || $this->assignedName( $body[0] ) !== $variable( 'index' ) || !$this->isLoopProperty( $this->assignedValue( $body[0] ), 'index0' ) ||
             $this->assignedName( $body[1] ) !== $variable( 'number' ) || !$this->isLoopProperty( $this->assignedValue( $body[1] ), 'index' ) )
            throw new UnexpectedValueException( 'a for over expsectionloop without the section variables index and number' );
        $body = array_slice( $body, 2 );

        list( $loop, $offset, $max, $reverse ) = $this->arguments( $source );
        $head = 'section' . ( $sectionName !== null ? ' name=' . $sectionName : '' ) . ' loop=' . $this->expr( $loop );
        if ( !$this->isConstant( $offset, 0 ) )
            $head .= ' offset=' . $this->expr( $offset );
        if ( !$this->isNull( $max ) )
            $head .= ' max=' . $this->expr( $max );
        if ( !$this->isConstant( $reverse, false ) )
            $head .= ' reverse=' . $this->expr( $reverse );
        if ( $body && $this->assignedName( $body[0] ) === $variable( 'sequence' ) )
        {
            $value = $this->assignedValue( $body[0] );
            if ( $this->function( $value, 'expsequence' ) && ( $args = $this->arguments( $value ) ) && $args[0] instanceof \Twig\Node\Expression\NameExpression &&
                 isset( $prelude[$args[0]->getAttribute( 'name' )] ) )
            {
                $consumed[] = $args[0]->getAttribute( 'name' );
                $head .= ' sequence=' . $this->expr( $this->assignedValue( $prelude[$args[0]->getAttribute( 'name' )] ) );
                array_shift( $body );
            }
        }
        if ( $item !== $variable( 'item' ) )
            $head .= ' var=' . $item;
        $this->addNamespace( $prefix );

        // section-exclude / section-include: __excludeN = false, {% if match %}{% set __excludeN = true|false %}..., {% if not __excludeN %}body
        $filters = array();
        if ( $body && ( $exclude = $this->helperName( $body[0] ) ) !== null && strncmp( $exclude, '__exclude', 9 ) === 0 )
        {
            $k = 1;
            while ( isset( $body[$k] ) && $body[$k] instanceof \Twig\Node\IfNode && count( $body[$k]->getNode( 'tests' ) ) === 2 && !$body[$k]->hasNode( 'else' ) )
            {
                $condition = $this->condition( $body[$k]->getNode( 'tests' )->getNode( '0' ) );
                $inner = $this->statements( $body[$k]->getNode( 'tests' )->getNode( '1' ) );
                if ( count( $inner ) === 1 && $this->helperName( $inner[0] ) === $exclude )
                {
                    $filters[] = ( $this->isConstant( $this->assignedValue( $inner[0] ), true ) ? 'section-exclude' : 'section-include' ) . ' match=' . $this->expr( $condition );
                    ++$k;
                    continue;
                }
                if ( $this->isNotName( $condition, $exclude ) && !isset( $body[$k + 1] ) )
                {
                    $body = $inner;
                    $k = -1;
                }
                break;
            }
            if ( $k !== -1 )
                throw new UnexpectedValueException( 'a {section} filter the converter does not read' );
        }
        list( $delimiter, $delimFlag, $body ) = $this->delimiterOf( $body, $prelude, $consumed );

        $saved = $this->namespace;
        if ( $prefix !== '' )
            $this->namespace = explode( '__', $prefix );
        $this->loops[] = array( 'item' => $item, 'key' => $key, 'kind' => 'section', 'namespace' => $prefix, 'skip' => $skip, 'stop' => $stop, 'delim' => $delimFlag );
        $this->pushScope();
        $items = array( array( 'F', $head ) );
        foreach ( $filters as $filter )
            $items[] = array( 'F', $filter );
        if ( $delimiter !== null )
            $items = array_merge( $items, array( array( 'F', 'delimiter' ) ), $this->convertNodes( $delimiter ), array( array( 'F', '/delimiter' ) ) );
        $items = array_merge( $items, $this->convertNodes( $body ) );
        $this->popScope();
        array_pop( $this->loops );
        if ( $else !== null )
        {
            $this->pushScope();
            $items = array_merge( $items, array( array( 'F', 'section-else' ) ), $this->convertNodes( $else ) );
            $this->popScope();
        }
        $this->namespace = $saved;
        $items[] = array( 'F', '/section' );
        foreach ( array_reverse( $wrappers ) as $wrapper )
            $items = array_merge( array( array( 'F', 'section name=' . $wrapper ) ), $items, array( array( 'F', '/section' ) ) );
        return array( $items, $consumed );
    }

    /**
     * {section show=...} with a section-else and a loop: the forward converter writes {% if show %}<loop>{% else %}...
     * Only the if itself is a plain if: nothing to recover here.
     */
    private function sectionShowNode( \Twig\Node\IfNode $node, array $prelude )
    {
        return null;
    }

    /**
     * {switch}: {% set __switchN = match %} and an if chain on it (== and in).
     */
    private function switchNode( \Twig\Node\IfNode $node, array $prelude )
    {
        $tests = $node->getNode( 'tests' );
        $first = $this->condition( $tests->getNode( '0' ) );
        $flag = null;
        if ( $first instanceof \Twig\Node\Expression\Binary\EqualBinary || $first instanceof \Twig\Node\Expression\Binary\InBinary )
        {
            $left = $first->getNode( 'left' );
            if ( $left instanceof \Twig\Node\Expression\NameExpression && strncmp( $left->getAttribute( 'name' ), '__switch', 8 ) === 0 && isset( $prelude[$left->getAttribute( 'name' )] ) )
                $flag = $left->getAttribute( 'name' );
        }
        if ( $flag === null )
            return null;
        $items = array( array( 'F', 'switch match=' . $this->expr( $this->assignedValue( $prelude[$flag] ) ) ) );
        for ( $k = 0; $k < count( $tests ); $k += 2 )
        {
            $condition = $this->condition( $tests->getNode( (string)$k ) );
            if ( !( $condition instanceof \Twig\Node\Expression\Binary\AbstractBinary ) || !$this->isName( $condition->getNode( 'left' ), $flag ) )
                return null;
            $right = $condition->getNode( 'right' );
            if ( $condition instanceof \Twig\Node\Expression\Binary\EqualBinary )
                $head = 'case match=' . $this->expr( $right );
            else if ( $condition instanceof \Twig\Node\Expression\Binary\InBinary )
            {
                if ( $this->function( $right, 'expcolumn' ) && count( $args = $this->arguments( $right ) ) === 2 )
                    $head = 'case in=' . $this->expr( $args[0] ) . ' key=' . $this->expr( $args[1] );
                else
                    $head = 'case in=' . $this->expr( $right );
            }
            else
                return null;
            $this->pushScope();
            $children = $tests->hasNode( (string)( $k + 1 ) ) ? $this->convertNodes( $this->statements( $tests->getNode( (string)( $k + 1 ) ) ) ) : array();
            $this->popScope();
            $items = array_merge( $items, array( array( 'F', $head ) ), $children, array( array( 'F', '/case' ) ) );
        }
        if ( $node->hasNode( 'else' ) )
        {
            $this->pushScope();
            $items = array_merge( $items, array( array( 'F', 'case' ) ), $this->convertNodes( $this->statements( $node->getNode( 'else' ) ) ), array( array( 'F', '/case' ) ) );
            $this->popScope();
        }
        $items[] = array( 'F', '/switch' );
        return array( $items, array( $flag ) );
    }

    /**
     * {% expwhile %}: {while}, or {do} ({% set __doN = true %} before it, {% set __doN = condition %} last in the round).
     */
    private function whileNode( expTwigTagNode $node, array $prelude )
    {
        $condition = $node->getNode( 'condition' );
        $consumed = array();
        $stop = null;
        // not __stopN and (...)
        $condition = $this->condition( $condition );
        if ( $condition instanceof \Twig\Node\Expression\Binary\AndBinary && $condition->getNode( 'left' ) instanceof \Twig\Node\Expression\Unary\NotUnary &&
             ( $inner = $this->condition( $condition->getNode( 'left' )->getNode( 'node' ) ) ) instanceof \Twig\Node\Expression\NameExpression &&
             strncmp( $inner->getAttribute( 'name' ), '__stop', 6 ) === 0 && isset( $prelude[$inner->getAttribute( 'name' )] ) )
        {
            $stop = $inner->getAttribute( 'name' );
            $consumed[] = $stop;
            $condition = $this->condition( $condition->getNode( 'right' ) );
        }
        $body = $this->statements( $node->getNode( 'body' ) );
        $skip = null;
        if ( $body && ( $helper = $this->helperName( $body[0] ) ) !== null && strncmp( $helper, '__skip', 6 ) === 0 )
        {
            $skip = $helper;
            array_shift( $body );
        }
        $isDo = false;
        if ( $condition instanceof \Twig\Node\Expression\NameExpression && strncmp( $flag = $condition->getAttribute( 'name' ), '__do', 4 ) === 0 &&
             isset( $prelude[$flag] ) && $body && $this->helperName( end( $body ) ) === $flag )
        {
            $isDo = true;
            $consumed[] = $flag;
            $while = $this->expr( $this->assignedValue( array_pop( $body ) ) );
        }
        $this->loops[] = array( 'item' => null, 'key' => null, 'kind' => 'while', 'skip' => $skip, 'stop' => $stop, 'delim' => null, 'namespace' => '' );
        $this->pushScope();
        $bodyItems = $this->convertNodes( $body );
        $this->popScope();
        array_pop( $this->loops );
        if ( $isDo )
            return array( array_merge( array( array( 'F', 'do' ) ), $bodyItems, array( array( 'F', '/do while ' . $while ) ) ), $consumed );
        return array( array_merge( array( array( 'F', 'while ' . $this->expr( $condition ) ) ), $bodyItems, array( array( 'F', '/while' ) ) ), $consumed );
    }

    // ----------------------------------------------------------------------------------------
    // Expressions

    /**
     * @return string the template expression of a Twig expression
     */
    public function expr( \Twig\Node\Node $e )
    {
        if ( $e instanceof \Twig\Node\Expression\ConstantExpression )
            return $this->constant( $e->getAttribute( 'value' ) );
        if ( $e instanceof \Twig\Node\Expression\NameExpression )
        {
            $name = $e->getAttribute( 'name' );
            if ( $name === 'loop' || $name === '_self' || $name === '_charset' )
                throw new UnexpectedValueException( "the variable $name has no template form" );
            return $this->variableName( $name );
        }
        if ( $e instanceof \Twig\Node\Expression\GetAttrExpression )
            return $this->attribute( $e );
        if ( $e instanceof \Twig\Node\Expression\ArrayExpression )
            return $this->arrayLiteral( $e );
        if ( $e instanceof \Twig\Node\Expression\FilterExpression )
            return $this->filter( $e );
        if ( $e instanceof \Twig\Node\Expression\FunctionExpression )
            return $this->call( $e->getAttribute( 'name' ), $this->arguments( $e ) );
        if ( $e instanceof \Twig\Node\Expression\Test\TrueTest )
            return $this->expr( $e->getNode( 'node' ) );
        if ( $e instanceof \Twig\Node\Expression\TestExpression )
            return $this->test( $e );
        if ( $e instanceof \Twig\Node\Expression\Unary\NotUnary )
        {
            $inner = $this->condition( $e->getNode( 'node' ) );
            if ( $inner instanceof \Twig\Node\Expression\Test\NullTest )
                return 'is_set(' . $this->expr( $inner->getNode( 'node' ) ) . ')';
            return 'not(' . $this->expr( $inner ) . ')';
        }
        if ( $e instanceof \Twig\Node\Expression\Unary\NegUnary )
        {
            $inner = $e->getNode( 'node' );
            if ( $inner instanceof \Twig\Node\Expression\ConstantExpression && is_numeric( $inner->getAttribute( 'value' ) ) )
                return '-' . $this->constant( $inner->getAttribute( 'value' ) );
            return 'sub(0, ' . $this->expr( $inner ) . ')';
        }
        if ( $e instanceof \Twig\Node\Expression\Unary\PosUnary )
            return $this->expr( $e->getNode( 'node' ) );
        if ( $e instanceof \Twig\Node\Expression\Binary\NullCoalesceBinary || $e instanceof \Twig\Node\Expression\NullCoalesceExpression )
        {
            if ( $e->hasNode( 'left' ) )
                return 'first_set(' . $this->expr( $e->getNode( 'left' ) ) . ', ' . $this->expr( $e->getNode( 'right' ) ) . ')';
            throw new UnexpectedValueException( 'the ?? operator in this form has no template form' );
        }
        if ( $e instanceof \Twig\Node\Expression\Binary\InBinary )
            return $this->expr( $e->getNode( 'right' ) ) . '|contains(' . $this->expr( $e->getNode( 'left' ) ) . ')';
        if ( $e instanceof \Twig\Node\Expression\Binary\NotInBinary )
            return 'not(' . $this->expr( $e->getNode( 'right' ) ) . '|contains(' . $this->expr( $e->getNode( 'left' ) ) . '))';
        if ( $e instanceof \Twig\Node\Expression\Binary\AbstractBinary )
        {
            $class = ( new ReflectionClass( $e ) )->getShortName();
            if ( !isset( self::BINARY[$class] ) )
                throw new UnexpectedValueException( "the Twig operator $class has no template form" );
            return self::BINARY[$class] . '(' . $this->expr( $e->getNode( 'left' ) ) . ', ' . $this->expr( $e->getNode( 'right' ) ) . ')';
        }
        if ( $e instanceof \Twig\Node\Expression\ConditionalExpression )
            return 'cond(' . $this->expr( $e->getNode( 'expr1' ) ) . ', ' . $this->expr( $e->getNode( 'expr2' ) ) . ', ' . $this->expr( $e->getNode( 'expr3' ) ) . ')';
        if ( $e instanceof \Twig\Node\Expression\Ternary\ConditionalTernary )
        {
            $test = $e->getNode( 'test' );
            if ( $test instanceof \Twig\Node\Expression\Test\TrueTest )
                $test = $test->getNode( 'node' );
            return 'cond(' . $this->expr( $test ) . ', ' . $this->expr( $e->getNode( 'left' ) ) . ', ' . $this->expr( $e->getNode( 'right' ) ) . ')';
        }
        if ( $e instanceof \Twig\Node\Expression\Test\TrueTest )
            return $this->expr( $e->getNode( 'node' ) );
        throw new UnexpectedValueException( 'the Twig expression ' . ( new ReflectionClass( $e ) )->getShortName() . ' has no template form' );
    }

    private function constant( $value )
    {
        if ( $value === null )
            return 'null()';
        if ( $value === true )
            return 'true()';
        if ( $value === false )
            return 'false()';
        if ( is_int( $value ) )
            return (string)$value;
        if ( is_float( $value ) )
            return var_export( $value, true );
        return $this->quote( (string)$value );
    }

    private function quote( $string )
    {
        return "'" . str_replace( array( '\\', "'" ), array( '\\\\', "\\'" ), $string ) . "'";
    }

    private function attribute( \Twig\Node\Expression\GetAttrExpression $e )
    {
        $base = $e->getNode( 'node' );
        $attribute = $e->getNode( 'attribute' );
        if ( $e->hasNode( 'arguments' ) && count( $e->getNode( 'arguments' ) ) )
            throw new UnexpectedValueException( 'a method call has no template form' );
        if ( $this->isName( $base, 'loop' ) && $attribute instanceof \Twig\Node\Expression\ConstantExpression )
            return $this->loopProperty( (string)$attribute->getAttribute( 'value' ) );
        if ( $this->isName( $base, '_context' ) && $attribute instanceof \Twig\Node\Expression\ConstantExpression )
            return $this->variableName( (string)$attribute->getAttribute( 'value' ) );
        $code = $this->expr( $base );
        if ( $attribute instanceof \Twig\Node\Expression\ConstantExpression )
        {
            $key = $attribute->getAttribute( 'value' );
            if ( is_int( $key ) || ctype_digit( (string)$key ) )
                return $code . '[' . $key . ']';
            if ( preg_match( '/^[a-zA-Z_][a-zA-Z0-9_]*$/', (string)$key ) )
                return $code . '.' . $key;
        }
        return $code . '[' . $this->expr( $attribute ) . ']';
    }

    /**
     * loop.* of the innermost loop: a counter for a foreach, the section's variables for a section.
     */
    private function loopProperty( $property )
    {
        $i = count( $this->loops ) - 1;
        while ( $i >= 0 && $this->loops[$i]['kind'] === 'while' )
            --$i;
        if ( $i < 0 )
            throw new UnexpectedValueException( "loop.$property outside of a loop" );
        $loop = $this->loops[$i];
        if ( $loop['kind'] === 'section' )
        {
            $prefix = $loop['namespace'] !== '' ? $loop['namespace'] . '__' : '';
            switch ( $property )
            {
                case 'index0':
                    return $this->variableName( $prefix . 'index' );
                case 'index':
                    return $this->variableName( $prefix . 'number' );
                case 'first':
                    return 'eq(' . $this->variableName( $prefix . 'index' ) . ', 0)';
            }
            throw new UnexpectedValueException( "loop.$property of a section has no template form" );
        }
        $counter = '$' . $loop['counter'];
        switch ( $property )
        {
            case 'index0':
                $this->loops[$i]['used'] = true;
                return $counter;
            case 'index':
                $this->loops[$i]['used'] = true;
                return $counter . '|inc';
            case 'first':
                $this->loops[$i]['used'] = true;
                return 'eq(' . $counter . ', 0)';
            case 'last':
                $this->loops[$i]['used'] = true;
                $this->loops[$i]['usedSource'] = true;
                return 'eq(' . $counter . '|inc, count(' . $loop['sourceRef'] . '))';
            case 'length':
                $this->loops[$i]['usedSource'] = true;
                return 'count(' . $loop['sourceRef'] . ')';
        }
        throw new UnexpectedValueException( "loop.$property has no template form" );
    }

    private function arrayLiteral( \Twig\Node\Expression\ArrayExpression $e )
    {
        $pairs = $e->getKeyValuePairs();
        $list = true;
        foreach ( $pairs as $index => $pair )
        {
            if ( !$this->isConstant( $pair['key'], $index ) )
            {
                $list = false;
                break;
            }
        }
        if ( $list )
            return 'array(' . implode( ', ', array_map( function ( $pair ) { return $this->expr( $pair['value'] ); }, $pairs ) ) . ')';
        $parts = array();
        foreach ( $pairs as $pair )
        {
            $parts[] = $this->expr( $pair['key'] );
            $parts[] = $this->expr( $pair['value'] );
        }
        return 'hash(' . implode( ', ', $parts ) . ')';
    }

    private function filter( \Twig\Node\Expression\FilterExpression $e )
    {
        $name = $e->getAttribute( 'name' );
        $input = $e->getNode( 'node' );
        if ( $e instanceof \Twig\Node\Expression\Filter\DefaultFilter || $name === 'default' )
            throw new UnexpectedValueException( "Twig's default filter has no exact template operator" );
        if ( $name === 'raw' )
            return $this->expr( $input );
        $args = $this->arguments( $e );
        if ( $name === 'escape' || $name === 'e' )
        {
            if ( $args && !$this->isConstant( $args[0], 'html' ) )
                throw new UnexpectedValueException( 'escape other than html has no template form' );
            return $this->pipe( $this->expr( $input ) ) . '|wash';
        }
        return $this->pipe( $this->expr( $input ) ) . '|' . $this->operatorName( $name, true ) . $this->argumentList( $args );
    }

    private function pipe( $code )
    {
        return $code;
    }

    private function argumentList( array $args )
    {
        if ( !$args )
            return '';
        return '(' . implode( ', ', array_map( function ( $arg ) { return $this->function( $arg, 'expvoid' ) ? '' : $this->expr( $arg ); }, $args ) ) . ')';
    }

    /**
     * A function call: a helper of lib/twig, an operator without input.
     */
    private function call( $name, array $args )
    {
        switch ( $name )
        {
            case 'expvoid':
                return '';
            case 'expvar':
                $variable = isset( $args[0] ) && $args[0] instanceof \Twig\Node\Expression\ConstantExpression ? (string)$args[0]->getAttribute( 'value' ) : null;
                $namespace = isset( $args[1] ) && $args[1] instanceof \Twig\Node\Expression\ConstantExpression ? (string)$args[1]->getAttribute( 'value' ) : '';
                if ( $variable === null )
                    throw new UnexpectedValueException( 'expvar() without a constant name' );
                return '$#' . ( $namespace !== '' ? $namespace . ':' : '' ) . $variable;
            case 'expattr':
                if ( count( $args ) !== 2 )
                    throw new UnexpectedValueException( 'expattr() needs two arguments' );
                $key = $args[1];
                if ( $key instanceof \Twig\Node\Expression\ConstantExpression && preg_match( '/^[a-zA-Z_][a-zA-Z0-9_]*$/', (string)$key->getAttribute( 'value' ) ) )
                    return $this->expr( $args[0] ) . '.' . $key->getAttribute( 'value' );
                return $this->expr( $args[0] ) . '[' . $this->expr( $key ) . ']';
            case 'expsequence':
            case 'expslice':
            case 'expsectionloop':
            case 'exprange':
            case 'expcolumn':
                throw new UnexpectedValueException( "$name() outside of the loop the converter writes it for" );
        }
        if ( class_exists( 'expTwigExtension' ) && expTwigExtension::isBridgedFunction( $name ) )
            throw new UnexpectedValueException( "the template function $name used as a value" );
        return $this->operatorName( $name, false ) . '(' . implode( ', ', array_map( function ( $arg ) { return $this->function( $arg, 'expvoid' ) ? '' : $this->expr( $arg ); }, $args ) ) . ')';
    }

    /**
     * The template operator of a filter or function name: exp_<name> is <name>; a Twig filter with an operator that
     * does the same is that operator; any other name must be an operator of the template system.
     */
    private function operatorName( $name, $isFilter )
    {
        if ( strncmp( $name, 'exp_', 4 ) === 0 && strlen( $name ) > 4 && !isset( $this->tpl->Operators[$name] ) )
            return substr( $name, 4 );
        if ( isset( $this->tpl->Operators[$name] ) )
            return $name;
        if ( $isFilter && isset( self::FILTERS[$name] ) )
            return self::FILTERS[$name];
        throw new UnexpectedValueException( "Twig's " . ( $isFilter ? 'filter' : 'function' ) . " $name has no template operator" );
    }

    private function test( \Twig\Node\Expression\TestExpression $e )
    {
        $name = $e->getAttribute( 'name' );
        $node = $e->getNode( 'node' );
        switch ( $name )
        {
            case 'defined':
                return 'is_set(' . $this->expr( $node ) . ')';
            case 'null':
            case 'none':
                return 'not(is_set(' . $this->expr( $node ) . '))';
            case 'even':
                return 'eq(mod(' . $this->expr( $node ) . ', 2), 0)';
            case 'odd':
                return 'eq(mod(' . $this->expr( $node ) . ', 2), 1)';
            case 'iterable':
                return 'is_array(' . $this->expr( $node ) . ')';
        }
        throw new UnexpectedValueException( "Twig's test $name has no template form" );
    }

    /**
     * The template variable of a Twig variable name: Ns__name (a variable of the namespace Ns, as the forward converter
     * writes it) is $Ns:name.
     */
    private function variableName( $name )
    {
        list( $namespace, $base ) = $this->splitNamespace( $name );
        return '$' . ( $namespace !== '' ? str_replace( '__', ':', $namespace ) . ':' : '' ) . $base;
    }

    /**
     * @return array( namespace prefix (Ns__Sub) or '', name )
     */
    private function splitNamespace( $name )
    {
        foreach ( $this->namespaces as $namespace )
        {
            if ( strncmp( $name, $namespace . '__', strlen( $namespace ) + 2 ) === 0 && strlen( $name ) > strlen( $namespace ) + 2 )
                return array( $namespace, substr( $name, strlen( $namespace ) + 2 ) );
        }
        $last = strrpos( $name, '__' );
        if ( $last !== false && $last > 0 && preg_match( '/^[A-Za-z0-9]+(?:_[A-Za-z0-9]+)*(?:__[A-Za-z0-9]+(?:_[A-Za-z0-9]+)*)*$/', substr( $name, 0, $last ) ) &&
             preg_match( '/^[A-Za-z][A-Za-z0-9_]*$/', substr( $name, $last + 2 ) ) )
            return array( substr( $name, 0, $last ), substr( $name, $last + 2 ) );
        return array( '', $name );
    }

    // ----------------------------------------------------------------------------------------
    // Output

    private function emit( array $items )
    {
        $merged = array();
        foreach ( $items as $item )
        {
            $last = count( $merged ) - 1;
            if ( $item[0] === 'T' && $last >= 0 && $merged[$last][0] === 'T' )
            {
                $merged[$last][1] .= $item[1];
                continue;
            }
            $merged[] = $item;
        }
        $out = '';
        $count = count( $merged );
        foreach ( $merged as $index => $item )
        {
            switch ( $item[0] )
            {
                case 'F':
                case 'V':
                    $out .= '{' . str_replace( '}', '\\}', $item[1] ) . '}';
                    break;
                case 'C':
                    $out .= '{*' . str_replace( '*}', '* }', $item[1] ) . '*}';
                    break;
                case 'T':
                    $text = $item[1];
                    $previousTag = $index > 0 && $merged[$index - 1][0] === 'F';
                    $nextTag = $index + 1 < $count && $merged[$index + 1][0] === 'F';
                    $before = $previousTag && isset( $text[0] ) && $text[0] === "\n" ? "\n" : '';
                    $after = $nextTag && substr( $text, -1 ) === "\n" ? "\n" : '';
                    if ( strpos( $text, '{' ) !== false )
                    {
                        if ( strpos( $text, '{/literal}' ) !== false )
                            $text = str_replace( '{', '{ldelim}', $text );
                        else
                            $text = '{literal}' . $text . '{/literal}';
                    }
                    $out .= $before . $text . $after;
                    break;
            }
        }
        return $out;
    }

    private function warn( $line, $message )
    {
        $this->warnings[] = ( $line ? "line $line: " : '' ) . $message;
    }
}
