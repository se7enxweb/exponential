<?php
/**
 * File containing the expSQLServerPDOTransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * expSQLServerDB over PDO: pdo_sqlsrv (Microsoft), PDO_ODBC with an ODBC driver for SQL Server (ODBC Driver 18 by
 * default), or pdo_dblib (FreeTDS).
 *
 * PDO_ODBC sends the statement text through ODBC's narrow API, in the client's code page: a non-ASCII character in
 * the SQL text arrives broken (measured with ODBC Driver 18: N'ä' became N'Ã¤'), while a bound parameter arrives
 * intact. expSQLServerDB therefore binds string literals on this layer. PDO_ODBC also prepares every query()
 * (sp_prepexec), and what a prepared call sets (SET options, #temp tables) ends with it: a batch that returns no
 * rows runs through exec() (SQLExecDirect) instead, in the session itself.
 */
class expSQLServerPDOTransport extends expSQLServerTransport
{
    /** @var string pdo_sqlsrv, pdo_odbc or pdo_dblib */
    private $layer;

    /** @var PDO|null */
    private $pdo = null;

    /**
     * @param string $layer pdo_sqlsrv, pdo_odbc or pdo_dblib
     */
    public function __construct( $layer )
    {
        if ( !in_array( $layer, array( 'pdo_sqlsrv', 'pdo_odbc', 'pdo_dblib' ), true ) )
            throw new InvalidArgumentException( "Unknown SQL Server PDO layer '$layer'" );
        $this->layer = $layer;
    }

    public function name()
    {
        return $this->layer;
    }

    /**
     * The DSN of this layer for $config.
     *
     * @param array $config
     * @return string
     */
    public function dsn( array $config )
    {
        if ( $this->layer === 'pdo_sqlsrv' )
            return expSQLServerDialect::pdoSqlsrvDSN( $config );
        if ( $this->layer === 'pdo_odbc' )
            return expSQLServerDialect::odbcDSN( $config, isset( $config['odbc_driver'] ) && $config['odbc_driver'] !== '' ? $config['odbc_driver'] : 'ODBC Driver 18 for SQL Server' );
        return expSQLServerDialect::dblibDSN( $config );
    }

    public function open( array $config )
    {
        $this->error = null;
        $options = array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_STRINGIFY_FETCHES => false );
        if ( $this->layer === 'pdo_sqlsrv' && defined( 'PDO::SQLSRV_ATTR_DIRECT_QUERY' ) )
        {
            // A statement without parameters runs directly in the session (not in sp_prepexec's scope), so a
            // #temporary table it creates lives on in the session.
            $options[PDO::SQLSRV_ATTR_DIRECT_QUERY] = true;
        }
        if ( isset( $config['login_timeout'] ) && (int)$config['login_timeout'] > 0 && $this->layer !== 'pdo_sqlsrv' )
            $options[PDO::ATTR_TIMEOUT] = (int)$config['login_timeout'];
        try
        {
            $this->pdo = new PDO( $this->dsn( $config ), isset( $config['user'] ) ? (string)$config['user'] : null,
                                  isset( $config['password'] ) ? (string)$config['password'] : null, $options );
        }
        catch ( PDOException $e )
        {
            $this->pdo = null;
            $this->takeException( $e );
            return false;
        }
        return true;
    }

    public function run( $sql, array $params = array() )
    {
        $this->error = null;
        if ( !$this->pdo )
        {
            $this->setError( null, '08003', 'The connection is closed' );
            return false;
        }
        try
        {
            if ( $params )
            {
                $statement = $this->pdo->prepare( $sql );
                $n = 1;
                foreach ( $params as $value )
                    $statement->bindValue( $n++, (string)$value, PDO::PARAM_STR );
                $statement->execute();
            }
            else if ( $this->layer === 'pdo_odbc' && !self::returnsRows( $sql ) )
            {
                // PDO_ODBC prepares every query(): SET options and #temp tables made in a prepared call end with it
                $affected = $this->pdo->exec( $sql );
                return array( 'sets' => array(), 'affected' => max( 0, (int)$affected ) );
            }
            else
                $statement = $this->pdo->query( $sql );
            $sets = array();
            $affected = 0;
            do
            {
                if ( $statement->columnCount() > 0 )
                {
                    $rows = array();
                    foreach ( $statement->fetchAll( PDO::FETCH_ASSOC ) as $row )
                        $rows[] = self::normalizeRow( $row );
                    $sets[] = $rows;
                }
                else
                    $affected += max( 0, (int)$statement->rowCount() );
                try
                {
                    $more = $statement->nextRowset();
                }
                catch ( PDOException $e )
                {
                    // PDO_ODBC reports "no more results" with some drivers as an error without a native number
                    $info = $e->errorInfo;
                    if ( is_array( $info ) && isset( $info[1] ) && (int)$info[1] > 0 )
                        throw $e;
                    $more = false;
                }
            }
            while ( $more );
            $statement->closeCursor();
            return array( 'sets' => $sets, 'affected' => $affected );
        }
        catch ( PDOException $e )
        {
            $this->takeException( $e );
            return false;
        }
    }

    /**
     * Whether a batch may return rows: a SELECT word outside literals and comments (also SELECT ... INTO, which
     * returns none, but a prepared SELECT INTO is harmless).
     *
     * @param string $sql
     * @return bool
     */
    public static function returnsRows( $sql )
    {
        foreach ( expSQLServerLexer::tokenize( (string)$sql ) as $token )
            if ( $token['type'] === expSQLServerLexer::WORD && strtoupper( $token['text'] ) === 'SELECT' )
                return true;
        return false;
    }

    public function close()
    {
        $this->pdo = null;
    }

    public function isOpen()
    {
        return $this->pdo !== null;
    }

    public function clientVersion()
    {
        $version = $this->layer . ' ' . (string)phpversion( $this->layer === 'pdo_sqlsrv' ? 'pdo_sqlsrv' : ( $this->layer === 'pdo_odbc' ? 'PDO_ODBC' : 'pdo_dblib' ) );
        if ( $this->pdo )
        {
            try
            {
                $client = $this->pdo->getAttribute( PDO::ATTR_CLIENT_VERSION );
                if ( is_array( $client ) )
                    $client = isset( $client['DriverName'] ) ? $client['DriverName'] . ' ' . ( isset( $client['DriverVer'] ) ? $client['DriverVer'] : '' ) : implode( ' ', $client );
                if ( (string)$client !== '' )
                    $version .= ', ' . $client;
            }
            catch ( PDOException $e )
            {
            }
        }
        return trim( $version );
    }

    /**
     * @param PDOException $e
     */
    private function takeException( PDOException $e )
    {
        $info = $e->errorInfo;
        $number = is_array( $info ) && isset( $info[1] ) && (int)$info[1] > 0 ? (int)$info[1] : null;
        $state = is_array( $info ) && isset( $info[0] ) ? (string)$info[0] : null;
        $message = is_array( $info ) && isset( $info[2] ) && $info[2] !== '' ? (string)$info[2] : $e->getMessage();
        if ( $number === null || $state === null )
        {
            list( $parsedNumber, $parsedState ) = expSQLServerErrorMap::parseMessage( $e->getMessage() );
            if ( $number === null )
                $number = $parsedNumber;
            if ( $state === null || $state === '' )
                $state = $parsedState;
        }
        $this->setError( $number, $state, $message );
    }
}
