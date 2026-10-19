<?php
/**
 * File containing the expHelpLanding class.
 *
 * The landing page of one audience of the help: exphelp/audience/<audience> (doc/features/6.0/help-system.md,
 * "Landing pages"). The page is a list of blocks, configured in exphelp.ini:
 *
 *   [LandingSettings] Blocks[]           the blocks of every landing page, in order
 *   [Landing_<audience>] Blocks[]        the blocks of one audience instead (optional)
 *   [Landing_<audience>] StartPage       the page the "Start here" button opens
 *   [Landing_<audience>] ReadingPath[]   the reading path, in order ("start here")
 *   [Landing_<audience>] InfoPages[]     the pages of the info boxes
 *   [Landing_<audience>] KeyPages[]      the curated list of key pages
 *   [Landing_<audience>] Categories[]    the categories that get a search button (default: those with pages for it)
 *   [Landing_<audience>] Related[]       related audiences
 *
 * A block whose list is empty in the settings is filled from the help index (featured pages, the help pages and
 * guides of the audience in their order, the recent changes); a page that is not in the index (not written yet,
 * hidden) is left out, so a landing page never links a missing page. Every list comes from the cached index: no file
 * is read to draw the page.
 *
 * The blocks: hero (name, summary, search box, buttons), start (reading path), boxes (info boxes), categories
 * (a search button per category), key (key pages), latest (recently updated pages), related (related audiences),
 * commits (the latest commits of the project, expHelpCommitFeed) and support (the Support Exponential block,
 * [SupportSettings]).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpLanding
{
    /** The blocks a landing page can have */
    const BLOCKS = array( 'hero', 'start', 'boxes', 'categories', 'key', 'latest', 'related', 'commits', 'support' );

    /** The blocks by default, when [LandingSettings] Blocks[] is not set */
    const DEFAULT_BLOCKS = array( 'hero', 'start', 'boxes', 'categories', 'key', 'latest', 'related' );

    /** The page types never picked for an automatic list */
    const DEFAULT_EXCLUDE_TYPES = array( 'changelog', 'history', 'bc', 'specification' );

    /** The summary of each audience: the sentence of its dashboard card (translated in kernel/exphelp) */
    const SUMMARIES = array(
        'editor' => 'Create, edit, translate and publish content: media, layouts, search and the editor.',
        'admin' => 'Run the installation: sites, users and roles, caches, logs, security and the system\'s health.',
        'developer' => 'Build on it: extensions, templates, settings, the APIs, the command line and upgrades.',
        'user' => 'Use the site: your account, signing in, two-factor authentication and finding things.',
        'project-manager' => 'Plan and lead projects: phases and roles, hosting choices, scoping, checklists, quality gates, releases and risks.',
        'sales-marketing' => 'Present and promote Exponential: positioning, feature highlights, demos, objections, licensing, brand and community.',
        'partner' => 'Build, host and sell with Exponential: the partner program, partner classes, benefits, requirements and how to apply.',
        'advocate' => 'Spread the word: talking points, events, community channels, demos and helping new users find their way.',
        'designer' => 'Design themes and templates, build extensions, then publish, sell and maintain them through their life cycle.',
        'hosting' => 'Host Exponential sites: server requirements, PHP and databases, Velocity, caching, security, backups and operations.',
        'app-creator' => 'Build applications on Exponential: content models, modules and views, the APIs, extensions and testing.',
        'website-creator' => 'Build websites with Exponential: plan the site, content classes, designs, layouts, the launch and its care.',
        'supporter' => 'Help the project grow: contribute code, documentation, translations and testing, and share what you build.',
        'sponsor' => 'Fund the work on Exponential: why sponsorship matters, what it pays for and how to sponsor 7x and the Foundation.',
        'press' => 'Write and talk about Exponential: the facts, the history, the people, quotes, images and who to contact.',
        'trainer' => 'Teach Exponential: courses for editors, administrators, developers and designers, workshops, exercises and assessments.',
    );

    /** @var expHelpDocumentIndex */
    protected $index;

    /** @var callable function( $group, $name, $default ) reading the settings */
    protected $setting;

    /**
     * @param expHelpDocumentIndex $index
     * @param callable|null $setting function( string $group, string $name, mixed $default ): mixed (tests); default
     *                               expHelp::setting()
     */
    public function __construct( expHelpDocumentIndex $index, $setting = null )
    {
        $this->index = $index;
        $this->setting = $setting !== null ? $setting : array( 'expHelp', 'setting' );
    }

    /** Whether the landing pages are on ([LandingSettings] Enabled) */
    public static function isEnabled()
    {
        return !class_exists( 'expHelp' ) || expHelp::setting( 'LandingSettings', 'Enabled', 'enabled' ) === 'enabled';
    }

    /** The module-relative address of an audience's landing page */
    public static function url( $audience )
    {
        return '/exphelp/audience/' . rawurlencode( (string)$audience );
    }

    protected function setting( $group, $name, $default )
    {
        $value = call_user_func( $this->setting, $group, $name, $default );
        return $value === null ? $default : $value;
    }

    /** A list setting without empty entries */
    protected function listSetting( $group, $name, array $default = array() )
    {
        $value = $this->setting( $group, $name, $default );
        $list = array();
        foreach ( (array)$value as $item )
            if ( is_string( $item ) && trim( $item ) !== '' )
                $list[] = trim( $item );
        return $list;
    }

    /**
     * The landing page of an audience, or null when the audience is unknown.
     *
     * @param string $audience
     * @return array|null id, name, icon, count, summary, blocks (list of ids), start (document or null), reading
     *                    (documents), boxes (documents), key (documents), categories (list of id, name, icon, count,
     *                    url), latest (list of id, title, time, subject), related (list of id, name, icon, count, url),
     *                    search_url, pages (number of pages for it)
     */
    public function build( $audience )
    {
        $audiences = $this->index->audiences();
        if ( !is_string( $audience ) || !isset( $audiences[$audience] ) )
            return null;
        $group = 'Landing_' . $audience;
        $limits = array(
            'start'  => max( 1, (int)$this->setting( 'LandingSettings', 'StartLimit', 6 ) ),
            'boxes'  => max( 1, (int)$this->setting( 'LandingSettings', 'BoxLimit', 6 ) ),
            'key'    => max( 1, (int)$this->setting( 'LandingSettings', 'KeyLimit', 10 ) ),
            'latest' => max( 1, (int)$this->setting( 'LandingSettings', 'LatestLimit', 8 ) ),
        );
        $exclude = $this->listSetting( 'LandingSettings', 'ExcludeTypes', self::DEFAULT_EXCLUDE_TYPES );

        $mine = array();
        foreach ( $this->index->visibleDocuments() as $id => $document )
            if ( in_array( $audience, $document['audience'], true ) )
                $mine[$id] = $document;
        $ranked = self::rank( array_filter( $mine, function ( $d ) use ( $exclude ) {
            return !in_array( (string)$d['type'], $exclude, true );
        } ) );

        $blocks = $this->listSetting( $group, 'Blocks', $this->listSetting( 'LandingSettings', 'Blocks', self::DEFAULT_BLOCKS ) );
        $blocks = array_values( array_unique( array_intersect( $blocks, self::BLOCKS ) ) );

        // the start page: the setting, else the first featured page of the audience's own help pages
        $start = $this->documentOf( (string)$this->setting( $group, 'StartPage', '' ) );
        if ( $start === null )
        {
            foreach ( $ranked as $document )
                if ( !empty( $document['featured'] ) && $document['type'] === 'help' )
                {
                    $start = $document;
                    break;
                }
        }

        $reading = $this->documentsOf( $this->listSetting( $group, 'ReadingPath' ) );
        if ( !$reading )
        {
            if ( $start !== null )
                $reading[$start['id']] = $start;
            foreach ( $ranked as $id => $document )
                if ( count( $reading ) < $limits['start'] && $document['type'] === 'help' && !isset( $reading[$id] ) )
                    $reading[$id] = $document;
        }
        $reading = array_slice( $reading, 0, $limits['start'], true );

        $boxes = $this->documentsOf( $this->listSetting( $group, 'InfoPages' ) );
        if ( !$boxes )
            foreach ( $ranked as $id => $document )
                if ( count( $boxes ) < $limits['boxes'] && !isset( $reading[$id] ) && $document['summary'] !== '' )
                    $boxes[$id] = $document;
        $boxes = array_slice( $boxes, 0, $limits['boxes'], true );

        $key = $this->documentsOf( $this->listSetting( $group, 'KeyPages' ) );
        if ( !$key )
            foreach ( $ranked as $id => $document )
                if ( count( $key ) < $limits['key'] && !isset( $reading[$id] ) && !isset( $boxes[$id] ) )
                    $key[$id] = $document;
        $key = array_slice( $key, 0, $limits['key'], true );

        // a search button per category: the configured ones, else every category with pages for the audience
        $counts = array();
        foreach ( $mine as $document )
            $counts[$document['category']] = ( isset( $counts[$document['category']] ) ? $counts[$document['category']] : 0 ) + 1;
        $wanted = $this->listSetting( $group, 'Categories' );
        $categories = array();
        foreach ( $this->index->categories() as $id => $category )
        {
            if ( $wanted ? !in_array( $id, $wanted, true ) : empty( $counts[$id] ) )
                continue;
            $categories[] = array( 'id' => $id, 'name' => $category['name'], 'icon' => $category['icon'],
                                   'count' => isset( $counts[$id] ) ? $counts[$id] : 0,
                                   'url' => self::searchUrl( array( 'audience' => array( $audience ), 'category' => array( $id ) ) ) );
        }

        $latest = array();
        foreach ( $this->index->recentChanges( 500 ) as $change )
        {
            if ( isset( $mine[$change['id']] ) && !isset( $latest[$change['id']] ) )
                $latest[$change['id']] = $change;
            if ( count( $latest ) >= $limits['latest'] )
                break;
        }

        $related = array();
        foreach ( $this->listSetting( $group, 'Related' ) as $id )
            if ( $id !== $audience && isset( $audiences[$id] ) )
                $related[] = array( 'id' => $id, 'name' => $audiences[$id]['name'], 'icon' => $audiences[$id]['icon'],
                                    'count' => $audiences[$id]['count'], 'url' => self::url( $id ) );

        $summary = (string)$this->setting( $group, 'Summary', '' );
        if ( $summary === '' && isset( self::SUMMARIES[$audience] ) )
            $summary = self::SUMMARIES[$audience];

        return array(
            'id'         => $audience,
            'name'       => $audiences[$audience]['name'],
            'icon'       => $audiences[$audience]['icon'],
            'count'      => count( $mine ),
            'pages'      => count( $mine ),
            'summary'    => $summary,
            'blocks'     => $blocks,
            'start'      => $start,
            'reading'    => array_values( $reading ),
            'boxes'      => array_values( $boxes ),
            'key'        => array_values( $key ),
            'categories' => $categories,
            'latest'     => array_values( $latest ),
            'related'    => $related,
            'search_url' => self::searchUrl( array( 'audience' => array( $audience ) ) ),
        );
    }

    /** expHelp::searchUrl() (the canonical search address) */
    protected static function searchUrl( array $state )
    {
        return class_exists( 'expHelp' ) ? expHelp::searchUrl( $state ) : '/exphelp/search';
    }

    /** A visible page by its id, or null */
    protected function documentOf( $id )
    {
        if ( $id === '' )
            return null;
        $document = $this->index->document( $id );
        return $document !== null && empty( $document['hidden'] ) ? $document : null;
    }

    /** The visible pages of a list of ids, in that order, keyed by id */
    protected function documentsOf( array $ids )
    {
        $documents = array();
        foreach ( $ids as $id )
        {
            $document = $this->documentOf( $id );
            if ( $document !== null )
                $documents[$document['id']] = $document;
        }
        return $documents;
    }

    /**
     * Pages in the order of the automatic lists: featured first, then help pages, guides, installation and feature
     * pages, then the front matter order, then the title.
     *
     * @param array $documents id => document
     * @return array id => document
     */
    public static function rank( array $documents )
    {
        $rank = array( 'help' => 0, 'guide' => 1, 'install' => 2, 'feature' => 3 );
        uasort( $documents, function ( $a, $b ) use ( $rank ) {
            $fa = empty( $a['featured'] ) ? 1 : 0;
            $fb = empty( $b['featured'] ) ? 1 : 0;
            if ( $fa !== $fb )
                return $fa - $fb;
            $ra = isset( $a['type'], $rank[$a['type']] ) ? $rank[$a['type']] : 9;
            $rb = isset( $b['type'], $rank[$b['type']] ) ? $rank[$b['type']] : 9;
            if ( $ra !== $rb )
                return $ra - $rb;
            $oa = isset( $a['order'] ) ? (int)$a['order'] : 1000;
            $ob = isset( $b['order'] ) ? (int)$b['order'] : 1000;
            return $oa !== $ob ? $oa - $ob : strcasecmp( (string)( $a['title'] ?? '' ), (string)( $b['title'] ?? '' ) );
        } );
        return $documents;
    }
}
