#!/usr/bin/env php
<?php

require "autoload.php";

$cli = eZCLI::instance();

$script = eZScript::instance(
    array(
        'description' => 'Update the datatype ezenhancedselection to the new version',
        'use-session' => false,
        'use-modules' => false,
        'use-extensions' => true
    )
);

$script->startup();
$script->initialize();

$cli->output( 'Updating ezenhancedselection to sckenhancedselection...' );
$cli->output();

// Find all class attributes based on ezenhancedselection
$db = eZDB::instance();

$IDs = $db->arrayQuery(
    "SELECT id
    FROM ezcontentclass_attribute
    WHERE version = 0 AND data_type_string = 'ezenhancedselection'"
);

$cli->output( $cli->stylize( 'bold', 'Updating class attributes' ) );

if ( is_array( $IDs ) and count( $IDs ) > 0 )
{
    foreach ( $IDs as $id )
    {
        $cli->output( 'Updating class attribute: ID - ' . $id['id'] );
        $classAttribute = eZContentClassAttribute::fetch( $id['id'] );
        $content = $classAttribute->content();

        $classAttribute->setAttribute( 'data_type_string', 'sckenhancedselection' );
        $classAttribute->DataTypeString = 'sckenhancedselection';
        $classAttribute->setContent( $content );
        $classAttribute->store();

        $classAttribute->setAttribute( 'data_int1', 0 );
        $classAttribute->setAttribute( 'data_text1', '' );
        $classAttribute->store();

        unset( $classAttribute );
    }
}
else
{
    $cli->output( 'No class attributes to update!' );
}

$IDs = $db->arrayQuery(
    "SELECT id, version
    FROM ezcontentobject_attribute
    WHERE data_type_string = 'ezenhancedselection'"
);

$cli->output();
$cli->output( $cli->stylize( 'bold', 'Updating object attributes' ) );

if ( is_array( $IDs ) and count( $IDs ) > 0 )
{
    foreach ( $IDs as $id )
    {
        $cli->output( 'Updating object attribute: id - ' . $id['id'] . ' & version - ' . $id['version'] );
        $objectAttribute = eZContentObjectAttribute::fetch( $id['id'], $id['version'] );

        $textString = $objectAttribute->attribute( 'data_text' );
        $textArray = explode( '***', $textString );

        $objectAttribute->setAttribute( 'data_type_string', 'sckenhancedselection' );
        $objectAttribute->DataTypeString = 'sckenhancedselection';
        $objectAttribute->setAttribute( 'data_text', serialize( $textArray ) );
        $objectAttribute->store();

        $objectAttribute->updateSortKey();

        $object = $objectAttribute->attribute( 'object' );
        $class = $object->attribute( 'content_class' );

        // Reset the name
        $object->setName( $class->contentObjectName( $object ) );

        // Update the nodes
        $nodes = $object->attribute( 'assigned_nodes' );

        foreach ( $nodes as $node )
        {
            eZContentOperationCollection::publishNode(
                $node->attribute( 'parent_node_id' ),
                $object->attribute( 'id' ),
                $object->attribute( 'current_version' ),
                $object->attribute( 'main_node_id' )
            );
        }

        eZSearch::removeObjectById( $object->attribute( 'id' ) );
        eZSearch::addObject( $object );

        unset( $objectAttribute, $object, $class, $node );
    }
}
else
{
    $cli->output( 'No object attributes to update!' );
}

$cli->output();
$cli->output( 'Done.' );

$script->shutdown();
