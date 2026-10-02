<?php
/**
 * The request rules (kernel/private/classes/requestrules), guide
 * doc/bc/6.0/view_full_security.md.
 *
 *  RR-01 — The first rule whose conditions all match decides; order counts; no match runs the view
 *  RR-02 — Values are any-of lists; a leading "!" turns a condition around; an empty value never matches
 *  RR-03 — The shipped rule: anonymous system full view goes to the alias (301, user parameters kept)
 *  RR-04 — The shipped rule leaves aliases, the front page, other view modes and users with the policy alone
 *  RR-05 — The shipped rule answers "not found" for a node without its own alias (FallbackAction)
 *  RR-06 — An action that cannot act passes to the fallback, then to the next rule
 *  RR-07 — Facts are resolved lazily: a rule that fails early never loads the node
 *  RR-08 — An unknown condition never matches, an unknown action never decides; validate() names both
 *  RR-09 — validate() is empty for good settings and names every kind of mistake
 *  RR-10 — addRule() is idempotent and can place a rule before another; removeRule() removes it
 *  RR-11 — Conditions and actions registered by name, as objects or classes, from settings or PHP
 *  RR-12 — Rule providers add rules after the list; a provider of the wrong class is reported
 *  RR-13 — Patterns: uri, host, header, param:<Name>, fact:<name>, module_view
 *  RR-14 — ip: IPv4 and IPv6 addresses and networks
 *  RR-15 — Placeholders in redirect and rewrite targets; redirect statuses
 *  RR-16 — Users, roles, groups, policies, siteaccess, method, scheme, class, section, subtree, state
 *  RR-17 — explain() tells which rule decides and why the others did not
 *  RR-18 — Enabled=false asks no rule; an empty list is inactive
 *  RR-19 — A request another request for the same stored page could be decided otherwise is marked; stable conditions first
 *  RR-20 — The login action never acts on the views a visitor needs to sign in
 *  RR-21 — The shipped settings/requestrules.ini is understood, rule by rule
 *
 * No database, no kernel: rules read facts and nothing else.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group requestrules
 */

foreach ( array( 'ezprequestcontext', 'ezprequestrule', 'ezprequestruleresult', 'ezprequestruleinterfaces',
                 'ezprequestruleconditions', 'ezprequestruleactions', 'ezprequestruleengine' ) as $file )
    require_once __DIR__ . '/../../../../../kernel/private/classes/requestrules/' . $file . '.php';

class ezpRequestRuleEngineTest extends PHPUnit\Framework\TestCase
{
    /** The rule shipped as system_url_full_view_to_alias */
    private static function shippedRule()
    {
        return array(
            'Conditions' => array(
                'requested_via' => 'system',
                'module_view' => 'content/view',
                'view_mode' => 'full',
                'policy' => '!content/view_system_url',
            ),
            'Action' => 'redirect_to_alias',
            'ActionArgs' => array( 'status' => '301' ),
            'FallbackAction' => 'notfound',
        );
    }

    /**
     * Facts of a request, as ezpRequestRuleKernel::context() gives them.
     */
    private static function request( array $facts = array() )
    {
        $policies = isset( $facts['policies'] ) ? $facts['policies'] : array();
        unset( $facts['policies'] );
        return new ezpRequestContext( $facts + array(
            'typed_uri' => 'content/view/full/123',
            'requested_via' => 'system',
            'resolved_uri' => 'content/view/full/123',
            'module' => 'content',
            'view' => 'view',
            'params' => array( 'ViewMode' => 'full', 'NodeID' => '123' ),
            'view_mode' => 'full',
            'node_id' => 123,
            'url_alias' => 'Fit-Healthy/Some-Article',
            'user_parameters_uri' => '',
            'is_anonymous' => true,
            'user_id' => 10,
            'user_login' => 'anonymous',
            'siteaccess' => 'site',
            'method' => 'GET',
            'scheme' => 'https',
            'host' => 'www.example.com',
            'has_access' => function () use ( $policies )
            {
                return function ( $module, $function ) use ( $policies )
                {
                    return in_array( "$module/$function", $policies, true );
                };
            },
        ) );
    }

