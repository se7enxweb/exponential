<?php
/**
 * File containing the expUrlMapBatch class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One import of a URL map: a row of exp_urlmap_batch (who, when, from which file, with which defaults, the counts and
 * the state), with its items in exp_urlmap_batch_item: one per entry the import created or changed, with the entry
 * as it was before (null when it was created) and after, so that expUrlMapImporter::undo() can put back exactly what
 * was there.
 *
 * States: applied, undone, partly-undone (an entry changed after the import was kept).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUrlMapBatch extends eZPersistentObject
{
    const TABLE = 'exp_urlmap_batch';
    const ITEM_TABLE = 'exp_urlmap_batch_item';

    const STATE_APPLIED = 'applied';
    const STATE_UNDONE = 'undone';
    const STATE_PARTLY_UNDONE = 'partly-undone';

    public static function definition()
    {
        static $definition = null;
        if ( $definition === null )
        {
            $int = array( 'datatype' => 'integer', 'default' => 0, 'required' => true );
            $string = array( 'datatype' => 'string', 'default' => '', 'required' => true );
            $fields = array( 'id' => array( 'name' => 'ID' ) + $int );
            foreach ( array( 'created', 'user_id', 'rows_total', 'created_count', 'updated_count', 'skipped_count', 'failed_count', 'undone', 'undone_by' ) as $name )
                $fields[$name] = array( 'name' => $name ) + $int;
            foreach ( array( 'source_name', 'format', 'siteaccess', 'language', 'on_conflict', 'state' ) as $name )
                $fields[$name] = array( 'name' => $name ) + $string;
            $fields['summary'] = array( 'name' => 'Summary', 'datatype' => 'text', 'default' => '', 'required' => false );
            $definition = array(
                'fields' => $fields,
                'keys' => array( 'id' ),
                'increment_key' => 'id',
                'function_attributes' => array( 'summary_array' => 'summaryArray', 'items' => 'items', 'user' => 'user' ),
                'sort' => array( 'id' => 'desc' ),
                'class_name' => 'expUrlMapBatch',
                'name' => self::TABLE,
            );
        }
        return $definition;
    }

    /**
     * Whether both tables exist.
     *
     * @param eZDBInterface|null $db
     * @return bool
     */
    public static function tablesExist( $db = null )
    {
        return expKernelTableSchema::exists( self::TABLE, $db, true ) === true && expKernelTableSchema::exists( self::ITEM_TABLE, $db, true ) === true;
    }

    /**
     * @param int $id
     * @return expUrlMapBatch|null
     */
    public static function fetch( $id )
    {
        $object = eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => (int)$id ), true );
        return $object instanceof self ? $object : null;
    }

    /**
     * The batches, newest first.
     *
     * @param int $offset
     * @param int $limit 0 for all
     * @return expUrlMapBatch[]
     */
    public static function fetchList( $offset = 0, $limit = 0 )
    {
        $list = eZPersistentObject::fetchObjectList( self::definition(), null, null, array( 'id' => 'desc' ),
                                                     $limit > 0 ? array( 'offset' => (int)$offset, 'length' => (int)$limit ) : null, true );
        return is_array( $list ) ? $list : array();
    }

    /** @return int */
    public static function fetchListCount()
    {
        return (int)eZPersistentObject::count( self::definition() );
    }

    /** @return array the summary (counts by plan state, options), decoded */
    public function summaryArray()
    {
        $decoded = json_decode( (string)$this->attribute( 'summary' ), true );
        return is_array( $decoded ) ? $decoded : array();
    }

    /** @return expUrlMapBatchItem[] the items, in the order they were applied */
    public function items()
    {
        $list = eZPersistentObject::fetchObjectList( expUrlMapBatchItem::definition(), null, array( 'batch_id' => (int)$this->attribute( 'id' ) ),
                                                     array( 'seq' => 'asc' ), null, true );
        return is_array( $list ) ? $list : array();
    }

    /**
     * A page of the items, in the order they were applied (the administration's details).
     *
     * @param int $offset
     * @param int $limit
     * @return expUrlMapBatchItem[]
     */
    public function itemsPage( $offset, $limit )
    {
        $list = eZPersistentObject::fetchObjectList( expUrlMapBatchItem::definition(), null, array( 'batch_id' => (int)$this->attribute( 'id' ) ),
                                                     array( 'seq' => 'asc' ), array( 'offset' => max( 0, (int)$offset ), 'length' => max( 1, (int)$limit ) ), true );
        return is_array( $list ) ? $list : array();
    }

    /** @return int how many items the batch has */
    public function itemCount()
    {
        return (int)eZPersistentObject::count( expUrlMapBatchItem::definition(), array( 'batch_id' => (int)$this->attribute( 'id' ) ) );
    }

    /** @return eZUser|null */
    public function user()
    {
        return eZUser::fetch( (int)$this->attribute( 'user_id' ) );
    }

    /**
     * The batch as an array (console JSON, the administration's history).
     *
     * @param bool $withItems
     * @return array
     */
    public function asArray( $withItems = false )
    {
        $out = array();
        foreach ( array_keys( self::definition()['fields'] ) as $name )
            $out[$name] = $name === 'summary' ? $this->summaryArray() : $this->attribute( $name );
        foreach ( array( 'id', 'created', 'user_id', 'rows_total', 'created_count', 'updated_count', 'skipped_count', 'failed_count', 'undone', 'undone_by' ) as $name )
            $out[$name] = (int)$out[$name];
        if ( $withItems )
            $out['items'] = array_map( function ( expUrlMapBatchItem $item ) { return $item->asArray(); }, $this->items() );
        return $out;
    }
}
