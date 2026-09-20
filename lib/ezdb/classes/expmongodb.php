<?php
/**
 * File containing the expMongoDB class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package sevenx_mongodb
 */

// NOTE: This is the ACTIVE file loaded by var/autoload/ezp_override.php
// Do NOT edit sevenx_mongodb/classes/expmongodb.php (root copy - never loaded)

 use Exception as Exception;
 use MongoDB\Client;

class expMongoDB extends eZDBInterface
{
    var $databaseName = "";

    /** @var string Last error message (cleared on each successful operation) */
    public $ErrorMessage = '';

    /** @var int Last error number (0 = no error) */
    public $ErrorNumber = 0;

    /** @var bool Whether to record errors into ErrorMessage/ErrorNumber */
    public $RecordError = true;

    /** @var bool Persistent connections flag (no-op for MongoDB) */
    public $UsePersistentConnection = false;

    /**
     * Write a statement to a trace file before it runs.
     *
     * A statement that never returns cannot be found in a log written after
     * the fact, and MongoDB's own profiler needs rights this user does not
     * have. Set EXP_MONGO_TRACE to a path to turn this on; it is off and free
     * otherwise.
     */
    function traceStatement( $what )
    {
        self::profileStatement( $what );

        static $path = null;
        if ( $path === null )
            $path = (string) getenv( 'EXP_MONGO_TRACE' );
        if ( $path === '' )
            return;

        file_put_contents( $path,
            date( 'H:i:s' ) . ' ' . preg_replace( '/\s+/', ' ', substr( $what, 0, 400 ) ) . "\n",
            FILE_APPEND );
    }

    /** Per-request statement tally, written at shutdown. See profileStatement(). */
    static $ProfileCounts = array();
    static $ProfileTotal = 0;
    static $ProfileStarted = 0.0;

    /**
     * Count statements for the current request.
     *
     * An env var cannot reach php-fpm, and a page that issues thousands of
     * small queries looks exactly like a page that issues ten slow ones from
     * the outside. Creating var/tmp/mongo_profile.on turns this on for web
     * requests as well as CLI; deleting it turns it off. Off, this is one
     * file_exists() per request.
     */
    static function profileStatement( $what )
    {
        static $on = null;
        if ( $on === null )
        {
            // Absolute: php-fpm does not run from the project root, so a
            // relative path silently never matches.
            $on = file_exists( self::profilePath( 'mongo_profile.on' ) );
            if ( $on )
            {
                self::$ProfileStarted = microtime( true );
                register_shutdown_function( array( 'expMongoDB', 'writeProfile' ) );
            }
        }
        if ( !$on )
            return;

        // Group by verb and table, so the tally names the caller's shape
        // rather than listing every value it was called with.
        $key = 'other';
        if ( preg_match( '/^(\w+)[ :]+\s*(SELECT|INSERT|UPDATE|DELETE)?\s*(?:INTO|FROM)?\s*([\w.]+)?/i',
            $what, $m ) )
        {
            $key = strtolower( $m[1] ) . ' ' . strtolower( $m[2] ?? '' ) . ' ' . strtolower( $m[3] ?? '' );
        }
        $key = trim( preg_replace( '/\s+/', ' ', $key ) );

        if ( !isset( self::$ProfileCounts[$key] ) )
            self::$ProfileCounts[$key] = 0;
        self::$ProfileCounts[$key]++;
        self::$ProfileTotal++;
    }

    /** An absolute path under the installation's var/tmp. */
    static function profilePath( $name )
    {
        return dirname( __DIR__, 3 ) . '/var/tmp/' . $name;
    }

    static function writeProfile()
    {
        if ( !self::$ProfileTotal )
            return;

        arsort( self::$ProfileCounts );
        $top = array();
        foreach ( array_slice( self::$ProfileCounts, 0, 8, true ) as $key => $count )
            $top[] = $count . 'x ' . $key;

        file_put_contents( self::profilePath( 'mongo_profile.log' ),
            sprintf( "%s  %-52s %5d statements in %6.0f ms  |  %s\n",
                date( 'H:i:s' ),
                substr( (string) ( $_SERVER['REQUEST_URI'] ?? 'cli' ), 0, 52 ),
                self::$ProfileTotal,
                ( microtime( true ) - self::$ProfileStarted ) * 1000,
                implode( ', ', $top ) ),
            FILE_APPEND );
    }

    function logError( $message )
    {
        eZDebug::writeError( $message, 'expMongoDB' );
    }

    public function __construct( $parameters = array() )
    {
        parent::__construct( $parameters );
        try {
            // Test connection by pinging the server
            $this->getClient()->selectDatabase( $this->DB )->command(['ping' => 1]);
            $this->IsConnected = true;
        } catch ( Exception $e ) {
            $this->logError('expMongoDB::__construct connection failed: ' . $e->getMessage());
            $this->IsConnected = false;
        }
    }

    /** Auto_increment column per collection, worked out once and kept. */
    public $AutoIncrementFields = array();

    /** The last generated value, keyed "table.column", for lastSerialID(). */
    public $LastSerialIDs = array();

    function databaseName()
    {
        return 'mongo';
    }

    public function getClient()
    {
        static $client = null;
        if ( $client === null ) {
            $server = $this->Server ?: 'localhost';
            $port   = $this->Port   ?: 27017;
            $user   = rawurlencode( $this->User );
            $pass   = rawurlencode( $this->Password );
            $dbName = $this->DB;
            $uri = 'mongodb://' . $user . ':' . $pass . '@' . $server . ':' . $port . '/' . $dbName;
            $client = new MongoDB\Client($uri);
        }
        return $client;
    }

    function findOne( $table, $condition, $server = false )
    {
        $dbName = $this->DB;
        $results = false;
        try {
            $filter = $this->translateConditions( is_array($condition) ? $condition : [] );
            $doc = $this->getClient()->selectCollection( $dbName, $table )->findOne($filter);
            $results = $doc === null ? [] : $doc->getArrayCopy();
        } catch (Exception $e) {
            $this->logError('expMongoDB::findOne ' . $e->getMessage());
        }
        return $results;
    }

    /**
     * Parse a SQL WHERE clause (ANDs only, no ORs) into a MongoDB filter array.
     * Handles: field=N, field='str', field!=N, field<>N, field LIKE 'pfx%', field IN (...)
     */
    /**
     * Translate a SQL WHERE clause into a MongoDB filter.
     *
     * The previous implementation split on AND with a regular expression and
     * quietly dropped every term it did not recognise, so a query scoped to
     * one layout or one status came back with the whole collection. That is
     * what made pages render other pages' blocks. It also had no notion of
     * OR, of the ordering comparisons, or of quotes containing the word AND.
     *
     * This is a tokeniser and a recursive descent parser over the grammar the
     * kernel actually emits: AND, OR, brackets, = != <> < <= > >=, IN, NOT IN,
     * LIKE, NOT LIKE, IS NULL and IS NOT NULL. Anything outside it is
     * reported and the clause is refused, because guessing produces silently
     * wrong pages rather than an error anyone can see.
     *
     * Returns an array filter, or false when the clause cannot be translated.
     */
    private function parseWhereClause( $whereSql )
    {
        // MySQL's row-locking suffixes. lock() and unlock() on this driver are
        // already no-ops because MongoDB has no equivalent, so the suffix is
        // dropped rather than refused: refusing it turned away every
        // "SELECT ... FOR UPDATE" the content engine issues, two or three per
        // object published.
        $whereSql = preg_replace( '/\s+(FOR\s+UPDATE|LOCK\s+IN\s+SHARE\s+MODE)\s*$/i',
            '', (string)$whereSql );

        $whereSql = $this->expandInSubqueries( $whereSql );
        if ( $whereSql === false )
            return false;

        $tokens = self::tokeniseSql( (string)$whereSql );
        if ( $tokens === false )
        {
            $this->logError( 'expMongoDB::parseWhereClause could not tokenise: ' . substr( $whereSql, 0, 200 ) );
            return false;
        }
        if ( !$tokens )
            return array();

        $position = 0;
        $filter = $this->parseWhereOr( $tokens, $position );

        if ( $filter === false || $position !== count( $tokens ) )
        {
            $this->logError( 'expMongoDB::parseWhereClause could not translate: ' . substr( $whereSql, 0, 200 ) );
            return false;
        }

        return $filter;
    }

    /**
     * Replace "col IN ( SELECT x FROM t WHERE ... )" with the literal list the
     * subquery returns.
     *
     * MongoDB has no correlated subqueries and the grammar below has no way to
     * express one, so the inner statement is run first and its column folded
     * into an IN list - which is what the SQL engines do for an uncorrelated
     * subquery anyway. Returns false when the inner statement cannot be run,
     * so the outer one is refused rather than applied to every document.
     */
    protected function expandInSubqueries( $whereSql )
    {
        if ( stripos( $whereSql, 'SELECT' ) === false )
            return $whereSql;

        $pattern = '/\b(NOT\s+IN|IN)\s*\(\s*(SELECT\s+[^()]+?)\s*\)/is';

        // Bounded: each pass consumes one subquery, and there are never many.
        for ( $pass = 0; $pass < 8; $pass++ )
        {
            if ( !preg_match( $pattern, $whereSql, $m, PREG_OFFSET_CAPTURE ) )
                return $whereSql;

            $keyword = $m[1][0];
            $inner = $m[2][0];

            $rows = $this->arrayQuery( $inner );
            if ( $rows === false || !is_array( $rows ) )
            {
                $this->logError( 'expMongoDB::parseWhereClause could not run the subquery '
                    . substr( $inner, 0, 200 ) );
                return false;
            }

            $values = array();
            foreach ( $rows as $row )
            {
                $row = (array)$row;
                $value = reset( $row );
                if ( $value === false && !$row )
                    continue;
                $values[] = is_string( $value ) ? "'" . str_replace( "'", "''", $value ) . "'" : (int)$value;
            }
            $values = array_values( array_unique( $values, SORT_REGULAR ) );

            // An empty list matches nothing, which is what SQL does too, and
            // "IN ()" is not something the tokeniser accepts.
            $replacement = $values
                ? $keyword . ' ( ' . implode( ', ', $values ) . ' )'
                : ( stripos( $keyword, 'NOT' ) === 0 ? 'IS NOT NULL' : 'IS NULL AND 1 = 0' );

            $whereSql = substr( $whereSql, 0, $m[0][1] ) . $replacement
                . substr( $whereSql, $m[0][1] + strlen( $m[0][0] ) );
        }

        return $whereSql;
    }

    /**
     * Break a clause into tokens: strings, numbers, identifiers, operators and
     * brackets. Quoted strings are kept whole, so a value containing AND or a
     * bracket cannot be mistaken for syntax.
     */
    static function tokeniseSql( $sql )
    {
        $tokens = array();
        $length = strlen( $sql );
        $i = 0;

        while ( $i < $length )
        {
            $char = $sql[$i];

            if ( ctype_space( $char ) )
            {
                $i++;
                continue;
            }

            // A quoted string, with '' and \' both meaning a literal quote.
            if ( $char === "'" )
            {
                $value = '';
                $i++;
                while ( $i < $length )
                {
                    if ( $sql[$i] === '\\' && $i + 1 < $length )
                    {
                        $value .= $sql[$i + 1];
                        $i += 2;
                        continue;
                    }
                    if ( $sql[$i] === "'" )
                    {
                        if ( $i + 1 < $length && $sql[$i + 1] === "'" )
                        {
                            $value .= "'";
                            $i += 2;
                            continue;
                        }
                        $i++;
                        break;
                    }
                    $value .= $sql[$i++];
                }
                $tokens[] = array( 'type' => 'string', 'value' => $value );
                continue;
            }

            // Two-character operators first, so <= does not read as < then =.
            $two = substr( $sql, $i, 2 );
            if ( in_array( $two, array( '<=', '>=', '!=', '<>' ), true ) )
            {
                $tokens[] = array( 'type' => 'op', 'value' => $two === '<>' ? '!=' : $two );
                $i += 2;
                continue;
            }

            if ( strpos( '=<>', $char ) !== false )
            {
                $tokens[] = array( 'type' => 'op', 'value' => $char );
                $i++;
                continue;
            }

            // The bitwise operators. The kernel writes these into WHERE for the
            // language masks - "language_mask & 1 = 1", "( lang_mask & 2 ) > 0"
            // - and refusing them left the url alias rows unfiltered. General
            // arithmetic is deliberately still out of the grammar, so anything
            // beyond this is refused rather than approximated.
            if ( strpos( '&|^~', $char ) !== false )
            {
                $tokens[] = array( 'type' => 'op', 'value' => $char );
                $i++;
                continue;
            }

            if ( $char === '(' || $char === ')' || $char === ',' )
            {
                $tokens[] = array( 'type' => $char, 'value' => $char );
                $i++;
                continue;
            }

            // A number, including a negative one and a decimal.
            if ( ctype_digit( $char )
                || ( $char === '-' && $i + 1 < $length && ctype_digit( $sql[$i + 1] ) ) )
            {
                $number = $char;
                $i++;
                while ( $i < $length && ( ctype_digit( $sql[$i] ) || $sql[$i] === '.' ) )
                    $number .= $sql[$i++];
                $tokens[] = array( 'type' => 'number', 'value' => $number );
                continue;
            }

            // An identifier, a keyword, or a qualified column name.
            if ( ctype_alpha( $char ) || $char === '_' )
            {
                $word = '';
                while ( $i < $length && ( ctype_alnum( $sql[$i] ) || $sql[$i] === '_' || $sql[$i] === '.' ) )
                    $word .= $sql[$i++];

                $upper = strtoupper( $word );
                if ( in_array( $upper, array( 'AND', 'OR', 'NOT', 'IN', 'LIKE', 'IS', 'NULL' ), true ) )
                    $tokens[] = array( 'type' => 'keyword', 'value' => $upper );
                else
                    $tokens[] = array( 'type' => 'ident', 'value' => $word );
                continue;
            }

            // Anything else - a function call, an arithmetic operator, a
            // placeholder - is not part of the grammar this understands.
            return false;
        }

        return $tokens;
    }

