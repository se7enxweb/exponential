<?php
/**
 * File containing the expRequestRuleSetInterface interface.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A rule set of the request classifier (settings/requestclassifier.ini, RuleSets[] and [RuleSet-<id>] Class=).
 *
 * The classifier creates it with new Class( $identifier, $settings, $classifier ): $settings is the INI group
 * [RuleSet-<id>] as an array (setting name => value), $classifier the expRequestClassifier (for its settings, its
 * cache directory and its salt). An implementation may ignore the arguments. expRequestPatternRuleSet is a base for a
 * set of regular expressions.
 *
 * evaluate() runs for every request that is classified, so it must be cheap: no database, no network, no file
 * access unless the request already is a not-found (the threshold set). It must not throw; the classifier skips a
 * set that does.
 *
 * @package kernel
 */
interface expRequestRuleSetInterface
{
    /**
     * The identifier of the set, as in RuleSets[].
     *
     * @return string
     */
    public function identifier();

    /**
     * The verdict for the request, or null when no rule of the set matches.
     *
     * @param expRequestContext $context
     * @return expRequestVerdict|null
     */
    public function evaluate( expRequestContext $context );
}
