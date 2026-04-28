<?php

class eZSQLiteSchema extends eZDBSchemaInterface
{
    function __construct( $params )
    {
        parent::__construct( $params );
    }

    /*!
     \reimp
    */
    function schema( $params = array() )
    {
        $params = array_merge( array( 'meta_data' => false,
                                      'format' => 'generic' ),
                               $params );
        $schema = array();

        if ( $this->Schema === false )
        {
            $tableArray = $this->DBInstance->arrayQuery( "SELECT name FROM sqlite_master WHERE type='table'" );

            foreach( $tableArray as $tableNameArray )
            {
                $table_name = current( $tableNameArray );
                if ( !isset( $params['table_include'] ) or
                     ( is_array( $params['table_include'] ) and
                       in_array( $table_name, $params['table_include'] ) ) )
                {
                    $schema_table['name'] = $table_name;
                    $schema_table['fields'] = $this->fetchTableFields( $table_name, $params );
                    $schema_table['indexes'] = $this->fetchTableIndexes( $table_name, $params );

                    $schema[$table_name] = $schema_table;
                }
            }
            $this->transformSchema( $schema, $params['format'] == 'local' );
            ksort( $schema );
            $this->Schema = $schema;
        }
        else
        {
            $this->transformSchema( $this->Schema, $params['format'] == 'local' );
            $schema = $this->Schema;
        }
        return $schema;
    }

    /*!
     \private

     \param table name
     */
    private function fetchTableFields( $table, $params )
    {
        $fields = array();

        $resultArray = $this->DBInstance->arrayQuery( "PRAGMA table_info($table)" );

        foreach( $resultArray as $row )
        {
            $field = array();
            // SQLite PRAGMA table_info returns type in mixed-case (e.g. INTEGER, bigint)
            $typeRaw = strtolower( trim( $row['type'] ) );
            $field['type'] = $this->parseType( $typeRaw, $field['length'] );
            // SQLite 'integer' maps to the schema type 'int'
            if ( $field['type'] === 'integer' )
                $field['type'] = 'int';
            if ( !$field['length'] )
            {
                unset( $field['length'] );
            }
            // SQLite uses 'notnull' (0/1), not MySQL's 'Null' column ('YES'/'NO')
            $field['not_null'] = 0;
            if ( $row['notnull'] == '1' )
            {
                $field['not_null'] = '1';
            }
            // SQLite stores defaults as SQL text expressions: 'DEFAULT NULL' → "NULL",
            // 'DEFAULT \'0\'' → "'0'", no-default auto_increment → PHP null.
            $dfltValue = $row['dflt_value'];
            if ( $dfltValue === 'NULL' )
            {
                $dfltValue = null; // SQL NULL keyword stored as text → PHP null
            }
            elseif ( is_string( $dfltValue ) && strlen( $dfltValue ) >= 2
                     && $dfltValue[0] === "'" && substr( $dfltValue, -1 ) === "'" )
            {
                $dfltValue = substr( $dfltValue, 1, -1 ); // strip surrounding single quotes
            }
            $field['default'] = false;
            if ( !$field['not_null'] )
            {
                if ( $dfltValue === null )
                    $field['default'] = null;
                else
                    $field['default'] = (string)$dfltValue;
            }
            else
            {
                $field['default'] = (string)$dfltValue;
            }

            $numericTypes = array( 'float', 'int', 'bigint', 'decimal', 'double' );
            $blobTypes = array( 'tinytext', 'text', 'mediumtext', 'longtext' );
            $charTypes = array( 'varchar', 'char' );
            if ( in_array( $field['type'], $charTypes ) )
            {
                if ( !$field['not_null'] )
                {
                    if ( $field['default'] === null )
                    {
                        $field['default'] = null;
                    }
                    else if ( $field['default'] === false )
                    {
                        $field['default'] = '';
                    }
                }
            }
            else if ( in_array( $field['type'], $numericTypes ) )
            {
                if ( $field['default'] === false )
                {
                    if ( $field['not_null'] )
                    {
                        $field['default'] = 0;
                    }
                }
                else if ( $field['type'] === 'int' || $field['type'] === 'bigint' )
                {
                    if ( $field['not_null'] or
                         is_numeric( $field['default'] ) )
                        $field['default'] = (int)$field['default'];
                }
                else if ( $field['type'] === 'float' || $field['type'] === 'decimal'
                          || $field['type'] === 'double' || is_numeric( $field['default'] ) )
                {
                    if ( $field['not_null'] or
                         is_numeric( $field['default'] ) )
                        $field['default'] = (float)$field['default'];
                }
            }
            else if ( in_array( $field['type'], $blobTypes ) )
            {
                // We do not want default for blobs.
                $field['default'] = false;
            }

            // Auto-increment: first PK column is bare INTEGER (no width) with notnull=0.
            // This covers both single-column PKs and composite PKs where id is first member.
            if ( $row['pk'] == 1 && !$row['notnull']
                 && strtolower( trim( $row['type'] ) ) === 'integer' )
            {
                unset( $field['length'] );
                $field['not_null'] = 0;
                $field['default'] = false;
                $field['type'] = 'auto_increment';
            }

            if ( !$field['not_null'] )
                unset( $field['not_null'] );

            // SQLite PRAGMA table_info uses 'name', not MySQL's 'Field'
            $fields[$row['name']] = $field;
        }
        ksort( $fields );

        return $fields;
    }

