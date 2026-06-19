#!/usr/bin/env php
<?php
/**
 * File containing the updateniceurls.php bin script
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

set_time_limit ( 0 );

require_once 'autoload.php';
$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => ( "eZ Publish url-alias imported and updater.\n\n" .
                                                         "Will import urls from the older (3.9) system into the new, controlled by the --import* options.\n" .
                                                         "Will also update the url-alias entries from the content object nodes in the system, controlled by the --update-nodes option.\n" .
                                                         "The default behaviour is to update urls for content object nodes only\n" .
                                                         "\n" .
                                                         "updateniceurls.php" ),
                                      'use-session' => true,
                                      'use-modules' => true,
                                      'use-extensions' => true ) );

$script->startup();

$helpRequested = in_array( '-h', $_SERVER['argv'] ) || in_array( '--help', $_SERVER['argv'] );

$options = $script->getOptions( "[db-host:][db-user:][db-password:][db-database:][db-type:|db-driver:][sql]" .
                                "[no-import]" .
                                "[import][import-nodes][import-aliases][import-redirections][import-wildcards]" .
                                "[no-update-nodes][update-nodes]" .
                                "[verify-data][interactive]" .
                                "[backup-tables:]" .
                                "[column-width:][fetch-limit:]",
                                "",
                                array( 'db-host' => "Database host",
                                       'db-user' => "Database user",
                                       'db-password' => "Database password",
                                       'db-database' => "Database name",
                                       'db-driver' => "Database driver",
                                       'db-type' => "Database driver, alias for --db-driver",
                                       'sql' => "Display sql queries",

                                       'no-import' => "Disables all import routines. To enable specific ones use the --import-* options.",
                                       'import' => "Enables all import routines.",
                                       'import-nodes' => "Enables importing of urls from the old node data.",
                                       'import-aliases' => "Enables importing of old urls (system and customized).",
                                       'import-redirections' => "Enables importing of urls which redirects to the correct url (ie. history).",
                                       'import-wildcards' => "Enables importing of urls which redirects to the correct url using wildcards (ie. history).",

                                       'no-update-nodes' => "Disable updating of the urls of content object nodes.",
                                       'update-nodes' => "Enable updating of the urls of content object nodes.",

                                       'verify-data' => "Verify the database after new data has been inserted, this should only be used for debugging.",
                                       'interactive' => "Enables interactive mode for --verify-data,\nthis will halt execution when database errors occurs and allow for manual inspection.",
                                       'backup-tables' => "Performs a backup of the ezurlalias and ezurlalias_ml tables after each stage is done (import or update),\nthe backup tables will use the original name but with the suffix supplied to this option.\nNote: Use only for debugging and only on MySQL.",

                                       'column-width' => "The approximate width of the output block, defaults to 72.",
                                       'fetch-limit' => "The number of items to fetch in one go, increasing it may reduce\ntotal time but will also increase memory usage, defaults to 200.",
                                       ) );

if ( $helpRequested || ( isset( $options['help'] ) && $options['help'] ) )
{
    $script->showHelp();
    $script->shutdown( 0 );
}

$script->initialize();

$dbUser = $options['db-user'] ? $options['db-user'] : false;
$dbPassword = $options['db-password'] ? $options['db-password'] : false;
$dbHost = isset( $options['db-host'] ) && $options['db-host'] ? $options['db-host'] : false;
$dbName = $options['db-database'] ? $options['db-database'] : false;
$dbImpl = $options['db-driver'] ? $options['db-driver'] : false;
$showSQL = $options['sql'] ? true : false;
$siteAccess = $options['siteaccess'] ? $options['siteaccess'] : false;
if ( $siteAccess )
{
    changeSiteAccessSetting( $siteAccess );
}

function changeSiteAccessSetting( $siteAccess )
{
    $cli = eZCLI::instance();
    if ( in_array( $siteAccess, eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) ) )
    {
        $cli->output( "Using siteaccess $siteAccess for nice url update" );
    }
    else
    {
        $cli->notice( "Siteaccess $siteAccess does not exist, using default siteaccess" );
    }
}

function updaterMaxExecutionTime()
{
    $cronjobIni = eZINI::instance( 'cronjob.ini' );
    if ( $cronjobIni->hasVariable( 'CronjobSettings', 'MaxScriptExecutionTime' ) )
        return (int)$cronjobIni->variable( 'CronjobSettings', 'MaxScriptExecutionTime' );
    return 3600;
}

function updaterStealMutex( $cli, $scriptMutex, $force = false )
{
    $cli->output( 'Stealing updater mutex. Old process has run too long.' );
    $oldPid = $scriptMutex->meta( 'pid' );

    if ( $force )
    {
        if ( is_numeric( $oldPid ) &&
             $oldPid != 0 &&
             function_exists( 'posix_kill' ) )
        {
            $cli->output( 'Killing process: ' . $oldPid );
            posix_kill( $oldPid, 9 );
        }
    }

    if ( $scriptMutex->steal( $force ) )
    {
        $scriptMutex->setMeta( 'pid', getmypid() );
        return true;
    }

    $cli->error( 'Failed to steal updater lock.' );
    return false;
}

function acquireUpdaterMutex( $cli, $siteAccess )
{
    $mutexName = 'bin/php/updateniceurls.php';
    if ( $siteAccess )
        $mutexName .= '::' . $siteAccess;

    $scriptMutex = new eZMutex( $mutexName );
    $lockTS = $scriptMutex->lockTS();
    $maxTime = updaterMaxExecutionTime();

    if ( $lockTS === false )
    {
        if ( $scriptMutex->lock() )
        {
            $scriptMutex->setMeta( 'pid', getmypid() );
            return $scriptMutex;
        }

        $cli->error( 'Failed to acquire updater lock: ' . $mutexName );
        return false;
    }

    if ( $lockTS < time() - 2 * $maxTime )
    {
        $cli->output( 'Forcing mutex lock steal: ' . $mutexName );
        if ( updaterStealMutex( $cli, $scriptMutex, true ) )
            return $scriptMutex;
        return false;
    }

    if ( $lockTS < time() - $maxTime )
    {
        $cli->output( 'Trying mutex lock steal: ' . $mutexName );
        if ( updaterStealMutex( $cli, $scriptMutex ) )
            return $scriptMutex;
        return false;
    }

    $cli->error( 'updateniceurls.php is already running (PID: ' . $scriptMutex->meta( 'pid' ) . ')' );
    return false;
}

function releaseUpdaterMutex( $updaterMutex )
{
    if ( $updaterMutex )
        $updaterMutex->unlock();
}

$updaterMutex = acquireUpdaterMutex( $cli, $siteAccess );
if ( !$updaterMutex )
{
    $script->shutdown( 1 );
}
register_shutdown_function( 'releaseUpdaterMutex', $updaterMutex );

$db = eZDB::instance();

if ( $dbHost or $dbName or $dbUser or $dbImpl )
{
    $params = array();
    if ( $dbHost !== false )
        $params['server'] = $dbHost;
    if ( $dbUser !== false )
    {
        $params['user'] = $dbUser;
        $params['password'] = '';
    }
    if ( $dbPassword !== false )
        $params['password'] = $dbPassword;
    if ( $dbName !== false )
        $params['database'] = $dbName;
    $db = eZDB::instance( $dbImpl, $params, true );
    eZDB::setInstance( $db );
}

$db->setIsSQLOutputEnabled( $showSQL );

eZContentLanguage::setCronjobMode( true );

$fetchLimit = 200;
if ( $options['fetch-limit'] !== null )
{
    $fetchLimit = $options['fetch-limit'];
    if ( $fetchLimit <= 0 )
    {
        $script->shutdown( 1, "The --fetch-limit must be 1 or higher, tried with $fetchLimit" );
    }
}

$percentLength = 6;
$timeLength = 12;
$columnWidth = 72;
if ( $options['column-width'] !== null )
{
    $columnWidth = $options['column-width'];
    if ( $columnWidth <= 0 )
    {
        $script->shutdown( 1, "The --column-width must be 1 or higher, tried with $columnWidth" );
    }
}
$maxColumn = max( $columnWidth - $percentLength - $timeLength, $percentLength + $timeLength + 1 );
$totalChangedNodes = 0;
$totalNodeCount = 0;

$interactive = false;
$performVerification = false;

if ( $options['verify-data'] )
{
    $performVerification = true;
}
if ( $options['interactive'] )
{
    $interactive = true;
}

$backupTables = false;
$backupTableSuffix = false;
if ( $options['backup-tables'] !== null )
{
    $backupTables = $options['backup-tables'];
    $backupTableSuffix = $backupTables;
}

$importNodes = false;
$importOldAlias = false;
$importOldAliasRedirections = false;
$importOldAliasWildcard = false;
$updateNodeAlias = true;

if ( $options['no-import'] )
{
    $importNodes = false;
    $importOldAlias = false;
    $importOldAliasRedirections = false;
    $importOldAliasWildcard = false;
}

if ( $options['import'] )
{
    $importNodes = true;
    $importOldAlias = true;
    $importOldAliasRedirections = true;
    $importOldAliasWildcard = true;
}

if ( $options['import-nodes'] )
{
    $importNodes = true;
}

if ( $options['import-aliases'] )
{
    $importOldAlias = true;
}

if ( $options['import-redirections'] )
{
    $importOldAliasRedirections = true;
}

if ( $options['import-wildcards'] )
{
    $importOldAliasWildcard = true;
}

if ( $options['no-update-nodes'] )
{
    $updateNodeAlias = false;
}

if ( $options['update-nodes'] )
{
    $updateNodeAlias = true;
}

$displayProgressClosure = function ( $statusCharacter, $startTime, $currentCount, $totalCount, $currentColumn ) use ( $maxColumn, $cli )
{
    if ( $statusCharacter !== false )
        $cli->output( $statusCharacter, false );

    if ( $currentColumn > $maxColumn )
    {
        $endTime = microtime( true );
        $relTime = ( $endTime - $startTime ) / $currentCount;
        $totalTime = ( $relTime * (float)($totalCount - $currentCount) );
        $percent = number_format( ( $currentCount * 100.0 ) / ( $totalCount ), 2 );

        $timeLeft = formatTime( $totalTime );

        $items = $currentCount . '/' . $totalCount;

        $cli->output( " " . $percent . "% " . $timeLeft . ' ' . $items );

        $currentColumn = 0;
    }
    else
    {
        ++$currentColumn;
    }
    ++$currentCount;
    flush();
    return array( $currentColumn, $currentCount );
};

function formatTime( $totalTime )
{
    $timeSeconds = (int)( $totalTime % 60 );
    $timeMinutes = (int)( ( $totalTime / 60.0 ) % 60 );
    $timeHours = (int)( $totalTime / ( 60.0 * 60.0 ) );
    $timeLeftArray = array();
    if ( $timeHours > 0 )
        $timeLeftArray[] = $timeHours . "h";
    if ( $timeMinutes > 0 )
        $timeLeftArray[] = $timeMinutes . "m";
    $timeLeftArray[] = $timeSeconds . "s";
    return implode( " ", $timeLeftArray );
}

function fetchMaskByNodeID( $nodeID )
{
    $db = eZDB::instance();
    $sql = "SELECT language_mask FROM ezcontentobject, ezcontentobject_tree
            WHERE ezcontentobject.id = ezcontentobject_tree.contentobject_id
            AND   ezcontentobject_tree.node_id = " . (int)$nodeID;
    $rows = $db->arrayQuery( $sql );
    if ( count( $rows ) > 0 )
    {
        return $rows[0]['language_mask'];
    }
    return false;
}

function isAlwaysAvailable( $nodeID )
{
    $mask = fetchMaskByNodeID( $nodeID );
    if ( ($mask & 1) > 0 )
        return true;
    return false;
}

function decodeAction( $destination )
{
    $alwaysAvailable = false;
    if ( preg_match( "#^content/view/full/([0-9]+)$#", $destination, $matches ) )
    {
        $nodeID = $matches[1];
        $action = 'eznode:' . $nodeID;
        $alwaysAvailable = isAlwaysAvailable( $nodeID );
    }
    else
    {
        $action = 'module:' . $destination;
    }
    return array( $action, $alwaysAvailable );
}

function decodeNodeID( $destination )
{
    if ( preg_match( "#^content/view/full/([0-9]+)$#", $destination, $matches ) )
    {
        return (int)$matches[1];
    }
    return null;
}

function logError( $msg )
{
    $logFile = fopen( 'urlalias_error.log', "a" );
    if ( $logFile )
    {
        $time = date( "M d Y H:i:s", strtotime( "now" ) );
        $logMessage = "[ " . $time . " ] $msg\n";
        fwrite( $logFile, $logMessage );
        fclose( $logFile );
    }
}

$logStoreClosure = function ( $res, $func, $args ) use ( $options )
{
    if ( !isset( $options['debug'] ) )
        return;

    $logFile = fopen( 'urlalias_store.log', "a" );
    if ( $logFile )
    {
        $time = date( "M d Y H:i:s", strtotime( "now" ) );
        $logMessage = "[ " . $time . " ] " . calltostring( $func, $args ) . "\n";
        fwrite( $logFile, $logMessage );
        fclose( $logFile );
    }
};

function resetLogFile( $file )
{
    $cli = eZCLI::instance();
    if ( file_exists( $file ) )
    {
        $s = stat( $file );
        if ( $s['size'] > 0 )
        {
            $archive = $file . "." . date( "YmdHis", $s['mtime'] );
            copy( $file, $archive );
            $cli->output( "Archived log file $file to $archive" );
        }
        fopen( $file, "w" );
    }
}

function resetErrorLog()
{
    resetLogFile( "urlalias_error.log" );
}

function resetStorageLog()
{
    resetLogFile( "urlalias_store.log" );
}

function fetchHistoricURLCount()
{
    $db = eZDB::instance();
    $sql = 'SELECT count(*) AS count FROM ezurlalias
            WHERE is_imported = 0 AND is_wildcard = 0 AND forward_to_id = 0';
    $rows = $db->arrayQuery( $sql );
    return $rows[0]['count'];
}

function fetchHistoricRedirectionCount()
{
    $db = eZDB::instance();
    $sql = 'SELECT count(*) AS count FROM ezurlalias
            WHERE is_imported = 0 AND is_wildcard = 0 AND forward_to_id != 0';
    $rows = $db->arrayQuery( $sql );
    return $rows[0]['count'];
}

function fetchHistoricWildcardCount()
{
    $db = eZDB::instance();
    $sql = 'SELECT count(*) AS count FROM ezurlalias
            WHERE is_imported = 0 AND is_wildcard != 0';
    $rows = $db->arrayQuery( $sql );
    return $rows[0]['count'];
}

function fetchHistoricURLChunk( $offset, $fetchLimit )
{
    $db = eZDB::instance();
    $sql = 'SELECT id, source_url, destination_url, is_internal FROM ezurlalias
            WHERE is_imported = 0 AND is_wildcard = 0 AND forward_to_id = 0';
    $rows = $db->arrayQuery( $sql,
                             array( 'offset' => $offset,
                                    'limit' => $fetchLimit ) );
    return array( $rows, $offset + count( $rows ) );
}

function fetchHistoricRedirectionChunk( $offset, $fetchLimit )
{
    $db = eZDB::instance();
    $sql = 'SELECT id, forward_to_id, source_url, destination_url FROM ezurlalias
            WHERE is_imported = 0 AND is_wildcard = 0 AND forward_to_id != 0';
    $rows = $db->arrayQuery( $sql,
                             array( 'offset' => $offset,
                                    'limit' => $fetchLimit ) );
    return array( $rows, $offset + count( $rows ) );
}

function fetchHistoricWildcardChunk( $offset, $fetchLimit )
{
    $db = eZDB::instance();
    $sql = 'SELECT id, is_wildcard, is_internal, source_url, destination_url
            FROM ezurlalias WHERE is_imported = 0 AND is_wildcard != 0';
    $rows = $db->arrayQuery( $sql,
                             array( 'offset' => $offset,
                                    'limit' => $fetchLimit ) );
    return array( $rows, $offset + count( $rows ) );
}

function fetchPathIdentificationString( $nodeID )
{
    $db = eZDB::instance();
    $sql = 'SELECT path_identification_string
            FROM ezcontentobject_tree WHERE node_id = ' . $nodeID;
    $rows = $db->arrayQuery( $sql );
    if ( count( $rows ) > 0 )
        return $rows[0]['path_identification_string'];
    return null;
}

function fetchPathIdentificationStringCount()
{
    $db = eZDB::instance();
    $sql = 'SELECT count(*) AS count
            FROM ezcontentobject WHERE ezcontentobject.status = ' . eZContentObject::STATUS_PUBLISHED;
    $rows = $db->arrayQuery( $sql );
    if ( count( $rows ) > 0 )
        return $rows[0]['count'];
    return 0;
}

function fetchPathIdentificationStringChunk( $offset, $fetchLimit )
{
    $db = eZDB::instance();
    $sql = 'SELECT id
            FROM ezcontentobject WHERE ezcontentobject.status = ' . eZContentObject::STATUS_PUBLISHED;
    $rows = $db->arrayQuery( $sql,
                             array( 'offset' => $offset,
                                    'limit' => $fetchLimit ) );
    if ( count( $rows ) == 0 )
        return false;
    $cond = createURLListCondition( $rows, 'contentobject_id', 'id' );
    $sql = 'SELECT path_identification_string, node_id, language_mask
            FROM ezcontentobject_tree, ezcontentobject WHERE contentobject_id = id AND (' . $cond . ')';
    $rows2 = $db->arrayQuery( $sql );
    return array( $rows2, $offset + count( $rows ) );
}

function createURLListCondition( $rows, $sqlField = 'id', $fieldKey = 'id' )
{
    if ( count( $rows ) == 0 )
        return false;
    $cond = "";
    $start = false;
    $last  = false;
    $ids  = array();
    foreach ( $rows as $row )
    {
        $ids[] = (int)$row[$fieldKey];
    }
    sort( $ids );
    $singleIDs = array();
    $betweens  = array();
    foreach ( $ids as $id )
    {
        if ( $last === false )
        {
            $start = $id;
            $last  = $id;
        }
        else if ( $last + 1 != $id )
        {
            if ( $start != $last )
            {
                $betweens[] = "({$sqlField} BETWEEN $start AND $last)";
            }
            else
            {
                $singleIDs[] = $last;
            }
            $start = $id;
            $last  = $id;
        }
        else
            $last = $id;
    }
    if ( $start != $last )
    {
        $betweens[] = "({$sqlField} BETWEEN $start AND $last)";
    }
    else
    {
        $singleIDs[] = $last;
    }
    $cond = join( " OR ", $betweens );
    if ( count( $singleIDs ) > 0 )
    {
        if ( $cond != "" )
             $cond .= " OR ";
        $cond .= eZDB::instance()->generateSQLINStatement( $singleIDs, $sqlField );
    }
    return $cond;
}

function removeURLList( $rows )
{
    if ( count( $rows ) == 0 )
        return;
    $db   = eZDB::instance();
    $cond =  createURLListCondition( $rows );
    $sql  =  "DELETE FROM ezurlalias WHERE $cond";
    $db->query( $sql );
}

function markAsImported( $rows )
{
    if ( count( $rows ) == 0 )
        return;
    $db   = eZDB::instance();
    $cond =  createURLListCondition( $rows );
    $sql  =  "UPDATE ezurlalias SET is_imported = 1 WHERE $cond";
    $db->query( $sql );
}

function calltostring( $func, $args )
{
    $msg = $func;
    if ( is_array( $args ) )
    {
        foreach ( $args as $key => $value )
        {
            $args[$key] = var_export( $value, true );
        }
        $msg .= "(" . join( ", ", $args ) . ")";
    }
    return $msg;
}

function logStoreError( $res, $func, $args )
{
    $errmsg = "Failed (status: {$res['status']}) to store the url-alias path when executing " . calltostring( $func, $args );
    if ( isset( $res['error_message'] ) )
        $errmsg .= ", error: " . $res['error_message'];
    logError( $errmsg );
}

function aliasTextMd5( $text )
{
    return md5( eZURLAliasML::strtolower( $text ) );
}

function checkUrlAliasIntegrity()
{
    $db = eZDB::instance();

    $orphanParentRows = $db->arrayQuery( "SELECT COUNT(*) AS count
        FROM ezurlalias_ml a
        WHERE a.parent != 0
          AND NOT EXISTS (SELECT 1 FROM ezurlalias_ml b WHERE b.id = a.parent)" );
    $orphanParentCount = (int)$orphanParentRows[0]['count'];
    if ( $orphanParentCount > 0 )
    {
        return array( false, "URL alias tree has {$orphanParentCount} orphaned parent references" );
    }

    $duplicateRows = $db->arrayQuery( "SELECT COUNT(*) AS count
        FROM (
            SELECT parent, text_md5
            FROM ezurlalias_ml
            GROUP BY parent, text_md5
            HAVING COUNT(*) > 1
        ) AS duplicates" );
    $duplicateCount = (int)$duplicateRows[0]['count'];
    if ( $duplicateCount > 0 )
    {
        return array( false, "URL alias tree has {$duplicateCount} duplicate parent/text_md5 combinations" );
    }

    return array( true, false );
}

$verifyDataInternalClosure = function ( &$result, $error ) use ( $interactive, $performVerification, $cli )
{
    if ( !$performVerification )
        return;

    $db = eZDB::instance();
    if ( $db->databaseName() != 'mysql' )
    {
        $cli->error( "Can only perform verification on a MySQL database." );
        $performVerification = false;
        return; // We only support MySQL for now
    }

    $tmprows = $db->arrayQuery( "SELECT a1.*, a2.link FROM ezurlalias_ml a1 LEFT JOIN ezurlalias_ml a2 ON a1.parent = a2.id WHERE a1.parent != 0 HAVING a2.link is null" );
    if ( count( $tmprows ) > 0 )
    {
        $tmpParentID = $tmprows[0]['parent'];
        $tmpText = $tmprows[0]['text'];
        $tmpID = $tmprows[0]['id'];
        $error .= " caused a URL alias element ({$tmpText} with ID {$tmpID}) to have an parent ID ({$tmpParentID}) to a non-existing element.";
        logError( $error );
        if ( $interactive )
            $cli->error( $error );
        $result = "X";
        if ( $interactive )
        {
            echo "Execution halted, press enter to continue: ";
            fgets(STDIN);
        }
    }
};

$verifyDataClosure = function ( &$result, $url, $id ) use ( $verifyDataInternalClosure )
{
    return $verifyDataInternalClosure( $result, "Importing the URL " . var_export( $url, true ) . " with ID $id");
};

$verifyNodeDataClosure = function ( &$result, $node ) use ( $verifyDataInternalClosure )
{
    return $verifyDataInternalClosure( $result, "Updating the node " . $node->attribute( 'node_id' ) );
};

$backupTablesClosure = function ( $stage ) use ( $backupTables, $backupTableSuffix, $cli )
{
    if ( !$backupTables )
        return;

    $db = eZDB::instance();
    if ( $db->databaseName() != 'mysql' )
        return; // We only support MySQL for now

    foreach ( array( 'ezurlalias', 'ezurlalias_ml' ) as $table )
    {
        $newTable = $table . $backupTableSuffix . '_' . $stage;
        $cli->output( "Backing up table $table to $newTable" );
        $db->query( "DROP TABLE IF EXISTS $newTable" );
        $db->query( "CREATE TABLE $newTable LIKE $table" );
        $db->query( "INSERT INTO $newTable SELECT * FROM $table" );
    }
};


$cli->output( "Note: any errors encountered will be logged to urlalias_error.log" );
$cli->output( "Using fetch limit: $fetchLimit" );

resetErrorLog();
resetStorageLog();

$globalStartTime = microtime( true );

// Move old historical elements to new table
$urlCount = 0;
if ( $importNodes || $importOldAlias || $importOldAliasRedirections || $importOldAliasWildcard )
{
    $rows = $db->arrayQuery( 'SELECT count(*) AS count FROM ezurlalias' );
    $urlCount = $rows[0]['count'];
}
if ( $urlCount > 0 )
{
    if ( $importNodes )
    {
        $cli->output( "Importing old node urls" );

        // First move standard urls
        $urlCount = fetchPathIdentificationStringCount();
        $cli->output( "Importing {$urlCount} " . $cli->stylize( 'emphasize', "node urls" ) );
        $column = $counter = $offset = 0;
        $urlImportStartTime = microtime( true );
        // First import from ezcontentobject_tree to get correct urls
        do
        {
            list( $rows, $offset ) = fetchPathIdentificationStringChunk( $offset, $fetchLimit );
            if ( !is_array( $rows ) )
            {
                break;
            }
            $count = count( $rows );
            foreach ( $rows as $row )
            {
                $nodeID = (int)$row['node_id'];
                if ( $nodeID == 1 )
                    continue; // Skip the root node
                $pathIdentificationString = $row['path_identification_string'];
                $pathIdentificationString = eZURLAliasML::sanitizeURL( $pathIdentificationString );
                $languageMask = $row['language_mask'];
                $alwaysAvailable = $languageMask & 1;
                $action = 'eznode:' . $nodeID;
                $aliases = eZURLAliasML::fetchByPath( $pathIdentificationString );
                if ( $aliases && $aliases[0]->attribute( 'action' ) != 'nop:' )
                {
                    // It is already present, skip it
                    list( $column, $counter ) = $displayProgressClosure( 's', $urlImportStartTime, $counter, $urlCount, $column );
                    continue;
                }
                $res = eZURLAliasML::storePath( $pathIdentificationString, $action,
                                                false, false, $alwaysAvailable, false,
                                                false );
                if ( !$res || $res['status'] !== true )
                {
                    logStoreError( $res, "eZURLAliasML::storePath", array( $pathIdentificationString, $action, false, false, $alwaysAvailable, false, false ) );
                    list( $column, $counter ) = $displayProgressClosure( 'E', $urlImportStartTime, $counter, $urlCount, $column );
                    continue;
                }
                $logStoreClosure( $res, "eZURLAliasML::storePath", array( $pathIdentificationString, $action, false, false, $alwaysAvailable, false, false ) );
                list( $column, $counter ) = $displayProgressClosure( '.', $urlImportStartTime, $counter, $urlCount, $column );
            }
        } while ( $count > 0 );
        flush();
        if ( $column > 0 )
            $cli->output();
        $backupTablesClosure( 'impnode' );
    }

    if ( $importOldAlias )
    {
        $cli->output( "Importing old url aliases" );

        // First move standard urls
        $urlCount = fetchHistoricURLCount();
        $cli->output( "Importing {$urlCount} " . $cli->stylize( 'emphasize', "standard urls" ) );
        $column = $counter = $offset = 0;
        $urlImportStartTime = microtime( true );
        // Then go over ezurlalias and make links to the real urls
        // Also import custom urls (non-node)
        do
        {
            list( $rows, $offset ) = fetchHistoricURLChunk( 0/*$offset*/, $fetchLimit );
            if ( !is_array( $rows ) )
            {
                break;
            }
            $count = count( $rows );
            foreach ( $rows as $row )
            {
                $source = $row['source_url'];
                $linkID = false;
                $source = eZURLAliasML::sanitizeURL( $source );
                $destination = $row['destination_url'];
                $aliasRedirects = true;

                list( $action, $alwaysAvailable ) = decodeAction( $destination );
                list( $actionType, $actionValue ) = explode( ":", $action, 2 );
                $aliases = eZURLAliasML::fetchByAction( $actionType, $actionValue );

                if ( $aliases && $actionType == 'eznode' )
                {
                    // This is a user-entered URL so lets make it an alias of the found dupe.
                    $linkID = (int)$aliases[0]->attribute( 'id' );
                }
                else if ( $actionType == 'eznode' )
                {
                    $query = "SELECT * FROM ezcontentobject_tree, ezcontentobject WHERE ezcontentobject_tree.contentobject_id = ezcontentobject.id AND ezcontentobject_tree.node_id = " . (int)$actionValue;
                    $tmprows = $db->arrayQuery( $query );
                    if ( count( $tmprows ) == 0 )
                    {
                        logError( "Found the alias " . var_export( $source, true ) . " with ID {$row['id']} which points to " . var_export( $action, true ) . " but that content-object/node does not exist in the database" );
                        list( $column, $counter ) = $displayProgressClosure( 's', $urlImportStartTime, $counter, $urlCount, $column );
                        continue;
                    }
                    if ( $tmprows[0]['status'] != eZContentObject::STATUS_PUBLISHED )
                    {
                        logError( "Found the alias " . var_export( $source, true ) . " with ID {$row['id']} which points to " . var_export( $action, true ) . " but that content-object/node is not currently published (status is {$tmprows[0]['status']})" );
                        list( $column, $counter ) = $displayProgressClosure( 's', $urlImportStartTime, $counter, $urlCount, $column );
                        continue;
                    }
                    $linkID = false;
                }
                else if ( $actionType == 'module' )
                {
                    $linkID = true;

                    // Links that pointed to modules in the old system does not
                    // redirect. Make sure they won't redirect in the new system either.
                    $aliasRedirects = false;
                }

                $aliases = eZURLAliasML::fetchByPath( $source );
                if ( $aliases )
                {
                    if ( $aliases[0]->attribute( 'action' ) != $action )
                    {
                        logError( "Found the alias " . var_export( $source, true ) . " with ID {$row['id']} which points to " . var_export( $action, true ) . " but that URL already exists, however the existing URL has the action " . var_export( $aliases[0]->attribute( 'action' ), true ) );
                        list( $column, $counter ) = $displayProgressClosure( 'E', $urlImportStartTime, $counter, $urlCount, $column );
                        continue;
                    }
                    // The path already exists, do not import
                    list( $column, $counter ) = $displayProgressClosure( 's', $urlImportStartTime, $counter, $urlCount, $column );
                    continue;
                }
                $res = eZURLAliasML::storePath( $source, $action,
                                                false, $linkID, $alwaysAvailable, false,
                                                false, false, true, $aliasRedirects );
                if ( !$res || $res['status'] !== true )
                {
                    logStoreError( $res, "eZURLAliasML::storePath", array( $source, $action, false, $linkID, $alwaysAvailable, false, false, false, true, $aliasRedirects ) );
                    list( $column, $counter ) = $displayProgressClosure( 'E', $urlImportStartTime, $counter, $urlCount, $column );
                    continue;
                }
                $logStoreClosure( $res, "eZURLAliasML::storePath", array( $source, $action, false, $linkID, $alwaysAvailable, false, false, false, true, $aliasRedirects ) );
                $result = '.';
                $verifyDataClosure( $result, $source, $row['id'] );
                list( $column, $counter ) = $displayProgressClosure( $result, $urlImportStartTime, $counter, $urlCount, $column );
            }
            markAsImported( $rows );
        } while ( $count > 0 );
        flush();
        if ( $column > 0 )
            $cli->output();
        $backupTablesClosure( 'impalias' );
    }

    if ( $importOldAliasRedirections )
    {
        // Then redirect urls
        $urlCount = fetchHistoricRedirectionCount();
        $cli->output( "Importing {$urlCount} " . $cli->stylize( 'emphasize', "redirections" ) );
        $column = $counter = $offset = 0;
        $urlImportStartTime = microtime( true );
        do
        {
            list( $rows, $offset ) = fetchHistoricRedirectionChunk( 0, $fetchLimit );
            if ( !is_array( $rows ) )
            {
                break;
            }
            $count = count( $rows );
            foreach ( $rows as $key => $row )
            {
                $forwardFromURL = $row['source_url'];
                $forwardFromURL = eZURLAliasML::sanitizeURL( $forwardFromURL );
                $forwardToID = (int)$row['forward_to_id'];
                $redirectedSource = false;
                $linkID = false;
                list( $action, $alwaysAvailable ) = decodeAction( $row['destination_url'] );
                list( $actionType, $actionValue ) = explode( ":", $action, 2 );

                $rows2 = $db->arrayQuery( "SELECT source_url FROM ezurlalias WHERE id = $forwardToID" );
                if ( count( $rows2 ) != 0 )
                {
                    $redirectedSource = $rows2[0]['source_url'];
                    $redirectedSource = eZURLAliasML::sanitizeURL( $redirectedSource );
                }
                if ( $redirectedSource === false )
                {
                    // Forwarded item does not exist, try to find the action in the ml table
                    $aliases = eZURLAliasML::fetchByAction( $actionType, $actionValue );
                    if ( $aliases )
                    {
                        $linkID = (int)$aliases[0]->attribute( 'id' );
                    }
                }
                if ( $redirectedSource === false and $linkID === false )
                {
                    // Did not find in ml table either, try to find one with same destination in old table
                    $rows2 = $db->arrayQuery( "SELECT source_url FROM ezurlalias WHERE destination_url = '" . $db->escapeString( $row['destination_url'] ) . "' AND forward_to_id = 0" );
                    if ( count( $rows2 ) == 0 )
                    {
                        // Did not find forwarded item, mark as error
                        logError( "Could not find urlalias entry with ID $forwardToID which was referenced by '{$forwardFromURL}' with ID " . $row['id'] );
                        list( $column, $counter ) = $displayProgressClosure( 'F', $urlImportStartTime, $counter, $urlCount, $column );
                        continue;
                    }
                    $redirectedSource = $rows2[0]['source_url'];
                }
                if ( $linkID === false )
                {
                    $elements = eZURLAliasML::fetchByPath( $redirectedSource );
                    if ( count( $elements ) != 0 )
                    {
                        $linkID = (int)$elements[0]->attribute( 'id' );
                    }
                }
                if ( $linkID === false )
                {
                    // Redirected source does not exist, try to find the action in the ml table
                    $aliases = eZURLAliasML::fetchByAction( $actionType, $actionValue );
                    if ( $aliases )
                    {
                        $linkID = (int)$aliases[0]->attribute( 'id' );
                    }
                }
                if ( $linkID === false )
                {
                    // Referenced url does not exist
                    logError( "The referenced path '$redirectedSource' can not be found among the new URL alias entries, old url entry is '{$forwardFromURL}' with ID " . $row['id'] );
                    list( $column, $counter ) = $displayProgressClosure( 'E', $urlImportStartTime, $counter, $urlCount, $column );
                    continue;
                }

                // Fetch the ID of the element to redirect to.
                $source      = $row['source_url'];
                $destination = $row['destination_url'];
                list( $action, $alwaysAvailable ) = decodeAction( $destination );
                $res = eZURLAliasML::storePath( $source, $action,
                                                false, $linkID, $alwaysAvailable, false,
                                                true, true );
                if ( !$res || $res['status'] !== true )
                {
                    logStoreError( $res, "eZURLAliasML::storePath", array( $source, $action, false, $linkID, $alwaysAvailable, false, true, true ) );
                    list( $column, $counter ) = $displayProgressClosure( 'E', $urlImportStartTime, $counter, $urlCount, $column );
                    continue;
                }
                $logStoreClosure( $res, "eZURLAliasML::storePath", array( $source, $action, false, $linkID, $alwaysAvailable, false, true, true ) );
                $result = '.';
                $verifyDataClosure( $result, $source, $row['id'] );
                list( $column, $counter ) = $displayProgressClosure( $result, $urlImportStartTime, $counter, $urlCount, $column );
            }
            markAsImported( $rows );
        } while ( $count > 0 );
        flush();
        if ( $column > 0 )
            $cli->output();
        $backupTablesClosure( 'impredir' );
    }

    if ( $importOldAliasWildcard )
    {
        // Then the wildcard changes
        $urlCount = fetchHistoricWildcardCount();
        $cli->output( "Importing {$urlCount} " . $cli->stylize( 'emphasize', "wildcards" ) );
        $column = $counter = $offset = 0;
        $urlImportStartTime = microtime( true );
        do
        {
            list( $rows, $offset ) = fetchHistoricWildcardChunk( 0, $fetchLimit );
            if ( !is_array( $rows ) )
            {
                break;
            }
            $count = count( $rows );
            foreach ( $rows as $key => $row )
            {
                $wildcardType        = (int)$row['is_wildcard']; // 1 is forward, 2 is direct (alias) for now they are both treated as forwarding/redirect
                $sourceWildcard      = $row['source_url'];
                $sourceWildcard = eZURLAliasML::sanitizeURL( $sourceWildcard );
                $destinationWildcard = $row['destination_url'];
                $destinationWildcard = eZURLAliasML::sanitizeURL( $destinationWildcard );
                if ( $row['is_wildcard'] && $row['is_internal'] != 1 )
                {
                    // If the wildcard is made by a user we import using the new wildcard system.
                    $row['type'] = (int)$row['is_wildcard'];

                    $wildcard = new eZURLWildcard( $row );
                    $wildcard->store();
                    list( $column, $counter ) = $displayProgressClosure( '.', $urlImportStartTime, $counter, $urlCount, $column );
                    continue;
                }

                while ( true )
                {
                    // Validate the wildcards
                    if ( !preg_match( "#^(.*)\*$#", $sourceWildcard, $matches ) )
                    {
                        logError( "Invalid source wildcard '$sourceWildcard', item is skipped, URL entry ID is " . $row['id'] );
                        list( $column, $counter ) = $displayProgressClosure( 'E', $urlImportStartTime, $counter, $urlCount, $column );
                        continue 2;
                    }
                    $fromPath = $matches[1];
                    $fromPath = eZURLAliasML::sanitizeURL( $fromPath );
                    if ( !preg_match( "#^(.*)\{1\}$#", $destinationWildcard, $matches ) )
                    {
                        logError( "Invalid destination wildcard '$destinationWildcard', item is skipped, URL entry ID is " . $row['id'] );
                        list( $column, $counter ) = $displayProgressClosure( 'E', $urlImportStartTime, $counter, $urlCount, $column );
                        continue 2;
                    }
                    $toPath = $matches[1];
                    $toPath = eZURLAliasML::sanitizeURL( $toPath );

                    $newWildcard = $toPath . '/*';
                    $newWildcardSQL = $db->escapeString( $newWildcard );
                    $query = "SELECT * FROM ezurlalias WHERE source_url = '{$newWildcardSQL}' AND is_wildcard=1";
                    $rowsw = $db->arrayQuery( $query );
                    if ( count( $rowsw ) == 0 )
                    {
                        // The redirection has stopped, we can use the destination
                        break;
                    }
                    $newSourceWildcard = $rowsw[0]['destination_url'];
                    if ( !preg_match( "#^(.*)\{1\}$#", $newSourceWildcard, $matches ) )
                    {
                        logError( "Invalid destination wildcard '$destinationWildcard', item is skipped, URL entry ID is " . $rowsw[0]['id'] );
                        list( $column, $counter ) = $displayProgressClosure( 'E', $urlImportStartTime, $counter, $urlCount, $column );
                        continue 2;
                    }
                    $newSourceWildcard = $matches[1];
                    $sourceWildcard = $newSourceWildcard;
                }

                $toPathSQL = $db->escapeString( $toPath );
                $query = "SELECT * FROM ezurlalias WHERE source_url = '{$toPathSQL}' AND is_wildcard = 0 AND forward_to_id = 0";
                $rowsw = $db->arrayQuery( $query );
                if ( count( $rowsw ) > 0 )
                {
                    list( $action, $alwaysAvailable ) = decodeAction( $rowsw[0]['destination_url'] );
                    list( $actionType, $actionValue ) = explode( ":", $action, 2 );
                    $elements = eZURLAliasML::fetchByAction( $actionType, $actionValue );
                    if ( $elements )
                    {
                        $toPath = $elements[0]->getPath();
                    }
                }

                $elements = eZURLAliasML::fetchByPath( $toPath );
                if ( count( $elements ) == 0 )
                {
                    // Referenced url does not exist
                    logError( "The referenced path '$toPath' can not be found among the new URL alias entries, url entry ID is " . $row['id'] );
                    list( $column, $counter ) = $displayProgressClosure( 'E', $urlImportStartTime, $counter, $urlCount, $column );
                    continue;
                }
                // Fetch the ID of the element to redirect to.
                $linkID = $elements[0]->attribute( 'id' );
                $action = $elements[0]->attribute( 'action' );
                if ( $action == 'nop:' )
                {
                    // Cannot redirect to nops
                    logError( "The referenced path '$toPath' with ID " . $elements[0]->attribute( 'id' ) . " is a 'nop:' entry and cannot be used" );
                    list( $column, $counter ) = $displayProgressClosure( 'E', $urlImportStartTime, $counter, $urlCount, $column );
                    continue;
                }
                $alwaysAvailable = ($elements[0]->attribute( 'lang_mask' ) & 1);
                $res = eZURLAliasML::storePath( $fromPath, $action,
                                                false, $linkID, $alwaysAvailable );
                if ( !$res || $res['status'] == 3 )
                {
                    logError( "The wildcard url " . var_export( $fromPath, true ) . " cannot be created since the path already exists" );
                    list( $column, $counter ) = $displayProgressClosure( 's', $urlImportStartTime, $counter, $urlCount, $column );
                    continue;
                }
                if ( !$res || $res['status'] !== true )
                {
                    logStoreError( $res, "eZURLAliasML::storePath", array( $fromPath, $action, false, $linkID, $alwaysAvailable ) );
                    list( $column, $counter ) = $displayProgressClosure( 'E', $urlImportStartTime, $counter, $urlCount, $column );
                    continue;
                }
                $logStoreClosure( $res, "eZURLAliasML::storePath", array( $fromPath, $action, false, $linkID, $alwaysAvailable ) );
                $result = '.';
                $verifyDataClosure( $result, $source, $row['id'] );
                list( $column, $counter ) = $displayProgressClosure( $result, $urlImportStartTime, $counter, $urlCount, $column );
            }
            markAsImported( $rows );
        } while ( $count > 0 );
        flush();
        if ( $column > 0 )
            $cli->output();
        $backupTablesClosure( 'impwcard' );
    }

