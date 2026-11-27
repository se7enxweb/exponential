<?php
/**
 * File containing the expCassandraDialect class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The SQL of expCassandraDB (Apache Cassandra and ScyllaDB), without a server: the subset of SQL the persistent
 * classes, the data table layer and the importers write, parsed into a statement the driver can plan, the rows a
 * WHERE clause keeps evaluated in PHP, and the types of CQL and of the .dba schema mapped onto each other.
 *
 * CQL has no joins, no sub-selects, no OR, no LIKE, ORDER BY only on the clustering columns of one partition and
 * WHERE only on the primary key (or a secondary index, or with ALLOW FILTERING). So the driver sends Cassandra what
 * it can answer (a key lookup, an index lookup, a filtered scan) and does the rest here: what a statement asks that
 * this class cannot parse (a join, a sub-select, GROUP BY, UNION, a function it does not know) throws
 * expDBUnsupportedException and is never answered with a wrong or an empty result.
 *
 * The statements:
 *
 *   SELECT [DISTINCT] <columns | * | COUNT(*) | COUNT(col) | MIN/MAX/SUM/AVG(col) | literals> FROM <table> [[AS] a]
 *     [WHERE <condition>] [ORDER BY <column> [ASC|DESC], ...] [LIMIT n [OFFSET m] | LIMIT m, n] [FOR UPDATE]
 *   INSERT [IGNORE] INTO <table> (<columns>) VALUES (<values>)[, (<values>) ...]
 *   UPDATE <table> SET <column> = <value or arithmetic of columns and values>, ... [WHERE <condition>]
 *   DELETE FROM <table> [WHERE <condition>]
 *
 * A condition: AND, OR, NOT, parentheses, = <> != < > <= >=, [NOT] IN (...), [NOT] LIKE, IS [NOT] NULL,
 * [NOT] BETWEEN, arithmetic and the bit operators & and | on both sides, LOWER() and UPPER(). Text compares as
 * Cassandra compares it (case-sensitive, by code points); a number and a numeric text compare as numbers, as MySQL and
 * SQLite do, and so do two numeric texts (the library gives decimals as text: '007' equals '7' here, not in MySQL).
 * Anything else (CREATE, DROP, ALTER, TRUNCATE ...) is CQL already and goes to the server as it is, with MySQL's
 * backquotes turned into double quotes.
 *
 * @since 6.0.15
 * @package lib
 */
class expCassandraDialect
{
    /** The dialect's name (expDBSQLHelpers, expDBUnsupportedException) */
    const NAME = 'cassandra';

    /** The words that end an expression or a list */
    const STOP_WORDS = array( 'FROM', 'WHERE', 'ORDER', 'GROUP', 'HAVING', 'LIMIT', 'OFFSET', 'FOR', 'UNION', 'JOIN', 'INNER', 'LEFT',
                              'RIGHT', 'FULL', 'CROSS', 'ON', 'AND', 'OR', 'AS', 'ASC', 'DESC', 'SET', 'VALUES', 'NOT', 'IS', 'IN',
                              'LIKE', 'BETWEEN', 'ESCAPE', 'THEN', 'ELSE', 'END', 'WHEN' );

    /** The aggregates the driver computes */
    const AGGREGATES = array( 'COUNT', 'MIN', 'MAX', 'SUM', 'AVG' );

    /** The functions the driver computes (in PHP, on the rows the server sent) */
    const FUNCTIONS = array( 'LOWER', 'UPPER', 'LENGTH', 'CHAR_LENGTH', 'COALESCE', 'IFNULL', 'ABS', 'CONCAT', 'SUBSTRING', 'SUBSTR', 'MD5',
                             'UNIX_TIMESTAMP', 'FROM_UNIXTIME' );

    // ------------------------------------------------------------------ tokens

