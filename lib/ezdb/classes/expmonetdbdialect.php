<?php
/**
 * File containing the expMonetDBDialect class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * What expMonetDBDB changes in a statement before MonetDB sees it, how it escapes text and how it names an error.
 *
 * MonetDB's SQL is standard SQL close to PostgreSQL's in what the kernel writes (sequences, ||, SUBSTRING, LIMIT ...
 * OFFSET, information_schema, CASE, COALESCE), so the kernel's PostgreSQL branches run as they are. What is changed:
 *
 * - SELECT ... FOR UPDATE / FOR SHARE / LOCK IN SHARE MODE: MonetDB has no row locks. Its transactions are optimistic:
 *   two transactions that change the same rows conflict, and the later COMMIT fails (SQLSTATE 40001 here). The clause is
 *   left out.
 * - LOCK TABLE / UNLOCK TABLES: nothing to lock; the statement is not sent.
 * - MySQL words the kernel's default branches may still write: `quoted` names ("quoted"), LIMIT offset, count (LIMIT
 *   count OFFSET offset).
 * - Some column names of the kernel and its extensions are reserved words in MonetDB: day
 *   (ezgeneral_digest_user_settings), records (expaudit_file), chain and snapshot (expai_chain_run), details
 *   (expai_probe_result) and next (sqliimport_scheduled). In statements on those tables they are quoted; NEXT of a
 *   sequence's NEXT VALUE FOR is left as it is.
 *
 * String literals and quoted names are never changed. MonetDB reads C escapes in string literals ('a\nb'), so escape()
 * doubles a backslash as well as a quote.
 *
 * @since 6.0.15
 * @package lib
 */
class expMonetDBDialect
{
    /**
     * Columns of the kernel and its extensions that are reserved words in MonetDB, and the table whose statements quote
     * them. MonetDB's sys.keywords does not list every reserved word (details and snapshot are missing from it): a
     * name belongs here when CREATE TABLE t (<name> int) fails.
     */
    const RESERVED_COLUMNS = array( 'day' => 'ezgeneral_digest_user_settings', 'records' => 'expaudit_file',
                                    'chain' => 'expai_chain_run', 'snapshot' => 'expai_chain_run',
                                    'details' => 'expai_probe_result', 'next' => 'sqliimport_scheduled' );

    /** First words of statements that read only */
    const READ_WORDS = array( 'SELECT', 'SHOW', 'DESCRIBE', 'EXPLAIN', 'PLAN', 'TRACE', 'VALUES', 'WITH' );

    /** First words of transaction control */
    const TRANSACTION_WORDS = array( 'BEGIN', 'START', 'COMMIT', 'ROLLBACK', 'SAVEPOINT', 'RELEASE' );

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
     * Whether a statement changes data or the schema (for the query cache and the debug output).
     *
     * @param string $sql
     * @return bool
     */
    public static function isWrite( $sql )
    {
        $word = self::firstWord( $sql );
        if ( $word === '' || in_array( $word, self::TRANSACTION_WORDS, true ) || $word === 'SET' )
            return false;
        if ( $word === 'WITH' )
        {
            $blank = self::blankLiterals( $sql );
            return (bool)preg_match( '/\b(INSERT|UPDATE|DELETE|MERGE)\b/i', $blank === null ? $sql : $blank );
        }
        return !in_array( $word, self::READ_WORDS, true );
    }

    /**
     * The statement as MonetDB takes it; null when there is nothing to send (LOCK TABLE).
     *
     * @param string $sql
     * @return string|null
     */
    public static function rewrite( $sql )
    {
        $sql = rtrim( (string)$sql );
        $word = self::firstWord( $sql );
        if ( $word === 'LOCK' || $word === 'UNLOCK' )
            return null;
        $blank = self::blankLiterals( $sql );
        if ( $blank === null )
            return $sql;   // a literal that does not end: MonetDB says what is wrong with it

        // `name` -> "name": the backticks are outside literals in the blanked copy, at the same offsets
        if ( strpos( $blank, '`' ) !== false )
        {
            $out = '';
            for ( $i = 0, $n = strlen( $sql ); $i < $n; $i++ )
                $out .= $blank[$i] === '`' ? '"' : $sql[$i];
            $sql = $out;
            $blank = self::blankLiterals( $sql );
        }

        // the reserved column names, in statements on their table
        foreach ( self::RESERVED_COLUMNS as $column => $table )
        {
            if ( stripos( $blank, $table ) === false || !preg_match( '/(?<![\w"])' . $column . '(?![\w"])/i', $blank ) )
                continue;
            $sql = self::replaceOutsideLiterals( $sql, $blank, '/(?<![\w"])' . $column . '(?![\w"(])(?!\s+VALUE\s+FOR\b)/i', '"' . $column . '"' );
            $blank = self::blankLiterals( $sql );
        }

        if ( $word === 'SELECT' || $word === 'WITH' )
        {
            if ( preg_match( '/\s+(?:FOR\s+(?:UPDATE|SHARE|NO\s+KEY\s+UPDATE|KEY\s+SHARE)(?:\s+OF\s+[\w\s,."]+?)?(?:\s+(?:NOWAIT|SKIP\s+LOCKED))?|LOCK\s+IN\s+SHARE\s+MODE)\s*;?\s*$/i', $blank, $m, PREG_OFFSET_CAPTURE ) )
            {
                $sql = substr( $sql, 0, $m[0][1] );
                $blank = substr( $blank, 0, $m[0][1] );
            }
            if ( preg_match( '/\bLIMIT\s+(\d+)\s*,\s*(\d+)\s*;?\s*$/i', $blank, $m, PREG_OFFSET_CAPTURE ) )
                $sql = substr( $sql, 0, $m[0][1] ) . 'LIMIT ' . $m[2][0] . ' OFFSET ' . $m[1][0];
        }
        return $sql;
    }

