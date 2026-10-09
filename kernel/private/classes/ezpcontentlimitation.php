<?php
/**
 * File containing the ezpContentLimitation class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Asks the handler an extension registered for a content policy limitation the kernel does not know
 * (site.ini [RoleSettings] LimitationHandlers[<limitation>]=<class>, see ezpContentLimitationHandler).
 *
 * Without a usable handler the limitation denies, in the PHP checks, in the SQL of fetches and in the filters of
 * searches (solrFilter(), for handlers that implement ezpContentLimitationSolrHandler) alike: a limitation
 * that nobody evaluates must never widen what a policy allows. "Usable" means the class exists, implements
 * ezpContentLimitationHandler and can be made without arguments; a handler that throws, or answers with something
 * that is not a valid condition, denies as well. Each such case is logged once per request.
 *
 * The handler of a limitation is made once per request and kept for the rest of it; a persistent worker (Velocity)
 * starts again with the next request, and a change of the setting (a test, a siteaccess switch) starts again at once.
 *
 * @package kernel
 */
class ezpContentLimitation
{
    /**
     * The SQL condition of a limitation that cannot be evaluated: it matches no row.
     */
    const DENY_SQL = '1 = 0';

    /**
     * The search filter of a limitation that cannot be evaluated: it matches no document.
     */
    const DENY_SOLR = '( *:* -*:* )';

    /**
     * The limitations the kernel evaluates itself (besides StateGroup_<identifier>). An extension cannot take one
     * of them over; where one does not apply (Language in a list fetch) it is left out, as before.
     *
     * @var string[]
     */
    public static $kernelLimitations = array( 'Class', 'ParentClass', 'ParentDepth', 'Section', 'User_Section',
                                              'Language', 'Owner', 'ParentOwner', 'Group', 'ParentGroup', 'State',
                                              'Status', 'Node', 'Subtree', 'User_Subtree', 'NewState' );

    /**
     * The handler of each limitation asked for in this request: the object, or false when there is none or it is
     * unusable.
     *
     * @var array
     */
    protected static $handlers = array();

    /**
     * The request and the setting $handlers belong to: REQUEST_TIME_FLOAT and a hash of LimitationHandlers[].
     *
     * @var string|null
     */
    protected static $cacheKey = null;

    /**
     * The messages logged in this request, so that a limitation checked for every node of a list is logged once.
     *
     * @var array
     */
    protected static $logged = array();

    /**
     * Returns whether the kernel evaluates the limitation $limitation itself.
     *
     * @param string $limitation
     * @return bool
     */
    public static function isKernelLimitation( $limitation )
    {
        return in_array( $limitation, self::$kernelLimitations, true ) || strncmp( (string)$limitation, 'StateGroup_', 11 ) === 0;
    }

    /**
     * Returns the handler registered for $limitation, or null when there is none or it is unusable.
     *
     * @param string $limitation
     * @return ezpContentLimitationHandler|null
     */
    public static function handler( $limitation )
    {
        if ( !is_string( $limitation ) || $limitation === '' || self::isKernelLimitation( $limitation ) )
        {
            return null;
        }
        $setting = self::requestState();
        if ( array_key_exists( $limitation, self::$handlers ) )
        {
            return self::$handlers[$limitation] === false ? null : self::$handlers[$limitation];
        }

        $handler = false;
        $class = isset( $setting[$limitation] ) && is_string( $setting[$limitation] ) ? trim( $setting[$limitation] ) : '';
        if ( $class === '' )
        {
            self::log( "No handler is registered for the limitation $limitation (site.ini [RoleSettings] LimitationHandlers[$limitation]); it denies", 'notice' );
        }
        else if ( !class_exists( $class ) )
        {
            self::log( "The handler class $class of the limitation $limitation does not exist (regenerate the autoloads?); the limitation denies" );
        }
        else if ( !in_array( 'ezpContentLimitationHandler', class_implements( $class ), true ) )
        {
            self::log( "The handler $class of the limitation $limitation does not implement ezpContentLimitationHandler; the limitation denies" );
        }
        else
        {
            try
            {
                $handler = new $class();
            }
            catch ( Throwable $e )
            {
                self::rethrowExit( $e );
                self::log( "The handler $class of the limitation $limitation could not be made (" . get_class( $e ) . ': ' . $e->getMessage() . '); the limitation denies' );
                $handler = false;
            }
        }
        self::$handlers[$limitation] = $handler;
        return $handler === false ? null : $handler;
    }

