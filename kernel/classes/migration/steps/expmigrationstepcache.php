<?php
/**
 * File containing the expMigrationStepCache class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "cache": clears caches of ezcache, by id or by tag, and the view cache of single objects.
 *
 *   - type: cache
 *     caches: [ template-block, exphttpcache ]   ids of ezcache (./console ... bin/php/ezcache.php --list-ids)
 *     tags: [ ini ]                               tags of ezcache (in the run's cache list as tag:ini)
 *     objects: [ "remote:home", 2 ]               the content view caches of these objects (their nodes and the
 *                                                 nodes the smart view cache clears with them)
 *     when: end                                   end (the default): cleared once after the run, with the caches
 *                                                 the other steps declare; now: cleared at once
 *
 * Every step type declares the caches its steps touch, and a run clears them after its migrations
 * (expMigrationAfterRun): a migration needs this step only for a cache no step knows about (a template that reads
 * something a step changed, an INI file a PHP migration wrote), or when a later step needs a cache cleared first
 * (when: now). The caches named here are part of the run's cache list, and so of the deploy it suggests.
 *
 * Clearing a cache undoes nothing and needs no undoing: the step reverts itself by clearing the caches again.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepCache extends expMigrationStepTypeBase implements expMigrationRevertibleStepType
{
    /** @return string */
    public function name()
    {
        return 'cache';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Clears caches of ezcache by id or tag and the view caches of single objects, at once or after the run',
            'keys' => array(
                'caches' => array( 'type' => 'list', 'description' => 'ids of ezcache: content, template-block, exphttpcache, template-override ...' ),
                'tags' => array( 'type' => 'list', 'description' => 'tags of ezcache: ini, template, content, image ... (every cache with the tag)' ),
                'objects' => array( 'type' => 'list', 'description' => 'objects (id, remote:, path:) whose content view caches are cleared' ),
                'when' => array( 'type' => 'string', 'default' => 'end', 'values' => array( 'end', 'now' ),
                                 'description' => 'end: cleared once after the run with the caches of the other steps; now: cleared at once' ),
            ),
            'values' => array(
                'caches' => 'the ids and tags the step clears (or leaves for the end), comma-separated',
                'objects' => 'how many objects had their view caches cleared',
            ),
            'example' => "- type: cache\n  caches: [ template-block ]\n  tags: [ ini ]\n  objects: [ \"remote:home\" ]\n  when: now\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = array();
        if ( empty( $step['caches'] ) && empty( $step['tags'] ) && empty( $step['objects'] ) )
            $errors[] = 'name what is cleared: caches:, tags: or objects:';
        $cacheList = null;
        foreach ( array( 'caches' => 'id', 'tags' => 'tag' ) as $key => $kind )
        {
            if ( !isset( $step[$key] ) || !is_array( $step[$key] ) )
                continue;
            foreach ( $step[$key] as $i => $name )
            {
                if ( !is_scalar( $name ) || trim( (string)$name ) === '' )
                {
                    $errors[] = "{$key}[$i]: a name of ezcache";
                    continue;
                }
                if ( expMigrationReferences::contains( (string)$name ) )
                    continue;
                $cacheList = $cacheList === null ? eZCache::fetchList() : $cacheList;
                $known = $kind === 'id' ? eZCache::fetchIDList( $cacheList ) : eZCache::fetchTagList( $cacheList );
                if ( !in_array( (string)$name, $known, true ) )
                    $errors[] = "{$key}[$i]: $name is no " . ( $kind === 'id' ? 'id' : 'tag' ) . ' of ezcache (' . implode( ', ', $known ) . ')';
            }
        }
        if ( isset( $step['objects'] ) && is_array( $step['objects'] ) )
            foreach ( $step['objects'] as $i => $object )
            {
                $problem = expMigrationReferences::contains( $object ) ? null : expMigrationIdentifier::problem( 'object', $object );
                if ( $problem !== null )
                    $errors[] = "objects[$i]: $problem";
            }
        return $errors;
    }

    /**
     * The ids and tags the step names: they join the run's cache list.
     *
     * @param array $step
     * @return string[]
     */
    public function caches( array $step )
    {
        $caches = array();
        foreach ( self::names( $step ) as $name )
            if ( !expMigrationReferences::contains( $name ) )
                $caches[] = $name;
        if ( !empty( $step['objects'] ) )
            $caches[] = 'content';
        return $caches;
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $names = self::names( $step );
        $lines = array();
        $at = $step['when'] === 'now' ? 'now' : 'after the run';
        if ( $names )
            $lines[] = 'clear the caches ' . implode( ', ', $names ) . " $at";
        if ( !empty( $step['objects'] ) )
            $lines[] = 'clear the view caches of ' . implode( ', ', array_map( array( 'expMigrationStepSchema', 'show' ), (array)$step['objects'] ) ) . ' now';
        return $lines;
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $lines = array();
        $names = self::names( $step );
        if ( $names && $step['when'] === 'now' )
        {
            $warnings = array();
            $lines = expMigrationAfterRun::clear( $names, $warnings );
            if ( $warnings )
                throw new RuntimeException( implode( '; ', $warnings ) );
        }
        elseif ( $names )
        {
            if ( $context instanceof expMigrationContext )
                $context->deferCaches( $names );
            $lines[] = 'the caches ' . implode( ', ', $names ) . ' are cleared after the run';
        }
        $count = 0;
        foreach ( isset( $step['objects'] ) ? (array)$step['objects'] : array() as $object )
        {
            $id = expMigrationIdentifier::find( 'object', $object );
            if ( $id === null )
                throw new RuntimeException( 'the object ' . expMigrationStepSchema::show( $object ) . ' does not exist' );
            eZContentCacheManager::clearContentCacheIfNeeded( (int)$id );
            eZContentObject::clearCache( array( (int)$id ) );
            $lines[] = "cleared the view caches of the object $id";
            $count++;
        }
        return self::result( $lines, array( 'caches' => implode( ',', $names ), 'objects' => $count ) );
    }

    /**
     * Clears the caches named by id or tag again, after the rollback: what they held is built anew either way. The
     * objects' view caches are cleared with the objects the rollback changes.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        $names = self::names( $step );
        if ( !$names )
            return array( 'cache: nothing to undo' );
        if ( $context instanceof expMigrationContext )
            $context->deferCaches( $names );
        return array( 'the caches ' . implode( ', ', $names ) . ' are cleared after the rollback' );
    }

    /**
     * The ids and tags of a step, each once; a tag as tag:<name>, since some names are an id and a tag at once
     * (content, template, ini).
     *
     * @param array $step
     * @return string[]
     */
    protected static function names( array $step )
    {
        $names = array();
        foreach ( array( 'caches' => '', 'tags' => 'tag:' ) as $key => $prefix )
            if ( isset( $step[$key] ) && is_array( $step[$key] ) )
                foreach ( $step[$key] as $name )
                    if ( is_scalar( $name ) && trim( (string)$name ) !== '' && !in_array( $prefix . trim( (string)$name ), $names, true ) )
                        $names[] = $prefix . trim( (string)$name );
        return $names;
    }
}
