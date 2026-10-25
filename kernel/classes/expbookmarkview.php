<?php
/**
 * File containing the expBookmarkView class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A bookmark of a system page: a page of a module view (setup/systemupgrade, exphelp/dashboard, audit/console/(user)/14
 * ...) instead of a node.
 *
 * Such a bookmark is an ordinary row of ezcontentbrowsebookmark with node_id 0: it has its name (the title the user
 * gave it), its folder and its place in the folder like a node bookmark, so the folders, the order, moving and removing
 * work for both kinds without a second code path. This table, expbookmark_view, holds what only a system page has: its
 * address (module_uri), keyed by the bookmark's id. The address is the module, the view and the view's parameters,
 * without the siteaccess and without a query string, so the same bookmark opens in every administration siteaccess.
 *
 * Whether the user may still open the page is decided when the bookmark is shown (expViewAccess, the rules a request
 * meets): a page the user may no longer open is shown without a link, marked "No access", and can still be renamed,
 * moved or removed.
 *
 * The table is created by the installer from share/db_schema.dba, by the update SQL of MySQL, PostgreSQL and SQLite,
 * and on every engine (Oracle and MongoDB included) by update/common/scripts/6.0/createbookmarkviewtable.php. Until it
 * exists nothing of this is offered and node bookmarks work as before (isAvailable()).
 *
 * \code
 * $bookmark = expBookmarkView::createNew( $userID, 'setup/systemupgrade', 'Upgrade check', $folderID );
 * $uris     = expBookmarkView::urisForUser( $userID );          // bookmark id => module/view address
 * $rows     = expBookmarkView::decorateRows( eZContentBrowseBookmarkFolder::fetchRowsForUser( $userID ) );
 * \endcode
 *
 * Guides: doc/eng-us/features/6.0/bookmarks-of-system-pages.md, doc/eng-us/guides/bookmarks.md
 *
 * @since 6.0.15
 * @package kernel
 */
class expBookmarkView extends eZPersistentObject
{
    const TABLE = 'expbookmark_view';

    /// The longest address taken, in characters.
    const MAX_URI_LENGTH = 2000;

    /** @var array database key => bool|null, see exists() */
    protected static $known = array();

    /** @var array user id => array( bookmark id => address ), for one request */
    protected static $uris = array();

    /** @var array address => access check, for one request and user */
    protected static $access = array();

    static function definition()
    {
        return array( 'fields' => array( 'bookmark_id' => array( 'name' => 'BookmarkID',
                                                                 'datatype' => 'integer',
                                                                 'default' => 0,
                                                                 'required' => true ),
                                         'user_id' => array( 'name' => 'UserID',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ),
                                         'module_uri' => array( 'name' => 'ModuleURI',
                                                                'datatype' => 'string',
                                                                'default' => '',
                                                                'required' => false ),
                                         'created' => array( 'name' => 'Created',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ) ),
                      'keys' => array( 'bookmark_id' ),
                      'sort' => array( 'bookmark_id' => 'asc' ),
                      'class_name' => 'expBookmarkView',
                      'name' => self::TABLE );
    }

    // ------------------------------------------------------------------ the table

    /**
     * Whether the table exists, so system pages can be bookmarked (asked once per database connection and process).
     *
     * @param eZDBInterface|null $db
     * @return bool
     */
    public static function isAvailable( $db = null )
    {
        return self::exists( $db ) === true;
    }

    /**
     * Whether the table exists.
     *
     * @param eZDBInterface|null $db
     * @param bool $fresh ask the database again
     * @return bool|null null when it cannot be told
     */
    public static function exists( $db = null, $fresh = false )
    {
        $db = $db ?: eZDB::instance();
        $key = spl_object_id( $db );
        if ( !$fresh && array_key_exists( $key, self::$known ) )
            return self::$known[$key];
        $rows = null;
        switch ( expAuditIndexSchema::type( $db ) )
        {
            case 'sqlite':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT name FROM sqlite_master WHERE type = 'table' AND name = 'expbookmark_view'" );
                break;
            case 'mysql':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT table_name AS name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'expbookmark_view'" );
                break;
            case 'postgresql':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT relname AS name FROM pg_class WHERE relkind = 'r' AND relname = 'expbookmark_view'" );
                break;
            case 'oracle':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT table_name AS name FROM user_tables WHERE table_name = 'EXPBOOKMARK_VIEW'" );
                break;
            case 'mongodb':
                if ( method_exists( $db, 'listCollectionNames' ) )
                {
                    try
                    {
                        return self::$known[$key] = in_array( self::TABLE, (array)$db->listCollectionNames(), true );
                    }
                    catch ( Throwable $e )
                    {
                        $rows = null;
                    }
                }
                break;
        }
        return self::$known[$key] = ( $rows === null ? null : count( $rows ) > 0 );
    }

