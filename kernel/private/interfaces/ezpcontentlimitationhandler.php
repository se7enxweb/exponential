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
     * Returns whether the limitation lets $userID use $functionName on $subject. Only true allows; an exception
     * denies (and is logged once per request), it does not end the request.
     *
     * The kernel makes one instance of the handler per request and asks it for every object, node or version it
     * checks, so the handler may keep what it looked up in properties of its own for the rest of the request. It
     * must not keep anything in static properties: a persistent worker (Velocity) serves the next request from
     * the same process. Checks are made for other users than the current one (notifications), so nothing may be
     * taken from the session or from eZUser::currentUser(): use $userID.
     *
     * @param string $limitation The limitation name, as the policy stores it
     * @param array $values The values of the limitation in the policy, as strings
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
     * ezcontentobject and to the node table under the alias $tableAliasName. Two forms are accepted:
     *
     * - A string, put in parentheses by the kernel. It must be self-contained: quotes and parentheses balanced, and
     *   outside quoted strings no ";" and no comment ("--", "#", slash-star); otherwise the policy gives no access
     *   in fetches. Values from the policy must be cast (intval) or escaped (eZDB::escapeString()) by the handler.
     * - array( 'column' => 'ezcontentobject.section_id', 'values' => $values, 'type' => 'int' or 'string',
     *   'not' => false ), or a list of such arrays joined by AND: the kernel writes the IN statement and casts or
     *   escapes the values itself. The safer form wherever it is enough.
     *
     * A handler that cannot express the limitation in SQL returns false, and the policy then gives no access in
     * fetches. So does an exception, or any other answer (see ezpContentLimitation::sqlCondition()).
     *
     * @param string $limitation The limitation name, as the policy stores it
     * @param array $values The values of the limitation in the policy, as strings
     * @param string $tableAliasName The alias of the node table in the query
     * @param int $userID The user the fetch is for
     * @return string|array|false
     */
    public function permissionSQL( $limitation, array $values, $tableAliasName, $userID );
}
