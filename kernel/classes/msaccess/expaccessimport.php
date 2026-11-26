<?php
/**
 * File containing the expAccessImport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Imports the tables of a Microsoft Access file into a database of the installation: the site's own, or a named
 * connection of the data table layer (expDataConnections). The tables are made through expDataTableSchema (so every
 * engine with a schema handler or the data table layer's statements works), then the rows are written in
 * transactions of a batch each, ids and all.
 *
 *   $reader = expAccessReader::create( 'var/import/shop.accdb' );          // MDBTools on Linux, ODBC on Windows
 *   $plan = expAccessImport::plan( $reader, $db, array( 'prefix' => 'shop_' ) );
 *   $report = expAccessImport::run( $reader, $db, array( 'prefix' => 'shop_', 'mode' => 'create' ) );
 *
 * Names: lower case ASCII letters, digits and _ (Größe -> grosse, "Order Details" -> order_details), with the prefix
 * on tables; a name that is an SQL keyword gets a trailing _. Types: AutoNumber -> auto_increment, Byte/Integer/Long
 * Integer/Yes-No -> int, Large Number -> bigint, Single/Double -> double, Currency/Decimal -> decimal, Date/Time ->
 * varchar(19) 'Y-m-d H:i:s' (Access keeps no time zone), Short Text -> varchar(n), Long Text -> longtext, OLE
 * object/binary -> longtext in base64, Replication ID -> char(38).
 *
 * Modes: create (new tables; a table that has rows already is left alone), append (add the rows), replace (delete the
 * table's rows first).
 *
 * @since 6.0.15
 * @package kernel
 */
class expAccessImport
{
    /** Words a column or table may not be called without quotes in one of the engines */
    const RESERVED = array( 'add', 'all', 'alter', 'and', 'any', 'as', 'asc', 'between', 'by', 'case', 'check', 'column', 'comment',
                            'constraint', 'create', 'cross', 'current', 'current_date', 'current_time', 'current_user', 'date', 'default',
                            'delete', 'desc', 'distinct', 'drop', 'else', 'end', 'exists', 'file', 'for', 'foreign', 'from', 'full', 'grant',
                            'group', 'having', 'identity', 'in', 'index', 'inner', 'insert', 'interval', 'into', 'is', 'join', 'key', 'left',
                            'level', 'like', 'limit', 'not', 'null', 'number', 'of', 'offset', 'on', 'option', 'or', 'order', 'outer',
                            'percent', 'position', 'primary', 'public', 'range', 'references', 'right', 'row', 'rows', 'select', 'session',
                            'set', 'size', 'table', 'then', 'time', 'timestamp', 'to', 'top', 'trigger', 'union', 'unique', 'update',
                            'user', 'uid', 'using', 'value', 'values', 'view', 'when', 'where', 'with' );

    /** The rows of one transaction */
    const BATCH = 500;

