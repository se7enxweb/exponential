<?php
/**
 * File containing the expRolePage class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the role pages (role/list, role/view, role/edit) work out besides the role itself: the rows of the role list
 * with their search and order, what each role on a page holds (its policies, whether one gives full access or lets
 * its users change roles), the policies of a page in words, how many users a role reaches and whether the draft of
 * the role editor differs from the saved role.
 *
 * The search, the order and the paging of the list rows are plain functions of arrays (filterRows(), sortRows(),
 * pageRows()) and are tested without a database; the rest reads the database and is used by the views.
 */
class expRolePage
{
    /** The orders of the role list: the name the address carries => whether it needs the counts of every role */
    const SORTS = array( 'name' => false, 'id' => false, 'policies' => true, 'assigned' => true );

    /** Up to this many roles the list can be sorted by the number of policies and assignments; past it by name */
    const COUNT_SORT_LIMIT = 500;

    /** Up to this many policies the role editor compares its draft with the saved role */
    const DRAFT_COMPARE_LIMIT = 2000;

    /** Up to this many assigned user groups the role page counts the users they hold */
    const AFFECTED_GROUP_LIMIT = 200;

    /**
     * The search of the role list as the address and the field carry it: trimmed, inner spaces folded, without
     * control characters, at most 100 characters.
     *
     * @param mixed $search
     * @return string
     */
    public static function normaliseSearch( $search )
    {
        if ( !is_string( $search ) )
            return '';
        $search = preg_replace( '/[\x00-\x1F\x7F]+/u', ' ', $search );
        $search = trim( preg_replace( '/\s+/u', ' ', (string)$search ) );
        if ( function_exists( 'mb_substr' ) )
            return mb_substr( $search, 0, 100, 'UTF-8' );
        return substr( $search, 0, 100 );
    }

    /**
     * The order of the role list: one of SORTS, name by default.
     *
     * @param mixed $sort
     * @return string
     */
    public static function normaliseSort( $sort )
    {
        return is_string( $sort ) && array_key_exists( $sort, self::SORTS ) ? $sort : 'name';
    }

    /**
     * The direction of an order: 'desc' or 'asc'.
     *
     * @param mixed $dir
     * @return string
     */
    public static function normaliseDirection( $dir )
    {
        return is_string( $dir ) && strtolower( $dir ) === 'desc' ? 'desc' : 'asc';
    }

    /**
     * The rows whose name contains $search (upper and lower case alike) or whose id is $search.
     *
     * @param array $rows arrays with id and name
     * @param string $search
     * @return array
     */
    public static function filterRows( array $rows, $search )
    {
        $search = self::normaliseSearch( $search );
        if ( $search === '' )
            return array_values( $rows );
        $needle = self::lower( $search );
        $keep = array();
        foreach ( $rows as $row )
        {
            if ( strpos( self::lower( (string)$row['name'] ), $needle ) !== false || (string)(int)$row['id'] === $search )
                $keep[] = $row;
        }
        return $keep;
    }

    /**
     * The rows in the order $sort, $dir. Names compare without regard to case; ties go by id, ascending, so paging
     * never repeats or drops a row.
     *
     * @param array $rows arrays with id, name, and for the count orders policies and assigned
     * @param string $sort one of SORTS
     * @param string $dir asc or desc
     * @return array
     */
    public static function sortRows( array $rows, $sort, $dir )
    {
        $sort = self::normaliseSort( $sort );
        $sign = self::normaliseDirection( $dir ) === 'desc' ? -1 : 1;
        // A count order puts equal counts in name order, A to Z, whichever way it runs
        usort( $rows, function ( $a, $b ) use ( $sort, $sign )
        {
            $byName = strcmp( expRolePage::lower( $a['name'] ), expRolePage::lower( $b['name'] ) );
            $byID = (int)$a['id'] <=> (int)$b['id'];
            switch ( $sort )
            {
                case 'id':
                    return $byID * $sign;
                case 'policies':
                case 'assigned':
                    $c = (int)( isset( $a[$sort] ) ? $a[$sort] : 0 ) <=> (int)( isset( $b[$sort] ) ? $b[$sort] : 0 );
                    if ( $c !== 0 )
                        return $c * $sign;
                    return $byName !== 0 ? $byName : $byID;
                default:
                    if ( $byName !== 0 )
                        return ( $byName < 0 ? -1 : 1 ) * $sign;
                    return $byID;
            }
        } );
        return $rows;
    }

