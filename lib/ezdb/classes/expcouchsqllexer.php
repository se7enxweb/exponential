<?php
/**
 * File containing the expCouchSQLLexer class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The SQL the kernel and the data table layer write, as tokens: the lexer under the Couchbase driver (which rewrites
 * the tokens into SQL++) and the CouchDB driver (which reads the statement's tables, columns and values from them).
 *
 * A token is array( type, text, value ):
 *
 *   str    a string literal ('...' with MySQL's escapes \' \\ \n \r \t \0 \Z and a doubled '); value: the decoded string
 *   num    a number (42, 3.5, 1e3, .5); value: int or float
 *   id     an identifier: a bare word that is no keyword, or `quoted` / "quoted" / [quoted]; value: the name
 *   kw     a keyword (SELECT, FROM, AND ...); value: upper case
 *   op     an operator (= <> != < <= > >= + - * / % & | ^ ~ || << >> !); value: the operator
 *   punc   ( ) , . ;   value: the character
 *   param  ? or :name   value: the text
 *
 * Comments (-- ..., # ..., /* ... *\/) are dropped. "..." is an identifier, as in standard SQL, SQLite and PostgreSQL
 * (the kernel quotes its strings with '...').
 *
 * @since 6.0.15
 * @package lib
 */
class expCouchSQLLexer
{
    /** The words read as keywords; any other bare word is an identifier (or a function name, when "(" follows) */
    const KEYWORDS = array(
        'ALL', 'AND', 'AS', 'ASC', 'BETWEEN', 'BY', 'CASE', 'CROSS', 'DEFAULT', 'DELETE', 'DESC', 'DISTINCT', 'DUPLICATE',
        'ELSE', 'END', 'ESCAPE', 'EXISTS', 'FALSE', 'FOR', 'FROM', 'FULL', 'GROUP', 'HAVING', 'IGNORE', 'IN', 'INNER', 'INSERT',
        'INTO', 'IS', 'JOIN', 'KEY', 'LEFT', 'LIKE', 'LIMIT', 'NATURAL', 'NOT', 'NULL', 'OFFSET', 'ON', 'OR', 'ORDER', 'OUTER',
        'REPLACE', 'RIGHT', 'SELECT', 'SET', 'THEN', 'TRUE', 'UNION', 'UPDATE', 'USING', 'VALUES', 'WHEN', 'WHERE', 'XOR', 'DIV',
        'TRUNCATE', 'TABLE', 'CREATE', 'DROP', 'ALTER', 'INDEX', 'UNIQUE', 'PRIMARY', 'TEMPORARY', 'IF', 'ADD', 'COLUMN',
        'COLLATE', 'BINARY', 'INDEXED', 'LOCK', 'UNLOCK', 'TABLES', 'WRITE', 'READ', 'SHARE', 'MODE', 'NOWAIT', 'ESCAPE',
    );

