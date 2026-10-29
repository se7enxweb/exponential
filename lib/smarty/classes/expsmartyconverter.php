<?php
/**
 * Converts Exponential templates (.tpl) to Smarty templates (.stpl) that render the same output
 * through lib/smarty.
 *
 * The .tpl is parsed by the template system's own parser (eZTemplateMultiPassParser), so the
 * converter sees exactly the tree the template system would run, with its whitespace rules
 * already applied. Every node is then written out as Smarty:
 *
 *  - text as it is (braces that Smarty would read as a tag become {ldelim});
 *  - {$expr} as {expr}, where variables keep their names, attribute lookups become dot or
 *    bracket lookups, and every template operator stays an operator, called as a Smarty
 *    modifier ($x|op:a:b) or, without input, as a function (op(a, b));
 *  - the template functions as their Smarty equivalent or as a tag of expSmartyExtension.
 *
 * The table of every mapping is in doc/eng-us/guides/smarty-templates-converter.md.
 *
 * Smarty removes a newline right after a tag that prints nothing ({if}, {foreach}, an
 * assignment, a block). The converter writes a newline after each such tag, so the layout of
 * the .stpl stays readable and the text after it keeps the newlines the template system
 * would have printed.
 *
 * @package lib
 */
class expSmartyConverter
{
    /** Smarty's own modifiers: an operator of the same name is called with the exp_ prefix. */
    const SMARTY_MODIFIERS = array(
        'capitalize', 'cat', 'count', 'count_characters', 'count_paragraphs', 'count_sentences',
        'count_words', 'date_format', 'debug_print_var', 'default', 'empty', 'escape', 'explode',
        'from_charset', 'implode', 'in_array', 'indent', 'is_array', 'isset', 'join', 'json_encode',
        'lower', 'mb_wordwrap', 'nl2br', 'noprint', 'number_format', 'raw', 'regex_replace', 'replace',
        'round', 'spacify', 'split', 'str_repeat', 'string_format', 'strip', 'strip_tags', 'strlen',
        'substr', 'to_charset', 'truncate', 'unescape', 'upper', 'wordwrap',
    );

    /** Names expSmartyExtension registers itself; an operator of the same name gets the exp_ prefix. */
    const OWN_MODIFIERS = array( 'expattr', 'expvoid', 'expvar', 'expsequence', 'expslice', 'exprange', 'expsectionloop' );

    /** Template functions the converter writes as Smarty code of its own. */
    const STRUCTURAL = array(
        'if', 'elseif', 'else', 'foreach', 'for', 'while', 'do', 'def', 'undef', 'set', 'let', 'default',
        'section', 'section-else', 'section-exclude', 'section-include', 'delimiter', 'switch', 'case',
        'set-block', 'append-block', 'cache-block', 'run-once', 'include', 'literal', 'ldelim', 'rdelim',
        'break', 'continue', 'skip',
    );

    /** @var eZTemplate */
    private $tpl;

    /** @var string[] */
    private $warnings = array();

    /** @var string */
    private $file = '';

    /** @var int */
    private $counter = 0;

    /**
     * Current namespace, relative to the template's root namespace: changed by named {section}s.
     * @var string[]
     */
    private $namespace = array();

    /** @var array[] Open loops: array( 'item' => smarty variable name of the loop item ) */
    private $loops = array();

    /** @var array cache of operator input modes */
    private static $modes = array();

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

    /**
     * @param string $file the .tpl file
     * @return string the .stpl text
     */
    public function convertFile( $file )
    {
        return $this->convert( file_get_contents( $file ), $file );
    }

    /**
     * @param string $text template text
     * @param string $file name used in messages
     * @return string the .stpl text
     */
    public function convert( $text, $file = 'template' )
    {
        $this->warnings = array();
        $this->file = $file;
        $this->counter = 0;
        $this->namespace = array();
        $this->loops = array();

        $parser = eZTemplateMultiPassParser::instance();
        $root = array( eZTemplate::NODE_ROOT, false );
        $resourceData = array( 'resource' => 'file', 'template-name' => $file, 'template-filename' => $file, 'text' => $text );
        $parser->parse( $this->tpl, $text, $root, '', $resourceData );

        $out = $this->nodes( is_array( $root[1] ) ? $root[1] : array() );
        // Smarty drops the first newline of a template (as the newline after a tag that prints nothing): a
        // template that starts with one gets a second, so that the .stpl prints it.
        if ( strncmp( $out, "\n", 1 ) === 0 || strncmp( $out, "\r\n", 2 ) === 0 )
            $out = "\n" . $out;
        return $out;
    }

