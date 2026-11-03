<?php
/**
 * File containing the expSearchSuggest class.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The suggestions under the administration's header search field (content/suggest, design/<admin design>/javascript/
 * page/search_suggest.js): what the typed text finds, in groups, each with its own limit, only what the current user
 * may see. Settings: admininterface.ini [SearchSuggestSettings].
 *
 * The groups, in the order Groups[] names them:
 *
 *   content  the content search (eZSearch::search(), which applies the user's read permissions): name, class icon
 *            and name, the location (shortened), a link to the node. With eZ Find the search of eZ Find, else the
 *            kernel's search engine.
 *   pages    the PDF pages that hold the words, only with eZ Find and the extension ezfindpdfdeepsearch active
 *            (its fetch function pdfdeepsearch/page_hits, which checks the document's read permission itself):
 *            the document's name and the page, a link to that page. Without it a document is found as a whole, in
 *            the content group.
 *   users    user accounts by login, e-mail or name, only those the user may read, a link to the account's node
 *   classes  content classes by name or identifier, only for a user who may open the class module (class/view)
 *   admin    the pages of the administration's menus: the top tabs and the links of each tab's left menu as the
 *            design draws them for the user (parts/<part>/menu.tpl, with all of its access checks), "Setup > Cache"
 *
 * The decisions that need no database are static and pure (backend(), searchText(), score(), matchItems(),
 * menuLinks(), shortenPath(), assemble() ...), so tests/tests/kernel/classes/expSearchSuggestTest.php checks them
 * without a site.
 *
 * Only the API of the kernel and the one eZ Find shares with the kernel is used: eZSearch::search() with
 * SearchLimit, SearchOffset and the result's SearchResult and SearchCount, the class name eZSolr to tell eZ Find
 * apart. Page hits are asked through eZFunctionHandler only when the module pdfdeepsearch has the function.
 */
class expSearchSuggest
{
    /** the groups, in their default order */
    const GROUPS = array( 'content', 'pages', 'users', 'classes', 'admin' );

    /** the limit of a group that Limits[] does not name */
    const DEFAULT_LIMIT = 5;

    /** the most items one group can have, whatever Limits[] says */
    const MAX_LIMIT = 20;

    /** the translation context of the texts */
    const CONTEXT = 'design/admin/search/suggest';

    /**
     * The settings of admininterface.ini [SearchSuggestSettings], each with its default.
     *
     * @param eZINI|null $ini admininterface.ini
     * @return array 'enabled' bool, 'min_length' int, 'groups' list, 'limits' group => int, 'pages_per_document' int,
     *               'page_hit_classes' list, 'wildcard' 'auto'|'enabled'|'disabled', 'cache_ttl' int,
     *               'menu_skip_parts' list
     */
    public static function settings( $ini = null )
    {
        if ( $ini === null )
            $ini = eZINI::instance( 'admininterface.ini' );
        $get = function ( $name, $default ) use ( $ini ) {
            return $ini->hasVariable( 'SearchSuggestSettings', $name ) ? $ini->variable( 'SearchSuggestSettings', $name ) : $default;
        };
        $groups = array_values( array_intersect( array_map( 'strval', (array)$get( 'Groups', self::GROUPS ) ), self::GROUPS ) );
        $limits = array();
        $configured = (array)$get( 'Limits', array() );
        foreach ( self::GROUPS as $group )
            $limits[$group] = self::limit( isset( $configured[$group] ) ? $configured[$group] : self::DEFAULT_LIMIT );
        $wildcard = strtolower( (string)$get( 'Wildcard', 'auto' ) );
        return array(
            'enabled' => strtolower( (string)$get( 'Enabled', 'true' ) ) === 'true',
            'min_length' => max( 1, (int)$get( 'MinLength', 3 ) ),
            'groups' => array_values( array_unique( $groups ) ),
            'limits' => $limits,
            'pages_per_document' => max( 1, min( self::MAX_LIMIT, (int)$get( 'PagesPerDocument', 2 ) ) ),
            'page_hit_classes' => array_values( array_filter( array_map( 'strval', (array)$get( 'PageHitClasses', array( 'file' ) ) ) ) ),
            'wildcard' => in_array( $wildcard, array( 'auto', 'enabled', 'disabled' ), true ) ? $wildcard : 'auto',
            'cache_ttl' => max( 0, (int)$get( 'AdminPagesCacheTTL', 300 ) ),
            'menu_skip_parts' => array_values( array_filter( array_map( 'strval', (array)$get( 'AdminMenuSkipParts', array() ) ) ) ),
        );
    }

    /**
     * A group's limit: 0 (the group asks nothing) up to MAX_LIMIT.
     *
     * @param mixed $value
     * @return int
     */
    public static function limit( $value )
    {
        return max( 0, min( self::MAX_LIMIT, (int)$value ) );
    }