//    $cli->output( "Removing urlalias data which have been imported" );
//    $db = eZDB::instance();
//    $db->query( "DELETE FROM ezurlalias WHERE is_imported = 1" ); // Removing all aliases which have been imported

    $rows = $db->arrayQuery( "SELECT count(*) AS count FROM ezurlalias WHERE is_imported = 0" );
    $remaining = $rows[0]['count'];
    if ( $remaining > 0 )
    {
        $cli->output( "There are $remaining remaining URL aliases in the old ezurlalias table, manual cleanup is needed." );
    }

    if ( $importOldAliasWildcard )
    {
        $cli->output( "Removing old wildcard caches" );
        eZCache::clearByID( 'urlalias' );
    }

    $cli->output( "Import completed" );

    $cli->output( "Import time taken: " . $cli->stylize( 'emphasize', formatTime( microtime( true ) - $globalStartTime ) ) );
}

// ============================================================================
// PATHPREFIX SAFETY ENHANCEMENT - CRITICAL FIX FOR URL ROUTING
// ============================================================================
// This ensures that if PathPrefix siteaccess routing is configured, all
// aliases are properly cloned to the prefixed namespace to prevent:
// - 301 redirect loops
// - kernel(20) errors on nested URLs  
// - Missing content pages
// ============================================================================