    /**
     * The tokens of a statement.
     *
     * @param string $sql
     * @param bool $backslashEscapes whether \ escapes in a string (MySQL's strings, which the Couchbase driver takes);
     *                               false: SQLite's and the standard's, where only a doubled ' does (the CouchDB driver)
     * @return array[] of array( type, text, value )
     * @throws expDBUnsupportedException on an unterminated string or quoted name
     */
    public static function tokenize( $sql, $backslashEscapes = true )
    {
        static $keywords = null;
        if ( $keywords === null )
            $keywords = array_flip( self::KEYWORDS );
        $sql = (string)$sql;
        $tokens = array();
        $n = strlen( $sql );
        $i = 0;
        while ( $i < $n )
        {
            $c = $sql[$i];
            if ( $c === ' ' || $c === "\t" || $c === "\n" || $c === "\r" || $c === "\f" )
            {
                $i++;
                continue;
            }
            if ( ( $c === '-' && $i + 1 < $n && $sql[$i + 1] === '-' ) || $c === '#' )
            {
                $end = strpos( $sql, "\n", $i );
                $i = $end === false ? $n : $end + 1;
                continue;
            }
            if ( $c === '/' && $i + 1 < $n && $sql[$i + 1] === '*' )
            {
                $end = strpos( $sql, '*/', $i + 2 );
                $i = $end === false ? $n : $end + 2;
                continue;
            }
            if ( $c === "'" )
            {
                $value = '';
                $j = $i + 1;
                $closed = false;
                while ( $j < $n )
                {
                    $d = $sql[$j];
                    if ( $backslashEscapes && $d === '\\' && $j + 1 < $n )
                    {
                        $e = $sql[$j + 1];
                        $map = array( 'n' => "\n", 'r' => "\r", 't' => "\t", '0' => "\0", 'Z' => "\x1a", 'b' => "\x08" );
                        $value .= isset( $map[$e] ) ? $map[$e] : $e;
                        $j += 2;
                        continue;
                    }
                    if ( $d === "'" )
                    {
                        if ( $j + 1 < $n && $sql[$j + 1] === "'" )
                        {
                            $value .= "'";
                            $j += 2;
                            continue;
                        }
                        $closed = true;
                        $j++;
                        break;
                    }
                    $value .= $d;
                    $j++;
                }
                if ( !$closed )
                    throw new expDBUnsupportedException( 'couch', 'unterminated string', substr( $sql, $i, 60 ) );
                $tokens[] = array( 'str', substr( $sql, $i, $j - $i ), $value );
                $i = $j;
                continue;
            }
            if ( $c === '`' || $c === '"' || $c === '[' )
            {
                $close = $c === '[' ? ']' : $c;
                $end = strpos( $sql, $close, $i + 1 );
                // a doubled quote inside a quoted name
                while ( $end !== false && $close !== ']' && $end + 1 < $n && $sql[$end + 1] === $close )
                    $end = strpos( $sql, $close, $end + 2 );
                if ( $end === false )
                    throw new expDBUnsupportedException( 'couch', 'unterminated quoted name', substr( $sql, $i, 60 ) );
                $name = str_replace( $close . $close, $close, substr( $sql, $i + 1, $end - $i - 1 ) );
                $tokens[] = array( 'id', substr( $sql, $i, $end - $i + 1 ), $name );
                $i = $end + 1;
                continue;
            }
            if ( ctype_digit( $c ) || ( $c === '.' && $i + 1 < $n && ctype_digit( $sql[$i + 1] ) ) )
            {
                if ( preg_match( '/\G(?:\d+\.?\d*|\.\d+)(?:[eE][+-]?\d+)?/', $sql, $m, 0, $i ) )
                {
                    $text = $m[0];
                    // a word that starts with digits (a table named 2fa_x): an identifier
                    if ( $i + strlen( $text ) < $n && ( ctype_alpha( $sql[$i + strlen( $text )] ) || $sql[$i + strlen( $text )] === '_' )
                         && preg_match( '/\G\w+/', $sql, $w, 0, $i ) )
                    {
                        $tokens[] = array( 'id', $w[0], $w[0] );
                        $i += strlen( $w[0] );
                        continue;
                    }
                    $isInt = preg_match( '/^\d+$/', $text ) && ( strlen( $text ) < 19 || $text <= (string)PHP_INT_MAX );
                    $tokens[] = array( 'num', $text, $isInt ? (int)$text : (float)$text );
                    $i += strlen( $text );
                    continue;
                }
            }
            if ( ctype_alpha( $c ) || $c === '_' || $c === '$' || ord( $c ) >= 0x80 )
            {
                preg_match( '/\G[\w$\x80-\xff]+/', $sql, $m, 0, $i );
                $word = $m[0];
                $upper = strtoupper( $word );
                if ( isset( $keywords[$upper] ) )
                    $tokens[] = array( 'kw', $word, $upper );
                else
                    $tokens[] = array( 'id', $word, $word );
                $i += strlen( $word );
                continue;
            }
            if ( $c === '?' )
            {
                $tokens[] = array( 'param', '?', '?' );
                $i++;
                continue;
            }
            if ( $c === ':' && $i + 1 < $n && ( ctype_alpha( $sql[$i + 1] ) || $sql[$i + 1] === '_' ) )
            {
                preg_match( '/\G:\w+/', $sql, $m, 0, $i );
                $tokens[] = array( 'param', $m[0], $m[0] );
                $i += strlen( $m[0] );
                continue;
            }
            $two = $i + 1 < $n ? $c . $sql[$i + 1] : $c;
            if ( in_array( $two, array( '<>', '!=', '<=', '>=', '||', '<<', '>>', '==' ), true ) )
            {
                $tokens[] = array( 'op', $two, $two === '==' ? '=' : $two );
                $i += 2;
                continue;
            }
            if ( strpos( '=<>+-*/%&|^~!', $c ) !== false )
            {
                $tokens[] = array( 'op', $c, $c );
                $i++;
                continue;
            }
            if ( strpos( '(),.;', $c ) !== false )
            {
                $tokens[] = array( 'punc', $c, $c );
                $i++;
                continue;
            }
            throw new expDBUnsupportedException( 'couch', 'character ' . json_encode( $c ), 'at ' . substr( $sql, $i, 40 ) );
        }
        // a trailing ;
        while ( $tokens && $tokens[count( $tokens ) - 1][0] === 'punc' && $tokens[count( $tokens ) - 1][2] === ';' )
            array_pop( $tokens );
        return $tokens;
    }

