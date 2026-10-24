<?php
/**
 * File containing the expMemberManagement class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Member management: user groups that manage the accounts of other user groups, set up from membermanagement.ini
 * (doc/eng-us/bc/6.0/member-management.md).
 *
 * A rule ([MemberRule_<name>]) names manager groups, managed groups, a policy set and a role. apply() puts the policy
 * set into the role, each content policy limited to the managed groups' subtrees, and assigns the role to the manager
 * groups. The defaults give the Editors group "Member management" over Members. A rule that would reach a protected
 * group ([MemberManagementSettings] ProtectedGroups[], default Administrator users and Editors) is refused.
 *
 * The guard (guardAllows()) closes the gap a Subtree limitation leaves: an object passes a Subtree policy when any of
 * its locations lies in the subtree, so an administrator who also has a location below Members would be in reach of
 * a member manager. eZContentObject::checkAccess() and eZContentObjectTreeNode::checkAccess() ask the guard after they
 * allowed a guarded function on an account: every location of that account below a protected group must allow the
 * same function on its own.
 *
 * parsePolicy(), plan(), validate() and missing() work on plain arrays and are tested without a database.
 */
class expMemberManagement
{
    /** Seconds the resolved protected paths and guarded class ids are kept in a long-running process */
    const CACHE_SECONDS = 60;

    /** @var array|null protected paths, guarded class ids, time */
    protected static $cache = null;

    /** @var bool set while the guard asks a node, so that a check it causes is not guarded again */
    protected static $inGuard = false;

    /**
     * @return eZINI
     */
    public static function ini()
    {
        return eZINI::instance( 'membermanagement.ini' );
    }

    /**
     * @return bool
     */
    public static function enabled()
    {
        $ini = self::ini();
        return !$ini->hasVariable( 'MemberManagementSettings', 'MemberManagement' )
            || $ini->variable( 'MemberManagementSettings', 'MemberManagement' ) !== 'disabled';
    }

    /**
     * The rules as plain data.
     *
     * @return array name => role, managers (string[]), managed (string[]), policy_set, entry_nodes (string[]),
     *               policies (string[] lines of the policy set), errors (string[])
     */
    public static function rules()
    {
        $ini = self::ini();
        $rules = array();
        $names = $ini->hasVariable( 'MemberManagementSettings', 'Rules' ) ? (array)$ini->variable( 'MemberManagementSettings', 'Rules' ) : array();
        foreach ( array_unique( array_filter( array_map( 'trim', $names ) ) ) as $name )
        {
            $block = 'MemberRule_' . $name;
            $rule = array( 'role' => '', 'managers' => array(), 'managed' => array(), 'policy_set' => '',
                           'entry_nodes' => array(), 'policies' => array(), 'errors' => array() );
            if ( !$ini->hasGroup( $block ) )
            {
                $rule['errors'][] = "no block [$block]";
                $rules[$name] = $rule;
                continue;
            }
            $get = function ( $setting, $default ) use ( $ini, $block ) {
                return $ini->hasVariable( $block, $setting ) ? $ini->variable( $block, $setting ) : $default;
            };
            $rule['role'] = trim( (string)$get( 'RoleName', '' ) );
            $rule['managers'] = array_values( array_filter( array_map( 'trim', (array)$get( 'ManagerGroups', array() ) ) ) );
            $rule['managed'] = array_values( array_filter( array_map( 'trim', (array)$get( 'ManagedGroups', array() ) ) ) );
            $rule['policy_set'] = trim( (string)$get( 'PolicySet', '' ) );
            $rule['entry_nodes'] = array_values( array_filter( array_map( 'trim', (array)$get( 'EntryNodes', array() ) ) ) );
            $setBlock = 'MemberPolicySet_' . $rule['policy_set'];
            if ( $rule['policy_set'] === '' || !$ini->hasGroup( $setBlock ) )
                $rule['errors'][] = "no policy set [$setBlock]";
            else if ( $ini->hasVariable( $setBlock, 'Policies' ) )
                $rule['policies'] = array_values( array_filter( array_map( 'trim', (array)$ini->variable( $setBlock, 'Policies' ) ) ) );
            if ( $rule['role'] === '' )
                $rule['errors'][] = 'no RoleName';
            if ( !$rule['managers'] )
                $rule['errors'][] = 'no ManagerGroups[]';
            if ( !$rule['managed'] )
                $rule['errors'][] = 'no ManagedGroups[]';
            if ( !$rule['policies'] )
                $rule['errors'][] = 'the policy set has no Policies[]';
            $rules[$name] = $rule;
        }
        return $rules;
    }

