<?php
/**
 * File containing the eZStepInstaller class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZStepInstaller ezstep_class_definition.ph
  \brief The class EZStepInstaller provide a framework for eZStep installer classes

*/
class eZStepInstaller
{
    const DB_ERROR_EMPTY_PASSWORD = 1;
    const DB_ERROR_NONMATCH_PASSWORD = 2;
    const DB_ERROR_CONNECTION_FAILED = 3;
    const DB_ERROR_NOT_EMPTY = 4;
    const DB_ERROR_NO_DATABASES = 5;
    const DB_ERROR_NO_DIGEST_PROC = 6;
    const DB_ERROR_VERSION_INVALID = 7;
    const DB_ERROR_CHARSET_DIFFERS = 8;
    const DB_ERROR_ALREADY_CHOSEN = 10;
    // SQLite: the database is a file, and these are what stands in the way of it
    const DB_ERROR_SQLITE_FILE_NAME = 21;
    const DB_ERROR_SQLITE_DIRECTORY_NOT_WRITABLE = 22;
    const DB_ERROR_SQLITE_FILE_NOT_WRITABLE = 23;
    const DB_ERROR_SQLITE_NOT_A_DATABASE = 24;

    /**
     * The name a SQLite database file may have in the wizard: a plain file
     * name in the driver's storage directory, with one of the extensions
     * neither the shipped .htaccess nor Velocity serves as a file.
     */
    const SQLITE_FILE_NAME_REGEXP = '/^[A-Za-z0-9][A-Za-z0-9_.-]{0,99}\.(db|db3|sqlite|sqlite3)$/';

    /** The database file a kickstarter SQLite install uses (kickstart.ini [database_init] Database) */
    const SQLITE_DEFAULT_FILE_NAME = 'sqlite.db';

    /**
     * The language the bundled clean data (share/db_data.dba) is written in.
     *
     * Its objects, attributes, names, class names and URL aliases all carry
     * this locale, and the ids and masks in it assume that this language is
     * the first one the database gets (id 2). Nothing in the setup rewrites
     * that data into another language, so the installation always keeps it
     * as a content language - also when another primary language is chosen,
     * which then becomes the site's language with this one as its fallback.
     */
    const CLEAN_DATA_LANGUAGE = 'eng-US';

    const DB_DATA_APPEND = 1;
    const DB_DATA_REMOVE = 2;
    const DB_DATA_KEEP = 3;
    const DB_DATA_CHOOSE = 4;

    /**
     * Default constructor for eZ Publish installer classes
     *
     * @param \eZTemplate $tpl
     * @param \eZHTTPTool $http
     * @param \eZINI $ini
     * @param array $persistenceList
     * @param string $identifier
     * @param string $name
     */
    public function __construct( eZTemplate $tpl, eZHTTPTool $http, eZINI $ini, array &$persistenceList,
                              $identifier, $name )
    {
        $this->Tpl = $tpl;
        $this->Http = $http;
        $this->Ini = $ini;
        $this->PersistenceList =& $persistenceList;
        $this->Identifier = $identifier;
        $this->Name = $name;
        $this->INI = eZINI::instance( 'kickstart.ini', '.' );
        $this->KickstartData = false;

        $this->PersistenceList['use_kickstart'][$identifier] = true;

        // If we have read data for this step earlier we do not use kickstart
        if ( isset( $this->PersistenceList['kickstart'][$identifier] ) and
             $this->PersistenceList['kickstart'][$identifier] )
        {
            $this->PersistenceList['use_kickstart'][$identifier] = false;
        }

        if ( $this->INI->hasGroup( $this->Identifier ) )
        {
            $this->KickstartData = $this->INI->group( $this->Identifier );
            $this->PersistenceList['kickstart'][$identifier] = true;
        }
    }

    /**
     * Processespost data from this class.
     *
     * Abstract (virtual) method for step classes to use.
     *
     * @return bool True if post data accepted, or false if post data is rejected.
     */
    function processPostData()
    {
    }

    /*!
     \virtual

    Performs test needed by this class.

    This class may access class variables to store data needed for viewing if output failed
    \return true if all tests passed and continue with next default step,
            number of next step if all tests passed and next step is "hard coded",
           false if tests failed
    */
    function init()
    {
    }

    /**
     * Virtual (abstract)
     *
     * Display information and forms needed to pass this step.
     * return result to use in template.
     *
     * @return array
     */
    function display()
    {
        $result = array();
        return $result;
    }