    /** Forgets what exists() found and what was read for this request (tests, after install()). */
    public static function reset()
    {
        self::$known = array();
        self::$uris = array();
        self::$access = array();
    }

    /**
     * The table's definition, from share/db_schema.dba.
     *
     * @param string|null $file
     * @return array table name => definition
     */
    public static function schemaDefinition( $file = null )
    {
        $schema = array();
        $file = $file ?: eZSys::rootDir() . '/share/db_schema.dba';
        if ( is_readable( $file ) )
            $schema = eZDbSchema::read( $file, true );
        if ( isset( $schema['schema'] ) )
            $schema = $schema['schema'];
        return isset( $schema[self::TABLE] ) ? array( self::TABLE => $schema[self::TABLE] ) : array();
    }

    /**
     * Creates the table when it is missing, through the engine's schema handler.
     *
     * @param eZDBInterface|null $db
     * @param string[] $messages what was done, for the command line
     * @return bool whether the table exists afterwards
     */
    public static function install( $db = null, &$messages = array() )
    {
        $db = $db ?: eZDB::instance();
        $exists = self::exists( $db, true );
        if ( $exists === true )
        {
            $messages[] = 'the table expbookmark_view exists already';
            return true;
        }
        if ( $exists === null )
        {
            $messages[] = 'cannot tell whether the table expbookmark_view exists on this database';
            return false;
        }
        $schema = self::schemaDefinition();
        if ( !$schema )
        {
            $messages[] = 'share/db_schema.dba has no expbookmark_view table';
            return false;
        }
        $type = expAuditIndexSchema::type( $db );
        $handler = eZDbSchema::instance( array( 'type' => $type === 'mongodb' ? $db->databaseName() : $type,
                                                'instance' => $db, 'schema' => $schema ) );
        if ( !$handler )
        {
            $messages[] = "no schema handler for $type";
            return false;
        }
        $ok = $handler->insertSchema( array( 'schema' => true, 'data' => false, 'table_type' => $type === 'mysql' ? 'innodb' : '' ) );
        $messages[] = $ok ? 'created expbookmark_view' : 'could not create expbookmark_view';
        return $ok && self::exists( $db, true ) === true;
    }

    // ------------------------------------------------------------------ addresses (no database)

    /**
     * An address as it is stored: "module/view/param/(name)/value", no leading or trailing slash, no doubled slash,
     * no query string or fragment. Refused (false): an empty address, an address of another site (a scheme, //host),
     * backslashes, control characters, "." and ".." steps, and anything longer than MAX_URI_LENGTH.
     *
     * @param mixed $uri
     * @return string|false
     */
    public static function normalizeURI( $uri )
    {
        // control characters anywhere (a line break at the end too) are refused before the spaces are trimmed
        if ( !is_string( $uri ) || preg_match( '/[\x00-\x1F\x7F]/', $uri ) )
            return false;
        $uri = trim( $uri );
        if ( $uri === '' || preg_match( '#^[a-z][a-z0-9+.-]*:#i', $uri ) || strpos( $uri, '//' ) === 0
             || strpos( $uri, '\\' ) !== false || preg_match( '/[\x00-\x1F\x7F]/', $uri ) )
            return false;
        $uri = preg_replace( '/[?#].*$/s', '', $uri );
        $parts = array();
        foreach ( explode( '/', $uri ) as $part )
        {
            if ( $part === '' )
                continue;
            if ( $part === '.' || $part === '..' )
                return false;
            $parts[] = $part;
        }
        $uri = implode( '/', $parts );
        if ( $uri === '' || strlen( $uri ) > self::MAX_URI_LENGTH )
            return false;
        return $uri;
    }

