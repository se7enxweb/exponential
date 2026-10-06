<?php
/**
 * File containing the expApiKeyAuthFilter class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Authentication filter for a personal API key (expApiKey) presented as "Authorization: Bearer expk_..."
 * (doc/guides/api-keys.md, "How the REST layer resolves a key").
 *
 * The credentials' id is the whole key. run() answers with the status codes of ezpOauthFilter, so the REST layer's
 * existing answers apply (401 invalid_token / expired_token), plus STATUS_RATE_LIMITED (429). Every refusal of a
 * well formed key is recorded as access.apikey.use.failed with a reason, the first use of a key as
 * access.apikey.use. Nothing about the secret is ever recorded.
 */
class expApiKeyAuthFilter extends ezcAuthenticationFilter
{
    const STATUS_RATE_LIMITED = 5;

    /**
     * The key of the request after a successful run().
     *
     * Reset by every run(); expApiKeyRest::reset() clears it at the start of each REST request, since a persistent
     * worker keeps statics from one request to the next.
     *
     * @var expApiKey|null
     */
    public static $key = null;

    /**
     * Why the last run() refused, for the audit and the tests: malformed, unknown, secret, revoked, expired,
     * owner, rate_limited, disabled.
     *
     * @var string|null
     */
    public static $reason = null;

    public function run( $credentials )
    {
        self::$key = null;
        self::$reason = null;
        $now = time();

        if ( !expApiKey::enabled() )
            return $this->refuse( 'disabled', ezpOauthFilter::STATUS_TOKEN_INVALID );

        // expApiKeyRest::authentication() passes '' for a key sent in the query string or the body
        if ( isset( $credentials->id ) && $credentials->id === '' )
            return $this->refuse( 'transport', ezpOauthFilter::STATUS_TOKEN_INVALID );

        $parts = expApiKey::parseToken( isset( $credentials->id ) ? $credentials->id : null );
        if ( $parts === null )
        {
            // still spend the time of a hash, so a malformed key cannot be told from a wrong one by timing
            expApiKey::secretMatches( 'x', 'x', null );
            return $this->refuse( 'malformed', ezpOauthFilter::STATUS_TOKEN_INVALID );
        }
        list( $prefix, $secret ) = $parts;

        $key = expApiKey::fetchByPrefix( $prefix );
        if ( !$key instanceof expApiKey )
        {
            expApiKey::secretMatches( $secret, 'x', null );
            return $this->refuse( 'unknown', ezpOauthFilter::STATUS_TOKEN_INVALID, array( 'type' => 'apikey', 'prefix' => $prefix ) );
        }
        if ( !$key->verifySecret( $secret ) )
            return $this->refuse( 'secret', ezpOauthFilter::STATUS_TOKEN_INVALID, $key->auditObject() );

        $status = $key->statusAt( $now );
        if ( $status === expApiKey::STATUS_REVOKED )
            return $this->refuse( 'revoked', ezpOauthFilter::STATUS_TOKEN_INVALID, $key->auditObject() );
        if ( $status === expApiKey::STATUS_EXPIRED )
            return $this->refuse( 'expired', ezpOauthFilter::STATUS_TOKEN_EXPIRED, $key->auditObject() );

        $owner = $key->owner();
        if ( !$owner instanceof eZUser || !$owner->isEnabled() || $owner->isAnonymous()
             || !eZUser::isEnabledAfterFailedLogin( $owner->attribute( 'contentobject_id' ) ) )
            return $this->refuse( 'owner', ezpOauthFilter::STATUS_TOKEN_INVALID, $key->auditObject() );

        if ( expApiKey::overRateLimit( $key->attribute( 'id' ), (int)expApiKey::setting( 'RateLimitPerMinute', 0 ), $now ) )
            return $this->refuse( 'rate_limited', self::STATUS_RATE_LIMITED, $key->auditObject(), false );

        $firstUse = $key->isNeverUsed();
        $key->touch( eZSys::clientIP(), $now, (int)expApiKey::setting( 'LastUsedInterval', 60 ) );
        if ( $firstUse && class_exists( 'expAudit' ) )
            expAudit::event( 'access.apikey.use', array( 'object' => $key->auditObject(),
                                                         'actor' => array( 'user_id' => (int)$owner->attribute( 'contentobject_id' ),
                                                                           'login' => (string)$owner->attribute( 'login' ) ),
                                                         'after' => array( 'first_use' => true, 'scopes' => $key->scopeList() ) ) );

        self::$key = $key;
        return self::STATUS_OK;
    }

    /**
     * Records a refusal and returns its status.
     *
     * @param string $reason
     * @param int $status
     * @param array|null $object what the key is known as
     * @param bool $record false for refusals that come in bursts (the rate limit records once a minute)
     * @return int
     */
    protected function refuse( $reason, $status, $object = null, $record = true )
    {
        self::$reason = $reason;
        if ( !$record && isset( $object['id'] ) )
        {
            // one record per key and minute for a key over its limit, not one per refused request
            $record = !function_exists( 'apcu_add' ) || apcu_add( 'expapikey:rl-audit:' . (int)$object['id'] . ':' . (int)floor( time() / 60 ), 1, 120 );
        }
        if ( $record && $reason !== 'disabled' && class_exists( 'expAudit' ) )
        {
            $data = array( 'result' => 'refused', 'reason' => $reason );
            if ( $object !== null )
                $data['object'] = $object;
            expAudit::event( 'access.apikey.use.failed', $data );
        }
        return $status;
    }
}
?>