    private static function engine( array $rules, array $settings = array() )
    {
        return new ezpRequestRuleEngine( $settings + array( 'rules' => $rules ) );
    }

    public function testFirstMatchingRuleDecidesInOrder()
    {
        $engine = self::engine( array(
            'a' => array( 'Conditions' => array( 'module' => 'user' ), 'Action' => 'forbidden' ),
            'b' => array( 'Conditions' => array( 'module' => 'content' ), 'Action' => 'notfound' ),
            'c' => array( 'Conditions' => array( 'module' => 'content' ), 'Action' => 'forbidden' ),
        ) );
        $result = $engine->evaluate( self::request() );
        $this->assertSame( ezpRequestRuleResult::NOT_FOUND, $result->type );
        $this->assertSame( 'b', $result->rule );
        $this->assertNull( $engine->evaluate( self::request( array( 'module' => 'shop' ) ) ), 'no rule matches: the view runs' );
    }

    public function testListsNegationAndEmptyValues()
    {
        $engine = self::engine( array(
            'r' => array( 'Conditions' => array( 'siteaccess' => 'bold, site', 'method' => '!POST,PUT' ), 'Action' => 'forbidden' ),
        ) );
        $this->assertNotNull( $engine->evaluate( self::request() ) );
        $this->assertNull( $engine->evaluate( self::request( array( 'method' => 'POST' ) ) ) );
        $this->assertNull( $engine->evaluate( self::request( array( 'siteaccess' => 'admin' ) ) ) );

        $empty = self::engine( array( 'r' => array( 'Conditions' => array( 'module' => ' ' ), 'Action' => 'forbidden' ) ) );
        $this->assertNull( $empty->evaluate( self::request() ) );
        $emptyNegated = self::engine( array( 'r' => array( 'Conditions' => array( 'module' => '!' ), 'Action' => 'forbidden' ) ) );
        $this->assertNull( $emptyNegated->evaluate( self::request() ), '"!" with no value is no condition, and never matches' );
    }

    public function testShippedRuleRedirectsAnonymousSystemFullViewToAlias()
    {
        $engine = self::engine( array( 'system_url_full_view_to_alias' => self::shippedRule() ) );
        $result = $engine->evaluate( self::request() );
        $this->assertSame( ezpRequestRuleResult::REDIRECT, $result->type );
        $this->assertSame( '/Fit-Healthy/Some-Article', $result->location );
        $this->assertSame( 301, $result->status );
        $this->assertSame( '301 Moved Permanently', $result->statusLine() );

        $paged = $engine->evaluate( self::request( array( 'user_parameters_uri' => '/(offset)/10' ) ) );
        $this->assertSame( '/Fit-Healthy/Some-Article/(offset)/10', $paged->location );
    }

    public function testShippedRuleLeavesEverythingElseAlone()
    {
        $engine = self::engine( array( 'system_url_full_view_to_alias' => self::shippedRule() ) );
        $this->assertNull( $engine->evaluate( self::request( array( 'requested_via' => 'alias', 'typed_uri' => 'Fit-Healthy/Some-Article' ) ) ), 'the alias itself' );
        $this->assertNull( $engine->evaluate( self::request( array( 'requested_via' => 'index', 'typed_uri' => '' ) ) ), 'the front page' );
        $this->assertNull( $engine->evaluate( self::request( array( 'view_mode' => 'line' ) ) ), 'another view mode' );
        $this->assertNull( $engine->evaluate( self::request( array( 'policies' => array( 'content/view_system_url' ) ) ) ), 'a user with the policy' );
        $this->assertNull( $engine->evaluate( self::request( array( 'view' => 'edit' ) ) ), 'another view' );
    }

    public function testShippedRuleAnswersNotFoundWithoutAlias()
    {
        $engine = self::engine( array( 'r' => self::shippedRule() ) );
        foreach ( array( null, '', 'content/view/full/123' ) as $alias )
        {
            $result = $engine->evaluate( self::request( array( 'url_alias' => $alias ) ) );
            $this->assertSame( ezpRequestRuleResult::NOT_FOUND, $result->type, var_export( $alias, true ) );
        }
    }

