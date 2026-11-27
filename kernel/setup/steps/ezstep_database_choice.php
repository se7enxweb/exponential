<?php
/**
 * File containing the eZStepDatabaseChoice class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZStepDatabaseChoice ezstep_database_choice.php
  \brief The class eZStepDatabaseChoice lets the user pick the database system.

  SQLite is the recommended default: it is listed first and preselected
  whenever the PHP sqlite3 extension is available. Otherwise the first
  available engine is preselected and the page says why.
*/

class eZStepDatabaseChoice extends eZStepInstaller
{
    /**
     * Constructor
     *
     * @param eZTemplate $tpl
     * @param eZHTTPTool $http
     * @param eZINI $ini
     * @param array $persistenceList
     */
    public function __construct( $tpl, $http, $ini, &$persistenceList )
    {
        parent::__construct( $tpl, $http, $ini, $persistenceList,
                                'database_choice', 'Database choice' );
    }

    /** @var bool|null tests: whether FFI may be used, instead of asking PHP (expDuckDBFFI::ffiUsable()) */
    public static $FFIUsable = null;

    function processPostData()
    {
        $databaseMap = eZSetupDatabaseMap();
        $type = $this->Http->hasPostVariable( 'eZSetupDatabaseType' ) ? $this->Http->postVariable( 'eZSetupDatabaseType' ) : '';
        if ( !is_string( $type ) || !isset( $databaseMap[$type] ) )
        {
            // Nothing (or something unknown) was posted: take the default the
            // page preselects, so the wizard never carries an empty choice on.
            $choice = $this->defaultChoice( $this->foundDatabaseTypes() );
            $type = $choice['type'];
        }
        if ( $type === null || !isset( $databaseMap[$type] ) )
            return false;
        $this->PersistenceList['database_info'] = $databaseMap[$type];
        return true;
    }

    /**
     * The database type the wizard recommends and preselects.
     *
     * Read from setup.ini [DatabaseSettings] DefaultType; SQLite when unset,
     * because it needs no database server and carries the bundled data.
     *
     * @return string
     */
    static function preferredDatabaseType()
    {
        $type = 'sqlite3';
        $ini = eZINI::instance( 'setup.ini' );
        if ( $ini->hasVariable( 'DatabaseSettings', 'DefaultType' ) )
        {
            $configured = trim( $ini->variable( 'DatabaseSettings', 'DefaultType' ) );
            $databaseMap = eZSetupDatabaseMap();
            if ( $configured !== '' && isset( $databaseMap[$configured] ) )
                $type = $configured;
        }
        return $type;
    }

    /**
     * The database types whose PHP extension the system check found, in
     * setup.ini order, limited to the ones the wizard knows.
     *
     * @return array
     */
    function foundDatabaseTypes()
    {
        $databaseMap = eZSetupDatabaseMap();
        $types = array();
        if ( isset( $this->PersistenceList['database_extensions']['found'] ) )
        {
            foreach ( (array)$this->PersistenceList['database_extensions']['found'] as $extension )
            {
                // the PHP extension of a driver in the kernel, by the type it gives
                // PDO_ODBC and PHP's odbc extension: a site over ODBC, one type per dialect (eZSetupODBCDatabaseMap())
                if ( $extension === 'pdo_odbc' || $extension === 'odbc' )
                {
                    foreach ( array_keys( eZSetupODBCDatabaseMap() ) as $type )
                        if ( isset( $databaseMap[$type] ) && !in_array( $type, $types ) )
                            $types[] = $type;
                    continue;
                }
                $extension = self::typeOfExtension( $extension );
                if ( $extension === '' )
                    continue;
                // pdo_ibm and ibm_db2 reach Db2 and, over DRDA, Informix
                if ( $extension === 'pdo_ibm' || $extension === 'ibm_db2' )
                {
                    foreach ( array( 'db2', 'informix' ) as $type )
                        if ( isset( $databaseMap[$type] ) && !in_array( $type, $types ) )
                            $types[] = $type;
                    continue;
                }
                if ( isset( $databaseMap[$extension] ) && !in_array( $extension, $types ) )
                    $types[] = $extension;
            }
        }
        return $types;
    }

    /**
     * The database type a PHP extension of setup.ini [database_extensions] gives, '' for none: the extension's name
     * when it is a type itself (mysqli, pgsql, sqlite3, mongodb, oci8), else the type of the kernel's driver that uses
     * it. sqlsrv and pdo_sqlsrv give SQL Server (expSQLServerDB), curl libSQL (expLibSQLDB, Turso listed after it). ffi
     * gives DuckDB (expDuckDBDB reaches libduckdb
     * through FFI) only when FFI may be used in this process: ffi.enable=preload, PHP's default, allows it on the
     * command line only, so a wizard in the web server would offer a database it cannot open.
     *
     * @param string $extension
     * @return string
     */
    static function typeOfExtension( $extension )
    {
        $extension = strtolower( (string)$extension );
        $types = array( 'pdo_firebird' => 'firebird', 'sqlsrv' => 'sqlserver', 'pdo_sqlsrv' => 'sqlserver', 'curl' => 'libsql' );
        if ( isset( $types[$extension] ) )
            return $types[$extension];
        if ( $extension === 'ffi' )
        {
            $usable = self::$FFIUsable !== null ? self::$FFIUsable : class_exists( 'expDuckDBFFI' ) && expDuckDBFFI::ffiUsable();
            return $usable ? 'duckdb' : '';
        }
        return $extension;
    }

