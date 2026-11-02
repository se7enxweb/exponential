<?php
/**
 * File containing the ezpDatabaseHelper class
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class ezpDatabaseHelper
{
    /**
     * Constructs new ezpDatabaseHelper and sets default db instance.
     */
    function __construct()
    {
        $this->DefaultDB = eZDB::instance();
        self::setInstance( $this );
    }

    /**
     * Returns true if we have instance of the ezpDatabaseHelper object, else false.
     *
     * @return bool
     */
    static function hasInstance()
    {
        return isset( $GLOBALS['ezpDatabaseHelper'] ) &&
                      $GLOBALS['ezpDatabaseHelper'] instanceof ezpDatabaseHelper;
    }

    /**
     * Sets the global ezpDatabaseHelper instance to \a $instance.
     *
     * @param ezpDatabaseHelper $instance
     */
    static function setInstance( ezpDatabaseHelper $instance )
    {
        $GLOBALS['ezpDatabaseHelper'] = $instance;
    }

    /**
     * Returns an instance of the ezpDatabaseHelper object.
     *
     * @return ezpDatabaseHelper Instance of ezpDatabaseHelper
     */
    static function instance()
    {
        if ( isset( $GLOBALS['ezpDatabaseHelper'] ) )
        {
            $impl =& $GLOBALS['ezpDatabaseHelper'];

            return $impl;
        }
        else
        {
            $impl = new ezpDatabaseHelper();
            self::setInstance( $impl );
            return $impl;
        }
    }

    /**
     * The parameters eZDB::instance() gets for the DSN. A database type whose driver is an extension (oracle:
     * ezoracle) has that extension loaded first. The Oracle driver connects to the connect string in 'database',
     * so a DSN with a host (oracle://user:password@host:port/service) gets the Easy Connect string
     * //host:port/service; a DSN without one keeps the TNS alias or descriptor it names.
     *
     * @param ezpDsn $dsn
     * @return array
     */
    static function connectionParameters( ezpDsn $dsn )
    {
        self::loadDatabaseExtension( $dsn->phptype );
        $dbParams = $dsn->parts;
        $dbParams['use_defaults'] = false;
        if ( $dsn->phptype === 'oracle' && !empty( $dbParams['host'] ) && strpos( (string)$dbParams['database'], '/' ) === false )
        {
            $dbParams['database'] = '//' . $dbParams['host'] . ( !empty( $dbParams['port'] ) ? ':' . (int)$dbParams['port'] : '' )
                . '/' . $dbParams['database'];
        }
        return $dbParams;
    }

    /**
     * Loads the extension that holds the driver of the database type $phptype, when the type is not one of the
     * kernel (oracle: ezoracle): its driver and schema handler classes, and its settings (the ImplementationAlias
     * of site.ini, the schema handler and the column names of dbschema.ini), as an installation with the extension
     * active has them. The extension is the folder EXP_TEST_EZORACLE_PATH names, else extension/ezoracle of the
     * tree the tests run in, else that of the installation EXP_TEST_EXPLAYOUTS_PATH points at.
     *
     * @param string $phptype
     * @return bool false when the type needs an extension that was not found
     */
    static function loadDatabaseExtension( $phptype )
    {
        static $loaded = array();
        $drivers = array( 'oracle' => array( 'ezoracle', 'eZOracleDB', 'ezdb/dbms-drivers/ezoracledb.php',
                                             'eZOracleSchema', 'ezdb/dbms-schema/ezoracleschema.php' ) );
        if ( !isset( $drivers[$phptype] ) )
            return true;
        if ( isset( $loaded[$phptype] ) )
            return $loaded[$phptype];

        list( $extension, $driverClass, $driverFile, $schemaClass, $schemaFile ) = $drivers[$phptype];
        $dir = false;
        foreach ( array( (string)getenv( 'EXP_TEST_' . strtoupper( $extension ) . '_PATH' ),
                         eZExtension::baseDirectory() . '/' . $extension,
                         rtrim( (string)getenv( 'EXP_TEST_EXPLAYOUTS_PATH' ), '/' ) . '/' . $extension ) as $candidate )
        {
            if ( $candidate !== '' && $candidate !== '/' . $extension && is_file( $candidate . '/' . $driverFile ) )
            {
                $dir = realpath( $candidate );
                break;
            }
        }
        if ( $dir === false )
        {
            eZDebug::writeError( "The $extension extension, which the database type $phptype needs, was not found", __METHOD__ );
            return $loaded[$phptype] = false;
        }

        if ( !class_exists( $driverClass, false ) )
            require_once $dir . '/' . $driverFile;
        if ( !class_exists( $schemaClass, false ) )
            require_once $dir . '/' . $schemaFile;

        // the settings of the extension below the overrides, as an active extension has them
        $ini = eZINI::instance();
        if ( !in_array( $extension, $ini->variable( 'ExtensionSettings', 'ActiveExtensions' ) ) )
        {
            $ini->prependOverrideDir( $dir . '/settings', true, 'extension:' . $extension, 'extension' );
            $ini->load();
            foreach ( array( 'dbschema.ini', $extension . '.ini' ) as $iniFile )
            {
                if ( eZINI::isLoaded( $iniFile ) )
                    eZINI::resetInstance( $iniFile );
            }
        }
        $aliases = $ini->variable( 'DatabaseSettings', 'ImplementationAlias' );
        $aliases[$phptype] = $driverClass;
        $ini->setVariable( 'DatabaseSettings', 'ImplementationAlias', $aliases );

        // the schema handler from the folder found, wherever the tests run
        $schemaIni = eZINI::instance( 'dbschema.ini' );
        $paths = $schemaIni->variable( 'SchemaSettings', 'SchemaPaths' );
        $paths[$phptype] = $dir . '/' . $schemaFile;
        $schemaIni->setVariable( 'SchemaSettings', 'SchemaPaths', $paths );
        $classes = $schemaIni->variable( 'SchemaSettings', 'SchemaHandlerClasses' );
        $classes[$phptype] = $schemaClass;
        $schemaIni->setVariable( 'SchemaSettings', 'SchemaHandlerClasses', $classes );

        return $loaded[$phptype] = true;
    }

    /**
     * Returns a database handler which uses database $database
     *
     * @param ezpDsn $database
     * @return eZDB Instance of eZDB
     */
    static function useDatabase( ezpDsn $dsn, $makeDefaultInstance = true )
    {
        $dbParams = self::connectionParameters( $dsn );

        $db = eZDB::instance( $dsn->phptype, $dbParams, true );

        if ( $makeDefaultInstance )
            eZDB::setInstance( $db );

        return $db;
    }

    /**
     * Returns a ezdb instance which should be logged in as root
     *
     * @param ezpDsn $database
     * @return eZDB Instance of eZDB
     */
    static function dbAsRootInstance( ezpDsn $dsn )
    {
        $dbParams = self::connectionParameters( $dsn );

        $db = eZDB::instance( $dsn->phptype, $dbParams, true );

        return $db;
    }

    /**
     * Resets the database handler back to default
     *
     * @return eZDB Instance of eZDB
     */
    public function resetDatabaseHandler()
    {
        if ( !$this->DefaultDB->isConnected() )
            return false;

        eZDB::setInstance( $this->DefaultDB );

        return $this->DefaultDB;
    }
}

?>