    /**
     * The tokens of a statement: array( type, value ) with type word (a name or keyword, value as written),
     * quoted (a quoted name, without its quotes), string (a text literal, unescaped), number, op, param (?).
     *
     * @param string $sql
     * @return array[]
     * @throws expDBUnsupportedException on a text that does not end
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
            if ( ctype_space( $c ) )
            {
                $i++;
                continue;
            }
            // comments
            if ( $c === '-' && $i + 1 < $length && $sql[$i + 1] === '-' )
            {
                $end = strpos( $sql, "\n", $i );
                $i = $end === false ? $length : $end + 1;
                continue;
            }
            if ( $c === '/' && $i + 1 < $length && $sql[$i + 1] === '*' )
            {
                $end = strpos( $sql, '*/', $i + 2 );
                $i = $end === false ? $length : $end + 2;
                continue;
            }
            if ( $c === "'" )
            {
                $value = '';
                $i++;
                $closed = false;
                while ( $i < $length )
                {
                    if ( $sql[$i] === "'" )
                    {
                        if ( $i + 1 < $length && $sql[$i + 1] === "'" )
                        {
                            $value .= "'";
                            $i += 2;
                            continue;
                        }
                        $closed = true;
                        $i++;
                        break;
                    }
                    $value .= $sql[$i];
                    $i++;
                }
                if ( !$closed )
                    throw new expDBUnsupportedException( self::NAME, 'text literal', 'a text literal does not end' );
                $tokens[] = array( 'string', $value );
                continue;
            }
            if ( $c === '`' || $c === '"' )
            {
                $end = strpos( $sql, $c, $i + 1 );
                if ( $end === false )
                    throw new expDBUnsupportedException( self::NAME, 'quoted name', 'a quoted name does not end' );
                $tokens[] = array( 'quoted', substr( $sql, $i + 1, $end - $i - 1 ) );
                $i = $end + 1;
                continue;
            }
            if ( ctype_digit( $c ) || ( $c === '.' && $i + 1 < $length && ctype_digit( $sql[$i + 1] ) ) )
            {
                if ( preg_match( '/\G(?:\d+(?:\.\d*)?|\.\d+)(?:[eE][-+]?\d+)?/', $sql, $m, 0, $i ) )
                {
                    $tokens[] = array( 'number', $m[0] );
                    $i += strlen( $m[0] );
                    continue;
                }
            }
            if ( ctype_alpha( $c ) || $c === '_' || $c === '$' || ord( $c ) >= 0x80 )
            {
                preg_match( '/\G[A-Za-z0-9_$\x80-\xff]+/', $sql, $m, 0, $i );
                $tokens[] = array( 'word', $m[0] );
                $i += strlen( $m[0] );
                continue;
            }
            $two = substr( $sql, $i, 2 );
            if ( in_array( $two, array( '<=', '>=', '<>', '!=', '||' ), true ) )
            {
                $tokens[] = array( 'op', $two );
                $i += 2;
                continue;
            }
            if ( $c === '?' )
            {
                $tokens[] = array( 'param', '?' );
                $i++;
                continue;
            }
            if ( strpos( '=<>(),.;*+-/%&|', $c ) !== false )
            {
                $tokens[] = array( 'op', $c );
                $i++;
                continue;
            }
            throw new expDBUnsupportedException( self::NAME, "the character '$c'" );
        }
        while ( $tokens && $tokens[count( $tokens ) - 1] === array( 'op', ';' ) )
            array_pop( $tokens );
        return $tokens;
    }

    // ------------------------------------------------------------------ statements

    /**
     * The kind of a statement: select, insert, update, delete, or cql (anything else, sent as it is).
     *
     * @param string $sql
     * @return string
     */
    public static function kind( $sql )
    {
        if ( preg_match( '/^\s*(?:\(\s*)*(SELECT|INSERT|UPDATE|DELETE|REPLACE)\b/i', (string)$sql, $m ) )
            return strtoupper( $m[1] ) === 'REPLACE' ? 'insert' : strtolower( $m[1] );
        return 'cql';
    }

    /**
     * Parses a statement of the subset (see the class).
     *
     * @param string $sql
     * @return array type (select, insert, update, delete) and its parts
     * @throws expDBUnsupportedException
     */
    public static function parse( $sql )
    {
        $p = new expCassandraDialectParser( self::tokenize( $sql ) );
        return $p->statement();
    }

    /**
     * A statement that is CQL already (DDL and the like) in CQL's quotes: `name` becomes "name".
     *
     * @param string $sql
     * @return string
     */
    public static function passThrough( $sql )
    {
        $out = '';
        $sql = rtrim( trim( (string)$sql ), ';' );
        $length = strlen( $sql );
        $quote = null;
        for ( $i = 0; $i < $length; $i++ )
        {
            $c = $sql[$i];
            if ( $quote !== null )
            {
                $out .= $c;
                if ( $c === $quote )
                    $quote = null;
                continue;
            }
            if ( $c === "'" || $c === '"' )
            {
                $quote = $c;
                $out .= $c;
                continue;
            }
            $out .= $c === '`' ? '"' : $c;
        }
        return trim( $out );
    }

    // ------------------------------------------------------------------ conditions

    /**
     * The conditions joined by the top-level AND of a WHERE tree (one when there is no AND at the top).
     *
     * @param array|null $where
     * @return array[]
     */
    public static function conjuncts( $where )
    {
        if ( $where === null )
            return array();
        if ( $where['t'] === 'and' )
            return array_merge( self::conjuncts( $where['l'] ), self::conjuncts( $where['r'] ) );
        return array( $where );
    }

    /**
     * A WHERE tree from a list of conditions joined by AND (null for none).
     *
     * @param array[] $conditions
     * @return array|null
     */
    public static function andOf( array $conditions )
    {
        $tree = null;
        foreach ( $conditions as $condition )
            $tree = $tree === null ? $condition : array( 't' => 'and', 'l' => $tree, 'r' => $condition );
        return $tree;
    }

    /**
     * A condition "column op constant" (or "constant op column", turned round), or null.
     *
     * @param array $condition
     * @return array|null column, op (= < > <= >= <>), value; for IN: column, op 'in', values
     */
    public static function simpleComparison( array $condition )
    {
        if ( $condition['t'] === 'cmp' )
        {
            $flip = array( '=' => '=', '<' => '>', '>' => '<', '<=' => '>=', '>=' => '<=', '<>' => '<>' );
            if ( $condition['l']['t'] === 'col' && $condition['r']['t'] === 'lit' )
                return array( 'column' => $condition['l']['name'], 'op' => $condition['op'], 'value' => $condition['r']['v'] );
            if ( $condition['r']['t'] === 'col' && $condition['l']['t'] === 'lit' )
                return array( 'column' => $condition['r']['name'], 'op' => $flip[$condition['op']], 'value' => $condition['l']['v'] );
            return null;
        }
        if ( $condition['t'] === 'in' && !$condition['not'] && $condition['e']['t'] === 'col' )
        {
            $values = array();
            foreach ( $condition['list'] as $item )
            {
                if ( $item['t'] !== 'lit' )
                    return null;
                $values[] = $item['v'];
            }
            return array( 'column' => $condition['e']['name'], 'op' => 'in', 'values' => $values );
        }
        return null;
    }

    /**
     * Whether a row passes a condition (SQL's three values: null counts as not passing).
     *
     * @param array|null $where
     * @param array $row column (lower case) => value
     * @return bool
     */
    public static function matches( $where, array $row )
    {
        return $where === null || self::truth( $where, $row ) === true;
    }

    /**
     * The value of a condition for a row: true, false or null (unknown).
     *
     * @param array $node
     * @param array $row
     * @return bool|null
     */
    public static function truth( array $node, array $row )
    {
        switch ( $node['t'] )
        {
            case 'and':
                $l = self::truth( $node['l'], $row );
                if ( $l === false )
                    return false;
                $r = self::truth( $node['r'], $row );
                if ( $r === false )
                    return false;
                return ( $l === null || $r === null ) ? null : true;
            case 'or':
                $l = self::truth( $node['l'], $row );
                if ( $l === true )
                    return true;
                $r = self::truth( $node['r'], $row );
                if ( $r === true )
                    return true;
                return ( $l === null || $r === null ) ? null : false;
            case 'not':
                $v = self::truth( $node['e'], $row );
                return $v === null ? null : !$v;
            case 'cmp':
                $a = self::value( $node['l'], $row );
                $b = self::value( $node['r'], $row );
                if ( $a === null || $b === null )
                    return null;
                $c = self::compare( $a, $b );
                switch ( $node['op'] )
                {
                    case '=': return $c === 0;
                    case '<>': return $c !== 0;
                    case '<': return $c < 0;
                    case '>': return $c > 0;
                    case '<=': return $c <= 0;
                    case '>=': return $c >= 0;
                }
                return null;
            case 'in':
                $a = self::value( $node['e'], $row );
                if ( $a === null )
                    return null;
                $unknown = false;
                foreach ( $node['list'] as $item )
                {
                    $b = self::value( $item, $row );
                    if ( $b === null )
                    {
                        $unknown = true;
                        continue;
                    }
                    if ( self::compare( $a, $b ) === 0 )
                        return !$node['not'];
                }
                return $unknown ? null : $node['not'];
            case 'like':
                $a = self::value( $node['e'], $row );
                $pattern = self::value( $node['p'], $row );
                if ( $a === null || $pattern === null )
                    return null;
                $hit = preg_match( self::likeRegex( (string)$pattern, $node['escape'] ), self::text( $a ) ) === 1;
                return $node['not'] ? !$hit : $hit;
            case 'null':
                $a = self::value( $node['e'], $row );
                return $node['not'] ? $a !== null : $a === null;
            case 'between':
                $a = self::value( $node['e'], $row );
                $low = self::value( $node['low'], $row );
                $high = self::value( $node['high'], $row );
                if ( $a === null || $low === null || $high === null )
                    return null;
                $in = self::compare( $a, $low ) >= 0 && self::compare( $a, $high ) <= 0;
                return $node['not'] ? !$in : $in;
            case 'bool':
                return $node['v'];
        }
        // a value used as a condition (WHERE 1, WHERE flags & 4): true when not zero
        $v = self::value( $node, $row );
        if ( $v === null )
            return null;
        return is_numeric( $v ) ? (float)$v != 0.0 : $v !== '';
    }

    /**
     * The value of an expression for a row.
     *
     * @param array $node
     * @param array $row
     * @return mixed
     */
    public static function value( array $node, array $row )
    {
        switch ( $node['t'] )
        {
            case 'lit':
                return $node['v'];
            case 'col':
                return array_key_exists( $node['name'], $row ) ? $row[$node['name']] : null;
            case 'neg':
                $v = self::value( $node['e'], $row );
                return $v === null ? null : -self::number( $v );
            case 'arith':
                $a = self::value( $node['l'], $row );
                $b = self::value( $node['r'], $row );
                if ( $a === null || $b === null )
                    return null;
                switch ( $node['op'] )
                {
                    case '+': return self::number( $a ) + self::number( $b );
                    case '-': return self::number( $a ) - self::number( $b );
                    case '*': return self::number( $a ) * self::number( $b );
                    case '/':
                        $d = self::number( $b );
                        return $d == 0 ? null : self::number( $a ) / $d;
                    case '%':
                        $d = (int)self::number( $b );
                        return $d === 0 ? null : (int)self::number( $a ) % $d;
                    case '&': return (int)self::number( $a ) & (int)self::number( $b );
                    case '|': return (int)self::number( $a ) | (int)self::number( $b );
                    case '||': return self::text( $a ) . self::text( $b );
                }
                return null;
            case 'func':
                $args = array();
                foreach ( $node['args'] as $arg )
                    $args[] = self::value( $arg, $row );
                switch ( $node['name'] )
                {
                    case 'LOWER':
                        return $args[0] === null ? null : mb_strtolower( self::text( $args[0] ) );
                    case 'UPPER':
                        return $args[0] === null ? null : mb_strtoupper( self::text( $args[0] ) );
                    case 'LENGTH':
                    case 'CHAR_LENGTH':
                        return $args[0] === null ? null : mb_strlen( self::text( $args[0] ) );
                    case 'COALESCE':
                    case 'IFNULL':
                        foreach ( $args as $arg )
                            if ( $arg !== null )
                                return $arg;
                        return null;
                    case 'ABS':
                        return $args[0] === null ? null : abs( self::number( $args[0] ) );
                    case 'CONCAT':
                        foreach ( $args as $arg )
                            if ( $arg === null )
                                return null;
                        return implode( '', array_map( array( __CLASS__, 'text' ), $args ) );
                    case 'SUBSTRING':
                    case 'SUBSTR':
                        if ( $args[0] === null || !isset( $args[1] ) || $args[1] === null )
                            return null;
                        $from = (int)self::number( $args[1] );
                        $text = self::text( $args[0] );
                        $start = $from > 0 ? $from - 1 : ( $from < 0 ? max( 0, mb_strlen( $text ) + $from ) : 0 );
                        if ( $from === 0 )
                            return '';
                        return isset( $args[2] ) ? ( $args[2] === null ? null : mb_substr( $text, $start, max( 0, (int)self::number( $args[2] ) ) ) ) : mb_substr( $text, $start );
                    case 'MD5':
                        return $args[0] === null ? null : md5( self::text( $args[0] ) );
                    case 'UNIX_TIMESTAMP':
                        if ( !isset( $args[0] ) )
                            return time();
                        if ( $args[0] === null )
                            return null;
                        $time = strtotime( self::text( $args[0] ) . ( preg_match( '/[zZ]|[+-]\d\d:?\d\d$/', self::text( $args[0] ) ) ? '' : ' UTC' ) );
                        return $time === false ? 0 : $time;
                    case 'FROM_UNIXTIME':
                        return $args[0] === null ? null : gmdate( 'Y-m-d H:i:s', (int)self::number( $args[0] ) );
                }
                return null;
        }
        return self::truth( $node, $row );
    }

    /**
     * SQL's comparison of two values that are not null: numbers as numbers, else text by bytes (as Cassandra orders
     * text, UTF-8 by code point).
     *
     * @param mixed $a
     * @param mixed $b
     * @return int -1, 0, 1
     */
    public static function compare( $a, $b )
    {
        if ( is_bool( $a ) )
            $a = $a ? 1 : 0;
        if ( is_bool( $b ) )
            $b = $b ? 1 : 0;
        if ( is_numeric( $a ) && is_numeric( $b ) )
        {
            // integers beyond a float's 53 bits compare exactly as text of digits
            if ( preg_match( '/^-?\d+$/', (string)$a ) && preg_match( '/^-?\d+$/', (string)$b ) && ( strlen( (string)$a ) > 15 || strlen( (string)$b ) > 15 ) )
                return self::compareIntegerText( (string)$a, (string)$b );
            $x = (float)$a;
            $y = (float)$b;
            return $x < $y ? -1 : ( $x > $y ? 1 : 0 );
        }
        $c = strcmp( self::text( $a ), self::text( $b ) );
        return $c < 0 ? -1 : ( $c > 0 ? 1 : 0 );
    }

    /**
     * Two integers written as digits, compared without loss.
     *
     * @param string $a
     * @param string $b
     * @return int
     */
    public static function compareIntegerText( $a, $b )
    {
        $na = $a[0] === '-';
        $nb = $b[0] === '-';
        if ( $na !== $nb )
            return $na ? -1 : 1;
        $a = ltrim( ltrim( $a, '-' ), '0' );
        $b = ltrim( ltrim( $b, '-' ), '0' );
        $c = strlen( $a ) <=> strlen( $b );
        if ( $c === 0 )
            $c = strcmp( $a, $b ) <=> 0;
        return $na ? -$c : $c;
    }

    /**
     * A value as text (true is 1, false 0).
     *
     * @param mixed $v
     * @return string
     */
    public static function text( $v )
    {
        if ( is_bool( $v ) )
            return $v ? '1' : '0';
        if ( is_float( $v ) )
            return self::floatText( $v );
        if ( is_array( $v ) )
            return json_encode( $v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        return (string)$v;
    }

    /**
     * A value as a number (a text that is none is 0, as in MySQL).
     *
     * @param mixed $v
     * @return int|float
     */
    public static function number( $v )
    {
        if ( is_int( $v ) || is_float( $v ) )
            return $v;
        if ( is_bool( $v ) )
            return $v ? 1 : 0;
        $v = trim( (string)$v );
        if ( preg_match( '/^-?\d+$/', $v ) && strlen( $v ) < 19 )
            return (int)$v;
        return is_numeric( $v ) ? (float)$v : 0;
    }

    /**
     * A float as plain digits (no exponent, no trailing zeros).
     *
     * @param float $v
     * @return string
     */
    public static function floatText( $v )
    {
        if ( is_nan( $v ) || is_infinite( $v ) )
            return (string)$v;
        if ( $v == floor( $v ) && abs( $v ) < 1e15 )
            return (string)(int)$v;
        $text = rtrim( rtrim( sprintf( '%.15F', $v ), '0' ), '.' );
        return $text === '-0' ? '0' : $text;
    }

    /**
     * The regular expression of a LIKE pattern (% any run, _ one character, the escape character before either).
     *
     * @param string $pattern
     * @param string|null $escape
     * @return string
     */
    public static function likeRegex( $pattern, $escape = null )
    {
        $regex = '';
        $chars = preg_split( '//u', (string)$pattern, -1, PREG_SPLIT_NO_EMPTY );
        if ( $chars === false )
            $chars = str_split( (string)$pattern );
        $count = count( $chars );
        for ( $i = 0; $i < $count; $i++ )
        {
            $c = $chars[$i];
            if ( $escape !== null && $escape !== '' && $c === $escape && $i + 1 < $count )
            {
                $regex .= preg_quote( $chars[++$i], '/' );
                continue;
            }
            if ( $c === '%' )
                $regex .= '.*';
            elseif ( $c === '_' )
                $regex .= '.';
            else
                $regex .= preg_quote( $c, '/' );
        }
        return '/^' . $regex . '$/su';
    }

    /**
     * The columns a WHERE tree or an expression reads.
     *
     * @param array|null $node
     * @return string[]
     */
    public static function columnsOf( $node )
    {
        if ( !is_array( $node ) )
            return array();
        if ( isset( $node['t'] ) && $node['t'] === 'col' )
            return array( $node['name'] );
        $out = array();
        foreach ( $node as $key => $child )
            if ( is_array( $child ) )
                $out = array_merge( $out, self::columnsOf( $child ) );
        return array_values( array_unique( $out ) );
    }

    // ------------------------------------------------------------------ rows

    /**
     * Sorts rows by ORDER BY (pure): nulls first ascending, last descending, as MySQL, SQLite and Cassandra do.
     *
     * @param array[] $rows
     * @param array[] $order array( expr, desc )
     * @return array[]
     */
    public static function sortRows( array $rows, array $order )
    {
        if ( !$order || count( $rows ) < 2 )
            return $rows;
        $keyed = array();
        foreach ( $rows as $i => $row )
        {
            $keys = array();
            foreach ( $order as $o )
                $keys[] = self::value( $o['expr'], $row );
            $keyed[] = array( $keys, $i, $row );
        }
        usort( $keyed, function ( $x, $y ) use ( $order ) {
            foreach ( $order as $n => $o )
            {
                $a = $x[0][$n];
                $b = $y[0][$n];
                if ( $a === null && $b === null )
                    $c = 0;
                elseif ( $a === null )
                    $c = -1;
                elseif ( $b === null )
                    $c = 1;
                else
                    $c = expCassandraDialect::compare( $a, $b );
                if ( $c !== 0 )
                    return $o['desc'] ? -$c : $c;
            }
            return $x[1] <=> $y[1];
        } );
        $out = array();
        foreach ( $keyed as $k )
            $out[] = $k[2];
        return $out;
    }

    /**
     * The result rows of a SELECT from the rows its WHERE kept (pure): the select list, DISTINCT, the aggregates,
     * ORDER BY, OFFSET and LIMIT.
     *
     * @param array $select a parsed SELECT
     * @param array[] $rows column => value
     * @param array $columns the table's columns in order (for *)
     * @param int|false $limit the limit of the call (arrayQuery's params) over the statement's
     * @param int $offset
     * @return array[]
     */
    public static function project( array $select, array $rows, array $columns, $limit = false, $offset = 0 )
    {
        $aggregate = false;
        foreach ( $select['columns'] as $item )
            if ( $item['expr']['t'] === 'agg' )
                $aggregate = true;
        if ( $aggregate )
        {
            $row = array();
            foreach ( $select['columns'] as $item )
                $row[$item['alias']] = $item['expr']['t'] === 'agg' ? self::aggregate( $item['expr'], $rows ) : ( $rows ? self::output( self::value( $item['expr'], $rows[0] ) ) : null );
            return array( $row );
        }
        $order = $select['order'];
        // ORDER BY names an alias of the select list or its position
        foreach ( $order as $n => $o )
        {
            if ( $o['expr']['t'] === 'lit' && is_int( $o['expr']['v'] ) && isset( $select['columns'][$o['expr']['v'] - 1] ) )
                $order[$n]['expr'] = $select['columns'][$o['expr']['v'] - 1]['expr'];
            elseif ( $o['expr']['t'] === 'col' )
                foreach ( $select['columns'] as $item )
                    if ( $item['alias'] === $o['expr']['name'] && $item['expr']['t'] !== 'star' && !in_array( $o['expr']['name'], $columns, true ) )
                        $order[$n]['expr'] = $item['expr'];
        }
        $rows = self::sortRows( $rows, $order );
        $out = array();
        foreach ( $rows as $row )
        {
            $line = array();
            foreach ( $select['columns'] as $item )
            {
                if ( $item['expr']['t'] === 'star' )
                {
                    foreach ( $columns as $column )
                        $line[$column] = array_key_exists( $column, $row ) ? self::output( $row[$column] ) : null;
                    continue;
                }
                $line[$item['alias']] = self::output( self::value( $item['expr'], $row ) );
            }
            $out[] = $line;
        }
        if ( $select['distinct'] )
        {
            $seen = array();
            $unique = array();
            foreach ( $out as $line )
            {
                $key = serialize( $line );
                if ( !isset( $seen[$key] ) )
                {
                    $seen[$key] = true;
                    $unique[] = $line;
                }
            }
            $out = $unique;
        }
        $offset = max( 0, (int)$offset ) + (int)$select['offset'];
        $limits = array();
        if ( $select['limit'] !== null )
            $limits[] = (int)$select['limit'];
        if ( $limit !== false && $limit !== null )
            $limits[] = (int)$limit;
        $length = $limits ? min( $limits ) : null;
        if ( $offset > 0 || $length !== null )
            $out = array_slice( $out, $offset, $length );
        return $out;
    }

    /**
     * An aggregate over rows: COUNT(*), COUNT([DISTINCT] e), MIN, MAX, SUM, AVG.
     *
     * @param array $agg
     * @param array[] $rows
     * @return mixed
     */
    public static function aggregate( array $agg, array $rows )
    {
        if ( $agg['fn'] === 'COUNT' && $agg['arg'] === null )
            return (string)count( $rows );
        $values = array();
        foreach ( $rows as $row )
        {
            $v = self::value( $agg['arg'], $row );
            if ( $v !== null )
                $values[] = $v;
        }
        if ( $agg['distinct'] )
        {
            $unique = array();
            foreach ( $values as $v )
                $unique[self::text( $v )] = $v;
            $values = array_values( $unique );
        }
        switch ( $agg['fn'] )
        {
            case 'COUNT':
                return (string)count( $values );
            case 'MIN':
            case 'MAX':
                if ( !$values )
                    return null;
                $best = array_shift( $values );
                foreach ( $values as $v )
                {
                    $c = self::compare( $v, $best );
                    if ( ( $agg['fn'] === 'MIN' && $c < 0 ) || ( $agg['fn'] === 'MAX' && $c > 0 ) )
                        $best = $v;
                }
                return self::output( $best );
            case 'SUM':
                if ( !$values )
                    return null;
                $sum = 0;
                foreach ( $values as $v )
                    $sum += self::number( $v );
                return self::output( $sum );
            case 'AVG':
                if ( !$values )
                    return null;
                $sum = 0;
                foreach ( $values as $v )
                    $sum += self::number( $v );
                return self::output( $sum / count( $values ) );
        }
        return null;
    }

    /**
     * A value as the kernel's drivers return it: null, or text (a boolean 1 or 0, a float in plain digits, a
     * collection as JSON).
     *
     * @param mixed $v
     * @return string|null
     */
    public static function output( $v )
    {
        return $v === null ? null : self::text( $v );
    }

    // ------------------------------------------------------------------ types

    /**
     * The CQL type of a .dba field (pure).
     *
     * @param array $field type, length
     * @return string
     */
    public static function cqlType( array $field )
    {
        $type = strtolower( isset( $field['type'] ) ? (string)$field['type'] : 'longtext' );
        switch ( $type )
        {
            case 'auto_increment':
            case 'int':
            case 'integer':
            case 'mediumint':
                return 'int';
            case 'smallint':
            case 'tinyint':
                return 'int';
            case 'bigint':
                return 'bigint';
            case 'float':
            case 'double':
            case 'real':
                return 'double';
            case 'decimal':
            case 'numeric':
                return 'decimal';
            case 'boolean':
            case 'bool':
                return 'boolean';
            case 'blob':
            case 'longblob':
            case 'mediumblob':
            case 'binary':
            case 'varbinary':
                return 'blob';
        }
        // char, varchar, text, longtext, date and time as the kernel keeps them (text or integers): text
        return 'text';
    }

    /**
     * The .dba field of a CQL column (pure): the integer types, double, decimal, boolean as int, text with a length
     * when it is a key, timestamps and dates as text, blobs, and the collections and the rest as long text (JSON).
     *
     * @param string $cqlType as system_schema.columns gives it (int, text, frozen<list<int>> ...)
     * @param bool $key whether it is a column of the primary key (a key cannot be a long text everywhere)
     * @return array
     */
    public static function dbaField( $cqlType, $key = false )
    {
        $type = strtolower( trim( (string)$cqlType ) );
        $notNull = $key ? '1' : '0';
        switch ( $type )
        {
            case 'int':
            case 'smallint':
            case 'tinyint':
                return array( 'length' => 11, 'type' => 'int', 'not_null' => $notNull, 'default' => $key ? 0 : null );
            case 'bigint':
            case 'counter':
                return array( 'length' => 20, 'type' => 'bigint', 'not_null' => $notNull, 'default' => $key ? 0 : null );
            case 'varint':
                return array( 'length' => '38,0', 'type' => 'decimal', 'not_null' => $notNull, 'default' => $key ? 0 : null );
            case 'float':
            case 'double':
                return array( 'type' => 'double', 'not_null' => $notNull, 'default' => $key ? 0 : null );
            case 'decimal':
                return array( 'length' => '38,10', 'type' => 'decimal', 'not_null' => $notNull, 'default' => $key ? 0 : null );
            case 'boolean':
                return array( 'length' => 11, 'type' => 'int', 'not_null' => $notNull, 'default' => $key ? 0 : null );
            case 'uuid':
            case 'timeuuid':
                return array( 'length' => 36, 'type' => 'char', 'not_null' => $notNull, 'default' => $key ? '' : null );
            case 'timestamp':
                return array( 'length' => 30, 'type' => 'varchar', 'not_null' => $notNull, 'default' => $key ? '' : null );
            case 'date':
                return array( 'length' => 10, 'type' => 'varchar', 'not_null' => $notNull, 'default' => $key ? '' : null );
            case 'time':
            case 'duration':
                return array( 'length' => 30, 'type' => 'varchar', 'not_null' => $notNull, 'default' => $key ? '' : null );
            case 'inet':
                return array( 'length' => 45, 'type' => 'varchar', 'not_null' => $notNull, 'default' => $key ? '' : null );
            case 'text':
            case 'varchar':
            case 'ascii':
                if ( $key )
                    return array( 'length' => 255, 'type' => 'varchar', 'not_null' => '1', 'default' => '' );
                return array( 'type' => 'longtext', 'not_null' => '0', 'default' => null );
        }
        // blob (base64), collections, tuples, user types, vectors (JSON)
        return array( 'type' => 'longtext', 'not_null' => '0', 'default' => null );
    }

    /**
     * A value for a column of a CQL type, as PHP gives it to the driver of the protocol (pure): integers, floats,
     * booleans, text; decimals and varints as digits; an empty text in a number column is null, as Cassandra has no
     * other way to keep "no number".
     *
     * @param mixed $value
     * @param string $cqlType
     * @return mixed
     */
    public static function bindValue( $value, $cqlType )
    {
        if ( $value === null )
            return null;
        $type = strtolower( trim( (string)$cqlType ) );
        switch ( $type )
        {
            case 'int':
            case 'smallint':
            case 'tinyint':
            case 'bigint':
            case 'counter':
                if ( is_bool( $value ) )
                    return $value ? 1 : 0;
                if ( is_string( $value ) && trim( $value ) === '' )
                    return null;
                $n = self::number( $value );
                return is_float( $n ) ? (int)round( $n ) : $n;
            case 'varint':
                if ( is_string( $value ) && trim( $value ) === '' )
                    return null;
                return preg_match( '/^-?\d+$/', trim( (string)$value ) ) ? trim( (string)$value ) : (string)(int)self::number( $value );
            case 'float':
            case 'double':
                if ( is_string( $value ) && trim( $value ) === '' )
                    return null;
                return (float)self::number( $value );
            case 'decimal':
                if ( is_string( $value ) && trim( $value ) === '' )
                    return null;
                return is_numeric( trim( (string)$value ) ) ? trim( (string)$value ) : '0';
            case 'boolean':
                if ( is_bool( $value ) )
                    return $value;
                return !in_array( strtolower( trim( (string)$value ) ), array( '', '0', 'false', 'f', 'no', 'off' ), true );
            case 'blob':
                return (string)$value;
        }
        if ( is_bool( $value ) )
            return $value ? '1' : '0';
        if ( is_float( $value ) )
            return self::floatText( $value );
        return is_array( $value ) ? json_encode( $value ) : (string)$value;
    }

    /**
     * The insert-or-update of one row (expDBSQLHelpers::upsert()) in the SQL the driver answers: REPLACE (every column
     * written; what an INSERT of CQL does), INSERT IGNORE (insert only), or ON DUPLICATE KEY UPDATE of the update
     * columns (the driver reads the key and updates them, or inserts the row).
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
        $columns = implode( ', ', array_map( array( __CLASS__, 'quoteName' ), array_keys( $row ) ) );
        $values = implode( ', ', array_map( array( __CLASS__, 'literal' ), array_values( $row ) ) );
        $t = self::quoteName( $table );
        $all = array_values( array_diff( array_keys( $row ), $keyColumns ) );
        if ( $updateColumns === null || ( $updateColumns && !array_diff( $all, $updateColumns ) ) )
            return "REPLACE INTO $t ($columns) VALUES ($values)";
        if ( !$updateColumns )
            return "INSERT IGNORE INTO $t ($columns) VALUES ($values)";
        $set = array();
        foreach ( $updateColumns as $column )
            $set[] = self::quoteName( $column ) . ' = VALUES(' . self::quoteName( $column ) . ')';
        return "INSERT INTO $t ($columns) VALUES ($values) ON DUPLICATE KEY UPDATE " . implode( ', ', $set );
    }

    /**
     * A name for CQL: lower case, in double quotes.
     *
     * @param string $name
     * @return string
     */
    public static function quoteName( $name )
    {
        $name = strtolower( trim( (string)$name, '`" ' ) );
        if ( $name === '' || strpos( $name, "\0" ) !== false )
            throw new InvalidArgumentException( 'An empty name or a name with a NUL byte is no identifier' );
        return '"' . str_replace( '"', '""', $name ) . '"';
    }

    /**
     * A text literal for CQL.
     *
     * @param string $text
     * @return string
     */
    public static function stringLiteral( $text )
    {
        return "'" . str_replace( "'", "''", (string)$text ) . "'";
    }

    /**
     * A PHP value as an SQL literal of this dialect.
     *
     * @param mixed $value
     * @return string
     */
    public static function literal( $value )
    {
        return expDBSQLHelpers::literal( function ( $text ) { return str_replace( "'", "''", $text ); }, $value );
    }

    /**
     * The category of a server error: syntax, invalid (the server refused the statement: filtering, a type, an
     * unknown column), unavailable (too few replicas, a timeout, overload), auth, connection, duplicate, other.
     *
     * @param int|null $code the protocol's error code
     * @param string $message
     * @return string
     */
    public static function errorCategory( $code, $message )
    {
        switch ( (int)$code )
        {
            case 0x2000:
                return 'syntax';
            case 0x2200:
            case 0x2300:
            case 0x2400:
            case 0x2500:
                return 'invalid';
            case 0x1000:
            case 0x1001:
            case 0x1002:
            case 0x1100:
            case 0x1200:
            case 0x1300:
            case 0x1400:
            case 0x1500:
                return 'unavailable';
            case 0x0100:
            case 0x2100:
                return 'auth';
            case 1062:
                return 'duplicate';
        }
        $text = strtolower( (string)$message );
        if ( strpos( $text, 'duplicate' ) !== false )
            return 'duplicate';
        if ( strpos( $text, 'connection' ) !== false || strpos( $text, 'stream' ) !== false || strpos( $text, 'socket' ) !== false )
            return 'connection';
        return 'other';
    }
}