    /*!
     * \private
     */
    private function fetchTableIndexes( $table, $params )
    {
        $indexes = array();

        // Detect primary key columns via PRAGMA table_info (pk > 0 means PK position)
        $tableInfo = $this->DBInstance->arrayQuery( "PRAGMA table_info($table)" );
        $pkColumns = array();
        foreach ( $tableInfo as $row )
        {
            if ( $row['pk'] > 0 )
                $pkColumns[$row['pk']] = $row['name'];
        }
        // Always emit PRIMARY — the distribution .dba defines it for all tables,
        // including auto_increment (where it is implicit in SQLite).
        if ( count( $pkColumns ) > 0 )
        {
            ksort( $pkColumns );
            $indexes['PRIMARY'] = array(
                'type'   => 'primary',
                'fields' => array_values( $pkColumns ),
            );
        }

        // Walk all explicit indexes via PRAGMA index_list
        $indexList = $this->DBInstance->arrayQuery( "PRAGMA index_list($table)" );
        foreach ( $indexList as $idxRow )
        {
            $indexName = $idxRow['name'];
            // Skip SQLite internal autoindex entries created for PK/UNIQUE constraints
            if ( strncmp( $indexName, 'sqlite_autoindex_', 17 ) === 0 )
                continue;

            $isUnique = ( $idxRow['unique'] == 1 );
            $indexFields = array();
            $indexInfo = $this->DBInstance->arrayQuery( "PRAGMA index_info($indexName)" );
            foreach ( $indexInfo as $colRow )
            {
                $indexFields[$colRow['seqno']] = $colRow['name'];
            }
            ksort( $indexFields );

            $indexes[$indexName] = array(
                'type'   => $isUnique ? 'unique' : 'non-unique',
                'fields' => array_values( $indexFields ),
            );
        }

        ksort( $indexes );

        return $indexes;
    }

    function parseType( $type_info, &$length_info )
    {
        preg_match ( "@([a-z ]*)(\(([0-9]*|[0-9]*,[0-9]*)\))?@", $type_info, $matches );
        if ( isset( $matches[3] ) )
        {
            $length_info = $matches[3];
            if ( is_numeric( $length_info ) )
                $length_info = (int)$length_info;
        }
        return $matches[1];
    }

