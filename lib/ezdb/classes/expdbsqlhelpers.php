<?php
/**
 * File containing the expDBSQLHelpers class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The SQL of the portable helpers every driver offers (eZDBInterface::sqlLimit(), sqlUpsert(), sqlGroupConcat(),
 * sqlUnixTimestamp(), sqlFromUnixTimestamp(), sqlQuoteIdentifier()), per SQL dialect: mysql (MySQL and MariaDB), duckdb,
 * postgresql, sqlite, oracle, sqlserver, msaccess (MS Access through ODBC, expMSAccessDB), firebird (expFirebirdDB),
 * db2 (expDb2DB), informix (expInformixDB), cassandra (expCassandraDB: the SQL its driver answers on CQL),
 * couchbase (expCouchbaseDB: MySQL's forms, which its driver turns into SQL++; a LIMIT without a number is an OFFSET
 * alone, as DuckDB's), monetdb (expMonetDBDB), clickhouse (expClickHouseDB);
 * hana (SAP HANA, expHANADB), ase (SAP ASE, expASEDB);
 * generic is ANSI SQL. Kernel code that would otherwise write MySQL's LIMIT,
 * ON DUPLICATE KEY UPDATE, GROUP_CONCAT or UNIX_TIMESTAMP calls the helper of its database instead, so no code
 * branches on the database it runs on.
 *
 * Values are written with the driver's own literal function ($literal: value => SQL), so escaping stays the
 * driver's.
 */
class expDBSQLHelpers
{
    const DIALECTS = array( 'mysql', 'postgresql', 'sqlite', 'oracle', 'sqlserver', 'msaccess', 'firebird', 'duckdb', 'db2', 'informix', 'cassandra', 'couchbase',
                            'monetdb', 'clickhouse', 'hana', 'ase', 'generic' );

    /**
     * The limit and offset as integers: limit false for "no limit" (false, null, '', negative or not a number).
     *
     * @return array( int|false, int )
     */
    public static function normalizeLimit( $limit, $offset )
    {
        $limit = ( $limit === false || $limit === null || $limit === '' || !is_numeric( $limit ) || $limit < 0 ) ? false : (int)$limit;
        $offset = is_numeric( $offset ) ? max( 0, (int)$offset ) : 0;
        return array( $limit, $offset );
    }

    /**
     * A name in the dialect's quotes; a dotted name part by part.
     *
     * @param string $dialect
     * @param string $name
     * @return string
     */
    public static function quoteIdentifier( $dialect, $name )
    {
        if ( $dialect === 'couchbase' )
            $dialect = 'mysql';
        if ( $dialect === 'sqlserver' )
            return expSQLServerDialect::quoteName( $name );
        if ( $dialect === 'msaccess' )
            return expMSAccessDialect::quoteName( $name );
        if ( $dialect === 'firebird' )
            return expFirebirdDialect::quoteName( $name );
        if ( $dialect === 'db2' )
            return expDb2Dialect::quoteName( $name );
        if ( $dialect === 'informix' )
            return expInformixDialect::quoteName( $name );
        if ( $dialect === 'hana' )
            return expHANADialect::quoteName( $name );
        if ( $dialect === 'ase' )
            return expASEDialect::quoteName( $name );
        $out = array();
        foreach ( explode( '.', (string)$name ) as $part )
        {
            if ( $part === '' || strpos( $part, "\0" ) !== false )
                throw new InvalidArgumentException( 'An empty name or a name with a NUL byte is no identifier' );
            $out[] = $dialect === 'mysql' ? '`' . str_replace( '`', '``', $part ) . '`' : '"' . str_replace( '"', '""', $part ) . '"';
        }
        return implode( '.', $out );
    }

