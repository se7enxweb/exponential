<?php
/**
 * File containing the expMSAccessDialect class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The SQL of Microsoft Access (the ACE / Jet database engine behind the Access ODBC driver), as pure functions, so it
 * is tested without Windows. Access SQL differs from the kernel's MySQL words in many ways:
 *
 *  - names in [brackets] (a name cannot hold "]"; 64 characters at most); "text" in double quotes is a string;
 *  - TOP n instead of LIMIT, and no OFFSET at all: a page is read as TOP offset+limit and the first rows are dropped
 *    in PHP (expMSAccessDB::arrayQuery()); TOP also returns the rows that tie with the last one in the ORDER BY;
 *  - AUTOINCREMENT (COUNTER) columns, and the new id from SELECT @@IDENTITY;
 *  - & joins text, IIf() is the conditional, Mid() and Len() the substring and length, #date# literals;
 *  - one statement per call (no batches; split() cuts a script), no comments;
 *  - no upsert (MERGE, ON CONFLICT), no GROUP_CONCAT, no MD5, no FOR UPDATE (the engine locks pages itself);
 *  - bitwise AND/OR only as BAND/BOR in ANSI-92 mode (ExtendedAnsiSQL=1 in the ODBC connection), else emulated with
 *    integer division for a constant mask;
 *  - DEFAULT and DECIMAL in a CREATE TABLE only in ANSI-92 mode.
 *
 * @since 6.0.15
 * @package lib
 */
class expMSAccessDialect
{
    /** The longest name Access takes */
    const NAME_LENGTH = 64;

    /** The longest TEXT column; a longer one is a LONGTEXT (memo) */
    const TEXT_LENGTH = 255;

    /** Token types of tokenize() */
    const WS = 'ws';
    const COMMENT = 'comment';
    const STRING = 'string';
    const IDENT = 'ident';
    const DATE = 'date';
    const NUMBER = 'number';
    const WORD = 'word';
    const PUNCT = 'punct';

    /**
     * The statement as tokens, lossless (implode of the texts is the statement): '...' and "..." strings (quotes
     * doubled inside), [names] and `names`, #date# literals, -- and slash-star comments, numbers, words, punctuation.
     *
     * @param string $sql
     * @return array[] array( 'type' => ..., 'text' => ... )
     */
    public static function tokenize( $sql )
    {
        $sql = (string)$sql;
        $tokens = array();
        $length = strlen( $sql );
        $i = 0;
        while ( $i < $length )
        {
            $c = $sql[$i];
            $next = $i + 1 < $length ? $sql[$i + 1] : '';
            if ( ctype_space( $c ) )
            {
                $j = $i;
                while ( $j < $length && ctype_space( $sql[$j] ) )
                    $j++;
                $tokens[] = array( 'type' => self::WS, 'text' => substr( $sql, $i, $j - $i ) );
                $i = $j;
            }
            elseif ( $c === '-' && $next === '-' )
            {
                $j = strpos( $sql, "\n", $i );
                $j = $j === false ? $length : $j;
                $tokens[] = array( 'type' => self::COMMENT, 'text' => substr( $sql, $i, $j - $i ) );
                $i = $j;
            }
            elseif ( $c === '/' && $next === '*' )
            {
                $j = strpos( $sql, '*/', $i + 2 );
                $j = $j === false ? $length : $j + 2;
                $tokens[] = array( 'type' => self::COMMENT, 'text' => substr( $sql, $i, $j - $i ) );
                $i = $j;
            }
            elseif ( $c === "'" || $c === '"' )
            {
                $j = $i + 1;
                while ( $j < $length )
                {
                    if ( $sql[$j] === $c )
                    {
                        if ( $j + 1 < $length && $sql[$j + 1] === $c )
                        {
                            $j += 2;
                            continue;
                        }
                        $j++;
                        break;
                    }
                    $j++;
                }
                $tokens[] = array( 'type' => self::STRING, 'text' => substr( $sql, $i, $j - $i ) );
                $i = $j;
            }
            elseif ( $c === '[' || $c === '`' )
            {
                $close = $c === '[' ? ']' : '`';
                $j = strpos( $sql, $close, $i + 1 );
                $j = $j === false ? $length : $j + 1;
                $tokens[] = array( 'type' => self::IDENT, 'text' => substr( $sql, $i, $j - $i ) );
                $i = $j;
            }
            elseif ( $c === '#' && preg_match( '/\G#[0-9]{1,4}[-\/][0-9]{1,2}[-\/][0-9]{1,4}(?:\s+[0-9]{1,2}:[0-9]{2}(?::[0-9]{2})?(?:\s*[AaPp][Mm])?)?#/', $sql, $m, 0, $i ) )
            {
                $tokens[] = array( 'type' => self::DATE, 'text' => $m[0] );
                $i += strlen( $m[0] );
            }
            elseif ( ctype_digit( $c ) || ( $c === '.' && ctype_digit( $next ) ) )
            {
                preg_match( '/\G[0-9]*\.?[0-9]+(?:[eE][-+]?[0-9]+)?/', $sql, $m, 0, $i );
                $tokens[] = array( 'type' => self::NUMBER, 'text' => $m[0] );
                $i += strlen( $m[0] );
            }
            elseif ( ctype_alpha( $c ) || $c === '_' || $c === '@' || ord( $c ) >= 128 )
            {
                preg_match( '/\G[A-Za-z0-9_@$\x80-\xff]+/', $sql, $m, 0, $i );
                $tokens[] = array( 'type' => self::WORD, 'text' => $m[0] );
                $i += strlen( $m[0] );
            }
            else
            {
                $tokens[] = array( 'type' => self::PUNCT, 'text' => $c );
                $i++;
            }
        }
        return $tokens;
    }

