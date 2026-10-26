<?php
/**
 * Help maintained as content, delivered into the help system (exphelp): the topics the current user may see, rendered
 * with the help's Markdown renderer, searchable next to the pages of doc/, as JSON for context help by help_id, and as
 * the answer of the context help for a module and view ([ContentSettings] ContextHelpIds[]).
 *
 * Who sees what is decided by the Exponential permissions alone (content/read with the section and state limitations
 * of the roles) plus one rule: a topic that is not Released is shown only to those who may edit it (help editors),
 * marked as a preview. Released topics are delivered; Draft and Review topics are delivered nowhere else.
 *
 * Page ids in the help are "content/<help_id>"; the topic view is exphelp/topic/<help_id>, the JSON
 * exphelp/context/<help_id> and exphelp/context/manual/<remote_id>.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentProvider implements expHelpContextProviderInterface, expHelpContextPageInterface
{
    /** The prefix of the page ids of topics in the help */
    const ID_PREFIX = 'content/';

    /** The default map of the plan's audiences to the help's audiences */
    const DEFAULT_AUDIENCE_MAP = array( 'visitors' => 'user', 'members' => 'user', 'editors' => 'editor', 'administrators' => 'admin' );

    /** Seconds the list of released help_ids of the context registry is kept */
    const CONTEXT_CACHE_TTL = 300;

    /** @var expHelpContentProvider|null */
    protected static $instance = null;

    /** @var expHelpContentRepository */
    protected $repository;

    /** @var array object id => read|preview|none */
    protected $access = array();

    /** @var expHelpContentAccess|false|null */
    protected $accessChecker = null;

    /** @var array|null releasedHelpIds() of this request (a static property: a persistent worker resets it per request) */
    protected static $releasedMemo = null;

    public function __construct( ?expHelpContentRepository $repository = null )
    {
        $this->repository = $repository;
    }

    /** @return expHelpContentProvider */
    public static function instance()
    {
        if ( self::$instance === null )
            self::$instance = new self();
        return self::$instance;
    }

    public static function resetInstance()
    {
        self::$instance = null;
        expHelpContentRepository::resetInstance();
    }

    /**
     * Whether help as content is switched on (exphelp.ini [ContentSettings] Enabled, default enabled) and installed
     *
     * @return bool
     */
    public static function isEnabled()
    {
        if ( (string)expHelpContentSchema::setting( 'Enabled', 'enabled' ) === 'disabled' )
            return false;
        try
        {
            return expHelpContentRepository::rootNode() !== null;
        }
        catch ( Exception $e )
        {
            return false;
        }
    }

    /** @return expHelpContentRepository */
    public function repository()
    {
        return $this->repository !== null ? $this->repository : expHelpContentRepository::instance();
    }

    /**
     * The topic with this help_id if the current user may see it (with 'preview' => true when it is not Released),
     * else null
     *
     * @param string $helpId
     * @return array|null
     */
    public function topic( $helpId )
    {
        if ( !expHelpContentValidator::isValidHelpId( $helpId ) )
            return null;
        $topic = $this->repository()->topic( $helpId );
        return $topic !== null ? $this->visible( $topic ) : null;
    }

    /**
     * Every topic the current user may see
     *
     * @param bool $withText false: without the text fields ('fields'), for lists and counts (the texts are not read)
     * @return array
     */
    public function topics( $withText = true )
    {
        $list = array();
        $repository = $this->repository();
        foreach ( ( $withText ? $repository->load() : $repository->index() )['topics'] as $topic )
        {
            if ( $topic['help_id'] !== '' && ( $topic = $this->visible( $topic ) ) !== null )
                $list[] = $topic;
        }
        return $list;
    }

    /**
     * The newest topics the current user may see, newest first (by modification; the same time in the order of the
     * tree), and how many the user may see in all: the dashboard's list, without the texts
     *
     * @param int $limit
     * @return array array( 'topics' => list of at most $limit topics, 'count' => int )
     */
    public function newestTopics( $limit )
    {
        $topics = $this->topics( false );
        $order = array_keys( $topics );
        usort( $order, function ( $a, $b ) use ( $topics ) {
            return $topics[$b]['modified'] <=> $topics[$a]['modified'] ?: $a <=> $b;
        } );
        $newest = array();
        foreach ( array_slice( $order, 0, max( 0, (int)$limit ) ) as $i )
            $newest[] = $topics[$i];
        return array( 'topics' => $newest, 'count' => count( $topics ) );
    }

    /**
     * Whether the help content is being read into the cache in the background, so that this request has none
     * (expHelpContentRepository::isBuilding())
     *
     * @return bool
     */
    public function isBuilding()
    {
        $repository = $this->repository();
        return method_exists( $repository, 'isBuilding' ) && $repository->isBuilding();
    }

    /**
     * The manuals the current user may see
     *
     * @return array
     */
    public function manuals()
    {
        $list = array();
        foreach ( $this->repository()->index()['maps'] as $map )
        {
            if ( $map['class'] === 'help_manual' && ( $map = $this->visible( $map ) ) !== null )
                $list[] = $map;
        }
        return $list;
    }

    /**
     * The access of the current user to help objects, decided without loading them where it can be
     *
     * @return expHelpContentAccess|null
     */
    protected function accessChecker()
    {
        if ( $this->accessChecker === null )
            $this->accessChecker = class_exists( 'expHelpContentAccess' ) ? expHelpContentAccess::forCurrentUser() : false;
        return $this->accessChecker !== false ? $this->accessChecker : null;
    }

    /**
     * A topic, manual or chapter as the current user may see it: Released and readable, or editable (a preview)
     *
     * Decided from the repository's data of the object (expHelpContentAccess) where the user's policies allow it, else
     * by the object's canRead() and canEdit().
     *
     * @param array $item
     * @return array|null
     */
    public function visible( array $item )
    {
        $id = (int)$item['object_id'];
        if ( !isset( $this->access[$id] ) )
        {
            $released = $item['state'] === expHelpContentSchema::STATE_RELEASED;
            $data = $this->repository()->accessData( $id );
            $checker = $data !== null ? $this->accessChecker() : null;
            $access = $checker !== null ? $checker->decide( $data, $released ) : null;
            if ( $access === null )
            {
                $object = $this->repository()->object( $id );
                $access = 'none';
                if ( $object instanceof eZContentObject )
                {
                    if ( $released && $object->canRead() )
                        $access = 'read';
                    elseif ( $object->canEdit() )
                        $access = 'preview';
                }
            }
            $this->access[$id] = $access;
        }
        if ( $this->access[$id] === 'none' )
            return null;
        $item['preview'] = $this->access[$id] === 'preview';
        return $item;
    }

    /**
     * The help's audiences (editor, admin, user ...) of a topic's audience names (exphelp.ini [ContentSettings]
     * AudienceMap[<lower case name>]=<help audience>)
     *
     * @param string[] $names
     * @return string[]
     */
    public static function helpAudiences( array $names )
    {
        $map = expHelpContentSchema::setting( 'AudienceMap', array() );
        $map = is_array( $map ) && $map ? array_change_key_case( $map, CASE_LOWER ) : self::DEFAULT_AUDIENCE_MAP;
        $audiences = array();
        foreach ( $names as $name )
        {
            $key = mb_strtolower( trim( (string)$name ) );
            if ( isset( $map[$key] ) && !in_array( $map[$key], $audiences, true ) )
                $audiences[] = (string)$map[$key];
        }
        return $audiences;
    }

    /**
     * The Markdown of a topic: its text fields in reading order, each under its heading
     *
     * @param array $topic
     * @param callable|null $translate function( $english ): string, default ezpI18n kernel/exphelp
     * @return string
     */
    public static function markdown( array $topic, $translate = null )
    {
        if ( $translate === null )
            $translate = function ( $text ) { return class_exists( 'ezpI18n' ) ? ezpI18n::tr( 'kernel/exphelp', $text ) : $text; };
        $parts = array();
        foreach ( expHelpContentSchema::textFields( $topic['class'] ) as $field => $heading )
        {
            $text = isset( $topic['fields'][$field] ) ? trim( (string)$topic['fields'][$field] ) : '';
            if ( $text === '' )
                continue;
            $parts[] = ( $heading !== '' ? '## ' . call_user_func( $translate, $heading ) . "\n\n" : '' ) . $text;
        }
        return self::linksRewritten( implode( "\n\n", $parts ) );
    }

    /**
     * Links to other topics are written help:<help_id>[#anchor]; for the renderer they become relative links to
     * content/<help_id>.md that linkResolver() turns into the topic view's address
     *
     * @param string $markdown
     * @return string
     */
    public static function linksRewritten( $markdown )
    {
        return preg_replace( '/\]\(\s*help:([a-z0-9.-]{1,80})(#[A-Za-z0-9_-]{1,100})?\s*\)/', '](content/$1.md$2)', (string)$markdown );
    }

    /**
     * The link resolver of topics: content/<help_id>.md to the topic view, everything else as for doc/ pages
     *
     * @param string $href
     * @param string $currentId
     * @return string|null
     */
    public static function linkResolver( $href, $currentId = '' )
    {
        if ( preg_match( '#^/?content/([a-z0-9.-]{1,80})\.md(?:\#(.*))?$#', (string)$href, $m ) )
            return expHelpLinks::absolute( expHelp::url( 'topic', $m[1], isset( $m[2] ) ? rawurldecode( $m[2] ) : '' ) );
        return expHelpLinks::link( $href, '' );
    }

    /**
     * The rendered topic: array( html, toc, title )
     *
     * @param array $topic
     * @return array
     */
    public function render( array $topic )
    {
        $renderer = new expHelpMarkdownRenderer( array(
            'link_resolver' => array( __CLASS__, 'linkResolver' ),
            'image_resolver' => array( 'expHelpLinks', 'image' ),
            'image_sources' => expContentSecurityPolicy::imageSources(),
            'remote_images' => class_exists( 'expHelpRemoteImages' ) ? array( new expHelpRemoteImages(), 'resolve' ) : null ) );
        return $renderer->render( self::markdown( $topic ), '' );
    }

    /**
     * The related topics and the figures of a topic, each only if the current user may see it
     *
     * @param array $topic
     * @return array array( 'related' => list of topics, 'figures' => list of figures )
     */
    public function relations( array $topic )
    {
        $related = array();
        foreach ( $topic['related'] as $objectId )
        {
            $found = $this->repository()->byObjectId( $objectId );
            if ( $found !== null && $found['kind'] === 'topics' && ( $item = $this->visible( $found['item'] ) ) !== null )
                $related[] = $item;
        }
        $figures = array();
        foreach ( $topic['figures'] as $objectId )
        {
            $found = $this->repository()->byObjectId( $objectId );
            if ( $found === null || $found['kind'] !== 'figures' )
                continue;
            $object = $this->repository()->object( $objectId );
            if ( $object instanceof eZContentObject && ( $object->canRead() || $object->canEdit() ) && $found['item']['image_url'] !== '' )
                $figures[] = $found['item'];
        }
        return array( 'related' => $related, 'figures' => $figures );
    }

    /**
     * The validator's findings of one topic (the style guide's rules), for the people who edit or release it: the
     * topic is checked with what its rules need (the topics it relates to, the topics with its help_id, its figures,
     * the glossary, the released help_ids), not the whole help. The map rules belong to the manuals and are left out.
     *
     * @param array $topic a topic of topic() (with its fields)
     * @return array the findings of expHelpContentValidator for this object, ERROR first
     */
    public function findings( array $topic )
    {
        $set = $this->repository()->load();
        $objectId = (int)$topic['object_id'];
        $related = array_map( 'intval', isset( $topic['related'] ) ? (array)$topic['related'] : array() );
        $topics = array();
        foreach ( $set['topics'] as $item )
        {
            if ( (int)$item['object_id'] === $objectId || in_array( (int)$item['object_id'], $related, true )
                 || ( $topic['help_id'] !== '' && $item['help_id'] === $topic['help_id'] ) )
                $topics[] = $item;
        }
        $wanted = array_map( 'intval', isset( $topic['figures'] ) ? (array)$topic['figures'] : array() );
        $figures = array_values( array_filter( $set['figures'], function ( $figure ) use ( $wanted ) {
            return in_array( (int)$figure['object_id'], $wanted, true );
        } ) );
        $registry = array();
        foreach ( expHelpContentHelpIds::registry() as $helpId => $id )
        {
            if ( (int)$id === $objectId || $helpId === $topic['help_id'] )
                $registry[$helpId] = $id;
        }
        $validator = new expHelpContentValidator( array( 'released_ids' => $registry, 'used_help_ids' => array() ) );
        $findings = array();
        foreach ( $validator->validate( $topics, $figures, array(), $set['glossary'] ) as $finding )
        {
            // a figure's section follows every topic that shows it, which this check does not read
            if ( (int)$finding['object_id'] === $objectId
                 || ( in_array( (int)$finding['object_id'], $wanted, true ) && $finding['code'] !== 'section.mismatch' ) )
                $findings[] = $finding;
        }
        return $findings;
    }

    /**
     * A topic as a document of the help (the shape of expHelpDocumentIndex), for lists, search and the viewer's frame
     *
     * @param array $topic
     * @return array
     */
    public static function document( array $topic )
    {
        return array(
            'id' => self::ID_PREFIX . $topic['help_id'],
            'path' => '',
            'title' => $topic['title'] !== '' ? $topic['title'] : $topic['help_id'],
            'summary' => $topic['shortdesc'],
            'type' => 'topic',
            'topic_type' => $topic['type'],
            'category' => 'help-topics',
            'audience' => self::helpAudiences( $topic['audience'] ),
            'audience_names' => $topic['audience'],
            'platform' => $topic['platform'],
            'help_id' => $topic['help_id'],
            'object_id' => $topic['object_id'],
            'node_id' => $topic['node_id'],
            'state' => $topic['state'],
            'preview' => !empty( $topic['preview'] ),
            'version_from' => $topic['version_from'],
            'version_to' => $topic['version_to'],
            'mtime' => $topic['modified'],
            'url' => expHelp::url( 'topic', $topic['help_id'] ),
            'suffix' => 'md',
        );
    }

    /**
     * The context help answer of a topic: what exphelp/context/<help_id> sends
     *
     * @param array $topic
     * @return array
     */
    public function json( array $topic )
    {
        $relations = $this->relations( $topic );
        $related = array();
        foreach ( $relations['related'] as $item )
            $related[] = array( 'help_id' => $item['help_id'], 'title' => $item['title'], 'shortdesc' => $item['shortdesc'],
                                'url' => expHelpLinks::absolute( expHelp::url( 'context', $item['help_id'] ) ) );
        $figures = array();
        foreach ( $relations['figures'] as $figure )
            $figures[] = array( 'url' => self::absoluteFileUrl( $figure['image_url'] ), 'alt' => $figure['alt_text'],
                                'caption' => $figure['caption'], 'figure_id' => $figure['figure_id'], 'field' => $figure['topic_field'] );
        return array(
            'help_id' => $topic['help_id'],
            'type' => $topic['type'],
            'title' => $topic['title'],
            'shortdesc' => $topic['shortdesc'],
            'audience' => $topic['audience'],
            'platform' => $topic['platform'],
            'version_from' => $topic['version_from'],
            'version_to' => $topic['version_to'],
            'state' => $topic['state'],
            'preview' => !empty( $topic['preview'] ),
            'language' => $topic['language'],
            'modified' => gmdate( 'c', (int)$topic['modified'] ),
            'markdown' => self::markdown( $topic, function ( $text ) { return $text; } ),
            'fields' => $topic['fields'],
            'related' => $related,
            'figures' => $figures,
            'url' => expHelpLinks::absolute( expHelp::url( 'topic', $topic['help_id'] ) ),
        );
    }

    /**
     * The table of contents of a manual (or chapter) as the current user may see it: nested chapters and topics;
     * what the user may not see is left out
     *
     * @param array $map
     * @param int $depth
     * @param array $seen object ids on the way (a chapter that lists itself ends there)
     * @return array list of array( kind chapter|topic, title, shortdesc, help_id|remote_id, children[] )
     */
    public function toc( array $map, $depth = 0, array $seen = array() )
    {
        $entries = array();
        if ( $depth > 8 )
            return $entries;
        $seen[] = (int)$map['object_id'];
        foreach ( $map['topicrefs'] as $objectId )
        {
            $found = $this->repository()->byObjectId( $objectId );
            if ( $found === null || in_array( (int)$objectId, $seen, true ) )
                continue;
            $item = $this->visible( $found['item'] );
            if ( $item === null )
                continue;
            if ( $found['kind'] === 'maps' )
                $entries[] = array( 'kind' => 'chapter', 'title' => $item['title'], 'shortdesc' => $item['shortdesc'],
                                    'remote_id' => $item['remote_id'], 'preview' => $item['preview'],
                                    'children' => $this->toc( $item, $depth + 1, $seen ) );
            elseif ( $found['kind'] === 'topics' && $item['help_id'] !== '' )
                $entries[] = array( 'kind' => 'topic', 'title' => $item['title'], 'shortdesc' => $item['shortdesc'],
                                    'help_id' => $item['help_id'], 'type' => $item['type'], 'preview' => $item['preview'],
                                    'url' => expHelp::url( 'topic', $item['help_id'] ), 'children' => array() );
        }
        return $entries;
    }

    /**
     * The JSON of a manual: its metadata and table of contents
     *
     * @param array $manual
     * @return array
     */
    public function manualJson( array $manual )
    {
        $strip = function ( array $entries ) use ( &$strip ) {
            $out = array();
            foreach ( $entries as $entry )
            {
                $row = array( 'kind' => $entry['kind'], 'title' => $entry['title'], 'shortdesc' => $entry['shortdesc'] );
                if ( $entry['kind'] === 'topic' )
                {
                    $row['help_id'] = $entry['help_id'];
                    $row['type'] = $entry['type'];
                    $row['url'] = expHelpLinks::absolute( expHelp::url( 'context', $entry['help_id'] ) );
                }
                else
                    $row['children'] = $strip( $entry['children'] );
                $out[] = $row;
            }
            return $out;
        };
        return array(
            'remote_id' => $manual['remote_id'],
            'title' => $manual['title'],
            'subtitle' => $manual['subtitle'],
            'shortdesc' => $manual['shortdesc'],
            'audience' => $manual['audience'],
            'edition' => $manual['edition'],
            'date' => $manual['date'] > 0 ? gmdate( 'Y-m-d', $manual['date'] ) : '',
            'license' => $manual['license'],
            'authors' => $manual['authors'],
            'preview' => !empty( $manual['preview'] ),
            'modified' => gmdate( 'c', (int)$manual['modified'] ),
            'toc' => $strip( $this->toc( $manual ) ),
            'url' => expHelpLinks::absolute( expHelp::url( 'manual', $manual['remote_id'] ) ),
        );
    }

    /**
     * The visible manual with this remote_id (help-manual-...), or null
     *
     * @param string $remoteId
     * @return array|null
     */
    public function manual( $remoteId )
    {
        if ( !is_string( $remoteId ) || !preg_match( '/^[A-Za-z0-9_.-]{1,100}$/', $remoteId ) )
            return null;
        $map = $this->repository()->map( $remoteId );
        if ( $map === null || $map['class'] !== 'help_manual' )
            return null;
        return $this->visible( $map );
    }

    /**
     * Searches the visible topics: title (boosted), help_id, short description and text. The hits have the shape of
     * expHelpSearch's, with type "topic" and category "help-topics".
     *
     * @param string $query
     * @param array $filters audience, type (only "topic" or the topic types keep topics), category
     * @param int $limit
     * @return array array( 'total' => int, 'hits' => list )
     */
    public function search( $query, array $filters = array(), $limit = 10 )
    {
        $terms = array_values( array_unique( expHelpSearch::tokenize( (string)$query ) ) );
        if ( !$terms )
            return array( 'total' => 0, 'hits' => array() );
        $stems = array();
        foreach ( $terms as $term )
            $stems[$term] = expHelpSearch::stem( $term );
        $hits = array();
        $weights = array( 'title' => 4.0, 'help_id' => 3.0, 'shortdesc' => 2.0, 'text' => 1.0 );
        // the words of every topic, kept with the repository's rows (computing them is what a search cost)
        $repository = $this->repository();
        $wordSets = method_exists( $repository, 'searchWords' ) ? $repository->searchWords() : array();
        // the cache is being filled in the background: no help topics until it is there
        if ( $this->isBuilding() )
            return array( 'total' => 0, 'hits' => array() );
        foreach ( $this->topics() as $topic )
        {
            $document = self::document( $topic );
            if ( !self::matchesFilters( $document, $filters ) )
                continue;
            $sets = isset( $wordSets[$topic['object_id']] ) ? $wordSets[$topic['object_id']] : self::searchWordSets( $topic );
            $score = 0.0;
            $all = true;
            foreach ( $stems as $term => $stem )
            {
                $best = 0.0;
                foreach ( $weights as $field => $weight )
                {
                    if ( isset( $sets[$field][$term] ) || isset( $sets[$field][$stem] ) )
                        $best = max( $best, $weight );
                }
                if ( $best === 0.0 )
                    $all = false;
                $score += $best;
            }
            if ( !$all || $score <= 0 )
                continue;
            $text = $topic['shortdesc'] !== '' ? $topic['shortdesc'] : expHelpSearch::plain( implode( "\n", $topic['fields'] ) );
            $parts = expHelpSearch::snippetParts( $text, array_merge( array_keys( $stems ), array_values( $stems ) ) );
            $hits[] = array(
                'id' => $document['id'],
                'path' => '',
                'title' => $document['title'],
                'section' => null,
                'anchor' => null,
                'url' => $document['url'],
                'href' => expHelpLinks::absolute( $document['url'] ),
                'snippet' => expHelpSearch::snippetHtml( $parts ),
                'snippet_parts' => $parts,
                'audience' => $document['audience'],
                'category' => $document['category'],
                'type' => $document['type'],
                'topic_type' => $topic['type'],
                'help_id' => $topic['help_id'],
                'preview' => $document['preview'],
                'score' => round( $score, 3 ),
            );
        }
        usort( $hits, function ( $a, $b ) { return $b['score'] <=> $a['score'] ?: strcmp( $a['title'], $b['title'] ); } );
        return array( 'total' => count( $hits ), 'hits' => array_slice( $hits, 0, max( 1, (int)$limit ) ) );
    }

    /**
     * The words search() compares the query with, per field of a topic: each word of the field and its stem
     * (expHelpSearch::tokenize(), stem()), as keys; the text is the topic's text fields as plain text
     *
     * @param array $topic a topic with its text fields
     * @return array array( 'title' => word => true, 'help_id' => ..., 'shortdesc' => ..., 'text' => ... )
     */
    public static function searchWordSets( array $topic )
    {
        $texts = array(
            'title' => $topic['title'],
            'help_id' => str_replace( array( '.', '-' ), ' ', $topic['help_id'] ),
            'shortdesc' => $topic['shortdesc'],
            'text' => expHelpSearch::plain( implode( "\n", isset( $topic['fields'] ) ? (array)$topic['fields'] : array() ) ),
        );
        $sets = array();
        foreach ( $texts as $field => $text )
        {
            $words = array();
            foreach ( expHelpSearch::tokenize( $text ) as $word )
                $words[$word] = $words[expHelpSearch::stem( $word )] = true;
            $sets[$field] = $words;
        }
        return $sets;
    }

    /**
     * Whether a topic document passes the search filters of the help
     *
     * @param array $document
     * @param array $filters
     * @return bool
     */
    public static function matchesFilters( array $document, array $filters )
    {
        if ( !empty( $filters['audience'] ) )
        {
            $wanted = explode( ',', (string)$filters['audience'] );
            if ( !array_intersect( $wanted, $document['audience'] ) )
                return false;
        }
        if ( !empty( $filters['type'] ) )
        {
            $wanted = explode( ',', (string)$filters['type'] );
            if ( !in_array( 'topic', $wanted, true ) && !in_array( $document['topic_type'], $wanted, true ) )
                return false;
        }
        if ( !empty( $filters['category'] ) && !in_array( 'help-topics', explode( ',', (string)$filters['category'] ), true ) )
            return false;
        return true;
    }

    // ---------------------------------------------------------------- context help

    /**
     * expHelpContextProviderInterface: the topic that answers a module and view, from exphelp.ini [ContentSettings]
     * ContextHelpIds[<module>/<view>] (or [<module>], [<design>:<module>/<view>] ...) = <help_id>, when that topic is
     * Released. The answer is "content/<help_id>", which expHelpContextRegistry turns into the topic view.
     *
     * @param string $module
     * @param string $view
     * @param string $design
     * @return string|null
     */
    public function helpContext( $module, $view, $design )
    {
        $map = expHelpContentSchema::setting( 'ContextHelpIds', array() );
        if ( !is_array( $map ) || !$map )
            return null;
        $normalized = array();
        foreach ( $map as $key => $helpId )
        {
            $key = expHelpContextRegistry::normalizeKey( $key );
            if ( $key !== '' && expHelpContentValidator::isValidHelpId( (string)$helpId ) )
                $normalized[$key] = (string)$helpId;
        }
        $released = null;
        foreach ( expHelpContextRegistry::candidates( $module, $view, $design ) as $candidate )
        {
            if ( !isset( $normalized[$candidate[0]] ) )
                continue;
            if ( $released === null )
                $released = self::releasedHelpIds();
            if ( isset( $released[$normalized[$candidate[0]]] ) )
                return self::ID_PREFIX . $normalized[$candidate[0]];
        }
        return null;
    }

    /**
     * expHelpContextPageInterface: the topic view of a Released topic "content/<help_id>"
     *
     * @param string $id
     * @return array|null
     */
    public function contextPage( $id )
    {
        if ( !preg_match( '#^content/([a-z0-9.-]{1,80})$#', (string)$id, $m ) )
            return null;
        $released = self::releasedHelpIds();
        if ( !isset( $released[$m[1]] ) )
            return null;
        return array( 'url' => expHelp::url( 'topic', $m[1] ), 'title' => (string)$released[$m[1]] );
    }

    /**
     * The help_ids of the Released topics and their titles, kept for CONTEXT_CACHE_TTL seconds in the help cache (every
     * admin page asks the context registry; the database is read at most once in that time)
     *
     * @param bool $refresh
     * @return array help_id => title
     */
    public static function releasedHelpIds( $refresh = false )
    {
        $memo =& self::$releasedMemo;
        if ( $memo !== null && !$refresh )
            return $memo;
        $file = self::cacheFile();
        if ( !$refresh && $file !== null && is_file( $file ) && filemtime( $file ) > time() - self::CONTEXT_CACHE_TTL )
        {
            $data = json_decode( (string)@file_get_contents( $file ), true );
            if ( is_array( $data ) )
                return $memo = $data;
        }
        $ids = array();
        try
        {
            if ( self::isEnabled() )
            {
                $repository = expHelpContentRepository::instance();
                foreach ( $repository->index()['topics'] as $topic )
                {
                    if ( $topic['state'] === expHelpContentSchema::STATE_RELEASED && $topic['help_id'] !== '' )
                        $ids[$topic['help_id']] = $topic['title'];
                }
                // while the background command fills the help cache: the last list, kept as it is
                if ( $repository->isBuilding() )
                {
                    $data = $file !== null ? json_decode( (string)@file_get_contents( $file ), true ) : null;
                    return $memo = is_array( $data ) ? $data : array();
                }
            }
        }
        catch ( Exception $e )
        {
            eZDebug::writeWarning( 'Help topics could not be read: ' . $e->getMessage(), __METHOD__ );
        }
        if ( $file !== null )
        {
            if ( !is_dir( dirname( $file ) ) )
                eZDir::mkdir( dirname( $file ), false, true );
            $tmp = $file . '.' . getmypid() . '.tmp';
            if ( is_dir( dirname( $file ) ) && @file_put_contents( $tmp, json_encode( $ids, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) !== false )
            {
                @chmod( $tmp, eZFile::fileMode( octdec( eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' ) ) ) );
                @rename( $tmp, $file );
            }
        }
        return $memo = $ids;
    }

    /**
     * Forgets the cached list of released help_ids (after a change by the command)
     */
    public static function clearCache()
    {
        $file = self::cacheFile();
        if ( $file !== null && is_file( $file ) )
            @unlink( $file );
        self::$releasedMemo = null;
    }

    protected static function cacheFile()
    {
        $dir = expHelp::cacheDirectory();
        return is_string( $dir ) && $dir !== '' ? rtrim( $dir, '/' ) . '/content-helpids.json' : null;
    }

    /**
     * A file URL of the storage (/var/...) with the site's host, for JSON clients
     *
     * @param string $url
     * @return string
     */
    public static function absoluteFileUrl( $url )
    {
        if ( $url === '' || preg_match( '#^https?://#', $url ) )
            return $url;
        $host = class_exists( 'eZSys' ) ? eZSys::serverURL() : '';
        return $host . eZSys::wwwDir() . '/' . ltrim( $url, '/' );
    }
}

?>
