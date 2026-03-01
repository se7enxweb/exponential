<?php
//
// Swark - extension for eZ Publish
// Author: Jan Kudlicka <jk@seeds.no>
// Copyright (C) 2008 Seeds Consulting AS, http://www.seeds.no/
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of version 2.0 of the GNU General
// Public License as published by the Free Software Foundation.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
// MA 02110-1301, USA.
//

class AutoPriorityType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_ID = 'autopriority';

    function __construct()
    {
        parent::__construct( AutoPriorityType::WORKFLOW_TYPE_ID, 'Automatically assign priority' );
    }

    function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );

        $objectID = $parameters['object_id'];
        $version = $parameters['version'];

        if ( $version == 1 )
        {
            $object = eZContentObject::fetch( $objectID );
            $nodeID = $object->attribute( 'main_node_id' );
            $parentNodeID = $object->attribute( 'main_parent_node_id' );

            $ini = eZINI::instance( 'swark.ini' );
            $increment = (int) $ini->variable( 'AutoPriority', 'PriorityIncrement' );
            if ( !$increment )
            {
                $increment = 10;
            }

            $db = eZDB::instance();
            $db->begin();
            
            $maxPriorityRow = $db->arrayQuery( "SELECT max(priority) AS max_priority
                                                FROM ezcontentobject_tree
                                                WHERE parent_node_id=$parentNodeID" );
            $maxPriority = (int) $maxPriorityRow[0]['max_priority'];
            $priority = $maxPriority + $increment;

            $db->query( "UPDATE ezcontentobject_tree SET priority=$priority WHERE node_id=$nodeID" );

            eZContentCache::cleanup( array( $parentNodeID ) );

            $db->commit();
        }

        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( AutoPriorityType::WORKFLOW_TYPE_ID, 'autoprioritytype' );

?>