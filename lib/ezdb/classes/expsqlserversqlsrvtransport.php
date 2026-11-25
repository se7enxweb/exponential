<?php
/**
 * File containing the expSQLServerSqlsrvTransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * expSQLServerDB over Microsoft's sqlsrv extension (Windows and Linux; on Linux over Microsoft's ODBC Driver 17/18).
 * UTF-8 in and out (CharacterSet UTF-8), dates as strings, bound strings as NVARCHAR.
 */
class expSQLServerSqlsrvTransport extends expSQLServerTransport
{
    /** @var resource|false */
    private $connection = false;

    public function name()
    {
        return 'sqlsrv';
    }

    public function open( array $config )
    {
        $this->error = null;
        $server = expSQLServerDialect::serverAddress( $config['host'], isset( $config['port'] ) ? $config['port'] : null );
        $connection = @sqlsrv_connect( $server, expSQLServerDialect::sqlsrvOptions( $config ) );
        if ( !$connection )
        {
            $this->takeError();
            return false;
        }
        $this->connection = $connection;
        return true;
    }

    public function run( $sql, array $params = array() )
    {
        $this->error = null;
        if ( !$this->connection )
        {
            $this->setError( null, '08003', 'The connection is closed' );
            return false;
        }
        $bound = array();
        foreach ( array_values( $params ) as $value )
        {
            $value = (string)$value;
            $size = strlen( $value ) > 4000 ? 'max' : 4000;
            $bound[] = array( $value, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING( 'UTF-8' ), SQLSRV_SQLTYPE_NVARCHAR( $size ) );
        }
        $statement = $bound ? @sqlsrv_query( $this->connection, $sql, $bound ) : @sqlsrv_query( $this->connection, $sql );
        if ( $statement === false )
        {
            $this->takeError();
            return false;
        }
        $sets = array();
        $affected = 0;
        do
        {
            $fields = sqlsrv_num_fields( $statement );
            if ( $fields )
            {
                $rows = array();
                while ( ( $row = sqlsrv_fetch_array( $statement, SQLSRV_FETCH_ASSOC ) ) !== null )
                {
                    if ( $row === false )
                    {
                        $this->takeError();
                        sqlsrv_free_stmt( $statement );
                        return false;
                    }
                    $rows[] = self::normalizeRow( $row );
                }
                $sets[] = $rows;
            }
            else
            {
                $count = sqlsrv_rows_affected( $statement );
                if ( $count !== false && $count > 0 )
                    $affected += $count;
            }
            $next = sqlsrv_next_result( $statement );
            if ( $next === false )
            {
                $this->takeError();
                sqlsrv_free_stmt( $statement );
                return false;
            }
        }
        while ( $next === true );
        sqlsrv_free_stmt( $statement );
        return array( 'sets' => $sets, 'affected' => $affected );
    }

    public function close()
    {
        if ( $this->connection )
            @sqlsrv_close( $this->connection );
        $this->connection = false;
    }

    public function isOpen()
    {
        return $this->connection !== false;
    }

    public function clientVersion()
    {
        if ( !$this->connection )
            return 'sqlsrv ' . phpversion( 'sqlsrv' );
        $info = sqlsrv_client_info( $this->connection );
        return 'sqlsrv ' . phpversion( 'sqlsrv' ) . ( is_array( $info ) && isset( $info['DriverName'] )
            ? ', ' . $info['DriverName'] . ' ' . ( isset( $info['DriverVer'] ) ? $info['DriverVer'] : '' ) : '' );
    }

    /**
     * The first error sqlsrv has (warnings are not errors).
     */
    private function takeError()
    {
        $errors = sqlsrv_errors( SQLSRV_ERR_ERRORS );
        if ( !$errors )
        {
            $this->setError( null, null, 'Unknown sqlsrv error' );
            return;
        }
        $error = $errors[0];
        $this->setError( isset( $error['code'] ) && (int)$error['code'] > 0 ? (int)$error['code'] : null,
                         isset( $error['SQLSTATE'] ) ? $error['SQLSTATE'] : null,
                         isset( $error['message'] ) ? $error['message'] : 'sqlsrv error' );
    }
}
