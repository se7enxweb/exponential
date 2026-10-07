<?php
/**
 * The code of kernel/content/urlalias_global.php, moved into a class (#207 stage 1). The file kernel/content/urlalias_global.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/urlalias_global.php:
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

class UrlaliasGlobal extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();

        $Offset = $Params['Offset'];
        if ( !is_numeric( $Offset ) || $Offset < 0 )
            $Offset = 0;
        $Offset = (int)$Offset;
        $viewParameters = array( 'offset' => $Offset );

        // A search over the alias and its destination, and the kind of alias, both off the address
        $search = \Exponential\View\Kernel\Url\ListView::searchText( $http->hasGetVariable( 'q' ) ? $http->getVariable( 'q' ) : '' );
        $userParameters = isset( $scope['Params']['UserParameters'] ) ? (array)$scope['Params']['UserParameters'] : array();
        $kind = self::kindKey( isset( $userParameters['kind'] ) ? $userParameters['kind'] : '' );
        // What the form held, given back to it when the alias could not be created
        $aliasForm = array( 'language' => false, 'all_languages' => false, 'redirects' => true );

        $tpl = \eZTemplate::factory();
        $limit = 20;

        // TODO: For PHP 5, merge similar code in urlalias.php and urlalias_global.php into a function/class.

        $infoCode = 'no-errors'; // This will be modified if info/warning is given to user.
        $infoData = array(); // Extra parameters can be added to this array
        $aliasText = false;
        $aliasDestinationText = false;
        $aliasOutputText = false;
        $aliasOutputDestinationText = false;

        if ( $Module->isCurrentAction( 'RemoveAllAliases' ) )
        {
            $filter = new \eZURLAliasQuery();
            $filter->actionTypesEx = array( 'eznode', 'nop' );
            $filter->offset = 0;
            $filter->limit = 50;

            while ( true )
            {
                $aliasList = $filter->fetchAll();
                if ( count( $aliasList ) == 0 )
                    break;
                foreach ( $aliasList as $alias )
                {
                    $parentID = (int)$alias->attribute( 'parent' );
                    $textMD5  = $alias->attribute( 'text_md5' );
                    $language = $alias->attribute( 'language_object' );
                    \eZURLAliasML::removeSingleEntry( $parentID, $textMD5, $language );
                }
                $filter->prepare();
            }
            $infoCode = "feedback-removed-all";
        }
        else if ( $Module->isCurrentAction( 'RemoveAlias' ) )
        {
            if ( $http->hasPostVariable( 'ElementList' ) )
            {
                $elementList = $http->postVariable( 'ElementList' );
                if ( !is_array( $elementList ) )
                    $elementList = array();
                foreach ( $elementList as $element )
                {
                    if ( preg_match( "#^([0-9]+).([a-fA-F0-9]+).([a-zA-Z0-9-]+)$#", $element, $matches ) )
                    {
                        $parentID = (int)$matches[1];
                        $textMD5  = $matches[2];
                        $language = $matches[3];
                        \eZURLAliasML::removeSingleEntry( $parentID, $textMD5, $language );
                    }
                }
                $infoCode = "feedback-removed";
            }
        }
        else if ( $Module->isCurrentAction( 'NewAlias' ) )
        {
            $aliasText = trim( $Module->actionParameter( 'AliasSourceText' ) );
            $aliasDestinationTextUnmodified = $Module->actionParameter( 'AliasDestinationText' );
            $aliasDestinationText = trim( $aliasDestinationTextUnmodified, " \t\r\n\0\x0B/" );
            $isAlwaysAvailable = $http->hasPostVariable( 'AllLanguages' ) && strlen( trim( $http->postVariable( 'AllLanguages' ) ) ) > 0;
            $languageCode = $Module->actionParameter( 'LanguageCode' );
            $language = \eZContentLanguage::fetchByLocale( $languageCode );
            $aliasRedirects  = $http->hasPostVariable( 'AliasRedirects' ) && $http->postVariable( 'AliasRedirects' );
            $aliasForm = array( 'language' => is_string( $languageCode ) ? $languageCode : false,
                                'all_languages' => $isAlwaysAvailable,
                                'redirects' => (bool)$aliasRedirects );

            if ( !$language )
            {
                $infoCode = "error-invalid-language";
                $infoData['language'] = $languageCode;
            }
            else if ( strlen( $aliasText ) == 0 )
            {
                $infoCode = "error-no-alias-text";
            }
            else if ( strlen( trim( $aliasDestinationTextUnmodified ) ) == 0 )
            {
                $infoCode = "error-no-alias-destination-text";
            }
            else
            {
                $parentID = 0; // Start from the top
                $linkID   = 0;
                $mask = $language->attribute( 'id' );
                if ( $isAlwaysAvailable )
                    $mask |= 1;

                $action = \eZURLAliasML::urlToAction( $aliasDestinationText );
                if ( !$action )
                {
                    $elements = \eZURLAliasML::fetchByPath( $aliasDestinationText );
                    if ( count( $elements ) > 0 )
                    {
                        $action = $elements[0]->attribute( 'action' );
                        $linkID = $elements[0]->attribute( 'link' );
                    }
                }
                if ( !$action )
                {
                    $infoCode = "error-action-invalid";
                    $infoData['aliasText'] = $aliasDestinationText;
                }
                else
                {
                    $origAliasText = $aliasText;
                    if ( $linkID == 0 )
                        $linkID = true;
                    $result = \eZURLAliasML::storePath( $aliasText, $action,
                                                       $language, $linkID, $isAlwaysAvailable, $parentID,
                                                       true, false, false, $aliasRedirects );
                    if ( $result['status'] === \eZURLAliasML::LINK_ALREADY_TAKEN )
                    {
                        $lastElements = \eZURLAliasML::fetchByPath( $result['path'] );
                        if ( count ( $lastElements ) > 0 )
                        {
                            $lastElement  = $lastElements[0];
                            $infoCode = "feedback-alias-exists";
                            $infoData['new_alias'] = $aliasText;
                            $infoData['url'] = $lastElement->attribute( 'path' );
                            $infoData['action_url'] = $lastElement->actionURL();
                            $aliasText = $origAliasText;
                        }
                    }
                    else if ( $result['status'] === true )
                    {
                        $aliasText = $result['path'];
                        if ( strcmp( $aliasText, $origAliasText ) != 0 )
                        {
                            $infoCode = "feedback-alias-cleanup";
                            $infoData['orig_alias']  = $origAliasText;
                            $infoData['new_alias'] = $aliasText;
                        }
                        else
                        {
                            $infoData['new_alias'] = $aliasText;
                        }
                        if ( $infoCode == 'no-errors' )
                        {
                            $infoCode = "feedback-alias-created";
                        }
                        $aliasText = false;
                        $aliasOutputText = false;
                        $aliasOutputDestinationText = false;
                    }
                    if ( preg_match( "#^eznode:(.+)$#", $action, $matches ) )
                    {
                        $infoData['node_id'] = $matches[1];
                    }
                }
            }
        }

        // An alias that was not created leaves what was typed in the form, so it can be corrected instead of retyped
        if ( $Module->isCurrentAction( 'NewAlias' ) && ( strpos( $infoCode, 'error-' ) === 0 || $infoCode === 'feedback-alias-exists' ) )
        {
            $aliasOutputText = is_string( $aliasText ) ? $aliasText : '';
            $aliasOutputDestinationText = isset( $aliasDestinationTextUnmodified ) && is_string( $aliasDestinationTextUnmodified ) ? trim( $aliasDestinationTextUnmodified ) : '';
        }

        // Audit (doc/bc/6.0/audit.md, content.urlalias.change): an alias or wildcard added or removed
        if ( class_exists( 'expAuditHook' ) && in_array( $infoCode, array( 'feedback-removed-all', 'feedback-removed', 'feedback-alias-created',
                                                                           'feedback-alias-cleanup', 'feedback-wildcard-removed-all',
                                                                           'feedback-wildcard-removed', 'feedback-wildcard-created' ), true ) )
            \expAuditHook::emit( 'content.urlalias.change', function () use ( $infoCode, $infoData, $Module ) {
                $removed = strpos( $infoCode, 'removed' ) !== false;
                $alias = isset( $infoData['new_alias'] ) ? (string)$infoData['new_alias'] : null;
                return array( 'object' => array( 'type' => 'alias', 'id' => $alias !== null ? $alias : $infoCode ),
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
        $limitID = \eZPreferences::value( 'admin_urlalias_list_limit' );
        foreach ( $limitList as $limitEntry )
        {
            $limitIDs[]                     = $limitEntry['id'];
            $limitValues[$limitEntry['id']] = $limitEntry['value'];
        }
        if ( !in_array( $limitID, $limitIDs ) )
        {
            $limitID = 2;
        }

        // Fetch global custom aliases (excluding eznode)
        $filter = new \eZURLAliasQuery();
        $filter->actionTypesEx = array( 'eznode', 'nop' );
        $filter->offset = $Offset;
        $filter->limit = $limitValues[$limitID];
        // The search (the last part of the path or the destination) and the kind are conditions of the query, so
        // a page costs one count and one page query whatever the number of aliases
        $filter->search = $search !== '' ? $search : null;
        $filter->redirects = $kind === 'all' ? null : $kind === 'redirect';

        // Prime the internal data for the template, for PHP5 this is no longer needed since objects will not be copied anymore in the template code.
        $count = $filter->count();
        $aliasCount = (int)$count;
        if ( $Offset > 0 && $Offset >= $aliasCount )
        {
            // a removal or a search left the page behind the end of the list
            $Offset = 0;
            $filter->offset = 0;
            $filter->prepare();
            $viewParameters['offset'] = 0;
        }
        $aliasList = $filter->fetchAll();
        if ( $filter->search === null && $filter->redirects === null )
        {
            $totalCount = $aliasCount;
        }
        else
        {
            $all = new \eZURLAliasQuery();
            $all->actionTypesEx = array( 'eznode', 'nop' );
            $totalCount = (int)$all->count();
        }

        $aliasInfo = array();
        foreach ( $aliasList as $element )
            $aliasInfo[self::elementKey( $element )] = self::aliasInfo( $element );
        if ( $kind !== 'all' )
            $viewParameters['kind'] = $kind;
        $path = array();
        $path[] = array( 'url'  => false,
                         'text' => \ezpI18n::tr( 'kernel/content/urlalias_global', 'Global URL aliases' ) );

        $languages = \eZContentLanguage::prioritizedLanguages();

        $tpl->setVariable( 'filter', $filter );
        $tpl->setVariable( 'languages', $languages );
        $tpl->setVariable( 'info_code', $infoCode );
        $tpl->setVariable( 'info_data', $infoData );
        $tpl->setVariable( 'aliasSourceText', $aliasOutputText );
        $tpl->setVariable( 'aliasDestinationText', $aliasOutputDestinationText );
        $tpl->setVariable( 'limitList', $limitList );
        $tpl->setVariable( 'limitID', $limitID );
        $tpl->setVariable( 'view_parameters', $viewParameters );
        // since 6.0.15: the page of aliases shown (the filter's page, or the matches of a search), how many there
        // are, what each one points to, the search, the kind and what the form held
        $tpl->setVariable( 'alias_list', $aliasList );
        $tpl->setVariable( 'alias_count', $aliasCount );
        $tpl->setVariable( 'alias_total_count', $totalCount );
        $tpl->setVariable( 'alias_info', $aliasInfo );
        $tpl->setVariable( 'alias_limit', $limitValues[$limitID] );
        $tpl->setVariable( 'alias_search', $search );
        $tpl->setVariable( 'alias_search_suffix', $search !== '' ? '?q=' . rawurlencode( $search ) : '' );
        $tpl->setVariable( 'alias_kind', $kind );
        $tpl->setVariable( 'alias_form', $aliasForm );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/urlalias_global.tpl' );
        $Result['path'] = $path;

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * The kind of alias asked for: 'all', 'redirect' (answers with a 301 to its destination) or 'direct' (shows
     * the destination under the alias's own address).
     *
     * @param mixed $value
     * @return string
     */
    public static function kindKey( $value )
    {
        return in_array( $value, array( 'redirect', 'direct' ), true ) ? $value : 'all';
    }

    /**
     * The value of an alias in the ElementList[] of the removal: parent, text md5 and language.
     *
     * @param \eZPathElement $element
     * @return string
     */
    public static function elementKey( $element )
    {
        $language = $element->attribute( 'language_object' );
        return $element->attribute( 'parent' ) . '.' . $element->attribute( 'text_md5' ) . '.'
             . ( is_object( $language ) ? $language->attribute( 'locale' ) : '' );
    }

    /**
     * What an alias's action points to: a module view ('module'), a node ('node'), another kind ('other') or
     * nothing that can be resolved ('none'), with the module, the view and whether the module exists.
     *
     * @param string $action e.g. module:user/login, eznode:2, nop:
     * @param callable|null $moduleExists given a module name, true when it exists (eZModule::exists by default)
     * @return array kind, url, module, view, node_id, module_exists
     */
    public static function destinationOf( $action, $moduleExists = null )
    {
        $action = (string)$action;
        $info = array( 'kind' => 'none', 'url' => '', 'module' => '', 'view' => '', 'node_id' => 0, 'module_exists' => false );
        if ( preg_match( '#^module:(.*)$#', $action, $matches ) )
        {
            $url = trim( $matches[1], '/' );
            $parts = explode( '/', $url );
            $info['kind'] = 'module';
            $info['url'] = $url;
            $info['module'] = $parts[0];
            $info['view'] = isset( $parts[1] ) ? $parts[1] : '';
            if ( $moduleExists === null )
                $moduleExists = function ( $name ) { return \eZModule::exists( $name ) !== null; };
            $info['module_exists'] = $parts[0] !== '' && (bool)call_user_func( $moduleExists, $parts[0] );
        }
        else if ( preg_match( '#^eznode:([0-9]+)$#', $action, $matches ) )
        {
            $info['kind'] = 'node';
            $info['node_id'] = (int)$matches[1];
            $info['url'] = 'content/view/full/' . (int)$matches[1];
        }
        else if ( $action !== '' && $action !== 'nop:' )
        {
            $info['kind'] = 'other';
            $info['url'] = $action;
        }
        return $info;
    }

    /**
     * What the page shows of an alias besides its row: its whole path, its destination and what that resolves to.
     *
     * @param \eZPathElement $element
     * @return array path, redirects, destination (see destinationOf())
     */
    public static function aliasInfo( $element )
    {
        return array( 'path' => (string)$element->attribute( 'path' ),
                      'redirects' => (bool)$element->attribute( 'alias_redirects' ),
                      'destination' => self::destinationOf( $element->attribute( 'action' ) ) );
    }
}

}