    /**
     * One page of rows. An offset past the end gives the last page.
     *
     * @param array $rows
     * @param int $offset
     * @param int $limit
     * @return array rows, offset (the one used), count
     */
    public static function pageRows( array $rows, $offset, $limit )
    {
        $count = count( $rows );
        $limit = max( 1, (int)$limit );
        $offset = max( 0, (int)$offset );
        if ( $offset >= $count )
            $offset = $count > 0 ? (int)( floor( ( $count - 1 ) / $limit ) * $limit ) : 0;
        return array( 'rows' => array_slice( $rows, $offset, $limit ), 'offset' => $offset, 'count' => $count );
    }

    /**
     * @param string $text
     * @return string
     */
    public static function lower( $text )
    {
        return function_exists( 'mb_strtolower' ) ? mb_strtolower( (string)$text, 'UTF-8' ) : strtolower( (string)$text );
    }

    /**
     * One page of the role list: the published roles (not the drafts of the role editor), searched, ordered and
     * paged. The id and name of every role are read in one query; the counts only for the page, unless the list is
     * ordered by a count, which needs the counts of every role (offered up to COUNT_SORT_LIMIT roles).
     *
     * @param string $search
     * @param string $sort
     * @param string $dir
     * @param int $offset
     * @param int $limit
     * @return array roles (eZRole objects of the page, in order), summaries (role id => see summaries()), count (rows
     *         that match), total (every role), offset, sort, dir, search, count_sort (whether the count orders are
     *         offered)
     */
    public static function listPage( $search, $sort, $dir, $offset, $limit )
    {
        $search = self::normaliseSearch( $search );
        $sort = self::normaliseSort( $sort );
        $dir = self::normaliseDirection( $dir );

        $rows = eZPersistentObject::fetchObjectList( eZRole::definition(), array( 'id', 'name' ),
                                                     array( 'version' => 0, 'is_new' => 0 ), array( 'id' => 'asc' ),
                                                     null, false );
        $all = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
            $all[] = array( 'id' => (int)$row['id'], 'name' => (string)$row['name'] );
        $total = count( $all );
        $countSort = $total <= self::COUNT_SORT_LIMIT;
        if ( self::SORTS[$sort] && !$countSort )
            $sort = 'name';

        $rows = self::filterRows( $all, $search );
        if ( self::SORTS[$sort] )
        {
            $ids = array();
            foreach ( $rows as $row )
                $ids[] = $row['id'];
            $summaries = self::summaries( $ids );
            foreach ( $rows as $key => $row )
            {
                $rows[$key]['policies'] = $summaries[$row['id']]['policies'];
                $rows[$key]['assigned'] = $summaries[$row['id']]['assigned'];
            }
        }
        $rows = self::sortRows( $rows, $sort, $dir );
        $page = self::pageRows( $rows, $offset, $limit );

        $roles = array();
        $ids = array();
        foreach ( $page['rows'] as $row )
        {
            $role = eZRole::fetch( $row['id'] );
            if ( $role instanceof eZRole )
            {
                $roles[] = $role;
                $ids[] = $row['id'];
            }
        }
        return array( 'roles' => $roles, 'summaries' => self::summaries( $ids ), 'count' => $page['count'],
                      'total' => $total, 'offset' => $page['offset'], 'sort' => $sort, 'dir' => $dir,
                      'search' => $search, 'count_sort' => $countSort );
    }

