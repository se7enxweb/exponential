#!/usr/bin/env php
<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

require 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 
                                      'description' => 'Exponential Package Manager CLI - Create, Import, Delete, Install, List Exp/eZp Packages',
                                      'debug-message' => '',
                                      'use-session' => true,
                                      'use-modules' => true,
                                      'use-extensions' => true ) );

$script->startup();


$endl = $cli->endlineString();
$webOutput = $cli->isWebOutput();

function help()
{
    $argv = $_SERVER['argv'];
    $cli = eZCLI::instance();
    $cli->output( "Usage: " . $argv[0] . " [OPTION]... COMMAND [COMMAND OPTION]... [-- COMMAND [COMMAND OPTION]...]...\n" .
                  "eZ Publish package manager.\n" .
                  "\n" .
                  "Type " . $argv[0] . " help for command overview\n" .
                  "\n" .
                  "General options:\n" .
                  "  -h,--help            display this help and exit \n" .
                  "  -q,--quiet           do not give any output except when errors occur\n" .
                  "  -s,--siteaccess      selected siteaccess for operations, if not specified default siteaccess is used\n" .
                  "  -d,--debug           display debug output at end of execution\n" .
                  "  -c,--colors          display output using ANSI colors (default)\n" .
                  "  -l,--login USER      login with USER and use it for all operations\n" .
                  "  -p,--password PWD    use PWD as password for USER\n" .
                  "  -r,--repos REPOS     use REPOS for repository when accessing packages\n" .
                  "  --db-type TYPE       set type of db to use\n" .
                  "  --db-name NAME       set name of db to use\n" .
                  "  --db-user USER       set database user\n" .
                  "  --db-password PASSWD set password for database user\n" .
                  "  --db-socket SOCKET   set socket for db connection\n" .
                  "  --db-host HOST       set host name for db connection\n" .
                  "  --logfiles           create log files\n" .
                  "  --no-logfiles        do not create log files (default)\n" .
                  "  --no-colors          do not use ANSI coloring\n" );
}

function helpCreate()
{
    $cli = eZCLI::instance();
    $cli->output( "create: Create a new, empty package.\n" .
                  "usage: create NAME [SUMMARY [VERSION [INSTALLTYPE]]]\n" .
                  "\n" .
                  "SUMMARY:     A short summary of your package\n" .
                  "VERSION:     The version of your package, default is 1.0\n" .
                  "INSTALLTYPE: Use install (default) for a package that installs files or\n" .
                  "             import for a package that can only be imported.\n" .
                  "\n" .
                  "A content-tree package is built in three steps: create, add, export.\n" .
                  "Example - package the entire content tree from the true root node (ID 1):\n" .
                  "  create mypackage 'My package summary' 1.0.1 install\n" .
                  "  add mypackage ezcontentobject 1\n" .
                  "  export mypackage -d /path/to/output\n"
                  );
}

function helpExport()
{
    $cli = eZCLI::instance();
    $cli->output( "export: Export a built package to a .ezpkg archive.\n" .
                  "usage: export PACKAGE [-d DIRECTORY]\n" .
                  "\n" .
                  "Options:\n" .
                  "  -d DIRECTORY   export the .ezpkg archive to DIRECTORY\n" .
                  "  (no -d)        export the .ezpkg archive to the current directory\n"
                  );
}

function helpInstall()
{
    $cli = eZCLI::instance();
    $cli->output( "install: Install an eZ Publish package.\n" .
                  "usage: install PACKAGE [-d NODE_ID | --destination-node-id NODE_ID]\n" .
                  "\n" .
                  "PACKAGE is the name of the of package\n" .
                  "\n" .
                  "Options:\n" .
                  "  -d NODE_ID, --destination-node-id NODE_ID  install content objects under the given parent node ID\n" .
                  "                                  (default is the root node, 2)\n"
                  );
}

function helpImport()
{
    $cli = eZCLI::instance();
    $cli->output( "import: Import an eZ Publish package.\n" .
                  "usage: import PACKAGE_FILE\n" .
                  "\n" .
                  "PACKAGE_FILE is the path to the .ezpkg package file\n"
                  );
}

function helpList()
{
    $cli = eZCLI::instance();
    $cli->output( "list (ls): Lists all the packages\n" .
                  "If repository ID is given (-r option) it will show packages\n" .
                  "only from the given repository" .
                  "usage: list\n"
                  );
}

function helpInfo()
{
    $cli = eZCLI::instance();
    $cli->output( "info: Displays information on a given package.\n" .
                  "usage: info PACKAGE\n"
                  );
}

