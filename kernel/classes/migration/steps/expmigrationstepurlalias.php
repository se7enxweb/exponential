<?php
/**
 * File containing the expMigrationStepUrlAlias class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "url_alias": the custom addresses of the administration's URL alias pages. An address leads to a node
 * (content/urlalias), to a module URL or another address (content/urlalias_global), or is a wildcard that sends a whole
 * group of addresses elsewhere (content/urlalias_wildcard).
 *
 *   - type: url_alias
 *     mode: ensure                     create, ensure, delete
 *     path: about-us                   the address, from the root
 *     node: "path:company/about"       ... leads to this node, or
 *     destination: content/search      ... to a module URL or another address
 *     language: eng-GB
 *     redirect: true                   the address redirects (301) to the node's own address (default), or shows it
 *   - type: url_alias
 *     mode: ensure
 *     wildcard: "old-news/*"
 *     destination: "news/{1}"
 *     redirect: true                   forward (301, the default), or show the destination at the old address
 *   - type: url_alias
 *     mode: delete
 *     node: "path:company/about"
 *     which: all                       every custom address of the node (without node: every global one)
 *
 * The addresses are written with eZURLAliasML::storePath() and removed with eZURLAliasML::removeSingleEntry(), the
 * wildcards stored as eZURLWildcard, as the administration's pages do. The address a node has from its name is not a
 * custom alias: it follows the node's name and place, and the step refuses to delete it. A create undoes itself (the
 * address or wildcard is removed again while it still leads where the step set it); ensure and delete do not.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepUrlAlias extends expMigrationStepTypeBase implements expMigrationPartlyRevertibleStepType
{
    /** @return string */
    public function name()
    {
        return 'url_alias';
    }

    /** @return array */
    public function schema()
    {
        $writing = array( 'create', 'ensure' );
        return array(
            'description' => 'Creates, ensures or deletes a custom URL alias of a node, a global URL alias or a URL wildcard',
            'modes' => array( 'create', 'ensure', 'delete' ),
            'keys' => array(
                'path' => array( 'type' => 'string',
                                 'description' => 'the address, from the root (about-us/team); each part is cleaned as the URL transformation of site.ini makes it' ),
                'node' => array( 'type' => 'identifier', 'kind' => 'node',
                                 'description' => 'the node the address leads to (create, ensure); delete: the node whose custom addresses go (which: all)' ),
                'destination' => array( 'type' => 'string', 'modes' => $writing,
                                        'description' => 'where a global address leads (a module URL as content/search, or another address); with wildcard: the destination, {1} the text of the first *' ),
                'wildcard' => array( 'type' => 'string',
                                     'description' => 'a URL wildcard instead of an alias: the pattern, * any text (old-news/*)' ),
                'language' => array( 'type' => 'string',
                                     'description' => 'the language of the address (create, ensure: default the default language of content; delete: default every language it has)' ),
                'always_available' => array( 'type' => 'bool', 'default' => false, 'modes' => $writing,
                                             'description' => 'true: the address works in every language of a siteaccess' ),
                'redirect' => array( 'type' => 'bool', 'default' => true, 'modes' => $writing,
                                     'description' => 'true (the default): the address redirects (301) to where it leads; false: it shows the page at its own address' ),
                'below_parent' => array( 'type' => 'bool', 'default' => false, 'modes' => $writing,
                                         'description' => 'true: path: is placed below the address of the node\'s parent, as the administration does by default' ),
                'which' => array( 'type' => 'string', 'values' => array( 'all', 'selected' ), 'modes' => array( 'delete' ),
                                  'description' => 'all: every custom address of node: (without node: every global address); selected: the address of path:' ),
                'missing' => array( 'type' => 'string', 'default' => 'fail', 'values' => array( 'fail', 'skip' ), 'modes' => array( 'delete' ),
                                    'description' => 'what happens when the address or wildcard is not there: fail (the default) or skip' ),
            ),
            'values' => array(
                'path' => 'the address as stored (cleaned), or the pattern of the wildcard',
                'action' => 'where it leads, as the kernel writes it (eznode:59, module:content/search), or the destination of the wildcard',
                'node_id' => 'the node it leads to, or null',
                'removed' => 'how many addresses a delete removed',
                'created' => 'true when the step made the address or wildcard',
                'changed' => 'true when the step changed something',
            ),
            'example' => "- type: url_alias\n  mode: ensure\n  path: about-us\n  node: \"path:company/about\"\n"
                       . "- type: url_alias\n  mode: ensure\n  wildcard: \"old-news/*\"\n  destination: \"news/{1}\"\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $mode = isset( $step['mode'] ) ? $step['mode'] : null;
        $errors = array();
        $has = function ( $key ) use ( $step ) {
            return isset( $step[$key] ) && trim( (string)$step[$key] ) !== '';
        };
        if ( in_array( $mode, array( 'create', 'ensure' ), true ) )
        {
            if ( $has( 'wildcard' ) )
            {
                if ( !$has( 'destination' ) )
                    $errors[] = "destination is missing: where the wildcard leads (mode $mode)";
                if ( isset( $step['node'] ) || $has( 'path' ) )
                    $errors[] = 'a wildcard has no path: and no node: (its destination: says where it leads)';
                else if ( $has( 'destination' ) && !expMigrationReferences::contains( $step['wildcard'] ) && !expMigrationReferences::contains( $step['destination'] ) )
                    $errors = array_merge( $errors, self::wildcardProblems( (string)$step['wildcard'], (string)$step['destination'] ) );
            }
            else
            {
                if ( !$has( 'path' ) )
                    $errors[] = "path is missing (mode $mode)";
                if ( isset( $step['node'] ) === $has( 'destination' ) )
                    $errors[] = 'say where the address leads by node: or by destination: (one of them)';
                if ( !empty( $step['below_parent'] ) && !isset( $step['node'] ) )
                    $errors[] = 'below_parent: places the address of a node; a global address has none';
            }
        }
        if ( $mode === 'delete' )
        {
            $which = isset( $step['which'] ) ? (string)$step['which'] : '';
            $named = ( $has( 'path' ) ? 1 : 0 ) + ( $has( 'wildcard' ) ? 1 : 0 ) + ( $which === 'all' ? 1 : 0 );
            if ( $named !== 1 )
                $errors[] = 'name what is deleted by path:, by wildcard: or by which: all (one of them)';
            if ( $which === 'selected' && !$has( 'path' ) )
                $errors[] = 'which: selected deletes the address of path:';
            if ( $has( 'wildcard' ) && isset( $step['node'] ) )
                $errors[] = 'a wildcard belongs to no node';
        }
        if ( $has( 'path' ) && !expMigrationReferences::contains( $step['path'] ) && preg_match( '#^[a-z][a-z0-9+.-]*://#i', (string)$step['path'] ) )
            $errors[] = 'path: the address without the scheme and host (about-us, not https://...)';
        if ( isset( $step['language'] ) && !expMigrationReferences::contains( $step['language'] ) )
        {
            $problem = expMigrationIdentifier::problem( 'language', $step['language'] );
            if ( $problem !== null )
                $errors[] = "language: $problem";
        }
        return $errors;
    }

    /**
     * The problems of a wildcard and its destination: each {n} of the destination needs an n-th *. Pure.
     *
     * @param string $wildcard
     * @param string $destination
     * @return string[]
     */
    protected static function wildcardProblems( $wildcard, $destination )
    {
        $stars = substr_count( $wildcard, '*' );
        $errors = array();
        if ( $stars === 0 )
            $errors[] = 'wildcard: a pattern has at least one * (old-news/*)';
        if ( preg_match_all( '/\{(\d+)\}/', $destination, $matches ) )
            foreach ( array_unique( $matches[1] ) as $number )
                if ( (int)$number < 1 || (int)$number > $stars )
                    $errors[] = "destination: {{$number}} has no * of its own in the wildcard ($stars)";
        return $errors;
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function caches( array $step )
    {
        return array( 'content', 'urlalias' );
    }

    /**
     * @param array $step
     * @return bool
     */
    public function revertible( array $step )
    {
        return isset( $step['mode'] ) && $step['mode'] === 'create';
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $mode = $step['mode'];
        if ( isset( $step['wildcard'] ) && $mode !== 'delete' )
            return array( "$mode the wildcard " . $step['wildcard'] . ' -> ' . $step['destination'] . ( $step['redirect'] ? ' (forward)' : ' (direct)' ) );
        if ( $mode === 'delete' )
        {
            if ( isset( $step['wildcard'] ) )
                return array( 'delete the wildcard ' . $step['wildcard'] );
            if ( isset( $step['path'] ) )
                return array( 'delete the address ' . $step['path'] . ( isset( $step['language'] ) ? ' in ' . $step['language'] : '' ) );
            return array( 'delete every custom address of ' . ( isset( $step['node'] ) ? 'node ' . expMigrationStepSchema::show( $step['node'] ) : 'the installation (the global ones)' ) );
        }
        $to = isset( $step['node'] ) ? 'node ' . expMigrationStepSchema::show( $step['node'] ) : $step['destination'];
        return array( "$mode the address " . $step['path'] . ( !empty( $step['below_parent'] ) ? ' (below the parent\'s address)' : '' ) . " -> $to"
                      . ( $step['redirect'] ? ', redirecting' : '' ) . ( isset( $step['language'] ) ? ' in ' . $step['language'] : '' ) );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $mode = $step['mode'];
        if ( isset( $step['wildcard'] ) )
            return $mode === 'delete' ? $this->deleteWildcard( $step ) : $this->storeWildcard( $step );
        if ( $mode === 'delete' )
            return isset( $step['path'] ) ? $this->deletePath( $step ) : $this->deleteAll( $step );
        if ( $mode === 'create' || $mode === 'ensure' )
            return $this->storeAlias( $step );
        throw new RuntimeException( "the mode $mode is not one of create, ensure, delete" );
    }

    /**
     * Removes the address or wildcard a create made, while it leads where the step set it.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        if ( !$this->revertible( $step ) )
            throw new RuntimeException( 'only a create can be undone by itself; give the migration a down:' );
        if ( isset( $step['wildcard'] ) )
        {
            $row = eZURLWildcard::fetchBySourceURL( self::wildcardText( $step['wildcard'] ), false );
            if ( !is_array( $row ) || (string)$row['destination_url'] !== self::wildcardText( $step['destination'] ) )
                return array( 'the wildcard ' . $step['wildcard'] . ' is not there as the step made it: nothing to undo' );
            $result = $this->deleteWildcard( $step );
            return $result['lines'];
        }
        $target = $this->target( $step );
        $full = $this->fullPath( $step, $target );
        $element = self::elementAt( $full );
        if ( $element === null || (string)$element->attribute( 'action' ) !== $target['action'] || self::isName( $element ) )
            return array( "the address $full does not lead to " . $target['action'] . ': nothing to undo' );
        $result = $this->deletePath( array( 'path' => $full, 'missing' => 'skip' ) + $step );
        return $result['lines'];
    }

    // ---- aliases ------------------------------------------------------------------------------------------------

    /**
     * Where a step's address leads: the action, the link (the element of the node's name, or true for a module URL)
     * and the node.
     *
     * @param array $step
     * @return array action, link, node_id, root (the element the address is placed below)
     * @throws RuntimeException
     */
    protected function target( array $step )
    {
        if ( isset( $step['node'] ) )
        {
            $nodeId = expMigrationIdentifier::resolve( 'node', $step['node'] );
            $name = self::nameElement( $nodeId );
            return array( 'action' => "eznode:$nodeId", 'link' => $name !== null ? (int)$name->attribute( 'id' ) : 0, 'node_id' => $nodeId,
                          'root' => !empty( $step['below_parent'] ) && $name !== null ? (int)$name->attribute( 'parent' ) : 0 );
        }
        $destination = trim( (string)$step['destination'], " \t\r\n\0\x0B/" );
        $action = eZURLAliasML::urlToAction( $destination );
        $link = true;
        if ( !$action )
        {
            $elements = eZURLAliasML::fetchByPath( $destination );
            if ( $elements )
            {
                $action = (string)$elements[0]->attribute( 'action' );
                $link = (int)$elements[0]->attribute( 'link' );
            }
        }
        if ( !$action )
            throw new RuntimeException( "the destination $destination is neither a module URL nor an address of this installation" );
        return array( 'action' => (string)$action, 'link' => $link, 'node_id' => eZURLAliasML::nodeIDFromAction( $action ) ?: null, 'root' => 0 );
    }

    /**
     * The address from the root: path: below the root element of the target (below_parent).
     *
     * @param array $step
     * @param array $target
     * @return string
     */
    protected function fullPath( array $step, array $target )
    {
        $path = trim( eZURLAliasML::cleanURL( (string)$step['path'] ), '/' );
        if ( !$target['root'] )
            return $path;
        $rows = eZPersistentObject::fetchObjectList( eZURLAliasML::definition(), null, array( 'id' => (int)$target['root'] ), null,
                                                     array( 'limit' => 1, 'offset' => 0 ), true );
        $prefix = is_array( $rows ) && $rows ? trim( (string)$rows[0]->getPath(), '/' ) : '';
        return $prefix === '' ? $path : "$prefix/$path";
    }

    /**
     * @param array $step
     * @return array
     */
    protected function storeAlias( array $step )
    {
        $target = $this->target( $step );
        $language = eZContentLanguage::fetchByLocale( expMigrationStepContentBase::localeOf( isset( $step['language'] ) ? $step['language'] : null ) );
        $full = $this->fullPath( $step, $target );
        $existing = self::elementAt( $full );
        if ( $existing !== null && (string)$existing->attribute( 'action' ) === $target['action'] )
        {
            if ( $step['mode'] === 'create' )
                throw new RuntimeException( "the address $full is there already (mode ensure keeps it)" );
            if ( (int)$existing->attribute( 'lang_mask' ) & (int)$language->attribute( 'id' ) || (int)$existing->attribute( 'lang_mask' ) & 1 )
                return self::result( array( "the address $full leads to " . $target['action'] . ' already' ),
                                     self::aliasValues( $full, $target ) + array( 'removed' => 0, 'created' => false, 'changed' => false ) );
        }
        else if ( $existing !== null )
        {
            throw new RuntimeException( "the address $full leads to " . $existing->attribute( 'action' ) . ', not to ' . $target['action'] );
        }
        $path = trim( eZURLAliasML::cleanURL( (string)$step['path'] ), '/' );
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $result = eZURLAliasML::storePath( $path, $target['action'], $language, $target['link'], !empty( $step['always_available'] ), $target['root'],
                                               true, false, false, !empty( $step['redirect'] ) );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        if ( !isset( $result['status'] ) || $result['status'] !== true )
            throw new RuntimeException( "the address $full was not stored (" . self::statusText( isset( $result['status'] ) ? $result['status'] : null ) . ')' );
        $stored = trim( (string)$result['path'], '/' );
        if ( $target['root'] )
            $stored = trim( substr( $full, 0, strlen( $full ) - strlen( $path ) ), '/' ) . '/' . $stored;
        $stored = trim( $stored, '/' );
        $element = self::elementAt( $stored );
        if ( $element === null || (string)$element->attribute( 'action' ) !== $target['action'] )
            throw new RuntimeException( "the address $stored was stored but does not lead to " . $target['action'] );
        if ( $target['node_id'] )
            eZContentCacheManager::clearContentCacheIfNeeded( (int)eZContentObjectTreeNode::fetch( (int)$target['node_id'] )->attribute( 'contentobject_id' ) );
        return self::result( array( "stored the address $stored -> " . $target['action'] . ' in ' . $language->attribute( 'locale' )
                                    . ( $stored !== $full ? " (asked for $full)" : '' ) ),
                             self::aliasValues( $stored, $target ) + array( 'removed' => 0, 'created' => true, 'changed' => true ) );
    }

    /**
     * @param array $step
     * @return array
     */
    protected function deletePath( array $step )
    {
        $path = trim( eZURLAliasML::cleanURL( (string)$step['path'] ), '/' );
        $element = self::elementAt( $path );
        if ( $element === null )
            return self::missing( $step, "the address $path" );
        $action = (string)$element->attribute( 'action' );
        if ( self::isName( $element ) )
            throw new RuntimeException( "the address $path is made from the name of what it leads to ($action): rename or move that instead" );
        if ( isset( $step['node'] ) && $action !== 'eznode:' . expMigrationIdentifier::resolve( 'node', $step['node'] ) )
            throw new RuntimeException( "the address $path leads to $action, not to the node " . expMigrationStepSchema::show( $step['node'] ) );
        $languages = array();
        if ( isset( $step['language'] ) )
            $languages[] = eZContentLanguage::fetchByLocale( expMigrationStepContentBase::localeOf( $step['language'] ) );
        else
            foreach ( eZContentLanguage::fetchList() as $language )
                if ( (int)$element->attribute( 'lang_mask' ) & (int)$language->attribute( 'id' ) )
                    $languages[] = $language;
        $db = eZDB::instance();
        $db->begin();
        try
        {
            foreach ( $languages as $language )
                eZURLAliasML::removeSingleEntry( (int)$element->attribute( 'parent' ), (string)$element->attribute( 'text_md5' ), $language );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        $left = self::elementAt( $path );
        if ( $left !== null && (string)$left->attribute( 'action' ) === $action && !isset( $step['language'] ) )
            throw new RuntimeException( "the address $path was not removed" );
        $nodeId = eZURLAliasML::nodeIDFromAction( $action );
        self::clearNode( $nodeId );
        return self::result( array( "deleted the address $path -> $action" . ( isset( $step['language'] ) ? ' in ' . $step['language'] : '' ) ),
                             array( 'path' => $path, 'action' => $action, 'node_id' => $nodeId ?: null, 'removed' => 1, 'created' => false, 'changed' => true ) );
    }

    /**
     * Every custom address of a node, or every global one: as the administration's "Remove all".
     *
     * @param array $step
     * @return array
     */
    protected function deleteAll( array $step )
    {
        $nodeId = null;
        if ( isset( $step['node'] ) )
        {
            $nodeId = expMigrationIdentifier::find( 'node', $step['node'] );
            if ( $nodeId === null )
                return self::missing( $step, 'the node ' . expMigrationStepSchema::show( $step['node'] ) );
        }
        $query = function () use ( $nodeId ) {
            $filter = new eZURLAliasQuery();
            if ( $nodeId !== null )
                $filter->actions = array( "eznode:$nodeId" );
            else
                $filter->actionTypesEx = array( 'eznode', 'nop' );
            $filter->type = 'alias';
            $filter->languages = false;
            $filter->offset = 0;
            $filter->limit = false;
            return $filter;
        };
        // each entry once per language, removed by its parent and the md5 of its text (the rows of the query on
        // MongoDB have no text_md5), as the administration's "Remove all" removes them
        $entries = array();
        $addresses = array();
        foreach ( (array)$query()->fetchAll() as $alias )
        {
            $md5 = md5( eZURLAliasML::strtolower( (string)$alias->attribute( 'text' ) ) );
            $key = (int)$alias->attribute( 'parent' ) . '/' . $md5;
            $entries[$key . '/' . (int)$alias->attribute( 'lang_mask' )] = array( (int)$alias->attribute( 'parent' ), $md5, $alias->attribute( 'language_object' ) );
            $addresses[$key] = true;
        }
        $db = eZDB::instance();
        $db->begin();
        try
        {
            foreach ( $entries as $entry )
                eZURLAliasML::removeSingleEntry( $entry[0], $entry[1], $entry[2] );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        $removed = count( $addresses );
        if ( $query()->count() > 0 )
            throw new RuntimeException( 'not every custom address was removed' );
        self::clearNode( $nodeId );
        $label = $nodeId !== null ? "of node $nodeId" : '(the global ones)';
        return self::result( array( $removed ? "deleted $removed custom address(es) $label" : "no custom address $label: nothing to delete" ),
                             array( 'path' => null, 'action' => $nodeId !== null ? "eznode:$nodeId" : null, 'node_id' => $nodeId, 'removed' => $removed,
                                    'created' => false, 'changed' => $removed > 0 ) );
    }

    // ---- wildcards ----------------------------------------------------------------------------------------------

    /**
     * @param array $step
     * @return array
     */
    protected function storeWildcard( array $step )
    {
        $source = self::wildcardText( $step['wildcard'] );
        $destination = self::wildcardText( $step['destination'] );
        $problems = self::wildcardProblems( $source, $destination );
        if ( $problems )
            throw new RuntimeException( implode( '; ', $problems ) );
        $type = !empty( $step['redirect'] ) ? eZURLWildcard::TYPE_FORWARD : eZURLWildcard::TYPE_DIRECT;
        $wildcard = eZURLWildcard::fetchBySourceURL( $source );
        $values = array( 'path' => $source, 'action' => $destination, 'node_id' => null, 'removed' => 0 );
        if ( $wildcard instanceof eZURLWildcard )
        {
            if ( $step['mode'] === 'create' )
                throw new RuntimeException( "the wildcard $source is there already (mode ensure changes it)" );
            if ( (string)$wildcard->attribute( 'destination_url' ) === $destination && (int)$wildcard->attribute( 'type' ) === $type )
                return self::result( array( "the wildcard $source leads to $destination already" ), $values + array( 'created' => false, 'changed' => false ) );
            $wildcard->setAttribute( 'destination_url', $destination );
            $wildcard->setAttribute( 'type', $type );
            $wildcard->store();
            eZURLWildcard::expireCache();
            return self::result( array( "changed the wildcard $source -> $destination" ), $values + array( 'created' => false, 'changed' => true ) );
        }
        $wildcard = new eZURLWildcard( array( 'source_url' => $source, 'destination_url' => $destination, 'type' => $type ) );
        $wildcard->store();
        eZURLWildcard::expireCache();
        if ( !eZURLWildcard::fetchBySourceURL( $source ) instanceof eZURLWildcard )
            throw new RuntimeException( "the wildcard $source was not stored" );
        return self::result( array( "stored the wildcard $source -> $destination" . ( $type === eZURLWildcard::TYPE_FORWARD ? ' (forward)' : ' (direct)' ) ),
                             $values + array( 'created' => true, 'changed' => true ) );
    }

    /**
     * @param array $step
     * @return array
     */
    protected function deleteWildcard( array $step )
    {
        $source = self::wildcardText( $step['wildcard'] );
        $wildcard = eZURLWildcard::fetchBySourceURL( $source );
        if ( !$wildcard instanceof eZURLWildcard )
            return self::missing( $step, "the wildcard $source" );
        $destination = (string)$wildcard->attribute( 'destination_url' );
        // by its key, a simple condition on every engine (removeByIDs() deletes with IN)
        $wildcard->remove();
        eZURLWildcard::expireCache();
        if ( eZURLWildcard::fetchBySourceURL( $source ) instanceof eZURLWildcard )
            throw new RuntimeException( "the wildcard $source was not removed" );
        return self::result( array( "deleted the wildcard $source -> $destination" ),
                             array( 'path' => $source, 'action' => $destination, 'node_id' => null, 'removed' => 1, 'created' => false, 'changed' => true ) );
    }

    // ---- helpers ------------------------------------------------------------------------------------------------

    /**
     * The element an address leads to, or null (nop: placeholders are no address): its whole row (fetchByPath() reads
     * only some of the columns, not is_alias, alias_redirects or text_md5).
     *
     * @param string $path
     * @return eZURLAliasML|null
     */
    protected static function elementAt( $path )
    {
        if ( trim( (string)$path, '/' ) === '' )
            return null;
        foreach ( (array)eZURLAliasML::fetchByPath( (string)$path ) as $element )
        {
            if ( !$element instanceof eZURLAliasML || (string)$element->attribute( 'action' ) === 'nop:' )
                continue;
            $row = eZPersistentObject::fetchObject( eZURLAliasML::definition(), null,
                                                    array( 'parent' => (int)$element->attribute( 'parent' ),
                                                           'text_md5' => md5( eZURLAliasML::strtolower( (string)$element->attribute( 'text' ) ) ) ), true );
            return $row instanceof eZURLAliasML ? $row : $element;
        }
        return null;
    }

    /**
     * Whether an element is the address a node has from its name (not a custom alias). Pure.
     *
     * @param eZURLAliasML $element
     * @return bool
     */
    protected static function isName( $element )
    {
        return strpos( (string)$element->attribute( 'action' ), 'eznode:' ) === 0 && !(int)$element->attribute( 'is_alias' );
    }

    /**
     * The element of a node's name (its own address), or null.
     *
     * @param int $nodeId
     * @return eZURLAliasML|null
     */
    protected static function nameElement( $nodeId )
    {
        $filter = new eZURLAliasQuery();
        $filter->actions = array( 'eznode:' . (int)$nodeId );
        $filter->type = 'name';
        $filter->languages = false;
        $filter->limit = false;
        $list = $filter->fetchAll();
        return $list ? $list[0] : null;
    }

    /**
     * A wildcard pattern or destination as the administration stores it. Pure.
     *
     * @param mixed $text
     * @return string
     */
    protected static function wildcardText( $text )
    {
        return trim( (string)$text, " \t\r\n\0\x0B/" );
    }

    /**
     * @param array $step
     * @param array $target
     * @return array
     */
    protected static function aliasValues( $path, array $target )
    {
        return array( 'path' => $path, 'action' => $target['action'], 'node_id' => $target['node_id'] );
    }

    /**
     * @param int|null $nodeId
     */
    protected static function clearNode( $nodeId )
    {
        $node = $nodeId ? eZContentObjectTreeNode::fetch( (int)$nodeId ) : null;
        if ( $node instanceof eZContentObjectTreeNode )
            eZContentCacheManager::clearContentCacheIfNeeded( (int)$node->attribute( 'contentobject_id' ) );
    }

    /**
     * @param mixed $status
     * @return string
     */
    protected static function statusText( $status )
    {
        $texts = array( eZURLAliasML::LINK_ALREADY_TAKEN => 'the address is taken by another', eZURLAliasML::ACTION_INVALID => 'the destination is not valid',
                        eZURLAliasML::LINK_ID_NOT_FOUND => 'the link was not found', eZURLAliasML::LINK_ID_WRONG_ACTION => 'the link leads elsewhere' );
        return isset( $texts[$status] ) ? $texts[$status] : 'status ' . json_encode( $status );
    }

    /**
     * @param array $step
     * @param string $what
     * @return array
     * @throws RuntimeException unless missing: skip
     */
    protected static function missing( array $step, $what )
    {
        if ( !isset( $step['missing'] ) || $step['missing'] !== 'skip' )
            throw new RuntimeException( "$what is not on this installation (missing: skip lets the step pass)" );
        return self::result( array( "$what is not there: skipped (missing: skip)" ),
                             array( 'path' => null, 'action' => null, 'node_id' => null, 'removed' => 0, 'created' => false, 'changed' => false ) );
    }
}
