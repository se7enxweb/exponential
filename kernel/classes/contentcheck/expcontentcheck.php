<?php
/**
 * File containing the expContentCheck class.
 *
 * What the two content checks share (doc/features/6.0/content-checks.md): their settings, where they answer, the
 * policy check, the form tokens of the trash action, the reading of node and class id lists, and the one rule both
 * build on: what counts as a reference to an object.
 *
 * A reference is a row of ezcontentobject_link (every relation type: common, XML embed, XML link, attribute) whose
 * source object is published or a draft object (not in the trash), from a version that is published, a draft, an
 * internal draft, pending or queued. Old (archived), rejected and repeat versions do not count, and neither does an
 * object that refers to itself.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentCheck
{
    /** The settings file */
    const INI_FILE = 'contentcheck.ini';

    /** The object statuses of a source whose relations count: draft (never published) and published, not archived (the trash) */
    const REFERENCE_OBJECT_STATUSES = array( 0, 1 );

    /** The version statuses whose relations count: draft, published, pending, internal draft, queued */
    const REFERENCE_VERSION_STATUSES = array( 0, 1, 2, 5, 7 );

    /** The posted field of the session token */
    const TOKEN_FIELD = 'ContentCheckToken';

    /** The posted field of the confirmation token */
    const CONFIRM_FIELD = 'ContentCheckConfirmToken';

    /** The session variable that holds the secret of the tokens */
    const SESSION_SECRET = 'ContentCheckSecret';

    /** At most this many ids are read from one list of ids */
    const MAX_IDS = 200;

    /** @var string|null A fixed secret for tests */
    protected static $testSecret = null;

    // ------------------------------------------------------------------ settings and access

    /**
     * A setting of contentcheck.ini.
     *
     * @param string $block
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function setting( $block, $name, $default )
    {
        $ini = eZINI::instance( self::INI_FILE );
        return $ini->hasVariable( $block, $name ) ? $ini->variable( $block, $name ) : $default;
    }

    /**
     * A whole number setting, bounded.
     *
     * @param string $block
     * @param string $name
     * @param int $default
     * @param int $min
     * @param int $max
     * @return int
     */
    public static function intSetting( $block, $name, $default, $min, $max )
    {
        $value = self::setting( $block, $name, $default );
        $value = is_numeric( $value ) ? (int)$value : (int)$default;
        return max( (int)$min, min( (int)$max, $value ) );
    }

    /**
     * Whether the checks answer on the current siteaccess: enabled, and the siteaccess is listed in SiteAccessList[]
     * or, with an empty list, has an administration design.
     *
     * @return bool
     */
    public static function isAvailableHere()
    {
        if ( self::setting( 'ContentCheckSettings', 'Enabled', 'true' ) !== 'true' )
            return false;
        $current = eZSiteAccess::current();
        $name = is_array( $current ) && isset( $current['name'] ) ? (string)$current['name'] : '';
        $list = self::setting( 'ContentCheckSettings', 'SiteAccessList', array() );
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
     * Whether the database can run the checks' queries: every SQL engine can; the MongoDB driver cannot translate
     * their NOT EXISTS subqueries.
     *
     * @param string|null $databaseName eZDB's databaseName(); null asks the current connection
     * @return bool
     */
    public static function isSupportedDatabase( $databaseName = null )
    {
        if ( $databaseName === null )
            $databaseName = eZDB::instance()->databaseName();
        return in_array( (string)$databaseName, array( 'mysql', 'postgresql', 'sqlite', 'oracle' ), true );
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

    // ------------------------------------------------------------------ input

    /**
     * A list of positive whole numbers from an array or a text with commas, spaces or semicolons between them:
     * unique, in the order given, at most $max. Anything that is not a number is left out.
     *
     * @param mixed $value
     * @param int $max
     * @return int[]
     */
    public static function idList( $value, $max = self::MAX_IDS )
    {
        if ( is_scalar( $value ) )
            $value = preg_split( '/[\s,;]+/', (string)$value, -1, PREG_SPLIT_NO_EMPTY );
        if ( !is_array( $value ) )
            return array();
        $ids = array();
        foreach ( $value as $item )
        {
            if ( !is_scalar( $item ) || !preg_match( '/^\s*[0-9]{1,10}\s*$/', (string)$item ) )
                continue;
            $id = (int)$item;
            if ( $id > 0 && $id <= 2147483647 && !in_array( $id, $ids, true ) )
                $ids[] = $id;
            if ( count( $ids ) >= $max )
                break;
        }
        return $ids;
    }

    /**
     * One positive whole number, or the default.
     *
     * @param mixed $value
     * @param int $default
     * @return int
     */
    public static function id( $value, $default )
    {
        $ids = self::idList( is_array( $value ) ? null : $value, 1 );
        return $ids ? $ids[0] : (int)$default;
    }

    /**
     * The node a check starts from, when the current user may read it.
     *
     * @param int $nodeID
     * @return eZContentObjectTreeNode|null
     */
    public static function readableNode( $nodeID )
    {
        $node = (int)$nodeID > 0 ? eZContentObjectTreeNode::fetch( (int)$nodeID ) : null;
        if ( !$node instanceof eZContentObjectTreeNode )
            return null;
        // the root node (1) has no object; it is the whole tree, readable with an unlimited content/read
        if ( (int)$node->attribute( 'node_id' ) === 1 )
        {
            $user = eZUser::currentUser();
            $access = $user instanceof eZUser ? $user->hasAccessTo( 'content', 'read' ) : array( 'accessWord' => 'no' );
            return isset( $access['accessWord'] ) && $access['accessWord'] === 'yes' ? $node : null;
        }
        return $node->canRead() ? $node : null;
    }

    /**
     * The path_string of a node, as LIKE prefix of the nodes below it: only digits and slashes, so nothing to escape.
     *
     * @param string $pathString e.g. /1/2/58/
     * @return string|null e.g. /1/2/58/% ; null for a path that is not one
     */
    public static function subtreePattern( $pathString )
    {
        $pathString = (string)$pathString;
        if ( !preg_match( '#^/([0-9]+/)+$#', $pathString ) )
            return null;
        return $pathString . '%';
    }

    /**
     * The content/read condition of the current user for any node, for the queries that read what a page shows
     * besides its rows (locations, sub-items, the targets of relations). 'limited' is false for a user who may read
     * everything: the queries then need no condition at all. The caller drops 'temp_tables'.
     *
     * @return array array( 'limited' => bool, 'from' => string, 'where' => string, 'temp_tables' => array )
     */
    public static function readPermission()
    {
        $user = eZUser::currentUser();
        $access = $user instanceof eZUser ? $user->hasAccessTo( 'content', 'read' ) : array( 'accessWord' => 'no' );
        if ( isset( $access['accessWord'] ) && $access['accessWord'] === 'yes' )
            return array( 'limited' => false, 'from' => '', 'where' => '', 'temp_tables' => array() );
        $limitation = false;
        $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( eZContentObjectTreeNode::getLimitationList( $limitation ) );
        return array( 'limited' => true, 'from' => (string)$sql['from'], 'where' => (string)$sql['where'],
                      'temp_tables' => isset( $sql['temp_tables'] ) ? (array)$sql['temp_tables'] : array() );
    }

    /**
     * Drops the temporary tables of a permission condition.
     *
     * @param array $permission
     */
    public static function dropPermission( array $permission )
    {
        if ( !empty( $permission['temp_tables'] ) )
            eZDB::instance()->dropTempTableList( $permission['temp_tables'] );
    }

    // ------------------------------------------------------------------ what counts as a reference

    /**
     * The condition "some other object refers to the object in $objectColumn" (see the class comment), as an
     * EXISTS subquery. Wrap it in NOT for "nothing refers to it".
     *
     * @param string $objectColumn the column with the object id, e.g. ezcontentobject.id
     * @return string
     */
    public static function referenceExistsSQL( $objectColumn )
    {
        $objectColumn = preg_replace( '/[^A-Za-z0-9_.]/', '', (string)$objectColumn );
        return 'EXISTS ( SELECT 1 FROM ezcontentobject_link cc_link, ezcontentobject cc_src, ezcontentobject_version cc_ver' .
               ' WHERE cc_link.to_contentobject_id = ' . $objectColumn .
               ' AND cc_link.from_contentobject_id <> ' . $objectColumn .
               ' AND cc_src.id = cc_link.from_contentobject_id' .
               ' AND cc_src.status IN ( ' . implode( ', ', self::REFERENCE_OBJECT_STATUSES ) . ' )' .
               ' AND cc_ver.contentobject_id = cc_link.from_contentobject_id' .
               ' AND cc_ver.version = cc_link.from_contentobject_version' .
               ' AND cc_ver.status IN ( ' . implode( ', ', self::REFERENCE_VERSION_STATUSES ) . ' ) )';
    }

    /**
     * An IN list of whole numbers for SQL.
     *
     * @param int[] $ids not empty
     * @return string
     */
    public static function inList( array $ids )
    {
        $ids = array_map( 'intval', $ids );
        return $ids ? implode( ', ', $ids ) : '0';
    }

    // ------------------------------------------------------------------ tokens

    /**
     * @param string|null $secret a fixed secret (tests); null goes back to the session's
     */
    public static function setTestSecret( $secret )
    {
        self::$testSecret = $secret === null ? null : (string)$secret;
    }

    /**
     * The secret of this session's tokens: random, made on first use and kept in the session.
     *
     * @return string
     */
    protected static function secret()
    {
        if ( self::$testSecret !== null )
            return self::$testSecret;
        $http = eZHTTPTool::instance();
        $secret = $http->hasSessionVariable( self::SESSION_SECRET ) ? $http->sessionVariable( self::SESSION_SECRET ) : null;
        if ( !is_string( $secret ) || strlen( $secret ) !== 64 )
        {
            if ( !eZSession::hasStarted() )
                eZSession::start();
            $secret = bin2hex( random_bytes( 32 ) );
            $http->setSessionVariable( self::SESSION_SECRET, $secret );
        }
        return $secret;
    }

    /**
     * @param string $purpose
     * @return string
     */
    protected static function token( $purpose )
    {
        $secret = self::secret();
        if ( $secret === '' )
            return '';
        $user = eZUser::currentUser();
        $userID = $user instanceof eZUser ? (int)$user->attribute( 'contentobject_id' ) : 0;
        return hash_hmac( 'sha256', 'contentcheck|' . $userID . '|' . $purpose, $secret );
    }

    /**
     * The token every form of the checks that posts carries.
     *
     * @return string
     */
    public static function formToken()
    {
        return self::token( 'form' );
    }

    /**
     * The confirmation token of a trash plan, bound to the objects it moves.
     *
     * @param string $fingerprint
     * @return string
     */
    public static function confirmToken( $fingerprint )
    {
        return self::token( 'confirm|' . (string)$fingerprint );
    }

    /**
     * @param mixed $posted
     * @param string $expected
     * @return bool
     */
    public static function matches( $posted, $expected )
    {
        return is_string( $posted ) && $expected !== '' && strlen( $posted ) === strlen( $expected ) && hash_equals( $expected, $posted );
    }

    /**
     * The form token of ezformtoken, checked here as well: true when the extension is not there or not protecting
     * this request (no session or anonymous user, where the view is refused anyway).
     *
     * @param array $post
     * @return bool
     */
    public static function checkEzFormToken( array $post )
    {
        if ( !class_exists( 'ezxFormToken' ) || !ezxFormToken::isEnabled() )
            return true;
        if ( !eZSession::hasStarted() || !eZUser::isCurrentUserRegistered() )
            return true;
        $field = defined( 'ezxFormToken::FORM_FIELD' ) ? ezxFormToken::FORM_FIELD : 'ezxform_token';
        return isset( $post[$field] ) && self::matches( $post[$field], (string)ezxFormToken::getToken() );
    }
}

?>