    /**
     * Returns whether the limitation $limitation with $values lets $userID use $functionName on $subject; false
     * when no handler evaluates it, when the handler throws, and for every answer but true.
     *
     * @param string $limitation
     * @param array $values
     * @param string $functionName
     * @param eZContentObject|eZContentObjectTreeNode|eZContentObjectVersion $subject
     * @param int $userID
     * @return bool
     */
    public static function checkAccess( $limitation, $values, $functionName, $subject, $userID )
    {
        $handler = self::handler( $limitation );
        if ( $handler === null )
        {
            return false;
        }
        try
        {
            $answer = $handler->checkAccess( $limitation, self::values( $values ), (string)$functionName, $subject, (int)$userID );
        }
        catch ( Throwable $e )
        {
            self::rethrowExit( $e );
            self::log( 'The handler ' . get_class( $handler ) . " of the limitation $limitation threw " . get_class( $e ) . ': ' . $e->getMessage() . '; the limitation denies' );
            return false;
        }
        return $answer === true;
    }

    /**
     * Returns the SQL condition of the limitation $limitation with $values for a content/read fetch of $userID, in
     * parentheses; DENY_SQL when no handler evaluates it, the handler cannot express it (false), throws, or answers
     * with something that is not a valid condition (see sqlCondition()).
     *
     * @param string $limitation
     * @param array $values
     * @param string $tableAliasName
     * @param int|bool $userID The user of the fetch; false for the current user
     * @return string
     */
    public static function permissionSQL( $limitation, $values, $tableAliasName, $userID = false )
    {
        $handler = self::handler( $limitation );
        if ( $handler === null )
        {
            return self::DENY_SQL;
        }
        if ( $userID === false )
        {
            $userID = eZUser::currentUserID();
        }
        try
        {
            $sql = $handler->permissionSQL( $limitation, self::values( $values ), (string)$tableAliasName, (int)$userID );
        }
        catch ( Throwable $e )
        {
            self::rethrowExit( $e );
            self::log( 'The handler ' . get_class( $handler ) . " of the limitation $limitation threw " . get_class( $e ) . ' in permissionSQL(): ' . $e->getMessage() . '; the policy gives no access in fetches' );
            return self::DENY_SQL;
        }
        if ( $sql === false )
        {
            return self::DENY_SQL;
        }
        $condition = self::sqlCondition( $sql );
        if ( $condition === false )
        {
            self::log( 'The handler ' . get_class( $handler ) . " of the limitation $limitation answered permissionSQL() with " .
                       ( is_string( $sql ) ? "'" . substr( $sql, 0, 200 ) . "'" : gettype( $sql ) ) .
                       ', which is not a condition the kernel accepts; the policy gives no access in fetches' );
            return self::DENY_SQL;
        }
        return $condition;
    }

    /**
     * Turns what a handler's permissionSQL() returned into a condition in parentheses, or false when it is none.
     *
     * - A string: an SQL condition. It must be self-contained: quotes and parentheses balanced, and outside quoted
     *   strings no ";" and no comment ("--", "#", "/*"). So it cannot end the parentheses it is put in, or the
     *   statement. Values in it are the handler's to cast or escape.
     * - An array with 'column' and 'values' (and optionally 'type' => 'int' (default) or 'string', 'not' => true, a
     *   boolean): "<column> IN ( ... )", the values cast to integers or escaped by the kernel. The column is a name
     *   or table.name. No values matches nothing ("NOT IN" with no values: everything). A 'not' that is no boolean
     *   ('false', 1, 0) makes it no condition, so the policy gives no access. A list of such arrays is joined by AND.
     *
     * @param mixed $sql
     * @return string|false
     */
    public static function sqlCondition( $sql )
    {
        if ( is_string( $sql ) )
        {
            // checked before trim(), which would drop a NUL byte at either end
            if ( !self::isSelfContainedSQL( $sql ) )
            {
                return false;
            }
            $sql = trim( $sql );
            return $sql !== '' ? '( ' . $sql . ' )' : false;
        }
        if ( !is_array( $sql ) || !$sql )
        {
            return false;
        }
        $list = isset( $sql['column'] ) ? array( $sql ) : $sql;
        $parts = array();
        foreach ( $list as $condition )
        {
            if ( !is_array( $condition ) || !isset( $condition['column'] ) || !is_string( $condition['column'] ) ||
                 !preg_match( '/^[A-Za-z_][A-Za-z0-9_]*(\.[A-Za-z_][A-Za-z0-9_]*)?$/', $condition['column'] ) ||
                 !isset( $condition['values'] ) || !is_array( $condition['values'] ) ||
                 !self::hasBooleanNot( $condition ) )
            {
                return false;
            }
            $type = isset( $condition['type'] ) ? $condition['type'] : 'int';
            $not = !empty( $condition['not'] );
            $literals = array();
            foreach ( $condition['values'] as $value )
            {
                if ( !is_scalar( $value ) )
                {
                    return false;
                }
                if ( $type === 'int' )
                {
                    if ( !preg_match( '/^\s*-?[0-9]{1,19}\s*$/', (string)$value ) )
                    {
                        return false;
                    }
                    $literals[] = (string)(int)$value;
                }
                else if ( $type === 'string' )
                {
                    $literals[] = "'" . eZDB::instance()->escapeString( (string)$value ) . "'";
                }
                else
                {
                    return false;
                }
            }
            if ( !$literals )
            {
                $parts[] = $not ? '1 = 1' : self::DENY_SQL;
                continue;
            }
            $parts[] = eZDB::instance()->generateSQLINStatement( array_values( array_unique( $literals ) ), $condition['column'], $not );
        }
        return '( ' . implode( ' AND ', $parts ) . ' )';
    }