    /**
     * The address of a module view, from what a page posts: normalized (normalizeURI()), a leading siteaccess name
     * that is not a module removed (an address copied with /admin/ in front), and the first step must be a module of
     * this installation. A URL alias (the address of a node) is not a module view: false.
     *
     * @param mixed $uri
     * @param string[]|null $siteAccesses the siteaccess names; null reads site.ini [SiteAccessSettings] AvailableSiteAccessList
     * @return string|false
     */
    public static function moduleURI( $uri, $siteAccesses = null )
    {
        $uri = self::normalizeURI( $uri );
        if ( $uri === false )
            return false;
        if ( $siteAccesses === null )
            $siteAccesses = (array)eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
        $parts = explode( '/', $uri );
        if ( count( $parts ) > 1 && in_array( $parts[0], $siteAccesses, true ) && !eZModule::exists( $parts[0] ) )
            array_shift( $parts );
        if ( !preg_match( '/^[A-Za-z0-9_]+$/', $parts[0] ) || !eZModule::exists( $parts[0] ) )
            return false;
        return implode( '/', $parts );
    }

    /**
     * The address of the page of this request with its view parameters: $requested_uri_string of the page layout
     * leaves out the "/(name)/value" parameters (content/bookmark/(folder)/top is "content/bookmark" there), so when
     * $uri is the request's address the request's parameters are put back, in their order.
     *
     * @param string $uri
     * @param array|null $request array( 'uri' => the request's address without parameters, 'parameters' => name => value );
     *                            null reads eZURI::instance()
     * @return string
     */
    public static function withRequestParameters( $uri, $request = null )
    {
        if ( $request === null )
        {
            $current = eZURI::instance();
            $request = array( 'uri' => $current->uriString(), 'parameters' => (array)$current->userParameters() );
        }
        $parameters = isset( $request['parameters'] ) ? (array)$request['parameters'] : array();
        if ( !$parameters || self::normalizeURI( (string)$uri ) === false
             || self::normalizeURI( (string)$uri ) !== self::normalizeURI( isset( $request['uri'] ) ? (string)$request['uri'] : '' ) )
            return (string)$uri;
        $suffix = '';
        foreach ( $parameters as $name => $value )
        {
            if ( !preg_match( '/^[A-Za-z0-9_]+$/', (string)$name ) )
                continue;
            $suffix .= '/(' . $name . ')/' . trim( (string)$value, '/' );
        }
        return rtrim( self::normalizeURI( (string)$uri ), '/' ) . $suffix;
    }

    /**
     * The name a new bookmark of a page gets: its title as the window shows it (the steps of the page's path, the
     * last first, joined with " / "), or its address when the page has no path.
     *
     * @param array $path the page's path ($module_result.path: items with 'text')
     * @param string $uri
     * @return string
     */
    public static function defaultName( array $path, $uri )
    {
        $texts = array();
        foreach ( array_reverse( $path ) as $item )
        {
            $text = is_array( $item ) && isset( $item['text'] ) ? trim( (string)$item['text'] ) : '';
            if ( $text !== '' )
                $texts[] = $text;
        }
        $name = eZContentBrowseBookmarkFolder::cleanName( implode( ' / ', $texts ) );
        return $name !== '' ? $name : eZContentBrowseBookmarkFolder::cleanName( '/' . trim( (string)$uri, '/' ) );
    }

    /**
     * What an address is, for its card: the module and the view.
     *
     * @param string $uri
     * @return array( 'module' => string, 'view' => string )
     */
    public static function parts( $uri )
    {
        $parts = explode( '/', trim( (string)$uri, '/' ) );
        return array( 'module' => (string)$parts[0], 'view' => isset( $parts[1] ) ? (string)$parts[1] : '' );
    }

    // ------------------------------------------------------------------ reading

    /**
     * The addresses of the system page bookmarks of a user, by bookmark id (one query, kept for the request).
     *
     * @param int $userID
     * @param bool $fresh read again
     * @return array bookmark id => address
     */
    public static function urisForUser( $userID, $fresh = false )
    {
        $userID = (int)$userID;
        if ( !$fresh && isset( self::$uris[$userID] ) )
            return self::$uris[$userID];
        $uris = array();
        if ( self::isAvailable() )
        {
            $rows = eZDB::instance()->arrayQuery( 'SELECT bookmark_id, module_uri FROM expbookmark_view WHERE user_id=' . $userID );
            foreach ( is_array( $rows ) ? $rows : array() as $row )
                $uris[(int)$row['bookmark_id']] = (string)$row['module_uri'];
        }
        return self::$uris[$userID] = $uris;
    }

