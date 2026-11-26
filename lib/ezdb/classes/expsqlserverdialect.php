<?php
/**
 * File containing the expSQLServerDialect class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The Transact-SQL that expSQLServerDB writes, as pure functions of their arguments: no connection, no settings.
 * Every rule here is tested without a server (tests/tests/lib/ezdb/sqlserver).
 *
 * Supported servers: SQL Server 2017 (14.x), 2019 (15.x), 2022 (16.x), 2025 (17.x) and Azure SQL Database / Azure
 * SQL Managed Instance. 2017 is the floor because of STRING_AGG(), TRIM() and CONCAT_WS(); a UTF-8 collation (used
 * by md5()) needs 2019. Azure SQL Edge was retired by Microsoft on 30 September 2025 and is not a target.
 */
class expSQLServerDialect
{
    /** Longest name SQL Server accepts (sysname) */
    const MAX_IDENTIFIER_LENGTH = 128;

    /** Longest name of a local temporary table (#name): SQL Server adds a suffix of its own up to 128 */
    const MAX_TEMP_TABLE_NAME_LENGTH = 116;

    /** Most parameters one statement or procedure call may take (error 8003 past it) */
    const MAX_PARAMETERS = 2100;

    /** Parameters a statement may take here: room for the ones the driver adds (paging, identity) */
    const PARAMETER_BUDGET = 2000;

    /** Largest key of a clustered index, in bytes (error 1946 warns, a longer key value fails) */
    const MAX_CLUSTERED_KEY_BYTES = 900;

    /** Largest key of a nonclustered index, in bytes (SQL Server 2016 and later) */
    const MAX_NONCLUSTERED_KEY_BYTES = 1700;

    /** Characters an NVARCHAR(n) column may declare before it must be NVARCHAR(MAX) */
    const MAX_NVARCHAR_LENGTH = 4000;

    /** Bytes a VARCHAR(n) / VARBINARY(n) column may declare before it must be (MAX) */
    const MAX_VARCHAR_LENGTH = 8000;

    /** A local temporary table name starts with this */
    const TEMP_PREFIX = '#';

    /** Lowest supported major version (SQL Server 2017) */
    const MIN_MAJOR_VERSION = 14;

    /**
     * The reserved keywords of Transact-SQL (Microsoft's list "Reserved keywords (Transact-SQL)"): a column or table
     * of one of these names must be written in brackets.
     *
     * @var string[]
     */
    const RESERVED_WORDS = array(
        'ADD', 'ALL', 'ALTER', 'AND', 'ANY', 'AS', 'ASC', 'AUTHORIZATION', 'BACKUP', 'BEGIN', 'BETWEEN', 'BREAK',
        'BROWSE', 'BULK', 'BY', 'CASCADE', 'CASE', 'CHECK', 'CHECKPOINT', 'CLOSE', 'CLUSTERED', 'COALESCE', 'COLLATE',
        'COLUMN', 'COMMIT', 'COMPUTE', 'CONSTRAINT', 'CONTAINS', 'CONTAINSTABLE', 'CONTINUE', 'CONVERT', 'CREATE',
        'CROSS', 'CURRENT', 'CURRENT_DATE', 'CURRENT_TIME', 'CURRENT_TIMESTAMP', 'CURRENT_USER', 'CURSOR', 'DATABASE',
        'DBCC', 'DEALLOCATE', 'DECLARE', 'DEFAULT', 'DELETE', 'DENY', 'DESC', 'DISK', 'DISTINCT', 'DISTRIBUTED',
        'DOUBLE', 'DROP', 'DUMP', 'ELSE', 'END', 'ERRLVL', 'ESCAPE', 'EXCEPT', 'EXEC', 'EXECUTE', 'EXISTS', 'EXIT',
        'EXTERNAL', 'FETCH', 'FILE', 'FILLFACTOR', 'FOR', 'FOREIGN', 'FREETEXT', 'FREETEXTTABLE', 'FROM', 'FULL',
        'FUNCTION', 'GOTO', 'GRANT', 'GROUP', 'HAVING', 'HOLDLOCK', 'IDENTITY', 'IDENTITY_INSERT', 'IDENTITYCOL', 'IF',
        'IN', 'INDEX', 'INNER', 'INSERT', 'INTERSECT', 'INTO', 'IS', 'JOIN', 'KEY', 'KILL', 'LEFT', 'LIKE', 'LINENO',
        'LOAD', 'MERGE', 'NATIONAL', 'NOCHECK', 'NONCLUSTERED', 'NOT', 'NULL', 'NULLIF', 'OF', 'OFF', 'OFFSETS', 'ON',
        'OPEN', 'OPENDATASOURCE', 'OPENQUERY', 'OPENROWSET', 'OPENXML', 'OPTION', 'OR', 'ORDER', 'OUTER', 'OVER',
        'PERCENT', 'PIVOT', 'PLAN', 'PRECISION', 'PRIMARY', 'PRINT', 'PROC', 'PROCEDURE', 'PUBLIC', 'RAISERROR', 'READ',
        'READTEXT', 'RECONFIGURE', 'REFERENCES', 'REPLICATION', 'RESTORE', 'RESTRICT', 'RETURN', 'REVERT', 'REVOKE',
        'RIGHT', 'ROLLBACK', 'ROWCOUNT', 'ROWGUIDCOL', 'RULE', 'SAVE', 'SCHEMA', 'SECURITYAUDIT', 'SELECT',
        'SEMANTICKEYPHRASETABLE', 'SEMANTICSIMILARITYDETAILSTABLE', 'SEMANTICSIMILARITYTABLE', 'SESSION_USER', 'SET',
        'SETUSER', 'SHUTDOWN', 'SOME', 'STATISTICS', 'SYSTEM_USER', 'TABLE', 'TABLESAMPLE', 'TEXTSIZE', 'THEN', 'TO',
        'TOP', 'TRAN', 'TRANSACTION', 'TRIGGER', 'TRUNCATE', 'TRY_CONVERT', 'TSEQUAL', 'UNION', 'UNIQUE', 'UNPIVOT',
        'UPDATE', 'UPDATETEXT', 'USE', 'USER', 'VALUES', 'VARYING', 'VIEW', 'WAITFOR', 'WHEN', 'WHERE', 'WHILE', 'WITH',
        'WITHIN', 'WRITETEXT',
    );

    /**
     * Reserved words that can only be names (columns, tables) inside SELECT, INSERT, UPDATE, DELETE and MERGE: they start
     * statements of their own (CHECKPOINT, BACKUP, DBCC ...) or belong to DDL only (KEY, FILLFACTOR ...). The driver
     * brackets them there; ambiguous ones (USER is a function, ORDER and OPTION start clauses) are left alone.
     *
     * @var string[]
     */
    const NAME_ONLY_RESERVED_WORDS = array( 'BACKUP', 'CHECKPOINT', 'DBCC', 'DUMP', 'ERRLVL', 'FILE', 'FILLFACTOR', 'KEY', 'KILL', 'LINENO',
        'LOAD', 'OFFSETS', 'PLAN', 'PUBLIC', 'READTEXT', 'RECONFIGURE', 'RESTORE', 'RULE', 'SECURITYAUDIT', 'SETUSER', 'SHUTDOWN',
        'STATISTICS', 'TSEQUAL', 'UPDATETEXT', 'WRITETEXT' );

    /** Table hints that take or keep a lock a read replica cannot give: a statement with one runs on the primary */
    const LOCKING_HINTS = array( 'UPDLOCK', 'XLOCK', 'HOLDLOCK', 'TABLOCKX', 'SERIALIZABLE', 'REPEATABLEREAD', 'PAGLOCK', 'ROWLOCK', 'TABLOCK' );

    /** @var array|null RESERVED_WORDS as keys */
    private static $reserved = null;

    // ------------------------------------------------------------------ names

    /**
     * Whether $word is a reserved keyword of Transact-SQL (any case).
     *
     * @param string $word
     * @return bool
     */
    public static function isReservedWord( $word )
    {
        if ( self::$reserved === null )
            self::$reserved = array_fill_keys( self::RESERVED_WORDS, true );
        return isset( self::$reserved[strtoupper( (string)$word )] );
    }

    /**
     * One name in brackets, with a "]" inside doubled. A name of more than 128 characters (116 for a #temporary
     * table), an empty name and a name with a NUL byte are refused.
     *
     * @param string $name
     * @return string
     * @throws InvalidArgumentException
     */
    public static function quoteIdentifier( $name )
    {
        $name = (string)$name;
        self::checkIdentifier( $name );
        return '[' . str_replace( ']', ']]', $name ) . ']';
    }

