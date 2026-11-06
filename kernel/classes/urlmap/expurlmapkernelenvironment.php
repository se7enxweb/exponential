<?php
/**
 * File containing the expUrlMapKernelEnvironment class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The installation as the URL map planner sees it (expUrlMapEnvironment): siteaccesses from site.ini, hosts through
 * the siteaccess matching (eZSiteAccess::matchAddress(), the same probes a request goes through), languages, targets
 * through the URL aliases and modules, and the entries already at an old address (ezurlalias_ml, ezurlwildcard,
 * exp_url_redirect).
 *
 * The rows of a URL alias path are read element by element by their key (parent, text_md5), the same key
 * eZURLAliasML::storePath() writes; aliasPathRows() is what the importer records before and after it writes one.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUrlMapKernelEnvironment implements expUrlMapEnvironment
{
    /** @var array|null */
    protected $siteaccesses = null;

    /** @var array siteaccess => locale */
    protected $languages = array();

    public function siteaccesses()
    {
        if ( $this->siteaccesses === null )
        {
            $ini = eZINI::instance();
            // the siteaccesses of this installation: the available ones, the related ones and the default one
            $list = array( $this->defaultSiteaccess() );
            foreach ( array( 'AvailableSiteAccessList', 'RelatedSiteAccessList' ) as $name )
                if ( $ini->hasVariable( 'SiteAccessSettings', $name ) )
                    $list = array_merge( $list, (array)$ini->variable( 'SiteAccessSettings', $name ) );
            $this->siteaccesses = array_values( array_unique( array_filter( array_map( 'strval', $list ), 'strlen' ) ) );
        }
        return $this->siteaccesses;
    }

    public function defaultSiteaccess()
    {
        $ini = eZINI::instance();
        return $ini->hasVariable( 'SiteSettings', 'DefaultAccess' ) ? (string)$ini->variable( 'SiteSettings', 'DefaultAccess' ) : '';
    }

    public function siteaccessForHost( $host, $path )
    {
        $port = 80;
        $name = (string)$host;
        if ( preg_match( '/^(.*):(\d+)$/', $name, $m ) )
        {
            $name = $m[1];
            $port = (int)$m[2];
        }
        $access = eZSiteAccess::matchAddress( rawurldecode( ltrim( (string)$path, '/' ) ), $name, $port );
        if ( !is_array( $access ) || !empty( $access['unmatched'] ) || !isset( $access['name'] ) )
            return null;
        $segments = array_values( array_filter( explode( '/', trim( (string)$path, '/' ) ), 'strlen' ) );
        foreach ( isset( $access['uri_part'] ) ? (array)$access['uri_part'] : array() as $part )
        {
            if ( $segments && strcasecmp( rawurldecode( $segments[0] ), (string)$part ) === 0 )
                array_shift( $segments );
        }
        return array( 'siteaccess' => (string)$access['name'], 'path' => '/' . implode( '/', $segments ) );
    }

    public function defaultLanguage( $siteaccess )
    {
        if ( !isset( $this->languages[$siteaccess] ) )
        {
            $locale = '';
            if ( $siteaccess !== '' && in_array( $siteaccess, $this->siteaccesses(), true ) )
            {
                $ini = eZSiteAccess::getIni( $siteaccess, 'site.ini' );
                $locale = $ini ? (string)$ini->variable( 'RegionalSettings', 'ContentObjectLocale' ) : '';
            }
            if ( $locale === '' )
            {
                $top = eZContentLanguage::topPriorityLanguage();
                $locale = $top ? (string)$top->attribute( 'locale' ) : (string)eZINI::instance()->variable( 'RegionalSettings', 'ContentObjectLocale' );
            }
            $this->languages[$siteaccess] = $locale;
        }
        return $this->languages[$siteaccess];
    }

    public function languageExists( $locale )
    {
        return eZContentLanguage::fetchByLocale( (string)$locale ) instanceof eZContentLanguage;
    }

    public function resolveTarget( $target, $siteaccess, $language )
    {
        $target = (string)$target;
        if ( preg_match( '#^node:(\d+)$#', $target, $m ) )
            return self::nodeTarget( (int)$m[1] );
        $path = trim( $target, '/' );
        if ( $path === '' )
            return self::nodeTarget( (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' ) );
        $action = eZURLAliasML::urlToAction( $path );
        if ( $action !== false )
        {
            $nodeId = (int)eZURLAliasML::nodeIDFromAction( $action );
            if ( $nodeId )
                return self::nodeTarget( $nodeId );
            return array( 'type' => 'module', 'action' => $action, 'link' => true, 'node_id' => 0 );
        }
        // the path's elements by their keys first (every language), then the kernel's lookup (the languages of the
        // current siteaccess)
        $rows = self::aliasPathRows( $path );
        $last = end( $rows );
        if ( $last && $last['row'] !== null && count( $rows ) === count( self::aliasElements( $path ) ) && (string)$last['row']['action'] !== 'nop:' )
        {
            $action = (string)$last['row']['action'];
            $nodeId = (int)eZURLAliasML::nodeIDFromAction( $action );
            if ( $nodeId )
                return self::nodeTarget( $nodeId );
            return array( 'type' => 'alias', 'action' => $action, 'link' => (int)$last['row']['link'] ?: true, 'node_id' => 0 );
        }
        $elements = eZURLAliasML::fetchByPath( $path );
        foreach ( is_array( $elements ) ? $elements : array() as $element )
        {
            $action = (string)$element->attribute( 'action' );
            if ( $action === '' || $action === 'nop:' )
                continue;
            $nodeId = (int)eZURLAliasML::nodeIDFromAction( $action );
            return array( 'type' => $nodeId ? 'node' : 'alias', 'action' => $action, 'link' => (int)$element->attribute( 'link' ) ?: true,
                          'node_id' => $nodeId );
        }
        return null;
    }

    public function aliasPath( $siteaccess, $path )
    {
        $settings = $this->pathPrefix( (string)$siteaccess );
        return self::prefixedPath( $path, $settings['prefix'], $settings['exclude'] );
    }

    /**
     * A path below a PathPrefix, unless it is the prefix already or its first element is excluded. Pure.
     *
     * @param string $path
     * @param string $prefix
     * @param string[] $exclude
     * @return string
     */
    public static function prefixedPath( $path, $prefix, array $exclude )
    {
        $path = '/' . trim( (string)$path, '/' );
        $prefix = trim( (string)$prefix, '/' );
        if ( $prefix === '' )
            return $path;
        $first = strtolower( (string)explode( '/', ltrim( $path, '/' ) )[0] );
        if ( $first === strtolower( explode( '/', $prefix )[0] ) && stripos( ltrim( $path, '/' ) . '/', $prefix . '/' ) === 0 )
            return $path;
        foreach ( $exclude as $item )
        {
            if ( strtolower( trim( (string)$item, '/' ) ) === $first )
                return $path;
        }
        return '/' . $prefix . ( $path === '/' ? '' : $path );
    }

    /**
     * The visitor's form of a stored URL alias path, the inverse of prefixedPath(): the path without the PathPrefix
     * when it is below it, else null (the prefix itself, a path outside it, and one whose first element after the
     * prefix is one of PathPrefixExclude[], which prefixedPath() would not put below the prefix again). Pure.
     *
     * @param string $path
     * @param string $prefix
     * @param string[] $exclude
     * @return string|null
     */
    public static function unprefixedPath( $path, $prefix, array $exclude )
    {
        $path = '/' . trim( (string)$path, '/' );
        $prefix = trim( (string)$prefix, '/' );
        if ( $prefix === '' || stripos( ltrim( $path, '/' ) . '/', $prefix . '/' ) !== 0 )
            return null;
        $rest = '/' . trim( (string)substr( ltrim( $path, '/' ), strlen( $prefix ) ), '/' );
        if ( $rest === '/' )
            return null;
        $first = strtolower( (string)explode( '/', ltrim( $rest, '/' ) )[0] );
        if ( $first === strtolower( explode( '/', $prefix )[0] ) )
            return null;
        foreach ( $exclude as $item )
        {
            if ( strtolower( trim( (string)$item, '/' ) ) === $first )
                return null;
        }
        return $rest;
    }

    /**
     * The PathPrefix and PathPrefixExclude[] of a siteaccess (site.ini [SiteAccessSettings]).
     *
     * @param string $siteaccess
     * @return array prefix (without slashes, '' for none), exclude
     */
    public function pathPrefix( $siteaccess )
    {
        $prefix = '';
        $exclude = array();
        if ( $siteaccess !== '' && in_array( $siteaccess, $this->siteaccesses(), true ) )
        {
            $ini = eZSiteAccess::getIni( $siteaccess, 'site.ini' );
            if ( $ini && $ini->hasVariable( 'SiteAccessSettings', 'PathPrefix' ) )
                $prefix = trim( (string)$ini->variable( 'SiteAccessSettings', 'PathPrefix' ), '/' );
            if ( $ini && $ini->hasVariable( 'SiteAccessSettings', 'PathPrefixExclude' ) )
                $exclude = (array)$ini->variable( 'SiteAccessSettings', 'PathPrefixExclude' );
        }
        return array( 'prefix' => $prefix, 'exclude' => $exclude );
    }

    public function isSystemPath( $path )
    {
        $path = trim( (string)$path, '/' );
        if ( $path === '' )
            return true;
        $first = explode( '/', $path )[0];
        return (bool)preg_match( '/^[a-zA-Z0-9_]+$/', $first ) && eZModule::exists( $first ) instanceof eZModule;
    }

    public function existingAlias( $path )
    {
        $rows = self::aliasPathRows( $path );
        $last = end( $rows );
        if ( !$last || $last['row'] === null || (string)$last['row']['action'] === 'nop:' )
            return null;
        $row = $last['row'];
        $row['page'] = !(int)$row['is_alias'] && strpos( (string)$row['action'], 'eznode:' ) === 0;
        return $row;
    }

    public function existingWildcard( $source )
    {
        $row = eZURLWildcard::fetchBySourceURL( (string)$source, false );
        if ( !is_array( $row ) )
            return null;
        return array( 'id' => (int)$row['id'], 'source_url' => (string)$row['source_url'], 'destination_url' => (string)$row['destination_url'],
                      'type' => (int)$row['type'] );
    }

    public function existingRedirect( $siteaccess, $path, $query )
    {
        $redirect = expUrlRedirect::fetchBySource( $siteaccess, $path, $query );
        return $redirect ? $redirect->asArray() : null;
    }

    /**
     * The elements of a URL alias path, read by key (parent, text_md5) from the root: for each element its key and
     * row (null where none is there; the elements after a missing one have no parent yet and are left out).
     *
     * @param string $path
     * @param eZDBInterface|null $db
     * @return array[] list of array( parent, text_md5, text, key, row )
     */
    public static function aliasPathRows( $path, $db = null )
    {
        $db = $db ?: eZDB::instance();
        $out = array();
        $parent = 0;
        foreach ( self::aliasElements( $path ) as $text )
        {
            $md5 = md5( eZURLAliasML::strtolower( $text ) );
            $row = self::aliasRow( $parent, $md5, $db );
            $out[] = array( 'parent' => $parent, 'text_md5' => $md5, 'text' => $text, 'key' => $parent . ':' . $md5, 'row' => $row );
            if ( $row === null )
                break;
            $parent = (int)$row['link'];
        }
        return $out;
    }

    /**
     * The elements of a path as eZURLAliasML stores them (no leading or trailing slash). Pure.
     *
     * @param string $path
     * @return string[]
     */
    public static function aliasElements( $path )
    {
        $path = eZURLAliasML::cleanURL( rawurldecode( (string)$path ) );
        return $path === '' ? array() : explode( '/', $path );
    }

    /**
     * The row of ezurlalias_ml with a key, or null.
     *
     * @param int $parent
     * @param string $md5
     * @param eZDBInterface|null $db
     * @return array|null every column, numbers as int
     */
    public static function aliasRow( $parent, $md5, $db = null )
    {
        $db = $db ?: eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT * FROM ezurlalias_ml WHERE parent = ' . (int)$parent . " AND text_md5 = '" . $db->escapeString( (string)$md5 ) . "'" );
        if ( !is_array( $rows ) || !$rows )
            return null;
        $row = array();
        foreach ( array_keys( eZURLAliasML::definition()['fields'] ) as $name )
        {
            $value = isset( $rows[0][$name] ) ? $rows[0][$name] : null;
            $row[$name] = in_array( $name, array( 'id', 'parent', 'lang_mask', 'link', 'is_alias', 'is_original', 'alias_redirects' ), true ) ? (int)$value : (string)$value;
        }
        return $row;
    }

    /**
     * The target of a node, or null when the node does not exist: its action and the element of its name to link to.
     *
     * @param int $nodeId
     * @return array|null
     */
    protected static function nodeTarget( $nodeId )
    {
        if ( $nodeId <= 0 || !eZContentObjectTreeNode::fetch( $nodeId, false, false ) )
            return null;
        $filter = new eZURLAliasQuery();
        $filter->actions = array( 'eznode:' . $nodeId );
        $filter->type = 'name';
        $filter->languages = false;
        $filter->limit = false;
        $list = $filter->fetchAll();
        $link = $list ? (int)$list[0]->attribute( 'id' ) : 0;
        return array( 'type' => 'node', 'action' => 'eznode:' . $nodeId, 'link' => $link ?: true, 'node_id' => $nodeId );
    }
}