function helpAdd()
{
    $cli = eZCLI::instance();
    $cli->output( "add: Adds an eZ Publish item to the package.\n" .
                  "usage: add PACKAGE ITEM [ITEMPARAMETERS]...\n" .
                  "\n" .
                  "Items:\n" .
                  "  group:             Add categorization groups\n" .
                  "  ezcontentclass:    Add contentclass definitions\n" .
                  "  ezcontentobject:   Add content objects and their subtrees\n" .
                  "  ezcontentsubtree:  Alias for ezcontentobject; exports the node and its subtree\n" .
                  "\n" .
                  "ezcontentobject parameters:\n" .
                  "  NODE                 Numeric node ID or URL path of the starting node\n" .
                  "                       (all children below it are included by default)\n" .
                  "  --include-classes    Include content classes in the package (default)\n" .
                  "  --exclude-classes    Do not include content classes\n" .
                  "  --include-templates  Include template overrides (default)\n" .
                  "  --exclude-templates  Do not include template overrides\n" .
                  "  --node-main          Export main node assignment only (default)\n" .
                  "  --node-selected      Export all selected node assignments\n" .
                  "  --siteaccess=sa,sa2  SiteAccesses to collect templates from\n" .
                  "  --language=loc1,loc2 Languages to export (default: all)\n" .
                  "  --current-version    Export current version only (default)\n" .
                  "  --all-versions       Export all versions\n" .
                  "  --minimal-template-set  Include only the minimal template set\n" .
                  "\n" .
                  "Examples:\n" .
                  "  add mypackage ezcontentobject 1\n" .
                  "    Export the entire content tree starting at the true root node (ID 1)\n" .
                  "  add mypackage ezcontentobject 60 --exclude-classes\n" .
                  "    Export the subtree under node 60 without content classes\n" .
                  "  add mypackage ezcontentobject /content/my-page --siteaccess=sevenx_site_user\n" .
                  "    Export the subtree under /content/my-page using templates from sevenx_site_user\n" .
                  "\n" .
                  "Note: Will open up a new release if no open releases exists yet.\n"
                  );
}

function helpSet()
{
    $cli = eZCLI::instance();
    $cli->output( "set: Sets an attribute in the package.\n" .
                  "usage: set PACKAGE ATTRIBUTE ATTRIBUTEVALUE\n" .
                  "\n" .
                  "Attributes:\n" .
                  "  summary     :\n" .
                  "  description :\n" .
                  "  vendor      :\n" .
                  "  priority    :\n" .
                  "  type        :\n" .
                  "  extension   :\n" .
                  "  source      :\n" .
                  "  version     :\n" .
                  "  licence     :\n" .
                  "  state       :\n" .
                  "Note: Will open up a new release if no open releases exists yet.\n"
                  );
}

function helpDelete()
{
    $cli = eZCLI::instance();
    $cli->output( "delete (del, remove, rm): Removes an eZ Publish item from the package.\n" .
                  "usage: delete PACKAGE ITEM [ITEMPARAMETERS]...\n" .
                  "\n" .
                  "Note: Will open up a new release if no open releases exists yet.\n"
                  );
}

function helpHelp()
{
    $argv = $_SERVER['argv'];
    $cli = eZCLI::instance();
    $cli->output( "help: Displays help information on commands.\n" .
                  "usage: help COMMAND\n" .
                  "\n" .
                  "Type \"" . $argv[0] . " help COMMAND\" for help on a specific command.\n" .
                  "\n" .
                  "Available commands:\n" .
                  "   help (?, h)\n" .
                  "   create\n" .
                  "   install\n" .
                  "   import\n" .
                  "   export\n" .
                  "   add\n" .
                  "   set\n" .
                  "   delete (del, remove, rm)\n" .
                  "   list\n" .
                  "   info\n"
                  );
}

function changeSiteAccessSetting( $siteAccess )
{
    global $siteaccess;
    $siteaccess = $siteAccess;
    $cli = eZCLI::instance();
    if ( file_exists( 'settings/siteaccess/' . $siteAccess) )
    {
        $cli->output( "Using siteaccess $siteAccess for nice url update" );
    }
    else
    {
        $cli->notice( "Siteaccess $siteAccess does not exist, using default siteaccess" );
    }
}

$siteaccess = false;
$debugOutput = false;
$allowedDebugLevels = false;
$useDebugAccumulators = false;
$useDebugTimingpoints = false;
$useIncludeFiles = false;
$useColors = true;
$isQuiet = false;
$useLogFiles = false;
$userLogin = false;
$userPassword = false;
$command = false;
$repositoryID = false;

$dbUser = false;
$dbPassword = false;
$dbSocket = false;
$dbHost = false;
$dbType = false;
$dbName = false;

// $packageName = false;
// $packageAttribute = false;
// $packageAttributeValue = false;
// $packagePart = false;
// $packagePartParameters = array();
// $packageSummary = false;
// $packageLicence = false;
// $packageVersion = false;
// $packageFile = false;

$commandList = array();

function resetCommandItem()
{
    $commandItem = array( 'command' => false,
                          'name' => false,
                          'attribute' => false,
                          'attribute-value' => false,
                          'item' => false,
                          'item-parameters' => array(),
                          'summary' => false,
                          'installtype' => false,
                          'version' => false,
                          'file' => false,
                          'destination-node-id' => false );
    return $commandItem;
}

$commandItem = resetCommandItem();

$optionsWithData = array( 's', 'o', 'l', 'p', 'r' );
$longOptionsWithData = array( 'siteaccess', 'login', 'password', 'repos', 'destination-node-id',
                              'db-type', 'db-name', 'db-user', 'db-password', 'db-socket', 'db-host' );

$commandAlias = array();
$commandAlias['help'] = array( '?', 'h' );
$commandAlias['delete'] = array( 'del', 'remove', 'rm' );
$commandAlias['list'] = array( 'ls' );
$commandMap = array();

foreach ( $commandAlias as $alias => $list )
{
    foreach ( $list as $commandName )
    {
        $commandMap[$commandName] = $alias;
    }
}

$readOptions = true;