    /**
     * The bookmark of a user for an address, or null.
     *
     * @param int $userID
     * @param string $uri a stored address (moduleURI())
     * @return eZContentBrowseBookmark|null
     */
    public static function findForUser( $userID, $uri )
    {
        foreach ( self::urisForUser( $userID ) as $bookmarkID => $stored )
        {
            if ( $stored !== $uri )
                continue;
            $bookmark = eZContentBrowseBookmark::fetch( $bookmarkID );
            if ( $bookmark && (int)$bookmark->attribute( 'user_id' ) === (int)$userID )
                return $bookmark;
        }
        return null;
    }

    /**
     * Whether the current user may open an address now (expViewAccess::check(), the rules of a request), kept for the
     * request.
     *
     * @param string $uri
     * @return array( 'result' => bool, 'module', 'view', 'reason' )
     */
    public static function access( $uri )
    {
        $key = eZUser::currentUserID() . ':' . $uri;
        if ( !isset( self::$access[$key] ) )
        {
            if ( (string)$uri === '' )
                self::$access[$key] = array( 'result' => false, 'module' => '', 'view' => '', 'reason' => 'empty' );
            else
            {
                try
                {
                    self::$access[$key] = expViewAccess::check( $uri );
                }
                catch ( Exception $e )
                {
                    eZDebug::writeWarning( 'Could not tell whether ' . $uri . ' can be opened: ' . $e->getMessage(), __METHOD__ );
                    self::$access[$key] = array( 'result' => false, 'module' => '', 'view' => '', 'reason' => 'error' );
                }
            }
        }
        return self::$access[$key];
    }

    /**
     * The rows of eZContentBrowseBookmarkFolder::fetchRowsForUser() with what the lists show of each bookmark:
     * 'kind' ('node' or 'view'); for a system page 'uri' (its address, '' when it is missing), 'module', 'view'
     * and 'can_open' (the current user may open it now). Folder rows are left as they are.
     *
     * @param array $rows
     * @param int|null $userID the owner of the rows (the current user when null)
     * @param bool $checkAccess ask whether each page can be opened (false: 'can_open' is true)
     * @return array
     */
    public static function decorateRows( array $rows, $userID = null, $checkAccess = true )
    {
        $userID = $userID === null ? eZUser::currentUserID() : (int)$userID;
        $uris = null;
        foreach ( $rows as $i => $row )
        {
            if ( !isset( $row['type'] ) || $row['type'] !== 'bookmark' )
                continue;
            $bookmark = $row['bookmark'];
            $nodeID = (int)( is_object( $bookmark ) ? $bookmark->attribute( 'node_id' ) : $bookmark['node_id'] );
            if ( $nodeID > 0 )
            {
                $rows[$i]['kind'] = 'node';
                continue;
            }
            if ( $uris === null )
                $uris = self::urisForUser( $userID );
            $uri = isset( $uris[(int)$row['id']] ) ? $uris[(int)$row['id']] : '';
            $parts = self::parts( $uri );
            $rows[$i]['kind'] = 'view';
            $rows[$i]['uri'] = $uri;
            $rows[$i]['module'] = $parts['module'];
            $rows[$i]['view'] = $parts['view'];
            if ( $uri === '' )
                $rows[$i]['can_open'] = false;
            else
            {
                $access = $checkAccess ? self::access( $uri ) : array( 'result' => true );
                $rows[$i]['can_open'] = (bool)$access['result'];
            }
        }
        return $rows;
    }

    // ------------------------------------------------------------------ writing