    /**
     * Which search answers the content group, and whether PDF pages can be suggested.
     *
     * @param string $engineClass the class of eZSearch::getEngine(), '' when no engine could be loaded (a site.ini
     *                            SearchEngine=ezsolr without the extension ezfind)
     * @param array $activeExtensions eZExtension::activeExtensions()
     * @param bool $pageHitsFunction whether the fetch function pdfdeepsearch/page_hits exists
     * @return array 'engine' => 'ezfind' (eZ Find answers through eZSearch), 'kernel' (the kernel's search engine:
     *               eZSearch's own, or eZSearchEngine directly when eZSearch has none), 'other' (another engine,
     *               through eZSearch); 'pages' => bool
     */
    public static function backend( $engineClass, array $activeExtensions, $pageHitsFunction )
    {
        $engineClass = (string)$engineClass;
        if ( strcasecmp( $engineClass, 'eZSolr' ) === 0 && in_array( 'ezfind', $activeExtensions, true ) )
            $engine = 'ezfind';
        else if ( $engineClass === '' || strcasecmp( $engineClass, 'eZSolr' ) === 0 || strcasecmp( $engineClass, 'eZSearchEngine' ) === 0 )
            $engine = 'kernel';
        else
            $engine = 'other';
        $pages = $engine === 'ezfind' && $pageHitsFunction && in_array( 'ezfindpdfdeepsearch', $activeExtensions, true );
        return array( 'engine' => $engine, 'pages' => $pages );
    }

    /**
     * The text handed to the search: the typed text, with a "*" after its last word when wildcards are used, so
     * "Oste" finds "Ostern". 'auto' follows site.ini [SearchSettings] EnableWildcard (both search engines honour
     * a "*" only with it), and a word shorter than MinCharacterWildcard gets none.
     *
     * @param string $text
     * @param string $mode 'auto', 'enabled' or 'disabled'
     * @param bool $siteWildcard site.ini [SearchSettings] EnableWildcard=true
     * @param int $minCharacters site.ini [SearchSettings] MinCharacterWildcard
     * @return string
     */
    public static function searchText( $text, $mode, $siteWildcard, $minCharacters )
    {
        $text = trim( (string)$text );
        if ( $text === '' || $mode === 'disabled' || ( $mode === 'auto' && !$siteWildcard ) )
            return $text;
        // not inside a phrase, not after a wildcard or an operator already there
        if ( substr_count( $text, '"' ) % 2 === 1 || preg_match( '/[*?"()+\-]$/u', $text ) )
            return $text;
        $words = preg_split( '/\s+/u', $text );
        $last = (string)end( $words );
        if ( self::length( $last ) < max( 1, (int)$minCharacters ) )
            return $text;
        return $text . '*';
    }

    /**
     * How well a text matches what was typed: 0 when one of the typed words is not in it, else 3 when the text starts
     * with the typed text, 2 when every word starts a word of the text, 1 when the words are only inside it. Letters
     * are compared without case.
     *
     * @param string $text
     * @param string $query
     * @return int
     */
    public static function score( $text, $query )
    {
        $text = self::lower( $text );
        $query = trim( self::lower( $query ) );
        if ( $query === '' || $text === '' )
            return 0;
        $words = preg_split( '/\s+/u', $query );
        $wordStarts = true;
        foreach ( $words as $word )
        {
            if ( strpos( $text, $word ) === false )
                return 0;
            if ( !preg_match( '/(^|[^\p{L}\p{N}])' . preg_quote( $word, '/' ) . '/u', $text ) )
                $wordStarts = false;
        }
        if ( strpos( $text, $query ) === 0 )
            return 3;
        return $wordStarts ? 2 : 1;
    }

    /**
     * The items whose fields match what was typed, best first (score(), then the order given).
     *
     * @param array $items arrays
     * @param string $query
     * @param array $fields the fields compared, the best score of them counts
     * @return array
     */
    public static function matchItems( array $items, $query, array $fields = array( 'title' ) )
    {
        $scored = array();
        $position = 0;
        foreach ( $items as $item )
        {
            $best = 0;
            foreach ( $fields as $field )
            {
                if ( isset( $item[$field] ) && is_scalar( $item[$field] ) )
                    $best = max( $best, self::score( (string)$item[$field], $query ) );
            }
            if ( $best > 0 )
                $scored[] = array( $best, $position, $item );
            ++$position;
        }
        usort( $scored, function ( $a, $b ) {
            return $a[0] === $b[0] ? $a[1] - $b[1] : $b[0] - $a[0];
        } );
        return array_map( function ( $entry ) { return $entry[2]; }, $scored );
    }

    /**
     * The items one may read, until the limit: $canRead is asked for one item after the other, never for more
     * than needed.
     *
     * @param array $candidates
     * @param callable $canRead item => bool
     * @param int $limit
     * @return array
     */
    public static function readable( array $candidates, $canRead, $limit )
    {
        $result = array();
        foreach ( $candidates as $candidate )
        {
            if ( count( $result ) >= $limit )
                break;
            if ( call_user_func( $canRead, $candidate ) )
                $result[] = $candidate;
        }
        return $result;
    }

    /**
     * The content classes that match, for a user who may open the class module; nothing for anybody else.
     *
     * @param array $classes arrays with 'id', 'identifier', 'name'
     * @param string $query
     * @param bool $mayOpen the user may open class/view
     * @return array arrays with 'id', 'identifier', 'title'
     */
    public static function matchClasses( array $classes, $query, $mayOpen )
    {
        if ( !$mayOpen )
            return array();
        $items = array();
        foreach ( $classes as $class )
        {
            $items[] = array( 'id' => (int)$class['id'], 'identifier' => (string)$class['identifier'],
                              'title' => (string)$class['name'] );
        }
        return self::matchItems( $items, $query, array( 'title', 'identifier' ) );
    }