    protected function parseWhereOr( array $tokens, &$i )
    {
        $branches = array();
        $first = $this->parseWhereAnd( $tokens, $i );
        if ( $first === false )
            return false;
        $branches[] = $first;

        while ( $i < count( $tokens )
            && $tokens[$i]['type'] === 'keyword' && $tokens[$i]['value'] === 'OR' )
        {
            $i++;
            $next = $this->parseWhereAnd( $tokens, $i );
            if ( $next === false )
                return false;
            $branches[] = $next;
        }

        if ( count( $branches ) === 1 )
            return $branches[0];

        return array( '$or' => $branches );
    }

    protected function parseWhereAnd( array $tokens, &$i )
    {
        $terms = array();
        $first = $this->parseWhereTerm( $tokens, $i );
        if ( $first === false )
            return false;
        $terms[] = $first;

        while ( $i < count( $tokens )
            && $tokens[$i]['type'] === 'keyword' && $tokens[$i]['value'] === 'AND' )
        {
            $i++;
            $next = $this->parseWhereTerm( $tokens, $i );
            if ( $next === false )
                return false;
            $terms[] = $next;
        }

        if ( count( $terms ) === 1 )
            return $terms[0];

        // $and rather than merging keys, so two conditions on one column both
        // survive instead of the second overwriting the first.
        return array( '$and' => $terms );
    }

    protected function parseWhereTerm( array $tokens, &$i )
    {
        if ( $i >= count( $tokens ) )
            return false;

        // A bracketed boolean group. A bracket can equally open an integer
        // expression - "( lang_mask & 2 ) > 0" - so when the group does not
        // read as a boolean, or an operator follows it, the position is put
        // back and the term is retried as a comparison.
        if ( $tokens[$i]['type'] === '(' )
        {
            $saved = $i;
            $i++;
            $inner = $this->parseWhereOr( $tokens, $i );
            if ( $inner !== false && $i < count( $tokens ) && $tokens[$i]['type'] === ')' )
            {
                $i++;
                if ( $i >= count( $tokens ) || $tokens[$i]['type'] !== 'op' )
                    return $inner;
            }
            $i = $saved;
        }

        $left = $this->parseWhereOperand( $tokens, $i );
        if ( $left === false )
            return false;

        if ( $i >= count( $tokens ) )
            return false;

        // Null when the left side is an expression rather than a plain column.
        $column = isset( $left['field'] ) ? $left['field'] : null;
        $token = $tokens[$i];

        // IN, LIKE and IS NULL read a column, never an expression.
        if ( $column === null && $token['type'] === 'keyword' )
            return false;

        // column IS NULL / IS NOT NULL
        if ( $token['type'] === 'keyword' && $token['value'] === 'IS' )
        {
            $i++;
            $negated = false;
            if ( $i < count( $tokens ) && $tokens[$i]['type'] === 'keyword' && $tokens[$i]['value'] === 'NOT' )
            {
                $negated = true;
                $i++;
            }
            if ( $i >= count( $tokens ) || $tokens[$i]['type'] !== 'keyword' || $tokens[$i]['value'] !== 'NULL' )
                return false;
            $i++;
            return $negated ? array( $column => array( '$ne' => null ) ) : array( $column => null );
        }

        // column NOT IN (...) / column NOT LIKE '...'
        if ( $token['type'] === 'keyword' && $token['value'] === 'NOT' )
        {
            $i++;
            if ( $i >= count( $tokens ) || $tokens[$i]['type'] !== 'keyword' )
                return false;
            $keyword = $tokens[$i]['value'];
            $i++;
            if ( $keyword === 'IN' )
            {
                $values = $this->parseWhereValueList( $tokens, $i );
                return $values === false ? false : array( $column => array( '$nin' => $values ) );
            }
            if ( $keyword === 'LIKE' )
            {
                $regex = $this->parseWhereLike( $tokens, $i );
                return $regex === false ? false : array( $column => array( '$not' => $regex ) );
            }
            return false;
        }

        // column IN (...)
        if ( $token['type'] === 'keyword' && $token['value'] === 'IN' )
        {
            $i++;
            $values = $this->parseWhereValueList( $tokens, $i );
            return $values === false ? false : array( $column => array( '$in' => $values ) );
        }

        // column LIKE '...'
        if ( $token['type'] === 'keyword' && $token['value'] === 'LIKE' )
        {
            $i++;
            $regex = $this->parseWhereLike( $tokens, $i );
            return $regex === false ? false : array( $column => $regex );
        }

        // <column or expression> <op> value
        if ( $token['type'] === 'op' )
        {
            $operator = $token['value'];
            if ( !isset( self::$ComparisonOperators[$operator] ) )
                return false;
            $i++;
            if ( $i >= count( $tokens ) )
                return false;
            $valueToken = $tokens[$i];
            if ( !in_array( $valueToken['type'], array( 'number', 'string' ), true ) )
                return false;
            $i++;
            $value = self::whereValue( $valueToken );

            if ( $column === null )
            {
                // $expr keeps the arithmetic in the server, so the comparison
                // is evaluated over the real field rather than approximated
                // here over a guess at what the field holds.
                return array( '$expr' => array(
                    self::$ComparisonOperators[$operator] => array( $left['expr'], $value ) ) );
            }

            switch ( $operator )
            {
                case '=':  return array( $column => $value );
                case '!=': return array( $column => array( '$ne' => $value ) );
                case '<':  return array( $column => array( '$lt' => $value ) );
                case '<=': return array( $column => array( '$lte' => $value ) );
                case '>':  return array( $column => array( '$gt' => $value ) );
                case '>=': return array( $column => array( '$gte' => $value ) );
            }
            return false;
        }

        return false;
    }

    /**
     * The left side of a comparison: either a plain column, or an integer
     * expression over columns and constants.
     *
     * Returns array( 'field' => name ) for a lone column, so the everyday
     * "node_id = 42" still produces a plain indexable filter, or
     * array( 'expr' => tree ) for an aggregation expression MongoDB evaluates
     * itself.
     */
    protected function parseWhereOperand( array $tokens, &$i )
    {
        $start = $i;
        $tree = $this->parseOperandBitOr( $tokens, $i );
        if ( $tree === false )
            return false;

        if ( $i === $start + 1 && $tokens[$start]['type'] === 'ident' )
            return array( 'field' => $tokens[$start]['value'] );

        return array( 'expr' => $tree );
    }

    protected function parseOperandBitOr( array $tokens, &$i )
    {
        $left = $this->parseOperandBitXor( $tokens, $i );
        if ( $left === false )
            return false;
        while ( $i < count( $tokens ) && $tokens[$i]['type'] === 'op' && $tokens[$i]['value'] === '|' )
        {
            $i++;
            $right = $this->parseOperandBitXor( $tokens, $i );
            if ( $right === false )
                return false;
            $left = array( '$bitOr' => array( $left, $right ) );
        }
        return $left;
    }

    protected function parseOperandBitXor( array $tokens, &$i )
    {
        $left = $this->parseOperandBitAnd( $tokens, $i );
        if ( $left === false )
            return false;
        while ( $i < count( $tokens ) && $tokens[$i]['type'] === 'op' && $tokens[$i]['value'] === '^' )
        {
            $i++;
            $right = $this->parseOperandBitAnd( $tokens, $i );
            if ( $right === false )
                return false;
            $left = array( '$bitXor' => array( $left, $right ) );
        }
        return $left;
    }

    protected function parseOperandBitAnd( array $tokens, &$i )
    {
        $left = $this->parseOperandUnary( $tokens, $i );
        if ( $left === false )
            return false;
        while ( $i < count( $tokens ) && $tokens[$i]['type'] === 'op' && $tokens[$i]['value'] === '&' )
        {
            $i++;
            $right = $this->parseOperandUnary( $tokens, $i );
            if ( $right === false )
                return false;
            $left = array( '$bitAnd' => array( $left, $right ) );
        }
        return $left;
    }

    protected function parseOperandUnary( array $tokens, &$i )
    {
        if ( $i >= count( $tokens ) )
            return false;

        $token = $tokens[$i];

        if ( $token['type'] === 'op' && $token['value'] === '~' )
        {
            $i++;
            $inner = $this->parseOperandUnary( $tokens, $i );
            return $inner === false ? false : array( '$bitNot' => $inner );
        }

        if ( $token['type'] === '(' )
        {
            $i++;
            $inner = $this->parseOperandBitOr( $tokens, $i );
            if ( $inner === false || $i >= count( $tokens ) || $tokens[$i]['type'] !== ')' )
                return false;
            $i++;
            return $inner;
        }

        if ( $token['type'] === 'number' )
        {
            $i++;
            return self::whereValue( $token );
        }

        if ( $token['type'] === 'ident' )
        {
            $i++;
            // $bitAnd and friends accept only int and long, and some of these
            // masks are stored as strings. $convert reads one either way and
            // treats an absent or unreadable value as 0, which is how the SQL
            // engines compare a NULL mask and how evaluateIntExpression()
            // reads one.
            return array( '$convert' => array(
                'input' => '$' . $token['value'], 'to' => 'long', 'onError' => 0, 'onNull' => 0 ) );
        }

        return false;
    }

    protected function parseWhereValueList( array $tokens, &$i )
    {
        if ( $i >= count( $tokens ) || $tokens[$i]['type'] !== '(' )
            return false;
        $i++;

        $values = array();
        while ( $i < count( $tokens ) && $tokens[$i]['type'] !== ')' )
        {
            if ( $tokens[$i]['type'] === ',' )
            {
                $i++;
                continue;
            }
            if ( !in_array( $tokens[$i]['type'], array( 'number', 'string' ), true ) )
                return false;
            $values[] = self::whereValue( $tokens[$i] );
            $i++;
        }

        if ( $i >= count( $tokens ) || $tokens[$i]['type'] !== ')' )
            return false;
        $i++;

        return $values;
    }

    /**
     * A LIKE pattern as a regex. % and _ are SQL's wildcards; everything else
     * is quoted so a dot or a bracket in a path does not become a wildcard.
     */
    protected function parseWhereLike( array $tokens, &$i )
    {
        if ( $i >= count( $tokens ) || $tokens[$i]['type'] !== 'string' )
            return false;

        $pattern = $tokens[$i]['value'];
        $i++;

        $regex = '';
        $length = strlen( $pattern );
        for ( $k = 0; $k < $length; $k++ )
        {
            $char = $pattern[$k];
            if ( $char === '%' )
                $regex .= '.*';
            elseif ( $char === '_' )
                $regex .= '.';
            else
                // No delimiter: a MongoDB regex has none, so escaping the
                // slashes in a path would only add noise to the pattern.
                $regex .= preg_quote( $char );
        }

        return new MongoDB\BSON\Regex( '^' . $regex . '$', '' );
    }

    /**
     * The PHP value for a token.
     *
     * A quoted numeric string becomes an integer because the kernel quotes
     * integers throughout - "WHERE node_id = '42'" - while the documents hold
     * them as numbers.
     */
    static function whereValue( array $token )
    {
        if ( $token['type'] === 'number' )
            return strpos( $token['value'], '.' ) !== false ? (float)$token['value'] : (int)$token['value'];

        $value = $token['value'];
        if ( preg_match( '/^-?\d+$/', $value ) )
            return (int)$value;

        return $value;
    }

