<?php
/**
 * Setup > Site data (setup/sitedata): the rows of ezsite_data (eZSiteData), such as ezpublish-version, read only,
 * by name, with a filter (a whole name, or * for any characters) and paging. Each row says whether templates may
 * read it (site.ini [SiteDataSettings] TemplateReadable[], fetch( 'content', 'site_data', ... )). Policy
 * setup/system_info. Nothing on the page writes a row. A value whose name holds a secret is not shown, and secrets
 * inside other values are masked (shownValue()).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 94b7b6c239), Copyright (C) Mugo Web, GPL v2.
 */

namespace Exponential\View\Kernel\Setup
{

class Sitedata extends \Exponential\Runnable\ModuleView
{
    /** rows per page */
    const LIMIT = 50;

    /** the most characters of a filter the page keeps */
    const FILTER_MAX = 100;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();

        // the filter comes from the form (GET or POST) or from the (filter) parameter of a paging link
        $filter = '';
        if ( $http->hasVariable( 'Filter' ) )
            $filter = (string)$http->variable( 'Filter' );
        elseif ( isset( $Params['Filter'] ) )
            $filter = (string)$Params['Filter'];
        $filter = self::cleanFilter( $filter );

        $offset = isset( $Params['Offset'] ) ? max( 0, (int)$Params['Offset'] ) : 0;
        $count = \eZSiteData::fetchListCount( $filter );
        if ( $offset >= $count && $count > 0 )
            $offset = (int)( floor( ( $count - 1 ) / self::LIMIT ) * self::LIMIT );

        $readable = \eZSiteData::templateReadableNames();
        $rows = array();
        foreach ( (array)\eZSiteData::fetchList( $filter, $offset, self::LIMIT ) as $row )
        {
            $name = (string)$row->attribute( 'name' );
            $rows[] = array( 'name' => $name, 'value' => self::shownValue( $name, (string)$row->attribute( 'value' ) ),
                             'template_readable' => in_array( $name, $readable, true ) );
        }

        $viewParameters = array( 'offset' => $offset );
        if ( $filter !== '' )
            $viewParameters['filter'] = $filter;

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'rows', $rows );
        $tpl->setVariable( 'count', $count );
        $tpl->setVariable( 'limit', self::LIMIT );
        $tpl->setVariable( 'filter', $filter );
        $tpl->setVariable( 'template_readable', $readable );
        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'page_uri', '/setup/sitedata' );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/sitedata.tpl' );
        $Result['path'] = array( array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/setup', 'Site data' ) ) );
        return $Result;
    }

    /**
     * A row's value as the page shows it: only whether it is set for a name that holds a secret
     * (expSystemReportMask::isSecretName()), and with the secrets inside it masked otherwise
     * (a password in an address, a "token=..." pair, a long key). Pure.
     *
     * @param string $name
     * @param string $value
     * @return string
     */
    public static function shownValue( $name, $value )
    {
        if ( \expSystemReportMask::isSecretName( (string)$name ) )
            return \expSystemReportMask::secret( (string)$value );
        return \expSystemReportMask::text( (string)$value );
    }

    /**
     * A filter as the page uses it: trimmed, at most FILTER_MAX characters, only characters a name can have.
     *
     * @param string $filter
     * @return string
     */
    public static function cleanFilter( $filter )
    {
        $filter = trim( (string)$filter );
        if ( function_exists( 'mb_substr' ) )
            $filter = mb_substr( $filter, 0, self::FILTER_MAX, 'UTF-8' );
        else
            $filter = substr( $filter, 0, self::FILTER_MAX );
        return preg_replace( '/[^\p{L}\p{N}*_.:\-]+/u', '', $filter );
    }
}

}
