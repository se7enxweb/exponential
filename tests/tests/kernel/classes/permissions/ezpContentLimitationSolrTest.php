<?php
/**
 * The search filter of a content policy limitation of an extension, without the database:
 * ezpContentLimitation::solrFilter(), for search engines that filter by the policies of the user themselves (eZ Find).
 *
 *  SL-01 - Without a handler, or with one that does not implement ezpContentLimitationSolrHandler, the policy gives no
 *          access in searches
 *  SL-02 - The filter of a handler is put in parentheses; it gets the limitation, its values as strings and the user
 *  SL-03 - False, an empty string, an exception or a filter that is not self-contained gives no access
 *  SL-04 - Field conditions: the kernel escapes the values, joins a list with AND, "not" and no values
 *  SL-05 - Field conditions with a field that is no name or a value that is no scalar give no access
 *  SL-06 - solrValue() escapes every character the query parser gives a meaning
 *  SL-07 - isSelfContainedSolr() accepts what stays in its parentheses and nothing else
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
            'X1SqlOnly' => 'X1ContentLimitationSqlOnlyHandler',
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
        return implode( "\n", array_keys( $logged->getValue() ) );
    }

    /** SL-01 */
    public function testWithoutASearchFilterThePolicyGivesNoAccess()
    {
        $this->assertFalse( ezpContentLimitation::solrFilter( 'X1Unregistered', array( '1' ), 14 ) );
        $this->assertFalse( ezpContentLimitation::solrFilter( 'X1SqlOnly', array( '1' ), 14 ) );
        $this->assertStringContainsString( 'does not implement ezpContentLimitationSolrHandler', $this->logged() );
    }

    /** SL-02 */
    public function testTheFilterOfAHandlerIsPutInParentheses()
    {
        $this->assertSame( '( meta_section_id_si:5 )', ezpContentLimitation::solrFilter( 'X1Solr', array( 5, '7' ), 14 ) );
        $this->assertSame( array( array( 'X1Solr', array( '5', '7' ), 14 ) ), X1ContentLimitationSolrHandler::$asked );
    }

    /** SL-03 */
    public function testWhatIsNoFilterGivesNoAccess()
    {
        foreach ( array( false, '', '  ', 'a ) OR ( *:*', 'a:"b', '{!join from=x to=y}*:*', "a:1\0", 'a:1 \\', ')(', null, 5, array() ) as $answer )
        {
            ezpContentLimitation::resetCache();
            X1ContentLimitationSolrHandler::$filter = $answer;
            $this->assertFalse( ezpContentLimitation::solrFilter( 'X1Solr', array( '1' ), 14 ), var_export( $answer, true ) );
        }

        ezpContentLimitation::resetCache();
        X1ContentLimitationSolrHandler::$throw = true;
        $this->assertFalse( ezpContentLimitation::solrFilter( 'X1Solr', array( '1' ), 14 ) );
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

        X1ContentLimitationSolrHandler::$filter = array( 'field' => 'meta_section_id_si', 'values' => array() );
        $this->assertSame( '( ' . ezpContentLimitation::DENY_SOLR . ' )', ezpContentLimitation::solrFilter( 'X1Solr', array(), 14 ) );

        X1ContentLimitationSolrHandler::$filter = array( 'field' => 'meta_section_id_si', 'values' => array(), 'not' => true );
        $this->assertSame( '( *:* )', ezpContentLimitation::solrFilter( 'X1Solr', array(), 14 ) );
    }

    /** SL-05 */
    public function testFieldConditionsThatAreNotWellFormedGiveNoAccess()
    {
        foreach ( array(
            array( 'field' => 'meta_id:1 OR x', 'values' => array( '1' ) ),
            array( 'field' => 'meta_id_si', 'values' => array( array( 1 ) ) ),
            array( 'field' => 'meta_id_si', 'values' => array( '' ) ),
            array( 'field' => 'meta_id_si' ),
            array( array( 'field' => 'meta_id_si', 'values' => array( '1' ) ), 'x' ),
        ) as $answer )
        {
            ezpContentLimitation::resetCache();
            X1ContentLimitationSolrHandler::$filter = $answer;
            $this->assertFalse( ezpContentLimitation::solrFilter( 'X1Solr', array(), 14 ), var_export( $answer, true ) );
        }
    }

    /** SL-06 */
    public function testSolrValueEscapesEveryCharacterThatMeansSomething()
    {
        $this->assertSame( 'plain_Text9', ezpContentLimitation::solrValue( 'plain_Text9' ) );
        $this->assertSame( '\\+\\-\\&\\&\\|\\|\\!\\(\\)\\{\\}\\[\\]\\^\\"\\~\\*\\?\\:\\\\\\/\\ ',
                           ezpContentLimitation::solrValue( '+-&&||!(){}[]^"~*?:\\/ ' ) );
        $this->assertSame( 'Gr\\ üße', ezpContentLimitation::solrValue( 'Gr üße' ) );
    }

    /** SL-07 */
    public function testIsSelfContainedSolr()
    {
        foreach ( array( 'a:1', '(a:1 OR b:2) AND c:"x ) y"', 'a:"q\\"uote"', 'a:\\(1\\)', '-a:1' ) as $filter )
            $this->assertTrue( ezpContentLimitation::isSelfContainedSolr( $filter ), $filter );
        foreach ( array( 'a:1)', '(a:1', 'a:"x', ') OR (', '{!lucene}a:1', 'a:1 {!join}', "a\0", 'a\\' ) as $filter )
            $this->assertFalse( ezpContentLimitation::isSelfContainedSolr( $filter ), $filter );
    }
}
