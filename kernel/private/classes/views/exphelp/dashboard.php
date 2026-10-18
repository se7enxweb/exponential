<?php
/**
 * exphelp/dashboard (doc/features/6.0/help-system.md): the help start page. Template design:exphelp/dashboard.tpl.
 *
 * Template variables: help_audiences (id => id, name, count), help_categories (id => id, name, icon, count),
 * help_featured (documents), help_recent (id, title, time, subject), help_tree, help_trackers (expHelpIssueReport),
 * help_search_url, help_report_url.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Dashboard extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Module = $scope['Params']['Module'];
        if ( !\expHelp::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $index = \expHelpDocumentIndex::instance();
        $featured = array();
        foreach ( (array)\expHelp::setting( 'FeaturedSettings', 'PageList', array() ) as $id )
        {
            $document = $index->document( (string)$id );
            if ( $document !== null )
                $featured[] = $document;
        }
        $report = new \expHelpIssueReport();
        $documents = $index->documents();
        $stats = self::stats( $documents, $index->categories() );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'help_i18n_json', \expHelp::clientStringsJson() );
        $tpl->setVariable( 'help_audiences', $index->audiences() );
        $tpl->setVariable( 'help_categories', $index->categories() );
        $tpl->setVariable( 'help_featured', $featured );
        $tpl->setVariable( 'help_recent', $index->recentChanges( (int)\expHelp::setting( 'DashboardSettings', 'RecentLimit', 8 ) ) );
        $tpl->setVariable( 'help_audience_pages', self::audiencePages( $documents, (int)\expHelp::setting( 'DashboardSettings', 'AudiencePageLimit', 4 ) ) );
        $tpl->setVariable( 'help_stats', $stats );
        $tpl->setVariable( 'help_tree', $index->tree() );
        $tpl->setVariable( 'help_trackers', $report->trackers() );
        $tpl->setVariable( 'help_search_url', \expHelp::url( 'search' ) );
        $tpl->setVariable( 'help_report_url', \expHelp::url( 'report' ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/dashboard.tpl' );
        $Result['path'] = array( array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ) );
        return $Result;
    }

    /**
     * Up to $limit pages per audience: the lowest order first, then by title.
     *
     * @param array $documents id => document
     * @param int $limit
     * @return array audience => list of documents
     */
    public static function audiencePages( array $documents, $limit )
    {
        uasort( $documents, function ( $a, $b ) {
            $oa = isset( $a['order'] ) ? (int)$a['order'] : 1000;
            $ob = isset( $b['order'] ) ? (int)$b['order'] : 1000;
            return $oa !== $ob ? $oa - $ob : strcasecmp( (string)( $a['title'] ?? '' ), (string)( $b['title'] ?? '' ) );
        } );
        $pages = array();
        foreach ( $documents as $document )
        {
            foreach ( isset( $document['audience'] ) ? (array)$document['audience'] : array() as $audience )
            {
                if ( !isset( $pages[$audience] ) )
                    $pages[$audience] = array();
                if ( count( $pages[$audience] ) < $limit )
                    $pages[$audience][] = $document;
            }
        }
        return $pages;
    }

    /**
     * @param array $documents
     * @param array $categories
     * @return array array( 'pages' => int, 'categories' => int )
     */
    public static function stats( array $documents, array $categories )
    {
        return array( 'pages' => count( $documents ), 'categories' => count( $categories ) );
    }
}

}