    /**
     * What each role holds: role id => policies (how many), assigned (users and groups it is assigned to),
     * full_access (a policy over every module), manages_roles (a policy over the role module or every module).
     * Counted by the database: three counts per role and one grouped count for the assignments.
     *
     * @param int[] $roleIDs
     * @return array
     */
    public static function summaries( array $roleIDs )
    {
        $ids = array();
        foreach ( $roleIDs as $id )
        {
            if ( (int)$id > 0 )
                $ids[(int)$id] = true;
        }
        if ( !$ids )
            return array();
        $assigned = eZRole::assignmentCounts( array_keys( $ids ) );
        $summaries = array();
        foreach ( array_keys( $ids ) as $id )
        {
            $policies = (int)eZPersistentObject::count( eZPolicy::definition(), array( 'role_id' => $id, 'original_id' => 0 ) );
            $full = $policies > 0 ? (int)eZPersistentObject::count( eZPolicy::definition(),
                                    array( 'role_id' => $id, 'original_id' => 0, 'module_name' => '*' ) ) : 0;
            $roleModule = $policies > 0 ? (int)eZPersistentObject::count( eZPolicy::definition(),
                                    array( 'role_id' => $id, 'original_id' => 0, 'module_name' => 'role' ) ) : 0;
            $summaries[$id] = array( 'policies' => $policies,
                                     'assigned' => isset( $assigned[$id] ) ? (int)$assigned[$id] : 0,
                                     'full_access' => $full > 0,
                                     'manages_roles' => $full + $roleModule > 0 );
        }
        return $summaries;
    }

    /**
     * A policy as expRolePolicySentence takes it: module, function and its limitations with their label, the names
     * of their values and whether no handler evaluates them.
     *
     * @param eZPolicy $policy
     * @return array
     */
    public static function policyData( eZPolicy $policy )
    {
        $limitations = array();
        foreach ( (array)$policy->attribute( 'limitations' ) as $limitation )
        {
            if ( !$limitation instanceof eZPolicyLimitation )
                continue;
            $values = array();
            foreach ( (array)$limitation->attribute( 'values_as_array_with_names' ) as $value )
            {
                if ( is_array( $value ) && isset( $value['Name'] ) )
                    $values[] = (string)$value['Name'];
            }
            $limitations[] = array( 'identifier' => (string)$limitation->attribute( 'identifier' ),
                                    'label' => (string)$limitation->attribute( 'label' ),
                                    'values' => $values,
                                    'denies' => (bool)$limitation->attribute( 'denies_without_handler' ) );
        }
        return array( 'id' => (int)$policy->attribute( 'id' ),
                      'module' => (string)$policy->attribute( 'module_name' ),
                      'function' => (string)$policy->attribute( 'function_name' ),
                      'limitations' => $limitations );
    }

    /**
     * The policies of a page in words: policy id => what expRolePolicySentence::describe() returns.
     *
     * @param eZPolicy[] $policies
     * @param expRolePolicySentence|null $sentence
     * @return array
     */
    public static function describePolicies( array $policies, $sentence = null )
    {
        $sentence = $sentence instanceof expRolePolicySentence ? $sentence : new expRolePolicySentence();
        $described = array();
        foreach ( $policies as $policy )
        {
            if ( $policy instanceof eZPolicy )
                $described[(int)$policy->attribute( 'id' )] = $sentence->describe( self::policyData( $policy ) );
        }
        return $described;
    }

    /**
     * How many people a role reaches: the users it is assigned to directly, the user groups, and every user those
     * groups hold, counted once each (a user in two groups of the role counts once). Users of assignments with a
     * subtree or section limitation count too: the role applies to them, inside its limitation. Null for the users
     * when the role is assigned to more than AFFECTED_GROUP_LIMIT groups or the database is not SQL.
     *
     * @param eZRole $role
     * @return array users_direct, groups, users_total (int|null), limited (assignments with a limitation)
     */
    public static function affected( eZRole $role )
    {
        $db = eZDB::instance();
        $result = array( 'users_direct' => 0, 'groups' => 0, 'users_total' => null, 'limited' => 0 );
        if ( $db->databaseName() === 'mongo' )
            return $result;
        $rows = $db->arrayQuery( 'SELECT contentobject_id, limit_identifier FROM ezuser_role WHERE role_id = ' . (int)$role->attribute( 'id' ) );
        $objectIDs = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $objectIDs[(int)$row['contentobject_id']] = true;
            if ( trim( (string)$row['limit_identifier'] ) !== '' )
                $result['limited']++;
        }
        if ( !$objectIDs )
        {
            $result['users_total'] = 0;
            return $result;
        }

