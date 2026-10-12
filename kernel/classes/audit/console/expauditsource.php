<?php
/**
 * An optional source of the audit console: an extension that writes its own events (usually into a channel of its
 * own) and tells the console's views how to show them. Registered in audit.ini:
 *   [AuditSourceSettings]
 *   Sources[myext]=myExtAuditSource
 * The views ask every registered source that works here (problem() is '') and whose channel the user may read:
 * the dashboard shows its panel, the console, recent, charts, alerts and export views its links, the charts view its
 * series, an event of its channel its fields and links, the settings view its state. A source that is not
 * registered, or whose extension is not active, changes nothing. Extend expAuditSourceBase, which answers every
 * method with "nothing". Guide: doc/bc/6.0/audit.md ("An extension's source in the console").
 *
 * Every address a source returns is a module/view path with view parameters (expai/log/(errors)/1), never a query
 * string or an absolute URL; the registry drops anything else. A source returns only links the current user may
 * follow. A source that throws is left out of that page and reported in error.log; it never breaks a view.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

interface expAuditSource
{
    /** @return string the name shown for the source, translated ("Exponential Intelligence") */
    public function label();

    /** @return string the channel the source's events are written to ('' when it has none of its own) */
    public function channel();

    /** @return string '' when the source can work here, otherwise why not (shown in audit/settings) */
    public function problem();

    /**
     * Links for a view of the console: shortcuts into the console filtered on the source's events, and into the
     * extension's own pages.
     *
     * @param string $view audit/console, audit/recent, audit/charts, audit/alerts, audit/export or audit/dashboard
     * @param array $filters the view's filters as in its address (channel, name, user, object, from, to, ...)
     * @return array[] label, url (module/view path)
     */
    public function links( $view, array $filters );

    /**
     * The source's panel on the dashboard, or null for none. Computed from the index ($q); cached by the dashboard
     * for a minute per user.
     *
     * @param expAuditQuery $q
     * @param string[]|null $allowed the channels the user may read (null: all)
     * @param array $period today_ms and week_ms: epoch ms of the start of today and of 7 days ago
     * @return array|null figures (label, value, url, state), lists (title, items: label, n, url), links (label,
     *                    url), note
     */
    public function dashboard( expAuditQuery $q, ?array $allowed, array $period );

    /**
     * What the source knows about one of its records, shown on audit/event.
     *
     * @param array $record the record as written
     * @return array|null fields (label, value), links (label, url)
     */
    public function event( array $record );

    /**
     * The address of an object or target type the source records ("expai_request"), or null.
     *
     * @param string $type
     * @param string $id
     * @return string|null module/view path
     */
    public function linkFor( $type, $id );

    /**
     * The source's charts on audit/charts: each a title and series, a series counting the events of a name
     * pattern per day.
     *
     * @return array[] title, series (label, name)
     */
    public function charts();
}