    /**
     * The protected group specifications.
     *
     * @return string[]
     */
    public static function protectedSpecs()
    {
        $ini = self::ini();
        return $ini->hasVariable( 'MemberManagementSettings', 'ProtectedGroups' )
            ? array_values( array_filter( array_map( 'trim', (array)$ini->variable( 'MemberManagementSettings', 'ProtectedGroups' ) ) ) )
            : array();
    }

    /**
     * Reads one Policies[] line: module/function[;Limitation=value,value;...].
     *
     * @param string $line
     * @return array|null module, function, limitations (identifier => string[]); null when the line is not one
     */
    public static function parsePolicy( $line )
    {
        $parts = array_map( 'trim', explode( ';', (string)$line ) );
        $head = array_shift( $parts );
        if ( !preg_match( '#^([A-Za-z0-9_*-]+)/([A-Za-z0-9_*-]+)$#', (string)$head, $m ) )
            return null;
        $limitations = array();
        foreach ( $parts as $part )
        {
            if ( $part === '' )
                continue;
            if ( !preg_match( '#^([A-Za-z_]+)=(.+)$#', $part, $l ) )
                return null;
            $values = array_values( array_filter( array_map( 'trim', explode( ',', $l[2] ) ), 'strlen' ) );
            if ( !$values )
                return null;
            $limitations[$l[1]] = $values;
        }
        return array( 'module' => $m[1], 'function' => $m[2], 'limitations' => $limitations );
    }

    /**
     * The content functions whose policies take a Subtree limitation (the managed groups are added to them).
     *
     * @return string[]
     */
    public static function subtreeFunctions()
    {
        return array( 'read', 'diff', 'view_embed', 'create', 'edit', 'publish', 'manage_locations', 'hide', 'translate',
                      'remove', 'versionread', 'versionremove', 'pdf' );
    }

    /**
     * The policies of a rule, with every value resolved.
     *
     * @param string[] $lines the policy set's Policies[]
     * @param string[] $managedPaths path strings of the managed groups
     * @param int[] $entryNodeIDs nodes readable on their own
     * @param array $classIDs class identifier => id
     * @return array policies (each module, function, limitations identifier => sorted string[]) and errors (string[])
     */
    public static function plan( array $lines, array $managedPaths, array $entryNodeIDs, array $classIDs )
    {
        $policies = array();
        $errors = array();
        sort( $managedPaths );
        foreach ( $lines as $line )
        {
            $policy = self::parsePolicy( $line );
            if ( $policy === null )
            {
                $errors[] = "not a policy: '$line'";
                continue;
            }
            if ( $policy['module'] === '*' || $policy['function'] === '*' )
            {
                $errors[] = "'$line': a member manager gets single functions, not module/* or */*";
                continue;
            }
            if ( in_array( $policy['module'], array( 'role', 'setup', 'section', 'class', 'state', 'workflow' ), true ) )
            {
                $errors[] = "'$line': the module {$policy['module']} belongs to the administrators";
                continue;
            }
            // A policy with a limitation that cannot be resolved is left out whole: without it the policy would be wider
            // than the line says
            $limitations = array();
            $bad = false;
            foreach ( $policy['limitations'] as $identifier => $values )
            {
                if ( $identifier === 'Class' || $identifier === 'ParentClass' )
                {
                    $ids = array();
                    foreach ( $values as $value )
                    {
                        if ( ctype_digit( $value ) )
                            $ids[] = (string)(int)$value;
                        else if ( isset( $classIDs[$value] ) )
                            $ids[] = (string)(int)$classIDs[$value];
                        else
                        {
                            $errors[] = "'$line': no class '$value'";
                            $bad = true;
                        }
                    }
                    $values = $ids;
                }
                else if ( $identifier === 'Subtree' || $identifier === 'Node' )
                {
                    $errors[] = "'$line': Subtree and Node come from the rule (ManagedGroups[], EntryNodes[])";
                    $bad = true;
                    continue;
                }
                $values = array_values( array_unique( array_map( 'strval', $values ) ) );
                sort( $values );
                if ( $values )
                    $limitations[$identifier] = $values;
            }
            if ( $bad )
                continue;
            if ( $policy['module'] === 'content' && in_array( $policy['function'], self::subtreeFunctions(), true ) )
            {
                if ( !$managedPaths )
                {
                    $errors[] = "'$line': no managed group to limit it to";
                    continue;
                }
                $limitations['Subtree'] = array_values( $managedPaths );
            }
            ksort( $limitations );
            $policies[] = array( 'module' => $policy['module'], 'function' => $policy['function'], 'limitations' => $limitations );
        }
        if ( $entryNodeIDs )
        {
            $ids = array_values( array_unique( array_map( 'strval', array_map( 'intval', $entryNodeIDs ) ) ) );
            sort( $ids );
            $policies[] = array( 'module' => 'content', 'function' => 'read', 'limitations' => array( 'Node' => $ids ) );
        }
        return array( 'policies' => $policies, 'errors' => $errors );
    }

