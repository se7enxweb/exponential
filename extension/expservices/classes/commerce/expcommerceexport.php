<?php
/**
 * Plain-array encoders of the shop objects, shared by the commerce services. Nothing here checks access: the
 * services do, before they encode.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expCommerceExport
{
    /** @return float|null a number as float, null when it is not numeric */
    public static function num( $v )
    {
        return is_numeric( $v ) ? round( (float)$v, 4 ) : null;
    }

    /** The price attribute (ezprice, ezmultiprice) of a product node as a price descriptor, null when it has none. */
    public static function price( $dataMapOrObject, $currency = false )
    {
        $dataMap = $dataMapOrObject instanceof eZContentObject ? $dataMapOrObject->attribute( 'data_map' ) : $dataMapOrObject;
        foreach ( $dataMap as $identifier => $attribute )
        {
            if ( !eZShopFunctions::isProductDatatype( $attribute->attribute( 'data_type_string' ) ) )
                continue;
            $p = $attribute->content();
            if ( !is_object( $p ) )
                continue;
            $code = (string)$p->attribute( 'currency' );
            $out = array( 'attribute' => $identifier, 'currency' => $code,
                'price' => self::num( $p->attribute( 'price' ) ),
                'inc_vat' => self::num( $p->attribute( 'inc_vat_price' ) ), 'ex_vat' => self::num( $p->attribute( 'ex_vat_price' ) ),
                'vat_percent' => self::num( $p->attribute( 'vat_percent' ) ), 'is_vat_included' => (bool)$p->attribute( 'is_vat_included' ),
                'discount_percent' => self::num( $p->attribute( 'discount_percent' ) ),
                'discount_inc_vat' => self::num( $p->attribute( 'discount_price_inc_vat' ) ),
                'discount_ex_vat' => self::num( $p->attribute( 'discount_price_ex_vat' ) ),
                'has_discount' => (bool)$p->attribute( 'has_discount' ) );
            if ( $currency && $code !== '' && strtoupper( $currency ) !== $code )
            {
                $c = self::convert( $code, strtoupper( $currency ), $out['inc_vat'] );
                $out['in_currency'] = array( 'currency' => strtoupper( $currency ), 'inc_vat' => $c,
                    'ex_vat' => self::convert( $code, strtoupper( $currency ), $out['ex_vat'] ) );
            }
            return $out;
        }
        return null;
    }

    /** A converted amount (the rates of the currency table), null when a rate is missing. */
    public static function convert( $from, $to, $value )
    {
        if ( $value === null )
            return null;
        $converter = eZCurrencyConverter::instance();
        if ( $converter->rateValue( $from ) <= 0 || $converter->rateValue( $to ) <= 0 )
            return null;
        return self::num( $converter->convert( $from, $to, $value ) );
    }

    /** A product node: node, object, name, class, prices, options flag. */
    public static function product( eZContentObjectTreeNode $node, $currency = false, $detail = false )
    {
        $object = $node->attribute( 'object' );
        $map = $node->attribute( 'data_map' );
        $out = array( 'node_id' => (int)$node->attribute( 'node_id' ), 'object_id' => (int)$object->attribute( 'id' ),
            'parent_node_id' => (int)$node->attribute( 'parent_node_id' ), 'name' => $node->attribute( 'name' ),
            'class' => $object->attribute( 'class_identifier' ), 'url_alias' => $node->attribute( 'url_alias' ),
            'published' => $object->attribute( 'published' ) ? gmdate( 'c', (int)$object->attribute( 'published' ) ) : null,
            'price' => self::price( $map, $currency ),
            'has_options' => self::optionAttributes( $map ) ? true : false );
        if ( $detail )
        {
            $fields = array();
            foreach ( $map as $id => $a )
            {
                $t = $a->attribute( 'data_type_string' );
                if ( in_array( $t, array( 'ezstring', 'eztext', 'ezinteger', 'ezfloat', 'ezboolean' ) ) )
                    $fields[$id] = $a->attribute( 'data_text' ) !== '' && $a->attribute( 'data_text' ) !== null ? $a->attribute( 'data_text' ) : $a->toString();
                else if ( $t === 'ezxmltext' )
                    $fields[$id] = trim( strip_tags( (string)$a->content()->attribute( 'output' )->attribute( 'output_text' ) ) );
            }
            $out['fields'] = $fields;
            $out['number'] = isset( $fields['product_number'] ) ? $fields['product_number'] : null;
        }
        return $out;
    }

    /** The option datatype attributes (ezoption, ezmultioption, ezmultioption2) of a data map. */
    public static function optionAttributes( $map )
    {
        $list = array();
        foreach ( $map as $id => $a )
            if ( in_array( $a->attribute( 'data_type_string' ), array( 'ezoption', 'ezmultioption', 'ezmultioption2' ) ) )
                $list[$id] = $a;
        return $list;
    }

    /** The options of one option attribute: groups with their choices and additional prices. */
    public static function options( eZContentObjectAttribute $a )
    {
        $c = $a->content();
        $type = $a->attribute( 'data_type_string' );
        $out = array( 'attribute_id' => (int)$a->attribute( 'id' ), 'identifier' => $a->attribute( 'contentclass_attribute_identifier' ), 'type' => $type, 'groups' => array() );
        if ( $type === 'ezoption' )
        {
            $group = array( 'name' => (string)$c->attribute( 'name' ), 'choices' => array() );
            foreach ( (array)$c->attribute( 'option_list' ) as $o )
                $group['choices'][] = array( 'id' => (int)$o['id'], 'value' => $o['value'], 'additional_price' => self::num( isset( $o['additional_price'] ) ? $o['additional_price'] : 0 ) );
            $out['groups'][] = $group;
        }
        else if ( $type === 'ezmultioption' )
        {
            foreach ( (array)$c->attribute( 'multioption_list' ) as $m )
            {
                $group = array( 'id' => (int)$m['id'], 'name' => $m['name'], 'default' => isset( $m['default_option_id'] ) ? $m['default_option_id'] : null, 'choices' => array() );
                foreach ( (array)$m['optionlist'] as $o )
                    $group['choices'][] = array( 'id' => (int)$o['option_id'], 'value' => $o['value'], 'additional_price' => self::num( $o['additional_price'] ) );
                $out['groups'][] = $group;
            }
        }
        return $out;
    }

    /** One line of a basket, wish list or order: product collection item with its options. */
    public static function item( eZProductCollectionItem $i )
    {
        $options = array();
        foreach ( $i->attribute( 'option_list' ) as $o )
            $options[] = array( 'name' => $o->attribute( 'name' ), 'value' => $o->attribute( 'value' ),
                'additional_price' => self::num( $o->attribute( 'price' ) ), 'attribute_id' => (int)$o->attribute( 'object_attribute_id' ) );
        $count = (int)$i->attribute( 'item_count' );
        $unit = (float)$i->attribute( 'price' );
        $vat = (float)$i->attribute( 'vat_value' );
        $disc = (float)$i->attribute( 'discount' );
        $incVat = (bool)$i->attribute( 'is_vat_inc' );
        $unitDiscounted = $unit * ( 100 - $disc ) / 100;
        $exVat = $incVat ? $unitDiscounted / ( 100 + $vat ) * 100 : $unitDiscounted;
        $inc = $incVat ? $unitDiscounted : $unitDiscounted * ( 100 + $vat ) / 100;
        $object = $i->attribute( 'contentobject' );
        return array( 'id' => (int)$i->attribute( 'id' ), 'object_id' => (int)$i->attribute( 'contentobject_id' ),
            'node_id' => $object ? (int)$object->attribute( 'main_node_id' ) : null, 'name' => $i->attribute( 'name' ),
            'count' => $count, 'unit_price' => self::num( $unit ), 'is_vat_included' => $incVat, 'vat_percent' => self::num( $vat ),
            'discount_percent' => self::num( $disc ), 'options' => $options,
            'unit_inc_vat' => self::num( $inc ), 'unit_ex_vat' => self::num( $exVat ),
            'total_inc_vat' => self::num( $inc * $count ), 'total_ex_vat' => self::num( $exVat * $count ) );
    }

    public static function items( eZProductCollection $collection )
    {
        $list = array();
        foreach ( $collection->itemList() as $i )
            $list[] = self::item( $i );
        return $list;
    }

    /** The sums of a list of encoded items. */
    public static function totals( array $items, $currency = '' )
    {
        $inc = 0.0; $ex = 0.0; $count = 0;
        foreach ( $items as $i )
        {
            $inc += $i['total_inc_vat']; $ex += $i['total_ex_vat']; $count += $i['count'];
        }
        return array( 'currency' => $currency, 'lines' => count( $items ), 'quantity' => $count,
            'total_inc_vat' => round( $inc, 4 ), 'total_ex_vat' => round( $ex, 4 ), 'vat' => round( $inc - $ex, 4 ) );
    }

    /** An order. $detail adds the lines, the extra order items and the status history. */
    public static function order( eZOrder $o, $detail = false )
    {
        $out = array( 'id' => (int)$o->attribute( 'id' ), 'order_nr' => (int)$o->attribute( 'order_nr' ), 'user_id' => (int)$o->attribute( 'user_id' ),
            'email' => $o->attribute( 'email' ), 'account_name' => $o->accountName(), 'created' => $o->attribute( 'created' ) ? gmdate( 'c', (int)$o->attribute( 'created' ) ) : null,
            'status_id' => (int)$o->attribute( 'status_id' ), 'status_name' => $o->attribute( 'status_name' ),
            'status_modified' => $o->attribute( 'status_modified' ) ? gmdate( 'c', (int)$o->attribute( 'status_modified' ) ) : null,
            'is_archived' => (bool)$o->attribute( 'is_archived' ), 'is_temporary' => (bool)$o->attribute( 'is_temporary' ),
            'currency' => $o->currencyCode(), 'total_inc_vat' => self::num( $o->totalIncVAT() ), 'total_ex_vat' => self::num( $o->totalExVAT() ) );
        if ( $detail )
        {
            $c = $o->productCollection();
            $out['items'] = $c ? self::items( $c ) : array();
            $out['order_items'] = array();
            foreach ( $o->orderItems() as $oi )
                $out['order_items'][] = array( 'id' => (int)$oi->attribute( 'id' ), 'type' => $oi->attribute( 'type' ), 'description' => $oi->attribute( 'description' ),
                    'price_inc_vat' => self::num( $oi->priceIncVAT() ), 'price_ex_vat' => self::num( $oi->priceExVAT() ), 'vat_percent' => self::num( $oi->attribute( 'vat_value' ) ) );
            $out['history'] = array();
            foreach ( (array)eZOrderStatusHistory::fetchListByOrder( $o->attribute( 'order_nr' ), false ) as $h )
                $out['history'][] = array( 'status_id' => (int)$h['status_id'], 'modifier_id' => (int)$h['modifier_id'], 'modified' => gmdate( 'c', (int)$h['modified'] ) );
        }
        return $out;
    }

    public static function vatType( eZVatType $t )
    {
        return array( 'id' => (int)$t->attribute( 'id' ), 'name' => $t->attribute( 'name' ), 'percentage' => self::num( $t->attribute( 'percentage' ) ),
            'is_dynamic' => (bool)$t->isDynamic() );
    }

    public static function vatRule( eZVatRule $r )
    {
        $ids = array_map( 'intval', (array)$r->attribute( 'product_categories_ids' ) );
        return array( 'id' => (int)$r->attribute( 'id' ), 'country_code' => $r->attribute( 'country_code' ), 'vat_type' => (int)$r->attribute( 'vat_type' ),
            'vat_type_name' => $r->attribute( 'vat_type_name' ), 'product_category_ids' => $ids, 'product_categories' => $r->attribute( 'product_categories_names' ) );
    }

    public static function currency( eZCurrencyData $c )
    {
        return array( 'code' => $c->attribute( 'code' ), 'symbol' => $c->attribute( 'symbol' ), 'locale' => $c->attribute( 'locale' ),
            'status' => (int)$c->attribute( 'status' ) === (int)eZCurrencyData::STATUS_ACTIVE ? 'active' : 'inactive',
            'auto_rate' => self::num( $c->attribute( 'auto_rate_value' ) ), 'custom_rate' => self::num( $c->attribute( 'custom_rate_value' ) ),
            'rate_factor' => self::num( $c->attribute( 'rate_factor' ) ), 'rate' => self::num( $c->attribute( 'rate_value' ) ) );
    }

    public static function discountRule( eZDiscountRule $r )
    {
        return array( 'id' => (int)$r->attribute( 'id' ), 'name' => $r->attribute( 'name' ) );
    }

    public static function subRule( eZDiscountSubRule $s )
    {
        return array( 'id' => (int)$s->attribute( 'id' ), 'rule_id' => (int)$s->attribute( 'discountrule_id' ), 'name' => $s->attribute( 'name' ),
            'percent' => self::num( $s->attribute( 'discount_percent' ) ), 'limitation' => $s->attribute( 'limitation' ) );
    }

    /** A collected form: the collection with its attribute values. */
    public static function collection( eZInformationCollection $c, $detail = false )
    {
        $out = array( 'id' => (int)$c->attribute( 'id' ), 'object_id' => (int)$c->attribute( 'contentobject_id' ), 'creator_id' => (int)$c->attribute( 'creator_id' ),
            'user_identifier' => $c->attribute( 'user_identifier' ), 'created' => $c->attribute( 'created' ) ? gmdate( 'c', (int)$c->attribute( 'created' ) ) : null,
            'modified' => $c->attribute( 'modified' ) ? gmdate( 'c', (int)$c->attribute( 'modified' ) ) : null );
        if ( $detail )
        {
            $out['values'] = array();
            foreach ( $c->informationCollectionAttributes() as $a )
                $out['values'][] = self::collectionValue( $a );
        }
        return $out;
    }

    public static function collectionValue( eZInformationCollectionAttribute $a )
    {
        $ca = $a->attribute( 'contentclass_attribute' );
        $text = $a->attribute( 'data_text' );
        return array( 'attribute' => $ca ? $ca->attribute( 'identifier' ) : null, 'name' => $ca ? $ca->attribute( 'name' ) : null,
            'type' => $ca ? $ca->attribute( 'data_type_string' ) : null,
            'text' => $text, 'int' => (int)$a->attribute( 'data_int' ), 'float' => (float)$a->attribute( 'data_float' ) );
    }

    public static function rssExport( eZRSSExport $e, $detail = false )
    {
        $out = array( 'id' => (int)$e->attribute( 'id' ), 'title' => $e->attribute( 'title' ), 'description' => $e->attribute( 'description' ),
            'access_url' => $e->attribute( 'access_url' ), 'active' => (bool)$e->attribute( 'active' ), 'rss_version' => $e->attribute( 'rss_version' ),
            'number_of_objects' => (int)$e->attribute( 'number_of_objects' ), 'main_node_only' => (bool)$e->attribute( 'main_node_only' ),
            'site_access' => $e->attribute( 'site_access' ), 'modified' => $e->attribute( 'modified' ) ? gmdate( 'c', (int)$e->attribute( 'modified' ) ) : null,
            'url' => '/rss/feed/' . $e->attribute( 'access_url' ) );
        if ( $detail )
        {
            $out['sources'] = array();
            foreach ( (array)$e->itemList() as $i )
                $out['sources'][] = array( 'id' => (int)$i->attribute( 'id' ), 'source_node_id' => (int)$i->attribute( 'source_node_id' ), 'class_id' => (int)$i->attribute( 'class_id' ),
                    'title' => $i->attribute( 'title' ), 'description' => $i->attribute( 'description' ), 'category' => $i->attribute( 'category' ), 'subnodes' => (bool)$i->attribute( 'subnodes' ) );
        }
        return $out;
    }

    public static function rssImport( eZRSSImport $i )
    {
        return array( 'id' => (int)$i->attribute( 'id' ), 'name' => $i->attribute( 'name' ), 'url' => $i->attribute( 'url' ), 'active' => (bool)$i->attribute( 'active' ),
            'destination_node_id' => (int)$i->attribute( 'destination_node_id' ), 'class_id' => (int)$i->attribute( 'class_id' ),
            'object_owner_id' => (int)$i->attribute( 'object_owner_id' ), 'modified' => $i->attribute( 'modified' ) ? gmdate( 'c', (int)$i->attribute( 'modified' ) ) : null );
    }

    /** A content node of the community: node, object, class, author, dates and the text fields of its data map. */
    public static function contentItem( eZContentObjectTreeNode $n )
    {
        $o = $n->attribute( 'object' );
        $fields = array();
        foreach ( $n->attribute( 'data_map' ) as $id => $a )
        {
            $t = $a->attribute( 'data_type_string' );
            if ( in_array( $t, array( 'ezstring', 'eztext', 'ezinteger', 'ezboolean', 'ezfloat', 'ezemail', 'ezdatetime' ) ) )
                $fields[$id] = $a->toString();
        }
        $owner = $o->attribute( 'owner' );
        return array( 'node_id' => (int)$n->attribute( 'node_id' ), 'object_id' => (int)$o->attribute( 'id' ), 'parent_node_id' => (int)$n->attribute( 'parent_node_id' ),
            'class' => $o->attribute( 'class_identifier' ), 'name' => $n->attribute( 'name' ), 'fields' => $fields, 'owner_id' => (int)$o->attribute( 'owner_id' ),
            'owner_name' => $owner ? $owner->attribute( 'name' ) : null, 'is_hidden' => (bool)$n->attribute( 'is_hidden' ), 'is_invisible' => (bool)$n->attribute( 'is_invisible' ),
            'published' => $o->attribute( 'published' ) ? gmdate( 'c', (int)$o->attribute( 'published' ) ) : null,
            'modified' => $o->attribute( 'modified' ) ? gmdate( 'c', (int)$o->attribute( 'modified' ) ) : null,
            'children_count' => (int)$n->attribute( 'children_count' ), 'url_alias' => $n->attribute( 'url_alias' ) );
    }
}
