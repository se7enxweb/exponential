<?php
/**
 * File containing the expMigrationStepSearch class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "search": the search index of objects, or of all of them.
 *
 *   - type: search
 *     mode: reindex                    reindex (the default) or full
 *     objects: [ "remote:home", 42 ]   reindex: these objects; without: the objects the run created or changed so far
 *     classes: [ article ]             full: only the objects of these classes
 *     queue: true                      true (the default): queued for the cronjob indexcontent, as delayed indexing
 *                                      queues them; false: indexed at once, while the migration runs
 *
 * A run reindexes the objects its steps created or changed anyway, after its migrations (migration.ini [RunSettings]
 * Reindex, expMigrationAfterRun): this step is for objects a step changes without saying so (a PHP migration, a SQL
 * change, a class whose attributes became searchable), for an index needed at once (queue: false), and for a full
 * reindex (mode: full) after a change of the search engine or its settings.
 *
 * Indexing undoes nothing and needs no undoing: the step reverts itself by indexing again.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepSearch extends expMigrationStepTypeBase implements expMigrationRevertibleStepType
{
    /** @return string */
    public function name()
    {
        return 'search';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Reindexes objects in the search engine (the ones named or the ones the run changed), or every object',
            'modes' => array( 'reindex', 'full' ),
            'mode' => 'reindex',
            'keys' => array(
                'objects' => array( 'type' => 'list', 'modes' => array( 'reindex' ),
                                    'description' => 'the objects (id, remote:, path:); without: the objects the run created or changed so far' ),
                'classes' => array( 'type' => 'list', 'modes' => array( 'full' ), 'description' => 'only the objects of these classes (identifiers)' ),
                'queue' => array( 'type' => 'bool', 'default' => true,
                                  'description' => 'true: queued for the cronjob indexcontent; false: indexed at once' ),
            ),
            'values' => array(
                'count' => 'how many objects were queued or indexed',
            ),
            'example' => "- type: search\n  mode: reindex\n  objects: [ \"remote:home\" ]\n  queue: false\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = array();
        if ( isset( $step['objects'] ) && is_array( $step['objects'] ) )
            foreach ( $step['objects'] as $i => $object )
            {
                $problem = expMigrationReferences::contains( $object ) ? null : expMigrationIdentifier::problem( 'object', $object );
                if ( $problem !== null )
                    $errors[] = "objects[$i]: $problem";
            }
        if ( isset( $step['classes'] ) && is_array( $step['classes'] ) )
            foreach ( $step['classes'] as $i => $class )
                if ( !is_scalar( $class ) || !preg_match( '/^[A-Za-z0-9_]+$/', (string)$class ) && !expMigrationReferences::contains( (string)$class ) )
                    $errors[] = "classes[$i]: the identifier of a class";
        return $errors;
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $how = !empty( $step['queue'] ) ? 'queue for the search index' : 'index at once';
        if ( $step['mode'] === 'full' )
            return array( "$how every published object" . ( !empty( $step['classes'] ) ? ' of the classes ' . implode( ', ', array_map( 'strval', $step['classes'] ) ) : '' ) );
        if ( !empty( $step['objects'] ) )
            return array( "$how the objects " . implode( ', ', array_map( array( 'expMigrationStepSchema', 'show' ), (array)$step['objects'] ) ) );
        return array( "$how the objects the run created or changed so far" );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $queue = !empty( $step['queue'] );
        $warnings = array();
        if ( $step['mode'] === 'full' )
        {
            $classes = isset( $step['classes'] ) ? array_map( 'strval', (array)$step['classes'] ) : array();
            foreach ( $classes as $class )
                if ( !eZContentClass::fetchByIdentifier( $class, false ) )
                    throw new RuntimeException( "the class $class does not exist" );
            $count = expMigrationAfterRun::reindexAll( $classes, $queue, $warnings );
            $what = 'every published object' . ( $classes ? ' of the classes ' . implode( ', ', $classes ) : '' );
        }
        else
        {
            if ( !empty( $step['objects'] ) )
            {
                $ids = array();
                foreach ( (array)$step['objects'] as $object )
                {
                    $id = expMigrationIdentifier::find( 'object', $object );
                    if ( $id === null )
                        throw new RuntimeException( 'the object ' . expMigrationStepSchema::show( $object ) . ' does not exist' );
                    $ids[] = (int)$id;
                }
                $what = 'the objects ' . implode( ', ', $ids );
            }
            else
            {
                $ids = $context instanceof expMigrationContext ? $context->touchedObjects() : array();
                $what = count( $ids ) . ' objects the run changed';
            }
            $ids = expMigrationAfterRun::published( $ids );
            $count = $queue ? expMigrationAfterRun::queue( $ids ) : expMigrationAfterRun::indexNow( $ids, $warnings );
        }
        $lines = array( ( $queue ? "queued $count for the search index: " : "indexed $count: " ) . $what );
        foreach ( $warnings as $warning )
            $lines[] = $warning;
        return self::result( $lines, array( 'count' => $count ) );
    }

    /**
     * Nothing to undo: the objects a rollback changes are reindexed with it.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        return array( 'search: nothing to undo' );
    }
}