for ( $i = 1; $i < count( $argv ); ++$i )
{
    $arg = $argv[$i];
    if ( $arg == '--' )
    {
        $commandList[]=& $commandItem;
        $commandItem = resetCommandItem();
    }
    else if ( $readOptions and
         strlen( $arg ) > 0 and
         $arg[0] == '-' )
    {
        if ( strlen( $arg ) > 1 and
             $arg[1] == '-' )
        {
            $flag = substr( $arg, 2 );
            if ( preg_match( '#^([^=]+)=(.+)$#', $flag, $matches ) )
            {
                $flag = $matches[1];
                $optionData = $matches[2];
            }
            else if ( in_array( $flag, $longOptionsWithData ) )
            {
                $optionData = $argv[$i+1];
                ++$i;
            }
            if ( $flag == 'help' )
            {
                help();
                exit();
            }
            else if ( $flag == 'siteaccess' )
            {
                changeSiteAccessSetting( $optionData );
            }
            else if ( $flag == 'debug' )
            {
                $debugOutput = true;
            }
            else if ( $flag == 'quiet' )
            {
                $isQuiet = true;
            }
            else if ( $flag == 'colors' )
            {
                $useColors = true;
            }
            else if ( $flag == 'no-colors' )
            {
                $useColors = false;
            }
            else if ( $flag == 'no-logfiles' )
            {
                $useLogFiles = false;
            }
            else if ( $flag == 'logfiles' )
            {
                $useLogFiles = true;
            }
            else if ( $flag == 'login' )
            {
                $userLogin = $optionData;
            }
            else if ( $flag == 'password' )
            {
                $userPassword = $optionData;
            }
            else if ( $flag == 'repos' )
            {
                $repositoryID = $optionData;
            }
            else if ( $flag == 'destination-node-id' )
            {
                $commandItem['destination-node-id'] = (int)$optionData;
            }
            else if ( $flag == 'db-user' )
            {
                $dbUser = $optionData;
            }
            else if ( $flag == 'db-password' )
            {
                $dbPassword = $optionData;
            }
            else if ( $flag == 'db-socket' )
            {
                $dbSocket = $optionData;
            }
            else if ( $flag == 'db-host' )
            {
                $dbHost = $optionData;
            }
            else if ( $flag == 'db-type' )
            {
                $dbType = $optionData;
            }
            else if ( $flag == 'db-name' )
            {
                $dbName = $optionData;
            }
        }
        else
        {
            $flag = substr( $arg, 1, 1 );
            $optionData = false;
            if ( in_array( $flag, $optionsWithData ) )
            {
                if ( strlen( $arg ) > 2 )
                {
                    $optionData = substr( $arg, 2 );
                }
                else
                {
                    $optionData = $argv[$i+1];
                    ++$i;
                }
            }
            if ( $flag == 'h' )
            {
                help();
                exit();
            }
            else if ( $flag == 'q' )
            {
                $isQuiet = true;
            }
            else if ( $flag == 'c' )
            {
                $useColors = true;
            }

            else if ( $flag == 's' )
            {
                changeSiteAccessSetting( $optionData );
            }
            else if ( $flag == 'l' )
            {
                $userLogin = $optionData;
            }
            else if ( $flag == 'p' )
            {
                $userPassword = $optionData;
            }
            else if ( $flag == 'r' )
            {
                $repositoryID = $optionData;
            }
            else if ( $flag == 'd' )
            {
                // For the install command, -d is the destination node ID.
                // Use --debug for debug output in install commands.
                if ( $commandItem['command'] === 'install' )
                {
                    if ( strlen( $arg ) > 2 )
                    {
                        $commandItem['destination-node-id'] = (int)substr( $arg, 2 );
                    }
                    else if ( isset( $argv[$i+1] ) && is_numeric( $argv[$i+1] ) )
                    {
                        $commandItem['destination-node-id'] = (int)$argv[$i+1];
                        ++$i;
                    }
                }
                else
                {
                    $debugOutput = true;
                    if ( strlen( $arg ) > 2 )
                    {
                        $levels = explode( ',', substr( $arg, 2 ) );
                        $allowedDebugLevels = array();
                        foreach ( $levels as $level )
                        {
                            if ( $level == 'all' )
                            {
                                $useDebugAccumulators = true;
                                $allowedDebugLevels = false;
                                $useDebugTimingpoints = true;
                                break;
                            }
                            if ( $level == 'accumulator' )
                            {
                                $useDebugAccumulators = true;
                                continue;
                            }
                            if ( $level == 'timing' )
                            {
                                $useDebugTimingpoints = true;
                                continue;
                            }
                            if ( $level == 'include' )
                            {
                                $useIncludeFiles = true;
                            }
                            if ( $level == 'error' )
                                $level = eZDebug::LEVEL_ERROR;
                            else if ( $level == 'warning' )
                                $level = eZDebug::LEVEL_WARNING;
                            else if ( $level == 'debug' )
                                $level = eZDebug::LEVEL_DEBUG;
                            else if ( $level == 'notice' )
                                $level = eZDebug::LEVEL_NOTICE;
                            else if ( $level == 'timing' )
                                $level = eZDebug::LEVEL_TIMING_POINT;
                            $allowedDebugLevels[] = $level;
                        }
                    }
                }
            }
        }
    }
    else
    {
        if ( $commandItem['command'] === false )
        {
            $realCommand = $arg;
            // Check for alias
            if ( isset( $commandMap[$realCommand] ) )
                $commandItem['command'] = $commandMap[$realCommand];
            else
                $commandItem['command'] = $realCommand;
            if ( !in_array( $commandItem['command'],
                           array( 'help',
                                  'create', 'import', 'install', 'export',
                                  'add', 'set', 'delete',
                                  'list', 'info' ) ) )
            {
                help();
                exit( 1 );
            }
            $readOptions = false;
        }
        else
        {
            if ( $commandItem['command'] == 'help' )
            {
                $realHelpTopic = $arg;
                // Check for alias
                if ( isset( $commandMap[$realHelpTopic] ) )
                    $helpTopic = $commandMap[$realHelpTopic];
                else
                    $helpTopic = $realHelpTopic;
                if ( $helpTopic == 'import' )
                    helpImport();
                else if ( $helpTopic == 'install' )
                    helpInstall();
                else if ( $helpTopic == 'export' )
                    helpExport();
                else if ( $helpTopic == 'create' )
                    helpCreate();
                else if ( $helpTopic == 'add' )
                    helpAdd();
                else if ( $helpTopic == 'set' )
                    helpSet();
                else if ( $helpTopic == 'delete' )
                    helpDelete();
                else if ( $helpTopic == 'list' )
                    helpList();
                else if ( $helpTopic == 'info' )
                    helpInfo();
                else
                    helpHelp();
                exit();
            }
            else if ( $commandItem['command'] == 'create' )
            {
                if ( $commandItem['name'] === false )
                    $commandItem['name'] = $arg;
                else if ( $commandItem['summary'] === false )
                    $commandItem['summary'] = $arg;
                else if ( $commandItem['version'] === false )
                    $commandItem['version'] = $arg;
                else if ( $commandItem['installtype'] === false )
                    $commandItem['installtype'] = strtolower( $arg );
            }
            else if ( $commandItem['command'] == 'set' )
            {
                if ( $commandItem['name'] === false )
                    $commandItem['name'] = $arg;
                else if ( $commandItem['attribute'] === false )
                    $commandItem['attribute'] = $arg;
                else if ( $commandItem['attribute-value'] === false )
                    $commandItem['attribute-value'] = $arg;
            }
            else if ( $commandItem['command'] == 'add' )
            {
                if ( $commandItem['name'] === false )
                    $commandItem['name'] = $arg;
                else if ( $commandItem['item'] === false )
                    $commandItem['item'] = $arg;
                else
                    $commandItem['item-parameters'][] = $arg;
            }
            else if ( $commandItem['command'] == 'info' )
            {
                if ( $commandItem['name'] === false )
                    $commandItem['name'] = $arg;
                else if ( $arg[0] == '-' )
                {
                    $infoOptions = substr( $arg, 1 );
                    if ( preg_match( '#[dfi]#', $infoOptions ) )
                    {
                        if ( !isset( $commandItem['info-types'] ) )
                            $commandItem['info-types'] = array();
                        if ( strpos( $infoOptions, 'd' ) !== false )
                            $commandItem['info-types'][] = 'dependency';
                        if ( strpos( $infoOptions, 'f' ) !== false )
                            $commandItem['info-types'][] = 'file';
                        if ( strpos( $infoOptions, 'i' ) !== false )
                            $commandItem['info-types'][] = 'info';
                    }
                }
            }
            else if ( $commandItem['command'] == 'import' )
            {
               if ( $commandItem['name'] === false )
                    $commandItem['name'] = $arg;
            }
            else if ( $commandItem['command'] == 'install' )
            {
                if ( $commandItem['name'] === false )
                    $commandItem['name'] = $arg;
            }
            else if ( $commandItem['command'] == 'export' )
            {
                if ( $commandItem['name'] === false )
                    $commandItem['name'] = $arg;
                else if ( $arg == '-d' )
                {
                    ++$i;
                    $commandItem['export-directory'] = $argv[$i];
                }
            }
            else if ( $commandItem['command'] == 'delete' )
            {
                if ( $commandItem['name'] === false )
                    $commandItem['name'] = $arg;
            }
        }
    }
}
$script->setIsQuiet( $isQuiet );
$script->setUseDebugOutput( $debugOutput );
$script->setAllowedDebugLevels( $allowedDebugLevels );
$script->setUseDebugAccumulators( $useDebugAccumulators );
$script->setUseDebugTimingPoints( $useDebugTimingpoints );
$script->setUseIncludeFiles( $useIncludeFiles );


