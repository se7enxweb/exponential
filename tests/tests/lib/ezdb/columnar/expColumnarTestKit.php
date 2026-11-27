<?php
/**
 * Shared pieces of the tests of the columnar drivers, MonetDB (expMonetDBDB) and ClickHouse (expClickHouseDB): the
 * drivers' files, quiet debug output, stand-ins for the MAPI client (expMonetDBFakeMapi) and the HTTP transport
 * (expClickHouseFakeHttp), and the live servers from the environment:
 *
 *   EXP_TEST_MONETDB_HOST, _PORT (50000), _USER (monetdb), _PASSWORD (monetdb), _DATABASE
 *   EXP_TEST_CLICKHOUSE_HOST, _PORT (8123), _USER (default), _PASSWORD, _DATABASE (default)
 *
 * The live tests skip without the _HOST of their database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

foreach ( array( 'lib/ezdb/classes/expdbsqlhelpers.php', 'lib/ezdb/classes/expmonetdbmapi.php', 'lib/ezdb/classes/expmonetdbdialect.php',
                 'lib/ezdb/classes/expmonetdbdb.php', 'lib/ezdb/classes/expclickhousehttp.php', 'lib/ezdb/classes/expclickhousedialect.php',
                 'lib/ezdb/classes/expclickhousedb.php', 'lib/ezdbschema/classes/ezdbschemainterface.php', 'lib/ezdbschema/classes/ezpgsqlschema.php',
                 'lib/ezdbschema/classes/expmonetdbschema.php', 'lib/ezdbschema/classes/expclickhouseschema.php' ) as $file )
    require_once dirname( __DIR__, 5 ) . '/' . $file;

class expColumnarTestKit
{
    /**
     * Silences eZDebug for a test; returns what to restore.
     *
     * @return array
     */
    public static function quietDebug()
    {
        $saved = array();
        foreach ( array( 'eZDebugGlobalInstance', 'eZDebugEnabled' ) as $key )
            $saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $GLOBALS['eZDebugGlobalInstance'] = new eZDebug();
        $GLOBALS['eZDebugEnabled'] = false;
        return $saved;
    }

    public static function restoreDebug( array $saved )
    {
        foreach ( $saved as $key => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $value[0];
        }
    }

    /**
     * The live server of a database from the environment, or null.
     *
     * @param string $name MONETDB or CLICKHOUSE
     * @return array|null host, port, user, password, database
     */
    public static function live( $name )
    {
        $host = (string)getenv( "EXP_TEST_{$name}_HOST" );
        if ( $host === '' )
            return null;
        $defaults = $name === 'MONETDB' ? array( 50000, 'monetdb', 'monetdb', 'exponential' ) : array( 8123, 'default', '', 'default' );
        $get = function ( $key, $default ) use ( $name ) {
            $value = getenv( "EXP_TEST_{$name}_$key" );
            return $value === false || $value === '' ? $default : $value;
        };
        return array( 'server' => $host, 'port' => (int)$get( 'PORT', $defaults[0] ), 'user' => $get( 'USER', $defaults[1] ),
                      'password' => $get( 'PASSWORD', $defaults[2] ), 'database' => $get( 'DATABASE', $defaults[3] ) );
    }

    /**
     * A MonetDB driver on the stand-in client.
     */
    public static function monetdb( expMonetDBFakeMapi $mapi, array $settings = array() )
    {
        return new expMonetDBDB( array( 'server' => 'mdb.example', 'port' => 50000, 'user' => 'u', 'password' => 'p', 'database' => 'site',
                                        'monetdb_mapi' => $mapi, 'monetdb' => $settings ) );
    }

    /**
     * A ClickHouse driver on the stand-in transport.
     */
    public static function clickhouse( expClickHouseFakeHttp $http, array $settings = array() )
    {
        return new expClickHouseDB( array( 'server' => 'ch.example', 'port' => 8123, 'user' => 'u', 'password' => 'p', 'database' => 'analytics',
                                           'clickhouse_http' => $http, 'clickhouse' => $settings ) );
    }

    /**
     * A name for one test's objects: <prefix>_<pid>_<random>.
     */
    public static function uniqueName( $prefix )
    {
        return $prefix . '_' . getmypid() . '_' . bin2hex( random_bytes( 3 ) );
    }
}

/**
 * The MAPI client without a server: records what is sent and answers from a script (statement pattern => results as
 * expMonetDBMapi::parse() gives them, or false for a lost connection).
 */
class expMonetDBFakeMapi
{
    public $Error = '';
    public $SQLState = '';
    public $Connects = array();
    public $Sent = array();
    public $Answers = array();
    public $ConnectError = null;
    protected $Connected = false;

    public function connect( $host, $port, $user, $password, $database, $timeout = 10.0 )
    {
        $this->Connects[] = array( $host, $port, $user, $password, $database );
        if ( $this->ConnectError !== null )
        {
            $this->Error = $this->ConnectError;
            $this->SQLState = '08001';
            return false;
        }
        $this->Connected = true;
        return true;
    }

    public function query( $sql )
    {
        $this->Sent[] = $sql;
        foreach ( $this->Answers as $pattern => $answer )
        {
            if ( !preg_match( $pattern, $sql ) )
                continue;
            if ( $answer === false )
            {
                $this->Error = 'The connection to MonetDB was lost';
                $this->SQLState = '08006';
                $this->Connected = false;
                return false;
            }
            return $answer;
        }
        return array( array( 'type' => 'schema' ) );
    }

    public function isConnected()
    {
        return $this->Connected;
    }

    public function close()
    {
        $this->Connected = false;
    }
}

/**
 * The HTTP transport without a server: records what is sent (statement, settings) and answers from a script
 * (statement pattern => a result, or array( 'error' => message, 'code' => number )).
 */
class expClickHouseFakeHttp
{
    public $Error = '';
    public $ErrorCode = 0;
    public $Sent = array();
    public $Answers = array();

    public function query( $sql, array $settings = array() )
    {
        $this->Sent[] = array( $sql, $settings );
        $this->Error = '';
        $this->ErrorCode = 0;
        foreach ( $this->Answers as $pattern => $answer )
        {
            if ( !preg_match( $pattern, $sql ) )
                continue;
            if ( isset( $answer['error'] ) )
            {
                $this->Error = $answer['error'];
                $this->ErrorCode = isset( $answer['code'] ) ? $answer['code'] : 0;
                return false;
            }
            return $answer + array( 'columns' => array(), 'types' => array(), 'rows' => array(), 'summary' => array() );
        }
        return array( 'columns' => array(), 'types' => array(), 'rows' => array(), 'summary' => array() );
    }

    /** The statements sent, without their settings */
    public function statements()
    {
        return array_map( function ( $s ) { return $s[0]; }, $this->Sent );
    }
}