function findPathPrefixNamespaceRootID( $pathPrefix )
{
    $db = eZDB::instance();

    if ( !$pathPrefix )
        return false;

    $pathPrefixHash = $db->escapeString( aliasTextMd5( $pathPrefix ) );
    $rootSQL = "SELECT id FROM ezurlalias_ml WHERE parent = 0 AND text_md5 = '" . $pathPrefixHash . "' ORDER BY id";
    $rootRows = $db->arrayQuery( $rootSQL, array( 'limit' => 1 ) );

    if ( count( $rootRows ) == 0 )
        return false;

    return (int)$rootRows[0]['id'];
}

function deleteAliasSubtreeChildren( $parentID )
{
    $db = eZDB::instance();
    $deletedCount = 0;

    $children = $db->arrayQuery( "SELECT id FROM ezurlalias_ml WHERE parent = " . (int)$parentID . " ORDER BY id" );
    foreach ( $children as $child )
    {
        $childID = (int)$child['id'];
        $deletedCount += deleteAliasSubtreeChildren( $childID );
        $db->query( "DELETE FROM ezurlalias_ml WHERE id = " . $childID );
        ++$deletedCount;
    }

    return $deletedCount;
}

function findAliasRootIDsForNode( $nodeID, $namespaceRootID = false )
{
    $db = eZDB::instance();
    $action = $db->escapeString( 'eznode:' . (int)$nodeID );
    $rootIDs = array();

    $rows = $db->arrayQuery( "SELECT id, parent FROM ezurlalias_ml WHERE action = '" . $action . "' ORDER BY id" );
    foreach ( $rows as $row )
    {
        $parentID = (int)$row['parent'];
        if ( $parentID === 0 || ( $namespaceRootID !== false && $parentID === (int)$namespaceRootID ) )
            $rootIDs[] = (int)$row['id'];
    }

    return $rootIDs;
}

