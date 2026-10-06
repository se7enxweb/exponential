<?php
/**
 * The code of kernel/content/urlalias_wildcard.php, moved into a class (#207 stage 1). The file kernel/content/urlalias_wildcard.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/urlalias_wildcard.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Content
{

class UrlaliasWildcard extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module =& $Params['Module'];
        $http = \eZHTTPTool::instance();

        $Offset = $Params['Offset'];
        if ( $Module->hasActionParameter( 'Offset' ) )
        {
            $Offset = $Module->actionParameter( 'Offset' );
        }

        $tpl = \eZTemplate::factory();
        $limit = 20;

        $infoCode = 'no-errors'; // This will be modified if info/warning is given to user.
        $infoData = array(); // Extra parameters can be added to this array
        $wildcardSrcText = false;
        $wildcardDstText = false;
        $wildcardType = false;

        if ( $Module->isCurrentAction( 'RemoveAllWildcards' ) )
        {
            \eZURLWildcard::removeAll();

            \eZURLWildcard::expireCache();

            $infoCode = "feedback-wildcard-removed-all";
        }
        else if ( $Module->isCurrentAction( 'RemoveWildcard' ) )
        {
            if ( $http->hasPostVariable( 'WildcardIDList' ) )
            {
                $wildcardIDs = $http->postVariable( 'WildcardIDList' );

                \eZURLWildcard::removeByIDs( $wildcardIDs );

                \eZURLWildcard::expireCache();

                $infoCode = "feedback-wildcard-removed";
            }
        }
        else if ( $Module->isCurrentAction( 'NewWildcard' ) )
        {
            $wildcardSrcText = trim( $Module->actionParameter( 'WildcardSourceText' ) );
            $wildcardSrcText = ltrim( $wildcardSrcText, '/' );
            $wildcardDstText = trim( $Module->actionParameter( 'WildcardDestinationText' ) );
            $wildcardType = $http->hasPostVariable( 'WildcardType' ) && strlen( trim( $http->postVariable( 'WildcardType' ) ) ) > 0;

            if ( strlen( $wildcardSrcText ) == 0 )
            {
                $infoCode = "error-no-wildcard-text";
            }
            else if ( strlen( $wildcardDstText ) == 0 )
            {
                $infoCode = "error-no-wildcard-destination-text";
            }
            else if ( $unknown = self::unknownPlaceholders( $wildcardSrcText, $wildcardDstText ) )
            {
                // {3} with two * in the pattern is replaced by nothing: the wildcard would send visitors to a
                // destination with a part missing
                $infoCode = "error-wildcard-placeholder";
                $infoData['placeholders'] = '{' . implode( '}, {', $unknown ) . '}';
                $infoData['stars'] = substr_count( $wildcardSrcText, '*' );
            }
            else
            {
                $wildcard = \eZURLWildcard::fetchBySourceURL( $wildcardSrcText, false );
                if ( $wildcard )
                {
                    $infoCode = "feedback-wildcard-exists";

                    $infoData['wildcard_src_url'] = $wildcardSrcText;
                    $infoData['wildcard_dst_url'] = $wildcard['destination_url'];
                }
                else
                {
                    $row = array(
                        'source_url' => $wildcardSrcText,
                        'destination_url' => $wildcardDstText,
                        'type' => $wildcardType ? \eZURLWildcard::TYPE_FORWARD : \eZURLWildcard::TYPE_DIRECT );

                    $wildcard = new \eZURLWildcard( $row );
                    $wildcard->store();

                    \eZURLWildcard::expireCache();

                    $infoData['wildcard_src_url'] = $wildcardSrcText;
                    $infoData['wildcard_dst_url'] = $wildcardDstText;

                    $wildcardSrcText = false;
                    $wildcardDstText = false;
                    $wildcardType = false;

                    $infoCode = "feedback-wildcard-created";
                }
            }
        }

        // Audit (doc/bc/6.0/audit.md, content.urlalias.change): an alias or wildcard added or removed
        if ( class_exists( 'expAuditHook' ) && in_array( $infoCode, array( 'feedback-removed-all', 'feedback-removed', 'feedback-alias-created',
                                                                           'feedback-alias-cleanup', 'feedback-wildcard-removed-all',
                                                                           'feedback-wildcard-removed', 'feedback-wildcard-created' ), true ) )
            \expAuditHook::emit( 'content.urlalias.change', function () use ( $infoCode, $infoData, $Module ) {
                $removed = strpos( $infoCode, 'removed' ) !== false;
                $alias = isset( $infoData['wildcard_src_url'] ) ? (string)$infoData['wildcard_src_url'] : null;
                return array( 'object' => array( 'type' => 'wildcard', 'id' => $alias !== null ? $alias : $infoCode ),
                              'target' => null,
                              'verb' => $removed ? 'remove' : 'add',
                              'before' => $removed ? array( 'removed' => $infoCode === 'feedback-removed-all' || $infoCode === 'feedback-wildcard-removed-all' ? 'all' : 'selected' ) : null,
                              'after' => $removed ? null : array( 'alias' => $alias,
                                                                  'cleaned_from' => isset( $infoData['orig_alias'] ) ? (string)$infoData['orig_alias'] : null ) );
            } );

        // User preferences
        $limitList = array( array( 'id'    => 1,
                                   'value' => 10 ),
                            array( 'id'    => 2,
                                   'value' => 25 ),
                            array( 'id'    => 3,
                                   'value' => 50 ),
                            array( 'id'    => 4,
                                   'value' => 100 ) );
        $limitID = \eZPreferences::value( 'admin_urlwildcard_list_limit' );
        $limitValues = array();
        foreach ( $limitList as $limitEntry )
        {
            $limitIDs[]                     = $limitEntry['id'];
            $limitValues[$limitEntry['id']] = $limitEntry['value'];
        }
        if ( !in_array( $limitID, $limitIDs ) )
        {
            $limitID = 2;
        }

        // A search over pattern and destination and the kind of wildcard, both off the address
        $search = \Exponential\View\Kernel\Url\ListView::searchText( $http->hasGetVariable( 'q' ) ? $http->getVariable( 'q' ) : '' );
        $userParameters = isset( $Params['UserParameters'] ) ? (array)$Params['UserParameters'] : array();
        $kind = UrlaliasGlobal::kindKey( isset( $userParameters['kind'] ) ? $userParameters['kind'] : '' );
        // An address to try against the wildcards, and what it gives
        $testText = \Exponential\View\Kernel\Url\ListView::searchText( $http->hasGetVariable( 'test' ) ? $http->getVariable( 'test' ) : '' );
        $testResult = false;

        // Fetch wildcads
        $wildcardsLimit = $limitValues[$limitID];
        $wildcardsCount = \eZURLWildcard::fetchListCount();
        $wildcardsTotalCount = (int)$wildcardsCount;
        if ( !is_numeric( $Offset ) || $Offset < 0 )
            $Offset = 0;
        $Offset = (int)$Offset;
        if ( $search === '' && $kind === 'all' )
        {
            // check offset, it can be out of range if some wildcards were removed.
            if ( $Offset >= $wildcardsCount )
            {
                $Offset = 0;
            }
            $wildcardList = \eZURLWildcard::fetchList( $Offset, $wildcardsLimit );
        }
        else
        {
            // the search and the kind are conditions of the query: one count and one page query
            $type = self::typeOfKind( $kind );
            $wildcardsCount = \eZURLWildcard::fetchFilteredListCount( $search !== '' ? $search : null, $type );
            if ( $Offset >= $wildcardsCount )
                $Offset = 0;
            $wildcardList = \eZURLWildcard::fetchFilteredList( $Offset, $wildcardsLimit, $search !== '' ? $search : null, $type );
        }

        if ( $testText !== '' )
        {
            $rows = \eZURLWildcard::fetchList( false, false, false );
            $testResult = self::firstMatch( is_array( $rows ) ? $rows : array(), $testText );
            if ( $testResult )
            {
                $resolved = \eZURLAliasML::urlToAction( $testResult['destination'] );
                if ( !$resolved )
                {
                    $elements = \eZURLAliasML::fetchByPath( $testResult['destination'] );
                    $resolved = $elements ? $elements[0]->attribute( 'action' ) : false;
                }
                $testResult['resolves'] = $resolved ? \eZURLAliasML::actionToUrl( $resolved ) : false;
                $testResult['external'] = (bool)preg_match( '#^[a-z][a-z0-9+.-]*://#i', $testResult['destination'] );
            }
        }

        $viewParameters = array( 'offset' => $Offset );
        if ( $kind !== 'all' )
            $viewParameters['kind'] = $kind;


        $path = array();
        $path[] = array( 'url'  => false,
                         'text' => \ezpI18n::tr( 'kernel/content/urlalias_wildcard', 'URL wildcard aliases' ) );

        $tpl->setVariable( 'wildcard_list', $wildcardList );
        $tpl->setVariable( 'wildcards_limit', $wildcardsLimit );
        $tpl->setVariable( 'wildcards_count', $wildcardsCount );
        $tpl->setVariable( 'info_code', $infoCode );
        $tpl->setVariable( 'info_data', $infoData );
        $tpl->setVariable( 'wildcardSourceText', $wildcardSrcText );
        $tpl->setVariable( 'wildcardDestinationText', $wildcardDstText );
        $tpl->setVariable( 'wildcardType', $wildcardType );
        $tpl->setVariable( 'limitList', $limitList );
        $tpl->setVariable( 'limitID', $limitID );
        $tpl->setVariable( 'view_parameters', $viewParameters );
        // since 6.0.15: the search, the kind, how many there are in all and the address tried
        $tpl->setVariable( 'wildcards_total_count', $wildcardsTotalCount );
        $tpl->setVariable( 'wildcard_search', $search );
        $tpl->setVariable( 'wildcard_search_suffix', $search !== '' ? '?q=' . rawurlencode( $search ) : '' );
        $tpl->setVariable( 'wildcard_kind', $kind );
        $tpl->setVariable( 'wildcard_test', $testText );
        $tpl->setVariable( 'wildcard_test_result', $testResult );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/urlalias_wildcard.tpl' );
        $Result['path'] = $path;

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * The placeholders of a destination ({1}, {2} ...) that the pattern has no * for, in ascending order. Each *
     * of the pattern is one placeholder, counted from 1.
     *
     * @param string $source
     * @param string $destination
     * @return int[]
     */
    public static function unknownPlaceholders( $source, $destination )
    {
        $stars = substr_count( (string)$source, '*' );
        preg_match_all( '#{([0-9]+)}#', (string)$destination, $matches );
        $unknown = array();
        foreach ( $matches[1] as $number )
        {
            $number = (int)$number;
            if ( ( $number < 1 || $number > $stars ) && !in_array( $number, $unknown, true ) )
                $unknown[] = $number;
        }
        sort( $unknown );
        return $unknown;
    }

    /**
     * The regular expression a wildcard pattern is matched with, as eZURLWildcard builds it: each * is any text,
     * the rest is literal, matched from the start of the address without regard to case.
     *
     * @param string $source
     * @return string
     */
    public static function patternRegexp( $source )
    {
        $parts = array();
        foreach ( explode( '*', (string)$source ) as $part )
            $parts[] = preg_quote( $part, '#' );
        return '#^' . implode( '(.*)', $parts ) . '#i';
    }

    /**
     * The replacement a destination becomes, as eZURLWildcard builds it: {n} is the text of the n-th *.
     *
     * @param string $destination
     * @return string
     */
    public static function destinationReplacement( $destination )
    {
        $pieces = preg_split( '#{([0-9]+)}#', (string)$destination, -1, PREG_SPLIT_DELIM_CAPTURE );
        $replacement = '';
        foreach ( $pieces as $index => $piece )
            $replacement .= ( $index % 2 ) == 0 ? $piece : '${' . $piece . '}';
        return $replacement;
    }

    /**
     * The first wildcard an address matches, in the order the wildcard cache tries them (the database's), and
     * the address it is turned into. The address is cleaned the way requests are: no leading or trailing slash.
     *
     * @param array $wildcards rows with id, source_url, destination_url, type
     * @param string $address
     * @return array|false id, source, destination (the translated address), type, pattern_destination
     */
    public static function firstMatch( array $wildcards, $address )
    {
        $address = trim( (string)$address, '/ ' );
        foreach ( $wildcards as $wildcard )
        {
            $regexp = self::patternRegexp( $wildcard['source_url'] );
            if ( preg_match( $regexp, $address ) )
            {
                return array( 'id' => (int)$wildcard['id'],
                              'source' => (string)$wildcard['source_url'],
                              'pattern_destination' => (string)$wildcard['destination_url'],
                              'destination' => preg_replace( $regexp, self::destinationReplacement( $wildcard['destination_url'] ), $address ),
                              'type' => (int)$wildcard['type'] );
            }
        }
        return false;
    }

    /**
     * The wildcard type of a kind: TYPE_FORWARD for redirect, TYPE_DIRECT for direct, null for all.
     *
     * @param string $kind see UrlaliasGlobal::kindKey()
     * @return int|null
     */
    public static function typeOfKind( $kind )
    {
        if ( $kind === 'redirect' )
            return \eZURLWildcard::TYPE_FORWARD;
        if ( $kind === 'direct' )
            return \eZURLWildcard::TYPE_DIRECT;
        return null;
    }
}

}
