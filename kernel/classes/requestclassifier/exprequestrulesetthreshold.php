<?php
/**
 * File containing the expRequestRuleSetThreshold class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Category "threshold", rule "404-rate": more than Max404PerMinute not-found answers (404 and 410, see
 * settings/requestclassifier.ini [ThresholdSettings]) to one client within a minute. Only a request whose status is
 * one of those touches the counter (expRequestThresholdCounter, in the cache directory), so a normal page costs
 * nothing here. The client is counted by expRequestClassifier::ipKey(), a salted hash; the address itself is never
 * stored.
 *
 * Every not-found answer over the limit is a verdict (its line goes to security.log instead of 404.log) until the
 * minute is over. A request another set flagged first (a probe) is not counted.
 *
 * @package kernel
 */
class expRequestRuleSetThreshold implements expRequestRuleSetInterface
{
    const RULE = '404-rate';

    /** @var string */
    protected $identifier;
    /** @var array */
    protected $settings;
    /** @var expRequestClassifier|null */
    protected $classifier;
    /** @var expRequestThresholdCounter|null */
    protected $counter = null;

    /**
     * @param string $identifier
     * @param array $settings the [RuleSet-<id>] group; the limits come from the classifier's threshold settings
     * @param expRequestClassifier|null $classifier
     */
    public function __construct( $identifier, array $settings = array(), $classifier = null )
    {
        $this->identifier = (string)$identifier;
        $this->settings = $settings;
        $this->classifier = $classifier instanceof expRequestClassifier ? $classifier : new expRequestClassifier( array() );
    }

    public function identifier()
    {
        return $this->identifier;
    }

    /**
     * @return expRequestThresholdCounter
     */
    public function counter()
    {
        if ( $this->counter === null )
        {
            $t = $this->classifier->thresholdSettings();
            $this->counter = new expRequestThresholdCounter( $this->classifier->cacheDirectory() . '/counter', $t['window'] );
        }
        return $this->counter;
    }

    public function evaluate( expRequestContext $context )
    {
        $t = $this->classifier->thresholdSettings();
        if ( $context->status === null || $t['max'] <= 0 || !in_array( (int)$context->status, $t['statuses'], true ) )
            return null;
        $count = $this->counter()->hit( $this->classifier->ipKey( $context->ip ) );
        if ( $count <= $t['max'] )
            return null;
        return new expRequestVerdict( 'threshold', self::RULE, $count > 2 * $t['max'] ? expRequestVerdict::HIGH : expRequestVerdict::MEDIUM,
                                      $count . ' in ' . $t['window'] . 's' );
    }
}