    /**
     * @param \eZLocale $primaryLanguage
     * @param \eZLocale[]|null $allLanguages
     * @param bool $canUseUnicode
     * @return bool|string
     */
    function findAppropriateCharset( $primaryLanguage, $allLanguages, $canUseUnicode )
    {
        $commonCharsets = array();

        if ( is_array( $allLanguages ) and count( $allLanguages ) > 0 )
        {

            $language = $allLanguages[ 0 ];
            $charsets = $language->allowedCharsets();
            foreach ( $charsets as $charset )
            {
                $commonCharsets[] = eZCharsetInfo::realCharsetCode( $charset );
            }
            $commonCharsets = array_unique( $commonCharsets );

            for ( $i = 1; $i < count( $allLanguages ); ++$i )
            {
                $language = $allLanguages[$i];
                $charsets = $language->allowedCharsets();
                $realCharsets = array();
                foreach ( $charsets as $charset )
                {
                    $realCharsets[] = eZCharsetInfo::realCharsetCode( $charset );
                }
                $realCharsets = array_unique( $realCharsets );
                $commonCharsets = array_intersect( $commonCharsets, $realCharsets );
            }
        }
        $usableCharsets = array_values( $commonCharsets );
        $charset = false;
        if ( count( $usableCharsets ) > 0 )
        {
            if ( in_array( eZCharsetInfo::realCharsetCode( $primaryLanguage->charset() ), $usableCharsets ) )
                $charset = eZCharsetInfo::realCharsetCode( $primaryLanguage->charset() );
            else // Pick the first charset
                $charset = $usableCharsets[0];
        }
        else
        {
            if ( $canUseUnicode )
            {
                $charset = eZCharsetInfo::realCharsetCode( 'utf-8' );
            }
//          else
//          {
//              // Pick preferred primary language
//              $charset = $primaryLanguage->charset();
//          }
        }
        return $charset;
    }

    /**
     * @param \eZLocale $primaryLanguage
     * @param \eZLocale[]|null $allLanguages
     * @param bool $canUseUnicode
     * @return array
     */
    function findAppropriateCharsetsList( $primaryLanguage, $allLanguages, $canUseUnicode )
    {
        $commonCharsets = array();

        if ( is_array( $allLanguages ) and count( $allLanguages ) > 0 )
        {

            $language = $allLanguages[ 0 ];
            $charsets = $language->allowedCharsets();
            foreach ( $charsets as $charset )
            {
                $commonCharsets[] = eZCharsetInfo::realCharsetCode( $charset );
            }
            $commonCharsets = array_unique( $commonCharsets );

            for ( $i = 1; $i < count( $allLanguages ); ++$i )
            {
                $language = $allLanguages[$i];
                $charsets = $language->allowedCharsets();
                $realCharsets = array();
                foreach ( $charsets as $charset )
                {
                    $realCharsets[] = eZCharsetInfo::realCharsetCode( $charset );
                }
                $realCharsets = array_unique( $realCharsets );
                $commonCharsets = array_intersect( $commonCharsets, $realCharsets );
            }
        }
        $usableCharsets = array_values( $commonCharsets );

        if ( count( $usableCharsets ) > 0 )
        {
            if ( in_array( $primaryLanguage->charset(), $usableCharsets ) )
            {
                array_unshift( $usableCharsets, $primaryLanguage->charset() );
                $usableCharsets = array_unique( $usableCharsets );
            }
        }
        else
        {
            if ( $canUseUnicode )
            {
                $usableCharsets[] = eZCharsetInfo::realCharsetCode( 'utf-8' );
            }
        }

        return $usableCharsets;
    }

    /**
     * @return array
     */
    function availableSitePackages()
    {
        $packageList = eZPackage::fetchPackages( array(), array( 'type' => 'site' ) );

        return $packageList;
    }

    /**
     * @return array
     */
    function extraDataList()
    {
        return array( 'title', 'url', 'database',
                      'access_type', 'access_type_value', 'admin_access_type_value',
                      'existing_database' );
    }

    /**
     * @return bool
     */
    function chosenSitePackage()
    {
        if ( isset( $this->PersistenceList['chosen_site_package']['0'] ) )
        {
            return $this->PersistenceList['chosen_site_package']['0'];
        }
        else
            return false;
    }

