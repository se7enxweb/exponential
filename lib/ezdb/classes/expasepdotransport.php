<?php
/**
 * File containing the expASEPDOTransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * expASEDB over pdo_dblib (FreeTDS's DB-Library) at TDS 5.0, or over PDO_ODBC with FreeTDS's ODBC driver
 * (TDS_Version=5.0) or SAP's own ASE ODBC driver. The PDO layer of the SQL Server driver does the work (result sets,
 * errors, the rows as MySQL gives them); only the data source names differ (expASEDialect::dblibDSN(), odbcDSN()).
 */
class expASEPDOTransport extends expSQLServerPDOTransport
{
    /** @var string pdo_dblib or pdo_odbc */
    protected $aseLayer;

    /**
     * @param string $layer pdo_dblib or pdo_odbc
     */
    public function __construct( $layer )
    {
        if ( !in_array( $layer, array( 'pdo_dblib', 'pdo_odbc' ), true ) )
            throw new InvalidArgumentException( "Unknown SAP ASE layer '$layer'" );
        parent::__construct( $layer );
        $this->aseLayer = $layer;
    }

    public function dsn( array $config )
    {
        if ( $this->aseLayer === 'pdo_odbc' )
            return expASEDialect::odbcDSN( $config, isset( $config['odbc_driver'] ) && $config['odbc_driver'] !== '' ? $config['odbc_driver'] : 'FreeTDS' );
        return expASEDialect::dblibDSN( $config );
    }

    /**
     * The layer to use: the configured one, or for "auto" pdo_dblib, then pdo_odbc. Null when PHP has neither.
     *
     * @param string $layer auto, pdo_dblib (dblib, freetds) or pdo_odbc (odbc)
     * @param string[]|null $available the layers PHP has
     * @return string|null
     */
    public static function chooseASELayer( $layer, $available = null )
    {
        if ( $available === null )
            $available = expSQLServerTransport::availableLayers();
        $layer = strtolower( trim( (string)$layer ) );
        $aliases = array( 'dblib' => 'pdo_dblib', 'freetds' => 'pdo_dblib', 'odbc' => 'pdo_odbc' );
        if ( isset( $aliases[$layer] ) )
            $layer = $aliases[$layer];
        if ( $layer === '' || $layer === 'auto' )
        {
            foreach ( array( 'pdo_dblib', 'pdo_odbc' ) as $candidate )
                if ( in_array( $candidate, $available, true ) )
                    return $candidate;
            return null;
        }
        return in_array( $layer, array( 'pdo_dblib', 'pdo_odbc' ), true ) && in_array( $layer, $available, true ) ? $layer : null;
    }
}
