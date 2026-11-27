<?php
/**
 * File containing the expDb2Dialect class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The Db2 SQL that expDb2DB writes, as pure functions of their arguments: no connection, no settings. Every rule here
 * is tested without a server (tests/tests/lib/ezdb/db2).
 *
 * Supported servers: IBM Db2 for Linux, UNIX and Windows 11.5 and 12.1. 11.5 is the floor because of LIMIT and
 * OFFSET, DROP ... IF EXISTS and CREATE ... IF NOT EXISTS (the kernel writes them), HASH() for md5() and LISTAGG().
 * Statements are read by expSQLServerLexer, which splits any SQL into tokens without losing a byte; nothing here
 * rewrites a statement with a regular expression over its text, so a keyword inside a string literal or a comment
 * never changes what a statement does.
 *
 * What the kernel writes that Db2 reads differently, and what this class makes of it (rewrite()):
 *
 *  - Names: Db2 stores an unquoted name in upper case and a quoted one as written. A MySQL `name` becomes "NAME", so
 *    it is the same table or column as the unquoted name. A column named like a special register (user) is quoted
 *    where it stands as a name; Db2 takes the other words the kernel uses as names (value, count, time ...) as they
 *    are.
 *  - Text functions count characters: LENGTH() is CHARACTER_LENGTH(x, CODEUNITS32) (Db2's LENGTH() counts bytes),
 *    SUBSTRING(s, f, n) and SUBSTR(s, f, n) are SUBSTRING(s, f, n, CODEUNITS32) (SUBSTR() fails past the end of the
 *    text, SUBSTRING() does not), CONCAT(a, b, c) (a || b || c) (CONCAT() takes two), MD5(x) the hex text of
 *    HASH(x, 0), UNIX_TIMESTAMP() the seconds since 1970 of the UTC clock.
 *  - The bit operators of the language masks: a & b BITAND(a, b), a | b BITOR(a, b), ~a BITNOT(a).
 *  - LIKE: Db2 has no escape character unless the statement names one. A pattern with a backslash gets ESCAPE '\'.
 *  - SELECT ... FOR UPDATE: Db2 refuses FOR UPDATE with ORDER BY or a join; the rows are locked with the isolation
 *    clause WITH RS USE AND KEEP UPDATE LOCKS instead, which every SELECT takes.
 *  - SELECT without FROM: FROM SYSIBM.SYSDUMMY1 (Db2's one-row table).
 *  - TRUNCATE TABLE t: DELETE FROM t (Db2's TRUNCATE must be the first statement of a unit of work).
 *  - CREATE TEMPORARY TABLE: a created global temporary table (see expDb2DB).
 *
 * LIMIT n, LIMIT n OFFSET m, LIMIT m, n, IFNULL(), NOW(), GREATEST(), LEAST(), DROP ... IF EXISTS and
 * CREATE ... IF NOT EXISTS are Db2's own words since 11.1 and 11.5 and stay as they are.
 */
class expDb2Dialect
{
    /** Longest name Db2 11.5 and 12.1 accept for a table, a column or an index, in bytes */
    const MAX_IDENTIFIER_LENGTH = 128;

    /** Most values of one IN list (Db2 takes far more; the statement stays readable) */
    const MAX_IN_LIST = 1000;

    /** Lowest supported version */
    const MIN_VERSION = '11.5';

    /** The default port of the server */
    const DEFAULT_PORT = 50000;

    /** Db2's one-row table, for a SELECT of expressions only */
    const ONE_ROW_TABLE = 'SYSIBM.SYSDUMMY1';

    /** The isolation clause that locks the rows a SELECT reads until the end of the transaction */
    const LOCK_CLAUSE = 'WITH RS USE AND KEEP UPDATE LOCKS';

    /** The string units of text functions and of the schema's VARCHAR columns */
    const STRING_UNITS = 'CODEUNITS32';

    /**
     * The reserved words of Db2 for LUW (SQL Reference, "Reserved schema names and reserved words", the words of the
     * SQL standard Db2 refuses as plain names in some place): a table or column of one of these names is quoted.
     *
     * @var string[]
     */
    const RESERVED_WORDS = array(
        'ADD', 'ALL', 'ALTER', 'AND', 'ANY', 'AS', 'ASC', 'BETWEEN', 'BY', 'CASE', 'CAST', 'CHECK', 'COLUMN', 'CONSTRAINT',
        'CREATE', 'CROSS', 'CURRENT', 'CURRENT_DATE', 'CURRENT_PATH', 'CURRENT_SCHEMA', 'CURRENT_SERVER', 'CURRENT_TIME',
        'CURRENT_TIMESTAMP', 'CURRENT_TIMEZONE', 'CURRENT_USER', 'DEFAULT', 'DELETE', 'DESC', 'DISTINCT', 'DROP', 'ELSE',
        'END', 'EXCEPT', 'EXISTS', 'FETCH', 'FOR', 'FOREIGN', 'FROM', 'FULL', 'GRANT', 'GROUP', 'HAVING', 'IN', 'INNER',
        'INSERT', 'INTERSECT', 'INTO', 'IS', 'JOIN', 'LEFT', 'LIKE', 'LIMIT', 'NOT', 'NULL', 'OFFSET', 'ON', 'OR', 'ORDER',
        'OUTER', 'PRIMARY', 'REFERENCES', 'REVOKE', 'RIGHT', 'SELECT', 'SESSION_USER', 'SET', 'SOME', 'SYSTEM_USER',
        'TABLE', 'THEN', 'TO', 'UNION', 'UNIQUE', 'UPDATE', 'USER', 'USING', 'VALUES', 'WHEN', 'WHERE', 'WITH',
    );

    /**
     * Reserved words that are names in the statements Exponential writes: the special registers USER and its
     * kind, which Db2 reads as the register where a column of that name is meant. rewrite() quotes them where they
     * stand as a name.
     *
     * @var string[]
     */
    const NAME_WORDS = array( 'USER', 'SESSION_USER', 'SYSTEM_USER', 'CURRENT_USER' );

    /** MySQL/PostgreSQL functions that only have another name here */
    const RENAMED_FUNCTIONS = array( 'UCASE' => 'UPPER', 'LCASE' => 'LOWER' );

    /** Calls rewrite() replaces as a whole */
    const REWRITTEN_CALLS = array( 'CONCAT', 'SUBSTRING', 'SUBSTR', 'MD5', 'UNIX_TIMESTAMP', 'LENGTH', 'CHAR_LENGTH', 'CHARACTER_LENGTH', 'CURDATE' );

    /** @var array[] RESERVED_WORDS and NAME_WORDS as keys, per class */
    private static $lists = array();

    // ------------------------------------------------------------------ names

    /**
     * @param string $const RESERVED_WORDS or NAME_WORDS
     * @return array the words of the class as keys
     */
    protected static function wordSet( $const )
    {
        $key = static::class . '::' . $const;
        if ( !isset( self::$lists[$key] ) )
            self::$lists[$key] = array_fill_keys( constant( static::class . '::' . $const ), true );
        return self::$lists[$key];
    }