    /**
     * A name in brackets; a dotted name part by part. Access cannot quote "]", "." inside a name, or a NUL.
     *
     * @param string $name
     * @return string
     * @throws InvalidArgumentException
     */
    public static function quoteName( $name )
    {
        $out = array();
        foreach ( explode( '.', (string)$name ) as $part )
        {
            if ( $part === '' || strpbrk( $part, "]\0[`!" ) !== false )
                throw new InvalidArgumentException( "Access cannot use '$part' as a name" );
            if ( mb_strlen( $part, 'UTF-8' ) > self::NAME_LENGTH )
                throw new InvalidArgumentException( "The name '$part' is longer than Access's " . self::NAME_LENGTH . ' characters' );
            $out[] = '[' . $part . ']';
        }
        return implode( '.', $out );
    }

    /**
     * Text ready to go between single quotes: quotes doubled, NUL dropped (backslashes mean nothing to Access).
     *
     * @param string $value
     * @return string
     */
    public static function escapeString( $value )
    {
        return str_replace( array( "\0", "'" ), array( '', "''" ), (string)$value );
    }

    /**
     * A PHP value as an Access literal: NULL, 1/0 for booleans, numbers as they are, else quoted text.
     *
     * @param mixed $value
     * @return string
     */
    public static function literal( $value )
    {
        return expDBSQLHelpers::literal( array( __CLASS__, 'escapeString' ), $value );
    }

    /**
     * A date literal, #yyyy-mm-dd hh:nn:ss#, from a unix time stamp (in PHP's time zone, as Access stores no zone)
     * or a date text strtotime() reads.
     *
     * @param int|string $value
     * @return string
     * @throws InvalidArgumentException
     */
    public static function dateLiteral( $value )
    {
        $time = is_int( $value ) || ( is_string( $value ) && ctype_digit( $value ) ) ? (int)$value : strtotime( (string)$value );
        if ( $time === false )
            throw new InvalidArgumentException( "Not a date: $value" );
        return '#' . date( 'Y-m-d H:i:s', $time ) . '#';
    }

    /**
     * The statement as Access takes it: comments removed, `names` in brackets, a trailing semicolon dropped.
     *
     * @param string $sql
     * @return string
     */
    public static function translate( $sql )
    {
        $out = '';
        foreach ( self::tokenize( $sql ) as $token )
        {
            if ( $token['type'] === self::COMMENT )
            {
                $out .= ' ';
                continue;
            }
            if ( $token['type'] === self::IDENT && $token['text'][0] === '`' )
            {
                $out .= '[' . substr( $token['text'], 1, -1 ) . ']';
                continue;
            }
            $out .= $token['text'];
        }
        return rtrim( trim( $out ), "; \t\r\n" );
    }

    /**
     * A script cut into its statements (Access runs one per call): at every semicolon outside strings, names and
     * comments. Comments are removed; empty statements are left out.
     *
     * @param string $sql
     * @return string[]
     */
    public static function split( $sql )
    {
        $statements = array();
        $current = '';
        foreach ( self::tokenize( $sql ) as $token )
        {
            if ( $token['type'] === self::COMMENT )
            {
                $current .= ' ';
                continue;
            }
            if ( $token['type'] === self::PUNCT && $token['text'] === ';' )
            {
                if ( trim( $current ) !== '' )
                    $statements[] = trim( $current );
                $current = '';
                continue;
            }
            $current .= $token['text'];
        }
        if ( trim( $current ) !== '' )
            $statements[] = trim( $current );
        return $statements;
    }