    public function testActionThatCannotActPassesToFallbackThenNextRule()
    {
        $engine = self::engine( array(
            'no_target' => array( 'Conditions' => array( 'module' => 'content' ), 'Action' => 'redirect' ),
            'no_target_fallback' => array( 'Conditions' => array( 'module' => 'content' ), 'Action' => 'rewrite', 'FallbackAction' => 'redirect' ),
            'last' => array( 'Conditions' => array( 'module' => 'content' ), 'Action' => 'forbidden' ),
        ) );
        $result = $engine->evaluate( self::request() );
        $this->assertSame( 'last', $result->rule );
        $this->assertSame( ezpRequestRuleResult::FORBIDDEN, $result->type );
    }

    public function testFactsAreResolvedOnlyWhenRead()
    {
        $loaded = 0;
        $context = self::request( array(
            'class_identifier' => function () use ( &$loaded ) { $loaded++; return 'article'; },
        ) );
        $engine = self::engine( array(
            'r' => array( 'Conditions' => array( 'module' => 'user', 'class' => 'article' ), 'Action' => 'forbidden' ),
        ) );
        $engine->evaluate( $context );
        $this->assertSame( 0, $loaded, 'the module did not match, so the class was never needed' );

        $engine = self::engine( array( 'r' => array( 'Conditions' => array( 'class' => 'article' ), 'Action' => 'forbidden' ) ) );
        $engine->evaluate( $context );
        $engine->evaluate( $context );
        $this->assertSame( 1, $loaded, 'resolved once, then kept' );

        $broken = new ezpRequestContext( array( 'x' => function () { throw new RuntimeException( 'gone' ); } ) );
        $this->assertSame( 'fallback', $broken->get( 'x', 'fallback' ), 'a resolver that throws gives the default' );
        $plain = new ezpRequestContext( array( 'typed_uri' => 'phpinfo' ) );
        $this->assertSame( 'phpinfo', $plain->get( 'typed_uri' ), 'a string is a value, never a function name' );
    }

    public function testUnknownConditionOrActionNeverDecides()
    {
        $engine = self::engine( array(
            'typo' => array( 'Conditions' => array( 'modul' => 'content' ), 'Action' => 'forbidden' ),
            'bad_action' => array( 'Conditions' => array( 'module' => 'content' ), 'Action' => 'forbiden' ),
        ) );
        $this->assertNull( $engine->evaluate( self::request() ) );
        $problems = implode( "\n", $engine->validate() );
        $this->assertStringContainsString( "unknown condition 'modul'", $problems );
        $this->assertStringContainsString( "unknown action 'forbiden'", $problems );
    }

    public function testValidateNamesEveryMistake()
    {
        $this->assertSame( array(), self::engine( array( 'r' => self::shippedRule() ) )->validate() );

        $engine = self::engine( array(
            'missing' => array(),
            'redirect_no_to' => array( 'Conditions' => array( 'module' => 'content' ), 'Action' => 'redirect', 'ActionArgs' => array( 'status' => '305' ) ),
            'bad_policy' => array( 'Conditions' => array( 'policy' => 'content' ), 'Action' => 'notfound' ),
            'no_argument' => array( 'Conditions' => array( 'param' => 'x' ), 'Action' => 'notfound' ),
            'no_value' => array( 'Conditions' => array( 'module' => '' ), 'Action' => 'notfound' ),
        ), array( 'condition_handlers' => array( 'broken' => 'NoSuchClass' ), 'fact_providers' => array( 'NoSuchProvider' ) ) );
        $problems = implode( "\n", $engine->validate() );
        foreach ( array( "'missing' has no Conditions[]", "'missing' has no Action", 'needs ActionArgs[to]', 'ActionArgs[status]=305',
                         'values are module/function', 'needs a name after the colon', 'Conditions[module] has no value',
                         "condition handler 'broken'", 'FactProviders: NoSuchProvider' ) as $expected )
            $this->assertStringContainsString( $expected, $problems );
    }

