<?php
/**
 * The code of kernel/pdf/list.php, moved into a class (#207 stage 1). The file kernel/pdf/list.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 * pdf/list: the PDF exports, an overview, a search, a filter, an order and pages, one card per export; Regenerate
 * writes a stored file anew; Remove selected asks first (design:pdf/confirmremove.tpl). What a card says is worked
 * out by expPDFExportInfo (kernel/classes/pdfexport). Guide: doc/guides/pdf-exports.md
 */
/*
 * The original header of kernel/pdf/list.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Pdf
{

class ListView extends \Exponential\Runnable\ModuleView
{
    /** The preference that remembers the page size a user picked */
    const LIMIT_PREFERENCE = 'admin_pdf_list_limit';

    /** The longest search that is used */
    const MAX_SEARCH = 100;

    /**
     * The page sizes the list offers, from admininterface.ini [PaginationSettings], with the configured size among
     * them.
     *
     * @param int $default
     * @param int[] $configured
     * @return int[]
     */
    public static function sizesOf( $default, array $configured )
    {
        $sizes = $configured;
        if ( !in_array( (int)$default, $sizes, true ) )
            $sizes[] = (int)$default;
        sort( $sizes );
        return $sizes;
    }

    /**
     * The page size: the one in the address when the list offers it, else the remembered one, else the default.
     *
     * @param mixed $asked
     * @param mixed $remembered
     * @param int $default
     * @param int[] $sizes
     * @return int
     */
    public static function limitOf( $asked, $remembered, $default, array $sizes )
    {
        foreach ( array( $asked, $remembered ) as $value )
        {
            if ( ( is_int( $value ) || ( is_string( $value ) && ctype_digit( $value ) ) ) && in_array( (int)$value, $sizes, true ) )
                return (int)$value;
        }
        return (int)$default;
    }

    /**
     * The first row of the page asked for, moved back to the last page when the list is shorter (after a removal
     * or a narrower search), and to a whole page.
     *
     * @param int $offset
     * @param int $limit
     * @param int $count
     * @return int
     */
    public static function offsetOf( $offset, $limit, $count )
    {
        $offset = max( 0, (int)$offset );
        $limit = max( 1, (int)$limit );
        if ( $offset >= $count )
            $offset = $count > 0 ? (int)( floor( ( $count - 1 ) / $limit ) * $limit ) : 0;
        return (int)( floor( $offset / $limit ) * $limit );
    }

    /**
     * The search as typed, cut to a length that is used: no control characters, no more than MAX_SEARCH characters.
     *
     * @param mixed $search
     * @return string
     */
    public static function searchOf( $search )
    {
        if ( !is_string( $search ) )
            return '';
        $search = trim( preg_replace( '/[\x00-\x1F\x7F]+/u', ' ', $search ) );
        return mb_substr( $search, 0, self::MAX_SEARCH );
    }

    /**
     * The address of the list with the state given; the parameters left out or empty are left out.
     *
     * @param array $state sort, dir, filter, limit, offset, search
     * @return string
     */
    public static function listURI( array $state )
    {
        $uri = '/pdf/list';
        foreach ( array( 'sort', 'dir', 'filter', 'limit', 'offset' ) as $name )
        {
            if ( isset( $state[$name] ) && (string)$state[$name] !== '' && !( $name === 'offset' && (int)$state[$name] === 0 ) )
                $uri .= '/(' . $name . ')/' . rawurlencode( (string)$state[$name] );
        }
        if ( isset( $state['search'] ) && $state['search'] !== '' )
            $uri .= '?search=' . rawurlencode( $state['search'] );
        return $uri;
    }

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();

        $feedback = $http->hasSessionVariable( Edit::FEEDBACK_KEY ) ? $http->sessionVariable( Edit::FEEDBACK_KEY ) : false;
        if ( $feedback !== false )
            $http->removeSessionVariable( Edit::FEEDBACK_KEY );

        // Create new PDF Export
        if ( $Module->isCurrentAction( 'NewExport' ) )
        {
            return $this->viewResult( null, $Module->redirect( 'pdf', 'edit' ) );
        }

        // Regenerate: writes the stored file of one export anew, then back to the list with what happened.
        if ( $http->hasPostVariable( 'RegenerateButton' ) )
        {
            $ids = \expPDFExportInfo::idList( $http->postVariable( 'RegenerateButton' ) );
            $export = $ids ? \eZPDFExport::fetch( $ids[0] ) : null;
            if ( $export instanceof \eZPDFExport )
            {
                $result = \expPDFExportGenerator::generateFile( $export );
                $feedback = array( 'type' => $result['ok'] ? 'regenerated' : 'generate_failed', 'id' => $ids[0],
                                   'name' => (string)$export->attribute( 'title' ), 'problem' => $result['problem'],
                                   'size' => $result['facts']['size'] );
            }
            else
            {
                $feedback = array( 'type' => 'gone', 'names' => array() );
            }
            $http->setSessionVariable( Edit::FEEDBACK_KEY, $feedback );
            $Module->redirectTo( '/pdf/list' );
            return $this->viewResult( null, null );
        }

        // Removing takes two steps. Remove selected only shows what would go (design:pdf/confirmremove.tpl); its
        // button posts the same ids again with ConfirmRemoveButton, and only then is anything removed. The result
        // comes back after a redirect, so a reload never removes twice.
        if ( $Module->isCurrentAction( 'RemoveExport' ) )
        {
            $ids = \expPDFExportInfo::idList( $Module->hasActionParameter( 'DeleteIDArray' ) ? $Module->actionParameter( 'DeleteIDArray' ) : array() );
            if ( !$ids )
            {
                $feedback = array( 'type' => 'none_selected', 'names' => array() );
            }
            else if ( !$http->hasPostVariable( 'ConfirmRemoveButton' ) )
            {
                $items = array();
                $classNames = array();
                foreach ( $ids as $id )
                {
                    $export = \eZPDFExport::fetch( $id );
                    if ( $export instanceof \eZPDFExport )
                        $items[] = array( 'object' => $export, 'info' => \expPDFExportInfo::info( $export, $classNames ) );
                }
                if ( $items )
                {
                    $tpl = \eZTemplate::factory();
                    $tpl->setVariable( 'pdf_remove_items', $items );
                    $Result = array();
                    $Result['content'] = $tpl->fetch( 'design:pdf/confirmremove.tpl' );
                    $Result['path'] = array( array( 'url' => 'pdf/list', 'text' => \ezpI18n::tr( 'kernel/pdf', 'PDF Export' ) ),
                                             array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/pdf/list', 'Confirm removal' ) ) );
                    return $this->viewResult( $Result, null );
                }
                $feedback = array( 'type' => 'gone', 'names' => array() );
            }
            else
            {
                $removed = \expPDFExportInfo::remove( $ids );
                $http->setSessionVariable( Edit::FEEDBACK_KEY, array( 'type' => 'removed', 'names' => $removed['names'],
                                                                      'files' => $removed['files'] ) );
                $Module->redirectTo( '/pdf/list' );
                return $this->viewResult( null, null );
            }
        }

        // ---- the list ----
        $all = \eZPDFExport::fetchList();
        $exports = array();
        $infos = array();
        $classNames = array();
        foreach ( $all as $export )
        {
            $id = (int)$export->attribute( 'id' );
            $exports[$id] = $export;
            $infos[$id] = \expPDFExportInfo::info( $export, $classNames );
        }
        $summary = \expPDFExportInfo::summaryOf( $infos, count( \expPDFExportInfo::unfinished() ) );

        $user = isset( $scope['Params']['UserParameters'] ) ? (array)$scope['Params']['UserParameters'] : array();
        $params = $scope['Params'];
        $param = function ( $name ) use ( $params, $user )
        {
            $key = ucfirst( $name );
            if ( isset( $params[$key] ) && $params[$key] !== false )
                return (string)$params[$key];
            return isset( $user[$name] ) ? (string)$user[$name] : '';
        };

        $search = self::searchOf( $http->hasGetVariable( 'search' ) ? $http->getVariable( 'search' ) : rawurldecode( $param( 'search' ) ) );
        $filter = in_array( $param( 'filter' ), \expPDFExportInfo::FILTERS, true ) ? $param( 'filter' ) : '';
        $sort = \expPDFExportInfo::sortOf( $param( 'sort' ), $param( 'dir' ) );

        $default = \expAdminPagination::limit( 'pdf/list' );
        $sizes = self::sizesOf( $default, \expAdminPagination::sizes( 'pdf/list' ) );
        $loggedIn = \eZUser::currentUser()->isRegistered();
        $remembered = $loggedIn ? \eZPreferences::value( self::LIMIT_PREFERENCE ) : false;
        $limit = self::limitOf( $param( 'limit' ), $remembered, $default, $sizes );
        if ( $loggedIn && $param( 'limit' ) !== '' && (string)$limit === $param( 'limit' ) && (string)$remembered !== (string)$limit )
            \eZPreferences::setValue( self::LIMIT_PREFERENCE, $limit );

        $matching = \expPDFExportInfo::sortList( \expPDFExportInfo::filterOf( $infos, $search, $filter ), $sort );
        $count = count( $matching );
        $offset = self::offsetOf( \expAdminPagination::offset( $scope['Params'] ), $limit, $count );
        $page = array_slice( $matching, $offset, $limit, true );

        $exportList = array();
        $pageInfo = array();
        foreach ( $page as $id => $info )
        {
            $exportList[$id] = $exports[$id];
            $pageInfo[$id] = $info;
        }

        $state = array( 'sort' => $sort['field'], 'dir' => $sort['direction'], 'filter' => $filter,
                        'limit' => $limit === $default ? '' : $limit, 'search' => $search );
        $sortLinks = array();
        foreach ( \expPDFExportInfo::SORT_FIELDS as $field )
        {
            $current = $field === $sort['field'];
            $fieldSort = \expPDFExportInfo::sortOf( $field, $current ? $sort['opposite'] : null );
            $sortLinks[$field] = array( 'uri' => self::listURI( array( 'sort' => $field, 'dir' => $fieldSort['direction'] ) + $state ),
                                        'current' => $current, 'direction' => $sort['direction'] );
        }
        $filterLinks = array();
        foreach ( array_merge( array( '' ), \expPDFExportInfo::FILTERS ) as $name )
            $filterLinks[$name === '' ? 'all' : $name] = array( 'uri' => self::listURI( array( 'filter' => $name ) + $state ),
                                                                'current' => $name === $filter,
                                                                'count' => count( \expPDFExportInfo::filterOf( $infos, $search, $name ) ) );
        $limitLinks = array();
        foreach ( $sizes as $size )
            $limitLinks[] = array( 'limit' => $size, 'uri' => self::listURI( array( 'limit' => $size ) + $state ), 'current' => $size === $limit );

        $tpl = \eZTemplate::factory();

        // The variables the list has always had
        $tpl->setVariable( 'pdfexport_list', $exportList );
        $tpl->setVariable( 'pdfexport_count', $count );
        $tpl->setVariable( 'limit', $limit );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );

        $tpl->setVariable( 'pdfexport_info', $pageInfo );
        $tpl->setVariable( 'pdfexport_summary', $summary );
        $tpl->setVariable( 'pdfexport_feedback', $feedback );
        $tpl->setVariable( 'pdfexport_search', $search );
        $tpl->setVariable( 'pdfexport_filter', $filter );
        $tpl->setVariable( 'pdfexport_sort', $sort + array( 'links' => $sortLinks ) );
        $tpl->setVariable( 'pdfexport_filter_links', $filterLinks );
        $tpl->setVariable( 'pdfexport_limit_links', $limitLinks );
        // For the pager (design:navigator/google.tpl): the state it carries along, with the search after it
        $tpl->setVariable( 'pdfexport_pager_parameters', array( 'offset' => $offset, 'sort' => $sort['field'], 'dir' => $sort['direction'],
                                                                'filter' => $filter, 'limit' => $state['limit'] ) );
        $tpl->setVariable( 'pdfexport_pager_suffix', $search !== '' ? '?search=' . rawurlencode( $search ) : '' );
        $tpl->setVariable( 'pdfexport_list_uri', self::listURI( $state ) );
        // The search form sends search=... to this address, which keeps the order, the filter and the page size
        $tpl->setVariable( 'pdfexport_search_action', self::listURI( array( 'search' => '' ) + $state ) );
        $tpl->setVariable( 'pdfexport_storage_directory', \expPDFExportFile::directory() );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:pdf/list.tpl" );
        $Result['path'] = array( array( 'url' => 'pdf/list',
                                        'text' => \ezpI18n::tr( 'kernel/pdf', 'PDF Export' ) ) );

        return $this->viewResult( $Result, null );
    }
}

}
