<?php
/**
 * File containing the expMSAccessDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * Microsoft Access (.accdb, .mdb) through ODBC: on Windows Microsoft's Access Database Engine (ACE) ODBC driver,
 * x64, which reads and writes. On Linux the MDBTools ODBC driver connects but cannot fetch rows through PDO_ODBC and
 * parses little SQL: there Access files are read by the import (expAccessMdbToolsReader) instead.
 * It is expODBCDB with the msaccess dialect (expMSAccessDialect), and databaseName() is "MSAccess".
 *
 *   [DatabaseSettings]
 *   DatabaseImplementation=msaccess
 *   Database=C:\data\shop.accdb
 *   [MSAccessSettings]
 *   Driver=Microsoft Access Driver (*.mdb, *.accdb)
 *   ExtendedAnsiSQL=enabled
 *
 * [MSAccessSettings]: Driver (empty: ACE on Windows, MDBTools elsewhere), ExtendedAnsiSQL (ANSI-92 mode: DEFAULT,
 * DECIMAL, BAND/BOR; enabled by default), ReadOnly, Exclusive, ConnectionString (more keywords), Layer (auto, odbc,
 * pdo_odbc). Database is the file; a workgroup password goes in Password.
 *
 * Access is a file database for one desktop: it serializes writers with page locks in a lock file next to the
 * database, holds 2 GB at most, and has no OFFSET, upsert, GROUP_CONCAT or MD5. It is meant for importing data and
 * for small read-mostly data tables, not for an Exponential site (doc/eng-us/features/6.0/ms-access-support.md).
 *
 * @since 6.0.15
 * @package lib
 */
class expMSAccessDB extends expODBCDB
{
    /** The [MSAccessSettings] and their defaults */
    const DEFAULT_SETTINGS = array(
        'Dialect' => 'msaccess',
        'Driver' => '',
        'DataSourceName' => '',
        'ConnectionString' => '',
        'Layer' => 'auto',
        'ExtendedAnsiSQL' => 'enabled',
        'ReadOnly' => 'disabled',
        'Exclusive' => 'disabled',
    );

    /**
     * @param array $parameters the eZDB parameters ('database' is the file) plus 'msaccess' => array( [MSAccessSettings] )
     * @throws eZDBNoConnectionException
     */
    public function __construct( $parameters )
    {
        $parameters = (array)$parameters;
        $overrides = isset( $parameters['msaccess'] ) && is_array( $parameters['msaccess'] ) ? $parameters['msaccess'] : array();
        if ( isset( $parameters['odbc'] ) && is_array( $parameters['odbc'] ) )
            $overrides = $parameters['odbc'] + $overrides;
        $overrides['Dialect'] = 'msaccess';
        $parameters['odbc'] = $overrides;
        parent::__construct( $parameters );
    }

    /** @return string */
    public static function settingsGroup()
    {
        return 'MSAccessSettings';
    }

    /**
     * The connection string: the file as DBQ, then the switches of ACE's driver (ExtendedAnsiSQL, ReadOnly,
     * Exclusive) unless the driver is MDBTools, which knows none of them.
     *
     * @return string
     */
    public static function connectionString( $dialect, array $settings, $server, $port, $database )
    {
        $extra = array();
        $driver = isset( $settings['Driver'] ) && trim( (string)$settings['Driver'] ) !== '' ? trim( (string)$settings['Driver'] ) : static::defaultDriver( 'msaccess' );
        if ( stripos( $driver, 'mdbtools' ) === false )
        {
            if ( self::on( isset( $settings['ExtendedAnsiSQL'] ) ? $settings['ExtendedAnsiSQL'] : false ) )
                $extra[] = 'ExtendedAnsiSQL=1';
            if ( self::on( isset( $settings['ReadOnly'] ) ? $settings['ReadOnly'] : false ) )
                $extra[] = 'ReadOnly=1';
            if ( self::on( isset( $settings['Exclusive'] ) ? $settings['Exclusive'] : false ) )
                $extra[] = 'Exclusive=1';
        }
        $given = isset( $settings['ConnectionString'] ) ? trim( (string)$settings['ConnectionString'], " ;\t\r\n" ) : '';
        $settings['ConnectionString'] = implode( ';', array_filter( array_merge( $extra, array( $given ) ), 'strlen' ) );
        return parent::connectionString( 'msaccess', $settings, $server, $port, $database );
    }

    /**
     * @param mixed $value enabled, true, 1, yes
     * @return bool
     */
    protected static function on( $value )
    {
        return in_array( strtolower( trim( (string)$value ) ), array( 'enabled', 'true', '1', 'yes', 'on' ), true );
    }

    /** @return bool whether the connection goes through Linux's read-only MDBTools driver */
    public function isMDBTools()
    {
        return stripos( $this->ConnectionString, 'mdbtools' ) !== false;
    }

    /**
     * The columns of a table through SQLColumns (the odbc layer): name => array( type, size, digits, nullable ).
     *
     * @param string $table
     * @return array|null null when the layer cannot tell
     */
    public function tableColumns( $table )
    {
        if ( !$this->IsConnected || !$this->PDO instanceof expODBCNativeConnection )
            return null;
        $columns = array();
        try
        {
            foreach ( $this->PDO->columns( (string)$table ) as $row )
            {
                $row = array_change_key_case( $row, CASE_UPPER );
                $columns[(string)$row['COLUMN_NAME']] = array( 'type' => strtoupper( (string)$row['TYPE_NAME'] ),
                                                               'data_type' => (int)$row['DATA_TYPE'],
                                                               'size' => isset( $row['COLUMN_SIZE'] ) ? (int)$row['COLUMN_SIZE'] : 0,
                                                               'digits' => isset( $row['DECIMAL_DIGITS'] ) ? (int)$row['DECIMAL_DIGITS'] : 0,
                                                               'nullable' => !isset( $row['NULLABLE'] ) || (int)$row['NULLABLE'] !== 0 );
            }
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'The columns of ' . $table . ' could not be read: ' . $this->mask( $e->getMessage() ), __CLASS__ );
            return null;
        }
        return $columns;
    }

    /** A new Access file is made with Access, or with ADOX (doc/eng-us/features/6.0/ms-access-support.md). */
    function createDatabase( $dbName )
    {
        return false;
    }
}
?>
