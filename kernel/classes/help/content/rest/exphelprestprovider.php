<?php
/**
 * The REST routes of context help (doc/eng-us/features/6.0/help-content.md, "Context help by help_id"; the plan's
 * delivery path 2: "internal topics with a token and a matching role"):
 *
 *   GET /api/help/v1/topic/<help_id>        a topic, the JSON of exphelp/context/<help_id>
 *   GET /api/help/v1/manual/<remote_id>     a manual's table of contents, the JSON of exphelp/context/manual/<remote_id>
 *
 * The REST layer authenticates the request (an OAuth token, or a personal API key "Authorization: Bearer expk_..."
 * with the scope help, rest.ini [ApiKeySettings] Scopes[help]=exphelp/context), so a mobile app or another client
 * reads the internal topics its user may read without a session cookie. exphelp/context stays the address for a
 * signed-in browser and, opted in, for the public topics without login.
 *
 * Registered in rest.ini [ApiProvider] ProviderClass[help]=expHelpRestProvider.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpRestProvider implements ezpRestProviderInterface
{
    public function getRoutes()
    {
        return array(
            'expHelpTopic' => new ezpRestVersionedRoute(
                new ezpMvcRailsRoute( '/topic/:helpId', 'expHelpRestController', array( 'http-get' => 'topic' ) ), 1 ),
            'expHelpManual' => new ezpRestVersionedRoute(
                new ezpMvcRailsRoute( '/manual/:remoteId', 'expHelpRestController', array( 'http-get' => 'manual' ) ), 1 ),
        );
    }

    public function getViewController()
    {
        return new expHelpRestViewController();
    }
}

?>