    // ----------------------------------------------------------------------------------------
    // Nodes

    /**
     * @param array $nodes
     * @return string
     */
    private function nodes( array $nodes )
    {
        $out = '';
        foreach ( $nodes as $node )
            $out .= $this->node( $node );
        return $out;
    }

    private function node( array $node )
    {
        switch ( $node[0] )
        {
            case eZTemplate::NODE_TEXT:
                return $this->text( $node[2] );
            case eZTemplate::NODE_VARIABLE:
                return '{' . $this->expr( $node[2] ) . '}';
            case eZTemplate::NODE_FUNCTION:
                return $this->func( $node );
        }
        $this->warn( 'unknown node type ' . $node[0] );
        return '';
    }

    /**
     * Text as it is. A brace that is not followed by white space would start a Smarty tag.
     */
    private function text( $text )
    {
        return preg_replace( '/\{(?!\s)/', '{ldelim}', (string)$text );
    }

    /**
     * A tag that prints nothing: Smarty drops the newline after it, so one is written for it.
     */
    private function tag( $code )
    {
        return '{' . $code . "}\n";
    }

    /**
     * {/while} without the newline: Smarty keeps the newline after this tag, where it removes it after the others.
     */
    private function closeWhile()
    {
        return '{/while}';
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
            case 'elseif':
                return $this->tag( 'elseif ' . $this->condition( $params ) );
            case 'else':
                return $this->tag( 'else' );
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
                return $this->tag( 'while ' . $this->condition( $params ) ) . $this->nodes( $children ) . $this->closeWhile();
            case 'do':
                return $this->doFunction( $params, $children );
            case 'section':
                return $this->sectionFunction( $params, $children );
            case 'switch':
                return $this->switchFunction( $params, $children );
            case 'set-block':
            case 'append-block':
                return $this->setBlockFunction( $name, $params, $children );
            case 'cache-block':
                return $this->tag( 'expcacheblock id=' . ( ++$this->counter ) . $this->namedParams( $params ) ) . $this->nodes( $children ) . $this->tag( '/expcacheblock' );
            case 'run-once':
                return $this->tag( 'exprunonce id=' . ( ++$this->counter ) ) . $this->nodes( $children ) . $this->tag( '/exprunonce' );
            case 'include':
                return '{expinclude' . $this->namedParams( $params ) . '}';
            case 'literal':
                return $this->nodes( $children );
            case 'ldelim':
                return '{ldelim}';
            case 'rdelim':
                return '{rdelim}';
            case 'break':
                return $this->tag( 'break' );
            case 'continue':
                return $this->tag( 'continue' );
            case 'skip':
                // no delimiter before the next item (see loopDelimiter())
                $loop = end( $this->loops );
                return ( $loop && isset( $loop['delimiter'] ) ? $this->tag( $loop['delimiter'] . ' = false' ) : '' ) . $this->tag( 'continue' );
            case 'delimiter':
            case 'section-else':
            case 'section-exclude':
            case 'section-include':
            case 'case':
                $this->warn( "{{$name}} outside of its loop or switch" );
                return '';
        }

