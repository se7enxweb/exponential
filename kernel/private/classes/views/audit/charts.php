<?php
/**
 * The audit/charts view (doc/bc/6.0/audit.md, "Views"): events per day per channel, refusals and failures per
 * day, logins against failed logins per day, the most active actors and the most frequent event names, over the
 * last /(days)/<n> days (14 by default) and under the console's filters. Needs the index (the files alone give no
 * charts). Policy audit/read with its Channel limitation. Recorded as system.audit.read.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Audit
{

class Charts extends \Exponential\Runnable\ModuleView
{
    /** The channels in their fixed colour order */
    const CHANNEL_ORDER = array( 'content', 'access', 'system', 'commerce', 'read' );

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !class_exists( 'expAuditConsole' ) || !\expAuditConsole::available() )
            return $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

        $raw = \expAuditConsole::rawFilters( $Params );
        $days = isset( $Params['Days'] ) && ctype_digit( (string)$Params['Days'] ) ? min( 366, max( 1, (int)$Params['Days'] ) ) : 14;
        if ( \expAuditConsole::isFormRequest() )
            return $Module->redirectTo( '/' . \expAuditConsole::url( 'audit/charts', $raw, array( 'days' => $days !== 14 ? $days : null ) ) );
        $allowed = \expAuditConsole::allowedChannels();
        if ( isset( $raw['channel'] ) && !\expAuditConsole::channelAllowed( $raw['channel'], $allowed ) )
            return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );

        $tpl = \eZTemplate::factory();
        $usable = \expAuditConsole::indexUsable();
        $tpl->setVariable( 'index_usable', $usable );
        $tpl->setVariable( 'filters', $raw );
        $tpl->setVariable( 'days', $days );
        $tpl->setVariable( 'console_url', \expAuditConsole::url( 'audit/console', $raw ) );
        $tpl->setVariable( 'channel_options', \expAuditConsole::channelOptions( $allowed ) );
        // the links of the extensions' sources (audit.ini [AuditSourceSettings] Sources[]); none without one
        $tpl->setVariable( 'audit_sources', \expAuditConsole::sourceLinks( 'audit/charts', $raw, $allowed ) );
        $tpl->setVariable( 'severities', \expAuditIndexRow::SEVERITIES );
        $tpl->setVariable( 'names', \expAuditConsole::knownNames( $allowed ) );
        if ( !$usable )
        {
            \expAuditConsole::recordRead( 'audit/charts', $raw, 0 );
            return \expAuditConsole::result( $tpl->fetch( 'design:audit/charts.tpl' ), \ezpI18n::tr( 'design/admin/audit', 'Charts' ) );
        }

        \expAuditConsole::refreshIndex();
        $f = \expAuditQuery::normalise( $raw );
        if ( !isset( $f['from_ms'] ) )
        {
            $from = mktime( 0, 0, 0, (int)date( 'n' ), (int)date( 'j' ) - ( $days - 1 ), (int)date( 'Y' ) );
            $f['from_ms'] = $from * 1000;
            $f['from'] = date( 'Y-m-d', $from );
        }
        $q = new \expAuditQuery();
        $offsetMs = (int)date( 'Z' ) * 1000;

        // the day axis: every day of the range, also the empty ones
        $start = (int)floor( $f['from_ms'] / 1000 );
        $end = isset( $f['to_ms'] ) ? (int)floor( $f['to_ms'] / 1000 ) - 1 : time();
        $axis = array();
        for ( $t = $start; $t <= $end && count( $axis ) < 400; $t = strtotime( '+1 day', $t ) )
            $axis[] = date( 'Y-m-d', $t );

        $perChannel = $q->perDay( $f, $allowed, 'channel', $offsetMs );
        $channels = array();
        foreach ( $perChannel as $d => $byChannel )
            foreach ( array_keys( $byChannel ) as $c )
                $channels[$c] = true;
        $ordered = array_values( array_intersect( self::CHANNEL_ORDER, array_keys( $channels ) ) );
        $ordered = array_merge( $ordered, array_values( array_diff( array_keys( $channels ), $ordered ) ) );
        $colour = array();
        foreach ( $ordered as $i => $c )
        {
            $slot = array_search( $c, self::CHANNEL_ORDER, true );
            $colour[$c] = 1 + ( $slot === false ? 5 + $i % 3 : $slot );
        }

        $resultFilter = $f;
        $refusals = $q->perDay( $resultFilter + array(), $allowed, 'result', $offsetMs );
        $loginFilter = $f;
        $loginFilter['names'] = array( 'access.session.login', 'access.session.login.failed' );
        $logins = $q->perDay( $loginFilter, $allowed, 'name', $offsetMs );

        $dayRows = array();
        $max = 1;
        $maxBad = 1;
        $maxLogin = 1;
        $total = 0;
        foreach ( $axis as $d )
        {
            $segments = array();
            $sum = 0;
            foreach ( $ordered as $c )
            {
                $n = isset( $perChannel[$d][$c] ) ? (int)$perChannel[$d][$c] : 0;
                $sum += $n;
                $segments[] = array( 'channel' => $c, 'n' => $n, 'slot' => $colour[$c] );
            }
            $refused = isset( $refusals[$d]['refused'] ) ? (int)$refusals[$d]['refused'] : 0;
            $failed = isset( $refusals[$d]['failed'] ) ? (int)$refusals[$d]['failed'] : 0;
            $ok = isset( $logins[$d]['access.session.login'] ) ? (int)$logins[$d]['access.session.login'] : 0;
            $bad = isset( $logins[$d]['access.session.login.failed'] ) ? (int)$logins[$d]['access.session.login.failed'] : 0;
            $max = max( $max, $sum );
            $maxBad = max( $maxBad, $refused + $failed );
            $maxLogin = max( $maxLogin, $ok, $bad );
            $total += $sum;
            $dayRows[] = array( 'day' => $d, 'label' => date( 'D j M', strtotime( $d ) ), 'segments' => $segments, 'sum' => $sum,
                                'refused' => $refused, 'failed' => $failed, 'logins' => $ok, 'failed_logins' => $bad );
        }
        // bar widths in percent of the largest day
        foreach ( $dayRows as $i => $r )
        {
            foreach ( $r['segments'] as $j => $s )
                $dayRows[$i]['segments'][$j]['pct'] = round( 100 * $s['n'] / $max, 2 );
            $dayRows[$i]['refused_pct'] = round( 100 * $r['refused'] / $maxBad, 2 );
            $dayRows[$i]['failed_pct'] = round( 100 * $r['failed'] / $maxBad, 2 );
            $dayRows[$i]['logins_pct'] = round( 100 * $r['logins'] / $maxLogin, 2 );
            $dayRows[$i]['failed_logins_pct'] = round( 100 * $r['failed_logins'] / $maxLogin, 2 );
        }

        $top = function ( $column ) use ( $q, $f, $allowed ) {
            $rows = $q->groupCount( array( $column ), $f, $allowed, 10 );
            $max = 1;
            foreach ( $rows as $r )
                $max = max( $max, (int)$r['n'] );
            $out = array();
            foreach ( $rows as $r )
                $out[] = array( 'value' => (string)$r[$column], 'n' => (int)$r['n'], 'pct' => round( 100 * (int)$r['n'] / $max, 2 ) );
            return $out;
        };
        $topActors = $top( 'login' );
        $topNames = $top( 'name' );
        foreach ( $topNames as $i => $r )
            $topNames[$i]['label'] = \expAuditConsole::label( $r['value'] );

        $sourceCharts = $this->sourceCharts( $q, $f, $raw, $allowed, $axis, $offsetMs );

        \expAuditConsole::recordRead( 'audit/charts', $raw, $total, array( 'days' => $days ) );

        $tpl->setVariable( 'source_charts', $sourceCharts );

        $tpl->setVariable( 'day_rows', $dayRows );
        $tpl->setVariable( 'legend', array_map( function ( $c ) use ( $colour ) { return array( 'channel' => $c, 'slot' => $colour[$c] ); }, $ordered ) );
        $tpl->setVariable( 'top_actors', $topActors );
        $tpl->setVariable( 'top_names', $topNames );
        $tpl->setVariable( 'total', $total );
        $tpl->setVariable( 'max_day', $max );
        $tpl->setVariable( 'from', $f['from'] );

        return \expAuditConsole::result( $tpl->fetch( 'design:audit/charts.tpl' ), \ezpI18n::tr( 'design/admin/audit', 'Charts' ) );
    }

    /**
     * The charts of the extensions' sources (expAuditSource::charts()): each series counts the events of its name
     * pattern per day, under the page's filters. A source of another channel than the filtered one is left out.
     *
     * @param \expAuditQuery $q
     * @param array $f the normalised filters
     * @param array $raw
     * @param string[]|null $allowed
     * @param string[] $axis the days
     * @param int $offsetMs
     * @return array[] identifier, title, console_url, series (label, name, slot, total, url), rows (day, label, values (n, pct, slot, label), text)
     */
    protected function sourceCharts( \expAuditQuery $q, array $f, array $raw, $allowed, array $axis, $offsetMs )
    {
        if ( !class_exists( 'expAuditSourceRegistry' ) )
            return array();
        $out = array();
        foreach ( \expAuditSourceRegistry::charts( $allowed ) as $chart )
        {
            if ( isset( $f['channel'] ) && $chart['channel'] !== '' && $f['channel'] !== $chart['channel'] )
                continue;
            $series = array();
            $counts = array();
            $max = 1;
            foreach ( $chart['series'] as $i => $s )
            {
                $sf = $f;
                unset( $sf['names'] );
                $sf['name'] = $s['name'];
                $perDay = $q->perDay( $sf, $allowed, 'channel', $offsetMs );
                $counts[$i] = array();
                $sum = 0;
                foreach ( $axis as $d )
                {
                    $n = isset( $perDay[$d] ) ? (int)array_sum( $perDay[$d] ) : 0;
                    $counts[$i][$d] = $n;
                    $sum += $n;
                    $max = max( $max, $n );
                }
                $series[] = array( 'label' => $s['label'], 'name' => $s['name'], 'slot' => 1 + $i % 8, 'total' => $sum,
                                   'url' => \expAuditConsole::url( 'audit/console', array( 'name' => $s['name'] ) + array_diff_key( $raw, array( 'name' => 1 ) ) ) );
            }
            $rows = array();
            foreach ( $axis as $d )
            {
                $values = array();
                foreach ( $series as $i => $s )
                    $values[] = array( 'n' => $counts[$i][$d], 'pct' => round( 100 * $counts[$i][$d] / $max, 2 ), 'slot' => $s['slot'], 'label' => $s['label'] );
                $rows[] = array( 'day' => $d, 'label' => date( 'D j M', strtotime( $d ) ), 'values' => $values,
                                 'text' => implode( ' / ', array_column( $values, 'n' ) ) );
            }
            $out[] = array( 'identifier' => $chart['identifier'], 'title' => $chart['title'], 'series' => $series, 'rows' => $rows,
                            'console_url' => \expAuditConsole::url( 'audit/console', $chart['channel'] !== '' ? array( 'channel' => $chart['channel'] ) + $raw : $raw ) );
        }
        return $out;
    }
}

}
