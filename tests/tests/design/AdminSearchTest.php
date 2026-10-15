<?php
/**
 * admin4's search pages, its pager and the list filters that search one page of a list.
 *
 *  AS-01 - content/search is a search landmark with a visible label and a search field; a failed search shows an
 *          alert instead of "no results", an empty text a prompt instead of "Search for <>"
 *  AS-02 - The pages of a search pass on every subtree as SubTreeArray[] and the section; the advanced search's
 *          pages pass on the words as typed, not the text with the phrase in front
 *  AS-03 - Without anything to search for the template search runs no fetch
 *  AS-04 - The pager is a navigation landmark and marks the current page
 *  AS-05 - The settings filter reacts to every input, announces its count and has no untranslated words
 *  AS-06 - The header search fetches the sections once for both of its copies, takes a node name from the page's
 *          path, and never offers more results per page than the search gives
 *  AS-07 - A list filter that searches only the page shown says so when the list has more pages
 *  AS-08 - Every new text is in the German and English translations
 *  AS-09 - A search result shows where it is
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminSearchTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminSearchTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        return (string)file_get_contents( self::root() . '/' . $path );
    }

    /** AS-01 */
    public function testTheSearchFormIsALabelledSearchLandmark()
    {
        $tpl = self::read( 'design/admin4/templates/content/search.tpl' );
        $this->assertStringContainsString( '<form action={\'/content/search/\'|ezurl} method="get" role="search">', $tpl );
        $this->assertStringContainsString( '<label for="Search">', $tpl );
        $this->assertStringContainsString( 'type="search" name="SearchText" id="Search"', $tpl );
        $this->assertStringNotContainsString( 'type="text" name="SearchText"', $tpl );

        $advanced = self::read( 'design/admin4/templates/content/advancedsearch.tpl' );
        $this->assertStringContainsString( 'method="get" role="search">', $advanced );
    }

    /** AS-01 */
    public function testAFailedSearchIsAnAlertNotAnEmptyResult()
    {
        foreach ( array( 'search', 'advancedsearch' ) as $name )
        {
            $tpl = self::read( "design/admin4/templates/content/$name.tpl" );
            $this->assertStringContainsString( '{if is_set( $search_extras )}{set $search_info = $search_extras}{elseif $search}{if is_set( $search.SearchExtras )}{set $search_info = $search.SearchExtras}{/if}{/if}', $tpl, $name );
            $this->assertStringContainsString( '{if is_object( $search_info )}{if $search_info.error}{set $search_failed = true()}{/if}{/if}', $tpl, $name );
            $this->assertMatchesRegularExpression( '#\{if \$search_failed\}\s*<div class="a4-notice is-bad" role="alert"><p>\{\'The search could not be run\.#', $tpl, $name );
            $this->assertLessThan( strpos( $tpl, 'No results were found' ), strpos( $tpl, '{if $search_failed}' ), "$name: the error comes before the no-results text" );
        }
        $tpl = self::read( 'design/admin4/templates/content/search.tpl' );
        $this->assertMatchesRegularExpression( "#\{elseif and\( \\\$search_count\|not, \\\$search_text\|trim\|eq\( '' \) \)\}\s*<p>\{'Type the words to search for\.'#", $tpl );
        $this->assertStringContainsString( "{if \$search_text|trim|eq( '' )}{'%count matches'", $tpl );
    }

    /** AS-02 */
    public function testThePagesKeepWhereTheSearchSearches()
    {
        $tpl = self::read( 'design/admin4/templates/content/search.tpl' );
        $this->assertStringContainsString( "concat( '&', 'SubTreeArray[]'|urlencode, '=', \$search_subtree_array|implode( concat( '&', 'SubTreeArray[]'|urlencode, '=' ) ) )", $tpl );
        $this->assertStringContainsString( "\$search_section_id|gt( 0 )|choose( '', concat( '&SectionID=', \$search_section_id ) )", $tpl );
        $this->assertStringNotContainsString( "\$search_subtree_array|implode( ',' )", $tpl );

        $advanced = self::read( 'design/admin4/templates/content/advancedsearch.tpl' );
        $this->assertStringContainsString( "page_uri_suffix=concat('?SearchText=',\$full_search_text|urlencode,", $advanced );
        $this->assertStringNotContainsString( "'?SearchText=',\$search_text|urlencode", $advanced );
    }

    /** AS-03 */
    public function testNoTemplateSearchWithoutSomethingToSearchFor()
    {
        foreach ( array( 'search', 'advancedsearch' ) as $name )
        {
            $tpl = self::read( "design/admin4/templates/content/$name.tpl" );
            $this->assertStringContainsString( '{section show=and( $use_template_search, $search_requested )}', $tpl, $name );
            $this->assertStringContainsString( '{default search_requested=true()', $tpl, $name );
        }
    }

    /** AS-04 */
    public function testThePagerIsANavigationLandmark()
    {
        $tpl = self::read( 'design/admin4/templates/navigator/google.tpl' );
        $this->assertStringContainsString( '<nav aria-label="{\'Pages\'|i18n( \'design/admin/navigator\' )|wash}">', $tpl );
        $this->assertSame( substr_count( $tpl, '<nav ' ), substr_count( $tpl, '</nav>' ) );
        $this->assertStringContainsString( '<span class="current" aria-current="page">', $tpl );
        $this->assertStringContainsString( '<div class="pagenavigator">', $tpl, 'the styled class stays on its div' );
    }

    /** AS-05 */
    public function testTheSettingsFilterReactsToEveryInput()
    {
        $tpl = self::read( 'design/admin4/templates/setup/rad.tpl' );
        $this->assertStringContainsString( "input.addEventListener( 'input', run );", $tpl );
        $this->assertStringNotContainsString( 'onkeyup', $tpl );
        $this->assertStringContainsString( 'id="radFindCount" aria-live="polite" data-text="{\'%shown of %count\'|i18n( \'design/admin/setup/rad\' )|wash}"', $tpl );
        $this->assertStringNotContainsString( "' of '", $tpl );
    }

    /** AS-06 */
    public function testTheHeaderSearchFetchesLittle()
    {
        $search = self::read( 'design/admin4/templates/page_search.tpl' );
        $menu = self::read( 'design/admin4/templates/page_topmenu.tpl' );
        $this->assertSame( 1, substr_count( $menu, "fetch( 'section', 'list' )" ) );
        $this->assertStringContainsString( "{def \$search_sections = cond( \$ui_context_edit, array(), fetch( 'section', 'list' ) )}", $menu );
        $this->assertLessThan( strpos( $menu, "{include uri='design:page_search.tpl'}" ), strpos( $menu, '{def $search_sections' ), 'set before the first copy' );
        $this->assertStringContainsString( "{if is_set( \$search_sections )|not}{def \$search_sections = fetch( 'section', 'list' )}{/if}", $search );
        $this->assertStringContainsString( '{foreach $search_sections as $section}', $search );
        $this->assertStringContainsString( '{foreach $module_result.path as $search_path_item}', $search );
        $this->assertStringContainsString( "ezini( 'SearchSettings', 'MaximumSearchLimit' )", $search );
        $this->assertMatchesRegularExpression( "#name=\"BrowsePageLimit\" type=\"hidden\" value=\"\{min\( min\( ezpreference\( 'admin_list_limit' \), 3 \)\|choose\( 10, 10, 25, 50 \), ezini\( 'SearchSettings', 'MaximumSearchLimit' \) \)\}\"#", $search );
    }

    /** AS-07 */
    public function testListFiltersSayTheySearchOnePage()
    {
        foreach ( array( 'class/grouplist' => array( '$group_count', 'classgroups' ),
                         'class/classlist' => array( '$class_count', 'classlist' ),
                         'workflow/grouplist' => array( '$group_count', 'wfgroups' ),
                         'section/list' => array( '$section_count', 'section' ) ) as $template => $names )
        {
            list( $count, $prefix ) = $names;
            $tpl = self::read( "design/admin4/templates/$template.tpl" );
            $this->assertStringContainsString( "{if $count|gt( \$limit )} $prefix-search-page{/if}\" />", $tpl, $template );
            $this->assertStringContainsString( "{if $count|gt( \$limit )}<span class=\"a4-help\" id=\"$prefix-search-page\">{'Only this page is searched: %from to %to of %count.'|i18n( 'design/admin/$template',", $tpl, $template );
        }
    }

    /** AS-08 */
    public function testNewTextsAreTranslated()
    {
        $texts = array( 'design/admin/content/search' => array( 'Search for', 'The search text was too long and was shortened.', 'Type the words to search for.',
                                                                 'The search could not be run. Try fewer or simpler words, or search again later.', '%count matches' ),
                        'design/admin/navigator' => array( 'Pages' ),
                        'design/admin/setup/rad' => array( '%shown of %count' ),
                        'design/admin/class/grouplist' => array( 'Only this page is searched: %from to %to of %count.' ),
                        'design/admin/class/classlist' => array( 'Only this page is searched: %from to %to of %count.' ),
                        'design/admin/workflow/grouplist' => array( 'Only this page is searched: %from to %to of %count.' ),
                        'design/admin/section/list' => array( 'Only this page is searched: %from to %to of %count.' ) );
        foreach ( array( 'ger-DE', 'eng-US' ) as $language )
        {
            $ts = new DOMDocument();
            $this->assertTrue( $ts->load( self::root() . "/share/translations/$language/translation.ts" ), $language );
            $xpath = new DOMXPath( $ts );
            foreach ( $texts as $context => $sources )
            {
                foreach ( $sources as $source )
                {
                    $query = sprintf( '//context[name=%s]/message[source=%s]/translation', self::xpathString( $context ), self::xpathString( $source ) );
                    $found = $xpath->query( $query );
                    $this->assertSame( 1, $found->length, "$language $context: $source" );
                    $this->assertNotSame( '', trim( $found->item( 0 )->textContent ), "$language $context: $source" );
                }
            }
        }
    }

    /** AS-09 */
    public function testASearchResultShowsWhereItIs()
    {
        $tpl = self::read( 'design/admin4/templates/content/searchresult.tpl' );
        $this->assertStringContainsString( '{foreach $SearchResult.item.path as $path_node}{$path_node.name|wash}{delimiter} / {/delimiter}{/foreach}', $tpl );
    }

    private static function xpathString( $text )
    {
        return strpos( $text, "'" ) === false ? "'$text'" : '"' . $text . '"';
    }
}