$commandList[] = $commandItem;

// Check all commands
foreach ( $commandList as $commandItem )
{
    if ( $commandItem['command'] == 'add' )
    {
        if ( !$commandItem['name'] and
             !$commandItem['item'] )
        {
            helpAdd();
            exit( 1 );
        }
    }
    else if ( $commandItem['command'] == 'set' )
    {
        if ( !$commandItem['name'] and
             !$commandItem['attribute'] and
             !$commandItem['attribute-value'] )
        {
            helpSet();
            exit( 1 );
        }
    }
    else if ( $commandItem['command'] == 'create' )
    {
        if ( !$commandItem['name'] )
        {
            helpCreate();
            exit( 1 );
        }
    }
    else if ( $commandItem['command'] == 'info' )
    {
        if ( !$commandItem['name'] )
        {
            helpInfo();
            exit( 1 );
        }
    }
    else if ( $commandItem['command'] == 'export' )
    {
        if ( !$commandItem['name'] )
        {
            helpExport();
            exit( 1 );
        }
    }
    else if ( $commandItem['command'] == 'import' )
    {
        if ( !$commandItem['name'] )
        {
            helpImport();
            exit( 1 );
        }
    }
    else if ( $commandItem['command'] == 'install' )
    {
        if ( !$commandItem['name'] )
        {
            helpInstall();
            exit( 1 );
        }
    }
    else if ( in_array( $commandItem['command'],
                        array( 'list' ) ) )
    {
    }
    else if ( $commandItem['command'] == 'help' )
    {
        helpHelp();
        exit( 0 );
    }
    else if ( $commandItem['command'] == 'delete' )
    {
        if ( !$commandItem['name'] )
        {
            helpDelete();
            exit( 1 );
        }
    }
    else
    {
        help();
        exit( 1 );
    }
}