    /**
     * Bookmarks a page of a module view for a user, or updates the bookmark the user has for that address: a name
     * that is given replaces the old one, a folder that is given moves it there (as the last entry); without a folder
     * a new bookmark goes to the top level and an existing one stays where it is.
     *
     * @param int $userID
     * @param string $uri a stored address (moduleURI())
     * @param string $name the title; empty is the address
     * @param int|false $folderID a folder of the user, 0 for the top level, false for "where it is"
     * @return eZContentBrowseBookmark|false false when the table is missing or the address is not valid
     */
    public static function createNew( $userID, $uri, $name, $folderID = false )
    {
        $userID = (int)$userID;
        $uri = self::normalizeURI( $uri );
        if ( $uri === false || !self::isAvailable() )
            return false;
        $name = eZContentBrowseBookmarkFolder::cleanName( $name );
        if ( $name === '' )
            $name = eZContentBrowseBookmarkFolder::cleanName( '/' . $uri );
        if ( $folderID !== false && $folderID !== null )
        {
            $folderID = (int)$folderID;
            if ( $folderID && !eZContentBrowseBookmarkFolder::fetchForUser( $userID, $folderID ) )
                $folderID = 0;
        }

        $db = eZDB::instance();
        $db->begin();
        $bookmark = self::findForUser( $userID, $uri );
        if ( $bookmark )
        {
            $bookmark->setAttribute( 'name', $name );
            $bookmark->store();
            if ( $folderID !== false && $folderID !== null )
                eZContentBrowseBookmarkFolder::moveBookmark( $userID, $bookmark->attribute( 'id' ), $folderID );
            $bookmark = eZContentBrowseBookmark::fetch( $bookmark->attribute( 'id' ) );
        }
        else
        {
            $folderID = ( $folderID === false || $folderID === null ) ? 0 : $folderID;
            $bookmark = new eZContentBrowseBookmark( array( 'user_id' => $userID,
                                                            'node_id' => 0,
                                                            'name' => $name,
                                                            'folder_id' => $folderID,
                                                            'priority' => eZContentBrowseBookmarkFolder::nextBookmarkPriority( $userID, $folderID ) ) );
            $bookmark->store();
            $view = new expBookmarkView( array( 'bookmark_id' => (int)$bookmark->attribute( 'id' ),
                                                'user_id' => $userID,
                                                'module_uri' => $uri,
                                                'created' => time() ) );
            $view->store();
        }
        $db->commit();
        unset( self::$uris[$userID] );
        return $bookmark;
    }

    /**
     * Renames a system page bookmark of a user. Node bookmarks show the name of their node and are not renamed.
     *
     * @param int $userID
     * @param int $bookmarkID
     * @param string $name
     * @return bool false when the bookmark is not the user's, is a node bookmark, or the name is empty
     */
    public static function rename( $userID, $bookmarkID, $name )
    {
        $bookmark = eZContentBrowseBookmark::fetch( (int)$bookmarkID );
        if ( !$bookmark || (int)$bookmark->attribute( 'user_id' ) !== (int)$userID || (int)$bookmark->attribute( 'node_id' ) !== 0 )
            return false;
        $name = eZContentBrowseBookmarkFolder::cleanName( $name );
        if ( $name === '' )
            return false;
        $bookmark->setAttribute( 'name', $name );
        $bookmark->store();
        return true;
    }

    /**
     * Removes the addresses of bookmarks that are removed (eZContentBrowseBookmark::remove(), a folder removed with
     * its bookmarks, cleanup).
     *
     * @param int[] $bookmarkIDs
     */
    public static function removeForBookmarks( array $bookmarkIDs )
    {
        $ids = array_values( array_unique( array_filter( array_map( 'intval', $bookmarkIDs ) ) ) );
        if ( !$ids || !self::isAvailable() )
            return;
        eZDB::instance()->query( 'DELETE FROM expbookmark_view WHERE bookmark_id IN (' . implode( ',', $ids ) . ')' );
        self::$uris = array();
    }

    /** Removes the addresses of all system page bookmarks of all users (eZContentBrowseBookmark::cleanup()). */
    public static function cleanupAll()
    {
        if ( self::isAvailable() )
            eZDB::instance()->query( 'DELETE FROM expbookmark_view' );
        self::$uris = array();
    }

