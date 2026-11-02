<?php
/**
 * The moderation/recent view (Dashboard > Recent content): the most recently created or modified objects, a page at a
 * time (expModerationList), with the filters, the sorting, the page sizes (remembered per user, the preference
 * admin_moderation_limit) and, with moderation/act, the selection and the actions (posted to moderation/action). The
 * address and the browser of a submission are read from the audit index for users with moderation/personaldata only.
 *
 * The filter form is posted to the view (FilterButton and the fields), which redirects to the same filters as view
 * parameters; no filter is read from the query string. Every page of a filter has one address (bookmarks, the pager,
 * the sort links). Policy moderation/read.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Moderation
{

class Recent extends \Exponential\Runnable\ModuleView
{
    /** How long the page may spend bringing the audit index up to date before it reads the submissions (seconds) */
    const INDEX_SECONDS = 0.5;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $settings = \expModerationList::settings();

        // the filter form (POST): answered with a redirect to the same filters as view parameters, the first page.
        // A post has no query string, so the redirect cannot carry one over
        $http = \eZHTTPTool::instance();
        if ( $http->hasPostVariable( 'FilterButton' ) )
        {
            $in = array();
            foreach ( array( 'from', 'to', 'date', 'class', 'status', 'hidden', 'held', 'section', 'siteaccess', 'sort', 'order' ) as $key )
                $in[$key] = $http->hasPostVariable( $key ) && is_string( $http->postVariable( $key ) ) ? $http->postVariable( $key ) : '';
            $creator = $http->hasPostVariable( 'creator' ) ? $http->postVariable( 'creator' ) : '';
            $in['creator'] = is_string( $creator ) && $creator !== '' ? (string)\expModerationAdmin::creatorID( $creator ) : '';
            $f = \expModerationList::filters( $in, $settings['sizes'], $settings['default'] );
            return $Module->redirectTo( '/moderation/recent' . \expModerationList::viewParameters( $f ) );
        }

        $in = array();
        foreach ( array( 'from' => 'From', 'to' => 'To', 'date' => 'Date', 'class' => 'Class', 'status' => 'Status', 'hidden' => 'Hidden',
                         'held' => 'Held', 'section' => 'Section', 'siteaccess' => 'Siteaccess', 'creator' => 'Creator', 'sort' => 'Sort',
                         'order' => 'Order', 'offset' => 'Offset' ) as $key => $param )
            $in[$key] = isset( $Params[$param] ) && is_scalar( $Params[$param] ) ? rawurldecode( (string)$Params[$param] ) : '';
        $sizeChoice = (int)\eZPreferences::value( \expModerationList::PREFERENCE );
        $in['limit'] = (string)\expModerationList::chosenSize( $settings['sizes'], $sizeChoice );
        $f = \expModerationList::filters( $in, $settings['sizes'], $settings['default'] );

        $personal = \expModerationAdmin::canDo( 'personaldata' );
        $started = microtime( true );
        $list = new \expModerationList();
        $problem = '';
        if ( $personal && $list->auditUsable() && class_exists( 'expAuditConsole' ) )
        {
            try
            {
                \expAuditConsole::refreshIndex( self::INDEX_SECONDS );
            }
            catch ( \Throwable $e )
            {
                \eZDebug::writeWarning( 'The audit index could not be brought up to date: ' . $e->getMessage(), __METHOD__ );
            }
        }
        try
        {
            $page = $list->page( $f, $personal );
        }
        catch ( \Throwable $e )
        {
            $page = array( 'rows' => array(), 'total' => 0, 'offset' => 0, 'limit' => $f['limit'], 'queries' => 0, 'audit' => false );
            $problem = $e->getMessage();
        }
        $elapsed = (int)round( ( microtime( true ) - $started ) * 1000 );
        $f['offset'] = $page['offset'];

        $creatorName = '';
        if ( $f['creator'] )
        {
            $creator = \eZContentObject::fetch( $f['creator'] );
            $creatorName = $creator instanceof \eZContentObject ? (string)$creator->attribute( 'name' ) : (string)$f['creator'];
        }
        $sizePositions = array();
        foreach ( $settings['sizes'] as $i => $size )
            $sizePositions[] = array( 'size' => $size, 'position' => $i + 1 );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'rows', $page['rows'] );
        $tpl->setVariable( 'rows_html', \expModerationAdmin::rowsHtml( $page['rows'], \expModerationAdmin::canDo( 'act' ), $personal ) );
        $tpl->setVariable( 'total', $page['total'] );
        $tpl->setVariable( 'filters', $f );
        $tpl->setVariable( 'filter_suffix', \expModerationList::viewParameters( $f ) );
        $tpl->setVariable( 'page_uri', '/moderation/recent' . \expModerationList::viewParameters( $f ) );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $page['offset'] ) );
        $tpl->setVariable( 'limit', $f['limit'] );
        $tpl->setVariable( 'sizes', $sizePositions );
        $tpl->setVariable( 'sort_links', self::sortLinks( $f ) );
        $tpl->setVariable( 'creator_name', $creatorName );
        $tpl->setVariable( 'classes', \expModerationAdmin::classChoices() );
        $tpl->setVariable( 'sections', \expModerationAdmin::sectionChoices() );
        $tpl->setVariable( 'siteaccesses', \expModerationAdmin::siteaccessChoices() );
        $tpl->setVariable( 'states', \expModerationAdmin::stateChoices() );
        $tpl->setVariable( 'can_act', \expModerationAdmin::canDo( 'act' ) );
        $tpl->setVariable( 'personal', $personal );
        $tpl->setVariable( 'audit', (bool)$page['audit'] );
        $tpl->setVariable( 'hold_ready', class_exists( 'expReviewHold' ) && \expReviewHold::group() !== null );
        $tpl->setVariable( 'job_threshold', $settings['job_threshold'] );
        $tpl->setVariable( 'elapsed', $elapsed );
        $tpl->setVariable( 'queries', (int)$page['queries'] );
        $tpl->setVariable( 'problem', $problem );
        $tpl->setVariable( 'return_url', 'moderation/recent' . \expModerationList::viewParameters( $f, array(), true ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:moderation/recent.tpl' );
        $Result['path'] = array( array( 'url' => 'content/dashboard', 'text' => \ezpI18n::tr( 'design/admin/moderation', 'Dashboard' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/moderation', 'Recent content' ) ) );
        return $Result;
    }

    /**
     * The links of the sortable column heads: the same filters, that column, the other order when it is the current
     * one. Pure.
     *
     * @param array $f
     * @return array sort key => array( url (view parameters), current (bool), order (asc|desc) )
     */
    public static function sortLinks( array $f )
    {
        $out = array();
        foreach ( \expModerationList::SORTS as $sort )
        {
            $current = $f['sort'] === $sort;
            $order = $current ? ( $f['order'] === 'asc' ? 'desc' : 'asc' ) : ( in_array( $sort, array( 'name', 'class', 'section', 'creator' ), true ) ? 'asc' : 'desc' );
            $out[$sort] = array( 'url' => '/moderation/recent' . \expModerationList::viewParameters( $f, array( 'sort' => $sort, 'order' => $order ) ),
                                 'current' => $current, 'order' => $f['order'] );
        }
        return $out;
    }
}

}
?>