if ( $webOutput )
    $useColors = true;

$cli->setUseStyles( $useColors );
$script->setDebugMessage( "\n\n" . str_repeat( '#', 36 ) . $cli->style( 'emphasize' ) . " DEBUG " . $cli->style( 'emphasize-end' )  . str_repeat( '#', 36 ) . "\n" );

if ( !$siteaccess )
{
    $siteaccess = eZINI::instance()->variable( 'SiteSettings', 'DefaultAccess' );
    if ( !$siteaccess )
        $siteaccess = 'sevenx_site_user';
}

$script->setUseSiteAccess( $siteaccess );

// Check the database settings and initialize them as current settings
if ( $dbUser !== false or $dbHost !== false or $dbSocket !== false or
     $dbType !== false or $dbName !== false )
{
    if ( $dbUser === false )
    {
        $cli->error( "No --db-user specified, cannot connect without a user." );
        $script->shutdown( 1 );
    }

    if ( $dbType === false )
    {
        $cli->error( "No --db-type specified, cannot connect without a specific type." );
        $script->shutdown( 1 );
    }

    $params = array( 'use_defaults' => false,
                     'server' => $dbHost,
                     'user' => $dbUser,
                     'socket' => $dbSocket,
                     'password' => $dbPassword,
                     'database' => $dbName );
    $db = eZDB::instance( $dbType,
                           $params,
                           true );

    if ( !$db->isConnected() )
    {
        $str = "Failed to connect to database: $dbType://$dbUser@$dbHost";
        $cli->error( $str );
        $script->shutdown( 1 );
    }
    eZDB::setInstance( $db );

    // Only continue if the database is using the same version as the PHP code
    $rows = $db->arrayQuery( "SELECT * FROM ezsite_data WHERE name = 'ezpublish-version'" );
    if ( count( $rows ) > 0 )
    {
        $version = $rows[0]['value'];
        if ( version_compare( $version, eZPublishSDK::version() ) != 0 )
        {
            $cli->error( "Version '$version' in database '$dbName' is different from the running version " . eZPublishSDK::version() );
            $script->shutdown( 1 );
        }
    }
}

$script->setUser( $userLogin, $userPassword );

$script->initialize();

$alreadyCreated = false;

$createdPackages = array();

