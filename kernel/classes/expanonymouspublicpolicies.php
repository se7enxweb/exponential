<?php
/**
 * File containing the expAnonymousPublicPolicies class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The policies a visitor who is not signed in needs on a public site, beyond reading content: registering, reading
 * the RSS feeds, the tag pages of eztags and the public views of cjw_newsletter.
 *
 * A new installation takes user/register out of PolicyOmitList[] (PolicyOmitListExclude[]=user/register, written by
 * the setup wizard and the kickstarter), so visitors can register only when the Anonymous role has the policy
 * user/register. Installations made from 2026-10-12 until the base data carried that policy have the setting without
 * the policy and refuse every registration. The setup step guarantees user/register on every new installation
 * (ensure()), whatever the site package does; bin/php/addanonymouspublicpolicies.php adds the missing policies on an
 * installation that exists (plan() and apply()).
 *
 * What a visitor already gets elsewhere is not repeated here: content/read for the Standard section (base data) also
 * opens content/search, content/advancedsearch and content/download, which declare the function read;
 * user/login, user/activate, user/success and user/forgotpassword are in PolicyOmitList[] or come with user/login;
 * oauth/authorize is checked by its view after signing in. Reading other sections, ezjscore/call function lists and
 * the site's own modules depend on the site and stay with the site package.
 *
 * plan() works on plain arrays, so it is tested without a database.
 */
class expAnonymousPublicPolicies
{
    /**
     * The role name the base data and every site package give the anonymous user.
     */
    const ROLE_NAME = 'Anonymous';

    /**
     * The policies, in the order they are planned. 'needs' names the policy module that must exist for the policy to
     * mean anything (an extension's module); a kernel module has none.
     *
     * @return array each: module, function, needs (module name or ''), reason
     */
    public static function definitions()
    {
        return array(
            array( 'module' => 'user', 'function' => 'register', 'needs' => '',
                   'reason' => 'registration: a new installation takes user/register out of PolicyOmitList[]' ),
            array( 'module' => 'rss', 'function' => 'feed', 'needs' => '',
                   'reason' => 'the RSS feeds (rss/feed/<name>) every site package publishes' ),
            array( 'module' => 'tags', 'function' => 'view', 'needs' => 'tags',
                   'reason' => 'the tag pages article and recipe pages link to (eztags)' ),
            array( 'module' => 'newsletter', 'function' => 'subscribe', 'needs' => 'newsletter',
                   'reason' => 'the newsletter subscription form (cjw_newsletter)' ),
            array( 'module' => 'newsletter', 'function' => 'configure', 'needs' => 'newsletter',
                   'reason' => 'the subscription settings link of every newsletter mail (cjw_newsletter)' ),
            array( 'module' => 'newsletter', 'function' => 'unsubscribe', 'needs' => 'newsletter',
                   'reason' => 'the unsubscribe link of every newsletter mail (cjw_newsletter)' ),
            array( 'module' => 'newsletter', 'function' => 'archive', 'needs' => 'newsletter',
                   'reason' => 'the web version of a sent newsletter (cjw_newsletter)' ),
        );
    }

    /**
     * The policies of a role as plain data for plan().
     *
     * @param eZRole $role
     * @return array each: module, function
     */
    public static function policiesOf( $role )
    {
        $policies = array();
        foreach ( $role->policyList() as $policy )
        {
            $policies[] = array( 'module' => (string)$policy->attribute( 'module_name' ),
                                 'function' => (string)$policy->attribute( 'function_name' ) );
        }
        return $policies;
    }

    /**
     * Whether a role grants module/function in any form: * / *, module / *, or module / function with or without
     * limitations (a limited policy is the site's choice and is not widened).
     *
     * @param array $policies as policiesOf() returns them
     * @param string $module
     * @param string $function
     * @return bool
     */
    public static function covers( array $policies, $module, $function )
    {
        foreach ( $policies as $policy )
        {
            if ( $policy['module'] === '*' )
                return true;
            if ( $policy['module'] === $module && ( $policy['function'] === '*' || $policy['function'] === $function ) )
                return true;
        }
        return false;
    }

    /**
     * Decides which policies the role gets.
     *
     * @param array $policies the role's policies (policiesOf())
     * @param string[] $modules the policy modules that exist on the installation (needs is checked against them)
     * @param string[] $only module/function entries to consider; empty for all
     * @param string[] $skip module/function entries never to grant (e.g. user/register on a site that keeps
     *                       registration closed)
     * @return array one entry per definition: module, function, reason, action ("grant" or "skip"), why
     */
    public static function plan( array $policies, array $modules, array $only = array(), array $skip = array() )
    {
        $plan = array();
        foreach ( self::definitions() as $definition )
        {
            $name = $definition['module'] . '/' . $definition['function'];
            $entry = array( 'module' => $definition['module'], 'function' => $definition['function'],
                            'reason' => $definition['reason'] );
            if ( $only && !in_array( $name, $only, true ) )
                $plan[] = $entry + array( 'action' => 'skip', 'why' => 'not asked for' );
            else if ( in_array( $name, $skip, true ) )
                $plan[] = $entry + array( 'action' => 'skip', 'why' => 'skipped on request' );
            else if ( $definition['needs'] !== '' && !in_array( $definition['needs'], $modules, true ) )
                $plan[] = $entry + array( 'action' => 'skip', 'why' => "no module {$definition['needs']} on this installation" );
            else if ( self::covers( $policies, $definition['module'], $definition['function'] ) )
                $plan[] = $entry + array( 'action' => 'skip', 'why' => 'the role has it already' );
            else
                $plan[] = $entry + array( 'action' => 'grant', 'why' => 'missing' );
        }
        return $plan;
    }

    /**
     * Adds a planned policy (without limitations) to the role.
     *
     * @param eZRole $role
     * @param array $entry a "grant" entry of plan()
     * @return eZPolicy|false
     */
    public static function apply( $role, array $entry )
    {
        if ( !isset( $entry['action'] ) || $entry['action'] !== 'grant' )
            return false;
        return $role->appendPolicy( $entry['module'], $entry['function'] );
    }

    /**
     * The setup step's guarantee: the Anonymous role of a new installation has user/register, whatever the base data
     * and the site package gave it. Nothing else is added: the other policies are the site package's choice.
     *
     * @return string what happened, for the setup log
     */
    public static function ensure()
    {
        $role = eZRole::fetchByName( self::ROLE_NAME );
        if ( !is_object( $role ) )
            return 'no role ' . self::ROLE_NAME . ', user/register not added';
        $plan = self::plan( self::policiesOf( $role ), array(), array( 'user/register' ) );
        foreach ( $plan as $entry )
        {
            if ( $entry['action'] === 'grant' )
            {
                self::apply( $role, $entry );
                return 'user/register added to the role ' . self::ROLE_NAME;
            }
        }
        return 'the role ' . self::ROLE_NAME . ' has user/register';
    }

    /**
     * The policy modules of the installation (kernel and active extensions), for plan().
     *
     * @return string[]
     */
    public static function availableModules()
    {
        $modules = array();
        foreach ( eZModule::globalPathList() as $path )
        {
            foreach ( (array)glob( rtrim( $path, '/' ) . '/*/module.php' ) as $file )
                $modules[] = basename( dirname( $file ) );
        }
        return array_values( array_unique( $modules ) );
    }
}
