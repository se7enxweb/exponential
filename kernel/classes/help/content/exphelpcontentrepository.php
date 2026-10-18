<?php
/**
 * Reads help maintained as content out of the database into plain arrays (the shapes expHelpContentValidator
 * describes), without any permission filter: the provider (expHelpContentProvider) filters by the current user, the
 * validator and the command see everything.
 *
 * Everything is found by remote_id (help-root ...) and class identifier, never by a node, section or state id from a
 * settings file: those differ between installations.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentRepository
{
    /** @var expHelpContentRepository|null */
    protected static $instance = null;

    /** @var array|null the loaded set: topics, figures, maps, glossary, objects (object id => eZContentObject) */
    protected $loaded = null;

    /** @var array section id => identifier */
    protected $sectionIdentifiers = array();

    /**
     * The repository of this request
     *
     * @return expHelpContentRepository
     */
    public static function instance()
    {
        if ( self::$instance === null )
            self::$instance = new self();
        return self::$instance;
    }

    /**
     * Forgets what was loaded (after a change, between requests of a persistent worker)
     */
    public static function resetInstance()
    {
        self::$instance = null;
    }

    /**
     * Whether the content model is installed: the classes exist and the help root is there
     *
     * @return bool
     */
    public static function isInstalled()
    {
        return self::rootNode() !== null && eZContentClass::fetchByIdentifier( 'help_task', false ) !== null;
    }

    /**
     * The node of the help root (remote_id help-root), null when the tree is not created
     *
     * @return eZContentObjectTreeNode|null
     */
    public static function rootNode()
    {
        $object = eZContentObject::fetchByRemoteID( expHelpContentSchema::ROOT );
        if ( !$object instanceof eZContentObject )
            return null;
        $node = $object->attribute( 'main_node' );
        return $node instanceof eZContentObjectTreeNode ? $node : null;
    }

    /**
     * Every help object below the help root, as arrays
     *
     * @return array array( 'topics' => [], 'figures' => [], 'maps' => [], 'glossary' => [], 'objects' => [id => eZContentObject] )
     */
    public function load()
    {
        if ( $this->loaded !== null )
            return $this->loaded;
        $set = array( 'topics' => array(), 'figures' => array(), 'maps' => array(), 'glossary' => array(), 'objects' => array() );
        $root = self::rootNode();
        if ( $root === null )
            return $this->loaded = $set;

        $classes = expHelpContentSchema::classIdentifiers();
        $nodes = eZContentObjectTreeNode::subTreeByNodeID(
            array( 'ClassFilterType' => 'include', 'ClassFilterArray' => $classes, 'Limitation' => array(),
                   'IgnoreVisibility' => true, 'MainNodeOnly' => true, 'LoadDataMap' => true,
                   'SortBy' => array( 'path', true ) ),
            (int)$root->attribute( 'node_id' ) );
        foreach ( is_array( $nodes ) ? $nodes : array() as $node )
        {
            $object = $node->attribute( 'object' );
            if ( !$object instanceof eZContentObject )
                continue;
            $row = $this->toArray( $object, $node );
            $set['objects'][(int)$object->attribute( 'id' )] = $object;
            switch ( $row['class'] )
            {
                case 'help_screenshot':
                    $set['figures'][] = $row;
                    break;
                case 'help_glossary_entry':
                    $set['glossary'][] = $row;
                    break;
                case 'help_manual':
                case 'help_section':
                    $set['maps'][] = $row;
                    break;
                default:
                    $set['topics'][] = $row;
            }
        }
        return $this->loaded = $set;
    }

    /**
     * The topic with this help_id (whatever its state), null when there is none
     *
     * @param string $helpId
     * @return array|null
     */
    public function topic( $helpId )
    {
        foreach ( $this->load()['topics'] as $topic )
        {
            if ( $topic['help_id'] === (string)$helpId )
                return $topic;
        }
        return null;
    }

    /**
     * The manual or chapter with this remote_id or object id, null when there is none
     *
     * @param string|int $key
     * @return array|null
     */
    public function map( $key )
    {
        foreach ( $this->load()['maps'] as $map )
        {
            if ( $map['remote_id'] === (string)$key || ( is_int( $key ) && $map['object_id'] === $key ) )
                return $map;
        }
        return null;
    }

    /**
     * Any loaded help object by object id: array( kind => topics|figures|maps|glossary, item => array ) or null
     *
     * @param int $objectId
     * @return array|null
     */
    public function byObjectId( $objectId )
    {
        foreach ( array( 'topics', 'figures', 'maps', 'glossary' ) as $kind )
        {
            foreach ( $this->load()[$kind] as $item )
            {
                if ( $item['object_id'] === (int)$objectId )
                    return array( 'kind' => $kind, 'item' => $item );
            }
        }
        return null;
    }

    /**
     * The content object of a loaded help object
     *
     * @param int $objectId
     * @return eZContentObject|null
     */
    public function object( $objectId )
    {
        $set = $this->load();
        return isset( $set['objects'][(int)$objectId] ) ? $set['objects'][(int)$objectId] : null;
    }

    /**
     * One object as an array (the shapes of expHelpContentValidator)
     *
     * @param eZContentObject $object
     * @param eZContentObjectTreeNode|null $node
     * @return array
     */
    public function toArray( eZContentObject $object, $node = null )
    {
        $class = (string)$object->attribute( 'class_identifier' );
        $dataMap = $object->attribute( 'data_map' );
        $value = function ( $identifier ) use ( $dataMap ) {
            return isset( $dataMap[$identifier] ) ? self::attributeValue( $dataMap[$identifier] ) : null;
        };
        $states = $object->stateIdentifierArray();
        $state = '';
        foreach ( $states as $pair )
        {
            if ( strpos( $pair, expHelpContentSchema::STATE_GROUP . '/' ) === 0 )
                $state = substr( $pair, strlen( expHelpContentSchema::STATE_GROUP ) + 1 );
        }
        $row = array(
            'object_id' => (int)$object->attribute( 'id' ),
            'node_id' => $node instanceof eZContentObjectTreeNode ? (int)$node->attribute( 'node_id' ) : (int)$object->attribute( 'main_node_id' ),
            'class' => $class,
            'remote_id' => (string)$object->attribute( 'remote_id' ),
            'name' => (string)$object->attribute( 'name' ),
            'section' => $this->sectionIdentifier( (int)$object->attribute( 'section_id' ) ),
            'section_id' => (int)$object->attribute( 'section_id' ),
            'state' => $state,
            'modified' => (int)$object->attribute( 'modified' ),
            'published' => (int)$object->attribute( 'published' ),
            'language' => (string)$object->attribute( 'current_language' ),
        );
        if ( isset( expHelpContentSchema::TOPIC_TYPES[$class] ) )
        {
            $fields = array();
            foreach ( array_keys( expHelpContentSchema::textFields( $class ) ) as $field )
                $fields[$field] = (string)$value( $field );
            $row += array(
                'type' => expHelpContentSchema::TOPIC_TYPES[$class],
                'help_id' => trim( (string)$value( 'help_id' ) ),
                'title' => trim( (string)$value( 'title' ) ),
                'shortdesc' => trim( (string)$value( 'shortdesc' ) ),
                'audience' => (array)$value( 'audience' ),
                'platform' => (array)$value( 'platform' ),
                'version_from' => trim( (string)$value( 'version_from' ) ),
                'version_to' => trim( (string)$value( 'version_to' ) ),
                'review_date' => (int)$value( 'review_date' ),
                'owner' => (int)$value( 'owner' ),
                'related' => (array)$value( 'related' ),
                'figures' => (array)$value( 'figures' ),
                'fields' => $fields,
            );
        }
        elseif ( $class === 'help_screenshot' )
        {
            $image = isset( $dataMap['image'] ) ? $dataMap['image'] : null;
            $row += array(
                'alt_text' => trim( (string)$value( 'alt_text' ) ),
                'caption' => trim( (string)$value( 'caption' ) ),
                'platform' => (array)$value( 'platform' ),
                'version' => trim( (string)$value( 'version' ) ),
                'figure_language' => trim( (string)$value( 'language' ) ),
                'topic_field' => trim( (string)$value( 'section' ) ),
                'figure_id' => trim( (string)$value( 'figure_id' ) ),
                'has_image' => $image instanceof eZContentObjectAttribute && $image->attribute( 'has_content' ),
                'image_url' => self::imageUrl( $image ),
            );
        }
        elseif ( $class === 'help_glossary_entry' )
        {
            $row += array(
                'term' => trim( (string)$value( 'term' ) ),
                'definition' => (string)$value( 'definition' ),
                'avoid' => self::splitList( (string)$value( 'avoid' ) ),
                'audience' => (array)$value( 'audience' ),
                'note' => (string)$value( 'note' ),
            );
        }
        else
        {
            $row += array(
                'title' => trim( (string)$value( 'title' ) ),
                'subtitle' => trim( (string)$value( 'subtitle' ) ),
                'shortdesc' => trim( (string)$value( 'shortdesc' ) ),
                'audience' => $class === 'help_manual' ? (array)$value( 'audience' ) : array(),
                'edition' => trim( (string)$value( 'edition' ) ),
                'date' => (int)$value( 'date' ),
                'license' => trim( (string)$value( 'license' ) ),
                'authors' => (array)$value( 'authors' ),
                'cover' => (int)$value( 'cover' ),
                'topicrefs' => (array)$value( 'topicrefs' ),
            );
        }
        return $row;
    }

    /**
     * The value of an attribute as plain PHP: text, list of option names, list of object ids, an object id, a timestamp
     *
     * @param eZContentObjectAttribute $attribute
     * @return mixed
     */
    public static function attributeValue( eZContentObjectAttribute $attribute )
    {
        switch ( $attribute->attribute( 'data_type_string' ) )
        {
            case 'ezstring':
            case 'eztext':
                return (string)$attribute->attribute( 'data_text' );
            case 'ezselection':
                $ids = explode( '-', (string)$attribute->attribute( 'data_text' ) );
                $content = $attribute->attribute( 'class_content' );
                $names = array();
                foreach ( isset( $content['options'] ) ? $content['options'] : array() as $option )
                {
                    if ( (string)$attribute->attribute( 'data_text' ) !== '' && in_array( (string)$option['id'], $ids, true ) )
                        $names[] = (string)$option['name'];
                }
                return $names;
            case 'ezobjectrelationlist':
                $content = $attribute->content();
                $ids = array();
                foreach ( isset( $content['relation_list'] ) ? $content['relation_list'] : array() as $item )
                    $ids[] = (int)$item['contentobject_id'];
                return $ids;
            case 'ezobjectrelation':
            case 'ezdate':
                return (int)$attribute->attribute( 'data_int' );
            case 'ezauthor':
                $content = $attribute->content();
                $names = array();
                if ( is_object( $content ) )
                {
                    foreach ( (array)$content->attribute( 'author_list' ) as $author )
                        $names[] = (string)$author['name'];
                }
                return $names;
        }
        return $attribute->attribute( 'has_content' ) ? (string)$attribute->toString() : '';
    }

    /**
     * The URL of the image of a figure (its "original" alias), '' when there is none
     *
     * @param eZContentObjectAttribute|null $attribute
     * @return string
     */
    public static function imageUrl( $attribute )
    {
        if ( !$attribute instanceof eZContentObjectAttribute || !$attribute->attribute( 'has_content' ) )
            return '';
        $content = $attribute->content();
        if ( !is_object( $content ) )
            return '';
        $alias = $content->attribute( 'original' );
        return is_array( $alias ) && !empty( $alias['url'] ) ? '/' . ltrim( (string)$alias['url'], '/' ) : '';
    }

    /**
     * A list field: one value per line or separated by commas
     *
     * @param string $text
     * @return string[]
     */
    public static function splitList( $text )
    {
        $parts = preg_split( '/[\r\n,;]+/', (string)$text );
        return array_values( array_filter( array_map( 'trim', $parts ), 'strlen' ) );
    }

    protected function sectionIdentifier( $sectionId )
    {
        if ( !isset( $this->sectionIdentifiers[$sectionId] ) )
        {
            $section = eZSection::fetch( $sectionId );
            $this->sectionIdentifiers[$sectionId] = $section instanceof eZSection ? (string)$section->attribute( 'identifier' ) : '';
        }
        return $this->sectionIdentifiers[$sectionId];
    }
}

?>