/**
 * The parser behind expCassandraDialect::parse(): a recursive descent over the tokens. Nodes are arrays with 't':
 * lit (v), col (name, lower case, without a table qualifier), star, agg (fn, arg, distinct), func (name, args),
 * neg (e), arith (op, l, r), cmp (op, l, r), and/or (l, r), not (e), in (e, list, not), like (e, p, escape, not),
 * null (e, not), between (e, low, high, not), bool (v).
 *
 * @since 6.0.15
 * @package lib
 */
class expCassandraDialectParser
{
    /** @var array[] */
    protected $tokens;

    /** @var int */
    protected $i = 0;

    /** @var string the table's alias, which qualifies its columns */
    protected $alias = '';

    /** @var string */
    protected $table = '';

    public function __construct( array $tokens )
    {
        $this->tokens = $tokens;
    }

    /**
     * @return array
     * @throws expDBUnsupportedException
     */
    public function statement()
    {
        $word = $this->peekWord();
        switch ( $word )
        {
            case 'SELECT':
                $result = $this->select();
                break;
            case 'INSERT':
            case 'REPLACE':
                $result = $this->insert();
                break;
            case 'UPDATE':
                $result = $this->update();
                break;
            case 'DELETE':
                $result = $this->delete();
                break;
            default:
                throw $this->unsupported( 'the statement ' . ( $word === '' ? '' : $word ) );
        }
        if ( $this->i < count( $this->tokens ) )
            throw $this->unsupported( 'the words after ' . $this->describe( $this->i - 1 ) . ' (' . $this->describe( $this->i ) . ')' );
        return $result;
    }

