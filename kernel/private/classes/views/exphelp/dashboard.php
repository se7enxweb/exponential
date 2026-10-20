<?php
/**
 * exphelp/dashboard (doc/en/us/features/6.0/help-system.md): the help start page. Template design:exphelp/dashboard.tpl.
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
        $tpl->setVariable( 'help_audiences', \expHelp::translatedNames( $index->audiences() ) );
        $tpl->setVariable( 'help_categories', \expHelp::translatedNames( $index->categories() ) );
        $tpl->setVariable( 'help_featured', $featured );
        $tpl->setVariable( 'help_recent', $index->recentChanges( (int)\expHelp::setting( 'DashboardSettings', 'RecentLimit', 8 ) ) );
        $tpl->setVariable( 'help_audience_pages', self::audiencePages( $documents, (int)\expHelp::setting( 'DashboardSettings', 'AudiencePageLimit', 4 ),
            (array)\expHelp::setting( 'DashboardSettings', 'AudienceExcludeTypes', array( 'changelog', 'history', 'bc', 'specification' ) ) ) );
        $tpl->setVariable( 'help_stats', $stats );
        $tpl->setVariable( 'help_tree', $index->tree() );
        $tpl->setVariable( 'help_trackers', \expHelp::translatedNames( $report->trackers(), array( 'label', 'description' ) ) );
        $tpl->setVariable( 'help_search_url', \expHelp::url( 'search' ) );
        $tpl->setVariable( 'help_report_url', \expHelp::url( 'report' ) );
        // help maintained as content (doc/en/us/features/6.0/help-content.md): the manuals and topics the user may see
        $tpl->setVariable( 'help_content', self::contentSummary() );
        // the fifth step of the grow band, only when its page is in the help (it never links a missing page)
        $tpl->setVariable( 'help_grow_extensions_page', $index->document( 'help/contribute-extensions-back.md' ) !== null ? 'help/contribute-extensions-back.md' : '' );
        // the Support Exponential block at the bottom ([SupportSettings]); a worker started before the class existed skips it
        $tpl->setVariable( 'help_support', class_exists( Audience::class ) ? Audience::support() : false );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/dashboard.tpl' );
        $Result['path'] = array( array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ) );
        return $Result;
    }

    /**
     * Up to $limit pages per audience: featured pages first, then help pages, guides, installation and feature pages,
     * then the lowest order, then by title. Hidden pages and the types of $excludeTypes (release notes, history,
     * changes of behaviour, specifications: [DashboardSettings] AudienceExcludeTypes[]) are never picked; they are
     * reached through the search and the categories.
     *
     * @param array $documents id => document
     * @param int $limit
     * @param string[]|null $excludeTypes null: changelog, history, bc, specification
     * @return array audience => list of documents
     */
    public static function audiencePages( array $documents, $limit, ?array $excludeTypes = null )
    {
        if ( $excludeTypes === null )
            $excludeTypes = array( 'changelog', 'history', 'bc', 'specification' );
        $rank = array( 'help' => 0, 'guide' => 1, 'install' => 2, 'feature' => 3 );
        $documents = array_filter( $documents, function ( $d ) use ( $excludeTypes ) {
            return empty( $d['hidden'] ) && !in_array( isset( $d['type'] ) ? (string)$d['type'] : '', $excludeTypes, true );
        } );
        uasort( $documents, function ( $a, $b ) use ( $rank ) {
            $fa = empty( $a['featured'] ) ? 1 : 0;
            $fb = empty( $b['featured'] ) ? 1 : 0;
            if ( $fa !== $fb )
                return $fa - $fb;
            $ra = isset( $a['type'], $rank[$a['type']] ) ? $rank[$a['type']] : 9;
            $rb = isset( $b['type'], $rank[$b['type']] ) ? $rank[$b['type']] : 9;
            if ( $ra !== $rb )
                return $ra - $rb;
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

    /**
     * The manuals and the newest topics of help maintained as content the user may see, false when it is not installed
     *
     * @return array|false array( 'manuals' => list of title, remote_id, shortdesc, preview; 'topics' => documents, newest
     *                     first, at most 6; 'count' => int )
     */
    public static function contentSummary()
    {
        if ( !class_exists( 'expHelpContentProvider' ) || !\expHelpContentProvider::isEnabled() )
            return false;
        try
        {
            $provider = \expHelpContentProvider::instance();
            $topics = array();
            foreach ( $provider->topics() as $topic )
                $topics[] = \expHelpContentProvider::document( $topic );
            usort( $topics, function ( $a, $b ) { return $b['mtime'] <=> $a['mtime']; } );
            $manuals = array();
            foreach ( $provider->manuals() as $manual )
                $manuals[] = array( 'title' => $manual['title'], 'remote_id' => $manual['remote_id'], 'shortdesc' => $manual['shortdesc'], 'preview' => $manual['preview'] );
            return array( 'manuals' => $manuals, 'topics' => array_slice( $topics, 0, 6 ), 'count' => count( $topics ) );
        }
        catch ( \Exception $e )
        {
            \eZDebug::writeWarning( 'Help topics could not be read: ' . $e->getMessage(), __METHOD__ );
            return false;
        }
    }
}

}