    /**
     * The links of a rendered menu: each link to an address of the site with a text, once per address. Links to
     * "#", to javascript:, to another host and without text are left out.
     *
     * @param string $html the menu as the design drew it
     * @param string $prefix put before each title with " › " (the tab), '' for none
     * @return array arrays with 'title' and 'url'
     */
    public static function menuLinks( $html, $prefix = '' )
    {
        $html = trim( (string)$html );
        if ( $html === '' || !class_exists( 'DOMDocument' ) )
            return array();
        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors( true );
        $doc->loadHTML( '<?xml encoding="utf-8"?><div>' . $html . '</div>', LIBXML_NONET );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        $links = array();
        $seen = array();
        foreach ( $doc->getElementsByTagName( 'a' ) as $a )
        {
            $url = trim( (string)$a->getAttribute( 'href' ) );
            $title = trim( preg_replace( '/\s+/u', ' ', (string)$a->textContent ) );
            if ( $title === '' || !self::isSiteAddress( $url ) || isset( $seen[$url] ) )
                continue;
            $seen[$url] = true;
            $links[] = array( 'title' => $prefix === '' ? $title : $prefix . ' › ' . $title, 'url' => $url );
        }
        return $links;
    }

    /**
     * An address of this site: a path (not "//host"), no fragment-only link, no scheme.
     *
     * @param string $url
     * @return bool
     */
    public static function isSiteAddress( $url )
    {
        $url = (string)$url;
        return $url !== '' && $url[0] === '/' && !preg_match( '#^//|^/\\\\#', $url ) && strpos( $url, '#' ) !== 0;
    }

    /**
     * The navigation part's left menu template, as the page layout includes it (page_leftmenu.tpl):
     * ez<name>navigationpart and the kernel's expauditnavigationpart => parts/<name>/menu.tpl, an extension's
     * <name>navigationpart => parts/<name>/menu.tpl.
     *
     * @param string $part
     * @return string|null design:parts/<name>/menu.tpl, null for a name that is not one
     */
    public static function menuTemplate( $part )
    {
        $part = (string)$part;
        if ( !preg_match( '/^([a-z0-9_]+)navigationpart$/', $part, $m ) )
            return null;
        if ( $part === 'expauditnavigationpart' )
            $name = 'audit';
        else if ( strpos( $part, 'ez' ) === 0 )
            $name = substr( $m[1], 2 );
        else
            $name = $m[1];
        return $name === '' ? null : 'design:parts/' . $name . '/menu.tpl';
    }

    /**
     * A location for a suggestion: the names from the top down, with the middle left out as "…" when there are
     * more than $max.
     *
     * @param array $names
     * @param int $max at least 2
     * @return array
     */
    public static function shortenPath( array $names, $max = 3 )
    {
        $names = array_values( $names );
        $max = max( 2, (int)$max );
        if ( count( $names ) <= $max )
            return $names;
        return array_merge( array( $names[0], '…' ), array_slice( $names, -( $max - 1 ) ) );
    }

    /**
     * The answer: the groups in their order, each cut to its limit, empty ones left out.
     *
     * @param array $found group => array( 'items' => array, 'count' => int|null (null: the items found) )
     * @param array $settings settings()
     * @param array $titles group => its title
     * @param string $query
     * @param int $total the number of content results (what "View all results" shows)
     * @return array 'query', 'total', 'groups' => list of 'id', 'title', 'count', 'items'
     */
    public static function assemble( array $found, array $settings, array $titles, $query, $total )
    {
        $groups = array();
        foreach ( $settings['groups'] as $group )
        {
            if ( empty( $found[$group]['items'] ) )
                continue;
            $limit = isset( $settings['limits'][$group] ) ? (int)$settings['limits'][$group] : self::DEFAULT_LIMIT;
            if ( $limit <= 0 )
                continue;
            $items = array_slice( array_values( $found[$group]['items'] ), 0, $limit );
            $count = isset( $found[$group]['count'] ) ? (int)$found[$group]['count'] : count( $found[$group]['items'] );
            $groups[] = array( 'id' => $group,
                               'title' => isset( $titles[$group] ) ? (string)$titles[$group] : $group,
                               'count' => max( $count, count( $items ) ),
                               'items' => $items );
        }
        return array( 'query' => (string)$query, 'total' => max( 0, (int)$total ), 'groups' => $groups );
    }

