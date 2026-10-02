<?php
/**
 * The code of runcronjobs.php, moved into a class (#207 stage 1). The file runcronjobs.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of ./runcronjobs.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{


function help()
{
    $argv = $_SERVER['argv'];
    $cli = eZCLI::instance();
    $cli->output( "Usage: " . $argv[0] . " [OPTION]... [PART]\n" .
                  "Executes Exponential cronjobs.\n" .
                  "\n" .
                  "General options:\n" .
                  "  -h,--help          display this help and exit \n" .
                  "  -q,--quiet         do not give any output except when errors occur\n" .
                  "  -s,--siteaccess    selected siteaccess for operations, if not specified default siteaccess is used\n" .
                  "  -d,--debug         display debug output at end of execution\n" .
                  "  -c,--colors        display output using ANSI colors\n" .
                  "  --sql              display sql queries (must be used in conjunction with debug option)\n" .
                  "  --logfiles         create log files\n" .
                  "  --no-logfiles      do not create log files (default)\n" .
                  "  --list             list all cronjobs parts and the scripts contained by each one\n" .
                  "  --script=<file>    run one named script instead of a whole part, for instance\n" .
                  "                     --script=notification.php. The file is looked for in the\n" .
                  "                     directories cronjob.ini names; only its file name is used\n" .
                  "  --no-colors        do not use ANSI coloring (default)\n" );
}

function changeSiteAccessSetting( &$siteaccess, $optionData )
{
    $cli = eZCLI::instance();
    if ( file_exists( 'settings/siteaccess/' . $optionData ) )
    {
        $siteaccess = $optionData;
        return "Using siteaccess $siteaccess for cronjob";
    }
    elseif ( isExtensionSiteaccess( $optionData ) )
    {
        $siteaccess = $optionData;
        eZExtension::prependExtensionSiteAccesses( $siteaccess );
        return "Using extension siteaccess $siteaccess for cronjob";
    }
    else
    {
        return "Siteaccess $optionData does not exist, using default siteaccess";
    }
}

/*
    Look in the ActiveExtensions for $siteaccessName
    We only need to look in ActiveExtensions and not ActiveAccessExtensions
    Return true if siteaccessName exists in an extension, false if not.
*/
function isExtensionSiteaccess( $siteaccessName )
{
    $activeExtensions = eZINI::instance()->variable( 'ExtensionSettings', 'ActiveExtensions' );

    foreach ( $activeExtensions as $extensionName )
    {
        $extensionPath = eZExtension::extensionPath( $extensionName );
        if ( $extensionPath === false )
            continue;

        $possibleExtensionPath = $extensionPath . '/settings/siteaccess/' . $siteaccessName;
        if ( file_exists( $possibleExtensionPath ) )
            return true;
    }
    return false;
}
}

namespace Exponential\Command\Kernel
{

class Runcronjobs extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'allowedDebugLevels', 'arg', 'argv', 'block', 'blockValues', 'cli', 'count', 'cronPart', 'cronScript', 'db', 'debugOutput', 'elapsedTime', 'endTime', 'extensionDirectories', 'fileExists', 'fileName', 'filePath', 'flag', 'i', 'index', 'ini', 'inlineData', 'isQuiet', 'level', 'levels', 'listCronjobs', 'longOptionsWithData', 'optionData', 'optionsWithData', 'readOptions', 'script', 'scriptDirectories', 'scriptDirectory', 'scriptFile', 'scriptGroup', 'scripts', 'showSQL', 'siteAccessChangeMessage', 'siteAccessSet', 'siteaccess', 'startTime', 'transactionCounterCheck', 'useColors', 'useDebugAccumulators', 'useDebugTimingpoints', 'useIncludeFiles', 'useLogFiles', 'webOutput' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        require_once( 'kernel/common/i18n.php' );

        \eZContentLanguage::setCronjobMode();

        $cli = \eZCLI::instance();
        $script = \eZScript::instance( array( 'debug-message' => '',
                                              'use-session' => true,
                                              'use-modules' => true,
                                              'use-extensions' => true ) );

        $script->startup();

        $webOutput = $cli->isWebOutput();

