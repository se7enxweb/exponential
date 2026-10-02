<?php
/**
 * A copy of the audit records outside the file (syslog, webhook, mail), registered in audit.ini:
 *   [AuditSinkSettings]
 *   SinkClasses[<name>]=<class implementing expAuditSink>
 * Counted by the RAD survey (registry "auditsinks"). The sinks themselves are delivered in stage 5; the
 * interface is here so the registry can be counted and checked from the start.
 * Guide: doc/bc/6.0/audit.md ("Sinks").
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

interface expAuditSink
{
    /** @return string The sink's name */
    public function name();

    /** @return string '' when it can work here, else why not (missing extension, no URL) */
    public function problem();

    /**
     * @param array[] $records records already through the privacy rules
     * @return int delivered count
     */
    public function deliver( array $records );
}