    /**
     * Runs the action posted to content/bookmark for system pages (field BookmarkViewAction):
     *   add     BookmarkViewURI, BookmarkViewName (optional), BookmarkFolderID (optional)
     *   rename  BookmarkID, BookmarkName
     * Only the user's own bookmarks are touched; a page is bookmarked only when the user may open it now.
     *
     * @param int $userID
     * @param eZHTTPTool $http
     * @return array( 'level' => 'feedback'|'error', 'text' => translated message, 'bookmark_id' => int )
     */
    public static function handleAction( $userID, $http )
    {
        $tr = function ( $text, $args = null ) { return ezpI18n::tr( 'kernel/content', $text, null, $args ); };
        $post = function ( $name, $default = '' ) use ( $http ) { return $http->hasPostVariable( $name ) ? $http->postVariable( $name ) : $default; };
        $action = (string)$post( 'BookmarkViewAction' );
        if ( !self::isAvailable() )
            return array( 'level' => 'error', 'bookmark_id' => 0,
                          'text' => $tr( 'System pages cannot be bookmarked yet: the database update of this version has not been run.' ) );
        switch ( $action )
        {
            case 'add':
                $uri = self::moduleURI( $post( 'BookmarkViewURI' ) );
                if ( $uri === false )
                    return array( 'level' => 'error', 'bookmark_id' => 0, 'text' => $tr( 'This page cannot be bookmarked: it is not a page of a module view.' ) );
                $access = self::access( $uri );
                if ( !$access['result'] )
                    return array( 'level' => 'error', 'bookmark_id' => 0, 'text' => $tr( 'This page cannot be bookmarked: you may not open it.' ) );
                $folder = $post( 'BookmarkFolderID', false );
                $bookmark = self::createNew( $userID, $uri, (string)$post( 'BookmarkViewName' ),
                                             ( $folder === false || $folder === '' ) ? false : (int)$folder );
                if ( !$bookmark )
                    return array( 'level' => 'error', 'bookmark_id' => 0, 'text' => $tr( 'The page could not be bookmarked.' ) );
                return array( 'level' => 'feedback', 'bookmark_id' => (int)$bookmark->attribute( 'id' ),
                              'text' => $tr( 'The page "%name" is in your bookmarks.', array( '%name' => $bookmark->attribute( 'name' ) ) ) );
            case 'rename':
                $id = (int)$post( 'BookmarkID', 0 );
                if ( !self::rename( $userID, $id, $post( 'BookmarkName' ) ) )
                    return array( 'level' => 'error', 'bookmark_id' => $id, 'text' => $tr( 'The bookmark could not be renamed: it needs a name.' ) );
                return array( 'level' => 'feedback', 'bookmark_id' => $id,
                              'text' => $tr( 'The bookmark was renamed to "%name".', array( '%name' => eZContentBrowseBookmarkFolder::cleanName( $post( 'BookmarkName' ) ) ) ) );
        }
        return array( 'level' => 'error', 'bookmark_id' => 0, 'text' => $tr( 'Unknown bookmark action.' ) );
    }

    /**
     * What the "Bookmark this page" action of the administration needs to know about the page shown
     * (fetch( 'content', 'view_bookmark', hash( 'uri', $requested_uri_string, 'path', $module_result.path ) )):
     *   'available' - system pages can be bookmarked here (the table exists, the user may use bookmarks, the address
     *                 is a module view)
     *   'uri'       - the address that is stored
     *   'name'      - the name a new bookmark gets (defaultName()), or the name of the existing bookmark
     *   'bookmark'  - the id of the user's bookmark of this page, 0 when there is none
     *   'folder_id' - the folder of that bookmark
     *
     * @param string $uri
     * @param array $path
     * @return array
     */
    public static function pageState( $uri, array $path = array() )
    {
        $state = array( 'available' => false, 'uri' => '', 'name' => '', 'bookmark' => 0, 'folder_id' => 0 );
        $user = eZUser::currentUser();
        if ( !$user instanceof eZUser || $user->isAnonymous() || !self::isAvailable() )
            return $state;
        $access = $user->hasAccessTo( 'content', 'bookmark' );
        if ( $access['accessWord'] === 'no' )
            return $state;
        $moduleURI = self::moduleURI( self::withRequestParameters( $uri ) );
        if ( $moduleURI === false )
            return $state;
        $state['available'] = true;
        $state['uri'] = $moduleURI;
        $state['name'] = self::defaultName( $path, $moduleURI );
        $bookmark = self::findForUser( $user->id(), $moduleURI );
        if ( $bookmark )
        {
            $state['bookmark'] = (int)$bookmark->attribute( 'id' );
            $state['name'] = (string)$bookmark->attribute( 'name' );
            $state['folder_id'] = (int)$bookmark->attribute( 'folder_id' );
        }
        return $state;
    }
}
