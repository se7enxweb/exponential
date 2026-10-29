<?php
/**
 * Converts Smarty templates (.stpl) back to Exponential templates (.tpl) that print the same output.
 *
 * The reverse of expSmartyConverter: it reads what that converter writes, and the common hand-written Smarty
 * (variables, modifiers, functions, {if}, {foreach}, {for}, {while}, assignments, {capture}, comments). The .stpl is
 * split into text, comments and tags by a tokenizer that knows Smarty's own rules (a brace followed by white space
 * is text, {literal}, quoted strings inside tags), every tag is parsed into an expression tree, and the tree is
 * written out as .tpl:
 *
 *  - variables keep their names; $Ns__name of a converted {section name=Ns} is $Ns:name again;
 *  - modifiers and functions become operators ($x|op:a:b -> $x|op(a, b)), without the exp_ prefix the forward
 *    converter adds where Smarty has the name (exp_count -> count); the Smarty operators (==, &&, +, !, ?:) become
 *    operators too (eq(), and(), sum(), not(), cond());
 *  - {$x = ...} is {def} the first time the variable is assigned in its block and {set} after it, {$x = null} is
 *    {undef};
 *  - the shapes the forward converter writes for {foreach} parameters ($__loopN, expslice, sequence), {delimiter}
 *    (if !$item@first, $__delimN), {for} (exprange), {section} (expsectionloop, $__skipN), {switch} ($__switchN),
 *    {do} ($__doN), {set-block}, {cache-block}, {run-once}, {include} and the other template functions become those
 *    template functions again.
 *
 * Whitespace: Smarty drops the newline right after a tag that prints nothing (and the first newline of the file);
 * the template system drops the newline right after and right before every function tag. Every text is first
 * reduced to what Smarty prints, then written with the newlines the template system needs to print exactly that.
 *
 * What has no exact .tpl form (Smarty's own modifiers such as |escape, $smarty, {section} of Smarty,
 * object methods, ...) is not guessed: it is reported (warnings()) and kept in the .tpl as a comment
 * {* SMARTY (not converted: <why>): <the Smarty source> *}, which prints nothing.
 *
 * The table of every mapping is in doc/eng-us/guides/smarty-templates-converter.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expSmartyReverseConverter
{
    /** Variables the forward converter adds for itself. */
    const HELPER_PATTERN = '/^__(loop|sequence|delim|switch|do|skip)\d+$/';

    /** Smarty tags that print nothing: Smarty drops the newline right after them (as after an assignment and a block tag). */
    const EATING_TAGS = array(
        'if', 'elseif', 'else', 'foreach', 'foreachelse', 'for', 'forelse', 'while', 'break', 'continue', 'capture',
        'assign', 'append', 'section', 'sectionelse', 'strip', 'nocache', 'function', 'block', 'extends',
        'config_load', 'setfilter', 'literal',
    );

    /** Smarty tags whose body is read up to their closing tag. */
    const BLOCK_TAGS = array( 'if', 'foreach', 'for', 'while', 'capture', 'section', 'strip', 'nocache', 'function', 'block', 'setfilter' );

    /** Smarty modifiers that do exactly what a template operator does. */
    const NATIVE_MODIFIERS = array(
        'upper' => 'upcase', 'lower' => 'downcase', 'count' => 'count', 'nl2br' => 'nl2br', 'strlen' => 'strlen',
        'substr' => 'substr', 'number_format' => 'number_format', 'round' => 'round', 'cat' => 'concat',
    );

    /** PHP functions Smarty allows in expressions that do exactly what a template operator does. */
    const NATIVE_FUNCTIONS = array(
        'isset' => 'is_set', 'count' => 'count', 'sizeof' => 'count', 'is_array' => 'is_array', 'nl2br' => 'nl2br',
        'strlen' => 'strlen', 'substr' => 'substr', 'round' => 'round', 'number_format' => 'number_format',
        'time' => 'currentdate',
    );

    /** Smarty's own modifiers (expSmartyConverter::SMARTY_MODIFIERS), which a converted operator never has as its name. */
    const SMARTY_NATIVE = array(
        'capitalize', 'cat', 'count', 'count_characters', 'count_paragraphs', 'count_sentences', 'count_words',
        'date_format', 'debug_print_var', 'default', 'empty', 'escape', 'explode', 'from_charset', 'implode',
        'in_array', 'indent', 'is_array', 'isset', 'join', 'json_encode', 'lower', 'mb_wordwrap', 'nl2br',
        'noprint', 'number_format', 'raw', 'regex_replace', 'replace', 'round', 'spacify', 'split', 'str_repeat',
        'string_format', 'strip', 'strip_tags', 'strlen', 'substr', 'to_charset', 'truncate', 'unescape', 'upper',
        'wordwrap',
    );

    /** Smarty's binary operators and the template operator of each. */
    const BINARY = array(
        '==' => 'eq', '!=' => 'ne', '<>' => 'ne', 'eq' => 'eq', 'ne' => 'ne', 'neq' => 'ne',
        '<' => 'lt', 'lt' => 'lt', '>' => 'gt', 'gt' => 'gt', '<=' => 'le', 'le' => 'le', 'lte' => 'le',
        '>=' => 'ge', 'ge' => 'ge', 'gte' => 'ge', '+' => 'sum', '-' => 'sub', '*' => 'mul', '/' => 'div',
        '%' => 'mod', 'mod' => 'mod', '&&' => 'and', 'and' => 'and', '||' => 'or', 'or' => 'or',
    );

    /** Word operators of Smarty expressions. */
    const WORD_OPERATORS = array( 'and', 'or', 'xor', 'not', 'eq', 'ne', 'neq', 'gt', 'lt', 'ge', 'gte', 'le', 'lte', 'mod', 'is', 'instanceof' );

    /** Template functions the converter writes itself: never written as a generic function tag. */
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

    /** @var string the source text */
    private $source = '';

    /** @var array[] the defined variables of each open block: name => true */
    private $scopes = array();

    /** @var array[] the open loops: item, key, kind, counter, source, used, namespace */
    private $loops = array();

    /** @var string[] the namespace path of the section the converted code is in */
    private $namespace = array();

    /** @var string[] the section namespaces seen (Ns, Ns__Sub), longest first */
    private $namespaces = array();

    /** @var array expression tokens and position, while parsing */
    private $tk = array();
    private $tp = 0;

    public function __construct( ?eZTemplate $tpl = null )
    {
        $this->tpl = $tpl ?: eZTemplate::factory();
    }

    /**
     * @return string[] what could not be converted exactly, in the last convert() call
     */
    public function warnings()
    {
        return $this->warnings;
    }

    /**
     * @param string $file the .stpl file
     * @return string the .tpl text
     */
    public function convertFile( $file )
    {
        return $this->convert( file_get_contents( $file ), $file );
    }

    /**
     * @param string $text Smarty template text
     * @param string $file name used in messages
     * @return string the .tpl text
     */
    public function convert( $text, $file = 'template' )
    {
        $this->warnings = array();
        $this->file = $file;
        $this->source = (string)$text;
        $this->scopes = array( array() );
        $this->loops = array();
        $this->namespace = array();
        $this->namespaces = array();

        $tokens = $this->lex( $this->source );
        $this->effectiveText( $tokens );
        $this->findSectionNamespaces( $tokens );
        $i = 0;
        list( $nodes, $stop ) = $this->build( $tokens, $i, array() );
        $items = $this->convertNodes( $nodes );
        return $this->emit( $items );
    }

    // ----------------------------------------------------------------------------------------
    // Tokens: text, comments, tags

    /**
     * @return array[] tokens: type text|comment|tag, v (text, comment text, tag code), pos (offset), end
     */
    private function lex( $text )
    {
        $tokens = array();
        $length = strlen( $text );
        $pos = 0;
        $textStart = 0;
        while ( $pos < $length )
        {
            $brace = strpos( $text, '{', $pos );
            if ( $brace === false )
                break;
            $next = $brace + 1 < $length ? $text[$brace + 1] : '';
            // Smarty's auto literal: a brace followed by white space (or at the end) is text
            if ( $next === '' || ctype_space( $next ) )
            {
                $pos = $brace + 1;
                continue;
            }
            if ( $brace > $textStart )
                $tokens[] = array( 'type' => 'text', 'v' => substr( $text, $textStart, $brace - $textStart ), 'pos' => $textStart );
            if ( $next === '*' )
            {
                $end = strpos( $text, '*}', $brace + 2 );
                if ( $end === false )
                {
                    $this->warn( $brace, 'a comment without its end *}: the rest of the file is kept as text' );
                    $textStart = $brace;
                    $pos = $length;
                    break;
                }
                $tokens[] = array( 'type' => 'comment', 'v' => substr( $text, $brace + 2, $end - $brace - 2 ), 'pos' => $brace, 'end' => $end + 2 );
                $pos = $textStart = $end + 2;
                continue;
            }
            $end = $this->tagEnd( $text, $brace + 1 );
            if ( $end === false )
            {
                $this->warn( $brace, 'a tag without its closing brace: the rest of the file is kept as text' );
                $textStart = $brace;
                $pos = $length;
                break;
            }
            $code = substr( $text, $brace + 1, $end - $brace - 1 );
            if ( trim( $code ) === 'literal' )
            {
                $close = strpos( $text, '{/literal}', $end + 1 );
                $contentEnd = $close === false ? $length : $close;
                $tokens[] = array( 'type' => 'tag', 'v' => 'literal', 'pos' => $brace, 'end' => $end + 1, 'literal' => 'open' );
                if ( $contentEnd > $end + 1 )
                    $tokens[] = array( 'type' => 'text', 'v' => substr( $text, $end + 1, $contentEnd - $end - 1 ), 'pos' => $end + 1 );
                $after = $close === false ? $length : $close + strlen( '{/literal}' );
                $tokens[] = array( 'type' => 'tag', 'v' => '/literal', 'pos' => $contentEnd, 'end' => $after, 'literal' => 'close' );
                $pos = $textStart = $after;
                continue;
            }
            $tokens[] = array( 'type' => 'tag', 'v' => $code, 'pos' => $brace, 'end' => $end + 1 );
            $pos = $textStart = $end + 1;
        }
        if ( $textStart < $length )
            $tokens[] = array( 'type' => 'text', 'v' => substr( $text, $textStart ), 'pos' => $textStart );

        // what each tag is, which tells whether Smarty drops the newline after it
        $closed = array();
        foreach ( $tokens as $token )
        {
            if ( $token['type'] === 'tag' && isset( $token['v'][0] ) && $token['v'][0] === '/' )
                $closed[trim( substr( $token['v'], 1 ) )] = true;
        }
        foreach ( $tokens as $index => $token )
        {
            if ( $token['type'] !== 'tag' )
                continue;
            if ( isset( $token['literal'] ) )
            {
                $tokens[$index]['head'] = array( 'k' => 'literal' );
                $tokens[$index]['eats'] = $token['literal'] === 'open';
                continue;
            }
            $head = $this->tagHead( $token['v'] );
            $tokens[$index]['head'] = $head;
            switch ( $head['k'] )
            {
                case 'close':
                    $eats = $head['name'] !== 'while';
                    break;
                case 'assign':
                    $eats = true;
                    break;
                case 'named':
                    $eats = !in_array( $head['name'], array( 'ldelim', 'rdelim' ), true ) &&
                            ( in_array( $head['name'], self::EATING_TAGS, true ) || isset( $closed[$head['name']] ) );
                    break;
                default:
                    $eats = false;
            }
            $tokens[$index]['eats'] = $eats;
        }
        return $tokens;
    }

    /**
     * Position of the brace that closes a tag opened before $start, skipping quoted strings and nested braces.
     */
    private function tagEnd( $text, $start )
    {
        $length = strlen( $text );
        $depth = 0;
        for ( $i = $start; $i < $length; ++$i )
        {
            $c = $text[$i];
            if ( $c === "'" || $c === '"' )
            {
                for ( ++$i; $i < $length && $text[$i] !== $c; ++$i )
                {
                    if ( $text[$i] === '\\' )
                        ++$i;
                }
                continue;
            }
            if ( $c === '{' )
                ++$depth;
            else if ( $c === '}' )
            {
                if ( $depth === 0 )
                    return $i;
                --$depth;
            }
        }
        return false;
    }

    /**
     * What a tag's code is: close (name), assign, print (expression), named (name, rest) or raw (why).
     */
    private function tagHead( $code )
    {
        $code = trim( $code );
        if ( $code === '' )
            return array( 'k' => 'raw', 'why' => 'an empty tag' );
        if ( $code[0] === '/' )
            return array( 'k' => 'close', 'name' => trim( substr( $code, 1 ) ) );
        if ( preg_match( '/^([a-zA-Z_]\w*)(?=\s|$)/', $code, $m ) && !in_array( strtolower( $m[1] ), array( 'true', 'false', 'null' ), true ) )
            return array( 'k' => 'named', 'name' => $m[1], 'rest' => trim( substr( $code, strlen( $m[1] ) ) ) );
        try
        {
            $this->startExpression( $code );
            if ( $this->peekIs( 'v' ) )
            {
                // {$x = ...} / {$x[] = ...} / {$x.k = ...}: a variable followed by =
                $save = $this->tp;
                $target = $this->parsePrimary( true );
                $append = false;
                if ( $this->peekIs( 'p', '[' ) && $this->peekIs( 'p', ']', 1 ) )
                {
                    $this->tp += 2;
                    $append = true;
                }
                if ( $this->peekIs( 'p', '=' ) )
                {
                    ++$this->tp;
                    $value = $this->parseExpression();
                    $this->expectEnd();
                    return array( 'k' => 'assign', 'target' => $target, 'append' => $append, 'e' => $value );
                }
                $this->tp = $save;
            }
            $e = $this->parseExpression();
            $flags = array();
            while ( $this->peekIs( 'id' ) )
                $flags[] = $this->tk[$this->tp++][1];
            $this->expectEnd();
            return array( 'k' => 'print', 'e' => $e, 'flags' => $flags );
        }
        catch ( UnexpectedValueException $e )
        {
            return array( 'k' => 'raw', 'why' => $e->getMessage() );
        }
    }

    /**
     * Reduces every text to what Smarty prints of it: the newline right after a tag that prints nothing (or a
     * comment) is dropped, and the first newline of the file. 'nl' remembers that a newline was dropped.
     */
    private function effectiveText( array &$tokens )
    {
        $atStart = true;
        $previousEats = false;
        foreach ( $tokens as $index => $token )
        {
            if ( $token['type'] === 'text' )
            {
                $eat = ( $atStart ? 1 : 0 ) + ( $previousEats ? 1 : 0 );
                $text = $token['v'];
                $nl = false;
                while ( $eat-- > 0 )
                {
                    if ( strncmp( $text, "\r\n", 2 ) === 0 )
                        $text = substr( $text, 2 );
                    else if ( isset( $text[0] ) && $text[0] === "\n" )
                        $text = substr( $text, 1 );
                    else
                        break;
                    $nl = true;
                }
                $tokens[$index]['e'] = $text;
                $tokens[$index]['nl'] = $nl;
                $atStart = false;
                $previousEats = false;
            }
            else if ( $token['type'] === 'comment' )
                $previousEats = true;
            else
            {
                $atStart = false;
                $previousEats = $token['eats'];
            }
        }
    }

    /**
     * The namespaces of the sections the forward converter wrote ({$__loopN = (...)|expsectionloop:...} and
     * {foreach $__loopN as $Ns__key => ...}), known before anything is converted: a variable $Ns__name is $Ns:name
     * wherever it is.
     */
    private function findSectionNamespaces( array $tokens )
    {
        $loops = array();
        foreach ( $tokens as $token )
        {
            if ( $token['type'] === 'tag' && isset( $token['head'] ) && $token['head']['k'] === 'assign' &&
                 $token['head']['target'][0] === 'var' && preg_match( '/^__loop\d+$/', $token['head']['target'][1] ) &&
                 $token['head']['e'][0] === 'mod' && $token['head']['e'][2] === 'expsectionloop' )
                $loops[$token['head']['target'][1]] = true;
        }
        foreach ( $tokens as $token )
        {
            if ( $token['type'] === 'tag' && isset( $token['head'] ) && $token['head']['k'] === 'named' && $token['head']['name'] === 'foreach' &&
                 preg_match( '/^\$(__loop\d+)\s+as\s+\$(\w+)__key\s*=>/', $token['head']['rest'], $m ) && isset( $loops[$m[1]] ) )
                $this->addNamespace( $m[2] );
        }
    }

    // ----------------------------------------------------------------------------------------
    // The tree

    /**
     * Builds the nodes up to one of the $stops ('/if', 'else', ...), which is not consumed.
     *
     * @return array( nodes, the stop found or null )
     */
    private function build( array $tokens, &$i, array $stops )
    {
        $nodes = array();
        $count = count( $tokens );
        while ( $i < $count )
        {
            $token = $tokens[$i];
            if ( $token['type'] === 'text' )
            {
                $nodes[] = array( 'k' => 'text', 'e' => $token['e'], 'nl' => $token['nl'] );
                ++$i;
                continue;
            }
            if ( $token['type'] === 'comment' )
            {
                $nodes[] = array( 'k' => 'comment', 'v' => $token['v'] );
                ++$i;
                continue;
            }
            $head = $token['head'];
            $stopName = $head['k'] === 'close' ? '/' . $head['name'] : ( $head['k'] === 'named' ? $head['name'] : null );
            if ( $stopName !== null && in_array( $stopName, $stops, true ) )
                return array( $nodes, $stopName );
            ++$i;
            $node = $this->buildTag( $tokens, $i, $token, $head );
            if ( $node !== null )
                $nodes[] = $node;
        }
        return array( $nodes, null );
    }

    private function buildTag( array $tokens, &$i, array $token, array $head )
    {
        $pos = $token['pos'];
        switch ( $head['k'] )
        {
            case 'literal':
                return null;
            case 'close':
                return array( 'k' => 'raw', 'pos' => $pos, 'raw' => '{' . $token['v'] . '}', 'why' => "a closing tag {/{$head['name']}} without its opening tag" );
            case 'raw':
                return array( 'k' => 'raw', 'pos' => $pos, 'raw' => '{' . $token['v'] . '}', 'why' => $head['why'] );
            case 'assign':
                return array( 'k' => 'assign', 'pos' => $pos, 'raw' => '{' . $token['v'] . '}', 'target' => $head['target'], 'append' => $head['append'], 'e' => $head['e'] );
            case 'print':
                return array( 'k' => 'print', 'pos' => $pos, 'raw' => '{' . $token['v'] . '}', 'e' => $head['e'], 'flags' => $head['flags'] );
        }

        $name = $head['name'];
        $rest = $head['rest'];
        if ( $name === 'ldelim' || $name === 'rdelim' )
            return array( 'k' => 'delim', 'name' => $name );

        $isBlock = in_array( $name, self::BLOCK_TAGS, true ) || $this->hasClose( $tokens, $i, $name );
        $node = array( 'k' => 'named', 'name' => $name, 'rest' => $rest, 'pos' => $pos, 'raw' => '{' . $token['v'] . '}' );
        if ( !$isBlock )
        {
            if ( in_array( $name, array( 'elseif', 'else', 'foreachelse', 'forelse', 'sectionelse' ), true ) )
                return array( 'k' => 'raw', 'pos' => $pos, 'raw' => $node['raw'], 'why' => "{{$name}} outside of its block" );
            $node['children'] = null;
            return $node;
        }

        // the body, with its inner tags
        $branches = array();
        $current = array( 'tag' => $name, 'rest' => $rest );
        $separators = $name === 'if' ? array( 'elseif', 'else' ) : ( $name === 'foreach' ? array( 'foreachelse' ) : ( $name === 'for' ? array( 'forelse' ) : ( $name === 'section' ? array( 'sectionelse' ) : array() ) ) );
        $stops = array_merge( $separators, array( '/' . $name ) );
        $closed = false;
        while ( true )
        {
            list( $children, $stop ) = $this->build( $tokens, $i, $stops );
            $current['children'] = $children;
            $branches[] = $current;
            if ( $stop === null )
                break;
            $stopToken = $tokens[$i];
            ++$i;
            if ( $stop === '/' . $name )
            {
                $closed = true;
                $node['end'] = $stopToken['end'];
                break;
            }
            $current = array( 'tag' => $stop, 'rest' => $stopToken['head']['rest'] );
        }
        if ( !$closed )
        {
            $node['end'] = strlen( $this->source );
            $this->warn( $pos, "{{$name}} without its closing tag {/{$name}}" );
        }
        $node['branches'] = $branches;
        $node['children'] = $branches[0]['children'];
        return $node;
    }

    private function hasClose( array $tokens, $i, $name )
    {
        $depth = 0;
        for ( $count = count( $tokens ); $i < $count; ++$i )
        {
            if ( $tokens[$i]['type'] !== 'tag' || !isset( $tokens[$i]['head'] ) )
                continue;
            $head = $tokens[$i]['head'];
            if ( $head['k'] === 'named' && $head['name'] === $name )
                ++$depth;
            else if ( $head['k'] === 'close' && $head['name'] === $name )
            {
                if ( $depth === 0 )
                    return true;
                --$depth;
            }
        }
        return false;
    }

    // ----------------------------------------------------------------------------------------
    // Expressions: tokens and parser

    private function startExpression( $code )
    {
        $this->tk = $this->expressionTokens( $code );
        $this->tp = 0;
    }

    /**
     * @return array[] array( kind, value ): v (variable name), id, n (number), s (string value), d (double-quoted
     *                 raw), p (punctuation)
     */
    private function expressionTokens( $code )
    {
        $tokens = array();
        $length = strlen( $code );
        $i = 0;
        while ( $i < $length )
        {
            $c = $code[$i];
            if ( ctype_space( $c ) )
            {
                ++$i;
                continue;
            }
            if ( $c === '$' && preg_match( '/\G\$([a-zA-Z_]\w*)/', $code, $m, 0, $i ) )
            {
                $tokens[] = array( 'v', $m[1] );
                $i += strlen( $m[0] );
                continue;
            }
            if ( $c === "'" || $c === '"' )
            {
                $value = '';
                $raw = '';
                for ( $j = $i + 1; $j < $length && $code[$j] !== $c; ++$j )
                {
                    if ( $code[$j] === '\\' && $j + 1 < $length )
                    {
                        $raw .= $code[$j] . $code[$j + 1];
                        // single quotes: only \' and \\ are escapes
                        $value .= ( $code[$j + 1] === "'" || $code[$j + 1] === '\\' ) ? $code[$j + 1] : $code[$j] . $code[$j + 1];
                        ++$j;
                        continue;
                    }
                    $raw .= $code[$j];
                    $value .= $code[$j];
                }
                if ( $j >= $length )
                    throw new UnexpectedValueException( 'a string without its closing quote' );
                $tokens[] = $c === "'" ? array( 's', $value ) : array( 'd', $raw );
                $i = $j + 1;
                continue;
            }
            if ( ctype_digit( $c ) && preg_match( '/\G\d+(\.\d+)?/', $code, $m, 0, $i ) )
            {
                $tokens[] = array( 'n', $m[0] );
                $i += strlen( $m[0] );
                continue;
            }
            if ( preg_match( '/\G[a-zA-Z_]\w*/', $code, $m, 0, $i ) )
            {
                $tokens[] = array( 'id', $m[0] );
                $i += strlen( $m[0] );
                continue;
            }
            foreach ( array( '===', '!==', '==', '!=', '<>', '<=', '>=', '&&', '||', '=>', '->', '::' ) as $op )
            {
                if ( substr_compare( $code, $op, $i, strlen( $op ) ) === 0 )
                {
                    $tokens[] = array( 'p', $op );
                    $i += strlen( $op );
                    continue 2;
                }
            }
            if ( strpos( '()[],.|:=<>!+-*/%?@#`', $c ) !== false )
            {
                $tokens[] = array( 'p', $c );
                ++$i;
                continue;
            }
            throw new UnexpectedValueException( "the character '$c' is not Smarty syntax the converter reads" );
        }
        return $tokens;
    }

    private function peekIs( $kind, $value = null, $offset = 0 )
    {
        $index = $this->tp + $offset;
        if ( !isset( $this->tk[$index] ) || $this->tk[$index][0] !== $kind )
            return false;
        return $value === null || $this->tk[$index][1] === $value;
    }

    private function expect( $kind, $value = null )
    {
        if ( !$this->peekIs( $kind, $value ) )
            throw new UnexpectedValueException( 'expected ' . ( $value !== null ? "'$value'" : $kind ) . $this->near() );
        return $this->tk[$this->tp++][1];
    }

    private function expectEnd()
    {
        if ( $this->tp < count( $this->tk ) )
            throw new UnexpectedValueException( 'unexpected ' . $this->tk[$this->tp][1] . $this->near() );
    }

    private function near()
    {
        return isset( $this->tk[$this->tp] ) ? " at '" . $this->tk[$this->tp][1] . "'" : ' at the end';
    }

    /** A word operator that is followed by = is an attribute name, not an operator. */
    private function peekWordOperator( array $words )
    {
        return $this->peekIs( 'id' ) && in_array( strtolower( $this->tk[$this->tp][1] ), $words, true ) && !$this->peekIs( 'p', '=', 1 );
    }

    public function parseExpression()
    {
        $condition = $this->parseBinary( 0 );
        if ( $this->peekIs( 'p', '?' ) )
        {
            ++$this->tp;
            $then = $this->parseExpression();
            $this->expect( 'p', ':' );
            $else = $this->parseExpression();
            return array( 'tern', $condition, $then, $else );
        }
        return $condition;
    }

    /** Binary operators by precedence, lowest first. */
    private static function levels()
    {
        return array(
            array( '||', 'or' ), array( 'xor' ), array( '&&', 'and' ),
            array( '==', '!=', '<>', '===', '!==', 'eq', 'ne', 'neq' ),
            array( '<', '>', '<=', '>=', 'lt', 'gt', 'le', 'ge', 'lte', 'gte', 'is', 'instanceof' ),
            array( '+', '-' ), array( '*', '/', '%', 'mod' ),
        );
    }

    private function parseBinary( $level )
    {
        $levels = self::levels();
        if ( $level >= count( $levels ) )
            return $this->parseUnary();
        $left = $this->parseBinary( $level + 1 );
        while ( true )
        {
            $op = null;
            if ( $this->peekIs( 'p' ) && in_array( $this->tk[$this->tp][1], $levels[$level], true ) )
                $op = $this->tk[$this->tp][1];
            else if ( $this->peekWordOperator( $levels[$level] ) )
                $op = strtolower( $this->tk[$this->tp][1] );
            if ( $op === null )
                return $left;
            ++$this->tp;
            if ( $op === 'is' || $op === 'instanceof' || $op === 'xor' )
                throw new UnexpectedValueException( "the operator '$op' has no template operator" );
            $right = $this->parseBinary( $level + 1 );
            $left = array( 'bin', $op, $left, $right );
        }
    }

    private function parseUnary()
    {
        if ( $this->peekIs( 'p', '!' ) || $this->peekWordOperator( array( 'not' ) ) )
        {
            ++$this->tp;
            return array( 'un', '!', $this->parseUnary() );
        }
        if ( $this->peekIs( 'p', '-' ) )
        {
            ++$this->tp;
            if ( $this->peekIs( 'n' ) )
                return $this->parseModifiers( array( 'num', '-' . $this->tk[$this->tp++][1] ) );
            return array( 'un', '-', $this->parseUnary() );
        }
        if ( $this->peekIs( 'p', '+' ) )
        {
            ++$this->tp;
            return $this->parseUnary();
        }
        return $this->parseModifiers( $this->parsePrimary() );
    }

    private function parseModifiers( $value )
    {
        while ( $this->peekIs( 'p', '|' ) )
        {
            ++$this->tp;
            if ( $this->peekIs( 'p', '@' ) )
                ++$this->tp;
            $name = $this->expect( 'id' );
            $args = array();
            while ( $this->peekIs( 'p', ':' ) )
            {
                ++$this->tp;
                $args[] = $this->parseModifierArgument();
            }
            $value = array( 'mod', $value, $name, $args );
        }
        return $value;
    }

    private function parseModifierArgument()
    {
        if ( $this->peekIs( 'p', '-' ) && $this->peekIs( 'n', null, 1 ) )
        {
            ++$this->tp;
            return array( 'num', '-' . $this->tk[$this->tp++][1] );
        }
        if ( $this->peekIs( 'p', '!' ) )
        {
            ++$this->tp;
            return array( 'un', '!', $this->parseModifierArgument() );
        }
        return $this->parsePrimary();
    }

    /**
     * @param bool $variableOnly a variable with its lookups only (the target of an assignment)
     */
    private function parsePrimary( $variableOnly = false )
    {
        if ( !isset( $this->tk[$this->tp] ) )
            throw new UnexpectedValueException( 'a value was expected' . $this->near() );
        list( $kind, $value ) = $this->tk[$this->tp++];
        if ( $variableOnly && $kind !== 'v' )
            throw new UnexpectedValueException( 'a variable was expected' );
        switch ( $kind )
        {
            case 'v':
                return $this->parseVariable( $value, $variableOnly );
            case 'n':
                return array( 'num', $value );
            case 's':
                return array( 'str', $value );
            case 'd':
                return $this->doubleQuoted( $value );
            case 'id':
                $lower = strtolower( $value );
                if ( $this->peekIs( 'p', '(' ) )
                {
                    ++$this->tp;
                    $args = array();
                    if ( !$this->peekIs( 'p', ')' ) )
                    {
                        $args[] = $this->parseExpression();
                        while ( $this->peekIs( 'p', ',' ) )
                        {
                            ++$this->tp;
                            $args[] = $this->parseExpression();
                        }
                    }
                    $this->expect( 'p', ')' );
                    return array( 'call', $value, $args );
                }
                if ( $lower === 'true' || $lower === 'false' )
                    return array( 'bool', $lower === 'true' );
                if ( $lower === 'null' )
                    return array( 'null' );
                if ( $this->peekIs( 'p', '::' ) )
                    throw new UnexpectedValueException( 'a static class member has no template form' );
                throw new UnexpectedValueException( "the bare name '$value' (a constant) has no template form" );
            case 'p':
                if ( $value === '(' )
                {
                    $e = $this->parseExpression();
                    $this->expect( 'p', ')' );
                    return $e;
                }
                if ( $value === '[' )
                {
                    $items = array();
                    while ( !$this->peekIs( 'p', ']' ) )
                    {
                        $first = $this->parseExpression();
                        if ( $this->peekIs( 'p', '=>' ) )
                        {
                            ++$this->tp;
                            $items[] = array( $first, $this->parseExpression() );
                        }
                        else
                            $items[] = array( null, $first );
                        if ( !$this->peekIs( 'p', ',' ) )
                            break;
                        ++$this->tp;
                    }
                    $this->expect( 'p', ']' );
                    return array( 'array', $items );
                }
                if ( $value === '#' )
                    throw new UnexpectedValueException( 'a config variable (#name#) has no template form' );
                if ( $value === '`' )
                    throw new UnexpectedValueException( 'a backtick expression outside of a string' );
        }
        throw new UnexpectedValueException( "unexpected '$value'" );
    }

    private function parseVariable( $name, $variableOnly )
    {
        $path = array();
        while ( true )
        {
            if ( $this->peekIs( 'p', '.' ) )
            {
                ++$this->tp;
                if ( $this->peekIs( 'id' ) )
                    $path[] = array( '.', $this->tk[$this->tp++][1] );
                else if ( $this->peekIs( 'n' ) )
                {
                    foreach ( explode( '.', $this->tk[$this->tp++][1] ) as $part )
                        $path[] = array( '.', $part );
                }
                else if ( $this->peekIs( 'v' ) )
                    $path[] = array( '[', array( 'var', $this->tk[$this->tp++][1], array() ) );
                else
                    throw new UnexpectedValueException( 'a name was expected after .' );
                continue;
            }
            if ( $this->peekIs( 'p', '[' ) && !( $variableOnly && $this->peekIs( 'p', ']', 1 ) ) )
            {
                ++$this->tp;
                $path[] = array( '[', $this->parseExpression() );
                $this->expect( 'p', ']' );
                continue;
            }
            if ( $this->peekIs( 'p', '->' ) )
            {
                ++$this->tp;
                $member = $this->expect( 'id' );
                if ( $this->peekIs( 'p', '(' ) )
                    throw new UnexpectedValueException( 'an object method call has no template form' );
                $path[] = array( '.', $member );
                continue;
            }
            if ( !$variableOnly && $this->peekIs( 'p', '@' ) )
            {
                ++$this->tp;
                $property = $this->expect( 'id' );
                if ( $path )
                    throw new UnexpectedValueException( 'a loop property of a variable lookup' );
                return array( 'prop', $name, $property );
            }
            break;
        }
        return array( 'var', $name, $path );
    }

    /**
     * A double-quoted string: its text, $variables and {expressions} or `expressions` inside it.
     */
    private function doubleQuoted( $raw )
    {
        $parts = array();
        $text = '';
        $length = strlen( $raw );
        for ( $i = 0; $i < $length; ++$i )
        {
            $c = $raw[$i];
            if ( $c === '\\' && $i + 1 < $length )
            {
                $map = array( 'n' => "\n", 't' => "\t", 'r' => "\r", '\\' => '\\', '"' => '"', '$' => '$', 'v' => "\v", 'e' => "\x1b", 'f' => "\f" );
                $text .= isset( $map[$raw[$i + 1]] ) ? $map[$raw[$i + 1]] : $c . $raw[$i + 1];
                ++$i;
                continue;
            }
            $inner = null;
            if ( $c === '$' && preg_match( '/\G\$[a-zA-Z_]\w*/', $raw, $m, 0, $i ) )
            {
                $inner = $m[0];
                $i += strlen( $m[0] ) - 1;
            }
            else if ( ( $c === '`' && ( $close = strpos( $raw, '`', $i + 1 ) ) !== false ) ||
                      ( $c === '{' && isset( $raw[$i + 1] ) && $raw[$i + 1] === '$' && ( $close = $this->tagEnd( $raw, $i + 1 ) ) !== false ) )
            {
                $inner = substr( $raw, $i + 1, $close - $i - 1 );
                $i = $close;
            }
            if ( $inner === null )
            {
                $text .= $c;
                continue;
            }
            if ( $text !== '' )
                $parts[] = array( 'str', $text );
            $text = '';
            $saved = array( $this->tk, $this->tp );
            $this->startExpression( $inner );
            $parts[] = $this->parseExpression();
            $this->expectEnd();
            list( $this->tk, $this->tp ) = $saved;
        }
        if ( $text !== '' || !$parts )
            $parts[] = array( 'str', $text );
        return count( $parts ) === 1 && $parts[0][0] === 'str' ? $parts[0] : array( 'call', 'concat', $parts );
    }

    /**
     * Parses tag attributes: name=value pairs, bare names (true) and positional values (numeric keys).
     */
    private function parseAttributes( $rest )
    {
        $this->startExpression( $rest );
        $attributes = array();
        $count = count( $this->tk );
        while ( $this->tp < $count )
        {
            if ( $this->peekIs( 'id' ) && $this->peekIs( 'p', '=', 1 ) )
            {
                $name = $this->tk[$this->tp][1];
                $this->tp += 2;
                // a bare word value is a string (assign=name)
                if ( $this->peekIs( 'id' ) && !$this->peekIs( 'p', '(', 1 ) && !$this->peekIs( 'p', '::', 1 ) &&
                     !in_array( strtolower( $this->tk[$this->tp][1] ), array( 'true', 'false', 'null' ), true ) )
                {
                    $attributes[$name] = array( 'str', $this->tk[$this->tp++][1] );
                    continue;
                }
                $attributes[$name] = $this->parseExpression();
                continue;
            }
            if ( $this->peekIs( 'id' ) && !$this->peekIs( 'p', '(', 1 ) && !in_array( strtolower( $this->tk[$this->tp][1] ), array( 'true', 'false', 'null' ), true ) )
            {
                $attributes[$this->tk[$this->tp][1]] = array( 'bool', true );
                ++$this->tp;
                continue;
            }
            $attributes[] = $this->parseExpression();
        }
        return $attributes;
    }

    // ----------------------------------------------------------------------------------------
    // Expressions written as .tpl

    /**
     * @param array $e an expression tree
     * @return string the template expression
     * @throws UnexpectedValueException when it has no exact template form
     */
    private function expr( array $e )
    {
        switch ( $e[0] )
        {
            case 'num':
                return $e[1];
            case 'str':
                return $this->quote( $e[1] );
            case 'bool':
                return $e[1] ? 'true()' : 'false()';
            case 'null':
                return 'null()';
            case 'var':
                return $this->variable( $e[1], $e[2] );
            case 'prop':
                return $this->loopProperty( $e[1], $e[2] );
            case 'array':
                return $this->arrayLiteral( $e[1] );
            case 'call':
                return $this->call( $e[1], $e[2] );
            case 'mod':
                return $this->modifier( $e[1], $e[2], $e[3] );
            case 'un':
                if ( $e[1] === '!' )
                    return 'not(' . $this->expr( $e[2] ) . ')';
                return 'sub(0, ' . $this->expr( $e[2] ) . ')';
            case 'bin':
                return $this->binary( $e[1], $e[2], $e[3] );
            case 'tern':
                return 'cond(' . $this->expr( $e[1] ) . ', ' . $this->expr( $e[2] ) . ', ' . $this->expr( $e[3] ) . ')';
        }
        throw new UnexpectedValueException( 'an unknown expression' );
    }

    private function binary( $op, array $left, array $right )
    {
        if ( $op === '===' || $op === '!==' )
            throw new UnexpectedValueException( "the strict comparison $op has no template operator (eq() and ne() compare loosely)" );
        $name = self::BINARY[$op];
        $args = array();
        // a && b && c: and(a, b, c)
        foreach ( array( $left, $right ) as $side )
        {
            if ( in_array( $name, array( 'and', 'or', 'sum', 'mul' ), true ) && $side[0] === 'bin' && isset( self::BINARY[$side[1]] ) &&
                 self::BINARY[$side[1]] === $name && $side === $left )
            {
                $inner = $this->binary( $side[1], $side[2], $side[3] );
                $args[] = substr( $inner, strlen( $name ) + 1, -1 );
                continue;
            }
            $args[] = $this->expr( $side );
        }
        return $name . '(' . implode( ', ', $args ) . ')';
    }

    private function arrayLiteral( array $items )
    {
        if ( !$items )
            return 'array()';
        $keyed = 0;
        foreach ( $items as $item )
        {
            if ( $item[0] !== null )
                ++$keyed;
        }
        if ( $keyed === 0 )
            return 'array(' . implode( ', ', array_map( function ( $item ) { return $this->expr( $item[1] ); }, $items ) ) . ')';
        if ( $keyed !== count( $items ) )
            throw new UnexpectedValueException( 'an array with some keys and some without has no template form' );
        $pairs = array();
        foreach ( $items as $item )
            $pairs[] = $this->expr( $item[0] ) . ', ' . $this->expr( $item[1] );
        return 'hash(' . implode( ', ', $pairs ) . ')';
    }

    /**
     * A variable: its name, with the section namespace it was converted from, and its lookups.
     */
    private function variable( $name, array $path )
    {
        if ( $name === 'smarty' )
            throw new UnexpectedValueException( '$smarty has no template form' );
        $code = $this->variableName( $name );
        foreach ( $path as $step )
            $code .= $this->lookup( $step );
        return $code;
    }

    private function lookup( array $step )
    {
        // a number as [0], a name as .name: the forms the template system's own templates use
        if ( $step[0] === '.' )
            return ctype_digit( (string)$step[1] ) ? '[' . $step[1] . ']' : '.' . $step[1];
        $key = $step[1];
        if ( $key[0] === 'str' && preg_match( '/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key[1] ) )
            return '.' . $key[1];
        return '[' . $this->expr( $key ) . ']';
    }

    /**
     * The template variable of a Smarty variable name: $Ns__name (a variable of the namespace Ns, as the forward
     * converter writes it) is $Ns:name.
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
        // any other Ns__name: a variable of a namespace whose section the converter did not see (a {section name=Ns}
        // without a loop, or a namespace the view sets), never a helper ($__loop1) or a name with a stray underscore
        $last = strrpos( $name, '__' );
        if ( $last !== false && $last > 0 && preg_match( '/^[A-Za-z0-9]+(?:_[A-Za-z0-9]+)*(?:__[A-Za-z0-9]+(?:_[A-Za-z0-9]+)*)*$/', substr( $name, 0, $last ) ) &&
             preg_match( '/^[A-Za-z][A-Za-z0-9_]*$/', substr( $name, $last + 2 ) ) )
            return array( substr( $name, 0, $last ), substr( $name, $last + 2 ) );
        return array( '', $name );
    }

    private function addNamespace( $namespace )
    {
        if ( $namespace === '' || in_array( $namespace, $this->namespaces, true ) )
            return;
        $this->namespaces[] = $namespace;
        usort( $this->namespaces, function ( $a, $b ) { return strlen( $b ) - strlen( $a ); } );
    }

    /**
     * $item@index and the other loop properties.
     */
    private function loopProperty( $item, $property )
    {
        for ( $i = count( $this->loops ) - 1; $i >= 0; --$i )
        {
            $loop = $this->loops[$i];
            if ( $loop['item'] !== $item )
                continue;
            if ( $loop['kind'] === 'section' )
            {
                $prefix = $loop['namespace'] !== '' ? $loop['namespace'] . '__' : '';
                switch ( $property )
                {
                    case 'index':
                        return $this->variableName( $prefix . 'index' );
                    case 'iteration':
                        return $this->variableName( $prefix . 'number' );
                    case 'first':
                        return 'eq(' . $this->variableName( $prefix . 'index' ) . ', 0)';
                    case 'key':
                        return $this->variableName( $prefix . 'key' );
                }
                throw new UnexpectedValueException( "the loop property @$property of a section has no template form" );
            }
            $counter = '$' . $loop['counter'];
            switch ( $property )
            {
                case 'index':
                    $this->loops[$i]['used'] = true;
                    return $counter;
                case 'iteration':
                    $this->loops[$i]['used'] = true;
                    return $counter . '|inc';
                case 'first':
                    $this->loops[$i]['used'] = true;
                    return 'eq(' . $counter . ', 0)';
                case 'last':
                    $this->loops[$i]['used'] = true;
                    $this->loops[$i]['usedSource'] = true;
                    return 'eq(' . $counter . '|inc, count(' . $loop['sourceRef'] . '))';
                case 'total':
                    $this->loops[$i]['usedSource'] = true;
                    return 'count(' . $loop['sourceRef'] . ')';
                case 'key':
                    if ( $loop['key'] !== null )
                        return $this->variableName( $loop['key'] );
                    throw new UnexpectedValueException( '@key of a {foreach} without a key variable' );
            }
            throw new UnexpectedValueException( "the loop property @$property has no template form" );
        }
        throw new UnexpectedValueException( "\$$item@$property outside of the loop of \$$item" );
    }

    /**
     * A function call: an operator without input.
     */
    private function call( $name, array $args )
    {
        switch ( $name )
        {
            case 'expvar':
                if ( count( $args ) === 2 && $args[0][0] === 'str' && $args[1][0] === 'str' )
                    return '$#' . ( $args[1][1] !== '' ? $args[1][1] . ':' : '' ) . $args[0][1];
                throw new UnexpectedValueException( 'expvar() with values that are not strings' );
            case 'expvoid':
            case 'exprange':
            case 'expslice':
            case 'expsectionloop':
            case 'expsequence':
            case 'expcolumn':
            case 'expattr':
                throw new UnexpectedValueException( "$name() outside of the code the tpl -> stpl converter writes for it" );
            case 'in_array':
                if ( count( $args ) === 2 )
                    return $this->pipe( $this->expr( $args[1] ) ) . '|contains(' . $this->expr( $args[0] ) . ')';
                break;
            case 'hash':
            case 'array':
                return $name . '(' . $this->arguments( $args ) . ')';
        }
        $operator = $this->operatorName( $name, self::NATIVE_FUNCTIONS );
        // an operator that reads its input, called first in an expression: the forward converter wrote op(null, ...);
        // an operator that reads its parameters only takes the same form as op(...) (expSmartyRenderer::callOperator())
        if ( $args && $args[0][0] === 'null' &&
             ( $this->inputMode( $operator ) === 'input' || ( class_exists( 'expSmartyRenderer' ) && expSmartyRenderer::isParameterOperator( $operator ) ) ) )
            array_shift( $args );
        return $operator . '(' . $this->arguments( $args ) . ')';
    }

    /**
     * The parameters of an operator. expvoid() is a parameter left empty in the .tpl (i18n( 'context',, hash() )).
     */
    private function arguments( array $args )
    {
        $codes = array();
        foreach ( $args as $arg )
            $codes[] = $arg[0] === 'call' && $arg[1] === 'expvoid' && !$arg[2] ? '' : $this->expr( $arg );
        return implode( ', ', $codes );
    }

    private function modifier( array $input, $name, array $args )
    {
        if ( $name === 'expattr' )
        {
            if ( count( $args ) !== 1 )
                throw new UnexpectedValueException( 'expattr with ' . count( $args ) . ' parameters' );
            // after an operator, .0 would be the name '0': a number is looked up as [0]
            if ( $args[0][0] === 'num' )
                return $this->pipe( $this->expr( $input ) ) . '[' . $args[0][1] . ']';
            return $this->pipe( $this->expr( $input ) ) . $this->lookup( array( '[', $args[0] ) );
        }
        // Smarty's default: the value, or the default when the value is null or '' (DefaultModifierCompiler)
        if ( $name === 'default' && count( $args ) <= 1 )
        {
            $default = $args ? $this->expr( $args[0] ) : "''";
            // a literal is decided here: the template compiler cannot take is_set() of a literal
            if ( in_array( $input[0], array( 'str', 'num', 'bool', 'null' ), true ) )
                return ( $input[0] === 'null' || ( $input[0] === 'str' && $input[1] === '' ) ) ? $default : $this->expr( $input );
            $value = $this->expr( $input );
            return 'cond( or( not( is_set( ' . $value . ' ) ), eq( ' . $value . ", '' ) ), " . $default . ', ' . $value . ' )';
        }
        if ( $name === 'in_array' && count( $args ) === 1 )
            return $this->pipe( $this->expr( $args[0] ) ) . '|contains(' . $this->expr( $input ) . ')';
        if ( in_array( $name, array( 'expvoid', 'expvar', 'exprange', 'expslice', 'expsectionloop', 'expsequence', 'expcolumn' ), true ) )
            throw new UnexpectedValueException( "|$name outside of the code the tpl -> stpl converter writes for it" );
        $operator = $this->operatorName( $name, self::NATIVE_MODIFIERS );
        $code = $this->pipe( $this->expr( $input ) ) . '|' . $operator;
        if ( $args )
            $code .= '(' . $this->arguments( $args ) . ')';
        return $code;
    }

    /**
     * A value an operator is applied to: every template expression the converter writes is one term.
     */
    private function pipe( $code )
    {
        return $code;
    }

    /**
     * The template operator of a Smarty modifier or function name.
     */
    private function operatorName( $name, array $natives )
    {
        if ( strncmp( $name, 'exp_', 4 ) === 0 && strlen( $name ) > 4 )
            return substr( $name, 4 );
        if ( in_array( $name, self::SMARTY_NATIVE, true ) || isset( $natives[$name] ) )
        {
            if ( isset( $natives[$name] ) )
                return $natives[$name];
            throw new UnexpectedValueException( "Smarty's $name has no exact template operator" );
        }
        if ( isset( $this->tpl->Operators[$name] ) )
            return $name;
        throw new UnexpectedValueException( "$name is neither a template operator nor an operator of Smarty the converter knows" );
    }

    private function inputMode( $name )
    {
        if ( !isset( $this->tpl->Operators[$name] ) || !class_exists( 'expSmartyRenderer' ) )
            return 'input';
        return expSmartyRenderer::operatorInputMode( $name );
    }

    /**
     * A string literal of the template language (which reads \n, \r, \t, \' and \" as escapes, and nothing else).
     */
    private function quote( $string )
    {
        $string = (string)$string;
        if ( preg_match( '/\\\\([nrt\'"]|$)/', $string ) )
            throw new UnexpectedValueException( 'a string with a backslash the template language would read as an escape' );
        return "'" . str_replace( array( "'", "\n", "\r", "\t" ), array( "\\'", '\n', '\r', '\t' ), $string ) . "'";
    }

    // ----------------------------------------------------------------------------------------
    // Nodes written as .tpl: a flat list of items
    //   array( 'T', text, nl )   text, as Smarty prints it
    //   array( 'V', code )       {expression}
    //   array( 'F', code )       {function tag}, opening, closing or single
    //   array( 'C', text )       {* comment *}

    private function convertNodes( array $nodes )
    {
        $items = array();
        $count = count( $nodes );
        for ( $i = 0; $i < $count; ++$i )
        {
            $node = $nodes[$i];

            // {skip}: the forward converter writes {$__delimN = false}{continue}
            if ( $node['k'] === 'assign' && ( $flag = $this->helperName( $node ) ) !== null && $this->isFalse( $node['e'] ) &&
                 ( $loop = end( $this->loops ) ) && isset( $loop['delimFlag'] ) && $loop['delimFlag'] === $flag )
            {
                $j = $this->nextNode( $nodes, $i + 1 );
                if ( $j !== null && $nodes[$j]['k'] === 'named' && $nodes[$j]['name'] === 'continue' )
                {
                    $items[] = array( 'F', 'skip' );
                    $i = $j;
                    continue;
                }
            }

            // {default}: the rest of the block is its body
            if ( $node['k'] === 'named' && $node['name'] === 'if' && ( $default = $this->defaultBlock( $nodes, $i ) ) !== null )
            {
                $items = array_merge( $items, $default[0] );
                $i = $default[1];
                continue;
            }

            // helper assignments before the loop or switch that uses them
            if ( $node['k'] === 'assign' && $this->helperName( $node ) !== null )
            {
                $prelude = array();
                $j = $i;
                while ( $j < $count )
                {
                    $n = $nodes[$j];
                    if ( $n['k'] === 'text' && $n['e'] === '' )
                    {
                        ++$j;
                        continue;
                    }
                    if ( $n['k'] === 'assign' && ( $helper = $this->helperName( $n ) ) !== null && !isset( $prelude[$helper] ) )
                    {
                        $prelude[$helper] = $n;
                        ++$j;
                        continue;
                    }
                    break;
                }
                $result = $j < $count ? $this->convertStructure( $nodes[$j], $prelude ) : null;
                if ( $result !== null )
                {
                    list( $structureItems, $consumed ) = $result;
                    foreach ( $prelude as $helper => $n )
                    {
                        if ( !in_array( $helper, $consumed, true ) )
                            $items = array_merge( $items, $this->convertNode( $n ) );
                    }
                    $items = array_merge( $items, $structureItems );
                    $i = $j;
                    continue;
                }
            }
            $items = array_merge( $items, $this->convertNode( $node ) );
        }
        return $items;
    }

    /** The helper variable an assignment sets ($__loop1 ...), or null. */
    private function helperName( array $node )
    {
        if ( $node['k'] !== 'assign' || $node['append'] || $node['target'][0] !== 'var' || $node['target'][2] )
            return null;
        return preg_match( self::HELPER_PATTERN, $node['target'][1] ) ? $node['target'][1] : null;
    }

    private function nextNode( array $nodes, $j )
    {
        for ( $count = count( $nodes ); $j < $count; ++$j )
        {
            if ( $nodes[$j]['k'] !== 'text' || $nodes[$j]['e'] !== '' )
                return $j;
        }
        return null;
    }

    /** The nodes without the empty texts (what is left between two tags that print nothing). */
    private function statements( array $nodes )
    {
        return array_values( array_filter( $nodes, function ( $n ) { return $n['k'] !== 'text' || $n['e'] !== ''; } ) );
    }

    private function isFalse( array $e )
    {
        return $e[0] === 'bool' && $e[1] === false;
    }

    private function isTrue( array $e )
    {
        return $e[0] === 'bool' && $e[1] === true;
    }

    private function isVariable( array $e, $name )
    {
        return $e[0] === 'var' && $e[1] === $name && !$e[2];
    }

    private function isProperty( array $e, $item, $property )
    {
        return $e[0] === 'prop' && $e[1] === $item && $e[2] === $property;
    }

    /**
     * A loop, switch or do that uses helper assignments before it.
     *
     * @return array|null array( items, the helpers it used ), null when it is not one
     */
    private function convertStructure( array $node, array $prelude )
    {
        if ( $node['k'] !== 'named' )
            return null;
        try
        {
            if ( $node['name'] === 'foreach' )
                return $this->foreachTag( $node, $prelude );
            if ( $node['name'] === 'if' )
                return $this->switchTag( $node, $prelude );
            if ( $node['name'] === 'while' )
                return $this->doTag( $node, $prelude );
        }
        catch ( UnexpectedValueException $e )
        {
            return array( $this->unconverted( $node, $e->getMessage() ), array() );
        }
        return null;
    }

    private function convertNode( array $node )
    {
        switch ( $node['k'] )
        {
            case 'text':
                return array( array( 'T', $node['e'], $node['nl'] ) );
            case 'delim':
                // {ldelim} and {rdelim} of Smarty are the template functions of the same name
                return array( array( 'F', $node['name'] ) );
            case 'comment':
                return array( array( 'C', $node['v'] ) );
            case 'raw':
                return $this->unconverted( $node, $node['why'] );
        }
        try
        {
            switch ( $node['k'] )
            {
                case 'print':
                    return array( array( 'V', $this->expr( $node['e'] ) ) );
                case 'assign':
                    return $this->assignment( $node['target'], $node['e'], $node['append'] );
                case 'named':
                    return $this->namedTag( $node );
            }
        }
        catch ( UnexpectedValueException $e )
        {
            return $this->unconverted( $node, $e->getMessage() );
        }
        return $this->unconverted( $node, 'an unknown tag' );
    }

    /**
     * What could not be converted: a note, and the Smarty source as a comment (of the whole block, for a block).
     */
    private function unconverted( array $node, $why )
    {
        $pos = isset( $node['pos'] ) ? $node['pos'] : 0;
        $raw = isset( $node['end'] ) ? substr( $this->source, $pos, $node['end'] - $pos ) : ( isset( $node['raw'] ) ? $node['raw'] : '' );
        $this->warn( $pos, $this->shorten( $raw ) . ": $why; kept as a comment" );
        return array( array( 'C', ' SMARTY (not converted: ' . str_replace( '*}', '* }', $why ) . '): ' . str_replace( '*}', '* }', $raw ) . ' ' ) );
    }

    private function shorten( $raw )
    {
        $raw = preg_replace( '/\s+/', ' ', $raw );
        return strlen( $raw ) > 80 ? substr( $raw, 0, 77 ) . '...' : $raw;
    }

    // ----------------------------------------------------------------------------------------
    // Variables and their blocks

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

    /** Undefined in every open block: after it, nothing may count on the variable. */
    private function undefine( $name )
    {
        foreach ( array_keys( $this->scopes ) as $i )
            unset( $this->scopes[$i][$name] );
    }

    private function assignment( array $target, array $e, $append )
    {
        if ( $target[0] !== 'var' || $target[2] )
            throw new UnexpectedValueException( 'an assignment to an element of an array has no template form' );
        $name = $target[1];
        list( $namespace, $base ) = $this->splitNamespace( $name );
        if ( $namespace !== '' )
        {
            if ( str_replace( '__', ':', $namespace ) !== implode( ':', $this->namespace ) )
                throw new UnexpectedValueException( "an assignment to \$$name, a variable of the section $namespace, outside of it" );
            if ( $e[0] === 'null' || $append )
                throw new UnexpectedValueException( "the variable \$$name of a section cannot be unset or appended to" );
            return array( array( 'F', 'set $' . $base . ' = ' . $this->expr( $e ) ) );
        }
        if ( $e[0] === 'null' && !$append )
        {
            $this->undefine( $name );
            return array( array( 'F', 'undef $' . $name ) );
        }
        $value = $this->expr( $e );
        // inside a section, {set} sets in the section's namespace: a variable of the template is set with {def}
        $verb = $this->isDefined( $name ) && !$this->namespace ? 'set' : 'def';
        if ( $append )
            $value = $verb === 'set' ? '$' . $name . '|append(' . $value . ')' : 'array(' . $value . ')';
        $this->define( $name );
        return array( array( 'F', $verb . ' $' . $name . ' = ' . $value ) );
    }

    // ----------------------------------------------------------------------------------------
    // Tags

    private function namedTag( array $node )
    {
        $name = $node['name'];
        switch ( $name )
        {
            case 'if':
                return $this->ifTag( $node );
            case 'foreach':
                list( $items ) = $this->foreachTag( $node, array() );
                return $items;
            case 'for':
                return $this->forTag( $node );
            case 'while':
                list( $items ) = $this->doTag( $node, array() ) ?: $this->whileTag( $node );
                return $items;
            case 'break':
            case 'continue':
                if ( $node['rest'] !== '' )
                    throw new UnexpectedValueException( "{{$name}} with a level" );
                return array( array( 'F', $name ) );
            case 'assign':
                $attributes = $this->parseAttributes( $node['rest'] );
                if ( !isset( $attributes['var'] ) || $attributes['var'][0] !== 'str' || !isset( $attributes['value'] ) || count( $attributes ) !== 2 )
                    throw new UnexpectedValueException( '{assign} other than var=... value=...' );
                return $this->assignment( array( 'var', $attributes['var'][1], array() ), $attributes['value'], false );
            case 'capture':
                $attributes = $this->parseAttributes( $node['rest'] );
                if ( !isset( $attributes['assign'] ) || count( $attributes ) !== 1 || $attributes['assign'][0] !== 'str' && $attributes['assign'][0] !== 'var' )
                    throw new UnexpectedValueException( '{capture} other than assign=<variable> ($smarty.capture has no template form)' );
                $variable = $attributes['assign'][0] === 'str' ? $attributes['assign'][1] : $attributes['assign'][1];
                return $this->setBlock( 'set-block', $variable, null, null, $node['children'] );
            case 'expinclude':
                return array( array( 'F', 'include' . $this->attributes( $this->parseAttributes( $node['rest'] ) ) ) );
            case 'expset':
                return $this->expsetTag( $this->parseAttributes( $node['rest'] ) );
            case 'expundefall':
                $this->scopes = array_map( function () { return array(); }, $this->scopes );
                return array( array( 'F', 'undef' ) );
            case 'expfetch':
                return $this->expfetchTag( $this->parseAttributes( $node['rest'] ) );
            case 'expsetblock':
            case 'expappendblock':
                $attributes = $this->parseAttributes( $node['rest'] );
                if ( !isset( $attributes['variable'] ) || $attributes['variable'][0] !== 'str' )
                    throw new UnexpectedValueException( "{{$name}} without variable='<name>'" );
                return $this->setBlock( $name === 'expsetblock' ? 'set-block' : 'append-block', $attributes['variable'][1],
                                        isset( $attributes['scope'] ) ? $this->constant( $attributes['scope'] ) : null,
                                        isset( $attributes['name'] ) ? $this->constant( $attributes['name'] ) : null, $node['children'] );
            case 'expcacheblock':
                $attributes = $this->parseAttributes( $node['rest'] );
                unset( $attributes['id'] );
                return $this->block( 'cache-block', $this->attributes( $attributes ), $node['children'] );
            case 'exprunonce':
                return $this->block( 'run-once', '', $node['children'] );
        }
        if ( in_array( $name, self::STRUCTURAL, true ) || !isset( $this->tpl->Functions[$name] ) )
            throw new UnexpectedValueException( "{{$name}} is neither a template function nor a tag of Smarty the converter knows" );
        $code = $name . $this->attributes( $this->parseAttributes( $node['rest'] ) );
        if ( $node['children'] === null )
            return array( array( 'F', $code ) );
        return $this->block( $name, substr( $code, strlen( $name ) ), $node['children'] );
    }

    private function block( $name, $attributeCode, array $children )
    {
        $this->pushScope();
        $body = $this->convertNodes( $children );
        $this->popScope();
        return array_merge( array( array( 'F', $name . $attributeCode ) ), $body, array( array( 'F', '/' . $name ) ) );
    }

    /**
     * " name=value ..." of a function tag.
     */
    private function attributes( array $attributes )
    {
        $code = '';
        foreach ( $attributes as $name => $value )
        {
            if ( is_int( $name ) )
                throw new UnexpectedValueException( 'a value without a parameter name' );
            $code .= ' ' . $name . '=' . $this->expr( $value );
        }
        return $code;
    }

    private function constant( array $e )
    {
        if ( $e[0] === 'str' || $e[0] === 'num' )
            return (string)$e[1];
        throw new UnexpectedValueException( 'a constant value was expected' );
    }

    /**
     * {default v=...}: the forward converter writes {if !isset($v)}{$v = ...}{/if}, the variable in the namespace the
     * {default} is in.
     *
     * @return array|null array( Smarty name, template name, value expression tree ), null when the node is not that
     */
    private function defaultPattern( array $node )
    {
        if ( $node['k'] !== 'named' || $node['name'] !== 'if' || count( $node['branches'] ) !== 1 )
            return null;
        try
        {
            $this->startExpression( $node['rest'] );
            $condition = $this->parseExpression();
            $this->expectEnd();
        }
        catch ( UnexpectedValueException $e )
        {
            return null;
        }
        $body = $this->statements( $node['children'] );
        if ( $condition[0] !== 'un' || $condition[1] !== '!' || $condition[2][0] !== 'call' || $condition[2][1] !== 'isset' ||
             count( $condition[2][2] ) !== 1 || $condition[2][2][0][0] !== 'var' || $condition[2][2][0][2] ||
             count( $body ) !== 1 || $body[0]['k'] !== 'assign' || $body[0]['append'] || !$this->isVariable( $body[0]['target'], $condition[2][2][0][1] ) ||
             $body[0]['e'][0] === 'null' )
            return null;
        $name = $condition[2][2][0][1];
        list( $namespace, $base ) = $this->splitNamespace( $name );
        if ( str_replace( '__', ':', $namespace ) !== implode( ':', $this->namespace ) )
            return null;
        return array( $name, $base, $body[0]['e'] );
    }

    /**
     * {default a=... b=...} and its body: the {default}s that follow each other at $i, and everything after them in
     * the block as their body. The template system unsets at {/default} what {default} set, and the .stpl keeps it to
     * the end: with the rest of the block as the body, both last to the end of the block.
     *
     * @return array|null array( items, the index of the last node used ), null when the node is not a {default}
     */
    private function defaultBlock( array $nodes, $i )
    {
        $defaults = array();
        $j = $i;
        while ( $j !== null && ( $default = $this->defaultPattern( $nodes[$j] ) ) !== null )
        {
            $defaults[] = $default;
            $last = $j;
            $j = $this->nextNode( $nodes, $j + 1 );
        }
        if ( !$defaults )
            return null;
        $code = 'default';
        try
        {
            foreach ( $defaults as $default )
                $code .= ' ' . $default[1] . '=' . $this->expr( $default[2] );
        }
        catch ( UnexpectedValueException $e )
        {
            return null;
        }
        $this->pushScope();
        foreach ( $defaults as $default )
        {
            if ( $this->splitNamespace( $default[0] )[0] === '' )
                $this->define( $default[0] );
        }
        $body = $this->convertNodes( array_slice( $nodes, $last + 1 ) );
        $this->popScope();
        return array( array_merge( array( array( 'F', $code ) ), $body, array( array( 'F', '/default' ) ) ), count( $nodes ) - 1 );
    }

    private function ifTag( array $node )
    {
        $items = array();
        $hasElse = false;
        // the conditions first: one that cannot be converted keeps the whole block as a comment
        $conditions = array();
        foreach ( $node['branches'] as $branch )
        {
            if ( $branch['tag'] === 'else' )
            {
                $conditions[] = null;
                continue;
            }
            $this->startExpression( $branch['rest'] );
            $condition = $this->parseExpression();
            $this->expectEnd();
            $conditions[] = $this->expr( $condition );
        }
        foreach ( $node['branches'] as $index => $branch )
        {
            $tag = $branch['tag'] === 'else' ? 'else' : ( $index === 0 ? 'if ' : 'elseif ' ) . $conditions[$index];
            $items[] = array( 'F', $tag );
            $this->pushScope();
            $items = array_merge( $items, $this->convertNodes( $branch['children'] ) );
            $this->popScope();
        }
        $items[] = array( 'F', '/if' );
        return $items;
    }

    /**
     * The head of a {foreach}: source, key, item ($a as $k => $v, or from=, key=, item=).
     */
    private function foreachHead( $rest )
    {
        $this->startExpression( $rest );
        if ( $this->peekIs( 'id' ) && $this->peekIs( 'p', '=', 1 ) )
        {
            $attributes = $this->parseAttributes( $rest );
            if ( !isset( $attributes['from'] ) || !isset( $attributes['item'] ) )
                throw new UnexpectedValueException( '{foreach} without from= and item=' );
            $name = function ( $e ) {
                if ( $e[0] === 'str' )
                    return $e[1];
                throw new UnexpectedValueException( '{foreach} item= and key= must name a variable' );
            };
            unset( $attributes['name'] );
            foreach ( array_keys( $attributes ) as $attribute )
            {
                if ( !in_array( $attribute, array( 'from', 'item', 'key' ), true ) )
                    throw new UnexpectedValueException( "{foreach} with $attribute=" );
            }
            return array( $attributes['from'], isset( $attributes['key'] ) ? $name( $attributes['key'] ) : null, $name( $attributes['item'] ) );
        }
        $source = $this->parseExpression();
        if ( !$this->peekIs( 'id', 'as' ) )
            throw new UnexpectedValueException( "{foreach} without 'as'" );
        ++$this->tp;
        $first = $this->expect( 'v' );
        $key = null;
        $item = $first;
        if ( $this->peekIs( 'p', '=>' ) )
        {
            ++$this->tp;
            $key = $first;
            $item = $this->expect( 'v' );
        }
        $this->expectEnd();
        return array( $source, $key, $item );
    }

    /**
     * {foreach}, with what the forward converter writes for its parameters, its delimiter and {section} and {for}.
     *
     * @return array( items, helpers used )
     */
    private function foreachTag( array $node, array $prelude )
    {
        list( $source, $key, $item ) = $this->foreachHead( $node['rest'] );
        $consumed = array();
        if ( $source[0] === 'var' && !$source[2] && isset( $prelude[$source[1]] ) && strncmp( $source[1], '__loop', 6 ) === 0 )
        {
            $consumed[] = $source[1];
            $source = $prelude[$source[1]]['e'];
        }
        $else = count( $node['branches'] ) > 1 ? $node['branches'][1]['children'] : null;

        // offset, max, reverse[, true (var=)[, the sequence]]
        if ( $source[0] === 'mod' && $source[2] === 'expsectionloop' && count( $source[3] ) >= 3 && count( $source[3] ) <= 5 )
            return $this->sectionTag( $node, $source, $key, $item, $prelude, $consumed, $else );

        $head = null;
        $isFor = $source[0] === 'call' && $source[1] === 'exprange' && count( $source[2] ) === 2 && $key === null;
        if ( $isFor )
            $head = 'for ' . $this->expr( $source[2][0] ) . ' to ' . $this->expr( $source[2][1] ) . ' as ' . $this->variableName( $item );
        $parameters = '';
        if ( !$isFor && $source[0] === 'mod' && $source[2] === 'expslice' && count( $source[3] ) === 3 )
        {
            list( $offset, $max, $reverse ) = $source[3];
            if ( !( $offset[0] === 'num' && $offset[1] === '0' ) )
                $parameters .= ' offset ' . $this->expr( $offset );
            if ( $max[0] !== 'null' )
                $parameters .= ' max ' . $this->expr( $max );
            // the template system's reverse is a flag: only a constant can be written back
            if ( $this->isTrue( $reverse ) || ( $reverse[0] === 'num' && (float)$reverse[1] != 0 ) )
                $parameters .= ' reverse';
            else if ( !$this->isFalse( $reverse ) && !( $reverse[0] === 'num' && (float)$reverse[1] == 0 ) )
                throw new UnexpectedValueException( 'a {foreach} reverse that is not a constant' );
            $source = $source[1];
        }

        $body = $this->statements( $node['children'] );
        // sequence: {$s = $__sequenceN|expsequence:$item@index} first in the body
        if ( $body && $body[0]['k'] === 'assign' && !$body[0]['append'] && $body[0]['target'][0] === 'var' && !$body[0]['target'][2] &&
             $body[0]['e'][0] === 'mod' && $body[0]['e'][2] === 'expsequence' && count( $body[0]['e'][3] ) === 1 &&
             $this->isProperty( $body[0]['e'][3][0], $item, 'index' ) && $body[0]['e'][1][0] === 'var' && isset( $prelude[$body[0]['e'][1][1]] ) )
        {
            $sequenceVariable = $body[0]['e'][1][1];
            $consumed[] = $sequenceVariable;
            $parameters = ' sequence ' . $this->expr( $prelude[$sequenceVariable]['e'] ) . ' as ' . $this->variableName( $body[0]['target'][1] ) . $parameters;
            array_shift( $body );
        }
        list( $delimiter, $delimFlag, $body ) = $this->delimiterOf( $body, $item, $prelude, $consumed );

        $sourceCode = $isFor ? null : $this->expr( $source );
        $simpleSource = $sourceCode !== null && preg_match( '/^\$[a-zA-Z0-9_:#.]+$/', $sourceCode );
        $counter = '__index_' . $item;
        $sourceRef = $simpleSource || $sourceCode === null ? (string)$sourceCode : '$__source_' . $item;
        if ( !$isFor )
            $head = 'foreach ' . $sourceCode . ' as ' . ( $key !== null ? $this->variableName( $key ) . ' => ' : '' ) . $this->variableName( $item ) . $parameters;
        else
            $head .= $parameters;

        $this->loops[] = array( 'item' => $item, 'key' => $key, 'kind' => 'foreach', 'counter' => $counter, 'sourceRef' => $sourceRef,
                                'used' => false, 'usedSource' => false, 'delimFlag' => $delimFlag, 'namespace' => '' );
        $this->pushScope();
        $this->define( $item );
        if ( $key !== null )
            $this->define( $key );
        $delimiterItems = $delimiter !== null ? $this->convertNodes( $delimiter ) : null;
        $bodyItems = $this->convertNodes( $body );
        $this->popScope();
        $loop = array_pop( $this->loops );
        if ( $isFor && $loop['usedSource'] )
            throw new UnexpectedValueException( '@last or @total of a {for}' );

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
            // the template system's {foreach} has no else: the loop runs only when there is something to loop over
            $this->pushScope();
            $elseItems = $this->convertNodes( $else );
            $this->popScope();
            $items = array_merge( array( array( 'F', 'if ' . ( $isFor ? 'true()' : $sourceCode . '|count' ) ) ), $items,
                                  array( array( 'F', 'else' ) ), $elseItems, array( array( 'F', '/if' ) ) );
        }
        return array( $items, $consumed );
    }

    /**
     * The delimiter at the start of a loop body: {if !$item@first}...{/if}, or with a {skip} in the loop
     * {if $__delimN}...{/if}{$__delimN = true} (with {$__delimN = false} before the loop).
     *
     * @return array( delimiter nodes or null, the flag variable or null, the rest of the body )
     */
    private function delimiterOf( array $body, $item, array $prelude, array &$consumed )
    {
        if ( !$body || $body[0]['k'] !== 'named' || $body[0]['name'] !== 'if' || count( $body[0]['branches'] ) !== 1 )
            return array( null, null, $body );
        try
        {
            $this->startExpression( $body[0]['rest'] );
            $condition = $this->parseExpression();
            $this->expectEnd();
        }
        catch ( UnexpectedValueException $e )
        {
            return array( null, null, $body );
        }
        if ( $condition[0] === 'un' && $condition[1] === '!' && $this->isProperty( $condition[2], $item, 'first' ) )
            return array( $body[0]['children'], null, array_slice( $body, 1 ) );
        if ( $condition[0] === 'var' && !$condition[2] && strncmp( $condition[1], '__delim', 7 ) === 0 && isset( $prelude[$condition[1]] ) &&
             $this->isFalse( $prelude[$condition[1]]['e'] ) && isset( $body[1] ) && $body[1]['k'] === 'assign' &&
             $this->helperName( $body[1] ) === $condition[1] && $this->isTrue( $body[1]['e'] ) )
        {
            $consumed[] = $condition[1];
            return array( $body[0]['children'], $condition[1], array_slice( $body, 2 ) );
        }
        return array( null, null, $body );
    }

    /**
     * {section}: the forward converter writes a {foreach} over (loop)|expsectionloop:offset:max:reverse with the
     * section's variables in its namespace ($Ns__key, $Ns__item, $Ns__index, $Ns__number, $Ns__sequence).
     */
    private function sectionTag( array $node, array $source, $key, $item, array $prelude, array $consumed, $else )
    {
        if ( $key === null || !preg_match( '/^(?:(.+)__)?key$/', $key, $m ) )
            throw new UnexpectedValueException( 'a {foreach} over |expsectionloop that is not the one the converter writes for {section}' );
        $prefix = isset( $m[1] ) ? $m[1] : '';
        $variable = function ( $name ) use ( $prefix ) { return $prefix !== '' ? $prefix . '__' . $name : $name; };
        $path = $prefix !== '' ? explode( '__', $prefix ) : array();
        $sectionName = null;
        $wrappers = array();
        if ( $path )
        {
            // a section inside a named section that has no loop ({section name=Basket show=...}, written as an {if})
            // is below that namespace: name='Basket:ProductItem', which the section adds to the current namespace
            $depth = count( $this->namespace );
            if ( count( $path ) <= $depth || array_slice( $path, 0, $depth ) !== $this->namespace )
                throw new UnexpectedValueException( "the section namespace $prefix is not below the namespace it is in" );
            $relative = array_slice( $path, $depth );
            $sectionName = array_pop( $relative );
            // the levels between: {section name=Basket} without a loop only changes the namespace
            $wrappers = $relative;
        }

        $body = $this->statements( $node['children'] );
        if ( count( $body ) < 2 || $body[0]['k'] !== 'assign' || !$this->isVariable( $body[0]['target'], $variable( 'index' ) ) ||
             !$this->isProperty( $body[0]['e'], $item, 'index' ) || $body[1]['k'] !== 'assign' ||
             !$this->isVariable( $body[1]['target'], $variable( 'number' ) ) || !$this->isProperty( $body[1]['e'], $item, 'iteration' ) )
            throw new UnexpectedValueException( 'a {foreach} over |expsectionloop without the section variables index and number' );
        $body = array_slice( $body, 2 );

        list( $loop, $offset, $max, $reverse ) = array( $source[1], $source[3][0], $source[3][1], $source[3][2] );
        $head = 'section' . ( $sectionName !== null ? ' name=' . $sectionName : '' ) . ' loop=' . $this->expr( $loop );
        if ( !( $offset[0] === 'num' && $offset[1] === '0' ) )
            $head .= ' offset=' . $this->expr( $offset );
        if ( $max[0] !== 'null' )
            $head .= ' max=' . $this->expr( $max );
        if ( !$this->isFalse( $reverse ) )
            $head .= ' reverse=' . $this->expr( $reverse );
        if ( $body && $body[0]['k'] === 'assign' && $this->isVariable( $body[0]['target'], $variable( 'sequence' ) ) &&
             $body[0]['e'][0] === 'mod' && $body[0]['e'][2] === 'expsequence' && $body[0]['e'][1][0] === 'var' &&
             isset( $prelude[$body[0]['e'][1][1]] ) )
        {
            $consumed[] = $body[0]['e'][1][1];
            $head .= ' sequence=' . $this->expr( $prelude[$body[0]['e'][1][1]]['e'] );
            array_shift( $body );
        }
        if ( $item !== $variable( 'item' ) )
            $head .= ' var=' . $item;

        $this->addNamespace( $prefix );

        // section-exclude / section-include: {$__skipN = false}{if match}{$__skipN = true|false}{/if}...{if !$__skipN}body{/if}
        $filters = array();
        if ( $body && $body[0]['k'] === 'assign' && ( $skip = $this->helperName( $body[0] ) ) !== null && strncmp( $skip, '__skip', 6 ) === 0 &&
             $this->isFalse( $body[0]['e'] ) )
        {
            $k = 1;
            while ( isset( $body[$k] ) && $body[$k]['k'] === 'named' && $body[$k]['name'] === 'if' && count( $body[$k]['branches'] ) === 1 )
            {
                $inner = $this->statements( $body[$k]['children'] );
                $this->startExpression( $body[$k]['rest'] );
                $condition = $this->parseExpression();
                $this->expectEnd();
                if ( count( $inner ) === 1 && $inner[0]['k'] === 'assign' && $this->helperName( $inner[0] ) === $skip )
                {
                    $filters[] = ( $this->isTrue( $inner[0]['e'] ) ? 'section-exclude' : 'section-include' ) . ' match=' . $this->expr( $condition );
                    ++$k;
                    continue;
                }
                if ( $condition[0] === 'un' && $condition[1] === '!' && $this->isVariable( $condition[2], $skip ) && !isset( $body[$k + 1] ) )
                {
                    $body = $this->statements( $body[$k]['children'] );
                    $k = -1;
                }
                break;
            }
            if ( $k !== -1 )
                throw new UnexpectedValueException( 'a {section} filter the converter does not read' );
        }
        list( $delimiter, $delimFlag, $body ) = $this->delimiterOf( $body, $item, $prelude, $consumed );

        $saved = $this->namespace;
        if ( $prefix !== '' )
            $this->namespace = explode( '__', $prefix );
        $this->loops[] = array( 'item' => $item, 'key' => $key, 'kind' => 'section', 'namespace' => $prefix, 'delimFlag' => $delimFlag );
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
     * {switch}: the forward converter writes {$__switchN = match} and an {if} chain on it.
     */
    private function switchTag( array $node, array $prelude )
    {
        $cases = array();
        $switch = null;
        foreach ( $node['branches'] as $branch )
        {
            if ( $branch['tag'] === 'else' )
            {
                $cases[] = array( 'case', $branch['children'] );
                continue;
            }
            $this->startExpression( $branch['rest'] );
            $condition = $this->parseExpression();
            $this->expectEnd();
            if ( $condition[0] === 'bin' && $condition[1] === '==' && $condition[2][0] === 'var' && !$condition[2][2] )
            {
                $variable = $condition[2][1];
                $case = 'case match=' . $this->expr( $condition[3] );
            }
            else if ( $condition[0] === 'mod' && $condition[2] === 'in_array' && count( $condition[3] ) === 1 && $condition[1][0] === 'var' && !$condition[1][2] )
            {
                $variable = $condition[1][1];
                $in = $condition[3][0];
                if ( $in[0] === 'mod' && $in[2] === 'expcolumn' && count( $in[3] ) === 1 )
                    $case = 'case in=' . $this->expr( $in[1] ) . ' key=' . $this->expr( $in[3][0] );
                else
                    $case = 'case in=' . $this->expr( $in );
            }
            else
                return null;
            if ( strncmp( $variable, '__switch', 8 ) !== 0 || !isset( $prelude[$variable] ) || ( $switch !== null && $switch !== $variable ) )
                return null;
            $switch = $variable;
            $cases[] = array( $case, $branch['children'] );
        }
        if ( $switch === null )
            return null;
        $items = array( array( 'F', 'switch match=' . $this->expr( $prelude[$switch]['e'] ) ) );
        foreach ( $cases as $case )
        {
            $items[] = array( 'F', $case[0] );
            $this->pushScope();
            $items = array_merge( $items, $this->convertNodes( $case[1] ) );
            $this->popScope();
            $items[] = array( 'F', '/case' );
        }
        $items[] = array( 'F', '/switch' );
        return array( $items, array( $switch ) );
    }

    /**
     * {do}: the forward converter writes {$__doN = true}{while $__doN}...{$__doN = condition}{/while}.
     */
    private function doTag( array $node, array $prelude )
    {
        $this->startExpression( $node['rest'] );
        $condition = $this->parseExpression();
        $this->expectEnd();
        if ( $condition[0] !== 'var' || $condition[2] || strncmp( $condition[1], '__do', 4 ) !== 0 || !isset( $prelude[$condition[1]] ) ||
             !$this->isTrue( $prelude[$condition[1]]['e'] ) )
            return null;
        $flag = $condition[1];
        $children = $node['children'];
        $last = null;
        for ( $k = count( $children ) - 1; $k >= 0; --$k )
        {
            if ( $children[$k]['k'] === 'text' && $children[$k]['e'] === '' )
                continue;
            $last = $k;
            break;
        }
        if ( $last === null || $children[$last]['k'] !== 'assign' || $this->helperName( $children[$last] ) !== $flag )
            return null;
        $while = $this->expr( $children[$last]['e'] );
        $this->pushScope();
        $body = $this->convertNodes( array_slice( $children, 0, $last ) );
        $this->popScope();
        return array( array_merge( array( array( 'F', 'do' ) ), $body, array( array( 'F', '/do while ' . $while ) ) ), array( $flag ) );
    }

    private function whileTag( array $node )
    {
        $this->startExpression( $node['rest'] );
        $condition = $this->expr( $this->parseExpression() );
        $this->expectEnd();
        $this->loops[] = array( 'item' => null, 'key' => null, 'kind' => 'while', 'delimFlag' => null );
        $this->pushScope();
        $body = $this->convertNodes( $node['children'] );
        $this->popScope();
        array_pop( $this->loops );
        return array( array_merge( array( array( 'F', 'while ' . $condition ) ), $body, array( array( 'F', '/while' ) ) ), array() );
    }

    /**
     * Smarty's {for $i = a to b}: the template system's {for} counts down too, so it only runs when a <= b.
     */
    private function forTag( array $node )
    {
        $this->startExpression( $node['rest'] );
        $variable = $this->expect( 'v' );
        $this->expect( 'p', '=' );
        $from = $this->parseExpression();
        if ( !$this->peekIs( 'id', 'to' ) )
            throw new UnexpectedValueException( '{for} other than {for $i = a to b}' );
        ++$this->tp;
        $to = $this->parseExpression();
        if ( $this->tp < count( $this->tk ) )
            throw new UnexpectedValueException( '{for} with step or max' );
        if ( count( $node['branches'] ) > 1 )
            throw new UnexpectedValueException( '{forelse}' );
        $fromCode = $this->expr( $from );
        $toCode = $this->expr( $to );
        $this->loops[] = array( 'item' => $variable, 'key' => null, 'kind' => 'for', 'counter' => '__index_' . $variable, 'sourceRef' => '',
                                'used' => false, 'usedSource' => false, 'delimFlag' => null, 'namespace' => '' );
        $this->pushScope();
        $this->define( $variable );
        $body = $this->convertNodes( $node['children'] );
        $this->popScope();
        $loop = array_pop( $this->loops );
        if ( $loop['used'] || $loop['usedSource'] )
            throw new UnexpectedValueException( 'a loop property of a {for} variable' );
        $items = array_merge( array( array( 'F', 'for ' . $fromCode . ' to ' . $toCode . ' as $' . $variable ) ), $body, array( array( 'F', '/for' ) ) );
        $ascending = $from[0] === 'num' && $to[0] === 'num' && (float)$from[1] <= (float)$to[1];
        if ( !$ascending )
            $items = array_merge( array( array( 'F', 'if le(' . $fromCode . ', ' . $toCode . ')' ) ), $items, array( array( 'F', '/if' ) ) );
        return $items;
    }

    private function setBlock( $tag, $variable, $scope, $namespace, array $children )
    {
        list( $prefix, $base ) = $this->splitNamespace( $variable );
        $code = $tag;
        if ( $prefix !== '' )
        {
            if ( str_replace( '__', ':', $prefix ) !== implode( ':', $this->namespace ) || $scope !== null || $namespace !== null )
                throw new UnexpectedValueException( "a capture into \$$variable, a variable of the section $prefix, outside of it" );
            $code .= ' variable=' . $base;
        }
        else
        {
            $code .= ' variable=' . $variable;
            // {capture assign=x} is the template's own variable: scope=root, which the set-block of the template system's own
            // templates mostly gives (the same as no scope outside of a section; in a section it is needed)
            if ( $scope === null && $namespace === null && $tag === 'set-block' )
                $scope = 'root';
            if ( $scope !== null )
                $code .= ' scope=' . $scope;
            if ( $namespace !== null )
                $code .= ' name=' . $namespace;
            if ( ( $scope === null || $scope === 'root' ) && $namespace === null )
                $this->define( $variable );
        }
        $this->pushScope();
        $body = $this->convertNodes( $children );
        $this->popScope();
        return array_merge( array( array( 'F', $code ) ), $body, array( array( 'F', '/' . $tag ) ) );
    }

    /**
     * {expset name='x' value=... scope='root' namespace='ns'}: {set scope=root name=ns x=...}
     */
    private function expsetTag( array $attributes )
    {
        if ( !isset( $attributes['name'] ) || !isset( $attributes['value'] ) )
            throw new UnexpectedValueException( '{expset} without name= and value=' );
        $code = 'set';
        if ( isset( $attributes['scope'] ) )
            $code .= ' scope=' . $this->constant( $attributes['scope'] );
        if ( isset( $attributes['namespace'] ) )
            $code .= ' name=' . $this->constant( $attributes['namespace'] );
        unset( $attributes['scope'], $attributes['namespace'] );
        if ( count( $attributes ) !== 2 )
            throw new UnexpectedValueException( '{expset} with parameters other than name, value, scope and namespace' );
        return array( array( 'F', $code . ' ' . $this->constant( $attributes['name'] ) . '=' . $this->expr( $attributes['value'] ) ) );
    }

    /**
     * {expfetch module='content' function='node' assign='node' node_id=2}: {def $node = fetch('content', 'node', hash('node_id', 2))}
     */
    private function expfetchTag( array $attributes )
    {
        foreach ( array( 'module', 'function', 'assign' ) as $required )
        {
            if ( !isset( $attributes[$required] ) )
                throw new UnexpectedValueException( "{expfetch} without $required=" );
        }
        $assign = $this->constant( $attributes['assign'] );
        $call = array( 'call', 'fetch', array( $attributes['module'], $attributes['function'] ) );
        unset( $attributes['module'], $attributes['function'], $attributes['assign'] );
        $pairs = array();
        foreach ( $attributes as $name => $value )
        {
            if ( is_int( $name ) )
                throw new UnexpectedValueException( 'a value without a parameter name' );
            $pairs[] = array( array( 'str', $name ), $value );
        }
        $call[2][] = $pairs ? array( 'array', $pairs ) : array( 'array', array() );
        if ( !$pairs )
            $call[2][2] = array( 'call', 'hash', array() );
        return $this->assignment( array( 'var', $assign, array() ), $call, false );
    }

    // ----------------------------------------------------------------------------------------
    // Output

    /**
     * Writes the items as .tpl text. A text gets the newlines the template system drops next to a function tag:
     * one before it when it starts with a newline (or when Smarty dropped one there, for the layout), one after it
     * when it ends with a newline.
     */
    private function emit( array $items )
    {
        // adjacent texts are one text
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
        // {undef $a}{undef $b} is {undef $a $b}, {set $a = 1}{set $b = 2} is {set $a = 1 $b = 2}: the forms the
        // template system's own templates use most ({def} is mostly written one variable per tag)
        $grouped = array();
        $last = null;
        foreach ( $merged as $item )
        {
            if ( $item[0] === 'T' && $item[1] === '' )
            {
                $grouped[] = $item;
                continue;
            }
            // never a variable the tag has already: its parameters are keyed by the variable name
            if ( $item[0] === 'F' && $last !== null && preg_match( '/^(undef|set) \$(\w+)/', $item[1], $m ) &&
                 strncmp( $grouped[$last][1], $m[1] . ' $', strlen( $m[1] ) + 2 ) === 0 &&
                 !preg_match( '/(^' . $m[1] . ' | )\$' . $m[2] . '( = |$| )/', $grouped[$last][1] ) )
            {
                $grouped[$last][1] .= substr( $item[1], strlen( $m[1] ) );
                // the empty texts between them go too
                $grouped = array_slice( $grouped, 0, $last + 1 );
                continue;
            }
            $grouped[] = $item;
            $last = $item[0] === 'F' ? count( $grouped ) - 1 : null;
        }
        $merged = $grouped;

        // a text with a brace and a {/literal} cannot be a {literal}: its braces become {ldelim}
        $items = array();
        foreach ( $merged as $item )
        {
            if ( $item[0] === 'T' && strpos( $item[1], '{/literal}' ) !== false )
            {
                $parts = explode( '{', $item[1] );
                foreach ( $parts as $index => $part )
                {
                    if ( $index > 0 )
                        $items[] = array( 'F', 'ldelim' );
                    if ( $part !== '' )
                        $items[] = array( 'T', $part, $index === 0 ? $item[2] : false );
                }
                continue;
            }
            $items[] = $item;
        }

        $out = '';
        $count = count( $items );
        foreach ( $items as $index => $item )
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
                    $previousTag = $index > 0 && $items[$index - 1][0] === 'F';
                    $nextTag = $index + 1 < $count && $items[$index + 1][0] === 'F';
                    $before = $previousTag && ( ( isset( $text[0] ) && $text[0] === "\n" ) || $item[2] ) ? "\n" : '';
                    $after = $nextTag && substr( $text, -1 ) === "\n" ? "\n" : '';
                    if ( strpos( $text, '{' ) !== false )
                        $text = '{literal}' . $text . '{/literal}';
                    $out .= $before . $text . $after;
                    break;
            }
        }
        return $out;
    }

    private function warn( $pos, $message )
    {
        $line = substr_count( $this->source, "\n", 0, min( (int)$pos, strlen( $this->source ) ) ) + 1;
        $this->warnings[] = "line $line: $message";
    }
}
