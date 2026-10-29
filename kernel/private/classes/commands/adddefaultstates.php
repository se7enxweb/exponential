<?php
/**
 * The code of bin/php/adddefaultstates.php, moved into a class (#207 stage 1). The file bin/php/adddefaultstates.php is one call to it.
 * @description Give every content object the default state of each state group it has no state of (dry run unless --apply)
 * Guide: doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/adddefaultstates.php:
 *
 *
 * File containing the adddefaultstates.php script.
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

class Adddefaultstates extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();

        $script = $this->script(
            array(
                'description' => "Adds default states to content objects: every object that has no state of a state group gets\n"
                    . "the group's default state (the state with priority 0). Objects with a state of the group are left\n"
                    . "alone, so the script can run again. Prints PASS or FAIL at the end.\n"
                    . "Without --apply nothing is written: the script lists per state group how many objects would get\n"
                    . "its default state. --dry-run means the same as no flag; --apply together with --dry-run is a usage\n"
                    . "error (exit 2).\n\n"
                    . "Example: php bin/php/adddefaultstates.php\n"
                    . "         php bin/php/adddefaultstates.php --apply",
                'use-session' => true,
                'use-modules' => false,
                'use-extensions' => true
            )
        );
        $script->startup();
        $options = $this->options(
            "[apply][dry-run]",
            '',
            array(
                'apply' => "Add the states (the default is a dry run that lists them and changes nothing)",
                'dry-run' => "The default: list how many objects would get a default state, change nothing"
            )
        );
        // before initialize(): a usage error ends the script before any database access
        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $script->shutdown( 2 );
        }
        $dryRun = !$apply;
        $script->initialize();

        $db = \eZDB::instance();
        $plan = self::plan( $db );
        $total = 0;
        foreach ( $plan as $group )
        {
            $cli->output( sprintf( '%s %-24s default state %-20s %d object(s)',
                                   $dryRun ? 'would add' : 'add      ', $group['group_identifier'], $group['state_identifier'], $group['missing'] ) );
            $total += $group['missing'];
        }

        if ( $total && !$dryRun )
        {
            if ( !$dryRun )
                self::apply( $db, $plan );
            // Check what was stored: no object lacks a state of a group now.
            foreach ( self::plan( $db ) as $group )
            {
                if ( $group['missing'] )
                {
                    $cli->error( "FAIL: {$group['missing']} object(s) still have no state of the group {$group['group_identifier']}" );
                    $script->shutdown( 1 );
                }
            }
        }

        $cli->output();
        if ( $dryRun )
            $cli->output( "PASS: $total default state(s) would be added (dry run, nothing changed; --apply writes)" );
        else
            $cli->output( "PASS: $total default state(s) added" );
        $script->shutdown( 0 );
    }

    /**
     * What is missing, read only: one entry per state group with a default state.
     *
     * @param \eZDBInterface $db
     * @return array each group_id, group_identifier, state_id, state_identifier, missing (the number of objects
     *               without a state of the group) and, on MongoDB, missing_ids
     */
    public static function plan( $db )
    {
        $plan = array();
        if ( $db->databaseName() === 'mongo' )
        {
            $allObjectIDs = array_map( 'intval', array_column(
                $db->aggregate( 'ezcontentobject', [ [ '$project' => [ '_id' => 0, 'id' => 1 ] ] ] ), 'id' ) );
            $groups = $db->aggregate( 'ezcobj_state_group', [ [ '$project' => [ '_id' => 0, 'id' => 1, 'identifier' => 1 ] ] ] );
            foreach ( $groups as $group )
            {
                $groupID = (int)$group['id'];
                $defaultStateRows = $db->aggregate( 'ezcobj_state', [
                    [ '$match'   => [ 'group_id' => $groupID, 'priority' => 0 ] ],
                    [ '$project' => [ '_id' => 0, 'id' => 1, 'identifier' => 1 ] ],
                ] );
                if ( empty( $defaultStateRows ) )
                    continue;
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
                $missing = array_values( array_diff( $allObjectIDs, $linkedObjectIDs ) );
                $plan[] = array( 'group_id' => $groupID, 'group_identifier' => (string)( isset( $group['identifier'] ) ? $group['identifier'] : $groupID ),
                                 'state_id' => (int)$defaultStateRows[0]['id'],
                                 'state_identifier' => (string)( isset( $defaultStateRows[0]['identifier'] ) ? $defaultStateRows[0]['identifier'] : '' ),
                                 'missing' => count( $missing ), 'missing_ids' => $missing );
            }
            return $plan;
        }

        $rows = $db->arrayQuery(
            "SELECT g.id AS group_id, g.identifier AS group_identifier, s0.id AS state_id, s0.identifier AS state_identifier
             FROM ezcobj_state_group g, ezcobj_state s0
             WHERE g.id=s0.group_id AND s0.priority=0
             ORDER BY g.id" );
        foreach ( (array)$rows as $row )
        {
            $groupID = (int)$row['group_id'];
            $count = $db->arrayQuery(
                "SELECT COUNT(*) AS missing
                 FROM ezcontentobject o
                 WHERE NOT EXISTS (
                  SELECT l.contentobject_id
                  FROM ezcobj_state_link l, ezcobj_state s
                  WHERE l.contentobject_state_id=s.id
                    AND l.contentobject_id=o.id
                    AND s.group_id=$groupID
                 )" );
            $plan[] = array( 'group_id' => $groupID, 'group_identifier' => (string)$row['group_identifier'],
                             'state_id' => (int)$row['state_id'], 'state_identifier' => (string)$row['state_identifier'],
                             'missing' => (int)$count[0]['missing'] );
        }
        return $plan;
    }

    /**
     * Adds the default states plan() found missing.
     *
     * @param \eZDBInterface $db
     * @param array $plan as plan() returns it
     */
    public static function apply( $db, array $plan )
    {
        $db->begin();
        if ( $db->databaseName() === 'mongo' )
        {
            foreach ( $plan as $group )
            {
                foreach ( $group['missing_ids'] as $objID )
                    $db->insert( 'ezcobj_state_link', [ 'contentobject_id' => (int)$objID, 'contentobject_state_id' => (int)$group['state_id'] ] );
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
    }
}

}
