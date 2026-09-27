<?php
/**
 * File containing the eZStepDatabaseInit class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZStepDatabaseInit ezstep_database_init.php
  \brief The class eZStepDatabaseInit does

*/

class eZStepDatabaseInit extends eZStepInstaller
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
        parent::__construct( $tpl, $http, $ini, $persistenceList, 'database_init', 'Database init' );
    }

    function processPostData()
    {
        $databaseMap = eZSetupDatabaseMap();
        // Get database parameters from input form.
        if ( $this->Http->hasPostVariable( 'eZSetupDatabaseServer' ) )
            $this->PersistenceList['database_info']['server'] = $this->Http->postVariable( 'eZSetupDatabaseServer' );
        if ( $this->Http->hasPostVariable( 'eZSetupDatabasePort' ) )
            $this->PersistenceList['database_info']['port'] = $this->Http->postVariable( 'eZSetupDatabasePort' );
        if ( $this->Http->hasPostVariable( 'eZSetupDatabaseName' ) )
            $this->PersistenceList['database_info']['dbname'] = $this->Http->postVariable( 'eZSetupDatabaseName' );
        if ( $this->Http->hasPostVariable( 'eZSetupDatabaseUser' ) )
            $this->PersistenceList['database_info']['user'] = $this->Http->postVariable( 'eZSetupDatabaseUser' );
        if ( $this->Http->hasPostVariable( 'eZSetupDatabaseSocket' ) )
            $this->PersistenceList['database_info']['socket'] = $this->Http->postVariable( 'eZSetupDatabaseSocket' );
        if ( !isset( $this->PersistenceList['database_info']['socket'] ) )
            $this->PersistenceList['database_info']['socket'] = false;
        if ( !isset( $this->PersistenceList['database_info']['database'] ) )
            $this->PersistenceList['database_info']['database'] = false;
        // SQLite's form has no server, port, user or password: every later
        // step reads them, so they are kept as empty values, not left out
        foreach ( array( 'server', 'port', 'user', 'password', 'dbname' ) as $key )
        {
            if ( !isset( $this->PersistenceList['database_info'][$key] ) )
                $this->PersistenceList['database_info'][$key] = '';
        }
        if ( $this->PersistenceList['database_info']['type'] == 'sqlite3' )
        {
            // A file, not a server: nothing of a server's is used or written
            // to the settings, whatever a form (or an earlier MySQL choice) left
            $this->resetSQLiteServerFields();
            $this->PersistenceList['database_info']['dbname'] = trim( $this->PersistenceList['database_info']['dbname'] );
            if ( $this->PersistenceList['database_info']['dbname'] === '' )
                $this->PersistenceList['database_info']['dbname'] = eZStepInstaller::SQLITE_DEFAULT_FILE_NAME;
        }

        $this->Error = 0;
        $dbStatus = false;

        // Get password
        $password = $this->PersistenceList['database_info']['password'];

        if ( $this->Http->hasPostVariable( 'eZSetupDatabasePassword' ) )
        {
            $password = $this->Http->postVariable( 'eZSetupDatabasePassword' );
            $this->PersistenceList['database_info']['password'] = $password;
        }

        $databaseChoice = false;

        // Check database connection
        $databaseInfo = $this->PersistenceList['database_info'];
        $databaseInfo['info'] = $databaseMap[$databaseInfo['type']];
        if ( isset( $this->PersistenceList['regional_info'] ) )
        {
            $regionalInfo = $this->PersistenceList['regional_info'];
        }
        else
        {
            $regionalInfo = '';
        }

        $this->PersistenceList['database_info']['password'] = $password;

        $result = $this->checkDatabaseRequirements( false );

        $this->PersistenceList['database_info']['version'] = $result['db_version'];
        if ( isset( $result['db_required_version'] ) )
            $this->PersistenceList['database_info']['required_version'] = $result['db_required_version'];
        if ( !$result['status'] )
        {
            $this->Error = $result['error_code'];
            $dbInfo = $this->PersistenceList['database_info'];
            $dbLog = "eZStepDatabaseInit: connection check failed (error code {$result['error_code']}) for server '{$dbInfo['server']}', user '{$dbInfo['user']}', db '{$dbInfo['dbname']}' (type '{$dbInfo['type']}')";
            eZLog::write( $dbLog, 'setup.log' );
            return false;
        }

        $db = $result['db_instance'];
        $this->PersistenceList['database_info']['use_unicode'] = $result['use_unicode'];

        // SQLite: the file named here is the site's database. It exists now
        // (opening it created it), and whether it holds tables already is
        // asked on the Site details page, with the choices MySQL gets there.
        if ( $databaseInfo['type'] == 'sqlite3' )
        {
            $this->PersistenceList['database_info_available'] = array( $this->PersistenceList['database_info']['dbname'] );
            return true;
        }

        // For MySQL/MariaDB, if the user explicitly provided a database name, we do
        // not need to enumerate available databases (which requires the global SHOW
        // DATABASES privilege and may access the 'mysql' system database). We can
        // simply use the named database directly.
        if ( in_array( $databaseInfo['type'], array( 'mysql', 'mysqli' ) ) &&
             !empty( $databaseInfo['dbname'] ) )
        {
            $this->PersistenceList['database_info_available'] = array( $databaseInfo['dbname'] );
            return true;
        }

        try
        {
            $availDatabases = $db->availableDatabases();
        }
        catch ( Exception $e )
        {
            $dbInfo = $this->PersistenceList['database_info'];
            eZLog::write( "eZStepDatabaseInit: availableDatabases() failed for server '{$dbInfo['server']}', user '{$dbInfo['user']}', db '{$dbInfo['dbname']}' (type '{$dbInfo['type']}') exception: " . get_class( $e ) . ' - ' . $e->getMessage(), 'setup.log' );
            $availDatabases = null;
        }

        if ( $availDatabases === false ) // not possible to determine if username and password is correct here
        {
            return true;
        }
        else if ( is_countable( $availDatabases ) && count( $availDatabases ) > 0 ) // login succeeded, and at least one database available
        {
            $this->PersistenceList['database_info_available'] = $availDatabases;
            return true;
        }
        else if ( $availDatabases === null && $db->isConnected() === true )
        {
            // availableDatabases() failed (e.g. SHOW DATABASES denied) but we are still
            // connected. If we have an explicit database name, use that; otherwise there
            // is genuinely no database available.
            if ( !empty( $databaseInfo['dbname'] ) )
            {
                $this->PersistenceList['database_info_available'] = array( $databaseInfo['dbname'] );
                return true;
            }
            $this->Error = eZStepInstaller::DB_ERROR_NO_DATABASES;
            return false;
        }

        $this->Error = eZStepInstaller::DB_ERROR_CONNECTION_FAILED;

        return false;
    }

    function init()
    {
        if ( $this->hasKickstartData() )
        {
            $data = $this->kickstartData();

            // Fill in database info in persistence list
            // This is needed for db requirement check
            $this->PersistenceList['database_info']['server'] = isset( $data['Server'] ) ? $data['Server'] : '';
            $this->PersistenceList['database_info']['port'] = isset( $data['Port'] ) ? $data['Port'] : '';
            $this->PersistenceList['database_info']['dbname'] = $data['Database'];
            $this->PersistenceList['database_info']['user'] = isset( $data['User'] ) ? $data['User'] : '';
            $this->PersistenceList['database_info']['password'] = isset( $data['Password'] ) ? $data['Password'] : '';
            $this->PersistenceList['database_info']['socket'] = isset( $data['Socket'] ) ? $data['Socket'] : '';
            $this->PersistenceList['database_info']['database'] = $data['Database'];

            $result = $this->checkDatabaseRequirements( false );

            $this->PersistenceList['database_info']['version'] = $result['db_version'];
            if ( isset( $result['db_required_version'] ) )
            {
                $this->PersistenceList['database_info']['required_version'] = $result['db_required_version'];
            }
            if ( !$result['status'] )
            {
                $this->Error = $result['error_code'];
                $dbInfo = $this->PersistenceList['database_info'];
                $dbLog = "eZStepDatabaseInit (kickstart): connection check failed (error code {$result['error_code']}) for server '{$dbInfo['server']}', user '{$dbInfo['user']}', db '{$dbInfo['dbname']}' (type '{$dbInfo['type']}')";
                eZLog::write( $dbLog, 'setup.log' );
                return false;
            }

            $this->PersistenceList['database_info']['use_unicode'] = $result['use_unicode'];

            return $this->kickstartContinueNextStep();
        }

        // If using windows installer, set standard values, and continue
/*        if ( eZSetupTestInstaller() == 'windows' )
        {
            $this->PersistenceList['database_info']['server'] = 'localhost';
            $this->PersistenceList['database_info']['user'] = 'root';
            $this->PersistenceList['database_info']['password'] = '';
            return true;
        }*/

        $config = eZINI::instance( 'setup.ini' );
        if ( !isset( $this->PersistenceList['database_info']['server'] ) or
             !$this->PersistenceList['database_info']['server'] )
            $this->PersistenceList['database_info']['server'] = $config->variable( 'DatabaseSettings', 'DefaultServer' );
        if ( !isset( $this->PersistenceList['database_info']['port'] ) or
             !$this->PersistenceList['database_info']['port'] )
            $this->PersistenceList['database_info']['port'] = $config->variable( 'DatabaseSettings', 'DefaultPort' );
        if ( !isset( $this->PersistenceList['database_info']['dbname'] ) or
             !$this->PersistenceList['database_info']['dbname'] )
            $this->PersistenceList['database_info']['dbname'] = $config->variable( 'DatabaseSettings', 'DefaultName' );

        if ( !isset( $this->PersistenceList['database_info']['user'] ) or
             !$this->PersistenceList['database_info']['user'] )
            $this->PersistenceList['database_info']['user'] = $config->variable( 'DatabaseSettings', 'DefaultUser' );
        if ( !isset( $this->PersistenceList['database_info']['password'] ) or
             !$this->PersistenceList['database_info']['password'] )
            $this->PersistenceList['database_info']['password'] = $config->variable( 'DatabaseSettings', 'DefaultPassword' );
        if ( !isset( $this->PersistenceList['database_info']['socket'] ) )
            $this->PersistenceList['database_info']['socket'] = '';

        if ( $this->PersistenceList['database_info']['type'] == 'sqlite3' )
        {
            // The server defaults above (localhost, root) are not SQLite's; the
            // file name is kept when one was given (the page shown again after
            // an error), except the server database's default name
            $this->resetSQLiteServerFields();
            $dbName = trim( (string)$this->PersistenceList['database_info']['dbname'] );
            if ( $dbName === '' or $dbName == $config->variable( 'DatabaseSettings', 'DefaultName' ) )
                $this->PersistenceList['database_info']['dbname'] = eZStepInstaller::SQLITE_DEFAULT_FILE_NAME;
        }

        if ( $this->Http->postVariable( 'eZSetup_current_step' ) == 'SiteDetails' ) // Failed to connect to tables in database
        {
            $this->Error = eZStepInstaller::DB_ERROR_CONNECTION_FAILED;
        }

        return false; // Always show database initialization
    }

    function display()
    {
        $databaseMap = eZSetupDatabaseMap();

        $dbError = 0;
        $dbNotEmpty = 0;
        if ( $this->Error )
        {
            $dbError = $this->databaseErrorInfo( array( 'error_code' => $this->Error,
                                                        'database_info' => $this->PersistenceList['database_info'] ) );
        }

        $databaseInfo = $this->PersistenceList['database_info'];
        $databaseInfo['info'] = $databaseMap[$databaseInfo['type']];
        $databaseInfo['table']['is_empty'] = $this->DBEmpty;
        if ( isset( $this->PersistenceList['regional_info'] ) )
        {
            $regionalInfo = $this->PersistenceList['regional_info'];
        }
        else
        {
            $regionalInfo = '';
        }

        $this->Tpl->setVariable( 'db_error', $dbError );
        $this->Tpl->setVariable( 'database_info', $databaseInfo );
        $this->Tpl->setVariable( 'regional_info', $regionalInfo );
        $this->Tpl->setVariable( 'db_not_empty', $dbNotEmpty );
        $this->Tpl->setVariable( 'database_directory', eZSQLite3DB::STORAGE_DIRECTORY );

        $result = array();
        // Display template
        $result['content'] = $this->Tpl->fetch( 'design:setup/init/database_init.tpl' );
        $result['path'] = array( array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                          'Database initialization' ),
                                        'url' => false ) );
        return $result;
    }

    public $Error = 0;
    public $DBEmpty = true;
}

?>
