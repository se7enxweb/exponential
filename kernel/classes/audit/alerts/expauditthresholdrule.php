<?php
/**
 * The alert rule class "threshold" (RuleClasses[threshold]=expAuditThresholdRule): fires when Threshold events
 * matching Event fall within Window seconds in one group (GroupBy, a record field such as actor.ip or object.id).
 * With CountChildren=enabled a parent's after.children_omitted counts too (mass delete).
 *
 *   [AlertRule_brute_force]  Class=threshold  Event=access.session.login.failed  Threshold=20  Window=300  GroupBy=actor.ip
 *
 * 20 failed logins from one network within 300 s fire once; the 40th fires again (the count doubled); 19 do not.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditThresholdRule extends expAuditAlertRuleBase
{
    public function defaults()
    {
        return array( 'Threshold' => '10', 'Window' => '300', 'GroupBy' => 'actor.ip', 'CountChildren' => 'disabled' );
    }

    public function evaluate( array $config, array $records, $state )
    {
        $config += $this->defaults();
        $threshold = max( 1, (int)$config['Threshold'] );
        $windowMs = max( 1, (int)$config['Window'] ) * 1000;
        $children = strtolower( trim( (string)$config['CountChildren'] ) ) === 'enabled';
        $alerts = array();
        foreach ( $records as $r )
        {
            $weight = 1;
            if ( $children && isset( $r['after']['children_omitted'] ) )
                $weight += max( 0, (int)$r['after']['children_omitted'] );
            $a = $this->count( $state, self::groupOf( $r, $config['GroupBy'] ), $r, $weight, $threshold, $windowMs );
            if ( $a )
            {
                $a['message'] = sprintf( '%d %s events within %d s for %s %s', $a['count'], $config['Event'], $windowMs / 1000,
                                         $config['GroupBy'], $a['group'] );
                $alerts[] = $a;
            }
        }
        return $alerts;
    }
}
