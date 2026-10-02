<?php
/**
 * An extension's branch of the audit taxonomy, registered in audit.ini:
 *   [AuditEventSettings]
 *   Branches[myext]=myExtAuditBranch
 * Counted by the RAD survey (registry "auditbranches"). Names start with the extension's own subject
 * (content.myext_poll.vote) so two extensions cannot claim the same branch; a name registered twice is refused.
 * Guide: doc/bc/6.0/audit.md ("Extension interfaces and their registries").
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

interface expAuditTaxonomyBranch
{
    /**
     * @return array name => array( 'label' => ..., 'severity' => 'info', 'channel' => null|'content',
     *                              'default' => 'on'|'off'|'always'|'sampled', 'privacy' => array( field => default ) )
     */
    public function events();
}