    function query( $sql, $server = false )
    {
        $dbName = $this->DB;
        $sql = trim( $sql );
        $this->traceStatement( 'query: ' . $sql );

        // --- UPDATE table SET col=val [, col=val ...] WHERE conditions ---
        if ( preg_match( '/^\s*UPDATE\s+([\w]+)\s+SET\s+(.+?)\s+WHERE\s+(.+)$/is', $sql, $m ) )
        {
            $table    = trim( $m[1] );
            $setClause = trim( $m[2] );
            $whereSql  = trim( $m[3] );

            // Parse SET clause into individual assignments (handles simple col=val and col=col+expr)
            $setFields = [];
            // Split on commas that are not inside parentheses or a quoted
            // string. Tracking only the brackets cut every JSON value at its
            // first comma - the layout collections store their whole query as
            // JSON, so each one was left holding
            // '{"use_topic_from_current_content":true and nothing else, and
            // every dynamic list on the site came back wrong or empty.
            $parts = [];
            $depth = 0;
            $inString = false;
            $current = '';
            $length = strlen( $setClause );
            for ( $i = 0; $i < $length; $i++ )
            {
                $c = $setClause[$i];

                if ( $inString )
                {
                    $current .= $c;
                    if ( $c === '\\' && $i + 1 < $length )
                    {
                        // A backslash escape carries the next character with it.
                        $current .= $setClause[++$i];
                    }
                    elseif ( $c === "'" )
                    {
                        if ( $i + 1 < $length && $setClause[$i + 1] === "'" )
                            $current .= $setClause[++$i];   // '' is one quote
                        else
                            $inString = false;
                    }
                    continue;
                }

                if ( $c === "'" )
                {
                    $inString = true;
                    $current .= $c;
                    continue;
                }

                if ( $c === '(' ) $depth++;
                elseif ( $c === ')' ) $depth--;
                if ( $c === ',' && $depth === 0 )
                {
                    $parts[] = $current;
                    $current = '';
                }
                else
                {
                    $current .= $c;
                }
            }
            if ( $current !== '' ) $parts[] = $current;
            foreach ( $parts as $part )
            {
                $part = trim( $part );
                if ( preg_match( '/^([\w]+)\s*=\s*(.+)$/s', $part, $fm ) )
                {
                    $col = trim( $fm[1] );
                    $val = trim( $fm[2] );
                    // Integer literal
                    if ( preg_match( '/^-?\d+$/', $val ) )
                    {
                        $setFields[$col] = (int) $val;
                    }
                    // depth = depth +/- N [+/- M ...] — evaluate the arithmetic
                    // delta. The kernel writes this both bare and bracketed,
                    // as "object_count = ( object_count - 1 )", so the
                    // brackets come off first.
                    elseif ( preg_match( '/^\(\s*(.+?)\s*\)$/s', $val, $bm )
                        && preg_match( '/^' . $col . '\s*[\+\-]/i', trim( $bm[1] ) )
                        && ( $val = trim( $bm[1] ) ) !== ''
                        && preg_match( '/^' . $col . '\s*([\+\-\d\s]+)$/i', $val, $dm ) )
                    {
                        $expr = $dm[1];
                        preg_match_all( '/([\+\-])\s*(\d+)/', $expr, $terms, PREG_SET_ORDER );
                        $delta = 0;
                        foreach ( $terms as $term )
                            $delta += ( $term[1] === '+' ? 1 : -1 ) * (int) $term[2];
                        $setFields[$col] = [ '$inc' => $delta ];
                    }
                    elseif ( preg_match( '/^' . $col . '\s*([\+\-\d\s]+)$/i', $val, $dm ) )
                    {
                        // Extract and evaluate: e.g. " + 3 - 2 + 1" => 2
                        $expr = $dm[1];
                        preg_match_all( '/([\+\-])\s*(\d+)/', $expr, $terms, PREG_SET_ORDER );
                        $delta = 0;
                        foreach ( $terms as $term )
                            $delta += ( $term[1] === '+' ? 1 : -1 ) * (int) $term[2];
                        $setFields[$col] = [ '$inc' => $delta ];
                    }
                    // String literal 'value'. A doubled quote is SQL's way of
                    // writing one, the same as castSqlLiteral() reads it.
                    elseif ( preg_match( "/^'(.*)'$/s", $val, $sm ) )
                    {
                        // Typed by the column, not by the quoting: writing a
                        // quoted number into a numeric column as a string is
                        // what stopped the next $inc on it from working.
                        $setFields[$col] = self::castSqlLiteralForColumn( $val, $table, $col );
                    }
                    // A bitwise expression over the row's own columns, which the
                    // kernel writes as raw SQL for the language masks, e.g.
                    // "language_id & ~1" or "( language_id & 1 ) | 2". Left as a
                    // string these land in the document as SQL text, and the next
                    // read of them fails on "Unsupported operand types".
                    elseif ( self::looksLikeIntExpression( $val ) )
                    {
                        $setFields[$col] = [ '__expr__' => $val ];
                    }
                    // CONCAT( expr, expr ) — for path_string / path_identification_string rebuild
                    elseif ( preg_match( '/^CONCAT\s*\((.+)\)$/is', $val, $cm ) )
                    {
                        // Store as a special marker; handled per-document below
                        $setFields[$col] = [ '__concat__' => $cm[1] ];
                    }
                    // An unquoted value that is neither a literal nor an
                    // expression this driver understands must not be written
                    // through: storing SQL text as a field value corrupts the
                    // row silently and only fails much later, somewhere else.
                    elseif ( preg_match( '/[()&|~^*\/]|\b[a-z_]+\s*\(/i', $val ) )
                    {
                        $this->logError( 'expMongoDB::query cannot translate the SET expression '
                            . var_export( $val, true ) . ' for column ' . $col
                            . '; refusing to store it as text. SQL: ' . substr( $sql, 0, 200 ) );
                        return false;
                    }
                    else
                    {
                        $setFields[$col] = $val; // a bare unquoted scalar
                    }
                }
            }

            // A clause the parser refuses must stop the statement. Treating it
            // as an empty filter would update every document in the
            // collection, which is far worse than not updating at all.
            $filter = $this->parseWhereClause( $whereSql );
            if ( $filter === false )
            {
                $this->logError( 'expMongoDB::query UPDATE refused, its WHERE could not be translated: '
                    . substr( $sql, 0, 200 ) );
                return false;
            }
            if ( empty( $filter ) )
            {
                $this->logError( 'expMongoDB::query UPDATE refused, its WHERE matched nothing to scope it: '
                    . substr( $sql, 0, 200 ) );
                return false;
            }

            $collection = $this->getClient()->selectCollection( $dbName, $table );

            // CONCAT and bitwise expressions both need the document in hand
            // before the new value can be worked out.
            $hasConcatField = false;
            foreach ( $setFields as $col => $val )
            {
                if ( is_array( $val ) && ( isset( $val['__concat__'] ) || isset( $val['__expr__'] ) ) )
                {
                    $hasConcatField = true;
                    break;
                }
            }

            try
            {
                if ( $hasConcatField )
                {
                    // Per-document update needed for CONCAT expressions
                    $docs = $collection->find( $filter );
                    foreach ( $docs as $doc )
                    {
                        $docUpdate = [];
                        foreach ( $setFields as $col => $fieldVal )
                        {
                            if ( is_array( $fieldVal ) && isset( $fieldVal['__concat__'] ) )
                            {
                                // Evaluate CONCAT: replace old path prefix with new path prefix
                                // Pattern: CONCAT('newPrefix', SUBSTRING(field, offset))
                                $concatExpr = $fieldVal['__concat__'];
                                // Handle both SUBSTRING(col, N) and substring( col from N ) syntax
                                if ( preg_match( "/^'([^']*)'\s*,\s*(?:SUBSTRING|SUBSTR)\s*\(\s*[\w]+\s*(?:,|FROM)\s*(\d+)\s*\)/i", $concatExpr, $concatM )
                                     || preg_match( "/^'([^']*)'\s*,\s*substring\s*\(\s*[\w]+\s+from\s+(\d+)\s*(?:for\s+\d+\s*)?\)/i", $concatExpr, $concatM ) )
                                {
                                    $newPrefix = $concatM[1];
                                    $offset    = (int) $concatM[2] - 1; // SQL SUBSTRING is 1-based
                                    $oldVal    = isset( $doc[$col] ) ? (string) $doc[$col] : '';
                                    $docUpdate[$col] = $newPrefix . substr( $oldVal, $offset );
                                }
                            }
                            elseif ( is_array( $fieldVal ) && isset( $fieldVal['__expr__'] ) )
                            {
                                $evaluated = self::evaluateIntExpression( $fieldVal['__expr__'], $doc );
                                if ( $evaluated === false )
                                {
                                    $this->logError( 'expMongoDB::query could not evaluate '
                                        . var_export( $fieldVal['__expr__'], true ) . ' for column ' . $col );
                                    return false;
                                }
                                $docUpdate[$col] = $evaluated;
                            }
                            elseif ( is_array( $fieldVal ) && isset( $fieldVal['$inc'] ) )
                            {
                                $docUpdate[$col] = ( (int) $doc[$col] ) + $fieldVal['$inc'];
                            }
                            elseif ( is_array( $fieldVal ) && isset( $fieldVal['$dec'] ) )
                            {
                                $docUpdate[$col] = ( (int) $doc[$col] ) - $fieldVal['$dec'];
                            }
                            else
                            {
                                $docUpdate[$col] = $fieldVal;
                            }
                        }
                        if ( $docUpdate )
                            $collection->updateOne( [ '_id' => $doc['_id'] ],
                                [ '$set' => self::toBsonSafe( $docUpdate ) ] );
                    }
                }
                else
                {
                    // Build standard $set / $inc updateMany
                    $setOp = [];
                    $incOp = [];
                    foreach ( $setFields as $col => $fieldVal )
                    {
                        if ( is_array( $fieldVal ) && isset( $fieldVal['$inc'] ) )
                            $incOp[$col] = $fieldVal['$inc'];
                        elseif ( is_array( $fieldVal ) && isset( $fieldVal['$dec'] ) )
                            $incOp[$col] = -$fieldVal['$dec'];
                        else
                            $setOp[$col] = $fieldVal;
                    }
                    $updateDoc = [];
                    if ( $setOp ) $updateDoc['$set'] = self::toBsonSafe( $setOp );
                    if ( $incOp ) $updateDoc['$inc'] = $incOp;
                    if ( $updateDoc )
                        $collection->updateMany( $filter, $updateDoc );
                }
            }
            catch ( Exception $e )
            {
                $this->logError( 'expMongoDB::query UPDATE failed: ' . $e->getMessage() . ' SQL: ' . substr( $sql, 0, 200 ) );
                return false;
            }
            return true;
        }

        // --- INSERT INTO table ( cols ) VALUES ( ... ) [, ( ... ) ...] ---
        // The kernel writes these by hand, with the table and columns spread
        // over several lines and sometimes several value tuples at once - the
        // search engine and the object state links both do. query() had no
        // INSERT branch at all, so every one of them was logged as unhandled
        // and silently dropped, which is why objects failed to reach the
        // search index and no state links were ever written.
        if ( preg_match( '/^\s*INSERT\s+(?:IGNORE\s+)?INTO\s+([\w]+)\s*\(([^)]*)\)\s*VALUES\s*(.+?)\s*;?\s*$/is', $sql, $m ) )
        {
            $table = trim( $m[1] );
            $columns = array_map( 'trim', explode( ',', $m[2] ) );
            $tuples = self::splitSqlTuples( $m[3] );

            if ( !$tuples )
            {
                $this->logError( 'expMongoDB::query INSERT with no parsable values: ' . substr( $sql, 0, 200 ) );
                return false;
            }

            $documents = array();
            foreach ( $tuples as $tuple )
            {
                $values = self::splitSqlList( $tuple );
                if ( count( $values ) !== count( $columns ) )
                {
                    $this->logError( 'expMongoDB::query INSERT column/value count mismatch ('
                        . count( $columns ) . ' vs ' . count( $values ) . '): ' . substr( $sql, 0, 200 ) );
                    return false;
                }
                $document = array();
                foreach ( $columns as $index => $column )
                    $document[$column] = self::castSqlLiteralForColumn( $values[$index], $table, $column );
                $documents[] = $document;
            }

            try
            {
                $collection = $this->getClient()->selectCollection( $this->DB, $table );
                $documents = $this->applyAutoIncrementBatch( $table, $documents );
                $documents = self::toBsonSafe( $documents );
                if ( count( $documents ) === 1 )
                    $collection->insertOne( $documents[0] );
                else
                    $collection->insertMany( $documents );
            }
            catch ( Exception $e )
            {
                $this->logError( 'expMongoDB::query INSERT failed: ' . $e->getMessage()
                    . ' SQL: ' . substr( $sql, 0, 200 ) );
                return false;
            }
            return true;
        }

        // --- DELETE FROM table WHERE conditions ---
        if ( preg_match( '/^\s*DELETE\s+FROM\s+([\w]+)\s+WHERE\s+(.+)$/is', $sql, $m ) )
        {
            $table    = trim( $m[1] );
            $whereSql = trim( $m[2] );
            $filter = $this->parseWhereClause( $whereSql );
            if ( $filter === false || empty( $filter ) )
            {
                $this->logError( 'expMongoDB::query DELETE refused, its WHERE could not be translated: '
                    . substr( $sql, 0, 200 ) );
                return false;
            }
            try
            {
                $this->getClient()->selectCollection( $dbName, $table )->deleteMany( $filter );
            }
            catch ( Exception $e )
            {
                $this->logError( 'expMongoDB::query DELETE failed: ' . $e->getMessage() );
                return false;
            }
            return true;
        }

        // --- UPDATE a alias INNER JOIN b alias ON a.x = b.y SET ... WHERE ... ---
        // eztags upgrades its rows this way. A join is two reads in MongoDB:
        // collect the joining values from the second collection, then update
        // the first where its own column is one of them.
        if ( preg_match( '/^\s*UPDATE\s+(\w+)\s+(\w+)\s+INNER\s+JOIN\s+(\w+)\s+(\w+)'
            . '\s+ON\s+(\w+)\.(\w+)\s*=\s*(\w+)\.(\w+)\s+SET\s+(.+?)\s+WHERE\s+(.+)$/is',
            $sql, $m ) )
        {
            $left = trim( $m[1] );
            $leftAlias = trim( $m[2] );
            $right = trim( $m[3] );
            $rightAlias = trim( $m[4] );
            $onLeft = array( trim( $m[5] ) => trim( $m[6] ) );
            $onRight = array( trim( $m[7] ) => trim( $m[8] ) );
            $setClause = trim( $m[9] );
            $whereSql = trim( $m[10] );

            // The ON sides may be written either way round.
            $aliases = array( $leftAlias => $left, $rightAlias => $right );
            $joinColumns = $onLeft + $onRight;
            if ( count( $joinColumns ) !== 2
                || !isset( $joinColumns[$leftAlias] ) || !isset( $joinColumns[$rightAlias] ) )
            {
                $this->logError( 'expMongoDB::query JOIN UPDATE refused, its ON clause does not '
                    . 'name both aliases: ' . substr( $sql, 0, 300 ) );
                return false;
            }

            // Everything in SET must belong to the updated collection, and
            // everything in WHERE to the joined one, or the rewrite would not
            // mean the same thing.
            $setFields = array();
            foreach ( explode( ',', $setClause ) as $assignment )
            {
                if ( !preg_match( '/^\s*(\w+)\.(\w+)\s*=\s*(.+?)\s*$/s', $assignment, $am )
                    || $am[1] !== $leftAlias )
                {
                    $this->logError( 'expMongoDB::query JOIN UPDATE refused, SET assigns outside '
                        . 'the updated table: ' . substr( $sql, 0, 300 ) );
                    return false;
                }
                $setFields[$am[2]] = self::castSqlLiteralForColumn( trim( $am[3] ), $left, $am[2] );
            }

            $joinedWhere = $this->parseWhereClause(
                preg_replace( '/\b' . preg_quote( $rightAlias, '/' ) . '\./', '', $whereSql ) );
            if ( $joinedWhere === false || empty( $joinedWhere ) )
            {
                $this->logError( 'expMongoDB::query JOIN UPDATE refused, its WHERE could not be '
                    . 'translated: ' . substr( $sql, 0, 300 ) );
                return false;
            }

            try
            {
                $client = $this->getClient();
                $keys = array();
                $cursor = $client->selectCollection( $dbName, $right )->find(
                    $joinedWhere, array( 'projection' => array( $joinColumns[$rightAlias] => 1 ) ) );
                foreach ( $cursor as $row )
                {
                    if ( isset( $row[$joinColumns[$rightAlias]] ) )
                        $keys[] = $row[$joinColumns[$rightAlias]];
                }
                $keys = array_values( array_unique( $keys, SORT_REGULAR ) );
                if ( !$keys )
                    return true;

                $client->selectCollection( $dbName, $left )->updateMany(
                    array( $joinColumns[$leftAlias] => array( '$in' => $keys ) ),
                    array( '$set' => self::toBsonSafe( $setFields ) ) );
            }
            catch ( Exception $e )
            {
                $this->logError( 'expMongoDB::query JOIN UPDATE failed: ' . $e->getMessage()
                    . ' SQL: ' . substr( $sql, 0, 300 ) );
                return false;
            }
            return true;
        }

        // --- TRUNCATE TABLE x --- empties the collection, keeps it in place.
        if ( preg_match( '/^\s*TRUNCATE\s+(?:TABLE\s+)?(\w+)\s*;?\s*$/i', $sql, $m ) )
        {
            try
            {
                $this->getClient()->selectCollection( $dbName, trim( $m[1] ) )->deleteMany( array() );
            }
            catch ( Exception $e )
            {
                $this->logError( 'expMongoDB::query TRUNCATE failed: ' . $e->getMessage()
                    . ' SQL: ' . substr( $sql, 0, 300 ) );
                return false;
            }
            return true;
        }

        // --- DROP TABLE [IF EXISTS] x --- dropping an absent collection is
        // not an error in MongoDB, so IF EXISTS needs no special case.
        if ( preg_match( '/^\s*DROP\s+TABLE\s+(?:IF\s+EXISTS\s+)?(\w+)\s*;?\s*$/i', $sql, $m ) )
        {
            try
            {
                $this->getClient()->selectCollection( $dbName, trim( $m[1] ) )->drop();
            }
            catch ( Exception $e )
            {
                $this->logError( 'expMongoDB::query DROP TABLE failed: ' . $e->getMessage()
                    . ' SQL: ' . substr( $sql, 0, 300 ) );
                return false;
            }
            return true;
        }

        // --- CREATE TABLE [IF NOT EXISTS] x ( ... ) --- MongoDB creates a
        // collection on first write, so the statement exists here for its
        // indexes. Without a PRIMARY index autoIncrementField() has no key to
        // read and generated ids come back empty; without the UNIQUE ones
        // nothing stops a duplicate. Extensions that build a table at runtime
        // used to get the unhandled-SQL path instead, which returned false and
        // left them writing into a collection that was never prepared.
        if ( preg_match( '/^\s*CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?[`"]?(\w+)[`"]?\s*\((.*)\)[^)]*$/is', $sql, $m ) )
        {
            $tableName = trim( $m[1] );
            try
            {
                $collection = $this->getClient()->selectCollection( $dbName, $tableName );
                foreach ( self::parseCreateTableIndexes( $m[2] ) as $indexName => $index )
                {
                    $keyDoc = array();
                    foreach ( $index['fields'] as $fieldName )
                        $keyDoc[$fieldName] = 1;

                    $options = array( 'name' => $indexName );
                    if ( $index['unique'] )
                        $options['unique'] = true;

                    // Re-issuing an identical index is a no-op in MongoDB, which
                    // is what IF NOT EXISTS has to mean here.
                    $collection->createIndex( $keyDoc, $options );
                }
            }
            catch ( Exception $e )
            {
                $this->logError( 'expMongoDB::query CREATE TABLE failed: ' . $e->getMessage()
                    . ' SQL: ' . substr( $sql, 0, 300 ) );
                return false;
            }
            $this->AutoIncrementFields = array();
            return true;
        }

        // --- SET <session variable> = ... --- MySQL session settings such as
        // FOREIGN_KEY_CHECKS and NAMES. MongoDB enforces no foreign keys and
        // speaks only UTF-8, so there is nothing to apply and nothing lost.
        if ( preg_match( '/^\s*SET\s+(?:SESSION\s+|GLOBAL\s+)?(?:FOREIGN_KEY_CHECKS|NAMES|'
            . 'CHARACTER\s+SET|AUTOCOMMIT|SQL_MODE|UNIQUE_CHECKS)\b/i', $sql ) )
        {
            return true;
        }

        $this->logError( 'expMongoDB::query unhandled SQL: ' . substr( $sql, 0, 300 ) );
        return false;
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        $dbName = $this->DB;
        $sql = trim( $sql );
        $this->traceStatement( 'arrayQuery: ' . $sql );

        // --- SELECT col[, col...] FROM single_table WHERE conditions [ORDER BY ...] [LIMIT n] ---
        // Only handle single-table, no JOINs, no sub-queries
        if ( preg_match( '/^\s*SELECT\s+(?:DISTINCT\s+)?(.+?)\s+FROM\s+([\w]+)\s*'
                . '(?:WHERE\s+(.+?))?(?:\s+ORDER\s+BY\s+(.+?))?'
                . '(?:\s+LIMIT\s+(\d+)(?:\s*,\s*(\d+))?)?\s*$/is', $sql, $m )
             && strpos( $m[2], ',' ) === false     // single table only
             && stripos( $sql, ' GROUP BY ' ) === false )
        {
            $selectClause = trim( $m[1] );
            $table        = trim( $m[2] );
            $whereSql     = isset( $m[3] ) ? trim( $m[3] ) : '';
            $orderBySql   = isset( $m[4] ) ? trim( $m[4] ) : '';
            $limitSql     = isset( $m[5] ) && $m[5] !== '' ? (int)$m[5] : 0;
            $limitOffset  = isset( $m[6] ) && $m[6] !== '' ? (int)$m[6] : 0;

            // "LIMIT offset, count" puts the offset first.
            if ( $limitOffset > 0 )
            {
                $skipRows = $limitSql;
                $limitSql = $limitOffset;
            }
            else
            {
                $skipRows = 0;
            }

            // An aggregate select - the existence checks and the counters -
            // has to be answered by a $group, not by handing back documents
            // that carry no such column.
            $aggregates = self::parseSelectAggregates( $selectClause );
            if ( $aggregates !== false )
            {
                $filter = $whereSql !== '' ? $this->parseWhereClause( $whereSql ) : array();
                if ( $filter === false )
                {
                    $this->logError( 'expMongoDB::arrayQuery aggregate refused, its WHERE could not '
                        . 'be translated: ' . substr( $sql, 0, 200 ) );
                    return false;
                }
                return $this->runAggregateSelect( $table, $filter, $aggregates, $sql );
            }

            // Build projection from SELECT list (skip * and COUNT(*))
            $projection = [ '_id' => 0 ];
            if ( $selectClause !== '*' && stripos( $selectClause, 'COUNT(' ) === false )
            {
                foreach ( preg_split( '/\s*,\s*/', $selectClause ) as $col )
                {
                    $col = trim( $col );
                    // strip table.col prefix if present
                    if ( strpos( $col, '.' ) !== false )
                        $col = substr( $col, strrpos( $col, '.' ) + 1 );
                    if ( $col !== '' )
                        $projection[$col] = 1;
                }
            }
            else
            {
                $projection = []; // no projection = all fields
            }

            $filter = $whereSql !== '' ? $this->parseWhereClause( $whereSql ) : array();
            if ( $filter === false )
            {
                // Returning every document would render one page's content on
                // another, which is exactly what the old silent-drop did.
                $this->logError( 'expMongoDB::query SELECT refused, its WHERE could not be translated: '
                    . substr( $sql, 0, 200 ) );
                return false;
            }

            // Pagination from $params
            $options = [];
            if ( !empty( $projection ) )
                $options['projection'] = $projection;
            if ( isset( $params['limit'] ) && $params['limit'] > 0 )
                $options['limit'] = (int) $params['limit'];
            if ( isset( $params['offset'] ) && $params['offset'] > 0 )
                $options['skip'] = (int) $params['offset'];

            // ORDER BY and LIMIT used to be matched and thrown away, so
            // "ORDER BY node_id ASC LIMIT 1" returned whichever row the
            // collection happened to yield first.
            if ( $orderBySql !== '' )
            {
                $sort = array();
                foreach ( explode( ',', $orderBySql ) as $term )
                {
                    $term = trim( $term );
                    if ( $term === '' )
                        continue;
                    $parts = preg_split( '/\s+/', $term );
                    $field = $parts[0];
                    if ( strpos( $field, '.' ) !== false )
                        $field = substr( $field, strrpos( $field, '.' ) + 1 );
                    $sort[$field] = ( isset( $parts[1] ) && strtoupper( $parts[1] ) === 'DESC' ) ? -1 : 1;
                }
                if ( $sort )
                    $options['sort'] = $sort;
            }
            if ( $limitSql > 0 && !isset( $options['limit'] ) )
                $options['limit'] = $limitSql;
            if ( $skipRows > 0 && !isset( $options['skip'] ) )
                $options['skip'] = $skipRows;

            $result = [];
            try {
                $cursor = $this->getClient()->selectCollection( $dbName, $table )->find( $filter, $options );
                foreach ( $cursor as $doc )
                    $result[] = $doc->getArrayCopy();
            } catch ( Exception $e ) {
                $this->logError( 'expMongoDB::arrayQuery SELECT failed: ' . $e->getMessage() . ' SQL: ' . substr( $sql, 0, 200 ) );
            }
            return $result;
        }

        // Cannot translate arbitrary multi-table SQL to MongoDB. Log caller info.
        $trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 5 );
        // Find the first frame outside this file
        $caller = '';
        foreach ( $trace as $frame )
        {
            $file = isset( $frame['file'] ) ? $frame['file'] : '';
            if ( strpos( $file, 'expmongodb.php' ) !== false ) continue;
            $file  = str_replace( '/web/vh/alpha.se7enx.com/doc/mongodb.alpha.se7enx.com/', '', $file );
            $line  = isset( $frame['line'] ) ? $frame['line'] : '?';
            $func  = ( isset( $frame['class'] ) ? $frame['class'] . '::' : '' ) . ( isset( $frame['function'] ) ? $frame['function'] : '' );
            $caller = "$file:$line ($func)";
            break;
        }
        // Extract tables referenced in the SQL for a concise label
        $tables = '';
        if ( preg_match( '/\bFROM\s+([\w, ]+?)(?:\s+WHERE|\s+ORDER|\s+GROUP|\s+LIMIT|$)/is', $sql, $m ) )
            $tables = trim( preg_replace( '/\s+/', ' ', $m[1] ) );
        $label = "MONGO TODO arrayQuery: tables=[$tables] caller=$caller";
        eZDebug::writeWarning( $label, 'expMongoDB' );
        if ( $this->OutputSQL )
        {
            eZDebug::accumulatorStart( 'mongodb_query', 'MongoDB Total', 'MongoDB queries' );
            $this->startTimer();
        }
        $result = [];
        if ( $this->OutputSQL )
        {
            $this->endTimer();
            $this->reportQuery( __CLASS__, $label, 0, $this->timeTaken() );
            eZDebug::accumulatorStop( 'mongodb_query' );
        }
        return $result;
    }

    /**
     * Read a SELECT list that is nothing but aggregate calls.
     *
     * Returns one entry per column - function, argument, distinct flag and the
     * name the caller will read the value back under - or false when the list
     * holds anything else, so an ordinary SELECT still takes the find() path.
     */
    static function parseSelectAggregates( $selectClause )
    {
        $columns = self::splitSqlList( $selectClause );
        if ( !$columns )
            return false;

        $aggregates = array();
        foreach ( $columns as $column )
        {
            if ( !preg_match( '/^\s*(COUNT|MAX|MIN|SUM|AVG)\s*\(\s*(DISTINCT\s+)?'
                . '(\*|[\w.]+)\s*\)(?:\s+AS\s+(\w+))?\s*$/i', $column, $m ) )
            {
                return false;
            }

            $field = $m[3];
            if ( strpos( $field, '.' ) !== false )
                $field = substr( $field, strrpos( $field, '.' ) + 1 );

            $aggregates[] = array(
                'function' => strtoupper( $m[1] ),
                'field'    => $field,
                'distinct' => trim( $m[2] ) !== '',
                // With no AS, MySQL names the column after the expression, and
                // that is what the caller indexes the row with.
                'alias'    => isset( $m[4] ) && $m[4] !== '' ? $m[4] : trim( $column ),
            );
        }

        return $aggregates;
    }

    /**
     * Answer an aggregate SELECT with a $group, returning the single row the
     * caller expects. An empty collection still yields a row, with 0 for the
     * counts and null for the rest, exactly as SQL does.
     */
    protected function runAggregateSelect( $table, array $filter, array $aggregates, $sql )
    {
        $group = array( '_id' => null );
        $project = array( '_id' => 0 );
        $empty = array();

        foreach ( $aggregates as $index => $aggregate )
        {
            $key = 'a' . $index;
            $alias = $aggregate['alias'];
            $field = '$' . $aggregate['field'];

            if ( $aggregate['function'] === 'COUNT' )
            {
                $empty[$alias] = 0;
                if ( $aggregate['distinct'] )
                {
                    $group[$key] = array( '$addToSet' => $field );
                    $project[$alias] = array( '$size' => '$' . $key );
                    continue;
                }
                $group[$key] = $aggregate['field'] === '*'
                    ? array( '$sum' => 1 )
                    : array( '$sum' => array( '$cond' => array(
                        array( '$ne' => array( $field, null ) ), 1, 0 ) ) );
                $project[$alias] = '$' . $key;
                continue;
            }

            $empty[$alias] = null;
            $group[$key] = array( '$' . strtolower( $aggregate['function'] ) => $field );
            $project[$alias] = '$' . $key;
        }

        $pipeline = array();
        if ( $filter )
            $pipeline[] = array( '$match' => $filter );
        $pipeline[] = array( '$group' => $group );
        $pipeline[] = array( '$project' => $project );

        try
        {
            $rows = iterator_to_array(
                $this->getClient()->selectCollection( $this->DB, $table )->aggregate( $pipeline ) );
        }
        catch ( Exception $e )
        {
            $this->logError( 'expMongoDB::arrayQuery aggregate failed: ' . $e->getMessage()
                . ' SQL: ' . substr( $sql, 0, 200 ) );
            return false;
        }

        if ( !$rows )
            return array( $empty );

        $row = is_object( $rows[0] ) && method_exists( $rows[0], 'getArrayCopy' )
            ? $rows[0]->getArrayCopy() : (array)$rows[0];

        // A $max over no rows gives null; keep the SQL shape either way.
        return array( array_merge( $empty, $row ) );
    }

    /**
     * Turn a pipeline that is really a query back into a query.
     *
     * Most of what reaches aggregate() is a find() written the long way: a
     * $match, usually a $sort, and a $project that drops _id. An aggregation
     * costs several times a find for the same work, and the content engine
     * issues these by the thousand - on a search reindex, eighty eight of
     * every ninety eight.
     *
     * Only the stages that map exactly onto find() options are accepted, and
     * only in an order where the two mean the same thing: a $limit ahead of a
     * $sort limits first, which find cannot express, and a $project ahead of
     * a $sort can remove the field being sorted on. A second $match would
     * have to be merged with the first, which is not always the same query.
     * Anything else keeps the pipeline.
     *
     * @param array $pipeline
     * @return array|false array( 'filter' => array, 'options' => array )
     */
    static function findFromSimplePipeline( $pipeline )
    {
        if ( !is_array( $pipeline ) || !$pipeline )
            return false;

        // The order find() imposes: match, then sort, then skip, then limit,
        // and a projection that applies to what comes out.
        $order = array( '$match' => 1, '$sort' => 2, '$skip' => 3, '$limit' => 4, '$project' => 5 );

        $filter = array();
        $options = array();
        $seen = array();
        $previous = 0;

        foreach ( $pipeline as $stage )
        {
            if ( !is_array( $stage ) || count( $stage ) !== 1 )
                return false;

            $name = key( $stage );
            $value = current( $stage );

            if ( !isset( $order[$name] ) || isset( $seen[$name] ) || $order[$name] < $previous )
                return false;

            $previous = $order[$name];
            $seen[$name] = true;

            switch ( $name )
            {
                case '$match':   $filter = (array) $value;                break;
                case '$sort':    $options['sort'] = (array) $value;       break;
                case '$project': $options['projection'] = (array) $value; break;
                case '$skip':    $options['skip'] = (int) $value;         break;
                case '$limit':   $options['limit'] = (int) $value;        break;
            }
        }

        return array( 'filter' => $filter, 'options' => $options );
    }

    /**
     * Run what findFromSimplePipeline() reduced a pipeline to.
     * Returns rows in the same shape aggregate() would have.
     */
    protected function runFind( $table, array $find )
    {
        $this->traceStatement( 'find ' . $table . ': ' . json_encode( $find['filter'] ) );

        $results = array();
        if ( $this->OutputSQL )
        {
            eZDebug::accumulatorStart( 'mongodb_query', 'MongoDB Total', 'MongoDB queries' );
            $this->startTimer();
        }

        try
        {
            $cursor = $this->getClient()->selectCollection( $this->DB, $table )
                ->find( $find['filter'], $find['options'] );
            foreach ( $cursor as $document )
                $results[] = $document->getArrayCopy();
        }
        catch ( Exception $e )
        {
            $this->logError( 'expMongoDB::runFind ' . $e->getMessage()
                . ' filter: ' . json_encode( $find['filter'] ) );
        }

        if ( $this->OutputSQL )
        {
            $this->endTimer();
            $this->reportQuery( __CLASS__, "find($table) " . json_encode( $find['filter'] ),
                count( $results ), $this->timeTaken() );
            eZDebug::accumulatorStop( 'mongodb_query' );
        }

        return $results;
    }

    function aggregate( $table, $pipeline = [] )
    {
        $find = self::findFromSimplePipeline( $pipeline );
        if ( $find !== false )
            return $this->runFind( $table, $find );

        $this->traceStatement( 'aggregate ' . $table . ': ' . json_encode( $pipeline ) );

        $dbName = $this->DB;
        $results = [];
        if ( $this->OutputSQL )
        {
            eZDebug::accumulatorStart( 'mongodb_query', 'MongoDB Total', 'MongoDB queries' );
            $this->startTimer();
        }
        try {
            $cursor = $this->getClient()->selectCollection( $dbName, $table )->aggregate($pipeline);
            foreach ( $cursor as $doc ) {
                $results[] = $doc->getArrayCopy();
            }
        } catch (Exception $e) {
            $this->logError('expMongoDB::aggregate ' . $e->getMessage());
        }
        if ( $this->OutputSQL )
        {
            $this->endTimer();
            $matchStage = isset( $pipeline[0]['$match'] ) ? json_encode( $pipeline[0]['$match'] ) : '...';
            $this->reportQuery( __CLASS__, "aggregate($table) " . $matchStage, count( $results ), $this->timeTaken() );
            eZDebug::accumulatorStop( 'mongodb_query' );
        }
        return $results;
    }

    /**
     * How many documents in $table match $conds.
     *
     * The kernel calls this where the SQL path issues SELECT COUNT(*): a pager
     * needs the total without fetching the rows. $conds takes the same shape
     * find() accepts.
     */
    function count( $table, $conds = array() )
    {
        try
        {
            return (int) $this->getClient()->selectCollection( $this->DB, $table )
                ->countDocuments( $this->translateConditions( $conds ) );
        }
        catch ( Exception $e )
        {
            $this->logError( 'expMongoDB::count ' . $table . ' ' . $e->getMessage() );
            return 0;
        }
    }

    function find( $table, $conds, $projection = [] )
    {
        $dbName = $this->DB;
        $results = false;
        $options = [];
        if ( !empty( $projection ) )
            $options['projection'] = $projection;
        try {
            $filter = $this->translateConditions( $conds );
            $cursor = $this->getClient()->selectCollection( $dbName, $table )->find( $filter, $options );
            $results = [];
            foreach ( $cursor as $doc ) {
                $results[] = $doc->getArrayCopy();
            }
        } catch (Exception $e) {
            $this->logError('expMongoDB::find ' . $e->getMessage());
        }
        return $results;
    }

    /**
     * Translates eZPersistentObject condition array to a MongoDB filter array.
     *   'field' => scalar                  -> exact match
     *   'field' => ['>', value]            -> $gt / $gte / $lt / $lte / $ne
     *   'field' => ['like', '%val%']       -> $regex
     *   'field' => [false, [start, end]]   -> $gte/$lte range
     *   'field' => [[1,5,7]]               -> $in
     */
    public function translateConditions( $conds )
    {
        if ( empty( $conds ) )
            return [];

        $opMap = [
            '='  => null,
            '!=' => '$ne',
            '>'  => '$gt',
            '>=' => '$gte',
            '<'  => '$lt',
            '<=' => '$lte',
        ];

        $filter = [];
        foreach ( $conds as $field => $value )
        {
            if ( !is_array( $value ) )
            {
                $filter[$field] = $value;
            }
            elseif ( isset( $value[0] ) && $value[0] === false && isset( $value[1] ) && is_array( $value[1] ) )
            {
                $filter[$field] = [ '$gte' => $value[1][0], '$lte' => $value[1][1] ];
            }
            elseif ( isset( $value[0] ) && is_array( $value[0] ) )
            {
                $filter[$field] = [ '$in' => $value[0] ];
            }
            elseif ( isset( $value[0] ) && is_string( $value[0] ) && array_key_exists( $value[0], $opMap ) )
            {
                $op = $opMap[$value[0]];
                $filter[$field] = $op === null ? $value[1] : [ $op => $value[1] ];
            }
            elseif ( isset( $value[0] ) && strtolower( $value[0] ) === 'like' )
            {
                $pattern = preg_quote( trim( $value[1], '%' ), '/' );
                $filter[$field] = [ '$regex' => $pattern, '$options' => 'i' ];
            }
            else
            {
                $filter[$field] = $value;
            }
        }
        return $filter;
    }

    /**
     * Insert a document into $table. If $doc does not contain the increment_key
     * field (or it is null/0), a new sequential ID is generated via nextSeqID().
     * Returns the inserted ID on success, false on failure.
     */
    function insert( $table, $doc )
    {
        $dbName = $this->DB;
        try {
            $doc = $this->applyAutoIncrement( $table, $doc );
            $this->getClient()->selectCollection( $dbName, $table )->insertOne( self::toBsonSafe( $doc ) );
            return true;
        } catch ( Exception $e ) {
            $this->logError( 'expMongoDB::insert ' . $table . ' ' . $e->getMessage() );
            return false;
        }
    }

    /**
     * Upsert: update matching $filter with $doc fields; insert if no match.
     */
    function upsert( $table, $filter, $doc )
    {
        $dbName = $this->DB;

        // A null in the filter matches no row, so the upsert would insert one
        // built from the filter - a document keyed on null, which nothing can
        // find again and which joins then multiply. Callers that meant an
        // UPDATE should use mongoUpdateMany().
        foreach ( $filter as $field => $value )
        {
            if ( $value === null )
            {
                $this->logError( 'expMongoDB::upsert ' . $table . ' refused, its filter has a null '
                    . $field . ', which would invent a row rather than update one' );
                return false;
            }
        }

        try {
            // Remove key fields from the $set payload to avoid immutable field errors
            $setDoc = self::toBsonSafe( array_diff_key( $doc, $filter ) );

            // Every field was part of the filter, so there is nothing to set
            // and the caller only wants the row to exist. MongoDB rejects
            // { $set: [] }, which is what this used to send.
            $update = $setDoc ? [ '$set' => $setDoc ] : [ '$setOnInsert' => $filter ];

            $this->getClient()->selectCollection( $dbName, $table )->updateOne(
                $filter,
                $update,
                [ 'upsert' => true ]
            );
            return true;
        } catch ( Exception $e ) {
            $this->logError( 'expMongoDB::upsert ' . $table . ' ' . $e->getMessage() );
            return false;
        }
    }

    /**
     * Return the next available sequential integer for $column in $table.
     * Uses a MongoDB $max aggregate to find the current maximum, then adds 1.
     */
    function nextSeqID( $table, $column )
    {
        $dbName = $this->DB;
        try {
            $cursor = $this->getClient()->selectCollection( $dbName, $table )->aggregate( [
                [ '$group' => [ '_id' => null, 'maxVal' => [ '$max' => '$' . $column ] ] ],
            ] );
            $rows = iterator_to_array( $cursor );
            $max  = ( !empty( $rows ) && isset( $rows[0]['maxVal'] ) ) ? (int) $rows[0]['maxVal'] : 0;
            return $max + 1;
        } catch ( Exception $e ) {
            $this->logError( 'expMongoDB::nextSeqID ' . $table . '.' . $column . ' ' . $e->getMessage() );
            return 1;
        }
    }

    function lock( $table ) {}
    function unlock() {}

    /**
     * No-op: MongoDB does not require an explicit BEGIN statement.
     * The base class eZDBInterface::begin() manages the transaction counter
     * and calls this method when it actually needs to start a transaction.
     */
    function beginQuery()
    {
        return true;
    }

    /**
     * No-op: MongoDB auto-commits single operations.
     */
    function commitQuery()
    {
        return true;
    }

    /**
     * No-op: nothing to roll back in auto-commit mode.
     */
    function rollbackQuery()
    {
        return true;
    }

    function deleteWhere( $table, $filter )
    {
        $dbName = $this->DB;
        try {
            $this->getClient()->selectCollection( $dbName, $table )->deleteMany( $filter );
            return true;
        } catch ( Exception $e ) {
            $this->logError( 'expMongoDB::deleteWhere ' . $table . ' ' . $e->getMessage() );
            return false;
        }
    }

    function eZTableList( $server = eZDBInterface::SERVER_MASTER )
    {
        $tables = array();
        foreach ( $this->listCollectionNames() as $name )
        {
            if ( strncmp( $name, 'ez', 2 ) === 0 )
                $tables[$name] = eZDBInterface::RELATION_TABLE;
        }
        return $tables;
    }

    /**
     * Returns a plain array of collection names present in the MongoDB database.
     * Used by setup/systemupgrade.php to validate expected collections exist.
     */
    function listCollectionNames()
    {
        $dbName = $this->DB;
        $names  = [];
        try {
            foreach ( $this->getClient()->selectDatabase( $dbName )->listCollectionNames() as $name )
                $names[] = $name;
        } catch ( Exception $e ) {
            $this->logError( 'expMongoDB::listCollectionNames ' . $e->getMessage() );
        }
        return $names;
    }

    /**
     * The column MySQL would fill in for this collection: the single field of
     * its PRIMARY index.
     *
     * Read from the collection's own indexes rather than a list kept here, so
     * it follows the schema instead of drifting from it. A compound primary
     * key has no auto_increment column and returns false, as does a
     * collection with no PRIMARY index at all.
     */
    function autoIncrementField( $table )
    {
        if ( isset( $this->AutoIncrementFields[$table] ) )
            return $this->AutoIncrementFields[$table];

        $field = false;
        try
        {
            foreach ( $this->getClient()->selectCollection( $this->DB, $table )->listIndexes() as $index )
            {
                if ( $index->getName() !== 'PRIMARY' )
                    continue;
                $keys = array_keys( (array)$index->getKey() );
                if ( count( $keys ) === 1 )
                    $field = $keys[0];
                break;
            }
        }
        catch ( Exception $e )
        {
            // An absent collection simply has no key to fill.
            $field = false;
        }

        $this->AutoIncrementFields[$table] = $field;
        return $field;
    }

    /**
     * Fill a document's auto_increment key when the statement left it out,
     * and remember the value for lastSerialID().
     */
    function applyAutoIncrement( $table, array $document )
    {
        $field = $this->autoIncrementField( $table );
        if ( $field === false || ( array_key_exists( $field, $document ) && $document[$field] !== null ) )
            return $document;

        // nextSeqID() answers this with a $group taking the maximum over the
        // whole collection. That is a full scan for one id, and the cost grows
        // with the collection: indexing a single article wrote hundreds of
        // ezsearch_object_word_link rows, each one scanning a table already
        // tens of thousands of rows long. The sequence counter is O(1).
        $newID = $this->nextAtomicID( $table . '.' . $field, $table, $field );
        if ( !$newID )
            return $document;

        $document[$field] = (int)$newID;
        $this->_lastInsertedID = (int)$newID;
        $this->LastSerialIDs[$table . '.' . $field] = (int)$newID;
        return $document;
    }

    /**
     * Give a whole batch of documents their ids in one reservation.
     *
     * One document at a time means one round trip each, which is what made a
     * multi-row INSERT of several hundred rows slow even after the scan was
     * gone. A single $inc of the batch size reserves a contiguous block, and
     * the ids are handed out from it locally.
     *
     * @param string $table
     * @param array $documents
     * @return array the documents, with ids filled in
     */
    function applyAutoIncrementBatch( $table, array $documents )
    {
        $field = $this->autoIncrementField( $table );
        if ( $field === false || !$documents )
            return $documents;

        $needing = array();
        foreach ( $documents as $index => $document )
        {
            if ( !array_key_exists( $field, $document ) || $document[$field] === null )
                $needing[] = $index;
        }

        if ( !$needing )
            return $documents;

        // One id is the ordinary case and the counter already handles it.
        if ( count( $needing ) === 1 )
        {
            $index = $needing[0];
            $documents[$index] = $this->applyAutoIncrement( $table, $documents[$index] );
            return $documents;
        }

        $last = $this->reserveAtomicIDs( $table . '.' . $field, count( $needing ), $table, $field );
        if ( !$last )
        {
            // Fall back to one at a time rather than write rows with no id.
            foreach ( $needing as $index )
                $documents[$index] = $this->applyAutoIncrement( $table, $documents[$index] );
            return $documents;
        }

        $next = $last - count( $needing ) + 1;
        foreach ( $needing as $index )
        {
            $documents[$index][$field] = (int)$next;
            $this->_lastInsertedID = (int)$next;
            $this->LastSerialIDs[$table . '.' . $field] = (int)$next;
            $next++;
        }

        return $documents;
    }

    /**
     * Reserve $count consecutive sequence values and return the last of them.
     * Seeds the counter the same way nextAtomicID() does.
     */
    function reserveAtomicIDs( $counterName, $count, $seedTable = null, $seedColumn = 'id' )
    {
        $count = (int)$count;
        if ( $count < 1 )
            return false;

        try
        {
            $collection = $this->getClient()->selectCollection( $this->DB, 'ezsequence' );

            if ( $seedTable !== null && $collection->findOne( array( '_id' => $counterName ) ) === null )
            {
                $cursor = $this->getClient()->selectCollection( $this->DB, $seedTable )->aggregate( array(
                    array( '$group' => array( '_id' => null, 'maxVal' => array( '$max' => '$' . $seedColumn ) ) ),
                ) );
                $rows = iterator_to_array( $cursor );
                $seed = ( !empty( $rows ) && isset( $rows[0]['maxVal'] ) ) ? (int)$rows[0]['maxVal'] : 0;
                $collection->updateOne( array( '_id' => $counterName ),
                    array( '$setOnInsert' => array( 'seq' => $seed ) ), array( 'upsert' => true ) );
            }

            $result = $collection->findOneAndUpdate(
                array( '_id' => $counterName ),
                array( '$inc' => array( 'seq' => $count ) ),
                array( 'upsert' => true,
                       'returnDocument' => \MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER ) );

            return isset( $result['seq'] ) ? (int)$result['seq'] : false;
        }
        catch ( Exception $e )
        {
            $this->logError( 'expMongoDB::reserveAtomicIDs ' . $counterName . ' ' . $e->getMessage() );
            return false;
        }
    }

    function lastSerialID( $table = false, $column = false )
    {
        if ( $table !== false && $column !== false
            && isset( $this->LastSerialIDs[$table . '.' . $column] ) )
        {
            return $this->LastSerialIDs[$table . '.' . $column];
        }

        return $this->_lastInsertedID;
    }

    private $_lastInsertedID = false;

    /**
     * Atomically increment and return a sequential integer counter for $counterName.
     * Uses a dedicated 'ezsequence' collection with {_id: $counterName, seq: N}.
     * Seeds from the current max of $seedTable.$seedColumn on first use.
     */
    function nextAtomicID( $counterName, $seedTable = null, $seedColumn = 'id' )
    {
        $dbName = $this->DB;
        try {
            $col = $this->getClient()->selectCollection( $dbName, 'ezsequence' );
            // If counter does not exist yet, seed it from the current max in the real collection
            $existing = $col->findOne( [ '_id' => $counterName ] );
            if ( $existing === null && $seedTable !== null ) {
                $cursor = $this->getClient()->selectCollection( $dbName, $seedTable )->aggregate( [
                    [ '$group' => [ '_id' => null, 'maxVal' => [ '$max' => '$' . $seedColumn ] ] ],
                ] );
                $rows   = iterator_to_array( $cursor );
                $seed   = ( !empty( $rows ) && isset( $rows[0]['maxVal'] ) ) ? (int)$rows[0]['maxVal'] : 0;
                $col->updateOne(
                    [ '_id' => $counterName ],
                    [ '$setOnInsert' => [ 'seq' => $seed ] ],
                    [ 'upsert' => true ]
                );
            }
            $result = $col->findOneAndUpdate(
                [ '_id' => $counterName ],
                [ '$inc' => [ 'seq' => 1 ] ],
                [ 'upsert' => true, 'returnDocument' => \MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER ]
            );
            return (int)( $result['seq'] ?? 1 );
        } catch ( \Exception $e ) {
            $this->logError( 'expMongoDB::nextAtomicID ' . $counterName . ' ' . $e->getMessage() );
            // Fallback: read max from actual table
            return $this->nextSeqID( $seedTable ?? $counterName, $seedColumn );
        }
    }

    function escapeString( $str )
    {
        // MongoDB queries use BSON — no SQL injection vector, but sanitise nulls
        return $str === null ? '' : (string)$str;
    }

    /**
     * Clears the error state. Called by the base class before every operation.
     */
    function setError( $connection = false )
    {
        $this->ErrorMessage = '';
        $this->ErrorNumber  = 0;
    }

    // -------------------------------------------------------------------------
    // Binding — MongoDB uses native PHP types, no binding needed
    // -------------------------------------------------------------------------

    function bindingType()
    {
        return eZDBInterface::BINDING_NO;
    }

    function bindVariable( $value, $fieldDef = false )
    {
        return $value;
    }

    // -------------------------------------------------------------------------
    // Charset — MongoDB stores UTF-8 natively
    // -------------------------------------------------------------------------

    function checkCharset( $charset, &$currentCharset )
    {
        return true;
    }

    function isCharsetSupported( $charset )
    {
        return true;
    }

    // -------------------------------------------------------------------------
    // SQL expression helpers — these return SQL-style expressions.
    // In practice they are never invoked via the MongoDB code path, but the
    // kernel may call them unconditionally, so we mirror the MySQL behaviour.
    // -------------------------------------------------------------------------

    function subString( $string, $from, $len = null )
    {
        return $len === null
            ? " substring( $string from $from ) "
            : " substring( $string from $from for $len ) ";
    }

    function concatString( $strings = array() )
    {
        return ' concat( ' . implode( ', ', $strings ) . ' ) ';
    }

    function md5( $str )
    {
        // The kernel hands this a quoted literal and puts the result straight
        // into a WHERE. Returning SQL text meant nothing could read it back,
        // so a literal is hashed here and only a column reference is left as
        // SQL for the engines that can evaluate it.
        if ( preg_match( "/^\s*'(.*)'\s*$/s", $str, $m ) )
            return "'" . md5( stripslashes( $m[1] ) ) . "'";

        return " MD5( $str ) ";
    }

    // The kernel asks the driver to spell a bitwise operation in its own
    // dialect. These were copied from the MySQL driver and returned
    // "cast( x & y AS SIGNED )", which this driver then could not read back;
    // a plain parenthesised expression is what its own SET parser evaluates.
    function bitAnd( $arg1, $arg2 )
    {
        return ' ( ' . $arg1 . ' & ' . $arg2 . ' ) ';
    }

    function bitOr( $arg1, $arg2 )
    {
        return ' ( ' . $arg1 . ' | ' . $arg2 . ' ) ';
    }

    /**
     * Update a single document in $table matching $filter with raw MongoDB $update operators.
     */
    function mongoUpdateOne( $table, $filter, $update )
    {
        $dbName = $this->DB;
        try {
            $this->getClient()->selectCollection( $dbName, $table )->updateOne( $filter, $update );
            return true;
        } catch ( \Exception $e ) {
            $this->logError( 'expMongoDB::mongoUpdateOne ' . $table . ' ' . $e->getMessage() );
            return false;
        }
    }

    /**
     * Update all documents in $table matching $filter with raw MongoDB $update operators.
     */
    function mongoUpdateMany( $table, $filter, $update )
    {
        $dbName = $this->DB;
        try {
            $this->getClient()->selectCollection( $dbName, $table )->updateMany( $filter, $update );
            return true;
        } catch ( \Exception $e ) {
            $this->logError( 'expMongoDB::mongoUpdateMany ' . $table . ' ' . $e->getMessage() );
            return false;
        }
    }

    /**
     * Delete a single document from $table matching $filter.
     */
    function mongoDeleteOne( $table, $filter )
    {
        $dbName = $this->DB;
        try {
            $this->getClient()->selectCollection( $dbName, $table )->deleteOne( $filter );
            return true;
        } catch ( \Exception $e ) {
            $this->logError( 'expMongoDB::mongoDeleteOne ' . $table . ' ' . $e->getMessage() );
            return false;
        }
    }

    // =========================================================================
    // Relation / schema introspection
    // =========================================================================

    function supportedRelationTypeMask()
    {
        return eZDBInterface::RELATION_TABLE_BIT;
    }

    function supportedRelationTypes()
    {
        return array( eZDBInterface::RELATION_TABLE );
    }

    function relationCounts( $relationMask )
    {
        if ( $relationMask & eZDBInterface::RELATION_TABLE_BIT )
            return $this->relationCount( eZDBInterface::RELATION_TABLE );
        return 0;
    }

    function relationCount( $relationType = eZDBInterface::RELATION_TABLE )
    {
        if ( $relationType !== eZDBInterface::RELATION_TABLE )
            return 0;
        return count( $this->listCollectionNames() );
    }

    function relationList( $relationType = eZDBInterface::RELATION_TABLE )
    {
        if ( $relationType !== eZDBInterface::RELATION_TABLE )
            return array();
        return $this->listCollectionNames();
    }

    function removeRelation( $relationName, $relationType )
    {
        if ( $relationType !== eZDBInterface::RELATION_TABLE )
            return false;
        $dbName = $this->DB;
        try {
            $this->getClient()->selectCollection( $dbName, $relationName )->drop();
            return true;
        } catch ( \Exception $e ) {
            $this->logError( 'expMongoDB::removeRelation ' . $relationName . ' ' . $e->getMessage() );
            return false;
        }
    }

    function relationMatchRegexp( $relationType )
    {
        return '#^ez#';
    }

    // =========================================================================
    // Version and server information
    // =========================================================================

    function databaseServerVersion()
    {
        try {
            $result = $this->getClient()->selectDatabase( $this->DB )->command( array( 'buildInfo' => 1 ) );
            $info   = current( iterator_to_array( $result ) );
            $versionString = isset( $info['version'] ) ? (string)$info['version'] : '0.0.0';
            return array( 'string' => $versionString,
                          'values' => explode( '.', $versionString ) );
        } catch ( \Exception $e ) {
            return array( 'string' => '0.0.0', 'values' => array( '0', '0', '0' ) );
        }
    }

    function databaseClientVersion()
    {
        // MongoDB PHP library version via Composer metadata
        $composerJson = dirname( __FILE__ ) . '/../../../../vendor/mongodb/mongodb/composer.json';
        $versionString = '0.0.0';
        if ( file_exists( $composerJson ) )
        {
            $json = json_decode( file_get_contents( $composerJson ), true );
            if ( isset( $json['version'] ) )
                $versionString = $json['version'];
        }
        return array( 'string' => $versionString,
                      'values' => explode( '.', $versionString ) );
    }

    function version()
    {
        $info = $this->databaseServerVersion();
        return $info['string'];
    }

    function availableDatabases()
    {
        $names = array();
        try {
            foreach ( $this->getClient()->listDatabaseNames() as $name )
                $names[] = $name;
        } catch ( \Exception $e ) {
            $this->logError( 'expMongoDB::availableDatabases ' . $e->getMessage() );
            return null;
        }
        return $names ?: false;
    }

    // =========================================================================
    // Database lifecycle
    // =========================================================================

    /**
     * MongoDB creates databases on first write — this is a no-op.
     */
    function createDatabase( $dbName )
    {
        // No explicit CREATE DATABASE in MongoDB; the DB is created on first insert.
    }

    /**
     * Drops the named MongoDB database.
     */
    function removeDatabase( $dbName )
    {
        try {
            $this->getClient()->selectDatabase( $dbName )->drop();
        } catch ( \Exception $e ) {
            $this->logError( 'expMongoDB::removeDatabase ' . $dbName . ' ' . $e->getMessage() );
        }
    }

    /**
     * MongoDB has no concept of temporary tables — silently ignore.
     */
    function createTempTable( $createTableQuery = '', $server = self::SERVER_SLAVE )
    {
        // no-op
    }

    /**
     * MongoDB has no concept of temporary tables — silently ignore.
     */
    function dropTempTable( $dropTableQuery = '', $server = self::SERVER_SLAVE )
    {
        // no-op
    }

    /**
     * Does this SET value look like an integer expression over the row's own
     * columns rather than a literal?
     *
     * The kernel writes raw SQL for the language masks - "language_id & ~1",
     * "( language_id & 1 ) | 2", and bitand( language_id, -2 ) on Oracle - and
     * expects the database to evaluate it. Only bitwise and simple arithmetic
     * over bare identifiers and integers counts; anything else is refused by
     * the caller rather than stored as text.
     */
    /**
     * The aggregation operator for each SQL comparison, used when the left
     * side of a comparison is an expression rather than a column.
     */
    static $ComparisonOperators = array(
        '='  => '$eq',
        '!=' => '$ne',
        '<'  => '$lt',
        '<=' => '$lte',
        '>'  => '$gt',
        '>=' => '$gte',
    );

    /**
     * Make a value safe to store as BSON.
     *
     * BSON strings must be valid UTF-8 and MongoDB rejects the whole write
     * when they are not. Some of the seeded content carries latin-1 bytes in
     * its sort keys - "\xdcber uns" - which MySQL stored happily and which
     * cost the attribute its value here. Those bytes are converted rather
     * than stripped, so the text survives as "Ueber uns" spelt properly.
     */
    static function toBsonSafe( $value )
    {
        if ( is_array( $value ) )
        {
            foreach ( $value as $key => $item )
                $value[$key] = self::toBsonSafe( $item );
            return $value;
        }

        if ( is_string( $value ) && !mb_check_encoding( $value, 'UTF-8' ) )
            return mb_convert_encoding( $value, 'UTF-8', 'ISO-8859-1' );

        return $value;
    }

    static function looksLikeIntExpression( $value )
    {
        $value = trim( (string)$value );
        if ( $value === '' )
            return false;

        // It has to contain an operator or a bitand() call to be an expression
        // at all, and must not contain a quoted string.
        if ( strpos( $value, "'" ) !== false || strpos( $value, '"' ) !== false )
            return false;
        if ( !preg_match( '/[&|~^]|bitand\s*\(/i', $value ) )
            return false;

        // Every token must be an identifier, an integer, an operator or a bracket.
        $stripped = preg_replace( '/\b(bitand|cast|as|signed|unsigned|integer)\b/i', '', $value );
        return (bool)preg_match( '/^[\s\w()&|~^+\-,]+$/', $stripped );
    }

    /**
     * Evaluate an integer expression against one document.
     *
     * Identifiers resolve to that document's fields; a field that is absent or
     * non-numeric counts as 0, which is how the database treats NULL in these
     * masks. Returns false when the expression cannot be parsed, so the caller
     * can refuse the write rather than guess.
     */
    static function evaluateIntExpression( $expression, $document )
    {
        // A cursor yields MongoDB\Model\BSONDocument, which is an ArrayObject
        // rather than an array; the parser below wants plain keys.
        if ( $document instanceof ArrayObject )
            $document = $document->getArrayCopy();
        elseif ( is_object( $document ) )
            $document = get_object_vars( $document );
        if ( !is_array( $document ) )
            return false;

        $normalised = (string)$expression;

        // cast( expr AS SIGNED ) is MySQL's wrapper; the cast is irrelevant here
        // because the result is used as an integer either way.
        $guardCast = 0;
        while ( preg_match( '/\bcast\s*\((.+?)\s+AS\s+\w+\s*\)/is', $normalised, $cm ) && $guardCast++ < 20 )
        {
            $normalised = str_replace( $cm[0], '(' . $cm[1] . ')', $normalised );
        }

        // bitand( a, b ) is Oracle's spelling of a & b.
        $guard = 0;
        while ( preg_match( '/bitand\s*\(([^(),]+),([^(),]+)\)/i', $normalised, $m ) && $guard++ < 20 )
        {
            $normalised = str_replace( $m[0], '(' . $m[1] . ' & ' . $m[2] . ')', $normalised );
        }

        $tokens = array();
        if ( !preg_match_all( '/\s*(\d+|[A-Za-z_][A-Za-z0-9_]*|[&|~^()+\-])/', $normalised, $matches, PREG_SET_ORDER ) )
            return false;
        foreach ( $matches as $match )
            $tokens[] = $match[1];

        // Reject anything the tokeniser did not consume entirely.
        if ( preg_replace( '/\s+/', '', implode( '', $tokens ) ) !== preg_replace( '/\s+/', '', $normalised ) )
            return false;

        $position = 0;
        $value = self::parseBitOr( $tokens, $position, $document );
        if ( $value === false || $position !== count( $tokens ) )
            return false;

        return (int)$value;
    }

    // A precedence ladder matching SQL and C: | then ^ then & then + - then unary.
    protected static function parseBitOr( array $tokens, &$i, array $doc )
    {
        $left = self::parseBitXor( $tokens, $i, $doc );
        if ( $left === false ) return false;
        while ( $i < count( $tokens ) && $tokens[$i] === '|' )
        {
            $i++;
            $right = self::parseBitXor( $tokens, $i, $doc );
            if ( $right === false ) return false;
            $left = $left | $right;
        }
        return $left;
    }

    protected static function parseBitXor( array $tokens, &$i, array $doc )
    {
        $left = self::parseBitAnd( $tokens, $i, $doc );
        if ( $left === false ) return false;
        while ( $i < count( $tokens ) && $tokens[$i] === '^' )
        {
            $i++;
            $right = self::parseBitAnd( $tokens, $i, $doc );
            if ( $right === false ) return false;
            $left = $left ^ $right;
        }
        return $left;
    }

    protected static function parseBitAnd( array $tokens, &$i, array $doc )
    {
        $left = self::parseAdditive( $tokens, $i, $doc );
        if ( $left === false ) return false;
        while ( $i < count( $tokens ) && $tokens[$i] === '&' )
        {
            $i++;
            $right = self::parseAdditive( $tokens, $i, $doc );
            if ( $right === false ) return false;
            $left = $left & $right;
        }
        return $left;
    }

    protected static function parseAdditive( array $tokens, &$i, array $doc )
    {
        $left = self::parseUnary( $tokens, $i, $doc );
        if ( $left === false ) return false;
        while ( $i < count( $tokens ) && ( $tokens[$i] === '+' || $tokens[$i] === '-' ) )
        {
            $op = $tokens[$i++];
            $right = self::parseUnary( $tokens, $i, $doc );
            if ( $right === false ) return false;
            $left = $op === '+' ? $left + $right : $left - $right;
        }
        return $left;
    }

    protected static function parseUnary( array $tokens, &$i, array $doc )
    {
        if ( $i >= count( $tokens ) )
            return false;

        $token = $tokens[$i];

        if ( $token === '~' )
        {
            $i++;
            $operand = self::parseUnary( $tokens, $i, $doc );
            return $operand === false ? false : ~$operand;
        }
        if ( $token === '-' )
        {
            $i++;
            $operand = self::parseUnary( $tokens, $i, $doc );
            return $operand === false ? false : -$operand;
        }
        if ( $token === '(' )
        {
            $i++;
            $inner = self::parseBitOr( $tokens, $i, $doc );
            if ( $inner === false || $i >= count( $tokens ) || $tokens[$i] !== ')' )
                return false;
            $i++;
            return $inner;
        }
        if ( preg_match( '/^\d+$/', $token ) )
        {
            $i++;
            return (int)$token;
        }
        if ( preg_match( '/^[A-Za-z_][A-Za-z0-9_]*$/', $token ) )
        {
            $i++;
            // A column of this row. Absent or non-numeric behaves as 0, the way
            // the database treats NULL in these masks.
            if ( !array_key_exists( $token, $doc ) )
                return 0;
            $fieldValue = $doc[$token];
            return is_numeric( $fieldValue ) ? (int)$fieldValue : 0;
        }

        return false;
    }

    /**
     * Read the index definitions out of a CREATE TABLE body.
     *
     * Only the parts MongoDB can act on are taken: the key clauses. Column
     * definitions and storage options carry nothing a collection can hold, so
     * they are skipped rather than translated.
     *
     * The names mirror MySQL's, because autoIncrementField() looks for an
     * index called PRIMARY and expMongoSchema writes the same names when it
     * builds collections from a .dba.
     *
     * @param string $body the text between the outer brackets
     * @return array indexName => array( 'fields' => array, 'unique' => bool )
     */
    static function parseCreateTableIndexes( $body )
    {
        $indexes = array();
        $unnamed = 0;

        foreach ( self::splitSqlList( $body ) as $clause )
        {
            $clause = trim( $clause );

            if ( preg_match( '/^PRIMARY\s+KEY\s*\((.+?)\)/is', $clause, $m ) )
            {
                $indexes['PRIMARY'] = array( 'fields' => self::parseIndexFieldList( $m[1] ), 'unique' => true );
                continue;
            }

            if ( preg_match( '/^(UNIQUE)\s+(?:KEY|INDEX)\s*[`"]?(\w*)[`"]?\s*\((.+?)\)/is', $clause, $m )
              || preg_match( '/^()(?:KEY|INDEX)\s*[`"]?(\w*)[`"]?\s*\((.+?)\)/is', $clause, $m ) )
            {
                $name = $m[2] !== '' ? $m[2] : 'idx_' . ( ++$unnamed );
                $indexes[$name] = array(
                    'fields' => self::parseIndexFieldList( $m[3] ),
                    'unique' => strtoupper( $m[1] ) === 'UNIQUE',
                );
                continue;
            }

            // A column definition carrying its own UNIQUE marker.
            if ( preg_match( '/^[`"]?(\w+)[`"]?\s+[\w()]+.*\bUNIQUE\b/is', $clause, $m ) )
                $indexes[$m[1]] = array( 'fields' => array( $m[1] ), 'unique' => true );
        }

        return $indexes;
    }

    /**
     * Turn "`a`, b(64), c" from an index clause into plain field names.
     * A prefix length is a MySQL storage detail with no MongoDB equivalent.
     */
    static function parseIndexFieldList( $text )
    {
        $fields = array();
        foreach ( explode( ',', $text ) as $field )
        {
            $field = trim( $field );
            $field = preg_replace( '/\(\s*\d+\s*\)\s*$/', '', $field );
            $field = trim( $field, " \t`\"[]" );
            if ( $field !== '' )
                $fields[] = $field;
        }
        return $fields;
    }

    /**
     * Split "( a, b ), ( c, d )" into the text of each tuple, respecting
     * quotes so a bracket or comma inside a string does not end one early.
     */
    static function splitSqlTuples( $text )
    {
        $tuples = array();
        $depth = 0;
        $inQuote = false;
        $current = '';
        $length = strlen( $text );

        for ( $i = 0; $i < $length; $i++ )
        {
            $char = $text[$i];

            if ( $inQuote )
            {
                if ( $char === '\\' && $i + 1 < $length )
                {
                    $current .= $char . $text[++$i];
                    continue;
                }
                if ( $char === "'" )
                {
                    // A doubled quote is an escaped one, not the end.
                    if ( $i + 1 < $length && $text[$i + 1] === "'" )
                    {
                        $current .= "''";
                        $i++;
                        continue;
                    }
                    $inQuote = false;
                }
                $current .= $char;
                continue;
            }

            if ( $char === "'" )
            {
                $inQuote = true;
                $current .= $char;
                continue;
            }
            if ( $char === '(' )
            {
                $depth++;
                if ( $depth === 1 )
                    continue;   // the tuple's own opening bracket
            }
            elseif ( $char === ')' )
            {
                $depth--;
                if ( $depth === 0 )
                {
                    $tuples[] = $current;
                    $current = '';
                    continue;
                }
            }

            if ( $depth >= 1 )
                $current .= $char;
        }

        return $tuples;
    }

    /**
     * Split one tuple's text on top-level commas, keeping quoted commas.
     */
    static function splitSqlList( $text )
    {
        $values = array();
        $inQuote = false;
        $depth = 0;
        $current = '';
        $length = strlen( $text );

        for ( $i = 0; $i < $length; $i++ )
        {
            $char = $text[$i];

            if ( $inQuote )
            {
                if ( $char === '\\' && $i + 1 < $length )
                {
                    $current .= $char . $text[++$i];
                    continue;
                }
                if ( $char === "'" )
                {
                    if ( $i + 1 < $length && $text[$i + 1] === "'" )
                    {
                        $current .= "''";
                        $i++;
                        continue;
                    }
                    $inQuote = false;
                }
                $current .= $char;
                continue;
            }

            if ( $char === "'" )
            {
                $inQuote = true;
                $current .= $char;
                continue;
            }
            if ( $char === '(' ) $depth++;
            if ( $char === ')' ) $depth--;

            if ( $char === ',' && $depth === 0 )
            {
                $values[] = trim( $current );
                $current = '';
                continue;
            }
            $current .= $char;
        }

        if ( trim( $current ) !== '' )
            $values[] = trim( $current );

        return $values;
    }

    /**
     * Turn one SQL literal into the PHP value it should be stored as, so an
     * integer column does not arrive in the document as a string.
     */
    /**
     * The numeric columns of a table, taken from the shipped .dba schema.
     *
     * MongoDB stores whatever type it is handed, and the two halves of this
     * driver disagreed about a quoted number: an INSERT wrote '1' as the
     * string "1", while a WHERE looked for the integer 1. A row written by
     * raw SQL could therefore never be found by raw SQL again.
     *
     * The consequences were not limited to lookups. MongoDB's $inc refuses a
     * string, so "SET object_count = object_count + 1" failed on every word
     * the search indexer touched, and the index stayed at fourteen objects out
     * of two hundred and eighty four.
     *
     * Guessing from the literal is not good enough - a digits-only value in a
     * genuinely textual column, a remote_id for instance, would be turned into
     * a number and stop matching what eZPersistentObject writes there. The
     * .dba is the same description the collections were built from, so it is
     * what decides the type.
     *
     * @param string $table
     * @return array column => 'int'|'float'
     */
    static function numericColumns( $table )
    {
        if ( self::$NumericColumns === null )
            self::$NumericColumns = self::loadNumericColumns();

        return isset( self::$NumericColumns[$table] ) ? self::$NumericColumns[$table] : array();
    }

    static protected $NumericColumns = null;

    /**
     * Read every .dba the installation ships and note the numeric columns.
     * Done once per request; the files are plain PHP and stay in the opcode
     * cache.
     */
    static protected function loadNumericColumns()
    {
        $map = array();

        $root = class_exists( 'eZSys' ) ? eZSys::rootDir() : '.';
        $files = array( $root . '/share/db_schema.dba' );

        if ( class_exists( 'eZExtension' ) )
        {
            foreach ( (array) eZExtension::activeExtensions() as $extension )
                $files[] = $root . '/extension/' . $extension . '/share/db_schema.dba';
        }

        $integerTypes = array( 'int', 'integer', 'bigint', 'smallint', 'tinyint', 'mediumint' );
        $floatTypes   = array( 'float', 'double', 'decimal', 'numeric' );

        foreach ( $files as $file )
        {
            if ( !is_readable( $file ) )
                continue;

            $schema = eZDbSchema::readArray( $file );
            if ( !is_array( $schema ) )
                continue;

            foreach ( $schema as $tableName => $definition )
            {
                if ( !is_array( $definition ) || !isset( $definition['fields'] )
                  || !is_array( $definition['fields'] ) )
                    continue;

                foreach ( $definition['fields'] as $column => $field )
                {
                    $type = isset( $field['type'] ) ? strtolower( (string) $field['type'] ) : '';
                    if ( in_array( $type, $integerTypes, true ) )
                        $map[$tableName][$column] = 'int';
                    elseif ( in_array( $type, $floatTypes, true ) )
                        $map[$tableName][$column] = 'float';
                }
            }
        }

        return $map;
    }

    /**
     * castSqlLiteral(), with the column's declared type applied.
     *
     * Use this wherever a value is written, so what goes into a document is
     * the type the schema says the column holds rather than the type the SQL
     * happened to quote it as.
     */
    static function castSqlLiteralForColumn( $literal, $table, $column )
    {
        $value = self::castSqlLiteral( $literal );

        if ( !is_string( $value ) || $value === '' )
            return $value;

        $numeric = self::numericColumns( $table );
        if ( !isset( $numeric[$column] ) )
            return $value;

        if ( $numeric[$column] === 'int' && preg_match( '/^-?\d+$/', $value ) )
            return (int) $value;
        if ( $numeric[$column] === 'float' && is_numeric( $value ) )
            return (float) $value;

        return $value;
    }

    static function castSqlLiteral( $literal )
    {
        $literal = trim( (string)$literal );

        if ( $literal === '' )
            return '';
        if ( strcasecmp( $literal, 'NULL' ) === 0 )
            return null;
        if ( preg_match( "/^'(.*)'$/s", $literal, $m ) )
        {
            $value = str_replace( "''", "'", $m[1] );
            return stripslashes( $value );
        }
        if ( preg_match( '/^-?\d+$/', $literal ) )
            return (int)$literal;
        if ( is_numeric( $literal ) )
            return (float)$literal;

        return $literal;
    }

    function close() {}
}