function findRootAliasIDs()
{
    $db = eZDB::instance();
    $rows = $db->arrayQuery( "SELECT id FROM ezurlalias_ml WHERE parent = 0 ORDER BY id" );
    $rootIDs = array();

    foreach ( $rows as $row )
    {
        $rootIDs[] = (int)$row['id'];
    }

    return $rootIDs;
}

function countAliasSubtreeNodes( $rootID )
{
    $db = eZDB::instance();

    if ( !$rootID )
        return 0;

    $count = 1;
    $stack = array( (int)$rootID );

    while ( count( $stack ) > 0 )
    {
        $parentID = array_pop( $stack );
        $children = $db->arrayQuery( "SELECT id FROM ezurlalias_ml WHERE parent = " . (int)$parentID . " ORDER BY id" );
        foreach ( $children as $child )
        {
            $childID = (int)$child['id'];
            ++$count;
            $stack[] = $childID;
        }
    }

    return $count;
}

function cloneAliasesToPathPrefix( $pathPrefix, $siteRootNodeID )
{
    $db = eZDB::instance();
    $cli = eZCLI::instance();
    $pathPrefixHash = $db->escapeString( aliasTextMd5( $pathPrefix ) );
    
    if ( !$pathPrefix )
        return 0;

    if ( !$siteRootNodeID )
        return 0;
    
    $cli->output( $cli->stylize( 'warning', "PathPrefix SAFETY: Cloning aliases to '{$pathPrefix}/*' namespace" ) );

    $prefixRootID = findPathPrefixNamespaceRootID( $pathPrefix );
    if ( !$prefixRootID )
    {
        $cli->error( "  Could not find PathPrefix root alias for '{$pathPrefix}'" );
        return 0;
    }

    // Normalize the prefix root to avoid self-redirect loops on /<prefix>.
    $db->query( "UPDATE ezurlalias_ml SET link = id, alias_redirects = 0, is_alias = 0, is_original = 1 WHERE id = " . (int)$prefixRootID );
    
    // Clone all root-level aliases to the prefixed namespace
    $rootAliasesSQL = "SELECT id FROM ezurlalias_ml WHERE parent = 0 AND text_md5 != '" . $pathPrefixHash . "' ORDER BY id";
    $rootAliases = $db->arrayQuery( $rootAliasesSQL );

    $rootAliasCount = count( $rootAliases );
    $cli->output( "  Preparing to clone {$rootAliasCount} root aliases into '{$pathPrefix}/*'" );

    $totalRowsRes = $db->arrayQuery( "SELECT COUNT(*) AS count FROM ezurlalias_ml" );
    $totalRows = (int)$totalRowsRes[0]['count'];
    $prefixRows = countAliasSubtreeNodes( $prefixRootID );
    $totalToScan = max( $totalRows - $prefixRows, 1 );
    
    $clonedCount = 0;
    $progressState = array( 'ticks' => 0,
                            'created' => 0,
                            'updated' => 0,
                            'total' => $totalToScan,
                            'start' => microtime( true ) );
    foreach ( $rootAliases as $alias )
    {
        $clonedCount += cloneAliasSubtree( (int)$alias['id'], $prefixRootID, $progressState );
    }

    if ( $progressState['ticks'] > 0 )
        $cli->output();

    $cli->output( "  Clone progress summary: scanned {$progressState['ticks']}, created {$progressState['created']}, updated {$progressState['updated']}" );
    
    return $clonedCount;
}

