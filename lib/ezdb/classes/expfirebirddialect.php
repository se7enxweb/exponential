<?php
/**
 * File containing the expFirebirdDialect class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The Firebird SQL that expFirebirdDB writes, as pure functions of their arguments: no connection, no settings.
 * Every rule here is tested without a server (tests/tests/lib/ezdb/firebird).
 *
 * Supported servers: Firebird 4.0 and 5.0. 4.0 is the floor because of names of up to 63 characters (Firebird 3
 * allows 31, shorter than some of the kernel's index names), CRYPT_HASH() (md5()) and time zones (the UTC clock of
 * unixTimestamp()). Statements are read by expSQLServerLexer, which splits any SQL into tokens without losing a
 * byte; nothing here rewrites a statement with a regular expression over its text, so a keyword inside a string
 * literal or a comment never changes what a statement does.
 *
 * What the kernel writes that Firebird reads differently, and what this class makes of it (rewrite()):
 *
 *  - Names: Firebird stores an unquoted name in upper case, and a quoted one as written. A MySQL `name` becomes
 *    "NAME", so it is the same table or column as the unquoted name. A column named like a reserved word of
 *    Firebird (value, count, time, day, position ...) is written "VALUE" etc. where it stands as a name.
 *  - Functions MySQL and PostgreSQL have and Firebird does not: LENGTH() is CHAR_LENGTH(), IFNULL() COALESCE(),
 *    NOW() LOCALTIMESTAMP, GREATEST()/LEAST() MAXVALUE()/MINVALUE(), UCASE()/LCASE() UPPER()/LOWER(), CONCAT(a, b)
 *    (a || b), SUBSTRING(s, f, n) SUBSTRING(s FROM f FOR n), MD5(x) the hex text of CRYPT_HASH(x USING MD5),
 *    UNIX_TIMESTAMP() the seconds since 1970 of the UTC clock.
 *  - LIKE: MySQL and PostgreSQL take a backslash as the escape character of a pattern; Firebird has none unless the
 *    statement names one. A pattern with a backslash gets ESCAPE '\'.
 *  - LIMIT n [OFFSET m] and LIMIT m, n at the end of a SELECT: OFFSET m ROWS FETCH NEXT n ROWS ONLY.
 *  - SELECT ... FOR UPDATE: Firebird's FOR UPDATE only names a cursor; FOR UPDATE WITH LOCK locks the rows.
 *  - SELECT without FROM: FROM RDB$DATABASE (Firebird's one-row table).
 *  - TRUNCATE TABLE t: DELETE FROM t (Firebird has no TRUNCATE).
 */
class expFirebirdDialect
{
    /** Longest name Firebird 4 and 5 accept, in characters */
    const MAX_IDENTIFIER_LENGTH = 63;

    /** Most values of one IN list (Firebird 4: 1,500; Firebird 5 takes 65,535) */
    const MAX_IN_LIST = 1500;

    /** Lowest supported major version */
    const MIN_MAJOR_VERSION = 4;

    /** The default port of the server */
    const DEFAULT_PORT = 3050;

    /** Firebird's one-row table, for a SELECT of expressions only */
    const ONE_ROW_TABLE = 'RDB$DATABASE';

    /**
     * The reserved words of Firebird 5 (Language Reference, appendix "Reserved Words and Keywords"): a table or
     * column of one of these names must be quoted.
     *
     * @var string[]
     */
    const RESERVED_WORDS = array(
        'ADD', 'ADMIN', 'ALL', 'ALTER', 'AND', 'ANY', 'AS', 'AT', 'AVG', 'BEGIN', 'BETWEEN', 'BIGINT', 'BINARY', 'BIT_LENGTH',
        'BLOB', 'BOOLEAN', 'BOTH', 'BY', 'CASE', 'CAST', 'CHAR', 'CHAR_LENGTH', 'CHARACTER', 'CHARACTER_LENGTH', 'CHECK',
        'CLOSE', 'COLLATE', 'COLUMN', 'COMMENT', 'COMMIT', 'CONNECT', 'CONSTRAINT', 'CORR', 'COUNT', 'COVAR_POP', 'COVAR_SAMP',
        'CREATE', 'CROSS', 'CURRENT', 'CURRENT_CONNECTION', 'CURRENT_DATE', 'CURRENT_ROLE', 'CURRENT_TIME', 'CURRENT_TIMESTAMP',
        'CURRENT_TRANSACTION', 'CURRENT_USER', 'CURSOR', 'DATE', 'DAY', 'DEC', 'DECFLOAT', 'DECIMAL', 'DECLARE', 'DEFAULT',
        'DELETE', 'DELETING', 'DETERMINISTIC', 'DISCONNECT', 'DISTINCT', 'DOUBLE', 'DROP', 'ELSE', 'END', 'ESCAPE', 'EXECUTE',
        'EXISTS', 'EXTERNAL', 'EXTRACT', 'FALSE', 'FETCH', 'FILTER', 'FLOAT', 'FOR', 'FOREIGN', 'FROM', 'FULL', 'FUNCTION',
        'GDSCODE', 'GLOBAL', 'GRANT', 'GROUP', 'HAVING', 'HOUR', 'IN', 'INDEX', 'INNER', 'INSENSITIVE', 'INSERT', 'INSERTING',
        'INT', 'INT128', 'INTEGER', 'INTO', 'IS', 'JOIN', 'LATERAL', 'LEADING', 'LEFT', 'LIKE', 'LOCAL', 'LOCALTIME',
        'LOCALTIMESTAMP', 'LONG', 'LOWER', 'MAX', 'MERGE', 'MIN', 'MINUTE', 'MONTH', 'NATIONAL', 'NATURAL', 'NCHAR', 'NO',
        'NOT', 'NULL', 'NUMERIC', 'OCTET_LENGTH', 'OF', 'OFFSET', 'ON', 'ONLY', 'OPEN', 'OR', 'ORDER', 'OUTER', 'OVER',
        'PARAMETER', 'PLAN', 'POSITION', 'POST_EVENT', 'PRECISION', 'PRIMARY', 'PROCEDURE', 'PUBLICATION', 'REAL',
        'RECORD_VERSION', 'RECREATE', 'RECURSIVE', 'REFERENCES', 'REGR_AVGX', 'REGR_AVGY', 'REGR_COUNT', 'REGR_INTERCEPT',
        'REGR_R2', 'REGR_SLOPE', 'REGR_SXX', 'REGR_SXY', 'REGR_SYY', 'RELEASE', 'RESETTING', 'RETURN', 'RETURNING_VALUES',
        'RETURNS', 'REVOKE', 'RIGHT', 'ROLLBACK', 'ROW', 'ROW_COUNT', 'ROWS', 'SAVEPOINT', 'SCROLL', 'SECOND', 'SELECT',
        'SENSITIVE', 'SET', 'SIMILAR', 'SMALLINT', 'SOME', 'SQLCODE', 'SQLSTATE', 'START', 'STDDEV_POP', 'STDDEV_SAMP', 'SUM',
        'TABLE', 'THEN', 'TIME', 'TIMESTAMP', 'TIMEZONE_HOUR', 'TIMEZONE_MINUTE', 'TO', 'TRAILING', 'TRIGGER', 'TRIM', 'TRUE',
        'UNBOUNDED', 'UNION', 'UNIQUE', 'UNKNOWN', 'UPDATE', 'UPDATING', 'UPPER', 'USER', 'USING', 'VALUE', 'VALUES',
        'VAR_POP', 'VAR_SAMP', 'VARBINARY', 'VARCHAR', 'VARIABLE', 'VARYING', 'VIEW', 'WHEN', 'WHERE', 'WHILE', 'WINDOW',
        'WITH', 'WITHOUT', 'YEAR',
    );

