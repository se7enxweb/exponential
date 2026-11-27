<?php
/**
 * File containing the expInformixDialect class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The Informix SQL that expInformixDB writes, as pure functions of their arguments; the rules it shares with Db2
 * come from expDb2Dialect (the lexer, the bit operators, LIKE's ESCAPE, CONCAT() of more than two, TRUNCATE as
 * DELETE). Supported servers: IBM Informix 14.10 and 15.
 *
 * What differs from Db2:
 *
 *  - Names: Informix stores an unquoted name in lower case, and reads "x" as a string unless the client sets
 *    DELIMIDENT; so a MySQL `name` becomes name, unquoted, and no name is ever quoted. The words the kernel uses
 *    as names (value, count, time, position, type ...) are names to Informix where they stand.
 *  - Paging: Informix has no LIMIT or OFFSET at the end of a SELECT: LIMIT n [OFFSET m] and LIMIT m, n become
 *    SELECT SKIP m FIRST n ... right after the SELECT keyword.
 *  - Text: LENGTH() is CHAR_LENGTH() (LENGTH() counts bytes), SUBSTRING(s, f, n) is SUBSTRING(s FROM f FOR n),
 *    MD5() of a string literal is computed by PHP (Informix has no MD5; the URL aliases only ever ask for the MD5
 *    of a literal); MD5() of anything else is refused.
 *  - SELECT ... FOR UPDATE is Informix's own and stays as it is; a SELECT without FROM is too (Informix 14.10).
 *  - LIKE on a column that is a CLOB in the schema compares CAST(column AS LVARCHAR(32739)): Informix has no LIKE
 *    for a CLOB.
 *  - CREATE TEMPORARY TABLE is CREATE TEMP TABLE ... WITH NO LOG: Informix's temporary tables belong to the session
 *    as MySQL's do.
 *  - GROUP_CONCAT: Informix has no LISTAGG (expDBUnsupportedException).
 *  - INSERT INTO t (id) VALUES (NULL), the kernel's way to draw the next number of a counter table, inserts 0:
 *    a SERIAL refuses NULL and numbers 0.
 */
class expInformixDialect extends expDb2Dialect
{
    /** Longest name Informix accepts, in bytes */
    const MAX_IDENTIFIER_LENGTH = 128;

    /** Lowest supported version */
    const MIN_VERSION = '14.10';

    /** The DRDA listener's port in IBM's container (drsoctcp) */
    const DEFAULT_PORT = 9089;

    /** A one-row table (the catalogue's own entry) */
    const ONE_ROW_TABLE = 'systables WHERE tabid = 1';

    /** Informix keeps FOR UPDATE: nothing to replace it with */
    const LOCK_CLAUSE = 'FOR UPDATE';

    /**
     * The words Informix refuses as plain names in the places the kernel puts names. Informix takes nearly every
     * keyword as a name; these are the ones that start or end a clause.
     *
     * @var string[]
     */
    const RESERVED_WORDS = array(
        'ALL', 'AND', 'ANY', 'AS', 'ASC', 'BETWEEN', 'BY', 'CASE', 'CREATE', 'DELETE', 'DESC', 'DISTINCT', 'DROP', 'ELSE',
        'END', 'EXISTS', 'FIRST', 'FOR', 'FROM', 'GROUP', 'HAVING', 'IN', 'INSERT', 'INTO', 'IS', 'JOIN', 'LIKE', 'LIMIT',
        'MATCHES', 'NOT', 'NULL', 'ON', 'OR', 'ORDER', 'OUTER', 'SELECT', 'SET', 'SKIP', 'TABLE', 'THEN', 'UNION', 'UNIQUE',
        'UPDATE', 'VALUES', 'WHEN', 'WHERE', 'WITH',
    );

    /** No word is quoted: Informix reads "x" as a string */
    const NAME_WORDS = array();

    /** Calls rewrite() replaces as a whole */
    const REWRITTEN_CALLS = array( 'CONCAT', 'SUBSTRING', 'SUBSTR', 'MD5', 'UNIX_TIMESTAMP', 'LENGTH', 'CURDATE', 'NOW', 'IFNULL' );

    // ------------------------------------------------------------------ names

    /**
     * A name as it stands in a statement: unquoted, in lower case (Informix has no quoted names without DELIMIDENT).
     * A name that is no plain name, or a reserved word, is refused.
     *
     * @param string $name
     * @return string
     * @throws InvalidArgumentException
     */
    public static function quoteIdentifier( $name )
    {
        $name = (string)$name;
        static::checkIdentifier( $name );
        if ( !preg_match( '/^[A-Za-z_][A-Za-z0-9_$]*$/', $name ) )
            throw new InvalidArgumentException( "The name '$name' cannot stand in an Informix statement without DELIMIDENT" );
        return static::fold( $name );
    }

