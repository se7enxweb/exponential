<?php
/**
 * File containing the expUpgradeStepHelpReadPolicy class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * exphelp/read for the roles that read the help, through expHelpReadPolicyGrant as bin/php/addexphelpreadpolicy.php
 * with its default options.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepHelpReadPolicy extends expUpgradeScriptStep
{
    public function id()
    {
        return '6.0.15-help-read-policy';
    }

    public function description()
    {
        return 'exphelp/read for the roles that read the help (bin/php/addexphelpreadpolicy.php)';
    }

    /** @return array the grant entries with their roles */
    protected function grants()
    {
        $roles = array();
        $data = array();
        foreach ( eZRole::fetchList() as $role )
        {
            $roles[(int)$role->attribute( 'id' )] = $role;
            $data[] = expHelpReadPolicyGrant::roleData( $role );
        }
        $out = array();
        foreach ( expHelpReadPolicyGrant::plan( $data, array() ) as $entry )
            if ( $entry['action'] === 'grant' && $entry['reason'] !== 'not listed' && isset( $roles[$entry['id']] ) )
                $out[] = array( $roles[$entry['id']], $entry );
        return $out;
    }

    protected function changes( expUpgradeContext $context )
    {
        $lines = array();
        foreach ( $this->grants() as $pair )
            $lines[] = sprintf( 'grant exphelp/read to %s (%d)', $pair[1]['name'], $pair[1]['id'] );
        return $lines;
    }

    protected function write( expUpgradeContext $context )
    {
        $done = array();
        foreach ( $this->grants() as $pair )
        {
            expHelpReadPolicyGrant::apply( $pair[0], $pair[1] );
            $done[] = sprintf( 'granted exphelp/read to %s (%d)', $pair[1]['name'], $pair[1]['id'] );
        }
        eZRole::expireCache();
        return $done;
    }
}