    public function testAddRuleIsIdempotentAndPositions()
    {
        $engine = self::engine( array() );
        $rule = new ezpRequestRule( 'a', array( 'module' => 'content' ), 'notfound' );
        $engine->addRule( $rule );
        $engine->addRule( $rule );
        $this->assertSame( array( 'a' ), array_keys( $engine->rules() ) );

        $engine->addRule( new ezpRequestRule( 'b', array( 'module' => 'content' ), 'forbidden' ) );
        $engine->addRule( new ezpRequestRule( 'allow_first', array( 'module' => 'content' ), 'allow' ), 'a' );
        $this->assertSame( array( 'allow_first', 'a', 'b' ), array_keys( $engine->rules() ) );
        $this->assertSame( ezpRequestRuleResult::ALLOW, $engine->evaluate( self::request() )->type );

        $engine->removeRule( 'allow_first' );
        $this->assertSame( 'a', $engine->evaluate( self::request() )->rule );
    }

    public function testCustomConditionsAndActions()
    {
        $tier = new class implements ezpRequestRuleCondition
        {
            public function matches( ezpRequestContext $context, array $values, $argument )
            {
                return in_array( $context->get( 'customer_tier' ), $values, true );
            }
        };
        $paywall = new class implements ezpRequestRuleAction
        {
            public function decide( ezpRequestContext $context, array $arguments, ezpRequestRule $rule )
            {
                return ezpRequestRuleResult::moduleResult( array( 'content' => 'Subscribe to read ' . $context->get( 'node_id' ) ) );
            }
        };
        $engine = self::engine(
            array( 'paywall' => array( 'Conditions' => array( 'customer_tier' => '!gold' ), 'Action' => 'paywall' ) ),
            array( 'condition_handlers' => array( 'customer_tier' => $tier ), 'action_handlers' => array( 'paywall' => $paywall ) )
        );
        $result = $engine->evaluate( self::request( array( 'customer_tier' => 'free' ) ) );
        $this->assertSame( ezpRequestRuleResult::MODULE_RESULT, $result->type );
        $this->assertSame( 'Subscribe to read 123', $result->moduleResult['content'] );
        $this->assertNull( $engine->evaluate( self::request( array( 'customer_tier' => 'gold' ) ) ) );
        $this->assertSame( array(), $engine->validate() );

        $later = self::engine( array( 'r' => array( 'Conditions' => array( 'always' => 'x' ), 'Action' => 'notfound' ) ) );
        $later->registerCondition( 'always', $tier );
        $this->assertNull( $later->evaluate( self::request() ) );
        $later->registerCondition( 'always', 'ezpRequestConditionModule' );
        $this->assertNull( $later->evaluate( self::request() ), 'module x does not match content' );
    }

    public function testRuleProviders()
    {
        $provider = new class implements ezpRequestRuleProvider
        {
            public function rules()
            {
                return array( new ezpRequestRule( 'provided', array( 'node' => '123' ), 'forbidden' ) );
            }
        };
        $engine = self::engine(
            array( 'listed' => array( 'Conditions' => array( 'node' => '5' ), 'Action' => 'notfound' ) ),
            array( 'rule_providers' => array( $provider, 'stdClass' ) )
        );
        $this->assertSame( array( 'listed', 'provided' ), array_keys( $engine->rules() ) );
        $this->assertSame( 'provided', $engine->evaluate( self::request() )->rule );
        $this->assertStringContainsString( 'RuleProviders: stdClass', implode( "\n", $engine->validate() ) );
    }

    public function testPatterns()
    {
        $cases = array(
            array( 'uri', 'content/view/*', true ),
            array( 'uri', 'Content/View/Full/*', true ),
            array( 'uri', 'fit-healthy/*', false ),
            array( 'resolved_uri', 'content/view/full/1?3', true ),
            array( 'host', '*.example.com', true ),
            array( 'host', 'example.com', false ),
            array( 'module_view', 'content/*', true ),
            array( 'module_view', 'user/*', false ),
            array( 'param:NodeID', '12*', true ),
            array( 'param:ViewMode', 'line', false ),
            array( 'param:Missing', '*', false ),
            array( 'header:X-Requested-With', 'XMLHttpRequest', true ),
            array( 'header:x-requested-with', '*', true ),
            array( 'header:X-Other', '*', false ),
            array( 'fact:customer_tier', 'go*', true ),
        );
        foreach ( $cases as $case )
        {
            $engine = self::engine( array( 'r' => array( 'Conditions' => array( $case[0] => $case[1] ), 'Action' => 'forbidden' ) ) );
            $context = self::request( array( 'headers' => array( 'x-requested-with' => 'XMLHttpRequest' ), 'customer_tier' => 'gold' ) );
            $this->assertSame( $case[2], $engine->evaluate( $context ) !== null, "{$case[0]} = {$case[1]}" );
        }
    }

