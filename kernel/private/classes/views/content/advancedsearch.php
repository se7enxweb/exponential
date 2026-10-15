<?php
/**
 * The code of kernel/content/advancedsearch.php, moved into a class (#207 stage 1). The file kernel/content/advancedsearch.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/advancedsearch.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{
if ( !function_exists( 'pageLimit' ) ) {
/*!
 Get search limit
 */
function pageLimit( $searchPageLimit )
{
    switch ( $searchPageLimit )
    {
        case 1:
            return 5;

        case 2:
        default:
            return 10;

        case 3:
            return 20;

        case 4:
            return 30;

        case 5:
            return 50;
    }
}
}
}

namespace Exponential\View\Kernel\Content
{

class Advancedsearch extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();

        $Module = $Params['Module'];
        $ViewMode = $Params['ViewMode'];
        $Offset = $Params['Offset'];

        if ( $ViewMode == 'offset' )
            $ViewMode = '';

        $tpl = \eZTemplate::factory();

        $ini = \eZINI::instance();
        $useSearchCode = $ini->variable( 'SearchSettings', 'SearchViewHandling' ) == 'default';
        $logSearchStats = $ini->variable( 'SearchSettings', 'LogSearchStats' ) == 'enabled';

        $searchText = '';
        $originalSearchText = '';
        $phraseSearchText = '';

        // a negative offset makes the search engine fail
        $Offset = is_numeric( $Offset ) ? max( 0, (int)$Offset ) : 0;

        $searchPageLimit = 2;
        if ( $http->hasVariable( 'SearchPageLimit' ) )
        {
            $searchPageLimit = (int)$http->variable( 'SearchPageLimit' );
        }

        $pageLimit = pageLimit( $searchPageLimit );

        $maximumSearchLimit = $ini->variable( 'SearchSettings', 'MaximumSearchLimit' );
        if ( $pageLimit > $maximumSearchLimit )
            $pageLimit = $maximumSearchLimit;

        // The phrase and the words together are cut to site.ini [SearchSettings] MaximumSearchTextWords words and
        // MaximumSearchTextLength characters: a very long text makes the search engine fail
        list( $maximumWords, $maximumLength ) = Search::searchTextLimits( $ini );
        $searchTextShortened = false;
        if ( $http->hasVariable( 'PhraseSearchText' ) and is_string( $http->variable( 'PhraseSearchText' ) )
             and trim( $http->variable( 'PhraseSearchText' ) ) != '' )
        {
            $phraseSearchText = Search::limitSearchText( $http->variable( 'PhraseSearchText' ), $maximumWords, $maximumLength );
            $searchTextShortened = $phraseSearchText !== $http->variable( 'PhraseSearchText' );
            $searchText = '"' . $phraseSearchText . '"';
        }

        $fullSearchText = '';
        if ( $http->hasVariable( 'SearchText' ) and is_string( $http->variable( 'SearchText' ) ) )
        {
            $usedWords = $phraseSearchText !== '' ? count( preg_split( '/\s+/u', trim( $phraseSearchText ), -1, PREG_SPLIT_NO_EMPTY ) ?: array() ) : 0;
            $originalSearchText = Search::limitSearchText( $http->variable( 'SearchText' ),
                                                           $maximumWords - $usedWords,
                                                           $maximumLength - mb_strlen( $searchText, 'UTF-8' ) );
            if ( $originalSearchText !== $http->variable( 'SearchText' ) )
                $searchTextShortened = true;
            if ( $searchText != '' && $originalSearchText != '' )
                $searchText .= ' ';
            $searchText .= $originalSearchText;
            $fullSearchText = $originalSearchText;
        }

        // Ids of the request are integers; a class, attribute, section or period that is not there is none
        $searchContentClassID = -1;
        $searchContentClassAttributeArray = array();
        if ( $http->hasVariable( 'SearchContentClassID' ) )
        {
            $searchContentClassID = self::classID( $http->variable( 'SearchContentClassID' ) );
            if ( $searchContentClassID > 0 )
            {
                $searchContentClass = \eZContentClass::fetch( $searchContentClassID );
                if ( is_object( $searchContentClass ) )
                    $searchContentClassAttributeArray = $searchContentClass->fetchSearchableAttributes();
                else
                    $searchContentClassID = -1;
            }
        }

