<?php
/**
 * MongoDB schema handler for eZDbSchema.
 *
 * Translates the DBA schema format into MongoDB collection + index creation,
 * and inserts initial DBA data rows as MongoDB documents.
 *
 * @package lib
 */

class expMongoSchema extends eZDBSchemaInterface
{
    /**
     * Returns the schema array. MongoDB is schema-less; we return the DBA-loaded
     * schema so that callers (e.g. insertSchema) can iterate the table/index
     * definitions to set up collections.
     *
     * @param array $params
     * @return array
     */
    function schema( $params = array() )
    {
        if ( $this->Schema === false )
            return array();

        return $this->Schema;
    }

    /**
     * Creates MongoDB collections and indexes from the DBA schema definition,
     * and/or inserts initial data rows as documents.
     *
     * @param array $params  Keys: 'schema' (bool) create collections+indexes,
     *                             'data'   (bool) insert seed documents.
     * @return bool
     */
    function insertSchema( $params = array() )
    {
        $params = array_merge( array( 'schema' => true, 'data' => false ), $params );

        if ( !is_object( $this->DBInstance ) )
        {
            eZDebug::writeError( "No database instance available", __METHOD__ );
            return false;
        }

        if ( !method_exists( $this->DBInstance, 'getClient' ) )
        {
            eZDebug::writeError( "DB instance does not support getClient(); cannot initialise MongoDB schema", __METHOD__ );
            return false;
        }

        $schema = $this->Schema;

        // When called with only data=true (e.g. from importDBDataFromDBAFile),
        // the schema may be absent; that is fine – we just skip collection/index creation.
        if ( $params['schema'] && is_array( $schema ) )
        {
            if ( !$this->createCollectionsAndIndexes( $schema ) )
                return false;
        }

        if ( $params['data'] && is_array( $this->Data ) )
        {
            $iterSchema = is_array( $schema ) ? $schema : array();
            if ( !$this->insertDataDocuments( $iterSchema, $this->Data ) )
                return false;
        }

        return true;
    }

    /**
     * Iterates over a DBA schema array and creates MongoDB indexes for each table.
     * Collections are created implicitly by MongoDB on first write.
     *
     * Index mapping from DBA types:
     *  - 'primary'    on a single auto_increment field  → skip (handled by sequence)
     *  - 'primary'    on one non-auto_increment field   → unique index
     *  - 'primary'    on multiple fields                → compound unique index
     *  - 'unique'                                       → unique index
     *  - 'non-unique'                                   → regular index
     *
     * @param array $schema DBA schema array
     * @return bool
     */
    protected function createCollectionsAndIndexes( array $schema )
    {
        $dbName = $this->DBInstance->DB;
        $client = $this->DBInstance->getClient();

        foreach ( $schema as $tableName => $tableDef )
        {
            if ( $tableName === '_info' || !is_array( $tableDef ) )
                continue;

            if ( !isset( $tableDef['indexes'] ) || !is_array( $tableDef['indexes'] ) )
                continue;

            $collection = $client->selectCollection( $dbName, $tableName );
            $fields     = isset( $tableDef['fields'] ) ? $tableDef['fields'] : array();

            foreach ( $tableDef['indexes'] as $indexName => $indexDef )
            {
                if ( !isset( $indexDef['fields'] ) || !is_array( $indexDef['fields'] ) )
                    continue;

                $indexType = isset( $indexDef['type'] ) ? $indexDef['type'] : 'non-unique';

                // Build the field key document: { fieldName: 1, ... }
                $keyDoc = array();
                foreach ( $indexDef['fields'] as $fieldEntry )
                {
                    $fieldName = is_array( $fieldEntry ) ? $fieldEntry['name'] : $fieldEntry;
                    $keyDoc[$fieldName] = 1;
                }

                // For a primary key that is a single auto_increment column,
                // MongoDB uses its own sequence mechanism – skip creating a unique index.
                if ( $indexType === 'primary' && count( $keyDoc ) === 1 )
                {
                    $singleField = key( $keyDoc );
                    if ( isset( $fields[$singleField] ) &&
                         $fields[$singleField]['type'] === 'auto_increment' )
                    {
                        continue;
                    }
                }

                $options = array( 'name' => $indexName );
                if ( $indexType === 'primary' || $indexType === 'unique' )
                    $options['unique'] = true;

                try
                {
                    $collection->createIndex( $keyDoc, $options );
                }
                catch ( \Exception $e )
                {
                    eZDebug::writeWarning(
                        "MongoDB createIndex '$indexName' on '$tableName': " . $e->getMessage(),
                        __METHOD__
                    );
                    // Non-fatal: warn and continue.
                }
            }
        }

        return true;
    }