    /**
     * Whether $word is a reserved word (any case).
     *
     * @param string $word
     * @return bool
     */
    public static function isReservedWord( $word )
    {
        $set = static::wordSet( 'RESERVED_WORDS' );
        return isset( $set[strtoupper( (string)$word )] );
    }

    /**
     * One name in double quotes, in the case the server folds an unquoted name to, with a '"' inside doubled. An
     * empty name, a name with a NUL byte and a name that is too long are refused.
     *
     * @param string $name
     * @return string
     * @throws InvalidArgumentException
     */
    public static function quoteIdentifier( $name )
    {
        $name = (string)$name;
        static::checkIdentifier( $name );
        return '"' . str_replace( '"', '""', static::fold( $name ) ) . '"';
    }

    /**
     * A name, dotted or not, in double quotes part by part.
     *
     * @param string $name
     * @return string
     */
    public static function quoteName( $name )
    {
        $parts = array();
        foreach ( explode( '.', (string)$name ) as $part )
            $parts[] = static::quoteIdentifier( $part );
        return implode( '.', $parts );
    }

    /**
     * A name as it may stand in a statement: quoted when it is a reserved word or is no plain name, else as it is.
     *
     * @param string $name
     * @return string
     */
    public static function quoteIdentifierIfNeeded( $name )
    {
        $name = (string)$name;
        if ( !static::isReservedWord( $name ) && preg_match( '/^[A-Za-z][A-Za-z0-9_$]*$/', $name ) )
            return $name;
        return static::quoteIdentifier( $name );
    }

    /**
     * @param string $name
     * @throws InvalidArgumentException when $name is no name the server takes
     */
    public static function checkIdentifier( $name )
    {
        $name = (string)$name;
        if ( $name === '' || strpos( $name, "\0" ) !== false )
            throw new InvalidArgumentException( 'An empty name or a name with a NUL byte is no identifier' );
        if ( strlen( $name ) > static::MAX_IDENTIFIER_LENGTH )
            throw new InvalidArgumentException( "The name '$name' is longer than " . static::MAX_IDENTIFIER_LENGTH . ' bytes' );
    }

