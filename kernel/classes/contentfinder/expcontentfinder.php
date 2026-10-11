<?php
/**
 * File containing the expContentFinder class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The content finder (module contentfinder, doc/features/6.0/content-finder.md): finds content objects by an id or
 * a path and describes each with what the current user may see of it.
 *
 *   $finder = new expContentFinder();
 *   $found = $finder->search( '/var/site/storage/images/media/images/photo/123-1-eng-GB/photo.jpg' );
 *   // $found['results']: one description per object the user may see
 *
 * Every lookup is one indexed query (or an exact match on a short column) with a hard row limit: integers are cast,
 * texts escaped, prefixes are LIKE 'x%' with the wildcards escaped; never a LIKE '%x%', but for the part of a URL alias (lookupURLAliasPart()). The text is recognised by
 * expContentFinderInput.
 *
 * Rights decide per result, through the kernel's own checks, so nobody sees more here than elsewhere: an object
 * needs content/read (eZContentObject::canRead()), a location content/read on that node, a version
 * content/versionread (or, for the published version, content/read), a trash entry content/restore or
 * content/cleantrash and the trash list's own permission SQL. A hit the user may not see is dropped exactly like a
 * miss: the result is the same "nothing found, or no access" either way, so an id cannot be probed.
 */
class expContentFinder
{
    /** At most this many objects are described */
    const DEFAULT_RESULT_LIMIT = 25;

    /** At most this many file or attribute rows are read per query */
    const DEFAULT_ROW_LIMIT = 50;

    /** @var int */
    protected $resultLimit;

    /** @var int */
    protected $rowLimit;

    /** @var array class attribute id => identifier, this instance */
    protected $attributeIdentifiers = array();

    /** @var array|null siteaccess => PathPrefix settings, this instance (siteAccessPrefixes()) */
    protected $siteAccessPrefixes = null;

    /**
     * @param int|null $resultLimit default: contentfinder.ini [ContentFinderSettings] ResultLimit (1 to 100)
     * @param int|null $rowLimit default: FileRowLimit (1 to 500)
     */
    public function __construct( $resultLimit = null, $rowLimit = null )
    {
        $this->resultLimit = self::bounded( $resultLimit !== null ? $resultLimit : self::setting( 'ResultLimit', self::DEFAULT_RESULT_LIMIT ), 1, 100, self::DEFAULT_RESULT_LIMIT );
        $this->rowLimit = self::bounded( $rowLimit !== null ? $rowLimit : self::setting( 'FileRowLimit', self::DEFAULT_ROW_LIMIT ), 1, 500, self::DEFAULT_ROW_LIMIT );
    }

    /**
     * @param mixed $value
     * @param int $min
     * @param int $max
     * @param int $default
     * @return int
     */
    protected static function bounded( $value, $min, $max, $default )
    {
        if ( !is_numeric( $value ) )
            return $default;
        return max( $min, min( $max, (int)$value ) );
    }

    /**
     * @param string $name a variable of contentfinder.ini [ContentFinderSettings]
     * @param mixed $default
     * @return mixed
     */
    public static function setting( $name, $default = null )
    {
        $ini = eZINI::instance( 'contentfinder.ini' );
        return $ini->hasVariable( 'ContentFinderSettings', $name ) ? $ini->variable( 'ContentFinderSettings', $name ) : $default;
    }

