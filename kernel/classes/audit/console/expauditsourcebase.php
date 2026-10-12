<?php
/**
 * The base of an audit console source (expAuditSource): every method answers "nothing", so a source implements only
 * what it shows. Helpers build console addresses and check a policy.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

abstract class expAuditSourceBase implements expAuditSource
{
    public function channel()
    {
        return '';
    }

    public function problem()
    {
        return '';
    }

    public function links( $view, array $filters )
    {
        return array();
    }

    public function dashboard( expAuditQuery $q, ?array $allowed, array $period )
    {
        return null;
    }

    public function event( array $record )
    {
        return null;
    }

    public function linkFor( $type, $id )
    {
        return null;
    }

    public function charts()
    {
        return array();
    }

    /** No columns of its own: the CSV export has the generic ones only */
    public function exportColumns()
    {
        return array();
    }

    /**
     * A console address with filters as view parameters: audit/console/(channel)/ai/(name)/system.expai.*
     *
     * @param string $view audit/console, audit/charts, audit/export, audit/recent
     * @param array $filters
     * @return string
     */
    public static function consoleUrl( $view, array $filters )
    {
        return expAuditConsole::url( $view, $filters );
    }

    /**
     * @param string $module
     * @param string $function
     * @return bool the current user holds the policy (with or without limitations)
     */
    public static function hasPolicy( $module, $function )
    {
        $user = eZUser::currentUser();
        $access = $user ? $user->hasAccessTo( $module, $function ) : array( 'accessWord' => 'no' );
        return $access['accessWord'] !== 'no';
    }
}