    /**
     * Checks that no managed group is, contains or lies below a protected group, and that no manager group is managed by
     * its own rule.
     *
     * @param string[] $managedPaths
     * @param string[] $protectedPaths
     * @param string[] $managerPaths
     * @return string[] errors
     */
    public static function validate( array $managedPaths, array $protectedPaths, array $managerPaths = array() )
    {
        $errors = array();
        foreach ( $managedPaths as $managed )
        {
            foreach ( $protectedPaths as $protected )
            {
                if ( strpos( $managed, $protected ) === 0 || strpos( $protected, $managed ) === 0 )
                    $errors[] = "the managed group $managed reaches the protected group $protected";
            }
            foreach ( $managerPaths as $manager )
            {
                if ( strpos( $manager, $managed ) === 0 )
                    $errors[] = "the manager group $manager lies in the managed group $managed and would manage itself";
            }
        }
        return array_values( array_unique( $errors ) );
    }

    /**
     * The planned policies a role does not have yet (same module, function and limitations).
     *
     * @param array $planned plan()'s policies
     * @param array $existing policiesOf()
     * @return array
     */
    public static function missing( array $planned, array $existing )
    {
        $have = array();
        foreach ( $existing as $policy )
            $have[] = self::key( $policy );
        $missing = array();
        foreach ( $planned as $policy )
        {
            if ( !in_array( self::key( $policy ), $have, true ) )
                $missing[] = $policy;
        }
        return $missing;
    }

    /**
     * A policy as a comparable string.
     *
     * @param array $policy module, function, limitations
     * @return string
     */
    public static function key( array $policy )
    {
        $limitations = isset( $policy['limitations'] ) ? $policy['limitations'] : array();
        ksort( $limitations );
        foreach ( $limitations as $identifier => $values )
        {
            $values = array_map( 'strval', (array)$values );
            sort( $values );
            $limitations[$identifier] = $values;
        }
        return $policy['module'] . '/' . $policy['function'] . ' ' . json_encode( $limitations );
    }

    /**
     * A policy for output: content/edit Class=4,3 Subtree=/1/5/12/.
     *
     * @param array $policy
     * @return string
     */
    public static function describe( array $policy )
    {
        $out = $policy['module'] . '/' . $policy['function'];
        foreach ( $policy['limitations'] as $identifier => $values )
            $out .= ' ' . $identifier . '=' . implode( ',', $values );
        return $out;
    }

    /**
     * The policies of a role as plain data.
     *
     * @param eZRole $role
     * @return array
     */
    public static function policiesOf( $role )
    {
        $policies = array();
        foreach ( (array)$role->policyList() as $policy )
        {
            $limitations = array();
            foreach ( (array)$policy->limitationList( false ) as $limitation )
            {
                $values = array_map( 'strval', (array)$limitation->allValues() );
                sort( $values );
                $limitations[(string)$limitation->attribute( 'identifier' )] = $values;
            }
            ksort( $limitations );
            $policies[] = array( 'module' => (string)$policy->attribute( 'module_name' ),
                                 'function' => (string)$policy->attribute( 'function_name' ), 'limitations' => $limitations );
        }
        return $policies;
    }