    /**
     * A SELECT limited to $limit rows from $offset on; null when $limit is 0 (no rows: the caller sends nothing).
     *
     * @param string $dialect
     * @param string $sql
     * @param int|false|null $limit
     * @param int $offset
     * @return string|null
     */
    public static function limit( $dialect, $sql, $limit, $offset = 0 )
    {
        if ( $dialect === 'couchbase' )
            $dialect = 'duckdb';
        if ( $dialect === 'sqlserver' )
            return expSQLServerDialect::applyLimit( $sql, $limit, $offset );
        if ( $dialect === 'msaccess' )
            return expMSAccessDialect::applyLimit( $sql, $limit, $offset );
        if ( $dialect === 'firebird' )
            return expFirebirdDialect::applyLimit( $sql, $limit, $offset );
        if ( $dialect === 'db2' )
            return expDb2Dialect::applyLimit( $sql, $limit, $offset );
        if ( $dialect === 'informix' )
            return expInformixDialect::applyLimit( $sql, $limit, $offset );
        if ( $dialect === 'hana' )
            return expHANADialect::applyLimit( $sql, $limit, $offset );
        if ( $dialect === 'ase' )
            return expASEDialect::limitStatement( $sql, $limit, $offset );
        list( $limit, $offset ) = self::normalizeLimit( $limit, $offset );
        $sql = rtrim( (string)$sql, " \t\r\n;" );
        if ( $limit === 0 )
            return null;
        if ( $limit === false && $offset === 0 )
            return $sql;
        switch ( $dialect )
        {
            case 'oracle':
            case 'generic':
                return $sql . "\nOFFSET $offset ROWS" . ( $limit !== false ? " FETCH NEXT $limit ROWS ONLY" : '' );
            case 'mysql':
                return $sql . "\nLIMIT " . ( $limit === false ? '18446744073709551615' : $limit ) . ( $offset ? " OFFSET $offset" : '' );
            case 'sqlite':
                return $sql . "\nLIMIT " . ( $limit === false ? '-1' : $limit ) . ( $offset ? " OFFSET $offset" : '' );
            case 'duckdb':
            case 'monetdb':
            case 'clickhouse':
                // DuckDB, MonetDB and ClickHouse have no LIMIT ALL: an offset alone is OFFSET without LIMIT
                return $sql . ( $limit === false ? "\nOFFSET $offset" : "\nLIMIT $limit" . ( $offset ? " OFFSET $offset" : '' ) );
            default:
                return $sql . "\nLIMIT " . ( $limit === false ? 'ALL' : $limit ) . ( $offset ? " OFFSET $offset" : '' );
        }
    }

