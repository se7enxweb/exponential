<?php
/**
 * Read-only persistent definitions of two kernel tables the subitems list columns count or read
 * and that have no persistent class of their own: ezuservisit (the user columns) and
 * ezsearch_object_word_link (the search index column). Going through eZPersistentObject keeps the
 * queries working on every database the kernel supports.
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/** A row of ezuservisit: a user's visits, logins and failed logins. Never stored from here. */
class expSubitemsUserVisitRow extends eZPersistentObject
{
    public static function definition()
    {
        return array( 'fields' => array( 'user_id' => array( 'name' => 'UserID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                                         'current_visit_timestamp' => array( 'name' => 'CurrentVisitTimestamp', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                                         'last_visit_timestamp' => array( 'name' => 'LastVisitTimestamp', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                                         'failed_login_attempts' => array( 'name' => 'FailedLoginAttempts', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                                         'login_count' => array( 'name' => 'LoginCount', 'datatype' => 'integer', 'default' => 0, 'required' => true ) ),
                      'keys' => array( 'user_id' ),
                      'class_name' => 'expSubitemsUserVisitRow',
                      'name' => 'ezuservisit' );
    }
}

/** A row of ezsearch_object_word_link: one indexed word position of an object. Never stored from here. */
class expSubitemsSearchWordLinkRow extends eZPersistentObject
{
    public static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                                         'contentobject_id' => array( 'name' => 'ContentObjectID', 'datatype' => 'integer', 'default' => 0, 'required' => true ) ),
                      'keys' => array( 'id' ),
                      'class_name' => 'expSubitemsSearchWordLinkRow',
                      'name' => 'ezsearch_object_word_link' );
    }
}