    /**
     * Finds a user group by its specification (remote:, node:, name:).
     *
     * @param string $spec
     * @return array|null node_id, object_id, path_string, name
     */
    public static function resolveGroup( $spec )
    {
        $node = null;
        if ( strpos( $spec, 'remote:' ) === 0 )
        {
            $remoteID = substr( $spec, 7 );
            $object = eZContentObject::fetchByRemoteID( $remoteID );
            if ( $object instanceof eZContentObject )
                $node = $object->attribute( 'main_node' );
            else
                $node = eZContentObjectTreeNode::fetchByRemoteID( $remoteID );
        }
        else if ( strpos( $spec, 'node:' ) === 0 )
        {
            $node = eZContentObjectTreeNode::fetch( (int)substr( $spec, 5 ) );
        }
        else if ( strpos( $spec, 'name:' ) === 0 )
        {
            $rootID = (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'UserRootNode' );
            $nodes = eZContentObjectTreeNode::subTreeByNodeID( array( 'AttributeFilter' => array( array( 'name', '=', substr( $spec, 5 ) ) ),
                                                                      'ClassFilterType' => 'include', 'ClassFilterArray' => array( 'user_group' ),
                                                                      'Depth' => 1, 'Limitation' => array() ), $rootID ?: 5 );
            $node = $nodes ? $nodes[0] : null;
        }
        if ( !$node instanceof eZContentObjectTreeNode )
            return null;
        return array( 'node_id' => (int)$node->attribute( 'node_id' ), 'object_id' => (int)$node->attribute( 'contentobject_id' ),
                      'path_string' => (string)$node->attribute( 'path_string' ), 'name' => (string)$node->attribute( 'name' ) );
    }

    /**
     * Resolves a list of group specifications.
     *
     * @param string[] $specs
     * @param string[] $errors receives what could not be found
     * @return array of resolveGroup() results
     */
    public static function resolveGroups( array $specs, array &$errors )
    {
        $groups = array();
        foreach ( $specs as $spec )
        {
            $group = self::resolveGroup( $spec );
            if ( $group === null )
                $errors[] = "no user group '$spec'";
            else
                $groups[] = $group;
        }
        return $groups;
    }

    /**
     * The entry nodes of a rule as node ids ("users" is content.ini [NodeSettings] UserRootNode).
     *
     * @param string[] $specs
     * @return int[]
     */
    public static function entryNodeIDs( array $specs )
    {
        $ids = array();
        foreach ( $specs as $spec )
        {
            if ( $spec === 'users' )
                $ids[] = (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'UserRootNode' );
            else if ( ctype_digit( $spec ) )
                $ids[] = (int)$spec;
            else if ( ( $group = self::resolveGroup( $spec ) ) !== null )
                $ids[] = $group['node_id'];
        }
        return array_values( array_unique( array_filter( $ids ) ) );
    }

    /**
     * Class identifier => id for the identifiers a set of policy lines names.
     *
     * @param string[] $lines
     * @return array
     */
    public static function classIDs( array $lines )
    {
        $ids = array();
        foreach ( $lines as $line )
        {
            $policy = self::parsePolicy( $line );
            if ( $policy === null )
                continue;
            foreach ( array( 'Class', 'ParentClass' ) as $identifier )
            {
                if ( !isset( $policy['limitations'][$identifier] ) )
                    continue;
                foreach ( $policy['limitations'][$identifier] as $value )
                {
                    if ( !ctype_digit( $value ) && !isset( $ids[$value] ) )
                    {
                        $id = eZContentClass::classIDByIdentifier( $value );
                        if ( $id )
                            $ids[$value] = (int)$id;
                    }
                }
            }
        }
        return $ids;
    }

    /**
     * Everything a rule needs on this installation: groups resolved, policies planned, errors.
     *
     * @param array $rule one of rules()
     * @return array rule + manager_groups, managed_groups, protected_groups, planned (policies), errors
     */
    public static function prepare( array $rule )
    {
        $errors = $rule['errors'];
        $rule['manager_groups'] = self::resolveGroups( $rule['managers'], $errors );
        $rule['managed_groups'] = self::resolveGroups( $rule['managed'], $errors );
        $rule['protected_groups'] = self::resolveGroups( self::protectedSpecs(), $errors );
        $managedPaths = array_column( $rule['managed_groups'], 'path_string' );
        $errors = array_merge( $errors, self::validate( $managedPaths, array_column( $rule['protected_groups'], 'path_string' ),
                                                        array_column( $rule['manager_groups'], 'path_string' ) ) );
        $plan = self::plan( $rule['policies'], $managedPaths, self::entryNodeIDs( $rule['entry_nodes'] ), self::classIDs( $rule['policies'] ) );
        $rule['planned'] = $plan['policies'];
        $rule['errors'] = array_values( array_unique( array_merge( $errors, $plan['errors'] ) ) );
        return $rule;
    }

