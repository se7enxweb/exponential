<?php
/**
 * File containing the expSQLServerLexer class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * Splits a Transact-SQL statement into tokens without losing a byte: implode() of the token texts is the statement
 * again. Everything in expSQLServerDialect that has to know where a string literal, a comment or a bracketed name
 * starts and ends (paging, Unicode literals, bound parameters, replica routing, temporary tables) reads these tokens
 * instead of a regular expression over the whole statement, so a quote, a keyword or a "?" inside a literal or a
 * comment never changes what the statement does.
 *
 * Token types:
 *  - ws        white space
 *  - comment   -- to the end of the line, or a block comment (nested block comments are one token, as T-SQL reads them)
 *  - string    '...' (quotes doubled inside); nstring: N'...'
 *  - ident     [name] (] doubled inside), "name" ("" doubled inside), `name` (MySQL style, `` doubled inside)
 *  - number    123, 1.5, .5, 1e10; binary: 0x0A0B
 *  - word      keywords, names, @variables, @@functions, #temp and ##global temp tables
 *  - param     ?
 *  - punct     every other single character ( ) , ; . = < > + - * / % & | ^ ~ !
 *
 * A literal, bracket or comment that is not closed runs to the end of the statement and sets the token's 'open'
 * flag; the server refuses such a statement, and nothing here tries to repair it.
 *
 * The statement is read as bytes, not as UTF-8 characters: a byte from 0x80 on is part of a name, so a name in any
 * script works, and a literal that is not valid UTF-8 (binary data written as text) never stops the lexer.
 */
class expSQLServerLexer
{
    const WS = 'ws';
    const COMMENT = 'comment';
    const STRING = 'string';
    const NSTRING = 'nstring';
    const IDENT = 'ident';
    const NUMBER = 'number';
    const BINARY = 'binary';
    const WORD = 'word';
    const PARAM = 'param';
    const PUNCT = 'punct';

