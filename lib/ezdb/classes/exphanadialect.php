<?php
/**
 * File containing the expHANADialect class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The SAP HANA SQL that expHANADB writes, as pure functions of their arguments: no connection, no settings. Every
 * rule here is tested without a server (tests/tests/lib/ezdb/hana).
 *
 * Supported servers: SAP HANA 2.0 SPS 05 and later (HANA express edition included) and SAP HANA Cloud. Statements are
 * read by expSQLServerLexer, which splits any SQL into tokens without losing a byte; nothing here rewrites a
 * statement with a regular expression over its text, so a keyword inside a string literal or a comment never changes
 * what a statement does.
 *
 * What the kernel writes that HANA reads differently, and what this class makes of it (rewrite()):
 *
 *  - Names: HANA stores an unquoted name in upper case, and a quoted one as written. A MySQL `name` becomes "NAME",
 *    so it is the same table or column as the unquoted name. An alias that is a reserved word of HANA is quoted.
 *  - Functions: CONCAT(a, b, c) (HANA's takes two arguments) becomes (a || b || c), CHAR_LENGTH() LENGTH(), SUBSTR()
 *    SUBSTRING(), MD5(x) the hex text of HASH_MD5(), CURDATE() CURRENT_DATE, UNIX_TIMESTAMP() and FROM_UNIXTIME() the
 *    seconds since 1970 of the UTC clock.
 *  - Bit operators: a & b is BITAND(a, b), a | b BITOR(a, b) (|| stays the concatenation), ~a BITNOT(a); HANA has no
 *    shift, so a >> n is TO_BIGINT(FLOOR((a) / POWER(2, n))) and a << n TO_BIGINT((a) * POWER(2, n)).
 *  - LIKE: MySQL and PostgreSQL take a backslash as the escape character of a pattern; HANA has none unless the
 *    statement names one. A pattern with a backslash gets ESCAPE '\'.
 *  - LIMIT m, n (MySQL's offset first) becomes LIMIT n OFFSET m; LIMIT n [OFFSET m] is HANA's own.
 *  - SELECT without FROM: FROM DUMMY (HANA's one-row table).
 *  - INSERT ... VALUES (...), (...): HANA takes one row per VALUES, so the rows become statements of their own
 *    (splitMultiRowInsert()).
 *  - TRUNCATE TABLE t: DELETE FROM t (HANA's TRUNCATE commits the transaction it runs in).
 *  - Text columns: an NCLOB (the .dba's text) cannot be compared, sorted or grouped in HANA. A column that is an
 *    NCLOB in every table that has it (the driver passes their names) is read as TO_NVARCHAR(column) where it is
 *    compared (=, <>, <, >, IN, BETWEEN) or is an item of ORDER BY or GROUP BY (wrapLobColumns()).
 */
class expHANADialect
{
    /** Longest name HANA accepts, in characters */
    const MAX_IDENTIFIER_LENGTH = 127;

    /** Values per IN list the driver writes (HANA takes far more; the statement stays readable) */
    const MAX_IN_LIST = 5000;

    /** The longest NVARCHAR; longer text is NCLOB */
    const MAX_NVARCHAR_LENGTH = 5000;

    /** Lowest supported major version (HANA 2.0; HANA Cloud reports 4) */
    const MIN_MAJOR_VERSION = 2;

    /** The SQL port of the first tenant database of an instance 90 (HANA express: HXE) */
    const DEFAULT_PORT = 39041;

    /** HANA's one-row table, for a SELECT of expressions only */
    const ONE_ROW_TABLE = 'DUMMY';

    /** The ODBC driver's name in odbcinst.ini of SAP's client installer */
    const DEFAULT_DRIVER = 'HDBODBC';

    /**
     * The reserved words of SAP HANA (SQL Reference, "Reserved Words"): a table, column or alias of one of these
     * names must be quoted.
     *
     * @var string[]
     */
    const RESERVED_WORDS = array(
        'ALL', 'ALTER', 'AS', 'BEFORE', 'BEGIN', 'BOTH', 'CASE', 'CHAR', 'CONDITION', 'CONNECT', 'CROSS', 'CUBE',
        'CURRENT_CONNECTION', 'CURRENT_DATE', 'CURRENT_SCHEMA', 'CURRENT_TIME', 'CURRENT_TIMESTAMP',
        'CURRENT_TRANSACTION_ISOLATION_LEVEL', 'CURRENT_USER', 'CURRENT_UTCDATE', 'CURRENT_UTCTIME', 'CURRENT_UTCTIMESTAMP',
        'CURRVAL', 'CURSOR', 'DECLARE', 'DISTINCT', 'ELSE', 'ELSEIF', 'END', 'EXCEPT', 'EXCEPTION', 'EXEC', 'FALSE', 'FOR',
        'FROM', 'FULL', 'GROUP', 'HAVING', 'IF', 'IN', 'INNER', 'INOUT', 'INTERSECT', 'INTO', 'IS', 'JOIN', 'LATERAL',
        'LEADING', 'LEFT', 'LIMIT', 'LOOP', 'MINUS', 'NATURAL', 'NCHAR', 'NEXTVAL', 'NULL', 'ON', 'ORDER', 'OUT', 'PRIOR',
        'RETURN', 'RETURNS', 'REVERSE', 'RIGHT', 'ROLLUP', 'ROWID', 'SELECT', 'SESSION_USER', 'SET', 'SQL', 'START',
        'SYSUUID', 'TABLESAMPLE', 'TOP', 'TRAILING', 'TRUE', 'UNION', 'UNKNOWN', 'USING', 'UTCTIMESTAMP', 'VALUES', 'WHEN',
        'WHERE', 'WHILE', 'WITH',
    );

    /** MySQL/PostgreSQL functions that only have another name here */
    const RENAMED_FUNCTIONS = array( 'CHAR_LENGTH' => 'LENGTH', 'CHARACTER_LENGTH' => 'LENGTH', 'SUBSTR' => 'SUBSTRING' );

    /** Words that start a type (after AS in a CAST) */
    const TYPE_WORDS = array( 'BIGINT', 'BLOB', 'BOOLEAN', 'CHAR', 'CLOB', 'DATE', 'DECIMAL', 'DOUBLE', 'FLOAT', 'INT', 'INTEGER',
                              'NCHAR', 'NCLOB', 'NVARCHAR', 'REAL', 'SECONDDATE', 'SMALLDECIMAL', 'SMALLINT', 'TIME', 'TIMESTAMP',
                              'TINYINT', 'VARBINARY', 'VARCHAR', 'ALPHANUM', 'SHORTTEXT', 'TEXT' );