    /**
     * Whether a token is the keyword (or one of the keywords) given.
     *
     * @param array|null $token
     * @param string|string[] $keyword
     * @return bool
     */
    public static function isKeyword( $token, $keyword )
    {
        if ( !$token || $token[0] !== 'kw' )
            return false;
        return is_array( $keyword ) ? in_array( $token[2], $keyword, true ) : $token[2] === $keyword;
    }

    /**
     * Whether a token is the punctuation or operator given.
     *
     * @param array|null $token
     * @param string $char
     * @return bool
     */
    public static function isChar( $token, $char )
    {
        return $token && ( $token[0] === 'punc' || $token[0] === 'op' ) && $token[2] === $char;
    }

    /**
     * The index of the token that closes the parenthesis at $open.
     *
     * @param array[] $tokens
     * @param int $open
     * @return int the index of ")", or count( $tokens ) when there is none
     */
    public static function closing( array $tokens, $open )
    {
        $depth = 0;
        $n = count( $tokens );
        for ( $i = $open; $i < $n; $i++ )
        {
            if ( self::isChar( $tokens[$i], '(' ) )
                $depth++;
            elseif ( self::isChar( $tokens[$i], ')' ) )
            {
                $depth--;
                if ( $depth === 0 )
                    return $i;
            }
        }
        return $n;
    }

    /**
     * Splits tokens at the commas of the outermost level.
     *
     * @param array[] $tokens
     * @return array[][]
     */
    public static function splitTopLevel( array $tokens, $char = ',' )
    {
        $parts = array();
        $current = array();
        $depth = 0;
        foreach ( $tokens as $token )
        {
            if ( self::isChar( $token, '(' ) )
                $depth++;
            elseif ( self::isChar( $token, ')' ) )
                $depth--;
            if ( $depth === 0 && self::isChar( $token, $char ) )
            {
                $parts[] = $current;
                $current = array();
                continue;
            }
            $current[] = $token;
        }
        if ( $current || $parts )
            $parts[] = $current;
        return $parts;
    }

    /**
     * The index of the first keyword of $keywords at the outermost level from $from, or -1.
     *
     * @param array[] $tokens
     * @param string[] $keywords
     * @param int $from
     * @return int
     */
    public static function findTopLevel( array $tokens, array $keywords, $from = 0 )
    {
        $depth = 0;
        $n = count( $tokens );
        for ( $i = $from; $i < $n; $i++ )
        {
            if ( self::isChar( $tokens[$i], '(' ) )
                $depth++;
            elseif ( self::isChar( $tokens[$i], ')' ) )
                $depth--;
            elseif ( $depth === 0 && self::isKeyword( $tokens[$i], $keywords ) )
                return $i;
        }
        return -1;
    }

    /**
     * The value of a literal token (str, num, NULL, TRUE, FALSE, a signed number); null when the tokens are no literal.
     *
     * @param array[] $tokens one value's tokens
     * @param bool $isLiteral set to whether they were one literal
     * @return mixed
     */
    public static function literal( array $tokens, &$isLiteral )
    {
        $isLiteral = true;
        if ( count( $tokens ) === 1 )
        {
            $t = $tokens[0];
            if ( $t[0] === 'str' || $t[0] === 'num' )
                return $t[2];
            if ( self::isKeyword( $t, 'NULL' ) )
                return null;
            if ( self::isKeyword( $t, 'TRUE' ) )
                return 1;
            if ( self::isKeyword( $t, 'FALSE' ) )
                return 0;
        }
        if ( count( $tokens ) === 2 && $tokens[0][0] === 'op' && ( $tokens[0][2] === '-' || $tokens[0][2] === '+' ) && $tokens[1][0] === 'num' )
            return $tokens[0][2] === '-' ? -$tokens[1][2] : $tokens[1][2];
        $isLiteral = false;
        return null;
    }

    /**
     * The tokens as text again, one space apart (for messages and for engines that take the SQL as it was).
     *
     * @param array[] $tokens
     * @return string
     */
    public static function text( array $tokens )
    {
        $out = '';
        $previous = null;
        foreach ( $tokens as $t )
        {
            $glue = ' ';
            if ( $previous && ( self::isChar( $previous, '.' ) || self::isChar( $previous, '(' ) ) )
                $glue = '';
            if ( self::isChar( $t, '.' ) || self::isChar( $t, ')' ) || self::isChar( $t, ',' ) || ( self::isChar( $t, '(' ) && $previous && $previous[0] === 'id' ) )
                $glue = '';
            $out .= ( $out === '' ? '' : $glue ) . $t[1];
            $previous = $t;
        }
        return $out;
    }
}
?>