    protected function select()
    {
        $this->expectWord( 'SELECT' );
        $distinct = false;
        if ( $this->acceptWord( 'DISTINCT' ) )
            $distinct = true;
        else
            $this->acceptWord( 'ALL' );
        // the select list is read after the table, which names the alias its columns may carry
        $listStart = $this->i;
        $depth = 0;
        while ( $this->i < count( $this->tokens ) )
        {
            $t = $this->tokens[$this->i];
            if ( $t === array( 'op', '(' ) )
                $depth++;
            elseif ( $t === array( 'op', ')' ) )
                $depth--;
            elseif ( $depth === 0 && $t[0] === 'word' && strtoupper( $t[1] ) === 'FROM' )
                break;
            $this->i++;
        }
        $listEnd = $this->i;
        $table = null;
        if ( $this->acceptWord( 'FROM' ) )
        {
            if ( $this->peekOp( '(' ) )
                throw $this->unsupported( 'a sub-select' );
            $table = $this->name();
            $this->table = $table;
            if ( $this->acceptWord( 'AS' ) )
                $this->alias = $this->name();
            elseif ( $this->peekType( 'word' ) && !in_array( $this->peekWord(), expCassandraDialect::STOP_WORDS, true ) )
                $this->alias = $this->name();
            if ( $this->peekOp( ',' ) || in_array( $this->peekWord(), array( 'JOIN', 'INNER', 'LEFT', 'RIGHT', 'FULL', 'CROSS', 'NATURAL' ), true ) )
                throw $this->unsupported( 'a join' );
        }
        $afterFrom = $this->i;
        $this->i = $listStart;
        $columns = $this->selectList( $listEnd );
        $this->i = $afterFrom;
        $where = null;
        if ( $this->acceptWord( 'WHERE' ) )
            $where = $this->expr();
        if ( $this->peekWord() === 'GROUP' )
            throw $this->unsupported( 'GROUP BY' );
        if ( $this->peekWord() === 'HAVING' )
            throw $this->unsupported( 'HAVING' );
        $order = array();
        if ( $this->acceptWord( 'ORDER' ) )
        {
            $this->expectWord( 'BY' );
            do
            {
                $expr = $this->additive();
                $desc = false;
                if ( $this->acceptWord( 'DESC' ) )
                    $desc = true;
                else
                    $this->acceptWord( 'ASC' );
                $order[] = array( 'expr' => $expr, 'desc' => $desc );
            }
            while ( $this->acceptOp( ',' ) );
        }
        $limit = null;
        $offset = 0;
        if ( $this->acceptWord( 'LIMIT' ) )
        {
            if ( $this->acceptWord( 'ALL' ) )
                $limit = null;
            else
            {
                $first = $this->integer();
                if ( $this->acceptOp( ',' ) )
                {
                    $offset = $first;
                    $limit = $this->integer();
                }
                else
                    $limit = $first;
            }
        }
        if ( $this->acceptWord( 'OFFSET' ) )
        {
            $offset = $this->integer();
            $this->acceptWord( 'ROWS' ) || $this->acceptWord( 'ROW' );
        }
        if ( $this->peekWord() === 'UNION' )
            throw $this->unsupported( 'UNION' );
        if ( $this->acceptWord( 'FOR' ) )
        {
            // FOR UPDATE: Cassandra has no row locks
            $this->expectWord( 'UPDATE' );
        }
        return array( 'type' => 'select', 'table' => $table, 'alias' => $this->alias, 'distinct' => $distinct, 'columns' => $columns,
                      'where' => $where, 'order' => $order, 'limit' => $limit, 'offset' => $offset );
    }

