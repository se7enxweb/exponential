<?php
/**
 * File containing the expStaticFormEndpoint class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The form endpoint of static publishing: the view staticpublish/form on the live installation, where the
 * information collectors of a static copy post (doc/eng-us/specifications/6.0/static-publishing-form-endpoint.md).
 * This class decides; the view (kernel/private/classes/views/staticpublish/form.php) reads the request, sends the
 * answer and hands the post over to the normal information collection (content/action with
 * ActionCollectInformation), so the collector's own validation, e-mail and redirect settings apply unchanged.
 *
 * A post is taken only when all of these hold, in this order (the first that fails answers):
 *   1. static publishing and [StaticPublishSettings] FormEndpoint are enabled                        404
 *   2. the post names a target (ExpStaticTarget) whose Forms=endpoint                                404
 *   3. its Origin (or, without one, its Referer) is the target's public address or custom domain     403
 *   4. its token (ExpStaticToken) is valid for the target and the object it posts to                 403
 *   5. the address has not posted more than FormRateLimit times in FormRateWindow seconds            429
 * A preflight (OPTIONS) is answered for any origin of a target with Forms=endpoint; never with a wildcard, never
 * with credentials.
 *
 * The answer is JSON (ok, message) to a script (Accept: application/json, or X-Requested-With), with the CORS headers
 * for the origin; a plain form post is sent back (303) to the page it came from, with #exp-static-form-sent or
 * #exp-static-form-failed, so a static page can show either without a script.
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticFormEndpoint
{
    /** The answers' fragments for a plain post */
    const SENT = 'exp-static-form-sent';
    const FAILED = 'exp-static-form-failed';

    /** The fields the endpoint reads besides the form's own */
    const OWN_FIELDS = array( expStaticDegrader::TARGET_FIELD, expStaticDegrader::TOKEN_FIELD );

    private $options;

    /**
     * @param array $options enabled (bool), targets (id => expStaticTarget), rate (callable( $key ) => int: 0 to go
     *        on, else the seconds to wait), hand_over (callable( expStaticTarget, array $post ) => array( ok, message )),
     *        days (token lifetime), now, key (the token key, tests), audit (callable( $name, $data ))
     */
    public function __construct( array $options = array() )
    {
        $this->options = $options + array( 'enabled' => false, 'targets' => array(), 'rate' => null, 'hand_over' => null,
                                           'days' => expStaticFormToken::DAYS, 'now' => null, 'key' => null, 'audit' => null );
    }

    /**
     * The settings of the installation as options: enabled, the targets, the token lifetime.
     *
     * @return array
     */
    public static function settings()
    {
        $ini = eZINI::instance( 'staticcache.ini' );
        $get = function ( $name, $default ) use ( $ini )
        {
            return $ini->hasVariable( 'StaticPublishSettings', $name ) ? (string)$ini->variable( 'StaticPublishSettings', $name ) : $default;
        };
        return array( 'enabled' => expStaticTarget::enabled( $ini ) && $get( 'FormEndpoint', 'enabled' ) === 'enabled',
                      'targets' => expStaticTarget::all( $ini ), 'days' => (int)$get( 'FormTokenDays', (string)expStaticFormToken::DAYS ),
                      'limit' => max( 1, (int)$get( 'FormRateLimit', '5' ) ), 'window' => max( 1, (int)$get( 'FormRateWindow', '600' ) ) );
    }

    /**
     * The origins a target's copy is served from: its public address and its custom domain (https and http). Pure.
     *
     * @param expStaticTarget $target
     * @return string[] scheme://host[:port]
     */
    public static function origins( expStaticTarget $target )
    {
        $origins = array();
        $public = $target->publicUrl();
        if ( $public !== '' && ( $origin = self::origin( $public ) ) !== '' )
            $origins[] = $origin;
        $domain = strtolower( trim( (string)$target->setting( 'CustomDomain' ) ) );
        if ( $domain !== '' && expStaticExtras::validDomain( $domain ) )
        {
            $origins[] = 'https://' . $domain;
            $origins[] = 'http://' . $domain;
        }
        return array_values( array_unique( $origins ) );
    }

    /** @return string the origin of an address (scheme://host[:port], lower case), '' when it has none */
    public static function origin( $url )
    {
        $parts = parse_url( trim( (string)$url ) );
        if ( !is_array( $parts ) || empty( $parts['scheme'] ) || empty( $parts['host'] ) || !in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) )
            return '';
        return strtolower( $parts['scheme'] ) . '://' . strtolower( $parts['host'] ) . ( isset( $parts['port'] ) ? ':' . (int)$parts['port'] : '' );
    }

    /** @return expStaticTarget[] the targets that take posts */
    private function formTargets()
    {
        $out = array();
        foreach ( (array)$this->options['targets'] as $id => $target )
            if ( $target instanceof expStaticTarget && $target->setting( 'Forms' ) === 'endpoint' )
                $out[(string)$id] = $target;
        return $out;
    }

    /**
     * The answer to one request.
     *
     * @param string $method
     * @param array $headers lower-case name => value (origin, referer, accept, x-requested-with)
     * @param array $post
     * @param string $client the client's address (for the rate limit; only its hash is kept)
     * @return array status, headers (name => value), json (array|null), redirect (string|null), reason, target
     */
    public function handle( $method, array $headers, array $post, $client )
    {
        $method = strtoupper( (string)$method );
        $origin = isset( $headers['origin'] ) ? self::origin( $headers['origin'] ) : '';
        $referer = isset( $headers['referer'] ) ? (string)$headers['referer'] : '';
        if ( $origin === '' && $referer !== '' )
            $origin = self::origin( $referer );
        $wantsJson = ( isset( $headers['accept'] ) && stripos( (string)$headers['accept'], 'application/json' ) !== false )
                  || ( isset( $headers['x-requested-with'] ) && $headers['x-requested-with'] !== '' );
        $answer = array( 'status' => 200, 'headers' => array( 'Vary' => 'Origin', 'Cache-Control' => 'no-store' ), 'json' => null, 'redirect' => null,
                         'reason' => '', 'target' => '' );

        if ( !$this->options['enabled'] )
            return $this->refuse( $answer, 404, 'the form endpoint is switched off', $wantsJson, '' );
        $targets = $this->formTargets();

        if ( $method === 'OPTIONS' )
        {
            foreach ( $targets as $target )
                if ( $origin !== '' && in_array( $origin, self::origins( $target ), true ) )
                {
                    $answer['status'] = 204;
                    $answer['headers'] += self::cors( $origin ) + array( 'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                                                                         'Access-Control-Allow-Headers' => 'Content-Type, Accept, X-Requested-With',
                                                                         'Access-Control-Max-Age' => '600' );
                    return $answer;
                }
            return $this->refuse( $answer, 403, 'the origin is not the address of a static copy', true, '' );
        }
        if ( $method !== 'POST' )
        {
            $answer['headers']['Allow'] = 'POST, OPTIONS';
            return $this->refuse( $answer, 405, 'only POST', $wantsJson, '' );
        }

        $targetId = isset( $post[expStaticDegrader::TARGET_FIELD] ) ? (string)$post[expStaticDegrader::TARGET_FIELD] : '';
        if ( !isset( $targets[$targetId] ) )
            return $this->refuse( $answer, 404, 'no target of this installation takes forms by this name', $wantsJson, '' );
        $target = $targets[$targetId];
        $answer['target'] = $targetId;
        $origins = self::origins( $target );
        if ( $origin === '' || !in_array( $origin, $origins, true ) )
            return $this->refuse( $answer, 403, 'the post does not come from the address of the target', $wantsJson, '' );
        $answer['headers'] += self::cors( $origin );
        $back = $referer !== '' && self::origin( $referer ) === $origin ? preg_replace( '/#.*$/', '', $referer ) : rtrim( $target->publicUrl() ?: $origin . '/', '/' ) . '/';

        $objectId = isset( $post['ContentObjectID'] ) ? (string)$post['ContentObjectID'] : '';
        $token = isset( $post[expStaticDegrader::TOKEN_FIELD] ) ? (string)$post[expStaticDegrader::TOKEN_FIELD] : '';
        $problem = expStaticFormToken::problem( $token, $targetId, $objectId, (int)$this->options['days'], $this->options['now'], $this->options['key'] );
        if ( $problem !== '' )
            return $this->refuse( $answer, 403, $problem, $wantsJson, $back );

        if ( is_callable( $this->options['rate'] ) )
        {
            $wait = (int)call_user_func( $this->options['rate'], hash( 'sha256', $targetId . '|' . (string)$client ) );
            if ( $wait > 0 )
            {
                $answer['headers']['Retry-After'] = (string)$wait;
                return $this->refuse( $answer, 429, 'too many posts from this address; try again later', $wantsJson, $back );
            }
        }

        $fields = $post;
        foreach ( self::OWN_FIELDS as $field )
            unset( $fields[$field] );
        $result = is_callable( $this->options['hand_over'] ) ? (array)call_user_func( $this->options['hand_over'], $target, $fields ) : array( false, 'no handler' );
        $ok = !empty( $result[0] );
        $message = isset( $result[1] ) ? (string)$result[1] : '';
        $this->event( $targetId, $objectId, $ok ? 'success' : 'failure', $ok ? '' : $message );
        if ( $wantsJson )
        {
            $answer['status'] = $ok ? 200 : 422;
            $answer['json'] = array( 'ok' => $ok, 'message' => $message );
        }
        else
        {
            $answer['status'] = 303;
            $answer['redirect'] = $back . '#' . ( $ok ? self::SENT : self::FAILED );
        }
        $answer['reason'] = $ok ? '' : $message;
        return $answer;
    }

    /** @return array the CORS headers for one origin: never a wildcard, never credentials */
    public static function cors( $origin )
    {
        return array( 'Access-Control-Allow-Origin' => (string)$origin );
    }

    /** An answer that refuses the request. */
    private function refuse( array $answer, $status, $reason, $wantsJson, $back )
    {
        $answer['status'] = (int)$status;
        $answer['reason'] = (string)$reason;
        if ( $status === 403 || $status === 429 )
            $this->event( $answer['target'], '', 'failure', $reason );
        if ( $wantsJson || $back === '' )
            $answer['json'] = array( 'ok' => false, 'message' => (string)$reason );
        else
        {
            $answer['status'] = 303;
            $answer['redirect'] = $back . '#' . self::FAILED;
        }
        return $answer;
    }

    /** The audit event system.staticpublish.form. */
    private function event( $targetId, $objectId, $result, $reason )
    {
        $data = array( 'object' => array( 'type' => 'static_target', 'id' => (string)$targetId ), 'verb' => 'form', 'result' => $result,
                       'reason' => (string)$reason, 'x' => array( 'content_object' => (string)$objectId ) );
        if ( is_callable( $this->options['audit'] ) )
            call_user_func( $this->options['audit'], 'system.staticpublish.form', $data );
        else
            expStaticPublisher::audit( 'system.staticpublish.form', $data );
    }
}
