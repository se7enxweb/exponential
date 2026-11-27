<?php
/**
 * File containing the expCouchbaseDialect class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The SQL the kernel writes, as Couchbase's SQL++ (N1QL): what expCouchbaseDB sends to the Query service.
 *
 * A table is a collection of the connection's scope; a row is a JSON document whose fields are the columns. SQL++ is
 * close to SQL (joins, sub-selects, GROUP BY, UNION, CASE), so most of a statement goes through as it is. What changes:
 *
 * - Names are quoted with backticks (SQL++ has many more reserved words: value, user, path ...) and written in lower
 *   case, as the kernel's tables and columns are. A column without its table in a statement over several tables gets
 *   the table that has it (SQL++ reads an unqualified name there as a table).
 * - Strings become JSON strings. A string compared with a numeric column becomes a number and a number compared with a
 *   text column becomes a string: SQL++ compares types strictly (1 = '1' is false), MySQL converts.
 * - IS NULL is IS NOT VALUED (NULL or MISSING: the unmatched side of a LEFT JOIN is MISSING), IS NOT NULL is IS VALUED.
 * - a & b, a | b, a ^ b are BITAND(), BITOR(), BITXOR(); MD5('literal') is computed here (SQL++ has no MD5).
 * - SELECT * is SELECT t.*; an expression without a name gets its own text as the name, as MySQL names it.
 * - A sub-select in an expression is SELECT RAW ...[0] (a value), after IN it is SELECT RAW ... (a list of values).
 * - LIMIT o, n is LIMIT n OFFSET o; FOR UPDATE, LOCK IN SHARE MODE, INDEXED BY, USE INDEX, COLLATE, BINARY are dropped.
 * - Functions: CONCAT (arguments as strings), SUBSTRING (SUBSTR1, from 1), IFNULL and COALESCE (IFMISSINGORNULL),
 *   LENGTH, UNIX_TIMESTAMP, FROM_UNIXTIME, NOW, CAST, IF, GROUP_CONCAT (CONCAT2 over ARRAY_AGG), RAND.
 *
 * INSERT, UPDATE and DELETE are parsed (insert()) or rewritten (statement()); the driver makes the documents, their
 * keys and the counted ids. What SQL++ cannot say raises expDBUnsupportedException with its name.
 *
 * @since 6.0.15
 * @package lib
 */
class expCouchbaseDialect
{
    /** The functions whose name SQL++ shares */
    const SAME_FUNCTIONS = array( 'COUNT', 'SUM', 'AVG', 'MIN', 'MAX', 'LOWER', 'UPPER', 'ABS', 'FLOOR', 'CEIL', 'ROUND', 'TRUNC', 'GREATEST',
                                  'LEAST', 'TRIM', 'LTRIM', 'RTRIM', 'REPLACE', 'LENGTH', 'POWER', 'SQRT', 'SIGN', 'MOD', 'ARRAY_AGG',
                                  'TOSTRING', 'TONUMBER', 'META', 'UUID', 'POSITION1', 'REVERSE', 'REPEAT', 'CONTAINS', 'REGEXP_CONTAINS',
                                  'BITAND', 'BITOR', 'BITXOR', 'BITNOT', 'BITSHIFT', 'CONCAT2', 'SUBSTR1', 'IFMISSINGORNULL', 'IFMISSING',
                                  'STR_TO_MILLIS', 'MILLIS_TO_STR', 'NOW_MILLIS', 'ARRAY_COUNT', 'ARRAY_LENGTH' );

    /** The most keys a statement reads by key (USE KEYS) instead of through an index */
    const MAX_KEYS = 1000;

    /** @var array the catalogue: table => array( 'fields' => array( column => kind (int, float, text) ), ... ) */
    protected $catalogue;

    /** @var array the names of the current statement's projection (aliases keep their case) */
    protected $aliases = array();

    /** @var string[] the table names of the enclosing query levels */
    protected $outer = array();

    /** @var array this query level's renamed tables: name in the SQL => name in the SQL++ */
    protected $renames = array();

    /**
     * @param array $catalogue table => array( 'fields' => column => int|float|text, 'order' => columns in order )
     */
    public function __construct( array $catalogue = array() )
    {
        $this->catalogue = $catalogue;
    }

