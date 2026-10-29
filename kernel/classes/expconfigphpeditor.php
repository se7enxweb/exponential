<?php
/**
 * File containing the expConfigPhpEditor class.
 *
 * Reads and changes the define( 'NAME', value ) settings of a config.php without running it and without a regular
 * expression over the PHP: the file is split into PHP tokens (token_get_all()), and only the value of one define()
 * statement is replaced, so the comments, the order and the formatting of everything else stay as they are.
 * Used by exp:config:list, exp:config:get, exp:config:set and exp:config:unset (bin/php/configphp.php).
 *
 *   $editor = expConfigPhpEditor::fromFile( 'config.php' );       // refuses a file that does not compile
 *   $entry  = $editor->find( 'EZP_INI_FILEMTIME_CHECK' );         // array or null
 *   $code   = $editor->withValue( 'EZP_INI_FILEMTIME_CHECK', 'false', $catalogue );
 *   $code   = $editor->without( 'EZP_INI_FILEMTIME_CHECK' );
 *
 * A setting is "set" when a define() of it runs unconditionally at the top level of the file. A define() inside an
 * if, a block, after "||" or "or", or with a name that is not a plain string is "conditional": it is reported, never
 * changed. A setting that is only in a comment line "// define( 'NAME', value );", as config.php-RECOMMENDED ships
 * every one of them, is "commented": exp:config:set takes the comment out and writes the value there, next to its
 * documentation. A setting found nowhere is added between the lines
 *
 *   // BEGIN settings set by ./console exp:config:set
 *   // END settings set by ./console exp:config:set
 *
 * which the first such write creates. exp:config:unset comments a set define() out again (and removes it from
 * that block).
 *
 * Guide: doc/eng-us/guides/setup-files-from-the-console.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expConfigPhpEditor
{
    const BLOCK_BEGIN = '// BEGIN settings set by ./console exp:config:set';
    const BLOCK_END = '// END settings set by ./console exp:config:set';

    /** The value types a setting can be written as (--type). */
    const TYPES = array( 'bool', 'int', 'octal', 'float', 'string', 'null' );

    /** Names whose values are hidden in output unless --show-secrets. */
    const SECRET_PATTERN = '/PASS|SECRET|KEY|TOKEN|CREDENTIAL|PWD/i';

    /** @var string the PHP code */
    protected $code;

    /** @var array|null the define() entries, see entries() */
    protected $entries;

    /**
     * @param string $code the content of a config.php
     */
    public function __construct( $code )
    {
        $this->code = (string)$code;
    }

    /**
     * The editor of a file. A file that does not exist is an empty config.php.
     *
     * @param string $file
     * @return expConfigPhpEditor
     */
    public static function fromFile( $file )
    {
        return new self( is_file( $file ) ? (string)file_get_contents( $file ) : '' );
    }

    /** @return string the code as it is */
    public function code()
    {
        return $this->code;
    }

    /**
     * The syntax error of the code, or null when it compiles (token_get_all() with TOKEN_PARSE, the parser of the
     * running PHP, so no other process is needed).
     *
     * @param string $code
     * @return string|null "message on line N"
     */
    public static function syntaxError( $code )
    {
        if ( $code === '' )
            return null;
        try
        {
            token_get_all( $code, TOKEN_PARSE );
        }
        catch ( ParseError $e )
        {
            return $e->getMessage() . ' on line ' . $e->getLine();
        }
        return null;
    }

    /**
     * Whether the value of a setting of this name is hidden in output (passwords, secrets, keys, tokens).
     *
     * @param string $name
     * @return bool
     */
    public static function isSecret( $name )
    {
        return (bool)preg_match( self::SECRET_PATTERN, (string)$name );
    }

    // ------------------------------------------------------------------ reading

    /**
     * The tokens of the code, each array( id, text, line, offset ); a one-character token has the id 0.
     *
     * @param string $code
     * @return array
     */
    public static function tokens( $code )
    {
        $tokens = array();
        $offset = 0;
        $line = 1;
        foreach ( token_get_all( $code ) as $t )
        {
            if ( is_array( $t ) )
                $tokens[] = array( $t[0], $t[1], $t[2], $offset );
            else
                $tokens[] = array( 0, $t, $line, $offset );
            $text = is_array( $t ) ? $t[1] : $t;
            $offset += strlen( $text );
            $line += substr_count( $text, "\n" );
        }
        return $tokens;
    }

    /** @return bool the token is whitespace or a comment */
    protected static function insignificant( array $t )
    {
        return $t[0] === T_WHITESPACE || $t[0] === T_COMMENT || $t[0] === T_DOC_COMMENT;
    }

    /**
     * Every define() statement and every commented "// define( ... );" line of the code, in order:
     *
     *   name, state ('set', 'conditional' or 'commented'), line, start and end (byte offsets of the statement or the
     *   comment), value_start and value_end (of the value expression), raw (the value as written), type, value
     *   (decoded, see decode()), in_block (inside the BEGIN/END block of exp:config:set)
     *
     * @return array
     */
    public function entries()
    {
        if ( $this->entries === null )
            $this->entries = self::parseEntries( $this->code );
        return $this->entries;
    }

    /**
     * @param string $code
     * @return array see entries()
     */
    public static function parseEntries( $code )
    {
        $tokens = self::tokens( $code );
        $entries = array();
        $depth = 0;
        $n = count( $tokens );
        $blockBegin = strpos( $code, self::BLOCK_BEGIN );
        $blockEnd = $blockBegin !== false ? strpos( $code, self::BLOCK_END, $blockBegin ) : false;
        $previousCommentLine = '';
        $previousCommentEndLine = -1;
        for ( $i = 0; $i < $n; ++$i )
        {
            $t = $tokens[$i];
            if ( $t[0] === 0 && ( $t[1] === '{' ) )
                ++$depth;
            else if ( $t[0] === T_CURLY_OPEN || $t[0] === T_DOLLAR_OPEN_CURLY_BRACES )
                ++$depth;
            else if ( $t[0] === 0 && $t[1] === '}' )
                $depth = max( 0, $depth - 1 );

            if ( $t[0] === T_COMMENT && substr( $t[1], 0, 2 ) === '//' )
            {
                $inner = rtrim( substr( $t[1], 2 ) );
                $commented = self::commentedDefine( $inner );
                // "//if ( ... )" on the line before makes the commented define conditional
                $conditionalComment = $previousCommentEndLine === $t[2] - 1 && preg_match( '/^\s*(if|elseif|else)\b/i', $previousCommentLine );
                if ( $commented !== null )
                {
                    $commented['state'] = 'commented';
                    $commented['conditional'] = (bool)$conditionalComment || $depth > 0;
                    $commented['line'] = $t[2];
                    $commented['start'] = $t[3];
                    $commented['end'] = $t[3] + strlen( rtrim( $t[1], "\r\n" ) );
                    $commented['in_block'] = $blockBegin !== false && $blockEnd !== false && $t[3] > $blockBegin && $t[3] < $blockEnd;
                    $entries[] = $commented;
                }
                $previousCommentLine = $inner;
                $previousCommentEndLine = $t[2];
                continue;
            }

            if ( !self::isDefineCall( $tokens, $i ) )
                continue;
            $entry = self::defineStatement( $tokens, $i, $depth );
            if ( $entry === null )
                continue;
            $entry['in_block'] = $blockBegin !== false && $blockEnd !== false && $entry['start'] > $blockBegin && $entry['start'] < $blockEnd;
            $entries[] = $entry;
        }
        return $entries;
    }

    /**
     * Whether the token at $i is the name of a call of the function define().
     *
     * @param array $tokens
     * @param int $i
     * @return bool
     */
    protected static function isDefineCall( array $tokens, $i )
    {
        $t = $tokens[$i];
        $name = strtolower( $t[1] );
        $isName = $t[0] === T_STRING && $name === 'define';
        if ( defined( 'T_NAME_FULLY_QUALIFIED' ) && $t[0] === T_NAME_FULLY_QUALIFIED && $name === '\\define' )
            $isName = true;
        if ( !$isName )
            return false;
        for ( $j = $i + 1; $j < count( $tokens ) && self::insignificant( $tokens[$j] ); ++$j );
        if ( !isset( $tokens[$j] ) || $tokens[$j][1] !== '(' )
            return false;
        $p = self::previousSignificant( $tokens, $i );
        if ( $p !== null && $tokens[$p][0] === T_NS_SEPARATOR )
            $p = self::previousSignificant( $tokens, $p );
        if ( $p !== null && in_array( $tokens[$p][0], array( T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION, T_NEW, T_CONST ), true ) )
            return false;
        if ( defined( 'T_NULLSAFE_OBJECT_OPERATOR' ) && $p !== null && $tokens[$p][0] === T_NULLSAFE_OBJECT_OPERATOR )
            return false;
        return true;
    }

    /** @return int|null the index of the significant token before $i */
    protected static function previousSignificant( array $tokens, $i )
    {
        for ( $j = $i - 1; $j >= 0; --$j )
            if ( !self::insignificant( $tokens[$j] ) )
                return $j;
        return null;
    }

    /**
     * The entry of the define() call whose name token is at $i, or null when it is not a define of a plain name.
     *
     * @param array $tokens
     * @param int $i
     * @param int $depth the brace depth at $i
     * @return array|null
     */
    protected static function defineStatement( array $tokens, $i, $depth )
    {
        $n = count( $tokens );
        // the statement starts at "\" when the call is written \define(
        $start = $tokens[$i][3];
        $p = self::previousSignificant( $tokens, $i );
        if ( $p !== null && $tokens[$p][0] === T_NS_SEPARATOR )
        {
            $start = $tokens[$p][3];
            $p = self::previousSignificant( $tokens, $p );
        }
        // unconditional: the first statement, or after ";", "}", "{" of no condition, or the open tag
        $unconditional = $depth === 0 && ( $p === null || $tokens[$p][1] === ';' || $tokens[$p][1] === '}'
                         || $tokens[$p][0] === T_OPEN_TAG || $tokens[$p][0] === T_CLOSE_TAG );

        for ( $j = $i + 1; $j < $n && $tokens[$j][1] !== '('; ++$j );
        // the name: one quoted string
        $k = $j + 1;
        while ( $k < $n && self::insignificant( $tokens[$k] ) )
            ++$k;
        if ( $k >= $n || $tokens[$k][0] !== T_CONSTANT_ENCAPSED_STRING )
            return null;
        $name = self::decodeString( $tokens[$k][1] );
        if ( $name === null || !preg_match( '/^[A-Za-z_][A-Za-z0-9_]*$/', $name ) )
            return null;
        $k++;
        while ( $k < $n && self::insignificant( $tokens[$k] ) )
            ++$k;
        if ( $k >= $n || $tokens[$k][1] !== ',' )
            return null;

        // the value: up to "," or ")" at the depth of the call
        $level = 0;
        $valueTokens = array();
        $valueStart = null;
        $valueEnd = null;
        $extraArgument = false;
        for ( $m = $k + 1; $m < $n; ++$m )
        {
            $text = $tokens[$m][1];
            if ( $tokens[$m][0] === 0 && ( $text === '(' || $text === '[' || $text === '{' ) )
                ++$level;
            else if ( $tokens[$m][0] === T_CURLY_OPEN || $tokens[$m][0] === T_DOLLAR_OPEN_CURLY_BRACES )
                ++$level;
            else if ( $tokens[$m][0] === 0 && ( $text === ')' || $text === ']' || $text === '}' ) )
            {
                if ( $level === 0 )
                    break;
                --$level;
            }
            else if ( $level === 0 && $tokens[$m][0] === 0 && $text === ',' )
            {
                $extraArgument = true;
                // the third argument (case_insensitive, removed in PHP 8): skip to the closing parenthesis
                $inner = 0;
                for ( ++$m; $m < $n; ++$m )
                {
                    $x = $tokens[$m][1];
                    if ( $tokens[$m][0] === 0 && ( $x === '(' || $x === '[' ) )
                        ++$inner;
                    else if ( $tokens[$m][0] === 0 && ( $x === ')' || $x === ']' ) )
                    {
                        if ( $inner === 0 )
                            break;
                        --$inner;
                    }
                }
                break;
            }
            if ( !self::insignificant( $tokens[$m] ) )
            {
                if ( $valueStart === null )
                    $valueStart = $tokens[$m][3];
                $valueEnd = $tokens[$m][3] + strlen( $text );
                $valueTokens[] = $tokens[$m];
            }
            else if ( $valueStart !== null && $tokens[$m][0] !== T_WHITESPACE )
            {
                $valueTokens[] = $tokens[$m];
            }
        }
        if ( $m >= $n || $valueStart === null )
            return null;
        $close = $m;
        // after ")": ";" or the close tag ends the statement; anything else ("or die") makes it conditional
        $after = $close + 1;
        while ( $after < $n && self::insignificant( $tokens[$after] ) )
            ++$after;
        $end = $tokens[$close][3] + 1;
        if ( $after < $n && $tokens[$after][1] === ';' )
            $end = $tokens[$after][3] + 1;
        else if ( !( $after < $n && $tokens[$after][0] === T_CLOSE_TAG ) && $after < $n )
            $unconditional = false;

        $value = self::decodeTokens( $valueTokens );
        return array(
            'name' => $name,
            'state' => $unconditional ? 'set' : 'conditional',
            'conditional' => !$unconditional,
            'line' => $tokens[$i][2],
            'start' => $start,
            'end' => $end,
            'value_start' => $valueStart,
            'value_end' => $valueEnd,
            'raw' => substr( self::joinTokens( $valueTokens ), 0 ),
            'type' => $value['type'],
            'value' => $value['value'],
            'case_argument' => $extraArgument,
        );
    }

    /** @return string the text of the tokens */
    protected static function joinTokens( array $tokens )
    {
        $text = '';
        $last = null;
        foreach ( $tokens as $t )
        {
            // whitespace between significant tokens was dropped: put one space where the code had any
            if ( $last !== null && $t[3] > $last )
                $text .= ' ';
            $text .= $t[1];
            $last = $t[3] + strlen( $t[1] );
        }
        return $text;
    }

    /**
     * The define() of a comment line's text ("define( 'NAME', value );" after "//"), or null.
     *
     * @param string $inner the comment without "//"
     * @return array|null name, raw, type, value, value_offset and value_length (in $inner)
     */
    public static function commentedDefine( $inner )
    {
        if ( !preg_match( '/^\s*\\\\?define\s*\(/i', $inner ) )
            return null;
        $code = '<?php ' . $inner;
        if ( self::syntaxError( $code ) !== null )
            return null;
        foreach ( self::parseEntries( $code ) as $entry )
        {
            if ( $entry['state'] === 'commented' )
                continue;
            return array(
                'name' => $entry['name'],
                'raw' => $entry['raw'],
                'type' => $entry['type'],
                'value' => $entry['value'],
                'value_offset' => $entry['value_start'] - 6,
                'value_length' => $entry['value_end'] - $entry['value_start'],
                'statement_offset' => $entry['start'] - 6,
                'statement_end' => $entry['end'] - 6,
            );
        }
        return null;
    }

    /**
     * The value of a string literal token, or null for a double quoted string with variables.
     *
     * @param string $literal
     * @return string|null
     */
    public static function decodeString( $literal )
    {
        $quote = $literal[0];
        $inner = substr( $literal, 1, -1 );
        if ( $quote === "'" )
            return preg_replace_callback( '/\\\\([\\\\\'])/', function ( $m ) { return $m[1]; }, $inner );
        if ( $quote === '"' )
        {
            if ( preg_match( '/(?<!\\\\)\$/', $inner ) )
                return null;
            return stripcslashes( $inner );
        }
        return null;
    }

    /**
     * The type and value of a value expression: bool, null, int, octal (an integer written with a leading 0), float,
     * string, or expression (anything else: a constant, a concatenation, a call), whose value is its text.
     *
     * @param array $tokens the significant tokens of the value
     * @return array type, value
     */
    protected static function decodeTokens( array $tokens )
    {
        $tokens = array_values( array_filter( $tokens, function ( $t ) { return !self::insignificant( $t ); } ) );
        $sign = 1;
        if ( count( $tokens ) === 2 && $tokens[0][1] === '-' )
        {
            $sign = -1;
            array_shift( $tokens );
        }
        if ( count( $tokens ) === 1 )
        {
            $t = $tokens[0];
            if ( $t[0] === T_CONSTANT_ENCAPSED_STRING && $sign === 1 )
            {
                $s = self::decodeString( $t[1] );
                if ( $s !== null )
                    return array( 'type' => 'string', 'value' => $s );
            }
            if ( $t[0] === T_LNUMBER )
            {
                $raw = str_replace( '_', '', $t[1] );
                if ( preg_match( '/^0[oO]?([0-7]+)$/', $raw, $m ) )
                    return array( 'type' => 'octal', 'value' => $sign * (int)octdec( $m[1] ) );
                if ( preg_match( '/^0[xX][0-9a-fA-F]+$/', $raw ) )
                    return array( 'type' => 'int', 'value' => $sign * (int)hexdec( $raw ) );
                if ( preg_match( '/^0[bB][01]+$/', $raw ) )
                    return array( 'type' => 'int', 'value' => $sign * (int)bindec( substr( $raw, 2 ) ) );
                return array( 'type' => 'int', 'value' => $sign * (int)$raw );
            }
            if ( $t[0] === T_DNUMBER )
                return array( 'type' => 'float', 'value' => $sign * (float)str_replace( '_', '', $t[1] ) );
            if ( $t[0] === T_STRING && $sign === 1 )
            {
                $word = strtolower( $t[1] );
                if ( $word === 'true' || $word === 'false' )
                    return array( 'type' => 'bool', 'value' => $word === 'true' );
                if ( $word === 'null' )
                    return array( 'type' => 'null', 'value' => null );
            }
        }
        return array( 'type' => 'expression', 'value' => self::joinTokens( $tokens ) );
    }

    /**
     * The entries of a name.
     *
     * @param string $name
     * @param string|null $state only this state
     * @return array
     */
    public function entriesOf( $name, $state = null )
    {
        $found = array();
        foreach ( $this->entries() as $entry )
            if ( $entry['name'] === $name && ( $state === null || $entry['state'] === $state ) )
                $found[] = $entry;
        return $found;
    }

    /**
     * The setting as config.php has it: the set define() (or the conditional one), else null.
     *
     * @param string $name
     * @return array|null an entry, see entries(); 'state' says whether it is set or conditional
     */
    public function find( $name )
    {
        $set = $this->entriesOf( $name, 'set' );
        if ( $set )
            return $set[count( $set ) - 1];
        $conditional = $this->entriesOf( $name, 'conditional' );
        return $conditional ? $conditional[0] : null;
    }

    /**
     * The names config.php defines (set or conditional), in order.
     *
     * @return array
     */
    public function definedNames()
    {
        $names = array();
        foreach ( $this->entries() as $entry )
            if ( $entry['state'] !== 'commented' && !in_array( $entry['name'], $names, true ) )
                $names[] = $entry['name'];
        return $names;
    }

    // ------------------------------------------------------------------ values

    /**
     * The PHP literal of a value of a type.
     *
     * @param mixed $value
     * @param string $type bool, int, octal, float, string, null
     * @return string
     */
    public static function literal( $value, $type )
    {
        switch ( $type )
        {
            case 'bool':
                return $value ? 'true' : 'false';
            case 'null':
                return 'null';
            case 'int':
                return (string)(int)$value;
            case 'octal':
                return ( $value < 0 ? '-' : '' ) . '0' . decoct( abs( (int)$value ) );
            case 'float':
                $text = var_export( (float)$value, true );
                return $text;
            default:
                return "'" . strtr( (string)$value, array( '\\' => '\\\\', "'" => "\\'" ) ) . "'";
        }
    }

    /**
     * The value given on the command line as a typed value.
     *
     * Without $type (no --type) the documented type of the setting decides ($documentedType); true, false and null are
     * always the PHP values, as the documentation of several settings allows false where the value is otherwise a
     * number or a string; --type=string writes them as text. For an undocumented setting the text decides: true,
     * false, null, an integer, an integer with a leading 0 (octal, as the modes are written), a number, else a string.
     *
     * @param string $text
     * @param string|null $type an explicit type (--type), or null
     * @param string|null $documentedType the type of the documented value (catalogue), or null
     * @return array type, value; or error (a message)
     */
    public static function typedValue( $text, $type = null, $documentedType = null )
    {
        $text = (string)$text;
        $word = strtolower( trim( $text ) );
        if ( $type === null )
        {
            if ( $word === 'true' || $word === 'false' )
                return array( 'type' => 'bool', 'value' => $word === 'true' );
            if ( $word === 'null' )
                return array( 'type' => 'null', 'value' => null );
            if ( $documentedType === 'expression' )
                $documentedType = 'string';
            $type = $documentedType !== null && $documentedType !== 'null' ? $documentedType : self::guessType( $text );
            $result = self::typedValue( $text, $type );
            if ( isset( $result['error'] ) && $documentedType !== null )
                $result['error'] .= " (the documented value is of type $documentedType; give --type=<type> to write another type)";
            return $result;
        }

        switch ( $type )
        {
            case 'bool':
                if ( in_array( $word, array( 'true', '1', 'yes', 'on', 'enabled' ), true ) )
                    return array( 'type' => 'bool', 'value' => true );
                if ( in_array( $word, array( 'false', '0', 'no', 'off', 'disabled' ), true ) )
                    return array( 'type' => 'bool', 'value' => false );
                return array( 'error' => "\"$text\" is not true or false" );
            case 'null':
                if ( $word === 'null' || $word === '' )
                    return array( 'type' => 'null', 'value' => null );
                return array( 'error' => "\"$text\" is not null" );
            case 'int':
                if ( preg_match( '/^[+-]?(0|[1-9][0-9]*)$/', trim( $text ) ) )
                    return array( 'type' => 'int', 'value' => (int)$text );
                if ( preg_match( '/^0[0-7]+$/', trim( $text ) ) )
                    return array( 'error' => "\"$text\" has a leading 0: give --type=octal for a mode such as 0640" );
                return array( 'error' => "\"$text\" is not an integer" );
            case 'octal':
                if ( preg_match( '/^(?:0[oO]?)?([0-7]{1,6})$/', trim( $text ), $m ) )
                    return array( 'type' => 'octal', 'value' => (int)octdec( $m[1] ) );
                return array( 'error' => "\"$text\" is not an octal number such as 0640" );
            case 'float':
                if ( is_numeric( trim( $text ) ) )
                    return array( 'type' => 'float', 'value' => (float)$text );
                return array( 'error' => "\"$text\" is not a number" );
            case 'string':
                return array( 'type' => 'string', 'value' => $text );
        }
        return array( 'error' => "unknown type \"$type\": give one of " . implode( ', ', self::TYPES ) );
    }

    /** @return string the type a text looks like */
    protected static function guessType( $text )
    {
        $trimmed = trim( $text );
        if ( preg_match( '/^-?(0|[1-9][0-9]*)$/', $trimmed ) )
            return 'int';
        if ( preg_match( '/^0[0-7]+$/', $trimmed ) )
            return 'octal';
        if ( $trimmed !== '' && is_numeric( $trimmed ) && $trimmed === $text )
            return 'float';
        return 'string';
    }

    /**
     * The value of an entry as text for a person or a shell script: a string without quotes, true, false, null, an
     * octal integer with its leading 0, an expression as written.
     *
     * @param array $entry
     * @return string
     */
    public static function plainValue( array $entry )
    {
        switch ( $entry['type'] )
        {
            case 'bool':
                return $entry['value'] ? 'true' : 'false';
            case 'null':
                return 'null';
            case 'octal':
                return self::literal( $entry['value'], 'octal' );
            case 'string':
                return (string)$entry['value'];
            case 'expression':
                return (string)$entry['raw'];
        }
        return (string)$entry['value'];
    }

    // ------------------------------------------------------------------ changing

    /**
     * The code with the setting set to a value.
     *
     * An unconditional define() gets the new value in place. Without one, the first commented
     * "// define( 'NAME', ... );" line that is not part of a commented condition becomes the define(). Without that,
     * the define() is added to the BEGIN/END block of exp:config:set (created before the first statement of the
     * file that is not a define(), else before "?>", else at the end).
     *
     * @param string $name
     * @param string $literal the PHP literal of the value (literal())
     * @return array code, how ('changed', 'uncommented', 'added', 'unchanged'), line; or error
     */
    public function withValue( $name, $literal )
    {
        $set = $this->entriesOf( $name, 'set' );
        $conditional = $this->entriesOf( $name, 'conditional' );
        if ( count( $set ) > 1 )
            return array( 'error' => "$name is defined more than once (lines " . implode( ', ', array_column( $set, 'line' ) ) . "); remove all but one by hand first" );
        if ( $conditional )
            return array( 'error' => "$name is defined inside a condition or an expression on line " . $conditional[0]['line'] . "; change it by hand" );
        if ( $set )
        {
            $entry = $set[0];
            if ( substr( $this->code, $entry['value_start'], $entry['value_end'] - $entry['value_start'] ) === $literal )
                return array( 'code' => $this->code, 'how' => 'unchanged', 'line' => $entry['line'] );
            $code = substr( $this->code, 0, $entry['value_start'] ) . $literal . substr( $this->code, $entry['value_end'] );
            return array( 'code' => $code, 'how' => 'changed', 'line' => $entry['line'] );
        }

        foreach ( $this->entriesOf( $name, 'commented' ) as $entry )
        {
            if ( $entry['conditional'] )
                continue;
            $lineStart = strrpos( substr( $this->code, 0, $entry['start'] ), "\n" );
            $lineStart = $lineStart === false ? 0 : $lineStart + 1;
            $indent = substr( $this->code, $lineStart, $entry['start'] - $lineStart );
            if ( trim( $indent ) !== '' )
                continue;
            $inner = substr( $this->code, $entry['start'] + 2, $entry['end'] - $entry['start'] - 2 );
            // the statement with the new value, and whatever followed it in the comment (a note after it)
            $statement = substr( $inner, 0, $entry['value_offset'] ) . $literal . substr( $inner, $entry['value_offset'] + $entry['value_length'] );
            $statement = ltrim( $statement );
            $code = substr( $this->code, 0, $entry['start'] ) . $statement . substr( $this->code, $entry['end'] );
            return array( 'code' => $code, 'how' => 'uncommented', 'line' => $entry['line'] );
        }

        $line = "define( '" . $name . "', " . $literal . " );";
        $code = $this->code;
        $end = $this->blockEnd();
        if ( $end !== null )
        {
            $code = substr( $code, 0, $end ) . $line . "\n" . substr( $code, $end );
        }
        else
        {
            $block = self::BLOCK_BEGIN . "\n" . $line . "\n" . self::BLOCK_END . "\n";
            if ( trim( $code ) === '' )
                $code = "<?php\n" . $block;
            else
            {
                $at = $this->insertionPoint();
                $before = substr( $code, 0, $at );
                if ( $before !== '' && substr( $before, -1 ) !== "\n" )
                    $before .= "\n";
                $after = substr( $code, $at );
                // an empty line between the block and the statement or comment that follows it
                $code = $before . $block . ( $after !== '' && strpos( $after, '?>' ) !== 0 ? "\n" : '' ) . $after;
            }
        }
        $added = self::parseEntries( $code );
        $lineNo = 0;
        foreach ( $added as $entry )
            if ( $entry['name'] === $name && $entry['state'] === 'set' )
                $lineNo = $entry['line'];
        return array( 'code' => $code, 'how' => 'added', 'line' => $lineNo );
    }

    /**
     * The code without the setting: the define() is commented out where it is ("//" before each of its lines), so
     * its documentation keeps its place; a define() in the BEGIN/END block of exp:config:set is removed.
     *
     * @param string $name
     * @return array code, how ('commented', 'removed', 'absent'), line; or error
     */
    public function without( $name )
    {
        $set = $this->entriesOf( $name, 'set' );
        $conditional = $this->entriesOf( $name, 'conditional' );
        if ( $conditional )
            return array( 'error' => "$name is defined inside a condition or an expression on line " . $conditional[0]['line'] . "; change it by hand" );
        if ( !$set )
            return array( 'code' => $this->code, 'how' => 'absent', 'line' => 0 );
        if ( count( $set ) > 1 )
            return array( 'error' => "$name is defined more than once (lines " . implode( ', ', array_column( $set, 'line' ) ) . "); remove all but one by hand first" );
        $entry = $set[0];
        $code = $this->code;
        $lineStart = strrpos( substr( $code, 0, $entry['start'] ), "\n" );
        $lineStart = $lineStart === false ? 0 : $lineStart + 1;
        $lineEnd = strpos( $code, "\n", $entry['end'] );
        $lineEnd = $lineEnd === false ? strlen( $code ) : $lineEnd;
        $alone = trim( substr( $code, $lineStart, $entry['start'] - $lineStart ) ) === ''
              && preg_match( '#^\s*(//.*|/\*.*\*/\s*)?$#', substr( $code, $entry['end'], $lineEnd - $entry['end'] ) );
        if ( $entry['in_block'] && $alone )
        {
            $cut = $lineEnd < strlen( $code ) ? $lineEnd + 1 : $lineEnd;
            $code = substr( $code, 0, $lineStart ) . substr( $code, $cut );
            // the last one gone: the empty block goes too, with the empty line the addition put after it
            $empty = self::BLOCK_BEGIN . "\n" . self::BLOCK_END . "\n";
            $at = strpos( $code, $empty );
            if ( $at !== false && ( $at === 0 || $code[$at - 1] === "\n" ) )
            {
                $length = strlen( $empty );
                if ( substr( $code, $at + $length, 1 ) === "\n" )
                    ++$length;
                $code = substr( $code, 0, $at ) . substr( $code, $at + $length );
            }
            return array( 'code' => $code, 'how' => 'removed', 'line' => $entry['line'] );
        }
        if ( !$alone )
            return array( 'code' => substr( $code, 0, $entry['start'] ) . substr( $code, $entry['end'] ), 'how' => 'removed', 'line' => $entry['line'] );
        $statement = substr( $code, $entry['start'], $entry['end'] - $entry['start'] );
        $commented = '//' . str_replace( "\n", "\n//", $statement );
        return array( 'code' => substr( $code, 0, $entry['start'] ) . $commented . substr( $code, $entry['end'] ), 'how' => 'commented', 'line' => $entry['line'] );
    }

    /** @return int|null the offset of the start of the BLOCK_END line, when the block exists */
    protected function blockEnd()
    {
        $begin = strpos( $this->code, self::BLOCK_BEGIN );
        if ( $begin === false )
            return null;
        $end = strpos( $this->code, self::BLOCK_END, $begin );
        if ( $end === false )
            return null;
        $lineStart = strrpos( substr( $this->code, 0, $end ), "\n" );
        return $lineStart === false ? 0 : $lineStart + 1;
    }

    /**
     * Where a new BEGIN/END block goes: before the first top-level statement that is not a define() (and before the
     * comment lines directly above it), else before "?>", else at the end.
     *
     * @return int
     */
    protected function insertionPoint()
    {
        $tokens = self::tokens( $this->code );
        $n = count( $tokens );
        $defines = array();
        foreach ( $this->entries() as $entry )
            if ( $entry['state'] !== 'commented' )
                $defines[] = array( $entry['start'], $entry['end'] );
        $atStatementStart = true;
        $depth = 0;
        for ( $i = 0; $i < $n; ++$i )
        {
            $t = $tokens[$i];
            if ( $t[0] === T_OPEN_TAG || self::insignificant( $t ) || $t[0] === T_INLINE_HTML )
                continue;
            if ( $t[0] === T_CLOSE_TAG )
                return self::lineStart( $this->code, $t[3], array(), 0 );
            $inDefine = false;
            foreach ( $defines as $d )
                if ( $t[3] >= $d[0] && $t[3] < $d[1] )
                    $inDefine = true;
            if ( $inDefine )
            {
                $atStatementStart = true;
                continue;
            }
            if ( $atStatementStart && $depth === 0 )
                return self::lineStart( $this->code, $t[3], $tokens, $i );
            if ( $t[1] === '{' )
                ++$depth;
            else if ( $t[1] === '}' )
                --$depth;
            $atStatementStart = ( $t[1] === ';' || $t[1] === '}' ) && $depth === 0;
        }
        return strlen( $this->code );
    }

    /**
     * The start of the line of the token at $i, moved up over the comment lines directly above it (up to an empty
     * line), so a statement keeps its comment.
     */
    protected static function lineStart( $code, $offset, array $tokens, $i )
    {
        $start = $offset;
        for ( $j = $i - 1; $j >= 0; --$j )
        {
            $t = $tokens[$j];
            if ( $t[0] === T_WHITESPACE )
            {
                if ( preg_match( '/\n[ \t]*\n/', $t[1] ) )
                    break;
                continue;
            }
            if ( $t[0] === T_COMMENT || $t[0] === T_DOC_COMMENT )
            {
                $start = $t[3];
                continue;
            }
            break;
        }
        $lineStart = strrpos( substr( $code, 0, $start ), "\n" );
        return $lineStart === false ? 0 : $lineStart + 1;
    }

    /**
     * A line of output with the values of secret settings hidden: "define( 'X_PASS', 'secret' );" shows
     * "define( 'X_PASS', '********' );". For diffs and listings of config.php.
     *
     * @param string $text
     * @return string
     */
    public static function maskSecrets( $text )
    {
        return preg_replace_callback(
            '/(define\s*\(\s*([\'"])([A-Za-z_][A-Za-z0-9_]*)\2\s*,\s*)(.*?)(\s*\)\s*;|$)/m',
            function ( $m )
            {
                if ( !self::isSecret( $m[3] ) )
                    return $m[0];
                return $m[1] . "'********'" . $m[5];
            },
            (string)$text
        );
    }
}
