<?php
/**
 * The account status the administration marks on user objects: enabled, disabled (Setup > Users,
 * ezuser_setting.is_enabled = 0) or locked (more failed logins than [UserSettings]
 * MaxNumberOfFailedLogin, as eZUser::isLocked() decides).
 *
 * Made for lists: forObjectIDs() reads the status of every object of a page with at most two
 * queries (ezuser_setting, then ezuservisit for the users among them), never one per row. Objects
 * that are no user accounts have no ezuser_setting row and are left out of the result.
 *
 * Who may see it: the same users as the subitems columns "Account enabled" and "Locked out"
 * (settings/subitemscolumns.ini, Policy[]=role/read), so the marks show nothing those columns
 * would not.
 * Guide: doc/features/6.0/admin4-design.md, section "Visibility and account status"
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expUserAccountStatus
{
    const ENABLED = 'enabled';
    const DISABLED = 'disabled';
    const LOCKED = 'locked';

    /** @var callable|null fn(): bool, replaces the role/read check (tests) */
    protected static $accessCheck = null;

    /** @var callable|null fn( string $table, int[] $ids ): array rows, replaces the database (tests) */
    protected static $rowLoader = null;

    /** @var int|null replaces [UserSettings] MaxNumberOfFailedLogin (tests) */
    protected static $maxFailedLogins = null;

    /**
     * Replaces the access check, the database reads and the failed-login limit (tests); no
     * argument restores all three.
     */
    public static function setTestDoubles( $accessCheck = null, $rowLoader = null, $maxFailedLogins = null )
    {
        self::$accessCheck = $accessCheck;
        self::$rowLoader = $rowLoader;
        self::$maxFailedLogins = $maxFailedLogins;
    }

    /**
     * Whether the current user may see account status: role/read, like the user columns of the
     * subitems table.
     *
     * @return bool
     */
    public static function canView()
    {
        if ( self::$accessCheck !== null )
            return (bool)call_user_func( self::$accessCheck );
        $access = eZUser::currentUser()->hasAccessTo( 'role', 'read' );
        return isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
    }

    /**
     * The status of each user account among the objects: object id => 'enabled', 'disabled' or
     * 'locked'. A disabled account is 'disabled' even when it is also locked. At most two queries
     * for the whole list; no access check (see canView()).
     *
     * @param int[] $objectIDs
     * @return string[]
     */
    public static function forObjectIDs( array $objectIDs )
    {
        $ids = array();
        foreach ( $objectIDs as $id )
        {
            $id = (int)$id;
            if ( $id > 0 )
                $ids[$id] = $id;
        }
        if ( !$ids )
            return array();

        $status = array();
        foreach ( self::rows( 'ezuser_setting', array_values( $ids ) ) as $row )
        {
            if ( !isset( $row['user_id'] ) )
                continue;
            $status[(int)$row['user_id']] = (int)$row['is_enabled'] ? self::ENABLED : self::DISABLED;
        }

        $max = self::$maxFailedLogins !== null ? (int)self::$maxFailedLogins : (int)eZUser::maxNumberOfFailedLogin();
        $enabled = array_keys( $status, self::ENABLED, true );
        if ( $max > 0 && $enabled )
        {
            foreach ( self::rows( 'ezuservisit', $enabled ) as $row )
            {
                // eZUser::isEnabledAfterFailedLogin(): locked once the attempts exceed the limit
                if ( isset( $row['user_id'] ) && (int)$row['failed_login_attempts'] > $max )
                    $status[(int)$row['user_id']] = self::LOCKED;
            }
        }
        return $status;
    }

    /**
     * The status of one object for templates (fetch( 'user', 'account_status', ... )): false when
     * the current user may not see it or the object is no user account.
     *
     * @param int $objectID
     * @return string|false
     */
    public static function forObjectID( $objectID )
    {
        if ( !self::canView() )
            return false;
        $status = self::forObjectIDs( array( $objectID ) );
        return isset( $status[(int)$objectID] ) ? $status[(int)$objectID] : false;
    }

    /**
     * Adds 'user_status' to each item of an encoded node list (ezjscAjaxContent::nodeEncode()
     * 'raw') that is a user account, when the current user may see it; other items stay as they
     * are.
     *
     * @param array $list items with 'contentobject_id'
     * @return array
     */
    public static function addToList( array $list )
    {
        if ( !$list || !self::canView() )
            return $list;
        $ids = array();
        foreach ( $list as $item )
        {
            if ( is_array( $item ) && isset( $item['contentobject_id'] ) )
                $ids[] = (int)$item['contentobject_id'];
        }
        $status = self::forObjectIDs( $ids );
        foreach ( $list as $i => $item )
        {
            if ( is_array( $item ) && isset( $item['contentobject_id'] ) && isset( $status[(int)$item['contentobject_id']] ) )
                $list[$i]['user_status'] = $status[(int)$item['contentobject_id']];
        }
        return $list;
    }

    /**
     * Marks the user's locations as modified (modified_subnode), so the content tree, which the
     * browser keeps by that stamp, loads the user's group again after the account was enabled or
     * disabled. Publishing does this for content; changing the account setting did not.
     *
     * @param int $userID
     */
    public static function touchTree( $userID )
    {
        $object = eZContentObject::fetch( (int)$userID );
        if ( !$object instanceof eZContentObject )
            return;
        foreach ( $object->assignedNodes() as $treeNode )
        {
            if ( $treeNode instanceof eZContentObjectTreeNode )
                $treeNode->updateAndStoreModified();
        }
    }

    /**
     * The rows of ezuser_setting or ezuservisit for the user ids, as arrays, in one query.
     *
     * @param string $table
     * @param int[] $ids
     * @return array
     */
    protected static function rows( $table, array $ids )
    {
        if ( self::$rowLoader !== null )
            return (array)call_user_func( self::$rowLoader, $table, $ids );

        $definition = $table === 'ezuservisit' ? expSubitemsUserVisitRow::definition() : eZUserSetting::definition();
        // eZPersistentObject writes array( array( ... ) ) as IN ( ... ); the MongoDB backend takes the filter as is
        $condition = eZDB::instance()->databaseName() === 'mongo' ? array( '$in' => $ids ) : array( $ids );
        $rows = eZPersistentObject::fetchObjectList( $definition, null, array( 'user_id' => $condition ), null, null, false );
        return is_array( $rows ) ? $rows : array();
    }
}
