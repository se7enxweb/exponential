<?php
/**
 * The content/jobs view: the list of content jobs (large subtree removes and copies running in the
 * background) with who started them, the operation, source and target, state, progress, items, start time,
 * duration and server, filters by state, type and user, sorting, paging, totals per state, and the Cancel and
 * Resume actions. A user sees the jobs they started; with the content/jobs policy, /(all)/1 lists everybody's.
 * Query parameters: state, type, user (a user id, all jobs only), sort (created|started|duration|items|state),
 * order (asc|desc), offset.
 * Guide: doc/bc/6.0/content-jobs.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Content
{

class Jobs extends \Exponential\Runnable\ModuleView
{
    /** jobs per page */
    const PAGE = 25;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $user = \eZUser::currentUser();

        $canSeeAll = Job::canSeeAll( $user );
        $showAll = $canSeeAll && ( !empty( $Params['All'] ) || !empty( $Params['UserParameters']['all'] ) || $http->hasPostVariable( 'ShowAll' ) );
        $listURL = $showAll ? '/content/jobs/(all)/1' : '/content/jobs';

        $error = '';
        foreach ( array( 'CancelJobButton', 'ResumeJobButton' ) as $button )
        {
            if ( !$http->hasPostVariable( $button ) )
                continue;
            $id = (string) $http->postVariable( $button );
            $job = preg_match( '/^[0-9a-z-]{1,64}$/', $id ) ? \expContentJob::fetch( $id ) : null;
            if ( !$job || !Job::canAccess( $job, $user ) )
                return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            $redirect = $listURL;
            $error = Job::act( $job, $http, $user, $redirect );
            if ( $error === '' )
                return $Module->redirectTo( $listURL );
        }

        $get = function ( $name, $allowed = null ) use ( $http ) {
            $v = $http->hasGetVariable( $name ) ? (string) $http->getVariable( $name ) : '';
            return ( $allowed === null || in_array( $v, $allowed, true ) ) ? $v : '';
        };
        $states = array( 'queued', 'running', 'done', 'failed', 'cancelled' );
        $filter = array( 'state' => $get( 'state', $states ),
                         'type' => preg_match( '/^[a-z][a-z0-9_]*$/', $get( 'type' ) ) ? $get( 'type' ) : '',
                         'user' => $showAll ? (int) $get( 'user' ) : 0,
                         'sort' => $get( 'sort', array( 'created', 'started', 'duration', 'items', 'state' ) ) ?: 'created',
                         'order' => $get( 'order', array( 'asc', 'desc' ) ) ?: 'desc' );
        $offset = max( 0, (int) $get( 'offset' ) );

        $all = array();
        $users = array();
        $types = array();
        $totals = array_fill_keys( $states, 0 );
        $hasActive = false;
        foreach ( \expContentJob::listFor( $showAll ? null : $user ) as $job )
        {
            $users[(int) $job->userID()] = true;
            $types[$job->type()] = true;
            if ( $filter['state'] !== '' && $job->state() !== $filter['state'] )
                continue;
            if ( $filter['type'] !== '' && $job->type() !== $filter['type'] )
                continue;
            if ( $filter['user'] && (int) $job->userID() !== $filter['user'] )
                continue;
            $hasActive = $hasActive || $job->isActive();
            if ( isset( $totals[$job->state()] ) )
                $totals[$job->state()]++;
            $all[] = $job;
        }

        $now = time();
        $key = function ( $job ) use ( $filter, $now ) {
            switch ( $filter['sort'] )
            {
                case 'started': return $job->started();
                case 'duration': return $job->started() ? ( $job->finished() ?: $now ) - $job->started() : 0;
                case 'items': $p = $job->progress(); return (int) $p['total'];
                case 'state': return $job->state();
                default: return $job->created();
            }
        };
        usort( $all, function ( $a, $b ) use ( $key, $filter ) {
            $c = $key( $a ) <=> $key( $b );
            return $filter['order'] === 'asc' ? $c : -$c;
        } );
        $count = count( $all );
        $list = array();
        foreach ( array_slice( $all, $offset, self::PAGE ) as $job )
        {
            $info = Job::info( $job );
            $info['duration'] = $job->started() && class_exists( 'Exponential\\Service\\ContentJobDetails' )
                                ? \Exponential\Service\ContentJobDetails::duration( ( $job->finished() ?: $now ) - $job->started() ) : '';
            $server = class_exists( 'Exponential\\Service\\ContentJobDetails' ) ? \Exponential\Service\ContentJobDetails::serverShort( $job ) : '';
            $info['server'] = trim( $server . ( $job->siteaccess() !== '' ? ' (' . $job->siteaccess() . ')' : '' ) );
            // short: the time today, else the date and time
            $t = $job->started() ?: $job->created();
            $locale = \eZLocale::instance();
            $info['when'] = $t ? ( date( 'Ymd', $t ) === date( 'Ymd', $now ) ? $locale->formatShortTime( $t ) : $locale->formatShortDateTime( $t ) ) : '';
            $ownerUser = \eZUser::fetch( (int) $job->userID() );
            $info['user_login'] = $ownerUser ? $ownerUser->attribute( 'login' ) : '#' . $job->userID();
            $list[] = $info;
        }

        $userList = array();
        foreach ( array_keys( $users ) as $id )
        {
            $o = \eZContentObject::fetch( $id );
            $userList[] = array( 'id' => $id, 'name' => $o ? $o->attribute( 'name' ) : '#' . $id );
        }
        usort( $userList, function ( $a, $b ) { return strcasecmp( $a['name'], $b['name'] ); } );

        $query = array_filter( array( 'state' => $filter['state'], 'type' => $filter['type'],
                                      'user' => $filter['user'] ?: '', 'sort' => $filter['sort'], 'order' => $filter['order'] ) );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'jobs', $list );
        $tpl->setVariable( 'count', $count );
        $tpl->setVariable( 'offset', $offset );
        $tpl->setVariable( 'page', self::PAGE );
        $tpl->setVariable( 'filter', $filter );
        $tpl->setVariable( 'query', http_build_query( $query ) );
        $tpl->setVariable( 'base_url', $listURL );
        $tpl->setVariable( 'totals', $totals );
        $tpl->setVariable( 'states', $states );
        $tpl->setVariable( 'types', array_keys( $types ) );
        $tpl->setVariable( 'users', $userList );
        $tpl->setVariable( 'show_all', $showAll );
        $tpl->setVariable( 'can_see_all', $canSeeAll );
        $tpl->setVariable( 'has_active', $hasActive );
        $tpl->setVariable( 'error', $error );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/jobs.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'design/admin/content/job', 'Content jobs' ),
                                        'url' => false ) );
        return $Result;
    }
}

}
