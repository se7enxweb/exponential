<?php
/**
 * File containing the expUrlMapImporter class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Applies a planned URL map import (expUrlMapPlan) as one batch, and undoes a batch.
 *
 *   $plan = ( new expUrlMapPlanner() )->plan( $rows, array( 'siteaccess' => 'site', 'on_conflict' => 'overwrite' ) );
 *   $result = ( new expUrlMapImporter() )->apply( $plan, array( 'user_id' => 14 ) );
 *   ( new expUrlMapImporter() )->undo( $result['batch_id'] );
 *
 * apply() writes the rows whose action is create or update, in one transaction where the database has them (MySQL,
 * PostgreSQL, SQLite, Oracle): global URL aliases with eZURLAliasML::storePath() (an alias being replaced is made a
 * placeholder first, so its children stay), wildcards as eZURLWildcard, redirects as expUrlRedirect. Before each
 * write the entry at the old address is compared with the plan's: one that changed since the plan is skipped
 * ("changed since the preview"). Every row it writes is recorded in exp_urlmap_batch_item with the row before (null
 * for a new one) and after; for an alias these are each element of the path the import wrote, by key.
 *
 * undo() restores the rows before, the last first. A row that changed after the import (it is not the row the
 * import left) is kept, unless force is given; the batch is then partly-undone. Both clear the wildcard cache and the
 * redirects' cache, and write the audit events content.urlalias.import and content.urlalias.import_undo with the
 * counts.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUrlMapImporter
{
    /** @var array */
    protected $options;

    /** @var expUrlMapEnvironment */
    protected $environment;

    /** @var int the batch apply() writes */
    protected $batchId = 0;

    /**
     * @param array $options db (eZDBInterface), environment (expUrlMapEnvironment), audit (callable( $name, $data )
     *                       instead of expAudit), user_id (who runs it; default the current user)
     */
    public function __construct( array $options = array() )
    {
        $this->options = $options + array( 'db' => null, 'environment' => null, 'audit' => null, 'user_id' => null );
        $this->environment = $this->options['environment'] ?: new expUrlMapKernelEnvironment();
    }

    /** @return eZDBInterface */
    protected function db()
    {
        return $this->options['db'] ?: eZDB::instance();
    }

    /**
     * Imports what the plan says.
     *
     * @param expUrlMapPlan $plan
     * @param array $options dry_run (only check, write nothing)
     * @return array batch_id (0 for a dry run), counts (created, updated, skipped, failed), items (per row: index,
     *               line, old_url, result created|updated|skipped|failed, message)
     * @throws RuntimeException when the tables are missing or the transaction fails
     */
    public function apply( expUrlMapPlan $plan, array $options = array() )
    {
        $dryRun = !empty( $options['dry_run'] );
        $db = $this->db();
        if ( !$dryRun && !expUrlMapBatch::tablesExist( $db ) )
            throw new RuntimeException( 'the tables exp_urlmap_batch and exp_urlmap_batch_item do not exist yet: ./console exp:upgrade --apply (step 6.0.15-url-map)' );
        $actionable = $plan->actionable();
        $needsRedirects = (bool)array_filter( $actionable, function ( $item ) { return $item['store'] === expUrlMapPlan::STORE_REDIRECT; } );
        if ( !$dryRun && $needsRedirects && !expUrlRedirect::tableExists( $db ) )
            throw new RuntimeException( 'the table exp_url_redirect does not exist yet: ./console exp:upgrade --apply (step 6.0.15-url-map)' );

        $counts = array( 'created' => 0, 'updated' => 0, 'skipped' => 0, 'failed' => 0 );
        $results = array();
        $batch = null;
        $seq = 0;
        $stores = array();
        if ( !$dryRun )
            $db->begin();
        try
        {
            if ( !$dryRun )
            {
                $batch = new expUrlMapBatch( array(
                    'created' => time(), 'user_id' => $this->userId(), 'source_name' => mb_substr( (string)$plan->option( 'source_name', '' ), 0, 255 ),
                    'format' => (string)$plan->option( 'format', '' ), 'siteaccess' => (string)$plan->option( 'siteaccess', '' ),
                    'language' => (string)$plan->option( 'language', '' ), 'on_conflict' => (string)$plan->option( 'on_conflict', '' ),
                    'state' => expUrlMapBatch::STATE_APPLIED, 'rows_total' => count( $plan->items() ) ) );
                $batch->store();
                $this->batchId = (int)$batch->attribute( 'id' );
            }
            foreach ( $plan->items() as $item )
            {
                if ( $item['action'] === 'none' )
                {
                    $counts['skipped']++;
                    continue;
                }
                $current = $this->current( $item );
                if ( !self::sameEntry( $current, $item['existing'] ) )
                {
                    $counts['skipped']++;
                    $results[] = self::result( $item, 'skipped', 'the entry at the old address changed since the preview' );
                    continue;
                }
                if ( $dryRun )
                {
                    $counts[$item['action'] === 'create' ? 'created' : 'updated']++;
                    $results[] = self::result( $item, $item['action'] === 'create' ? 'created' : 'updated', 'dry run: nothing written' );
                    continue;
                }
                $changes = $this->write( $item );
                if ( $changes === null )
                {
                    $counts['failed']++;
                    $results[] = self::result( $item, 'failed', 'the entry could not be written' );
                    continue;
                }
                foreach ( $changes as $change )
                {
                    $record = new expUrlMapBatchItem( array(
                        'batch_id' => (int)$batch->attribute( 'id' ), 'seq' => ++$seq, 'line' => (int)$item['line'], 'kind' => $item['store'],
                        'target_table' => $change['table'], 'target_key' => $change['key'], 'action' => $change['before'] === null ? 'created' : 'updated',
                        'state' => expUrlMapBatchItem::STATE_APPLIED, 'old_url' => mb_substr( (string)$item['old_url'], 0, 1000 ),
                        'before_image' => expUrlMapBatchItem::encode( $change['before'] ), 'after_image' => expUrlMapBatchItem::encode( $change['after'] ) ) );
                    $record->store();
                }
                $stores[$item['store']] = true;
                $counts[$item['action'] === 'create' ? 'created' : 'updated']++;
                $results[] = self::result( $item, $item['action'] === 'create' ? 'created' : 'updated', '' );
            }
            if ( !$dryRun )
            {
                $batch->setAttribute( 'created_count', $counts['created'] );
                $batch->setAttribute( 'updated_count', $counts['updated'] );
                $batch->setAttribute( 'skipped_count', $counts['skipped'] );
                $batch->setAttribute( 'failed_count', $counts['failed'] );
                $batch->setAttribute( 'summary', (string)json_encode( array( 'plan' => $plan->counts(), 'options' => $plan->options() ),
                                                                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR ) );
                $batch->store();
                $db->commit();
            }
        }
        catch ( Throwable $e )
        {
            if ( !$dryRun && $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $batchId = $batch ? (int)$batch->attribute( 'id' ) : 0;
        if ( !$dryRun )
        {
            self::expireCaches( $stores );
            $this->event( 'content.urlalias.import', array( 'object' => array( 'type' => 'urlmap_batch', 'id' => $batchId ), 'verb' => 'import',
                                                           'after' => $counts + array( 'rows' => count( $plan->items() ), 'source' => (string)$plan->option( 'source_name', '' ) ) ) );
        }
        return array( 'batch_id' => $batchId, 'dry_run' => $dryRun, 'counts' => $counts, 'items' => $results );
    }

    /**
     * Undoes a batch: every row it wrote back as it was, the last first.
     *
     * @param int $batchId
     * @param array $options force (also restore rows that changed after the import, and the rows a
     *                       first undo kept), user_id
     * @return array batch_id, state, counts (undone, kept), items (target_table, target_key, result undone|kept)
     * @throws RuntimeException when the batch does not exist or was undone already
     */
    public function undo( $batchId, array $options = array() )
    {
        $db = $this->db();
        if ( !expUrlMapBatch::tablesExist( $db ) )
            throw new RuntimeException( 'the tables exp_urlmap_batch and exp_urlmap_batch_item do not exist' );
        $batch = expUrlMapBatch::fetch( $batchId );
        if ( !$batch )
            throw new RuntimeException( "there is no URL map import $batchId" );
        if ( $batch->attribute( 'state' ) === expUrlMapBatch::STATE_UNDONE )
            throw new RuntimeException( "the URL map import $batchId was undone already" );
        $force = !empty( $options['force'] );
        $counts = array( 'undone' => 0, 'kept' => 0 );
        $results = array();
        $stores = array();
        $db->begin();
        try
        {
            foreach ( array_reverse( $batch->items() ) as $item )
            {
                $state = (string)$item->attribute( 'state' );
                if ( $state !== expUrlMapBatchItem::STATE_APPLIED && !( $force && $state === expUrlMapBatchItem::STATE_KEPT ) )
                    continue;
                $table = (string)$item->attribute( 'target_table' );
                $key = (string)$item->attribute( 'target_key' );
                $now = $this->readRow( $table, $key );
                if ( !$force && !self::sameRow( $now, $item->after() ) )
                {
                    $item->setAttribute( 'state', expUrlMapBatchItem::STATE_KEPT );
                    $item->store();
                    $counts['kept']++;
                    $results[] = array( 'target_table' => $table, 'target_key' => $key, 'old_url' => (string)$item->attribute( 'old_url' ), 'result' => 'kept',
                                        'message' => 'changed after the import' );
                    continue;
                }
                $this->restoreRow( $table, $key, $item->before() );
                $item->setAttribute( 'state', expUrlMapBatchItem::STATE_UNDONE );
                $item->store();
                $stores[(string)$item->attribute( 'kind' )] = true;
                $counts['undone']++;
                $results[] = array( 'target_table' => $table, 'target_key' => $key, 'old_url' => (string)$item->attribute( 'old_url' ), 'result' => 'undone', 'message' => '' );
            }
            $state = $counts['kept'] ? expUrlMapBatch::STATE_PARTLY_UNDONE : expUrlMapBatch::STATE_UNDONE;
            $batch->setAttribute( 'state', $state );
            $batch->setAttribute( 'undone', time() );
            $batch->setAttribute( 'undone_by', isset( $options['user_id'] ) ? (int)$options['user_id'] : $this->userId() );
            $batch->store();
            $db->commit();
        }
        catch ( Throwable $e )
        {
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        self::expireCaches( $stores );
        $this->event( 'content.urlalias.import_undo', array( 'object' => array( 'type' => 'urlmap_batch', 'id' => (int)$batchId ), 'verb' => 'undo',
                                                            'after' => $counts + array( 'state' => $state ) ) );
        return array( 'batch_id' => (int)$batchId, 'state' => $state, 'counts' => $counts, 'items' => $results );
    }

    /**
     * The entries of a batch that changed after the import: an undo keeps them unless it is forced. Only items an
     * undo would still restore count (applied ones; with $kept also those a first undo kept). Reads, writes nothing.
     *
     * @param int $batchId
     * @param bool $kept also the items a first undo kept
     * @return array[] target_table, target_key, old_url of each
     * @since 6.0.15
     */
    public function changedSince( $batchId, $kept = false )
    {
        $batch = expUrlMapBatch::tablesExist( $this->db() ) ? expUrlMapBatch::fetch( $batchId ) : null;
        if ( !$batch )
            return array();
        $out = array();
        foreach ( $batch->items() as $item )
        {
            $state = (string)$item->attribute( 'state' );
            if ( $state !== expUrlMapBatchItem::STATE_APPLIED && !( $kept && $state === expUrlMapBatchItem::STATE_KEPT ) )
                continue;
            $table = (string)$item->attribute( 'target_table' );
            $key = (string)$item->attribute( 'target_key' );
            if ( !self::sameRow( $this->readRow( $table, $key ), $item->after() ) )
                $out[] = array( 'target_table' => $table, 'target_key' => $key, 'old_url' => (string)$item->attribute( 'old_url' ) );
        }
        return $out;
    }

    /**
     * The entry at an item's old address now, as the planner compares it.
     *
     * @param array $item
     * @return array|null
     */
    protected function current( array $item )
    {
        switch ( $item['store'] )
        {
            case expUrlMapPlan::STORE_ALIAS:
                return $this->environment->existingAlias( $item['write']['path'] );
            case expUrlMapPlan::STORE_WILDCARD:
                return $this->environment->existingWildcard( $item['write']['source_url'] );
            case expUrlMapPlan::STORE_REDIRECT:
                return $this->environment->existingRedirect( $item['write']['siteaccess'], $item['write']['path'], $item['write']['query'] );
        }
        return null;
    }

    /**
     * Writes one item.
     *
     * @param array $item
     * @return array[]|null the rows it changed: table, key, before, after; null when it failed
     */
    protected function write( array $item )
    {
        $w = $item['write'];
        switch ( $item['store'] )
        {
            case expUrlMapPlan::STORE_ALIAS:
                return $this->writeAlias( $w );
            case expUrlMapPlan::STORE_WILDCARD:
                $existing = eZURLWildcard::fetchBySourceURL( $w['source_url'] );
                $before = $existing ? self::wildcardRow( $existing ) : null;
                $wildcard = $existing ?: new eZURLWildcard( array( 'source_url' => $w['source_url'] ) );
                $wildcard->setAttribute( 'destination_url', $w['destination_url'] );
                $wildcard->setAttribute( 'type', (int)$w['type'] );
                $wildcard->store();
                return array( array( 'table' => 'ezurlwildcard', 'key' => (string)(int)$wildcard->attribute( 'id' ), 'before' => $before,
                                     'after' => self::wildcardRow( $wildcard ) ) );
            case expUrlMapPlan::STORE_REDIRECT:
                $existing = expUrlRedirect::fetchBySource( $w['siteaccess'], $w['path'], $w['query'] );
                $before = $existing ? $existing->asArray() : null;
                $redirect = $existing ?: expUrlRedirect::create( $w['path'], $w['query'], $w['target'], $w['status'], $w['siteaccess'], $w['language'] );
                $redirect->setAttribute( 'target', (string)$w['target'] );
                $redirect->setAttribute( 'status', (int)$w['status'] );
                $redirect->setAttribute( 'language', (string)$w['language'] );
                $redirect->setAttribute( 'modified', time() );
                $redirect->setAttribute( 'batch_id', $this->batchId );
                $redirect->store();
                return array( array( 'table' => expUrlRedirect::TABLE, 'key' => (string)(int)$redirect->attribute( 'id' ), 'before' => $before,
                                     'after' => $redirect->asArray() ) );
        }
        return null;
    }

    /**
     * Writes a global URL alias and returns every element of its path that changed.
     *
     * @param array $w path, action, link, redirects, language
     * @return array[]|null
     */
    protected function writeAlias( array $w )
    {
        $db = $this->db();
        $before = expUrlMapKernelEnvironment::aliasPathRows( $w['path'], $db );
        $last = end( $before );
        // an alias being replaced becomes a placeholder first: storePath() reuses it, its children stay
        if ( $last && $last['row'] !== null && (string)$last['row']['action'] !== 'nop:' && count( $before ) === count( expUrlMapKernelEnvironment::aliasElements( $w['path'] ) ) )
            $db->query( "UPDATE ezurlalias_ml SET action = 'nop:', action_type = 'nop', is_alias = 0 WHERE parent = " . (int)$last['parent']
                        . " AND text_md5 = '" . $db->escapeString( $last['text_md5'] ) . "'" );
        $result = eZURLAliasML::storePath( trim( $w['path'], '/' ), $w['action'], $w['language'], $w['link'], false, false, false, false, false,
                                           (bool)$w['redirects'] );
        if ( !is_array( $result ) || $result['status'] !== true )
            return null;
        $beforeByKey = array();
        foreach ( $before as $element )
            $beforeByKey[$element['key']] = $element['row'];
        $changes = array();
        foreach ( expUrlMapKernelEnvironment::aliasPathRows( $w['path'], $db ) as $element )
        {
            $old = isset( $beforeByKey[$element['key']] ) ? $beforeByKey[$element['key']] : null;
            if ( self::sameRow( $old, $element['row'] ) )
                continue;
            $changes[] = array( 'table' => 'ezurlalias_ml', 'key' => $element['key'], 'before' => $old, 'after' => $element['row'] );
        }
        return $changes;
    }

    /**
     * A row by table and key, or null.
     *
     * @param string $table
     * @param string $key
     * @return array|null
     */
    protected function readRow( $table, $key )
    {
        switch ( $table )
        {
            case 'ezurlalias_ml':
                list( $parent, $md5 ) = explode( ':', $key, 2 ) + array( 1 => '' );
                return expUrlMapKernelEnvironment::aliasRow( (int)$parent, $md5, $this->db() );
            case 'ezurlwildcard':
                $wildcard = eZURLWildcard::fetch( (int)$key );
                return $wildcard ? self::wildcardRow( $wildcard ) : null;
            case expUrlRedirect::TABLE:
                $redirect = expUrlRedirect::fetch( (int)$key );
                return $redirect ? $redirect->asArray() : null;
        }
        return null;
    }

    /**
     * Puts a row back as it was (null: removes it).
     *
     * @param string $table
     * @param string $key
     * @param array|null $row
     */
    protected function restoreRow( $table, $key, $row )
    {
        $db = $this->db();
        switch ( $table )
        {
            case 'ezurlalias_ml':
                list( $parent, $md5 ) = explode( ':', $key, 2 ) + array( 1 => '' );
                $db->query( 'DELETE FROM ezurlalias_ml WHERE parent = ' . (int)$parent . " AND text_md5 = '" . $db->escapeString( $md5 ) . "'" );
                if ( $row !== null )
                    $db->query( self::insertSql( $db, 'ezurlalias_ml', $row, array( 'id', 'parent', 'lang_mask', 'link', 'is_alias', 'is_original', 'alias_redirects' ) ) );
                break;
            case 'ezurlwildcard':
                eZURLWildcard::removeByIDs( array( (int)$key ) );
                if ( $row !== null )
                    $db->query( self::insertSql( $db, 'ezurlwildcard', $row, array( 'id', 'type' ) ) );
                break;
            case expUrlRedirect::TABLE:
                $db->query( 'DELETE FROM ' . expUrlRedirect::TABLE . ' WHERE id = ' . (int)$key );
                if ( $row !== null )
                    $db->query( self::insertSql( $db, expUrlRedirect::TABLE, $row, array( 'id', 'status', 'batch_id', 'created', 'modified' ) ) );
                break;
        }
    }

    /**
     * @param eZDBInterface $db
     * @param string $table
     * @param array $row
     * @param string[] $numbers the number columns
     * @return string
     */
    protected static function insertSql( $db, $table, array $row, array $numbers )
    {
        $values = array();
        foreach ( $row as $name => $value )
            $values[] = in_array( $name, $numbers, true ) ? (string)(int)$value : "'" . $db->escapeString( (string)$value ) . "'";
        return 'INSERT INTO ' . $table . ' ( ' . implode( ', ', array_keys( $row ) ) . ' ) VALUES ( ' . implode( ', ', $values ) . ' )';
    }

    /**
     * @param eZURLWildcard $wildcard
     * @return array
     */
    protected static function wildcardRow( eZURLWildcard $wildcard )
    {
        return array( 'id' => (int)$wildcard->attribute( 'id' ), 'source_url' => (string)$wildcard->attribute( 'source_url' ),
                      'destination_url' => (string)$wildcard->attribute( 'destination_url' ), 'type' => (int)$wildcard->attribute( 'type' ) );
    }

    /**
     * Whether two rows are the same (values compared as strings). Pure.
     *
     * @param array|null $a
     * @param array|null $b
     * @return bool
     */
    public static function sameRow( $a, $b )
    {
        if ( $a === null || $b === null )
            return $a === $b;
        $a = array_map( 'strval', $a );
        $b = array_map( 'strval', $b );
        ksort( $a );
        ksort( $b );
        return $a == $b;
    }

    /**
     * Whether the entry at an old address is still the one the plan saw (the columns both have). Pure.
     *
     * @param array|null $now
     * @param array|null $planned
     * @return bool
     */
    public static function sameEntry( $now, $planned )
    {
        if ( $now === null || $planned === null )
            return $now === $planned;
        $keys = array_intersect( array_keys( $now ), array_keys( $planned ) );
        foreach ( $keys as $key )
        {
            if ( is_scalar( $now[$key] ) && is_scalar( $planned[$key] ) && (string)$now[$key] !== (string)$planned[$key] )
                return false;
        }
        return true;
    }

    /**
     * @param array $item
     * @param string $result
     * @param string $message
     * @return array
     */
    protected static function result( array $item, $result, $message )
    {
        return array( 'index' => $item['index'], 'line' => $item['line'], 'old_url' => $item['old_url'], 'store' => $item['store'], 'result' => $result,
                      'message' => $message );
    }

    /**
     * Expires the caches of the stores that changed (the URL alias lookups have no cache of their own).
     *
     * @param array $stores store => true
     */
    protected static function expireCaches( array $stores )
    {
        if ( isset( $stores[expUrlMapPlan::STORE_WILDCARD] ) )
            eZURLWildcard::expireCache();
        if ( isset( $stores[expUrlMapPlan::STORE_REDIRECT] ) )
            expUrlRedirect::expireCache();
    }

    /** @return int */
    protected function userId()
    {
        if ( $this->options['user_id'] !== null )
            return (int)$this->options['user_id'];
        return class_exists( 'eZUser' ) ? (int)eZUser::currentUserID() : 0;
    }

    /**
     * @param string $name
     * @param array $data
     */
    protected function event( $name, array $data )
    {
        if ( is_callable( $this->options['audit'] ) )
        {
            call_user_func( $this->options['audit'], $name, $data );
            return;
        }
        try
        {
            if ( class_exists( 'expAudit' ) )
                expAudit::event( $name, $data );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'The audit event ' . $name . ' was not written: ' . $e->getMessage(), __METHOD__ );
        }
    }
}
