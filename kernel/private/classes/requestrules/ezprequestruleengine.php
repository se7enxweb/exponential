<?php
/**
 * File containing the ezpRequestRuleEngine class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * The request rules: an ordered list of rules, each a set of conditions and an
 * action, asked about every module view the kernel is about to run. The first
 * rule whose conditions all match decides; when none does, the request runs as
 * it always has. Settings: requestrules.ini. Guide:
 * doc/bc/6.0/view_full_security.md.
 *
 * The rules come on top of the role policies, never instead of them: the
 * kernel asks them only after the user's policies allowed the view, so a rule
 * can take access away or send the visitor elsewhere, but never give access a
 * policy refused.
 *
 * Usage from PHP:
 *
 *   $engine = ezpRequestRuleEngine::instance();          // this siteaccess's rules
 *   $engine->addRule( new ezpRequestRule( 'my_rule', array( 'uri' => 'private/*' ), 'login' ) );
 *   $result = $engine->evaluate( $context );            // ezpRequestRuleResult or null
 *   foreach ( $engine->explain( $context ) as $row ) ... // why each rule did or did not match
 *
 * The engine holds no state about any request: one instance serves every
 * request of a siteaccess, also in a persistent worker.
 */
class ezpRequestRuleEngine
{
    /** @var ezpRequestRule[] name => rule, in evaluation order */
    protected $rules = array();
    /** @var array condition name => class name or ezpRequestRuleCondition */
    protected $conditionHandlers = array();
    /** @var array action name => class name or ezpRequestRuleAction */
    protected $actionHandlers = array();
    /** @var array class names of ezpRequestFactProvider */
    protected $factProviders = array();
    /** @var bool */
    protected $enabled = true;
    /** @var int */
    protected $maxRewrites = 3;
    /** @var string[] problems found while loading the settings */
    protected $loadProblems = array();

    /** @var ezpRequestRuleEngine[] siteaccess name => engine */
    protected static $instances = array();

    /** @var array the handlers every engine knows, before requestrules.ini adds to them */
    public static $builtInConditions = array(
        'requested_via' => 'ezpRequestConditionRequestedVia',
        'uri' => 'ezpRequestConditionUri',
        'resolved_uri' => 'ezpRequestConditionResolvedUri',
        'module' => 'ezpRequestConditionModule',
        'module_view' => 'ezpRequestConditionModuleView',
        'view_mode' => 'ezpRequestConditionViewMode',
        'param' => 'ezpRequestConditionParam',
        'node' => 'ezpRequestConditionNode',
        'subtree' => 'ezpRequestConditionSubtree',
        'class' => 'ezpRequestConditionClass',
        'section' => 'ezpRequestConditionSection',
        'state' => 'ezpRequestConditionState',
        'user' => 'ezpRequestConditionUser',
        'user_id' => 'ezpRequestConditionUserId',
        'user_login' => 'ezpRequestConditionUserLogin',
        'role' => 'ezpRequestConditionRole',
        'group' => 'ezpRequestConditionGroup',
        'policy' => 'ezpRequestConditionPolicy',
        'siteaccess' => 'ezpRequestConditionSiteAccess',
        'method' => 'ezpRequestConditionMethod',
        'scheme' => 'ezpRequestConditionScheme',
        'host' => 'ezpRequestConditionHost',
        'header' => 'ezpRequestConditionHeader',
        'fact' => 'ezpRequestConditionFact',
        'ip' => 'ezpRequestConditionIp',
    );

    /** @var array */
    public static $builtInActions = array(
        'allow' => 'ezpRequestActionAllow',
        'notfound' => 'ezpRequestActionNotFound',
        'forbidden' => 'ezpRequestActionForbidden',
        'login' => 'ezpRequestActionLogin',
        'redirect' => 'ezpRequestActionRedirect',
        'redirect_to_alias' => 'ezpRequestActionRedirectToAlias',
        'rewrite' => 'ezpRequestActionRewrite',
        'log' => 'ezpRequestActionLog',
    );

