<?php
/**
 * File containing the expHelpMarkdownRenderer class.
 *
 * Renders the Markdown of the documentation (doc/**.md) to the HTML of the help viewer (exphelp/viewer,
 * doc/features/6.0/help-system.md): CommonMark blocks (ATX and setext headings, paragraphs, block quotes, ordered and
 * bullet lists, fenced and indented code, thematic breaks), the GitHub extensions the documentation uses (tables with
 * alignment, task lists, strikethrough, footnotes, autolinked URLs, alerts "> [!NOTE]"), callouts written as
 * "> **Note:** ..." or ":::tip Title ... :::", heading ids (GitHub's slugs, so links written for GitHub land on the
 * same heading) and a table of contents. Code blocks are highlighted on the server (expHelpHighlighter).
 *
 * Safe by construction. The document's characters are HTML-escaped; the renderer emits only its own markup. Raw HTML
 * in a document is never passed through: a short allowlist of formatting elements without attributes (b, strong, i,
 * em, s, del, ins, mark, small, sub, sup, kbd, code, u, br, wbr), <a> (its href through the link policy, name/id as an
 * anchor), <img> (through the image policy), <details>/<summary> and <abbr title> are rebuilt by the renderer, HTML
 * comments are dropped, and every other tag (<script>, <style>, <iframe>, on* attributes ...) is shown as text.
 *
 * The link policy: http(s) and mailto addresses (external links get rel="noopener noreferrer"); #anchors on the
 * page; relative paths, resolved against the page's id, that the link_resolver turns into a viewer address (it
 * decides which files exist and may be shown: the document index). Anything else (javascript:, data:, vbscript:,
 * file:, a path above the documentation root, a path the resolver does not know) is shown as plain text in
 * span.a4-help-deadlink. Images: https addresses, or relative paths the image_resolver turns into an address (only
 * files of the index, served by exphelp/asset); otherwise the alternative text is shown.
 *
 * Options (constructor):
 *   link_resolver  callable( string $href, string $currentId ): ?string  a relative link as written (the anchor
 *                  already reduced to the id it names) to an address; null = shown as text (expHelpLinks::link).
 *   image_resolver callable( string $src, string $currentId ): string|array|null  the address of an image, or
 *                  array( 'url', 'width', 'height' ) so the page reserves its space; null = the alternative text
 *                  (expHelpLinks::image).
 *   translate      callable( string $text ): string  the interface words (Note, Warning, Table ...); default
 *                  ezpI18n::tr( 'kernel/exphelp', ... ) when the kernel is loaded, else the English text.
 *   id_prefix      string prefixed to every id the renderer makes (default '').
 *
 * The markup (styled by help.css; the class contract with the viewer and exphelp.js): div.a4-help-body around the
 * page; h1-h6.a4-help-h[id] with a.a4-help-anchor; div.a4-help-code[data-lang][data-label] > pre.a4-help-pre >
 * code.language-<x> with span.a4-help-tok-<kind> (expHelpHighlighter); div.a4-help-callout.a4-help-callout--<note|
 * tip|important|warning|caution>[role=note] > p.a4-help-callout-title (span.a4-icon + title) + the body;
 * div.a4-help-table-wrap[role=region][tabindex=0] > table.a4-table.a4-help-table, cells .a4-help-align-<left|center|
 * right>; blockquote.a4-help-quote; hr.a4-help-hr; figure.a4-help-figure > img.a4-help-img + figcaption;
 * ul.a4-help-tasks > li.a4-help-task > span.a4-help-task-box(--done); details.a4-help-details; a.a4-help-ext for
 * external links; span.a4-help-deadlink; span.a4-help-target[id] for <a name>; sup.a4-help-fnref and
 * section.a4-help-footnotes > ol > li[id] with a.a4-help-fnback; div.a4-help-plain for plain text pages.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpMarkdownRenderer
{
    /** Largest document that is rendered; a larger one is shown as plain text */
    const MAX_LENGTH = 4000000;

    /** Deepest nesting of block quotes, lists and containers; deeper content is shown as text */
    const MAX_DEPTH = 16;

    /** @var array the elements kept from inline HTML, without attributes */
    protected static $inlineTags = array( 'b', 'strong', 'i', 'em', 's', 'del', 'ins', 'mark', 'small', 'sub', 'sup',
                                          'kbd', 'code', 'u', 'var', 'samp', 'q', 'cite', 'dfn', 'tt' );

    /** @var array callout kinds, the words that name them and their icon */
    protected static $calloutWords = array(
        'note' => 'note', 'info' => 'note', 'information' => 'note', 'status' => 'note', 'see also' => 'note',
        'tip' => 'tip', 'hint' => 'tip', 'rest assured' => 'tip', 'good to know' => 'tip', 'success' => 'tip',
        'important' => 'important', 'action required' => 'important', 'attention' => 'important', 'remember' => 'important',
        'warning' => 'warning', 'deprecated' => 'warning', 'heads up' => 'warning', 'breaking' => 'warning', 'breaking change' => 'warning',
        'caution' => 'caution', 'danger' => 'caution', 'critical' => 'caution', 'security' => 'caution',
    );

    /** @var array callout kind => array( icon, default title ) */
    protected static $callouts = array(
        'note' => array( 'a4-bi-info-circle', 'Note' ),
        'tip' => array( 'a4-bi-lightning', 'Tip' ),
        'important' => array( 'a4-bi-exclamation-diamond', 'Important' ),
        'warning' => array( 'a4-bi-exclamation-triangle', 'Warning' ),
        'caution' => array( 'a4-bi-x-circle-fill', 'Caution' ),
    );

    /** @var array options */
    protected $options;

    /** @var string the repository-relative path of the document being rendered */
    protected $path = '';

    /** @var array ids used on the page */
    protected $usedIds = array();

    /** @var array the headings: list of array( 'level', 'id', 'text' ) */
    protected $toc = array();

    /** @var array reference link definitions: label => array( url, title ) */
    protected $refs = array();

    /** @var array footnote definitions: label => markdown */
    protected $footnoteDefs = array();

    /** @var array footnotes in the order of their first reference: label => number */
    protected $footnoteOrder = array();

    /** @var int nesting of the current block */
    protected $depth = 0;

    /** @var array texts and backtick run lengths that have no closing run: key => run => from offset */
    protected $backtickMiss = array();

    /** @var array texts => the offsets of their matching brackets (bracketMatches()) */
    protected $bracketCache = array();

    public function __construct( array $options = array() )
    {
        $this->options = $options + array(
            'link_resolver' => null,
            'image_resolver' => null,
            'translate' => null,
            'id_prefix' => '',
        );
    }

    /**
     * Render a document.
     *
     * @param string $markdown
     * @param string $path the page id (features/6.0/x.md, a path below the documentation root), to resolve its links
     * @return array( 'html' => string, 'toc' => list of array( 'level' => int, 'text' => string, 'anchor' => id, 'id' => id ),
     *                'title' => string (the first level 1 heading, else the first heading, else ''),
     *                'frontMatter' => string (the raw front matter block, '' if none) )
     */
    public function render( $markdown, $path = '' )
    {
        $this->path = self::normalizePath( (string)$path );
        if ( $this->path === null )
            $this->path = '';
        $this->usedIds = array();
        $this->toc = array();
        $this->refs = array();
        $this->footnoteDefs = array();
        $this->footnoteOrder = array();
        $this->depth = 0;

        $markdown = (string)$markdown;
        if ( !preg_match( '//u', $markdown ) )
            $markdown = mb_convert_encoding( $markdown, 'UTF-8', 'UTF-8' );
        if ( strlen( $markdown ) > self::MAX_LENGTH )
            return array( 'html' => $this->renderPlain( $markdown ), 'toc' => array(), 'title' => '', 'frontMatter' => '' );

        // placeholders of the inline renderer must never come from the document; neither do other control characters
        $markdown = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', str_replace( array( "\r\n", "\r" ), "\n", $markdown ) );
        $markdown = preg_replace( '/^\x{FEFF}/u', '', $markdown );
        list( $frontMatter, $markdown ) = self::splitFrontMatter( $markdown );

        $lines = explode( "\n", $markdown );
        foreach ( $lines as $i => $line )
            $lines[$i] = self::expandTabs( $line );
        $lines = $this->collectDefinitions( $lines );

        $html = $this->renderBlocks( $lines );
        $html .= $this->renderFootnotes();

        $title = '';
        foreach ( $this->toc as $heading )
        {
            if ( $heading['level'] === 1 )
            {
                $title = $heading['text'];
                break;
            }
        }
        if ( $title === '' && $this->toc )
            $title = $this->toc[0]['text'];

        return array(
            'html' => '<div class="a4-help-body">' . $html . '</div>',
            'toc' => $this->toc,
            'title' => $title,
            'frontMatter' => $frontMatter,
        );
    }

    /**
     * A plain text page (.txt, .rst, a file without suffix): preformatted, escaped text. The title is its first
     * line that is not empty and not a rule of = - * # characters.
     *
     * @param string $text
     * @param string $currentId
     * @return array same shape as render()
     */
    public function renderPlainText( $text, $currentId = '' )
    {
        // a list of records (the release and branch history) is shown as tables, recognised by its shape
        if ( strpos( (string)$text, "\n- " ) !== false )
        {
            $used = array();
            $records = new expHelpRecordRenderer( array( 'translate' => array( $this, 't' ) ) );
            $rendered = $records->render( $text, function( $heading ) use ( &$used )
            {
                return $this->options['id_prefix'] . self::slug( $heading, $used );
            } );
            if ( $rendered !== null )
                return $rendered + array( 'frontMatter' => '' );
        }
        $title = '';
        foreach ( explode( "\n", str_replace( array( "\r\n", "\r" ), "\n", (string)$text ), 40 ) as $line )
        {
            $line = trim( $line );
            if ( $line !== '' && preg_match( '/^[=\-*#~_+]+$/', $line ) !== 1 )
            {
                $title = mb_substr( preg_replace( '/[\x00-\x1F\x7F]/', '', $line ), 0, 200, 'UTF-8' );
                break;
            }
        }
        return array( 'html' => $this->renderPlain( $text ), 'toc' => array(), 'title' => (string)$title, 'frontMatter' => '' );
    }

    /**
     * Whether a link target is one the renderer may write as it is: http, https or mailto with a valid address, an
     * #anchor, or a relative path that stays inside the documentation root (whether that page exists is the link
     * resolver's question). javascript:, data:, vbscript:, file: and every other scheme are not, however they are
     * spelled (entities, white space and control characters inside the scheme are ignored as a browser would).
     *
     * @param string $url
     * @return bool
     */
    public static function isSafeUrl( $url )
    {
        $renderer = new self( array(
            'link_resolver' => function( $href ) { return '/'; },
            'translate' => function( $text ) { return $text; },
        ) );
        return $renderer->resolveUrl( $url ) !== null;
    }

    /**
     * Preformatted, escaped text.
     *
     * @param string $text
     * @return string
     */
    protected function renderPlain( $text )
    {
        $text = (string)$text;
        if ( !preg_match( '//u', $text ) )
            $text = mb_convert_encoding( $text, 'UTF-8', 'UTF-8' );
        return '<div class="a4-help-body a4-help-plain"><pre class="a4-help-pre" tabindex="0">' . self::escape( $text ) . '</pre></div>';
    }

    /**
     * The id GitHub gives a heading: inline Markdown and tags removed, lower case, only letters, digits, spaces,
     * hyphens and underscores kept, spaces to hyphens. With $used, a repeated id gets -1, -2 ... and is recorded.
     *
     * @param string $text
     * @param array|null $used
     * @return string
     */
    public static function slug( $text, ?array &$used = null )
    {
        $text = (string)$text;
        $text = preg_replace( '/<[^>]*>/', '', $text );
        $text = preg_replace( '/!?\[([^\]]*)\]\([^)]*\)/', '$1', $text );
        $text = preg_replace( '/!?\[([^\]]*)\]\[[^\]]*\]/', '$1', $text );
        $text = str_replace( array( '`', '~~' ), '', $text );
        $text = preg_replace( '/\*+/', '', $text );
        $text = preg_replace( '/(?<![\p{L}\p{N}])_+|_+(?![\p{L}\p{N}])/u', '', $text );
        $text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        $text = mb_strtolower( trim( $text ), 'UTF-8' );
        $slug = preg_replace( '/[^\p{L}\p{N}\p{M} _-]/u', '', $text );
        $slug = str_replace( ' ', '-', (string)$slug );
        if ( $slug === '' )
            $slug = 'section';
        if ( $used !== null )
        {
            $base = $slug;
            $n = 0;
            while ( isset( $used[$slug] ) )
                $slug = $base . '-' . ( ++$n );
            $used[$slug] = true;
        }
        return $slug;
    }

    /**
     * Split a leading front matter block ("---" lines around "key: value" lines) from a document.
     *
     * @param string $text
     * @return array( string front matter (without the --- lines), string the rest )
     */
    public static function splitFrontMatter( $text )
    {
        $text = (string)$text;
        if ( preg_match( '/^\x{FEFF}?---[ \t]*\n(.*?)\n(?:---|\.\.\.)[ \t]*(?:\n|$)/su', $text, $m ) === 1
             && preg_match( '/^[A-Za-z_][\w-]*\s*:/m', $m[1] ) === 1 && substr_count( $m[1], "\n" ) < 200 )
            return array( $m[1], (string)substr( $text, strlen( $m[0] ) ) );
        return array( '', $text );
    }

    /**
     * Resolve a relative link target against a page id (a path below the documentation root, features/6.0/x.md).
     * Returns the normalised path, or null when it climbs above the root or holds characters a path never has.
     *
     * @param string $base the page id
     * @param string $target the link (../c.md, ./img/x.png)
     * @param bool $fromRoot resolve from the root instead of the page's directory (a link written as /a/b.md)
     * @return string|null
     */
    public static function resolvePath( $base, $target, $fromRoot = false )
    {
        $target = (string)$target;
        if ( $target === '' || $target[0] === '/' || strpos( $target, '\\' ) !== false )
            return null;
        $dir = $fromRoot ? '' : dirname( (string)$base );
        if ( $dir === '.' || $dir === '' || $dir === '/' )
            $dir = '';
        return self::normalizePath( ( $dir === '' ? '' : $dir . '/' ) . $target );
    }

    /**
     * Normalise a repository-relative path: no empty or "." segments, ".." applied, never above the root.
     *
     * @param string $path
     * @return string|null
     */
    public static function normalizePath( $path )
    {
        if ( $path === '' )
            return '';
        if ( preg_match( '/[\x00-\x1F\x7F\\\\]|^\//', $path ) === 1 )
            return null;
        $out = array();
        foreach ( explode( '/', $path ) as $segment )
        {
            if ( $segment === '' || $segment === '.' )
                continue;
            if ( $segment === '..' )
            {
                if ( !$out )
                    return null;
                array_pop( $out );
                continue;
            }
            $out[] = $segment;
        }
        return implode( '/', $out );
    }

    /**
     * Escape for HTML text and attribute values.
     */
    public static function escape( $text )
    {
        return htmlspecialchars( (string)$text, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8' );
    }

    // ------------------------------------------------------------------------------------------------------------
    // blocks

    /**
     * Take the reference link and footnote definitions out of the document (outside code blocks).
     *
     * @param array $lines
     * @return array the remaining lines
     */
    protected function collectDefinitions( array $lines )
    {
        $out = array();
        $fence = null;
        $count = count( $lines );
        for ( $i = 0; $i < $count; $i++ )
        {
            $line = $lines[$i];
            if ( $fence !== null )
            {
                if ( preg_match( '/^ {0,3}(' . preg_quote( $fence[0], '/' ) . '{' . strlen( $fence ) . ',})\s*$/', $line ) === 1 )
                    $fence = null;
                $out[] = $line;
                continue;
            }
            if ( preg_match( '/^ {0,3}(`{3,}|~{3,})/', $line, $m ) === 1 )
            {
                $fence = $m[1];
                $out[] = $line;
                continue;
            }
            if ( preg_match( '/^ {0,3}\[\^([^\]\s]{1,64})\]:[ \t]?(.*)$/', $line, $m ) === 1 )
            {
                $body = array( $m[2] );
                while ( $i + 1 < $count && ( preg_match( '/^(?: {4}| *$)/', $lines[$i + 1] ) === 1 )
                        && !( trim( $lines[$i + 1] ) === '' && ( $i + 2 >= $count || preg_match( '/^ {4}/', $lines[$i + 2] ) !== 1 ) ) )
                    $body[] = (string)substr( $lines[++$i], 4 );
                $label = mb_strtolower( $m[1], 'UTF-8' );
                if ( !isset( $this->footnoteDefs[$label] ) )
                    $this->footnoteDefs[$label] = implode( "\n", $body );
                continue;
            }
            if ( preg_match( '/^ {0,3}\[([^\]^][^\]]{0,200})\]:[ \t]*<?([^\s>]+)>?(?:[ \t]+(?:"([^"]*)"|\'([^\']*)\'|\(([^)]*)\)))?[ \t]*$/', $line, $m ) === 1 )
            {
                $label = self::refLabel( $m[1] );
                if ( !isset( $this->refs[$label] ) )
                    $this->refs[$label] = array( $m[2], isset( $m[3] ) && $m[3] !== '' ? $m[3] : ( isset( $m[4] ) && $m[4] !== '' ? $m[4] : ( isset( $m[5] ) ? $m[5] : '' ) ) );
                continue;
            }
            $out[] = $line;
        }
        return $out;
    }

    /**
     * Render a list of lines as blocks.
     *
     * @param array $lines
     * @param bool $tight paragraphs without <p> (the items of a tight list)
     * @return string
     */
    protected function renderBlocks( array $lines, $tight = false )
    {
        if ( $this->depth > self::MAX_DEPTH )
            return '<p>' . $this->renderInline( implode( "\n", array_map( 'trim', $lines ) ) ) . '</p>';
        $this->depth++;
        $html = '';
        $openDetails = 0;
        $count = count( $lines );
        $i = 0;
        while ( $i < $count )
        {
            $line = $lines[$i];
            if ( trim( $line ) === '' )
            {
                $i++;
                continue;
            }

            // fenced code
            if ( preg_match( '/^( {0,3})(`{3,}|~{3,})[ \t]*([^\n]*)$/', $line, $m ) === 1 && ( $m[2][0] === '~' || strpos( $m[3], '`' ) === false ) )
            {
                $indent = strlen( $m[1] );
                $fence = $m[2];
                $code = array();
                $i++;
                while ( $i < $count && preg_match( '/^ {0,3}' . preg_quote( $fence[0], '/' ) . '{' . strlen( $fence ) . ',}[ \t]*$/', $lines[$i] ) !== 1 )
                {
                    $code[] = preg_replace( '/^ {0,' . $indent . '}/', '', $lines[$i] );
                    $i++;
                }
                $i++;
                $html .= $this->renderCode( implode( "\n", $code ), $m[3] );
                continue;
            }

            // ATX heading
            if ( preg_match( '/^ {0,3}(#{1,6})(?:[ \t]+(.*?))?(?:[ \t]+#+)?[ \t]*$/', $line, $m ) === 1 )
            {
                $html .= $this->renderHeading( strlen( $m[1] ), isset( $m[2] ) ? $m[2] : '' );
                $i++;
                continue;
            }

            // thematic break
            if ( preg_match( '/^ {0,3}([-*_])(?:[ \t]*\1){2,}[ \t]*$/', $line ) === 1 )
            {
                $html .= '<hr class="a4-help-hr">';
                $i++;
                continue;
            }

            // HTML comment: dropped
            if ( preg_match( '/^ {0,3}<!--/', $line ) === 1 )
            {
                while ( $i < $count && strpos( $lines[$i], '-->' ) === false )
                    $i++;
                $rest = $i < $count ? trim( (string)substr( $lines[$i], strpos( $lines[$i], '-->' ) + 3 ) ) : '';
                $i++;
                if ( $rest !== '' )
                    $html .= '<p>' . $this->renderInline( $rest ) . '</p>';
                continue;
            }

            // <details> and <summary>
            if ( preg_match( '/^ {0,3}<details(\s+open(?:=["\']?open["\']?)?)?\s*>\s*(?:<summary>(.*?)<\/summary>)?\s*$/i', $line, $m ) === 1 )
            {
                $html .= '<details class="a4-help-details"' . ( !empty( $m[1] ) ? ' open' : '' ) . '>';
                if ( isset( $m[2] ) && $m[2] !== '' )
                    $html .= '<summary>' . $this->renderInline( $m[2] ) . '</summary>';
                $openDetails++;
                $i++;
                continue;
            }
            if ( $openDetails > 0 && preg_match( '/^ {0,3}<summary>(.*?)<\/summary>\s*$/i', $line, $m ) === 1 )
            {
                $html .= '<summary>' . $this->renderInline( $m[1] ) . '</summary>';
                $i++;
                continue;
            }
            if ( $openDetails > 0 && preg_match( '/^ {0,3}<\/details>\s*$/i', $line ) === 1 )
            {
                $html .= '</details>';
                $openDetails--;
                $i++;
                continue;
            }

            // block quote, alert, callout
            if ( preg_match( '/^ {0,3}>/', $line ) === 1 )
            {
                $quote = array();
                while ( $i < $count )
                {
                    if ( preg_match( '/^ {0,3}> ?(.*)$/', $lines[$i], $q ) === 1 )
                        $quote[] = $q[1];
                    elseif ( trim( $lines[$i] ) !== '' && $quote && trim( end( $quote ) ) !== '' && !$this->startsBlock( $lines[$i] ) )
                        $quote[] = $lines[$i];
                    else
                        break;
                    $i++;
                }
                $html .= $this->renderQuote( $quote );
                continue;
            }

            // ::: container
            if ( preg_match( '/^ {0,3}(:{3,})[ \t]*([A-Za-z][\w-]*)?[ \t]*(.*)$/', $line, $m ) === 1 && $m[2] !== '' )
            {
                $marker = $m[1];
                $inner = array();
                $nest = 0;
                $i++;
                while ( $i < $count )
                {
                    if ( preg_match( '/^ {0,3}:{3,}[ \t]*[A-Za-z]/', $lines[$i] ) === 1 )
                        $nest++;
                    elseif ( preg_match( '/^ {0,3}:{3,}[ \t]*$/', $lines[$i] ) === 1 )
                    {
                        if ( $nest === 0 )
                            break;
                        $nest--;
                    }
                    $inner[] = $lines[$i];
                    $i++;
                }
                $i++;
                $word = strtolower( $m[2] );
                $kind = isset( self::$calloutWords[$word] ) ? self::$calloutWords[$word] : ( isset( self::$callouts[$word] ) ? $word : 'note' );
                $title = trim( $m[3] );
                $html .= $this->renderCallout( $kind, $title === '' ? null : $this->renderInline( $title ), $this->renderBlocks( $inner ) );
                continue;
            }

            // table
            if ( $i + 1 < $count && strpos( $line, '|' ) !== false && self::isTableDelimiter( $lines[$i + 1] ) )
            {
                $header = self::splitRow( $line );
                $aligns = self::tableAligns( $lines[$i + 1] );
                if ( count( $header ) === count( $aligns ) )
                {
                    $rows = array();
                    $i += 2;
                    while ( $i < $count && trim( $lines[$i] ) !== '' && strpos( $lines[$i], '|' ) !== false && !$this->startsBlock( $lines[$i], true ) )
                    {
                        $rows[] = self::splitRow( $lines[$i] );
                        $i++;
                    }
                    $html .= $this->renderTable( $header, $aligns, $rows );
                    continue;
                }
            }

            // list
            if ( self::listMarker( $line ) !== null )
            {
                $html .= $this->renderList( $lines, $i );
                continue;
            }

            // indented code
            if ( preg_match( '/^ {4}/', $line ) === 1 )
            {
                $code = array();
                while ( $i < $count && ( preg_match( '/^ {4}/', $lines[$i] ) === 1 || trim( $lines[$i] ) === '' ) )
                {
                    $code[] = (string)substr( $lines[$i], 4 );
                    $i++;
                }
                while ( $code && trim( end( $code ) ) === '' )
                    array_pop( $code );
                $html .= $this->renderCode( implode( "\n", $code ), '' );
                continue;
            }

            // paragraph, setext heading
            $para = array( $line );
            $i++;
            $setext = 0;
            while ( $i < $count )
            {
                $next = $lines[$i];
                if ( trim( $next ) === '' )
                    break;
                if ( preg_match( '/^ {0,3}=+[ \t]*$/', $next ) === 1 )
                {
                    $setext = 1;
                    $i++;
                    break;
                }
                if ( preg_match( '/^ {0,3}-+[ \t]*$/', $next ) === 1 )
                {
                    $setext = 2;
                    $i++;
                    break;
                }
                if ( $this->startsBlock( $next, true ) || ( $i + 1 < $count && strpos( $next, '|' ) !== false && self::isTableDelimiter( $lines[$i + 1] ) ) )
                    break;
                $para[] = $next;
                $i++;
            }
            $text = implode( "\n", array_map( 'ltrim', $para ) );
            if ( $setext )
                $html .= $this->renderHeading( $setext, trim( $text ) );
            else
                $html .= $this->renderParagraph( rtrim( $text ), $tight );
        }
        $html .= str_repeat( '</details>', $openDetails );
        $this->depth--;
        return $html;
    }

    /**
     * Does a line start a block that ends a paragraph?
     *
     * @param string $line
     * @param bool $interrupt only the blocks that may interrupt a paragraph
     * @return bool
     */
    protected function startsBlock( $line, $interrupt = false )
    {
        if ( preg_match( '/^ {0,3}(?:#{1,6}(?:[ \t]|$)|`{3,}|~{3,}|>|<!--|:{3,}[ \t]*[A-Za-z]|<\/?details\b|<summary>)/i', $line ) === 1 )
            return true;
        if ( preg_match( '/^ {0,3}([-*_])(?:[ \t]*\1){2,}[ \t]*$/', $line ) === 1 )
            return true;
        $marker = self::listMarker( $line );
        if ( $marker !== null && trim( $marker['rest'] ) !== '' && ( $marker['ordered'] === false || $marker['start'] === 1 || !$interrupt ) )
            return true;
        return false;
    }

    /**
     * A list item marker at the start of a line.
     *
     * @param string $line
     * @return array|null array( 'indent', 'marker', 'ordered', 'start', 'width' (content column), 'rest' )
     */
    protected static function listMarker( $line )
    {
        if ( preg_match( '/^( {0,3})([-*+]|(\d{1,9})([.)]))( {1,4}(?! )|[ \t]*$)(.*)$/', $line, $m ) !== 1 )
            return null;
        if ( preg_match( '/^ {0,3}([-*_])(?:[ \t]*\1){2,}[ \t]*$/', $line ) === 1 )
            return null;
        $ordered = $m[3] !== '';
        $width = strlen( $m[1] ) + strlen( $m[2] ) + max( 1, strlen( $m[5] ) );
        return array(
            'indent' => strlen( $m[1] ),
            'marker' => $ordered ? $m[4] : $m[2],
            'ordered' => $ordered,
            'start' => $ordered ? (int)$m[3] : 1,
            'width' => $width,
            'rest' => $m[6],
        );
    }

    /**
     * A list starting at $lines[$i]; $i is moved past it.
     */
    protected function renderList( array $lines, &$i )
    {
        $count = count( $lines );
        $first = self::listMarker( $lines[$i] );
        $items = array();
        $loose = false;
        while ( $i < $count )
        {
            $marker = self::listMarker( $lines[$i] );
            if ( $marker === null || $marker['ordered'] !== $first['ordered'] || $marker['marker'] !== $first['marker'] || $marker['indent'] > $first['indent'] + 3 )
                break;
            $body = array( $marker['rest'] );
            $i++;
            $blankBefore = false;
            while ( $i < $count )
            {
                $line = $lines[$i];
                if ( trim( $line ) === '' )
                {
                    // a blank line continues the item when the next text belongs to it
                    $j = $i + 1;
                    while ( $j < $count && trim( $lines[$j] ) === '' )
                        $j++;
                    if ( $j < $count && self::indentOf( $lines[$j] ) > $marker['indent'] && self::indentOf( $lines[$j] ) >= min( $marker['width'], $marker['indent'] + 2 ) )
                    {
                        for ( ; $i < $j; $i++ )
                            $body[] = '';
                        $blankBefore = true;
                        continue;
                    }
                    break;
                }
                $indent = self::indentOf( $line );
                if ( $indent > $marker['indent'] && $indent >= min( $marker['width'], $marker['indent'] + 2 ) )
                {
                    if ( $blankBefore )
                        $loose = $loose || self::listMarker( substr( $line, min( $indent, $marker['width'] ) ) ) === null || true;
                    $body[] = (string)substr( $line, min( $indent, $marker['width'] ) );
                    $i++;
                    continue;
                }
                // lazy continuation of the item's paragraph
                if ( !$blankBefore && trim( end( $body ) ) !== '' && !$this->startsBlock( $line ) && self::listMarker( $line ) === null
                     && !( $i + 1 < $count && strpos( $line, '|' ) !== false && self::isTableDelimiter( $lines[$i + 1] ) ) )
                {
                    $body[] = ltrim( $line );
                    $i++;
                    continue;
                }
                break;
            }
            $items[] = $body;
            // a blank line between two items makes the list loose
            if ( $i < $count && trim( $lines[$i] ) === '' )
            {
                $j = $i;
                while ( $j < $count && trim( $lines[$j] ) === '' )
                    $j++;
                $next = $j < $count ? self::listMarker( $lines[$j] ) : null;
                if ( $next !== null && $next['ordered'] === $first['ordered'] && $next['marker'] === $first['marker'] && $next['indent'] <= $first['indent'] + 3 )
                {
                    $loose = true;
                    $i = $j;
                    continue;
                }
                break;
            }
        }
        if ( !$loose )
        {
            foreach ( $items as $body )
            {
                // blank lines inside an item between two of its blocks make it loose too
                $inner = array_values( array_filter( array_keys( $body ), function( $k ) use ( $body ) { return trim( $body[$k] ) === ''; } ) );
                foreach ( $inner as $k )
                {
                    if ( $k > 0 && isset( $body[$k + 1] ) && trim( $body[$k + 1] ) !== '' && self::listMarker( $body[$k + 1] ) === null && self::indentOf( $body[$k + 1] ) < 4 )
                    {
                        $loose = true;
                        break 2;
                    }
                }
            }
        }

        $tag = $first['ordered'] ? 'ol' : 'ul';
        $html = '<' . $tag . ( $first['ordered'] && $first['start'] !== 1 ? ' start="' . $first['start'] . '"' : '' );
        $isTasks = false;
        $out = '';
        foreach ( $items as $body )
        {
            $task = null;
            if ( preg_match( '/^\[([ xX])\](?:[ \t]+|$)(.*)$/', $body[0], $m ) === 1 )
            {
                $task = $m[1] !== ' ';
                $body[0] = $m[2];
                $isTasks = true;
            }
            $content = $this->renderBlocks( $body, !$loose );
            if ( $task !== null )
                $out .= '<li class="a4-help-task">' . $this->taskBox( $task ) . $content . '</li>';
            else
                $out .= '<li>' . $content . '</li>';
        }
        return $html . ( $isTasks ? ' class="a4-help-tasks"' : '' ) . '>' . $out . '</' . $tag . '>';
    }

    /**
     * The tick of a task list item (an icon and a word for screen readers; no form control).
     */
    protected function taskBox( $done )
    {
        return '<span class="' . ( $done ? 'a4-icon a4-bi-check2-square a4-help-task-box a4-help-task-box--done' : 'a4-help-task-box' )
            . '" aria-hidden="true"></span><span class="a4-sr-only">' . self::escape( $this->t( $done ? 'Done:' : 'To do:' ) ) . ' </span>';
    }

    protected static function indentOf( $line )
    {
        return strlen( $line ) - strlen( ltrim( $line, ' ' ) );
    }

    protected static function expandTabs( $line )
    {
        if ( strpos( $line, "\t" ) === false )
            return $line;
        // leading tabs to the next multiple of four, the rest left alone
        $out = '';
        $len = strlen( $line );
        for ( $k = 0; $k < $len; $k++ )
        {
            $c = $line[$k];
            if ( $c === "\t" )
                $out .= str_repeat( ' ', 4 - ( strlen( $out ) % 4 ) );
            elseif ( $c === ' ' )
                $out .= ' ';
            else
                return $out . substr( $line, $k );
        }
        return $out;
    }

    /**
     * A heading with its id, recorded in the table of contents.
     */
    protected function renderHeading( $level, $text )
    {
        $text = trim( $text );
        $id = $this->options['id_prefix'] . self::slug( $text, $this->usedIds );
        $inner = $this->renderInline( $text );
        $plain = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( strip_tags( $inner ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
        $this->toc[] = array( 'level' => $level, 'text' => $plain, 'anchor' => $id, 'id' => $id );
        $eid = self::escape( $id );
        return '<h' . $level . ' id="' . $eid . '" class="a4-help-h">' . $inner
            . ' <a class="a4-help-anchor" href="#' . $eid . '" aria-hidden="true" tabindex="-1">#</a></h' . $level . '>';
    }

    /**
     * A paragraph; one that holds only an image becomes a figure with the alternative text as caption.
     */
    protected function renderParagraph( $text, $tight )
    {
        if ( preg_match( '/^!\[([^\]\n]*)\]\(([^)\s]*)(?:\s+"[^"]*")?\)$/', $text, $m ) === 1 && trim( $m[1] ) !== '' )
        {
            $img = $this->renderInline( $text );
            if ( strpos( $img, '<img ' ) === 0 )
                return '<figure class="a4-help-figure">' . $img . '<figcaption>' . $this->renderInline( $m[1] ) . '</figcaption></figure>';
        }
        $inner = $this->renderInline( $text );
        return $tight ? $inner : '<p>' . $inner . '</p>';
    }

    /**
     * A fenced or indented code block, highlighted.
     */
    protected function renderCode( $code, $info )
    {
        $info = trim( $info );
        $language = expHelpHighlighter::language( $info );
        $name = '';
        if ( preg_match( '/^(?:language-)?([A-Za-z0-9_+-]{1,30})/', $info, $m ) === 1 )
            $name = strtolower( $m[1] );
        if ( $language === '' && $name === '' )
            $language = expHelpHighlighter::guessLanguage( $code );
        $class = $name !== '' ? $name : $language;
        $label = expHelpHighlighter::label( $language );
        $attrs = ' data-lang="' . self::escape( $class === '' ? 'text' : $class ) . '"';
        if ( $label !== '' && $language !== 'text' )
            $attrs .= ' data-label="' . self::escape( $label ) . '"';
        return '<div class="a4-help-code"' . $attrs . '><pre class="a4-help-pre" tabindex="0"><code class="language-'
            . self::escape( $class === '' ? 'text' : $class ) . '">'
            . expHelpHighlighter::highlight( $code, $language === '' ? 'text' : $language ) . '</code></pre></div>';
    }

    /**
     * A block quote: a GitHub alert (> [!NOTE]), a callout (> **Warning:** ...) or a plain quote.
     */
    protected function renderQuote( array $lines )
    {
        while ( $lines && trim( $lines[0] ) === '' )
            array_shift( $lines );
        if ( $lines && preg_match( '/^\s*\[!([A-Za-z]+)\][ \t]*(.*)$/', $lines[0], $m ) === 1 && isset( self::$callouts[strtolower( $m[1] )] ) )
        {
            $kind = strtolower( $m[1] );
            if ( trim( $m[2] ) === '' )
                array_shift( $lines );
            else
                $lines[0] = $m[2];
            return $this->renderCallout( $kind, null, $this->renderBlocks( $lines ) );
        }
        if ( $lines && preg_match( '/^\s*(\*\*|__)([^*_\n]{1,40}?)(:?)\1(:?)[ \t]*(.*)$/', $lines[0], $m ) === 1
             && ( $m[3] === ':' || $m[4] === ':' || trim( $m[5] ) === '' ) )
        {
            $word = strtolower( trim( $m[2] ) );
            if ( isset( self::$calloutWords[$word] ) )
            {
                if ( trim( $m[5] ) === '' )
                    array_shift( $lines );
                else
                    $lines[0] = $m[5];
                return $this->renderCallout( self::$calloutWords[$word], $this->renderInline( trim( $m[2] ) ), $this->renderBlocks( $lines ) );
            }
        }
        return '<blockquote class="a4-help-quote">' . $this->renderBlocks( $lines ) . '</blockquote>';
    }

    /**
     * A callout box. $title is HTML (already rendered) or null for the kind's own word.
     */
    protected function renderCallout( $kind, $title, $body )
    {
        list( $icon, $word ) = self::$callouts[$kind];
        if ( $title === null )
            $title = self::escape( $this->t( $word ) );
        return '<div class="a4-help-callout a4-help-callout--' . $kind . '" role="note">'
            . '<p class="a4-help-callout-title"><span class="a4-icon ' . $icon . '" aria-hidden="true"></span> ' . $title . '</p>'
            . $body . '</div>';
    }

    protected static function isTableDelimiter( $line )
    {
        return preg_match( '/^ {0,3}\|?[ \t]*:?-+:?[ \t]*(?:\|[ \t]*:?-+:?[ \t]*)*\|?[ \t]*$/', $line ) === 1
            && ( strpos( $line, '|' ) !== false || preg_match( '/^\s*:?-+:?\s*$/', $line ) !== 1 );
    }

    protected static function tableAligns( $line )
    {
        $aligns = array();
        foreach ( self::splitRow( $line ) as $cell )
        {
            $cell = trim( $cell );
            $left = $cell !== '' && $cell[0] === ':';
            $right = substr( $cell, -1 ) === ':';
            $aligns[] = $left && $right ? 'center' : ( $right ? 'right' : ( $left ? 'left' : '' ) );
        }
        return $aligns;
    }

    /**
     * The cells of a table row: split on | outside code spans and not escaped.
     */
    protected static function splitRow( $line )
    {
        $line = trim( $line );
        if ( $line !== '' && $line[0] === '|' )
            $line = substr( $line, 1 );
        if ( substr( $line, -1 ) === '|' && substr( $line, -2 ) !== '\\|' )
            $line = substr( $line, 0, -1 );
        $cells = array();
        $cell = '';
        $len = strlen( $line );
        $tick = 0;
        for ( $k = 0; $k < $len; $k++ )
        {
            $c = $line[$k];
            if ( $c === '\\' && $k + 1 < $len && $line[$k + 1] === '|' )
            {
                $cell .= '|';
                $k++;
                continue;
            }
            if ( $c === '`' )
            {
                $run = strspn( $line, '`', $k );
                if ( $tick === 0 )
                {
                    // a code span only when it closes on this row
                    if ( strpos( $line, str_repeat( '`', $run ), $k + $run ) !== false )
                        $tick = $run;
                }
                elseif ( $run === $tick )
                    $tick = 0;
                $cell .= str_repeat( '`', $run );
                $k += $run - 1;
                continue;
            }
            if ( $c === '|' && $tick === 0 )
            {
                $cells[] = $cell;
                $cell = '';
                continue;
            }
            $cell .= $c;
        }
        $cells[] = $cell;
        return $cells;
    }

    protected function renderTable( array $header, array $aligns, array $rows )
    {
        $cols = count( $header );
        $html = '<div class="a4-help-table-wrap" role="region" tabindex="0" aria-label="' . self::escape( $this->t( 'Table' ) ) . '">'
            . '<table class="a4-table a4-help-table">';
        // a header row of empty cells (a key / value table) is left out: empty column headers help nobody
        if ( trim( implode( '', $header ) ) !== '' )
        {
            $html .= '<thead><tr>';
            foreach ( $header as $k => $cell )
                $html .= '<th scope="col"' . self::alignClass( $aligns[$k] ) . '>' . $this->renderInline( trim( $cell ) ) . '</th>';
            $html .= '</tr></thead>';
        }
        if ( $rows )
        {
            $html .= '<tbody>';
            foreach ( $rows as $row )
            {
                $html .= '<tr>';
                for ( $k = 0; $k < $cols; $k++ )
                    $html .= '<td' . self::alignClass( $aligns[$k] ) . '>' . ( isset( $row[$k] ) ? $this->renderInline( trim( $row[$k] ) ) : '' ) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
        }
        return $html . '</table></div>';
    }

    protected static function alignClass( $align )
    {
        return $align === '' ? '' : ' class="a4-help-align-' . $align . '"';
    }

    /**
     * The footnotes, in the order of their first reference.
     */
    protected function renderFootnotes()
    {
        if ( !$this->footnoteOrder )
            return '';
        $html = '<section class="a4-help-footnotes" aria-label="' . self::escape( $this->t( 'Footnotes' ) ) . '"><hr class="a4-help-hr"><ol>';
        // a footnote may reference another one: render until no new ones appear
        $done = array();
        while ( count( $done ) < count( $this->footnoteOrder ) )
        {
            foreach ( $this->footnoteOrder as $label => $n )
            {
                if ( isset( $done[$label] ) )
                    continue;
                $done[$label] = true;
                $id = self::escape( $this->footnoteId( $label ) );
                $body = $this->renderBlocks( explode( "\n", $this->footnoteDefs[$label] ), true );
                $html .= '<li id="fn-' . $id . '">' . $body . ' <a class="a4-help-fnback" href="#fnref-' . $id . '" aria-label="'
                    . self::escape( $this->t( 'Back to the reference' ) ) . '">&#8617;</a></li>';
            }
        }
        return $html . '</ol></section>';
    }

    protected function footnoteId( $label )
    {
        return $this->options['id_prefix'] . preg_replace( '/[^A-Za-z0-9_-]/', '-', $label );
    }

    // ------------------------------------------------------------------------------------------------------------
    // inline

    /**
     * Render inline Markdown. The document's text is escaped; links, code, images and the allowed tags are built
     * by the renderer and kept in placeholders (\x1A n \x1B) while emphasis is applied to the escaped text.
     *
     * @param string $text
     * @return string
     */
    protected function renderInline( $text )
    {
        $text = str_replace( array( "\x1A", "\x1B" ), '', (string)$text );
        $stash = array();
        $put = function( $html ) use ( &$stash )
        {
            $stash[] = $html;
            return "\x1A" . ( count( $stash ) - 1 ) . "\x1B";
        };
        $open = array();
        $raw = '';
        $len = strlen( $text );
        $k = 0;
        while ( $k < $len )
        {
            $c = $text[$k];
            // backslash escape, hard break
            if ( $c === '\\' && $k + 1 < $len )
            {
                $n = $text[$k + 1];
                if ( $n === "\n" )
                {
                    $raw .= $put( '<br>' ) . "\n";
                    $k += 2;
                    continue;
                }
                if ( strpos( '!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~', $n ) !== false )
                {
                    $raw .= $put( self::escape( $n ) );
                    $k += 2;
                    continue;
                }
            }
            // code span
            if ( $c === '`' )
            {
                $run = strspn( $text, '`', $k );
                $end = $this->findBacktickRun( $text, $k + $run, $run );
                if ( $end !== null )
                {
                    $code = str_replace( "\n", ' ', substr( $text, $k + $run, $end - $k - $run ) );
                    if ( strlen( $code ) > 2 && $code[0] === ' ' && substr( $code, -1 ) === ' ' && trim( $code ) !== '' )
                        $code = substr( $code, 1, -1 );
                    $raw .= $put( '<code>' . self::escape( $code ) . '</code>' );
                    $k = $end + $run;
                    continue;
                }
                $raw .= str_repeat( '`', $run );
                $k += $run;
                continue;
            }
            // image, link, footnote reference
            if ( $c === '!' && $k + 1 < $len && $text[$k + 1] === '[' )
            {
                $parsed = $this->parseLink( $text, $k + 1 );
                if ( $parsed !== null )
                {
                    $raw .= $put( $this->renderImage( $parsed['label'], $parsed['url'], $parsed['title'] ) );
                    $k = $parsed['end'];
                    continue;
                }
            }
            if ( $c === '[' )
            {
                if ( preg_match( '/\G\[\^([^\]\s]{1,64})\]/', $text, $m, 0, $k ) === 1 && isset( $this->footnoteDefs[mb_strtolower( $m[1], 'UTF-8' )] ) )
                {
                    $raw .= $put( $this->renderFootnoteRef( mb_strtolower( $m[1], 'UTF-8' ) ) );
                    $k += strlen( $m[0] );
                    continue;
                }
                $parsed = $this->parseLink( $text, $k );
                if ( $parsed !== null )
                {
                    $raw .= $put( $this->renderLink( $parsed['label'], $parsed['url'], $parsed['title'] ) );
                    $k = $parsed['end'];
                    continue;
                }
            }
            if ( $c === '<' )
            {
                // autolink
                if ( preg_match( '/\G<((?:https?|mailto):[^\s<>]+)>/i', $text, $m, 0, $k ) === 1
                     || preg_match( '/\G<([A-Za-z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?(?:\.[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?)+)>/', $text, $m, 0, $k ) === 1 )
                {
                    $url = strpos( $m[1], '@' ) !== false && stripos( $m[1], 'mailto:' ) !== 0 && stripos( $m[1], 'http' ) !== 0 ? 'mailto:' . $m[1] : $m[1];
                    $raw .= $put( $this->renderLinkHtml( $url, self::escape( $m[1] ), '' ) );
                    $k += strlen( $m[0] );
                    continue;
                }
                // comment
                if ( substr( $text, $k, 4 ) === '<!--' )
                {
                    $end = strpos( $text, '-->', $k + 4 );
                    if ( $end !== false )
                    {
                        $k = $end + 3;
                        continue;
                    }
                }
                $tag = $this->inlineTag( $text, $k, $open );
                if ( $tag !== null )
                {
                    $raw .= $put( $tag[0] );
                    $k = $tag[1];
                    continue;
                }
            }
            // bare URL (GitHub autolink extension)
            if ( ( $c === 'h' || $c === 'H' || $c === 'w' || $c === 'W' ) && ( $k === 0 || preg_match( '/[\s(*_~"\']/', $text[$k - 1] ) === 1 )
                 && preg_match( '/\G(?:https?:\/\/|www\.)[^\s<>"`]+/i', $text, $m, 0, $k ) === 1 )
            {
                $url = $m[0];
                // trailing punctuation and an unbalanced closing parenthesis are not part of it
                while ( $url !== '' && ( strpos( '.,:;!?*_~\'"', substr( $url, -1 ) ) !== false
                        || ( substr( $url, -1 ) === ')' && substr_count( $url, '(' ) < substr_count( $url, ')' ) ) ) )
                    $url = substr( $url, 0, -1 );
                if ( strlen( $url ) > 7 )
                {
                    $href = stripos( $url, 'www.' ) === 0 ? 'https://' . $url : $url;
                    $raw .= $put( $this->renderLinkHtml( $href, self::escape( $url ), '' ) );
                    $k += strlen( $url );
                    continue;
                }
            }
            // hard break: two spaces before a new line
            if ( $c === "\n" && substr( $raw, -2 ) === '  ' )
            {
                $raw = rtrim( $raw, ' ' ) . $put( '<br>' ) . "\n";
                $k++;
                continue;
            }
            $raw .= $c;
            $k++;
        }
        // close what inline HTML left open
        while ( $open )
        {
            $last = array_pop( $open );
            $raw .= $put( $last[1] );
        }

        $html = self::escape( $raw );
        $html = $this->emphasis( $html );
        // placeholders: restore in one pass (stashed HTML is final)
        return preg_replace_callback( '/\x1A(\d+)\x1B/', function( $m ) use ( $stash )
        {
            return isset( $stash[(int)$m[1]] ) ? $stash[(int)$m[1]] : '';
        }, $html );
    }

    /**
     * Strong, emphasis and strikethrough on escaped text.
     */
    protected function emphasis( $html )
    {
        $rules = array(
            // the content of a span never holds its own delimiter (strong: never "**"), so a failed opener stops at
            // the next delimiter and a page of unmatched "*a *a *a" is matched in linear time
            '/(?<![\\\\*])\*\*(?=[^\s*])((?:[^*]|\*(?!\*)){1,600}?)(?<=[^\s*])\*\*(?!\*)/su' => '<strong>$1</strong>',
            '/(?<![\p{L}\p{N}_])__(?=[^\s_])((?:[^_]|_(?!_)){1,600}?)(?<=[^\s_])__(?![\p{L}\p{N}_])/su' => '<strong>$1</strong>',
            '/(?<![\\\\*])\*(?=[^\s*])([^*]{1,600}?)(?<=[^\s*])\*(?!\*)/su' => '<em>$1</em>',
            '/(?<![\p{L}\p{N}_])_(?=[^\s_])([^_]{1,600}?)(?<=[^\s_])_(?![\p{L}\p{N}_])/su' => '<em>$1</em>',
            '/(?<!~)~~(?=[^\s~])((?:[^~]|~(?!~)){1,600}?)(?<=[^\s~])~~(?!~)/su' => '<del>$1</del>',
        );
        foreach ( $rules as $regex => $replace )
        {
            $result = preg_replace( $regex, $replace, $html );
            if ( is_string( $result ) )
                $html = $result;
        }
        return $html;
    }

    /**
     * The offset of the next run of exactly $run backticks from $from, or null.
     */
    protected function findBacktickRun( $text, $from, $run )
    {
        // a run length that found no closer from an offset finds none from any later offset either
        $key = strlen( $text ) . ':' . crc32( $text );
        if ( isset( $this->backtickMiss[$key][$run] ) && $from >= $this->backtickMiss[$key][$run] )
            return null;
        $len = strlen( $text );
        $k = $from;
        while ( $k < $len )
        {
            $p = strpos( $text, '`', $k );
            if ( $p === false )
                break;
            $r = strspn( $text, '`', $p );
            if ( $r === $run )
                return $p;
            $k = $p + $r;
        }
        if ( count( $this->backtickMiss ) > 64 )
            $this->backtickMiss = array();
        $this->backtickMiss[$key][$run] = $from;
        return null;
    }

    /**
     * The offset of the closing "]" of every "[" in a text (escaped brackets and code spans skipped), computed once
     * per text with a stack.
     *
     * @param string $text
     * @return array offset of "[" => offset of its "]"
     */
    protected function bracketMatches( $text )
    {
        $key = strlen( $text ) . ':' . crc32( $text );
        if ( isset( $this->bracketCache[$key] ) )
            return $this->bracketCache[$key];
        $matches = array();
        $stack = array();
        $len = strlen( $text );
        for ( $p = 0; $p < $len; $p++ )
        {
            $c = $text[$p];
            if ( $c === '\\' )
            {
                $p++;
                continue;
            }
            if ( $c === '`' )
            {
                $run = strspn( $text, '`', $p );
                $end = $this->findBacktickRun( $text, $p + $run, $run );
                $p = ( $end !== null ? $end + $run : $p + $run ) - 1;
                continue;
            }
            if ( $c === '[' )
                $stack[] = $p;
            elseif ( $c === ']' && $stack )
                $matches[array_pop( $stack )] = $p;
        }
        if ( count( $this->bracketCache ) > 64 )
            $this->bracketCache = array();
        return $this->bracketCache[$key] = $matches;
    }

    /**
     * A link or image at $text[$k] === '[': [label](url "title"), [label][ref], [label][], [label].
     *
     * @return array|null array( 'label', 'url', 'title', 'end' )
     */
    protected function parseLink( $text, $k )
    {
        $len = strlen( $text );
        // the closing bracket of every opening one, found in one pass over the text (a scan per "[" made
        // "[[[[..." quadratic); a label is at most 999 characters (CommonMark)
        $matches = $this->bracketMatches( $text );
        $close = isset( $matches[$k] ) && $matches[$k] - $k <= 1000 ? $matches[$k] : null;
        if ( $close === null )
            return null;
        $label = substr( $text, $k + 1, $close - $k - 1 );
        $after = $close + 1;
        // inline: ( destination "title" )
        if ( $after < $len && $text[$after] === '(' )
        {
            $q = $after + 1;
            while ( $q < $len && ( $text[$q] === ' ' || $text[$q] === "\n" ) )
                $q++;
            $url = '';
            if ( $q < $len && $text[$q] === '<' )
            {
                $end = strpos( substr( $text, $q, 2050 ), '>' );
                if ( $end !== false )
                    $end += $q;
                if ( $end === false || strpos( substr( $text, $q, $end - $q ), "\n" ) !== false )
                    return null;
                $url = substr( $text, $q + 1, $end - $q - 1 );
                $q = $end + 1;
            }
            else
            {
                // bounded like CommonMark (32 nested parentheses) and by the longest address, so a run of
                // unclosed "[a](" stays linear
                $parens = 0;
                $limit = min( $len, $q + 2048 );
                $start = $q;
                while ( $q < $limit )
                {
                    $c = $text[$q];
                    if ( $c === '\\' && $q + 1 < $len )
                    {
                        $q += 2;
                        continue;
                    }
                    if ( ctype_space( $c ) )
                        break;
                    if ( $c === '(' )
                    {
                        if ( ++$parens > 32 )
                            return null;
                    }
                    elseif ( $c === ')' )
                    {
                        if ( $parens === 0 )
                            break;
                        $parens--;
                    }
                    $q++;
                }
                $url = substr( $text, $start, $q - $start );
            }
            while ( $q < $len && ( $text[$q] === ' ' || $text[$q] === "\n" ) )
                $q++;
            $title = '';
            if ( $q < $len && ( $text[$q] === '"' || $text[$q] === "'" || $text[$q] === '(' ) )
            {
                $closer = $text[$q] === '(' ? ')' : $text[$q];
                $end = $q + 1;
                $stop = min( $len, $q + 1000 );
                while ( $end < $stop && $text[$end] !== $closer )
                    $end += $text[$end] === '\\' ? 2 : 1;
                if ( $end >= $stop )
                    return null;
                $title = self::unescapeUrl( substr( $text, $q + 1, $end - $q - 1 ) );
                $q = $end + 1;
                while ( $q < $len && ( $text[$q] === ' ' || $text[$q] === "\n" ) )
                    $q++;
            }
            if ( $q >= $len || $text[$q] !== ')' )
                return null;
            return array( 'label' => $label, 'url' => self::unescapeUrl( $url ), 'title' => $title, 'end' => $q + 1 );
        }
        // reference forms
        $ref = null;
        $end = $after;
        if ( preg_match( '/\G\[([^\]]{0,200})\]/', $text, $m, 0, $after ) === 1 )
        {
            $ref = $m[1] === '' ? $label : $m[1];
            $end = $after + strlen( $m[0] );
        }
        else
            $ref = $label;
        $key = self::refLabel( $ref );
        if ( $key === '' || !isset( $this->refs[$key] ) )
            return null;
        return array( 'label' => $label, 'url' => $this->refs[$key][0], 'title' => $this->refs[$key][1], 'end' => $end );
    }

    protected static function refLabel( $label )
    {
        return mb_strtolower( trim( preg_replace( '/\s+/u', ' ', (string)$label ) ), 'UTF-8' );
    }

    protected static function unescapeUrl( $url )
    {
        return preg_replace( '/\\\\([!"#$%&\'()*+,\-.\/:;<=>?@\[\\\\\]^_`{|}~])/', '$1', $url );
    }

    /**
     * Decide what a link target may become.
     *
     * @param string $url the target as written
     * @param bool $image
     * @return array|null array( 'href' => string, 'external' => bool, 'width' => int|null, 'height' => int|null )
     */
    public function resolveUrl( $url, $image = false )
    {
        $url = html_entity_decode( trim( (string)$url ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        if ( $url === '' || strlen( $url ) > 2048 )
            return null;
        // what a browser would see as the scheme: control characters and white space are ignored by it
        $probe = preg_replace( '/[\x00-\x20\x7F]+/', '', $url );
        if ( strncmp( $probe, '//', 2 ) === 0 )
        {
            $url = 'https:' . $probe;
            $probe = $url;
        }
        if ( preg_match( '/^([A-Za-z][A-Za-z0-9+.-]*):/', $probe, $m ) === 1 )
        {
            $scheme = strtolower( $m[1] );
            if ( $image ? $scheme !== 'https' : !in_array( $scheme, array( 'http', 'https', 'mailto' ), true ) )
                return null;
            if ( $scheme !== 'mailto' )
            {
                $parts = parse_url( $probe );
                if ( !is_array( $parts ) || empty( $parts['host'] ) || preg_match( '/^[A-Za-z0-9.-]+$|^\[[0-9A-Fa-f:.]+\]$/', $parts['host'] ) !== 1 )
                    return null;
            }
            elseif ( preg_match( '/^mailto:[^\s<>"]+$/i', $probe ) !== 1 )
                return null;
            return array( 'href' => $probe, 'external' => true, 'width' => null, 'height' => null );
        }
        // no scheme: an anchor on this page or a relative path
        $anchor = '';
        $hash = strpos( $url, '#' );
        if ( $hash !== false )
        {
            $anchor = (string)substr( $url, $hash + 1 );
            $url = (string)substr( $url, 0, $hash );
        }
        $query = strpos( $url, '?' );
        if ( $query !== false )
            $url = (string)substr( $url, 0, $query );
        $anchorId = $anchor === '' ? '' : $this->options['id_prefix'] . self::anchorText( rawurldecode( $anchor ) );
        if ( $url === '' )
        {
            if ( $image || $anchorId === '' )
                return null;
            return array( 'href' => '#' . $anchorId, 'external' => false, 'width' => null, 'height' => null );
        }
        // a relative path: it must stay inside the documentation (checked here too), the resolver decides the rest
        $decoded = rawurldecode( $url );
        if ( preg_match( '/[\x00-\x1F\x7F\\\\]/', $decoded ) === 1
             || self::resolvePath( $this->path, $decoded[0] === '/' ? ltrim( $decoded, '/' ) : $decoded, $decoded[0] === '/' ) === null )
            return null;
        $resolver = $image ? $this->options['image_resolver'] : $this->options['link_resolver'];
        if ( !is_callable( $resolver ) )
            return null;
        $result = call_user_func( $resolver, $image ? $url : $url . ( $anchorId === '' ? '' : '#' . $anchorId ), $this->path );
        $width = $height = null;
        if ( is_array( $result ) )
        {
            $width = isset( $result['width'] ) ? (int)$result['width'] : null;
            $height = isset( $result['height'] ) ? (int)$result['height'] : null;
            $result = isset( $result['url'] ) ? $result['url'] : null;
        }
        if ( !is_string( $result ) || $result === '' )
            return null;
        // the resolver's own address must not carry a script scheme either
        if ( preg_match( '/^([A-Za-z][A-Za-z0-9+.-]*):/', preg_replace( '/[\x00-\x20\x7F]+/', '', $result ), $m ) === 1
             && !in_array( strtolower( $m[1] ), array( 'http', 'https' ), true ) )
            return null;
        return array( 'href' => $result, 'external' => false, 'width' => $width ?: null, 'height' => $height ?: null );
    }

    /**
     * An anchor as written in a link, reduced to the characters of an id.
     */
    protected static function anchorText( $anchor )
    {
        return preg_replace( '/[^\p{L}\p{N}\p{M}_.:-]/u', '', mb_strtolower( (string)$anchor, 'UTF-8' ) ) === ''
            ? '' : preg_replace( '/[^\p{L}\p{N}\p{M}_.:-]/u', '', (string)$anchor );
    }

    protected function renderLink( $label, $url, $title )
    {
        return $this->renderLinkHtml( $url, $this->renderInline( $label ), $title );
    }

    /**
     * A link around rendered HTML, or the HTML in span.a4-help-deadlink when the target is not allowed.
     */
    protected function renderLinkHtml( $url, $inner, $title )
    {
        $target = $this->resolveUrl( $url );
        if ( $target === null )
            return '<span class="a4-help-deadlink">' . $inner . '</span>';
        // a link cannot hold another link
        $inner = preg_replace( '/<a\b[^>]*>|<\/a>/', '', $inner );
        $attrs = ' href="' . self::escape( $target['href'] ) . '"';
        if ( $title !== '' )
            $attrs .= ' title="' . self::escape( html_entity_decode( $title, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) . '"';
        if ( $target['external'] && stripos( $target['href'], 'mailto:' ) !== 0 )
            $attrs .= ' class="a4-help-ext" rel="noopener noreferrer"';
        return '<a' . $attrs . '>' . $inner . '</a>';
    }

    protected function renderImage( $alt, $url, $title )
    {
        $altText = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( strip_tags( $this->renderInline( $alt ) ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
        $target = $this->resolveUrl( $url, true );
        if ( $target === null )
            return '<span class="a4-help-deadlink">' . self::escape( $altText ) . '</span>';
        $html = '<img class="a4-help-img" src="' . self::escape( $target['href'] ) . '" alt="' . self::escape( $altText ) . '"';
        if ( $title !== '' )
            $html .= ' title="' . self::escape( html_entity_decode( $title, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) . '"';
        if ( $target['width'] && $target['height'] )
            $html .= ' width="' . (int)$target['width'] . '" height="' . (int)$target['height'] . '"';
        return $html . ' loading="lazy" decoding="async">';
    }

    protected function renderFootnoteRef( $label )
    {
        if ( !isset( $this->footnoteOrder[$label] ) )
            $this->footnoteOrder[$label] = count( $this->footnoteOrder ) + 1;
        $n = $this->footnoteOrder[$label];
        $id = self::escape( $this->footnoteId( $label ) );
        return '<sup class="a4-help-fnref"><a href="#fn-' . $id . '" id="fnref-' . $id . '">' . $n . '</a></sup>';
    }

    /**
     * An inline HTML tag at $text[$k]: rebuilt when allowed, null to show it as text.
     *
     * @param string $text
     * @param int $k
     * @param array $open the stack of open elements: list of array( name, closing html )
     * @return array|null array( html, end offset )
     */
    protected function inlineTag( $text, $k, array &$open )
    {
        if ( preg_match( '/\G<\/([A-Za-z][A-Za-z0-9]*)\s*>/', $text, $m, 0, $k ) === 1 )
        {
            $name = strtolower( $m[1] );
            $html = '';
            for ( $j = count( $open ) - 1; $j >= 0; $j-- )
            {
                if ( $open[$j][0] === $name )
                {
                    while ( count( $open ) > $j )
                    {
                        $last = array_pop( $open );
                        $html .= $last[1];
                    }
                    return array( $html, $k + strlen( $m[0] ) );
                }
            }
            return null;
        }
        if ( preg_match( '/\G<([A-Za-z][A-Za-z0-9]*)((?:\s+[A-Za-z_:][\w:.-]*(?:\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s"\'=<>`]+))?)*)\s*(\/?)>/', $text, $m, 0, $k ) !== 1 )
            return null;
        $name = strtolower( $m[1] );
        $end = $k + strlen( $m[0] );
        $attrs = self::parseAttributes( $m[2] );
        if ( $name === 'br' || $name === 'wbr' )
            return array( '<' . $name . '>', $end );
        if ( in_array( $name, self::$inlineTags, true ) )
        {
            if ( $m[3] === '/' )
                return array( '', $end );
            $open[] = array( $name, '</' . $name . '>' );
            return array( '<' . $name . '>', $end );
        }
        if ( $name === 'abbr' )
        {
            $open[] = array( 'abbr', '</abbr>' );
            return array( '<abbr' . ( isset( $attrs['title'] ) ? ' title="' . self::escape( $attrs['title'] ) . '"' : '' ) . '>', $end );
        }
        if ( $name === 'img' )
        {
            if ( !isset( $attrs['src'] ) )
                return array( '', $end );
            $html = $this->renderImage( isset( $attrs['alt'] ) ? $attrs['alt'] : '', $attrs['src'], isset( $attrs['title'] ) ? $attrs['title'] : '' );
            return array( $html, $end );
        }
        if ( $name === 'a' )
        {
            if ( isset( $attrs['href'] ) )
            {
                $target = $this->resolveUrl( $attrs['href'] );
                if ( $target === null )
                {
                    $open[] = array( 'a', '</span>' );
                    return array( '<span class="a4-help-deadlink">', $end );
                }
                $html = '<a href="' . self::escape( $target['href'] ) . '"';
                if ( $target['external'] && stripos( $target['href'], 'mailto:' ) !== 0 )
                    $html .= ' class="a4-help-ext" rel="noopener noreferrer"';
                $open[] = array( 'a', '</a>' );
                return array( $html . '>', $end );
            }
            $id = isset( $attrs['id'] ) ? $attrs['id'] : ( isset( $attrs['name'] ) ? $attrs['name'] : '' );
            $id = self::anchorText( $id );
            $html = $id === '' ? '<span>' : '<span class="a4-help-target" id="' . self::escape( $this->options['id_prefix'] . $id ) . '">';
            if ( $m[3] === '/' )
                return array( $html . '</span>', $end );
            $open[] = array( 'a', '</span>' );
            return array( $html, $end );
        }
        return null;
    }

    /**
     * The attributes of a tag, names lower case, values with their entities decoded.
     */
    protected static function parseAttributes( $source )
    {
        $attrs = array();
        if ( preg_match_all( '/([A-Za-z_:][\w:.-]*)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'=<>`]+)))?/', $source, $all, PREG_SET_ORDER ) )
        {
            foreach ( $all as $a )
            {
                $value = isset( $a[4] ) && $a[4] !== '' ? $a[4] : ( isset( $a[3] ) && $a[3] !== '' ? $a[3] : ( isset( $a[2] ) ? $a[2] : '' ) );
                $name = strtolower( $a[1] );
                if ( !isset( $attrs[$name] ) )
                    $attrs[$name] = html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
            }
        }
        return $attrs;
    }

    /**
     * An interface word, translated.
     */
    public function t( $text )
    {
        if ( is_callable( $this->options['translate'] ) )
            return (string)call_user_func( $this->options['translate'], $text );
        if ( class_exists( 'ezpI18n', false ) )
        {
            try
            {
                return ezpI18n::tr( 'kernel/exphelp', $text );
            }
            catch ( Throwable $e )
            {
            }
        }
        return $text;
    }
}
