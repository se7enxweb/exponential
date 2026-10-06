<?php
/**
 * File containing the ezpContentLimitation class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Asks the handler an extension registered for a content policy limitation the kernel does not know
 * (site.ini [RoleSettings] LimitationHandlers[<limitation>]=<class>, see ezpContentLimitationHandler).
 *
 * Without a usable handler the limitation denies, in the PHP checks and in the SQL of fetches alike: a limitation
 * that nobody evaluates must never widen what a policy allows.
 *
 * @package kernel
 */
class ezpContentLimitation
{
    /**
     * The SQL condition of a limitation that cannot be evaluated: it matches no row.
     */
    const DENY_SQL = '1 = 0';

    /**
     * The limitations the kernel evaluates itself (besides StateGroup_<identifier>). An extension cannot take one
     * of them over; where one does not apply (Language in a list fetch) it is left out, as before.
     *
     * @var string[]
     */
    public static $kernelLimitations = array( 'Class', 'ParentClass', 'ParentDepth', 'Section', 'User_Section',
                                              'Language', 'Owner', 'ParentOwner', 'Group', 'ParentGroup', 'State',
                                              'Status', 'Node', 'Subtree', 'User_Subtree', 'NewState' );

    /**
     * Returns whether the kernel evaluates the limitation $limitation itself.
     *
     * @param string $limitation
     * @return bool
     */
    public static function isKernelLimitation( $limitation )
    {
        return in_array( $limitation, self::$kernelLimitations, true ) || strncmp( $limitation, 'StateGroup_', 11 ) === 0;
    }

    /**
     * Returns the handler registered for $limitation, or null when there is none or it is unusable.
     *
     * @param string $limitation
     * @return ezpContentLimitationHandler|null
     */
    public static function handler( $limitation )
    {
        if ( self::isKernelLimitation( $limitation ) )
        {
            return null;
        }
        $ini = eZINI::instance( 'site.ini' );
        if ( !$ini->hasVariable( 'RoleSettings', 'LimitationHandlers' ) )
        {
            return null;
        }
        $handler = eZExtension::getHandlerClass( new ezpExtensionOptions( array( 'iniFile' => 'site.ini',
                                                                                 'iniSection' => 'RoleSettings',
                                                                                 'iniVariable' => 'LimitationHandlers',
                                                                                 'handlerIndex' => $limitation ) ) );
        if ( !is_object( $handler ) )
        {
            return null;
        }
        if ( !$handler instanceof ezpContentLimitationHandler )
        {
            eZDebug::writeError( 'The handler ' . get_class( $handler ) . " of the limitation $limitation does not implement ezpContentLimitationHandler; the limitation denies", __METHOD__ );
            return null;
        }
        return $handler;
    }

    /**
     * Returns whether the limitation $limitation with $values lets $userID use $functionName on $subject; false
     * when no handler evaluates it.
     *
     * @param string $limitation
     * @param array $values
     * @param string $functionName
     * @param eZContentObject|eZContentObjectTreeNode|eZContentObjectVersion $subject
     * @param int $userID
     * @return bool
     */
    public static function checkAccess( $limitation, $values, $functionName, $subject, $userID )
    {
        $handler = self::handler( $limitation );
        if ( $handler === null )
        {
            eZDebug::writeDebug( "No handler for the limitation $limitation; it denies", __METHOD__ );
            return false;
        }
        return $handler->checkAccess( $limitation, (array)$values, $functionName, $subject, (int)$userID ) === true;
    }

    /**
     * Returns the SQL condition of the limitation $limitation with $values for a content/read fetch of $userID;
     * DENY_SQL when no handler evaluates it or the handler cannot express it.
     *
     * @param string $limitation
     * @param array $values
     * @param string $tableAliasName
     * @param int|bool $userID The user of the fetch; false for the current user
     * @return string
     */
    public static function permissionSQL( $limitation, $values, $tableAliasName, $userID = false )
    {
        $handler = self::handler( $limitation );
        if ( $handler === null )
        {
            eZDebug::writeDebug( "No handler for the limitation $limitation; the policy gives no access in fetches", __METHOD__ );
            return self::DENY_SQL;
        }
        if ( $userID === false )
        {
            $userID = eZUser::currentUserID();
        }
        $sql = $handler->permissionSQL( $limitation, (array)$values, $tableAliasName, (int)$userID );
        if ( !is_string( $sql ) || trim( $sql ) === '' )
        {
            return self::DENY_SQL;
        }
        return '( ' . $sql . ' )';
    }
}
