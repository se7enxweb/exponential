<?php
/**
 * File containing the expMailCategoryRow class.
 *
 * A category made or changed in the admin's category manager (table expmail_category). A row with the identifier
 * of an INI category changes its name, description and default; a row with a new identifier is a category of its
 * own (source 'admin'). expMailCategoryRegistry reads them; use the registry, not this class, to work with
 * categories.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMailCategoryRow extends eZPersistentObject
{
    static function definition()
    {
        $int = array( 'datatype' => 'integer', 'default' => 0, 'required' => true );
        $str = array( 'datatype' => 'string', 'default' => '', 'required' => true );
        return array( 'fields' => array( 'id' => array( 'name' => 'ID' ) + $int,
                                         'identifier' => array( 'name' => 'Identifier' ) + $str,
                                         'name' => array( 'name' => 'Name' ) + $str,
                                         'description' => array( 'name' => 'Description' ) + $str,
                                         'essential' => array( 'name' => 'Essential' ) + $int,
                                         'default_on' => array( 'name' => 'DefaultOn' ) + $int,
                                         'frequencies' => array( 'name' => 'Frequencies' ) + $str,
                                         'double_opt_in' => array( 'name' => 'DoubleOptIn' ) + $int,
                                         'handler_class' => array( 'name' => 'HandlerClass' ) + $str,
                                         'priority' => array( 'name' => 'Priority' ) + $int,
                                         'created' => array( 'name' => 'Created' ) + $int,
                                         'modified' => array( 'name' => 'Modified' ) + $int ),
                      'keys' => array( 'id' ),
                      'increment_key' => 'id',
                      'sort' => array( 'priority' => 'asc', 'id' => 'asc' ),
                      'class_name' => 'expMailCategoryRow',
                      'name' => 'expmail_category' );
    }

    /** @return expMailCategoryRow[] */
    static function fetchAll()
    {
        return eZPersistentObject::fetchObjectList( self::definition(), null, null, null, null, true );
    }

    /** @return expMailCategoryRow|null */
    static function fetchByIdentifier( $identifier )
    {
        return eZPersistentObject::fetchObject( self::definition(), null, array( 'identifier' => (string)$identifier ), true );
    }
}