    /**
     * @return array
     */
    function chosenSiteType()
    {
        if ( isset( $this->PersistenceList['chosen_site_package']['0'] ) )
        {
            $siteTypeIdentifier = $this->PersistenceList['chosen_site_package']['0'];
            $chosenSiteType['identifier'] = $siteTypeIdentifier;
            $extraList = $this->extraDataList();

            foreach ( $extraList as $extraItem )
            {
                if ( isset( $this->PersistenceList['site_extra_data_' . $extraItem][$siteTypeIdentifier] ) )
                {
                    $chosenSiteType[$extraItem] = $this->PersistenceList['site_extra_data_' . $extraItem][$siteTypeIdentifier];
                }
            }
        }
        return $chosenSiteType;
    }

    /**
     * @param string $sitePackageName
     * @return bool
     */
    function selectSiteType( $sitePackageName )
    {
        $package = eZPackage::fetch( $sitePackageName );
        if ( !$package )
            return false;

        $this->PersistenceList['chosen_site_package']['0'] = $sitePackageName;

        $this->PersistenceList['site_extra_data_title'][$sitePackageName] = $package->attribute('summary');
        return true;
    }

    /**
     * @param array $siteType
     */
    function storeSiteType( $siteType )
    {
        $extraList = $this->extraDataList();
        $siteIdentifier = $siteType['identifier'];
        foreach ( $extraList as $extraItem )
        {
            if ( isset( $siteType[$extraItem] ) )
            {
                $this->PersistenceList['site_extra_data_' . $extraItem][$siteIdentifier] = $siteType[$extraItem];
            }
        }
        $this->PersistenceList['chosen_site_package']['0'] = $siteIdentifier;
        if ( $this->hasKickstartData() )
            $this->storePersistenceData();
    }

    /**
     *
     */
    function storePersistenceData()
    {
        foreach ( $this->PersistenceList as $key => $value )
        {
            eZSetupSetPersistencePostVariable( $key, $value );
        }
    }

    function storeExtraSiteData( $siteIdentifier, $dataIdentifier, $value )
    {
        if ( !isset( $this->PersistenceList['site_extra_data_' . $dataIdentifier] ) )
            $this->PersistenceList['site_extra_data_' . $dataIdentifier] = array();
        $this->PersistenceList['site_extra_data_' . $dataIdentifier][$siteIdentifier] = $value;
    }

    /**
     * @param string $dataIdentifier
     * @return bool
     */
    function extraData( $dataIdentifier )
    {
        if ( isset( $this->PersistenceList['site_extra_data_' . $dataIdentifier] ) )
            return $this->PersistenceList['site_extra_data_' . $dataIdentifier];
        return false;
    }

    /**
     * @param string $siteIdentifier
     * @param string $dataIdentifier
     * @return bool
     */
    function extraSiteData( $siteIdentifier, $dataIdentifier )
    {
        if ( isset( $this->PersistenceList['site_extra_data_' . $dataIdentifier][$siteIdentifier] ) )
            return $this->PersistenceList['site_extra_data_' . $dataIdentifier][$siteIdentifier];
        return false;
    }