    public static function quoteIdentifierIfNeeded( $name )
    {
        return static::quoteIdentifier( $name );
    }

    /**
     * A name in lower case, as Informix stores an unquoted name.
     *
     * @param string $name
     * @return string
     */
    public static function fold( $name )
    {
        return strtr( (string)$name, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz' );
    }

    protected static function backtickName( $name )
    {
        return static::fold( $name );
    }

    // ------------------------------------------------------------------ statements

    public static function rewrite( $sql )
    {
        $sql = trim( (string)$sql );
        while ( substr( $sql, -1 ) === ';' )
            $sql = rtrim( substr( $sql, 0, -1 ) );
        // CREATE TEMPORARY TABLE as Informix's own
        $temporary = static::temporaryTable( $sql );
        if ( $temporary !== null )
            return $temporary['sql'];
        // a counter table's next number (the kernel's INSERT INTO ezurlalias_ml_incr(id) VALUES(NULL)): a SERIAL
        // takes 0 for the next number and refuses NULL
        if ( preg_match( '/^INSERT\s+INTO\s+([`"]?\w+[`"]?)\s*\(\s*([`"]?\w+[`"]?)\s*\)\s*VALUES\s*\(\s*NULL\s*\)$/i', $sql, $m ) )
            return 'INSERT INTO ' . static::fold( trim( $m[1], '`"' ) ) . ' (' . static::fold( trim( $m[2], '`"' ) ) . ') VALUES (0)';
        return static::castLargeTextForLike( parent::rewrite( $sql ) );
    }

    /**
     * Informix has no LIKE for a CLOB: a column that is a CLOB in the schema (by its name, the columns of
     * expDb2Schema::LARGE_TEXT_COLUMNS) is compared as CAST(column AS LVARCHAR(32739)) before LIKE and NOT LIKE, which
     * matches within its first 32,739 bytes. The cast is harmless on a column of that name that is no CLOB.
     *
     * @param string $sql
     * @return string
     */
    public static function castLargeTextForLike( $sql )
    {
        if ( stripos( $sql, 'LIKE' ) === false )
            return $sql;
        $names = array();
        foreach ( expDb2Schema::LARGE_TEXT_COLUMNS as $column )
        {
            $parts = explode( '.', $column );
            $names[end( $parts )] = true;
        }
        $tokens = expSQLServerLexer::tokenize( $sql );
        $significant = expSQLServerLexer::significant( $tokens );
        $wrap = array();
        foreach ( $significant as $n => $entry )
        {
            if ( expSQLServerLexer::keyword( $tokens[$entry[0]] ) !== 'LIKE' )
                continue;
            $k = $n - 1;
            if ( $k >= 0 && expSQLServerLexer::keyword( $tokens[$significant[$k][0]] ) === 'NOT' )
                $k--;
            if ( $k < 0 )
                continue;
            $last = $tokens[$significant[$k][0]];
            if ( !in_array( $last['type'], array( expSQLServerLexer::WORD, expSQLServerLexer::IDENT ), true )
                 || !isset( $names[strtolower( expSQLServerLexer::identifierValue( $last ) )] ) )
                continue;
            $first = $k;
            while ( $first >= 2 && $tokens[$significant[$first - 1][0]]['text'] === '.' )
                $first -= 2;
            $wrap[$significant[$first][0]] = $significant[$k][0];
        }
        if ( !$wrap )
            return $sql;
        $out = '';
        $count = count( $tokens );
        for ( $i = 0; $i < $count; $i++ )
        {
            if ( isset( $wrap[$i] ) )
            {
                $out .= 'CAST(' . expSQLServerLexer::join( array_slice( $tokens, $i, $wrap[$i] - $i + 1 ) ) . ' AS LVARCHAR(32739))';
                $i = $wrap[$i];
                continue;
            }
            $out .= $tokens[$i]['text'];
        }
        return $out;
    }

    /**
     * A trailing LIMIT clause as SKIP m FIRST n after the SELECT keyword.
     *
     * @param string $sql
     * @return string
     */
    public static function pagingClause( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( $sql );
        $outer = static::outerWords( $tokens );
        $forUpdate = static::trailingForUpdate( $tokens );
        $end = $forUpdate === null ? count( $tokens ) : $forUpdate;
        $limitAt = null;
        foreach ( $outer as $n => $entry )
            if ( $entry[1] === 'LIMIT' && $entry[0] < $end )
                $limitAt = $n;
        if ( $limitAt === null )
            return $sql;
        $parts = array();
        for ( $n = $limitAt + 1; isset( $outer[$n] ) && $outer[$n][0] < $end; $n++ )
            $parts[] = $outer[$n];
        if ( count( $parts ) === 1 && ctype_digit( $parts[0][2] ) )
        {
            $limit = (int)$parts[0][2];
            $offset = 0;
        }
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
            return $sql;
        $head = array_slice( $tokens, 0, $outer[$limitAt][0] );
        $tail = $forUpdate === null ? '' : ' ' . trim( expSQLServerLexer::join( array_slice( $tokens, $forUpdate ) ) );
        return static::pagedBody( $head, $limit, $offset ) . $tail;
    }

    /**
     * The statement of $tokens with SKIP and FIRST after its SELECT keyword; a statement that pages already, or one
     * that starts with WITH or has a UNION, is wrapped.
     *
     * @param array[] $tokens
     * @param int|false $limit
     * @param int $offset
     * @return string
     */
    protected static function pagedBody( array $tokens, $limit, $offset )
    {
        $paging = ( $offset > 0 ? "SKIP $offset " : '' ) . ( $limit !== false ? "FIRST $limit " : '' );
        $outer = static::outerWords( $tokens );
        $wrap = !$outer || $outer[0][1] !== 'SELECT';
        foreach ( $outer as $entry )
            if ( in_array( $entry[1], array( 'SKIP', 'FIRST', 'LIMIT', 'UNION', 'INTERSECT', 'EXCEPT', 'MINUS' ), true ) )
                $wrap = true;
        $body = rtrim( expSQLServerLexer::join( $tokens ) );
        if ( $wrap )
            return "SELECT {$paging}* FROM (\n$body\n) exp_page";
        $at = $outer[0][0];
        return rtrim( expSQLServerLexer::join( array_slice( $tokens, 0, $at + 1 ) ) ) . ' ' . $paging
             . trim( expSQLServerLexer::join( array_slice( $tokens, $at + 1 ) ) );
    }

    /**
     * Informix's FOR UPDATE locks the rows: left as it is.
     *
     * @param string $sql
     * @return string
     */
    public static function lockingForUpdate( $sql )
    {
        return $sql;
    }

    /**
     * Informix takes a SELECT without FROM: left as it is.
     *
     * @param string $sql
     * @return string
     */
    public static function addOneRowTable( $sql )
    {
        return $sql;
    }

    public static function trailingIsolation( array $tokens )
    {
        return null;
    }

    public static function forUpdate( $sql )
    {
        return rtrim( (string)$sql, " \t\r\n;" ) . ' FOR UPDATE';
    }

    /**
     * The catalogue query that finds a table, sequence, trigger, view or index by its stored name.
     */
    public static function existsQuery( $type, $name )
    {
        $literal = static::stringLiteral( static::fold( $name ) );
        switch ( $type )
        {
            case 'INDEX':
                return "SELECT 1 AS found FROM sysindexes WHERE idxname = $literal";
            case 'TRIGGER':
                return "SELECT 1 AS found FROM systriggers WHERE trigname = $literal";
            case 'SEQUENCE':
                return "SELECT 1 AS found FROM systables WHERE tabname = $literal AND tabtype = 'Q'";
            case 'VIEW':
                return "SELECT 1 AS found FROM systables WHERE tabname = $literal AND tabtype = 'V'";
        }
        return "SELECT 1 AS found FROM systables WHERE tabname = $literal AND tabtype = 'T'";
    }

    /**
     * An insert-or-update of one row as one MERGE, the source a one-row SELECT.
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
        foreach ( $row as $column => $value )
            $source[] = static::literal( $value ) . ' AS ' . $q( $column );
        $on = array();
        foreach ( $keyColumns as $key )
            $on[] = 't.' . $q( $key ) . ' = s.' . $q( $key );
        $sql = 'MERGE INTO ' . $q( $table ) . ' t USING (SELECT ' . implode( ', ', $source ) . ' FROM ' . static::ONE_ROW_TABLE
             . ') s ON (' . implode( ' AND ', $on ) . ')';
        if ( $updateColumns )
        {
            $set = array();
            foreach ( $updateColumns as $column )
                $set[] = 't.' . $q( $column ) . ' = s.' . $q( $column );
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
     * The rewrite of a call of a MySQL function Informix lacks; null when the call is left as it is.
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
            case 'NOW':
                return $text ? null : 'CURRENT YEAR TO SECOND';
            case 'CURDATE':
                return $text ? null : 'TODAY';
            case 'IFNULL':
                return count( $text ) === 2 ? 'NVL(' . $text[0] . ', ' . $text[1] . ')' : null;
            case 'LENGTH':
                return count( $text ) === 1 ? static::charLength( $text[0] ) : null;
            case 'MD5':
                if ( count( $text ) !== 1 )
                    return null;
                $literal = static::literalText( $text[0] );
                return $literal === null ? null : "'" . md5( $literal ) . "'";
        }
        return parent::rewriteCall( $word, $args );
    }

    public static function groupConcat( $expression, $separator = ',', $orderBy = null )
    {
        throw new expDBUnsupportedException( 'informix', 'GROUP_CONCAT', 'join the rows in PHP' );
    }

    /**
     * Seconds since 1970-01-01 UTC, of now or of a DATETIME expression taken as UTC.
     */
    public static function unixTimestamp( $expression = null )
    {
        if ( $expression === null )
            return "DBINFO('utc_current')";
        return "CAST(CAST(CAST(($expression) AS DATETIME YEAR TO SECOND) - CAST('1970-01-01 00:00:00' AS DATETIME YEAR TO SECOND) AS INTERVAL SECOND(10) TO SECOND) AS CHAR(20))::BIGINT";
    }

    public static function fromUnixTimestamp( $expression )
    {
        return "DBINFO('utc_to_datetime', $expression)";
    }

    public static function subString( $string, $from, $length = null )
    {
        return "SUBSTRING($string FROM $from" . ( $length !== null && $length !== '' ? " FOR $length" : '' ) . ')';
    }

    /**
     * The MD5 of a string literal, computed here; Informix has no MD5 of an expression.
     */
    public static function md5( $expression )
    {
        $literal = static::literalText( $expression );
        if ( $literal === null )
            throw new expDBUnsupportedException( 'informix', 'MD5', 'Informix has no MD5 function; compute it in PHP' );
        return "'" . md5( $literal ) . "'";
    }

    public static function charLength( $expression )
    {
        return "CHAR_LENGTH($expression)";
    }

    // ------------------------------------------------------------------ temporary tables

    /**
     * A kernel CREATE TEMPORARY TABLE as Informix's CREATE TEMP TABLE ... WITH NO LOG (the session's own table, as
     * MySQL's), or null when $sql is no such statement.
     */
    public static function temporaryTable( $sql )
    {
        $parsed = static::parseTemporaryTable( $sql );
        if ( $parsed === null )
            return null;
        return array( 'name' => static::fold( $parsed['name'] ),
                      'sql' => 'CREATE TEMP TABLE ' . static::quoteIdentifier( $parsed['name'] ) . ' ( ' . implode( ', ', $parsed['columns'] ) . ' ) WITH NO LOG' );
    }

    public static function columnConstraintsLast( $definition )
    {
        $text = parent::columnConstraintsLast( $definition );
        return str_replace( 'VARCHAR(8000)', 'LVARCHAR(8000)', $text );
    }

    // ------------------------------------------------------------------ errors

    /**
     * The kind of an error from Informix's SQLCODE (and ISAM code in the message), the SQLSTATE and the message.
     */
    public static function errorCategory( $sqlCode, $state = null, $message = '' )
    {
        $code = (int)$sqlCode;
        $state = strtoupper( (string)$state );
        $text = strtolower( (string)$message );
        if ( in_array( $code, array( -908, -930, -25580, -25582, -1803, -30081, -30080, -900 ), true ) || in_array( $state, array( '08001', '08003', '08S01' ), true )
             || strpos( $text, 'communication error' ) !== false || strpos( $text, 'the connection is closed' ) !== false )
            return 'connection';
        if ( $code === -143 || strpos( $text, 'deadlock' ) !== false || $state === '40001' )
            return 'deadlock';
        if ( in_array( $code, array( -243, -244, -245, -246, -154, -107, -113, -144 ), true ) )
            return 'lock';
        if ( in_array( $code, array( -239, -268, -100, -803 ), true ) || $state === '23505' )
            return 'duplicate';
        if ( $code === -201 || $state === '42000' )
            return 'syntax';
        if ( in_array( $code, array( -206, -217, -674 ), true ) )
            return 'unknown_object';
        return 'other';
    }

    // ------------------------------------------------------------------ connection

    public static function productName()
    {
        return 'Informix';
    }
}