    /**
     * An engine from settings given as an array -- what tests and scripts use;
     * the kernel uses instance(), which reads requestrules.ini.
     *
     * @param array $settings
     *   'enabled'            => bool, default true
     *   'rules'              => array( name => ezpRequestRule | array( 'Conditions' => ..., 'Action' => ..., ... ) )
     *   'condition_handlers' => array( name => class name or object ), added to the built-in ones
     *   'action_handlers'    => array( name => class name or object )
     *   'rule_providers'     => array( class name or ezpRequestRuleProvider )
     *   'fact_providers'     => array( class name )
     *   'max_rewrites'       => int, default 3
     */
    public function __construct( array $settings = array() )
    {
        $this->enabled = !isset( $settings['enabled'] ) || (bool)$settings['enabled'];
        $this->maxRewrites = isset( $settings['max_rewrites'] ) ? max( 0, (int)$settings['max_rewrites'] ) : 3;
        $this->conditionHandlers = self::$builtInConditions;
        $this->actionHandlers = self::$builtInActions;
        foreach ( isset( $settings['condition_handlers'] ) ? $settings['condition_handlers'] : array() as $name => $handler )
            $this->registerCondition( $name, $handler );
        foreach ( isset( $settings['action_handlers'] ) ? $settings['action_handlers'] : array() as $name => $handler )
            $this->registerAction( $name, $handler );
        $this->factProviders = isset( $settings['fact_providers'] ) ? array_values( $settings['fact_providers'] ) : array();

        foreach ( isset( $settings['rules'] ) ? $settings['rules'] : array() as $name => $rule )
            $this->addRule( $rule instanceof ezpRequestRule ? $rule : ezpRequestRule::fromSettings( $name, (array)$rule ) );

        foreach ( isset( $settings['rule_providers'] ) ? $settings['rule_providers'] : array() as $provider )
        {
            $object = is_object( $provider ) ? $provider : ( class_exists( $provider ) ? new $provider() : null );
            if ( !$object instanceof ezpRequestRuleProvider )
            {
                $this->loadProblems[] = 'RuleProviders: ' . ( is_object( $provider ) ? get_class( $provider ) : $provider )
                                      . ' is not a class implementing ezpRequestRuleProvider';
                continue;
            }
            foreach ( $object->rules() as $rule )
            {
                if ( $rule instanceof ezpRequestRule )
                    $this->addRule( $rule );
            }
        }
    }

    /**
     * The engine of the current siteaccess, from requestrules.ini. Kept per
     * siteaccess, because a persistent worker serves several of them.
     *
     * @return ezpRequestRuleEngine
     */
    public static function instance()
    {
        $siteAccess = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : '';
        if ( !isset( self::$instances[$siteAccess] ) )
            self::$instances[$siteAccess] = new self( self::settingsFromINI( eZINI::instance( 'requestrules.ini' ) ) );
        return self::$instances[$siteAccess];
    }

    /**
     * Forgets the engines, so the next instance() reads the settings again.
     */
    public static function resetInstance()
    {
        self::$instances = array();
    }

    /**
     * The constructor's settings from requestrules.ini.
     *
     * @param eZINI $ini
     * @return array
     */
    public static function settingsFromINI( eZINI $ini )
    {
        $get = function ( $name, $default ) use ( $ini )
        {
            return $ini->hasVariable( 'RequestRuleSettings', $name ) ? $ini->variable( 'RequestRuleSettings', $name ) : $default;
        };
        $list = function ( $name ) use ( $get )
        {
            $value = $get( $name, array() );
            return is_array( $value ) ? array_values( array_filter( $value, 'strlen' ) ) : array();
        };

        $settings = array(
            'enabled' => $get( 'Enabled', 'true' ) === 'true' || $get( 'Enabled', 'true' ) === 'enabled',
            'max_rewrites' => (int)$get( 'MaxRewrites', 3 ),
            'condition_handlers' => array_filter( (array)$get( 'ConditionHandlers', array() ), 'strlen' ),
            'action_handlers' => array_filter( (array)$get( 'ActionHandlers', array() ), 'strlen' ),
            'rule_providers' => $list( 'RuleProviders' ),
            'fact_providers' => $list( 'FactProviders' ),
            'rules' => array(),
        );
        foreach ( $list( 'RuleList' ) as $name )
        {
            // A missing group still becomes a rule, an empty one: validate() reports it
            $group = $ini->hasGroup( 'Rule-' . $name ) ? $ini->group( 'Rule-' . $name ) : array();
            $settings['rules'][$name] = ezpRequestRule::fromSettings( $name, $group );
        }
        return $settings;
    }