    /**
     * A name that may have several parts (schema.table, database.schema.table, table.column): each part in brackets.
     * A part already in brackets or double quotes is taken as it is written, with its own escapes.
     *
     * @param string $name
     * @return string
     */
    public static function quoteName( $name )
    {
        $parts = array();
        $current = '';
        $pending = false;
        foreach ( expSQLServerLexer::tokenize( (string)$name ) as $token )
        {
            if ( $token['type'] === expSQLServerLexer::PUNCT && $token['text'] === '.' )
            {
                $parts[] = $current;
                $current = '';
                $pending = false;
                continue;
            }
            if ( expSQLServerLexer::isNoise( $token ) && !$pending )
                continue;
            $current .= $token['type'] === expSQLServerLexer::IDENT ? expSQLServerLexer::identifierValue( $token ) : $token['text'];
            $pending = true;
        }
        $parts[] = $current;
        return implode( '.', array_map( array( __CLASS__, 'quoteIdentifier' ), array_map( 'rtrim', $parts ) ) );
    }

    /**
     * A name only in brackets when it must be: a reserved word, or a name that is not a regular identifier.
     *
     * @param string $name
     * @return string
     */
    public static function quoteIdentifierIfNeeded( $name )
    {
        $name = (string)$name;
        if ( !self::isReservedWord( $name ) && preg_match( '/^[A-Za-z_#][A-Za-z0-9_@#$]*$/', $name ) && strlen( $name ) <= self::MAX_IDENTIFIER_LENGTH )
            return $name;
        return self::quoteIdentifier( $name );
    }

    /**
     * Refuses a name SQL Server would refuse.
     *
     * @param string $name
     * @throws InvalidArgumentException
     */
    public static function checkIdentifier( $name )
    {
        if ( $name === '' )
            throw new InvalidArgumentException( 'An empty name is not a SQL Server identifier' );
        if ( strpos( $name, "\0" ) !== false )
            throw new InvalidArgumentException( 'A SQL Server identifier cannot hold a NUL byte' );
        $max = ( $name[0] === '#' && ( !isset( $name[1] ) || $name[1] !== '#' ) ) ? self::MAX_TEMP_TABLE_NAME_LENGTH : self::MAX_IDENTIFIER_LENGTH;
        $length = function_exists( 'mb_strlen' ) ? mb_strlen( $name, 'UTF-8' ) : strlen( $name );
        if ( $length > $max )
            throw new InvalidArgumentException( "The SQL Server identifier '" . substr( $name, 0, 40 ) . "...' has $length characters, more than $max" );
    }

    // ------------------------------------------------------------------ literals

    /**
     * A value for the inside of a string literal: quotes doubled, NUL bytes dropped (a NUL ends a string in parts
     * of the client stack). Backslashes stay as they are: only a backslash right before a line break means anything
     * to T-SQL (see unicodeLiterals()).
     *
     * @param string $value
     * @return string
     */
    public static function escapeString( $value )
    {
        return str_replace( array( "\0", "'" ), array( '', "''" ), (string)$value );
    }

    /**
     * A complete Unicode string literal: N'...'.
     *
     * @param string $value
     * @return string
     */
    public static function stringLiteral( $value )
    {
        return self::nliteral( self::escapeString( $value ) );
    }

    /**
     * Any PHP value as a literal: null as NULL, a bool as 1 or 0, an integer as its digits, a float in plain
     * notation (no exponent SQL Server would read as FLOAT where DECIMAL was meant), anything else as N'...'.
     *
     * @param mixed $value
     * @return string
     */
    public static function literal( $value )
    {
        if ( $value === null )
            return 'NULL';
        if ( is_bool( $value ) )
            return $value ? '1' : '0';
        if ( is_int( $value ) )
            return (string)$value;
        if ( is_float( $value ) )
        {
            if ( is_nan( $value ) || is_infinite( $value ) )
                return 'NULL';
            $text = rtrim( rtrim( sprintf( '%.15F', $value ), '0' ), '.' );
            return $text === '' || $text === '-' ? '0' : $text;
        }
        return self::stringLiteral( (string)$value );
    }

    /**
     * Bytes as a binary literal (0x...); an empty value is 0x.
     *
     * @param string $bytes
     * @return string
     */
    public static function binaryLiteral( $bytes )
    {
        return '0x' . strtoupper( bin2hex( (string)$bytes ) );
    }

    /**
     * Turns every '...' literal of a statement into N'...' (Unicode) and splits a literal that holds a backslash
     * right before a line break, which T-SQL would otherwise remove as a line continuation. Nothing outside the
     * literals changes; a quote, keyword or "N" inside a literal or a comment stays as it is.
     *
     * @param string $sql
     * @return string
     */
    public static function unicodeLiterals( $sql )
    {
        $sql = (string)$sql;
        if ( strpos( $sql, "'" ) === false )
            return $sql;
        $tokens = expSQLServerLexer::tokenize( $sql );
        $out = '';
        foreach ( $tokens as $token )
        {
            if ( ( $token['type'] === expSQLServerLexer::STRING || $token['type'] === expSQLServerLexer::NSTRING ) && !$token['open'] )
                $out .= self::nliteral( self::escapeString( expSQLServerLexer::literalValue( $token ) ) );
            else
                $out .= $token['text'];
        }
        return $out;
    }

    /**
     * N'escaped', split around a backslash before a line break: 'a\<LF>b' becomes (N'a\' + NCHAR(10) + N'b').
     *
     * @param string $escaped the inside of the literal, quotes already doubled
     * @return string
     */
    private static function nliteral( $escaped )
    {
        if ( strpos( $escaped, "\\\n" ) === false && strpos( $escaped, "\\\r" ) === false )
            return "N'" . $escaped . "'";
        $parts = preg_split( '/(?<=\\\\)(\r\n|\r|\n)/', $escaped, -1, PREG_SPLIT_DELIM_CAPTURE );
        $pieces = array();
        foreach ( $parts as $i => $part )
        {
            if ( $i % 2 === 1 )
                $pieces[] = str_replace( array( "\r", "\n" ), array( 'NCHAR(13)', 'NCHAR(10)' ), $part === "\r\n" ? "\r + \n" : $part );
            else if ( $part !== '' )
                $pieces[] = "N'" . $part . "'";
        }
        return '(' . implode( ' + ', $pieces ) . ')';
    }