    /**
     * @param string|bool $dbCharset Default charset used if false
     * @param array $overrideDBParameters
     * @return array
     */
    function checkDatabaseRequirements( $dbCharset = false, $overrideDBParameters = array() )
    {
        $result = array( 'error_code' => false,
                         'use_unicode' => false,
                         'db_version' => false,
                         'db_require_version' => false,
                         'site_charset' => false,
                         'status' => false );

        $databaseMap = eZSetupDatabaseMap();
        $databaseInfo = $this->PersistenceList['database_info'];
        $databaseInfo['info'] = $databaseMap[$databaseInfo['type']];

        $dbDriver = $databaseInfo['info']['driver'];

        if ( $dbCharset === false )
            $dbCharset = 'iso-8859-1';
        $dbParameters = array( 'server' => $databaseInfo['server'],
                               'port' => $databaseInfo['port'],
                               'user' => $databaseInfo['user'],
                               'password' => $databaseInfo['password'],
                               'socket' => trim( $databaseInfo['socket'] ) == '' ? false : $databaseInfo['socket'],
                               'database' => $databaseInfo['database'],
                               'charset' => $dbCharset );
        $dbParameters = array_merge( $dbParameters, $overrideDBParameters );

        // SQLite requires us to specifiy a database name
        if( $dbParameters['database'] == '' and $this->PersistenceList['database_info']['type'] == 'sqlite3' )
            $dbParameters['database'] = $databaseInfo['dbname'];

        // SQLite: the file is checked before the driver opens it, which would
        // otherwise create it wherever the name points, and fail on a directory
        // it cannot write with no more than "unable to open database file"
        if ( $this->PersistenceList['database_info']['type'] == 'sqlite3' )
        {
            $fileCheck = $this->checkSQLiteDatabaseFile( $dbParameters['database'] );
            $this->PersistenceList['database_info']['sqlite_file'] = $fileCheck['path'];
            if ( $fileCheck['error_code'] )
            {
                $result['error_code'] = $fileCheck['error_code'];
                $result['connected'] = false;
                eZLog::write( "eZStepInstaller: SQLite database file '{$fileCheck['path']}' refused (error code {$fileCheck['error_code']})", 'setup.log' );
                return $result;
            }
        }

        // PostgreSQL requires us to specify database name.
        // We use template1 here since it exists on all PostgreSQL installations.
        if( $dbParameters['database'] == '' and $this->PersistenceList['database_info']['type'] == 'pgsql' )
            $dbParameters['database'] = 'template1';

        // MySQL: if the user provided a database name, connect directly to it.
        // This avoids any attempt to select the system 'mysql' database and
        // means SHOW DATABASES / root access is not required.
        if ( in_array( $this->PersistenceList['database_info']['type'], array( 'mysql', 'mysqli' ) ) and
             ( $dbParameters['database'] == '' or $dbParameters['database'] === false ) and
             !empty( $databaseInfo['dbname'] ) )
        {
            $dbParameters['database'] = $databaseInfo['dbname'];
        }

        // MongoDB: the setup form posts 'dbname' not 'database', so database stays empty.
        // Read the DB name from the existing siteaccess configuration.
        if ( $this->PersistenceList['database_info']['type'] == 'mongodb' and
             ( $dbParameters['database'] == '' or $dbParameters['database'] === false ) )
        {
            if ( !empty( $databaseInfo['dbname'] ) )
            {
                $dbParameters['database'] = $databaseInfo['dbname'];
            }
            else
            {
                $databaseNameFromSiteAccess = '';
                $siteRootPath = dirname( __FILE__, 4 );
                $overrideIniFileContent = @file_get_contents( $siteRootPath . '/settings/override/site.ini.append.php' );
                $defaultSiteAccessName = '';
                if ( $overrideIniFileContent && preg_match( '/DefaultAccess\s*=\s*(\S+)/', $overrideIniFileContent, $regexMatches ) )
                    $defaultSiteAccessName = trim( $regexMatches[1] );
                if ( $defaultSiteAccessName )
                {
                    $siteAccessIniFileContent = @file_get_contents( $siteRootPath . '/settings/siteaccess/' . $defaultSiteAccessName . '/site.ini.append.php' );
                    if ( $siteAccessIniFileContent && preg_match( '/\bDatabase\s*=\s*([^\s\r\n*]+)/', $siteAccessIniFileContent, $regexMatches ) )
                        $databaseNameFromSiteAccess = trim( $regexMatches[1] );
                }
                // Fallback: scan all siteaccess dirs for a mongodb one
                if ( empty( $databaseNameFromSiteAccess ) )
                {
                    foreach ( glob( $siteRootPath . '/settings/siteaccess/*/site.ini.append.php' ) ?: [] as $siteAccessIniFilePath )
                    {
                        $siteAccessFileContent = file_get_contents( $siteAccessIniFilePath );
                        if ( preg_match( '/DatabaseImplementation\s*=\s*mongodb/i', $siteAccessFileContent ) &&
                             preg_match( '/\bDatabase\s*=\s*([^\s\r\n*]+)/', $siteAccessFileContent, $regexMatches ) )
                        {
                            $databaseNameFromSiteAccess = trim( $regexMatches[1] );
                            break;
                        }
                    }
                }
                if ( !empty( $databaseNameFromSiteAccess ) )
                    $dbParameters['database'] = $databaseNameFromSiteAccess;
            }
        }

        try
        {
            $db = eZDB::instance( $dbDriver, $dbParameters, true );
            $result['db_instance'] = $db;
            $result['connected'] = $db->isConnected();
        }

        catch( eZDBNoConnectionException $e )
        {
            $result['error_code'] = self::DB_ERROR_CONNECTION_FAILED;
            return $result;
        }
        catch ( Exception $e )
        {
            // A driver that fails in its own way (SQLite3's "unable to open
            // database file") is a failed connection too, not a fatal error
            eZLog::write( "eZStepInstaller: connecting with driver '$dbDriver' failed: " . get_class( $e ) . ' - ' . $e->getMessage(), 'setup.log' );
            $result['error_code'] = self::DB_ERROR_CONNECTION_FAILED;
            return $result;
        }

        // Check if the version of the database fits the minimum required
        $dbVersion = $db->databaseServerVersion();
        $result['db_version'] = $dbVersion['string'];
        $result['db_required_version'] = $databaseInfo['info']['required_version'];
        if ( $dbVersion != null )
        {
            if ( version_compare( $result['db_version'], $databaseInfo['info']['required_version'] ) == -1 )
            {
                $result['connected'] = false;
                $result['error_code'] = self::DB_ERROR_VERSION_INVALID;
                return $result;
            }
        }

        // If we have PostgreSQL we need to make sure we have the 'digest' procedure available.
        if ( $db->databaseName() == 'postgresql' and $dbParameters['database'] != 'template1' )
        {
            $sql = "SELECT count(*) AS count FROM pg_proc WHERE proname='digest'";
            $rows = $db->arrayQuery( $sql );
            $count = $rows[0]['count'];
            // If it is 0 we don't have it
            if ( $count == 0 )
            {
                $result['error_code'] = self::DB_ERROR_NO_DIGEST_PROC;
                return $result;
            }
        }

        // If the connection is not valid, there is no point in checking charset;
        // report the connection failure immediately. This can happen when the user
        // provides a database name that does not exist or that they have no access to.
        if ( $result['connected'] === false )
        {
            $result['error_code'] = self::DB_ERROR_CONNECTION_FAILED;
            return $result;
        }

        $result['use_unicode'] = false;
        if ( $db->isCharsetSupported( 'utf-8' ) )
        {
            $result['use_unicode'] = true;
        }

        // If we regional info we can start checking the charset
        if ( isset( $this->PersistenceList['regional_info'] ) )
        {
            if ( isset( $this->PersistenceList['regional_info']['site_charset'] ) and
                 strlen( $this->PersistenceList['regional_info']['site_charset'] ) > 0 )
            {
                $charsetsList = array( $this->PersistenceList['regional_info']['site_charset'] );
            }
            else
            {
                // Figure out charset automatically if it is not set yet
                $primaryLanguage     = null;
                $allLanguages        = array();
                $allLanguageCodes    = array();
                $variationsLanguages = array();
                $primaryLanguageCode = $this->PersistenceList['regional_info']['primary_language'];
                $extraLanguageCodes  = isset( $this->PersistenceList['regional_info']['languages'] ) ? $this->PersistenceList['regional_info']['languages'] : array();
                $extraLanguageCodes  = array_diff( $extraLanguageCodes, array( $primaryLanguageCode ) );

                /*
                if ( isset( $this->PersistenceList['regional_info']['variations'] ) )
                {
                    $variations = $this->PersistenceList['regional_info']['variations'];
                    foreach ( $variations as $variation )
                    {
                        $locale = eZLocale::create( $variation );
                        if ( $locale->localeCode() == $primaryLanguageCode )
                        {
                            $primaryLanguage = $locale;
                        }
                        else
                        {
                            $variationsLanguages[] = $locale;
                        }
                    }
                }
                */

                if ( $primaryLanguage === null )
                    $primaryLanguage = eZLocale::create( $primaryLanguageCode );

                $allLanguages[] = $primaryLanguage;

                foreach ( $extraLanguageCodes as $extraLanguageCode )
                {
                    $allLanguages[] = eZLocale::create( $extraLanguageCode );
                    $allLanguageCodes[] = $extraLanguageCode;
                }

                $charsetsList = $this->findAppropriateCharsetsList( $primaryLanguage, $allLanguages, $result['use_unicode'] );
            }

            $checkedCharset = $db->checkCharset( $charsetsList, $currentCharset );
            if ( $checkedCharset === false )
            {
                // If the current charset is utf-8 or utf8mb4 we use that instead
                // since they can represent any character possible in the chosen languages
                if ( in_array( $currentCharset, array( 'utf-8', 'utf8mb4' ) ) )
                {
                    $result['site_charset'] = 'utf-8';
                }
                else
                {
                    $result['connected'] = false;
                    $this->PersistenceList['database_info']['requested_charset'] = implode( ", ", $charsetsList );
                    $this->PersistenceList['database_info']['current_charset'] = $currentCharset;
                    $result['error_code'] = self::DB_ERROR_CHARSET_DIFFERS;
                    return $result;
                }
            }
            else if ( $checkedCharset === true )
            {
                $result['site_charset'] = $charsetsList[ 0 ];
            }
            else
            {
                $result['site_charset'] = $checkedCharset;
            }
        }

        $result['status'] = true;
        return $result;
    }

