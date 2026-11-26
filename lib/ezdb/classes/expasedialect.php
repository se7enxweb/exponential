<?php
/**
 * File containing the expASEDialect class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The Transact-SQL of SAP Adaptive Server Enterprise (ASE, formerly Sybase) that expASEDB writes, as pure functions
 * of their arguments: no connection, no settings. Every rule here is tested without a server (tests/tests/lib/ezdb/ase).
 *
 * ASE and Microsoft SQL Server share their origin, the TDS protocol and most of Transact-SQL, so this class extends
 * expSQLServerDialect and keeps its lexer-based readers of a statement (parseInsert(), rewriteIdentityInsert(),
 * statementKind(), backticksToBrackets()). Where ASE differs, it says so here:
 *
 *  - Names: up to 255 characters; brackets [name] as SQL Server, and double quotes with quoted_identifier on. A
 *    column named like a reserved word of ASE (count) is bracketed where it stands as a name.
 *  - Literals: '...' (ASE has no N'...'); the connection's character set is UTF-8 and the text columns are
 *    UNIVARCHAR/UNITEXT, so ASE converts the text. ASE stores an empty string '' as one space ' ' in a variable
 *    length text column; the driver reads a value of exactly one space back as ''.
 *  - Paging: no OFFSET/FETCH in ASE 16.0 before SP02, so SELECT TOP (offset + limit) and the first offset rows
 *    dropped by the driver (applyLimit() returns how many). A trailing MySQL LIMIT of the kernel is read the same way.
 *  - Functions: CONCAT(a, b) as (a + b), LENGTH() as char_length(), SUBSTR() as substring(), IFNULL() as isnull(),
 *    NOW() as getdate(), CURDATE() as current_date(), MD5() as lower(hash(x, 'md5')), UNIX_TIMESTAMP() and
 *    FROM_UNIXTIME() with datediff()/dateadd() from '19700101' on the UTC clock (getutcdate()).
 *  - LIKE: a backslash in a pattern gets ESCAPE '\', and a "[" in such a pattern is escaped, as ASE reads [...] as a
 *    set of characters.
 *  - INSERT ... VALUES (...), (...): ASE takes one row per VALUES; the rows become statements of their own.
 *  - GROUP_CONCAT: ASE has no string aggregate (expDBUnsupportedException).
 *  - Upsert: MERGE (ASE 15.7 and later).
 *
 * Untested against a server: no SAP ASE was available for this driver's tests (the ASE developer edition is a
 * download behind SAP's registration and its license allows no redistribution as a container image); the rules
 * follow SAP's ASE 16.0 reference manuals.
 */
class expASEDialect extends expSQLServerDialect
{
    /** Longest name ASE 15 and 16 accept */
    const MAX_IDENTIFIER_LENGTH = 255;

    /** The default port of an ASE server */
    const DEFAULT_PORT = 5000;

    /** Lowest supported version: ASE 15.7 (MERGE, int identity columns, hash()) */
    const MIN_VERSION = 15.7;

    /** The longest UNIVARCHAR the schema handler writes, in characters (a 16 KB page holds a row of 16,000 bytes) */
    const MAX_UNIVARCHAR_LENGTH = 4000;