    /** One token at a time, anchored at the offset (\G). Block comments are handled in tokenize(). */
    const PATTERN = '/\G(?:
          (?P<ws>\s++)
        | (?P<lc>--[^\n]*+)
        | (?P<ns>[nN]\'(?:[^\']++|\'\')*+\'?)
        | (?P<s>\'(?:[^\']++|\'\')*+\'?)
        | (?P<br>\[(?:[^\]]++|\]\])*+\]?)
        | (?P<dq>"(?:[^"]++|"")*+"?)
        | (?P<bt>`(?:[^`]++|``)*+`?)
        | (?P<hex>0[xX][0-9a-fA-F]*+)
        | (?P<num>(?:[0-9]++(?:\.[0-9]*+)?|\.[0-9]++)(?:[eE][+-]?[0-9]++)?)
        | (?P<word>[A-Za-z_@#$\x80-\xFF][A-Za-z0-9_@#$\x80-\xFF]*+)
        | (?P<q>\?)
        )/xs';

    /** @var array the last statement and its tokens: the same statement is often read several times in a row */
    private static $last = array( null, null );

    /**
     * The tokens of $sql, each array( 'type' => ..., 'text' => ..., 'open' => bool ).
     *
     * @param string $sql
     * @return array[]
     */
    public static function tokenize( $sql )
    {
        $sql = (string)$sql;
        if ( self::$last[0] === $sql )
            return self::$last[1];
        $tokens = array();
        $length = strlen( $sql );
        $offset = 0;
        while ( $offset < $length )
        {
            if ( $sql[$offset] === '/' && $offset + 1 < $length && $sql[$offset + 1] === '*' )
            {
                $end = self::blockCommentEnd( $sql, $offset );
                $tokens[] = array( 'type' => self::COMMENT, 'text' => substr( $sql, $offset, min( $end, $length ) - $offset ), 'open' => $end > $length );
                $offset = min( $end, $length );
                continue;
            }
            if ( preg_match( self::PATTERN, $sql, $m, 0, $offset ) && $m[0] !== '' )
            {
                $text = $m[0];
                $open = false;
                if ( isset( $m['ws'] ) && $m['ws'] !== '' )
                    $type = self::WS;
                else if ( isset( $m['lc'] ) && $m['lc'] !== '' )
                    $type = self::COMMENT;
                else if ( isset( $m['ns'] ) && $m['ns'] !== '' )
                {
                    $type = self::NSTRING;
                    $open = !self::closed( substr( $text, 1 ), "'" );
                }
                else if ( isset( $m['s'] ) && $m['s'] !== '' )
                {
                    $type = self::STRING;
                    $open = !self::closed( $text, "'" );
                }
                else if ( isset( $m['br'] ) && $m['br'] !== '' )
                {
                    $type = self::IDENT;
                    $open = !self::closed( $text, ']' );
                }
                else if ( isset( $m['dq'] ) && $m['dq'] !== '' )
                {
                    $type = self::IDENT;
                    $open = !self::closed( $text, '"' );
                }
                else if ( isset( $m['bt'] ) && $m['bt'] !== '' )
                {
                    $type = self::IDENT;
                    $open = !self::closed( $text, '`' );
                }
                else if ( isset( $m['hex'] ) && $m['hex'] !== '' )
                    $type = self::BINARY;
                else if ( isset( $m['num'] ) && $m['num'] !== '' )
                    $type = self::NUMBER;
                else if ( isset( $m['word'] ) && $m['word'] !== '' )
                    $type = self::WORD;
                else
                    $type = self::PARAM;
                $tokens[] = array( 'type' => $type, 'text' => $text, 'open' => $open );
                $offset += strlen( $text );
                continue;
            }
            $tokens[] = array( 'type' => self::PUNCT, 'text' => $sql[$offset], 'open' => false );
            $offset++;
        }
        self::$last = array( $sql, $tokens );
        return $tokens;
    }

    /**
     * The statement again from its tokens.
     *
     * @param array[] $tokens
     * @return string
     */
    public static function join( array $tokens )
    {
        $sql = '';
        foreach ( $tokens as $token )
            $sql .= $token['text'];
        return $sql;
    }

    /**
     * Whether a token changes nothing a statement does (white space or a comment).
     *
     * @param array $token
     * @return bool
     */
    public static function isNoise( array $token )
    {
        return $token['type'] === self::WS || $token['type'] === self::COMMENT;
    }

    /**
     * The upper-case text of a word token, or '' for any other token.
     *
     * @param array $token
     * @return string
     */
    public static function keyword( array $token )
    {
        return $token['type'] === self::WORD ? strtoupper( $token['text'] ) : '';
    }

    /**
     * The value of a string literal token: without the quotes (and N), with doubled quotes made single.
     *
     * @param array $token
     * @return string
     */
    public static function literalValue( array $token )
    {
        $text = $token['text'];
        if ( $token['type'] === self::NSTRING )
            $text = substr( $text, 1 );
        $inner = substr( $text, 1, $token['open'] ? strlen( $text ) - 1 : strlen( $text ) - 2 );
        return str_replace( "''", "'", (string)$inner );
    }

    /**
     * The name in an identifier token, without its brackets or quotes; the text of any other token.
     *
     * @param array $token
     * @return string
     */
    public static function identifierValue( array $token )
    {
        if ( $token['type'] !== self::IDENT )
            return $token['text'];
        $text = $token['text'];
        $close = array( '[' => ']', '"' => '"', '`' => '`' );
        $quote = $close[$text[0]];
        $inner = substr( $text, 1, $token['open'] ? strlen( $text ) - 1 : strlen( $text ) - 2 );
        return str_replace( $quote . $quote, $quote, (string)$inner );
    }

    /**
     * The tokens that count, with their parenthesis depth: array( index into $tokens, depth ) for every token that
     * is not white space or a comment. A "(" and its ")" have the depth outside them; the tokens between, one more.
     *
     * @param array[] $tokens
     * @return array[]
     */
    public static function significant( array $tokens )
    {
        $list = array();
        $depth = 0;
        foreach ( $tokens as $i => $token )
        {
            if ( self::isNoise( $token ) )
                continue;
            if ( $token['type'] === self::PUNCT && $token['text'] === ')' )
                $depth = max( 0, $depth - 1 );
            $list[] = array( $i, $depth );
            if ( $token['type'] === self::PUNCT && $token['text'] === '(' )
                $depth++;
        }
        return $list;
    }

    /**
     * Whether a quoted token ends with its closing quote (not with half of a doubled one).
     *
     * @param string $text the token from its opening quote on
     * @param string $quote the closing quote
     * @return bool
     */
    private static function closed( $text, $quote )
    {
        $length = strlen( $text );
        if ( $length < 2 || $text[$length - 1] !== $quote )
            return false;
        // Inside there are only doubled quotes: closed when the run of quotes before the last one is even.
        $inner = substr( $text, 1, $length - 2 );
        $run = strlen( $inner ) - strlen( rtrim( $inner, $quote ) );
        return $run % 2 === 0;
    }

    /**
     * The offset after the block comment that starts at $offset; nested comments count, as in T-SQL. Past the end
     * of the statement (length + 1) when the comment is not closed.
     *
     * @param string $sql
     * @param int $offset
     * @return int
     */
    private static function blockCommentEnd( $sql, $offset )
    {
        $length = strlen( $sql );
        $depth = 0;
        $i = $offset;
        while ( $i < $length - 1 )
        {
            $pair = $sql[$i] . $sql[$i + 1];
            if ( $pair === '/*' )
            {
                $depth++;
                $i += 2;
                continue;
            }
            if ( $pair === '*/' )
            {
                $depth--;
                $i += 2;
                if ( $depth === 0 )
                    return $i;
                continue;
            }
            $i++;
        }
        return $length + 1;
    }
}