        $searchContentClassAttributeID = -1;
        if ( $http->hasVariable( 'SearchContentClassAttributeID' ) )
        {
            $searchContentClassAttributeID = self::classAttributeID( $http->variable( 'SearchContentClassAttributeID' ),
                                                                     $searchContentClassID,
                                                                     $searchContentClassAttributeArray );
        }

        $searchDate = -1;
        if ( $http->hasVariable( 'SearchDate' ) )
        {
            $searchDate = self::searchDate( $http->variable( 'SearchDate' ) );
        }

        $searchTimestamp = false;
        if ( $http->hasVariable( 'SearchTimestamp' ) and
             $http->variable( 'SearchTimestamp' ) )
        {
            $searchTimestamp = self::positiveID( $http->variable( 'SearchTimestamp' ) );
            if ( $searchTimestamp < 1 )
                $searchTimestamp = false;
        }

        $searchSectionID = -1;
        $searchSection = null;
        if ( $http->hasVariable( 'SearchSectionID' ) )
        {
            $searchSectionID = self::positiveID( $http->variable( 'SearchSectionID' ) );
            if ( $searchSectionID > 0 )
            {
                $searchSection = \eZSection::fetch( $searchSectionID );
                if ( !$searchSection instanceof \eZSection )
                    $searchSectionID = -1;
            }
        }

        $subTreeArray = array();
        if ( $http->hasVariable( 'SubTreeArray' ) )
        {
            if ( is_array( $http->variable( 'SubTreeArray' ) ) )
            {
                $subTreeList = $http->variable( 'SubTreeArray' );
            }
            else
            {
                $subTreeList = array( $http->variable( 'SubTreeArray' ) );
            }
            foreach ( $subTreeList as $subTreeItem )
            {
                // as form input is generally a string, is_int cannot be used for checking the value type
                if ( is_numeric( $subTreeItem ) && (int)$subTreeItem > 0  )
                {
                    $subTreeArray[] = (int)$subTreeItem;
                }
            }
        }

        $Module->setTitle( "Search for: " . htmlspecialchars( $searchText, ENT_QUOTES, 'UTF-8' ) );

        $classArray = \eZContentClass::fetchList( \eZContentClass::VERSION_STATUS_DEFINED, true, false, array( 'name' => 'asc' ) );

        $sectionArray = \eZSection::fetchList();

        $searchArray = \eZSearch::buildSearchArray();

        // The page itself, opened without anything to search for, runs no search: it would list every object
        $searchRequested = self::hasCriteria( $searchText, $searchContentClassID, $searchSectionID, $searchDate, $searchArray );
        $searchResult = Search::emptySearchResult();

        if ( $useSearchCode && $searchRequested )
        {
            $searchResult = \eZSearch::search( $searchText, array( 'SearchSectionID' => $searchSectionID,
                                                                  'SearchContentClassID' => $searchContentClassID,
                                                                  'SearchContentClassAttributeID' => $searchContentClassAttributeID,
                                                                  'SearchSubTreeArray' => $subTreeArray,
                                                                  'SearchDate' => $searchDate,
                                                                  'SearchTimestamp' => $searchTimestamp,
                                                                  'SearchLimit' => $pageLimit,
                                                                  'SearchOffset' => $Offset ),
                                               $searchArray );
            // Audit (doc/bc/6.0/audit.md, content.search.query): a sampled read
            if ( isset( $searchResult ) && is_array( $searchResult ) && class_exists( 'expAuditHook' ) )
                \expAuditHook::read( 'content.search.query', null, function () use ( $searchText, $searchResult ) {
                    $phrase = (string)$searchText;
                    if ( function_exists( 'mb_substr' ) && mb_strlen( $phrase ) > 64 )
                        $phrase = mb_substr( $phrase, 0, 64 ) . '…';
                    return array( 'object' => array( 'type' => 'search', 'id' => 'content/advancedsearch' ),
                                  'after' => array( 'phrase' => $phrase, 'hits' => isset( $searchResult['SearchCount'] ) ? (int)$searchResult['SearchCount'] : null ) );
                } );

            if ( strlen(trim($searchText)) == 0 && count( $searchArray ) > 0  )
            {
                $searchText = 'search by additional parameter';
            }
        }

        $viewParameters = array( 'offset' => $Offset );