    protected function selectList( $end )
    {
        $columns = array();
        $n = 0;
        while ( $this->i < $end )
        {
            $n++;
            if ( $this->peekOp( '*' ) )
            {
                $this->i++;
                $columns[] = array( 'expr' => array( 't' => 'star' ), 'alias' => '*' );
            }
            elseif ( $this->peekType( 'word' ) && isset( $this->tokens[$this->i + 2] ) && $this->tokens[$this->i + 1] === array( 'op', '.' )
                     && $this->tokens[$this->i + 2] === array( 'op', '*' ) )
            {
                $this->i += 3;
                $columns[] = array( 'expr' => array( 't' => 'star' ), 'alias' => '*' );
            }
            else
            {
                $start = $this->i;
                $expr = $this->expr();
                $alias = null;
                if ( $this->acceptWord( 'AS' ) )
                    $alias = $this->name();
                elseif ( $this->i < $end && ( $this->peekType( 'word' ) || $this->peekType( 'quoted' ) ) && !in_array( $this->peekWord(), expCassandraDialect::STOP_WORDS, true ) )
                    $alias = $this->name();
                if ( $alias === null )
                    $alias = $expr['t'] === 'col' ? $expr['name'] : $this->sourceText( $start, $this->i );
                $columns[] = array( 'expr' => $expr, 'alias' => $alias );
            }
            if ( $this->i < $end )
                $this->expectOp( ',' );
        }
        if ( !$columns )
            throw $this->unsupported( 'an empty select list' );
        return $columns;
    }

