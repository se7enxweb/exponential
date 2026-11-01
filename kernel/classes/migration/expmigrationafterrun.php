<?php
/**
 * File containing the expMigrationAfterRun class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What a run does after its migrations: the smart cache list and the search index.
 *
 * - The caches: each step type declares the caches its steps touch (expMigrationStepType::caches()); the caches of
 *   the migrations a run applied, and those the step type cache left for the end, are cleared once after the run,
 *   by their names in ezcache (ids such as content or template-block, tags such as ini). Only those, never all of
 *   them. The same list goes into the report (caches) with the deploy that clears exactly those caches on a server
 *   with Velocity and PHP-FPM too (deploy: ./console exp:velocity deploy --caches=... --apply).
 * - The search index: the objects the steps created or changed (expMigrationContext::touchedObjects()) are queued for
 *   the search engine (the pending actions the cronjob indexcontent works off, as delayed indexing queues them), or
 *   indexed at once, or left alone: migration.ini [RunSettings] Reindex=queue|now|disabled.
 *
 * Neither is done for a dry run, or when nothing was applied.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationAfterRun
{
    /** How many objects one query of reindexAll() reads. */
    const BATCH = 200;

    /**
     * The caches the applied migrations of a report touched, with those steps left for the end. Pure but for the
     * migrations' caches().
     *
     * @param array $report a run's report
     * @param expMigration[] $byId id => migration
     * @param string[] $deferred the caches the steps asked for (expMigrationContext::deferredCaches())
     * @return string[] sorted, each once
     */
    public static function caches( array $report, array $byId, array $deferred = array() )
    {
        $caches = $deferred;
        foreach ( $report['migrations'] as $entry )
        {
            if ( !isset( $entry['action'] ) || !in_array( $entry['action'], array( 'applied', 'undone' ), true ) || !isset( $byId[$entry['id']] ) )
                continue;
            $migration = $byId[$entry['id']];
            if ( method_exists( $migration, 'caches' ) )
                foreach ( (array)$migration->caches() as $cache )
                    $caches[] = (string)$cache;
        }
        return self::normalize( $caches );
    }

    /**
     * Cache names trimmed, each once, sorted. Pure.
     *
     * @param string[] $caches
     * @return string[]
     */
    public static function normalize( array $caches )
    {
        $list = array();
        foreach ( $caches as $cache )
        {
            $cache = trim( (string)$cache );
            if ( $cache !== '' && !in_array( $cache, $list, true ) )
                $list[] = $cache;
        }
        sort( $list );
        return $list;
    }

    /**
     * The command that clears exactly these caches on the server, PHP-FPM and Velocity included. Pure.
     *
     * @param string[] $caches
     * @return string '' without caches
     */
    public static function deployCommand( array $caches )
    {
        $caches = self::normalize( $caches );
        return $caches ? './console exp:velocity deploy --caches=' . implode( ',', $caches ) . ' --apply' : '';
    }

    /**
     * Whether a name is a cache of ezcache: 'id', 'tag' or '' (unknown). An id first (content, template and ini are
     * ids and tags at once); tag:<name> is always the tag.
     *
     * @param string $name
     * @param array|null $cacheList eZCache::fetchList() (read when null)
     * @return string
     */
    public static function kind( $name, $cacheList = null )
    {
        $cacheList = $cacheList === null ? eZCache::fetchList() : $cacheList;
        $name = (string)$name;
        if ( strpos( $name, 'tag:' ) === 0 )
            return in_array( substr( $name, 4 ), eZCache::fetchTagList( $cacheList ), true ) ? 'tag' : '';
        if ( in_array( $name, eZCache::fetchIDList( $cacheList ), true ) )
            return 'id';
        if ( in_array( $name, eZCache::fetchTagList( $cacheList ), true ) )
            return 'tag';
        return '';
    }

    /**
     * Clears caches by their ids or tags in ezcache.
     *
     * @param string[] $caches
     * @param string[] $warnings the names that are no cache of ezcache, and the failures
     * @return string[] what was cleared, one line per cache
     */
    public static function clear( array $caches, array &$warnings = array() )
    {
        $lines = array();
        $cacheList = eZCache::fetchList();
        foreach ( self::normalize( $caches ) as $cache )
        {
            try
            {
                switch ( self::kind( $cache, $cacheList ) )
                {
                    case 'id':
                        eZCache::clearByID( array( $cache ), $cacheList );
                        $lines[] = "cleared the cache $cache";
                        break;
                    case 'tag':
                        $tag = strpos( $cache, 'tag:' ) === 0 ? substr( $cache, 4 ) : $cache;
                        eZCache::clearByTag( $tag, $cacheList );
                        $lines[] = "cleared the caches tagged $tag";
                        break;
                    default:
                        $warnings[] = "the cache $cache is not a cache of ezcache: not cleared";
                }
            }
            catch ( Throwable $e )
            {
                $warnings[] = "the cache $cache could not be cleared: " . $e->getMessage();
            }
        }
        return $lines;
    }

    /**
     * The published objects of a list that exist (an object a step deleted or moved to the trash is left out: its
     * removal from the index happened with it).
     *
     * @param int[] $objectIds
     * @return int[]
     */
    public static function published( array $objectIds )
    {
        $ids = array();
        foreach ( array_unique( array_map( 'intval', $objectIds ) ) as $id )
        {
            if ( $id <= 0 )
                continue;
            $row = eZContentObject::fetch( $id, false );
            if ( is_array( $row ) && (int)$row['status'] === eZContentObject::STATUS_PUBLISHED )
                $ids[] = $id;
        }
        return $ids;
    }

    /**
     * Queues objects for the search engine: a pending action index_object for each, unless one is queued already.
     * The cronjob indexcontent indexes them.
     *
     * @param int[] $objectIds
     * @return int how many were queued
     */
    public static function queue( array $objectIds )
    {
        $queued = 0;
        foreach ( array_unique( array_map( 'intval', $objectIds ) ) as $id )
        {
            if ( $id <= 0 )
                continue;
            $waiting = eZPersistentObject::fetchObjectList( eZPendingActions::definition(), array( 'id' ), array( 'action' => 'index_object', 'param' => (string)$id ),
                                                            null, array( 'offset' => 0, 'limit' => 1 ), false );
            if ( $waiting )
                continue;
            $action = new eZPendingActions( array( 'action' => 'index_object', 'created' => time(), 'param' => (string)$id ) );
            $action->store();
            $queued++;
        }
        return $queued;
    }

    /**
     * Indexes objects in the search engine at once.
     *
     * @param int[] $objectIds
     * @param string[] $warnings the objects that could not be indexed
     * @return int how many were indexed
     */
    public static function indexNow( array $objectIds, array &$warnings = array() )
    {
        if ( eZSearch::isDisabled() )
        {
            $warnings[] = 'the search engine is switched off: nothing was indexed';
            return 0;
        }
        $done = 0;
        foreach ( array_unique( array_map( 'intval', $objectIds ) ) as $id )
        {
            $object = $id > 0 ? eZContentObject::fetch( $id ) : null;
            if ( !$object instanceof eZContentObject )
                continue;
            if ( eZSearch::needRemoveWithUpdate() )
                eZSearch::removeObjectById( $id );
            if ( eZSearch::addObject( $object, true ) )
                $done++;
            else
                $warnings[] = "the object $id could not be indexed";
            eZContentObject::clearCache( array( $id ) );
        }
        return $done;
    }

    /**
     * The ids of every published object, or of the objects of some classes, a batch at a time.
     *
     * @param string[] $classes class identifiers; empty: every class
     * @param int $offset
     * @param int $limit
     * @return int[]
     */
    public static function publishedIds( array $classes, $offset, $limit )
    {
        $conditions = array( 'status' => eZContentObject::STATUS_PUBLISHED );
        if ( $classes )
        {
            $classIds = array();
            foreach ( $classes as $identifier )
            {
                $class = eZContentClass::fetchByIdentifier( (string)$identifier, false );
                if ( is_array( $class ) )
                    $classIds[] = (int)$class['id'];
            }
            if ( !$classIds )
                return array();
            $conditions['contentclass_id'] = array( $classIds );
        }
        $rows = eZPersistentObject::fetchObjectList( eZContentObject::definition(), array( 'id' ), $conditions, array( 'id' => 'asc' ),
                                                     array( 'offset' => (int)$offset, 'limit' => (int)$limit ), false );
        return array_map( function ( $row ) { return (int)$row['id']; }, is_array( $rows ) ? $rows : array() );
    }

    /**
     * Reindexes every published object (of some classes): queued or at once.
     *
     * @param string[] $classes
     * @param bool $queue
     * @param string[] $warnings
     * @return int how many were queued or indexed
     */
    public static function reindexAll( array $classes, $queue, array &$warnings = array() )
    {
        $count = 0;
        for ( $offset = 0; ; $offset += self::BATCH )
        {
            $ids = self::publishedIds( $classes, $offset, self::BATCH );
            if ( !$ids )
                break;
            $count += $queue ? self::queue( $ids ) : self::indexNow( $ids, $warnings );
            if ( count( $ids ) < self::BATCH )
                break;
        }
        return $count;
    }

    /**
     * The mode of the search index after a run: queue, now or disabled ([RunSettings] Reindex, or the option reindex).
     *
     * @param expMigrationContext|null $context
     * @param eZINI|null $ini
     * @return string
     */
    public static function reindexMode( $context = null, $ini = null )
    {
        $mode = $context instanceof expUpgradeContext ? (string)$context->option( 'reindex', '' ) : '';
        if ( $mode === '' )
        {
            $ini = $ini ?: eZINI::instance( 'migration.ini' );
            $mode = $ini->hasVariable( 'RunSettings', 'Reindex' ) ? trim( (string)$ini->variable( 'RunSettings', 'Reindex' ) ) : 'queue';
        }
        return in_array( $mode, array( 'queue', 'now', 'disabled' ), true ) ? $mode : 'queue';
    }

    /**
     * Whether the caches are cleared after a run ([RunSettings] ClearCaches, or the option no-cache-clear).
     *
     * @param expMigrationContext|null $context
     * @param eZINI|null $ini
     * @return bool
     */
    public static function clearsCaches( $context = null, $ini = null )
    {
        if ( $context instanceof expUpgradeContext && $context->option( 'no-cache-clear' ) )
            return false;
        $ini = $ini ?: eZINI::instance( 'migration.ini' );
        return !$ini->hasVariable( 'RunSettings', 'ClearCaches' ) || $ini->variable( 'RunSettings', 'ClearCaches' ) !== 'disabled';
    }

    /**
     * After a run that applied migrations: the caches cleared and the touched objects reindexed. Sets in the report:
     * caches (the list), deploy (the command that clears them on the server too), reindex (mode and count); the
     * lines go to the run log, problems to the warnings. Never throws.
     *
     * @param array $report
     * @param expMigration[] $byId
     * @param expMigrationContext $context
     */
    public static function finish( array &$report, array $byId, expMigrationContext $context )
    {
        try
        {
            $caches = self::caches( $report, $byId, $context->deferredCaches() );
            $report['caches'] = $caches;
            $report['deploy'] = self::deployCommand( $caches );
            $applied = ( isset( $report['summary']['applied'] ) ? (int)$report['summary']['applied'] : 0 )
                     + ( isset( $report['summary']['undone'] ) ? (int)$report['summary']['undone'] : 0 );
            if ( !$context->isApply() || $applied === 0 && !$context->deferredCaches() )
                return;
            if ( $caches && self::clearsCaches( $context ) )
                foreach ( self::clear( $caches, $report['warnings'] ) as $line )
                    $context->log( 'info', $line );
            $mode = self::reindexMode( $context );
            $ids = self::published( $context->touchedObjects() );
            $count = 0;
            if ( $ids && $mode === 'queue' )
                $count = self::queue( $ids );
            elseif ( $ids && $mode === 'now' )
                $count = self::indexNow( $ids, $report['warnings'] );
            $report['reindex'] = array( 'mode' => $mode, 'objects' => count( $ids ), 'count' => $count );
            if ( $ids )
                $context->log( 'info', $mode === 'disabled' ? count( $ids ) . ' objects changed, not reindexed ([RunSettings] Reindex=disabled)'
                                                            : ( $mode === 'queue' ? "$count objects queued for the search index" : "$count objects indexed" ) );
        }
        catch ( Throwable $e )
        {
            $report['warnings'][] = 'after the run (caches, search index): ' . $e->getMessage();
        }
    }
}