    /**
     * Whether a SQLite database file can be used, before anything opens it.
     *
     * The wizard accepts a plain file name in the driver's storage directory
     * (var/storage/sqlite3); an absolute path only comes from kickstart.ini,
     * as the command-line kickstarter allows. The directory has to be
     * writable, not only the file: WAL mode keeps <file>-wal and <file>-shm
     * next to it. An existing file has to be writable and a SQLite database
     * (an empty file is one); whether it already holds tables is the Site
     * details step's question, as for every other database.
     *
     * @param string $fileName
     * @return array 'error_code' (false or a DB_ERROR_SQLITE_* code), 'path'
     *               (the file, relative to the installation), 'tables' (count)
     */
    function checkSQLiteDatabaseFile( $fileName )
    {
        $fileName = trim( (string)$fileName );
        $isAbsolute = strlen( $fileName ) > 0 && $fileName[0] === '/';
        $check = array( 'error_code' => false,
                        'path' => eZSQLite3DB::filePath( $fileName ),
                        'tables' => 0 );

        if ( $isAbsolute ? !$this->hasKickstartData() || strpos( $fileName, '/../' ) !== false
                         : !preg_match( self::SQLITE_FILE_NAME_REGEXP, $fileName ) )
        {
            $check['error_code'] = self::DB_ERROR_SQLITE_FILE_NAME;
            return $check;
        }

        $path = $check['path'];
        $directory = dirname( $path );
        if ( is_dir( $directory ) )
        {
            if ( !is_writable( $directory ) )
            {
                $check['error_code'] = self::DB_ERROR_SQLITE_DIRECTORY_NOT_WRITABLE;
                return $check;
            }
        }
        else
        {
            // Created by the driver: the nearest directory that exists has to let it
            $parent = dirname( $directory );
            while ( $parent !== '.' && $parent !== '/' && !is_dir( $parent ) )
                $parent = dirname( $parent );
            if ( file_exists( $directory ) || !is_writable( $parent ) )
            {
                $check['error_code'] = self::DB_ERROR_SQLITE_DIRECTORY_NOT_WRITABLE;
                return $check;
            }
        }

        if ( file_exists( $path ) )
        {
            if ( !is_file( $path ) )
            {
                $check['error_code'] = self::DB_ERROR_SQLITE_NOT_A_DATABASE;
                return $check;
            }
            if ( !is_writable( $path ) )
            {
                $check['error_code'] = self::DB_ERROR_SQLITE_FILE_NOT_WRITABLE;
                return $check;
            }
            if ( filesize( $path ) > 0 )
            {
                $header = (string)@file_get_contents( $path, false, null, 0, 16 );
                if ( $header !== "SQLite format 3\0" )
                {
                    $check['error_code'] = self::DB_ERROR_SQLITE_NOT_A_DATABASE;
                    return $check;
                }
            }
        }

        return $check;
    }