    protected function insert()
    {
        $replace = $this->peekWord() === 'REPLACE';
        $this->i++;
        $ignore = false;
        if ( $this->acceptWord( 'IGNORE' ) )
            $ignore = true;
        if ( $this->acceptWord( 'OR' ) )
        {
            // SQLite's INSERT OR REPLACE / INSERT OR IGNORE
            $what = $this->peekWord();
            $this->i++;
            if ( $what === 'REPLACE' )
                $replace = true;
            elseif ( $what === 'IGNORE' )
                $ignore = true;
            else
                throw $this->unsupported( 'INSERT OR ' . $what );
        }
        $this->acceptWord( 'INTO' );
        $table = $this->name();
        $this->table = $table;
        if ( !$this->acceptOp( '(' ) )
            throw $this->unsupported( 'an INSERT without its list of columns' );
        $columns = array();
        do
        {
            $columns[] = $this->columnName();
        }
        while ( $this->acceptOp( ',' ) );
        $this->expectOp( ')' );
        if ( $this->peekWord() === 'SELECT' )
            throw $this->unsupported( 'INSERT ... SELECT' );
        $this->expectWord( 'VALUES' );
        $rows = array();
        do
        {
            $this->expectOp( '(' );
            $values = array();
            do
            {
                $values[] = $this->constant();
            }
            while ( $this->acceptOp( ',' ) );
            $this->expectOp( ')' );
            if ( count( $values ) !== count( $columns ) )
                throw $this->unsupported( 'an INSERT whose values do not match its columns' );
            $rows[] = array_combine( $columns, $values );
        }
        while ( $this->acceptOp( ',' ) );
        $result = array( 'type' => 'insert', 'table' => $table, 'columns' => $columns, 'rows' => $rows, 'ignore' => $ignore, 'replace' => $replace );
        if ( $this->acceptWord( 'ON' ) )
        {
            // MySQL's ON DUPLICATE KEY UPDATE col = VALUES(col), ...: the columns written over an existing row
            if ( !$this->acceptWord( 'DUPLICATE' ) )
                throw $this->unsupported( 'ON CONFLICT (use sqlUpsert())' );
            $this->expectWord( 'KEY' );
            $this->expectWord( 'UPDATE' );
            $update = array();
            do
            {
                $column = $this->columnName();
                $this->expectOp( '=' );
                $this->expectWord( 'VALUES' );
                $this->expectOp( '(' );
                if ( $this->columnName() !== $column )
                    throw $this->unsupported( 'ON DUPLICATE KEY UPDATE with another value than VALUES() of the same column' );
                $this->expectOp( ')' );
                $update[] = $column;
            }
            while ( $this->acceptOp( ',' ) );
            $result['update_columns'] = $update;
        }
        return $result;
    }