    /**
     * Whether the 'not' of a column or field condition is usable: left out (or null) for "only these", or a real
     * boolean. Any other value ('false', 'true', 1, 0, an array) is refused, so that it can neither turn "only
     * these" into "all but these" nor the other way round; the condition is then none and the policy gives no access.
     *
     * @param array $condition
     * @return bool
     */
    protected static function hasBooleanNot( array $condition )
    {
        return !isset( $condition['not'] ) || is_bool( $condition['not'] );
    }

    /**
     * Whether $sql can stand inside parentheses on its own: quotes ('...', "...", `...`) closed, parentheses
     * balanced and never closed before they open, and outside quoted strings no statement end (;) and no comment
     * (--, #, /*, * /). A backslash escapes the next character inside a quoted string.
     *
     * @param string $sql
     * @return bool
     */
    public static function isSelfContainedSQL( $sql )
    {
        if ( strpos( $sql, "\0" ) !== false )
        {
            return false;
        }
        $depth = 0;
        $quote = null;
        $length = strlen( $sql );
        for ( $i = 0; $i < $length; ++$i )
        {
            $char = $sql[$i];
            if ( $quote !== null )
            {
                if ( $char === '\\' )
                {
                    ++$i;
                }
                else if ( $char === $quote )
                {
                    // a doubled quote is an escaped one
                    if ( $i + 1 < $length && $sql[$i + 1] === $quote )
                        ++$i;
                    else
                        $quote = null;
                }
                continue;
            }
            $next = $i + 1 < $length ? $sql[$i + 1] : '';
            if ( $char === "'" || $char === '"' || $char === '`' )
            {
                $quote = $char;
            }
            else if ( $char === '(' )
            {
                ++$depth;
            }
            else if ( $char === ')' )
            {
                if ( --$depth < 0 )
                    return false;
            }
            else if ( $char === ';' || $char === '#' || ( $char === '-' && $next === '-' ) ||
                      ( $char === '/' && $next === '*' ) || ( $char === '*' && $next === '/' ) )
            {
                return false;
            }
        }
        return $quote === null && $depth === 0;
    }

    /**
     * Returns the search filter of the extension limitation $limitation with $values, in parentheses, for a search
     * engine that filters by the policies of the user itself (eZ Find). The search engine joins it with AND to the
     * other limitations of the policy.
     *
     * DENY_SOLR, a filter that matches no document, comes back when the policy gives no access in searches: for a
     * limitation the kernel evaluates itself (the search engine has to translate those), when no handler is
     * registered, when the handler does not implement ezpContentLimitationSolrHandler, when it returns false or
     * throws, and when its answer is no filter the kernel accepts (see solrCondition()). It is a filter and not
     * false on purpose, as permissionSQL() gives DENY_SQL: a search engine that leaves a policy out of its filter
     * when it has no policy left filters by nothing at all (eZ Find does), so a limitation that nobody evaluates
     * would widen what the user can find instead of narrowing it.
     *
     * @param string $limitation
     * @param array $values
     * @param int|bool $userID The user of the search; false for the current user
     * @return string
     */
    public static function solrFilter( $limitation, $values, $userID = false )
    {
        if ( is_string( $limitation ) && self::isKernelLimitation( $limitation ) )
        {
            // forgets the messages of an earlier request first (Velocity), as handler() does
            self::requestState();
            self::log( "The search engine asked for the filter of the limitation $limitation, which the kernel evaluates itself and the search engine has to translate; the policy gives no access in searches" );
            return self::DENY_SOLR;
        }
        $handler = self::handler( $limitation );
        if ( $handler === null )
        {
            return self::DENY_SOLR;
        }
        if ( !$handler instanceof ezpContentLimitationSolrHandler )
        {
            self::log( 'The handler ' . get_class( $handler ) . " of the limitation $limitation does not implement ezpContentLimitationSolrHandler; the policy gives no access in searches", 'warning' );
            return self::DENY_SOLR;
        }
        if ( $userID === false )
        {
            $userID = eZUser::currentUserID();
        }
        try
        {
            $filter = $handler->solrFilter( $limitation, self::values( $values ), (int)$userID );
        }
        catch ( Throwable $e )
        {
            self::rethrowExit( $e );
            self::log( 'The handler ' . get_class( $handler ) . " of the limitation $limitation threw " . get_class( $e ) . ' in solrFilter(): ' . $e->getMessage() . '; the policy gives no access in searches' );
            return self::DENY_SOLR;
        }
        if ( $filter === false )
        {
            return self::DENY_SOLR;
        }
        $condition = self::solrCondition( $filter );
        if ( $condition === false )
        {
            self::log( 'The handler ' . get_class( $handler ) . " of the limitation $limitation answered solrFilter() with " .
                       ( is_string( $filter ) ? "'" . substr( $filter, 0, 200 ) . "'" : gettype( $filter ) ) .
                       ', which is not a filter the kernel accepts; the policy gives no access in searches' );
            return self::DENY_SOLR;
        }
        return $condition;
    }