function cloneAliasSubtree( $sourceID, $targetParentID, &$progressState = null )
{
    $db = eZDB::instance();
    $cli = eZCLI::instance();
    $clonedCount = 0;

    if ( is_array( $progressState ) )
    {
        ++$progressState['ticks'];
        if ( ( $progressState['ticks'] % 250 ) == 0 )
        {
            $elapsed = max( microtime( true ) - $progressState['start'], 0.001 );
            $percent = min( 100.0, ( $progressState['ticks'] * 100.0 ) / $progressState['total'] );
            $rate = $progressState['ticks'] / $elapsed;
            $remaining = max( 0, $progressState['total'] - $progressState['ticks'] );
            $eta = $rate > 0 ? $remaining / $rate : 0;
            $cli->output( sprintf( "  Clone progress: %.2f%% (%d/%d), ETA %s", $percent, $progressState['ticks'], $progressState['total'], formatTime( $eta ) ) );
            flush();
        }
    }
    
    $sourceAliasSQL = "SELECT * FROM ezurlalias_ml WHERE id = " . (int)$sourceID;
    $sourceAliases = $db->arrayQuery( $sourceAliasSQL );
    
    if ( count( $sourceAliases ) == 0 )
        return 0;
    
    $sourceAlias = $sourceAliases[0];
    // Check if already exists under target parent
    $sourceAliasTextHash = $db->escapeString( aliasTextMd5( $sourceAlias['text'] ) );
    $existingSQL = "SELECT id, link, action, is_alias, alias_redirects, lang_mask FROM ezurlalias_ml WHERE parent = " . (int)$targetParentID . 
                   " AND text_md5 = '" . $sourceAliasTextHash . "'";
    $existingRows = $db->arrayQuery( $existingSQL );
    
    if ( count( $existingRows ) > 0 )
    {
        $targetID = (int)$existingRows[0]['id'];
        $needsUpdate = (int)$existingRows[0]['link'] !== $targetID ||
                       $existingRows[0]['action'] !== $sourceAlias['action'] ||
                       (int)$existingRows[0]['is_alias'] !== (int)$sourceAlias['is_alias'] ||
                       (int)$existingRows[0]['alias_redirects'] !== 0 ||
                       (int)$existingRows[0]['id'] !== (int)$targetID ||
                       (int)$existingRows[0]['lang_mask'] !== (int)$sourceAlias['lang_mask'];

        if ( $needsUpdate )
        {
            $updateSQL = "UPDATE ezurlalias_ml SET " .
                         "action = '" . $db->escapeString( $sourceAlias['action'] ) . "', " .
                         "link = " . $targetID . ", " .
                         "is_alias = " . (int)$sourceAlias['is_alias'] . ", " .
                         "is_original = 1, " .
                         "alias_redirects = 0, " .
                         "lang_mask = " . (int)$sourceAlias['lang_mask'] . " " .
                         "WHERE id = " . $targetID;
            $db->query( $updateSQL );
            if ( is_array( $progressState ) )
                ++$progressState['updated'];
        }
    }
    else
    {
        // Create new alias under prefixed namespace
        $newAlias = eZURLAliasML::create( $sourceAlias['text'], $sourceAlias['action'], 
                                          $targetParentID, $sourceAlias['lang_mask'] );
        $newAlias->setAttribute( 'is_original', 1 );
        $newAlias->setAttribute( 'is_alias', (int)$sourceAlias['is_alias'] );
        $newAlias->setAttribute( 'alias_redirects', 0 );
        $newAlias->store();
        $targetID = (int)$newAlias->attribute( 'id' );
        $db->query( "UPDATE ezurlalias_ml SET link = id, is_original = 1, alias_redirects = 0 WHERE id = " . (int)$targetID );
        $clonedCount++;
        if ( is_array( $progressState ) )
            ++$progressState['created'];
    }
    
    // Recursively clone all children
    $childrenSQL = "SELECT id FROM ezurlalias_ml WHERE parent = " . (int)$sourceID . " ORDER BY id";
    $children = $db->arrayQuery( $childrenSQL );
    
    foreach ( $children as $child )
    {
        $clonedCount += cloneAliasSubtree( (int)$child['id'], $targetID, $progressState );
    }
    
    return $clonedCount;
}

