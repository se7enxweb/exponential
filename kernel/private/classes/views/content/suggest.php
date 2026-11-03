<?php
/**
 * File containing the content/suggest view.
 *
 * The suggestions of the administration's header search field as JSON (expSearchSuggest; the script
 * javascript/page/search_suggest.js of the admin designs asks for them while the user types): content/suggest/(text)/
 * <typed text>, the text written as every view parameter is (expViewParameters::encode(), in the scripts
 * window.a4ViewParameter.encode()). It only reads: a GET, no form token, nothing changed, nothing logged as a search.
 *
 * The answer is always JSON, with a status the script can trust:
 *
 *   200  { query, total, groups: [ { id, title, count, items: [ { type, title, url, meta, icon, path, excerpt } ] } ] }
 *   403  { error: 'signed-out' }  no signed-in user (the anonymous user of a site where it has content/read)
 *   404  { error: 'disabled' }    admininterface.ini [SearchSuggestSettings] Enabled=false
 *
 * Every answer is private to the user (Cache-Control: private, no-store; Vary: Cookie). The view only reads the
 * session, so it releases it at once.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Content
{

class Suggest extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        \eZDebug::updateSettings( array( 'debug-enabled' => false ) );

        $settings = \expSearchSuggest::settings();
        if ( !$settings['enabled'] )
            return $this->send( 404, array( 'error' => 'disabled' ) );

        $user = \eZUser::currentUser();
        \Exponential\View\Kernel\User\Megamenu::releaseSession();
        if ( !$user->isRegistered() )
            return $this->send( 403, array( 'error' => 'signed-out' ) );

        $text = \expViewParameters::text( $Params, 'text', 'Text' );
        return $this->send( 200, \expSearchSuggest::suggest( $text, $settings ) + array( 'min_length' => $settings['min_length'] ) );
    }

    /**
     * @param int $status
     * @param array $body
     * @return null
     */
    protected function send( $status, array $body )
    {
        \eZExecution::discardOutputBuffers();
        if ( !headers_sent() )
        {
            http_response_code( $status );
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Cache-Control: private, no-store' );
            header( 'Vary: Cookie' );
            header( 'X-Content-Type-Options: nosniff' );
        }
        echo json_encode( $body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_INVALID_UTF8_SUBSTITUTE );
        \eZExecution::cleanExit();
        return null;
    }
}

}