    /**
     * An insert-or-update of one row, atomic in the database: MySQL ON DUPLICATE KEY UPDATE (INSERT IGNORE without
     * update columns), PostgreSQL and SQLite ON CONFLICT (...) DO UPDATE / DO NOTHING, Oracle and SQL Server MERGE.
     * $keyColumns must be the columns of a primary key or unique index (MySQL decides by any unique key of the row).
     *
     * @param string $dialect
     * @param callable $literal function( mixed $value ): string
     * @param string $table
     * @param array $row column => value
     * @param string[] $keyColumns
     * @param string[]|null $updateColumns null: every column but the keys; array(): insert only
     * @return string
     */
    public static function upsert( $dialect, callable $literal, $table, array $row, array $keyColumns, $updateColumns = null )
    {
        if ( $dialect === 'couchbase' )
            $dialect = 'mysql';
        if ( $dialect === 'sqlserver' )
            return expSQLServerDialect::upsert( $table, $row, $keyColumns, $updateColumns );
        if ( $dialect === 'firebird' )
            return expFirebirdDialect::upsert( $table, $row, $keyColumns, $updateColumns );
        if ( $dialect === 'db2' )
            return expDb2Dialect::upsert( $table, $row, $keyColumns, $updateColumns );
        if ( $dialect === 'informix' )
            return expInformixDialect::upsert( $table, $row, $keyColumns, $updateColumns );
        if ( $dialect === 'cassandra' )
            return expCassandraDialect::upsert( $table, $row, $keyColumns, $updateColumns );
        if ( $dialect === 'hana' )
            return expHANADialect::upsert( $table, $row, $keyColumns, $updateColumns );
        if ( $dialect === 'ase' )
            return expASEDialect::upsert( $table, $row, $keyColumns, $updateColumns );
        if ( !$row || !$keyColumns )
            throw new InvalidArgumentException( 'An upsert needs at least one column and one key column' );
        foreach ( $keyColumns as $key )
            if ( !array_key_exists( $key, $row ) )
                throw new InvalidArgumentException( "The key column '$key' has no value" );
        if ( $dialect === 'msaccess' )
            throw new expDBUnsupportedException( 'msaccess', 'upsert', 'run an UPDATE and, when it changed no row, an INSERT' );
        if ( $dialect === 'clickhouse' )
            throw new expDBUnsupportedException( 'clickhouse', 'upsert', 'ClickHouse enforces no key: read the row first, then UPDATE or INSERT (one writer at a time)' );
        if ( $updateColumns === null )
            $updateColumns = array_values( array_diff( array_keys( $row ), $keyColumns ) );
        $q = function ( $name ) use ( $dialect ) { return expDBSQLHelpers::quoteIdentifier( $dialect, $name ); };
        $columns = implode( ', ', array_map( $q, array_keys( $row ) ) );
        $values = implode( ', ', array_map( $literal, array_values( $row ) ) );
        $t = $q( $table );
        switch ( $dialect )
        {
            case 'mysql':
                if ( !$updateColumns )
                    return "INSERT IGNORE INTO $t ($columns) VALUES ($values)";
                $set = array();
                foreach ( $updateColumns as $column )
                    $set[] = $q( $column ) . ' = VALUES(' . $q( $column ) . ')';
                return "INSERT INTO $t ($columns) VALUES ($values) ON DUPLICATE KEY UPDATE " . implode( ', ', $set );
            case 'oracle':
            case 'monetdb':
                // MonetDB: MERGE with a SELECT without FROM, the target unaliased (SET names its own columns bare)
                $source = array();
                foreach ( $row as $column => $value )
                    $source[] = $literal( $value ) . ' AS ' . $q( $column );
                $target = $dialect === 'monetdb' ? $t : 't';
                $on = array();
                foreach ( $keyColumns as $key )
                    $on[] = $target . '.' . $q( $key ) . ' = s.' . $q( $key );
                $sql = $dialect === 'monetdb'
                     ? "MERGE INTO $t USING (SELECT " . implode( ', ', $source ) . ') AS s ON (' . implode( ' AND ', $on ) . ')'
                     : "MERGE INTO $t t USING (SELECT " . implode( ', ', $source ) . ' FROM dual) s ON (' . implode( ' AND ', $on ) . ')';
                if ( $updateColumns )
                {
                    $set = array();
                    foreach ( $updateColumns as $column )
                        $set[] = ( $dialect === 'monetdb' ? '' : 't.' ) . $q( $column ) . ' = s.' . $q( $column );
                    $sql .= ' WHEN MATCHED THEN UPDATE SET ' . implode( ', ', $set );
                }
                $sValues = array();
                foreach ( array_keys( $row ) as $column )
                    $sValues[] = 's.' . $q( $column );
                return $sql . " WHEN NOT MATCHED THEN INSERT ($columns) VALUES (" . implode( ', ', $sValues ) . ')';
            default:
                $sql = "INSERT INTO $t ($columns) VALUES ($values) ON CONFLICT (" . implode( ', ', array_map( $q, $keyColumns ) ) . ')';
                if ( !$updateColumns )
                    return $sql . ' DO NOTHING';
                $set = array();
                foreach ( $updateColumns as $column )
                    $set[] = $q( $column ) . ' = excluded.' . $q( $column );
                return $sql . ' DO UPDATE SET ' . implode( ', ', $set );
        }
    }

