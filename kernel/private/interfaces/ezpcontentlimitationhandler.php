<?php
/**
 * File containing the ezpContentLimitationHandler interface
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Evaluates a policy limitation of the content module that the kernel does not know itself.
 *
 * An extension that adds a limitation to a content function (for example through the module/functionlist filter)
 * registers one handler per limitation in site.ini:
 *
 * <code>
 * [RoleSettings]
 * LimitationHandlers[MyLimitation]=myLimitationHandler
 * </code>
 *
 * The kernel asks the handler wherever a policy with that limitation is checked: the checkAccess() methods of
 * objects, nodes and versions, the subtree notification rules, and the SQL of list and tree fetches. A limitation
 * without a handler denies everywhere.
 *
 * @see ezpContentLimitation
 * @package kernel
 */
interface ezpContentLimitationHandler
{
    /**
     * Returns whether the limitation lets $userID use $functionName on $subject.
     *
     * @param string $limitation The limitation name, as the policy stores it
     * @param array $values The values of the limitation in the policy
     * @param string $functionName The content function: read, edit, versionread, ...
     * @param eZContentObject|eZContentObjectTreeNode|eZContentObjectVersion $subject What the access is checked on
     * @param int $userID The user the access is checked for, not always the current user
     * @return bool
     */
    public function checkAccess( $limitation, array $values, $functionName, $subject, $userID );

    /**
     * Returns the SQL condition that keeps the objects of a content/read list or tree fetch the limitation allows.
     *
     * The condition is joined with AND to the other limitations of the same policy. It may refer to the table
     * ezcontentobject and to the node table under the alias $tableAliasName. Values from the policy must be cast
     * or escaped. A handler that cannot express the limitation in SQL returns false, and the policy then gives no
     * access in fetches.
     *
     * @param string $limitation The limitation name, as the policy stores it
     * @param array $values The values of the limitation in the policy
     * @param string $tableAliasName The alias of the node table in the query
     * @param int $userID The user the fetch is for
     * @return string|false
     */
    public function permissionSQL( $limitation, array $values, $tableAliasName, $userID );
}
