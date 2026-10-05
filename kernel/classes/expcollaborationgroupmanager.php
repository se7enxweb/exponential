<?php
/**
 * File containing the expCollaborationGroupManager class.
 *
 * Creates, renames and deletes the collaboration groups of one user and moves items between them. Every
 * method works on the groups of the given user only and returns array( 'ok' => bool, 'text' => the message ).
 * Deleting a group never deletes an item: the items of the group and its subgroups go to the user's main group.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expCollaborationGroupManager
{
    const MAX_GROUPS = 100;
    const MAX_DEPTH = 5;

    private static function result( $ok, $text )
    {
        return array( 'ok' => (bool)$ok, 'text' => $text );
    }

    private static function cleanTitle( $title )
    {
        $title = trim( preg_replace( '/[\x00-\x1F\x7F]+/u', ' ', (string)$title ) );
        return mb_substr( $title, 0, 255 );
    }

    /** @return array id => array( id, parent_group_id, title ) of the user's groups */
    private static function groups( $userID )
    {
        $db = eZDB::instance();
        $list = array();
        foreach ( (array)$db->arrayQuery( 'SELECT id, parent_group_id, title, depth FROM ezcollab_group WHERE user_id=' . (int)$userID ) as $r )
            $list[(int)$r['id']] = array( 'id' => (int)$r['id'], 'parent_group_id' => (int)$r['parent_group_id'],
                                          'title' => $r['title'], 'depth' => (int)$r['depth'] );
        return $list;
    }

    public static function create( $userID, $title, $parentID = 0 )
    {
        $title = self::cleanTitle( $title );
        if ( $title === '' )
            return self::result( false, ezpI18n::tr( 'kernel/collaboration', 'Enter a name for the group.' ) );
        $groups = self::groups( $userID );
        if ( count( $groups ) >= self::MAX_GROUPS )
            return self::result( false, ezpI18n::tr( 'kernel/collaboration', 'There are too many groups already.' ) );
        $parentID = (int)$parentID;
        if ( $parentID > 0 )
        {
            if ( !isset( $groups[$parentID] ) )
                return self::result( false, ezpI18n::tr( 'kernel/collaboration', 'The parent group was not found.' ) );
            if ( $groups[$parentID]['depth'] + 1 >= self::MAX_DEPTH )
                return self::result( false, ezpI18n::tr( 'kernel/collaboration', 'Groups cannot be nested that deep.' ) );
        }
        foreach ( $groups as $g )
            if ( $g['parent_group_id'] === $parentID && mb_strtolower( $g['title'] ) === mb_strtolower( $title ) )
                return self::result( false, ezpI18n::tr( 'kernel/collaboration', 'A group with this name exists already.' ) );
        eZCollaborationGroup::instantiate( (int)$userID, $title, $parentID );
        return self::result( true, ezpI18n::tr( 'kernel/collaboration', 'The group "%1" was created.', null, array( $title ) ) );
    }

    public static function rename( $userID, $groupID, $title )
    {
        $title = self::cleanTitle( $title );
        $group = eZCollaborationGroup::fetch( (int)$groupID, (int)$userID );
        if ( !$group )
            return self::result( false, ezpI18n::tr( 'kernel/collaboration', 'The group was not found.' ) );
        if ( (int)$group->attribute( 'id' ) === self::mainGroupID( $userID ) )
            return self::result( false, ezpI18n::tr( 'kernel/collaboration', 'The main group cannot be renamed.' ) );
        if ( $title === '' )
            return self::result( false, ezpI18n::tr( 'kernel/collaboration', 'Enter a name for the group.' ) );
        $group->setAttribute( 'title', $title );
        $group->setAttribute( 'modified', time() );
        $group->store();
        return self::result( true, ezpI18n::tr( 'kernel/collaboration', 'The group was renamed to "%1".', null, array( $title ) ) );
    }

    public static function delete( $userID, $groupID )
    {
        $userID = (int)$userID;
        $groupID = (int)$groupID;
        $groups = self::groups( $userID );
        if ( !isset( $groups[$groupID] ) )
            return self::result( false, ezpI18n::tr( 'kernel/collaboration', 'The group was not found.' ) );
        $main = self::mainGroupID( $userID );
        if ( $groupID === $main )
            return self::result( false, ezpI18n::tr( 'kernel/collaboration', 'The main group cannot be deleted.' ) );
        $doomed = array( $groupID );
        for ( $i = 0; $i < count( $doomed ); ++$i )
            foreach ( $groups as $g )
                if ( $g['parent_group_id'] === $doomed[$i] && !in_array( $g['id'], $doomed, true ) )
                    $doomed[] = $g['id'];
        if ( in_array( $main, $doomed, true ) )
            return self::result( false, ezpI18n::tr( 'kernel/collaboration', 'The main group cannot be deleted.' ) );
        $in = implode( ',', $doomed );
        $db = eZDB::instance();
        $db->begin();
        $db->query( "UPDATE ezcollab_item_group_link SET group_id=" . (int)$main . " WHERE user_id=$userID AND group_id IN ($in)" );
        $db->query( "DELETE FROM ezcollab_group WHERE user_id=$userID AND id IN ($in)" );
        $db->commit();
        return self::result( true, ezpI18n::tr( 'kernel/collaboration', 'The group "%1" was deleted. Its items are in the main group now.', null,
                                                array( $groups[$groupID]['title'] ) ) );
    }

    public static function moveItem( $userID, $itemID, $groupID )
    {
        $userID = (int)$userID;
        $itemID = (int)$itemID;
        $groupID = (int)$groupID;
        $group = eZCollaborationGroup::fetch( $groupID, $userID );
        if ( !$group )
            return self::result( false, ezpI18n::tr( 'kernel/collaboration', 'The group was not found.' ) );
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT collaboration_id FROM ezcollab_item_group_link WHERE user_id=$userID AND collaboration_id=$itemID" );
        if ( !$rows )
            return self::result( false, ezpI18n::tr( 'kernel/collaboration', 'The item was not found.' ) );
        $db->query( "UPDATE ezcollab_item_group_link SET group_id=$groupID WHERE user_id=$userID AND collaboration_id=$itemID" );
        return self::result( true, ezpI18n::tr( 'kernel/collaboration', 'The item was moved to "%1".', null, array( $group->attribute( 'title' ) ) ) );
    }

    private static function mainGroupID( $userID )
    {
        $profile = eZCollaborationProfile::instance( (int)$userID );
        return (int)$profile->attribute( 'main_group' );
    }

    /**
     * Keeps a message for the next page: one notice per user, shown once. $type is success, warning or error.
     */
    public static function setNotice( $type, $text )
    {
        if ( PHP_SAPI === 'cli' )
            return; // a script has no page to show it on
        $http = eZHTTPTool::instance();
        $http->setSessionVariable( 'CollaborationNotice', array( 'type' => $type, 'text' => $text ) );
    }

    /** @return array|false the pending notice, which is forgotten when it is read */
    public static function takeNotice()
    {
        $http = eZHTTPTool::instance();
        if ( !$http->hasSessionVariable( 'CollaborationNotice', false ) )
            return false;
        $notice = $http->sessionVariable( 'CollaborationNotice' );
        $http->removeSessionVariable( 'CollaborationNotice' );
        return is_array( $notice ) ? $notice : false;
    }
}

?>
