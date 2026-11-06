<?php
/**
 * File containing the expUrlMapExporter class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The URL map of an installation as rows (expUrlMapRow), for every writer of expUrlMapFormats:
 *
 *   $rows = ( new expUrlMapExporter() )->export( array( 'kinds' => array( 'alias', 'wildcard', 'redirect' ) ) );
 *   expUrlMapFormats::writeFile( 'url-map.xlsx', $rows );
 *
 * - alias: the global URL aliases of the URL translator (is_alias, an action that is not a node's: module URLs and
 *   other addresses) as redirect or alias rows; with include_node_aliases also the custom aliases of nodes (target
 *   node:<id>). One row per language of the alias (its language column), "always available" ones without one.
 * - wildcard: every URL wildcard (wildcard-forward, wildcard-forward-temporary, wildcard-direct)
 * - redirect: the redirects of exp_url_redirect, with their siteaccess (* for one of every siteaccess), language and
 *   status
 *
 * Filters: kinds (alias, wildcard, redirect; default all), siteaccess and language (for redirects and aliases; the
 * aliases and wildcards themselves have no siteaccess). Import of the export gives the same entries again.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUrlMapExporter
{
    const KINDS = array( 'alias', 'wildcard', 'redirect' );

    /**
     * @param array $options kinds, siteaccess, language, include_node_aliases
     * @return expUrlMapRow[]
     */
    public function export( array $options = array() )
    {
        $options += array( 'kinds' => self::KINDS, 'siteaccess' => '', 'language' => '', 'include_node_aliases' => false );
        $kinds = array_values( array_intersect( self::KINDS, (array)$options['kinds'] ) );
        $rows = array();
        if ( in_array( 'alias', $kinds, true ) )
            $rows = array_merge( $rows, $this->aliases( (string)$options['language'], !empty( $options['include_node_aliases'] ) ) );
        if ( in_array( 'wildcard', $kinds, true ) )
            $rows = array_merge( $rows, $this->wildcards() );
        if ( in_array( 'redirect', $kinds, true ) )
            $rows = array_merge( $rows, $this->redirects( (string)$options['siteaccess'], (string)$options['language'] ) );
        return $rows;
    }

    /**
     * @param string $language
     * @param bool $withNodeAliases
     * @return expUrlMapRow[]
     */
    protected function aliases( $language, $withNodeAliases )
    {
        $db = eZDB::instance();
        $types = $withNodeAliases ? "action_type <> 'nop'" : "action_type NOT IN ( 'nop', 'eznode' )";
        $list = $db->arrayQuery( "SELECT * FROM ezurlalias_ml WHERE is_alias = 1 AND is_original = 1 AND $types ORDER BY id" );
        $rows = array();
        foreach ( is_array( $list ) ? $list : array() as $row )
        {
            $element = new eZURLAliasML( $row );
            $path = '/' . trim( (string)$element->getPath(), '/' );
            $action = (string)$row['action'];
            $nodeId = (int)eZURLAliasML::nodeIDFromAction( $action );
            $target = $nodeId ? 'node:' . $nodeId : '/' . ltrim( (string)eZURLAliasML::actionToUrl( $action ), '/' );
            $kind = (int)$row['alias_redirects'] ? expUrlMapRow::KIND_REDIRECT : expUrlMapRow::KIND_ALIAS;
            $locales = self::locales( (int)$row['lang_mask'] );
            if ( $language !== '' )
                $locales = in_array( $language, $locales, true ) ? array( $language ) : array();
            foreach ( $locales ?: ( $language === '' ? array( '' ) : array() ) as $locale )
                $rows[] = new expUrlMapRow( $path, $target, $kind, '', $locale );
        }
        return $rows;
    }

    /** @return expUrlMapRow[] */
    protected function wildcards()
    {
        $rows = array();
        foreach ( (array)eZURLWildcard::fetchList( false, false, false ) as $row )
        {
            $type = (int)$row['type'];
            $kind = $type === eZURLWildcard::TYPE_DIRECT ? expUrlMapRow::KIND_WILDCARD_DIRECT
                  : ( $type === eZURLWildcard::TYPE_FORWARD_TEMPORARY ? expUrlMapRow::KIND_WILDCARD_FORWARD_TEMPORARY : expUrlMapRow::KIND_WILDCARD_FORWARD );
            $destination = (string)$row['destination_url'];
            if ( !preg_match( '#^[a-z][a-z0-9+.\-]*://#i', $destination ) )
                $destination = '/' . ltrim( $destination, '/' );
            $rows[] = new expUrlMapRow( '/' . ltrim( (string)$row['source_url'], '/' ), $destination, $kind );
        }
        return $rows;
    }

    /**
     * @param string $siteaccess
     * @param string $language
     * @return expUrlMapRow[]
     */
    protected function redirects( $siteaccess, $language )
    {
        $conditions = array();
        if ( $siteaccess !== '' )
            $conditions['siteaccess'] = $siteaccess;
        if ( $language !== '' )
            $conditions['language'] = $language;
        $rows = array();
        foreach ( expUrlRedirect::fetchList( $conditions ) as $redirect )
        {
            $status = (int)$redirect->attribute( 'status' );
            $rows[] = new expUrlMapRow( $redirect->source(), (string)$redirect->attribute( 'target' ), expUrlMapRow::kindForStatus( false, $status ),
                                        ( (string)$redirect->attribute( 'siteaccess' ) !== '' ? (string)$redirect->attribute( 'siteaccess' ) : '*' ), (string)$redirect->attribute( 'language' ), '', 0,
                                        expUrlMapRow::statusColumn( $status ) );
        }
        return $rows;
    }

    /**
     * The locales of a language mask (without the always-available bit).
     *
     * @param int $mask
     * @return string[]
     */
    protected static function locales( $mask )
    {
        $out = array();
        foreach ( eZContentLanguage::fetchList() as $language )
        {
            if ( $mask & (int)$language->attribute( 'id' ) )
                $out[] = (string)$language->attribute( 'locale' );
        }
        return $out;
    }
}