    /*!
     * \private
     */
    function generateAddIndexSql( $table_name, $index_name, $def, $params, $isEmbedded = false )
    {
        $diffFriendly = isset( $params['diff_friendly'] ) ? $params['diff_friendly'] : false;
        $sql = '';

        // Will be set to true when primary key is inside CREATE TABLE
        if ( !$isEmbedded )
        {
            $sql .= "CREATE ";
            $sql .= " ";
        }

        switch ( $def['type'] )
        {
            case 'primary':
            {
                $sql .= 'PRIMARY KEY';
            } break;

            case 'non-unique':
            {
                $sql .= "INDEX $index_name";
            } break;

            case 'unique':
            {
                $sql .= "UNIQUE INDEX $index_name";
            } break;
        }

        if ( !$isEmbedded )
        {
            $sql .= " ON $table_name ";
        }

        $sql .= $diffFriendly ? " (\n    " : " ( " ;
        $fields = $def['fields'];
        $i = 0;
        foreach ( $fields as $fieldDef )
        {
            if ( $i > 0 )
            {
                $sql .= $diffFriendly ? ",\n    " : ', ';
            }
            if ( is_array( $fieldDef ) )
            {
                $sql .= $fieldDef['name'];
                if ( isset( $fieldDef['sqlite:length'] ) )
                {
                    if ( $diffFriendly )
                    {
                        $sql .= "(\n";
                        $sql .= "    " . str_repeat( ' ', strlen( $fieldDef['name'] ) );
                    }
                    else
                    {
                        $sql .= "( ";
                    }
                    $sql .= $fieldDef['sqlite:length'];
                    if ( $diffFriendly )
                    {
                        $sql .= ")";
                    }
                    else
                    {
                        $sql .= " )";
                    }
                }
            }
            else
            {
                $sql .= $fieldDef;
            }
            ++$i;
        }
        $sql .= $diffFriendly ? "\n)" : " )";

        if ( !$isEmbedded )
        {
            return $sql . ";\n";
        }
        return $sql;
    }

    /*!
     * \private
     */
    function generateFieldDef( $field_name, $def, &$skip_primary, $params = null )
    {
        $diffFriendly = isset( $params['diff_friendly'] ) ? $params['diff_friendly'] : false;

        $sql_def = $field_name . ' ';
        $defaultText = "DEFAULT";

        if ( $def['type'] != 'auto_increment' )
        {
            $defList = array();
            $type = $def['type'];
            if ( $type === 'int' )
            {
                $type = 'INTEGER';
            }
            if ( isset( $def['length'] ) )
            {
                $type .= "({$def['length']})";
            }
            $defList[] = $type;
            if ( $type !== 'int' && isset( $def['not_null'] ) && ( $def['not_null'] ) )
            {
                $defList[] = 'NOT NULL';
            }
            if ( array_key_exists( 'default', $def ) )
            {
                if ( $def['default'] === null )
                {
                    $defList[] = "$defaultText NULL";
                }
                else if ( $def['default'] !== false )
                {
                    $defList[] = "$defaultText '{$def['default']}'";
                }
            }
            else if ( $def['type'] == 'varchar' )
            {
                $defList[] = "$defaultText ''";
            }
            $sql_def .= join( $diffFriendly ? "\n    " : " ", $defList );
            $skip_primary = false;
        }
        else
        {
            $incrementText = ""; /*"PRIMARY KEY"*/
            if ( $diffFriendly )
            {
                $sql_def .= "INTEGER\n    $incrementText";
            }
            else
            {
                $sql_def .= "INTEGER $incrementText";
            }
            $skip_primary = true;
        }
        return $sql_def;
    }

    /*!
     \reimp
     \note Calls generateTableSQL() with \a $asArray set to \c false
    */
    function generateTableSchema( $tableName, $table, $params )
    {
        return $this->generateTableSQL( $tableName, $table, $params, false, false );
    }

    /*!
     \reimp
     \note Calls generateTableSQL() with \a $asArray set to \c true
    */
    function generateTableSQLList( $tableName, $table, $params, $separateTypes )
    {
        return $this->generateTableSQL( $tableName, $table, $params, true, $separateTypes );
    }

