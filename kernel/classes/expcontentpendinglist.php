<?php
/**
 * File containing the expContentPendingList class.
 *
 * What the pending page (content/pendinglist, "My pending items") lists, worked out from plain arrays: the versions
 * the user sent for publishing that a workflow holds (an approval, a waiting event), and the versions waiting for the
 * user's own approval; the filters of the address (which list, class, order), the rows that match them, the figures
 * and the address part that keeps the filters. Nothing here reads the database or the current user, so every rule can
 * be tested without an installation (tests/tests/kernel/classes/expContentPendingListTest.php). The view
 * (kernel/private/classes/views/content/pendinglist.php) reads the versions, approvals and workflow processes and
 * leaves out every version the user may not see: their own are theirs, one waiting for their approval only when
 * content/versionread allows it.
 *
 * Guide: doc/guides/drafts-and-pending.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentPendingList
{
    /// The lists: everything, the user's own versions, the versions waiting for the user's approval
    const SCOPES = array( 'all', 'mine', 'approve' );

    /// The orders, the first is the default: the most recently sent first, the longest waiting first, by name
    const SORTS = array( 'newest', 'oldest', 'name' );

    /// The states of an approval (ezapprove's data_int3), by their number
    const APPROVAL_STATES = array( 0 => 'waiting', 1 => 'accepted', 2 => 'denied', 3 => 'deferred' );

    /**
     * @param array $userParameters (scope)/<all|mine|approve>, (class)/<identifier>, (sort)/<order>
     * @return array( 'scope' => string, 'class' => string, 'sort' => string )
     */
    static function filters( array $userParameters )
    {
        $scope = isset( $userParameters['scope'] ) ? (string)$userParameters['scope'] : '';
        $class = isset( $userParameters['class'] ) ? (string)$userParameters['class'] : '';
        $sort = isset( $userParameters['sort'] ) ? (string)$userParameters['sort'] : '';
        return array( 'scope' => in_array( $scope, self::SCOPES, true ) ? $scope : self::SCOPES[0],
                      'class' => preg_match( '/^[a-z0-9_]{1,64}$/i', $class ) ? $class : '',
                      'sort' => in_array( $sort, self::SORTS, true ) ? $sort : self::SORTS[0] );
    }

    /**
     * @param array $filters
     * @param array $change
     * @return string the address part, such as "/(scope)/approve/(sort)/oldest"
     */
    static function suffix( array $filters, array $change = array() )
    {
        $filters = array_merge( $filters, $change );
        $suffix = '';
        if ( !empty( $filters['scope'] ) && $filters['scope'] !== self::SCOPES[0] )
            $suffix .= '/(scope)/' . $filters['scope'];
        if ( !empty( $filters['class'] ) )
            $suffix .= '/(class)/' . $filters['class'];
        if ( !empty( $filters['sort'] ) && $filters['sort'] !== self::SORTS[0] )
            $suffix .= '/(sort)/' . $filters['sort'];
        return $suffix;
    }

    /**
     * @param array $filters
     * @param array $classes identifier => name
     * @return array the address parts of the filter links ("all" is the filter left out)
     */
    static function links( array $filters, array $classes )
    {
        $links = array( 'scope' => array(), 'class' => array( 'all' => self::suffix( $filters, array( 'class' => '' ) ) ), 'sort' => array() );
        foreach ( self::SCOPES as $scope )
            $links['scope'][$scope] = self::suffix( $filters, array( 'scope' => $scope ) );
        foreach ( array_keys( $classes ) as $identifier )
            $links['class'][$identifier] = self::suffix( $filters, array( 'class' => $identifier ) );
        foreach ( self::SORTS as $sort )
            $links['sort'][$sort] = self::suffix( $filters, array( 'sort' => $sort ) );
        return $links;
    }

    /**
     * One row from the fields of a pending version and what holds it.
     *
     * @param array $item id, version, object_id, name, class_identifier, class_name, language, language_name,
     *                    creator_id, creator_name, sent (when it was sent for publishing: the version's modified time),
     *                    is_new, node_id, location, mine (the user sent it), approver (it waits for the user's
     *                    approval), approval_id (the collaboration item, 0 for none), approval_state (data_int3),
     *                    approvers (names), approval_link (the user takes part in the approval), workflows (names
     *                    of the workflows whose process holds it), can_versionread
     * @return array the same as plain values, with approval_state_name
     */
    static function row( array $item )
    {
        $int = function ( $key ) use ( $item ) { return isset( $item[$key] ) ? (int)$item[$key] : 0; };
        $str = function ( $key ) use ( $item ) { return isset( $item[$key] ) ? (string)$item[$key] : ''; };
        $list = function ( $key ) use ( $item ) { return isset( $item[$key] ) && is_array( $item[$key] ) ? array_values( array_map( 'strval', $item[$key] ) ) : array(); };
        $state = $int( 'approval_state' );
        return array( 'id' => $int( 'id' ), 'version' => $int( 'version' ), 'object_id' => $int( 'object_id' ),
                      'name' => $str( 'name' ), 'class_identifier' => $str( 'class_identifier' ), 'class_name' => $str( 'class_name' ),
                      'language' => $str( 'language' ), 'language_name' => $str( 'language_name' ),
                      'creator_id' => $int( 'creator_id' ), 'creator_name' => $str( 'creator_name' ), 'sent' => $int( 'sent' ),
                      'is_new' => !empty( $item['is_new'] ), 'node_id' => $int( 'node_id' ), 'location' => $str( 'location' ),
                      'mine' => !empty( $item['mine'] ), 'approver' => !empty( $item['approver'] ),
                      'approval_id' => $int( 'approval_id' ), 'approval_state' => $state,
                      'approval_state_name' => $int( 'approval_id' ) ? ( isset( self::APPROVAL_STATES[$state] ) ? self::APPROVAL_STATES[$state] : 'waiting' ) : '',
                      'approvers' => $list( 'approvers' ), 'approval_link' => !empty( $item['approval_link'] ),
                      'workflows' => $list( 'workflows' ), 'can_versionread' => !empty( $item['can_versionread'] ) );
    }

    /**
     * Whether a version waiting for the user's approval is shown: only when the user may read that version
     * (content/versionread), as the approval itself needs.
     *
     * @param array $row from row()
     * @return bool
     */
    static function visible( array $row )
    {
        return $row['mine'] || ( $row['approver'] && $row['can_versionread'] );
    }

    /**
     * The rows that match the filters, in the order asked for; rows the user may not see are left out.
     *
     * @param array $rows
     * @param array $filters
     * @return array
     */
    static function select( array $rows, array $filters )
    {
        $out = array();
        foreach ( $rows as $row )
        {
            if ( !self::visible( $row ) )
                continue;
            if ( $filters['scope'] === 'mine' && !$row['mine'] )
                continue;
            if ( $filters['scope'] === 'approve' && !$row['approver'] )
                continue;
            if ( !empty( $filters['class'] ) && $row['class_identifier'] !== $filters['class'] )
                continue;
            $out[] = $row;
        }
        $sort = $filters['sort'];
        usort( $out, function ( $a, $b ) use ( $sort )
        {
            if ( $sort === 'name' )
            {
                $order = strnatcasecmp( $a['name'], $b['name'] );
                return $order !== 0 ? $order : $b['sent'] <=> $a['sent'];
            }
            $order = $sort === 'oldest' ? $a['sent'] <=> $b['sent'] : $b['sent'] <=> $a['sent'];
            return $order !== 0 ? $order : $b['id'] <=> $a['id'];
        } );
        return $out;
    }

    /**
     * @param array $rows every row
     * @return array( 'mine' => int, 'approve' => int, 'held_by_approval' => int, 'classes' => array( identifier => name ) )
     *         counting only what the user may see
     */
    static function overview( array $rows )
    {
        $mine = 0;
        $approve = 0;
        $approval = 0;
        $classes = array();
        foreach ( $rows as $row )
        {
            if ( !self::visible( $row ) )
                continue;
            if ( $row['mine'] )
                $mine++;
            if ( $row['approver'] )
                $approve++;
            if ( $row['approval_id'] )
                $approval++;
            if ( $row['class_identifier'] !== '' )
                $classes[$row['class_identifier']] = $row['class_name'] !== '' ? $row['class_name'] : $row['class_identifier'];
        }
        asort( $classes, SORT_NATURAL | SORT_FLAG_CASE );
        return array( 'mine' => $mine, 'approve' => $approve, 'held_by_approval' => $approval, 'classes' => $classes );
    }
}

?>
