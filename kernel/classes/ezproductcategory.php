<?php
/**
 * File containing the eZProductCategory class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZProductCategory ezproductcategory.php
  \brief Handles product categories used by the default VAT handler.
  \ingroup eZKernel
*/

class eZProductCategory extends eZPersistentObject
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
                      "class_name" => "eZProductCategory",
                      "name" => "ezproductcategory" );
    }

    static function fetch( $id, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZProductCategory::definition(),
                                                null,
                                                array( "id" => $id ),
                                                $asObject );
    }

    static function fetchByName( $name, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZProductCategory::definition(),
                                                null,
                                                array( "name" => $name ),
                                                $asObject );
    }

    static function fetchList( $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZProductCategory::definition(),
                                                    null, null, array( 'name' => 'asc' ), null,
                                                    $asObject );
    }

    /**
     * Returns number of products belonging to the given category.
     *
     * \public
     * \static
     */
    static function fetchProductCountByCategory( $categoryID )
    {
        $ini = eZINI::instance( 'shop.ini' );
        if ( !$ini->hasVariable( 'VATSettings', 'ProductCategoryAttribute' ) ||
             !$categoryAttrName = $ini->variable( 'VATSettings', 'ProductCategoryAttribute' ) )
            return 0;

        $db = eZDB::instance();
        $categoryID =(int) $categoryID;
        $categoryAttrName = $db->escapeString( $categoryAttrName );

        if ( $db->databaseName() === 'mongo' )
        {
            // Find the class attribute IDs for ezproductcategory attributes with this identifier
            $attrDefs = $db->aggregate( 'ezcontentclass_attribute', [
                [ '$match' => [ 'data_type_string' => 'ezproductcategory', 'identifier' => $categoryAttrName, 'version' => 0 ] ],
                [ '$project' => [ '_id' => 0, 'id' => 1 ] ],
            ] );
            $attrDefIDs = array_map( 'intval', array_column( $attrDefs, 'id' ) );
            if ( empty( $attrDefIDs ) )
                return 0;

            $rows = $db->aggregate( 'ezcontentobject_attribute', [
                [ '$match' => [
                    'contentclassattribute_id' => [ '$in' => $attrDefIDs ],
                    'data_int'                 => $categoryID,
                ] ],
                [ '$lookup' => [
                    'from'         => 'ezcontentobject',
                    'localField'   => 'contentobject_id',
                    'foreignField' => 'id',
                    'as'           => '_obj',
                ] ],
                [ '$unwind' => '$_obj' ],
                [ '$match'  => [ '$expr' => [ '$eq' => [ '$version', '$_obj.current_version' ] ] ] ],
                [ '$count'  => 'count' ],
            ] );
            return !empty( $rows ) ? (int)$rows[0]['count'] : 0;
        }

        $query = "SELECT COUNT(*) AS count " .
                 " FROM ezcontentobject_attribute coa, ezcontentclass_attribute cca, ezcontentobject co " .
                 "WHERE " .
                 " cca.id=coa.contentclassattribute_id " .
                 " AND coa.contentobject_id=co.id " .
                 " AND cca.data_type_string='ezproductcategory' " .
                 " AND cca.identifier='$categoryAttrName' " .
                 " AND coa.version=co.current_version " .
                 " AND coa.data_int=$categoryID";
        $rows = $db->arrayQuery( $query );
        return $rows[0]['count'];
    }

    static function create()
    {
        $row = array(
            "id" => null,
            "name" => ezpI18n::tr( 'kernel/shop/productcategories', 'Product category' ) );
        return new eZProductCategory( $row );
    }

    /**
     * Remove the given category and all references to it.
     *
     * \public
     * \static
     */
    static function removeByID( $id )
    {
        $id = (int) $id;

        $db = eZDB::instance();
        $db->begin();

        // Delete references to the category from VAT charging rules.
        eZVatRule::removeReferencesToProductCategory( $id );

        // Reset product category attribute for all products
        // that have been referencing the category.
        $ini = eZINI::instance( 'shop.ini' );
        if ( $ini->hasVariable( 'VATSettings', 'ProductCategoryAttribute' ) &&
             $categoryAttrName = $ini->variable( 'VATSettings', 'ProductCategoryAttribute' ) )
        {
            $categoryAttrName = $db->escapeString( $categoryAttrName );

            if ( $db->databaseName() === 'mongo' )
            {
                // Find class attribute IDs for ezproductcategory with this identifier
                $attrDefs = $db->aggregate( 'ezcontentclass_attribute', [
                    [ '$match' => [ 'data_type_string' => 'ezproductcategory', 'identifier' => $categoryAttrName, 'version' => 0 ] ],
                    [ '$project' => [ '_id' => 0, 'id' => 1 ] ],
                ] );
                $attrDefIDs = array_map( 'intval', array_column( $attrDefs, 'id' ) );
                if ( !empty( $attrDefIDs ) )
                {
                    // Find attribute instances pointing at this category on current versions
                    $attrRows = $db->aggregate( 'ezcontentobject_attribute', [
                        [ '$match' => [
                            'contentclassattribute_id' => [ '$in' => $attrDefIDs ],
                            'data_int'                 => $id,
                        ] ],
                        [ '$lookup' => [
                            'from'         => 'ezcontentobject',
                            'localField'   => 'contentobject_id',
                            'foreignField' => 'id',
                            'as'           => '_obj',
                        ] ],
                        [ '$unwind' => '$_obj' ],
                        [ '$match'  => [ '$expr' => [ '$eq' => [ '$version', '$_obj.current_version' ] ] ] ],
                        [ '$project' => [ '_id' => 0, 'id' => 1 ] ],
                    ] );
                    foreach ( $attrRows as $attrRow )
                    {
                        $db->mongoUpdateOne( 'ezcontentobject_attribute',
                            [ 'id' => (int)$attrRow['id'] ],
                            [ '$set' => [ 'data_int' => 0, 'sort_key_int' => 0 ] ] );
                    }
                }
            }
            else
            {
                $query = "SELECT coa.id FROM ezcontentobject_attribute coa, ezcontentclass_attribute cca, ezcontentobject co " .
                         "WHERE " .
                         " cca.id=coa.contentclassattribute_id " .
                         " AND coa.contentobject_id=co.id " .
                         " AND cca.data_type_string='ezproductcategory' " .
                         " AND cca.identifier='$categoryAttrName' " .
                         " AND coa.version=co.current_version " .
                         " AND coa.data_int=$id";

                $rows = $db->arrayQuery( $query );

                foreach ( $rows as $row )
                {
                    $query = "UPDATE ezcontentobject_attribute SET data_int=0, sort_key_int=0 WHERE id=" . (int) $row['id'];
                    $db->query( $query );
                }
            }
        }

        // Remove the category itself.
        eZPersistentObject::removeObject( eZProductCategory::definition(),
                                          array( "id" => $id ) );

        $db->commit();
    }
}

?>
