<?php
/**
 * File containing the expClickHouseDialect class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * What expClickHouseDB changes in a statement before ClickHouse sees it, how it escapes text and how it names an error.
 *
 * ClickHouse's SQL reads most of what portable code writes: SELECT with JOIN, GROUP BY, HAVING, UNION, subqueries,
 * LIMIT n OFFSET m and MySQL's LIMIT m, n, backticks and double quotes, INSERT ... VALUES with several rows, DELETE
 * (a lightweight delete). What is changed:
 *
 * - START TRANSACTION, BEGIN, COMMIT, ROLLBACK: ClickHouse has no transactions over statements (each INSERT is atomic
 *   on its own); nothing is sent, and expClickHouseDB warns when a rollback cannot undo what was written.
 * - SELECT ... FOR UPDATE / FOR SHARE / LOCK IN SHARE MODE, LOCK TABLE: nothing to lock; left out or not sent.
 * - UPDATE t SET ... WHERE ...: sent as ClickHouse's lightweight UPDATE (25.7 and later, on a table with the block
 *   number columns, as expClickHouseSchema makes them); refused there, it is sent again as the mutation ALTER TABLE t
 *   UPDATE ... WHERE ..., waited for (mutations_sync). An UPDATE without WHERE gets WHERE 1.
 * - DELETE FROM t without WHERE: TRUNCATE TABLE t.
 *
 * String literals and quoted names are never changed. ClickHouse reads backslash escapes in string literals, so
 * escape() doubles a backslash and writes a quote as \'.
 *
 * @since 6.0.15
 * @package lib
 */
class expClickHouseDialect
{
    /** First words of statements that read only */
    const READ_WORDS = array( 'SELECT', 'WITH', 'SHOW', 'DESCRIBE', 'DESC', 'EXPLAIN', 'EXISTS', 'CHECK' );

    /** First words of transaction control */
    const TRANSACTION_WORDS = array( 'BEGIN', 'START', 'COMMIT', 'ROLLBACK', 'END', 'SAVEPOINT', 'RELEASE' );

    /** A table name, perhaps with its database, perhaps quoted (in a statement with blanked literals) */
    const NAME_PATTERN = '(?:`[^`]*`|"[^"]*"|\w)(?:`[^`]*`|"[^"]*"|[\w.])*';

    /** ClickHouse's error numbers and the SQLSTATE PostgreSQL gives the same error */
    const ERROR_STATES = array(
        60 => '42P01',      // UNKNOWN_TABLE
        81 => '3D000',      // UNKNOWN_DATABASE
        47 => '42703',      // UNKNOWN_IDENTIFIER
        16 => '42703',      // NO_SUCH_COLUMN_IN_TABLE
        57 => '42P07',      // TABLE_ALREADY_EXISTS
        62 => '42601',      // SYNTAX_ERROR
        46 => '42883',      // UNKNOWN_FUNCTION
        48 => '0A000',      // NOT_IMPLEMENTED
        1 => '0A000',       // UNSUPPORTED_METHOD
        53 => '42804',      // TYPE_MISMATCH
        6 => '22P02',       // CANNOT_PARSE_TEXT
        27 => '22P02',      // CANNOT_PARSE_INPUT_ASSERTION_FAILED
        43 => '42804',      // ILLEGAL_TYPE_OF_ARGUMENT
        159 => '57014',     // TIMEOUT_EXCEEDED
        394 => '57014',     // QUERY_WAS_CANCELLED
        516 => '28000',     // AUTHENTICATION_FAILED
        192 => '28000',     // UNKNOWN_USER
        497 => '42501',     // ACCESS_DENIED
        164 => '25006',     // READONLY
        241 => '53200',     // MEMORY_LIMIT_EXCEEDED
        209 => '08006',     // SOCKET_TIMEOUT
        210 => '08006',     // NETWORK_ERROR
    );

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
     * Whether a statement changes data or the schema.
     *
     * @param string $sql
     * @return bool
     */
    public static function isWrite( $sql )
    {
        $word = self::firstWord( $sql );
        if ( $word === '' || in_array( $word, self::TRANSACTION_WORDS, true ) || $word === 'SET' || $word === 'USE' )
            return false;
        return !in_array( $word, self::READ_WORDS, true );
    }

    /**
     * Whether a statement is transaction control (not sent: ClickHouse has none).
     */
    public static function isTransaction( $sql )
    {
        return in_array( self::firstWord( $sql ), self::TRANSACTION_WORDS, true );
    }

