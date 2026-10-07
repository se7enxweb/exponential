<?php
/**
 * The code of bin/php/ezcsvimport.php, moved into a class (#207 stage 1). The file bin/php/ezcsvimport.php is one call to it.
 * @description Import content objects of a class below a node from a CSV file (--class, --creator)
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/ezcsvimport.php:
 *
 *
 * File containing the ezcsvimport.php bin script
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\Command\Kernel
{

class Ezcsvimport extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'attribute', 'attributes', 'class', 'cli', 'contentObject', 'contentObjectID', 'createClass', 'creator', 'csvLineLength', 'dataString', 'datatypeString', 'fp', 'inputFileName', 'key', 'node', 'nodeAssignment', 'nodeID', 'objectData', 'operationResult', 'options', 'parentObject', 'script', 'storageDir', 'version' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = $this->cli();
        $script = $this->script( array( 'description' => ( "Import content objects of a class below a node from a CSV file\n\nExample: ezcsvimport.php --class=article 2 articles.csv\n" .
                                                                "\n" .
                                                                "\n" .
                                                                "\n" .
                                                                "\n" .
                                                                "" ),
                                             'use-session' => false,
                                             'use-modules' => true,
                                             'use-extensions' => true ) );

        $options = $this->startup( "[class:][creator:][storage-dir:]",
                                        "[node][file]",
                                        array( 'node' => 'parent node_id to upload object under',
                                               'file' => 'file to read CSV data from',
                                               'class' => 'class identifier to create objects',
                                               'creator' => 'user id of imported objects creator',
                                               'storage-dir' => 'path to directory which will be added to the path of CSV elements' ),
                                        false,
                                        array( 'user' => true ));

        if ( count( $options['arguments'] ) < 2 )
        {
            $cli->error( "Need a parent node to place object under and file to read data from" );
            $script->shutdown( 1 );
        }

        $nodeID = $options['arguments'][0];
        $inputFileName = $options['arguments'][1];
        $createClass = $options['class'];
        $creator = $options['creator'];
        if ( $options['storage-dir'] )
        {
            $storageDir = $options['storage-dir'];
        }
        else
        {
            $storageDir = '';
        }

        $csvLineLength = 100000;

        $cli->output( "Going to import objects of class $createClass under node $nodeID from file $inputFileName\n" );

        $node = \eZContentObjectTreeNode::fetch( $nodeID );
        if ( !$node )
        {
            $cli->error( "No such node to import objects" );
            $script->shutdown( 1 );
        }
        $parentObject = $node->attribute( 'object' );

        $class = \eZContentClass::fetchByIdentifier( $createClass );

        if ( !$class )
        {
            $cli->error( "No class with identifier $createClass" );
            $script->shutdown( 1 );
        }

        $fp = @fopen( $inputFileName, "r" );
        if ( !$fp )
        {
            $cli->error( "Can not open file $inputFileName for reading" );
            $script->shutdown( 1 );
        }

        $auditCreated = 0;
        while ( $objectData = fgetcsv( $fp, $csvLineLength , ';', '"', '\\' ) )
        {

            $contentObject = $class->instantiate( $creator );
            $contentObject->store();

            $nodeAssignment = \eZNodeAssignment::create( array(
                                                             'contentobject_id' => $contentObject->attribute( 'id' ),
                                                             'contentobject_version' => $contentObject->attribute( 'current_version' ),
                                                             'parent_node' => $nodeID,
                                                             'is_main' => 1
                                                             )
                                                         );
            $nodeAssignment->store();

            $version = $contentObject->version( 1 );
            $version->setAttribute( 'modified', \eZDateTime::currentTimeStamp() );
            $version->setAttribute( 'status', \eZContentObjectVersion::STATUS_DRAFT );
            $version->store();

            $contentObjectID = $contentObject->attribute( 'id' );

            $attributes = $contentObject->attribute( 'contentobject_attributes' );

            foreach ( $attributes as $key => $attribute )
            {
                $dataString = $objectData[$key];
                switch ( $datatypeString = $attribute->attribute( 'data_type_string' ) )
                {
                    case 'ezimage':
                    case 'ezbinaryfile':
                    case 'ezmedia':
                    {
                        $dataString = trim( $dataString );
                        $dataString = !empty( $dataString ) ? \eZDir::path( array( $storageDir, $dataString ) ) : '';
                        break;
                    }
                    default:
                }
                $attribute->fromString( $dataString );
                $attribute->store();
            }

            $operationResult = \eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $contentObjectID,
                                                                                         'version' => 1 ) );
            $auditCreated++;
        }

        fclose( $fp );

        // Audit (doc/bc/6.0/audit.md, data.import.csv)
        if ( class_exists( 'expAuditHook' ) )
            \expAuditHook::emit( 'data.import.csv', array( 'object' => array( 'type' => 'file', 'id' => basename( (string)$inputFileName ),
                                                                              'sha256' => is_file( $inputFileName ) ? hash_file( 'sha256', $inputFileName ) : null ),
                'target' => \expAuditHook::node( $node ), 'verb' => 'import',
                'after' => array( 'class' => (string)$createClass, 'created' => $auditCreated ) ) );

        $script->shutdown();
    }
}

}
