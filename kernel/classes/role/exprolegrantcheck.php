<?php
/**
 * File containing the expRoleGrantCheck class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Whether a user who edits roles may grant a policy: only one that their own access covers (site.ini [RoleSettings]
 * PreventPrivilegeEscalation). Without it, anyone with access to the role module could give themselves, or anyone,
 * every right of the installation.
 *
 * A grant is a policy as the role stores it: module, function and limitations (identifier => stored values: section
 * and class ids, node ids, subtree path strings ...). The editor's access is their access array (eZUser::accessArray()):
 * module => function => ( '*' => '*' | policy key => identifier => values ).
 *
 * A grant is covered by one policy of the editor when
 *  - the module is the policy's or the policy has every module ('*'); the same for the function; a grant over every
 *    module or every function needs the same from the policy;
 *  - every limitation of the editor's policy is met by the grant at least as narrowly:
 *    - Subtree (and User_Subtree, a role assigned for a subtree): every subtree of the grant lies inside one of the
 *      policy's, or every node of the grant does;
 *    - User_Section (a role assigned for a section): the grant's sections are among the policy's;
 *    - every other limitation (Section, Class, ParentClass, Node, Owner, Group, Language, SiteAccess, NewSection,
 *      NewState, StateGroup_*, FunctionList ...): the grant has it, with values among the policy's.
 *    A limitation the grant has and the policy does not only narrows the grant.
 * A grant not covered by one policy is still covered when, for one of its limitations with several values, each value
 * on its own is covered (two policies for section A and section B together cover a grant for A and B).
 *
 * The check is conservative: a grant it cannot prove narrower is refused (a subtree that happens to lie in a section
 * is not taken as covered by a policy limited to that section). Users with every function of every module, without a
 * limitation, may grant anything. The class works on arrays and is tested without a database; forCurrentUser() and
 * the static helpers read the database.
 */
class expRoleGrantCheck
{
    /** @var array the editor's access array */
    protected $access;

    /** @var callable|null ( int $nodeID ) => string|null the path string of a node */
    protected $nodePath;

    /** How many times a grant is split by the values of one limitation before it counts as not covered */
    const SPLIT_DEPTH = 2;

    /**
     * @param array $access an access array
     * @param callable|null $nodePath ( int $nodeID ) => path string or null, for nodes inside subtrees
     */
    public function __construct( array $access, $nodePath = null )
    {
        $this->access = $access;
        $this->nodePath = is_callable( $nodePath ) ? $nodePath : null;
    }

    /**
     * Whether the access holds every function of every module without a limitation.
     *
     * @return bool
     */
    public function isUnlimited()
    {
        return isset( $this->access['*']['*']['*'] ) && $this->access['*']['*']['*'] === '*';
    }

    /**
     * Whether the grant is covered.
     *
     * @param string $module
     * @param string $function
     * @param array $limitations identifier => values
     * @return bool
     */
    public function covers( $module, $function, array $limitations = array() )
    {
        if ( $this->isUnlimited() )
            return true;
        return $this->coversSplit( (string)$module, (string)$function, self::normalise( $limitations ), self::SPLIT_DEPTH );
    }

    /**
     * The grants of a list that are not covered, with their keys.
     *
     * @param array $grants arrays with module, function, limitations
     * @return array
     */
    public function uncovered( array $grants )
    {
        $refused = array();
        foreach ( $grants as $key => $grant )
        {
            if ( !$this->covers( $grant['module'], $grant['function'], isset( $grant['limitations'] ) ? $grant['limitations'] : array() ) )
                $refused[$key] = $grant;
        }
        return $refused;
    }

