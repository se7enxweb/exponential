<?php
/**
 * File containing the eZTemplateRoleOperator class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) 1999 - 2026 7x / Brookins Consulting / eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version 1.0.2
 * @package exponential
 */

/**
 * The eZTemplateRoleOperator class provides template operators for checking user role membership
 * and policies (doc/eng-us/features/6.0/role-and-policy-template-operators.md).
 *
 * The piped value is ignored: the user is the one named by the parameters, or the current user.
 *
 * Template usage examples:
 * {if has_role('Administrator')}
 * {if has_role_id(1)}
 * {if has_any_role(array('Editor', 'Administrator'))}
 * {if has_any_role(array(1, 2, 3))}
 * {if has_any_role(array('Editor', 2, 'Publisher'), 0, true())}   (match_all; 0 = the current user)
 * {if has_role_by_user($user_object, 1)}
 * {if has_role_by_user_id(42, 1)}
 * {if has_policy('content', 'edit')}
 * {if has_policy_by_user($user_object, 'content', 'edit')}
 */
class eZTemplateRoleOperator
{
    /**
     * Array of operators provided by this class
     *
     * @var array
     */
    private $Operators;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Operators = array(
            'has_role',
            'has_role_id',
            'has_any_role',
            'has_role_by_user',
            'has_role_by_user_id',
            'has_policy',
            'has_policy_by_user'
        );
    }

    /**
     * Returns the operators in this class
     *
     * @return array
     */
    public function operatorList()
    {
        return $this->Operators;
    }

    /**
     * Returns true to tell the template engine that the parameter list
     * exists per operator type
     *
     * @return bool
     */
    public function namedParameterPerOperator()
    {
        return true;
    }

    /**
     * Returns the named parameters for each operator
     *
     * @return array
     */
    public function namedParameterList()
    {
        return array(
            'has_role' => array(
                'role_name' => array(
                    'type' => 'string',
                    'required' => true,
                    'default' => ''
                ),
                'user_id' => array(
                    'type' => 'integer',
                    'required' => false,
                    'default' => 0
                )
            ),
            'has_role_id' => array(
                'role_id' => array(
                    'type' => 'integer',
                    'required' => true,
                    'default' => 0
                ),
                'user_id' => array(
                    'type' => 'integer',
                    'required' => false,
                    'default' => 0
                )
            ),
            'has_any_role' => array(
                'roles' => array(
                    'type' => 'array',
                    'required' => true,
                    'default' => array()
                ),
                'user_id' => array(
                    'type' => 'integer',
                    'required' => false,
                    'default' => 0
                ),
                'match_all' => array(
                    'type' => 'boolean',
                    'required' => false,
                    'default' => false
                )
            ),
            'has_role_by_user' => array(
                'user' => array(
                    'type' => 'object',
                    'required' => true,
                    'default' => null
                ),
                'role_id' => array(
                    'type' => 'integer',
                    'required' => true,
                    'default' => 0
                )
            ),
            'has_role_by_user_id' => array(
                'user_id' => array(
                    'type' => 'integer',
                    'required' => true,
                    'default' => 0
                ),
                'role_id' => array(
                    'type' => 'integer',
                    'required' => true,
                    'default' => 0
                )
            ),
            'has_policy' => array(
                'module' => array(
                    'type' => 'string',
                    'required' => true,
                    'default' => ''
                ),
                'function' => array(
                    'type' => 'string',
                    'required' => true,
                    'default' => ''
                ),
                'user_id' => array(
                    'type' => 'integer',
                    'required' => false,
                    'default' => 0
                )
            ),
            'has_policy_by_user' => array(
                'user' => array(
                    'type' => 'object',
                    'required' => true,
                    'default' => null
                ),
                'module' => array(
                    'type' => 'string',
                    'required' => true,
                    'default' => ''
                ),
                'function' => array(
                    'type' => 'string',
                    'required' => true,
                    'default' => ''
                )
            )
        );
    }

    /**
     * Executes the operator
     *
     * @param eZTemplate $tpl Template object
     * @param string $operatorName The operator name
     * @param array $operatorParameters Operator parameters
     * @param string $rootNamespace Root namespace
     * @param string $currentNamespace Current namespace
     * @param mixed &$operatorValue The value to operate on
     * @param array $namedParameters Named parameters
     */
    public function modify(
        $tpl,
        $operatorName,
        $operatorParameters,
        $rootNamespace,
        $currentNamespace,
        &$operatorValue,
        $namedParameters
    )
    {
        switch ( $operatorName )
        {
            case 'has_role':
            {
                $roleName = $namedParameters['role_name'];
                $userId = self::userIdOrCurrent( $namedParameters['user_id'] );

                $operatorValue = $this->isMemberOfRole( $userId, $roleName );
            }
            break;

            case 'has_role_id':
            {
                $roleId = (int)$namedParameters['role_id'];
                $userId = self::userIdOrCurrent( $namedParameters['user_id'] );

                $operatorValue = $this->isMemberOfRoleId( $userId, $roleId );
            }
            break;

            case 'has_any_role':
            {
                $roles = $namedParameters['roles'];
                $userId = self::userIdOrCurrent( $namedParameters['user_id'] );
                $matchAll = (bool)$namedParameters['match_all'];

                $operatorValue = $this->isMemberOfAnyRole( $userId, $roles, $matchAll );
            }
            break;

            case 'has_role_by_user':
            {
                $user = $namedParameters['user'];
                $roleId = $namedParameters['role_id'];
                
                $operatorValue = self::memberOfRoleByUser( $user, $roleId );
            }
            break;

            case 'has_role_by_user_id':
            {
                $userId = (int)$namedParameters['user_id'];
                $roleId = (int)$namedParameters['role_id'];

                $operatorValue = self::memberOfRoleByUserId( $userId, $roleId );
            }
            break;

            case 'has_policy':
            {
                $module = $namedParameters['module'];
                $function = $namedParameters['function'];
                $userId = self::userIdOrCurrent( $namedParameters['user_id'] );

                $operatorValue = $this->userHasPolicy( $userId, $module, $function );
            }
            break;

            case 'has_policy_by_user':
            {
                $user = $namedParameters['user'];
                $module = $namedParameters['module'];
                $function = $namedParameters['function'];

                $operatorValue = self::hasPolicyByUser($user, $module, $function);
            }
            break;
        }
    }

    /**
     * The user id a template passed, or the current user's for 0 (the default) and an empty value
     *
     * @param mixed $userId The user_id parameter (an integer, or a string of digits such as $node.contentobject_id)
     * @return int
     */
    private static function userIdOrCurrent( $userId )
    {
        $userId = (int)$userId;
        return $userId === 0 ? (int)eZUser::currentUserID() : $userId;
    }

    /**
     * Check if user is member of a specific role by role name
     *
     * @param int $userId User ID
     * @param string $roleName Role name to check
     * @return bool True if user has the role, false otherwise
     */
    public function isMemberOfRole( $userId, $roleName )
    {
        if ( empty( $roleName ) || $userId <= 0 )
        {
            return false;
        }

        $user = eZUser::fetch( $userId );
        if ( !$user instanceof eZUser )
        {
            return false;
        }

        $roles = $user->roles();
        
        foreach ( $roles as $role )
        {
            if ( $role instanceof eZRole && $role->attribute('name') == $roleName )
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user is member of a specific role by role ID
     *
     * @param int $userId User ID
     * @param int $roleId Role ID to check
     * @return bool True if user has the role, false otherwise
     */
    public function isMemberOfRoleId( $userId, $roleId )
    {
        if ( $roleId <= 0 || $userId <= 0 )
        {
            return false;
        }

        $user = eZUser::fetch( $userId );
        if ( !$user instanceof eZUser )
        {
            return false;
        }

        $roles = $user->roles();
        
        foreach ( $roles as $role )
        {
            if ( $role instanceof eZRole && $role->attribute('id') == $roleId )
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user is member of any role from provided roles (by name or ID)
     *
     * @param int $userId User ID
     * @param array $roles Array of role names (strings) or role IDs (integers)
     * @param bool $matchAll If true, user must have all roles; if false, user must have at least one role
     * @return bool True if condition is met, false otherwise
     */
    public function isMemberOfAnyRole( $userId, $roles, $matchAll = false )
    {
        if ( empty( $roles ) || !is_array( $roles ) || $userId <= 0 )
        {
            return false;
        }

        $user = eZUser::fetch( $userId );
        if ( !$user instanceof eZUser )
        {
            return false;
        }

        $userRoles = $user->roles();
        $userRoleNames = array();
        $userRoleIds = array();
        
        // Build arrays of user's role names and IDs
        foreach ( $userRoles as $role )
        {
            if ( $role instanceof eZRole )
            {
                $userRoleNames[] = $role->attribute('name');
                $userRoleIds[] = (int)$role->attribute('id');
            }
        }

        if ( $matchAll )
        {
            // User must have all specified roles
            foreach ( $roles as $role )
            {
                $matched = false;
                
                // Check if it's a role ID (integer) or role name (string)
                if ( is_int( $role ) || ( is_string( $role ) && ctype_digit( $role ) ) )
                {
                    // Match by role ID
                    if ( in_array( (int)$role, $userRoleIds ) )
                    {
                        $matched = true;
                    }
                }
                else
                {
                    // Match by role name
                    if ( in_array( $role, $userRoleNames ) )
                    {
                        $matched = true;
                    }
                }
                
                // If any role doesn't match, return false
                if ( !$matched )
                {
                    return false;
                }
            }
            return true;
        }
        else
        {
            // User must have at least one of the specified roles
            foreach ( $roles as $role )
            {
                // Check if it's a role ID (integer) or role name (string)
                if ( is_int( $role ) || ( is_string( $role ) && ctype_digit( $role ) ) )
                {
                    // Match by role ID
                    if ( in_array( (int)$role, $userRoleIds ) )
                    {
                        return true;
                    }
                }
                else
                {
                    // Match by role name
                    if ( in_array( $role, $userRoleNames ) )
                    {
                        return true;
                    }
                }
            }
            return false;
        }
    }

    /**
     * Return true if user roles of the provided user object match provided roleID
     *
     * @static
     * @param eZUser $user User object
     * @param int $roleID Role ID to check
     * @return bool True if user roles contain a match of the roleID parameter, false otherwise
     */
    static function memberOfRoleByUser( $user, $roleID )
    {
        // Anything but a user object (a number piped in, an empty variable) is no user
        if ( !$user instanceof eZUser )
        {
            return false;
        }

        return self::memberOfRoleByUserId( (int)$user->attribute( 'contentobject_id' ), (int)$roleID );
    }

    /**
     * Return true if user roles of the provided userID match provided roleID
     *
     * @static
     * @param int $userID User content object ID
     * @param int $roleID Role ID to check
     * @return bool True if user roles contain a match of the roleID parameter, false otherwise
     */
    static function memberOfRoleByUserId( $userID, $roleID )
    {
        $result = false;
        
        if ( $roleID <= 0 || $userID <= 0 )
        {
            return false;
        }

        $user = eZUser::fetch( $userID );
        if ( !$user instanceof eZUser )
        {
            return false;
        }

        $roles = $user->roles();
        
        foreach ( $roles as $role )
        {
            if ( $role instanceof eZRole && $role->attribute('id') == $roleID )
            {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Check if user has a specific policy by module and function
     *
     * A policy for every module (`*`, as the Administrator role has) or for every function of the module
     * (`content/*`) counts, as it does for eZUser::hasAccessTo(). Limitations are not evaluated.
     *
     * @param int $userId User ID
     * @param string $module Policy module name (e.g., 'content', 'user')
     * @param string $function Policy function name (e.g., 'read', 'edit', 'create')
     * @return bool True if user has the policy, false otherwise
     */
    public function userHasPolicy( $userId, $module, $function )
    {
        $userId = (int)$userId;
        if ( empty( $module ) || empty( $function ) || $userId <= 0 )
        {
            return false;
        }

        $user = eZUser::fetch( $userId );
        if ( !$user instanceof eZUser )
        {
            return false;
        }

        foreach ( $user->roles() as $role )
        {
            if ( !$role instanceof eZRole )
            {
                continue;
            }

            foreach ( $role->policyList() as $policy )
            {
                if ( !$policy instanceof eZPolicy )
                {
                    continue;
                }

                $policyModule = $policy->attribute( 'module_name' );
                $policyFunction = $policy->attribute( 'function_name' );

                // An exact match, or a policy for every module / every function of the module
                if ( ( $policyModule == $module || $policyModule == '*' ) &&
                     ( $policyFunction == $function || $policyFunction == '*' ) )
                {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if user object has a specific policy by module and function
     *
     * @static
     * @param eZUser $user User object
     * @param string $module Policy module name (e.g., 'content', 'user')
     * @param string $function Policy function name (e.g., 'read', 'edit', 'create')
     * @return bool True if user has the policy, false otherwise
     */
    static function hasPolicyByUser( $user, $module, $function )
    {
        if ( !$user instanceof eZUser )
        {
            return false;
        }

        $instance = new self();
        return $instance->userHasPolicy( (int)$user->attribute( 'contentobject_id' ), $module, $function );
    }
}

?>