    /**
     * The significant tokens (no white space, no comments) with their depth of parentheses.
     *
     * @param string $sql
     * @return array[] array( type, text, depth, index )
     */
    protected static function significant( $sql )
    {
        $out = array();
        $depth = 0;
        foreach ( self::tokenize( $sql ) as $index => $token )
        {
            if ( $token['type'] === self::WS || $token['type'] === self::COMMENT )
                continue;
            if ( $token['type'] === self::PUNCT && $token['text'] === ')' )
                $depth--;
            $out[] = $token + array( 'depth' => $depth, 'index' => $index );
            if ( $token['type'] === self::PUNCT && $token['text'] === '(' )
                $depth++;
        }
        return $out;
    }

    /**
     * Whether the statement is a SELECT (not a SELECT ... INTO, which makes a table).
     *
     * @param string $sql
     * @return bool
     */
    public static function isSelect( $sql )
    {
        $tokens = self::significant( $sql );
        if ( !$tokens )
            return false;
        $first = $tokens[0];
        if ( $first['type'] === self::PUNCT && $first['text'] === '(' && isset( $tokens[1] ) )
            $first = $tokens[1];
        if ( $first['type'] !== self::WORD || strtoupper( $first['text'] ) !== 'SELECT' )
            return false;
        foreach ( $tokens as $token )
            if ( $token['depth'] === 0 && $token['type'] === self::WORD && strtoupper( $token['text'] ) === 'INTO' )
                return false;
        return true;
    }

    /**
     * Whether the statement is an INSERT.
     *
     * @param string $sql
     * @return bool
     */
    public static function isInsert( $sql )
    {
        $tokens = self::significant( $sql );
        return $tokens && $tokens[0]['type'] === self::WORD && strtoupper( $tokens[0]['text'] ) === 'INSERT';
    }

    /**
     * A SELECT that returns at most $count rows: TOP n after SELECT [ALL|DISTINCT|DISTINCTROW]. A statement that has a
     * TOP already, or a UNION, is wrapped: SELECT TOP n * FROM (...) AS [page]. TOP keeps the rows that tie with the
     * last one, so the caller still counts the rows it takes. null for 0 rows (nothing to send); the statement as it
     * is for false (no limit).
     *
     * @param string $sql
     * @param int|false $count
     * @return string|null
     */
    public static function top( $sql, $count )
    {
        $sql = self::translate( $sql );
        if ( $count === false || $count === null )
            return $sql;
        $count = max( 0, (int)$count );
        if ( $count === 0 )
            return null;
        $tokens = self::tokenize( $sql );
        $significant = self::significant( $sql );
        $hasUnion = false;
        $hasTop = false;
        foreach ( $significant as $n => $token )
        {
            if ( $token['depth'] !== 0 || $token['type'] !== self::WORD )
                continue;
            $word = strtoupper( $token['text'] );
            if ( $word === 'UNION' )
                $hasUnion = true;
            if ( $word === 'TOP' && $n <= 2 )
                $hasTop = true;
        }
        if ( !$significant || $significant[0]['type'] !== self::WORD || strtoupper( $significant[0]['text'] ) !== 'SELECT' || $hasUnion || $hasTop )
            return "SELECT TOP $count * FROM ( $sql ) AS [page]";
        // after SELECT and its predicate
        $at = $significant[0]['index'];
        if ( isset( $significant[1] ) && $significant[1]['type'] === self::WORD && in_array( strtoupper( $significant[1]['text'] ), array( 'ALL', 'DISTINCT', 'DISTINCTROW' ), true ) )
            $at = $significant[1]['index'];
        $out = '';
        foreach ( $tokens as $index => $token )
        {
            $out .= $token['text'];
            if ( $index === $at )
                $out .= " TOP $count";
        }
        return $out;
    }

    /**
     * The portable sqlLimit(): TOP n. Access has no OFFSET; with an offset this throws, and the caller reads the page
     * through arrayQuery( $sql, array( 'limit' => n, 'offset' => m ) ), which drops the first rows in PHP.
     *
     * @param string $sql
     * @param int|false|null $limit
     * @param int $offset
     * @return string|null
     * @throws expDBUnsupportedException with an offset
     */
    public static function applyLimit( $sql, $limit, $offset = 0 )
    {
        list( $limit, $offset ) = expDBSQLHelpers::normalizeLimit( $limit, $offset );
        if ( $limit === 0 )
            return null;
        if ( $offset > 0 )
            throw new expDBUnsupportedException( 'msaccess', 'OFFSET', 'read the page with arrayQuery() and its offset parameter' );
        return self::top( $sql, $limit );
    }