    /**
     * @param string $module
     * @param string $function
     * @param array $limits
     * @param int $depth
     * @return bool
     */
    protected function coversSplit( $module, $function, array $limits, $depth )
    {
        foreach ( $this->candidates( $module, $function ) as $policy )
        {
            if ( $this->policyCovers( $policy, $limits ) )
                return true;
        }
        if ( $depth <= 0 )
            return false;
        foreach ( $limits as $identifier => $values )
        {
            if ( count( $values ) < 2 )
                continue;
            $all = true;
            foreach ( $values as $value )
            {
                $one = $limits;
                $one[$identifier] = array( $value );
                if ( !$this->coversSplit( $module, $function, $one, $depth - 1 ) )
                {
                    $all = false;
                    break;
                }
            }
            if ( $all )
                return true;
        }
        return false;
    }

    /**
     * The editor's policies that could cover a grant of this module and function: their limitations, array() for a
     * policy without any.
     *
     * @param string $module
     * @param string $function
     * @return array
     */
    public function candidates( $module, $function )
    {
        $modules = $module === '*' ? array( '*' ) : array( $module, '*' );
        $functions = $function === '*' ? array( '*' ) : array( $function, '*' );
        $policies = array();
        foreach ( $modules as $m )
        {
            if ( !isset( $this->access[$m] ) || !is_array( $this->access[$m] ) )
                continue;
            foreach ( $functions as $f )
            {
                if ( !isset( $this->access[$m][$f] ) || !is_array( $this->access[$m][$f] ) )
                    continue;
                foreach ( $this->access[$m][$f] as $key => $limits )
                {
                    if ( $key === '*' || $limits === '*' )
                        $policies[] = array();
                    else if ( is_array( $limits ) )
                        $policies[] = self::normalise( $limits );
                }
            }
        }
        return $policies;
    }

    /**
     * Whether one policy of the editor covers the grant's limitations.
     *
     * @param array $policy identifier => values
     * @param array $grant identifier => values
     * @return bool
     */
    public function policyCovers( array $policy, array $grant )
    {
        foreach ( $policy as $identifier => $allowed )
        {
            switch ( $identifier )
            {
                case 'Subtree':
                case 'User_Subtree':
                    if ( !$this->insideSubtrees( $grant, $allowed ) )
                        return false;
                    break;
                case 'User_Section':
                    if ( !isset( $grant['Section'] ) || !self::subset( $grant['Section'], $allowed ) )
                        return false;
                    break;
                default:
                    if ( !isset( $grant[$identifier] ) || !self::subset( $grant[$identifier], $allowed ) )
                        return false;
            }
        }
        return true;
    }

    /**
     * Whether the grant lies inside the subtrees $paths: by its own subtrees, or else by its nodes.
     *
     * @param array $grant
     * @param array $paths
     * @return bool
     */
    protected function insideSubtrees( array $grant, array $paths )
    {
        if ( isset( $grant['Subtree'] ) && $grant['Subtree'] && self::pathsInside( $grant['Subtree'], $paths ) )
            return true;
        if ( isset( $grant['Node'] ) && $grant['Node'] && $this->nodePath )
        {
            $nodePaths = array();
            foreach ( $grant['Node'] as $nodeID )
            {
                $path = call_user_func( $this->nodePath, (int)$nodeID );
                if ( !is_string( $path ) || $path === '' )
                    return false;
                $nodePaths[] = $path;
            }
            return self::pathsInside( $nodePaths, $paths );
        }
        return false;
    }

    /**
     * Whether every path lies inside (or is) one of the subtrees.
     *
     * @param array $paths
     * @param array $subtrees
     * @return bool
     */
    public static function pathsInside( array $paths, array $subtrees )
    {
        foreach ( $paths as $path )
        {
            $path = self::slashed( $path );
            $inside = false;
            foreach ( $subtrees as $subtree )
            {
                if ( strpos( $path, self::slashed( $subtree ) ) === 0 )
                {
                    $inside = true;
                    break;
                }
            }
            if ( !$inside )
                return false;
        }
        return true;
    }

