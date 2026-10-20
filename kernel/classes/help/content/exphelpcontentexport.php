<?php
/**
 * E-books of a manual of help maintained as content (doc/en/us/features/6.0/help-content.md, "E-books"): EPUB 3 (written
 * here, pure PHP with ZipArchive) and PDF (with the help's PDF writer, expHelpPdfWriter, when it is installed).
 *
 * Only Released chapters and topics go into an e-book, and only those the user (on the command line: --login) may
 * read: a preview is never exported. Topics keep their order in the manual (topicrefs), chapters nest. Every topic is
 * rendered by the help's safe Markdown renderer with its own id prefix, so anchors stay unique in the book; links
 * to other topics of the book become links inside the book, others to the topic's address in the help.
 *
 * EPUB: EPUB 3.3 with a navigation document, xml:lang, and the accessibility metadata of EPUB Accessibility 1.1
 * (schema.org accessMode, accessModeSufficient, accessibilityFeature, accessibilityHazard, accessibilitySummary).
 * Figures are put into the book from the storage (alt text and caption kept). Checking it with epubcheck and Ace by
 * DAISY is left to the site (neither is shipped).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentExport
{
    /** @var expHelpContentProvider */
    protected $provider;

    /** @var array the manual */
    protected $manual;

    /** @var array the book: list of entries (kind chapter|topic, level, title, shortdesc, topic, id, children) */
    protected $entries = null;

    public function __construct( expHelpContentProvider $provider, array $manual )
    {
        $this->provider = $provider;
        $this->manual = $manual;
    }

    /** @return string[] the formats this installation can write */
    public static function formats()
    {
        $formats = array();
        if ( class_exists( 'ZipArchive' ) )
            $formats[] = 'epub';
        if ( class_exists( 'expHelpPdfWriter' ) )
            $formats[] = 'pdf';
        return $formats;
    }

    /**
     * The Released entries of the manual in reading order, nested
     *
     * @return array
     */
    public function entries()
    {
        if ( $this->entries === null )
            $this->entries = $this->collect( $this->manual, 1, array( (int)$this->manual['object_id'] ) );
        return $this->entries;
    }

    protected function collect( array $map, $level, array $seen )
    {
        $entries = array();
        if ( $level > 6 )
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
            // released and readable only: an e-book never carries a preview
            if ( $item === null || !empty( $item['preview'] ) || $item['state'] !== expHelpContentSchema::STATE_RELEASED )
                continue;
            if ( $found['kind'] === 'maps' )
                $entries[] = array( 'kind' => 'chapter', 'level' => $level, 'title' => $item['title'], 'shortdesc' => $item['shortdesc'],
                                    'id' => 'ch-' . $objectId, 'children' => $this->collect( $item, $level + 1, array_merge( $seen, array( $objectId ) ) ) );
            elseif ( $found['kind'] === 'topics' && $item['help_id'] !== '' )
                $entries[] = array( 'kind' => 'topic', 'level' => $level, 'title' => $item['title'], 'shortdesc' => $item['shortdesc'],
                                    'id' => self::topicAnchor( $item['help_id'] ), 'topic' => $item, 'children' => array() );
        }
        return $entries;
    }

    /**
     * The flat list of the book's topics
     *
     * @param array|null $entries
     * @return array help_id => entry
     */
    public function topics( ?array $entries = null )
    {
        $list = array();
        foreach ( $entries === null ? $this->entries() : $entries as $entry )
        {
            if ( $entry['kind'] === 'topic' )
                $list[$entry['topic']['help_id']] = $entry;
            else
                $list += $this->topics( $entry['children'] );
        }
        return $list;
    }

    /** @return string the anchor of a topic in a book */
    public static function topicAnchor( $helpId )
    {
        return 't-' . preg_replace( '/[^a-z0-9-]/', '-', (string)$helpId );
    }

    /**
     * Moves every Markdown heading down by $by levels (outside fenced code), at most to level 6
     *
     * @param string $markdown
     * @param int $by
     * @return string
     */
    public static function demoteHeadings( $markdown, $by )
    {
        $out = array();
        $fence = false;
        foreach ( preg_split( '/\r\n|\r|\n/', (string)$markdown ) as $line )
        {
            if ( preg_match( '/^\s*(```|~~~)/', $line ) )
                $fence = !$fence;
            elseif ( !$fence && preg_match( '/^(#{1,6})(\s+.*)$/', $line, $m ) )
                $line = str_repeat( '#', min( 6, strlen( $m[1] ) + $by ) ) . $m[2];
            $out[] = $line;
        }
        return implode( "\n", $out );
    }

    /**
     * A topic's HTML for a book: the renderer's safe HTML of its fields, headings below the topic's title
     *
     * @param array $topic
     * @param int $level the heading level of the topic's title
     * @param callable $link function( help_id, anchor ): string|null, the address of another topic
     * @return string
     */
    public function topicHtml( array $topic, $level, $link )
    {
        $renderer = new expHelpMarkdownRenderer( array(
            'id_prefix' => self::topicAnchor( $topic['help_id'] ) . '-',
            'link_resolver' => function ( $href ) use ( $link ) {
                if ( preg_match( '#^/?content/([a-z0-9.-]{1,80})\.md(?:\#(.*))?$#', (string)$href, $m ) )
                    return call_user_func( $link, $m[1], isset( $m[2] ) ? $m[2] : '' );
                return expHelpLinks::link( $href, '' );
            },
            'image_resolver' => array( 'expHelpLinks', 'image' ) ) );
        $markdown = expHelpContentProvider::markdown( $topic, function ( $text ) { return $text; } );
        $rendered = $renderer->render( self::demoteHeadings( $markdown, max( 0, $level + 1 - 2 ) ), '' );
        return (string)$rendered['html'];
    }

    // ---------------------------------------------------------------- EPUB

    /**
     * The EPUB of the manual
     *
     * @param string $file where to write it
     * @return array array( 'file' => path, 'bytes' => int, 'topics' => int, 'figures' => int )
     * @throws RuntimeException
     */
    public function writeEpub( $file )
    {
        if ( !class_exists( 'ZipArchive' ) )
            throw new RuntimeException( 'EPUB needs the PHP zip extension' );
        $topics = $this->topics();
        if ( !$topics )
            throw new RuntimeException( 'The manual has no Released topic the user may read' );
        $language = self::xmlLang( isset( $this->manual['language'] ) ? $this->manual['language'] : '' );
        $uid = 'urn:exponential:help:' . $this->manual['remote_id'] . ':' . gmdate( 'YmdHis', (int)$this->manual['modified'] );

        $files = array();       // path in OEBPS => content
        $manifest = array();    // id => array( href, media-type, properties )
        $spine = array();
        $images = array();      // storage url => array( href, id, media )
        $topicFile = array();
        $i = 0;
        foreach ( $topics as $helpId => $entry )
            $topicFile[$helpId] = sprintf( 'topic-%03d.xhtml', ++$i );
        $link = function ( $helpId, $anchor ) use ( $topicFile ) {
            if ( isset( $topicFile[$helpId] ) )
                return $topicFile[$helpId] . '#' . ( $anchor !== '' ? self::topicAnchor( $helpId ) . '-' . $anchor : self::topicAnchor( $helpId ) );
            return expHelpLinks::absolute( expHelp::url( 'topic', $helpId ) );
        };

        $files['cover.xhtml'] = self::xhtml( $language, $this->manual['title'],
            '<section epub:type="titlepage" class="titlepage"><h1>' . self::h( $this->manual['title'] ) . '</h1>'
            . ( $this->manual['subtitle'] !== '' ? '<p class="subtitle">' . self::h( $this->manual['subtitle'] ) . '</p>' : '' )
            . ( $this->manual['shortdesc'] !== '' ? '<p>' . self::h( $this->manual['shortdesc'] ) . '</p>' : '' )
            . ( $this->manual['edition'] !== '' ? '<p>' . self::h( 'Edition ' . $this->manual['edition'] ) . '</p>' : '' )
            . ( $this->manual['authors'] ? '<p>' . self::h( implode( ', ', $this->manual['authors'] ) ) . '</p>' : '' )
            . ( $this->manual['license'] !== '' ? '<p class="license">' . self::h( $this->manual['license'] ) . '</p>' : '' )
            . '</section>' );
        $manifest['cover'] = array( 'cover.xhtml', 'application/xhtml+xml', '' );
        $spine[] = 'cover';

        $figures = 0;
        $chapterFile = array();
        $c = 0;
        foreach ( $this->flatten( $this->entries() ) as $entry )
        {
            if ( $entry['kind'] !== 'chapter' )
                continue;
            $chapterFile[$entry['id']] = sprintf( 'chapter-%03d.xhtml', ++$c );
            $level = min( 6, $entry['level'] );
            $files[$chapterFile[$entry['id']]] = self::xhtml( $language, $entry['title'],
                '<section epub:type="part" id="' . self::h( $entry['id'] ) . '"><h' . $level . '>' . self::h( $entry['title'] ) . '</h' . $level . '>'
                . ( $entry['shortdesc'] !== '' ? '<p class="shortdesc">' . self::h( $entry['shortdesc'] ) . '</p>' : '' ) . '</section>' );
            $manifest['chapter' . sprintf( '%03d', $c )] = array( $chapterFile[$entry['id']], 'application/xhtml+xml', '' );
        }
        $this->chapterFile = $chapterFile;
        foreach ( $this->flatten( $this->entries() ) as $flat )
        {
            if ( $flat['kind'] === 'chapter' )
            {
                $spine[] = 'chapter' . substr( $chapterFile[$flat['id']], 8, 3 );
                continue;
            }
            $helpId = $flat['topic']['help_id'];
            $entry = $topics[$helpId];
            $topic = $entry['topic'];
            $level = min( 6, $entry['level'] );
            $body = '<section epub:type="chapter" id="' . self::topicAnchor( $helpId ) . '" aria-labelledby="' . self::topicAnchor( $helpId ) . '-title">'
                  . '<h' . $level . ' id="' . self::topicAnchor( $helpId ) . '-title">' . self::h( $topic['title'] ) . '</h' . $level . '>'
                  . ( $topic['shortdesc'] !== '' ? '<p class="shortdesc">' . self::h( $topic['shortdesc'] ) . '</p>' : '' )
                  . self::xhtmlFragment( $this->topicHtml( $topic, $level, $link ) );
            foreach ( $this->provider->relations( $topic )['figures'] as $figure )
            {
                $path = self::storageFile( $figure['image_url'] );
                if ( $path === null )
                    continue;
                if ( !isset( $images[$figure['image_url']] ) )
                {
                    $media = self::imageMedia( $path );
                    if ( $media === null )
                        continue;
                    $n = count( $images ) + 1;
                    $href = sprintf( 'images/figure-%03d.%s', $n, $media[1] );
                    $images[$figure['image_url']] = array( $href, 'img' . $n, $media[0] );
                    $files[$href] = (string)file_get_contents( $path );
                    $manifest['img' . $n] = array( $href, $media[0], '' );
                }
                $body .= '<figure><img src="' . self::h( $images[$figure['image_url']][0] ) . '" alt="' . self::h( $figure['alt_text'] ) . '"/>'
                       . ( $figure['caption'] !== '' ? '<figcaption>' . self::h( $figure['caption'] ) . '</figcaption>' : '' ) . '</figure>';
                $figures++;
            }
            $body .= '</section>';
            $id = 'topic' . substr( $topicFile[$helpId], 6, 3 );
            $files[$topicFile[$helpId]] = self::xhtml( $language, $topic['title'], $body );
            $manifest[$id] = array( $topicFile[$helpId], 'application/xhtml+xml', '' );
            $spine[] = $id;
        }

        $files['nav.xhtml'] = self::xhtml( $language, $this->manual['title'],
            '<nav epub:type="toc" id="toc" role="doc-toc"><h1>' . self::h( 'Contents' ) . '</h1>' . $this->navList( $this->entries(), $topicFile ) . '</nav>' );
        $manifest['nav'] = array( 'nav.xhtml', 'application/xhtml+xml', 'nav' );
        $files['style.css'] = "body { font-family: serif; line-height: 1.5; }\nh1, h2, h3, h4 { font-family: sans-serif; }\n"
                            . ".shortdesc { font-style: italic; }\nfigure { margin: 1em 0; }\nimg { max-width: 100%; }\n"
                            . "table { border-collapse: collapse; }\nth, td { border: 1px solid #999; padding: 0.2em 0.4em; }\n"
                            . "pre { white-space: pre-wrap; }\n";
        $manifest['css'] = array( 'style.css', 'text/css', '' );

        $files['content.opf'] = $this->opf( $uid, $language, $manifest, $spine );

        $dir = dirname( $file );
        if ( !is_dir( $dir ) )
            eZDir::mkdir( $dir, false, true );
        $tmp = $file . '.' . getmypid() . '.tmp';
        $zip = new ZipArchive();
        if ( $zip->open( $tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE ) !== true )
            throw new RuntimeException( "Cannot write $file" );
        // the mimetype first and stored, as the OCF requires
        $zip->addFromString( 'mimetype', 'application/epub+zip' );
        $zip->setCompressionName( 'mimetype', ZipArchive::CM_STORE );
        $zip->addFromString( 'META-INF/container.xml', '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container"><rootfiles>'
            . '<rootfile full-path="OEBPS/content.opf" media-type="application/oebps-package+xml"/></rootfiles></container>' );
        foreach ( $files as $path => $content )
            $zip->addFromString( 'OEBPS/' . $path, $content );
        // every entry dated by the book's content, not by the clock: the same content gives the same file, so a stored
        // e-book is only published as a new version when its content changed (expHelpContentEbooks)
        if ( method_exists( $zip, 'setMtimeName' ) )
        {
            for ( $i = 0; $i < $zip->numFiles; $i++ )
                $zip->setMtimeName( (string)$zip->getNameIndex( $i ), $this->modified() );
        }
        $zip->close();
        if ( !@rename( $tmp, $file ) )
            throw new RuntimeException( "Cannot write $file" );
        @chmod( $file, eZFile::fileMode( 0644 ) );
        return array( 'file' => $file, 'bytes' => (int)filesize( $file ), 'topics' => count( $topics ), 'figures' => $figures );
    }

    /** @var array chapter id => its file in the EPUB */
    protected $chapterFile = array();

    /**
     * When the book's content last changed: the newest modification of the manual and of its chapters and topics
     *
     * @return int
     */
    public function modified()
    {
        $modified = (int)$this->manual['modified'];
        foreach ( $this->flatten( $this->entries() ) as $entry )
        {
            if ( $entry['kind'] === 'topic' )
                $modified = max( $modified, (int)$entry['topic']['modified'] );
        }
        return $modified;
    }

    /**
     * The entries in reading order, chapters before their children
     *
     * @param array $entries
     * @return array
     */
    public function flatten( array $entries )
    {
        $list = array();
        foreach ( $entries as $entry )
        {
            $list[] = $entry;
            if ( $entry['kind'] === 'chapter' )
                $list = array_merge( $list, $this->flatten( $entry['children'] ) );
        }
        return $list;
    }

    protected function navList( array $entries, array $topicFile )
    {
        if ( !$entries )
            return '';
        $html = '<ol>';
        foreach ( $entries as $entry )
        {
            if ( $entry['kind'] === 'topic' )
                $html .= '<li><a href="' . self::h( $topicFile[$entry['topic']['help_id']] ) . '">' . self::h( $entry['title'] ) . '</a></li>';
            else
            {
                $target = isset( $this->chapterFile[$entry['id']] ) ? $this->chapterFile[$entry['id']] : '';
                $html .= '<li>' . ( $target !== '' ? '<a href="' . self::h( $target ) . '">' . self::h( $entry['title'] ) . '</a>'
                                                  : '<span>' . self::h( $entry['title'] ) . '</span>' )
                       . $this->navList( $entry['children'], $topicFile ) . '</li>';
            }
        }
        return $html . '</ol>';
    }

    protected function opf( $uid, $language, array $manifest, array $spine )
    {
        $meta = array(
            '<dc:identifier id="uid">' . self::h( $uid ) . '</dc:identifier>',
            '<dc:title>' . self::h( $this->manual['title'] ) . '</dc:title>',
            '<dc:language>' . self::h( $language ) . '</dc:language>',
            '<meta property="dcterms:modified">' . gmdate( 'Y-m-d\TH:i:s\Z', $this->modified() ) . '</meta>',
            '<dc:publisher>Exponential</dc:publisher>',
            '<meta property="schema:accessMode">textual</meta>',
            '<meta property="schema:accessMode">visual</meta>',
            '<meta property="schema:accessModeSufficient">textual</meta>',
            '<meta property="schema:accessibilityFeature">structuralNavigation</meta>',
            '<meta property="schema:accessibilityFeature">tableOfContents</meta>',
            '<meta property="schema:accessibilityFeature">alternativeText</meta>',
            '<meta property="schema:accessibilityHazard">none</meta>',
            '<meta property="schema:accessibilitySummary">Help topics with headings, a table of contents and alternative text for every figure.</meta>',
            '<meta property="dcterms:conformsTo">EPUB Accessibility 1.1 - WCAG 2.2 Level AA</meta>',
        );
        foreach ( $this->manual['authors'] as $author )
            $meta[] = '<dc:creator>' . self::h( $author ) . '</dc:creator>';
        if ( $this->manual['shortdesc'] !== '' )
            $meta[] = '<dc:description>' . self::h( $this->manual['shortdesc'] ) . '</dc:description>';
        if ( $this->manual['license'] !== '' )
            $meta[] = '<dc:rights>' . self::h( $this->manual['license'] ) . '</dc:rights>';
        $items = array();
        foreach ( $manifest as $id => $item )
            $items[] = '<item id="' . self::h( $id ) . '" href="' . self::h( $item[0] ) . '" media-type="' . self::h( $item[1] ) . '"'
                     . ( $item[2] !== '' ? ' properties="' . self::h( $item[2] ) . '"' : '' ) . '/>';
        $refs = array();
        foreach ( $spine as $id )
            $refs[] = '<itemref idref="' . self::h( $id ) . '"/>';
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
             . '<package xmlns="http://www.idpf.org/2007/opf" version="3.0" unique-identifier="uid" xml:lang="' . self::h( $language ) . '">'
             . '<metadata xmlns:dc="http://purl.org/dc/elements/1.1/">' . implode( '', $meta ) . '</metadata>'
             . '<manifest>' . implode( '', $items ) . '</manifest>'
             . '<spine>' . implode( '', $refs ) . '</spine></package>';
    }

    /**
     * An XHTML content document
     */
    public static function xhtml( $language, $title, $body )
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n<!DOCTYPE html>\n"
             . '<html xmlns="http://www.w3.org/1999/xhtml" xmlns:epub="http://www.idpf.org/2007/ops" xml:lang="' . self::h( $language )
             . '" lang="' . self::h( $language ) . '"><head><meta charset="UTF-8"/><title>' . self::h( $title ) . '</title>'
             . '<link rel="stylesheet" type="text/css" href="style.css"/></head><body>' . $body . '</body></html>';
    }

    /**
     * The renderer's HTML as well-formed XHTML (void elements closed, entities numeric, attributes quoted)
     *
     * @param string $html
     * @return string
     */
    public static function xhtmlFragment( $html )
    {
        $html = trim( (string)$html );
        if ( $html === '' )
            return '';
        $doc = new DOMDocument( '1.0', 'UTF-8' );
        $previous = libxml_use_internal_errors( true );
        $doc->loadHTML( '<?xml encoding="UTF-8"?><html><body><div id="__x">' . $html . '</div></body></html>', LIBXML_NONET | LIBXML_NOERROR );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        $root = $doc->getElementById( '__x' );
        if ( !$root )
            return '';
        // buttons and scripts of the screen (copy buttons) have no place in a book
        $xpath = new DOMXPath( $doc );
        foreach ( iterator_to_array( $xpath->query( './/button|.//script|.//style|.//form|.//input|.//a[contains(concat(" ", @class, " "), " a4-help-anchor ")]', $root ) ) as $node )
            $node->parentNode->removeChild( $node );
        $out = '';
        foreach ( $root->childNodes as $child )
            $out .= $doc->saveXML( $child );
        return $out;
    }

    // ---------------------------------------------------------------- PDF

    /**
     * The PDF of the manual (one document: the title block, then each chapter and topic)
     *
     * @param string $file
     * @return array array( 'file', 'bytes', 'topics' )
     * @throws RuntimeException
     */
    public function writePdf( $file )
    {
        if ( !class_exists( 'expHelpPdfWriter' ) )
            throw new RuntimeException( 'PDF needs the help\'s PDF writer (expHelpPdfWriter), which this installation does not have yet' );
        $topics = $this->topics();
        if ( !$topics )
            throw new RuntimeException( 'The manual has no Released topic the user may read' );
        $link = function ( $helpId, $anchor ) use ( $topics ) {
            if ( isset( $topics[$helpId] ) )
                return '#' . ( $anchor !== '' ? self::topicAnchor( $helpId ) . '-' . $anchor : self::topicAnchor( $helpId ) );
            return expHelp::url( 'topic', $helpId );
        };
        $html = $this->bookHtml( $this->entries(), $link );
        $writer = new expHelpPdfWriter( array(
            'fonts' => expHelpPdfWriter::defaultFonts(),
            'base_url' => class_exists( 'eZSys' ) ? eZSys::serverURL() : '',
            'image_resolver' => function ( $src ) { return self::storageFile( $src ); },
        ) );
        $pdf = $writer->render( array( 'title' => $this->manual['title'],
                                       'summary' => trim( $this->manual['subtitle'] . ( $this->manual['subtitle'] !== '' && $this->manual['shortdesc'] !== '' ? ' - ' : '' ) . $this->manual['shortdesc'] ),
                                       'path' => '', 'changed' => $this->modified(),
                                       'url' => expHelpLinks::absolute( expHelp::url( 'manual', $this->manual['remote_id'] ) ),
                                       'notice' => $this->manual['license'] ), $html );
        $dir = dirname( $file );
        if ( !is_dir( $dir ) )
            eZDir::mkdir( $dir, false, true );
        if ( @file_put_contents( $file, $pdf ) === false )
            throw new RuntimeException( "Cannot write $file" );
        @chmod( $file, eZFile::fileMode( 0644 ) );
        return array( 'file' => $file, 'bytes' => strlen( $pdf ), 'topics' => count( $topics ) );
    }

    /**
     * One HTML of the whole book for the PDF writer: chapters as headings, topics with their figures
     */
    public function bookHtml( array $entries, $link )
    {
        $html = '';
        foreach ( $entries as $entry )
        {
            $level = min( 6, $entry['level'] );
            if ( $entry['kind'] === 'chapter' )
            {
                $html .= '<h' . $level . ' id="' . self::h( $entry['id'] ) . '">' . self::h( $entry['title'] ) . '</h' . $level . '>'
                       . ( $entry['shortdesc'] !== '' ? '<p><em>' . self::h( $entry['shortdesc'] ) . '</em></p>' : '' )
                       . $this->bookHtml( $entry['children'], $link );
                continue;
            }
            $topic = $entry['topic'];
            $html .= '<h' . $level . ' id="' . self::h( $entry['id'] ) . '">' . self::h( $topic['title'] ) . '</h' . $level . '>'
                   . ( $topic['shortdesc'] !== '' ? '<p><em>' . self::h( $topic['shortdesc'] ) . '</em></p>' : '' )
                   . $this->topicHtml( $topic, $level, $link );
            foreach ( $this->provider->relations( $topic )['figures'] as $figure )
                $html .= '<figure><img src="' . self::h( $figure['image_url'] ) . '" alt="' . self::h( $figure['alt_text'] ) . '">'
                       . ( $figure['caption'] !== '' ? '<figcaption>' . self::h( $figure['caption'] ) . '</figcaption>' : '' ) . '</figure>';
        }
        return $html;
    }

    // ---------------------------------------------------------------- helpers

    /**
     * The local file of a storage URL (/var/<site>/storage/images/...), null when it is not one or does not exist
     *
     * @param string $url
     * @return string|null
     */
    public static function storageFile( $url )
    {
        $path = ltrim( (string)parse_url( (string)$url, PHP_URL_PATH ), '/' );
        $www = trim( class_exists( 'eZSys' ) ? (string)eZSys::wwwDir() : '', '/' );
        if ( $www !== '' && strpos( $path, $www . '/' ) === 0 )
            $path = substr( $path, strlen( $www ) + 1 );
        if ( $path === '' || strpos( $path, 'var/' ) !== 0 || strpos( $path, '..' ) !== false || !preg_match( '#/storage/#', $path ) )
            return null;
        if ( class_exists( 'eZClusterFileHandler' ) )
        {
            $handler = eZClusterFileHandler::instance( $path );
            if ( method_exists( $handler, 'fetch' ) && !is_file( $path ) )
                $handler->fetch();
        }
        $file = realpath( $path );
        return $file !== false && is_file( $file ) ? $file : null;
    }

    /**
     * @param string $file
     * @return array|null array( media type, suffix ) of PNG, JPEG, GIF, WebP, SVG is refused
     */
    public static function imageMedia( $file )
    {
        $info = @getimagesize( $file );
        $types = array( IMAGETYPE_PNG => array( 'image/png', 'png' ), IMAGETYPE_JPEG => array( 'image/jpeg', 'jpg' ),
                        IMAGETYPE_GIF => array( 'image/gif', 'gif' ), IMAGETYPE_WEBP => array( 'image/webp', 'webp' ) );
        return is_array( $info ) && isset( $types[$info[2]] ) ? $types[$info[2]] : null;
    }

    /**
     * A BCP 47 language tag of a locale (eng-US -> en-US), "en" when there is none
     *
     * @param string $locale
     * @return string
     */
    public static function xmlLang( $locale )
    {
        if ( class_exists( 'eZLocale' ) && preg_match( '/^[a-z]{3}-[A-Z]{2}$/', (string)$locale ) )
        {
            $l = eZLocale::instance( $locale );
            $code = $l ? (string)$l->httpLocaleCode() : '';
            if ( $code !== '' )
                return $code;
        }
        if ( preg_match( '/^([a-z]{2})[-_]([A-Za-z]{2})$/', (string)$locale, $m ) )
            return $m[1] . '-' . strtoupper( $m[2] );
        return 'en';
    }

    protected static function h( $text )
    {
        return htmlspecialchars( (string)$text, ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE, 'UTF-8' );
    }
}

?>
