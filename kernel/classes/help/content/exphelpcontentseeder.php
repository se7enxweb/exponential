<?php
/**
 * Creates the initial glossary, topics, chapters and manuals from a data file (share/help/seed-glossary.json,
 * share/help/seed-topics.json). Objects are found again by remote_id: help-topic-<help_id>, help-glossary-<term>, and
 * the remote_id a chapter or manual has in the file. An existing object stays as the editors left it unless the
 * option update is given. New objects start in the first state, Draft; with release they are set to Released.
 *
 * Changes are recorded as in expHelpContentInstaller: array( step, action, what ).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentSeeder
{
    /** @var bool */
    protected $dryRun;
    /** @var bool */
    protected $update;
    /** @var bool */
    protected $release;
    /** @var string */
    protected $language;
    /** @var int */
    protected $userId;
    /** @var array */
    protected $changes = array();
    /** @var int[] object ids created or updated */
    protected $touched = array();

    /**
     * @param array $options dry_run, update, release (bool), language (locale), user_id
     */
    public function __construct( array $options = array() )
    {
        $this->dryRun = !empty( $options['dry_run'] );
        $this->update = !empty( $options['update'] );
        $this->release = !empty( $options['release'] );
        $this->language = !empty( $options['language'] ) ? (string)$options['language'] : (string)eZContentObject::defaultLanguage();
        $this->userId = !empty( $options['user_id'] ) ? (int)$options['user_id'] : (int)eZUser::currentUserID();
    }

    /** @return array */
    public function changes()
    {
        return $this->changes;
    }

    /** @return int[] */
    public function touched()
    {
        return $this->touched;
    }

    /**
     * Reads a seed file
     *
     * @param string $file
     * @return array
     * @throws RuntimeException when it is not a JSON object
     */
    public static function readFile( $file )
    {
        $data = is_file( $file ) ? json_decode( (string)file_get_contents( $file ), true ) : null;
        if ( !is_array( $data ) )
            throw new RuntimeException( "The seed file is missing or not JSON: $file" );
        return $data;
    }

    /**
     * The glossary entries of a seed file
     *
     * @param array $data
     */
    public function seedGlossary( array $data )
    {
        $folder = $this->folderNode( expHelpContentSchema::GLOSSARY );
        foreach ( isset( $data['entries'] ) ? (array)$data['entries'] : array() as $entry )
        {
            $term = isset( $entry['term'] ) ? trim( (string)$entry['term'] ) : '';
            if ( $term === '' )
                continue;
            $remoteId = 'help-glossary-' . preg_replace( '/[^a-z0-9]+/', '-', mb_strtolower( $term ) );
            $attributes = array(
                'term' => $term,
                'definition' => isset( $entry['definition'] ) ? (string)$entry['definition'] : '',
                'avoid' => implode( "\n", isset( $entry['avoid'] ) ? (array)$entry['avoid'] : array() ),
                'note' => isset( $entry['note'] ) ? (string)$entry['note'] : '',
            );
            if ( !empty( $entry['audience'] ) )
                $attributes['audience'] = implode( '|', (array)$entry['audience'] );
            $this->ensureObject( 'glossary', 'help_glossary_entry', $remoteId, $folder, $attributes, "glossary entry \"$term\"" );
        }
    }

    /**
     * The topics, chapters and manuals of a seed file. Relations name help_ids (topics) and remote_ids (chapters);
     * they are set once every object of the file exists.
     *
     * @param array $data
     */
    public function seedTopics( array $data )
    {
        $relations = array();
        foreach ( isset( $data['topics'] ) ? (array)$data['topics'] : array() as $topic )
        {
            $class = isset( $topic['class'] ) ? (string)$topic['class'] : '';
            $helpId = isset( $topic['help_id'] ) ? (string)$topic['help_id'] : '';
            if ( !isset( expHelpContentSchema::TOPIC_FOLDERS[$class] ) || !expHelpContentValidator::isValidHelpId( $helpId ) )
            {
                $this->change( 'error', "topic \"$helpId\": no topic class or not a valid help_id" );
                continue;
            }
            $attributes = array(
                'title' => (string)$topic['title'],
                'shortdesc' => (string)$topic['shortdesc'],
                'help_id' => $helpId,
                'audience' => implode( '|', (array)$topic['audience'] ),
                'platform' => implode( '|', isset( $topic['platform'] ) ? (array)$topic['platform'] : array() ),
                'version_from' => isset( $topic['version_from'] ) ? (string)$topic['version_from'] : '',
                'review_date' => (string)self::reviewDate( $helpId ),
            );
            foreach ( isset( $topic['fields'] ) ? (array)$topic['fields'] : array() as $field => $text )
                $attributes[$field] = (string)$text;
            $remoteId = expHelpContentSchema::topicRemoteId( $helpId );
            $this->ensureObject( 'topics', $class, $remoteId, $this->folderNode( expHelpContentSchema::TOPIC_FOLDERS[$class] ), $attributes, "topic $helpId ($class)" );
            if ( !empty( $topic['related'] ) )
                $relations[] = array( $remoteId, 'related', (array)$topic['related'] );
        }
        $manualsFolder = $this->folderNode( expHelpContentSchema::MANUALS );
        foreach ( array( 'chapters' => 'help_section', 'manuals' => 'help_manual' ) as $key => $class )
        {
            foreach ( isset( $data[$key] ) ? (array)$data[$key] : array() as $map )
            {
                $remoteId = isset( $map['remote_id'] ) ? (string)$map['remote_id'] : '';
                if ( !preg_match( '/^help-(manual|chapter)-[a-z0-9-]+$/', $remoteId ) )
                {
                    $this->change( 'error', "$key: the remote_id \"$remoteId\" is not help-manual-... or help-chapter-..." );
                    continue;
                }
                $attributes = array( 'title' => (string)$map['title'], 'shortdesc' => isset( $map['shortdesc'] ) ? (string)$map['shortdesc'] : '' );
                if ( $class === 'help_manual' )
                {
                    foreach ( array( 'subtitle', 'edition', 'license' ) as $field )
                    {
                        if ( isset( $map[$field] ) )
                            $attributes[$field] = (string)$map[$field];
                    }
                    $attributes['audience'] = implode( '|', isset( $map['audience'] ) ? (array)$map['audience'] : array() );
                    $attributes['date'] = (string)mktime( 0, 0, 0 );
                }
                // a chapter lives in the manual that lists it first; until then, in the manuals folder
                $this->ensureObject( 'topics', $class, $remoteId, $manualsFolder, $attributes, ( $class === 'help_manual' ? 'manual ' : 'chapter ' ) . $remoteId );
                if ( !empty( $map['topicrefs'] ) )
                    $relations[] = array( $remoteId, 'topicrefs', (array)$map['topicrefs'] );
            }
        }
        foreach ( $relations as $relation )
            $this->ensureRelations( $relation[0], $relation[1], $relation[2] );
    }

    /**
     * Release review date of the style guide: + 12 months, areas privacy and account + 6 months
     *
     * @param string $helpId
     * @param int|null $now
     * @return int
     */
    public static function reviewDate( $helpId, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $area = strtok( (string)$helpId, '.' );
        $months = in_array( $area, array( 'privacy', 'account' ), true ) ? 6 : 12;
        return (int)mktime( 0, 0, 0, (int)date( 'n', $now ) + $months, (int)date( 'j', $now ), (int)date( 'Y', $now ) );
    }

    protected function ensureObject( $step, $class, $remoteId, $parentNode, array $attributes, $label )
    {
        $object = eZContentObject::fetchByRemoteID( $remoteId );
        if ( $object instanceof eZContentObject )
        {
            if ( !$this->update )
                return $object;
            if ( !self::differs( $object, $attributes ) )
                return $object;
            $this->change( 'update', $label );
            if ( $this->dryRun )
                return $object;
            eZContentFunctions::updateAndPublishObject( $object, array( 'attributes' => $attributes, 'language' => $this->language ) );
            $this->touched[] = (int)$object->attribute( 'id' );
            return eZContentObject::fetch( (int)$object->attribute( 'id' ) );
        }
        $this->change( 'create', $label );
        if ( $this->dryRun )
            return null;
        if ( !$parentNode instanceof eZContentObjectTreeNode )
        {
            $this->change( 'error', "$label: its folder does not exist (run exp:help:content install first)" );
            return null;
        }
        $object = eZContentFunctions::createAndPublishObject( array(
            'class_identifier' => $class,
            'parent_node_id' => (int)$parentNode->attribute( 'node_id' ),
            'creator_id' => $this->userId,
            'remote_id' => $remoteId,
            'language' => $this->language,
            'attributes' => $attributes,
        ) );
        if ( !$object instanceof eZContentObject )
        {
            $this->change( 'error', "$label could not be created" );
            return null;
        }
        $this->touched[] = (int)$object->attribute( 'id' );
        if ( $this->release )
            $this->releaseObject( $object );
        return $object;
    }

    /**
     * Whether a seed's attribute values differ from the object's
     */
    protected static function differs( eZContentObject $object, array $attributes )
    {
        $dataMap = $object->attribute( 'data_map' );
        foreach ( $attributes as $identifier => $value )
        {
            if ( !isset( $dataMap[$identifier] ) )
                continue;
            if ( in_array( $identifier, array( 'review_date', 'date' ), true ) )
                continue;
            $current = expHelpContentRepository::attributeValue( $dataMap[$identifier] );
            $current = is_array( $current ) ? implode( '|', $current ) : (string)$current;
            if ( $current !== (string)$value )
                return true;
        }
        return false;
    }

    /**
     * Sets a relation list from help_ids / remote_ids, when it is empty (or with update, when it differs)
     */
    protected function ensureRelations( $remoteId, $attribute, array $keys )
    {
        $ids = array();
        $missing = array();
        foreach ( $keys as $key )
        {
            $target = eZContentObject::fetchByRemoteID( expHelpContentValidator::isValidHelpId( $key ) ? expHelpContentSchema::topicRemoteId( $key ) : $key );
            if ( !$target instanceof eZContentObject )
                $target = eZContentObject::fetchByRemoteID( $key );
            if ( $target instanceof eZContentObject )
                $ids[] = (int)$target->attribute( 'id' );
            else
                $missing[] = $key;
        }
        if ( $missing )
            $this->change( 'error', "$remoteId: $attribute names what does not exist: " . implode( ', ', $missing ) );
        $object = eZContentObject::fetchByRemoteID( $remoteId );
        if ( !$object instanceof eZContentObject )
        {
            if ( $this->dryRun )
                $this->change( 'relate', "$remoteId: $attribute (" . count( $keys ) . ')' );
            return;
        }
        $dataMap = $object->attribute( 'data_map' );
        $current = isset( $dataMap[$attribute] ) ? (array)expHelpContentRepository::attributeValue( $dataMap[$attribute] ) : array();
        if ( $current === $ids || ( $current && !$this->update ) || !$ids )
            return;
        $this->change( 'relate', "$remoteId: $attribute (" . count( $ids ) . ')' );
        if ( $this->dryRun )
            return;
        $wasReleased = in_array( expHelpContentSchema::STATE_GROUP . '/' . expHelpContentSchema::STATE_RELEASED, $object->stateIdentifierArray(), true );
        eZContentFunctions::updateAndPublishObject( $object, array( 'attributes' => array( $attribute => implode( '-', $ids ) ), 'language' => $this->language ) );
        $this->touched[] = (int)$object->attribute( 'id' );
        if ( $wasReleased || $this->release )
            $this->releaseObject( eZContentObject::fetch( (int)$object->attribute( 'id' ) ) );
    }

    /**
     * Sets the state Released (the seed is a release decision of the person who runs it)
     *
     * @param eZContentObject $object
     */
    public function releaseObject( eZContentObject $object )
    {
        $group = eZContentObjectStateGroup::fetchByIdentifier( expHelpContentSchema::STATE_GROUP );
        $state = $group instanceof eZContentObjectStateGroup ? $group->stateByIdentifier( expHelpContentSchema::STATE_RELEASED ) : null;
        if ( !$state instanceof eZContentObjectState )
            return;
        if ( $this->dryRun )
            return;
        if ( $object->assignState( $state ) )
        {
            $this->change( 'release', 'object ' . $object->attribute( 'id' ) . ' (' . $object->attribute( 'remote_id' ) . ')' );
            eZContentCacheManager::clearContentCacheIfNeeded( (int)$object->attribute( 'id' ) );
        }
        // a released help_id is never renamed or reused: recorded as the operation records it
        if ( class_exists( 'expHelpContentWorkflow' ) )
            expHelpContentWorkflow::recordHelpId( $object );
    }

    protected function folderNode( $remoteId )
    {
        $object = eZContentObject::fetchByRemoteID( $remoteId );
        $node = $object instanceof eZContentObject ? $object->attribute( 'main_node' ) : null;
        return $node instanceof eZContentObjectTreeNode ? $node : null;
    }

    protected function change( $action, $what )
    {
        $this->changes[] = array( 'step' => 'seed', 'action' => $action, 'what' => $what );
    }
}

?>