    /**
     * Whether every value is among the allowed ones (compared as strings).
     *
     * @param array $values
     * @param array $allowed
     * @return bool
     */
    public static function subset( array $values, array $allowed )
    {
        $allowed = array_map( 'strval', $allowed );
        foreach ( $values as $value )
        {
            if ( !in_array( (string)$value, $allowed, true ) )
                return false;
        }
        return true;
    }

    /**
     * Limitations as identifier => list of string values; a scalar becomes a list of one.
     *
     * @param array $limits
     * @return array
     */
    public static function normalise( array $limits )
    {
        $clean = array();
        foreach ( $limits as $identifier => $values )
        {
            $list = array();
            foreach ( is_array( $values ) ? $values : array( $values ) as $value )
            {
                if ( is_scalar( $value ) )
                    $list[] = (string)$value;
            }
            $clean[(string)$identifier] = array_values( array_unique( $list ) );
        }
        return $clean;
    }

    /**
     * A path string with its slashes: "/1/2/55/".
     *
     * @param string $path
     * @return string
     */
    protected static function slashed( $path )
    {
        return '/' . trim( (string)$path, '/' ) . '/';
    }

    /**
     * The grants of a role's policies narrowed by an assignment's limitation: a section adds or narrows Section, a
     * subtree (its path) adds or narrows Subtree.
     *
     * @param array $grants arrays with module, function, limitations
     * @param string $limitIdent '', 'section' or 'subtree' (as role/assign takes them), or 'Section' / 'Subtree'
     * @param string $limitValue the section id, or the subtree's path string
     * @return array
     */
    public static function narrowByAssignment( array $grants, $limitIdent, $limitValue )
    {
        $kind = strtolower( (string)$limitIdent );
        if ( $kind !== 'section' && $kind !== 'subtree' )
            return $grants;
        foreach ( $grants as $key => $grant )
        {
            $limits = self::normalise( isset( $grant['limitations'] ) ? $grant['limitations'] : array() );
            if ( $kind === 'section' )
            {
                $limits['Section'] = isset( $limits['Section'] )
                                   ? array_values( array_intersect( $limits['Section'], array( (string)$limitValue ) ) )
                                   : array( (string)$limitValue );
            }
            else
            {
                $assigned = self::slashed( $limitValue );
                if ( !isset( $limits['Subtree'] ) )
                    $limits['Subtree'] = array( $assigned );
                else
                {
                    $deeper = array();
                    foreach ( $limits['Subtree'] as $own )
                    {
                        $own = self::slashed( $own );
                        if ( strpos( $own, $assigned ) === 0 )
                            $deeper[] = $own;
                        else if ( strpos( $assigned, $own ) === 0 )
                            $deeper[] = $assigned;
                    }
                    $limits['Subtree'] = array_values( array_unique( $deeper ) );
                }
            }
            $grants[$key]['limitations'] = $limits;
        }
        return $grants;
    }

    // ---- With the database ----

    /**
     * Whether site.ini [RoleSettings] PreventPrivilegeEscalation is on (it is unless it says disabled).
     *
     * @return bool
     */
    public static function enabled()
    {
        $ini = eZINI::instance();
        return !$ini->hasVariable( 'RoleSettings', 'PreventPrivilegeEscalation' )
               || $ini->variable( 'RoleSettings', 'PreventPrivilegeEscalation' ) !== 'disabled';
    }

    /**
     * The check for the user signed in, or null when it does not apply: the setting is disabled or the user may do
     * everything.
     *
     * @return expRoleGrantCheck|null
     */
    public static function forCurrentUser()
    {
        if ( !self::enabled() )
            return null;
        $user = eZUser::currentUser();
        $check = new self( (array)$user->accessArray(), array( __CLASS__, 'nodePathOf' ) );
        return $check->isUnlimited() ? null : $check;
    }

    /**
     * @param int $nodeID
     * @return string|null
     */
    public static function nodePathOf( $nodeID )
    {
        $node = eZContentObjectTreeNode::fetch( (int)$nodeID, false, false );
        return is_array( $node ) && isset( $node['path_string'] ) ? (string)$node['path_string'] : null;
    }