    /**
     * The reserved words of ASE 16.0 (Reference Manual: Building Blocks, "Reserved words"): a table or column of one
     * of these names must be bracketed.
     *
     * @var string[]
     */
    const RESERVED_WORDS = array(
        'ADD', 'ALL', 'ALTER', 'AND', 'ANY', 'ARITH_OVERFLOW', 'AS', 'ASC', 'AT', 'AUTHORIZATION', 'AVG', 'BEGIN', 'BETWEEN',
        'BREAK', 'BROWSE', 'BULK', 'BY', 'CASCADE', 'CASE', 'CHAR_CONVERT', 'CHECK', 'CHECKPOINT', 'CLOSE', 'CLUSTERED',
        'COALESCE', 'COMMIT', 'COMPRESSED', 'COMPUTE', 'CONFIRM', 'CONNECT', 'CONSTRAINT', 'CONTINUE', 'CONTROLROW',
        'CONVERT', 'COUNT', 'COUNT_BIG', 'CREATE', 'CURRENT', 'CURSOR', 'DATABASE', 'DBCC', 'DEALLOCATE', 'DECLARE',
        'DECRYPT', 'DECRYPT_DEFAULT', 'DEFAULT', 'DELETE', 'DESC', 'DETERMINISTIC', 'DISK', 'DISTINCT', 'DROP',
        'DUAL_CONTROL', 'DUMMY', 'DUMP', 'ELSE', 'ENCRYPT', 'END', 'ENDTRAN', 'ERRLVL', 'ERRORDATA', 'ERROREXIT', 'ESCAPE',
        'EXCEPT', 'EXCLUSIVE', 'EXEC', 'EXECUTE', 'EXISTS', 'EXIT', 'EXP_ROW_SIZE', 'EXTERNAL', 'FETCH', 'FILLFACTOR', 'FOR',
        'FOREIGN', 'FROM', 'FUNC', 'FUNCTION', 'GOTO', 'GRANT', 'GROUP', 'HAVING', 'HOLDLOCK', 'IDENTITY', 'IDENTITY_GAP',
        'IDENTITY_START', 'IF', 'IN', 'INDEX', 'INOUT', 'INSENSITIVE', 'INSERT', 'INSTALL', 'INTERSECT', 'INTO', 'IS',
        'ISOLATION', 'JAR', 'JOIN', 'KEY', 'KILL', 'LEVEL', 'LIKE', 'LINENO', 'LOAD', 'LOCK', 'MATERIALIZED', 'MAX',
        'MAX_ROWS_PER_PAGE', 'MIN', 'MIRROR', 'MIRROREXIT', 'MODIFY', 'NATIONAL', 'NEW', 'NOHOLDLOCK', 'NONCLUSTERED',
        'NONSCROLLABLE', 'NON_SENSITIVE', 'NOT', 'NULL', 'NULLIF', 'NUMERIC_TRUNCATION', 'OF', 'OFF', 'OFFSETS', 'ON', 'ONCE',
        'ONLINE', 'ONLY', 'OPEN', 'OPTION', 'OR', 'ORDER', 'OUT', 'OUTPUT', 'OVER', 'PARTITION', 'PERM', 'PERMANENT', 'PLAN',
        'PREPARE', 'PRIMARY', 'PRINT', 'PRIVILEGES', 'PROC', 'PROCEDURE', 'PROCESSEXIT', 'PROXY_TABLE', 'PUBLIC', 'QUIESCE',
        'RAISERROR', 'READ', 'READPAST', 'READTEXT', 'RECONFIGURE', 'REFERENCES', 'REMOVE', 'REORG', 'REPLACE', 'REPLICATION',
        'RESERVEPAGEGAP', 'RETURN', 'RETURNS', 'REVOKE', 'ROLE', 'ROLLBACK', 'ROWCOUNT', 'ROWS', 'RULE', 'SAVE', 'SCHEMA',
        'SCROLL', 'SCROLLABLE', 'SELECT', 'SEMI_SENSITIVE', 'SET', 'SETUSER', 'SHARED', 'SHUTDOWN', 'SOME', 'STATISTICS',
        'STRINGSIZE', 'STRIPE', 'SUM', 'SYB_IDENTITY', 'SYB_RESTREE', 'SYB_TERMINATE', 'TABLE', 'TEMP', 'TEMPORARY',
        'TEXTSIZE', 'TO', 'TRACEFILE', 'TRAN', 'TRANSACTION', 'TRIGGER', 'TRUNCATE', 'TSEQUAL', 'UNION', 'UNIQUE',
        'UNPARTITION', 'UPDATE', 'USE', 'USER', 'USER_OPTION', 'USING', 'VALUES', 'VARYING', 'VIEW', 'WAITFOR', 'WHEN',
        'WHERE', 'WHILE', 'WITH', 'WORK', 'WRITETEXT', 'XMLEXTRACT', 'XMLPARSE', 'XMLTEST', 'XMLVALIDATE',
    );

    /**
     * Reserved words that are names in the statements Exponential writes (ezview_counter.count,
     * ezsubtree_notification_rule ... ): bracketed where they stand as a name, not as a call (COUNT(*)).
     *
     * @var string[]
     */
    const NAME_WORDS = array( 'COUNT', 'LEVEL', 'ROLE', 'KEY', 'NEW', 'ONLINE', 'ONCE', 'WORK', 'TEMP', 'ROWS', 'PLAN', 'DUMMY', 'SHARED', 'MODIFY' );

    /** MySQL functions that only have another name here */
    const RENAMED_FUNCTIONS = array( 'LENGTH' => 'char_length', 'CHARACTER_LENGTH' => 'char_length', 'SUBSTR' => 'substring',
                                     'IFNULL' => 'isnull', 'LCASE' => 'lower', 'UCASE' => 'upper' );

    /** @var array|null RESERVED_WORDS as keys */
    private static $aseReserved = null;

    // ------------------------------------------------------------------ names