    /*!
     \private

     \param $asArray If \c true all SQLs are return in an array,
                     if not they are returned as a string.
     \note When returned as array the SQLs will not have a semi-colon to end the statement
    */
    function generateTableSQL( $tableName, $tableDef, $params, $asArray, $separateTypes = false )
    {
        $diffFriendly = isset( $params['diff_friendly'] ) ? $params['diff_friendly'] : false;
        $mysqlCompatible = isset( $params['compatible_sql'] ) ? $params['compatible_sql'] : false;

        if ( $asArray )
        {
            if ( $separateTypes )
            {
                $sqlList = array( 'tables' => array() );
            }
            else
            {
                $sqlList = array();
            }
        }

        $sql = '';
        $skip_pk = false;
        $sql_fields = array();
        $sql .= "CREATE TABLE $tableName (\n";

        $fields = $tableDef['fields'];

        foreach ( $fields as $field_name => $field_def )
        {
            $sql_fields[] = '  ' . self::generateFieldDef( $field_name, $field_def, $skip_pk_flag, $params );
            if ( $skip_pk_flag )
            {
                $skip_pk = true;
            }
        }

        // Make sure the order is as defined by 'offset'
        $indexes = $tableDef['indexes'];

        // We need to add all keys in table definition
        foreach ( $indexes as $index_name => $index_def )
        {
            if ( $index_def['type'] == 'primary' )
            {
                $sql_fields[] = ( $diffFriendly ? '' : '  ' ) . self::generateAddIndexSql( $tableName, $index_name, $index_def, $params, true );
            }
        }
        $sql .= join( ",\n", $sql_fields );
        $sql .= "\n)";

        // Add some extra table options if they are required
        $extraOptions = array();
        if ( isset( $params['table_type'] ) and $params['table_type'] )
        {
            $typeName = $this->tableStorageTypeName( $params['table_type'] );
            if ( $typeName )
            {
                $extraOptions[] = "TYPE=" . $typeName;
            }
        }

        if ( isset( $tableDef['options'] ) )
        {
            foreach( $tableDef['options'] as $optionType => $optionValue )
            {
                $optionText = $this->generateTableOption( $tableName, $tableDef, $optionType, $optionValue, $params );
                if ( $optionText )
                    $extraOptions[] = $optionText;
            }
        }

        if ( count( $extraOptions ) > 0 )
        {
            $sql .= " " . implode( $diffFriendly ? "\n" : " ", $extraOptions );
        }

        if ( $asArray )
        {
            if ( $separateTypes )
            {
                $sqlList['tables'][] = $sql . ";";
            }
            else
            {
                $sqlList[] = $sql . ";";
            }

            foreach ( $indexes as $index_name => $index_def )
            {
                if ( $index_def['type'] != 'primary' )
                {
                    $sqlList[] = ( $diffFriendly ? '' : '  ' ) . self::generateAddIndexSql( $tableName, $index_name, $index_def, $params, false );
                }
            }
        }
        else
        {
            $sql .= ";\n";

            if ( $mysqlCompatible )
            {
                $sql .= "\n\n\n";
            }

            foreach ( $indexes as $index_name => $index_def )
            {
                if ( $index_def['type'] != 'primary' )
                {
                    $sql .= ( $diffFriendly ? '' : '  ' ) . self::generateAddIndexSql( $tableName, $index_name, $index_def, $params, false ) . "\n\n\n";
                }
            }
        }

        return $asArray ? $sqlList : $sql;
    }

    /*!
     * \private
     */
    function generateDropTable( $table, $params )
    {
        return "DROP TABLE $table;\n";
    }

    /*!
     \reimp
    */
    function isMultiInsertSupported()
    {
        return false;
    }

    /*!
     \reimp
    */
    function escapeSQLString( $value )
    {
        if ( $this->DBInstance instanceof eZDBInterface )
        {
            return $this->DBInstance->escapeString( $value );
        }

        return $value;
    }

    /*!
     \reimp
    */
    function schemaType()
    {
        return 'sqlite';
    }

    /*!
     \reimp
    */
    function schemaName()
    {
        return 'SQLite';
    }
}

?>