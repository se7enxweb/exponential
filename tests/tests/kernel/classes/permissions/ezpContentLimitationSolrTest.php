<?php
/**
 * The search filter of a content policy limitation of an extension, without the database:
 * ezpContentLimitation::solrFilter(), for search engines that filter by the policies of the user themselves (eZ Find).
 *
 *  SL-01 - Without a handler, or with one that does not implement ezpContentLimitationSolrHandler, the policy gives no
 *          access in searches: the filter matches nothing (DENY_SOLR), it is never false
 *  SL-02 - The filter of a handler is put in parentheses; it gets the limitation, its values as strings and the user
 *  SL-03 - False, an empty string, an exception or a filter that is not self-contained gives no access
 *  SL-04 - Field conditions: the kernel escapes the values, joins a list with AND, "not" and no values
 *  SL-05 - Field conditions with a field that is no name, a value that is no scalar or no UTF-8, or a "not" that is
 *          no boolean give no access
 *  SL-06 - solrValue() escapes every character the query parser gives a meaning, and the operators AND, OR, NOT
 *  SL-07 - isSelfContainedSolr() accepts what stays in its parentheses and nothing else
 *  SL-08 - A kernel limitation is never asked of a handler: the search engine translates it, here it denies
 *  SL-09 - Allow/deny matrix: policies OR'd, limitations AND'd as eZ Find joins them, evaluated against documents;
 *          a limitation nobody evaluates removes its policy and never widens the search, not even when it is the
 *          user's only policy
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

/** A handler with a search filter */
class X1ContentLimitationSolrHandler implements ezpContentLimitationSolrHandler
{
    public static $filter = 'meta_section_id_si:5';
    public static $throw = false;
    public static $asked = array();

    public function checkAccess( $limitation, array $values, $functionName, $subject, $userID )
    {
        return true;
    }

    public function permissionSQL( $limitation, array $values, $tableAliasName, $userID )
    {
        return 'ezcontentobject.section_id = 5';
    }

    public function solrFilter( $limitation, array $values, $userID )
    {
        self::$asked[] = array( $limitation, $values, $userID );
        if ( self::$throw )
        {
            throw new RuntimeException( 'x1 solr filter failed' );
        }
        return self::$filter;
    }
}

/** "Own content only": the documents the user of the search owns */
class X1ContentLimitationSolrOwnHandler implements ezpContentLimitationSolrHandler
{
    public function checkAccess( $limitation, array $values, $functionName, $subject, $userID )
    {
        return true;
    }

    public function permissionSQL( $limitation, array $values, $tableAliasName, $userID )
    {
        return array( 'column' => 'ezcontentobject.owner_id', 'values' => array( $userID ) );
    }

    public function solrFilter( $limitation, array $values, $userID )
    {
        return array( 'field' => 'meta_owner_id_si', 'values' => array( (string)$userID ) );
    }
}

/** "Every class but these" */
class X1ContentLimitationSolrNotClassHandler implements ezpContentLimitationSolrHandler
{
    public function checkAccess( $limitation, array $values, $functionName, $subject, $userID )
    {
        return true;
    }

    public function permissionSQL( $limitation, array $values, $tableAliasName, $userID )
    {
        return false;
    }

    public function solrFilter( $limitation, array $values, $userID )
    {
        return array( 'field' => 'meta_class_identifier_ms', 'values' => $values, 'not' => true );
    }
}

/** A handler of the fetches only: it has no search filter */
class X1ContentLimitationSqlOnlyHandler implements ezpContentLimitationHandler
{
    public function checkAccess( $limitation, array $values, $functionName, $subject, $userID )
    {
        return true;
    }

    public function permissionSQL( $limitation, array $values, $tableAliasName, $userID )
    {
        return 'ezcontentobject.section_id = 5';
    }
}

/**
 * Evaluates the part of the Solr standard query syntax the kernel and eZ Find write (parentheses, AND, OR, "-",
 * *:*, field:value, field:(v1 OR v2), backslash escapes) against a document, so that a filter can be checked by
 * what it matches instead of by its text.
 */
