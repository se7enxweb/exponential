<?php
/**
 * File containing the expDataTableSchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The schema of data tables on any connection (doc/eng-us/features/6.0/data-tables.md, "Schema"): a .dba schema, or
 * one made from a persistent class's definition(), created or brought up to date on a connection, with the SQL shown
 * before anything runs.
 *
 *   $schema = expDataTableSchema::fromDefinition( myArchiveRow::definition() );     // or expKernelTableSchema::read( $dba )
 *   $plan = expDataTableSchema::plan( $schema, expDataConnections::db( 'archive' ) );
 *   // $plan['tables'][<table>]: action (create, upgrade, none), statements, held (what is not done unless asked:
 *   // dropping columns and indexes, changing a column), notes
 *   $result = expDataTableSchema::apply( $schema, $db, array( 'drop' => false, 'alter' => false ) );
 *
 * Engines: MySQL/MariaDB, PostgreSQL and SQLite through the kernel's schema handlers (CREATE from them, the upgrade
 * from eZDbSchemaChecker and the handler's upgrade SQL); Oracle through ezoracle's handler when it is active, else
 * through the statements here (a sequence and a trigger for the counted key, named as expPDODB reads them); SQL Server
 * through the statements here (IDENTITY); MongoDB through its schema handler (collections and indexes). An upgrade
 * adds columns and indexes; it drops or changes one only when asked ('drop', 'alter').
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataTableSchema
{
    /**
     * A .dba table from a persistent class's definition (pure).
     *
     * @param array $definition
     * @param array $lengths field => varchar length (255 when not given)
     * @return array table name => dba table
     */
    public static function fromDefinition( array $definition, array $lengths = array() )
    {
        $table = isset( $definition['name'] ) ? (string)$definition['name'] : '';
        $fields = array();
        $increment = isset( $definition['increment_key'] ) ? (string)$definition['increment_key'] : '';
        foreach ( isset( $definition['fields'] ) && is_array( $definition['fields'] ) ? $definition['fields'] : array() as $name => $field )
        {
            $field = is_array( $field ) ? $field : array();
            $datatype = isset( $field['datatype'] ) ? strtolower( (string)$field['datatype'] ) : 'string';
            $default = isset( $field['default'] ) && is_scalar( $field['default'] ) ? $field['default'] : null;
            if ( $name === $increment )
            {
                $fields[$name] = array( 'type' => 'auto_increment', 'default' => false );
                continue;
            }
            switch ( $datatype )
            {
                case 'integer':
                case 'int':
                    $fields[$name] = array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => $default === null ? 0 : (int)$default );
                    break;
                case 'float':
                case 'double':
                case 'decimal':
                    $fields[$name] = array( 'type' => 'float', 'not_null' => '1', 'default' => $default === null ? 0 : (float)$default );
                    break;
                case 'text':
                    $fields[$name] = array( 'type' => 'longtext', 'default' => false );
                    break;
                default:
                    $fields[$name] = array( 'length' => isset( $lengths[$name] ) ? max( 1, min( 4000, (int)$lengths[$name] ) ) : 255,
                                            'type' => 'varchar', 'not_null' => '1', 'default' => $default === null ? '' : (string)$default );
            }
        }
        ksort( $fields );
        $indexes = array();
        $keys = isset( $definition['keys'] ) ? array_values( (array)$definition['keys'] ) : array();
        if ( $keys )
            $indexes['PRIMARY'] = array( 'type' => 'primary', 'fields' => $keys );
        // an index on each relation column: the content view asks "which rows point at this object"
        foreach ( isset( $definition['relations'] ) && is_array( $definition['relations'] ) ? array_keys( $definition['relations'] ) : array() as $column )
        {
            if ( isset( $fields[$column] ) && !in_array( $column, $keys, true ) )
                $indexes[self::indexName( $table, $column )] = array( 'type' => 'non-unique', 'fields' => array( $column ) );
        }
        foreach ( isset( $definition['unique'] ) && is_array( $definition['unique'] ) ? $definition['unique'] : array() as $name => $columns )
            $indexes[is_string( $name ) ? $name : self::indexName( $table, implode( '_', (array)$columns ) )] = array( 'type' => 'unique', 'fields' => array_values( (array)$columns ) );
        return array( $table => array( 'name' => $table, 'fields' => $fields, 'indexes' => $indexes ) );
    }

    /**
     * An index name of at most 30 characters (Oracle's limit), from the table and the columns (pure).
     *
     * @param string $table
     * @param string $columns
     * @return string
     */
    public static function indexName( $table, $columns )
    {
        $name = strtolower( $table . '_' . $columns );
        return strlen( $name ) <= 30 ? $name : substr( $name, 0, 21 ) . '_' . substr( md5( $name ), 0, 8 );
    }

    /**
     * What applying a schema to a connection would do, table by table, with the SQL (nothing runs).
     *
     * @param array $schema table => dba table
     * @param eZDBInterface $db
     * @param array $options drop (drop columns and indexes the schema does not have), alter (change columns)
     * @return array engine, tables => table => array( action, statements, held, notes ), error
     */
    public static function plan( array $schema, $db, array $options = array() )
    {
        unset( $schema['_info'] );
        $engine = expKernelTableSchema::type( $db );
        $plan = array( 'engine' => $engine, 'tables' => array(), 'error' => '' );
        $current = null;
        foreach ( $schema as $table => $definition )
        {
            if ( !is_array( $definition ) || !preg_match( '/^[A-Za-z_][A-Za-z0-9_]{0,127}$/', (string)$table ) )
            {
                $plan['tables'][$table] = array( 'action' => 'none', 'statements' => array(), 'held' => array(), 'notes' => array( 'not a table name' ) );
                continue;
            }
            $exists = expKernelTableSchema::exists( $table, $db, true );
            if ( $exists === null )
            {
                $plan['tables'][$table] = array( 'action' => 'unknown', 'statements' => array(), 'held' => array(),
                                                 'notes' => array( 'the database does not list its tables to this user' ) );
                continue;
            }
            if ( $exists === false )
            {
                $plan['tables'][$table] = array( 'action' => 'create', 'statements' => self::createStatements( $engine, $table, $definition, $db ),
                                                 'held' => array(), 'notes' => array() );
                continue;
            }
            $plan['tables'][$table] = self::upgradePlan( $engine, $table, $definition, $db, $options );
        }
        return $plan;
    }

    /**
     * Creates and upgrades the tables of a schema on a connection.
     *
     * @param array $schema
     * @param eZDBInterface $db
     * @param array $options drop, alter (see plan())
     * @return array ok, tables => table => array( action, ok, done (statements run), errors ), plan
     */
    public static function apply( array $schema, $db, array $options = array() )
    {
        $plan = self::plan( $schema, $db, $options );
        $result = array( 'ok' => true, 'tables' => array(), 'plan' => $plan );
        foreach ( $plan['tables'] as $table => $step )
        {
            $outcome = array( 'action' => $step['action'], 'ok' => true, 'done' => 0, 'errors' => array() );
            if ( $step['action'] === 'unknown' )
            {
                $outcome['ok'] = false;
                $outcome['errors'] = $step['notes'];
            }
            elseif ( $step['action'] === 'create' && in_array( $plan['engine'], array( 'mysql', 'postgresql', 'sqlite', 'mongodb' ), true )
                     || ( $step['action'] === 'create' && $plan['engine'] === 'oracle' && self::hasHandler( 'oracle' ) && !$db instanceof expPDODB ) )
            {
                $messages = array();
                $outcome['ok'] = expKernelTableSchema::install( array( $table ), $db, array( $table => $schema[$table] ), $messages );
                if ( !$outcome['ok'] )
                    $outcome['errors'] = $messages;
                $outcome['done'] = $outcome['ok'] ? 1 : 0;
            }
            elseif ( $step['statements'] )
            {
                $outcome = self::run( $db, $step['statements'] ) + $outcome;
            }
            expKernelTableSchema::reset();
            $result['tables'][$table] = $outcome;
            $result['ok'] = $result['ok'] && $outcome['ok'];
        }
        return $result;
    }

    /**
     * The CREATE statements of one table for an engine (pure for oracle, sqlserver; the kernel's handlers otherwise).
     *
     * @param string $engine
     * @param string $table
     * @param array $definition dba table
     * @param eZDBInterface|null $db
     * @return string[]
     */
    public static function createStatements( $engine, $table, array $definition, $db = null )
    {
        switch ( $engine )
        {
            case 'sqlserver':
                return self::sqlServerCreate( $table, $definition );
            case 'oracle':
                if ( self::hasHandler( 'oracle' ) && !$db instanceof expPDODB )
                    return self::split( expKernelTableSchema::sql( array( $table => $definition ), 'oracle' ) );
                return self::oracleCreate( $table, $definition );
            case 'mongodb':
                return array( '// MongoDB: the collection ' . $table . ' is made on its first write; indexes: '
                              . json_encode( isset( $definition['indexes'] ) ? $definition['indexes'] : array(), JSON_UNESCAPED_SLASHES ) );
        }
        return self::split( expKernelTableSchema::sql( array( $table => $definition ), $engine ) );
    }

    /**
     * The install SQL of a schema for every engine, for an extension's sql/<engine>/schema.sql (pure).
     *
     * @param array $schema
     * @return array engine => SQL text (mysql, postgresql, sqlite, oracle, sqlserver)
     */
    public static function installSql( array $schema )
    {
        unset( $schema['_info'] );
        $sql = array();
        foreach ( array( 'mysql', 'postgresql', 'sqlite', 'oracle', 'sqlserver' ) as $engine )
        {
            $statements = array();
            foreach ( $schema as $table => $definition )
            {
                $list = $engine === 'oracle' ? self::oracleCreate( $table, $definition ) : self::createStatements( $engine, $table, $definition );
                foreach ( $list as $statement )
                    $statements[] = rtrim( trim( $statement ), ';' ) . ( $engine === 'oracle' && preg_match( '/^CREATE OR REPLACE TRIGGER/i', trim( $statement ) ) ? ";\n/" : ';' );
            }
            $sql[$engine] = implode( "\n\n", $statements ) . "\n";
        }
        return $sql;
    }

    /**
     * Columns and indexes for SQL Server (pure).
     *
     * @param string $table
     * @param array $definition
     * @return string[]
     */
    public static function sqlServerCreate( $table, array $definition )
    {
        $columns = array();
        $primary = isset( $definition['indexes']['PRIMARY']['fields'] ) ? (array)$definition['indexes']['PRIMARY']['fields'] : array();
        foreach ( isset( $definition['fields'] ) ? $definition['fields'] : array() as $name => $field )
            $columns[] = '  ' . self::quoteSqlServer( $name ) . ' ' . self::sqlServerType( $field );
        if ( $primary )
            $columns[] = '  CONSTRAINT ' . self::quoteSqlServer( self::indexName( $table, 'pk' ) ) . ' PRIMARY KEY ( ' . implode( ', ', array_map( array( __CLASS__, 'quoteSqlServer' ), $primary ) ) . ' )';
        $statements = array( 'CREATE TABLE ' . self::quoteSqlServer( $table ) . " (\n" . implode( ",\n", $columns ) . "\n)" );
        foreach ( isset( $definition['indexes'] ) ? $definition['indexes'] : array() as $name => $index )
        {
            if ( $name === 'PRIMARY' || !isset( $index['fields'] ) )
                continue;
            $statements[] = 'CREATE ' . ( isset( $index['type'] ) && $index['type'] === 'unique' ? 'UNIQUE ' : '' ) . 'INDEX ' . self::quoteSqlServer( $name )
                          . ' ON ' . self::quoteSqlServer( $table ) . ' ( ' . implode( ', ', array_map( array( __CLASS__, 'quoteSqlServer' ), (array)$index['fields'] ) ) . ' )';
        }
        return $statements;
    }

    /**
     * Columns, indexes, and a sequence with a trigger for the counted key, for Oracle (pure). The sequence is named
     * as expPDODB::oracleSequence() reads it.
     *
     * @param string $table
     * @param array $definition
     * @return string[]
     */
    public static function oracleCreate( $table, array $definition )
    {
        $columns = array();
        $auto = '';
        $primary = isset( $definition['indexes']['PRIMARY']['fields'] ) ? (array)$definition['indexes']['PRIMARY']['fields'] : array();
        foreach ( isset( $definition['fields'] ) ? $definition['fields'] : array() as $name => $field )
        {
            if ( isset( $field['type'] ) && $field['type'] === 'auto_increment' )
                $auto = $name;
            $columns[] = '  ' . $name . ' ' . self::oracleType( $field );
        }
        if ( $primary )
            $columns[] = '  PRIMARY KEY ( ' . implode( ', ', $primary ) . ' )';
        $statements = array( 'CREATE TABLE ' . $table . " (\n" . implode( ",\n", $columns ) . "\n)" );
        foreach ( isset( $definition['indexes'] ) ? $definition['indexes'] : array() as $name => $index )
        {
            if ( $name === 'PRIMARY' || !isset( $index['fields'] ) )
                continue;
            $statements[] = 'CREATE ' . ( isset( $index['type'] ) && $index['type'] === 'unique' ? 'UNIQUE ' : '' ) . 'INDEX ' . substr( $name, 0, 30 )
                          . ' ON ' . $table . ' ( ' . implode( ', ', (array)$index['fields'] ) . ' )';
        }
        if ( $auto !== '' )
        {
            $sequence = class_exists( 'expPDODB' ) ? expPDODB::oracleSequence( $table ) : substr( 'se_' . $table, 0, 30 );
            $trigger = substr( $sequence . '_tr', 0, 30 );
            $statements[] = 'CREATE SEQUENCE ' . $sequence . ' START WITH 1 INCREMENT BY 1 NOCACHE';
            $statements[] = 'CREATE OR REPLACE TRIGGER ' . $trigger . ' BEFORE INSERT ON ' . $table . " FOR EACH ROW WHEN ( new.$auto IS NULL )\nBEGIN\n  SELECT "
                          . $sequence . '.NEXTVAL INTO :new.' . $auto . " FROM DUAL;\nEND;";
        }
        return $statements;
    }

    /**
     * @param array $field dba field
     * @return string
     */
    public static function sqlServerType( array $field )
    {
        $type = isset( $field['type'] ) ? strtolower( (string)$field['type'] ) : 'varchar';
        $notNull = !empty( $field['not_null'] ) ? ' NOT NULL' : ' NULL';
        $default = self::defaultClause( $field, 'sqlserver' );
        switch ( $type )
        {
            case 'auto_increment':
                return 'INT IDENTITY(1,1) NOT NULL';
            case 'int':
            case 'integer':
                return 'INT' . $default . $notNull;
            case 'bigint':
                return 'BIGINT' . $default . $notNull;
            case 'float':
            case 'double':
                return 'FLOAT' . $default . $notNull;
            case 'decimal':
                return 'DECIMAL(' . ( isset( $field['length'] ) ? (int)$field['length'] : 10 ) . ',2)' . $default . $notNull;
            case 'longtext':
            case 'text':
            case 'mediumtext':
                return 'NVARCHAR(MAX) NULL';
            case 'char':
                return 'NCHAR(' . ( isset( $field['length'] ) ? (int)$field['length'] : 1 ) . ')' . $default . $notNull;
        }
        return 'NVARCHAR(' . ( isset( $field['length'] ) ? max( 1, min( 4000, (int)$field['length'] ) ) : 255 ) . ')' . $default . $notNull;
    }

    /**
     * @param array $field dba field
     * @return string
     */
    public static function oracleType( array $field )
    {
        $type = isset( $field['type'] ) ? strtolower( (string)$field['type'] ) : 'varchar';
        // Oracle stores '' as NULL: a text column is never NOT NULL
        $default = self::defaultClause( $field, 'oracle' );
        switch ( $type )
        {
            case 'auto_increment':
                return 'INTEGER NOT NULL';
            case 'int':
            case 'integer':
                return 'INTEGER' . $default . ( !empty( $field['not_null'] ) ? ' NOT NULL' : '' );
            case 'bigint':
                return 'NUMBER(19)' . $default . ( !empty( $field['not_null'] ) ? ' NOT NULL' : '' );
            case 'float':
            case 'double':
                return 'BINARY_DOUBLE' . $default . ( !empty( $field['not_null'] ) ? ' NOT NULL' : '' );
            case 'decimal':
                return 'NUMBER(' . ( isset( $field['length'] ) ? (int)$field['length'] : 10 ) . ',2)' . $default;
            case 'longtext':
            case 'text':
            case 'mediumtext':
                return 'CLOB';
            case 'char':
                return 'CHAR(' . ( isset( $field['length'] ) ? (int)$field['length'] : 1 ) . ')' . $default;
        }
        return 'VARCHAR2(' . ( isset( $field['length'] ) ? max( 1, min( 4000, (int)$field['length'] ) ) : 255 ) . ' CHAR)' . $default;
    }

    /**
     * @param string $name
     * @return string
     */
    public static function quoteSqlServer( $name )
    {
        return '[' . str_replace( ']', ']]', (string)$name ) . ']';
    }

    /**
     * Splits SQL text into statements (on a semicolon at a line end; comments dropped).
     *
     * @param string $sql
     * @return string[]
     */
    public static function split( $sql )
    {
        $statements = array();
        foreach ( preg_split( '/;\s*(\n|$)/', (string)$sql ) as $part )
        {
            $lines = array();
            foreach ( explode( "\n", $part ) as $line )
                if ( !preg_match( '/^\s*(--|#)/', $line ) )
                    $lines[] = $line;
            $statement = trim( implode( "\n", $lines ) );
            if ( $statement !== '' )
                $statements[] = $statement;
        }
        return $statements;
    }

    /**
     * Whether the kernel has a schema handler for an engine here.
     *
     * @param string $engine
     * @return bool
     */
    public static function hasHandler( $engine )
    {
        try
        {
            $paths = (array)eZINI::instance( 'dbschema.ini' )->variable( 'SchemaSettings', 'SchemaPaths' );
            return isset( $paths[$engine === 'mongodb' ? 'mongo' : $engine] );
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * The upgrade of an existing table.
     *
     * @param string $engine
     * @param string $table
     * @param array $wanted
     * @param eZDBInterface $db
     * @param array $options
     * @return array action, statements, held, notes
     */
    protected static function upgradePlan( $engine, $table, array $wanted, $db, array $options )
    {
        $step = array( 'action' => 'none', 'statements' => array(), 'held' => array(), 'notes' => array() );
        if ( $engine === 'mongodb' )
        {
            $step['notes'][] = 'MongoDB: the fields need no change; the indexes are made again when missing.';
            if ( isset( $wanted['indexes'] ) && count( $wanted['indexes'] ) > 1 )
            {
                $step['action'] = 'upgrade';
                $step['statements'][] = '// indexes ' . json_encode( $wanted['indexes'], JSON_UNESCAPED_SLASHES );
            }
            return $step;
        }
        if ( in_array( $engine, array( 'mysql', 'postgresql', 'sqlite' ), true ) || ( $engine === 'oracle' && self::hasHandler( 'oracle' ) && !$db instanceof expPDODB ) )
        {
            $handler = expKernelTableSchema::handler( $db, array() );
            if ( !$handler )
            {
                $step['action'] = 'unknown';
                $step['notes'][] = 'no schema handler';
                return $step;
            }
            try
            {
                $all = $handler->schema( array( 'format' => 'generic' ) );
            }
            catch ( Throwable $e )
            {
                $all = array();
            }
            $current = isset( $all[$table] ) ? array( $table => $all[$table] ) : array();
            $diff = eZDbSchemaChecker::diff( $current, array( $table => $wanted ) );
            $diff = is_array( $diff ) ? $diff : array();
            $changes = isset( $diff['table_changes'][$table] ) ? $diff['table_changes'][$table] : array();
            foreach ( array( 'removed_fields' => 'drop', 'removed_indexes' => 'drop', 'changed_fields' => 'alter', 'changed_indexes' => 'alter' ) as $kind => $option )
            {
                if ( empty( $changes[$kind] ) )
                    continue;
                foreach ( array_keys( $changes[$kind] ) as $name )
                    $step['held'][] = str_replace( '_', ' ', $kind ) . ': ' . $name . ( empty( $options[$option] ) ? '' : ' (done)' );
                if ( empty( $options[$option] ) )
                    unset( $changes[$kind] );
            }
            if ( $changes )
            {
                $sql = $handler->generateUpgradeFile( array( 'table_changes' => array( $table => $changes ) ), array( 'schema' => true, 'data' => false ) );
                $step['statements'] = self::split( $sql );
            }
        }
        else
        {
            // Oracle through PDO, SQL Server: add the missing columns and indexes
            $columns = expDataTableDescriber::columns( $db, $table );
            foreach ( isset( $wanted['fields'] ) ? $wanted['fields'] : array() as $name => $field )
            {
                if ( isset( $columns[strtolower( $name )] ) )
                    continue;
                $step['statements'][] = $engine === 'sqlserver'
                    ? 'ALTER TABLE ' . self::quoteSqlServer( $table ) . ' ADD ' . self::quoteSqlServer( $name ) . ' ' . self::sqlServerType( $field )
                    : 'ALTER TABLE ' . $table . ' ADD ( ' . $name . ' ' . self::oracleType( $field ) . ' )';
            }
            $known = array_map( 'strtolower', array_keys( $wanted['fields'] ) );
            foreach ( array_keys( $columns ) as $name )
                if ( !in_array( strtolower( $name ), $known, true ) )
                    $step['held'][] = 'removed fields: ' . $name;
            $step['notes'][] = 'Indexes are compared on MySQL, PostgreSQL and SQLite only; on this engine new indexes are listed in the install SQL.';
        }
        if ( $step['statements'] )
            $step['action'] = 'upgrade';
        return $step;
    }

    /**
     * Runs statements, stopping at the first that fails.
     *
     * @param eZDBInterface $db
     * @param string[] $statements
     * @return array ok, done, errors
     */
    protected static function run( $db, array $statements )
    {
        $before = expAuditIndexSchema::errorHandling( $db );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $done = 0;
        $errors = array();
        foreach ( $statements as $statement )
        {
            if ( strpos( ltrim( $statement ), '//' ) === 0 )
            {
                $done++;
                continue;
            }
            try
            {
                if ( $db->query( $statement ) === false )
                {
                    $errors[] = 'failed: ' . mb_substr( $statement, 0, 120 ) . ' ' . $db->errorMessage();
                    break;
                }
                $done++;
            }
            catch ( Throwable $e )
            {
                $errors[] = mb_substr( $e->getMessage(), 0, 300 );
                break;
            }
        }
        $db->setErrorHandling( $before );
        return array( 'ok' => !$errors, 'done' => $done, 'errors' => $errors );
    }

    /**
     * @param array $field
     * @param string $engine
     * @return string
     */
    protected static function defaultClause( array $field, $engine )
    {
        if ( !array_key_exists( 'default', $field ) || $field['default'] === false || $field['default'] === null )
            return '';
        $value = $field['default'];
        if ( is_int( $value ) || is_float( $value ) || ( is_string( $value ) && is_numeric( $value ) && in_array( strtolower( (string)$field['type'] ), array( 'int', 'integer', 'bigint', 'float', 'double', 'decimal' ), true ) ) )
            return ' DEFAULT ' . ( 0 + $value );
        if ( $engine === 'oracle' && (string)$value === '' )
            return '';
        return ' DEFAULT ' . ( $engine === 'sqlserver' ? 'N' : '' ) . "'" . str_replace( "'", "''", (string)$value ) . "'";
    }
}
?>