    /**
     * A JSON string literal of SQL++.
     *
     * @param string $value
     * @return string
     */
    public static function stringLiteral( $value )
    {
        return json_encode( (string)$value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
    }

    /**
     * A name quoted for SQL++.
     *
     * @param string $name
     * @return string
     */
    public static function quoteName( $name )
    {
        return '`' . str_replace( '`', '``', (string)$name ) . '`';
    }

    /**
     * A PHP value as a SQL++ literal.
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
            return is_finite( $value ) ? json_encode( $value ) : 'NULL';
        return self::stringLiteral( $value );
    }

    /**
     * The kind of a column of a .dba definition: int, float or text.
     *
     * @param array $field
     * @return string
     */
    public static function kindOf( array $field )
    {
        $type = strtolower( isset( $field['type'] ) ? (string)$field['type'] : 'varchar' );
        if ( in_array( $type, array( 'int', 'integer', 'auto_increment', 'bigint', 'smallint', 'tinyint', 'mediumint', 'bool', 'boolean', 'serial', 'bigserial' ), true ) )
            return 'int';
        if ( in_array( $type, array( 'float', 'double', 'real', 'decimal', 'numeric', 'double precision' ), true ) )
            return 'float';
        return 'text';
    }

    /**
     * A value as the column's kind stores it (MySQL's conversion: a numeric prefix, else 0).
     *
     * @param mixed $value
     * @param string|null $kind int, float, text or null (as it is)
     * @return mixed
     */
    public static function coerce( $value, $kind )
    {
        if ( $value === null || $kind === null )
            return $value;
        if ( is_bool( $value ) )
            $value = $value ? 1 : 0;
        if ( $kind === 'text' )
            return is_float( $value ) ? rtrim( rtrim( sprintf( '%.15F', $value ), '0' ), '.' ) : (string)$value;
        if ( is_int( $value ) || is_float( $value ) )
            return $kind === 'int' && is_float( $value ) && floor( $value ) == $value && abs( $value ) < 9.2e18 ? (int)$value : $value;
        $text = trim( (string)$value );
        if ( !preg_match( '/^[+-]?(\d+\.?\d*|\.\d+)([eE][+-]?\d+)?/', $text, $m ) )
            return 0;
        $number = $m[0];
        if ( preg_match( '/^[+-]?\d+$/', $number ) && ( strlen( ltrim( $number, '+-' ) ) < 19 ) )
            return (int)$number;
        $float = (float)$number;
        return $kind === 'int' && floor( $float ) == $float && abs( $float ) < 9.2e18 ? (int)$float : $float;
    }

    /**
     * The statement's kind, from its first words: select, insert, update, delete, ddl, transaction, noop, native.
     *
     * @param string $sql
     * @return string
     */
    public static function kind( $sql )
    {
        $text = ltrim( (string)$sql, " \t\r\n(" );
        if ( strncmp( $text, '/*sqlpp*/', 9 ) === 0 )
            return 'native';
        if ( !preg_match( '/^(\w+)(?:\s+(\w+))?(?:\s+(\w+))?/', $text, $m ) )
            return 'noop';
        $first = strtoupper( $m[1] );
        $second = isset( $m[2] ) ? strtoupper( $m[2] ) : '';
        switch ( $first )
        {
            case 'SELECT':
            case 'WITH':
                return 'select';
            case 'INSERT':
            case 'REPLACE':
                return 'insert';
            case 'UPDATE':
                return 'update';
            case 'DELETE':
                return 'delete';
            case 'TRUNCATE':
            case 'CREATE':
            case 'DROP':
            case 'ALTER':
                return 'ddl';
            case 'BEGIN':
            case 'START':
            case 'COMMIT':
            case 'ROLLBACK':
                return $second === 'WORK' || $second === 'TRANSACTION' || $second === '' ? 'transaction' : 'noop';
            case 'LOCK':
            case 'UNLOCK':
            case 'SET':
            case 'ANALYZE':
            case 'VACUUM':
            case 'PRAGMA':
            case 'OPTIMIZE':
                return 'noop';
            case 'UPSERT':
            case 'MERGE':
            case 'INFER':
            case 'EXPLAIN':
            case 'ADVISE':
            case 'BUILD':
            case 'EXECUTE':
            case 'PREPARE':
                return 'native';
        }
        return 'noop';
    }

    // ------------------------------------------------------------------ SELECT

    /** Forgets the state of a statement (one that was refused halfway leaves it) */
    protected function reset()
    {
        $this->aliases = array();
        $this->outer = array();
        $this->renames = array();
    }

    /**
     * A SELECT (with UNION) as SQL++.
     *
     * @param string $sql
     * @return array( 'sql' => SQL++, 'columns' => the names of the result in order, or null when unknown )
     * @throws expDBUnsupportedException
     */
    public function select( $sql )
    {
        $this->reset();
        $tokens = expCouchSQLLexer::tokenize( $sql );
        $columns = null;
        $out = $this->query( $tokens, 'top', $columns );
        return array( 'sql' => $out, 'columns' => $columns );
    }

    /**
     * A query (a SELECT with its UNIONs), at the outer level or as a sub-select.
     *
     * @param array[] $tokens
     * @param string $context top, from (a derived table), in (after IN), value (in an expression), exists
     * @param array|null $columns set to the result's names (of the first SELECT)
     * @return string
     */
    protected function query( array $tokens, $context, &$columns )
    {
        // UNION [ALL] at the outer level
        $parts = array();
        $glue = array();
        $depth = 0;
        $start = 0;
        $n = count( $tokens );
        for ( $i = 0; $i < $n; $i++ )
        {
            if ( expCouchSQLLexer::isChar( $tokens[$i], '(' ) )
                $depth++;
            elseif ( expCouchSQLLexer::isChar( $tokens[$i], ')' ) )
                $depth--;
            elseif ( $depth === 0 && expCouchSQLLexer::isKeyword( $tokens[$i], 'UNION' ) )
            {
                $parts[] = array_slice( $tokens, $start, $i - $start );
                $all = isset( $tokens[$i + 1] ) && expCouchSQLLexer::isKeyword( $tokens[$i + 1], 'ALL' );
                $glue[] = $all ? 'UNION ALL' : 'UNION';
                $start = $i + ( $all ? 2 : 1 );
            }
        }
        $parts[] = array_slice( $tokens, $start );
        $sql = '';
        $orderTail = '';
        foreach ( $parts as $k => $part )
        {
            // a SELECT in parentheses inside a UNION
            if ( $part && expCouchSQLLexer::isChar( $part[0], '(' ) && expCouchSQLLexer::closing( $part, 0 ) === count( $part ) - 1 )
                $part = array_slice( $part, 1, -1 );
            $partColumns = null;
            $raw = ( $context === 'in' || $context === 'value' ) && count( $parts ) === 1;
            $sql .= ( $k ? ' ' . $glue[$k - 1] . ' ' : '' ) . $this->selectCore( $part, $raw, $partColumns );
            if ( $k === 0 )
                $columns = $partColumns;
        }
        if ( count( $parts ) > 1 && ( $context === 'in' || $context === 'value' ) )
            throw new expDBUnsupportedException( 'couchbase', 'UNION in a sub-select used as a value' );
        return $sql . $orderTail;
    }

    /**
     * One SELECT.
     *
     * @param array[] $tokens
     * @param bool $raw SELECT RAW (a sub-select that gives values)
     * @param array|null $columns
     * @return string
     */
    protected function selectCore( array $tokens, $raw, &$columns )
    {
        if ( !$tokens || !expCouchSQLLexer::isKeyword( $tokens[0], 'SELECT' ) )
            throw new expDBUnsupportedException( 'couchbase', 'statement', 'not a SELECT: ' . mb_substr( expCouchSQLLexer::text( $tokens ), 0, 80 ) );
        $clauses = array( 'FROM', 'WHERE', 'GROUP', 'HAVING', 'ORDER', 'LIMIT', 'OFFSET', 'FOR', 'LOCK' );
        $positions = array();
        $from = 1;
        $n = count( $tokens );
        $depth = 0;
        for ( $i = 1; $i < $n; $i++ )
        {
            if ( expCouchSQLLexer::isChar( $tokens[$i], '(' ) )
                $depth++;
            elseif ( expCouchSQLLexer::isChar( $tokens[$i], ')' ) )
                $depth--;
            elseif ( $depth === 0 && $tokens[$i][0] === 'kw' && in_array( $tokens[$i][2], $clauses, true ) && !isset( $positions[$tokens[$i][2]] ) )
            {
                // FOR UPDATE / LOCK IN SHARE MODE: the rest is dropped
                $positions[$tokens[$i][2]] = $i;
            }
        }
        $order = array();
        foreach ( $positions as $word => $at )
            $order[$at] = $word;
        ksort( $order );
        $sections = array();
        $bounds = array_keys( $order );
        $projectionEnd = $bounds ? $bounds[0] : $n;
        foreach ( $bounds as $k => $at )
        {
            $end = isset( $bounds[$k + 1] ) ? $bounds[$k + 1] : $n;
            $sections[$order[$at]] = array_slice( $tokens, $at, $end - $at );
        }
        $projection = array_slice( $tokens, 1, $projectionEnd - 1 );
        $distinct = '';
        if ( $projection && expCouchSQLLexer::isKeyword( $projection[0], array( 'DISTINCT', 'ALL' ) ) )
        {
            $distinct = $projection[0][2] === 'DISTINCT' ? 'DISTINCT ' : '';
            array_shift( $projection );
        }

        // FROM: the tables and their names
        $scope = array();
        $fromSQL = '';
        $previousRenames = $this->renames;
        $this->renames = array();
        if ( isset( $sections['FROM'] ) )
            $fromSQL = $this->fromClause( array_slice( $sections['FROM'], 1 ), $scope );
        // the sub-selects of this level see its tables as enclosing ones
        $previousOuter = $this->outer;
        $this->outer = array_merge( $this->outer, array_keys( $scope ) );
        $previousAliases = $this->aliases;
        $this->aliases = array();
        foreach ( expCouchSQLLexer::splitTopLevel( $projection ) as $item )
        {
            $c = count( $item );
            if ( $c >= 2 && $item[$c - 1][0] === 'id' && ( expCouchSQLLexer::isKeyword( $item[$c - 2], 'AS' ) || $item[$c - 2][0] !== 'op' ) )
                $this->aliases[strtolower( $item[$c - 1][2] )] = $item[$c - 1][2];
            elseif ( $c >= 2 && $item[$c - 1][0] === 'str' && expCouchSQLLexer::isKeyword( $item[$c - 2], 'AS' ) )
                $this->aliases[strtolower( $item[$c - 1][2] )] = $item[$c - 1][2];
        }

        // the projection
        $items = array();
        $columns = array();
        foreach ( expCouchSQLLexer::splitTopLevel( $projection ) as $item )
        {
            if ( !$item )
                throw new expDBUnsupportedException( 'couchbase', 'empty column in the select list' );
            $c = count( $item );
            if ( $c === 1 && expCouchSQLLexer::isChar( $item[0], '*' ) )
            {
                foreach ( $scope as $alias => $table )
                {
                    $items[] = self::quoteName( $alias ) . '.*';
                    $columns = $columns === null ? null : $this->tableColumns( $table, $columns );
                }
                continue;
            }
            if ( $c === 3 && $item[0][0] === 'id' && expCouchSQLLexer::isChar( $item[1], '.' ) && expCouchSQLLexer::isChar( $item[2], '*' ) )
            {
                $alias = $this->aliasName( $item[0][2], $scope );
                $items[] = self::quoteName( $alias ) . '.*';
                $columns = $columns === null ? null : $this->tableColumns( isset( $scope[$alias] ) ? $scope[$alias] : null, $columns );
                continue;
            }
            $alias = null;
            $expression = $item;
            if ( $c >= 2 && ( $item[$c - 1][0] === 'id' || $item[$c - 1][0] === 'str' )
                 && ( expCouchSQLLexer::isKeyword( $item[$c - 2], 'AS' ) || ( $item[$c - 1][0] === 'id' && $item[$c - 2][0] !== 'op' && !expCouchSQLLexer::isChar( $item[$c - 2], '.' ) ) ) )
            {
                $alias = $item[$c - 1][2];
                $expression = array_slice( $item, 0, expCouchSQLLexer::isKeyword( $item[$c - 2], 'AS' ) ? $c - 2 : $c - 1 );
            }
            $sql = $this->expression( $expression, $scope );
            if ( $alias === null )
            {
                // a column keeps its name; anything else is named by its text, as MySQL names it
                $e = count( $expression );
                if ( $e === 1 && $expression[0][0] === 'id' )
                    $alias = strtolower( $expression[0][2] );
                elseif ( $e === 3 && $expression[0][0] === 'id' && expCouchSQLLexer::isChar( $expression[1], '.' ) && $expression[2][0] === 'id' )
                    $alias = strtolower( $expression[2][2] );
                else
                    $alias = expCouchSQLLexer::text( $expression );
            }
            if ( $columns !== null )
                $columns[] = $alias;
            $items[] = $sql . ' AS ' . self::quoteName( $alias );
        }
        if ( $raw )
        {
            if ( count( $items ) !== 1 || substr( $items[0], -2 ) === '.*' )
                throw new expDBUnsupportedException( 'couchbase', 'sub-select of more than one column used as a value' );
            $items[0] = preg_replace( '/ AS `(?:[^`]|``)*`$/', '', $items[0] );
        }

        $where = isset( $sections['WHERE'] ) ? $this->expression( array_slice( $sections['WHERE'], 1 ), $scope ) : '';
        // a WHERE that names the whole primary key: the documents are read by their keys (no index, no wait for one)
        if ( isset( $sections['WHERE'] ) && count( $scope ) === 1 && reset( $scope ) !== null && strpos( $fromSQL, ' JOIN ' ) === false )
        {
            $keys = $this->keysFromWhere( reset( $scope ), key( $scope ), array_slice( $sections['WHERE'], 1 ) );
            if ( $keys !== null )
                $fromSQL .= ' USE KEYS ' . self::keysLiteral( $keys );
        }
        $sql = 'SELECT ' . ( $raw ? 'RAW ' : '' ) . $distinct . implode( ', ', $items );
        if ( $fromSQL !== '' )
            $sql .= ' FROM ' . $fromSQL;
        // COUNT over a whole collection without a filter is answered from the collection's statistics, which lag
        // behind the writes: a filter on the key makes it count the documents
        if ( $where === '' && count( $scope ) === 1 && !isset( $sections['GROUP'] ) && preg_match( '/\bCOUNT\s*\(/i', $sql ) )
            $where = 'META(' . self::quoteName( key( $scope ) ) . ').id IS NOT MISSING';
        if ( $where !== '' )
            $sql .= ' WHERE ' . $where;
        if ( isset( $sections['GROUP'] ) )
        {
            $group = array_slice( $sections['GROUP'], 1 );
            if ( $group && expCouchSQLLexer::isKeyword( $group[0], 'BY' ) )
                array_shift( $group );
            $sql .= ' GROUP BY ' . $this->expressionList( $group, $scope );
        }
        if ( isset( $sections['HAVING'] ) )
            $sql .= ' HAVING ' . $this->expression( array_slice( $sections['HAVING'], 1 ), $scope );
        if ( isset( $sections['ORDER'] ) )
        {
            $orderTokens = array_slice( $sections['ORDER'], 1 );
            if ( $orderTokens && expCouchSQLLexer::isKeyword( $orderTokens[0], 'BY' ) )
                array_shift( $orderTokens );
            $sql .= ' ORDER BY ' . $this->orderList( $orderTokens, $scope );
        }
        $limit = null;
        $offset = null;
        if ( isset( $sections['LIMIT'] ) )
        {
            $parts = expCouchSQLLexer::splitTopLevel( array_slice( $sections['LIMIT'], 1 ) );
            if ( count( $parts ) === 2 )
            {
                $offset = $this->expression( $parts[0], $scope );
                $limit = $this->expression( $parts[1], $scope );
            }
            else
                $limit = $this->expression( $parts[0], $scope );
        }
        if ( isset( $sections['OFFSET'] ) )
            $offset = $this->expression( array_slice( $sections['OFFSET'], 1 ), $scope );
        if ( $limit !== null )
            $sql .= ' LIMIT ' . $limit;
        if ( $offset !== null )
            $sql .= ' OFFSET ' . $offset;
        $this->aliases = $previousAliases;
        $this->outer = $previousOuter;
        $this->renames = $previousRenames;
        return $sql;
    }

    /**
     * The columns of a table, added to a list (null when the table is unknown).
     *
     * @param string|null $table
     * @param array $columns
     * @return array|null
     */
    protected function tableColumns( $table, array $columns )
    {
        if ( $table === null || !isset( $this->catalogue[$table]['order'] ) )
            return null;
        foreach ( $this->catalogue[$table]['order'] as $column )
            if ( !in_array( $column, $columns, true ) )
                $columns[] = $column;
        return $columns;
    }

    /**
     * An alias of the scope, by its name in any case.
     *
     * @param string $name
     * @param array $scope
     * @return string
     */
    protected function aliasName( $name, array $scope )
    {
        // a name this query level renamed (it shadowed one of an enclosing level)
        foreach ( $this->renames as $from => $to )
            if ( strcasecmp( $from, $name ) === 0 && array_key_exists( $to, $scope ) )
                return $to;
        if ( isset( $scope[$name] ) )
            return $name;
        foreach ( $scope as $alias => $table )
            if ( strcasecmp( $alias, $name ) === 0 )
                return $alias;
        return strtolower( $name );
    }

    /**
     * The name a table of this query level gets: its own, or, when an enclosing level uses it, a new one (SQL++
     * refuses a name a sub-select repeats, where SQL lets the inner one hide the outer).
     *
     * @param string $alias
     * @return string
     */
    protected function unshadowed( $alias )
    {
        foreach ( $this->outer as $used )
            if ( strcasecmp( $used, $alias ) === 0 )
            {
                $new = $alias . '_' . ( count( $this->outer ) + 1 );
                $this->renames[$alias] = $new;
                return $new;
            }
        return $alias;
    }

    /**
     * The FROM clause: tables (with JOINs and derived tables) in SQL++; fills the scope alias => table.
     *
     * @param array[] $tokens
     * @param array $scope
     * @return string
     */
    protected function fromClause( array $tokens, array &$scope )
    {
        // first pass: the names of every table, so the ON conditions can name any of them
        $items = array();
        $i = 0;
        $n = count( $tokens );
        $current = array( 'join' => null, 'tokens' => array() );
        $depth = 0;
        for ( ; $i < $n; $i++ )
        {
            $t = $tokens[$i];
            if ( expCouchSQLLexer::isChar( $t, '(' ) )
                $depth++;
            elseif ( expCouchSQLLexer::isChar( $t, ')' ) )
                $depth--;
            if ( $depth === 0 && expCouchSQLLexer::isChar( $t, ',' ) )
            {
                $items[] = $current;
                $current = array( 'join' => ',', 'tokens' => array() );
                continue;
            }
            if ( $depth === 0 && expCouchSQLLexer::isKeyword( $t, array( 'JOIN', 'LEFT', 'INNER', 'CROSS', 'RIGHT', 'FULL', 'NATURAL' ) ) )
            {
                $words = array();
                while ( $i < $n && expCouchSQLLexer::isKeyword( $tokens[$i], array( 'LEFT', 'INNER', 'CROSS', 'RIGHT', 'FULL', 'OUTER', 'NATURAL' ) ) )
                    $words[] = $tokens[$i++][2];
                if ( $i >= $n || !expCouchSQLLexer::isKeyword( $tokens[$i], 'JOIN' ) )
                    throw new expDBUnsupportedException( 'couchbase', 'join ' . implode( ' ', $words ) );
                if ( array_intersect( $words, array( 'RIGHT', 'FULL', 'NATURAL' ) ) )
                    throw new expDBUnsupportedException( 'couchbase', implode( ' ', $words ) . ' JOIN' );
                $items[] = $current;
                $current = array( 'join' => in_array( 'LEFT', $words, true ) ? 'LEFT JOIN' : ( in_array( 'CROSS', $words, true ) ? 'CROSS' : 'JOIN' ), 'tokens' => array() );
                continue;
            }
            $current['tokens'][] = $t;
        }
        $items[] = $current;

        foreach ( $items as $k => $item )
        {
            $t = $item['tokens'];
            $on = expCouchSQLLexer::findTopLevel( $t, array( 'ON', 'USING' ) );
            $ref = $on >= 0 ? array_slice( $t, 0, $on ) : $t;
            $items[$k]['condition'] = $on >= 0 ? array_slice( $t, $on ) : array();
            // drop SQLite's INDEXED BY x / NOT INDEXED and MySQL's USE|FORCE|IGNORE INDEX (...)
            $clean = array();
            for ( $j = 0; $j < count( $ref ); $j++ )
            {
                if ( expCouchSQLLexer::isKeyword( $ref[$j], 'INDEXED' ) && isset( $ref[$j + 1] ) && expCouchSQLLexer::isKeyword( $ref[$j + 1], 'BY' ) )
                {
                    $j += 2;
                    continue;
                }
                if ( expCouchSQLLexer::isKeyword( $ref[$j], 'NOT' ) && isset( $ref[$j + 1] ) && expCouchSQLLexer::isKeyword( $ref[$j + 1], 'INDEXED' ) )
                {
                    $j++;
                    continue;
                }
                if ( ( $ref[$j][0] === 'id' && in_array( strtoupper( $ref[$j][2] ), array( 'USE', 'FORCE' ), true ) || expCouchSQLLexer::isKeyword( $ref[$j], 'IGNORE' ) )
                     && isset( $ref[$j + 1] ) && expCouchSQLLexer::isKeyword( $ref[$j + 1], array( 'INDEX', 'KEY' ) ) )
                {
                    $j += 2;
                    if ( isset( $ref[$j] ) && expCouchSQLLexer::isChar( $ref[$j], '(' ) )
                        $j = expCouchSQLLexer::closing( $ref, $j );
                    continue;
                }
                $clean[] = $ref[$j];
            }
            $ref = $clean;
            if ( $ref && expCouchSQLLexer::isChar( $ref[0], '(' ) )
            {
                $close = expCouchSQLLexer::closing( $ref, 0 );
                $rest = array_slice( $ref, $close + 1 );
                if ( $rest && expCouchSQLLexer::isKeyword( $rest[0], 'AS' ) )
                    array_shift( $rest );
                if ( count( $rest ) !== 1 || $rest[0][0] !== 'id' )
                    throw new expDBUnsupportedException( 'couchbase', 'derived table without a name' );
                $items[$k]['derived'] = array_slice( $ref, 1, $close - 1 );
                $items[$k]['alias'] = $this->unshadowed( $rest[0][2] );
                $scope[$items[$k]['alias']] = null;
                continue;
            }
            if ( !$ref || $ref[0][0] !== 'id' )
                throw new expDBUnsupportedException( 'couchbase', 'table reference', expCouchSQLLexer::text( $ref ) );
            $table = strtolower( $ref[0][2] );
            $next = 1;
            // a schema-qualified name: the last part is the collection
            while ( isset( $ref[$next] ) && expCouchSQLLexer::isChar( $ref[$next], '.' ) && isset( $ref[$next + 1] ) && $ref[$next + 1][0] === 'id' )
            {
                $table = strtolower( $ref[$next + 1][2] );
                $next += 2;
            }
            $rest = array_slice( $ref, $next );
            if ( $rest && expCouchSQLLexer::isKeyword( $rest[0], 'AS' ) )
                array_shift( $rest );
            $alias = $rest && $rest[0][0] === 'id' ? $rest[0][2] : $table;
            if ( count( $rest ) > 1 )
                throw new expDBUnsupportedException( 'couchbase', 'table reference', expCouchSQLLexer::text( $ref ) );
            $alias = $this->unshadowed( $alias );
            $items[$k]['table'] = $table;
            $items[$k]['alias'] = $alias;
            $scope[$alias] = $table;
        }

        $sql = '';
        foreach ( $items as $k => $item )
        {
            if ( isset( $item['derived'] ) )
            {
                $derivedColumns = null;
                $ref = '(' . $this->query( $item['derived'], 'from', $derivedColumns ) . ') AS ' . self::quoteName( $item['alias'] );
            }
            else
                $ref = self::quoteName( $item['table'] ) . ' AS ' . self::quoteName( $item['alias'] );
            if ( $k === 0 )
                $sql = $ref;
            elseif ( $item['join'] === ',' || $item['join'] === 'CROSS' )
            {
                if ( $item['condition'] )
                    throw new expDBUnsupportedException( 'couchbase', 'ON after a comma join' );
                $sql .= ', ' . $ref;
            }
            else
            {
                $condition = $item['condition'];
                if ( !$condition )
                    $sql .= ', ' . $ref;
                elseif ( expCouchSQLLexer::isKeyword( $condition[0], 'USING' ) )
                {
                    // USING (a, b): the same names on both sides
                    $names = array();
                    foreach ( expCouchSQLLexer::splitTopLevel( array_slice( $condition, 2, -1 ) ) as $name )
                        $names[] = strtolower( $name[0][2] );
                    $previous = $items[$k - 1]['alias'];
                    $parts = array();
                    foreach ( $names as $name )
                        $parts[] = self::quoteName( $previous ) . '.' . self::quoteName( $name ) . ' = ' . self::quoteName( $item['alias'] ) . '.' . self::quoteName( $name );
                    $sql .= ' ' . $item['join'] . ' ' . $ref . ' ON ' . implode( ' AND ', $parts );
                }
                else
                    $sql .= ' ' . $item['join'] . ' ' . $ref . ' ON ' . $this->expression( array_slice( $condition, 1 ), $scope );
            }
        }
        return $sql;
    }

    /**
     * A list of expressions (GROUP BY).
     */
    protected function expressionList( array $tokens, array $scope )
    {
        $parts = array();
        foreach ( expCouchSQLLexer::splitTopLevel( $tokens ) as $part )
            $parts[] = $this->groupOrOrderItem( $part, $scope );
        return implode( ', ', $parts );
    }

    /**
     * ORDER BY items: a number is the position in the select list (SQL++ has no positions: refused).
     */
    protected function orderList( array $tokens, array $scope )
    {
        $parts = array();
        foreach ( expCouchSQLLexer::splitTopLevel( $tokens ) as $part )
        {
            $direction = '';
            $c = count( $part );
            if ( $c > 1 && expCouchSQLLexer::isKeyword( $part[$c - 1], array( 'ASC', 'DESC' ) ) )
            {
                $direction = ' ' . $part[$c - 1][2];
                array_pop( $part );
            }
            $parts[] = $this->groupOrOrderItem( $part, $scope ) . $direction;
        }
        return implode( ', ', $parts );
    }

    protected function groupOrOrderItem( array $part, array $scope )
    {
        if ( count( $part ) === 1 && $part[0][0] === 'num' )
            throw new expDBUnsupportedException( 'couchbase', 'ORDER BY or GROUP BY by position' );
        // a name of the select list keeps its case
        if ( count( $part ) === 1 && $part[0][0] === 'id' && isset( $this->aliases[strtolower( $part[0][2] )] ) && !$this->isColumnOfScope( $part[0][2], $scope ) )
            return self::quoteName( $this->aliases[strtolower( $part[0][2] )] );
        return $this->expression( $part, $scope );
    }

    protected function isColumnOfScope( $name, array $scope )
    {
        $name = strtolower( $name );
        foreach ( $scope as $table )
            if ( $table !== null && isset( $this->catalogue[$table]['fields'][$name] ) )
                return true;
        return false;
    }

    // ------------------------------------------------------------------ expressions

    /**
     * An expression (a condition, a value, a select list item) in SQL++.
     *
     * @param array[] $tokens
     * @param array $scope alias => table
     * @return string
     */
    public function expression( array $tokens, array $scope )
    {
        $out = $this->expressionTokens( $tokens, $scope );
        $out = $this->bitOperators( $out );
        return self::join( $out );
    }

    /**
     * Output tokens as text.
     *
     * @param array[] $out array( type, text )
     * @return string
     */
    protected static function join( array $out )
    {
        $sql = '';
        $previous = null;
        foreach ( $out as $t )
        {
            $glue = ' ';
            if ( $previous !== null && ( $previous[1] === '(' || $previous[1] === '.' || $previous[0] === 'fn' ) )
                $glue = '';
            if ( $t[1] === ')' || $t[1] === ',' || $t[1] === '.' || $t[1] === '[' || $t[1] === ']' )
                $glue = '';
            $sql .= ( $sql === '' ? '' : $glue ) . $t[1];
            $previous = $t;
        }
        return $sql;
    }

    /**
     * The kind of the column a name refers to (int, float, text) or null.
     *
     * @param string|null $alias
     * @param string $column
     * @param array $scope
     * @return string|null
     */
    protected function columnKind( $alias, $column, array $scope )
    {
        $column = strtolower( $column );
        if ( $alias !== null )
        {
            $table = isset( $scope[$alias] ) ? $scope[$alias] : null;
            return $table !== null && isset( $this->catalogue[$table]['fields'][$column] ) ? $this->catalogue[$table]['fields'][$column] : null;
        }
        $kind = null;
        foreach ( $scope as $table )
            if ( $table !== null && isset( $this->catalogue[$table]['fields'][$column] ) )
            {
                if ( $kind !== null && $kind !== $this->catalogue[$table]['fields'][$column] )
                    return null;
                $kind = $this->catalogue[$table]['fields'][$column];
            }
        return $kind;
    }

    /**
     * The alias of the table that has a column, for a name without one in a statement over several tables.
     */
    protected function ownerOf( $column, array $scope )
    {
        $column = strtolower( $column );
        foreach ( $scope as $alias => $table )
            if ( $table !== null && isset( $this->catalogue[$table]['fields'][$column] ) )
                return $alias;
        return null;
    }

    /**
     * The tokens of an expression as output tokens.
     *
     * @param array[] $tokens
     * @param array $scope
     * @return array[] array( type, text, kind ) with type col (a column, kind its kind), lit, kw, op, punc, fn, sub, other
     */
    protected function expressionTokens( array $tokens, array $scope )
    {
        $out = array();
        $n = count( $tokens );
        $pending = null;       // the kind a following literal is converted to
        $pendingUntil = 0;     // literals left (0: none; -1: a list in parentheses)
        for ( $i = 0; $i < $n; $i++ )
        {
            $t = $tokens[$i];
            $type = $t[0];
            if ( $type === 'str' || $type === 'num' || ( $type === 'op' && ( $t[2] === '-' || $t[2] === '+' ) && isset( $tokens[$i + 1] ) && $tokens[$i + 1][0] === 'num'
                 && ( !$out || in_array( $out[count( $out ) - 1][0], array( 'op', 'kw', 'punc' ), true ) && $out[count( $out ) - 1][1] !== ')' ) ) )
            {
                $value = $type === 'op' ? ( $t[2] === '-' ? -$tokens[++$i][2] : $tokens[++$i][2] ) : $t[2];
                $kind = null;
                if ( $pending !== null )
                    $kind = $pending;
                else
                {
                    // literal op column
                    $j = $i + 1;
                    if ( isset( $tokens[$j] ) && $tokens[$j][0] === 'op' && in_array( $tokens[$j][2], array( '=', '<>', '!=', '<', '<=', '>', '>=' ), true ) )
                    {
                        $alias = null;
                        $column = null;
                        if ( isset( $tokens[$j + 1] ) && $tokens[$j + 1][0] === 'id' )
                        {
                            if ( isset( $tokens[$j + 2] ) && expCouchSQLLexer::isChar( $tokens[$j + 2], '.' ) && isset( $tokens[$j + 3] ) && $tokens[$j + 3][0] === 'id' )
                            {
                                $alias = $this->aliasName( $tokens[$j + 1][2], $scope );
                                $column = $tokens[$j + 3][2];
                            }
                            elseif ( !isset( $tokens[$j + 2] ) || !expCouchSQLLexer::isChar( $tokens[$j + 2], '(' ) )
                                $column = $tokens[$j + 1][2];
                        }
                        if ( $column !== null )
                            $kind = $this->columnKind( $alias, $column, $scope );
                    }
                }
                if ( $kind !== null )
                    $value = self::coerce( $value, $kind );
                // MySQL's TRUE/FALSE are 1/0 (done in literal())
                $out[] = array( 'lit', self::literal( $value ), null );
                if ( $pendingUntil > 0 && --$pendingUntil === 0 )
                    $pending = null;
                continue;
            }
            if ( $type === 'kw' )
            {
                $word = $t[2];
                if ( $word === 'NULL' )
                {
                    $out[] = array( 'lit', 'NULL', null );
                    continue;
                }
                if ( $word === 'TRUE' || $word === 'FALSE' )
                {
                    $out[] = array( 'lit', $word === 'TRUE' ? '1' : '0', null );
                    continue;
                }
                if ( $word === 'IS' )
                {
                    $not = isset( $tokens[$i + 1] ) && expCouchSQLLexer::isKeyword( $tokens[$i + 1], 'NOT' );
                    $k = $i + ( $not ? 2 : 1 );
                    if ( isset( $tokens[$k] ) && expCouchSQLLexer::isKeyword( $tokens[$k], 'NULL' ) )
                    {
                        $out[] = array( 'kw', $not ? 'IS VALUED' : 'IS NOT VALUED', null );
                        $i = $k;
                        continue;
                    }
                    if ( isset( $tokens[$k] ) && expCouchSQLLexer::isKeyword( $tokens[$k], array( 'TRUE', 'FALSE' ) ) )
                    {
                        $out[] = array( 'op', $not ? '!=' : '=', null );
                        $out[] = array( 'lit', $tokens[$k][2] === 'TRUE' ? '1' : '0', null );
                        $i = $k;
                        continue;
                    }
                }
                // MySQL's IF( condition, a, b ), REPLACE( s, a, b ), LEFT( s, n ): words the lexer reads as keywords, used as functions
                if ( in_array( $word, array( 'IF', 'REPLACE', 'LEFT' ), true ) && isset( $tokens[$i + 1] ) && expCouchSQLLexer::isChar( $tokens[$i + 1], '(' ) )
                {
                    $close = expCouchSQLLexer::closing( $tokens, $i + 1 );
                    $out[] = array( 'other', $this->functionCall( $word, array_slice( $tokens, $i + 2, $close - $i - 2 ), $scope ), null );
                    $i = $close;
                    continue;
                }
                if ( $word === 'BINARY' )
                    continue;
                if ( $word === 'COLLATE' )
                {
                    $i++;
                    continue;
                }
                if ( $word === 'DIV' )
                    throw new expDBUnsupportedException( 'couchbase', 'DIV' );
                if ( $word === 'XOR' )
                    throw new expDBUnsupportedException( 'couchbase', 'XOR' );
                if ( $word === 'ESCAPE' )
                {
                    // SQL++'s LIKE escapes with \ already
                    if ( isset( $tokens[$i + 1] ) && $tokens[$i + 1][0] === 'str' && $tokens[$i + 1][2] === '\\' )
                    {
                        $i++;
                        continue;
                    }
                    throw new expDBUnsupportedException( 'couchbase', 'LIKE ... ESCAPE other than \\' );
                }
                if ( $word === 'IN' && $pending !== null && isset( $tokens[$i + 1] ) && expCouchSQLLexer::isChar( $tokens[$i + 1], '(' ) )
                {
                    // the list's literals take the column's kind
                    $close = expCouchSQLLexer::closing( $tokens, $i + 1 );
                    $isSelect = isset( $tokens[$i + 2] ) && expCouchSQLLexer::isKeyword( $tokens[$i + 2], 'SELECT' );
                    if ( !$isSelect )
                    {
                        $kind = $pending;
                        $list = array();
                        foreach ( expCouchSQLLexer::splitTopLevel( array_slice( $tokens, $i + 2, $close - $i - 2 ) ) as $item )
                        {
                            $isLiteral = false;
                            $value = expCouchSQLLexer::literal( $item, $isLiteral );
                            $list[] = $isLiteral ? self::literal( self::coerce( $value, $kind ) ) : $this->expression( $item, $scope );
                        }
                        $out[] = array( 'kw', 'IN', null );
                        $out[] = array( 'other', '[' . implode( ', ', $list ) . ']', null );
                        $i = $close;
                        $pending = null;
                        continue;
                    }
                }
                if ( $word === 'IN' && isset( $tokens[$i + 1] ) && expCouchSQLLexer::isChar( $tokens[$i + 1], '(' )
                     && !( isset( $tokens[$i + 2] ) && expCouchSQLLexer::isKeyword( $tokens[$i + 2], 'SELECT' ) ) )
                {
                    // IN ( a, b ): SQL++ takes an array
                    $close = expCouchSQLLexer::closing( $tokens, $i + 1 );
                    $list = array();
                    foreach ( expCouchSQLLexer::splitTopLevel( array_slice( $tokens, $i + 2, $close - $i - 2 ) ) as $item )
                        $list[] = $this->expression( $item, $scope );
                    $out[] = array( 'kw', 'IN', null );
                    $out[] = array( 'other', '[' . implode( ', ', $list ) . ']', null );
                    $i = $close;
                    continue;
                }
                if ( $word === 'BETWEEN' && $pending !== null )
                    $pendingUntil = 2;
                elseif ( !in_array( $word, array( 'NOT', 'IN', 'LIKE', 'BETWEEN', 'AND' ), true ) || ( $word === 'AND' && $pendingUntil <= 0 ) )
                    $pending = null;
                if ( $word === 'LIKE' )
                    $pending = $pending === 'text' ? 'text' : null;
                $out[] = array( 'kw', $word, null );
                continue;
            }
            if ( $type === 'id' )
            {
                // a function
                if ( isset( $tokens[$i + 1] ) && expCouchSQLLexer::isChar( $tokens[$i + 1], '(' ) && $t[1][0] !== '`' && $t[1][0] !== '"' )
                {
                    $close = expCouchSQLLexer::closing( $tokens, $i + 1 );
                    $args = array_slice( $tokens, $i + 2, $close - $i - 2 );
                    $out[] = array( 'other', $this->functionCall( strtoupper( $t[2] ), $args, $scope ), $this->functionKind( strtoupper( $t[2] ) ) );
                    $i = $close;
                    $pending = $this->pendingAfter( $tokens, $i, $out[count( $out ) - 1][2] );
                    $pendingUntil = $pending !== null ? 1 : 0;
                    continue;
                }
                // a qualified name
                if ( isset( $tokens[$i + 1] ) && expCouchSQLLexer::isChar( $tokens[$i + 1], '.' ) && isset( $tokens[$i + 2] ) && ( $tokens[$i + 2][0] === 'id' || $tokens[$i + 2][0] === 'kw' ) )
                {
                    $alias = $this->aliasName( $t[2], $scope );
                    // a schema-qualified table.column (db.table.column): the last two
                    if ( isset( $tokens[$i + 3] ) && expCouchSQLLexer::isChar( $tokens[$i + 3], '.' ) && isset( $tokens[$i + 4] ) && $tokens[$i + 4][0] === 'id' )
                    {
                        $alias = $this->aliasName( $tokens[$i + 2][2], $scope );
                        $i += 2;
                    }
                    $column = strtolower( $tokens[$i + 2][2] );
                    $kind = $this->columnKind( $alias, $column, $scope );
                    $out[] = array( 'col', self::quoteName( $alias ) . '.' . self::quoteName( $column ), $kind );
                    $i += 2;
                    $pending = $this->pendingAfter( $tokens, $i, $kind );
                    $pendingUntil = $pending !== null ? 1 : 0;
                    continue;
                }
                $name = $t[2];
                // a name of the select list (HAVING, ORDER BY) keeps its case
                if ( isset( $this->aliases[strtolower( $name )] ) && !$this->isColumnOfScope( $name, $scope ) )
                {
                    $out[] = array( 'col', self::quoteName( $this->aliases[strtolower( $name )] ), null );
                    continue;
                }
                $column = strtolower( $name );
                $kind = $this->columnKind( null, $column, $scope );
                $owner = count( $scope ) > 1 ? $this->ownerOf( $column, $scope ) : null;
                $out[] = array( 'col', ( $owner !== null ? self::quoteName( $owner ) . '.' : '' ) . self::quoteName( $column ), $kind );
                $pending = $this->pendingAfter( $tokens, $i, $kind );
                $pendingUntil = $pending !== null ? 1 : 0;
                continue;
            }
            if ( expCouchSQLLexer::isChar( $t, '(' ) )
            {
                $close = expCouchSQLLexer::closing( $tokens, $i );
                $inner = array_slice( $tokens, $i + 1, $close - $i - 1 );
                if ( $inner && expCouchSQLLexer::isKeyword( $inner[0], 'SELECT' ) )
                {
                    $previous = $out ? $out[count( $out ) - 1] : null;
                    $context = 'value';
                    if ( $previous && $previous[0] === 'kw' && in_array( $previous[1], array( 'IN', 'EXISTS' ), true ) )
                        $context = $previous[1] === 'IN' ? 'in' : 'exists';
                    // ANY/ALL/SOME ( SELECT ... ) are not SQL++
                    $subColumns = null;
                    $sub = $this->query( $inner, $context, $subColumns );
                    $out[] = array( 'sub', '(' . $sub . ')' . ( $context === 'value' ? '[0]' : '' ), null );
                    $i = $close;
                    $pending = null;
                    continue;
                }
                // a group: keeps the pending kind for a list after IN (handled above) only
                $innerOut = $this->expressionTokens( $inner, $scope );
                $innerOut = $this->bitOperators( $innerOut );
                $out[] = array( 'group', '(' . self::join( $innerOut ) . ')', count( $innerOut ) === 1 ? $innerOut[0][2] : null );
                $i = $close;
                continue;
            }
            if ( $type === 'op' )
            {
                if ( !in_array( $t[2], array( '=', '<>', '!=', '<', '<=', '>', '>=' ), true ) )
                    $pending = null;
                if ( $t[2] === '!' )
                {
                    $out[] = array( 'kw', 'NOT', null );
                    continue;
                }
                $out[] = array( 'op', $t[2], null );
                continue;
            }
            if ( $type === 'param' )
                throw new expDBUnsupportedException( 'couchbase', 'bound parameter in the SQL' );
            if ( expCouchSQLLexer::isChar( $t, ',' ) )
            {
                $out[] = array( 'punc', ',', null );
                $pending = null;
                continue;
            }
            $out[] = array( 'other', $t[1], null );
        }
        return $out;
    }

    /**
     * The kind a literal after a column takes: when an operator, IN, LIKE or BETWEEN follows the column.
     */
    protected function pendingAfter( array $tokens, $i, $kind )
    {
        if ( $kind === null || !isset( $tokens[$i + 1] ) )
            return null;
        $next = $tokens[$i + 1];
        if ( $next[0] === 'op' && in_array( $next[2], array( '=', '<>', '!=', '<', '<=', '>', '>=' ), true ) )
            return $kind;
        if ( expCouchSQLLexer::isKeyword( $next, array( 'IN', 'NOT', 'BETWEEN', 'LIKE' ) ) )
            return $kind;
        return null;
    }

    /** The kind of a function's result, where it is plain */
    protected function functionKind( $name )
    {
        if ( in_array( $name, array( 'COUNT', 'LENGTH', 'CHAR_LENGTH', 'UNIX_TIMESTAMP', 'FLOOR', 'CEIL', 'CEILING', 'ROUND' ), true ) )
            return 'int';
        if ( in_array( $name, array( 'LOWER', 'UPPER', 'CONCAT', 'SUBSTRING', 'SUBSTR', 'MD5', 'TRIM' ), true ) )
            return 'text';
        return null;
    }

    /**
     * A function call in SQL++.
     *
     * @param string $name upper case
     * @param array[] $args the tokens between the parentheses
     * @param array $scope
     * @return string
     */
    protected function functionCall( $name, array $args, array $scope )
    {
        if ( $name === 'CAST' || $name === 'CONVERT' )
        {
            $as = expCouchSQLLexer::findTopLevel( $args, array( 'AS' ) );
            if ( $as < 0 && $name === 'CONVERT' )
            {
                $parts = expCouchSQLLexer::splitTopLevel( $args );
                if ( count( $parts ) !== 2 )
                    throw new expDBUnsupportedException( 'couchbase', 'CONVERT' );
                $value = $parts[0];
                $type = strtoupper( $parts[1][0][1] );
            }
            elseif ( $as < 0 )
                throw new expDBUnsupportedException( 'couchbase', 'CAST without AS' );
            else
            {
                $value = array_slice( $args, 0, $as );
                $type = strtoupper( $args[$as + 1][1] );
            }
            $inner = $this->expression( $value, $scope );
            if ( in_array( $type, array( 'CHAR', 'VARCHAR', 'TEXT', 'NCHAR', 'STRING' ), true ) )
                return 'TOSTRING(' . $inner . ')';
            if ( in_array( $type, array( 'SIGNED', 'UNSIGNED', 'INT', 'INTEGER', 'BIGINT' ), true ) )
                return 'TRUNC(TONUMBER(' . $inner . '))';
            return 'TONUMBER(' . $inner . ')';
        }
        $parts = $args ? expCouchSQLLexer::splitTopLevel( $args ) : array();
        $distinct = '';
        if ( $parts && $parts[0] && expCouchSQLLexer::isKeyword( $parts[0][0], 'DISTINCT' ) )
        {
            $distinct = 'DISTINCT ';
            $parts[0] = array_slice( $parts[0], 1 );
        }
        if ( $name === 'MD5' )
        {
            $isLiteral = false;
            $value = count( $parts ) === 1 ? expCouchSQLLexer::literal( $parts[0], $isLiteral ) : null;
            if ( !$isLiteral )
                throw new expDBUnsupportedException( 'couchbase', 'MD5 of a column', 'SQL++ has no MD5; only MD5 of a literal is computed by the driver' );
            return $value === null ? 'NULL' : self::stringLiteral( md5( (string)$value ) );
        }
        if ( $name === 'GROUP_CONCAT' )
        {
            $separator = ',';
            $last = $parts ? $parts[count( $parts ) - 1] : array();
            $sep = expCouchSQLLexer::findTopLevel( $last, array( 'SEPARATOR' ) );
            // SEPARATOR is no keyword of the lexer: an identifier
            foreach ( $last as $k => $tok )
                if ( $tok[0] === 'id' && strtoupper( $tok[2] ) === 'SEPARATOR' && isset( $last[$k + 1] ) && $last[$k + 1][0] === 'str' )
                {
                    $separator = $last[$k + 1][2];
                    $parts[count( $parts ) - 1] = array_slice( $last, 0, $k );
                    break;
                }
            unset( $sep );
            $order = expCouchSQLLexer::findTopLevel( $parts[0], array( 'ORDER' ) );
            if ( $order >= 0 )
                throw new expDBUnsupportedException( 'couchbase', 'GROUP_CONCAT ... ORDER BY' );
            return 'CONCAT2(' . self::stringLiteral( $separator ) . ', ARRAY_AGG(' . $distinct . 'TOSTRING(' . $this->expression( $parts[0], $scope ) . ')))';
        }
        $sql = array();
        foreach ( $parts as $part )
            $sql[] = count( $part ) === 1 && expCouchSQLLexer::isChar( $part[0], '*' ) ? '*' : $this->expression( $part, $scope );
        switch ( $name )
        {
            case 'CONCAT':
                foreach ( $sql as $k => $arg )
                    $sql[$k] = $arg[0] === '"' ? $arg : 'TOSTRING(' . $arg . ')';
                return 'CONCAT(' . implode( ', ', $sql ) . ( count( $sql ) === 1 ? ', ""' : '' ) . ')';
            case 'CONCAT_WS':
                $separator = array_shift( $sql );
                foreach ( $sql as $k => $arg )
                    $sql[$k] = 'TOSTRING(' . $arg . ')';
                return 'CONCAT2(' . $separator . ', ' . implode( ', ', $sql ) . ')';
            case 'SUBSTRING':
            case 'SUBSTR':
            case 'MID':
                return 'SUBSTR1(TOSTRING(' . $sql[0] . '), ' . implode( ', ', array_slice( $sql, 1 ) ) . ')';
            case 'CHAR_LENGTH':
            case 'CHARACTER_LENGTH':
            case 'LENGTH':
                return 'LENGTH(TOSTRING(' . $sql[0] . '))';
            case 'IFNULL':
            case 'COALESCE':
            case 'NVL':
                return 'IFMISSINGORNULL(' . implode( ', ', $sql ) . ')';
            case 'IF':
            case 'IIF':
                if ( count( $sql ) !== 3 )
                    throw new expDBUnsupportedException( 'couchbase', 'IF with ' . count( $sql ) . ' arguments' );
                return 'CASE WHEN ' . $sql[0] . ' THEN ' . $sql[1] . ' ELSE ' . $sql[2] . ' END';
            case 'UNIX_TIMESTAMP':
                return $sql ? 'TRUNC(STR_TO_MILLIS(' . $sql[0] . ') / 1000)' : 'TRUNC(NOW_MILLIS() / 1000)';
            case 'FROM_UNIXTIME':
                return 'MILLIS_TO_STR(' . $sql[0] . ' * 1000, "1111-11-11 00:00:00")';
            case 'NOW':
            case 'CURRENT_TIMESTAMP':
                return 'MILLIS_TO_STR(NOW_MILLIS(), "1111-11-11 00:00:00")';
            case 'RAND':
            case 'RANDOM':
                return 'RANDOM()';
            case 'CEILING':
                return 'CEIL(' . implode( ', ', $sql ) . ')';
            case 'LEFT':
                return 'SUBSTR1(TOSTRING(' . $sql[0] . '), 1, ' . $sql[1] . ')';
            case 'LCASE':
                return 'LOWER(' . $sql[0] . ')';
            case 'UCASE':
                return 'UPPER(' . $sql[0] . ')';
            case 'INSTR':
                return 'POSITION1(' . $sql[0] . ', ' . $sql[1] . ')';
            case 'LOCATE':
                return 'POSITION1(' . $sql[1] . ', ' . $sql[0] . ')';
            case 'BIT_AND':
            case 'BIT_OR':
                throw new expDBUnsupportedException( 'couchbase', $name );
        }
        if ( in_array( $name, self::SAME_FUNCTIONS, true ) )
            return $name . '(' . $distinct . implode( ', ', $sql ) . ')';
        throw new expDBUnsupportedException( 'couchbase', 'function ' . $name );
    }

    /**
     * a & b, a | b, a ^ b, a << b, a >> b as BITAND(), BITOR(), BITXOR(), BITSHIFT(): on the operands next to the
     * operator (a column, a literal, a group, a function), which is how the kernel writes them.
     *
     * @param array[] $out
     * @return array[]
     */
    protected function bitOperators( array $out )
    {
        $functions = array( '&' => 'BITAND', '|' => 'BITOR', '^' => 'BITXOR', '<<' => 'BITSHIFT', '>>' => 'BITSHIFT' );
        for ( $k = 0; $k < count( $out ); $k++ )
        {
            $t = $out[$k];
            if ( $t[0] !== 'op' || !isset( $functions[$t[1]] ) )
                continue;
            if ( $k === 0 || !isset( $out[$k + 1] ) )
                throw new expDBUnsupportedException( 'couchbase', 'operator ' . $t[1] . ' without two operands' );
            $left = $out[$k - 1];
            $right = $out[$k + 1];
            $rightSpan = 1;
            if ( $right[0] === 'op' && ( $right[1] === '-' || $right[1] === '~' ) && isset( $out[$k + 2] ) )
            {
                $right = array( 'other', $right[1] === '-' ? '-' . $out[$k + 2][1] : 'BITNOT(' . $out[$k + 2][1] . ')', null );
                $rightSpan = 2;
            }
            if ( in_array( $left[0], array( 'op', 'kw', 'punc' ), true ) || in_array( $right[0], array( 'op', 'kw', 'punc' ), true ) )
                throw new expDBUnsupportedException( 'couchbase', 'operator ' . $t[1] . ' between expressions', 'put the operands in parentheses' );
            $call = $functions[$t[1]] . '(' . $left[1] . ', ' . $right[1] . ( $t[1] === '<<' ? '' : ( $t[1] === '>>' ? '' : '' ) ) . ')';
            if ( $t[1] === '<<' )
                $call = 'BITSHIFT(' . $left[1] . ', ' . $right[1] . ')';
            elseif ( $t[1] === '>>' )
                $call = 'BITSHIFT(' . $left[1] . ', -(' . $right[1] . '))';
            array_splice( $out, $k - 1, 2 + $rightSpan, array( array( 'other', $call, 'int' ) ) );
            $k -= 1;
        }
        return $out;
    }

    // ------------------------------------------------------------------ UPDATE, DELETE

    /**
     * An UPDATE or DELETE as SQL++, with what the driver needs to know.
     *
     * @param string $sql
     * @return array( 'kind' => update|delete, 'table' => ..., 'alias' => ..., 'sql' => SQL++ without RETURNING,
     *                'keys_changed' => whether a column of the key is set, 'set' => column => SQL++ )
     * @throws expDBUnsupportedException
     */
    public function write( $sql )
    {
        $this->reset();
        $tokens = expCouchSQLLexer::tokenize( $sql );
        $first = $tokens ? $tokens[0][2] : '';
        if ( $first === 'TRUNCATE' )
        {
            $k = 1;
            if ( isset( $tokens[$k] ) && expCouchSQLLexer::isKeyword( $tokens[$k], 'TABLE' ) )
                $k++;
            $table = strtolower( $tokens[$k][2] );
            return array( 'kind' => 'delete', 'table' => $table, 'alias' => $table, 'sql' => 'DELETE FROM ' . self::quoteName( $table ), 'keys_changed' => false, 'set' => array() );
        }
        if ( $first === 'DELETE' )
        {
            if ( !isset( $tokens[1] ) || !expCouchSQLLexer::isKeyword( $tokens[1], 'FROM' ) )
                throw new expDBUnsupportedException( 'couchbase', 'DELETE of several tables' );
            $rest = array_slice( $tokens, 2 );
            $whereAt = expCouchSQLLexer::findTopLevel( $rest, array( 'WHERE', 'ORDER', 'LIMIT' ) );
            $ref = $whereAt >= 0 ? array_slice( $rest, 0, $whereAt ) : $rest;
            list( $table, $alias ) = $this->tableRef( $ref );
            $scope = array( $alias => $table );
            $sqlpp = 'DELETE FROM ' . self::quoteName( $table ) . ' AS ' . self::quoteName( $alias );
            $sqlpp .= $whereAt >= 0 ? $this->useKeys( $table, $alias, array_slice( $rest, $whereAt ) ) : '';
            $sqlpp .= $whereAt >= 0 ? $this->writeTail( array_slice( $rest, $whereAt ), $scope ) : '';
            return array( 'kind' => 'delete', 'table' => $table, 'alias' => $alias, 'sql' => $sqlpp, 'keys_changed' => false, 'set' => array() );
        }
        if ( $first !== 'UPDATE' )
            throw new expDBUnsupportedException( 'couchbase', 'statement ' . $first );
        $rest = array_slice( $tokens, 1 );
        if ( $rest && expCouchSQLLexer::isKeyword( $rest[0], 'IGNORE' ) )
            array_shift( $rest );
        $setAt = expCouchSQLLexer::findTopLevel( $rest, array( 'SET' ) );
        if ( $setAt < 0 )
            throw new expDBUnsupportedException( 'couchbase', 'UPDATE without SET' );
        list( $table, $alias ) = $this->tableRef( array_slice( $rest, 0, $setAt ) );
        $scope = array( $alias => $table );
        $tail = array_slice( $rest, $setAt + 1 );
        $whereAt = expCouchSQLLexer::findTopLevel( $tail, array( 'WHERE', 'ORDER', 'LIMIT' ) );
        $assignments = $whereAt >= 0 ? array_slice( $tail, 0, $whereAt ) : $tail;
        $set = array();
        $keyColumns = isset( $this->catalogue[$table]['primary'] ) ? $this->catalogue[$table]['primary'] : array();
        $keysChanged = false;
        foreach ( expCouchSQLLexer::splitTopLevel( $assignments ) as $assignment )
        {
            $eq = -1;
            foreach ( $assignment as $k => $tok )
                if ( $tok[0] === 'op' && $tok[2] === '=' )
                {
                    $eq = $k;
                    break;
                }
            if ( $eq < 1 )
                throw new expDBUnsupportedException( 'couchbase', 'assignment', expCouchSQLLexer::text( $assignment ) );
            $target = array_slice( $assignment, 0, $eq );
            $column = strtolower( $target[count( $target ) - 1][2] );
            $valueTokens = array_slice( $assignment, $eq + 1 );
            $isLiteral = false;
            $value = expCouchSQLLexer::literal( $valueTokens, $isLiteral );
            $kind = isset( $this->catalogue[$table]['fields'][$column] ) ? $this->catalogue[$table]['fields'][$column] : null;
            $set[$column] = $isLiteral ? self::literal( self::coerce( $value, $kind ) ) : $this->expression( $valueTokens, $scope );
            if ( in_array( $column, $keyColumns, true ) )
                $keysChanged = true;
        }
        $parts = array();
        foreach ( $set as $column => $value )
            $parts[] = self::quoteName( $alias ) . '.' . self::quoteName( $column ) . ' = ' . $value;
        $sqlpp = 'UPDATE ' . self::quoteName( $table ) . ' AS ' . self::quoteName( $alias )
               . ( $whereAt >= 0 ? $this->useKeys( $table, $alias, array_slice( $tail, $whereAt ) ) : '' ) . ' SET ' . implode( ', ', $parts );
        $sqlpp .= $whereAt >= 0 ? $this->writeTail( array_slice( $tail, $whereAt ), $scope ) : '';
        return array( 'kind' => 'update', 'table' => $table, 'alias' => $alias, 'sql' => $sqlpp, 'keys_changed' => $keysChanged, 'set' => $set );
    }

    /**
     * The table of an UPDATE or DELETE: array( table, alias ).
     */
    protected function tableRef( array $ref )
    {
        if ( !$ref || $ref[0][0] !== 'id' || count( $ref ) > 3 || expCouchSQLLexer::findTopLevel( $ref, array( 'JOIN' ) ) >= 0
             || array_filter( $ref, function ( $t ) { return expCouchSQLLexer::isChar( $t, ',' ); } ) )
            throw new expDBUnsupportedException( 'couchbase', 'UPDATE or DELETE of several tables' );
        $table = strtolower( $ref[0][2] );
        $alias = $table;
        if ( isset( $ref[1] ) && expCouchSQLLexer::isKeyword( $ref[1], 'AS' ) && isset( $ref[2] ) )
            $alias = $ref[2][2];
        elseif ( isset( $ref[1] ) && $ref[1][0] === 'id' )
            $alias = $ref[1][2];
        return array( $table, $alias );
    }

    /**
     * USE KEYS [...] for the WHERE (with its ORDER BY and LIMIT) of an UPDATE or DELETE, or ''.
     */
    protected function useKeys( $table, $alias, array $tail )
    {
        if ( !$tail || !expCouchSQLLexer::isKeyword( $tail[0], 'WHERE' ) )
            return '';
        $end = expCouchSQLLexer::findTopLevel( $tail, array( 'ORDER', 'LIMIT' ) );
        $keys = $this->keysFromWhere( $table, $alias, array_slice( $tail, 1, $end >= 0 ? $end - 1 : null ) );
        return $keys === null ? '' : ' USE KEYS ' . self::keysLiteral( $keys );
    }

    /**
     * The keys of the documents a WHERE names by the whole primary key (col = literal AND ..., or col IN ( literals )
     * for one column of it), or null when it does not.
     *
     * @param string $table
     * @param string $alias
     * @param array[] $tokens the condition
     * @return string[]|null
     */
    public function keysFromWhere( $table, $alias, array $tokens )
    {
        $primary = isset( $this->catalogue[$table]['primary'] ) ? $this->catalogue[$table]['primary'] : array();
        if ( !$primary || !$tokens )
            return null;
        // a condition in parentheses as a whole
        while ( expCouchSQLLexer::isChar( $tokens[0], '(' ) && expCouchSQLLexer::closing( $tokens, 0 ) === count( $tokens ) - 1 )
            $tokens = array_slice( $tokens, 1, -1 );
        if ( expCouchSQLLexer::findTopLevel( $tokens, array( 'OR', 'XOR' ) ) >= 0 )
            return null;
        $values = array();
        // the conjuncts: split at AND outside parentheses (and outside BETWEEN ... AND)
        $conjuncts = array();
        $current = array();
        $depth = 0;
        $between = false;
        foreach ( $tokens as $t )
        {
            if ( expCouchSQLLexer::isChar( $t, '(' ) )
                $depth++;
            elseif ( expCouchSQLLexer::isChar( $t, ')' ) )
                $depth--;
            if ( $depth === 0 && expCouchSQLLexer::isKeyword( $t, 'BETWEEN' ) )
                $between = true;
            elseif ( $depth === 0 && $between && expCouchSQLLexer::isKeyword( $t, 'AND' ) )
                $between = false;
            elseif ( $depth === 0 && expCouchSQLLexer::isKeyword( $t, 'AND' ) )
            {
                $conjuncts[] = $current;
                $current = array();
                continue;
            }
            $current[] = $t;
        }
        $conjuncts[] = $current;
        foreach ( $conjuncts as $c )
        {
            while ( $c && expCouchSQLLexer::isChar( $c[0], '(' ) && expCouchSQLLexer::closing( $c, 0 ) === count( $c ) - 1 )
                $c = array_slice( $c, 1, -1 );
            $column = null;
            $rest = $c;
            if ( count( $c ) >= 3 && $c[0][0] === 'id' && expCouchSQLLexer::isChar( $c[1], '.' ) && $c[2][0] === 'id' )
            {
                if ( strcasecmp( $c[0][2], $alias ) !== 0 && strcasecmp( $c[0][2], $table ) !== 0 )
                    continue;
                $column = strtolower( $c[2][2] );
                $rest = array_slice( $c, 3 );
            }
            elseif ( $c && $c[0][0] === 'id' && !( isset( $c[1] ) && expCouchSQLLexer::isChar( $c[1], '(' ) ) )
            {
                $column = strtolower( $c[0][2] );
                $rest = array_slice( $c, 1 );
            }
            if ( $column === null || !in_array( $column, $primary, true ) || !$rest )
                continue;
            $kind = isset( $this->catalogue[$table]['fields'][$column] ) ? $this->catalogue[$table]['fields'][$column] : null;
            if ( $rest[0][0] === 'op' && $rest[0][2] === '=' )
            {
                $isLiteral = false;
                $value = expCouchSQLLexer::literal( array_slice( $rest, 1 ), $isLiteral );
                if ( $isLiteral && $value !== null )
                    $values[$column] = array( self::coerce( $value, $kind ) );
            }
            elseif ( expCouchSQLLexer::isKeyword( $rest[0], 'IN' ) && isset( $rest[1] ) && expCouchSQLLexer::isChar( $rest[1], '(' )
                     && expCouchSQLLexer::closing( $rest, 1 ) === count( $rest ) - 1 && !( isset( $rest[2] ) && expCouchSQLLexer::isKeyword( $rest[2], 'SELECT' ) ) )
            {
                $list = array();
                foreach ( expCouchSQLLexer::splitTopLevel( array_slice( $rest, 2, -1 ) ) as $item )
                {
                    $isLiteral = false;
                    $value = expCouchSQLLexer::literal( $item, $isLiteral );
                    if ( !$isLiteral || $value === null )
                        return null;
                    $list[] = self::coerce( $value, $kind );
                }
                if ( $list )
                    $values[$column] = $list;
            }
        }
        $keys = array( array() );
        foreach ( $primary as $column )
        {
            if ( !isset( $values[$column] ) )
                return null;
            $next = array();
            foreach ( $keys as $partial )
                foreach ( array_unique( $values[$column], SORT_REGULAR ) as $value )
                    $next[] = $partial + array( $column => $value );
            $keys = $next;
            if ( count( $keys ) > self::MAX_KEYS )
                return null;
        }
        $out = array();
        foreach ( $keys as $row )
            $out[] = $this->documentKey( $table, $row );
        return array_values( array_unique( $out ) );
    }

    /**
     * The key of a row's document: the primary key's values (one: the value as text; several: a JSON array of them),
     * or null when the table has none or a value is missing.
     *
     * @param string $table
     * @param array $row column => value (as stored)
     * @return string|null
     */
    public function documentKey( $table, array $row )
    {
        $primary = isset( $this->catalogue[$table]['primary'] ) ? $this->catalogue[$table]['primary'] : array();
        if ( !$primary )
            return null;
        $parts = array();
        foreach ( $primary as $column )
        {
            if ( !array_key_exists( $column, $row ) || $row[$column] === null )
                return null;
            $kind = isset( $this->catalogue[$table]['fields'][$column] ) ? $this->catalogue[$table]['fields'][$column] : null;
            $parts[] = (string)self::coerce( $row[$column], $kind === null ? 'text' : ( $kind === 'text' ? 'text' : $kind ) );
        }
        return count( $parts ) === 1 ? $parts[0] : json_encode( $parts, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    }

    /** A list of keys as a SQL++ array */
    public static function keysLiteral( array $keys )
    {
        return json_encode( array_values( array_map( 'strval', $keys ) ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
    }

    /** The catalogue's entry of a table, or null */
    public function table( $table )
    {
        return isset( $this->catalogue[$table] ) ? $this->catalogue[$table] : null;
    }

    /**
     * WHERE ... [ORDER BY ...] [LIMIT n] of an UPDATE or DELETE.
     */
    protected function writeTail( array $tokens, array $scope )
    {
        $sql = '';
        $orderAt = expCouchSQLLexer::findTopLevel( $tokens, array( 'ORDER' ) );
        $limitAt = expCouchSQLLexer::findTopLevel( $tokens, array( 'LIMIT' ) );
        $whereEnd = $orderAt >= 0 ? $orderAt : ( $limitAt >= 0 ? $limitAt : count( $tokens ) );
        if ( expCouchSQLLexer::isKeyword( $tokens[0], 'WHERE' ) )
            $sql .= ' WHERE ' . $this->expression( array_slice( $tokens, 1, $whereEnd - 1 ), $scope );
        if ( $limitAt >= 0 )
        {
            if ( $orderAt >= 0 )
                throw new expDBUnsupportedException( 'couchbase', 'UPDATE or DELETE with ORDER BY and LIMIT' );
            $sql .= ' LIMIT ' . $this->expression( array_slice( $tokens, $limitAt + 1 ), $scope );
        }
        return $sql;
    }

    // ------------------------------------------------------------------ INSERT

    /**
     * An INSERT (or REPLACE) parsed: the driver makes the documents.
     *
     * @param string $sql
     * @return array( 'table', 'columns' => names, 'rows' => list of list of array( 'literal' => bool, 'value' => PHP value
     *                or SQL++ ), 'mode' => insert|replace|ignore, 'update' => column => SQL++ with VALUES(c) as
     *                array, or null, 'select' => SQL++ of INSERT ... SELECT or null )
     * @throws expDBUnsupportedException
     */
    public function insert( $sql )
    {
        $this->reset();
        $tokens = expCouchSQLLexer::tokenize( $sql );
        $i = 0;
        $mode = 'insert';
        if ( expCouchSQLLexer::isKeyword( $tokens[0], 'REPLACE' ) )
            $mode = 'replace';
        $i++;
        if ( isset( $tokens[$i] ) && expCouchSQLLexer::isKeyword( $tokens[$i], 'IGNORE' ) )
        {
            $mode = 'ignore';
            $i++;
        }
        if ( isset( $tokens[$i] ) && expCouchSQLLexer::isKeyword( $tokens[$i], 'OR' ) )
        {
            // SQLite's INSERT OR REPLACE / OR IGNORE
            $mode = isset( $tokens[$i + 1] ) && expCouchSQLLexer::isKeyword( $tokens[$i + 1], 'REPLACE' ) ? 'replace' : 'ignore';
            $i += 2;
        }
        if ( isset( $tokens[$i] ) && expCouchSQLLexer::isKeyword( $tokens[$i], 'INTO' ) )
            $i++;
        if ( !isset( $tokens[$i] ) || $tokens[$i][0] !== 'id' )
            throw new expDBUnsupportedException( 'couchbase', 'INSERT without a table' );
        $table = strtolower( $tokens[$i][2] );
        $i++;
        $columns = null;
        if ( isset( $tokens[$i] ) && expCouchSQLLexer::isChar( $tokens[$i], '(' ) )
        {
            $close = expCouchSQLLexer::closing( $tokens, $i );
            $columns = array();
            foreach ( expCouchSQLLexer::splitTopLevel( array_slice( $tokens, $i + 1, $close - $i - 1 ) ) as $c )
                $columns[] = strtolower( $c[count( $c ) - 1][2] );
            $i = $close + 1;
        }
        if ( $columns === null )
            $columns = isset( $this->catalogue[$table]['order'] ) ? $this->catalogue[$table]['order'] : null;
        $rows = array();
        $select = null;
        $update = null;
        $scope = array( $table => $table );
        if ( isset( $tokens[$i] ) && expCouchSQLLexer::isKeyword( $tokens[$i], 'DEFAULT' ) )
        {
            // DEFAULT VALUES
            $rows[] = array();
            $columns = array();
            $i += 2;
        }
        elseif ( isset( $tokens[$i] ) && expCouchSQLLexer::isKeyword( $tokens[$i], 'VALUES' ) )
        {
            $i++;
            $n = count( $tokens );
            while ( $i < $n && expCouchSQLLexer::isChar( $tokens[$i], '(' ) )
            {
                $close = expCouchSQLLexer::closing( $tokens, $i );
                $row = array();
                foreach ( expCouchSQLLexer::splitTopLevel( array_slice( $tokens, $i + 1, $close - $i - 1 ) ) as $k => $valueTokens )
                {
                    $isLiteral = false;
                    $value = expCouchSQLLexer::literal( $valueTokens, $isLiteral );
                    if ( !$isLiteral && count( $valueTokens ) === 1 && expCouchSQLLexer::isKeyword( $valueTokens[0], 'DEFAULT' ) )
                        $row[] = array( 'literal' => true, 'default' => true, 'value' => null );
                    else
                        $row[] = $isLiteral ? array( 'literal' => true, 'value' => $value ) : array( 'literal' => false, 'value' => $this->expression( $valueTokens, array() ) );
                }
                if ( $columns === null || count( $row ) !== count( $columns ) )
                    throw new expDBUnsupportedException( 'couchbase', 'INSERT whose values do not match its columns' );
                $rows[] = $row;
                $i = $close + 1;
                if ( isset( $tokens[$i] ) && expCouchSQLLexer::isChar( $tokens[$i], ',' ) )
                    $i++;
            }
        }
        elseif ( isset( $tokens[$i] ) && expCouchSQLLexer::isKeyword( $tokens[$i], 'SELECT' ) )
        {
            $end = expCouchSQLLexer::findTopLevel( $tokens, array( 'ON' ), $i );
            $selectTokens = array_slice( $tokens, $i, $end >= 0 ? $end - $i : null );
            $selectColumns = null;
            $select = $this->query( $selectTokens, 'top', $selectColumns );
            if ( $columns === null )
                throw new expDBUnsupportedException( 'couchbase', 'INSERT ... SELECT into a table of unknown columns' );
            $i = $end >= 0 ? $end : count( $tokens );
        }
        else
            throw new expDBUnsupportedException( 'couchbase', 'INSERT form', mb_substr( (string)$sql, 0, 80 ) );

        if ( isset( $tokens[$i] ) && expCouchSQLLexer::isKeyword( $tokens[$i], 'ON' ) )
        {
            // ON DUPLICATE KEY UPDATE a = VALUES(a), b = b + 1
            if ( !isset( $tokens[$i + 1] ) || !expCouchSQLLexer::isKeyword( $tokens[$i + 1], 'DUPLICATE' ) )
                throw new expDBUnsupportedException( 'couchbase', 'ON CONFLICT' );
            $update = array();
            foreach ( expCouchSQLLexer::splitTopLevel( array_slice( $tokens, $i + 4 ) ) as $assignment )
            {
                $column = strtolower( $assignment[0][2] );
                $valueTokens = array_slice( $assignment, 2 );
                // VALUES(col): the value the row would have inserted
                if ( count( $valueTokens ) === 4 && expCouchSQLLexer::isKeyword( $valueTokens[0], 'VALUES' ) )
                    $update[$column] = array( 'values' => strtolower( $valueTokens[2][2] ) );
                else
                    $update[$column] = array( 'sql' => $this->expression( $valueTokens, $scope ) );
            }
        }
        return array( 'table' => $table, 'columns' => $columns, 'rows' => $rows, 'mode' => $mode, 'update' => $update, 'select' => $select );
    }

    // ------------------------------------------------------------------ DDL

    /**
     * CREATE TABLE, CREATE INDEX, DROP, ALTER TABLE ADD/DROP: what the driver does.
     *
     * The CouchDB driver uses the same parser for its DDL, with SQLite's strings ($backslashEscapes false).
     *
     * @param string $sql
     * @param bool $backslashEscapes
     * @return array( 'action' => create_table|drop_table|create_index|drop_index|add_column|drop_column, ... )
     * @throws expDBUnsupportedException
     */
    public function ddl( $sql, $backslashEscapes = true )
    {
        $tokens = expCouchSQLLexer::tokenize( $sql, $backslashEscapes );
        $words = array();
        foreach ( array_slice( $tokens, 0, 6 ) as $t )
            $words[] = $t[0] === 'kw' ? $t[2] : '';
        if ( $words[0] === 'CREATE' )
        {
            $i = 1;
            $temporary = false;
            $unique = false;
            if ( expCouchSQLLexer::isKeyword( $tokens[$i], 'TEMPORARY' ) || ( $tokens[$i][0] === 'id' && strtoupper( $tokens[$i][2] ) === 'TEMP' ) )
            {
                $temporary = true;
                $i++;
            }
            if ( expCouchSQLLexer::isKeyword( $tokens[$i], 'UNIQUE' ) )
            {
                $unique = true;
                $i++;
            }
            if ( expCouchSQLLexer::isKeyword( $tokens[$i], 'TABLE' ) )
                return $this->createTable( array_slice( $tokens, $i + 1 ), $temporary );
            if ( expCouchSQLLexer::isKeyword( $tokens[$i], 'INDEX' ) )
            {
                $i++;
                if ( expCouchSQLLexer::isKeyword( $tokens[$i], 'IF' ) )
                    $i += 3;
                $name = $tokens[$i][2];
                $on = $i + 1;
                $table = strtolower( $tokens[$on + 1][2] );
                $open = $on + 2;
                $close = expCouchSQLLexer::closing( $tokens, $open );
                $fields = array();
                foreach ( expCouchSQLLexer::splitTopLevel( array_slice( $tokens, $open + 1, $close - $open - 1 ) ) as $f )
                    $fields[] = strtolower( $f[0][2] );
                return array( 'action' => 'create_index', 'table' => $table, 'name' => $name, 'fields' => $fields, 'unique' => $unique );
            }
        }
        if ( $words[0] === 'DROP' )
        {
            $i = 1;
            if ( $tokens[$i][0] === 'id' && strtoupper( $tokens[$i][2] ) === 'TEMPORARY' || expCouchSQLLexer::isKeyword( $tokens[$i], 'TEMPORARY' ) )
                $i++;
            $what = $tokens[$i][2];
            $i++;
            if ( isset( $tokens[$i] ) && expCouchSQLLexer::isKeyword( $tokens[$i], 'IF' ) )
                $i += 2;
            if ( $what === 'TABLE' )
            {
                $tables = array();
                foreach ( expCouchSQLLexer::splitTopLevel( array_slice( $tokens, $i ) ) as $t )
                    $tables[] = strtolower( $t[count( $t ) - 1][2] );
                return array( 'action' => 'drop_table', 'tables' => $tables );
            }
            if ( $what === 'INDEX' )
            {
                $name = $tokens[$i][2];
                $table = isset( $tokens[$i + 2] ) ? strtolower( $tokens[$i + 2][2] ) : null;
                return array( 'action' => 'drop_index', 'name' => $name, 'table' => $table );
            }
        }
        if ( $words[0] === 'ALTER' && $words[1] === 'TABLE' )
        {
            $table = strtolower( $tokens[2][2] );
            $rest = array_slice( $tokens, 3 );
            if ( expCouchSQLLexer::isKeyword( $rest[0], 'ADD' ) )
            {
                $rest = array_slice( $rest, 1 );
                if ( expCouchSQLLexer::isKeyword( $rest[0], 'COLUMN' ) )
                    $rest = array_slice( $rest, 1 );
                if ( expCouchSQLLexer::isKeyword( $rest[0], array( 'INDEX', 'KEY', 'UNIQUE', 'PRIMARY' ) ) )
                {
                    $open = 0;
                    while ( isset( $rest[$open] ) && !expCouchSQLLexer::isChar( $rest[$open], '(' ) )
                        $open++;
                    $name = $rest[$open - 1][0] === 'id' ? $rest[$open - 1][2] : $table . '_index';
                    $close = expCouchSQLLexer::closing( $rest, $open );
                    $fields = array();
                    foreach ( expCouchSQLLexer::splitTopLevel( array_slice( $rest, $open + 1, $close - $open - 1 ) ) as $f )
                        $fields[] = strtolower( $f[0][2] );
                    return array( 'action' => 'create_index', 'table' => $table, 'name' => $name, 'fields' => $fields, 'unique' => expCouchSQLLexer::isKeyword( $rest[0], 'UNIQUE' ) );
                }
                $column = self::columnDefinition( $rest );
                return array( 'action' => 'add_column', 'table' => $table, 'column' => $column['name'], 'field' => $column['field'] );
            }
            if ( expCouchSQLLexer::isKeyword( $rest[0], 'DROP' ) )
            {
                $rest = array_slice( $rest, 1 );
                if ( expCouchSQLLexer::isKeyword( $rest[0], array( 'INDEX', 'KEY' ) ) )
                    return array( 'action' => 'drop_index', 'table' => $table, 'name' => $rest[1][2] );
                if ( expCouchSQLLexer::isKeyword( $rest[0], 'COLUMN' ) )
                    $rest = array_slice( $rest, 1 );
                return array( 'action' => 'drop_column', 'table' => $table, 'column' => strtolower( $rest[0][2] ) );
            }
            // MODIFY / CHANGE / ALTER COLUMN: documents keep any value; the definition changes
            if ( $rest[0][0] === 'id' && in_array( strtoupper( $rest[0][2] ), array( 'MODIFY', 'CHANGE' ), true ) || expCouchSQLLexer::isKeyword( $rest[0], 'ALTER' ) )
            {
                $rest = array_slice( $rest, 1 );
                if ( expCouchSQLLexer::isKeyword( $rest[0], 'COLUMN' ) )
                    $rest = array_slice( $rest, 1 );
                $column = self::columnDefinition( $rest );
                return array( 'action' => 'modify_column', 'table' => $table, 'column' => $column['name'], 'field' => $column['field'] );
            }
        }
        throw new expDBUnsupportedException( 'couchbase', 'DDL', mb_substr( (string)$sql, 0, 80 ) );
    }

    /**
     * CREATE TABLE name ( columns, keys ) as a .dba table definition.
     */
    protected function createTable( array $tokens, $temporary )
    {
        $i = 0;
        if ( expCouchSQLLexer::isKeyword( $tokens[0], 'IF' ) )
            $i = 3;
        $table = strtolower( $tokens[$i][2] );
        $open = $i + 1;
        if ( !isset( $tokens[$open] ) || !expCouchSQLLexer::isChar( $tokens[$open], '(' ) )
            throw new expDBUnsupportedException( 'couchbase', 'CREATE TABLE ... AS SELECT' );
        $close = expCouchSQLLexer::closing( $tokens, $open );
        $fields = array();
        $indexes = array();
        foreach ( expCouchSQLLexer::splitTopLevel( array_slice( $tokens, $open + 1, $close - $open - 1 ) ) as $part )
        {
            if ( !$part )
                continue;
            if ( expCouchSQLLexer::isKeyword( $part[0], array( 'PRIMARY', 'KEY', 'INDEX', 'UNIQUE' ) ) || ( $part[0][0] === 'id' && strtoupper( $part[0][2] ) === 'CONSTRAINT' ) )
            {
                $p = 0;
                while ( isset( $part[$p] ) && !expCouchSQLLexer::isChar( $part[$p], '(' ) )
                    $p++;
                if ( !isset( $part[$p] ) )
                    continue;
                $pClose = expCouchSQLLexer::closing( $part, $p );
                $cols = array();
                foreach ( expCouchSQLLexer::splitTopLevel( array_slice( $part, $p + 1, $pClose - $p - 1 ) ) as $f )
                    $cols[] = strtolower( $f[0][2] );
                $isPrimary = false;
                $isUnique = false;
                foreach ( array_slice( $part, 0, $p ) as $w )
                {
                    if ( expCouchSQLLexer::isKeyword( $w, 'PRIMARY' ) )
                        $isPrimary = true;
                    if ( expCouchSQLLexer::isKeyword( $w, 'UNIQUE' ) )
                        $isUnique = true;
                }
                $name = $isPrimary ? 'PRIMARY' : ( $p > 1 && $part[$p - 1][0] === 'id' ? $part[$p - 1][2] : $table . '_' . implode( '_', $cols ) );
                $indexes[$name] = array( 'type' => $isPrimary ? 'primary' : ( $isUnique ? 'unique' : 'non-unique' ), 'fields' => $cols );
                continue;
            }
            $column = self::columnDefinition( $part );
            $fields[$column['name']] = $column['field'];
            if ( $column['primary'] )
                $indexes['PRIMARY'] = array( 'type' => 'primary', 'fields' => array( $column['name'] ) );
        }
        return array( 'action' => 'create_table', 'table' => $table, 'temporary' => $temporary,
                      'definition' => array( 'name' => $table, 'fields' => $fields, 'indexes' => $indexes ) );
    }

    /**
     * One column of CREATE TABLE or ALTER TABLE ADD as a .dba field.
     *
     * @param array[] $part
     * @return array( 'name', 'field', 'primary' )
     */
    public static function columnDefinition( array $part )
    {
        $name = strtolower( $part[0][2] );
        $typeWord = isset( $part[1] ) ? strtolower( $part[1][2] ) : 'varchar';
        $length = null;
        $k = 2;
        if ( isset( $part[$k] ) && expCouchSQLLexer::isChar( $part[$k], '(' ) )
        {
            $close = expCouchSQLLexer::closing( $part, $k );
            $length = isset( $part[$k + 1] ) && $part[$k + 1][0] === 'num' ? (int)$part[$k + 1][2] : null;
            $k = $close + 1;
        }
        $field = array( 'type' => $typeWord, 'not_null' => '0', 'default' => false );
        $primary = false;
        $n = count( $part );
        for ( ; $k < $n; $k++ )
        {
            $w = $part[$k];
            if ( expCouchSQLLexer::isKeyword( $w, 'NOT' ) && isset( $part[$k + 1] ) && expCouchSQLLexer::isKeyword( $part[$k + 1], 'NULL' ) )
            {
                $field['not_null'] = '1';
                $k++;
            }
            elseif ( expCouchSQLLexer::isKeyword( $w, 'DEFAULT' ) && isset( $part[$k + 1] ) )
            {
                $isLiteral = false;
                $value = expCouchSQLLexer::literal( array( $part[$k + 1] ), $isLiteral );
                if ( !$isLiteral && $part[$k + 1][0] === 'op' && isset( $part[$k + 2] ) )
                {
                    $value = expCouchSQLLexer::literal( array( $part[$k + 1], $part[$k + 2] ), $isLiteral );
                    $k++;
                }
                $field['default'] = $isLiteral ? $value : false;
                $k++;
            }
            elseif ( ( $w[0] === 'id' && in_array( strtoupper( $w[2] ), array( 'AUTO_INCREMENT', 'AUTOINCREMENT', 'IDENTITY' ), true ) ) )
                $field['type'] = 'auto_increment';
            elseif ( expCouchSQLLexer::isKeyword( $w, 'PRIMARY' ) )
            {
                $primary = true;
                $k++;
            }
        }
        if ( in_array( $typeWord, array( 'serial', 'bigserial' ), true ) )
            $field['type'] = 'auto_increment';
        if ( $length !== null && $field['type'] !== 'auto_increment' )
            $field['length'] = $length;
        return array( 'name' => $name, 'field' => $field, 'primary' => $primary );
    }

    /**
     * The error number the kernel knows for a Query service error code (1062: a duplicate key).
     *
     * @param int $code
     * @param string $message
     * @return int
     */
    public static function errorNumber( $code, $message )
    {
        $code = (int)$code;
        if ( $code === 12009 && stripos( (string)$message, 'Duplicate Key' ) !== false )
            return 1062;
        if ( $code === 17012 )
            return 1062;
        if ( in_array( $code, array( 12003, 12021, 13014 ), true ) )
            return 1146;
        return $code ? $code : 1;
    }
}
?>