    /**
     * The suggestions for the current user.
     *
     * @param string $query the typed text
     * @param array|null $settings settings(); null reads them
     * @return array assemble()
     */
    public static function suggest( $query, $settings = null )
    {
        if ( $settings === null )
            $settings = self::settings();
        $query = trim( (string)$query );
        $titles = self::titles();
        if ( self::length( $query ) < $settings['min_length'] )
            return self::assemble( array(), $settings, $titles, $query, 0 );

        $wanted = array();
        foreach ( $settings['groups'] as $group )
            $wanted[$group] = $settings['limits'][$group] > 0;

        $found = array();
        $total = 0;
        $users = array();
        if ( !empty( $wanted['users'] ) )
        {
            $users = self::users( $query, $settings['limits']['users'] );
            $found['users'] = array( 'items' => $users );
        }
        if ( !empty( $wanted['content'] ) || !empty( $wanted['pages'] ) )
        {
            $content = self::content( $query, $settings, !empty( $wanted['pages'] ) );
            // a user account found as content is shown once, in the users group
            $userObjects = array();
            foreach ( $users as $user )
                $userObjects[$user['object_id']] = true;
            $content['items'] = array_values( array_filter( $content['items'], function ( $item ) use ( $userObjects ) {
                return !isset( $userObjects[$item['object_id']] );
            } ) );
            if ( !empty( $wanted['content'] ) )
                $found['content'] = array( 'items' => $content['items'], 'count' => $content['count'] );
            $found['pages'] = array( 'items' => $content['pages'] );
            $total = $content['count'];
        }
        if ( !empty( $wanted['classes'] ) )
            $found['classes'] = array( 'items' => self::classes( $query ) );
        if ( !empty( $wanted['admin'] ) )
            $found['admin'] = array( 'items' => self::matchItems( self::adminPages( $settings ), $query ) );

        return self::assemble( $found, $settings, $titles, $query, $total );
    }

    /**
     * The titles of the groups, translated.
     *
     * @return array
     */
    public static function titles()
    {
        return array( 'content' => ezpI18n::tr( self::CONTEXT, 'Content' ),
                      'pages' => ezpI18n::tr( self::CONTEXT, 'Document pages' ),
                      'users' => ezpI18n::tr( self::CONTEXT, 'Users' ),
                      'classes' => ezpI18n::tr( self::CONTEXT, 'Content classes' ),
                      'admin' => ezpI18n::tr( self::CONTEXT, 'Administration' ) );
    }

    /**
     * The search answering the content group (backend()) for this site.
     *
     * @return array backend() plus 'search' => the engine object
     */
    public static function currentBackend()
    {
        $engine = eZSearch::getEngine();
        $pageHits = false;
        if ( in_array( 'ezfindpdfdeepsearch', eZExtension::activeExtensions(), true ) )
        {
            $info = eZFunctionHandler::moduleFunctionInfo( 'pdfdeepsearch' );
            $pageHits = is_object( $info ) && $info->isValid() && isset( $info->FunctionList['page_hits'] );
        }
        $backend = self::backend( is_object( $engine ) ? get_class( $engine ) : '', eZExtension::activeExtensions(), $pageHits );
        if ( !is_object( $engine ) || ( $backend['engine'] === 'kernel' && !( $engine instanceof eZSearchEngine ) ) )
            $engine = new eZSearchEngine();
        $backend['search'] = $engine;
        return $backend;
    }