    /**
     * Whether a role is assigned to a user or group without a limitation.
     *
     * @param eZRole $role
     * @param int $objectID
     * @return bool
     */
    public static function isAssigned( $role, $objectID )
    {
        foreach ( (array)$role->fetchUserByRole() as $row )
        {
            $object = $row['user_object'];
            if ( $object instanceof eZContentObject && (int)$object->attribute( 'id' ) === (int)$objectID && (string)$row['limit_ident'] === '' )
                return true;
        }
        return false;
    }

    /**
     * What a prepared rule would change: whether the role is created, the policies added, the groups assigned.
     *
     * @param array $rule prepare()'s result
     * @return array create_role (bool), role (eZRole|null), add (policies), assign (groups)
     */
    public static function changes( array $rule )
    {
        $role = eZRole::fetchByName( $rule['role'] );
        $role = $role instanceof eZRole ? $role : null;
        $add = self::missing( $rule['planned'], $role ? self::policiesOf( $role ) : array() );
        $assign = array();
        foreach ( $rule['manager_groups'] as $group )
        {
            if ( !$role || !self::isAssigned( $role, $group['object_id'] ) )
                $assign[] = $group;
        }
        return array( 'create_role' => $role === null, 'role' => $role, 'add' => $add, 'assign' => $assign );
    }

    /**
     * Applies a prepared rule without errors.
     *
     * @param array $rule prepare()'s result
     * @return array changes() as applied, with role set
     */
    public static function apply( array $rule )
    {
        $changes = self::changes( $rule );
        $db = eZDB::instance();
        $db->begin();
        $role = $changes['role'];
        if ( $role === null )
        {
            $role = eZRole::create( $rule['role'] );
            $role->store();
        }
        foreach ( $changes['add'] as $policy )
            $role->appendPolicy( $policy['module'], $policy['function'], $policy['limitations'] );
        foreach ( $changes['assign'] as $group )
            $role->assignToUser( $group['object_id'] );
        $db->commit();
        if ( $changes['add'] || $changes['assign'] || $changes['create_role'] )
        {
            eZRole::expireCache();
            eZUser::cleanupCache();
        }
        $changes['role'] = $role;
        return $changes;
    }

    /**
     * The setup step's guarantee (setup wizard, kickstarter, site package installers): every valid rule is applied. A
     * rule with errors is skipped and named.
     *
     * @return string what happened, for the setup log
     */
    public static function ensure()
    {
        if ( !self::enabled() )
            return 'member management is disabled ([MemberManagementSettings] MemberManagement)';
        $done = array();
        foreach ( self::rules() as $name => $rule )
        {
            $rule = self::prepare( $rule );
            if ( $rule['errors'] )
            {
                $done[] = "rule $name skipped: " . implode( '; ', $rule['errors'] );
                continue;
            }
            $changes = self::apply( $rule );
            $done[] = sprintf( 'rule %s: role %s%s, %d polic%s added, assigned to %d group(s)', $name, $rule['role'],
                               $changes['create_role'] ? ' created' : '', count( $changes['add'] ), count( $changes['add'] ) === 1 ? 'y' : 'ies',
                               count( $changes['assign'] ) );
        }
        return $done ? implode( '; ', $done ) : 'no member management rules';
    }

    /**
     * The guard settings.
     *
     * @return array guard (bool), functions (string[]), classes (string[]), setting_requires_edit (bool)
     */
    public static function guardSettings()
    {
        $ini = self::ini();
        $get = function ( $setting, $default ) use ( $ini ) {
            return $ini->hasVariable( 'MemberGuardSettings', $setting ) ? $ini->variable( 'MemberGuardSettings', $setting ) : $default;
        };
        return array( 'guard' => $get( 'Guard', 'disabled' ) === 'enabled',
                      'functions' => array_values( array_filter( (array)$get( 'GuardedFunctions', array() ) ) ),
                      'classes' => array_values( array_filter( (array)$get( 'GuardedClasses', array() ) ) ),
                      'setting_requires_edit' => $get( 'SettingRequiresEdit', 'disabled' ) === 'enabled' );
    }

    /**
     * Whether the guard is on and guards a function (cheap: what checkAccess() asks first).
     *
     * @param string $functionName
     * @return bool
     */
    public static function guards( $functionName )
    {
        if ( self::$inGuard )
            return false;
        $settings = self::guardSettings();
        return $settings['guard'] && in_array( $functionName, $settings['functions'], true );
    }