function normalizePathPrefixNamespace( $prefixRootID, $showProgress = true )
{
    $db = eZDB::instance();
    $cli = eZCLI::instance();

    if ( !$prefixRootID )
        return 0;

    $normalizedCount = 0;

    $db->query( "UPDATE ezurlalias_ml SET link = id, is_original = 1, is_alias = 0, alias_redirects = 0 WHERE id = " . (int)$prefixRootID );

    $totalToNormalize = 0;
    $startTime = microtime( true );
    if ( $showProgress )
    {
        $totalToNormalize = max( countAliasSubtreeNodes( $prefixRootID ) - 1, 1 );
    }

    $stack = array( (int)$prefixRootID );
    $ticks = 0;
    while ( count( $stack ) > 0 )
    {
        $parentID = array_pop( $stack );
        $children = $db->arrayQuery( "SELECT id FROM ezurlalias_ml WHERE parent = " . (int)$parentID . " ORDER BY id" );
        foreach ( $children as $child )
        {
            $childID = (int)$child['id'];
            $db->query( "UPDATE ezurlalias_ml SET link = id, is_original = 1, is_alias = 0, alias_redirects = 0 WHERE id = " . $childID );
            ++$normalizedCount;
            ++$ticks;
            if ( $showProgress && ( $ticks % 250 ) == 0 )
            {
                $elapsed = max( microtime( true ) - $startTime, 0.001 );
                $percent = min( 100.0, ( $ticks * 100.0 ) / $totalToNormalize );
                $rate = $ticks / $elapsed;
                $remaining = max( 0, $totalToNormalize - $ticks );
                $eta = $rate > 0 ? $remaining / $rate : 0;
                $cli->output( sprintf( "  Normalize progress: %.2f%% (%d/%d), ETA %s", $percent, $ticks, $totalToNormalize, formatTime( $eta ) ) );
                flush();
            }
            $stack[] = $childID;
        }
    }

    if ( $showProgress && $ticks > 0 )
        $cli->output( "  Normalize progress: 100.00% ({$ticks}/{$totalToNormalize}), ETA 0s" );

    return $normalizedCount;
}

