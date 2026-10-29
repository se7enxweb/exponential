<?php
/**
 * Converts Exponential templates (.tpl) to Twig templates (.twig) that render the same output through lib/twig.
 *
 * The .tpl is parsed by the template system's own parser (eZTemplateMultiPassParser), so the converter sees exactly
 * the tree the template system would run, with its whitespace rules already applied. Every node is written as Twig:
 *
 *  - text as it is ("{" before "{", "%" or "#" becomes {{ '{' }} so Twig does not read a tag);
 *  - {$expr} as {{ expr }}: variables keep their names, attribute lookups become a.b or a[k], and every template
 *    operator stays an operator: input|op(a, b) with an input, op(a, b) without (exp_op where Twig has a filter or
 *    function of that name);
 *  - the template functions as Twig code ({% if %}, {% for %}, {% set %}) or as a tag of lib/twig
 *    (expinclude, expset, expsetblock, expcacheblock, exprunonce, expwhile, expundefall, block functions);
 *  - {break}, {continue} and {skip}, which Twig does not have, as flags: the rest of the round sits in
 *    {% if not __skipN %}, and a {break} also stops the rounds after it (__stopN);
 *  - {while} and {do} as {% expwhile %}, a loop of at most [TwigSettings] WhileLoopLimit rounds.
 *
 * Twig removes the newline right after a {% %} tag: the converter writes one after each such tag, so the text after
 * it keeps the newlines the template system would have printed.
 *
 * A Twig {% for %} forgets the variables first set inside it when it ends, where a .tpl keeps them: a variable set
 * inside a loop is declared at the top of the template ({% set x = x ?? null %}).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigConverter
{
    /** Twig's filters (3.x); an operator of the same name is called with the exp_ prefix as a filter. */
    const TWIG_FILTERS = array(
        'abs', 'batch', 'capitalize', 'column', 'convert_encoding', 'country_name', 'currency_name', 'currency_symbol',
        'data_uri', 'date', 'date_modify', 'default', 'e', 'escape', 'filter', 'find', 'first', 'format',
        'format_currency', 'format_date', 'format_datetime', 'format_number', 'format_time', 'html_to_markdown',
        'inky_to_html', 'inline_css', 'invoke', 'join', 'json_encode', 'keys', 'language_name', 'last', 'length',
        'locale_name', 'lower', 'map', 'markdown_to_html', 'merge', 'nl2br', 'number_format', 'plural', 'raw',
        'reduce', 'replace', 'reverse', 'round', 'shuffle', 'singular', 'slice', 'slug', 'sort', 'spaceless',
        'split', 'striptags', 'timezone_name', 'title', 'trim', 'u', 'upper', 'url_encode',
    );

    /** Twig's functions (3.x); an operator of the same name is called with the exp_ prefix as a function. */
    const TWIG_FUNCTIONS = array(
        'attribute', 'block', 'constant', 'country_names', 'country_timezones', 'currency_names', 'cycle', 'date',
        'dump', 'enum', 'enum_cases', 'html_classes', 'html_cva', 'include', 'language_names', 'locale_names', 'max',
        'min', 'parent', 'random', 'range', 'script_names', 'source', 'template_from_string', 'timezone_names',
    );

    /** Words Twig's lexer reads as operators or literals: never a function, filter or variable name of their own. */
    const TWIG_WORDS = array(
        'and', 'or', 'not', 'in', 'is', 'matches', 'xor', 'b-and', 'b-or', 'b-xor', 'starts', 'ends', 'has', 'same',
        'true', 'false', 'null', 'none', 'if', 'else', 'for', 'with', 'only', 'as', 'divisible', 'defined', 'empty',
        'iterable', 'even', 'odd', '_self', '_context', '_charset', 'loop',
    );

    /** The helper functions of lib/twig the converter writes (expTwigExtension::getFunctions()). */
    const HELPERS = array( 'expvar', 'expattr', 'expvoid', 'expsequence', 'expslice', 'expsectionloop', 'exprange', 'expcolumn', 'expi18nargs' );

    /** Template functions the converter writes as Twig code of its own. */
    const STRUCTURAL = array(
        'if', 'elseif', 'else', 'foreach', 'for', 'while', 'do', 'def', 'undef', 'set', 'let', 'default',
        'section', 'section-else', 'section-exclude', 'section-include', 'delimiter', 'switch', 'case',
        'set-block', 'append-block', 'cache-block', 'run-once', 'include', 'literal', 'ldelim', 'rdelim',
        'break', 'continue', 'skip',
    );

    /** Template functions that loop: a {break} inside belongs to them. */
    const LOOPS = array( 'foreach', 'for', 'while', 'do' );

    /** @var eZTemplate */
    private $tpl;

    /** @var string[] */
    private $warnings = array();

    /** @var string */
    private $file = '';

    /** @var int */
    private $counter = 0;

    /** @var string[] current namespace relative to the root namespace (named {section}s) */
    private $namespace = array();

    /** @var array[] open loops: item, skip (flag of continue/skip), stop (flag of break), delimiter (flag) */
    private $loops = array();

    /** @var array variable names set inside a loop, declared at the top */
    private $hoist = array();

    public function __construct( eZTemplate $tpl )
    {
        $this->tpl = $tpl;
    }

    /**
     * @return string[] what could not be converted exactly, in the last convert() call
     */
    public function warnings()
    {
        return $this->warnings;
    }

    public function convertFile( $file )
    {
        return $this->convert( file_get_contents( $file ), $file );
    }

    /**
     * @param string $text template text
     * @param string $file name used in messages
     * @return string the .twig text
     */
    public function convert( $text, $file = 'template' )
    {
        $this->warnings = array();
        $this->file = $file;
        $this->counter = 0;
        $this->namespace = array();
        $this->loops = array();
        $this->hoist = array();

        $parser = eZTemplateMultiPassParser::instance();
        $root = array( eZTemplate::NODE_ROOT, false );
        $resourceData = array( 'resource' => 'file', 'template-name' => $file, 'template-filename' => $file, 'text' => $text );
        $parser->parse( $this->tpl, $text, $root, '', $resourceData );

        $out = $this->nodes( is_array( $root[1] ) ? $root[1] : array() );
        $head = '';
        foreach ( array_keys( $this->hoist ) as $name )
            $head .= $this->tag( 'set ' . $name . ' = ' . $name . ' ?? null' );
        return $head . $out;
    }

    // ----------------------------------------------------------------------------------------
    // Nodes

    /**
     * A list of nodes. Inside a loop with {continue}, {skip} or {break}, the nodes after one that may jump sit in
     * {% if not __skipN %}.
     */
    private function nodes( array $nodes )
    {
        $out = '';
        $loop = end( $this->loops );
        $count = count( $nodes );
        for ( $i = 0; $i < $count; ++$i )
        {
            $out .= $this->node( $nodes[$i] );
            if ( $loop && !empty( $loop['skip'] ) && $i + 1 < $count && $this->hasJump( array( $nodes[$i] ) ) )
            {
                $rest = $this->nodes( array_slice( $nodes, $i + 1 ) );
                if ( $rest !== '' )
                    $out .= $this->tag( 'if not ' . $loop['skip'] ) . $rest . $this->tag( 'endif' );
                break;
            }
        }
        return $out;
    }

    private function node( array $node )
    {
        switch ( $node[0] )
        {
            case eZTemplate::NODE_TEXT:
                return $this->text( $node[2] );
            case eZTemplate::NODE_VARIABLE:
                return '{{ ' . $this->expr( $node[2] ) . ' }}';
            case eZTemplate::NODE_FUNCTION:
                return $this->func( $node );
        }
        $this->warn( 'unknown node type ' . $node[0] );
        return '';
    }

    /**
     * Text as it is; "{" that would start a Twig tag, print or comment, or that ends the text (the next node may
     * begin with "{"), is printed by {{ '{' }}.
     */
    private function text( $text )
    {
        return preg_replace( '/\{(?=[{%#]|$)/', "{{ '{' }}", (string)$text );
    }

    /**
     * A {% %} tag: Twig drops the newline after it, so one is written for it.
     */
    private function tag( $code )
    {
        return '{% ' . $code . " %}\n";
    }

    /**
     * Whether nodes hold a {break}, {continue} or {skip} of the current loop (not of a loop inside them).
     */
    private function hasJump( array $nodes, $breakOnly = false )
    {
        foreach ( $nodes as $node )
        {
            if ( $node[0] != eZTemplate::NODE_FUNCTION )
                continue;
            $name = $node[2];
            if ( $name === 'break' || ( !$breakOnly && ( $name === 'continue' || $name === 'skip' ) ) )
                return true;
            if ( in_array( $name, self::LOOPS, true ) || ( $name === 'section' && isset( $node[3]['loop'] ) ) )
                continue;
            if ( is_array( $node[1] ) && $this->hasJump( $node[1], $breakOnly ) )
                return true;
        }
        return false;
    }

    private function hasSkip( array $nodes )
    {
        foreach ( $nodes as $node )
        {
            if ( $node[0] != eZTemplate::NODE_FUNCTION )
                continue;
            if ( $node[2] === 'skip' )
                return true;
            if ( !in_array( $node[2], array( 'foreach', 'for', 'section', 'while', 'do' ), true ) && is_array( $node[1] ) && $this->hasSkip( $node[1] ) )
                return true;
        }
        return false;
    }

    private function func( array $node )
    {
        $name = $node[2];
        $children = is_array( $node[1] ) ? $node[1] : array();
        $params = isset( $node[3] ) && is_array( $node[3] ) ? $node[3] : array();

        switch ( $name )
        {
            case 'if':
                return $this->ifFunction( $params, $children );
            case 'def':
            case 'set':
                return $this->defFunction( $name, $params );
            case 'undef':
                return $this->undefFunction( $params );
            case 'let':
            case 'default':
                return $this->letFunction( $name, $params, $children );
            case 'foreach':
                return $this->foreachFunction( $params, $children );
            case 'for':
                return $this->forFunction( $params, $children );
            case 'while':
                return $this->whileFunction( $params, $children, false );
            case 'do':
                return $this->whileFunction( $params, $children, true );
            case 'section':
                return $this->sectionFunction( $params, $children );
            case 'switch':
                return $this->switchFunction( $params, $children );
            case 'set-block':
            case 'append-block':
                return $this->setBlockFunction( $name, $params, $children );
            case 'cache-block':
                return $this->tag( 'expcacheblock id=' . ( ++$this->counter ) . $this->namedParams( $params ) ) . $this->nodes( $children ) . $this->tag( 'endexpcacheblock' );
            case 'run-once':
                return $this->tag( 'exprunonce id=' . ( ++$this->counter ) ) . $this->nodes( $children ) . $this->tag( 'endexprunonce' );
            case 'include':
                return $this->tag( 'expinclude' . $this->namedParams( $params ) );
            case 'literal':
                return $this->nodes( $children );
            case 'ldelim':
                return "{{ '{' }}";
            case 'rdelim':
                return '}';
            case 'break':
            case 'continue':
            case 'skip':
                return $this->jump( $name );
            case 'elseif':
            case 'else':
            case 'delimiter':
            case 'section-else':
            case 'section-exclude':
            case 'section-include':
            case 'case':
                $this->warn( "{{$name}} outside of its block" );
                return '';
        }

        if ( !isset( $this->tpl->Functions[$name] ) )
        {
            $this->warn( "unknown function {{$name}}: it prints nothing, as in the .tpl" );
            return '';
        }
        if ( !preg_match( '/^[a-z][a-z0-9_]*$/i', $name ) || in_array( $name, self::TWIG_WORDS, true ) )
        {
            $this->warn( "function {{$name}}: the name is not a Twig name" );
            return '';
        }
        // A template function with children is a tag of the same name, any other one a function taking a hash.
        if ( is_array( $node[1] ) )
            return $this->tag( $name . $this->namedParams( $params ) ) . $this->nodes( $children ) . $this->tag( 'end' . $name );
        return '{{ ' . $name . '(' . $this->hash( $params ) . ') }}';
    }

    private function jump( $name )
    {
        $loop = end( $this->loops );
        if ( !$loop || empty( $loop['skip'] ) )
        {
            $this->warn( "{{$name}} outside of a loop" );
            return '';
        }
        $out = '';
        if ( $name === 'skip' && !empty( $loop['delimiter'] ) )
            $out .= $this->tag( 'set ' . $loop['delimiter'] . ' = false' );
        if ( $name === 'break' && !empty( $loop['stop'] ) )
            $out .= $this->tag( 'set ' . $loop['stop'] . ' = true' );
        return $out . $this->tag( 'set ' . $loop['skip'] . ' = true' );
    }

    /**
     * Opens a loop: the flags it needs. Returns array( code before the loop, code at the start of each round,
     * code at the end of each round ).
     */
    private function openLoop( $item, array $body )
    {
        $entry = array( 'item' => $item, 'skip' => null, 'stop' => null, 'delimiter' => null );
        $before = '';
        $start = '';
        $end = '';
        if ( $this->hasJump( $body ) )
        {
            $entry['skip'] = '__skip' . ( ++$this->counter );
            if ( $this->hasJump( $body, true ) )
            {
                $entry['stop'] = '__stop' . ( ++$this->counter );
                $before .= $this->tag( 'set ' . $entry['stop'] . ' = false' );
                $start .= $this->tag( 'if not ' . $entry['stop'] );
                $end = $this->tag( 'endif' );
            }
            $start .= $this->tag( 'set ' . $entry['skip'] . ' = false' );
        }
        $this->loops[] = $entry;
        return array( $before, $start, $end );
    }

    private function closeLoop()
    {
        array_pop( $this->loops );
    }

    /**
     * Splits the children of {if} into its branches at {elseif} and {else}.
     */
    private function ifFunction( array $params, array $children )
    {
        if ( !isset( $params['condition'] ) || !$params['condition'] )
        {
            $this->warn( '{if} without a condition (a parse error in the .tpl): it prints nothing, as in the .tpl' );
            return '';
        }
        $out = $this->tag( 'if ' . $this->condition( $params ) );
        $branch = array();
        foreach ( $children as $child )
        {
            if ( $child[0] == eZTemplate::NODE_FUNCTION && ( $child[2] === 'elseif' || $child[2] === 'else' ) )
            {
                $out .= $this->nodes( $branch );
                $branch = array();
                $out .= $child[2] === 'else' ? $this->tag( 'else' )
                    : $this->tag( 'elseif ' . $this->condition( isset( $child[3] ) && is_array( $child[3] ) ? $child[3] : array() ) );
                continue;
            }
            $branch[] = $child;
        }
        return $out . $this->nodes( $branch ) . $this->tag( 'endif' );
    }

    private function condition( array $params )
    {
        if ( !isset( $params['condition'] ) )
        {
            $this->warn( 'condition missing' );
            return 'false';
        }
        return $this->expr( $params['condition'] );
    }

    /**
     * {% set name = value %}; a name inside a loop is declared at the top of the template too.
     */
    private function setVariable( $name, $code )
    {
        if ( in_array( $name, self::TWIG_WORDS, true ) )
            return $this->tag( 'expset name=' . $this->quote( $name ) . ' value=' . $this->param( $code ) . ' scope=' . $this->quote( 'root' ) );
        // helper variables (__name) are the converters' own: nothing after the loop reads them
        if ( $this->loops && strncmp( $name, '__', 2 ) !== 0 )
            $this->hoist[$name] = true;
        return $this->tag( 'set ' . $name . ' = ' . $code );
    }

    private function defFunction( $name, array $params )
    {
        $scope = isset( $params['-scope'] ) ? $this->constant( $params['-scope'] ) : null;
        $namespace = isset( $params['-name'] ) ? $this->constant( $params['-name'] ) : null;
        $out = '';
        foreach ( $params as $variable => $value )
        {
            if ( $variable === '-scope' || $variable === '-name' )
                continue;
            $code = $this->expr( $value );
            if ( ( $scope !== null && $scope !== 'relative' ) || $namespace )
            {
                $out .= $this->tag( 'expset name=' . $this->quote( $variable ) . ' value=' . $this->param( $code ) .
                                    ( $scope ? ' scope=' . $this->quote( $scope ) : '' ) .
                                    ( $namespace ? ' namespace=' . $this->quote( $namespace ) : '' ) );
                continue;
            }
            $target = $name === 'def' ? array() : $this->namespace;
            $out .= $this->setVariable( $this->varName( $target, $variable ), $code );
        }
        return $out;
    }

    private function undefFunction( array $params )
    {
        $out = '';
        foreach ( $params as $variable => $value )
        {
            $name = $variable;
            if ( is_int( $variable ) || ( isset( $value[0][0] ) && $value[0][0] == eZTemplate::TYPE_VARIABLE ) )
            {
                if ( isset( $value[0][1][2] ) )
                    $name = $value[0][1][2];
            }
            if ( !is_string( $name ) || $name === '' )
                continue;
            $out .= $this->setVariable( $this->varName( array(), $name ), 'null' );
        }
        if ( !$params )
            $out .= $this->tag( 'expundefall' );
        return $out;
    }

    private function letFunction( $name, array $params, array $children )
    {
        $namespace = isset( $params['name'] ) ? $this->constant( $params['name'] ) : '';
        $target = $this->namespace;
        if ( $namespace !== '' && $namespace !== null )
            $target[] = $namespace;
        $set = '';
        $unset = '';
        foreach ( $params as $variable => $value )
        {
            if ( $variable === 'name' || $variable === '-name' )
                continue;
            $var = $this->varName( $target, $variable );
            if ( $name === 'default' )
                $set .= $this->tag( 'if ' . $this->readName( $var ) . ' is null' ) . $this->setVariable( $var, $this->expr( $value ) ) . $this->tag( 'endif' );
            else
                $set .= $this->setVariable( $var, $this->expr( $value ) );
            $unset .= $this->setVariable( $var, 'null' );
        }
        $saved = $this->namespace;
        $this->namespace = $target;
        $body = $this->nodes( $children );
        $this->namespace = $saved;
        return $set . $body . ( $name === 'let' ? $unset : '' );
    }

    /**
     * Splits a loop's children into the body, its {delimiter}, the section filters and the section-else part.
     */
    private function loopParts( array $children, &$delimiter, &$filters = null, &$else = null )
    {
        $body = array();
        $delimiter = null;
        $filters = array();
        $else = null;
        foreach ( $children as $child )
        {
            if ( $child[0] == eZTemplate::NODE_FUNCTION )
            {
                if ( $child[2] === 'delimiter' )
                {
                    $delimiter = is_array( $child[1] ) ? $child[1] : array();
                    continue;
                }
                if ( $child[2] === 'section-exclude' || $child[2] === 'section-include' )
                {
                    $filters[] = $child;
                    continue;
                }
                if ( $child[2] === 'section-else' )
                {
                    $else = array();
                    continue;
                }
            }
            if ( $else !== null )
                $else[] = $child;
            else
                $body[] = $child;
        }
        return $body;
    }

    /**
     * The delimiter at the start of a round: before every item but the first, and not after an item that ended
     * with {skip} (a flag, set false by the {skip}). $before gets the flag's initialization.
     */
    private function loopDelimiter( $delimiter, array $body, &$before )
    {
        $before = '';
        if ( $delimiter === null )
            return '';
        if ( !$this->hasSkip( $body ) )
            return $this->tag( 'if not loop.first' ) . $this->nodes( $delimiter ) . $this->tag( 'endif' );
        $flag = '__delim' . ( ++$this->counter );
        $this->loops[count( $this->loops ) - 1]['delimiter'] = $flag;
        $before = $this->tag( 'set ' . $flag . ' = false' );
        return $this->tag( 'if ' . $flag ) . $this->nodes( $delimiter ) . $this->tag( 'endif' ) . $this->tag( 'set ' . $flag . ' = true' );
    }

    private function foreachFunction( array $params, array $children )
    {
        $array = isset( $params['array'] ) ? $this->expr( $params['array'] ) : '[]';
        $item = isset( $params['item_var'] ) ? $this->varName( array(), $this->variableName( $params['item_var'] ) ) : 'item';
        $key = isset( $params['key_var'] ) ? $this->varName( array(), $this->variableName( $params['key_var'] ) ) : null;
        if ( isset( $params['offset'] ) || isset( $params['max'] ) || isset( $params['reverse'] ) )
        {
            $array = 'expslice(' . $array . ', ' .
                ( isset( $params['offset'] ) ? $this->expr( $params['offset'] ) : '0' ) . ', ' .
                ( isset( $params['max'] ) ? $this->expr( $params['max'] ) : 'null' ) . ', ' .
                ( isset( $params['reverse'] ) ? $this->expr( $params['reverse'] ) : 'false' ) . ')';
        }
        $body = $this->loopParts( $children, $delimiter );
        $out = '';
        $sequence = '';
        if ( isset( $params['sequence_array'] ) && isset( $params['sequence_var'] ) )
        {
            $seq = '__sequence' . ( ++$this->counter );
            $out .= $this->tag( 'set ' . $seq . ' = ' . $this->expr( $params['sequence_array'] ) );
            $sequenceVar = $this->varName( array(), $this->variableName( $params['sequence_var'] ) );
            $sequence = $this->tag( 'set ' . $sequenceVar . ' = expsequence(' . $seq . ', loop.index0)' );
        }
        list( $before, $start, $end ) = $this->openLoop( $item, $body );
        $delimiterCode = $this->loopDelimiter( $delimiter, $body, $delimBefore );
        $out .= $before . $delimBefore . $this->tag( 'for ' . ( $key ? $key . ', ' : '' ) . $item . ' in ' . $array );
        $out .= $start . $sequence . $delimiterCode . $this->nodes( $body ) . $end;
        $this->closeLoop();
        return $out . $this->tag( 'endfor' );
    }

    private function forFunction( array $params, array $children )
    {
        $from = isset( $params['first_val'] ) ? $this->expr( $params['first_val'] ) : '0';
        $to = isset( $params['last_val'] ) ? $this->expr( $params['last_val'] ) : '0';
        $item = isset( $params['loop_var'] ) ? $this->varName( array(), $this->variableName( $params['loop_var'] ) ) : 'i';
        $body = $this->loopParts( $children, $delimiter );
        $out = '';
        $sequence = '';
        if ( isset( $params['sequence_array'] ) && isset( $params['sequence_var'] ) )
        {
            $seq = '__sequence' . ( ++$this->counter );
            $out .= $this->tag( 'set ' . $seq . ' = ' . $this->expr( $params['sequence_array'] ) );
            $sequence = $this->tag( 'set ' . $this->varName( array(), $this->variableName( $params['sequence_var'] ) ) . ' = expsequence(' . $seq . ', loop.index0)' );
        }
        // {for} counts down as well as up: loop over the range
        list( $before, $start, $end ) = $this->openLoop( $item, $body );
        $delimiterCode = $this->loopDelimiter( $delimiter, $body, $delimBefore );
        $out .= $before . $delimBefore . $this->tag( 'for ' . $item . ' in exprange(' . $from . ', ' . $to . ')' );
        $out .= $start . $sequence . $delimiterCode . $this->nodes( $body ) . $end;
        $this->closeLoop();
        return $out . $this->tag( 'endfor' );
    }

    /**
     * {while} and {do} ({do} checks its condition after the first round) as {% expwhile %}.
     */
    private function whileFunction( array $params, array $children, $isDo )
    {
        $condition = $this->condition( $params );
        list( $before, $start, $end ) = $this->openLoop( null, $children );
        $loop = end( $this->loops );
        $stop = $loop['stop'];
        $out = $before;
        if ( $isDo )
        {
            $flag = '__do' . ( ++$this->counter );
            $out .= $this->tag( 'set ' . $flag . ' = true' );
            $test = ( $stop ? 'not ' . $stop . ' and ' : '' ) . $flag;
            $out .= $this->tag( 'expwhile ' . $test ) . ( $loop['skip'] ? $this->tag( 'set ' . $loop['skip'] . ' = false' ) : '' ) .
                    $this->nodes( $children ) . $this->tag( 'set ' . $flag . ' = ' . $this->param( $condition ) );
        }
        else
        {
            $test = ( $stop ? 'not ' . $stop . ' and ' : '' ) . $this->param( $condition );
            $out .= $this->tag( 'expwhile ' . $test ) . ( $loop['skip'] ? $this->tag( 'set ' . $loop['skip'] . ' = false' ) : '' ) .
                    $this->nodes( $children );
        }
        $this->closeLoop();
        return $out . $this->tag( 'endexpwhile' );
    }

    /**
     * {section}: a {% for %} with the section's variables (item, key, index, number, sequence) in the section's
     * namespace, or an {% if %} for a section with show= only.
     */
    private function sectionFunction( array $params, array $children )
    {
        $name = isset( $params['name'] ) ? $this->constant( $params['name'] ) : '';
        $namespace = $this->namespace;
        if ( $name !== '' && $name !== null )
            $namespace[] = $name;

        $body = $this->loopParts( $children, $delimiter, $filters, $else );
        $show = isset( $params['show'] ) ? $this->expr( $params['show'] ) : null;

        $saved = $this->namespace;
        $this->namespace = $namespace;

        if ( !isset( $params['loop'] ) )
        {
            $out = $this->nodes( $body );
            $this->namespace = $saved;
            if ( $show === null )
                return $out;
            return $this->tag( 'if ' . $show ) . $out . ( $else !== null ? $this->tag( 'else' ) . $this->nodes( $else ) : '' ) . $this->tag( 'endif' );
        }

        $this->namespace = $saved;
        $loop = 'expsectionloop(' . $this->expr( $params['loop'] ) . ', ' .
            ( isset( $params['offset'] ) ? $this->expr( $params['offset'] ) : '0' ) . ', ' .
            ( isset( $params['max'] ) ? $this->expr( $params['max'] ) : 'null' ) . ', ' .
            ( isset( $params['reverse'] ) ? $this->expr( $params['reverse'] ) : 'false' );
        $seqCode = isset( $params['sequence'] ) ? $this->expr( $params['sequence'] ) : null;
        $this->namespace = $namespace;

        $item = isset( $params['var'] ) ? $this->varName( array(), $this->variableName( $params['var'] ) ) : $this->varName( $namespace, 'item' );
        $key = $this->varName( $namespace, 'key' );
        $out = '';
        if ( $show !== null )
            $out .= $this->tag( 'if ' . $show );
        $seq = null;
        if ( $seqCode !== null )
        {
            $seq = '__sequence' . ( ++$this->counter );
            $out .= $this->tag( 'set ' . $seq . ' = ' . $seqCode );
        }
        // var=<name>: the variable is the section's iterator (v.index, v.item, v.name ...), as in the template system
        $loop .= isset( $params['var'] ) ? ', true' . ( $seq !== null ? ', ' . $seq : '' ) . ')' : ')';
        list( $before, $start, $end ) = $this->openLoop( $item, $body );
        $inner = $this->setVariable( $this->varName( $namespace, 'index' ), 'loop.index0' );
        $inner .= $this->setVariable( $this->varName( $namespace, 'number' ), 'loop.index' );
        if ( $seq )
            $inner .= $this->setVariable( $this->varName( $namespace, 'sequence' ), 'expsequence(' . $seq . ', loop.index0)' );
        $filterOpen = '';
        $filterClose = '';
        if ( $filters )
        {
            $skip = '__exclude' . ( ++$this->counter );
            $filterOpen .= $this->tag( 'set ' . $skip . ' = false' );
            foreach ( $filters as $filter )
            {
                $match = isset( $filter[3]['match'] ) ? $this->expr( $filter[3]['match'] ) : 'false';
                $filterOpen .= $this->tag( 'if ' . $match ) . $this->tag( 'set ' . $skip . ' = ' . ( $filter[2] === 'section-exclude' ? 'true' : 'false' ) ) . $this->tag( 'endif' );
            }
            $filterOpen .= $this->tag( 'if not ' . $skip );
            $filterClose = $this->tag( 'endif' );
        }
        $delimiterCode = $this->loopDelimiter( $delimiter, $body, $delimBefore );
        $out .= $before . $delimBefore . $this->tag( 'for ' . $key . ', ' . $item . ' in ' . $loop );
        $out .= $start . $inner . $filterOpen . $delimiterCode . $this->nodes( $body ) . $filterClose . $end;
        $this->closeLoop();
        $this->namespace = $saved;
        if ( $else !== null )
            $out .= $this->tag( 'else' ) . $this->nodes( $else );
        $out .= $this->tag( 'endfor' );
        if ( $show !== null )
            $out .= ( $else !== null ? $this->tag( 'else' ) . $this->nodes( $else ) : '' ) . $this->tag( 'endif' );
        return $out;
    }

    /**
     * {switch} as an {% if %} chain on the matched value (== and in, as the switch function compares).
     */
    private function switchFunction( array $params, array $children )
    {
        $value = '__switch' . ( ++$this->counter );
        $out = $this->tag( 'set ' . $value . ' = ' . ( isset( $params['match'] ) ? $this->expr( $params['match'] ) : 'null' ) );
        $conditions = array();
        $default = null;
        foreach ( $children as $child )
        {
            if ( $child[0] != eZTemplate::NODE_FUNCTION || $child[2] !== 'case' )
                continue;
            $caseParams = isset( $child[3] ) && is_array( $child[3] ) ? $child[3] : array();
            $caseBody = is_array( $child[1] ) ? $child[1] : array();
            if ( isset( $caseParams['match'] ) )
                $conditions[] = array( $value . ' == ' . $this->param( $this->expr( $caseParams['match'] ) ), $caseBody );
            else if ( isset( $caseParams['in'] ) )
            {
                $in = $this->expr( $caseParams['in'] );
                if ( isset( $caseParams['key'] ) )
                    $in = 'expcolumn(' . $in . ', ' . $this->expr( $caseParams['key'] ) . ')';
                $conditions[] = array( $value . ' in ' . $this->pipeInput( $in ), $caseBody );
            }
            else
                $default = $caseBody;
        }
        $first = true;
        foreach ( $conditions as $condition )
        {
            $out .= $this->tag( ( $first ? 'if ' : 'elseif ' ) . $condition[0] ) . $this->nodes( $condition[1] );
            $first = false;
        }
        if ( $default !== null )
            $out .= $first ? $this->nodes( $default ) : $this->tag( 'else' ) . $this->nodes( $default );
        if ( !$first )
            $out .= $this->tag( 'endif' );
        return $out;
    }

    private function setBlockFunction( $name, array $params, array $children )
    {
        $variable = isset( $params['variable'] ) ? $params['variable'] : null;
        $variableName = null;
        if ( $variable )
        {
            if ( $variable[0][0] == eZTemplate::TYPE_VARIABLE )
                $variableName = $variable[0][1][2];
            else
                $variableName = $this->constant( $variable );
        }
        if ( !$variableName )
        {
            $this->warn( "{{$name}} without a variable" );
            return $this->nodes( $children );
        }
        $scope = isset( $params['scope'] ) ? $this->constant( $params['scope'] ) : null;
        $namespace = isset( $params['name'] ) ? $this->constant( $params['name'] ) : null;
        if ( $name === 'append-block' || ( $scope !== null && $scope !== 'relative' && $scope !== 'root' ) || $namespace )
        {
            $tag = $name === 'append-block' ? 'expappendblock' : 'expsetblock';
            return $this->tag( $tag . ' variable=' . $this->quote( $variableName ) .
                               ( $scope ? ' scope=' . $this->quote( $scope ) : '' ) .
                               ( $namespace ? ' name=' . $this->quote( $namespace ) : '' ) ) .
                   $this->nodes( $children ) . $this->tag( 'end' . $tag );
        }
        $target = $scope === 'root' ? array() : $this->namespace;
        $var = $this->varName( $target, $variableName );
        if ( in_array( $var, self::TWIG_WORDS, true ) )
            return $this->tag( 'expsetblock variable=' . $this->quote( $var ) . ' scope=' . $this->quote( 'root' ) ) . $this->nodes( $children ) . $this->tag( 'endexpsetblock' );
        if ( $this->loops )
            $this->hoist[$var] = true;
        return $this->tag( 'set ' . $var ) . $this->nodes( $children ) . $this->tag( 'endset' );
    }

    /**
     * Named parameters of a tag: " a=<expr> b=<expr>".
     */
    private function namedParams( array $params )
    {
        $out = '';
        foreach ( $params as $name => $value )
        {
            if ( !preg_match( '/^[a-z_][a-z0-9_]*$/i', (string)$name ) )
            {
                $this->warn( "parameter name '$name' is not a Twig name" );
                continue;
            }
            $out .= ' ' . $name . '=' . $this->param( $this->expr( $value ) );
        }
        return $out;
    }

    /**
     * Named parameters of a template function as a Twig hash: {a: <expr>, b: <expr>}.
     */
    private function hash( array $params )
    {
        $pairs = array();
        foreach ( $params as $name => $value )
            $pairs[] = $this->quote( (string)$name ) . ': ' . $this->expr( $value );
        return '{' . implode( ', ', $pairs ) . '}';
    }

    // ----------------------------------------------------------------------------------------
    // Expressions

    /**
     * @param array $elements an element list of the parse tree
     * @return string Twig expression
     */
    public function expr( $elements )
    {
        if ( !is_array( $elements ) || !$elements )
            return 'expvoid()';
        $code = null;
        $simple = false;
        foreach ( $elements as $element )
        {
            switch ( $element[0] )
            {
                case eZTemplate::TYPE_STRING:
                case eZTemplate::TYPE_IDENTIFIER:
                    $code = $this->quote( (string)$element[1] );
                    $simple = false;
                    break;
                case eZTemplate::TYPE_NUMERIC:
                    $code = $this->number( $element[1] );
                    $simple = false;
                    break;
                case eZTemplate::TYPE_BOOLEAN:
                    $code = $element[1] ? 'true' : 'false';
                    $simple = false;
                    break;
                case eZTemplate::TYPE_VOID:
                    $code = 'expvoid()';
                    $simple = false;
                    break;
                case eZTemplate::TYPE_VARIABLE:
                    list( $code, $simple ) = $this->variable( $element[1] );
                    break;
                case eZTemplate::TYPE_ATTRIBUTE:
                    $code = $this->attribute( $code, $simple, $element[1] );
                    $simple = $code !== 'null';
                    break;
                case eZTemplate::TYPE_OPERATOR:
                    $operator = $element[1];
                    $operatorName = array_shift( $operator );
                    $code = $this->operator( $operatorName, $operator, $code );
                    $simple = false;
                    break;
                default:
                    $this->warn( 'unknown element type ' . $element[0] );
            }
        }
        return $code === null ? 'null' : $code;
    }

    /**
     * @return array (code, whether further lookups can be appended with . and [])
     */
    private function variable( array $data )
    {
        list( $namespace, $scope, $name ) = $data;
        $namespace = (string)$namespace;
        if ( $scope == eZTemplate::NAMESPACE_SCOPE_GLOBAL )
            return array( 'expvar(' . $this->quote( $name ) . ', ' . $this->quote( $namespace ) . ')', false );
        $path = $scope == eZTemplate::NAMESPACE_SCOPE_RELATIVE ? $this->namespace : array();
        if ( $namespace !== '' )
            $path = array_merge( $path, explode( ':', $namespace ) );
        return array( $this->readName( $this->varName( $path, $name ) ), true );
    }

    /**
     * A variable as read in an expression: _context['in'] for a name Twig reads as a word.
     */
    private function readName( $name )
    {
        if ( in_array( $name, self::TWIG_WORDS, true ) )
            return "_context['" . $name . "']";
        return $name;
    }

    /**
     * Twig variable of a template variable in a namespace below the root namespace: name, Namespace__name.
     */
    private function varName( array $path, $name )
    {
        $name = preg_replace( '/[^a-z0-9_]/i', '_', (string)$name );
        if ( $name === '' || ctype_digit( $name[0] ) )
            $name = '_' . $name;
        $prefix = '';
        foreach ( $path as $part )
            $prefix .= preg_replace( '/[^a-z0-9_]/i', '_', $part ) . '__';
        return $prefix . $name;
    }

    private function variableName( $elements )
    {
        if ( isset( $elements[0][0] ) && $elements[0][0] == eZTemplate::TYPE_VARIABLE )
            return $elements[0][1][2];
        return $this->constant( $elements );
    }

    private function attribute( $code, $simple, array $keyElements )
    {
        if ( $code === null )
        {
            $this->warn( 'attribute lookup without a value' );
            return 'null';
        }
        $key = null;
        if ( count( $keyElements ) == 1 && in_array( $keyElements[0][0], array( eZTemplate::TYPE_IDENTIFIER, eZTemplate::TYPE_STRING, eZTemplate::TYPE_NUMERIC ) ) )
            $key = $keyElements[0][1];
        $base = $simple ? $code : '(' . $code . ')';
        if ( $key !== null && is_string( $key ) && preg_match( '/^[a-z_][a-z0-9_]*$/i', $key ) )
            return $base . '.' . $key;
        $keyCode = $key !== null ? ( is_int( $key ) ? (string)$key : $this->quote( (string)$key ) ) : $this->expr( $keyElements );
        return $base . '[' . $keyCode . ']';
    }

    /**
     * An operator call: input|name(p1, p2) with an input, name(p1, p2) without.
     */
    private function operator( $name, array $parameters, $input )
    {
        $args = array();
        foreach ( $parameters as $parameter )
            $args[] = $this->expr( $parameter );

        if ( $input === null )
        {
            switch ( $name )
            {
                case 'hash':
                    $pairs = array();
                    for ( $i = 0; $i + 1 < count( $args ); $i += 2 )
                        $pairs[] = $this->hashKey( $args[$i] ) . ': ' . $args[$i + 1];
                    if ( count( $args ) % 2 )
                        $this->warn( 'hash() with an odd number of parameters' );
                    return '{' . implode( ', ', $pairs ) . '}';
                case 'array':
                    return '[' . implode( ', ', $args ) . ']';
                case 'true':
                    if ( !$args )
                        return 'true';
                    break;
                case 'false':
                    if ( !$args )
                        return 'false';
                    break;
            }
            return $this->operatorName( $name, self::TWIG_FUNCTIONS ) . '(' . implode( ', ', $args ) . ')';
        }
        return $this->pipeInput( $input ) . '|' . $this->operatorName( $name, self::TWIG_FILTERS ) . ( $args ? '(' . implode( ', ', $args ) . ')' : '' );
    }

    /**
     * A value a filter is applied to: parenthesised unless it is a single term.
     */
    private function pipeInput( $code )
    {
        if ( preg_match( '/^([a-z_][a-z0-9_]*(\.[a-z_][a-z0-9_]*|\[[^\[\]]*\])*|\'(?:[^\'\\\\]|\\\\.)*\'|[0-9.]+|true|false|null)$/i', $code ) )
            return $code;
        if ( preg_match( '/^[a-z0-9_]+\(/i', $code ) && $this->balanced( $code ) )
            return $code;
        return $this->balancedParens( $code ) ? $code : '(' . $code . ')';
    }

    /**
     * A value used as a tag parameter: parenthesised unless it is a single term.
     */
    private function param( $code )
    {
        if ( preg_match( '/^([a-z_][a-z0-9_]*(\.[a-z_][a-z0-9_]*)*|\'(?:[^\'\\\\]|\\\\.)*\'|-?[0-9.]+|true|false|null)$/i', $code ) )
            return $code;
        if ( $this->balancedParens( $code ) )
            return $code;
        return '(' . $code . ')';
    }

    private function hashKey( $code )
    {
        if ( preg_match( '/^(\'(?:[^\'\\\\]|\\\\.)*\'|[0-9]+)$/', $code ) )
            return $code;
        return '(' . $code . ')';
    }

    private function balancedParens( $code )
    {
        return strlen( $code ) > 1 && $code[0] === '(' && substr( $code, -1 ) === ')' && $this->closes( $code, 0 ) === strlen( $code ) - 1;
    }

    private function balanced( $code )
    {
        $open = strpos( $code, '(' );
        return $open !== false && substr( $code, -1 ) === ')' && $this->closes( $code, $open ) === strlen( $code ) - 1;
    }

    private function closes( $code, $start )
    {
        $depth = 0;
        $length = strlen( $code );
        for ( $i = $start; $i < $length; ++$i )
        {
            $c = $code[$i];
            if ( $c === "'" )
            {
                for ( ++$i; $i < $length && $code[$i] !== "'"; ++$i )
                {
                    if ( $code[$i] === '\\' )
                        ++$i;
                }
                continue;
            }
            if ( $c === '(' || $c === '[' || $c === '{' )
                ++$depth;
            else if ( $c === ')' || $c === ']' || $c === '}' )
            {
                --$depth;
                if ( $depth === 0 )
                    return $i;
            }
        }
        return -1;
    }

    /**
     * The filter or function name of an operator: its own name, or exp_<name> where Twig has a filter or function
     * of that name, where the name is a word of Twig's or a helper of lib/twig, where the operator's name begins with
     * exp_ and is not registered, or where the operator is not registered (exp_<name> then passes its input through, as in
     * a .tpl). A registered operator whose own name begins with exp_ is written by its name.
     */
    private function operatorName( $name, array $twigNames )
    {
        // an operator whose own name begins with exp_ (exp_path) is found by that name first: written as it is
        if ( strncmp( $name, 'exp_', 4 ) === 0 && isset( $this->tpl->Operators[$name] ) )
            return $name;
        if ( in_array( $name, $twigNames, true ) || in_array( $name, self::TWIG_WORDS, true ) || in_array( $name, self::HELPERS, true ) ||
             !isset( $this->tpl->Operators[$name] ) || !preg_match( '/^[a-z_][a-z0-9_]*$/i', $name ) )
            return 'exp_' . preg_replace( '/[^a-z0-9_]/i', '_', $name );
        // a template function of the same name would be called as the function
        if ( $twigNames === self::TWIG_FUNCTIONS && isset( $this->tpl->Functions[$name] ) )
            return 'exp_' . $name;
        return $name;
    }

    private function constant( $elements )
    {
        if ( isset( $elements[0] ) && count( $elements ) == 1 &&
             in_array( $elements[0][0], array( eZTemplate::TYPE_STRING, eZTemplate::TYPE_IDENTIFIER, eZTemplate::TYPE_NUMERIC, eZTemplate::TYPE_BOOLEAN ) ) )
            return $elements[0][1];
        $this->warn( 'a constant value was expected' );
        return null;
    }

    private function quote( $string )
    {
        return "'" . str_replace( array( '\\', "'" ), array( '\\\\', "\\'" ), $string ) . "'";
    }

    private function number( $value )
    {
        if ( is_int( $value ) )
            return (string)$value;
        if ( is_float( $value ) )
            return var_export( $value, true );
        return is_numeric( $value ) ? (string)$value : $this->quote( (string)$value );
    }

    private function warn( $message )
    {
        $this->warnings[] = $message;
    }
}