    /**
     * Inserts DBA data rows as MongoDB documents.
     *
     * DBA data format per table:
     *   $data[$tableName] = array(
     *       'fields' => array( 'field1', 'field2', ... ),
     *       'rows'   => array( array( val1, val2, ... ), ... ),
     *   )
     *
     * @param array $schema DBA schema array (used to identify auto_increment fields)
     * @param array $data   DBA data array
     * @return bool
     */
    protected function insertDataDocuments( array $schema, array $data )
    {
        $dbName = $this->DBInstance->DB;
        $client = $this->DBInstance->getClient();

        foreach ( $data as $tableName => $tableData )
        {
            if ( $tableName === '_info' )
                continue;

            if ( !isset( $tableData['fields'] ) || !isset( $tableData['rows'] ) )
                continue;

            if ( empty( $tableData['rows'] ) )
                continue;

            $fieldNames = $tableData['fields'];
            $collection = $client->selectCollection( $dbName, $tableName );

            // Determine which field is auto_increment so we can feed the sequence.
            $autoIncrField = false;
            if ( isset( $schema[$tableName]['fields'] ) )
            {
                foreach ( $schema[$tableName]['fields'] as $fname => $fdef )
                {
                    if ( isset( $fdef['type'] ) && $fdef['type'] === 'auto_increment' )
                    {
                        $autoIncrField = $fname;
                        break;
                    }
                }
            }

            $maxAutoIncrValue = 0;

            foreach ( $tableData['rows'] as $row )
            {
                $doc = array_combine( $fieldNames, $row );
                if ( $doc === false )
                    continue;

                // Cast numeric string values to their proper PHP types so that
                // queries using integer literals (e.g. version=0) match correctly.
                // DBA files store all values as strings; MongoDB does no type coercion.
                foreach ( $doc as $k => $v )
                {
                    if ( is_string( $v ) && is_numeric( $v ) )
                    {
                        $doc[$k] = strpos( $v, '.' ) === false ? (int)$v : (float)$v;
                    }
                }

                // Track the max value for the auto_increment field so the
                // sequence can be initialised to the correct next value.
                if ( $autoIncrField !== false && isset( $doc[$autoIncrField] ) )
                {
                    $val = (int) $doc[$autoIncrField];
                    if ( $val > $maxAutoIncrValue )
                        $maxAutoIncrValue = $val;
                }

                try
                {
                    $collection->insertOne( $doc );
                }
                catch ( \Exception $e )
                {
                    eZDebug::writeWarning(
                        "MongoDB insertOne into '$tableName': " . $e->getMessage(),
                        __METHOD__
                    );
                }
            }

            // Advance the eZPersistentObject sequence so it starts above
            // the highest value we just inserted.
            if ( $autoIncrField !== false && $maxAutoIncrValue > 0 &&
                 method_exists( $this->DBInstance, 'setSequenceValue' ) )
            {
                $this->DBInstance->setSequenceValue( $tableName, $autoIncrField, $maxAutoIncrValue );
            }
        }

        return true;
    }

    /**
     * Returns the schema type identifier (matches the 'type' key in DBA arrays
     * and the DatabaseImplementation alias).
     */
    function schemaType()
    {
        return 'mongo';
    }

    /**
     * Returns a human-readable name for this schema handler.
     */
    function schemaName()
    {
        return 'MongoDB';
    }

    // -------------------------------------------------------------------------
    // Abstract stubs – not used for MongoDB but required by eZDBSchemaInterface
    // -------------------------------------------------------------------------

    function generateTableSchema( $table, $tableDef, $params = array() )
    {
        return '';
    }

    function generateTableSQLList( $tableName, $table, $params, $separateIndexes = false )
    {
        return array();
    }

    function generateAddFieldSql( $table, $fieldName, $def, $params )
    {
        return '';
    }

    function generateAlterFieldSql( $table, $fieldName, $def, $params )
    {
        return '';
    }

    function generateDropFieldSql( $table, $fieldName, $params )
    {
        return '';
    }

    function generateAddIndexSql( $tableName, $indexName, $def, $params, $withAlter = true )
    {
        return '';
    }

    function generateDropIndexSql( $tableName, $indexName, $indexDef, $params )
    {
        return '';
    }

    function generateDropTable( $table )
    {
        return '';
    }

    function generateTableInsert( $tableName, $tableDef, $dataEntries, $params )
    {
        return '';
    }

    function generateTableInsertSQLList( $tableName, $table, $dataEntries, $params, $withClear = false )
    {
        return array();
    }

    function transformSchema( &$schema, $toLocal )
    {
        // MongoDB has no local/generic distinction.
    }
}
