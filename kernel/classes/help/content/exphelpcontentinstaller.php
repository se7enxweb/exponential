<?php
/**
 * Creates the content model of help maintained as content on an installed site, idempotently: the eight help_*
 * classes (class group "Help"), the sections help_public and help_internal, the object state group help_status
 * (Draft, Review, Released), the folder tree below the parent node, the roles "Help editor" and "Help release" and
 * the one additional policy of Anonymous. A second run changes nothing ("No change.").
 *
 * Existing classes are only extended: a missing attribute is added (and initialised on the existing objects), a
 * missing selection option is appended at the end; an existing attribute is never changed otherwise. Policies are
 * compared by module, function and limitation values; a missing one is added, nothing is removed.
 *
 * Every step records what it does (or, with dry-run, would do) in changes(): array( step, action, what ).
 *
 * Two kernel facts: the first state of a new state group is given to every object of the installation (one INSERT,
 * harmless while only help policies evaluate the group), and every state translation gets a description (MySQL has
 * no default for ezcobj_state_language.description, so a strict INSERT without one fails).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentInstaller
{
    /** @var bool */
    protected $dryRun;

    /** @var string the locale of names (classes, states, folders) */
    protected $language;

    /** @var int the user who creates (class creator, object owner) */
    protected $userId;

    /** @var array */
    protected $changes = array();

    /**
     * @param array $options dry_run (bool), language (locale, default: the default language), user_id (default: current)
     */
    public function __construct( array $options = array() )
    {
        $this->dryRun = !empty( $options['dry_run'] );
        $this->language = !empty( $options['language'] ) ? (string)$options['language'] : (string)eZContentObject::defaultLanguage();
        $this->userId = !empty( $options['user_id'] ) ? (int)$options['user_id'] : (int)eZUser::currentUserID();
    }

    /** @return array the changes: list of array( step, action, what ) */
    public function changes()
    {
        return $this->changes;
    }

    /** @return bool */
    public function isDryRun()
    {
        return $this->dryRun;
    }

    /**
     * classes, tree, roles (in that order: the tree needs the sections and states, the roles the tree)
     *
     * @param string[] $steps
     * @return array changes
     */
    public function install( array $steps = array( 'classes', 'tree', 'roles' ) )
    {
        if ( in_array( 'classes', $steps, true ) )
            $this->installClasses();
        if ( in_array( 'tree', $steps, true ) )
            $this->installTree();
        if ( in_array( 'roles', $steps, true ) )
            $this->installRoles();
        return $this->changes;
    }

    // ---------------------------------------------------------------- classes

    public function installClasses()
    {
        $group = eZContentClassGroup::fetchByName( expHelpContentSchema::CLASS_GROUP );
        if ( !$group instanceof eZContentClassGroup )
        {
            $this->change( 'classes', 'create', 'class group "' . expHelpContentSchema::CLASS_GROUP . '"' );
            if ( !$this->dryRun )
            {
                $group = eZContentClassGroup::create( $this->userId );
                $group->setAttribute( 'name', expHelpContentSchema::CLASS_GROUP );
                $group->store();
            }
        }
        // the topic classes first: the relation lists of the maps name them
        $classes = expHelpContentSchema::classes();
        foreach ( $classes as $identifier => $definition )
            $this->ensureClass( $identifier, $definition, $group );
        if ( !$this->dryRun )
        {
            eZContentClass::expireCache();
            eZContentClassAttribute::expireCache();
        }
    }

    protected function ensureClass( $identifier, array $definition, $group )
    {
        $class = eZContentClass::fetchByIdentifier( $identifier, true, eZContentClass::VERSION_STATUS_DEFINED );
        if ( !$class instanceof eZContentClass )
        {
            $this->change( 'classes', 'create', "class $identifier with " . count( $definition['attributes'] ) . ' attributes' );
            if ( $this->dryRun )
                return;
            $db = eZDB::instance();
            $db->begin();
            $class = eZContentClass::create( $this->userId, array(
                'version' => eZContentClass::VERSION_STATUS_DEFINED,
                'identifier' => $identifier,
                'remote_id' => 'help-class-' . $identifier,
                'contentobject_name' => $definition['name_pattern'],
                'is_container' => $definition['container'] ? 1 : 0,
                'always_available' => 0,
                'name' => $definition['name'],
                'description' => $definition['description'],
            ), $this->language );
            $class->store();
            $placement = 0;
            foreach ( $definition['attributes'] as $attributeIdentifier => $attribute )
                $this->createAttribute( $class, $attributeIdentifier, $attribute, ++$placement, false );
            if ( $group instanceof eZContentClassGroup )
                eZContentClassClassGroup::create( $class->attribute( 'id' ), eZContentClass::VERSION_STATUS_DEFINED,
                                                  $group->attribute( 'id' ), $group->attribute( 'name' ) )->store();
            $db->commit();
            return;
        }

        $existing = array();
        $placement = 0;
        foreach ( $class->fetchAttributes() as $attribute )
        {
            $existing[$attribute->attribute( 'identifier' )] = $attribute;
            $placement = max( $placement, (int)$attribute->attribute( 'placement' ) );
        }
        foreach ( $definition['attributes'] as $attributeIdentifier => $attribute )
        {
            if ( !isset( $existing[$attributeIdentifier] ) )
            {
                $this->change( 'classes', 'extend', "class $identifier: add attribute $attributeIdentifier ({$attribute['type']})" );
                if ( !$this->dryRun )
                {
                    $db = eZDB::instance();
                    $db->begin();
                    $this->createAttribute( $class, $attributeIdentifier, $attribute, ++$placement, true );
                    $db->commit();
                }
                continue;
            }
            if ( $attribute['type'] === 'ezselection' && $existing[$attributeIdentifier]->attribute( 'data_type_string' ) === 'ezselection' )
                $this->appendOptions( $identifier, $existing[$attributeIdentifier], $attribute['params']['options'] );
        }
        if ( $group instanceof eZContentClassGroup )
        {
            $inGroup = false;
            foreach ( (array)eZContentClassClassGroup::fetchGroupList( $class->attribute( 'id' ), eZContentClass::VERSION_STATUS_DEFINED ) as $link )
            {
                if ( (int)$link->attribute( 'group_id' ) === (int)$group->attribute( 'id' ) )
                    $inGroup = true;
            }
            if ( !$inGroup )
            {
                $this->change( 'classes', 'extend', "class $identifier: add to class group " . expHelpContentSchema::CLASS_GROUP );
                if ( !$this->dryRun )
                    eZContentClassClassGroup::create( $class->attribute( 'id' ), eZContentClass::VERSION_STATUS_DEFINED,
                                                      $group->attribute( 'id' ), $group->attribute( 'name' ) )->store();
            }
        }
    }

    protected function createAttribute( eZContentClass $class, $identifier, array $definition, $placement, $initializeObjects )
    {
        $params = $definition['params'];
        $values = array(
            'version' => eZContentClass::VERSION_STATUS_DEFINED,
            'identifier' => $identifier,
            'name' => $definition['name'],
            'placement' => $placement,
            'is_required' => $definition['required'] ? 1 : 0,
            'is_searchable' => $definition['searchable'] ? 1 : 0,
            'can_translate' => $definition['translatable'] ? 1 : 0,
            'is_information_collector' => 0,
        );
        switch ( $definition['type'] )
        {
            case 'ezstring':
                $values['data_int1'] = isset( $params['max_length'] ) ? (int)$params['max_length'] : 0;
                break;
            case 'eztext':
                $values['data_int1'] = isset( $params['rows'] ) ? (int)$params['rows'] : 10;
                break;
            case 'ezselection':
                $values['data_int1'] = !empty( $params['multiple'] ) ? 1 : 0;
                $values['data_text5'] = self::selectionXml( $params['options'] );
                break;
            case 'ezobjectrelationlist':
                $values['data_text5'] = eZObjectRelationListType::createClassDOMDocument( array(
                    'class_constraint_list' => isset( $params['classes'] ) ? $params['classes'] : array(),
                    'selection_type' => 0, 'type' => 2, 'object_class' => '', 'default_placement' => false ) )->saveXML();
                break;
            case 'ezdate':
                $values['data_int1'] = 0;
                break;
        }
        $attribute = eZContentClassAttribute::create( (int)$class->attribute( 'id' ), $definition['type'], $values, $this->language );
        $dataType = $attribute->dataType();
        if ( $dataType && !in_array( $definition['type'], array( 'ezselection', 'ezobjectrelationlist' ), true ) )
            $dataType->initializeClassAttribute( $attribute );
        $attribute->store();
        if ( $initializeObjects )
            $attribute->initializeObjectAttributes();
        return $attribute;
    }

    /**
     * Appends the options the attribute does not have yet; existing options keep their id and position
     */
    protected function appendOptions( $classIdentifier, eZContentClassAttribute $attribute, array $wanted )
    {
        $content = $attribute->content();
        $options = isset( $content['options'] ) ? $content['options'] : array();
        $names = array();
        $maxId = -1;
        foreach ( $options as $option )
        {
            if ( (string)$option['name'] !== '' )
                $names[] = mb_strtolower( (string)$option['name'] );
            $maxId = max( $maxId, (int)$option['id'] );
        }
        $missing = array();
        foreach ( $wanted as $name )
        {
            if ( !in_array( mb_strtolower( $name ), $names, true ) )
                $missing[] = $name;
        }
        if ( !$missing )
            return;
        $this->change( 'classes', 'extend', "class $classIdentifier: append to {$attribute->attribute( 'identifier' )} the options " . implode( ', ', $missing ) );
        if ( $this->dryRun )
            return;
        $list = array();
        foreach ( $options as $option )
        {
            if ( (string)$option['name'] !== '' )
                $list[(int)$option['id']] = (string)$option['name'];
        }
        foreach ( $missing as $name )
            $list[++$maxId] = $name;
        $attribute->setAttribute( 'data_text5', self::selectionXml( $list, true ) );
        $attribute->store();
    }

    /**
     * The class XML of ezselection
     *
     * @param array $options names (ids are their positions) or, with $keyed, id => name
     * @param bool $keyed
     * @return string
     */
    public static function selectionXml( array $options, $keyed = false )
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );
        $root = $doc->createElement( 'ezselection' );
        $doc->appendChild( $root );
        $list = $doc->createElement( 'options' );
        $root->appendChild( $list );
        $i = 0;
        foreach ( $options as $id => $name )
        {
            $node = $doc->createElement( 'option' );
            $node->setAttribute( 'id', (string)( $keyed ? (int)$id : $i++ ) );
            $node->setAttribute( 'name', (string)$name );
            $list->appendChild( $node );
        }
        return $doc->saveXML();
    }

    // ---------------------------------------------------------------- sections, states, tree

    public function installTree()
    {
        $sections = $this->ensureSections();
        $this->ensureStates();
        $parent = $this->parentNode();
        if ( $parent === null )
        {
            $this->change( 'tree', 'error', 'the parent node of the help (exphelp.ini [ContentSettings] ParentNode) was not found' );
            return;
        }
        $nodes = array( '' => $parent );
        foreach ( expHelpContentSchema::tree() as $remoteId => $entry )
        {
            list( $parentRemoteId, $name ) = $entry;
            $object = eZContentObject::fetchByRemoteID( $remoteId );
            if ( $object instanceof eZContentObject )
            {
                $nodes[$remoteId] = $object->attribute( 'main_node' );
                $this->ensureObjectSection( $object, isset( $sections[expHelpContentSchema::SECTION_INTERNAL] ) ? $sections[expHelpContentSchema::SECTION_INTERNAL] : 0, 'tree' );
                continue;
            }
            $this->change( 'tree', 'create', "folder \"$name\" (remote_id $remoteId)" );
            if ( $this->dryRun )
            {
                $nodes[$remoteId] = null;
                continue;
            }
            $parentNode = isset( $nodes[$parentRemoteId] ) ? $nodes[$parentRemoteId] : null;
            if ( !$parentNode instanceof eZContentObjectTreeNode )
                continue;
            $object = eZContentFunctions::createAndPublishObject( array(
                'class_identifier' => 'folder',
                'parent_node_id' => (int)$parentNode->attribute( 'node_id' ),
                'creator_id' => $this->userId,
                'remote_id' => $remoteId,
                'language' => $this->language,
                'attributes' => array( 'name' => $name ),
            ) );
            if ( !$object instanceof eZContentObject )
            {
                $this->change( 'tree', 'error', "folder $remoteId could not be created (is there a class \"folder\"?)" );
                continue;
            }
            $object = eZContentObject::fetch( (int)$object->attribute( 'id' ) );
            $nodes[$remoteId] = $object->attribute( 'main_node' );
            // on the object only, never on the subtree: a subtree assignment would undo the validator's decisions
            if ( isset( $sections[expHelpContentSchema::SECTION_INTERNAL] ) )
                $this->setObjectSection( $object, $sections[expHelpContentSchema::SECTION_INTERNAL] );
        }
    }

    /**
     * The node the help root is created below: exphelp.ini [ContentSettings] ParentNode = media (default), content,
     * users, or the remote_id of an object or node. Never inside the site's content tree by default: help must not
     * show up in its menus, listings and feeds.
     *
     * @return eZContentObjectTreeNode|null
     */
    public function parentNode()
    {
        $setting = (string)expHelpContentSchema::setting( 'ParentNode', 'media' );
        $contentIni = eZINI::instance( 'content.ini' );
        $byName = array( 'media' => 'MediaRootNode', 'content' => 'RootNode', 'users' => 'UserRootNode' );
        if ( isset( $byName[$setting] ) )
            return eZContentObjectTreeNode::fetch( (int)$contentIni->variable( 'NodeSettings', $byName[$setting] ) ) ?: null;
        $object = eZContentObject::fetchByRemoteID( $setting );
        if ( $object instanceof eZContentObject && $object->attribute( 'main_node' ) instanceof eZContentObjectTreeNode )
            return $object->attribute( 'main_node' );
        $node = eZContentObjectTreeNode::fetchByRemoteID( $setting );
        return $node instanceof eZContentObjectTreeNode ? $node : null;
    }

    /**
     * @return array section identifier => section id (only those that exist, or would after a dry run: 0)
     */
    protected function ensureSections()
    {
        $ids = array();
        foreach ( expHelpContentSchema::sections() as $identifier => $name )
        {
            $section = eZSection::fetchByIdentifier( $identifier );
            if ( $section instanceof eZSection )
            {
                $ids[$identifier] = (int)$section->attribute( 'id' );
                continue;
            }
            $this->change( 'tree', 'create', "section $identifier (\"$name\")" );
            if ( $this->dryRun )
            {
                $ids[$identifier] = 0;
                continue;
            }
            $section = new eZSection( array( 'name' => $name, 'identifier' => $identifier, 'locale' => '',
                                             'navigation_part_identifier' => 'ezmedianavigationpart' ) );
            $section->store();
            $ids[$identifier] = (int)$section->attribute( 'id' );
        }
        return $ids;
    }

    protected function ensureStates()
    {
        $group = eZContentObjectStateGroup::fetchByIdentifier( expHelpContentSchema::STATE_GROUP );
        if ( !$group instanceof eZContentObjectStateGroup )
        {
            $this->change( 'tree', 'create', 'object state group ' . expHelpContentSchema::STATE_GROUP . ' (its first state goes to every object)' );
            if ( $this->dryRun )
            {
                foreach ( expHelpContentSchema::states() as $identifier => $state )
                    $this->change( 'tree', 'create', "state " . expHelpContentSchema::STATE_GROUP . "/$identifier" );
                return;
            }
            $group = new eZContentObjectStateGroup();
            $group->setAttribute( 'identifier', expHelpContentSchema::STATE_GROUP );
            $translation = $group->translationByLocale( $this->language );
            if ( !$translation )
            {
                $this->change( 'tree', 'error', "the language {$this->language} is not available for the state group" );
                return;
            }
            $group->setAttribute( 'default_language_id', (int)$translation->realLanguageID() );
            $translation->setAttribute( 'name', 'Help status' );
            $translation->setAttribute( 'description', 'Draft, Review, Released: only Released help is delivered' );
            $group->store();
            $group = eZContentObjectStateGroup::fetchByIdentifier( expHelpContentSchema::STATE_GROUP );
        }
        foreach ( expHelpContentSchema::states() as $identifier => $state )
        {
            if ( $group->stateByIdentifier( $identifier ) instanceof eZContentObjectState )
                continue;
            $this->change( 'tree', 'create', "state " . expHelpContentSchema::STATE_GROUP . "/$identifier" );
            if ( $this->dryRun )
                continue;
            $new = $group->newState( $identifier );
            $translation = $new->translationByLocale( $this->language );
            $new->setAttribute( 'default_language_id', (int)$translation->realLanguageID() );
            $translation->setAttribute( 'name', $state[0] );
            $translation->setAttribute( 'description', $state[1] );
            $new->store();
            $group = eZContentObjectStateGroup::fetchByIdentifier( expHelpContentSchema::STATE_GROUP );
        }
        if ( !$this->dryRun )
        {
            eZContentObjectState::cleanDefaultsCache();
            eZExpiryHandler::instance()->setTimestamp( 'state-limitations', time() );
            eZExpiryHandler::instance()->store();
        }
    }

    // ---------------------------------------------------------------- roles

    public function installRoles()
    {
        $resolved = $this->resolver();
        if ( $resolved === null )
            return;
        foreach ( expHelpContentSchema::roles() as $roleName => $policies )
        {
            $role = eZRole::fetchByName( $roleName );
            if ( !$role instanceof eZRole )
            {
                if ( $roleName === 'Anonymous' )
                {
                    $this->change( 'roles', 'error', 'there is no role "Anonymous"; the public help cannot be read' );
                    continue;
                }
                $this->change( 'roles', 'create', "role \"$roleName\"" );
                if ( !$this->dryRun )
                {
                    $role = eZRole::create( $roleName );
                    $role->store();
                }
            }
            $existing = $role instanceof eZRole ? self::policySignatures( $role ) : array();
            foreach ( $policies as $policy )
            {
                list( $module, $function, $limitations ) = $policy;
                $values = $resolved( $limitations );
                if ( $values === null )
                {
                    $this->change( 'roles', 'error', "$roleName: $module/$function: a limitation value does not exist (run the tree step first)" );
                    continue;
                }
                $signature = self::signature( $module, $function, $values );
                if ( in_array( $signature, $existing, true ) )
                    continue;
                $this->change( 'roles', 'add', "$roleName: policy $module/$function" . ( $limitations ? ' (' . self::describeLimitations( $limitations ) . ')' : '' ) );
                if ( !$this->dryRun && $role instanceof eZRole )
                    $role->appendPolicy( $module, $function, $values );
            }
        }
        if ( !$this->dryRun )
        {
            eZRole::expireCache();
            eZUser::cleanupCache();
        }
    }

    /**
     * A function that turns symbolic limitation values into ids, null when a value does not exist. In a dry run on
     * an installation without the tree the values are placeholders (-1), so the plan is still shown.
     *
     * @return callable|null
     */
    protected function resolver()
    {
        $dryRun = $this->dryRun;
        $group = eZContentObjectStateGroup::fetchByIdentifier( expHelpContentSchema::STATE_GROUP );
        return function ( array $limitations ) use ( $group, $dryRun ) {
            $values = array();
            foreach ( $limitations as $identifier => $symbols )
            {
                $ids = array();
                foreach ( $symbols as $symbol )
                {
                    $id = null;
                    if ( $identifier === 'Subtree' )
                    {
                        $object = eZContentObject::fetchByRemoteID( $symbol );
                        $node = $object instanceof eZContentObject ? $object->attribute( 'main_node' ) : null;
                        $id = $node instanceof eZContentObjectTreeNode ? (string)$node->attribute( 'path_string' ) : null;
                    }
                    elseif ( $identifier === 'Section' )
                    {
                        $section = eZSection::fetchByIdentifier( $symbol );
                        $id = $section instanceof eZSection ? (int)$section->attribute( 'id' ) : null;
                    }
                    elseif ( $identifier === 'NewState' || strpos( $identifier, 'StateGroup_' ) === 0 )
                    {
                        $state = $group instanceof eZContentObjectStateGroup ? $group->stateByIdentifier( $symbol ) : null;
                        $id = $state instanceof eZContentObjectState ? (int)$state->attribute( 'id' ) : null;
                    }
                    if ( $id === null )
                    {
                        if ( !$dryRun )
                            return null;
                        $id = -1;
                    }
                    $ids[] = $id;
                }
                $values[$identifier] = $ids;
            }
            return $values;
        };
    }

    /**
     * @param eZRole $role
     * @return string[] the signatures of its policies
     */
    public static function policySignatures( eZRole $role )
    {
        $signatures = array();
        foreach ( (array)$role->policyList() as $policy )
        {
            $values = array();
            foreach ( (array)$policy->limitationList( false ) as $limitation )
                $values[(string)$limitation->attribute( 'identifier' )] = (array)$limitation->attribute( 'values_as_array' );
            $signatures[] = self::signature( (string)$policy->attribute( 'module_name' ), (string)$policy->attribute( 'function_name' ), $values );
        }
        return $signatures;
    }

    /**
     * A policy as a comparable string: module/function and the sorted limitation values
     *
     * @param string $module
     * @param string $function
     * @param array $limitations identifier => values
     * @return string
     */
    public static function signature( $module, $function, array $limitations )
    {
        ksort( $limitations );
        $parts = array();
        foreach ( $limitations as $identifier => $values )
        {
            $values = array_map( 'strval', (array)$values );
            sort( $values );
            $parts[] = $identifier . '=' . implode( ',', $values );
        }
        return $module . '/' . $function . '|' . implode( ';', $parts );
    }

    protected static function describeLimitations( array $limitations )
    {
        $parts = array();
        foreach ( $limitations as $identifier => $values )
            $parts[] = $identifier . ': ' . implode( ', ', $values );
        return implode( '; ', $parts );
    }

    // ---------------------------------------------------------------- sections of objects

    /**
     * Sets the section of one object (never of its subtree)
     *
     * @param eZContentObject $object
     * @param int $sectionId
     * @return bool true when it changed
     */
    public function setObjectSection( eZContentObject $object, $sectionId )
    {
        $sectionId = (int)$sectionId;
        if ( $sectionId <= 0 || (int)$object->attribute( 'section_id' ) === $sectionId )
            return false;
        if ( $this->dryRun )
            return true;
        $db = eZDB::instance();
        $db->begin();
        $object->setAttribute( 'section_id', $sectionId );
        $object->store();
        $db->commit();
        if ( class_exists( 'eZSearch' ) && method_exists( 'eZSearch', 'updateObjectsSection' ) )
            eZSearch::updateObjectsSection( array( (int)$object->attribute( 'id' ) ), $sectionId );
        eZContentCacheManager::clearContentCacheIfNeeded( (int)$object->attribute( 'id' ) );
        return true;
    }

    protected function ensureObjectSection( eZContentObject $object, $sectionId, $step )
    {
        if ( $sectionId > 0 && (int)$object->attribute( 'section_id' ) !== (int)$sectionId )
        {
            $this->change( $step, 'section', 'object ' . $object->attribute( 'id' ) . ' (' . $object->attribute( 'remote_id' ) . ') to section ' . $sectionId );
            $this->setObjectSection( $object, $sectionId );
        }
    }

    /**
     * Applies the section fixes of the validator's findings (code section.mismatch, fix = section identifier)
     *
     * @param array $findings
     * @return int the number of objects changed (or, in a dry run, that would change)
     */
    public function fixSections( array $findings )
    {
        $count = 0;
        foreach ( $findings as $finding )
        {
            if ( $finding['code'] !== 'section.mismatch' || $finding['fix'] === '' || $finding['object_id'] <= 0 )
                continue;
            $section = eZSection::fetchByIdentifier( $finding['fix'] );
            $object = eZContentObject::fetch( (int)$finding['object_id'] );
            if ( !$section instanceof eZSection || !$object instanceof eZContentObject )
                continue;
            $this->change( 'check', 'section', 'object ' . $finding['object_id'] . ( $finding['help_id'] !== '' ? ' (' . $finding['help_id'] . ')' : '' ) . ' to section ' . $finding['fix'] );
            if ( $this->setObjectSection( $object, (int)$section->attribute( 'id' ) ) )
                $count++;
        }
        return $count;
    }

    // ---------------------------------------------------------------- report and uninstall

    /**
     * What is installed: classes, sections, states, tree, roles (with their assignments) and counts of the content
     *
     * @return array
     */
    public static function report()
    {
        $report = array( 'classes' => array(), 'sections' => array(), 'states' => array(), 'tree' => array(), 'roles' => array(), 'content' => array() );
        foreach ( expHelpContentSchema::classes() as $identifier => $definition )
        {
            $class = eZContentClass::fetchByIdentifier( $identifier, true, eZContentClass::VERSION_STATUS_DEFINED );
            $missing = array();
            if ( $class instanceof eZContentClass )
            {
                $have = array();
                foreach ( $class->fetchAttributes() as $attribute )
                    $have[] = $attribute->attribute( 'identifier' );
                $missing = array_values( array_diff( array_keys( $definition['attributes'] ), $have ) );
            }
            $report['classes'][$identifier] = array(
                'exists' => $class instanceof eZContentClass,
                'id' => $class instanceof eZContentClass ? (int)$class->attribute( 'id' ) : 0,
                'objects' => $class instanceof eZContentClass ? (int)$class->objectCount() : 0,
                'missing_attributes' => $missing,
            );
        }
        foreach ( expHelpContentSchema::sections() as $identifier => $name )
        {
            $section = eZSection::fetchByIdentifier( $identifier );
            $report['sections'][$identifier] = $section instanceof eZSection ? (int)$section->attribute( 'id' ) : 0;
        }
        $group = eZContentObjectStateGroup::fetchByIdentifier( expHelpContentSchema::STATE_GROUP );
        foreach ( expHelpContentSchema::states() as $identifier => $state )
        {
            $found = $group instanceof eZContentObjectStateGroup ? $group->stateByIdentifier( $identifier ) : null;
            $report['states'][$identifier] = $found instanceof eZContentObjectState ? (int)$found->attribute( 'id' ) : 0;
        }
        foreach ( expHelpContentSchema::tree() as $remoteId => $entry )
        {
            $object = eZContentObject::fetchByRemoteID( $remoteId );
            $report['tree'][$remoteId] = $object instanceof eZContentObject ? (int)$object->attribute( 'main_node_id' ) : 0;
        }
        foreach ( expHelpContentSchema::roles() as $roleName => $policies )
        {
            $role = eZRole::fetchByName( $roleName );
            $assigned = array();
            if ( $role instanceof eZRole )
            {
                foreach ( (array)$role->fetchUserByRole() as $row )
                {
                    if ( isset( $row['user_object'] ) && $row['user_object'] instanceof eZContentObject )
                        $assigned[] = (string)$row['user_object']->attribute( 'name' );
                }
            }
            $report['roles'][$roleName] = array( 'exists' => $role instanceof eZRole,
                                                 'policies' => $role instanceof eZRole ? count( (array)$role->policyList() ) : 0,
                                                 'assigned_to' => $assigned );
        }
        if ( expHelpContentRepository::rootNode() !== null )
        {
            $set = expHelpContentRepository::instance()->load();
            $states = array();
            foreach ( $set['topics'] as $topic )
                $states[$topic['state'] !== '' ? $topic['state'] : '-'] = ( isset( $states[$topic['state']] ) ? $states[$topic['state']] : 0 ) + 1;
            $report['content'] = array( 'topics' => count( $set['topics'] ), 'topics_by_state' => $states, 'figures' => count( $set['figures'] ),
                                        'manuals_and_chapters' => count( $set['maps'] ), 'glossary' => count( $set['glossary'] ) );
        }
        return $report;
    }

    /**
     * Takes away what install() added. Always: the help policies of Anonymous and the two roles. With $withContent
     * also the help tree (into the trash), the classes, the state group and the sections (when no object uses them).
     *
     * @param bool $withContent
     * @return array changes
     */
    public function uninstall( $withContent = false )
    {
        $resolved = $this->resolver();
        $anonymous = eZRole::fetchByName( 'Anonymous' );
        $roles = expHelpContentSchema::roles();
        if ( $anonymous instanceof eZRole )
        {
            // the opt-in context policy too, whether or not it is switched on now
            $wanted = array( self::signature( 'exphelp', 'context', array() ) );
            foreach ( $roles['Anonymous'] as $policy )
            {
                $values = $resolved( $policy[2] );
                if ( $values !== null )
                    $wanted[] = self::signature( $policy[0], $policy[1], $values );
            }
            foreach ( (array)$anonymous->policyList() as $policy )
            {
                $values = array();
                foreach ( (array)$policy->limitationList( false ) as $limitation )
                    $values[(string)$limitation->attribute( 'identifier' )] = (array)$limitation->attribute( 'values_as_array' );
                if ( in_array( self::signature( (string)$policy->attribute( 'module_name' ), (string)$policy->attribute( 'function_name' ), $values ), $wanted, true ) )
                {
                    $this->change( 'uninstall', 'remove', 'Anonymous: policy ' . $policy->attribute( 'module_name' ) . '/' . $policy->attribute( 'function_name' ) );
                    if ( !$this->dryRun )
                        $policy->removeThis();
                }
            }
        }
        foreach ( array( expHelpContentSchema::ROLE_EDITOR, expHelpContentSchema::ROLE_RELEASE ) as $roleName )
        {
            $role = eZRole::fetchByName( $roleName );
            if ( $role instanceof eZRole )
            {
                $this->change( 'uninstall', 'remove', "role \"$roleName\"" );
                if ( !$this->dryRun )
                    eZRole::removeRole( (int)$role->attribute( 'id' ) );
            }
        }
        if ( $withContent )
        {
            $root = eZContentObject::fetchByRemoteID( expHelpContentSchema::ROOT );
            if ( $root instanceof eZContentObject )
            {
                $this->change( 'uninstall', 'remove', 'the help tree (node ' . $root->attribute( 'main_node_id' ) . ') into the trash' );
                if ( !$this->dryRun )
                    eZContentObjectTreeNode::removeSubtrees( array( (int)$root->attribute( 'main_node_id' ) ), true );
            }
            foreach ( array_reverse( expHelpContentSchema::classIdentifiers() ) as $identifier )
            {
                $class = eZContentClass::fetchByIdentifier( $identifier, true, eZContentClass::VERSION_STATUS_DEFINED );
                if ( !$class instanceof eZContentClass )
                    continue;
                if ( (int)$class->objectCount() > 0 && !$this->dryRun )
                {
                    $this->change( 'uninstall', 'keep', "class $identifier: it still has objects (empty the trash first)" );
                    continue;
                }
                $this->change( 'uninstall', 'remove', "class $identifier" );
                if ( !$this->dryRun )
                    eZContentClassOperations::remove( (int)$class->attribute( 'id' ) );
            }
            $group = eZContentObjectStateGroup::fetchByIdentifier( expHelpContentSchema::STATE_GROUP );
            if ( $group instanceof eZContentObjectStateGroup )
            {
                $this->change( 'uninstall', 'remove', 'object state group ' . expHelpContentSchema::STATE_GROUP );
                if ( !$this->dryRun )
                {
                    eZContentObjectStateGroup::removeByID( (int)$group->attribute( 'id' ) );
                    eZContentObjectState::cleanDefaultsCache();
                    eZExpiryHandler::instance()->setTimestamp( 'state-limitations', time() );
                    eZExpiryHandler::instance()->store();
                }
            }
            foreach ( array_keys( expHelpContentSchema::sections() ) as $identifier )
            {
                $section = eZSection::fetchByIdentifier( $identifier );
                if ( !$section instanceof eZSection )
                    continue;
                if ( !$this->dryRun && !$section->canBeRemoved() )
                {
                    $this->change( 'uninstall', 'keep', "section $identifier: objects still use it" );
                    continue;
                }
                $this->change( 'uninstall', 'remove', "section $identifier" );
                if ( !$this->dryRun )
                    $section->removeThis();
            }
        }
        if ( !$this->dryRun )
        {
            eZRole::expireCache();
            eZUser::cleanupCache();
            eZContentClass::expireCache();
        }
        return $this->changes;
    }

    protected function change( $step, $action, $what )
    {
        $this->changes[] = array( 'step' => $step, 'action' => $action, 'what' => $what );
    }
}

?>
