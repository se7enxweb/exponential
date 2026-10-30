<?php
/**
 * The schema extensions of a siteaccess (graphql.ini [GraphQLSettings] SchemaExtensionList[]): every class named
 * there that implements expGraphQLSchemaExtension and says it is available. A class that is missing or not an
 * extension is reported to the debug log and left out, so a removed extension never breaks the API.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLSchemaExtensions
{
    /**
     * @return expGraphQLSchemaExtension[] the available extensions of SchemaExtensionList[], in its order
     */
    public static function fromSettings()
    {
        return self::instances( expGraphQL::listSetting( 'SchemaExtensionList' ) );
    }

    /**
     * @param string[] $classes
     * @return expGraphQLSchemaExtension[]
     */
    public static function instances( array $classes )
    {
        $extensions = array();
        foreach ( array_unique( $classes ) as $class )
        {
            if ( !class_exists( $class ) || !is_subclass_of( $class, 'expGraphQLSchemaExtension' ) )
            {
                eZDebug::writeWarning( "graphql.ini SchemaExtensionList[]: $class is not a class implementing expGraphQLSchemaExtension", __METHOD__ );
                continue;
            }
            $extension = new $class();
            if ( $extension->isAvailable() )
                $extensions[] = $extension;
        }
        return $extensions;
    }

    /**
     * @param expGraphQLSchemaExtension[] $extensions
     * @return string[] their names (exp:graphql:status, the schema hash)
     */
    public static function names( array $extensions )
    {
        $names = array();
        foreach ( $extensions as $extension )
            $names[] = $extension->name();
        return $names;
    }
}

?>
