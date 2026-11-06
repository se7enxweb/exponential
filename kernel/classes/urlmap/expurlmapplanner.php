<?php
/**
 * File containing the expUrlMapPlanner class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Plans the import of URL map rows (expUrlMapRow): where each row goes, whether its target exists, what is there
 * already, and so what the import would do (expUrlMapPlan). Nothing is written.
 *
 *   $plan = ( new expUrlMapPlanner() )->plan( expUrlMapFormats::readFile( 'old-site.csv' ),
 *                                             array( 'siteaccess' => 'site', 'on_conflict' => 'skip' ) );
 *
 * Options: siteaccess (for bare paths; default the installation's DefaultAccess), language (default the
 * siteaccess's content language), on_conflict (skip or overwrite), hosts (host => siteaccess: old hosts that are not
 * (yet) this installation's), source_name and format (recorded with the batch).
 *
 * Where a row goes:
 * - wildcard-forward, wildcard-forward-temporary, wildcard-direct: a URL wildcard (ezurlwildcard; wildcards apply in
 *   every siteaccess). Temporary is type 3 (302); 303 and 307 are answered as 302, 308 as 301.
 * - redirect with 301 to a page of this site, without a query: a global URL alias that redirects (as the URL
 *   translator makes it); alias: one that does not redirect.
 * - every other redirect (a query in the old address, a temporary status, 308, a target with a query, a target on
 *   another site): the table exp_url_redirect, checked on the full request before the aliases.
 *
 * The host of a full old address picks the siteaccess through the installation's siteaccess matching (or the option
 * hosts); a host that is not this installation's is unknown-host. The states: new, unchanged (the same entry is
 * there), changed (another entry of the same store is at the old address: updated with on_conflict overwrite),
 * duplicate (an earlier row of the file has the same old address), conflict (the old address is a page of this
 * site, a module address, or the front page: never replaced), broken-target (the target does not exist),
 * unknown-host, not-importable (the row's own problems, an unknown siteaccess or language, an alias with a query or
 * to another site).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUrlMapPlanner
{
    /** @var expUrlMapEnvironment */
    protected $environment;

    /**
     * @param expUrlMapEnvironment|null $environment null for the installation's (expUrlMapKernelEnvironment)
     */
    public function __construct( ?expUrlMapEnvironment $environment = null )
    {
        $this->environment = $environment ?: new expUrlMapKernelEnvironment();
    }

    /** @return expUrlMapEnvironment */
    public function environment()
    {
        return $this->environment;
    }

    /**
     * @param expUrlMapRow[] $rows
     * @param array $options
     * @return expUrlMapPlan
     */
    public function plan( array $rows, array $options = array() )
    {
        $options = self::options( $options, $this->environment );
        $items = array();
        $seen = array();
        foreach ( array_values( $rows ) as $index => $row )
        {
            $item = $this->planRow( $row, $index, $options );
            if ( $item['key'] !== '' && in_array( $item['state'], array( expUrlMapPlan::STATE_NEW, expUrlMapPlan::STATE_CHANGED, expUrlMapPlan::STATE_UNCHANGED ), true ) )
            {
                if ( isset( $seen[$item['key']] ) )
                {
                    $item['state'] = expUrlMapPlan::STATE_DUPLICATE;
                    $item['messages'][] = 'the same old address as ' . ( $seen[$item['key']] ? 'line ' . $seen[$item['key']] : 'an earlier row' ) . ' (the first one counts)';
                }
                else
                    $seen[$item['key']] = $item['line'];
            }
            $items[] = $item;
        }
        return new expUrlMapPlan( $items, $options );
    }

    /**
     * The options with their defaults; an unknown conflict policy is an exception.
     *
     * @param array $options
     * @param expUrlMapEnvironment $environment
     * @return array
     */
    public static function options( array $options, expUrlMapEnvironment $environment )
    {
        $options += array( 'siteaccess' => '', 'language' => '', 'on_conflict' => expUrlMapPlan::ON_CONFLICT_SKIP, 'hosts' => array(),
                           'source_name' => '', 'format' => '' );
        if ( !in_array( $options['on_conflict'], array( expUrlMapPlan::ON_CONFLICT_SKIP, expUrlMapPlan::ON_CONFLICT_OVERWRITE ), true ) )
            throw new InvalidArgumentException( 'on_conflict must be skip or overwrite, not ' . $options['on_conflict'] );
        if ( $options['siteaccess'] === '' || $options['siteaccess'] === null )
            $options['siteaccess'] = $environment->defaultSiteaccess();
        $hosts = array();
        foreach ( (array)$options['hosts'] as $host => $siteaccess )
            $hosts[strtolower( trim( (string)$host ) )] = (string)$siteaccess;
        $options['hosts'] = $hosts;
        return $options;
    }

    /**
     * @param expUrlMapRow $row
     * @param int $index
     * @param array $options
     * @return array the item (see expUrlMapPlan)
     */
    protected function planRow( expUrlMapRow $row, $index, array $options )
    {
        $item = array( 'index' => $index, 'line' => $row->line(), 'old_url' => $row->oldUrl(), 'new_url' => $row->newUrl(), 'kind' => $row->kind(),
                       'note' => $row->note(), 'state' => expUrlMapPlan::STATE_NEW, 'messages' => array(), 'store' => '', 'key' => '',
                       'siteaccess' => '', 'language' => '', 'status' => 0, 'source' => array( 'path' => $row->oldPath(), 'query' => $row->oldQuery() ),
                       'write' => null, 'existing' => null, 'decision' => 'skip', 'action' => 'none' );
        if ( !$row->isImportable() )
            return self::finish( $item, expUrlMapPlan::STATE_NOT_IMPORTABLE, $row->problems() );
        $item['kind'] = $row->effectiveKind();
        $item['status'] = $row->redirectStatus();

        // the siteaccess: the row's, the host's, or the chosen one; and the path inside it
        $path = $row->oldPath();
        $siteaccess = $row->siteaccess();
        $siteaccesses = $this->environment->siteaccesses();
        $every = $siteaccess === '*';
        if ( $every )
            $siteaccess = '';
        if ( $siteaccess !== '' && !in_array( $siteaccess, $siteaccesses, true ) )
            return self::finish( $item, expUrlMapPlan::STATE_NOT_IMPORTABLE, array( 'the siteaccess ' . $siteaccess . ' does not exist' ) );
        if ( $row->oldHost() !== '' )
        {
            $host = $row->oldHost();
            if ( isset( $options['hosts'][$host] ) )
                $hostAccess = array( 'siteaccess' => $options['hosts'][$host], 'path' => $path );
            else
                $hostAccess = $this->environment->siteaccessForHost( $host, $path );
            if ( $hostAccess === null )
                return self::finish( $item, expUrlMapPlan::STATE_UNKNOWN_HOST, array( 'the host ' . $host . ' is not one of this installation\'s; map it to a siteaccess (hosts) or point it here first' ) );
            if ( $siteaccess === '' && !$every )
                $siteaccess = $hostAccess['siteaccess'];
            $path = $hostAccess['path'];
        }
        if ( $siteaccess === '' )
            $siteaccess = (string)$options['siteaccess'];
        $item['siteaccess'] = $every ? '*' : $siteaccess;
        $item['source']['path'] = $path;
        $language = $row->language() !== '' ? $row->language() : ( (string)$options['language'] !== '' ? (string)$options['language'] : $this->environment->defaultLanguage( $siteaccess ) );
        $item['language'] = $language;
        if ( !$this->environment->languageExists( $language ) )
            return self::finish( $item, expUrlMapPlan::STATE_NOT_IMPORTABLE, array( 'the language ' . $language . ' does not exist' ) );
        $query = $row->oldQuery();

        if ( $row->isWildcard() )
            return $this->planWildcard( $item, $row, $path );
        return $this->planExact( $item, $row, $path, $query, $siteaccess, $language );
    }

    /**
     * @param array $item
     * @param expUrlMapRow $row
     * @param string $path
     * @return array
     */
    protected function planWildcard( array $item, expUrlMapRow $row, $path )
    {
        $source = ltrim( $path, '/' );
        $target = $this->target( $row->newUrl() );
        $kind = $row->effectiveKind();
        if ( $target['external'] && $kind === expUrlMapRow::KIND_WILDCARD_DIRECT )
            return self::finish( $item, expUrlMapPlan::STATE_NOT_IMPORTABLE, array( 'a wildcard (direct) shows a page of this site; ' . $row->newUrl() . ' is on another site' ) );
        $destination = $target['external'] ? $target['url'] : ltrim( $target['path'], '/' ) . ( $target['query'] !== '' ? '?' . $target['query'] : '' );
        $type = $kind === expUrlMapRow::KIND_WILDCARD_DIRECT ? eZURLWildcard::TYPE_DIRECT
              : ( $kind === expUrlMapRow::KIND_WILDCARD_FORWARD_TEMPORARY ? eZURLWildcard::TYPE_FORWARD_TEMPORARY : eZURLWildcard::TYPE_FORWARD );
        $item['store'] = expUrlMapPlan::STORE_WILDCARD;
        $item['key'] = 'wildcard|' . self::lower( $source );
        $item['write'] = array( 'source_url' => $source, 'destination_url' => $destination, 'type' => $type );
        $answered = eZURLWildcard::redirectStatusOfType( $type );
        if ( $item['status'] && $answered !== $item['status'] )
            $item['messages'][] = 'a wildcard answers with 301 or 302: ' . $item['status'] . ' is answered as ' . $answered;
        $item['status'] = $answered;
        if ( $row->siteaccess() !== '' || $row->oldHost() !== '' )
            $item['messages'][] = 'a wildcard applies in every siteaccess';
        $existing = $this->environment->existingWildcard( $source );
        $item['existing'] = $existing;
        if ( $existing === null )
            return self::finish( $item, expUrlMapPlan::STATE_NEW );
        if ( (string)$existing['destination_url'] === $destination && (int)$existing['type'] === $type )
            return self::finish( $item, expUrlMapPlan::STATE_UNCHANGED );
        return self::finish( $item, expUrlMapPlan::STATE_CHANGED, array( 'the wildcard is there with ' . $existing['destination_url'] . ' (' . eZURLWildcard::typeName( (int)$existing['type'] ) . ')' ) );
    }

    /**
     * @param array $item
     * @param expUrlMapRow $row
     * @param string $path
     * @param string $query
     * @param string $siteaccess
     * @param string $language
     * @return array
     */
    protected function planExact( array $item, expUrlMapRow $row, $path, $query, $siteaccess, $language )
    {
        $kind = $row->effectiveKind();
        $target = $this->target( $row->newUrl() );
        if ( $query === '' && strtolower( $path ) === '/index.php' )
            return self::finish( $item, expUrlMapPlan::STATE_NOT_IMPORTABLE, array( '/index.php without a query is the front controller of this site' ) );
        $resolved = null;
        if ( !$target['external'] )
        {
            $resolved = $target['node'] !== '' ? $this->environment->resolveTarget( $target['node'], $siteaccess, $language )
                                                 : $this->resolvePath( $target['path'], $item['siteaccess'] === '*' ? '' : $siteaccess, $language );
            if ( $resolved === null )
                return self::finish( $item, expUrlMapPlan::STATE_BROKEN_TARGET, array( 'the target ' . $row->newUrl() . ' does not exist on this site' ) );
        }
        if ( $kind === expUrlMapRow::KIND_ALIAS )
        {
            if ( $query !== '' )
                return self::finish( $item, expUrlMapPlan::STATE_NOT_IMPORTABLE, array( 'an alias cannot have a query; use the kind redirect' ) );
            if ( $target['external'] || $target['query'] !== '' )
                return self::finish( $item, expUrlMapPlan::STATE_NOT_IMPORTABLE, array( 'an alias shows a page of this site at the old address; its target cannot be on another site or have a query' ) );
        }
        $isAlias = $kind === expUrlMapRow::KIND_ALIAS
                   || ( $kind === expUrlMapRow::KIND_REDIRECT && $item['status'] === 301 && $query === '' && !$target['external'] && $target['query'] === '' );

        // where the siteaccess looks a URL alias of the old address up (below its PathPrefix)
        $aliasPath = $item['siteaccess'] === '*' ? $path : $this->environment->aliasPath( $siteaccess, $path );

        // an old address that is this site's own page, a module address or the front page is never replaced
        if ( $query === '' )
        {
            if ( trim( $path, '/' ) === '' )
                return self::finish( $item, expUrlMapPlan::STATE_CONFLICT, array( 'the old address is the front page of this site' ) );
            $alias = $this->environment->existingAlias( $aliasPath );
            if ( $alias !== null && !empty( $alias['page'] ) )
                return self::finish( $item, expUrlMapPlan::STATE_CONFLICT, array( 'the old address is the address of a page of this site (' . $alias['action'] . ')' ), $alias );
            if ( $this->environment->isSystemPath( $path ) )
                return self::finish( $item, expUrlMapPlan::STATE_CONFLICT, array( 'the old address is an address of the system (a module) on this site' ) );
        }

        if ( $isAlias )
        {
            $redirects = $kind !== expUrlMapRow::KIND_ALIAS;
            $item['store'] = expUrlMapPlan::STORE_ALIAS;
            $item['key'] = 'alias|' . self::lower( $aliasPath );
            $item['write'] = array( 'path' => $aliasPath, 'action' => $resolved['action'], 'link' => $resolved['link'], 'redirects' => $redirects, 'language' => $language );
            if ( !$redirects )
                $item['status'] = 0;
            $existing = $this->environment->existingAlias( $aliasPath );
            $item['existing'] = $existing;
            if ( $existing === null )
                return self::finish( $item, expUrlMapPlan::STATE_NEW );
            if ( (string)$existing['action'] === $resolved['action'] && (bool)(int)$existing['alias_redirects'] === $redirects )
                return self::finish( $item, expUrlMapPlan::STATE_UNCHANGED );
            return self::finish( $item, expUrlMapPlan::STATE_CHANGED, array( 'the URL alias is there and leads to ' . $existing['action'] . ( (int)$existing['alias_redirects'] ? ' (redirecting)' : '' ) ) );
        }

        $value = $target['external'] ? $target['url']
               : ( $target['node'] !== '' ? $target['node'] : $target['path'] . ( $target['query'] !== '' ? '?' . $target['query'] : '' ) );
        $canonical = expUrlRedirect::canonicalQuery( $query );
        // a redirect of every siteaccess (siteaccess *) is stored without one
        if ( $item['siteaccess'] === '*' )
            $siteaccess = '';
        $item['store'] = expUrlMapPlan::STORE_REDIRECT;
        $item['key'] = 'redirect|' . $siteaccess . '|' . expUrlRedirect::sourceKey( $path, $query );
        $item['source']['query'] = $canonical;
        $item['write'] = array( 'path' => expUrlRedirect::normalizePath( $path ), 'query' => $canonical, 'target' => $value, 'status' => $item['status'],
                                'siteaccess' => $siteaccess, 'language' => $language );
        if ( $query !== '' )
            $item['messages'][] = 'redirected with its query (' . $canonical . '), before the URL aliases';
        $existing = $this->environment->existingRedirect( $siteaccess, $path, $query );
        $item['existing'] = $existing;
        if ( $existing === null )
            return self::finish( $item, expUrlMapPlan::STATE_NEW );
        if ( (string)$existing['target'] === $value && (int)$existing['status'] === $item['status'] )
            return self::finish( $item, expUrlMapPlan::STATE_UNCHANGED );
        return self::finish( $item, expUrlMapPlan::STATE_CHANGED, array( 'the redirect is there to ' . $existing['target'] . ' (' . $existing['status'] . ')' ) );
    }

    /**
     * A target path as the siteaccess serves it: a visitor's address (/recipes) is looked up below the siteaccess's
     * PathPrefix first (/fit-healthy/recipes; not when its first element is one of PathPrefixExclude[]), then as it
     * is, so a path written with the prefix, or one outside it, resolves too.
     *
     * @param string $path
     * @param string $siteaccess '' for a row of every siteaccess (no prefix)
     * @param string $language
     * @return array|null as expUrlMapEnvironment::resolveTarget()
     */
    protected function resolvePath( $path, $siteaccess, $language )
    {
        if ( $siteaccess !== '' && trim( (string)$path, '/' ) !== '' )
        {
            $prefixed = $this->environment->aliasPath( $siteaccess, $path );
            if ( $prefixed !== $path )
            {
                $resolved = $this->environment->resolveTarget( $prefixed, $siteaccess, $language );
                if ( $resolved !== null )
                    return $resolved;
            }
        }
        return $this->environment->resolveTarget( $path, $siteaccess, $language );
    }

    /**
     * A row's target split: a node reference, a path and query of this site, or a full URL of another site (a full
     * URL whose host is this installation's is a path of this site).
     *
     * @param string $newUrl
     * @return array node (node:<id> or ''), path, query, external (bool), url
     */
    protected function target( $newUrl )
    {
        $out = array( 'node' => '', 'path' => '', 'query' => '', 'external' => false, 'url' => (string)$newUrl );
        if ( preg_match( '#^node:\d+$#', $newUrl ) )
        {
            $out['node'] = $newUrl;
            return $out;
        }
        $parts = expUrlMapRow::parseUrl( $newUrl );
        $out['path'] = $parts['path'];
        $out['query'] = $parts['query'];
        if ( $parts['host'] !== '' )
        {
            $access = strpos( $parts['path'], '{' ) === false ? $this->environment->siteaccessForHost( $parts['host'], $parts['path'] ) : null;
            if ( $access === null )
                $out['external'] = true;
            else
                $out['path'] = $access['path'];
        }
        return $out;
    }

    /**
     * @param array $item
     * @param string $state
     * @param string[] $messages
     * @param array|null $existing
     * @return array
     */
    protected static function finish( array $item, $state, array $messages = array(), $existing = null )
    {
        $item['state'] = $state;
        foreach ( $messages as $message )
            $item['messages'][] = (string)$message;
        if ( $existing !== null )
            $item['existing'] = $existing;
        if ( in_array( $state, array( expUrlMapPlan::STATE_CONFLICT, expUrlMapPlan::STATE_BROKEN_TARGET, expUrlMapPlan::STATE_UNKNOWN_HOST,
                                      expUrlMapPlan::STATE_NOT_IMPORTABLE ), true ) )
        {
            $item['store'] = '';
            $item['write'] = null;
        }
        return $item;
    }

    /**
     * @param string $text
     * @return string
     */
    protected static function lower( $text )
    {
        return function_exists( 'mb_strtolower' ) ? mb_strtolower( (string)$text, 'UTF-8' ) : strtolower( (string)$text );
    }
}