        $siteaccess = false;
        $debugOutput = false;
        $allowedDebugLevels = false;
        $useDebugAccumulators = false;
        $useDebugTimingpoints = false;
        $useIncludeFiles = false;
        $useColors = false;
        $isQuiet = false;
        $useLogFiles = false;
        $showSQL = false;
        $cronPart = false;
        $listCronjobs = false;
        // One named script instead of a whole part. Everything else about the run is
        // the same: the same siteaccess handling, the same per script mutex, the same
        // output, so a single script cannot collide with the part it belongs to.
        $cronScript = false;

        $optionsWithData = array( 's' );
        $longOptionsWithData = array( 'siteaccess', 'script' );

        $readOptions = true;
        $siteAccessSet = false;

        for ( $i = 1, $count = count( $argv ); $i < $count; ++$i )
        {
            $arg = $argv[$i];
            if ( $readOptions and
                 strlen( $arg ) > 0 and
                 $arg[0] == '-' )
            {
                if ( strlen( $arg ) > 1 and
                     $arg[1] == '-' )
                {
                    $flag = substr( $arg, 2 );
                    // Both --option value and --option=value, so a caller can write
                    // whichever reads better and neither is a surprise.
                    $inlineData = false;
                    if ( strpos( $flag, '=' ) !== false )
                    {
                        list( $flag, $inlineData ) = explode( '=', $flag, 2 );
                    }
                    if ( in_array( $flag, $longOptionsWithData ) )
                    {
                        if ( $inlineData !== false )
                        {
                            $optionData = $inlineData;
                        }
                        else
                        {
                            $optionData = isset( $argv[$i+1] ) ? $argv[$i+1] : false;
                            ++$i;
                        }
                    }
                    if ( $flag == 'help' )
                    {
                        help();
                        exit();
                    }
                    else if ( $flag == 'siteaccess' )
                    {
                        $siteAccessSet = $optionData;
                    }
                    else if ( $flag == 'script' )
                    {
                        $cronScript = $optionData;
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
                    else if ( $flag == 'list' )
                    {
                        $listCronjobs = true;
                    }
                    else if ( $flag == 'sql' )
                    {
                        $showSQL = true;
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
                    else if ( $flag == 'd' )
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
                                    $level = \eZDebug::LEVEL_ERROR;
                                else if ( $level == 'warning' )
                                    $level = \eZDebug::LEVEL_WARNING;
                                else if ( $level == 'debug' )
                                    $level = \eZDebug::LEVEL_DEBUG;
                                else if ( $level == 'notice' )
                                    $level = \eZDebug::LEVEL_NOTICE;
                                else if ( $level == 'timing' )
                                    $level = \eZDebug::LEVEL_TIMING_POINT;
                                $allowedDebugLevels[] = $level;
                            }
                        }
                    }
                    else if ( $flag == 's' )
                    {
                        $siteAccessSet = $optionData;
                    }
                }
            }
            else
            {
                if ( $cronPart === false )
                {
                    $readOptions = false;
                    $cronPart = $arg;
                }
            }
        }
        $script->setUseDebugOutput( $debugOutput );
        $script->setAllowedDebugLevels( $allowedDebugLevels );
        $script->setUseDebugAccumulators( $useDebugAccumulators );
        $script->setUseDebugTimingPoints( $useDebugTimingpoints );
        $script->setUseIncludeFiles( $useIncludeFiles );
        $script->setIsQuiet( $isQuiet );

        $siteAccessChangeMessage = false;

        if ( $siteAccessSet )
        {
            $siteAccessChangeMessage = changeSiteAccessSetting( $siteaccess, $siteAccessSet );
        }

        if ( $webOutput )
            $useColors = true;

        $cli->setUseStyles( $useColors );
        $script->setDebugMessage( "\n\n" . str_repeat( '#', 36 ) . $cli->style( 'emphasize' ) . " DEBUG " . $cli->style( 'emphasize-end' )  . str_repeat( '#', 36 ) . "\n" );

        $script->setUseSiteAccess( $siteaccess );

        $script->initialize();
        if ( !$script->isInitialized() )
        {
            $cli->error( 'Error initializing script: ' . $script->initializationError() . '.' );
            $script->shutdown( 0 );
        }

        if ( $siteAccessChangeMessage )
        {
            $cli->output( $siteAccessChangeMessage );
        }
        else
        {
            $cli->output( "Using siteaccess $siteaccess for cronjob" );
        }

        if ( $cronPart )
        {
            $cli->output( "Running cronjob part '$cronPart'" );
        }

        $db = \eZDB::instance();
        $db->setIsSQLOutputEnabled( $showSQL );

        $ini = \eZINI::instance( 'cronjob.ini' );
        $scriptDirectories = $ini->variable( 'CronjobSettings', 'ScriptDirectories' );

        /* Include extension directories */
        $extensionDirectories = $ini->variable( 'CronjobSettings', 'ExtensionDirectories' );
        $scriptDirectories = array_merge( $scriptDirectories, \eZExtension::expandedPathList( $extensionDirectories, 'cronjobs' ) );

        if ( $listCronjobs )
        {
            foreach ( $ini->groups() as $block => $blockValues )
            {
                if ( strpos( $block, 'Cronjob' ) !== false )
                {
                    $cli->output( $cli->endLineString() );
                    $cli->output( "{$block}:" );

                    foreach ( $blockValues['Scripts'] as $fileName )
                    {
                        $fileExists = false;
                        foreach ( $scriptDirectories as $scriptDirectory )
                        {
                            $filePath = $scriptDirectory . "/" . $fileName;
                            if ( file_exists( $filePath ) )
                            {
                                $fileExists = true;
                                $cli->output( "{$cli->goToColumn( 4 )} {$filePath}" );
                            }
                        }
                        if ( !$fileExists )
                            $cli->output( "{$cli->goToColumn( 4 )} Error: No {$fileName} file in any of configured directories!" );
                    }
                }   
            }
            $script->shutdown( 0 );
        }

        if ( $cronScript !== false && $cronScript !== '' )
        {
            // Only the file name is taken, so a path cannot be used to reach a script
            // outside the directories cronjob.ini names. The loop below looks for it
            // in each of them and skips it if it is in none.
            $scripts = array( basename( $cronScript ) );
            if ( !$isQuiet )
                $cli->output( "Running cronjob script '" . $scripts[0] . "'" );
        }
        else
        {
            $scriptGroup = 'CronjobSettings';
            if ( $cronPart !== false )
                $scriptGroup = "CronjobPart-$cronPart";
            $scripts = $ini->variable( $scriptGroup, 'Scripts' );
        }

        if ( !is_array( $scripts ) or empty( $scripts ) )
        {
            $cli->notice( 'Notice: No scripts found for execution.' );
            $script->shutdown( 0 );
        }

        $index = 0;

        foreach ( $scripts as $cronScript )
        {
            foreach ( $scriptDirectories as $scriptDirectory )
            {
                $scriptFile = $scriptDirectory . '/' . $cronScript;
                if ( file_exists( $scriptFile ) )
                    break;
            }
            if ( file_exists( $scriptFile ) )
            {
                if ( $index > 0 )
                {
                    $cli->output();
                }
                if ( !$isQuiet )
                {
                    $startTime = new \eZDateTime();
                    $cli->output( 'Running ' . $cli->stylize( 'emphasize', $scriptFile ) . ' at: ' . $startTime->toString( true ) );
                }

                \eZDebug::addTimingPoint( "Script $scriptFile starting" );
                \eZRunCronjobs::runScript( $cli, $scriptFile );
                \eZDebug::addTimingPoint( "Script $scriptFile done" );
                ++$index;
                // The transaction check
                $transactionCounterCheck = \eZDB::checkTransactionCounter();
                if ( isset( $transactionCounterCheck['error'] ) )
                    $cli->error( $transactionCounterCheck['error'] );

                if ( !$isQuiet )
                {
                    $endTime = new \eZDateTime();
                    $cli->output( 'Completing ' . $cli->stylize( 'emphasize', $scriptFile ) . ' at: ' . $endTime->toString( true ) );
                    $elapsedTime = new \eZTime( $endTime->timeStamp() - $startTime->timeStamp() );
                    $cli->output( 'Elapsed time: ' . sprintf( '%02d:%02d:%02d', $elapsedTime->hour(), $elapsedTime->minute(), $elapsedTime->second() ) );
                }
            }
        }

        $script->shutdown();
    }
}

}