    /**
     * A SELECT that locks the rows it reads until the end of the transaction: FOR UPDATE (MySQL, PostgreSQL, Oracle,
     * generic), FOR UPDATE WITH LOCK on Firebird, an isolation clause keeping update locks on Db2, table hints on SQL Server, the statement as it is on SQLite (whose write
     * transaction locks the database), MS Access (whose engine locks pages itself), DuckDB (one process writes at a
     * time; it has no row locks), MonetDB (optimistic transactions: a conflicting commit fails) and ClickHouse (no
     * transactions).
     *
     * @param string $dialect
     * @param string $sql a SELECT without FOR UPDATE
     * @return string
     */
    public static function forUpdate( $dialect, $sql )
    {
        if ( $dialect === 'couchbase' )
            $dialect = 'mysql';
        $sql = rtrim( (string)$sql, " \t\r\n;" );
        if ( in_array( $dialect, array( 'sqlite', 'msaccess', 'duckdb', 'monetdb', 'clickhouse' ), true ) )
            return $sql;
        if ( $dialect === 'sqlserver' )
            return expSQLServerDialect::forUpdate( $sql );
        if ( $dialect === 'firebird' )
            return expFirebirdDialect::forUpdate( $sql );
        if ( $dialect === 'db2' )
            return expDb2Dialect::forUpdate( $sql );
        if ( $dialect === 'informix' )
            return expInformixDialect::forUpdate( $sql );
        if ( $dialect === 'hana' )
            return expHANADialect::forUpdate( $sql );
        if ( $dialect === 'ase' )
            return expASEDialect::forUpdate( $sql );
        return $sql . ' FOR UPDATE';
    }

    /**
     * The values of a group joined into one text: GROUP_CONCAT, STRING_AGG, LISTAGG. SQLite before 3.44 cannot order
     * inside GROUP_CONCAT: there $orderBy is left out (order the rows in a subquery when it matters).
     *
     * @param string $dialect
     * @param callable $literal
     * @param string $expression
     * @param string $separator
     * @param string|null $orderBy
     * @return string
     */
    public static function groupConcat( $dialect, callable $literal, $expression, $separator = ',', $orderBy = null )
    {
        if ( $dialect === 'couchbase' )
            $dialect = 'mysql';
        if ( $dialect === 'sqlserver' )
            return expSQLServerDialect::groupConcat( $expression, $separator, $orderBy );
        if ( $dialect === 'msaccess' )
            throw new expDBUnsupportedException( 'msaccess', 'GROUP_CONCAT', 'join the rows in PHP' );
        if ( $dialect === 'cassandra' )
            throw new expDBUnsupportedException( 'cassandra', 'GROUP_CONCAT', 'CQL has no aggregate of text: join the rows in PHP' );
        if ( $dialect === 'firebird' )
            return expFirebirdDialect::groupConcat( $expression, $separator, $orderBy );
        if ( $dialect === 'db2' )
            return expDb2Dialect::groupConcat( $expression, $separator, $orderBy );
        if ( $dialect === 'informix' )
            return expInformixDialect::groupConcat( $expression, $separator, $orderBy );
        if ( $dialect === 'hana' )
            return expHANADialect::groupConcat( $expression, $separator, $orderBy );
        if ( $dialect === 'ase' )
            return expASEDialect::groupConcat( $expression, $separator, $orderBy );
        $order = ( $orderBy !== null && trim( (string)$orderBy ) !== '' ) ? trim( (string)$orderBy ) : null;
        $sep = $literal( (string)$separator );
        switch ( $dialect )
        {
            case 'mysql':
                return "GROUP_CONCAT($expression" . ( $order ? " ORDER BY $order" : '' ) . " SEPARATOR $sep)";
            case 'sqlite':
                return "GROUP_CONCAT($expression, $sep)";
            case 'oracle':
                return "LISTAGG($expression, $sep) WITHIN GROUP (ORDER BY " . ( $order ? $order : 'NULL' ) . ')';
            case 'monetdb':
                return "GROUP_CONCAT(CAST($expression AS TEXT), $sep" . ( $order ? " ORDER BY $order" : '' ) . ')';
            case 'clickhouse':
                // groupArray keeps no order of its own: with an order, the values are sorted by it first
                if ( !$order )
                    return "arrayStringConcat(groupArray(toString($expression)), $sep)";
                $descending = (bool)preg_match( '/\s+DESC\s*$/i', $order );
                $key = preg_replace( '/\s+(ASC|DESC)\s*$/i', '', $order );
                return "arrayStringConcat(arrayMap(x -> x.2, array" . ( $descending ? 'ReverseSort' : 'Sort' ) . "(x -> x.1, groupArray(tuple($key, toString($expression))))), $sep)";
            default:
                return "STRING_AGG(CAST($expression AS TEXT), $sep" . ( $order ? " ORDER BY $order" : '' ) . ')';
        }
    }