    /**
     * The content group (and the PDF pages of the documents in it).
     *
     * @param string $query
     * @param array $settings
     * @param bool $withPages
     * @param string|null $thumbnailAlias null: the administration's items; a string (an image alias, or '' for none):
     *                                    the site's items, which also carry class_identifier, is_user and thumbnail
     * @return array 'items', 'count', 'pages'
     */
    public static function content( $query, array $settings, $withPages, $thumbnailAlias = null )
    {
        $none = array( 'items' => array(), 'count' => 0, 'pages' => array() );
        $limit = max( $settings['limits']['content'], $withPages ? $settings['limits']['pages'] : 0 );
        if ( $limit <= 0 )
            return $none;
        $backend = self::currentBackend();
        $siteIni = eZINI::instance();
        $text = self::searchText( $query, $settings['wildcard'],
                                  $siteIni->variable( 'SearchSettings', 'EnableWildcard' ) === 'true',
                                  (int)$siteIni->variable( 'SearchSettings', 'MinCharacterWildcard' ) );
        $searchParams = array( 'SearchOffset' => 0, 'SearchLimit' => min( 2 * $limit, 2 * self::MAX_LIMIT ) );
        // a site with a PathPrefix (/bold) reaches only the content below it, as its search page (content/search)
        if ( $thumbnailAlias !== null && $siteIni->hasVariable( 'SiteAccessSettings', 'PathPrefix' )
             && trim( (string)$siteIni->variable( 'SiteAccessSettings', 'PathPrefix' ), '/' ) !== '' )
        {
            $subTree = \Exponential\View\Kernel\Content\Search::pathPrefixSubTreeArray( $siteIni->variable( 'SiteAccessSettings', 'PathPrefix' ) );
            if ( $subTree )
                $searchParams['SearchSubTreeArray'] = $subTree;
        }
        try
        {
            // twice as many as shown: a hit the user may not read or one found twice is left out below
            $result = $backend['search']->search( $text, $searchParams );
        }
        catch ( Exception $e )
        {
            eZDebug::writeError( 'The search failed: ' . $e->getMessage(), __METHOD__ );
            return $none;
        }
        if ( !is_array( $result ) || !isset( $result['SearchResult'] ) || !is_array( $result['SearchResult'] ) )
            return $none;

        $items = array();
        $pages = array();
        $iconCache = array();
        $userClasses = array();
        $seen = array();
        // each document asked for its pages is one more request to the search engine: at most as many as the group
        // "Document pages" shows, also when a document has no page with the words
        $pageLookups = 0;
        foreach ( $result['SearchResult'] as $hit )
        {
            $node = self::nodeOf( $hit );
            if ( !$node )
                continue;
            $object = $node->attribute( 'object' );
            // eZSearch has applied the permissions; asked again here for a result of another engine
            if ( !$object instanceof eZContentObject || !$object->canRead() )
                continue;
            // an object once, though the index holds it more than once (a translation, a part of a document)
            if ( isset( $seen[(int)$object->attribute( 'id' )] ) )
                continue;
            $seen[(int)$object->attribute( 'id' )] = true;
            $classIdentifier = (string)$node->attribute( 'class_identifier' );
            $item = array( 'type' => 'content',
                           'title' => (string)$node->attribute( 'name' ),
                           'url' => self::url( $node ),
                           'meta' => (string)$node->attribute( 'class_name' ),
                           'icon' => self::classIcon( $classIdentifier, $iconCache ),
                           'path' => self::nodePath( $node ),
                           'object_id' => (int)$object->attribute( 'id' ) );
            if ( $thumbnailAlias !== null )
            {
                if ( !isset( $userClasses[$classIdentifier] ) )
                    $userClasses[$classIdentifier] = eZUser::isUserObject( $object );
                $item['class_identifier'] = $classIdentifier;
                $item['is_user'] = $userClasses[$classIdentifier];
                $item['thumbnail'] = $item['is_user'] ? '' : self::thumbnail( $object, $thumbnailAlias );
            }
            $items[] = $item;
            if ( $backend['pages'] && $withPages && count( $pages ) < $settings['limits']['pages']
                 && $pageLookups < $settings['limits']['pages']
                 && in_array( $classIdentifier, $settings['page_hit_classes'], true ) )
            {
                $pageLookups++;
                foreach ( self::pageHits( $item, $query, $settings['pages_per_document'] ) as $page )
                    $pages[] = $page;
            }
        }
        return array( 'items' => array_slice( $items, 0, max( 0, $settings['limits']['content'] ) ),
                      'count' => isset( $result['SearchCount'] ) ? (int)$result['SearchCount'] : count( $items ),
                      'pages' => $pages );
    }