        $idList = $db->generateSQLINStatement( array_keys( $objectIDs ), 'contentobject_id', false, true, 'int' );
        $users = array();
        foreach ( (array)$db->arrayQuery( "SELECT contentobject_id FROM ezuser WHERE $idList" ) as $row )
            $users[(int)$row['contentobject_id']] = true;
        $result['users_direct'] = count( $users );

        $groupIDs = array_diff_key( $objectIDs, $users );
        $paths = array();
        if ( $groupIDs )
        {
            $nodes = $db->arrayQuery( 'SELECT path_string FROM ezcontentobject_tree WHERE '
                                      . $db->generateSQLINStatement( array_keys( $groupIDs ), 'contentobject_id', false, true, 'int' ) );
            foreach ( (array)$nodes as $node )
                $paths[(string)$node['path_string']] = true;
            $result['groups'] = count( $groupIDs );
        }
        if ( count( $groupIDs ) > self::AFFECTED_GROUP_LIMIT )
            return $result;

        $total = $users;
        if ( $paths )
        {
            $likes = array();
            foreach ( array_keys( $paths ) as $path )
                $likes[] = "t.path_string LIKE '" . $db->escapeString( $path ) . "%'";
            $members = $db->arrayQuery( 'SELECT DISTINCT u.contentobject_id FROM ezuser u, ezcontentobject_tree t
                                         WHERE t.contentobject_id = u.contentobject_id AND ( ' . implode( ' OR ', $likes ) . ' )' );
            foreach ( (array)$members as $row )
                $total[(int)$row['contentobject_id']] = true;
        }
        $result['users_total'] = count( $total );
        return $result;
    }

    /**
     * A policy's signature for comparing a draft with the saved role: module, function and the limitations with
     * their stored values, in a fixed order; the ids are left out.
     *
     * @param array $policy module, function, limitations (identifier => list of stored values)
     * @return string
     */
    public static function policySignature( array $policy )
    {
        $limitations = isset( $policy['limitations'] ) && is_array( $policy['limitations'] ) ? $policy['limitations'] : array();
        ksort( $limitations );
        foreach ( $limitations as $identifier => $values )
        {
            $values = array_map( 'strval', (array)$values );
            sort( $values );
            $limitations[$identifier] = $values;
        }
        return json_encode( array( (string)$policy['module'], (string)$policy['function'], $limitations ) );
    }

    /**
     * Whether two lists of policy signatures hold different policies (the order counts: the editor can change it).
     *
     * @param string[] $a
     * @param string[] $b
     * @return bool
     */
    public static function signaturesDiffer( array $a, array $b )
    {
        return array_values( $a ) !== array_values( $b );
    }

    /**
     * Whether the draft the role editor works on differs from the role as it is saved: its name, or its policies
     * in their order. Null when one of them has more than DRAFT_COMPARE_LIMIT policies (then the page does not say).
     *
     * @param eZRole $draft
     * @return bool|null
     */
    public static function draftDiffers( eZRole $draft )
    {
        $originalID = (int)$draft->attribute( 'version' );
        if ( $originalID <= 0 )
            return null;
        $original = eZRole::fetch( $originalID );
        if ( !$original instanceof eZRole )
            return null;
        if ( (int)$original->attribute( 'is_new' ) === 1 )
            return true;
        if ( (string)$draft->attribute( 'name' ) !== (string)$original->attribute( 'name' ) )
            return true;
        $draftCount = $draft->policyCount();
        $originalCount = $original->policyCount();
        if ( $draftCount !== $originalCount )
            return true;
        if ( $draftCount > self::DRAFT_COMPARE_LIMIT )
            return null;
        return self::signaturesDiffer( self::signatures( $draft ), self::signatures( $original ) );
    }