    /**
     * Text joined: a & b & c (NULL counts as empty text, unlike +).
     *
     * @param string[] $strings
     * @return string
     */
    public static function concatString( array $strings )
    {
        return ' ( ' . implode( ' & ', $strings ) . ' ) ';
    }

    /**
     * Mid( string, from [, length] ), 1-based as SUBSTRING.
     *
     * @return string
     */
    public static function subString( $string, $from, $length = null )
    {
        return $length === null ? " Mid( $string, $from ) " : " Mid( $string, $from, $length ) ";
    }

    /** @return string Len( expression ) */
    public static function charLength( $expression )
    {
        return "Len( $expression )";
    }

    /** @return string IIf( condition, then, else ) */
    public static function iif( $condition, $then, $else )
    {
        return "IIf( $condition, $then, $else )";
    }

    /**
     * Seconds since 1970 of now or of a date expression, from the date's days as a Double (DateDiff in seconds is a
     * Long and overflows after 2038). Access keeps no time zone: Now() is the server's local time.
     *
     * @param string|null $expression
     * @return string
     */
    public static function unixTimestamp( $expression = null )
    {
        return 'Round( ( CDbl( ' . ( $expression === null ? 'Now()' : $expression ) . ' ) - CDbl( #1970-01-01 00:00:00# ) ) * 86400, 0 )';
    }

    /**
     * The date of seconds since 1970.
     *
     * @param string $expression
     * @return string
     */
    public static function fromUnixTimestamp( $expression )
    {
        // days as a Double: DateAdd in seconds takes a Long and overflows after 2038
        return "CDate( CDbl( #1970-01-01 00:00:00# ) + ( $expression ) / 86400 )";
    }

    /**
     * Bits of $a that are also in $b. In ANSI-92 mode BAND; otherwise, for a constant non-negative mask, the sum of
     * ((a \ 2^k) MOD 2) * 2^k over the mask's bits, which Access computes exactly for Long values.
     *
     * @param string $a
     * @param string $b
     * @param bool $ansi92
     * @return string
     * @throws expDBUnsupportedException when neither BAND nor a constant mask is there
     */
    public static function bitAnd( $a, $b, $ansi92 = false )
    {
        if ( $ansi92 )
            return " ( $a BAND $b ) ";
        foreach ( array( array( $a, $b ), array( $b, $a ) ) as $pair )
        {
            $mask = trim( (string)$pair[1] );
            if ( !preg_match( '/^\(?\s*([0-9]+)\s*\)?$/', $mask, $m ) || (int)$m[1] > 0x7fffffff )
                continue;
            $mask = (int)$m[1];
            if ( $mask === 0 )
                return ' 0 ';
            $parts = array();
            for ( $bit = 0; $bit < 31; $bit++ )
            {
                $value = 1 << $bit;
                if ( $mask & $value )
                    $parts[] = $value === 1 ? "( ( {$pair[0]} ) MOD 2 )" : "( ( ( {$pair[0]} ) \\ $value ) MOD 2 ) * $value";
            }
            return ' ( ' . implode( ' + ', $parts ) . ' ) ';
        }
        throw new expDBUnsupportedException( 'msaccess', 'bitwise AND of two columns', 'switch ExtendedAnsiSQL on (BAND) or use a constant mask' );
    }

    /**
     * Bits in $a or $b: BOR in ANSI-92 mode, else a + b - (a AND b) for a constant mask.
     *
     * @return string
     * @throws expDBUnsupportedException
     */
    public static function bitOr( $a, $b, $ansi92 = false )
    {
        if ( $ansi92 )
            return " ( $a BOR $b ) ";
        return " ( ( $a ) + ( $b ) - " . trim( self::bitAnd( $a, $b, false ) ) . ' ) ';
    }

