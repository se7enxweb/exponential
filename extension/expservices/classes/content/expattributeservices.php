<?php
/**
 * Attribute services: ezjscore/call/expattribute::<method>[::arg...]
 *
 * The attributes of content objects and the datatype catalogue: values per datatype, strings, titles, history over
 * the versions, where a datatype is used, and writes that change one or several attributes of an object (published
 * as a new version) or of a draft.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expAttributeServices extends expContentServiceBase
{
    public static $services = array(
        'get' => array( 'summary' => 'One attribute of an object with its value', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'identifier' => 'string', 'language' => 'string' ), 'returns' => 'attribute' ),
        'getById' => array( 'summary' => 'An attribute by its id and version', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'attribute_id' => 'int', 'version' => 'int' ), 'returns' => 'attribute' ),
        'value' => array( 'summary' => 'Only the value of an attribute', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'identifier' => 'string', 'language' => 'string' ), 'returns' => 'value' ),
        'string' => array( 'summary' => 'The attribute as its string form (the format writes accept)', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'identifier' => 'string', 'language' => 'string' ), 'returns' => '{string}' ),
        'title' => array( 'summary' => 'The title text of an attribute', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'identifier' => 'string', 'language' => 'string' ), 'returns' => '{title}' ),
        'hasContent' => array( 'summary' => 'Whether the attribute has content', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'identifier' => 'string', 'language' => 'string' ), 'returns' => '{has_content}' ),
        'dataType' => array( 'summary' => 'The datatype of an attribute', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'identifier' => 'string' ), 'returns' => 'datatype' ),
        'classAttribute' => array( 'summary' => 'The class attribute definition of an attribute', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'identifier' => 'string' ), 'returns' => 'class attribute' ),
        'history' => array( 'summary' => 'The value of an attribute in every version, newest first', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'identifier' => 'string', 'limit' => 'int' ), 'returns' => 'versions' ),
        'languages' => array( 'summary' => 'The value of an attribute in every language', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'identifier' => 'string' ), 'returns' => 'language => value' ),
        'missingRequired' => array( 'summary' => 'Required attributes without content in a version', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'version' => 'int' ), 'returns' => 'identifiers' ),
        'byDataType' => array( 'summary' => 'Attributes of a datatype across current objects (readable ones)', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'datatype' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page' ),
        'dataTypes' => array( 'summary' => 'The datatype catalogue', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'datatypes' ),
        'dataTypeInfo' => array( 'summary' => 'One datatype: name, flags and the classes using it', 'access' => 'user', 'write' => false, 'args' => array( 'datatype' => 'string' ), 'returns' => 'datatype' ),
        'dataTypeClasses' => array( 'summary' => 'The classes that have an attribute of a datatype', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'datatype' => 'string' ), 'returns' => 'classes' ),
        'set' => array( 'summary' => 'Sets one attribute and publishes a new version. POST: value, language', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'identifier' => 'string' ), 'returns' => 'attribute' ),
        'setMany' => array( 'summary' => 'Sets several attributes and publishes a new version. POST: values (json identifier => string), language', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int' ), 'returns' => 'object' ),
        'clear' => array( 'summary' => 'Empties an attribute and publishes a new version', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'identifier' => 'string' ), 'returns' => 'attribute' ),
        'setDraft' => array( 'summary' => 'Sets one attribute of a draft version (not published). POST: value, language', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'version' => 'int', 'identifier' => 'string' ), 'returns' => 'attribute' ),
        'setDraftMany' => array( 'summary' => 'Sets several attributes of a draft version. POST: values (json), language', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'version' => 'int' ), 'returns' => 'attributes' ),
        'validateDraft' => array( 'summary' => 'Checks a draft: required attributes filled, input valid', 'access' => array( 'content', 'edit' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'version' => 'int' ), 'returns' => '{valid, problems}' ),
    );

    /** @return eZContentObjectAttribute */
    protected static function attr( $args, $objectIndex = 0, $versionNr = false )
    {
        $o = self::object( self::arg( $args, $objectIndex, 'int' ) );
        $id = self::arg( $args, $objectIndex + 1, 'string' );
        $lang = self::arg( $args, $objectIndex + 2, 'string', false ) ?: false;
        $map = $o->fetchDataMap( $versionNr, $lang );
        if ( !isset( $map[$id] ) )
            throw new expServiceException( "The object has no attribute '$id'", 404 );
        return $map[$id];
    }

    public static function get( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::exportAttribute( self::attr( $args ) ) );
    }

    public static function getById( $args )
    {
        static::guard( __FUNCTION__ );
        $a = eZContentObjectAttribute::fetch( self::arg( $args, 0, 'int' ), self::arg( $args, 1, 'int' ) );
        if ( !$a )
            throw new expServiceException( 'No such attribute', 404 );
        self::object( $a->attribute( 'contentobject_id' ) );
        return self::ok( self::exportAttribute( $a ) );
    }

    public static function value( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::attributeValue( self::attr( $args ) ) );
    }

    public static function string( $args )
    {
        static::guard( __FUNCTION__ );
        $a = self::attr( $args );
        return self::ok( array( 'string' => in_array( $a->attribute( 'data_type_string' ), array( 'ezuser', 'ezpassword' ), true ) ? '' : (string)$a->toString() ) );
    }

    public static function title( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'title' => (string)self::attr( $args )->title() ) );
    }

    public static function hasContent( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'has_content' => (bool)self::attr( $args )->hasContent() ) );
    }

    public static function dataType( $args )
    {
        static::guard( __FUNCTION__ );
        $a = self::attr( $args );
        return self::ok( self::exportDataType( $a->attribute( 'data_type_string' ) ) );
    }

    protected static function exportDataType( $identifier )
    {
        $dt = eZDataType::create( $identifier );
        if ( !$dt )
            throw new expServiceException( "Datatype '$identifier' is not available", 404 );
        $info = $dt->attribute( 'information' );
        return array( 'identifier' => $identifier, 'name' => $info['name'], 'is_indexable' => (bool)$dt->isIndexable(),
                      'is_information_collector' => (bool)$dt->isInformationCollector(), 'can_be_set_remotely' => !in_array( $identifier, self::$deniedInputTypes, true ) );
    }

    public static function classAttribute( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::exportClassAttribute( self::attr( $args )->contentClassAttribute() ) );
    }

    public static function history( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $id = self::arg( $args, 1, 'string' );
        $limit = min( 50, max( 1, self::arg( $args, 2, 'int', 10 ) ) );
        $out = array();
        foreach ( (array)$o->versions() as $v )
        {
            if ( count( $out ) >= $limit )
                break;
            if ( !$v->canVersionRead() )
                continue;
            $map = $o->fetchDataMap( $v->attribute( 'version' ) );
            if ( isset( $map[$id] ) )
                $out[] = array( 'version' => (int)$v->attribute( 'version' ), 'status' => (int)$v->attribute( 'status' ), 'modified' => self::iso( $v->attribute( 'modified' ) ),
                                'value' => self::attributeValue( $map[$id] ) );
        }
        usort( $out, function ( $a, $b ) { return $b['version'] - $a['version']; } );
        return self::ok( $out );
    }

    public static function languages( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $id = self::arg( $args, 1, 'string' );
        $out = array();
        foreach ( (array)$o->availableLanguages() as $code )
        {
            $map = $o->fetchDataMap( false, $code );
            if ( isset( $map[$id] ) )
                $out[$code] = self::attributeValue( $map[$id] );
        }
        return self::ok( (object)$out );
    }

    public static function missingRequired( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $v = self::arg( $args, 1, 'int', 0 );
        $missing = array();
        foreach ( (array)$o->fetchDataMap( $v ?: false ) as $id => $a )
            if ( $a->contentClassAttribute()->attribute( 'is_required' ) && !$a->hasContent() )
                $missing[] = $id;
        return self::ok( $missing );
    }

    public static function byDataType( $args )
    {
        static::guard( __FUNCTION__ );
        $type = self::arg( $args, 0, 'string' );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $db = eZDB::instance();
        $t = $db->escapeString( $type );
        $from = "FROM ezcontentobject_attribute a, ezcontentobject o WHERE a.contentobject_id = o.id AND a.version = o.current_version AND o.status = 1 AND a.data_type_string = '$t'";
        $total = (int)$db->arrayQuery( "SELECT COUNT(*) AS cnt $from" )[0]['cnt'];
        $rows = $db->arrayQuery( "SELECT a.id AS id, a.contentobject_id AS object_id, a.contentclassattribute_id AS class_attribute_id, a.language_code AS language $from ORDER BY a.id", array( 'limit' => $limit, 'offset' => $offset ) );
        $items = array();
        foreach ( $rows as $r )
        {
            $o = eZContentObject::fetch( (int)$r['object_id'] );
            if ( !$o || !$o->canRead() )
                continue;
            $ca = eZContentClassAttribute::fetch( (int)$r['class_attribute_id'] );
            $items[] = array( 'attribute_id' => (int)$r['id'], 'object_id' => (int)$r['object_id'], 'identifier' => $ca ? $ca->attribute( 'identifier' ) : null,
                              'language' => $r['language'], 'class' => $o->attribute( 'class_identifier' ), 'name' => $o->attribute( 'name' ) );
        }
        return self::ok( $items, array( 'total' => $total, 'offset' => $offset, 'limit' => $limit, 'count' => count( $items ), 'has_more' => $offset + count( $rows ) < $total, 'note' => 'total counts attributes before the read policy is applied' ) );
    }

    public static function dataTypes( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array();
        foreach ( eZDataType::allowedTypes() as $type )
            if ( eZDataType::create( $type ) )
                $out[] = self::exportDataType( $type );
        return self::ok( $out );
    }

    public static function dataTypeInfo( $args )
    {
        static::guard( __FUNCTION__ );
        $type = self::arg( $args, 0, 'string' );
        if ( !in_array( $type, eZDataType::allowedTypes(), true ) )
            throw new expServiceException( "Datatype '$type' is not available", 404 );
        $row = self::exportDataType( $type );
        $row['class_count'] = count( (array)eZContentClass::fetchIDListContainingDatatype( $type ) );
        return self::ok( $row );
    }

    public static function dataTypeClasses( $args )
    {
        static::guard( __FUNCTION__ );
        $type = self::arg( $args, 0, 'string' );
        $out = array();
        foreach ( (array)eZContentClass::fetchIDListContainingDatatype( $type ) as $id )
        {
            $c = eZContentClass::fetch( (int)$id );
            if ( $c )
                $out[] = self::exportClass( $c );
        }
        return self::ok( $out );
    }

    // ------------------------------------------------------------------ writes

    public static function set( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        $id = self::arg( $args, 1, 'string' );
        $o = self::updateObject( $o, array( $id => self::post( 'value', 'string', '' ) ), self::post( 'language', 'string', false ) );
        $map = $o->fetchDataMap();
        return self::ok( self::exportAttribute( $map[$id] ) );
    }

    public static function setMany( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        $o = self::updateObject( $o, (array)self::post( 'values', 'json' ), self::post( 'language', 'string', false ) );
        return self::ok( self::exportObject( $o, true ) );
    }

    public static function clear( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        $id = self::arg( $args, 1, 'string' );
        $o = self::updateObject( $o, array( $id => '' ) );
        $map = $o->fetchDataMap();
        return self::ok( self::exportAttribute( $map[$id] ) );
    }

    protected static function draftAttributes( $args, array $values )
    {
        $o = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        $v = self::version( $o, self::arg( $args, 1, 'int' ), true );
        self::checkInput( $o->contentClass(), $values );
        $lang = self::post( 'language', 'string', '' );
        $attrs = $v->contentObjectAttributes( $lang !== '' ? self::languageCode( $lang, $o ) : false );
        $found = array();
        foreach ( $attrs as $a )
            if ( array_key_exists( $a->attribute( 'contentclass_attribute_identifier' ), $values ) )
                $found[] = $a->attribute( 'contentclass_attribute_identifier' );
        foreach ( $values as $id => $value )
            if ( !in_array( $id, $found, true ) )
                throw new expServiceException( "The draft has no attribute '$id' in that language", 404 );
        self::storeInput( $attrs, $values );
        $v->setAttribute( 'modified', time() );
        $v->store();
        $out = array();
        foreach ( $v->contentObjectAttributes( $lang !== '' ? $lang : false ) as $a )
            if ( array_key_exists( $a->attribute( 'contentclass_attribute_identifier' ), $values ) )
                $out[$a->attribute( 'contentclass_attribute_identifier' )] = self::exportAttribute( $a );
        return $out;
    }

    public static function setDraft( $args )
    {
        static::guard( __FUNCTION__ );
        $id = self::arg( $args, 2, 'string' );
        $out = self::draftAttributes( $args, array( $id => self::post( 'value', 'string', '' ) ) );
        return self::ok( $out[$id] );
    }

    public static function setDraftMany( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::draftAttributes( $args, (array)self::post( 'values', 'json' ) ) );
    }

    public static function validateDraft( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $v = self::version( $o, self::arg( $args, 1, 'int' ) );
        $problems = array();
        foreach ( $v->contentObjectAttributes() as $a )
            if ( $a->contentClassAttribute()->attribute( 'is_required' ) && !$a->hasContent() )
                $problems[] = array( 'identifier' => $a->attribute( 'contentclass_attribute_identifier' ), 'problem' => 'required but empty' );
        return self::ok( array( 'valid' => !$problems, 'problems' => $problems ) );
    }
}