    public static function isReservedWord( $word )
    {
        if ( self::$aseReserved === null )
            self::$aseReserved = array_fill_keys( self::RESERVED_WORDS, true );
        return isset( self::$aseReserved[strtoupper( (string)$word )] );
    }

    /**
     * One name in brackets, with a "]" inside doubled. An empty name, a name with a NUL byte and a name of more than
     * 255 characters are refused.
     *
     * @param string $name
     * @return string
     */
    public static function quoteIdentifier( $name )
    {
        $name = (string)$name;
        self::checkIdentifier( $name );
        return '[' . str_replace( ']', ']]', $name ) . ']';
    }

    /**
     * A dotted name in brackets part by part.
     *
     * @param string $name
     * @return string
     */
    public static function quoteName( $name )
    {
        $parts = array();
        foreach ( explode( '.', (string)$name ) as $part )
            $parts[] = self::quoteIdentifier( trim( $part, '[]' ) );
        return implode( '.', $parts );
    }

    public static function quoteIdentifierIfNeeded( $name )
    {
        $name = (string)$name;
        if ( !self::isReservedWord( $name ) && preg_match( '/^[A-Za-z_#][A-Za-z0-9_@#$]*$/', $name ) && strlen( $name ) <= self::MAX_IDENTIFIER_LENGTH )
            return $name;
        return self::quoteIdentifier( $name );
    }

    public static function checkIdentifier( $name )
    {
        $name = (string)$name;
        if ( $name === '' || strpos( $name, "\0" ) !== false )
            throw new InvalidArgumentException( 'An empty name or a name with a NUL byte is no identifier' );
        $length = function_exists( 'mb_strlen' ) ? mb_strlen( $name, 'UTF-8' ) : strlen( $name );
        if ( $length > self::MAX_IDENTIFIER_LENGTH )
            throw new InvalidArgumentException( "The name '" . substr( $name, 0, 40 ) . "...' is longer than " . self::MAX_IDENTIFIER_LENGTH . ' characters' );
    }

    // ------------------------------------------------------------------ literals

    /**
     * A complete string literal: '...' with quotes doubled (ASE has no N'...').
     *
     * @param string $value
     * @return string
     */
    public static function stringLiteral( $value )
    {
        return "'" . self::escapeString( $value ) . "'";
    }

    /**
     * A PHP value as a literal: NULL, 1/0, digits, a float in plain notation, else the escaped text in quotes.
     *
     * @param mixed $value
     * @return string
     */
    public static function literal( $value )
    {
        return expDBSQLHelpers::literal( array( __CLASS__, 'escapeString' ), $value );
    }

    // ------------------------------------------------------------------ statements

    /**
     * A statement of the kernel (MySQL words, as far as they reach the driver) in ASE's words; see the class comment.
     * Statements of the schema keep their words (they come from the schema handler in ASE's words already).
     *
     * @param string $sql
     * @return string
     */
    public static function rewrite( $sql )
    {
        $sql = trim( (string)$sql );
        while ( substr( $sql, -1 ) === ';' )
            $sql = rtrim( substr( $sql, 0, -1 ) );
        $sql = self::backticksToBrackets( $sql );
        $kind = self::statementKind( $sql );
        if ( in_array( $kind, array( 'CREATE', 'ALTER', 'DROP', 'EXEC', 'SET', 'GRANT', 'REVOKE', 'DECLARE', 'IF', 'BEGIN', 'COMMIT', 'ROLLBACK' ), true ) )
            return $sql;
        if ( $kind === 'TRUNCATE' )
        {
            $tokens = expSQLServerLexer::tokenize( $sql );
            $significant = expSQLServerLexer::significant( $tokens );
            $n = isset( $significant[1] ) && expSQLServerLexer::keyword( $tokens[$significant[1][0]] ) === 'TABLE' ? 2 : 1;
            return isset( $significant[$n] ) ? 'DELETE FROM ' . trim( expSQLServerLexer::join( array_slice( $tokens, $significant[$n][0] ) ) ) : $sql;
        }
        return self::rewriteTokens( expSQLServerLexer::tokenize( $sql ) );
    }

