<?php
/**
 * File containing the expInformixDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The Informix driver: IBM Informix 14.10 and 15 over ibm_db2 (or pdo_ibm, which cannot read a CLOB of Informix),
 * through the server's DRDA listener. site.ini:
 *
 * <code>
 * [DatabaseSettings]
 * DatabaseImplementation=informix
 * Server=ifx.example.com
 * Port=9089                    (a listener of the drsoctcp protocol in the server's sqlhosts)
 * Database=exponential
 * </code>
 *
 * and [InformixSettings] of site.ini. It is expDb2DB with Informix's SQL (expInformixDialect) and catalogue, and
 * these differences:
 *
 *  - New ids: an auto_increment column is a SERIAL. Informix numbers a row without id or with 0, moves its counter
 *    past an id an insert gives itself, and lastSerialID() reads DBINFO('sqlca.sqlerrd1') of the connection: the
 *    id of its last insert. Informix keeps it until the next statement, so the kernel's insert-then-read works, and
 *    the driver keeps the id of each INSERT itself for a later call.
 *  - Temporary tables: Informix's own (CREATE TEMP TABLE ... WITH NO LOG), which belong to the session.
 *  - Text: every connection allows line breaks in string literals (IFX_ALLOW_NEWLINE), which the kernel's XML has.
 *  - Locks: every connection waits up to LockWait seconds for a lock (SET LOCK MODE TO WAIT; Informix does not
 *    wait at all by default), and reads the last committed version of a row a writer holds (COMMITTED READ LAST
 *    COMMITTED), as MySQL's InnoDB does.
 *
 * @since 6.0.15
 * @package lib
 */
class expInformixDB extends expDb2DB
{
    const NAME = 'informix';

    const DIALECT = 'expInformixDialect';

    const SETTINGS_GROUP = 'InformixSettings';

    /** ibm_db2 first: pdo_ibm 1.7 ends PHP with a segmentation fault when it reads a CLOB of Informix */
    const AUTO_LAYERS = array( 'ibm_db2', 'pdo_ibm' );

    /** @var array the defaults of [InformixSettings] */
    const DEFAULT_SETTINGS = array(
        'Layer' => 'auto',
        'Driver' => '',
        'Security' => '',
        'ConnectionOptions' => '',
        'StatementConcentrator' => 'disabled',
        'LockWait' => 30,
        'DeadlockRetries' => 3,
        'DeadlockRetryWait' => 50,
        'ReconnectOnLoss' => 'enabled',
        'InListChunkSize' => 1000,
        'Schema' => '',
    );

    /** @var array the id of the last INSERT per table (lower case) of this connection */
    public $LastIds = array();

    public static function productName()
    {
        return 'Informix';
    }

    protected static function missingLayerText()
    {
        return 'Informix needs the ibm_db2 or pdo_ibm extension (they reach Informix over DRDA), the DB handler will not be initialized.';
    }

    protected static function newTransport( $layer )
    {
        if ( $layer === 'ibm_db2' )
            return new expDb2IbmDb2Transport( 'expInformixDialect' );
        return new expInformixPDOTransport( $layer );
    }

    /**
     * Lock waits and the isolation of every connection.
     *
     * @return bool
     */
    protected function sessionSetup()
    {
        $this->LastIds = array();
        $wait = (int)$this->Settings['LockWait'];
        if ( $this->Transport->run( $wait > 0 ? "SET LOCK MODE TO WAIT $wait" : 'SET LOCK MODE TO NOT WAIT' ) === false )
            return false;
        if ( $this->Transport->run( 'SET ISOLATION TO COMMITTED READ LAST COMMITTED' ) === false )
            return false;
        // string literals with line breaks (the kernel's XML): Informix refuses them unless the session allows them
        return $this->Transport->run( "EXECUTE PROCEDURE IFX_ALLOW_NEWLINE('t')" ) !== false;
    }

    protected function serverVersionString()
    {
        $result = $this->Transport->run( "SELECT DBINFO('version', 'full') AS v FROM systables WHERE tabid = 1" );
        if ( $result !== false && isset( $result['rows'][0]['v'] ) && $result['rows'][0]['v'] !== '' )
            return (string)$result['rows'][0]['v'];
        return $this->Transport->serverVersion();
    }

    /**
     * After an INSERT the id it gave (DBINFO('sqlca.sqlerrd1') is only there until the next statement), and the
     * catalogue after a schema change.
     *
     * @param string $prepared
     */
    protected function afterStatement( $prepared )
    {
        $kind = expInformixDialect::statementKind( $prepared );
        if ( $kind === 'INSERT' )
        {
            $table = expInformixDialect::insertTable( $prepared );
            if ( $table === null )
                return;
            $this->LastInsertTable = $table;
            $result = $this->Transport->run( "SELECT DBINFO('sqlca.sqlerrd1') AS id FROM systables WHERE tabid = 1" );
            $id = $result !== false && isset( $result['rows'][0]['id'] ) ? (int)$result['rows'][0]['id'] : 0;
            if ( $id > 0 )
                $this->LastIds[$table] = $id;
            else
                unset( $this->LastIds[$table] );
            return;
        }
        if ( in_array( $kind, array( 'CREATE', 'ALTER', 'DROP', 'RENAME' ), true ) )
            $this->KnownTables = null;
    }

