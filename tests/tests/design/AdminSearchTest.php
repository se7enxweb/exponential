<?php
/**
 * admin4's search pages, its pager and the list filters that search one page of a list.
 *
 *  AS-01 - content/search is one GET form, a search landmark with a visible label and a search field; a failed search
 *          shows an alert, an empty page a prompt, no matches the tips; the header reads "Results for “x” (n)"
 *  AS-02 - The pages pass on the whole search: every filter, the sort order and the page size
 *  AS-03 - One fetch, fetch( 'content', 'filtered_search' ), searches for content/search and the former
 *          content/advancedsearch, which shows the same page
 *  AS-04 - The pager is a navigation landmark and marks the current page
 *  AS-05 - The settings filter reacts to every input, announces its count and has no untranslated words
 *  AS-06 - The header search fetches the sections once for both of its copies, takes a node name from the page's
 *          path, and never offers more results per page than the search gives
 *  AS-07 - A list filter that searches only the page shown says so when the list has more pages
 *  AS-08 - Every new text is in the German and English translations
 *  AS-09 - A search result shows where it is, its type, modification, translations and an edit button
 *  AS-10 - Each active filter is a chip that links to the same search without it; the spelling suggestion keeps them
 *  AS-11 - design/admin and design/admin4 carry the same page; the filter bar works without scripts and offers only
 *          what the search engine can do
 *  AS-12 - The landmarks, the chips, the sort order and the checkboxes are named for assistive technology
 *  AS-13 - A page past the last one says that it has no matches and links to the first page
 *  AS-14 - A result shows eZ Find's highlight washed, with only the marks put back, and a PDF's pages with the words;
 *          eZ Find's extended filters only when registered
 *  AS-15 - No inline script, style or event handler; the filter bar's script needs no jQuery
 *  AS-16 - The filter bar's script (node, js/content_search_filters.js): Apply, dates, sort order, empty fields, fold
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminSearchTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';
require_once __DIR__ . '/AdminNodeBinary.php';

class AdminSearchTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        return AdminFiles::read( self::root() . '/' . $path );
    }

    /** The templates of the search page, which design/admin and design/admin4 carry alike */
    const SHARED = array( 'templates/content/search.tpl', 'templates/content/search_page.tpl', 'templates/content/searchresult.tpl', 'templates/content/advancedsearch.tpl',
                          'javascript/page/content_search_filters.js', 'javascript/page/content_searchresult.js',
                          'stylesheets/modules/search.css' );

    /** AS-01 */
    public function testTheSearchFormIsALabelledSearchLandmark()
    {
        $tpl = self::read( 'design/admin4/templates/content/search_page.tpl' );
        $this->assertStringContainsString( '<form class="a4-search-form" id="search-form" action={\'/content/search/\'|ezurl} method="post" role="search" aria-label=', $tpl );
        $this->assertStringContainsString( '<label for="Search">', $tpl );
        $this->assertStringContainsString( 'type="search" name="SearchText" id="Search"', $tpl );
        $this->assertSame( 1, substr_count( $tpl, '<form' ), 'one form: every filter, the sort order and the words go together; it is posted and answered with a 303 to the view parameters' );
        $this->assertStringNotContainsString( 'method="get"', $tpl );
        $this->assertStringNotContainsString( "concat( '?", $tpl, 'no link of the page carries a query string' );
    }

    /** AS-01 */
    public function testAFailedSearchIsAnAlertNotAnEmptyResult()
    {
        $tpl = self::read( 'design/admin4/templates/content/search_page.tpl' );
        $this->assertMatchesRegularExpression( '#\{if \$page\.failed\}\s*<div class="a4-notice is-bad" role="alert"><p>\{\'The search could not be run\.#', $tpl );
        $failed = strpos( $tpl, '{if $page.failed}' );
        $this->assertLessThan( strpos( $tpl, '{elseif $page.requested|not}' ), $failed, 'the error, then the initial state' );
        $this->assertLessThan( strpos( $tpl, '{elseif $page.count|not}' ), strpos( $tpl, '{elseif $page.requested|not}' ), 'the initial state, then no results' );
        $this->assertStringContainsString( "{'Type the words to search for.'|i18n( 'design/admin/content/search' )}", $tpl );
        // the empty state keeps the tips and names the words
        $this->assertStringContainsString( "{'No results were found for “%text”.'|i18n( 'design/admin/content/search',, hash( '%text', \$f.text ) )|wash}", $tpl );
        $this->assertStringContainsString( "{'Check spelling of keywords.'|i18n( 'design/admin/content/search' )}", $tpl );
        // the result header, as Ibexa's: Results for “x” (n)
        $this->assertStringContainsString( "{'Results for “%text” (%count)'|i18n( 'design/admin/content/search',, hash( '%text', \$f.text, '%count', \$page.count ) )|wash}", $tpl );
    }

    /** AS-02 */
    public function testThePagesKeepTheWholeSearch()
    {
        $tpl = self::read( 'design/admin4/templates/content/search_page.tpl' );
        // the search as view parameters without the page (expContentSearchFilters::viewSuffix(): every filter, the sort order, the page size)
        $this->assertStringContainsString( 'page_uri_suffix=$search_query', $tpl );
        $this->assertStringContainsString( '$search_query = $page.query', $tpl );
        $this->assertStringContainsString( 'view_parameters=hash( \'offset\', $page.offset )', $tpl );
        $this->assertStringContainsString( 'item_limit=$f.limit}', $tpl );
    }

    /** AS-03 */
    public function testOneFetchSearchesForBothPages()
    {
        $tpl = self::read( 'design/admin4/templates/content/search_page.tpl' );
        $this->assertSame( 1, substr_count( $tpl, "fetch( 'content', 'filtered_search', hash( 'offset', first_set( \$view_parameters.offset, 0 ) ) )" ) );
        $this->assertStringNotContainsString( "fetch( 'ezfind'", $tpl, 'the back end is chosen by expContentSearchFilters' );
        $this->assertStringNotContainsString( 'fetch(content,search', $tpl );
        // the advanced search is this page
        $advanced = self::read( 'design/admin4/templates/content/advancedsearch.tpl' );
        $this->assertStringContainsString( "{include uri='design:content/search_page.tpl'}", $advanced );
        // content/search.tpl only includes the page: eZ Find overrides that name for the design admin, not search_page.tpl
        $this->assertStringContainsString( "{include uri='design:content/search_page.tpl'}", self::read( 'design/admin4/templates/content/search.tpl' ) );
        $this->assertFileDoesNotExist( self::root() . '/extension/ezfind/design/admin/templates/content/search_page.tpl' );
        $this->assertStringNotContainsString( '<form', $advanced );
        $FunctionList = array();
        include self::root() . '/kernel/content/function_definition.php';
        $this->assertSame( 'fetchFilteredSearch', $FunctionList['filtered_search']['call_method']['method'] );
        $this->assertSame( array( 'offset' ), array_column( $FunctionList['filtered_search']['parameters'], 'name' ) );
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
        $js = self::read( 'design/admin4/javascript/page/setup_rad.js' );
        $this->assertStringContainsString( "{ezscript( array( 'page/setup_rad.js' ) )}", $tpl );
        $this->assertStringContainsString( "input.addEventListener( 'input', run );", $js );
        $this->assertStringNotContainsString( 'onkeyup', $tpl );
        $this->assertStringNotContainsString( 'onkeyup', $js );
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
                                                                 'The search could not be run. Try fewer or simpler words, or search again later.', '%count matches',
                                                                 'There are no matches on this page.', 'Go to the first page',
                                                                 'Filters', 'Apply', 'Reset', 'Show filters', 'Hide filters', 'Results for “%text” (%count)',
                                                                 'Sort by', 'Active filters', 'Remove this filter', 'Content type', 'Owner', 'Custom range' ),
                        'design/admin/navigator' => array( 'Pages' ),
                        'design/admin/pagelayout' => array( 'Quick search', 'Where' ),
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
        $this->assertStringContainsString( '{foreach $result.path as $path_node}<bdi>{$path_node.name|wash}</bdi>{delimiter} / {/delimiter}{/foreach}', $tpl );
        // the columns of Ibexa's result table: type, name, modified, content type, translations, edit
        foreach ( array( "'Name'", "'Modified'", "'Content type'", "'Translations'" ) as $head )
            $this->assertStringContainsString( "<th scope=\"col\">{{$head}|i18n( 'design/admin/content/search' )}</th>", $tpl, $head );
        $this->assertStringContainsString( '{$result.class_identifier|class_icon( small, $result.class_name|wash )}', $tpl );
        $this->assertStringContainsString( "concat( '/content/view/full/', \$result.node_id, '/(language)/', \$translation.locale )", $tpl );
        $this->assertStringContainsString( "{if \$result_object.can_edit}", $tpl );
        $this->assertStringContainsString( "concat( '/content/edit/', \$result.contentobject_id, '/f/', \$result_language )", $tpl );
        // "Edit selected" stays, with its own script
        $this->assertStringContainsString( 'id="ezsearch-multiedit" data-multiedit-url=', $tpl );
        $this->assertStringContainsString( "{ezscript( array( 'page/content_searchresult.js' ) )}", $tpl );
    }

    /** AS-11 */
    public function testBothDesignsCarryTheSamePage()
    {
        foreach ( self::SHARED as $file )
            $this->assertSame( self::read( "design/admin4/$file" ), self::read( "design/admin/$file" ), $file );
    }

    /** AS-11 */
    public function testTheFilterBarWorksWithoutScripts()
    {
        $tpl = self::read( 'design/admin4/templates/content/search_page.tpl' );
        foreach ( array( 'name="SearchMode"', 'name="ClassID[]"', 'name="ClassAttributeID"', 'name="SectionID[]"', 'name="{$date_field.name}"',
                         'name="{$date_field.name}From"', 'name="{$date_field.name}To"', 'name="Owner"', 'name="Language"', 'name="SubTreeArray[]"',
                         'name="SearchPageLimit"', 'name="SortBy"' ) as $field )
            $this->assertStringContainsString( $field, $tpl, $field );
        // Apply and the sort button are submit buttons; Reset is a link
        $this->assertSame( 2, substr_count( $tpl, '<button type="submit" class="a4-btn a4-btn-small a4-btn-primary" data-search-apply="">' ) + substr_count( $tpl, '<button type="submit" class="a4-btn a4-btn-primary" data-search-apply="">' ) );
        $this->assertStringContainsString( '<button type="submit" class="a4-btn a4-btn-small" data-search-sort-button="">', $tpl );
        $this->assertStringContainsString( "{'Reset'|i18n( 'design/admin/content/search' )}</a>", $tpl );
        // "Show more" is a details element, no script needed
        $this->assertStringContainsString( '<details class="a4-search-more">', $tpl );
        // a filter the engine cannot apply is not offered
        foreach ( array( '{if $capabilities.modified}', '{if $capabilities.owner}', '{if and( $capabilities.language, $page.languages|count|gt( 1 ) )}', '{foreach $capabilities.modes as $mode}', '{foreach $page.sorts as $sort}' ) as $guard )
            $this->assertStringContainsString( $guard, $tpl, $guard );
    }

    /** AS-12 */
    public function testTheSearchIsNamedForAssistiveTechnology()
    {
        $tpl = self::read( 'design/admin4/templates/content/search_page.tpl' );
        $this->assertStringContainsString( "role=\"search\" aria-label=\"{'Search'|i18n( 'design/admin/content/search' )|wash}\"", $tpl );
        $this->assertStringContainsString( '<aside class="a4-search-filters" id="search-filters" aria-labelledby="search-filters-title">', $tpl );
        $this->assertStringContainsString( "<ul class=\"a4-plain a4-search-chips\" aria-label=\"{'Active filters'|i18n( 'design/admin/content/search' )|wash}\">", $tpl );
        $this->assertStringContainsString( "<span class=\"a4-icon a4-bi-x-lg\" role=\"img\" aria-label=\"{'Remove this filter'|i18n( 'design/admin/content/search' )|wash}\"></span>", $tpl );
        $this->assertStringContainsString( '<label for="search-sort">', $tpl );
        $this->assertStringContainsString( 'aria-expanded="true" data-search-toggle=""', $tpl );
        $header = self::read( 'design/admin4/templates/page_search.tpl' );
        $this->assertStringContainsString( "role=\"search\" aria-label=\"{'Quick search'|i18n( 'design/admin/pagelayout' )|wash}\">", $header );
        $this->assertStringContainsString( 'aria-haspopup="dialog"', $header );
        $result = self::read( 'design/admin4/templates/content/searchresult.tpl' );
        $this->assertStringContainsString( "<input aria-label=\"{'Select this item for editing.'|i18n( 'design/admin/content/search' )|wash} {\$result.name|wash}\"", $result );
        // the checkboxes are not sent with the search form
        $this->assertStringNotContainsString( 'class="ezsearch-select" name=', $result );
    }

    /** AS-13 */
    public function testAPagePastTheLastLinksToTheFirstPage()
    {
        $tpl = self::read( 'design/admin4/templates/content/search_page.tpl' );
        $this->assertStringContainsString( "{elseif \$page.result|not}", $tpl );
        $this->assertStringContainsString( "{'There are no matches on this page.'|i18n( 'design/admin/content/search' )} <a href={concat( '/content/search', \$search_query )|ezurl}>{'Go to the first page'|i18n( 'design/admin/content/search' )}</a>", $tpl );
        $this->assertLessThan( strpos( $tpl, "{include name=Result uri='design:content/searchresult.tpl'" ), strpos( $tpl, '{elseif $page.result|not}' ) );
    }

    /** AS-10 */
    public function testEachChipLinksToTheSearchWithoutIt()
    {
        $tpl = self::read( 'design/admin4/templates/content/search_page.tpl' );
        $this->assertStringContainsString( "<a class=\"a4-tag a4-search-chip\" href={concat( '/content/search', \$chip.query )|ezurl}", $tpl );
        foreach ( array( 'mode', 'class', 'class_attribute', 'section', 'modified', 'published', 'owner', 'language', 'subtree' ) as $type )
            $this->assertStringContainsString( "{case match='$type'}", $tpl, $type );
        // the spelling suggestion keeps the filters
        $this->assertStringContainsString( "<a href={concat( '/content/search', \$page.spellcheck.query )|ezurl}>{\$page.spellcheck.text|wash}</a>", $tpl );
    }

    /** AS-14 */
    public function testAResultShowsWhereTheWordsAre()
    {
        $result = self::read( 'design/admin4/templates/content/searchresult.tpl' );
        // No shorten before the marks are put back: it could cut a <em> in half and show a stray "&lt;e".
        $this->assertStringNotContainsString( 'highlight|shorten', $result );
        $this->assertStringContainsString( "{\$result.highlight|wash|explode( '&lt;em&gt;' )|implode( '<mark>' )|explode( '&lt;/em&gt;' )|implode( '</mark>' )}", $result );
        $this->assertStringContainsString( "{include uri='design:ezfindpdfdeepsearch/child_hits.tpl' object_id=\$result.contentobject_id query=\$result_query is_file=true()}", $result );
        $this->assertStringContainsString( "ezini( 'ExtensionSettings', 'ActiveExtensions' )|contains( 'ezfindpdfdeepsearch' )", $result, 'only with the extension' );
        $this->assertMatchesRegularExpression( "#uri='design:content/searchresult\\.tpl' search_result=\\\$page\\.result search_text=\\\$f\\.text\\}#", self::read( 'design/admin4/templates/content/search_page.tpl' ) );
        // the template of the PDF pages comes with the extension ezfindpdfdeepsearch, where it is installed
        if ( is_dir( self::root() . '/extension/ezfindpdfdeepsearch' ) )
        {
            $pages = self::read( 'extension/ezfindpdfdeepsearch/design/admin4/templates/ezfindpdfdeepsearch/child_hits.tpl' );
            $this->assertStringContainsString( "fetch( 'pdfdeepsearch', 'page_hits'", $pages );
            $this->assertStringContainsString( '<ul class="a4-plain a4-search-pages">', $pages );
        }
        // eZ Find's own filters only when they are registered (ezfindcollapse is not in the eZ Find package)
        $class = self::read( 'kernel/classes/expcontentsearchfilters.php' );
        $this->assertStringContainsString( "\$findINI->variable( 'ExtendedAttributeFilters', 'FiltersList' )", $class );
        $this->assertStringContainsString( 'class_exists( $className )', $class );
    }

    /** AS-15 */
    public function testThePageRunsUnderAContentSecurityPolicy()
    {
        foreach ( array( 'search', 'searchresult', 'advancedsearch' ) as $name )
        {
            foreach ( array( 'admin4', 'admin' ) as $design )
            {
                $tpl = self::read( "design/$design/templates/content/$name.tpl" );
                $this->assertDoesNotMatchRegularExpression( '#<script(?![^>]*\bsrc=)|<style|\sstyle="|\son[a-z]+="#i', $tpl, "$design $name" );
            }
        }
        $this->assertStringContainsString( "{ezscript( array( 'page/content_search_filters.js' ) )}", self::read( 'design/admin4/templates/content/search_page.tpl' ) );
        $js = self::read( 'design/admin4/javascript/page/content_search_filters.js' );
        $this->assertDoesNotMatchRegularExpression( '#jQuery\s*[(.]|\$\(|YUI\.|\.innerHTML|eval\(#', $js );
    }

    /** AS-16 */
    public function testTheFilterBarScript()
    {
        $node = AdminNodeBinary::require();
        $out = shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/content_search_filters.js' ) . ' '
                           . escapeshellarg( self::root() . '/design/admin4/javascript/page/content_search_filters.js' ) . ' 2>&1' );
        $cases = array();
        foreach ( explode( "\n", trim( (string)$out ) ) as $line )
        {
            $case = json_decode( $line, true );
            $this->assertIsArray( $case, (string)$out );
            $cases[$case['name']] = $case;
        }
        $this->assertSame( array( true, true ), $cases['initial']['apply'], 'Apply waits for a change' );
        $this->assertTrue( $cases['initial']['range'], 'no dates without a custom range' );
        $this->assertTrue( $cases['initial']['sortButton'], 'the sort order submits itself' );
        $this->assertFalse( $cases['initial']['toggle'] );
        $this->assertTrue( $cases['initial']['folded'] );
        $this->assertSame( 'false', $cases['initial']['expanded'] );
        $this->assertSame( array( false, false ), $cases['a filter changed and back']['changed'] );
        $this->assertSame( array( true, true ), $cases['a filter changed and back']['back'] );
        $this->assertFalse( $cases['dates']['custom'] );
        $this->assertTrue( $cases['dates']['week'] );
        $this->assertSame( 'custom', $cases['dates']['typed'], 'a typed date chooses the custom range' );
        $this->assertSame( array( 'search-form' ), $cases['sort']['submitted'] );
        $this->assertTrue( $cases['sort']['ownerDisabled'], 'an empty field is not sent' );
        $this->assertFalse( $cases['sort']['textDisabled'], 'the words are always sent' );
        $this->assertSame( array( 'owner' => false, 'from' => false, 'to' => true, 'period' => false ), array_diff_key( $cases['submit'], array( 'name' => 1 ) ) );
        $this->assertFalse( $cases['unfold']['folded'] );
        $this->assertSame( 'true', $cases['unfold']['expanded'] );
    }

    private static function xpathString( $text )
    {
        return strpos( $text, "'" ) === false ? "'$text'" : '"' . $text . '"';
    }
}
