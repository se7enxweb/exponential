<?php
/**
 * What content/search and content/advancedsearch take from the request, without a database:
 * \Exponential\View\Kernel\Content\Search and \Exponential\View\Kernel\Content\Advancedsearch.
 *
 *  SR-01 - A search text is cut to the words and characters of site.ini [SearchSettings] MaximumSearchTextWords and
 *          MaximumSearchTextLength (16 and 1024 without the settings); a text within both is left as it is
 *  SR-02 - A section id is a positive integer or -1; anything else is -1
 *  SR-03 - The pages of a search pass on every subtree as SubTreeArray[], the section and the time limit
 *  SR-04 - Ids of the advanced search are positive integers or -1, a period is 1 to 5 or -1
 *  SR-05 - A class attribute is searched in only when it is a searchable attribute of the class searched, or,
 *          without a class, a searchable attribute that exists
 *  SR-06 - The advanced search page runs a search only when there is something to search for
 *  SR-07 - Both views check the section before using it, and set the title escaped
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class ContentSearchRequestTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        if ( !class_exists( '\Exponential\View\Kernel\Content\Search', false ) )
            require_once 'kernel/private/classes/views/content/search.php';
        if ( !class_exists( '\Exponential\View\Kernel\Content\Advancedsearch', false ) )
            require_once 'kernel/private/classes/views/content/advancedsearch.php';
    }

    private function ini( array $settings )
    {
        return new class( $settings )
        {
            private $settings;
            public function __construct( array $settings ) { $this->settings = $settings; }
            public function hasVariable( $group, $name ) { return isset( $this->settings[$name] ); }
            public function variable( $group, $name ) { return $this->settings[$name]; }
        };
    }

    private function classAttribute( $id, $searchable = true )
    {
        return new class( $id, $searchable )
        {
            private $values;
            public function __construct( $id, $searchable ) { $this->values = array( 'id' => $id, 'is_searchable' => $searchable ); }
            public function attribute( $name ) { return $this->values[$name]; }
        };
    }

    /** SR-01 */
    public function testTheSearchTextIsCutToTheLimits()
    {
        $search = '\Exponential\View\Kernel\Content\Search';
        $this->assertSame( 'one two three', $search::limitSearchText( 'one two three', 3, 100 ) );
        $this->assertSame( '  one  two ', $search::limitSearchText( '  one  two ', 3, 100 ), 'a text within the limits is not touched' );
        $this->assertSame( 'one two', $search::limitSearchText( "one two\tthree\nfour", 2, 100 ) );
        $this->assertSame( 'abc', $search::limitSearchText( 'abcdef', 5, 3 ) );
        $this->assertSame( 'Müll', $search::limitSearchText( 'Müller', 5, 4 ), 'characters, not bytes' );
        $this->assertSame( '', $search::limitSearchText( 'one', 0, 100 ) );
        $this->assertSame( '', $search::limitSearchText( 'one', 3, 0 ) );

        $words = implode( ' ', array_fill( 0, 400, 'wort' ) );
        $this->assertSame( 16, count( explode( ' ', $search::limitSearchText( $words, 16, 1024 ) ) ) );
        $this->assertSame( 1024, mb_strlen( $search::limitSearchText( str_repeat( 'a', 5000 ), 16, 1024 ) ) );
    }

    /** SR-01 */
    public function testTheLimitsComeFromTheSettings()
    {
        $search = '\Exponential\View\Kernel\Content\Search';
        $this->assertSame( array( 16, 1024 ), $search::searchTextLimits( $this->ini( array() ) ) );
        $this->assertSame( array( 8, 200 ), $search::searchTextLimits( $this->ini( array( 'MaximumSearchTextWords' => '8', 'MaximumSearchTextLength' => '200' ) ) ) );
        $this->assertSame( array( 16, 1024 ), $search::searchTextLimits( $this->ini( array( 'MaximumSearchTextWords' => '0', 'MaximumSearchTextLength' => 'many' ) ) ) );

        $ini = new eZINI( 'site.ini', 'settings', null, false, null, true );
        $this->assertSame( '16', $ini->variable( 'SearchSettings', 'MaximumSearchTextWords' ) );
        $this->assertSame( '1024', $ini->variable( 'SearchSettings', 'MaximumSearchTextLength' ) );
    }

    /** SR-02 */
    public function testASectionIdIsAPositiveInteger()
    {
        $search = '\Exponential\View\Kernel\Content\Search';
        $this->assertSame( 3, $search::sectionID( '3' ) );
        $this->assertSame( 3, $search::sectionID( 3 ) );
        $this->assertSame( 99, $search::sectionID( '99 OR other' ), 'what follows the number is dropped' );
        $this->assertSame( -1, $search::sectionID( '-1' ) );
        $this->assertSame( -1, $search::sectionID( '-5' ) );
        $this->assertSame( -1, $search::sectionID( '0' ) );
        $this->assertSame( -1, $search::sectionID( 'abc' ) );
        $this->assertSame( -1, $search::sectionID( array( 3 ) ) );
        $this->assertSame( -1, $search::sectionID( null ) );
    }

    /** SR-03 */
    public function testThePagesPassOnEverySubtreeAndTheSection()
    {
        $search = '\Exponential\View\Kernel\Content\Search';
        $this->assertSame( 'SearchText=a+b' . '&SubTreeArray%5B%5D=2&SubTreeArray%5B%5D=43&SectionID=3&SearchTimestamp=1700000000',
                           $search::pageURISuffix( 'a b', array( 2, 43 ), 3, 1700000000 ) );
        $this->assertSame( 'SearchText=x', $search::pageURISuffix( 'x', array(), -1, false ) );
        $this->assertSame( 'SearchText=%26', $search::pageURISuffix( '&', array(), 0, 0 ) );
    }

    /** SR-04 */
    public function testAdvancedSearchIdsAreIntegers()
    {
        $advanced = '\Exponential\View\Kernel\Content\Advancedsearch';
        $this->assertSame( 5, $advanced::positiveID( '5' ) );
        $this->assertSame( 99, $advanced::positiveID( '99 OR other' ) );
        $this->assertSame( -1, $advanced::positiveID( '-1' ) );
        $this->assertSame( -1, $advanced::positiveID( 'x' ) );
        $this->assertSame( -1, $advanced::positiveID( array( 5 ) ) );
        // a class is an id or an identifier; the lookup's answer is used only when it is an id
        $lookup = function ( $identifier ) { return array( 'article' => 16, 'odd' => '3 OR 1' )[$identifier] ?? false; };
        $this->assertSame( 16, $advanced::classID( 'article', $lookup ) );
        $this->assertSame( 7, $advanced::classID( '7', $lookup ) );
        $this->assertSame( -1, $advanced::classID( 'unknown', $lookup ) );
        $this->assertSame( -1, $advanced::classID( 'odd', $lookup ) );
        $this->assertSame( -1, $advanced::classID( 'a b', $lookup ) );
        $this->assertSame( -1, $advanced::classID( array( 'article' ), $lookup ) );

        $this->assertSame( 1, $advanced::searchDate( '1' ) );
        $this->assertSame( 5, $advanced::searchDate( 5 ) );
        $this->assertSame( -1, $advanced::searchDate( 6 ) );
        $this->assertSame( -1, $advanced::searchDate( '0' ) );
        $this->assertSame( -1, $advanced::searchDate( 'last' ) );
    }

    /** SR-05 */
    public function testAClassAttributeMustBelongToTheClassSearched()
    {
        $advanced = '\Exponential\View\Kernel\Content\Advancedsearch';
        $attributes = array( $this->classAttribute( 10 ), $this->classAttribute( 11 ) );
        $this->assertSame( 11, $advanced::classAttributeID( '11', 4, $attributes ) );
        $this->assertSame( -1, $advanced::classAttributeID( '12', 4, $attributes ), 'an attribute of another class' );
        $this->assertSame( -1, $advanced::classAttributeID( '-1', 4, $attributes ) );
        $this->assertSame( 10, $advanced::classAttributeID( '10', 4, array( array( 'id' => '10' ) ) ) );

        $asked = array();
        $fetch = function ( $id ) use ( &$asked )
        {
            $asked[] = $id;
            if ( $id === 20 )
                return $this->classAttribute( 20, true );
            if ( $id === 21 )
                return $this->classAttribute( 21, false );
            return null;
        };
        $this->assertSame( 20, $advanced::classAttributeID( '20', -1, array(), $fetch ) );
        $this->assertSame( -1, $advanced::classAttributeID( '21', -1, array(), $fetch ), 'not searchable' );
        $this->assertSame( -1, $advanced::classAttributeID( '999999', -1, array(), $fetch ), 'does not exist' );
        $this->assertSame( array( 20, 21, 999999 ), $asked );
        $this->assertSame( -1, $advanced::classAttributeID( 'abc', -1, array(), $fetch ) );
        $this->assertSame( array( 20, 21, 999999 ), $asked, 'no lookup for what is no id' );
    }

    /** SR-06 */
    public function testTheAdvancedSearchNeedsSomethingToSearchFor()
    {
        $advanced = '\Exponential\View\Kernel\Content\Advancedsearch';
        $this->assertFalse( $advanced::hasCriteria( '', -1, -1, -1, array() ) );
        $this->assertFalse( $advanced::hasCriteria( '   ', -1, -1, -1, array() ) );
        $this->assertTrue( $advanced::hasCriteria( 'word', -1, -1, -1, array() ) );
        $this->assertTrue( $advanced::hasCriteria( '', 4, -1, -1, array() ) );
        $this->assertTrue( $advanced::hasCriteria( '', -1, 3, -1, array() ) );
        $this->assertTrue( $advanced::hasCriteria( '', -1, -1, 2, array() ) );
        $this->assertTrue( $advanced::hasCriteria( '', -1, -1, -1, array( array( array( 'classattribute_id' => 5 ) ) ) ) );

        $empty = \Exponential\View\Kernel\Content\Search::emptySearchResult();
        $this->assertSame( array(), $empty['SearchResult'] );
        $this->assertSame( 0, $empty['SearchCount'] );
        $this->assertSame( array(), $empty['StopWordArray'] );
    }

    /** SR-07 */
    public function testTheViewsCheckTheSectionAndEscapeTheTitle()
    {
        foreach ( array( 'search', 'advancedsearch' ) as $view )
        {
            $code = (string)file_get_contents( "kernel/private/classes/views/content/$view.php" );
            $this->assertMatchesRegularExpression( '#\$\w+ = \\\\eZSection::fetch\( \$searchSectionID \);\s+if \( !\$\w+ instanceof \\\\eZSection \)\s+\$searchSectionID = -1;#', $code, $view );
            $this->assertSame( 1, substr_count( $code, '\eZSection::fetch(' ), "$view fetches the section once, before the search" );
            $this->assertStringContainsString( '$Module->setTitle( "Search for: " . htmlspecialchars( $searchText, ENT_QUOTES, \'UTF-8\' ) );', $code, $view );
            $this->assertMatchesRegularExpression( '#\$Offset = is_numeric\( \$Offset \) \? max\( 0, \(int\)\$Offset \) : 0;#', $code, $view );
        }
    }
}
