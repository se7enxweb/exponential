<?php
/**
 * File containing the eZStepSiteDetails class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZStepSiteDetails ezstep_site_details.php
  \brief The class eZStepSiteDetails does

*/

class eZStepSiteDetails extends eZStepInstaller
{
    const SITE_ACCESS_ILLEGAL = 11;

    const SITE_ACCESS_DEFAULT_REGEXP = '/^([a-zA-Z0-9_]*)$/';
    const SITE_ACCESS_HOSTNAME_REGEXP = '/^([a-zA-Z0-9.\-:]*)$/';
    const SITE_ACCESS_PORT_REGEXP = '/^([0-9]*)$/';

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
        parent::__construct( $tpl, $http, $ini, $persistenceList, 'site_details', 'Site details' );
    }

    function processPostData()
    {
        $databaseMap = eZSetupDatabaseMap();

        $databaseInfo = $this->PersistenceList['database_info'];
        $databaseInfo['info'] = $databaseMap[$databaseInfo['type']];
        $regionalInfo = $this->PersistenceList['regional_info'];

        $dbStatus = array();
        $dbDriver = $databaseInfo['info']['driver'];
        $dbServer = $databaseInfo['server'];
        $dbUser = $databaseInfo['user'];
        $dbSocket = $databaseInfo['socket'];
        $dbPwd = $databaseInfo['password'];

        $chosenDatabases = array();
        $siteAccessValues = array();
        $siteAccessValues['admin'] = 1; // Add user and admin as illegal site access values
        $siteAccessValues['user'] = 1;

        $siteType = $this->chosenSiteType();
        // This should not be run, it will remove the utf-8 choice from previous steps.
//        unset( $this->PersistenceList['regional_info']['site_charset'] );


        $siteType['title'] = $this->Http->postVariable( 'eZSetup_site_templates_title' );
        $siteType['url'] = $this->Http->postVariable( 'eZSetup_site_templates_url' );
        // the sender of the optional e-mail (mailpreferences.ini [FooterSettings]); both may stay empty
        $siteType['organisation_name'] = $this->Http->hasPostVariable( 'eZSetup_site_templates_organisation_name' )
                                       ? trim( (string)$this->Http->postVariable( 'eZSetup_site_templates_organisation_name' ) ) : '';
        $siteType['organisation_address'] = $this->Http->hasPostVariable( 'eZSetup_site_templates_organisation_address' )
                                          ? trim( (string)$this->Http->postVariable( 'eZSetup_site_templates_organisation_address' ) ) : '';

        $error = false;
        $userPath = $this->Http->postVariable( 'eZSetup_site_templates_value' );

        $regexp = self::SITE_ACCESS_DEFAULT_REGEXP;
        if ( $siteType['access_type'] == 'port' )
        {
            $regexp = self::SITE_ACCESS_PORT_REGEXP;
        }
        elseif ( $siteType['access_type'] == 'hostname' )
        {
            $regexp = self::SITE_ACCESS_HOSTNAME_REGEXP;
        }
        $validateUserPath = preg_match( $regexp, $userPath );

        if ( isset( $siteAccessValues[$userPath] ) or !$validateUserPath ) // check for equal site access values
        {
            $this->Error[0] = self::SITE_ACCESS_ILLEGAL;
            /* Check for valid host name */
            $userPath = ( ( $siteType['access_type'] == 'hostname' ) and ( strpos( $userPath, '_' ) !== false ) ) ? strtr( $userPath, '_', '-' ) : $userPath;
            $error = true;
        }

        // User siteaccess
        $siteType['access_type_value'] = $userPath;
        if ( $siteType['access_type_value'] == '' )
        {
            $this->Error[0] = self::SITE_ACCESS_ILLEGAL;
            return false;
        }

        $siteAccessValues[$siteType['access_type_value']] = 1;
        $adminPath = $this->Http->postVariable( 'eZSetup_site_templates_admin_value' );
        $validateAdminPath = preg_match( $regexp, $adminPath );

        if ( isset( $siteAccessValues[$adminPath] ) or !$validateAdminPath ) // check for equal site access values
        {
            $this->Error[0] = self::SITE_ACCESS_ILLEGAL;
            /* Check for valid host name */
            $adminPath = ( ( $siteType['access_type'] == 'hostname' ) and ( strpos( $adminPath, '_' ) !== false ) ) ? strtr( $adminPath, '_', '-' ) : $adminPath;
            $error = true;
        }

        // Admin siteaccess
        $siteType['admin_access_type_value'] = $adminPath;
        if ( $siteType['admin_access_type_value'] == '' )
        {
            $this->Error[0] = self::SITE_ACCESS_ILLEGAL;
            return false;
        }

        $siteAccessValues[$siteType['admin_access_type_value']] = 1;

        // Editor siteaccess: required, like the admin one
        $editorPath = $this->Http->hasPostVariable( 'eZSetup_site_templates_editor_value' )
                    ? trim( (string)$this->Http->postVariable( 'eZSetup_site_templates_editor_value' ) ) : '';
        if ( isset( $siteAccessValues[$editorPath] ) or !preg_match( $regexp, $editorPath ) ) // check for equal site access values
        {
            $this->Error[0] = self::SITE_ACCESS_ILLEGAL;
            /* Check for valid host name */
            $editorPath = ( ( $siteType['access_type'] == 'hostname' ) and ( strpos( $editorPath, '_' ) !== false ) ) ? strtr( $editorPath, '_', '-' ) : $editorPath;
            $error = true;
        }

        $siteType['editor_access_type_value'] = $editorPath;
        if ( $siteType['editor_access_type_value'] == '' )
        {
            $this->Error[0] = self::SITE_ACCESS_ILLEGAL;
            $this->storeSiteType( $siteType );
            return false;
        }

        $siteAccessValues[$siteType['editor_access_type_value']] = 1;

        $siteType['database'] = $this->Http->postVariable( 'eZSetup_site_templates_database' );

        if ( isset( $chosenDatabases[$siteType['database']] ) )
        {
            $this->Error[0] = eZStepInstaller::DB_ERROR_ALREADY_CHOSEN;
            $error = true;
        }

        $chosenDatabases[$siteType['database']] = 1;

        if ( $error )
        {
            $this->storeSiteType( $siteType );
            return false;
        }

        // Check database connection
        $result = $this->checkDatabaseRequirements( false,
                                                    array( 'database' => $siteType['database'] ) );

        if ( !$result['status'] )
        {
            $this->storeSiteType( $siteType );
            $this->Error[0] = array( 'type' => 'db',
                                            'error_code' => $result['error_code'] );
            $dbInfo = $this->PersistenceList['database_info'];
            $dbLog = "eZStepSiteDetails: database requirement check failed (error code {$result['error_code']}) for site '{$siteType['database']}', server '{$dbInfo['server']}', user '{$dbInfo['user']}', db '{$dbInfo['dbname']}' (type '{$dbInfo['type']}')";
            eZLog::write( $dbLog, 'setup.log' );
            return false;
        }
        // Store charset if found
        if ( $result['site_charset'] )
        {
            $this->PersistenceList['regional_info']['site_charset'] = $result['site_charset'];
        }

        $db = $result['db_instance'];

        $dbStatus['connected'] = $result['connected'];

        $dbError = false;
        $demoDataResult = true;
        if ( $dbStatus['connected'] && $db instanceof eZDBInterface )
        {

            $this->TableCount = count( $db->eZTableList() );
            if ( $this->TableCount != 0 )
            {
                if ( $this->Http->hasPostVariable( 'eZSetup_site_templates_existing_database' ) &&
                     $this->Http->postVariable( 'eZSetup_site_templates_existing_database' ) != eZStepInstaller::DB_DATA_CHOOSE )
                {
                    $siteType['existing_database'] = $this->Http->postVariable( 'eZSetup_site_templates_existing_database' );
                }
                else
                {
                    $this->Error[0] = eZStepInstaller::DB_ERROR_NOT_EMPTY ;
                }
            }
        }
        else
        {
            return 'DatabaseInit';
        }

        $this->storeSiteType( $siteType );

        return ( count( $this->Error ) == 0 );
    }

    function init()
    {
        if ( $this->hasKickstartData() )
        {
            $data = $this->kickstartData();

            $siteType = $this->chosenSiteType();

            $portCounter = 8080;

            if ( isset( $data['Title'] ) )
                $siteType['title'] = $data['Title'];

            if ( !$siteType['title'] )
                $siteType['title'] = $siteType['name'];

            // kickstart.ini [site_details] OrganisationName, OrganisationAddress (the address may use \n)
            $siteType['organisation_name'] = isset( $data['OrganisationName'] ) ? trim( (string)$data['OrganisationName'] ) : '';
            $siteType['organisation_address'] = isset( $data['OrganisationAddress'] ) ? str_replace( '\n', "\n", trim( (string)$data['OrganisationAddress'] ) ) : '';

            $siteType['url'] = isset( $data['URL'] ) ? $data['URL'] : false;
            if ( strlen( $siteType['url'] ) == 0 )
                $siteType['url'] = 'http://' . eZSys::hostName() . eZSys::indexDir( false );

            switch ( $siteType['access_type'] )
            {
                case 'port':
                {
                    // Change access port for user site, if not use default which is the current value of $portCoutner
                    if ( isset( $data['AccessPort'] ) )
                        $siteType['access_type_value'] = $data['AccessPort'];
                    else
                        $siteType['access_type_value'] = $portCounter++;

                    // Change access port for admin site, if not use default which is the current value of $portCoutner
                    if ( isset( $data['AdminAccessPort'] ) )
                        $siteType['admin_access_type_value'] = $data['AdminAccessPort'];
                    else
                        $siteType['admin_access_type_value'] = $portCounter++;

                    $siteType['editor_access_type_value'] = isset( $data['EditorAccessPort'] ) ? $data['EditorAccessPort'] : $portCounter++;
                }
                break;

                case 'hostname':
                {
                    if ( isset( $data['AccessHostname'] ) )
                        $siteType['access_type_value'] = $data['AccessHostname'];
                    else
                        $siteType['access_type_value'] = $siteType['identifier'] . '.' . eZSys::hostName();

                    if ( isset( $data['AdminAccessHostname'] ) )
                        $siteType['admin_access_type_value'] = $data['AdminAccessHostname'];
                    else
                        $siteType['admin_access_type_value'] = $siteType['identifier'] . '-admin.' . eZSys::hostName();

                    $siteType['editor_access_type_value'] = isset( $data['EditorAccessHostname'] ) ? $data['EditorAccessHostname'] : eZStepSiteAccess::defaultEditorAccessValue( 'hostname' );
                }
                break;

                default:
                {
                    // Change access name for user site, if not use default which is the identifier
                    if ( isset( $data['Access'] ) )
                        $siteType['access_type_value'] = $data['Access'];
                    else
                        $siteType['access_type_value'] = $siteType['identifier'];

                    // Change access name for admin site, if not use default which is the identifier + _admin
                    if ( isset( $data['AdminAccess'] ) )
                        $siteType['admin_access_type_value'] = $data['AdminAccess'];
                    else
                        $siteType['admin_access_type_value'] = $siteType['identifier'] . '_admin';

                    $siteType['editor_access_type_value'] = isset( $data['EditorAccess'] ) ? $data['EditorAccess'] : eZStepSiteAccess::defaultEditorAccessValue( 'url' );
                }
                break;
            }

            $siteType['database'] = $data['Database'];
            $action = eZStepInstaller::DB_DATA_APPEND;
            $map = array( 'ignore' => 1,
                          'remove' => 2,
                          'skip' => 3 );
            // Figure out what to do with database, do we need cleanup etc?
            if ( isset( $map[$data['DatabaseAction']] ) )
                $action = $map[$data['DatabaseAction']];
            $siteType['existing_database'] = $action;

            $chosenDatabases[$siteType['database']] = 1;

            $result = $this->checkDatabaseRequirements( false,
                                                        array( 'database' => $siteType['database'] ) );

            if ( !$result['status'] )
            {
                $this->Error[0] = array( 'type' => 'db',
                                                'error_code' => $result['error_code'] );
                return false;
            }

            // Store charset if found
            if ( $result['site_charset'] )
            {
                $this->PersistenceList['regional_info']['site_charset'] = $result['site_charset'];
            }

            $this->storeSiteType( $siteType );

            return $this->kickstartContinueNextStep();
        }

        // Get available databases
        $databaseMap = eZSetupDatabaseMap();
        $databaseInfo = $this->PersistenceList['database_info'];
        $databaseInfo['info'] = $databaseMap[$databaseInfo['type']];
        $regionalInfo = $this->PersistenceList['regional_info'];

        $demoDataResult = false;

        $dbStatus = array();
        $dbDriver = $databaseInfo['info']['driver'];
        $dbServer = $databaseInfo['server'];
        $dbPort = $databaseInfo['port'];

        // SQLite: the database is the file chosen on the Database page, and
        // nothing has to be opened to know it. The page asks for a file name
        // (it may be a new one); the files already there are shown beside it.
        if ( $databaseInfo['info']['type'] == 'sqlite3' )
        {
            $this->PersistenceList['database_info']['database'] = $databaseInfo['dbname'];
            $this->PersistenceList['database_info_available'] = array( $databaseInfo['dbname'] );
            return false; // Always show site details
        }

        // For MySQL/MariaDB, always prefer the explicitly typed database name.
        // Falling back to the 'mysql' system database requires root/DBA privileges
        // and breaks on shared hosting where the application user only has access
        // to their own database. If no name is provided, we leave the database
        // parameter empty so we connect to the server without selecting a DB.
        if ( in_array( $databaseInfo['info']['type'], array( 'mysql', 'mysqli' ) ) )
        {
            $explicitName = isset( $databaseInfo['dbname'] ) ? trim( $databaseInfo['dbname'] ) : '';
            $dbName = $explicitName !== '' ? $explicitName : '';
        }
        else
            $dbName = isset( $databaseInfo['dbname'] ) ? $databaseInfo['dbname'] : $databaseInfo['database'];

        $dbUser = $databaseInfo['user'];
        $dbSocket = $databaseInfo['socket'];
        if ( trim( $dbSocket ) == '' )
            $dbSocket = false;
        $dbPwd = $databaseInfo['password'];
        $dbCharset = 'iso-8859-1';
        $dbParameters = array( 'server' => $dbServer,
                               'port' => $dbPort,
                               'user' => $dbUser,
                               'password' => $dbPwd,
                               'socket' => $dbSocket,
                               'database' => $dbName,
                               'charset' => $dbCharset );

        // PostgreSQL requires us to specify a database name: the one typed on
        // the database page, as for MySQL. Only without one is template1 (it
        // exists on all PostgreSQL installations) opened to list the databases,
        // and the list is then offered. Listing them otherwise put the server's
        // first database in the field, not the one that was typed.
        if( $databaseInfo['info']['type'] == 'pgsql' )
        {
            $explicitName = isset( $databaseInfo['dbname'] ) ? trim( (string)$databaseInfo['dbname'] ) : '';
            $dbParameters['database'] = $explicitName !== '' ? $explicitName : 'template1';
        }

        if( $dbParameters['database'] != '' && $databaseInfo['info']['type'] == 'sqlite3' )
            $dbParameters['database'] = $dbName;

        $this->PersistenceList['database_info']['database'] = $dbParameters['database'];

        try
        {
            $db = eZDB::instance( $dbDriver, $dbParameters, true );
        }
        catch ( eZDBNoConnectionException $e )
        {
            // The server could not be reached at all. Leave the database list empty
            // so the user can still edit the form and try again.
            $this->PersistenceList['database_info_available'] = array();
            eZLog::write( "eZStepSiteDetails: eZDBNoConnectionException for server '{$dbParameters['server']}', user '{$dbParameters['user']}', db '{$dbParameters['database']}': " . $e->getMessage(), 'setup.log' );
            return false;
        }
        catch ( Exception $e )
        {
            // Any other connection error (e.g. ErrorException from a MySQLi warning)
            // should not be fatal; log it and let the user correct the form.
            $this->PersistenceList['database_info_available'] = array();
            eZLog::write( "eZStepSiteDetails: unexpected exception for server '{$dbParameters['server']}', user '{$dbParameters['user']}', db '{$dbParameters['database']}': " . get_class( $e ) . ' - ' . $e->getMessage(), 'setup.log' );
            return false;
        }

        // For MySQL/MariaDB, use the named database directly and skip the
        // SHOW DATABASES call (which requires global privileges and may access
        // the 'mysql' system database). This makes shared-hosting installs work.
        if ( in_array( $databaseInfo['info']['type'], array( 'mysql', 'mysqli', 'pgsql' ) ) &&
             isset( $databaseInfo['dbname'] ) && trim( $databaseInfo['dbname'] ) !== '' )
        {
            $this->PersistenceList['database_info_available'] = array( trim( $databaseInfo['dbname'] ) );
            return false;
        }

        try
        {
            $availDatabases = $db->availableDatabases();
        }
        catch ( Exception $e )
        {
            $availDatabases = null;
        }

        if ( is_countable( $availDatabases ) && count( $availDatabases ) > 0 )
        {
            $this->PersistenceList['database_info_available'] = $availDatabases;
        }
        else if ( is_countable( $availDatabases ) && count( $availDatabases ) == 0 )
        {
            // The server returned an empty list of databases, but the connection
            // succeeded. Provide an empty list so the template does not fail.
            $this->PersistenceList['database_info_available'] = array();
        }
        else if ( $availDatabases === null && $db->isConnected() === true && !empty( $databaseInfo['dbname'] ) )
        {
            // SHOW DATABASES may be denied on shared hosting; fall back to the
            // database name the user provided earlier.
            $this->PersistenceList['database_info_available'] = array( $databaseInfo['dbname'] );
        }

        return false; // Always show site details
    }

    function display()
    {
        $config = eZINI::instance( 'setup.ini' );
        $siteType = $this->chosenSiteType();

        // SQLite: the file named on the Database page, unless this page was
        // answered with another one already (shown again after an error)
        $isSQLite = $this->PersistenceList['database_info']['type'] == 'sqlite3';
        $sqliteFileName = $isSQLite && trim( (string)$this->PersistenceList['database_info']['dbname'] ) !== ''
                        ? $this->PersistenceList['database_info']['dbname']
                        : eZStepInstaller::SQLITE_DEFAULT_FILE_NAME;
        if ( $isSQLite && ( !isset( $siteType['database'] ) || trim( (string)$siteType['database'] ) === '' ) )
            $siteType['database'] = $sqliteFileName;

        $availableDatabaseList = array();
        if ( isset( $this->PersistenceList['database_info_available'] ) && is_array( $this->PersistenceList['database_info_available'] ) )
        {
            $availableDatabaseList = $this->PersistenceList['database_info_available'];
        }
        $databaseList = $availableDatabaseList;
        $databaseCounter = 0;

        if ( !isset( $siteType['title'] ) )
            $siteType['title'] = $siteType['name'];
        // a wizard started before the editor siteaccess existed has no value yet
        if ( !isset( $siteType['editor_access_type_value'] ) || trim( (string)$siteType['editor_access_type_value'] ) === '' )
            $siteType['editor_access_type_value'] = eZStepSiteAccess::defaultEditorAccessValue( isset( $siteType['access_type'] ) ? $siteType['access_type'] : 'url' );
        $siteType['errors'] = array();

        // The wizard's forms post to index.php by name, so indexDir() named it
        // too and every site got SiteURL=<host>/index.php (and canonical links
        // with /index.php/ in them). The site is served with URL rewriting (the
        // shipped .htaccess, Velocity), as the kickstarter's default assumes.
        $siteType['url'] = ( eZSys::isSSLNow() ? 'https://' : 'http://' ) . eZSys::hostName() . eZSys::wwwDir();

        if ( !isset( $siteType['site_access_illegal'] ) )
            $siteType['site_access_illegal'] = false;
        if ( !isset( $siteType['db_already_chosen'] ) )
            $siteType['db_already_chosen'] = false;
        if ( !isset( $siteType['db_not_empty'] ) )
            $siteType['db_not_empty'] = 0;
        if ( !isset( $siteType['database'] ) )
        {
            if ( is_array( $databaseList ) && count( $databaseList ) > 0 )
            {
                $matchedDBName = false;
                // First the database typed on the database page, when it is offered
                $typedName = isset( $this->PersistenceList['database_info']['dbname'] ) ? trim( (string)$this->PersistenceList['database_info']['dbname'] ) : '';
                if ( $typedName !== '' && in_array( $typedName, $databaseList ) )
                    $matchedDBName = $typedName;
                // Then a database named like the site
                foreach ( $matchedDBName ? array() : $databaseList as $databaseName )
                {
                    $dbName = trim( strtolower( $databaseName ) );
                    $identifier = trim( strtolower( $siteType['identifier'] ) );
                    if ( $dbName == $identifier )
                    {
                        $matchedDBName = $databaseName;
                        break;
                    }
                }
                if ( !$matchedDBName )
                    $matchedDBName = $databaseList[$databaseCounter++];
                $databaseList = array_values( array_diff( $databaseList, array( $matchedDBName ) ) );
                $siteType['database'] = $matchedDBName;
            }
            else
            {
                $siteType['database'] = '';
            }
        }
        if ( !isset( $siteType['existing_database'] ) )
        {
            $siteType['existing_database'] = eZStepInstaller::DB_DATA_APPEND;
        }

        $this->Tpl->setVariable( 'db_not_empty', 0 );
        $this->Tpl->setVariable( 'db_already_chosen', 0 );
        $this->Tpl->setVariable( 'db_charset_differs', 0 );
        $this->Tpl->setVariable( 'site_access_illegal', 0 );
        $this->Tpl->setVariable( 'site_access_illegal_name', 0 );

        // TODO: remove sites error array

        if ( isset( $this->Error[0] ) )
        {
            $error = $this->Error[0];

            $type = 'site';
            if ( is_array( $error ) )
            {
                $type = $error['type'];
                $error = $error['error_code'];
            }
            if ( $type == 'site' )
            {
                switch ( $error )
                {
                    case eZStepInstaller::DB_ERROR_NOT_EMPTY:
                    {
                        $this->Tpl->setVariable( 'db_not_empty', 1 );
                        $siteType['db_not_empty'] = 1;
                    } break;

                    case eZStepInstaller::DB_ERROR_ALREADY_CHOSEN:
                    {
                        $this->Tpl->setVariable( 'db_already_chosen', 1 );
                        $siteType['db_already_chosen'] = 1;
                    } break;

                    case self::SITE_ACCESS_ILLEGAL:
                    {
                        $this->Tpl->setVariable( 'site_access_illegal', 1 );
                        $siteType['site_access_illegal'] = 1;
                    } break;
                }
            }
            else if ( $type == 'db' )
            {
                if ( $error == eZStepInstaller::DB_ERROR_CHARSET_DIFFERS )
                    $this->Tpl->setVariable( 'db_charset_differs', 1 );
                $siteType['errors'][] = $this->databaseErrorInfo( array( 'error_code' => $error,
                                                                         'database_info' => $this->PersistenceList['database_info'],
                                                                         'site_type' => $siteType ) );
            }
        }
        $this->storeSiteType( $siteType );

        $sqliteFiles = array();
        if ( $isSQLite )
        {
            $this->Tpl->setVariable( 'database_default', $sqliteFileName );
            // A name to type, which may be a new file: no drop-down of the
            // files there are, which offered only those
            $availableDatabaseList = array();
            $sqliteFiles = eZSQLite3DB::availableDatabasesIn( eZSQLite3DB::STORAGE_DIRECTORY );
        }
        else
        {
            $this->Tpl->setVariable( 'database_default', $config->variable( 'DatabaseSettings', 'DefaultName' ) );
        }
        $this->Tpl->setVariable( 'database_is_file', $isSQLite );
        $this->Tpl->setVariable( 'database_directory', eZSQLite3DB::STORAGE_DIRECTORY );
        $this->Tpl->setVariable( 'database_files', $sqliteFiles );
        $this->Tpl->setVariable( 'database_table_count', $this->TableCount );

        $this->Tpl->setVariable( 'database_available', $availableDatabaseList );
        $this->Tpl->setVariable( 'site_type', $siteType );

        // Return template and data to be shown
        $result = array();
        // Display template
        $result['content'] = $this->Tpl->fetch( 'design:setup/init/site_details.tpl' );
        $result['path'] = array( array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                          'Site details' ),
                                        'url' => false ) );
        return $result;
    }

    public $Error = array();
    // Tables in the chosen database, counted when this page was answered
    public $TableCount = 0;
}

?>
