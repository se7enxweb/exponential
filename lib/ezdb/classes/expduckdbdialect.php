<?php
/**
 * File containing the expDuckDBDialect class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * What expDuckDBDB changes in a statement before DuckDB sees it, and how it reads one.
 *
 * DuckDB's SQL is PostgreSQL's in almost every respect the kernel uses (sequences and nextval(), ||, SUBSTRING ... FROM
 * ... FOR, ON CONFLICT, STRING_AGG, EXTRACT(EPOCH ...), information_schema), so the kernel's PostgreSQL schema and
 * statements run as they are. What is changed:
 *
 * - SELECT ... FOR UPDATE / FOR SHARE / LOCK IN SHARE MODE: DuckDB has no row locks (one process writes at a time,
 *   and inside it transactions are checked optimistically). The clause is left out.
 * - LOCK TABLE: nothing to lock either; the statement is not sent.
 * - MySQL words the kernel's default branches may still write: INSERT IGNORE (INSERT OR IGNORE), REPLACE INTO (INSERT
 *   OR REPLACE), `quoted` names ("quoted"), LIMIT offset, count (LIMIT count OFFSET offset).
 *
 * String literals and quoted names are never changed: the scanner skips '...' (with '' inside) and "...".
 *
 * @since 6.0.15
 * @package lib
 */
class expDuckDBDialect
{
    /** First words of statements that read only */
    const READ_WORDS = array( 'SELECT', 'SHOW', 'DESCRIBE', 'DESC', 'EXPLAIN', 'PRAGMA', 'VALUES', 'SUMMARIZE', 'FROM', 'TABLE' );

    /** First words of statements that belong to the connection, not the database (replayed on a new connection) */
    const SESSION_WORDS = array( 'SET', 'RESET' );

    /** First words of transaction control */
    const TRANSACTION_WORDS = array( 'BEGIN', 'START', 'COMMIT', 'END', 'ROLLBACK', 'ABORT' );

    /**
     * The first word of a statement, upper case ('' for none), after comments and opening parentheses.
     *
     * @param string $sql
     * @return string
     */
    public static function firstWord( $sql )
    {
        $sql = (string)$sql;
        while ( true )
        {
            $sql = ltrim( $sql, " \t\r\n(" );
            if ( strncmp( $sql, '--', 2 ) === 0 )
            {
                $end = strpos( $sql, "\n" );
                $sql = $end === false ? '' : substr( $sql, $end + 1 );
                continue;
            }
            if ( strncmp( $sql, '/*', 2 ) === 0 )
            {
                $end = strpos( $sql, '*/' );
                $sql = $end === false ? '' : substr( $sql, $end + 2 );
                continue;
            }
            break;
        }
        return preg_match( '/^([A-Za-z_]+)/', $sql, $m ) ? strtoupper( $m[1] ) : '';
    }

    /**
     * Whether a statement changes the database (or makes a temporary table, which must outlive the read-only
     * connection that would otherwise run it). A WITH counts as a write when it holds INSERT, UPDATE or DELETE.
     *
     * @param string $sql
     * @return bool
     */
    public static function isWrite( $sql )
    {
        $word = self::firstWord( $sql );
        if ( $word === '' || in_array( $word, self::READ_WORDS, true ) || in_array( $word, self::SESSION_WORDS, true )
             || in_array( $word, self::TRANSACTION_WORDS, true ) )
            return false;
        if ( $word === 'WITH' )
            return (bool)preg_match( '/\b(INSERT|UPDATE|DELETE|MERGE)\b/i', self::blankLiterals( $sql ) );
        return true;
    }

    /**
     * Whether a statement is a setting of the connection (SET, RESET).
     *
     * @param string $sql
     * @return bool
     */
    public static function isSessionStatement( $sql )
    {
        return in_array( self::firstWord( $sql ), self::SESSION_WORDS, true );
    }

    /**
     * The statement as DuckDB takes it; null when there is nothing to send (LOCK TABLE).
     *
     * @param string $sql
     * @return string|null
     */
    public static function rewrite( $sql )
    {
        $sql = (string)$sql;
        $word = self::firstWord( $sql );
        if ( $word === 'LOCK' || $word === 'UNLOCK' )
            return null;
        $blank = self::blankLiterals( $sql );
        if ( $blank === null )
            return $sql;   // a literal that does not end: DuckDB says what is wrong with it

        // `name` -> "name": the backticks are outside literals in the blanked copy, at the same offsets
        if ( strpos( $blank, '`' ) !== false )
        {
            $out = '';
            for ( $i = 0, $n = strlen( $sql ); $i < $n; $i++ )
                $out .= $blank[$i] === '`' ? '"' : $sql[$i];
            $sql = $out;
        }

        if ( $word === 'SELECT' || $word === 'WITH' )
        {
            if ( preg_match( '/\s+(?:FOR\s+(?:UPDATE|SHARE|NO\s+KEY\s+UPDATE|KEY\s+SHARE)(?:\s+OF\s+[\w\s,."]+?)?(?:\s+(?:NOWAIT|SKIP\s+LOCKED))?|LOCK\s+IN\s+SHARE\s+MODE)\s*;?\s*$/i', $blank, $m, PREG_OFFSET_CAPTURE ) )
                $sql = substr( $sql, 0, $m[0][1] );
            $blank = self::blankLiterals( $sql );
            if ( $blank !== null && preg_match( '/\bLIMIT\s+(\d+)\s*,\s*(\d+)\s*;?\s*$/i', $blank, $m, PREG_OFFSET_CAPTURE ) )
                $sql = substr( $sql, 0, $m[0][1] ) . 'LIMIT ' . $m[2][0] . ' OFFSET ' . $m[1][0];
        }
        elseif ( $word === 'INSERT' && preg_match( '/^(\s*)INSERT\s+IGNORE\s+INTO\b/i', $sql, $m ) )
        {
            $sql = $m[1] . 'INSERT OR IGNORE INTO' . substr( $sql, strlen( $m[0] ) );
        }
        elseif ( $word === 'REPLACE' && preg_match( '/^(\s*)REPLACE\s+INTO\b/i', $sql, $m ) )
        {
            $sql = $m[1] . 'INSERT OR REPLACE INTO' . substr( $sql, strlen( $m[0] ) );
        }
        return $sql;
    }

