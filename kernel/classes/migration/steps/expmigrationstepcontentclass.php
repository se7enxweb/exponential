<?php
/**
 * File containing the expMigrationStepContentClass class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "content_class": creates, updates, ensures and deletes content classes and their attributes.
 *
 *   - type: content_class
 *     mode: ensure                     create, update, ensure (create when missing, else change what differs), delete
 *     identifier: news_article
 *     language: eng-GB
 *     name: News article
 *     names: { ger-DE: Nachrichtenartikel }
 *     name_pattern: "<title>"
 *     groups: [ Content ]
 *     attributes:
 *         - { identifier: title, type: ezstring, name: Title, names: { ger-DE: Titel }, required: true, searchable: true,
 *             settings: { data_int1: 255 } }
 *         - { identifier: body, type: ezxmltext, name: Body }
 *
 * An update adds attributes (add_attributes:, through eZSiteInstaller::addClassAttributes(), which gives every object
 * of the class the new attribute in each of its versions and languages), changes them (update_attributes:) and
 * removes them (remove_attributes:, eZSiteInstaller::removeClassAttribute(), with the attribute's data in every
 * object). The datatype settings of an attribute are the columns of the class attribute (settings: data_int1 ...
 * data_text5), as a datatype's class edit stores them. A delete removes the objects of the class too, the way the
 * administration does (eZContentClassOperations::remove()), and only with with_objects: true when there are any.
 *
 * A create undoes itself: the class is removed again while it has no objects.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepContentClass extends expMigrationStepStructureBase implements expMigrationPartlyRevertibleStepType
{
    /** The columns of a class attribute that settings: can write. */
    const SETTING_COLUMNS = array( 'data_int1', 'data_int2', 'data_int3', 'data_int4', 'data_float1', 'data_float2', 'data_float3',
                                   'data_float4', 'data_text1', 'data_text2', 'data_text3', 'data_text4', 'data_text5' );

    /** The keys of an attribute of attributes:, add_attributes: and update_attributes:. */
    const ATTRIBUTE_KEYS = array( 'identifier', 'type', 'language', 'name', 'names', 'description', 'descriptions', 'required', 'searchable',
                                  'translatable', 'information_collector', 'category', 'placement', 'settings' );

    /** The flags of an attribute and their columns. */
    const ATTRIBUTE_FLAGS = array( 'required' => 'is_required', 'searchable' => 'is_searchable', 'translatable' => 'can_translate',
                                   'information_collector' => 'is_information_collector' );

    /** @return string */
    public function name()
    {
        return 'content_class';
    }

    /** @return array */
    public function schema()
    {
        $writing = array( 'create', 'update', 'ensure' );
        return array(
            'description' => 'Creates, updates, ensures or deletes a content class with its attributes, their datatype settings and the translations of their names',
            'modes' => array( 'create', 'update', 'ensure', 'delete' ),
            'keys' => array(
                'class' => array( 'type' => 'identifier', 'kind' => 'class', 'modes' => array( 'update', 'delete' ),
                                  'description' => 'the class to update or delete: its identifier, id or remote:<remote id>' ),
                'identifier' => array( 'type' => 'string', 'modes' => $writing,
                                       'description' => 'the identifier of the class; ensure finds the class by it (update: a new identifier)' ),
                'remote_id' => array( 'type' => 'string', 'modes' => array( 'create', 'ensure' ),
                                      'description' => 'the remote id of a new class (default: a new one)' ),
            ) + self::nameKeys( $writing, 'the class', true ) + array(
                'name_pattern' => array( 'type' => 'string', 'modes' => $writing,
                                         'description' => 'how an object is named from its attributes (<title>; default: the first attribute)' ),
                'url_alias_pattern' => array( 'type' => 'string', 'modes' => $writing,
                                              'description' => 'how the URL alias of an object is made (default: as its name)' ),
                'container' => array( 'type' => 'bool', 'modes' => $writing, 'description' => 'whether objects of the class can have children' ),
                'always_available' => array( 'type' => 'bool', 'modes' => $writing,
                                             'description' => 'whether a new object of the class is shown in a language it does not have' ),
                'sort_field' => array( 'type' => 'string', 'values' => expMigrationStepLocation::SORT_FIELDS, 'modes' => $writing,
                                       'description' => 'how the children of a new object are sorted' ),
                'sort_order' => array( 'type' => 'string', 'values' => array( 'asc', 'desc' ), 'modes' => $writing,
                                       'description' => 'asc or desc' ),
                'groups' => array( 'type' => 'list', 'modes' => $writing,
                                   'description' => 'the class groups the class is in: names or ids (create, ensure: default Content; update and ensure add the class to those it is not in)' ),
                'attributes' => array( 'type' => 'list', 'modes' => array( 'create', 'ensure' ),
                                       'description' => 'the attributes, each a map: identifier, type (the datatype), name, names, description, descriptions, required, searchable, translatable, information_collector, category, placement, settings; ensure adds the missing ones and changes what differs' ),
                'add_attributes' => array( 'type' => 'list', 'modes' => array( 'update' ),
                                           'description' => 'attributes to add, as in attributes:; every object of the class gets them' ),
                'update_attributes' => array( 'type' => 'map', 'modes' => array( 'update' ),
                                              'description' => 'attribute identifier => what changes (the keys of attributes: but identifier and type)' ),
                'remove_attributes' => array( 'type' => 'list', 'modes' => array( 'update' ),
                                              'description' => 'the identifiers of attributes to remove, with their data in every object' ),
                'with_objects' => array( 'type' => 'bool', 'default' => false, 'modes' => array( 'delete' ),
                                         'description' => 'true: a class with objects is deleted with them; false (the default): it is not deleted' ),
            ) + self::missingKey( array( 'update', 'delete' ), 'the class' ),
            'values' => array(
                'class_id' => 'the id of the class',
                'identifier' => 'its identifier',
                'remote_id' => 'its remote id',
                'name' => 'its name',
                'created' => 'true when the step created the class',
                'changed' => 'true when the step changed it (created, updated or deleted)',
            ),
            'example' => "- type: content_class\n  mode: ensure\n  identifier: news_article\n  name: News article\n  names: { ger-DE: Nachrichtenartikel }\n"
                       . "  name_pattern: \"<title>\"\n  groups: [ Content ]\n  attributes:\n"
                       . "    - { identifier: title, type: ezstring, name: Title, names: { ger-DE: Titel }, required: true, searchable: true, settings: { data_int1: 255 } }\n"
                       . "    - { identifier: body, type: ezxmltext, name: Body }\n  references: { news_class: class_id }\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $mode = isset( $step['mode'] ) ? $step['mode'] : null;
        $errors = self::translationProblems( $step ) + array();
        $errors = array_merge( $errors, array_diff( self::translationProblems( $step, 'description', 'descriptions' ), $errors ) );
        if ( in_array( $mode, array( 'create', 'ensure' ), true ) && ( !isset( $step['identifier'] ) || (string)$step['identifier'] === '' ) )
            $errors[] = "identifier is missing (mode $mode)";
        if ( isset( $step['identifier'] ) && !expMigrationReferences::contains( $step['identifier'] ) && !preg_match( '/^[a-z][a-z0-9_]*$/', (string)$step['identifier'] ) )
            $errors[] = 'identifier: ' . expMigrationStepSchema::show( $step['identifier'] ) . ' is not a class identifier (lower case letters, digits and _)';
        if ( in_array( $mode, array( 'update', 'delete' ), true ) && !isset( $step['class'] ) )
            $errors[] = "class is missing (mode $mode)";
        if ( isset( $step['groups'] ) && is_array( $step['groups'] ) )
            foreach ( $step['groups'] as $i => $group )
                if ( ( $problem = expMigrationIdentifier::problem( 'class_group', $group ) ) !== null )
                    $errors[] = "groups[$i]: $problem";
        foreach ( array( 'attributes', 'add_attributes' ) as $key )
        {
            if ( !isset( $step[$key] ) || !is_array( $step[$key] ) )
                continue;
            $seen = array();
            foreach ( $step[$key] as $i => $attribute )
            {
                $errors = array_merge( $errors, self::attributeProblems( $attribute, "{$key}[$i]", true ) );
                if ( is_array( $attribute ) && isset( $attribute['identifier'] ) )
                {
                    if ( isset( $seen[$attribute['identifier']] ) )
                        $errors[] = "{$key}[$i]: the attribute " . $attribute['identifier'] . ' is there twice';
                    $seen[$attribute['identifier']] = true;
                }
            }
        }
        if ( isset( $step['update_attributes'] ) && is_array( $step['update_attributes'] ) )
            foreach ( $step['update_attributes'] as $identifier => $attribute )
                $errors = array_merge( $errors, self::attributeProblems( is_array( $attribute ) ? array( 'identifier' => (string)$identifier ) + $attribute : $attribute,
                                                                        "update_attributes.$identifier", false ) );
        if ( isset( $step['remove_attributes'] ) && is_array( $step['remove_attributes'] ) )
            foreach ( $step['remove_attributes'] as $i => $identifier )
                if ( !is_string( $identifier ) || !preg_match( '/^[a-z0-9_]+$/', $identifier ) )
                    $errors[] = "remove_attributes[$i]: an attribute identifier (lower case letters, digits and _)";
        if ( $mode === 'update' && !array_intersect( array_keys( $step ), array( 'identifier', 'name', 'names', 'description', 'descriptions', 'name_pattern',
                                                                                 'url_alias_pattern', 'container', 'always_available', 'sort_field', 'sort_order',
                                                                                 'groups', 'add_attributes', 'update_attributes', 'remove_attributes' ) ) )
            $errors[] = 'nothing to update';
        return array_values( array_unique( $errors ) );
    }

    /**
     * The problems of one attribute map. Pure.
     *
     * @param mixed $attribute
     * @param string $where
     * @param bool $new whether it is added (type is required then)
     * @return string[]
     */
    public static function attributeProblems( $attribute, $where, $new )
    {
        if ( !is_array( $attribute ) || ( $attribute && array_keys( $attribute ) === range( 0, count( $attribute ) - 1 ) ) )
            return expMigrationReferences::contains( $attribute ) ? array() : array( "$where: an attribute is a map (identifier, type, name ...)" );
        $errors = array();
        foreach ( array_diff( array_keys( $attribute ), self::ATTRIBUTE_KEYS ) as $key )
            $errors[] = "$where: the unknown key $key (" . implode( ', ', self::ATTRIBUTE_KEYS ) . ')';
        if ( !isset( $attribute['identifier'] ) || !is_string( $attribute['identifier'] ) || !preg_match( '/^[a-z][a-z0-9_]*$/', $attribute['identifier'] ) )
            $errors[] = "$where: identifier is missing or not an attribute identifier (lower case letters, digits and _)";
        if ( $new && ( !isset( $attribute['type'] ) || !is_string( $attribute['type'] ) || !preg_match( '/^[a-z][a-z0-9_]*$/', $attribute['type'] ) ) )
            $errors[] = "$where: type (the datatype, e.g. ezstring) is missing";
        if ( !$new && isset( $attribute['type'] ) )
            $errors[] = "$where: the datatype of an attribute cannot be changed (remove it and add a new one)";
        foreach ( array_keys( self::ATTRIBUTE_FLAGS ) as $flag )
            if ( isset( $attribute[$flag] ) && !is_bool( $attribute[$flag] ) )
                $errors[] = "$where.$flag: true or false";
        if ( isset( $attribute['placement'] ) && !is_int( $attribute['placement'] ) )
            $errors[] = "$where.placement: a whole number";
        if ( isset( $attribute['settings'] ) )
        {
            if ( !is_array( $attribute['settings'] ) )
                $errors[] = "$where.settings: a map of column => value (" . implode( ', ', self::SETTING_COLUMNS ) . ')';
            else
                foreach ( $attribute['settings'] as $column => $value )
                {
                    if ( !in_array( $column, self::SETTING_COLUMNS, true ) )
                        $errors[] = "$where.settings: $column is not a setting column (" . implode( ', ', self::SETTING_COLUMNS ) . ')';
                    else if ( !is_scalar( $value ) && $value !== null )
                        $errors[] = "$where.settings.$column: a text or a number";
                }
        }
        foreach ( self::translationProblems( $attribute ) as $problem )
            $errors[] = "$where: $problem";
        foreach ( self::translationProblems( $attribute, 'description', 'descriptions' ) as $problem )
            $errors[] = "$where: $problem";
        return $errors;
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function caches( array $step )
    {
        return array( 'classid', 'content', 'sortkey' );
    }

    /**
     * @param array $step
     * @return bool
     */
    public function revertible( array $step )
    {
        return isset( $step['mode'] ) && $step['mode'] === 'create';
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $names = self::namesText( $step );
        $attributes = function ( $key ) use ( $step ) {
            $list = array();
            foreach ( isset( $step[$key] ) && is_array( $step[$key] ) ? $step[$key] : array() as $attribute )
                if ( is_array( $attribute ) && isset( $attribute['identifier'] ) )
                    $list[] = $attribute['identifier'] . ( isset( $attribute['type'] ) ? ' (' . $attribute['type'] . ')' : '' );
            return $list;
        };
        switch ( $step['mode'] )
        {
            case 'create':
            case 'ensure':
                $list = $attributes( 'attributes' );
                $lines = array( ( $step['mode'] === 'create' ? 'create' : 'ensure' ) . ' the class ' . $step['identifier'] . $names
                                . ( $step['mode'] === 'ensure' ? ': create it when missing, change what differs' : '' ) );
                if ( $list )
                    $lines[] = 'with the attributes ' . implode( ', ', $list );
                return $lines;
            case 'update':
                $target = 'the class ' . expMigrationStepSchema::show( $step['class'] );
                $lines = array();
                $changes = array_intersect( array_keys( $step ), array( 'identifier', 'name', 'names', 'description', 'descriptions', 'name_pattern',
                                                                        'url_alias_pattern', 'container', 'always_available', 'sort_field', 'sort_order', 'groups' ) );
                if ( $changes )
                    $lines[] = "update $target: " . implode( ', ', $changes );
                if ( $list = $attributes( 'add_attributes' ) )
                    $lines[] = "add to $target the attributes " . implode( ', ', $list );
                if ( isset( $step['update_attributes'] ) && is_array( $step['update_attributes'] ) )
                    foreach ( $step['update_attributes'] as $identifier => $attribute )
                        $lines[] = "change the attribute $identifier of $target: " . implode( ', ', is_array( $attribute ) ? array_keys( $attribute ) : array() );
                if ( isset( $step['remove_attributes'] ) && is_array( $step['remove_attributes'] ) )
                    $lines[] = "remove from $target the attributes " . implode( ', ', $step['remove_attributes'] ) . ' with their data in every object';
                return $lines;
            case 'delete':
                return array( 'delete the class ' . expMigrationStepSchema::show( $step['class'] ) . ( !empty( $step['with_objects'] ) ? ' with its objects' : ' (only when it has no objects)' ) );
        }
        return parent::plan( $step, $context );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        switch ( $step['mode'] )
        {
            case 'create':
                if ( eZContentClass::fetchByIdentifier( (string)$step['identifier'], false ) )
                    throw new RuntimeException( 'the class ' . $step['identifier'] . ' is there already (mode ensure changes it)' );
                return $this->create( $step );
            case 'ensure':
                $class = eZContentClass::fetchByIdentifier( (string)$step['identifier'] );
                return $class instanceof eZContentClass ? $this->update( $class, $step, true ) : $this->create( $step );
            case 'update':
            case 'delete':
                $id = expMigrationIdentifier::find( 'class', $step['class'] );
                $class = $id !== null ? eZContentClass::fetch( $id ) : null;
                if ( !$class instanceof eZContentClass )
                    return self::missingResult( $step, 'the class ' . expMigrationStepSchema::show( $step['class'] ),
                                                array( 'class_id' => null, 'identifier' => null, 'remote_id' => null, 'name' => null ) );
                return $step['mode'] === 'update' ? $this->update( $class, $step, false ) : $this->delete( $class, !empty( $step['with_objects'] ) );
        }
        throw new RuntimeException( 'the mode ' . $step['mode'] . ' is not one of create, update, ensure, delete' );
    }

    /**
     * Removes the class a create made, while it has no objects.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        if ( !$this->revertible( $step ) )
            throw new RuntimeException( 'only a create can be undone by itself; give the migration a down:' );
        $class = eZContentClass::fetchByIdentifier( (string)$step['identifier'] );
        if ( !$class instanceof eZContentClass )
            return array( 'the class ' . $step['identifier'] . ' is not there: nothing to undo' );
        $result = $this->delete( $class, false );
        return $result['lines'];
    }

    // ---- the modes ---------------------------------------------------------------------------------------------

    /**
     * @param array $step
     * @return array
     */
    protected function create( array $step )
    {
        $language = expMigrationStepContentBase::localeOf( isset( $step['language'] ) ? $step['language'] : null );
        $names = self::translations( $step );
        if ( !isset( $names[$language] ) )
            $names = array( $language => ucfirst( str_replace( '_', ' ', (string)$step['identifier'] ) ) ) + $names;
        $groups = $this->groups( isset( $step['groups'] ) ? $step['groups'] : array( 'Content' ), !isset( $step['groups'] ) );
        $attributes = isset( $step['attributes'] ) && is_array( $step['attributes'] ) ? $step['attributes'] : array();
        $pattern = isset( $step['name_pattern'] ) ? (string)$step['name_pattern'] : ( $attributes ? '<' . $attributes[0]['identifier'] . '>' : '' );

        $db = eZDB::instance();
        $db->begin();
        try
        {
            $values = array( 'version' => eZContentClass::VERSION_STATUS_DEFINED, 'identifier' => (string)$step['identifier'],
                             'contentobject_name' => $pattern, 'name' => $names[$language] );
            if ( isset( $step['remote_id'] ) && (string)$step['remote_id'] !== '' )
                $values['remote_id'] = (string)$step['remote_id'];
            $class = eZContentClass::create( eZUser::currentUserID(), $values, $language );
            foreach ( $names as $locale => $name )
                $class->setName( $name, $locale );
            $this->setClassFields( $class, $step, $language );
            $class->store();
            foreach ( $groups as $group )
                eZContentClassClassGroup::create( (int)$class->attribute( 'id' ), eZContentClass::VERSION_STATUS_DEFINED,
                                                  (int)$group->attribute( 'id' ), (string)$group->attribute( 'name' ) )->store();
            $added = array();
            foreach ( $attributes as $attribute )
            {
                $this->addAttribute( $class, $attribute, $language );
                $added[] = $attribute['identifier'];
            }
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        $class = self::fresh( $class );
        $lines = array( 'created the class ' . $class->attribute( 'identifier' ) . ' "' . $class->attribute( 'name' ) . '" (' . $class->attribute( 'id' ) . ')'
                        . ( $added ? ' with the attributes ' . implode( ', ', $added ) : '' ) );
        return self::result( $lines, self::values( $class ) + array( 'created' => true, 'changed' => true ) );
    }

    /**
     * Updates a class; $ensure: only what differs, and the attributes of attributes: are added or changed.
     *
     * @param eZContentClass $class
     * @param array $step
     * @param bool $ensure
     * @return array
     */
    protected function update( eZContentClass $class, array $step, $ensure )
    {
        $language = expMigrationStepContentBase::localeOf( isset( $step['language'] ) ? $step['language'] : null );
        $label = 'the class ' . $class->attribute( 'identifier' );
        $lines = array();
        $db = eZDB::instance();
        $db->begin();
        try
        {

            $changed = $this->classChanges( $class, $step, $language, $ensure );
            if ( $changed )
            {
                $class->setAttribute( 'modified', time() );
                $class->setAttribute( 'modifier_id', eZUser::currentUserID() );
                $class->store();
                $lines[] = "updated $label: " . implode( ', ', $changed );
            }
            if ( isset( $step['groups'] ) )
            {
                $in = array();
                foreach ( (array)eZContentClassClassGroup::fetchGroupList( (int)$class->attribute( 'id' ), eZContentClass::VERSION_STATUS_DEFINED ) as $link )
                    $in[] = (int)$link->attribute( 'group_id' );
                foreach ( $this->groups( $step['groups'], false ) as $group )
                {
                    if ( in_array( (int)$group->attribute( 'id' ), $in, true ) )
                        continue;
                    eZContentClassClassGroup::create( (int)$class->attribute( 'id' ), eZContentClass::VERSION_STATUS_DEFINED,
                                                      (int)$group->attribute( 'id' ), (string)$group->attribute( 'name' ) )->store();
                    $lines[] = "added $label to the class group \"" . $group->attribute( 'name' ) . '"';
                }
            }

            $add = $ensure ? array() : ( isset( $step['add_attributes'] ) ? (array)$step['add_attributes'] : array() );
            $change = $ensure ? array() : ( isset( $step['update_attributes'] ) ? (array)$step['update_attributes'] : array() );
            if ( $ensure && isset( $step['attributes'] ) )
            {
                foreach ( (array)$step['attributes'] as $attribute )
                {
                    $existing = $class->fetchAttributeByIdentifier( $attribute['identifier'] );
                    if ( !$existing instanceof eZContentClassAttribute )
                    {
                        $add[] = $attribute;
                        continue;
                    }
                    if ( $existing->attribute( 'data_type_string' ) !== $attribute['type'] )
                        throw new RuntimeException( "$label: the attribute " . $attribute['identifier'] . ' is a ' . $existing->attribute( 'data_type_string' )
                                                    . ', not a ' . $attribute['type'] . ' (a datatype cannot be changed)' );
                    $rest = $attribute;
                    unset( $rest['identifier'], $rest['type'] );
                    $change[$attribute['identifier']] = $rest;
                }
            }
            foreach ( $add as $attribute )
            {
                if ( $class->fetchAttributeByIdentifier( $attribute['identifier'] ) instanceof eZContentClassAttribute )
                    throw new RuntimeException( "$label has the attribute " . $attribute['identifier'] . ' already' );
                $this->addAttribute( $class, $attribute, $language );
                $lines[] = "added to $label the attribute " . $attribute['identifier'] . ' (' . $attribute['type'] . ')';
            }
            foreach ( $change as $identifier => $attribute )
            {
                $existing = $class->fetchAttributeByIdentifier( (string)$identifier );
                if ( !$existing instanceof eZContentClassAttribute )
                    throw new RuntimeException( "$label has no attribute $identifier" );
                $fields = $this->attributeChanges( $existing, (array)$attribute, $language, $ensure );
                if ( $fields )
                {
                    $existing->store();
                    $lines[] = "changed the attribute $identifier of $label: " . implode( ', ', $fields );
                }
            }
            if ( !$ensure && isset( $step['remove_attributes'] ) )
            {
                foreach ( (array)$step['remove_attributes'] as $identifier )
                {
                    if ( !$class->fetchAttributeByIdentifier( (string)$identifier ) instanceof eZContentClassAttribute )
                        throw new RuntimeException( "$label has no attribute $identifier to remove" );
                    $installer = self::installer();
                    $installer->removeClassAttribute( array( 'class_id' => (int)$class->attribute( 'id' ), 'attribute_identifier' => (string)$identifier ) );
                    self::checkInstaller( $installer, "removing the attribute $identifier of $label" );
                    $lines[] = "removed the attribute $identifier from $label with its data";
                }
            }
            if ( isset( $step['update_attributes'] ) || isset( $step['add_attributes'] ) || isset( $step['remove_attributes'] ) || $ensure )
                self::placeAttributes( $class );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();

        $class = self::fresh( $class );
        $changedAny = (bool)$lines;
        if ( !$lines )
            $lines[] = "$label is as the step has it";
        return self::result( $lines, self::values( $class ) + array( 'created' => false, 'changed' => $changedAny ) );
    }

    /**
     * @param eZContentClass $class
     * @param bool $withObjects
     * @return array
     */
    protected function delete( eZContentClass $class, $withObjects )
    {
        $values = self::values( $class );
        $count = (int)$class->objectCount();
        if ( $count > 0 && !$withObjects )
            throw new RuntimeException( 'the class ' . $values['identifier'] . " has $count object(s): with_objects: true deletes them with it" );
        if ( !$class->isRemovable() )
            throw new RuntimeException( 'the class ' . $values['identifier'] . ' cannot be removed (an object of it is a top level node, or the class is the user class of the site)' );
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $removed = eZContentClassOperations::remove( (int)$values['class_id'] );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        if ( !$removed )
            throw new RuntimeException( 'the class ' . $values['identifier'] . ' could not be removed' );
        return self::result( array( 'deleted the class ' . $values['identifier'] . ' (' . $values['class_id'] . ')' . ( $count ? " with its $count object(s)" : '' ) ),
                             $values + array( 'created' => false, 'changed' => true ) );
    }

    // ---- helpers ---------------------------------------------------------------------------------------------

    /**
     * Writes the class fields of a step into a class (not stored).
     *
     * @param eZContentClass $class
     * @param array $step
     * @param string $language
     */
    protected function setClassFields( eZContentClass $class, array $step, $language )
    {
        foreach ( self::translations( $step, 'description', 'descriptions' ) as $locale => $text )
            $class->setDescription( $text, $locale );
        foreach ( self::classColumns( $step ) as $column => $value )
            $class->setAttribute( $column, $value );
    }

    /**
     * The columns of a class a step sets. Pure.
     *
     * @param array $step
     * @return array column => value
     */
    protected static function classColumns( array $step )
    {
        $columns = array();
        if ( isset( $step['name_pattern'] ) )
            $columns['contentobject_name'] = (string)$step['name_pattern'];
        if ( isset( $step['url_alias_pattern'] ) )
            $columns['url_alias_name'] = (string)$step['url_alias_pattern'];
        if ( isset( $step['container'] ) )
            $columns['is_container'] = self::flag( $step['container'] );
        if ( isset( $step['always_available'] ) )
            $columns['always_available'] = self::flag( $step['always_available'] );
        if ( isset( $step['sort_field'] ) )
            $columns['sort_field'] = (int)eZContentObjectTreeNode::sortFieldID( (string)$step['sort_field'] );
        if ( isset( $step['sort_order'] ) )
            $columns['sort_order'] = $step['sort_order'] === 'asc' ? 1 : 0;
        return $columns;
    }

    /**
     * Sets on a class what the step changes, and lists it; with $onlyChanges only what differs.
     *
     * @param eZContentClass $class
     * @param array $step
     * @param string $language
     * @param bool $onlyChanges
     * @return string[] what changed
     */
    protected function classChanges( eZContentClass $class, array $step, $language, $onlyChanges )
    {
        $changed = array();
        if ( isset( $step['identifier'] ) && (string)$class->attribute( 'identifier' ) !== (string)$step['identifier'] )
        {
            if ( eZContentClass::fetchByIdentifier( (string)$step['identifier'], false ) )
                throw new RuntimeException( 'another class has the identifier ' . $step['identifier'] );
            $class->setAttribute( 'identifier', (string)$step['identifier'] );
            $changed[] = 'identifier';
        }
        foreach ( self::classColumns( $step ) as $column => $value )
        {
            if ( (string)$class->attribute( $column ) === (string)$value )
                continue;
            $class->setAttribute( $column, $value );
            $changed[] = $column;
        }
        $names = $class->nameList();
        foreach ( self::translations( $step ) as $locale => $name )
        {
            if ( isset( $names[$locale] ) && (string)$names[$locale] === $name )
                continue;
            $class->setName( $name, $locale );
            $changed[] = "name ($locale)";
        }
        $descriptions = $class->descriptionList();
        foreach ( self::translations( $step, 'description', 'descriptions' ) as $locale => $text )
        {
            if ( isset( $descriptions[$locale] ) && (string)$descriptions[$locale] === $text )
                continue;
            $class->setDescription( $text, $locale );
            $changed[] = "description ($locale)";
        }
        return $changed;
    }

    /**
     * Adds an attribute through eZSiteInstaller::addClassAttributes() (every object gets it), then sets what that
     * helper does not: further names, descriptions, the datatype settings, flags, category and placement.
     *
     * @param eZContentClass $class
     * @param array $attribute
     * @param string $language
     */
    protected function addAttribute( eZContentClass $class, array $attribute, $language )
    {
        $own = isset( $attribute['language'] ) ? expMigrationStepContentBase::localeOf( $attribute['language'] ) : $language;
        $names = self::translations( array( 'language' => $own ) + $attribute );
        $name = isset( $names[$own] ) ? $names[$own] : ucfirst( str_replace( '_', ' ', $attribute['identifier'] ) );
        $installer = self::installer();
        $installer->addClassAttributes( array(
            'class' => array( 'id' => (int)$class->attribute( 'id' ) ),
            'attributes' => array( array(
                'identifier' => $attribute['identifier'],
                'name' => $name,
                'data_type_string' => $attribute['type'],
                'can_translate' => isset( $attribute['translatable'] ) ? self::flag( $attribute['translatable'] ) : 1,
                'is_required' => !empty( $attribute['required'] ) ? 1 : 0,
                'is_searchable' => isset( $attribute['searchable'] ) ? self::flag( $attribute['searchable'] ) : 1,
                'language' => $own,
            ) ),
        ) );
        self::checkInstaller( $installer, 'adding the attribute ' . $attribute['identifier'] . ' to the class ' . $class->attribute( 'identifier' ) );
        $added = $class->fetchAttributeByIdentifier( $attribute['identifier'] );
        if ( !$added instanceof eZContentClassAttribute )
            throw new RuntimeException( 'the attribute ' . $attribute['identifier'] . ' was not added (is ' . $attribute['type'] . ' a datatype of this installation?)' );
        $rest = $attribute;
        unset( $rest['identifier'], $rest['type'] );
        if ( $this->attributeChanges( $added, $rest, $language, false ) )
            $added->store();
    }

    /**
     * Sets on an attribute what an attribute map changes (not stored), and lists it; with $onlyChanges only what
     * differs.
     *
     * @param eZContentClassAttribute $attribute
     * @param array $change
     * @param string $language
     * @param bool $onlyChanges
     * @return string[] what changed
     */
    protected function attributeChanges( eZContentClassAttribute $attribute, array $change, $language, $onlyChanges )
    {
        $changed = array();
        $own = isset( $change['language'] ) ? expMigrationStepContentBase::localeOf( $change['language'] ) : $language;
        $names = $attribute->nameList();
        foreach ( self::translations( array( 'language' => $own ) + $change ) as $locale => $name )
        {
            if ( isset( $names[$locale] ) && (string)$names[$locale] === $name )
                continue;
            $attribute->setName( $name, $locale );
            $changed[] = "name ($locale)";
        }
        $descriptions = $attribute->descriptionList();
        foreach ( self::translations( array( 'language' => $own ) + $change, 'description', 'descriptions' ) as $locale => $text )
        {
            if ( isset( $descriptions[$locale] ) && (string)$descriptions[$locale] === $text )
                continue;
            $attribute->setDescription( $text, $locale );
            $changed[] = "description ($locale)";
        }
        $columns = isset( $change['settings'] ) && is_array( $change['settings'] ) ? $change['settings'] : array();
        foreach ( self::ATTRIBUTE_FLAGS as $flag => $column )
            if ( isset( $change[$flag] ) )
                $columns[$column] = self::flag( $change[$flag] );
        if ( isset( $change['category'] ) )
            $columns['category'] = (string)$change['category'];
        if ( isset( $change['placement'] ) )
            $columns['placement'] = (int)$change['placement'];
        foreach ( $columns as $column => $value )
        {
            if ( (string)$attribute->attribute( $column ) === (string)$value )
                continue;
            $attribute->setAttribute( $column, $value );
            $changed[] = $column;
        }
        return $changed;
    }

    /**
     * Numbers the attributes of a class 1, 2, 3 ... in their order of placement.
     *
     * @param eZContentClass $class
     */
    protected static function placeAttributes( eZContentClass $class )
    {
        $attributes = $class->fetchAttributes();
        usort( $attributes, function ( $a, $b ) {
            return array( (int)$a->attribute( 'placement' ), (int)$a->attribute( 'id' ) ) <=> array( (int)$b->attribute( 'placement' ), (int)$b->attribute( 'id' ) );
        } );
        $position = 0;
        foreach ( $attributes as $attribute )
        {
            if ( (int)$attribute->attribute( 'placement' ) === ++$position )
                continue;
            $attribute->setAttribute( 'placement', $position );
            $attribute->store();
        }
    }

    /**
     * The class groups of a groups: list.
     *
     * @param array $groups
     * @param bool $default whether the list is the default (Content): an installation without it takes its first group
     * @return eZContentClassGroup[]
     * @throws RuntimeException for a group that is not there
     */
    protected function groups( array $groups, $default )
    {
        $found = array();
        foreach ( $groups as $name )
        {
            $id = expMigrationIdentifier::find( 'class_group', $name );
            $group = $id !== null ? eZContentClassGroup::fetch( $id ) : null;
            if ( !$group instanceof eZContentClassGroup && $default )
            {
                $all = eZContentClassGroup::fetchList();
                $group = $all ? reset( $all ) : null;
            }
            if ( !$group instanceof eZContentClassGroup )
                throw new RuntimeException( 'no class group ' . expMigrationStepSchema::show( $name ) . ' on this installation (content_class_group creates one)' );
            $found[(int)$group->attribute( 'id' )] = $group;
        }
        return array_values( $found );
    }

    /**
     * @param eZContentClass $class
     * @return eZContentClass the class fresh from the database
     */
    protected static function fresh( eZContentClass $class )
    {
        eZContentClass::expireCache();
        $fresh = eZContentClass::fetch( (int)$class->attribute( 'id' ), true, eZContentClass::VERSION_STATUS_DEFINED, false );
        return $fresh instanceof eZContentClass ? $fresh : $class;
    }

    /**
     * @param eZContentClass $class
     * @return array
     */
    protected static function values( eZContentClass $class )
    {
        return array( 'class_id' => (int)$class->attribute( 'id' ), 'identifier' => (string)$class->attribute( 'identifier' ),
                      'remote_id' => (string)$class->attribute( 'remote_id' ), 'name' => (string)$class->attribute( 'name' ) );
    }
}