        $searchData = false;
        $tpl->setVariable( "search_data", $searchData );
        $tpl->setVariable( 'search_contentclass_id', $searchContentClassID );
        $tpl->setVariable( 'search_contentclass_attribute_id', $searchContentClassAttributeID );
        $tpl->setVariable( 'search_section_id', $searchSectionID );
        $tpl->setVariable( 'search_date', $searchDate );
        $tpl->setVariable( 'search_timestamp', $searchTimestamp );
        $tpl->setVariable( 'search_sub_tree', $subTreeArray );
        $tpl->setVariable( 'search_text', $searchText );
        $tpl->setVariable( 'search_text_shortened', $searchTextShortened );
        $tpl->setVariable( 'search_requested', $searchRequested );
        $tpl->setVariable( 'search_page_limit', $searchPageLimit );
        $tpl->setVariable( 'full_search_text', $fullSearchText );
        $tpl->setVariable( 'phrase_search_text', $phraseSearchText );

        $tpl->setVariable( "view_parameters", $viewParameters );
        $tpl->setVariable( 'use_template_search', !$useSearchCode );

        // --- Compatibility code start ---
        if ( $useSearchCode )
        {
            $tpl->setVariable( 'offset', $Offset );
            $tpl->setVariable( 'page_limit', $pageLimit );
            $tpl->setVariable( 'search_text_enc', urlencode( $originalSearchText ) );
            $tpl->setVariable( 'phrase_search_text_enc', urlencode( $phraseSearchText ) );
            $tpl->setVariable( 'search_result', $searchResult['SearchResult'] );
            $tpl->setVariable( 'search_count', $searchResult['SearchCount'] );
            $tpl->setVariable( 'stop_word_array', $searchResult['StopWordArray'] );
            if ( isset( $searchResult["SearchExtras"] ) )
            {
                $tpl->setVariable( "search_extras", $searchResult["SearchExtras"] );
            }
        }
        else
        {
            $tpl->setVariable( 'offset', false );
            $tpl->setVariable( 'page_limit', false );
            $tpl->setVariable( 'search_text_enc', false );
            $tpl->setVariable( 'phrase_search_text_enc', false );
            $tpl->setVariable( 'search_result', false );
            $tpl->setVariable( 'search_count', false );
            $tpl->setVariable( 'stop_word_array', false );
        }
        // --- Compatibility code end ---

        $tpl->setVariable( 'content_class_array', $classArray );
        $tpl->setVariable( 'section_array', $sectionArray );
        $tpl->setVariable( 'search_content_class_attribute_array', $searchContentClassAttributeArray );

        // Set template variable containing search terms for attribute-based search.
        $searchTermsArray = array();
        // BEGIN old code for backwards compatibility
        // Set template variable for attribute-based search.
        // Make it a hash with classattribute_id as key. If it has an identifier, add that to the key.
        $searchArrayByClassAttributeID = array();
        // END old code for backwards compatibility
        foreach ( $searchArray as $searchItem )
        {
            foreach ( $searchItem as $searchTerms )
            {
                if ( isSet( $searchTerms['identifier'] ) )
                {
                    $searchTermsArray[$searchTerms['identifier']] = $searchTerms;
                    // BEGIN old code for backwards compatibility
                    $searchArrayByClassAttributeID[$searchTerms['classattribute_id'] . '_' .
                                                   $searchTerms['identifier']] = $searchTerms;
                    // END old code for backwards compatibility
                }
                else
                {
                    $searchTermsArray[$searchTerms['classattribute_id']] = $searchTerms;
                    // BEGIN old code for backwards compatibility
                    $searchArrayByClassAttributeID[$searchTerms['classattribute_id']] = $searchTerms;
                    // END old code for backwards compatibility
                }
            }
        }
        $tpl->setVariable( 'search_terms_array', $searchTermsArray );
        // BEGIN old code for backwards compatibility
        $tpl->setVariable( 'search_array_by_class_attribute_id', $searchArrayByClassAttributeID );
        // END old code for backwards compatibility

        if ( $searchSectionID != -1 )
        {
            $res = \eZTemplateDesignResource::instance();
            $keyArray = array( array( 'section', $searchSectionID ),
                               array( 'section_identifier', $searchSection->attribute( 'identifier' ) ) );
            $res->setKeys( $keyArray );
        }

        $Result = array();

