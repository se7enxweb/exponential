<?php
/**
 * The audit/alerts view (doc/bc/6.0/audit.md, "Views", "Alerts (F5)"): the alerts that fired (system.audit.alert
 * records: rule, group, count, window, the first and last matching events) newest first, and the alert rules in use
 * with their settings and any problem. Read-only: evaluating rules and acknowledging alerts are not done here.
 * Policy audit/read; alerts are records of the system channel, so a Channel limitation without it shows none.
 * Recorded as system.audit.read.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Audit
{

class Alerts extends \Exponential\Runnable\ModuleView
{
    const PAGE = 50;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !class_exists( 'expAuditConsole' ) || !\expAuditConsole::available() )
            return $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

        $allowed = \expAuditConsole::allowedChannels();
        $offset = isset( $Params['Offset'] ) && ctype_digit( (string)$Params['Offset'] ) ? (int)$Params['Offset'] : 0;
        \expAuditConsole::refreshIndex();
        $page = \expAuditConsole::page( array( 'name' => 'system.audit.alert' ), $allowed, $offset, self::PAGE );
        $alerts = array();
        foreach ( $page['rows'] as $row )
        {
            $v = \expAuditConsole::view( $row );
            $rec = json_decode( (string)$row['record'], true );
            $after = isset( $rec['after'] ) && is_array( $rec['after'] ) ? $rec['after'] : array();
            $v['rule'] = isset( $after['rule'] ) ? (string)$after['rule'] : '';
            $v['group'] = isset( $after['group'] ) ? Event::text( $after['group'] ) : '';
            $v['count'] = isset( $after['count'] ) ? (int)$after['count'] : 0;
            $v['window'] = isset( $after['window'] ) ? (int)$after['window'] : 0;
            $v['message'] = isset( $after['message'] ) ? (string)$after['message'] : '';
            $v['first'] = isset( $after['first'] ) && is_string( $after['first'] ) ? $after['first'] : '';
            $v['last'] = isset( $after['last'] ) && is_string( $after['last'] ) ? $after['last'] : '';
            $alerts[] = $v;
        }

        $rules = array();
        foreach ( self::rules() as $name => $r )
        {
            $c = $r['config'];
            $rules[] = array( 'name' => $name, 'class' => $r['class_key'], 'event' => isset( $c['Event'] ) ? (string)$c['Event'] : '',
                              'threshold' => isset( $c['Threshold'] ) ? (string)$c['Threshold'] : '',
                              'window' => isset( $c['Window'] ) ? (string)$c['Window'] : '',
                              'group_by' => isset( $c['GroupBy'] ) ? (string)$c['GroupBy'] : '',
                              'severity' => isset( $c['Severity'] ) ? (string)$c['Severity'] : '',
                              'sinks' => implode( ', ', array_filter( (array)( isset( $c['Sinks'] ) ? $c['Sinks'] : array() ), 'strlen' ) ),
                              'problem' => $r['problem'],
                              'fired_url' => \expAuditConsole::url( 'audit/console', array( 'name' => 'system.audit.alert', 'q' => $name ) ) );
        }

        \expAuditConsole::recordRead( 'audit/alerts', array(), (int)$page['total'] );

        $ini = \eZINI::instance( 'audit.ini' );
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'alerts', $alerts );
        $tpl->setVariable( 'total', (int)$page['total'] );
        $tpl->setVariable( 'offset', $offset );
        $tpl->setVariable( 'page_size', self::PAGE );
        $tpl->setVariable( 'rules', $rules );
        $tpl->setVariable( 'alerts_enabled', strtolower( (string)$ini->variable( 'AuditAlertSettings', 'Alerts' ) ) === 'enabled' );
        $tpl->setVariable( 'system_allowed', \expAuditConsole::channelAllowed( 'system', $allowed ) );
        $tpl->setVariable( 'can_manage', \expAuditConsole::canManage() );
        return \expAuditConsole::result( $tpl->fetch( 'design:audit/alerts.tpl' ), \ezpI18n::tr( 'design/admin/audit', 'Alerts' ) );
    }

    /**
     * The rules in use: the evaluator's list when it is there, else audit.ini read directly.
     *
     * @return array name => config, class_key, problem
     */
    public static function rules()
    {
        if ( class_exists( 'expAuditAlertEvaluator' ) && method_exists( 'expAuditAlertEvaluator', 'rules' ) )
        {
            try
            {
                return \expAuditAlertEvaluator::rules();
            }
            catch ( \Throwable $e )
            {
            }
        }
        $ini = \eZINI::instance( 'audit.ini' );
        $out = array();
        $names = $ini->hasVariable( 'AuditAlertSettings', 'Rules' ) ? (array)$ini->variable( 'AuditAlertSettings', 'Rules' ) : array();
        foreach ( array_filter( $names, 'strlen' ) as $name )
        {
            $block = 'AlertRule_' . $name;
            $config = $ini->hasGroup( $block ) ? $ini->group( $block ) : array();
            $out[$name] = array( 'config' => $config, 'class_key' => isset( $config['Class'] ) ? $config['Class'] : 'threshold',
                                 'problem' => $config ? '' : "no [$block] block" );
        }
        return $out;
    }
}

}
