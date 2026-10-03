<?php
/**
 * ezjscore/call/expproduct::<method>: the products of the shop (content objects whose class has a price attribute):
 * lists, search, one product with its prices in a currency, variations and options.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expProductServices extends expServiceBase
{
    public static $services = array(
        'classes' => array( 'summary' => 'The product classes: content classes with a price attribute', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'list of identifier, id, name, price attribute and type' ),
        'list' => array( 'summary' => 'The products below a node, newest or by name, with prices (optionally in a currency)', 'access' => 'public', 'write' => false,
            'args' => array( 'parent_node_id' => 'int', 'limit' => 'int', 'offset' => 'int', 'currency' => 'string', 'sort' => 'string name|published|price' ),
            'returns' => 'paged list of product descriptors' ),
        'count' => array( 'summary' => 'How many products are below a node', 'access' => 'public', 'write' => false,
            'args' => array( 'parent_node_id' => 'int' ), 'returns' => 'count' ),
        'search' => array( 'summary' => 'Products by words in name, number or description', 'access' => 'public', 'write' => false,
            'args' => array( 'text' => 'string', 'limit' => 'int', 'offset' => 'int', 'currency' => 'string' ), 'returns' => 'paged list of product descriptors' ),
        'view' => array( 'summary' => 'One product by node id with its fields, prices, options', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int', 'currency' => 'string' ), 'returns' => 'product descriptor with fields and options' ),
        'viewByObject' => array( 'summary' => 'One product by object id', 'access' => 'public', 'write' => false,
            'args' => array( 'object_id' => 'int', 'currency' => 'string' ), 'returns' => 'product descriptor with fields and options' ),
        'isProduct' => array( 'summary' => 'Whether a node is a product', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int' ), 'returns' => 'is_product, class' ),
        'price' => array( 'summary' => 'The price of a product, optionally converted into a currency', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int', 'currency' => 'string' ), 'returns' => 'price descriptor' ),
        'prices' => array( 'summary' => 'The price of a product in every active currency with a rate', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int' ), 'returns' => 'list of currency, inc_vat, ex_vat' ),
        'options' => array( 'summary' => 'The options (ezoption/ezmultioption) a buyer can choose, with additional prices', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int' ), 'returns' => 'list of option attributes with groups and choices' ),
        'variations' => array( 'summary' => 'Every combination of the option groups with its resulting price', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int', 'limit' => 'int' ), 'returns' => 'list of choices and price' ),
        'optionPrice' => array( 'summary' => 'The price of a product with chosen options (option attribute id => choice id as JSON)', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int', 'choices' => 'json' ), 'returns' => 'base, options, total' ),
        'latest' => array( 'summary' => 'The most recently published products', 'access' => 'public', 'write' => false,
            'args' => array( 'limit' => 'int', 'currency' => 'string' ), 'returns' => 'list of product descriptors' ),
        'categories' => array( 'summary' => 'The product categories: nodes below a node that hold products, with their product count', 'access' => 'public', 'write' => false,
            'args' => array( 'parent_node_id' => 'int' ), 'returns' => 'list of node, name, product count' ),
        'priceRange' => array( 'summary' => 'The lowest, highest and average price below a node', 'access' => 'public', 'write' => false,
            'args' => array( 'parent_node_id' => 'int' ), 'returns' => 'min, max, avg, count' ),
        'byNumber' => array( 'summary' => 'A product by its product number', 'access' => 'public', 'write' => false,
            'args' => array( 'number' => 'string', 'currency' => 'string' ), 'returns' => 'product descriptor' ),
        'related' => array( 'summary' => 'Other products of the same parent', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int', 'limit' => 'int' ), 'returns' => 'list of product descriptors' ),
        'vat' => array( 'summary' => 'The VAT of a product: the type selected and the percentage', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int' ), 'returns' => 'vat type and percent' ),
        'setPrice' => array( 'summary' => 'Sets the price of a product (publishes a new version)', 'access' => array( 'content', 'edit' ), 'write' => true,
            'args' => array( 'node_id' => 'int', 'price' => 'string POST' ), 'returns' => 'the product with its new price' ),
    );

    /** @return array the identifiers of the product classes */
    public static function classIdentifiers()
    {
        $list = array();
        foreach ( (array)eZShopFunctions::productClassList() as $class )
            $list[] = $class->attribute( 'identifier' );
        return $list;
    }

    protected static function rootNode()
    {
        return 1;
    }

    /** The top level node needs no read check: the tree fetches apply the read policies. */
    protected static function node( $nodeId, $function = 'read' )
    {
        if ( (int)$nodeId === 1 && $function === 'read' )
            return eZContentObjectTreeNode::fetch( 1 );
        return parent::node( $nodeId, $function );
    }

    /** The product node of the id, 404 when it is not a product, 403 when it is not readable. */
    protected static function product( $nodeId )
    {
        $node = self::node( $nodeId, 'read' );
        if ( !eZShopFunctions::isProductObject( $node->attribute( 'object' ) ) )
            throw new expServiceException( "Node $nodeId is not a product", 404 );
        return $node;
    }

    protected static function currency( array $args, $i )
    {
        $c = self::arg( $args, $i, 'string', '' );
        $c = strtoupper( $c );
        if ( $c !== '' && !preg_match( '/^[A-Z]{3}$/', $c ) )
            throw new expServiceException( 'The currency is a three letter code', 400 );
        return $c;
    }

    public static function classes( array $args )
    {
        self::guard( 'classes' );
        $out = array();
        foreach ( (array)eZShopFunctions::productClassList() as $class )
            $out[] = array( 'identifier' => $class->attribute( 'identifier' ), 'id' => (int)$class->attribute( 'id' ), 'name' => $class->attribute( 'name' ),
                'price_attribute' => eZShopFunctions::priceAttributeIdentifier( $class ), 'type' => eZShopFunctions::productTypeByClass( $class ) );
        return self::ok( $out );
    }

    public static function list( array $args )
    {
        self::guard( 'list' );
        $parent = self::arg( $args, 0, 'int', self::rootNode() );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $currency = self::currency( $args, 3 );
        $sort = self::arg( $args, 4, 'string', 'name' );
        $classes = self::classIdentifiers();
        if ( !$classes )
            return self::page( array(), 0, $offset, $limit );
        self::node( $parent, 'read' );
        $sortBy = $sort === 'published' ? array( array( 'published', false ) ) : array( array( 'name', true ) );
        $params = array( 'ClassFilterType' => 'include', 'ClassFilterArray' => $classes, 'SortBy' => $sortBy, 'Limit' => $limit, 'Offset' => $offset );
        if ( $sort === 'price' )
        {
            $all = eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => $classes, 'Limit' => 1000 ), $parent );
            $items = array();
            foreach ( (array)$all as $n )
                $items[] = expCommerceExport::product( $n, $currency );
            usort( $items, function ( $a, $b ) { return ( $a['price']['inc_vat'] ?? 0 ) <=> ( $b['price']['inc_vat'] ?? 0 ); } );
            return self::pageOf( $items, $args, 1, 2 );
        }
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $params, $parent );
        $total = eZContentObjectTreeNode::subTreeCountByNodeID( $params, $parent );
        $items = array();
        foreach ( (array)$nodes as $n )
            $items[] = expCommerceExport::product( $n, $currency );
        return self::page( $items, $total, $offset, $limit );
    }

    public static function count( array $args )
    {
        self::guard( 'count' );
        $parent = self::arg( $args, 0, 'int', self::rootNode() );
        $classes = self::classIdentifiers();
        $n = $classes ? eZContentObjectTreeNode::subTreeCountByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => $classes ), $parent ) : 0;
        return self::ok( array( 'count' => (int)$n, 'parent_node_id' => $parent ) );
    }

    public static function search( array $args )
    {
        self::guard( 'search' );
        $text = trim( self::arg( $args, 0, 'string' ) );
        if ( $text === '' )
            throw new expServiceException( 'The search text is empty', 400 );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $currency = self::currency( $args, 3 );
        $classes = self::classIdentifiers();
        $items = array();
        // the name and the product number are compared on every product: the shop is small and this needs no index
        $needle = mb_strtolower( $text );
        $nodes = $classes ? eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => $classes, 'Limit' => 2000 ), self::rootNode() ) : array();
        foreach ( (array)$nodes as $n )
        {
            $hay = $n->attribute( 'name' );
            $map = $n->attribute( 'data_map' );
            foreach ( array( 'product_number', 'short_description', 'description', 'caption' ) as $f )
                if ( isset( $map[$f] ) )
                    $hay .= ' ' . strip_tags( (string)$map[$f]->toString() );
            if ( mb_strpos( mb_strtolower( $hay ), $needle ) !== false )
                $items[] = expCommerceExport::product( $n, $currency );
        }
        return self::pageOf( $items, array( 1 => $limit, 2 => $offset ), 1, 2 );
    }

    public static function view( array $args )
    {
        self::guard( 'view' );
        $node = self::product( self::arg( $args, 0, 'int' ) );
        $out = expCommerceExport::product( $node, self::currency( $args, 1 ), true );
        $out['options'] = array();
        foreach ( expCommerceExport::optionAttributes( $node->attribute( 'data_map' ) ) as $a )
            $out['options'][] = expCommerceExport::options( $a );
        return self::ok( $out );
    }

    public static function viewByObject( array $args )
    {
        self::guard( 'viewByObject' );
        $object = eZContentObject::fetch( self::arg( $args, 0, 'int' ) );
        if ( !$object instanceof eZContentObject )
            throw new expServiceException( 'No such object', 404 );
        return self::view( array( $object->attribute( 'main_node_id' ), self::arg( $args, 1, 'string', '' ) ) );
    }

    public static function isProduct( array $args )
    {
        self::guard( 'isProduct' );
        $node = self::node( self::arg( $args, 0, 'int' ), 'read' );
        return self::ok( array( 'is_product' => (bool)eZShopFunctions::isProductObject( $node->attribute( 'object' ) ), 'class' => $node->attribute( 'class_identifier' ) ) );
    }

    public static function price( array $args )
    {
        self::guard( 'price' );
        $node = self::product( self::arg( $args, 0, 'int' ) );
        return self::ok( expCommerceExport::price( $node->attribute( 'data_map' ), self::currency( $args, 1 ) ) );
    }

    public static function prices( array $args )
    {
        self::guard( 'prices' );
        $node = self::product( self::arg( $args, 0, 'int' ) );
        $base = expCommerceExport::price( $node->attribute( 'data_map' ) );
        $out = array();
        if ( $base )
        {
            $out[] = array( 'currency' => $base['currency'], 'inc_vat' => $base['inc_vat'], 'ex_vat' => $base['ex_vat'], 'base' => true );
            foreach ( (array)eZCurrencyData::fetchList( array( 'status' => eZCurrencyData::STATUS_ACTIVE ), true ) as $code => $c )
            {
                if ( $code === $base['currency'] )
                    continue;
                $inc = expCommerceExport::convert( $base['currency'], $code, $base['inc_vat'] );
                if ( $inc !== null )
                    $out[] = array( 'currency' => $code, 'inc_vat' => $inc, 'ex_vat' => expCommerceExport::convert( $base['currency'], $code, $base['ex_vat'] ), 'base' => false );
            }
        }
        return self::ok( $out );
    }

    public static function options( array $args )
    {
        self::guard( 'options' );
        $node = self::product( self::arg( $args, 0, 'int' ) );
        $out = array();
        foreach ( expCommerceExport::optionAttributes( $node->attribute( 'data_map' ) ) as $a )
            $out[] = expCommerceExport::options( $a );
        return self::ok( $out );
    }

    public static function variations( array $args )
    {
        self::guard( 'variations' );
        $node = self::product( self::arg( $args, 0, 'int' ) );
        $limit = min( self::arg( $args, 1, 'int', 100 ), 500 );
        $base = expCommerceExport::price( $node->attribute( 'data_map' ) );
        $basePrice = $base ? $base['inc_vat'] : 0.0;
        $groups = array();
        foreach ( expCommerceExport::optionAttributes( $node->attribute( 'data_map' ) ) as $a )
            foreach ( expCommerceExport::options( $a )['groups'] as $g )
                if ( $g['choices'] )
                    $groups[] = array( 'attribute_id' => (int)$a->attribute( 'id' ), 'name' => isset( $g['name'] ) ? $g['name'] : '', 'choices' => $g['choices'] );
        $combos = array( array( 'choices' => array(), 'price' => $basePrice ) );
        foreach ( $groups as $g )
        {
            $next = array();
            foreach ( $combos as $c )
                foreach ( $g['choices'] as $ch )
                {
                    $c2 = $c;
                    $c2['choices'][] = array( 'attribute_id' => $g['attribute_id'], 'group' => $g['name'], 'choice_id' => $ch['id'], 'value' => $ch['value'] );
                    $c2['price'] = round( $c['price'] + (float)$ch['additional_price'], 4 );
                    $next[] = $c2;
                    if ( count( $next ) >= $limit )
                        break 2;
                }
            $combos = $next;
        }
        return self::ok( array_slice( $combos, 0, $limit ), array( 'base_price' => $basePrice, 'groups' => count( $groups ) ) );
    }

    public static function optionPrice( array $args )
    {
        self::guard( 'optionPrice' );
        $node = self::product( self::arg( $args, 0, 'int' ) );
        $choices = self::arg( $args, 1, 'json', array() );
        $base = expCommerceExport::price( $node->attribute( 'data_map' ) );
        $basePrice = $base ? $base['inc_vat'] : 0.0;
        $extra = 0.0;
        $used = array();
        foreach ( expCommerceExport::optionAttributes( $node->attribute( 'data_map' ) ) as $a )
        {
            $aid = (int)$a->attribute( 'id' );
            if ( !isset( $choices[$aid] ) )
                continue;
            foreach ( (array)$choices[$aid] as $wanted )
                foreach ( expCommerceExport::options( $a )['groups'] as $g )
                    foreach ( $g['choices'] as $ch )
                        if ( (string)$ch['id'] === (string)$wanted )
                        {
                            $extra += (float)$ch['additional_price'];
                            $used[] = array( 'attribute_id' => $aid, 'choice_id' => $ch['id'], 'value' => $ch['value'], 'additional_price' => $ch['additional_price'] );
                        }
        }
        return self::ok( array( 'base' => $basePrice, 'options' => $used, 'additional' => round( $extra, 4 ), 'total' => round( $basePrice + $extra, 4 ),
            'currency' => $base ? $base['currency'] : null ) );
    }

    public static function latest( array $args )
    {
        self::guard( 'latest' );
        $limit = min( self::arg( $args, 0, 'int', 10 ), 100 );
        $currency = self::currency( $args, 1 );
        $classes = self::classIdentifiers();
        $nodes = $classes ? eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => $classes,
            'SortBy' => array( array( 'published', false ), array( 'node_id', false ) ), 'Limit' => $limit ), self::rootNode() ) : array();
        $out = array();
        foreach ( (array)$nodes as $n )
            $out[] = expCommerceExport::product( $n, $currency );
        return self::ok( $out );
    }

    public static function categories( array $args )
    {
        self::guard( 'categories' );
        $parent = self::arg( $args, 0, 'int', self::rootNode() );
        self::node( $parent, 'read' );
        $classes = self::classIdentifiers();
        $out = array();
        foreach ( (array)eZContentObjectTreeNode::subTreeByNodeID( array( 'Depth' => 1, 'DepthOperator' => 'eq', 'Limit' => 500 ), $parent ) as $n )
        {
            $c = $classes ? (int)eZContentObjectTreeNode::subTreeCountByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => $classes ), $n->attribute( 'node_id' ) ) : 0;
            if ( $c > 0 )
                $out[] = array( 'node_id' => (int)$n->attribute( 'node_id' ), 'name' => $n->attribute( 'name' ), 'class' => $n->attribute( 'class_identifier' ), 'products' => $c );
        }
        return self::ok( $out );
    }

    public static function priceRange( array $args )
    {
        self::guard( 'priceRange' );
        $parent = self::arg( $args, 0, 'int', self::rootNode() );
        self::node( $parent, 'read' );
        $classes = self::classIdentifiers();
        $prices = array();
        $nodes = $classes ? eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => $classes, 'Limit' => 2000 ), $parent ) : array();
        foreach ( (array)$nodes as $n )
        {
            $p = expCommerceExport::price( $n->attribute( 'data_map' ) );
            if ( $p )
                $prices[] = $p['inc_vat'];
        }
        return self::ok( array( 'count' => count( $prices ), 'min' => $prices ? min( $prices ) : null, 'max' => $prices ? max( $prices ) : null,
            'avg' => $prices ? round( array_sum( $prices ) / count( $prices ), 4 ) : null ) );
    }

    public static function byNumber( array $args )
    {
        self::guard( 'byNumber' );
        $number = trim( self::arg( $args, 0, 'string' ) );
        $classes = self::classIdentifiers();
        $nodes = $classes ? eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => $classes, 'Limit' => 2000 ), self::rootNode() ) : array();
        foreach ( (array)$nodes as $n )
        {
            $map = $n->attribute( 'data_map' );
            if ( isset( $map['product_number'] ) && trim( $map['product_number']->toString() ) === $number )
                return self::ok( expCommerceExport::product( $n, self::currency( $args, 1 ), true ) );
        }
        throw new expServiceException( 'No product with that number', 404 );
    }

    public static function related( array $args )
    {
        self::guard( 'related' );
        $node = self::product( self::arg( $args, 0, 'int' ) );
        $limit = min( self::arg( $args, 1, 'int', 10 ), 100 );
        $out = array();
        foreach ( (array)eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => self::classIdentifiers(),
            'Depth' => 1, 'DepthOperator' => 'eq', 'Limit' => $limit + 1 ), $node->attribute( 'parent_node_id' ) ) as $n )
            if ( $n->attribute( 'node_id' ) != $node->attribute( 'node_id' ) && count( $out ) < $limit )
                $out[] = expCommerceExport::product( $n );
        return self::ok( $out );
    }

    public static function vat( array $args )
    {
        self::guard( 'vat' );
        $node = self::product( self::arg( $args, 0, 'int' ) );
        foreach ( $node->attribute( 'data_map' ) as $id => $a )
        {
            if ( !eZShopFunctions::isProductDatatype( $a->attribute( 'data_type_string' ) ) )
                continue;
            $p = $a->content();
            $t = $p->attribute( 'selected_vat_type' );
            return self::ok( array( 'attribute' => $id, 'vat_type' => $t ? expCommerceExport::vatType( $t ) : null,
                'percent' => expCommerceExport::num( $p->attribute( 'vat_percent' ) ), 'is_vat_included' => (bool)$p->attribute( 'is_vat_included' ) ) );
        }
        throw new expServiceException( 'The product has no price attribute', 404 );
    }

    public static function setPrice( array $args )
    {
        self::guard( 'setPrice' );
        $node = self::product( self::arg( $args, 0, 'int' ) );
        if ( !$node->canEdit() )
            throw new expServiceException( 'No edit access to this product', 403 );
        $price = self::post( 'price', 'string' );
        if ( !is_numeric( $price ) || (float)$price < 0 )
            throw new expServiceException( 'The price must be a number of 0 or more', 422 );
        $map = $node->attribute( 'data_map' );
        $identifier = null;
        foreach ( $map as $id => $a )
            if ( $a->attribute( 'data_type_string' ) === 'ezprice' )
                $identifier = $id;
        if ( $identifier === null )
            throw new expServiceException( 'The product has no ezprice attribute', 422 );
        $parts = explode( '|', $map[$identifier]->toString() );
        $parts = array_pad( $parts, 3, '' );
        $parts[0] = (string)(float)$price;
        $before = expCommerceExport::price( $map );
        $ok = eZContentFunctions::updateAndPublishObject( $node->attribute( 'object' ), array( 'attributes' => array( $identifier => implode( '|', $parts ) ) ) );
        if ( !$ok )
            throw new expServiceException( 'The product could not be updated', 422 );
        $fresh = eZContentObjectTreeNode::fetch( $node->attribute( 'node_id' ) );
        self::audit( 'content.object.change', array( 'object' => array( 'type' => 'object', 'id' => (int)$node->attribute( 'contentobject_id' ) ), 'verb' => 'price',
            'before' => array( 'price' => $before ? $before['price'] : null ), 'after' => array( 'price' => (float)$price ) ) );
        return self::ok( expCommerceExport::product( $fresh ) );
    }
}
