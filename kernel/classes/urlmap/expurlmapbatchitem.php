<?php
/**
 * File containing the expUrlMapBatchItem class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One entry an import created or changed: a row of exp_urlmap_batch_item. kind is the store (alias, wildcard,
 * redirect), target_table and target_key the row (a URL alias element is keyed "parent:text_md5", a wildcard and a
 * redirect by id), action created or updated, and before_image and after_image the row as JSON (before is null for a
 * created one). The items of one alias row are each element of its path the import wrote (a placeholder for a
 * missing parent too).
 *
 * States: applied, undone, kept (left as it was at the undo: it had changed after the import).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUrlMapBatchItem extends eZPersistentObject
{
    const STATE_APPLIED = 'applied';
    const STATE_UNDONE = 'undone';
    const STATE_KEPT = 'kept';

    public static function definition()
    {
        static $definition = null;
        if ( $definition === null )
        {
            $int = array( 'datatype' => 'integer', 'default' => 0, 'required' => true );
            $string = array( 'datatype' => 'string', 'default' => '', 'required' => true );
            $fields = array( 'id' => array( 'name' => 'ID' ) + $int );
            foreach ( array( 'batch_id', 'seq', 'line' ) as $name )
                $fields[$name] = array( 'name' => $name ) + $int;
            foreach ( array( 'kind', 'target_table', 'target_key', 'action', 'state', 'old_url' ) as $name )
                $fields[$name] = array( 'name' => $name ) + $string;
            foreach ( array( 'before_image', 'after_image' ) as $name )
                $fields[$name] = array( 'name' => $name, 'datatype' => 'text', 'default' => '', 'required' => false );
            $definition = array(
                'fields' => $fields,
                'keys' => array( 'id' ),
                'increment_key' => 'id',
                'function_attributes' => array( 'before' => 'before', 'after' => 'after' ),
                'sort' => array( 'seq' => 'asc' ),
                'class_name' => 'expUrlMapBatchItem',
                'name' => expUrlMapBatch::ITEM_TABLE,
            );
        }
        return $definition;
    }

    /** @return array|null the row before the import, null when the import created it */
    public function before()
    {
        return self::decode( $this->attribute( 'before_image' ) );
    }

    /** @return array|null the row as the import left it, null when the import removed it */
    public function after()
    {
        return self::decode( $this->attribute( 'after_image' ) );
    }

    /** @return array */
    public function asArray()
    {
        $out = array();
        foreach ( array_keys( self::definition()['fields'] ) as $name )
            $out[$name] = $this->attribute( $name );
        foreach ( array( 'id', 'batch_id', 'seq', 'line' ) as $name )
            $out[$name] = (int)$out[$name];
        $out['before_image'] = $this->before();
        $out['after_image'] = $this->after();
        return $out;
    }

    /**
     * A row as JSON for an image, '' for none. Pure.
     *
     * @param array|null $row
     * @return string
     */
    public static function encode( $row )
    {
        return $row === null ? '' : (string)json_encode( $row, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR );
    }

    /**
     * @param mixed $json
     * @return array|null
     */
    public static function decode( $json )
    {
        if ( !is_string( $json ) || $json === '' )
            return null;
        $decoded = json_decode( $json, true );
        return is_array( $decoded ) ? $decoded : null;
    }
}
