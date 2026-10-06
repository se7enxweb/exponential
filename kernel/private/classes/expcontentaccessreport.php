<?php
/**
 * File containing the expContentAccessReport class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Answers "may this user do this with this object or node, and if not, which limitation refuses it": the access
 * checks of the kernel made for another user than the current one (eZUser::accessUser()), with the policies and
 * limitations that refused, extension limitations included. Used by the command exp:access:check, so that whoever
 * keeps the roles can ask what a user sees without signing in as that user.
 *
 * It decides exactly as the views do: checkAccess() of the node or object, and for edit
 * eZContentObject::editAccess() with the filter content/edit/access. Nothing is changed and nothing of the answer
 * is kept.
 *
 * @package kernel
 */
class expContentAccessReport
{
    /**
     * The content functions that are checked on an existing object or node.
     *
     * @var string[]
     */
    public static $functions = array( 'read', 'edit', 'remove', 'pdf', 'diff', 'view_embed', 'translate', 'manage_locations', 'hide' );

    /**
     * Checks $function on $subject for the user $userID.
     *
     * @param eZContentObject|eZContentObjectTreeNode $subject
     * @param int $userID
     * @param string $function One of $functions
     * @param string|bool $language A language code, or false
     * @return array 'allowed' (bool), 'user_id', 'function', 'language', 'refused_by' (a list of array( 'policy',
     *               'limitation', 'required', 'handler' ) of the policies that did not allow it; a policy with no
     *               limitation named is one the user does not have at all) and 'error' (a sentence, or null)
     */
    public static function check( $subject, $userID, $function = 'read', $language = false )
    {
        $report = array( 'allowed' => false, 'user_id' => 0, 'function' => (string)$function,
                         'language' => $language ? (string)$language : false, 'refused_by' => array(), 'error' => null );
        if ( !in_array( $function, self::$functions, true ) )
        {
            $report['error'] = "The function '$function' is not checked on an object; one of: " . implode( ', ', self::$functions );
            return $report;
        }
        $object = $subject instanceof eZContentObjectTreeNode ? $subject->attribute( 'object' ) : $subject;
        if ( !$object instanceof eZContentObject )
        {
            $report['error'] = 'There is no such object or node.';
            return $report;
        }
        $user = eZUser::accessUser( $userID ? $userID : false );
        if ( !$user instanceof eZUser )
        {
            $report['error'] = 'There is no such user, or the account is disabled: it has no access.';
            return $report;
        }
        $userID = (int)$user->attribute( 'contentobject_id' );
        $report['user_id'] = $userID;
        $language = $report['language'];

        if ( $function === 'edit' )
        {
            $allowed = $object->editAccess( null, $language, $userID );
        }
        else if ( $subject instanceof eZContentObjectTreeNode )
        {
            $allowed = $subject->checkAccess( $function, false, false, false, $language, $userID ) == 1;
        }
        else
        {
            $allowed = $object->checkAccess( $function, false, false, false, $language, $userID ) == 1;
        }
        $report['allowed'] = (bool)$allowed;
        if ( !$report['allowed'] )
        {
            $report['refused_by'] = self::refusals( $object->checkAccess( $function, false, false, true, $language, $userID ), $function );
        }
        return $report;
    }

    /**
     * The policies of an access list of eZContentObject::checkAccess() and the limitation that refused each.
     *
     * @param mixed $accessList
     * @param string $function
     * @return array
     */
    protected static function refusals( $accessList, $function )
    {
        $refusals = array();
        // edit without a policy answers with the rule of objects never published (0) instead of an access list
        if ( !is_array( $accessList ) || empty( $accessList['PolicyList'] ) )
        {
            $refusals[] = array( 'policy' => null, 'limitation' => null, 'required' => array(), 'handler' => null,
                                 'text' => "no policy for content/$function" );
            return $refusals;
        }
        foreach ( $accessList['PolicyList'] as $policy )
        {
            $limitation = isset( $policy['LimitationList']['Limitation'] ) ? (string)$policy['LimitationList']['Limitation'] : null;
            $required = isset( $policy['LimitationList']['Required'] ) ? array_values( (array)$policy['LimitationList']['Required'] ) : array();
            $handler = null;
            if ( $limitation !== null && !ezpContentLimitation::isKernelLimitation( $limitation ) )
            {
                $instance = ezpContentLimitation::handler( $limitation );
                $handler = $instance ? get_class( $instance ) : false;
            }
            $refusals[] = array( 'policy' => isset( $policy['PolicyID'] ) ? $policy['PolicyID'] : null, 'limitation' => $limitation,
                                 'required' => $required, 'handler' => $handler,
                                 'text' => $limitation === null ? 'no limitation named'
                                           : $limitation . '( ' . implode( ', ', array_map( 'strval', $required ) ) . ' )' .
                                             ( $handler === false ? ', no handler evaluates it' : ( $handler ? ", evaluated by $handler" : '' ) ) );
        }
        return $refusals;
    }
}
