<?php
/**
 * The DITA 1.3 export of help maintained as content (doc/eng-us/specifications/6.0/help-system.md, "The standard: what
 * DITA means here" and delivery paths 3 and 4; doc/eng-us/features/6.0/help-content.md, "DITA export").
 *
 * The mapping:
 *
 *   help_concept, help_task, help_reference,   one topic of that DITA type per file, topics/<help_id>.dita; the id of the
 *   help_troubleshooting                       topic is its help_id, so are its file name and its resourceid appid
 *   title, shortdesc                           title, shortdesc
 *   audience, platform, version_from           the filter attributes audience, platform (space separated, lower case,
 *                                              blanks as hyphens) and rev; the validity, review date and help_id are
 *                                              also in the prolog (critdates, othermeta, resourceid)
 *   body (concept)                             conbody, a heading opens a section
 *   body (reference)                           refbody, every block in a section
 *   context, prereq, steps, result, postreq    taskbody: context, prereq, steps (exactly one numbered list; each
 *                                              item a step with cmd and info), else steps-informal, result, postreq
 *   condition, cause, remedy                   troublebody: condition, troubleSolution with cause and remedy (steps or
 *                                              steps-informal)
 *   figures (help_screenshot)                  fig with title (caption) and image with alt, in the element of the
 *                                              field the figure names; the files go to images/
 *   related                                    related-links
 *   help_manual                                a bookmap, <remote_id>.ditamap: booktitle, bookmeta (shortdesc,
 *                                              authors, dates, edition, license), chapters, and a DITAVAL file
 *                                              <remote_id>.ditaval that keeps the manual's audiences (the edition for
 *                                              its audiences)
 *   help_section                               a chapter (chapters/<remote_id>.dita, a concept with the chapter's title
 *                                              and short description), its topics as topicrefs below it
 *   help_glossary_entry                        glossentry (glossary/<term>.dita): glossterm, glossdef, the synonyms
 *                                              to avoid as glossAlt/glossSynonym with glossStatus "prohibited", the
 *                                              note
 *   (the whole export)                         help-pool.ditamap: every exported topic by type, and the glossary
 *
 * Only Released objects the user may read go into an export, as into an e-book: a preview never does. Links between
 * exported topics stay inside the export; a link to a topic outside it goes to the topic's address in the help.
 *
 * validate() checks an export: every file well-formed, the DITA 1.3 DOCTYPE of its root element, unique topic ids, and
 * every reference of a map, a topic or an image resolves to a file of the export.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentDita
{
    /** Root element => array( public id, system id ) of DITA 1.3 */
    const DOCTYPES = array(
        'concept' => array( '-//OASIS//DTD DITA 1.3 Concept//EN', 'concept.dtd' ),
        'task' => array( '-//OASIS//DTD DITA 1.3 Task//EN', 'task.dtd' ),
        // the root element task too: the general task, whose steps may be informal (a task whose steps are not one list)
        'generalTask' => array( '-//OASIS//DTD DITA 1.3 General Task//EN', 'generalTask.dtd' ),
        'reference' => array( '-//OASIS//DTD DITA 1.3 Reference//EN', 'reference.dtd' ),
        'troubleshooting' => array( '-//OASIS//DTD DITA 1.3 Troubleshooting//EN', 'troubleshooting.dtd' ),
        'glossentry' => array( '-//OASIS//DTD DITA 1.3 Glossary Entry//EN', 'glossentry.dtd' ),
        'map' => array( '-//OASIS//DTD DITA 1.3 Map//EN', 'map.dtd' ),
        'bookmap' => array( '-//OASIS//DTD DITA 1.3 BookMap//EN', 'bookmap.dtd' ),
    );

    /** The map of the whole export */
    const POOL_MAP = 'help-pool.ditamap';

    /** The directories of an export (an export removes only files of these and maps/DITAVAL files of its root) */
    const DIRECTORIES = array( 'topics', 'chapters', 'glossary', 'images' );

    /** @var expHelpContentProvider */
    protected $provider;

    /** @var string|null the remote_id of the one manual to export, null for everything */
    protected $manualKey;

    /** @var array path => XML */
    protected $files = array();

    /** @var array path => source file (figures) */
    protected $copies = array();

    /** @var array help_id => topic (the topics of the export) */
    protected $topics = array();

    /** @var array figure object id => path in the export */
    protected $figurePaths = array();

    /** @var array counts */
    protected $counts = array( 'manuals' => 0, 'chapters' => 0, 'topics' => 0, 'figures' => 0, 'glossary' => 0 );

    /** @var callable|null function( help_id ): string, the address of a topic outside the export */
    protected $topicUrl;

    /**
     * @param expHelpContentProvider $provider decides what the user may read
     * @param string|null $manual the remote_id of one manual, null: every manual, the whole topic pool and the glossary
     * @param callable|null $topicUrl the address of a topic outside the export (default: its page in the help)
     */
    public function __construct( expHelpContentProvider $provider, $manual = null, $topicUrl = null )
    {
        $this->provider = $provider;
        $this->manualKey = $manual === null || $manual === '' ? null : (string)$manual;
        $this->topicUrl = $topicUrl;
    }

    /** @return bool whether this installation can make the download (a zip of the export) */
    public static function canZip()
    {
        return class_exists( 'ZipArchive' );
    }

    /**
     * Builds the export in memory
     *
     * @return array array( 'files' => path => XML, 'copies' => path => source file, 'counts' => array )
     * @throws RuntimeException when the manual asked for is not there or not Released
     */
    public function build()
    {
        $this->files = $this->copies = $this->topics = $this->figurePaths = array();
        $this->counts = array( 'manuals' => 0, 'chapters' => 0, 'topics' => 0, 'figures' => 0, 'glossary' => 0 );
        $manuals = array();
        if ( $this->manualKey !== null )
        {
            $manual = $this->provider->manual( $this->manualKey );
            if ( $manual === null || !self::delivered( $manual ) )
                throw new RuntimeException( 'No Released manual with that remote_id the user may read' );
            $manuals[] = $manual;
        }
        else
        {
            foreach ( $this->provider->manuals() as $manual )
            {
                if ( self::delivered( $manual ) )
                    $manuals[] = $manual;
            }
        }

        // the structure of each manual first: it names the topics of a one-manual export
        $structures = array();
        foreach ( $manuals as $manual )
            $structures[$manual['remote_id']] = $this->collect( $manual, 1, array( (int)$manual['object_id'] ) );
        if ( $this->manualKey === null )
        {
            foreach ( $this->provider->topics() as $topic )
            {
                if ( self::delivered( $topic ) && $topic['help_id'] !== '' )
                    $this->topics[$topic['help_id']] = $topic;
            }
        }
        ksort( $this->topics );

        foreach ( $this->topics as $helpId => $topic )
            $this->files[self::topicPath( $helpId )] = $this->topicXml( $topic );
        foreach ( $manuals as $manual )
        {
            $this->files[self::fileName( $manual['remote_id'] ) . '.ditamap'] = $this->bookmapXml( $manual, $structures[$manual['remote_id']] );
            $this->files[self::fileName( $manual['remote_id'] ) . '.ditaval'] = self::ditaval( $manual['audience'] );
            $this->counts['manuals']++;
        }
        if ( $this->manualKey === null )
        {
            $glossary = $this->glossary();
            $this->files[self::POOL_MAP] = $this->poolMapXml( $glossary );
        }
        $this->counts['topics'] = count( $this->topics );
        $this->counts['figures'] = count( $this->copies );
        ksort( $this->files );
        ksort( $this->copies );
        return array( 'files' => $this->files, 'copies' => $this->copies, 'counts' => $this->counts );
    }

    /**
     * Whether an object of the help is delivered: Released, and readable (not a preview)
     *
     * @param array $item
     * @return bool
     */
    public static function delivered( array $item )
    {
        return empty( $item['preview'] ) && isset( $item['state'] ) && $item['state'] === expHelpContentSchema::STATE_RELEASED;
    }

    /**
     * The chapters and topics of a map, nested, Released and readable only
     *
     * @return array list of array( kind chapter|topic, item, children )
     */
    protected function collect( array $map, $level, array $seen )
    {
        $entries = array();
        if ( $level > 8 )
            return $entries;
        foreach ( $map['topicrefs'] as $objectId )
        {
            $objectId = (int)$objectId;
            if ( in_array( $objectId, $seen, true ) )
                continue;
            $found = $this->provider->repository()->byObjectId( $objectId );
            if ( $found === null )
                continue;
            $item = $this->provider->visible( $found['item'] );
            if ( $item === null || !self::delivered( $item ) )
                continue;
            if ( $found['kind'] === 'maps' && $item['class'] === 'help_section' )
            {
                $path = 'chapters/' . self::fileName( $item['remote_id'] ) . '.dita';
                if ( !isset( $this->files[$path] ) )
                {
                    $this->files[$path] = $this->chapterXml( $item );
                    $this->counts['chapters']++;
                }
                $entries[] = array( 'kind' => 'chapter', 'item' => $item, 'path' => $path,
                                    'children' => $this->collect( $item, $level + 1, array_merge( $seen, array( $objectId ) ) ) );
            }
            elseif ( $found['kind'] === 'topics' && $item['help_id'] !== '' )
            {
                $this->topics[$item['help_id']] = $item;
                $entries[] = array( 'kind' => 'topic', 'item' => $item, 'path' => self::topicPath( $item['help_id'] ), 'children' => array() );
            }
        }
        return $entries;
    }

    /** @return string the path of a topic in an export */
    public static function topicPath( $helpId )
    {
        return 'topics/' . self::fileName( $helpId ) . '.dita';
    }

    /**
     * A file name (and DITA id) from a help_id, remote_id or term: a-z 0-9 . _ -, starting with a letter
     *
     * @param string $key
     * @return string
     */
    public static function fileName( $key )
    {
        $name = strtolower( trim( preg_replace( '/[^A-Za-z0-9._-]+/', '-', (string)$key ), '-.' ) );
        if ( $name === '' || !preg_match( '/^[a-z]/', $name ) )
            $name = 'x-' . $name;
        return substr( $name, 0, 120 );
    }

    /**
     * Filter attribute values: lower case, blanks as hyphens, space separated
     *
     * @param string[] $names
     * @return string
     */
    public static function filterValue( array $names )
    {
        $values = array();
        foreach ( $names as $name )
        {
            $value = trim( preg_replace( '/[^a-z0-9_.-]+/', '-', mb_strtolower( trim( (string)$name ) ) ), '-' );
            if ( $value !== '' && !in_array( $value, $values, true ) )
                $values[] = $value;
        }
        return implode( ' ', $values );
    }

    // ---------------------------------------------------------------- topics

    /**
     * The DITA topic of a help topic
     *
     * @param array $topic
     * @return string XML
     */
    public function topicXml( array $topic )
    {
        $type = $topic['type'];
        $doctype = $type;
        $doc = new DOMDocument( '1.0', 'UTF-8' );
        $markup = new expHelpDitaMarkup( $doc, function ( $helpId ) {
            return $this->topicLink( $helpId );
        } );
        $root = $markup->el( $type, array(
            'id' => self::fileName( $topic['help_id'] ),
            'audience' => self::filterValue( $topic['audience'] ),
            'platform' => self::filterValue( $topic['platform'] ),
            'rev' => $topic['version_from'] !== '' ? $topic['version_from'] : null,
        ) );
        self::setLang( $root, $topic['language'] );
        $doc->appendChild( $root );
        $root->appendChild( $markup->el( 'title', array(), $topic['title'] !== '' ? $topic['title'] : $topic['help_id'] ) );
        if ( $topic['shortdesc'] !== '' )
            $root->appendChild( $markup->el( 'shortdesc', array(), $topic['shortdesc'] ) );
        $root->appendChild( $this->prolog( $markup, $topic ) );

        $html = array();
        foreach ( array_keys( expHelpContentSchema::textFields( $topic['class'] ) ) as $field )
        {
            $text = isset( $topic['fields'][$field] ) ? trim( (string)$topic['fields'][$field] ) : '';
            $html[$field] = $text === '' ? '' : $this->fieldHtml( $topic, $text );
        }
        $figures = $this->figures( $markup, $topic );

        switch ( $type )
        {
            case 'concept':
            case 'reference':
                $body = $markup->el( $type === 'concept' ? 'conbody' : 'refbody' );
                $markup->appendBlocks( $body, $html['body'], $type === 'concept' ? 'sections' : 'refsections' );
                if ( $figures )
                {
                    $section = $markup->el( 'section', array( 'outputclass' => 'figures' ) );
                    foreach ( $figures as $figure )
                        $section->appendChild( $figure[1] );
                    $body->appendChild( $section );
                }
                $root->appendChild( $body );
                break;
            case 'task':
                $body = $markup->el( 'taskbody' );
                $parts = array();
                foreach ( array( 'context', 'prereq', 'result', 'postreq' ) as $field )
                {
                    if ( $html[$field] !== '' )
                    {
                        $parts[$field] = $markup->el( $field );
                        $markup->appendBlocks( $parts[$field], $html[$field], 'flat' );
                    }
                }
                $example = null;
                foreach ( $figures as $figure )
                {
                    $field = in_array( $figure[0], array( 'context', 'prereq', 'result', 'postreq' ), true ) ? $figure[0] : '';
                    if ( $field !== '' )
                    {
                        if ( !isset( $parts[$field] ) )
                            $parts[$field] = $markup->el( $field );
                        $parts[$field]->appendChild( $figure[1] );
                        continue;
                    }
                    if ( $example === null )
                        $example = $markup->el( 'example', array( 'outputclass' => 'figures' ) );
                    $example->appendChild( $figure[1] );
                }
                // prereq before context: the order of the (strict) DITA 1.3 task
                foreach ( array( 'prereq', 'context' ) as $field )
                {
                    if ( isset( $parts[$field] ) )
                        $body->appendChild( $parts[$field] );
                }
                if ( $html['steps'] !== '' )
                {
                    $steps = $markup->steps( $html['steps'] );
                    // steps that are not one numbered list: only the general task allows steps-informal
                    if ( $steps->nodeName === 'steps-informal' )
                        $doctype = 'generalTask';
                    $body->appendChild( $steps );
                }
                if ( isset( $parts['result'] ) )
                    $body->appendChild( $parts['result'] );
                if ( $example !== null )
                    $body->appendChild( $example );
                if ( isset( $parts['postreq'] ) )
                    $body->appendChild( $parts['postreq'] );
                $root->appendChild( $body );
                break;
            case 'troubleshooting':
                $body = $markup->el( 'troublebody' );
                $condition = $html['condition'] !== '' ? $markup->el( 'condition' ) : null;
                if ( $condition !== null )
                    $markup->appendBlocks( $condition, $html['condition'], 'flat' );
                $cause = $html['cause'] !== '' ? $markup->el( 'cause' ) : null;
                if ( $cause !== null )
                    $markup->appendBlocks( $cause, $html['cause'], 'flat' );
                $remedy = null;
                if ( $html['remedy'] !== '' )
                {
                    $remedy = $markup->el( 'remedy' );
                    $remedy->appendChild( $markup->steps( $html['remedy'] ) );
                }
                foreach ( $figures as $figure )
                {
                    if ( $figure[0] === 'cause' )
                    {
                        if ( $cause === null )
                            $cause = $markup->el( 'cause' );
                        $cause->appendChild( $figure[1] );
                    }
                    elseif ( $figure[0] === 'remedy' && $remedy !== null && $remedy->firstChild->nodeName === 'steps-informal' )
                        $remedy->firstChild->appendChild( $figure[1] );
                    else
                    {
                        if ( $condition === null )
                            $condition = $markup->el( 'condition' );
                        $condition->appendChild( $figure[1] );
                    }
                }
                if ( $condition !== null )
                    $body->appendChild( $condition );
                $solution = $markup->el( 'troubleSolution' );
                if ( $cause !== null )
                    $solution->appendChild( $cause );
                if ( $remedy !== null )
                    $solution->appendChild( $remedy );
                $body->appendChild( $solution );
                $root->appendChild( $body );
                break;
        }

        $links = $this->relatedLinks( $markup, $topic );
        if ( $links !== null )
            $root->appendChild( $links );
        return self::serialize( $doc, $doctype );
    }

    /**
     * A field's Markdown as the renderer's HTML, with links to other topics marked for the export
     */
    protected function fieldHtml( array $topic, $markdown )
    {
        $renderer = new expHelpMarkdownRenderer( array(
            'id_prefix' => self::fileName( $topic['help_id'] ) . '-',
            'link_resolver' => function ( $href ) {
                if ( preg_match( '#^/?content/([a-z0-9.-]{1,80})\.md(?:\#.*)?$#', (string)$href, $m ) )
                    return expHelpDitaMarkup::TOPIC_LINK_PREFIX . $m[1];
                $link = expHelpLinks::link( $href, '' );
                return is_string( $link ) && $link !== '' ? self::absolute( $link ) : null;
            },
            'image_resolver' => function ( $src ) {
                $image = expHelpLinks::image( $src, '' );
                if ( is_array( $image ) )
                    $image = isset( $image['url'] ) ? $image['url'] : '';
                return is_string( $image ) && $image !== '' ? self::absolute( $image ) : null;
            } ) );
        $rendered = $renderer->render( expHelpContentProvider::linksRewritten( $markdown ), '' );
        return (string)$rendered['html'];
    }

    /**
     * Where a link of a topic to another topic goes: inside the export (a path relative to topics/), else to the
     * topic's page in the help
     *
     * @param string $helpId
     * @return array|null array( href, scope local|external )
     */
    public function topicLink( $helpId )
    {
        if ( isset( $this->topics[$helpId] ) )
            return array( 'href' => self::fileName( $helpId ) . '.dita', 'scope' => 'local' );
        $url = is_callable( $this->topicUrl ) ? call_user_func( $this->topicUrl, $helpId ) : self::absolute( expHelp::url( 'topic', $helpId ) );
        return is_string( $url ) && $url !== '' ? array( 'href' => $url, 'scope' => 'external' ) : null;
    }

    protected static function absolute( $url )
    {
        if ( preg_match( '#^https?://#i', (string)$url ) )
            return (string)$url;
        return class_exists( 'eZURI' ) ? expHelpLinks::absolute( (string)$url ) : (string)$url;
    }

    protected function prolog( expHelpDitaMarkup $markup, array $topic )
    {
        $prolog = $markup->el( 'prolog' );
        $dates = $markup->el( 'critdates' );
        if ( !empty( $topic['published'] ) )
            $dates->appendChild( $markup->el( 'created', array( 'date' => gmdate( 'Y-m-d', (int)$topic['published'] ) ) ) );
        if ( !empty( $topic['modified'] ) )
            $dates->appendChild( $markup->el( 'revised', array( 'modified' => gmdate( 'Y-m-d', (int)$topic['modified'] ) ) ) );
        if ( $dates->hasChildNodes() )
            $prolog->appendChild( $dates );
        $metadata = $markup->el( 'metadata' );
        foreach ( $topic['audience'] as $audience )
            $metadata->appendChild( $markup->el( 'audience', array( 'type' => 'other', 'othertype' => self::filterValue( array( $audience ) ) ) ) );
        $meta = array( 'help_id' => $topic['help_id'], 'type' => $topic['type'], 'version_from' => $topic['version_from'],
                       'version_to' => $topic['version_to'],
                       'review_date' => !empty( $topic['review_date'] ) ? gmdate( 'Y-m-d', (int)$topic['review_date'] ) : '' );
        foreach ( $meta as $name => $value )
        {
            if ( (string)$value !== '' )
                $metadata->appendChild( $markup->el( 'othermeta', array( 'name' => $name, 'content' => $value ) ) );
        }
        $prolog->appendChild( $metadata );
        $prolog->appendChild( $markup->el( 'resourceid', array( 'appname' => 'exphelp', 'appid' => $topic['help_id'] ) ) );
        return $prolog;
    }

    /**
     * The topic's figures: list of array( field, fig element ); their files go to images/
     */
    protected function figures( expHelpDitaMarkup $markup, array $topic )
    {
        $list = array();
        foreach ( $this->provider->relations( $topic )['figures'] as $figure )
        {
            if ( !self::delivered( $figure + array( 'preview' => false ) ) )
                continue;
            $id = (int)$figure['object_id'];
            if ( !isset( $this->figurePaths[$id] ) )
            {
                $source = $this->figureFile( $figure );
                if ( $source === null )
                    continue;
                $media = expHelpContentExport::imageMedia( $source );
                if ( $media === null )
                    continue;
                $base = $figure['figure_id'] !== '' ? self::fileName( $figure['figure_id'] ) : 'figure-' . $id;
                $path = 'images/' . $base . '.' . $media[1];
                for ( $n = 2; isset( $this->copies[$path] ) && $this->copies[$path] !== $source; $n++ )
                    $path = 'images/' . $base . '-' . $n . '.' . $media[1];
                $this->copies[$path] = $source;
                $this->figurePaths[$id] = $path;
            }
            $fig = $markup->el( 'fig', array( 'id' => $figure['figure_id'] !== '' ? 'fig-' . self::fileName( $figure['figure_id'] ) : 'fig-' . $id ) );
            if ( $figure['caption'] !== '' )
                $fig->appendChild( $markup->el( 'title', array(), $figure['caption'] ) );
            $image = $markup->el( 'image', array( 'href' => '../' . $this->figurePaths[$id], 'placement' => 'break' ) );
            $image->appendChild( $markup->el( 'alt', array(), $figure['alt_text'] ) );
            $fig->appendChild( $image );
            $list[] = array( (string)$figure['topic_field'], $fig );
        }
        return $list;
    }

    /**
     * The local file of a figure's image (overridable for tests)
     *
     * @param array $figure
     * @return string|null
     */
    protected function figureFile( array $figure )
    {
        return expHelpContentExport::storageFile( $figure['image_url'] );
    }

    protected function relatedLinks( expHelpDitaMarkup $markup, array $topic )
    {
        $links = $markup->el( 'related-links' );
        foreach ( $this->provider->relations( $topic )['related'] as $item )
        {
            if ( !self::delivered( $item ) || $item['help_id'] === '' )
                continue;
            $target = $this->topicLink( $item['help_id'] );
            if ( $target === null )
                continue;
            $link = $markup->el( 'link', $target['scope'] === 'local' ? array( 'href' => $target['href'], 'format' => 'dita' )
                                                                       : array( 'href' => $target['href'], 'scope' => 'external', 'format' => 'html' ) );
            $link->appendChild( $markup->el( 'linktext', array(), $item['title'] !== '' ? $item['title'] : $item['help_id'] ) );
            if ( $item['shortdesc'] !== '' )
                $link->appendChild( $markup->el( 'desc', array(), $item['shortdesc'] ) );
            $links->appendChild( $link );
        }
        return $links->hasChildNodes() ? $links : null;
    }

    // ---------------------------------------------------------------- maps

    /**
     * A chapter as a concept topic: its title and short description
     */
    public function chapterXml( array $chapter )
    {
        $doc = new DOMDocument( '1.0', 'UTF-8' );
        $markup = new expHelpDitaMarkup( $doc );
        $root = $markup->el( 'concept', array( 'id' => self::fileName( $chapter['remote_id'] ), 'outputclass' => 'chapter' ) );
        self::setLang( $root, isset( $chapter['language'] ) ? $chapter['language'] : '' );
        $doc->appendChild( $root );
        $root->appendChild( $markup->el( 'title', array(), $chapter['title'] !== '' ? $chapter['title'] : $chapter['name'] ) );
        if ( $chapter['shortdesc'] !== '' )
            $root->appendChild( $markup->el( 'shortdesc', array(), $chapter['shortdesc'] ) );
        $root->appendChild( $markup->el( 'conbody' ) );
        return self::serialize( $doc, 'concept' );
    }

    /**
     * The bookmap of a manual
     *
     * @param array $manual
     * @param array $entries collect()
     * @return string XML
     */
    public function bookmapXml( array $manual, array $entries )
    {
        $doc = new DOMDocument( '1.0', 'UTF-8' );
        $markup = new expHelpDitaMarkup( $doc );
        $root = $markup->el( 'bookmap', array( 'id' => self::fileName( $manual['remote_id'] ), 'audience' => self::filterValue( $manual['audience'] ) ) );
        self::setLang( $root, $manual['language'] );
        $doc->appendChild( $root );
        $title = $markup->el( 'booktitle', array(), array( $markup->el( 'mainbooktitle', array(), $manual['title'] !== '' ? $manual['title'] : $manual['name'] ) ) );
        if ( $manual['subtitle'] !== '' )
            $title->appendChild( $markup->el( 'booktitlealt', array(), $manual['subtitle'] ) );
        $root->appendChild( $title );

        $meta = $markup->el( 'bookmeta' );
        if ( $manual['shortdesc'] !== '' )
            $meta->appendChild( $markup->el( 'shortdesc', array(), $manual['shortdesc'] ) );
        foreach ( $manual['authors'] as $author )
            $meta->appendChild( $markup->el( 'author', array(), $author ) );
        $dates = $markup->el( 'critdates' );
        $created = !empty( $manual['date'] ) ? (int)$manual['date'] : (int)$manual['published'];
        if ( $created > 0 )
            $dates->appendChild( $markup->el( 'created', array( 'date' => gmdate( 'Y-m-d', $created ) ) ) );
        if ( !empty( $manual['modified'] ) )
            $dates->appendChild( $markup->el( 'revised', array( 'modified' => gmdate( 'Y-m-d', (int)$manual['modified'] ) ) ) );
        if ( $dates->hasChildNodes() )
            $meta->appendChild( $dates );
        foreach ( $manual['audience'] as $audience )
            $meta->appendChild( $markup->el( 'audience', array( 'type' => 'other', 'othertype' => self::filterValue( array( $audience ) ) ) ) );
        if ( $manual['license'] !== '' )
            $meta->appendChild( $markup->el( 'othermeta', array( 'name' => 'license', 'content' => $manual['license'] ) ) );
        $meta->appendChild( $markup->el( 'othermeta', array( 'name' => 'ditaval', 'content' => self::fileName( $manual['remote_id'] ) . '.ditaval' ) ) );
        $meta->appendChild( $markup->el( 'resourceid', array( 'appname' => 'exphelp', 'appid' => $manual['remote_id'] ) ) );
        if ( $manual['edition'] !== '' )
            $meta->appendChild( $markup->el( 'bookid', array(), array( $markup->el( 'edition', array(), $manual['edition'] ) ) ) );
        $root->appendChild( $meta );
        $root->appendChild( $markup->el( 'frontmatter', array(), array( $markup->el( 'booklists', array(), array( $markup->el( 'toc' ) ) ) ) ) );
        foreach ( $entries as $entry )
            $root->appendChild( $this->mapRef( $markup, $entry, 'chapter' ) );
        return self::serialize( $doc, 'bookmap' );
    }

    protected function mapRef( expHelpDitaMarkup $markup, array $entry, $element )
    {
        $ref = $markup->el( $element, array( 'href' => $entry['path'] ) );
        foreach ( $entry['children'] as $child )
            $ref->appendChild( $this->mapRef( $markup, $child, 'topicref' ) );
        return $ref;
    }

    /**
     * The glossary entries of the whole export: Released and readable; path => entry
     */
    protected function glossary()
    {
        $list = array();
        foreach ( $this->provider->repository()->load()['glossary'] as $entry )
        {
            $entry = $this->provider->visible( $entry );
            if ( $entry === null || !self::delivered( $entry ) || $entry['term'] === '' )
                continue;
            $path = 'glossary/' . self::fileName( $entry['term'] ) . '.dita';
            for ( $n = 2; isset( $list[$path] ); $n++ )
                $path = 'glossary/' . self::fileName( $entry['term'] ) . '-' . $n . '.dita';
            $list[$path] = $entry;
            $this->files[$path] = $this->glossentryXml( $entry, basename( $path, '.dita' ) );
            $this->counts['glossary']++;
        }
        return $list;
    }

    public function glossentryXml( array $entry, $id )
    {
        $doc = new DOMDocument( '1.0', 'UTF-8' );
        $markup = new expHelpDitaMarkup( $doc );
        $root = $markup->el( 'glossentry', array( 'id' => $id, 'audience' => self::filterValue( $entry['audience'] ) ) );
        self::setLang( $root, $entry['language'] );
        $doc->appendChild( $root );
        $root->appendChild( $markup->el( 'glossterm', array(), $entry['term'] ) );
        $definition = trim( preg_replace( '/\s+/u', ' ', expHelpSearch::plain( (string)$entry['definition'] ) ) );
        if ( $definition !== '' )
            $root->appendChild( $markup->el( 'glossdef', array(), $definition ) );
        $body = $markup->el( 'glossBody' );
        $note = trim( (string)$entry['note'] );
        if ( $note !== '' )
            $body->appendChild( $markup->el( 'note', array(), trim( preg_replace( '/\s+/u', ' ', expHelpSearch::plain( $note ) ) ) ) );
        foreach ( $entry['avoid'] as $word )
        {
            $alt = $markup->el( 'glossAlt' );
            $alt->appendChild( $markup->el( 'glossSynonym', array(), $word ) );
            $alt->appendChild( $markup->el( 'glossStatus', array( 'value' => 'prohibited' ) ) );
            $body->appendChild( $alt );
        }
        if ( $body->hasChildNodes() )
            $root->appendChild( $body );
        return self::serialize( $doc, 'glossentry' );
    }

    /**
     * The map of the whole export: the topics by type, then the glossary
     */
    protected function poolMapXml( array $glossary )
    {
        $doc = new DOMDocument( '1.0', 'UTF-8' );
        $markup = new expHelpDitaMarkup( $doc );
        $root = $markup->el( 'map', array( 'id' => 'help-pool' ) );
        $first = reset( $this->topics );
        self::setLang( $root, $first ? $first['language'] : '' );
        $doc->appendChild( $root );
        $root->appendChild( $markup->el( 'title', array(), 'Topic pool' ) );
        $names = array( 'concept' => 'Concepts', 'task' => 'Tasks', 'reference' => 'Reference', 'troubleshooting' => 'Troubleshooting' );
        foreach ( $names as $type => $name )
        {
            $head = $markup->el( 'topichead', array(), array( $markup->el( 'topicmeta', array(), array( $markup->el( 'navtitle', array(), $name ) ) ) ) );
            foreach ( $this->topics as $helpId => $topic )
            {
                if ( $topic['type'] === $type )
                    $head->appendChild( $markup->el( 'topicref', array( 'href' => self::topicPath( $helpId ), 'type' => $type ) ) );
            }
            if ( $head->childNodes->length > 1 )
                $root->appendChild( $head );
        }
        if ( $glossary )
        {
            $head = $markup->el( 'topichead', array(), array( $markup->el( 'topicmeta', array(), array( $markup->el( 'navtitle', array(), 'Glossary' ) ) ) ) );
            foreach ( array_keys( $glossary ) as $path )
                $head->appendChild( $markup->el( 'topicref', array( 'href' => $path, 'type' => 'glossentry' ) ) );
            $root->appendChild( $head );
        }
        return self::serialize( $doc, 'map' );
    }

    /**
     * The DITAVAL of a manual: its audiences are kept, every other audience option is left out
     *
     * @param string[] $audiences
     * @param string[]|null $all the audience options (default exphelp.ini [ContentSettings] Audiences[])
     * @return string XML
     */
    public static function ditaval( array $audiences, ?array $all = null )
    {
        $keep = $audiences ? explode( ' ', self::filterValue( $audiences ) ) : array();
        $props = array();
        foreach ( explode( ' ', self::filterValue( $all === null ? expHelpContentSchema::audiences() : $all ) ) as $value )
        {
            if ( $value === '' )
                continue;
            $action = !$keep || in_array( $value, $keep, true ) ? 'include' : 'exclude';
            $props[] = '  <prop att="audience" val="' . htmlspecialchars( $value, ENT_QUOTES | ENT_XML1, 'UTF-8' ) . '" action="' . $action . '"/>';
        }
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<val>' . "\n" . implode( "\n", $props ) . ( $props ? "\n" : '' ) . '</val>' . "\n";
    }

    // ---------------------------------------------------------------- output

    /**
     * A DITA document with its XML declaration and DOCTYPE
     *
     * @param DOMDocument $doc
     * @param string $doctype a key of DOCTYPES (generalTask: a task with steps-informal)
     * @return string
     */
    public static function serialize( DOMDocument $doc, $doctype )
    {
        // indented, one element per line where an element holds only elements (libxml leaves mixed content as it is),
        // so the history of an export in git shows changes line by line
        $doc->formatOutput = true;
        $body = $doc->saveXML( $doc->documentElement );
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
             . '<!DOCTYPE ' . $doc->documentElement->nodeName . ' PUBLIC "' . self::DOCTYPES[$doctype][0] . '" "' . self::DOCTYPES[$doctype][1] . '">' . "\n"
             . $body . "\n";
    }

    protected static function setLang( DOMElement $element, $locale )
    {
        $element->setAttributeNS( 'http://www.w3.org/XML/1998/namespace', 'xml:lang', expHelpContentExport::xmlLang( (string)$locale ) );
    }

    /**
     * Writes an export into a directory: changed files only, and files of an earlier export that are not in this one
     * are removed (only in the export's directories and its root's .ditamap and .ditaval files; a .git directory or
     * anything else there is left alone)
     *
     * @param array $export build()
     * @param string $dir
     * @param bool $dryRun
     * @return array array( 'written' => paths, 'unchanged' => int, 'removed' => paths )
     */
    public static function write( array $export, $dir, $dryRun = false )
    {
        $dir = rtrim( (string)$dir, '/' );
        $result = array( 'written' => array(), 'unchanged' => 0, 'removed' => array() );
        $wanted = array();
        foreach ( $export['files'] as $path => $xml )
        {
            $wanted[$path] = true;
            $file = $dir . '/' . $path;
            if ( is_file( $file ) && (string)file_get_contents( $file ) === $xml )
            {
                $result['unchanged']++;
                continue;
            }
            $result['written'][] = $path;
            if ( !$dryRun )
                self::put( $file, $xml );
        }
        foreach ( $export['copies'] as $path => $source )
        {
            $wanted[$path] = true;
            $file = $dir . '/' . $path;
            if ( is_file( $file ) && sha1_file( $file ) === sha1_file( $source ) )
            {
                $result['unchanged']++;
                continue;
            }
            $result['written'][] = $path;
            if ( !$dryRun )
                self::put( $file, (string)file_get_contents( $source ) );
        }
        foreach ( self::existing( $dir ) as $path )
        {
            if ( isset( $wanted[$path] ) )
                continue;
            $result['removed'][] = $path;
            if ( !$dryRun )
                @unlink( $dir . '/' . $path );
        }
        return $result;
    }

    /**
     * The files of an export in a directory (its directories and the root's maps and DITAVAL files)
     *
     * @param string $dir
     * @return string[] paths
     */
    public static function existing( $dir )
    {
        $dir = rtrim( (string)$dir, '/' );
        $paths = array();
        foreach ( (array)glob( $dir . '/*.{ditamap,ditaval}', GLOB_BRACE ) as $file )
        {
            if ( is_file( $file ) )
                $paths[] = basename( $file );
        }
        foreach ( self::DIRECTORIES as $sub )
        {
            foreach ( (array)glob( $dir . '/' . $sub . '/*' ) as $file )
            {
                if ( is_file( $file ) )
                    $paths[] = $sub . '/' . basename( $file );
            }
        }
        sort( $paths );
        return $paths;
    }

    protected static function put( $file, $data )
    {
        $dir = dirname( $file );
        // the site's directory and file modes (eZDir::mkdir, eZFile::fileMode)
        if ( !is_dir( $dir ) )
            eZDir::mkdir( $dir, false, true );
        $tmp = $file . '.' . getmypid() . '.tmp';
        if ( @file_put_contents( $tmp, $data ) === false || !@rename( $tmp, $file ) )
            throw new RuntimeException( "Cannot write $file" );
        @chmod( $file, eZFile::fileMode( 0644 ) );
    }

    /**
     * The export as one zip file
     *
     * @param array $export build()
     * @param string $file
     * @param string $folder the folder in the zip ('' for none)
     * @return int bytes
     */
    public static function zip( array $export, $file, $folder = '' )
    {
        if ( !class_exists( 'ZipArchive' ) )
            throw new RuntimeException( 'The DITA download needs the PHP zip extension' );
        $prefix = $folder !== '' ? rtrim( $folder, '/' ) . '/' : '';
        $zip = new ZipArchive();
        if ( $zip->open( $file, ZipArchive::CREATE | ZipArchive::OVERWRITE ) !== true )
            throw new RuntimeException( "Cannot write $file" );
        foreach ( $export['files'] as $path => $xml )
            $zip->addFromString( $prefix . $path, $xml );
        foreach ( $export['copies'] as $path => $source )
            $zip->addFile( $source, $prefix . $path );
        $zip->close();
        return (int)filesize( $file );
    }

    // ---------------------------------------------------------------- validation

    /**
     * Checks an export (in memory: build(), or a directory): well-formed XML, the DITA 1.3 DOCTYPE of the root
     * element, unique topic ids, and every href of a map, topic or image that is not external resolves to a file of
     * the export. The DTDs are not loaded (nothing is fetched); a DTD validation is left to DITA-OT or xmllint with
     * the OASIS catalog.
     *
     * @param array|string $export build() or a directory
     * @return array list of array( 'file' => path, 'message' => text ); empty when the export is fine
     */
    public static function validate( $export )
    {
        if ( is_string( $export ) )
        {
            $dir = rtrim( $export, '/' );
            $files = array();
            $copies = array();
            foreach ( self::existing( $dir ) as $path )
            {
                if ( preg_match( '/\.(dita|ditamap|ditaval)$/', $path ) )
                    $files[$path] = (string)file_get_contents( $dir . '/' . $path );
                else
                    $copies[$path] = $dir . '/' . $path;
            }
            $export = array( 'files' => $files, 'copies' => $copies );
        }
        $problems = array();
        $present = array_fill_keys( array_merge( array_keys( $export['files'] ), array_keys( $export['copies'] ) ), true );
        $ids = array();
        foreach ( $export['files'] as $path => $xml )
        {
            $doc = new DOMDocument();
            $previous = libxml_use_internal_errors( true );
            $ok = $doc->loadXML( $xml, LIBXML_NONET );
            $errors = libxml_get_errors();
            libxml_clear_errors();
            libxml_use_internal_errors( $previous );
            if ( !$ok || !$doc->documentElement )
            {
                $problems[] = array( 'file' => $path, 'message' => 'not well-formed XML' . ( $errors ? ': ' . trim( $errors[0]->message ) . ' (line ' . $errors[0]->line . ')' : '' ) );
                continue;
            }
            $root = $doc->documentElement->nodeName;
            if ( substr( $path, -8 ) === '.ditaval' )
            {
                if ( $root !== 'val' )
                    $problems[] = array( 'file' => $path, 'message' => "the root element of a DITAVAL file is val, not $root" );
                continue;
            }
            if ( !isset( self::DOCTYPES[$root] ) )
            {
                $problems[] = array( 'file' => $path, 'message' => "unknown root element $root" );
                continue;
            }
            $type = $doc->doctype;
            $wanted = array( self::DOCTYPES[$root] );
            if ( $root === 'task' )
                $wanted[] = self::DOCTYPES['generalTask'];
            if ( !$type || $type->name !== $root || !in_array( array( $type->publicId, $type->systemId ), $wanted, true ) )
                $problems[] = array( 'file' => $path, 'message' => "the DOCTYPE is not the DITA 1.3 one of $root (" . self::DOCTYPES[$root][0] . ')' );
            $isMap = substr( $path, -8 ) === '.ditamap';
            if ( $isMap !== in_array( $root, array( 'map', 'bookmap' ), true ) )
                $problems[] = array( 'file' => $path, 'message' => "a .ditamap file holds a map, a .dita file a topic ($root)" );
            $id = (string)$doc->documentElement->getAttribute( 'id' );
            if ( !$isMap )
            {
                if ( $id === '' )
                    $problems[] = array( 'file' => $path, 'message' => 'the topic has no id' );
                elseif ( isset( $ids[$id] ) )
                    $problems[] = array( 'file' => $path, 'message' => "the topic id $id is also the id of " . $ids[$id] );
                else
                    $ids[$id] = $path;
                if ( !$doc->documentElement->getElementsByTagName( 'title' )->length && $root !== 'glossentry' )
                    $problems[] = array( 'file' => $path, 'message' => 'the topic has no title' );
            }
            $base = dirname( $path ) === '.' ? '' : dirname( $path ) . '/';
            $xpath = new DOMXPath( $doc );
            foreach ( $xpath->query( '//*[@href]' ) as $element )
            {
                $href = (string)$element->getAttribute( 'href' );
                if ( $element->getAttribute( 'scope' ) === 'external' || preg_match( '#^[a-z][a-z0-9+.-]*:#i', $href ) )
                    continue;
                $target = self::resolve( $base, preg_replace( '/#.*$/', '', $href ) );
                if ( $target === null || !isset( $present[$target] ) )
                    $problems[] = array( 'file' => $path, 'message' => '<' . $element->nodeName . "> href=\"$href\" does not resolve to a file of the export" );
            }
        }
        return $problems;
    }

    /**
     * A relative href from a directory of the export, null when it leaves the export
     */
    protected static function resolve( $base, $href )
    {
        if ( $href === '' || $href[0] === '/' )
            return null;
        $parts = array();
        foreach ( explode( '/', $base . $href ) as $part )
        {
            if ( $part === '' || $part === '.' )
                continue;
            if ( $part === '..' )
            {
                if ( !$parts )
                    return null;
                array_pop( $parts );
                continue;
            }
            $parts[] = $part;
        }
        return implode( '/', $parts );
    }
}

?>
