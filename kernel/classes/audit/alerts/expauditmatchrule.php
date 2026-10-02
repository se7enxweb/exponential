<?php
/**
 * The alert rule class "match" (RuleClasses[match]=expAuditMatchRule): fires for every record matching Event (and,
 * with Policies[], only when the role the record names grants one of those module/function pairs). Each record
 * fires once (GroupBy=id by default); with GroupBy and Window set, once per group and window.
 *
 *   [AlertRule_admin_role_granted]  Class=match  Event=access.role.assign  Policies[]=*\/*  Policies[]=setup/*
 *   [AlertRule_audit_disabled]      Class=match  Event=system.audit.disable
 *
 * Policies[]: "*\/*" matches a policy on every module; "setup/*" a policy on the setup module (any function) or on
 * every module; "audit/manage" that function, a policy on all audit functions, or on every module. The role's
 * policies come from the record (after.policies as "module/function" strings) or, for object.type role, from the
 * database (eZRole).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditMatchRule extends expAuditAlertRuleBase
{
    public function defaults()
    {
        return array( 'Threshold' => '1', 'Window' => '0', 'GroupBy' => 'id', 'Policies' => array() );
    }

    public function evaluate( array $config, array $records, $state )
    {
        $config += $this->defaults();
        $policies = array_values( array_filter( array_map( 'trim', (array)$config['Policies'] ), 'strlen' ) );
        $windowMs = max( 0, (int)$config['Window'] ) * 1000;
        $alerts = array();
        foreach ( $records as $r )
        {
            $granted = null;
            if ( $policies )
            {
                $granted = self::grantedPairs( $r, $policies );
                if ( !$granted )
                    continue;
            }
            $a = $this->count( $state, self::groupOf( $r, $config['GroupBy'] ), $r, 1, max( 1, (int)$config['Threshold'] ), $windowMs );
            if ( $a )
            {
                $a['message'] = $config['Event'] . ( $granted ? ' granting ' . implode( ', ', $granted ) : '' );
                if ( $granted )
                    $a['policies'] = $granted;
                $alerts[] = $a;
            }
        }
        return $alerts;
    }

    /**
     * The rule's policy patterns the record's role grants.
     *
     * @param array $record
     * @param string[] $patterns module/function
     * @return string[]
     */
    public static function grantedPairs( array $record, array $patterns )
    {
        $have = self::policiesOf( $record );
        $out = array();
        foreach ( $patterns as $p )
        {
            list( $pm, $pf ) = array_pad( explode( '/', $p, 2 ), 2, '*' );
            foreach ( $have as $h )
            {
                list( $m, $f ) = array_pad( explode( '/', $h, 2 ), 2, '*' );
                $moduleOk = $pm === '*' ? $m === '*' : ( $m === '*' || $m === $pm );
                $functionOk = $pf === '*' || $f === '*' || $f === $pf;
                if ( $moduleOk && $functionOk )
                {
                    $out[] = $p;
                    break;
                }
            }
        }
        return array_values( array_unique( $out ) );
    }

    /**
     * The module/function pairs of the role a record names.
     *
     * @param array $record
     * @return string[]
     */
    public static function policiesOf( array $record )
    {
        foreach ( array( 'after.policies', 'object.policies' ) as $path )
        {
            $p = self::field( $record, $path );
            if ( is_array( $p ) && $p )
                return array_map( 'strval', array_values( $p ) );
        }
        $roleID = null;
        if ( isset( $record['object']['type'], $record['object']['id'] ) && $record['object']['type'] === 'role' )
            $roleID = (int)$record['object']['id'];
        if ( !$roleID || !class_exists( 'eZRole' ) || expAuditConfig::isOverridden() )
            return array();
        try
        {
            $role = eZRole::fetch( $roleID );
            if ( !$role )
                return array();
            $out = array();
            foreach ( $role->policyList() as $policy )
                $out[] = $policy->attribute( 'module_name' ) . '/' . $policy->attribute( 'function_name' );
            return $out;
        }
        catch ( Throwable $e )
        {
            return array();
        }
    }
}