    protected function update()
    {
        $this->expectWord( 'UPDATE' );
        $table = $this->name();
        $this->table = $table;
        if ( $this->peekType( 'word' ) && $this->peekWord() !== 'SET' )
            $this->alias = $this->name();
        $this->expectWord( 'SET' );
        $set = array();
        do
        {
            $column = $this->columnName();
            $this->expectOp( '=' );
            $set[$column] = $this->bitwise();
        }
        while ( $this->acceptOp( ',' ) );
        $where = null;
        if ( $this->acceptWord( 'WHERE' ) )
            $where = $this->expr();
        if ( $this->peekWord() === 'ORDER' || $this->peekWord() === 'LIMIT' )
            throw $this->unsupported( 'UPDATE with ORDER BY or LIMIT' );
        return array( 'type' => 'update', 'table' => $table, 'set' => $set, 'where' => $where );
    }

    protected function delete()
    {
        $this->expectWord( 'DELETE' );
        $this->expectWord( 'FROM' );
        $table = $this->name();
        $this->table = $table;
        if ( $this->peekType( 'word' ) && $this->peekWord() !== 'WHERE' )
            $this->alias = $this->name();
        $where = null;
        if ( $this->acceptWord( 'WHERE' ) )
            $where = $this->expr();
        if ( $this->peekWord() === 'ORDER' || $this->peekWord() === 'LIMIT' )
            throw $this->unsupported( 'DELETE with ORDER BY or LIMIT' );
        return array( 'type' => 'delete', 'table' => $table, 'where' => $where );
    }

    // ------------------------------------------------------------------ expressions

    public function expr()
    {
        $left = $this->conjunction();
        while ( $this->acceptWord( 'OR' ) )
            $left = array( 't' => 'or', 'l' => $left, 'r' => $this->conjunction() );
        return $left;
    }

    protected function conjunction()
    {
        $left = $this->negation();
        while ( $this->acceptWord( 'AND' ) )
            $left = array( 't' => 'and', 'l' => $left, 'r' => $this->negation() );
        return $left;
    }

    protected function negation()
    {
        if ( $this->acceptWord( 'NOT' ) )
            return array( 't' => 'not', 'e' => $this->negation() );
        return $this->predicate();
    }

    protected function predicate()
    {
        if ( $this->peekWord() === 'EXISTS' )
            throw $this->unsupported( 'EXISTS (a sub-select)' );
        $left = $this->bitwise();
        $not = false;
        if ( $this->peekWord() === 'NOT' && in_array( $this->peekWord( 1 ), array( 'IN', 'LIKE', 'BETWEEN' ), true ) )
        {
            $this->i++;
            $not = true;
        }
        if ( $this->acceptWord( 'IN' ) )
        {
            $this->expectOp( '(' );
            if ( $this->peekWord() === 'SELECT' )
                throw $this->unsupported( 'IN (a sub-select)' );
            $list = array();
            if ( !$this->peekOp( ')' ) )
            {
                do
                {
                    $list[] = $this->bitwise();
                }
                while ( $this->acceptOp( ',' ) );
            }
            $this->expectOp( ')' );
            return array( 't' => 'in', 'e' => $left, 'list' => $list, 'not' => $not );
        }
        if ( $this->acceptWord( 'LIKE' ) )
        {
            $pattern = $this->bitwise();
            $escape = null;
            if ( $this->acceptWord( 'ESCAPE' ) )
            {
                $e = $this->constant();
                $escape = (string)$e['v'];
            }
            return array( 't' => 'like', 'e' => $left, 'p' => $pattern, 'escape' => $escape, 'not' => $not );
        }
        if ( $this->acceptWord( 'BETWEEN' ) )
        {
            $low = $this->bitwise();
            $this->expectWord( 'AND' );
            $high = $this->bitwise();
            return array( 't' => 'between', 'e' => $left, 'low' => $low, 'high' => $high, 'not' => $not );
        }
        if ( $this->acceptWord( 'IS' ) )
        {
            $isNot = $this->acceptWord( 'NOT' );
            $this->expectWord( 'NULL' );
            return array( 't' => 'null', 'e' => $left, 'not' => $isNot );
        }
        foreach ( array( '=', '<>', '!=', '<=', '>=', '<', '>' ) as $op )
        {
            if ( $this->acceptOp( $op ) )
            {
                if ( $this->peekOp( '(' ) && $this->peekWord( 1 ) === 'SELECT' )
                    throw $this->unsupported( 'a sub-select' );
                if ( in_array( $this->peekWord(), array( 'ANY', 'ALL', 'SOME' ), true ) )
                    throw $this->unsupported( $this->peekWord() . ' (a sub-select)' );
                return array( 't' => 'cmp', 'op' => $op === '!=' ? '<>' : $op, 'l' => $left, 'r' => $this->bitwise() );
            }
        }
        return $left;
    }

    protected function bitwise()
    {
        $left = $this->additive();
        while ( $this->peekOp( '&' ) || $this->peekOp( '|' ) || $this->peekOp( '||' ) )
        {
            $op = $this->tokens[$this->i++][1];
            $left = array( 't' => 'arith', 'op' => $op, 'l' => $left, 'r' => $this->additive() );
        }
        return $left;
    }

    protected function additive()
    {
        $left = $this->multiplicative();
        while ( $this->peekOp( '+' ) || $this->peekOp( '-' ) )
        {
            $op = $this->tokens[$this->i++][1];
            $left = array( 't' => 'arith', 'op' => $op, 'l' => $left, 'r' => $this->multiplicative() );
        }
        return $left;
    }

    protected function multiplicative()
    {
        $left = $this->unary();
        while ( $this->peekOp( '*' ) || $this->peekOp( '/' ) || $this->peekOp( '%' ) )
        {
            $op = $this->tokens[$this->i++][1];
            $left = array( 't' => 'arith', 'op' => $op, 'l' => $left, 'r' => $this->unary() );
        }
        return $left;
    }

