<?php
/**
 * File containing the expMailPendingRow class.
 *
 * A double opt-in waiting for its confirmation (table expmail_pending): kind 'category' (a newsletter or marketing
 * category switched on), 'email_change' (a new e-mail address of an account; data holds it) or 'link' (a "send me a
 * link" request, kept for the rate limit only). Expired rows are removed by expConsentLog::cleanup().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMailPendingRow extends eZPersistentObject
{
    static function definition()
    {
        $int = array( 'datatype' => 'integer', 'default' => 0, 'required' => true );
        $str = array( 'datatype' => 'string', 'default' => '', 'required' => true );
        return array( 'fields' => array( 'id' => array( 'name' => 'ID' ) + $int,
                                         'recipient_key' => array( 'name' => 'RecipientKey' ) + $str,
                                         'user_id' => array( 'name' => 'UserID' ) + $int,
                                         'category' => array( 'name' => 'Category' ) + $str,
                                         'kind' => array( 'name' => 'Kind' ) + $str,
                                         'data' => array( 'name' => 'Data' ) + $str,
                                         'created' => array( 'name' => 'Created' ) + $int,
                                         'expires' => array( 'name' => 'Expires' ) + $int ),
                      'keys' => array( 'id' ),
                      'increment_key' => 'id',
                      'sort' => array( 'id' => 'asc' ),
                      'class_name' => 'expMailPendingRow',
                      'name' => 'expmail_pending' );
    }

    /** @return expMailPendingRow|null */
    static function fetch( $id )
    {
        return eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => (int)$id ), true );
    }

    /** @return expMailPendingRow[] */
    static function fetchForKey( $recipientKey, $kind = null, $category = null )
    {
        $conds = array( 'recipient_key' => (string)$recipientKey );
        if ( $kind !== null )
            $conds['kind'] = (string)$kind;
        if ( $category !== null )
            $conds['category'] = (string)$category;
        return (array)eZPersistentObject::fetchObjectList( self::definition(), null, $conds, null, null, true );
    }

    /** @return array the data column decoded */
    public function dataArray()
    {
        $d = json_decode( (string)$this->attribute( 'data' ), true );
        return is_array( $d ) ? $d : array();
    }
}
