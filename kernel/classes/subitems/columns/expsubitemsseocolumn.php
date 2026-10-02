<?php
/**
 * Subitems list columns for search engines and visitors: the page title in the browser, its
 * length, and the meta title, description and keywords an xrowmetadata attribute holds.
 *
 * Field= picks the column: page_title, title_length, meta_title, meta_description, meta_keywords.
 *
 * page_title is the <title> the public siteaccess's pagelayout prints for the node, worked out
 * from the node and the siteaccess's settings without rendering a page. TitleFormat= in the
 * column block picks the rule of the design that siteaccess uses:
 *
 *   name (the media design, extension/sevenx_themes_media pagelayout/head/title.tpl):
 *        "<page title> - <SiteName>", where <page title> is the meta title of the node's
 *        xrowmetadata attribute when it has one, else the node's name; just "<page title>"
 *        when it equals SiteName.
 *   path (design/standard and ezwebin page_head.tpl):
 *        the names of the node and its ancestors up to the top, the node first, joined by
 *        " / ", then " - <SiteName>".
 *
 * SiteName is [SiteSettings] SiteName of the public siteaccess (SiteAccess= in the block, else
 * [SiteSettings] DefaultAccess). Names are taken in that siteaccess's first SiteLanguageList[]
 * language when the object has it. Cost: none for "name" without metadata; the data map for
 * the meta fields; for "path" the ancestors, fetched once per parent (every row of a page
 * shares them).
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsSEOColumn extends expSubitemsFieldColumn
{
    /** The browser's title of the node's page on the public site. */
    protected function fieldPageTitle( eZContentObjectTreeNode $node )
    {
        return self::pageTitle( $node, (string)$this->setting( 'TitleFormat', 'name' ), (string)$this->setting( 'SiteAccess', '' ) );
    }

    /** Characters in the page title; search engines show about 60. */
    protected function fieldTitleLength( eZContentObjectTreeNode $node )
    {
        $title = $this->fieldPageTitle( $node );
        return $title === null ? null : mb_strlen( $title, 'UTF-8' );
    }

    /** The title set in the node's xrowmetadata attribute, null when none (or the extension is not there). */
    protected function fieldMetaTitle( eZContentObjectTreeNode $node )
    {
        $meta = self::metaData( $node );
        return $meta && isset( $meta->title ) && trim( (string)$meta->title ) !== '' ? (string)$meta->title : null;
    }

    protected function fieldMetaDescription( eZContentObjectTreeNode $node )
    {
        $meta = self::metaData( $node );
        return $meta && isset( $meta->description ) && trim( (string)$meta->description ) !== '' ? (string)$meta->description : null;
    }

    protected function fieldMetaKeywords( eZContentObjectTreeNode $node )
    {
        $meta = self::metaData( $node );
        if ( !$meta || empty( $meta->keywords ) )
            return null;
        $keywords = is_array( $meta->keywords ) ? $meta->keywords : explode( ',', (string)$meta->keywords );
        $keywords = array_values( array_filter( array_map( 'trim', $keywords ), 'strlen' ) );
        return $keywords ? $keywords : null;
    }

    /**
     * The <title> text (unescaped) of $node on the public siteaccess, by rule $format ("name"
     * or "path"); see the class description.
     */
    public static function pageTitle( eZContentObjectTreeNode $node, $format = 'name', $siteAccess = '' )
    {
        $ini = expSubitemsURLColumn::publicSiteIni( $siteAccess );
        // a siteaccess named in the block but not in AvailableSiteAccessList[]: no title to tell
        if ( !$ini && trim( (string)$siteAccess ) !== '' )
            return null;
        $siteName = $ini ? (string)$ini->variable( 'SiteSettings', 'SiteName' ) : (string)eZINI::instance()->variable( 'SiteSettings', 'SiteName' );
        $locale = self::publicLocale( $ini );

        if ( strtolower( trim( $format ) ) === 'path' )
        {
            $names = array( self::nodeName( $node, $locale ) );
            foreach ( array_reverse( self::ancestorNames( $node, $locale ) ) as $name )
                $names[] = $name;
            $names = array_values( array_filter( $names, 'strlen' ) );
            return implode( ' / ', $names ) . ' - ' . $siteName;
        }

        $pageTitle = self::nodeName( $node, $locale );
        $meta = self::metaData( $node );
        if ( $meta && isset( $meta->title ) && (string)$meta->title !== '' )
            $pageTitle = (string)$meta->title;

        if ( $pageTitle === '' )
            return $siteName === '' ? null : $siteName;
        if ( $pageTitle === $siteName )
            return $pageTitle;
        return $pageTitle . ( $siteName !== '' ? ' - ' . $siteName : '' );
    }

    /** The first language of the siteaccess (SiteLanguageList[], else Locale), or null. */
    protected static function publicLocale( $ini )
    {
        if ( !$ini instanceof eZINI )
            return null;
        if ( $ini->hasVariable( 'RegionalSettings', 'SiteLanguageList' ) )
        {
            $list = $ini->variable( 'RegionalSettings', 'SiteLanguageList' );
            if ( is_array( $list ) && isset( $list[0] ) && $list[0] !== '' )
                return (string)$list[0];
        }
        return $ini->hasVariable( 'RegionalSettings', 'ContentObjectLocale' )
            ? (string)$ini->variable( 'RegionalSettings', 'ContentObjectLocale' ) : null;
    }

    /** The node's name in $locale when the object has that translation, else as the admin shows it. */
    protected static function nodeName( eZContentObjectTreeNode $node, $locale )
    {
        $object = $node->attribute( 'object' );
        if ( $locale && $object instanceof eZContentObject )
        {
            $name = $object->name( false, $locale );
            if ( is_string( $name ) && $name !== '' )
                return $name;
        }
        return (string)$node->getName();
    }

    /** The names of the ancestors from the top (node 1 left out) down to the parent, memoised per parent. */
    protected static function ancestorNames( eZContentObjectTreeNode $node, $locale )
    {
        $parentID = (int)$node->attribute( 'parent_node_id' );
        return self::memo( 'ancestornames', $parentID . '/' . $locale, function () use ( $node, $locale )
        {
            $names = array();
            $path = $node->fetchPath();
            foreach ( is_array( $path ) ? $path : array() as $ancestor )
                $names[] = self::nodeName( $ancestor, $locale );
            return $names;
        } );
    }

    /** The node's xrowmetadata content (an xrowMetaData), or null; read from the data map, no other query. */
    protected static function metaData( eZContentObjectTreeNode $node )
    {
        if ( !class_exists( 'xrowMetaData' ) )
            return null;
        foreach ( self::dataMap( $node ) as $attribute )
        {
            if ( $attribute->attribute( 'data_type_string' ) === 'xrowmetadata' && $attribute->hasContent() )
            {
                $content = $attribute->content();
                return is_object( $content ) ? $content : null;
            }
        }
        return null;
    }
}
