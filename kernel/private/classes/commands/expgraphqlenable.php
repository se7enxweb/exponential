<?php
/**
 * @description Switch the GraphQL API on in one command: the siteaccesses, the mutations, CORS for a front end, an API key, the schema, a curl example; a dry run unless --apply
 * @alias exp:graphql:enable
 *
 * File containing the exp:graphql:enable command (bin/php/expgraphqlenable.php); guide: doc/eng-us/features/6.0/graphql.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expgraphqlenable extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_ERRORS = 1;
    const EXIT_USAGE = 2;
    const EXIT_UNAVAILABLE = 3;

    public function run()
    {
        $this->script( array( 'description' => "Switch the GraphQL API on\n" .
                                               "Sets graphql.ini [GraphQLSettings] Enabled=enabled in settings/override, adds the siteaccesses of\n" .
                                               "--siteaccess to SiteAccessList[] (without it the API answers in every siteaccess); the mutations\n" .
                                               "come on with it (MutationsEnabled=enabled, the default), and --mutations writes that too, where\n" .
                                               "exp:graphql:disable --mutations switched them off. Lets a front end's origin call it with\n" .
                                               "--origin (rest.ini [CorsSettings]), makes a personal API key for a user with --key-for (the token\n" .
                                               "is shown once), checks that the schema builds, and prints the end point, a curl example and the\n" .
                                               "playground. Shows the changes as a diff and writes nothing without --apply; with --apply each file\n" .
                                               "is backed up first and the INI caches are cleared.\n" .
                                               "\n" .
                                               "./console exp:graphql:enable                                        what would change\n" .
                                               "./console exp:graphql:enable --siteaccess=site --apply\n" .
                                               "./console exp:graphql:enable --siteaccess=site --origin=https://shop.example.com --key-for=frontend --apply\n" .
                                               "./console exp:graphql:enable --siteaccess=site --mutations --key-for=editor --apply\n" .
                                               "\n" .
                                               "Exit code: 0 done (or shown); 1 a file could not be written, or the key not made; 3 the library is\n" .
                                               "missing; 2 wrong options (an unknown siteaccess or user, an origin that is not one).",
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true ) );
        $options = $this->startup( '[siteaccess:][origin:][mutations][key-for:][key-name:][scope:][days:][apply]', '',
                                   array( 'siteaccess' => 'the siteaccesses it answers in, comma separated (default: every siteaccess)',
                                          'origin' => 'the origin of a front end allowed to call it from a browser (CORS), comma separated',
                                          'mutations' => 'switch the mutations on again (in the siteaccesses of --siteaccess); they are on by default',
                                          'key-for' => 'make a personal API key for this user (a login)',
                                          'key-name' => 'the name of the key (default: GraphQL front end)',
                                          'scope' => 'the scopes of the key: graphql:read (the default), graphql:write (the default with --mutations)',
                                          'days' => 'the lifetime of the key in days (rest.ini [ApiKeySettings] ExpiryChoices[]; default 90)',
                                          'apply' => 'write; without it nothing is changed' ) );
        $apply = !empty( $options['apply'] );
        $out = $this->cli();
        $siteAccesses = self::listOption( $options, 'siteaccess' );
        $known = \expIniEditor::knownSiteAccesses();
        foreach ( $siteAccesses as $siteAccess )
        {
            if ( !in_array( $siteAccess, $known, true ) )
                $this->usage( "no siteaccess $siteAccess (" . implode( ', ', $known ) . ')' );
        }
        $mutations = !empty( $options['mutations'] );
        $owner = null;
        $scopes = array();
        $expires = 0;
        if ( !empty( $options['key-for'] ) )
        {
            $owner = \eZUser::fetchByName( (string)$options['key-for'] );
            if ( !$owner instanceof \eZUser )
                $this->usage( 'no user with the login ' . $options['key-for'] );
            try
            {
                $scopes = \expGraphQLSetup::keyScopes( self::listOption( $options, 'scope' ), $mutations );
            }
            catch ( \expGraphQLError $e )
            {
                $this->usage( $e->getMessage() );
            }
            $expires = Expgraphqlkeys::expiry( isset( $options['days'] ) && $options['days'] !== false ? (string)$options['days'] : '90' );
            if ( $expires === false )
                $this->usage( '--days must be one of ' . implode( ', ', \expApiKey::expiryChoices() ) );
            $allowed = \expApiKey::scopesForUser( $owner );
            if ( array_diff( $scopes, $allowed ) )
                $this->usage( 'the user ' . $options['key-for'] . ' may not have a key with ' . implode( ', ', array_diff( $scopes, $allowed ) )
                              . ' (the policies its scopes need, and the Scope limitation of apikey/create)' );
        }
        if ( !\expGraphQL::loadLibrary() )
        {
            $out->error( 'FAIL webonyx/graphql-php is not installed (composer.json, or graphql.ini [GraphQLSettings] LibraryPath)' );
            $this->shutdown( self::EXIT_UNAVAILABLE );
        }
        $changes = array();
        try
        {
            $changes = \expGraphQLSetup::enableChanges( $siteAccesses, self::listOption( $options, 'origin' ), $mutations );
        }
        catch ( \expGraphQLError $e )
        {
            $this->usage( $e->getMessage() );
        }

        // the schema first: a schema that does not build is no reason to switch anything on
        $builder = \expGraphQLSchemaBuilder::fromSettings();
        $builder->schema()->assertValid();
        $out->output( 'PASS the schema builds: ' . count( $builder->catalogue()->identifiers() ) . ' classes, schema ' . $builder->schemaHash() );

        $result = \expGraphQLSetup::apply( $changes, !$apply );
        $failed = self::report( $out, $result, $apply );

        if ( $owner !== null )
        {
            if ( !$apply )
                $out->output( 'DRY RUN would make an API key for ' . $options['key-for'] . ' with ' . implode( ' ', $scopes ) );
            else if ( !$failed )
            {
                $made = Expgraphqlkeys::make( $owner, isset( $options['key-name'] ) && $options['key-name'] ? (string)$options['key-name'] : 'GraphQL front end',
                                              $scopes, $expires );
                if ( $made === null )
                {
                    $out->error( 'FAIL the API key could not be made (rest.ini [ApiKeySettings] ApiKeys, the table exp_api_key)' );
                    $failed = true;
                }
                else
                    $out->output( 'PASS API key ' . $made['prefix'] . ' for ' . $options['key-for'] . ' (' . implode( ' ', $scopes ) . "), shown once:\n\n    " . $made['token'] . "\n" );
            }
        }

        $endpoint = self::endpoint();
        $out->output( '' );
        $out->output( 'End point   ' . $endpoint );
        $out->output( 'Playground  Setup > GraphQL (graphql/playground) in the administration' );
        $out->output( 'Try it      curl -H \'Content-Type: application/json\'' . ( $owner !== null ? ' -H \'Authorization: Bearer <the key>\'' : '' )
                      . ' -d \'{"query":"{ siteaccess { name schemaHash } currentUser { login } }"}\' ' . $endpoint );
        if ( !$apply )
            $out->output( 'DRY RUN nothing written; run again with --apply' );
        $this->shutdown( $failed ? self::EXIT_ERRORS : self::EXIT_OK );
    }

    /**
     * Prints the files of a plan with their diffs.
     *
     * @param \eZCLI $out
     * @param array $result of expGraphQLSetup::apply()
     * @param bool $apply
     * @return bool a file failed
     */
    public static function report( $out, array $result, $apply )
    {
        $failed = false;
        foreach ( $result['files'] as $file )
        {
            if ( $file['error'] !== null )
            {
                $out->error( 'FAIL ' . $file['path'] . ': ' . $file['error'] );
                $failed = true;
                continue;
            }
            if ( !$file['changed'] )
            {
                $out->output( 'PASS ' . $file['path'] . ' says so already' );
                continue;
            }
            $out->output( ( $apply ? 'PASS wrote ' : 'DRY RUN would change ' ) . $file['path'] );
            $out->output( rtrim( $file['diff'] ) );
        }
        return $failed;
    }

    /**
     * @param array $options
     * @param string $name
     * @return string[] a comma separated option as a list
     */
    public static function listOption( array $options, $name )
    {
        if ( !isset( $options[$name] ) || $options[$name] === false || $options[$name] === '' )
            return array();
        return array_values( array_unique( array_filter( array_map( 'trim', explode( ',', (string)$options[$name] ) ), 'strlen' ) ) );
    }

    /**
     * @return string the address of the end point (site.ini [SiteSettings] SiteURL; a siteaccess of its own host, or
     *                the header X-Siteaccess, picks another)
     */
    public static function endpoint()
    {
        $host = \eZINI::instance( 'site.ini' )->variable( 'SiteSettings', 'SiteURL' );
        $host = trim( (string)$host ) !== '' ? (string)$host : 'example.com';
        if ( !preg_match( '#^https?://#', $host ) )
            $host = 'https://' . $host;
        return rtrim( $host, '/' ) . '/api/graphql';
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
