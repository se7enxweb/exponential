<?php
/**
 * File containing the eZPackageRemovalPlan class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What removing a selection of packages would do, before it is done: per package its directory, files and size,
 * whether it is installed (its install record goes with it, what it installed stays), which packages require it,
 * and whether the installer takes it as a source; and which of the selection cannot be removed, and why.
 *
 * The package list removed whatever names a form posted, without the package/remove policy and without saying
 * what went. A selection value is a package name ("sevenx_classes", as the old list posted) or a name with its
 * repository ("sevenx_classes@7x", as the list posts now, so two packages of the same name in two repositories
 * cannot be mistaken for each other). No database: the cards come from eZPackageCatalog::scan().
 */
class eZPackageRemovalPlan
{
    /**
     * The name and repository of a selection value, or false when it is not a safe one.
     *
     * @param mixed $value
     * @return array( string $name, string $repositoryID ('' when not given) )|false
     */
    static function parseValue( $value )
    {
        if ( !is_string( $value ) )
            return false;
        $parts = explode( '@', $value );
        if ( count( $parts ) > 2 )
            return false;
        $name = $parts[0];
        $repositoryID = isset( $parts[1] ) ? $parts[1] : '';
        if ( !eZPackageRequestGuard::isSafeName( $name ) )
            return false;
        if ( $repositoryID !== '' && !eZPackageRequestGuard::isSafeName( $repositoryID ) )
            return false;
        return array( $name, $repositoryID );
    }

    /**
     * The plan.
     *
     * @param mixed $selection the posted PackageSelection
     * @param string $repositoryID the repository the list showed ('' for all): a bare name is looked for there
     * @param array $cards eZPackageCatalog::scan()['cards']
     * @param callable|null $canRemove function( array $card ): bool, the package/remove policy; null allows all
     * @param string $storagePath the package storage path, for the paths shown
     * @return array( 'items' => removable cards (key => card + 'path'), 'refused' => list of array( 'value', 'name',
     *                'reason' => 'unsafe'|'not_found'|'ambiguous'|'policy'|'links' ), 'installer_sources' => int,
     *                'installed' => int, 'files' => int, 'bytes' => int, 'required_elsewhere' => int )
     */
    static function build( $selection, $repositoryID, array $cards, $canRemove = null, $storagePath = '' )
    {
        $plan = array( 'items' => array(), 'refused' => array(), 'installer_sources' => 0, 'installed' => 0,
                       'files' => 0, 'bytes' => 0, 'required_elsewhere' => 0 );
        $values = is_array( $selection ) ? $selection : ( $selection === null || $selection === '' ? array() : array( $selection ) );
        foreach ( $values as $value )
        {
            $parsed = self::parseValue( $value );
            if ( $parsed === false )
            {
                $plan['refused'][] = array( 'value' => is_scalar( $value ) ? (string)$value : '', 'name' => '', 'reason' => 'unsafe' );
                continue;
            }
            list( $name, $repository ) = $parsed;
            if ( $repository === '' )
                $repository = (string)$repositoryID;
            $matches = array();
            foreach ( $cards as $key => $card )
                if ( $card['name'] === $name && ( $repository === '' || $card['repository_id'] === $repository ) )
                    $matches[] = $key;
            if ( !$matches )
            {
                $plan['refused'][] = array( 'value' => (string)$value, 'name' => $name, 'reason' => 'not_found' );
                continue;
            }
            if ( count( $matches ) > 1 )
            {
                $plan['refused'][] = array( 'value' => (string)$value, 'name' => $name, 'reason' => 'ambiguous' );
                continue;
            }
            $card = $cards[$matches[0]];
            if ( isset( $plan['items'][$card['key']] ) )
                continue;
            if ( $canRemove !== null && !call_user_func( $canRemove, $card ) )
            {
                $plan['refused'][] = array( 'value' => (string)$value, 'name' => $name, 'reason' => 'policy' );
                continue;
            }
            // eZDir::recursiveDelete() follows a link to a directory and empties its target: such a package is
            // left for someone to look at on the server
            if ( !empty( $card['links'] ) )
            {
                $plan['refused'][] = array( 'value' => (string)$value, 'name' => $name, 'reason' => 'links' );
                continue;
            }
            $card['path'] = rtrim( (string)$storagePath, '/' ) . ( $storagePath !== '' ? '/' : '' ) . $card['repository_id'] . '/' . $card['name'];
            $card['value'] = $card['name'] . '@' . $card['repository_id'];
            $plan['items'][$card['key']] = $card;
        }
        foreach ( $plan['items'] as $card )
        {
            if ( $card['installer_source'] )
                $plan['installer_sources']++;
            if ( $card['installed'] )
                $plan['installed']++;
            $plan['files'] += $card['files'];
            $plan['bytes'] += $card['bytes'];
            // required by a package that stays
            foreach ( $card['required_by'] as $requirer )
            {
                $staying = true;
                foreach ( $plan['items'] as $other )
                    if ( $other['name'] === $requirer )
                        $staying = false;
                if ( $staying )
                {
                    $plan['required_elsewhere']++;
                    break;
                }
            }
        }
        return $plan;
    }

    /**
     * The selection values of a plan, as the confirmation posts them and the session keeps them.
     *
     * @return string[]
     */
    static function values( array $plan )
    {
        $values = array();
        foreach ( $plan['items'] as $card )
            $values[] = $card['value'];
        sort( $values );
        return $values;
    }

    /**
     * Whether a confirmation may remove what it posts: every posted value was offered by the confirmation page
     * (kept in the session), and the installer sources among them were confirmed with their own tick.
     *
     * @param string[] $offered the values the confirmation page offered
     * @param array $plan the plan built again from the posted values
     * @param bool $installerSourcesConfirmed
     * @return string|null null when it may, else 'not_offered' or 'installer_source'
     */
    static function confirmationProblem( array $offered, array $plan, $installerSourcesConfirmed )
    {
        foreach ( self::values( $plan ) as $value )
            if ( !in_array( $value, $offered, true ) )
                return 'not_offered';
        if ( $plan['installer_sources'] > 0 && !$installerSourcesConfirmed )
            return 'installer_source';
        return null;
    }
}

?>