    /**
     * @param eZRole $role
     * @return string[]
     */
    protected static function signatures( eZRole $role )
    {
        $signatures = array();
        foreach ( $role->policyPage( 0, false ) as $policy )
        {
            $limitations = array();
            foreach ( (array)$policy->attribute( 'limitations' ) as $limitation )
                $limitations[(string)$limitation->attribute( 'identifier' )] = (array)$limitation->attribute( 'values_as_array' );
            $signatures[] = self::policySignature( array( 'module' => $policy->attribute( 'module_name' ),
                                                          'function' => $policy->attribute( 'function_name' ),
                                                          'limitations' => $limitations ) );
        }
        return $signatures;
    }

    /**
     * The moves that bring the policy $policyID to $position (1 for the first) in the order $ids: one
     * array( id, 'up'|'down' ) per call of eZRole::movePolicy(), the role's only way of reordering (a move swaps the
     * contents of a policy and its neighbour and leaves the ids, which are the order, in place). A position past
     * either end is the end. Empty when the policy is not in $ids or already there.
     *
     * @param int[] $ids the role's policy ids in their order (ascending)
     * @param int $policyID
     * @param int $position
     * @return array
     */
    public static function moveSteps( array $ids, $policyID, $position )
    {
        $ids = array_values( array_map( 'intval', $ids ) );
        $from = array_search( (int)$policyID, $ids, true );
        if ( $from === false || !$ids )
            return array();
        $to = max( 0, min( count( $ids ) - 1, (int)$position - 1 ) );
        $steps = array();
        // the moved contents travel with each swap: from the id at $k to the id next to it
        for ( $k = $from; $k < $to; $k++ )
            $steps[] = array( $ids[$k], 'down' );
        for ( $k = $from; $k > $to; $k-- )
            $steps[] = array( $ids[$k], 'up' );
        return $steps;
    }

    /**
     * What a list of moves does to the order, without a database: $contents (id => what the policy holds) after the
     * steps, as eZRole::movePolicy() would leave them. For the tests and for checking moveSteps().
     *
     * @param array $contents id => contents, in the order of the ids
     * @param array $steps see moveSteps()
     * @return array id => contents
     */
    public static function applySteps( array $contents, array $steps )
    {
        ksort( $contents );
        $ids = array_keys( $contents );
        foreach ( $steps as $step )
        {
            $i = array_search( (int)$step[0], $ids, true );
            $j = $step[1] === 'up' ? $i - 1 : $i + 1;
            if ( $i === false || !isset( $ids[$j] ) )
                continue;
            $a = $contents[$ids[$i]];
            $contents[$ids[$i]] = $contents[$ids[$j]];
            $contents[$ids[$j]] = $a;
        }
        return $contents;
    }

    /**
     * Moves the policy $policyID of the draft $draft to $position (1 for the first) of the role's order, by
     * eZRole::movePolicy() steps in one transaction. Only a draft of the role editor (version > 0): the order of a
     * saved role changes only by saving a draft. Nothing a policy permits changes; only which id holds it.
     *
     * @param eZRole $draft
     * @param int $policyID
     * @param int $position
     * @return int the number of steps made, 0 when nothing moved
     */
    public static function movePolicyTo( eZRole $draft, $policyID, $position )
    {
        if ( (int)$draft->attribute( 'version' ) <= 0 )
            return 0;
        $rows = eZPersistentObject::fetchObjectList( eZPolicy::definition(), array( 'id' ),
                                                     array( 'role_id' => (int)$draft->attribute( 'id' ), 'original_id' => 0 ),
                                                     array( 'id' => 'asc' ), null, false );
        $ids = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
            $ids[] = (int)$row['id'];
        $steps = self::moveSteps( $ids, $policyID, $position );
        if ( !$steps )
            return 0;
        $db = eZDB::instance();
        $db->begin();
        $made = 0;
        foreach ( $steps as $step )
        {
            if ( !$draft->movePolicy( $step[0], $step[1] ) )
                break;
            $made++;
        }
        $db->commit();
        return $made;
    }

    /**
     * The limitation a role is assigned with from role/view's "Assign with limitation": subtree or section, nothing
     * else (the value comes from a form and becomes part of an address).
     *
     * @param mixed $type
     * @return string|false
     */
    public static function assignLimitType( $type )
    {
        return is_string( $type ) && in_array( $type, array( 'subtree', 'section' ), true ) ? $type : false;
    }
}
