<?php
/**
 * The base class of a column of the admin subitems list ("Table options").
 *
 * One instance per column key. A column is configured by a [Column_<key>] block in
 * settings/subitemscolumns.ini (or made automatically for a class attribute), and only the
 * columns a user has made visible are computed, only for the rows of the current page.
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

abstract class expSubitemsColumn
{
    protected $key;       // the column key, e.g. "parentnodeid" or "attr:article/intro"
    protected $settings;  // the [Column_<key>] block as an array (Name, Group, Type, SortField, Policy, ...)

    public function __construct( $key, array $settings ) { $this->key = $key; $this->settings = $settings; }
    public function key() { return $this->key; }
    public function settings() { return $this->settings; }
    public function setting( $name, $default = null ) { return isset( $this->settings[$name] ) ? $this->settings[$name] : $default; }
    public function name() { return \ezpI18n::tr( 'design/admin/node/view/full', $this->setting( 'Name', $this->key ) ); }

    /** The raw value: null, a scalar, or a list/map of scalars (JSON-safe). Called only for visible columns. */
    abstract public function value( eZContentObjectTreeNode $node );

    /** The cell's HTML. Default: the escaped value; Type=link -> <a>, Type=bool -> yes/no, list -> comma list. */
    public function html( eZContentObjectTreeNode $node, $value )
    {
        if ( $value === null || $value === '' || $value === array() )
            return '';

        $type = strtolower( (string)$this->setting( 'Type', 'text' ) );

        if ( is_array( $value ) )
        {
            $parts = array();
            foreach ( $value as $item )
                $parts[] = self::escape( self::scalarText( $item ) );
            return implode( ', ', $parts );
        }

        switch ( $type )
        {
            case 'bool':
                return $value
                    ? self::escape( \ezpI18n::tr( 'design/admin/node/view/full', 'Yes' ) )
                    : self::escape( \ezpI18n::tr( 'design/admin/node/view/full', 'No' ) );

            case 'link':
                $href = (string)$value;
                // only http(s), protocol relative and site relative links become anchors
                if ( !preg_match( '#^(https?://|//|/)#i', $href ) )
                    return self::escape( $href );
                return '<a href="' . self::escape( $href ) . '">' . self::escape( $href ) . '</a>';

            case 'date':
            case 'datetime':
                if ( is_numeric( $value ) && (int)$value > 0 )
                {
                    $locale = eZLocale::instance();
                    $text = $type === 'date' ? $locale->formatShortDate( (int)$value ) : $locale->formatShortDateTime( (int)$value );
                    return self::escape( $text );
                }
                return self::escape( (string)$value );

            case 'code':
                return '<code>' . self::escape( (string)$value ) . '</code>';

            case 'html':
                // Type=html: the column itself produced trusted markup (a handler or a template)
                return (string)$value;

            default:
                return self::escape( self::scalarText( $value ) );
        }
    }

    /** The CSV text. Default: the value flattened to one line. */
    public function text( eZContentObjectTreeNode $node, $value )
    {
        if ( $value === null )
            return '';

        if ( is_array( $value ) )
        {
            $parts = array();
            foreach ( $value as $item )
                $parts[] = self::scalarText( $item );
            $text = implode( ', ', $parts );
        }
        else
        {
            $type = strtolower( (string)$this->setting( 'Type', 'text' ) );
            if ( $type === 'bool' )
                $text = $value ? '1' : '0';
            else if ( ( $type === 'date' || $type === 'datetime' ) && is_numeric( $value ) && (int)$value > 0 )
                $text = date( $type === 'date' ? 'Y-m-d' : 'Y-m-d H:i:s', (int)$value );
            else if ( $type === 'html' )
                $text = html_entity_decode( strip_tags( (string)$value ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
            else
                $text = self::scalarText( $value );
        }

        return trim( preg_replace( '/\s+/u', ' ', $text ) );
    }

    /** false, or the subTree() SortBy field for this column: 'path', 'published', 'modified', 'section', 'depth',
     *  'class_identifier', 'class_name', 'priority', 'name', 'modified_subnode', 'node_id', 'contentobject_id',
     *  or array( 'attribute', '<class>/<attribute>' ). Default: from SortField=. */
    public function sortBy()
    {
        $field = trim( (string)$this->setting( 'SortField', '' ) );
        if ( $field === '' || $field === 'false' )
            return false;

        if ( strpos( $field, 'attribute:' ) === 0 )
        {
            $identifier = substr( $field, strlen( 'attribute:' ) );
            if ( preg_match( '#^[a-z0-9_]+/[a-z0-9_]+$#i', $identifier ) )
                return array( 'attribute', $identifier );
            return false;
        }

        if ( in_array( $field, self::sortFields(), true ) )
            return $field;

        return false;
    }

    /** Whether the current user may see this column under $parent: Policy[]=module/function entries all granted. */
    public function isAvailable( eZContentObjectTreeNode $parent )
    {
        $policies = $this->setting( 'Policy', array() );
        if ( !is_array( $policies ) )
            $policies = $policies === '' || $policies === null ? array() : array( $policies );

        foreach ( $policies as $policy )
        {
            foreach ( preg_split( '/[;,]/', (string)$policy ) as $entry )
            {
                $entry = trim( $entry );
                if ( $entry === '' )
                    continue;
                $parts = explode( '/', $entry );
                if ( count( $parts ) !== 2 || $parts[0] === '' || $parts[1] === '' )
                    return false; // a broken entry never grants anything

                // eZUser::currentUser()->hasAccessTo( module, function ), "no" refuses
                if ( !expSubitemsColumnRegistry::hasAccess( $parts[0], $parts[1] ) )
                    return false;
            }
        }
        return true;
    }

    /** The SortBy fields subTree() understands (the plain names; attributes are array( 'attribute', 'class/attr' )). */
    public static function sortFields()
    {
        return array( 'path', 'path_string', 'published', 'modified', 'section', 'depth', 'class_identifier',
                      'class_name', 'priority', 'name', 'modified_subnode', 'node_id', 'contentobject_id', 'visibility' );
    }

    /** HTML escaping for cell output. */
    public static function escape( $text )
    {
        return htmlspecialchars( (string)$text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
    }

    /** A scalar (or nested list) as one line of text. */
    protected static function scalarText( $value )
    {
        if ( $value === null )
            return '';
        if ( is_bool( $value ) )
            return $value ? '1' : '0';
        if ( is_array( $value ) )
        {
            $parts = array();
            foreach ( $value as $k => $item )
                $parts[] = ( is_string( $k ) ? $k . ': ' : '' ) . self::scalarText( $item );
            return implode( ', ', $parts );
        }
        if ( is_object( $value ) )
            return method_exists( $value, '__toString' ) ? (string)$value : '';
        return (string)$value;
    }
}