    /**
     * Seconds since 1970-01-01 UTC, of now or of a date/time expression (taken as UTC).
     *
     * @param string $dialect
     * @param string|null $expression
     * @return string
     */
    public static function unixTimestamp( $dialect, $expression = null )
    {
        if ( $dialect === 'couchbase' )
            $dialect = 'mysql';
        switch ( $dialect )
        {
            case 'sqlserver':
                return expSQLServerDialect::unixTimestamp( $expression );
            case 'msaccess':
                return expMSAccessDialect::unixTimestamp( $expression );
            case 'firebird':
                return expFirebirdDialect::unixTimestamp( $expression );
            case 'db2':
                return expDb2Dialect::unixTimestamp( $expression );
            case 'informix':
                return expInformixDialect::unixTimestamp( $expression );
            case 'cassandra':
                return $expression === null ? 'UNIX_TIMESTAMP()' : "UNIX_TIMESTAMP($expression)";
            case 'hana':
                return expHANADialect::unixTimestamp( $expression );
            case 'ase':
                return expASEDialect::unixTimestamp( $expression );
            case 'mysql':
                return $expression === null ? 'UNIX_TIMESTAMP()' : "UNIX_TIMESTAMP($expression)";
            case 'sqlite':
                return $expression === null ? "CAST(strftime('%s', 'now') AS INTEGER)" : "CAST(strftime('%s', $expression) AS INTEGER)";
            case 'oracle':
                $value = $expression === null ? 'SYS_EXTRACT_UTC(SYSTIMESTAMP)' : $expression;
                return "ROUND((CAST($value AS DATE) - DATE '1970-01-01') * 86400)";
            case 'monetdb':
                return $expression === null ? 'CAST(epoch(CURRENT_TIMESTAMP) AS BIGINT)' : "CAST(epoch($expression) AS BIGINT)";
            case 'clickhouse':
                return $expression === null ? 'toUnixTimestamp(now())' : "toUnixTimestamp($expression, 'UTC')";
            default:
                return $expression === null ? 'CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS BIGINT)' : "CAST(EXTRACT(EPOCH FROM $expression) AS BIGINT)";
        }
    }

    /**
     * A date/time (UTC) from seconds since 1970.
     *
     * @param string $dialect
     * @param string $expression
     * @return string
     */
    public static function fromUnixTimestamp( $dialect, $expression )
    {
        if ( $dialect === 'couchbase' )
            $dialect = 'mysql';
        switch ( $dialect )
        {
            case 'sqlserver':
                return expSQLServerDialect::fromUnixTimestamp( $expression );
            case 'msaccess':
                return expMSAccessDialect::fromUnixTimestamp( $expression );
            case 'firebird':
                return expFirebirdDialect::fromUnixTimestamp( $expression );
            case 'db2':
                return expDb2Dialect::fromUnixTimestamp( $expression );
            case 'informix':
                return expInformixDialect::fromUnixTimestamp( $expression );
            case 'cassandra':
                return "FROM_UNIXTIME($expression)";
            case 'hana':
                return expHANADialect::fromUnixTimestamp( $expression );
            case 'ase':
                return expASEDialect::fromUnixTimestamp( $expression );
            case 'mysql':
                return "FROM_UNIXTIME($expression)";
            case 'sqlite':
                return "datetime($expression, 'unixepoch')";
            case 'oracle':
                return "(TIMESTAMP '1970-01-01 00:00:00' + NUMTODSINTERVAL($expression, 'SECOND'))";
            case 'monetdb':
                return "CAST(epoch(CAST($expression AS BIGINT)) AS TIMESTAMP)";
            case 'clickhouse':
                return "toDateTime($expression, 'UTC')";
            default:
                return "(TIMESTAMP '1970-01-01 00:00:00' + ($expression) * INTERVAL '1 second')";
        }
    }

    /**
     * A PHP value as a literal: NULL, 1/0, digits, a float in plain notation, else the escaped text in quotes.
     *
     * @param callable $escape function( string ): string, the driver's escapeString()
     * @param mixed $value
     * @return string
     */
    public static function literal( callable $escape, $value )
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
            return ( $text === '' || $text === '-' || $text === '-0' ) ? '0' : $text;
        }
        return "'" . $escape( (string)$value ) . "'";
    }
}
