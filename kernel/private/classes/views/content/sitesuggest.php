<?php
/**
 * File containing the content/sitesuggest view.
 *
 * The suggestions of a site's header search field as JSON (expSearchSuggest::siteSuggest(); the include
 * parts/front_search_suggest.tpl and javascript/page/search_suggest.js ask for them while the visitor types):
 * content/sitesuggest/(text)/<typed text>. Only content the current user may read (the anonymous user: what is
 * public), only content objects (never user accounts, classes, administration pages or settings), and of each only
 * its name, address, class name and a small image. It only reads: a GET, nothing changed, nothing logged as a search.
 *
 *   200  { query, total, groups: [ { id, title, count, items: [ { type, title, url, meta, icon } ] } ], min_length }
 *   404  { error: 'disabled' }  site.ini [SiteSearchSuggestSettings] Enabled=false
 *   429  { error: 'rate' }      more than RateLimit requests of one address in RateWindow seconds (Retry-After)
 *
 * An anonymous answer is the same for every anonymous visitor of the siteaccess: it is kept PublicCacheTTL seconds
 * (cache item searchsuggest, keyed by siteaccess, languages and text) and sent as Cache-Control: public with that
 * max-age. A signed-in user's answer is never kept: Cache-Control: private, no-store. Vary: Cookie on both.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Content
{

class SiteSuggest extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        \eZDebug::updateSettings( array( 'debug-enabled' => false ) );

        $settings = \expSearchSuggest::siteSettings();
        if ( !$settings['enabled'] )
            return $this->send( 404, array( 'error' => 'disabled' ), false, 0 );

        $user = \eZUser::currentUser();
        \Exponential\View\Kernel\User\Megamenu::releaseSession();
        $anonymous = !$user->isRegistered();

        $wait = self::rateLimit( $settings, \eZSys::clientIP() );
        if ( $wait > 0 )
        {
            if ( !headers_sent() )
                header( 'Retry-After: ' . $wait );
            return $this->send( 429, array( 'error' => 'rate' ), false, 0 );
        }

        $text = \expViewParameters::text( $Params, 'text', 'Text' );
        if ( !$anonymous )
            return $this->send( 200, \expSearchSuggest::siteSuggest( $text, $settings ) + array( 'min_length' => $settings['min_length'] ), false, 0 );

        $key = self::cacheKey( $text, $GLOBALS['eZCurrentAccess']['name'] ?? '', \eZContentLanguage::prioritizedLanguageCodes(), (int)$user->id() );
        $handler = $settings['cache_ttl'] > 0
                   ? \eZClusterFileHandler::instance( \eZSys::cacheDirectory() . '/searchsuggest/site/' . substr( $key, 0, 2 ) . '/' . $key . '.json' )
                   : null;
        if ( $handler && $handler->exists() && $handler->mtime() > time() - $settings['cache_ttl'] )
        {
            $cached = json_decode( (string)$handler->fetchContents(), true );
            if ( is_array( $cached ) )
                return $this->send( 200, $cached, true, $settings['cache_ttl'] );
        }
        $body = \expSearchSuggest::siteSuggest( $text, $settings ) + array( 'min_length' => $settings['min_length'] );
        if ( $handler )
            $handler->storeContents( json_encode( $body ), 'searchsuggest', 'json' );
        return $this->send( 200, $body, true, $settings['cache_ttl'] );
    }

    /**
     * The key of an anonymous answer: the same text, siteaccess, languages and anonymous user give the same answer.
     *
     * @param string $text
     * @param string $siteAccess
     * @param array $languages
     * @param int $userID
     * @return string
     */
    public static function cacheKey( $text, $siteAccess, array $languages, $userID )
    {
        return hash( 'sha256', json_encode( array( 'site', trim( (string)$text ), (string)$siteAccess, array_values( $languages ), (int)$userID,
                                                   \eZSys::indexDir(), \eZSys::hostname() ) ) );
    }

    /**
     * Counts the request of an address: 0 when it may go on, else the seconds to wait. RateLimit=0: no limit.
     *
     * @param array $settings siteSettings()
     * @param string $address
     * @return int
     */
    public static function rateLimit( array $settings, $address )
    {
        if ( $settings['rate_limit'] <= 0 || (string)$address === '' )
            return 0;
        $limit = new \expLogRateLimit( \eZSys::cacheDirectory() . '/searchsuggest/rate', 'sitesuggest|' . $address,
                                       $settings['rate_limit'], $settings['rate_window'] );
        return $limit->hit();
    }

    /**
     * @param int $status
     * @param array $body
     * @param bool $public whether a shared cache may keep the answer
     * @param int $maxAge
     * @return null
     */
    protected function send( $status, array $body, $public, $maxAge )
    {
        \eZExecution::discardOutputBuffers();
        if ( !headers_sent() )
        {
            http_response_code( $status );
            header( 'Content-Type: application/json; charset=utf-8' );
            header( $public && $maxAge > 0 ? 'Cache-Control: public, max-age=' . (int)$maxAge : 'Cache-Control: private, no-store' );
            header( 'Vary: Cookie' );
            header( 'X-Content-Type-Options: nosniff' );
        }
        echo json_encode( $body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_INVALID_UTF8_SUBSTITUTE );
        \eZExecution::cleanExit();
        return null;
    }
}

}