if ( $updateNodeAlias )
{
    $nodeGlobalStartTime = microtime( true );
    $nativePathPrefix = false;

    list( $integrityOk, $integrityError ) = checkUrlAliasIntegrity();
    if ( !$integrityOk )
    {
        $cli->error( $integrityError );
        $cli->error( "Refusing to continue because updateSubTreePath() would crash on corrupted URL aliases." );
        $cli->error( "Run bin/php/verify_aliases.php --verbose or restore a clean database backup first." );
        $script->shutdown( 1 );
    }
    
    // PATHPREFIX SAFETY: Detect and warn about PathPrefix configuration
    $pathPrefix = eZURLAliasML::getPathPrefix();
    if ( $pathPrefix )
    {
        $cli->output( "" );
        $cli->output( $cli->stylize( 'warning', "WARNING: PathPrefix '{$pathPrefix}' detected; rebuilding '{$pathPrefix}/*' namespace after node updates" ) );
        $cli->output( "" );
        $nativePathPrefix = $pathPrefix;

        // Avoid native updateSubTreePath() writing redirecting entries under
        // the PathPrefix namespace while updates are in progress.
        $runtimeINI = eZINI::instance();
        $runtimeINI->setVariable( 'SiteAccessSettings', 'PathPrefix', '' );
    }
    
    // Start updating nodes
    $topLevelNodesArray = $db->arrayQuery( 'SELECT node_id FROM ezcontentobject_tree WHERE depth = 1 ORDER BY node_id' );
    $pathPrefixRootNodeID = count( $topLevelNodesArray ) > 0 ? (int)$topLevelNodesArray[0]['node_id'] : 0;

    // Keep reruns non-destructive: do not purge existing alias subtrees up-front.
    // If execution is interrupted mid-run, preserving existing rows avoids global 404 fallout.
    if ( $pathPrefix )
    {
        $cli->output( "Synchronizing PathPrefix namespace before node updates" );
        $preClonedAliasCount = cloneAliasesToPathPrefix( $pathPrefix, $pathPrefixRootNodeID );
        $prefixRootID = findPathPrefixNamespaceRootID( $pathPrefix );
        $preNormalizedAliasCount = normalizePathPrefixNamespace( $prefixRootID );
        $cli->output( "Pre-synchronized {$preClonedAliasCount} aliases to '{$pathPrefix}/*' namespace" );
        $cli->output( "Pre-normalized {$preNormalizedAliasCount} aliases under '{$pathPrefix}/*' namespace" );
        eZCache::clearByID( 'urlalias' );
    }
    else
    {
        $prefixRootID = false;
    }

    foreach ( array_keys( $topLevelNodesArray ) as $key )
    {
        $topLevelNodeID = $topLevelNodesArray[$key]['node_id'];
        $rootNode = eZContentObjectTreeNode::fetch( $topLevelNodeID );
        $done = false;
        $offset = 0;
        $counter = 0;
        $column = 0;
        $changedNodes = 0;
        $nodeCount = $rootNode->subTreeCount( array( 'Limitation' => array(),
                                                     'IgnoreVisibility' => true ) );
        $totalNodeCount += $nodeCount;
        $cli->output( "Starting updates for " . $cli->stylize( 'mark', $rootNode->attribute( 'name' ) ) . ", $nodeCount nodes" );
        $nodeStartTime = microtime( true );
        while ( !$done )
        {
            $nodeList = $rootNode->subTree( array( 'Offset' => $offset,
                                                    'Limit' => $fetchLimit,
                                                    'IgnoreVisibility' => true,
                                                    'Limitation' => array() ) );
            foreach ( array_keys( $nodeList ) as $key )
            {
                $node = $nodeList[ $key ];
                $hasChanged = false;
                try
                {
                    $hasChanged = $node->updateSubTreePath();
                }
                catch ( Exception $e )
                {
                    logError( "updateSubTreePath failed for node " . $node->attribute( 'node_id' ) . ": " . $e->getMessage() );
                    $hasChanged = false;
                }
                if ( $hasChanged )
                {
                    ++$changedNodes;
                    ++$totalChangedNodes;
                }
                $changeCharacters = array( '.', '+', '*' );
                $changeCharacter = '.';
                if ( isset( $changeCharacters[$hasChanged] ) )
                    $changeCharacter = $changeCharacters[$hasChanged];
                $verifyNodeDataClosure( $changeCharacter, $node );
                list( $column, $counter ) = $displayProgressClosure( $changeCharacter, $nodeStartTime, $counter, $nodeCount, $column );
            }
            if ( count( $nodeList ) == 0 )
                $done = true;

            if ( $prefixRootID )
            {
                normalizePathPrefixNamespace( $prefixRootID, false );
                eZCache::clearByID( 'urlalias' );
            }

            unset( $nodeList );
            $offset += $fetchLimit;
            eZContentObject::clearCache();
        }
        flush();
        if ( $column > 0 )
            $cli->output();
        $cli->output( "Updated " . $cli->stylize( 'emphasize', "$changedNodes/$nodeCount" ) . " for " . $cli->stylize( 'mark', $rootNode->attribute( 'name' ) ) );
        $cli->output();
        $backupTablesClosure( 'node_' . strtolower( $rootNode->attribute( 'name' ) ) );
    }

    $cli->output();
    $cli->output( "Total update " . $cli->stylize( 'emphasize', "$totalChangedNodes/$totalNodeCount" ) );
    $cli->output( "Node time taken: " . $cli->stylize( 'emphasize', formatTime( microtime( true ) - $nodeGlobalStartTime ) ) );
    
    // PATHPREFIX SAFETY: Manual clone step disabled.
    // Under the digg siteaccess, updateSubTreePath() already writes the prefixed namespace.
    // Running a second clone pass reintroduces duplicate-key corruption.
    if ( $pathPrefix )
    {
        $cli->output( "" );
        $cli->output( $cli->stylize( 'warning', "=== PATHPREFIX SAFETY EXECUTION ===" ) );
        $aliasCountBefore = $db->arrayQuery( "SELECT COUNT(*) as count FROM ezurlalias_ml" );
        $beforeCount = $aliasCountBefore[0]['count'];
        
        $clonedAliasCount = cloneAliasesToPathPrefix( $pathPrefix, $pathPrefixRootNodeID );
        $prefixRootID = findPathPrefixNamespaceRootID( $pathPrefix );
        $normalizedAliasCount = normalizePathPrefixNamespace( $prefixRootID );
        
        $aliasCountAfter = $db->arrayQuery( "SELECT COUNT(*) as count FROM ezurlalias_ml" );
        $afterCount = $aliasCountAfter[0]['count'];
        
        $cli->output( "  Total aliases before cloning: {$beforeCount}" );
        $cli->output( $cli->stylize( 'emphasize', "  Cloned {$clonedAliasCount} aliases to '{$pathPrefix}/*' namespace" ) );
        $cli->output( $cli->stylize( 'emphasize', "  Normalized {$normalizedAliasCount} aliases under '{$pathPrefix}/*' namespace" ) );
        $cli->output( "  Total aliases after cloning: {$afterCount}" );
        
        // Clear cache
        eZCache::clearByID( 'urlalias' );
        $cli->output( "  URL alias cache cleared" );
        $cli->output( "" );
    }
}


$cli->output( "Total time taken: " . $cli->stylize( 'emphasize', formatTime( microtime( true ) - $globalStartTime ) ) );

$script->shutdown();

?>