    /**
     * @return bool whether the rules are on and there is at least one
     */
    public function isActive()
    {
        return $this->enabled && !empty( $this->rules );
    }

    /** @return bool */
    public function isEnabled() { return $this->enabled; }

    /** @return int */
    public function maxRewrites() { return $this->maxRewrites; }

    /** @return array class names of the fact providers */
    public function factProviders() { return $this->factProviders; }

    /**
     * Adds a rule, or replaces the rule of the same name where it stands, so
     * adding the same rule twice (a persistent worker, a script run again)
     * never doubles it.
     *
     * @param ezpRequestRule $rule
     * @param string|null $before name of the rule to put it before; null puts a new rule last
     */
    public function addRule( ezpRequestRule $rule, $before = null )
    {
        if ( isset( $this->rules[$rule->name] ) && $before === null )
        {
            $this->rules[$rule->name] = $rule;
            return;
        }
        unset( $this->rules[$rule->name] );
        if ( $before === null || !isset( $this->rules[$before] ) )
        {
            $this->rules[$rule->name] = $rule;
            return;
        }
        $rules = array();
        foreach ( $this->rules as $name => $existing )
        {
            if ( $name === $before )
                $rules[$rule->name] = $rule;
            $rules[$name] = $existing;
        }
        $this->rules = $rules;
    }

    /**
     * @param string $name
     */
    public function removeRule( $name )
    {
        unset( $this->rules[$name] );
    }

    /**
     * @return ezpRequestRule[] name => rule, in evaluation order
     */
    public function rules()
    {
        return $this->rules;
    }

    /**
     * @param string $name
     * @param string|ezpRequestRuleCondition $handler class name or object
     */
    public function registerCondition( $name, $handler )
    {
        $this->conditionHandlers[strtolower( $name )] = $handler;
    }

    /**
     * @param string $name
     * @param string|ezpRequestRuleAction $handler class name or object
     */
    public function registerAction( $name, $handler )
    {
        $this->actionHandlers[strtolower( $name )] = $handler;
    }

    /**
     * Asks the rules about a request.
     *
     * Also sets the fact 'rules_vary_by_request' on the context: true when a
     * rule with a condition that varies by request (ezpRequestRuleStableCondition
     * not implemented: header, ip, user_id, ...) could have decided -- every
     * other condition of it matched. Such a page must not be stored by a
     * shared cache; ezpRequestRuleKernel makes sure it is not.
     *
     * @param ezpRequestContext $context
     * @return ezpRequestRuleResult|null the decision of the first rule that
     *         matched and could act, with ->rule set; null when no rule
     *         decided and the request runs as usual
     */
    public function evaluate( ezpRequestContext $context )
    {
        $context->define( 'rules_vary_by_request', false );
        if ( !$this->enabled )
            return null;
        foreach ( $this->rules as $rule )
        {
            $answers = array();
            $varies = false;
            $matched = $this->matchRule( $rule, $context, $answers, $varies );
            if ( $varies )
                $context->define( 'rules_vary_by_request', true );
            if ( !$matched )
                continue;
            $result = $this->decide( $rule, $context );
            if ( $result !== null )
                return $result;
        }
        return null;
    }

    /**
     * Why each rule did or did not decide, without acting on anything (an
     * action's decide() runs; the log action writes its line).
     *
     * @param ezpRequestContext $context
     * @return array one row per rule: 'rule', 'description', 'conditions'
     *         (key => true|false|'not asked'|'unknown condition', in the order
     *         they are asked: the conditions that cannot vary by request
     *         first), 'matched', 'varies' (a condition of this rule varies by
     *         request and the others matched: the page is not cached),
     *         'result' (description of the decision or null), 'decides'
     *         (whether this is the rule that decides the request)
     */
    public function explain( ezpRequestContext $context )
    {
        $rows = array();
        $decided = !$this->enabled;
        foreach ( $this->rules as $rule )
        {
            $answers = array();
            $varies = false;
            $matched = $this->matchRule( $rule, $context, $answers, $varies );
            $result = $matched ? $this->decide( $rule, $context ) : null;
            $rows[] = array(
                'rule' => $rule->name,
                'description' => $rule->description,
                'conditions' => $answers,
                'matched' => $matched,
                'varies' => $varies && !$decided,
                'result' => $result ? $result->describe() : null,
                'decides' => !$decided && $result !== null,
            );
            if ( $result !== null )
                $decided = true;
        }
        return $rows;
    }

