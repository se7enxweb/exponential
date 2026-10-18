<?php
/**
 * The content model of help maintained as content (doc/specifications/6.0/help-system.md, doc/features/6.0/help-content.md):
 * the eight help_* content classes, the sections, the object state group, the folder tree and the two roles. Data only,
 * no database: the installer, the validator, the provider and the tests all read the model from here.
 *
 * The audiences and platforms are configurable (exphelp.ini [ContentSettings] Audiences[], PublicAudiences[],
 * Platforms[]). Selection options are only ever appended: ezselection stores the position of an option, so a
 * reordered list would silently give every topic another audience.
 *
 * Text fields (body, context, steps ...) are eztext holding Markdown: the help renders them with
 * expHelpMarkdownRenderer, the context help endpoint delivers them as they are, and the validator can see the
 * numbered list of a task and the bold labels of a step.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentSchema
{
    const CLASS_GROUP = 'Help';

    const STATE_GROUP = 'help_status';
    const STATE_DRAFT = 'draft';
    const STATE_REVIEW = 'review';
    const STATE_RELEASED = 'released';

    const SECTION_PUBLIC = 'help_public';
    const SECTION_INTERNAL = 'help_internal';

    const ROLE_EDITOR = 'Help editor';
    const ROLE_RELEASE = 'Help release';

    const ROOT = 'help-root';
    const POOL = 'help-pool';
    const MANUALS = 'help-manuals';
    const EBOOKS = 'help-ebooks';
    const GLOSSARY = 'help-pool-glossary';
    const FIGURES = 'help-pool-figures';

    /** The prefix of the remote_id of a topic: help-topic-<help_id> */
    const TOPIC_REMOTE_PREFIX = 'help-topic-';
    /** The prefix of the remote_id of a manual: help-manual-<key> */
    const MANUAL_REMOTE_PREFIX = 'help-manual-';

    /** Topic class => DITA topic type */
    const TOPIC_TYPES = array(
        'help_concept' => 'concept',
        'help_task' => 'task',
        'help_reference' => 'reference',
        'help_troubleshooting' => 'troubleshooting',
    );

    /** Topic class => the folder of the pool it is created in */
    const TOPIC_FOLDERS = array(
        'help_concept' => 'help-pool-concepts',
        'help_task' => 'help-pool-tasks',
        'help_reference' => 'help-pool-reference',
        'help_troubleshooting' => 'help-pool-troubleshooting',
    );

    /** The map classes: manual and chapter */
    const MAP_CLASSES = array( 'help_manual', 'help_section' );

    /** Defaults of the configurable lists (the example audiences of the plan) */
    const DEFAULT_AUDIENCES = array( 'Visitors', 'Members', 'Editors', 'Administrators' );
    const DEFAULT_PUBLIC_AUDIENCES = array( 'Visitors' );
    const DEFAULT_PLATFORMS = array( 'Web', 'Admin', 'Mobile' );

    /**
     * A setting of exphelp.ini [ContentSettings], or the default when it is not set (or exphelp.ini cannot be read)
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function setting( $name, $default )
    {
        if ( !class_exists( 'eZINI', false ) && !class_exists( 'eZINI' ) )
            return $default;
        try
        {
            $ini = eZINI::instance( 'exphelp.ini' );
            if ( $ini->hasVariable( 'ContentSettings', $name ) )
            {
                $value = $ini->variable( 'ContentSettings', $name );
                if ( is_array( $default ) )
                {
                    // keys are kept (AudienceMap[visitors]=user, ContextHelpIds[content/edit]=...); a plain list keeps its order
                    $value = array_filter( (array)$value, function ( $v ) { return is_scalar( $v ) && trim( (string)$v ) !== ''; } );
                    if ( count( array_filter( array_keys( $value ), 'is_string' ) ) === 0 )
                        $value = array_values( $value );
                    return $value ? $value : $default;
                }
                return $value;
            }
        }
        catch ( Exception $e )
        {
        }
        return $default;
    }

    /** @return string[] the audience options, in their stored order */
    public static function audiences()
    {
        return self::setting( 'Audiences', self::DEFAULT_AUDIENCES );
    }

    /** @return string[] the audiences whose topics are public (readable without login) */
    public static function publicAudiences()
    {
        return self::setting( 'PublicAudiences', self::DEFAULT_PUBLIC_AUDIENCES );
    }

    /** @return string[] the platform options, in their stored order */
    public static function platforms()
    {
        return self::setting( 'Platforms', self::DEFAULT_PLATFORMS );
    }

    /** @return string[] the topic class identifiers */
    public static function topicClasses()
    {
        return array_keys( self::TOPIC_TYPES );
    }

    /** @return string[] every help class identifier */
    public static function classIdentifiers()
    {
        return array_keys( self::classes() );
    }

    /**
     * The fields of a topic type that hold its text, in reading order, with the heading the help shows above each
     * ('' = no heading). The heading is English; the provider translates it (context kernel/exphelp).
     *
     * @param string $class
     * @return array field => heading
     */
    public static function textFields( $class )
    {
        switch ( $class )
        {
            case 'help_task':
                return array( 'context' => '', 'prereq' => 'Before you begin', 'steps' => 'Steps', 'result' => 'Result', 'postreq' => 'What to do next' );
            case 'help_troubleshooting':
                return array( 'condition' => 'Condition', 'cause' => 'Cause', 'remedy' => 'Remedy' );
            case 'help_concept':
            case 'help_reference':
                return array( 'body' => '' );
        }
        return array();
    }

    /**
     * The fields the style guide requires per topic type (section 1)
     *
     * @param string $class
     * @return string[]
     */
    public static function requiredFields( $class )
    {
        $shared = array( 'title', 'shortdesc', 'help_id', 'audience' );
        switch ( $class )
        {
            case 'help_concept':
            case 'help_reference':
                return array_merge( $shared, array( 'body' ) );
            case 'help_task':
                return array_merge( $shared, array( 'steps' ) );
            case 'help_troubleshooting':
                return array_merge( $shared, array( 'condition', 'remedy' ) );
        }
        return $shared;
    }

    /**
     * The eight classes: identifier => array( name, container, name_pattern, description, attributes ). Each attribute:
     * identifier => array( name, type, required, searchable, translatable, params ). params: max_length (ezstring),
     * rows (eztext), options + multiple (ezselection), classes (relation constraints), default_empty (ezdate).
     *
     * @return array
     */
    public static function classes()
    {
        $topics = array_merge( self::topicClasses(), array() );
        $shared = array(
            'title' => self::attr( 'Title', 'ezstring', true, true, true, array( 'max_length' => 255 ) ),
            'shortdesc' => self::attr( 'Short description', 'ezstring', true, true, true, array( 'max_length' => 250 ) ),
            'help_id' => self::attr( 'Help ID', 'ezstring', true, true, false, array( 'max_length' => 80 ) ),
            'audience' => self::attr( 'Audience', 'ezselection', true, true, false, array( 'options' => self::audiences(), 'multiple' => true ) ),
            'platform' => self::attr( 'Platform', 'ezselection', false, true, false, array( 'options' => self::platforms(), 'multiple' => true ) ),
            'version_from' => self::attr( 'Valid from version', 'ezstring', false, false, false, array( 'max_length' => 20 ) ),
            'version_to' => self::attr( 'Valid to version', 'ezstring', false, false, false, array( 'max_length' => 20 ) ),
            'review_date' => self::attr( 'Review date', 'ezdate', false, false, false, array( 'default_empty' => true ) ),
            'owner' => self::attr( 'Owner', 'ezobjectrelation', false, false, false, array() ),
            'related' => self::attr( 'Related topics', 'ezobjectrelationlist', false, false, false, array( 'classes' => $topics ) ),
            'figures' => self::attr( 'Figures', 'ezobjectrelationlist', false, false, false, array( 'classes' => array( 'help_screenshot' ) ) ),
        );
        $text = function ( $name, $required ) {
            return self::attr( $name, 'eztext', $required, true, true, array( 'rows' => 12 ) );
        };
        $insert = function ( array $own ) use ( $shared ) {
            // the shared fields with the type's text after audience/platform and before the version and relations
            $head = array_slice( $shared, 0, 5, true );
            $tail = array_slice( $shared, 5, null, true );
            return $head + $own + $tail;
        };
        $refs = array_merge( array( 'help_section' ), $topics );

        return array(
            'help_manual' => array(
                'name' => 'Help manual', 'container' => true, 'name_pattern' => '<title>',
                'description' => 'A manual (DITA bookmap): an ordered list of chapters and topics',
                'attributes' => array(
                    'title' => self::attr( 'Title', 'ezstring', true, true, true, array( 'max_length' => 255 ) ),
                    'subtitle' => self::attr( 'Subtitle', 'ezstring', false, true, true, array( 'max_length' => 255 ) ),
                    'shortdesc' => self::attr( 'Short description', 'ezstring', true, true, true, array( 'max_length' => 250 ) ),
                    'audience' => self::attr( 'Audience', 'ezselection', true, false, false, array( 'options' => self::audiences(), 'multiple' => true ) ),
                    'edition' => self::attr( 'Edition', 'ezstring', false, false, false, array( 'max_length' => 40 ) ),
                    'date' => self::attr( 'Date', 'ezdate', false, false, false, array( 'default_empty' => true ) ),
                    'cover' => self::attr( 'Cover', 'ezobjectrelation', false, false, false, array() ),
                    'license' => self::attr( 'License', 'ezstring', false, false, true, array( 'max_length' => 255 ) ),
                    'authors' => self::attr( 'Authors', 'ezauthor', false, false, false, array() ),
                    'topicrefs' => self::attr( 'Chapters and topics', 'ezobjectrelationlist', false, false, false, array( 'classes' => $refs ) ),
                ) ),
            'help_section' => array(
                'name' => 'Help chapter', 'container' => true, 'name_pattern' => '<title>',
                'description' => 'A chapter of a manual: an ordered list of topics (and chapters)',
                'attributes' => array(
                    'title' => self::attr( 'Title', 'ezstring', true, true, true, array( 'max_length' => 255 ) ),
                    'shortdesc' => self::attr( 'Short description', 'ezstring', false, true, true, array( 'max_length' => 250 ) ),
                    'topicrefs' => self::attr( 'Topics', 'ezobjectrelationlist', false, false, false, array( 'classes' => $refs ) ),
                ) ),
            'help_concept' => array(
                'name' => 'Help concept', 'container' => false, 'name_pattern' => '<title>',
                'description' => 'What is this, what is it for? (DITA concept)',
                'attributes' => $insert( array( 'body' => $text( 'Body', true ) ) ) ),
            'help_task' => array(
                'name' => 'Help task', 'container' => false, 'name_pattern' => '<title>',
                'description' => 'How do I do this? (DITA task: exactly one numbered list of steps)',
                'attributes' => $insert( array(
                    'context' => $text( 'Context', false ),
                    'prereq' => $text( 'Prerequisites', false ),
                    'steps' => $text( 'Steps', true ),
                    'result' => $text( 'Result', false ),
                    'postreq' => $text( 'What to do next', false ),
                ) ) ),
            'help_reference' => array(
                'name' => 'Help reference', 'container' => false, 'name_pattern' => '<title>',
                'description' => 'Which values, options, limits exist? (DITA reference)',
                'attributes' => $insert( array( 'body' => $text( 'Body', true ) ) ) ),
            'help_troubleshooting' => array(
                'name' => 'Help troubleshooting', 'container' => false, 'name_pattern' => '<title>',
                'description' => 'What is wrong, and how do I fix it? (DITA troubleshooting)',
                'attributes' => $insert( array(
                    'condition' => $text( 'Condition', true ),
                    'cause' => $text( 'Cause', false ),
                    'remedy' => $text( 'Remedy', true ),
                ) ) ),
            'help_screenshot' => array(
                'name' => 'Help figure', 'container' => false, 'name_pattern' => '<caption|figure_id|alt_text>',
                'description' => 'A figure of a topic: image with alt text (required)',
                'attributes' => array(
                    'image' => self::attr( 'Image', 'ezimage', true, false, false, array() ),
                    'alt_text' => self::attr( 'Alt text', 'ezstring', true, true, true, array( 'max_length' => 500 ) ),
                    'caption' => self::attr( 'Caption', 'ezstring', false, true, true, array( 'max_length' => 255 ) ),
                    'platform' => self::attr( 'Platform', 'ezselection', false, false, false, array( 'options' => self::platforms(), 'multiple' => true ) ),
                    'version' => self::attr( 'Version', 'ezstring', false, false, false, array( 'max_length' => 20 ) ),
                    'language' => self::attr( 'Language', 'ezstring', false, false, false, array( 'max_length' => 20 ) ),
                    'section' => self::attr( 'Field of the topic', 'ezstring', false, false, false, array( 'max_length' => 40 ) ),
                    'figure_id' => self::attr( 'Figure ID', 'ezstring', false, false, false, array( 'max_length' => 100 ) ),
                ) ),
            'help_glossary_entry' => array(
                'name' => 'Help glossary entry', 'container' => false, 'name_pattern' => '<term>',
                'description' => 'A term of the glossary and the synonyms to avoid',
                'attributes' => array(
                    'term' => self::attr( 'Term', 'ezstring', true, true, true, array( 'max_length' => 100 ) ),
                    'definition' => self::attr( 'Definition', 'eztext', false, true, true, array( 'rows' => 5 ) ),
                    'avoid' => self::attr( 'Synonyms to avoid', 'eztext', false, true, true, array( 'rows' => 3 ) ),
                    'audience' => self::attr( 'Audience', 'ezselection', false, false, false, array( 'options' => self::audiences(), 'multiple' => true ) ),
                    'note' => self::attr( 'Note', 'eztext', false, false, true, array( 'rows' => 3 ) ),
                ) ),
        );
    }

    /**
     * The folder tree below the parent node (exphelp.ini [ContentSettings] ParentNode): remote_id => array( parent
     * remote_id or '' for the parent node, name )
     *
     * @return array
     */
    public static function tree()
    {
        return array(
            self::ROOT => array( '', 'Help' ),
            self::POOL => array( self::ROOT, 'Topic pool' ),
            'help-pool-concepts' => array( self::POOL, 'Concepts' ),
            'help-pool-tasks' => array( self::POOL, 'Tasks' ),
            'help-pool-reference' => array( self::POOL, 'Reference' ),
            'help-pool-troubleshooting' => array( self::POOL, 'Troubleshooting' ),
            self::FIGURES => array( self::POOL, 'Figures' ),
            self::GLOSSARY => array( self::POOL, 'Glossary' ),
            self::MANUALS => array( self::ROOT, 'Manuals' ),
            self::EBOOKS => array( self::ROOT, 'E-books' ),
        );
    }

    /** @return array identifier => name */
    public static function sections()
    {
        return array( self::SECTION_PUBLIC => 'Help (public)', self::SECTION_INTERNAL => 'Help (internal)' );
    }

    /** @return array identifier => array( name, description ), in priority order (the first is the default) */
    public static function states()
    {
        return array(
            self::STATE_DRAFT => array( 'Draft', 'Work in progress, delivered nowhere' ),
            self::STATE_REVIEW => array( 'Review', 'Ready for the technical and linguistic review' ),
            self::STATE_RELEASED => array( 'Released', 'Delivered: web, context help, e-book' ),
        );
    }

    /**
     * The policies of the two roles and the one policy added to Anonymous. Symbolic limitation values, resolved by the
     * installer: 'Subtree' => array( remote_id ), 'Section' => array( section identifier ),
     * 'StateGroup_help_status' / 'NewState' => array( state identifier ).
     *
     * @return array role name => list of array( module, function, limitations )
     */
    public static function roles()
    {
        $group = 'StateGroup_' . self::STATE_GROUP;
        $tree = array( 'Subtree' => array( self::ROOT ) );
        $editable = array( $group => array( self::STATE_DRAFT, self::STATE_REVIEW ) );
        $all = array( self::STATE_DRAFT, self::STATE_REVIEW, self::STATE_RELEASED );
        $anonymous = array(
            // ONE policy with both limitations: two policies would grant read on either condition
            array( 'content', 'read', array( 'Section' => array( self::SECTION_PUBLIC ), $group => array( self::STATE_RELEASED ) ) ),
        );
        // Opt-in: the context help JSON without login (exphelp/context) for public topics, for a public site or app
        if ( self::setting( 'AnonymousContextHelp', 'disabled' ) === 'enabled' )
            $anonymous[] = array( 'exphelp', 'context', array() );
        return array(
            self::ROLE_EDITOR => array(
                array( 'content', 'read', $tree ),
                array( 'content', 'versionread', $tree ),
                array( 'content', 'create', $tree ),
                array( 'content', 'edit', $tree + $editable ),
                array( 'content', 'remove', $tree + $editable ),
                array( 'state', 'assign', $tree + array( $group => array( self::STATE_DRAFT ), 'NewState' => array( self::STATE_REVIEW ) ) ),
                array( 'exphelp', 'read', array() ),
            ),
            self::ROLE_RELEASE => array(
                array( 'state', 'assign', $tree + array( $group => $all, 'NewState' => $all ) ),
            ),
            'Anonymous' => $anonymous,
        );
    }

    /**
     * Whether a list of audience names makes a topic public: one of them is a public audience. No audience: internal.
     *
     * @param string[] $audiences
     * @param string[]|null $public default publicAudiences()
     * @return bool
     */
    public static function isPublicAudience( array $audiences, ?array $public = null )
    {
        $public = array_map( 'mb_strtolower', $public === null ? self::publicAudiences() : $public );
        foreach ( $audiences as $audience )
        {
            if ( in_array( mb_strtolower( trim( (string)$audience ) ), $public, true ) )
                return true;
        }
        return false;
    }

    /**
     * The section a topic (or manual) belongs in, from its audience
     *
     * @param string[] $audiences
     * @param string[]|null $public
     * @return string section identifier
     */
    public static function sectionForAudience( array $audiences, ?array $public = null )
    {
        return self::isPublicAudience( $audiences, $public ) ? self::SECTION_PUBLIC : self::SECTION_INTERNAL;
    }

    /**
     * The remote_id of the topic with this help_id
     *
     * @param string $helpId
     * @return string
     */
    public static function topicRemoteId( $helpId )
    {
        return self::TOPIC_REMOTE_PREFIX . $helpId;
    }

    protected static function attr( $name, $type, $required, $searchable, $translatable, array $params )
    {
        return array( 'name' => $name, 'type' => $type, 'required' => (bool)$required, 'searchable' => (bool)$searchable,
                      'translatable' => (bool)$translatable, 'params' => $params );
    }
}

?>
