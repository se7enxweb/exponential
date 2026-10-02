<?php
/**
 * An automatic subitems column for one class attribute, key attr:<class>/<attribute>.
 *
 * The registry offers one for every attribute of the classes present among the parent's
 * children (AttributeColumns=enabled in subitems.ini), no INI block needed. The value is the
 * attribute's title(), or else its toString() as plain text, cut to MaxLength characters;
 * the HTML is that text escaped. Items of another class have no value. The column is
 * sortable when the datatype has a sort key (or sorts itself).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsAttributeColumn extends expSubitemsColumn
{
    const KEY_PREFIX = 'attr:';
    const MAX_LENGTH = 300;

    /**
     * Splits an attribute column key.
     *
     * @param string $key attr:<class>/<attribute>
     * @return array|null array( class identifier, attribute identifier ), or null when it is not one
     */
    public static function parseKey( $key )
    {
        if ( !is_string( $key ) || !preg_match( '#^attr:([a-z0-9_]{1,100})/([a-z0-9_]{1,100})$#', $key, $m ) )
            return null;
        return array( $m[1], $m[2] );
    }

    /**
     * @param string $classIdentifier
     * @param string $attributeIdentifier
     * @return string the column key
     */
    public static function makeKey( $classIdentifier, $attributeIdentifier )
    {
        return self::KEY_PREFIX . $classIdentifier . '/' . $attributeIdentifier;
    }

    /**
     * The column for a class attribute.
     *
     * @param eZContentClass $class
     * @param eZContentClassAttribute $classAttribute
     * @param array $policies Policy[] entries for this column (subitems.ini AttributeDataTypePolicy[])
     * @return expSubitemsAttributeColumn
     */
    public static function fromClassAttribute( eZContentClass $class, eZContentClassAttribute $classAttribute, array $policies = array() )
    {
        $classIdentifier = $class->attribute( 'identifier' );
        $attributeIdentifier = $classAttribute->attribute( 'identifier' );

        $sortable = false;
        $dataType = $classAttribute->dataType();
        if ( $dataType instanceof eZDataType )
            $sortable = $dataType->customSorting() || $dataType->sortKeyType() !== false;

        $settings = array(
            'Name' => $classAttribute->attribute( 'name' ),
            'Group' => 'Attributes: ' . $class->attribute( 'name' ),
            'Type' => 'text',
            'SortField' => $sortable ? 'attribute:' . $classIdentifier . '/' . $attributeIdentifier : '',
            'Description' => $classIdentifier . '/' . $attributeIdentifier
                             . ( $dataType instanceof eZDataType ? ' (' . $classAttribute->attribute( 'data_type_string' ) . ')' : '' ),
            'Copy' => 'true',
            'Order' => 100000 + (int)$classAttribute->attribute( 'placement' ),
            'ClassIdentifier' => $classIdentifier,
            'AttributeIdentifier' => $attributeIdentifier,
            'DataType' => $classAttribute->attribute( 'data_type_string' ),
            'Policy' => $policies,
        );
        return new static( self::makeKey( $classIdentifier, $attributeIdentifier ), $settings );
    }

    /**
     * The name is the class attribute's own (already translated) name, not an i18n string.
     */
    public function name()
    {
        return (string)$this->setting( 'Name', $this->key );
    }

    public function value( eZContentObjectTreeNode $node )
    {
        $parsed = self::parseKey( $this->key );
        if ( $parsed === null )
            return null;
        list( $classIdentifier, $attributeIdentifier ) = $parsed;

        $object = $node->object();
        if ( !$object instanceof eZContentObject )
            return null;
        if ( $object->attribute( 'class_identifier' ) !== $classIdentifier )
            return null;

        $dataMap = $object->dataMap();
        if ( !isset( $dataMap[$attributeIdentifier] ) )
            return null;
        $attribute = $dataMap[$attributeIdentifier];
        if ( !$attribute->attribute( 'has_content' ) )
            return null;

        return self::attributeText( $attribute );
    }

    /** Loads the data maps of the page's items of this column's class in one query. */
    public function prefetch( array $nodes )
    {
        $parsed = self::parseKey( $this->key );
        if ( $parsed === null )
            return;
        $ofClass = array();
        foreach ( $nodes as $node )
        {
            $object = $node->attribute( 'object' );
            if ( $object instanceof eZContentObject && $object->attribute( 'class_identifier' ) === $parsed[0] )
                $ofClass[] = $node;
        }
        self::prefetchDataMaps( $ofClass );
    }

    /**
     * An attribute as one line of plain text: title(), or else toString() without markup.
     *
     * @param eZContentObjectAttribute $attribute
     * @return string|null
     */
    public static function attributeText( eZContentObjectAttribute $attribute )
    {
        $text = $attribute->title();
        if ( !is_string( $text ) || trim( $text ) === '' )
        {
            $text = $attribute->toString();
            if ( !is_scalar( $text ) )
                return null;
            $text = (string)$text;
            if ( strpos( $text, '<' ) !== false )
                $text = self::markupToText( $text, false, ENT_XML1 );
        }
        $text = self::oneLine( $text );
        if ( $text === '' )
            return null;
        if ( function_exists( 'mb_strlen' ) && mb_strlen( $text, 'UTF-8' ) > self::MAX_LENGTH )
            $text = mb_substr( $text, 0, self::MAX_LENGTH, 'UTF-8' ) . '…';
        return $text;
    }
}
