<?php
/**
 * File containing the expReauthTriggerHandler interface.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A class that decides for an extension that a web request needs "Verify it's you" (expReauth), where a rule of
 * [ReauthGroup_<name>] Rules[] cannot say it (the decision depends on the extension's own data). The kernel asks every
 * class of site.ini [ReauthSettings] TriggerHandlers[] after the rules matched nothing, signed-in users only, never on
 * REST, SOAP, WebDAV or the command line. The handler names a group; the request asks only while that group is on
 * and applies to the user (its UserGroups[]), so the switches on setup/reauth stay in charge.
 *
 * A view that wants the question asked from its own code instead calls expReauth::requireFresh().
 * Guide: doc/eng-us/guides/verify-its-you.md
 *
 * @since 6.0.15
 */
interface expReauthTriggerHandler
{
    /**
     * The group a request belongs to, or null when the handler does not ask for it.
     *
     * @param string $module
     * @param string $view
     * @param string $method GET, POST ...
     * @param array $post the POST fields (without the fields of the "Verify it's you" form)
     * @param array $params the view's ordered parameters
     * @param eZUser $user the signed-in user
     * @return string|null a group name of [ReauthSettings] Groups[]
     */
    public function reauthGroup( $module, $view, $method, array $post, array $params, eZUser $user );
}