        if ( trim( $ViewMode ) != '' )
        {
            // Fetch override template for viewmode if wanted
            $Result['content'] = $tpl->fetch( "design:content/advancedsearch/$ViewMode.tpl" );
        }
        else
        {
            $Result['content'] = $tpl->fetch( 'design:content/advancedsearch.tpl' );
        }
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'Search' ),
                                        'url' => false ),
                                 array( 'text' => \ezpI18n::tr( 'kernel/content', 'Advanced' ),
                                        'url' => false ) );

        $searchData = false;
        if ( !$useSearchCode )
        {
            if ( $tpl->hasVariable( "search_data" ) )
            {
                $searchData = $tpl->variable( "search_data" );
            }
        }
        else
        {
            $searchData = $searchResult;
        }

        if ( $logSearchStats and
             trim( $searchText ) != "" and
             is_array( $searchData ) and
             array_key_exists( 'SearchCount', $searchData ) and
             is_numeric( $searchData['SearchCount'] ) )
        {
            \eZSearchLog::addPhrase( $searchText, $searchData["SearchCount"] );
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * An id of the request as a positive integer, or -1 for none (also for anything that is no number).
     *
     * @param mixed $value
     * @return int
     */
    public static function positiveID( $value )
    {
        $id = is_scalar( $value ) ? (int)$value : 0;
        return $id > 0 ? $id : -1;
    }

    /**
     * The class of the request as a positive integer id, or -1 for none: a number is an id (positiveID()), a class
     * identifier (article, folder ...) is looked up, as eZ Find always did with SearchContentClassID. Anything else,
     * and an identifier that names no class, is -1.
     *
     * @param mixed $value
     * @param callable|null $byIdentifier identifier => class id or false; defaults to eZContentClass::classIDByIdentifier()
     * @return int
     */
    public static function classID( $value, $byIdentifier = null )
    {
        if ( !is_scalar( $value ) )
            return -1;
        $value = trim( (string)$value );
        if ( preg_match( '/^[A-Za-z_][A-Za-z0-9_]*$/', $value ) )
        {
            $id = $byIdentifier ? $byIdentifier( $value ) : \eZContentClass::classIDByIdentifier( $value );
            return is_scalar( $id ) && ctype_digit( (string)$id ) && (int)$id > 0 ? (int)$id : -1;
        }
        return self::positiveID( $value );
    }

    /**
     * The period of the request (1 last day, 2 last week, 3 last month, 4 last three months, 5 last year), or -1.
     *
     * @param mixed $value
     * @return int
     */
    public static function searchDate( $value )
    {
        $date = self::positiveID( $value );
        return $date <= 5 ? $date : -1;
    }

    /**
     * The class attribute to search in: with a class, one of its searchable attributes; without a class, a
     * searchable attribute that exists. Anything else is -1 (all attributes). Before, an attribute that does not
     * exist stopped the search with an error and one of another class gave no result.
     *
     * @param mixed $value the requested class attribute id
     * @param int $classID the class searched, -1 for any
     * @param array $classAttributes the searchable attributes of that class (objects or arrays with an id)
     * @param callable|null $fetch class attribute id => eZContentClassAttribute or null, for a search without a
     *                             class; defaults to eZContentClassAttribute::fetch()
     * @return int
     */
    public static function classAttributeID( $value, $classID, array $classAttributes, $fetch = null )
    {
        $attributeID = self::positiveID( $value );
        if ( $attributeID < 1 )
            return -1;
        if ( $classID > 0 )
        {
            foreach ( $classAttributes as $classAttribute )
            {
                $id = is_object( $classAttribute ) ? $classAttribute->attribute( 'id' ) : ( isset( $classAttribute['id'] ) ? $classAttribute['id'] : 0 );
                if ( (int)$id === $attributeID )
                    return $attributeID;
            }
            return -1;
        }
        if ( $fetch === null )
            $fetch = function ( $id ) { return \eZContentClassAttribute::fetch( $id ); };
        $classAttribute = call_user_func( $fetch, $attributeID );
        if ( is_object( $classAttribute ) && $classAttribute->attribute( 'is_searchable' ) )
            return $attributeID;
        return -1;
    }

    /**
     * Whether there is something to search for: a text, a class, a section, a period or an attribute search.
     * Subtrees alone are not: they come along as hidden fields of the form.
     *
     * @param string $searchText
     * @param int $classID
     * @param int $sectionID
     * @param int $searchDate
     * @param array $searchArray eZSearch::buildSearchArray()
     * @return bool
     */
    public static function hasCriteria( $searchText, $classID, $sectionID, $searchDate, array $searchArray )
    {
        return trim( (string)$searchText ) !== '' || $classID > 0 || $sectionID > 0 || $searchDate > 0 || count( $searchArray ) > 0;
    }
}

}