        if ( !isset( $this->tpl->Functions[$name] ) )
        {
            // The template system prints nothing for a function it does not know.
            $this->warn( "unknown function {{$name}}: it prints nothing, as in the .tpl" );
            return '';
        }
        if ( !preg_match( '/^[a-z][a-z0-9_]*$/i', $name ) )
        {
            $this->warn( "function {{$name}}: the name is not a Smarty tag name" );
            return '';
        }
        // A template function with children ({explblock name=...}...{/explblock}) is a Smarty
        // block of the same name (expSmartyBlockFunction), any other one a Smarty tag of the
        // same name (expSmartyFunction).
        if ( is_array( $node[1] ) )
            return $this->tag( $name . $this->namedParams( $params ) ) . $this->nodes( $children ) . $this->tag( '/' . $name );
        return '{' . $name . $this->namedParams( $params ) . '}';
    }

    private function ifFunction( array $params, array $children )
    {
        if ( !isset( $params['condition'] ) || !$params['condition'] )
        {
            // The if function prints nothing at all, no branch, when it has no condition.
            $this->warn( '{if} without a condition (a parse error in the .tpl): it prints nothing, as in the .tpl' );
            return '';
        }
        return $this->tag( 'if ' . $this->condition( $params ) ) . $this->nodes( $children ) . $this->tag( '/if' );
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
                // {set scope=root|global name=...}: into the template system's namespaces too.
                // {expset} is a function: Smarty keeps the newline after it, so none is written for it (see tag()).
                $out .= '{expset name=' . $this->quote( $variable ) . ' value=' . $this->param( $code ) .
                            ( $scope ? ' scope=' . $this->quote( $scope ) : '' ) .
                            ( $namespace ? ' namespace=' . $this->quote( $namespace ) : '' ) . '}';
                continue;
            }
            // {def} defines in the root namespace, {set} sets in the current one.
            $target = $name === 'def' ? array() : $this->namespace;
            $out .= $this->tag( $this->varName( $target, $variable ) . ' = ' . $code );
        }
        return $out;
    }

    private function undefFunction( array $params )
    {
        $out = '';
        foreach ( $params as $variable => $value )
        {
            // {undef $a $b}: the parameters are variable elements, keyed by number
            $name = $variable;
            if ( is_int( $variable ) || ( isset( $value[0][0] ) && $value[0][0] == eZTemplate::TYPE_VARIABLE ) )
            {
                if ( isset( $value[0][1][2] ) )
                    $name = $value[0][1][2];
            }
            if ( !is_string( $name ) || $name === '' )
                continue;
            $out .= $this->tag( $this->varName( array(), $name ) . ' = null' );
        }
        if ( !$params )
            // {expundefall} is a function: Smarty keeps the newline after it (see tag())
            $out .= '{expundefall}';
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
                $set .= $this->tag( 'if !isset(' . $var . ')' ) . $this->tag( $var . ' = ' . $this->expr( $value ) ) . $this->tag( '/if' );
            else
                $set .= $this->tag( $var . ' = ' . $this->expr( $value ) );
            $unset .= $this->tag( $var . ' = null' );
        }
        $saved = $this->namespace;
        $this->namespace = $target;
        $body = $this->nodes( $children );
        $this->namespace = $saved;
        return $set . $body . ( $name === 'let' ? $unset : '' );
    }

    /**
     * Splits a loop's children into the body and its {delimiter}.
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

    private function foreachFunction( array $params, array $children )
    {
        $array = isset( $params['array'] ) ? $this->expr( $params['array'] ) : '[]';
        $item = isset( $params['item_var'] ) ? $this->varName( array(), $this->variableName( $params['item_var'] ) ) : '$item';
        $key = isset( $params['key_var'] ) ? $this->varName( array(), $this->variableName( $params['key_var'] ) ) : null;

        if ( isset( $params['offset'] ) || isset( $params['max'] ) || isset( $params['reverse'] ) )
        {
            $array = '(' . $array . ')|expslice:' .
                ( isset( $params['offset'] ) ? $this->param( $this->expr( $params['offset'] ) ) : '0' ) . ':' .
                ( isset( $params['max'] ) ? $this->param( $this->expr( $params['max'] ) ) : 'null' ) . ':' .
                ( isset( $params['reverse'] ) ? $this->param( $this->expr( $params['reverse'] ) ) : 'false' );
        }

        $body = $this->loopParts( $children, $delimiter );
        $out = '';
        $sequence = '';
        if ( isset( $params['sequence_array'] ) && isset( $params['sequence_var'] ) )
        {
            $seq = '$__sequence' . ( ++$this->counter );
            $out .= $this->tag( $seq . ' = ' . $this->expr( $params['sequence_array'] ) );
            $sequence = $this->tag( $this->varName( array(), $this->variableName( $params['sequence_var'] ) ) . ' = ' . $seq . '|expsequence:' . $item . '@index' );
        }
        $source = $this->loopSource( $array, $out );
        $loopStart = strlen( $out );
        $out .= $this->tag( 'foreach ' . $source . ' as ' . ( $key ? $key . ' => ' : '' ) . $item );
        $out .= $sequence;
        $this->loops[] = array( 'item' => $item );
        $out .= $this->loopDelimiter( $item, $delimiter, $body, $before );
        $out = substr_replace( $out, $before, $loopStart, 0 );
        $out .= $this->nodes( $body );
        array_pop( $this->loops );
        return $out . $this->tag( '/foreach' );
    }

    private function forFunction( array $params, array $children )
    {
        $from = isset( $params['first_val'] ) ? $this->expr( $params['first_val'] ) : '0';
        $to = isset( $params['last_val'] ) ? $this->expr( $params['last_val'] ) : '0';
        $item = isset( $params['loop_var'] ) ? $this->varName( array(), $this->variableName( $params['loop_var'] ) ) : '$i';
        $body = $this->loopParts( $children, $delimiter );
        $out = '';
        $sequence = '';
        if ( isset( $params['sequence_array'] ) && isset( $params['sequence_var'] ) )
        {
            $seq = '$__sequence' . ( ++$this->counter );
            $out .= $this->tag( $seq . ' = ' . $this->expr( $params['sequence_array'] ) );
            $sequence = $this->tag( $this->varName( array(), $this->variableName( $params['sequence_var'] ) ) . ' = ' . $seq . '|expsequence:' . $item . '@index' );
        }
        // eZ's {for} counts down as well as up, Smarty's {for} only up: loop over the range.
        $loopStart = strlen( $out );
        $out .= $this->tag( 'foreach exprange(' . $from . ', ' . $to . ') as ' . $item ) . $sequence;
        $this->loops[] = array( 'item' => $item );
        $out .= $this->loopDelimiter( $item, $delimiter, $body, $before );
        $out = substr_replace( $out, $before, $loopStart, 0 );
        $out .= $this->nodes( $body );
        array_pop( $this->loops );
        return $out . $this->tag( '/foreach' );
    }

    /**
     * Whether a loop body holds a {skip} of its own (not one of a loop inside it).
     */
    private function hasSkip( array $nodes )
    {
        foreach ( $nodes as $node )
        {
            if ( $node[0] != eZTemplate::NODE_FUNCTION )
                continue;
            if ( $node[2] === 'skip' )
                return true;
            if ( !in_array( $node[2], array( 'foreach', 'for', 'section', 'while', 'do' ), true ) &&
                 is_array( $node[1] ) && $this->hasSkip( $node[1] ) )
                return true;
        }
        return false;
    }

    /**
     * The delimiter at the start of a loop body. The template system prints it before every item but
     * the first, and not after an item that ended with {skip}: with a {skip} in the body, a flag
     * ($__delim<n>, set by the {skip}, see the 'skip' case of func()) keeps that; otherwise it is
     * {if !$item@first}. $before gets the flag's initialization, which goes before the loop tag.
     */
    private function loopDelimiter( $item, $delimiter, array $body, &$before )
    {
        $before = '';
        if ( $delimiter === null )
            return '';
        if ( !$this->hasSkip( $body ) )
            return $this->tag( 'if !' . $item . '@first' ) . $this->nodes( $delimiter ) . $this->tag( '/if' );
        $flag = '$__delim' . ( ++$this->counter );
        $this->loops[count( $this->loops ) - 1]['delimiter'] = $flag;
        $before = $this->tag( $flag . ' = false' );
        return $this->tag( 'if ' . $flag ) . $this->nodes( $delimiter ) . $this->tag( '/if' ) . $this->tag( $flag . ' = true' );
    }

    private function doFunction( array $params, array $children )
    {
        $condition = $this->condition( $params );
        $flag = '$__do' . ( ++$this->counter );
        return $this->tag( $flag . ' = true' ) . $this->tag( 'while ' . $flag ) . $this->nodes( $children ) .
               $this->tag( $flag . ' = ' . $condition ) . $this->closeWhile();
    }

    /**
     * {section}: a {foreach} with the section's variables (item, key, index, number, sequence) in
     * the section's namespace, or an {if} for a section with show= only.
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
            return $this->tag( 'if ' . $show ) . $out . ( $else !== null ? $this->tag( 'else' ) . $this->nodes( $else ) : '' ) . $this->tag( '/if' );
        }

        $loop = $this->expr( $params['loop'] );
        $this->namespace = $saved;
        $loop = '(' . $loop . ')|expsectionloop:' .
            ( isset( $params['offset'] ) ? $this->param( $this->expr( $params['offset'] ) ) : '0' ) . ':' .
            ( isset( $params['max'] ) ? $this->param( $this->expr( $params['max'] ) ) : 'null' ) . ':' .
            ( isset( $params['reverse'] ) ? $this->param( $this->expr( $params['reverse'] ) ) : 'false' );
        $this->namespace = $namespace;

        // var=<name> names the item variable ({section var=v loop=$l}{$v}{/section}); otherwise it is $item
        $item = isset( $params['var'] ) ? $this->varName( array(), $this->variableName( $params['var'] ) ) : $this->varName( $namespace, 'item' );
        $key = $this->varName( $namespace, 'key' );
        $out = '';
        if ( $show !== null )
            $out .= $this->tag( 'if ' . $show );
        $seq = null;
        if ( isset( $params['sequence'] ) )
        {
            $this->namespace = $saved;
            $seq = '$__sequence' . ( ++$this->counter );
            $out .= $this->tag( $seq . ' = ' . $this->expr( $params['sequence'] ) );
            $this->namespace = $namespace;
        }
        // var=<name>: the variable is the section's iterator ($v.index, $v.item, $v.name ...), as in the template system
        if ( isset( $params['var'] ) )
            $loop .= ':true' . ( $seq !== null ? ':' . $seq : '' );
        $source = $this->loopSource( $loop, $out );
        $loopStart = strlen( $out );
        $out .= $this->tag( 'foreach ' . $source . ' as ' . $key . ' => ' . $item );
        $out .= $this->tag( $this->varName( $namespace, 'index' ) . ' = ' . $item . '@index' );
        $out .= $this->tag( $this->varName( $namespace, 'number' ) . ' = ' . $item . '@iteration' );
        if ( $seq )
            $out .= $this->tag( $this->varName( $namespace, 'sequence' ) . ' = ' . $seq . '|expsequence:' . $item . '@index' );
        $this->loops[] = array( 'item' => $item );
        $skip = null;
        if ( $filters )
        {
            $skip = '$__skip' . ( ++$this->counter );
            $out .= $this->tag( $skip . ' = false' );
            foreach ( $filters as $filter )
            {
                $match = isset( $filter[3]['match'] ) ? $this->expr( $filter[3]['match'] ) : 'false';
                $out .= $this->tag( 'if ' . $match ) . $this->tag( $skip . ' = ' . ( $filter[2] === 'section-exclude' ? 'true' : 'false' ) ) . $this->tag( '/if' );
            }
            $out .= $this->tag( 'if !' . $skip );
        }
        $out .= $this->loopDelimiter( $item, $delimiter, $body, $before );
        $out = substr_replace( $out, $before, $loopStart, 0 );
        $out .= $this->nodes( $body );
        if ( $skip )
            $out .= $this->tag( '/if' );
        array_pop( $this->loops );
        $this->namespace = $saved;
        if ( $else !== null )
            $out .= $this->tag( 'foreachelse' ) . $this->nodes( $else );
        $out .= $this->tag( '/foreach' );
        if ( $show !== null )
            $out .= ( $else !== null ? $this->tag( 'else' ) . $this->nodes( $else ) : '' ) . $this->tag( '/if' );
        return $out;
    }

    /**
     * {switch match=...}{case match=...}...{/case}{case in=array(...)}...{/case}{case}...{/case}{/switch}
     * as an {if} chain on the matched value (== and in_array, as the switch function compares).
     */
    private function switchFunction( array $params, array $children )
    {
        $value = '$__switch' . ( ++$this->counter );
        $out = $this->tag( $value . ' = ' . ( isset( $params['match'] ) ? $this->expr( $params['match'] ) : 'null' ) );
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
                    $in = '(' . $in . ')|expcolumn:' . $this->param( $this->expr( $caseParams['key'] ) );
                $conditions[] = array( $value . '|in_array:' . $this->param( $in ), $caseBody );
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
            $out .= $this->tag( '/if' );
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
                   $this->nodes( $children ) . $this->tag( '/' . $tag );
        }
        // scope=root (the template's root namespace) and relative: the template's own variable,
        // which the renderer hands back to the template system when the template is done.
        $target = $scope === 'root' ? array() : $this->namespace;
        return $this->tag( 'capture assign=' . substr( $this->varName( $target, $variableName ), 1 ) ) . $this->nodes( $children ) . $this->tag( '/capture' );
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
                $this->warn( "parameter name '$name' is not a Smarty name" );
                continue;
            }
            $out .= ' ' . $name . '=' . $this->param( $this->expr( $value ) );
        }
        return $out;
    }

    // ----------------------------------------------------------------------------------------
    // Expressions

    /**
     * @param array $elements an element list of the parse tree
     * @return string Smarty expression
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
                    $code = $this->quote( (string)$element[1] );
                    $simple = false;
                    break;
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
                    break;
                case eZTemplate::TYPE_OPERATOR:
                    $operator = $element[1];
                    $operatorName = array_shift( $operator );
                    $code = $this->operator( $operatorName, $operator, $code, $simple );
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
        if ( $scope == eZTemplate::NAMESPACE_SCOPE_RELATIVE )
            $path = $this->namespace;
        else
            $path = array();
        if ( $namespace !== '' )
            $path = array_merge( $path, explode( ':', $namespace ) );
        return array( $this->varName( $path, $name ), true );
    }

    /**
     * Smarty variable of a template variable in a namespace below the root namespace:
     * $name, $Namespace__name for {section name=Namespace}.
     */
    private function varName( array $path, $name )
    {
        $name = preg_replace( '/[^a-z0-9_]/i', '_', (string)$name );
        if ( $name === '' || ctype_digit( $name[0] ) )
            $name = '_' . $name;
        $prefix = '';
        foreach ( $path as $part )
            $prefix .= preg_replace( '/[^a-z0-9_]/i', '_', $part ) . '__';
        return '$' . $prefix . $name;
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
        if ( $simple )
        {
            if ( $key !== null && preg_match( '/^([a-z_][a-z0-9_]*|0|[1-9][0-9]*)$/i', (string)$key ) )
                return $code . '.' . $key;
            $keyCode = $key !== null ? ( is_int( $key ) ? (string)$key : $this->quote( (string)$key ) ) : $this->expr( $keyElements );
            return $code . '[' . $this->param( $keyCode ) . ']';
        }
        $keyCode = $key !== null ? ( is_int( $key ) ? (string)$key : $this->quote( (string)$key ) ) : $this->expr( $keyElements );
        return $this->pipeInput( $code ) . '|expattr:' . $this->param( $keyCode );
    }

    /**
     * An operator call. With an input: $input|name:p1:p2. Without: name(p1, p2), or for an
     * operator that reads its input, name(null, p1, p2), which is how the template system calls
     * an operator that comes first in an expression.
     */
    private function operator( $name, array $parameters, $input, $inputSimple )
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
                        $pairs[] = $this->arrayKey( $args[$i] ) . ' => ' . $args[$i + 1];
                    if ( count( $args ) % 2 )
                        $this->warn( 'hash() with an odd number of parameters' );
                    return '[' . implode( ', ', $pairs ) . ']';
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
            $function = $this->operatorName( $name );
            if ( !$args || $this->inputMode( $name ) !== 'input' )
                return $function . '(' . implode( ', ', $args ) . ')';
            return $function . '(null, ' . implode( ', ', $args ) . ')';
        }

        // cond and first_set ignore their input at run time ($a|first_set( $b ) is $b when $b is
        // set), and so do the operators that read their parameters only ($a|ezhttp( 'Name' ) is
        // ezhttp( 'Name' )): the .stpl leaves it out to print the same.
        if ( in_array( $name, array( 'cond', 'first_set' ), true ) || expSmartyRenderer::isParameterOperator( $name ) )
            return $this->operatorName( $name ) . '(' . implode( ', ', $args ) . ')';
        $out = $this->pipeInput( $input ) . '|' . $this->operatorName( $name );
        foreach ( $args as $arg )
            $out .= ':' . $this->param( $arg );
        return $out;
    }

    /**
     * A value that a modifier is applied to.
     */
    private function pipeInput( $code )
    {
        if ( preg_match( '/^(\$[a-z0-9_]+(\.[a-z0-9_]+|\[[^\[\]]*\])*|\'(?:[^\'\\\\]|\\\\.)*\'|[0-9.]+|true|false|null)$/i', $code ) )
            return $code;
        if ( preg_match( '/^[a-z0-9_]+\(/i', $code ) && $this->balanced( $code ) )
            return '(' . $code . ')';
        return $this->balancedParens( $code ) ? $code : '(' . $code . ')';
    }

    /**
     * What a {foreach} iterates: a single term as it is; anything else is assigned to a variable
     * first ({$__loop1 = ...} added to $out), since Smarty reads "{foreach (...) as $x}" as the
     * attribute form and refuses it ("missing 'from' attribute").
     */
    private function loopSource( $code, &$out )
    {
        if ( preg_match( '/^\$[a-z0-9_]+(\.[a-z0-9_]+)*$/i', $code ) )
            return $code;
        $variable = '$__loop' . ( ++$this->counter );
        $out .= $this->tag( $variable . ' = ' . $code );
        return $variable;
    }

    /**
     * A value used as a modifier parameter, an index or a tag attribute: parenthesised unless it
     * is a single term.
     */
    private function param( $code )
    {
        if ( preg_match( '/^(\$[a-z0-9_]+(\.[a-z0-9_]+)*|\'(?:[^\'\\\\]|\\\\.)*\'|-?[0-9.]+|true|false|null|[a-z_][a-z0-9_]*\(\))$/i', $code ) )
            return $code;
        if ( $this->balancedParens( $code ) )
            return $code;
        return '(' . $code . ')';
    }

    /**
     * Whether $code is one parenthesised term "( ... )".
     */
    private function balancedParens( $code )
    {
        return strlen( $code ) > 1 && $code[0] === '(' && substr( $code, -1 ) === ')' && $this->closes( $code, 0 ) === strlen( $code ) - 1;
    }

    /**
     * Whether $code is one function call "name( ... )".
     */
    private function balanced( $code )
    {
        $open = strpos( $code, '(' );
        return $open !== false && substr( $code, -1 ) === ')' && $this->closes( $code, $open ) === strlen( $code ) - 1;
    }

    /**
     * Position of the bracket that closes the one at $start, skipping quoted strings.
     */
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
            if ( $c === '(' || $c === '[' )
                ++$depth;
            else if ( $c === ')' || $c === ']' )
            {
                --$depth;
                if ( $depth === 0 )
                    return $i;
            }
        }
        return -1;
    }

    private function arrayKey( $code )
    {
        return $this->param( $code );
    }

    /**
     * The modifier name of an operator: its own name, or exp_<name> where Smarty or
     * expSmartyExtension has a modifier of that name, or where the operator is not registered.
     */
    private function operatorName( $name )
    {
        if ( in_array( $name, self::SMARTY_MODIFIERS, true ) || in_array( $name, self::OWN_MODIFIERS, true ) ||
             strncmp( $name, 'exp_', 4 ) === 0 || !isset( $this->tpl->Operators[$name] ) )
            return 'exp_' . $name;
        return $name;
    }

    /**
     * 'param' when the operator takes its input as its first parameter (the operator hints
     * input-as-parameter, or input false), 'input' otherwise.
     */
    private function inputMode( $name )
    {
        if ( !isset( self::$modes[$name] ) )
            self::$modes[$name] = expSmartyRenderer::operatorInputMode( $name );
        return self::$modes[$name];
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
        {
            $text = var_export( $value, true );
            return $text;
        }
        return is_numeric( $value ) ? (string)$value : $this->quote( (string)$value );
    }

    private function warn( $message )
    {
        $this->warnings[] = $message;
    }
}
