<?php
/**
 * exphelp/search (doc/en/us/features/6.0/help-system.md, "Search"): the search and browse page of the help.
 *
 * The state lives in view parameters, in a canonical order (expHelp::searchUrl()):
 *   exphelp/search/(q)/upload+image/(category)/layouts/(audience)/editor/(type)/help/(offset)/20/(limit)/10
 * Every part is optional. Without (q) the page browses: it lists every page the filters allow (never an empty page).
 * A GET request with q, audience, category, type, offset or limit (the search form without scripts, old links) is
 * redirected (302) to the canonical address. A JSON request (format=json, or Accept: application/json, as the help
 * script sends it) is never redirected and takes both forms: expHelpSearch::search()'s array plus 'canonical' (the
 * module-relative address of that state) and 'canonical_href' (with the siteaccess).
 *
 * Values are checked: a category, audience or type must be one exphelp.ini knows (others are dropped), q is at most
 * 200 characters, offset at most 10000, limit 1 to 50.
 *
 * Template design:exphelp/search.tpl: help_search (the result), help_filters (category, audience, type as lists
 * joined by ","), help_canonical, help_url_base (the canonical address without offset; append "/(offset)/<n>"),
 * help_facet_urls (facet => value => address of that choice, offset reset; value '' clears the facet),
 * help_category (the chosen category: id, name, icon, count, intro) or false, help_audiences, help_categories,
 * help_types, help_pager (prev, next: addresses or false; from, to, total, pages: list of array( number, url,
 * current )), help_i18n_json.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Search extends \Exponential\Runnable\ModuleView
{
    /** The view parameter variables of module.php's unordered_params, by parameter name. */
    const VIEW_PARAMETERS = array( 'q' => 'Query', 'category' => 'Category', 'audience' => 'Audience', 'type' => 'Type',
                                   'extension' => 'Extension', 'offset' => 'Offset', 'limit' => 'Limit', 'format' => 'Format' );

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !\expHelp::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $index = \expHelpDocumentIndex::instance();
        $known = self::knownValues( $index );
        $defaultLimit = self::defaultLimit();
        $viewState = self::viewParameters( $Params );
        $json = self::wantsJson( $viewState, $_GET, $_SERVER );

        // the form without scripts, and old ?q= links: to the canonical address (never for JSON, never for POST)
        if ( !$json && self::redirectsGet( $_GET, isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'GET' ) )
        {
            $state = self::state( array_merge( $viewState, self::getState( $_GET ) ), $known, $defaultLimit );
            // Sent here, not through $Module->redirectTo(): the kernel carries the query string over a module redirect,
            // so the canonical address would arrive with ?q=... again and redirect to itself forever. Without HTML
            // escaping: the address goes into the Location header.
            $target = \expHelp::searchUrl( $state, $defaultLimit );
            \eZURI::transformURI( $target, false, 'full', false );
            \eZHTTPTool::redirect( $target, array(), '302 Found', false );
            \eZExecution::cleanExit();
        }

        $state = self::state( $json ? array_merge( self::getState( $_GET ), array_filter( $viewState, function ( $v ) { return $v !== false && $v !== null && $v !== ''; } ) )
                                    : $viewState, $known, $defaultLimit );
        $filters = array();
        foreach ( array( 'audience', 'category', 'type', 'extension' ) as $facet )
        {
            if ( $state[$facet] )
                $filters[$facet] = implode( ',', $state[$facet] );
        }
        $search = new \expHelpSearch( $index );
        $result = $search->search( $state['q'], $filters, $state['limit'], $state['offset'] );
        $result = self::withContentTopics( $result, array( 'q' => $state['q'], 'filters' => $filters, 'limit' => $state['limit'], 'offset' => $state['offset'] ) );
        $result['canonical'] = \expHelp::searchUrl( $state, $defaultLimit );
        $result['canonical_href'] = \expHelpLinks::absolute( $result['canonical'] );

        if ( $json )
        {
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Cache-Control: private, no-store' );
            header( 'X-Content-Type-Options: nosniff' );
            echo json_encode( $result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE );
            \eZExecution::cleanExit();
        }

        $categories = \expHelp::translatedNames( $index->categories() );
        $category = false;
        if ( count( $state['category'] ) === 1 && isset( $categories[$state['category'][0]] ) )
        {
            $category = $categories[$state['category'][0]];
            $category['intro'] = self::categoryIntro( $category['id'] );
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'help_i18n_json', \expHelp::clientStringsJson() );
        $tpl->setVariable( 'help_search', $result );
        $tpl->setVariable( 'help_filters', $filters );
        $tpl->setVariable( 'help_canonical', $result['canonical'] );
        $tpl->setVariable( 'help_url_base', \expHelp::searchUrl( array( 'offset' => 0 ) + $state, $defaultLimit ) );
        $tpl->setVariable( 'help_facet_urls', self::facetUrls( $state, $known, $defaultLimit ) );
        $tpl->setVariable( 'help_pager', self::pager( $state, (int)$result['total'], $defaultLimit ) );
        $tpl->setVariable( 'help_category', $category );
        $tpl->setVariable( 'help_audiences', \expHelp::translatedNames( $index->audiences() ) );
        $tpl->setVariable( 'help_categories', $categories );
        $tpl->setVariable( 'help_types', $index->types() );
        // the extension whose documentation the search is limited to (exphelp/extensions/<name>), or false
        $tpl->setVariable( 'help_extension', count( $state['extension'] ) === 1 ? $state['extension'][0] : false );

        $title = $state['q'] !== '' ? \ezpI18n::tr( 'kernel/exphelp', 'Search' )
                                    : ( $category ? $category['name'] : \ezpI18n::tr( 'kernel/exphelp', 'Browse' ) );
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/search.tpl' );
        $Result['path'] = array( array( 'url' => 'exphelp/dashboard', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ),
                                 array( 'url' => false, 'text' => $title ) );
        return $Result;
    }

    /**
     * Help topics maintained as content (doc/en/us/features/6.0/help-content.md) the user may see, put before the pages of
     * doc/ on the first page of results; the total counts them too. Nothing when help as content is not installed.
     *
     * @param array $result expHelpSearch::search()'s array
     * @param array $request q, filters, limit, offset
     * @return array
     */
    public static function withContentTopics( array $result, array $request )
    {
        if ( $request['q'] === '' || !class_exists( 'expHelpContentProvider' ) || !\expHelpContentProvider::isEnabled() )
            return $result;
        try
        {
            $topics = \expHelpContentProvider::instance()->search( $request['q'], $request['filters'], $request['limit'] );
        }
        catch ( \Exception $e )
        {
            \eZDebug::writeWarning( 'Help topics could not be searched: ' . $e->getMessage(), __METHOD__ );
            return $result;
        }
        if ( !$topics['hits'] )
            return $result;
        if ( (int)$request['offset'] === 0 )
        {
            $hits = array_slice( array_merge( $topics['hits'], isset( $result['hits'] ) ? $result['hits'] : array() ), 0, $request['limit'] );
            $result['hits'] = $result['results'] = $hits;
        }
        $result['total'] = ( isset( $result['total'] ) ? (int)$result['total'] : 0 ) + $topics['total'];
        $result['topics_total'] = $topics['total'];
        return $result;
    }

    /**
     * The search parameters of a request, bounded.
     * The view parameters of the request, by name (q, category, ...): string, or false when not given.
     *
     * @param array $params the view's $Params
     * @return array
     */
    public static function viewParameters( array $params )
    {
        $state = array();
        $user = isset( $params['UserParameters'] ) && is_array( $params['UserParameters'] ) ? $params['UserParameters'] : array();
        foreach ( self::VIEW_PARAMETERS as $name => $variable )
        {
            $value = isset( $params[$variable] ) && is_string( $params[$variable] ) ? $params[$variable]
                   : ( isset( $user[$name] ) && is_string( $user[$name] ) ? $user[$name] : false );
            // an address decodes "+" to a space only with urldecode(); take both spellings
            if ( $name === 'q' && is_string( $value ) )
                $value = str_replace( '+', ' ', $value );
            $state[$name] = $value;
        }
        return $state;
    }

    /**
     * The search parameters of a query string (q, category, audience, type, offset, limit) that are set.
     *
     * @param array $get
     * @return array
     */
    public static function getState( array $get )
    {
        $state = array();
        foreach ( \expHelp::SEARCH_PARAMS as $name )
        {
            if ( isset( $get[$name] ) && is_string( $get[$name] ) && $get[$name] !== '' )
                $state[$name] = $get[$name];
        }
        return $state;
    }

    /**
     * Whether a request is to be redirected to the canonical address: a GET (or HEAD) with a search parameter in
     * its query string, empty ones included (the form sends q= with nothing typed). JSON requests are decided before.
     *
     * @param array $get
     * @param string $method
     * @return bool
     */
    public static function redirectsGet( array $get, $method )
    {
        if ( !in_array( strtoupper( (string)$method ), array( 'GET', 'HEAD' ), true ) )
            return false;
        foreach ( \expHelp::SEARCH_PARAMS as $name )
        {
            if ( array_key_exists( $name, $get ) )
                return true;
        }
        return false;
    }

    /**
     * Whether the answer is JSON: format=json in the query string or as (format)/json, or Accept: application/json.
     *
     * @param array $viewState
     * @param array $get
     * @param array $server
     * @return bool
     */
    public static function wantsJson( array $viewState, array $get, array $server )
    {
        return ( isset( $get['format'] ) && $get['format'] === 'json' )
            || ( isset( $viewState['format'] ) && $viewState['format'] === 'json' )
            || self::acceptsJson( $server );
    }

    /**
     * A checked state: q (string, at most 200 characters, "/", "(", ")" as spaces), category, audience, type,
     * extension (lists of known values, at most 10), offset (0 to 10000), limit (1 to 50).
     *
     * @param array $raw name => string
     * @param array $known facet => list of the known values
     * @param int $defaultLimit
     * @return array
     */
    public static function state( array $raw, array $known, $defaultLimit = 20 )
    {
        $state = array( 'q' => \expHelp::searchWords( isset( $raw['q'] ) ? $raw['q'] : '' ) );
        foreach ( array( 'category', 'audience', 'type', 'extension' ) as $facet )
        {
            $values = isset( $raw[$facet] ) && is_string( $raw[$facet] ) ? explode( ',', strtolower( $raw[$facet] ) ) : array();
            $allowed = isset( $known[$facet] ) ? array_flip( $known[$facet] ) : array();
            $values = array_values( array_unique( array_filter( array_map( 'trim', $values ), function ( $v ) use ( $allowed ) {
                return $v !== '' && isset( $allowed[$v] );
            } ) ) );
            $state[$facet] = array_slice( $values, 0, 10 );
        }
        $number = function ( $name ) use ( $raw ) {
            return isset( $raw[$name] ) && is_scalar( $raw[$name] ) && preg_match( '/^\d{1,9}$/', (string)$raw[$name] ) ? (int)$raw[$name] : null;
        };
        $offset = $number( 'offset' );
        $limit = $number( 'limit' );
        $state['limit'] = max( 1, min( 50, $limit === null ? (int)$defaultLimit : $limit ) );
        $state['offset'] = max( 0, min( 10000, $offset === null ? 0 : $offset ) );
        return $state;
    }

    /**
     * The known values of each facet: the categories, audiences and types of exphelp.ini, and the extensions that
     * have pages in the help (their names in lower case).
     *
     * @param \expHelpDocumentIndex $index
     * @return array facet => list
     */
    public static function knownValues( $index )
    {
        $extensions = array();
        foreach ( array_keys( $index->visibleDocuments() ) as $id )
            if ( preg_match( '#^ext/([^/]+)/#', (string)$id, $m ) )
                $extensions[strtolower( $m[1] )] = true;
        return array( 'category' => array_map( 'strval', array_keys( $index->categories() ) ),
                      'audience' => array_map( 'strval', array_keys( $index->audiences() ) ),
                      'type' => array_map( 'strval', array_keys( $index->types() ) ),
                      'extension' => array_map( 'strval', array_keys( $extensions ) ) );
    }

    /**
     * The address of each facet choice from a state: facet => value => address ('' clears the facet). A choice
     * replaces the facet's value and starts again at the first page.
     *
     * @param array $state
     * @param array $known
     * @param int $defaultLimit
     * @return array
     */
    public static function facetUrls( array $state, array $known, $defaultLimit = 20 )
    {
        $urls = array();
        foreach ( array( 'category', 'audience', 'type' ) as $facet )
        {
            $urls[$facet] = array( '' => \expHelp::searchUrl( array( $facet => array(), 'offset' => 0 ) + $state, $defaultLimit ) );
            foreach ( isset( $known[$facet] ) ? $known[$facet] : array() as $value )
                $urls[$facet][$value] = \expHelp::searchUrl( array( $facet => array( $value ), 'offset' => 0 ) + $state, $defaultLimit );
        }
        // the extension is no facet with counts: only the address that searches the whole help again
        $urls['extension'] = array( '' => \expHelp::searchUrl( array( 'extension' => array(), 'offset' => 0 ) + $state, $defaultLimit ) );
        return $urls;
    }

    /**
     * The pager of a state: prev, next (addresses or false), from, to, total, pages (a window of at most 7 pages
     * around the current one: number, url, current).
     *
     * @param array $state
     * @param int $total
     * @param int $defaultLimit
     * @return array
     */
    public static function pager( array $state, $total, $defaultLimit = 20 )
    {
        $limit = max( 1, (int)$state['limit'] );
        $offset = (int)$state['offset'];
        $url = function ( $offset ) use ( $state, $defaultLimit ) {
            return \expHelp::searchUrl( array( 'offset' => max( 0, $offset ) ) + $state, $defaultLimit );
        };
        $count = (int)ceil( $total / $limit );
        $current = (int)floor( $offset / $limit );
        $pages = array();
        if ( $count > 1 )
        {
            $first = max( 0, min( $current - 3, $count - 7 ) );
            for ( $n = $first; $n < min( $count, $first + 7 ); $n++ )
                $pages[] = array( 'number' => $n + 1, 'url' => $url( $n * $limit ), 'current' => $n === $current );
        }
        return array(
            'prev' => $offset > 0 ? $url( $offset - $limit ) : false,
            'next' => $offset + $limit < $total ? $url( $offset + $limit ) : false,
            'from' => $total > 0 ? min( $total, $offset + 1 ) : 0,
            'to' => min( $total, $offset + $limit ),
            'total' => $total,
            'pages' => $pages,
        );
    }

    /**
     * The introduction of a category on its browse page: exphelp.ini [CategorySettings] CategoryIntros[<id>], else
     * the built-in sentence for the standard categories, else ''.
     *
     * @param string $id
     * @return string
     */
    public static function categoryIntro( $id )
    {
        $intros = \expHelp::setting( 'CategorySettings', 'CategoryIntros', array() );
        if ( is_array( $intros ) && isset( $intros[$id] ) && is_string( $intros[$id] ) && trim( $intros[$id] ) !== '' )
            return trim( $intros[$id] );
        $ctx = 'kernel/exphelp';
        switch ( $id )
        {
            case 'getting-started': return \ezpI18n::tr( $ctx, 'First steps in the administration: signing in, finding your way and the everyday tasks.' );
            case 'manual': return \ezpI18n::tr( $ctx, 'One question per page: who may do it, the steps, what can go wrong and where to read more.' );
            case 'editing': return \ezpI18n::tr( $ctx, 'Creating and changing content: the editor, fields, drafts and versions.' );
            case 'media': return \ezpI18n::tr( $ctx, 'Images, files and other media: uploading, choosing and reusing them.' );
            case 'publishing': return \ezpI18n::tr( $ctx, 'Publishing, scheduling, hiding and moving content.' );
            case 'search': return \ezpI18n::tr( $ctx, 'Finding content in the administration and on the site.' );
            case 'languages': return \ezpI18n::tr( $ctx, 'Translations and the languages of the site.' );
            case 'account': return \ezpI18n::tr( $ctx, 'Your account: password, two-step sign-in and preferences.' );
            case 'layouts': return \ezpI18n::tr( $ctx, 'Page layouts: zones, blocks and the rules that choose a layout.' );
            case 'shortcuts': return \ezpI18n::tr( $ctx, 'Keyboard shortcuts that make the administration faster.' );
            case 'support': return \ezpI18n::tr( $ctx, 'Getting help and reporting an error.' );
            case 'leadership': return \ezpI18n::tr( $ctx, 'Planning, running and growing Exponential projects: phases, roles, checklists, risks and working with the community.' );
            case 'business': return \ezpI18n::tr( $ctx, 'Presenting and promoting Exponential: positioning, demos, objections, licensing, brand and growing the community.' );
            case 'partnership': return \ezpI18n::tr( $ctx, 'Working with Exponential as a partner: the partner program, partner classes, benefits and how to apply.' );
            case 'advocacy': return \ezpI18n::tr( $ctx, 'Spreading the word about Exponential: talking points, events, channels and welcoming new users.' );
            case 'theme-design': return \ezpI18n::tr( $ctx, 'Designing themes for Exponential: designs, the override system, styles, assets and accessibility.' );
            case 'templates': return \ezpI18n::tr( $ctx, 'The template language: variables, operators, functions, fetches, includes and overrides.' );
            case 'extension-development': return \ezpI18n::tr( $ctx, 'Building extensions step by step: structure, settings, modules, templates, classes and contributing back.' );
            case 'theme-extensions': return \ezpI18n::tr( $ctx, 'Packaging a theme as an extension: structure, settings, dependencies and the designs it adds.' );
            case 'theme-distribution': return \ezpI18n::tr( $ctx, 'Publishing themes and extensions: repositories, packages, releases, versions and licenses.' );
            case 'theme-sales': return \ezpI18n::tr( $ctx, 'Selling themes and the services around them: offers, pricing, support and working with clients.' );
            case 'theme-lifecycle': return \ezpI18n::tr( $ctx, 'Keeping themes and extensions alive: upgrades, compatibility, security fixes and retirement.' );
            case 'hosting': return \ezpI18n::tr( $ctx, 'Hosting Exponential: servers, PHP, databases, web servers, Velocity, caching, backups and operations.' );
            case 'app-creation': return \ezpI18n::tr( $ctx, 'Building applications on Exponential: content models, modules, views, APIs and testing.' );
            case 'website-creation': return \ezpI18n::tr( $ctx, 'Building websites with Exponential: planning, content structure, designs, layouts and the launch.' );
            case 'supporting': return \ezpI18n::tr( $ctx, 'Helping Exponential grow: contributing code, documentation, translations, testing and word of mouth.' );
            case 'sponsorship': return \ezpI18n::tr( $ctx, 'Funding Exponential: what sponsorship pays for and how to sponsor 7x and the Exponential Foundation.' );
            case 'training': return \ezpI18n::tr( $ctx, 'Teaching Exponential: courses, lesson plans, workshops, exercises and assessments for every role.' );
            case 'press-media': return \ezpI18n::tr( $ctx, 'For journalists, media and influencers: facts, history, announcements, images and contacts.' );
            case 'project': return \ezpI18n::tr( $ctx, 'The Exponential project: what it is, its licenses, roadmap, support, community channels, and how to contribute and give.' );
            case 'build-two-ways': return \ezpI18n::tr( $ctx, 'Two ways to build a site with Exponential: how they differ and how to choose between them.' );
            case 'business-solutions': return \ezpI18n::tr( $ctx, 'Exponential for companies and organizations: what it offers, costs, support, hosting, security and working with partners.' );
            case 'public-sector': return \ezpI18n::tr( $ctx, 'Exponential for governments and public bodies: accessibility, privacy, security, open source, procurement and long-term care.' );
            case 'customer-success': return \ezpI18n::tr( $ctx, 'For organizations and people running their own site on Exponential: team, publishing, commerce, upkeep, growth and partners.' );
            case 'administration': return \ezpI18n::tr( $ctx, 'Running the site: settings, users and roles, caches, cronjobs and logs.' );
            case 'features': return \ezpI18n::tr( $ctx, 'What each feature does and how to set it up.' );
            case 'development': return \ezpI18n::tr( $ctx, 'Templates, extensions, the API and the code behind it.' );
            case 'install-upgrade': return \ezpI18n::tr( $ctx, 'Installing Exponential and upgrading it to a new release.' );
            case 'release-notes': return \ezpI18n::tr( $ctx, 'What changed in each release.' );
            case 'history': return \ezpI18n::tr( $ctx, 'Older documents kept for reference.' );
            case 'reference': return \ezpI18n::tr( $ctx, 'Reference material: settings, commands and formats.' );
            case 'extensions': return \ezpI18n::tr( $ctx, 'The documentation the active extensions bring with them: their guides, README, changelog and license.' );
        }
        return '';
    }

    /** @return int [SearchSettings] ResultLimit, 1 to 50 */
    public static function defaultLimit()
    {
        return max( 1, min( 50, (int)\expHelp::setting( 'SearchSettings', 'ResultLimit', 20 ) ) );
    }

    /**
     * The search parameters of a query string, bounded (the JSON client's form; kept for callers of wave 1).
     *
     * @param array $get
     * @return array array( 'q' => string, 'filters' => array, 'limit' => int, 'offset' => int, 'format' => html|json )
     */
    public static function request( array $get )
    {
        $state = self::state( self::getState( $get ), array( 'category' => self::anyValues( $get, 'category' ),
                                                              'audience' => self::anyValues( $get, 'audience' ),
                                                              'type' => self::anyValues( $get, 'type' ) ), self::defaultLimit() );
        $filters = array();
        foreach ( array( 'audience', 'category', 'type' ) as $facet )
        {
            $values = array_filter( $state[$facet], function ( $v ) { return preg_match( '/^[a-z0-9_.-]+$/', $v ) === 1; } );
            if ( $values )
                $filters[$facet] = implode( ',', $values );
        }
        return array( 'q' => $state['q'], 'filters' => $filters, 'limit' => $state['limit'], 'offset' => $state['offset'],
                      'format' => isset( $get['format'] ) && $get['format'] === 'json' || self::acceptsJson() ? 'json' : 'html' );
    }

    /** The values of a facet as given (request() checks their syntax only). */
    protected static function anyValues( array $get, $facet )
    {
        return isset( $get[$facet] ) && is_string( $get[$facet] ) ? explode( ',', strtolower( $get[$facet] ) ) : array();
    }

    /**
     * Whether the client asks for JSON first (Accept: application/json, as the help script sends); a browser's
     * Accept (text/html first, or any type) gets the page.
     *
     * @param array|null $server default $_SERVER
     * @return bool
     */
    public static function acceptsJson( ?array $server = null )
    {
        $server = $server === null ? $_SERVER : $server;
        $accept = isset( $server['HTTP_ACCEPT'] ) && is_string( $server['HTTP_ACCEPT'] ) ? strtolower( $server['HTTP_ACCEPT'] ) : '';
        return (bool)preg_match( '/^\s*application\/json\s*(?:[;,]|$)/', $accept );
    }
}

}
