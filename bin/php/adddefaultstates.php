#!/usr/bin/env php
<?php
/**
 * File containing the adddefaultstates.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

require_once 'autoload.php';

$cli = eZCLI::instance();

$scriptSettings = array();
$scriptSettings['description'] = 'Adds default states to content objects';
$scriptSettings['use-session'] = true;
$scriptSettings['use-modules'] = false;
$scriptSettings['use-extensions'] = true;

$script = eZScript::instance( $scriptSettings );
$script->startup();

$config = '';
$argumentConfig = '';
$optionHelp = false;
$arguments = false;
$useStandardOptions = true;

$options = $script->getOptions( $config, $argumentConfig, $optionHelp, $arguments, $useStandardOptions );
$script->initialize();

$cli->output( 'Adding default states to content objects...' );

$db = eZDB::instance();

$db->begin();

if ( $db->databaseName() === 'mongo' )
{
    // Fetch all state groups
    $groups = $db->aggregate( 'ezcobj_state_group', [
        [ '$project' => [ '_id' => 0, 'id' => 1 ] ],
    ] );

    foreach ( $groups as $group )
    {
        $groupID = (int)$group['id'];

        // Find the default state for this group (priority = 0)
        $defaultStateRows = $db->aggregate( 'ezcobj_state', [
            [ '$match'   => [ 'group_id' => $groupID, 'priority' => 0 ] ],
            [ '$project' => [ '_id' => 0, 'id' => 1 ] ],
        ] );
        if ( empty( $defaultStateRows ) ) continue;
        $defaultStateID = (int)$defaultStateRows[0]['id'];

        // Find all content object IDs that already have a state link for this group
        $linkedObjectIDs = array_map( 'intval', array_column(
            $db->aggregate( 'ezcobj_state_link', [
                [ '$lookup' => [
                    'from'         => 'ezcobj_state',
                    'localField'   => 'contentobject_state_id',
                    'foreignField' => 'id',
                    'as'           => '_state',
                ] ],
                [ '$match'   => [ '_state.group_id' => $groupID ] ],
                [ '$project' => [ '_id' => 0, 'contentobject_id' => 1 ] ],
            ] ),
            'contentobject_id'
        ) );

        // Fetch all content object IDs
        $allObjectIDs = array_map( 'intval', array_column(
            $db->aggregate( 'ezcontentobject', [
                [ '$project' => [ '_id' => 0, 'id' => 1 ] ],
            ] ),
            'id'
        ) );

        // Insert a default state link for each object that is missing one
        $missingObjectIDs = array_diff( $allObjectIDs, $linkedObjectIDs );
        foreach ( $missingObjectIDs as $objID )
        {
            $db->insert( 'ezcobj_state_link', [
                'contentobject_id'       => (int)$objID,
                'contentobject_state_id' => $defaultStateID,
            ] );
        }
    }
}
else
{
    $db->query(
"INSERT INTO ezcobj_state_link
SELECT o.id contentobject_id, s0.id contentobject_state_id
FROM ezcontentobject o, ezcobj_state s0, ezcobj_state_group g
WHERE g.id=s0.group_id
 AND s0.priority=0
 AND NOT EXISTS (
  SELECT contentobject_id
  FROM ezcobj_state_link l, ezcobj_state s
  WHERE l.contentobject_state_id=s.id
    AND l.contentobject_id=o.id
    AND s.group_id=g.id
)" );
}

$db->commit();

$cli->output( 'Finished!' );

$script->shutdown( 0 );

?>
