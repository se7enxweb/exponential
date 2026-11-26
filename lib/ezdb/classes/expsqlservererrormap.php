<?php
/**
 * File containing the expSQLServerErrorMap class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * What a SQL Server error means for the driver: whether the statement may be tried again, whether the connection is
 * gone, whether the transaction around it is gone. Keyed by the native error number (sys.messages), with the ODBC
 * SQLSTATE as the fallback for errors the client library raises itself (no server number).
 */
class expSQLServerErrorMap
{
    const DEADLOCK = 'deadlock';
    const LOCK_TIMEOUT = 'lock_timeout';
    const DUPLICATE_KEY = 'duplicate_key';
    const FOREIGN_KEY = 'foreign_key';
    const NOT_NULL = 'not_null';
    const TRUNCATION = 'truncation';
    const CONVERSION = 'conversion';
    const OVERFLOW = 'overflow';
    const TOO_MANY_PARAMETERS = 'too_many_parameters';
    const SYNTAX = 'syntax';
    const UNKNOWN_OBJECT = 'unknown_object';
    const PERMISSION = 'permission';
    const CONNECTION = 'connection';
    const TRANSIENT = 'transient';
    const READ_ONLY = 'read_only';
    const SNAPSHOT_CONFLICT = 'snapshot_conflict';
    const IDENTITY_INSERT = 'identity_insert';
    const TIMEOUT = 'timeout';
    const OTHER = 'other';

    /** @var array native error number => category */
    const NUMBERS = array(
        1205 => self::DEADLOCK,            // chosen as deadlock victim (transaction rolled back)
        1222 => self::LOCK_TIMEOUT,        // lock request time out period exceeded (statement only)
        2627 => self::DUPLICATE_KEY,       // violation of PRIMARY KEY / UNIQUE constraint
        2601 => self::DUPLICATE_KEY,       // duplicate key row in object with unique index
        547 => self::FOREIGN_KEY,          // conflicted with a FOREIGN KEY / CHECK constraint
        515 => self::NOT_NULL,             // cannot insert NULL into column
        8152 => self::TRUNCATION,          // string or binary data would be truncated
        2628 => self::TRUNCATION,          // ... in table/column (2019 and later)
        245 => self::CONVERSION,           // conversion failed (varchar to int)
        8114 => self::CONVERSION,          // error converting data type
        241 => self::CONVERSION,           // conversion failed when converting date/time from string
        242 => self::CONVERSION,           // date out of range
        8115 => self::OVERFLOW,            // arithmetic overflow
        220 => self::OVERFLOW,             // arithmetic overflow for data type smallint/tinyint
        8134 => self::OVERFLOW,            // divide by zero
        8003 => self::TOO_MANY_PARAMETERS, // the incoming request has too many parameters (over 2100)
        102 => self::SYNTAX,               // incorrect syntax near
        156 => self::SYNTAX,               // incorrect syntax near the keyword
        10744 => self::SYNTAX,             // FETCH NEXT 0 ROWS (the number of rows must be greater than zero)
        208 => self::UNKNOWN_OBJECT,       // invalid object name
        207 => self::UNKNOWN_OBJECT,       // invalid column name
        229 => self::PERMISSION,           // permission denied
        262 => self::PERMISSION,           // permission denied in database
        3906 => self::READ_ONLY,           // database is read-only (a readable secondary)
        3961 => self::SNAPSHOT_CONFLICT,   // snapshot isolation: object changed by another transaction
        3960 => self::SNAPSHOT_CONFLICT,   // snapshot isolation update conflict
        8107 => self::IDENTITY_INSERT,     // IDENTITY_INSERT is already ON for another table
        544 => self::IDENTITY_INSERT,      // cannot insert explicit value for identity column
        // the connection is gone
        233 => self::CONNECTION, 10053 => self::CONNECTION, 10054 => self::CONNECTION, 10060 => self::CONNECTION,
        10061 => self::CONNECTION, 64 => self::CONNECTION, 121 => self::CONNECTION, 596 => self::CONNECTION,
        6005 => self::CONNECTION, 4060 => self::CONNECTION, 18456 => self::CONNECTION,
        // transient: Azure SQL failover, throttling, resource limits; and a database being recovered
        40613 => self::TRANSIENT, 40197 => self::TRANSIENT, 40501 => self::TRANSIENT, 40540 => self::TRANSIENT,
        49918 => self::TRANSIENT, 49919 => self::TRANSIENT, 49920 => self::TRANSIENT, 4221 => self::TRANSIENT,
        10928 => self::TRANSIENT, 10929 => self::TRANSIENT, 40143 => self::TRANSIENT, 615 => self::TRANSIENT,
        926 => self::TRANSIENT, 976 => self::TRANSIENT, 983 => self::TRANSIENT, 701 => self::TRANSIENT,
    );

