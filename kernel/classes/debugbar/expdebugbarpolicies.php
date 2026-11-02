<?php
/**
 * File containing the expDebugBarPolicies class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The policies of the current user's roles for the debug bar's User tab, loaded on demand (expdebugbar::policies):
 * the roles with how many policies each has and how they are assigned, and the policies one page at a time, for a
 * role, matching a search, or all of them. A user with hundreds of roles costs the same few queries: the policies,
 * their limitations and values of all roles in three (one more per 500 ids), the names of classes and sections once,
 * the names of nodes only for the rows of the page.
 */
class expDebugBarPolicies
{
    /** Rows of a page. */
    const PAGE = 200;

    /** Ids in one IN () of the policy queries. */
    const QUERY_CHUNK = 500;

    /**
     * The answer of expdebugbar::policies for a user.
     *
     * @param eZUser $user
     * @param int|null $roleID only this role (one of the user's), null: all
     * @param string $query only rows whose module, function, role, limitation or (resolved) value contains it
     * @param int $offset
     * @return array roles (id, name, count, assignments), total, matched, offset, limit, rows
     */
    public static function forUser( $user, $roleID = null, $query = '', $offset = 0 )
    {
        $assignments = expDebugBarUser::assignments( $user );
        $roles = array();
        foreach ( $assignments as $a )
        {
            if ( !isset( $roles[$a['role_id']] ) )
                $roles[$a['role_id']] = array( 'id' => $a['role_id'], 'name' => $a['role'], 'count' => 0, 'assignments' => array() );
            $roles[$a['role_id']]['assignments'][] = array( 'limit' => $a['limit_identifier'] === '' ? '' : $a['limit_identifier'] . ' ' . $a['limit_value'],
                                                            'from' => $a['direct'] ? '' : $a['from_name'], 'from_id' => $a['from_id'] );
        }
        $policies = $roles ? self::fetchPolicies( array_keys( $roles ) ) : array();
        foreach ( $policies as $p )
        {
            if ( isset( $roles[$p['role_id']] ) )
                $roles[$p['role_id']]['count']++;
        }
        $names = array();
        foreach ( $roles as $id => $role )
            $names[$id] = $role['name'];

        $resolver = new expDebugBarPolicyNames();
        $selected = self::select( $policies, $names, $roleID, $query, $resolver );
        $page = array_slice( $selected, max( 0, (int)$offset ), self::PAGE );
        $resolver->primeNodes( $page );
        $rows = array();
        foreach ( $page as $p )
            $rows[] = self::row( $p, $names, $resolver );

        uasort( $roles, function ( $a, $b ) { return strcasecmp( $a['name'], $b['name'] ) ?: $a['id'] <=> $b['id']; } );
        return array( 'roles' => array_values( $roles ), 'total' => count( $policies ), 'matched' => count( $selected ),
                      'offset' => max( 0, (int)$offset ), 'limit' => self::PAGE, 'rows' => $rows );
    }

    /**
     * The published policies of roles with their limitations: three queries, one more each per QUERY_CHUNK ids.
     *
     * @param int[] $roleIDs
     * @return array of array( id, role_id, module, function, limitations => array( identifier => values[] ) ),
     *         sorted by module, function, role
     */
    public static function fetchPolicies( array $roleIDs )
    {
        return self::fetchPoliciesWith( $roleIDs, function ( $definition, array $conditions ) {
            return eZPersistentObject::fetchObjectList( $definition, null, $conditions, null, null, false );
        } );
    }

    /**
     * fetchPolicies() with the query given: through the persistent objects of ezpolicy, ezpolicy_limitation and
     * ezpolicy_limitation_value, one plain IN () per table and QUERY_CHUNK ids, so it works on every database the
     * kernel speaks (MySQL, PostgreSQL, SQLite, Oracle, MongoDB) and with any number of roles.
     *
     * @param int[] $roleIDs
     * @param callable $fetch function( array $definition, array $conditions ): array of rows
     * @return array combine()
     */
    public static function fetchPoliciesWith( array $roleIDs, callable $fetch )
    {
        $chunked = function ( $definition, $column, array $ids, array $more = array() ) use ( $fetch ) {
            $rows = array();
            foreach ( array_chunk( $ids, self::QUERY_CHUNK ) as $chunk )
            {
                $found = $fetch( $definition, array( $column => array( $chunk ) ) + $more );
                if ( is_array( $found ) )
                    $rows = array_merge( $rows, $found );
            }
            return $rows;
        };
        $roleIDs = array_values( array_unique( array_map( 'intval', $roleIDs ) ) );
        if ( !$roleIDs )
            return array();
        // published policies only, as eZRole::policyList(): a role being edited has copies with original_id set
        $rows = $chunked( eZPolicy::definition(), 'role_id', $roleIDs, array( 'original_id' => 0 ) );
        if ( !$rows )
            return array();
        $limitations = array();
        foreach ( $chunked( eZPolicyLimitation::definition(), 'policy_id', array_map( 'intval', array_column( $rows, 'id' ) ) ) as $l )
            $limitations[(int)$l['id']] = $l;
        $values = array();
        if ( $limitations )
        {
            foreach ( $chunked( eZPolicyLimitationValue::definition(), 'limitation_id', array_keys( $limitations ) ) as $v )
            {
                $l = isset( $limitations[(int)$v['limitation_id']] ) ? $limitations[(int)$v['limitation_id']] : null;
                if ( $l !== null )
                    $values[] = array( 'policy_id' => $l['policy_id'], 'identifier' => $l['identifier'], 'value' => $v['value'] );
            }
        }
        return self::combine( $rows, $values );
    }