    /** @var array|null RESERVED_WORDS as keys */
    private static $reserved = null;

    // ------------------------------------------------------------------ names

    /**
     * Whether $word is a reserved word of HANA (any case).
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
     * One name in double quotes, in upper case (the way HANA stores the same name unquoted), with a '"' inside
     * doubled. An empty name, a name with a NUL byte and a name of more than 127 characters are refused.
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
        if ( !self::isReservedWord( $name ) && preg_match( '/^[A-Za-z_][A-Za-z0-9_#$]*$/', $name ) )
            return $name;
        return self::quoteIdentifier( $name );
    }

    /**
     * @param string $name
     * @throws InvalidArgumentException when $name is no name HANA takes
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
     * A name in upper case as HANA folds an unquoted one (ASCII letters only).
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
     * Text for inside a string literal: a quote doubled. A backslash is an ordinary character in HANA.
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
        return in_array( self::statementKind( $sql ), array( 'CREATE', 'ALTER', 'DROP', 'RENAME', 'COMMENT' ), true );
    }

    /**
     * A statement of the kernel (MySQL words, as far as they reach the driver) in HANA's words; see the class comment
     * for the rules. Statements of the schema (CREATE, ALTER, DROP ...) keep their function names: they come from the
     * schema handler in HANA's words already, and a trigger body is no place for rewriting.
     *
     * @param string $sql
     * @return string
     */
    public static function rewrite( $sql, array $lobColumns = array() )
    {
        $sql = trim( (string)$sql );
        while ( substr( $sql, -1 ) === ';' )
            $sql = rtrim( substr( $sql, 0, -1 ) );
        $kind = self::statementKind( $sql );
        if ( in_array( $kind, array( 'CREATE', 'ALTER', 'DROP', 'RENAME', 'COMMENT', 'CALL', 'DO', 'SET', 'GRANT', 'REVOKE' ), true ) )
            return self::backticksToQuotes( $sql );
        if ( $kind === 'TRUNCATE' )
            return self::truncateToDelete( $sql );
        $sql = self::bitOperators( $sql );
        $sql = self::rewriteTokens( expSQLServerLexer::tokenize( $sql ) );
        if ( $lobColumns )
            $sql = self::wrapLobColumns( $sql, $lobColumns );
        if ( $kind === 'SELECT' || $kind === 'WITH' )
        {
            $sql = self::limitClause( $sql );
            if ( $kind === 'SELECT' )
                $sql = self::addOneRowTable( $sql );
        }
        return $sql;
    }

    /**
     * DROP <type> IF EXISTS name and CREATE <type> IF NOT EXISTS name (HANA 2.0 has neither): array( 'exists' => true
     * for IF NOT EXISTS (run when the object is missing), false for IF EXISTS (run when it is there), 'type' => TABLE,
     * INDEX, SEQUENCE, VIEW or TRIGGER, 'name' => the name as HANA stores it, 'sql' => the statement without the
     * condition ), or null for any other statement.
     *
     * @param string $sql
     * @return array|null
     */
    public static function conditionalDDL( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( trim( (string)$sql ) );
        $significant = expSQLServerLexer::significant( $tokens );
        $words = array();
        foreach ( array_slice( $significant, 0, 7 ) as $entry )
            $words[] = expSQLServerLexer::keyword( $tokens[$entry[0]] );
        if ( count( $words ) < 4 || !in_array( $words[0], array( 'CREATE', 'DROP' ), true ) )
            return null;
        $n = 1;
        while ( $words[0] === 'CREATE' && isset( $words[$n] ) && in_array( $words[$n], array( 'UNIQUE', 'COLUMN', 'ROW', 'GLOBAL', 'LOCAL', 'TEMPORARY' ), true ) )
            $n++;
        $type = isset( $words[$n] ) ? $words[$n] : '';
        if ( !in_array( $type, array( 'TABLE', 'INDEX', 'SEQUENCE', 'VIEW', 'TRIGGER' ), true ) )
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
        return array( 'exists' => $words[0] === 'CREATE', 'type' => $type, 'name' => $name, 'sql' => self::backticksToQuotes( $rest ) );
    }

    /**
     * The table a DROP TABLE statement drops (its name in lower case, as the schema handler names its sequence after
     * it), or null.
     *
     * @param string $sql
     * @return string|null
     */
    public static function droppedTable( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( trim( (string)$sql ) );
        $significant = expSQLServerLexer::significant( $tokens );
        if ( count( $significant ) < 3 || expSQLServerLexer::keyword( $tokens[$significant[0][0]] ) !== 'DROP'
             || expSQLServerLexer::keyword( $tokens[$significant[1][0]] ) !== 'TABLE' )
            return null;
        if ( count( $significant ) > 3 && !( count( $significant ) === 4 && in_array( expSQLServerLexer::keyword( $tokens[$significant[3][0]] ), array( 'CASCADE', 'RESTRICT' ), true ) ) )
            return null;
        return strtolower( expSQLServerLexer::identifierValue( $tokens[$significant[2][0]] ) );
    }

    /**
     * The table an INSERT writes to (lower case), or null.
     *
     * @param string $sql
     * @return string|null
     */
    public static function insertTable( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( trim( (string)$sql ) );
        $significant = expSQLServerLexer::significant( $tokens );
        if ( count( $significant ) < 3 || !in_array( expSQLServerLexer::keyword( $tokens[$significant[0][0]] ), array( 'INSERT', 'UPSERT', 'REPLACE' ), true ) )
            return null;
        $n = 1;
        if ( expSQLServerLexer::keyword( $tokens[$significant[1][0]] ) === 'INTO' )
            $n++;
        if ( !isset( $significant[$n] ) )
            return null;
        $token = $tokens[$significant[$n][0]];
        if ( !in_array( $token['type'], array( expSQLServerLexer::WORD, expSQLServerLexer::IDENT ), true ) )
            return null;
        // schema.table: the table
        if ( isset( $significant[$n + 2] ) && $tokens[$significant[$n + 1][0]]['text'] === '.' )
            $token = $tokens[$significant[$n + 2][0]];
        return strtolower( expSQLServerLexer::identifierValue( $token ) );
    }