    /**
     * The token-level rules of rewrite(): names that are reserved words, functions, LIKE patterns.
     *
     * @param array[] $tokens
     * @return string
     */
    protected static function rewriteTokens( array $tokens )
    {
        $nameWords = array_fill_keys( self::NAME_WORDS, true );
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
            $next = null;
            for ( $j = $i + 1; $j < $count; $j++ )
                if ( !expSQLServerLexer::isNoise( $tokens[$j] ) )
                {
                    $next = $j;
                    break;
                }
            $nextToken = $next === null ? null : $tokens[$next];
            $nextText = $nextToken === null ? '' : $nextToken['text'];
            $nextWord = $nextToken === null ? '' : expSQLServerLexer::keyword( $nextToken );
            $text = $token['text'];
            if ( $token['type'] === expSQLServerLexer::WORD )
            {
                $word = strtoupper( $token['text'] );
                $isCall = $nextText === '(' && $prevText !== '.';
                if ( $isCall && isset( self::RENAMED_FUNCTIONS[$word] ) )
                    $text = self::RENAMED_FUNCTIONS[$word];
                else if ( $isCall && in_array( $word, array( 'CONCAT', 'MD5', 'NOW', 'CURDATE', 'UNIX_TIMESTAMP', 'FROM_UNIXTIME', 'SUBSTRING' ), true ) )
                {
                    $close = self::matchingParen( $tokens, $next );
                    if ( $close !== null )
                    {
                        $args = self::splitArguments( array_slice( $tokens, $next + 1, $close - $next - 1 ) );
                        $replacement = self::rewriteCall( $word, $args );
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
                else if ( !$isCall && isset( $nameWords[$word] ) )
                    $text = '[' . $token['text'] . ']';
            }
            else if ( $token['type'] === expSQLServerLexer::STRING && $prevWord === 'LIKE' && $nextWord !== 'ESCAPE'
                      && ( strpos( $token['text'], '\\' ) !== false || strpos( $token['text'], '[' ) !== false ) )
            {
                // ASE reads [...] in a pattern as a set of characters: escaped, with the backslash of MySQL
                $value = expSQLServerLexer::literalValue( $token );
                $value = preg_replace( '/(?<!\\\\)((?:\\\\\\\\)*)\[/', '$1\\[', $value );
                $text = self::stringLiteral( $value ) . " ESCAPE '\\'";
            }
            $out .= $text;
            $prevWord = expSQLServerLexer::keyword( $token );
            $prevText = $token['text'];
        }
        return $out;
    }

    /**
     * The rewrite of a call of a MySQL function ASE lacks, from its arguments; null when the call is left as it is.
     *
     * @param string $word
     * @param array[] $args
     * @return string|null
     */
    protected static function rewriteCall( $word, array $args )
    {
        $text = array();
        foreach ( $args as $arg )
            $text[] = trim( self::rewriteTokens( $arg ) );
        if ( count( $text ) === 1 && $text[0] === '' )
            $text = array();
        switch ( $word )
        {
            case 'CONCAT':
                return $text ? self::concatString( $text ) : null;
            case 'SUBSTRING':
                return count( $text ) === 2 ? self::subString( $text[0], $text[1] ) : null;
            case 'MD5':
                return count( $text ) === 1 ? self::md5( $text[0] ) : null;
            case 'NOW':
                return $text ? null : 'getdate()';
            case 'CURDATE':
                return $text ? null : 'current_date()';
            case 'UNIX_TIMESTAMP':
                return self::unixTimestamp( $text ? $text[0] : null );
            case 'FROM_UNIXTIME':
                return count( $text ) === 1 ? self::fromUnixTimestamp( $text[0] ) : null;
        }
        return null;
    }

    /**
     * The index of the ")" that closes the "(" at $open, or null.
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
            else if ( $tokens[$i]['text'] === ')' && --$depth === 0 )
                return $i;
        }
        return null;
    }

    /**
     * The arguments of a call split at the commas outside nested parentheses.
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
     * The significant tokens at depth 0: array( array( index, WORD, text ) ).
     */
    private static function outerWords( array $tokens )
    {
        $list = array();
        foreach ( expSQLServerLexer::significant( $tokens ) as $entry )
            if ( $entry[1] === 0 )
                $list[] = array( $entry[0], expSQLServerLexer::keyword( $tokens[$entry[0]] ), $tokens[$entry[0]]['text'] );
        return $list;
    }

    /**
     * A trailing MySQL LIMIT clause of a SELECT (LIMIT n, LIMIT n OFFSET m, LIMIT m, n): array( statement without it,
     * limit, offset ), or null when there is none.
     *
     * @param string $sql
     * @return array|null
     */
    public static function trailingLimit( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( (string)$sql );
        $outer = self::outerWords( $tokens );
        $limitAt = null;
        foreach ( $outer as $n => $entry )
            if ( $entry[1] === 'LIMIT' )
                $limitAt = $n;
        if ( $limitAt === null )
            return null;
        $parts = array_slice( $outer, $limitAt + 1 );
        $limit = null;
        $offset = 0;
        if ( count( $parts ) === 1 && ctype_digit( $parts[0][2] ) )
            $limit = (int)$parts[0][2];
        else if ( count( $parts ) === 3 && ctype_digit( $parts[0][2] ) && $parts[1][2] === ',' && ctype_digit( $parts[2][2] ) )
        {
            $offset = (int)$parts[0][2];
            $limit = (int)$parts[2][2];
        }
        else if ( count( $parts ) === 3 && ctype_digit( $parts[0][2] ) && $parts[1][1] === 'OFFSET' && ctype_digit( $parts[2][2] ) )
        {
            $limit = (int)$parts[0][2];
            $offset = (int)$parts[2][2];
        }
        else
            return null;
        return array( rtrim( expSQLServerLexer::join( array_slice( $tokens, 0, $outer[$limitAt][0] ) ) ), $limit, $offset );
    }

    /**
     * A SELECT that returns at most $offset + $limit rows: SELECT TOP n (after DISTINCT); the caller drops the first
     * $offset rows. array( sql, offset to skip ) - the statement as it is (all rows) for a UNION at its outer level,
     * where TOP would only cut the first SELECT; null for a limit of 0.
     *
     * @param string $sql
     * @param int|string|false|null $limit
     * @param int|string $offset
     * @return array|null array( string $sql, int $skip, bool $cut ): $cut false when the caller must also cut the
     *                    rows after offset + limit itself
     */
    public static function applyLimit( $sql, $limit, $offset = 0 )
    {
        list( $limit, $offset ) = expDBSQLHelpers::normalizeLimit( $limit, $offset );
        $sql = rtrim( (string)$sql, " \t\r\n;" );
        if ( $limit === 0 )
            return null;
        if ( $limit === false && $offset === 0 )
            return array( $sql, 0, true );
        $kind = self::statementKind( $sql );
        $tokens = expSQLServerLexer::tokenize( $sql );
        $outer = self::outerWords( $tokens );
        if ( $kind !== 'SELECT' || !$outer || $outer[0][1] !== 'SELECT' )
            return array( $sql, $offset, false );
        foreach ( $outer as $entry )
            if ( in_array( $entry[1], array( 'UNION', 'EXCEPT', 'INTERSECT', 'TOP' ), true ) )
                return array( $sql, $offset, false );
        if ( $limit === false )
            return array( $sql, $offset, true );
        $at = 1;
        if ( isset( $outer[1] ) && in_array( $outer[1][1], array( 'DISTINCT', 'ALL' ), true ) )
            $at = 2;
        $insertAt = isset( $outer[$at] ) ? $outer[$at][0] : count( $tokens );
        $head = rtrim( expSQLServerLexer::join( array_slice( $tokens, 0, $insertAt ) ) );
        $rest = ltrim( expSQLServerLexer::join( array_slice( $tokens, $insertAt ) ) );
        return array( $head . ' TOP ' . ( $offset + $limit ) . ' ' . $rest, $offset, true );
    }

    /**
     * The statement the portable helper sqlLimit() gives: TOP when there is no offset; with an offset ASE has no
     * clause of its own (expDBUnsupportedException: page with arrayQuery()'s limit and offset).
     *
     * @param string $sql
     * @param int|false $limit
     * @param int $offset
     * @return string|null
     */
    public static function limitStatement( $sql, $limit, $offset = 0 )
    {
        list( $limit, $offset ) = expDBSQLHelpers::normalizeLimit( $limit, $offset );
        if ( $limit === 0 )
            return null;
        if ( $offset > 0 )
            throw new expDBUnsupportedException( 'ase', 'OFFSET', 'use arrayQuery() with limit and offset' );
        $paged = self::applyLimit( $sql, $limit, 0 );
        if ( $paged === null )
            return null;
        if ( !$paged[2] && $limit !== false )
            throw new expDBUnsupportedException( 'ase', 'TOP on a UNION', 'use arrayQuery() with a limit' );
        return $paged[0];
    }

    /**
     * An INSERT of several rows (VALUES (...), (...)) as one INSERT per row; null when $sql is no such statement.
     *
     * @param string $sql
     * @return string[]|null
     */
    public static function splitMultiRowInsert( $sql )
    {
        $parsed = self::parseInsert( $sql );
        if ( $parsed === null || count( $parsed['rows'] ) < 2 )
            return null;
        $tokens = $parsed['tokens'];
        $head = 'INSERT INTO ' . $parsed['tableText'];
        if ( $parsed['columnTokens'] !== null )
            $head .= ' ' . expSQLServerLexer::join( array_slice( $tokens, $parsed['columnTokens'][0], $parsed['columnTokens'][1] - $parsed['columnTokens'][0] + 1 ) );
        $out = array();
        foreach ( $parsed['rows'] as $row )
        {
            $values = array();
            foreach ( $row as $value )
            {
                if ( $value[0] === null )
                    return null;
                $values[] = trim( expSQLServerLexer::join( array_slice( $tokens, $value[0], $value[1] - $value[0] + 1 ) ) );
            }
            $out[] = $head . ' VALUES (' . implode( ', ', $values ) . ')';
        }
        return $out;
    }

    /**
     * A SELECT that locks the rows it reads until the end of the transaction (ASE 15.7: FOR UPDATE on a table with
     * datarows locking; HOLDLOCK keeps the shared locks of the other schemes).
     *
     * @param string $sql
     * @return string
     */
    public static function forUpdate( $sql )
    {
        return rtrim( (string)$sql, " \t\r\n;" ) . ' FOR UPDATE';
    }

    /**
     * An insert-or-update of one row as MERGE (ASE 15.7 and later).
     *
     * @param string $table
     * @param array $row
     * @param string[] $keyColumns
     * @param string[]|null $updateColumns
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
        $q = array( __CLASS__, 'quoteIdentifier' );
        $source = array();
        foreach ( $row as $column => $value )
            $source[] = self::literal( $value ) . ' AS ' . $q( $column );
        $on = array();
        foreach ( $keyColumns as $key )
            $on[] = 't.' . $q( $key ) . ' = s.' . $q( $key );
        $sql = 'MERGE INTO ' . self::quoteName( $table ) . ' AS t USING (SELECT ' . implode( ', ', $source ) . ') AS s ON (' . implode( ' AND ', $on ) . ')';
        if ( $updateColumns )
        {
            $set = array();
            foreach ( $updateColumns as $column )
                $set[] = $q( $column ) . ' = s.' . $q( $column );
            $sql .= ' WHEN MATCHED THEN UPDATE SET ' . implode( ', ', $set );
        }
        $columns = array();
        $values = array();
        foreach ( array_keys( $row ) as $column )
        {
            $columns[] = $q( $column );
            $values[] = 's.' . $q( $column );
        }
        return $sql . ' WHEN NOT MATCHED THEN INSERT (' . implode( ', ', $columns ) . ') VALUES (' . implode( ', ', $values ) . ')';
    }

    // ------------------------------------------------------------------ expressions

    /**
     * ASE has no string aggregate.
     *
     * @throws expDBUnsupportedException
     */
    public static function groupConcat( $expression, $separator = ',', $orderBy = null )
    {
        throw new expDBUnsupportedException( 'ase', 'GROUP_CONCAT', 'join the rows in PHP' );
    }

    /**
     * Seconds since 1970-01-01 UTC, of now (getutcdate()) or of a datetime expression taken as UTC.
     *
     * @param string|null $expression
     * @return string
     */
    public static function unixTimestamp( $expression = null )
    {
        return "datediff(ss, '19700101', " . ( $expression === null ? 'getutcdate()' : $expression ) . ')';
    }

    /**
     * A datetime (UTC) of seconds since 1970.
     *
     * @param string $expression
     * @return string
     */
    public static function fromUnixTimestamp( $expression )
    {
        return "dateadd(ss, $expression, '19700101')";
    }

    public static function charLength( $expression )
    {
        return "char_length($expression)";
    }

    public static function subString( $string, $from, $length = null )
    {
        if ( $length === null || $length === '' || $length === false )
            return "substring($string, $from, char_length($string))";
        return "substring($string, $from, $length)";
    }

    /**
     * Concatenation with + (texts only: + adds numbers).
     *
     * @param string[] $strings
     * @return string
     */
    public static function concatString( array $strings )
    {
        $strings = array_values( $strings );
        if ( !$strings )
            return "''";
        if ( count( $strings ) === 1 )
            return $strings[0];
        return '(' . implode( ' + ', $strings ) . ')';
    }

    /**
     * MD5 as 32 lower-case hex digits (ASE 15.0.2 and later: hash()).
     *
     * @param string $expression
     * @param int $majorVersion unused
     * @return string
     */
    public static function md5( $expression, $majorVersion = 16 )
    {
        return "lower(hash($expression, 'md5'))";
    }

    public static function inList( array $elements, $column, $not = false, $type = false, $chunk = 1000 )
    {
        if ( $type === 'string' )
        {
            $elements = array_map( array( __CLASS__, 'stringLiteral' ), array_map( 'strval', $elements ) );
            $type = false;
        }
        return parent::inList( $elements, $column, $not, $type, $chunk );
    }

    // ------------------------------------------------------------------ temporary tables

    /**
     * MySQL's CREATE TEMPORARY TABLE name as ASE's CREATE TABLE #name with ASE's types; array( sql, #name ) or null.
     *
     * @param string $sql
     * @return array|null
     */
    public static function temporaryTable( $sql )
    {
        $made = parent::temporaryTable( $sql );
        if ( $made === null )
            return null;
        list( $statement, $name ) = $made;
        $tokens = expSQLServerLexer::tokenize( $statement );
        $open = null;
        foreach ( $tokens as $i => $token )
            if ( $token['type'] === expSQLServerLexer::PUNCT && $token['text'] === '(' )
            {
                $open = $i;
                break;
            }
        $close = $open === null ? null : self::matchingParen( $tokens, $open );
        if ( $close === null )
            return $made;
        $columns = array();
        foreach ( self::splitArguments( array_slice( $tokens, $open + 1, $close - $open - 1 ) ) as $column )
            $columns[] = self::columnDefinition( trim( expSQLServerLexer::join( $column ) ) );
        return array( 'CREATE TABLE ' . $name . ' ( ' . implode( ', ', $columns ) . ' )', $name );
    }

    /**
     * A column definition of MySQL's words in ASE's: int(11) INT, varchar(n) UNIVARCHAR(n), text UNITEXT, NULL
     * written out (an ASE column is NOT NULL unless it says NULL).
     *
     * @param string $definition
     * @return string
     */
    public static function columnDefinition( $definition )
    {
        $text = ' ' . preg_replace( '/\s+/', ' ', $definition ) . ' ';
        $notNull = stripos( $text, ' not null ' ) !== false;
        $primary = stripos( $text, ' primary key ' ) !== false;
        $text = trim( preg_replace( '/\s+/', ' ', str_ireplace( array( ' not null ', ' primary key ', ' null ' ), ' ', $text ) ) );
        $text = preg_replace( '/\b(?:int|integer|mediumint)\s*(\(\s*\d+\s*\))?(\s+unsigned)?/i', 'INT', $text );
        $text = preg_replace( '/\btinyint\s*(\(\s*\d+\s*\))?(\s+unsigned)?/i', 'SMALLINT', $text );
        $text = preg_replace( '/\bbigint\s*(\(\s*\d+\s*\))?(\s+unsigned)?/i', 'BIGINT', $text );
        $text = preg_replace( '/\bn?varchar\b/i', 'UNIVARCHAR', $text );
        $text = preg_replace( '/\b(?:longtext|mediumtext|tinytext|text)\b/i', 'UNITEXT', $text );
        if ( preg_match( '/^([A-Za-z_][A-Za-z0-9_$#]*)(\s.*)?$/s', $text, $m ) )
            $text = self::quoteIdentifierIfNeeded( $m[1] ) . ( isset( $m[2] ) ? $m[2] : '' );
        return $text . ( $notNull || $primary ? ' NOT NULL' : ' NULL' ) . ( $primary ? ' PRIMARY KEY' : '' );
    }

    /**
     * DROP TABLE #name that never fails.
     *
     * @param string $name
     * @return string
     */
    public static function dropTemporaryTable( $name )
    {
        $name = (string)$name;
        if ( $name === '' || $name[0] !== '#' )
            $name = '#' . $name;
        self::checkIdentifier( $name );
        return 'IF OBJECT_ID(' . self::stringLiteral( 'tempdb..' . $name ) . ') IS NOT NULL DROP TABLE ' . $name;
    }

    // ------------------------------------------------------------------ connection

    /**
     * The statements every new connection runs first: quoted identifiers, unchained (autocommit) mode, the whole of
     * a text column in a result, standard NULL comparisons, and no error for a string cut to its column's length
     * (MySQL's behaviour).
     *
     * @return string
     */
    public static function sessionSetup( $lockTimeoutMs = -1 )
    {
        $sql = 'set quoted_identifier on set chained off set textsize 2147483647 set ansinull on set string_rtruncation off';
        if ( (int)$lockTimeoutMs >= 0 )
            $sql .= ' set lock wait ' . max( 0, (int)ceil( $lockTimeoutMs / 1000 ) );
        return $sql;
    }

    /**
     * A PDO DSN for pdo_dblib (FreeTDS) at TDS 5.0, UTF-8.
     *
     * @param array $config host, port, database, app_name
     * @return string
     */
    public static function dblibDSN( array $config )
    {
        $host = trim( (string)$config['host'] );
        $port = isset( $config['port'] ) && (int)$config['port'] > 0 ? (int)$config['port'] : self::DEFAULT_PORT;
        $dsn = 'dblib:host=' . ( $host === '' ? 'localhost' : $host ) . ':' . $port . ';version=5.0;charset=UTF-8';
        if ( isset( $config['database'] ) && (string)$config['database'] !== '' )
            $dsn .= ';dbname=' . $config['database'];
        if ( isset( $config['app_name'] ) && (string)$config['app_name'] !== '' )
            $dsn .= ';appname=' . $config['app_name'];
        return $dsn;
    }

    /**
     * A PDO DSN for PDO_ODBC: FreeTDS's ODBC driver (TDS_Version=5.0) or SAP's own ASE ODBC driver (its name has
     * "Adaptive Server").
     *
     * @param array $config
     * @param string $driver
     * @return string
     */
    public static function odbcDSN( array $config, $driver = 'FreeTDS' )
    {
        $host = trim( (string)$config['host'] ) === '' ? 'localhost' : trim( (string)$config['host'] );
        $port = isset( $config['port'] ) && (int)$config['port'] > 0 ? (int)$config['port'] : self::DEFAULT_PORT;
        $parts = array( 'Driver={' . str_replace( '}', '}}', (string)$driver ) . '}' );
        if ( stripos( (string)$driver, 'adaptive server' ) !== false )
            $parts = array_merge( $parts, array( 'Server=' . self::connectionValue( $host ), 'Port=' . $port, 'CharSet=utf8' ) );
        else
            $parts = array_merge( $parts, array( 'Server=' . self::connectionValue( $host ), 'Port=' . $port, 'TDS_Version=5.0', 'ClientCharset=UTF-8' ) );
        if ( isset( $config['database'] ) && (string)$config['database'] !== '' )
            $parts[] = 'Database=' . self::connectionValue( $config['database'] );
        return 'odbc:' . implode( ';', $parts );
    }

    /**
     * What @@version says: "Adaptive Server Enterprise/16.0 SP04 PL04/EBF 30399 SMP/P/x86_64/SLES 12.4/..." .
     *
     * @param string $productVersion
     * @param mixed $engineEdition unused
     * @return array major, minor, sp, string, values, name, supported, note
     */
    public static function describeVersion( $productVersion, $engineEdition = null )
    {
        $text = (string)$productVersion;
        $major = 0;
        $minor = 0;
        $sp = 0;
        if ( preg_match( '#/(\d+)\.(\d+)(?:\.(\d+))?(?:\s+SP(\d+))?#i', $text, $m ) || preg_match( '#^(\d+)\.(\d+)(?:\.(\d+))?(?:\s+SP(\d+))?#', $text, $m ) )
        {
            $major = (int)$m[1];
            $minor = (int)$m[2];
            $sp = isset( $m[4] ) ? (int)$m[4] : 0;
        }
        $string = $major ? $major . '.' . $minor . ( $sp ? ' SP' . sprintf( '%02d', $sp ) : '' ) : $text;
        $supported = (float)( $major . '.' . $minor ) >= self::MIN_VERSION;
        return array( 'major' => $major, 'minor' => $minor, 'sp' => $sp, 'string' => $string, 'values' => array( $major, $minor, $sp ),
                      'name' => 'SAP ASE ' . $string, 'supported' => $supported,
                      'note' => $supported ? '' : 'SAP ASE 15.7 or later is needed (MERGE, int identity columns, hash())' );
    }

    /**
     * The kind of an error, from ASE's message number: deadlock (1205), lock (12205, 12207: a lock wait ran out),
     * duplicate (2601, 2627), connection (state 08, or the client's "connection" messages), syntax (102, 156),
     * unknown_object (207, 208), or other.
     *
     * @param int|null $number
     * @param string $message
     * @param string|null $state
     * @return string
     */
    public static function errorCategory( $number, $message = '', $state = null )
    {
        $number = (int)$number;
        $message = strtolower( (string)$message );
        if ( $number === 1205 || strpos( $message, 'deadlock' ) !== false )
            return 'deadlock';
        if ( in_array( $number, array( 12205, 12207 ), true ) || strpos( $message, 'lock wait' ) !== false )
            return 'lock';
        if ( in_array( $number, array( 2601, 2627 ), true ) || strpos( $message, 'duplicate key' ) !== false )
            return 'duplicate';
        if ( strpos( strtoupper( (string)$state ), '08' ) === 0 || in_array( $number, array( 20004, 20006, 20009, 20047 ), true )
             || strpos( $message, 'connection' ) !== false && ( strpos( $message, 'closed' ) !== false || strpos( $message, 'lost' ) !== false || strpos( $message, 'dead' ) !== false ) )
            return 'connection';
        if ( in_array( $number, array( 102, 156 ), true ) )
            return 'syntax';
        if ( in_array( $number, array( 207, 208 ), true ) )
            return 'unknown_object';
        return 'other';
    }
}
