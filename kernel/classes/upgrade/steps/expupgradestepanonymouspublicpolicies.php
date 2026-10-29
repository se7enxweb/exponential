<?php
/**
 * File containing the expUpgradeStepAnonymousPublicPolicies class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The public policies of the Anonymous role (registration, password reset ...), through expAnonymousPublicPolicies
 * as bin/php/addanonymouspublicpolicies.php with its default options. Not applicable without a role Anonymous.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepAnonymousPublicPolicies extends expUpgradeScriptStep
{
    public function id()
    {
        return '6.0.15-anonymous-public-policies';
    }

    public function description()
    {
        return 'The public policies of the Anonymous role (bin/php/addanonymouspublicpolicies.php)';
    }

    /** @return array the plan entries that grant */
    protected function grants( $role )
    {
        $out = array();
        foreach ( expAnonymousPublicPolicies::plan( expAnonymousPublicPolicies::policiesOf( $role ), expAnonymousPublicPolicies::availableModules(), array(), array() ) as $entry )
            if ( $entry['action'] === 'grant' )
                $out[] = $entry;
        return $out;
    }

    protected function changes( expUpgradeContext $context )
    {
        $role = self::role( expAnonymousPublicPolicies::ROLE_NAME );
        if ( !$role )
        {
            $this->notApplicable = true;
            $this->reason = 'no role ' . expAnonymousPublicPolicies::ROLE_NAME;
            return null;
        }
        $lines = array();
        foreach ( $this->grants( $role ) as $entry )
            $lines[] = "add {$entry['module']}/{$entry['function']} to " . $role->attribute( 'name' ) . ": {$entry['reason']}";
        return $lines;
    }

    protected function write( expUpgradeContext $context )
    {
        $role = self::role( expAnonymousPublicPolicies::ROLE_NAME );
        $done = array();
        foreach ( $this->grants( $role ) as $entry )
        {
            expAnonymousPublicPolicies::apply( $role, $entry );
            $done[] = "added {$entry['module']}/{$entry['function']}";
        }
        eZRole::expireCache();
        return $done;
    }
}
