<?php
/**
 * File containing the expUrlMapEnvironment interface.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the planner (expUrlMapPlanner) asks of an installation: its siteaccesses and hosts, languages, whether a
 * target exists, and the entries already there at an old address. expUrlMapKernelEnvironment answers from the
 * installation; tests give one of their own, so the planner's classification is tested without a database.
 *
 * @since 6.0.15
 * @package kernel
 */
interface expUrlMapEnvironment
{
    /** @return string[] the names of the siteaccesses */
    public function siteaccesses();

    /** @return string the siteaccess a bare path belongs to when none is chosen */
    public function defaultSiteaccess();

    /**
     * The siteaccess of this installation an address with a host reaches (its host and URI matching), or null when
     * the host is not one of this installation's.
     *
     * @param string $host in lower case, with ":port" when not the default one
     * @param string $path
     * @return array|null siteaccess, path (the path inside the siteaccess, its URI part taken off)
     */
    public function siteaccessForHost( $host, $path );

    /**
     * @param string $siteaccess
     * @return string the locale of its content (eng-GB)
     */
    public function defaultLanguage( $siteaccess );

    /**
     * @param string $locale
     * @return bool whether the installation has the language
     */
    public function languageExists( $locale );

    /**
     * Where a target of this site leads, or null when it does not exist.
     *
     * @param string $target node:<id>, or a path (with a leading slash, no query)
     * @param string $siteaccess
     * @param string $language
     * @return array|null type (node, module, alias), action (eznode:59, module:content/search), link (the element to
     *                    link to, or true for a module), node_id (or 0)
     */
    public function resolveTarget( $target, $siteaccess, $language );

    /**
     * Whether a path is an address of the system itself: a module (content/view/full/2, user/login).
     *
     * @param string $path
     * @return bool
     */
    public function isSystemPath( $path );

    /**
     * The URL alias element at a path (its last element), or null when there is none or only a placeholder.
     *
     * @param string $path
     * @return array|null the row of ezurlalias_ml (action, link, is_alias, alias_redirects, lang_mask, ...) plus
     *                    page (true when it is the address of a page, a node's own name, not an alias)
     */
    public function existingAlias( $path );

    /**
     * @param string $source the wildcard's pattern as stored (old/*, no leading slash)
     * @return array|null id, source_url, destination_url, type
     */
    public function existingWildcard( $source );

    /**
     * @param string $siteaccess
     * @param string $path
     * @param string $query
     * @return array|null the row of exp_url_redirect
     */
    public function existingRedirect( $siteaccess, $path, $query );
}
