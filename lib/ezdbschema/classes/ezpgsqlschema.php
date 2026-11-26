<?php
/**
 * File containing the eZPgsqlSchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZPgsqlSchema ezpgsqlschema.php
  \ingroup eZDbSchema
  \brief Handles schemas for PostgreSQL

*/

class eZPgsqlSchema extends eZDBSchemaInterface
{
    const SHOW_TABLES_QUERY = '
        SELECT n.nspname as "Schema",
               c.relname as "Name",
               CASE c.relkind
                    WHEN \'r\' THEN \'table\'
                    WHEN \'v\' THEN \'view\'
                    WHEN \'i\' THEN \'index\'
                    WHEN \'S\' THEN \'sequence\'
                    WHEN \'s\' THEN \'special\'
               END as "Type",
               u.usename as "Owner"
        FROM pg_catalog.pg_class c
             LEFT JOIN pg_catalog.pg_user u ON u.usesysid = c.relowner
             LEFT JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relkind IN (\'r\',\'\')
              AND n.nspname NOT IN (\'pg_catalog\', \'pg_toast\', \'information_schema\', \'crdb_internal\', \'pg_extension\')
              AND pg_catalog.pg_table_is_visible(c.oid)
        ORDER BY 1, 2';

    const FETCH_TABLE_OID_QUERY = '
        SELECT c.oid,
               n.nspname,
               c.relname
        FROM pg_catalog.pg_class c
             LEFT JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
        WHERE pg_catalog.pg_table_is_visible(c.oid)
              AND c.relname ~ \'^<<tablename>>$\'
        ORDER BY 2, 3';

    const FETCH_TABLE_DEF_QUERY = '
        SELECT a.attname,
               pg_catalog.format_type(a.atttypid, a.atttypmod),
               (SELECT substring(pg_get_expr(d.adbin, d.adrelid) for 128) FROM pg_catalog.pg_attrdef d
                WHERE d.adrelid = a.attrelid AND d.adnum = a.attnum AND a.atthasdef) as default,
               a.attnotnull, a.attnum
        FROM pg_catalog.pg_attribute a
        WHERE a.attrelid = \'<<oid>>\' AND a.attnum > 0 AND NOT a.attisdropped
        ORDER BY a.attnum';

    const FETCH_INDEX_DEF_QUERY = '
        SELECT c.relname, i.*
        FROM pg_catalog.pg_index i, pg_catalog.pg_class c
        WHERE indrelid = \'<<oid>>\'
              AND i.indexrelid = c.oid';

    const FETCH_INDEX_COL_NAMES_QUERY = '
        SELECT a.attnum, a.attname, pg_catalog.format_type(a.atttypid, a.atttypmod)
        FROM pg_catalog.pg_attribute a
        WHERE a.attrelid = \'<<indexrelid>>\' AND a.attnum IN (<<attids>>) AND NOT a.attisdropped
        ORDER BY a.attnum';

    function schema( $params = array() )
    {
        $params = array_merge( array( 'meta_data' => false,
                                      'format' => 'generic',
                                      'sort_columns' => true,
                                      'sort_indexes' => true ),
                               $params );
        $schema = array();

        if ( $this->Schema === false )
        {
            $resultArray = $this->tableListRows();

            foreach( $resultArray as $row )
            {
                $table_name = $row['Name'];
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

    /**
     * The tables of the database, rows with "Name": from pg_class, or from information_schema where the database has
     * no real pg_catalog (Spanner, whose PGAdapter emulates pg_class only as far as psql needs it).
     *
     * @return array
     * @since 6.0.15
     */
    protected function tableListRows()
    {
        $profile = $this->wireProfile();
        if ( $profile && $profile['catalog'] === 'information_schema' )
            return (array)$this->DBInstance->arrayQuery( "SELECT table_name AS \"Name\" FROM information_schema.tables"
                                                       . " WHERE table_schema = 'public' AND table_type = 'BASE TABLE' ORDER BY table_name" );
        return (array)$this->DBInstance->arrayQuery( eZPgsqlSchema::SHOW_TABLES_QUERY );
    }

    /**
     * The columns of a table, rows with attname, format_type, default and attnotnull, in their order.
     *
     * @param string $table
     * @return array
     * @since 6.0.15
     */
    protected function tableDefinitionRows( $table )
    {
        $profile = $this->wireProfile();
        if ( $profile && $profile['catalog'] === 'information_schema' )
        {
            $name = $this->DBInstance->escapeString( $table );
            return (array)$this->DBInstance->arrayQuery( "SELECT column_name AS attname, spanner_type AS format_type, column_default AS \"default\","
                                                       . " CASE WHEN is_nullable = 'NO' THEN 't' ELSE 'f' END AS attnotnull"
                                                       . " FROM information_schema.columns WHERE table_schema = 'public' AND table_name = '$name'"
                                                       . " ORDER BY ordinal_position" );
        }
        $resultArray = $this->DBInstance->arrayQuery( str_replace( '<<tablename>>', $table, eZPgsqlSchema::FETCH_TABLE_OID_QUERY ) );
        if ( empty( $resultArray[0]['oid'] ) )
            return array();
        return (array)$this->DBInstance->arrayQuery( str_replace( '<<oid>>', $resultArray[0]['oid'], eZPgsqlSchema::FETCH_TABLE_DEF_QUERY ) );
    }

    /**
     * The indexes of a table from information_schema.indexes and index_columns (Spanner): the primary key as PRIMARY.
     *
     * @param string $table
     * @param array $params
     * @return array
     * @since 6.0.15
     */
    protected function informationSchemaIndexes( $table, $params )
    {
        $name = $this->DBInstance->escapeString( $table );
        $indexes = array();
        $rows = (array)$this->DBInstance->arrayQuery( "SELECT i.index_name, i.index_type, i.is_unique, c.column_name, c.ordinal_position"
                                                    . " FROM information_schema.indexes i JOIN information_schema.index_columns c"
                                                    . " ON c.table_schema = i.table_schema AND c.table_name = i.table_name AND c.index_name = i.index_name"
                                                    . " WHERE i.table_schema = 'public' AND i.table_name = '$name' AND c.ordinal_position IS NOT NULL"
                                                    . " ORDER BY i.index_name, c.ordinal_position" );
        foreach ( $rows as $row )
        {
            $primary = $row['index_type'] === 'PRIMARY_KEY';
            $key = $primary ? 'PRIMARY' : $row['index_name'];
            if ( !isset( $indexes[$key] ) )
                $indexes[$key] = array( 'type' => $primary ? 'primary' : ( self::isTrue( $row['is_unique'] ) || $row['is_unique'] === 'YES' ? 'unique' : 'non-unique' ),
                                        'fields' => array() );
            $indexes[$key]['fields'][] = $row['column_name'];
        }
        if ( !empty( $params['sort_indexes'] ) )
            ksort( $indexes );
        return $indexes;
    }

    /*!
     * \private
     */
    function fetchTableFields( $table, $params )
    {
        $fields = array();

        $resultArray = $this->tableDefinitionRows( $table );
        foreach( $resultArray as $row )
        {
            if ( $this->isEngineOnlyType( $row['format_type'] ) )
            {
                eZDebug::writeNotice( "Column $table.{$row['attname']} ({$row['format_type']}) is PostgreSQL's own and not part of the schema", __METHOD__ );
                continue;
            }
            $field = array();
            $autoinc = false;
            $field['type'] = $this->parseType( $row['format_type'], $field['length'] );
            if ( !$field['length'] )
            {
                unset( $field['length'] );
            }

            $field['not_null'] = 0;
            if ( self::isTrue( $row['attnotnull'] ) )
            {
                $field['not_null'] = '1';
            }

            $field['default'] = false;
            if ( !$field['not_null'] )
            {
                if ( $row['default'] === null )
                    $field['default'] = null;
                else
                    $field['default'] = $this->parseDefault ( $row['default'], $autoinc );
            }
            else
            {
                $field['default'] = (string)$this->parseDefault ( $row['default'], $autoinc );
            }

            $numericTypes = array( 'float', 'int' );
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
                else if ( $field['type'] == 'int' )
                {
                    if ( $field['not_null'] or
                         is_numeric( $field['default'] ) )
                    {
                        $field['default'] = (int)$field['default'];
                    }
                }
                else if ( $field['type'] == 'float' )
                {
                    if ( $field['not_null'] or
                         is_numeric( $field['default'] ) )
                    {
                        $field['default'] = (float)$field['default'];
                    }
                }
            }
            else if ( in_array( $field['type'], $blobTypes ) )
            {
                // We do not want default for blobs.
                $field['default'] = false;
            }

            if ( $autoinc )
            {
                unset( $field['length'] );
                $field['not_null'] = 0;
                $field['default'] = false;
                $field['type'] = 'auto_increment';
            }

            if ( !$field['not_null'] )
                unset( $field['not_null'] );

            $fields[$row['attname']] = $field;
        }
        if ( $params['sort_columns'] )
        {
            ksort( $fields );
        }

        return $fields;
    }

    /*!
     * \private
     */
    function fetchTableIndexes( $table, $params )
    {
        $metaData = false;
        if ( isset( $params['meta_data'] ) )
        {
            $metaData = $params['meta_data'];
        }

        $indexes = array();

        // Spanner: no pg_index; its information_schema names the indexes and their columns
        $profile = $this->wireProfile();
        if ( $profile && $profile['catalog'] === 'information_schema' )
            return $this->informationSchemaIndexes( $table, $params );

        $resultArray = $this->DBInstance->arrayQuery( str_replace( '<<tablename>>', $table, eZPgsqlSchema::FETCH_TABLE_OID_QUERY ) );
        $row = $resultArray[0];
        $oid = $row['oid'];

        $resultArray = $this->DBInstance->arrayQuery( str_replace( '<<oid>>', $oid, eZPgsqlSchema::FETCH_INDEX_DEF_QUERY ) );

        foreach( $resultArray as $row )
        {
            $fields = array();
            $kn = $row['relname'];

            $column_id_array = explode( ' ', $row['indkey'] );
            if ( self::isTrue( $row['indisprimary'] ) )
            {
                // If the name of the key matches our primary key naming standard
                // we change the name to PRIMARY, this makes it 100% similar to
                // primary keys in MySQL
                $correctName = $this->primaryKeyIndexName( $table, $kn, $column_id_array );
                if ( strlen( $correctName ) > 63 )
                {
                    eZDebug::writeError( "The index name '$correctName' (" . strlen( $correctName ) . ") exceeds 63 characters which is the PostgreSQL limit for names" );
                }
                if ( $kn == $correctName )
                {
                    $kn = 'PRIMARY';
                }

                // Extra meta data:
                // Include the name of the index that postgresql will use
                if ( $metaData )
                {
                    $indexes[$kn]['postgresql:name'] = $correctName;
                }

                $indexes[$kn]['type'] = 'primary';
            }
            else
            {
                $indexes[$kn]['type'] = self::isTrue( $row['indisunique'] ) ? 'unique' : 'non-unique';
            }

            /* getting fieldnames requires yet another query and it doesn't return it 'in order' either.
             * grumbl, stupid pgsql :) */
            $att_ids = join( ', ',  $column_id_array );
            $query = str_replace( '<<indexrelid>>', $row['indrelid'], eZPgsqlSchema::FETCH_INDEX_COL_NAMES_QUERY );
            $query = str_replace( '<<attids>>', $att_ids, $query );

            $fieldsArray = $this->DBInstance->arrayQuery( $query );
            $engineOnly = false;
            foreach( $fieldsArray as $fields_row )
            {
                $fields[$fields_row['attnum']] = $fields_row['attname'];
                if ( isset( $fields_row['format_type'] ) && $this->isEngineOnlyType( $fields_row['format_type'] ) )
                {
                    $engineOnly = true;
                }
            }
            // an index over a column the schema leaves out (a GIN index on a tsvector) is left out with it
            if ( $engineOnly )
            {
                unset( $indexes[$kn] );
                continue;
            }
            foreach ( $column_id_array as $rank => $id )
            {
                $indexes[$kn]['fields'][$rank] = $fields[$id];
            }
        }
        if ( $params['sort_indexes'] )
        {
            ksort( $indexes );
        }

        return $indexes;
    }

    /*!
     Whether \a $pgType is a type only PostgreSQL has, which the database-independent schema cannot describe:
     a column of that type is added by engine-specific SQL (the audit log's full-text column is a tsvector),
     so reading the schema leaves it and the indexes over it out instead of failing on it.
    */
    function isEngineOnlyType( $pgType )
    {
        return in_array( strtolower( trim( (string)$pgType ) ), array( 'tsvector', 'tsquery' ), true );
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
        $type = $this->convertToStandardType ( $matches[1], $length_info );
        return $type;
    }

    function isTypeLengthSupported( $pgType )
    {
        switch ( $pgType )
        {
            case 'integer':
            case 'smallint':
            case 'double precision':
            case 'real':
            case 'bigint':
            case 'timestamp':
            case 'date':
            case 'time':
            {
                return false;
            } break;
        }
        return true;
    }

    function convertFromStandardType( $type, &$length )
    {
        switch ( $type )
        {
            case 'char':
                return 'character';

            case 'int':
                return 'integer';

            // Not in the standard set, but used by extension schemas (for
            // flags); MySQL and SQLite take them as they are.
            case 'tinyint':
            case 'smallint':
                return 'smallint';

            case 'mediumint':
                return 'integer';

            case 'bigint':
                return 'bigint';

            case 'varchar':
                return 'character varying';

            case 'longtext':
                return 'text';

            case 'mediumtext':
                return 'text';

            case 'text':
                return 'text';

            case 'float':
                return 'real';

            case 'double':
                return 'double precision';

            case 'decimal':
                return 'numeric';

            // Not in the standard set either: date and time columns of extension or third-party tables in a MySQL
            // database being converted (MySQL's datetime has no time zone, as PostgreSQL's timestamp)
            case 'timestamp':
            case 'datetime':
                return 'timestamp';

            case 'date':
                return 'date';

            case 'time':
                return 'time';

            default:
                eZDebug::writeError( "Unhandled column type: $type", __METHOD__ );
                throw new RuntimeException( "Unhandled column type: $type" );
        }
    }

    /*!
     \private
     The name will consist of the table name and _pkey, since it is only allowed
     to have one primary key pre table that shouldn't be a problem.

     \return A string representing the name of the primary key index.
    */
    function primaryKeyIndexName( $tableName, $indexName, $fields )
    {
        return $tableName . '_pkey';
    }

    function convertToStandardType( $type, &$length )
    {
        switch ( $type )
        {
            case 'bigint':
                $length = 20;
                return 'bigint';

            case 'integer':
                $length = 11;
                return 'int';

            case 'smallint':
                $length = 6;
                return 'int';

            case 'character varying':
                return 'varchar';

            case 'text':
                return 'longtext';

            case 'double precision':
                return 'double';
                
            case 'real':
                return 'float';

            case 'character':
                return 'char';

            case 'numeric':
                return 'decimal';

            case 'timestamp without time zone':
            case 'timestamp':
            // Spanner's only timestamp
            case 'timestamp with time zone':
            case 'timestamptz':
                return 'timestamp';

            case 'date':
                return 'date';

            case 'time without time zone':
            case 'time':
                return 'time';

            default:
                eZDebug::writeError( "Unhandled column type: $type", __METHOD__ );
                throw new RuntimeException( "Unhandled column type: $type" );
        }
    }

    function parseDefault( $default, &$autoinc )
    {
        if ( preg_match( "@^NULL::.*@", $default, $matches ) )
        {
            return null;
        }

        // postgresql 7.x: nextval('ezbasket_id_seq'::text)
        // postgresql 8.x: nextval(('ezbasket_id_seq'::text)::regclass)
        // CockroachDB: nextval('public.ezbasket_id_seq'::REGCLASS); Spanner: nextval('ezbasket_id_seq')
        if ( preg_match( "@^nextval\(\(?'([a-z0-9_]+_id_seq)'::text\)@", $default, $matches ) ||
             preg_match( "@^nextval\('(?:[a-z0-9_]+\.)?([a-z0-9_]+_id_seq)'(?:::regclass)?\)$@i", $default, $matches ) )
        {
            $autoinc = 1;
            return '';
        }

        if ( preg_match( "@^\(?([^()]*)\)?::double precision@", $default, $matches ) )
        {
            return $matches[1];
        }

        if ( preg_match( "@^(.*)::bigint@", $default, $matches ) )
        {
            return $matches[1];
        }

        if ( preg_match( "@^'(.*)'::character\ varying$@", $default, $matches ) )
        {
            return $matches[1];
        }

        if ( preg_match( "@^'(.*)'::[a-zA-Z ]+$@", $default, $matches ) )
        {
            return $matches[1];
        }

        if ( preg_match( "@^'(.*)'$@", $default, $matches ) )
        {
            return $matches[1];
        }

        return $default;
    }

    /**
     * The name an index is created (or dropped) under. PostgreSQL index names
     * are unique per schema, not per table as in MySQL, so extension schema
     * files that reuse a name (contentobject_attribute_id, import_id...) in
     * several tables collided. The schema's own name is kept, so the kernel's
     * indexes and every upgrade script that names them stay as they were; only
     * when an index of that name already exists on another table is it
     * created as "<table>__<name>". Without a connected database (SQL written
     * to a file) the name is kept as it is.
     */
    function physicalIndexName( $table, $index )
    {
        // Only a PostgreSQL connection has these catalogues: a dump for PostgreSQL
        // made on a site that runs another database asked them of that one.
        if ( strpos( $index, $table . '_' ) === 0 || !$this->DBInstance instanceof eZPostgreSQLDB || !$this->DBInstance->isConnected() )
            return $index;

        $owner = $this->DBInstance->arrayQuery(
            "SELECT t.relname AS table_name FROM pg_class i" .
            " JOIN pg_index x ON x.indexrelid = i.oid JOIN pg_class t ON t.oid = x.indrelid" .
            " JOIN pg_namespace n ON n.oid = i.relnamespace" .
            " WHERE i.relkind = 'i' AND n.nspname = current_schema() AND i.relname = '" . $this->DBInstance->escapeString( $index ) . "'" );
        if ( empty( $owner ) || $owner[0]['table_name'] === $table )
            return $index;
        return $table . '__' . $index;
    }

    /*!
     \private
     \param $table_name The table name
     \param $index_name The index name
     \param $def The index structure, see eZDBSchemaInterface for more details
     \param $params An associative array with optional parameters which controls the output of SQLs
     \param $withClosure If \c true then the SQLs will contain semi-colons to close them.
    */
    function generateAddIndexSql( $table_name, $index_name, $def, $params, $withClosure = true )
    {
        $diffFriendly = isset( $params['diff_friendly'] ) ? $params['diff_friendly'] : false;
        $postgresqlCompatible = isset( $params['compatible_sql'] ) ? $params['compatible_sql'] : false;

        $spacing = $postgresqlCompatible ? "\n    " : " ";
        switch ( $def['type'] )
        {
            case 'primary':
            {
                $pkeyName = $this->primaryKeyIndexName( $table_name, $index_name, $def['fields'] );
                if ( strlen( $pkeyName ) > 63 )
                {
                    eZDebug::writeError( "The primary key '$pkeyName' (" . strlen( $pkeyName ) . ") exceeds 63 characters which is the PostgreSQL limit for names" );
                }
                $profile = $this->wireProfile( $params );
                $sql = "ALTER TABLE " . ( ( !$profile || $profile['alter_table_only'] ) ? 'ONLY ' : '' ) . $table_name . $spacing . "ADD CONSTRAINT $pkeyName PRIMARY KEY";
            } break;

            case 'non-unique':
            {
                $sql = "CREATE INDEX " . $this->physicalIndexName( $table_name, $index_name ) . " ON $table_name" . $this->indexMethod( $params );
            } break;

            case 'unique':
            {
                $sql = "CREATE UNIQUE INDEX " . $this->physicalIndexName( $table_name, $index_name ) . " ON $table_name" . $this->indexMethod( $params );
            } break;
        }

        $sql .= ( $diffFriendly ? " (\n    " : ( $postgresqlCompatible ? ' (' : ' ( ' ) );
        $i = 0;
        foreach ( $def['fields'] as $fieldDef )
        {
            if ( $i > 0 )
            {
                $sql .= $diffFriendly ? ",\n    " : ', ';
            }
            if ( is_array( $fieldDef ) )
            {
                $fieldName = $fieldDef['name'];
            }
            else
            {
                $fieldName = $fieldDef;
            }
            if ( in_array( $fieldName, $this->reservedKeywordList() ) )
            {
                $sql .= '"' . $fieldName . '"';
            }
            else
            {
                $sql .= $fieldName;
            }
            ++$i;
        }

        $sql .= ( $diffFriendly ? "\n)" : ( $postgresqlCompatible ? ')' : ' )' ) );

        return $sql . ( $withClosure ? ";\n" : "" );
    }

    /*!
     * \private
     */
    function generateDropIndexSql( $table_name, $index_name, $def, $withClosure = true )
    {
        if ($def['type'] == 'primary' )
        {
            $sql = "ALTER TABLE $table_name DROP CONSTRAINT "
                 . $this->primaryKeyIndexName( $table_name, $index_name, $def['fields'] );
        }
        else
        {
            $sql = "DROP INDEX " . $this->physicalIndexName( $table_name, $index_name );
        }
        return $sql . ( $withClosure ? ";\n" : "" );
    }

    /*!
     * \private
     */
    function generateFieldDef( $table_name, $field_name, $def, $add_default_not_null = true, $params = array() )
    {
        $diffFriendly = isset( $params['diff_friendly'] ) ? $params['diff_friendly'] : false;

        if ( in_array( $field_name, $this->reservedKeywordList() ) )
        {
            $sql_def = '"' . $field_name . '"';
        }
        else
        {
            $sql_def = $field_name;
        }

        $sql_def .= ( $diffFriendly ? "\n    " : " " );
        if ( $def['type'] != 'auto_increment' )
        {
            $pgType = eZPgsqlSchema::convertFromStandardType( $def['type'], $def['length'] );
            // the length belongs to PostgreSQL's type; the database's own name for it (int4, bigint) takes none
            $lengthSupported = eZPgsqlSchema::isTypeLengthSupported( $pgType );
            $profile = $this->wireProfile( $params );
            if ( $profile )
                $pgType = expPostgreSQLWireProfile::mapType( $profile, $pgType );
            $sql_def .= $pgType;
            if ( $lengthSupported and isset( $def['length'] ) && $def['length'] )
            {
                $sql_def .= "({$def['length']})";
            }
            if ( $add_default_not_null )
            {
                $defaultDef = eZPGSQLSchema::generateDefaultDef( false, false, $def, $params );
                if ( $defaultDef )
                {
                    $sql_def .= ( $diffFriendly ? "\n    " : " " );
                    $sql_def .= rtrim( $defaultDef );
                }
                $nullDef = eZPGSQLSchema::generateNullDef( false, false, $def, $params );
                if ( $nullDef )
                {
                    $sql_def .= ( $diffFriendly ? "\n    " : " " );
                    $sql_def .= trim( $nullDef );
                }
            }
        }
        else if ( ( $profile = $this->wireProfile( $params ) ) && $profile['sequences'] === 'bit_reversed' )
        {
            // Spanner: a bit-reversed sequence, 64-bit numbers
            $sql_def .= "bigint" . ( $diffFriendly ? "\n    " : " " ) . "DEFAULT nextval('{$table_name}_{$field_name}_seq')"
                      . ( $diffFriendly ? "\n    " : " " ) . "NOT NULL";
        }
        else
        {
            if ( $diffFriendly )
            {
                $sql_def .= "integer\n    DEFAULT nextval('{$table_name}_{$field_name}_seq'::text)\n    NOT NULL";
            }
            else
            {
                $sql_def .= "integer DEFAULT nextval('{$table_name}_{$field_name}_seq'::text) NOT NULL";
            }
        }
        return $sql_def;
    }

    /*!
     \private
    */
    function generateDefaultDef( $table_name, $field_name, $def, $params )
    {
        $postgresqlCompatible = isset( $params['compatible_sql'] ) ? $params['compatible_sql'] : false;
        // A MySQL "NOT NULL" number or date column without a default reaches the schema as default '' (MySQL's
        // implicit default): PostgreSQL rejects '' as a number or a date, so such a column gets no DEFAULT at all
        if ( isset( $def['default'] ) && $def['default'] === '' &&
             in_array( $def['type'], array( 'int', 'bigint', 'smallint', 'tinyint', 'mediumint', 'decimal', 'float', 'double',
                                            'timestamp', 'datetime', 'date', 'time' ), true ) )
        {
            return '';
        }
        $sql_def = '';
        if ( $table_name and $field_name )
        {
            $sql_def .= "ALTER TABLE $table_name ALTER $field_name SET ";
        }
        if ( array_key_exists( 'default', $def ) and
             $def['default'] !== false )
        {
            if ( $def['default'] === null )
            {
                if ( !$postgresqlCompatible )
                    $sql_def .= "DEFAULT NULL ";
            }
            else if ( $def['default'] !== false )
            {
                if ( $def['type'] == 'int' )
                {
                    $sql_def .= "DEFAULT {$def['default']} ";
                }
                else if ( $def['type'] == 'float' )
                {
                    $sql_def .= "DEFAULT {$def['default']}::real ";
                }
                else if ( $def['type'] == 'double' )
                {
                    $sql_def .= "DEFAULT {$def['default']}::double precision ";
                }
                else if ( $def['type'] == 'varchar' )
                {
                    $sql_def .= "DEFAULT '{$def['default']}'::character varying ";
                }
                else if ( $def['type'] == 'char' )
                {
                    $sql_def .= "DEFAULT '{$def['default']}'::bpchar ";
                }
                else
                {
                    $sql_def .= "DEFAULT '{$def['default']}' ";
                }
            }
        }
        else if ( $table_name and $field_name )
        {
            return false;
        }
        return $sql_def;
    }

    /*!
     \private
    */
    function generateNullDef( $table_name, $field_name, $def, $params )
    {
        $sql_def = '';
        if ( $table_name and $field_name )
        {
            $sql_def .= "ALTER TABLE $table_name ALTER $field_name SET ";
        }
        if ( isset( $def['not_null'] ) && ( $def['not_null'] ) )
        {
            $sql_def .= 'NOT NULL ';
        }
        else if ( $table_name and $field_name )
        {
            return false;
        }
        return $sql_def;
    }

    /*!
     * \private
     */
    function generateAddFieldSql( $table_name, $field_name, $def, $params )
    {
        $sql = "ALTER TABLE $table_name ADD COLUMN ";
        $sql .= eZPgsqlSchema::generateFieldDef( $table_name, $field_name, $def, false, $params ) . ";\n";
        $defaultSQL = eZPGSQLSchema::generateDefaultDef( $table_name, $field_name, $def, $params );
        if ( $defaultSQL )
            $sql .= $defaultSQL . ";\n";
        $nullSQL = eZPGSQLSchema::generateNullDef( $table_name, $field_name, $def, $params );
        if ( $nullSQL )
        {
            // SET DEFAULT does not fill the rows the table already has: give them the default first, or
            // SET NOT NULL fails with "column ... contains null values" on any table that is not empty
            $fill = $this->generateFillExistingRowsSql( $table_name, $field_name, $def, $params );
            if ( $fill )
                $sql .= $fill . ";\n";
            $sql .= $nullSQL . ";\n";
        }
        $sql .= "\n";
        return $sql;
    }

    /**
     * The UPDATE that gives the rows a table already has the default of a column just added to it, so the
     * column can then be made NOT NULL. False when the column has no default other than NULL.
     *
     * @return string|false
     */
    function generateFillExistingRowsSql( $table_name, $field_name, $def, $params )
    {
        if ( !array_key_exists( 'default', $def ) || $def['default'] === false || $def['default'] === null )
            return false;
        $value = trim( (string)eZPgsqlSchema::generateDefaultDef( false, false, $def, $params ) );
        if ( strncmp( $value, 'DEFAULT ', 8 ) !== 0 )
            return false;
        $value = trim( substr( $value, 8 ) );
        $column = in_array( $field_name, $this->reservedKeywordList() ) ? '"' . $field_name . '"' : $field_name;
        return "UPDATE $table_name SET $column = $value WHERE $column IS NULL";
    }

    /*!
     \private
    */
    function generateDropFieldSql( $table_name, $field_name, $params )
    {
        if ( in_array( $field_name, $this->reservedKeywordList() ) )
        {
            $field_name = '"' . $field_name . '"';
        }

        $sql = "ALTER TABLE $table_name DROP COLUMN $field_name";

        return $sql . ";\n";
    }

    /*!
     * \private
     */
    function generateAlterFieldSql( $table_name, $field_name, $def, $params )
    {
        $sql = "ALTER TABLE $table_name RENAME COLUMN $field_name TO " . $field_name . "_tmp;\n";
        $sql .= "ALTER TABLE $table_name ADD COLUMN ";
        $sql .= eZPgsqlSchema::generateFieldDef( $table_name, $field_name, $def, false, $params ) . ";\n";
        $defaultSQL = eZPGSQLSchema::generateDefaultDef( $table_name, $field_name, $def, $params );
        if ( $defaultSQL )
            $sql .= $defaultSQL . ";\n";
        $nullSQL = eZPGSQLSchema::generateNullDef( $table_name, $field_name, $def, $params );
        if ( $nullSQL )
            $sql .= $nullSQL . ";\n";
        $sql .= "UPDATE $table_name SET $field_name=" . $field_name . "_tmp;\n";
        $sql .= "ALTER TABLE $table_name DROP COLUMN " . $field_name . "_tmp;\n\n";
        return $sql;
    }

    /**
     * The profile of the database the DDL is for (expPostgreSQLWireProfile): the connected driver's, or the one
     * $params['wire_profile'] names (an SQL file written without a connection); null for PostgreSQL itself.
     *
     * @param array $params
     * @return array|null
     * @since 6.0.15
     */
    function wireProfile( $params = array() )
    {
        if ( is_array( $params ) && !empty( $params['wire_profile'] ) )
        {
            $profile = expPostgreSQLWireProfile::get( $params['wire_profile'] );
            return ( $profile && $profile['name'] !== 'postgresql' ) ? $profile : null;
        }
        if ( $this->DBInstance instanceof expPostgreSQLWireDB )
        {
            $profile = $this->DBInstance->wireProfile();
            return $profile['name'] !== 'postgresql' ? $profile : null;
        }
        return null;
    }

    /**
     * " USING btree", or nothing where the database has an access method of its own (YugabyteDB's lsm) or no
     * USING clause (Spanner).
     *
     * @param array $params
     * @return string
     */
    protected function indexMethod( $params )
    {
        $profile = $this->wireProfile( $params );
        $method = $profile ? $profile['index_method'] : 'btree';
        return $method !== '' ? " USING $method" : '';
    }

    /**
     * The column names of an index definition, a reserved word in quotes.
     *
     * @param array $fields
     * @return string[]
     */
    protected function indexFieldNames( array $fields )
    {
        $names = array();
        foreach ( $fields as $fieldDef )
        {
            $name = is_array( $fieldDef ) ? $fieldDef['name'] : $fieldDef;
            $names[] = in_array( $name, $this->reservedKeywordList() ) ? '"' . $name . '"' : $name;
        }
        return $names;
    }

    function generateTableSchema( $table, $table_def, $params )
    {
        $arrays = $this->generateTableArrays( $table, $table_def, $params, true );
        return ( join( "\n\n", $arrays['sequences'] ) . "\n" .
                 join( "\n\n", $arrays['tables'] ) . "\n" .
                 join( "\n\n", $arrays['indexes'] ) . "\n" .
                 join( "\n\n", $arrays['constraints'] ) . "\n" );
    }

    function generateTableSQLList( $table, $table_def, $params, $separateTypes )
    {
        $arrays = $this->generateTableArrays( $table, $table_def, $params, false );

        // If we have to separate the types the current array is sufficient
        if ( $separateTypes )
            return $arrays;
        return array_merge( $arrays['sequences'],
                            $arrays['tables'],
                            $arrays['indexes'],
                            $arrays['constraints'] );
    }

    /*!
     \private
     \param $table The table name
     \param $table_def The table structure, see eZDBSchemaInterface for more details
     \param $params An associative array with optional parameters which controls the output of SQLs
     \param $withClosure If \c true then the SQLs will contain semi-colons to close them.
    */
    function generateTableArrays( $table, $table_def, $params, $withClosure = true )
    {
        $diffFriendly = isset( $params['diff_friendly'] ) ? $params['diff_friendly'] : false;
        $postgresqlCompatible = isset( $params['compatible_sql'] ) ? $params['compatible_sql'] : false;

        $arrays = array( 'sequences' => array(),
                         'tables' => array(),
                         'indexes' => array(),
                         'constraints' => array() );

        $sql_fields = array();

        $spacing = $postgresqlCompatible ? '    ' : '  ';

        // First we need to check if we use auto increment fields as
        // sequences need to exist before we use them
        foreach ( $table_def['fields'] as $field_name => $field_def )
        {
            if ( $field_def['type'] == 'auto_increment' && ( $profile = $this->wireProfile( $params ) ) && $profile['sequences'] === 'bit_reversed' )
            {
                $arrays['sequences'][] = "CREATE SEQUENCE IF NOT EXISTS {$table}_{$field_name}_seq BIT_REVERSED_POSITIVE" . ( $withClosure ? ';' : '' );
            }
            else if ( $field_def['type'] == 'auto_increment' )
            {
                $sequenceFields = array( "CREATE SEQUENCE IF NOT EXISTS {$table}_{$field_name}_seq",
                                         "START 1",
                                         "INCREMENT 1",
                                         "MAXVALUE 9223372036854775807",
                                         "MINVALUE 1",
                                         "CACHE 1" );
                $arrays['sequences'][] = join( "\n$spacing", $sequenceFields ) . ( $withClosure ? ';' : '' );
            }
        }

        $sql = "CREATE TABLE IF NOT EXISTS $table (\n";
        $fields = $table_def['fields'];
        foreach ( $fields as $field_name => $field_def )
        {
            $sql_fields[] = $spacing . eZPgsqlSchema::generateFieldDef( $table, $field_name, $field_def, true, $params );
        }
        // CockroachDB, YugabyteDB and Spanner: the primary key belongs in CREATE TABLE. Added afterwards it replaces
        // CockroachDB's hidden rowid key, rewrites a YugabyteDB table, and Spanner does not take it at all.
        $inlinePrimaryKey = ( $profile = $this->wireProfile( $params ) ) && $profile['inline_primary_key'];
        if ( $inlinePrimaryKey )
        {
            foreach ( $table_def['indexes'] as $index_name => $index_def )
            {
                // Spanner takes no name for the key
                if ( $index_def['type'] == 'primary' )
                    $sql_fields[] = $spacing . ( $profile['named_primary_key'] ? 'CONSTRAINT ' . $this->primaryKeyIndexName( $table, $index_name, $index_def['fields'] ) . ' ' : '' )
                                  . 'PRIMARY KEY ( ' . implode( ', ', $this->indexFieldNames( $index_def['fields'] ) ) . ' )';
            }
        }
        $sql .= join( ",\n", $sql_fields ) . ( $withClosure ? "\n);" : "\n)" );
        $arrays['tables'][] = $sql;

        foreach ( $table_def['indexes'] as $index_name => $index_def )
        {
            if ( $index_def['type'] != 'primary' )
            {
                $arrays['indexes'][] = eZPgsqlSchema::generateAddIndexSql( $table, $index_name, $index_def, $params, $withClosure );
            }
        }
        foreach ( $table_def['indexes'] as $index_name => $index_def )
        {
            if ( $index_def['type'] == 'primary' && !$inlinePrimaryKey )
            {
                $arrays['constraints'][] = eZPgsqlSchema::generateAddIndexSql( $table, $index_name, $index_def, $params, $withClosure );
            }
        }

        return $arrays;
    }


    /*!

     This calls eZDBSchemaInterface::generateTableInsertSQLList() and adds a setval SQL if
     the table has auto increments.
    */
    function generateTableInsertSQLList( $tableName, $tableDef, $dataEntries, $params, $withClosure = true )
    {
        $sqlList = eZDBSchemaInterface::generateTableInsertSQLList( $tableName, $tableDef, $dataEntries, $params, $withClosure );

        $profile = $this->wireProfile( $params );
        foreach ( $tableDef['fields'] as $fieldName => $fieldDef )
        {
            // a bit-reversed sequence (Spanner) has no order to move past the rows
            if ( $fieldDef['type'] == 'auto_increment' && ( !$profile || $profile['sequences'] === 'sql' ) )
            {
                $sql = "SELECT setval('" . $tableName . "_{$fieldName}_seq',max(" . $fieldName . ")+1) FROM " . $tableName;
                if ( $withClosure )
                    $sql .= ";";
                $sqlList[] = $sql;
            }
        }
        return $sqlList;
    }

    function generateSchemaFile( $schema, $params = array() )
    {
        $sql = '';
        $postgresqlCompatible = isset( $params['compatible_sql'] ) ? $params['compatible_sql'] : false;

        $i = 0;
        $allArrays = array( 'sequences' => array(),
                            'tables' => array(),
                            'indexes' => array(),
                            'constraints' => array() );

        foreach ( $schema as $table => $tableDef )
        {
            // Skip the info structure, this is not a table
            if ( $table == '_info' )
                continue;

            $arrays = $this->generateTableArrays( $table, $tableDef, $params, true );
            if ( $postgresqlCompatible )
            {
                $allArrays['sequences'] = array_merge( $allArrays['sequences'],
                                                       $arrays['sequences'] );
                $allArrays['tables'] = array_merge( $allArrays['tables'],
                                                    $arrays['tables'] );
                $allArrays['indexes'] = array_merge( $allArrays['indexes'],
                                                     $arrays['indexes'] );
                $allArrays['constraints'] = array_merge( $allArrays['constraints'],
                                                         $arrays['constraints'] );
            }
            else
            {
                if ( $i > 0 )
                    $sql .= "\n\n";
                ++$i;

                $sql .= ( join( "\n", $arrays['sequences'] ) . "\n" .
                          join( "\n", $arrays['tables'] ) . "\n" .
                          join( "\n", $arrays['indexes'] ) . "\n" .
                          join( "\n", $arrays['constraints'] ) . "\n" );
            }
        }

        if ( $postgresqlCompatible )
        {
            $sql = ( str_repeat( "\n", 11 ) .
                     join( str_repeat( "\n", 8 ), $allArrays['sequences'] ) . str_repeat( "\n", 8 ) .
                     join( str_repeat( "\n", 8 ), $allArrays['tables'] ) . str_repeat( "\n", 8 ) .
                     join( str_repeat( "\n", 7 ), $allArrays['indexes'] ) . str_repeat( "\n", 8 ) .
                     join( str_repeat( "\n", 7 ), $allArrays['constraints'] ) . str_repeat( "\n", 8 ) );
        }

        return $sql;
    }

    /*!
     * \private
     */
    function generateDropTable( $table )
    {
        return "DROP TABLE $table;\n";
    }

    function escapeSQLString( $value )
    {
        if ( $this->DBInstance instanceof eZPostgreSQLDB && $this->DBInstance->isConnected() )
        {
            return $this->DBInstance->escapeString( $value );
        }

        // No connection (an SQL dump made from .dba files): standard SQL
        // doubles a quote, and with standard_conforming_strings (on by default
        // since PostgreSQL 9.1) a backslash is itself. pg_escape_string()
        // without a connection is deprecated since PHP 8.1 and fails when no
        // connection was ever opened. NUL cannot be stored in text.
        return str_replace( array( "\0", "'" ), array( '', "''" ), (string)$value );
    }

    function schemaType()
    {
        return 'postgresql';
    }

    function schemaName()
    {
        return 'PostgreSQL';
    }

    /**
     * A reserved word in double quotes, as the table definitions already write it ("group" in a third-party
     * permissions table); every other name stays as it is (unquoted names fold to lower case, as before).
     *
     * @param string $name
     * @return string
     */
    function quoteSchemaIdentifier( $name )
    {
        return in_array( $name, $this->reservedKeywordList() ) ? '"' . str_replace( '"', '""', $name ) . '"' : $name;
    }

    /** INSERT column lists quote reserved words the same way */
    function insertColumnName( $name )
    {
        return $this->quoteSchemaIdentifier( $name );
    }

    /*!
     \return An array with keywords that are reserved by PostgreSQL.
    */
    function reservedKeywordList()
    {
        return array( 'abort',
                      'absolute',
                      'access',
                      'action',
                      'add',
                      'after',
                      'aggregate',
                      'all',
                      'alter',
                      'analyse',
                      'analyze',
                      'and',
                      'any',
                      'as',
                      'asc',
                      'assertion',
                      'assignment',
                      'at',
                      'authorization',
                      'backward',
                      'before',
                      'begin',
                      'between',
                      'bigint',
                      'binary',
                      'bit',
                      'boolean',
                      'both',
                      'by',
                      'cache',
                      'called',
                      'cascade',
                      'case',
                      'cast',
                      'chain',
                      'char',
                      'character',
                      'characteristics',
                      'check',
                      'checkpoint',
                      'class',
                      'close',
                      'cluster',
                      'coalesce',
                      'collate',
                      'column',
                      'comment',
                      'commit',
                      'committed',
                      'constraint',
                      'constraints',
                      'conversion',
                      'convert',
                      'copy',
                      'create',
                      'createdb',
                      'createuser',
                      'cross',
                      'current_date',
                      'current_time',
                      'current_timestamp',
                      'current_user',
                      'cursor',
                      'cycle',
                      'database',
                      'day',
                      'deallocate',
                      'dec',
                      'decimal',
                      'declare',
                      'default',
                      'deferrable',
                      'deferred',
                      'definer',
                      'delete',
                      'delimiter',
                      'delimiters',
                      'desc',
                      'distinct',
                      'do',
                      'domain',
                      'double',
                      'drop',
                      'each',
                      'else',
                      'encoding',
                      'encrypted',
                      'end',
                      'escape',
                      'except',
                      'exclusive',
                      'execute',
                      'exists',
                      'explain',
                      'external',
                      'extract',
                      'false',
                      'fetch',
                      'float',
                      'for',
                      'force',
                      'foreign',
                      'forward',
                      'freeze',
                      'from',
                      'full',
                      'function',
                      'get',
                      'global',
                      'grant',
                      'group',
                      'handler',
                      'having',
                      'hour',
                      'ilike',
                      'immediate',
                      'immutable',
                      'implicit',
                      'in',
                      'increment',
                      'index',
                      'inherits',
                      'initially',
                      'inner',
                      'inout',
                      'input',
                      'insensitive',
                      'insert',
                      'instead',
                      'int',
                      'integer',
                      'intersect',
                      'interval',
                      'into',
                      'invoker',
                      'is',
                      'isnull',
                      'isolation',
                      'join',
                      'key',
                      'lancompiler',
                      'language',
                      'leading',
                      'left',
                      'level',
                      'like',
                      'limit',
                      'listen',
                      'load',
                      'local',
                      'localtime',
                      'localtimestamp',
                      'location',
                      'lock',
                      'match',
                      'maxvalue',
                      'minute',
                      'minvalue',
                      'mode',
                      'month',
                      'move',
                      'names',
                      'national',
                      'natural',
                      'nchar',
                      'new',
                      'next',
                      'no',
                      'nocreatedb',
                      'nocreateuser',
                      'none',
                      'not',
                      'nothing',
                      'notify',
                      'notnull',
                      'null',
                      'nullif',
                      'numeric',
                      'of',
                      'off',
                      'offset',
                      'oids',
                      'old',
                      'on',
                      'only',
                      'operator',
                      'option',
                      'or',
                      'order',
                      'out',
                      'outer',
                      'overlaps',
                      'overlay',
                      'owner',
                      'partial',
                      'password',
                      'path',
                      'pendant',
                      'placing',
                      'position',
                      'precision',
                      'prepare',
                      'primary',
                      'prior',
                      'privileges',
                      'procedural',
                      'procedure',
                      'read',
                      'real',
                      'recheck',
                      'references',
                      'reindex',
                      'relative',
                      'rename',
                      'replace',
                      'reset',
                      'restrict',
                      'returns',
                      'revoke',
                      'right',
                      'rollback',
                      'row',
                      'rule',
                      'schema',
                      'scroll',
                      'second',
                      'security',
                      'select',
                      'sequence',
                      'serializable',
                      'session',
                      'session_user',
                      'set',
                      'setof',
                      'share',
                      'show',
                      'similar',
                      'simple',
                      'smallint',
                      'some',
                      'stable',
                      'start',
                      'statement',
                      'statistics',
                      'stdin',
                      'stdout',
                      'storage',
                      'strict',
                      'substring',
                      'sysid',
                      'table',
                      'temp',
                      'template',
                      'temporary',
                      'then',
                      'time',
                      'timestamp',
                      'to',
                      'toast',
                      'trailing',
                      'transaction',
                      'treat',
                      'trigger',
                      'trim',
                      'true',
                      'truncate',
                      'trusted',
                      'type',
                      'unencrypted',
                      'union',
                      'unique',
                      'unknown',
                      'unlisten',
                      'until',
                      'update',
                      'usage',
                      'user',
                      'using',
                      'vacuum',
                      'valid',
                      'validator',
                      'values',
                      'varchar',
                      'varying',
                      'verbose',
                      'version',
                      'view',
                      'volatile',
                      'when',
                      'where',
                      'with',
                      'without',
                      'work',
                      'write',
                      'year',
                      'zone' );
    }

    /**
     * A boolean of the catalogue as true or false: 't' from pgsql, '1' (or -1) from psqlODBC through expODBCDB.
     *
     * @param mixed $value
     * @return bool
     */
    protected static function isTrue( $value )
    {
        return $value === true || in_array( strtolower( trim( (string)$value ) ), array( 't', 'true', '1', '-1' ), true );
    }
}
?>