    /**
     * Turns what a handler's solrFilter() returned into a filter in parentheses, or false when it is none.
     *
     * - A string: a Solr query. It must be self-contained (see isSelfContainedSolr()): quotes closed, parentheses
     *   balanced and never closed before they open, range brackets closed, no local parameters ("{!", which change
     *   the parser) and no nested query (_query_), no NUL byte. So it cannot end the parentheses it is put in.
     *   Values in it are the handler's to escape (solrValue()).
     * - An array with 'field' and 'values' (and optionally 'not' => true, a boolean): "field:(v1 OR v2)", the values
     *   escaped by the kernel. The field is a name of letters, digits and "_", the values are non-empty scalars in
     *   UTF-8. No values matches nothing ("not" with no values: everything). A list of such arrays is joined by AND.
     *
     * @param mixed $filter
     * @return string|false
     */
    public static function solrCondition( $filter )
    {
        if ( is_string( $filter ) )
        {
            if ( !self::isSelfContainedSolr( $filter ) )
            {
                return false;
            }
            $filter = trim( $filter );
            return $filter !== '' ? '( ' . $filter . ' )' : false;
        }
        if ( !is_array( $filter ) || !$filter )
        {
            return false;
        }
        $list = isset( $filter['field'] ) ? array( $filter ) : $filter;
        $parts = array();
        foreach ( $list as $condition )
        {
            if ( !is_array( $condition ) || !isset( $condition['field'] ) || !is_string( $condition['field'] ) ||
                 !preg_match( '/^[A-Za-z_][A-Za-z0-9_]*$/', $condition['field'] ) ||
                 !isset( $condition['values'] ) || !is_array( $condition['values'] ) ||
                 !self::hasBooleanNot( $condition ) )
            {
                return false;
            }
            $not = !empty( $condition['not'] );
            $literals = array();
            foreach ( $condition['values'] as $value )
            {
                if ( !is_scalar( $value ) || (string)$value === '' || !preg_match( '//u', (string)$value ) )
                {
                    return false;
                }
                $literals[] = self::solrValue( (string)$value );
            }
            $literals = array_values( array_unique( $literals ) );
            if ( !$literals )
            {
                $parts[] = $not ? '*:*' : self::DENY_SOLR;
                continue;
            }
            $in = $condition['field'] . ':(' . implode( ' OR ', $literals ) . ')';
            $parts[] = $not ? '( *:* -' . $in . ' )' : $in;
        }
        return '( ' . implode( ' AND ', $parts ) . ' )';
    }

    /**
     * Escapes $value for a Solr query, so that it stands for itself: every character the standard query parser
     * gives a meaning (+ - && || ! ( ) { } [ ] ^ " ~ * ? : \ / and whitespace) gets a backslash, and a value that
     * is one of the operators AND, OR and NOT gets one before its first letter, so that it is a term and no operator.
     * A value that is not valid UTF-8 is escaped byte by byte (field conditions refuse such values altogether).
     *
     * @param string $value
     * @return string
     */
    public static function solrValue( $value )
    {
        $value = (string)$value;
        $escaped = preg_replace( '/([+\-&|!(){}\[\]^"~*?:\\\\\/\s])/u', '\\\\$1', $value );
        if ( $escaped === null )
        {
            $escaped = preg_replace( '/([+\-&|!(){}\[\]^"~*?:\\\\\/\s])/', '\\\\$1', $value );
        }
        if ( $escaped === 'AND' || $escaped === 'OR' || $escaped === 'NOT' )
        {
            $escaped = '\\' . $escaped;
        }
        return $escaped;
    }

