<?php
/**
 * Class services: ezjscore/call/expclass::<method>[::arg...]
 *
 * Content class definitions: reads (list, by identifier and remote id, attributes, groups, usage, datatypes) and the
 * draft workflow of the class editor as writes: createDraft or editDraft, change the draft and its attributes,
 * publishDraft or discardDraft, plus copy and remove. A draft is the class in the temporary version of the kernel,
 * the same one the admin class editor works on.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expClassServices extends expContentServiceBase
{
    public static $services = array(
        'list' => array( 'summary' => 'Content classes, optionally of one group', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'group_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of classes' ),
        'count' => array( 'summary' => 'Number of classes', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array(), 'returns' => '{count}' ),
        'identifiers' => array( 'summary' => 'Light map of class id => identifier and name', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'list' ),
        'get' => array( 'summary' => 'A class with its attributes and groups (id or identifier)', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'class' => 'string' ), 'returns' => 'class' ),
        'getByRemoteId' => array( 'summary' => 'A class by its remote id', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'remote_id' => 'string' ), 'returns' => 'class' ),
        'search' => array( 'summary' => 'Classes whose name or identifier contains a text', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'text' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of classes' ),
        'attributes' => array( 'summary' => 'The attributes of a class in order', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'class' => 'string' ), 'returns' => 'attributes' ),
        'attribute' => array( 'summary' => 'One class attribute by id', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'attribute_id' => 'int' ), 'returns' => 'attribute' ),
        'attributeByIdentifier' => array( 'summary' => 'One class attribute by class and identifier', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'class' => 'string', 'identifier' => 'string' ), 'returns' => 'attribute' ),
        'searchableAttributes' => array( 'summary' => 'The searchable attributes of a class', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'class' => 'string' ), 'returns' => 'attributes' ),
        'requiredAttributes' => array( 'summary' => 'The required attributes of a class', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'class' => 'string' ), 'returns' => 'attributes' ),
        'collectorAttributes' => array( 'summary' => 'The information collector attributes of a class', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'class' => 'string' ), 'returns' => 'attributes' ),
        'groups' => array( 'summary' => 'The groups a class is in', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'class' => 'string' ), 'returns' => 'groups' ),
        'objectCount' => array( 'summary' => 'Number of objects of a class', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'class' => 'string' ), 'returns' => '{count}' ),
        'usage' => array( 'summary' => 'Classes with their object counts, most used first', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page' ),
        'containers' => array( 'summary' => 'Classes that are containers', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array(), 'returns' => 'classes' ),
        'names' => array( 'summary' => 'The name of a class in every language', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'class' => 'string' ), 'returns' => 'language => name' ),
        'languages' => array( 'summary' => 'Languages a class has names in', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'class' => 'string' ), 'returns' => 'language codes' ),
        'patterns' => array( 'summary' => 'Object name and URL alias patterns of a class', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'class' => 'string' ), 'returns' => '{object_name, url_alias}' ),
        'removable' => array( 'summary' => 'Whether the class can be removed and what blocks it', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'class' => 'string' ), 'returns' => '{removable, blockers}' ),
        'canInstantiate' => array( 'summary' => 'Classes the current user may create objects of', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'classes' ),
        'drafts' => array( 'summary' => 'Class drafts (temporary versions) being edited', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array(), 'returns' => 'classes' ),
        'draft' => array( 'summary' => 'A class draft with its attributes', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'class_id' => 'int' ), 'returns' => 'class' ),
        'dataTypes' => array( 'summary' => 'The datatypes available for class attributes', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array(), 'returns' => 'datatypes' ),
        'sortFields' => array( 'summary' => 'The names accepted for the default sort of children', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'names' ),
        'createDraft' => array( 'summary' => 'Starts a new class draft. POST: name, identifier, group_id, language, description, is_container, always_available, object_name_pattern, url_alias_pattern, sort_field, sort_order', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array(), 'returns' => 'class draft' ),
        'editDraft' => array( 'summary' => 'Starts or continues a draft of an existing class', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array( 'class_id' => 'int' ), 'returns' => 'class draft' ),
        'updateDraft' => array( 'summary' => 'Changes the class draft fields (same POST fields as createDraft)', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array( 'class_id' => 'int' ), 'returns' => 'class draft' ),
        'addAttributeDraft' => array( 'summary' => 'Adds an attribute to the draft. POST: data_type, identifier, name, description, is_required, is_searchable, can_translate, is_information_collector, language', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array( 'class_id' => 'int' ), 'returns' => 'attribute' ),
        'updateAttributeDraft' => array( 'summary' => 'Changes an attribute of the draft (same POST fields except data_type)', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array( 'class_id' => 'int', 'attribute_id' => 'int' ), 'returns' => 'attribute' ),
        'removeAttributeDraft' => array( 'summary' => 'Removes an attribute from the draft', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array( 'class_id' => 'int', 'attribute_id' => 'int' ), 'returns' => '{removed}' ),
        'moveAttributeDraft' => array( 'summary' => 'Moves an attribute of the draft: up, down, top or bottom', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array( 'class_id' => 'int', 'attribute_id' => 'int', 'direction' => 'string' ), 'returns' => 'attributes' ),
        'publishDraft' => array( 'summary' => 'Publishes the draft as the class definition', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array( 'class_id' => 'int' ), 'returns' => 'class' ),
        'discardDraft' => array( 'summary' => 'Discards the class draft', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array( 'class_id' => 'int' ), 'returns' => '{discarded}' ),
        'copy' => array( 'summary' => 'Starts a draft that is a copy of a class (publishDraft makes it a class)', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array( 'class_id' => 'int' ), 'returns' => 'class draft' ),
        'remove' => array( 'summary' => 'Removes a class that has no objects', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array( 'class_id' => 'int' ), 'returns' => '{removed}' ),
        'setName' => array( 'summary' => 'Sets the name of a class in a language (through a draft that is published)', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array( 'class_id' => 'int', 'language' => 'string', 'name' => 'string' ), 'returns' => 'class' ),
    );

    protected static function classIdArg( $args, $i )
    {
        return self::contentClass( self::arg( $args, $i, 'string' ) );
    }

    protected static function classAttributes( eZContentClass $c )
    {
        $out = array();
        foreach ( (array)$c->fetchAttributes() as $a )
            $out[] = self::exportClassAttribute( $a );
        return $out;
    }

    // ------------------------------------------------------------------ reads

    public static function list( $args )
    {
        static::guard( __FUNCTION__ );
        $group = self::arg( $args, 0, 'int', 0 );
        if ( $group )
            $classes = eZContentClassClassGroup::fetchClassList( eZContentClass::VERSION_STATUS_DEFINED, $group );
        else
            $classes = eZContentClass::fetchAllClasses( true, false );
        $items = array();
        foreach ( (array)$classes as $c )
            $items[] = self::exportClass( $c );
        return self::pageOf( $items, $args, 1, 2 );
    }

    public static function count( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'count' => count( (array)eZContentClass::fetchAllClasses( false, false ) ) ) );
    }

    public static function identifiers( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array();
        foreach ( (array)eZContentClass::fetchAllClasses( true, false ) as $c )
            $out[] = array( 'id' => (int)$c->attribute( 'id' ), 'identifier' => $c->attribute( 'identifier' ), 'name' => $c->attribute( 'name' ) );
        return self::ok( $out );
    }

    public static function get( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::exportClass( self::classIdArg( $args, 0 ), true ) );
    }

    public static function getByRemoteId( $args )
    {
        static::guard( __FUNCTION__ );
        $c = eZContentClass::fetchByRemoteID( self::arg( $args, 0, 'string' ) );
        if ( !$c )
            throw new expServiceException( 'No class with that remote id', 404 );
        return self::ok( self::exportClass( $c, true ) );
    }

    public static function search( $args )
    {
        static::guard( __FUNCTION__ );
        $text = mb_strtolower( self::arg( $args, 0, 'string' ) );
        $items = array();
        foreach ( (array)eZContentClass::fetchAllClasses( true, false ) as $c )
            if ( mb_strpos( mb_strtolower( $c->attribute( 'identifier' ) . ' ' . $c->attribute( 'name' ) ), $text ) !== false )
                $items[] = self::exportClass( $c );
        return self::pageOf( $items, $args, 1, 2 );
    }

    public static function attributes( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::classAttributes( self::classIdArg( $args, 0 ) ) );
    }

    public static function attribute( $args )
    {
        static::guard( __FUNCTION__ );
        $a = eZContentClassAttribute::fetch( self::arg( $args, 0, 'int' ) );
        if ( !$a )
            throw new expServiceException( 'No such class attribute', 404 );
        return self::ok( self::exportClassAttribute( $a ) );
    }

    public static function attributeByIdentifier( $args )
    {
        static::guard( __FUNCTION__ );
        $c = self::classIdArg( $args, 0 );
        $a = $c->fetchAttributeByIdentifier( self::arg( $args, 1, 'string' ) );
        if ( !$a )
            throw new expServiceException( 'The class has no such attribute', 404 );
        return self::ok( self::exportClassAttribute( $a ) );
    }

    protected static function attributeFilter( $args, $field )
    {
        $out = array();
        foreach ( (array)self::classIdArg( $args, 0 )->fetchAttributes() as $a )
            if ( $a->attribute( $field ) )
                $out[] = self::exportClassAttribute( $a );
        return self::ok( $out );
    }

    public static function searchableAttributes( $args )
    {
        static::guard( __FUNCTION__ );
        return self::attributeFilter( $args, 'is_searchable' );
    }

    public static function requiredAttributes( $args )
    {
        static::guard( __FUNCTION__ );
        return self::attributeFilter( $args, 'is_required' );
    }

    public static function collectorAttributes( $args )
    {
        static::guard( __FUNCTION__ );
        return self::attributeFilter( $args, 'is_information_collector' );
    }

    public static function groups( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array();
        foreach ( (array)eZContentClassClassGroup::fetchGroupList( self::classIdArg( $args, 0 )->attribute( 'id' ), eZContentClass::VERSION_STATUS_DEFINED ) as $g )
            $out[] = array( 'id' => (int)$g->attribute( 'group_id' ), 'name' => $g->attribute( 'group_name' ) );
        return self::ok( $out );
    }

    public static function objectCount( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'count' => (int)self::classIdArg( $args, 0 )->objectCount() ) );
    }

    public static function usage( $args )
    {
        static::guard( __FUNCTION__ );
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT contentclass_id AS class_id, COUNT(*) AS cnt FROM ezcontentobject WHERE status = 1 GROUP BY contentclass_id ORDER BY cnt DESC' );
        $items = array();
        foreach ( $rows as $r )
        {
            $c = eZContentClass::fetch( (int)$r['class_id'] );
            if ( $c )
                $items[] = array( 'class_id' => (int)$c->attribute( 'id' ), 'identifier' => $c->attribute( 'identifier' ), 'name' => $c->attribute( 'name' ), 'objects' => (int)$r['cnt'] );
        }
        return self::pageOf( $items, $args, 0, 1 );
    }

    public static function containers( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array();
        foreach ( (array)eZContentClass::fetchAllClasses( true, false ) as $c )
            if ( $c->attribute( 'is_container' ) )
                $out[] = self::exportClass( $c );
        return self::ok( $out );
    }

    public static function names( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( (object)self::classIdArg( $args, 0 )->nameList() );
    }

    public static function languages( $args )
    {
        static::guard( __FUNCTION__ );
        $codes = array();
        foreach ( (array)self::classIdArg( $args, 0 )->languages() as $l )
            $codes[] = is_object( $l ) ? $l->attribute( 'locale' ) : (string)$l;
        return self::ok( $codes );
    }

    public static function patterns( $args )
    {
        static::guard( __FUNCTION__ );
        $c = self::classIdArg( $args, 0 );
        return self::ok( array( 'object_name' => $c->attribute( 'contentobject_name' ), 'url_alias' => $c->attribute( 'url_alias_name' ) ) );
    }

    public static function removable( $args )
    {
        static::guard( __FUNCTION__ );
        $c = self::classIdArg( $args, 0 );
        $info = $c->removableInformation( false );
        return self::ok( array( 'removable' => count( $info['list'] ) === 0, 'blockers' => array_map( function ( $l ) { return isset( $l['text'] ) ? (string)$l['text'] : json_encode( $l ); }, (array)$info['list'] ),
                                'objects' => (int)$c->objectCount() ) );
    }

    public static function canInstantiate( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array();
        foreach ( (array)eZContentClass::canInstantiateClassList( true ) as $c )
            $out[] = array( 'id' => (int)$c->attribute( 'id' ), 'identifier' => $c->attribute( 'identifier' ), 'name' => $c->attribute( 'name' ) );
        return self::ok( $out );
    }

    public static function drafts( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array();
        foreach ( (array)eZContentClass::fetchList( eZContentClass::VERSION_STATUS_TEMPORARY, true ) as $c )
            $out[] = self::exportClass( $c );
        return self::ok( $out );
    }

    public static function draft( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::exportClass( self::draftOf( self::arg( $args, 0, 'int' ) ), true ) );
    }

    public static function dataTypes( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array();
        foreach ( eZDataType::allowedTypes() as $type )
        {
            $dt = eZDataType::create( $type );
            if ( $dt )
                $out[] = array( 'identifier' => $type, 'name' => $dt->attribute( 'information' )['name'], 'is_indexable' => (bool)$dt->isIndexable(),
                                'is_information_collector' => (bool)$dt->isInformationCollector(), 'can_be_set_remotely' => !in_array( $type, self::$deniedInputTypes, true ) );
        }
        return self::ok( $out );
    }

    public static function sortFields( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::$sortFields );
    }

    // ------------------------------------------------------------------ draft handling

    /** @return eZContentClass the temporary version of a class @throws expServiceException */
    protected static function draftOf( $classId )
    {
        $c = eZContentClass::fetch( (int)$classId, true, eZContentClass::VERSION_STATUS_TEMPORARY );
        if ( !$c instanceof eZContentClass || !$c->attribute( 'id' ) )
            throw new expServiceException( "Class $classId has no draft", 404 );
        return $c;
    }

    protected static function identifier( $value )
    {
        $id = strtolower( trim( (string)$value ) );
        if ( !preg_match( '/^[a-z][a-z0-9_]{0,49}$/', $id ) )
            throw new expServiceException( 'The identifier starts with a letter and has only a-z, 0-9 and _ (up to 50)', 422 );
        return $id;
    }

    /** Applies the POST fields of createDraft/updateDraft to a class. */
    protected static function applyClassFields( eZContentClass $c, $language )
    {
        if ( ( $v = self::post( 'name', 'string', '' ) ) !== '' )
            $c->setName( $v, $language );
        if ( ( $v = self::post( 'description', 'string', null ) ) !== null )
            $c->setDescription( $v, $language );
        if ( ( $v = self::post( 'identifier', 'string', '' ) ) !== '' )
            $c->setAttribute( 'identifier', self::identifier( $v ) );
        if ( ( $v = self::post( 'object_name_pattern', 'string', '' ) ) !== '' )
            $c->setAttribute( 'contentobject_name', $v );
        if ( ( $v = self::post( 'url_alias_pattern', 'string', null ) ) !== null )
            $c->setAttribute( 'url_alias_name', $v );
        if ( ( $v = self::post( 'is_container', 'bool', null ) ) !== null )
            $c->setAttribute( 'is_container', $v ? 1 : 0 );
        if ( ( $v = self::post( 'always_available', 'bool', null ) ) !== null )
            $c->setAttribute( 'always_available', $v ? 1 : 0 );
        if ( ( $v = self::post( 'sort_field', 'string', '' ) ) !== '' )
        {
            if ( !in_array( $v, self::$sortFields, true ) )
                throw new expServiceException( 'Unknown sort_field', 400 );
            $c->setAttribute( 'sort_field', eZContentObjectTreeNode::sortFieldID( $v ) );
        }
        if ( ( $v = self::post( 'sort_order', 'string', '' ) ) !== '' )
        {
            if ( !in_array( $v, array( 'asc', 'desc' ), true ) )
                throw new expServiceException( 'sort_order is asc or desc', 400 );
            $c->setAttribute( 'sort_order', $v === 'asc' ? 1 : 0 );
        }
        $c->setAttribute( 'modified', time() );
        $c->setAttribute( 'modifier_id', (int)eZUser::currentUser()->attribute( 'contentobject_id' ) );
    }

    public static function createDraft( $args )
    {
        static::guard( __FUNCTION__ );
        $language = self::languageCode( self::post( 'language', 'string', '' ) );
        $group = eZContentClassGroup::fetch( self::post( 'group_id', 'int' ) );
        if ( !$group )
            throw new expServiceException( 'The group does not exist', 404 );
        $name = trim( self::post( 'name', 'string' ) );
        $identifier = self::identifier( self::post( 'identifier', 'string' ) );
        $existing = eZContentClass::fetchByIdentifier( $identifier );
        if ( $existing )
            throw new expServiceException( "A class with the identifier '$identifier' exists", 409 );
        $uid = (int)eZUser::currentUser()->attribute( 'contentobject_id' );
        $class = eZContentClass::create( $uid, array(), $language );
        $class->setName( $name, $language );
        self::applyClassFields( $class, $language );
        $class->store();
        $class->setAlwaysAvailableLanguageID( (int)eZContentLanguage::idByLocale( $language ) );
        $link = eZContentClassClassGroup::create( $class->attribute( 'id' ), $class->attribute( 'version' ), $group->attribute( 'id' ), $group->attribute( 'name' ) );
        $link->store();
        return self::ok( self::exportClass( self::draftOf( $class->attribute( 'id' ) ), true ) );
    }

    public static function editDraft( $args )
    {
        static::guard( __FUNCTION__ );
        $id = self::arg( $args, 0, 'int' );
        $existing = eZContentClass::fetch( $id, true, eZContentClass::VERSION_STATUS_TEMPORARY );
        if ( $existing instanceof eZContentClass && $existing->attribute( 'id' ) )
        {
            $timeout = (int)eZINI::instance( 'content.ini' )->variable( 'ClassSettings', 'DraftTimeout' );
            $me = (int)eZUser::currentUser()->attribute( 'contentobject_id' );
            if ( (int)$existing->attribute( 'modifier_id' ) !== $me && $existing->attribute( 'modified' ) + $timeout > time() )
                throw new expServiceException( 'Another user is editing this class', 409 );
            return self::ok( self::exportClass( $existing, true ) );
        }
        $defined = eZContentClass::fetch( $id, true, eZContentClass::VERSION_STATUS_DEFINED );
        if ( !$defined )
            throw new expServiceException( "Class $id does not exist", 404 );
        $attributes = $defined->fetchAttributes( $id, true, eZContentClass::VERSION_STATUS_DEFINED );
        $groups = eZContentClassClassGroup::fetchGroupList( $id, eZContentClass::VERSION_STATUS_DEFINED );
        $db = eZDB::instance();
        $db->begin();
        foreach ( $groups as $g )
            eZContentClassClassGroup::create( $id, eZContentClass::VERSION_STATUS_TEMPORARY, $g->attribute( 'group_id' ), $g->attribute( 'group_name' ) )->store();
        $defined->setAttribute( 'version', eZContentClass::VERSION_STATUS_TEMPORARY );
        $defined->NameList->setHasDirtyData();
        foreach ( $attributes as $a )
            $a->setAttribute( 'version', eZContentClass::VERSION_STATUS_TEMPORARY );
        $defined->setAttribute( 'modified', time() );
        $defined->setAttribute( 'modifier_id', (int)eZUser::currentUser()->attribute( 'contentobject_id' ) );
        $defined->store( $attributes );
        $db->commit();
        return self::ok( self::exportClass( self::draftOf( $id ), true ) );
    }

    public static function updateDraft( $args )
    {
        static::guard( __FUNCTION__ );
        $c = self::draftOf( self::arg( $args, 0, 'int' ) );
        self::applyClassFields( $c, self::languageCode( self::post( 'language', 'string', '' ) ) );
        $c->store( true );
        return self::ok( self::exportClass( self::draftOf( $c->attribute( 'id' ) ), true ) );
    }

    /** @return eZContentClassAttribute an attribute of the draft */
    protected static function draftAttribute( eZContentClass $draft, $attributeId )
    {
        $a = eZContentClassAttribute::fetch( (int)$attributeId, true, eZContentClass::VERSION_STATUS_TEMPORARY );
        if ( !$a || (int)$a->attribute( 'contentclass_id' ) !== (int)$draft->attribute( 'id' ) )
            throw new expServiceException( "The draft has no attribute $attributeId", 404 );
        return $a;
    }

    protected static function applyAttributeFields( eZContentClassAttribute $a, eZContentClass $draft, $language )
    {
        if ( ( $v = self::post( 'name', 'string', '' ) ) !== '' )
            $a->setName( $v, $language );
        if ( ( $v = self::post( 'description', 'string', null ) ) !== null )
            $a->setDescription( $v, $language );
        if ( ( $v = self::post( 'identifier', 'string', '' ) ) !== '' )
        {
            $id = self::identifier( $v );
            foreach ( (array)$draft->fetchAttributes( $draft->attribute( 'id' ), true, eZContentClass::VERSION_STATUS_TEMPORARY ) as $other )
                if ( $other->attribute( 'identifier' ) === $id && (int)$other->attribute( 'id' ) !== (int)$a->attribute( 'id' ) )
                    throw new expServiceException( "The identifier '$id' is used by another attribute", 409 );
            $a->setAttribute( 'identifier', $id );
        }
        foreach ( array( 'is_required', 'is_searchable', 'can_translate', 'is_information_collector' ) as $flag )
            if ( ( $v = self::post( $flag, 'bool', null ) ) !== null )
                $a->setAttribute( $flag, $v ? 1 : 0 );
    }

    public static function addAttributeDraft( $args )
    {
        static::guard( __FUNCTION__ );
        $draft = self::draftOf( self::arg( $args, 0, 'int' ) );
        $type = self::post( 'data_type', 'string' );
        if ( !in_array( $type, eZDataType::allowedTypes(), true ) || !eZDataType::create( $type ) )
            throw new expServiceException( "Datatype '$type' is not available", 422 );
        $language = self::languageCode( self::post( 'language', 'string', '' ) );
        $a = eZContentClassAttribute::create( $draft->attribute( 'id' ), $type, array(), $language );
        $a->setName( self::post( 'name', 'string', 'new attribute' ), $language );
        self::applyAttributeFields( $a, $draft, $language );
        if ( $a->attribute( 'identifier' ) === '' )
            throw new expServiceException( 'The identifier is required', 400 );
        $a->dataType()->initializeClassAttribute( $a );
        $a->store();
        return self::ok( self::exportClassAttribute( $a ) );
    }

    public static function updateAttributeDraft( $args )
    {
        static::guard( __FUNCTION__ );
        $draft = self::draftOf( self::arg( $args, 0, 'int' ) );
        $a = self::draftAttribute( $draft, self::arg( $args, 1, 'int' ) );
        self::applyAttributeFields( $a, $draft, self::languageCode( self::post( 'language', 'string', '' ) ) );
        $a->store();
        return self::ok( self::exportClassAttribute( self::draftAttribute( $draft, $a->attribute( 'id' ) ) ) );
    }

    public static function removeAttributeDraft( $args )
    {
        static::guard( __FUNCTION__ );
        $draft = self::draftOf( self::arg( $args, 0, 'int' ) );
        $a = self::draftAttribute( $draft, self::arg( $args, 1, 'int' ) );
        if ( !$a->removeThis() )
            throw new expServiceException( 'This attribute cannot be removed', 409 );
        return self::ok( array( 'removed' => (int)self::arg( $args, 1, 'int' ) ) );
    }

    public static function moveAttributeDraft( $args )
    {
        static::guard( __FUNCTION__ );
        $draft = self::draftOf( self::arg( $args, 0, 'int' ) );
        $a = self::draftAttribute( $draft, self::arg( $args, 1, 'int' ) );
        $dir = self::arg( $args, 2, 'string' );
        switch ( $dir )
        {
            case 'up': $a->move( false ); break;
            case 'down': $a->move( true ); break;
            case 'top': $a->moveToEdge( true ); break;
            case 'bottom': $a->moveToEdge( false ); break;
            default: throw new expServiceException( 'direction is up, down, top or bottom', 400 );
        }
        $out = array();
        foreach ( (array)$draft->fetchAttributes( $draft->attribute( 'id' ), true, eZContentClass::VERSION_STATUS_TEMPORARY ) as $x )
            $out[] = self::exportClassAttribute( $x );
        return self::ok( $out );
    }

    public static function publishDraft( $args )
    {
        static::guard( __FUNCTION__ );
        $class = self::draftOf( self::arg( $args, 0, 'int' ) );
        $id = (int)$class->attribute( 'id' );
        $attributes = $class->fetchAttributes( $id, true, eZContentClass::VERSION_STATUS_TEMPORARY );
        if ( trim( $class->attribute( 'name' ) ) === '' )
            throw new expServiceException( 'The class needs a name', 422 );
        $identifier = self::identifier( $class->attribute( 'identifier' ) );
        $rows = eZDB::instance()->arrayQuery( "SELECT COUNT(*) AS cnt FROM ezcontentclass WHERE identifier='" . eZDB::instance()->escapeString( $identifier ) . "' AND version=" . eZContentClass::VERSION_STATUS_DEFINED . " AND id <> $id" );
        if ( (int)$rows[0]['cnt'] > 0 )
            throw new expServiceException( "A class with the identifier '$identifier' exists", 409 );
        if ( !$attributes )
            throw new expServiceException( 'The class needs at least one attribute', 422 );
        $seen = array();
        foreach ( $attributes as $a )
        {
            $ai = $a->attribute( 'identifier' );
            if ( $ai === '' || isset( $seen[$ai] ) )
                throw new expServiceException( $ai === '' ? 'Every attribute needs an identifier' : "The attribute identifier '$ai' is used twice", 422 );
            $seen[$ai] = true;
        }
        $db = eZDB::instance();
        $db->begin();
        if ( eZContentObject::fetchSameClassListCount( $id ) > 0 )
        {
            $unordered = array( 'Language' => $class->attribute( 'top_priority_language_locale' ) );
            eZExtension::getHandlerClass( new ezpExtensionOptions( array( 'iniFile' => 'site.ini', 'iniSection' => 'ContentSettings', 'iniVariable' => 'ContentClassEditHandler' ) ) )
                ->store( $class, $attributes, $unordered );
        }
        else
            $class->storeVersioned( $attributes, eZContentClass::VERSION_STATUS_DEFINED );
        $db->commit();
        ezpEvent::getInstance()->notify( 'content/class/cache', array( $id ) );
        return self::ok( self::exportClass( eZContentClass::fetch( $id ), true ) );
    }

    public static function discardDraft( $args )
    {
        static::guard( __FUNCTION__ );
        $class = self::draftOf( self::arg( $args, 0, 'int' ) );
        $id = (int)$class->attribute( 'id' );
        $defined = eZContentClass::fetch( $id, true, eZContentClass::VERSION_STATUS_DEFINED );
        $db = eZDB::instance();
        $db->begin();
        $class->setVersion( eZContentClass::VERSION_STATUS_TEMPORARY );
        $class->remove( true, eZContentClass::VERSION_STATUS_TEMPORARY );
        eZContentClassClassGroup::removeClassMembers( $id, eZContentClass::VERSION_STATUS_TEMPORARY );
        $db->commit();
        return self::ok( array( 'discarded' => $id, 'class_still_defined' => (bool)$defined ) );
    }

    public static function copy( $args )
    {
        static::guard( __FUNCTION__ );
        $source = eZContentClass::fetch( self::arg( $args, 0, 'int' ), true, eZContentClass::VERSION_STATUS_DEFINED );
        if ( !$source )
            throw new expServiceException( 'The class does not exist', 404 );
        $groups = eZContentClassClassGroup::fetchGroupList( $source->attribute( 'id' ), eZContentClass::VERSION_STATUS_DEFINED );
        $attributes = $source->fetchAttributes();
        $copy = clone $source;
        $copy->initializeCopy( $source );
        $copy->setAttribute( 'version', eZContentClass::VERSION_STATUS_TEMPORARY );
        $copy->store();
        foreach ( $groups as $g )
            eZContentClassClassGroup::create( $copy->attribute( 'id' ), eZContentClass::VERSION_STATUS_TEMPORARY, $g->attribute( 'group_id' ), $g->attribute( 'group_name' ) )->store();
        foreach ( $attributes as $a )
        {
            $ac = clone $a;
            $dt = $ac->dataType();
            if ( !$dt )
                continue;
            $dt->cloneClassAttribute( $a, $ac );
            $ac->setAttribute( 'contentclass_id', $copy->attribute( 'id' ) );
            $ac->setAttribute( 'version', eZContentClass::VERSION_STATUS_TEMPORARY );
            $ac->store();
        }
        return self::ok( self::exportClass( self::draftOf( $copy->attribute( 'id' ) ), true ) );
    }

    public static function remove( $args )
    {
        static::guard( __FUNCTION__ );
        $c = eZContentClass::fetch( self::arg( $args, 0, 'int' ) );
        if ( !$c )
            throw new expServiceException( 'The class does not exist', 404 );
        if ( $c->objectCount() > 0 )
            throw new expServiceException( 'The class has ' . $c->objectCount() . ' objects; remove them first', 409 );
        if ( !$c->isRemovable() )
            throw new expServiceException( 'The class is in use and cannot be removed', 409 );
        $id = (int)$c->attribute( 'id' );
        if ( !eZContentClassOperations::remove( $id ) )
            throw new expServiceException( 'The class could not be removed', 422 );
        return self::ok( array( 'removed' => $id ) );
    }

    public static function setName( $args )
    {
        static::guard( __FUNCTION__ );
        $id = self::arg( $args, 0, 'int' );
        $language = self::languageCode( self::arg( $args, 1, 'string' ) );
        $name = trim( self::arg( $args, 2, 'string' ) );
        if ( $name === '' )
            throw new expServiceException( 'The name cannot be empty', 422 );
        $c = eZContentClass::fetch( $id );
        if ( !$c )
            throw new expServiceException( 'The class does not exist', 404 );
        $attributes = $c->fetchAttributes();
        $c->setName( $name, $language );
        $c->setAttribute( 'modified', time() );
        $c->setAttribute( 'modifier_id', (int)eZUser::currentUser()->attribute( 'contentobject_id' ) );
        $c->store();
        eZContentClass::expireCache();
        return self::ok( self::exportClass( eZContentClass::fetch( $id ), true ) );
    }
}
