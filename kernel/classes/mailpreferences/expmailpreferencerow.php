<?php
/**
 * File containing the expMailPreferenceRow class.
 *
 * One stored preference (table expmail_preference): a recipient (recipient_key 'u:<user id>' or 'a:<address hash>'),
 * a category, its state (on, off, pending) and frequency. The master switch is the row of category '_master'.
 * No row means the category's default (or what its handler reads from older data). Use expMailPreferences.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMailPreferenceRow extends eZPersistentObject
{
    const MASTER = '_master';

    static function definition()
    {
        $int = array( 'datatype' => 'integer', 'default' => 0, 'required' => true );
        $str = array( 'datatype' => 'string', 'default' => '', 'required' => true );
        return array( 'fields' => array( 'id' => array( 'name' => 'ID' ) + $int,
                                         'recipient_key' => array( 'name' => 'RecipientKey' ) + $str,
                                         'user_id' => array( 'name' => 'UserID' ) + $int,
                                         'category' => array( 'name' => 'Category' ) + $str,
                                         'state' => array( 'name' => 'State' ) + $str,
                                         'frequency' => array( 'name' => 'Frequency' ) + $str,
                                         'created' => array( 'name' => 'Created' ) + $int,
                                         'modified' => array( 'name' => 'Modified' ) + $int ),
                      'keys' => array( 'id' ),
                      'increment_key' => 'id',
                      'sort' => array( 'id' => 'asc' ),
                      'class_name' => 'expMailPreferenceRow',
                      'name' => 'expmail_preference' );
    }

    /** @return expMailPreferenceRow[] category => row */
    static function fetchForKey( $recipientKey )
    {
        $out = array();
        $rows = eZPersistentObject::fetchObjectList( self::definition(), null, array( 'recipient_key' => (string)$recipientKey ), null, null, true );
        foreach ( (array)$rows as $row )
            $out[$row->attribute( 'category' )] = $row;
        return $out;
    }
}