    /**
     * Picks the default database type out of the available ones.
     *
     * @param array $types Available types, see foundDatabaseTypes()
     * @return array 'type' (null when nothing is available) and
     *               'preferred_missing' (true when the recommended type is
     *               not available and another one was taken instead)
     */
    function defaultChoice( $types )
    {
        $preferred = self::preferredDatabaseType();
        if ( in_array( $preferred, $types ) )
            return array( 'type' => $preferred, 'preferred_missing' => false );
        return array( 'type' => count( $types ) > 0 ? $types[0] : null,
                      'preferred_missing' => true );
    }

    function init()
    {
        $databaseMap = eZSetupDatabaseMap();

        if ( $this->hasKickstartData() )
        {
            $data = $this->kickstartData();
            $extension = isset( $data['Type'] ) ? $data['Type'] : self::preferredDatabaseType();
            $map = array( 'postgresql' => 'pgsql',
                          'mysql' => 'mysqli',
                          'sqlite' => 'sqlite3',
                          'oracle' => 'oci8',
                          'mssql' => 'sqlserver',
                          'sqlsrv' => 'sqlserver',
                          'planetscale' => 'vitess',
                          'memsql' => 'singlestore',
                          'aurora-mysql' => 'aurora',
                          'auroramysql' => 'aurora',
                          'fb' => 'firebird',
                          'pdo_firebird' => 'firebird',
                          'duckdb' => 'duckdb',
                          'sqld' => 'libsql',
                          'monetdb' => 'monetdb',
                          'cockroach' => 'cockroachdb',
                          'crdb' => 'cockroachdb',
                          'yugabyte' => 'yugabytedb',
                          'ysql' => 'yugabytedb',
                          'aurora-postgresql' => 'aurorapostgresql',
                          'ibm_db2' => 'db2',
                          'ifx' => 'informix',
                          'hana' => 'hana',
                          'saphana' => 'hana',
                          'ase' => 'ase',
                          'sybase' => 'ase',
                          'ezoracle' => 'oci8' );
            if ( isset( $map[$extension] ) )
                $extension = $map[$extension];
            // Type=odbc: the wizard type of the dialect ([database_choice] Dialect=sqlserver, postgresql, mysql or db2)
            if ( $extension === 'odbc' )
            {
                $odbcType = eZSetupODBCType( isset( $data['Dialect'] ) ? $data['Dialect'] : '' );
                if ( $odbcType !== null )
                    $extension = $odbcType;
            }

            if ( isset( $databaseMap[$extension] ) )
            {
                $this->PersistenceList['database_info'] = $databaseMap[$extension];
                return $this->kickstartContinueNextStep();
            }
        }

        $types = $this->foundDatabaseTypes();

        if ( eZSetupTestInstaller() == 'windows' )
        {
            $choice = $this->defaultChoice( $types );
            $type = $choice['type'] !== null ? $choice['type'] : 'mysqli';
            $this->PersistenceList['database_info'] = $databaseMap[$type];
            return true;
        }

        if ( count( $types ) != 1 )
        {
            return false;
        }

        $database = $databaseMap[$types[0]];
        $database['name'] = null;
        $this->PersistenceList['database_info'] = $database;

        return true;
    }

    function display()
    {
        $databaseMap = eZSetupDatabaseMap();
        $types = $this->foundDatabaseTypes();
        $choice = $this->defaultChoice( $types );
        $preferred = self::preferredDatabaseType();

        // The default goes first, the rest keep the order setup.ini gives them.
        if ( $choice['type'] !== null )
        {
            $types = array_values( array_diff( $types, array( $choice['type'] ) ) );
            array_unshift( $types, $choice['type'] );
        }

        $availableDatabases = array();
        $databaseList = array();
        foreach ( $types as $type )
        {
            $database = $databaseMap[$type];
            $database['recommended'] = ( $type === $preferred );
            $databaseList[] = $database;
            $availableDatabases[$type] = true;
            // the databases of the PostgreSQL wire protocol go with PHP's pgsql extension, after PostgreSQL, and
            // those of the MySQL protocol (TiDB, Vitess, SingleStore, Xpand, Aurora MySQL) with mysqli, after MySQL
            foreach ( $databaseMap as $variant => $entry )
            {
                if ( isset( $entry['php_extension'] ) && $entry['php_extension'] === $type && !isset( $availableDatabases[$variant] ) )
                {
                    $entry['recommended'] = false;
                    $databaseList[] = $entry;
                    $availableDatabases[$variant] = true;
                }
            }
        }

        $databaseInfo = $choice['type'] !== null ? $databaseMap[$choice['type']] : false;
        if ( isset( $this->PersistenceList['database_info']['type'] ) &&
             isset( $availableDatabases[$this->PersistenceList['database_info']['type']] ) )
        {
            // Coming back to this page keeps what was chosen before.
            $databaseInfo = $this->PersistenceList['database_info'];
        }

        $this->Tpl->setVariable( 'database_list', $databaseList );
        $this->Tpl->setVariable( 'database_info', $databaseInfo );
        $this->Tpl->setVariable( 'available_databases', $availableDatabases );
        $this->Tpl->setVariable( 'preferred_database', $databaseMap[$preferred] );
        $this->Tpl->setVariable( 'preferred_database_missing', $choice['preferred_missing'] );

        $result = array();
        // Display template
        $result['content'] = $this->Tpl->fetch( "design:setup/init/database_choice.tpl" );
        $result['path'] = array( array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                          'Database choice' ),
                                        'url' => false ) );
        return $result;
    }

}

?>
