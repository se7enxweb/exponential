<?php
/**
 * The help index (workstream B): every servable page and image below the configured roots, with its metadata,
 * cached in <cache dir>/exphelp/index-<signature>.php. Only what expHelpAllowlist allows is in it; every other
 * path is unknown (404). Skeleton: B replaces the bodies.
 *
 * Document (page) shape: array( id, path (repo-relative, doc/...), title, summary, type, category, audience[],
 * order, suffix, headings[] (level, text, anchor), mtime, size )
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpDocumentIndex
{
    /** @var expHelpDocumentIndex|null */
    protected static $instance = null;

    /** @return expHelpDocumentIndex */
    public static function instance()
    {
        if ( self::$instance === null )
            self::$instance = new self();
        return self::$instance;
    }

    /** @return array<string,array> id => document */
    public function documents() { return array(); }

    /** @param string $id @return array|null the document, null when unknown or not allowed */
    public function document( $id ) { return null; }

    /** @param string $id @return bool whether the id names an allowed, git-tracked page or image */
    public function isAllowed( $id ) { return false; }

    /** @param string $id @return string|null the raw text of an allowed page */
    public function source( $id ) { return null; }

    /** @param string $id @return string|null the absolute path of an allowed image */
    public function asset( $id ) { return null; }

    /** @return string what the cached index depends on (git HEAD, settings, mtimes) */
    public function signature() { return ''; }

    /** @return string */
    public function cacheDir() { return expHelp::cacheDirectory(); }

    /** @return array statistics of the rebuild */
    public function rebuild() { return array(); }

    /** @return array the navigation tree: categories/sections with their pages, ordered */
    public function tree() { return array(); }

    /** @return array id => array( id, name, icon, count ) */
    public function categories() { return array(); }

    /** @return array id => array( id, name, count ) */
    public function audiences() { return array(); }

    /** @param string $id @return array array( 'prev' => document|null, 'next' => document|null ) in tree order */
    public function neighbours( $id ) { return array( 'prev' => null, 'next' => null ); }

    /** @param int $limit @return array the latest changes below the roots: array( id, title, time, subject ) */
    public function recentChanges( $limit = 10 ) { return array(); }
}

?>
