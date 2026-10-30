<?php
/**
 * A schema extension of the GraphQL API (graphql.ini [GraphQLSettings] SchemaExtensionList[]; doc/eng-us/features/
 * 6.0/graphql.md, "Schema extensions"): what an extension adds to the schema of every siteaccess where it is
 * available: root queries, fields of Location, and the type of the fields of its datatypes. The kernel ships two, for
 * eztags (expGraphQLTagsExtension) and explayouts (expGraphQLLayoutsExtension), each available only while its
 * extension is active.
 *
 * Every method builds field configs of webonyx/graphql-php, as the kernel's own types do (expGraphQLTypes): a type an
 * extension defines is made with $types->register( name, factory ), once per schema, and its name must not be one a
 * content class could take (expGraphQLNaming::RESERVED_TYPES lists the kernel's; an extension's own names go there
 * too when it is shipped with the kernel). A resolver reads through the user's rights, as the kernel's do: what the
 * user may not read is null or left out. A resolver that needs many values of a level asks a batch loader
 * ($ctx->loader( kind, language, batch )).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

interface expGraphQLSchemaExtension
{
    /**
     * @return bool the extension can answer here (its extension is active, its classes are there)
     */
    public function isAvailable();

    /**
     * @return string a name for the schema hash: the hash changes when the extensions of a schema change
     */
    public function name();

    /**
     * @param expGraphQLTypes $types
     * @return array field name => field config: root queries
     */
    public function queryFields( expGraphQLTypes $types );

    /**
     * @param expGraphQLTypes $types
     * @return array field name => field config: fields of Location (the value is an expGraphQLLocation)
     */
    public function locationFields( expGraphQLTypes $types );

    /**
     * @return string[] the datatypes it maps (data_type_string)
     */
    public function datatypes();

    /**
     * @param array $attribute a class attribute of expGraphQLClassCatalogue
     * @param expGraphQLTypes $types
     * @return array|null array( type, function( eZContentObjectAttribute $field, expGraphQLContext $ctx, array $args ) )
     */
    public function fieldMapping( array $attribute, expGraphQLTypes $types );
}

?>
