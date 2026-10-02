<?php
/**
 * File containing the interfaces of the request rules: conditions, actions,
 * rule providers and fact providers.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * A condition: one test on the facts of a request.
 *
 * Registered by name in requestrules.ini [RequestRuleSettings]
 * ConditionHandlers[<name>]=<class>, and used in a rule as
 * Conditions[<name>]=<values> or Conditions[<name>:<argument>]=<values>.
 * The engine splits the values on commas and handles the leading "!" itself,
 * so a condition only answers "does any of these values match?".
 *
 * A condition must not change anything: it may be asked more than once, or
 * not at all when an earlier condition of the same rule did not match.
 */
interface ezpRequestRuleCondition
{
    /**
     * @param ezpRequestContext $context
     * @param string[] $values the rule's values, never empty
     * @param string|null $argument the part of the key after the colon, or null
     * @return bool true when any of $values matches
     */
    public function matches( ezpRequestContext $context, array $values, $argument );
}

/**
 * Marks a condition whose answer is the same for every request that the page
 * caches would answer with one stored page: it reads only what their keys hold
 * (the address, siteaccess, host, scheme, the node shown, and the user's roles
 * and policies -- the permission context). A condition without this mark
 * (header, ip, user_id, a custom condition) varies by request: wherever it
 * could decide, the page is sent with Cache-Control: private, no-store and
 * the role-aware HTTP cache and Velocity's response cache do not store it,
 * so a stored page never answers a request the rules would treat otherwise.
 *
 * Implement it on a custom condition only when that is true.
 */
interface ezpRequestRuleStableCondition
{
}

/**
 * An action: what happens when every condition of a rule matches.
 *
 * Registered by name in requestrules.ini [RequestRuleSettings]
 * ActionHandlers[<name>]=<class>, and used in a rule as Action=<name> with
 * ActionArgs[<argument>]=<value>.
 */
interface ezpRequestRuleAction
{
    /**
     * @param ezpRequestContext $context
     * @param array $arguments the rule's ActionArgs (or FallbackArgs)
     * @param ezpRequestRule $rule
     * @return ezpRequestRuleResult|null the decision, or null when this action
     *         cannot act on this request: the rule's FallbackAction is tried
     *         then, and without one the next rule is asked
     */
    public function decide( ezpRequestContext $context, array $arguments, ezpRequestRule $rule );
}

/**
 * Adds rules from PHP: an extension whose rules are computed (from a
 * database table, from another INI file) rather than written in
 * requestrules.ini. Listed in [RequestRuleSettings] RuleProviders[]=<class>.
 * Its rules come after the RuleList rules, in the order given.
 */
interface ezpRequestRuleProvider
{
    /**
     * @return ezpRequestRule[]
     */
    public function rules();
}

/**
 * Adds facts to every request's context before the rules run. Listed in
 * [RequestRuleSettings] FactProviders[]=<class>. Define facts as resolvers
 * ( $context->define( 'name', function ( $context ) { ... } ) ), so they cost
 * nothing on requests where no rule reads them.
 */
interface ezpRequestFactProvider
{
    /**
     * @param ezpRequestContext $context
     */
    public function defineFacts( ezpRequestContext $context );
}

?>