    /**
     * @param string $key condition key, e.g. 'header:X-Test'
     * @return bool whether the condition gives the same answer for every
     *         request one stored page answers (ezpRequestRuleStableCondition)
     */
    public function isStableCondition( $key )
    {
        list( $name ) = ezpRequestRule::splitKey( $key );
        return $this->handler( 'condition', $name ) instanceof ezpRequestRuleStableCondition;
    }

    /**
     * Problems in the settings: rules without a group, without conditions or
     * action, unknown condition or action names, handler classes that do not
     * exist or implement the wrong interface. An empty list means every rule
     * is understood.
     *
     * @return string[]
     */
    public function validate()
    {
        $problems = $this->loadProblems;
        foreach ( array( 'condition' => $this->conditionHandlers, 'action' => $this->actionHandlers ) as $kind => $handlers )
        {
            foreach ( $handlers as $name => $handler )
            {
                if ( $this->handler( $kind, $name ) === null )
                    $problems[] = "The $kind handler '$name' (" . ( is_object( $handler ) ? get_class( $handler ) : $handler )
                                . ') is not a class implementing ' . ( $kind === 'condition' ? 'ezpRequestRuleCondition' : 'ezpRequestRuleAction' );
            }
        }
        foreach ( $this->factProviders as $class )
        {
            if ( !class_exists( $class ) || !in_array( 'ezpRequestFactProvider', class_implements( $class ) ) )
                $problems[] = "FactProviders: $class is not a class implementing ezpRequestFactProvider";
        }
        foreach ( $this->rules as $rule )
        {
            $where = "Rule '{$rule->name}'";
            if ( empty( $rule->conditions ) )
                $problems[] = "$where has no Conditions[] (is there a [Rule-{$rule->name}] group?); it never matches";
            if ( $rule->action === '' )
                $problems[] = "$where has no Action; it never decides";
            foreach ( $rule->conditions as $key => $text )
            {
                list( $name, $argument ) = ezpRequestRule::splitKey( $key );
                list( $values ) = ezpRequestRule::splitValue( $text );
                if ( !isset( $this->conditionHandlers[$name] ) )
                    $problems[] = "$where uses the unknown condition '$name'; the rule never matches";
                if ( empty( $values ) )
                    $problems[] = "$where: Conditions[$key] has no value; it never matches";
                if ( in_array( $name, array( 'param', 'header', 'fact' ), true ) && ( $argument === null || $argument === '' ) )
                    $problems[] = "$where: Conditions[$key] needs a name after the colon, e.g. Conditions[$name:Name]";
                if ( $name === 'policy' )
                {
                    foreach ( $values as $value )
                    {
                        if ( substr_count( $value, '/' ) !== 1 )
                            $problems[] = "$where: Conditions[policy] values are module/function, not '$value'";
                    }
                }
            }
            foreach ( array_filter( array( $rule->action, (string)$rule->fallbackAction ), 'strlen' ) as $action )
            {
                if ( !isset( $this->actionHandlers[strtolower( $action )] ) )
                    $problems[] = "$where uses the unknown action '$action'";
            }
            foreach ( array( array( $rule->action, $rule->actionArgs, 'ActionArgs' ), array( (string)$rule->fallbackAction, $rule->fallbackArgs, 'FallbackArgs' ) ) as $check )
            {
                if ( in_array( strtolower( $check[0] ), array( 'redirect', 'rewrite' ), true ) && empty( $check[1]['to'] ) )
                    $problems[] = "$where: the {$check[0]} action needs {$check[2]}[to]";
                if ( isset( $check[1]['status'] ) && !isset( ezpRequestRuleResult::$statusText[(int)$check[1]['status']] ) )
                    $problems[] = "$where: {$check[2]}[status]={$check[1]['status']} is not 301, 302, 303, 307 or 308 (302 is used)";
            }
        }
        return $problems;
    }