    /**
     * The statement as ClickHouse takes it; null when there is nothing to send (LOCK TABLE, transaction control).
     *
     * @param string $sql
     * @return string|null
     */
    public static function rewrite( $sql )
    {
        $sql = rtrim( (string)$sql, " \t\r\n;" );
        $word = self::firstWord( $sql );
        if ( $word === 'LOCK' || $word === 'UNLOCK' || in_array( $word, self::TRANSACTION_WORDS, true ) )
            return null;
        $blank = self::blankLiterals( $sql );
        if ( $blank === null )
            return $sql;   // a literal that does not end: ClickHouse says what is wrong with it

        if ( $word === 'SELECT' || $word === 'WITH' )
        {
            if ( preg_match( '/\s+(?:FOR\s+(?:UPDATE|SHARE|NO\s+KEY\s+UPDATE|KEY\s+SHARE)(?:\s+OF\s+[\w\s,."`]+?)?(?:\s+(?:NOWAIT|SKIP\s+LOCKED))?|LOCK\s+IN\s+SHARE\s+MODE)\s*$/i', $blank, $m, PREG_OFFSET_CAPTURE ) )
                $sql = substr( $sql, 0, $m[0][1] );
            return $sql;
        }
        // a quoted name is blanked like a literal: `   ` in $blank, the name itself at the same offset in $sql
        if ( $word === 'DELETE' && preg_match( '/^\s*DELETE\s+FROM\s+(' . self::NAME_PATTERN . ')\s*$/i', $blank, $m, PREG_OFFSET_CAPTURE ) )
            return 'TRUNCATE TABLE ' . substr( $sql, $m[1][1], strlen( $m[1][0] ) );
        if ( $word === 'UPDATE' && !preg_match( '/\bWHERE\b/i', $blank ) )
            return $sql . ' WHERE 1';
        return $sql;
    }

    /**
     * An UPDATE as ClickHouse's mutation ALTER TABLE ... UPDATE ... WHERE ... (pure); null when it is no simple UPDATE
     * of one table.
     *
     * @param string $sql
     * @return string|null
     */
    public static function updateAsMutation( $sql )
    {
        $sql = rtrim( (string)$sql, " \t\r\n;" );
        $blank = self::blankLiterals( $sql );
        if ( $blank === null || !preg_match( '/^\s*UPDATE\s+(' . self::NAME_PATTERN . ')\s+SET\s+/i', $blank, $m, PREG_OFFSET_CAPTURE ) )
            return null;
        $table = substr( $sql, $m[1][1], strlen( $m[1][0] ) );
        $rest = substr( $sql, strlen( $m[0][0] ) );
        $restBlank = substr( $blank, strlen( $m[0][0] ) );
        if ( !preg_match( '/\bWHERE\b/i', $restBlank ) )
            return "ALTER TABLE $table UPDATE " . $rest . ' WHERE 1';
        return "ALTER TABLE $table UPDATE " . $rest;
    }

    /**
     * The statement with the inside of every string literal and quoted name replaced by spaces (same length, so
     * offsets hold). Backslash escapes count, as ClickHouse reads them. Null when a literal does not end.
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
            if ( $c !== "'" && $c !== '"' && $c !== '`' )
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
                if ( $d === '\\' )
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
     * A string as the inside of a literal: a backslash doubled, a quote as \'.
     *
     * @param string|null $text
     * @return string
     */
    public static function escape( $text )
    {
        if ( $text === null )
            return '';
        return str_replace( array( '\\', "'" ), array( '\\\\', "\\'" ), (string)$text );
    }

    /**
     * The error number of a failed statement: the SQLSTATE PostgreSQL gives the same error where there is one
     * ('42P01' missing table), else 'CH' and ClickHouse's own number.
     *
     * @param int $code ClickHouse's error number (-1: no answer)
     * @return string
     */
    public static function errorCode( $code )
    {
        $code = (int)$code;
        if ( $code === -1 )
            return '08006';
        if ( isset( self::ERROR_STATES[$code] ) )
            return self::ERROR_STATES[$code];
        return $code > 0 ? 'CH' . $code : 'XX000';
    }

    /**
     * Whether an UPDATE failed because the table takes no lightweight update (it is then sent as a mutation).
     *
     * @param int $code
     * @param string $message
     * @return bool
     */
    public static function isLightweightUpdateRefused( $code, $message )
    {
        // 25.7 and later without block numbers: "Lightweight updates are not supported"; before 25.7 the setting is unknown (115)
        return stripos( (string)$message, 'Lightweight update' ) !== false
            || ( (int)$code === 115 && stripos( (string)$message, 'lightweight_update' ) !== false )
            || ( (int)$code === 48 && stripos( (string)$message, 'update' ) !== false )
            || ( (int)$code === 62 && stripos( (string)$message, 'UPDATE' ) !== false );
    }
}