    public function testIpNetworks()
    {
        $cases = array(
            array( '10.1.2.3', '10.0.0.0/8', true ),
            array( '10.1.2.3', '10.1.2.3', true ),
            array( '10.1.2.3', '10.1.2.4', false ),
            array( '192.168.1.130', '192.168.1.128/25', true ),
            array( '192.168.1.127', '192.168.1.128/25', false ),
            array( '2001:db8::1', '2001:db8::/32', true ),
            array( '2001:db9::1', '2001:db8::/32', false ),
            array( '10.1.2.3', '2001:db8::/32', false ),
            array( '10.1.2.3', '0.0.0.0/0', true ),
            array( 'not-an-ip', '0.0.0.0/0', false ),
            array( '10.1.2.3', '10.0.0.0/33', false ),
        );
        foreach ( $cases as $case )
        {
            $engine = self::engine( array( 'r' => array( 'Conditions' => array( 'ip' => $case[1] ), 'Action' => 'forbidden' ) ) );
            $this->assertSame( $case[2], $engine->evaluate( self::request( array( 'client_ip' => $case[0] ) ) ) !== null, "{$case[0]} in {$case[1]}" );
        }
    }

    public function testPlaceholdersAndStatuses()
    {
        $engine = self::engine( array(
            'r' => array( 'Conditions' => array( 'node' => '123' ), 'Action' => 'redirect',
                          'ActionArgs' => array( 'to' => '/archive/{node_id}/{param:ViewMode}/{unknown}', 'status' => '308' ) ),
        ) );
        $result = $engine->evaluate( self::request() );
        $this->assertSame( '/archive/123/full/', $result->location );
        $this->assertSame( 308, $result->status );

        $this->assertSame( 302, ezpRequestRuleResult::redirect( '/x', 200 )->status, 'a status that is no redirect becomes 302' );
        $this->assertSame( 'content/view/full/2', ezpRequestRuleResult::rewrite( '/content/view/full/2' )->location );

        $rewrite = self::engine( array( 'r' => array( 'Conditions' => array( 'node' => '123' ), 'Action' => 'rewrite',
                                                      'ActionArgs' => array( 'to' => 'content/view/line/{node_id}' ) ) ) );
        $this->assertSame( 'rewrite to content/view/line/123', $rewrite->evaluate( self::request() )->describe() );
    }

    public function testUserAndContentConditions()
    {
        $context = self::request( array(
            'is_anonymous' => false, 'user_id' => 14, 'user_login' => 'editor.jane',
            'role_names' => array( 'Editor', 'Member' ), 'group_ids' => array( 12, 13 ),
            'policies' => array( 'content/read', 'content/view_system_url' ),
            'class_identifier' => 'article', 'section_id' => 6, 'section_identifier' => 'members',
            'path_ids' => array( 1, 2, 864, 123 ), 'state_identifiers' => array( 'ez_lock/not_locked' ),
        ) );
        $cases = array(
            array( 'user', 'logged_in', true ), array( 'user', 'anonymous', false ),
            array( 'user_id', '14', true ), array( 'user_login', 'editor.*', true ),
            array( 'role', 'editor', true ), array( 'role', 'Administrator', false ),
            array( 'group', '13', true ), array( 'group', '11', false ),
            array( 'policy', 'content/view_system_url', true ), array( 'policy', 'setup/setup', false ),
            array( 'policy', 'content/edit, content/read', true ),
            array( 'siteaccess', 'si*', true ), array( 'method', 'get', true ), array( 'scheme', 'https', true ),
            array( 'class', 'folder,article', true ), array( 'section', 'members', true ), array( 'section', '6', true ),
            array( 'section', 'standard', false ), array( 'subtree', '864', true ), array( 'subtree', '43', false ),
            array( 'node', '123', true ), array( 'view_mode', 'full', true ), array( 'requested_via', 'system', true ),
            array( 'state', 'ez_lock/*', true ), array( 'state', 'ez_lock/locked', false ),
        );
        foreach ( $cases as $case )
        {
            $engine = self::engine( array( 'r' => array( 'Conditions' => array( $case[0] => $case[1] ), 'Action' => 'forbidden' ) ) );
            $this->assertSame( $case[2], $engine->evaluate( $context ) !== null, "{$case[0]} = {$case[1]}" );
        }

        $noNode = self::request( array( 'node_id' => null, 'class_identifier' => null, 'section_id' => null, 'path_ids' => null ) );
        foreach ( array( 'class' => 'article', 'section' => '6', 'subtree' => '2', 'node' => '123' ) as $key => $value )
        {
            $engine = self::engine( array( 'r' => array( 'Conditions' => array( $key => $value ), 'Action' => 'forbidden' ) ) );
            $this->assertNull( $engine->evaluate( $noNode ), "$key on a request without a node" );
        }
    }

