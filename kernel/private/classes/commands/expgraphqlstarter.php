<?php
/**
 * @description Generate a starter front end (Next.js, Nuxt, Astro, plain HTML or Expo) that reads the site through the GraphQL API, with queries from the live schema; a dry run unless --apply
 * @alias exp:graphql:starter
 *
 * File containing the exp:graphql:starter command (bin/php/expgraphqlstarter.php); guide: doc/eng-us/guides/graphql-walkthrough.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expgraphqlstarter extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_ERRORS = 1;
    const EXIT_USAGE = 2;
    const EXIT_UNAVAILABLE = 3;

    public function run()
    {
        $kits = array();
        foreach ( \expGraphQLStarter::KITS as $name => $kit )
            $kits[] = sprintf( '  %-6s %s', $name, $kit['label'] );
        $this->script( array( 'description' => "Generate a starter front end for the GraphQL API\n" .
                                               "Writes a project into a new directory: the kit's files (shipped in share/graphql/starters/) with the\n" .
                                               "end point filled in, and queries generated from the schema of the siteaccess: the menu, a page by its\n" .
                                               "URL alias, and one query per content class with every field of the class, as a module of the kit and\n" .
                                               "as .graphql files for exp:graphql:persist. Every generated query is checked against the schema and its\n" .
                                               "limits first. Nothing is installed or deployed: run npm in the directory. Lists the files and writes\n" .
                                               "nothing without --apply.\n" .
                                               "\n" .
                                               implode( "\n", $kits ) . "\n" .
                                               "\n" .
                                               "./console exp:graphql:starter --kit=next --to=../shop-front\n" .
                                               "./console exp:graphql:starter --kit=astro --to=../site-static --endpoint=https://www.example.com/api/graphql --apply\n" .
                                               "./console exp:graphql:starter --list\n" .
                                               "\n" .
                                               "Exit code: 0 done (or shown); 1 a generated query does not fit the schema or its limits, or a file\n" .
                                               "could not be written; 3 the library is missing; 2 wrong options (an unknown kit, a directory that is\n" .
                                               "not empty).",
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true ) );
        $options = $this->startup( '[kit:][to:][endpoint:][list][apply]', '',
                                   array( 'kit' => 'the kit: html, next, nuxt, astro or expo',
                                          'to' => 'the directory to write the project to (new or empty)',
                                          'endpoint' => 'the address of the end point (default: site.ini [SiteSettings] SiteURL and /api/graphql)',
                                          'list' => 'list the kits',
                                          'apply' => 'write; without it nothing is changed' ) );
        $out = $this->cli();
        if ( !empty( $options['list'] ) )
        {
            foreach ( \expGraphQLStarter::KITS as $name => $kit )
                $out->output( sprintf( '%-6s %s', $name, $kit['label'] ) );
            $this->shutdown( self::EXIT_OK );
        }
        $kit = isset( $options['kit'] ) && $options['kit'] ? (string)$options['kit'] : '';
        if ( !isset( \expGraphQLStarter::KITS[$kit] ) )
            $this->usage( '--kit must be one of ' . implode( ', ', \expGraphQLStarter::kits() ) );
        $to = isset( $options['to'] ) && $options['to'] ? (string)$options['to'] : '';
        if ( $to === '' )
            $this->usage( 'give the directory with --to' );
        if ( is_dir( $to ) && count( (array)scandir( $to ) ) > 2 )
            $this->usage( "$to is not empty: give a new directory" );
        if ( !\expGraphQL::loadLibrary() )
        {
            $out->error( 'FAIL webonyx/graphql-php is not installed (composer.json, or graphql.ini [GraphQLSettings] LibraryPath)' );
            $this->shutdown( self::EXIT_UNAVAILABLE );
        }
        $endpoint = isset( $options['endpoint'] ) && $options['endpoint'] ? (string)$options['endpoint'] : Expgraphqlenable::endpoint();
        $builder = \expGraphQLSchemaBuilder::fromSettings();
        $starter = new \expGraphQLStarter( $builder );
        $files = $starter->files( $kit, array( 'endpoint' => $endpoint, 'site' => \expGraphQL::currentSiteAccess() ) );

        $failed = 0;
        foreach ( $starter->queries() as $name => $query )
        {
            $problems = self::problems( $builder->schema(), $query );
            if ( $problems )
            {
                $failed++;
                $out->error( "FAIL $name: " . implode( '; ', $problems ) );
            }
        }
        if ( $failed )
        {
            $out->error( "FAIL $failed generated queries do not fit the schema or its limits; nothing written" );
            $this->shutdown( self::EXIT_ERRORS );
        }
        foreach ( array_keys( $files ) as $relative )
            $out->output( ( !empty( $options['apply'] ) ? 'PASS wrote ' : 'DRY RUN would write ' ) . $to . '/' . $relative );
        if ( empty( $options['apply'] ) )
        {
            $out->output( 'DRY RUN ' . count( $files ) . " files of the kit $kit, schema " . $builder->schemaHash() . '; run again with --apply' );
            $this->shutdown( self::EXIT_OK );
        }
        try
        {
            \expGraphQLStarter::write( $to, $files );
        }
        catch ( \expGraphQLError $e )
        {
            $out->error( 'FAIL ' . $e->getMessage() );
            $this->shutdown( self::EXIT_ERRORS );
        }
        $out->output( 'PASS ' . count( $files ) . " files of the kit $kit in $to. Next: cd $to && " . ( $kit === 'html' ? 'python3 -m http.server 8000' : 'npm install && npm run dev' )
                      . ' (README.md says more)' );
        $this->shutdown( self::EXIT_OK );
    }

    /**
     * @param \GraphQL\Type\Schema $schema
     * @param string $query
     * @return string[] why a generated query does not fit the schema or graphql.ini's limits
     */
    public static function problems( $schema, $query )
    {
        $rules = \GraphQL\Validator\DocumentValidator::allRules();
        $rules[\GraphQL\Validator\Rules\QueryDepth::class] = new \GraphQL\Validator\Rules\QueryDepth( \expGraphQL::intSetting( 'MaxDepth', 12 ) );
        $problems = Expgraphqlpersist::problems( $schema, $query );
        if ( !$problems )
        {
            foreach ( \GraphQL\Validator\DocumentValidator::validate( $schema, \GraphQL\Language\Parser::parse( $query ), $rules ) as $error )
                $problems[] = $error->getMessage();
        }
        return $problems;
    }

    /**
     * @param string $message
     */
    protected function usage( $message )
    {
        $this->cli()->error( "FAIL $message" );
        $this->shutdown( self::EXIT_USAGE );
    }
}

}
?>
