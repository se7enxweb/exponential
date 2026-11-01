<?php
/**
 * File containing the expMigrationStepData class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "data": rows of a table, written once for every engine. The values are quoted by the connection
 * (eZDB::escapeString()) on the SQL engines and stored as they are on MongoDB, so a value from a reference
 * (${ref.name}) is safe here, unlike in the statements of the step type sql.
 *
 *   - type: data
 *     mode: insert                    insert, ensure, update, delete
 *     table: exp_news_rating
 *     rows:
 *       - { contentobject_id: "${ref.article}", rating: 5, comment: "" }
 *     id_column: id                   the auto_increment column: its new value is the result's id
 *     references: { rating: id }
 *
 *   - type: data
 *     mode: ensure                    insert a row that is not there, update the one that is
 *     table: ezsite_data
 *     key: [ name ]                   the columns that find the row
 *     rows:
 *       - { name: news_list_limit, value: "20" }
 *
 *   - type: data
 *     mode: update                    also delete (where only)
 *     table: ezcontentclass
 *     where: { identifier: news }     column => value: equal; a list: one of; null: is null
 *     values: { always_available: 1 }
 *     all: false                      true: every row (an update or delete without where)
 *
 * Values: a string, a number, true or false (1 and 0) or null. Columns are names of letters, digits and _. The step
 * cannot undo itself: a migration with it needs a down: or a rollback to its caption.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepData extends expMigrationStepTypeBase
{
    /** @return string */
    public function name()
    {
        return 'data';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Inserts, updates or deletes rows of a table on every engine, the values quoted by the connection',
            'modes' => array( 'insert', 'ensure', 'update', 'delete' ),
            'keys' => array(
                'table' => array( 'type' => 'string', 'required' => true, 'description' => 'the table (a collection on MongoDB)' ),
                'rows' => array( 'type' => 'list', 'required' => true, 'modes' => array( 'insert', 'ensure' ), 'description' => 'the rows, each column => value' ),
                'key' => array( 'type' => 'list', 'required' => true, 'modes' => array( 'ensure' ), 'description' => 'the columns that find a row' ),
                'id_column' => array( 'type' => 'string', 'modes' => array( 'insert', 'ensure' ), 'description' => 'the auto_increment column; its value of the last row inserted is the result\'s id' ),
                'where' => array( 'type' => 'map', 'modes' => array( 'update', 'delete' ), 'description' => 'column => value (a list: one of them; null: is null)' ),
                'values' => array( 'type' => 'map', 'required' => true, 'modes' => array( 'update' ), 'description' => 'column => new value' ),
                'all' => array( 'type' => 'bool', 'default' => false, 'modes' => array( 'update', 'delete' ), 'description' => 'true: every row, without where' ),
            ),
            'values' => array(
                'count' => 'how many rows were inserted, updated or deleted',
                'id' => 'insert, ensure: the id_column of the last row inserted (null when none was)',
            ),
            'example' => "- type: data\n  mode: ensure\n  table: ezsite_data\n  key: [ name ]\n  rows:\n    - { name: news_list_limit, value: \"20\" }\n",
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
        if ( isset( $step['table'] ) && !expMigrationReferences::contains( $step['table'] ) && !expMigrationStepSchemaChange::safeName( $step['table'] ) )
            $errors[] = 'table: a name of letters, digits and _';
        if ( isset( $step['id_column'] ) && !expMigrationStepSchemaChange::safeName( $step['id_column'] ) )
            $errors[] = 'id_column: the name of a column';
        foreach ( isset( $step['rows'] ) && is_array( $step['rows'] ) ? $step['rows'] : array() as $i => $row )
        {
            if ( !is_array( $row ) || !$row || expMigrationYaml::isList( $row ) )
            {
                $errors[] = "rows[$i]: a map of column => value";
                continue;
            }
            foreach ( self::mapProblems( $row ) as $problem )
                $errors[] = "rows[$i]$problem";
            foreach ( isset( $step['key'] ) ? (array)$step['key'] : array() as $column )
                if ( is_string( $column ) && !array_key_exists( $column, $row ) )
                    $errors[] = "rows[$i]: the key column $column is missing";
        }
        if ( isset( $step['rows'] ) && $step['rows'] === array() )
            $errors[] = 'rows: at least one row';
        foreach ( isset( $step['key'] ) ? (array)$step['key'] : array() as $column )
            if ( !expMigrationStepSchemaChange::safeName( $column ) )
                $errors[] = 'key: names of columns';
        if ( isset( $step['key'] ) && $step['key'] === array() )
            $errors[] = 'key: at least one column';
        foreach ( array( 'where', 'values' ) as $key )
            if ( isset( $step[$key] ) && is_array( $step[$key] ) )
                foreach ( self::mapProblems( $step[$key], $key === 'where' ) as $problem )
                    $errors[] = $key . $problem;
        if ( in_array( $mode, array( 'update', 'delete' ), true ) )
        {
            $where = isset( $step['where'] ) && is_array( $step['where'] ) && $step['where'];
            if ( !$where && empty( $step['all'] ) )
                $errors[] = "where: the rows to $mode (all: true for every row)";
            if ( $where && !empty( $step['all'] ) )
                $errors[] = 'all: not with where';
        }
        return $errors;
    }

    /**
     * The problems of a map of column => value, each starting with "." or ":". Pure.
     *
     * @param array $map
     * @param bool $lists whether a value may be a list (where)
     * @return string[]
     */
    protected static function mapProblems( array $map, $lists = false )
    {
        $problems = array();
        foreach ( $map as $column => $value )
        {
            if ( !expMigrationStepSchemaChange::safeName( (string)$column ) )
                $problems[] = ".$column: a column name of letters, digits and _";
            $values = $lists && is_array( $value ) && expMigrationYaml::isList( $value ) ? $value : array( $value );
            foreach ( $values as $one )
                if ( $one !== null && !is_scalar( $one ) )
                    $problems[] = ".$column: a string, a number, true, false or null" . ( $lists ? ', or a list of them' : '' );
        }
        return $problems;
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $table = $step['table'];
        switch ( $step['mode'] )
        {
            case 'insert':
                return array( 'insert ' . count( $step['rows'] ) . ' ' . self::rows( count( $step['rows'] ) ) . " into $table" );
            case 'ensure':
                return array( 'ensure ' . count( $step['rows'] ) . ' ' . self::rows( count( $step['rows'] ) ) . " of $table by " . implode( ', ', $step['key'] ) );
            case 'update':
                return array( "update the rows of $table " . self::describeWhere( $step ) . ': set ' . implode( ', ', array_keys( $step['values'] ) ) );
            case 'delete':
                return array( "delete the rows of $table " . self::describeWhere( $step ) );
        }
        return array( 'data ' . $step['mode'] );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $db = $context->db();
        $mongo = $context->engine() === 'mongodb';
        $table = $step['table'];
        $where = isset( $step['where'] ) && is_array( $step['where'] ) ? $step['where'] : array();
        $idColumn = isset( $step['id_column'] ) ? (string)$step['id_column'] : '';
        $count = 0;
        $id = null;
        switch ( $step['mode'] )
        {
            case 'insert':
                foreach ( $step['rows'] as $row )
                {
                    $this->insert( $db, $mongo, $table, $row );
                    $count++;
                    if ( $idColumn !== '' )
                        $id = $this->lastId( $db, $table, $idColumn, $row );
                }
                return self::result( array( "inserted $count " . self::rows( $count ) . " into $table" ), array( 'count' => $count, 'id' => $id ) );
            case 'ensure':
                $updated = 0;
                foreach ( $step['rows'] as $row )
                {
                    $match = array_intersect_key( $row, array_flip( $step['key'] ) );
                    if ( $this->count( $db, $mongo, $table, $match ) === 0 )
                    {
                        $this->insert( $db, $mongo, $table, $row );
                        $count++;
                        if ( $idColumn !== '' )
                            $id = $this->lastId( $db, $table, $idColumn, $row );
                    }
                    else if ( array_diff_key( $row, $match ) )
                    {
                        $this->update( $db, $mongo, $table, $match, array_diff_key( $row, $match ) );
                        $updated++;
                    }
                }
                return self::result( array( "inserted $count, updated $updated " . self::rows( $updated ) . " of $table" ),
                                     array( 'count' => $count + $updated, 'id' => $id ) );
            case 'update':
                $count = $this->count( $db, $mongo, $table, $where );
                if ( $count )
                    $this->update( $db, $mongo, $table, $where, $step['values'] );
                return self::result( array( "updated $count " . self::rows( $count ) . " of $table" ), array( 'count' => $count ) );
            case 'delete':
                $count = $this->count( $db, $mongo, $table, $where );
                if ( $count )
                {
                    if ( $mongo )
                    {
                        if ( !$db->deleteWhere( $table, self::filter( $where ) ) )
                            throw new RuntimeException( "delete from $table failed" );
                    }
                    else
                    {
                        expUpgradeCatalog::execute( $db, "DELETE FROM $table" . self::whereSql( $db, $where ) );
                    }
                }
                return self::result( array( "deleted $count " . self::rows( $count ) . " of $table" ), array( 'count' => $count ) );
        }
        throw new RuntimeException( 'unknown mode ' . $step['mode'] );
    }

    /**
     * @param eZDBInterface $db
     * @param bool $mongo
     * @param string $table
     * @param array $row
     */
    protected function insert( $db, $mongo, $table, array $row )
    {
        if ( $mongo )
        {
            if ( !$db->insert( $table, $row ) )
                throw new RuntimeException( "insert into $table failed" );
            return;
        }
        $values = array();
        foreach ( $row as $value )
            $values[] = self::literal( $db, $value );
        expUpgradeCatalog::execute( $db, "INSERT INTO $table (" . implode( ', ', array_keys( $row ) ) . ') VALUES (' . implode( ', ', $values ) . ')' );
    }

    /**
     * @param eZDBInterface $db
     * @param bool $mongo
     * @param string $table
     * @param array $where
     * @param array $values
     */
    protected function update( $db, $mongo, $table, array $where, array $values )
    {
        if ( $mongo )
        {
            if ( !$db->mongoUpdateMany( $table, self::filter( $where ), array( '$set' => $values ) ) )
                throw new RuntimeException( "update of $table failed" );
            return;
        }
        $sets = array();
        foreach ( $values as $column => $value )
            $sets[] = "$column = " . self::literal( $db, $value );
        expUpgradeCatalog::execute( $db, "UPDATE $table SET " . implode( ', ', $sets ) . self::whereSql( $db, $where ) );
    }

    /**
     * @param eZDBInterface $db
     * @param bool $mongo
     * @param string $table
     * @param array $where
     * @return int
     */
    protected function count( $db, $mongo, $table, array $where )
    {
        if ( $mongo )
            return (int)$db->getClient()->selectCollection( $db->DB, $table )->countDocuments( self::filter( $where ) );
        $before = expAuditIndexSchema::errorHandling( $db );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            $rows = $db->arrayQuery( "SELECT COUNT(*) AS n FROM $table" . self::whereSql( $db, $where ) );
        }
        finally
        {
            $db->setErrorHandling( $before );
        }
        if ( !is_array( $rows ) || !$rows )
            throw new RuntimeException( "cannot count the rows of $table" );
        $row = array_change_key_case( (array)$rows[0], CASE_LOWER );
        return (int)( isset( $row['n'] ) ? $row['n'] : reset( $row ) );
    }

    /**
     * @param eZDBInterface $db
     * @param string $table
     * @param string $column
     * @param array $row
     * @return mixed
     */
    protected function lastId( $db, $table, $column, array $row )
    {
        if ( isset( $row[$column] ) )
            return $row[$column];
        $id = $db->lastSerialID( $table, $column );
        return $id === false ? null : ( is_numeric( $id ) ? (int)$id : $id );
    }

    /**
     * A value as SQL.
     *
     * @param eZDBInterface $db
     * @param mixed $value
     * @return string
     */
    public static function literal( $db, $value )
    {
        if ( $value === null )
            return 'NULL';
        if ( is_bool( $value ) )
            return $value ? '1' : '0';
        if ( is_int( $value ) || is_float( $value ) )
            return (string)$value;
        return "'" . $db->escapeString( (string)$value ) . "'";
    }

    /**
     * " WHERE ..." of a map, or "" for none.
     *
     * @param eZDBInterface $db
     * @param array $where
     * @return string
     */
    public static function whereSql( $db, array $where )
    {
        $parts = array();
        foreach ( $where as $column => $value )
        {
            if ( $value === null )
                $parts[] = "$column IS NULL";
            else if ( is_array( $value ) )
                $parts[] = $value ? "$column IN (" . implode( ', ', array_map( function ( $v ) use ( $db ) { return expMigrationStepData::literal( $db, $v ); }, $value ) ) . ')' : '1 = 0';
            else
                $parts[] = "$column = " . self::literal( $db, $value );
        }
        return $parts ? ' WHERE ' . implode( ' AND ', $parts ) : '';
    }

    /**
     * A MongoDB filter of a map. Pure.
     *
     * @param array $where
     * @return array
     */
    public static function filter( array $where )
    {
        $filter = array();
        foreach ( $where as $column => $value )
            $filter[$column] = is_array( $value ) ? array( '$in' => array_values( $value ) ) : $value;
        return $filter;
    }

    /** @return string */
    protected static function describeWhere( array $step )
    {
        if ( !empty( $step['all'] ) )
            return '(every row)';
        $parts = array();
        foreach ( $step['where'] as $column => $value )
            $parts[] = $column . ( $value === null ? ' is null' : ( is_array( $value ) ? ' in (' . count( $value ) . ' values)' : ' = ' . expMigrationStepSchema::show( $value ) ) );
        return 'where ' . implode( ' and ', $parts );
    }

    /** @return string */
    protected static function rows( $n )
    {
        return $n === 1 ? 'row' : 'rows';
    }
}