    /**
     * The policy rows and their limitation values as one list, sorted by module, function and role id.
     *
     * @param array $rows id, role_id, module_name, function_name
     * @param array $values policy_id, identifier, value
     * @return array
     */
    public static function combine( array $rows, array $values )
    {
        $limitations = array();
        foreach ( $values as $v )
            $limitations[(int)$v['policy_id']][(string)$v['identifier']][] = (string)$v['value'];
        $policies = array();
        foreach ( $rows as $r )
        {
            $id = (int)$r['id'];
            $policies[] = array( 'id' => $id, 'role_id' => (int)$r['role_id'], 'module' => (string)$r['module_name'],
                                 'function' => (string)$r['function_name'],
                                 'limitations' => isset( $limitations[$id] ) ? $limitations[$id] : array() );
        }
        usort( $policies, function ( $a, $b ) {
            return strcmp( $a['module'], $b['module'] ) ?: strcmp( $a['function'], $b['function'] ) ?: $a['role_id'] <=> $b['role_id'] ?: $a['id'] <=> $b['id'];
        } );
        return $policies;
    }

    /**
     * The policies of a role, or matching a search (module, function, role name, limitation identifier, its values
     * and the names of classes, sections and siteaccesses), or all. A search "module/function" also finds the policies
     * that grant it with a wildcard: "content/read" finds the policies of every function of content and of every
     * module as well.
     *
     * @param array $policies combine()
     * @param array $roleNames role id => name
     * @param int|null $roleID
     * @param string $query
     * @param expDebugBarPolicyNames $resolver
     * @return array
     */
    public static function select( array $policies, array $roleNames, $roleID, $query, $resolver )
    {
        $query = function_exists( 'mb_strtolower' ) ? mb_strtolower( trim( (string)$query ), 'UTF-8' ) : strtolower( trim( (string)$query ) );
        $grant = self::grantQuery( $query );
        $out = array();
        foreach ( $policies as $p )
        {
            if ( $roleID !== null && $p['role_id'] !== (int)$roleID )
                continue;
            if ( $query !== '' && !self::grants( $p, $grant ) && strpos( self::searchText( $p, $roleNames, $resolver ), $query ) === false )
                continue;
            $out[] = $p;
        }
        return $out;
    }

    /**
     * A search that names exactly a module and a function ("content/read", "content/*"), lower case, as
     * array( module, function ); null for any other search.
     */
    protected static function grantQuery( $query )
    {
        return preg_match( '#^([a-z0-9_]+|\*)/([a-z0-9_]+|\*)$#', $query, $m ) ? array( $m[1], $m[2] ) : null;
    }

    /**
     * Whether a policy grants the module and function a search names: its own, or every module or function (*), which
     * grants that one as well. "content/*" asks for the policies of every function of content.
     */
    protected static function grants( array $p, $grant )
    {
        if ( $grant === null )
            return false;
        list( $module, $function ) = $grant;
        $pm = strtolower( $p['module'] );
        $pf = strtolower( $p['function'] );
        return ( $module === '*' || $pm === '*' || $pm === $module ) && ( $function === '*' || $pf === '*' || $pf === $function );
    }

    /** What a search looks in, lower case. */
    protected static function searchText( array $p, array $roleNames, $resolver )
    {
        $text = $p['module'] . '/' . $p['function'] . ' ' . ( isset( $roleNames[$p['role_id']] ) ? $roleNames[$p['role_id']] : '' );
        foreach ( $p['limitations'] as $identifier => $values )
        {
            $text .= ' ' . $identifier;
            foreach ( $values as $value )
                $text .= ' ' . $value . ' ' . $resolver->cheapName( $identifier, $value );
        }
        return function_exists( 'mb_strtolower' ) ? mb_strtolower( $text, 'UTF-8' ) : strtolower( $text );
    }

    /**
     * A row as the tab shows it: module, function, the limitations with readable values, the role.
     *
     * @return array module, function, limitations (array of identifier, values[]), role_id, role
     */
    public static function row( array $p, array $roleNames, $resolver )
    {
        $limitations = array();
        foreach ( $p['limitations'] as $identifier => $values )
        {
            $shown = array();
            foreach ( $values as $value )
            {
                $name = $resolver->name( $identifier, $value );
                $shown[] = $name === '' || $name === $value ? $value : $name . ' (' . $value . ')';
            }
            $limitations[] = array( 'identifier' => $identifier, 'values' => $shown );
        }
        return array( 'module' => $p['module'], 'function' => $p['function'], 'limitations' => $limitations,
                      'role_id' => $p['role_id'], 'role' => isset( $roleNames[$p['role_id']] ) ? $roleNames[$p['role_id']] : '' );
    }
}

