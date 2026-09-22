<?php
/**
 * File containing the eZRoleFunctionCollection class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZRoleFunctionCollection ezrolefunctioncollection.php
  \brief The class eZRoleFunctionCollection does

*/


if ( !class_exists( 'eZRoleFunctionCollection', false ) ) {
class eZRoleFunctionCollection
{
    function fetchRole( $roleID )
    {
        $role = eZRole::fetch( $roleID );
        return array( 'result' => $role );
    }

    /**
     * One page of a role's policies.
     *
     * $role.policies is every policy the role has. That is what the permission
     * system needs and what a screen must not ask for: a role carrying policies
     * in the millions cannot be drawn, and does not need to be to show the
     * first twenty five.
     *
     * @param int $roleID
     * @param int $offset
     * @param int|false $limit
     * @return array
     */
    static function fetchRolePolicies( $roleID, $offset = 0, $limit = false )
    {
        $role = eZRole::fetch( (int)$roleID );

        if ( !$role instanceof eZRole )
            return array( 'result' => array() );

        return array( 'result' => $role->policyPage( (int)$offset, $limit ) );
    }

    /**
     * How many policies a role has.
     *
     * @param int $roleID
     * @return array
     */
    static function fetchRolePolicyCount( $roleID )
    {
        $role = eZRole::fetch( (int)$roleID );

        if ( !$role instanceof eZRole )
            return array( 'result' => 0 );

        return array( 'result' => $role->policyCount() );
    }

}
}


?>