    protected function unary()
    {
        if ( $this->acceptOp( '-' ) )
        {
            $e = $this->unary();
            if ( $e['t'] === 'lit' && ( is_int( $e['v'] ) || is_float( $e['v'] ) ) )
                return array( 't' => 'lit', 'v' => -$e['v'] );
            if ( $e['t'] === 'lit' && is_string( $e['v'] ) && preg_match( '/^\d+$/', $e['v'] ) )
                return array( 't' => 'lit', 'v' => '-' . $e['v'] );
            return array( 't' => 'neg', 'e' => $e );
        }
        if ( $this->acceptOp( '+' ) )
            return $this->unary();
        return $this->primary();
    }

    protected function primary()
    {
        if ( $this->i >= count( $this->tokens ) )
            throw $this->unsupported( 'a statement that ends too early' );
        $t = $this->tokens[$this->i];
        if ( $t === array( 'op', '(' ) )
        {
            $this->i++;
            if ( $this->peekWord() === 'SELECT' )
                throw $this->unsupported( 'a sub-select' );
            $e = $this->expr();
            $this->expectOp( ')' );
            return $e;
        }
        if ( $t[0] === 'string' )
        {
            $this->i++;
            return array( 't' => 'lit', 'v' => $t[1] );
        }
        if ( $t[0] === 'number' )
        {
            $this->i++;
            return array( 't' => 'lit', 'v' => self::numberValue( $t[1] ) );
        }
        if ( $t[0] === 'param' )
            throw $this->unsupported( 'a bound parameter (?) in SQL: values are written into the statement' );
        if ( $t[0] === 'word' )
        {
            $upper = strtoupper( $t[1] );
            if ( $upper === 'NULL' )
            {
                $this->i++;
                return array( 't' => 'lit', 'v' => null );
            }
            if ( $upper === 'TRUE' || $upper === 'FALSE' )
            {
                $this->i++;
                return array( 't' => 'lit', 'v' => $upper === 'TRUE' ? 1 : 0 );
            }
            if ( $upper === 'CASE' )
                throw $this->unsupported( 'CASE' );
            if ( isset( $this->tokens[$this->i + 1] ) && $this->tokens[$this->i + 1] === array( 'op', '(' ) )
                return $this->call();
        }
        if ( $t[0] === 'word' || $t[0] === 'quoted' )
            return array( 't' => 'col', 'name' => $this->columnName() );
        throw $this->unsupported( 'the expression ' . $this->describe( $this->i ) );
    }

    protected function call()
    {
        $name = strtoupper( $this->tokens[$this->i][1] );
        $this->i += 2;
        if ( in_array( $name, expCassandraDialect::AGGREGATES, true ) )
        {
            $distinct = $this->acceptWord( 'DISTINCT' );
            if ( $name === 'COUNT' && $this->acceptOp( '*' ) )
                $arg = null;
            else
                $arg = $this->expr();
            $this->expectOp( ')' );
            return array( 't' => 'agg', 'fn' => $name, 'arg' => $arg, 'distinct' => $distinct );
        }
        if ( !in_array( $name, expCassandraDialect::FUNCTIONS, true ) )
            throw $this->unsupported( "the function $name()" );
        $args = array();
        if ( !$this->peekOp( ')' ) )
        {
            do
            {
                $args[] = $this->expr();
            }
            while ( $this->acceptOp( ',' ) );
        }
        $this->expectOp( ')' );
        if ( !$args && $name !== 'UNIX_TIMESTAMP' )
            throw $this->unsupported( "$name() without arguments" );
        return array( 't' => 'func', 'name' => $name, 'args' => $args );
    }

    /** A constant of a VALUES list: a literal, a negative number, NULL */
    protected function constant()
    {
        $e = $this->bitwise();
        if ( $e['t'] !== 'lit' )
            throw $this->unsupported( 'a value that is not a constant in VALUES' );
        return $e;
    }

    // ------------------------------------------------------------------ names

    /** A table's name, without a keyspace or schema qualifier */
    protected function name()
    {
        $t = isset( $this->tokens[$this->i] ) ? $this->tokens[$this->i] : null;
        if ( $t === null || ( $t[0] !== 'word' && $t[0] !== 'quoted' ) )
            throw $this->unsupported( 'a name expected at ' . $this->describe( $this->i ) );
        $this->i++;
        $name = $t[1];
        while ( $this->peekOp( '.' ) && isset( $this->tokens[$this->i + 1] ) && in_array( $this->tokens[$this->i + 1][0], array( 'word', 'quoted' ), true ) )
        {
            $this->i++;
            $name = $this->tokens[$this->i++][1];
        }
        return strtolower( $name );
    }

    /** A column's name, without its table or alias */
    protected function columnName()
    {
        $name = $this->name();
        return $name;
    }

    protected static function numberValue( $text )
    {
        if ( preg_match( '/^\d+$/', $text ) )
            return strlen( $text ) < 19 ? (int)$text : $text;
        return (float)$text;
    }

    protected function integer()
    {
        $t = isset( $this->tokens[$this->i] ) ? $this->tokens[$this->i] : null;
        if ( $t === null || $t[0] !== 'number' || !ctype_digit( $t[1] ) )
            throw $this->unsupported( 'a number expected at ' . $this->describe( $this->i ) );
        $this->i++;
        return (int)$t[1];
    }

    // ------------------------------------------------------------------ tokens

    protected function peekWord( $ahead = 0 )
    {
        $t = isset( $this->tokens[$this->i + $ahead] ) ? $this->tokens[$this->i + $ahead] : null;
        return $t !== null && $t[0] === 'word' ? strtoupper( $t[1] ) : '';
    }

    protected function peekType( $type )
    {
        return isset( $this->tokens[$this->i] ) && $this->tokens[$this->i][0] === $type;
    }

    protected function peekOp( $op )
    {
        return isset( $this->tokens[$this->i] ) && $this->tokens[$this->i] === array( 'op', $op );
    }

    protected function acceptWord( $word )
    {
        if ( $this->peekWord() === $word )
        {
            $this->i++;
            return true;
        }
        return false;
    }

    protected function acceptOp( $op )
    {
        if ( $this->peekOp( $op ) )
        {
            $this->i++;
            return true;
        }
        return false;
    }

    protected function expectWord( $word )
    {
        if ( !$this->acceptWord( $word ) )
            throw $this->unsupported( "$word expected at " . $this->describe( $this->i ) );
    }

    protected function expectOp( $op )
    {
        if ( !$this->acceptOp( $op ) )
            throw $this->unsupported( "'$op' expected at " . $this->describe( $this->i ) );
    }

    protected function describe( $i )
    {
        if ( !isset( $this->tokens[$i] ) )
            return 'the end';
        $t = $this->tokens[$i];
        return $t[0] === 'string' ? "'" . mb_substr( $t[1], 0, 20 ) . "'" : $t[1];
    }

    protected function sourceText( $from, $to )
    {
        $parts = array();
        for ( $k = $from; $k < $to; $k++ )
            $parts[] = $this->tokens[$k][0] === 'string' ? "'" . $this->tokens[$k][1] . "'" : $this->tokens[$k][1];
        return strtolower( implode( '', $parts ) );
    }

    protected function unsupported( $what )
    {
        return new expDBUnsupportedException( expCassandraDialect::NAME, $what, 'CQL cannot answer it and the driver does not emulate it' );
    }
}
?>