    /**
     * Protected paths and guarded class ids, kept for CACHE_SECONDS.
     *
     * @return array protected (string[] path strings), roots (string[] the managed groups' paths), classes (int[])
     */
    protected static function guardData()
    {
        if ( self::$cache !== null && self::$cache['time'] > time() - self::CACHE_SECONDS )
            return self::$cache;
        $errors = array();
        $protected = array_column( self::resolveGroups( self::protectedSpecs(), $errors ), 'path_string' );
        $classes = array_map( 'intval', (array)eZUser::contentClassIDs() );
        foreach ( self::guardSettings()['classes'] as $identifier )
        {
            $id = eZContentClass::classIDByIdentifier( $identifier );
            if ( $id )
                $classes[] = (int)$id;
        }
        // The managed groups of every rule (their own nodes are kept from the managers: see guardAllows())
        $roots = array();
        foreach ( self::rules() as $rule )
            $roots = array_merge( $roots, array_column( self::resolveGroups( $rule['managed'], $errors ), 'path_string' ) );
        self::$cache = array( 'protected' => $protected, 'roots' => array_values( array_unique( $roots ) ),
                              'classes' => array_values( array_unique( $classes ) ), 'time' => time() );
        return self::$cache;
    }

    /**
     * Forgets the cached guard data (tests, after the groups moved).
     */
    public static function resetCache()
    {
        self::$cache = null;
    }

    /**
     * The guard, asked by eZContentObject::checkAccess() and eZContentObjectTreeNode::checkAccess() after they allowed a
     * function: an account with a location below a protected group keeps the function only when every such location
     * allows it on its own.
     *
     * @param eZContentObject $object
     * @param string $functionName
     * @param int|bool $userID as checkAccess() got it
     * @return bool
     */
    public static function guardAllows( $object, $functionName, $userID = false )
    {
        if ( !$object instanceof eZContentObject || !self::guards( $functionName ) )
            return true;
        // Only a limited policy can reach an account through another of its locations: a user with the function
        // everywhere ('yes') passes each location anyway, and an allowance without a policy ('no', the user's own new
        // draft) concerns an object that is no account yet
        $user = eZUser::accessUser( $userID );
        if ( !$user instanceof eZUser )
            return true;
        $access = $user->hasAccessTo( 'content', $functionName === 'move' ? 'edit' : $functionName );
        if ( !isset( $access['accessWord'] ) || $access['accessWord'] !== 'limited' )
            return true;
        self::$inGuard = true;
        try
        {
            $data = self::guardData();
            if ( ( !$data['protected'] && !$data['roots'] ) || !in_array( (int)$object->attribute( 'contentclass_id' ), $data['classes'], true ) )
                return true;
            $nodeFunction = $functionName === 'move' ? 'edit' : $functionName;
            foreach ( (array)$object->assignedNodes( false ) as $row )
            {
                $path = (string)$row['path_string'];
                // A location below a protected group: the function must be allowed there on its own
                foreach ( $data['protected'] as $protected )
                {
                    if ( strpos( $path, $protected ) !== 0 )
                        continue;
                    $node = eZContentObjectTreeNode::fetch( (int)$row['node_id'] );
                    if ( !$node instanceof eZContentObjectTreeNode || $node->checkAccess( $nodeFunction, false, false, false, false, $userID ) != 1 )
                        return false;
                }
                // A managed group itself (Members): a Subtree policy on it includes the group's own node, so a manager
                // could edit, move or remove the whole group with every member in it. That needs the same function
                // at the group's parent, which a manager does not have.
                if ( in_array( $path, $data['roots'], true ) )
                {
                    $parent = eZContentObjectTreeNode::fetch( (int)$row['parent_node_id'] );
                    if ( !$parent instanceof eZContentObjectTreeNode || $parent->checkAccess( $nodeFunction, false, false, false, false, $userID ) != 1 )
                        return false;
                }
            }
            return true;
        }
        finally
        {
            self::$inGuard = false;
        }
    }

    /**
     * Whether user/setting may change an account: always unless SettingRequiresEdit, then when the account may be
     * edited (which includes the guard).
     *
     * @param eZContentObject $userObject
     * @return bool
     */
    public static function settingAllowed( $userObject )
    {
        if ( !self::guardSettings()['setting_requires_edit'] )
            return true;
        return $userObject instanceof eZContentObject && $userObject->canEdit();
    }
}