    public function testExplain()
    {
        $engine = self::engine( array(
            'other_module' => array( 'Conditions' => array( 'module' => 'user', 'node' => '123' ), 'Action' => 'forbidden' ),
            'no_target' => array( 'Conditions' => array( 'module' => 'content' ), 'Action' => 'redirect' ),
            'shipped' => self::shippedRule(),
            'after' => array( 'Conditions' => array( 'module' => 'content' ), 'Action' => 'forbidden' ),
        ) );
        $rows = $engine->explain( self::request() );
        $this->assertSame( array( 'module' => false, 'node' => 'not asked' ), $rows[0]['conditions'] );
        $this->assertTrue( $rows[1]['matched'] );
        $this->assertNull( $rows[1]['result'] );
        $this->assertTrue( $rows[2]['decides'] );
        $this->assertSame( 'redirect 301 to /Fit-Healthy/Some-Article', $rows[2]['result'] );
        $this->assertTrue( $rows[3]['matched'] );
        $this->assertFalse( $rows[3]['decides'], 'only the first deciding rule decides' );
    }

    public function testDisabledAndEmpty()
    {
        $disabled = self::engine( array( 'r' => self::shippedRule() ), array( 'enabled' => false ) );
        $this->assertNull( $disabled->evaluate( self::request() ) );
        $this->assertFalse( $disabled->isActive() );
        $this->assertFalse( self::engine( array() )->isActive() );
        $this->assertTrue( self::engine( array( 'r' => self::shippedRule() ) )->isActive() );
    }

    public function testLoginNeverActsOnTheSignInViews()
    {
        $engine = self::engine( array( 'everything' => array( 'Conditions' => array( 'user' => 'anonymous' ), 'Action' => 'login' ) ) );
        $this->assertSame( ezpRequestRuleResult::LOGIN, $engine->evaluate( self::request() )->type );
        foreach ( array( 'login', 'forgotpassword', 'register', 'activate' ) as $view )
            $this->assertNull( $engine->evaluate( self::request( array( 'module' => 'user', 'view' => $view ) ) ), "user/$view" );
    }