    /**
     * Whether $filter can stand inside parentheses on its own in a Solr query: double quotes closed (a backslash
     * escapes the next character), parentheses outside quotes balanced and never closed before they open, range
     * brackets ([ ] and { }) closed before any other bracket and never nested, no NUL byte, and no local parameters
     * ("{!") or nested query (_query_) anywhere, not even in quotes, where a nested query would read them.
     *
     * @param string $filter
     * @return bool
     */
    public static function isSelfContainedSolr( $filter )
    {
        if ( strpos( $filter, "\0" ) !== false || strpos( $filter, '{!' ) !== false || stripos( $filter, '_query_' ) !== false )
        {
            return false;
        }
        $depth = 0;
        $quoted = false;
        $range = false;
        $length = strlen( $filter );
        for ( $i = 0; $i < $length; ++$i )
        {
            $char = $filter[$i];
            if ( $char === '\\' )
            {
                ++$i;
                continue;
            }
            if ( $quoted )
            {
                if ( $char === '"' )
                {
                    $quoted = false;
                }
                continue;
            }
            if ( $char === '"' )
            {
                $quoted = true;
            }
            else if ( $range )
            {
                // a range end may be any character, also a parenthesis: only its own closing bracket is allowed
                if ( $char === ']' || $char === '}' )
                {
                    $range = false;
                }
                else if ( $char === '(' || $char === ')' || $char === '[' || $char === '{' )
                {
                    return false;
                }
            }
            else if ( $char === '[' || $char === '{' )
            {
                $range = true;
            }
            else if ( $char === ']' || $char === '}' )
            {
                return false;
            }
            else if ( $char === '(' )
            {
                ++$depth;
            }
            else if ( $char === ')' )
            {
                if ( --$depth < 0 )
                {
                    return false;
                }
            }
        }
        return !$quoted && !$range && $depth === 0 && $i === $length;
    }

    /**
     * Forgets the handlers and the messages of the request; for tests, and done by itself when the request or the
     * setting changes.
     */
    public static function resetCache()
    {
        self::$handlers = array();
        self::$logged = array();
        self::$cacheKey = null;
    }

    /**
     * Returns site.ini [RoleSettings] LimitationHandlers[], after forgetting what belongs to another request or to
     * another value of the setting.
     *
     * @return array
     */
    protected static function requestState()
    {
        $ini = eZINI::instance( 'site.ini' );
        $setting = $ini->hasVariable( 'RoleSettings', 'LimitationHandlers' ) ? $ini->variable( 'RoleSettings', 'LimitationHandlers' ) : array();
        if ( !is_array( $setting ) )
        {
            $setting = array();
        }
        $key = ( isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : '' ) . '|' . md5( serialize( $setting ) );
        if ( $key !== self::$cacheKey )
        {
            self::$handlers = array();
            self::$logged = array();
            self::$cacheKey = $key;
        }
        return $setting;
    }

    /**
     * The values of a limitation as the handler gets them: a list of strings.
     *
     * @param mixed $values
     * @return array
     */
    protected static function values( $values )
    {
        $list = array();
        foreach ( (array)$values as $value )
        {
            if ( is_scalar( $value ) )
            {
                $list[] = (string)$value;
            }
        }
        return $list;
    }

    /**
     * Logs $message once in this request.
     *
     * @param string $message
     * @param string $level 'error', 'warning' or 'notice'
     */
    protected static function log( $message, $level = 'error' )
    {
        if ( isset( self::$logged[$message] ) )
        {
            return;
        }
        self::$logged[$message] = true;
        if ( $level === 'notice' )
        {
            eZDebug::writeNotice( $message, __CLASS__ );
        }
        else if ( $level === 'warning' )
        {
            eZDebug::writeWarning( $message, __CLASS__ );
        }
        else
        {
            eZDebug::writeError( $message, __CLASS__ );
        }
    }

    /**
     * Lets the exception a persistent worker (Velocity) ends a request with through: it is no failure of the handler.
     *
     * @param Throwable $e
     */
    protected static function rethrowExit( $e )
    {
        if ( is_a( $e, 'Q_WebServer_ExitSignal' ) )
        {
            throw $e;
        }
    }
}
