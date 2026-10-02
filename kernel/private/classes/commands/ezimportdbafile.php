<?php
/**
 * The code of bin/php/ezimportdbafile.php, moved into a class (#207 stage 1). The file bin/php/ezimportdbafile.php is one call to it.
 * @description Import the dba data of a datatype into the database (--datatype=ezisbn)
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/ezimportdbafile.php:
 *
 *
 * File containing the ezimportdbafile.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\Command\Kernel
{

class Ezimportdbafile extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'activeExtension', 'activeExtensions', 'allowedDatatypes', 'cli', 'dataType', 'dataTypeName', 'errorString', 'extensionPath', 'fileName', 'options', 'registeredDataTypes', 'script' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = \eZCLI::instance();
        $script = \eZScript::instance( array( 'description' => ( "Exponential datatype sql update\n\n" .
                                                                "Script can be run as:\n" .
                                                                "bin/php/ezimportdbafile.php --datatype=\n\n" .
                                                                "Example: bin/php/ezimportdbafile.php --datatype=ezisbn" ),
                                             'use-session' => false,
                                             'use-modules' => true,
                                             'use-extensions' => true ) );

        $script->startup();

        $options = $script->getOptions( "[datatype:]", "",
                                        array( 'datatype' => "The name of the datatype where the database should be updated." ) );
        $script->initialize();
        $dataTypeName = $options['datatype'];

        if ( $dataTypeName === null )
        {
            $cli->output( "Error: The option --datatype is required. Add --help for more information." );
        }

        $allowedDatatypes = \eZDataType::allowedTypes();
        if ( $dataTypeName !== null and
             in_array( $dataTypeName, $allowedDatatypes ) )
        {
            // Inserting data from the dba-data files of the datatypes
            \eZDataType::loadAndRegisterAllTypes();
            $registeredDataTypes = \eZDataType::registeredDataTypes();

            if ( isset( $registeredDataTypes[$dataTypeName] ) )
            {
                $dataType = $registeredDataTypes[$dataTypeName];
                if ( $dataType->importDBDataFromDBAFile() )
                {
                    $cli->output( "The database is updated for the datatype: " .
                                  $cli->style( 'emphasize' ) . $dataType->DataTypeString . $cli->style( 'emphasize-end' ) . "\n" .
                                  'dba-data is imported from the file: ' .
                                  $cli->style( 'emphasize' ) . $dataType->getDBAFilePath() .  $cli->style( 'emphasize-end' ) );
                }
                else
                {
                    $activeExtensions = \eZExtension::activeExtensions();
                    $errorString = "Failed importing datatype related data into database: \n" .
                                   '  datatype - ' . $dataType->DataTypeString . ", \n" .
                                   '  checked dba-data file - ' . $dataType->getDBAFilePath( false );
                    foreach ( $activeExtensions as $activeExtension )
                    {
                        $extensionPath = \eZExtension::extensionPath( $activeExtension );
                        if ( $extensionPath === false )
                            continue;

                        $fileName = $extensionPath .
                                    '/datatypes/' . $dataType->DataTypeString . '/' . $dataType->getDBAFileName();
                        $errorString .= "\n" . str_repeat( ' ', 23 ) . ' - ' . $fileName;
                        if ( file_exists( $fileName ) )
                        {
                            $errorString .= " (found, but not successfully imported)";
                        }
                    }

                    $cli->error( $errorString );
                }
            }
            else
            {
                $cli->error( "Error: The datatype " . $dataTypeName . " does not exist." );
            }
        }
        else
        {
            $cli->error( "Error: The datatype " . $dataTypeName . " is not registered." );
        }
        $script->shutdown();
    }
}

}