    /**
     * The statement with its string literals as "?" parameters, for a statement that can take them (a query or a
     * data change; not DDL, where a DEFAULT must be a literal). array( sql, values ) or null when the statement is
     * better sent with Unicode literals instead (unicodeLiterals()):
     *  - not a SELECT/INSERT/UPDATE/DELETE/MERGE (or WITH ... one of them);
     *  - a GROUP BY: an expression of the select list must equal one of GROUP BY, and two parameters never do;
     *  - more literals than PARAMETER_BUDGET (the server takes at most 2,100 parameters);
     *  - a "?" already outside the literals;
     *  - a literal that is not closed.
     *
     * @param string $sql
     * @return array|null array( string $sql, string[] $values )
     */
    public static function parameterize( $sql )
    {
        $sql = (string)$sql;
        if ( strpos( $sql, "'" ) === false )
            return array( $sql, array() );
        $kind = self::statementKind( $sql );
        if ( !in_array( $kind, array( 'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'MERGE' ), true ) )
            return null;
        $tokens = expSQLServerLexer::tokenize( $sql );
        $values = array();
        $out = '';
        $previous = '';
        // the function each open parenthesis belongs to, and the last significant token
        $calls = array();
        $lastWord = '';
        $lastSignificant = '';
        foreach ( $tokens as $token )
        {
            if ( $token['type'] === expSQLServerLexer::PARAM )
                return null;
            if ( $token['type'] === expSQLServerLexer::PUNCT && $token['text'] === '(' )
                $calls[] = $lastSignificant === 'word' ? $lastWord : '';
            else if ( $token['type'] === expSQLServerLexer::PUNCT && $token['text'] === ')' )
                array_pop( $calls );
            // STRING_AGG's separator stays a literal: the server refuses anything else there
            if ( ( $token['type'] === expSQLServerLexer::STRING || $token['type'] === expSQLServerLexer::NSTRING ) && !$token['open']
                 && $calls && end( $calls ) === 'STRING_AGG' && $lastSignificant === ',' )
            {
                $out .= self::stringLiteral( expSQLServerLexer::literalValue( $token ) );
                $lastSignificant = 'literal';
                continue;
            }
            if ( !expSQLServerLexer::isNoise( $token ) )
            {
                $lastSignificant = $token['type'] === expSQLServerLexer::WORD ? 'word' : $token['text'];
                if ( $token['type'] === expSQLServerLexer::WORD )
                    $lastWord = strtoupper( $token['text'] );
            }
            if ( $token['type'] === expSQLServerLexer::WORD )
            {
                $word = strtoupper( $token['text'] );
                if ( $word === 'BY' && $previous === 'GROUP' )
                    return null;
                $previous = $word;
            }
            else if ( !expSQLServerLexer::isNoise( $token ) )
                $previous = '';
            if ( $token['type'] === expSQLServerLexer::STRING || $token['type'] === expSQLServerLexer::NSTRING )
            {
                if ( $token['open'] )
                    return null;
                $values[] = str_replace( "\0", '', expSQLServerLexer::literalValue( $token ) );
                if ( count( $values ) > self::PARAMETER_BUDGET )
                    return null;
                $out .= '?';
                continue;
            }
            $out .= $token['text'];
        }
        return array( $out, $values );
    }

    /**
     * A query or data change with every NAME_ONLY_RESERVED_WORDS word outside literals, comments and brackets in
     * brackets: INSERT INTO expqueue_job (checkpoint) becomes INSERT INTO expqueue_job ([checkpoint]).
     *
     * @param string $sql
     * @return string
     */
    public static function bracketNameOnlyWords( $sql )
    {
        $sql = (string)$sql;
        if ( !in_array( self::statementKind( $sql ), array( 'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'MERGE' ), true ) )
            return $sql;
        if ( !preg_match( '/\\b(?:' . implode( '|', self::NAME_ONLY_RESERVED_WORDS ) . ')\\b/i', $sql ) )
            return $sql;
        $out = '';
        foreach ( expSQLServerLexer::tokenize( $sql ) as $token )
        {
            if ( $token['type'] === expSQLServerLexer::WORD && in_array( strtoupper( $token['text'] ), self::NAME_ONLY_RESERVED_WORDS, true ) )
                $out .= '[' . $token['text'] . ']';
            else
                $out .= $token['text'];
        }
        return $out;
    }

    /**
     * MySQL style `name` as [name]. Nothing else changes.
     *
     * @param string $sql
     * @return string
     */
    public static function backticksToBrackets( $sql )
    {
        $sql = (string)$sql;
        if ( strpos( $sql, '`' ) === false )
            return $sql;
        $out = '';
        foreach ( expSQLServerLexer::tokenize( $sql ) as $token )
        {
            if ( $token['type'] === expSQLServerLexer::IDENT && $token['text'][0] === '`' && !$token['open'] )
                $out .= self::quoteIdentifier( expSQLServerLexer::identifierValue( $token ) );
            else
                $out .= $token['text'];
        }
        return $out;
    }

    // ------------------------------------------------------------------ statements

    /**
     * What a statement does: SELECT, INSERT, UPDATE, DELETE, MERGE, CREATE, ALTER, DROP, TRUNCATE, EXEC, SET,
     * BEGIN, COMMIT, ROLLBACK, DECLARE ... (its first keyword, upper case). A WITH statement is the kind of the
     * statement after its common table expressions; "(SELECT ...)" is a SELECT. '' for an empty statement.
     *
     * @param string $sql
     * @return string
     */
    public static function statementKind( $sql )
    {
        $sql = (string)$sql;
        if ( preg_match( '/^\s*([A-Za-z]+)\b/', $sql, $m ) && strtoupper( $m[1] ) !== 'WITH' )
        {
            $kind = strtoupper( $m[1] );
            return $kind === 'EXECUTE' ? 'EXEC' : $kind;
        }
        $tokens = expSQLServerLexer::tokenize( $sql );
        $first = null;
        foreach ( expSQLServerLexer::significant( $tokens ) as $entry )
        {
            list( $i, $depth ) = $entry;
            $word = expSQLServerLexer::keyword( $tokens[$i] );
            if ( $first === null )
            {
                if ( $tokens[$i]['text'] === '(' )
                    continue;
                if ( $word === '' )
                    return '';
                $first = $word;
                if ( $first !== 'WITH' )
                    return $first === 'EXECUTE' ? 'EXEC' : $first;
                continue;
            }
            if ( $depth === 0 && in_array( $word, array( 'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'MERGE' ), true ) )
                return $word;
        }
        return $first === null ? '' : $first;
    }

    /**
     * Whether the statement is one statement: no ";" at the outer level other than at its end.
     *
     * @param string $sql
     * @return bool
     */
    public static function isSingleStatement( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( (string)$sql );
        $significant = expSQLServerLexer::significant( $tokens );
        $count = count( $significant );
        foreach ( $significant as $n => $entry )
        {
            list( $i, $depth ) = $entry;
            if ( $tokens[$i]['text'] === ';' && $depth === 0 )
            {
                for ( $k = $n + 1; $k < $count; $k++ )
                    if ( $tokens[$significant[$k][0]]['text'] !== ';' )
                        return false;
            }
        }
        return true;
    }

    /**
     * The offset (in $tokens) of the outer ORDER of the last outer ORDER BY, or null.
     *
     * @param array[] $tokens
     * @return int|null
     */
    public static function outerOrderBy( array $tokens )
    {
        $found = null;
        $significant = expSQLServerLexer::significant( $tokens );
        foreach ( $significant as $n => $entry )
        {
            list( $i, $depth ) = $entry;
            if ( $depth !== 0 || expSQLServerLexer::keyword( $tokens[$i] ) !== 'ORDER' )
                continue;
            if ( isset( $significant[$n + 1] ) && expSQLServerLexer::keyword( $tokens[$significant[$n + 1][0]] ) === 'BY' )
                $found = $i;
        }
        return $found;
    }

    /**
     * The facts about the outer level of a SELECT that paging needs.
     *
     * @param string $sql
     * @return array keys: order (token offset or null), tail (token offset of an outer OPTION/FOR clause, or null),
     *               top (bool), distinct (bool), setop (UNION/EXCEPT/INTERSECT, bool), offset (an OFFSET already, bool)
     */
    public static function outerShape( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( (string)$sql );
        $shape = array( 'order' => null, 'tail' => null, 'top' => false, 'distinct' => false, 'setop' => false, 'offset' => false );
        $significant = expSQLServerLexer::significant( $tokens );
        $afterSelect = false;
        foreach ( $significant as $n => $entry )
        {
            list( $i, $depth ) = $entry;
            if ( $depth !== 0 )
                continue;
            $word = expSQLServerLexer::keyword( $tokens[$i] );
            $next = isset( $significant[$n + 1] ) ? expSQLServerLexer::keyword( $tokens[$significant[$n + 1][0]] ) : '';
            if ( $word === 'SELECT' )
            {
                $afterSelect = true;
                continue;
            }
            if ( $afterSelect && ( $word === 'ALL' || $word === 'DISTINCT' ) )
            {
                if ( $word === 'DISTINCT' )
                    $shape['distinct'] = true;
                continue;
            }
            if ( $afterSelect && $word === 'TOP' )
                $shape['top'] = true;
            $afterSelect = false;
            if ( $word === 'ORDER' && $next === 'BY' )
                $shape['order'] = $i;
            else if ( $word === 'UNION' || $word === 'EXCEPT' || $word === 'INTERSECT' )
            {
                $shape['setop'] = true;
                $shape['order'] = null;
            }
            else if ( $word === 'OFFSET' && $shape['order'] !== null )
                $shape['offset'] = true;
            else if ( $shape['tail'] === null && ( $word === 'OPTION' || ( $word === 'FOR' && in_array( $next, array( 'XML', 'JSON', 'BROWSE' ), true ) ) ) )
                $shape['tail'] = $i;
        }
        return $shape;
    }

    /**
     * A SELECT with LIMIT and OFFSET in SQL Server's words: ORDER BY ... OFFSET n ROWS FETCH NEXT m ROWS ONLY.
     *
     *  - Its own ORDER BY is kept; a statement without one gets ORDER BY (SELECT NULL), which orders nothing (the
     *    rows come in no promised order, as on MySQL without ORDER BY) but makes OFFSET legal.
     *  - A statement with TOP, an OFFSET of its own, DISTINCT without ORDER BY, or a UNION/EXCEPT/INTERSECT without
     *    ORDER BY is wrapped: SELECT * FROM ( ... ) AS exp_page ORDER BY (SELECT NULL) OFFSET ...
     *  - An outer OPTION (...) or FOR XML/JSON stays at the end.
     *  - A limit of 0 returns null: FETCH NEXT 0 ROWS is an error (10744), and the answer is no rows anyway.
     *  - No limit (false/null/'' or negative) and no offset returns the statement as it is; an offset alone pages
     *    without FETCH.
     *  - A statement that is not a SELECT is returned as it is.
     *
     * @param string $sql
     * @param int|string|false|null $limit
     * @param int|string $offset
     * @return string|null
     */
    public static function applyLimit( $sql, $limit, $offset = 0 )
    {
        $sql = rtrim( (string)$sql );
        while ( substr( $sql, -1 ) === ';' )
            $sql = rtrim( substr( $sql, 0, -1 ) );
        $limit = ( $limit === false || $limit === null || $limit === '' || !is_numeric( $limit ) || $limit < 0 ) ? false : (int)$limit;
        $offset = is_numeric( $offset ) ? max( 0, (int)$offset ) : 0;
        if ( $limit === 0 )
            return null;
        if ( $limit === false && $offset === 0 )
            return $sql;
        if ( self::statementKind( $sql ) !== 'SELECT' )
            return $sql;
        $paging = "OFFSET $offset ROWS" . ( $limit !== false ? " FETCH NEXT $limit ROWS ONLY" : '' );
        $shape = self::outerShape( $sql );
        $tokens = expSQLServerLexer::tokenize( $sql );
        $wrap = $shape['top'] || $shape['offset'] || ( $shape['order'] === null && ( $shape['distinct'] || $shape['setop'] ) );
        $tail = '';
        if ( $shape['tail'] !== null )
        {
            $tail = ' ' . trim( expSQLServerLexer::join( array_slice( $tokens, $shape['tail'] ) ) );
            $tokens = array_slice( $tokens, 0, $shape['tail'] );
        }
        $body = rtrim( expSQLServerLexer::join( $tokens ) );
        if ( $wrap )
            return "SELECT * FROM (\n$body\n) AS exp_page\nORDER BY (SELECT NULL)\n$paging" . $tail;
        if ( $shape['order'] === null )
            return "$body\nORDER BY (SELECT NULL)\n$paging" . $tail;
        return "$body\n$paging" . $tail;
    }

    /**
     * Whether a statement may run on a read replica (an Always On readable secondary): one SELECT that writes
     * nothing (no SELECT ... INTO), takes no lock a secondary cannot give (UPDLOCK, XLOCK, HOLDLOCK, TABLOCKX ...),
     * reads no temporary table (#name lives in the session that made it, on the primary), asks no sequence
     * (NEXT VALUE FOR) and no session value (@@IDENTITY, SCOPE_IDENTITY(), @@SPID ...). Words inside literals and
     * comments do not count.
     *
     * @param string $sql
     * @return bool
     */
    public static function isReplicaReadable( $sql )
    {
        $sql = (string)$sql;
        if ( self::statementKind( $sql ) !== 'SELECT' || !self::isSingleStatement( $sql ) )
            return false;
        $tokens = expSQLServerLexer::tokenize( $sql );
        $significant = expSQLServerLexer::significant( $tokens );
        foreach ( $significant as $n => $entry )
        {
            $token = $tokens[$entry[0]];
            if ( $token['type'] === expSQLServerLexer::IDENT && strpos( expSQLServerLexer::identifierValue( $token ), '#' ) === 0 )
                return false;
            if ( $token['type'] !== expSQLServerLexer::WORD )
                continue;
            $word = strtoupper( $token['text'] );
            if ( $word[0] === '#' )
                return false;
            if ( in_array( $word, array( 'INTO', 'INSERT', 'UPDATE', 'DELETE', 'MERGE', 'EXEC', 'EXECUTE', 'SCOPE_IDENTITY',
                                         '@@IDENTITY', '@@SPID', '@@TRANCOUNT', 'IDENT_CURRENT', 'SP_GETAPPLOCK' ), true )
                 || in_array( $word, self::LOCKING_HINTS, true ) )
                return false;
            if ( $word === 'NEXT' && isset( $significant[$n + 1] ) && expSQLServerLexer::keyword( $tokens[$significant[$n + 1][0]] ) === 'VALUE' )
                return false;
        }
        return true;
    }

    /**
     * A statement that changes the schema (and so the cached identity columns).
     *
     * @param string $sql
     * @return bool
     */
    public static function isSchemaChange( $sql )
    {
        return in_array( self::statementKind( $sql ), array( 'CREATE', 'ALTER', 'DROP', 'TRUNCATE', 'SP_RENAME' ), true )
               || ( self::statementKind( $sql ) === 'EXEC' && stripos( (string)$sql, 'sp_rename' ) !== false );
    }

    /**
     * The table of an INSERT INTO statement (as written, without brackets or schema), or null.
     *
     * @param string $sql
     * @return string|null
     */
    public static function insertTable( $sql )
    {
        $parsed = self::parseInsert( $sql );
        return $parsed === null ? null : $parsed['table'];
    }

    /**
     * Reads INSERT [INTO] table [(columns)] VALUES (row)[, (row) ...] | SELECT ... | DEFAULT VALUES.
     *
     * @param string $sql
     * @return array|null keys: table (last name part, unquoted), tableText (as written), columns (unquoted names, or
     *                    null), columnTokens (array( first, last ) token offsets of the column list inside the
     *                    parentheses), rows (each a list of array( first, last ) token offsets of a value), tokens
     */
    public static function parseInsert( $sql )
    {
        $sql = (string)$sql;
        if ( !preg_match( '/^\s*INSERT\b/i', $sql ) && self::statementKind( $sql ) !== 'INSERT' )
            return null;
        $tokens = expSQLServerLexer::tokenize( $sql );
        $significant = expSQLServerLexer::significant( $tokens );
        $n = 0;
        $count = count( $significant );
        while ( $n < $count && expSQLServerLexer::keyword( $tokens[$significant[$n][0]] ) !== 'INSERT' )
            $n++;
        $n++;
        if ( $n < $count && expSQLServerLexer::keyword( $tokens[$significant[$n][0]] ) === 'INTO' )
            $n++;
        if ( $n >= $count )
            return null;
        // table: name tokens joined by dots
        $nameParts = array();
        $tableText = '';
        while ( $n < $count )
        {
            $token = $tokens[$significant[$n][0]];
            if ( $token['type'] === expSQLServerLexer::WORD || $token['type'] === expSQLServerLexer::IDENT )
            {
                $nameParts[] = expSQLServerLexer::identifierValue( $token );
                $tableText .= $token['text'];
                $n++;
                if ( $n < $count && $tokens[$significant[$n][0]]['text'] === '.' )
                {
                    $tableText .= '.';
                    $n++;
                    continue;
                }
                break;
            }
            return null;
        }
        if ( !$nameParts )
            return null;
        $result = array( 'table' => end( $nameParts ), 'tableText' => $tableText, 'columns' => null, 'columnTokens' => null,
                         'rows' => array(), 'tokens' => $tokens );
        if ( $n < $count && $tokens[$significant[$n][0]]['text'] === '(' )
        {
            $open = $significant[$n][0];
            $openDepth = $significant[$n][1];
            $columns = array();
            $n++;
            while ( $n < $count )
            {
                list( $i, $depth ) = $significant[$n];
                $token = $tokens[$i];
                if ( $token['text'] === ')' && $depth === $openDepth )
                    break;
                if ( $token['type'] === expSQLServerLexer::WORD || $token['type'] === expSQLServerLexer::IDENT )
                    $columns[] = expSQLServerLexer::identifierValue( $token );
                $n++;
            }
            if ( $n >= $count )
                return null;
            $result['columns'] = $columns;
            $result['columnTokens'] = array( $open, $significant[$n][0] );
            $n++;
        }
        if ( $n < $count && expSQLServerLexer::keyword( $tokens[$significant[$n][0]] ) === 'VALUES' )
        {
            $n++;
            while ( $n < $count && $tokens[$significant[$n][0]]['text'] === '(' )
            {
                $baseDepth = $significant[$n][1];
                $n++;
                $row = array();
                $start = null;
                $last = null;
                while ( $n < $count )
                {
                    list( $i, $depth ) = $significant[$n];
                    $text = $tokens[$i]['text'];
                    $isPunct = $tokens[$i]['type'] === expSQLServerLexer::PUNCT;
                    if ( $isPunct && $text === ',' && $depth === $baseDepth + 1 )
                    {
                        $row[] = array( $start, $last );
                        $start = null;
                        $n++;
                        continue;
                    }
                    // the closing parenthesis of the row has the depth outside it (see significant())
                    if ( $isPunct && $text === ')' && $depth === $baseDepth )
                    {
                        $row[] = array( $start, $last );
                        $n++;
                        break;
                    }
                    if ( $start === null )
                        $start = $i;
                    $last = $i;
                    $n++;
                }
                $result['rows'][] = $row;
                if ( $n < $count && $tokens[$significant[$n][0]]['text'] === ',' )
                    $n++;
                else
                    break;
            }
        }
        return $result;
    }

    /**
     * An INSERT that names the identity column of its table, rewritten for SQL Server:
     *  - every row gives it NULL or 0 (the MySQL way of asking for the next number): the column and its values are
     *    left out, and the server numbers the rows;
     *  - a row gives it a real number: array( 'identity_insert' => true ) and the statement unchanged; the caller
     *    switches IDENTITY_INSERT on for this table around the statement, and off again even when it fails.
     *
     * @param string $sql
     * @param string $identityColumn
     * @return array keys: sql, identity_insert (bool), changed (bool)
     */
    public static function rewriteIdentityInsert( $sql, $identityColumn )
    {
        $result = array( 'sql' => (string)$sql, 'identity_insert' => false, 'changed' => false );
        $parsed = self::parseInsert( $sql );
        if ( $parsed === null || $parsed['columns'] === null )
            return $result;
        $position = null;
        foreach ( $parsed['columns'] as $k => $column )
            if ( strcasecmp( $column, $identityColumn ) === 0 )
                $position = $k;
        if ( $position === null )
            return $result;
        $tokens = $parsed['tokens'];
        if ( !$parsed['rows'] )
        {
            // INSERT ... SELECT: the values cannot be seen, the caller must allow explicit ones
            $result['identity_insert'] = true;
            return $result;
        }
        $allAuto = true;
        foreach ( $parsed['rows'] as $row )
        {
            if ( count( $row ) !== count( $parsed['columns'] ) || !isset( $row[$position] ) || $row[$position][0] === null )
                return $result;
            list( $first, $last ) = $row[$position];
            $value = trim( expSQLServerLexer::join( array_slice( $tokens, $first, $last - $first + 1 ) ) );
            if ( !preg_match( '/^(NULL|0+|\'0*\'|N\'0*\'|\'\'|N\'\')$/i', $value ) )
                $allAuto = false;
        }
        if ( !$allAuto )
        {
            $result['identity_insert'] = true;
            return $result;
        }
        // Leave the column and one value per row out: remove the tokens, with the comma before or after.
        $remove = array();
        $removeColumn = function ( $first, $last ) use ( &$remove, $tokens )
        {
            // extend to the comma after; else the comma before
            $after = $last + 1;
            while ( isset( $tokens[$after] ) && expSQLServerLexer::isNoise( $tokens[$after] ) )
                $after++;
            if ( isset( $tokens[$after] ) && $tokens[$after]['text'] === ',' )
            {
                $after++;
                while ( isset( $tokens[$after] ) && $tokens[$after]['type'] === expSQLServerLexer::WS )
                    $after++;
                for ( $i = $first; $i < $after; $i++ )
                    $remove[$i] = true;
                return;
            }
            $before = $first - 1;
            while ( $before >= 0 && expSQLServerLexer::isNoise( $tokens[$before] ) )
                $before--;
            if ( $before >= 0 && $tokens[$before]['text'] === ',' )
                $first = $before;
            for ( $i = $first; $i <= $last; $i++ )
                $remove[$i] = true;
        };
        // the column name token(s) in the column list
        list( $open, $close ) = $parsed['columnTokens'];
        $index = -1;
        for ( $i = $open + 1; $i < $close; $i++ )
        {
            $token = $tokens[$i];
            if ( $token['type'] === expSQLServerLexer::WORD || $token['type'] === expSQLServerLexer::IDENT )
            {
                $index++;
                if ( $index === $position )
                {
                    $removeColumn( $i, $i );
                    break;
                }
            }
        }
        foreach ( $parsed['rows'] as $row )
            $removeColumn( $row[$position][0], $row[$position][1] );
        $out = '';
        foreach ( $tokens as $i => $token )
            if ( !isset( $remove[$i] ) )
                $out .= $token['text'];
        if ( count( $parsed['columns'] ) === 1 )
        {
            // the only column: INSERT INTO t DEFAULT VALUES (one row; several rows cannot be written so)
            if ( count( $parsed['rows'] ) !== 1 )
                return array( 'sql' => (string)$sql, 'identity_insert' => false, 'changed' => false );
            $out = 'INSERT INTO ' . $parsed['tableText'] . ' DEFAULT VALUES';
        }
        $result['sql'] = $out;
        $result['changed'] = true;
        return $result;
    }

    /**
     * MySQL's CREATE TEMPORARY TABLE name as SQL Server's CREATE TABLE #name (a temporary table of this session).
     * array( sql, table name with # ) or null when the statement is no CREATE [TEMPORARY] TABLE.
     *
     * @param string $sql
     * @return array|null
     */
    public static function temporaryTable( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( (string)$sql );
        $significant = expSQLServerLexer::significant( $tokens );
        if ( count( $significant ) < 3 || expSQLServerLexer::keyword( $tokens[$significant[0][0]] ) !== 'CREATE' )
            return null;
        $n = 1;
        $temporary = null;
        $keyword = expSQLServerLexer::keyword( $tokens[$significant[$n][0]] );
        if ( $keyword === 'TEMPORARY' || $keyword === 'TEMP' )
        {
            $temporary = $significant[$n][0];
            $n++;
        }
        if ( !isset( $significant[$n] ) || expSQLServerLexer::keyword( $tokens[$significant[$n][0]] ) !== 'TABLE' )
            return null;
        $n++;
        // MySQL: IF NOT EXISTS
        $ifNotExists = null;
        if ( isset( $significant[$n + 2] ) && expSQLServerLexer::keyword( $tokens[$significant[$n][0]] ) === 'IF'
             && expSQLServerLexer::keyword( $tokens[$significant[$n + 1][0]] ) === 'NOT'
             && expSQLServerLexer::keyword( $tokens[$significant[$n + 2][0]] ) === 'EXISTS' )
        {
            $ifNotExists = array( $significant[$n][0], $significant[$n + 2][0] );
            $n += 3;
        }
        if ( !isset( $significant[$n] ) )
            return null;
        $nameIndex = $significant[$n][0];
        $name = expSQLServerLexer::identifierValue( $tokens[$nameIndex] );
        if ( $name === '' )
            return null;
        if ( $temporary === null && $name[0] !== '#' )
            return null;
        if ( $name[0] !== '#' )
            $name = '#' . $name;
        self::checkIdentifier( $name );
        $out = '';
        foreach ( $tokens as $i => $token )
        {
            if ( $i === $temporary )
            {
                // drop "TEMPORARY" and the space after it
                continue;
            }
            if ( $temporary !== null && $i === $temporary + 1 && $token['type'] === expSQLServerLexer::WS )
                continue;
            if ( $ifNotExists !== null && $i >= $ifNotExists[0] && $i <= $ifNotExists[1] + 1 )
                continue;
            $out .= $i === $nameIndex ? $name : $token['text'];
        }
        return array( $out, $name );
    }

    /**
     * DROP TABLE #name that never fails (the table may already be gone with its session).
     *
     * @param string $name with or without #
     * @return string
     */
    public static function dropTemporaryTable( $name )
    {
        $name = (string)$name;
        if ( $name === '' || $name[0] !== '#' )
            $name = '#' . $name;
        self::checkIdentifier( $name );
        return "IF OBJECT_ID(" . self::stringLiteral( 'tempdb..' . $name ) . ") IS NOT NULL DROP TABLE " . $name;
    }

    // ------------------------------------------------------------------ helpers the kernel can use on every engine

    /**
     * An insert-or-update of one row, atomic under concurrency: MERGE ... WITH (HOLDLOCK) (a plain MERGE checks and
     * inserts under different locks, and two sessions can both insert). Without $updateColumns (or with an empty
     * list) a row that exists is left as it is. MERGE must end with ";".
     *
     * @param string $table
     * @param array $row column => value (PHP values, written with literal())
     * @param string[] $keyColumns the columns that find the row
     * @param string[]|null $updateColumns the columns to update when the row exists; null: every column but the keys
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
        $source = array();
        foreach ( $row as $column => $value )
            $source[] = self::literal( $value ) . ' AS ' . self::quoteIdentifier( $column );
        $on = array();
        foreach ( $keyColumns as $key )
            $on[] = 't.' . self::quoteIdentifier( $key ) . ' = s.' . self::quoteIdentifier( $key );
        $sql = 'MERGE INTO ' . self::quoteName( $table ) . ' WITH (HOLDLOCK) AS t'
             . ' USING (SELECT ' . implode( ', ', $source ) . ') AS s'
             . ' ON (' . implode( ' AND ', $on ) . ')';
        if ( $updateColumns )
        {
            $set = array();
            foreach ( $updateColumns as $column )
                $set[] = 't.' . self::quoteIdentifier( $column ) . ' = s.' . self::quoteIdentifier( $column );
            $sql .= ' WHEN MATCHED THEN UPDATE SET ' . implode( ', ', $set );
        }
        $columns = array_map( array( __CLASS__, 'quoteIdentifier' ), array_keys( $row ) );
        $values = array();
        foreach ( array_keys( $row ) as $column )
            $values[] = 's.' . self::quoteIdentifier( $column );
        $sql .= ' WHEN NOT MATCHED THEN INSERT (' . implode( ', ', $columns ) . ') VALUES (' . implode( ', ', $values ) . ');';
        return $sql;
    }

    /**
     * A SELECT that locks the rows it reads: WITH (UPDLOCK, ROWLOCK) after every table of its outer FROM and JOINs
     * (SQL Server knows FOR UPDATE only in cursors). A table that has hints of its own keeps them; a trailing
     * FOR UPDATE is removed.
     *
     * @param string $sql
     * @return string
     */
    public static function forUpdate( $sql )
    {
        $sql = self::withoutTrailingForUpdate( rtrim( (string)$sql, " \t\r\n;" ) );
        $tokens = expSQLServerLexer::tokenize( $sql );
        $significant = expSQLServerLexer::significant( $tokens );
        $count = count( $significant );
        $insert = array();
        $inFrom = false;
        $stop = array( 'WHERE', 'GROUP', 'ORDER', 'HAVING', 'JOIN', 'INNER', 'LEFT', 'RIGHT', 'FULL', 'CROSS', 'ON', 'UNION',
                       'EXCEPT', 'INTERSECT', 'OPTION', 'FOR', 'OUTER', 'APPLY', 'OFFSET' );
        for ( $n = 0; $n < $count; $n++ )
        {
            list( $i, $depth ) = $significant[$n];
            $word = expSQLServerLexer::keyword( $tokens[$i] );
            if ( $depth !== 0 || !( $word === 'FROM' || $word === 'JOIN' || ( $tokens[$i]['text'] === ',' && $inFrom ) ) )
            {
                if ( $depth === 0 && in_array( $word, array( 'WHERE', 'GROUP', 'ORDER', 'HAVING', 'UNION' ), true ) )
                    $inFrom = false;
                continue;
            }
            if ( $word === 'FROM' )
                $inFrom = true;
            // the table: name parts joined by dots
            $k = $n + 1;
            if ( !isset( $significant[$k] ) || $tokens[$significant[$k][0]]['text'] === '(' )
                continue;
            $last = $significant[$k][0];
            while ( isset( $significant[$k + 2] ) && $tokens[$significant[$k + 1][0]]['text'] === '.' )
            {
                $k += 2;
                $last = $significant[$k][0];
            }
            // an alias, with or without AS
            if ( isset( $significant[$k + 1] ) && expSQLServerLexer::keyword( $tokens[$significant[$k + 1][0]] ) === 'AS' && isset( $significant[$k + 2] ) )
            {
                $k += 2;
                $last = $significant[$k][0];
            }
            else if ( isset( $significant[$k + 1] ) && in_array( $tokens[$significant[$k + 1][0]]['type'], array( expSQLServerLexer::WORD, expSQLServerLexer::IDENT ), true )
                      && !in_array( expSQLServerLexer::keyword( $tokens[$significant[$k + 1][0]] ), array_merge( $stop, array( 'WITH' ) ), true ) )
            {
                $k += 1;
                $last = $significant[$k][0];
            }
            if ( isset( $significant[$k + 1] ) && expSQLServerLexer::keyword( $tokens[$significant[$k + 1][0]] ) === 'WITH' )
                continue;
            $insert[$last] = true;
        }
        $out = '';
        foreach ( $tokens as $i => $token )
            $out .= $token['text'] . ( isset( $insert[$i] ) ? ' WITH (UPDLOCK, ROWLOCK)' : '' );
        return $out;
    }

    /**
     * The statement without a trailing outer FOR UPDATE (as MySQL, PostgreSQL and Oracle write a row lock).
     *
     * @param string $sql
     * @return string
     */
    public static function withoutTrailingForUpdate( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( (string)$sql );
        $significant = expSQLServerLexer::significant( $tokens );
        $c = count( $significant );
        if ( $c >= 2 && $significant[$c - 1][1] === 0 && expSQLServerLexer::keyword( $tokens[$significant[$c - 1][0]] ) === 'UPDATE'
             && expSQLServerLexer::keyword( $tokens[$significant[$c - 2][0]] ) === 'FOR' )
            return rtrim( expSQLServerLexer::join( array_slice( $tokens, 0, $significant[$c - 2][0] ) ) );
        return (string)$sql;
    }

    /**
     * Whether a SELECT ends with an outer FOR UPDATE.
     *
     * @param string $sql
     * @return bool
     */
    public static function hasTrailingForUpdate( $sql )
    {
        return stripos( (string)$sql, 'update' ) !== false && self::withoutTrailingForUpdate( $sql ) !== (string)$sql;
    }

    /**
     * GROUP_CONCAT in SQL Server's words: STRING_AGG (SQL Server 2017 and later) over NVARCHAR(MAX), so a result
     * past 8,000 bytes is no error (9829); optionally in an order.
     *
     * @param string $expression
     * @param string $separator
     * @param string|null $orderBy e.g. "name ASC"
     * @return string
     */
    public static function groupConcat( $expression, $separator = ',', $orderBy = null )
    {
        // the separator must be a string literal (error 8733 for an expression or a bound value): parameterize() keeps it
        $sql = 'STRING_AGG(CAST(' . $expression . ' AS NVARCHAR(MAX)), ' . self::stringLiteral( $separator ) . ')';
        if ( $orderBy !== null && trim( (string)$orderBy ) !== '' )
            $sql .= ' WITHIN GROUP (ORDER BY ' . $orderBy . ')';
        return $sql;
    }

    /**
     * A text constant no binding touches: CAST(0x<UTF-16LE bytes> AS NVARCHAR(n)).
     *
     * @param string $text UTF-8
     * @return string
     */
    public static function nvarcharConstant( $text )
    {
        $text = str_replace( "\0", '', (string)$text );
        $utf16 = function_exists( 'mb_convert_encoding' ) ? mb_convert_encoding( $text, 'UTF-16LE', 'UTF-8' ) : iconv( 'UTF-8', 'UTF-16LE', $text );
        $length = max( 1, intdiv( strlen( $utf16 ), 2 ) );
        return 'CAST(' . self::binaryLiteral( $utf16 ) . ' AS NVARCHAR(' . ( $length > self::MAX_NVARCHAR_LENGTH ? 'MAX' : $length ) . '))';
    }

    /**
     * Seconds since 1970-01-01 00:00:00 UTC: of now (no argument), or of a DATETIME2/DATETIME expression taken as UTC.
     * DATEDIFF_BIG, so dates after 2038 do not overflow. The epoch is no string literal: bound as a parameter, ODBC
     * types it through SQLDescribeParam and fails to convert '19700101' (22018, measured with PDO_ODBC).
     *
     * @param string|null $expression
     * @return string
     */
    public static function unixTimestamp( $expression = null )
    {
        $value = $expression === null ? 'SYSUTCDATETIME()' : $expression;
        return "DATEDIFF_BIG(SECOND, DATETIME2FROMPARTS(1970, 1, 1, 0, 0, 0, 0, 0), $value)";
    }

    /**
     * A DATETIME2 (UTC) from seconds since 1970. DATEADD takes an INT, so a value past 2038-01-19 03:14:07 is added
     * as days and the seconds left over.
     *
     * @param string $expression
     * @return string
     */
    public static function fromUnixTimestamp( $expression )
    {
        return "DATEADD(SECOND, CAST(($expression) % 86400 AS INT), DATEADD(DAY, CAST(($expression) / 86400 AS INT), DATETIME2FROMPARTS(1970, 1, 1, 0, 0, 0, 0, 0)))";
    }

    /**
     * The number of characters of a text, trailing spaces included (LEN() leaves them out).
     *
     * @param string $expression
     * @return string
     */
    public static function charLength( $expression )
    {
        return "(LEN(CONCAT($expression, N'x')) - 1)";
    }

    /**
     * SUBSTRING with an optional length: SQL Server needs the length, and a fixed 8,000 would cut longer texts.
     *
     * @param string $string
     * @param string|int $from 1-based
     * @param string|int|null $length
     * @return string
     */
    public static function subString( $string, $from, $length = null )
    {
        if ( $length === null || $length === '' || $length === false )
            return "SUBSTRING($string, $from, 2147483647)";
        return "SUBSTRING($string, $from, $length)";
    }

    /**
     * Concatenation: CONCAT(), which takes numbers as well as text (the + operator would add them) and reads NULL as
     * '' (MySQL's CONCAT() returns NULL when one argument is NULL).
     *
     * @param string[] $strings
     * @return string
     */
    public static function concatString( array $strings )
    {
        $strings = array_values( $strings );
        if ( !$strings )
            return "N''";
        if ( count( $strings ) === 1 )
            return 'CONCAT(' . $strings[0] . ", N'')";
        return 'CONCAT(' . implode( ', ', $strings ) . ')';
    }

    /**
     * MD5 as 32 lower-case hex digits of the text's UTF-8 bytes, as PHP's md5() gives it. On SQL Server 2019 and later
     * the text is turned into UTF-8 through a UTF-8 collation; on 2017 HASHBYTES sees the text as it is stored (UTF-16
     * for NVARCHAR), which matches PHP only for ASCII text.
     *
     * @param string $expression
     * @param int $majorVersion of the server
     * @return string
     */
    public static function md5( $expression, $majorVersion = 15 )
    {
        if ( $majorVersion >= 15 )
            $bytes = "CAST(CAST($expression AS NVARCHAR(MAX)) COLLATE Latin1_General_100_BIN2_UTF8 AS VARCHAR(MAX))";
        else
            $bytes = "CAST($expression AS VARCHAR(MAX))";
        return "LOWER(CONVERT(VARCHAR(32), HASHBYTES('MD5', $bytes), 2))";
    }

    /**
     * @param string $a
     * @param string $b
     * @return string
     */
    public static function bitAnd( $a, $b )
    {
        return "($a & $b)";
    }

    /**
     * @param string $a
     * @param string $b
     * @return string
     */
    public static function bitOr( $a, $b )
    {
        return "($a | $b)";
    }

    /**
     * column IN (...) for a list of any size: lists of at most $chunk values joined with OR (NOT IN: AND), so no
     * statement grows past what the optimizer takes in one list (error 8623 on very long lists). An empty list is
     * a condition that is never true (IN) or always true (NOT IN) instead of invalid SQL.
     *
     * @param array $elements
     * @param string $column
     * @param bool $not
     * @param string|false $type int, float, string, or false for the values as they are
     * @param int $chunk
     * @return string
     */
    public static function inList( array $elements, $column, $not = false, $type = false, $chunk = 1000 )
    {
        $chunk = max( 1, (int)$chunk );
        $values = array();
        foreach ( $elements as $element )
        {
            if ( $type === 'int' || $type === 'integer' )
                $values[] = (string)(int)$element;
            else if ( $type === 'float' || $type === 'double' )
                $values[] = self::literal( (float)$element );
            else if ( $type === 'string' )
                $values[] = self::stringLiteral( (string)$element );
            else
                $values[] = (string)$element;
        }
        if ( !$values )
            return $not ? '(1=1)' : '(1=0)';
        $parts = array();
        foreach ( array_chunk( $values, $chunk ) as $list )
            $parts[] = $column . ( $not ? ' NOT IN ( ' : ' IN ( ' ) . implode( ', ', $list ) . ' )';
        if ( count( $parts ) === 1 )
            return $parts[0];
        return '( ' . implode( $not ? ' AND ' : ' OR ', $parts ) . ' )';
    }

    // ------------------------------------------------------------------ connection

    /**
     * The statements every new connection runs first, so each connection behaves the same whatever the server's or
     * the client library's defaults are (ODBC, FreeTDS and sqlsrv differ):
     *  - NOCOUNT ON: no "rows affected" messages between result sets;
     *  - XACT_ABORT ON: an error rolls the whole transaction back, never half of it;
     *  - the ANSI options an indexed view or computed column index needs;
     *  - DATEFORMAT ymd: '2026-11-24' reads the same in every login language;
     *  - LOCK_TIMEOUT: milliseconds to wait for a lock (-1: wait, the server's default) - error 1222 past it;
     *  - READ COMMITTED (with READ_COMMITTED_SNAPSHOT on, readers never wait for writers).
     *
     * @param int $lockTimeoutMs
     * @return string
     */
    public static function sessionSetup( $lockTimeoutMs = -1 )
    {
        $lockTimeoutMs = (int)$lockTimeoutMs < 0 ? -1 : (int)$lockTimeoutMs;
        return 'SET NOCOUNT ON; SET XACT_ABORT ON; SET ANSI_NULLS ON; SET ANSI_PADDING ON; SET ANSI_WARNINGS ON; '
             . 'SET ARITHABORT ON; SET CONCAT_NULL_YIELDS_NULL ON; SET QUOTED_IDENTIFIER ON; SET NUMERIC_ROUNDABORT OFF; '
             . 'SET IMPLICIT_TRANSACTIONS OFF; SET DATEFORMAT ymd; SET DATEFIRST 7; SET TEXTSIZE 2147483647; '
             . "SET LOCK_TIMEOUT $lockTimeoutMs; SET TRANSACTION ISOLATION LEVEL READ COMMITTED";
    }

    /**
     * The server part of a connection: "tcp:host,port", "host\instance" (named instance, port from the SQL Server
     * Browser unless one is given), "(localdb)\name" (Windows LocalDB, no port) or an IPv6 address in brackets.
     *
     * @param string $host
     * @param int|string|null $port
     * @return string
     */
    public static function serverAddress( $host, $port = null )
    {
        $host = trim( (string)$host );
        if ( $host === '' )
            $host = 'localhost';
        if ( stripos( $host, '(localdb)' ) === 0 || stripos( $host, 'np:' ) === 0 || stripos( $host, 'lpc:' ) === 0 )
            return $host;
        if ( stripos( $host, 'tcp:' ) === 0 )
            $host = substr( $host, 4 );
        if ( strpos( $host, ',' ) !== false )
            return 'tcp:' . $host;
        if ( substr_count( $host, ':' ) > 1 && $host[0] !== '[' )
            $host = '[' . $host . ']';
        $port = ( $port === null || $port === '' || $port === false || (int)$port === 0 ) ? null : (int)$port;
        if ( strpos( $host, '\\' ) !== false )
            return $port === null ? $host : 'tcp:' . $host . ',' . $port;
        return 'tcp:' . $host . ',' . ( $port === null ? 1433 : $port );
    }

    /**
     * A value for a connection string ({...} when it holds ; } { = or spaces at either end; } doubled inside).
     *
     * @param string $value
     * @return string
     */
    public static function connectionValue( $value )
    {
        $value = (string)$value;
        if ( $value === '' || preg_match( '/[;{}=]|^\s|\s$/', $value ) )
            return '{' . str_replace( '}', '}}', $value ) . '}';
        return $value;
    }

    /**
     * The keywords of a connection to SQL Server, from the driver's settings (see expSQLServerDB::settings()):
     * Encrypt (yes, no, strict - TDS 8.0, SQL Server 2022 and later; ODBC Driver 18 encrypts by default),
     * TrustServerCertificate, MultiSubnetFailover (an Always On listener over several subnets), ApplicationIntent
     * (ReadOnly for a readable secondary through read-only routing), LoginTimeout, ConnectRetryCount (ODBC's idle
     * connection resiliency, which reconnects behind the driver's back and so is off by default: expSQLServerDB opens a
     * lost connection again itself, knowing about #temp tables and transactions), APP (the program name the server
     * shows), and the database.
     *
     * @param array $config keys: database, encrypt, trust_server_certificate, multi_subnet_failover, application_intent, login_timeout, app_name
     * @return array keyword => value as strings, in ODBC spelling
     */
    public static function connectionKeywords( array $config )
    {
        $keywords = array();
        if ( isset( $config['database'] ) && (string)$config['database'] !== '' )
            $keywords['Database'] = (string)$config['database'];
        $encrypt = strtolower( (string)( isset( $config['encrypt'] ) ? $config['encrypt'] : 'yes' ) );
        $keywords['Encrypt'] = in_array( $encrypt, array( 'no', 'false', '0', 'optional', 'disabled' ), true ) ? 'no'
                             : ( $encrypt === 'strict' ? 'strict' : 'yes' );
        $keywords['TrustServerCertificate'] = self::flag( isset( $config['trust_server_certificate'] ) ? $config['trust_server_certificate'] : false ) ? 'yes' : 'no';
        if ( self::flag( isset( $config['multi_subnet_failover'] ) ? $config['multi_subnet_failover'] : false ) )
            $keywords['MultiSubnetFailover'] = 'yes';
        $intent = isset( $config['application_intent'] ) ? strtolower( (string)$config['application_intent'] ) : '';
        if ( $intent === 'readonly' )
            $keywords['ApplicationIntent'] = 'ReadOnly';
        if ( isset( $config['login_timeout'] ) && (int)$config['login_timeout'] > 0 )
            $keywords['LoginTimeout'] = (string)(int)$config['login_timeout'];
        if ( isset( $config['connect_retry_count'] ) && $config['connect_retry_count'] !== null && $config['connect_retry_count'] !== '' )
            $keywords['ConnectRetryCount'] = (string)max( 0, min( 255, (int)$config['connect_retry_count'] ) );
        if ( isset( $config['app_name'] ) && (string)$config['app_name'] !== '' )
            $keywords['APP'] = (string)$config['app_name'];
        return $keywords;
    }

    /**
     * A PDO DSN for pdo_sqlsrv. Its flags take 1/0 (true/false): "yes" is read as false by pdo_sqlsrv 5.11. MARS is
     * switched off: pdo_sqlsrv has it on by default, and under MARS a transaction begun in a batch of its own
     * (BEGIN TRANSACTION) is rolled back at the end of that batch.
     *
     * @param array $config the connectionKeywords() config plus host and port
     * @return string
     */
    public static function pdoSqlsrvDSN( array $config )
    {
        $parts = array( 'Server=' . self::serverAddress( $config['host'], isset( $config['port'] ) ? $config['port'] : null ) );
        foreach ( self::connectionKeywords( $config ) as $keyword => $value )
        {
            if ( $value === 'yes' || $value === 'no' )
                $value = $value === 'yes' ? '1' : '0';
            $parts[] = $keyword . '=' . self::connectionValue( $value );
        }
        $parts[] = 'MultipleActiveResultSets=0';
        $parts[] = 'ConnectionPooling=' . ( self::connectionPooling( $config ) ? '1' : '0' );
        return 'sqlsrv:' . implode( ';', $parts );
    }

    /**
     * Whether sqlsrv and pdo_sqlsrv may pool connections ('connection_pooling', off by default). Both pool by default:
     * a closed connection stays open for the next connect with the same options. A process that forks after closing
     * one (Velocity's parent after its warm-up, a cluster script with workers) would hand that one socket to every
     * child, and the first child to finish breaks it for all the others (TCP Provider: Error code 0x20 / 0x2746).
     * PDO_ODBC pools only when unixODBC's odbcinst.ini says Pooling=Yes, outside of this.
     *
     * @param array $config
     * @return bool
     */
    public static function connectionPooling( array $config )
    {
        return self::flag( isset( $config['connection_pooling'] ) ? $config['connection_pooling'] : false );
    }

    /**
     * A PDO DSN for PDO_ODBC with Microsoft's ODBC driver (or another SQL Server ODBC driver by name).
     *
     * @param array $config
     * @param string $driver e.g. ODBC Driver 18 for SQL Server
     * @return string
     */
    public static function odbcDSN( array $config, $driver = 'ODBC Driver 18 for SQL Server' )
    {
        $parts = array( 'Driver={' . str_replace( '}', '}}', (string)$driver ) . '}',
                        'Server=' . self::connectionValue( self::serverAddress( $config['host'], isset( $config['port'] ) ? $config['port'] : null ) ) );
        foreach ( self::connectionKeywords( $config ) as $keyword => $value )
            $parts[] = $keyword . '=' . self::connectionValue( $value );
        return 'odbc:' . implode( ';', $parts );
    }

    /**
     * A PDO DSN for pdo_dblib (FreeTDS), TDS 7.4, UTF-8. FreeTDS has no MultiSubnetFailover and no ApplicationIntent;
     * encryption follows freetds.conf.
     *
     * @param array $config
     * @return string
     */
    public static function dblibDSN( array $config )
    {
        $host = trim( (string)$config['host'] );
        $port = isset( $config['port'] ) && (int)$config['port'] > 0 ? (int)$config['port'] : 1433;
        $dsn = 'dblib:host=' . $host . ( strpos( $host, '\\' ) === false ? ':' . $port : '' ) . ';version=7.4;charset=UTF-8';
        if ( isset( $config['database'] ) && (string)$config['database'] !== '' )
            $dsn .= ';dbname=' . $config['database'];
        if ( isset( $config['app_name'] ) && (string)$config['app_name'] !== '' )
            $dsn .= ';appname=' . $config['app_name'];
        return $dsn;
    }

    /**
     * The connection options of sqlsrv_connect() (the server goes separately: serverAddress()).
     *
     * @param array $config the connectionKeywords() config plus user and password
     * @return array
     */
    public static function sqlsrvOptions( array $config )
    {
        $options = array( 'CharacterSet' => 'UTF-8', 'ReturnDatesAsStrings' => true, 'MultipleActiveResultSets' => false,
                          'ConnectionPooling' => self::connectionPooling( $config ) );
        foreach ( self::connectionKeywords( $config ) as $keyword => $value )
        {
            if ( $keyword === 'Encrypt' )
                $options['Encrypt'] = $value === 'strict' ? 'strict' : ( $value === 'yes' ? 1 : 0 );
            else if ( $keyword === 'TrustServerCertificate' || $keyword === 'MultiSubnetFailover' )
                $options[$keyword] = $value === 'yes' ? 1 : 0;
            else if ( $keyword === 'LoginTimeout' || $keyword === 'ConnectRetryCount' )
                $options[$keyword] = (int)$value;
            else
                $options[$keyword] = $value;
        }
        if ( isset( $config['user'] ) && (string)$config['user'] !== '' )
        {
            $options['UID'] = (string)$config['user'];
            $options['PWD'] = isset( $config['password'] ) ? (string)$config['password'] : '';
        }
        return $options;
    }

    // ------------------------------------------------------------------ versions and replicas

    /**
     * The parts of a product version ("16.0.4325.4") and what it is.
     *
     * @param string $productVersion SERVERPROPERTY('ProductVersion')
     * @param int|null $engineEdition SERVERPROPERTY('EngineEdition'): 5 Azure SQL Database, 8 Managed Instance, 9 Azure SQL Edge
     * @return array keys: string, values (int[]), major, name (SQL Server 2022, Azure SQL Database ...), supported (bool), note
     */
    public static function describeVersion( $productVersion, $engineEdition = null )
    {
        $productVersion = trim( (string)$productVersion );
        $values = array_map( 'intval', explode( '.', $productVersion ) );
        $major = $values[0];
        $names = array( 11 => 'SQL Server 2012', 12 => 'SQL Server 2014', 13 => 'SQL Server 2016', 14 => 'SQL Server 2017',
                        15 => 'SQL Server 2019', 16 => 'SQL Server 2022', 17 => 'SQL Server 2025' );
        $name = isset( $names[$major] ) ? $names[$major] : ( $major > 17 ? "SQL Server (version $major)" : "SQL Server $productVersion" );
        $supported = $major >= self::MIN_MAJOR_VERSION;
        $note = $supported ? '' : 'SQL Server 2017 or later is needed (STRING_AGG, TRIM, CONCAT_WS)';
        $edition = (int)$engineEdition;
        if ( $edition === 5 )
        {
            $name = 'Azure SQL Database';
            $supported = true;
            $note = '';
        }
        else if ( $edition === 8 )
        {
            $name = 'Azure SQL Managed Instance';
            $supported = true;
            $note = '';
        }
        else if ( $edition === 9 )
        {
            $name = 'Azure SQL Edge';
            $supported = false;
            $note = 'Azure SQL Edge was retired on 30 September 2025; use SQL Server 2022 or later on Linux';
        }
        return array( 'string' => $productVersion, 'values' => $values, 'major' => $major, 'name' => $name,
                      'supported' => $supported, 'note' => $note );
    }

    /**
     * Whether a read replica may be read: it answers as a readable secondary (Updateability READ_ONLY) or is a
     * primary itself (READ_WRITE: the listener sent the connection to the primary - reading there is correct), and
     * when a lag limit is set, its lag (seconds behind the primary, when the server tells) is within the limit.
     *
     * @param string|null $updateability DATABASEPROPERTYEX(DB_NAME(), 'Updateability')
     * @param int|float|null $lagSeconds null when unknown
     * @param int|null $maxLag seconds, null or 0 for no limit
     * @return bool
     */
    public static function replicaUsable( $updateability, $lagSeconds = null, $maxLag = null )
    {
        $updateability = strtoupper( trim( (string)$updateability ) );
        if ( $updateability !== 'READ_ONLY' && $updateability !== 'READ_WRITE' )
            return false;
        if ( $maxLag !== null && (int)$maxLag > 0 && $lagSeconds !== null && (float)$lagSeconds > (int)$maxLag )
            return false;
        return true;
    }

    /**
     * Whether a setting value means yes.
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