    public function testRequestsThatCachesMustNotShareAreMarked()
    {
        $headerRule = array( 'Conditions' => array( 'header:X-Test' => 'deny', 'module_view' => 'content/view' ), 'Action' => 'forbidden' );
        $engine = self::engine( array( 'r' => $headerRule ) );

        $plain = self::request();
        $this->assertNull( $engine->evaluate( $plain ) );
        $this->assertTrue( $plain->get( 'rules_vary_by_request' ), 'no header: the view runs, but a request with the header would be refused' );

        $other = self::request( array( 'module' => 'user' ) );
        $engine->evaluate( $other );
        $this->assertFalse( $other->get( 'rules_vary_by_request' ), 'a module the rule never applies to stays cacheable' );

        $shipped = self::engine( array( 'r' => self::shippedRule() ) );
        foreach ( array( self::request(), self::request( array( 'requested_via' => 'alias' ) ) ) as $context )
        {
            $shipped->evaluate( $context );
            $this->assertFalse( $context->get( 'rules_vary_by_request' ), 'the shipped rule reads only what the cache keys hold' );
        }

        $context = self::request();
        $rows = self::engine( array( 'r' => $headerRule ) )->explain( $context );
        $this->assertTrue( $rows[0]['varies'] );
        $this->assertSame( array( 'header:X-Test' => false, 'module_view' => true ), $rows[0]['conditions'] );
        $rows = self::engine( array( 'r' => $headerRule ) )->explain( self::request( array( 'view' => 'edit' ) ) );
        $this->assertSame( array( 'header:X-Test' => 'not asked', 'module_view' => false ), $rows[0]['conditions'],
                           'stable conditions are asked first: the header is never read for a view the rule cannot apply to' );
        $this->assertFalse( $rows[0]['varies'] );

        foreach ( array( 'header:X' => false, 'ip' => false, 'user_id' => false, 'user_login' => false, 'group' => false, 'fact:x' => false,
                         'uri' => true, 'node' => true, 'policy' => true, 'role' => true, 'user' => true, 'siteaccess' => true ) as $key => $stable )
            $this->assertSame( $stable, $engine->isStableCondition( $key ), $key );
    }

    public function testShippedSettingsFile()
    {
        $file = __DIR__ . '/../../../../../settings/requestrules.ini';
        $groups = self::readIni( $file );
        $this->assertArrayHasKey( 'RequestRuleSettings', $groups );
        $rules = array();
        foreach ( $groups as $name => $group )
        {
            if ( strncmp( $name, 'Rule-', 5 ) === 0 )
                $rules[substr( $name, 5 )] = ezpRequestRule::fromSettings( substr( $name, 5 ), $group );
        }
        $this->assertSame( array( 'system_url_full_view_to_alias', 'system_url_content_view_notfound', 'system_url_full_view_audit' ), array_keys( $rules ) );
        $engine = new ezpRequestRuleEngine( array( 'rules' => $rules ) );
        $this->assertSame( array(), $engine->validate() );

        $this->assertSame( ezpRequestRuleResult::REDIRECT, ( new ezpRequestRuleEngine( array( 'rules' => array( $rules['system_url_full_view_to_alias'] ) ) ) )->evaluate( self::request() )->type );
        $this->assertSame( ezpRequestRuleResult::NOT_FOUND, ( new ezpRequestRuleEngine( array( 'rules' => array( $rules['system_url_content_view_notfound'] ) ) ) )->evaluate( self::request( array( 'view_mode' => 'line' ) ) )->type );
        $this->assertSame( array( 'RuleList' => array() ), array_intersect_key( $groups['RequestRuleSettings'], array( 'RuleList' => 1 ) ), 'no rule is on by default' );
    }

    /**
     * Reads an eZ INI file the way eZINI does for what this file uses:
     * groups, Name=value, Name[]=value (Name[] alone empties), Name[key]=value.
     */
    private static function readIni( $file )
    {
        $groups = array();
        $group = null;
        foreach ( file( $file, FILE_IGNORE_NEW_LINES ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' || $line[0] === '#' )
                continue;
            if ( preg_match( '/^\[(.+)\]$/', $line, $m ) )
            {
                $group = $m[1];
                $groups[$group] = array();
                continue;
            }
            if ( !preg_match( '/^([A-Za-z0-9_-]+)(\[([^\]]*)\])?=?(.*)$/', $line, $m ) )
                continue;
            if ( isset( $m[2] ) && $m[2] !== '' )
            {
                if ( !isset( $groups[$group][$m[1]] ) )
                    $groups[$group][$m[1]] = array();
                if ( $m[3] === '' && strpos( $line, '=' ) === false )
                    $groups[$group][$m[1]] = array();
                else if ( $m[3] === '' )
                    $groups[$group][$m[1]][] = $m[4];
                else
                    $groups[$group][$m[1]][$m[3]] = $m[4];
            }
            else
                $groups[$group][$m[1]] = $m[4];
        }
        return $groups;
    }
}
