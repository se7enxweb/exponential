<?php
/**
 * The manuals of help maintained as content, made from the Markdown help (doc/eng-us/features/6.0/help-content.md,
 * "Manuals from the Markdown help"). The Markdown stays the source: this class turns the folders and pages listed in
 * exphelp.ini [ManualSettings] Manuals[] into a plan of manuals (help_manual), chapters (help_section) and topics
 * (help_concept, help_task, help_reference, help_troubleshooting), and expHelpManualSync makes the database match it.
 *
 * No database here: the plan is plain arrays, so the tests check the mapping without one.
 *
 * - A manual per line of Manuals[]: <key>;<folder below doc/eng-us>[;<title>], or <folder>/*.md for the pages directly in
 *   a folder. Each subfolder is a chapter, a subfolder of it a chapter of the chapter. A folder's README.md (its index)
 *   is the first topic of its manual or chapter. Pages and folders are in the order the help shows them (front matter
 *   order, then title).
 * - Identifiers that never change while the page stays where it is: remote_id help-gen-manual-<key>,
 *   help-gen-chapter-<hash of the folder>, help-gen-topic-<hash of the page>; help_id doc.<path of the page>
 *   (doc.help.editors.publishing for help/editors/publishing.md). Every generated object has the prefix help-gen-, and
 *   nothing without it is ever changed or removed.
 * - The topic type: front matter topic_type (concept, task, reference, troubleshooting), else reference for the
 *   reference pages of the help (type reference, catalogue, specification, generated), else a task for a "How to ..."
 *   page with exactly one numbered list, else a concept. A task or troubleshooting page that cannot be split into the
 *   fields of its type is a concept.
 * - The text: the page without its front matter and its title, Markdown as the topic fields store it. A link to a page
 *   that is a topic becomes help:<help_id>; a link to any other page or image of the help becomes /doc/<file>, which
 *   the help resolves when it shows the topic.
 * - Audience: the help's audiences of the page ([AudienceSettings]), each as the first audience option that
 *   [ContentSettings] AudienceMap[] maps to it. Section: help_public when one of them is public, else help_internal.
 * - Translations: the pages of a translation directory (doc/ger-de/, paired through its .paths.json) are the
 *   translation of the same topics; chapters and manuals are translated when they contain a translated topic.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpManualBuilder
{
    /** Every object the builder makes has a remote_id with this prefix; it never touches another */
    const PREFIX = 'help-gen-';
    const MANUAL_PREFIX = 'help-gen-manual-';
    const CHAPTER_PREFIX = 'help-gen-chapter-';
    const TOPIC_PREFIX = 'help-gen-topic-';

    /** The area of the help_ids of generated topics */
    const AREA = 'doc';

    const TITLE_MAX = 255;

    /** The doc types of the help whose pages are reference topics */
    const REFERENCE_TYPES = array( 'reference', 'catalogue', 'specification', 'generated' );

    /** @var object the source of the default language (expHelpManualSource) */
    protected $source;

    /** @var array locale => source of a translation */
    protected $translations;

    /** @var string[] the audience options, in their order */
    protected $audienceOptions;

    /** @var array lower case option name => help audience */
    protected $audienceMap;

    /** @var string[] */
    protected $publicAudiences;

    /** @var string */
    protected $edition;

    /** @var string */
    protected $license;

    /** @var array key => array( locale => title ) */
    protected $translatedTitles;

    /** @var array list of messages: pages that could not be used as they are */
    protected $notices = array();

    /**
     * @param object $source the default language: tree( folder ), page( id ), idOf( path ) (expHelpManualSource)
     * @param array $translations locale => source of that language
     * @param array $options audiences (option names), audience_map (lower case name => help audience),
     *                       public_audiences, edition, license, translated_titles (key => locale => title);
     *                       defaults from exphelp.ini
     */
    public function __construct( $source, array $translations = array(), array $options = array() )
    {
        $this->source = $source;
        $this->translations = $translations;
        $this->audienceOptions = isset( $options['audiences'] ) ? array_values( (array)$options['audiences'] ) : expHelpContentSchema::audiences();
        $map = isset( $options['audience_map'] ) ? (array)$options['audience_map'] : expHelpContentSchema::setting( 'AudienceMap', expHelpContentProvider::DEFAULT_AUDIENCE_MAP );
        $this->audienceMap = array_change_key_case( $map, CASE_LOWER );
        $this->publicAudiences = isset( $options['public_audiences'] ) ? (array)$options['public_audiences'] : expHelpContentSchema::publicAudiences();
        $this->edition = isset( $options['edition'] ) ? (string)$options['edition'] : '';
        $this->license = isset( $options['license'] ) ? (string)$options['license'] : '';
        $this->translatedTitles = isset( $options['translated_titles'] ) ? (array)$options['translated_titles'] : array();
    }

    /** @return string[] what the last plan() could not take over as it is */
    public function notices()
    {
        return $this->notices;
    }

    // ---------------------------------------------------------------- settings

    /**
     * A setting of exphelp.ini [ManualSettings], or the default
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function setting( $name, $default )
    {
        try
        {
            $ini = eZINI::instance( 'exphelp.ini' );
            if ( $ini->hasVariable( 'ManualSettings', $name ) )
                return $ini->variable( 'ManualSettings', $name );
        }
        catch ( Exception $e )
        {
        }
        return $default;
    }

    /**
     * The manuals of Manuals[] (or of the lines given)
     *
     * @param string[]|null $lines
     * @return array array( 'manuals' => list of parseManualLine(), 'errors' => list of messages )
     */
    public static function manualsFromSettings( ?array $lines = null )
    {
        $lines = $lines === null ? (array)self::setting( 'Manuals', array() ) : $lines;
        $manuals = array();
        $errors = array();
        $keys = array();
        foreach ( $lines as $line )
        {
            if ( !is_string( $line ) || trim( $line ) === '' )
                continue;
            $manual = self::parseManualLine( $line );
            if ( $manual === null )
                $errors[] = "[ManualSettings] Manuals[]=$line is not <key>;<folder>[;<title>]";
            elseif ( isset( $keys[$manual['key']] ) )
                $errors[] = "[ManualSettings] Manuals[]: the key {$manual['key']} is used twice";
            else
            {
                $keys[$manual['key']] = true;
                $manuals[] = $manual;
            }
        }
        return array( 'manuals' => $manuals, 'errors' => $errors );
    }

    /**
     * One line of Manuals[]: <key>;<folder>[;<title>]
     *
     * @param string $line
     * @return array|null array( 'key', 'folder', 'loose' (bool: only the pages directly in it), 'title' ('' default) )
     */
    public static function parseManualLine( $line )
    {
        $parts = array_map( 'trim', explode( ';', (string)$line ) );
        if ( count( $parts ) < 2 || count( $parts ) > 3 )
            return null;
        $key = $parts[0];
        $folder = trim( str_replace( '\\', '/', $parts[1] ), '/' );
        if ( !preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $key ) || strlen( self::MANUAL_PREFIX . $key ) > 100 )
            return null;
        $loose = false;
        if ( preg_match( '#^(.*?)/?\*\.md$#', $folder, $m ) )
        {
            $loose = true;
            $folder = trim( $m[1], '/' );
        }
        if ( $folder !== '' && !preg_match( '#^[A-Za-z0-9._-]+(?:/[A-Za-z0-9._-]+)*$#', $folder ) || strpos( '/' . $folder . '/', '/../' ) !== false )
            return null;
        if ( $folder === '' && !$loose )
            return null;
        return array( 'key' => $key, 'folder' => $folder, 'loose' => $loose, 'title' => isset( $parts[2] ) ? $parts[2] : '' );
    }

    /**
     * TranslatedTitles[]=<key>;<locale>;<title>: the title of a manual whose title Manuals[] gives, in a translation
     *
     * @param string[]|null $lines
     * @return array key => array( locale => title )
     */
    public static function translatedTitlesFromSettings( ?array $lines = null )
    {
        $lines = $lines === null ? (array)self::setting( 'TranslatedTitles', array() ) : $lines;
        $titles = array();
        foreach ( $lines as $line )
        {
            $parts = array_map( 'trim', explode( ';', (string)$line, 3 ) );
            if ( count( $parts ) === 3 && $parts[0] !== '' && $parts[1] !== '' && $parts[2] !== '' )
                $titles[$parts[0]][$parts[1]] = $parts[2];
        }
        return $titles;
    }

    // ---------------------------------------------------------------- identifiers

    /**
     * Whether an object was made by the builder (and may be changed or removed by it)
     *
     * @param string $remoteId
     * @return bool
     */
    public static function isGenerated( $remoteId )
    {
        return strncmp( (string)$remoteId, self::PREFIX, strlen( self::PREFIX ) ) === 0;
    }

    /** @return string */
    public static function manualRemoteId( $key )
    {
        return self::MANUAL_PREFIX . $key;
    }

    /** @return string the remote_id of the chapter of a folder ("help/editors/publishing") */
    public static function chapterRemoteId( $folder )
    {
        return self::CHAPTER_PREFIX . substr( sha1( 'folder:' . trim( (string)$folder, '/' ) ), 0, 20 );
    }

    /** @return string the remote_id of the topic of a page ("help/editors/publishing.md") */
    public static function topicRemoteId( $pageId )
    {
        return self::TOPIC_PREFIX . substr( sha1( 'page:' . (string)$pageId ), 0, 20 );
    }

    /**
     * The help_id a page would have: doc.<path segments> (help/editors/publishing.md -> doc.help.editors.publishing)
     *
     * @param string $pageId
     * @return string
     */
    public static function helpIdCandidate( $pageId )
    {
        $path = preg_replace( '/\.(md|markdown)$/i', '', (string)$pageId );
        $parts = array( self::AREA );
        foreach ( explode( '/', $path ) as $segment )
        {
            $slug = trim( preg_replace( '/[^a-z0-9]+/', '-', strtolower( $segment ) ), '-' );
            $parts[] = $slug === '' ? 'x' : $slug;
        }
        return implode( '.', $parts );
    }

    /**
     * The help_ids of pages: the candidate, or, when it is longer than a help_id may be or another page has the same
     * candidate, its beginning and a hash of the page (the same on every run, whichever pages there are with it)
     *
     * @param string[] $pageIds
     * @return array page id => help_id
     */
    public static function helpIds( array $pageIds )
    {
        $candidates = array();
        $count = array();
        foreach ( $pageIds as $pageId )
        {
            $candidate = self::helpIdCandidate( $pageId );
            $candidates[$pageId] = $candidate;
            $count[$candidate] = isset( $count[$candidate] ) ? $count[$candidate] + 1 : 1;
        }
        $ids = array();
        foreach ( $candidates as $pageId => $candidate )
        {
            if ( strlen( $candidate ) > expHelpContentValidator::HELP_ID_MAX || $count[$candidate] > 1 )
                $candidate = rtrim( substr( $candidate, 0, expHelpContentValidator::HELP_ID_MAX - 9 ), '.-' ) . '-' . substr( sha1( 'page:' . $pageId ), 0, 8 );
            $ids[$pageId] = $candidate;
        }
        return $ids;
    }

    // ---------------------------------------------------------------- pages as topics

    /**
     * The text without a leading title heading (the topic's title is its own field)
     *
     * @param string $body
     * @return string
     */
    public static function withoutTitle( $body )
    {
        $text = ltrim( str_replace( array( "\r\n", "\r" ), "\n", (string)$body ), "\n" );
        if ( preg_match( '/^#[ \t]+[^\n]*(\n|$)/', $text, $m ) )
            $text = substr( $text, strlen( $m[0] ) );
        elseif ( preg_match( '/^[^\n#>\-*|`][^\n]*\n=+[ \t]*(\n|$)/', $text, $m ) )
            $text = substr( $text, strlen( $m[0] ) );
        return trim( $text, "\n" ) . "\n";
    }

    /**
     * The links and images of a page's Markdown, made independent of the file it was read from: a page that is a
     * topic becomes help:<help_id>[#anchor], any other file of the installation /<file>[#anchor] (the help resolves
     * /doc/... when it shows the topic). Links with a scheme, anchors on the page and code are left as they are.
     *
     * @param string $markdown
     * @param string $sourcePath the file the page was read from, relative to the installation root
     * @param callable $idOf function( path ): page id or null
     * @param array $helpIds page id => help_id of the pages that are topics
     * @return string
     */
    public static function rewriteLinks( $markdown, $sourcePath, $idOf, array $helpIds )
    {
        $target = function ( $href, $image ) use ( $sourcePath, $idOf, $helpIds ) {
            $bracketed = strlen( $href ) > 1 && $href[0] === '<' && substr( $href, -1 ) === '>';
            $raw = $bracketed ? substr( $href, 1, -1 ) : $href;
            if ( $raw === '' || $raw[0] === '#' || preg_match( '#^([a-z][a-z0-9+.-]*:|//)#i', $raw ) )
                return $href;
            $anchor = '';
            if ( ( $hash = strpos( $raw, '#' ) ) !== false )
            {
                $anchor = substr( $raw, $hash + 1 );
                $raw = substr( $raw, 0, $hash );
            }
            $raw = preg_replace( '/\?.*$/', '', $raw );
            if ( $raw === '' )
                return $href;
            $path = rawurldecode( $raw );
            $file = $path[0] === '/' ? ltrim( $path, '/' ) : expHelpLinks::resolvePathFrom( $path, $sourcePath );
            if ( $file === null || $file === '' )
                return $href;
            if ( !$image )
            {
                $id = call_user_func( $idOf, $file );
                if ( $id !== null && isset( $helpIds[$id] ) )
                    return 'help:' . $helpIds[$id] . ( preg_match( '/^[A-Za-z0-9_-]{1,100}$/', $anchor ) ? '#' . $anchor : '' );
            }
            $url = '/' . implode( '/', array_map( 'rawurlencode', explode( '/', $file ) ) ) . ( $anchor !== '' ? '#' . $anchor : '' );
            return $bracketed || strpos( $url, ' ' ) !== false ? '<' . $url . '>' : $url;
        };
        $out = array();
        $fence = null;
        foreach ( explode( "\n", str_replace( array( "\r\n", "\r" ), "\n", (string)$markdown ) ) as $line )
        {
            if ( preg_match( '/^\s{0,3}(`{3,}|~{3,})/', $line, $m ) )
            {
                if ( $fence === null )
                    $fence = $m[1][0];
                elseif ( $m[1][0] === $fence )
                    $fence = null;
                $out[] = $line;
                continue;
            }
            if ( $fence !== null || preg_match( '/^( {4}|\t)/', $line ) && trim( $line ) !== '' && !preg_match( '/^\s*([-*+]|\d+[.)])\s/', $line ) && !preg_match( '/\]\(/', $line ) )
            {
                $out[] = $line;
                continue;
            }
            // a reference definition: [label]: target
            if ( preg_match( '/^(\s{0,3}\[[^\]]+\]:\s*)(<[^>]*>|\S+)(.*)$/', $line, $m ) )
            {
                $out[] = $m[1] . $target( $m[2], (bool)preg_match( '/\.(png|jpe?g|gif|webp|svg)$/i', $m[2] ) ) . $m[3];
                continue;
            }
            // code spans keep their text
            $codes = array();
            $line = preg_replace_callback( '/(`+)(.+?)\1/', function ( $m ) use ( &$codes ) {
                $codes[] = $m[0];
                return "\x01" . ( count( $codes ) - 1 ) . "\x02";
            }, $line );
            $line = preg_replace_callback( '/(!?)\[((?:[^\[\]]|\[[^\]]*\])*)\]\(\s*(<[^>]*>|[^)\s]+)((?:\s+(?:"[^"]*"|\'[^\']*\'))?)\s*\)/',
                function ( $m ) use ( $target ) {
                    return $m[1] . '[' . $m[2] . '](' . $target( $m[3], $m[1] === '!' ) . $m[4] . ')';
                }, $line );
            $line = preg_replace_callback( "/\x01(\d+)\x02/", function ( $m ) use ( $codes ) {
                return $codes[(int)$m[1]];
            }, $line );
            $out[] = $line;
        }
        return implode( "\n", $out );
    }

    /**
     * The topic class of a page
     *
     * @param array $meta front matter
     * @param array $doc the index entry (type, title)
     * @param string $text the text without the title
     * @return string
     */
    public static function topicClass( array $meta, array $doc, $text )
    {
        $type = isset( $meta['topic_type'] ) && is_string( $meta['topic_type'] ) ? strtolower( trim( $meta['topic_type'] ) ) : '';
        if ( in_array( $type, array( 'concept', 'task', 'reference', 'troubleshooting' ), true ) )
            return 'help_' . $type;
        if ( isset( $doc['type'] ) && in_array( (string)$doc['type'], self::REFERENCE_TYPES, true ) )
            return 'help_reference';
        if ( isset( $doc['title'] ) && preg_match( '/^(how to|how do i)\b/i', (string)$doc['title'] ) && self::topicFields( 'help_task', $text ) !== null )
            return 'help_task';
        return 'help_concept';
    }

    /**
     * The fields of a topic class from a page's text, null when the text does not fit the class (a task needs exactly
     * one numbered list, troubleshooting the headings Condition and Remedy)
     *
     * @param string $class
     * @param string $text
     * @return array|null field => Markdown
     */
    public static function topicFields( $class, $text )
    {
        $text = trim( (string)$text );
        switch ( $class )
        {
            case 'help_concept':
            case 'help_reference':
                return array( 'body' => $text );
            case 'help_task':
                $lines = explode( "\n", $text );
                $blocks = array();
                $fence = false;
                $current = null;
                foreach ( $lines as $n => $line )
                {
                    if ( preg_match( '/^\s*(```|~~~)/', $line ) )
                        $fence = !$fence;
                    if ( $fence )
                    {
                        if ( $current !== null && !preg_match( '/^\s{2,}/', $line ) )
                            $current = null;
                        continue;
                    }
                    if ( preg_match( '/^\d{1,3}[.)]\s+/', $line ) )
                    {
                        if ( $current === null )
                        {
                            $blocks[] = array( $n, $n );
                            $current = count( $blocks ) - 1;
                        }
                        $blocks[$current][1] = $n;
                    }
                    elseif ( $current !== null && ( trim( $line ) === '' || preg_match( '/^\s{2,}\S/', $line ) ) )
                    {
                        if ( trim( $line ) !== '' )
                            $blocks[$current][1] = $n;
                    }
                    else
                        $current = null;
                }
                if ( count( $blocks ) !== 1 )
                    return null;
                list( $from, $to ) = $blocks[0];
                $fields = array(
                    'context' => trim( implode( "\n", array_slice( $lines, 0, $from ) ) ),
                    'steps' => trim( implode( "\n", array_slice( $lines, $from, $to - $from + 1 ) ) ),
                    'result' => trim( implode( "\n", array_slice( $lines, $to + 1 ) ) ),
                );
                if ( count( expHelpContentValidator::numberedLists( $fields['steps'] )['lists'] ) !== 1
                     || expHelpContentValidator::numberedLists( $fields['context'] )['lists']
                     || expHelpContentValidator::numberedLists( $fields['result'] )['lists'] )
                    return null;
                return array_filter( $fields, 'strlen' ) + array( 'steps' => $fields['steps'] );
            case 'help_troubleshooting':
                $sections = array();
                $name = '';
                foreach ( explode( "\n", $text ) as $line )
                {
                    // "## Condition" (Symptom, Problem), "## Cause" (Why), "## Remedy" (Solution, Fix, What to do) start
                    // the fields; any other heading stays in the field it is in
                    if ( preg_match( '/^#{2,3}\s+(.+?)\s*#*$/', $line, $m ) )
                    {
                        $heading = strtolower( trim( $m[1] ) );
                        $field = preg_match( '/^(condition|symptoms?|problem)$/', $heading ) ? 'condition'
                               : ( preg_match( '/^(causes?|why)$/', $heading ) ? 'cause'
                               : ( preg_match( '/^(remedy|solution|fix|what to do)$/', $heading ) ? 'remedy' : '' ) );
                        if ( $field !== '' )
                        {
                            $name = $field;
                            continue;
                        }
                    }
                    if ( $name !== '' )
                        $sections[$name][] = $line;
                }
                $fields = array();
                foreach ( $sections as $field => $part )
                    $fields[$field] = trim( implode( "\n", $part ) );
                if ( empty( $fields['condition'] ) || empty( $fields['remedy'] ) )
                    return null;
                return array_filter( $fields, 'strlen' );
        }
        return null;
    }

    /**
     * A short description of the style guide from a page's summary: one sentence, no markup, at most 250 characters,
     * ending with a full stop
     *
     * @param string $summary
     * @param string $title used when there is no summary
     * @return string
     */
    public static function shortdesc( $summary, $title )
    {
        $text = (string)$summary;
        $text = preg_replace( '/!?\[([^\]]*)\]\([^)]*\)/', '$1', $text );
        $text = preg_replace( '/\*\*|__|`|<[^>]*>/', '', $text );
        $text = trim( preg_replace( '/\s+/u', ' ', $text ) );
        if ( $text === '' )
            $text = trim( (string)$title );
        // the first sentence
        if ( preg_match( '/^(.+?[.!?]["\')\]]?)\s+(?=[\p{Lu}\p{N}"])/u', preg_replace( '/\b(e\.g|i\.e|etc|vs|approx|incl)\./i', "$1\x03", $text ), $m ) )
            $text = str_replace( "\x03", '.', $m[1] );
        $text = rtrim( $text, " …" );
        if ( mb_strlen( $text ) > expHelpContentValidator::SHORTDESC_MAX - 1 )
        {
            $text = mb_substr( $text, 0, expHelpContentValidator::SHORTDESC_MAX - 1 );
            $space = mb_strrpos( $text, ' ' );
            if ( $space !== false && $space > 100 )
                $text = mb_substr( $text, 0, $space );
            $text = rtrim( $text, ' ,;:-–' );
        }
        if ( !preg_match( '/[.!?]["\')\]]?$/u', $text ) )
            $text = rtrim( $text, '.' ) . '.';
        return $text;
    }

    /**
     * The audience options of the help's audiences of a page
     *
     * @param string[] $helpAudiences editor, admin, user ...
     * @return string[] option names, in the order of the options
     */
    public function audienceNames( array $helpAudiences )
    {
        $names = array();
        foreach ( $this->audienceOptions as $option )
        {
            $key = mb_strtolower( trim( (string)$option ) );
            if ( !isset( $this->audienceMap[$key] ) || !in_array( $this->audienceMap[$key], $helpAudiences, true ) )
                continue;
            // the first option of each help audience (visitors, not also members, for user)
            $taken = false;
            foreach ( $names as $name )
                $taken = $taken || $this->audienceMap[mb_strtolower( $name )] === $this->audienceMap[$key];
            if ( !$taken )
                $names[] = (string)$option;
        }
        return $names;
    }

    // ---------------------------------------------------------------- the plan

    /**
     * The plan of the manuals: every manual, chapter and topic as the database should have it, parents before their
     * children
     *
     * @param array $manuals list of parseManualLine()
     * @return array array( 'items' => remote_id => item, 'manuals' => key => remote_id, 'pages' => page id => help_id )
     *               item: kind (manual|chapter|topic), class, remote_id, parent (remote_id, '' = the folder Manuals),
     *               manual (key), page (page id of a topic), attributes, translations (locale => attributes),
     *               refs (remote_ids, manuals and chapters), section (identifier)
     */
    public function plan( array $manuals )
    {
        $this->notices = array();
        // the structure of each manual first, so every page that becomes a topic is known before a link is rewritten
        $structures = array();
        $owner = array();
        foreach ( $manuals as $manual )
        {
            $node = $this->source->tree( $manual['folder'] );
            $structures[$manual['key']] = array( 'manual' => $manual, 'node' => $node,
                                                 'entries' => $this->entries( $node, $manual['loose'], $owner, $manual['key'] ) );
        }
        $helpIds = self::helpIds( array_keys( $owner ) );

        $plan = array( 'items' => array(), 'manuals' => array(), 'pages' => $helpIds );
        foreach ( $structures as $key => $structure )
        {
            $manual = $structure['manual'];
            $remoteId = self::manualRemoteId( $key );
            $plan['manuals'][$key] = $remoteId;
            $item = array( 'kind' => 'manual', 'class' => 'help_manual', 'remote_id' => $remoteId, 'parent' => '', 'manual' => $key,
                           'page' => '', 'attributes' => array(), 'translations' => array(), 'refs' => array(), 'section' => '' );
            // the manual's place comes before its chapters and topics; its values follow when they are known
            $plan['items'][$remoteId] = $item;
            $topics = array();
            $item['refs'] = $this->addEntries( $plan, $structure['entries'], $remoteId, $key, $helpIds, $topics );
            $start = $this->startPage( $structure['node'], $structure['entries'] );
            $startPage = $start !== null ? $this->source->page( $start ) : null;
            $title = $manual['title'] !== '' ? $manual['title'] : self::folderTitle( $structure['node'], $startPage );
            $audiences = $startPage !== null ? $this->audienceNames( (array)$startPage['doc']['audience'] ) : array();
            if ( !$audiences )
            {
                foreach ( $topics as $topicId )
                    foreach ( explode( '|', $plan['items'][$topicId]['attributes']['audience'] ) as $name )
                        if ( $name !== '' && !in_array( $name, $audiences, true ) )
                            $audiences[] = $name;
                $audiences = array_values( array_intersect( $this->audienceOptions, $audiences ) );
            }
            $public = expHelpContentSchema::isPublicAudience( $audiences, $this->publicAudiences );
            $item['attributes'] = array(
                'title' => self::cut( $title, self::TITLE_MAX ),
                'shortdesc' => self::shortdesc( $startPage !== null ? $startPage['doc']['summary'] : '', $title ),
                'audience' => implode( '|', $audiences ),
                'edition' => $this->edition,
                'license' => $this->license,
            );
            $item['section'] = $public ? expHelpContentSchema::SECTION_PUBLIC : expHelpContentSchema::SECTION_INTERNAL;
            foreach ( $this->translations as $locale => $translation )
            {
                if ( !$this->hasTranslation( $plan, $item, $locale ) )
                    continue;
                $page = $start !== null ? $translation->page( $start ) : null;
                if ( isset( $this->translatedTitles[$key][$locale] ) )
                    $translatedTitle = $this->translatedTitles[$key][$locale];
                elseif ( $manual['title'] !== '' )
                    $translatedTitle = $title;
                else
                    $translatedTitle = self::folderTitle( $translation->tree( $manual['folder'] ), $page );
                $item['translations'][$locale] = array(
                    'title' => self::cut( $translatedTitle, self::TITLE_MAX ),
                    'shortdesc' => $page !== null ? self::shortdesc( $page['doc']['summary'], $translatedTitle ) : $item['attributes']['shortdesc'],
                    'license' => $this->license,
                );
            }
            $plan['items'][$remoteId] = $item;
            // a chapter of a public manual is public
            if ( $public )
                $this->publishChapters( $plan, $item['refs'] );
        }
        return $plan;
    }

    /**
     * The entries of a folder: its index page, then its pages and subfolders (chapters) in the help's order
     *
     * @param array $node expHelpDocumentIndex::tree()
     * @param bool $loose only the pages directly in it
     * @param array $owner page id => manual key of the pages already taken (a page is a topic once)
     * @param string $key
     * @return array list of array( 'topic', page id ) | array( 'chapter', node, entries )
     */
    protected function entries( array $node, $loose, array &$owner, $key )
    {
        $list = array();
        if ( !empty( $node['index'] ) && $this->takePage( $node['index'], $owner, $key ) )
            $list[] = array( 'kind' => 'topic', 'id' => $node['index'] );
        $children = array();
        foreach ( isset( $node['documents'] ) ? $node['documents'] : array() as $document )
            $children[] = array( 'kind' => 'topic', 'id' => $document['id'], 'order' => (int)$document['order'], 'title' => (string)$document['title'], 'rank' => 0 );
        if ( !$loose )
        {
            foreach ( isset( $node['children'] ) ? $node['children'] : array() as $child )
            {
                if ( $child['name'] === 'images' )
                    continue;
                $children[] = array( 'kind' => 'chapter', 'node' => $child, 'order' => (int)$child['order'], 'title' => (string)$child['title'], 'rank' => 1 );
            }
        }
        usort( $children, function ( $a, $b ) {
            return array( $a['order'], $a['rank'] ) <=> array( $b['order'], $b['rank'] ) ?: strnatcasecmp( $a['title'], $b['title'] );
        } );
        foreach ( $children as $child )
        {
            if ( $child['kind'] === 'topic' )
            {
                if ( $this->takePage( $child['id'], $owner, $key ) )
                    $list[] = array( 'kind' => 'topic', 'id' => $child['id'] );
                continue;
            }
            $entries = $this->entries( $child['node'], false, $owner, $key );
            if ( $entries )
                $list[] = array( 'kind' => 'chapter', 'node' => $child['node'], 'entries' => $entries );
        }
        return $list;
    }

    /**
     * Whether a page becomes a topic of this manual: a Markdown page not taken by an earlier manual
     */
    protected function takePage( $id, array &$owner, $key )
    {
        if ( isset( $owner[$id] ) )
        {
            if ( $owner[$id] !== $key )
                $this->notices[] = "$id is in the manual {$owner[$id]} already, not also in $key";
            return false;
        }
        if ( !$this->source->has( $id ) )
            return false;
        $owner[$id] = $key;
        return true;
    }

    /**
     * Adds the items of entries below a parent
     *
     * @return string[] the remote_ids of the entries, in order
     */
    protected function addEntries( array &$plan, array $entries, $parent, $key, array $helpIds, array &$topics )
    {
        $refs = array();
        foreach ( $entries as $entry )
        {
            if ( $entry['kind'] === 'topic' )
            {
                $topic = $this->topicItem( $entry['id'], $parent, $key, $helpIds );
                $plan['items'][$topic['remote_id']] = $topic;
                $refs[] = $topic['remote_id'];
                $topics[] = $topic['remote_id'];
                continue;
            }
            $node = $entry['node'];
            $remoteId = self::chapterRemoteId( $node['path'] );
            $index = !empty( $node['index'] ) ? $this->source->page( $node['index'] ) : null;
            $title = self::folderTitle( $node, null );
            $chapter = array( 'kind' => 'chapter', 'class' => 'help_section', 'remote_id' => $remoteId, 'parent' => $parent, 'manual' => $key,
                              'page' => '', 'refs' => array(), 'translations' => array(), 'section' => expHelpContentSchema::SECTION_INTERNAL,
                              'attributes' => array( 'title' => self::cut( $title, self::TITLE_MAX ),
                                                     'shortdesc' => $index !== null && trim( (string)$index['doc']['summary'] ) !== ''
                                                                  ? self::shortdesc( $index['doc']['summary'], $title ) : '' ) );
            $plan['items'][$remoteId] = $chapter;
            $plan['items'][$remoteId]['refs'] = $this->addEntries( $plan, $entry['entries'], $remoteId, $key, $helpIds, $topics );
            foreach ( $this->translations as $locale => $translation )
            {
                if ( !$this->hasTranslation( $plan, $plan['items'][$remoteId], $locale ) )
                    continue;
                $translated = !empty( $node['index'] ) ? $translation->page( $node['index'] ) : null;
                $translatedTitle = self::folderTitle( $translation->tree( $node['path'] ), $translated );
                $plan['items'][$remoteId]['translations'][$locale] = array(
                    'title' => self::cut( $translatedTitle, self::TITLE_MAX ),
                    'shortdesc' => $translated !== null && trim( (string)$translated['doc']['summary'] ) !== ''
                                 ? self::shortdesc( $translated['doc']['summary'], $translatedTitle ) : '',
                );
            }
            $refs[] = $remoteId;
        }
        return $refs;
    }

    /**
     * Whether a manual or chapter contains a topic translated into a language
     */
    protected function hasTranslation( array $plan, array $item, $locale )
    {
        foreach ( $item['refs'] as $ref )
        {
            $child = $plan['items'][$ref];
            if ( isset( $child['translations'][$locale] ) )
                return true;
            if ( $child['kind'] === 'chapter' && $this->hasTranslation( $plan, $child, $locale ) )
                return true;
        }
        return false;
    }

    protected function publishChapters( array &$plan, array $refs )
    {
        foreach ( $refs as $ref )
        {
            if ( $plan['items'][$ref]['kind'] !== 'chapter' )
                continue;
            $plan['items'][$ref]['section'] = expHelpContentSchema::SECTION_PUBLIC;
            $this->publishChapters( $plan, $plan['items'][$ref]['refs'] );
        }
    }

    /**
     * The topic of a page
     */
    protected function topicItem( $pageId, $parent, $key, array $helpIds )
    {
        $page = $this->source->page( $pageId );
        $doc = $page['doc'];
        $title = trim( (string)$doc['title'] ) !== '' ? (string)$doc['title'] : basename( $pageId );
        $text = self::rewriteLinks( self::withoutTitle( $page['body'] ), (string)$doc['path'], array( $this->source, 'idOf' ), $helpIds );
        $class = self::topicClass( (array)$page['meta'], $doc, $text );
        $fields = self::topicFields( $class, $text );
        if ( $fields === null )
        {
            $this->notices[] = "$pageId: front matter topic_type " . substr( $class, 5 ) . ', but the page does not have its parts; made a concept';
            $class = 'help_concept';
            $fields = self::topicFields( $class, $text );
        }
        $shortdesc = self::shortdesc( $doc['summary'], $title );
        foreach ( $fields as $field => $value )
        {
            if ( trim( $value ) === '' && in_array( $field, expHelpContentSchema::requiredFields( $class ), true ) )
                $fields[$field] = $shortdesc;
        }
        $audiences = $this->audienceNames( (array)$doc['audience'] );
        if ( !$audiences )
        {
            $this->notices[] = "$pageId: no audience option for " . implode( ', ', (array)$doc['audience'] ) . ' ([ContentSettings] AudienceMap[]); Administrators';
            $audiences = array( 'Administrators' );
        }
        $item = array(
            'kind' => 'topic', 'class' => $class, 'remote_id' => self::topicRemoteId( $pageId ), 'parent' => $parent, 'manual' => $key,
            'page' => $pageId, 'refs' => array(), 'translations' => array(),
            'section' => expHelpContentSchema::sectionForAudience( $audiences, $this->publicAudiences ),
            'attributes' => array(
                'title' => self::cut( $title, self::TITLE_MAX ),
                'shortdesc' => $shortdesc,
                'help_id' => $helpIds[$pageId],
                'audience' => implode( '|', $audiences ),
            ) + $fields,
        );
        foreach ( $this->translations as $locale => $translation )
        {
            $translated = $translation->page( $pageId );
            if ( $translated === null )
                continue;
            $translatedTitle = trim( (string)$translated['doc']['title'] ) !== '' ? (string)$translated['doc']['title'] : $title;
            $translatedText = self::rewriteLinks( self::withoutTitle( $translated['body'] ), (string)$translated['doc']['path'],
                                                  array( $translation, 'idOf' ), $helpIds );
            $translatedFields = self::topicFields( $class, $translatedText );
            if ( $translatedFields === null )
            {
                $this->notices[] = "$pageId ($locale): the translation does not have the parts of a " . substr( $class, 5 ) . '; not translated';
                continue;
            }
            $translatedShortdesc = self::shortdesc( $translated['doc']['summary'], $translatedTitle );
            foreach ( $translatedFields as $field => $value )
            {
                if ( trim( $value ) === '' && in_array( $field, expHelpContentSchema::requiredFields( $class ), true ) )
                    $translatedFields[$field] = $translatedShortdesc;
            }
            // every text field of the class, so a field the translation lacks is not the default language's text
            foreach ( array_keys( expHelpContentSchema::textFields( $class ) ) as $field )
                if ( !isset( $translatedFields[$field] ) && isset( $fields[$field] ) )
                    $translatedFields[$field] = '';
            $item['translations'][$locale] = array( 'title' => self::cut( $translatedTitle, self::TITLE_MAX ), 'shortdesc' => $translatedShortdesc ) + $translatedFields;
        }
        return $item;
    }

    /**
     * The page a manual starts with: its folder's README, else its start page (<name>-start-here.md), else none
     *
     * @return string|null page id
     */
    protected function startPage( array $node, array $entries )
    {
        if ( !empty( $node['index'] ) )
            return $node['index'];
        foreach ( isset( $node['documents'] ) ? $node['documents'] : array() as $document )
        {
            if ( preg_match( '/-start-here\.md$/', $document['id'] ) )
                return $document['id'];
        }
        return null;
    }

    /**
     * The title of a folder: its README's, else its start page's without ": start here", else its name
     *
     * @param array $node
     * @param array|null $startPage
     * @return string
     */
    public static function folderTitle( array $node, $startPage )
    {
        if ( !empty( $node['index'] ) && isset( $node['title'] ) && trim( (string)$node['title'] ) !== '' )
            return trim( (string)$node['title'] );
        if ( is_array( $startPage ) && trim( (string)$startPage['doc']['title'] ) !== '' )
        {
            $title = preg_replace( '/\s*[:–-]\s*(start here|hier (beginnen|anfangen|starten)|einstieg|erste schritte)\s*$/iu', '', trim( (string)$startPage['doc']['title'] ) );
            if ( $title !== '' )
                return $title;
        }
        return isset( $node['title'] ) && trim( (string)$node['title'] ) !== '' ? trim( (string)$node['title'] ) : 'Help';
    }

    protected static function cut( $text, $max )
    {
        $text = trim( preg_replace( '/\s+/u', ' ', (string)$text ) );
        return mb_strlen( $text ) > $max ? rtrim( mb_substr( $text, 0, $max - 1 ) ) . '…' : $text;
    }
}

?>
