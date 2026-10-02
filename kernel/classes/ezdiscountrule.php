<?php
/**
 * File containing the eZDiscountRule class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZDiscountRule ezdiscountrule.php
  \brief The class eZDiscountRule does

*/

class eZDiscountRule extends eZPersistentObject
{
    static function definition()
    {
        return array( "fields" => array( "id" => array( 'name' => 'ID',
                                                         'datatype' => 'integer',
                                                         'default' => 0,
                                                         'required' => true ),
                                         "name" => array( 'name' => "Name",
                                                         'datatype' => 'string',
                                                         'default' => '',
                                                         'required' => true ) ),
                      "keys" => array( "id" ),
                      "increment_key" => "id",
                      "class_name" => "eZDiscountRule",
                      "name" => "ezdiscountrule" );
    }

    static function fetch( $id, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZDiscountRule::definition(),
                                                null,
                                                array( "id" => $id ),
                                                $asObject );
    }

    static function fetchList( $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZDiscountRule::definition(),
                                                    null, null, null, null,
                                                    $asObject );
    }

    static function create()
    {
        $row = array(
            "id" => null,
            "name" => ezpI18n::tr( "kernel/shop/discountgroup", "New discount group" ) );
        return new eZDiscountRule( $row );
    }

    /**
     * Stores the discount group and records what changed (doc/bc/6.0/audit.md, commerce.discount.change).
     */
    function store( $fieldFilters = null )
    {
        $auditBefore = class_exists( 'expAuditHook' ) ? expAuditHook::rowBefore( 'commerce.discount.change', $this ) : null;
        parent::store( $fieldFilters );
        if ( $auditBefore !== null )
            expAuditHook::rowChanged( 'commerce.discount.change', $this, $auditBefore, array( 'name' ),
                                      array( 'type' => 'discount_group', 'id' => (int)$this->attribute( 'id' ) ) );
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    static function removeByID( $id )
    {
        // Audit (doc/bc/6.0/audit.md, commerce.discount.change): the row removed
        if ( class_exists( 'expAuditHook' ) && expAuditHook::on( 'commerce.discount.change' ) )
        {
            $auditRow = eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => (int)$id ), false );
            if ( is_array( $auditRow ) )
                expAuditHook::emit( 'commerce.discount.change', array( 'object' => array( 'type' => 'discount_group', 'id' => (int)$id ), 'verb' => 'remove',
                                                 'before' => array_intersect_key( $auditRow, array_flip( array( 'name' ) ) ) ) );
        }
        eZPersistentObject::removeObject( eZDiscountRule::definition(),
                                          array( "id" => $id ) );
    }
}
?>
