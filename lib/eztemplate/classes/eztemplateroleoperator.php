<?php
/**
 * File containing the eZTemplateRoleOperator class
 *
 * @copyright Copyright (C) 1999 - 2026 7x / Brookins Consulting / eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version 1.0.2
 * @package exponential
 */

/**
 * The eZTemplateRoleOperator class provides template operators for checking user role membership
 * 
 * Template usage examples:
 * {if $current_user|member_of_role('Administrator')}
 * {if $current_user|member_of_role_id(1)}
 * {if $current_user|member_of_any_role(array('Editor', 'Administrator'))}
 * {if $current_user|member_of_any_role(array(1, 2, 3))}
 * {if $current_user|member_of_any_role(array('Editor', 2, 'Publisher'), match_all=true())}
 * {if $user_object|member_of_role_by_user(1)}
 * {if 42|member_of_role_by_user_id(1)}
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
            'member_of_role',
            'member_of_role_id',
            'member_of_any_role',
            'member_of_role_by_user',
            'member_of_role_by_user_id',
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
            'member_of_role' => array(
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
            'member_of_role_id' => array(
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
            'member_of_any_role' => array(
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
            'member_of_role_by_user' => array(
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
            'member_of_role_by_user_id' => array(
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
            case 'member_of_role':
            {
                $roleName = $namedParameters['role_name'];
                $userId = $namedParameters['user_id'];
                
                if ( $userId === 0 )
                {
                    $userId = eZUser::currentUserID();
                }
                
                $operatorValue = $this->isMemberOfRole( $userId, $roleName );
            }
            break;

            case 'member_of_role_id':
            {
                $roleId = $namedParameters['role_id'];
                $userId = $namedParameters['user_id'];
                
                if ( $userId === 0 )
                {
                    $userId = eZUser::currentUserID();
                }
                
                $operatorValue = $this->isMemberOfRoleId( $userId, $roleId );
            }
            break;

            case 'member_of_any_role':
            {
                $roles = $namedParameters['roles'];
                $userId = $namedParameters['user_id'];
                $matchAll = $namedParameters['match_all'];
                
                if ( $userId === 0 )
                {
                    $userId = eZUser::currentUserID();
                }
                
                $operatorValue = $this->isMemberOfAnyRole( $userId, $roles, $matchAll );
            }
            break;

            case 'member_of_role_by_user':
            {
                $user = $namedParameters['user'];
                $roleId = $namedParameters['role_id'];
                
                $operatorValue = self::memberOfRoleByUser( $user, $roleId );
            }
            break;

            case 'member_of_role_by_user_id':
            {
                $userId = $namedParameters['user_id'];
                $roleId = $namedParameters['role_id'];
                
                $operatorValue = self::memberOfRoleByUserId( $userId, $roleId );
            }
            break;

            case 'has_policy':
            {
                $module = $namedParameters['module'];
                $function = $namedParameters['function'];
                $userId = $namedParameters['user_id'];

                if ($userId === 0)
                {
                    $userId = eZUser::currentUserID();
                }

                $operatorValue = $this->userHasPolicy($userId, $module, $function);
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
        $result = false;
        
        // Fetch user ID
        $userID = $user->attribute('contentobject_id');
        
        // Perform comparison
        $result = self::memberOfRoleByUserId( $userID, $roleID );
        
        return $result;
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
     * @param int $userId User ID
     * @param string $module Policy module name (e.g., 'content', 'user')
     * @param string $function Policy function name (e.g., 'read', 'edit', 'create')
     * @return bool True if user has the policy, false otherwise
     */
    public function userHasPolicy( $userId, $module, $function )
    {
        if ( empty( $module ) || empty( $function ) || $userId <= 0 )
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
            if ( !$role instanceof eZRole )
            {
                continue;
            }

            // Check if eZRole has the hasPolicy method (from kernel override extension)
            if ( method_exists( $role, 'hasPolicy' ) )
            {
                if ( $role->hasPolicy( $module, $function ) )
                {
                    return true;
                }
            }
            else
            {
                // Fallback: manually check policies if hasPolicy method doesn't exist
                $policies = $role->policyList();

                foreach ( $policies as $policy )
                {
                    if ( !$policy instanceof eZPolicy )
                    {
                        continue;
                    }

                    $policyModule = $policy->attribute('module_name');
                    $policyFunction = $policy->attribute('function_name');

                    // Check for exact match or wildcard (*)
                    if ( ( $policyModule == $module || $policyModule == '*' ) &&
                        ( $policyFunction == $function || $policyFunction == '*' ) )
                    {
                        return true;
                    }
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
        $result = false;

        if ( !$user instanceof eZUser )
        {
            return false;
        }

        // Fetch user ID
        $userID = $user->attribute('contentobject_id');

        // Create instance to call userHasPolicy
        $instance = new self();
        $result = $instance->userHasPolicy( $userID, $module, $function );

        return $result;
    }
}

?>