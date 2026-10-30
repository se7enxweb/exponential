<?php
/**
 * The controller of the context help routes (expHelpRestProvider): the JSON of a help topic or a manual's table of
 * contents, for the user the REST layer authenticated (an OAuth token or a personal API key), exactly as
 * exphelp/context answers a signed-in browser: a Released topic the user may read (content/read with its section and
 * state), or a preview for who may edit it. The user needs exphelp/context too. Anything else, unknown or not, is the
 * same 404, so the answer does not tell whether an internal topic exists.
 *
 * Never kept in the REST application cache (rest.ini [expHelpRestController_CacheSettings]): what a user may see
 * depends on the user.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The route's variables come from ezcMvcController::__get() (its properties); a declared property would hide them.
 *
 * @property-read string $helpId the route's :helpId
 * @property-read string $remoteId the route's :remoteId
 */
class expHelpRestController extends ezpRestMvcController
{
    public function doTopic()
    {
        $refusal = $this->refusal();
        if ( $refusal !== null )
            return $refusal;
        $provider = expHelpContentProvider::instance();
        $topic = $provider->topic( (string)$this->helpId );
        return $topic === null ? self::notFound() : self::result( $provider->json( $topic ) );
    }

    public function doManual()
    {
        $refusal = $this->refusal();
        if ( $refusal !== null )
            return $refusal;
        $provider = expHelpContentProvider::instance();
        $manual = $provider->manual( (string)$this->remoteId );
        return $manual === null ? self::notFound() : self::result( $provider->manualJson( $manual ) );
    }

    /**
     * 404 when help as content is off or not installed, 403 when the user lacks exphelp/context
     *
     * @return ezpRestMvcResult|null
     */
    protected function refusal()
    {
        if ( !class_exists( 'expHelp' ) || !expHelp::isEnabled() || !expHelpContentProvider::isEnabled() )
            return self::notFound();
        $access = eZUser::currentUser()->hasAccessTo( 'exphelp', 'context' );
        if ( $access['accessWord'] === 'no' )
            return self::status( 403, array( 'error' => 'access_denied', 'error_message' => 'The user may not read context help (exphelp/context).' ) );
        return null;
    }

    /**
     * @param array $data
     * @return ezpRestMvcResult
     */
    protected static function result( array $data )
    {
        $result = new ezpRestMvcResult();
        $result->variables = $data;
        return $result;
    }

    /** @return ezpRestMvcResult */
    protected static function notFound()
    {
        return self::status( 404, array( 'error' => 'not_found' ) );
    }

    /**
     * @param int $code
     * @param array $body
     * @return ezpRestMvcResult
     */
    protected static function status( $code, array $body )
    {
        $result = new ezpRestMvcResult();
        $result->status = new ezpRestStatusResponse( (int)$code, $body );
        return $result;
    }
}

?>