    /**
     * The statement with the inside of every string literal and quoted name replaced by spaces (same length, so
     * offsets hold), comments as they are. Null when a literal does not end.
     *
     * @param string $sql
     * @return string|null
     */
    public static function blankLiterals( $sql )
    {
        $sql = (string)$sql;
        $out = '';
        $n = strlen( $sql );
        for ( $i = 0; $i < $n; $i++ )
        {
            $c = $sql[$i];
            if ( $c !== "'" && $c !== '"' )
            {
                $out .= $c;
                continue;
            }
            $end = $i + 1;
            while ( true )
            {
                $end = strpos( $sql, $c, $end );
                if ( $end === false )
                    return null;
                if ( $end + 1 < $n && $sql[$end + 1] === $c )
                {
                    $end += 2;
                    continue;
                }
                break;
            }
            $out .= $c . str_repeat( ' ', $end - $i - 1 ) . $c;
            $i = $end;
        }
        return $out;
    }

    /**
     * A string as the inside of a literal: quotes doubled, NUL bytes left out (DuckDB's VARCHAR takes no NUL, and
     * the other drivers drop it as well). A backslash is an ordinary character in DuckDB, as in standard SQL.
     *
     * @param string|null $text
     * @return string
     */
    public static function escape( $text )
    {
        if ( $text === null )
            return '';
        return str_replace( array( "\0", "'" ), array( '', "''" ), (string)$text );
    }

    /**
     * The error number of a failed statement: the SQLSTATE PostgreSQL gives the same error, so code that tells a
     * duplicate key ('23505') or a missing table ('42P01') from the rest works unchanged. DuckDB itself names an
     * error by its class (duckdb_error_type) and its message only.
     *
     * @param int $type duckdb_error_type
     * @param string $message
     * @return string
     */
    public static function errorCode( $type, $message )
    {
        $message = (string)$message;
        if ( stripos( $message, 'Could not set lock' ) !== false || stripos( $message, 'Conflicting lock' ) !== false )
            return '55P03';
        switch ( (int)$type )
        {
            case expDuckDBFFI::ERROR_CONSTRAINT:
                if ( stripos( $message, 'NOT NULL' ) !== false )
                    return '23502';
                if ( stripos( $message, 'foreign key' ) !== false )
                    return '23503';
                if ( stripos( $message, 'CHECK constraint' ) !== false )
                    return '23514';
                return '23505';
            case expDuckDBFFI::ERROR_CATALOG:
                if ( preg_match( '/(Table|View) with name .* does not exist/i', $message ) )
                    return '42P01';
                if ( preg_match( '/with name .* already exists/i', $message ) )
                    return '42P07';
                return '42704';
            case expDuckDBFFI::ERROR_BINDER:
                return stripos( $message, 'column' ) !== false ? '42703' : '42000';
            case expDuckDBFFI::ERROR_PARSER:
            case expDuckDBFFI::ERROR_SYNTAX:
                return '42601';
            case expDuckDBFFI::ERROR_CONVERSION:
                return '22P02';
            case expDuckDBFFI::ERROR_TRANSACTION:
                return stripos( $message, 'conflict' ) !== false ? '40001' : '25P02';
            case expDuckDBFFI::ERROR_INTERRUPT:
                return '57014';
            case expDuckDBFFI::ERROR_IO:
                return '58030';
        }
        return 'XX000';
    }

    /**
     * Whether an open failed because another process holds the file (DuckDB: one writing process, or any number of
     * reading ones).
     *
     * @param string $message
     * @return bool
     */
    public static function isLockError( $message )
    {
        return stripos( (string)$message, 'Could not set lock' ) !== false || stripos( (string)$message, 'Conflicting lock' ) !== false;
    }

    /**
     * Whether a statement failed because the connection is read only.
     *
     * @param string $message
     * @return bool
     */
    public static function isReadOnlyError( $message )
    {
        return stripos( (string)$message, 'read-only' ) !== false || stripos( (string)$message, 'read only mode' ) !== false;
    }
}
