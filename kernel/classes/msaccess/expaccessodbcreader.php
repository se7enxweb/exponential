<?php
/**
 * File containing the expAccessODBCReader class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads an Access file through ODBC (expMSAccessDB): on Windows Microsoft's Access Database Engine driver ("Microsoft
 * Access Driver (*.mdb, *.accdb)", x64 for a 64-bit PHP), opened read only. Tables come from SQLTables, columns from
 * SQLColumns and indexes from SQLStatistics (PHP's odbc extension), as Access's own catalogue MSysObjects is not
 * readable through ODBC.
 *
 * Settings: Driver, Layer (auto, odbc, pdo_odbc), Password (a database password).
 *
 * @since 6.0.15
 * @package kernel
 */
class expAccessODBCReader extends expAccessReader
{
    /** @var expMSAccessDB */
    protected $db;

    /**
     * @param string $file
     * @param array $settings Driver, Layer, Password
     * @throws RuntimeException when the file cannot be opened
     */
    public function __construct( $file, array $settings = array() )
    {
        parent::__construct( $file );
        $access = array( 'ReadOnly' => 'enabled' );
        foreach ( array( 'Driver', 'Layer', 'ConnectionString' ) as $name )
            if ( isset( $settings[$name] ) && $settings[$name] !== '' )
                $access[$name] = $settings[$name];
        try
        {
            $this->db = new expMSAccessDB( array( 'database' => realpath( $this->file ) ?: $this->file, 'user' => '',
                                                  'password' => isset( $settings['Password'] ) ? (string)$settings['Password'] : '',
                                                  'msaccess' => $access, 'connect_retries' => 0 ) );
        }
        catch ( Throwable $e )
        {
            throw new RuntimeException( 'The Access file could not be opened through ODBC: ' . $e->getMessage() );
        }
        $this->db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
    }

    public function name()
    {
        return 'odbc';
    }

    /** @return expMSAccessDB */
    public function db()
    {
        return $this->db;
    }

    public function format()
    {
        return strtolower( (string)pathinfo( $this->file, PATHINFO_EXTENSION ) ) === 'accdb' ? 'ACE' : 'JET';
    }

    public function tables()
    {
        return array_keys( $this->db->eZTableList() );
    }

    public function table( $table )
    {
        $description = array( 'fields' => array(), 'primary' => array(), 'indexes' => array() );
        $columns = $this->db->tableColumns( $table );
        if ( $columns === null )
            $columns = $this->columnsFromResult( $table );
        foreach ( $columns as $name => $column )
        {
            $type = self::type( $column['type'] );
            $description['fields'][$name] = array( 'type' => $type, 'length' => (int)$column['size'], 'scale' => (int)$column['digits'],
                                                   'not_null' => $type === 'counter' || !$column['nullable'], 'auto' => $type === 'counter' );
        }
        $pdo = $this->db->pdo();
        if ( $pdo instanceof expODBCNativeConnection )
        {
            $indexes = array();
            foreach ( $pdo->statistics( $table ) as $row )
            {
                $row = array_change_key_case( $row, CASE_UPPER );
                if ( empty( $row['INDEX_NAME'] ) || empty( $row['COLUMN_NAME'] ) )
                    continue;
                $name = (string)$row['INDEX_NAME'];
                if ( !isset( $indexes[$name] ) )
                    $indexes[$name] = array( 'unique' => isset( $row['NON_UNIQUE'] ) && (int)$row['NON_UNIQUE'] === 0, 'fields' => array() );
                $indexes[$name]['fields'][(int)$row['ORDINAL_POSITION']] = (string)$row['COLUMN_NAME'];
            }
            foreach ( $indexes as $name => $index )
            {
                ksort( $index['fields'] );
                $index['fields'] = array_values( $index['fields'] );
                if ( strcasecmp( $name, 'PrimaryKey' ) === 0 )
                    $description['primary'] = $index['fields'];
                else
                    $description['indexes'][$name] = $index;
            }
        }
        return $description;
    }

    /**
     * The columns of a table from a result's metadata (PDO_ODBC, which has no SQLColumns).
     *
     * @param string $table
     * @return array
     */
    protected function columnsFromResult( $table )
    {
        $columns = array();
        $statement = $this->db->pdo()->query( 'SELECT TOP 1 * FROM ' . expMSAccessDialect::quoteName( $table ) );
        for ( $i = 0; $i < $statement->columnCount(); $i++ )
        {
            $meta = $statement->getColumnMeta( $i );
            if ( $meta === false )
                continue;
            $columns[$meta['name']] = array( 'type' => strtoupper( isset( $meta['native_type'] ) ? (string)$meta['native_type'] : 'LONGCHAR' ),
                                             'size' => isset( $meta['len'] ) ? (int)$meta['len'] : 0,
                                             'digits' => isset( $meta['precision'] ) ? (int)$meta['precision'] : 0, 'nullable' => true );
        }
        $statement->closeCursor();
        return $columns;
    }

    /**
     * The reader's type of an ODBC type name as Access's driver reports it.
     *
     * @param string $typeName
     * @return string
     */
    public static function type( $typeName )
    {
        switch ( strtoupper( trim( (string)$typeName ) ) )
        {
            case 'COUNTER':
            case 'AUTOINCREMENT':
                return 'counter';
            case 'BYTE':
            case 'TINYINT':
                return 'byte';
            case 'SMALLINT':
            case 'SHORT':
                return 'smallint';
            case 'INTEGER':
            case 'INT':
            case 'LONG':
                return 'integer';
            case 'BIGINT':
                return 'bigint';
            case 'BIT':
            case 'YESNO':
                return 'boolean';
            case 'REAL':
            case 'SINGLE':
                return 'single';
            case 'DOUBLE':
            case 'FLOAT':
                return 'double';
            case 'CURRENCY':
            case 'MONEY':
                return 'currency';
            case 'DECIMAL':
            case 'NUMERIC':
                return 'decimal';
            case 'DATETIME':
            case 'DATE':
            case 'TIMESTAMP':
            case 'TIME':
                return 'datetime';
            case 'VARCHAR':
            case 'CHAR':
            case 'TEXT':
            case 'WVARCHAR':
            case 'WCHAR':
                return 'text';
            case 'GUID':
                return 'guid';
            case 'LONGBINARY':
            case 'VARBINARY':
            case 'BINARY':
            case 'OLEOBJECT':
                return 'binary';
        }
        return 'memo';
    }

    public function rows( $table, callable $each )
    {
        $fields = $this->table( $table )['fields'];
        $pdo = $this->db->pdo();
        $statement = $pdo->query( 'SELECT * FROM ' . expMSAccessDialect::quoteName( $table ) );
        $count = 0;
        while ( ( $data = $statement->fetch( PDO::FETCH_ASSOC ) ) !== false )
        {
            $row = array();
            foreach ( $fields as $name => $field )
            {
                $value = array_key_exists( $name, $data ) ? $data[$name] : null;
                if ( is_resource( $value ) )
                    $value = stream_get_contents( $value );
                if ( $value !== null )
                {
                    if ( $field['type'] === 'binary' )
                        $value = new expAccessBinary( $value );
                    elseif ( $field['type'] === 'datetime' )
                        $value = self::date( $value );
                    elseif ( $field['type'] === 'boolean' )
                        $value = $value && $value !== '0' ? 1 : 0;
                }
                $row[$name] = $value;
            }
            $each( $row );
            $count++;
        }
        $statement->closeCursor();
        return $count;
    }
}
?>