/**
 * Names for limitation values: classes and sections by id, siteaccesses by their crc32, nodes and subtrees by node id
 * or path. Classes, sections and siteaccesses are read once and also searched (cheapName()); nodes only for the rows
 * shown (name()).
 */
class expDebugBarPolicyNames
{
    protected $classes = null;
    protected $sections = null;
    protected $siteAccesses = null;
    protected $nodes = array();

    /**
     * Reads the names of the nodes the limitations of these policies name (Node, Subtree, User_Subtree), with one
     * query per QUERY_CHUNK nodes, so name() does not ask once per value.
     *
     * @param array $policies expDebugBarPolicies::combine() entries
     */
    public function primeNodes( array $policies )
    {
        $ids = array();
        foreach ( $policies as $p )
        {
            foreach ( $p['limitations'] as $identifier => $values )
            {
                foreach ( $values as $value )
                {
                    $id = self::nodeID( $identifier, $value );
                    if ( $id > 0 && !array_key_exists( $id, $this->nodes ) )
                        $ids[$id] = true;
                }
            }
        }
        if ( !$ids || !class_exists( 'eZContentObjectTreeNode' ) )
            return;
        try
        {
            foreach ( array_chunk( array_keys( $ids ), expDebugBarPolicies::QUERY_CHUNK ) as $chunk )
            {
                $rows = eZContentObjectTreeNode::fetch( $chunk, false, false );
                if ( is_array( $rows ) && isset( $rows['node_id'] ) )
                    $rows = array( $rows );
                foreach ( is_array( $rows ) ? $rows : array() as $row )
                {
                    if ( isset( $row['node_id'] ) )
                        $this->nodes[(int)$row['node_id']] = isset( $row['name'] ) ? (string)$row['name'] : '';
                }
            }
        }
        catch ( Throwable $e )
        {
        }
        // nodes that are gone: no name, and no query of their own later
        foreach ( array_keys( $ids ) as $id )
        {
            if ( !array_key_exists( $id, $this->nodes ) )
                $this->nodes[$id] = '';
        }
    }

    /** The node id a Node, Subtree or User_Subtree value names, 0 for any other limitation. */
    protected static function nodeID( $identifier, $value )
    {
        if ( $identifier === 'Node' )
            return (int)$value;
        if ( $identifier === 'Subtree' || $identifier === 'User_Subtree' )
            return (int)basename( rtrim( (string)$value, '/' ) );
        return 0;
    }

    /** A name the search may use: classes, sections, siteaccesses; '' for the rest. */
    public function cheapName( $identifier, $value )
    {
        switch ( $identifier )
        {
            case 'Class':
            case 'ParentClass':
                return $this->lookup( 'classes', $value );
            case 'Section':
            case 'NewSection':
            case 'UserSection':
                return $this->lookup( 'sections', $value );
            case 'SiteAccess':
                return $this->lookup( 'siteAccesses', $value );
        }
        return '';
    }

    /** The name of a value as the tab shows it, '' when there is none. */
    public function name( $identifier, $value )
    {
        if ( $identifier === 'Node' || $identifier === 'Subtree' || $identifier === 'User_Subtree' )
        {
            $nodeID = self::nodeID( $identifier, $value );
            if ( $nodeID <= 0 )
                return '';
            if ( !array_key_exists( $nodeID, $this->nodes ) )
            {
                $node = class_exists( 'eZContentObjectTreeNode' ) ? eZContentObjectTreeNode::fetch( $nodeID, false, false ) : null;
                $this->nodes[$nodeID] = is_array( $node ) && isset( $node['name'] ) ? (string)$node['name'] : '';
            }
            return $this->nodes[$nodeID];
        }
        return $this->cheapName( $identifier, $value );
    }

    protected function lookup( $map, $value )
    {
        if ( $this->$map === null )
            $this->$map = $this->load( $map );
        return isset( $this->{$map}[(string)$value] ) ? $this->{$map}[(string)$value] : '';
    }

    /** id => name, read once; empty when the kernel cannot be asked. */
    protected function load( $map )
    {
        $names = array();
        try
        {
            if ( $map === 'classes' && class_exists( 'eZContentClass' ) )
            {
                foreach ( (array)eZContentClass::fetchList( eZContentClass::VERSION_STATUS_DEFINED, true ) as $class )
                    $names[(string)$class->attribute( 'id' )] = (string)$class->attribute( 'name' );
            }
            else if ( $map === 'sections' && class_exists( 'eZSection' ) )
            {
                foreach ( (array)eZSection::fetchList() as $section )
                    $names[(string)$section->attribute( 'id' )] = (string)$section->attribute( 'name' );
            }
            else if ( $map === 'siteAccesses' && class_exists( 'eZSiteAccess' ) )
            {
                foreach ( (array)eZSiteAccess::siteAccessList() as $item )
                    $names[(string)$item['id']] = (string)$item['name'];
            }
        }
        catch ( Throwable $e )
        {
        }
        return $names;
    }
}
