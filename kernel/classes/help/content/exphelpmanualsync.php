<?php
/**
 * Makes the database match a plan of expHelpManualBuilder: creates the manuals, chapters and topics that are missing,
 * updates those whose values differ, adds, updates and removes their translations, sets their section (and, with
 * release, the state Released), and removes the generated objects whose page is gone. Only objects whose remote_id
 * starts with help-gen- are ever changed or removed. A run that finds nothing to do changes nothing ("No change.").
 *
 * Fast enough for every page of the help (doc/eng-us/features/6.0/help-content.md, "Manuals from the Markdown help"):
 * - what is stored is read with three queries, not object by object, and compared with the plan before anything is
 *   written, so an unchanged object costs no write at all;
 * - writes are grouped in transactions of [ManualSettings] BatchSize objects;
 * - published without notification (no mail to subscribers of the help tree), the search index is updated later by
 *   the indexcontent cronjob (site.ini [SearchSettings] DelayedIndexing, set for this run only: the help searches its
 *   topics itself), and the view cache is not cleared per object (help topics are shown by exphelp, not content/view;
 *   the help's own cache is cleared once at the end).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpManualSync
{
    /** @var bool */
    protected $dryRun;
    /** @var bool */
    protected $release;
    /** @var int */
    protected $userId;
    /** @var int */
    protected $batchSize;
    /** @var string the default language of the installation (the manuals' main language) */
    protected $language;
    /** @var string|null only this manual (key) */
    protected $manual;
    /** @var array list of array( step, action, what ) */
    protected $changes = array();
    /** @var array action => count */
    protected $counts = array();
    /** @var array class identifier => array( 'id', 'attributes' => identifier => array( 'type', 'options' => name => id ) ) */
    protected $classes = array();
    /** @var array remote_id => stored row (loadStored()) */
    protected $stored = array();
    /** @var array remote_id => object id */
    protected $objectIds = array();
    /** @var array remote_id => node id */
    protected $nodeIds = array();
    /** @var int writes in the open transaction */
    protected $pending = 0;
    /** @var string[] the translations of this run */
    protected $translationLanguages = array();
    /** @var callable|null function( done, total ): called every 250 objects of the first pass */
    protected $progress = null;

    /**
     * @param array $options dry_run, release (bool), user_id, batch_size, language (default language), manual (key)
     */
    public function __construct( array $options = array() )
    {
        $this->dryRun = !empty( $options['dry_run'] );
        $this->release = !empty( $options['release'] );
        $this->userId = !empty( $options['user_id'] ) ? (int)$options['user_id'] : (int)eZUser::currentUserID();
        $this->batchSize = max( 1, isset( $options['batch_size'] ) ? (int)$options['batch_size'] : (int)expHelpManualBuilder::setting( 'BatchSize', 50 ) );
        $this->language = !empty( $options['language'] ) ? (string)$options['language'] : (string)eZContentObject::defaultLanguage();
        $this->manual = isset( $options['manual'] ) && $options['manual'] !== '' ? (string)$options['manual'] : null;
        $this->progress = isset( $options['progress'] ) && is_callable( $options['progress'] ) ? $options['progress'] : null;
    }

    /** @return array list of array( step, action, what ) */
    public function changes()
    {
        return $this->changes;
    }

    /** @return array action => count */
    public function counts()
    {
        return $this->counts;
    }

    /**
     * The content languages of the installation among the wanted ones; the others are reported, never created
     *
     * @param string[] $wanted locales
     * @return array array( 'languages' => locales that exist, 'missing' => locales that do not )
     */
    public static function existingLanguages( array $wanted )
    {
        $have = array();
        foreach ( eZContentLanguage::fetchList() as $language )
            $have[] = (string)$language->attribute( 'locale' );
        $languages = $missing = array();
        foreach ( $wanted as $locale )
        {
            if ( in_array( $locale, $have, true ) )
                $languages[] = $locale;
            else
                $missing[] = $locale;
        }
        return array( 'languages' => $languages, 'missing' => $missing );
    }

    /** The passes of a synchronisation, in order */
    const PHASES = array( 'objects', 'refs', 'translations', 'sections', 'remove', 'done' );

    /**
     * Makes the database match the plan, in one go (the command; the background job uses step())
     *
     * @param array $plan expHelpManualBuilder::plan()
     * @param string[] $languages the translations to write (locales that exist)
     */
    public function run( array $plan, array $languages = array() )
    {
        $cursor = array();
        try
        {
            while ( !$this->step( $plan, $languages, $cursor, PHP_INT_MAX ) )
            {
            }
        }
        finally
        {
            $this->close();
        }
    }

    /**
     * Does at most $limit units of the synchronisation from where $cursor says (array( phase, offset ), empty at
     * the start), and moves the cursor on. A unit is an object of a pass; the removal is one unit. The background
     * job (expContentJobHelpManuals) does one call per batch of the content job, each in a transaction of its own;
     * what is stored is read once per instance and kept up to date as it writes, so the same instance goes on
     * where it stopped, and a new one (after a crash) finds what was done and does not do it again.
     *
     * @param array $plan
     * @param string[] $languages
     * @param array $cursor by reference: array( 'phase' => one of PHASES, 'offset' => int )
     * @param int $limit
     * @return bool whether it is finished
     */
    public function step( array $plan, array $languages, array &$cursor, $limit )
    {
        $cursor += array( 'phase' => 'objects', 'offset' => 0 );
        if ( $cursor['phase'] === 'done' )
            return true;
        if ( !$this->open( $plan, $languages ) )
        {
            $cursor = array( 'phase' => 'done', 'offset' => 0 );
            return true;
        }
        $items = $this->items;
        $keys = array_keys( $items );
        $units = 0;
        // a unit is an object that changed: objects found as they should be cost no write, so a call goes past
        // them (up to 50 times the limit), and a pass that has nothing to do is over in a few calls
        $looked = 0;
        $lookLimit = $limit >= PHP_INT_MAX / 50 ? PHP_INT_MAX : $limit * 50;
        try
        {
            while ( $units < $limit && $looked < $lookLimit && $cursor['phase'] !== 'done' )
            {
                $phase = $cursor['phase'];
                if ( $phase === 'remove' )
                {
                    // 5. the generated objects that are no longer in the plan
                    $this->removeGone( $plan, $this->manualsNode );
                    $units++;
                    $cursor = array( 'phase' => 'done', 'offset' => 0 );
                    break;
                }
                if ( $cursor['offset'] >= count( $keys ) )
                {
                    $this->flush();
                    $cursor = array( 'phase' => self::PHASES[array_search( $phase, self::PHASES, true ) + 1], 'offset' => 0 );
                    continue;
                }
                $item = $items[$keys[$cursor['offset']]];
                $cursor['offset']++;
                $looked++;
                $before = count( $this->changes );
                switch ( $phase )
                {
                    case 'objects':
                        // 1. the objects and their values in the default language (with their translations when new), parents first
                        $this->ensureObject( $item, (int)$this->manualsNode->attribute( 'node_id' ), $this->sections );
                        if ( $this->progress !== null && $cursor['offset'] % 250 === 0 )
                            call_user_func( $this->progress, $cursor['offset'], count( $keys ), count( $this->changes ) );
                        break;
                    case 'refs':
                        // 2. the chapters and topics of each manual and chapter (now that every object has an id)
                        if ( $item['kind'] !== 'topic' )
                            $this->ensureRefs( $item );
                        break;
                    case 'translations':
                        // 3. the translations of objects that existed already
                        foreach ( $this->translationLanguages as $locale )
                            $this->ensureTranslation( $item, $locale );
                        break;
                    case 'sections':
                        // 4. the sections and states
                        $this->ensureSectionAndState( $item, $this->sections, $this->releasedState );
                        break;
                }
                if ( count( $this->changes ) > $before )
                    $units++;
            }
        }
        finally
        {
            $this->flush();
        }
        return $cursor['phase'] === 'done';
    }

    /**
     * How many manuals of the plan the first pass has gone through at a cursor (for "n of 52")
     *
     * @param array $plan
     * @param array $cursor
     * @return int
     */
    public static function manualsDone( array $plan, array $cursor )
    {
        $cursor += array( 'phase' => 'objects', 'offset' => 0 );
        if ( $cursor['phase'] !== 'objects' )
            return count( $plan['manuals'] );
        $last = array();
        $index = 0;
        foreach ( $plan['items'] as $item )
            $last[$item['manual']] = $index++;
        $done = 0;
        foreach ( $last as $position )
            if ( $position < (int)$cursor['offset'] )
                $done++;
        return $done;
    }

    /** @var array|null the items of this run (the plan's, or one manual's) once open() */
    protected $items = null;
    /** @var eZContentObjectTreeNode|null */
    protected $manualsNode = null;
    /** @var array section identifier => id */
    protected $sections = array();
    /** @var eZContentObjectState|null */
    protected $releasedState = null;
    /** @var array|null the settings changed for the run, to put back */
    protected $savedSettings = null;

    /**
     * Reads what is stored and sets this run's settings, once per instance
     *
     * @return bool false when the run cannot start (the change says why)
     */
    protected function open( array $plan, array $languages )
    {
        if ( $this->items !== null )
            return true;
        $default = $this->language;
        $this->translationLanguages = array_values( array_filter( $languages, function ( $locale ) use ( $default ) { return $locale !== $default; } ) );
        $this->loadClasses();
        $manualsFolder = eZContentObject::fetchByRemoteID( expHelpContentSchema::MANUALS );
        $manualsNode = $manualsFolder instanceof eZContentObject ? $manualsFolder->attribute( 'main_node' ) : null;
        if ( !$manualsNode instanceof eZContentObjectTreeNode )
        {
            $this->change( 'error', 'the folder Manuals (remote_id ' . expHelpContentSchema::MANUALS . ') does not exist (run exp:help:content install --apply)' );
            return false;
        }
        if ( $this->manual !== null && !isset( $plan['manuals'][$this->manual] ) )
        {
            $this->change( 'error', "no manual with the key {$this->manual} in [ManualSettings] Manuals[]" );
            return false;
        }
        $this->manualsNode = $manualsNode;
        $this->loadStored();
        $this->items = array();
        foreach ( $plan['items'] as $remoteId => $item )
        {
            if ( $this->manual === null || $item['manual'] === $this->manual )
                $this->items[$remoteId] = $item;
        }
        foreach ( expHelpContentSchema::sections() as $identifier => $name )
        {
            $section = eZSection::fetchByIdentifier( $identifier );
            $this->sections[$identifier] = $section instanceof eZSection ? (int)$section->attribute( 'id' ) : 0;
        }
        $this->releasedState = expHelpContentEbooks::releasedState();
        $ini = eZINI::instance();
        $this->savedSettings = array( 'DelayedIndexing' => $ini->variable( 'SearchSettings', 'DelayedIndexing' ),
                                      'ViewCaching' => $ini->variable( 'ContentSettings', 'ViewCaching' ) );
        $ini->setVariable( 'SearchSettings', 'DelayedIndexing', 'enabled' );
        $ini->setVariable( 'ContentSettings', 'ViewCaching', 'disabled' );
        return true;
    }

    /**
     * Commits what is open and puts the settings of the run back
     */
    public function close()
    {
        $this->flush();
        if ( $this->savedSettings !== null )
        {
            $ini = eZINI::instance();
            $ini->setVariable( 'SearchSettings', 'DelayedIndexing', $this->savedSettings['DelayedIndexing'] );
            $ini->setVariable( 'ContentSettings', 'ViewCaching', $this->savedSettings['ViewCaching'] );
            $this->savedSettings = null;
        }
    }

    // ---------------------------------------------------------------- what is stored

    protected function loadClasses()
    {
        foreach ( array_merge( expHelpContentSchema::MAP_CLASSES, expHelpContentSchema::topicClasses() ) as $identifier )
        {
            $class = eZContentClass::fetchByIdentifier( $identifier );
            if ( !$class instanceof eZContentClass )
                continue;
            $attributes = array();
            foreach ( $class->fetchAttributes() as $attribute )
            {
                $row = array( 'id' => (int)$attribute->attribute( 'id' ), 'type' => (string)$attribute->attribute( 'data_type_string' ), 'options' => array() );
                if ( $row['type'] === 'ezselection' )
                {
                    $content = $attribute->content();
                    foreach ( isset( $content['options'] ) ? $content['options'] : array() as $option )
                        $row['options'][(string)$option['name']] = (string)$option['id'];
                }
                $attributes[(string)$attribute->attribute( 'identifier' )] = $row;
            }
            $this->classes[$identifier] = array( 'id' => (int)$class->attribute( 'id' ), 'attributes' => $attributes );
        }
    }

    /**
     * Every generated object as it is stored: class, section, node, parent, state Released, and the values of its
     * current version in each language (three queries)
     */
    protected function loadStored()
    {
        $db = eZDB::instance();
        $classById = array();
        $attributeById = array();
        foreach ( $this->classes as $identifier => $class )
        {
            $classById[$class['id']] = $identifier;
            foreach ( $class['attributes'] as $attributeIdentifier => $attribute )
                $attributeById[$attribute['id']] = $attributeIdentifier;
        }
        $like = "o.remote_id LIKE '" . $db->escapeString( expHelpManualBuilder::PREFIX ) . "%'";
        $rows = $db->arrayQuery( "SELECT o.id, o.remote_id, o.contentclass_id, o.section_id, t.node_id, t.parent_node_id, t.path_string
                                  FROM ezcontentobject o LEFT JOIN ezcontentobject_tree t ON t.contentobject_id = o.id AND t.node_id = t.main_node_id
                                  WHERE $like" );
        $byId = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $remoteId = (string)$row['remote_id'];
            $this->stored[$remoteId] = array(
                'object_id' => (int)$row['id'],
                'class' => isset( $classById[(int)$row['contentclass_id']] ) ? $classById[(int)$row['contentclass_id']] : '',
                'section_id' => (int)$row['section_id'],
                'node_id' => (int)$row['node_id'],
                'parent_node_id' => (int)$row['parent_node_id'],
                'path_string' => (string)$row['path_string'],
                'released' => false,
                'values' => array(),
            );
            $this->objectIds[$remoteId] = (int)$row['id'];
            if ( (int)$row['node_id'] > 0 )
                $this->nodeIds[$remoteId] = (int)$row['node_id'];
            $byId[(int)$row['id']] = $remoteId;
        }
        if ( !$byId )
            return;
        $values = $db->arrayQuery( "SELECT a.contentobject_id, a.contentclassattribute_id, a.language_code, a.data_text
                                    FROM ezcontentobject o JOIN ezcontentobject_attribute a ON a.contentobject_id = o.id AND a.version = o.current_version
                                    WHERE $like" );
        foreach ( is_array( $values ) ? $values : array() as $row )
        {
            $objectId = (int)$row['contentobject_id'];
            $attributeId = (int)$row['contentclassattribute_id'];
            if ( !isset( $byId[$objectId] ) || !isset( $attributeById[$attributeId] ) )
                continue;
            $this->stored[$byId[$objectId]]['values'][(string)$row['language_code']][$attributeById[$attributeId]] = (string)$row['data_text'];
        }
        $released = expHelpContentEbooks::releasedState();
        if ( $released instanceof eZContentObjectState )
        {
            $states = $db->arrayQuery( "SELECT l.contentobject_id FROM ezcobj_state_link l JOIN ezcontentobject o ON o.id = l.contentobject_id
                                        WHERE l.contentobject_state_id = " . (int)$released->attribute( 'id' ) . " AND $like" );
            foreach ( is_array( $states ) ? $states : array() as $row )
            {
                if ( isset( $byId[(int)$row['contentobject_id']] ) )
                    $this->stored[$byId[(int)$row['contentobject_id']]]['released'] = true;
            }
        }
    }

    /**
     * The attributes of a plan whose stored value differs (or that are not stored in that language)
     *
     * @param string $class
     * @param array|null $stored identifier => data_text of one language, null: not stored
     * @param array $attributes identifier => value as the plan has it
     * @return array identifier => value
     */
    public function differing( $class, $stored, array $attributes )
    {
        $differ = array();
        foreach ( $attributes as $identifier => $value )
        {
            if ( !isset( $this->classes[$class]['attributes'][$identifier] ) )
                continue;
            if ( $stored === null || !array_key_exists( $identifier, $stored ) )
            {
                $differ[$identifier] = $value;
                continue;
            }
            $attribute = $this->classes[$class]['attributes'][$identifier];
            if ( !self::sameValue( $attribute, $stored[$identifier], $value ) )
                $differ[$identifier] = $value;
        }
        return $differ;
    }

    /**
     * Whether a stored data_text holds a value of the plan
     *
     * @param array $attribute array( 'type', 'options' => name => id )
     * @param string $stored data_text
     * @param string|int[] $value text, option names joined by |, or (relation list) object ids
     * @return bool
     */
    public static function sameValue( array $attribute, $stored, $value )
    {
        switch ( $attribute['type'] )
        {
            case 'ezselection':
                $want = array();
                foreach ( $value === '' ? array() : explode( '|', (string)$value ) as $name )
                {
                    if ( isset( $attribute['options'][$name] ) )
                        $want[] = (string)$attribute['options'][$name];
                }
                $have = (string)$stored === '' ? array() : explode( '-', (string)$stored );
                sort( $want );
                sort( $have );
                return $want === $have;
            case 'ezobjectrelationlist':
                return self::relationIds( $stored ) === array_map( 'intval', (array)$value );
        }
        return (string)$stored === (string)$value;
    }

    /**
     * The object ids of a stored relation list, in order
     *
     * @param string $xml
     * @return int[]
     */
    public static function relationIds( $xml )
    {
        $ids = array();
        if ( preg_match_all( '/<relation-item\b[^>]*\bcontentobject-id="(\d+)"[^>]*\bpriority="(\d+)"|<relation-item\b[^>]*\bpriority="(\d+)"[^>]*\bcontentobject-id="(\d+)"/', (string)$xml, $m, PREG_SET_ORDER ) )
        {
            $rows = array();
            foreach ( $m as $n => $match )
            {
                $id = $match[1] !== '' ? (int)$match[1] : (int)$match[4];
                $priority = $match[1] !== '' ? (int)$match[2] : (int)$match[3];
                $rows[] = array( $priority, $n, $id );
            }
            sort( $rows );
            foreach ( $rows as $row )
                $ids[] = $row[2];
        }
        elseif ( preg_match_all( '/contentobject-id="(\d+)"/', (string)$xml, $m ) )
            $ids = array_map( 'intval', $m[1] );
        return $ids;
    }

    // ---------------------------------------------------------------- writing

    protected function ensureObject( array $item, $manualsNodeId, array $sections )
    {
        $remoteId = $item['remote_id'];
        $label = $item['kind'] . ' ' . ( $item['page'] !== '' ? $item['page'] : $item['attributes']['title'] ) . " ($remoteId)";
        $parentNodeId = $item['parent'] === '' ? $manualsNodeId : ( isset( $this->nodeIds[$item['parent']] ) ? $this->nodeIds[$item['parent']] : 0 );
        $stored = isset( $this->stored[$remoteId] ) ? $this->stored[$remoteId] : null;
        if ( $stored !== null && $stored['class'] !== $item['class'] )
        {
            // another topic type: the class of an object cannot change, so it is made again
            $this->change( 'retype', "$label from {$stored['class']} to {$item['class']}" );
            if ( !$this->dryRun )
                $this->removeObject( $stored );
            unset( $this->stored[$remoteId], $this->objectIds[$remoteId], $this->nodeIds[$remoteId] );
            $stored = null;
        }
        if ( !isset( $this->classes[$item['class']] ) )
        {
            $this->change( 'error', "$label: the class {$item['class']} does not exist (run exp:help:content install --apply)" );
            return;
        }
        if ( $stored === null )
        {
            $this->change( 'create', $label );
            if ( $this->dryRun )
            {
                $this->nodeIds[$remoteId] = -1;
                return;
            }
            if ( $parentNodeId <= 0 )
            {
                $this->change( 'error', "$label: its parent was not made" );
                return;
            }
            $this->begin();
            $translations = array_intersect_key( $item['translations'], array_flip( $this->translationLanguages ) );
            $object = $this->createObject( $item['class'], $parentNodeId, $remoteId, isset( $sections[$item['section']] ) ? $sections[$item['section']] : 0,
                                           $item['attributes'], $translations );
            if ( !$object instanceof eZContentObject || !$object->attribute( 'main_node_id' ) )
            {
                $this->change( 'error', "$label could not be created" );
                return;
            }
            foreach ( array_keys( $translations ) as $locale )
                $this->change( 'translate', $item['kind'] . ' ' . ( $item['page'] !== '' ? $item['page'] : $item['attributes']['title'] ) . " ($remoteId, $locale)" );
            $values = array( $this->language => $this->storedValues( $item['class'], $item['attributes'] ) );
            foreach ( $translations as $locale => $attributes )
                $values[$locale] = $this->storedValues( $item['class'], $attributes );
            $this->objectIds[$remoteId] = (int)$object->attribute( 'id' );
            $this->nodeIds[$remoteId] = (int)$object->attribute( 'main_node_id' );
            $this->stored[$remoteId] = array( 'object_id' => (int)$object->attribute( 'id' ), 'class' => $item['class'],
                                              'section_id' => (int)$object->attribute( 'section_id' ), 'node_id' => $this->nodeIds[$remoteId],
                                              'parent_node_id' => $parentNodeId, 'path_string' => '', 'released' => false,
                                              'values' => $values );
            eZContentObject::clearCache( array( (int)$object->attribute( 'id' ) ) );
            return;
        }
        $values = isset( $stored['values'][$this->language] ) ? $stored['values'][$this->language] : null;
        $differ = $this->differing( $item['class'], $values, $item['attributes'] );
        if ( $differ )
        {
            $this->change( 'update', $label . ': ' . implode( ', ', array_keys( $differ ) ) );
            if ( !$this->dryRun )
                $this->update( $stored['object_id'], $differ, $this->language, $label );
            $this->stored[$remoteId]['values'][$this->language] = array_merge( (array)$values, $this->storedValues( $item['class'], $differ ) );
        }
        if ( $parentNodeId > 0 && $stored['parent_node_id'] !== $parentNodeId && $stored['node_id'] > 0 )
        {
            $this->change( 'move', $label );
            if ( !$this->dryRun )
            {
                $this->begin();
                eZContentObjectTreeNodeOperations::move( $stored['node_id'], $parentNodeId );
            }
            $this->stored[$remoteId]['parent_node_id'] = $parentNodeId;
        }
    }

    /**
     * A new object with its translations in ONE published version (eZContentFunctions::createAndPublishObject() plus
     * the translations): one publish instead of one per language, the most expensive part of a first build. Each
     * translation starts as a copy of the default language's attributes (so the attributes that are not translated,
     * help_id and audience, are the same), then gets its own values.
     *
     * @return eZContentObject|null
     */
    protected function createObject( $classIdentifier, $parentNodeId, $remoteId, $sectionId, array $attributes, array $translations )
    {
        $class = eZContentClass::fetchByIdentifier( $classIdentifier );
        if ( !$class instanceof eZContentClass )
            return null;
        $db = eZDB::instance();
        $db->begin();
        $object = $class->instantiate( $this->userId, $sectionId, false, $this->language );
        $object->setAttribute( 'remote_id', $remoteId );
        $object->store();
        eZNodeAssignment::create( array( 'contentobject_id' => $object->attribute( 'id' ), 'contentobject_version' => 1, 'parent_node' => $parentNodeId,
                                         'is_main' => 1, 'sort_field' => $class->attribute( 'sort_field' ), 'sort_order' => $class->attribute( 'sort_order' ) ) )->store();
        $version = $object->version( 1 );
        $version->setAttribute( 'modified', time() );
        $version->setAttribute( 'status', eZContentObjectVersion::STATUS_DRAFT );
        $version->store();
        $defaultAttributes = $object->contentObjectAttributes( true, 1, $this->language );
        foreach ( $defaultAttributes as $attribute )
        {
            $identifier = $attribute->attribute( 'contentclass_attribute_identifier' );
            if ( isset( $attributes[$identifier] ) && !is_array( $attributes[$identifier] ) )
            {
                $attribute->fromString( (string)$attributes[$identifier] );
                $attribute->store();
            }
        }
        foreach ( $translations as $locale => $values )
        {
            foreach ( $defaultAttributes as $attribute )
            {
                $copy = $attribute->cloneContentObjectAttribute( 1, 1, false, $locale );
                $identifier = $attribute->attribute( 'contentclass_attribute_identifier' );
                if ( isset( $values[$identifier] ) && $attribute->contentClassAttributeCanTranslate() )
                {
                    $copy->fromString( (string)$values[$identifier] );
                    $copy->store();
                }
            }
        }
        $db->commit();
        eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $object->attribute( 'id' ), 'version' => 1,
                                                                  'notify' => eZContentOperationCollection::notifyRequested( false ) ) );
        eZContentObject::clearCache( array( (int)$object->attribute( 'id' ) ) );
        $published = eZContentObject::fetch( (int)$object->attribute( 'id' ) );
        return $published instanceof eZContentObject ? $published : null;
    }

    /** The values of the plan as they will be stored (option names as their ids), for a later comparison */
    protected function storedValues( $class, array $attributes )
    {
        $values = array();
        foreach ( $attributes as $identifier => $value )
        {
            if ( !isset( $this->classes[$class]['attributes'][$identifier] ) )
                continue;
            $attribute = $this->classes[$class]['attributes'][$identifier];
            if ( $attribute['type'] === 'ezselection' )
            {
                $ids = array();
                foreach ( $value === '' ? array() : explode( '|', (string)$value ) as $name )
                    if ( isset( $attribute['options'][$name] ) )
                        $ids[] = $attribute['options'][$name];
                $values[$identifier] = implode( '-', $ids );
            }
            elseif ( $attribute['type'] === 'ezobjectrelationlist' )
                $values[$identifier] = implode( '', array_map( function ( $id ) { return '<relation-item contentobject-id="' . (int)$id . '"/>'; }, (array)$value ) );
            else
                $values[$identifier] = (string)$value;
        }
        return $values;
    }

    protected function update( $objectId, array $attributes, $language, $label )
    {
        $object = eZContentObject::fetch( (int)$objectId );
        if ( !$object instanceof eZContentObject )
        {
            $this->change( 'error', "$label: the object is gone" );
            return;
        }
        foreach ( $attributes as $identifier => $value )
        {
            if ( is_array( $value ) )
                $attributes[$identifier] = implode( '-', $value );
        }
        $this->begin();
        if ( !eZContentFunctions::updateAndPublishObject( $object, array( 'attributes' => $attributes, 'language' => $language ), false ) )
            $this->change( 'error', "$label could not be updated" );
        eZContentObject::clearCache( array( (int)$objectId ) );
    }

    protected function ensureRefs( array $item )
    {
        $remoteId = $item['remote_id'];
        $ids = array();
        foreach ( $item['refs'] as $ref )
        {
            if ( isset( $this->objectIds[$ref] ) )
                $ids[] = $this->objectIds[$ref];
            elseif ( !$this->dryRun )
                $this->change( 'error', "$remoteId: refers to $ref, which was not made" );
        }
        if ( !isset( $this->stored[$remoteId] ) )
        {
            if ( $this->dryRun && $item['refs'] )
                $this->change( 'relate', "{$item['kind']} {$item['attributes']['title']} ($remoteId): " . count( $item['refs'] ) . ' entries' );
            return;
        }
        $stored = $this->stored[$remoteId];
        $current = isset( $stored['values'][$this->language]['topicrefs'] ) ? self::relationIds( $stored['values'][$this->language]['topicrefs'] ) : array();
        if ( $current === $ids )
            return;
        $this->change( 'relate', "{$item['kind']} {$item['attributes']['title']} ($remoteId): " . count( $ids ) . ' entries' );
        if ( !$this->dryRun )
            $this->update( $stored['object_id'], array( 'topicrefs' => $ids ), $this->language, $remoteId );
        $this->stored[$remoteId]['values'][$this->language]['topicrefs'] = $this->storedValues( $item['class'], array( 'topicrefs' => $ids ) )['topicrefs'];
    }

    protected function ensureTranslation( array $item, $locale )
    {
        $remoteId = $item['remote_id'];
        $label = $item['kind'] . ' ' . ( $item['page'] !== '' ? $item['page'] : $item['attributes']['title'] ) . " ($remoteId, $locale)";
        $stored = isset( $this->stored[$remoteId] ) ? $this->stored[$remoteId] : null;
        $values = $stored !== null && isset( $stored['values'][$locale] ) ? $stored['values'][$locale] : null;
        if ( !isset( $item['translations'][$locale] ) )
        {
            if ( $values === null )
                return;
            $this->change( 'untranslate', $label );
            if ( !$this->dryRun )
            {
                $object = eZContentObject::fetch( $stored['object_id'] );
                $language = eZContentLanguage::fetchByLocale( $locale );
                if ( $object instanceof eZContentObject && $language instanceof eZContentLanguage )
                {
                    $this->begin();
                    $object->removeTranslation( (int)$language->attribute( 'id' ) );
                    eZContentObject::clearCache( array( (int)$stored['object_id'] ) );
                }
            }
            return;
        }
        $translation = $item['translations'][$locale];
        if ( $stored === null )
        {
            if ( $this->dryRun )
                $this->change( 'translate', $label );
            return;
        }
        $differ = $this->differing( $item['class'], $values, $translation );
        if ( !$differ )
            return;
        // a new translation gets every field, so nothing of it is empty
        if ( $values === null )
            $differ = $translation;
        $this->change( $values === null ? 'translate' : 'update', $label . ( $values === null ? '' : ': ' . implode( ', ', array_keys( $differ ) ) ) );
        if ( !$this->dryRun )
            $this->update( $stored['object_id'], $differ, $locale, $label );
        $this->stored[$remoteId]['values'][$locale] = array_merge( (array)$values, $this->storedValues( $item['class'], $differ ) );
    }

    protected function ensureSectionAndState( array $item, array $sections, $released )
    {
        $remoteId = $item['remote_id'];
        if ( !isset( $this->stored[$remoteId] ) )
            return;
        $stored = $this->stored[$remoteId];
        $sectionId = isset( $sections[$item['section']] ) ? $sections[$item['section']] : 0;
        if ( $sectionId > 0 && $stored['section_id'] !== $sectionId )
        {
            $this->change( 'section', "$remoteId to {$item['section']}" );
            if ( !$this->dryRun )
            {
                $object = eZContentObject::fetch( $stored['object_id'] );
                if ( $object instanceof eZContentObject )
                {
                    $this->begin();
                    $object->setAttribute( 'section_id', $sectionId );
                    $object->store();
                    if ( method_exists( 'eZSearch', 'updateObjectsSection' ) )
                        eZSearch::updateObjectsSection( array( $stored['object_id'] ), $sectionId );
                    eZContentObject::clearCache( array( $stored['object_id'] ) );
                }
            }
        }
        if ( $this->release && $released instanceof eZContentObjectState && !$stored['released'] )
        {
            $this->change( 'release', $remoteId );
            if ( !$this->dryRun )
            {
                $object = eZContentObject::fetch( $stored['object_id'] );
                if ( $object instanceof eZContentObject )
                {
                    $this->begin();
                    $object->assignState( $released );
                    eZContentObject::clearCache( array( $stored['object_id'] ) );
                }
            }
        }
    }

    /**
     * Removes the generated objects that are no longer in the plan (with a manual given: only those below it)
     */
    protected function removeGone( array $plan, eZContentObjectTreeNode $manualsNode )
    {
        $below = null;
        if ( $this->manual !== null )
        {
            $manualRemoteId = expHelpManualBuilder::manualRemoteId( $this->manual );
            if ( !isset( $this->stored[$manualRemoteId] ) || $this->stored[$manualRemoteId]['node_id'] <= 0 )
                return;
            $below = '/' . $this->stored[$manualRemoteId]['node_id'] . '/';
        }
        foreach ( self::gone( $this->stored, $plan['items'], $below ) as $remoteId => $stored )
        {
            $this->change( 'remove', "$remoteId ({$stored['class']})" );
            if ( !$this->dryRun )
                $this->removeObject( $stored );
            unset( $this->stored[$remoteId] );
        }
    }

    /**
     * The stored objects to remove: generated (help-gen-...), not in the plan, below a path when one is given; the
     * deepest first, so a chapter that goes takes nothing with it that stays
     *
     * @param array $stored remote_id => array( 'path_string', ... )
     * @param array $items the plan's items, remote_id => item
     * @param string|null $below a node path ("/1/43/9001/"): only objects below it
     * @return array remote_id => stored row
     */
    public static function gone( array $stored, array $items, $below = null )
    {
        $gone = array();
        foreach ( $stored as $remoteId => $row )
        {
            if ( isset( $items[$remoteId] ) || !expHelpManualBuilder::isGenerated( $remoteId ) )
                continue;
            if ( $below !== null && strpos( (string)$row['path_string'], $below ) === false )
                continue;
            $gone[$remoteId] = $row;
        }
        uasort( $gone, function ( $a, $b ) { return substr_count( (string)$b['path_string'], '/' ) <=> substr_count( (string)$a['path_string'], '/' ); } );
        return $gone;
    }

    /**
     * The classes as differing() compares them (tests give them without a database)
     *
     * @param array $classes identifier => array( 'id', 'attributes' => identifier => array( 'type', 'options' ) )
     */
    public function setClasses( array $classes )
    {
        $this->classes = $classes;
    }

    protected function removeObject( array $stored )
    {
        $this->flush();
        $object = eZContentObject::fetch( (int)$stored['object_id'] );
        if ( !$object instanceof eZContentObject )
            return;
        // whatever is still below it (nothing the plan keeps: that was moved away before) goes with it
        if ( (int)$stored['node_id'] > 0 )
            eZContentObjectTreeNode::removeSubtrees( array( (int)$stored['node_id'] ), false );
        else
            $object->purge();
        eZContentObject::clearCache( array( (int)$stored['object_id'] ) );
    }

    // ---------------------------------------------------------------- transactions

    /** Called before each write: the write joins the open transaction, a full one is committed first */
    protected function begin()
    {
        if ( $this->pending >= $this->batchSize )
            $this->flush();
        if ( $this->pending === 0 )
            eZDB::instance()->begin();
        $this->pending++;
    }

    protected function flush()
    {
        if ( $this->pending > 0 )
        {
            eZDB::instance()->commit();
            $this->pending = 0;
        }
    }

    protected function change( $action, $what )
    {
        $this->changes[] = array( 'step' => 'manuals', 'action' => $action, 'what' => $what );
        $this->counts[$action] = isset( $this->counts[$action] ) ? $this->counts[$action] + 1 : 1;
    }
}

?>
