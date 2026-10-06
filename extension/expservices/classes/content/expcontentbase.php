<?php
/**
 * The shared part of the content domains of expservices: fetch helpers with the access checks, the exporters that
 * turn content objects into plain arrays, attribute value export/import per datatype, sort and filter parsing for
 * the node lists, and the "now or background job" routing of large operations.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

abstract class expContentServiceBase extends expServiceBase
{
    /** Datatypes whose fromString() reads a local path or URL, or that hold credentials: never written remotely. */
    public static $deniedInputTypes = array( 'ezimage', 'ezbinaryfile', 'ezmedia', 'ezuser', 'ezpassword', 'ezflowblock' );

    /** Sort names the node lists accept. */
    public static $sortFields = array( 'path', 'published', 'modified', 'section', 'depth', 'class_identifier', 'class_name',
                                       'priority', 'name', 'modified_subnode', 'node_id', 'contentobject_id' );

    // ------------------------------------------------------------------ fetch with checks

    /** @return eZContentObject @throws expServiceException 404, 403 */
    protected static function object( $objectId, $function = 'read' )
    {
        if ( !is_numeric( $objectId ) || (int)$objectId < 1 )
            throw new expServiceException( 'The object id must be a positive integer', 400 );
        $object = eZContentObject::fetch( (int)$objectId );
        if ( !$object instanceof eZContentObject )
            throw new expServiceException( "Object $objectId does not exist", 404 );
        $check = 'can' . ucfirst( $function );
        if ( !method_exists( $object, $check ) )
            throw new expServiceException( "Unknown object right '$function'", 500 );
        if ( !$object->$check() )
            throw new expServiceException( "No $function access to object $objectId", 403 );
        return $object;
    }

    /** @return eZContentClass @throws expServiceException 404 */
    protected static function contentClass( $idOrIdentifier )
    {
        $class = is_numeric( $idOrIdentifier ) ? eZContentClass::fetch( (int)$idOrIdentifier )
                                               : eZContentClass::fetchByIdentifier( (string)$idOrIdentifier );
        if ( !$class instanceof eZContentClass )
            throw new expServiceException( "Class $idOrIdentifier does not exist", 404 );
        return $class;
    }

    /** @return eZContentObjectVersion @throws expServiceException */
    protected static function version( eZContentObject $object, $versionNr, $write = false )
    {
        $version = $object->version( (int)$versionNr );
        if ( !$version instanceof eZContentObjectVersion )
            throw new expServiceException( "Version $versionNr of object " . $object->attribute( 'id' ) . ' does not exist', 404 );
        if ( !$version->canVersionRead() )
            throw new expServiceException( 'No access to this version', 403 );
        if ( $write )
        {
            if ( (int)$version->attribute( 'status' ) !== eZContentObjectVersion::STATUS_DRAFT )
                throw new expServiceException( 'Only a draft can be changed', 409 );
            $user = eZUser::currentUser();
            if ( (int)$version->attribute( 'creator_id' ) !== (int)$user->attribute( 'contentobject_id' ) && !self::can( 'content', 'versionremove' ) )
                throw new expServiceException( 'This draft belongs to another user', 403 );
        }
        return $version;
    }

    /** The language code to work in: the argument, else the object's initial language. */
    protected static function languageCode( $code, ?eZContentObject $object = null )
    {
        if ( $code === null || $code === '' )
            return $object ? $object->attribute( 'current_language' ) : eZContentObject::defaultLanguage();
        if ( !eZContentLanguage::fetchByLocale( (string)$code ) )
            throw new expServiceException( "Language $code is not available on this site", 422 );
        return (string)$code;
    }

    // ------------------------------------------------------------------ exporters

    protected static function exportNode( eZContentObjectTreeNode $node, $detail = false )
    {
        $object = $node->attribute( 'object' );
        $row = array( 'node_id' => (int)$node->attribute( 'node_id' ),
                      'object_id' => (int)$node->attribute( 'contentobject_id' ),
                      'parent_node_id' => (int)$node->attribute( 'parent_node_id' ),
                      'main_node_id' => (int)$node->attribute( 'main_node_id' ),
                      'is_main' => (int)$node->attribute( 'main_node_id' ) === (int)$node->attribute( 'node_id' ),
                      'class' => $node->attribute( 'class_identifier' ),
                      'name' => $node->attribute( 'name' ),
                      'url_alias' => $node->attribute( 'url_alias' ),
                      'depth' => (int)$node->attribute( 'depth' ),
                      'path_ids' => array_map( 'intval', array_values( array_filter( explode( '/', $node->attribute( 'path_string' ) ), 'strlen' ) ) ),
                      'priority' => (int)$node->attribute( 'priority' ),
                      'sort_field' => eZContentObjectTreeNode::sortFieldName( $node->attribute( 'sort_field' ) ),
                      'sort_order' => $node->attribute( 'sort_order' ) ? 'asc' : 'desc',
                      'is_hidden' => (bool)$node->attribute( 'is_hidden' ),
                      'is_invisible' => (bool)$node->attribute( 'is_invisible' ),
                      'remote_id' => $node->attribute( 'remote_id' ) );
        if ( $object instanceof eZContentObject )
        {
            $row['section_id'] = (int)$object->attribute( 'section_id' );
            $row['published'] = self::iso( $object->attribute( 'published' ) );
            $row['modified'] = self::iso( $object->attribute( 'modified' ) );
            $row['owner_id'] = (int)$object->attribute( 'owner_id' );
        }
        if ( $detail )
        {
            $row['is_container'] = (bool)$node->classIsContainer();
            $row['children_count'] = (int)$node->attribute( 'children_count' );
            $row['object'] = $object instanceof eZContentObject ? self::exportObject( $object ) : null;
        }
        return $row;
    }

    protected static function exportNodes( array $nodes, $detail = false )
    {
        $out = array();
        foreach ( $nodes as $node )
            if ( $node instanceof eZContentObjectTreeNode )
                $out[] = self::exportNode( $node, $detail );
        return $out;
    }

    protected static function exportObject( eZContentObject $o, $detail = false )
    {
        $row = array( 'id' => (int)$o->attribute( 'id' ),
                      'name' => $o->attribute( 'name' ),
                      'class' => $o->attribute( 'class_identifier' ),
                      'class_id' => (int)$o->attribute( 'contentclass_id' ),
                      'remote_id' => $o->attribute( 'remote_id' ),
                      'section_id' => (int)$o->attribute( 'section_id' ),
                      'owner_id' => (int)$o->attribute( 'owner_id' ),
                      'current_version' => (int)$o->attribute( 'current_version' ),
                      'status' => (int)$o->attribute( 'status' ),
                      'published' => self::iso( $o->attribute( 'published' ) ),
                      'modified' => self::iso( $o->attribute( 'modified' ) ),
                      'main_node_id' => (int)$o->attribute( 'main_node_id' ),
                      'initial_language' => $o->attribute( 'initial_language_code' ),
                      'always_available' => (bool)$o->attribute( 'always_available' ) );
        if ( $detail )
        {
            $row['languages'] = array_values( (array)$o->availableLanguages() );
            $row['node_ids'] = array();
            foreach ( (array)$o->assignedNodes() as $n )
                $row['node_ids'][] = (int)$n->attribute( 'node_id' );
        }
        return $row;
    }

    protected static function exportVersion( eZContentObjectVersion $v )
    {
        $statusNames = array( eZContentObjectVersion::STATUS_DRAFT => 'draft', eZContentObjectVersion::STATUS_PUBLISHED => 'published',
                              eZContentObjectVersion::STATUS_PENDING => 'pending', eZContentObjectVersion::STATUS_ARCHIVED => 'archived',
                              eZContentObjectVersion::STATUS_REJECTED => 'rejected', eZContentObjectVersion::STATUS_INTERNAL_DRAFT => 'internal_draft',
                              eZContentObjectVersion::STATUS_REPEAT => 'repeat', eZContentObjectVersion::STATUS_QUEUED => 'queued' );
        $status = (int)$v->attribute( 'status' );
        return array( 'id' => (int)$v->attribute( 'id' ),
                      'object_id' => (int)$v->attribute( 'contentobject_id' ),
                      'version' => (int)$v->attribute( 'version' ),
                      'status' => $status,
                      'status_name' => isset( $statusNames[$status] ) ? $statusNames[$status] : 'unknown',
                      'creator_id' => (int)$v->attribute( 'creator_id' ),
                      'created' => self::iso( $v->attribute( 'created' ) ),
                      'modified' => self::iso( $v->attribute( 'modified' ) ),
                      'initial_language' => $v->attribute( 'initial_language_code' ),
                      'language_codes' => array_map( function ( $l ) { return is_object( $l ) ? $l->attribute( 'language_code' ) : (string)$l; },
                                                     (array)$v->translationList( false, false ) ) );
    }

    protected static function exportClass( eZContentClass $c, $withAttributes = false )
    {
        $row = array( 'id' => (int)$c->attribute( 'id' ),
                      'identifier' => $c->attribute( 'identifier' ),
                      'name' => $c->attribute( 'name' ),
                      'description' => $c->attribute( 'description' ),
                      'remote_id' => $c->attribute( 'remote_id' ),
                      'version' => (int)$c->attribute( 'version' ),
                      'is_container' => (bool)$c->attribute( 'is_container' ),
                      'always_available' => (bool)$c->attribute( 'always_available' ),
                      'object_name_pattern' => $c->attribute( 'contentobject_name' ),
                      'url_alias_pattern' => $c->attribute( 'url_alias_name' ),
                      'sort_field' => eZContentObjectTreeNode::sortFieldName( $c->attribute( 'sort_field' ) ),
                      'sort_order' => $c->attribute( 'sort_order' ) ? 'asc' : 'desc',
                      'created' => self::iso( $c->attribute( 'created' ) ),
                      'modified' => self::iso( $c->attribute( 'modified' ) ),
                      'creator_id' => (int)$c->attribute( 'creator_id' ),
                      'modifier_id' => (int)$c->attribute( 'modifier_id' ),
                      'initial_language' => $c->attribute( 'top_priority_language_locale' ) );
        if ( $withAttributes )
        {
            $row['attributes'] = array();
            foreach ( $c->fetchAttributes( false, true, (int)$c->attribute( 'version' ) ) as $a )
                $row['attributes'][] = self::exportClassAttribute( $a );
            $row['groups'] = array();
            foreach ( eZContentClassClassGroup::fetchGroupList( $c->attribute( 'id' ), (int)$c->attribute( 'version' ) ) as $g )
                $row['groups'][] = array( 'id' => (int)$g->attribute( 'group_id' ), 'name' => $g->attribute( 'group_name' ) );
        }
        return $row;
    }

    protected static function exportClassAttribute( eZContentClassAttribute $a )
    {
        return array( 'id' => (int)$a->attribute( 'id' ),
                      'class_id' => (int)$a->attribute( 'contentclass_id' ),
                      'identifier' => $a->attribute( 'identifier' ),
                      'name' => $a->attribute( 'name' ),
                      'description' => $a->attribute( 'description' ),
                      'data_type' => $a->attribute( 'data_type_string' ),
                      'is_required' => (bool)$a->attribute( 'is_required' ),
                      'is_searchable' => (bool)$a->attribute( 'is_searchable' ),
                      'is_information_collector' => (bool)$a->attribute( 'is_information_collector' ),
                      'can_translate' => (bool)$a->attribute( 'can_translate' ),
                      'placement' => (int)$a->attribute( 'placement' ),
                      'version' => (int)$a->attribute( 'version' ) );
    }

    protected static function exportClassGroup( eZContentClassGroup $g )
    {
        return array( 'id' => (int)$g->attribute( 'id' ), 'name' => $g->attribute( 'name' ),
                      'created' => self::iso( $g->attribute( 'created' ) ), 'modified' => self::iso( $g->attribute( 'modified' ) ),
                      'creator_id' => (int)$g->attribute( 'creator_id' ), 'modifier_id' => (int)$g->attribute( 'modifier_id' ) );
    }

    protected static function exportSection( eZSection $s )
    {
        return array( 'id' => (int)$s->attribute( 'id' ), 'name' => $s->attribute( 'name' ), 'identifier' => $s->attribute( 'identifier' ),
                      'navigation_part_identifier' => $s->attribute( 'navigation_part_identifier' ) );
    }

    protected static function exportStateGroup( eZContentObjectStateGroup $g, $withStates = false )
    {
        $row = array( 'id' => (int)$g->attribute( 'id' ), 'identifier' => $g->attribute( 'identifier' ),
                      'name' => $g->attribute( 'current_translation' ) ? $g->attribute( 'current_translation' )->attribute( 'name' ) : $g->attribute( 'identifier' ),
                      'is_internal' => (bool)$g->isInternal(), 'default_language' => ( $l = $g->defaultLanguage() ) ? $l->attribute( 'locale' ) : null );
        if ( $withStates )
        {
            $row['states'] = array();
            foreach ( $g->states() as $s )
                $row['states'][] = self::exportState( $s );
        }
        return $row;
    }

    protected static function exportState( eZContentObjectState $s )
    {
        $t = $s->attribute( 'current_translation' );
        return array( 'id' => (int)$s->attribute( 'id' ), 'group_id' => (int)$s->attribute( 'group_id' ),
                      'identifier' => $s->attribute( 'identifier' ), 'priority' => (int)$s->attribute( 'priority' ),
                      'name' => $t ? $t->attribute( 'name' ) : $s->attribute( 'identifier' ),
                      'description' => $t ? $t->attribute( 'description' ) : '',
                      'default_language' => ( $l = $s->defaultLanguage() ) ? $l->attribute( 'locale' ) : null );
    }

    // ------------------------------------------------------------------ attribute values

    /** One object attribute with its value exported per datatype. */
    protected static function exportAttribute( eZContentObjectAttribute $a, $withValue = true )
    {
        $ca = $a->contentClassAttribute();
        $row = array( 'id' => (int)$a->attribute( 'id' ),
                      'identifier' => $ca->attribute( 'identifier' ),
                      'name' => $ca->attribute( 'name' ),
                      'data_type' => $a->attribute( 'data_type_string' ),
                      'language' => $a->attribute( 'language_code' ),
                      'version' => (int)$a->attribute( 'version' ),
                      'has_content' => (bool)$a->hasContent(),
                      'is_required' => (bool)$ca->attribute( 'is_required' ) );
        if ( $withValue )
            $row['value'] = self::attributeValue( $a );
        return $row;
    }

    /** The value of an attribute as plain data: scalars as they are, structured types as small objects. */
    protected static function attributeValue( eZContentObjectAttribute $a )
    {
        $type = $a->attribute( 'data_type_string' );
        try
        {
            switch ( $type )
            {
                case 'ezuser':
                    $u = $a->content();
                    return $u instanceof eZUser ? array( 'login' => $u->attribute( 'login' ), 'is_enabled' => (bool)$u->isEnabled() ) : null;
                case 'ezpassword':
                    return null;
                case 'ezboolean':
                    return (bool)$a->attribute( 'data_int' );
                case 'ezinteger':
                    return (int)$a->attribute( 'data_int' );
                case 'ezfloat':
                    return (float)$a->attribute( 'data_float' );
                case 'ezdatetime':
                case 'ezdate':
                case 'eztime':
                    $ts = (int)$a->attribute( 'data_int' );
                    return array( 'timestamp' => $ts, 'iso' => $ts ? self::iso( $ts ) : null );
                case 'ezxmltext':
                    $output = $a->content()->attribute( 'output' );
                    return array( 'xml' => $a->attribute( 'data_text' ), 'html' => $output ? $output->attribute( 'output_text' ) : '' );
                case 'ezimage':
                    $c = $a->content();
                    if ( !$c || !$c->attribute( 'is_valid' ) )
                        return null;
                    $original = $c->attribute( 'original' );
                    return array( 'alternative_text' => $c->attribute( 'alternative_text' ), 'url' => $original['url'],
                                  'width' => (int)$original['width'], 'height' => (int)$original['height'],
                                  'filesize' => (int)$original['filesize'], 'original_filename' => $original['original_filename'] );
                case 'ezbinaryfile':
                case 'ezmedia':
                    $c = $a->content();
                    if ( !$c )
                        return null;
                    return array( 'filename' => $c->attribute( 'filename' ), 'original_filename' => $c->attribute( 'original_filename' ),
                                  'filesize' => (int)$c->attribute( 'filesize' ), 'mime_type' => $c->attribute( 'mime_type' ),
                                  'download_url' => 'content/download/' . $a->attribute( 'contentobject_id' ) . '/' . $a->attribute( 'id' ) . '/file/' . rawurlencode( $c->attribute( 'original_filename' ) ) );
                case 'ezobjectrelation':
                    return $a->attribute( 'data_int' ) ? (int)$a->attribute( 'data_int' ) : null;
                case 'ezobjectrelationlist':
                    $list = array();
                    $c = $a->content();
                    if ( is_array( $c ) && isset( $c['relation_list'] ) )
                        foreach ( $c['relation_list'] as $r )
                            $list[] = (int)$r['contentobject_id'];
                    return $list;
                case 'ezkeyword':
                    $c = $a->content();
                    return $c ? array_values( (array)$c->attribute( 'keyword_string' ) !== array() ? array_filter( array_map( 'trim', explode( ',', $c->attribute( 'keyword_string' ) ) ), 'strlen' ) : array() ) : array();
                default:
                    return $a->toString();
            }
        }
        catch ( Throwable $e )
        {
            return null;
        }
    }

    /** The attributes of an object version as identifier => value (data map export). */
    protected static function exportDataMap( eZContentObject $object, $versionNr = false, $language = false, $withValues = true )
    {
        $out = array();
        $attrs = $object->fetchDataMap( $versionNr ?: false, $language ?: false );
        foreach ( (array)$attrs as $identifier => $attribute )
            $out[$identifier] = self::exportAttribute( $attribute, $withValues );
        return $out;
    }

    /**
     * Checks an identifier => value input for a class: known identifiers, writable datatypes (no file paths, no
     * credentials: those have their own services).
     *
     * @throws expServiceException 422
     */
    protected static function checkInput( eZContentClass $class, array $input )
    {
        $known = array();
        foreach ( $class->fetchAttributes() as $a )
            $known[$a->attribute( 'identifier' )] = $a->attribute( 'data_type_string' );
        foreach ( $input as $identifier => $value )
        {
            if ( !isset( $known[$identifier] ) )
                throw new expServiceException( "Class " . $class->attribute( 'identifier' ) . " has no attribute '$identifier'", 422 );
            if ( in_array( $known[$identifier], self::$deniedInputTypes, true ) )
                throw new expServiceException( "Attribute '$identifier' ({$known[$identifier]}) cannot be set remotely with a string value", 422 );
            if ( is_array( $value ) || is_object( $value ) )
                throw new expServiceException( "The value of '$identifier' must be a string", 422 );
        }
    }

    /** Stores string values into the attributes of a version through fromString(). */
    protected static function storeInput( array $attributes, array $input )
    {
        foreach ( $attributes as $attribute )
        {
            $identifier = $attribute->attribute( 'contentclass_attribute_identifier' );
            if ( array_key_exists( $identifier, $input ) )
            {
                if ( !$attribute->fromString( (string)$input[$identifier] ) && (string)$input[$identifier] !== '' && $attribute->attribute( 'data_type_string' ) !== 'ezstring' )
                {
                    // fromString() reports failure for values the datatype does not take; ezstring-like types take everything
                }
                $attribute->store();
            }
        }
    }

    /** Runs the publish operation on a version. @return bool */
    protected static function publishVersion( eZContentObject $object, $versionNr )
    {
        $result = eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $object->attribute( 'id' ), 'version' => (int)$versionNr ) );
        if ( !is_array( $result ) || !isset( $result['status'] ) )
            return false;
        return $result['status'] == eZModuleOperationInfo::STATUS_CONTINUE || $result['status'] == eZModuleOperationInfo::STATUS_OK;
    }

    // ------------------------------------------------------------------ list parameters

    /** array( 'SortBy' => ... ) from sort name and order. */
    protected static function sortBy( $sort, $order )
    {
        $sort = $sort === null || $sort === '' ? 'path' : strtolower( (string)$sort );
        if ( !in_array( $sort, self::$sortFields, true ) )
            throw new expServiceException( "Unknown sort '$sort', use one of " . implode( ', ', self::$sortFields ), 400 );
        $order = strtolower( (string)$order );
        if ( $order !== '' && !in_array( $order, array( 'asc', 'desc' ), true ) )
            throw new expServiceException( 'order is asc or desc', 400 );
        return array( array( $sort, $order !== 'desc' ) );
    }

    /**
     * Filter parameters for subTree()/subTreeCount() from a json map: class (list), exclude_class (list),
     * section (int), state (int list), owner (int), name (string), language (code), hidden (bool), from, to (published range).
     */
    protected static function filterParams( $filter )
    {
        $p = array();
        if ( !is_array( $filter ) )
            return $p;
        $attr = array();
        if ( !empty( $filter['class'] ) )
        {
            $p['ClassFilterType'] = 'include';
            $p['ClassFilterArray'] = array_values( (array)$filter['class'] );
        }
        else if ( !empty( $filter['exclude_class'] ) )
        {
            $p['ClassFilterType'] = 'exclude';
            $p['ClassFilterArray'] = array_values( (array)$filter['exclude_class'] );
        }
        if ( isset( $filter['section'] ) )
            $attr[] = array( 'section', '=', (int)$filter['section'] );
        if ( !empty( $filter['state'] ) )
            $attr[] = array( 'state', 'in', array_map( 'intval', (array)$filter['state'] ) );
        if ( isset( $filter['owner'] ) )
            $attr[] = array( 'owner', '=', (int)$filter['owner'] );
        if ( !empty( $filter['from'] ) )
            $attr[] = array( 'published', '>=', is_numeric( $filter['from'] ) ? (int)$filter['from'] : (int)strtotime( $filter['from'] ) );
        if ( !empty( $filter['to'] ) )
            $attr[] = array( 'published', '<=', is_numeric( $filter['to'] ) ? (int)$filter['to'] : (int)strtotime( $filter['to'] ) );
        if ( $attr )
            $p['AttributeFilter'] = array_merge( array( 'and' ), $attr );
        if ( isset( $filter['name'] ) && $filter['name'] !== '' )
            $p['ObjectNameFilter'] = (string)$filter['name'];
        if ( !empty( $filter['language'] ) )
            $p['Language'] = (string)$filter['language'];
        if ( !empty( $filter['hidden'] ) )
            $p['IgnoreVisibility'] = true;
        if ( !empty( $filter['depth'] ) )
        {
            $p['Depth'] = (int)$filter['depth'];
            $p['DepthOperator'] = 'le';
        }
        return $p;
    }

    /** A paged list of the nodes below $node: depth 1 for children, unlimited for the subtree. */
    protected static function nodeList( eZContentObjectTreeNode $node, array $args, $first, $depth, $detail = false )
    {
        // $first = index of the sort argument; order, limit, offset, filter follow
        $sort = self::arg( $args, $first, 'string', 'path' );
        $order = self::arg( $args, $first + 1, 'string', 'asc' );
        list( $limit, $offset ) = self::paging( $args, $first + 2, $first + 3 );
        $filter = self::arg( $args, $first + 4, 'json', array() );
        $params = array_merge( array( 'Limit' => $limit, 'Offset' => $offset, 'SortBy' => self::sortBy( $sort, $order ),
                                      'AsObject' => true ), self::filterParams( $filter ) );
        if ( $depth )
        {
            $params['Depth'] = $depth;
            $params['DepthOperator'] = 'eq';
        }

        $count = $node->subTreeCount( array_diff_key( $params, array( 'Limit' => 1, 'Offset' => 1, 'SortBy' => 1 ) ) );
        $nodes = $count ? $node->subTree( $params ) : array();
        return self::page( self::exportNodes( (array)$nodes, $detail ), $count, $offset, $limit );
    }

    // ------------------------------------------------------------------ jobs

    /** The "mode" of a write: POST field 'mode' job|now|auto. */
    protected static function jobMode()
    {
        $mode = self::post( 'mode', 'string', 'auto' );
        if ( !in_array( $mode, array( 'job', 'now', 'auto' ), true ) )
            throw new expServiceException( "mode is job, now or auto", 400 );
        return $mode;
    }

    /**
     * Routes a large operation: as a content job when the mode asks for it (or auto and it is large), else the callback.
     *
     * @param string $type content job type
     * @param array $params job parameters
     * @param callable $now the synchronous implementation, returns the data
     * @return array envelope
     */
    protected static function runOrJob( $type, array $params, $now )
    {
        $params['mode'] = self::jobMode();
        if ( !class_exists( 'expContentJob' ) )
            return self::ok( call_user_func( $now ), array( 'mode' => 'now' ) );
        try
        {
            if ( expContentJob::shouldRunAsJob( $type, $params ) )
            {
                $job = expContentJob::create( $type, $params, eZUser::currentUser() );
                $started = $job->spawn();
                return self::ok( array( 'job_id' => $job->id(), 'type' => $type, 'state' => $job->state(), 'spawned' => (bool)$started ),
                                 array( 'mode' => 'job' ) );
            }
            expContentJob::checkNow( $type, $params );
        }
        catch ( expContentJobException $e )
        {
            throw new expServiceException( $e->getMessage(), $e->getCode() == 409 ? 409 : ( $e->getCode() == 413 ? 422 : 403 ) );
        }
        return self::ok( call_user_func( $now ), array( 'mode' => 'now' ) );
    }

    /** The checkNode-style guard: a locked subtree (background job running on it) refuses a synchronous write. */
    protected static function notLocked( array $nodeIds )
    {
        if ( class_exists( 'expContentJobLock' ) && ( $holder = expContentJobLock::checkNodes( $nodeIds ) ) )
            throw new expServiceException( 'The subtree is being changed by background job ' . $holder->id() . ', try again when it is done', 409 );
    }

    /**
     * Runs a content operation through the operation handler (workflow triggers apply) when it is available,
     * else through the fallback, the way the admin views do.
     *
     * @return mixed the operation result, or what the fallback returns
     */
    protected static function operation( $name, array $params, $fallback )
    {
        if ( eZOperationHandler::operationIsAvailable( 'content_' . $name ) )
            return eZOperationHandler::execute( 'content', $name, $params, null, true );
        return call_user_func( $fallback );
    }

    /** The node id most recently created under $parentNodeId (highest id), used to report a copy. */
    protected static function newestChild( $parentNodeId )
    {
        $parent = eZContentObjectTreeNode::fetch( (int)$parentNodeId );
        if ( !$parent )
            return null;
        $list = $parent->subTree( array( 'Depth' => 1, 'DepthOperator' => 'eq', 'Limit' => 1, 'SortBy' => array( array( 'node_id', false ) ),
                                         'IgnoreVisibility' => true, 'AsObject' => true ) );
        return $list ? $list[0] : null;
    }

    /** Required attributes of a class must be in the input (datatypes that cannot be set remotely are skipped). */
    protected static function checkRequired( eZContentClass $class, array $input )
    {
        foreach ( $class->fetchAttributes() as $a )
        {
            $id = $a->attribute( 'identifier' );
            if ( $a->attribute( 'is_required' ) && !in_array( $a->attribute( 'data_type_string' ), self::$deniedInputTypes, true )
                 && ( !isset( $input[$id] ) || trim( (string)$input[$id] ) === '' ) )
                throw new expServiceException( "The attribute '$id' is required", 422 );
        }
    }

    /** Changes attributes and publishes the result as a new version. @return eZContentObject the fresh object */
    protected static function updateObject( eZContentObject $object, array $attrs, $language = false )
    {
        // Decided as content/edit and the REST interface decide it (eZContentObject::editAccess()): in the language
        // written, and through the filter content/edit/access
        if ( !$object->editAccess( null, $language ? (string)$language : false ) )
            throw new expServiceException( 'No edit access to object ' . $object->attribute( 'id' ) . ( $language ? " in $language" : '' ), 403 );
        if ( !$attrs )
            throw new expServiceException( 'No attributes given', 400 );
        self::checkInput( $object->contentClass(), $attrs );
        if ( $language )
            self::languageCode( $language, $object );
        $params = array( 'attributes' => $attrs );
        if ( $language )
            $params['language'] = $language;
        if ( !eZContentFunctions::updateAndPublishObject( $object, $params ) )
            throw new expServiceException( 'The new version could not be published', 422 );
        return eZContentObject::fetch( (int)$object->attribute( 'id' ) );
    }
}