    /**
     * Reserved words that are names in the statements Exponential writes (columns of the kernel and of extensions:
     * ezsite_data.value, ezview_counter.count, ezuser_accountkey.time ...). rewrite() quotes them where they stand
     * as a name: not as a function call (COUNT(*), POSITION(...)), not as a type (CAST(x AS DATE)), not before a
     * literal (DATE '2026-01-01'), not as the unit of EXTRACT(DAY FROM x).
     *
     * @var string[]
     */
    const NAME_WORDS = array( 'VALUE', 'COUNT', 'TIME', 'DAY', 'POSITION', 'DATE', 'TIMESTAMP', 'YEAR', 'MONTH', 'HOUR',
                              'MINUTE', 'SECOND', 'COMMENT', 'USER', 'ROW', 'CHECK', 'PLAN', 'OPEN', 'CLOSE', 'START', 'RELEASE',
                              'GLOBAL', 'LOCAL', 'CONNECT', 'TRIM', 'FILTER', 'ADMIN', 'BINARY', 'LONG', 'CURSOR', 'WINDOW' );

    /** MySQL/PostgreSQL functions that only have another name here */
    const RENAMED_FUNCTIONS = array( 'LENGTH' => 'CHAR_LENGTH', 'IFNULL' => 'COALESCE', 'GREATEST' => 'MAXVALUE',
                                     'LEAST' => 'MINVALUE', 'UCASE' => 'UPPER', 'LCASE' => 'LOWER', 'CHARACTER_LENGTH' => 'CHAR_LENGTH' );

    /** @var array|null RESERVED_WORDS as keys */
    private static $reserved = null;

    /** @var array|null NAME_WORDS as keys */
    private static $nameWords = null;

    // ------------------------------------------------------------------ names

    /**
     * Whether $word is a reserved word of Firebird (any case).
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
     * One name in double quotes, in upper case (the way Firebird stores the same name unquoted), with a '"' inside
     * doubled. An empty name, a name with a NUL byte and a name of more than 63 characters are refused.
     *
     * @param string $name
     * @return string
     * @throws InvalidArgumentException
     */
    public static function quoteIdentifier( $name )
    {
        $name = (string)$name;
        self::checkIdentifier( $name );
        return '"' . str_replace( '"', '""', self::upper( $name ) ) . '"';
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
            $parts[] = self::quoteIdentifier( $part );
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
        if ( !self::isReservedWord( $name ) && preg_match( '/^[A-Za-z][A-Za-z0-9_$]*$/', $name ) )
            return $name;
        return self::quoteIdentifier( $name );
    }

    /**
     * @param string $name
     * @throws InvalidArgumentException when $name is no name Firebird takes
     */
    public static function checkIdentifier( $name )
    {
        $name = (string)$name;
        if ( $name === '' || strpos( $name, "\0" ) !== false )
            throw new InvalidArgumentException( 'An empty name or a name with a NUL byte is no identifier' );
        $length = function_exists( 'mb_strlen' ) ? mb_strlen( $name, 'UTF-8' ) : strlen( $name );
        if ( $length > self::MAX_IDENTIFIER_LENGTH )
            throw new InvalidArgumentException( "The name '$name' is longer than " . self::MAX_IDENTIFIER_LENGTH . ' characters' );
    }