    /**
     * The column type of a .dba field: COUNTER, LONG, DECIMAL (ANSI-92) or DOUBLE, TEXT(n), LONGTEXT, DOUBLE,
     * CURRENCY, with NOT NULL and (ANSI-92 only) DEFAULT.
     *
     * @param array $field
     * @param bool $ansi92
     * @return string
     */
    public static function columnType( array $field, $ansi92 = true )
    {
        $type = isset( $field['type'] ) ? strtolower( (string)$field['type'] ) : 'varchar';
        $length = isset( $field['length'] ) ? (int)$field['length'] : 0;
        $notNull = !empty( $field['not_null'] ) ? ' NOT NULL' : '';
        switch ( $type )
        {
            case 'auto_increment':
                return 'COUNTER NOT NULL';
            case 'int':
            case 'integer':
            case 'smallint':
            case 'tinyint':
                $sql = 'LONG';
                break;
            case 'bigint':
                $sql = $ansi92 ? 'DECIMAL(19,0)' : 'DOUBLE';
                break;
            case 'float':
            case 'double':
            case 'real':
                $sql = 'DOUBLE';
                break;
            case 'decimal':
            case 'numeric':
                $sql = $ansi92 ? 'DECIMAL(' . ( $length > 0 && $length <= 28 ? $length : 18 ) . ',' . ( isset( $field['scale'] ) ? (int)$field['scale'] : 2 ) . ')' : 'CURRENCY';
                break;
            case 'longtext':
            case 'mediumtext':
            case 'text':
            case 'tinytext':
                // a memo has no default
                return 'LONGTEXT';
            case 'datetime':
            case 'date':
            case 'timestamp':
                $sql = 'DATETIME';
                break;
            case 'blob':
            case 'longblob':
                return 'LONGBINARY';
            case 'char':
            case 'varchar':
            default:
                if ( $length > self::TEXT_LENGTH )
                    return 'LONGTEXT';
                $sql = 'TEXT(' . max( 1, $length ?: self::TEXT_LENGTH ) . ')';
        }
        if ( $ansi92 && array_key_exists( 'default', $field ) && $field['default'] !== false && $field['default'] !== null )
            $sql .= ' DEFAULT ' . ( is_numeric( $field['default'] ) && !in_array( $type, array( 'char', 'varchar' ), true ) ? 0 + $field['default'] : "'" . self::escapeString( $field['default'] ) . "'" );
        return $sql . $notNull;
    }

    /**
     * The CREATE TABLE and CREATE INDEX statements of a .dba table (one statement each: Access takes no batches).
     * Index names are made unique per database (Access names them per table, but a primary key's constraint name
     * must be unique in the file).
     *
     * @param string $table
     * @param array $definition
     * @param bool $ansi92
     * @return string[]
     */
    public static function createTable( $table, array $definition, $ansi92 = true )
    {
        $columns = array();
        foreach ( isset( $definition['fields'] ) ? (array)$definition['fields'] : array() as $name => $field )
            $columns[] = self::quoteName( $name ) . ' ' . self::columnType( (array)$field, $ansi92 );
        $indexes = isset( $definition['indexes'] ) ? (array)$definition['indexes'] : array();
        foreach ( $indexes as $name => $index )
        {
            if ( $name === 'PRIMARY' || ( isset( $index['type'] ) && $index['type'] === 'primary' ) )
            {
                $columns[] = 'CONSTRAINT ' . self::quoteName( self::indexName( $table, 'pk' ) ) . ' PRIMARY KEY ('
                           . implode( ', ', array_map( array( __CLASS__, 'quoteName' ), self::indexColumns( $index ) ) ) . ')';
            }
        }
        $statements = array( 'CREATE TABLE ' . self::quoteName( $table ) . ' (' . implode( ', ', $columns ) . ')' );
        foreach ( $indexes as $name => $index )
        {
            if ( $name === 'PRIMARY' || ( isset( $index['type'] ) && $index['type'] === 'primary' ) )
                continue;
            $statements[] = 'CREATE ' . ( isset( $index['type'] ) && $index['type'] === 'unique' ? 'UNIQUE ' : '' ) . 'INDEX '
                          . self::quoteName( self::indexName( $table, $name ) ) . ' ON ' . self::quoteName( $table ) . ' ('
                          . implode( ', ', array_map( array( __CLASS__, 'quoteName' ), self::indexColumns( $index ) ) ) . ')';
        }
        return $statements;
    }

    /**
     * The columns of a .dba index (a field may be array( 'name' => ..., 'mysql:length' => ... )).
     *
     * @param array $index
     * @return string[]
     */
    public static function indexColumns( array $index )
    {
        $columns = array();
        foreach ( isset( $index['fields'] ) ? (array)$index['fields'] : array() as $field )
            $columns[] = is_array( $field ) ? (string)$field['name'] : (string)$field;
        return $columns;
    }

    /**
     * An index name within Access's 64 characters.
     *
     * @param string $table
     * @param string $name
     * @return string
     */
    public static function indexName( $table, $name )
    {
        $full = $table . '_' . $name;
        return strlen( $full ) <= self::NAME_LENGTH ? $full : substr( $full, 0, 55 ) . '_' . substr( md5( $full ), 0, 8 );
    }
}
?>
