<?php
/**
 * File containing the expHelpLinkCheck class.
 *
 * Checks the links and images of the help pages (doc/en/us/features/6.0/help-system.md, "Checking the documentation";
 * "./console exp:help check"). Every inline link and image and every reference definition of a Markdown page,
 * outside code, is resolved as the viewer resolves it:
 *
 *   error    missing     a relative link or image whose file does not exist
 *   error    outside     a relative link that leaves the installation root
 *   error    unsafe      a javascript:, vbscript: or data: link (the renderer drops it)
 *   error    image       an image that is not an image of the help (the viewer cannot show it)
 *   warning  anchor      a #anchor that no heading of the target page has
 *   warning  absolute    a site-absolute link (/...), which the viewer cannot resolve
 *   notice   not-served  an existing file that is not a help page (source code, settings, an excluded or untracked
 *                        file): the viewer links it to the repository, not to itself
 *
 * External links (http, https, mailto ...) are counted, never fetched.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpLinkCheck
{
    /** Problem kind => severity */
    const SEVERITY = array( 'missing' => 'error', 'outside' => 'error', 'unsafe' => 'error', 'image' => 'error',
                            'anchor' => 'warning', 'absolute' => 'warning', 'not-served' => 'notice' );

    /** @var expHelpDocumentIndex */
    protected $index;

    /** @var array counts of the last check() */
    protected $stats = array();

    /**
     * @param expHelpDocumentIndex|null $index default expHelpDocumentIndex::instance()
     */
    public function __construct( ?expHelpDocumentIndex $index = null )
    {
        $this->index = $index !== null ? $index : expHelpDocumentIndex::instance();
    }

    /**
     * Checks the pages (all, or those whose id starts with $below).
     *
     * @param string|null $below an id prefix (e.g. 'guides/' or 'guides/caches.md')
     * @return array list of problems: array( 'id', 'line', 'kind' => link|image, 'target', 'problem', 'severity', 'resolved' )
     */
    public function check( $below = null )
    {
        $problems = array();
        $this->stats = array( 'pages' => 0, 'links' => 0, 'images' => 0, 'external' => 0, 'error' => 0, 'warning' => 0, 'notice' => 0 );
        foreach ( $this->index->documents() as $id => $document )
        {
            if ( $below !== null && $below !== '' && strncmp( $id, $below, strlen( $below ) ) !== 0 )
                continue;
            ++$this->stats['pages'];
            if ( $document['format'] !== 'markdown' )
                continue;
            $body = $this->index->body( $id );
            if ( $body === null )
                continue;
            foreach ( self::links( $body ) as $link )
            {
                ++$this->stats[$link['kind'] === 'image' ? 'images' : 'links'];
                $problem = $this->checkLink( $document, $link );
                if ( $problem === null )
                    continue;
                if ( $problem === 'external' )
                {
                    ++$this->stats['external'];
                    continue;
                }
                ++$this->stats[$problem['severity']];
                $problems[] = $problem + array( 'id' => $id, 'line' => $link['line'], 'kind' => $link['kind'], 'target' => $link['target'] );
            }
        }
        return $problems;
    }

    /**
     * The counts of the last check(): pages, links, images, external, error, warning, notice.
     *
     * @return array
     */
    public function stats()
    {
        return $this->stats;
    }

    /**
     * Checks one link of a page.
     *
     * @param array $document the page's index entry
     * @param array $link array( 'kind', 'target', 'line' )
     * @return array|string|null null when it is fine, 'external', else array( 'problem', 'severity', 'resolved' )
     */
    public function checkLink( array $document, array $link )
    {
        $target = trim( $link['target'] );
        if ( $target === '' )
            return self::problem( 'missing', '' );
        if ( preg_match( '/^([a-z][a-z0-9+.-]*):/i', $target, $m ) )
        {
            $scheme = strtolower( $m[1] );
            if ( in_array( $scheme, array( 'javascript', 'vbscript', 'data', 'file' ), true ) )
                return self::problem( 'unsafe', '' );
            return 'external';
        }
        if ( strncmp( $target, '//', 2 ) === 0 )
            return 'external';
        if ( $target[0] === '#' )
            return $this->checkAnchor( $document, substr( $target, 1 ), $document['path'] );
        if ( $target[0] === '/' )
            return self::problem( 'absolute', '' );

        $anchor = '';
        if ( ( $hash = strpos( $target, '#' ) ) !== false )
        {
            $anchor = substr( $target, $hash + 1 );
            $target = substr( $target, 0, $hash );
        }
        if ( ( $query = strpos( $target, '?' ) ) !== false )
            $target = substr( $target, 0, $query );
        $resolved = self::resolve( dirname( $document['path'] ), rawurldecode( $target ) );
        if ( $resolved === null )
            return self::problem( 'outside', '' );

        $rootDir = $this->index->allowlist()->rootDir();
        $id = $this->index->idOf( $resolved );
        if ( $link['kind'] === 'image' )
        {
            if ( $id !== null && $this->index->assetInfo( $id ) !== null )
                return null;
            return self::problem( file_exists( $rootDir . '/' . $resolved ) ? 'image' : 'missing', $resolved );
        }
        if ( $id !== null && ( $target = $this->index->document( $id ) ) !== null )
            return $anchor === '' ? null : $this->checkAnchor( $target, $anchor, $resolved );
        if ( $id !== null && $this->index->assetInfo( $id ) !== null )
            return null;
        if ( is_dir( $rootDir . '/' . $resolved ) )
        {
            // a folder of the help (the viewer shows its index page, or its list of pages)
            if ( $id === '' || $resolved === 'doc' )
                return null;
            if ( $id !== null )
                foreach ( array_keys( $this->index->documents() ) as $documentId )
                    if ( strncmp( $documentId, $id . '/', strlen( $id ) + 1 ) === 0 )
                        return null;
        }
        return self::problem( file_exists( $rootDir . '/' . $resolved ) ? 'not-served' : 'missing', $resolved );
    }

    /**
     * Whether a page has a heading with the anchor.
     *
     * @param array $document
     * @param string $anchor
     * @param string $resolved
     * @return array|null
     */
    protected function checkAnchor( array $document, $anchor, $resolved )
    {
        if ( $anchor === '' || $document['format'] !== 'markdown' )
            return null;
        $anchor = rawurldecode( $anchor );
        foreach ( $document['headings'] as $heading )
            if ( $heading['anchor'] === $anchor || $heading['anchor'] === strtolower( $anchor ) )
                return null;
        return self::problem( 'anchor', $resolved . '#' . $anchor );
    }

    /**
     * @param string $problem
     * @param string $resolved
     * @return array
     */
    protected static function problem( $problem, $resolved )
    {
        return array( 'problem' => $problem, 'severity' => self::SEVERITY[$problem], 'resolved' => $resolved );
    }

    /**
     * A relative path resolved against a directory, both relative to the installation root; null when it would
     * leave the root or holds a NUL or backslash.
     *
     * @param string $dir
     * @param string $relative
     * @return string|null
     */
    public static function resolve( $dir, $relative )
    {
        if ( strpbrk( $relative, "\0\\" ) !== false )
            return null;
        $parts = $dir === '.' || $dir === '' ? array() : explode( '/', $dir );
        foreach ( explode( '/', $relative ) as $segment )
        {
            if ( $segment === '' || $segment === '.' )
                continue;
            if ( $segment === '..' )
            {
                if ( !$parts )
                    return null;
                array_pop( $parts );
                continue;
            }
            $parts[] = $segment;
        }
        return implode( '/', $parts );
    }

    /**
     * The links, images and reference definitions of a Markdown text, outside fenced and indented code and code spans.
     *
     * @param string $markdown
     * @return array list of array( 'kind' => link|image, 'target', 'line' )
     */
    public static function links( $markdown )
    {
        $links = array();
        $fence = null;
        $previousBlank = true;
        foreach ( preg_split( '/\r\n|\r|\n/', (string)$markdown ) as $number => $line )
        {
            if ( $fence !== null )
            {
                if ( preg_match( '/^ {0,3}(`{3,}|~{3,})\s*$/', $line, $m ) && $m[1][0] === $fence[0] && strlen( $m[1] ) >= strlen( $fence ) )
                    $fence = null;
                continue;
            }
            if ( preg_match( '/^ {0,3}(`{3,}|~{3,})/', $line, $m ) )
            {
                $fence = $m[1];
                continue;
            }
            $blank = trim( $line ) === '';
            if ( $previousBlank && preg_match( '/^( {4}|\t)/', $line ) )
                continue; // indented code
            $previousBlank = $blank;
            if ( preg_match( '/^ {0,3}\[([^\]]+)\]:\s*(<[^>]*>|\S+)/', $line, $m ) )
            {
                $links[] = array( 'kind' => 'link', 'target' => trim( $m[2], '<>' ), 'line' => $number + 1 );
                continue;
            }
            // code spans keep their length, so nothing inside them is a link
            $line = preg_replace_callback( '/(`+)(.+?)\1/', function ( $m ) { return str_repeat( ' ', strlen( $m[0] ) ); }, $line );
            // a backslash-escaped bracket or parenthesis is text, as the renderer reads it: "\[x\](y)" is no link
            $line = preg_replace( '/\\\\[\[\]()!]/', '  ', $line );
            if ( strpos( $line, '](' ) === false )
                continue;
            if ( preg_match_all( '/(!?)\[((?:[^\[\]]|\[[^\]]*\])*)\]\(\s*(<[^>]*>|[^\s()]*(?:\([^\s()]*\)[^\s()]*)*)(?:\s+(?:"[^"]*"|\'[^\']*\'|\([^)]*\)))?\s*\)/', $line, $all, PREG_SET_ORDER ) )
                foreach ( $all as $m )
                {
                    $links[] = array( 'kind' => $m[1] === '!' ? 'image' : 'link', 'target' => trim( $m[3], '<>' ), 'line' => $number + 1 );
                    // an image inside a link: [![alt](img)](target)
                    if ( $m[1] === '' && preg_match( '/!\[[^\]]*\]\(\s*(<[^>]*>|[^\s)]+)/', $m[2], $inner ) )
                        $links[] = array( 'kind' => 'image', 'target' => trim( $inner[1], '<>' ), 'line' => $number + 1 );
                }
        }
        return $links;
    }
}