    /** ODBC SQLSTATE classes that mean the link is gone */
    const CONNECTION_STATES = array( '08S01', '08001', '08003', '08004', '08007', 'HYT01', '01002' );

    /**
     * The category of an error.
     *
     * @param int|string|null $number native SQL Server error number
     * @param string|null $sqlState
     * @param string $message the client library refuses some statements itself, with a message but no number
     * @return string one of the constants
     */
    public static function category( $number, $sqlState = null, $message = '' )
    {
        $number = (int)$number;
        if ( $number && isset( self::NUMBERS[$number] ) )
            return self::NUMBERS[$number];
        if ( !$number && preg_match( '/maximum of 2100 parameters|too many parameters/i', (string)$message ) )
            return self::TOO_MANY_PARAMETERS;
        $sqlState = strtoupper( (string)$sqlState );
        if ( $sqlState === '40001' )
            return self::DEADLOCK;
        if ( $sqlState === '23000' )
            return self::DUPLICATE_KEY;
        if ( $sqlState === '22018' )
            return self::CONVERSION;
        if ( $sqlState === '22001' )
            return self::TRUNCATION;
        if ( $sqlState === '07002' || $sqlState === '07009' )
            return self::TOO_MANY_PARAMETERS;
        if ( $sqlState === 'HYT00' )
            return self::TIMEOUT;
        if ( in_array( $sqlState, self::CONNECTION_STATES, true ) )
            return self::CONNECTION;
        return self::OTHER;
    }

    /**
     * Whether a single statement outside a transaction may simply be sent again: a deadlock victim, an Azure
     * failover or throttle, a snapshot conflict. A lock timeout is not retried (the caller set the wait it wants).
     *
     * @param string $category
     * @return bool
     */
    public static function isRetryable( $category )
    {
        return in_array( $category, array( self::DEADLOCK, self::TRANSIENT, self::SNAPSHOT_CONFLICT ), true );
    }

    /**
     * Whether the server has rolled back the transaction the statement ran in: a deadlock victim always; with
     * XACT_ABORT ON (the driver's setting) every runtime error but a few compile errors does too.
     *
     * @param string $category
     * @param bool $xactAbort
     * @return bool
     */
    public static function endsTransaction( $category, $xactAbort = true )
    {
        if ( in_array( $category, array( self::DEADLOCK, self::CONNECTION, self::TRANSIENT, self::SNAPSHOT_CONFLICT ), true ) )
            return true;
        if ( !$xactAbort )
            return false;
        return !in_array( $category, array( self::SYNTAX, self::UNKNOWN_OBJECT ), true );
    }

    /**
     * Whether the connection must be opened again.
     *
     * @param string $category
     * @return bool
     */
    public static function isConnectionLost( $category )
    {
        return $category === self::CONNECTION;
    }

    /**
     * The native number and SQLSTATE in a message of one of the client libraries ("[Microsoft][ODBC Driver 18 for SQL
     * Server][SQL Server]Violation ... (SQLExecute[2627] at ...)" from PDO_ODBC, "SQLSTATE[23000]: ...: 2627 ..." from
     * pdo_sqlsrv, "General SQL Server error: Check messages from the SQL Server [2627] (severity 14)" from pdo_dblib).
     *
     * @param string $message
     * @return array( int|null number, string|null sqlstate )
     */
    public static function parseMessage( $message )
    {
        $message = (string)$message;
        $state = null;
        $number = null;
        if ( preg_match( '/SQLSTATE\[([0-9A-Z]{5})\]/', $message, $m ) )
            $state = $m[1];
        if ( preg_match( '/\(SQL\w+\[(\d+)\]/', $message, $m ) )
            $number = (int)$m[1];
        else if ( preg_match( '/SQLSTATE\[[0-9A-Z]{5}\]: [^:]*: (\d+) /', $message, $m ) )
            $number = (int)$m[1];
        else if ( preg_match( '/\[(\d{2,6})\] \(severity \d+\)/', $message, $m ) )
            $number = (int)$m[1];
        // PDO_ODBC writes [0] for an error the ODBC driver raised itself (22001 right truncation): no server number
        return array( $number ? $number : null, $state );
    }
}
