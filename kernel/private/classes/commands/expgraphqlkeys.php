<?php
/**
 * @description The personal API keys of the GraphQL API: list them, make one for a user (the token is shown once) or revoke one; a dry run unless --apply
 * @alias exp:graphql:keys
 *
 * File containing the exp:graphql:keys command (bin/php/expgraphqlkeys.php); guide: doc/eng-us/features/6.0/graphql.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expgraphqlkeys extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_ERRORS = 1;
    const EXIT_USAGE = 2;

    /** The machine output of --list --format=json. */
    const SCHEMA = 'exp-graphql-keys/1';

    public function run()
    {
        $this->script( array( 'description' => "The personal API keys of the GraphQL API\n" .
                                               "Lists the keys with a scope of the GraphQL API (graphql:read, graphql:write), makes one for a user\n" .
                                               "(the scopes are checked against the user's policies as on the user's own page; the token is shown\n" .
                                               "once and never stored), or revokes one. The keys are the kernel's personal API keys (rest.ini\n" .
                                               "[ApiKeySettings]): the owner manages them on the page API access, an administrator at\n" .
                                               "oauthadmin/keys. Making and revoking are recorded in the audit (access.apikey.create, .revoke).\n" .
                                               "Nothing is written without --apply.\n" .
                                               "\n" .
                                               "./console exp:graphql:keys                                          the list\n" .
                                               "./console exp:graphql:keys --list --format=json\n" .
                                               "./console exp:graphql:keys --create --for=frontend --name='Shop front end' --apply\n" .
                                               "./console exp:graphql:keys --create --for=editor --scope=graphql:read,graphql:write --days=30 --apply\n" .
                                               "./console exp:graphql:keys --revoke=expk_4f2a9c1d7e3b --apply\n" .
                                               "\n" .
                                               "Exit code: 0 done (or shown); 1 the key could not be made or revoked; 2 wrong options (an unknown\n" .
                                               "user or key, a scope the user may not have, a lifetime that is not offered).",
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true ) );
        $options = $this->startup( '[list][create][for:][name:][scope:][days:][revoke:][format:][apply]', '',
                                   array( 'list' => 'list the keys (the default)',
                                          'create' => 'make a key (with --for)',
                                          'for' => 'the owner of the new key (a login)',
                                          'name' => 'the name of the new key (default: GraphQL)',
                                          'scope' => 'the scopes of the new key, comma separated: graphql:read (the default), graphql:write',
                                          'days' => 'the lifetime of the new key in days (rest.ini [ApiKeySettings] ExpiryChoices[]; default 90)',
                                          'revoke' => 'revoke the key of this prefix (expk_...) or id',
                                          'format' => 'text (the default) or json (with --list)',
                                          'apply' => 'write; without it nothing is changed' ) );
        $format = isset( $options['format'] ) && $options['format'] ? (string)$options['format'] : 'text';
        if ( $format !== 'text' && $format !== 'json' )
            $this->usage( "--format must be text or json: $format" );
        $apply = !empty( $options['apply'] );
        $out = $this->cli();
        if ( !\expApiKey::enabled() )
            $this->usage( 'personal API keys are switched off (rest.ini [ApiKeySettings] ApiKeys)' );

        if ( !empty( $options['create'] ) )
        {
            $owner = !empty( $options['for'] ) ? \eZUser::fetchByName( (string)$options['for'] ) : null;
            if ( !$owner instanceof \eZUser )
                $this->usage( '--create needs --for=<login> of an existing user' );
            $scopes = array();
            try
            {
                $scopes = \expGraphQLSetup::keyScopes( Expgraphqlenable::listOption( $options, 'scope' ), false );
            }
            catch ( \expGraphQLError $e )
            {
                $this->usage( $e->getMessage() );
            }
            $denied = array_diff( $scopes, \expApiKey::scopesForUser( $owner ) );
            if ( $denied )
                $this->usage( 'the user ' . $options['for'] . ' may not have a key with ' . implode( ', ', $denied )
                              . ' (the policies its scopes need, and the Scope limitation of apikey/create)' );
            $expires = self::expiry( isset( $options['days'] ) && $options['days'] !== false ? (string)$options['days'] : '90' );
            if ( $expires === false )
                $this->usage( '--days must be one of ' . implode( ', ', \expApiKey::expiryChoices() ) );
            $name = isset( $options['name'] ) && $options['name'] ? (string)$options['name'] : 'GraphQL';
            if ( !$apply )
            {
                $out->output( 'DRY RUN would make the key "' . $name . '" for ' . $options['for'] . ' with ' . implode( ' ', $scopes )
                              . ( $expires ? ', valid until ' . date( 'Y-m-d', $expires ) : ', never expiring' ) . '; run again with --apply' );
                $this->shutdown( self::EXIT_OK );
            }
            $made = self::make( $owner, $name, $scopes, $expires );
            if ( $made === null )
            {
                $out->error( 'FAIL the key could not be made' );
                $this->shutdown( self::EXIT_ERRORS );
            }
            $out->output( 'PASS made the key ' . $made['prefix'] . ' "' . $name . '" for ' . $options['for'] . ' (' . implode( ' ', $scopes )
                          . "). The token, shown this once:\n\n    " . $made['token'] . "\n\nSend it as: Authorization: Bearer <token>" );
            $this->shutdown( self::EXIT_OK );
        }

        if ( isset( $options['revoke'] ) && $options['revoke'] !== false && $options['revoke'] !== '' )
        {
            $id = (string)$options['revoke'];
            $key = ctype_digit( $id ) ? \expApiKey::fetch( (int)$id ) : \expApiKey::fetchByPrefix( $id );
            if ( !$key instanceof \expApiKey || !\expGraphQLSetup::isGraphQLKey( $key->scopeList() ) )
                $this->usage( "no key $id with a scope of the GraphQL API" );
            if ( $key->attribute( 'revoked' ) )
            {
                $out->output( "PASS $id is revoked already" );
                $this->shutdown( self::EXIT_OK );
            }
            if ( !$apply )
            {
                $out->output( 'DRY RUN would revoke ' . $key->attribute( 'key_prefix' ) . ' "' . $key->attribute( 'name' ) . '" of ' . $key->ownerLogin() . '; run again with --apply' );
                $this->shutdown( self::EXIT_OK );
            }
            $by = \eZUser::currentUser();
            if ( !$key->revoke( (int)$by->attribute( 'contentobject_id' ) ) )
            {
                $out->error( "FAIL $id could not be revoked" );
                $this->shutdown( self::EXIT_ERRORS );
            }
            if ( class_exists( 'expAudit' ) )
                \expAudit::event( 'access.apikey.revoke', array( 'object' => $key->auditObject(), 'before' => array( 'status' => 'active' ),
                                                                  'after' => array( 'status' => 'revoked', 'by' => 'console' ) ) );
            $out->output( 'PASS revoked ' . $key->attribute( 'key_prefix' ) . '; requests with it are refused from now on' );
            $this->shutdown( self::EXIT_OK );
        }

        $keys = \expGraphQLSetup::keys( 1000 );
        if ( $format === 'json' )
            $out->output( json_encode( array( 'schema' => self::SCHEMA, 'keys' => $keys ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        else
        {
            foreach ( $keys as $key )
                $out->output( sprintf( '%-18s %-8s %-28s %-16s %-28s %s', $key['prefix'], $key['status'], mb_substr( $key['name'], 0, 28 ), $key['owner'],
                                       implode( ' ', $key['scopes'] ), $key['lastUsed'] ? 'used ' . date( 'Y-m-d', $key['lastUsed'] ) : 'never used' ) );
            $out->output( 'PASS ' . count( $keys ) . ' keys with a scope of the GraphQL API' );
        }
        $this->shutdown( self::EXIT_OK );
    }

    /**
     * @param string $days
     * @return int|false the expiry of a key of this lifetime (0: never), false when it is not one of ExpiryChoices[]
     */
    public static function expiry( $days )
    {
        if ( !ctype_digit( (string)$days ) || !in_array( (int)$days, \expApiKey::expiryChoices(), true ) )
            return false;
        return \expApiKey::expiryFor( (string)$days, time(), (int)\expApiKey::setting( 'MaxExpiryDays', 365 ) );
    }

    /**
     * Makes a key and records it in the audit.
     *
     * @param \eZUser $owner
     * @param string $name
     * @param string[] $scopes checked already
     * @param int $expires
     * @return array|null prefix, token
     */
    public static function make( \eZUser $owner, $name, array $scopes, $expires )
    {
        try
        {
            $by = \eZUser::currentUser();
            $made = \expApiKey::create( (int)$owner->attribute( 'contentobject_id' ), $name, $scopes, (int)$expires, (int)$by->attribute( 'contentobject_id' ) );
        }
        catch ( \Exception $e )
        {
            \eZDebug::writeError( 'The API key could not be made: ' . $e->getMessage(), __METHOD__ );
            return null;
        }
        if ( class_exists( 'expAudit' ) )
            \expAudit::event( 'access.apikey.create', array( 'object' => $made['key']->auditObject(),
                                                              'after' => array( 'scopes' => $scopes, 'expires' => (int)$expires, 'by' => 'console' ) ) );
        return array( 'prefix' => (string)$made['key']->attribute( 'key_prefix' ), 'token' => (string)$made['token'] );
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
