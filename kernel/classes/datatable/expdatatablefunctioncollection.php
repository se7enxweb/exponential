<?php
/**
 * File containing the expDataTableFunctionCollection class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The template fetch functions of the datatable module (kernel/datatable/function_definition.php,
 * doc/eng-us/features/6.0/data-tables.md, "In templates"):
 *
 *   {def $rows = fetch( 'datatable', 'list', hash( 'class', 'myArchiveRow', 'conditions', hash( 'state', 'open' ),
 *                                                  'sort', array( 'created', false() ), 'offset', 0, 'limit', 20 ) )}
 *   {fetch( 'datatable', 'count', hash( 'class', 'myArchiveRow' ) )}
 *   {fetch( 'datatable', 'row', hash( 'class', 'myArchiveRow', 'key', hash( 'id', 42 ) ) )}
 *   {fetch( 'datatable', 'pointing_at', hash( 'object_id', $node.contentobject_id, 'node_id', $node.node_id ) )}
 *   {fetch( 'datatable', 'can', hash( 'function', 'read' ) )}
 *
 * list, count and row read classes of extensions and of named connections; a kernel class only for a user with
 * datatable/read (templates of a site have their own fetch functions for kernel content). Conditions are equality
 * on fields of the class only.
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataTableFunctionCollection
{
    /** The most rows one list returns */
    const MAX_LIMIT = 500;

    /**
     * @param string $class
     * @param array|false $conditions
     * @param array|false $sort array( field, ascending )
     * @param int $offset
     * @param int $limit
     * @return array result: list of persistent objects
     */
    public static function fetchList( $class, $conditions = false, $sort = false, $offset = 0, $limit = 50 )
    {
        $definition = self::definitionOf( $class );
        if ( $definition === null )
            return array( 'result' => array() );
        $sorts = null;
        if ( is_array( $sort ) && isset( $sort[0] ) && isset( $definition['fields'][$sort[0]] ) )
            $sorts = array( $sort[0] => !isset( $sort[1] ) || $sort[1] ? 'asc' : 'desc' );
        $limit = max( 1, min( self::MAX_LIMIT, (int)$limit ) );
        $list = eZPersistentObject::fetchObjectList( $definition, null, self::conditions( $definition, $conditions ), $sorts,
                                                     array( 'offset' => max( 0, (int)$offset ), 'length' => $limit ) );
        return array( 'result' => is_array( $list ) ? $list : array() );
    }

    /**
     * @param string $class
     * @param array|false $conditions
     * @return array result: int
     */
    public static function fetchCount( $class, $conditions = false )
    {
        $definition = self::definitionOf( $class );
        if ( $definition === null )
            return array( 'result' => 0 );
        return array( 'result' => (int)eZPersistentObject::count( $definition, self::conditions( $definition, $conditions ) ) );
    }

    /**
     * @param string $class
     * @param array $key field => value of the class's key
     * @return array result: the object or false
     */
    public static function fetchRow( $class, $key )
    {
        $definition = self::definitionOf( $class );
        if ( $definition === null || !is_array( $key ) || !$key )
            return array( 'result' => false );
        $conditions = self::conditions( $definition, $key );
        if ( count( $conditions ) !== count( $key ) )
            return array( 'result' => false );
        return array( 'result' => eZPersistentObject::fetchObject( $definition, null, $conditions ) ?: false );
    }

    /**
     * @param int $objectID
     * @param int $nodeID
     * @return array result: expDataTableRelation::pointingAt()
     */
    public static function fetchPointingAt( $objectID, $nodeID = 0 )
    {
        if ( !self::may( 'read' ) )
            return array( 'result' => array() );
        return array( 'result' => expDataTableRelation::pointingAt( (int)$objectID, (int)$nodeID ) );
    }

    /**
     * @param string $function read, manage, connections, sync
     * @return array result: bool
     */
    public static function fetchCan( $function )
    {
        return array( 'result' => in_array( $function, array( 'read', 'manage', 'connections', 'sync' ), true ) && self::may( $function ) );
    }

    /**
     * Whether the current user has a policy function of the module.
     *
     * @param string $function
     * @return bool
     */
    public static function may( $function )
    {
        $access = eZUser::currentUser()->hasAccessTo( 'datatable', (string)$function );
        return $access['accessWord'] !== 'no';
    }

    /**
     * The definition of a class a template may read, or null.
     *
     * @param string $class
     * @return array|null
     */
    protected static function definitionOf( $class )
    {
        $entry = is_string( $class ) && preg_match( '/^[A-Za-z_][A-Za-z0-9_]{0,127}$/', $class ) ? expDataTableCatalogue::instance()->entry( $class ) : null;
        if ( $entry === null || $entry['error'] !== '' || $entry['table'] === '' || !class_exists( $entry['class'] ) )
            return null;
        if ( $entry['origin'] === 'kernel' && $entry['connection'] === '' && !self::may( 'read' ) )
            return null;
        return call_user_func( array( $entry['class'], 'definition' ) );
    }

    /**
     * Equality conditions on fields of the definition only.
     *
     * @param array $definition
     * @param mixed $conditions
     * @return array|null
     */
    protected static function conditions( array $definition, $conditions )
    {
        if ( !is_array( $conditions ) || !$conditions )
            return null;
        $clean = array();
        foreach ( $conditions as $field => $value )
            if ( is_string( $field ) && isset( $definition['fields'][$field] ) && is_scalar( $value ) )
                $clean[$field] = $value;
        return $clean ?: null;
    }
}
?>