    /**
     * Empties what a SQLite database has no use for: the server, port, user,
     * password and socket are neither asked for nor written to site.ini
     * (Server=, Port=, User=, Password=, Socket=disabled, as the kickstarter
     * writes them for SQLite).
     */
    function resetSQLiteServerFields()
    {
        foreach ( array( 'server', 'port', 'user', 'password', 'socket' ) as $key )
            $this->PersistenceList['database_info'][$key] = '';
    }

    /**
     * The user PHP runs as, the one that has to be able to write a directory.
     *
     * @return string
     */
    protected static function processUserName()
    {
        if ( function_exists( 'posix_geteuid' ) && function_exists( 'posix_getpwuid' ) )
        {
            $user = posix_getpwuid( posix_geteuid() );
            if ( is_array( $user ) && isset( $user['name'] ) )
                return $user['name'];
        }
        return get_current_user();
    }

    /**
     * @param array $errorInfo
     * @return array|bool
     */
    function databaseErrorInfo( $errorInfo )
    {
        $code = $errorInfo['error_code'];
        $dbError = false;

        $sqliteFile = isset( $errorInfo['database_info']['sqlite_file'] ) ? (string)$errorInfo['database_info']['sqlite_file'] : '';
        $sqliteArguments = array( '%file' => htmlspecialchars( $sqliteFile ),
                                  '%directory' => htmlspecialchars( $sqliteFile !== '' ? dirname( $sqliteFile ) : eZSQLite3DB::STORAGE_DIRECTORY ),
                                  '%user' => htmlspecialchars( self::processUserName() ) );
        switch ( $code )
        {
            case self::DB_ERROR_SQLITE_FILE_NAME:
            {
                $dbError = array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                    'The database file name is not valid. Give a plain file name ending in .db, .db3, .sqlite or .sqlite3, made of letters, digits, dots, dashes and underscores, such as sqlite.db. The file is kept in %directory.',
                                                    null, array( '%directory' => eZSQLite3DB::STORAGE_DIRECTORY ) ),
                                  'url' => false,
                                  'number' => $code );
                break;
            }
            case self::DB_ERROR_SQLITE_DIRECTORY_NOT_WRITABLE:
            {
                $dbError = array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                    'The directory %directory cannot be written by the web server (user %user). SQLite needs to create the database file there, and the -wal and -shm files it keeps next to it. Give that user write access to the directory (create it first if it does not exist), then try again.',
                                                    null, $sqliteArguments ),
                                  'url' => false,
                                  'number' => $code );
                break;
            }
            case self::DB_ERROR_SQLITE_FILE_NOT_WRITABLE:
            {
                $dbError = array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                    'The database file %file exists but cannot be written by the web server (user %user). Give that user write access to it, or choose another file name.',
                                                    null, $sqliteArguments ),
                                  'url' => false,
                                  'number' => $code );
                break;
            }
            case self::DB_ERROR_SQLITE_NOT_A_DATABASE:
            {
                $dbError = array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                    'The file %file exists and is not a SQLite database. Choose another file name; the setup does not overwrite it.',
                                                    null, $sqliteArguments ),
                                  'url' => false,
                                  'number' => $code );
                break;
            }
            case self::DB_ERROR_CONNECTION_FAILED:
            {
                if ( $errorInfo['database_info']['type'] == 'pgsql' )
                {
                    $dbError = array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                        'Please make sure that the username and the password is correct. Verify that your PostgreSQL database is configured correctly.'
                                                        .'<br>See the PHP documentation for more information about this.'
                                                        .'<br>Remember to start postmaster with the -i option.'
                                                        .'<br>Note that PostgreSQL 7.2 is not supported.' ),
                                      'url' => array( 'href' => 'http://www.php.net/manual/en/ref.pgsql.php',
                                                      'text' => 'PHP documentation' ),
                                      'number' => self::DB_ERROR_CONNECTION_FAILED );
                }
                else if ( $errorInfo['database_info']['type'] == 'sqlite3' )
                {
                    $dbError = array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                        'The SQLite database file %file could not be opened. See var/log/setup.log and var/log/error.log for the reason.',
                                                        null, $sqliteArguments ),
                                      'url' => false,
                                      'number' => self::DB_ERROR_CONNECTION_FAILED );
                }
                else
                {
                    $dbError = array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                        'The database would not accept the connection, please review your settings and try again.' ),
                                  'url' => false,
                                      'number' => self::DB_ERROR_CONNECTION_FAILED );
                }

                break;
            }
            case self::DB_ERROR_NONMATCH_PASSWORD:
            {
                $dbError = array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                    'Password entries did not match.' ),
                                  'url' => false,
                                  'number' => self::DB_ERROR_NONMATCH_PASSWORD );
                break;
            }
            case self::DB_ERROR_NOT_EMPTY:
            {
                $dbError = array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                    'The selected database was not empty, please choose from the alternatives below.' ),
                                  'url' => false,
                                  'number' => self::DB_ERROR_NOT_EMPTY );
                $dbNotEmpty = 1;
                break;
            }
            case self::DB_ERROR_NO_DATABASES:
            {
                $dbError = array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                    'The selected user has not got access to any databases. Change user or create a database for the user.' ),
                                  'url' => false,
                                  'number' => self::DB_ERROR_NO_DATABASES );
                break;
            }

            case self::DB_ERROR_NO_DIGEST_PROC:
            {
                $dbError = array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                    "The 'digest' function is not available in your database, you cannot run Exponential without this. See the documentation for more information." ),
                                  'url' => array( 'href' => 'http://ez.no/doc/ez_publish/technical_manual/current/installation/normal_installation/requirements_for_doing_a_normal_installation#digest_function',
                                                  'text' => 'PostgreSQL digest FAQ' ),
                                  'number' => self::DB_ERROR_NO_DATABASES );
                break;
            }

            case self::DB_ERROR_VERSION_INVALID:
            {
                $dbError = array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                    "Your database version %version does not fit the minimum requirement which is %req_version.
See the requirements page for more information.",
                                                    null,
                                                    array( '%version' => $errorInfo['database_info']['version'],
                                                           '%req_version' => $errorInfo['database_info']['required_version'] ) ),
                                  'url' => array( 'href' => 'http://ez.no/ez_publish/documentation/general_information/what_is_ez_publish/ez_publish_requirements',
                                                  'text' => 'eZ Publish requirements' ),
                                  'number' => self::DB_ERROR_NO_DATABASES );
                break;
            }

            case self::DB_ERROR_CHARSET_DIFFERS:
            {
                $dbError = array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                    "The database [%database_name] cannot be used, the setup wizard wants to create the site in [%req_charset] but the database has been created using character set [%charset]. You will have to choose a database having support for [%req_charset] or modify [%database_name] .",
                                                    null,
                                                    array( '%database_name' => $errorInfo['site_type']['database'],
                                                           '%charset' => $errorInfo['database_info']['current_charset'],
                                                           '%req_charset' => $errorInfo['database_info']['requested_charset'] ) ),
                                  'url' => false,
                                  'number' => self::DB_ERROR_CHARSET_DIFFERS );
                break;
            }
        }

        return $dbError;
    }

    /**
     * @return bool True if the step has kickstart data available.
     */
    function hasKickstartData()
    {
        if ( !$this->isKickstartAllowed() )
            return false;
        return $this->KickstartData !== false;
    }

    /**
     * @return array|bool All kickstart data as an associative array or false if no data available
     */
    function kickstartData()
    {
        return $this->KickstartData;
    }

    /**
     * @return bool True if kickstart functionality can be used.
     */
    function isKickstartAllowed()
    {
        $identifier = $this->Identifier;
        if ( isset( $this->PersistenceList['use_kickstart'][$identifier] ) and
             !$this->PersistenceList['use_kickstart'][$identifier] )
            return false;

        if ( isset( $GLOBALS['eZStepAllowKickstart'] ) )
            return $GLOBALS['eZStepAllowKickstart'];

        return true;
    }

    /**
     * @return bool True if the kickstart functionality should continue to the next step.
     */
    function kickstartContinueNextStep()
    {
        if ( isset( $this->KickstartData['Continue'] ) and
             $this->KickstartData['Continue'] == 'true' )
            return true;
        return false;
    }

    /*!
     Sets whether kickstart data can be checked or not.
    */
    function setAllowKickstart( $allow )
    {
        $GLOBALS['eZStepAllowKickstart'] = $allow;
    }

    /**
     * @return array Urls to access user and admin siteaccesses
     */
    function siteaccessURLs()
    {
        $siteType = $this->chosenSiteType();

        $url = $siteType['url'];
        if ( !preg_match( "#^[a-zA-Z0-9]+://(.*)$#", $url ) )
        {
            $url = 'http://' . $url;
        }
        $currentURL = $url;
        $adminURL = $url;

        if ( $siteType['access_type'] == 'url' )
        {
            $ini = eZINI::instance();
            if ( $ini->hasVariable( 'SiteSettings', 'DefaultAccess' ) )
            {
                $siteType['access_type_value'] = $ini->variable( 'SiteSettings', 'DefaultAccess' );
            }

            $url .= '/' . $siteType['access_type_value'];
            $adminURL .= '/' . $siteType['admin_access_type_value'];
        }
        else if ( $siteType['access_type'] == 'hostname' )
        {
            $url = $siteType['access_type_value'];
            $adminURL = $siteType['admin_access_type_value'];
            if ( !preg_match( "#^[a-zA-Z0-9]+://(.*)$#", $url ) )
            {
                $url = 'http://' . $url;
            }
            if ( !preg_match( "#^[a-zA-Z0-9]+://(.*)$#", $adminURL ) )
            {
                $adminURL = 'http://' . $adminURL;
            }
            $url .= eZSys::indexDir( false );
            $adminURL .= eZSys::indexDir( false );
        }
        else if ( $siteType['access_type'] == 'port' )
        {
            $url = eZHTTPTool::createRedirectURL( $currentURL, array( 'override_port' => $siteType['access_type_value'] ) );
            $adminURL = eZHTTPTool::createRedirectURL( $currentURL, array( 'override_port' => $siteType['admin_access_type_value'] ) );
        }

        $siteaccessURL = array( 'url' => $url,
                                'admin_url' => $adminURL );

        return $siteaccessURL;
    }

    public $Tpl;
    public $Http;
    public $Ini;
    public $PersistenceList;
    // The identifier of the current step
    public $Identifier;
    // The name of the current step
    public $Name;
    /// Kickstart INI file, if one is found
    public $INI;
    /// The kickstart data as an associative array or \c false if no data available
    public $KickstartData;
}

?>
