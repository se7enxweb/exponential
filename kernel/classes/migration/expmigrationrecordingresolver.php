<?php
/**
 * File containing the expMigrationRecordingResolver class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The recording's view of the installation (expMigrationRecordingSource), read through the kernel's own classes so
 * it works on every engine: remote ids, identifiers and the state after a change.
 *
 * A node or an object an event is about may be gone when the event is recorded (a removal is recorded after it): the
 * audit's descriptions (expAuditHook::node(), object(), user()) call remember() while the thing still exists, so its
 * remote id is known afterwards.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRecordingResolver implements expMigrationRecordingSource
{
    /** @var array "node:<id>" / "object:<id>" => remote id, kept by remember() */
    protected static $remembered = array();

    /** The limitations whose values are class ids. */
    const CLASS_LIMITATIONS = array( 'class', 'parentclass' );

    /**
     * Keeps the remote id of a node or an object while it exists (the audit's description of it is being built).
     *
     * @param string $kind node or object
     * @param int $id
     * @param string $remoteId
     */
    public static function remember( $kind, $id, $remoteId )
    {
        if ( (string)$remoteId === '' || (int)$id <= 0 )
            return;
        if ( count( self::$remembered ) > 2000 )
            self::$remembered = array();
        self::$remembered[$kind . ':' . (int)$id] = (string)$remoteId;
    }

    /** Forgets the remembered remote ids (a new request in a persistent worker; tests). */
    public static function forget()
    {
        self::$remembered = array();
    }

    public function nodeRef( $nodeId )
    {
        $nodeId = (int)$nodeId;
        if ( $nodeId <= 0 )
            return null;
        $node = eZContentObjectTreeNode::fetch( $nodeId, false, false );
        if ( is_array( $node ) && isset( $node['remote_id'] ) && (string)$node['remote_id'] !== '' )
            return 'remote:' . $node['remote_id'];
        return isset( self::$remembered["node:$nodeId"] ) ? 'remote:' . self::$remembered["node:$nodeId"] : null;
    }

    public function objectRef( $objectId )
    {
        $objectId = (int)$objectId;
        if ( $objectId <= 0 )
            return null;
        $object = eZContentObject::fetch( $objectId, false );
        if ( is_array( $object ) && isset( $object['remote_id'] ) && (string)$object['remote_id'] !== '' )
            return 'remote:' . $object['remote_id'];
        return isset( self::$remembered["object:$objectId"] ) ? 'remote:' . self::$remembered["object:$objectId"] : null;
    }

    public function node( $nodeId )
    {
        $node = eZContentObjectTreeNode::fetch( (int)$nodeId );
        if ( !$node instanceof eZContentObjectTreeNode )
            return null;
        $parentId = (int)$node->attribute( 'parent_node_id' );
        return array(
            'remote_id' => (string)$node->attribute( 'remote_id' ),
            'object_id' => (int)$node->attribute( 'contentobject_id' ),
            'object' => $this->objectRef( (int)$node->attribute( 'contentobject_id' ) ),
            'parent' => $parentId > 0 && $parentId !== (int)$node->attribute( 'node_id' ) ? $this->nodeRef( $parentId ) : null,
            'hidden' => (bool)$node->attribute( 'is_hidden' ),
            'priority' => (int)$node->attribute( 'priority' ),
            'sort_field' => (string)eZContentObjectTreeNode::sortFieldName( (int)$node->attribute( 'sort_field' ) ),
            'sort_order' => (int)$node->attribute( 'sort_order' ) === 1 ? 'asc' : 'desc',
            'main' => (int)$node->attribute( 'main_node_id' ) === (int)$node->attribute( 'node_id' ),
        );
    }

    public function object( $objectId )
    {
        $object = eZContentObject::fetch( (int)$objectId );
        if ( !$object instanceof eZContentObject )
            return null;
        $isUser = $this->isUser( (int)$objectId );
        $mainNodeId = (int)$object->attribute( 'main_node_id' );
        $main = $mainNodeId ? eZContentObjectTreeNode::fetch( $mainNodeId, false, false ) : null;
        $locations = array();
        $groups = array();
        foreach ( (array)$object->assignedNodes( false ) as $row )
        {
            $location = $this->node( (int)$row['node_id'] );
            if ( $location === null )
                continue;
            $locations[] = $location;
            if ( $isUser )
            {
                $parent = eZContentObjectTreeNode::fetch( (int)$row['parent_node_id'] );
                $groupRef = $parent instanceof eZContentObjectTreeNode ? $this->objectRef( (int)$parent->attribute( 'contentobject_id' ) ) : null;
                if ( $groupRef !== null )
                    $groups[] = $groupRef;
            }
        }
        $languages = array();
        $version = $object->currentVersion();
        $locales = $version ? (array)$version->translationList( false, false ) : array();
        sort( $locales );
        foreach ( $locales as $locale )
        {
            $fields = array();
            foreach ( (array)$object->fetchDataMap( false, $locale ) as $identifier => $attribute )
                $fields[(string)$identifier] = $this->field( $attribute );
            ksort( $fields );
            $languages[(string)$locale] = $fields;
        }
        $states = array();
        foreach ( (array)$object->stateIdentifierArray() as $pair )
        {
            $parts = explode( '/', (string)$pair, 2 );
            if ( count( $parts ) === 2 )
                $states[$parts[0]] = $parts[1];
        }
        ksort( $states );
        $snapshot = array(
            'remote_id' => (string)$object->attribute( 'remote_id' ),
            'class' => (string)$object->attribute( 'class_identifier' ),
            'section' => $this->sectionIdentifier( (int)$object->attribute( 'section_id' ) ),
            'always_available' => (bool)$object->isAlwaysAvailable(),
            'initial_language' => (string)$object->attribute( 'initial_language_code' ),
            'is_user' => $isUser,
            'parent' => is_array( $main ) && isset( $main['parent_node_id'] ) ? $this->nodeRef( (int)$main['parent_node_id'] ) : null,
            'locations' => $locations,
            'states' => $states,
            'languages' => $languages,
        );
        if ( $isUser )
        {
            $snapshot['user_groups'] = array_values( array_unique( $groups ) );
            $setting = eZUserSetting::fetch( (int)$objectId );
            if ( $setting instanceof eZUserSetting )
                $snapshot['enabled'] = (bool)$setting->attribute( 'is_enabled' );
        }
        return $snapshot;
    }

    /**
     * The value of an attribute for a content step: its text form (fromString() reads it back), relations as remote
     * ids, files with a note.
     *
     * @param eZContentObjectAttribute $attribute
     * @return array datatype, value, note (optional)
     */
    protected function field( $attribute )
    {
        $datatype = (string)$attribute->attribute( 'data_type_string' );
        $field = array( 'datatype' => $datatype, 'value' => null );
        if ( in_array( $datatype, expMigrationRecordingEvent::ACCOUNT_DATATYPES, true ) )
            return $field;
        if ( !$attribute->hasContent() )
        {
            $field['value'] = '';
            return $field;
        }
        $text = $attribute->toString();
        $text = is_scalar( $text ) ? (string)$text : json_encode( $text );
        switch ( $datatype )
        {
            case 'ezobjectrelation':
            case 'ezobjectrelationlist':
                $refs = array();
                foreach ( preg_split( '/[-,]/', $text, -1, PREG_SPLIT_NO_EMPTY ) as $id )
                {
                    $ref = $this->objectRef( (int)$id );
                    if ( $ref !== null )
                        $refs[] = $ref;
                }
                $field['value'] = $datatype === 'ezobjectrelation' ? ( $refs ? $refs[0] : '' ) : $refs;
                break;
            case 'ezimage':
            case 'ezbinaryfile':
            case 'ezmedia':
                $field['value'] = $text;
                $field['note'] = 'a file: the value is the path in var/ and the name; a file step copies the file';
                break;
            default:
                $field['value'] = $text;
        }
        return $field;
    }

    public function contentClass( $class )
    {
        $found = is_numeric( $class ) ? eZContentClass::fetch( (int)$class ) : eZContentClass::fetchByIdentifier( (string)$class );
        if ( !$found instanceof eZContentClass )
            return null;
        $groups = array();
        foreach ( (array)$found->fetchGroupList() as $link )
            $groups[] = (string)$link->attribute( 'group_name' );
        sort( $groups );
        $attributes = array();
        foreach ( (array)$found->fetchAttributes() as $a )
        {
            $definition = array(
                'datatype' => (string)$a->attribute( 'data_type_string' ),
                'names' => self::nameList( $a->nameList() ),
                'placement' => (int)$a->attribute( 'placement' ),
                'required' => (bool)$a->attribute( 'is_required' ),
                'searchable' => (bool)$a->attribute( 'is_searchable' ),
                'translatable' => (bool)$a->attribute( 'can_translate' ),
                'information_collector' => (bool)$a->attribute( 'is_information_collector' ),
            );
            $descriptions = self::nameList( $a->descriptionList() );
            if ( $descriptions )
                $definition['descriptions'] = $descriptions;
            $category = (string)$a->attribute( 'category' );
            if ( $category !== '' )
                $definition['category'] = $category;
            $settings = array();
            foreach ( array( 'data_int1', 'data_int2', 'data_int3', 'data_int4', 'data_float1', 'data_float2', 'data_float3', 'data_float4',
                             'data_text1', 'data_text2', 'data_text3', 'data_text4', 'data_text5' ) as $column )
            {
                $value = $a->attribute( $column );
                if ( $value !== null && $value !== '' && $value !== 0 && $value !== '0' && $value !== 0.0 )
                    $settings[$column] = is_numeric( $value ) && strpos( $column, 'data_text' ) !== 0 ? $value + 0 : (string)$value;
            }
            if ( $settings )
                $definition['settings'] = $settings;
            $attributes[(string)$a->attribute( 'identifier' )] = $definition;
        }
        $definition = array(
            'identifier' => (string)$found->attribute( 'identifier' ),
            'names' => self::nameList( $found->nameList() ),
            'contentobject_name' => (string)$found->attribute( 'contentobject_name' ),
            'url_alias_name' => (string)$found->attribute( 'url_alias_name' ),
            'is_container' => (bool)$found->attribute( 'is_container' ),
            'always_available' => (bool)$found->attribute( 'always_available' ),
            'sort_field' => (string)eZContentObjectTreeNode::sortFieldName( (int)$found->attribute( 'sort_field' ) ),
            'sort_order' => (int)$found->attribute( 'sort_order' ) === 1 ? 'asc' : 'desc',
            'groups' => $groups,
            'attributes' => $attributes,
        );
        $descriptions = self::nameList( $found->descriptionList() );
        if ( $descriptions )
            $definition['descriptions'] = $descriptions;
        $remote = (string)$found->attribute( 'remote_id' );
        if ( $remote !== '' )
            $definition['remote_id'] = $remote;
        return $definition;
    }

    /**
     * A name list (locale => text) without the kernel's always-available entry, sorted. Pure.
     *
     * @param mixed $list
     * @return array
     */
    public static function nameList( $list )
    {
        $names = array();
        foreach ( is_array( $list ) ? $list : array() as $locale => $text )
            if ( $locale !== 'always-available' && is_string( $text ) && $text !== '' )
                $names[(string)$locale] = $text;
        ksort( $names );
        return $names;
    }

    public function role( $role )
    {
        $found = is_numeric( $role ) ? eZRole::fetch( (int)$role ) : eZRole::fetchByName( (string)$role );
        if ( !$found instanceof eZRole )
            return null;
        $policies = array();
        $notes = array();
        foreach ( (array)$found->policyList() as $policy )
        {
            $entry = array( 'module' => (string)$policy->attribute( 'module_name' ), 'function' => (string)$policy->attribute( 'function_name' ) );
            $limitations = array();
            foreach ( (array)$policy->limitationList() as $limitation )
            {
                $identifier = (string)$limitation->attribute( 'identifier' );
                $values = array();
                foreach ( (array)$limitation->attribute( 'values' ) as $v )
                    $values[] = is_object( $v ) ? (string)$v->attribute( 'value' ) : (string)$v;
                $limitations[$identifier] = $this->limitationValues( $identifier, $values, $notes );
            }
            if ( $limitations )
            {
                ksort( $limitations );
                $entry['limitations'] = $limitations;
            }
            $policies[] = $entry;
        }
        usort( $policies, function ( $a, $b ) {
            return strcmp( json_encode( $a ), json_encode( $b ) );
        } );
        return array( 'name' => (string)$found->attribute( 'name' ), 'policies' => $policies, 'notes' => array_values( array_unique( $notes ) ) );
    }

    /**
     * The values of a limitation in stable forms: classes by identifier, sections by identifier, nodes and subtrees as
     * remote:<node remote id>, states as <group>/<state>; the others as they are (with a note when they are ids).
     *
     * @param string $identifier
     * @param string[] $values
     * @param string[] $notes
     * @return string[]
     */
    protected function limitationValues( $identifier, array $values, array &$notes )
    {
        $key = strtolower( $identifier );
        $out = array();
        foreach ( $values as $value )
        {
            $stable = null;
            if ( in_array( $key, self::CLASS_LIMITATIONS, true ) )
            {
                $class = eZContentClass::fetch( (int)$value, false );
                $stable = is_array( $class ) && isset( $class['identifier'] ) ? (string)$class['identifier'] : null;
            }
            else if ( $key === 'section' || $key === 'usersection' )
                $stable = $this->sectionIdentifier( (int)$value );
            else if ( $key === 'node' )
                $stable = $this->nodeRef( (int)$value );
            else if ( $key === 'subtree' || $key === 'usersubtree' )
            {
                $ids = array_values( array_filter( explode( '/', (string)$value ) ) );
                $stable = $ids ? $this->nodeRef( (int)end( $ids ) ) : null;
            }
            else if ( strpos( $key, 'stategroup_' ) === 0 || $key === 'newstate' )
            {
                $state = eZContentObjectState::fetchById( (int)$value );
                $group = $state ? $state->group() : null;
                $stable = $state && $group ? $group->attribute( 'identifier' ) . '/' . $state->attribute( 'identifier' ) : null;
            }
            else
            {
                $out[] = (string)$value;
                if ( in_array( $key, array( 'siteaccess' ), true ) )
                    $notes[] = "the limitation $identifier is kept as the kernel stores it";
                continue;
            }
            if ( $stable === null )
            {
                $notes[] = "a value of the limitation $identifier names nothing here: kept as $value";
                $out[] = (string)$value;
            }
            else
                $out[] = $stable;
        }
        return $out;
    }

    public function section( $sectionId )
    {
        $section = eZSection::fetch( (int)$sectionId );
        if ( !$section instanceof eZSection )
            return null;
        return array( 'identifier' => (string)$section->attribute( 'identifier' ), 'name' => (string)$section->attribute( 'name' ),
                      'navigation_part' => (string)$section->attribute( 'navigation_part_identifier' ) );
    }

    public function stateGroup( $group )
    {
        $found = is_numeric( $group ) ? eZContentObjectStateGroup::fetchById( (int)$group ) : eZContentObjectStateGroup::fetchByIdentifier( (string)$group );
        if ( !$found instanceof eZContentObjectStateGroup )
            return null;
        $names = function ( $item ) {
            $list = array();
            foreach ( (array)$item->allTranslations() as $t )
            {
                $language = eZContentLanguage::fetch( (int)$t->attribute( 'language_id' ) & ~1 );
                if ( $language && (string)$t->attribute( 'name' ) !== '' )
                    $list[(string)$language->attribute( 'locale' )] = (string)$t->attribute( 'name' );
            }
            ksort( $list );
            return $list;
        };
        $states = array();
        foreach ( (array)$found->states() as $state )
            $states[(string)$state->attribute( 'identifier' )] = $names( $state );
        $default = $found->defaultLanguage();
        return array( 'identifier' => (string)$found->attribute( 'identifier' ), 'names' => $names( $found ), 'states' => $states,
                      'default_language' => $default ? (string)$default->attribute( 'locale' ) : '' );
    }

    public function sectionIdentifier( $sectionId )
    {
        $section = eZSection::fetch( (int)$sectionId );
        if ( !$section instanceof eZSection )
            return null;
        $identifier = (string)$section->attribute( 'identifier' );
        return $identifier !== '' ? $identifier : null;
    }

    public function isUser( $objectId )
    {
        $object = eZContentObject::fetch( (int)$objectId, false );
        if ( !is_array( $object ) || !isset( $object['contentclass_id'] ) )
            return false;
        return in_array( (int)$object['contentclass_id'], array_map( 'intval', (array)eZUser::contentClassIDs() ), true );
    }
}