class X1SolrFilterEvaluator
{
    public static function matches( $query, array $document )
    {
        $tokens = self::tokens( $query );
        $position = 0;
        $matcher = self::group( $tokens, $position, null );
        if ( $position !== count( $tokens ) )
        {
            throw new InvalidArgumentException( "Unbalanced query: $query" );
        }
        return $matcher( $document );
    }

    private static function tokens( $query )
    {
        $tokens = array();
        $term = '';
        $length = strlen( $query );
        for ( $i = 0; $i < $length; ++$i )
        {
            $char = $query[$i];
            if ( $char === '\\' )
            {
                $term .= $char . $query[++$i];
            }
            else if ( $char === ' ' || $char === '(' || $char === ')' )
            {
                if ( $term !== '' )
                    $tokens[] = $term;
                $term = '';
                if ( $char !== ' ' )
                    $tokens[] = $char;
            }
            else if ( $char === '-' && $term === '' )
            {
                $tokens[] = '-';
            }
            else
            {
                $term .= $char;
            }
        }
        if ( $term !== '' )
            $tokens[] = $term;
        return $tokens;
    }

    private static function group( array $tokens, &$position, $field )
    {
        $clauses = array();
        $and = false;
        while ( $position < count( $tokens ) && $tokens[$position] !== ')' )
        {
            $token = $tokens[$position];
            if ( $token === 'AND' || $token === 'OR' )
            {
                if ( $token === 'AND' && $clauses && $clauses[count( $clauses ) - 1][0] === 'should' )
                    $clauses[count( $clauses ) - 1][0] = 'must';
                $and = $token === 'AND';
                ++$position;
                continue;
            }
            $not = false;
            if ( $token === '-' )
            {
                $not = true;
                $token = $tokens[++$position];
            }
            if ( $token === '(' || ( substr( $token, -1 ) === ':' && isset( $tokens[$position + 1] ) && $tokens[$position + 1] === '(' ) )
            {
                $subField = $token === '(' ? $field : substr( $token, 0, -1 );
                $position += $token === '(' ? 1 : 2;
                $matcher = self::group( $tokens, $position, $subField );
                if ( !isset( $tokens[$position] ) || $tokens[$position] !== ')' )
                    throw new InvalidArgumentException( 'Unclosed parenthesis' );
                ++$position;
            }
            else
            {
                $matcher = self::term( $token, $field );
                ++$position;
            }
            $clauses[] = array( $not ? 'not' : ( $and ? 'must' : 'should' ), $matcher );
            $and = false;
        }
        return function ( $document ) use ( $clauses )
        {
            $must = $should = 0;
            $anyShould = false;
            foreach ( $clauses as $clause )
            {
                $hit = $clause[1]( $document );
                if ( $clause[0] === 'not' && $hit )
                    return false;
                if ( $clause[0] === 'must' )
                {
                    if ( !$hit )
                        return false;
                    ++$must;
                }
                if ( $clause[0] === 'should' )
                {
                    $anyShould = true;
                    $should += $hit ? 1 : 0;
                }
            }
            // a group of prohibited clauses alone matches nothing, as in Lucene
            return $must > 0 || ( $anyShould && $should > 0 );
        };
    }

    private static function term( $token, $field )
    {
        if ( $token === '*:*' )
        {
            return function () { return true; };
        }
        if ( preg_match( '/^([A-Za-z_][A-Za-z0-9_]*):(.+)$/s', $token, $match ) )
        {
            $field = $match[1];
            $token = $match[2];
        }
        if ( $field === null )
        {
            throw new InvalidArgumentException( "A term without a field: $token" );
        }
        $value = preg_replace( '/\\\\(.)/s', '$1', $token );
        return function ( $document ) use ( $field, $value )
        {
            return isset( $document[$field] ) && in_array( $value, (array)$document[$field], true );
        };
    }
}

class ezpContentLimitationSolrTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        ezpContentLimitation::resetCache();
        X1ContentLimitationSolrHandler::$filter = 'meta_section_id_si:5';
        X1ContentLimitationSolrHandler::$throw = false;
        X1ContentLimitationSolrHandler::$asked = array();
        ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'LimitationHandlers', array(
            'X1Solr' => 'X1ContentLimitationSolrHandler',
            'X1Own' => 'X1ContentLimitationSolrOwnHandler',
            'X1NotClass' => 'X1ContentLimitationSolrNotClassHandler',
            'X1SqlOnly' => 'X1ContentLimitationSqlOnlyHandler',
            // a kernel limitation never goes to a handler, even one registered for it
            'Section' => 'X1ContentLimitationSolrHandler',
        ) );
    }

    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        ezpContentLimitation::resetCache();
    }

    private function logged()
    {
        $logged = new ReflectionProperty( 'ezpContentLimitation', 'logged' );
        if ( PHP_VERSION_ID < 80100 )
        {
            $logged->setAccessible( true );
        }
        return implode( "\n", array_keys( $logged->getValue() ) );
    }

    /** SL-01 */
    public function testWithoutASearchFilterThePolicyGivesNoAccess()
    {
        $this->assertSame( '( *:* -*:* )', ezpContentLimitation::DENY_SOLR );
        $this->assertSame( ezpContentLimitation::DENY_SOLR, ezpContentLimitation::solrFilter( 'X1Unregistered', array( '1' ), 14 ) );
        $this->assertSame( ezpContentLimitation::DENY_SOLR, ezpContentLimitation::solrFilter( 'X1SqlOnly', array( '1' ), 14 ) );
        $this->assertSame( ezpContentLimitation::DENY_SOLR, ezpContentLimitation::solrFilter( null, array( '1' ), 14 ) );
        $this->assertSame( ezpContentLimitation::DENY_SOLR, ezpContentLimitation::solrFilter( '', array( '1' ), 14 ) );
        $this->assertStringContainsString( 'does not implement ezpContentLimitationSolrHandler', $this->logged() );
        $this->assertStringContainsString( 'No handler is registered for the limitation X1Unregistered', $this->logged() );
    }

    /** SL-02 */
    public function testTheFilterOfAHandlerIsPutInParentheses()
    {
        $this->assertSame( '( meta_section_id_si:5 )', ezpContentLimitation::solrFilter( 'X1Solr', array( 5, '7', array( 9 ) ), 14 ) );
        $this->assertSame( array( array( 'X1Solr', array( '5', '7' ), 14 ) ), X1ContentLimitationSolrHandler::$asked );
    }

    /** SL-03 */
    public function testWhatIsNoFilterGivesNoAccess()
    {
        foreach ( array( false, '', '  ', 'a ) OR ( *:*', 'a:"b', '{!join from=x to=y}*:*', "a:1\0", 'a:1 \\', ')(', null, true, 5, array(),
                         'a:[1 TO ) OR ( *:* ]', '_query_:"{!lucene}*:*"' ) as $answer )
        {
            ezpContentLimitation::resetCache();
            X1ContentLimitationSolrHandler::$filter = $answer;
            $this->assertSame( ezpContentLimitation::DENY_SOLR, ezpContentLimitation::solrFilter( 'X1Solr', array( '1' ), 14 ), var_export( $answer, true ) );
        }

        ezpContentLimitation::resetCache();
        X1ContentLimitationSolrHandler::$throw = true;
        $this->assertSame( ezpContentLimitation::DENY_SOLR, ezpContentLimitation::solrFilter( 'X1Solr', array( '1' ), 14 ) );
        $this->assertStringContainsString( 'threw RuntimeException in solrFilter()', $this->logged() );
    }

    /** SL-04 */
    public function testFieldConditionsAreWrittenAndEscapedByTheKernel()
    {
        X1ContentLimitationSolrHandler::$filter = array( 'field' => 'meta_owner_id_si', 'values' => array( '14', '14', 'a b:(c)' ) );
        $this->assertSame( '( meta_owner_id_si:(14 OR a\\ b\\:\\(c\\)) )', ezpContentLimitation::solrFilter( 'X1Solr', array(), 14 ) );

        X1ContentLimitationSolrHandler::$filter = array(
            array( 'field' => 'meta_section_id_si', 'values' => array( '1', '2' ) ),
            array( 'field' => 'meta_class_identifier_ms', 'values' => array( 'folder' ), 'not' => true ),
        );
        $this->assertSame( '( meta_section_id_si:(1 OR 2) AND ( *:* -meta_class_identifier_ms:(folder) ) )',
                           ezpContentLimitation::solrFilter( 'X1Solr', array(), 14 ) );

        X1ContentLimitationSolrHandler::$filter = array( 'field' => 'meta_section_id_si', 'values' => array( '1' ), 'not' => false );
        $this->assertSame( '( meta_section_id_si:(1) )', ezpContentLimitation::solrFilter( 'X1Solr', array(), 14 ) );

        X1ContentLimitationSolrHandler::$filter = array( 'field' => 'meta_section_id_si', 'values' => array() );
        $this->assertSame( '( ' . ezpContentLimitation::DENY_SOLR . ' )', ezpContentLimitation::solrFilter( 'X1Solr', array(), 14 ) );

        X1ContentLimitationSolrHandler::$filter = array( 'field' => 'meta_section_id_si', 'values' => array(), 'not' => true );
        $this->assertSame( '( *:* )', ezpContentLimitation::solrFilter( 'X1Solr', array(), 14 ) );

        // a value that is an operator is a term
        X1ContentLimitationSolrHandler::$filter = array( 'field' => 'meta_name_t', 'values' => array( 'NOT', 'or' ) );
        $this->assertSame( '( meta_name_t:(\\NOT OR or) )', ezpContentLimitation::solrFilter( 'X1Solr', array(), 14 ) );
    }

    /** SL-05 */
    public function testFieldConditionsThatAreNotWellFormedGiveNoAccess()
    {
        foreach ( array(
            array( 'field' => 'meta_id:1 OR x', 'values' => array( '1' ) ),
            array( 'field' => 'meta_id_si', 'values' => array( array( 1 ) ) ),
            array( 'field' => 'meta_id_si', 'values' => array( '' ) ),
            array( 'field' => 'meta_id_si', 'values' => array( false ) ),
            array( 'field' => 'meta_id_si', 'values' => array( "a\xC3" ) ),
            array( 'field' => 'meta_id_si' ),
            array( 'field' => 'meta_id_si', 'values' => '1' ),
            // a "not" that is no boolean would turn "only 1" into "all but 1"
            array( 'field' => 'meta_id_si', 'values' => array( '1' ), 'not' => 'false' ),
            array( 'field' => 'meta_id_si', 'values' => array( '1' ), 'not' => 1 ),
            array( array( 'field' => 'meta_id_si', 'values' => array( '1' ) ), 'x' ),
        ) as $answer )
        {
            ezpContentLimitation::resetCache();
            X1ContentLimitationSolrHandler::$filter = $answer;
            $this->assertSame( ezpContentLimitation::DENY_SOLR, ezpContentLimitation::solrFilter( 'X1Solr', array(), 14 ), var_export( $answer, true ) );
        }
    }

    /** SL-06 */
    public function testSolrValueEscapesEveryCharacterThatMeansSomething()
    {
        $this->assertSame( 'plain_Text9', ezpContentLimitation::solrValue( 'plain_Text9' ) );
        $this->assertSame( '\\+\\-\\&\\&\\|\\|\\!\\(\\)\\{\\}\\[\\]\\^\\"\\~\\*\\?\\:\\\\\\/\\ ',
                           ezpContentLimitation::solrValue( '+-&&||!(){}[]^"~*?:\\/ ' ) );
        $this->assertSame( 'Gr\\ üße', ezpContentLimitation::solrValue( 'Gr üße' ) );
        $this->assertSame( '\\AND', ezpContentLimitation::solrValue( 'AND' ) );
        $this->assertSame( '\\OR', ezpContentLimitation::solrValue( 'OR' ) );
        $this->assertSame( '\\NOT', ezpContentLimitation::solrValue( 'NOT' ) );
        $this->assertSame( 'not', ezpContentLimitation::solrValue( 'not' ) );
        $this->assertSame( 'NOTE', ezpContentLimitation::solrValue( 'NOTE' ) );
        // not UTF-8: escaped byte by byte instead of becoming an empty string
        $this->assertSame( "a\\ \xC3\\)", ezpContentLimitation::solrValue( "a \xC3)" ) );
    }

    /** SL-07 */
    public function testIsSelfContainedSolr()
    {
        foreach ( array( 'a:1', '(a:1 OR b:2) AND c:"x ) y"', 'a:"q\\"uote"', 'a:\\(1\\)', '-a:1', 'a:[1 TO 5]', 'a:{1 TO *}',
                         'a:["x" TO "y]"]', '(a:[1 TO 5] OR b:2)', 'a:\\[1' ) as $filter )
            $this->assertTrue( ezpContentLimitation::isSelfContainedSolr( $filter ), $filter );
        foreach ( array( 'a:1)', '(a:1', 'a:"x', ') OR (', '{!lucene}a:1', 'a:1 {!join}', "a\0", 'a\\',
                         'a:[1 TO 5', 'a:1]', 'a:1}', 'a:[1 TO )]', 'a:[( TO 5]', 'a:[1 TO [5]]', 'a:"{!lucene}"',
                         '_query_:"a:1"', 'b:1 OR _QUERY_:"a:1"' ) as $filter )
            $this->assertFalse( ezpContentLimitation::isSelfContainedSolr( $filter ), $filter );
    }

    /** SL-08 */
    public function testAKernelLimitationIsNeverAskedOfAHandler()
    {
        foreach ( array( 'Section', 'Subtree', 'Owner', 'Language', 'StateGroup_ez_lock' ) as $limitation )
        {
            $this->assertSame( ezpContentLimitation::DENY_SOLR, ezpContentLimitation::solrFilter( $limitation, array( '1' ), 14 ), $limitation );
        }
        $this->assertSame( array(), X1ContentLimitationSolrHandler::$asked );
        $this->assertStringContainsString( 'which the kernel evaluates itself', $this->logged() );

        // a persistent worker (Velocity): the next request starts with no messages of the last one
        $hadTime = array_key_exists( 'REQUEST_TIME_FLOAT', $_SERVER );
        $time = $hadTime ? $_SERVER['REQUEST_TIME_FLOAT'] : null;
        try
        {
            ezpContentLimitation::resetCache();
            $_SERVER['REQUEST_TIME_FLOAT'] = 1000.25;
            ezpContentLimitation::solrFilter( 'Section', array( '1' ), 14 );
            $_SERVER['REQUEST_TIME_FLOAT'] = 2000.5;
            ezpContentLimitation::solrFilter( 'Subtree', array( '1' ), 14 );
            $this->assertStringNotContainsString( 'limitation Section', $this->logged() );
            $this->assertStringContainsString( 'limitation Subtree', $this->logged() );
        }
        finally
        {
            if ( $hadTime )
                $_SERVER['REQUEST_TIME_FLOAT'] = $time;
            else
                unset( $_SERVER['REQUEST_TIME_FLOAT'] );
        }
    }

    /**
     * The policy filter of a search, joined as eZ Find joins it: policies with OR, the limitations of a policy with
     * AND, the values of a kernel limitation with OR; the extension limitations from solrFilter(). Like eZ Find, it
     * filters by nothing when no policy gives a filter.
     */
    private function searchFilter( array $policies, $userID )
    {
        $fields = array( 'Section' => 'meta_section_id_si', 'Class' => 'meta_class_identifier_ms' );
        $filters = array();
        foreach ( $policies as $policy )
        {
            $limitations = array();
            foreach ( $policy as $limitation => $values )
            {
                if ( isset( $fields[$limitation] ) )
                {
                    $parts = array();
                    foreach ( $values as $value )
                        $parts[] = $fields[$limitation] . ':' . ezpContentLimitation::solrValue( $value );
                    $limitations[] = '( ' . implode( ' OR ', $parts ) . ' )';
                }
                else
                {
                    $limitations[] = ezpContentLimitation::solrFilter( $limitation, $values, $userID );
                }
            }
            if ( $limitations )
                $filters[] = '( ' . implode( ' AND ', $limitations ) . ' )';
        }
        return $filters ? implode( ' OR ', $filters ) : '*:*';
    }

    public static function providerMatrix()
    {
        return array(
            'own content in section 1' => array( array( array( 'Section' => array( '1' ), 'X1Own' => array() ) ), array( 'd1' ) ),
            'only policy, no handler' => array( array( array( 'Section' => array( '1' ), 'X1Unregistered' => array( 'x' ) ) ), array() ),
            'only policy, only limitation, no handler' => array( array( array( 'X1Unregistered' => array( 'x' ) ) ), array() ),
            'only policies, none with a search filter' => array( array( array( 'X1Unregistered' => array( 'x' ) ), array( 'X1SqlOnly' => array( 'y' ) ) ), array() ),
            'second policy without a search filter' => array( array( array( 'Section' => array( '2' ) ), array( 'Section' => array( '1' ), 'X1SqlOnly' => array( 'x' ) ) ), array( 'd2' ) ),
            'own content or section 3' => array( array( array( 'Section' => array( '1' ), 'X1Own' => array() ), array( 'Section' => array( '3' ) ) ), array( 'd1', 'd4' ) ),
            'all classes but folder' => array( array( array( 'X1NotClass' => array( 'folder' ) ) ), array( 'd2', 'd3', 'd5' ) ),
            'all classes but folder, own' => array( array( array( 'X1NotClass' => array( 'folder' ), 'X1Own' => array() ) ), array( 'd2' ) ),
            'filter that leaves its parentheses' => array( array( array( 'Section' => array( '1', '3' ), 'X1Solr' => array( 'x' ) ) ), array(), 'meta_section_id_si:1 ) OR ( *:*' ),
            'handler that throws, next policy' => array( array( array( 'Section' => array( '2' ) ), array( 'Section' => array( '1' ), 'X1Solr' => array( 'x' ) ) ), array( 'd2' ), null, true ),
            'kernel limitation routed to the handlers' => array( array( array( 'Section' => array( '1' ) ), array( 'Section' => array( '3' ), 'Language' => array( 'eng-GB' ) ) ), array( 'd1', 'd3' ) ),
            'value that is an operator' => array( array( array( 'X1Solr' => array( 'x' ) ) ), array( 'd5' ), array( 'field' => 'meta_name_ms', 'values' => array( 'NOT' ) ) ),
        );
    }

    /**
     * SL-09
     *
     * @dataProvider providerMatrix
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'providerMatrix' )]
    public function testAllowDenyMatrix( array $policies, array $expected, $solrAnswer = null, $throw = false )
    {
        if ( $solrAnswer !== null )
            X1ContentLimitationSolrHandler::$filter = $solrAnswer;
        X1ContentLimitationSolrHandler::$throw = $throw;
        $documents = array(
            'd1' => array( 'meta_section_id_si' => '1', 'meta_class_identifier_ms' => array( 'folder' ), 'meta_owner_id_si' => '14' ),
            'd2' => array( 'meta_section_id_si' => '2', 'meta_class_identifier_ms' => array( 'article' ), 'meta_owner_id_si' => '14' ),
            'd3' => array( 'meta_section_id_si' => '1', 'meta_class_identifier_ms' => array( 'article' ), 'meta_owner_id_si' => '10' ),
            'd4' => array( 'meta_section_id_si' => '3', 'meta_class_identifier_ms' => array( 'folder' ), 'meta_owner_id_si' => '10' ),
            'd5' => array( 'meta_section_id_si' => '4', 'meta_class_identifier_ms' => array( 'image' ), 'meta_owner_id_si' => '10',
                           'meta_name_ms' => array( 'NOT' ) ),
        );
        $filter = $this->searchFilter( $policies, 14 );
        $found = array();
        foreach ( $documents as $id => $document )
        {
            if ( X1SolrFilterEvaluator::matches( $filter, $document ) )
                $found[] = $id;
        }
        $this->assertSame( $expected, $found, $filter );
    }
}
