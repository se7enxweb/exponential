<?php
/**
 * An alert rule class, registered in audit.ini:
 *   [AuditAlertSettings]
 *   RuleClasses[<name>]=<class implementing expAuditAlertRule>
 * Counted by the RAD survey (registry "auditalertrules"). Rules are evaluated from stage 5 on.
 * Guide: doc/bc/6.0/audit.md ("Alerts").
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

interface expAuditAlertRule
{
    /**
     * @param array $config the [AlertRule_*] block
     * @param array[] $records new records to look at
     * @param object $state the rule's window state (expAuditAlertState)
     * @return array[] alerts: array( 'group' => ..., 'count' => ..., 'events' => ids, 'message' => ... )
     */
    public function evaluate( array $config, array $records, $state );
}