    /**
     * Replaces matches of $pattern found in the blanked copy, in the statement itself.
     */
    protected static function replaceOutsideLiterals( $sql, $blank, $pattern, $replacement )
    {
        if ( !preg_match_all( $pattern, $blank, $matches, PREG_OFFSET_CAPTURE ) )
            return $sql;
        foreach ( array_reverse( $matches[0] ) as $match )
            $sql = substr( $sql, 0, $match[1] ) . $replacement . substr( $sql, $match[1] + strlen( $match[0] ) );
        return $sql;
    }

    /**
     * The statement with the inside of every string literal and quoted name replaced by spaces (same length, so
     * offsets hold). A backslash in a string literal escapes the next character, as MonetDB reads it. Null when a
     * literal does not end.
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
            if ( $c === '-' && $i + 1 < $n && $sql[$i + 1] === '-' )
            {
                $end = strpos( $sql, "\n", $i );
                $end = $end === false ? $n : $end;
                $out .= str_repeat( ' ', $end - $i );
                $i = $end - 1;
                continue;
            }
            if ( $c !== "'" && $c !== '"' )
            {
                $out .= $c;
                continue;
            }
            $end = $i + 1;
            while ( true )
            {
                if ( $end >= $n )
                    return null;
                $d = $sql[$end];
                if ( $c === "'" && $d === '\\' )
                {
                    $end += 2;
                    continue;
                }
                if ( $d === $c )
                {
                    if ( $end + 1 < $n && $sql[$end + 1] === $c )
                    {
                        $end += 2;
                        continue;
                    }
                    break;
                }
                $end++;
            }
            $out .= $c . str_repeat( ' ', $end - $i - 1 ) . $c;
            $i = $end;
        }
        return $out;
    }

    /**
     * A string as the inside of a literal: backslashes and quotes doubled, NUL bytes left out.
     *
     * @param string|null $text
     * @return string
     */
    public static function escape( $text )
    {
        if ( $text === null )
            return '';
        return str_replace( array( "\0", '\\', "'" ), array( '', '\\\\', "''" ), (string)$text );
    }

    /**
     * The error number of a failed statement: the SQLSTATE PostgreSQL gives the same error, so code that tells a
     * duplicate key ('23505') or a missing table ('42P01') from the rest works unchanged. MonetDB gives 40002 for
     * every broken constraint and 42000 for most other errors; the message tells them apart.
     *
     * @param string $state the SQLSTATE MonetDB gave
     * @param string $message
     * @return string
     */
    public static function errorCode( $state, $message )
    {
        $state = (string)$state;
        $message = (string)$message;
        if ( stripos( $message, 'concurrency conflict' ) !== false || stripos( $message, 'transaction is aborted' ) !== false
             || stripos( $message, 'conflict with another transaction' ) !== false )
            return '40001';
        if ( $state === '40002' || stripos( $message, 'constraint' ) !== false && stripos( $message, 'violated' ) !== false )
        {
            if ( stripos( $message, 'NOT NULL' ) !== false )
                return '23502';
            if ( stripos( $message, 'FOREIGN KEY' ) !== false )
                return '23503';
            if ( stripos( $message, 'CHECK' ) !== false )
                return '23514';
            return '23505';
        }
        if ( $state === '42S02' || preg_match( '/no such (table|view)/i', $message ) )
            return '42P01';
        if ( preg_match( "/(identifier '.*' unknown|no such column|column .* unknown)/i", $message ) )
            return '42703';
        if ( preg_match( '/name .* already in use|already exists/i', $message ) )
            return '42P07';
        if ( stripos( $message, 'syntax error' ) !== false )
            return '42601';
        if ( stripos( $message, 'too long for type' ) !== false )
            return '22001';
        if ( stripos( $message, 'conversion of string' ) !== false || stripos( $message, 'cannot convert' ) !== false )
            return '22P02';
        if ( $state === '08006' || $state === '08001' || $state === '08003' )
            return $state;
        return $state !== '' ? $state : 'XX000';
    }
}
