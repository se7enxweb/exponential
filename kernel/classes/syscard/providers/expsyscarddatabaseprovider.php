<?php
/**
 * File containing the expSysCardDatabaseProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The database: its type, the server's version (when syscard.ini [Database] AskVersion=enabled: one connection, one
 * question) and the host it is on. Only the host and port of an address are shown: never the user, the password,
 * the database name or a full DSN.
 */
class expSysCardDatabaseProvider extends expSysCardBaseProvider
{
    protected $id = 'database';
    protected $section = 'database';
    protected $priority = 30;
    protected $budget = 2.0;

    const TYPES = array(
        'ezmysqli' => 'MySQL/MariaDB', 'ezmysql' => 'MySQL/MariaDB', 'mysql' => 'MySQL/MariaDB', 'mysqli' => 'MySQL/MariaDB',
        'ezpostgresql' => 'PostgreSQL', 'postgresql' => 'PostgreSQL', 'pgsql' => 'PostgreSQL',
        'ezsqlite3' => 'SQLite', 'sqlite3' => 'SQLite', 'sqlite' => 'SQLite',
        'ezmongodb' => 'MongoDB', 'mongodb' => 'MongoDB', 'ezoracle' => 'Oracle', 'oracle' => 'Oracle',
    );

    public function collect( expSysCardContext $context )
    {
        $implementation = strtolower( (string)$context->ini( 'site.ini', 'DatabaseSettings', 'DatabaseImplementation', '' ) );
        $type = isset( self::TYPES[$implementation] ) ? self::TYPES[$implementation] : ( $implementation !== '' ? $implementation : 'n/a' );
        $facts = array();
        $version = '';
        $severity = expSysCardFact::INFO;
        $hint = '';
        if ( $context->iniEnabled( 'syscard.ini', 'Database', 'AskVersion', 'enabled' ) )
        {
            $answer = $context->get( 'database.version', function () {
                if ( !class_exists( 'eZDB' ) )
                    return null;
                $db = eZDB::instance();
                if ( !$db->isConnected() )
                    return false;
                $version = $db->databaseServerVersion();
                if ( is_array( $version ) )
                    $version = isset( $version['string'] ) && is_string( $version['string'] ) ? $version['string']
                             : ( isset( $version['values'] ) ? implode( '.', (array)$version['values'] ) : '' );
                return is_string( $version ) ? $version : '';
            }, false ); // an exception (no connection) is false too
            if ( $answer === false )
            {
                $severity = expSysCardFact::ERROR;
                $hint = 'The database does not answer: check that its server runs and that site.ini [DatabaseSettings] is right (./console exp:system:info).';
            }
            elseif ( is_string( $answer ) && $answer !== '' )
            {
                $version = (string)preg_replace( '/-log$|-[0-9]+ubuntu.*$/', '', $answer );
                $severity = expSysCardFact::OK;
            }
        }
        $facts[] = $this->fact( 'type', 'Database', trim( $type . ( $version !== '' ? ' ' . $version : '' ) . ( $severity === expSysCardFact::ERROR ? ' (no connection)' : '' ) ), $severity, $hint );
        $facts[] = $this->fact( 'host', 'Host', self::hostOf( $type, (string)$context->ini( 'site.ini', 'DatabaseSettings', 'Server', '' ),
                                                              (string)$context->ini( 'site.ini', 'DatabaseSettings', 'Port', '' ),
                                                              (string)$context->ini( 'site.ini', 'DatabaseSettings', 'Socket', '' ) ) );
        if ( $context->mode === expSysCardContext::MODE_INFO )
        {
            $charset = (string)$context->ini( 'site.ini', 'DatabaseSettings', 'Charset', '' );
            if ( $charset !== '' )
                $facts[] = $this->fact( 'charset', 'Charset', $charset );
            $slave = $context->iniEnabled( 'site.ini', 'DatabaseSettings', 'UseSlaveServer', 'disabled' );
            $facts[] = $this->fact( 'replica', 'Read replica', $slave ? 'yes' : 'no' );
        }
        return $facts;
    }

    /**
     * Where the database is, without anything else of its address.
     *
     * @param string $type
     * @param string $server a host name, an address, or a URI (mongodb://user:pw@host:27017/db)
     * @param string $port
     * @param string $socket
     * @return string host[:port], "local socket" or "local file"
     */
    public static function hostOf( $type, $server, $port = '', $socket = '' )
    {
        if ( $type === 'SQLite' )
            return 'local file';
        $server = trim( $server );
        if ( strpos( $server, '://' ) !== false )
        {
            $parts = parse_url( $server );
            $host = is_array( $parts ) && isset( $parts['host'] ) ? $parts['host'] : '';
            if ( $port === '' && is_array( $parts ) && isset( $parts['port'] ) )
                $port = (string)$parts['port'];
            $server = $host;
        }
        elseif ( strpos( $server, '@' ) !== false )
            $server = substr( $server, strrpos( $server, '@' ) + 1 );
        $server = (string)preg_replace( '#[/?].*$#', '', $server );
        if ( ( $server === '' || $server === 'localhost' ) && trim( $socket ) !== '' && strtolower( trim( $socket ) ) !== 'disabled' )
            return 'local socket';
        if ( $server === '' )
            return 'n/a';
        return $server . ( $port !== '' && ctype_digit( $port ) && strpos( $server, ':' ) === false ? ':' . $port : '' );
    }
}
