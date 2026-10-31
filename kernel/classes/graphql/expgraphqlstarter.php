<?php
/**
 * The starter kits of the GraphQL API (exp:graphql:starter; doc/eng-us/guides/graphql-walkthrough.md): a front-end
 * project that reads the site's content from the first minute, generated into a directory the user names from the
 * templates the kernel ships (share/graphql/starters/<kit>/) and the live schema of the siteaccess. Nothing is
 * deployed and no package is installed: the user runs npm (or opens index.html).
 *
 *   html    plain HTML and JavaScript, no build step
 *   next    Next.js (App Router, server components)
 *   nuxt    Nuxt 3
 *   astro   Astro, a static build
 *   expo    React Native with Expo
 *
 * Every kit gets the same queries, generated from the schema: Menu (the children of the site's root), Node (a location
 * by its URL alias: name, class, parent, children) and one query per content class (Content_<class>) with a selection
 * of every field of the class (images in the alias large with their alternative text, rich text as HTML, relations by
 * name and URL alias). They are written twice: as a module of the kit's language and as .graphql files in graphql/,
 * ready for exp:graphql:persist. In a template file, __EXP_ENDPOINT__, __EXP_SITE__, __EXP_SCHEMA_HASH__ and
 * __EXP_KIT__ are replaced; a file name starting with _ starts with . instead (_env is .env).
 *
 * Loaded only after webonyx/graphql-php.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\UnionType;

class expGraphQLStarter
{
    /** The kits: name => label, the module the queries go in, its language (ts, js) */
    const KITS = array(
        'html' => array( 'label' => 'Plain HTML and JavaScript, no build step', 'queries' => 'queries.js', 'language' => 'browser' ),
        'next' => array( 'label' => 'Next.js (App Router)', 'queries' => 'lib/queries.ts', 'language' => 'ts' ),
        'nuxt' => array( 'label' => 'Nuxt 3', 'queries' => 'utils/queries.ts', 'language' => 'ts' ),
        'astro' => array( 'label' => 'Astro, a static build', 'queries' => 'src/lib/queries.ts', 'language' => 'ts' ),
        'expo' => array( 'label' => 'React Native with Expo', 'queries' => 'lib/queries.ts', 'language' => 'ts' ),
    );

    /** The most fields of one class a generated query selects. */
    const MAX_FIELDS = 40;

    /** @var expGraphQLSchemaBuilder */
    protected $builder;

    /** @var string */
    protected $templateRoot;

    /**
     * @param expGraphQLSchemaBuilder $builder the schema the queries are generated from
     * @param string|null $templateRoot the directory of the kits (null: share/graphql/starters of the installation)
     */
    public function __construct( expGraphQLSchemaBuilder $builder, $templateRoot = null )
    {
        $this->builder = $builder;
        $this->templateRoot = rtrim( $templateRoot !== null ? (string)$templateRoot : expGraphQL::rootDir() . '/share/graphql/starters', '/' );
    }

    /**
     * @return string[] the names of the kits
     */
    public static function kits()
    {
        return array_keys( self::KITS );
    }

    /**
     * The files of a kit: the templates with their placeholders filled, and the generated queries.
     *
     * @param string $kit
     * @param array $values endpoint, site
     * @return array relative path => content
     * @throws expGraphQLError BAD_USER_INPUT for an unknown kit
     */
    public function files( $kit, array $values )
    {
        if ( !isset( self::KITS[$kit] ) )
            throw new expGraphQLError( "No starter kit $kit (" . implode( ', ', self::kits() ) . ')', expGraphQLError::BAD_USER_INPUT );
        $replace = array( '__EXP_ENDPOINT__' => (string)$values['endpoint'], '__EXP_SITE__' => (string)$values['site'],
                          '__EXP_SCHEMA_HASH__' => $this->builder->schemaHash(), '__EXP_KIT__' => $kit );
        $files = array();
        $dir = $this->templateRoot . '/' . $kit;
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $iterator as $file )
        {
            if ( !$file->isFile() )
                continue;
            $relative = substr( $file->getPathname(), strlen( $dir ) + 1 );
            $parts = explode( '/', $relative );
            foreach ( $parts as $i => $part )
            {
                if ( $part !== '' && $part[0] === '_' )
                    $parts[$i] = '.' . substr( $part, 1 );
            }
            $files[implode( '/', $parts )] = strtr( (string)file_get_contents( $file->getPathname() ), $replace );
        }
        $queries = $this->queries();
        foreach ( $queries as $name => $query )
            $files['graphql/' . self::fileName( $name ) . '.graphql'] = $query . "\n";
        $files[self::KITS[$kit]['queries']] = self::module( $queries, self::KITS[$kit]['language'], $this->builder->schemaHash() );
        ksort( $files );
        return $files;
    }

    /**
     * The queries every kit gets: Menu, Node, and Content_<class> for every class of the schema.
     *
     * @return array name => query text
     */
    public function queries()
    {
        $schema = $this->builder->schema();
        $queries = array(
            'Menu' => "query Menu {\n  node(urlAlias: \"/\") {\n    id\n    name\n    children(limit: 20) {\n      nodes { id name urlAlias classIdentifier }\n    }\n  }\n}",
            'Node' => "query Node(\$path: String!) {\n  node(urlAlias: \$path) {\n    id\n    name\n    classIdentifier\n    urlAlias\n"
                      . "    mainLocation { id }\n    parent { name urlAlias }\n    children(limit: 20) {\n      totalCount\n"
                      . "      nodes { id name urlAlias classIdentifier }\n    }\n  }\n}",
        );
        $contentFields = array_flip( expGraphQLNaming::CONTENT_FIELDS );
        foreach ( $this->builder->typeNames() as $typeName => $identifier )
        {
            $type = $schema->getType( $typeName );
            if ( !$type instanceof ObjectType )
                continue;
            $lines = array();
            foreach ( $type->getFields() as $name => $field )
            {
                if ( isset( $contentFields[$name] ) || count( $lines ) >= self::MAX_FIELDS )
                    continue;
                $selection = self::selection( $field->getType() );
                if ( $selection === null || self::needsArguments( $field ) )
                    continue;
                $lines[] = '      ' . $name . ( $selection !== '' ? ' ' . $selection : '' );
            }
            $queries['Content_' . $identifier] = "query Content_" . preg_replace( '/[^A-Za-z0-9_]/', '_', $identifier ) . "(\$id: Int!) {\n  content(id: \$id) {\n    id\n    name\n    classIdentifier\n"
                                                 . ( $lines ? "    ... on $typeName {\n" . implode( "\n", $lines ) . "\n    }\n" : '' ) . "  }\n}";
        }
        return $queries;
    }

    /**
     * The sub-selection of a field of this type ('' for a scalar or an enum, null for a type the kits do not read).
     *
     * @param Type $type
     * @return string|null
     */
    public static function selection( Type $type )
    {
        while ( $type instanceof NonNull || $type instanceof ListOfType )
            $type = $type->getWrappedType();
        if ( Type::isLeafType( $type ) )
            return '';
        if ( $type instanceof UnionType )
            return null;
        $name = $type->name;
        if ( $name === 'Image' )
            return '{ url(alias: "large") width(alias: "large") height(alias: "large") alt }';
        if ( $name === 'RichText' )
            return '{ html }';
        if ( $type instanceof InterfaceType && $name === 'Content' )
            return '{ id name urlAlias classIdentifier }';
        if ( !$type instanceof ObjectType && !$type instanceof InterfaceType )
            return null;
        $leaves = array();
        foreach ( $type->getFields() as $fieldName => $field )
        {
            $inner = $field->getType();
            while ( $inner instanceof NonNull || $inner instanceof ListOfType )
                $inner = $inner->getWrappedType();
            if ( Type::isLeafType( $inner ) && !self::needsArguments( $field ) )
                $leaves[] = $fieldName;
            if ( count( $leaves ) >= 6 )
                break;
        }
        return $leaves ? '{ ' . implode( ' ', $leaves ) . ' }' : '{ __typename }';
    }

    /**
     * @param \GraphQL\Type\Definition\FieldDefinition $field
     * @return bool the field has an argument that must be given
     */
    protected static function needsArguments( $field )
    {
        foreach ( $field->args as $arg )
        {
            if ( $arg->getType() instanceof NonNull && !$arg->defaultValueExists() )
                return true;
        }
        return false;
    }

    /**
     * The queries as a module of the kit's language.
     *
     * @param array $queries name => text
     * @param string $language ts or browser
     * @param string $schemaHash
     * @return string
     */
    public static function module( array $queries, $language, $schemaHash )
    {
        $byClass = array();
        foreach ( $queries as $name => $query )
        {
            if ( strncmp( $name, 'Content_', 8 ) === 0 )
                $byClass[substr( $name, 8 )] = $query;
        }
        $json = function ( $value ) { return json_encode( $value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); };
        $head = "// Generated by ./console exp:graphql:starter from the schema $schemaHash of the site. Run it again after a\n"
              . "// class changed (exp:graphql:status shows the schema hash), or edit the queries here.\n";
        if ( $language === 'browser' )
            return $head . "window.EXP_QUERIES = {\n  MENU: " . $json( $queries['Menu'] ) . ",\n  NODE: " . $json( $queries['Node'] )
                   . ",\n  BY_CLASS: " . $json( (object)$byClass ) . "\n};\n";
        return $head . "export const MENU = " . $json( $queries['Menu'] ) . ";\n\nexport const NODE = " . $json( $queries['Node'] )
               . ";\n\n/** The query of each content class, by class identifier: content(id) with every field of the class. */\n"
               . "export const BY_CLASS: Record<string, string> = " . $json( (object)$byClass ) . ";\n";
    }

    /**
     * @param string $name Content_ng_article
     * @return string content-ng-article
     */
    public static function fileName( $name )
    {
        return trim( preg_replace( '/[^a-z0-9]+/', '-', strtolower( $name ) ), '-' );
    }

    /**
     * Writes the files of a kit below a directory, which must not exist or be empty.
     *
     * @param string $to
     * @param array $files relative path => content
     * @return int how many files were written
     * @throws expGraphQLError BAD_USER_INPUT when the directory has files, or a path would leave it
     */
    public static function write( $to, array $files )
    {
        $to = rtrim( (string)$to, '/' );
        if ( is_dir( $to ) && count( (array)scandir( $to ) ) > 2 )
            throw new expGraphQLError( "$to is not empty: give a new directory", expGraphQLError::BAD_USER_INPUT );
        if ( file_exists( $to ) && !is_dir( $to ) )
            throw new expGraphQLError( "$to is a file: give a new directory", expGraphQLError::BAD_USER_INPUT );
        $written = 0;
        foreach ( $files as $relative => $content )
        {
            if ( $relative === '' || $relative[0] === '/' || preg_match( '#(^|/)\.\.(/|$)#', $relative ) )
                throw new expGraphQLError( "Not a path inside the kit: $relative", expGraphQLError::BAD_USER_INPUT );
            $path = $to . '/' . $relative;
            if ( !is_dir( dirname( $path ) ) && !mkdir( dirname( $path ), eZDir::dirMode( 0755 ), true ) && !is_dir( dirname( $path ) ) )
                throw new expGraphQLError( 'Cannot make ' . dirname( $path ), expGraphQLError::INTERNAL );
            if ( file_put_contents( $path, $content ) === false )
                throw new expGraphQLError( "Cannot write $path", expGraphQLError::INTERNAL );
            @chmod( $path, eZFile::fileMode( 0644 ) );
            $written++;
        }
        return $written;
    }
}

?>