    /**
     * @param ezpRequestRule $rule
     * @param ezpRequestContext $context
     * @return bool whether every condition matches; a rule without conditions never does
     */
    public function ruleMatches( ezpRequestRule $rule, ezpRequestContext $context )
    {
        $answers = array();
        $varies = false;
        return $this->matchRule( $rule, $context, $answers, $varies );
    }

    /**
     * Asks a rule's conditions: first those that cannot vary by request, then
     * the others, stopping at the first that does not match.
     *
     * @param ezpRequestRule $rule
     * @param ezpRequestContext $context
     * @param array $answers set to condition key => true|false|'not asked'|'unknown condition'
     * @param bool $varies set to true when every stable condition matched and
     *        the rule has a condition that varies by request
     * @return bool
     */
    protected function matchRule( ezpRequestRule $rule, ezpRequestContext $context, array &$answers, &$varies )
    {
        $varies = false;
        $stable = $volatile = array();
        foreach ( $rule->conditions as $key => $text )
        {
            $answers[$key] = 'not asked';
            if ( $this->isStableCondition( $key ) )
                $stable[$key] = $text;
            else
                $volatile[$key] = $text;
        }
        if ( empty( $rule->conditions ) )
            return false;
        foreach ( array( $stable, $volatile ) as $i => $group )
        {
            if ( $i === 1 && $group )
                $varies = true;
            foreach ( $group as $key => $text )
            {
                $answer = $this->conditionMatches( $key, $text, $context );
                $answers[$key] = $answer === null ? 'unknown condition' : $answer;
                if ( $answer !== true )
                {
                    // An unknown condition is a broken rule, not a request-dependent one
                    if ( $answer === null )
                        $varies = false;
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param string $key condition key
     * @param string $text condition value text
     * @param ezpRequestContext $context
     * @return bool|null null when the condition is unknown (the rule then never matches)
     */
    protected function conditionMatches( $key, $text, ezpRequestContext $context )
    {
        list( $name, $argument ) = ezpRequestRule::splitKey( $key );
        list( $values, $negated ) = ezpRequestRule::splitValue( $text );
        $handler = $this->handler( 'condition', $name );
        if ( $handler === null )
        {
            self::debugError( "Unknown request rule condition '$name'" );
            return null;
        }
        if ( empty( $values ) )
            return false;
        $matches = (bool)$handler->matches( $context, $values, $argument );
        return $negated ? !$matches : $matches;
    }

    /**
     * Runs the rule's action, and its fallback when the action cannot act.
     *
     * @param ezpRequestRule $rule
     * @param ezpRequestContext $context
     * @return ezpRequestRuleResult|null
     */
    protected function decide( ezpRequestRule $rule, ezpRequestContext $context )
    {
        foreach ( array( array( $rule->action, $rule->actionArgs ), array( $rule->fallbackAction, $rule->fallbackArgs ) ) as $step )
        {
            if ( $step[0] === null || $step[0] === '' )
                continue;
            $handler = $this->handler( 'action', strtolower( $step[0] ) );
            if ( $handler === null )
            {
                self::debugError( "Unknown request rule action '{$step[0]}' in rule '{$rule->name}'" );
                continue;
            }
            $result = $handler->decide( $context, $step[1], $rule );
            if ( $result instanceof ezpRequestRuleResult )
            {
                $result->rule = $rule->name;
                return $result;
            }
        }
        return null;
    }

    /**
     * @param string $kind 'condition' or 'action'
     * @param string $name
     * @return ezpRequestRuleCondition|ezpRequestRuleAction|null
     */
    protected function handler( $kind, $name )
    {
        $handlers =& $this->{$kind . 'Handlers'};
        if ( !isset( $handlers[$name] ) )
            return null;
        $interface = $kind === 'condition' ? 'ezpRequestRuleCondition' : 'ezpRequestRuleAction';
        if ( is_string( $handlers[$name] ) )
        {
            $class = $handlers[$name];
            if ( !class_exists( $class ) )
                return null;
            $handlers[$name] = new $class();
        }
        return $handlers[$name] instanceof $interface ? $handlers[$name] : null;
    }

    protected static function debugError( $message )
    {
        if ( class_exists( 'eZDebug', false ) )
            eZDebug::writeError( $message, __CLASS__ );
    }
}

?>
