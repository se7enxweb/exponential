<?php
/**
 * The machine-checkable rules of the help style guide (doc/en/us/specifications/6.0/help-style-guide.md), on plain arrays:
 * no database. expHelpContentRepository loads the topics, figures, maps and glossary into the shapes below; the
 * command `exp:help:content check` prints the findings and exits 1 while an ERROR is left.
 *
 * Topic:    array( object_id, class, help_id, title, shortdesc, audience[], platform[], version_from, version_to,
 *                  review_date (timestamp, 0 none), owner (object id, 0 none), fields{name => Markdown},
 *                  related[object ids], figures[object ids], section (identifier), state (identifier) )
 * Figure:   array( object_id, alt_text, caption, figure_id, has_image, section )
 * Map:      array( object_id, class (help_manual|help_section), title, shortdesc, audience[], topicrefs[object ids],
 *                  remote_id, section )
 * Glossary: array( object_id, term, avoid[] , section )
 *
 * Finding:  array( level ERROR|WARNING|NOTICE, code, object_id, help_id, field, message, fix (section identifier
 *                  to set, or '') )
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentValidator
{
    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const NOTICE = 'NOTICE';

    /** help_id: area.topic[.variant], a-z 0-9, a hyphen inside a part, a dot between parts, 2+ parts, 80 characters */
    const HELP_ID_PATTERN = '/^[a-z0-9]+(?:-[a-z0-9]+)*(?:\.[a-z0-9]+(?:-[a-z0-9]+)*)+$/';
    const HELP_ID_MAX = 80;
    const SHORTDESC_MAX = 250;
    const ALT_TEXT_MAX = 150;
    const MAX_STEPS = 9;

    /** The default areas of the style guide (section 7); exphelp.ini [ContentSettings] Areas[] replaces them */
    const DEFAULT_AREAS = array( 'account', 'content', 'media', 'search', 'users', 'setup', 'privacy', 'help' );

    /** @var array */
    protected $findings = array();

    /** @var int now, for the review date */
    protected $now;

    /** @var string[] */
    protected $areas;

    /** @var string[]|null */
    protected $publicAudiences;

    /**
     * @param array $options now (timestamp), areas (string[]), public_audiences (string[]), used_help_ids (where =>
     *                       help_id, default usedHelpIdsFromSettings())
     */
    public function __construct( array $options = array() )
    {
        $this->now = isset( $options['now'] ) ? (int)$options['now'] : time();
        $this->areas = isset( $options['areas'] ) ? array_values( (array)$options['areas'] )
                                                  : expHelpContentSchema::setting( 'Areas', self::DEFAULT_AREAS );
        $this->publicAudiences = isset( $options['public_audiences'] ) ? (array)$options['public_audiences'] : null;
        $this->usedHelpIds = isset( $options['used_help_ids'] ) ? (array)$options['used_help_ids'] : self::usedHelpIdsFromSettings();
    }

    /** @var array where => help_id: the help_ids called from outside (usedHelpIdsFromSettings()) */
    protected $usedHelpIds = array();

    /**
     * Whether a help_id has the form of the style guide
     *
     * @param mixed $helpId
     * @return bool
     */
    public static function isValidHelpId( $helpId )
    {
        return is_string( $helpId ) && strlen( $helpId ) <= self::HELP_ID_MAX && (bool)preg_match( self::HELP_ID_PATTERN, $helpId );
    }

    /**
     * The parts of a help_id, null when it is not valid
     *
     * @param string $helpId
     * @return array|null array( area, topic, variant ('' none) )
     */
    public static function parseHelpId( $helpId )
    {
        if ( !self::isValidHelpId( $helpId ) )
            return null;
        $parts = explode( '.', $helpId );
        return array( 'area' => $parts[0], 'topic' => $parts[1], 'variant' => implode( '.', array_slice( $parts, 2 ) ) );
    }

    /**
     * The numbered lists of a Markdown text: one entry per list, each the list of its items' first lines. Nested
     * (indented) items count as sub-steps of the item above them.
     *
     * @param string $markdown
     * @return array array( 'lists' => array of item lists, 'nested' => int number of indented items )
     */
    public static function numberedLists( $markdown )
    {
        $lists = array();
        $current = null;
        $nested = 0;
        $inFence = false;
        foreach ( preg_split( '/\r\n|\r|\n/', (string)$markdown ) as $line )
        {
            if ( preg_match( '/^\s*(```|~~~)/', $line ) )
            {
                $inFence = !$inFence;
                continue;
            }
            if ( $inFence )
                continue;
            if ( preg_match( '/^(\d{1,3})[.)]\s+(.*)$/', $line, $m ) )
            {
                if ( $current === null )
                    $current = count( $lists );
                $lists[$current][] = trim( $m[2] );
                continue;
            }
            if ( preg_match( '/^\s{2,}(\d{1,3}[.)]|[-*+])\s+/', $line ) )
            {
                if ( $current !== null )
                    $nested++;
                continue;
            }
            // an indented continuation (the result of a step) or a blank line keeps the list open
            if ( trim( $line ) === '' || preg_match( '/^\s{2,}\S/', $line ) )
                continue;
            $current = null;
        }
        return array( 'lists' => array_values( $lists ), 'nested' => $nested );
    }

    /**
     * The number of sentences of a short text (a full stop, question or exclamation mark followed by a space and an
     * upper-case letter or digit starts a new one; "e.g." and "i.e." do not)
     *
     * @param string $text
     * @return int
     */
    public static function sentenceCount( $text )
    {
        $text = trim( preg_replace( '/\b(e\.g|i\.e|etc|vs|approx|incl)\./i', '$1', (string)$text ) );
        if ( $text === '' )
            return 0;
        return 1 + preg_match_all( '/[.!?]["\')\]]?\s+(?=[\p{Lu}\p{N}"])/u', $text );
    }

    /**
     * The text without bold spans (quoted UI labels are not checked against the glossary), code and links' targets
     *
     * @param string $markdown
     * @return string
     */
    public static function uncheckedTextRemoved( $markdown )
    {
        $text = preg_replace( '/```.*?```/s', ' ', (string)$markdown );
        $text = preg_replace( '/`[^`]*`/', ' ', $text );
        $text = preg_replace( '/\*\*[^*]+\*\*|__[^_]+__/', ' ', $text );
        $text = preg_replace( '/\]\([^)]*\)/', '] ', $text );
        return $text;
    }

    /**
     * All findings, sorted ERROR first
     *
     * @param array $topics
     * @param array $figures
     * @param array $maps
     * @param array $glossary
     * @return array
     */
    public function validate( array $topics, array $figures = array(), array $maps = array(), array $glossary = array() )
    {
        $this->findings = array();
        $byObject = array();
        foreach ( $topics as $topic )
            $byObject[(int)$topic['object_id']] = $topic;
        $figureIds = array();
        foreach ( $figures as $figure )
            $figureIds[(int)$figure['object_id']] = $figure;
        $mapIds = array();
        foreach ( $maps as $map )
            $mapIds[(int)$map['object_id']] = $map;

        $seen = array();
        foreach ( $topics as $topic )
        {
            $this->checkTopic( $topic, $byObject, $figureIds, $glossary );
            $id = isset( $topic['help_id'] ) ? (string)$topic['help_id'] : '';
            if ( $id !== '' )
            {
                if ( isset( $seen[$id] ) )
                    $this->add( self::ERROR, 'help-id.duplicate', $topic, 'help_id',
                                "The help_id \"$id\" is also used by object " . $seen[$id] . '; a help_id is unique' );
                else
                    $seen[$id] = (int)$topic['object_id'];
            }
        }
        $figureSections = $this->figureSections( $topics );
        foreach ( $figures as $figure )
            $this->checkFigure( $figure, isset( $figureSections[(int)$figure['object_id']] ) ? $figureSections[(int)$figure['object_id']] : null );
        $this->checkMaps( $maps, $byObject, $mapIds );
        $this->checkUsedHelpIds( $topics );
        foreach ( $glossary as $entry )
        {
            if ( isset( $entry['section'] ) && $entry['section'] !== expHelpContentSchema::SECTION_INTERNAL )
                $this->add( self::ERROR, 'section.mismatch', $entry, 'section', 'A glossary entry is always internal',
                            expHelpContentSchema::SECTION_INTERNAL );
        }

        $order = array( self::ERROR => 0, self::WARNING => 1, self::NOTICE => 2 );
        usort( $this->findings, function ( $a, $b ) use ( $order ) {
            return array( $order[$a['level']], $a['help_id'], $a['object_id'], $a['code'] ) <=> array( $order[$b['level']], $b['help_id'], $b['object_id'], $b['code'] );
        } );
        return $this->findings;
    }

    /**
     * The section each figure should be in: public as soon as one public topic refers to it
     *
     * @param array $topics
     * @return array figure object id => section identifier
     */
    public function figureSections( array $topics )
    {
        $sections = array();
        foreach ( $topics as $topic )
        {
            $public = expHelpContentSchema::isPublicAudience( isset( $topic['audience'] ) ? (array)$topic['audience'] : array(), $this->publicAudiences );
            foreach ( isset( $topic['figures'] ) ? (array)$topic['figures'] : array() as $figureId )
            {
                $figureId = (int)$figureId;
                if ( $public || !isset( $sections[$figureId] ) )
                    $sections[$figureId] = $public ? expHelpContentSchema::SECTION_PUBLIC
                                                   : ( isset( $sections[$figureId] ) ? $sections[$figureId] : expHelpContentSchema::SECTION_INTERNAL );
            }
        }
        return $sections;
    }

    /**
     * The checks of one topic
     */
    protected function checkTopic( array $topic, array $byObject, array $figures, array $glossary )
    {
        $class = isset( $topic['class'] ) ? (string)$topic['class'] : '';
        $fields = isset( $topic['fields'] ) ? (array)$topic['fields'] : array();
        $value = function ( $name ) use ( $topic, $fields ) {
            if ( isset( $fields[$name] ) )
                return $fields[$name];
            return isset( $topic[$name] ) ? $topic[$name] : '';
        };

        // required fields (style guide section 1)
        foreach ( expHelpContentSchema::requiredFields( $class ) as $field )
        {
            $v = $value( $field );
            if ( ( is_array( $v ) && !$v ) || ( !is_array( $v ) && trim( (string)$v ) === '' ) )
                $this->add( self::ERROR, 'field.required', $topic, $field, "The field \"$field\" is required for a " . self::typeName( $class ) );
        }

        // help_id
        $helpId = (string)$value( 'help_id' );
        if ( $helpId !== '' )
        {
            if ( !self::isValidHelpId( $helpId ) )
                $this->add( self::ERROR, 'help-id.invalid', $topic, 'help_id',
                            'The help_id is not area.topic[.variant] of a-z, 0-9 and inner hyphens (at most 80 characters)' );
            else
            {
                $parts = self::parseHelpId( $helpId );
                if ( $this->areas && !in_array( $parts['area'], $this->areas, true ) )
                    $this->add( self::WARNING, 'help-id.area', $topic, 'help_id',
                                "The area \"{$parts['area']}\" is not in the list of areas (style guide section 7)" );
                if ( preg_match( '/^(task|concept|reference|troubleshooting)-/', $parts['topic'] ) )
                    $this->add( self::WARNING, 'help-id.type', $topic, 'help_id', 'The topic part names the subject, not the type' );
            }
        }

        // shortdesc (section 8)
        $shortdesc = trim( (string)$value( 'shortdesc' ) );
        if ( $shortdesc !== '' )
        {
            if ( mb_strlen( $shortdesc ) > self::SHORTDESC_MAX )
                $this->add( self::ERROR, 'shortdesc.too-long', $topic, 'shortdesc', 'The short description is longer than 250 characters' );
            if ( self::sentenceCount( $shortdesc ) > 1 )
                $this->add( self::ERROR, 'shortdesc.sentences', $topic, 'shortdesc', 'The short description is more than one sentence' );
            if ( !preg_match( '/[.!?]["\')]?$/u', $shortdesc ) )
                $this->add( self::WARNING, 'shortdesc.full-stop', $topic, 'shortdesc', 'The short description does not end with a full stop' );
            if ( preg_match( '/\*\*|__|\[[^\]]*\]\(|<[a-z]/i', $shortdesc ) )
                $this->add( self::WARNING, 'shortdesc.markup', $topic, 'shortdesc', 'The short description has markup, a link or a bold label' );
            if ( preg_match( '/^this (topic|page|section)\b/i', $shortdesc ) )
                $this->add( self::WARNING, 'shortdesc.this-topic', $topic, 'shortdesc', 'The short description begins with "This topic"' );
            $title = trim( (string)$value( 'title' ) );
            if ( $title !== '' && mb_strtolower( rtrim( $shortdesc, '.' ) ) === mb_strtolower( $title ) )
                $this->add( self::WARNING, 'shortdesc.repeats-title', $topic, 'shortdesc', 'The short description repeats the title' );
        }

        // task steps (section 4)
        if ( $class === 'help_task' )
        {
            $steps = (string)$value( 'steps' );
            if ( trim( $steps ) !== '' )
            {
                $lists = self::numberedLists( $steps );
                $count = count( $lists['lists'] );
                if ( $count !== 1 )
                    $this->add( self::ERROR, 'task.steps-list', $topic, 'steps', "The steps are $count numbered lists; a task has exactly one" );
                elseif ( count( $lists['lists'][0] ) > self::MAX_STEPS )
                    $this->add( self::WARNING, 'task.too-many-steps', $topic, 'steps', 'More than 9 steps: split the task into two' );
                if ( $lists['nested'] > 0 )
                    $this->add( self::WARNING, 'task.sub-steps', $topic, 'steps', 'The steps have sub-steps' );
            }
            foreach ( array( 'context', 'prereq', 'result' ) as $field )
            {
                $lists = self::numberedLists( (string)$value( $field ) );
                if ( $lists['lists'] )
                    $this->add( self::WARNING, 'task.steps-elsewhere', $topic, $field, "The field \"$field\" has a numbered list; steps belong in \"steps\"" );
            }
        }

        // notices (section 5): no hazard signal words in software help
        foreach ( $fields as $field => $text )
        {
            if ( preg_match( '/\*\*(DANGER|WARNING|CAUTION)\b/', (string)$text, $m ) )
                $this->add( self::WARNING, 'notice.signal-word', $topic, $field,
                            "The signal word {$m[1]} is for hazards to persons; use Attention, Data protection or Note" );
        }
        $this->checkNotices( $topic, $class, $fields );

        // the glossary decides (Terminology)
        $this->checkTerminology( $topic, $fields + array( 'title' => (string)$value( 'title' ), 'shortdesc' => $shortdesc ), $glossary );

        // fields editors always fill in (section 1 and 8)
        foreach ( array( 'platform', 'version_from', 'review_date', 'owner' ) as $field )
        {
            $v = $value( $field );
            if ( ( is_array( $v ) && !$v ) || ( !is_array( $v ) && ( $v === '' || $v === 0 || $v === '0' || $v === null ) ) )
                $this->add( self::NOTICE, 'field.recommended', $topic, $field, "The field \"$field\" is empty; editors always fill it in" );
        }
        foreach ( array( 'version_from', 'version_to' ) as $field )
        {
            $v = trim( (string)$value( $field ) );
            if ( $v !== '' && !preg_match( '/^\d+\.\d+\.\d+$/', $v ) )
                $this->add( self::WARNING, 'version.format', $topic, $field, 'A version is MAJOR.MINOR.PATCH' );
        }
        $review = (int)$value( 'review_date' );
        if ( $review > 0 && $review < $this->now )
            $this->add( self::WARNING, 'review-date.past', $topic, 'review_date', 'The review date is in the past (' . gmdate( 'Y-m-d', $review ) . ')' );

        // relations
        foreach ( isset( $topic['related'] ) ? (array)$topic['related'] : array() as $id )
        {
            if ( !isset( $byObject[(int)$id] ) )
                $this->add( self::ERROR, 'related.target', $topic, 'related', "Related object $id is not a help topic" );
        }
        foreach ( isset( $topic['figures'] ) ? (array)$topic['figures'] : array() as $id )
        {
            if ( !isset( $figures[(int)$id] ) )
                $this->add( self::ERROR, 'figures.target', $topic, 'figures', "Figure object $id is not a help figure" );
        }

        // section from audience (set by --fix, never by hand)
        $expected = expHelpContentSchema::sectionForAudience( isset( $topic['audience'] ) ? (array)$topic['audience'] : array(), $this->publicAudiences );
        if ( isset( $topic['section'] ) && $topic['section'] !== $expected )
            $this->add( self::ERROR, 'section.mismatch', $topic, 'section',
                        "The section is \"{$topic['section']}\", the audience requires \"$expected\" (--fix sets it)", $expected );
    }

    /**
     * The help_ids that are called from outside (plan, "Checks": clients keep a list of the IDs they use, and a test
     * checks that each exists): exphelp.ini [ContentSettings] ContextHelpIds[] (the "?" of the admin pages) and
     * ClientHelpIds[] (the help_ids a mobile app or another client opens). An id no topic has is an ERROR (the client
     * gets a 404); one whose topic is not Released yet is a WARNING.
     *
     * @param array $topics
     */
    protected function checkUsedHelpIds( array $topics )
    {
        $byId = array();
        foreach ( $topics as $topic )
            if ( isset( $topic['help_id'] ) && (string)$topic['help_id'] !== '' )
                $byId[(string)$topic['help_id']] = $topic;
        foreach ( $this->usedHelpIds as $where => $helpId )
        {
            $item = array( 'object_id' => 0, 'help_id' => $helpId );
            if ( !isset( $byId[$helpId] ) )
                $this->add( self::ERROR, 'client.help-id-unknown', $item, $where, "No topic has the help_id \"$helpId\" that $where names" );
            elseif ( isset( $byId[$helpId]['state'] ) && $byId[$helpId]['state'] !== expHelpContentSchema::STATE_RELEASED )
                $this->add( self::WARNING, 'client.help-id-not-released', $byId[$helpId], $where,
                            "The topic \"$helpId\" that $where names is not Released; clients get a 404 until it is" );
        }
    }

    /**
     * The help_ids called from outside: "ContextHelpIds[<key>]" or "ClientHelpIds[]" => help_id
     *
     * @return array
     */
    public static function usedHelpIdsFromSettings()
    {
        $used = array();
        foreach ( (array)expHelpContentSchema::setting( 'ContextHelpIds', array() ) as $key => $helpId )
            if ( is_scalar( $helpId ) && trim( (string)$helpId ) !== '' )
                $used["ContextHelpIds[$key]"] = trim( (string)$helpId );
        foreach ( array_values( (array)expHelpContentSchema::setting( 'ClientHelpIds', array() ) ) as $n => $helpId )
            if ( is_scalar( $helpId ) && trim( (string)$helpId ) !== '' )
                $used["ClientHelpIds[$n]"] = trim( (string)$helpId );
        return $used;
    }

    /**
     * The notice levels of the style guide (section 5, IEC/IEEE 82079-1): what can be checked without reading the text.
     *
     * - notice.position: an Attention or Data protection notice of a task stands in the step, not in context, prereq,
     *   result or postreq;
     * - notice.per-step: one notice per step;
     * - notice.before-action: in a step, an Attention or Data protection notice comes first, before the action;
     * - notice.too-long: a notice outside the steps (a paragraph or quote of its own) has at most three sentences.
     *   In a step the notice and the action share the text, so its length is left to the review.
     *
     * @param array $topic
     * @param string $class
     * @param array $fields field => Markdown
     */
    protected function checkNotices( array $topic, $class, array $fields )
    {
        foreach ( $fields as $field => $text )
        {
            $text = (string)$text;
            if ( $class === 'help_task' && $field === 'steps' )
            {
                foreach ( self::stepItems( $text ) as $n => $item )
                {
                    $notices = self::notices( $item );
                    if ( count( $notices ) > 1 )
                        $this->add( self::WARNING, 'notice.per-step', $topic, $field,
                                    'Step ' . ( $n + 1 ) . ' has ' . count( $notices ) . ' notices; one notice per step, or split the step' );
                    foreach ( $notices as $notice )
                        if ( $notice['level'] !== 'note' && $notice['offset'] > 0 )
                            $this->add( self::WARNING, 'notice.before-action', $topic, $field,
                                        'Step ' . ( $n + 1 ) . ': the ' . $notice['label'] . ' notice stands after the action; it comes first in the step' );
                }
                continue;
            }
            foreach ( self::notices( $text ) as $notice )
            {
                if ( $class === 'help_task' && $notice['level'] !== 'note' && in_array( $field, array( 'context', 'prereq', 'result', 'postreq' ), true ) )
                    $this->add( self::WARNING, 'notice.position', $topic, $field,
                                "The {$notice['label']} notice is in \"$field\"; it stands in the step where the action happens" );
                if ( self::sentenceCount( $notice['paragraph'] ) > 3 )
                    $this->add( self::WARNING, 'notice.too-long', $topic, $field,
                                "The {$notice['label']} notice has more than three sentences" );
            }
        }
    }

    /**
     * The notices of a Markdown text: a bold label Attention, Data protection or Note with a colon (**Attention:**,
     * **Attention**:), outside code. Each: level (attention|data-protection|note), label (as written), offset (of the
     * label in the text without leading blanks and quote markers), paragraph (the notice's text after the label, up
     * to the end of its paragraph).
     *
     * @param string $markdown
     * @return array
     */
    public static function notices( $markdown )
    {
        $text = preg_replace( '/```.*?```/s', ' ', (string)$markdown );
        $text = preg_replace( '/`[^`]*`/', ' ', $text );
        $text = preg_replace( '/^[ \t]*>[ \t]?/m', '', $text );
        $text = ltrim( $text );
        $notices = array();
        if ( !preg_match_all( '/(?:\*\*|__)[ \t]*(Attention|Data protection|Note)[ \t]*(?::[ \t]*(?:\*\*|__)|(?:\*\*|__)[ \t]*:)/i',
                              $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) )
            return $notices;
        foreach ( $matches as $m )
        {
            $start = $m[0][1] + strlen( $m[0][0] );
            $rest = substr( $text, $start );
            $paragraph = preg_split( '/\n[ \t]*\n/', $rest, 2 )[0];
            $label = ucfirst( strtolower( $m[1][0] ) );
            $notices[] = array( 'level' => str_replace( ' ', '-', strtolower( $m[1][0] ) ), 'label' => $label,
                                'offset' => $m[0][1], 'paragraph' => trim( preg_replace( '/\s+/', ' ', $paragraph ) ) );
        }
        return $notices;
    }

    /**
     * The items of the numbered lists of a Markdown text (the steps), each with its continuation lines, in order.
     *
     * @param string $markdown
     * @return string[]
     */
    public static function stepItems( $markdown )
    {
        $items = array();
        $current = null;
        $inFence = false;
        foreach ( preg_split( '/\r\n|\r|\n/', (string)$markdown ) as $line )
        {
            if ( preg_match( '/^\s*(```|~~~)/', $line ) )
                $inFence = !$inFence;
            if ( !$inFence && preg_match( '/^(\d{1,3})[.)]\s+(.*)$/', $line, $m ) )
            {
                $items[] = $m[2];
                $current = count( $items ) - 1;
                continue;
            }
            if ( $current !== null && ( $inFence || trim( $line ) === '' || preg_match( '/^\s{2,}\S/', $line ) ) )
            {
                $items[$current] .= "\n" . trim( $line );
                continue;
            }
            $current = null;
        }
        return array_map( 'trim', $items );
    }

    protected function checkTerminology( array $topic, array $texts, array $glossary )
    {
        foreach ( $glossary as $entry )
        {
            $term = isset( $entry['term'] ) ? trim( (string)$entry['term'] ) : '';
            foreach ( isset( $entry['avoid'] ) ? (array)$entry['avoid'] : array() as $avoid )
            {
                $avoid = trim( (string)$avoid );
                if ( $avoid === '' )
                    continue;
                $pattern = '/(?<![\p{L}\p{N}])' . preg_quote( $avoid, '/' ) . '(?![\p{L}\p{N}])/iu';
                foreach ( $texts as $field => $text )
                {
                    if ( preg_match( $pattern, self::uncheckedTextRemoved( (string)$text ) ) )
                    {
                        $this->add( self::WARNING, 'glossary.avoid', $topic, $field, "\"$avoid\": the glossary says \"$term\"" );
                        break;
                    }
                }
            }
        }
    }

    protected function checkFigure( array $figure, $expectedSection )
    {
        $alt = trim( isset( $figure['alt_text'] ) ? (string)$figure['alt_text'] : '' );
        $generated = isset( $figure['figure_id'] ) && trim( (string)$figure['figure_id'] ) !== '';
        if ( $alt === '' )
            $this->add( self::ERROR, 'figure.alt-missing', $figure, 'alt_text', 'The figure has no alt text' );
        else
        {
            if ( mb_strlen( $alt ) > self::ALT_TEXT_MAX )
                $this->add( $generated ? self::ERROR : self::WARNING, 'figure.alt-too-long', $figure, 'alt_text', 'The alt text is longer than 150 characters' );
            if ( preg_match( '/^(an? )?(image|screenshot|picture|photo) of\b/i', $alt ) )
                $this->add( self::WARNING, 'figure.alt-image-of', $figure, 'alt_text', 'The alt text begins with "Image of" or "Screenshot of"' );
            $caption = trim( isset( $figure['caption'] ) ? (string)$figure['caption'] : '' );
            if ( $caption !== '' && mb_strtolower( $caption ) === mb_strtolower( $alt ) )
                $this->add( self::WARNING, 'figure.alt-caption', $figure, 'alt_text', 'The alt text repeats the caption' );
        }
        if ( isset( $figure['has_image'] ) && !$figure['has_image'] )
            $this->add( self::ERROR, 'figure.image-missing', $figure, 'image', 'The figure has no image' );
        if ( $generated && !preg_match( '/^[a-z0-9.-]+\/\d+$/', (string)$figure['figure_id'] ) )
            $this->add( self::WARNING, 'figure.id-format', $figure, 'figure_id', 'A generated figure has the figure_id <help_id>/<n>' );
        $expected = $expectedSection === null ? expHelpContentSchema::SECTION_INTERNAL : $expectedSection;
        if ( isset( $figure['section'] ) && $figure['section'] !== $expected )
            $this->add( self::ERROR, 'section.mismatch', $figure, 'section',
                        "The section is \"{$figure['section']}\", its topics require \"$expected\" (--fix sets it)", $expected );
    }

    protected function checkMaps( array $maps, array $topics, array $mapIds )
    {
        $referenced = array();
        foreach ( $maps as $map )
        {
            $refs = isset( $map['topicrefs'] ) ? (array)$map['topicrefs'] : array();
            if ( trim( isset( $map['title'] ) ? (string)$map['title'] : '' ) === '' )
                $this->add( self::ERROR, 'field.required', $map, 'title', 'A manual or chapter has a title' );
            if ( $map['class'] === 'help_manual' && trim( isset( $map['shortdesc'] ) ? (string)$map['shortdesc'] : '' ) === '' )
                $this->add( self::ERROR, 'field.required', $map, 'shortdesc', 'A manual has a short description' );
            if ( !$refs )
                $this->add( self::WARNING, 'map.empty', $map, 'topicrefs', 'The ' . ( $map['class'] === 'help_manual' ? 'manual' : 'chapter' ) . ' refers to nothing' );
            $dupes = array_diff_assoc( $refs, array_unique( $refs ) );
            if ( $dupes )
                $this->add( self::WARNING, 'map.duplicate', $map, 'topicrefs', 'The same topic is listed twice: ' . implode( ', ', array_unique( $dupes ) ) );
            foreach ( $refs as $id )
            {
                $id = (int)$id;
                $referenced[$id][] = (int)$map['object_id'];
                if ( isset( $topics[$id] ) )
                    continue;
                if ( isset( $mapIds[$id] ) && $mapIds[$id]['class'] === 'help_section' )
                    continue;
                $this->add( self::ERROR, 'map.target', $map, 'topicrefs', "Object $id is neither a chapter nor a help topic" );
            }
            if ( $map['class'] === 'help_manual' )
            {
                $expected = expHelpContentSchema::sectionForAudience( isset( $map['audience'] ) ? (array)$map['audience'] : array(), $this->publicAudiences );
                if ( isset( $map['section'] ) && $map['section'] !== $expected )
                    $this->add( self::ERROR, 'section.mismatch', $map, 'section', "The section is \"{$map['section']}\", the audience requires \"$expected\" (--fix sets it)", $expected );
            }
        }
        foreach ( $maps as $map )
        {
            if ( $map['class'] === 'help_section' && empty( $referenced[(int)$map['object_id']] ) )
                $this->add( self::WARNING, 'map.orphan-chapter', $map, 'topicrefs', 'No manual refers to the chapter' );
            if ( $this->hasCycle( (int)$map['object_id'], $mapIds, array() ) )
                $this->add( self::ERROR, 'map.cycle', $map, 'topicrefs', 'The chapter refers to itself through its chapters' );
        }
        // a chapter's section: public when a public manual (directly or through chapters) lists it
        foreach ( $this->chapterSections( $maps ) as $id => $expected )
        {
            if ( isset( $mapIds[$id]['section'] ) && $mapIds[$id]['section'] !== $expected )
                $this->add( self::ERROR, 'section.mismatch', $mapIds[$id], 'section', "The section is \"{$mapIds[$id]['section']}\", its manuals require \"$expected\" (--fix sets it)", $expected );
        }
    }

    /**
     * The section of each chapter: public when a public manual lists it, directly or through other chapters
     *
     * @param array $maps
     * @return array chapter object id => section identifier
     */
    public function chapterSections( array $maps )
    {
        $byId = array();
        foreach ( $maps as $map )
            $byId[(int)$map['object_id']] = $map;
        $result = array();
        foreach ( $maps as $map )
        {
            if ( $map['class'] === 'help_section' )
                $result[(int)$map['object_id']] = expHelpContentSchema::SECTION_INTERNAL;
        }
        foreach ( $maps as $map )
        {
            if ( $map['class'] !== 'help_manual' )
                continue;
            if ( !expHelpContentSchema::isPublicAudience( isset( $map['audience'] ) ? (array)$map['audience'] : array(), $this->publicAudiences ) )
                continue;
            $queue = isset( $map['topicrefs'] ) ? (array)$map['topicrefs'] : array();
            $seen = array();
            while ( $queue )
            {
                $id = (int)array_shift( $queue );
                if ( isset( $seen[$id] ) || !isset( $byId[$id] ) || $byId[$id]['class'] !== 'help_section' )
                    continue;
                $seen[$id] = true;
                $result[$id] = expHelpContentSchema::SECTION_PUBLIC;
                $queue = array_merge( $queue, isset( $byId[$id]['topicrefs'] ) ? (array)$byId[$id]['topicrefs'] : array() );
            }
        }
        return $result;
    }

    protected function hasCycle( $start, array $maps, array $path )
    {
        if ( in_array( $start, $path, true ) )
            return true;
        if ( !isset( $maps[$start] ) )
            return false;
        $path[] = $start;
        foreach ( isset( $maps[$start]['topicrefs'] ) ? (array)$maps[$start]['topicrefs'] : array() as $id )
        {
            $id = (int)$id;
            if ( isset( $maps[$id] ) && $maps[$id]['class'] === 'help_section' && $this->hasCycle( $id, $maps, $path ) )
                return true;
        }
        return false;
    }

    protected function add( $level, $code, array $item, $field, $message, $fix = '' )
    {
        $this->findings[] = array(
            'level' => $level,
            'code' => $code,
            'object_id' => isset( $item['object_id'] ) ? (int)$item['object_id'] : 0,
            'help_id' => isset( $item['help_id'] ) ? (string)$item['help_id'] : ( isset( $item['remote_id'] ) ? (string)$item['remote_id'] : '' ),
            'field' => $field,
            'message' => $message,
            'fix' => $fix,
        );
    }

    protected static function typeName( $class )
    {
        $types = expHelpContentSchema::TOPIC_TYPES;
        return isset( $types[$class] ) ? $types[$class] : $class;
    }

    /**
     * @param array $findings
     * @return array level => count
     */
    public static function counts( array $findings )
    {
        $counts = array( self::ERROR => 0, self::WARNING => 0, self::NOTICE => 0 );
        foreach ( $findings as $finding )
            $counts[$finding['level']]++;
        return $counts;
    }
}

?>
