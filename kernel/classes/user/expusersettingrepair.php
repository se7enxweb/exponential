<?php
/**
 * File containing the expUserSettingRepair class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Creates the ezuser_setting rows that accounts of an older installation lack.
 *
 * An account without its row cannot sign in (since August 2026) and, since 6.0.15, is not enabled anywhere
 * (eZUser::isEnabled()): its session, API key or OAuth token stops working. Every way the kernel creates an account
 * writes the row (the user datatype, the package import, the LDAP and text file login handlers), so such accounts come
 * from an installation upgraded from an old release, an import that wrote ezuser by SQL, or a row removed by hand.
 * Before 6.0.15 such an account counted as enabled everywhere but at the sign-in, so the repair gives it back that
 * state, update/common/scripts/6.0/createmissingusersettings.php:
 *
 * - an account with a published content object gets an enabled row (max_login 1000, as the installer's accounts);
 * - one with an account key is waiting for its activation mail and gets a disabled row, as user/register leaves it;
 * - with $enable false every row is created disabled: the accounts stay locked out, but an administrator sees them as
 *   disabled and enables them one by one (user settings, user hygiene);
 * - an account whose object is not published (a draft held for approval, an orphan ezuser row) is left alone.
 *
 * plan() decides on plain values and is tested without a database; missing() and apply() read and write.
 */
class expUserSettingRepair
{
    /** max_login of a created row, as the installer's own accounts have */
    const MAX_LOGIN = 1000;

    /**
     * What to create for each account without a row.
     *
     * @param array $accounts list of array( 'id' => int, 'published' => bool, 'has_key' => bool )
     * @param bool $enable false: create every row disabled
     * @return array id => 1 (enabled row), 0 (disabled row) or null (left alone: not published)
     */
    public static function plan( array $accounts, $enable = true )
    {
        $plan = array();
        foreach ( $accounts as $account )
        {
            $id = (int)$account['id'];
            if ( $id <= 0 )
                continue;
            if ( empty( $account['published'] ) )
                $plan[$id] = null;
            else
                $plan[$id] = ( $enable && empty( $account['has_key'] ) ) ? 1 : 0;
        }
        ksort( $plan );
        return $plan;
    }

    /**
     * The accounts without an ezuser_setting row.
     *
     * @return array list of array( 'id' => int, 'published' => bool, 'has_key' => bool )
     */
    public static function missing()
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT u.contentobject_id AS id FROM ezuser u'
                                 . ' LEFT JOIN ezuser_setting s ON s.user_id = u.contentobject_id'
                                 . ' WHERE s.user_id IS NULL ORDER BY u.contentobject_id' );
        $accounts = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $id = (int)$row['id'];
            $object = eZContentObject::fetch( $id );
            $accounts[] = array(
                'id' => $id,
                'published' => $object instanceof eZContentObject
                               && (int)$object->attribute( 'status' ) === eZContentObject::STATUS_PUBLISHED,
                'has_key' => eZUserAccountKey::fetchByUserID( $id ) instanceof eZUserAccountKey,
            );
        }
        return $accounts;
    }

    /**
     * Creates the rows of a plan; a row that appeared meanwhile is left as it is.
     *
     * @param array $plan from plan()
     * @return int[] the ids that got a row
     */
    public static function apply( array $plan )
    {
        $created = array();
        foreach ( $plan as $id => $isEnabled )
        {
            if ( $isEnabled === null || eZUserSetting::fetch( $id ) instanceof eZUserSetting )
                continue;
            $setting = eZUserSetting::create( (int)$id, (int)$isEnabled );
            $setting->setAttribute( 'max_login', self::MAX_LOGIN );
            $setting->store();
            // the user cache keeps is_enabled (eZUser::isEnabled() reads it first)
            eZUser::purgeUserCacheByUserId( (int)$id );
            $created[] = (int)$id;
        }
        return $created;
    }
}