    /**
     * The catalogue query that finds an object of a type by its stored name in the current schema (one row when it
     * exists).
     *
     * @param string $type TABLE, INDEX, SEQUENCE, VIEW or TRIGGER
     * @param string $name
     * @return string
     */
    public static function existsQuery( $type, $name )
    {
        $literal = self::stringLiteral( $name );
        switch ( $type )
        {
            case 'INDEX':
                return "SELECT 1 AS found FROM SYS.INDEXES WHERE SCHEMA_NAME = CURRENT_SCHEMA AND INDEX_NAME = $literal";
            case 'SEQUENCE':
                return "SELECT 1 AS found FROM SYS.SEQUENCES WHERE SCHEMA_NAME = CURRENT_SCHEMA AND SEQUENCE_NAME = $literal";
            case 'TRIGGER':
                return "SELECT 1 AS found FROM SYS.TRIGGERS WHERE SCHEMA_NAME = CURRENT_SCHEMA AND TRIGGER_NAME = $literal";
            case 'VIEW':
                return "SELECT 1 AS found FROM SYS.VIEWS WHERE SCHEMA_NAME = CURRENT_SCHEMA AND VIEW_NAME = $literal";
        }
        return "SELECT 1 AS found FROM SYS.TABLES WHERE SCHEMA_NAME = CURRENT_SCHEMA AND TABLE_NAME = $literal";
    }

    /**
     * The bit operators of MySQL and PostgreSQL as HANA's functions: a & b as BITAND(a, b), a | b as BITOR(a, b) (||
     * stays the concatenation), ~a as BITNOT(a). An operand is a name (qualified or not), a number, a "?", a call or a
     * parenthesized expression, as in the kernel's language mask conditions (ezcontentobject.language_mask & 6 > 0);
     * & and | bind tighter than a comparison there, as in MySQL.
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
                // >> and << (two adjacent tokens): HANA has no shift, so a division or a product by a power of two
                if ( ( $t['text'] === '>' || $t['text'] === '<' ) && isset( $tokens[$i + 1] ) && $tokens[$i + 1]['text'] === $t['text'] )
                {
                    $right = self::operandAfter( $tokens, $i + 1 );
                    $left = $right === null ? null : self::operandBefore( $tokens, $i );
                    if ( $left === null )
                    {
                        $i++;
                        continue;
                    }
                    $a = trim( expSQLServerLexer::join( array_slice( $tokens, $left, $i - $left ) ) );
                    $b = trim( expSQLServerLexer::join( array_slice( $tokens, $i + 2, $right - $i - 1 ) ) );
                    $sql = expSQLServerLexer::join( array_slice( $tokens, 0, $left ) )
                         . ( $t['text'] === '>' ? "TO_BIGINT(FLOOR(($a) / POWER(2, $b)))" : "TO_BIGINT(($a) * POWER(2, $b))" )
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
                    $sql = expSQLServerLexer::join( array_slice( $tokens, 0, $i ) ) . 'BITNOT(' . trim( expSQLServerLexer::join( array_slice( $tokens, $i + 1, $right - $i ) ) ) . ')'
                         . expSQLServerLexer::join( array_slice( $tokens, $right + 1 ) );
                    $done = false;
                    break;
                }
                $left = self::operandBefore( $tokens, $i );
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
            return self::operandAfter( $tokens, $j );
        if ( $tokens[$j]['text'] === '(' )
            return self::matchingParen( $tokens, $j );
        if ( !in_array( $tokens[$j]['type'], array( expSQLServerLexer::WORD, expSQLServerLexer::IDENT, expSQLServerLexer::NUMBER, expSQLServerLexer::PARAM ), true ) )
            return null;
        $end = $j;
        while ( $end + 2 < $count && $tokens[$end + 1]['text'] === '.'
                && in_array( $tokens[$end + 2]['type'], array( expSQLServerLexer::WORD, expSQLServerLexer::IDENT ), true ) )
            $end += 2;
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
     * References to the columns $lobColumns (lower-case names) as TO_NVARCHAR(column) where HANA cannot read an NCLOB:
     * next to a comparison operator (=, <, >, !=, <>, IN, NOT IN, BETWEEN) and as an item of ORDER BY or GROUP BY. The
     * columns an UPDATE sets, IS [NOT] NULL and LIKE (which HANA does on an NCLOB) are left alone.
     *
     * @param string $sql
     * @param string[] $lobColumns
     * @return string
     */
    public static function wrapLobColumns( $sql, array $lobColumns )
    {
        $lob = array_fill_keys( array_map( 'strtolower', $lobColumns ), true );
        $tokens = expSQLServerLexer::tokenize( (string)$sql );
        $count = count( $tokens );
        $kind = self::statementKind( $sql );
        $significant = array();
        $depthAt = array();
        $depth = 0;
        foreach ( $tokens as $i => $token )
        {
            if ( $token['type'] === expSQLServerLexer::PUNCT && $token['text'] === ')' )
                $depth--;
            if ( !expSQLServerLexer::isNoise( $token ) )
            {
                $significant[] = $i;
                $depthAt[$i] = $depth;
            }
            if ( $token['type'] === expSQLServerLexer::PUNCT && $token['text'] === '(' )
                $depth++;
        }
        $comparison = array( '=', '<', '>', '!' );
        $itemEnds = array( ',', ')', 'ASC', 'DESC', 'LIMIT', 'HAVING', 'UNION', 'FOR', 'NULLS', 'OFFSET', 'ORDER', 'EXCEPT', 'INTERSECT', 'MINUS', '' );
        // the clause at each depth: BY (an ORDER BY or GROUP BY list), SET (an UPDATE's assignments), or ''
        $clause = array();
        $wrap = array();
        $n = count( $significant );
        for ( $k = 0; $k < $n; $k++ )
        {
            $i = $significant[$k];
            $token = $tokens[$i];
            $d = $depthAt[$i];
            $word = expSQLServerLexer::keyword( $token );
            if ( $token['type'] === expSQLServerLexer::WORD )
            {
                $prevWord = $k > 0 ? expSQLServerLexer::keyword( $tokens[$significant[$k - 1]] ) : '';
                if ( $word === 'BY' && in_array( $prevWord, array( 'ORDER', 'GROUP' ), true ) )
                    $clause[$d] = 'BY';
                else if ( $word === 'SET' && in_array( $kind, array( 'UPDATE', 'MERGE' ), true ) )
                    $clause[$d] = 'SET';
                else if ( in_array( $word, array( 'WHERE', 'FROM', 'HAVING', 'LIMIT', 'UNION', 'SELECT', 'ON', 'FOR', 'OFFSET', 'WHEN' ), true ) )
                    $clause[$d] = '';
            }
            if ( !in_array( $token['type'], array( expSQLServerLexer::WORD, expSQLServerLexer::IDENT ), true ) )
                continue;
            // the last part of a dotted name, not a call and not a name followed by a dot
            $nextAt = isset( $significant[$k + 1] ) ? $significant[$k + 1] : null;
            $nextText = $nextAt === null ? '' : $tokens[$nextAt]['text'];
            if ( $nextText === '.' || $nextText === '(' )
                continue;
            if ( !isset( $lob[strtolower( expSQLServerLexer::identifierValue( $token ) )] ) )
                continue;
            $first = $k;
            while ( $first >= 2 && $tokens[$significant[$first - 1]]['text'] === '.'
                    && in_array( $tokens[$significant[$first - 2]]['type'], array( expSQLServerLexer::WORD, expSQLServerLexer::IDENT ), true ) )
                $first -= 2;
            $prevText = $first > 0 ? $tokens[$significant[$first - 1]]['text'] : '';
            $prevKey = $first > 0 ? self::wordOrText( $tokens[$significant[$first - 1]] ) : '';
            $nextKey = $nextAt === null ? '' : self::wordOrText( $tokens[$nextAt] );
            $afterNext = isset( $significant[$k + 2] ) ? self::wordOrText( $tokens[$significant[$k + 2]] ) : '';
            $inClause = isset( $clause[$d] ) ? $clause[$d] : '';
            // an assignment of an UPDATE or a MERGE: SET col = value, col = value (neither side is compared)
            if ( $inClause === 'SET' && ( ( in_array( $prevKey, array( 'SET', ',' ), true ) && $nextText === '=' ) || ( $prevText === '=' && in_array( $nextKey, array( ',', 'WHERE', 'WHEN', '' ), true ) ) ) )
                continue;
            $compared = in_array( $nextText, $comparison, true ) || in_array( $prevText, $comparison, true )
                     || in_array( $nextKey, array( 'IN', 'BETWEEN' ), true ) || ( $nextKey === 'NOT' && in_array( $afterNext, array( 'IN', 'BETWEEN' ), true ) )
                     || ( in_array( $prevKey, array( 'BETWEEN', 'AND' ), true ) && $inClause !== 'BY' && self::betweenOperand( $tokens, $significant, $first ) );
            $item = $inClause === 'BY' && in_array( $prevKey, array( 'BY', ',' ), true ) && in_array( $nextKey, $itemEnds, true );
            if ( $compared || $item )
                $wrap[$significant[$first]] = $i;
        }
        if ( !$wrap )
            return (string)$sql;
        $out = '';
        for ( $i = 0; $i < $count; $i++ )
        {
            if ( isset( $wrap[$i] ) )
            {
                $out .= 'TO_NVARCHAR(' . expSQLServerLexer::join( array_slice( $tokens, $i, $wrap[$i] - $i + 1 ) ) . ')';
                $i = $wrap[$i];
                continue;
            }
            $out .= $tokens[$i]['text'];
        }
        return $out;
    }