    /**
     * The SERIAL id the last INSERT of this connection into $table made (the last table inserted into without
     * $table); false when there is none.
     */
    function lastSerialID( $table = false, $column = false )
    {
        if ( !$this->IsConnected )
            return false;
        if ( $table === false || $table === null || $table === '' )
            $table = $this->LastInsertTable;
        $table = strtolower( trim( (string)$table, '`" ' ) );
        return isset( $this->LastIds[$table] ) ? $this->LastIds[$table] : false;
    }

    /**
     * Nothing to do: a SERIAL moves past the ids an insert gives itself.
     *
     * @return bool
     */
    function correctSequenceValues()
    {
        return true;
    }

    // ------------------------------------------------------------------ temporary tables

    function generateUniqueTempTableName( $pattern, $randomizeIndex = false, $server = self::SERVER_SLAVE )
    {
        return str_replace( '%', $randomizeIndex === false ? (string)mt_rand() : (string)(int)$randomizeIndex, (string)$pattern );
    }

    /**
     * CREATE TEMPORARY TABLE as Informix's own temporary table of the session.
     */
    function createTempTable( $createTableQuery = '', $server = self::SERVER_SLAVE )
    {
        $temporary = expInformixDialect::temporaryTable( $createTableQuery );
        if ( $temporary === null )
            return $this->query( $createTableQuery );
        $this->TempTables[strtolower( $temporary['name'] )] = $temporary['name'];
        return $this->query( $temporary['sql'] );
    }

    function dropTempTable( $dropTableQuery = '', $server = self::SERVER_SLAVE )
    {
        if ( preg_match( '/^\s*DROP\s+(?:TEMPORARY\s+)?TABLE\s+(?:IF\s+EXISTS\s+)?([`"]?[\w$]+[`"]?)\s*;?\s*$/i', (string)$dropTableQuery, $m ) )
        {
            $name = strtolower( trim( $m[1], '`"' ) );
            unset( $this->TempTables[$name] );
            return $this->query( 'DROP TABLE ' . $name );
        }
        return $this->query( $dropTableQuery );
    }

    // ------------------------------------------------------------------ relations

    public static function tableNamesQuery( $temporary = false )
    {
        return "SELECT TRIM(tabname) AS name FROM systables WHERE tabid >= 100 AND tabtype = 'T' ORDER BY 1";
    }

    public static function relationListQuery( $relationType )
    {
        switch ( $relationType )
        {
            case eZDBInterface::RELATION_TABLE:
                return static::tableNamesQuery();
            case eZDBInterface::RELATION_SEQUENCE:
                return "SELECT TRIM(tabname) AS name FROM systables WHERE tabid >= 100 AND tabtype = 'Q' ORDER BY 1";
            case eZDBInterface::RELATION_TRIGGER:
                return 'SELECT TRIM(trigname) AS name FROM systriggers ORDER BY 1';
            case eZDBInterface::RELATION_VIEW:
                return "SELECT TRIM(tabname) AS name FROM systables WHERE tabid >= 100 AND tabtype = 'V' ORDER BY 1";
            case eZDBInterface::RELATION_INDEX:
                return "SELECT TRIM(i.idxname) AS name FROM sysindexes i WHERE i.tabid >= 100 AND i.idxname NOT LIKE ' %' ORDER BY 1";
        }
        return null;
    }

    /**
     * The columns of a table from the catalogue: name, type code, length, nullable, default.
     *
     * @param string $table
     * @return array|false
     */
    public function tableColumns( $table )
    {
        return $this->arrayQuery( 'SELECT TRIM(c.colname) AS name, c.coltype AS type_code, c.collength AS length, c.extended_id AS extended_id,'
                                . ' d.type AS default_type, d.default AS default_value'
                                . ' FROM syscolumns c JOIN systables t ON t.tabid = c.tabid'
                                . ' LEFT JOIN sysdefaults d ON d.tabid = c.tabid AND d.colno = c.colno'
                                . ' WHERE t.tabname = ' . expInformixDialect::stringLiteral( expInformixDialect::fold( $table ) ) . ' ORDER BY c.colno' );
    }

    /**
     * Informix makes a database with CREATE DATABASE over a connection to another one (sysmaster): not from here,
     * where the connection is to the site's database.
     */
    function createDatabase( $dbName )
    {
        $this->ErrorMessage = 'Create the Informix database first (dbaccess: CREATE DATABASE ... IN <dbspace> WITH LOG, with DB_LOCALE=en_us.utf8)';
        $this->ErrorNumber = -1;
        eZDebug::writeError( $this->ErrorMessage, __METHOD__ );
        return false;
    }

    function removeDatabase( $dbName )
    {
        $this->ErrorMessage = 'Drop the Informix database outside of a connection to it (dbaccess: DROP DATABASE)';
        $this->ErrorNumber = -1;
        eZDebug::writeError( $this->ErrorMessage, __METHOD__ );
        return false;
    }

    function close()
    {
        if ( $this->Transport )
            $this->Transport->close();
        $this->TempTables = array();
        $this->IsConnected = false;
    }
}