    /**
     * A name the target can use: ASCII lower case, digits and _, at most $max characters, not starting with a digit,
     * a keyword with a trailing _.
     *
     * @param string $name
     * @param string $prefix
     * @param int $max
     * @return string
     */
    public static function name( $name, $prefix = '', $max = 60 )
    {
        $name = (string)$name;
        $ascii = strtr( $name, array( 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ß' => 'ss' ) );
        if ( function_exists( 'transliterator_transliterate' ) )
            $ascii = (string)transliterator_transliterate( 'Any-Latin; Latin-ASCII', $ascii );
        elseif ( function_exists( 'iconv' ) )
            $ascii = (string)@iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $ascii );
        $ascii = strtolower( preg_replace( '/[^A-Za-z0-9]+/', '_', $ascii ) );
        $ascii = trim( $ascii, '_' );
        if ( $ascii === '' )
            $ascii = 'c' . substr( md5( $name ), 0, 8 );
        $full = strtolower( (string)$prefix ) . $ascii;
        if ( preg_match( '/^[0-9]/', $full ) )
            $full = 't_' . $full;
        if ( in_array( $full, self::RESERVED, true ) )
            $full .= '_';
        if ( strlen( $full ) > $max )
            $full = substr( $full, 0, $max - 9 ) . '_' . substr( md5( $name ), 0, 8 );
        return $full;
    }

    /**
     * The .dba field of a reader's field (pure).
     *
     * @param array $field type, length, scale, not_null, auto
     * @return array
     */
    public static function dbaField( array $field )
    {
        $notNull = !empty( $field['not_null'] ) ? '1' : '0';
        switch ( $field['type'] )
        {
            case 'counter':
                return array( 'type' => 'auto_increment', 'default' => false );
            case 'byte':
            case 'smallint':
            case 'integer':
            case 'boolean':
                return array( 'length' => 11, 'type' => 'int', 'not_null' => $notNull, 'default' => $notNull === '1' ? 0 : null );
            case 'bigint':
                return array( 'length' => 20, 'type' => 'bigint', 'not_null' => $notNull, 'default' => $notNull === '1' ? 0 : null );
            case 'single':
            case 'double':
                return array( 'type' => 'double', 'not_null' => $notNull, 'default' => $notNull === '1' ? 0 : null );
            case 'currency':
            case 'decimal':
                return array( 'length' => $field['length'] > 0 && $field['length'] <= 38 ? (int)$field['length'] : 19, 'type' => 'decimal',
                              'not_null' => $notNull, 'default' => $notNull === '1' ? 0 : null );
            case 'datetime':
                return array( 'length' => 19, 'type' => 'varchar', 'not_null' => '0', 'default' => null );
            case 'guid':
                return array( 'length' => 38, 'type' => 'char', 'not_null' => '0', 'default' => null );
            case 'text':
                $length = (int)$field['length'];
                if ( $length > 0 && $length <= 255 )
                    return array( 'length' => $length, 'type' => 'varchar', 'not_null' => '0', 'default' => null );
                return array( 'type' => 'longtext', 'not_null' => '0', 'default' => null );
        }
        // memo, binary (base64) and whatever else
        return array( 'type' => 'longtext', 'not_null' => '0', 'default' => null );
    }

    /**
     * The target schema of the file's tables, and the names it maps (pure apart from the reader).
     *
     * @param expAccessReader $reader
     * @param string[] $tables the Access tables (all of them when empty)
     * @param string $prefix
     * @return array schema (table => dba table), map (Access table => array( table, columns: Access column => column, types )), notes
     */
    public static function schema( expAccessReader $reader, array $tables = array(), $prefix = '' )
    {
        $available = $reader->tables();
        $tables = $tables ? $tables : $available;
        $result = array( 'schema' => array(), 'map' => array(), 'notes' => array() );
        $used = array();
        foreach ( $tables as $table )
        {
            if ( !in_array( $table, $available, true ) )
            {
                $result['notes'][] = "The file has no table '$table'";
                continue;
            }
            $description = $reader->table( $table );
            $target = self::name( $table, $prefix );
            for ( $n = 2; isset( $used[$target] ); $n++ )
                $target = self::name( $table, $prefix ) . '_' . $n;
            $used[$target] = true;
            $columns = array();
            $types = array();
            $fields = array();
            $names = array();
            foreach ( $description['fields'] as $column => $field )
            {
                $name = self::name( $column );
                for ( $n = 2; isset( $names[$name] ); $n++ )
                    $name = self::name( $column ) . '_' . $n;
                $names[$name] = true;
                $columns[$column] = $name;
                $types[$column] = $field['type'];
                $fields[$name] = self::dbaField( $field );
                if ( $field['type'] === 'binary' )
                    $result['notes'][] = "$table.$column is binary: written as base64 text into $target.$name";
            }
            if ( !$fields )
            {
                $result['notes'][] = "The table '$table' has no columns the reader can see";
                continue;
            }
            $indexes = array();
            $primary = array_values( array_filter( array_map( function ( $c ) use ( $columns ) { return isset( $columns[$c] ) ? $columns[$c] : null; }, $description['primary'] ) ) );
            $autoColumns = array_keys( array_filter( $fields, function ( $f ) { return $f['type'] === 'auto_increment'; } ) );
            // an AutoNumber is the key of its table on every engine (MySQL requires it)
            if ( $autoColumns && ( !$primary || !in_array( $autoColumns[0], $primary, true ) ) )
            {
                if ( $primary )
                    $indexes[expDataTableSchema::indexName( $target, 'access_pk' )] = array( 'type' => 'unique', 'fields' => $primary );
                $primary = array( $autoColumns[0] );
            }
            foreach ( array_slice( $autoColumns, 1 ) as $extra )
            {
                $fields[$extra] = array( 'length' => 11, 'type' => 'int', 'not_null' => '0', 'default' => null );
                $result['notes'][] = "$table has a second AutoNumber column; $target.$extra is a plain integer";
            }
            if ( $primary )
            {
                $indexes['PRIMARY'] = array( 'type' => 'primary', 'fields' => $primary );
                // a key column is NOT NULL everywhere (SQL Server refuses a key on a nullable column)
                foreach ( $primary as $key )
                    if ( $fields[$key]['type'] !== 'auto_increment' )
                    {
                        $fields[$key]['not_null'] = '1';
                        // MySQL refuses DEFAULT NULL on a NOT NULL column
                        if ( !isset( $fields[$key]['default'] ) )
                            $fields[$key]['default'] = in_array( $fields[$key]['type'], array( 'varchar', 'char' ), true ) ? '' : 0;
                    }
            }
            foreach ( $description['indexes'] as $name => $index )
            {
                $indexFields = array_values( array_filter( array_map( function ( $c ) use ( $columns ) { return isset( $columns[$c] ) ? $columns[$c] : null; }, $index['fields'] ) ) );
                if ( !$indexFields || $indexFields === $primary )
                    continue;
                // long text cannot be a key everywhere
                $long = false;
                foreach ( $indexFields as $f )
                    $long = $long || $fields[$f]['type'] === 'longtext';
                if ( $long )
                {
                    $result['notes'][] = "The index $table.$name is on long text and is left out";
                    continue;
                }
                $indexes[expDataTableSchema::indexName( $target, implode( '_', $indexFields ) )] = array( 'type' => $index['unique'] ? 'unique' : 'non-unique', 'fields' => $indexFields );
            }
            $result['schema'][$target] = array( 'name' => $target, 'fields' => $fields, 'indexes' => $indexes );
            $result['map'][$table] = array( 'table' => $target, 'columns' => $columns, 'types' => $types );
        }
        return $result;
    }

    /**
     * What an import would do: the schema, the tables' plan on the target (create, upgrade, none), and the rows each
     * Access table has (nothing is written).
     *
     * @param expAccessReader $reader
     * @param eZDBInterface $db
     * @param array $options tables, prefix
     * @return array schema, map, notes, plan (expDataTableSchema::plan())
     */
    public static function plan( expAccessReader $reader, $db, array $options = array() )
    {
        $schema = self::schema( $reader, isset( $options['tables'] ) ? (array)$options['tables'] : array(), isset( $options['prefix'] ) ? (string)$options['prefix'] : '' );
        $schema['plan'] = $schema['schema'] ? expDataTableSchema::plan( $schema['schema'], $db ) : array( 'engine' => expKernelTableSchema::type( $db ), 'tables' => array(), 'error' => '' );
        return $schema;
    }

    /**
     * Imports: makes the tables, writes the rows.
     *
     * @param expAccessReader $reader
     * @param eZDBInterface $db
     * @param array $options tables, prefix, mode (create, append, replace), batch
     * @return array ok, engine, reader, tables => Access table => array( table, action, read, written, errors ), notes
     */
    public static function run( expAccessReader $reader, $db, array $options = array() )
    {
        $mode = isset( $options['mode'] ) ? (string)$options['mode'] : 'create';
        if ( !in_array( $mode, array( 'create', 'append', 'replace' ), true ) )
            throw new InvalidArgumentException( "The mode must be create, append or replace, not $mode" );
        $batch = isset( $options['batch'] ) ? max( 1, (int)$options['batch'] ) : self::BATCH;
        $plan = self::plan( $reader, $db, $options );
        $engine = $plan['plan']['engine'];
        $report = array( 'ok' => true, 'engine' => $engine, 'reader' => $reader->name(), 'mode' => $mode, 'tables' => array(), 'notes' => $plan['notes'] );
        $applied = $plan['schema'] ? expDataTableSchema::apply( $plan['schema'], $db ) : array( 'tables' => array() );
        foreach ( $plan['map'] as $access => $map )
        {
            $target = $map['table'];
            $outcome = array( 'table' => $target, 'action' => isset( $plan['plan']['tables'][$target]['action'] ) ? $plan['plan']['tables'][$target]['action'] : 'none',
                              'read' => 0, 'written' => 0, 'errors' => array() );
            $schemaResult = isset( $applied['tables'][$target] ) ? $applied['tables'][$target] : array( 'ok' => true, 'errors' => array() );
            if ( !$schemaResult['ok'] )
            {
                $outcome['errors'] = array_merge( array( 'the table could not be made' ), (array)$schemaResult['errors'] );
                $report['tables'][$access] = $outcome;
                $report['ok'] = false;
                continue;
            }
            try
            {
                $existing = $outcome['action'] === 'create' ? 0 : self::countRows( $db, $target );
                if ( $existing > 0 && $mode === 'create' )
                {
                    $outcome['errors'][] = "$target has $existing rows already: use the mode append or replace";
                    $report['tables'][$access] = $outcome;
                    $report['ok'] = false;
                    continue;
                }
                if ( $existing > 0 && $mode === 'replace' )
                    $db->query( 'DELETE FROM ' . $db->sqlQuoteIdentifier( $target ) );
                $counts = self::copyRows( $reader, $db, $engine, $access, $map, $plan['schema'][$target], $batch );
                $outcome['read'] = $counts['read'];
                $outcome['written'] = $counts['written'];
                $outcome['errors'] = $counts['errors'];
            }
            catch ( Throwable $e )
            {
                $outcome['errors'][] = $e->getMessage();
            }
            $report['ok'] = $report['ok'] && !$outcome['errors'];
            $report['tables'][$access] = $outcome;
        }
        if ( method_exists( $db, 'correctSequenceValues' ) && $engine === 'postgresql' )
            $db->correctSequenceValues();
        return $report;
    }

    /**
     * @param eZDBInterface $db
     * @param string $table
     * @return int
     */
    protected static function countRows( $db, $table )
    {
        $rows = $db->arrayQuery( 'SELECT COUNT(*) AS n FROM ' . $db->sqlQuoteIdentifier( $table ) );
        return isset( $rows[0]['n'] ) ? (int)$rows[0]['n'] : (int)( is_array( $rows ) && $rows ? reset( $rows[0] ) : 0 );
    }

    /**
     * Copies the rows of one table, a transaction per batch. When a statement of a batch fails, the batch is rolled
     * back and its rows are written one by one, so a bad row is reported and the others still arrive.
     *
     * @return array read, written, errors
     */
    protected static function copyRows( expAccessReader $reader, $db, $engine, $access, array $map, array $definition, $batch )
    {
        $target = $map['table'];
        $quotedTable = $db->sqlQuoteIdentifier( $target );
        $columns = array();
        foreach ( $map['columns'] as $column => $name )
            $columns[] = $db->sqlQuoteIdentifier( $name );
        $prefix = "INSERT INTO $quotedTable (" . implode( ', ', $columns ) . ') VALUES (';
        $auto = false;
        foreach ( $definition['fields'] as $field )
            $auto = $auto || $field['type'] === 'auto_increment';
        // explicit ids into an IDENTITY column (expSQLServerDB does this itself)
        $identityInsert = $auto && $engine === 'sqlserver' && !( $db instanceof expSQLServerDB );
        $counts = array( 'read' => 0, 'written' => 0, 'errors' => array() );
        $before = expAuditIndexSchema::errorHandling( $db );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $buffer = array();
        $flush = function () use ( $db, &$buffer, &$counts ) {
            if ( !$buffer )
                return;
            try
            {
                $db->begin();
                foreach ( $buffer as $sql )
                    $db->query( $sql[1] );
                $db->commit();
                $counts['written'] += count( $buffer );
            }
            catch ( Throwable $e )
            {
                while ( $db->transactionCounter() > 0 )
                    $db->rollback();
                foreach ( $buffer as $sql )
                {
                    try
                    {
                        $db->query( $sql[1] );
                        $counts['written']++;
                    }
                    catch ( Throwable $rowError )
                    {
                        if ( count( $counts['errors'] ) < 20 )
                            $counts['errors'][] = 'row ' . $sql[0] . ': ' . mb_substr( $rowError->getMessage(), 0, 300 );
                        elseif ( count( $counts['errors'] ) === 20 )
                            $counts['errors'][] = 'more rows failed';
                    }
                }
            }
            $buffer = array();
        };
        try
        {
            if ( $identityInsert )
                $db->query( "SET IDENTITY_INSERT $quotedTable ON" );
            $reader->rows( $access, function ( array $row ) use ( $db, $map, $definition, $prefix, $batch, $flush, &$buffer, &$counts ) {
                $counts['read']++;
                $values = array();
                foreach ( $map['columns'] as $column => $name )
                    $values[] = expAccessImport::literal( $db, array_key_exists( $column, $row ) ? $row[$column] : null, $definition['fields'][$name] );
                $buffer[] = array( $counts['read'], $prefix . implode( ', ', $values ) . ')' );
                if ( count( $buffer ) >= $batch )
                    $flush();
            } );
            $flush();
        }
        catch ( Throwable $e )
        {
            $flush();
            $counts['errors'][] = $e->getMessage();
        }
        if ( $identityInsert )
        {
            try
            {
                $db->query( "SET IDENTITY_INSERT $quotedTable OFF" );
            }
            catch ( Throwable $e )
            {
                $counts['errors'][] = $e->getMessage();
            }
        }
        $db->setErrorHandling( $before );
        return $counts;
    }

    /**
     * A value as the target's literal for its .dba field.
     *
     * @param eZDBInterface $db
     * @param mixed $value
     * @param array $field
     * @return string
     */
    public static function literal( $db, $value, array $field )
    {
        if ( $value === null )
            return 'NULL';
        if ( $value instanceof expAccessBinary )
            $value = (string)$value;
        if ( in_array( $field['type'], array( 'auto_increment', 'int', 'bigint', 'double', 'decimal' ), true ) )
        {
            if ( is_bool( $value ) )
                return $value ? '1' : '0';
            if ( is_int( $value ) || is_float( $value ) )
                return $db->sqlLiteral( $value );
            $text = trim( (string)$value );
            if ( preg_match( '/^-?(?:\d+(?:\.\d*)?|\.\d+)(?:[eE][-+]?\d+)?$/', $text ) )
                return $text;
            return $text === '' ? 'NULL' : $db->sqlLiteral( $text );
        }
        return $db->sqlLiteral( (string)$value );
    }
}
?>
