<?php
/**
 * Switching the GraphQL API on and off, and what Setup > GraphQL and exp:graphql:status show: the settings changes
 * of exp:graphql:enable and exp:graphql:disable (and of the buttons of Setup > GraphQL) as a plan of INI changes,
 * written line by line through expIniEditor (a backup first, the audit's system.setting.write, the managed
 * configuration's refusal), then the INI caches cleared and the response cache expired. A plan is shown as a diff
 * before anything is written (the console's dry run).
 *
 *   graphql.ini [GraphQLSettings] Enabled, SiteAccessList[]   settings/override (it wins over a siteaccess's file)
 *   graphql.ini [GraphQLSettings] MutationsEnabled             settings/override, or each siteaccess's file
 *   rest.ini [CorsSettings] Cors, AllowedOrigins[]             settings/override (an origin for a front end)
 *
 * Needs no database for the plan and its diff; the keys need one.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLSetup
{
    /** The scopes of a personal API key for the GraphQL API. */
    const KEY_SCOPES = array( 'graphql:read', 'graphql:write' );

    /**
     * The changes that switch the API on. settings/override wins over the settings of a siteaccess, so where the API
     * answers is decided there: Enabled=enabled, and SiteAccessList[] (empty: every siteaccess). The siteaccesses
     * named are added to the list, unless the API answers everywhere already; none named makes it answer everywhere.
     * The mutations come on with the API (MutationsEnabled is enabled by default); asked for, MutationsEnabled=enabled
     * is written too (mutationChanges()), where exp:graphql:disable --mutations switched them off. CORS for the
     * origins of a front end.
     *
     * @param string[] $siteAccesses empty: every siteaccess
     * @param string[] $origins https://shop.example.com
     * @param bool $mutations write MutationsEnabled=enabled too
     * @return array[] the changes (scope, file, op, block, variable, value)
     * @throws expGraphQLError BAD_USER_INPUT for an origin that is not one
     */
    public static function enableChanges( array $siteAccesses, array $origins, $mutations )
    {
        $changes = array( self::change( 'global', 'graphql', 'set', 'GraphQLSettings', 'Enabled', 'enabled' ) );
        $wasOn = self::globalValue( 'graphql', 'GraphQLSettings', 'Enabled' ) === 'enabled';
        $list = self::globalList( 'graphql', 'GraphQLSettings', 'SiteAccessList' );
        if ( !$siteAccesses )
        {
            if ( $list )
                $changes[] = self::change( 'global', 'graphql', 'clear', 'GraphQLSettings', 'SiteAccessList', '' );
        }
        else if ( !( $wasOn && !$list ) )
        {
            // switched off until now: only the siteaccesses named; answering in some: those named too
            foreach ( $siteAccesses as $siteAccess )
                $changes[] = self::change( 'global', 'graphql', 'add', 'GraphQLSettings', 'SiteAccessList', $siteAccess );
        }
        if ( $mutations )
            $changes = array_merge( $changes, self::mutationChanges( $siteAccesses, true ) );
        if ( $origins )
        {
            $changes[] = self::change( 'global', 'rest', 'set', 'CorsSettings', 'Cors', 'enabled' );
            foreach ( $origins as $origin )
            {
                if ( !self::isOrigin( $origin ) )
                    throw new expGraphQLError( expGraphQLMessage::tr( 'Not an origin (https://host or https://host:port): %origin', array( '%origin' => $origin ) ),
                                               expGraphQLError::BAD_USER_INPUT );
                $changes[] = self::change( 'global', 'rest', 'add', 'CorsSettings', 'AllowedOrigins', rtrim( $origin, '/' ) );
            }
        }
        return $changes;
    }

    /**
     * The changes that switch the API (or only its mutations) off, for every siteaccess or for those named: the API in
     * settings/override (Enabled=disabled, or the siteaccesses named taken out of SiteAccessList[]; a list that would
     * become empty switches it off instead, since an empty list means every siteaccess).
     *
     * @param string[] $siteAccesses empty: every siteaccess
     * @param bool $mutationsOnly switch the mutations off, leave the queries on
     * @param string[]|null $known every siteaccess of the installation (null: expIniEditor::knownSiteAccesses())
     * @return array[]
     */
    public static function disableChanges( array $siteAccesses, $mutationsOnly, $known = null )
    {
        if ( $mutationsOnly )
            return self::mutationChanges( $siteAccesses, false, $known );
        $off = array( self::change( 'global', 'graphql', 'set', 'GraphQLSettings', 'Enabled', 'disabled' ) );
        if ( !$siteAccesses )
            return $off;
        if ( self::globalValue( 'graphql', 'GraphQLSettings', 'Enabled' ) !== 'enabled' )
            return array(); // off everywhere already
        $list = self::globalList( 'graphql', 'GraphQLSettings', 'SiteAccessList' );
        if ( !$list )
            $list = $known !== null ? $known : expIniEditor::knownSiteAccesses();
        $remaining = array_values( array_diff( $list, $siteAccesses ) );
        if ( !$remaining )
            return $off;
        $changes = array( self::change( 'global', 'graphql', 'clear', 'GraphQLSettings', 'SiteAccessList', '' ) );
        foreach ( $remaining as $siteAccess )
            $changes[] = self::change( 'global', 'graphql', 'add', 'GraphQLSettings', 'SiteAccessList', $siteAccess );
        return $changes;
    }

    /**
     * The changes that switch the mutations on or off. For every siteaccess: MutationsEnabled in settings/override.
     * For those named: MutationsEnabled in each one's graphql.ini.append.php; a value in settings/override would win
     * over it, so one that says otherwise is moved into the files of the other siteaccesses first.
     *
     * @param string[] $siteAccesses empty: every siteaccess
     * @param bool $on
     * @param string[]|null $known every siteaccess of the installation (null: expIniEditor::knownSiteAccesses())
     * @return array[]
     */
    public static function mutationChanges( array $siteAccesses, $on, $known = null )
    {
        $value = $on ? 'enabled' : 'disabled';
        if ( !$siteAccesses )
            return array( self::change( 'global', 'graphql', 'set', 'GraphQLSettings', 'MutationsEnabled', $value ) );
        $changes = array();
        $global = self::globalValue( 'graphql', 'GraphQLSettings', 'MutationsEnabled' );
        if ( $global !== null )
        {
            if ( $global === $value )
                return array(); // so in every siteaccess already
            $changes[] = self::change( 'global', 'graphql', 'remove', 'GraphQLSettings', 'MutationsEnabled', '' );
            foreach ( $known !== null ? $known : expIniEditor::knownSiteAccesses() as $siteAccess )
            {
                if ( !in_array( $siteAccess, $siteAccesses, true ) )
                    $changes[] = self::change( "siteaccess:$siteAccess", 'graphql', 'set', 'GraphQLSettings', 'MutationsEnabled', (string)$global );
            }
        }
        foreach ( $siteAccesses as $siteAccess )
            $changes[] = self::change( "siteaccess:$siteAccess", 'graphql', 'set', 'GraphQLSettings', 'MutationsEnabled', $value );
        return $changes;
    }

    /**
     * Writes (or only shows, $dryRun) a plan of changes, a file at a time.
     *
     * @param array[] $changes
     * @param bool $dryRun
     * @return array files (path, diff, changed, error), written (how many files), ok
     */
    public static function apply( array $changes, $dryRun )
    {
        $editors = array();
        $files = array();
        $ok = true;
        foreach ( $changes as $change )
        {
            $key = $change['scope'] . '|' . $change['file'];
            try
            {
                if ( !isset( $editors[$key] ) )
                    $editors[$key] = new expIniEditor( expIniEditor::scope( $change['scope'] ), $change['file'] );
                if ( $change['op'] === 'add' )
                    $editors[$key]->add( $change['block'], $change['variable'], $change['value'] );
                else if ( $change['op'] === 'clear' )
                    $editors[$key]->clearArray( $change['block'], $change['variable'] );
                else if ( $change['op'] === 'remove' )
                    $editors[$key]->remove( $change['block'], $change['variable'], $change['value'] !== '' ? $change['value'] : null );
                else if ( $change['op'] === 'removeBlock' )
                    $editors[$key]->removeBlock( $change['block'] );
                else
                    $editors[$key]->set( $change['block'], $change['variable'], $change['value'] );
            }
            catch ( Exception $e )
            {
                $files[$key] = array( 'path' => $change['scope'] . ' ' . $change['file'] . '.ini', 'diff' => '', 'changed' => false, 'error' => $e->getMessage() );
                $ok = false;
            }
        }
        $written = 0;
        foreach ( $editors as $key => $editor )
        {
            if ( isset( $files[$key] ) )
                continue;
            $file = array( 'path' => $editor->relativePath(), 'diff' => $editor->diff(), 'changed' => $editor->hasChanges(), 'error' => null );
            if ( !$dryRun && $editor->hasChanges() )
            {
                try
                {
                    $editor->save();
                    $written++;
                }
                catch ( Exception $e )
                {
                    $file['error'] = $e->getMessage();
                    $ok = false;
                }
            }
            $files[$key] = $file;
        }
        // the caches of this installation, when its own files were written (not those of another root: tests)
        if ( $written > 0 && expIniEditor::isRealRoot() )
        {
            if ( class_exists( 'expSettingsPage' ) )
                expSettingsPage::clearIniCache();
            expGraphQLResponseCache::expireIfAvailable();
        }
        return array( 'files' => array_values( $files ), 'written' => $written, 'ok' => $ok );
    }

    /**
     * @param string $origin
     * @return bool scheme://host[:port], nothing else (CORS compares origins exactly)
     */
    public static function isOrigin( $origin )
    {
        return (bool)preg_match( '#^https?://[a-z0-9]([a-z0-9.-]*[a-z0-9])?(:\d{1,5})?/?$#i', (string)$origin );
    }

    /**
     * @param string[] $wanted scopes asked for (empty: graphql:read, and graphql:write with $mutations)
     * @param bool $mutations
     * @return string[] the scopes of a key for the GraphQL API
     * @throws expGraphQLError BAD_USER_INPUT for a scope that is not a GraphQL one
     */
    public static function keyScopes( array $wanted, $mutations )
    {
        $wanted = array_values( array_unique( array_filter( array_map( 'trim', $wanted ), 'strlen' ) ) );
        if ( !$wanted )
            $wanted = $mutations ? self::KEY_SCOPES : array( 'graphql:read' );
        foreach ( $wanted as $scope )
        {
            if ( !in_array( $scope, self::KEY_SCOPES, true ) )
                throw new expGraphQLError( expGraphQLMessage::tr( 'Not a scope of the GraphQL API: %scope (graphql:read, graphql:write)', array( '%scope' => $scope ) ),
                                           expGraphQLError::BAD_USER_INPUT );
        }
        // a key that writes also reads: the route needs graphql:read
        if ( in_array( 'graphql:write', $wanted, true ) && !in_array( 'graphql:read', $wanted, true ) )
            array_unshift( $wanted, 'graphql:read' );
        return array_values( array_intersect( self::KEY_SCOPES, $wanted ) );
    }

    /**
     * @param string[] $scopes of a key
     * @return bool it has a scope of the GraphQL API
     */
    public static function isGraphQLKey( array $scopes )
    {
        return (bool)array_intersect( self::KEY_SCOPES, $scopes );
    }

    /**
     * The personal API keys that have a scope of the GraphQL API.
     *
     * @param int $limit
     * @return array[] id, prefix, name, owner (login), scopes, status, created, expires, lastUsed
     */
    public static function keys( $limit = 200 )
    {
        if ( !class_exists( 'expApiKey' ) )
            return array();
        $keys = array();
        try
        {
            foreach ( (array)expApiKey::fetchList( array(), 0, (int)$limit ) as $key )
            {
                if ( !$key instanceof expApiKey || !self::isGraphQLKey( $key->scopeList() ) )
                    continue;
                $keys[] = array( 'id' => (int)$key->attribute( 'id' ), 'prefix' => (string)$key->attribute( 'key_prefix' ),
                                 'name' => (string)$key->attribute( 'name' ), 'owner' => (string)$key->ownerLogin(),
                                 'scopes' => array_values( array_intersect( $key->scopeList(), self::KEY_SCOPES ) ),
                                 'status' => (string)$key->status(), 'created' => (int)$key->attribute( 'created' ),
                                 'expires' => (int)$key->attribute( 'expires' ), 'lastUsed' => (int)$key->attribute( 'last_used' ) );
            }
        }
        catch ( Exception $e )
        {
            eZDebug::writeWarning( 'The API keys could not be read: ' . $e->getMessage(), __METHOD__ );
        }
        return $keys;
    }

    /**
     * Whether the API and its mutations answer in each siteaccess, from the settings of that siteaccess.
     *
     * @param string[]|null $siteAccesses null: every siteaccess of the installation
     * @return array[] name, enabled, mutations
     */
    public static function siteAccessStates( $siteAccesses = null )
    {
        if ( $siteAccesses === null )
            $siteAccesses = expIniEditor::knownSiteAccesses();
        $states = array();
        foreach ( $siteAccesses as $siteAccess )
        {
            $enabled = expIniEditor::effectiveValue( 'graphql.ini', 'GraphQLSettings', 'Enabled', $siteAccess ) === 'enabled';
            $list = array_values( array_filter( (array)expIniEditor::effectiveValue( 'graphql.ini', 'GraphQLSettings', 'SiteAccessList', $siteAccess ), 'strlen' ) );
            $states[] = array( 'name' => (string)$siteAccess,
                               'enabled' => $enabled && ( !$list || in_array( $siteAccess, $list, true ) ),
                               // MutationsEnabled is enabled unless a file says otherwise (settings/graphql.ini ships it on)
                               'mutations' => ( expIniEditor::effectiveValue( 'graphql.ini', 'GraphQLSettings', 'MutationsEnabled', $siteAccess ) ?? 'enabled' ) === 'enabled' );
        }
        return $states;
    }

    /**
     * What exp:graphql:status and Setup > GraphQL show of the current siteaccess.
     *
     * @return array siteaccess, enabled, mutations, siteaccessList, endpoint, library, libraryVersion, libraryState
     *               (available, missing, incompatible), libraryProblem (null when available), limits,
     *               rateLimits, persisted, responseCache, introspection, naming, schemaHash, classes, fields,
     *               extensions, unmappedDatatypes
     */
    public static function status()
    {
        $library = expGraphQL::loadLibrary();
        $status = array( 'siteaccess' => expGraphQL::currentSiteAccess(),
                         'enabled' => expGraphQL::isEnabled(),
                         'mutations' => expGraphQLMutations::enabled(),
                         'siteaccessList' => expGraphQL::listSetting( 'SiteAccessList' ),
                         'endpoint' => '/api/graphql',
                         'library' => $library,
                         'libraryVersion' => $library ? expGraphQL::libraryVersion() : null,
                         'libraryState' => expGraphQL::libraryState(),
                         'libraryProblem' => expGraphQL::libraryProblem(),
                         'limits' => array( 'maxDepth' => expGraphQL::intSetting( 'MaxDepth', 12 ), 'maxComplexity' => expGraphQL::intSetting( 'MaxComplexity', 1000 ),
                                            'maxListLimit' => expGraphQL::intSetting( 'MaxListLimit', 100 ), 'defaultListLimit' => expGraphQL::intSetting( 'DefaultListLimit', 25 ),
                                            'maxQueryLength' => expGraphQL::intSetting( 'MaxQueryLength', expGraphQLServer::MAX_QUERY_BYTES ),
                                            'maxUploadSize' => expGraphQLUpload::maxBytes() ),
                         'rateLimits' => array( 'user' => (int)expGraphQL::setting( 'RateLimit', 600 ), 'anonymous' => (int)expGraphQL::setting( 'RateLimitAnonymous', 120 ),
                                                'window' => expGraphQL::intSetting( 'RateLimitWindow', 60 ) ),
                         'persisted' => array( 'enabled' => expGraphQL::setting( 'PersistedQueries', 'enabled' ) === 'enabled',
                                               'automatic' => (string)expGraphQL::setting( 'AutomaticPersistedQueries', 'signed-in' ),
                                               'only' => (string)expGraphQL::setting( 'PersistedQueriesOnly', 'disabled' ),
                                               'stored' => expGraphQLPersistedQueries::fromSettings()->count() ),
                         'responseCache' => array( 'enabled' => expGraphQL::setting( 'ResponseCache', 'enabled' ) === 'enabled',
                                                   'ttl' => (int)expGraphQL::setting( 'ResponseCacheTtl', 86400 ) ),
                         'introspection' => (string)expGraphQL::setting( 'Introspection', 'signed-in' ),
                         'naming' => expGraphQLNaming::fromSettings()->style(),
                         'schemaHash' => null, 'classes' => 0, 'fields' => 0, 'extensions' => array(), 'unmappedDatatypes' => array() );
        if ( $library )
        {
            $summary = expGraphQLSchemaBuilder::fromSettings()->summary();
            $status['schemaHash'] = $summary['schemaHash'];
            $status['extensions'] = $summary['extensions'];
            $status['classes'] = count( $summary['classes'] );
            $unmapped = array();
            foreach ( $summary['classes'] as $class )
            {
                $status['fields'] += count( $class['fields'] );
                foreach ( $class['fields'] as $field )
                    if ( !$field['mapped'] )
                        $unmapped[$field['datatype']] = true;
            }
            ksort( $unmapped );
            $status['unmappedDatatypes'] = array_keys( $unmapped );
        }
        return $status;
    }

    /**
     * @param string $scope
     * @param string $file
     * @param string $op set or add
     * @param string $block
     * @param string $variable
     * @param string $value
     * @return array
     */
    protected static function change( $scope, $file, $op, $block, $variable, $value )
    {
        return array( 'scope' => $scope, 'file' => $file, 'op' => $op, 'block' => $block, 'variable' => $variable, 'value' => (string)$value );
    }

    /**
     * @return mixed the value settings/override gives a setting (null: none)
     */
    protected static function globalValue( $file, $block, $variable )
    {
        return self::scopeValue( 'global', $file, $block, $variable );
    }

    /**
     * @return string[] the values settings/override gives an array setting, without empty ones
     */
    protected static function globalList( $file, $block, $variable )
    {
        return array_values( array_filter( array_map( 'strval', (array)self::globalValue( $file, $block, $variable ) ), 'strlen' ) );
    }

    /**
     * @return mixed the value one scope's file gives a setting (null: none, or no such scope)
     */
    protected static function scopeValue( $scope, $file, $block, $variable )
    {
        try
        {
            return ( new expIniEditor( expIniEditor::scope( $scope ), $file ) )->get( $block, $variable );
        }
        catch ( Exception $e )
        {
            return null;
        }
    }
}

?>
