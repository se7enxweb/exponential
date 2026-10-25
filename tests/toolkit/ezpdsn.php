<?php
/**
 * File containing the ezpDsn class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class ezpDsn
{
    // declared, so that setting the DSN creates no dynamic properties (deprecated since PHP 8.2)
    protected $dsnString = '';
    protected $parts = array();

    public function __construct( $dsn )
    {
        $this->dsn = $dsn;
    }

    public function __get( $name )
    {
        switch ( $name )
        {
            case 'parts':
                return $this->parts;
            case 'dsn':
                return $this->dsnString;
            default:
                return isset( $this->parts[$name] ) ? $this->parts[$name] : null;
        }
    }

    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'dsn':
                $this->dsnString = $value;
                $this->parts = $value !== '' ? self::parseDSN( $value ) : array();
                break;
            default:
                $this->$name = $value;
        }
    }

    static public function parseDSN( $dsn )
    {
        $parts = ezcDbFactory::parseDSN( $dsn );
        if ( isset( $parts['hostspec'] ) )
        {
            $parts['host'] = $parts['hostspec'];
            $parts['server'] = $parts['hostspec'];
        }

        if ( isset( $parts['username'] ) )
            $parts['user'] = $parts['username'];

        // the keys the cluster tests read, present also when the DSN does not name them
        $parts += array( 'port' => null, 'socket' => null, 'database' => null, 'user' => null, 'password' => null );

        return $parts;
    }
}

?>