    /**
     * The upper-case word of a word token, else its text.
     */
    private static function wordOrText( array $token )
    {
        return $token['type'] === expSQLServerLexer::WORD ? strtoupper( $token['text'] ) : $token['text'];
    }

    /**
     * A SELECT DISTINCT whose select list may hold an NCLOB (a * or a column of $lobColumns) without its DISTINCT:
     * HANA refuses DISTINCT over an NCLOB, so the caller drops the duplicate rows itself. Null for any other statement.
     *
     * @param string $sql
     * @param string[] $lobColumns
     * @return string|null
     */
    public static function withoutLobDistinct( $sql, array $lobColumns )
    {
        if ( !$lobColumns || stripos( (string)$sql, 'distinct' ) === false )
            return null;
        $lob = array_fill_keys( array_map( 'strtolower', $lobColumns ), true );
        $tokens = expSQLServerLexer::tokenize( (string)$sql );
        $outer = self::outerWords( $tokens );
        if ( count( $outer ) < 3 || $outer[0][1] !== 'SELECT' || $outer[1][1] !== 'DISTINCT' )
            return null;
        $distinctAt = $outer[1][0];
        $fromAt = null;
        foreach ( $outer as $entry )
            if ( $entry[1] === 'FROM' )
            {
                $fromAt = $entry[0];
                break;
            }
        if ( $fromAt === null )
            return null;
        $hasLob = false;
        for ( $i = $distinctAt + 1; $i < $fromAt; $i++ )
        {
            $token = $tokens[$i];
            if ( ( $token['type'] === expSQLServerLexer::PUNCT && $token['text'] === '*' )
                 || ( in_array( $token['type'], array( expSQLServerLexer::WORD, expSQLServerLexer::IDENT ), true ) && isset( $lob[strtolower( expSQLServerLexer::identifierValue( $token ) )] ) ) )
            {
                $hasLob = true;
                break;
            }
        }
        if ( !$hasLob )
            return null;
        $end = $distinctAt + 1;
        if ( isset( $tokens[$end] ) && $tokens[$end]['type'] === expSQLServerLexer::WS )
            $end++;
        return expSQLServerLexer::join( array_slice( $tokens, 0, $distinctAt ) ) . expSQLServerLexer::join( array_slice( $tokens, $end ) );
    }

    /**
     * Whether the name at significant position $first is an operand of a BETWEEN (x BETWEEN col AND ..., or the
     * bound after AND).
     */
    private static function betweenOperand( array $tokens, array $significant, $first )
    {
        for ( $k = $first - 1, $steps = 0; $k >= 0 && $steps < 8; $k--, $steps++ )
        {
            $word = expSQLServerLexer::keyword( $tokens[$significant[$k]] );
            if ( $word === 'BETWEEN' )
                return true;
            if ( in_array( $word, array( 'WHERE', 'OR', 'ON', 'SELECT', 'SET', 'WHEN', 'THEN' ), true ) )
                return false;
        }
        return false;
    }