    /**
     * The pages of a document that hold the words (pdfdeepsearch/page_hits, which checks the read permission of
     * the document itself).
     *
     * @param array $document the content item
     * @param string $query
     * @param int $limit
     * @return array
     */
    public static function pageHits( array $document, $query, $limit )
    {
        $hits = eZFunctionHandler::execute( 'pdfdeepsearch', 'page_hits',
                                            array( 'object_id' => $document['object_id'], 'query' => $query, 'limit' => $limit ) );
        if ( !is_array( $hits ) )
            return array();
        $pages = array();
        foreach ( $hits as $hit )
        {
            if ( !is_array( $hit ) || empty( $hit['url'] ) || !isset( $hit['page_number'] ) )
                continue;
            $url = (string)$hit['url'];
            if ( !preg_match( '#^[a-z][a-z0-9+.-]*:#i', $url ) && !preg_match( '#^//#', $url ) )
                eZURI::transformURI( $url );
            if ( !self::isSiteAddress( $url ) )
                continue;
            $snippet = isset( $hit['snippet'] ) ? trim( html_entity_decode( strip_tags( (string)$hit['snippet'] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) : '';
            $pages[] = array( 'type' => 'page',
                              'title' => $document['title'],
                              'url' => $url,
                              'meta' => ezpI18n::tr( self::CONTEXT, 'Page %number', null, array( '%number' => (int)$hit['page_number'] ) ),
                              'icon' => $document['icon'],
                              'excerpt' => self::cut( $snippet, 120 ),
                              'object_id' => $document['object_id'] );
        }
        return $pages;
    }

    /**
     * The node of a search result: the result itself, or the node it names (eZ Find's eZFindResultNode).
     *
     * @param mixed $hit
     * @return eZContentObjectTreeNode|null
     */
    public static function nodeOf( $hit )
    {
        if ( !is_object( $hit ) || !method_exists( $hit, 'attribute' ) )
            return null;
        if ( get_class( $hit ) === 'eZContentObjectTreeNode' )
            return $hit;
        $nodeID = (int)$hit->attribute( 'node_id' );
        if ( $nodeID <= 0 )
            $nodeID = (int)$hit->attribute( 'main_node_id' );
        $node = $nodeID > 0 ? eZContentObjectTreeNode::fetch( $nodeID ) : null;
        return $node instanceof eZContentObjectTreeNode ? $node : null;
    }

    /**
     * @param eZContentObjectTreeNode $node
     * @return string the node's address, as a link of the page
     */
    public static function url( $node )
    {
        $url = (string)$node->attribute( 'url_alias' );
        if ( $url === '' )
            $url = 'content/view/full/' . (int)$node->attribute( 'node_id' );
        eZURI::transformURI( $url );
        return $url;
    }

    /**
     * @param eZContentObjectTreeNode $node
     * @return array the names above the node, without the root, shortened (shortenPath())
     */
    public static function nodePath( $node )
    {
        $names = array();
        foreach ( (array)$node->attribute( 'path' ) as $pathNode )
        {
            if ( $pathNode instanceof eZContentObjectTreeNode && (int)$pathNode->attribute( 'depth' ) > 1 )
                $names[] = (string)$pathNode->attribute( 'name' );
        }
        return self::shortenPath( $names );
    }

    /**
     * The address of a class's small icon (icon.ini [ClassIcons], as the template operator class_icon finds it).
     *
     * @param string $identifier
     * @param array $cache identifier => address, filled
     * @return string
     */
    public static function classIcon( $identifier, array &$cache )
    {
        if ( isset( $cache[$identifier] ) )
            return $cache[$identifier];
        $value = $identifier;
        $operator = new eZWordToImageOperator();
        $tpl = eZTemplate::factory();
        $parameters = array( array( array( eZTemplate::TYPE_STRING, 'small' ) ),
                             array( array( eZTemplate::TYPE_STRING, '' ) ),
                             array( array( eZTemplate::TYPE_BOOLEAN, true ) ) );
        $operator->modify( $tpl, 'class_icon', $parameters, '', '', $value, array(), false );
        $cache[$identifier] = is_string( $value ) && self::isSiteAddress( $value ) ? $value : '';
        return $cache[$identifier];
    }

    /**
     * User accounts by login, e-mail or name that the current user may read.
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public static function users( $query, $limit )
    {
        if ( $limit <= 0 )
            return array();
        $db = eZDB::instance();
        // % and _ of the input match only themselves: escaped with !, written with ESCAPE '!', which every SQL engine
        // takes (SQLite has no default escape character), as eZRole and eZURLAliasQuery do
        $like = '%' . str_replace( array( '!', '%', '_' ), array( '!!', '!%', '!_' ), self::lower( $query ) ) . '%';
        $like = $db->escapeString( $like );
        $rows = $db->arrayQuery( "SELECT ezuser.contentobject_id AS id, ezuser.login AS login
                                  FROM ezuser, ezcontentobject
                                  WHERE ezuser.contentobject_id = ezcontentobject.id AND ezcontentobject.status = 1
                                    AND ( LOWER( ezuser.login ) LIKE '$like' ESCAPE '!' OR LOWER( ezuser.email ) LIKE '$like' ESCAPE '!'
                                          OR LOWER( ezcontentobject.name ) LIKE '$like' ESCAPE '!' )
                                  ORDER BY ezcontentobject.name",
                                 array( 'limit' => $limit * 4 ) );
        $candidates = array();
        foreach ( (array)$rows as $row )
            $candidates[] = array( 'id' => (int)$row['id'], 'login' => (string)$row['login'] );
        $iconCache = array();
        $userClasses = array();
        $users = array();
        foreach ( self::readable( $candidates, function ( $candidate ) {
                      $object = eZContentObject::fetch( $candidate['id'] );
                      return $object instanceof eZContentObject && $object->canRead() && $object->attribute( 'main_node_id' );
                  }, $limit ) as $candidate )
        {
            $object = eZContentObject::fetch( $candidate['id'] );
            $node = $object->attribute( 'main_node' );
            if ( !$node instanceof eZContentObjectTreeNode )
                continue;
            $users[] = array( 'type' => 'user',
                              'title' => (string)$object->attribute( 'name' ),
                              'url' => self::url( $node ),
                              'meta' => $candidate['login'],
                              'icon' => self::classIcon( (string)$object->attribute( 'class_identifier' ), $iconCache ),
                              'path' => self::nodePath( $node ),
                              'object_id' => (int)$candidate['id'] );
        }
        return $users;
    }

    /**
     * Content classes by name or identifier, for a user who may open the class module.
     *
     * @param string $query
     * @return array
     */
    public static function classes( $query )
    {
        $mayOpen = class_exists( 'expViewAccess' ) ? expViewAccess::canOpen( 'class/grouplist' )
                                                   : self::accessWord( eZUser::currentUser()->hasAccessTo( 'class', '*' ) );
        if ( !$mayOpen )
            return array();
        $classes = array();
        foreach ( eZContentClass::fetchList( eZContentClass::VERSION_STATUS_DEFINED, true ) as $class )
        {
            $classes[] = array( 'id' => $class->attribute( 'id' ), 'identifier' => $class->attribute( 'identifier' ),
                                'name' => $class->attribute( 'name' ) );
        }
        $iconCache = array();
        $userClasses = array();
        $items = array();
        foreach ( self::matchClasses( $classes, $query, true ) as $class )
        {
            $url = 'class/view/' . $class['id'];
            eZURI::transformURI( $url );
            $items[] = array( 'type' => 'class', 'title' => $class['title'], 'url' => $url, 'meta' => $class['identifier'],
                              'icon' => self::classIcon( $class['identifier'], $iconCache ) );
        }
        return $items;
    }

    /**
     * @param array $access eZUser::hasAccessTo()
     * @return bool
     */
    protected static function accessWord( $access )
    {
        return is_array( $access ) && isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
    }

    /**
     * The pages of the administration's menus the current user may open: each top tab and the links of its left
     * menu, drawn by the design as for a page of the tab. Kept per user and rights (and the address the site is reached at) for AdminPagesCacheTTL seconds.
     *
     * @param array $settings
     * @return array arrays with 'type', 'title', 'url'
     */
    public static function adminPages( array $settings )
    {
        $user = eZUser::currentUser();
        $access = eZSiteAccess::current();
        $key = md5( json_encode( array( (int)$user->attribute( 'contentobject_id' ), $user->accessArray(),
                                        is_array( $access ) && isset( $access['name'] ) ? $access['name'] : '',
                                        eZLocale::currentLocaleCode(), $settings['menu_skip_parts'],
                                        // the addresses differ by the way the site is reached (index.php, a host)
                                        eZSys::indexDir(), eZSys::hostname() ) ) );
        $file = eZSys::cacheDirectory() . '/searchsuggest/' . substr( $key, 0, 2 ) . '/' . $key . '.json';
        $handler = $settings['cache_ttl'] > 0 ? eZClusterFileHandler::instance( $file ) : null;
        if ( $handler && $handler->exists() && $handler->mtime() > time() - $settings['cache_ttl'] )
        {
            $cached = json_decode( (string)$handler->fetchContents(), true );
            if ( is_array( $cached ) )
                return $cached;
        }
        $pages = self::renderAdminPages( $settings['menu_skip_parts'] );
        if ( $handler )
            $handler->storeContents( json_encode( $pages ), 'searchsuggest', 'json' );
        return $pages;
    }

    /**
     * The tabs of the top menu the user may open, and the links of their left menus.
     *
     * @param array $skipParts navigation parts whose left menu is not read (a tree, not a list of pages)
     * @return array
     */
    public static function renderAdminPages( array $skipParts )
    {
        $tabs = array();
        $operator = new eZTopMenuOperator();
        $operator->modify( null, 'topmenu', array(), '', '', $tabs,
                           array( 'context' => 'navigation', 'filter_on_access' => true ), null );
        $pages = array();
        $seen = array();
        $tpl = eZTemplate::factory();
        foreach ( (array)$tabs as $tab )
        {
            if ( empty( $tab['access'] ) || empty( $tab['enabled'] ) || empty( $tab['url'] ) )
                continue;
            $name = (string)$tab['name'];
            $url = (string)$tab['url'];
            eZURI::transformURI( $url );
            if ( !isset( $seen[$url] ) )
            {
                $seen[$url] = true;
                $pages[] = array( 'type' => 'admin', 'title' => $name, 'url' => $url );
            }
            $part = (string)$tab['navigationpart_identifier'];
            $template = in_array( $part, $skipParts, true ) ? null : self::menuTemplate( $part );
            if ( $template === null )
                continue;
            // what the page layout gives its left menu
            $tpl->setVariable( 'uri_string', '' );
            $tpl->setVariable( 'ui_context', 'navigation' );
            $tpl->setVariable( 'navigation_part', array( 'identifier' => $part ) );
            $html = $tpl->fetch( $template );
            foreach ( self::menuLinks( is_string( $html ) ? $html : '', $name ) as $link )
            {
                if ( isset( $seen[$link['url']] ) )
                    continue;
                $seen[$link['url']] = true;
                $pages[] = array( 'type' => 'admin', 'title' => $link['title'], 'url' => $link['url'] );
            }
        }
        return $pages;
    }

    /**
     * @param string $text
     * @param int $max
     * @return string at most $max characters, "…" at the end when cut
     */
    public static function cut( $text, $max )
    {
        $text = (string)$text;
        if ( self::length( $text ) <= $max )
            return $text;
        return rtrim( function_exists( 'mb_substr' ) ? mb_substr( $text, 0, $max - 1, 'UTF-8' ) : substr( $text, 0, $max - 1 ) ) . '…';
    }

    /**
     * The settings of the site's suggestions (content/sitesuggest, parts/front_search_suggest.tpl): site.ini
     * [SiteSearchSuggestSettings] of the siteaccess, each with its default. Only content is ever suggested there.
     *
     * @param eZINI|null $ini site.ini
     * @return array 'enabled' bool, 'min_length' int, 'limit' int, 'exclude_classes' list, 'thumbnail_alias' string,
     *               'wildcard' string, 'cache_ttl' int, 'rate_limit' int, 'rate_window' int
     */
    public static function siteSettings( $ini = null )
    {
        if ( $ini === null )
            $ini = eZINI::instance();
        $get = function ( $name, $default ) use ( $ini ) {
            return $ini->hasVariable( 'SiteSearchSuggestSettings', $name ) ? $ini->variable( 'SiteSearchSuggestSettings', $name ) : $default;
        };
        $wildcard = strtolower( (string)$get( 'Wildcard', 'auto' ) );
        $exclude = array_values( array_filter( array_map( 'strval', (array)$get( 'ExcludeClasses', array( 'user', 'user_group' ) ) ) ) );
        return array(
            'enabled' => strtolower( (string)$get( 'Enabled', 'true' ) ) === 'true',
            'min_length' => max( 1, (int)$get( 'MinLength', 3 ) ),
            'limit' => self::limit( $get( 'Limit', 6 ) ),
            'exclude_classes' => $exclude,
            'thumbnail_alias' => preg_replace( '/[^a-z0-9_]/i', '', (string)$get( 'ThumbnailAlias', 'small' ) ),
            'wildcard' => in_array( $wildcard, array( 'auto', 'enabled', 'disabled' ), true ) ? $wildcard : 'auto',
            'cache_ttl' => max( 0, min( 3600, (int)$get( 'PublicCacheTTL', 60 ) ) ),
            'rate_limit' => max( 0, (int)$get( 'RateLimit', 60 ) ),
            'rate_window' => max( 1, (int)$get( 'RateWindow', 60 ) ),
        );
    }

    /**
     * The fields a site suggestion may carry, from a content item of content(): nothing of the account, the owner or
     * the object id. An item of an excluded class or a user account is dropped (null).
     *
     * @param array $item content() item plus 'class_identifier' and 'is_user'
     * @param array $settings siteSettings()
     * @return array|null 'type', 'title', 'url', 'meta', 'icon'
     */
    public static function siteItem( array $item, array $settings )
    {
        if ( !empty( $item['is_user'] ) || ( isset( $item['class_identifier'] ) && in_array( (string)$item['class_identifier'], $settings['exclude_classes'], true ) ) )
            return null;
        if ( !isset( $item['url'] ) || !self::isSiteAddress( (string)$item['url'] ) )
            return null;
        $icon = isset( $item['thumbnail'] ) && $item['thumbnail'] !== '' ? (string)$item['thumbnail'] : ( isset( $item['icon'] ) ? (string)$item['icon'] : '' );
        return array( 'type' => 'content',
                      'title' => (string)$item['title'],
                      'url' => (string)$item['url'],
                      'meta' => isset( $item['meta'] ) ? (string)$item['meta'] : '',
                      'icon' => $icon !== '' && self::isSiteAddress( $icon ) ? $icon : '' );
    }

    /**
     * The site's suggestions for the current user: content only, as far as the user may read it (the search applies
     * the read permissions, each hit is asked again), never user accounts, classes or administration pages.
     *
     * @param string $query
     * @param array|null $settings siteSettings()
     * @return array assemble() with the one group 'content'
     */
    public static function siteSuggest( $query, $settings = null )
    {
        if ( $settings === null )
            $settings = self::siteSettings();
        $query = trim( (string)$query );
        $groupSettings = array( 'groups' => array( 'content' ), 'limits' => array( 'content' => $settings['limit'] ) );
        $titles = array( 'content' => ezpI18n::tr( self::CONTEXT, 'Content' ) );
        if ( self::length( $query ) < $settings['min_length'] || $settings['limit'] <= 0 )
            return self::assemble( array(), $groupSettings, $titles, $query, 0 );
        // twice as many asked: a user account or an excluded class is left out afterwards
        $content = self::content( $query, array( 'limits' => array( 'content' => min( self::MAX_LIMIT, 2 * $settings['limit'] ), 'pages' => 0 ),
                                                 'wildcard' => $settings['wildcard'], 'page_hit_classes' => array(), 'pages_per_document' => 1 ),
                                  false, $settings['thumbnail_alias'] );
        $items = array();
        foreach ( $content['items'] as $item )
        {
            $item = self::siteItem( $item, $settings );
            if ( $item !== null )
                $items[] = $item;
        }
        return self::assemble( array( 'content' => array( 'items' => $items, 'count' => $content['count'] ) ),
                               $groupSettings, $titles, $query, $content['count'] );
    }

    /**
     * The address of a small image of the object (its first image attribute, in the given alias), or ''.
     *
     * @param eZContentObject $object
     * @param string $alias
     * @return string
     */
    public static function thumbnail( $object, $alias )
    {
        if ( $alias === '' || !$object instanceof eZContentObject )
            return '';
        foreach ( (array)$object->attribute( 'data_map' ) as $attribute )
        {
            if ( !$attribute instanceof eZContentObjectAttribute || $attribute->attribute( 'data_type_string' ) !== 'ezimage' || !$attribute->attribute( 'has_content' ) )
                continue;
            $content = $attribute->attribute( 'content' );
            $image = is_object( $content ) && method_exists( $content, 'imageAlias' ) ? $content->imageAlias( $alias ) : null;
            if ( !is_array( $image ) || empty( $image['url'] ) )
                return '';
            $url = '/' . ltrim( (string)$image['url'], '/' );
            eZURI::transformURI( $url, true );
            return self::isSiteAddress( $url ) ? $url : '';
        }
        return '';
    }

    /**
     * @param string $text
     * @return int characters
     */
    protected static function length( $text )
    {
        return function_exists( 'mb_strlen' ) ? mb_strlen( (string)$text, 'UTF-8' ) : strlen( (string)$text );
    }

    /**
     * @param string $text
     * @return string
     */
    protected static function lower( $text )
    {
        return function_exists( 'mb_strtolower' ) ? mb_strtolower( (string)$text, 'UTF-8' ) : strtolower( (string)$text );
    }
}