    /**
     * A stored policy as a grant: module, function, limitations with their stored values.
     *
     * @param eZPolicy $policy
     * @return array
     */
    public static function grantOf( eZPolicy $policy )
    {
        $limits = array();
        foreach ( (array)eZPolicyLimitation::fetchByPolicyID( (int)$policy->attribute( 'id' ) ) as $limitation )
        {
            $values = array();
            foreach ( (array)eZPolicyLimitationValue::fetchList( (int)$limitation->attribute( 'id' ) ) as $value )
                $values[] = (string)$value->attribute( 'value' );
            $limits[(string)$limitation->attribute( 'identifier' )] = $values;
        }
        return array( 'id' => (int)$policy->attribute( 'id' ), 'module' => (string)$policy->attribute( 'module_name' ),
                      'function' => (string)$policy->attribute( 'function_name' ), 'limitations' => $limits );
    }

    /**
     * The grants of every policy of a role (its saved policies, not temporary copies).
     *
     * @param eZRole $role
     * @return array
     */
    public static function grantsOfRole( eZRole $role )
    {
        $grants = array();
        foreach ( $role->policyPage( 0, false ) as $policy )
            $grants[(int)$policy->attribute( 'id' )] = self::grantOf( $policy );
        return $grants;
    }

    /**
     * The grants of a draft that the saved role does not have in the same form: new policies and changed ones.
     *
     * @param eZRole $draft
     * @return array
     */
    public static function changedGrants( eZRole $draft )
    {
        $original = (int)$draft->attribute( 'version' ) > 0 ? eZRole::fetch( (int)$draft->attribute( 'version' ) ) : null;
        $before = array();
        if ( $original instanceof eZRole && (int)$original->attribute( 'is_new' ) !== 1 )
        {
            foreach ( self::grantsOfRole( $original ) as $grant )
                $before[expRolePage::policySignature( $grant )] = true;
        }
        $changed = array();
        foreach ( self::grantsOfRole( $draft ) as $id => $grant )
        {
            if ( !isset( $before[expRolePage::policySignature( $grant )] ) )
                $changed[$id] = $grant;
        }
        return $changed;
    }

    /**
     * The grants in words, for the refusal message.
     *
     * @param array $grants
     * @return string[]
     */
    public static function describe( array $grants )
    {
        $sentence = new expRolePolicySentence();
        $texts = array();
        foreach ( $grants as $grant )
        {
            $limitations = array();
            foreach ( isset( $grant['limitations'] ) ? $grant['limitations'] : array() as $identifier => $values )
                $limitations[] = array( 'identifier' => $identifier, 'label' => $identifier, 'values' => $values );
            $said = $sentence->describe( array( 'module' => $grant['module'], 'function' => $grant['function'], 'limitations' => $limitations ) );
            $texts[] = $said['sentence'];
        }
        return $texts;
    }

    /**
     * The session variable of the refusal message the role pages show once.
     */
    const SESSION_KEY = 'expRoleGrantRefused';

    /**
     * Keeps a refusal for the next page of the role pages.
     *
     * @param string $what 'policy', 'save', 'assign' or 'copy'
     * @param array $grants the grants refused
     */
    public static function remember( $what, array $grants )
    {
        eZHTTPTool::instance()->setSessionVariable( self::SESSION_KEY, array( 'what' => $what, 'grants' => self::describe( $grants ) ) );
    }

    /**
     * The refusal kept by remember(), once; false when there is none.
     *
     * @return array|false what, grants (sentences)
     */
    public static function takeRemembered()
    {
        $http = eZHTTPTool::instance();
        if ( !$http->hasSessionVariable( self::SESSION_KEY ) )
            return false;
        $refusal = $http->sessionVariable( self::SESSION_KEY );
        $http->removeSessionVariable( self::SESSION_KEY );
        return is_array( $refusal ) ? $refusal : false;
    }
}
