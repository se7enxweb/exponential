<?php
/**
 * File containing the expDataTableRelation class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Relations between data tables and content (doc/eng-us/features/6.0/data-tables.md, "Relations"): what a relation
 * column of a row points at (a content object, a node, a class, a user, a section or a row of another table), the
 * address of it in the administration, the rows that point at an object or a node (the "Data tables" tab of the
 * content view), and the references a row given to a writer may hold instead of an id:
 *
 *   @object:<remote id>     the content object with this remote id
 *   @node:<remote id>       the node with this remote id
 *   @path:<url alias>       the node at this address
 *
 * A relation is an entry of the catalogue's relations: column, kind, class, field, source.
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataTableRelation
{
    /** How many rows of one table the content view lists */
    const POINTING_LIMIT = 10;

    /**
     * What a value of a relation column points at, or null.
     *
     * @param array $relation
     * @param mixed $value
     * @return object|null eZContentObject, eZContentObjectTreeNode, eZContentClass, eZUser, eZSection or a persistent object
     */
    public static function target( array $relation, $value )
    {
        if ( $value === null || $value === '' || $value === 0 || $value === '0' )
            return null;
        try
        {
            switch ( $relation['kind'] )
            {
                case 'object':
                    return eZContentObject::fetch( (int)$value ) ?: null;
                case 'node':
                    return eZContentObjectTreeNode::fetch( (int)$value ) ?: null;
                case 'class':
                    return eZContentClass::fetch( (int)$value ) ?: null;
                case 'user':
                    return eZUser::fetch( (int)$value ) ?: null;
                case 'section':
                    return eZSection::fetch( (int)$value ) ?: null;
                case 'table':
                    $entry = $relation['class'] !== '' ? expDataTableCatalogue::instance()->entry( $relation['class'] ) : null;
                    if ( $entry === null || !class_exists( $entry['class'] ) )
                        return null;
                    $definition = call_user_func( array( $entry['class'], 'definition' ) );
                    return eZPersistentObject::fetchObject( $definition, null, array( $relation['field'] !== '' ? $relation['field'] : 'id' => $value ) );
            }
        }
        catch ( Throwable $e )
        {
            return null;
        }
        return null;
    }

    /**
     * The administration's address of what a relation value points at ('' when there is none).
     *
     * @param array $relation
     * @param mixed $value
     * @return string a module URL (without the siteaccess)
     */
    public static function url( array $relation, $value )
    {
        if ( $value === null || $value === '' || (string)$value === '0' )
            return '';
        $value = (string)$value;
        switch ( $relation['kind'] )
        {
            case 'object':
                $object = self::target( $relation, $value );
                return $object instanceof eZContentObject && $object->attribute( 'main_node_id' ) ? 'content/view/full/' . (int)$object->attribute( 'main_node_id' ) : '';
            case 'node':
                return ctype_digit( $value ) ? 'content/view/full/' . (int)$value : '';
            case 'class':
                return ctype_digit( $value ) ? 'class/view/' . (int)$value : '';
            case 'user':
                $object = ctype_digit( $value ) ? eZContentObject::fetch( (int)$value ) : null;
                return $object instanceof eZContentObject && $object->attribute( 'main_node_id' ) ? 'content/view/full/' . (int)$object->attribute( 'main_node_id' ) : '';
            case 'section':
                return ctype_digit( $value ) ? 'section/view/' . (int)$value : '';
            case 'table':
                return $relation['class'] !== '' ? 'datatable/rows/' . $relation['class'] : '';
        }
        return '';
    }

    /**
     * The data tables whose rows point at an object or a node, with their first rows.
     *
     * Only the tables of extensions and of other connections are asked (the kernel's own tables relate to content
     * everywhere and are not listed), and only through relations that are declared, foreign keys or set in the
     * settings, or inferred in an extension's table.
     *
     * @param int $objectID
     * @param int $nodeID
     * @param int $limit rows per table
     * @return array list of array( class, table, column, kind, count, rows (field => value), more )
     */
    public static function pointingAt( $objectID, $nodeID = 0, $limit = self::POINTING_LIMIT )
    {
        $found = array();
        $catalogue = expDataTableCatalogue::instance();
        $kernelTables = array();
        foreach ( $catalogue->classes() as $entry )
            if ( $entry['origin'] === 'kernel' && $entry['connection'] === '' )
                $kernelTables[$entry['table']] = true;
        $inferred = self::includeInferred();
        foreach ( $catalogue->classes() as $class => $entry )
        {
            // the kernel's tables, and an extension's class over a kernel table, relate to content everywhere
            if ( $entry['error'] !== '' || ( $entry['connection'] === '' && isset( $kernelTables[$entry['table']] ) ) || !class_exists( $class ) )
                continue;
            foreach ( $entry['relations'] as $relation )
            {
                if ( $relation['source'] === 'inferred' && !$inferred )
                    continue;
                $id = $relation['kind'] === 'object' ? (int)$objectID : ( $relation['kind'] === 'node' ? (int)$nodeID : 0 );
                if ( $id <= 0 )
                    continue;
                try
                {
                    $definition = call_user_func( array( $class, 'definition' ) );
                    // an extension whose tables were never installed: nothing points from there
                    if ( expKernelTableSchema::exists( $entry['table'], eZPersistentObject::dbFor( $definition ) ) !== true )
                        continue;
                    $conditions = array( $relation['column'] => $id );
                    $count = (int)eZPersistentObject::count( $definition, $conditions );
                    if ( $count === 0 )
                        continue;
                    $rows = eZPersistentObject::fetchObjectList( $definition, null, $conditions, null, array( 'offset' => 0, 'length' => (int)$limit ), false );
                    $found[] = array( 'class' => $class, 'table' => $entry['table'], 'column' => $relation['column'], 'kind' => $relation['kind'],
                                      'count' => $count, 'rows' => is_array( $rows ) ? $rows : array(), 'more' => $count > (int)$limit,
                                      'fields' => array_keys( $entry['fields'] ) );
                }
                catch ( Throwable $e )
                {
                    eZDebug::writeWarning( "$class.{$relation['column']}: " . $e->getMessage(), __METHOD__ );
                }
            }
        }
        return $found;
    }

    /**
     * Whether the content view also asks the relations inferred from column names
     * (datatable.ini [DataTableSettings] ContentTabInferred).
     *
     * @return bool
     */
    public static function includeInferred()
    {
        try
        {
            $ini = eZINI::instance( 'datatable.ini' );
            return $ini->hasVariable( 'DataTableSettings', 'ContentTabInferred' )
                   && strtolower( (string)$ini->variable( 'DataTableSettings', 'ContentTabInferred' ) ) === 'enabled';
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * Whether a value is a reference (@object:, @node:, @path:).
     *
     * @param mixed $value
     * @return bool
     */
    public static function isReference( $value )
    {
        return is_string( $value ) && preg_match( '/^@(object|node|path):./', $value ) === 1;
    }

    /**
     * The id a reference stands for for a relation of a kind (object id for object and user, node id for node).
     *
     * @param string $reference
     * @param string $kind object, node or user
     * @return int|null null when nothing has the reference
     */
    public static function resolveReference( $reference, $kind )
    {
        if ( !preg_match( '/^@(object|node|path):(.+)$/', (string)$reference, $m ) )
            return null;
        $node = null;
        $object = null;
        switch ( $m[1] )
        {
            case 'object':
                $object = eZContentObject::fetchByRemoteID( $m[2] );
                break;
            case 'node':
                $node = eZContentObjectTreeNode::fetchByRemoteID( $m[2] );
                break;
            case 'path':
                $nodeID = eZURLAliasML::fetchNodeIDByPath( trim( $m[2], '/' ) );
                $node = $nodeID ? eZContentObjectTreeNode::fetch( (int)$nodeID ) : null;
                break;
        }
        if ( $kind === 'node' )
        {
            if ( !$node && $object instanceof eZContentObject )
                return $object->attribute( 'main_node_id' ) ? (int)$object->attribute( 'main_node_id' ) : null;
            return $node instanceof eZContentObjectTreeNode ? (int)$node->attribute( 'node_id' ) : null;
        }
        if ( !$object && $node instanceof eZContentObjectTreeNode )
            return (int)$node->attribute( 'contentobject_id' );
        return $object instanceof eZContentObject ? (int)$object->attribute( 'id' ) : null;
    }
}
?>