    /**
     * A name in the case the server stores an unquoted name in: upper case on Db2 (ASCII letters only).
     *
     * @param string $name
     * @return string
     */
    public static function fold( $name )
    {
        return strtr( (string)$name, 'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
    }

    // ------------------------------------------------------------------ literals

    /**
     * Text for inside a string literal: a quote doubled. A backslash is an ordinary character in Db2.
     *
     * @param string $value
     * @return string
     */
    public static function escapeString( $value )
    {
        return str_replace( "'", "''", (string)$value );
    }

    /**
     * @param string $value
     * @return string
     */
    public static function stringLiteral( $value )
    {
        return "'" . static::escapeString( $value ) . "'";
    }

    /**
     * A PHP value as a literal: NULL, 1/0, digits, a float in plain notation, else the escaped text in quotes.
     *
     * @param mixed $value
     * @return string
     */
    public static function literal( $value )
    {
        return expDBSQLHelpers::literal( array( static::class, 'escapeString' ), $value );
    }

    // ------------------------------------------------------------------ statements

    /**
     * The first keyword of a statement (SELECT, INSERT, UPDATE, DELETE, CREATE, WITH, VALUES ...), or ''.
     *
     * @param string $sql
     * @return string
     */
    public static function statementKind( $sql )
    {
        foreach ( expSQLServerLexer::tokenize( (string)$sql ) as $token )
        {
            if ( expSQLServerLexer::isNoise( $token ) || ( $token['type'] === expSQLServerLexer::PUNCT && $token['text'] === '(' ) )
                continue;
            return expSQLServerLexer::keyword( $token );
        }
        return '';
    }

    /**
     * Whether a statement changes the schema (and with it the table catalogue the driver keeps).
     *
     * @param string $sql
     * @return bool
     */
    public static function isSchemaChange( $sql )
    {
        return in_array( static::statementKind( $sql ), array( 'CREATE', 'ALTER', 'DROP', 'RENAME', 'COMMENT' ), true );
    }

    /**
     * A statement of the kernel (MySQL words, as far as they reach the driver) in Db2's words; see the class
     * comment for the rules. Statements of the schema (CREATE, ALTER, DROP ...) only get their backticks as quotes:
     * they come from the schema handler in Db2's words already, and a trigger body is no place for rewriting.
     *
     * @param string $sql
     * @return string
     */
    public static function rewrite( $sql )
    {
        $sql = trim( (string)$sql );
        while ( substr( $sql, -1 ) === ';' )
            $sql = rtrim( substr( $sql, 0, -1 ) );
        $kind = static::statementKind( $sql );
        if ( in_array( $kind, array( 'CREATE', 'ALTER', 'DROP', 'RENAME', 'COMMENT', 'CALL', 'SET', 'GRANT', 'REVOKE', 'DECLARE' ), true ) )
            return static::backticksToQuotes( $sql );
        if ( $kind === 'TRUNCATE' )
            return static::truncateToDelete( $sql );
        $sql = static::bitOperators( $sql );
        $sql = static::rewriteTokens( expSQLServerLexer::tokenize( $sql ) );
        if ( $kind === 'SELECT' || $kind === 'WITH' )
        {
            $sql = static::pagingClause( $sql );
            $sql = static::lockingForUpdate( $sql );
            if ( $kind === 'SELECT' )
                $sql = static::addOneRowTable( $sql );
        }
        return $sql;
    }

    /**
     * The paging of a SELECT as the server takes it: Db2 takes MySQL's LIMIT clauses as they are.
     *
     * @param string $sql
     * @return string
     */
    public static function pagingClause( $sql )
    {
        return $sql;
    }

    /**
     * The table a DROP TABLE statement drops (its name in lower case, as the schema handler names its sequence
     * after it), or null.
     *
     * @param string $sql
     * @return string|null
     */
    public static function droppedTable( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( trim( (string)$sql ) );
        $significant = expSQLServerLexer::significant( $tokens );
        $words = array();
        foreach ( $significant as $entry )
            $words[] = $entry;
        if ( count( $words ) < 3 || expSQLServerLexer::keyword( $tokens[$words[0][0]] ) !== 'DROP'
             || expSQLServerLexer::keyword( $tokens[$words[1][0]] ) !== 'TABLE' )
            return null;
        $n = 2;
        if ( count( $words ) === 5 && expSQLServerLexer::keyword( $tokens[$words[2][0]] ) === 'IF'
             && expSQLServerLexer::keyword( $tokens[$words[3][0]] ) === 'EXISTS' )
            $n = 4;
        if ( count( $words ) !== $n + 1 )
            return null;
        return strtolower( expSQLServerLexer::identifierValue( $tokens[$words[$n][0]] ) );
    }

    /**
     * The table an INSERT statement writes to (lower case), or null: lastSerialID() without a table reads the
     * sequence of the last table this connection inserted into.
     *
     * @param string $sql
     * @return string|null
     */
    public static function insertTable( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( trim( (string)$sql ) );
        $significant = expSQLServerLexer::significant( $tokens );
        if ( count( $significant ) < 3 || expSQLServerLexer::keyword( $tokens[$significant[0][0]] ) !== 'INSERT'
             || expSQLServerLexer::keyword( $tokens[$significant[1][0]] ) !== 'INTO' )
            return null;
        $token = $tokens[$significant[2][0]];
        if ( $token['type'] !== expSQLServerLexer::WORD && $token['type'] !== expSQLServerLexer::IDENT )
            return null;
        $name = expSQLServerLexer::identifierValue( $token );
        // schema.table
        if ( isset( $significant[4] ) && $tokens[$significant[3][0]]['text'] === '.' )
            $name = expSQLServerLexer::identifierValue( $tokens[$significant[4][0]] );
        return strtolower( $name );
    }

    /**
     * The catalogue query that finds a table, sequence, trigger, view or index by its stored name (one row when it
     * exists), in the current schema.
     *
     * @param string $type TABLE, INDEX, SEQUENCE, VIEW or TRIGGER
     * @param string $name
     * @return string
     */
    public static function existsQuery( $type, $name )
    {
        $literal = static::stringLiteral( static::fold( $name ) );
        switch ( $type )
        {
            case 'INDEX':
                return "SELECT 1 AS found FROM SYSCAT.INDEXES WHERE INDSCHEMA = CURRENT SCHEMA AND INDNAME = $literal";
            case 'SEQUENCE':
                return "SELECT 1 AS found FROM SYSCAT.SEQUENCES WHERE SEQSCHEMA = CURRENT SCHEMA AND SEQNAME = $literal";
            case 'TRIGGER':
                return "SELECT 1 AS found FROM SYSCAT.TRIGGERS WHERE TRIGSCHEMA = CURRENT SCHEMA AND TRIGNAME = $literal";
        }
        return "SELECT 1 AS found FROM SYSCAT.TABLES WHERE TABSCHEMA = CURRENT SCHEMA AND TABNAME = $literal";
    }

    /**
     * The bit operators of MySQL and PostgreSQL as functions: a & b as BITAND(a, b), a | b as BITOR(a, b) (|| stays
     * the concatenation), ~a as BITNOT(a). An operand is a name (qualified or not), a number, a "?", a call or a
     * parenthesized expression, as in the kernel's language mask conditions (ezcontentobject.language_mask & 6 > 0);
     * & and | bind tighter than a comparison there, as in MySQL.
     *
     * @param string $sql
     * @return string
     */
    public static function bitOperators( $sql )
    {
        $sql = (string)$sql;
        if ( strpbrk( $sql, '&|~' ) === false )
            return $sql;
        for ( $round = 0; $round < 200; $round++ )
        {
            $tokens = expSQLServerLexer::tokenize( $sql );
            $count = count( $tokens );
            $done = true;
            for ( $i = 0; $i < $count; $i++ )
            {
                $t = $tokens[$i];
                if ( $t['type'] !== expSQLServerLexer::PUNCT )
                    continue;
                if ( !in_array( $t['text'], array( '&', '|', '~' ), true ) )
                    continue;
                if ( $t['text'] === '|' && ( ( isset( $tokens[$i + 1] ) && $tokens[$i + 1]['text'] === '|' ) || ( $i > 0 && $tokens[$i - 1]['text'] === '|' ) ) )
                    continue;
                $right = static::operandAfter( $tokens, $i );
                if ( $right === null )
                    continue;
                if ( $t['text'] === '~' )
                {
                    $sql = expSQLServerLexer::join( array_slice( $tokens, 0, $i ) ) . 'BITNOT(' . trim( expSQLServerLexer::join( array_slice( $tokens, $i + 1, $right - $i ) ) ) . ')'
                         . expSQLServerLexer::join( array_slice( $tokens, $right + 1 ) );
                    $done = false;
                    break;
                }
                $left = static::operandBefore( $tokens, $i );
                if ( $left === null )
                    continue;
                $function = $t['text'] === '&' ? 'BITAND' : 'BITOR';
                $sql = expSQLServerLexer::join( array_slice( $tokens, 0, $left ) ) . $function . '('
                     . trim( expSQLServerLexer::join( array_slice( $tokens, $left, $i - $left ) ) ) . ', '
                     . trim( expSQLServerLexer::join( array_slice( $tokens, $i + 1, $right - $i ) ) ) . ')'
                     . expSQLServerLexer::join( array_slice( $tokens, $right + 1 ) );
                $done = false;
                break;
            }
            if ( $done )
                break;
        }
        return $sql;
    }

    /**
     * The index of the first token of the operand that ends just before $at, or null.
     */
    protected static function operandBefore( array $tokens, $at )
    {
        $j = $at - 1;
        while ( $j >= 0 && expSQLServerLexer::isNoise( $tokens[$j] ) )
            $j--;
        if ( $j < 0 )
            return null;
        if ( $tokens[$j]['text'] === ')' )
        {
            $depth = 0;
            for ( $k = $j; $k >= 0; $k-- )
            {
                if ( $tokens[$k]['type'] !== expSQLServerLexer::PUNCT )
                    continue;
                if ( $tokens[$k]['text'] === ')' )
                    $depth++;
                else if ( $tokens[$k]['text'] === '(' && --$depth === 0 )
                    break;
            }
            if ( $k < 0 )
                return null;
            $before = $k - 1;
            while ( $before >= 0 && expSQLServerLexer::isNoise( $tokens[$before] ) )
                $before--;
            // a call: the function's name belongs to the operand
            if ( $before >= 0 && $tokens[$before]['type'] === expSQLServerLexer::WORD
                 && !in_array( strtoupper( $tokens[$before]['text'] ), array( 'AND', 'OR', 'NOT', 'WHERE', 'ON', 'IN', 'SELECT', 'WHEN', 'THEN', 'ELSE', 'BY', 'HAVING' ), true ) )
                return $before;
            return $k;
        }
        if ( !in_array( $tokens[$j]['type'], array( expSQLServerLexer::WORD, expSQLServerLexer::IDENT, expSQLServerLexer::NUMBER, expSQLServerLexer::PARAM ), true ) )
            return null;
        $start = $j;
        while ( $start >= 2 && $tokens[$start - 1]['text'] === '.'
                && in_array( $tokens[$start - 2]['type'], array( expSQLServerLexer::WORD, expSQLServerLexer::IDENT ), true ) )
            $start -= 2;
        return $start;
    }

    /**
     * The index of the last token of the operand that starts after $at, or null.
     */
    protected static function operandAfter( array $tokens, $at )
    {
        $count = count( $tokens );
        $j = $at + 1;
        while ( $j < $count && expSQLServerLexer::isNoise( $tokens[$j] ) )
            $j++;
        if ( $j >= $count )
            return null;
        if ( $tokens[$j]['text'] === '-' || $tokens[$j]['text'] === '~' )
        {
            $inner = static::operandAfter( $tokens, $j );
            return $inner;
        }
        if ( $tokens[$j]['text'] === '(' )
            return static::matchingParen( $tokens, $j );
        if ( !in_array( $tokens[$j]['type'], array( expSQLServerLexer::WORD, expSQLServerLexer::IDENT, expSQLServerLexer::NUMBER, expSQLServerLexer::PARAM ), true ) )
            return null;
        $end = $j;
        while ( $end + 2 < $count && $tokens[$end + 1]['text'] === '.'
                && in_array( $tokens[$end + 2]['type'], array( expSQLServerLexer::WORD, expSQLServerLexer::IDENT ), true ) )
            $end += 2;
        // a call
        $next = $end + 1;
        while ( $next < $count && expSQLServerLexer::isNoise( $tokens[$next] ) )
            $next++;
        if ( $tokens[$end]['type'] === expSQLServerLexer::WORD && $next < $count && $tokens[$next]['text'] === '(' )
            return static::matchingParen( $tokens, $next );
        return $end;
    }

    /**
     * MySQL's `name` as "NAME" (the case of an unquoted name), everything else as it is.
     *
     * @param string $sql
     * @return string
     */
    public static function backticksToQuotes( $sql )
    {
        $out = '';
        foreach ( expSQLServerLexer::tokenize( (string)$sql ) as $token )
        {
            if ( $token['type'] === expSQLServerLexer::IDENT && $token['text'][0] === '`' )
                $out .= static::backtickName( expSQLServerLexer::identifierValue( $token ) );
            else
                $out .= $token['text'];
        }
        return $out;
    }

    /**
     * A name MySQL wrote in backticks, as it stands here.
     *
     * @param string $name
     * @return string
     */
    protected static function backtickName( $name )
    {
        return '"' . str_replace( '"', '""', static::fold( $name ) ) . '"';
    }

    /**
     * TRUNCATE [TABLE] t as DELETE FROM t.
     *
     * @param string $sql
     * @return string
     */
    public static function truncateToDelete( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( static::backticksToQuotes( $sql ) );
        $significant = expSQLServerLexer::significant( $tokens );
        $n = 1;
        if ( isset( $significant[$n] ) && expSQLServerLexer::keyword( $tokens[$significant[$n][0]] ) === 'TABLE' )
            $n++;
        if ( !isset( $significant[$n] ) )
            return $sql;
        // Db2's own TRUNCATE TABLE t IMMEDIATE: the trailing words go
        $name = expSQLServerLexer::join( array( $tokens[$significant[$n][0]] ) );
        if ( isset( $significant[$n + 2] ) && $tokens[$significant[$n + 1][0]]['text'] === '.' )
            $name .= '.' . expSQLServerLexer::join( array( $tokens[$significant[$n + 2][0]] ) );
        return 'DELETE FROM ' . $name;
    }

    /**
     * The token-level rules of rewrite(): names, functions, LIKE patterns.
     *
     * @param array[] $tokens
     * @return string
     */
    protected static function rewriteTokens( array $tokens )
    {
        $nameWords = static::wordSet( 'NAME_WORDS' );
        $count = count( $tokens );
        $out = '';
        $prevWord = '';
        $prevText = '';
        for ( $i = 0; $i < $count; $i++ )
        {
            $token = $tokens[$i];
            if ( expSQLServerLexer::isNoise( $token ) )
            {
                $out .= $token['text'];
                continue;
            }
            $next = static::nextSignificant( $tokens, $i );
            $nextToken = $next === null ? null : $tokens[$next];
            $nextText = $nextToken === null ? '' : $nextToken['text'];
            $nextWord = $nextToken === null ? '' : expSQLServerLexer::keyword( $nextToken );

            if ( $token['type'] === expSQLServerLexer::IDENT && $token['text'][0] === '`' )
            {
                $text = static::backtickName( expSQLServerLexer::identifierValue( $token ) );
            }
            else if ( $token['type'] === expSQLServerLexer::WORD )
            {
                $word = strtoupper( $token['text'] );
                $text = $token['text'];
                $isCall = $nextText === '(' && $prevText !== '.';
                $renamed = static::RENAMED_FUNCTIONS;
                if ( $isCall && isset( $renamed[$word] ) )
                    $text = $renamed[$word];
                else if ( $isCall && in_array( $word, static::REWRITTEN_CALLS, true ) )
                {
                    $close = static::matchingParen( $tokens, $next );
                    if ( $close !== null )
                    {
                        $args = static::splitArguments( array_slice( $tokens, $next + 1, $close - $next - 1 ) );
                        $replacement = static::rewriteCall( $word, $args );
                        if ( $replacement !== null )
                        {
                            $out .= $replacement;
                            $i = $close;
                            $prevWord = '';
                            $prevText = ')';
                            continue;
                        }
                    }
                }
                else if ( !$isCall && isset( $nameWords[$word] ) && static::standsAsName( $word, $prevWord, $prevText, $nextToken, $nextWord ) )
                    $text = '"' . static::fold( $token['text'] ) . '"';
            }
            else if ( $token['type'] === expSQLServerLexer::STRING && $prevWord === 'LIKE' && strpos( $token['text'], '\\' ) !== false
                      && $nextWord !== 'ESCAPE' )
            {
                $text = $token['text'] . " ESCAPE '\\'";
            }
            else
                $text = $token['text'];
            $out .= $text;
            $prevWord = expSQLServerLexer::keyword( $token );
            $prevText = $token['text'];
        }
        return $out;
    }

    /**
     * Whether a word of NAME_WORDS stands as a name where it is: after a "." (t.user), in a column list, as an
     * alias; not after CURRENT, TO, FROM or GRANT (CURRENT USER, GRANT ... TO USER x).
     */
    protected static function standsAsName( $word, $prevWord, $prevText, $nextToken, $nextWord )
    {
        if ( in_array( $prevWord, array( 'CURRENT', 'GRANT', 'TO' ), true ) )
            return false;
        return true;
    }

    /**
     * The rewrite of a call of a MySQL function the server lacks, from its arguments (each a token list); null
     * when the call is left as it is.
     *
     * @param string $word
     * @param array[] $args
     * @return string|null
     */
    protected static function rewriteCall( $word, array $args )
    {
        $text = array();
        foreach ( $args as $arg )
            $text[] = trim( static::rewriteTokens( $arg ) );
        if ( count( $text ) === 1 && $text[0] === '' )
            $text = array();
        switch ( $word )
        {
            case 'CONCAT':
                return $text ? static::concatString( $text ) : null;
            case 'SUBSTRING':
            case 'SUBSTR':
                // SUBSTRING(s FROM f FOR n) and a call with its string units given are left as they are
                if ( count( $text ) < 2 || count( $text ) > 3 || preg_match( '/^(CODEUNITS16|CODEUNITS32|OCTETS)$/i', end( $text ) ) )
                    return null;
                return static::subString( $text[0], $text[1], isset( $text[2] ) ? $text[2] : null );
            case 'LENGTH':
            case 'CHAR_LENGTH':
            case 'CHARACTER_LENGTH':
                return count( $text ) === 1 ? static::charLength( $text[0] ) : null;
            case 'MD5':
                return count( $text ) === 1 ? static::md5( $text[0] ) : null;
            case 'CURDATE':
                return $text ? null : 'CURRENT DATE';
            case 'UNIX_TIMESTAMP':
                return static::unixTimestamp( $text ? $text[0] : null );
        }
        return null;
    }

    /**
     * The index of the next token that is not white space or a comment, or null.
     */
    protected static function nextSignificant( array $tokens, $i )
    {
        $count = count( $tokens );
        for ( $j = $i + 1; $j < $count; $j++ )
            if ( !expSQLServerLexer::isNoise( $tokens[$j] ) )
                return $j;
        return null;
    }

    /**
     * The index of the ")" that closes the "(" at $open, or null.
     *
     * @param array[] $tokens
     * @param int $open
     * @return int|null
     */
    public static function matchingParen( array $tokens, $open )
    {
        $depth = 0;
        $count = count( $tokens );
        for ( $i = $open; $i < $count; $i++ )
        {
            if ( $tokens[$i]['type'] !== expSQLServerLexer::PUNCT )
                continue;
            if ( $tokens[$i]['text'] === '(' )
                $depth++;
            else if ( $tokens[$i]['text'] === ')' )
            {
                $depth--;
                if ( $depth === 0 )
                    return $i;
            }
        }
        return null;
    }

    /**
     * The arguments of a call (the tokens between its parentheses) split at the commas outside nested parentheses.
     *
     * @param array[] $tokens
     * @return array[]
     */
    public static function splitArguments( array $tokens )
    {
        $args = array( array() );
        $depth = 0;
        foreach ( $tokens as $token )
        {
            if ( $token['type'] === expSQLServerLexer::PUNCT )
            {
                if ( $token['text'] === '(' )
                    $depth++;
                else if ( $token['text'] === ')' )
                    $depth--;
                else if ( $token['text'] === ',' && $depth === 0 )
                {
                    $args[] = array();
                    continue;
                }
            }
            $args[count( $args ) - 1][] = $token;
        }
        return $args;
    }

    /**
     * The significant tokens at parenthesis depth 0 with their upper-case words: array( array( index, WORD, text ) ).
     *
     * @param array[] $tokens
     * @return array[]
     */
    public static function outerWords( array $tokens )
    {
        $list = array();
        foreach ( expSQLServerLexer::significant( $tokens ) as $entry )
            if ( $entry[1] === 0 )
                $list[] = array( $entry[0], expSQLServerLexer::keyword( $tokens[$entry[0]] ), $tokens[$entry[0]]['text'] );
        return $list;
    }

    /**
     * Where a trailing FOR UPDATE [OF ...] [NOWAIT | SKIP LOCKED ...] starts (token index), or null.
     *
     * @param array[] $tokens
     * @return int|null
     */
    public static function trailingForUpdate( array $tokens )
    {
        $outer = static::outerWords( $tokens );
        for ( $n = count( $outer ) - 1; $n > 0; $n-- )
            if ( $outer[$n - 1][1] === 'FOR' && $outer[$n][1] === 'UPDATE' )
                return $outer[$n - 1][0];
        return null;
    }

    /**
     * A trailing FOR UPDATE as the isolation clause that keeps update locks on the rows read (LOCK_CLAUSE): Db2's
     * FOR UPDATE only names an updatable cursor and is refused with ORDER BY or a join.
     *
     * @param string $sql
     * @return string
     */
    public static function lockingForUpdate( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( $sql );
        $at = static::trailingForUpdate( $tokens );
        if ( $at === null )
            return $sql;
        return rtrim( expSQLServerLexer::join( array_slice( $tokens, 0, $at ) ) ) . ' ' . static::LOCK_CLAUSE;
    }

    /**
     * A SELECT without FROM (outside parentheses) reads FROM the one-row table; a SELECT with a UNION is left alone.
     *
     * @param string $sql
     * @return string
     */
    public static function addOneRowTable( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( $sql );
        $outer = static::outerWords( $tokens );
        foreach ( $outer as $entry )
            if ( in_array( $entry[1], array( 'FROM', 'UNION', 'INTO', 'EXCEPT', 'INTERSECT' ), true ) )
                return $sql;
        foreach ( $outer as $entry )
            if ( in_array( $entry[1], array( 'WHERE', 'ORDER', 'GROUP', 'OFFSET', 'FETCH', 'LIMIT', 'FOR', 'WITH' ), true ) )
                return rtrim( expSQLServerLexer::join( array_slice( $tokens, 0, $entry[0] ) ) ) . ' FROM ' . static::ONE_ROW_TABLE . ' '
                     . ltrim( expSQLServerLexer::join( array_slice( $tokens, $entry[0] ) ) );
        return rtrim( $sql ) . ' FROM ' . static::ONE_ROW_TABLE;
    }

    /**
     * Where the trailing isolation clause (WITH RS USE AND KEEP ... LOCKS, WITH UR ...) of a SELECT starts, or null.
     *
     * @param array[] $tokens
     * @return int|null
     */
    public static function trailingIsolation( array $tokens )
    {
        $outer = static::outerWords( $tokens );
        $count = count( $outer );
        for ( $n = $count - 2; $n >= 0; $n-- )
            if ( $outer[$n][1] === 'WITH' && in_array( $outer[$n + 1][1], array( 'RS', 'RR', 'CS', 'UR' ), true ) )
                return $outer[$n][0];
        return null;
    }

    /**
     * A SELECT limited to $limit rows from $offset on: OFFSET n ROWS FETCH FIRST m ROWS ONLY, in front of a trailing
     * FOR UPDATE or isolation clause. A statement with paging of its own is wrapped. Null for a limit of 0 (no rows:
     * send nothing); the statement as it is without limit and offset, and for anything but a SELECT.
     *
     * @param string $sql
     * @param int|string|false|null $limit
     * @param int|string $offset
     * @return string|null
     */
    public static function applyLimit( $sql, $limit, $offset = 0 )
    {
        list( $limit, $offset ) = expDBSQLHelpers::normalizeLimit( $limit, $offset );
        $sql = rtrim( (string)$sql, " \t\r\n;" );
        if ( $limit === 0 )
            return null;
        if ( $limit === false && $offset === 0 )
            return $sql;
        $kind = static::statementKind( $sql );
        if ( $kind !== 'SELECT' && $kind !== 'WITH' )
            return $sql;
        $tokens = expSQLServerLexer::tokenize( $sql );
        $cut = static::trailingForUpdate( $tokens );
        if ( $cut === null )
            $cut = static::trailingIsolation( $tokens );
        $tail = '';
        if ( $cut !== null )
        {
            $tail = ' ' . trim( expSQLServerLexer::join( array_slice( $tokens, $cut ) ) );
            $tokens = array_slice( $tokens, 0, $cut );
        }
        return static::pagedBody( $tokens, $limit, $offset ) . $tail;
    }

    /**
     * The statement of $tokens (without a lock clause) with its page.
     *
     * @param array[] $tokens
     * @param int|false $limit
     * @param int $offset
     * @return string
     */
    protected static function pagedBody( array $tokens, $limit, $offset )
    {
        $paging = "OFFSET $offset ROWS" . ( $limit !== false ? " FETCH FIRST $limit ROWS ONLY" : '' );
        $body = rtrim( expSQLServerLexer::join( $tokens ) );
        foreach ( static::outerWords( $tokens ) as $entry )
            if ( in_array( $entry[1], array( 'OFFSET', 'FETCH', 'LIMIT' ), true ) )
                return "SELECT * FROM (\n$body\n) exp_page\n$paging";
        return "$body\n$paging";
    }

    /**
     * A SELECT that locks the rows it reads until the end of the transaction.
     *
     * @param string $sql a SELECT without FOR UPDATE
     * @return string
     */
    public static function forUpdate( $sql )
    {
        return rtrim( (string)$sql, " \t\r\n;" ) . ' ' . static::LOCK_CLAUSE;
    }

    /**
     * An insert-or-update of one row as one MERGE.
     *
     * @param string $table
     * @param array $row column => value
     * @param string[] $keyColumns
     * @param string[]|null $updateColumns null: every column but the keys; array(): insert only
     * @return string
     */
    public static function upsert( $table, array $row, array $keyColumns, $updateColumns = null )
    {
        if ( !$row || !$keyColumns )
            throw new InvalidArgumentException( 'An upsert needs at least one column and one key column' );
        foreach ( $keyColumns as $key )
            if ( !array_key_exists( $key, $row ) )
                throw new InvalidArgumentException( "The key column '$key' has no value" );
        if ( $updateColumns === null )
            $updateColumns = array_values( array_diff( array_keys( $row ), $keyColumns ) );
        $q = array( static::class, 'quoteIdentifier' );
        $source = array();
        $names = array();
        foreach ( $row as $column => $value )
        {
            $source[] = static::literal( $value );
            $names[] = $q( $column );
        }
        $on = array();
        foreach ( $keyColumns as $key )
            $on[] = 't.' . $q( $key ) . ' = s.' . $q( $key );
        $sql = 'MERGE INTO ' . static::quoteName( $table ) . ' t USING (VALUES (' . implode( ', ', $source ) . ')) s ('
             . implode( ', ', $names ) . ') ON ' . implode( ' AND ', $on );
        if ( $updateColumns )
        {
            $set = array();
            foreach ( $updateColumns as $column )
                $set[] = $q( $column ) . ' = s.' . $q( $column );
            $sql .= ' WHEN MATCHED THEN UPDATE SET ' . implode( ', ', $set );
        }
        $values = array();
        foreach ( array_keys( $row ) as $column )
            $values[] = 's.' . $q( $column );
        return $sql . ' WHEN NOT MATCHED THEN INSERT (' . implode( ', ', $names ) . ') VALUES (' . implode( ', ', $values ) . ')';
    }

    // ------------------------------------------------------------------ expressions

    /**
     * MySQL's GROUP_CONCAT as LISTAGG(), with the order of the rows when one is given.
     *
     * @param string $expression
     * @param string $separator
     * @param string|null $orderBy
     * @return string
     */
    public static function groupConcat( $expression, $separator = ',', $orderBy = null )
    {
        $sql = "LISTAGG(CAST($expression AS VARCHAR(32000)), " . static::stringLiteral( (string)$separator ) . ')';
        if ( $orderBy !== null && trim( (string)$orderBy ) !== '' )
            $sql .= ' WITHIN GROUP (ORDER BY ' . trim( (string)$orderBy ) . ')';
        return $sql;
    }

    /**
     * Seconds since 1970-01-01 UTC, of now (the server's clock less its time zone) or of a timestamp expression
     * taken as UTC.
     *
     * @param string|null $expression
     * @return string
     */
    public static function unixTimestamp( $expression = null )
    {
        if ( $expression === null )
            return 'BIGINT(EXTRACT(EPOCH FROM CURRENT TIMESTAMP - CURRENT TIMEZONE))';
        return "BIGINT(EXTRACT(EPOCH FROM TIMESTAMP($expression)))";
    }

    /**
     * A timestamp (UTC) of seconds since 1970.
     *
     * @param string $expression
     * @return string
     */
    public static function fromUnixTimestamp( $expression )
    {
        return "(TIMESTAMP('1970-01-01-00.00.00') + ($expression) SECONDS)";
    }

    /**
     * @param string $string
     * @param string|int $from 1-based
     * @param string|int|null $length
     * @return string
     */
    public static function subString( $string, $from, $length = null )
    {
        return "SUBSTRING($string, $from" . ( $length !== null && $length !== '' ? ", $length" : '' ) . ', ' . static::STRING_UNITS . ')';
    }

    /**
     * @param string[] $strings expressions
     * @return string
     */
    public static function concatString( array $strings )
    {
        $strings = array_values( $strings );
        if ( !$strings )
            return "''";
        if ( count( $strings ) === 1 )
            return $strings[0];
        return '(' . implode( ' || ', $strings ) . ')';
    }

    /**
     * The MD5 of a text expression as 32 lower-case hex digits (HASH() with algorithm 0, of the UTF-8 bytes).
     *
     * @param string $expression
     * @return string
     */
    public static function md5( $expression )
    {
        $literal = static::literalText( $expression );
        if ( $literal !== null )
            return "'" . md5( $literal ) . "'";
        return "LOWER(HEX(HASH($expression, 0)))";
    }

    /**
     * The text of an expression that is one string literal ('...' with doubled quotes), or null: md5() of a literal
     * is computed here (the URL aliases ask for the MD5 of a literal every time).
     *
     * @param string $expression
     * @return string|null
     */
    public static function literalText( $expression )
    {
        $tokens = expSQLServerLexer::tokenize( trim( (string)$expression ) );
        if ( count( $tokens ) !== 1 || $tokens[0]['type'] !== expSQLServerLexer::STRING || !empty( $tokens[0]['open'] ) )
            return null;
        return expSQLServerLexer::literalValue( $tokens[0] );
    }

    public static function bitAnd( $a, $b )
    {
        return "BITAND($a, $b)";
    }

    public static function bitOr( $a, $b )
    {
        return "BITOR($a, $b)";
    }

    /**
     * The length of a text in characters.
     *
     * @param string $expression
     * @return string
     */
    public static function charLength( $expression )
    {
        return "CHARACTER_LENGTH($expression, " . static::STRING_UNITS . ')';
    }

    /**
     * column IN (...) for any number of values: lists of at most $chunk values joined with OR (AND for NOT IN).
     *
     * @param array $elements
     * @param string $column
     * @param bool $not
     * @param string|false $type int/integer and float/double write numbers, string a quoted literal; without a type
     *                           the elements are SQL as they are (the kernel passes '...' already quoted), as in
     *                           every other driver's generateSQLINStatement()
     * @param int $chunk
     * @param bool $unique
     * @return string
     */
    public static function inList( array $elements, $column, $not = false, $type = false, $chunk = self::MAX_IN_LIST, $unique = true )
    {
        $values = array();
        foreach ( $elements as $element )
        {
            if ( $type === 'int' || $type === 'integer' )
                $values[] = (string)(int)$element;
            else if ( $type === 'float' || $type === 'double' )
                $values[] = static::literal( (float)$element );
            else if ( $type === 'string' )
                $values[] = static::stringLiteral( (string)$element );
            else
                $values[] = (string)$element;
        }
        if ( $unique )
            $values = array_values( array_unique( $values ) );
        if ( !$values )
            return $not ? '1 = 1' : '1 = 0';
        $chunk = max( 1, (int)$chunk );
        $parts = array();
        foreach ( array_chunk( $values, $chunk ) as $part )
            $parts[] = $column . ( $not ? ' NOT IN ( ' : ' IN ( ' ) . implode( ', ', $part ) . ' )';
        if ( count( $parts ) === 1 )
            return $parts[0];
        return '( ' . implode( $not ? ' AND ' : ' OR ', $parts ) . ' )';
    }

    /**
     * String literals longer than $threshold bytes as "?" parameters: Db2 refuses a string constant of more than
     * 32,672 bytes, while a parameter takes a text of any length into a CLOB. Null when there is none, or when the
     * statement has "?" of its own.
     *
     * @param string $sql
     * @param int $threshold
     * @return array|null array( sql, params )
     */
    public static function parameterizeLongLiterals( $sql, $threshold = 16000 )
    {
        if ( strlen( (string)$sql ) <= $threshold )
            return null;
        $tokens = expSQLServerLexer::tokenize( (string)$sql );
        $params = array();
        $out = '';
        foreach ( $tokens as $token )
        {
            if ( $token['type'] === expSQLServerLexer::PARAM )
                return null;
            if ( ( $token['type'] === expSQLServerLexer::STRING || $token['type'] === expSQLServerLexer::NSTRING ) && !$token['open']
                 && strlen( $token['text'] ) > $threshold )
            {
                $params[] = expSQLServerLexer::literalValue( $token );
                $out .= '?';
                continue;
            }
            $out .= $token['text'];
        }
        return $params ? array( $out, $params ) : null;
    }

    // ------------------------------------------------------------------ temporary tables

    /**
     * A kernel CREATE TEMPORARY TABLE as a created global temporary table of Db2 (its definition is permanent and
     * shared, its rows belong to the connection that wrote them and stay until it ends or deletes them; it takes no
     * primary key, which is left out): array(
     * 'name' => table, 'sql' => CREATE GLOBAL TEMPORARY TABLE ... ON COMMIT PRESERVE ROWS NOT LOGGED ), or null when
     * $sql is no such statement.
     *
     * @param string $sql
     * @return array|null
     */
    public static function temporaryTable( $sql )
    {
        $parsed = static::parseTemporaryTable( $sql );
        if ( $parsed === null )
            return null;
        // a created temporary table takes no primary key or unique constraint (SQL0526N)
        $columns = array();
        foreach ( $parsed['columns'] as $column )
            $columns[] = preg_replace( '/\s+(PRIMARY\s+KEY|UNIQUE)\b/i', '', $column );
        return array( 'name' => $parsed['name'], 'columns' => static::columnNames( $columns ),
                      'sql' => 'CREATE GLOBAL TEMPORARY TABLE ' . static::quoteIdentifierIfNeeded( $parsed['name'] ) . ' ( '
                             . implode( ', ', $columns ) . ' ) ON COMMIT PRESERVE ROWS NOT LOGGED' );
    }

    /**
     * The names of column definitions (their first word, unquoted, in lower case).
     *
     * @param string[] $definitions
     * @return string[]
     */
    public static function columnNames( array $definitions )
    {
        $names = array();
        foreach ( $definitions as $definition )
            if ( preg_match( '/^\s*"?([^"\s]+)"?/', (string)$definition, $m ) )
                $names[] = strtolower( $m[1] );
        return $names;
    }

    /**
     * The name and the column definitions (in the server's words) of a CREATE [GLOBAL] TEMPORARY TABLE, or null.
     *
     * @param string $sql
     * @return array|null
     */
    protected static function parseTemporaryTable( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( static::backticksToQuotes( trim( (string)$sql ) ) );
        $significant = expSQLServerLexer::significant( $tokens );
        $words = array();
        foreach ( array_slice( $significant, 0, 5 ) as $entry )
            $words[] = expSQLServerLexer::keyword( $tokens[$entry[0]] );
        if ( count( $words ) < 4 || $words[0] !== 'CREATE' )
            return null;
        $n = 1;
        if ( $words[1] === 'GLOBAL' )
            $n++;
        if ( !in_array( $words[$n], array( 'TEMPORARY', 'TEMP' ), true ) || $words[$n + 1] !== 'TABLE' )
            return null;
        $nameAt = $significant[$n + 2][0];
        $name = expSQLServerLexer::identifierValue( $tokens[$nameAt] );
        if ( !isset( $significant[$n + 3] ) || $tokens[$significant[$n + 3][0]]['text'] !== '(' )
            return null;
        $open = $significant[$n + 3][0];
        $close = static::matchingParen( $tokens, $open );
        if ( $close === null )
            return null;
        $columns = array();
        foreach ( static::splitArguments( array_slice( $tokens, $open + 1, $close - $open - 1 ) ) as $column )
            $columns[] = static::columnConstraintsLast( trim( expSQLServerLexer::join( $column ) ) );
        return array( 'name' => $name, 'columns' => $columns );
    }

    /**
     * A column definition of MySQL's free word order (int primary key not null) in the server's (INTEGER NOT NULL
     * PRIMARY KEY), with MySQL's types the server lacks given its own.
     *
     * @param string $definition
     * @return string
     */
    public static function columnConstraintsLast( $definition )
    {
        $text = ' ' . preg_replace( '/\s+/', ' ', $definition ) . ' ';
        $notNull = stripos( $text, ' not null ' ) !== false;
        $primary = stripos( $text, ' primary key ' ) !== false;
        $text = str_ireplace( array( ' not null ', ' primary key ', ' null ' ), ' ', $text );
        $text = preg_replace( '/\bint\b/i', 'INTEGER', trim( preg_replace( '/\s+/', ' ', $text ) ) );
        $text = preg_replace( '/\b(INTEGER|BIGINT|SMALLINT)\(\d+\)/i', '$1', $text );
        $text = preg_replace( '/\b(?:tinyint|mediumint)\b(?:\(\d+\))?/i', 'INTEGER', $text );
        $text = preg_replace( '/\b(?:long|medium|tiny)?text\b/i', 'VARCHAR(8000)', $text );
        // the column's name: quoted when it is a reserved word (user)
        if ( preg_match( '/^([A-Za-z_][A-Za-z0-9_$]*)(\s.*)?$/s', $text, $m ) )
            $text = static::quoteIdentifierIfNeeded( $m[1] ) . ( isset( $m[2] ) ? $m[2] : '' );
        return $text . ( $notNull || $primary ? ' NOT NULL' : '' ) . ( $primary ? ' PRIMARY KEY' : '' );
    }

    // ------------------------------------------------------------------ errors

    /**
     * The kind of an error, from Db2's SQLCODE, the SQLSTATE and the message: deadlock (a deadlock or a rollback
     * after a lock timeout: a single statement may be sent again), lock (a lock wait that timed out), duplicate (a
     * primary or unique key), connection (the connection is gone), syntax, unknown_object, or other.
     *
     * @param int|null $sqlCode
     * @param string|null $state
     * @param string $message
     * @return string
     */
    public static function errorCategory( $sqlCode, $state = null, $message = '' )
    {
        $code = (int)$sqlCode;
        $state = strtoupper( (string)$state );
        $message = strtolower( (string)$message );
        if ( in_array( $code, array( -30081, -30080, -1224, -1229, -900, -1776 ), true ) || in_array( $state, array( '08001', '08003', '08S01', '40003', '57019' ), true )
             || strpos( $message, 'communication error' ) !== false || strpos( $message, 'the connection is closed' ) !== false )
            return 'connection';
        if ( $code === -911 || $state === '40001' || strpos( $message, 'deadlock' ) !== false )
            return 'deadlock';
        if ( $code === -913 || $state === '57033' )
            return 'lock';
        if ( $code === -803 || $state === '23505' )
            return 'duplicate';
        if ( in_array( $code, array( -104, -199, -7 ), true ) || $state === '42601' )
            return 'syntax';
        if ( in_array( $code, array( -204, -206 ), true ) || in_array( $state, array( '42704', '42703' ), true ) )
            return 'unknown_object';
        return 'other';
    }

    // ------------------------------------------------------------------ connection

    /**
     * The data source name of pdo_ibm (ibm:DATABASE=...;HOSTNAME=...;PORT=...;PROTOCOL=TCPIP;) or of PDO_ODBC
     * (odbc:Driver={...};...). Without a host the database is a catalogued alias of the client.
     *
     * @param array $config layer, host, port, database, driver, security, concentrator, extra
     * @return string
     */
    public static function pdoDSN( array $config )
    {
        $host = isset( $config['host'] ) ? trim( (string)$config['host'] ) : '';
        $port = isset( $config['port'] ) && $config['port'] ? (int)$config['port'] : static::DEFAULT_PORT;
        $database = isset( $config['database'] ) ? (string)$config['database'] : '';
        $parts = array();
        if ( isset( $config['layer'] ) && $config['layer'] === 'pdo_odbc' )
            $parts[] = 'DRIVER={' . static::dsnValue( isset( $config['driver'] ) && $config['driver'] !== '' ? $config['driver'] : 'IBM DB2 ODBC DRIVER', '{}' ) . '}';
        $parts[] = 'DATABASE=' . static::dsnValue( $database );
        if ( $host !== '' )
        {
            $parts[] = 'HOSTNAME=' . static::dsnValue( $host );
            $parts[] = 'PORT=' . $port;
            $parts[] = 'PROTOCOL=TCPIP';
        }
        if ( !empty( $config['security'] ) )
            $parts[] = 'SECURITY=' . static::dsnValue( $config['security'] );
        // the statement concentrator: one compiled plan for statements that differ only in their literals
        if ( !empty( $config['concentrator'] ) )
            $parts[] = 'StmtConcentrator=WITHLITERALS';
        if ( !empty( $config['extra'] ) )
            foreach ( explode( ';', (string)$config['extra'] ) as $pair )
                if ( trim( $pair ) !== '' && strpos( $pair, '=' ) !== false && strpos( $pair, "\0" ) === false )
                    $parts[] = trim( $pair );
        return ( isset( $config['layer'] ) && $config['layer'] === 'pdo_odbc' ? 'odbc:' : 'ibm:' ) . implode( ';', $parts ) . ';';
    }

    /**
     * A value of a DSN: a ";" ends the pair, so a value with one is refused.
     *
     * @param string $value
     * @param string $also more characters that are refused
     * @return string
     * @throws InvalidArgumentException
     */
    public static function dsnValue( $value, $also = '' )
    {
        $value = (string)$value;
        if ( strpbrk( $value, ";\0" . $also ) !== false )
            throw new InvalidArgumentException( 'A connection value cannot hold ";"' );
        return $value;
    }

    /**
     * What a server version says: SERVICE_LEVEL of SYSIBMADM.ENV_INST_INFO ("DB2 v12.1.6.0") or the product id of
     * the connection ("SQL12016", "DB2/LINUXX8664 12.1.6.0").
     *
     * @param string $version
     * @return array major, minor, string, supported, name
     */
    public static function describeVersion( $version )
    {
        $version = (string)$version;
        $major = 0;
        $minor = 0;
        $string = '';
        if ( preg_match( '/(\d+)\.(\d+)(?:\.(\d+))?(?:\.(\d+))?/', $version, $m ) )
        {
            $major = (int)$m[1];
            $minor = (int)$m[2];
            $string = $m[0];
        }
        else if ( preg_match( '/^SQL(\d\d)(\d\d)(\d)/', $version, $m ) )
        {
            $major = (int)$m[1];
            $minor = (int)$m[2];
            $string = $major . '.' . $minor . '.' . (int)$m[3];
        }
        list( $minMajor, $minMinor ) = array_map( 'intval', explode( '.', static::MIN_VERSION ) );
        return array( 'major' => $major, 'minor' => $minor, 'string' => $string,
                      'name' => static::productName() . ' ' . $major . '.' . $minor,
                      'supported' => $major > $minMajor || ( $major === $minMajor && $minor >= $minMinor ) );
    }

    /** @return string */
    public static function productName()
    {
        return 'Db2';
    }

    /**
     * enabled/yes/true/1/on as true.
     *
     * @param mixed $value
     * @return bool
     */
    public static function flag( $value )
    {
        if ( is_bool( $value ) )
            return $value;
        return in_array( strtolower( trim( (string)$value ) ), array( '1', 'yes', 'true', 'enabled', 'on' ), true );
    }
}
