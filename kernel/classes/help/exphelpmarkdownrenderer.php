<?php
/**
 * The safe Markdown renderer of the help (workstream A): a CommonMark subset with GFM tables, fenced code with
 * server-side highlighting (expHelpHighlighter), callouts ("> **Note**", ":::tip"), heading anchors and a TOC.
 * Raw HTML is escaped or reduced to an allowlist; javascript: and other unsafe links are dropped; relative .md links
 * become viewer URLs and images asset URLs through the resolvers. Skeleton: A replaces the bodies.
 *
 * Options: 'link_resolver' => callable( string $href, string $currentId ): string|null (null drops the link),
 *          'image_resolver' => callable( string $src, string $currentId ): string|null (null drops the image).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpMarkdownRenderer
{
    /** @var array */
    protected $options;

    /** @param array $options */
    public function __construct( array $options = array() ) { $this->options = $options; }

    /**
     * @param string $markdown the page without its front matter
     * @param string $currentId the page id, to resolve relative links
     * @return array array( 'html' => string, 'toc' => array of ( level, text, anchor ), 'title' => string )
     */
    public function render( $markdown, $currentId = '' )
    {
        return array( 'html' => '<pre>' . htmlspecialchars( (string)$markdown, ENT_QUOTES, 'UTF-8' ) . '</pre>', 'toc' => array(), 'title' => '' );
    }

    /** @param string $text a plain text page @return array same shape as render() */
    public function renderPlainText( $text, $currentId = '' ) { return $this->render( $text, $currentId ); }

    /** @param string $text @return string the heading anchor (lowercase, a-z0-9-, unique per page is the caller's job) */
    public static function slug( $text )
    {
        return trim( preg_replace( '/[^a-z0-9]+/', '-', strtolower( (string)$text ) ), '-' );
    }

    /** @param string $url @return bool whether a link target is safe (http, https, mailto, relative, #) */
    public static function isSafeUrl( $url ) { return false; }
}

?>
