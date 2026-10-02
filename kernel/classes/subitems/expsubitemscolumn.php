<?php
/**
 * The base class of a column of the admin subitems list ("Table options").
 *
 * One instance per column key. A column is configured by a [Column_<key>] block in
 * settings/subitemscolumns.ini (or made automatically for a class attribute), and only the
 * columns a user has made visible are computed, only for the rows of the current page.
 *
 * The class is also the one place for how values are shown: the cell HTML (html()) and the CSV
 * text (text()) by Type=, and the formatting helpers every column shares (escape(), oneLine(),
 * markupToText(), formatDate(), formatAge(), days(), formatBytes()).
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

abstract class expSubitemsColumn
{
    /** The i18n context of every string a column shows. */
    const I18N_CONTEXT = 'design/admin/node/view/full';

    /** The most entries one memo bucket keeps (one admin page is at most a few hundred rows). */
    const MEMO_LIMIT = 2000;

    /** @var array per-request memo shared by every column: bucket => key => value */
    private static $memo = array();

    protected $key;       // the column key, e.g. "parentnodeid" or "attr:article/intro"
    protected $settings;  // the [Column_<key>] block as an array (Name, Group, Type, SortField, Policy, ...)

    public function __construct( $key, array $settings ) { $this->key = $key; $this->settings = $settings; }
    public function key() { return $this->key; }
    public function settings() { return $this->settings; }
    public function setting( $name, $default = null ) { return isset( $this->settings[$name] ) ? $this->settings[$name] : $default; }
    public function name() { return self::tr( $this->setting( 'Name', $this->key ) ); }

    /** The raw value: null, a scalar, or a list/map of scalars (JSON-safe). Called only for visible columns. */
    abstract public function value( eZContentObjectTreeNode $node );

    /**
     * Loads in bulk, for the rows of one page, what value() will read, so that a column that
     * would cost a query per row costs one query per page. Called once per page with the page's
     * nodes, before value() is called for any of them (the rows function and the CSV export do).
     * What it loads must be exactly what value() would load on its own: the values never depend
     * on whether prefetch() ran. The default loads nothing.
     *
     * @param eZContentObjectTreeNode[] $nodes
     */
    public function prefetch( array $nodes )
    {
    }

    /**
     * Runs $compute once per request for ($bucket, $key) and returns the remembered result.
     * Every column shares this memo, so the columns of one row that read the same data (the
     * version rows, the data map, the URL alias rows ...) share one query, and prefetch() fills
     * the same buckets for a whole page.
     *
     * @param string $bucket
     * @param string|int $key
     * @param callable $compute fn(): the value
     * @return mixed
     */
    protected static function memo( $bucket, $key, $compute )
    {
        if ( !self::isMemoised( $bucket, $key ) )
            self::remember( $bucket, $key, $compute() );
        return self::$memo[$bucket][$key];
    }

    /** Whether the memo holds ($bucket, $key). */
    protected static function isMemoised( $bucket, $key )
    {
        return isset( self::$memo[$bucket] ) && array_key_exists( $key, self::$memo[$bucket] );
    }

    /** Puts a value in the memo (prefetch() does, for every row of a page). */
    protected static function remember( $bucket, $key, $value )
    {
        // keep the memo bounded: one admin page is at most a few hundred rows
        if ( isset( self::$memo[$bucket] ) && count( self::$memo[$bucket] ) > self::MEMO_LIMIT && !array_key_exists( $key, self::$memo[$bucket] ) )
            self::$memo[$bucket] = array();
        self::$memo[$bucket][$key] = $value;
    }

    /**
     * Forgets every memo. The rows function and the CSV export call it first, so a persistent
     * worker never answers from an earlier request; tests call it between cases.
     */
    public static function resetMemo()
    {
        self::$memo = array();
    }

    /**
     * Loads the data maps of the nodes' objects in one query, for every object not loaded yet in
     * this request: the attributes of each object's current version in its current language, as
     * eZContentObject::fillNodeListAttributes() (subTree()'s LoadDataMap) does. That function's
     * query names every object as (id, version, language) in one OR list, which SQLite cannot
     * serve from an index (it took longer than one query per row); this one selects by object id
     * and keeps the same rows. On MongoDB the kernel's function is used.
     *
     * @param eZContentObjectTreeNode[] $nodes
     */
    protected static function prefetchDataMaps( array $nodes )
    {
        $objects = array();
        foreach ( $nodes as $node )
        {
            $object = $node->attribute( 'object' );
            if ( !$object instanceof eZContentObject || (int)$object->attribute( 'id' ) <= 0 )
                continue;
            if ( !self::isMemoised( 'datamaploaded', self::dataMapKey( $object ) ) )
                $objects[(int)$object->attribute( 'id' )] = $object;
        }
        if ( !$objects )
            return;

        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
        {
            eZContentObject::fillNodeListAttributes( array_values( $objects ) );
            foreach ( $objects as $object )
                self::remember( 'datamaploaded', self::dataMapKey( $object ), true );
            return;
        }

        $objectCondition = $db->generateSQLINStatement( array_keys( $objects ), 'ezcontentobject_attribute.contentobject_id', false, true, 'int' );
        $rows = $db->arrayQuery(
            "SELECT ezcontentobject_attribute.*, ezcontentclass_attribute.identifier AS identifier
               FROM ezcontentobject_attribute
                    INNER JOIN ezcontentobject ON ( ezcontentobject.id = ezcontentobject_attribute.contentobject_id
                                                    AND ezcontentobject.current_version = ezcontentobject_attribute.version )
                    INNER JOIN ezcontentclass_attribute ON ( ezcontentclass_attribute.id = ezcontentobject_attribute.contentclassattribute_id
                                                             AND ezcontentclass_attribute.version = 0 )
              WHERE $objectCondition
              ORDER BY ezcontentobject_attribute.contentobject_id, ezcontentclass_attribute.placement ASC" );
        if ( !is_array( $rows ) )
            return;

        $byObject = array();
        foreach ( $rows as $row )
        {
            $object = $objects[(int)$row['contentobject_id']] ?? null;
            // the version and language the object itself would load (fetchDataMap())
            if ( !$object || (int)$row['version'] !== (int)$object->attribute( 'current_version' )
                 || $row['language_code'] !== $object->currentLanguage() )
                continue;
            $attribute = new eZContentObjectAttribute( $row );
            $attribute->setContentClassAttributeIdentifier( $row['identifier'] );
            $byObject[(int)$row['contentobject_id']][] = $attribute;
        }
        foreach ( $byObject as $id => $attributes )
        {
            $object = $objects[$id];
            $object->setContentObjectAttributes( $attributes, $object->attribute( 'current_version' ), $object->currentLanguage() );
            self::remember( 'datamaploaded', self::dataMapKey( $object ), true );
        }
    }

    /** The memo key of an object's data map: id, current version, current language. */
    protected static function dataMapKey( eZContentObject $object )
    {
        return $object->attribute( 'id' ) . '/' . $object->attribute( 'current_version' ) . '/' . $object->currentLanguage();
    }

    /** The cell's HTML. Default: the escaped value; Type=link -> <a>, Type=bool -> yes/no, list -> comma list. */
    public function html( eZContentObjectTreeNode $node, $value )
    {
        if ( $value === null || $value === '' || $value === array() )
            return '';

        if ( is_array( $value ) )
        {
            $parts = array();
            foreach ( $value as $item )
                $parts[] = self::escape( self::scalarText( $item ) );
            return implode( ', ', $parts );
        }

        switch ( $type = $this->type() )
        {
            case 'bool':
                return self::escape( $value ? ezpI18n::tr( 'design/admin/node/view/full', 'Yes' )
                                            : ezpI18n::tr( 'design/admin/node/view/full', 'No' ) );

            case 'link':
                return self::linkHtml( (string)$value );

            case 'date':
            case 'datetime':
                return self::escape( self::isTimestamp( $value ) ? self::formatDate( $value, $type ) : (string)$value );

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
            return self::oneLine( implode( ', ', $parts ) );
        }

        $type = $this->type();
        if ( $type === 'bool' )
            $text = $value ? '1' : '0';
        else if ( ( $type === 'date' || $type === 'datetime' ) && self::isTimestamp( $value ) )
            $text = date( $type === 'date' ? 'Y-m-d' : 'Y-m-d H:i:s', (int)$value );
        else if ( $type === 'html' )
            $text = self::markupToText( (string)$value );
        else
            $text = self::scalarText( $value );

        return self::oneLine( $text );
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
            return preg_match( '#^[a-z0-9_]+/[a-z0-9_]+$#i', $identifier ) ? array( 'attribute', $identifier ) : false;
        }

        return in_array( $field, self::sortFields(), true ) ? $field : false;
    }

    /**
     * Whether the current user may see this column under $parent: every Policy[]=module/function
     * entry (several in one line with ; between them) is granted. A malformed entry never grants.
     */
    public function isAvailable( eZContentObjectTreeNode $parent )
    {
        foreach ( expSubitemsColumnRegistry::splitList( $this->setting( 'Policy', array() ) ) as $entry )
        {
            $parts = explode( '/', $entry );
            if ( count( $parts ) !== 2 || $parts[0] === '' || $parts[1] === '' )
                return false;
            // eZUser::currentUser()->hasAccessTo( module, function ), "no" refuses
            if ( !expSubitemsColumnRegistry::hasAccess( $parts[0], $parts[1] ) )
                return false;
        }
        return true;
    }

    /** The column's Type=, lowercase (text when unset). */
    protected function type()
    {
        return strtolower( (string)$this->setting( 'Type', 'text' ) );
    }

    /** The SortBy fields subTree() understands (the plain names; attributes are array( 'attribute', 'class/attr' )). */
    public static function sortFields()
    {
        return array( 'path', 'path_string', 'published', 'modified', 'section', 'depth', 'class_identifier',
                      'class_name', 'priority', 'name', 'modified_subnode', 'node_id', 'contentobject_id', 'visibility' );
    }

    /**
     * A string that is not a literal (a setting, a computed unit) translated in the columns'
     * context. Literal strings are written as ezpI18n::tr( 'design/admin/node/view/full', '...' )
     * in full, so the translation tools find them.
     */
    public static function tr( $text, array $arguments = array() )
    {
        return ezpI18n::tr( self::I18N_CONTEXT, $text, null, $arguments );
    }

    /** HTML escaping for cell output. */
    public static function escape( $text )
    {
        return htmlspecialchars( (string)$text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
    }

    /** Text on one line: runs of white space become one space, the ends are trimmed. */
    public static function oneLine( $text )
    {
        return trim( preg_replace( '/\s+/u', ' ', (string)$text ) );
    }

    /**
     * Markup (HTML, XML text, rich text) as plain text: tags left out, entities decoded. With
     * $separateBlocks, words stay apart where block elements (paragraph, li, td, br ...) end.
     *
     * @param string $markup
     * @param bool $separateBlocks
     * @param int $entityFlags html_entity_decode() flags (ENT_HTML5 by default, ENT_XML1 for stored XML)
     * @return string not folded to one line; see oneLine()
     */
    public static function markupToText( $markup, $separateBlocks = false, $entityFlags = ENT_HTML5 )
    {
        $markup = (string)$markup;
        if ( $separateBlocks )
            $markup = preg_replace( '#<(/?(paragraph|para|p|li|header|section|title|td|th|br|line)\b[^>]*)>#i', ' <$1>', $markup );
        return html_entity_decode( strip_tags( $markup ), ENT_QUOTES | $entityFlags, 'UTF-8' );
    }

    /** An anchor for http(s)://, // and / addresses; anything else (javascript: ...) stays escaped text. */
    public static function linkHtml( $href )
    {
        $href = (string)$href;
        if ( !preg_match( '#^(https?://|//|/)#i', $href ) )
            return self::escape( $href );
        return '<a href="' . self::escape( $href ) . '">' . self::escape( $href ) . '</a>';
    }

    /** Whether a value is a timestamp a date column can format (numeric, after 1970). */
    public static function isTimestamp( $value )
    {
        return is_numeric( $value ) && (int)$value > 0;
    }

    /** A timestamp in the locale's short date ($type date) or short date and time (datetime). */
    public static function formatDate( $timestamp, $type = 'datetime' )
    {
        $locale = eZLocale::instance();
        return $type === 'date' ? $locale->formatShortDate( (int)$timestamp ) : $locale->formatShortDateTime( (int)$timestamp );
    }

    /** Whole days from $time until $now (default: now), null for no time. */
    public static function days( $time, $now = null )
    {
        if ( !$time )
            return null;
        $now = $now === null ? time() : (int)$now;
        return (int)floor( ( $now - (int)$time ) / 86400 );
    }

    /** "3 days ago", "in 2 hours", "just now" for a timestamp, relative to $now; null for no time. */
    public static function formatAge( $timestamp, $now = null )
    {
        $timestamp = (int)$timestamp;
        if ( $timestamp <= 0 )
            return null;
        $now = $now === null ? time() : (int)$now;
        $diff = $now - $timestamp;
        $future = $diff < 0;
        $diff = abs( $diff );

        $units = array( array( 31536000, 'year', 'years' ), array( 2592000, 'month', 'months' ),
                        array( 604800, 'week', 'weeks' ), array( 86400, 'day', 'days' ),
                        array( 3600, 'hour', 'hours' ), array( 60, 'minute', 'minutes' ) );
        foreach ( $units as $unit )
        {
            if ( $diff >= $unit[0] )
            {
                $n = (int)floor( $diff / $unit[0] );
                $text = $n . ' ' . self::tr( $n === 1 ? $unit[1] : $unit[2] );
                return $future
                    ? ezpI18n::tr( 'design/admin/node/view/full', 'in %1', null, array( $text ) )
                    : ezpI18n::tr( 'design/admin/node/view/full', '%1 ago', null, array( $text ) );
            }
        }
        return ezpI18n::tr( 'design/admin/node/view/full', 'just now' );
    }

    /** A human readable byte size: 1.4 MB, 820 kB, 12 B. */
    public static function formatBytes( $bytes )
    {
        $bytes = (float)$bytes;
        foreach ( array( 'B', 'kB', 'MB', 'GB', 'TB' ) as $i => $unit )
        {
            if ( $bytes < 1024 || $unit === 'TB' )
                return ( $i === 0 ? (string)(int)$bytes : number_format( $bytes, $bytes < 10 ? 1 : 0 ) ) . ' ' . $unit;
            $bytes /= 1024;
        }
        return '';
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
