<?php
/**
 * File containing the expMigrationStepSchemaChange class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "schema": a table, a column or an index, written once and made on every engine by the kernel's own
 * schema handlers (eZDbSchema: MySQL/MariaDB, PostgreSQL, SQLite, Oracle with ezoracle, MongoDB), the way the
 * kernel's own tables are made (expKernelTableSchema) and its upgrade steps add columns (expUpgradeSchemaStep).
 *
 *   - type: schema
 *     mode: create_table
 *     table: exp_news_rating
 *     columns:                              name => definition (the .dba format of share/db_schema.dba)
 *       id: { type: auto_increment }
 *       contentobject_id: { type: int, not_null: true, default: 0 }
 *       rating: { type: int, length: 11, not_null: true, default: 0 }
 *       comment: { type: varchar, length: 255, default: "" }
 *     primary_key: [ id ]
 *     indexes:
 *       exp_news_rating_object: { columns: [ contentobject_id ], unique: false }
 *
 *   - type: schema
 *     mode: add_column                      also drop_column, add_index, drop_index, drop_table
 *     table: exp_news_rating
 *     column: created
 *     definition: { type: int, not_null: true, default: 0 }
 *
 *   - type: schema
 *     mode: add_index
 *     table: exp_news_rating
 *     index: exp_news_rating_created
 *     columns: [ created ]
 *     unique: false
 *
 * Column types: auto_increment, int, bigint, float, double, decimal (length "10,2"), char, varchar (with length),
 * longtext (text is taken as longtext). Each mode does nothing when it finds its work done (the table, the column or an
 * index on the same columns is there; what is dropped is gone), asked of the engine's catalogue (expUpgradeCatalog),
 * so a migration that stopped half way can run again.
 *
 * MongoDB: create_table makes the collection and its indexes, add_column gives the documents without the field the
 * default (when there is one), drop_column removes the field from every document, the index modes make and drop the
 * collection's indexes.
 *
 * create_table, add_column and add_index can be undone by the step itself (exp:migration:rollback without down:):
 * drop_table, drop_column and drop_index cannot, they need a down: or a rollback to the caption. MySQL/MariaDB and
 * Oracle commit each change at once (see the step type sql).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepSchemaChange extends expMigrationStepTypeBase implements expMigrationPartlyRevertibleStepType
{
    /** The column types of the DSL. */
    const COLUMN_TYPES = array( 'auto_increment', 'int', 'bigint', 'float', 'double', 'decimal', 'char', 'varchar', 'longtext', 'text' );

    /** The keys of a column definition. */
    const COLUMN_KEYS = array( 'type', 'length', 'not_null', 'default' );

    /** @return string */
    public function name()
    {
        return 'schema';
    }

    /** @return array */
    public function schema()
    {
        $all = array( 'create_table', 'drop_table', 'add_column', 'drop_column', 'add_index', 'drop_index' );
        return array(
            'description' => 'Makes or drops a table, a column or an index on every engine through the kernel\'s schema handlers',
            'modes' => $all,
            'keys' => array(
                'table' => array( 'type' => 'string', 'required' => true, 'description' => 'the table (a collection on MongoDB)' ),
                'columns' => array( 'type' => 'any', 'modes' => array( 'create_table', 'add_index', 'drop_index' ),
                                    'description' => 'create_table: name => definition (type, length, not_null, default); add_index, drop_index: the columns of the index' ),
                'primary_key' => array( 'type' => 'list', 'modes' => array( 'create_table' ), 'description' => 'the columns of the primary key' ),
                'indexes' => array( 'type' => 'map', 'modes' => array( 'create_table' ), 'description' => 'name => { columns: [...], unique: bool }' ),
                'column' => array( 'type' => 'string', 'required' => true, 'modes' => array( 'add_column', 'drop_column' ), 'description' => 'the column' ),
                'definition' => array( 'type' => 'map', 'required' => true, 'modes' => array( 'add_column' ), 'description' => 'the column: type, length, not_null, default' ),
                'index' => array( 'type' => 'string', 'required' => true, 'modes' => array( 'add_index', 'drop_index' ), 'description' => 'the name of the index' ),
                'unique' => array( 'type' => 'bool', 'default' => false, 'modes' => array( 'add_index' ), 'description' => 'a unique index' ),
            ),
            'values' => array(
                'changed' => 'true when the step changed the schema, false when it found its work done',
            ),
            'example' => "- type: schema\n  mode: add_column\n  table: exp_news_rating\n  column: created\n"
                       . "  definition: { type: int, not_null: true, default: 0 }\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = array();
        $mode = isset( $step['mode'] ) ? $step['mode'] : '';
        foreach ( array( 'table', 'column', 'index' ) as $key )
            if ( isset( $step[$key] ) && !expMigrationReferences::contains( $step[$key] ) && !self::safeName( $step[$key] ) )
                $errors[] = "$key: a name of letters, digits and _ that starts with a letter or _";
        if ( $mode === 'create_table' )
        {
            if ( !isset( $step['columns'] ) || !is_array( $step['columns'] ) || !$step['columns'] || expMigrationYaml::isList( $step['columns'] ) )
                $errors[] = 'columns: the columns, name => definition';
            else
                foreach ( $step['columns'] as $name => $def )
                    foreach ( self::columnProblems( $def ) as $problem )
                        $errors[] = "columns.$name$problem";
            $names = isset( $step['columns'] ) && is_array( $step['columns'] ) ? array_keys( $step['columns'] ) : array();
            foreach ( $names as $name )
                if ( !self::safeName( $name ) )
                    $errors[] = "columns.$name: a name of letters, digits and _";
            foreach ( isset( $step['primary_key'] ) ? (array)$step['primary_key'] : array() as $name )
                if ( !in_array( $name, $names, true ) )
                    $errors[] = "primary_key: $name is not one of the columns";
            foreach ( isset( $step['indexes'] ) && is_array( $step['indexes'] ) ? $step['indexes'] : array() as $name => $index )
            {
                if ( !self::safeName( $name ) )
                    $errors[] = "indexes.$name: a name of letters, digits and _";
                if ( !is_array( $index ) || !isset( $index['columns'] ) || !is_array( $index['columns'] ) || !$index['columns'] )
                    $errors[] = "indexes.$name: { columns: [...], unique: bool }";
                else if ( array_diff( $index['columns'], $names ) )
                    $errors[] = "indexes.$name: " . implode( ', ', array_diff( $index['columns'], $names ) ) . ' not among the columns';
                if ( is_array( $index ) && array_diff( array_keys( $index ), array( 'columns', 'unique' ) ) )
                    $errors[] = "indexes.$name: only columns and unique";
            }
        }
        if ( $mode === 'add_index' && ( !isset( $step['columns'] ) || !is_array( $step['columns'] ) || !$step['columns'] || !expMigrationYaml::isList( $step['columns'] ) ) )
            $errors[] = 'columns: the list of the index\'s columns';
        if ( in_array( $mode, array( 'add_index', 'drop_index' ), true ) && isset( $step['columns'] ) && is_array( $step['columns'] ) )
            foreach ( $step['columns'] as $name )
                if ( !is_string( $name ) || !self::safeName( $name ) )
                    $errors[] = 'columns: names of columns';
        if ( $mode === 'add_column' && isset( $step['definition'] ) )
            foreach ( self::columnProblems( $step['definition'] ) as $problem )
                $errors[] = "definition$problem";
        if ( $mode === 'add_column' && isset( $step['definition']['type'] ) && $step['definition']['type'] === 'auto_increment' )
            $errors[] = 'definition.type: an auto_increment column is made with its table (create_table)';
        return $errors;
    }

    /**
     * The problems of a column definition, each starting with ":" or ".<key>". Pure.
     *
     * @param mixed $def
     * @return string[]
     */
    public static function columnProblems( $def )
    {
        if ( !is_array( $def ) )
            return array( ': a map with type, length, not_null, default' );
        $problems = array();
        foreach ( array_diff( array_keys( $def ), self::COLUMN_KEYS ) as $key )
            $problems[] = ": the unknown key $key (" . implode( ', ', self::COLUMN_KEYS ) . ')';
        if ( !isset( $def['type'] ) || !in_array( $def['type'], self::COLUMN_TYPES, true ) )
            $problems[] = '.type: one of ' . implode( ', ', self::COLUMN_TYPES );
        else if ( in_array( $def['type'], array( 'char', 'varchar' ), true ) && empty( $def['length'] ) )
            $problems[] = '.length: ' . $def['type'] . ' needs a length';
        if ( isset( $def['length'] ) && !preg_match( '/^\d+(,\d+)?$/', (string)$def['length'] ) )
            $problems[] = '.length: a number (decimal: "10,2")';
        return $problems;
    }

    /**
     * A column of the DSL as a field of the .dba format. Pure.
     *
     * @param array $def
     * @return array
     */
    public static function field( array $def )
    {
        $field = array( 'type' => $def['type'] === 'text' ? 'longtext' : $def['type'] );
        if ( isset( $def['length'] ) )
            $field['length'] = is_int( $def['length'] ) || ctype_digit( (string)$def['length'] ) ? (int)$def['length'] : (string)$def['length'];
        if ( $field['type'] === 'auto_increment' )
        {
            $field['default'] = false;
            return $field;
        }
        $field['not_null'] = !empty( $def['not_null'] ) ? '1' : '0';
        $field['default'] = array_key_exists( 'default', $def ) ? $def['default'] : ( !empty( $def['not_null'] ) ? false : null );
        if ( is_bool( $field['default'] ) && array_key_exists( 'default', $def ) )
            $field['default'] = $field['default'] ? 1 : 0;
        return $field;
    }

    /**
     * The table of a create_table step in the .dba format. Pure.
     *
     * @param array $step
     * @return array
     */
    public static function table( array $step )
    {
        $table = array( 'name' => $step['table'], 'fields' => array(), 'indexes' => array() );
        foreach ( $step['columns'] as $name => $def )
            $table['fields'][$name] = self::field( $def );
        $primary = isset( $step['primary_key'] ) ? array_values( (array)$step['primary_key'] ) : array();
        if ( !$primary )
            foreach ( $step['columns'] as $name => $def )
                if ( $def['type'] === 'auto_increment' )
                    $primary = array( $name );
        if ( $primary )
            $table['indexes']['PRIMARY'] = array( 'type' => 'primary', 'fields' => $primary );
        foreach ( isset( $step['indexes'] ) ? (array)$step['indexes'] : array() as $name => $index )
            $table['indexes'][$name] = array( 'type' => !empty( $index['unique'] ) ? 'unique' : 'non-unique', 'fields' => array_values( $index['columns'] ) );
        ksort( $table['fields'] );
        return $table;
    }

    /**
     * @param array $step
     * @return bool
     */
    public function revertible( array $step )
    {
        return in_array( $step['mode'], array( 'create_table', 'add_column', 'add_index' ), true );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $what = self::describe( $step );
        try
        {
            $state = $this->done( $step, $context );
        }
        catch ( RuntimeException $e )
        {
            // a table a step before makes in the same run
            $state = null;
        }
        if ( $state === true )
            return array( "$what: done already, nothing to do" );
        $lines = array( $what . ( $state === null ? ' (cannot tell whether it is done)' : '' ) );
        foreach ( $this->statements( $step, $context->engine(), $context->db() ) as $sql )
            $lines[] = '  ' . expMigrationStepSql::show( $sql );
        return $lines;
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $what = self::describe( $step );
        $state = $this->done( $step, $context );
        if ( $state === true )
            return self::result( array( "$what: done already" ), array( 'changed' => false ) );
        if ( $state === null )
            throw new RuntimeException( "$what: cannot tell from the engine's catalogue whether it is done" );
        $db = $context->db();
        $engine = $context->engine();
        $lines = array();
        if ( $step['mode'] === 'create_table' )
        {
            $messages = array();
            if ( !expKernelTableSchema::install( array( $step['table'] ), $db, array( $step['table'] => self::table( $step ) ), $messages ) )
                throw new RuntimeException( "$what: " . implode( '; ', $messages ) );
            $lines[] = "$what: created";
            if ( $context->transactionMode() === expMigrationContext::TRANSACTION_DATA && $db->transactionCounter() > 0 )
                $lines[] = "$engine committed the schema change at once: a failure later in this migration does not undo it";
        }
        else if ( $engine === 'mongodb' )
        {
            $lines[] = $this->applyMongo( $step, $db );
        }
        else
        {
            $statements = $this->statements( $step, $engine, $db );
            if ( !$statements )
                throw new RuntimeException( "$what: no SQL for $engine (no schema handler)" );
            $lines = array_merge( $lines, expMigrationStepSql::runStatements( $context, $statements ) );
            array_unshift( $lines, "$what: done" );
        }
        expKernelTableSchema::reset();
        return self::result( $lines, array( 'changed' => true ) );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        $undo = array( 'table' => $step['table'] );
        switch ( $step['mode'] )
        {
            case 'create_table':
                $undo['mode'] = 'drop_table';
                break;
            case 'add_column':
                $undo += array( 'mode' => 'drop_column', 'column' => $step['column'] );
                break;
            case 'add_index':
                $undo += array( 'mode' => 'drop_index', 'index' => $step['index'], 'columns' => $step['columns'] );
                break;
            default:
                throw new RuntimeException( self::describe( $step ) . ' cannot be undone by the step itself' );
        }
        $result = $this->apply( $undo, $context );
        return $result['lines'];
    }

    /**
     * Whether the step's work is done already: true, false, null (the catalogue cannot be read).
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return bool|null
     */
    protected function done( array $step, $context )
    {
        $db = $context->db();
        $table = $step['table'];
        $exists = expKernelTableSchema::exists( $table, $db, true );
        if ( $exists === null )
            return null;
        switch ( $step['mode'] )
        {
            case 'create_table':
                return $exists;
            case 'drop_table':
                return !$exists;
        }
        if ( !$exists )
        {
            if ( in_array( $step['mode'], array( 'drop_column', 'drop_index' ), true ) )
                return true;
            throw new RuntimeException( self::describe( $step ) . ": there is no table $table" );
        }
        $mongo = $context->engine() === 'mongodb';
        switch ( $step['mode'] )
        {
            case 'add_column':
            case 'drop_column':
                if ( $mongo )
                    return false;
                $has = expUpgradeCatalog::hasColumn( $db, $table, $step['column'] );
                return $has === null ? null : ( $step['mode'] === 'add_column' ? $has : !$has );
            case 'add_index':
                if ( $mongo )
                    return self::mongoIndexName( $db, $table, $step['index'] ) !== null;
                $has = expUpgradeCatalog::hasIndex( $db, $table, $step['columns'], !empty( $step['unique'] ) );
                return $has;
            case 'drop_index':
                if ( $mongo )
                    return self::mongoIndexName( $db, $table, $step['index'] ) === null;
                $name = self::indexName( $db, $table, $step );
                return $name === false ? null : $name === null;
        }
        return false;
    }

    /**
     * The statements of a mode on an SQL engine (create_table: what expKernelTableSchema::install() runs).
     *
     * @param array $step
     * @param string $engine
     * @param eZDBInterface|null $db the connection, for the real name of an index to drop
     * @return string[]
     */
    protected function statements( array $step, $engine, $db = null )
    {
        if ( $engine === 'mongodb' )
            return array();
        $table = $step['table'];
        switch ( $step['mode'] )
        {
            case 'create_table':
                return expUpgradeCatalog::statements( expKernelTableSchema::sql( array( $table => self::table( $step ) ), $engine ) );
            case 'drop_table':
                return array( "DROP TABLE $table" );
            case 'drop_column':
                if ( $engine === 'sqlite' && $db && !self::sqliteDropsColumns( $db ) )
                    return self::sqliteRebuildWithout( $db, $table, $step['column'] );
                return array( "ALTER TABLE $table DROP COLUMN {$step['column']}" );
            case 'drop_index':
                $name = self::indexName( $db ?: eZDB::instance(), $table, $step );
                $name = is_string( $name ) ? $name : $step['index'];
                return array( $engine === 'mysql' ? "ALTER TABLE $table DROP INDEX $name" : "DROP INDEX $name" );
        }
        $handler = eZDbSchema::instance( array( 'type' => $engine, 'instance' => expKernelTableSchema::nullDB(), 'schema' => array() ) );
        if ( !$handler )
            return array();
        $params = array( 'schema' => true, 'data' => false, 'allow_multi_insert' => false, 'diff_friendly' => false,
                         'table_type' => $engine === 'mysql' ? 'innodb' : '' );
        if ( $step['mode'] === 'add_column' )
        {
            $field = self::field( $step['definition'] );
            // PostgreSQL: default and NOT NULL in the one statement, so the rows there are filled (expUpgradeSchemaStep)
            $sql = $engine === 'postgresql'
                 ? "ALTER TABLE $table ADD COLUMN " . $handler->generateFieldDef( $table, $step['column'], $field, true, $params )
                 : (string)$handler->generateAddFieldSql( $table, $step['column'], $field, $params );
            return expUpgradeCatalog::statements( $sql );
        }
        $def = array( 'type' => !empty( $step['unique'] ) ? 'unique' : 'non-unique', 'fields' => array_values( (array)$step['columns'] ) );
        return expUpgradeCatalog::statements( (string)$handler->generateAddIndexSql( $table, $step['index'], $def, $params ) );
    }

    /**
     * Whether SQLite knows ALTER TABLE ... DROP COLUMN (3.35 and later).
     *
     * @param eZDBInterface $db
     * @return bool
     */
    protected static function sqliteDropsColumns( $db )
    {
        $rows = expKernelTableSchema::tryArrayQuery( $db, 'SELECT sqlite_version() AS v' );
        $version = $rows ? (string)reset( $rows[0] ) : '';
        return $version !== '' && version_compare( $version, '3.35.0', '>=' );
    }

    /**
     * The statements that drop a column on an SQLite before 3.35, as SQLite's documentation says: a new table without
     * the column, the rows copied, the old table dropped, the new one renamed, its other indexes made again. A column
     * in a primary key, or in a constraint of the table, is refused.
     *
     * @param eZDBInterface $db
     * @param string $table
     * @param string $column
     * @return string[]
     * @throws RuntimeException
     */
    protected static function sqliteRebuildWithout( $db, $table, $column )
    {
        $rows = expKernelTableSchema::tryArrayQuery( $db, "SELECT type, name, sql FROM sqlite_master WHERE tbl_name = '$table' AND sql IS NOT NULL" );
        $create = null;
        $indexes = array();
        foreach ( (array)$rows as $row )
        {
            $row = array_change_key_case( (array)$row, CASE_LOWER );
            if ( $row['type'] === 'table' && strcasecmp( $row['name'], $table ) === 0 )
                $create = (string)$row['sql'];
            else if ( $row['type'] === 'index' )
                $indexes[] = (string)$row['sql'];
        }
        if ( $create === null || !preg_match( '/^[^(]*\((.*)\)\s*$/s', $create, $m ) )
            throw new RuntimeException( "cannot read the table $table from sqlite_master" );
        $parts = array();
        $depth = 0;
        $quote = null;
        $current = '';
        foreach ( str_split( $m[1] ) as $char )
        {
            if ( $quote !== null )
                $quote = $char === $quote ? null : $quote;
            else if ( $char === "'" || $char === '"' || $char === '`' )
                $quote = $char;
            else if ( $char === '(' )
                $depth++;
            else if ( $char === ')' )
                $depth--;
            else if ( $char === ',' && $depth === 0 )
            {
                $parts[] = trim( $current );
                $current = '';
                continue;
            }
            $current .= $char;
        }
        $parts[] = trim( $current );
        $keep = array();
        $columns = array();
        $pattern = '/^["`\[]?' . preg_quote( $column, '/' ) . '["`\]]?(\s|$)/i';
        foreach ( $parts as $part )
        {
            if ( preg_match( $pattern, $part ) )
            {
                if ( stripos( $part, 'PRIMARY KEY' ) !== false )
                    throw new RuntimeException( "the column $table.$column is the primary key" );
                continue;
            }
            if ( preg_match( '/^(CONSTRAINT|PRIMARY|UNIQUE|CHECK|FOREIGN)\b/i', $part ) )
            {
                if ( preg_match( '/\b' . preg_quote( $column, '/' ) . '\b/i', $part ) )
                    throw new RuntimeException( "the column $table.$column is in a constraint of the table: $part" );
            }
            else if ( preg_match( '/^["`\[]?([A-Za-z_][A-Za-z0-9_]*)/', $part, $name ) )
            {
                $columns[] = $name[1];
            }
            $keep[] = $part;
        }
        if ( count( $keep ) === count( $parts ) )
            throw new RuntimeException( "the table $table has no column $column" );
        $new = $table . '__wp_rebuild';
        $list = implode( ', ', $columns );
        $statements = array( "CREATE TABLE $new (\n  " . implode( ",\n  ", $keep ) . "\n)",
                             "INSERT INTO $new ( $list ) SELECT $list FROM $table",
                             "DROP TABLE $table",
                             "ALTER TABLE $new RENAME TO $table" );
        foreach ( $indexes as $index )
            if ( !preg_match( '/\(([^)]*)\)\s*$/', $index, $on ) || !preg_match( '/\b' . preg_quote( $column, '/' ) . '\b/i', $on[1] ) )
                $statements[] = $index;
        return $statements;
    }

    /**
     * The name the database gives an index of the step: the step's name, the table-prefixed name of SQLite, or an
     * index on the step's columns; null when there is none, false when the catalogue cannot be read.
     *
     * @param eZDBInterface $db
     * @param string $table
     * @param array $step
     * @return string|null|false
     */
    protected static function indexName( $db, $table, array $step )
    {
        $indexes = expUpgradeCatalog::indexes( $db, $table );
        if ( $indexes === null )
            return false;
        $wanted = strtolower( $step['index'] );
        foreach ( array_keys( $indexes ) as $name )
            if ( in_array( strtolower( $name ), array( $wanted, strtolower( $table ) . '__' . $wanted ), true ) )
                return $name;
        if ( !empty( $step['columns'] ) )
        {
            $fields = array_map( 'strtolower', array_values( (array)$step['columns'] ) );
            foreach ( $indexes as $name => $index )
                if ( $index['fields'] === $fields && stripos( $name, 'primary' ) === false && stripos( $name, 'pkey' ) === false )
                    return $name;
        }
        return null;
    }

    /**
     * @param expMongoDB $db
     * @param string $table
     * @param string $name
     * @return string|null
     */
    protected static function mongoIndexName( $db, $table, $name )
    {
        foreach ( $db->getClient()->selectCollection( $db->DB, $table )->listIndexes() as $index )
            if ( $index->getName() === $name )
                return $name;
        return null;
    }

    /**
     * A change on MongoDB (a collection has no columns: fields are filled or removed in the documents).
     *
     * @param array $step
     * @param expMongoDB $db
     * @return string
     */
    protected function applyMongo( array $step, $db )
    {
        $what = self::describe( $step );
        $table = $step['table'];
        switch ( $step['mode'] )
        {
            case 'drop_table':
                return expMigrationStepSql::runMongo( $db, array( 'drop_collection' => $table ) );
            case 'add_column':
                $field = self::field( $step['definition'] );
                if ( $field['default'] === null || $field['default'] === false )
                    return "$what: MongoDB has no columns, nothing to fill";
                $db->getClient()->selectCollection( $db->DB, $table )->updateMany( array( $step['column'] => array( '$exists' => false ) ),
                                                                                  array( '$set' => array( $step['column'] => $field['default'] ) ) );
                return "$what: the documents without it have the default";
            case 'drop_column':
                $db->getClient()->selectCollection( $db->DB, $table )->updateMany( array(), array( '$unset' => array( $step['column'] => '' ) ) );
                return "$what: removed from the documents";
            case 'add_index':
                $keys = array_fill_keys( array_values( (array)$step['columns'] ), 1 );
                return expMigrationStepSql::runMongo( $db, array( 'create_index' => $table, 'keys' => $keys, 'name' => $step['index'], 'unique' => !empty( $step['unique'] ) ) );
            case 'drop_index':
                return expMigrationStepSql::runMongo( $db, array( 'drop_index' => $table, 'name' => $step['index'] ) );
        }
        throw new RuntimeException( "$what: unknown mode" );
    }

    /**
     * @param array $step
     * @return string
     */
    protected static function describe( array $step )
    {
        switch ( $step['mode'] )
        {
            case 'create_table':
                return "create the table {$step['table']}";
            case 'drop_table':
                return "drop the table {$step['table']}";
            case 'add_column':
                return "add the column {$step['table']}.{$step['column']}";
            case 'drop_column':
                return "drop the column {$step['table']}.{$step['column']}";
            case 'add_index':
                return "add the index {$step['index']} on {$step['table']}";
            case 'drop_index':
                return "drop the index {$step['index']} of {$step['table']}";
        }
        return 'schema ' . $step['mode'];
    }

    /**
     * Whether a name is a plain table, column or index name. Pure.
     *
     * @param mixed $name
     * @return bool
     */
    public static function safeName( $name )
    {
        return is_string( $name ) && (bool)preg_match( '/^[A-Za-z_][A-Za-z0-9_]{0,127}$/', $name );
    }
}