    /**
     * A name in upper case as Firebird folds it (ASCII letters only: Firebird does not fold other letters).
     *
     * @param string $name
     * @return string
     */
    public static function upper( $name )
    {
        return strtr( (string)$name, 'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
    }

    // ------------------------------------------------------------------ literals

    /**
     * Text for inside a string literal: a quote doubled. A backslash is an ordinary character in Firebird.
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
     * The first keyword of a statement (SELECT, INSERT, UPDATE, DELETE, CREATE, WITH ...), or ''.
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
        return in_array( self::statementKind( $sql ), array( 'CREATE', 'ALTER', 'DROP', 'RECREATE', 'COMMENT' ), true );
    }

    /**
     * A statement of the kernel (MySQL words, as far as they reach the driver) in Firebird's words; see the class
     * comment for the rules. Statements of the schema (CREATE, ALTER, DROP ...) keep their function names: they
     * come from the schema handler in Firebird's words already, and a trigger body is no place for rewriting.
     *
     * @param string $sql
     * @return string
     */
    public static function rewrite( $sql )
    {
        $sql = trim( (string)$sql );
        while ( substr( $sql, -1 ) === ';' )
            $sql = rtrim( substr( $sql, 0, -1 ) );
        $kind = self::statementKind( $sql );
        if ( in_array( $kind, array( 'CREATE', 'ALTER', 'DROP', 'RECREATE', 'COMMENT', 'EXECUTE', 'SET', 'GRANT', 'REVOKE' ), true ) )
            return self::backticksToQuotes( $sql );
        if ( $kind === 'TRUNCATE' )
            return self::truncateToDelete( $sql );
        $sql = self::bitOperators( $sql );
        $tokens = expSQLServerLexer::tokenize( $sql );
        $sql = self::rewriteTokens( $tokens );
        if ( $kind === 'SELECT' || $kind === 'WITH' )
        {
            $sql = self::limitClauseToOffsetFetch( $sql );
            $sql = self::lockingForUpdate( $sql );
            if ( $kind === 'SELECT' )
                $sql = self::addOneRowTable( $sql );
        }
        return $sql;
    }

    /**
     * DROP <type> IF EXISTS name and CREATE <type> IF NOT EXISTS name (Firebird 4 and 5 have neither): array(
     * 'exists' => true for IF NOT EXISTS (run when the object is missing), false for IF EXISTS (run when it is there),
     * 'type' => TABLE, INDEX, SEQUENCE, GENERATOR, VIEW or TRIGGER, 'name' => the name as Firebird stores it,
     * 'sql' => the statement without the condition ), or null for any other statement.
     *
     * @param string $sql
     * @return array|null
     */
    public static function conditionalDDL( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( trim( (string)$sql ) );
        $significant = expSQLServerLexer::significant( $tokens );
        $words = array();
        foreach ( array_slice( $significant, 0, 6 ) as $entry )
            $words[] = expSQLServerLexer::keyword( $tokens[$entry[0]] );
        if ( count( $words ) < 4 || !in_array( $words[0], array( 'CREATE', 'DROP' ), true ) )
            return null;
        $n = 1;
        if ( $words[0] === 'CREATE' && in_array( $words[1], array( 'UNIQUE', 'ASC', 'ASCENDING', 'DESC', 'DESCENDING' ), true ) )
            $n++;
        $type = isset( $words[$n] ) ? $words[$n] : '';
        if ( !in_array( $type, array( 'TABLE', 'INDEX', 'SEQUENCE', 'GENERATOR', 'VIEW', 'TRIGGER' ), true ) )
            return null;
        $condition = $words[0] === 'CREATE' ? array( 'IF', 'NOT', 'EXISTS' ) : array( 'IF', 'EXISTS' );
        if ( array_slice( $words, $n + 1, count( $condition ) ) !== $condition || !isset( $significant[$n + 1 + count( $condition )] ) )
            return null;
        $first = $significant[$n + 1][0];
        $last = $significant[$n + count( $condition )][0];
        $nameToken = $tokens[$significant[$n + 1 + count( $condition )][0]];
        $name = $nameToken['type'] === expSQLServerLexer::IDENT && $nameToken['text'][0] === '"'
              ? expSQLServerLexer::identifierValue( $nameToken ) : self::upper( expSQLServerLexer::identifierValue( $nameToken ) );
        $rest = expSQLServerLexer::join( array_slice( $tokens, 0, $first ) ) . ltrim( expSQLServerLexer::join( array_slice( $tokens, $last + 1 ) ) );
        return array( 'exists' => $words[0] === 'CREATE', 'type' => $type, 'name' => $name, 'sql' => $rest );
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
        if ( count( $significant ) !== 3 || expSQLServerLexer::keyword( $tokens[$significant[0][0]] ) !== 'DROP'
             || expSQLServerLexer::keyword( $tokens[$significant[1][0]] ) !== 'TABLE' )
            return null;
        return strtolower( expSQLServerLexer::identifierValue( $tokens[$significant[2][0]] ) );
    }

    /**
     * The catalogue query that finds an object of a type by its stored name (one row when it exists).
     *
     * @param string $type TABLE, INDEX, SEQUENCE, GENERATOR, VIEW or TRIGGER
     * @param string $name
     * @return string
     */
    public static function existsQuery( $type, $name )
    {
        $literal = self::stringLiteral( $name );
        switch ( $type )
        {
            case 'INDEX':
                return "SELECT 1 AS found FROM RDB\$INDICES WHERE RDB\$INDEX_NAME = $literal";
            case 'SEQUENCE':
            case 'GENERATOR':
                return "SELECT 1 AS found FROM RDB\$GENERATORS WHERE RDB\$GENERATOR_NAME = $literal";
            case 'TRIGGER':
                return "SELECT 1 AS found FROM RDB\$TRIGGERS WHERE RDB\$TRIGGER_NAME = $literal";
        }
        return "SELECT 1 AS found FROM RDB\$RELATIONS WHERE RDB\$RELATION_NAME = $literal";
    }

    /**
     * The bit operators of MySQL and PostgreSQL as Firebird's functions: a & b as BIN_AND(a, b), a | b as BIN_OR(a, b)
     * (|| stays the concatenation), ~a as BIN_NOT(a). An operand is a name (qualified or not), a number, a "?", a
     * call or a parenthesized expression, as in the kernel's language mask conditions
     * (ezcontentobject.language_mask & 6 > 0); & and | bind tighter than a comparison there, as in MySQL.
     *
     * @param string $sql
     * @return string
     */
    public static function bitOperators( $sql )
    {
        $sql = (string)$sql;
        if ( strpbrk( $sql, '&|~' ) === false && strpos( $sql, '>>' ) === false && strpos( $sql, '<<' ) === false )
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
                // >> and << (two adjacent tokens): BIN_SHR, BIN_SHL
                $shift = ( $t['text'] === '>' || $t['text'] === '<' ) && isset( $tokens[$i + 1] ) && $tokens[$i + 1]['text'] === $t['text'];
                if ( $shift )
                {
                    $right = self::operandAfter( $tokens, $i + 1 );
                    $left = $right === null ? null : self::operandBefore( $tokens, $i );
                    if ( $left === null )
                    {
                        $i++;
                        continue;
                    }
                    $sql = expSQLServerLexer::join( array_slice( $tokens, 0, $left ) ) . ( $t['text'] === '>' ? 'BIN_SHR(' : 'BIN_SHL(' )
                         . trim( expSQLServerLexer::join( array_slice( $tokens, $left, $i - $left ) ) ) . ', '
                         . trim( expSQLServerLexer::join( array_slice( $tokens, $i + 2, $right - $i - 1 ) ) ) . ')'
                         . expSQLServerLexer::join( array_slice( $tokens, $right + 1 ) );
                    $done = false;
                    break;
                }
                if ( !in_array( $t['text'], array( '&', '|', '~' ), true ) )
                    continue;
                if ( $t['text'] === '|' && ( ( isset( $tokens[$i + 1] ) && $tokens[$i + 1]['text'] === '|' ) || ( $i > 0 && $tokens[$i - 1]['text'] === '|' ) ) )
                    continue;
                $right = self::operandAfter( $tokens, $i );
                if ( $right === null )
                    continue;
                if ( $t['text'] === '~' )
                {
                    $sql = expSQLServerLexer::join( array_slice( $tokens, 0, $i ) ) . 'BIN_NOT(' . trim( expSQLServerLexer::join( array_slice( $tokens, $i + 1, $right - $i ) ) ) . ')'
                         . expSQLServerLexer::join( array_slice( $tokens, $right + 1 ) );
                    $done = false;
                    break;
                }
                $left = self::operandBefore( $tokens, $i );
                if ( $left === null )
                    continue;
                $function = $t['text'] === '&' ? 'BIN_AND' : 'BIN_OR';
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
    private static function operandBefore( array $tokens, $at )
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
    private static function operandAfter( array $tokens, $at )
    {
        $count = count( $tokens );
        $j = $at + 1;
        while ( $j < $count && expSQLServerLexer::isNoise( $tokens[$j] ) )
            $j++;
        if ( $j >= $count )
            return null;
        if ( $tokens[$j]['text'] === '-' || $tokens[$j]['text'] === '~' )
        {
            $inner = self::operandAfter( $tokens, $j );
            return $inner;
        }
        if ( $tokens[$j]['text'] === '(' )
            return self::matchingParen( $tokens, $j );
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
            return self::matchingParen( $tokens, $next );
        return $end;
    }

    /**
     * MySQL's `name` as "NAME"; nothing else changed.
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
                $out .= '"' . str_replace( '"', '""', self::upper( expSQLServerLexer::identifierValue( $token ) ) ) . '"';
            else
                $out .= $token['text'];
        }
        return $out;
    }

    /**
     * TRUNCATE [TABLE] t as DELETE FROM t.
     *
     * @param string $sql
     * @return string
     */
    public static function truncateToDelete( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( self::backticksToQuotes( $sql ) );
        $significant = expSQLServerLexer::significant( $tokens );
        $n = 1;
        if ( isset( $significant[$n] ) && expSQLServerLexer::keyword( $tokens[$significant[$n][0]] ) === 'TABLE' )
            $n++;
        if ( !isset( $significant[$n] ) )
            return $sql;
        return 'DELETE FROM ' . trim( expSQLServerLexer::join( array_slice( $tokens, $significant[$n][0] ) ) );
    }

    /**
     * The token-level rules of rewrite(): names, functions, LIKE patterns.
     *
     * @param array[] $tokens
     * @return string
     */
    protected static function rewriteTokens( array $tokens )
    {
        if ( self::$nameWords === null )
            self::$nameWords = array_fill_keys( self::NAME_WORDS, true );
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
            $next = self::nextSignificant( $tokens, $i );
            $nextToken = $next === null ? null : $tokens[$next];
            $nextText = $nextToken === null ? '' : $nextToken['text'];
            $nextWord = $nextToken === null ? '' : expSQLServerLexer::keyword( $nextToken );

            if ( $token['type'] === expSQLServerLexer::IDENT && $token['text'][0] === '`' )
            {
                $text = '"' . str_replace( '"', '""', self::upper( expSQLServerLexer::identifierValue( $token ) ) ) . '"';
            }
            else if ( $token['type'] === expSQLServerLexer::WORD )
            {
                $word = strtoupper( $token['text'] );
                $text = $token['text'];
                $isCall = $nextText === '(' && $prevText !== '.';
                if ( $isCall && isset( self::RENAMED_FUNCTIONS[$word] ) )
                    $text = self::RENAMED_FUNCTIONS[$word];
                else if ( $isCall && in_array( $word, array( 'CONCAT', 'SUBSTRING', 'SUBSTR', 'MD5', 'NOW', 'UNIX_TIMESTAMP', 'CURDATE' ), true ) )
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
                else if ( !$isCall && isset( self::$nameWords[$word] ) && self::standsAsName( $word, $prevWord, $prevText, $nextToken, $nextWord ) )
                    $text = '"' . $word . '"';
                // an alias that is a reserved word (AS row_count, AS position), not the type of a CAST
                else if ( !$isCall && $prevWord === 'AS' && self::isReservedWord( $word ) && !self::isTypeAfterAs( $word, $nextToken, $nextWord ) )
                    $text = '"' . $word . '"';
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
     * Whether a reserved word of NAME_WORDS stands as a name where it is.
     */
    private static function standsAsName( $word, $prevWord, $prevText, $nextToken, $nextWord )
    {
        // DATE '2026-01-01', TIMESTAMP '...'
        if ( $nextToken !== null && $nextToken['type'] === expSQLServerLexer::STRING )
            return false;
        // EXTRACT(DAY FROM x), DATEADD(DAY, 1, x), DATEDIFF(DAY FROM a TO b)
        if ( $nextWord === 'FROM' && $prevText === '(' )
            return false;
        if ( $prevText === '(' && $nextToken !== null && $nextToken['text'] === ',' && in_array( $word, array( 'DAY', 'YEAR', 'MONTH', 'HOUR', 'MINUTE', 'SECOND' ), true ) )
            return false;
        // CAST(x AS DATE), CAST(x AS TIMESTAMP WITHOUT TIME ZONE), TIME ZONE
        if ( $prevWord === 'AS' && in_array( $word, array( 'DATE', 'TIME', 'TIMESTAMP' ), true )
             && ( $nextToken === null || in_array( $nextToken['text'], array( ')', '(' ), true ) || in_array( $nextWord, array( 'WITH', 'WITHOUT' ), true ) ) )
            return false;
        if ( in_array( $prevWord, array( 'WITH', 'WITHOUT' ), true ) && $word === 'TIME' && $nextWord === 'ZONE' )
            return false;
        // CURRENT_... and words after another keyword that make a clause: WITH LOCK, FOR UPDATE ... are not names
        if ( $word === 'ROW' && in_array( $prevWord, array( 'ON', 'EACH' ), true ) )
            return false;
        if ( $word === 'USER' && in_array( $prevWord, array( 'CURRENT', 'TO', 'FROM', 'GRANT' ), true ) )
            return false;
        if ( $word === 'TIME' && $prevWord === 'AT' )
            return false;
        if ( $word === 'LOCAL' && $prevWord === 'AT' )
            return false;
        return true;
    }

    /** Words that start a type */
    const TYPE_WORDS = array( 'BIGINT', 'BLOB', 'BOOLEAN', 'CHAR', 'CHARACTER', 'DATE', 'DEC', 'DECIMAL', 'DECFLOAT', 'DOUBLE', 'FLOAT',
                              'INT', 'INT128', 'INTEGER', 'NATIONAL', 'NCHAR', 'NUMERIC', 'REAL', 'SMALLINT', 'TIME', 'TIMESTAMP',
                              'VARCHAR', 'VARBINARY', 'BINARY' );

    /**
     * Whether a word after AS is the type of a CAST (followed by ")", "(", a type's next word, or the end).
     */
    private static function isTypeAfterAs( $word, $nextToken, $nextWord )
    {
        if ( !in_array( $word, self::TYPE_WORDS, true ) )
            return false;
        if ( $nextToken === null || in_array( $nextToken['text'], array( ')', '(' ), true ) )
            return true;
        return in_array( $nextWord, array( 'WITH', 'WITHOUT', 'PRECISION', 'SUB_TYPE', 'CHARACTER', 'VARYING', 'COLLATE' ), true );
    }

    /**
     * The rewrite of a call of a MySQL function Firebird lacks, from its arguments (each a token list); null when the
     * call is left as it is.
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
            case 'SUBSTR':
                // SUBSTRING(s FROM f FOR n) has one argument: left as it is
                if ( count( $text ) < 2 )
                    return null;
                return self::subString( $text[0], $text[1], isset( $text[2] ) ? $text[2] : null );
            case 'MD5':
                return count( $text ) === 1 ? self::md5( $text[0] ) : null;
            case 'NOW':
                return $text ? null : 'LOCALTIMESTAMP';
            case 'CURDATE':
                return $text ? null : 'CURRENT_DATE';
            case 'UNIX_TIMESTAMP':
                return self::unixTimestamp( $text ? $text[0] : null );
        }
        return null;
    }

    /**
     * The index of the next token that is not white space or a comment, or null.
     */
    private static function nextSignificant( array $tokens, $i )
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
     * Where a trailing FOR UPDATE [OF ...] [WITH LOCK] starts (token index), or null.
     *
     * @param array[] $tokens
     * @return int|null
     */
    public static function trailingForUpdate( array $tokens )
    {
        $outer = self::outerWords( $tokens );
        for ( $n = count( $outer ) - 1; $n > 0; $n-- )
            if ( $outer[$n - 1][1] === 'FOR' && $outer[$n][1] === 'UPDATE' )
                return $outer[$n - 1][0];
        return null;
    }

    /**
     * A trailing MySQL LIMIT clause (LIMIT n, LIMIT n OFFSET m, LIMIT m, n) of a SELECT as OFFSET ... FETCH.
     *
     * @param string $sql
     * @return string
     */
    public static function limitClauseToOffsetFetch( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( $sql );
        $outer = self::outerWords( $tokens );
        $forUpdate = self::trailingForUpdate( $tokens );
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
            return $sql;
        $head = rtrim( expSQLServerLexer::join( array_slice( $tokens, 0, $outer[$limitAt][0] ) ) );
        $tail = $forUpdate === null ? '' : ' ' . trim( expSQLServerLexer::join( array_slice( $tokens, $forUpdate ) ) );
        return $head . "\nOFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY" . $tail;
    }

    /**
     * A trailing FOR UPDATE without WITH LOCK gets it: Firebird locks the rows only then.
     *
     * @param string $sql
     * @return string
     */
    public static function lockingForUpdate( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( $sql );
        if ( self::trailingForUpdate( $tokens ) === null )
            return $sql;
        $outer = self::outerWords( $tokens );
        $count = count( $outer );
        if ( $count >= 2 && $outer[$count - 2][1] === 'WITH' && $outer[$count - 1][1] === 'LOCK' )
            return $sql;
        return rtrim( $sql ) . ' WITH LOCK';
    }

    /**
     * A SELECT without FROM (outside parentheses) reads FROM RDB$DATABASE; a SELECT with a UNION is left alone.
     *
     * @param string $sql
     * @return string
     */
    public static function addOneRowTable( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( $sql );
        foreach ( self::outerWords( $tokens ) as $entry )
            if ( in_array( $entry[1], array( 'FROM', 'UNION', 'INTO', 'EXCEPT', 'INTERSECT' ), true ) )
                return $sql;
        $outer = self::outerWords( $tokens );
        foreach ( $outer as $entry )
            if ( in_array( $entry[1], array( 'WHERE', 'ORDER', 'GROUP', 'OFFSET', 'FETCH', 'ROWS', 'FOR' ), true ) )
                return rtrim( expSQLServerLexer::join( array_slice( $tokens, 0, $entry[0] ) ) ) . ' FROM ' . self::ONE_ROW_TABLE . ' '
                     . ltrim( expSQLServerLexer::join( array_slice( $tokens, $entry[0] ) ) );
        return rtrim( $sql ) . ' FROM ' . self::ONE_ROW_TABLE;
    }

    /**
     * A SELECT limited to $limit rows from $offset on: OFFSET n ROWS FETCH NEXT m ROWS ONLY, in front of a trailing
     * FOR UPDATE. A statement with paging of its own is wrapped. Null for a limit of 0 (no rows: send nothing); the
     * statement as it is without limit and offset, and for anything but a SELECT.
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
        $kind = self::statementKind( $sql );
        if ( $kind !== 'SELECT' && $kind !== 'WITH' )
            return $sql;
        $paging = "OFFSET $offset ROWS" . ( $limit !== false ? " FETCH NEXT $limit ROWS ONLY" : '' );
        $tokens = expSQLServerLexer::tokenize( $sql );
        $forUpdate = self::trailingForUpdate( $tokens );
        $tail = '';
        if ( $forUpdate !== null )
        {
            $tail = ' ' . trim( expSQLServerLexer::join( array_slice( $tokens, $forUpdate ) ) );
            $tokens = array_slice( $tokens, 0, $forUpdate );
        }
        $body = rtrim( expSQLServerLexer::join( $tokens ) );
        foreach ( self::outerWords( $tokens ) as $entry )
            if ( in_array( $entry[1], array( 'OFFSET', 'FETCH', 'ROWS', 'LIMIT', 'FIRST', 'SKIP' ), true ) )
                return "SELECT * FROM (\n$body\n) exp_page\n$paging" . $tail;
        return "$body\n$paging" . $tail;
    }

    /**
     * A SELECT that locks the rows it reads until the end of the transaction.
     *
     * @param string $sql a SELECT without FOR UPDATE
     * @return string
     */
    public static function forUpdate( $sql )
    {
        return rtrim( (string)$sql, " \t\r\n;" ) . ' FOR UPDATE WITH LOCK';
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
        $q = array( __CLASS__, 'quoteIdentifier' );
        $source = array();
        foreach ( $row as $column => $value )
            $source[] = self::literal( $value ) . ' AS ' . $q( $column );
        $on = array();
        foreach ( $keyColumns as $key )
            $on[] = 't.' . $q( $key ) . ' = s.' . $q( $key );
        $sql = 'MERGE INTO ' . self::quoteName( $table ) . ' t USING (SELECT ' . implode( ', ', $source ) . ' FROM ' . self::ONE_ROW_TABLE
             . ') s ON (' . implode( ' AND ', $on ) . ')';
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
     * MySQL's GROUP_CONCAT: Firebird's LIST(). LIST() has no ORDER BY: $orderBy is not applied (order the rows in a
     * derived table when it matters).
     *
     * @param string $expression
     * @param string $separator
     * @param string|null $orderBy
     * @return string
     */
    public static function groupConcat( $expression, $separator = ',', $orderBy = null )
    {
        return "LIST($expression, " . self::stringLiteral( (string)$separator ) . ')';
    }

    /**
     * Seconds since 1970-01-01 UTC, of now (the server's UTC clock) or of a timestamp expression taken as UTC.
     *
     * @param string|null $expression
     * @return string
     */
    public static function unixTimestamp( $expression = null )
    {
        $value = $expression === null
            ? "CAST(CURRENT_TIMESTAMP AT TIME ZONE 'UTC' AS TIMESTAMP WITHOUT TIME ZONE)"
            : "CAST($expression AS TIMESTAMP)";
        return "DATEDIFF(SECOND FROM TIMESTAMP '1970-01-01 00:00:00' TO $value)";
    }

    /**
     * A timestamp (UTC) of seconds since 1970.
     *
     * @param string $expression
     * @return string
     */
    public static function fromUnixTimestamp( $expression )
    {
        return "DATEADD(SECOND, $expression, TIMESTAMP '1970-01-01 00:00:00')";
    }

    /**
     * @param string $string
     * @param string|int $from 1-based
     * @param string|int|null $length
     * @return string
     */
    public static function subString( $string, $from, $length = null )
    {
        return "SUBSTRING($string FROM $from" . ( $length !== null && $length !== '' ? " FOR $length" : '' ) . ')';
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
     * The MD5 of a text expression as 32 lower-case hex digits (Firebird 4's CRYPT_HASH()).
     *
     * @param string $expression
     * @return string
     */
    public static function md5( $expression )
    {
        return "LOWER(HEX_ENCODE(CRYPT_HASH($expression USING MD5)))";
    }

    public static function bitAnd( $a, $b )
    {
        return "BIN_AND($a, $b)";
    }

    public static function bitOr( $a, $b )
    {
        return "BIN_OR($a, $b)";
    }

    public static function charLength( $expression )
    {
        return "CHAR_LENGTH($expression)";
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
                $values[] = self::literal( (float)$element );
            else if ( $type === 'string' )
                $values[] = self::stringLiteral( (string)$element );
            else
                $values[] = (string)$element;
        }
        if ( $unique )
            $values = array_values( array_unique( $values ) );
        if ( !$values )
            return $not ? '1 = 1' : '1 = 0';
        $chunk = max( 1, min( (int)$chunk, self::MAX_IN_LIST ) );
        $parts = array();
        foreach ( array_chunk( $values, $chunk ) as $part )
            $parts[] = $column . ( $not ? ' NOT IN ( ' : ' IN ( ' ) . implode( ', ', $part ) . ' )';
        if ( count( $parts ) === 1 )
            return $parts[0];
        return '( ' . implode( $not ? ' AND ' : ' OR ', $parts ) . ' )';
    }

    /**
     * String literals longer than $threshold bytes as "?" parameters: Firebird refuses a literal of more than 64 KB
     * (and a statement of more than 10 MB), while a parameter takes a text of any length into a BLOB. Null when
     * there is none, or when the statement has "?" of its own.
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
     * A kernel CREATE TEMPORARY TABLE as a global temporary table of Firebird (its rows belong to the connection
     * that wrote them and stay until it ends or deletes them): array( 'name' => table, 'sql' => CREATE GLOBAL
     * TEMPORARY TABLE ... ON COMMIT PRESERVE ROWS ), or null when $sql is no such statement.
     *
     * @param string $sql
     * @return array|null
     */
    public static function temporaryTable( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( self::backticksToQuotes( trim( (string)$sql ) ) );
        $significant = expSQLServerLexer::significant( $tokens );
        $words = array();
        foreach ( array_slice( $significant, 0, 5 ) as $entry )
            $words[] = expSQLServerLexer::keyword( $tokens[$entry[0]] );
        if ( count( $words ) < 4 || $words[0] !== 'CREATE' )
            return null;
        $n = 1;
        if ( $words[1] === 'GLOBAL' )
            $n++;
        if ( $words[$n] !== 'TEMPORARY' || $words[$n + 1] !== 'TABLE' )
            return null;
        $nameAt = $significant[$n + 2][0];
        $name = expSQLServerLexer::identifierValue( $tokens[$nameAt] );
        if ( !isset( $significant[$n + 3] ) || $tokens[$significant[$n + 3][0]]['text'] !== '(' )
            return null;
        $open = $significant[$n + 3][0];
        $close = self::matchingParen( $tokens, $open );
        if ( $close === null )
            return null;
        $columns = array();
        foreach ( self::splitArguments( array_slice( $tokens, $open + 1, $close - $open - 1 ) ) as $column )
            $columns[] = self::columnConstraintsLast( trim( expSQLServerLexer::join( $column ) ) );
        return array( 'name' => $name,
                      'sql' => 'CREATE GLOBAL TEMPORARY TABLE ' . self::quoteIdentifierIfNeeded( $name ) . ' ( ' . implode( ', ', $columns ) . ' ) ON COMMIT PRESERVE ROWS' );
    }

    /**
     * A column definition of MySQL's free word order (int primary key not null) in Firebird's (INTEGER NOT NULL
     * PRIMARY KEY).
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
        $text = preg_replace( '/\bINTEGER\(\d+\)/i', 'INTEGER', $text );
        // the column's name: quoted when it is a reserved word (count)
        if ( preg_match( '/^([A-Za-z_][A-Za-z0-9_$]*)(\s.*)?$/s', $text, $m ) )
            $text = self::quoteIdentifierIfNeeded( $m[1] ) . ( isset( $m[2] ) ? $m[2] : '' );
        return $text . ( $notNull || $primary ? ' NOT NULL' : '' ) . ( $primary ? ' PRIMARY KEY' : '' );
    }

    /**
     * The SQL name of a column type of the catalogue (RDB$FIELDS.RDB$FIELD_TYPE, sub type and scale).
     *
     * @param int|string $code
     * @param int|string|null $subType
     * @param int|string|null $scale
     * @return string
     */
    public static function typeName( $code, $subType = 0, $scale = 0 )
    {
        $code = (int)$code;
        if ( in_array( $code, array( 7, 8, 16, 26 ), true ) && ( (int)$subType > 0 || (int)$scale < 0 ) )
            return (int)$subType === 1 ? 'numeric' : 'decimal';
        $names = array( 7 => 'smallint', 8 => 'integer', 16 => 'bigint', 26 => 'int128', 10 => 'float', 27 => 'double precision',
                        37 => 'varchar', 14 => 'char', 35 => 'timestamp', 29 => 'timestamp with time zone', 12 => 'date', 13 => 'time',
                        28 => 'time with time zone', 23 => 'boolean', 24 => 'decfloat(16)', 25 => 'decfloat(34)' );
        if ( $code === 261 )
            return (int)$subType === 1 ? 'blob sub_type text' : 'blob';
        return isset( $names[$code] ) ? $names[$code] : 'unknown';
    }

    // ------------------------------------------------------------------ errors

    /**
     * The kind of an error, from Firebird's SQLCODE, its GDS code (when the layer gives it) and the message:
     * deadlock (an update conflict or a deadlock: a single statement may be sent again), lock (a lock conflict of
     * a NO WAIT transaction or a lock timeout), duplicate (a primary or unique key), connection (the connection is
     * gone), syntax, unknown_object, or other.
     *
     * @param int|null $sqlCode
     * @param string $message
     * @param int|null $gdsCode
     * @return string
     */
    public static function errorCategory( $sqlCode, $message = '', $gdsCode = null )
    {
        $message = strtolower( (string)$message );
        $gds = (int)$gdsCode;
        if ( $gds === 335544336 || $gds === 335544451 )
            return 'deadlock';
        if ( in_array( $gds, array( 335544721, 335544726, 335544741, 335544648, 335544856, 335545106 ), true )
             || strpos( $message, 'connection shutdown' ) !== false || strpos( $message, 'connection lost' ) !== false
             || strpos( $message, 'error reading data from the connection' ) !== false
             || strpos( $message, 'error writing data to the connection' ) !== false
             || strpos( $message, 'connection rejected' ) !== false || strpos( $message, 'the connection is closed' ) !== false || strpos( $message, 'unable to complete network request' ) !== false )
            return 'connection';
        if ( (int)$sqlCode === -913 || strpos( $message, 'deadlock' ) !== false || strpos( $message, 'update conflicts with concurrent update' ) !== false )
            return 'deadlock';
        if ( strpos( $message, 'lock conflict' ) !== false || strpos( $message, 'lock time-out' ) !== false || $gds === 335544345 )
            return 'lock';
        if ( (int)$sqlCode === -803 || in_array( $gds, array( 335544665, 335544349 ), true ) || strpos( $message, 'violation of primary or unique key' ) !== false
             || strpos( $message, 'attempt to store duplicate value' ) !== false )
            return 'duplicate';
        if ( (int)$sqlCode === -104 )
            return 'syntax';
        if ( (int)$sqlCode === -204 || (int)$sqlCode === -206 )
            return 'unknown_object';
        return 'other';
    }

    // ------------------------------------------------------------------ connection

    /**
     * The data source name of pdo_firebird: firebird:dbname=host/port:database;charset=UTF8[;role=...]. The database
     * is a path on the server or an alias of its databases.conf; with no host it is opened by the embedded engine
     * or the local server.
     *
     * @param array $config host, port, database, charset, role
     * @return string
     */
    public static function pdoDSN( array $config )
    {
        $host = isset( $config['host'] ) ? trim( (string)$config['host'] ) : '';
        $port = isset( $config['port'] ) && $config['port'] ? (int)$config['port'] : 0;
        $database = isset( $config['database'] ) ? (string)$config['database'] : '';
        $target = $database;
        if ( $host !== '' && $host !== 'localhost:embedded' )
        {
            if ( strpos( $host, ':' ) !== false && $host[0] !== '[' )
                $host = '[' . $host . ']';
            $target = $host . ( $port && $port !== self::DEFAULT_PORT ? '/' . $port : '' ) . ':' . $database;
        }
        $dsn = 'firebird:dbname=' . self::dsnValue( $target ) . ';charset=' . self::dsnValue( isset( $config['charset'] ) && $config['charset'] ? $config['charset'] : 'UTF8' );
        if ( !empty( $config['role'] ) )
            $dsn .= ';role=' . self::dsnValue( $config['role'] );
        return $dsn;
    }

    /**
     * A value of a DSN: a ";" ends the pair, so a value with one is refused.
     *
     * @param string $value
     * @return string
     * @throws InvalidArgumentException
     */
    public static function dsnValue( $value )
    {
        $value = (string)$value;
        if ( strpos( $value, ';' ) !== false || strpos( $value, "\0" ) !== false )
            throw new InvalidArgumentException( 'A Firebird connection value cannot hold ";"' );
        return $value;
    }

    /**
     * What a server version string says: "LI-V5.0.4.1812 Firebird 5.0" (the first part of PDO's server version, or
     * rdb$get_context('SYSTEM', 'ENGINE_VERSION') "5.0.4").
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
        if ( preg_match( '/V?(\d+)\.(\d+)(?:\.(\d+))?(?:\.(\d+))?/', $version, $m ) )
        {
            $major = (int)$m[1];
            $minor = (int)$m[2];
            $string = $m[0][0] === 'V' ? substr( $m[0], 1 ) : $m[0];
        }
        $interbase = stripos( $version, 'interbase' ) !== false;
        return array( 'major' => $major, 'minor' => $minor, 'string' => $string,
                      'name' => $interbase ? 'InterBase' : 'Firebird ' . $major . '.' . $minor,
                      'supported' => !$interbase && $major >= self::MIN_MAJOR_VERSION );
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