    /**
     * The token-level rules of rewrite(): names, functions, LIKE patterns.
     *
     * @param array[] $tokens
     * @return string
     */
    protected static function rewriteTokens( array $tokens )
    {
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
                $text = '"' . str_replace( '"', '""', self::upper( expSQLServerLexer::identifierValue( $token ) ) ) . '"';
            else if ( $token['type'] === expSQLServerLexer::WORD )
            {
                $word = strtoupper( $token['text'] );
                $text = $token['text'];
                $isCall = $nextText === '(' && $prevText !== '.';
                if ( $isCall && isset( self::RENAMED_FUNCTIONS[$word] ) )
                    $text = self::RENAMED_FUNCTIONS[$word];
                else if ( $isCall && in_array( $word, array( 'CONCAT', 'MD5', 'UNIX_TIMESTAMP', 'FROM_UNIXTIME', 'CURDATE' ), true ) )
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
                // an alias that is a reserved word (AS order, AS top), not the type of a CAST
                else if ( !$isCall && $prevWord === 'AS' && self::isReservedWord( $word ) && !self::isTypeAfterAs( $word, $nextToken, $nextWord ) )
                    $text = '"' . $word . '"';
            }
            else if ( $token['type'] === expSQLServerLexer::STRING && $prevWord === 'LIKE' && strpos( $token['text'], '\\' ) !== false
                      && $nextWord !== 'ESCAPE' )
                $text = $token['text'] . " ESCAPE '\\'";
            else
                $text = $token['text'];
            $out .= $text;
            $prevWord = expSQLServerLexer::keyword( $token );
            $prevText = $token['text'];
        }
        return $out;
    }

    /**
     * Whether a word after AS is the type of a CAST (followed by ")", "(", or the end).
     */
    private static function isTypeAfterAs( $word, $nextToken, $nextWord )
    {
        if ( !in_array( $word, self::TYPE_WORDS, true ) )
            return false;
        return $nextToken === null || in_array( $nextToken['text'], array( ')', '(' ), true );
    }

    /**
     * The rewrite of a call of a MySQL function HANA lacks, from its arguments (each a token list); null when the
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
            case 'MD5':
                return count( $text ) === 1 ? self::md5( $text[0] ) : null;
            case 'CURDATE':
                return $text ? null : 'CURRENT_DATE';
            case 'UNIX_TIMESTAMP':
                return self::unixTimestamp( $text ? $text[0] : null );
            case 'FROM_UNIXTIME':
                return count( $text ) === 1 ? self::fromUnixTimestamp( $text[0] ) : null;
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
     * Where a trailing FOR UPDATE starts (token index), or null.
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
     * A trailing MySQL LIMIT m, n of a SELECT as LIMIT n OFFSET m; LIMIT n and LIMIT n OFFSET m stay.
     *
     * @param string $sql
     * @return string
     */
    public static function limitClause( $sql )
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
        if ( count( $parts ) !== 3 || !ctype_digit( $parts[0][2] ) || $parts[1][2] !== ',' || !ctype_digit( $parts[2][2] ) )
            return $sql;
        $head = rtrim( expSQLServerLexer::join( array_slice( $tokens, 0, $outer[$limitAt][0] ) ) );
        $tail = $forUpdate === null ? '' : ' ' . trim( expSQLServerLexer::join( array_slice( $tokens, $forUpdate ) ) );
        return $head . "\nLIMIT " . (int)$parts[2][2] . ' OFFSET ' . (int)$parts[0][2] . $tail;
    }

    /**
     * A SELECT without FROM (outside parentheses) reads FROM DUMMY; a SELECT with a UNION is left alone.
     *
     * @param string $sql
     * @return string
     */
    public static function addOneRowTable( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( $sql );
        $outer = self::outerWords( $tokens );
        foreach ( $outer as $entry )
            if ( in_array( $entry[1], array( 'FROM', 'UNION', 'INTO', 'EXCEPT', 'INTERSECT', 'MINUS' ), true ) )
                return $sql;
        foreach ( $outer as $entry )
            if ( in_array( $entry[1], array( 'WHERE', 'ORDER', 'GROUP', 'LIMIT', 'OFFSET', 'FOR' ), true ) )
                return rtrim( expSQLServerLexer::join( array_slice( $tokens, 0, $entry[0] ) ) ) . ' FROM ' . self::ONE_ROW_TABLE . ' '
                     . ltrim( expSQLServerLexer::join( array_slice( $tokens, $entry[0] ) ) );
        return rtrim( $sql ) . ' FROM ' . self::ONE_ROW_TABLE;
    }

    /**
     * An INSERT of several rows (VALUES (...), (...)) as one INSERT per row; null when $sql is no such statement.
     *
     * @param string $sql
     * @return string[]|null
     */
    public static function splitMultiRowInsert( $sql )
    {
        $tokens = expSQLServerLexer::tokenize( (string)$sql );
        $outer = self::outerWords( $tokens );
        if ( !$outer || !in_array( $outer[0][1], array( 'INSERT', 'UPSERT' ), true ) )
            return null;
        $valuesAt = null;
        foreach ( $outer as $n => $entry )
            if ( $entry[1] === 'VALUES' )
            {
                $valuesAt = $n;
                break;
            }
        if ( $valuesAt === null )
            return null;
        $rows = array();
        $i = $outer[$valuesAt][0] + 1;
        $count = count( $tokens );
        while ( $i < $count )
        {
            while ( $i < $count && expSQLServerLexer::isNoise( $tokens[$i] ) )
                $i++;
            if ( $i >= $count || $tokens[$i]['text'] !== '(' )
                return null;
            $close = self::matchingParen( $tokens, $i );
            if ( $close === null )
                return null;
            $rows[] = expSQLServerLexer::join( array_slice( $tokens, $i, $close - $i + 1 ) );
            $i = $close + 1;
            while ( $i < $count && expSQLServerLexer::isNoise( $tokens[$i] ) )
                $i++;
            if ( $i >= $count )
                break;
            if ( $tokens[$i]['text'] !== ',' )
                return null;
            $i++;
        }
        if ( count( $rows ) < 2 )
            return null;
        $head = expSQLServerLexer::join( array_slice( $tokens, 0, $outer[$valuesAt][0] ) ) . 'VALUES ';
        $out = array();
        foreach ( $rows as $row )
            $out[] = $head . $row;
        return $out;
    }

    /**
     * A SELECT limited to $limit rows from $offset on: LIMIT n OFFSET m, in front of a trailing FOR UPDATE. A
     * statement with paging of its own is wrapped. Null for a limit of 0 (no rows: send nothing); the statement as it
     * is without limit and offset, and for anything but a SELECT.
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
        // HANA has no LIMIT ALL: an offset alone is the largest limit
        $paging = 'LIMIT ' . ( $limit !== false ? $limit : '2147483647' ) . ( $offset ? " OFFSET $offset" : '' );
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
            if ( in_array( $entry[1], array( 'LIMIT', 'OFFSET', 'TOP' ), true ) )
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
        return rtrim( (string)$sql, " \t\r\n;" ) . ' FOR UPDATE';
    }

    /**
     * An insert-or-update of one row: UPSERT ... WITH PRIMARY KEY when the key columns are the primary key, else a
     * MERGE (insert only: a MERGE without WHEN MATCHED).
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
     * MySQL's GROUP_CONCAT: HANA's STRING_AGG(), ordered when asked.
     *
     * @param string $expression
     * @param string $separator
     * @param string|null $orderBy
     * @return string
     */
    public static function groupConcat( $expression, $separator = ',', $orderBy = null )
    {
        $order = ( $orderBy !== null && trim( (string)$orderBy ) !== '' ) ? ' ORDER BY ' . trim( (string)$orderBy ) : '';
        return "STRING_AGG(TO_NVARCHAR($expression), " . self::stringLiteral( (string)$separator ) . "$order)";
    }

    /**
     * Seconds since 1970-01-01 UTC, of now (the server's UTC clock) or of a timestamp expression taken as UTC.
     *
     * @param string|null $expression
     * @return string
     */
    public static function unixTimestamp( $expression = null )
    {
        $value = $expression === null ? 'CURRENT_UTCTIMESTAMP' : "TO_TIMESTAMP($expression)";
        return "SECONDS_BETWEEN(TO_TIMESTAMP('1970-01-01 00:00:00'), $value)";
    }

    /**
     * A timestamp (UTC) of seconds since 1970.
     *
     * @param string $expression
     * @return string
     */
    public static function fromUnixTimestamp( $expression )
    {
        return "ADD_SECONDS(TO_TIMESTAMP('1970-01-01 00:00:00'), $expression)";
    }

    /**
     * @param string $string
     * @param string|int $from 1-based
     * @param string|int|null $length
     * @return string
     */
    public static function subString( $string, $from, $length = null )
    {
        return "SUBSTRING($string, $from" . ( $length !== null && $length !== '' ? ", $length" : '' ) . ')';
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
     * The MD5 of a text expression as 32 lower-case hex digits, of the text's UTF-8 bytes (HANA keeps a character
     * outside the Basic Multilingual Plane as CESU-8, so for an emoji the digest differs from PHP's md5()).
     *
     * @param string $expression
     * @return string
     */
    public static function md5( $expression )
    {
        return "LOWER(BINTOHEX(HASH_MD5(TO_VARBINARY(TO_NVARCHAR($expression)))))";
    }

    public static function bitAnd( $a, $b )
    {
        return "BITAND($a, $b)";
    }

    public static function bitOr( $a, $b )
    {
        return "BITOR($a, $b)";
    }

    public static function charLength( $expression )
    {
        return "LENGTH($expression)";
    }

    /**
     * column IN (...) for any number of values: lists of at most $chunk values joined with OR (AND for NOT IN).
     *
     * @param array $elements
     * @param string $column
     * @param bool $not
     * @param string|false $type int/integer and float/double write numbers, string a quoted literal; without a type
     *                           the elements are SQL as they are (the kernel passes '...' already quoted)
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
     * String literals longer than $threshold bytes as "?" parameters: a HANA string literal is at most 5,000
     * characters long, while a parameter takes a text of any length into an NCLOB. Null when there is none, or when
     * the statement has "?" of its own.
     *
     * @param string $sql
     * @param int $threshold
     * @return array|null array( sql, params )
     */
    public static function parameterizeLongLiterals( $sql, $threshold = 4000 )
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

    /**
     * The literal values of a data statement as "?" parameters, so HANA compiles a statement once for all its values
     * (HANA compiles every new statement text, about 20 ms each; the kernel writes its values as literals): every
     * string and number of an INSERT's VALUES, and elsewhere a string or number right of a comparison (=, <>, <, >,
     * LIKE) or in an IN list - not where both sides would be parameters (1 = 1), not in a select list, ORDER BY or a
     * type. array( sql, values ) or null when the statement has none or already has "?" of its own.
     *
     * @param string $sql
     * @return array|null array( string $sql, string[] $values )
     */
    public static function parameterize( $sql )
    {
        $sql = (string)$sql;
        $kind = self::statementKind( $sql );
        if ( !in_array( $kind, array( 'SELECT', 'WITH', 'INSERT', 'UPSERT', 'UPDATE', 'DELETE', 'MERGE' ), true ) )
            return null;
        $tokens = expSQLServerLexer::tokenize( $sql );
        $count = count( $tokens );
        $params = array();
        $replace = array();
        $significant = array();
        foreach ( $tokens as $i => $token )
        {
            if ( $token['type'] === expSQLServerLexer::PARAM )
                return null;
            if ( !expSQLServerLexer::isNoise( $token ) )
                $significant[] = $i;
        }
        $comparison = array( '=', '<', '>', '!' );
        // the depth of the parentheses of an INSERT's VALUES rows and of IN lists
        $valuesDepth = null;
        $inLists = array();
        $depth = 0;
        $n = count( $significant );
        for ( $k = 0; $k < $n; $k++ )
        {
            $i = $significant[$k];
            $token = $tokens[$i];
            $text = $token['text'];
            $prev = $k > 0 ? $tokens[$significant[$k - 1]] : null;
            $prevText = $prev === null ? '' : $prev['text'];
            $prevWord = $prev === null ? '' : expSQLServerLexer::keyword( $prev );
            if ( $token['type'] === expSQLServerLexer::PUNCT && $text === '(' )
            {
                $depth++;
                if ( $prevWord === 'IN' )
                    $inLists[$depth] = true;
                continue;
            }
            if ( $token['type'] === expSQLServerLexer::PUNCT && $text === ')' )
            {
                unset( $inLists[$depth] );
                $depth--;
                continue;
            }
            $word = expSQLServerLexer::keyword( $token );
            if ( $word === 'VALUES' && $depth === 0 && in_array( $kind, array( 'INSERT', 'UPSERT' ), true ) )
                $valuesDepth = 1;
            else if ( $word === 'SELECT' && $depth === 0 )
                $valuesDepth = null;
            $isString = $token['type'] === expSQLServerLexer::STRING && !$token['open'];
            $isNumber = $token['type'] === expSQLServerLexer::NUMBER;
            if ( !$isString && !$isNumber )
                continue;
            $next = isset( $significant[$k + 1] ) ? $tokens[$significant[$k + 1]] : null;
            $nextText = $next === null ? '' : $next['text'];
            // a signed number: the sign goes into the value
            $signed = $isNumber && $prevText === '-' && $k > 1 && ( in_array( $tokens[$significant[$k - 2]]['text'], array_merge( $comparison, array( ',', '(' ) ), true )
                                                                 || expSQLServerLexer::keyword( $tokens[$significant[$k - 2]] ) === 'IN' );
            $before = $signed ? ( $k > 1 ? $tokens[$significant[$k - 2]] : null ) : $prev;
            $beforeText = $before === null ? '' : $before['text'];
            $beforeWord = $before === null ? '' : expSQLServerLexer::keyword( $before );
            $inValues = $valuesDepth !== null && $depth === $valuesDepth && in_array( $beforeText, array( '(', ',' ), true ) && in_array( $nextText, array( ',', ')' ), true );
            $inList = isset( $inLists[$depth] ) && in_array( $beforeText, array( '(', ',' ), true ) && in_array( $nextText, array( ',', ')' ), true );
            $compared = false;
            if ( in_array( $beforeText, $comparison, true ) || $beforeWord === 'LIKE' )
            {
                // the operand left of the operator: not a literal (1 = 1 would leave HANA no type for either side)
                $at = $signed ? $k - 2 : $k - 1;
                if ( $beforeWord === 'LIKE' )
                    $at--;
                else
                    while ( $at >= 0 && in_array( $tokens[$significant[$at]]['text'], $comparison, true ) )
                        $at--;
                $left = $at >= 0 ? $tokens[$significant[$at]] : null;
                $compared = $left !== null && !in_array( $left['type'], array( expSQLServerLexer::STRING, expSQLServerLexer::NUMBER ), true );
            }
            if ( !$inValues && !$inList && !$compared )
                continue;
            $value = $isString ? expSQLServerLexer::literalValue( $token ) : ( $signed ? '-' : '' ) . $text;
            $params[] = $value;
            $replace[$i] = true;
            if ( $signed )
                $replace[$significant[$k - 1]] = false;
        }
        if ( !$params )
            return null;
        $out = '';
        foreach ( $tokens as $i => $token )
        {
            if ( isset( $replace[$i] ) )
                $out .= $replace[$i] ? '?' : '';
            else
                $out .= $token['text'];
        }
        return array( $out, $params );
    }

    /**
     * The string literals of a statement that hold more than ASCII as TO_NVARCHAR(BINTOSTR(X'...')) of their CESU-8
     * bytes. HANA's ODBC driver reads the text of a statement (not its parameters) in a single-byte code page, so
     * 'ä' in a statement arrives as two characters; the bytes of a binary literal arrive as they are. Statements of
     * the schema keep their literals (a DEFAULT takes no function); parameterize() binds most values anyway.
     *
     * @param string $sql
     * @return string
     */
    public static function unicodeLiterals( $sql )
    {
        $sql = (string)$sql;
        if ( !preg_match( '/[\x80-\xFF]/', $sql ) )
            return $sql;
        if ( !in_array( self::statementKind( $sql ), array( 'SELECT', 'WITH', 'INSERT', 'UPSERT', 'UPDATE', 'DELETE', 'MERGE' ), true ) )
            return $sql;
        $out = '';
        foreach ( expSQLServerLexer::tokenize( $sql ) as $token )
        {
            if ( $token['type'] === expSQLServerLexer::STRING && !$token['open'] && preg_match( '/[\x80-\xFF]/', $token['text'] ) )
                $out .= "TO_NVARCHAR(BINTOSTR(X'" . strtoupper( bin2hex( self::cesu8( expSQLServerLexer::literalValue( $token ) ) ) ) . "'))";
            else
                $out .= $token['text'];
        }
        return $out;
    }

    /**
     * UTF-8 text as CESU-8, HANA's own encoding: a character outside the Basic Multilingual Plane as its two UTF-16
     * surrogates of three bytes each. Bytes that are no UTF-8 are kept as they are.
     *
     * @param string $text
     * @return string
     */
    public static function cesu8( $text )
    {
        $text = (string)$text;
        if ( !preg_match( '/[\xF0-\xF4]/', $text ) || !mb_check_encoding( $text, 'UTF-8' ) )
            return $text;
        return preg_replace_callback( '/[\xF0-\xF4][\x80-\xBF]{3}/', function ( $m )
        {
            $cp = mb_ord( $m[0], 'UTF-8' ) - 0x10000;
            $out = '';
            foreach ( array( 0xD800 + ( $cp >> 10 ), 0xDC00 + ( $cp & 0x3FF ) ) as $unit )
                $out .= chr( 0xE0 | ( $unit >> 12 ) ) . chr( 0x80 | ( ( $unit >> 6 ) & 0x3F ) ) . chr( 0x80 | ( $unit & 0x3F ) );
            return $out;
        }, $text );
    }

    // ------------------------------------------------------------------ temporary tables

    /**
     * A kernel CREATE TEMPORARY TABLE as a global temporary table of HANA (its rows belong to the session that wrote
     * them): array( 'name' => table, 'sql' => CREATE GLOBAL TEMPORARY TABLE ... ), or null when $sql is no such
     * statement.
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
            $columns[] = self::columnDefinition( trim( expSQLServerLexer::join( $column ) ) );
        return array( 'name' => $name,
                      'sql' => 'CREATE GLOBAL TEMPORARY TABLE ' . self::quoteIdentifierIfNeeded( $name ) . ' ( ' . implode( ', ', $columns ) . ' )' );
    }

    /**
     * A column definition of MySQL's words (int(11) primary key not null, varchar(n), text) in HANA's (INTEGER NOT
     * NULL PRIMARY KEY, NVARCHAR(n), NCLOB).
     *
     * @param string $definition
     * @return string
     */
    public static function columnDefinition( $definition )
    {
        $text = ' ' . preg_replace( '/\s+/', ' ', $definition ) . ' ';
        $notNull = stripos( $text, ' not null ' ) !== false;
        $primary = stripos( $text, ' primary key ' ) !== false;
        $text = str_ireplace( array( ' not null ', ' primary key ', ' null ' ), ' ', $text );
        $text = trim( preg_replace( '/\s+/', ' ', $text ) );
        $text = preg_replace( '/\b(?:int|integer|mediumint)\s*(\(\s*\d+\s*\))?(\s+unsigned)?/i', 'INTEGER', $text );
        $text = preg_replace( '/\btinyint\s*(\(\s*\d+\s*\))?(\s+unsigned)?/i', 'SMALLINT', $text );
        $text = preg_replace( '/\bbigint\s*(\(\s*\d+\s*\))?(\s+unsigned)?/i', 'BIGINT', $text );
        $text = preg_replace( '/\bvarchar\b/i', 'NVARCHAR', $text );
        $text = preg_replace( '/\b(?:longtext|mediumtext|tinytext|text)\b/i', 'NCLOB', $text );
        $text = preg_replace( '/\bdouble\b(?!\s+precision)/i', 'DOUBLE', $text );
        if ( preg_match( '/^([A-Za-z_][A-Za-z0-9_$#]*)(\s.*)?$/s', $text, $m ) )
            $text = self::quoteIdentifierIfNeeded( $m[1] ) . ( isset( $m[2] ) ? $m[2] : '' );
        return $text . ( $notNull || $primary ? ' NOT NULL' : '' ) . ( $primary ? ' PRIMARY KEY' : '' );
    }

    /**
     * The SQL name of a column type of the catalogue (SYS.TABLE_COLUMNS.DATA_TYPE_NAME, length and scale).
     *
     * @param string $type
     * @param int|string|null $length
     * @param int|string|null $scale
     * @return string
     */
    public static function typeName( $type, $length = null, $scale = null )
    {
        $type = strtolower( trim( (string)$type ) );
        if ( in_array( $type, array( 'varchar', 'nvarchar', 'char', 'nchar', 'varbinary', 'alphanum', 'shorttext' ), true ) && (int)$length > 0 )
            return $type . '(' . (int)$length . ')';
        if ( $type === 'decimal' && (int)$length > 0 )
            return 'decimal(' . (int)$length . ( $scale !== null && $scale !== '' ? ',' . (int)$scale : '' ) . ')';
        return $type;
    }

    // ------------------------------------------------------------------ errors

    /**
     * The kind of an error, from HANA's error code and the message: deadlock (a single statement may be sent again),
     * lock (a lock wait timeout), duplicate (a primary or unique key), connection (the connection is gone), syntax,
     * unknown_object, or other.
     *
     * @param int|null $code HANA's error code (ODBC's native error)
     * @param string $message
     * @param string|null $state the SQLSTATE
     * @return string
     */
    public static function errorCategory( $code, $message = '', $state = null )
    {
        $message = strtolower( (string)$message );
        $code = (int)$code;
        $state = strtoupper( (string)$state );
        if ( $code === 133 || strpos( $message, 'deadlock' ) !== false || $state === '40001' )
            return 'deadlock';
        if ( in_array( $code, array( -10709, -10807, -10108, -10106, -10821, 1033, 139, 3584, 4321 ), true )
             || strpos( $state, '08' ) === 0 || strpos( $message, 'connection is closed' ) !== false
             || strpos( $message, 'connection failed' ) !== false || strpos( $message, 'connection broken' ) !== false
             || strpos( $message, 'socket closed' ) !== false || strpos( $message, 'communication link failure' ) !== false )
            return 'connection';
        if ( $code === 131 || strpos( $message, 'lock wait timeout' ) !== false )
            return 'lock';
        if ( $code === 301 || strpos( $message, 'unique constraint violated' ) !== false )
            return 'duplicate';
        if ( $code === 257 )
            return 'syntax';
        if ( in_array( $code, array( 259, 260, 313, 397 ), true ) )
            return 'unknown_object';
        return 'other';
    }

    // ------------------------------------------------------------------ connection

    /**
     * The ODBC connection string of SAP's HANA client: DRIVER=...;SERVERNODE=host:port;CURRENTSCHEMA=...;
     * CHAR_AS_UTF8=TRUE (text comes as UTF-8 in SQL_C_CHAR, which both of PHP's ODBC layers read), plus the extra
     * keyword=value pairs of $config['extra'].
     *
     * @param array $config host, port, schema, driver, tenant, encrypt, validate_certificate, extra
     * @return string
     */
    public static function connectionString( array $config )
    {
        $host = isset( $config['host'] ) && trim( (string)$config['host'] ) !== '' ? trim( (string)$config['host'] ) : 'localhost';
        $port = isset( $config['port'] ) && (int)$config['port'] > 0 ? (int)$config['port'] : self::DEFAULT_PORT;
        if ( strpos( $host, ':' ) !== false && $host[0] !== '[' && substr_count( $host, ':' ) > 1 )
            $host = '[' . $host . ']';
        $driver = isset( $config['driver'] ) && trim( (string)$config['driver'] ) !== '' ? trim( (string)$config['driver'] ) : self::DEFAULT_DRIVER;
        $pairs = array( 'DRIVER' => $driver, 'SERVERNODE' => $host . ':' . $port );
        if ( !empty( $config['tenant'] ) )
            $pairs['DATABASENAME'] = (string)$config['tenant'];
        if ( !empty( $config['schema'] ) )
            $pairs['CURRENTSCHEMA'] = self::upper( (string)$config['schema'] );
        $pairs['CHAR_AS_UTF8'] = 'TRUE';
        if ( !empty( $config['encrypt'] ) )
        {
            $pairs['ENCRYPT'] = 'TRUE';
            $pairs['sslValidateCertificate'] = !empty( $config['validate_certificate'] ) ? 'TRUE' : 'FALSE';
        }
        $out = array();
        foreach ( $pairs as $key => $value )
            $out[] = $key . '=' . self::connectionValue( $value );
        $extra = isset( $config['extra'] ) ? trim( (string)$config['extra'], " ;" ) : '';
        return implode( ';', $out ) . ( $extra !== '' ? ';' . $extra : '' );
    }

    /**
     * A value of the connection string, in braces when it holds ; { } = or spaces at its ends.
     *
     * @param string $value
     * @return string
     */
    public static function connectionValue( $value )
    {
        $value = (string)$value;
        if ( strpos( $value, "\0" ) !== false )
            throw new InvalidArgumentException( 'A HANA connection value cannot hold a NUL byte' );
        if ( strpbrk( $value, ';{}=' ) === false && trim( $value ) === $value )
            return $value;
        return '{' . str_replace( '}', '}}', $value ) . '}';
    }

    /**
     * What a version string of SYS.M_DATABASE says ("2.00.072.00.1690304772"; HANA Cloud "4.00.000.00...").
     *
     * @param string $version
     * @return array major, minor, sps, string, supported, name
     */
    public static function describeVersion( $version )
    {
        $version = trim( (string)$version );
        $major = 0;
        $minor = 0;
        $sps = 0;
        if ( preg_match( '/^(\d+)\.(\d+)\.(\d+)/', $version, $m ) )
        {
            $major = (int)$m[1];
            $minor = (int)$m[2];
            $sps = (int)$m[3];
        }
        $cloud = $major >= 4;
        $name = $cloud ? 'SAP HANA Cloud' : 'SAP HANA ' . $major . '.0' . ( $major === 2 ? ' SPS ' . sprintf( '%02d', intdiv( $sps, 10 ) ) : '' );
        $supported = $cloud || ( $major === 2 && $sps >= 50 );
        return array( 'major' => $major, 'minor' => $minor, 'sps' => $sps, 'string' => $version, 'name' => $name, 'supported' => $supported );
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
