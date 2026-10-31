<?php
/**
 * A schema extension (expGraphQLSchemaExtension, graphql.ini SchemaExtensionList[]) that adds fields to the root type
 * Mutation as well (doc/eng-us/features/6.0/graphql.md, "Schema extensions"). The fields come after the kernel's
 * mutations; a name the kernel has already is left to the kernel.
 *
 * A mutation of an extension applies the rules of every mutation itself before it changes anything: its resolver
 * calls expGraphQLMutations::refusal( $ctx ) first (mutations switched off, a GET, an anonymous user, an API key
 * without the scope graphql:write, a session without its CSRF token) and throws an expGraphQLError with what it
 * answers. expwt (the website sidebar) is the first: expwtWrite.
 *
 * @since 6.0.15
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

interface expGraphQLMutationExtension
{
    /**
     * @param expGraphQLTypes $types
     * @return array field name => field config: fields of Mutation
     */
    public function mutationFields( expGraphQLTypes $types );
}

?>