    /**
     * Whether the finder answers on the current siteaccess: Enabled=true, and the siteaccess is in SiteAccessList[]
     * or, with that list empty, its design list is an administration design (admin, admin2 ... adminui).
     *
     * @return bool
     */
    public static function isAvailableHere()
    {
        if ( self::setting( 'Enabled', 'true' ) !== 'true' )
            return false;
        $current = eZSiteAccess::current();
        $name = is_array( $current ) && isset( $current['name'] ) ? (string)$current['name'] : '';
        $list = self::setting( 'SiteAccessList', array() );
        $list = is_array( $list ) ? array_values( array_filter( $list, 'strlen' ) ) : array();
        if ( $list )
            return $name !== '' && in_array( $name, $list, true );

        $ini = eZINI::instance();
        $designs = array( (string)$ini->variable( 'DesignSettings', 'SiteDesign' ) );
        if ( $ini->hasVariable( 'DesignSettings', 'AdditionalSiteDesignList' ) )
            $designs = array_merge( $designs, (array)$ini->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) );
        foreach ( $designs as $design )
        {
            if ( is_string( $design ) && preg_match( '/^admin/', $design ) )
                return true;
        }
        return false;
    }

    /**
     * Whether the current user has a policy of a module function (any limitation).
     *
     * @param string $module
     * @param string $function
     * @param eZUser|null $user
     * @return bool
     */
    public static function hasPolicy( $module, $function, $user = null )
    {
        $user = $user instanceof eZUser ? $user : eZUser::currentUser();
        if ( !$user instanceof eZUser )
            return false;
        $access = $user->hasAccessTo( $module, $function );
        return isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
    }

    // ------------------------------------------------------------------ search

    /**
     * Finds the objects a search text stands for.
     *
     * @param mixed $text
     * @param mixed $type a type of expContentFinderInput::types()
     * @return array array( 'input' => expContentFinderInput::parse(), 'results' => descriptions (describe()),
     *               'count' => results shown, 'hidden' => objects found but not shown (for the audit only),
     *               'truncated' => more objects than ResultLimit )
     */
    public function search( $text, $type = 'auto' )
    {
        $input = expContentFinderInput::parse( $text, $type );
        $this->notice = '';
        $found = array( 'input' => $input, 'results' => array(), 'count' => 0, 'hidden' => 0, 'truncated' => false, 'notice' => '' );
        if ( $input['error'] !== '' )
            return $found;

        $hits = array();
        foreach ( $input['queries'] as $query )
        {
            foreach ( $this->lookup( $query['type'], $query['value'], isset( $query['host'] ) ? $query['host'] : '' ) as $hit )
            {
                $objectID = (int)$hit['object_id'];
                if ( $objectID <= 0 )
                    continue;
                $hits[$objectID][] = $hit;
            }
        }

        foreach ( $hits as $objectID => $objectHits )
        {
            if ( count( $found['results'] ) >= $this->resultLimit )
            {
                $found['truncated'] = true;
                break;
            }
            $description = $this->describe( $objectID, $objectHits );
            if ( $description === null )
                $found['hidden']++;
            else
                $found['results'][] = $description;
        }
        $found['count'] = count( $found['results'] );
        $found['notice'] = $this->notice;
        return $found;
    }

    /**
     * The raw hits of one query: what the database holds, before any right is checked.
     *
     * @param string $type
     * @param mixed $value from expContentFinderInput
     * @param string $host the host of a full URL (a URL alias query's 'host'), '' for none
     * @return array list of array( 'object_id', 'via', ... )
     */
    public function lookup( $type, $value, $host = '' )
    {
        switch ( $type )
        {
            case 'object':
                return $this->lookupObject( (int)$value );
            case 'node':
                return $this->lookupNode( (int)$value );
            case 'attribute':
                return $this->lookupAttribute( (int)$value );
            case 'version':
                return $this->lookupVersion( (int)$value );
            case 'remote':
                return $this->lookupRemoteID( (string)$value );
            case 'urlalias':
                return $this->lookupURLAlias( (string)$value, (string)$host );
            case 'file':
                return is_array( $value ) && isset( $value['path'] ) ? $this->lookupFile( (string)$value['path'], !empty( $value['prefix'] ) ) : array();
        }
        return array();
    }

    /**
     * @param string $sql
     * @param int $limit
     * @return array
     */
    protected function rows( $sql, $limit )
    {
        $rows = eZDB::instance()->arrayQuery( $sql, array( 'limit' => (int)$limit, 'offset' => 0 ) );
        return is_array( $rows ) ? $rows : array();
    }

    /**
     * @param int $id
     * @return array
     */
    protected function lookupObject( $id )
    {
        $hits = array();
        foreach ( $this->rows( 'SELECT id FROM ezcontentobject WHERE id = ' . (int)$id, 1 ) as $row )
            $hits[] = array( 'object_id' => (int)$row['id'], 'via' => 'object', 'value' => (int)$id );
        return $hits;
    }

    /**
     * A node in the tree, or a node that is in the trash.
     *
     * @param int $id
     * @return array
     */
    protected function lookupNode( $id )
    {
        $hits = array();
        foreach ( $this->rows( 'SELECT contentobject_id FROM ezcontentobject_tree WHERE node_id = ' . (int)$id, 1 ) as $row )
            $hits[] = array( 'object_id' => (int)$row['contentobject_id'], 'via' => 'node', 'value' => (int)$id, 'node_id' => (int)$id );
        if ( !$hits )
        {
            foreach ( $this->rows( 'SELECT contentobject_id FROM ezcontentobject_trash WHERE node_id = ' . (int)$id, 1 ) as $row )
                $hits[] = array( 'object_id' => (int)$row['contentobject_id'], 'via' => 'trash_node', 'value' => (int)$id, 'node_id' => (int)$id );
        }
        return $hits;
    }

    /**
     * @param int $id
     * @return array
     */
    protected function lookupAttribute( $id )
    {
        return $this->attributeHits( array( (int)$id => array() ), 'attribute', (int)$id );
    }

    /**
     * The objects of content object attributes, with the versions and languages of the rows found.
     *
     * @param array $files attribute id => list of array( 'file' => path, 'version' => int|null ) the attribute was found by
     * @param string $via
     * @param mixed $value
     * @return array one hit per object
     */
    protected function attributeHits( array $files, $via, $value )
    {
        if ( !$files )
            return array();
        $db = eZDB::instance();
        $sql = 'SELECT id, contentobject_id, version, language_code, data_type_string, contentclassattribute_id'
             . ' FROM ezcontentobject_attribute WHERE '
             . $db->generateSQLINStatement( array_map( 'intval', array_keys( $files ) ), 'id', false, true, 'int' )
             . ' ORDER BY contentobject_id, version DESC';
        $byObject = array();
        foreach ( $this->rows( $sql, $this->rowLimit * 4 ) as $row )
        {
            $attributeID = (int)$row['id'];
            $version = (int)$row['version'];
            $paths = array();
            foreach ( isset( $files[$attributeID] ) ? $files[$attributeID] : array() as $file )
            {
                // a binary file row names its version; an image file row stands for every version of the attribute
                if ( $file['version'] === null || (int)$file['version'] === $version )
                    $paths[] = $file['file'];
            }
            if ( $files[$attributeID] && !$paths )
                continue;
            $objectID = (int)$row['contentobject_id'];
            if ( !isset( $byObject[$objectID] ) )
                $byObject[$objectID] = array( 'object_id' => $objectID, 'via' => $via, 'value' => $value, 'rows' => array() );
            $byObject[$objectID]['rows'][] = array( 'attribute_id' => $attributeID, 'version' => $version,
                                                    'language' => (string)$row['language_code'],
                                                    'datatype' => (string)$row['data_type_string'],
                                                    'class_attribute_id' => (int)$row['contentclassattribute_id'],
                                                    'files' => array_values( array_unique( $paths ) ) );
        }
        return array_values( $byObject );
    }

    /**
     * @param int $id
     * @return array
     */
    protected function lookupVersion( $id )
    {
        $hits = array();
        foreach ( $this->rows( 'SELECT contentobject_id, version FROM ezcontentobject_version WHERE id = ' . (int)$id, 1 ) as $row )
            $hits[] = array( 'object_id' => (int)$row['contentobject_id'], 'via' => 'version', 'value' => (int)$id,
                             'version' => (int)$row['version'] );
        return $hits;
    }

    /**
     * An object's, a node's or a trashed node's remote id, exactly.
     *
     * @param string $remoteID
     * @return array
     */
    protected function lookupRemoteID( $remoteID )
    {
        $db = eZDB::instance();
        $remoteID = (string)$remoteID;
        $quoted = "'" . $db->escapeString( $remoteID ) . "'";
        $hits = array();
        // the database finds, the exact comparison here decides: MySQL's collations compare without case (and
        // without trailing spaces), the other engines do not; a remote id matches in its case everywhere
        foreach ( $this->rows( 'SELECT id, remote_id FROM ezcontentobject WHERE remote_id = ' . $quoted, 2 ) as $row )
            if ( (string)$row['remote_id'] === $remoteID )
                $hits[] = array( 'object_id' => (int)$row['id'], 'via' => 'object_remote_id', 'value' => $remoteID );
        foreach ( $this->rows( 'SELECT contentobject_id, node_id, remote_id FROM ezcontentobject_tree WHERE remote_id = ' . $quoted, 2 ) as $row )
            if ( (string)$row['remote_id'] === $remoteID )
                $hits[] = array( 'object_id' => (int)$row['contentobject_id'], 'via' => 'node_remote_id', 'value' => $remoteID,
                                 'node_id' => (int)$row['node_id'] );
        foreach ( $this->rows( 'SELECT contentobject_id, node_id, remote_id FROM ezcontentobject_trash WHERE remote_id = ' . $quoted, 2 ) as $row )
            if ( (string)$row['remote_id'] === $remoteID )
                $hits[] = array( 'object_id' => (int)$row['contentobject_id'], 'via' => 'trash_node', 'value' => $remoteID,
                                 'node_id' => (int)$row['node_id'] );
        return $hits;
    }

    /**
     * A URL alias, exactly, resolved by eZURLAliasML. The text is tried as it is first; then as the page address of
     * a siteaccess: the siteaccess the kernel would match for it (eZSiteAccess::match(): its URI segment taken off,
     * by host with the host of a full URL), with that siteaccess's PathPrefix put in front (unless the path already
     * starts with it or PathPrefixExclude names its first element); then with each other PathPrefix of the
     * siteaccesses. Exact matches only, the first that resolves wins.
     *
     * @param string $path
     * @param string $host the host of a full URL, '' for a path
     * @return array
     */
    protected function lookupURLAlias( $path, $host = '' )
    {
        $siteAccesses = $this->siteAccessPrefixes();
        $matched = null;
        if ( class_exists( 'eZSiteAccess' ) )
        {
            try
            {
                $match = eZSiteAccess::match( new eZURI( $path ), (string)$host, 80, '/index.php' );
                if ( is_array( $match ) && isset( $match['name'] ) && isset( $siteAccesses[$match['name']] ) && empty( $match['unmatched'] ) )
                    $matched = array( 'name' => (string)$match['name'],
                                      'uri_part' => isset( $match['uri_part'] ) && is_array( $match['uri_part'] ) ? array_values( $match['uri_part'] ) : array() );
            }
            catch ( Throwable $e )
            {
                eZDebug::writeWarning( 'Content finder: no siteaccess match for the path: ' . $e->getMessage(), __METHOD__ );
            }
        }
        foreach ( self::urlAliasCandidates( $path, $siteAccesses, $matched ) as $candidate )
        {
            $nodeID = eZURLAliasML::fetchNodeIDByPath( $candidate['path'] );
            if ( !$nodeID )
                continue;
            $hits = array();
            foreach ( $this->rows( 'SELECT contentobject_id FROM ezcontentobject_tree WHERE node_id = ' . (int)$nodeID, 1 ) as $row )
                $hits[] = array( 'object_id' => (int)$row['contentobject_id'], 'via' => 'urlalias', 'value' => $candidate['path'], 'node_id' => (int)$nodeID,
                                 'siteaccess' => $candidate['siteaccess'], 'prefix' => $candidate['prefix'] );
            return $hits;
        }
        $hits = $this->lookupURLAliasTail( $path );
        return $hits ? $hits : $this->lookupURLAliasPart( $path );
    }

    /**
     * The words of a part of an alias: the last element of the path, split at dashes, underscores and spaces, in
     * lower case. Pure.
     *
     * @param string $path
     * @return string[]
     */
    public static function aliasPartWords( $path )
    {
        $elements = array_values( array_filter( explode( '/', trim( (string)$path, '/' ) ), 'strlen' ) );
        $last = $elements ? (string)end( $elements ) : '';
        $lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $last, 'UTF-8' ) : strtolower( $last );
        return array_values( array_filter( preg_split( '/[-_\s]+/u', $lower ), 'strlen' ) );
    }

    /**
     * The LIKE pattern of a part of an alias (to be used on LOWER( text ) with ESCAPE '!'): the words in order,
     * anything in front and behind, one character between them (the database narrows; partMatches() decides).
     * Pure.
     *
     * @param string[] $words from aliasPartWords()
     * @return string
     */
    public static function aliasPartPattern( array $words )
    {
        $escaped = array();
        foreach ( $words as $word )
            $escaped[] = strtr( (string)$word, array( '!' => '!!', '%' => '!%', '_' => '!_' ) );
        return '%' . implode( '_', $escaped ) . '%';
    }

    /**
     * Whether an alias element contains the words, in order, each separated by one dash, underscore or space,
     * without case. Pure.
     *
     * @param string $text
     * @param string[] $words
     * @return bool
     */
    public static function partMatches( $text, array $words )
    {
        if ( !$words )
            return false;
        $quoted = array();
        foreach ( $words as $word )
            $quoted[] = preg_quote( (string)$word, '/' );
        return (bool)preg_match( '/' . implode( '[-_ ]', $quoted ) . '/iu', (string)$text );
    }

    /**
     * How close an alias element is to the words, null when it does not contain them (partMatches()). Pure.
     *  0 the element is the words; 1 it starts with them as whole words; 2 it has them as whole words;
     *  3 it starts with them, or has them at the start of a word; 4 they are inside a word
     *
     * @param string $text
     * @param string[] $words
     * @return int|null
     */
    public static function partRank( $text, array $words )
    {
        if ( !self::partMatches( $text, $words ) )
            return null;
        $quoted = array();
        foreach ( $words as $word )
            $quoted[] = preg_quote( (string)$word, '/' );
        $words = implode( '[-_ ]', $quoted );
        $text = (string)$text;
        if ( preg_match( '/^' . $words . '$/iu', $text ) )
            return 0;
        if ( preg_match( '/^' . $words . '(?=[-_ ])/iu', $text ) )
            return 1;
        if ( preg_match( '/(?<=[-_ ])' . $words . '(?=[-_ ]|$)/iu', $text ) )
            return 2;
        if ( preg_match( '/(^|(?<=[-_ ]))' . $words . '/iu', $text ) )
            return 3;
        return 4;
    }

    /** @var string '' or why a lookup gave nothing although the text was taken (alias_part_too_short) */
    protected $notice = '';

    /**
     * The very last resort for a URL alias, when the exact, siteaccess, prefix and last-part lookups found nothing:
     * the alias elements that CONTAIN the text (the last element of the path; "test product" also finds
     * test-product). contentfinder.ini PartialAliasSearch=enabled, at least MinimumPartLength characters (3).
     * One LIKE '%...%' on LOWER( text ) of ezurlalias_ml, escaped, which reads the whole table; current entries
     * first, history (marked) only when no current one contains it. Each match with its full path, one per node,
     * at most ResultLimit + 1 (so the page can say there were more).
     *
     * @param string $path
     * @return array
     */
    protected function lookupURLAliasPart( $path )
    {
        // a path (a text with a slash) is looked for as a path only; a part is one element
        if ( self::setting( 'PartialAliasSearch', 'enabled' ) !== 'enabled' || strpos( trim( (string)$path, '/' ), '/' ) !== false )
            return array();
        $words = self::aliasPartWords( $path );
        $minimum = self::bounded( self::setting( 'MinimumPartLength', 3 ), 1, 100, 3 );
        $length = function_exists( 'mb_strlen' ) ? mb_strlen( implode( ' ', $words ), 'UTF-8' ) : strlen( implode( ' ', $words ) );
        if ( !$words || $length < $minimum )
        {
            $this->notice = 'alias_part_too_short';
            return array();
        }
        $db = eZDB::instance();
        $like = "LOWER( text ) LIKE '" . $db->escapeString( self::aliasPartPattern( $words ) ) . "' ESCAPE '!'";
        $wanted = $this->resultLimit + 1;
        foreach ( array( true, false ) as $currentOnly )
        {
            $sql = 'SELECT id, parent, text, action, is_original, is_alias FROM ezurlalias_ml WHERE ' . $like
                 . ( $currentOnly ? ' AND is_original = 1' : ' AND is_original = 0' );
            // up to PartRowLimit rows, the closest matches first (partRank()); an alias row can outlive its node, so
            // only the nodes that exist count towards the cap
            $candidates = array();
            foreach ( $this->rows( $sql, self::bounded( self::setting( 'PartRowLimit', 2000 ), 1, 100000, 2000 ) ) as $row )
            {
                $rank = self::partRank( (string)$row['text'], $words );
                if ( $rank !== null && preg_match( '/^eznode:([0-9]+)$/', (string)$row['action'] ) )
                    $candidates[] = array( $rank, strlen( (string)$row['text'] ), (int)$row['id'], $row );
            }
            sort( $candidates );
            $hits = array();
            $seen = array();
            foreach ( $candidates as $candidate )
            {
                $row = $candidate[3];
                preg_match( '/^eznode:([0-9]+)$/', (string)$row['action'], $m );
                $nodeID = (int)$m[1];
                if ( isset( $seen[$nodeID] ) )
                    continue;
                $seen[$nodeID] = true;
                $tree = $this->rows( 'SELECT contentobject_id FROM ezcontentobject_tree WHERE node_id = ' . $nodeID, 1 );
                if ( !$tree )
                    continue;
                $full = $this->aliasPathUpwards( (int)$row['parent'], array( (string)$row['text'] ), array() );
                if ( $full === null )
                    continue;
                $hits[] = array( 'object_id' => (int)$tree[0]['contentobject_id'], 'via' => 'urlalias_part', 'value' => $full,
                                 'node_id' => $nodeID, 'history' => !$currentOnly, 'part' => implode( ' ', $words ) );
                if ( count( $hits ) >= $wanted )
                    break;
            }
            if ( $hits )
                return $hits;
        }
        return array();
    }

    /**
     * The last resort for a URL alias: the alias elements whose own text is the last element of the path (as
     * eZURLAliasML compares, without case), each verified upwards against the rest of the path, with its full path.
     * Exact matches only: the text is looked up by the indexed text column, and only when that finds nothing by
     * text_md5 (the md5 of the lower-case text, as the kernel stores it). History entries (old addresses) are listed
     * only when nothing current matches, and marked.
     *
     * @param string $path
     * @return array one hit per node, at most ResultLimit
     */
    protected function lookupURLAliasTail( $path )
    {
        $elements = array_values( array_filter( explode( '/', trim( (string)$path, '/' ) ), 'strlen' ) );
        if ( !$elements )
            return array();
        $db = eZDB::instance();
        $last = (string)end( $elements );
        $lower = eZURLAliasML::strtolower( $last );
        $columns = 'id, parent, text, action, is_original, is_alias';
        $rows = $this->rows( "SELECT $columns FROM ezurlalias_ml WHERE text = '" . $db->escapeString( $last ) . "'", $this->rowLimit );
        if ( !$rows && $lower !== $last )
            $rows = $this->rows( "SELECT $columns FROM ezurlalias_ml WHERE text = '" . $db->escapeString( $lower ) . "'", $this->rowLimit );
        if ( !$rows )
            $rows = $this->rows( "SELECT $columns FROM ezurlalias_ml WHERE text_md5 = '" . $db->escapeString( md5( $lower ) ) . "'", $this->rowLimit );

        $current = array();
        $history = array();
        foreach ( $rows as $row )
        {
            if ( eZURLAliasML::strtolower( (string)$row['text'] ) !== $lower || !preg_match( '/^eznode:([0-9]+)$/', (string)$row['action'], $m ) )
                continue;
            $nodeID = (int)$m[1];
            $full = $this->aliasPathUpwards( (int)$row['parent'], array( (string)$row['text'] ), array_slice( $elements, 0, -1 ) );
            if ( $full === null )
                continue;
            $isHistory = (int)$row['is_original'] === 0 && (int)$row['is_alias'] === 0;
            if ( $isHistory && !isset( $history[$nodeID] ) )
                $history[$nodeID] = array( 'path' => $full, 'history' => true );
            else if ( !$isHistory && !isset( $current[$nodeID] ) )
                $current[$nodeID] = array( 'path' => $full, 'history' => false );
        }
        $found = $current ? $current : $history;
        $hits = array();
        foreach ( array_slice( $found, 0, $this->resultLimit, true ) as $nodeID => $alias )
        {
            foreach ( $this->rows( 'SELECT contentobject_id FROM ezcontentobject_tree WHERE node_id = ' . (int)$nodeID, 1 ) as $row )
                $hits[] = array( 'object_id' => (int)$row['contentobject_id'], 'via' => 'urlalias_tail', 'value' => $alias['path'],
                                 'node_id' => (int)$nodeID, 'history' => $alias['history'] );
        }
        return $hits;
    }

    /**
     * The full path of an alias element, read upwards from its parent; null when the elements above do not end with
     * $tail (compared without case) or the chain is broken or deeper than 64.
     *
     * @param int $parentID
     * @param string[] $path the elements found so far, top last
     * @param string[] $tail the elements of the search text still to verify, in order
     * @return string|null
     */
    protected function aliasPathUpwards( $parentID, array $path, array $tail )
    {
        for ( $depth = 0; $parentID > 0; $depth++ )
        {
            if ( $depth >= 64 )
                return null;
            $rows = $this->rows( 'SELECT parent, text, is_original FROM ezurlalias_ml WHERE id = ' . (int)$parentID, 20 );
            if ( !$rows )
                return null;
            // the original entry of the element; any of its languages otherwise
            usort( $rows, function ( $a, $b ) { return (int)$b['is_original'] - (int)$a['is_original']; } );
            $row = $rows[0];
            array_unshift( $path, (string)$row['text'] );
            $parentID = (int)$row['parent'];
        }
        $start = count( $path ) - 1 - count( $tail );
        if ( $start < 0 )
            return null;
        foreach ( $tail as $i => $element )
        {
            if ( eZURLAliasML::strtolower( $path[$start + $i] ) !== eZURLAliasML::strtolower( (string)$element ) )
                return null;
        }
        return implode( '/', $path );
    }

    /**
     * The PathPrefix and PathPrefixExclude[] of every siteaccess (AvailableSiteAccessList[], RelatedSiteAccessList[]),
     * each read from that siteaccess's own site.ini; once per instance.
     *
     * @return array name => array( 'prefix' => without slashes, '' for none, 'exclude' => string[] )
     */
    protected function siteAccessPrefixes()
    {
        if ( $this->siteAccessPrefixes !== null )
            return $this->siteAccessPrefixes;
        $ini = eZINI::instance();
        $names = array();
        foreach ( array( 'AvailableSiteAccessList', 'RelatedSiteAccessList' ) as $list )
            if ( $ini->hasVariable( 'SiteAccessSettings', $list ) )
                $names = array_merge( $names, (array)$ini->variable( 'SiteAccessSettings', $list ) );
        $this->siteAccessPrefixes = array();
        foreach ( array_unique( array_filter( array_map( 'strval', $names ), 'strlen' ) ) as $name )
        {
            $prefix = '';
            $exclude = array();
            try
            {
                $saINI = eZSiteAccess::getIni( $name, 'site.ini' );
                if ( $saINI->hasVariable( 'SiteAccessSettings', 'PathPrefix' ) )
                    $prefix = trim( (string)$saINI->variable( 'SiteAccessSettings', 'PathPrefix' ), '/' );
                if ( $saINI->hasVariable( 'SiteAccessSettings', 'PathPrefixExclude' ) )
                    $exclude = array_values( array_filter( array_map( function ( $item ) { return trim( (string)$item, '/' ); },
                                                                      (array)$saINI->variable( 'SiteAccessSettings', 'PathPrefixExclude' ) ), 'strlen' ) );
            }
            catch ( Throwable $e )
            {
                eZDebug::writeWarning( "Content finder: the settings of siteaccess $name: " . $e->getMessage(), __METHOD__ );
            }
            $this->siteAccessPrefixes[$name] = array( 'prefix' => $prefix, 'exclude' => $exclude );
        }
        return $this->siteAccessPrefixes;
    }

    /**
     * The paths a URL alias text is tried as, in order, each once. Pure.
     *
     *  1. the path as it is;
     *  2. with the matched siteaccess (eZSiteAccess::match() of the text): its URI segment taken off, its PathPrefix
     *     put in front (prefixed()), then without the prefix;
     *  3. a first segment that names a siteaccess taken off, that siteaccess's PathPrefix put in front, then without;
     *  4. the path with each distinct PathPrefix of the siteaccesses in front.
     *
     * @param string $path without leading or trailing slashes
     * @param array $siteAccesses from siteAccessPrefixes()
     * @param array|null $matched array( 'name', 'uri_part' => string[] ) of the siteaccess the kernel matched, or null
     * @return array list of array( 'path', 'siteaccess' => '' or the name, 'prefix' => '' or the PathPrefix used )
     */
    public static function urlAliasCandidates( $path, array $siteAccesses, $matched = null )
    {
        $path = trim( (string)$path, '/' );
        $list = array();
        $add = function ( $candidate, $siteAccess, $prefix ) use ( &$list ) {
            $candidate = trim( $candidate, '/' );
            if ( $candidate !== '' && !isset( $list[$candidate] ) )
                $list[$candidate] = array( 'path' => $candidate, 'siteaccess' => (string)$siteAccess, 'prefix' => (string)$prefix );
        };
        $add( $path, '', '' );
        $elements = explode( '/', $path );

        $bySiteAccess = array();
        if ( is_array( $matched ) && isset( $matched['name'] ) && isset( $siteAccesses[$matched['name']] ) )
        {
            $rest = $elements;
            $part = isset( $matched['uri_part'] ) ? array_values( (array)$matched['uri_part'] ) : array();
            if ( $part && count( $rest ) > count( $part )
                 && strtolower( implode( '/', array_slice( $rest, 0, count( $part ) ) ) ) === strtolower( implode( '/', $part ) ) )
                $rest = array_slice( $rest, count( $part ) );
            $bySiteAccess[] = array( $matched['name'], implode( '/', $rest ) );
        }
        if ( count( $elements ) > 1 && isset( $siteAccesses[$elements[0]] ) )
            $bySiteAccess[] = array( $elements[0], implode( '/', array_slice( $elements, 1 ) ) );
        foreach ( $bySiteAccess as $item )
        {
            list( $name, $rest ) = $item;
            $settings = $siteAccesses[$name];
            $prefixed = self::prefixed( $rest, $settings['prefix'], $settings['exclude'] );
            $add( $prefixed, $name, $prefixed !== $rest ? $settings['prefix'] : '' );
            $add( $rest, $name, '' );
        }

        foreach ( $siteAccesses as $name => $settings )
        {
            if ( $settings['prefix'] === '' )
                continue;
            $prefixed = self::prefixed( $path, $settings['prefix'], $settings['exclude'] );
            if ( $prefixed !== $path )
                $add( $prefixed, $name, $settings['prefix'] );
        }
        return array_values( $list );
    }

    /**
     * A path as a siteaccess with this PathPrefix stores it, as eZURLAliasML::translate() does: the prefix in front,
     * unless the path already starts with it or its first element starts with an entry of PathPrefixExclude[].
     *
     * @param string $path
     * @param string $prefix
     * @param string[] $exclude
     * @return string
     */
    public static function prefixed( $path, $prefix, array $exclude = array() )
    {
        $path = trim( (string)$path, '/' );
        $prefix = trim( (string)$prefix, '/' );
        if ( $prefix === '' )
            return $path;
        if ( preg_match( '#^' . preg_quote( $prefix, '#' ) . '(/.*)?$#i', $path ) )
            return $path;
        foreach ( $exclude as $item )
        {
            $item = trim( (string)$item, '/' );
            if ( $item !== '' && preg_match( '#^' . preg_quote( $item, '#' ) . '(/.*)?$#i', $path ) )
                return $path;
        }
        return $path === '' ? $prefix : $prefix . '/' . $path;
    }

    /**
     * A file below var/: an image file (ezimagefile.filepath, every alias of it), a binary file or a media file
     * (ezbinaryfile, ezmedia: the file name in storage/original/<group>/). Exact, or every file below a prefix.
     *
     * @param string $path var/...
     * @param bool $prefix
     * @return array
     */
    protected function lookupFile( $path, $prefix )
    {
        $db = eZDB::instance();
        $files = array();
        $condition = $prefix
            ? "filepath LIKE '" . $db->escapeString( expContentFinderInput::likePrefix( $path ) ) . "' ESCAPE '!'"
            : "filepath = '" . $db->escapeString( $path ) . "'";
        // as for remote ids: the database narrows (MySQL and SQLite's LIKE ignore case), matches() decides in the case
        // of the path, as the file system does
        foreach ( $this->rows( 'SELECT contentobject_attribute_id, filepath FROM ezimagefile WHERE ' . $condition, $this->rowLimit ) as $row )
            if ( self::matches( (string)$row['filepath'], $path, $prefix ) )
                $files[(int)$row['contentobject_attribute_id']][] = array( 'file' => (string)$row['filepath'], 'version' => null );

        // storage/original/<group>/<name>: the binary and media files keep the name and the mime type
        if ( preg_match( '#^(.*/)?storage/original/([a-z0-9.+\-]+)(?:/([^/]*))?$#i', rtrim( $path, '/' ) . ( substr( $path, -1 ) === '/' ? '/' : '' ), $m ) )
        {
            $group = strtolower( $m[2] );
            $name = isset( $m[3] ) ? (string)$m[3] : '';
            $storage = rtrim( $m[1], '/' ) . ( $m[1] !== '' ? '/' : '' ) . 'storage/original/' . $m[2] . '/';
            if ( $name !== '' || $prefix )
            {
                $nameCondition = $prefix
                    ? ( $name === '' ? '' : "filename LIKE '" . $db->escapeString( expContentFinderInput::likePrefix( $name ) ) . "' ESCAPE '!' AND " )
                    : "filename = '" . $db->escapeString( $name ) . "' AND ";
                $groupCondition = "mime_type LIKE '" . $db->escapeString( expContentFinderInput::likePrefix( $group . '/' ) ) . "' ESCAPE '!'";
                foreach ( array( 'ezbinaryfile', 'ezmedia' ) as $table )
                {
                    $sql = 'SELECT contentobject_attribute_id, version, filename FROM ' . $table . ' WHERE ' . $nameCondition . $groupCondition;
                    foreach ( $this->rows( $sql, $this->rowLimit ) as $row )
                        if ( self::matches( (string)$row['filename'], $name, $prefix ) )
                            $files[(int)$row['contentobject_attribute_id']][] = array( 'file' => $storage . (string)$row['filename'], 'version' => (int)$row['version'] );
                }
            }
        }
        return $this->attributeHits( $files, 'file', $path . ( $prefix && substr( $path, -1 ) !== '/' ? '*' : '' ) );
    }

    /**
     * Whether a stored value is the text looked for, in its case: equal, or (prefix) starting with it.
     *
     * @param string $stored
     * @param string $text
     * @param bool $prefix
     * @return bool
     */
    public static function matches( $stored, $text, $prefix )
    {
        $stored = (string)$stored;
        $text = (string)$text;
        return $prefix ? strncmp( $stored, $text, strlen( $text ) ) === 0 : $stored === $text;
    }

    // ------------------------------------------------------------------ describe

    /**
     * What the current user may see of one object and the hits that found it; null when nothing.
     *
     * @param int $objectID
     * @param array $hits from lookup()
     * @return array|null
     */
    public function describe( $objectID, array $hits )
    {
        $object = eZContentObject::fetch( (int)$objectID );
        if ( !$object instanceof eZContentObject )
            return null;
        $user = eZUser::currentUser();
        $canRead = (bool)$object->canRead();
        $trash = $this->trashEntries( $object, $user );
        if ( !$canRead && !$trash )
            return null;

        $versions = $this->versions( $object, $canRead );
        $locations = $canRead ? $this->locations( $object ) : array();

        $matches = array();
        foreach ( $hits as $hit )
        {
            $match = $this->visibleMatch( $hit, $object, $canRead, $versions, $locations, $trash );
            if ( $match !== null )
                $matches[] = $match;
        }
        if ( !$matches )
            return null;

        $class = $object->contentClass();
        $owner = $object->attribute( 'owner' );
        $section = eZSection::fetch( (int)$object->attribute( 'section_id' ) );
        $states = array();
        if ( $canRead )
        {
            foreach ( $object->stateIdentifierArray() as $state )
                $states[] = (string)$state;
        }
        $statusNames = array( eZContentObject::STATUS_DRAFT => 'draft', eZContentObject::STATUS_PUBLISHED => 'published',
                              eZContentObject::STATUS_ARCHIVED => 'archived' );
        $status = (int)$object->attribute( 'status' );

        $description = array(
            'object_id' => (int)$object->attribute( 'id' ),
            'name' => (string)$object->attribute( 'name' ),
            'class_name' => $class ? (string)$class->attribute( 'name' ) : '',
            'class_identifier' => $class ? (string)$class->attribute( 'identifier' ) : '',
            'owner_id' => (int)$object->attribute( 'owner_id' ),
            'owner_name' => $owner instanceof eZContentObject ? (string)$owner->attribute( 'name' ) : '',
            'section_id' => (int)$object->attribute( 'section_id' ),
            'section_name' => $section ? (string)$section->attribute( 'name' ) : '',
            'languages' => array_values( array_map( 'strval', (array)$object->availableLanguages() ) ),
            'initial_language' => (string)$object->attribute( 'initial_language_code' ),
            'states' => $states,
            'remote_id' => (string)$object->attribute( 'remote_id' ),
            'published' => (int)$object->attribute( 'published' ),
            'modified' => (int)$object->attribute( 'modified' ),
            'status' => isset( $statusNames[$status] ) ? $statusNames[$status] : (string)$status,
            'current_version' => (int)$object->attribute( 'current_version' ),
            'main_node_id' => $canRead ? (int)$object->attribute( 'main_node_id' ) : 0,
            'matches' => $matches,
            'versions' => $versions,
            'locations' => $locations,
            'trash' => $trash,
            'in_trash' => (bool)$trash,
        );
        $description['actions'] = self::actions( $object, $description, $user );
        return $description;
    }

    /**
     * The match a hit gives, or null when the user may not see what it found.
     *
     * @return array|null array( 'via', 'value', 'node_id', 'trash', 'rows' (attribute and file hits), 'version' )
     */
    protected function visibleMatch( array $hit, eZContentObject $object, $canRead, array $versions, array $locations, array $trash )
    {
        $match = array( 'via' => $hit['via'], 'value' => is_scalar( $hit['value'] ) ? (string)$hit['value'] : '',
                        'node_id' => isset( $hit['node_id'] ) ? (int)$hit['node_id'] : 0, 'trash' => false, 'rows' => array(),
                        'version' => isset( $hit['version'] ) ? (int)$hit['version'] : 0,
                        'siteaccess' => isset( $hit['siteaccess'] ) ? (string)$hit['siteaccess'] : '',
                        'prefix' => isset( $hit['prefix'] ) ? (string)$hit['prefix'] : '',
                        'history' => !empty( $hit['history'] ),
                        'part' => isset( $hit['part'] ) ? (string)$hit['part'] : '' );
        switch ( $hit['via'] )
        {
            case 'object':
            case 'object_remote_id':
                return $canRead || $trash ? $match : null;
            case 'node':
            case 'node_remote_id':
            case 'urlalias':
            case 'urlalias_tail':
            case 'urlalias_part':
                foreach ( $locations as $location )
                {
                    if ( $location['node_id'] === $match['node_id'] )
                        return $match;
                }
                return null;
            case 'trash_node':
                foreach ( $trash as $entry )
                {
                    if ( $entry['node_id'] === $match['node_id'] )
                    {
                        $match['trash'] = true;
                        return $match;
                    }
                }
                return null;
            case 'version':
                foreach ( $versions as $version )
                {
                    if ( $version['version'] === $match['version'] )
                        return $match;
                }
                return null;
            case 'attribute':
            case 'file':
                $visible = array();
                foreach ( $versions as $version )
                    $visible[$version['version']] = true;
                foreach ( $hit['rows'] as $row )
                {
                    if ( !isset( $visible[$row['version']] ) )
                        continue;
                    $row['identifier'] = $this->attributeIdentifier( $row['class_attribute_id'] );
                    unset( $row['class_attribute_id'] );
                    $match['rows'][] = $row;
                }
                return $match['rows'] ? $match : null;
        }
        return null;
    }

    /**
     * @param int $classAttributeID
     * @return string
     */
    protected function attributeIdentifier( $classAttributeID )
    {
        $classAttributeID = (int)$classAttributeID;
        if ( !isset( $this->attributeIdentifiers[$classAttributeID] ) )
        {
            $attribute = eZContentClassAttribute::fetch( $classAttributeID );
            $this->attributeIdentifiers[$classAttributeID] = $attribute ? (string)$attribute->attribute( 'identifier' ) : '';
        }
        return $this->attributeIdentifiers[$classAttributeID];
    }

    /**
     * The versions the user may see: content/versionread for the version, or the published version for who may
     * read the object.
     *
     * @param eZContentObject $object
     * @param bool $canRead
     * @return array newest first
     */
    protected function versions( eZContentObject $object, $canRead )
    {
        $names = self::versionStatusNames();
        $canEdit = null;
        $list = array();
        $versions = $object->versions( true, array( 'sort' => array( 'version' => 'desc' ),
                                                    'limit' => array( 'offset' => 0, 'length' => 200 ) ) );
        foreach ( is_array( $versions ) ? $versions : array() as $version )
        {
            $status = (int)$version->attribute( 'status' );
            if ( !( $version->canVersionRead() || ( $canRead && $status === eZContentObjectVersion::STATUS_PUBLISHED ) ) )
                continue;
            if ( $canEdit === null )
                $canEdit = (bool)$object->editAccess();
            $creator = $version->attribute( 'creator' );
            $languages = array();
            foreach ( (array)$version->translationList( false, false ) as $code )
                $languages[] = (string)$code;
            $list[] = array(
                'id' => (int)$version->attribute( 'id' ),
                'version' => (int)$version->attribute( 'version' ),
                'status' => $status,
                'status_name' => isset( $names[$status] ) ? $names[$status] : (string)$status,
                'language' => (string)$version->initialLanguageCode(),
                'languages' => $languages,
                'creator_name' => $creator instanceof eZContentObject ? (string)$creator->attribute( 'name' ) : '',
                'created' => (int)$version->attribute( 'created' ),
                'modified' => (int)$version->attribute( 'modified' ),
                'can_remove' => $canEdit && self::isRemovableVersionStatus( $status ) && (bool)$version->canVersionRemove(),
            );
        }
        usort( $list, function ( $a, $b ) { return $b['version'] - $a['version']; } );
        return $list;
    }

    /**
     * @return array status => name (the names the templates translate)
     */
    public static function versionStatusNames()
    {
        return array( eZContentObjectVersion::STATUS_DRAFT => 'draft',
                      eZContentObjectVersion::STATUS_PUBLISHED => 'published',
                      eZContentObjectVersion::STATUS_PENDING => 'pending',
                      eZContentObjectVersion::STATUS_ARCHIVED => 'archived',
                      eZContentObjectVersion::STATUS_REJECTED => 'rejected',
                      eZContentObjectVersion::STATUS_INTERNAL_DRAFT => 'internal_draft',
                      eZContentObjectVersion::STATUS_REPEAT => 'repeat',
                      eZContentObjectVersion::STATUS_QUEUED => 'queued' );
    }

    /**
     * The statuses a version can be removed in: those content/history offers (draft, archived, rejected, internal
     * draft); never the published one or one a workflow still holds.
     *
     * @param int $status
     * @return bool
     */
    public static function isRemovableVersionStatus( $status )
    {
        if ( class_exists( 'Exponential\\View\\Kernel\\Content\\History' ) && method_exists( 'Exponential\\View\\Kernel\\Content\\History', 'isRemovableStatus' ) )
            return (bool)\Exponential\View\Kernel\Content\History::isRemovableStatus( $status );
        return in_array( (int)$status, array( eZContentObjectVersion::STATUS_DRAFT, eZContentObjectVersion::STATUS_ARCHIVED,
                                             eZContentObjectVersion::STATUS_REJECTED, eZContentObjectVersion::STATUS_INTERNAL_DRAFT ), true );
    }

    /**
     * The locations the user may read.
     *
     * @param eZContentObject $object
     * @return array
     */
    protected function locations( eZContentObject $object )
    {
        $list = array();
        $mainNodeID = (int)$object->attribute( 'main_node_id' );
        $nodes = $object->assignedNodes( true );
        foreach ( is_array( $nodes ) ? $nodes : array() as $node )
        {
            if ( !$node instanceof eZContentObjectTreeNode || !$node->canRead() )
                continue;
            $list[] = array(
                'node_id' => (int)$node->attribute( 'node_id' ),
                'parent_node_id' => (int)$node->attribute( 'parent_node_id' ),
                'path' => (string)$node->attribute( 'path_identification_string' ),
                'url_alias' => (string)$node->urlAlias(),
                'is_main' => (int)$node->attribute( 'node_id' ) === $mainNodeID,
                'is_hidden' => (bool)$node->attribute( 'is_hidden' ),
                'is_invisible' => (bool)$node->attribute( 'is_invisible' ),
                'can_remove' => (bool)$node->canRemove(),
            );
        }
        return $list;
    }

    /**
     * The object's trash entries the user may see: content/restore or content/cleantrash, and the trash list's own
     * permission SQL (content/read on where the node was).
     *
     * @param eZContentObject $object
     * @param eZUser|null $user
     * @return array
     */
    protected function trashEntries( eZContentObject $object, $user )
    {
        if ( !self::hasPolicy( 'content', 'restore', $user ) && !self::hasPolicy( 'content', 'cleantrash', $user ) )
            return array();
        $nodes = eZContentObjectTrashNode::trashList( array( 'ContentObjectIDList' => array( (int)$object->attribute( 'id' ) ),
                                                             'AttributeFilter' => false, 'Limit' => 10, 'Offset' => 0,
                                                             'SortBy' => array( 'trashed', '0' ) ) );
        $columns = eZContentObjectTrashNode::hasTrashedByColumns();
        $list = array();
        foreach ( is_array( $nodes ) ? $nodes : array() as $node )
        {
            $entry = array(
                'node_id' => (int)$node->attribute( 'node_id' ),
                'parent_node_id' => (int)$node->attribute( 'parent_node_id' ),
                'path' => (string)$node->attribute( 'path_identification_string' ),
                'trashed' => (int)$node->attribute( 'trashed' ),
                'trashed_by_id' => 0,
                'trashed_by_name' => '',
                'trashed_via' => '',
            );
            if ( $columns )
            {
                $entry['trashed_by_id'] = (int)$node->attribute( 'trashed_by' );
                $entry['trashed_via'] = (string)$node->attribute( 'trashed_via' );
                if ( $entry['trashed_by_id'] > 0 )
                {
                    $by = eZContentObject::fetch( $entry['trashed_by_id'] );
                    $entry['trashed_by_name'] = $by instanceof eZContentObject ? (string)$by->attribute( 'name' ) : '';
                }
            }
            $list[] = $entry;
        }
        return $list;
    }

    /**
     * Which actions the result view offers (each is checked again by expContentFinderAction before it is done):
     * every one needs contentfinder/delete as well.
     *
     * @param eZContentObject $object
     * @param array $description from describe()
     * @param eZUser|null $user
     * @return array array( 'trash', 'delete', 'purge', 'restore', 'remove_version' ) => bool
     */
    public static function actions( eZContentObject $object, array $description, $user = null )
    {
        $actions = array( 'trash' => false, 'delete' => false, 'purge' => false, 'restore' => false, 'remove_version' => false );
        if ( !self::hasPolicy( 'contentfinder', 'delete', $user ) )
            return $actions;
        $assigned = $object->attribute( 'status' ) == eZContentObject::STATUS_PUBLISHED ? count( (array)$object->assignedNodes( false ) ) : 0;
        $removable = $assigned > 0 && count( $description['locations'] ) === $assigned;
        foreach ( $description['locations'] as $location )
            $removable = $removable && $location['can_remove'];
        $actions['delete'] = $removable;
        $actions['trash'] = $removable;
        if ( $description['trash'] )
        {
            $actions['purge'] = self::hasPolicy( 'content', 'cleantrash', $user ) && self::hasPolicy( 'content', 'remove', $user );
            $actions['restore'] = self::hasPolicy( 'content', 'restore', $user );
        }
        foreach ( $description['versions'] as $version )
            $actions['remove_version'] = $actions['remove_version'] || $version['can_remove'];
        return $actions;
    }
}

?>