foreach ( $commandList as $commandItem )
{
    $command = $commandItem['command'];

    // Prevent concurrent long-running install/add commands from clobbering each
    // other and causing database lock-wait timeouts.
    if ( $command == 'install' || $command == 'add' )
    {
        $lockFile = eZDir::path( array( eZSys::varDirectory(), 'ezpm' ) ) . '.lock';
        eZDir::mkdir( eZSys::varDirectory(), false, true );
        $lockHandle = @fopen( $lockFile, 'c' );
        if ( $lockHandle && !@flock( $lockHandle, LOCK_EX | LOCK_NB ) )
        {
            $cli->error( 'Another ezpm process is currently running. Please wait for it to finish, or kill it with `killall -9 php` and try again.' );
            $script->shutdown( 1 );
        }
    }

    if ( $command != 'help' )
        expScriptStatus::instance()->start( $command, 'Command: ' . $command );

    // For long install/add operations, disable view/template cache clearing and
    // delay search indexing until after the run. This is safe for package
    // installation because no frontend pages are being served, and the user can
    // rebuild search indexes later with the updatesearchindex script.
    if ( $command == 'install' || $command == 'add' )
    {
        eZINI::instance( 'site.ini' )->setVariables(
            array(
                'ContentSettings' => array(
                    'ViewCaching' => 'disabled',
                ),
                'TemplateSettings' => array(
                    'TemplateCache' => 'disabled',
                ),
                'SearchSettings' => array(
                    'DelayedIndexing' => 'enabled',
                ),
            )
        );
    }

    if ( $command == 'list' )
    {
        $fetchParameters = array();
        if ( $repositoryID )
        {
            $fetchParameters['repository_id'] = $repositoryID;
            $cli->output( "The list of packages in the repository " . $cli->stylize( 'dir', $fetchParameters['repository_id'] ) . ":" );
        }
        else
             $cli->output( "The list of all packages:" );

        $packages = eZPackage::fetchPackages( $fetchParameters );
        if ( count( $packages ) > 0 )
        {
            foreach ( $packages as $package )
            {
                $packageRepInfo = $package->currentRepositoryInformation();
                $cli->output( '[' . $packageRepInfo['id'] . '] ' . $cli->stylize( 'blue', $package->attribute( 'name' ) ) . '  ver.' . $package->attribute( 'version-number' ) . '-' . $package->attribute( 'release-number' ) . ' (' . $cli->stylize( 'emphasize', $package->attribute( 'summary' ) ) . ')' );
            }
        }
        else
            $cli->output( "No packages are available" );
    }
    else if ( $command == 'info' )
    {
        if ( isset( $createdPackages[$commandItem['name']] ) )
            $package =& $createdPackages[$commandItem['name']];
        else
            $package = eZPackage::fetch( $commandItem['name'] );
        if ( $package )
        {
            $showInfo = false;
            $showFiles = false;
            $showDependencies = false;
            if ( isset( $commandItem['info-types'] ) )
            {
                $showInfo = in_array( 'info', $commandItem['info-types'] );
                $showFiles = in_array( 'file', $commandItem['info-types'] );
                $showDependencies = in_array( 'dependency', $commandItem['info-types'] );
            }
            else
                $showInfo = true;
            if ( $showInfo )
            {
                $cli->output( "Name        : " . $cli->stylize( 'blue', $package->attribute( 'name' ) ) . str_repeat( ' ', 30 - strlen( $package->attribute( 'name' ) ) ) . "Vendor  : " . $package->attribute( 'vendor' ) );
                $cli->output( "Version     : " . $package->attribute( 'version-number' ) . str_repeat( ' ', 30 - strlen( $package->attribute( 'version-number' ) ) ) . "Source  : " . $package->attribute( 'source' ) );
                $cli->output( "Release     : " . $package->attribute( 'release-number' ) . str_repeat( ' ', 30 - strlen( $package->attribute( 'release-number' ) ) ) . "Licence : " . $package->attribute( 'licence' ) );
                $cli->output( "Summary     : " . $package->attribute( 'summary' ) . str_repeat( ' ', 30 - strlen( $package->attribute( 'summary' ) ) ) . "State   : " . $package->attribute( 'state' ) );
                $cli->output( "eZ Publish  : " . $package->attribute( 'ezpublish-named-version' ) .
                              " (" . $package->attribute( 'ezpublish-version' ) . ")" );
                $cli->output( "Description : " . $package->attribute( 'description' ) );
            }
            if ( $showDependencies )
            {
                $i = 0;
                foreach ( array( 'provides', 'requires', 'obsoletes', 'conflicts' ) as $dependencySection )
                {
                    $dependencyItems = $package->dependencyItems( $dependencySection, false, false, false );
                    if ( count( $dependencyItems ) == 0 )
                        continue;
                    if ( $i > 0 )
                        $cli->output();
                    $cli->output( $dependencySection . ':' );
                    $dependencyTypes = $package->groupDependencyItemsByType( $dependencyItems );
                    foreach ( $dependencyTypes as $dependencyTypeName => $dependencyItems )
                    {
                        foreach ( $dependencyItems as $dependencyItem )
                        {
                            $dependencyText = $package->createDependencyText( $cli, $dependencyItem, $dependencySection );
                            $cli->output( $dependencyText );
                        }
                    }
                    ++$i;
                }
            }
        }
        else
        {
            $cli->output( "package " . $cli->stylize( 'blue', $commandItem['name'] ) . " is not in the repository" );
            $script->setExitCode( 1 );
            expScriptStatus::instance()->fail();
        }
    }
    else if ( $command == 'add' )
    {
        if ( isset( $createdPackages[$commandItem['name']] ) )
            $package =& $createdPackages[$commandItem['name']];
        else
            $package = eZPackage::fetch( $commandItem['name'] );
        if ( $package )
        {
            $itemType = $commandItem['item'];
            if ( $itemType == 'ezcontentsubtree' )
                $itemType = 'ezcontentobject';
            switch ( $itemType )
            {
                case 'group':
                {
                    $groups = $commandItem['item-parameters'];
                    if ( count( $groups ) > 0 )
                    {
                        foreach ( $groups as $group )
                        {
                            $package->appendGroup( $group );
                            $cli->output( "Added to group $group" );
                        }
                        $package->store();
                    }
                    else
                    {
                        $cli->error( "No groups supplied" );
                    }
                } break;
                default:
                {
                    $handler = $package->packageHandler( $itemType );
                    if ( is_object( $handler ) )
                    {
                        $realItemType = $handler->handlerType();
                        $parameters = $handler->handleAddParameters( $itemType, $package, $cli, $commandItem['item-parameters'] );
                        if ( $parameters )
                        {
                            $handler->add( $itemType, $package, $cli, $parameters );
                            $package->store();
                            if ( ( $itemType == 'ezcontentobject' || $itemType == 'ezcontentsubtree' ) &&
                                 isset( $parameters['node-list'] ) )
                            {
                                expScriptStatus::instance()->newline();
                                foreach ( $parameters['node-list'] as $nodeItem )
                                {
                                    foreach ( $nodeItem['node-id-list'] as $nodeIDItem )
                                    {
                                        $node = isset( $nodeIDItem['node'] ) && is_object( $nodeIDItem['node'] ) ? $nodeIDItem['node'] : eZContentObjectTreeNode::fetch( $nodeIDItem['id'] );
                                        if ( is_object( $node ) )
                                        {
                                            $nodePath = $node->pathWithNames();
                                            $nodePath = $nodePath !== '' ? '/' . $nodePath : '/';
                                            $cli->output( "Added subtree rooted at " . $cli->stylize( 'dir', $nodePath ) .
                                                          " (node ID " . $nodeIDItem['id'] . ") to package " .
                                                          $cli->stylize( 'blue', $package->attribute( 'name' ) ) . "-" .
                                                          $cli->stylize( 'symbol', $package->attribute( 'version-number' ) . "-" . $package->attribute( 'release-number' ) ) );
                                        }
                                    }
                                }
                            }
                        }
                        else
                        {
                            $cli->error( "Failed adding items to package" );
                            $script->setExitCode( 1 );
                            expScriptStatus::instance()->fail();
                            break 2;
                        }
                    }
                    else
                    {
                        $cli->error( "Unknown package item type $itemType" );
                        $script->setExitCode( 1 );
                        expScriptStatus::instance()->fail();
                    }
                } break;
            }
        }
        else
        {
            $cli->output( "package " . $cli->stylize( 'blue', $commandItem['name'] ) . " is not in the repository" );
            $script->setExitCode( 1 );
            expScriptStatus::instance()->fail();
        }
    }
    else if ( $command == 'set' )
    {
        $packageAttributes = array( 'summary',
                                    'description',
                                    'vendor',
                                    'priority',
                                    'type',
                                    'extension',
                                    'source',
                                    'version',
                                    'state' );
        if ( !in_array( $commandItem['attribute'], $packageAttributes ) )
        {
            helpSet();
            $script->setExitCode( 1 );
            expScriptStatus::instance()->fail();
        }
        else
        {
            if ( isset( $createdPackages[$commandItem['name']] ) )
                $package =& $createdPackages[$commandItem['name']];
            else
                $package = eZPackage::fetch( $commandItem['name'] );
            if ( $package )
            {
                switch ( $commandItem['attribute'] )
                {
                    case 'summary':
                    case 'description':
                    case 'vendor':
                    case 'extension':
                    case 'source':
                    case 'type':
                    case 'priority':
                    case 'state':
                    {
                        $package->setAttribute( $commandItem['attribute'], $commandItem['attribute-value'] );
                        $cli->output( "Attribute " . $cli->style( 'symbol' ) . $commandItem['attribute'] . $cli->style( 'symbol-end' ) .
                                      " was set to " . $cli->style( 'symbol' ) . $commandItem['attribute-value'] . $cli->style( 'symbol-end' ) );
                    } break;
                }
                $package->store();
            }
            else
            {
                $cli->output( "package " . $cli->stylize( 'blue', $commandItem['name'] ) . " is not in repository" );
                $script->setExitCode( 1 );
                expScriptStatus::instance()->fail();
            }
        }
    }
    else if ( $command == 'import' )
    {
        $packageFile = $commandItem['name'];

        if ( $packageFile && file_exists( $packageFile ) )
        {
            $packageFile = realpath( $packageFile );

            $package = eZPackage::import( $packageFile, $packageName, true, $repositoryID );

            if ( $package instanceof eZPackage )
            {
                $cli->output( "Package " . $cli->stylize( 'blue', $packageName ) . " sucessfully imported" );
            }
            else if ( $package == eZPackage::STATUS_ALREADY_EXISTS )
            {
                $cli->error( "Could not import package " . $cli->stylize( 'blue', $packageName ) . ", it already exists" );
                $script->setExitCode( 1 );
                expScriptStatus::instance()->fail();
            }
            else if ( $package == eZPackage::STATUS_INVALID_NAME )
            {
                $cli->error( "Could not import package " . $cli->stylize( 'blue', $packageName ) . ", its name is invalid" );
                $script->setExitCode( 1 );
                expScriptStatus::instance()->fail();
            }
            else
            {
                $cli->error( "Could not import package " . $packageFile . ", invalid package file" );
                $script->setExitCode( 1 );
                expScriptStatus::instance()->fail();
            }
        }
        else
        {
            $cli->error( "Could not import package " . $packageFile . ", file was not found" );
            $script->setExitCode( 1 );
            expScriptStatus::instance()->fail();
        }
    }
    else if ( $command == 'install' )
    {
        $package = eZPackage::fetch( $commandItem['name'] );
        if ( $package )
        {
            $user = eZUser::currentUser();
            $userID = is_object( $user ) ? $user->attribute( 'contentobject_id' ) : 0;
            $topNodeID = ( $commandItem['destination-node-id'] !== false && (int)$commandItem['destination-node-id'] > 0 ) ? (int)$commandItem['destination-node-id'] : 2;
            $installParameters = array( 'site_access_map' => array( '*' => $siteaccess ),
                                        'top_nodes_map' => array( '*' => $topNodeID ),
                                        'design_map' => array( '*' => $siteaccess ),
                                        'restore_dates' => true,
                                        'user_id' => $userID,
                                        'non-interactive' => true,
                                        'language_map' => $package->defaultLanguageMap() );
            $result = $package->install( $installParameters );
            if ( $result )
            {
                $cli->output( "Package " . $cli->stylize( 'blue', $package->attribute( 'name' ) ) . " sucessfully installed" );
            }
            else
            {
                $cli->error( "Failed to install package " . $cli->stylize( 'blue', $package->attribute( 'name' ) ) );
                if ( isset( $installParameters['error'] ) && is_array( $installParameters['error'] ) && count( $installParameters['error'] ) )
                {
                    if ( isset( $installParameters['error']['description'] ) && $installParameters['error']['description'] )
                    {
                        $cli->error( "Details: " . $installParameters['error']['description'] );
                    }
                    if ( isset( $installParameters['error']['element_id'] ) && $installParameters['error']['element_id'] )
                    {
                        $cli->error( "Element ID: " . $installParameters['error']['element_id'] );
                    }
                    if ( !isset( $installParameters['error']['description'] ) && isset( $installParameters['error']['error_code'] ) )
                    {
                        $cli->error( "Error code: " . $installParameters['error']['error_code'] );
                    }
                }
                else
                {
                    $cli->notice( "Run with --debug to see full eZDebug output for the failing install item." );
                }
                $script->setExitCode( 1 );
                expScriptStatus::instance()->fail();
            }
        }
        else
        {
            $cli->error( "Could not open package " . $cli->stylize( 'blue', $commandItem['name'] ) );
            $script->setExitCode( 1 );
            expScriptStatus::instance()->fail();
        }
    }
    else if ( $command == 'export' )
    {
        if ( isset( $createdPackages[$commandItem['name']] ) )
            $package =& $createdPackages[$commandItem['name']];
        else
            $package = eZPackage::fetch( $commandItem['name'] );
        if ( $package )
        {
            if ( isset( $commandItem['export-directory'] ) )
            {
                $exportDirectory = $commandItem['export-directory'];
                if ( !file_exists( $exportDirectory ) )
                {
                    $cli->warning( "The directory " . $cli->style( 'dir' ) . $exportDirectory . $cli->style( 'dir-end' ) . " does not exist, cannot export package" );
                    $script->setExitCode( 1 );
                    expScriptStatus::instance()->fail();
                }
                else
                {
                    $exportPath = $package->exportToArchive( $exportDirectory . eZSys::fileSeparator() . $package->exportName() );
                    if ( $exportPath )
                    {
                        $cli->output( "Package " . $cli->stylize( 'blue', $package->attribute( 'name' ) ) . " exported to directory " . $cli->stylize( 'dir', $exportDirectory ) );
                    }
                    else
                    {
                        $cli->error( "Failed to export package " . $cli->stylize( 'blue', $package->attribute( 'name' ) ) );
                        $script->setExitCode( 1 );
                        expScriptStatus::instance()->fail();
                    }
                }
            }
            else
            {
                $exportPath = $package->exportToArchive( $package->exportName() );
                if ( $exportPath )
                {
                    $cli->output( "Package " . $cli->stylize( 'blue', $package->attribute( 'name' ) ) . " exported to file " . $cli->stylize( 'file', $exportPath ) );
                }
                else
                {
                    $cli->error( "Failed to export package " . $cli->stylize( 'blue', $package->attribute( 'name' ) ) );
                    $script->setExitCode( 1 );
                    expScriptStatus::instance()->fail();
                }
            }
        }
        else
        {
            $cli->error( "Could not locate package " . $cli->stylize( 'blue', $commandItem['name'] ) );
            $script->setExitCode( 1 );
            expScriptStatus::instance()->fail();
        }
    }
    else if ( $command == 'create' )
    {
        if ( $alreadyCreated )
            $cli->output();
        $package = eZPackage::create( $commandItem['name'],
                                      array( 'summary' => $commandItem['summary'] ),
                                      false, $repositoryID );

        $user = eZUser::currentUser();
        $userObject = is_object( $user ) ? $user->attribute( 'contentobject' ) : false;

        $commandItem['licence'] = 'GPL';
        if ( !in_array( $commandItem['installtype'], array( 'install', 'import' ) ) )
            $commandItem['installtype'] = 'install';
        if ( !$commandItem['version'] )
            $commandItem['version'] = '1.0';

        $package->setRelease( $commandItem['version'], '1', false,
                              $commandItem['licence'], 'alpha' );
        $package->setAttribute( 'install_type', $commandItem['installtype'] );
        if ( $userObject )
            $package->appendMaintainer( $userObject->attribute( 'name' ), $user->attribute( 'email' ), 'lead' );
        eZPackageCreationHandler::appendLicence( $package );
        if ( $userObject )
            $package->appendChange( $userObject->attribute( 'name' ), $user->attribute( 'email' ), 'Creation of package' );

        $package->store();
        $text = "Created package " . $cli->stylize( 'blue', $commandItem['name'] ) . "-" . $cli->stylize( 'symbol', $commandItem['version'] );
        if ( $commandItem['summary'] )
            $text .= " " . $cli->stylize( 'archive', $commandItem['summary'] );
        $cli->output( $text );
        $alreadyCreated = true;
        $createdPackages[$commandItem['name']] =& $package;
    }
    else if ( $command == 'delete' )
    {
        $package = eZPackage::fetch( $commandItem['name'] );
        if ( $package )
        {
            $package->remove();
            $cli->output( "Package " . $cli->stylize( 'blue', $commandItem['name'] ) . " deleted." );
        }
        else
        {
            $cli->error( "Could not open package " . $cli->stylize( 'blue', $commandItem['name'] ) );
            $script->setExitCode( 1 );
            expScriptStatus::instance()->fail();
        }
        expScriptStatus::instance()->end();
    }
}

$cli->output();

expScriptStatus::instance()->end();

$script->shutdown();

?>
